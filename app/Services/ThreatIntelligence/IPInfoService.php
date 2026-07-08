<?php

namespace App\Services\ThreatIntelligence;

class IPInfoService extends BaseService
{
    protected $baseUrl = 'https://ipinfo.io/';

    public function __construct()
    {
        $this->apiKey = config('services.ipinfo.api_key');
    }

    protected function getHeaders(): array
    {
        return [];
    }

    public function getIPInfo(string $ip): ?array
    {
        return $this->makeRequest("{$ip}/json", [
            'token' => $this->apiKey,
        ]);
    }

    public function getBatchIPInfo(array $ips): ?array
    {
        return $this->makeRequest("batch", [
            'token' => $this->apiKey,
            'ips' => implode(',', $ips),
        ]);
    }

    public function analyzeIP(string $ip): array
    {
        $data = $this->getIPInfo($ip);
        
        if (!$data) {
            return [
                'city' => null,
                'region' => null,
                'country' => null,
                'country_code' => null,
                'latitude' => null,
                'longitude' => null,
                'timezone' => null,
                'isp' => null,
                'org' => null,
                'asn' => null,
            ];
        }

        $loc = explode(',', $data['loc'] ?? '0,0');
        
        return [
            'city' => $data['city'] ?? null,
            'region' => $data['region'] ?? null,
            'country' => $data['country'] ?? null,
            'country_code' => $data['country'] ?? null,
            'latitude' => (float) ($loc[0] ?? 0),
            'longitude' => (float) ($loc[1] ?? 0),
            'timezone' => $data['timezone'] ?? null,
            'isp' => $data['org'] ?? null,
            'org' => $data['org'] ?? null,
            'asn' => $data['asn'] ?? null,
        ];
    }
}