<?php
/**
 * Front controller único.
 * Todas las requests entran acá y son despachadas al Router.
 */
declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/app/config/config.php';
require ROOT_PATH . '/app/core/Helpers.php';
require ROOT_PATH . '/app/core/Database.php';
require ROOT_PATH . '/app/core/Auth.php';
require ROOT_PATH . '/app/core/Model.php';
require ROOT_PATH . '/app/core/Controller.php';
require ROOT_PATH . '/app/core/Router.php';

// Autoload simple (PSR-0 flat) para controllers y models
spl_autoload_register(function (string $class): void {
    foreach (['controllers', 'models', 'middleware'] as $dir) {
        $file = ROOT_PATH . "/app/{$dir}/{$class}.php";
        if (is_file($file)) { require $file; return; }
    }
});

session_start();

$router = new Router();
require ROOT_PATH . '/routes/web.php';

try {
    $router->dispatch();
} catch (Throwable $e) {
    http_response_code(500);
    if (DEBUG) {
        echo '<pre style="padding:20px;font-family:monospace">'
           . htmlspecialchars($e->getMessage() . "\n\n" . $e->getTraceAsString()) . '</pre>';
    } else {
        echo '<h1>Error interno del servidor</h1>';
    }
}
