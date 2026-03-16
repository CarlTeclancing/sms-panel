<!-- <style>
    body {
        background: 
            radial-gradient(62.5% 17.09% at 20% 20%, rgba(20, 110, 245, 0.18) 0%, rgba(20, 110, 245, 0) 100%),
            radial-gradient(52.08% 14.25% at 80% 0%, rgba(122, 61, 255, 0.16) 0%, rgba(122, 61, 255, 0) 100%),
            radial-gradient(46.87% 12.82% at 50% 100%, rgba(237, 82, 203, 0.12) 0%, rgba(237, 82, 203, 0) 100%),

            #F0F0F0 !important;
        background-attachment: fixed !important;
    }
</style> -->
<section class="flex justify-center pb-12 items-center reveal">
    <div class="fade-in-up">
        <div class="inline-flex items-center gap-2 bg-white border border-slate-200 rounded-full px-4 py-2 text-xs text-slate-600">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            Cheaper. Faster. Global.
        </div>
        <div class="flex justify-center items-center text-center flex-col">
            <h1 class="text-4xl md:text-5xl font-bold text-slate-900 mt-4">Get verified, stay connected, &amp; grow your socials instantly!</h1>
        <p class="mt-4 text-slate-600 text-lg">Rent numbers, receive SMS codes, get global eSIMs, and boost your social media quick, secure, and hassle-free.</p>
        <div class="flex flex-wrap gap-3 my-16">
            <a href="<?= url('/register') ?>" class="bg-primary text-white px-6 py-3 rounded soft-btn">Get started</a>
            <a href="<?= url('/services') ?>" class="border border-slate-300 text-slate-700 px-6 py-3 rounded soft-btn">Explore services</a>
        </div>
        </div>
         
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 justify-center items-center ">
            <div class="card flex flex-col justify-center gap-4 p-2 w-full sm:w-[218px] h-32 fade-in-up fade-in-delay-1">
                <div class="flex justify-between items-center">
                   <p class="text-xs text-slate-500">Active Users</p>
                   <i data-lucide="users" class="w-10 h-8 text-primary"></i>
                </div>
                 <p class="text-2xl font-semibold mt-1" data-counter data-target="10000" data-suffix="+">0</p>
                
            </div>
            <div class="card flex flex-col justify-center gap-4 p-2 w-full sm:w-[218px] h-32 fade-in-up fade-in-delay-2">
                <div class="flex justify-between items-center">
                  <p class="text-xs text-slate-500">Engagement</p>
                 <i data-lucide="messages-square" class="w-10 h-8 text-primary"></i>
                </div>
                 
                  <p class="text-2xl font-semibold mt-1">Cheapest &amp; Fastest</p>
            </div>
            <div class="card flex flex-col justify-center gap-4 p-2 w-full sm:w-[218px] h-32 fade-in-up fade-in-delay-3">
                <div class="flex justify-between items-center">
                  <p class="text-xs text-slate-500">Coverage</p>
                   <i data-lucide="globe" class="w-10 h-8 text-primary"></i>
                </div>
               
                <p class="text-2xl font-semibold mt-1" data-counter data-target="10" data-suffix="+">0</p>
            </div> 
            <div class="card flex flex-col justify-center gap-4 p-2 w-full sm:w-[218px] h-32 fade-in-up fade-in-delay-4 ">
                <div class="flex justify-between items-center">
                <p class="text-xs text-slate-500">Avg delivery time</p>
                <i data-lucide="clock" class="w-10 h-8 text-primary"></i>
            </div>
              
            <p class="text-2xl font-semibold mt-1">&lt; 30s</p>
        </div>
        </div>
    </div>
