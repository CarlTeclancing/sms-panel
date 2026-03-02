<?php
$filters = $filters ?? ['category' => '', 'platform' => '', 'year' => '', 'search' => ''];
?>

<div class="flex items-center justify-between">
    <h2 class="text-2xl font-semibold">Account Marketplace</h2>
    <a href="<?= url('/accounts/sell') ?>" class="bg-primary text-white px-4 py-2 rounded text-sm">Sell an account</a>
</div>

<script>
    const toggleAccountFilters = document.getElementById('toggleAccountFilters');
    const accountFilters = document.getElementById('accountFilters');
    if (toggleAccountFilters && accountFilters) {
        toggleAccountFilters.addEventListener('click', () => {
            const isHidden = accountFilters.classList.contains('hidden');
            accountFilters.classList.toggle('hidden', !isHidden ? true : false);
            toggleAccountFilters.textContent = isHidden ? 'Hide' : 'Show';
        });
    }
</script>

<div class="mt-4 bg-white border border-slate-200 rounded p-4">
    <div class="flex items-center justify-between md:hidden">
        <h4 class="text-sm font-semibold">Filters</h4>
        <button type="button" id="toggleAccountFilters" class="text-sm text-primary">Show</button>
    </div>
    <form method="get" action="<?= url('/accounts') ?>" id="accountFilters" class="mt-3 md:mt-0 hidden md:grid grid md:grid-cols-4 gap-3">
        <div>
            <label class="block text-sm font-medium">Category</label>
            <?php if (!empty($categories)): ?>
                <select name="category" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                    <option value="">All</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category) ?>" <?= $filters['category'] === $category ? 'selected' : '' ?>><?= htmlspecialchars($category) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input name="category" type="text" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" placeholder="Category" value="<?= htmlspecialchars($filters['category'] ?? '') ?>">
            <?php endif; ?>
        </div>
        <div>
            <label class="block text-sm font-medium">Platform</label>
            <?php if (!empty($platforms)): ?>
                <select name="platform" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                    <option value="">All</option>
                    <?php foreach ($platforms as $platform): ?>
                        <option value="<?= htmlspecialchars($platform) ?>" <?= $filters['platform'] === $platform ? 'selected' : '' ?>><?= htmlspecialchars($platform) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input name="platform" type="text" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" placeholder="Platform" value="<?= htmlspecialchars($filters['platform'] ?? '') ?>">
            <?php endif; ?>
        </div>
        <div>
            <label class="block text-sm font-medium">Year</label>
            <input name="year" type="number" min="2000" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" value="<?= htmlspecialchars((string)($filters['year'] ?? '')) ?>">
        </div>
        <div>
            <label class="block text-sm font-medium">Search</label>
            <input name="search" type="text" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" placeholder="Title or description" value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
        </div>
        <div class="md:col-span-4">
            <button class="bg-primary text-white px-4 py-2 rounded">Filter</button>
        </div>
    </form>
</div>

<div class="mt-6 grid md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php if (empty($listings)): ?>
        <div class="bg-white border border-slate-200 rounded p-6 text-slate-500 md:col-span-3">No approved listings found.</div>
    <?php else: ?>
        <?php foreach ($listings as $listing): ?>
            <div class="bg-white border border-slate-200 rounded-lg p-5 flex flex-col">
                <div class="text-xs text-slate-500"><?= htmlspecialchars($listing['category']) ?> • <?= htmlspecialchars($listing['platform']) ?></div>
                <h3 class="text-lg font-semibold mt-1"><?= htmlspecialchars($listing['title']) ?></h3>
                <p class="text-sm text-slate-500 mt-1">Year: <?= htmlspecialchars($listing['year'] ?: '-') ?></p>
                <?php if (!empty($listing['description'])): ?>
                    <p class="text-sm text-slate-600 mt-2"><?= htmlspecialchars($listing['description']) ?></p>
                <?php endif; ?>
                <div class="mt-auto pt-4">
                    <div class="text-lg font-semibold text-primary">$<?= number_format((float)$listing['price'], 2) ?></div>
                    <?php if ($user): ?>
                        <form method="post" action="<?= url('/accounts/buy') ?>" class="mt-3">
                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                            <input type="hidden" name="listing_id" value="<?= (int)$listing['id'] ?>">
                            <button class="w-full bg-primary text-white px-4 py-2 rounded">Buy</button>
                        </form>
                    <?php else: ?>
                        <div class="mt-3">
                            <a href="<?= url('/login') ?>" class="w-full block bg-primary text-white px-4 py-2 rounded text-center">Login to buy</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
