<?php

namespace App\Services\ThreatIntelligence;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudUrlAnalysisService
{
    /** Known advertising / tracking / crypto-miner domains */
    protected array $adDomains = [
        'doubleclick.net', 'googlesyndication.com', 'adnxs.com', 'ads.yahoo.com',
        'adroll.com', 'advertising.com', 'taboola.com', 'outbrain.com',
        'rubiconproject.com', 'openx.net', 'pubmatic.com', 'smartadserver.com',
        'criteo.com', 'amazon-adsystem.com', 'adsrvr.org', 'moatads.com',
        'scorecardresearch.com', 'coinhive.com', 'coin-hive.com', 'cryptoloot.pro',
        'mathtod.online', 'minero.cc', 'popads.net', 'popcash.net', 'clickadu.com',
        'trafficjunky.net', 'exoclick.com', 'juicyads.com', 'hilltopads.net',
    ];

    /** Suspicious keywords in page content */
    protected array $suspiciousKeywords = [
        'keygen', 'crack', 'activator', 'warez', 'torrent', 'illegal',
        'hack', 'cheat', 'exploit', 'ransomware', 'trojan', 'malware',
        'phishing', 'scam', 'free download', 'pirate', 'nulled', 'leaked',
    ];

    /** Dangerous downloadable file extensions */
    protected array $dangerousExtensions = [
        '.exe', '.bat', '.cmd', '.msi', '.vbs', '.ps1',
        '.scr', '.pif', '.com', '.hta', '.reg', '.dll', '.apk',
    ];

    /** Suspicious file extensions (medium risk) */
    protected array $suspiciousExtensions = [
        '.zip', '.rar', '.7z', '.iso', '.img', '.dmg',
    ];

    /**
     * Perform a real HTTP analysis of the given URL.
     */
    public function analyze(string $url): array
    {
        $provider  = $this->detectProvider($url);
        $startTime = microtime(true);

        // Real HTTP fetch
        $httpResult   = $this->fetchUrl($url);
        $responseTime = round((microtime(true) - $startTime) * 1000); // ms

        $html       = $httpResult['html'];
        $statusCode = $httpResult['status'];
        $redirects  = $httpResult['redirects'];
        $fetchError = $httpResult['error'];

        // Parse signals
        $isHttps        = str_starts_with(strtolower($url), 'https://');
        $adScriptCount  = $this->countAdScripts($html);
        $iframeCount    = $this->countIframes($html);
        $extScripts     = $this->countExternalScripts($html);
        $suspiciousKw   = $this->detectSuspiciousKeywords($html);
        $downloadLinks  = $this->extractDownloadLinks($html);
        $pageTitle      = $this->extractTitle($html);

        // Score (0-100)
        $score = 0;
        if (!$isHttps)        $score += 20;
        if ($redirects > 3)   $score += 15;
        elseif ($redirects > 1) $score += 8;
        $score += min(25, $adScriptCount * 5);
        $score += min(10, $iframeCount * 3);
        $score += min(20, count($suspiciousKw) * 4);
        if ($statusCode >= 500) $score += 10;

        // Lag from real response time
        $lagLevel     = 'None';
        $loadDelaySec = round($responseTime / 1000, 2);
        if ($responseTime > 6000)      { $lagLevel = 'High';   $score += 20; }
        elseif ($responseTime > 3000)  { $lagLevel = 'Medium'; $score += 10; }
        elseif ($responseTime > 1500)  { $lagLevel = 'Low';    $score += 5; }

        $score = min(100, $score);

        // Severity
        if ($score >= 70)      $severity = 'Critical';
        elseif ($score >= 45)  $severity = 'High';
        elseif ($score >= 20)  $severity = 'Medium';
        else                   $severity = 'Low';

        $files       = $this->buildFileList($downloadLinks, $suspiciousKw);
        $description = $this->buildDescription(
            $severity, $provider, $adScriptCount,
            $lagLevel, $isHttps, $suspiciousKw
        );

        return [
            'provider'            => $provider,
            'popup_count'         => $adScriptCount + $iframeCount,
            'lag_level'           => $lagLevel,
            'load_delay_seconds'  => $loadDelaySec,
            'response_time_ms'    => $responseTime,
            'external_scripts'    => $extScripts,
            'ad_script_count'     => $adScriptCount,
            'iframe_count'        => $iframeCount,
            'redirect_count'      => $redirects,
            'http_status'         => $statusCode,
            'ssl_secure'          => $isHttps,
            'page_title'          => $pageTitle ?: 'Unknown Page',
            'suspicious_keywords' => $suspiciousKw,
            'files'               => $files,
            'score'               => $score,
            'severity'            => $severity,
            'description'         => $description,
            'fetch_error'         => $fetchError,
            'original_severity'   => $severity,
        ];
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function fetchUrl(string $url): array
    {
        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0 Safari/537.36',
                    'Accept'     => 'text/html,application/xhtml+xml,*/*;q=0.8',
                ])
                ->get($url);

            $redirects = 0;
            try {
                $transfers = $response->transferStats;
                if ($transfers) {
                    $redirects = (int) $transfers->getHandlerStat('redirect_count');
                }
            } catch (\Throwable $e) {}