</section>
<section class="py-14 md:py-24  grid lg:grid-cols-2 gap-12 items-center reveal">
   <img src="<?= url('/uploads/vectorfast.png') ?>" class="w-full  object-cover">


     <div class="card p-6 shadow-xl">
        <h3 class="text-xl font-semibold">Cheapest and Fastest Engagements &amp; Online SMS Verification</h3>
        <p class="text-slate-600 mt-3">All the tools you need to verify, connect, and grow—built for speed and trust.</p>
        <div class="mt-6 grid sm:grid-cols-2 gap-4 text-sm">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                    <i data-lucide="zap" class="w-5 h-5 text-primary"></i>
                </div>
                <div>
                    <p class="font-semibold">Instant delivery</p>
                    <p class="text-slate-600">Fast OTPs and engagements.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                    <i data-lucide="refresh-ccw" class="w-5 h-5 text-primary"></i>
                </div>
                <div>
                    <p class="font-semibold">Refund protection</p>
                    <p class="text-slate-600">Unused numbers are refunded.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                    <i data-lucide="globe-2" class="w-5 h-5 text-primary"></i>
                </div>
                <div>
                    <p class="font-semibold">Global coverage</p>
                    <p class="text-slate-600">Choose from 10+ countries.</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                    <i data-lucide="lock" class="w-5 h-5 text-primary"></i>
                </div>
                <div>
                    <p class="font-semibold">Secure payments</p>
                    <p class="text-slate-600">Trusted checkout flow.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="py-14 md:py-24 grid lg:grid-cols-2 gap-6 items-center reveal">
    <div class="card p-6 shadow-xl">
        <h3 class="text-xl font-semibold">Plus 10 countries and numbers for different services</h3>
        <p class="text-slate-600 mt-3">Access a wide selection of countries for verification and eSIM services.</p>
        <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-slate-600">
            <div class="flex items-center gap-2"><span><?= getFlag('US') ?></span> United States</div>
            <div class="flex items-center gap-2"><span><?= getFlag('GB') ?></span> United Kingdom</div>
            <div class="flex items-center gap-2"><span><?= getFlag('CM') ?></span> Cameroon</div>
            <div class="flex items-center gap-2"><span><?= getFlag('NG') ?></span> Nigeria</div>

            <div class="flex items-center gap-2"><span><?= getFlag('FR') ?></span> France</div>
            <div class="flex items-center gap-2"><span><?= getFlag('DE') ?></span> Germany</div>
            <div class="flex items-center gap-2"><span><?= getFlag('CN') ?></span> China</div>
            <div class="flex items-center gap-2"><span><?= getFlag('IN') ?></span> India</div>
            <div class="flex items-center gap-2"><span><?= getFlag('CA') ?></span> Canada</div>
            <div class="flex items-center gap-2"><span><?= getFlag('RU') ?></span> Russia</div>
        </div>
    </div>
   <img src="<?= url('/uploads/vectorinternet.png') ?>" class="w-full  object-cover">

</section>
<section class="reveal py-14 md:py-24 rid lg:grid-cols-2 gap-6 items-center">
    <h1 class="ext-4xl md:text-5xl font-bold text-slate-900 mt-4 text-center py-4">
        Everything You Need: Secure Numbers, eSIMs & Social Scaling
    </h1>'
    <div class="flex justify-center items-center">
 <div class="grid lg:grid-cols-2 gap-4 items-center">
        <div class=" card p-6 reveal">
            <div class="flex justify-between items-center mb-4">
                <div class="flex flex-col">
                    <h3 class="text-xl font-semibold">Sell Accounts</h3>
                    <span class="text-sm text-slate-500">Top 20 Number Services</span>
                </div>
               <img src="<?= url('/uploads/flag.png') ?>" class="w-1/4  object-cover">
            </div>
       
         <div id="homeServices" class="mt-4 grid  gap-4"></div>
      </div>
      <img src="<?= url('/uploads/boost-social-media.png') ?>" class="w-full h-full  object-cover">
   </div>
    
    </div>
  
</section>

