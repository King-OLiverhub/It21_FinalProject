<?php

namespace App\Console\Commands;

use App\Services\NetworkDeviceScanner;
use Illuminate\Console\Command;

class ScanNetworkDevices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'network:scan {--subnet : Perform a full /24 subnet scan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan the network and update device statuses in inventory';

    /**
     * Execute the console command.
     */
    public function handle(NetworkDeviceScanner $scanner)
    {
        $this->info("Scanning network devices...");
        $scanSubnet = $this->option('subnet');

        if ($scanSubnet) {
            $this->warn("Full subnet scan requested. This might take a few seconds...");
        }

        $results = $scanner->scanAndSync($scanSubnet);

        $this->table(
            ['Metric', 'Value'],
            [
                ['Target IPs Checked', $results['scanned_ips']],
                ['Active Hosts Found', $results['active_hosts']],
                ['New Devices Discovered', $results['inserted']],
                ['Existing Devices Updated', $results['updated']],
                ['Devices Marked Offline', $results['marked_offline']],
            ]
        );

        $this->info("Network scan completed successfully.");
    }
}
