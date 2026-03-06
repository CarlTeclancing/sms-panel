<?php
$filters = $filters ?? ['category' => '', 'platform' => '', 'year' => '', 'search' => ''];
$activeFilterCount = 0;
foreach (['category', 'platform', 'year', 'search'] as $filterKey) {
    if (!empty($filters[$filterKey])) {
        $activeFilterCount++;
    }
}
?>

<div class="flex items-center justify-between">
    <h2 class="text-2xl font-semibold">Account Marketplace</h2>
    <a href="<?= url('/accounts/sell') ?>" class="bg-primary text-white px-4 py-2 rounded text-sm">Sell an account</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleAccountFilters = document.getElementById('toggleAccountFilters');
        const accountFilters = document.getElementById('accountFilters');
        if (!toggleAccountFilters || !accountFilters) {
            return;
        }

        const setState = (show) => {
            accountFilters.classList.toggle('hidden', !show);
            accountFilters.classList.toggle('grid', show);
            toggleAccountFilters.textContent = show ? 'Hide' : 'Show';
        };

        const media = window.matchMedia('(min-width: 768px)');
        setState(media.matches);

        toggleAccountFilters.addEventListener('click', () => {
            const isHidden = accountFilters.classList.contains('hidden');
            setState(isHidden);
        });

        if (typeof media.addEventListener === 'function') {
            media.addEventListener('change', (event) => setState(event.matches));
        } else if (typeof media.addListener === 'function') {
            media.addListener((event) => setState(event.matches));
        }
    });
</script>

<div class="mt-4 border border-slate-200 rounded-2xl bg-white/80 backdrop-blur shadow-sm p-4 md:p-5">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="text-sm font-semibold">Filter listings</h4>
                <p class="text-xs text-slate-500">Refine by category, platform, year, or keyword.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <?php if ($activeFilterCount > 0): ?>
                <span class="text-xs text-slate-600 bg-slate-100 border border-slate-200 px-2 py-1 rounded-full"><?= $activeFilterCount ?> active</span>
            <?php endif; ?>
            <button type="button" id="toggleAccountFilters" class="text-sm text-primary">Show</button>
        </div>
    </div>

    <form method="get" action="<?= url('/accounts') ?>" id="accountFilters" class="mt-4 gap-4">
        <div class="grid lg:grid-cols-[2fr_repeat(3,1fr)] gap-4">
            <div class="relative">
                <label class="block text-xs font-semibold text-slate-500">Search</label>
                <div class="mt-2 flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    <input name="search" type="text" class="w-full text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none" placeholder="Title, description, or keyword" value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500">Category</label>
                <?php if (!empty($categories)): ?>
                    <select name="category" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm">
                        <option value="">All categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category) ?>" <?= $filters['category'] === $category ? 'selected' : '' ?>><?= htmlspecialchars($category) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input name="category" type="text" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm" placeholder="Any category" value="<?= htmlspecialchars($filters['category'] ?? '') ?>">
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500">Platform</label>
                <?php if (!empty($platforms)): ?>
                    <select name="platform" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm">
                        <option value="">All platforms</option>
                        <?php foreach ($platforms as $platform): ?>
                            <option value="<?= htmlspecialchars($platform) ?>" <?= $filters['platform'] === $platform ? 'selected' : '' ?>><?= htmlspecialchars($platform) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input name="platform" type="text" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm" placeholder="Any platform" value="<?= htmlspecialchars($filters['platform'] ?? '') ?>">
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500">Year</label>
                <input name="year" type="number" min="2000" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm" placeholder="Any" value="<?= htmlspecialchars((string)($filters['year'] ?? '')) ?>">
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button class="bg-primary text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm">Apply filters</button>
            <a href="<?= url('/accounts') ?>" class="px-5 py-2.5 rounded-xl text-sm font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50">Reset</a>
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
                    <div class="text-lg font-semibold text-primary">XAF <?= number_format((float)$listing['price'], 2) ?></div>
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
