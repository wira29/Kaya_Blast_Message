<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = ['brand_id', 'name', 'description'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function affiliates()
    {
        return $this->hasMany(Affiliate::class);
    }

    public function blastHistories()
    {
        return $this->hasMany(BlastHistory::class);
    }

    public function blastSchedules()
    {
        return $this->hasMany(BlastSchedule::class);
    }
}
