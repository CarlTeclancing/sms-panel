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
    <aside id="adminSidebar" class="hidden md:flex w-72 bg-white border-r border-slate-200 flex-col h-full overflow-hidden fixed md:static inset-y-0 left-0 z-40">
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
                    <h2 class="text-2xl font-semibold">Withdrawal Requests</h2>
                    <p class="text-sm text-slate-500">Approve or reject user withdrawals.</p>
                </div>
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

        <div class="p-6">
            <section class="bg-white border border-slate-200 rounded-lg p-5">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left border-b border-slate-200">
                                <th class="p-3">User ID</th>
                                <th class="p-3">Amount</th>
                                <th class="p-3">Fee</th>
                                <th class="p-3">Net</th>
                                <th class="p-3">Note</th>
                                <th class="p-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($requests)): ?>
                                <tr><td class="p-3 text-slate-500" colspan="6">No pending withdrawals.</td></tr>
                            <?php else: ?>
                                <?php foreach ($requests as $request): ?>
                                    <tr class="border-b border-slate-100">
                                        <td class="p-3">#<?= (int)$request['user_id'] ?></td>
                                        <td class="p-3">$<?= number_format((float)$request['amount'], 2) ?></td>
                                        <td class="p-3">$<?= number_format((float)$request['fee'], 2) ?></td>
                                        <td class="p-3">$<?= number_format((float)$request['net_amount'], 2) ?></td>
                                        <td class="p-3 text-xs text-slate-500"><?= htmlspecialchars($request['note'] ?? '-') ?></td>
                                        <td class="p-3">
                                            <div class="flex gap-2">
                                                <form method="post" action="<?= url('/admin/withdrawals/approve') ?>">
                                                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                                    <input type="hidden" name="request_id" value="<?= (int)$request['id'] ?>">
                                                    <button class="bg-primary text-white px-3 py-1 rounded text-xs">Approve</button>
                                                </form>
                                                <form method="post" action="<?= url('/admin/withdrawals/reject') ?>" class="flex items-center gap-2">
                                                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                                    <input type="hidden" name="request_id" value="<?= (int)$request['id'] ?>">
                                                    <input name="note" type="text" class="border border-slate-300 rounded px-2 py-1 text-xs" placeholder="Reason">
                                                    <button class="bg-danger text-white px-3 py-1 rounded text-xs">Reject</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
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
