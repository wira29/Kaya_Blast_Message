<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlastSchedule extends Model
{
    protected $fillable = [
        'campaign_id',
        'message_type',
        'message_content',
        'frequency',
        'schedule_time',
        'schedule_day',
        'next_run_at',
        'last_run_at',
        'is_active',
    ];

    protected $casts = [
        'next_run_at' => 'datetime',
        'last_run_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
