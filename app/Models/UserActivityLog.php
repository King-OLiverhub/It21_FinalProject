<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_type',
        'ip_address',
        'details',
        'activity_at',
    ];

    protected $casts = [
        'activity_at' => 'datetime',
    ];

    /**
     * Get the user associated with this activity log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
