<div class="flex items-center justify-between">
    <h2 class="text-2xl font-semibold">Sell Accounts</h2>
    <a href="<?= url('/accounts') ?>" class="text-primary">Browse marketplace</a>
</div>

<div class="mt-4 bg-white border border-slate-200 rounded p-4">
    <h3 class="text-lg font-semibold">Seller onboarding</h3>
    <?php if ($sellerFee > 0 && !$hasPaidFee): ?>
        <p class="text-sm text-slate-600 mt-2">A one-time seller fee of <strong>XAF <?= number_format((float)$sellerFee, 2) ?></strong> is required before listing. The fee will be charged from your wallet when you submit your first listing.</p>
    <?php else: ?>
        <p class="text-sm text-slate-600 mt-2">You are verified to sell. Your listings will be reviewed by admin.</p>
    <?php endif; ?>
    <div class="mt-4">
        <?php if (!empty($storeUrl)): ?>
            <p class="text-xs text-slate-500">Your public store link</p>
            <input readonly class="mt-2 w-full border border-slate-300 rounded px-3 py-2 text-sm" value="<?= htmlspecialchars($storeUrl) ?>">
        <?php else: ?>
            <p class="text-sm text-slate-500">Set up your store branding in your profile to get a shareable link.</p>
            <a href="<?= url('/profile') ?>" class="text-primary text-sm">Go to profile settings</a>
        <?php endif; ?>
    </div>
</div>

<form method="post" action="<?= url('/accounts/sell') ?>" class="mt-4 bg-slate-50 border border-slate-200 rounded p-6 space-y-4">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <div>
        <label class="block text-sm font-medium">Title</label>
        <input name="title" type="text" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" placeholder="e.g. Verified Gmail Account" required>
    </div>
    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Category</label>
            <?php if (!empty($categories)): ?>
                <select name="category" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
                    <option value="">Select category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input name="category" type="text" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" placeholder="Category" required>
            <?php endif; ?>
        </div>
        <div>
            <label class="block text-sm font-medium">Platform</label>
            <?php if (!empty($platforms)): ?>
                <select name="platform" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
                    <option value="">Select platform</option>
                    <?php foreach ($platforms as $platform): ?>
                        <option value="<?= htmlspecialchars($platform) ?>"><?= htmlspecialchars($platform) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input name="platform" type="text" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" placeholder="Platform" required>
            <?php endif; ?>
        </div>
    </div>
    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Year (optional)</label>
            <input name="year" type="number" min="2000" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Price (XAF)</label>
            <input name="price" type="number" step="0.01" min="0" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium">Description</label>
        <textarea name="description" rows="3" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" placeholder="Short description"></textarea>
    </div>
    <div>
        <label class="block text-sm font-medium">Account details</label>
        <textarea name="account_details" rows="5" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" placeholder="Login details, recovery info, etc." required></textarea>
        <p class="text-xs text-slate-500 mt-1">Only admin and the buyer will see these details.</p>
    </div>
    <button class="bg-primary text-white px-4 py-2 rounded">Submit listing</button>
</form>

<div class="mt-8 bg-white border border-slate-200 rounded p-5">
    <h3 class="text-lg font-semibold">Your listings</h3>
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-slate-200">
                    <th class="p-3">Title</th>
                    <th class="p-3">Category</th>
                    <th class="p-3">Platform</th>
                    <th class="p-3">Price</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Created</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listings)): ?>
                    <tr><td class="p-3 text-slate-500" colspan="6">No listings yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($listings as $listing): ?>
                        <tr class="border-b border-slate-100">
                            <td class="p-3 font-medium"><?= htmlspecialchars($listing['title']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($listing['category']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($listing['platform']) ?></td>
                            <td class="p-3">XAF <?= number_format((float)$listing['price'], 2) ?></td>
                            <td class="p-3 capitalize"><?= htmlspecialchars($listing['status']) ?></td>
                            <td class="p-3 text-xs text-slate-500"><?= htmlspecialchars($listing['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
