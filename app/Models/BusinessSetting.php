<?php

namespace App\Models;

use App\CentralLogics\Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BusinessSetting extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'key',
        'value'
    ];
    public function storage()
    {
        return $this->morphMany(Storage::class, 'data');
    }
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    protected static function booted(): void
    {
        static::addGlobalScope('storage', function ($builder) {
            $builder->with('storage');
        });

    }

    protected static function boot()
    {
        parent::boot();
        static::saved(function ($model) {
             Helpers::deleteCacheData('business_settings_all_data');
            $value = Helpers::getDisk();

            DB::table('storages')->updateOrInsert([
                'data_type' => get_class($model),
                'data_id' => $model->id,
            ], [
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        static::created(function () {
            Helpers::deleteCacheData('business_settings_all_data');
        });
        static::deleted(function(){
            Helpers::deleteCacheData('business_settings_all_data');
        });

        static::updated(function(){
            Helpers::deleteCacheData('business_settings_all_data');
        });

    }

}
