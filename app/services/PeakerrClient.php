<?php

class PeakerrClient
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct(array $config)
    {
        $this->baseUrl = rtrim($config['base_url'] ?? 'https://peakerr.com/api/v2', '/');
        $this->apiKey = $config['api_key'] ?? '';
    }

    private function request(array $params): array
    {
        if (!$this->apiKey) {
            return ['success' => false, 'error' => 'Peakerr API key not configured'];
        }

        $params['key'] = $this->apiKey;

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($params),
                'timeout' => 20,
            ],
        ]);

        $response = file_get_contents($this->baseUrl, false, $context);
        if ($response === false) {
            return ['success' => false, 'error' => 'Peakerr request failed'];
        }

        $data = json_decode($response, true);
        return $data ?? ['success' => false, 'error' => 'Invalid Peakerr response'];
    }

    public function services(): array
    {
        return $this->request(['action' => 'services']);
    }

    public function addOrder(int $serviceId, string $link, int $quantity, ?int $runs = null, ?int $interval = null): array
    {
        $params = [
            'action' => 'add',
            'service' => $serviceId,
            'link' => $link,
            'quantity' => $quantity,
        ];
        if ($runs !== null) {
            $params['runs'] = $runs;
        }
        if ($interval !== null) {
            $params['interval'] = $interval;
        }
        return $this->request($params);
    }

    public function orderStatus(int $orderId): array
    {
        return $this->request(['action' => 'status', 'order' => $orderId]);
    }

    public function orderStatuses(array $orders): array
    {
        return $this->request(['action' => 'status', 'orders' => implode(',', $orders)]);
    }

    public function balance(): array
    {
        return $this->request(['action' => 'balance']);
    }
}
