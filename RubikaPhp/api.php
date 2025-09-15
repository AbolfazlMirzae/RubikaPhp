<?php
namespace RubikaPhp\Api;
class RubikaAPIError extends \InvalidArgumentException {}
class RubikaNetworkError extends \InvalidArgumentException {}

class RubikaAPI {
    private string $token;
    private int $timeout;
    private string $baseUrl = "https://botapi.rubika.ir/v3";

    public function __construct(string $token, int $timeout = 60) {
        $this->token = $token;
        $this->timeout = $timeout;
    }

    private function url(string $method): string {
        return "{$this->baseUrl}/{$this->token}/{$method}";
    }

    public function request(string $method, ?array $data = null): array {
        $url = $this->url($method);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json"
        ]);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RubikaNetworkError("Network error: $error");
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new RubikaAPIError("Bad response: $response", $httpCode);
        }

        $result = json_decode($response, true);

        if (!isset($result["status"]) || $result["status"] !== "OK") {
            throw new RubikaAPIError("Rubika API Error: " . json_encode($result), $httpCode);
        }

        return $result["data"] ?? [];
    }
    public function uploadFileToUrl(string $url, string $file_path): string
    {
        $mime_type = mime_content_type($file_path);
        $filename = basename($file_path);
        $curl_file = new \CURLFile($file_path, $mime_type, $filename);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => ['file' => $curl_file],
            CURLOPT_HTTPHEADER => ['Content-Type: multipart/form-data'],
            CURLOPT_TIMEOUT => 240,
        ]);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);
        if ($http_code !== 200 || !is_array($data)) {
            throw new \RuntimeException("Upload failed: HTTP $http_code - " . ($response ?: 'No response'));
        }
        if (!isset($data['data']['file_id'])) {
            throw new \RuntimeException("No file_id returned from upload: " . json_encode($data));
        }
        return $data['data']['file_id'];
    }
    public function requestSendFile(string $type): string
    {
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
    public function detectFileType(string $mime_type): string
    {
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