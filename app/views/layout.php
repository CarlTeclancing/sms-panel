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
    <?php $favicon = setting('favicon_path'); ?>
    <?php if (!empty($favicon)): ?>
        <link rel="icon" href="<?= htmlspecialchars(url($favicon)) ?>">
        <link rel="apple-touch-icon" href="<?= htmlspecialchars(url($favicon)) ?>">
    <?php endif; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#146EF5',
                        primaryAlt: '#006AFF',
                        ink: '#080808',
                        white: '#FFFFFF',
                        surface: '#F0F0F0',
                        muted: '#898989',
                        border: '#D8D8D8',
                        gray900: '#171717',
                        gray800: '#222222',
                        gray700: '#363636',
                        gray600: '#5A5A5A',
                        gray500: '#757575',
                        gray400: '#898989',
                        gray300: '#ABABAB',
                        gray200: '#D8D8D8',
                        gray100: '#F0F0F0',
                        accentPurple: '#7A3DFF',
                        accentPink: '#ED52CB',
                        accentRed: '#EE1D36',
                        accentOrange: '#FF6B00',
                        accentGreen: '#00D722',
                        accentYellow: '#FFAE13',
                        danger: '#EE1D36'
                    },
                    boxShadow: {
                        soft: '0 10px 30px rgba(8, 8, 8, 0.12)'
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
            --glass-bg: rgba(255, 255, 255, 0.18);
            --glass-border: rgba(255, 255, 255, 0.35);
            --glass-shadow: 0 20px 60px rgba(8, 8, 8, 0.12);
            --glass-blur: blur(18px);
        }
        body {
            background: radial-gradient(1200px 600px at 10% 10%, rgba(20, 110, 245, 0.18), transparent),
                        radial-gradient(1000px 500px at 90% 0%, rgba(122, 61, 255, 0.16), transparent),
                        radial-gradient(900px 450px at 50% 100%, rgba(237, 82, 203, 0.12), transparent),
                        #F0F0F0;
            color: #080808;
        }
        .card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 1.25rem;
            box-shadow: var(--glass-shadow);
            backdrop-filter: var(--glass-blur) saturate(160%);
            -webkit-backdrop-filter: var(--glass-blur) saturate(160%);
        }
        .soft-input {
            border-radius: 0.85rem;
            border: 1px solid #D8D8D8;
            background: rgba(255, 255, 255, 0.9);
            color: #080808;
        }
        .soft-input:focus {
            outline: none;
            border-color: #146EF5;
            box-shadow: 0 0 0 3px rgba(20, 110, 245, 0.25);
        }
        .soft-btn {
            border-radius: 0.85rem;
            font-weight: 600;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .soft-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(8, 8, 8, 0.14);
        }
        .chip {
            border-radius: 9999px;
            border: 1px solid var(--glass-border);
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur) saturate(160%);
            -webkit-backdrop-filter: var(--glass-blur) saturate(160%);
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
        .glass-panel {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 1.25rem;
            box-shadow: var(--glass-shadow);
            backdrop-filter: var(--glass-blur) saturate(160%);
            -webkit-backdrop-filter: var(--glass-blur) saturate(160%);
        }
    </style>
