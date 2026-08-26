<?php
declare(strict_types=1);

/**
 * Módulo Usuarios — solo admin/supervisor.
 * Lista profiles + rol; crea nuevo usuario (users + profiles + user_roles).
 */
final class UsuariosController extends Controller
{
    public function index(): void
    {
        $this->requireRole('admin', 'supervisor');

        $sql = "SELECT p.id, p.user_id, p.nombre, p.email, p.activo, p.created_at,
                       (SELECT role FROM user_roles ur WHERE ur.user_id = p.user_id LIMIT 1) AS role
                FROM profiles p
                ORDER BY p.nombre ASC";
        $users = Database::pdo()->query($sql)->fetchAll();

        $this->view('usuarios/index', [
            'users' => $users,
            'error' => Helpers::flash('usr_error'),
            'ok'    => Helpers::flash('usr_ok'),
        ]);
    }

    public function store(): void
    {
        $this->requireRole('admin', 'supervisor');
        Helpers::csrfCheck();

        $nombre = trim((string)($_POST['nombre'] ?? ''));
        $email  = trim((string)($_POST['email']  ?? ''));
        $pass   = (string)($_POST['password']    ?? '');
        $role   = (string)($_POST['role']        ?? 'operador');

        $validRoles = ['admin','supervisor','operador','vendedor','marketing'];
        if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || strlen($pass) < 6 || !in_array($role, $validRoles, true)) {
            Helpers::flash('usr_error', 'Datos inválidos. Verifica nombre, email, contraseña (mín. 6) y rol.');
            Helpers::redirect('/usuarios');
        }

        $db = Database::pdo();
        $exists = $db->prepare('SELECT 1 FROM users WHERE email = ? LIMIT 1');
        $exists->execute([$email]);
        if ($exists->fetchColumn()) {
            Helpers::flash('usr_error', 'Este email ya está registrado.');
            Helpers::redirect('/usuarios');
        }

        try {
            $db->beginTransaction();

            $userId    = Helpers::uuid();
            $profileId = Helpers::uuid();
            $roleId    = Helpers::uuid();
            $hash      = password_hash($pass, PASSWORD_BCRYPT);

            $db->prepare('INSERT INTO users (id, email, password_hash) VALUES (?,?,?)')
               ->execute([$userId, $email, $hash]);

            $db->prepare('INSERT INTO profiles (id, user_id, nombre, email, activo) VALUES (?,?,?,?,1)')
               ->execute([$profileId, $userId, $nombre, $email]);

            $db->prepare('INSERT INTO user_roles (id, user_id, role) VALUES (?,?,?)')
               ->execute([$roleId, $userId, $role]);

            $db->commit();
            Helpers::flash('usr_ok', 'Usuario creado correctamente.');
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            Helpers::flash('usr_error', 'No se pudo crear el usuario: '.$e->getMessage());
        }
        Helpers::redirect('/usuarios');
    }

    public function toggleActive(string $id): void
    {
        $this->requireRole('admin', 'supervisor');
        Helpers::csrfCheck();
        Database::pdo()
            ->prepare('UPDATE profiles SET activo = 1 - activo WHERE id = ?')
            ->execute([$id]);
        Helpers::flash('usr_ok', 'Estado actualizado.');
        Helpers::redirect('/usuarios');
    }

    public function updateRole(string $id): void
    {
        $this->requireRole('admin', 'supervisor');
        Helpers::csrfCheck();

        $role = (string)($_POST['role'] ?? '');
        $valid = ['admin','supervisor','operador','vendedor','marketing'];
        if (!in_array($role, $valid, true)) {
            Helpers::flash('usr_error', 'Rol inválido.');
            Helpers::redirect('/usuarios');
        }

        $db = Database::pdo();
        $stmt = $db->prepare('SELECT user_id FROM profiles WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $userId = $stmt->fetchColumn();
        if (!$userId) { Helpers::flash('usr_error','Usuario no encontrado.'); Helpers::redirect('/usuarios'); }

        $db->prepare('DELETE FROM user_roles WHERE user_id = ?')->execute([$userId]);
        $db->prepare('INSERT INTO user_roles (id, user_id, role) VALUES (?,?,?)')
           ->execute([Helpers::uuid(), $userId, $role]);

        Helpers::flash('usr_ok', 'Rol actualizado.');
        Helpers::redirect('/usuarios');
    }
}
