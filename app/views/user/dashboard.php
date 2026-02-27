<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <h2 class="text-2xl font-semibold">Recent Purchases</h2>
        <div class="mt-4 bg-slate-50 border border-slate-200 rounded">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-200">
                        <th class="p-3">Number</th>
                        <th class="p-3">Request ID</th>
                        <th class="p-3">Type</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($purchases)): ?>
                        <tr><td class="p-3 text-slate-500" colspan="5">No purchases yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($purchases as $purchase): ?>
                            <tr class="border-b border-slate-100">
                                <td class="p-3"><?= htmlspecialchars($purchase['number']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($purchase['request_id']) ?></td>
                                <td class="p-3">
                                    <?= htmlspecialchars($purchase['purchase_type'] ?? 'buy') ?>
                                    <?php if (!empty($purchase['rental_end_at'])): ?>
                                        <div class="text-xs text-slate-500">Until <?= htmlspecialchars($purchase['rental_end_at']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3"><?= htmlspecialchars($purchase['status']) ?></td>
                                <td class="p-3">$<?= number_format((float)$purchase['cost'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div>
            <h2 class="text-2xl font-semibold">Boosting Orders</h2>
            <div class="mt-4 bg-slate-50 border border-slate-200 rounded">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-slate-200">
                            <th class="p-3">Service</th>
                            <th class="p-3">Quantity</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Charge</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($socialOrders)): ?>
                            <tr><td class="p-3 text-slate-500" colspan="4">No boosting orders yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($socialOrders as $order): ?>
                                <tr class="border-b border-slate-100">
                                    <td class="p-3">
                                        <div class="font-medium"><?= htmlspecialchars($order['service_name'] ?? 'Service') ?></div>
                                        <div class="text-xs text-slate-500"><?= htmlspecialchars($order['service_category'] ?? '') ?></div>
                                    </td>
                                    <td class="p-3"><?= (int)$order['quantity'] ?></td>
                                    <td class="p-3"><?= htmlspecialchars($order['status']) ?></td>
                                    <td class="p-3">$<?= number_format((float)($order['charge'] ?? 0), 4) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div>
        <div class="bg-primary text-white rounded p-4">
            <p class="text-sm">Wallet balance</p>
            <p class="text-2xl font-semibold">$<?= number_format((float)current_user()['balance'], 2) ?></p>
            <a href="<?= url('/wallet') ?>" class="inline-block mt-3 bg-white text-primary px-4 py-2 rounded">Refill</a>
        </div>
        <div class="mt-6 border border-slate-200 rounded p-4">
            <h3 class="text-lg font-semibold">API Token</h3>
            <?php if (!empty($apiKey)): ?>
                <p class="text-sm text-slate-600 mt-2">Use this token to access the API.</p>
                <div class="mt-2 bg-slate-50 border border-slate-200 rounded px-3 py-2 text-sm font-mono break-all">
                    <?= htmlspecialchars($apiKey['token']) ?>
                </div>
            <?php else: ?>
                <p class="text-sm text-slate-600 mt-2">Generate an API token to start using the developer API.</p>
                <form method="post" action="<?= url('/api-token') ?>" class="mt-3">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <button class="bg-primary text-white px-4 py-2 rounded">Generate token</button>
                </form>
            <?php endif; ?>
        </div>
        <div class="mt-6">
            <h3 class="text-lg font-semibold">Transactions</h3>
            <ul class="mt-3 space-y-3 text-sm">
                <?php if (empty($transactions)): ?>
                    <li class="text-slate-500">No transactions yet.</li>
                <?php else: ?>
                    <?php foreach ($transactions as $tx): ?>
                        <li class="border border-slate-200 rounded px-3 py-2">
                            <div class="flex justify-between">
                                <span><?= htmlspecialchars($tx['type']) ?></span>
                                <span><?= number_format((float)$tx['amount'], 2) ?></span>
                            </div>
                            <p class="text-slate-500 text-xs"><?= htmlspecialchars($tx['status']) ?> · <?= htmlspecialchars($tx['created_at']) ?></p>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
