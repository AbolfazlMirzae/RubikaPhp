<?php
namespace RubikaPhp\Models;

require_once "enums.php";
require_once "core.php";

use RubikaPhp\Core\Bot;
use RubikaPhp\Enums\MessageSenderEnum;
use RubikaPhp\Enums\UpdateTypeEnum;
use RubikaPhp\Enums\ButtonTypeEnum;
use RubikaPhp\Enums\ChatKeypadTypeEnum;
use RubikaPhp\Enums\ChatTypeEnum;

class Parameters {
    protected ?string $chatId = null;
    protected ?string $message_id = null;
    protected ?bool $disable_notification = null;
    protected ?string $text = null;
    protected ?string $question = null;
    protected ?array $options = null;
    protected ?string $latitude = null;
    protected ?string $longitude = null;
    protected ?string $first_name = null;
    protected ?string $last_name = null;
    protected ?string $phone_number = null;
    protected ?Keypad $chat_keypad = null;
    protected ?Keypad $inline_keypad = null;
    protected ?string $chat_keypad_type = null;
    protected ?string $from_chat_id = null;
    protected ?string $to_chat_id = null;
    protected ?string $file_path = null;
    protected ?string $file_id = null;

    public function chatId(string $chatId): static {
        $this->chatId = $chatId;
        return $this;
    }
    
    public function messageId(string $messageId): static {
        $this->message_id = $messageId;
        return $this;
    }
    
    public function disableNotification(bool $disable): static {
        $this->disable_notification = $disable;
        return $this;
    }
    
    public function text(string $text): static {
        $this->text = $text;
        return $this;
    }
    
    public function question(string $question): static {
        $this->question = $question;
        return $this;
    }
    
    public function options(array $options): static {
        $this->options = $options;
        return $this;
    }

    public function location($lng, $lat): static {
        $this->latitude = $lat;
        $this->longitude = $lng;
        return $this;
    }

    public function contact(string $first_name, string $phone_number, ?string $last_name = null): static {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->phone_number = $phone_number;
        return $this;
    }

    public function fromChatId(string $fromChatId): static {
        $this->from_chat_id = $fromChatId;
        return $this;
    }
    
    public function toChatId(string $toChatId): static {
        $this->to_chat_id = $toChatId;
        return $this;
    }
    
    public function chatKeypad(Keypad $chatKeypad, ?ChatKeypadTypeEnum $chat_keypad_type = ChatKeypadTypeEnum::NEW): static {
        $this->chat_keypad = $chatKeypad;
        $this->chat_keypad_type = $chat_keypad_type->value;
        return $this;
    }

    public function inlineKeypad(Keypad $inline_keypad): static {
        $this->inline_keypad = $inline_keypad;
        return $this;
    }

    public function filePath(string $file_path): static {
        $this->file_path = $file_path;
        return $this;
    }

    public function fileId(string $file_id): static {
        $this->file_id = $file_id;
        return $this;
    }
    public function resetAll(): static {
        $this->chatId = null;
        $this->message_id = null;
        $this->disable_notification = null;
        $this->text = null;
        $this->question = null;
        $this->options = null;
        $this->latitude = null;
        $this->longitude = null;
        $this->first_name = null;
        $this->last_name = null;
        $this->phone_number = null;
        $this->chat_keypad = null;
        $this->inline_keypad = null;
        $this->chat_keypad_type = null;
        $this->from_chat_id = null;
        $this->to_chat_id = null;
        
        return $this;
    }
}

class File {
    public string $file_id;
    public string $file_name;
    public int $size;

    public function __construct(string $file_id, string $file_name, int $size) {
        $this->file_id = $file_id;
        $this->file_name = $file_name;
        $this->size = $size;
    }
}

class Location {
    public string $longitude;
    public string $latitude;

    public function __construct(string $longitude, string $latitude) {
        $this->longitude = $longitude;
        $this->latitude = $latitude;
    }
}

class Sticker {
    public string $sticker_id;
    public ?File $file;
    public string $emoji_character;

    public function __construct(string $sticker_id, array $file, string $emoji_character) {
        $this->sticker_id = $sticker_id;
        $this->file = new File(...$file);
        $this->emoji_character = $emoji_character;
    }
}

class ContactMessage  {
    public string $phone_number;
    public string $first_name;
    public string $last_name;

    public function __construct(string $phone_number, string $first_name, string $last_name)
    {
        $this->phone_number = $phone_number;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
    }
}
class PollStatus {
    public string $state;
    public int $selection_index;
    public array $percent_vote_options;
    public int $total_vote;
    public bool $show_total_votes;

    public function __construct(string $state, int $selection_index, array $percent_vote_options, int $total_vote, bool $show_total_votes)
    {
        $this->state = $state;
        $this->selection_index = $selection_index;
        $this->percent_vote_options = $percent_vote_options;
        $this->total_vote = $total_vote;
        $this->show_total_votes = $show_total_votes;
    }
}
class Poll {
    public string $question;
    public array $options;
    public PollStatus $poll_status;

