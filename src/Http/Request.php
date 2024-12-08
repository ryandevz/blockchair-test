<?php
namespace App\Http;

use App\Http\Interfaces\RequestInterface;

class Request implements RequestInterface
{
    private array $headers;
    private string $method;
    private array $params;
    private array $routeParams = [];
    private string $uri;

    public function __construct()
    {
        $this->headers = $this->getRequestHeaders();
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->params = $this->getRequestParams();
        $this->uri = $_SERVER['REQUEST_URI'] ?? '/';
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setRouteParams(array $params): void 
    {
        $this->routeParams = $params;
    }

    public function getRouteParam(string $name): ?string 
    {
        return $this->routeParams[$name] ?? null;
    }

    private function getRequestHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))))] = $value;
            }
        }
        return $headers;
    }

    private function getRequestParams(): array
    {
        $params = [];
        if ($this->method === 'GET') {
            parse_str($_SERVER['QUERY_STRING'] ?? '', $params);
        } else {
            $input = file_get_contents('php://input');
            if (!empty($input)) {
                $params = json_decode($input, true) ?? [];
            }
        }
        return $params;
    }
}