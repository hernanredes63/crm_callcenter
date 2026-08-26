<?php
declare(strict_types=1);

final class AuthMiddleware
{
    public static function check(): void
    {
        if (!Auth::check()) Helpers::redirect('/login');
    }

    /** Devuelve un callable que exige alguno de los roles. */
    public static function role(string ...$roles): callable
    {
        return function () use ($roles): void {
            self::check();
            if (!Auth::hasRole(...$roles)) {
                http_response_code(403);
                exit('No tienes permisos para acceder a este recurso.');
            }
        };
    }
}
