<?php
namespace App\Router;

use App\Http\Request;
use App\Http\Response;

class Router
{
    private array $routes = [];

    public function addRoute(string $method, string $path, callable $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(Request $request, Response $response): void
    {
        $method = $request->getMethod();
        $uri = parse_url($request->getUri(), PHP_URL_PATH);

        if (isset($this->routes[$method][$uri])) {
            call_user_func($this->routes[$method][$uri], $request, $response);
        } else {
            $response->withStatus(404)
                    ->withBody(['status' => 'error', 'message' => 'Route not found'])
                    ->send();
        }
    }
}