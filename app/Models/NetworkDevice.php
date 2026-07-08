<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_name',
        'ip_address',
        'mac_address',
        'device_type',
        'status',
        'firmware_version',
        'location',
        'last_scanned_at',
    ];

    protected $casts = [
        'last_scanned_at' => 'datetime',
    ];
}
