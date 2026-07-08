<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreatLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'source_ip',
        'destination_ip',
        'action',
        'details',
        'user_id',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Log activity
    public static function log($eventType, $action, $details = [], $sourceIp = null, $destinationIp = null)
    {
        return self::create([
            'event_type' => $eventType,
            'action' => $action,
            'details' => $details,
            'source_ip' => $sourceIp ?? request()->ip(),
            'destination_ip' => $destinationIp,
            'user_id' => auth()->id(),
        ]);
    }
}