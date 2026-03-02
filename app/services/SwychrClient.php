<?php

class SwychrClient
{
    private string $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function authenticate(string $email, string $password): array
    {
        $url = $this->baseUrl . '/v1/auth/login';
        $payload = [
            'email' => $email,
            'password' => $password,
        ];

        return $this->postJson($url, $payload, []);
    }

    /**
     * Create a payment link using Swychr API
     * Endpoint: https://api.swychr.com/v1/create_payment_links
     */
    public function createPaymentLink(array $payload, string $token): array
    {
        $url = $this->baseUrl . '/v1/create_payment_links';
        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ];
        return $this->postJson($url, $payload, $headers);
    }

    /**
     * Check payment status using Swychr API
     * Endpoint: https://api.swychr.com/v1/payment_link_status
     */
    public function getPaymentStatus(string $transactionId, string $token): array
    {
        $url = $this->baseUrl . '/v1/payment_link_status';
        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ];
        $payload = [
            'transaction_id' => $transactionId,
        ];
        return $this->postJson($url, $payload, $headers);
    }

    private function postJson(string $url, array $payload, array $headers): array
    {
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => $headers,
                'content' => json_encode($payload),
                'timeout' => 30,
                'ignore_errors' => true, // get content even on HTTP error
            ],
        ];
        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            $error = error_get_last();
            $http_response_header = $http_response_header ?? [];
            return [
                'success' => false,
                'message' => 'Swychr request failed',
                'error' => $error,
                'http_response_header' => $http_response_header,
            ];
        }
        $data = json_decode($response, true);
        if ($data === null) {
            $http_response_header = $http_response_header ?? [];
            return [
                'success' => false,
                'message' => 'Invalid Swychr response',
                'raw_response' => $response,
                'http_response_header' => $http_response_header,
            ];
        }
        return $data;
    }
}
