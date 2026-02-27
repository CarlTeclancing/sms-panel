<?php

class UserController
{
    private ServiceRepository $services;
    private SocialServiceRepository $socialServices;
    private PurchaseRepository $purchases;
    private SocialOrderRepository $socialOrders;
    private TransactionRepository $transactions;
    private UserRepository $users;
    private ApiKeyRepository $apiKeys;
    private SettingsRepository $settings;
    private TicketRepository $tickets;

    public function __construct()
    {
        $this->services = new ServiceRepository();
        $this->socialServices = new SocialServiceRepository();
        $this->purchases = new PurchaseRepository();
        $this->socialOrders = new SocialOrderRepository();
        $this->transactions = new TransactionRepository();
        $this->users = new UserRepository();
        $this->apiKeys = new ApiKeyRepository();
        $this->settings = new SettingsRepository();
        $this->tickets = new TicketRepository();
    }

    public function dashboard(): void
    {
        $user = current_user();
        $purchases = $this->purchases->allByUser($user['id']);
        $socialOrders = $this->socialOrders->allByUser($user['id']);
        $transactions = $this->transactions->allByUser($user['id']);
        $apiKey = $this->apiKeys->findByUser($user['id']);

        render('user/dashboard', [
            'title' => 'Dashboard',
            'purchases' => $purchases,
            'socialOrders' => $socialOrders,
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
        $this->autoSyncBoostingServices();
        $services = $this->services->allActive();
        $boostingServices = $this->socialServices->allActive();
        $smsMarkup = setting('sms_markup_percent', '0');
        $boostMarkup = setting('boost_markup_percent', '0');

        foreach ($services as &$service) {
            $service['display_price'] = price_with_markup((float)$service['price'], $smsMarkup);
        }
        unset($service);

        foreach ($boostingServices as &$boostService) {
            $boostService['display_rate'] = price_with_markup((float)$boostService['rate'], $boostMarkup);
        }
        unset($boostService);
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
            'boostingServices' => $boostingServices,
            'smsMarkup' => $smsMarkup,
            'boostMarkup' => $boostMarkup,
            'countries' => $countries,
            'selectedCountryId' => $selectedCountryId,
            'availability' => $availability,
            'priceRange' => $priceRange,
        ]);
    }

    public function boosting(): void
    {
        $this->autoSyncBoostingServices();
        $boostingServices = $this->socialServices->allActive();
        $boostMarkup = setting('boost_markup_percent', '0');

        foreach ($boostingServices as &$boostService) {
            $boostService['display_rate'] = price_with_markup((float)$boostService['rate'], $boostMarkup);
        }
        unset($boostService);

        render('user/boosting', [
            'title' => 'Boosting Services',
            'boostingServices' => $boostingServices,
            'boostMarkup' => $boostMarkup,
        ]);
    }

    public function boostOrder(): void
    {
        verify_csrf();
        $user = current_user();

        $serviceId = (int)($_POST['boost_service_id'] ?? 0);
        $link = trim($_POST['link'] ?? '');
        $quantity = (int)($_POST['quantity'] ?? 0);
        $runs = (int)($_POST['runs'] ?? 0);
        $interval = (int)($_POST['interval'] ?? 0);

        if ($serviceId <= 0 || !$link || $quantity <= 0) {
            flash('error', 'Please provide valid order details.');
            redirect('/boosting');
        }

        $service = $this->socialServices->findById($serviceId);
        if (!$service || (int)$service['active'] !== 1) {
            flash('error', 'Boosting service not found.');
            redirect('/boosting');
        }

        $min = (int)$service['min_qty'];
        $max = (int)$service['max_qty'];
        if ($quantity < $min || $quantity > $max) {
            flash('error', "Quantity must be between {$min} and {$max}.");
            redirect('/boosting');
        }

        $boostMarkup = setting('boost_markup_percent', '0');
        $rate = price_with_markup((float)$service['rate'], $boostMarkup);
        $cost = ($rate * $quantity) / 1000;
        if ((float)$user['balance'] < $cost) {
            flash('error', 'Insufficient balance. Please refill your wallet.');
            redirect('/wallet');
        }

        $config = app_config();
        $client = new PeakerrClient($config['peakerr']);
        $response = $client->addOrder((int)$service['peakerr_service_id'], $link, $quantity, $runs > 0 ? $runs : null, $interval > 0 ? $interval : null);

        if (!isset($response['order'])) {
            $error = $response['error'] ?? 'Failed to place boosting order.';
            flash('error', $error);
            redirect('/boosting');
        }

        $orderId = (int)$response['order'];

        $this->socialOrders->create([
            'user_id' => $user['id'],
            'service_id' => $serviceId,
            'peakerr_order_id' => $orderId,
            'link' => $link,
            'quantity' => $quantity,
            'runs' => $runs > 0 ? $runs : null,
            'interval_minutes' => $interval > 0 ? $interval : null,
            'status' => 'pending',
            'charge' => $cost,
            'currency' => 'USD',
        ]);

        $newBalance = (float)$user['balance'] - $cost;
        $this->users->updateBalance($user['id'], $newBalance);
        $_SESSION['user'] = $this->users->findById($user['id']);

        $this->transactions->create([
            'user_id' => $user['id'],
            'type' => 'purchase',
            'amount' => -$cost,
            'ref' => 'peakerr-' . $orderId,
            'provider' => 'peakerr',
            'status' => 'success',
            'meta' => json_encode(['order' => $orderId, 'service' => $service['name']]),
        ]);

        flash('success', 'Boosting order placed successfully.');
        redirect('/dashboard');
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

        $smsMarkup = setting('sms_markup_percent', '0');
        $cost = price_with_markup((float)$service['price'], $smsMarkup);
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
        $user = current_user();
        $transactions = $this->transactions->allByUser($user['id']);
        $purchases = $this->purchases->allByUser($user['id']);
        $socialOrders = $this->socialOrders->allByUser($user['id']);

        $activity = [];
        foreach ($purchases as $purchase) {
            $activity[] = [
                'label' => 'SMS Purchase',
                'detail' => $purchase['number'] ?? '',
                'amount' => -(float)($purchase['cost'] ?? 0),
                'status' => $purchase['status'] ?? 'pending',
                'created_at' => $purchase['created_at'] ?? null,
            ];
        }
        foreach ($socialOrders as $order) {
            $activity[] = [
                'label' => 'Boosting Order',
                'detail' => $order['service_name'] ?? 'Social service',
                'amount' => -(float)($order['charge'] ?? 0),
                'status' => $order['status'] ?? 'pending',
                'created_at' => $order['created_at'] ?? null,
            ];
        }

        usort($activity, function ($a, $b) {
            return strtotime((string)($b['created_at'] ?? '')) <=> strtotime((string)($a['created_at'] ?? ''));
        });

        render('user/wallet', [
            'title' => 'Wallet',
            'balance' => (float)($user['balance'] ?? 0),
            'transactions' => $transactions,
            'activity' => $activity,
        ]);
    }

