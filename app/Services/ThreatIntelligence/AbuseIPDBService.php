<?php

namespace App\Services\ThreatIntelligence;

class AbuseIPDBService extends BaseService
{
    protected $baseUrl = 'https://api.abuseipdb.com/api/v2/';

    public function __construct()
    {
        $this->apiKey = config('services.abuseipdb.api_key');
    }

    protected function getHeaders(): array
    {
        return [
            'Key' => $this->apiKey,
            'Accept' => 'application/json',
        ];
    }

    public function checkIP(string $ip, int $maxAgeInDays = 90): ?array
    {
        return $this->makeRequest('check', [
            'ipAddress' => $ip,
            'maxAgeInDays' => $maxAgeInDays,
            'verbose' => true,
        ]);
    }

    public function reportIP(string $ip, array $categories, string $comment): ?array
    {
        return $this->makeRequest('report', [
            'ip' => $ip,
            'categories' => implode(',', $categories),
            'comment' => $comment,
        ], 'POST');
    }

    public function getBlacklistedIPs(int $limit = 100): ?array
    {
        return $this->makeRequest('blacklist', [
            'limit' => $limit,
        ]);
    }

    public function analyzeIP(string $ip): array
    {
        $data = $this->checkIP($ip);
        
        if (!$data || !isset($data['data'])) {
            return [
                'is_malicious' => false,
                'abuse_score' => 0,
                'total_reports' => 0,
                'last_report_at' => null,
                'country_code' => null,
                'isp' => null,
            ];
        }

        $attributes = $data['data'] ?? [];
        
        return [
            'is_malicious' => ($attributes['abuseConfidenceScore'] ?? 0) > 25,
            'abuse_score' => $attributes['abuseConfidenceScore'] ?? 0,
            'total_reports' => $attributes['totalReports'] ?? 0,
            'last_report_at' => $attributes['lastReportedAt'] ?? null,
            'country_code' => $attributes['countryCode'] ?? null,
            'isp' => $attributes['isp'] ?? null,
        ];
    }
}