<?php

class SmsManClient
{
    private string $baseUrl;
    private string $token;
    private string $cacheDir;

    public function __construct(array $config)
    {
        $this->baseUrl = rtrim($config['base_url'], '/');
        $this->token = $config['token'];
        $this->cacheDir = $config['cache_dir'] ?? (__DIR__ . '/../cache');
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }
    }

    private function request(string $path, array $params = []): array
    {
        if (!$this->token) {
            return ['success' => false, 'error_msg' => 'SMS-Man token not configured'];
        }

        $params['token'] = $this->token;
        $url = $this->baseUrl . '/' . ltrim($path, '/') . '?' . http_build_query($params);

        $response = file_get_contents($url);
        if ($response === false) {
            return ['success' => false, 'error_msg' => 'SMS-Man request failed'];
        }

        $data = json_decode($response, true);
        return $data ?? ['success' => false, 'error_msg' => 'Invalid SMS-Man response'];
    }

    private function requestCached(string $path, array $params, int $ttlSeconds): array
    {
        $cacheKey = md5($path . '|' . http_build_query($params));
        $cacheFile = rtrim($this->cacheDir, '/\\') . DIRECTORY_SEPARATOR . $cacheKey . '.json';

        if (is_file($cacheFile)) {
            $age = time() - filemtime($cacheFile);
            if ($age >= 0 && $age <= $ttlSeconds) {
                $cached = json_decode((string)file_get_contents($cacheFile), true);
                if (is_array($cached)) {
                    return $cached;
                }
            }
        }

        $data = $this->request($path, $params);
        if (is_array($data) && !(isset($data['success']) && $data['success'] === false)) {
            @file_put_contents($cacheFile, json_encode($data));
        }
        return $data;
    }

    public function getBalance(): array
    {
        return $this->request('get-balance');
    }

    public function getCountries(): array
    {
        return $this->requestCached('countries', [], 86400);
    }

    public function getApplications(bool $forceFresh = false): array
    {
        if ($forceFresh) {
            return $this->request('applications');
        }
        return $this->requestCached('applications', [], 3600);
    }

    public function getPrices(int $countryId = 0, bool $forceFresh = false): array
    {
        if ($forceFresh) {
            return $this->request('get-prices', ['country_id' => $countryId]);
        }
        return $this->requestCached('get-prices', ['country_id' => $countryId], 300);
    }

    public function getNumber(int $countryId, int $applicationId, ?int $maxPrice = null, string $currency = 'USD', ?bool $hasMultipleSms = null): array
    {
        $params = [
            'country_id' => $countryId,
            'application_id' => $applicationId,
            'currency' => $currency,
        ];
        if ($maxPrice !== null) {
            $params['maxPrice'] = $maxPrice;
        }
        if ($hasMultipleSms !== null) {
            $params['hasMultipleSms'] = $hasMultipleSms ? 'True' : 'False';
        }
        return $this->request('get-number', $params);
    }

    public function getSms(int $requestId): array
    {
        return $this->request('get-sms', ['request_id' => $requestId]);
    }

    public function setStatus(int $requestId, string $status): array
    {
        return $this->request('set-status', ['request_id' => $requestId, 'status' => $status]);
    }
}
