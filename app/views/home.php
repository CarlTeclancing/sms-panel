<section class="grid md:grid-cols-2 gap-10 items-center">
    <div>
        <h1 class="text-4xl font-bold text-slate-900">Buy or rent mobile numbers for verification</h1>
        <p class="mt-4 text-slate-600">Get instant access to virtual numbers for popular services. Fund your wallet with MoMo or Orange Money, purchase numbers, and retrieve SMS verification codes fast.</p>
        <div class="mt-6 space-x-3">
            <a href="<?= url('/register') ?>" class="bg-primary text-white px-6 py-3 rounded">Get started</a>
            <a href="<?= url('/api/docs') ?>" class="border border-primary text-primary px-6 py-3 rounded">API Docs</a>
        </div>
    </div>
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-6">
        <h3 class="font-semibold text-lg">Why teams choose GetSMS</h3>
        <ul class="mt-4 space-y-3 text-slate-600">
            <li>Instant number allocation from SMS-Man.</li>
            <li>Real-time SMS retrieval and status tracking.</li>
            <li>Wallet-based payments for easy budgeting.</li>
            <li>Developer-friendly API for automation.</li>
        </ul>
    </div>
</section>

<section class="mt-10">
    <div class="flex items-center justify-between">
        <h3 class="text-xl font-semibold">Services</h3>
        <span class="text-sm text-slate-500">Loads 10 at a time</span>
    </div>
    <div id="homeServices" class="mt-4 grid sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>
    <div id="homeServicesSentinel" class="h-10"></div>
</section>

<script>
    const servicesData = <?php echo json_encode($services ?? []); ?>;
    const list = document.getElementById('homeServices');
    const sentinel = document.getElementById('homeServicesSentinel');
    let cursor = 0;
    const pageSize = 10;

    const renderNext = () => {
        const slice = servicesData.slice(cursor, cursor + pageSize);
        slice.forEach(service => {
            const card = document.createElement('div');
            card.className = 'border border-slate-200 rounded-lg p-4 bg-white';
            card.innerHTML = `
                <h4 class="font-semibold">${service.name}</h4>
                <p class="text-sm text-slate-500">Code: ${service.code}</p>
                <p class="mt-2 font-semibold text-primary">$${Number(service.price).toFixed(4)}</p>
            `;
            list.appendChild(card);
        });
        cursor += slice.length;
        if (cursor >= servicesData.length) {
            sentinel.remove();
        }
    };

    renderNext();

    if (sentinel && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    renderNext();
                }
            });
        }, { rootMargin: '100px' });
        observer.observe(sentinel);
    }
</script>
