<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ItemCampaignGenericName extends Pivot
{
    use HasFactory;
    protected $table = 'item_campaign_generic_names';
    protected $casts = [
        'id'=>'integer',
        'item_id'=>'integer',
        'generic_name_id'=>'integer'
    ];
}
