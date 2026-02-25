<div class="max-w-md mx-auto">
    <h2 class="text-2xl font-semibold">Welcome back</h2>
    <form method="post" action="<?= url('/login') ?>" class="mt-6 space-y-4">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input name="email" type="email" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Password</label>
            <input name="password" type="password" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
        </div>
        <button class="w-full bg-primary text-white py-2 rounded">Login</button>
        <p class="text-sm text-slate-500">New here? <a href="<?= url('/register') ?>" class="text-primary">Create account</a></p>
    </form>
</div>
