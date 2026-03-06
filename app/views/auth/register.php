<div class="max-w-md mx-auto">
    <h2 class="text-2xl font-semibold">Create your account</h2>
    <form method="post" action="<?= url('/register') ?>" class="mt-6 space-y-4">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <?php if (!empty($referralCode)): ?>
            <input type="hidden" name="referral_code" value="<?= htmlspecialchars($referralCode) ?>">
        <?php endif; ?>
        <div>
            <label class="block text-sm font-medium text-slate-700">Name</label>
            <input name="name" type="text" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input name="email" type="email" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Phone number</label>
            <input name="phone_number" type="tel" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" placeholder="+237 6xx xxx xxx" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Country</label>
            <?php
                $countries = [
                    'Cameroon', 'Nigeria', 'Ghana', 'Kenya', 'South Africa', 'Cote d’Ivoire', 'Senegal', 'Uganda',
                    'Tanzania', 'Rwanda', 'Zambia', 'Zimbabwe', 'Egypt', 'Morocco', 'Algeria', 'Tunisia',
                    'United States', 'Canada', 'United Kingdom', 'France', 'Germany', 'Spain', 'Italy',
                    'India', 'Pakistan', 'Bangladesh', 'United Arab Emirates', 'Saudi Arabia'
                ];
            ?>
            <select name="country" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
                <option value="">Select country</option>
                <?php foreach ($countries as $country): ?>
                    <option value="<?= htmlspecialchars($country) ?>"><?= htmlspecialchars($country) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Password</label>
            <input name="password" type="password" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
        </div>
        <button class="w-full bg-primary text-white py-2 rounded">Create account</button>
        <p class="text-sm text-slate-500">Already have an account? <a href="<?= url('/login') ?>" class="text-primary">Login</a></p>
    </form>
</div>
