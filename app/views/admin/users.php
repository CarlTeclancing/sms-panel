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
                    <h2 class="text-2xl font-semibold">User Management</h2>
                    <p class="text-sm text-slate-500">Manage accounts, roles, and balances</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="relative">
                    <input type="text" placeholder="Search users..." class="w-64 border border-slate-300 rounded-lg px-3 py-2 text-sm">
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
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left border-b border-slate-200">
                                <th class="p-3">User</th>
                                <th class="p-3">Role</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Balance</th>
                                <th class="p-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr class="border-b border-slate-100 align-top">
                                    <td class="p-3">
                                        <div class="font-semibold"><?= htmlspecialchars($user['name']) ?></div>
                                        <div class="text-xs text-slate-500"><?= htmlspecialchars($user['email']) ?></div>
                                    </td>
                                    <td class="p-3"><?= htmlspecialchars($user['role']) ?></td>
                                    <td class="p-3"><?= (int)$user['active'] === 1 ? 'Active' : 'Disabled' ?></td>
                                    <td class="p-3">$<?= number_format((float)$user['balance'], 2) ?></td>
                                    <td class="p-3 space-y-2">
                                        <button
                                            class="px-3 py-1 rounded border border-slate-300"
                                            data-edit-user
                                            data-user-id="<?= htmlspecialchars($user['id']) ?>"
                                            data-user-name="<?= htmlspecialchars($user['name']) ?>"
                                            data-user-email="<?= htmlspecialchars($user['email']) ?>"
                                            data-user-role="<?= htmlspecialchars($user['role']) ?>"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            class="px-3 py-1 rounded border border-slate-300 <?= (int)$user['active'] === 1 ? 'text-danger' : 'text-green-700' ?>"
                                            data-disable-user
                                            data-user-id="<?= htmlspecialchars($user['id']) ?>"
                                            data-user-active="<?= (int)$user['active'] ?>"
                                            data-user-name="<?= htmlspecialchars($user['name']) ?>"
                                        >
                                            <?= (int)$user['active'] === 1 ? 'Disable' : 'Enable' ?>
                                        </button>
                                        <button
                                            class="px-3 py-1 rounded border border-slate-300"
                                            data-adjust-balance
                                            data-user-id="<?= htmlspecialchars($user['id']) ?>"
                                            data-user-name="<?= htmlspecialchars($user['name']) ?>"
                                        >
                                            Adjust balance
                                        </button>
                                        <form method="post" action="<?= url('/admin/api/rotate') ?>" class="mt-2">
                                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']) ?>">
                                            <button class="px-3 py-1 rounded border border-slate-300">Rotate API Token</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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

<div id="editUserModal" class="fixed inset-0 hidden items-center justify-center bg-slate-900/40 z-50">
    <div class="bg-white rounded-lg w-full max-w-lg p-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">Edit User</h3>
            <button id="closeEditModal" class="text-slate-500">Close</button>
        </div>
        <form method="post" action="<?= url('/admin/user/update') ?>" class="mt-4 space-y-3">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="user_id" id="editUserId">
            <div>
                <label class="block text-sm font-medium">Name</label>
                <input name="name" id="editUserName" type="text" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input name="email" id="editUserEmail" type="email" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Role</label>
                <select name="role" id="editUserRole" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelEditModal" class="border border-slate-300 px-4 py-2 rounded">Cancel</button>
                <button class="bg-primary text-white px-4 py-2 rounded">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="confirmDisableModal" class="fixed inset-0 hidden items-center justify-center bg-slate-900/40 z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-lg font-semibold">Confirm action</h3>
        <p id="confirmDisableText" class="mt-2 text-sm text-slate-600"></p>
        <form method="post" action="<?= url('/admin/user/toggle') ?>" class="mt-4 flex justify-end space-x-2">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="user_id" id="confirmUserId">
            <input type="hidden" name="active" id="confirmUserActive">
            <button type="button" id="cancelDisableModal" class="border border-slate-300 px-4 py-2 rounded">Cancel</button>
            <button class="bg-primary text-white px-4 py-2 rounded">Confirm</button>
        </form>
    </div>
