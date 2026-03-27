<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class StoreConfig extends Model
{
    use HasFactory;

    protected $table;
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = Schema::hasTable('storeConfigs') ? 'storeConfigs' : 'store_configs';
    }

    protected $guarded = ['id'];

    protected $casts = [
        'store_id' => 'integer',
        'minimum_stock_for_warning' => 'integer',
        'section_wise_ai_use_count' => 'integer',
        'image_wise_ai_use_count' => 'integer',
        'is_recommended' => 'boolean',
        'is_recommended_deleted' => 'boolean',
        'halal_tag_status' => 'boolean',
        'extra_packaging_status' => 'boolean',
        'extra_packaging_amount' => 'float',
    ];

    public function Store()
    {
        return $this->belongsTo(Store::class);
    }
}
