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

<div class="flex h-screen w-full bg-slate-50/60 backdrop-blur-xl overflow-hidden">
    <aside id="adminSidebar" class="hidden md:flex w-72 bg-white/60 border-r border-slate-200 flex-col h-screen overflow-y-auto fixed md:static inset-y-0 left-0 z-40 backdrop-blur-xl">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
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
        <div class="bg-white/60 border-b border-white/30 px-6 py-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 sticky top-0 z-10 backdrop-blur-xl">
            <div class="flex items-center space-x-3">
                <button id="toggleSidebar" class="lg:hidden border border-slate-200 rounded px-3 py-1 text-sm">Menu</button>
                <div>
                    <h2 class="text-2xl font-semibold">Admin Console</h2>
                    <p class="text-sm text-slate-500">Overview</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="relative">
                    <input type="text" placeholder="Search users, transactions..." class="w-64 border border-slate-300 rounded-lg px-3 py-2 text-sm">
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

        <div class="p-6 space-y-6">
            <section class="grid md:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-200 rounded-lg p-4">
                    <p class="text-sm text-slate-500">Total users</p>
                    <p class="text-2xl font-semibold mt-1"><?= number_format((int)($totalUsers ?? 0)) ?></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-lg p-4">
                    <p class="text-sm text-slate-500">Total revenue</p>
                    <p class="text-2xl font-semibold mt-1">XAF <?= number_format((float)($totalRevenue ?? 0), 4) ?></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-lg p-4">
                    <p class="text-sm text-slate-500">Total services</p>
                    <p class="text-2xl font-semibold mt-1"><?= number_format((int)($totalServices ?? 0)) ?></p>
                </div>
            </section>

            <section class="grid md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-white border border-slate-200 rounded-lg p-4">
                    <p class="text-sm text-slate-500">Total user balance</p>
                    <p class="text-2xl font-semibold mt-1">XAF <?= number_format((float)($walletTotals['total_balance'] ?? 0), 2) ?></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-lg p-4">
                    <p class="text-sm text-slate-500">Earnings from listings</p>
                    <p class="text-2xl font-semibold mt-1">XAF <?= number_format((float)($listingEarnings ?? 0), 2) ?></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-lg p-4">
                    <p class="text-sm text-slate-500">Pending payouts</p>
                    <p class="text-2xl font-semibold mt-1">XAF <?= number_format((float)($pendingPayouts ?? 0), 2) ?></p>
                </div>
                <div class="bg-white border border-slate-200 rounded-lg p-4">
                    <p class="text-sm text-slate-500">Gross balance</p>
                    <p class="text-2xl font-semibold mt-1">XAF <?= number_format((float)($grossBalance ?? 0), 2) ?></p>
                </div>
            </section>

            <section class="bg-white border border-slate-200 rounded-lg p-5">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Platform performance</h3>
                    <span class="text-sm text-slate-500">Last 14 days</span>
                </div>
                <div class="mt-4">
                    <canvas id="performanceChart" height="120"></canvas>
                </div>
            </section>

            <section class="grid lg:grid-cols-2 gap-6">
                <div class="bg-white border border-slate-200 rounded-lg p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">User growth</h3>
                        <span class="text-sm text-slate-500">Last 14 days</span>
                    </div>
                    <div class="mt-4">
                        <canvas id="signupChart" height="140"></canvas>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 rounded-lg p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Wallet flows</h3>
                        <span class="text-sm text-slate-500">Last 14 days</span>
                    </div>
                    <div class="mt-4">
                        <canvas id="walletChart" height="140"></canvas>
                    </div>
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

    if (window.lucide) {
        window.lucide.createIcons();
    }

    const dailyRevenue = <?php echo json_encode($dailyRevenue ?? []); ?>;
    const dailySignups = <?php echo json_encode($dailySignups ?? []); ?>;
    const dailyTopups = <?php echo json_encode($dailyTopups ?? []); ?>;
    const dailyPurchases = <?php echo json_encode($dailyPurchases ?? []); ?>;
    const dailyWithdrawals = <?php echo json_encode($dailyWithdrawals ?? []); ?>;

    const labels = dailyRevenue.map(item => item.day);
    const revenueValues = dailyRevenue.map(item => Number(item.total || 0));

    const mapToLabels = (items, sourceLabels) => {
        const lookup = {};
        items.forEach(item => {
            if (!item || !item.day) return;
            lookup[item.day] = Number(item.total || 0);
        });
        return sourceLabels.map(label => lookup[label] || 0);
    };

    const signupLabels = dailySignups.map(item => item.day);
    const signupValues = dailySignups.map(item => Number(item.total || 0));

    const topupValues = mapToLabels(dailyTopups, labels);
    const purchaseValues = mapToLabels(dailyPurchases, labels);
    const withdrawalValues = mapToLabels(dailyWithdrawals, labels);

    const chartScript = document.createElement('script');
    chartScript.src = 'https://cdn.jsdelivr.net/npm/chart.js';
    chartScript.onload = () => {
        const ctx = document.getElementById('performanceChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Revenue',
                    data: revenueValues,
                    borderColor: '#1D4ED8',
                    backgroundColor: 'rgba(29, 78, 216, 0.12)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 2,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        const signupCtx = document.getElementById('signupChart');
        if (signupCtx) {
            new Chart(signupCtx, {
                type: 'bar',
                data: {
                    labels: signupLabels,
                    datasets: [{
                        label: 'New users',
                        data: signupValues,
                        backgroundColor: 'rgba(16, 185, 129, 0.25)',
                        borderColor: '#10B981',
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        const walletCtx = document.getElementById('walletChart');
        if (walletCtx) {
            new Chart(walletCtx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Topups',
                            data: topupValues,
                            borderColor: '#2563EB',
                            backgroundColor: 'rgba(37, 99, 235, 0.12)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 2,
                        },
                        {
                            label: 'Purchases',
                            data: purchaseValues,
                            borderColor: '#F97316',
                            backgroundColor: 'rgba(249, 115, 22, 0.12)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 2,
                        },
                        {
                            label: 'Withdrawals',
                            data: withdrawalValues,
                            borderColor: '#EF4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.12)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 2,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
    };
    document.head.appendChild(chartScript);
</script>