</div>

<div id="adjustBalanceModal" class="fixed inset-0 hidden items-center justify-center bg-slate-900/40 z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">Adjust balance</h3>
            <button id="closeAdjustModal" class="text-slate-500">Close</button>
        </div>
        <p id="adjustBalanceText" class="mt-2 text-sm text-slate-600"></p>
        <form method="post" action="<?= url('/admin/balance/adjust') ?>" class="mt-4 space-y-3">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="user_id" id="adjustUserId">
            <div>
                <label class="block text-sm font-medium">Amount (+/-)</label>
                <input name="amount" type="number" step="0.01" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Note</label>
                <input name="note" type="text" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" placeholder="Reason or reference">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" id="cancelAdjustModal" class="border border-slate-300 px-4 py-2 rounded">Cancel</button>
                <button class="bg-primary text-white px-4 py-2 rounded">Confirm</button>
            </div>
        </form>
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

    const editModal = document.getElementById('editUserModal');
    const closeEditModal = document.getElementById('closeEditModal');
    const cancelEditModal = document.getElementById('cancelEditModal');
    const editUserId = document.getElementById('editUserId');
    const editUserName = document.getElementById('editUserName');
    const editUserEmail = document.getElementById('editUserEmail');
    const editUserRole = document.getElementById('editUserRole');

    document.querySelectorAll('[data-edit-user]').forEach(button => {
        button.addEventListener('click', () => {
            editUserId.value = button.dataset.userId;
            editUserName.value = button.dataset.userName;
            editUserEmail.value = button.dataset.userEmail;
            editUserRole.value = button.dataset.userRole;
            editModal.classList.remove('hidden');
            editModal.classList.add('flex');
        });
    });

    [closeEditModal, cancelEditModal].forEach(el => {
        if (el) {
            el.addEventListener('click', () => {
                editModal.classList.add('hidden');
                editModal.classList.remove('flex');
            });
        }
    });

    const confirmModal = document.getElementById('confirmDisableModal');
    const confirmUserId = document.getElementById('confirmUserId');
    const confirmUserActive = document.getElementById('confirmUserActive');
    const confirmText = document.getElementById('confirmDisableText');
    const cancelDisableModal = document.getElementById('cancelDisableModal');

    document.querySelectorAll('[data-disable-user]').forEach(button => {
        button.addEventListener('click', () => {
            const isActive = button.dataset.userActive === '1';
            confirmUserId.value = button.dataset.userId;
            confirmUserActive.value = isActive ? 0 : 1;
            confirmText.textContent = isActive
                ? `Disable ${button.dataset.userName}? This will block login and API access.`
                : `Enable ${button.dataset.userName}?`;
            confirmModal.classList.remove('hidden');
            confirmModal.classList.add('flex');
        });
    });

    if (cancelDisableModal) {
        cancelDisableModal.addEventListener('click', () => {
            confirmModal.classList.add('hidden');
            confirmModal.classList.remove('flex');
        });
    }

    const adjustModal = document.getElementById('adjustBalanceModal');
    const adjustUserId = document.getElementById('adjustUserId');
    const adjustText = document.getElementById('adjustBalanceText');
    const closeAdjustModal = document.getElementById('closeAdjustModal');
    const cancelAdjustModal = document.getElementById('cancelAdjustModal');

    document.querySelectorAll('[data-adjust-balance]').forEach(button => {
        button.addEventListener('click', () => {
            adjustUserId.value = button.dataset.userId;
            adjustText.textContent = `Adjust balance for ${button.dataset.userName}.`;
            adjustModal.classList.remove('hidden');
            adjustModal.classList.add('flex');
        });
    });

    [closeAdjustModal, cancelAdjustModal].forEach(el => {
        if (el) {
            el.addEventListener('click', () => {
                adjustModal.classList.add('hidden');
                adjustModal.classList.remove('flex');
            });
        }
    });
</script>
