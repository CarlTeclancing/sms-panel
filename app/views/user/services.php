<?php
$flagMap = [
    'Russia' => ['flag' => '🇷🇺', 'code' => '+7'],
    'China' => ['flag' => '🇨🇳', 'code' => '+86'],
    'United States' => ['flag' => '🇺🇸', 'code' => '+1'],
    'United Kingdom' => ['flag' => '🇬🇧', 'code' => '+44'],
    'France' => ['flag' => '🇫🇷', 'code' => '+33'],
    'Germany' => ['flag' => '🇩🇪', 'code' => '+49'],
    'Canada' => ['flag' => '🇨🇦', 'code' => '+1'],
    'India' => ['flag' => '🇮🇳', 'code' => '+91'],
    'Nigeria' => ['flag' => '🇳🇬', 'code' => '+234'],
    'Cameroon' => ['flag' => '🇨🇲', 'code' => '+237'],
];
?>

<div class="flex items-center justify-between">
    <h2 class="text-2xl font-semibold">Services</h2>
    <a href="<?= url('/wallet') ?>" class="text-primary">Refill wallet</a>
</div>

<div class="mt-3 bg-white border border-slate-200 rounded p-3">
    <label class="block text-xs font-medium text-slate-500">Search services</label>
    <input id="serviceSearchTop" type="text" placeholder="Search by service name..." class="mt-2 w-full border border-slate-300 rounded px-3 py-2 text-sm">
</div>

<div class="mt-4 bg-white border border-slate-200 rounded p-4">
    <h3 class="text-lg font-semibold">SMS Verification</h3>
    <p class="text-sm text-slate-500">Buy or rent numbers for verification.</p>
</div>

<div class="mt-4 bg-white border border-slate-200 rounded p-4">
    <div class="flex items-center justify-between md:hidden">
        <h4 class="text-sm font-semibold">Filters</h4>
        <button type="button" id="toggleServiceFilters" class="text-sm text-primary">Show</button>
    </div>
    <form method="get" action="<?= url('/services') ?>" id="serviceFilters" class="mt-3 md:mt-0 hidden md:block">
        <label class="block text-sm font-medium">Select country</label>
        <select name="country_id" class="mt-1 w-full border border-slate-300 rounded px-3 py-2" onchange="this.form.submit()">
            <option value="0" <?= $selectedCountryId === 0 ? 'selected' : '' ?>>🌐 Any country</option>
            <?php foreach ($countries as $country): ?>
                <?php
                    $title = $country['title'] ?? '';
                    $flag = $flagMap[$title]['flag'] ?? '🌐';
                    $code = $flagMap[$title]['code'] ?? '';
                ?>
                <option value="<?= (int)$country['id'] ?>" <?= $selectedCountryId === (int)$country['id'] ? 'selected' : '' ?>>
                    <?= $flag ?> <?= htmlspecialchars($title) ?> <?= $code ? '(' . $code . ')' : '' ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="text-xs text-slate-500 mt-2" id="priceRangeText">Live price range: loading...</p>
    </form>
</div>

