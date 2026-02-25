<div class="max-w-md mx-auto">
    <h2 class="text-2xl font-semibold">Create your account</h2>
    <form method="post" action="<?= url('/register') ?>" class="mt-6 space-y-4">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <div>
            <label class="block text-sm font-medium text-slate-700">Name</label>
            <input name="name" type="text" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input name="email" type="email" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Password</label>
            <input name="password" type="password" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" required>
        </div>
        <button class="w-full bg-primary text-white py-2 rounded">Create account</button>
        <p class="text-sm text-slate-500">Already have an account? <a href="<?= url('/login') ?>" class="text-primary">Login</a></p>
    </form>
</div>
