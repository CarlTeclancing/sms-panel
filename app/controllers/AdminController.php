<?php

class AdminController
{
    private ServiceRepository $services;
    private UserRepository $users;
    private TransactionRepository $transactions;
    private ApiKeyRepository $apiKeys;
    private SettingsRepository $settings;

    public function __construct()
    {
        $this->services = new ServiceRepository();
        $this->users = new UserRepository();
        $this->transactions = new TransactionRepository();
        $this->apiKeys = new ApiKeyRepository();
        $this->settings = new SettingsRepository();
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
        $services = $this->services->allActive();
        $logo = $this->settings->get('logo_path');
        render('admin/services', [
            'title' => 'Manage Services',
            'services' => $services,
            'logo' => $logo,
        ]);
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

    public function settingsPage(): void
    {
        $logo = $this->settings->get('logo_path');
        render('admin/settings', [
            'title' => 'Settings',
            'logo' => $logo,
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

        $this->users->incrementBalance($userId, $amount);
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
                flash('success', 'Logo updated.');
                redirect('/admin');
            }
        }

        flash('error', 'Logo upload failed.');
        redirect('/admin');
    }
}
