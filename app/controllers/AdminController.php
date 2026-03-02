<?php

class AdminController
{
    private ServiceRepository $services;
    private SocialServiceRepository $socialServices;
    private UserRepository $users;
    private TransactionRepository $transactions;
    private ApiKeyRepository $apiKeys;
    private SettingsRepository $settings;
    private TicketRepository $tickets;
    private AccountListingRepository $accountListings;
    private WithdrawalRepository $withdrawals;

    public function __construct()
    {
        $this->services = new ServiceRepository();
        $this->socialServices = new SocialServiceRepository();
        $this->users = new UserRepository();
        $this->transactions = new TransactionRepository();
        $this->apiKeys = new ApiKeyRepository();
        $this->settings = new SettingsRepository();
        $this->tickets = new TicketRepository();
        $this->accountListings = new AccountListingRepository();
        $this->withdrawals = new WithdrawalRepository();
    }

    public function dashboard(): void
    {
        $services = $this->services->allActive();
        $users = $this->users->all();
        $transactions = $this->transactions->allFiltered([
            'type' => $_GET['type'] ?? null,
            'status' => $_GET['status'] ?? null,
            'user_id' => $_GET['user_id'] ?? null,
            'from' => $_GET['from'] ?? null,
            'to' => $_GET['to'] ?? null,
        ]);
        $logo = $this->settings->get('logo_path');
        $totalUsers = $this->users->countAll();
        $totalServices = $this->services->countAll();
        $totalRevenue = $this->transactions->totalRevenue();
        $dailyRevenue = $this->transactions->dailyRevenue(14);
        render('admin/dashboard', [
            'title' => 'Admin Console',
            'services' => $services,
            'users' => $users,
            'transactions' => $transactions,
            'logo' => $logo,
            'totalUsers' => $totalUsers,
            'totalServices' => $totalServices,
            'totalRevenue' => $totalRevenue,
            'dailyRevenue' => $dailyRevenue,
        ]);
    }

