<?php

class AuthController
{
    private UserRepository $users;

    public function __construct()
    {
        $this->users = new UserRepository();
    }

    public function showRegister(): void
    {
        render('auth/register', [
            'title' => 'Create Account',
        ]);
    }

    public function register(): void
    {
        verify_csrf();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$name || !$email || !$password) {
            flash('error', 'All fields are required.');
            redirect('/register');
        }

        if ($this->users->findByEmail($email)) {
            flash('error', 'Email already exists.');
            redirect('/register');
        }

        $userId = $this->users->create([
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'role' => 'user',
            'balance' => 0,
        ]);

        $_SESSION['user'] = $this->users->findById($userId);
        redirect('/dashboard');
    }

    public function showLogin(): void
    {
        render('auth/login', [
            'title' => 'Login',
        ]);
    }

    public function login(): void
    {
        verify_csrf();
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->users->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            flash('error', 'Invalid credentials.');
            redirect('/login');
        }

        if ((int)$user['active'] !== 1) {
            flash('error', 'Account disabled. Contact support.');
            redirect('/login');
        }

        $_SESSION['user'] = $user;
        redirect('/dashboard');
    }

    public function logout(): void
    {
        verify_csrf();
        session_destroy();
        redirect('/login');
    }
}
