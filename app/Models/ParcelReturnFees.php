<?php

namespace App\Models;

use App\Traits\ReportFilter;
use Illuminate\Database\Eloquent\Model;

class ParcelReturnFees extends Model
{
    use ReportFilter;
    protected  $guarded = ['id'];

    protected $casts = [
        'delivery_man_id' => 'integer',
        'order_id' => 'integer',
        'user_id' => 'integer',
        'return_fee' => 'float'
    ];

}
