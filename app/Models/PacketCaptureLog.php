<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PacketCaptureLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_ip',
        'destination_ip',
        'source_port',
        'destination_port',
        'protocol',
        'packet_size',
        'info',
        'status',
        'direction',
        'captured_at',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
    ];

    /**
     * Scope: filter by status
     */
    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: filter by protocol
     */
    public function scopeOfProtocol($query, $protocol)
    {
        return $query->where('protocol', $protocol);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Malicious'  => 'danger',
            'Suspicious' => 'warning',
            default      => 'success',
        };
    }

    /**
     * Get status icon
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'Malicious'  => 'fa-skull-crossbones',
            'Suspicious' => 'fa-exclamation-triangle',
            default      => 'fa-check-circle',
        };
    }
}
