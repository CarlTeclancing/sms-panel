<?php
$sidebarItems = [
    ['label' => 'Dashboard', 'href' => '/admin', 'icon' => 'layout-dashboard'],
    ['label' => 'Services', 'href' => '/admin/services', 'icon' => 'smartphone'],
    ['label' => 'Users', 'href' => '/admin/users', 'icon' => 'users'],
    ['label' => 'Transactions', 'href' => '/admin/transactions', 'icon' => 'receipt'],
    ['label' => 'API Keys', 'href' => '/admin/api-keys', 'icon' => 'key'],
    ['label' => 'Notifications', 'href' => '/admin/notifications', 'icon' => 'bell'],
    ['label' => 'Settings', 'href' => '/admin/settings', 'icon' => 'settings'],
];
$currentPath = current_path();
?>

<div class="flex min-h-[75vh] bg-slate-50 border border-slate-200 rounded-xl overflow-hidden">
    <aside id="adminSidebar" class="w-72 bg-white border-r border-slate-200 flex flex-col">
        <div class="p-5 border-b border-slate-200 flex items-center space-x-3">
            <?php if (!empty($logo)): ?>
                <img src="<?= htmlspecialchars(url($logo)) ?>" alt="Logo" class="h-10">
            <?php endif; ?>
            <div>
                <p class="text-lg font-semibold text-slate-900">Admin Panel</p>
                <p class="text-xs text-slate-500">GetSMS Control</p>
            </div>
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

    <div class="flex-1">
        <div class="bg-white border-b border-slate-200 px-6 py-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center space-x-3">
                <button id="toggleSidebar" class="lg:hidden border border-slate-200 rounded px-3 py-1 text-sm">Menu</button>
                <div>
                    <h2 class="text-2xl font-semibold">Transaction Logs</h2>
                    <p class="text-sm text-slate-500">Audit refills, purchases, and adjustments</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="relative">
                    <input type="text" placeholder="Search transactions..." class="w-64 border border-slate-300 rounded-lg px-3 py-2 text-sm">
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

        <div class="p-6 space-y-4">
            <section class="bg-white border border-slate-200 rounded-lg p-5">
                <form method="get" action="<?= url('/admin/transactions') ?>" class="grid grid-cols-2 gap-2 text-sm">
                    <input name="user_id" type="number" class="border border-slate-300 rounded px-2 py-1" placeholder="User ID" value="<?= htmlspecialchars($_GET['user_id'] ?? '') ?>">
                    <select name="type" class="border border-slate-300 rounded px-2 py-1">
                        <option value="">All types</option>
                        <option value="refill" <?= ($_GET['type'] ?? '') === 'refill' ? 'selected' : '' ?>>Refill</option>
                        <option value="purchase" <?= ($_GET['type'] ?? '') === 'purchase' ? 'selected' : '' ?>>Purchase</option>
                        <option value="adjustment" <?= ($_GET['type'] ?? '') === 'adjustment' ? 'selected' : '' ?>>Adjustment</option>
                    </select>
                    <select name="status" class="border border-slate-300 rounded px-2 py-1">
                        <option value="">All statuses</option>
                        <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="success" <?= ($_GET['status'] ?? '') === 'success' ? 'selected' : '' ?>>Success</option>
                        <option value="failed" <?= ($_GET['status'] ?? '') === 'failed' ? 'selected' : '' ?>>Failed</option>
                    </select>
                    <input name="from" type="date" class="border border-slate-300 rounded px-2 py-1" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
                    <input name="to" type="date" class="border border-slate-300 rounded px-2 py-1" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
                    <button class="col-span-2 bg-primary text-white px-3 py-1 rounded">Apply filters</button>
                </form>
            </section>

            <section class="bg-white border border-slate-200 rounded-lg p-5">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left border-b border-slate-200">
                                <th class="p-3">User ID</th>
                                <th class="p-3">Type</th>
                                <th class="p-3">Amount</th>
                                <th class="p-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $tx): ?>
                                <tr class="border-b border-slate-100">
                                    <td class="p-3"><?= htmlspecialchars($tx['user_id']) ?></td>
                                    <td class="p-3"><?= htmlspecialchars($tx['type']) ?></td>
                                    <td class="p-3">$<?= number_format((float)$tx['amount'], 2) ?></td>
                                    <td class="p-3"><?= htmlspecialchars($tx['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    const toggleButton = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('adminSidebar');
    if (toggleButton && sidebar) {
        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
        });
    }

    if (window.lucide) {
        window.lucide.createIcons();
    }
</script>
