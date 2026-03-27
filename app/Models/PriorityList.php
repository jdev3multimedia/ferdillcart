<?php

namespace App\Models;

use App\CentralLogics\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriorityList extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Helpers::deleteCacheData('priority_settings_all_data');
        });

        static::deleted(function () {
            Helpers::deleteCacheData('priority_settings_all_data');
        });

    }
}
