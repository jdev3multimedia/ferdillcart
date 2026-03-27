<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Review extends Model
{
    use HasFactory;
    protected $casts = [
        'item_id' => 'integer',
        'user_id' => 'integer',
        'order_id' => 'integer',
        'rating' => 'integer',
        'store_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function scopeModule($query, $module_id)
    {
        return $query->where('module_id', $module_id);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status',1);
    }

    protected static function boot()
    {
        parent::boot();
        static::saved(function ($review) {
            if($review->review_id == null){
                $review->review_id = $review->generateReviewId($review->order_id);
                $review->save();
            }
        });
    }
    private function generateReviewId($id)
    {
        $review_id = Str::slug($id);
        if ($max_review_id = static::where('review_id', 'like',"{$review_id}%")->latest('id')->value('review_id')) {

            if($max_review_id == $review_id) return "{$review_id}-2";

            $max_review_id = explode('-',$max_review_id);
            $count = array_pop($max_review_id);
            if (isset($count) && is_numeric($count)) {
                $max_review_id[]= ++$count;
                return implode('-', $max_review_id);
            }
        }
        return $review_id;
    }
}
