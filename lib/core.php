<?php

namespace RubikaPhp\Core;

require_once "api.php";
require_once "models.php";
require_once "filters.php";

use RubikaPhp\Filters\Filters;
use RubikaPhp\Models\Chat;
use RubikaPhp\Models\Parameters;
use RubikaPhp\Models\BotInfo;
use RubikaPhp\Models\Update;
use RubikaPhp\Api\RubikaAPI;
use RubikaPhp\Enums\UpdateEndpointTypeEnum;

class Bot extends Parameters
{
    public RubikaAPI $api;
    private array $handlers = [];
    private string $token;
    private string $tokenHash;
    private bool $stopped = false;

    public function __construct(string $token)
    {
        $this->api = new RubikaAPI($token);
        $this->token = $token;
        $this->tokenHash = hash('sha256', $token.'RubikaPhp');
    }

    public function onUpdate(Filters $filter, callable $func)
    {
        $this->handlers[] = [
            "func" => $func,
            "filter" => $filter
        ];
    }

    private function handleUpdate(array $upd): void
    {
        foreach ($this->handlers as $handler) {
            if ($this->stopped) break;

            $update = Update::fromArray($upd);
            $update->setBot($this);
            $filter = $handler["filter"];
            if ($filter === null || $filter->check($update)) {
                try {
                    $handler["func"]($this, $update);
                } catch (\Exception $e) {
                    error_log("Handler error: " . $e->getMessage());
                }
            }
        }
    }

    private function runOn(): void
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->stopped = false;
            $data = json_decode(file_get_contents('php://input'), true);
            
