<div class="max-w-xl">
    <h2 class="text-2xl font-semibold">Wallet Refill</h2>
    <p class="text-slate-600 mt-2">Fund your account using MoMo or Orange Money via Fapshi.</p>

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
        <button class="bg-primary text-white px-6 py-2 rounded">Initiate payment</button>
    </form>
</div>
