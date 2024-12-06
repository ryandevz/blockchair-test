<?php
namespace App\Blockchain;

class RPC {
    private string $url;
    private string $username;
    private string $password;
    private int $timeout;
    private array $headers;

    public function __construct(
        string $url,
        string $username = '',
        string $password = '',
        int $timeout = 30
    ) {
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
        $this->timeout = $timeout;
        $this->headers = [
            'Content-Type: application/json',
        ];
    }
    
    public function call(string $method, array $params = []): array
    {
        $payload = json_encode([
            'jsonrpc' => '2.0',
            'id' => time(),
            'method' => $method,
            'params' => $params
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $this->headers,
            CURLOPT_TIMEOUT => $this->timeout
        ]);

        if ($this->username && $this->password) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            throw new \RuntimeException("CURL Error: $error");
        }

        if ($httpCode !== 200) {
            throw new \RuntimeException("HTTP Error: $httpCode");
        }

        $decoded = json_decode($response, true);
        if (isset($decoded['error']) && $decoded['error']) {
            throw new \RuntimeException(
                "RPC Error: " . $decoded['error']['message'] ?? 'Unknown error'
            );
        }

        return $decoded['result'] ?? [];
    }
}