            $this->handleUpdate(
                is_array($data) && isset($data['update'])
                    ? $data['update']
                    : $data
            );
            return;
        }

        if (php_sapi_name() !== 'cli') return;

        $offset = null;
        if (file_exists($this->tokenHash.'.txt')) $offset = file_get_contents($this->tokenHash.'.txt');
        while (true) {
            try {
                $this->stopped = false;
                $response = $this->getUpdates(100, $offset);
                $updates = $response["updates"] ?? [];

                if (!empty($response["next_offset_id"])) {
                    $offset = $response["next_offset_id"];
                    file_put_contents($this->tokenHash.'.txt', $offset);
                }

                foreach ($updates as $upd) {
                    $this->handleUpdate($upd);
                }

                sleep(2);
            } catch (\Exception $e) {
                error_log("Polling error: " . $e->getMessage());
                sleep(2);
            }
        }
    }

    public function run(): void
    {
        $fin = new \Fiber(fn() => $this->runOn());
        $fin->start();
    }

    public function stopPropagation() {
        $this->stopped = true;
    }

    public function getMe(): BotInfo
    {
        $data = $this->api->request("getMe");
        $info = new BotInfo(...$data['bot']);
        return $info;
    }

    public function sendMessage(): array
    {
        $data = [
            "chat_id" => $this->chatId,
            "text" => $this->text
        ];
        
        if ($this->inline_keypad) $data["inline_keypad"] = $this->inline_keypad;
        $data["chat_keypad"] = $this->chat_keypad;
        if ($this->chat_keypad_type) $data["chat_keypad_type"] = $this->chat_keypad_type;
        if ($this->message_id) $data["reply_to_message_id"] = $this->message_id;
        if ($this->disable_notification) $data["disable_notification"] = $this->disable_notification;

        return $this->api->request("sendMessage", $data);
    }

    public function sendFile(): array
    {
        if (!$this->chatId) {
            throw new \InvalidArgumentException("chat_id is required");
        }

        if (!$this->file_path && !$this->file_id) {
            throw new \InvalidArgumentException("file path or file_id is required");
        }

        if (!isset($this->file_id)) {
            $mime_type = mime_content_type($this->file_path);
            $file_type = $this->api->detectFileType($mime_type);
            $upload_url = $this->api->requestSendFile($file_type);
            $file_id = $this->api->uploadFileToUrl($upload_url, $this->file_path);
        } else {
            $file_type = $this->file_type ?? 'Image';
            $file_id = $this->file_id ?? null;
        }

        $params = [
            'chat_id' => $this->chatId,
            'file_id' => $file_id,
            'type' => $file_type,
        ];

        if ($this->message_id) {
            $params['reply_to_message_id'] = $this->message_id;
        }

        if ($this->text) {
            $params['text'] = $this->text;
        }

        $params["chat_keypad"] = $this->chat_keypad;
        if ($this->chat_keypad_type) $params["chat_keypad_type"] = $this->chat_keypad_type;

        if ($this->inline_keypad) {
            $params['inline_keypad'] = $this->inline_keypad;
        }

        if ($this->disable_notification) {
            $params['disable_notification'] = $this->disable_notification;
        }

        $res = $this->api->request('sendFile', $params);
        $this->resetAll();

        return ['api' => $res, 'file_id' => $file_id, 'type' => $file_type];
    }

    public function sendPoll(): array
    {
        $data = [
            "chat_id" => $this->chatId,
            "question" => $this->question,
            "options" => $this->options
        ];

        if ($this->message_id) $data["reply_to_message_id"] = $this->message_id;
        if ($this->disable_notification) $data["disable_notification"] = $this->disable_notification;

        return $this->api->request("sendPoll", $data);
    }

    public function sendLocation(): array
    {
        $data = [
            "chat_id" => $this->chatId,
            "latitude" => $this->latitude,
            "longitude" => $this->longitude
        ];

        if ($this->chat_keypad) $data["chat_keypad"] = $this->chat_keypad;
        if ($this->chat_keypad_type) $data["chat_keypad_type"] = $this->chat_keypad_type;
        if ($this->inline_keypad) $data["inline_keypad"] = $this->inline_keypad;
        if ($this->message_id) $data["reply_to_message_id"] = $this->message_id;
        if ($this->disable_notification) $data["disable_notification"] = $this->disable_notification;

        return $this->api->request("sendLocation", $data);
    }

    public function sendContact(): array
    {
        $data = [
            "chat_id" => $this->chatId,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "phone_number" => $this->phone_number
        ];

        if ($this->chat_keypad) $data["chat_keypad"] = $this->chat_keypad;
        if ($this->chat_keypad_type) $data["chat_keypad_type"] = $this->chat_keypad_type;
        if ($this->inline_keypad) $data["inline_keypad"] = $this->inline_keypad;
        if ($this->message_id) $data["reply_to_message_id"] = $this->message_id;
        if ($this->disable_notification) $data["disable_notification"] = $this->disable_notification;

        return $this->api->request("sendContact", $data);
    }

    public function getChat(): Chat
    {
        $data = $this->api->request("getChat", ["chat_id" => $this->chatId]);
        $info = new Chat(...$data["chat"]);
        return $info;
    }

    public function getUpdates(int $limit = 100, ?string $offset = null): array
    {
        $data = ["limit" => $limit];
        if ($offset) $data["offset_id"] = $offset;
        return $this->api->request("getUpdates", $data);
    }

    public function forwardMessage(): array
    {
        $data = [
            "from_chat_id" => $this->from_chat_id,
            "message_id" => $this->message_id,
            "to_chat_id" => $this->to_chat_id
        ];

        if ($this->disable_notification) $data["disable_notification"] = $this->disable_notification;

        return $this->api->request("forwardMessage", $data);
    }

    public function editMessageText(): array
    {
        return $this->api->request("editMessageText", [
            "chat_id" => $this->chatId,
            "message_id" => $this->message_id,
            "text" => $this->text
        ]);
    }

    public function editMessageKeypad(): array
    {
        return $this->api->request("editMessageKeypad", [
            "chat_id" => $this->chatId,
            "message_id" => $this->message_id,
            "inline_keypad" => $this->inline_keypad
        ]);
    }

    public function deleteMessage(): array
    {
        return $this->api->request("deleteMessage", [
            "chat_id" => $this->chatId,
            "message_id" => $this->message_id
        ]);
    }

    public function setCommands(array $commands): array
    {
        return $this->api->request("setCommands", [
            "bot_commands" => $commands
        ]);
    }

    public function updateBotEndpoints(string $url, ?string $endpointType = "ReceiveUpdate"): array
    {
        return $this->api->request("updateBotEndpoints", [
            "url" => $url,
            "type" => $endpointType
        ]);
    }

    public function editChatKeypad(): array
    {
        $data = [
            "chat_id" => $this->chatId,
            "chat_keypad_type" => $this->chat_keypad_type
        ];

        if ($this->chat_keypad) $data["chat_keypad"] = $this->chat_keypad;

        return $this->api->request("editChatKeypad", $data);
    }

    public function setEndpoint(string $url, ?string $token = null)
    {
        if (!$token) $token = $this->token;
        $data = [];

        foreach (UpdateEndpointTypeEnum::cases() as $case) {
            $dat = $this->updateBotEndpoints($url, $case->value);
            $data[] = [
                'type' => $case->value,
                'status' => $dat['status']
            ];
        }

        return $data;
    }
}