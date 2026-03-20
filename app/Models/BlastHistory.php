<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlastHistory extends Model
{
    protected $fillable = [
        'campaign_id',
        'message_type',
        'message_content',
        'total_affiliate',
        'success_count',
        'failed_count',
        'status',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
