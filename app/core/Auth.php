<?php
declare(strict_types=1);

/**
 * Autenticación por sesión + RBAC.
 * Reemplazo directo del contexto AuthContext.tsx original.
 */
final class Auth
{
    /** Intenta autenticar. Devuelve true en éxito. */
    public static function attempt(string $email, string $password): bool
    {
        $db = Database::pdo();
        $stmt = $db->prepare('SELECT id, password_hash FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $u = $stmt->fetch();
        if (!$u || !password_verify($password, $u['password_hash'])) return false;

        // Perfil + roles
        $stmt = $db->prepare('SELECT id, nombre, email, activo FROM profiles WHERE user_id = ? LIMIT 1');
        $stmt->execute([$u['id']]);
        $profile = $stmt->fetch();
        if (!$profile || (int)$profile['activo'] !== 1) return false;

        $stmt = $db->prepare('SELECT role FROM user_roles WHERE user_id = ?');
        $stmt->execute([$u['id']]);
        $roles = array_column($stmt->fetchAll(), 'role');

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'         => $u['id'],
            'profile_id' => $profile['id'],
            'nombre'     => $profile['nombre'],
            'email'      => $profile['email'],
            'roles'      => $roles,
            'role'       => self::topRole($roles),
        ];
        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool          { return isset($_SESSION['user']); }
    public static function user(): ?array         { return $_SESSION['user'] ?? null; }
    public static function userId(): ?string      { return $_SESSION['user']['id'] ?? null; }
    public static function profileId(): ?string   { return $_SESSION['user']['profile_id'] ?? null; }
    public static function role(): ?string        { return $_SESSION['user']['role'] ?? null; }
    public static function roles(): array         { return $_SESSION['user']['roles'] ?? []; }

    public static function hasRole(string ...$roles): bool
    {
        return (bool)array_intersect($roles, self::roles());
    }

    public static function isAdmin(): bool        { return self::hasRole('admin'); }
    public static function isSupervisor(): bool   { return self::hasRole('admin', 'supervisor'); }
    public static function isOperador(): bool     { return self::hasRole('operador'); }
    public static function isVendedor(): bool     { return self::hasRole('vendedor'); }
    public static function isMarketing(): bool    { return self::hasRole('marketing'); }

    /** Devuelve el rol de mayor jerarquía del usuario. */
    private static function topRole(array $roles): ?string
    {
        foreach (['admin','supervisor','operador','vendedor','marketing'] as $r) {
            if (in_array($r, $roles, true)) return $r;
        }
        return null;
    }
}