<section class="mt-12 reveal">
    <div class="flex items-center justify-between">
        <h3 class="text-xl font-semibold">Top SMM Services</h3>
        <span class="text-sm text-slate-500">Growth essentials</span>
    </div>
    <div class="mt-4 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php if (!empty($smmServices)): ?>
            <?php foreach ($smmServices as $smm): ?>
                <div class="card p-4">
                    <div class="flex items-center gap-2 text-sm text-slate-600">
                        <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center">
                            <i data-lucide="sparkles" class="w-4 h-4 text-primary"></i>
                        </div>
                        <span><?= htmlspecialchars($smm['category'] ?? 'Social') ?></span>
                    </div>
                    <h4 class="font-semibold mt-3"><?= htmlspecialchars($smm['name']) ?></h4>
                    <p class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($smm['type'] ?? 'Standard') ?></p>
                    <p class="mt-3 font-semibold text-primary">XAF <?= format_xaf((float)($smm['display_rate'] ?? $smm['rate']), 4) ?> / 1k</p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card p-4 text-slate-500">No SMM services available.</div>
        <?php endif; ?>
    </div>
</section>
<section class="reveal py-14 md:py-24 ">
    <h3 class="text-2xl font-semibold text-center mb-16 text-slate-900">How It Works</h3>
    
    <div class="relative max-w-6xl mx-auto px-6">
        <div class="absolute inset-0 flex justify-center">
            <div class="w-1 h-full bg-blue-500 hidden lg:block z-0"></div>
        </div>
        
        <div class="space-y-24 relative z-10">
        
            <div class="flex flex-col lg:flex-row items-start gap-12 fade-in-up fade-in-delay-1 relative">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 hidden lg:block z-20">
                    <div class="w-12 h-12 bg-blue-600 text-white flex items-center justify-center rounded-2xl font-bold text-lg shadow-2xl border-4 border-white">1</div>
                </div>
                
                <div class="lg:w-1/2 lg:pr-8">
                    <h4 class="text-xl font-semibold text-slate-900 mb-4">Sign Up</h4>
                    <p class="text-slate-600 mb-6 leading-relaxed">Number ServiceStart by signing up with your email, and other necessary details.</p>
                </div>
                
                <div class="lg:w-1/2 lg:pl-8">
                    <div class="">
                        <img src="<?= url('/uploads/SighnUp.png') ?>" alt="Create Account" class="w-full object-cover ">
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row-reverse items-start gap-12 fade-in-up fade-in-delay-2 relative">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 hidden lg:block z-20">
                    <div class="w-12 h-12 bg-blue-600 text-white flex items-center justify-center rounded-2xl font-bold text-lg shadow-2xl border-4 border-white">2</div>
                </div>
                
             
                <div class="lg:w-1/2 lg:pl-8">
                    <h4 class="text-xl font-semibold text-slate-900 mb-4">Fund Your Wallet</h4>
                    <p class="text-slate-600 mb-6 leading-relaxed">Easily add funds using credit/debit cards, PayPal, or cryptocurrency.</p>
                </div>
                
                <div class="lg:w-1/2 lg:pr-8">
                    <div class="">
                        <img src="<?= url('/uploads/Waller.png') ?>" alt="Fund Wallet" class="w-full object-cover">
                    </div>
                </div>
            </div>
            <div class="flex flex-col lg:flex-row items-start gap-12 fade-in-up fade-in-delay-3 relative">
    
                <div class="absolute top-0 left-1/2 -translate-x-1/2 hidden lg:block z-20">
                    <div class="w-12 h-12 bg-blue-600 text-white flex items-center justify-center rounded-2xl font-bold text-lg shadow-2xl border-4 border-white">3</div>
                </div>
                <div class="lg:w-1/2 lg:pr-8">
                    <h4 class="text-xl font-semibold text-slate-900 mb-4">Select the Service You Need</h4>
                    <p class="text-slate-600 mb-6 leading-relaxed">Choose from our range of services</p>
                </div>
                <div class="lg:w-1/2 lg:pl-8">
                    <div class="">
                        <img src="<?= url('/uploads/select-service.png') ?>" alt="Select Service" class="w-full object-cover ">
                    </div>
                </div>
            </div>
            <div class="flex flex-col lg:flex-row-reverse items-start gap-12 fade-in-up fade-in-delay-4 relative">
        
                <div class="absolute top-0 left-1/2 -translate-x-1/2 hidden lg:block z-20">
                    <div class="w-12 h-12 bg-blue-600 text-white flex items-center justify-center rounded-2xl font-bold text-lg shadow-2xl border-4 border-white">4</div>
                </div>
          
                <div class="lg:w-1/2 lg:pl-8">
                    <h4 class="text-xl font-semibold text-slate-900 mb-4">Manage & Track Your Services</h4>
                    <p class="text-slate-600 mb-6 leading-relaxed">Monitor your active services from a centralized dashboard.</p>
                </div>
                
         
                <div class="lg:w-1/2 lg:pr-8">
                    <div class="">
                        <img src="<?= url('/uploads/recent-purchasepng.png') ?>" alt="Track Services" class="w-full  object-cover ">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</section>