            return [
                'html'      => $response->body(),
                'status'    => $response->status(),
                'redirects' => $redirects,
                'error'     => false,
            ];
        } catch (\Exception $e) {
            Log::warning("ThreatPulse: fetch failed [{$url}]: " . $e->getMessage());
            return [
                'html'      => '',
                'status'    => 0,
                'redirects' => 0,
                'error'     => $e->getMessage(),
            ];
        }
    }

    private function detectProvider(string $url): string
    {
        $map = [
            'drive.google.com'  => 'Google Drive',
            'docs.google.com'   => 'Google Docs',
            'mega.nz'           => 'Mega NZ',
            'mega.co.nz'        => 'Mega NZ',
            'mediafire.com'     => 'MediaFire',
            'dropbox.com'       => 'Dropbox',
            'onedrive.live.com' => 'OneDrive',
            'sharepoint.com'    => 'SharePoint / OneDrive',
            'box.com'           => 'Box',
            '1drv.ms'           => 'OneDrive',
            'wetransfer.com'    => 'WeTransfer',
            'sendspace.com'     => 'SendSpace',
            'zippyshare.com'    => 'ZippyShare',
            'anonfiles.com'     => 'AnonFiles',
            'gofile.io'         => 'GoFile',
        ];
        foreach ($map as $domain => $name) {
            if (stripos($url, $domain) !== false) return $name;
        }
        return parse_url($url, PHP_URL_HOST) ?? 'Unknown Host';
    }

    private function countExternalScripts(string $html): int
    {
        if (!$html) return 0;
        preg_match_all('/<script[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $m);
        return count(array_unique($m[1]));
    }

    private function countAdScripts(string $html): int
    {
        if (!$html) return 0;
        $count = 0;
        $lower = strtolower($html);
        foreach ($this->adDomains as $domain) {
            $count += substr_count($lower, strtolower($domain));
        }
        return $count;
    }

    private function countIframes(string $html): int
    {
        if (!$html) return 0;
        preg_match_all('/<iframe/i', $html, $m);
        return count($m[0]);
    }

    private function detectSuspiciousKeywords(string $html): array
    {
        if (!$html) return [];
        $found = [];
        $lower = strtolower(strip_tags($html));
        foreach ($this->suspiciousKeywords as $kw) {
            if (str_contains($lower, $kw)) {
                $found[] = $kw;
            }
        }
        return array_values(array_unique($found));
    }

    private function extractDownloadLinks(string $html): array
    {
        if (!$html) return [];
        preg_match_all('/(?:href|src)=["\']([^"\']+)["\']/', $html, $m);
        $files = [];
        $seen  = [];
        foreach (array_filter($m[1]) as $link) {
            $lower = strtolower($link);
            foreach ($this->dangerousExtensions as $ext) {
                if (str_ends_with($lower, $ext) && !in_array($link, $seen)) {
                    $files[] = ['href' => $link, 'type' => 'dangerous', 'ext' => $ext];
                    $seen[]  = $link;
                    break;
                }
            }
            foreach ($this->suspiciousExtensions as $ext) {
                if (str_ends_with($lower, $ext) && !in_array($link, $seen)) {
                    $files[] = ['href' => $link, 'type' => 'suspicious', 'ext' => $ext];
                    $seen[]  = $link;
                    break;
                }
            }
        }
        return array_slice($files, 0, 8);
    }

    private function extractTitle(string $html): string
    {
        if (!$html) return '';
        preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $m);
        return trim(strip_tags($m[1] ?? ''));
    }

    private function buildFileList(array $downloadLinks, array $keywords): array
    {
        $files = [];
        foreach ($downloadLinks as $idx => $link) {
            $name = basename(parse_url($link['href'], PHP_URL_PATH));
            if (!$name) $name = 'file_' . ($idx + 1) . $link['ext'];
            $infected = $link['type'] === 'dangerous';
            $files[] = [
                'id'          => 'f' . ($idx + 1),
                'name'        => $name,
                'size'        => 'Unknown',
                'status'      => $infected ? 'infected' : 'suspicious',
                'threat_type' => $infected
                    ? 'Riskware.' . strtoupper(ltrim($link['ext'], '.'))
                    : 'Suspicious.Archive',
                'lag_impact'  => $infected ? 'High' : 'Medium',
                'source'      => 'detected',
            ];
        }

        // Add inferred files based on keyword context
        if (!empty($keywords) && count($files) < 3) {
            $files[] = [
                'id'          => 'f' . (count($files) + 1),
                'name'        => 'page_scripts.js',
                'size'        => 'N/A',
                'status'      => 'suspicious',
                'threat_type' => 'Riskware.WebScript',
                'lag_impact'  => 'Low',
                'source'      => 'inferred',
            ];
        }

        // Always add a baseline clean file entry
        $files[] = [
            'id'          => 'f' . (count($files) + 1),
            'name'        => 'index.html',
            'size'        => 'N/A',
            'status'      => 'clean',
            'threat_type' => 'None',
            'lag_impact'  => 'None',
            'source'      => 'inferred',
        ];

        return $files;
    }

    private function buildDescription(
        string $severity, string $provider, int $adCount,
        string $lagLevel, bool $isHttps, array $keywords
    ): string {
        $parts = [];
        if (!$isHttps)           $parts[] = 'no SSL/TLS encryption';
        if ($adCount > 0)        $parts[] = "{$adCount} ad/tracking script(s)";
        if ($lagLevel !== 'None') $parts[] = "{$lagLevel} load lag";
        if (!empty($keywords))   $parts[] = 'suspicious keywords (' . implode(', ', array_slice($keywords, 0, 3)) . ')';

        $riskText = match ($severity) {
            'Critical' => "CRITICAL RISK — {$provider} link flagged as highly dangerous. Do NOT enter.",
            'High'     => "HIGH RISK — {$provider} link shows multiple threat signals. Entering is not recommended.",
            'Medium'   => "MEDIUM RISK — {$provider} link has suspicious signals. Inspect files carefully before proceeding.",
            default    => "LOW RISK — {$provider} link appears generally safe based on real-time analysis.",
        };

        return $riskText . (!empty($parts) ? ' Detected: ' . implode('; ', $parts) . '.' : '');
    }
}

