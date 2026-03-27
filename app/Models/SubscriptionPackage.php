<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class SubscriptionPackage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'price'=>'float',
        'validity'=>'integer',
        'chat'=>'integer',
        'review'=>'integer',
        'package_id'=>'integer',
        'status'=>'integer',
        'pos'=>'integer',
        'default'=>'integer',
        'mobile_app'=>'integer',
        'total_package_renewed'=>'integer',
        'self_delivery'=>'integer',
        'store_id'=>'integer',
        'is_trial'=>'integer',
        'max_order'=>'string',
        'max_product'=>'string',
    ];

    /**
     * @param $query
     * @param $status
     * @return void
     */
    public function scopeOfStatus($query, $status): void
    {
        $query->where('status', '=', $status);
    }

    public function transactions()
    {
        return $this->hasMany(SubscriptionTransaction::class, 'package_id');
    }
    public function currentSubscribers()
    {
        return $this->hasMany(StoreSubscription::class, 'package_id')->where('status' ,1);
    }
    public function Subscribers()
    {
        return $this->hasMany(StoreSubscription::class, 'package_id');
    }

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }
    public function getPackageNameAttribute($value){
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] == 'package_name') {
                    return $translation['value'];
                }
            }
        }
        return $value;
    }
    public function getTextAttribute($value){
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] == 'text') {
                    return $translation['value'];
                }
            }
        }
        return $value;
    }

    protected static function booted()
    {
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }
}
