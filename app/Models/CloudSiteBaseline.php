<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CloudSiteBaseline extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'url',
        'label',
        'provider',
        'baseline_data',
        'saved_at',
    ];

    protected $casts = [
        'baseline_data' => 'array',
        'saved_at'      => 'datetime',
    ];

    /**
     * The user who saved this baseline.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
