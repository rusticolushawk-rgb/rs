<?php
namespace Core\Routing;

class Router
{
    private array $routes = [];
    private array $middleware = [];

    public function get(string $path, string $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, string $handler): self
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, string $handler): self
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, string $handler): self
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'pattern' => $this->buildPattern($path),
        ];
        return $this;
    }

    private function buildPattern(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '(?:\?.*)?$#';
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->callHandler($route['handler'], $params);
                return;
            }
        }

        // 404
        http_response_code(404);
        if (str_starts_with($uri, '/api/')) {
            echo json_encode(['error' => 'Not Found', 'code' => 404]);
        } else {
            include BASE_PATH . '/views/errors/404.php';
        }
    }

    private function callHandler(string $handler, array $params): void
    {
        [$controllerClass, $method] = explode('@', $handler);

        if (!class_exists($controllerClass)) {
            throw new \RuntimeException("Controller {$controllerClass} not found");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            throw new \RuntimeException("Method {$method} not found in {$controllerClass}");
        }

        $controller->$method(...array_values($params));
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
