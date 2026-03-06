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

<div class="flex h-screen w-full bg-slate-50 overflow-hidden">
    <aside id="adminSidebar" class="hidden md:flex w-72 bg-white border-r border-slate-200 flex-col h-screen overflow-y-auto fixed md:static inset-y-0 left-0 z-40">
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
                    <h2 class="text-2xl font-semibold">Account Listings</h2>
                    <p class="text-sm text-slate-500">Verify and approve seller listings.</p>
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
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">Account Listings</h3>
                        <p class="text-sm text-slate-500">Review pending listings and manage active ones.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="listing-tab px-3 py-1 rounded-full text-sm font-medium bg-primary/10 text-primary" data-tab="pending">Pending</button>
                        <button class="listing-tab px-3 py-1 rounded-full text-sm font-medium text-slate-600 hover:bg-slate-50" data-tab="active">Active</button>
                        <button class="listing-tab px-3 py-1 rounded-full text-sm font-medium text-slate-600 hover:bg-slate-50" data-tab="sold">Sold</button>
                    </div>
                </div>

                <div class="mt-4" id="pendingListingsPanel">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left border-b border-slate-200">
                                    <th class="p-3">Listing</th>
                                    <th class="p-3">Category</th>
                                    <th class="p-3">Platform</th>
                                    <th class="p-3">Price</th>
                                    <th class="p-3">Seller</th>
                                    <th class="p-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pendingListings)): ?>
                                    <tr><td class="p-3 text-slate-500" colspan="6">No pending listings.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($pendingListings as $listing): ?>
                                        <tr class="border-b border-slate-100">
                                            <td class="p-3">
                                                <div class="font-medium"><?= htmlspecialchars($listing['title']) ?></div>
                                                <div class="text-xs text-slate-500">Seller ID: <?= (int)$listing['seller_id'] ?></div>
                                            </td>
                                            <td class="p-3"><?= htmlspecialchars($listing['category']) ?></td>
                                            <td class="p-3"><?= htmlspecialchars($listing['platform']) ?></td>
                                            <td class="p-3">XAF <?= number_format((float)$listing['price'], 2) ?></td>
                                            <td class="p-3 text-xs text-slate-500">
                                                <?= htmlspecialchars($listing['seller_name'] ?? 'Seller') ?><br>
                                                <?= htmlspecialchars($listing['seller_email'] ?? '') ?>
                                            </td>
                                            <td class="p-3">
                                                <div class="flex gap-2">
                                                    <form method="post" action="<?= url('/admin/account-listings/approve') ?>">
                                                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                                        <input type="hidden" name="listing_id" value="<?= (int)$listing['id'] ?>">
                                                        <button class="bg-primary text-white px-3 py-1 rounded text-xs">Approve</button>
                                                    </form>
                                                    <form method="post" action="<?= url('/admin/account-listings/reject') ?>">
                                                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                                        <input type="hidden" name="listing_id" value="<?= (int)$listing['id'] ?>">
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
                </div>

                <div class="mt-4 hidden" id="activeListingsPanel">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left border-b border-slate-200">
                                    <th class="p-3">Listing</th>
                                    <th class="p-3">Category</th>
                                    <th class="p-3">Platform</th>
                                    <th class="p-3">Price</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($activeListings)): ?>
                                    <tr><td class="p-3 text-slate-500" colspan="6">No active listings.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($activeListings as $listing): ?>
                                        <tr class="border-b border-slate-100">
                                            <td class="p-3">
                                                <div class="font-medium"><?= htmlspecialchars($listing['title']) ?></div>
                                                <div class="text-xs text-slate-500">Seller ID: <?= (int)$listing['seller_id'] ?></div>
                                            </td>
                                            <td class="p-3"><?= htmlspecialchars($listing['category']) ?></td>
                                            <td class="p-3"><?= htmlspecialchars($listing['platform']) ?></td>
                                            <td class="p-3">XAF <?= number_format((float)$listing['price'], 2) ?></td>
                                            <td class="p-3 capitalize"><?= htmlspecialchars($listing['status']) ?></td>
                                            <td class="p-3">
                                                <button
                                                    type="button"
                                                    class="border border-slate-300 px-3 py-1 rounded text-xs"
                                                    data-view-listing
                                                    data-title="<?= htmlspecialchars($listing['title']) ?>"
                                                    data-category="<?= htmlspecialchars($listing['category']) ?>"
                                                    data-platform="<?= htmlspecialchars($listing['platform']) ?>"
                                                    data-year="<?= htmlspecialchars((string)($listing['year'] ?? '')) ?>"
                                                    data-price="XAF <?= number_format((float)$listing['price'], 2) ?>"
                                                    data-status="<?= htmlspecialchars($listing['status']) ?>"
                                                    data-details="<?= htmlspecialchars($listing['account_details']) ?>"
                                                    data-seller-name="<?= htmlspecialchars($listing['seller_name'] ?? '') ?>"
                                                    data-seller-email="<?= htmlspecialchars($listing['seller_email'] ?? '') ?>"
                                                    data-buyer-name="<?= htmlspecialchars($listing['buyer_name'] ?? '') ?>"
                                                    data-buyer-email="<?= htmlspecialchars($listing['buyer_email'] ?? '') ?>"
                                                    data-bought="<?= ($listing['status'] ?? '') === 'sold' ? 'Yes' : 'No' ?>"
                                                >
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 hidden" id="soldListingsPanel">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left border-b border-slate-200">
                                    <th class="p-3">Listing</th>
                                    <th class="p-3">Category</th>
                                    <th class="p-3">Platform</th>
                                    <th class="p-3">Price</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($soldListings)): ?>
                                    <tr><td class="p-3 text-slate-500" colspan="6">No sold listings.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($soldListings as $listing): ?>
                                        <tr class="border-b border-slate-100">
                                            <td class="p-3">
                                                <div class="font-medium"><?= htmlspecialchars($listing['title']) ?></div>
                                                <div class="text-xs text-slate-500">Seller ID: <?= (int)$listing['seller_id'] ?></div>
                                            </td>
                                            <td class="p-3"><?= htmlspecialchars($listing['category']) ?></td>
                                            <td class="p-3"><?= htmlspecialchars($listing['platform']) ?></td>
                                            <td class="p-3">XAF <?= number_format((float)$listing['price'], 2) ?></td>
                                            <td class="p-3 capitalize"><?= htmlspecialchars($listing['status']) ?></td>
                                            <td class="p-3">
                                                <button
                                                    type="button"
                                                    class="border border-slate-300 px-3 py-1 rounded text-xs"
                                                    data-view-listing
                                                    data-title="<?= htmlspecialchars($listing['title']) ?>"
                                                    data-category="<?= htmlspecialchars($listing['category']) ?>"
                                                    data-platform="<?= htmlspecialchars($listing['platform']) ?>"
                                                    data-year="<?= htmlspecialchars((string)($listing['year'] ?? '')) ?>"
                                                    data-price="XAF <?= number_format((float)$listing['price'], 2) ?>"
                                                    data-status="<?= htmlspecialchars($listing['status']) ?>"
                                                    data-details="<?= htmlspecialchars($listing['account_details']) ?>"
                                                    data-seller-name="<?= htmlspecialchars($listing['seller_name'] ?? '') ?>"
                                                    data-seller-email="<?= htmlspecialchars($listing['seller_email'] ?? '') ?>"
                                                    data-buyer-name="<?= htmlspecialchars($listing['buyer_name'] ?? '') ?>"
                                                    data-buyer-email="<?= htmlspecialchars($listing['buyer_email'] ?? '') ?>"
                                                    data-bought="Yes"
                                                >
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<div id="listingDetailsModal" class="fixed inset-0 hidden items-center justify-center bg-slate-900/40 z-50">
    <div class="bg-white rounded-lg w-full max-w-2xl p-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">Listing details</h3>
            <button id="closeListingModal" class="text-slate-500">Close</button>
        </div>
        <div class="mt-4 grid md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs text-slate-500">Title</p>
                <p id="listingTitle" class="font-medium"></p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Status</p>
                <p id="listingStatus" class="font-medium capitalize"></p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Category</p>
                <p id="listingCategory"></p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Platform</p>
                <p id="listingPlatform"></p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Year</p>
                <p id="listingYear"></p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Price</p>
                <p id="listingPrice" class="font-medium"></p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Uploaded by</p>
                <p id="listingSeller"></p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Bought</p>
                <p id="listingBought"></p>
            </div>
            <div class="md:col-span-2">
                <p class="text-xs text-slate-500">Buyer</p>
                <p id="listingBuyer"></p>
            </div>
        </div>
        <div class="mt-4">
            <p class="text-xs text-slate-500">Account details</p>
            <pre id="listingDetails" class="mt-2 whitespace-pre-wrap text-xs bg-slate-50 border border-slate-200 rounded p-3"></pre>
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

    const listingTabs = document.querySelectorAll('.listing-tab');
    const pendingPanel = document.getElementById('pendingListingsPanel');
    const activePanel = document.getElementById('activeListingsPanel');
    const soldPanel = document.getElementById('soldListingsPanel');
    const listingModal = document.getElementById('listingDetailsModal');
    const closeListingModal = document.getElementById('closeListingModal');

    const setListingTab = (tab) => {
        pendingPanel.classList.toggle('hidden', tab !== 'pending');
        activePanel.classList.toggle('hidden', tab !== 'active');
        soldPanel.classList.toggle('hidden', tab !== 'sold');
        listingTabs.forEach(btn => {
            const active = btn.dataset.tab === tab;
            btn.classList.toggle('bg-primary/10', active);
            btn.classList.toggle('text-primary', active);
            btn.classList.toggle('text-slate-600', !active);
            btn.classList.toggle('hover:bg-slate-50', !active);
        });
    };

    listingTabs.forEach(btn => {
        btn.addEventListener('click', () => setListingTab(btn.dataset.tab));
    });

    document.querySelectorAll('[data-view-listing]').forEach(btn => {
        btn.addEventListener('click', () => {
            if (!listingModal) return;
            document.getElementById('listingTitle').textContent = btn.dataset.title || '';
            document.getElementById('listingStatus').textContent = btn.dataset.status || '';
            document.getElementById('listingCategory').textContent = btn.dataset.category || '';
            document.getElementById('listingPlatform').textContent = btn.dataset.platform || '';
            document.getElementById('listingYear').textContent = btn.dataset.year || '-';
            document.getElementById('listingPrice').textContent = btn.dataset.price || '';
            document.getElementById('listingSeller').textContent = `${btn.dataset.sellerName || ''} (${btn.dataset.sellerEmail || ''})`;
            document.getElementById('listingBought').textContent = btn.dataset.bought || 'No';
            const buyerName = btn.dataset.buyerName || '';
            const buyerEmail = btn.dataset.buyerEmail || '';
            document.getElementById('listingBuyer').textContent = buyerName || buyerEmail ? `${buyerName} ${buyerEmail ? '(' + buyerEmail + ')' : ''}`.trim() : 'N/A';
            document.getElementById('listingDetails').textContent = btn.dataset.details || '';
            listingModal.classList.remove('hidden');
            listingModal.classList.add('flex');
        });
    });

    const hideListingModal = () => {
        listingModal.classList.add('hidden');
        listingModal.classList.remove('flex');
    };

    if (closeListingModal) {
        closeListingModal.addEventListener('click', hideListingModal);
    }
    if (listingModal) {
        listingModal.addEventListener('click', (event) => {
            if (event.target === listingModal) {
                hideListingModal();
            }
        });
    }

    setListingTab('pending');
</script>
