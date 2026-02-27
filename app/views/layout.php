<?php
$viewFile = __DIR__ . '/' . $view . '.php';
$user = current_user();
$flashError = flash('error');
$flashSuccess = flash('success');
$currentPath = current_path();
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
                        danger: '#DC2626',
                        surface: '#F8FAFC',
                        muted: '#64748B'
                    },
                    boxShadow: {
                        soft: '0 10px 30px rgba(15, 23, 42, 0.06)'
                    },
                    borderRadius: {
                        xl2: '1.25rem'
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            color-scheme: light;
        }
        body {
            background: #F8FAFC;
        }
        .card {
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }
        .soft-input {
            border-radius: 0.75rem;
            border: 1px solid #E2E8F0;
            background: #ffffff;
        }
        .soft-input:focus {
            outline: none;
            border-color: #1D4ED8;
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.15);
        }
        .soft-btn {
            border-radius: 0.75rem;
            font-weight: 600;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .soft-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
        }
        .chip {
            border-radius: 9999px;
            border: 1px solid #E2E8F0;
            background: #ffffff;
        }
        .nav-pill {
            border-radius: 1rem;
        }
        .float-slow {
            animation: float 8s ease-in-out infinite;
        }
        .float-fast {
            animation: float 5s ease-in-out infinite;
        }
        .fade-in-up {
            animation: fadeUp 0.8s ease both;
        }
        .fade-in-delay-1 { animation-delay: 0.15s; }
        .fade-in-delay-2 { animation-delay: 0.3s; }
        .fade-in-delay-3 { animation-delay: 0.45s; }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .reveal {
            opacity: 0;
            transform: translateY(16px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .reveal-visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="text-slate-800">
    <?php if (!$user || $user['role'] === 'admin'): ?>
        <nav class="border-b border-slate-200 sticky top-0 bg-white/90 backdrop-blur z-40">
            <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
                <a href="<?= url('/') ?>" class="font-bold text-primary text-xl flex items-center space-x-2">
                    <?php $logo = setting('logo_path'); ?>
                    <?php if ($logo): ?>
                        <img src="<?= htmlspecialchars(url($logo)) ?>" alt="Logo" class="h-8">
                    <?php endif; ?>
                    <span>GetSMS</span>
                </a>
                <div class="hidden md:flex items-center space-x-4">
                    <?php if (!$user): ?>
                        <a href="<?= url('/login') ?>" class="text-slate-700 hover:text-primary">Login</a>
                        <a href="<?= url('/register') ?>" class="bg-primary text-white px-4 py-2 rounded">Create account</a>
                    <?php else: ?>
                        <a href="<?= url('/dashboard') ?>" class="text-slate-700 hover:text-primary">Dashboard</a>
                        <a href="<?= url('/services') ?>" class="text-slate-700 hover:text-primary">Buy Number</a>
                        <a href="<?= url('/boosting') ?>" class="text-slate-700 hover:text-primary">Boosting</a>
                        <a href="<?= url('/wallet') ?>" class="text-slate-700 hover:text-primary">Wallet</a>
                        <a href="<?= url('/profile') ?>" class="text-slate-700 hover:text-primary">Profile</a>
                        <a href="<?= url('/help') ?>" class="text-slate-700 hover:text-primary">Help</a>
                        <a href="<?= url('/api/docs') ?>" class="text-slate-700 hover:text-primary">API Docs</a>
                        <?php if ($user['role'] === 'admin'): ?>
                            <a href="<?= url('/admin') ?>" class="text-slate-700 hover:text-primary">Admin</a>
                        <?php endif; ?>
                        <form method="post" action="<?= url('/logout') ?>" class="inline">
                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                            <button class="bg-danger text-white px-4 py-2 rounded inline-flex items-center space-x-2">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="md:hidden flex items-center space-x-2">
                    <?php if (!$user): ?>
                        <button id="mobileMenuToggle" class="border border-slate-200 rounded px-3 py-2 text-sm">Menu</button>
                    <?php else: ?>
                        <button id="mobileMenuToggle" class="border border-slate-200 rounded px-3 py-2 text-sm">More</button>
                    <?php endif; ?>
                </div>
            </div>
            <div id="mobileMenu" class="md:hidden hidden border-t border-slate-200 bg-white">
                <div class="max-w-6xl mx-auto px-4 py-3 space-y-2">
                    <?php if (!$user): ?>
                        <a href="<?= url('/login') ?>" class="block text-slate-700">Login</a>
                        <a href="<?= url('/register') ?>" class="block text-primary font-semibold">Create account</a>
                    <?php else: ?>
                        <?php if ($user['role'] === 'admin'): ?>
                            <a href="<?= url('/admin') ?>" class="block text-slate-700">Admin</a>
                        <?php endif; ?>
                        <a href="<?= url('/profile') ?>" class="block text-slate-700">Profile</a>
                        <a href="<?= url('/help') ?>" class="block text-slate-700">Help Center</a>
                        <a href="<?= url('/api/docs') ?>" class="block text-slate-700">API Docs</a>
                        <form method="post" action="<?= url('/logout') ?>" class="block">
                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                            <button class="w-full bg-danger text-white px-4 py-2 rounded inline-flex items-center justify-center space-x-2">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    <?php endif; ?>

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

    <?php if ($user && $user['role'] !== 'admin'): ?>
        <?php
            $userNavItems = [
                ['href' => '/dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard'],
                ['href' => '/services', 'label' => 'SMS Services', 'icon' => 'smartphone'],
                ['href' => '/boosting', 'label' => 'Boosting', 'icon' => 'rocket'],
                ['href' => '/wallet', 'label' => 'Wallet', 'icon' => 'wallet'],
                ['href' => '/profile', 'label' => 'Profile', 'icon' => 'user'],
                ['href' => '/help', 'label' => 'Help Center', 'icon' => 'life-buoy'],
            ];
        ?>
        <div class="flex h-[75vh] bg-slate-50 border border-slate-200 rounded-xl overflow-hidden max-w-6xl mx-auto">
            <aside class="hidden md:flex w-72 bg-white border-r border-slate-200 flex-col min-h-0 overflow-y-auto">
                <div class="p-5 border-b border-slate-200 flex items-center space-x-3">
                    <?php $logo = setting('logo_path'); ?>
                    <?php if ($logo): ?>
                        <img src="<?= htmlspecialchars(url($logo)) ?>" alt="Logo" class="h-10">
                    <?php endif; ?>
                    <div>
                        <p class="text-lg font-semibold text-slate-900">Account</p>
                        <p class="text-xs text-slate-500">Welcome back</p>
                    </div>
                </div>
                <nav class="flex-1 p-4 space-y-1">
                    <?php foreach ($userNavItems as $item): ?>
                        <?php $isActive = $currentPath === $item['href']; ?>
                        <a href="<?= url($item['href']) ?>" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-sm font-medium <?= $isActive ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                            <i data-lucide="<?= htmlspecialchars($item['icon']) ?>" class="w-4 h-4"></i>
                            <span><?= htmlspecialchars($item['label']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>
                <form method="post" action="<?= url('/logout') ?>" class="p-4 border-t border-slate-200">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <button class="w-full bg-danger text-white px-4 py-2 rounded inline-flex items-center justify-center space-x-2">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </aside>
            <main class="flex-1 min-h-0 overflow-y-auto px-4 py-6 md:py-8 pb-28 md:pb-8">
                <?php if (file_exists($viewFile)) { require $viewFile; } else { echo '<p>View not found.</p>'; } ?>
            </main>
        </div>
    <?php else: ?>
        <main class="max-w-6xl mx-auto px-4 py-6 md:py-8 pb-28 md:pb-8">
            <?php if (file_exists($viewFile)) { require $viewFile; } else { echo '<p>View not found.</p>'; } ?>
        </main>
    <?php endif; ?>

    <?php if ($user && $user['role'] !== 'admin'): ?>
        <nav class="md:hidden fixed bottom-4 left-1/2 -translate-x-1/2 w-[calc(100%-2rem)] max-w-md bg-white/90 backdrop-blur border border-slate-200 shadow-lg rounded-2xl z-50">
            <div class="grid grid-cols-5 text-[11px]">
                <?php
                    $navItems = [
                        ['href' => '/dashboard', 'label' => 'Home', 'icon' => 'layout-dashboard'],
                        ['href' => '/services', 'label' => 'SMS', 'icon' => 'smartphone'],
                        ['href' => '/boosting', 'label' => 'Boost', 'icon' => 'rocket'],
                        ['href' => '/wallet', 'label' => 'Wallet', 'icon' => 'wallet'],
                        ['href' => '/api/docs', 'label' => 'API', 'icon' => 'code'],
                    ];
                ?>
                <?php foreach ($navItems as $item): ?>
                    <?php $isActive = $currentPath === $item['href']; ?>
                    <a href="<?= url($item['href']) ?>" class="flex flex-col items-center justify-center py-2.5 px-1 rounded-2xl <?= $isActive ? 'text-primary' : 'text-slate-500' ?>">
                        <div class="w-9 h-9 flex items-center justify-center rounded-xl <?= $isActive ? 'bg-primary/10' : '' ?>">
                            <i data-lucide="<?= htmlspecialchars($item['icon']) ?>" class="w-5 h-5"></i>
                        </div>
                        <span class="mt-1 font-medium"><?= htmlspecialchars($item['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </nav>
    <?php endif; ?>

    <script>
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileMenuToggle && mobileMenu) {
            mobileMenuToggle.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        if (window.lucide) {
            window.lucide.createIcons();
        }
    </script>
</body>
</html>
