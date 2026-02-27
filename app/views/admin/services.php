<?php
$sidebarItems = [
    ['label' => 'Dashboard', 'href' => '/admin', 'icon' => 'layout-dashboard'],
    ['label' => 'Services', 'href' => '/admin/services', 'icon' => 'smartphone'],
    ['label' => 'Boosting Services', 'href' => '/admin/boosting-services', 'icon' => 'rocket'],
    ['label' => 'Users', 'href' => '/admin/users', 'icon' => 'users'],
    ['label' => 'Transactions', 'href' => '/admin/transactions', 'icon' => 'receipt'],
    ['label' => 'API Keys', 'href' => '/admin/api-keys', 'icon' => 'key'],
    ['label' => 'Tickets', 'href' => '/admin/tickets', 'icon' => 'life-buoy'],
    ['label' => 'Notifications', 'href' => '/admin/notifications', 'icon' => 'bell'],
    ['label' => 'Settings', 'href' => '/admin/settings', 'icon' => 'settings'],
];
$currentPath = current_path();
?>

<div class="flex h-[75vh] bg-slate-50 border border-slate-200 rounded-xl overflow-hidden">
    <aside id="adminSidebar" class="hidden md:flex w-72 bg-white border-r border-slate-200 flex-col min-h-0 overflow-y-auto fixed md:static inset-y-0 left-0 z-40">
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
        <nav class="flex-1 p-4 space-y-1">
            <?php foreach ($sidebarItems as $item): ?>
                <?php $isActive = $currentPath === $item['href']; ?>
                <a href="<?= url($item['href']) ?>" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-sm font-medium <?= $isActive ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                    <i data-lucide="<?= htmlspecialchars($item['icon']) ?>" class="w-4 h-4"></i>
                    <span><?= htmlspecialchars($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div class="flex-1 min-h-0 overflow-y-auto">
        <div class="bg-white border-b border-slate-200 px-6 py-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 sticky top-0 z-10">
            <div class="flex items-center space-x-3">
                <button id="toggleSidebar" class="lg:hidden border border-slate-200 rounded px-3 py-1 text-sm">Menu</button>
                <div>
                    <h2 class="text-2xl font-semibold">Service Pricing</h2>
                    <p class="text-sm text-slate-500">Manage service pricing</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="relative">
                    <input type="text" placeholder="Search services..." class="w-64 border border-slate-300 rounded-lg px-3 py-2 text-sm">
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
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">Service Pricing</h3>
                        <p class="text-sm text-slate-500">Pull services from SMS-Man and set prices.</p>
                    </div>
                    <form method="post" action="<?= url('/admin/services/sync') ?>">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <button class="border border-slate-300 px-3 py-2 rounded text-sm">Sync from SMS-Man</button>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left border-b border-slate-200">
                                <th class="p-3">Service</th>
                                <th class="p-3">Code</th>
                                <th class="p-3">Price (USD)</th>
                                <th class="p-3">Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($services)): ?>
                                <?php foreach ($services as $service): ?>
                                    <tr class="border-b border-slate-100">
                                        <td class="p-3"><?= htmlspecialchars($service['name']) ?></td>
                                        <td class="p-3"><?= htmlspecialchars($service['code']) ?></td>
                                        <td class="p-3">$<?= number_format((float)$service['price'], 4) ?></td>
                                        <td class="p-3">
                                            <form method="post" action="<?= url('/admin/price') ?>" class="flex items-center space-x-2">
                                                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                                <input type="hidden" name="service_id" value="<?= htmlspecialchars($service['id']) ?>">
                                                <input name="price" type="number" step="0.01" class="border border-slate-300 rounded px-2 py-1" required>
                                                <button class="bg-primary text-white px-3 py-1 rounded">Save</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="p-6 text-center text-slate-500">No services found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (($totalPages ?? 1) > 1): ?>
                    <?php
                        $currentPage = $currentPage ?? 1;
                        $perPage = $perPage ?? 20;
                        $totalServices = $totalServices ?? 0;
                        $start = $totalServices > 0 ? (($currentPage - 1) * $perPage + 1) : 0;
                        $end = $totalServices > 0 ? min($totalServices, $currentPage * $perPage) : 0;
                    ?>
                    <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <p class="text-sm text-slate-500">Showing <?= $start ?>-<?= $end ?> of <?= (int)$totalServices ?></p>
                        <div class="flex items-center space-x-2">
                            <a href="<?= url('/admin/services?page=' . max(1, $currentPage - 1)) ?>" class="px-3 py-1 rounded border border-slate-300 text-sm <?= $currentPage <= 1 ? 'pointer-events-none opacity-50' : '' ?>">Previous</a>
                            <span class="text-sm text-slate-600">Page <?= (int)$currentPage ?> of <?= (int)($totalPages ?? 1) ?></span>
                            <a href="<?= url('/admin/services?page=' . min(($totalPages ?? 1), $currentPage + 1)) ?>" class="px-3 py-1 rounded border border-slate-300 text-sm <?= $currentPage >= ($totalPages ?? 1) ? 'pointer-events-none opacity-50' : '' ?>">Next</a>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</div>

<?php
    $adminNavItems = [
        ['href' => '/admin', 'label' => 'Home', 'icon' => 'layout-dashboard'],
        ['href' => '/admin/services', 'label' => 'SMS', 'icon' => 'smartphone'],
        ['href' => '/admin/boosting-services', 'label' => 'Boost', 'icon' => 'rocket'],
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
</script>
