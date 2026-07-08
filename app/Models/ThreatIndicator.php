<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreatIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'indicator_type',
        'value',
        'severity',
        'confidence_score',
        'threat_data',
        'source',
        'description',
        'tags',
        'country',
        'city',
        'latitude',
        'longitude',
        'reports_count',
        'detected_at',
    ];

    protected $casts = [
        'threat_data' => 'array',
        'tags' => 'array',
        'detected_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function alerts()
    {
        return $this->hasMany(Alert::class);
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

    public function getSeverityBadgeAttribute()
    {
        return match($this->severity) {
            'Critical' => 'bg-danger',
            'High' => 'bg-warning',
            'Medium' => 'bg-info',
            'Low' => 'bg-success',
            default => 'bg-secondary',
        };
    }

    // Scopes
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('indicator_type', $type);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('detected_at', '>=', now()->subDays($days));
    }
}