<?php if ($user): ?>
<form method="post" action="<?= url('/purchase') ?>" class="mt-6 bg-slate-50 border border-slate-200 rounded p-6">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="country_id" value="<?= (int)$selectedCountryId ?>">
    <input type="hidden" name="service_id" id="serviceInput" value="">
    <input type="hidden" name="purchase_type" id="purchaseType" value="buy">

    <div>
        <label class="block text-sm font-medium">Service</label>
        <div class="mt-2">
            <input id="serviceSearch" type="text" placeholder="Search services..." class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
        </div>
        <div class="mt-3 border border-slate-200 rounded-lg p-3 h-64 overflow-y-auto bg-white" id="serviceScroll">
            <div class="space-y-4" id="serviceGroups">
                <div class="text-xs text-slate-500" id="servicesLoading">Loading services...</div>
            </div>
            <div class="mt-4 flex justify-center">
                <button type="button" id="loadMoreServices" class="border border-slate-300 px-4 py-2 rounded text-sm">Load more</button>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <label class="block text-sm font-medium">Type</label>
        <div class="mt-2 flex flex-wrap gap-2" id="typeButtons">
            <button type="button" class="type-btn border border-slate-300 rounded-full px-4 py-2 text-sm" data-type="buy">Buy (single SMS)</button>
            <button type="button" class="type-btn border border-slate-300 rounded-full px-4 py-2 text-sm" data-type="rent">Rent (multiple SMS)</button>
        </div>
    </div>
    <div id="rentalOptions" class="mt-4 hidden">
        <label class="block text-sm font-medium">Rental duration (hours)</label>
        <select name="rental_hours" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
            <option value="1">1 hour</option>
            <option value="3">3 hours</option>
            <option value="6">6 hours</option>
            <option value="12">12 hours</option>
            <option value="24">24 hours</option>
        </select>
    </div>
    <button class="mt-4 bg-primary text-white px-6 py-2 rounded">Get number</button>
</form>
<?php else: ?>
<div class="mt-6 bg-slate-50 border border-slate-200 rounded p-6 text-center">
    <p class="mb-4 text-slate-600">You must <a href="<?= url('/login') ?>" class="text-primary underline">login</a> to buy or rent a number.</p>
    <a href="<?= url('/login') ?>" class="bg-primary text-white px-6 py-2 rounded">Login to buy</a>
</div>
<?php endif; ?>

<div class="mt-8">
    <h3 class="text-lg font-semibold">Available services</h3>
    <div id="availableServices" class="mt-3 grid md:grid-cols-3 gap-4"></div>
    <div class="mt-4">
        <button type="button" id="loadMoreAvailable" class="border border-slate-300 px-4 py-2 rounded text-sm">Load more</button>
    </div>
</div>

<div class="mt-10 bg-white border border-slate-200 rounded p-4">
    <h3 class="text-lg font-semibold">Social Media Boosting</h3>
    <p class="text-sm text-slate-500">Place social media engagement orders via Peakerr.</p>
</div>
<?php if ($user): ?>
<form method="post" action="<?= url('/boost/order') ?>" class="mt-4 bg-slate-50 border border-slate-200 rounded p-6">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Service</label>
            <select name="boost_service_id" id="boostServiceSelect" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
                <?php if (empty($boostingServices)): ?>
                    <option value="">No boosting services available</option>
                <?php else: ?>
                    <?php
                        $currentCategory = null;
                        foreach ($boostingServices as $boostService):
                            $category = $boostService['category'] ?? 'Other';
                            if ($category !== $currentCategory):
                                if ($currentCategory !== null) {
                                    echo '</optgroup>';
                                }
                                $currentCategory = $category;
                                echo '<optgroup label="' . htmlspecialchars($currentCategory) . '">';
                            endif;
                    ?>
                        <option
                            value="<?= (int)$boostService['id'] ?>"
                            data-rate="<?= htmlspecialchars($boostService['display_rate'] ?? $boostService['rate']) ?>"
                            data-min="<?= (int)$boostService['min_qty'] ?>"
                            data-max="<?= (int)$boostService['max_qty'] ?>"
                            data-type="<?= htmlspecialchars($boostService['type'] ?? '') ?>"
                        >
                            <?= htmlspecialchars($boostService['name']) ?>
                        </option>
                    <?php endforeach; if ($currentCategory !== null) { echo '</optgroup>'; } ?>
                <?php endif; ?>
            </select>
            <p class="text-xs text-slate-500 mt-2" id="boostServiceMeta"></p>
        </div>
        <div>
            <label class="block text-sm font-medium">Link</label>
            <input name="link" type="url" required placeholder="https://..." class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Quantity</label>
            <input name="quantity" type="number" min="1" required class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Runs (optional)</label>
            <input name="runs" type="number" min="1" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Interval in minutes (optional)</label>
            <input name="interval" type="number" min="1" class="mt-1 w-full border border-slate-300 rounded px-3 py-2">
        </div>
    </div>
    <button class="mt-4 bg-primary text-white px-6 py-2 rounded" <?= empty($boostingServices) ? 'disabled' : '' ?>>Place boosting order</button>
