<?php
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

$token = getenv('SMSMAN_TOKEN');
$mask = $token ? substr($token, 0, 4) . str_repeat('*', max(0, strlen($token) - 8)) . substr($token, -4) : 'not set';

$url = 'https://api.sms-man.com/control/applications?token=' . $token;
$response = @file_get_contents($url);

header('Content-Type: text/plain');
echo "token: {$mask}\n";
if ($response === false) {
    echo "response: false\n";
    exit;
}

echo "response (first 500 chars):\n";
echo substr($response, 0, 500) . "\n";
