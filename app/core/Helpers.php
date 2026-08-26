<?php
declare(strict_types=1);

final class Helpers
{
    /** UUID v4 aleatorio (compatible con CHAR(36)). */
    public static function uuid(): string
    {
        $d = random_bytes(16);
        $d[6] = chr((ord($d[6]) & 0x0f) | 0x40);
        $d[8] = chr((ord($d[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4));
    }

    /** Genera URL absoluta a partir de la BASE_URL. */
    public static function url(string $path = ''): string
    {
        return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }

    /** Redirige y termina el script. */
    public static function redirect(string $path): void
    {
        header('Location: ' . self::url($path));
        exit;
    }

    /** Escape HTML seguro. */
    public static function e($v): string
    {
        return htmlspecialchars((string)($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** Devuelve/crea el token CSRF de la sesión. */
    public static function csrf(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(24));
        }
        return $_SESSION['_csrf'];
    }

    /** Valida el token CSRF del POST. Si falla, aborta con 419. */
    public static function csrfCheck(): void
    {
        $sent = $_POST['_csrf'] ?? '';
        if (!hash_equals($_SESSION['_csrf'] ?? '', (string)$sent)) {
            http_response_code(419);
            exit('CSRF token inválido');
        }
    }

    /** Flash messages: setter/getter one-shot. */
    public static function flash(string $key, ?string $value = null): ?string
    {
        if ($value !== null) { $_SESSION['_flash'][$key] = $value; return null; }
        $v = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $v;
    }

    public static function old(string $key, $default = ''): string
    {
        return self::e($_POST[$key] ?? $default);
    }
}
