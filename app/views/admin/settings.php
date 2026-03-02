<?php
$sidebarItems = [
    ['label' => 'Dashboard', 'href' => '/admin', 'icon' => 'layout-dashboard'],
    ['label' => 'Services', 'href' => '/admin/services', 'icon' => 'smartphone'],
    ['label' => 'Boosting Services', 'href' => '/admin/boosting-services', 'icon' => 'rocket'],
    ['label' => 'Account Listings', 'href' => '/admin/account-listings', 'icon' => 'shield-check'],
    ['label' => 'Withdrawals', 'href' => '/admin/withdrawals', 'icon' => 'banknote'],
    ['label' => 'Users', 'href' => '/admin/users', 'icon' => 'users'],
    ['label' => 'Transactions', 'href' => '/admin/transactions', 'icon' => 'receipt'],
    ['label' => 'API Keys', 'href' => '/admin/api-keys', 'icon' => 'key'],
    ['label' => 'Tickets', 'href' => '/admin/tickets', 'icon' => 'life-buoy'],
    ['label' => 'Notifications', 'href' => '/admin/notifications', 'icon' => 'bell'],
    ['label' => 'Settings', 'href' => '/admin/settings', 'icon' => 'settings'],
];
$currentPath = current_path();
?>

<div class="flex min-h-screen w-full bg-slate-50 overflow-hidden">
    <aside id="adminSidebar" class="hidden md:flex w-72 bg-white border-r border-slate-200 flex-col h-full overflow-hidden fixed md:static inset-y-0 left-0 z-40" style="margin-left:0;padding-left:0;left:0;">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <?php if (!empty($logo)): ?>
                <img src="<?= htmlspecialchars(url($logo)) ?>" alt="Logo" class="h-10">
            <?php endif; ?>
            <div>
                <p class="text-lg font-semibold text-slate-900">Admin Panel</p>
                <p class="text-xs text-slate-500">GetSMS Control</p>
            </div>
            <button id="closeSidebar" class="md:hidden border border-slate-200 rounded px-2 py-1 text-xs">Close</button>
        </div>
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <?php foreach ($sidebarItems as $item): ?>
                <?php $isActive = $currentPath === $item['href']; ?>
                <a href="<?= url($item['href']) ?>" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-sm font-medium <?= $isActive ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                    <i data-lucide="<?= htmlspecialchars($item['icon']) ?>" class="w-4 h-4"></i>
                    <span><?= htmlspecialchars($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <form method="post" action="<?= url('/logout') ?>" class="p-4 border-t border-slate-200 bg-white">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <button class="w-full bg-danger text-white px-4 py-2 rounded inline-flex items-center justify-center space-x-2">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                <span>Logout</span>
            </button>
        </form>
    </aside>

    <div class="flex-1 min-h-0 overflow-y-auto">
        <div class="bg-white border-b border-slate-200 px-6 py-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 sticky top-0 z-10">
            <div class="flex items-center space-x-3">
                <button id="toggleSidebar" class="lg:hidden border border-slate-200 rounded px-3 py-1 text-sm">Menu</button>
                <div>
                    <h2 class="text-2xl font-semibold">Settings</h2>
                    <p class="text-sm text-slate-500">Update branding and configuration</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="relative">
                    <input type="text" placeholder="Search settings..." class="w-64 border border-slate-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-slate-700">Hi, <?= htmlspecialchars(current_user()['name']) ?></span>
                    <form method="post" action="<?= url('/logout') ?>">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <button class="bg-primary text-white px-3 py-2 rounded text-sm flex items-center space-x-2">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="p-6">
            <section class="bg-white border border-slate-200 rounded-lg p-5">
                <div class="grid lg:grid-cols-[220px_1fr] gap-6">
                    <aside class="border border-slate-200 rounded p-4 space-y-2">
                        <button class="settings-tab w-full text-left px-3 py-2 rounded text-sm font-medium bg-primary/10 text-primary" data-tab="branding">Branding</button>
                        <button class="settings-tab w-full text-left px-3 py-2 rounded text-sm font-medium text-slate-600 hover:bg-slate-50" data-tab="pricing">Pricing</button>
                        <button class="settings-tab w-full text-left px-3 py-2 rounded text-sm font-medium text-slate-600 hover:bg-slate-50" data-tab="marketplace">Marketplace</button>
                        <button class="settings-tab w-full text-left px-3 py-2 rounded text-sm font-medium text-slate-600 hover:bg-slate-50" data-tab="payments">Payments</button>
                    </aside>
                    <form method="post" action="<?= url('/admin/settings') ?>" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <section id="branding" class="settings-panel space-y-4">
                        <div>
                            <label class="block text-sm font-medium">Logo</label>
                            <input name="logo" type="file" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                            <?php if (!empty($logo)): ?>
                                <img src="<?= htmlspecialchars(url($logo)) ?>" alt="Current logo" class="h-10 mt-2">
                            <?php endif; ?>
                        </div>
                    </section>

                    <section id="pricing" class="settings-panel hidden space-y-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium">SMS services markup (%)</label>
                                <input name="sms_markup_percent" type="number" step="0.01" min="0" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" value="<?= htmlspecialchars((string)($smsMarkup ?? '0')) ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Boosting services markup (%)</label>
                                <input name="boost_markup_percent" type="number" step="0.01" min="0" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" value="<?= htmlspecialchars((string)($boostMarkup ?? '0')) ?>">
                            </div>
                        </div>
                    </section>

                    <section id="marketplace" class="settings-panel hidden space-y-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium">Account seller fee (one-time)</label>
                                <input name="account_seller_fee" type="number" step="0.01" min="0" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" value="<?= htmlspecialchars((string)($sellerFee ?? '0')) ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Withdrawal fee (%)</label>
                                <input name="account_withdrawal_fee_percent" type="number" step="0.01" min="0" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" value="<?= htmlspecialchars((string)($withdrawFee ?? '10')) ?>">
                            </div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium">Account categories (comma or new line separated)</label>
                                <textarea name="account_categories" rows="4" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" placeholder="e.g. Email, Social, Gaming"><?= htmlspecialchars((string)($accountCategories ?? '')) ?></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Account platforms (comma or new line separated)</label>
                                <textarea name="account_platforms" rows="4" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" placeholder="e.g. Gmail, Facebook, Steam"><?= htmlspecialchars((string)($accountPlatforms ?? '')) ?></textarea>
                            </div>
                        </div>
                    </section>

                    <section id="payments" class="settings-panel hidden space-y-4">
                        <div class="border border-slate-200 rounded p-4">
                            <h4 class="text-sm font-semibold text-slate-700">Active provider</h4>
                            <div class="mt-2 grid md:grid-cols-2 gap-4">
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" name="active_payment_provider" value="fapshi" <?= ($activePaymentProvider ?? 'fapshi') === 'fapshi' ? 'checked' : '' ?>>
                                    Fapshi
                                </label>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" name="active_payment_provider" value="swychr" <?= ($activePaymentProvider ?? 'fapshi') === 'swychr' ? 'checked' : '' ?>>
                                    Swychr
                                </label>
                            </div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium">Fapshi API key</label>
                                <input name="fapshi_api_key" type="text" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" value="<?= htmlspecialchars((string)($fapshiApiKey ?? '')) ?>">
                            </div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 my-4">
                            <div class="text-yellow-800 text-sm">
                                Swychr credentials and configuration are now managed via the <b>.env</b> file.<br>
                                Please update <b>.env</b> with the following keys:<br>
                                <code>SWYCHR_BASE_URL</code>, <code>SWYCHR_EMAIL</code>, <code>SWYCHR_PASSWORD</code>, <code>SWYCHR_COUNTRY_CODE</code>, <code>SWYCHR_CURRENCY</code>, <code>SWYCHR_PASS_DIGITAL_CHARGE</code>
                            </div>
                        </div>
                    </section>

                    <button class="bg-primary text-white px-3 py-2 rounded">Save settings</button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>

<?php
    $adminNavItems = [
        ['href' => '/admin', 'label' => 'Home', 'icon' => 'layout-dashboard'],
        ['href' => '/admin/services', 'label' => 'SMS', 'icon' => 'smartphone'],
        ['href' => '/admin/boosting-services', 'label' => 'Boost', 'icon' => 'rocket'],
        ['href' => '/admin/account-listings', 'label' => 'Accounts', 'icon' => 'shield-check'],
        ['href' => '/admin/withdrawals', 'label' => 'Withdrawals', 'icon' => 'banknote'],
        ['href' => '/admin/users', 'label' => 'Users', 'icon' => 'users'],
        ['href' => '/admin/settings', 'label' => 'Settings', 'icon' => 'settings'],
    ];
?>
<nav class="md:hidden fixed bottom-4 left-1/2 -translate-x-1/2 w-[calc(100%-2rem)] max-w-md bg-white/90 backdrop-blur border border-slate-200 shadow-lg rounded-2xl z-50">
    <div class="grid grid-cols-5 text-[11px]">
        <?php foreach ($adminNavItems as $item): ?>
            <?php $isActive = $currentPath === $item['href']; ?>
            <a href="<?= url($item['href']) ?>" class="flex flex-col items-center justify-center py-2.5 px-1 rounded-2xl <?= $isActive ? 'text-primary' : 'text-slate-500' ?>">
                <div class="w-9 h-9 flex items-center justify-center rounded-xl <?= $isActive ? 'bg-primary/10' : '' ?>">
                    <i data-lucide="<?= htmlspecialchars($item['icon']) ?>" class="w-5 h-5"></i>
                </div>
                <span class="mt-1 font-medium"><?= htmlspecialchars($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</nav>

<script>
    const toggleButton = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('adminSidebar');
    const closeSidebar = document.getElementById('closeSidebar');
    if (toggleButton && sidebar) {
        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
        });
    }
    if (closeSidebar && sidebar) {
        closeSidebar.addEventListener('click', () => {
            sidebar.classList.add('hidden');
        });
    }

    const settingsTabs = document.querySelectorAll('.settings-tab');
    const settingsPanels = document.querySelectorAll('.settings-panel');
    const setSettingsTab = (tabId) => {
        settingsPanels.forEach(panel => {
            panel.classList.toggle('hidden', panel.id !== tabId);
        });
        settingsTabs.forEach(btn => {
            const active = btn.dataset.tab === tabId;
            btn.classList.toggle('bg-primary/10', active);
            btn.classList.toggle('text-primary', active);
            btn.classList.toggle('text-slate-600', !active);
            btn.classList.toggle('hover:bg-slate-50', !active);
        });
    };

    settingsTabs.forEach(btn => {
        btn.addEventListener('click', () => setSettingsTab(btn.dataset.tab));
    });

    if (window.lucide) {
        window.lucide.createIcons();
    }
</script>
