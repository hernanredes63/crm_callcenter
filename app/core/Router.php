<?php
declare(strict_types=1);

/**
 * Router mínimo con soporte para parámetros dinámicos {id}.
 *
 *   $router->get('/leads/{id}', [LeadsController::class, 'show']);
 *
 * Middleware simple: el 4º argumento acepta callables que se invocan antes.
 *   $router->get('/x', $handler, 'GET', [fn() => Auth::check() || Helpers::redirect('/login')]);
 */
final class Router
{
    /** @var array<int, array{method:string, pattern:string, handler:mixed, middleware:array}> */
    private array $routes = [];

    public function get(string $path, $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function any(string $path, $handler, array $middleware = []): void
    {
        $this->add('GET',  $path, $handler, $middleware);
        $this->add('POST', $path, $handler, $middleware);
    }

    private function add(string $method, string $path, $handler, array $mw): void
    {
        $pattern = preg_replace('#\{([a-z_][a-z0-9_]*)\}#i', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . rtrim($pattern, '/') . '/?$#i';
        $this->routes[] = compact('method', 'pattern', 'handler') + ['middleware' => $mw];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        // Quitar el base path si el proyecto está bajo un subdirectorio
        $basePath = rtrim(parse_url(BASE_URL, PHP_URL_PATH) ?? '', '/');
        if ($basePath !== '' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        // Quitar /index.php si se incluye explícitamente en la URL
        if (strpos($uri, '/index.php') === 0) {
            $uri = substr($uri, 10);
        }

        $uri = '/' . ltrim($uri, '/');

        foreach ($this->routes as $r) {
            if ($r['method'] !== $method) continue;
            if (!preg_match($r['pattern'], $uri, $m)) continue;

            foreach ($r['middleware'] as $mw) { $mw(); }

            $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);

            [$class, $action] = is_array($r['handler']) ? $r['handler'] : [null, $r['handler']];
            if ($class) {
                $obj = new $class();
                echo $obj->$action(...array_values($params));
            } else {
                echo call_user_func_array($r['handler'], array_values($params));
            }
            return;
        }

        http_response_code(404);
        require ROOT_PATH . '/app/views/errors/404.php';
    }
}
