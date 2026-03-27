<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SurgePrice extends Model
{
    use HasFactory;

    protected $casts = [
        'custom_days' => 'array',
        'custom_times' => 'array',
        'weekly_days' => 'array',
        'module_ids' => 'array',
        'customer_note_status' => 'integer',
    ];

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function details()
    {
        return $this->hasMany(SurgePriceDate::class, 'surge_price_id');
    }

    public function getSurgePriceNameAttribute($value){
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] == 'surge_price_name') {
                    return $translation['value'];
                }
            }
        }

        return $value;
    }
    public function getCustomerNoteAttribute($value){
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] == 'customer_note') {
                    return $translation['value'];
                }
            }
        }

        return $value;
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    protected static function booted()
    {
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function($query){
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }
}
