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

    public function update(string $id): void
    {
        $this->requireRole('admin', 'supervisor');
        Helpers::csrfCheck();

        $nombre = trim((string)($_POST['nombre'] ?? ''));
        $email  = trim((string)($_POST['email']  ?? ''));
        $pass   = (string)($_POST['password']    ?? '');

        if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Helpers::flash('usr_error', 'El nombre y un email válido son obligatorios.');
            Helpers::redirect('/usuarios');
            return;
        }

        if ($pass !== '' && strlen($pass) < 6) {
            Helpers::flash('usr_error', 'La nueva contraseña debe tener al menos 6 caracteres.');
            Helpers::redirect('/usuarios');
            return;
        }

        $db = Database::pdo();
        
        // 1. Obtenemos el user_id autentico a partir del ID del perfil
        $stmt = $db->prepare('SELECT user_id FROM profiles WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $userId = $stmt->fetchColumn();

        if (!$userId) {
            Helpers::flash('usr_error', 'Usuario no encontrado.');
            Helpers::redirect('/usuarios');
            return;
        }

        // 2. Verificamos que el nuevo email no esté siendo usado por OTRA persona
        $exists = $db->prepare('SELECT 1 FROM users WHERE email = ? AND id != ? LIMIT 1');
        $exists->execute([$email, $userId]);
        if ($exists->fetchColumn()) {
            Helpers::flash('usr_error', 'El email ya está en uso por otro usuario.');
            Helpers::redirect('/usuarios');
            return;
        }

        try {
            $db->beginTransaction();

            // 3. Actualizamos la tabla Profiles (Información visual)
            $db->prepare('UPDATE profiles SET nombre = ?, email = ? WHERE id = ?')
               ->execute([$nombre, $email, $id]);

            // 4. Actualizamos la tabla Users (Credenciales)
            if ($pass !== '') {
                // Si el admin escribió una contraseña nueva, la encriptamos y la guardamos
                $hash = password_hash($pass, PASSWORD_BCRYPT);
                $db->prepare('UPDATE users SET email = ?, password_hash = ? WHERE id = ?')
                   ->execute([$email, $hash, $userId]);
            } else {
                // Si la dejó en blanco, conservamos la contraseña vieja y solo cambiamos el email
                $db->prepare('UPDATE users SET email = ? WHERE id = ?')
                   ->execute([$email, $userId]);
            }

            $db->commit();
            Helpers::flash('usr_ok', 'Usuario actualizado correctamente.');
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            Helpers::flash('usr_error', 'No se pudo actualizar el usuario: ' . $e->getMessage());
        }

        Helpers::redirect('/usuarios');
    }



}
