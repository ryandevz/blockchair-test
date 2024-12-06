<?php
namespace App\Http;

use App\Http\Interfaces\ResponseInterface;

class Response implements ResponseInterface
{
    private int $statusCode;
    private array $headers;
    private string $body;

    public function __construct()
    {
        $this->statusCode = 200;
        $this->headers = [
            'Content-Type' => 'application/json',
            'Access-Control-Allow-Origin' => '*'
        ];
        $this->body = '';
    }

    public function withStatus(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function withBody(mixed $data): self
    {
        $this->body = json_encode($data);
        return $this;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        
        echo $this->body;
        exit;
    }
}