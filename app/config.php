<?php

// Load .env if present
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ($key !== '' && getenv($key) === false) {
            putenv($key . '=' . $value);
        }
    }
}

return [
    'app' => [
        'name' => 'GetSMS',
        'base_url' => getenv('APP_BASE_URL') ?: 'http://localhost/getsms/public',
        'primary_color' => '#1D4ED8', // royal blue
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'name' => getenv('DB_NAME') ?: 'getsms',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
    'smsman' => [
        'base_url' => 'https://api.sms-man.com/control',
        'token' => getenv('SMSMAN_TOKEN') ?: '',
    ],
    'fapshi' => [
        'base_url' => 'https://api.fapshi.com',
        'api_key' => getenv('FAPSHI_API_KEY') ?: '',
    ],
    'peakerr' => [
        'base_url' => 'https://peakerr.com/api/v2',
        'api_key' => getenv('PEAKERR_API_KEY') ?: '',
    ],
];
