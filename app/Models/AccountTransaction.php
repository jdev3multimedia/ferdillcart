<?php

namespace App\Models;

use App\Models\Store;
use App\Models\DeliveryMan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class   AccountTransaction extends Model
{
    use HasFactory;

    protected $casts = [
        'amount' => 'float',
        'current_balance' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // public function getStoreAttribute()
    // {
    //     if($this->from_type == 'store'){
    //         return Store::find($this->from_id);
    //     }
    //     return null;
    // }

    // public function getDeliverymanAttribute()
    // {
    //     if($this->from_type == 'deliveryman'){
    //         return DeliveryMan::find($this->from_id);
    //     }
    //     return null;
    // }

    public function store()
    {
        if ($this->from_type == 'store') {
            return $this->belongsTo(Store::class,'from_id','vendor_id');
        }
        return $this->belongsTo(Store::class)->whereNull('id');
    }

    public function deliveryman()
    {
        if ($this->from_type == 'deliveryman') {
            return $this->belongsTo(DeliveryMan::class,'from_id','id');
        }
        return $this->belongsTo(DeliveryMan::class)->whereNull('id');
    }



}
