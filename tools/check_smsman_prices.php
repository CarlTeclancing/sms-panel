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
$url = 'https://api.sms-man.com/control/get-prices?token=' . $token . '&country_id=0';
$response = @file_get_contents($url);

header('Content-Type: text/plain');
if ($response === false) {
    echo "response: false\n";
    exit;
}

echo substr($response, 0, 800) . "\n";