</head>
<body class="text-slate-800">
    <?php if (!$user || $user['role'] === 'admin'): ?>
        <nav class="border-b border-slate-200 sticky top-0 bg-white/90 backdrop-blur z-40">
            <div class="w-full px-4 py-4 flex items-center justify-between">
                <a href="<?= url('/') ?>" class="font-bold text-primary text-xl flex items-center space-x-2">
                    <?php $logo = setting('logo_path'); ?>
                    <?php if ($logo): ?>
                        <img src="<?= htmlspecialchars(url($logo)) ?>" alt="Logo" class="h-8">
                    <?php endif; ?>
                </a>
                <div class="hidden md:flex items-center space-x-4">
                    <?php if (!$user): ?>
                        <a href="<?= url('/services') ?>" class="text-slate-700 hover:text-primary">Services</a>
                        <a href="<?= url('/accounts') ?>" class="text-slate-700 hover:text-primary">Marketplace</a>
                        <a href="<?= url('/login') ?>" class="text-slate-700 hover:text-primary">Login</a>
                        <a href="<?= url('/register') ?>" class="bg-primary text-white px-4 py-2 rounded">Create account</a>
                    <?php else: ?>
                        <a href="<?= url('/dashboard') ?>" class="text-slate-700 hover:text-primary">Dashboard</a>
                        <a href="<?= url('/services') ?>" class="text-slate-700 hover:text-primary">Buy Number</a>
                        <a href="<?= url('/boosting') ?>" class="text-slate-700 hover:text-primary">Boosting</a>
                        <a href="<?= url('/accounts') ?>" class="text-slate-700 hover:text-primary">Marketplace</a>
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
                <div class="w-full px-4 py-3 space-y-2">
                    <?php if (!$user): ?>
                        <a href="<?= url('/services') ?>" class="block text-slate-700">Services</a>
                        <a href="<?= url('/accounts') ?>" class="block text-slate-700">Marketplace</a>
                        <a href="<?= url('/login') ?>" class="block text-slate-700">Login</a>
                        <a href="<?= url('/register') ?>" class="block text-primary font-semibold">Create account</a>
                    <?php else: ?>
                        <?php if ($user['role'] === 'admin'): ?>
                            <a href="<?= url('/admin') ?>" class="block text-slate-700">Admin</a>
                        <?php endif; ?>
                        <a href="<?= url('/accounts') ?>" class="block text-slate-700">Marketplace</a>
                        <a href="<?= url('/accounts/sell') ?>" class="block text-slate-700">Sell Accounts</a>
                        <a href="<?= url('/accounts/purchases') ?>" class="block text-slate-700">Purchased Accounts</a>
                        <a href="<?= url('/accounts/withdrawals') ?>" class="block text-slate-700">Withdrawals</a>
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
        <div class="w-full mt-4 px-4">
            <div class="bg-red-50 text-danger border border-red-200 px-4 py-3 rounded">
                <?= htmlspecialchars($flashError) ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($flashSuccess): ?>
        <div class="w-full mt-4 px-4">
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
                ['href' => '/accounts', 'label' => 'Marketplace', 'icon' => 'store'],
                ['href' => '/accounts/sell', 'label' => 'Sell Accounts', 'icon' => 'badge-dollar-sign'],
                ['href' => '/accounts/purchases', 'label' => 'Purchased', 'icon' => 'shopping-bag'],
                ['href' => '/accounts/withdrawals', 'label' => 'Withdrawals', 'icon' => 'banknote'],
                ['href' => '/wallet', 'label' => 'Wallet', 'icon' => 'wallet'],
                ['href' => '/profile', 'label' => 'Profile', 'icon' => 'user'],
                ['href' => '/help', 'label' => 'Help Center', 'icon' => 'life-buoy'],
            ];
        ?>
        <div class="flex h-screen bg-slate-50/60 border border-slate-200 rounded-xl overflow-hidden w-full backdrop-blur-xl" style="width:100vw;">
            <aside class="hidden md:flex w-72 bg-white/60 border-r border-slate-200 flex-col h-full overflow-y-auto backdrop-blur-xl">
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
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                    <?php foreach ($userNavItems as $item): ?>
                        <?php $isActive = $currentPath === $item['href']; ?>
                        <a href="<?= url($item['href']) ?>" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-sm font-medium <?= $isActive ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                            <i data-lucide="<?= htmlspecialchars($item['icon']) ?>" class="w-4 h-4"></i>
                            <span><?= htmlspecialchars($item['label']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>
                <form method="post" action="<?= url('/logout') ?>" class="p-4 border-t border-slate-200 bg-white mb-24">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <button class="w-full bg-danger text-white px-4 py-2 rounded inline-flex items-center justify-center space-x-2">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </aside>
            <div class="flex-1 min-h-0 overflow-hidden flex flex-col">
                <div class="sticky top-0 z-20 bg-white/90 backdrop-blur border-b border-slate-200">
                    <div class="px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <button id="userMenuToggle" class="md:hidden border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                <i data-lucide="menu" class="w-4 h-4"></i>
                            </button>
                            <div class="flex items-center gap-2">
                                <?php $logo = setting('logo_path'); ?>
                                <?php if ($logo): ?>
                                    <img src="<?= htmlspecialchars(url($logo)) ?>" alt="Logo" class="h-8">
                                <?php else: ?>
                                    <span class="font-semibold text-slate-800">GetSMS</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="<?= url('/help') ?>" class="p-2 rounded-lg border border-slate-200 bg-white">
                                <i data-lucide="bell" class="w-4 h-4"></i>
                            </a>
                            <a href="<?= url('/profile') ?>" class="w-9 h-9 rounded-full bg-slate-100 overflow-hidden flex items-center justify-center">
                                <?php if (!empty(current_user()['profile_image'])): ?>
                                    <img src="<?= htmlspecialchars(url(current_user()['profile_image'])) ?>" alt="Profile" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <span class="text-slate-400 text-sm"><?= strtoupper(substr(current_user()['name'] ?? 'U', 0, 1)) ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                </div>
                <main class="flex-1 min-h-0 overflow-y-auto px-4 py-6 md:py-8 pb-28 md:pb-8">
                    <?php if (file_exists($viewFile)) { require $viewFile; } else { echo '<p>View not found.</p>'; } ?>
                </main>
            </div>
        </div>

        <div id="userMobileMenu" class="md:hidden fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-black/40" data-close="userMobileMenu"></div>
            <div class="absolute top-0 left-0 h-full w-72 bg-white border-r border-slate-200 flex flex-col">
                <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <?php $logo = setting('logo_path'); ?>
                        <?php if ($logo): ?>
                            <img src="<?= htmlspecialchars(url($logo)) ?>" alt="Logo" class="h-8">
                        <?php else: ?>
                            <span class="font-semibold text-slate-800">GetSMS</span>
                        <?php endif; ?>
                    </div>
                    <button id="userMenuClose" class="border border-slate-200 rounded px-2 py-1 text-xs">Close</button>
                </div>
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                    <?php foreach ($userNavItems as $item): ?>
                        <?php $isActive = $currentPath === $item['href']; ?>
                        <a href="<?= url($item['href']) ?>" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-sm font-medium <?= $isActive ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                            <i data-lucide="<?= htmlspecialchars($item['icon']) ?>" class="w-4 h-4"></i>
                            <span><?= htmlspecialchars($item['label']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>
                <form method="post" action="<?= url('/logout') ?>" class="p-4 border-t border-slate-200 bg-white">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <button class="w-full bg-danger text-white px-4 py-2 rounded inline-flex items-center justify-center space-x-2">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
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
                        ['href' => '/accounts', 'label' => 'Market', 'icon' => 'store'],
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

    <?php $hideFooter = ($currentPath === '/dashboard' || str_starts_with($currentPath, '/admin')); ?>
    <?php if (!$hideFooter): ?>
        <footer class="mt-10 border-t border-slate-200 bg-white/70 backdrop-blur">
            <div class="max-w-6xl mx-auto px-4 py-10">
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <?php $logo = setting('logo_path'); ?>
                            <?php if ($logo): ?>
                                <img src="<?= htmlspecialchars(url($logo)) ?>" alt="Logo" class="h-9">
                            <?php endif; ?>
                            <span class="text-sm text-slate-500">Secure digital services.</span>
                        </div>
                        <p class="text-sm text-slate-600">Connect, purchase, and manage services with confidence.</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold">Legal</h4>
                        <ul class="mt-3 space-y-2 text-sm text-slate-600">
                            <li><a href="<?= url('/terms') ?>" class="hover:text-primary">Terms &amp; Conditions</a></li>
                            <li><a href="<?= url('/privacy') ?>" class="hover:text-primary">Privacy Policy</a></li>
                            <li><a href="<?= url('/returns') ?>" class="hover:text-primary">Return Policy</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold">Connect</h4>
                        <div class="mt-3 flex items-center gap-3">
                            <a href="#" class="glass-panel p-2 text-slate-700 hover:text-primary" aria-label="Twitter">
                                <i data-lucide="twitter" class="w-4 h-4"></i>
                            </a>
                            <a href="#" class="glass-panel p-2 text-slate-700 hover:text-primary" aria-label="Facebook">
                                <i data-lucide="facebook" class="w-4 h-4"></i>
                            </a>
                            <a href="#" class="glass-panel p-2 text-slate-700 hover:text-primary" aria-label="Instagram">
                                <i data-lucide="instagram" class="w-4 h-4"></i>
                            </a>
                            <a href="#" class="glass-panel p-2 text-slate-700 hover:text-primary" aria-label="LinkedIn">
                                <i data-lucide="linkedin" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="mt-8 text-xs text-slate-500">© <?= date('Y') ?> GetSMS. All rights reserved.</div>
            </div>
        </footer>
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

        const userMenuToggle = document.getElementById('userMenuToggle');
        const userMenuClose = document.getElementById('userMenuClose');
        const userMobileMenu = document.getElementById('userMobileMenu');
        if (userMenuToggle && userMobileMenu) {
            userMenuToggle.addEventListener('click', () => {
                userMobileMenu.classList.remove('hidden');
            });
        }
        if (userMenuClose && userMobileMenu) {
            userMenuClose.addEventListener('click', () => {
                userMobileMenu.classList.add('hidden');
            });
        }
        if (userMobileMenu) {
            userMobileMenu.addEventListener('click', (event) => {
                const target = event.target;
                if (target && target.dataset && target.dataset.close === 'userMobileMenu') {
                    userMobileMenu.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>
