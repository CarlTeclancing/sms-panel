<div class="flex items-center justify-between">
    <h2 class="text-2xl font-semibold">Withdrawals</h2>
    <a href="<?= url('/wallet') ?>" class="text-primary">Go to wallet</a>
</div>

<div class="mt-4 bg-white border border-slate-200 rounded p-5">
    <h3 class="text-lg font-semibold">Request withdrawal</h3>
    <p class="text-sm text-slate-500 mt-1">Withdrawals are taken from earnings balance. A <?= number_format((float)$feePercent, 2) ?>% fee is deducted from every withdrawal.</p>
    <form method="post" action="<?= url('/accounts/withdrawals') ?>" class="mt-4 grid md:grid-cols-2 gap-4">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <div>
            <label class="block text-sm font-medium">Amount (USD)</label>
            <input name="amount" type="number" step="0.01" min="0" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium">Note (payment details)</label>
            <input name="note" type="text" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" placeholder="Bank, mobile money, etc.">
        </div>
        <div class="md:col-span-2">
            <button class="bg-primary text-white px-4 py-2 rounded">Submit request</button>
        </div>
    </form>
</div>

<div class="mt-6 bg-white border border-slate-200 rounded p-5">
    <h3 class="text-lg font-semibold">Your withdrawal requests</h3>
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-slate-200">
                    <th class="p-3">Amount</th>
                    <th class="p-3">Fee</th>
                    <th class="p-3">Net</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Created</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($withdrawals)): ?>
                    <tr><td class="p-3 text-slate-500" colspan="5">No withdrawal requests yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($withdrawals as $withdrawal): ?>
                        <tr class="border-b border-slate-100">
                            <td class="p-3">$<?= number_format((float)$withdrawal['amount'], 2) ?></td>
                            <td class="p-3">$<?= number_format((float)$withdrawal['fee'], 2) ?></td>
                            <td class="p-3">$<?= number_format((float)$withdrawal['net_amount'], 2) ?></td>
                            <td class="p-3 capitalize"><?= htmlspecialchars($withdrawal['status']) ?></td>
                            <td class="p-3 text-xs text-slate-500"><?= htmlspecialchars($withdrawal['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
