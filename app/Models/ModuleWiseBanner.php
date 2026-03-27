<?php

namespace App\Models;

use App\CentralLogics\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;

class ModuleWiseBanner extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => 'integer',
        'module_id' => 'integer',
    ];

    protected $fillable = ['module_id', 'key', 'type', 'value'];

    protected $appends = ['value_full_url'];

    public function scopeModule($query, $module_id)
    {
        return $query->where('module_id', $module_id);
    }

    public function module()
    {
        return $this->belongsTo(Module::class,'module_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }


    public function getValueAttribute($value){
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] == $this->key) {
                    return $translation['value'];
                }
            }
        }
        return $value;
    }

    public function getValueFullUrlAttribute(){
        $value = $this->value;
        if (count($this->storage) > 0) {
            foreach ($this->storage as $storage) {
                if ($storage['key'] == 'value') {
                    return Helpers::get_full_url('promotional_banner',$value,$storage['value']);
                }
            }
        }

        return Helpers::get_full_url('promotional_banner',$value,'public');
    }

    public function storage()
    {
        return $this->morphMany(Storage::class, 'data');
    }
    protected static function booted()
    {
        static::addGlobalScope('storage', function ($builder) {
            $builder->with('storage');
        });
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function($query){
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }

    protected static function boot()
    {
        parent::boot();
        static::saved(function ($model) {
            $value = Helpers::getDisk();

            DB::table('storages')->updateOrInsert([
                'data_type' => get_class($model),
                'data_id' => $model->id,
                'key' => 'value',
            ], [
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

    }
}
