<?php

namespace App\Services;

use App\Models\NetworkDevice;
use Illuminate\Support\Facades\Log;

class NetworkDeviceScanner
{
    /**
     * Scan the network and sync status with the database.
     *
     * @param bool $scanSubnet Whether to scan the entire local /24 subnet.
     * @return array Summary of the scan results.
     */
    public function scanAndSync($scanSubnet = false)
    {
        Log::info("Starting network scan. Subnet scan: " . ($scanSubnet ? 'Yes' : 'No'));

        // 1. Gather all target IPs
        $targetIps = [];

        // Add currently registered device IPs from database
        $dbDevices = NetworkDevice::all();
        foreach ($dbDevices as $dev) {
            if ($dev->ip_address && filter_var($dev->ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $targetIps[] = $dev->ip_address;
            }
        }

        // Add local subnets if requested
        if ($scanSubnet) {
            $subnets = $this->detectSubnets();
            foreach ($subnets as $subnet) {
                Log::info("Scanning local subnet: {$subnet}.0/24");
                for ($i = 1; $i <= 254; $i++) {
                    $targetIps[] = "{$subnet}.{$i}";
                }
            }
        }

        $targetIps = array_unique(array_filter($targetIps));

        // 2. Ping targets in parallel (chunked)
        Log::info("Pinging " . count($targetIps) . " target IPs in parallel.");
        $pingResults = $this->pingInParallel($targetIps);

        // 3. Read and parse local ARP cache to find MAC addresses
        Log::info("Parsing local ARP cache.");
        $arpMap = $this->parseArpCache();

        // 4. Match and identify active devices
        $activeDevices = []; // ip => mac
        foreach ($pingResults as $ip => $responded) {
            // A device is active if it responded to ping,
            // OR if it exists in the ARP map (dynamic ARP mapping means active traffic).
            $mac = $arpMap[$ip] ?? null;
            
            if ($responded || $mac) {
                // If we don't have a MAC but host responded, try to run a quick single arp query for that IP
                if (!$mac) {
                    $mac = $this->queryArpForIp($ip);
                }
                
                if ($mac) {
                    $activeDevices[$ip] = $mac;
                }
            }
        }

        Log::info("Found " . count($activeDevices) . " active devices on the local network.");

        // 5. Update/Insert in database
        $updatedIds = [];
        $insertedCount = 0;
        $updatedCount = 0;

        foreach ($activeDevices as $ip => $mac) {
            // Find by MAC first (handles IP changes)
            $device = NetworkDevice::where('mac_address', $mac)->first();

            if (!$device) {
                // Fallback to IP address lookup
                $device = NetworkDevice::where('ip_address', $ip)->first();
            }

            if ($device) {
                // Update existing device
                $oldStatus = $device->status;
                
                // If IP or MAC changed, update them
                $device->ip_address = $ip;
                $device->mac_address = $mac;
                $device->last_scanned_at = now();

                // If currently Inactive, mark Active. Keep Blocked or Maintenance status.
                if ($device->status === 'Inactive') {
                    $device->status = 'Active';
                }

                $device->save();
                $updatedIds[] = $device->id;
                $updatedCount++;
            } else {
                // Resolve hostname
                $hostname = @gethostbyaddr($ip);
                if (!$hostname || $hostname === $ip) {
                    $hostname = "Host-" . substr($mac, -8); // Host-4D:5F:60 or similar
                }

                // Clean hostname
                $hostname = trim($hostname);

                // Create new device
                $device = NetworkDevice::create([
                    'device_name' => $hostname,
                    'ip_address' => $ip,
                    'mac_address' => $mac,
                    'device_type' => $this->classifyDeviceType($ip, $hostname),
                    'status' => 'Active',
                    'firmware_version' => null,
                    'location' => 'Auto-Discovered',
                    'last_scanned_at' => now(),
                ]);

                $updatedIds[] = $device->id;
                $insertedCount++;
            }
        }

        // 6. Mark non-responsive, non-arp devices as offline (Inactive)
        $offlineCount = 0;
        foreach ($dbDevices as $dev) {
            if (!in_array($dev->id, $updatedIds)) {
                // Preserve Blocked and Maintenance status
                if ($dev->status === 'Active') {
                    $dev->status = 'Inactive';
                    $dev->save();
                    $offlineCount++;
                }
            }
        }

        Log::info("Scan completed. New: {$insertedCount}, Updated: {$updatedCount}, Marked Offline: {$offlineCount}");

        return [
            'scanned_ips' => count($targetIps),
            'active_hosts' => count($activeDevices),
            'inserted' => $insertedCount,
            'updated' => $updatedCount,
            'marked_offline' => $offlineCount,
        ];
    }

    /**
     * Ping a list of IPs in parallel chunks.
     */
    private function pingInParallel(array $ips, $chunkSize = 50)
    {
        $results = [];
        $chunks = array_chunk($ips, $chunkSize);
        $isWindows = stripos(PHP_OS, 'WIN') === 0;

        foreach ($chunks as $chunk) {
            $processes = [];
            foreach ($chunk as $ip) {
                // Windows ping options: -n 1 (1 packet), -w 300 (300ms timeout)
                // Linux/Mac ping options: -c 1 (1 packet), -W 1 (1s timeout)
                $cmd = $isWindows 
                    ? "ping -n 1 -w 300 " . escapeshellarg($ip)
                    : "ping -c 1 -W 1 " . escapeshellarg($ip);
                
                $descriptors = [
                    0 => ["pipe", "r"],
                    1 => ["pipe", "w"],
                    2 => ["pipe", "w"]
                ];
                
                $proc = proc_open($cmd, $descriptors, $pipes);
                if (is_resource($proc)) {
                    $processes[$ip] = [
                        'proc' => $proc,
                        'pipes' => $pipes
                    ];
                }
            }

            // Gather outputs
            foreach ($processes as $ip => $data) {
                $proc = $data['proc'];
                $pipes = $data['pipes'];
                
                $stdout = stream_get_contents($pipes[1]);
                fclose($pipes[0]);
                fclose($pipes[1]);
                fclose($pipes[2]);
                proc_close($proc);
                
                $responded = false;
                if ($isWindows) {
                    if (stripos($stdout, 'TTL=') !== false) {
                        $responded = true;
                    }
                } else {
                    if (stripos($stdout, 'ttl=') !== false || stripos($stdout, '1 received') !== false || stripos($stdout, '1 packets received') !== false) {
                        $responded = true;
                    }
                }
                $results[$ip] = $responded;
            }
        }

        return $results;
    }

    /**
     * Parse local ARP cache.
     */
    private function parseArpCache()
    {
        $arpMap = [];
        $output = shell_exec('arp -a');
        if (!$output) {
            return $arpMap;
        }

        // Match lines containing an IP and a MAC address
        // Supported formats:
        // Windows:  192.168.254.107       a4-ae-12-4f-0a-c3     dynamic
        // Linux:    ? (192.168.254.107) at a4:ae:12:4f:0a:c3 [ether] on wlan0
        $lines = explode("\n", $output);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Simple regex to pull out the first IP and first MAC-like address
            if (preg_match('/\b((?:\d{1,3}\.){3}\d{1,3})\b/', $line, $ipMatches) &&
                preg_match('/\b((?:[0-9a-fA-F]{2}[:-]){5}[0-9a-fA-F]{2})\b/', $line, $macMatches)) {
                
                $ip = $ipMatches[1];
                $mac = strtoupper(str_replace('-', ':', $macMatches[1]));

                // Filter out broadcast and multicast MACs
                if ($mac === 'FF:FF:FF:FF:FF:FF' || str_starts_with($mac, '01:00:5E') || str_starts_with($mac, '33:33')) {
                    continue;
                }

                $arpMap[$ip] = $mac;
            }
        }

        return $arpMap;
    }

