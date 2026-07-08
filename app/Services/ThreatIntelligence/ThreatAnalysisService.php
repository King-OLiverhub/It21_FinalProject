<?php

namespace App\Services\ThreatIntelligence;

use App\Models\ThreatIndicator;
use App\Models\Alert;
use App\Models\ThreatLog;
use App\Models\SecurityEvent;
use Carbon\Carbon;

class ThreatAnalysisService
{
    protected $virusTotal;
    protected $abuseIPDB;
    protected $ipInfo;

    public function __construct(
        VirusTotalService $virusTotal,
        AbuseIPDBService $abuseIPDB,
        IPInfoService $ipInfo
    ) {
        $this->virusTotal = $virusTotal;
        $this->abuseIPDB = $abuseIPDB;
        $this->ipInfo = $ipInfo;
    }

    public function analyzeIP(string $ip): array
    {
        $vtData = $this->virusTotal->analyzeIP($ip);
        $abuseData = $this->abuseIPDB->analyzeIP($ip);
        $ipInfo = $this->ipInfo->analyzeIP($ip);

        $combinedData = array_merge($vtData, $abuseData, $ipInfo);
        
        // Calculate severity
        $severity = $this->calculateSeverity($combinedData);
        
        // Calculate confidence score
        $confidenceScore = $this->calculateConfidence($combinedData);
        
        return [
            'indicator_type' => 'ip',
            'value' => $ip,
            'severity' => $severity,
            'confidence_score' => $confidenceScore,
            'threat_data' => $combinedData,
            'source' => 'VirusTotal & AbuseIPDB',
            'country' => $combinedData['country'] ?? null,
            'city' => $combinedData['city'] ?? null,
            'latitude' => $combinedData['latitude'] ?? null,
            'longitude' => $combinedData['longitude'] ?? null,
            'reports_count' => $combinedData['total_reports'] ?? 0,
        ];
    }

    public function analyzeDomain(string $domain): array
    {
        $vtData = $this->virusTotal->checkDomain($domain);
        
        $severity = 'Low';
        $confidenceScore = 0;
        
        if ($vtData && isset($vtData['data']['attributes']['last_analysis_stats'])) {
            $stats = $vtData['data']['attributes']['last_analysis_stats'];
            $malicious = $stats['malicious'] ?? 0;
            
            if ($malicious > 5) {
                $severity = 'Critical';
                $confidenceScore = 90;
            } elseif ($malicious > 2) {
                $severity = 'High';
                $confidenceScore = 70;
            } elseif ($malicious > 0) {
                $severity = 'Medium';
                $confidenceScore = 40;
            }
        }
        
        return [
            'indicator_type' => 'domain',
            'value' => $domain,
            'severity' => $severity,
            'confidence_score' => $confidenceScore,
            'threat_data' => $vtData ?? [],
            'source' => 'VirusTotal',
        ];
    }

    public function analyzeFileHash(string $hash): array
    {
        $vtData = $this->virusTotal->checkFileHash($hash);
        
        $severity = 'Low';
        $confidenceScore = 0;
        
        if ($vtData && isset($vtData['data']['attributes']['last_analysis_stats'])) {
            $stats = $vtData['data']['attributes']['last_analysis_stats'];
            $malicious = $stats['malicious'] ?? 0;
            
            if ($malicious > 10) {
                $severity = 'Critical';
                $confidenceScore = 95;
            } elseif ($malicious > 5) {
                $severity = 'High';
                $confidenceScore = 75;
            } elseif ($malicious > 0) {
                $severity = 'Medium';
                $confidenceScore = 45;
            }
        }
        
        return [
            'indicator_type' => 'file_hash',
            'value' => $hash,
            'severity' => $severity,
            'confidence_score' => $confidenceScore,
            'threat_data' => $vtData ?? [],
            'source' => 'VirusTotal',
        ];
    }

    protected function calculateSeverity(array $data): string
    {
        $score = 0;
        
        // VirusTotal malicious count
        $malicious = $data['last_analysis_stats']['malicious'] ?? 0;
        if ($malicious > 5) $score += 3;
        elseif ($malicious > 2) $score += 2;
        elseif ($malicious > 0) $score += 1;
        
        // AbuseIPDB score
        $abuseScore = $data['abuse_score'] ?? 0;
        if ($abuseScore > 75) $score += 3;
        elseif ($abuseScore > 50) $score += 2;
        elseif ($abuseScore > 25) $score += 1;
        
        // Determine severity
        if ($score >= 5) return 'Critical';
        if ($score >= 3) return 'High';
        if ($score >= 1) return 'Medium';
        return 'Low';
    }

    protected function calculateConfidence(array $data): float
    {
        $confidence = 0;
        
        // VirusTotal confidence
        $malicious = $data['last_analysis_stats']['malicious'] ?? 0;
        $total = $data['last_analysis_stats']['total'] ?? 1;
        $confidence += ($malicious / $total) * 50;
        
        // AbuseIPDB confidence
        $abuseScore = $data['abuse_score'] ?? 0;
        $confidence += ($abuseScore / 100) * 50;
        
        return min(100, $confidence);
    }

    public function saveThreatIndicator(array $data): ThreatIndicator
    {
        $indicator = ThreatIndicator::create($data);
        
        // Create alert if severity is High or Critical
        if (in_array($data['severity'], ['High', 'Critical'])) {
            $this->createAlert($indicator);
        }
        
        // Log the detection
        ThreatLog::log(
            'threat_detection',
            'threat_indicator_created',
            $data,
            $data['value']
        );
        
        return $indicator;
    }

    protected function createAlert(ThreatIndicator $indicator)
    {
        $message = "{$indicator->severity} severity threat detected: {$indicator->value} ({$indicator->indicator_type})";
        
        Alert::create([
            'threat_indicator_id' => $indicator->id,
            'alert_type' => 'threat_detected',
            'severity' => $indicator->severity,
            'message' => $message,
            'recommendation' => $this->getRecommendation($indicator->severity),
            'is_read' => false,
            'is_resolved' => false,
        ]);
    }

    protected function getRecommendation(string $severity): string
    {
        return match($severity) {
            'Critical' => 'Immediate action required. Block the indicator and investigate the source.',
            'High' => 'High priority. Block and monitor the indicator closely.',
            'Medium' => 'Monitor the indicator and investigate if further suspicious activity is detected.',
            default => 'Low priority. Keep monitoring for any changes.',
        };
    }
}