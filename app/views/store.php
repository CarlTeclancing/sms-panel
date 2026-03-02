<?php
$storeName = $storeOwner['store_name'] ?? $storeOwner['name'] ?? 'Store';
$tagline = $storeOwner['store_tagline'] ?? '';
$description = $storeOwner['store_description'] ?? '';
$logo = $storeOwner['profile_image'] ?? null;
$user = current_user();
?>

<div class="space-y-8">
    <div class="bg-white border border-slate-200 rounded-xl p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl bg-slate-100 overflow-hidden flex items-center justify-center">
                    <?php if (!empty($logo)): ?>
                        <img src="<?= htmlspecialchars(url($logo)) ?>" alt="Store logo" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-slate-400 text-xl">🏪</span>
                    <?php endif; ?>
                </div>
                <div>
                    <h2 class="text-2xl font-semibold"><?= htmlspecialchars($storeName) ?></h2>
                    <?php if ($tagline): ?>
                        <p class="text-sm text-slate-500 mt-1"><?= htmlspecialchars($tagline) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="text-sm text-slate-500">Seller: <?= htmlspecialchars($storeOwner['name'] ?? '') ?></div>
        </div>
        <?php if ($description): ?>
            <p class="mt-4 text-sm text-slate-600"><?= nl2br(htmlspecialchars($description)) ?></p>
        <?php endif; ?>
    </div>

    <div>
        <h3 class="text-lg font-semibold">Available accounts</h3>
        <div class="mt-4 grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php if (empty($listings)): ?>
                <div class="bg-white border border-slate-200 rounded p-6 text-slate-500 md:col-span-3">No approved listings yet.</div>
            <?php else: ?>
                <?php foreach ($listings as $listing): ?>
                    <div class="bg-white border border-slate-200 rounded-lg p-5 flex flex-col">
                        <div class="text-xs text-slate-500"><?= htmlspecialchars($listing['category']) ?> • <?= htmlspecialchars($listing['platform']) ?></div>
                        <h4 class="text-lg font-semibold mt-1"><?= htmlspecialchars($listing['title']) ?></h4>
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
                                <a href="<?= url('/login') ?>" class="mt-3 inline-flex items-center justify-center w-full border border-slate-300 rounded px-4 py-2 text-sm">Login to buy</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