    /**
     * Query ARP specifically for a single IP.
     */
    private function queryArpForIp($ip)
    {
        $isWindows = stripos(PHP_OS, 'WIN') === 0;
        $cmd = $isWindows ? "arp -a " . escapeshellarg($ip) : "arp -n " . escapeshellarg($ip);
        $output = shell_exec($cmd);
        if ($output && preg_match('/\b((?:[0-9a-fA-F]{2}[:-]){5}[0-9a-fA-F]{2})\b/', $output, $matches)) {
            $mac = strtoupper(str_replace('-', ':', $matches[1]));
            if ($mac !== 'FF:FF:FF:FF:FF:FF' && !str_starts_with($mac, '01:00:5E') && !str_starts_with($mac, '33:33')) {
                return $mac;
            }
        }
        return null;
    }

    /**
     * Detect local IP subnets (/24 networks).
     */
    private function detectSubnets()
    {
        $subnets = [];
        $isWindows = stripos(PHP_OS, 'WIN') === 0;

        if ($isWindows) {
            $output = shell_exec('ipconfig');
            if ($output) {
                $lines = explode("\n", $output);
                $currentIp = null;
                foreach ($lines as $line) {
                    if (preg_match('/IPv4 Address.*:\s*([0-9\.]+)/i', $line, $matches)) {
                        $currentIp = trim($matches[1]);
                    } elseif (preg_match('/Subnet Mask.*:\s*([0-9\.]+)/i', $line, $matches) && $currentIp) {
                        $mask = trim($matches[1]);
                        if ($mask === '255.255.255.0' && !str_starts_with($currentIp, '169.254') && !str_starts_with($currentIp, '127.')) {
                            $parts = explode('.', $currentIp);
                            $subnets[] = "{$parts[0]}.{$parts[1]}.{$parts[2]}";
                        }
                        $currentIp = null;
                    }
                }
            }
        } else {
            // Linux/Mac
            $output = shell_exec("ip addr show | grep -E 'inet '");
            if ($output) {
                $lines = explode("\n", $output);
                foreach ($lines as $line) {
                    if (preg_match('/inet\s+([0-9\.]+)\/24/i', $line, $matches)) {
                        $ip = $matches[1];
                        if (!str_starts_with($ip, '127.') && !str_starts_with($ip, '169.254')) {
                            $parts = explode('.', $ip);
                            $subnets[] = "{$parts[0]}.{$parts[1]}.{$parts[2]}";
                        }
                    }
                }
            }
        }

        // Fallback if no subnets detected
        if (empty($subnets)) {
            $localIp = gethostbyname(gethostname());
            if ($localIp && $localIp !== '127.0.0.1' && !str_starts_with($localIp, '169.254')) {
                $parts = explode('.', $localIp);
                if (count($parts) === 4) {
                    $subnets[] = "{$parts[0]}.{$parts[1]}.{$parts[2]}";
                }
            }
        }

        return array_unique($subnets);
    }

