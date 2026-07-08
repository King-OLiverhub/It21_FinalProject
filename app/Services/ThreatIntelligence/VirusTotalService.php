<?php

namespace App\Services\ThreatIntelligence;

class VirusTotalService extends BaseService
{
    protected $baseUrl = 'https://www.virustotal.com/api/v3/';

    public function __construct()
    {
        $this->apiKey = config('services.virustotal.api_key');
    }

    protected function getHeaders(): array
    {
        return [
            'x-apikey' => $this->apiKey,
            'Accept' => 'application/json',
        ];
    }

    public function checkIP(string $ip): ?array
    {
        return $this->makeRequest("ip_addresses/{$ip}");
    }

    public function checkDomain(string $domain): ?array
    {
        return $this->makeRequest("domains/{$domain}");
    }

    public function checkFileHash(string $hash): ?array
    {
        return $this->makeRequest("files/{$hash}");
    }

    public function checkURL(string $url): ?array
    {
        $encodedUrl = urlencode($url);
        return $this->makeRequest("urls/{$encodedUrl}");
    }

    public function analyzeIP(string $ip): array
    {
        $data = $this->checkIP($ip);
        
        if (!$data) {
            return [
                'is_malicious' => false,
                'reputation' => 0,
                'last_analysis_stats' => [],
                'country' => null,
                'owner' => null,
            ];
        }

        $attributes = $data['data']['attributes'] ?? [];
        
        return [
            'is_malicious' => ($attributes['last_analysis_stats']['malicious'] ?? 0) > 0,
            'reputation' => $attributes['reputation'] ?? 0,
            'last_analysis_stats' => $attributes['last_analysis_stats'] ?? [],
            'country' => $attributes['country'] ?? null,
            'owner' => $attributes['as_owner'] ?? null,
        ];
    }
}