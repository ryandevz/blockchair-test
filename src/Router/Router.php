<?php
namespace App\Router;

use App\Http\Request;
use App\Http\Response;

class Router
{
    private array $routes = [];
    private array $params = [];

    public function addRoute(string $method, string $path, callable $handler): void
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = "#^" . $pattern . "$#";
        
        $this->routes[$method][$pattern] = [
            'handler' => $handler,
            'originalPath' => $path
        ];
    }

    public function dispatch(Request $request, Response $response): void
    {
        $method = $request->getMethod();
        $uri = parse_url($request->getUri(), PHP_URL_PATH);
        $routeFound = false;

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $pattern => $route) {
                if (preg_match($pattern, $uri, $matches)) {
                    $params = array_filter($matches, function($key) {
                        return !is_numeric($key);
                    }, ARRAY_FILTER_USE_KEY);

                    $request->setRouteParams($params);
                    
                    call_user_func($route['handler'], $request, $response);
                    $routeFound = true;
                    break;
                }
            }
        }

        if (!$routeFound) {
            $response->withStatus(404)
                    ->withBody(['status' => 'error', 'message' => 'Route not found'])
                    ->send();
        }
    }
}