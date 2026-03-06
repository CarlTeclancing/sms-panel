<?php

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return $needle === '' || strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}

if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool
    {
        return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
    }
}

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}

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

function ensure_public_base_url(string $baseUrl): string
{
    $baseUrl = rtrim($baseUrl, '/');
    if (!str_ends_with($baseUrl, '/public')) {
        $baseUrl .= '/public';
    }
    return $baseUrl;
}

function url(string $path = ''): string
{
    $config = app_config();
    $base = ensure_public_base_url($config['app']['base_url']);
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
    $baseUrl = ensure_public_base_url($config['app']['base_url']);
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

function convert_usd_to_xaf(float $amount): float
{
    $rateRaw = setting('usd_to_xaf_rate', '1');
    $rate = is_numeric($rateRaw) ? (float)$rateRaw : 1.0;
    if ($rate <= 0) {
        $rate = 1.0;
    }
    return $amount * $rate;
}

function format_xaf(float $amount, int $decimals = 2): string
{
    return number_format(convert_usd_to_xaf($amount), $decimals);
}

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9\s-]/', '', $value) ?? '';
    $value = preg_replace('/[\s-]+/', '-', $value) ?? '';
    return trim($value, '-');
}

function send_email(string $to, string $subject, string $message): bool
{
    $config = app_config();
    $from = getenv('APP_EMAIL_FROM') ?: 'no-reply@' . parse_url($config['app']['base_url'], PHP_URL_HOST);
    $headers = "From: {$from}\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    return @mail($to, $subject, $message, $headers);
}

function getFlag(string $code, string $classes = "w-6 h-4"): string
{
    $src = "https://flagcdn.com/" . strtolower($code) . ".svg";
    return "<img src='$src' class='$classes shadow-sm border border-gray-100 rounded-sm' alt='Flag of $code'>";
}
