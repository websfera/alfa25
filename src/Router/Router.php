<?php

namespace App\Router;

use App\DI\Container;
use Tracy\Debugger;

class Router
{
    private const BASE_URL = "";
    private const REGEX = '/\{([a-zA-Z0-9_]+)\}/';
    private array $routes = [];

    public function addRoute(string $path, string $controller, string $action): void
    {
        $this->routes[] = [
            'path' => self::BASE_URL . $path,
            'controller' => $controller,
            'action' => $action,
        ];
    }

    public function match(string $uri): array|null
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        //$uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            $pattern = preg_replace(
                self::REGEX,
                '(?P<$1>[^/]+)',
                $route['path'],
            );
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                return [
                    'controller' => $route['controller'],
                    'action' => $route['action'],
                    'params' => $params,
                ];
            }
        }

        return null;
    }

    public function dispatch(string $uri, Container $container): void
    {
        $match = $this->match($uri);

        if ($match === null) {
            http_response_code(404);
            echo "404 Page not found";

            return;
        }

        $controllerClass = $match['controller'];
        $action = $match['action'];
        $params = $match['params'];

        $controller = new $controllerClass($container);
        $controller->$action(...array_values($params));
    }
}
