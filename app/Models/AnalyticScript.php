<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\CentralLogics\Helpers;

class AnalyticScript extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'name' => 'string',
        'type' => 'string',
        'script_id' => 'string',
        'script' => 'string',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    protected static function boot(): void
    {
        parent::boot();
        static::saved(function ($model) {
            Helpers::deleteCacheData('analytic_script');
        });

        static::deleted(function ($model) {
            Helpers::deleteCacheData('analytic_script');
        });
    }
}
