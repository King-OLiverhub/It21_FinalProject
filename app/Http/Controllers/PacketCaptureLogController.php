<?php

namespace App\Http\Controllers;

use App\Models\PacketCaptureLog;
use Illuminate\Http\Request;

class PacketCaptureLogController extends Controller
{
    /**
     * Display the packet capture log listing with filters.
     * Admin-only via route middleware.
     */
    public function index(Request $request)
    {
        $status   = $request->get('status');
        $protocol = $request->get('protocol');
        $direction = $request->get('direction');
        $search   = $request->get('search');

        $query = PacketCaptureLog::query()->orderBy('captured_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($protocol) {
            $query->where('protocol', $protocol);
        }

        if ($direction) {
            $query->where('direction', $direction);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('source_ip', 'like', "%{$search}%")
                  ->orWhere('destination_ip', 'like', "%{$search}%")
                  ->orWhere('info', 'like', "%{$search}%");
            });
        }

        $packets = $query->paginate(15)->withQueryString();

        // Summary counts for header stats
        $total      = PacketCaptureLog::count();
        $normal     = PacketCaptureLog::where('status', 'Normal')->count();
        $suspicious = PacketCaptureLog::where('status', 'Suspicious')->count();
        $malicious  = PacketCaptureLog::where('status', 'Malicious')->count();

        return view('packet_capture_logs.index', compact(
            'packets', 'status', 'protocol', 'direction', 'search',
            'total', 'normal', 'suspicious', 'malicious'
        ));
    }

    /**
     * Flush all packet capture logs.
     * Admin-only via route middleware.
     */
    public function flush(Request $request)
    {
        PacketCaptureLog::truncate();
        return redirect()->route('packet-capture-logs.index')
                         ->with('success', 'All packet capture logs have been cleared successfully.');
    }

    /**
     * Get list of interfaces using tshark -D.
     * Admin-only via route middleware.
     */
    public function interfaces()
    {
        $tshark = 'C:\Program Files\Wireshark\tshark.exe';
        if (!file_exists($tshark)) {
            return response()->json(['error' => 'TShark executable not found'], 500);
        }

        exec('"' . $tshark . '" -D', $output, $resultCode);
        if ($resultCode !== 0) {
            return response()->json(['error' => 'Failed to retrieve interfaces from TShark'], 500);
        }

        $interfaces = [];
        foreach ($output as $line) {
            if (preg_match('/^(\d+)\.\s+(\S+)\s+\((.+)\)$/', trim($line), $matches)) {
                $interfaces[] = [
                    'index' => $matches[1],
                    'id' => $matches[2],
                    'name' => $matches[3]
                ];
            } elseif (preg_match('/^(\d+)\.\s+(\S+)$/', trim($line), $matches)) {
                $interfaces[] = [
                    'index' => $matches[1],
                    'id' => $matches[2],
                    'name' => $matches[2]
                ];
            }
        }

        return response()->json($interfaces);
    }

    /**
     * Live stream packet captures using Server-Sent Events (SSE).
     * Admin-only via route middleware.
     */
    public function stream(Request $request)
    {
        $interface = $request->get('interface');
        $filter = $request->get('filter');
        $limit = intval($request->get('limit', 100));
        if ($limit <= 0) {
            $limit = 100;
        }

        $tshark = 'C:\Program Files\Wireshark\tshark.exe';
        if (!file_exists($tshark)) {
            return response()->json(['error' => 'TShark executable not found'], 500);
        }

        $cmd = '"' . $tshark . '" -i ' . escapeshellarg($interface) . ' -T fields -e ip.src -e ipv6.src -e ip.dst -e ipv6.dst -e tcp.srcport -e tcp.dstport -e udp.srcport -e udp.dstport -e frame.protocols -e frame.len -e _ws.col.Info -E separator=/t -l';
        
        if ($filter) {
            $cmd .= ' -f ' . escapeshellarg($filter);
        }

        $cmd .= ' -c ' . $limit;

        return response()->stream(function () use ($cmd, $limit) {
            $descriptorspec = [
                0 => ["pipe", "r"],
                1 => ["pipe", "w"],
                2 => ["pipe", "w"]
            ];

            $process = proc_open($cmd, $descriptorspec, $pipes);

            if (!is_resource($process)) {
                echo "event: error\n";
                echo "data: " . json_encode(['message' => 'Failed to start TShark process']) . "\n\n";
                ob_flush();
                flush();
                return;
            }

            $packetCount = 0;
            while (!feof($pipes[1])) {
                if (connection_aborted()) {
                    break;
                }

                $line = fgets($pipes[1]);
                if ($line === false) {
                    break;
                }

                $line = trim($line, "\r\n");
                $parts = explode("\t", $line);
                if (count($parts) < 11) {
                    continue;
                }

                $srcIp = !empty($parts[0]) ? $parts[0] : (!empty($parts[1]) ? $parts[1] : null);
                $dstIp = !empty($parts[2]) ? $parts[2] : (!empty($parts[3]) ? $parts[3] : null);

                if (!$srcIp && !$dstIp) {
                    $srcIp = '0.0.0.0';
                    $dstIp = '0.0.0.0';
                }

                $srcPort = !empty($parts[4]) ? intval($parts[4]) : (!empty($parts[6]) ? intval($parts[6]) : 0);
                $dstPort = !empty($parts[5]) ? intval($parts[5]) : (!empty($parts[7]) ? intval($parts[7]) : 0);

                $protocols = !empty($parts[8]) ? $parts[8] : '';
                $len = !empty($parts[9]) ? intval($parts[9]) : 0;
                $info = !empty($parts[10]) ? $parts[10] : '';

                $protocol = 'IP';
                $lowerProto = strtolower($protocols);
                if (str_contains($lowerProto, 'dns')) {
                    $protocol = 'DNS';
                } elseif (str_contains($lowerProto, 'tls') || str_contains($lowerProto, 'ssl')) {
                    $protocol = 'HTTPS';
                } elseif (str_contains($lowerProto, 'http')) {
                    $protocol = 'HTTP';
                } elseif (str_contains($lowerProto, 'ftp')) {
                    $protocol = 'FTP';
                } elseif (str_contains($lowerProto, 'arp')) {
                    $protocol = 'ARP';
                } elseif (str_contains($lowerProto, 'icmp')) {
                    $protocol = 'ICMP';
                } elseif (str_contains($lowerProto, 'tcp')) {
                    $protocol = 'TCP';
                } elseif (str_contains($lowerProto, 'udp')) {
                    $protocol = 'UDP';
                } else {
                    $protoParts = explode(':', $protocols);
                    $last = end($protoParts);
                    if ($last && $last !== 'null') {
                        $protocol = strtoupper($last);
                    }
                }

                $isLocal = function ($ip) {
                    if ($ip === '127.0.0.1' || $ip === '::1' || $ip === 'localhost') return true;
                    if (strpos($ip, '192.168.') === 0) return true;
                    if (strpos($ip, '10.') === 0) return true;
                    if (strpos($ip, '172.') === 0) {
                        $p = explode('.', $ip);
                        if (count($p) >= 2 && intval($p[1]) >= 16 && intval($p[1]) <= 31) {
                            return true;
                        }
                    }
                    return false;
                };

                if ($isLocal($srcIp) && $isLocal($dstIp)) {
                    $direction = 'Internal';
                } elseif ($isLocal($srcIp)) {
                    $direction = 'Outbound';
                } elseif ($isLocal($dstIp)) {
                    $direction = 'Inbound';
                } else {
                    $direction = 'Inbound';
                }

                $status = 'Normal';
                $lowerInfo = strtolower($info);

                if (str_contains($lowerInfo, 'brute') || str_contains($lowerInfo, 'flood') || str_contains($lowerInfo, 'attack') || str_contains($lowerInfo, 'spoof') || str_contains($lowerInfo, 'scan') || str_contains($lowerInfo, 'overflow')) {
                    $status = 'Malicious';
                } elseif ($dstPort === 4444 || $srcPort === 4444) {
                    $status = 'Suspicious';
                } elseif ($dstPort === 23 || $srcPort === 23) {
                    $status = 'Suspicious';
                } elseif (str_contains($lowerInfo, 'tunnel') || str_contains($lowerInfo, 'suspicious') || str_contains($lowerInfo, 'fail') || str_contains($lowerInfo, 'error') || str_contains($lowerInfo, 'nmap')) {
                    $status = 'Suspicious';
                }

                $log = PacketCaptureLog::create([
                    'source_ip' => $srcIp,
                    'destination_ip' => $dstIp,
                    'source_port' => $srcPort,
                    'destination_port' => $dstPort,
                    'protocol' => $protocol,
                    'packet_size' => $len,
                    'info' => $info,
                    'status' => $status,
                    'direction' => $direction,
                    'captured_at' => now(),
                ]);

                $logData = $log->toArray();
                $logData['captured_at_formatted'] = $log->captured_at->format('H:i:s');
                $logData['captured_at_human'] = $log->captured_at->diffForHumans();
                $logData['status_color'] = $log->status_color;
                $logData['status_icon'] = $log->status_icon;

                echo "data: " . json_encode($logData) . "\n\n";
                ob_flush();
                flush();

                $packetCount++;
                if ($packetCount >= $limit) {
                    break;
                }
            }

            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_terminate($process);
            proc_close($process);

            echo "event: end\n";
            echo "data: " . json_encode(['message' => 'Capture session completed']) . "\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
