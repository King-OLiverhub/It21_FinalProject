<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_name',
        'event_type',
        'severity',
        'description',
        'affected_systems',
        'status',
        'assigned_to',
        'resolved_at',
    ];

    protected $casts = [
        'affected_systems' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'open' => 'danger',
            'in_progress' => 'warning',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'info',
        };
    }
}