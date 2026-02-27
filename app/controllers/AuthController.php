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
            'referralCode' => trim($_GET['ref'] ?? ''),
        ]);
    }

    public function register(): void
    {
        verify_csrf();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $referralCode = trim($_POST['referral_code'] ?? '');

        if (!$name || !$email || !$password) {
            flash('error', 'All fields are required.');
            redirect('/register');
        }

        if ($this->users->findByEmail($email)) {
            flash('error', 'Email already exists.');
            redirect('/register');
        }

        $referrer = null;
        if ($referralCode) {
            $referrer = $this->users->findByReferralCode($referralCode);
        }

        $refCode = null;
        for ($i = 0; $i < 5; $i++) {
            $candidate = strtoupper(bin2hex(random_bytes(4)));
            if (!$this->users->findByReferralCode($candidate)) {
                $refCode = $candidate;
                break;
            }
        }

        $userId = $this->users->create([
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'role' => 'user',
            'balance' => 0,
            'referral_code' => $refCode,
            'referred_by' => $referrer['id'] ?? null,
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
