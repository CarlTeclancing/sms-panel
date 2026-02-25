<?php
session_start();

require __DIR__ . '/../app/db.php';
require __DIR__ . '/../app/helpers.php';
require __DIR__ . '/../app/services/SmsManClient.php';
require __DIR__ . '/../app/services/FapshiClient.php';
require __DIR__ . '/../app/repositories/UserRepository.php';
require __DIR__ . '/../app/repositories/ServiceRepository.php';
require __DIR__ . '/../app/repositories/PurchaseRepository.php';
require __DIR__ . '/../app/repositories/TransactionRepository.php';
require __DIR__ . '/../app/repositories/ApiKeyRepository.php';
require __DIR__ . '/../app/repositories/SettingsRepository.php';
require __DIR__ . '/../app/controllers/AuthController.php';
require __DIR__ . '/../app/controllers/UserController.php';
require __DIR__ . '/../app/controllers/AdminController.php';
require __DIR__ . '/../app/controllers/ApiController.php';
require __DIR__ . '/../app/controllers/WebhookController.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($basePath && str_starts_with($path, $basePath)) {
    $path = substr($path, strlen($basePath));
}
if ($path === '') {
    $path = '/';
}
$method = $_SERVER['REQUEST_METHOD'];

$auth = new AuthController();
$userController = new UserController();
$adminController = new AdminController();
$apiController = new ApiController();
$webhookController = new WebhookController();

if ($path === '/' || $path === '/index.php') {
    $serviceRepo = new ServiceRepository();
    $services = $serviceRepo->allActive();
    render('home', ['title' => 'GetSMS', 'services' => $services]);
    exit;
}

if ($path === '/register' && $method === 'GET') {
    $auth->showRegister();
    exit;
}
if ($path === '/register' && $method === 'POST') {
    $auth->register();
    exit;
}
if ($path === '/login' && $method === 'GET') {
    $auth->showLogin();
    exit;
}
if ($path === '/login' && $method === 'POST') {
    $auth->login();
    exit;
}
if ($path === '/logout' && $method === 'POST') {
    $auth->logout();
    exit;
}

if ($path === '/dashboard') {
    require_auth();
    $userController->dashboard();
    exit;
}

if ($path === '/services') {
    require_auth();
    $userController->services();
    exit;
}

if ($path === '/purchase' && $method === 'POST') {
    require_auth();
    $userController->purchase();
    exit;
}

if ($path === '/wallet' && $method === 'GET') {
    require_auth();
    $userController->wallet();
    exit;
}

if ($path === '/wallet/refill' && $method === 'POST') {
    require_auth();
    $userController->refill();
    exit;
}

if ($path === '/api-token' && $method === 'POST') {
    require_auth();
    $userController->generateApiToken();
    exit;
}

if ($path === '/admin') {
    require_auth();
    require_admin();
    $adminController->dashboard();
    exit;
}

if ($path === '/admin/services') {
    require_auth();
    require_admin();
    $adminController->services();
    exit;
}

if ($path === '/admin/services/sync' && $method === 'POST') {
    require_auth();
    require_admin();
    $adminController->syncServices();
    exit;
}

if ($path === '/admin/users') {
    require_auth();
    require_admin();
    $adminController->users();
    exit;
}

if ($path === '/admin/transactions') {
    require_auth();
    require_admin();
    $adminController->transactions();
    exit;
}

if ($path === '/admin/notifications') {
    require_auth();
    require_admin();
    $adminController->notifications();
    exit;
}

if ($path === '/admin/api-keys') {
    require_auth();
    require_admin();
    $adminController->apiKeys();
    exit;
}

if ($path === '/admin/settings') {
    require_auth();
    require_admin();
    if ($method === 'POST') {
        $adminController->settings();
    } else {
        $adminController->settingsPage();
    }
    exit;
}

if ($path === '/admin/price' && $method === 'POST') {
    require_auth();
    require_admin();
    $adminController->updatePrice();
    exit;
}

if ($path === '/admin/user/update' && $method === 'POST') {
    require_auth();
    require_admin();
    $adminController->updateUser();
    exit;
}

if ($path === '/admin/user/toggle' && $method === 'POST') {
    require_auth();
    require_admin();
    $adminController->toggleUser();
    exit;
}

if ($path === '/admin/balance/adjust' && $method === 'POST') {
    require_auth();
    require_admin();
    $adminController->adjustBalance();
    exit;
}

if ($path === '/admin/api/rotate' && $method === 'POST') {
    require_auth();
    require_admin();
    $adminController->rotateApiKey();
    exit;
}


if ($path === '/api/docs') {
    require_auth();
    $apiController->docs();
    exit;
}

if ($path === '/api/v1/balance') {
    $apiController->balance();
    exit;
}

if ($path === '/api/v1/services') {
    $apiController->services();
    exit;
}

if ($path === '/api/v1/purchase' && $method === 'POST') {
    $apiController->purchase();
    exit;
}

if ($path === '/api/v1/sms-status') {
    $apiController->smsStatus();
    exit;
}

if ($path === '/webhooks/fapshi' && $method === 'POST') {
    $webhookController->fapshi();
    exit;
}

http_response_code(404);
echo 'Not Found';