    public function profile(): void
    {
        $user = current_user();
        $referralCode = $user['referral_code'] ?? '';
        if (!$referralCode) {
            for ($i = 0; $i < 5; $i++) {
                $candidate = strtoupper(bin2hex(random_bytes(4)));
                if (!$this->users->findByReferralCode($candidate)) {
                    $this->users->updateReferralCode($user['id'], $candidate);
                    $user = $this->users->findById($user['id']);
                    $_SESSION['user'] = $user;
                    $referralCode = $candidate;
                    break;
                }
            }
        }
        $referralLink = $referralCode ? url('/register?ref=' . urlencode($referralCode)) : '';
        $referralsCount = $this->users->countReferrals($user['id']);
        $referralEarnings = $this->transactions->referralEarnings($user['id']);

        render('user/profile', [
            'title' => 'Profile',
            'referralLink' => $referralLink,
            'referralsCount' => $referralsCount,
            'referralEarnings' => $referralEarnings,
        ]);
    }

    public function updateProfile(): void
    {
        verify_csrf();
        $user = current_user();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (!$name || !$email) {
            flash('error', 'Name and email are required.');
            redirect('/profile');
        }

        $existing = $this->users->findByEmail($email);
        if ($existing && (int)$existing['id'] !== (int)$user['id']) {
            flash('error', 'Email is already in use.');
            redirect('/profile');
        }

        $profileImage = null;
        if (!empty($_FILES['profile_image']['name'])) {
            $uploadDir = __DIR__ . '/../../public/uploads';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $filename = 'profile-' . $user['id'] . '-' . time() . '.' . $ext;
            $target = $uploadDir . '/' . $filename;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
                $profileImage = '/uploads/' . $filename;
            }
        }

        $this->users->updateProfileInfo($user['id'], $name, $email, $profileImage);
        $_SESSION['user'] = $this->users->findById($user['id']);
        flash('success', 'Profile updated.');
        redirect('/profile');
    }

    public function updatePassword(): void
    {
        verify_csrf();
        $user = current_user();
        $current = $_POST['current_password'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!$current || !$password || !$confirm) {
            flash('error', 'All password fields are required.');
            redirect('/profile');
        }

        $fresh = $this->users->findById($user['id']);
        if (!$fresh || !password_verify($current, $fresh['password_hash'])) {
            flash('error', 'Current password is incorrect.');
            redirect('/profile');
        }

        if ($password !== $confirm) {
            flash('error', 'Passwords do not match.');
            redirect('/profile');
        }

        $this->users->updatePassword($user['id'], password_hash($password, PASSWORD_BCRYPT));
        flash('success', 'Password updated.');
        redirect('/profile');
    }

    public function help(): void
    {
        $user = current_user();
        $tickets = $this->tickets->allByUser($user['id']);

        render('user/help', [
            'title' => 'Help Center',
            'tickets' => $tickets,
        ]);
    }

    public function submitTicket(): void
    {
        verify_csrf();
        $user = current_user();
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (!$subject || !$message) {
            flash('error', 'Subject and message are required.');
            redirect('/help');
        }

        $this->tickets->create($user['id'], $subject, $message);
        flash('success', 'Ticket submitted. Our team will respond soon.');
        redirect('/help');
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

    private function autoSyncBoostingServices(): void
    {
        $lastSync = $this->settings->get('peakerr_services_last_sync');
        $lastSyncTs = $lastSync ? strtotime($lastSync) : 0;
        $now = time();

        if ($now - $lastSyncTs < 3600) {
            return;
        }

        $this->syncBoostingServicesInternal();
    }

    private function syncBoostingServicesInternal(): ?int
    {
        $config = app_config();
        $client = new PeakerrClient($config['peakerr']);
        $services = $client->services();

        if (!is_array($services) || isset($services['error']) || isset($services['success'])) {
            return null;
        }

        $count = $this->socialServices->upsertFromPeakerr($services);
        $this->settings->set('peakerr_services_last_sync', date('c'));
        return $count;
    }
}
