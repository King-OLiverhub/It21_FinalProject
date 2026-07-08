<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'provider',
        'status',
        'cpu_usage',
        'memory_usage',
        'storage_used',
        'storage_total',
        'virtual_machines',
        'databases',
        'running_applications',
        'bandwidth_usage',
        'incoming_traffic',
        'outgoing_traffic',
        'response_time',
        'failed_logins',
        'firewall_status',
        'monthly_cost',
        'daily_usage',
        'budget_remaining',
    ];

    protected $casts = [
        'cpu_usage' => 'integer',
        'memory_usage' => 'integer',
        'storage_used' => 'float',
        'storage_total' => 'float',
        'virtual_machines' => 'integer',
        'databases' => 'integer',
        'running_applications' => 'integer',
        'bandwidth_usage' => 'float',
        'incoming_traffic' => 'float',
        'outgoing_traffic' => 'float',
        'response_time' => 'integer',
        'failed_logins' => 'integer',
        'monthly_cost' => 'float',
        'daily_usage' => 'float',
        'budget_remaining' => 'float',
    ];

    /**
     * Fluctuates server metrics dynamically to simulate a live system
     */
    public function getLiveMetrics()
    {
        if ($this->status !== 'online') {
            return [
                'cpu' => 0,
                'ram' => 0,
                'bandwidth' => 0,
                'response_time' => 0,
                'status' => 'offline',
            ];
        }

        // Fluctuate CPU usage by +/- 5% (stay between 5% and 98%)
        $cpuChange = rand(-5, 5);
        $cpu = max(5, min(98, $this->cpu_usage + $cpuChange));

        // Fluctuate RAM usage by +/- 2% (stay between 10% and 95%)
        $ramChange = rand(-2, 2);
        $ram = max(10, min(95, $this->memory_usage + $ramChange));

        // Fluctuate bandwidth by +/- 10%
        $bwChange = (rand(-10, 10) / 100) * $this->bandwidth_usage;
        $bandwidth = max(0.1, round($this->bandwidth_usage + $bwChange, 2));

        // Fluctuate response time by +/- 5ms
        $rtChange = rand(-5, 5);
        $responseTime = max(5, $this->response_time + $rtChange);

        return [
            'cpu' => $cpu,
            'ram' => $ram,
            'bandwidth' => $bandwidth,
            'response_time' => $responseTime,
            'status' => 'online',
        ];
    }
}
