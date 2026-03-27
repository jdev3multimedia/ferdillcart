<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nutrition extends Model
{
    use HasFactory;
    protected $table = 'nutritions';

    protected $fillable = ['nutrition'];

    public function items()
    {
        return $this->belongsToMany(Item::class)->using('App\Models\ItemNutrition');
    }

    public function item_campaigns()
    {
        return $this->belongsToMany(ItemCampaign::class)->using('App\Models\ItemCampaignNutrition');
    }
}
