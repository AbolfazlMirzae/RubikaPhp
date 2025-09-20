<?php
namespace RubikaPhp\Api;

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class RubikaAPIError extends \InvalidArgumentException {}
class RubikaNetworkError extends \InvalidArgumentException {}

class RubikaAPI {
    private string $token;
    private int $timeout;
    private string $baseUrl = "https://botapi.rubika.ir/v3";
    private Client $client;

    public function __construct(string $token, int $timeout = 60) {
        $this->token = $token;
        $this->timeout = $timeout;

        $this->client = new Client([
            'base_uri' => "{$this->baseUrl}/{$this->token}/",
            'timeout'  => $this->timeout,
        ]);
    }

    private function url(string $method): string {
        return "{$this->baseUrl}/{$this->token}/{$method}";
    }

    public function request(string $method, ?array $data = null): array {
        try {
            $options = [];
            if (!empty($data)) {
                $options['json'] = $data;
            }

            $response = $this->client->post($method, $options);
            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
            $result = json_decode($body, true);

            if ($statusCode !== 200) {
                throw new RubikaAPIError("Bad response: $body", $statusCode);
            }

            if (!isset($result['status']) || $result['status'] !== 'OK') {
                throw new RubikaAPIError("Rubika API Error: " . json_encode($result), $statusCode);
            }

            return $result['data'] ?? [];

        } catch (RequestException $e) {
            throw new RubikaNetworkError("Network error: " . $e->getMessage());
        }
    }

    public function uploadFileToUrl(string $url, string $file_path): string {
        $mime_type = mime_content_type($file_path);
        $filename = basename($file_path);

        try {
            $response = $this->client->post($url, [
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => fopen($file_path, 'r'),
                        'filename' => $filename,
                        'headers'  => ['Content-Type' => $mime_type],
                    ]
                ],
                'timeout' => 240,
            ]);

            $data = json_decode((string)$response->getBody(), true);
            if (!isset($data['data']['file_id'])) {
                throw new \RuntimeException("No file_id returned from upload: " . json_encode($data));
            }

            return $data['data']['file_id'];

        } catch (RequestException $e) {
            $status = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 'N/A';
            throw new \RuntimeException("Upload failed: HTTP $status - " . $e->getMessage());
        }
    }

    public function requestSendFile(string $type): string {
        $validTypes = ['File', 'Image', 'Voice', 'Music', 'Gif', 'Video'];
        if (!in_array($type, $validTypes)) {
            throw new \InvalidArgumentException("Invalid file type: {$type}");
        }

        $response = $this->request('requestSendFile', ['type' => $type]);
        if (empty($response['upload_url'])) {
            throw new \RuntimeException("No upload_url returned: " . json_encode($response));
        }
        return $response['upload_url'];
    }

    public function detectFileType(string $mime_type): string {
        $map = [
            'image/jpeg' => 'Image',
            'image/png' => 'Image',
            'image/gif' => 'Gif',
            'video/mp4' => 'Video',
            'video/quicktime' => 'Video',
            'audio/mpeg' => 'File',
            'audio/wav' => 'File',
            'application/pdf' => 'File',
            'application/msword' => 'File',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'File',
            'application/zip' => 'File',
            'application/x-rar-compressed' => 'File',
        ];

        return $map[strtolower($mime_type)] ?? 'File';
    }
}