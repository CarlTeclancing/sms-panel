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
    private AccountListingRepository $accountListings;
    private AccountPurchaseRepository $accountPurchases;
    private SellerFeeRepository $sellerFees;
    private WithdrawalRepository $withdrawals;

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
        $this->accountListings = new AccountListingRepository();
        $this->accountPurchases = new AccountPurchaseRepository();
        $this->sellerFees = new SellerFeeRepository();
        $this->withdrawals = new WithdrawalRepository();
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
        $topupBalance = (float)($user['balance_topup'] ?? $user['balance'] ?? 0);
        if ($topupBalance < $cost) {
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

        $newTopupBalance = $topupBalance - $cost;
        $this->users->updateTopupBalance($user['id'], $newTopupBalance);
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
        $topupBalance = (float)($user['balance_topup'] ?? $user['balance'] ?? 0);
        if ($topupBalance < $cost) {
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

        $newTopupBalance = $topupBalance - $cost;
        $this->users->updateTopupBalance($user['id'], $newTopupBalance);
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
            'balanceTopup' => (float)($user['balance_topup'] ?? $user['balance'] ?? 0),
            'balanceEarnings' => (float)($user['balance_earnings'] ?? 0),
            'transactions' => $transactions,
            'activity' => $activity,
            'activePaymentProvider' => $this->settings->get('active_payment_provider') ?? 'fapshi',
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

        $storeSlug = $user['store_slug'] ?? '';
        $storeUrl = $storeSlug ? url('/store/' . $storeSlug) : '';

        render('user/profile', [
            'title' => 'Profile',
            'referralLink' => $referralLink,
            'referralsCount' => $referralsCount,
            'referralEarnings' => $referralEarnings,
            'storeUrl' => $storeUrl,
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

    public function updateStoreProfile(): void
    {
        verify_csrf();
        $user = current_user();
        $storeName = trim($_POST['store_name'] ?? '');
        $storeSlug = trim($_POST['store_slug'] ?? '');
        $storeTagline = trim($_POST['store_tagline'] ?? '');
        $storeDescription = trim($_POST['store_description'] ?? '');

        if ($storeName === '') {
            flash('error', 'Store name is required.');
            redirect('/profile');
        }

        $slugBase = $storeSlug !== '' ? $storeSlug : $storeName;
        $slug = slugify($slugBase);
        if ($slug === '') {
            flash('error', 'Provide a valid store URL slug.');
            redirect('/profile');
        }

        $existing = $this->users->findByStoreSlug($slug);
        if ($existing && (int)$existing['id'] !== (int)$user['id']) {
            flash('error', 'Store URL is already taken.');
            redirect('/profile');
        }

        $this->users->updateStoreProfile($user['id'], $storeName, $slug, $storeTagline, $storeDescription);
        $_SESSION['user'] = $this->users->findById($user['id']);
        flash('success', 'Store branding updated.');
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

        $activeProvider = $this->settings->get('active_payment_provider') ?? 'fapshi';

        if ($amount <= 0 || ($activeProvider === 'fapshi' && (!$phone || !$provider)) || ($activeProvider === 'swychr' && !$phone)) {
            flash('error', 'Please provide valid refill details.');
            redirect('/wallet');
        }

        if ($activeProvider === 'swychr') {
            // Use the new plain PHP Swychr integration for all top-ups
            $name = $user['name'] ?? ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '');
            $email = $user['email'] ?? '';
            $mobile = $phone;
            $amount = (float)($_POST['amount'] ?? 0);
            $description = 'Wallet deposit for user #' . $user['id'];

            $_POST['name'] = $name;
            $_POST['email'] = $email;
            $_POST['mobile'] = $mobile;
            $_POST['amount'] = $amount;
            $_POST['description'] = $description;

            require_once __DIR__ . '/../services/SwychrPlainIntegration.php';
            exit; // The integration script will handle the redirect or output
        }

        $config = app_config();
        $fapshiKey = $this->settings->get('fapshi_api_key') ?? $config['fapshi']['api_key'];
        $client = new FapshiClient([
            'base_url' => $config['fapshi']['base_url'],
            'api_key' => $fapshiKey,
        ]);

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

    private function parseListSetting(?string $value): array
    {
        if (!$value) {
            return [];
        }
        $parts = preg_split('/[\r\n,]+/', $value);
        $clean = [];
        foreach ($parts as $part) {
            $item = trim($part);
            if ($item !== '') {
                $clean[] = $item;
            }
        }
        return array_values(array_unique($clean));
    }

    public function accountsMarketplace(): void
    {
        $categories = $this->parseListSetting($this->settings->get('account_categories'));
        $platforms = $this->parseListSetting($this->settings->get('account_platforms'));

        $filters = [
            'category' => trim($_GET['category'] ?? ''),
            'platform' => trim($_GET['platform'] ?? ''),
            'year' => trim($_GET['year'] ?? ''),
            'search' => trim($_GET['search'] ?? ''),
        ];

        $listings = $this->accountListings->allApproved($filters);
        render('user/accounts', [
            'title' => 'Account Marketplace',
            'listings' => $listings,
            'categories' => $categories,
            'platforms' => $platforms,
            'filters' => $filters,
        ]);
    }

    public function sellAccount(): void
    {
        $user = current_user();
        $categories = $this->parseListSetting($this->settings->get('account_categories'));
        $platforms = $this->parseListSetting($this->settings->get('account_platforms'));
        $sellerFee = (float)($this->settings->get('account_seller_fee') ?? 0);
        $hasPaidFee = $this->sellerFees->hasPaid($user['id']) || $sellerFee <= 0;
        $listings = $this->accountListings->allBySeller($user['id']);

        $storeSlug = $user['store_slug'] ?? '';
        $storeUrl = $storeSlug ? url('/store/' . $storeSlug) : '';

        render('user/account-sell', [
            'title' => 'Sell Accounts',
            'categories' => $categories,
            'platforms' => $platforms,
            'sellerFee' => $sellerFee,
            'hasPaidFee' => $hasPaidFee,
            'listings' => $listings,
            'storeUrl' => $storeUrl,
        ]);
    }

    public function publicStore(string $slug): void
    {
        $storeOwner = $this->users->findByStoreSlug($slug);
        if (!$storeOwner) {
            http_response_code(404);
            echo 'Store not found';
            exit;
        }

        $listings = $this->accountListings->allApprovedBySellerId((int)$storeOwner['id']);
        render('store', [
            'title' => ($storeOwner['store_name'] ?? $storeOwner['name']) . ' Store',
            'storeOwner' => $storeOwner,
            'listings' => $listings,
        ]);
    }

    public function createAccountListing(): void
    {
        verify_csrf();
        $user = current_user();
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $platform = trim($_POST['platform'] ?? '');
        $year = (int)($_POST['year'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $details = trim($_POST['account_details'] ?? '');

        if (!$title || !$category || !$platform || $price <= 0 || !$details) {
            flash('error', 'Please fill all required fields.');
            redirect('/accounts/sell');
        }

        $sellerFee = (float)($this->settings->get('account_seller_fee') ?? 0);
        $hasPaidFee = $this->sellerFees->hasPaid($user['id']) || $sellerFee <= 0;
        if (!$hasPaidFee) {
            $topupBalance = (float)($user['balance_topup'] ?? $user['balance'] ?? 0);
            if ($topupBalance < $sellerFee) {
                flash('error', 'Insufficient balance to pay the one-time seller fee.');
                redirect('/wallet');
            }

            $newTopupBalance = $topupBalance - $sellerFee;
            $this->users->updateTopupBalance($user['id'], $newTopupBalance);
            $_SESSION['user'] = $this->users->findById($user['id']);

            $ref = 'seller-fee-' . time();
            $this->sellerFees->create($user['id'], $sellerFee, $ref);
            $this->transactions->create([
                'user_id' => $user['id'],
                'type' => 'adjustment',
                'amount' => -$sellerFee,
                'ref' => $ref,
                'provider' => 'seller_fee',
                'status' => 'success',
                'meta' => json_encode(['fee' => $sellerFee]),
            ]);
        }

        $this->accountListings->create([
            'seller_id' => $user['id'],
            'title' => $title,
            'category' => $category,
            'platform' => $platform,
            'year' => $year > 0 ? $year : null,
            'price' => $price,
            'description' => $description,
            'account_details' => $details,
            'status' => 'pending',
        ]);

        flash('success', 'Listing submitted for verification.');
        redirect('/accounts/sell');
    }

    public function purchaseAccount(): void
    {
        verify_csrf();
        $user = current_user();
        $listingId = (int)($_POST['listing_id'] ?? 0);
        $listing = $this->accountListings->findById($listingId);

        if (!$listing || $listing['status'] !== 'approved' || !empty($listing['sold_at'])) {
            flash('error', 'Listing not available.');
            redirect('/accounts');
        }

        if ((int)$listing['seller_id'] === (int)$user['id']) {
            flash('error', 'You cannot buy your own listing.');
            redirect('/accounts');
        }

        $price = (float)$listing['price'];
        $topupBalance = (float)($user['balance_topup'] ?? $user['balance'] ?? 0);
        if ($topupBalance < $price) {
            flash('error', 'Insufficient balance to complete purchase.');
            redirect('/wallet');
        }

        $newTopupBalance = $topupBalance - $price;
        $this->users->updateTopupBalance($user['id'], $newTopupBalance);
        $_SESSION['user'] = $this->users->findById($user['id']);

        $seller = $this->users->findById((int)$listing['seller_id']);
        if ($seller) {
            $this->users->incrementEarningsBalance($seller['id'], $price);
            $this->transactions->create([
                'user_id' => $seller['id'],
                'type' => 'adjustment',
                'amount' => $price,
                'ref' => 'account-sale-' . $listingId,
                'provider' => 'account_market',
                'status' => 'success',
                'meta' => json_encode(['listing_id' => $listingId, 'buyer_id' => $user['id']]),
            ]);
        }

        $this->transactions->create([
            'user_id' => $user['id'],
            'type' => 'purchase',
            'amount' => -$price,
            'ref' => 'account-buy-' . $listingId,
            'provider' => 'account_market',
            'status' => 'success',
            'meta' => json_encode(['listing_id' => $listingId]),
        ]);

        $this->accountListings->markSold($listingId, $user['id']);
        $this->accountPurchases->create([
            'listing_id' => $listingId,
            'buyer_id' => $user['id'],
            'seller_id' => (int)$listing['seller_id'],
            'price' => $price,
            'platform_fee' => 0,
            'net_amount' => $price,
            'details_snapshot' => $listing['account_details'],
        ]);

        $subject = 'Your account purchase details';
        $message = "Thank you for your purchase.\n\nListing: {$listing['title']}\nCategory: {$listing['category']}\nPlatform: {$listing['platform']}\nYear: " . ($listing['year'] ?: '-') . "\nPrice: $" . number_format($price, 2) . "\n\nAccount Details:\n{$listing['account_details']}\n\nPlease keep this information secure.";
        send_email($user['email'], $subject, $message);

        flash('success', 'Purchase completed. Details sent to your email and available in your account.');
        redirect('/accounts/purchases');
    }

    public function accountPurchases(): void
    {
        $user = current_user();
        $purchases = $this->accountPurchases->allByBuyer($user['id']);
        render('user/account-purchases', [
            'title' => 'Purchased Accounts',
            'purchases' => $purchases,
        ]);
    }

    public function withdrawals(): void
    {
        $user = current_user();
        $withdrawals = $this->withdrawals->allByUser($user['id']);
        $feePercent = (float)($this->settings->get('account_withdrawal_fee_percent') ?? 10);
        render('user/account-withdrawals', [
            'title' => 'Withdrawals',
            'withdrawals' => $withdrawals,
            'feePercent' => $feePercent,
        ]);
    }

    public function requestWithdrawal(): void
    {
        verify_csrf();
        $user = current_user();
        $amount = (float)($_POST['amount'] ?? 0);
        $note = trim($_POST['note'] ?? '');

        if ($amount <= 0) {
            flash('error', 'Enter a valid amount.');
            redirect('/accounts/withdrawals');
        }
        $earningsBalance = (float)($user['balance_earnings'] ?? 0);
        if ($earningsBalance < $amount) {
            flash('error', 'Insufficient balance for withdrawal.');
            redirect('/accounts/withdrawals');
        }

        $feePercent = (float)($this->settings->get('account_withdrawal_fee_percent') ?? 10);
        $fee = $amount * ($feePercent / 100);
        $net = $amount - $fee;

        $this->withdrawals->create([
            'user_id' => $user['id'],
            'amount' => $amount,
            'fee' => $fee,
            'net_amount' => $net,
            'status' => 'pending',
            'note' => $note,
        ]);

        flash('success', 'Withdrawal request submitted. Admin approval required.');
        redirect('/accounts/withdrawals');
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
