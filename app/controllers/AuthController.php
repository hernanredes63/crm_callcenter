<?php
declare(strict_types=1);

final class AuthController extends Controller
{
    public function redirectHome(): void
    {
        Helpers::redirect(Auth::check() ? '/leads' : '/login');
    }

    public function showLogin(): void
    {
        if (Auth::check()) Helpers::redirect('/leads');
        $this->view('auth/login', ['error' => Helpers::flash('login_error')], 'layout/blank');
    }

    public function login(): void
    {
        Helpers::csrfCheck();
        $email    = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
            Helpers::flash('login_error', 'Email o contraseña inválidos.');
            Helpers::redirect('/login');
        }

        if (!Auth::attempt($email, $password)) {
            Helpers::flash('login_error', 'Credenciales incorrectas o usuario inactivo.');
            Helpers::redirect('/login');
        }

        Helpers::redirect('/leads');
    }

    public function logout(): void
    {
        Auth::logout();
        Helpers::redirect('/login');
    }
}
