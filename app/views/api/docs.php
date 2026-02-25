<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-semibold">GetSMS API</h2>
        <p class="text-slate-600 mt-2">Use your API token to integrate with GetSMS for automated number purchases.</p>
    </div>

    <div class="bg-slate-50 border border-slate-200 rounded p-4">
        <h3 class="font-semibold">Authentication</h3>
        <p class="text-sm text-slate-600 mt-2">Generate your token on the dashboard, then pass it as <span class="font-mono">token</span> query or body parameter.</p>
    </div>

    <div class="space-y-4">
        <div class="border border-slate-200 rounded p-4">
            <h4 class="font-semibold">GET /api/v1/balance</h4>
            <p class="text-sm text-slate-600">Returns your wallet balance.</p>
        </div>
        <div class="border border-slate-200 rounded p-4">
            <h4 class="font-semibold">GET /api/v1/services</h4>
            <p class="text-sm text-slate-600">List available services and pricing.</p>
        </div>
        <div class="border border-slate-200 rounded p-4">
            <h4 class="font-semibold">POST /api/v1/purchase</h4>
            <p class="text-sm text-slate-600">Purchase a number. Params: <span class="font-mono">token</span>, <span class="font-mono">service_id</span>, <span class="font-mono">country_id</span>.</p>
        </div>
        <div class="border border-slate-200 rounded p-4">
            <h4 class="font-semibold">GET /api/v1/sms-status</h4>
            <p class="text-sm text-slate-600">Check SMS status. Params: <span class="font-mono">token</span>, <span class="font-mono">purchase_id</span>.</p>
        </div>
    </div>
</div>
