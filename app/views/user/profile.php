<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold">Profile</h2>
        <a href="<?= url('/help') ?>" class="text-primary">Help Center</a>
    </div>

    <div class="grid md:grid-cols-[220px_1fr] gap-6">
        <aside class="bg-white border border-slate-200 rounded p-4 space-y-2">
            <button class="profile-tab w-full text-left px-3 py-2 rounded text-sm font-medium bg-primary/10 text-primary" data-tab="profile-info">Profile info</button>
            <button class="profile-tab w-full text-left px-3 py-2 rounded text-sm font-medium text-slate-600 hover:bg-slate-50" data-tab="referrals">Referrals</button>
            <button class="profile-tab w-full text-left px-3 py-2 rounded text-sm font-medium text-slate-600 hover:bg-slate-50" data-tab="password">Password</button>
        </aside>

        <div class="space-y-6">
            <section id="profile-info" class="profile-panel bg-white border border-slate-200 rounded p-5">
                <h3 class="text-lg font-semibold">Profile information</h3>
                <form method="post" action="<?= url('/profile/update') ?>" enctype="multipart/form-data" class="mt-4 space-y-4">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-full bg-slate-100 overflow-hidden flex items-center justify-center">
                            <?php if (!empty(current_user()['profile_image'])): ?>
                                <img src="<?= htmlspecialchars(url(current_user()['profile_image'])) ?>" alt="Profile image" class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="text-slate-400 text-xl">?</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Profile image</label>
                            <input name="profile_image" type="file" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Name</label>
                            <input name="name" type="text" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" value="<?= htmlspecialchars(current_user()['name'] ?? '') ?>" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Email</label>
                            <input name="email" type="email" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" value="<?= htmlspecialchars(current_user()['email'] ?? '') ?>" required>
                        </div>
                    </div>
                    <button class="bg-primary text-white px-4 py-2 rounded">Save changes</button>
                </form>
            </section>

            <section id="referrals" class="profile-panel hidden bg-white border border-slate-200 rounded p-5">
                <h3 class="text-lg font-semibold">Referral program</h3>
                <p class="text-sm text-slate-500 mt-1">Share your link and earn $1 after a referral makes their first deposit.</p>
                <div class="mt-4 grid md:grid-cols-3 gap-4">
                    <div class="border border-slate-200 rounded p-4">
                        <p class="text-xs text-slate-500">Referral link</p>
                        <input readonly class="mt-2 w-full border border-slate-300 rounded px-3 py-2 text-sm" value="<?= htmlspecialchars($referralLink ?? '') ?>">
                    </div>
                    <div class="border border-slate-200 rounded p-4">
                        <p class="text-xs text-slate-500">Referrals</p>
                        <p class="text-2xl font-semibold mt-2"><?= (int)($referralsCount ?? 0) ?></p>
                    </div>
                    <div class="border border-slate-200 rounded p-4">
                        <p class="text-xs text-slate-500">Earnings</p>
                        <p class="text-2xl font-semibold mt-2">$<?= number_format((float)($referralEarnings ?? 0), 2) ?></p>
                    </div>
                </div>
            </section>

            <section id="password" class="profile-panel hidden bg-white border border-slate-200 rounded p-5">
                <h3 class="text-lg font-semibold">Reset password</h3>
                <form method="post" action="<?= url('/profile/password') ?>" class="mt-4 space-y-4">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Current password</label>
                            <input name="current_password" type="password" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">New password</label>
                            <input name="password" type="password" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Confirm new password</label>
                            <input name="confirm_password" type="password" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
                        </div>
                    </div>
                    <button class="bg-primary text-white px-4 py-2 rounded">Update password</button>
                </form>
            </section>
        </div>
    </div>
</div>

<script>
    const tabButtons = document.querySelectorAll('.profile-tab');
    const panels = document.querySelectorAll('.profile-panel');

    const setTab = (tabId) => {
        panels.forEach(panel => {
            panel.classList.toggle('hidden', panel.id !== tabId);
        });
        tabButtons.forEach(btn => {
            const active = btn.dataset.tab === tabId;
            btn.classList.toggle('bg-primary/10', active);
            btn.classList.toggle('text-primary', active);
            btn.classList.toggle('text-slate-600', !active);
            btn.classList.toggle('hover:bg-slate-50', !active);
        });
    };

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => setTab(btn.dataset.tab));
    });
</script>
