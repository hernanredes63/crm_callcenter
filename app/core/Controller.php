<?php
declare(strict_types=1);

/**
 * Controlador base. Provee render de vistas y respuestas JSON.
 */
abstract class Controller
{
    /** Renderiza una vista dentro del layout principal. */
    protected function view(string $view, array $data = [], string $layout = 'layout/main'): void
    {
        extract($data, EXTR_SKIP);
        $__view = ROOT_PATH . '/app/views/' . $view . '.php';
        $__layout = ROOT_PATH . '/app/views/' . $layout . '.php';

        if (!is_file($__view)) throw new RuntimeException("Vista no encontrada: $view");

        ob_start();
        require $__view;
        $content = ob_get_clean();

        require $__layout;
    }

    /** Renderiza una vista parcial sin layout (para HTML embebido / includes). */
    protected function partial(string $view, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require ROOT_PATH . '/app/views/' . $view . '.php';
        return ob_get_clean();
    }

    /** Respuesta JSON. */
    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /** Requiere autenticación. Si no, redirige a /login. */
    protected function requireAuth(): void
    {
        if (!Auth::check()) Helpers::redirect('/login');
    }

    /** Requiere que el usuario tenga alguno de los roles indicados. */
    protected function requireRole(string ...$roles): void
    {
        $this->requireAuth();
        if (!Auth::hasRole(...$roles)) {
            #http_response_code(403);
            #exit('No tienes permisos para acceder a este recurso.');
            //guardamos un mesaje visula bonito (flash)
            Helpers::flash('error', 'No tienes persmisos para acceder a este modulo.');
            //lo pateamos de nuevo a la pantalla inicial del leads
            Helpers::redirect('/leads');
            exit;
        }
    }
}
