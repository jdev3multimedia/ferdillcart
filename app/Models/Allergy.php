<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    use HasFactory;

    protected $fillable = ['allergy'];

    public function items()
    {
        return $this->belongsToMany(Item::class)->using('App\Models\AllergyItem');
    }

    public function item_campaigns()
    {
        return $this->belongsToMany(ItemCampaign::class)->using('App\Models\AllergyItemCampaign');
    }
}
