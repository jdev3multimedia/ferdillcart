<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Illuminate\Database\Eloquent\Builder;
use App\Scopes\ZoneScope;

/**
 * Class Zone
 *
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property mixed $coordinates
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $store_wise_topic
 * @property string|null $customer_wise_topic
 * @property string|null $deliveryman_wise_topic
 * @property int $cash_on_delivery
 * @property int $digital_payment
 * @property float $increased_delivery_fee
 * @property int $increased_delivery_fee_status
 * @property string|null $increase_delivery_charge_message
 * @property int $offline_payment
 * @property boolean $is_default
 */
class Zone extends Model
{
    use HasFactory;
    use HasSpatial;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $with = ['translations'];
    protected $fillable = [
        'name',
        'display_name',
        'coordinates',
        'status',
        'store_wise_topic',
        'customer_wise_topic',
        'deliveryman_wise_topic',
        'cash_on_delivery',
        'digital_payment',
        'increased_delivery_fee',
        'increased_delivery_fee_status',
        'increase_delivery_charge_message',
        'offline_payment',
        'is_default'
    ];

    protected $casts = [
        'status' => 'integer',
        'increased_delivery_fee_status' => 'integer',
        'increased_delivery_fee' => 'integer',
        'cash_on_delivery' => 'boolean',
        'digital_payment' => 'boolean',
        'offline_payment' => 'boolean',
        'fixed_shipping_charge' => 'float',
        'coordinates' => Polygon::class,
        'is_default' => 'boolean',
    ];

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function getNameAttribute($value){
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] == 'name') {
                    return $translation['value'];
                }
            }
        }

        return $value;
    }

    public function getDisplayNameAttribute($value){
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] == 'display_name') {
                    return $translation['value'];
                }
            }
        }

        return $value;
    }

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class);
    }

    public function deliverymen(): HasMany
    {
        return $this->hasMany(DeliveryMan::class);
    }

    public function surge_prices(): HasMany
    {
        return $this->hasMany(SurgePrice::class);
    }

    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(Order::class, Store::class);
    }


    public function campaigns(): HasManyThrough
    {
        return $this->hasManyThrough(Campaigns::class, Store::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function scopeContains($query,$abc){
        return $query->whereRaw("ST_Distance_Sphere(coordinates, POINT({$abc}))");
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ZoneScope);

        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function($query){
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class)->withPivot(['per_km_shipping_charge','minimum_shipping_charge','maximum_shipping_charge','maximum_cod_order_amount','delivery_charge_type','fixed_shipping_charge'])->using('App\Models\ModuleZone');
    }

    public static function query(): Builder
    {
        return parent::query();
    }
}
