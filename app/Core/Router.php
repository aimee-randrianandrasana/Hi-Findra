<?php

declare(strict_types=1);

namespace App\Core;

// Routeur : associe methode HTTP + URI a un controleur avec parametres dynamiques et middlewares
final class Router
{
    // @var array<string, array<int, array{pattern: string, handler: array, middlewares: array}>>
    private array $routes = [
        'GET'    => [],
        'POST'   => [],
        'PUT'    => [],
        'DELETE' => [],
    ];

    public function get(string $uri, array $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $uri, $handler, $middlewares);
    }

    public function post(string $uri, array $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $uri, $handler, $middlewares);
    }

    public function put(string $uri, array $handler, array $middlewares = []): void
    {
        $this->addRoute('PUT', $uri, $handler, $middlewares);
    }

    public function delete(string $uri, array $handler, array $middlewares = []): void
    {
        $this->addRoute('DELETE', $uri, $handler, $middlewares);
    }

    private function addRoute(string $method, string $uri, array $handler, array $middlewares): void
    {
        $pattern = $this->convertToRegex($uri);

        $this->routes[$method][] = [
            'pattern'     => $pattern,
            'handler'     => $handler,
            'middlewares' => $middlewares,
        ];
    }

    private function convertToRegex(string $uri): string
    {
        $uri = trim($uri, '/');
        $pattern = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $uri);

        return '#^' . $pattern . '$#';
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $uri = trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter(
                    $matches,
                    static fn ($key) => is_string($key),
                    ARRAY_FILTER_USE_KEY
                );

                foreach ($route['middlewares'] as $middleware) {
                    (new $middleware())->handle();
                }

                [$controllerClass, $action] = $route['handler'];
                $controller = new $controllerClass();
                $controller->$action(...array_values($params));

                return;
            }
        }

        $this->abort404();
    }

    private function abort404(): void
    {
        http_response_code(404);
        require dirname(__DIR__) . '/Views/errors/404.php';
    }
}
