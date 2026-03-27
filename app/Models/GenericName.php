<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenericName extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_generic_names')->using(ItemGenericName::class);
    }

    public function item_campaigns()
    {
        return $this->belongsToMany(ItemCampaign::class, 'item_campaign_generic_names')->using(ItemCampaignGenericName::class);
    }
}
