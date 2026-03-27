<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParcelCancellation extends Model
{
    use HasFactory;

        protected $casts = [
        'order_id'=> 'integer',
        'return_otp' => 'integer',
        'return_fee' => 'float',
        'dm_penalty_fee' => 'float',
        'before_pickup' => 'integer',
        'set_return_date' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getReasonAttribute($value)
    {
        return json_decode($value, true);
    }
}