    public function __construct(string $question, array $options, array $poll_status)
    {
        $this->question = $question;
        $this->options = $options;
        $this->poll_status = new PollStatus(...$poll_status);
    }
}

class Chat {
    public string $chat_id;
    public string $chat_type;
    public ?string $user_id;
    public ?string $first_name;
    public ?string $last_name;
    public ?string $title;
    public ?string $username;

    public function __construct(
        string $chat_id,
        string $chat_type,
        ?string $user_id = null,
        ?string $first_name = null,
        ?string $last_name = null,
        ?string $title = null,
        ?string $username = null
    ) {
        $this->chat_id = $chat_id;
        $this->chat_type = $chat_type;
        $this->user_id = $user_id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->title = $title;
        $this->username = $username;
    }
}

class BotInfo {
    public string $bot_id;
    public string $bot_title;
    public ?File $avatar;
    public ?string $description;
    public ?string $username;
    public ?string $start_message;
    public ?string $share_url;

    public function __construct(
        string $bot_id,
        string $bot_title,
        ?array $avatar = null,
        ?string $description = null,
        ?string $username = null,
        ?string $start_message = null,
        ?string $share_url = null
    ) {
        $this->bot_id = $bot_id;
        $this->bot_title = $bot_title;
        $this->avatar = $avatar ? new File(...$avatar) : null;
        $this->description = $description;
        $this->username = $username;
        $this->start_message = $start_message;
        $this->share_url = $share_url;
    }
}

class BotCommand {
    public string $command;
    public string $description;

    public function __construct(string $command, string $description) {
        $this->command = $command;
        $this->description = $description;
    }
}

class MessageKeypadUpdate {
    public string $message_id;
    public Keypad $inline_keypad;

    public function __construct(string $message_id, array $inline_keypad) {
        $this->message_id = $message_id;
        $this->inline_keypad = new Keypad(...$inline_keypad);
    }
}

class MessageTextUpdate {
    public string $message_id;
    public string $text;

    public function __construct(string $message_id, string $text) {
        $this->message_id = $message_id;
        $this->text = $text;
    }
}

class LiveLocation {
    public string $start_time;
    public int $live_period;
    public Location $current_location;
    public string $user_id;
    public string $status;
    public string $last_update_time;

    public function __construct(string $start_time, int $live_period, array $current_location, string $user_id, string $status, string $last_update_time)
    {
        $this->start_time = $start_time;
        $this->live_period = $live_period;
        $this->current_location = new Location(...$current_location);
        $this->user_id = $user_id;
        $this->status = $status;
        $this->last_update_time = $last_update_time;
    }
}

class ForwardedFrom {
    public ?string $type_from;
    public ?string $message_id;
    public ?string $from_chat_id;
    public ?string $from_sender_id;

    public function __construct(string $type_from = "", string $message_id = "", string $from_chat_id = "", string $from_sender_id = "") {
        $this->type_from = $type_from;
        $this->message_id = $message_id;
        $this->from_chat_id = $from_chat_id;
        $this->from_sender_id = $from_sender_id;
    }
}

class AuxData {
    public ?string $start_id;
    public ?string $button_id;

    public function __construct(?string $start_id = null, ?string $button_id = null) {
        $this->start_id = $start_id;
        $this->button_id = $button_id;
    }
}

class Button {
    public string $id;
    public ButtonTypeEnum $type;
    public string $button_text;
    public mixed $button_selection;
    public mixed $button_calendar;
    public mixed $button_number_picker;
    public mixed $button_string_picker;
    public mixed $button_location;
    public mixed $button_textbox;

    public function __construct(
        string $id,
        ButtonTypeEnum $type,
        string $button_text,
        mixed $button_selection = null,
        mixed $button_calendar = null,
        mixed $button_number_picker = null,
        mixed $button_string_picker = null,
        mixed $button_location = null,
        mixed $button_textbox = null
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->button_text = $button_text;
        $this->button_selection = $button_selection;
        $this->button_calendar = $button_calendar;
        $this->button_number_picker = $button_number_picker;
        $this->button_string_picker = $button_string_picker;
        $this->button_location = $button_location;
        $this->button_textbox = $button_textbox;
    }
}

class KeypadRow {
    /** @var Button[] */
    public array $buttons;

    public function __construct(array $buttons) {
        $this->buttons = $buttons;
    }
}

class Keypad {
    /** @var KeypadRow[] */
    public array $rows;
    public bool $resize_keyboard;
    public bool $one_time_keyboard;

    public function __construct(array $rows, bool $resize_keyboard = false, bool $one_time_keyboard = false) {
        $this->rows = $rows;
        $this->resize_keyboard = $resize_keyboard;
        $this->one_time_keyboard = $one_time_keyboard;
    }
}

