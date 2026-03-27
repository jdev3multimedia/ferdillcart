<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\ZoneScope;

class SubscriptionTransaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'package_details' => 'array',
        'id'=> 'string',
        'chat'=>'integer',
        'review'=>'integer',
        'package_id'=>'integer',
        'store_id'=>'integer',
        'status'=>'integer',
        'self_delivery'=>'integer',
        'max_order'=>'string',
        'max_product'=>'string',
        'payment_method'=>'string',
        'paid_amount'=>'float',
        'validity'=>'integer',
        'is_trial'=>'integer',
        'store_subscription_id'=>'integer',

    ];

    public function store()
    {
        return $this->hasOne(Store::class,'id', 'store_id')->withoutGlobalScopes();
    }
    public function package()
    {
        return $this->belongsTo(SubscriptionPackage::class, 'package_id', 'id')->withoutGlobalScopes();
    }
    public function subscription()
    {
        return $this->belongsTo(StoreSubscription::class, 'store_subscription_id');
    }
    protected static function booted()
    {
        static::addGlobalScope(new ZoneScope);
    }

}
