<?php

namespace App\Models;

use App\CentralLogics\Helpers;
use Illuminate\Database\Eloquent\Model;

class ItemSeoData extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['image_full_url'];

    protected $casts = [
        'meta_data' => 'array',
    ];



    public function getImageFullUrlAttribute()
    {
        $value = $this->image;
        return Helpers::get_full_url('item_meta_data', $value, 'public');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
