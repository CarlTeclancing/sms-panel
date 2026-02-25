<?php
$viewFile = __DIR__ . '/' . $view . '.php';
$user = current_user();
$flashError = flash('error');
$flashSuccess = flash('success');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'GetSMS') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1D4ED8',
                        danger: '#DC2626'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white text-slate-800">
    <nav class="border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="<?= url('/') ?>" class="font-bold text-primary text-xl flex items-center space-x-2">
                <?php $logo = setting('logo_path'); ?>
                <?php if ($logo): ?>
                    <img src="<?= htmlspecialchars(url($logo)) ?>" alt="Logo" class="h-8">
                <?php endif; ?>
                <span>GetSMS</span>
            </a>
            <div class="space-x-4">
                <?php if (!$user): ?>
                    <a href="<?= url('/login') ?>" class="text-slate-700 hover:text-primary">Login</a>
                    <a href="<?= url('/register') ?>" class="bg-primary text-white px-4 py-2 rounded">Create account</a>
                <?php else: ?>
                    <a href="<?= url('/dashboard') ?>" class="text-slate-700 hover:text-primary">Dashboard</a>
                    <a href="<?= url('/services') ?>" class="text-slate-700 hover:text-primary">Buy Number</a>
                    <a href="<?= url('/wallet') ?>" class="text-slate-700 hover:text-primary">Wallet</a>
                    <a href="<?= url('/api/docs') ?>" class="text-slate-700 hover:text-primary">API Docs</a>
                    <?php if ($user['role'] === 'admin'): ?>
                        <a href="<?= url('/admin') ?>" class="text-slate-700 hover:text-primary">Admin</a>
                    <?php endif; ?>
                    <form method="post" action="<?= url('/logout') ?>" class="inline">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <button class="text-slate-700 hover:text-primary">Logout</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <?php if ($flashError): ?>
        <div class="max-w-6xl mx-auto mt-4 px-4">
            <div class="bg-red-50 text-danger border border-red-200 px-4 py-3 rounded">
                <?= htmlspecialchars($flashError) ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($flashSuccess): ?>
        <div class="max-w-6xl mx-auto mt-4 px-4">
            <div class="bg-green-50 text-green-700 border border-green-200 px-4 py-3 rounded">
                <?= htmlspecialchars($flashSuccess) ?>
            </div>
        </div>
    <?php endif; ?>

    <main class="max-w-6xl mx-auto px-4 py-8 pb-24 md:pb-8">
        <?php if (file_exists($viewFile)) { require $viewFile; } else { echo '<p>View not found.</p>'; } ?>
    </main>

    <?php if ($user && $user['role'] !== 'admin'): ?>
        <nav class="md:hidden fixed bottom-0 inset-x-0 bg-white border-t border-slate-200 z-50">
            <div class="grid grid-cols-4 text-xs">
                <a href="<?= url('/dashboard') ?>" class="flex flex-col items-center justify-center py-3 text-slate-600">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Home</span>
                </a>
                <a href="<?= url('/services') ?>" class="flex flex-col items-center justify-center py-3 text-slate-600">
                    <i data-lucide="smartphone" class="w-5 h-5"></i>
                    <span>Services</span>
                </a>
                <a href="<?= url('/wallet') ?>" class="flex flex-col items-center justify-center py-3 text-slate-600">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                    <span>Wallet</span>
                </a>
                <a href="<?= url('/api/docs') ?>" class="flex flex-col items-center justify-center py-3 text-slate-600">
                    <i data-lucide="code" class="w-5 h-5"></i>
                    <span>API</span>
                </a>
            </div>
        </nav>
    <?php endif; ?>

    <script>
        if (window.lucide) {
            window.lucide.createIcons();
        }
    </script>
</body>
</html>
