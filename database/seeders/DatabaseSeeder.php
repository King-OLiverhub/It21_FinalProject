<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Server;
use App\Models\Alert;
use App\Models\NetworkDevice;
use App\Models\PacketCaptureLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed users if they do not exist
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'System Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ]);
        }

        

        if (!User::where('email', 'user@example.com')->exists()) {
            User::create([
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SECURITY_ANALYST,
                'is_active' => true,
            ]);
        }

        // 2. Seed Servers
        Server::truncate();

        $servers = [
            [
                'name' => 'AWS-EC2-Production',
                'ip_address' => '54.210.45.188',
                'provider' => 'AWS',
                'status' => 'online',
                'cpu_usage' => 67,
                'memory_usage' => 54,
                'storage_used' => 2.30,
                'storage_total' => 5.00,
                'virtual_machines' => 4,
                'databases' => 2,
                'running_applications' => 8,
                'bandwidth_usage' => 185.50, // MB/s
                'incoming_traffic' => 120.40, // GB/day
                'outgoing_traffic' => 95.20,  // GB/day
                'response_time' => 42, // ms
                'failed_logins' => 0,
                'firewall_status' => 'Active',
                'monthly_cost' => 180.00,
                'daily_usage' => 6.00,
                'budget_remaining' => 320.00,
            ],
            [
                'name' => 'Azure-VM-Gateway',
                'ip_address' => '13.64.105.22',
                'provider' => 'Azure',
                'status' => 'online',
                'cpu_usage' => 45,
                'memory_usage' => 62,
                'storage_used' => 0.85,
                'storage_total' => 2.00,
                'virtual_machines' => 2,
                'databases' => 1,
                'running_applications' => 4,
                'bandwidth_usage' => 95.20,
                'incoming_traffic' => 65.10,
                'outgoing_traffic' => 40.80,
                'response_time' => 58,
                'failed_logins' => 3, // some failed logins for testing security alerts!
                'firewall_status' => 'Active',
                'monthly_cost' => 140.00,
                'daily_usage' => 4.60,
                'budget_remaining' => 160.00,
            ],
            [
                'name' => 'GCP-Kubernetes-Cluster',
                'ip_address' => '35.240.12.18',
                'provider' => 'GCP',
                'status' => 'online',
                'cpu_usage' => 88, // triggers high CPU alerts!
                'memory_usage' => 78,
                'storage_used' => 4.10,
                'storage_total' => 4.50, // triggers low storage alerts!
                'virtual_machines' => 8,
                'databases' => 3,
                'running_applications' => 15,
                'bandwidth_usage' => 340.10,
                'incoming_traffic' => 280.50,
                'outgoing_traffic' => 240.20,
                'response_time' => 35,
                'failed_logins' => 1,
                'firewall_status' => 'Restricted',
                'monthly_cost' => 250.00,
                'daily_usage' => 8.30,
                'budget_remaining' => 50.00,
            ],
            [
                'name' => 'Local-Dev-Workstation',
                'ip_address' => '127.0.0.1',
                'provider' => 'Local',
                'status' => 'online',
                'cpu_usage' => 28,
                'memory_usage' => 42,
                'storage_used' => 0.45,
                'storage_total' => 1.00,
                'virtual_machines' => 1,
                'databases' => 1,
                'running_applications' => 5,
                'bandwidth_usage' => 12.40,
                'incoming_traffic' => 2.10,
                'outgoing_traffic' => 1.80,
                'response_time' => 2,
                'failed_logins' => 0,
                'firewall_status' => 'Active',
                'monthly_cost' => 0.00,
                'daily_usage' => 0.00,
                'budget_remaining' => 0.00,
            ],
            [
                'name' => 'AWS-S3-Backup-Archive',
                'ip_address' => '54.210.46.22',
                'provider' => 'AWS',
                'status' => 'offline', // offline server to test status
                'cpu_usage' => 0,
                'memory_usage' => 0,
                'storage_used' => 12.50,
                'storage_total' => 50.00,
                'virtual_machines' => 0,
                'databases' => 0,
                'running_applications' => 0,
                'bandwidth_usage' => 0.00,
                'incoming_traffic' => 0.00,
                'outgoing_traffic' => 0.00,
                'response_time' => 0,
                'failed_logins' => 0,
                'firewall_status' => 'Disabled',
                'monthly_cost' => 50.00,
                'daily_usage' => 1.60,
                'budget_remaining' => 150.00,
            ],
        ];

        foreach ($servers as $srv) {
            Server::create($srv);
        }

        // 3. Seed some alerts
        Alert::truncate();
        Alert::create([
            'alert_type' => 'High CPU',
            'severity' => 'High',
            'message' => 'Server GCP-Kubernetes-Cluster CPU usage is at 88%, exceeding the threshold of 80%.',
            'recommendation' => 'Check active processes or scale up the cluster.',
            'is_read' => false,
            'is_resolved' => false,
        ]);

        Alert::create([
            'alert_type' => 'Low Storage',
            'severity' => 'Medium',
            'message' => 'Server GCP-Kubernetes-Cluster storage is running low: 4.1 TB of 4.5 TB used (91%).',
            'recommendation' => 'Expand disk partition size or purge temporary log directories.',
            'is_read' => false,
            'is_resolved' => false,
        ]);

        Alert::create([
            'alert_type' => 'Server Offline',
            'severity' => 'Critical',
            'message' => 'Server AWS-S3-Backup-Archive has gone offline. Failed ping check.',
            'recommendation' => 'Check network gateway route or restart interface.',
            'is_read' => false,
            'is_resolved' => false,
        ]);

        Alert::create([
            'alert_type' => 'Security Alert',
            'severity' => 'High',
            'message' => 'Multiple failed login attempts (3) detected on Azure-VM-Gateway.',
            'recommendation' => 'Investigate the offending source IP and verify login security rules.',
            'is_read' => false,
            'is_resolved' => false,
        ]);

        // Seed Network Devices
        NetworkDevice::truncate();
        
        $devices = [
            [
                'device_name' => 'Gateway-Router-1',
                'ip_address' => '192.168.1.1',
                'mac_address' => '00:1A:2B:3C:4D:5E',
                'device_type' => 'Router',
                'status' => 'Active',
                'firmware_version' => 'v15.2.4',
                'location' => 'Core Rack A-01',
                'last_scanned_at' => now()->subMinutes(12),
            ],
            [
                'device_name' => 'Core-Switch-Stack',
                'ip_address' => '192.168.1.2',
                'mac_address' => '00:1A:2B:3C:4D:5F',
                'device_type' => 'Switch',
                'status' => 'Active',
                'firmware_version' => 'v12.2.55',
                'location' => 'Core Rack A-02',
                'last_scanned_at' => now()->subMinutes(15),
            ],
            [
                'device_name' => 'Corporate-Firewall',
                'ip_address' => '192.168.1.254',
                'mac_address' => '00:1A:2B:3C:4D:60',
                'device_type' => 'Firewall',
                'status' => 'Active',
                'firmware_version' => 'v6.4.8',
                'location' => 'DMZ Rack B-01',
                'last_scanned_at' => now()->subMinutes(5),
            ],
            [
                'device_name' => 'AP-Office-West',
                'ip_address' => '192.168.2.10',
                'mac_address' => '00:1A:2B:3C:4D:61',
                'device_type' => 'Access Point',
                'status' => 'Maintenance',
                'firmware_version' => 'v8.10.130',
                'location' => '2nd Floor Ceiling West',
                'last_scanned_at' => now()->subDays(1),
            ],
            [
                'device_name' => 'AP-Office-East',
                'ip_address' => '192.168.2.11',
                'mac_address' => '00:1A:2B:3C:4D:62',
                'device_type' => 'Access Point',
                'status' => 'Active',
                'firmware_version' => 'v8.10.130',
                'location' => '2nd Floor Ceiling East',
                'last_scanned_at' => now()->subMinutes(20),
            ],
        ];

        foreach ($devices as $dev) {
            NetworkDevice::create($dev);
        }

        // Seed Packet Capture Logs
        PacketCaptureLog::truncate();

        $packets = [
            // Normal traffic
            ['source_ip' => '192.168.1.10', 'destination_ip' => '8.8.8.8',       'source_port' => 52341, 'destination_port' => 53,   'protocol' => 'DNS',   'packet_size' => 74,   'info' => 'Standard DNS query for google.com',                   'status' => 'Normal',     'direction' => 'Outbound', 'captured_at' => now()->subMinutes(2)],
            ['source_ip' => '8.8.8.8',       'destination_ip' => '192.168.1.10', 'source_port' => 53,   'destination_port' => 52341,'protocol' => 'DNS',   'packet_size' => 90,   'info' => 'DNS response: A record 142.250.80.46',                'status' => 'Normal',     'direction' => 'Inbound',  'captured_at' => now()->subMinutes(2)],
            ['source_ip' => '192.168.1.15', 'destination_ip' => '13.64.105.22',  'source_port' => 63100, 'destination_port' => 443,  'protocol' => 'HTTPS', 'packet_size' => 512,  'info' => 'TLS 1.3 Application Data to Azure-VM-Gateway',        'status' => 'Normal',     'direction' => 'Outbound', 'captured_at' => now()->subMinutes(4)],
            ['source_ip' => '192.168.1.1',  'destination_ip' => '192.168.1.255', 'source_port' => 68,   'destination_port' => 67,   'protocol' => 'UDP',   'packet_size' => 342,  'info' => 'DHCP Broadcast Request from Gateway-Router-1',        'status' => 'Normal',     'direction' => 'Internal', 'captured_at' => now()->subMinutes(6)],
            ['source_ip' => '192.168.1.20', 'destination_ip' => '54.210.45.188', 'source_port' => 49200, 'destination_port' => 22,   'protocol' => 'TCP',   'packet_size' => 148,  'info' => 'SSH Session Established [SYN-ACK] to AWS-EC2',        'status' => 'Normal',     'direction' => 'Outbound', 'captured_at' => now()->subMinutes(8)],
            ['source_ip' => '192.168.1.1',  'destination_ip' => '192.168.1.10',  'source_port' => 0,    'destination_port' => 0,    'protocol' => 'ICMP',  'packet_size' => 64,   'info' => 'ICMP Echo Request (ping) from router to host',        'status' => 'Normal',     'direction' => 'Internal', 'captured_at' => now()->subMinutes(10)],
            ['source_ip' => '192.168.1.25', 'destination_ip' => '35.240.12.18',  'source_port' => 55900, 'destination_port' => 80,   'protocol' => 'HTTP',  'packet_size' => 890,  'info' => 'GET /api/health HTTP/1.1 → GCP-Kubernetes',           'status' => 'Normal',     'direction' => 'Outbound', 'captured_at' => now()->subMinutes(12)],
            // Suspicious traffic
            ['source_ip' => '203.0.113.47', 'destination_ip' => '192.168.1.254', 'source_port' => 61200, 'destination_port' => 3389, 'protocol' => 'TCP',   'packet_size' => 1480, 'info' => 'Repeated RDP SYN packets — possible brute-force scan', 'status' => 'Suspicious', 'direction' => 'Inbound',  'captured_at' => now()->subMinutes(14)],
            ['source_ip' => '192.168.1.88', 'destination_ip' => '198.51.100.12', 'source_port' => 42000, 'destination_port' => 4444, 'protocol' => 'TCP',   'packet_size' => 220,  'info' => 'Outbound connection to known C2 port 4444',           'status' => 'Suspicious', 'direction' => 'Outbound', 'captured_at' => now()->subMinutes(17)],
            ['source_ip' => '10.0.0.99',   'destination_ip' => '192.168.1.254', 'source_port' => 55120, 'destination_port' => 53,   'protocol' => 'DNS',   'packet_size' => 512,  'info' => 'Unusually large DNS query — possible DNS tunneling',   'status' => 'Suspicious', 'direction' => 'Inbound',  'captured_at' => now()->subMinutes(20)],
            ['source_ip' => '192.168.2.55', 'destination_ip' => '192.168.1.2',   'source_port' => 33445, 'destination_port' => 23,   'protocol' => 'TCP',   'packet_size' => 60,   'info' => 'Telnet connection attempt to Core-Switch (plain text)', 'status' => 'Suspicious', 'direction' => 'Internal', 'captured_at' => now()->subMinutes(25)],
            // Malicious traffic
            ['source_ip' => '45.33.32.156', 'destination_ip' => '192.168.1.1',   'source_port' => 12345, 'destination_port' => 22,   'protocol' => 'TCP',   'packet_size' => 1480, 'info' => 'SSH brute-force: 47 failed login attempts in 60s',     'status' => 'Malicious',  'direction' => 'Inbound',  'captured_at' => now()->subMinutes(30)],
            ['source_ip' => '192.168.1.77', 'destination_ip' => '192.168.1.0',   'source_port' => 0,    'destination_port' => 0,    'protocol' => 'ICMP',  'packet_size' => 65500,'info' => 'ICMP flood / Smurf attack — oversized ping payload',  'status' => 'Malicious',  'direction' => 'Internal', 'captured_at' => now()->subMinutes(35)],
            ['source_ip' => '192.168.1.45', 'destination_ip' => '255.255.255.255','source_port' => 137,  'destination_port' => 137,  'protocol' => 'UDP',   'packet_size' => 78,   'info' => 'NetBIOS name query storm — possible NBNS spoofing',    'status' => 'Malicious',  'direction' => 'Internal', 'captured_at' => now()->subMinutes(40)],
            ['source_ip' => '198.51.100.88','destination_ip' => '192.168.1.254', 'source_port' => 80,   'destination_port' => 443,  'protocol' => 'HTTPS', 'packet_size' => 4096, 'info' => 'Encrypted payload from blacklisted IP — flagged by IDS','status' => 'Malicious',  'direction' => 'Inbound',  'captured_at' => now()->subMinutes(45)],
        ];

        foreach ($packets as $pkt) {
            PacketCaptureLog::create($pkt);
        }
    }
}
