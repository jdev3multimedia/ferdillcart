<?php

namespace App\Models;

use App\Traits\DemoMaskable;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use DemoMaskable;
    
    protected $casts = [
        'user_id' => 'integer',
        'zone_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
