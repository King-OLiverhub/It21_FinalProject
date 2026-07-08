<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'threat_indicator_id',
        'alert_type',
        'severity',
        'message',
        'recommendation',
        'is_read',
        'is_resolved',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function threatIndicator()
    {
        return $this->belongsTo(ThreatIndicator::class);
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public function resolve($userId)
    {
        $this->update([
            'is_resolved' => true,
            'resolved_by' => $userId,
            'resolved_at' => now(),
        ]);
    }

    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            'Critical' => 'danger',
            'High' => 'warning',
            'Medium' => 'info',
            'Low' => 'success',
            default => 'secondary',
        };
    }
}