<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold">Help Center</h2>
        <a href="<?= url('/profile') ?>" class="text-primary">Profile</a>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200 rounded p-4">
            <h3 class="font-semibold">Getting started</h3>
            <p class="text-sm text-slate-500 mt-2">Fund your wallet, then buy SMS or boosting services from the Services page.</p>
        </div>
        <div class="bg-white border border-slate-200 rounded p-4">
            <h3 class="font-semibold">Track orders</h3>
            <p class="text-sm text-slate-500 mt-2">View SMS purchases and boosting orders in your dashboard and wallet activity.</p>
        </div>
        <div class="bg-white border border-slate-200 rounded p-4">
            <h3 class="font-semibold">Need help?</h3>
            <p class="text-sm text-slate-500 mt-2">Submit a ticket and our team will respond as soon as possible.</p>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded p-5">
        <h3 class="text-lg font-semibold">Submit a ticket</h3>
        <form method="post" action="<?= url('/help/ticket') ?>" class="mt-4 space-y-4">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <div>
                <label class="block text-sm font-medium">Subject</label>
                <input name="subject" type="text" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Message</label>
                <textarea name="message" rows="5" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required></textarea>
            </div>
            <button class="bg-primary text-white px-4 py-2 rounded">Send ticket</button>
        </form>
    </div>

    <div class="bg-white border border-slate-200 rounded p-5">
        <h3 class="text-lg font-semibold">Your tickets</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-200">
                        <th class="p-3">Subject</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                        <tr><td class="p-3 text-slate-500" colspan="3">No tickets yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr class="border-b border-slate-100">
                                <td class="p-3"><?= htmlspecialchars($ticket['subject']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($ticket['status']) ?></td>
                                <td class="p-3 text-xs text-slate-500"><?= htmlspecialchars($ticket['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
