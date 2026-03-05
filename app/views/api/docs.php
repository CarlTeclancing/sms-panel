<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-semibold">GetSMS API</h2>
        <p class="text-slate-600 mt-2">Use your API token to integrate with GetSMS for automated number purchases.</p>
        <p class="text-sm text-slate-500 mt-2">Base URL: <span class="font-mono"><?= htmlspecialchars(rtrim(app_config()['app']['base_url'], '/')) ?></span></p>
    </div>

    <div class="bg-slate-50 border border-slate-200 rounded p-4">
        <h3 class="font-semibold">Authentication</h3>
        <p class="text-sm text-slate-600 mt-2">Generate your token on the dashboard, then pass it as <span class="font-mono">token</span> query or body parameter.</p>
        <p class="text-xs text-slate-500 mt-2">All responses are JSON. Currency values are returned in XAF.</p>
    </div>

    <div class="bg-slate-50 border border-slate-200 rounded p-4">
        <h3 class="font-semibold">Common parameters</h3>
        <ul class="mt-2 text-sm text-slate-600 list-disc list-inside space-y-1">
            <li><span class="font-mono">token</span> (required) – API token from dashboard.</li>
            <li><span class="font-mono">country_id</span> (required for purchases) – numeric country ID.</li>
            <li><span class="font-mono">service_id</span> (required for purchases) – numeric service ID.</li>
            <li><span class="font-mono">purchase_type</span> (optional) – <span class="font-mono">buy</span> or <span class="font-mono">rent</span>.</li>
            <li><span class="font-mono">rental_hours</span> (required if <span class="font-mono">purchase_type=rent</span>) – 1, 3, 6, 12, 24.</li>
        </ul>
    </div>

    <div class="space-y-4">
        <div class="border border-slate-200 rounded p-4">
            <h4 class="font-semibold">GET /api/v1/balance</h4>
            <p class="text-sm text-slate-600">Returns total balance plus topup and earnings balances.</p>
            <p class="text-xs text-slate-500 mt-2">Response: <span class="font-mono">balance</span>, <span class="font-mono">balance_topup</span>, <span class="font-mono">balance_earnings</span>.</p>
        </div>
        <div class="border border-slate-200 rounded p-4">
            <h4 class="font-semibold">GET /api/v1/services</h4>
            <p class="text-sm text-slate-600">List available services and pricing.</p>
            <p class="text-xs text-slate-500 mt-2">Response: <span class="font-mono">services</span> array with <span class="font-mono">id</span>, <span class="font-mono">name</span>, <span class="font-mono">code</span>, <span class="font-mono">price</span>.</p>
        </div>
        <div class="border border-slate-200 rounded p-4">
            <h4 class="font-semibold">POST /api/v1/purchase</h4>
            <p class="text-sm text-slate-600">Purchase a number. Params: <span class="font-mono">token</span>, <span class="font-mono">service_id</span>, <span class="font-mono">country_id</span>, <span class="font-mono">purchase_type</span>, <span class="font-mono">rental_hours</span>.</p>
            <p class="text-xs text-slate-500 mt-2">Response: <span class="font-mono">success</span>, <span class="font-mono">purchase_id</span>, <span class="font-mono">request_id</span>, <span class="font-mono">number</span>, <span class="font-mono">purchase_type</span>, <span class="font-mono">rental_end_at</span>.</p>
        </div>
        <div class="border border-slate-200 rounded p-4">
            <h4 class="font-semibold">GET /api/v1/sms-status</h4>
            <p class="text-sm text-slate-600">Check SMS status. Params: <span class="font-mono">token</span>, <span class="font-mono">purchase_id</span>.</p>
            <p class="text-xs text-slate-500 mt-2">Response: SMS-Man status payload for the request.</p>
        </div>
    </div>

    <div class="bg-slate-50 border border-slate-200 rounded p-4">
        <h3 class="font-semibold">Errors</h3>
        <p class="text-sm text-slate-600 mt-2">Errors return JSON with <span class="font-mono">success=false</span> and a <span class="font-mono">message</span> field. Common status codes: 401 (missing/invalid token), 402 (insufficient balance), 404 (not found), 422 (validation error).</p>
    </div>
</div>
