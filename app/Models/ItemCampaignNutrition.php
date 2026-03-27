<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ItemCampaignNutrition extends Pivot
{
    use HasFactory;
    protected $casts = [
        'id'=>'integer',
        'item_id'=>'integer',
        'nutrition_id'=>'integer'
    ];
}
