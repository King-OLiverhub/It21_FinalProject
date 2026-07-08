<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\Alert;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the cloud intelligence dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Fetch active servers
        $servers = Server::all();
        
        // Fetch recent unresolved alerts
        $recentAlerts = Alert::where('is_resolved', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Compute summary metrics
        $totalServers = $servers->count();
        $activeServers = $servers->where('status', 'online')->count();
        
        // Averages for online servers
        $onlineServers = $servers->where('status', 'online');
        $avgCpu = $onlineServers->avg('cpu_usage') ?: 0;
        $avgMemory = $onlineServers->avg('memory_usage') ?: 0;
        $totalStorageUsed = $servers->sum('storage_used');
        $monthlyCost = $servers->sum('monthly_cost');

        // Security metrics
        $totalFailedLogins = $servers->sum('failed_logins');
        $firewallRulesActive = $servers->where('firewall_status', 'Active')->count();

        // Network metrics
        $avgResponseTime = $onlineServers->avg('response_time') ?: 0;
        $totalIncomingTraffic = $servers->sum('incoming_traffic');
        $totalOutgoingTraffic = $servers->sum('outgoing_traffic');

        // Cloud aggregates
        $totalVMs = $servers->sum('virtual_machines');
        $totalDatabases = $servers->sum('databases');
        $totalApps = $servers->sum('running_applications');

        $systemStatus = $this->getSystemStatus();

        return view('dashboard', [
            'user' => $user,
            'servers' => $servers,
            'recentAlerts' => $recentAlerts,
            'systemStatus' => $systemStatus,
            'stats' => [
                'total_servers' => $totalServers,
                'active_servers' => $activeServers,
                'avg_cpu' => round($avgCpu, 1),
                'avg_memory' => round($avgMemory, 1),
                'storage_used' => round($totalStorageUsed, 2),
                'monthly_cost' => round($monthlyCost, 2),
                'failed_logins' => $totalFailedLogins,
                'firewall_active' => $firewallRulesActive,
                'response_time' => round($avgResponseTime),
                'incoming_traffic' => round($totalIncomingTraffic, 1),
                'outgoing_traffic' => round($totalOutgoingTraffic, 1),
                'vms' => $totalVMs,
                'databases' => $totalDatabases,
                'applications' => $totalApps,
            ]
        ]);
    }

    /**
     * Get system status information
     */
    protected function getSystemStatus()
    {
        return [
            'status' => 'Operational',
            'uptime' => '12 days, 4 hours',
            'last_check' => now()->format('Y-m-d H:i:s'),
            'services' => [
                'database' => $this->checkDatabaseConnection(),
                'aws_integration' => ['status' => 'Connected', 'icon' => 'success'],
                'azure_integration' => ['status' => 'Connected', 'icon' => 'success'],
            ]
        ];
    }

    /**
     * Check database connection
     */
    protected function checkDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'Connected', 'icon' => 'success'];
        } catch (\Exception $e) {
            return ['status' => 'Error', 'icon' => 'danger'];
        }
    }

    /**
     * Get dashboard data as JSON (for live ticker and widget refreshes)
     */
    public function getData(Request $request)
    {
        $servers = Server::all();
        $liveServers = [];
        
        foreach ($servers as $server) {
            $metrics = $server->getLiveMetrics();
            $liveServers[] = [
                'id' => $server->id,
                'name' => $server->name,
                'provider' => $server->provider,
                'ip' => $server->ip_address,
                'status' => $metrics['status'],
                'cpu' => $metrics['cpu'],
                'ram' => $metrics['ram'],
                'bandwidth' => $metrics['bandwidth'],
                'response_time' => $metrics['response_time'],
                'storage_used' => $server->storage_used,
                'storage_total' => $server->storage_total,
                'monthly_cost' => $server->monthly_cost,
            ];
        }

        // Recompute live aggregates
        $onlineServersCount = collect($liveServers)->where('status', 'online')->count();
        $avgCpu = collect($liveServers)->where('status', 'online')->avg('cpu') ?: 0;
        $avgRam = collect($liveServers)->where('status', 'online')->avg('ram') ?: 0;
        $totalStorage = collect($liveServers)->sum('storage_used');
        $monthlyCost = collect($liveServers)->sum('monthly_cost');

        return response()->json([
            'success' => true,
            'data' => [
                'servers' => $liveServers,
                'aggregates' => [
                    'total_servers' => count($liveServers),
                    'active_servers' => $onlineServersCount,
                    'avg_cpu' => round($avgCpu, 1),
                    'avg_memory' => round($avgRam, 1),
                    'storage_used' => round($totalStorage, 2),
                    'monthly_cost' => round($monthlyCost, 2),
                ],
                'recent_alerts' => Alert::where('is_resolved', false)->orderBy('created_at', 'desc')->limit(10)->get()
            ]
        ]);
    }

    /**
     * Resolve alert action
     */
    public function resolveAlert($id)
    {
        $alert = Alert::findOrFail($id);
        $alert->resolve(Auth::id());

        return response()->json([
            'success' => true,
            'message' => 'Alert successfully acknowledged and resolved.'
        ]);
    }

    /**
     * Simulate a new alert (Admin only trigger)
     */
    public function simulateAlert(Request $request)
    {
        $type = $request->input('type', 'High CPU');
        $serverName = $request->input('server');

        if (!$serverName) {
            $srv = Server::inRandomOrder()->first();
            $serverName = $srv ? $srv->name : 'AWS-EC2-Production';
        }

        $alert = new Alert();
        $alert->alert_type = $type;
        
        if ($type === 'High CPU') {
            $alert->severity = 'High';
            $alert->message = "Server {$serverName} CPU usage spiked to " . rand(85, 99) . "%, exceeding threshold limit (80%).";
            $alert->recommendation = 'Check resource monitor, stop runaway threads, or upgrade instance type.';
        } elseif ($type === 'Low Storage') {
            $alert->severity = 'Medium';
            $alert->message = "Server {$serverName} disk storage capacity has exceeded 90% threshold.";
            $alert->recommendation = 'Run storage cleanup commands or attach a larger storage block.';
        } elseif ($type === 'Server Offline') {
            $alert->severity = 'Critical';
            $alert->message = "Ping sensor reports {$serverName} is unresponsive. Status set to offline.";
            $alert->recommendation = 'Attempt reboot via cloud portal or verify firewall security group definitions.';
        } else {
            $alert->severity = 'High';
            $alert->message = "Failed security verification. Multiple authentication failed logins recorded on {$serverName}.";
            $alert->recommendation = 'Audit access log reports, update credentials, and restrict security group rules.';
        }

        $alert->is_read = false;
        $alert->is_resolved = false;
        $alert->save();

        return response()->json([
            'success' => true,
            'message' => "Simulated '{$type}' alert triggered for {$serverName}."
        ]);
    }

    /**
     * Get historical metrics trends for Chart.js
     */
    public function getTrends(Request $request)
    {
        $days = $request->get('days', 7);
        $dates = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $dates[] = now()->subDays($i)->format('M d');
        }

        // Generate clean mock trends corresponding to seeder parameters
        $servers = Server::all();
        $totalCost = $servers->sum('monthly_cost');

        // CPU trend (last 7 days average fluctuations around 50-60)
        $cpuTrend = [];
        // Storage trend (steadily increasing over 7 days)
        $storageTrend = [];
        // Traffic trend
        $trafficTrend = [];
        
        $baseCpu = 45;
        $baseStorage = 7.7;
        
        for ($i = 0; $i < $days; $i++) {
            $cpuTrend[] = rand($baseCpu - 10, $baseCpu + 15);
            $storageTrend[] = round($baseStorage + ($i * 0.05) + (rand(-1, 2) / 100), 2);
            $trafficTrend[] = rand(400, 650);
        }

        // Cost breakdown by provider for pie/donut chart
        $costBreakdown = [];
        $providers = Server::select('provider', DB::raw('sum(monthly_cost) as total'))
            ->groupBy('provider')
            ->get();
            
        foreach ($providers as $prov) {
            $costBreakdown[$prov->provider] = round($prov->total, 2);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $dates,
                'cpu' => $cpuTrend,
                'storage' => $storageTrend,
                'traffic' => $trafficTrend,
                'cost' => $costBreakdown,
            ]
        ]);
    }
}