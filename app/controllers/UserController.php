<?php

class UserController
{
    private ServiceRepository $services;
    private PurchaseRepository $purchases;
    private TransactionRepository $transactions;
    private UserRepository $users;
    private ApiKeyRepository $apiKeys;

    public function __construct()
    {
        $this->services = new ServiceRepository();
        $this->purchases = new PurchaseRepository();
        $this->transactions = new TransactionRepository();
        $this->users = new UserRepository();
        $this->apiKeys = new ApiKeyRepository();
    }

    public function dashboard(): void
    {
        $user = current_user();
        $purchases = $this->purchases->allByUser($user['id']);
        $transactions = $this->transactions->allByUser($user['id']);
        $apiKey = $this->apiKeys->findByUser($user['id']);

        render('user/dashboard', [
            'title' => 'Dashboard',
            'purchases' => $purchases,
            'transactions' => $transactions,
            'apiKey' => $apiKey,
        ]);
    }

    public function generateApiToken(): void
    {
        verify_csrf();
        $user = current_user();
        $existing = $this->apiKeys->findByUser($user['id']);
        if ($existing) {
            flash('error', 'API token already exists.');
            redirect('/dashboard');
        }

        $token = bin2hex(random_bytes(24));
        $this->apiKeys->create($user['id'], $token);
        flash('success', 'API token generated.');
        redirect('/dashboard');
    }

    public function services(): void
    {
        $services = $this->services->allActive();
        $config = app_config();
        $client = new SmsManClient($config['smsman']);
        $countries = $client->getCountries();
        if (is_array($countries) && isset($countries['data']) && is_array($countries['data'])) {
            $countries = $countries['data'];
        }
        if (!is_array($countries) || isset($countries['success'])) {
            $countries = [];
        }

        $selectedCountryId = (int)($_GET['country_id'] ?? 0);
        $prices = $client->getPrices($selectedCountryId);
        if (is_array($prices) && isset($prices['data']) && is_array($prices['data'])) {
            $prices = $prices['data'];
        }

        $priceRange = null;
        if (is_array($prices) && !isset($prices['success'])) {
            $min = null;
            $max = null;
            if (isset($prices[(string)$selectedCountryId]) && is_array($prices[(string)$selectedCountryId])) {
                foreach ($prices[(string)$selectedCountryId] as $entry) {
                    if (!is_array($entry) || !isset($entry['cost'])) {
                        continue;
                    }
                    $cost = (float)$entry['cost'];
                    $min = $min === null ? $cost : min($min, $cost);
                    $max = $max === null ? $cost : max($max, $cost);
                }
            }
            if ($min !== null && $max !== null) {
                $priceRange = ['min' => $min, 'max' => $max];
            }
        }
        $availability = [];
        if (is_array($prices) && !isset($prices['success'])) {
            if (isset($prices[(string)$selectedCountryId])) {
                foreach ($prices[(string)$selectedCountryId] as $appId => $entry) {
                    $availability[(int)$appId] = (int)($entry['count'] ?? 0);
                }
            } elseif ($selectedCountryId === 0) {
                foreach ($prices as $countryId => $apps) {
                    if (!is_array($apps)) {
                        continue;
                    }
                    foreach ($apps as $appId => $entry) {
                        $availability[(int)$appId] = max($availability[(int)$appId] ?? 0, (int)($entry['count'] ?? 0));
                    }
                }
            }
        }
        render('user/services', [
            'title' => 'Buy Number',
            'services' => $services,
            'countries' => $countries,
            'selectedCountryId' => $selectedCountryId,
            'availability' => $availability,
            'priceRange' => $priceRange,
        ]);
    }

    public function purchase(): void
    {
        verify_csrf();
        $user = current_user();

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
            flash('error', 'Service not found.');
            redirect('/services');
        }

        $cost = (float)$service['price'];
        if ((float)$user['balance'] < $cost) {
            flash('error', 'Insufficient balance. Please refill your wallet.');
            redirect('/wallet');
        }

        $config = app_config();
        $client = new SmsManClient($config['smsman']);
        $isRent = $purchaseType === 'rent';
        if ($isRent && $rentalHours <= 0) {
            flash('error', 'Please select a valid rental duration.');
            redirect('/services');
        }

        $response = $client->getNumber($countryId, (int)$service['smsman_application_id'], null, 'USD', $isRent ? true : null);

        if (!isset($response['request_id'])) {
            flash('error', $response['error_msg'] ?? 'Failed to get number.');
            redirect('/services');
        }

        $rentalEndAt = null;
        if ($isRent) {
            $rentalEndAt = date('Y-m-d H:i:s', time() + ($rentalHours * 3600));
        }

        $this->purchases->create([
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
        $_SESSION['user'] = $this->users->findById($user['id']);

        $this->transactions->create([
            'user_id' => $user['id'],
            'type' => 'purchase',
            'amount' => -$cost,
            'ref' => 'smsman-' . $response['request_id'],
            'provider' => 'smsman',
            'status' => 'success',
            'meta' => json_encode($response),
        ]);

        flash('success', 'Number reserved successfully.');
        redirect('/dashboard');
    }

    public function wallet(): void
    {
        render('user/wallet', [
            'title' => 'Wallet',
        ]);
    }

    public function refill(): void
    {
        verify_csrf();
        $user = current_user();
        $amount = (float)($_POST['amount'] ?? 0);
        $phone = trim($_POST['phone'] ?? '');
        $provider = trim($_POST['provider'] ?? '');

        if ($amount <= 0 || !$phone || !$provider) {
            flash('error', 'Please provide valid refill details.');
            redirect('/wallet');
        }

        $config = app_config();
        $client = new FapshiClient($config['fapshi']);

        $payload = [
            'amount' => $amount,
            'phone' => $phone,
            'provider' => $provider,
            'email' => $user['email'],
            'externalId' => 'wallet-' . time() . '-' . $user['id'],
        ];

        $response = $client->initiatePayment($payload);

        $this->transactions->create([
            'user_id' => $user['id'],
            'type' => 'refill',
            'amount' => $amount,
            'ref' => $payload['externalId'],
            'provider' => 'fapshi',
            'status' => $response['success'] ?? false ? 'pending' : 'failed',
            'meta' => json_encode($response),
        ]);

        if (!($response['success'] ?? false)) {
            flash('error', $response['message'] ?? 'Payment initiation failed.');
            redirect('/wallet');
        }

        flash('success', 'Payment initiated. Complete the payment on your phone.');
        redirect('/wallet');
    }
}
