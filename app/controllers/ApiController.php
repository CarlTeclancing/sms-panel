<?php

class ApiController
{
    private ServiceRepository $services;
    private PurchaseRepository $purchases;
    private UserRepository $users;
    private TransactionRepository $transactions;

    public function __construct()
    {
        $this->services = new ServiceRepository();
        $this->purchases = new PurchaseRepository();
        $this->users = new UserRepository();
        $this->transactions = new TransactionRepository();
    }

    public function docs(): void
    {
        render('api/docs', [
            'title' => 'API Documentation',
        ]);
    }

    private function requireApiAuth(): array
    {
        $token = $_GET['token'] ?? $_POST['token'] ?? '';
        if (!$token) {
            $this->json(['success' => false, 'message' => 'Missing token'], 401);
        }

        $stmt = db()->prepare('SELECT * FROM api_keys WHERE token = ? LIMIT 1');
        $stmt->execute([$token]);
        $key = $stmt->fetch();

        if (!$key) {
            $this->json(['success' => false, 'message' => 'Invalid token'], 401);
        }

        $user = $this->users->findById((int)$key['user_id']);
        if (!$user) {
            $this->json(['success' => false, 'message' => 'User not found'], 404);
        }

        if ((int)$user['active'] !== 1) {
            $this->json(['success' => false, 'message' => 'Account disabled'], 403);
        }

        return $user;
    }

    public function balance(): void
    {
        $user = $this->requireApiAuth();
        $this->json(['balance' => (float)$user['balance']]);
    }

    public function services(): void
    {
        $this->requireApiAuth();
        $services = $this->services->allActive();
        $smsMarkup = setting('sms_markup_percent', '0');
        foreach ($services as &$service) {
            $service['price'] = price_with_markup((float)$service['price'], $smsMarkup);
        }
        unset($service);
        $this->json(['services' => $services]);
    }

    public function purchase(): void
    {
        $user = $this->requireApiAuth();
        $serviceId = (int)($_POST['service_id'] ?? 0);
        $countryId = (int)($_POST['country_id'] ?? 0);
        $purchaseType = $_POST['purchase_type'] ?? 'buy';
        $rentalHours = (int)($_POST['rental_hours'] ?? 0);

        $services = $this->services->allActive();
        $service = null;
        foreach ($services as $item) {
            if ((int)$item['id'] === $serviceId) {
                $service = $item;
                break;
            }
        }

        if (!$service) {
            $this->json(['success' => false, 'message' => 'Service not found'], 404);
        }

        $smsMarkup = setting('sms_markup_percent', '0');
        $cost = price_with_markup((float)$service['price'], $smsMarkup);
        if ((float)$user['balance'] < $cost) {
            $this->json(['success' => false, 'message' => 'Insufficient balance'], 402);
        }

        $config = app_config();
        $client = new SmsManClient($config['smsman']);
        $isRent = $purchaseType === 'rent';
        if ($isRent && $rentalHours <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid rental duration'], 422);
        }

        $response = $client->getNumber($countryId, (int)$service['smsman_application_id'], null, 'USD', $isRent ? true : null);

        if (!isset($response['request_id'])) {
            $this->json(['success' => false, 'message' => $response['error_msg'] ?? 'Failed'], 500);
        }

        $rentalEndAt = null;
        if ($isRent) {
            $rentalEndAt = date('Y-m-d H:i:s', time() + ($rentalHours * 3600));
        }

        $purchaseId = $this->purchases->create([
            'user_id' => $user['id'],
            'request_id' => $response['request_id'],
            'country_id' => $response['country_id'],
            'application_id' => $response['application_id'],
            'number' => $response['number'],
            'status' => 'ready',
            'purchase_type' => $isRent ? 'rent' : 'buy',
            'rental_hours' => $isRent ? $rentalHours : null,
            'rental_end_at' => $rentalEndAt,
            'cost' => $cost,
        ]);

        $newBalance = (float)$user['balance'] - $cost;
        $this->users->updateBalance($user['id'], $newBalance);

        $this->transactions->create([
            'user_id' => $user['id'],
            'type' => 'purchase',
            'amount' => -$cost,
            'ref' => 'smsman-' . $response['request_id'],
            'provider' => 'smsman',
            'status' => 'success',
            'meta' => json_encode($response),
        ]);

        $this->json([
            'success' => true,
            'purchase_id' => $purchaseId,
            'request_id' => $response['request_id'],
            'number' => $response['number'],
            'purchase_type' => $isRent ? 'rent' : 'buy',
            'rental_end_at' => $rentalEndAt,
        ]);
    }

    public function smsStatus(): void
    {
        $this->requireApiAuth();
        $purchaseId = (int)($_GET['purchase_id'] ?? 0);
        $purchase = $this->purchases->findById($purchaseId);

        if (!$purchase) {
            $this->json(['success' => false, 'message' => 'Purchase not found'], 404);
        }

        $config = app_config();
        $client = new SmsManClient($config['smsman']);
        $response = $client->getSms((int)$purchase['request_id']);
        $this->json($response);
    }

    private function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
