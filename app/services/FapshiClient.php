<?php

class FapshiClient
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct(array $config)
    {
        $this->baseUrl = rtrim($config['base_url'], '/');
        $this->apiKey = $config['api_key'];
    }

    public function initiatePayment(array $payload): array
    {
        if (!$this->apiKey) {
            return ['success' => false, 'message' => 'Fapshi API key not configured'];
        }

        $url = $this->baseUrl . '/initiate-pay';
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/json',
                    'apiuser: ' . $this->apiKey,
                ],
                'content' => json_encode($payload),
                'timeout' => 30,
            ],
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        if ($response === false) {
            return ['success' => false, 'message' => 'Fapshi request failed'];
        }

        $data = json_decode($response, true);
        return $data ?? ['success' => false, 'message' => 'Invalid Fapshi response'];
    }
}