    public function services(): void
    {
        $this->autoSyncServices();
        $perPage = 20;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $totalServices = $this->services->countActive();
        $totalPages = max(1, (int)ceil($totalServices / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;
        $services = $this->services->paginateActive($perPage, $offset);
        $logo = $this->settings->get('logo_path');
        render('admin/services', [
            'title' => 'Manage Services',
            'services' => $services,
            'logo' => $logo,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalServices' => $totalServices,
            'perPage' => $perPage,
        ]);
    }

    public function boostingServices(): void
    {
        $this->autoSyncBoostingServices();
        $services = $this->socialServices->allActive();
        $logo = $this->settings->get('logo_path');
        render('admin/boosting-services', [
            'title' => 'Boosting Services',
            'services' => $services,
            'logo' => $logo,
        ]);
    }

    public function syncBoostingServices(): void
    {
        verify_csrf();
        $count = $this->syncBoostingServicesInternal();
        if ($count === null) {
            flash('error', 'Failed to fetch boosting services from Peakerr.');
            redirect('/admin/boosting-services');
        }
        flash('success', "Synced {$count} boosting services from Peakerr.");
        redirect('/admin/boosting-services');
    }

    public function syncServices(): void
    {
        verify_csrf();
        $count = $this->syncServicesInternal();
        if ($count === null) {
            flash('error', 'Failed to fetch services from SMS-Man.');
            redirect('/admin/services');
        }
        flash('success', "Synced {$count} services from SMS-Man.");
        redirect('/admin/services');
    }

    private function autoSyncServices(): void
    {
        $lastSync = $this->settings->get('smsman_services_last_sync');
        $lastSyncTs = $lastSync ? strtotime($lastSync) : 0;
        $now = time();

        if ($now - $lastSyncTs < 3600) {
            return;
        }

        $this->syncServicesInternal();
    }

    private function syncServicesInternal(): ?int
    {
        $config = app_config();
            $client = new SmsManClient($config['smsman']);
            $apps = $client->getApplications(true);
            $prices = $client->getPrices(0, true);

        if (!is_array($apps)) {
            return null;
        }

        if (isset($apps['success']) && $apps['success'] === false) {
            return null;
        }

        if (isset($apps['data']) && is_array($apps['data'])) {
            $apps = $apps['data'];
        }

        if (!empty($apps) && array_keys($apps) !== range(0, count($apps) - 1)) {
            $apps = array_values($apps);
        }

        if (is_array($prices) && isset($prices['data']) && is_array($prices['data'])) {
            $prices = $prices['data'];
        }

        $priceMap = [];
        if (is_array($prices) && !isset($prices['success'])) {
            foreach ($prices as $countryId => $appsPrices) {
                if (!is_array($appsPrices)) {
                    continue;
                }
                foreach ($appsPrices as $appId => $entry) {
                    $appId = isset($entry['application_id']) ? (int)$entry['application_id'] : (int)$appId;
                    $cost = isset($entry['cost']) ? (float)$entry['cost'] : null;
                    if ($cost === null) {
                        continue;
                    }
                    if (!isset($priceMap[$appId]) || $cost < $priceMap[$appId]) {
                        $priceMap[$appId] = $cost;
                    }
                }
            }
        }

        $count = $this->services->upsertFromSmsMan($apps, $priceMap);
        $this->settings->set('smsman_services_last_sync', date('c'));
        return $count;
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

    public function users(): void
    {
        $users = $this->users->all();
        $logo = $this->settings->get('logo_path');
        render('admin/users', [
            'title' => 'User Management',
            'users' => $users,
            'logo' => $logo,
        ]);
    }

    public function transactions(): void
    {
        $transactions = $this->transactions->allFiltered([
            'type' => $_GET['type'] ?? null,
            'status' => $_GET['status'] ?? null,
            'user_id' => $_GET['user_id'] ?? null,
            'from' => $_GET['from'] ?? null,
            'to' => $_GET['to'] ?? null,
        ]);
        $logo = $this->settings->get('logo_path');
        render('admin/transactions', [
            'title' => 'Transaction Logs',
            'transactions' => $transactions,
            'logo' => $logo,
        ]);
    }

    public function apiKeys(): void
    {
        $apiKeys = $this->apiKeys->all();
        $logo = $this->settings->get('logo_path');
        render('admin/api-keys', [
            'title' => 'API Keys',
            'apiKeys' => $apiKeys,
            'logo' => $logo,
        ]);
    }

    public function notifications(): void
    {
        $logo = $this->settings->get('logo_path');
        render('admin/notifications', [
            'title' => 'Notifications',
            'logo' => $logo,
        ]);
    }

    public function tickets(): void
    {
        $logo = $this->settings->get('logo_path');
        $tickets = $this->tickets->all();
        render('admin/tickets', [
            'title' => 'Support Tickets',
            'tickets' => $tickets,
            'logo' => $logo,
        ]);
    }

    public function settingsPage(): void
    {
        $logo = $this->settings->get('logo_path');
        $smsMarkup = $this->settings->get('sms_markup_percent') ?? '0';
        $boostMarkup = $this->settings->get('boost_markup_percent') ?? '0';
        $sellerFee = $this->settings->get('account_seller_fee') ?? '0';
        $withdrawFee = $this->settings->get('account_withdrawal_fee_percent') ?? '10';
        $accountCategories = $this->settings->get('account_categories') ?? '';
        $accountPlatforms = $this->settings->get('account_platforms') ?? '';
        $activePaymentProvider = $this->settings->get('active_payment_provider') ?? 'fapshi';
        $swychrBaseUrl = $this->settings->get('swychr_base_url') ?? 'https://app.swychrconnect.com';
        $swychrToken = $this->settings->get('swychr_token') ?? '';
        $swychrEmail = $this->settings->get('swychr_email') ?? '';
        $swychrPassword = $this->settings->get('swychr_password') ?? '';
        $swychrCountryCode = $this->settings->get('swychr_country_code') ?? 'CM';
        $swychrCurrency = $this->settings->get('swychr_currency') ?? 'XAF';
        $swychrPassDigitalCharge = $this->settings->get('swychr_pass_digital_charge') ?? '1';
        $fapshiApiKey = $this->settings->get('fapshi_api_key') ?? '';
        render('admin/settings', [
            'title' => 'Settings',
            'logo' => $logo,
            'smsMarkup' => $smsMarkup,
            'boostMarkup' => $boostMarkup,
            'sellerFee' => $sellerFee,
            'withdrawFee' => $withdrawFee,
            'accountCategories' => $accountCategories,
            'accountPlatforms' => $accountPlatforms,
            'activePaymentProvider' => $activePaymentProvider,
            'swychrBaseUrl' => $swychrBaseUrl,
            'swychrToken' => $swychrToken,
            'swychrEmail' => $swychrEmail,
            'swychrPassword' => $swychrPassword,
            'swychrCountryCode' => $swychrCountryCode,
            'swychrCurrency' => $swychrCurrency,
            'swychrPassDigitalCharge' => $swychrPassDigitalCharge,
            'fapshiApiKey' => $fapshiApiKey,
        ]);
    }

    public function updatePrice(): void
    {
        verify_csrf();
        $id = (int)($_POST['service_id'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);

        if ($id <= 0 || $price <= 0) {
            flash('error', 'Provide a valid price.');
            redirect('/admin');
        }

        $this->services->updatePrice($id, $price);
        flash('success', 'Price updated.');
        redirect('/admin');
    }

    public function updateUser(): void
    {
        verify_csrf();
        $userId = (int)($_POST['user_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = trim($_POST['role'] ?? 'user');

        if ($userId <= 0 || !$name || !$email) {
            flash('error', 'Invalid user data.');
            redirect('/admin');
        }

        $this->users->updateProfile($userId, $name, $email, $role === 'admin' ? 'admin' : 'user');
        flash('success', 'User updated.');
        redirect('/admin');
    }

    public function toggleUser(): void
    {
        verify_csrf();
        $userId = (int)($_POST['user_id'] ?? 0);
        $active = (int)($_POST['active'] ?? 0);

        if ($userId <= 0) {
            flash('error', 'Invalid user.');
            redirect('/admin');
        }

        $this->users->updateActive($userId, $active === 1 ? 1 : 0);
        flash('success', 'User status updated.');
        redirect('/admin');
    }

    public function adjustBalance(): void
    {
        verify_csrf();
        $userId = (int)($_POST['user_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0);
        $note = trim($_POST['note'] ?? '');

        if ($userId <= 0 || $amount == 0) {
            flash('error', 'Invalid adjustment.');
            redirect('/admin');
        }

        $this->users->incrementTopupBalance($userId, $amount);
        $this->transactions->create([
            'user_id' => $userId,
            'type' => 'adjustment',
            'amount' => $amount,
            'ref' => 'admin-adjust-' . time(),
            'provider' => 'admin',
            'status' => 'success',
            'meta' => json_encode(['note' => $note]),
        ]);

        flash('success', 'Balance adjusted.');
        redirect('/admin');
    }

    public function rotateApiKey(): void
    {
        verify_csrf();
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId <= 0) {
            flash('error', 'Invalid user.');
            redirect('/admin');
        }

        $this->apiKeys->deleteByUser($userId);
        $token = bin2hex(random_bytes(24));
        $this->apiKeys->create($userId, $token);
        flash('success', 'API token rotated.');
        redirect('/admin');
    }

    public function settings(): void
    {
        verify_csrf();
        $smsMarkup = (float)($_POST['sms_markup_percent'] ?? 0);
        $boostMarkup = (float)($_POST['boost_markup_percent'] ?? 0);
        $sellerFee = (float)($_POST['account_seller_fee'] ?? 0);
        $withdrawFee = (float)($_POST['account_withdrawal_fee_percent'] ?? 10);
        $accountCategories = trim($_POST['account_categories'] ?? '');
        $accountPlatforms = trim($_POST['account_platforms'] ?? '');
        $activePaymentProvider = trim($_POST['active_payment_provider'] ?? 'fapshi');
        $swychrBaseUrl = trim($_POST['swychr_base_url'] ?? 'https://app.swychrconnect.com');
        $swychrToken = trim($_POST['swychr_token'] ?? '');
        $swychrEmail = trim($_POST['swychr_email'] ?? '');
        $swychrPassword = trim($_POST['swychr_password'] ?? '');
        $swychrCountryCode = trim($_POST['swychr_country_code'] ?? 'CM');
        $swychrCurrency = trim($_POST['swychr_currency'] ?? 'XAF');
        $swychrPassDigitalCharge = trim($_POST['swychr_pass_digital_charge'] ?? '1');
        $fapshiApiKey = trim($_POST['fapshi_api_key'] ?? '');

        $this->settings->set('sms_markup_percent', (string)$smsMarkup);
        $this->settings->set('boost_markup_percent', (string)$boostMarkup);
        $this->settings->set('account_seller_fee', (string)$sellerFee);
        $this->settings->set('account_withdrawal_fee_percent', (string)$withdrawFee);
        $this->settings->set('account_categories', $accountCategories);
        $this->settings->set('account_platforms', $accountPlatforms);
        $this->settings->set('active_payment_provider', $activePaymentProvider === 'swychr' ? 'swychr' : 'fapshi');
        $this->settings->set('swychr_base_url', $swychrBaseUrl);
        $this->settings->set('swychr_token', $swychrToken);
        $this->settings->set('swychr_email', $swychrEmail);
        $this->settings->set('swychr_password', $swychrPassword);
        $this->settings->set('swychr_country_code', $swychrCountryCode);
        $this->settings->set('swychr_currency', $swychrCurrency);
        $this->settings->set('swychr_pass_digital_charge', $swychrPassDigitalCharge === '0' ? '0' : '1');
        $this->settings->set('fapshi_api_key', $fapshiApiKey);

        $updatedLogo = false;
        if (!empty($_FILES['logo']['name'])) {
            $uploadDir = __DIR__ . '/../../public/uploads';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $filename = 'logo-' . time() . '.' . $ext;
            $target = $uploadDir . '/' . $filename;
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
                $this->settings->set('logo_path', '/uploads/' . $filename);
                $updatedLogo = true;
            }
        }

        if ($updatedLogo) {
            flash('success', 'Settings updated.');
        } else {
            flash('success', 'Settings updated.');
        }
        redirect('/admin/settings');
    }

    public function accountListings(): void
    {
        $logo = $this->settings->get('logo_path');
        $listings = $this->accountListings->allPending();
        render('admin/account-listings', [
            'title' => 'Account Listings',
            'logo' => $logo,
            'listings' => $listings,
        ]);
    }

    public function approveAccountListing(): void
    {
        verify_csrf();
        $listingId = (int)($_POST['listing_id'] ?? 0);
        if ($listingId <= 0) {
            flash('error', 'Invalid listing.');
            redirect('/admin/account-listings');
        }
        $this->accountListings->updateStatus($listingId, 'approved');
        flash('success', 'Listing approved.');
        redirect('/admin/account-listings');
    }

    public function rejectAccountListing(): void
    {
        verify_csrf();
        $listingId = (int)($_POST['listing_id'] ?? 0);
        if ($listingId <= 0) {
            flash('error', 'Invalid listing.');
            redirect('/admin/account-listings');
        }
        $this->accountListings->updateStatus($listingId, 'rejected');
        flash('success', 'Listing rejected.');
        redirect('/admin/account-listings');
    }

    public function withdrawals(): void
    {
        $logo = $this->settings->get('logo_path');
        $requests = $this->withdrawals->allPending();
        render('admin/withdrawals', [
            'title' => 'Withdrawal Requests',
            'logo' => $logo,
            'requests' => $requests,
        ]);
    }

    public function approveWithdrawal(): void
    {
        verify_csrf();
        $requestId = (int)($_POST['request_id'] ?? 0);
        $request = $this->withdrawals->findById($requestId);

        if (!$request || $request['status'] !== 'pending') {
            flash('error', 'Invalid withdrawal request.');
            redirect('/admin/withdrawals');
        }

        $user = $this->users->findById((int)$request['user_id']);
        $earningsBalance = (float)($user['balance_earnings'] ?? 0);
        if (!$user || $earningsBalance < (float)$request['amount']) {
            flash('error', 'User balance insufficient for this withdrawal.');
            redirect('/admin/withdrawals');
        }

        $this->users->incrementEarningsBalance($user['id'], -(float)$request['amount']);
        $this->transactions->create([
            'user_id' => $user['id'],
            'type' => 'adjustment',
            'amount' => -(float)$request['amount'],
            'ref' => 'withdrawal-' . $requestId,
            'provider' => 'withdrawal',
            'status' => 'success',
            'meta' => json_encode(['fee' => $request['fee'], 'net' => $request['net_amount']]),
        ]);

        $this->withdrawals->updateStatus($requestId, 'approved');
        flash('success', 'Withdrawal approved.');
        redirect('/admin/withdrawals');
    }

    public function rejectWithdrawal(): void
    {
        verify_csrf();
        $requestId = (int)($_POST['request_id'] ?? 0);
        if ($requestId <= 0) {
            flash('error', 'Invalid withdrawal request.');
            redirect('/admin/withdrawals');
        }
        $note = trim($_POST['note'] ?? '');
        $this->withdrawals->updateStatus($requestId, 'rejected', $note);
        flash('success', 'Withdrawal rejected.');
        redirect('/admin/withdrawals');
    }
}
