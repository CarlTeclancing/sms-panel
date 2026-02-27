<div class="flex items-center justify-between">
    <h2 class="text-2xl font-semibold">Social Media Boosting</h2>
    <a href="<?= url('/wallet') ?>" class="text-primary">Refill wallet</a>
</div>

<div class="mt-4 bg-white border border-slate-200 rounded p-4">
    <h3 class="text-lg font-semibold">Boosting Services</h3>
    <p class="text-sm text-slate-500">Place social media engagement orders via Peakerr.</p>
</div>

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

<script>
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
        boostServiceMeta.textContent = `Type: ${type} · Rate: $${rate} per 1000 · Min: ${min} · Max: ${max}`;
    };

    if (boostServiceSelect) {
        boostServiceSelect.addEventListener('change', updateBoostMeta);
        updateBoostMeta();
    }
</script>
