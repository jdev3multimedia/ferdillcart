<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurgePriceDate extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'surge_price_id',
        'zone_id',
        'module_id',
        'applicable_date',
        'start_time',
        'end_time',
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function surge_price()
    {
        return $this->belongsTo(SurgePrice::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

}