class Message {
    public string $message_id;
    public ?string $text;
    public ?int $time;
    public bool $is_edited;
    public ?MessageSenderEnum $sender_type;
    public ?string $sender_id;
    public ?AuxData $aux_data;
    public ?File $file;
    public ?string $reply_to_message_id;
    public ?Location $location;
    public ?Sticker $sticker;
    public ?ContactMessage $contact_message;
    public ?Poll $poll;
    public ?LiveLocation $live_location;
    public ?string $forwarded_no_link;
    public ?ForwardedFrom $forwarded_from;

    public function __construct(
        string $message_id,
        ?string $text = null,
        ?int $time = null,
        bool $is_edited = false,
        ?MessageSenderEnum $sender_type = null,
        ?string $sender_id = null,
        ?AuxData $aux_data = null,
        ?File $file = null,
        ?string $reply_to_message_id = null,
        ?Location $location = null,
        ?Sticker $sticker = null,
        ?ContactMessage $contact_message = null,
        ?Poll $poll = null,
        ?LiveLocation $live_location = null,
        ?string $forwarded_no_link = null,
        ?ForwardedFrom $forwarded_from = null,
    ) {
        $this->message_id = $message_id;
        $this->text = $text;
        $this->time = $time;
        $this->is_edited = $is_edited;
        $this->sender_type = $sender_type;
        $this->sender_id = $sender_id;
        $this->aux_data = $aux_data;
        $this->file = $file;
        $this->reply_to_message_id = $reply_to_message_id;
        $this->location = $location;
        $this->sticker = $sticker;
        $this->contact_message = $contact_message;
        $this->poll = $poll;
        $this->live_location = $live_location;
        $this->forwarded_no_link = $forwarded_no_link;
        $this->forwarded_from = $forwarded_from;
    }

    public static function fromArray(array $data): Message {
        return new Message(
            strval($data["message_id"] ?? ""),
            $data["text"] ?? null,
            $data["time"] ?? null,
            $data["is_edited"] ?? false,
            isset($data["sender_type"]) ? MessageSenderEnum::from($data["sender_type"]) : null,
            $data["sender_id"] ?? null,
            isset($data["aux_data"]) ? new AuxData(...$data["aux_data"]) : null,
            isset($data["file"]) ? new File(...$data["file"]) : null,
            $data["reply_to_message_id"] ?? null,
            isset($data["location"]) ? new Location(...$data["location"]) : null,
            isset($data["sticker"]) ? new Sticker(...$data["sticker"]) : null,
            isset($data["contact_message"]) ? new ContactMessage(...$data["contact_message"]) : null,
            isset($data["poll"]) ? new Poll(...$data["poll"]) : null,
            isset($data["live_location"]) ? new LiveLocation(...$data["live_location"]) : null,
            $data["forwarded_no_link"] ?? null,
            isset($data["forwarded_from"]) ? new ForwardedFrom(...$data["forwarded_from"]) : null,
        );
    }
}

class Update extends Parameters {
    public UpdateTypeEnum $type;
    public string $chat_id;
    public ?Message $new_message;
    public ?Message $updated_message;
    public ?Message $inline_message;
    public ?string $removed_message_id;
    public mixed $updated_payment;
    public Bot $bot;

    public function __construct(
        UpdateTypeEnum $type,
        string $chat_id,
        ?Message $new_message = null,
        ?Message $updated_message = null,
        ?Message $inline_message = null,
        ?string $removed_message_id = null,
        mixed $updated_payment = null
    ) {
        $this->type = $type;
        $this->chat_id = $chat_id;
        $this->new_message = $new_message;
        $this->updated_message = $updated_message;
        $this->inline_message = $inline_message;
        $this->removed_message_id = $removed_message_id;
        $this->updated_payment = $updated_payment;
    }
    public function setBot(Bot $bot): Update {
        $this->bot = $bot;
        return $this;
    }
    public static function fromArray(array $data): Update {
        $message = null;
        if (isset($data["new_message"])) {
            $message = Message::fromArray($data["new_message"]);
        }
        return new Update(
            UpdateTypeEnum::from($data["type"]),
            $data["chat_id"] ?? "",
            $message,
            isset($data["updated_message"]) ? Message::fromArray($data["updated_message"]) : null,
            isset($data["inline_message"]) ? Message::fromArray($data["inline_message"]) : null,
            $data["removed_message_id"] ?? null,
            $data["updated_payment"] ?? null
        );
    }

    public function reply() {
        $_reply = function (Bot $bot, Update $update) {
            $data = [
                "chat_id" => $update->chat_id,
                "text" => $this->text,
                "reply_to_message_id" => $update->new_message?->message_id ?? $update->updated_message?->message_id
            ];
            if ($this->inline_keypad) {
                $data["inline_keypad"] = (array)$this->inline_keypad;
            }
            if ($this->chat_keypad && $this->chat_keypad_type) {
                $data["chat_keypad"] = (array)$this->chat_keypad;
                $data["chat_keypad_type"] = $this->chat_keypad_type;
            }
            parent::resetAll();
            return $bot->api->request("sendMessage", $data);
        };
        return $_reply($this->bot, $this);
    }
}