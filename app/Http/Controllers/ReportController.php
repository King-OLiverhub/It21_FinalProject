<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\Alert;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display reports summary page.
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'daily'); // daily, weekly, monthly

        $servers = Server::where('status', '!=', 'archived')->get();
        $alerts = Alert::orderBy('created_at', 'desc')->get();

        // Calculate aggregate statistics
        $totalServers = $servers->count();
        $activeServers = $servers->where('status', 'online')->count();
        
        $avgCpu = $servers->where('status', 'online')->avg('cpu_usage') ?: 0;
        $avgRam = $servers->where('status', 'online')->avg('memory_usage') ?: 0;
        $totalStorageUsed = $servers->sum('storage_used');
        $totalStorageCapacity = $servers->sum('storage_total');
        
        $totalMonthlyCost = $servers->sum('monthly_cost');
        $totalDailyUsage = $servers->sum('daily_usage');

        // Dynamic simulated trends based on report type
        $trends = [];
        if ($type === 'daily') {
            $trends = [
                'period' => 'Last 24 Hours',
                'summary' => 'System performance remained stable. Security scans completed with no critical vulnerabilities, but GCP CPU threshold was exceeded.',
                'metrics' => [
                    'Peak CPU Load' => '88% (GCP-Kubernetes-Cluster)',
                    'Average Response Time' => '34ms',
                    'Total Outgoing Network Traffic' => '377.8 GB',
                    'Failed Login Warnings' => '3 instances',
                    'System Backup Status' => 'Completed successfully at 04:00 AM',
                ]
            ];
        } elseif ($type === 'weekly') {
            $trends = [
                'period' => 'Last 7 Days',
                'summary' => 'Weekly operational review shows resource usage spiked on mid-week. Average bandwidth consumption grew by 12.4% compared to the previous week.',
                'metrics' => [
                    'Average CPU Load' => round($avgCpu, 1) . '%',
                    'Average RAM Load' => round($avgRam, 1) . '%',
                    'Failed Logins (Weekly Total)' => '14 attempts',
                    'Total Weekly Traffic' => '2.64 TB',
                    'Triggered Alerts Count' => $alerts->count(),
                    'Budget Usage Level' => 'On track (34% consumed)',
                ]
            ];
        } else { // monthly
            $trends = [
                'period' => 'Last 30 Days',
                'summary' => 'Monthly cost audit. Total cloud budget spent is $' . number_format($totalMonthlyCost, 2) . '. All cloud virtual machines are scaled appropriately, with a recommendations to resize Azure-VM-Gateway.',
                'metrics' => [
                    'Total Cloud Spend' => '$' . number_format($totalMonthlyCost, 2),
                    'Daily Average Burn Rate' => '$' . number_format($totalDailyUsage, 2),
                    'Estimated Budget Remaining' => '$' . number_format($servers->sum('budget_remaining'), 2),
                    'Peak Bandwidth Consumption' => '410.5 MB/s',
                    'Incident SLA Adherence' => '99.4%',
                ]
            ];
        }

        return view('reports.index', compact('type', 'servers', 'alerts', 'trends', 'totalServers', 'activeServers', 'avgCpu', 'avgRam', 'totalStorageUsed', 'totalStorageCapacity', 'totalMonthlyCost', 'totalDailyUsage'));
    }
}
