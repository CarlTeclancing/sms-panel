<?php

function app_config(): array
{
    static $config = null;
    if (!$config) {
        $config = require __DIR__ . '/config.php';
    }
    return $config;
}

function render(string $view, array $data = []): void
{
    extract($data);
    $config = app_config();
    $baseUrl = $config['app']['base_url'];
    require __DIR__ . '/views/layout.php';
}

function url(string $path = ''): string
{
    $config = app_config();
    $base = rtrim($config['app']['base_url'], '/');
    $path = ltrim($path, '/');
    return $base . '/' . $path;
}

function current_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
    $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    if ($basePath && str_starts_with($path, $basePath)) {
        $path = substr($path, strlen($basePath));
    }
    return $path === '' ? '/' : $path;
}

function redirect(string $path): void
{
    $config = app_config();
    $baseUrl = rtrim($config['app']['base_url'], '/');
    header('Location: ' . $baseUrl . $path);
    exit;
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_auth(): void
{
    $user = current_user();
    if (!$user) {
        redirect('/login');
    }

    $stmt = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$user['id']]);
    $fresh = $stmt->fetch();
    if (!$fresh || (int)$fresh['active'] !== 1) {
        session_destroy();
        redirect('/login');
    }
    $_SESSION['user'] = $fresh;
}

function require_admin(): void
{
    $user = current_user();
    if (!$user || $user['role'] !== 'admin') {
        redirect('/dashboard');
    }
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
        return null;
    }

    $msg = $_SESSION['flash'][$key] ?? null;
    if ($msg !== null) {
        unset($_SESSION['flash'][$key]);
    }
    return $msg;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(419);
        exit('Invalid CSRF token');
    }
}

function setting(string $name, ?string $default = null): ?string
{
    $stmt = db()->prepare('SELECT value FROM settings WHERE name = ? LIMIT 1');
    $stmt->execute([$name]);
    $row = $stmt->fetch();
    if (!$row) {
        return $default;
    }
    return $row['value'] ?? $default;
}

function price_with_markup(float $amount, ?string $percent = null): float
{
    $percentValue = is_numeric($percent) ? (float)$percent : 0.0;
    if ($percentValue <= 0) {
        return $amount;
    }
    return $amount * (1 + ($percentValue / 100));
}
