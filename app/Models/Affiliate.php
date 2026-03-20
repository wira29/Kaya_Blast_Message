<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{
    protected $fillable = ['campaign_id', 'name', 'phone', 'link'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
