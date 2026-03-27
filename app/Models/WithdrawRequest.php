<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\ZoneScope;

class WithdrawRequest extends Model
{
    use HasFactory;

    protected $casts = [
        'amount'=>'float'
    ];

    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }
    public function store(){
        return $this->belongsTo(Store::class,'vendor_id','vendor_id');
    }
    public function method(){
        return $this->belongsTo(WithdrawalMethod::class,'withdrawal_method_id');
    }


    public function disbursementMethod(){
        return $this->belongsTo(DisbursementWithdrawalMethod::class,'withdrawal_method_id');
    }

    public function deliveryman(){
        return $this->belongsTo(DeliveryMan::class,'delivery_man_id');
    }

    protected static function booted()
    {
        static::addGlobalScope(new ZoneScope);
    }
}