    /**
     * Classify network device type based on hostname and IP characteristics.
     */
    private function classifyDeviceType($ip, $hostname)
    {
        $hostnameLower = strtolower($hostname);
        if (str_contains($hostnameLower, 'switch')) {
            return 'Switch';
        }
        if (str_contains($hostnameLower, 'firewall') || str_contains($hostnameLower, 'pfsense') || str_contains($hostnameLower, 'fortigate') || str_contains($hostnameLower, 'opnsense')) {
            return 'Firewall';
        }
        if (str_contains($hostnameLower, 'ap') || str_contains($hostnameLower, 'airport') || str_contains($hostnameLower, 'wireless') || str_contains($hostnameLower, 'unifi') || str_contains($hostnameLower, 'accesspoint')) {
            return 'Access Point';
        }
        if (str_contains($hostnameLower, 'router') || str_contains($hostnameLower, 'gateway')) {
            return 'Router';
        }
        if (str_contains($hostnameLower, 'server') || str_contains($hostnameLower, 'nas') || str_contains($hostnameLower, 'proxmox') || str_contains($hostnameLower, 'esxi')) {
            return 'Server';
        }
        
        // IP address heuristics
        $lastOctet = (int) substr(strrchr($ip, '.'), 1);
        if ($lastOctet === 1 || $lastOctet === 254) {
            return 'Router';
        }
        
        return 'Other';
    }
}
