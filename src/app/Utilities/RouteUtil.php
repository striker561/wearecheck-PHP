<?php

namespace JSONAPI\Utilities;

use JSONAPI\App;

class RouteUtil
{
    private $routes = [];

    public function __construct(
        private App $app
    ) {}

    public function addRoute(
        string $method,
        string $path,
        string $controller,
        string $action
    ): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function handleRequest(): void
    {

        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url(url: $_SERVER['REQUEST_URI'], component: PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($method !== $route['method']) {
                continue;
            }
            $pattern = '#^' . $route['path'] . '$#';
            if (preg_match(pattern: $pattern, subject: $uri, matches: $matches)) {
                array_shift(array: $matches);
                $controller = new $route['controller']();
                call_user_func_array([$controller, $route['action']], $matches);
                return;
            }
        }

        $this->app->sendResponse(
            statusCode: 404,
            data: ['error' => 'Resource Not Found']
        );
    }
}
