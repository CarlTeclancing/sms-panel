<div class="flex items-center justify-between">
    <h2 class="text-2xl font-semibold">Purchased Accounts</h2>
    <a href="<?= url('/accounts') ?>" class="text-primary">Browse marketplace</a>
</div>

<div class="mt-6 bg-white border border-slate-200 rounded p-5">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-slate-200">
                    <th class="p-3">Listing ID</th>
                    <th class="p-3">Price</th>
                    <th class="p-3">Details</th>
                    <th class="p-3">Purchased</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($purchases)): ?>
                    <tr><td class="p-3 text-slate-500" colspan="4">No purchases yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($purchases as $purchase): ?>
                        <tr class="border-b border-slate-100">
                            <td class="p-3">#<?= (int)$purchase['listing_id'] ?></td>
                            <td class="p-3">XAF <?= number_format((float)$purchase['price'], 2) ?></td>
                            <td class="p-3">
                                <details class="cursor-pointer">
                                    <summary class="text-primary">View details</summary>
                                    <pre class="mt-2 whitespace-pre-wrap text-xs bg-slate-50 border border-slate-200 rounded p-3"><?= htmlspecialchars($purchase['details_snapshot']) ?></pre>
                                </details>
                            </td>
                            <td class="p-3 text-xs text-slate-500"><?= htmlspecialchars($purchase['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
