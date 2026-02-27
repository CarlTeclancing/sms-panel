<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold">Wallet</h2>
            <p class="text-slate-600 mt-1">Manage your balance and recent activity.</p>
        </div>
        <button id="openTopup" class="bg-primary text-white px-4 py-2 rounded">Top up</button>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-1 bg-primary text-white rounded p-5">
            <p class="text-sm">Available balance</p>
            <p class="text-3xl font-semibold mt-2">$<?= number_format((float)($balance ?? 0), 2) ?></p>
            <button id="openTopupCard" class="mt-4 bg-white text-primary px-4 py-2 rounded">Top up</button>
        </div>
        <div class="md:col-span-2 bg-white border border-slate-200 rounded p-5">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Recent Transactions</h3>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-slate-200">
                            <th class="p-3">Type</th>
                            <th class="p-3">Amount</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr><td class="p-3 text-slate-500" colspan="4">No transactions yet.</td></tr>
                        <?php else: ?>
                            <?php foreach (array_slice($transactions, 0, 10) as $tx): ?>
                                <tr class="border-b border-slate-100">
                                    <td class="p-3"><?= htmlspecialchars($tx['type']) ?></td>
                                    <td class="p-3">$<?= number_format((float)$tx['amount'], 2) ?></td>
                                    <td class="p-3"><?= htmlspecialchars($tx['status']) ?></td>
                                    <td class="p-3 text-slate-500 text-xs"><?= htmlspecialchars($tx['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded p-5">
        <h3 class="text-lg font-semibold">Activity</h3>
        <div class="mt-4">
            <?php if (empty($activity)): ?>
                <p class="text-slate-500 text-sm">No recent activity.</p>
            <?php else: ?>
                <ul class="space-y-3">
                    <?php foreach (array_slice($activity, 0, 10) as $item): ?>
                        <li class="border border-slate-200 rounded px-4 py-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium"><?= htmlspecialchars($item['label']) ?></p>
                                    <p class="text-xs text-slate-500"><?= htmlspecialchars($item['detail']) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold">$<?= number_format((float)$item['amount'], 2) ?></p>
                                    <p class="text-xs text-slate-500"><?= htmlspecialchars($item['status']) ?> · <?= htmlspecialchars($item['created_at'] ?? '') ?></p>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="topupModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-lg p-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">Top up wallet</h3>
            <button id="closeTopup" class="text-slate-500">Close</button>
        </div>
        <p class="text-sm text-slate-500 mt-1">Fund your account using MoMo or Orange Money via Fapshi.</p>
        <form method="post" action="<?= url('/wallet/refill') ?>" class="mt-6 space-y-4">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <div>
                <label class="block text-sm font-medium">Amount (USD)</label>
                <input name="amount" type="number" step="0.01" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Mobile Number</label>
                <input name="phone" type="text" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Provider</label>
                <select name="provider" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
                    <option value="mtn">MTN MoMo</option>
                    <option value="orange">Orange Money</option>
                </select>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" id="cancelTopup" class="border border-slate-300 px-4 py-2 rounded">Cancel</button>
                <button class="bg-primary text-white px-6 py-2 rounded">Initiate payment</button>
            </div>
        </form>
    </div>
</div>

<script>
    const topupModal = document.getElementById('topupModal');
    const openTopup = document.getElementById('openTopup');
    const openTopupCard = document.getElementById('openTopupCard');
    const closeTopup = document.getElementById('closeTopup');
    const cancelTopup = document.getElementById('cancelTopup');

    const showTopup = () => {
        if (topupModal) {
            topupModal.classList.remove('hidden');
            topupModal.classList.add('flex');
        }
    };

    const hideTopup = () => {
        if (topupModal) {
            topupModal.classList.add('hidden');
            topupModal.classList.remove('flex');
        }
    };

    if (openTopup) openTopup.addEventListener('click', showTopup);
    if (openTopupCard) openTopupCard.addEventListener('click', showTopup);
    if (closeTopup) closeTopup.addEventListener('click', hideTopup);
    if (cancelTopup) cancelTopup.addEventListener('click', hideTopup);

    if (topupModal) {
        topupModal.addEventListener('click', (event) => {
            if (event.target === topupModal) {
                hideTopup();
            }
        });
    }
</script>
