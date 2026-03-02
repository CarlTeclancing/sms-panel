<?php
// Swychr payment integration without Composer or external libraries

// Load .env variables manually
env_load(__DIR__ . '/../../.env');

function env_load($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }
        $_ENV[$name] = $value;
        putenv("$name=$value");
    }
}

function uuidv4() {
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // version 4
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // variant
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function getToken($email, $password) {
    $endpoint = ($_ENV['SWYCHR_BASE_URL'] ?? getenv('SWYCHR_BASE_URL')) . "/admin/auth";
    $data = ["email" => $email, "password" => $password];
    $response = http_post_json($endpoint, $data);
    if (!$response || empty($response['token'])) return false;
    return $response['token'];
}

function createLink($data, $token) {
    $endpoint = ($_ENV['SWYCHR_BASE_URL'] ?? getenv('SWYCHR_BASE_URL')) . '/create_payment_links';
    $headers = [
        'Authorization: Bearer ' . $token,
        'Accept: application/json',
        'Content-Type: application/json'
    ];
    return http_post_json($endpoint, $data, $headers);
}

function http_post_json($url, $data, $headers = ['Content-Type: application/json']) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $result = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($status !== 200) return false;
    return json_decode($result, true);
}

function run() {
    $baseUrl = $_ENV['SWYCHR_BASE_URL'] ?? getenv('SWYCHR_BASE_URL');
    $email = $_ENV['SWYCHR_EMAIL'] ?? getenv('SWYCHR_EMAIL');
    $password = $_ENV['SWYCHR_PASSWORD'] ?? getenv('SWYCHR_PASSWORD');
    if (!$baseUrl || !$email || !$password) {
        echo "Missing SWYCHR_BASE_URL, SWYCHR_EMAIL, or SWYCHR_PASSWORD in .env\n";
        return;
    }
    $token = getToken($email, $password);
    if (!$token) {
        echo "Failed to get token\n";
        return;
    }
    $transactionId = 'txn_' . uuidv4();
    $countryCode = $_ENV['SWYCHR_COUNTRY_CODE'] ?? getenv('SWYCHR_COUNTRY_CODE') ?? 'CM';
    $currency = $_ENV['SWYCHR_CURRENCY'] ?? getenv('SWYCHR_CURRENCY') ?? 'XAF';
    $passDigital = ($_ENV['SWYCHR_PASS_DIGITAL_CHARGE'] ?? getenv('SWYCHR_PASS_DIGITAL_CHARGE') ?? '1') === '1';
    $callbackUrl = $_ENV['APP_BASE_URL'] . '/webhooks/swychr';

    // Accept user/deposit data from POST or GET
    $name = $_POST['name'] ?? $_GET['name'] ?? 'Customer';
    $email = $_POST['email'] ?? $_GET['email'] ?? '';
    $mobile = $_POST['mobile'] ?? $_GET['mobile'] ?? '';
    $amount = floatval($_POST['amount'] ?? $_GET['amount'] ?? 0);
    $description = $_POST['description'] ?? $_GET['description'] ?? 'Wallet deposit';

    if (!$email || !$mobile || !$amount) {
        echo "Missing required user/payment data (email, mobile, amount)\n";
        return;
    }

    $data = [
        "country_code" => $countryCode,
        "name" => $name,
        "email" => $email,
        "mobile" => $mobile,
        "amount" => $amount,
        "currency" => $currency,
        "transaction_id" => $transactionId,
        "description" => $description,
        "pass_digital_charge" => $passDigital,
        "callback_url" => $callbackUrl
    ];
    $response = createLink($data, $token);
    if (!empty($response['data']['payment_link'])) {
        header("Location: " . $response['data']['payment_link']);
        exit;
    }
    print_r($response);
}

run();
