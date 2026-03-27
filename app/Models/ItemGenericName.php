<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ItemGenericName extends Pivot
{
    use HasFactory;
    protected $table = 'item_generic_names';
    protected $casts = [
        'id'=>'integer',
        'item_id'=>'integer',
        'generic_name_id'=>'integer'
    ];
}
