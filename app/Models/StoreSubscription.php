<?php

namespace App\Models;

use App\Scopes\ZoneScope;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreSubscription extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    protected $casts = [
        // 'expiry_date'=> 'datetime',
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
        'max_order'=>'string',
        'max_product'=>'string',
    ];

    public function package()
    {
        return $this->belongsTo(SubscriptionPackage::class,'package_id');
    }
    public function transcations()
    {
        return $this->hasMany(SubscriptionTransaction::class,'store_id');
    }
    public function last_transcations()
    {
        return $this->hasOne(SubscriptionTransaction::class,'store_subscription_id')->latestOfMany();
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    protected static function booted()
    {
        static::addGlobalScope(new ZoneScope);
    }
    public function getExpiryDateParsedAttribute($value){
        return Carbon::parse($this->expiry_date) ;
    }
}
