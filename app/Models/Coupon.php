<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * Class Coupon
 *
 * @property int $id
 * @property string $title
 * @property string $code
 * @property Carbon|null $start_date
 * @property Carbon|null $expire_date
 * @property float $min_purchase
 * @property float $max_discount
 * @property float $discount
 * @property string $discount_type
 * @property string $coupon_type
 * @property int|null $limit
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $data
 * @property int $total_uses
 * @property int $module_id
 * @property string $created_by
 * @property string $customer_id
 * @property string|null $slug
 * @property int|null $store_id
 */
class Coupon extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'code',
        'start_date',
        'expire_date',
        'min_purchase',
        'max_discount',
        'discount',
        'discount_type',
        'coupon_type',
        'limit',
        'status',
        'data',
        'total_uses',
        'module_id',
        'created_by',
        'customer_id',
        'slug',
        'store_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'min_purchase' => 'float',
        'max_discount' => 'float',
        'discount' => 'float',
        'limit'=>'integer',
        'store_id'=>'integer',
        'status'=>'integer',
        'id'=>'integer',
        'total_uses'=>'integer',
    ];

    /**
     * @return MorphMany
     */
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getTitleAttribute($value): mixed
    {
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                // dd($translation['key']);
                if ($translation['key'] == 'title') {
                    return $translation['value'];
                }
            }
        }

        return $value;
    }

    /**
     * @return BelongsTo
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * @return BelongsTo
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive($query): mixed
    {
        return $query->where('status', '=', 1);
    }

    /**
     * @param $query
     * @param $module_id
     * @return mixed
     */
    public function scopeModule($query, $module_id): mixed
    {
        return $query->where('module_id', $module_id);
    }

    /**
     * @return void
     */
    protected static function booted(): void
    {
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }
}