<section class="mt-12 grid lg:grid-cols-2 gap-6 reveal">
    <div class="card p-6">
        <h3 class="text-xl font-semibold">Common questions</h3>
        <p class="text-sm text-slate-500 mt-2">Find answers to frequently asked questions.</p>
    </div>
    <div class="space-y-3">
        <div class="card p-4">
            <p class="font-semibold">What is an SMM Panel?</p>
            <p class="text-sm text-slate-600 mt-2">An SMM panel provides social media services such as likes, follows, and engagement boosts.</p>
        </div>
        <div class="card p-4">
            <p class="font-semibold">What is SMS Service?</p>
            <p class="text-sm text-slate-600 mt-2">SMS services provide temporary numbers for receiving verification codes online.</p>
        </div>
        <div class="card p-4">
            <p class="font-semibold">What payment options do you accept?</p>
            <p class="text-sm text-slate-600 mt-2">You can fund your wallet using cards, PayPal, or crypto depending on availability.</p>
        </div>
        <div class="card p-4">
            <p class="font-semibold">What services does Panelsuite offer?</p>
            <p class="text-sm text-slate-600 mt-2"> offers SMS verification, eSIMs, and social media boosting in one platform.</p>
        </div>
    </div>
</section>

<script>
    const revealEls = document.querySelectorAll('.reveal');
    const counterEls = document.querySelectorAll('[data-counter]');

    const runCounters = (root) => {
        const counters = root.querySelectorAll('[data-counter]');
        counters.forEach(counter => {
            if (counter.dataset.ran === '1') {
                return;
            }
            counter.dataset.ran = '1';
            const target = Number(counter.dataset.target || '0');
            const suffix = counter.dataset.suffix || '';
            const duration = 1200;
            const start = performance.now();

            const tick = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                const value = Math.floor(progress * target);
                counter.textContent = value.toLocaleString() + suffix;
                if (progress < 1) {
                    requestAnimationFrame(tick);
                }
            };

            requestAnimationFrame(tick);
        });
    };

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal-visible');
                    runCounters(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });

        revealEls.forEach(el => observer.observe(el));
    } else {
        revealEls.forEach(el => el.classList.add('reveal-visible'));
        runCounters(document);
    }
</script>

<script>
    const servicesData = <?php echo json_encode($services ?? []); ?>;
    const list = document.getElementById('homeServices');

    const renderAll = () => {
        servicesData.forEach(service => {
            const card = document.createElement('div');
            card.className = 'card p-2 ';
            card.innerHTML = `
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <div class="w-8 h-8 rounded-xl bg-primary/10 flex items-center justify-center">
                        <i data-lucide="smartphone" class="w-3 h-3 text-primary"></i>
                    </div>
                    <span>Number Service</span>
                </div>
                <div class="flex justify-between items-start mt-1">
                <div>
                <h4 class="font-semibold mt-1">${service.name}</h4>
                <p class="text-xs text-slate-500">Code: ${service.code}</p>
                </div>
                <p class="mt-1 font-semibold text-primary">$${Number(service.display_price ?? service.price).toFixed(4)}</p>
               </div>
                
            `;
            list.appendChild(card);
        });
    };

    renderAll();
</script>