</form>
<?php else: ?>
<div class="mt-4 bg-slate-50 border border-slate-200 rounded p-6 text-center">
    <p class="mb-4 text-slate-600">You must <a href="<?= url('/login') ?>" class="text-primary underline">login</a> to place boosting orders.</p>
    <a href="<?= url('/login') ?>" class="bg-primary text-white px-6 py-2 rounded">Login to boost</a>
</div>
<?php endif; ?>

<script>
    const servicesDataUrl = "<?= url('/services/data') ?>";
    const selectedCountryId = <?= (int)$selectedCountryId ?>;
    const purchaseType = document.getElementById('purchaseType');
    const rentalOptions = document.getElementById('rentalOptions');
    const typeButtons = document.querySelectorAll('.type-btn');
    const serviceInput = document.getElementById('serviceInput');
    const serviceSearch = document.getElementById('serviceSearch');
    const serviceSearchTop = document.getElementById('serviceSearchTop');
    const loadMoreBtn = document.getElementById('loadMoreServices');
    const serviceGroups = document.getElementById('serviceGroups');
    let servicesLoading = document.getElementById('servicesLoading');
    const priceRangeText = document.getElementById('priceRangeText');

    let serviceButtons = [];
    let servicesData = [];
    let totalServices = 0;
    let serviceOffset = 0;
    let visibleCount = 10;
    let isLoadingServices = false;
    let currentSearch = '';

    const refreshServiceButtons = () => {
        serviceButtons = Array.from(document.querySelectorAll('.service-btn'));
    };

    const getServiceQuery = () => {
        return (serviceSearch?.value || serviceSearchTop?.value || '').toLowerCase();
    };

    const filteredCount = () => {
        const query = getServiceQuery();
        let total = 0;
        serviceButtons.forEach(btn => {
            const name = (btn.dataset.serviceName || '').toLowerCase();
            if (!query || name.includes(query)) {
                total += 1;
            }
        });
        return total;
    };

    const updateServiceVisibility = () => {
        const query = getServiceQuery();
        let shown = 0;
        const groups = document.querySelectorAll('.service-group');

        groups.forEach(group => {
            let groupHasVisible = false;
            const buttons = group.querySelectorAll('.service-btn');
            buttons.forEach(btn => {
                const name = (btn.dataset.serviceName || '').toLowerCase();
                const matches = !query || name.includes(query);
                const canShow = matches && shown < visibleCount;
                btn.classList.toggle('hidden', !canShow);
                if (canShow) {
                    shown += 1;
                    groupHasVisible = true;
                }
            });
            group.classList.toggle('hidden', !groupHasVisible);
        });

        if (loadMoreBtn) {
            loadMoreBtn.classList.toggle('hidden', shown >= filteredCount() && serviceOffset >= totalServices);
        }
    };

    const setActiveType = (type) => {
        purchaseType.value = type;
        typeButtons.forEach(btn => {
            const active = btn.dataset.type === type;
            btn.classList.toggle('bg-primary', active);
            btn.classList.toggle('text-white', active);
            btn.classList.toggle('border-primary', active);
        });
        if (rentalOptions) {
            rentalOptions.classList.toggle('hidden', type !== 'rent');
        }
    };

    typeButtons.forEach(btn => {
        btn.addEventListener('click', () => setActiveType(btn.dataset.type));
    });

    const setActiveService = (id) => {
        serviceInput.value = id;
        serviceButtons.forEach(btn => {
            const active = btn.dataset.serviceId === id;
            btn.classList.toggle('bg-primary', active);
            btn.classList.toggle('text-white', active);
            btn.classList.toggle('border-primary', active);
        });
    };

    const attachServiceButtonHandlers = () => {
        serviceButtons.forEach(btn => {
            if (btn.dataset.bound === '1') {
                return;
            }
            btn.dataset.bound = '1';
            btn.addEventListener('click', () => {
                if (btn.dataset.disabled === '1') {
                    return;
                }
                setActiveService(btn.dataset.serviceId);
            });
        });
    };

    const ensureGroup = (groupKey) => {
        let group = serviceGroups.querySelector(`.service-group[data-group="${groupKey}"]`);
        if (!group) {
            group = document.createElement('div');
            group.className = 'service-group';
            group.dataset.group = groupKey;
            group.innerHTML = `
                <p class="text-xs font-semibold text-slate-500 mb-2">${groupKey}</p>
                <div class="flex flex-wrap gap-2" data-group-buttons></div>
            `;
            serviceGroups.appendChild(group);
        }
        return group;
    };

    const renderServiceBatch = (batch) => {
        if (servicesLoading) {
            servicesLoading.remove();
            servicesLoading = null;
        }
        batch.forEach(service => {
            const name = service.name || '';
            const groupKey = name ? name.trim().charAt(0).toUpperCase() : '#';
            const group = ensureGroup(groupKey);
            const buttonsContainer = group.querySelector('[data-group-buttons]');
            const disabled = service.availability !== null && Number(service.availability) <= 0;
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `service-btn border border-slate-300 rounded-full px-4 py-2 text-sm ${disabled ? 'opacity-50 cursor-not-allowed' : 'hover:border-primary'}`;
            button.dataset.serviceId = String(service.id ?? '');
            button.dataset.serviceName = String(service.name ?? '');
            button.dataset.disabled = disabled ? '1' : '0';
            button.textContent = `${service.name} · XAF ${Number(service.display_price ?? service.price ?? 0).toFixed(4)}`;
            buttonsContainer.appendChild(button);
        });
        refreshServiceButtons();
        attachServiceButtonHandlers();

        if (!serviceInput.value) {
            const firstEnabled = serviceButtons.find(btn => btn.dataset.disabled === '0');
            if (firstEnabled) {
                setActiveService(firstEnabled.dataset.serviceId);
            }
        }
    };

    const fetchServices = async (reset = false) => {
        if (isLoadingServices) {
            return;
        }
        isLoadingServices = true;

        if (reset) {
            serviceOffset = 0;
            visibleCount = 10;
            servicesData = [];
            totalServices = 0;
            serviceGroups.innerHTML = '<div class="text-xs text-slate-500" id="servicesLoading">Loading services...</div>';
            servicesLoading = document.getElementById('servicesLoading');
            if (availableServicesEl) {
                availableServicesEl.innerHTML = '';
            }
            availableCursor = 0;
        }

        const params = new URLSearchParams({
            country_id: String(selectedCountryId),
            offset: String(serviceOffset),
            limit: String(200),
        });
        if (currentSearch) {
            params.set('search', currentSearch);
        }

        try {
            const response = await fetch(`${servicesDataUrl}?${params.toString()}`);
            if (!response.ok) {
                throw new Error('Failed to load services');
            }
            const data = await response.json();
            const batch = Array.isArray(data.services) ? data.services : [];
            totalServices = Number(data.total || 0);

            if (reset && priceRangeText) {
                if (data.price_range && data.price_range.min !== undefined && data.price_range.max !== undefined) {
                    priceRangeText.textContent = `Live price range: XAF ${Number(data.price_range.min).toFixed(4)} - XAF ${Number(data.price_range.max).toFixed(4)}`;
                } else {
                    priceRangeText.textContent = 'Live price range: unavailable';
                }
            }

            servicesData = servicesData.concat(batch);
            renderServiceBatch(batch);
            serviceOffset += batch.length;
            updateServiceVisibility();
        } catch (error) {
            if (priceRangeText && reset) {
                priceRangeText.textContent = 'Live price range: unavailable';
            }
            if (servicesLoading) {
                servicesLoading.textContent = 'Failed to load services.';
            }
        } finally {
            isLoadingServices = false;
        }
    };

    const handleServiceSearch = (event) => {
        if (event && event.target === serviceSearch && serviceSearchTop) {
            serviceSearchTop.value = serviceSearch.value;
        }
        if (event && event.target === serviceSearchTop && serviceSearch) {
            serviceSearch.value = serviceSearchTop.value;
        }
        currentSearch = getServiceQuery();
        fetchServices(true);
    };

    if (serviceSearch) {
        serviceSearch.addEventListener('input', handleServiceSearch);
    }
    if (serviceSearchTop) {
        serviceSearchTop.addEventListener('input', handleServiceSearch);
    }

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', async () => {
            visibleCount += 10;
            if (visibleCount > serviceButtons.length && serviceOffset < totalServices) {
                await fetchServices(false);
            }
            updateServiceVisibility();
        });
    }

    const availableServicesEl = document.getElementById('availableServices');
    const loadMoreAvailable = document.getElementById('loadMoreAvailable');
    let availableCursor = 0;

    const renderAvailable = () => {
        const slice = servicesData.slice(availableCursor, availableCursor + 10);
        slice.forEach(service => {
            const card = document.createElement('div');
            card.className = 'border border-slate-200 rounded p-4 bg-white';
            card.innerHTML = `
                <h4 class="font-semibold">${service.name}</h4>
                <p class="text-sm text-slate-500">Code: ${service.code}</p>
                <p class="mt-2 font-semibold text-primary">XAF ${Number(service.display_price ?? service.price).toFixed(4)}</p>
            `;
            availableServicesEl.appendChild(card);
        });
        availableCursor += slice.length;
        if (availableCursor >= servicesData.length && serviceOffset >= totalServices) {
            loadMoreAvailable.classList.add('hidden');
        } else {
            loadMoreAvailable.classList.remove('hidden');
        }
    };

    if (loadMoreAvailable) {
        loadMoreAvailable.addEventListener('click', async () => {
            if (availableCursor >= servicesData.length && serviceOffset < totalServices) {
                await fetchServices(false);
            }
            renderAvailable();
        });
    }

    const boostServiceSelect = document.getElementById('boostServiceSelect');
    const boostServiceMeta = document.getElementById('boostServiceMeta');

    const updateBoostMeta = () => {
        if (!boostServiceSelect || !boostServiceMeta) {
            return;
        }
        const option = boostServiceSelect.options[boostServiceSelect.selectedIndex];
        if (!option || !option.dataset) {
            boostServiceMeta.textContent = '';
            return;
        }
        const rate = option.dataset.rate ? Number(option.dataset.rate).toFixed(4) : '-';
        const min = option.dataset.min || '-';
        const max = option.dataset.max || '-';
        const type = option.dataset.type || '';
        boostServiceMeta.textContent = `Type: ${type} · Rate: XAF ${rate} per 1000 · Min: ${min} · Max: ${max}`;
    };

    if (boostServiceSelect) {
        boostServiceSelect.addEventListener('change', updateBoostMeta);
        updateBoostMeta();
    }

    fetchServices(true).then(() => {
        renderAvailable();
        updateServiceVisibility();
        setActiveType('buy');
    });

    const toggleServiceFilters = document.getElementById('toggleServiceFilters');
    const serviceFilters = document.getElementById('serviceFilters');
    if (toggleServiceFilters && serviceFilters) {
        toggleServiceFilters.addEventListener('click', () => {
            const isHidden = serviceFilters.classList.contains('hidden');
            serviceFilters.classList.toggle('hidden', !isHidden ? true : false);
            toggleServiceFilters.textContent = isHidden ? 'Hide' : 'Show';
        });
    }
</script>
