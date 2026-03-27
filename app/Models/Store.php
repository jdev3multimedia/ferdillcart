<?php

namespace App\Models;

use App\CentralLogics\Helpers;
use App\Mail\SubscriptionDeadLineWarning;
use App\Scopes\ZoneScope;
use App\Traits\ReportFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Traits\DemoMaskable;
use Modules\Rental\Entities\Trips;
use Modules\Rental\Entities\TripTransaction;
use Modules\Rental\Entities\Vehicle;
use Modules\Rental\Entities\VehicleDriver;
use Modules\Rental\Entities\VehicleIdentity;
use Modules\Rental\Entities\VehicleReview;
use Modules\TaxModule\Entities\OrderTax;
use Illuminate\Support\Str;

/**
 * Class Store
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string|null $email
 * @property string|null $logo
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $address
 * @property string|null $footer_text
 * @property float $minimum_order
 * @property float|null $comission
 * @property bool $schedule_order
 * @property bool $status
 * @property int $vendor_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool $free_delivery
 * @property string|null $rating
 * @property string|null $cover_photo
 * @property bool $delivery
 * @property bool $take_away
 * @property bool $item_section
 * @property float $tax
 * @property int|null $zone_id
 * @property bool $reviews_section
 * @property bool $active
 * @property string $off_day
 * @property string|null $gst
 * @property bool $self_delivery_system
 * @property bool $pos_system
 * @property float $minimum_shipping_charge
 * @property string|null $delivery_time
 * @property bool $veg
 * @property bool $non_veg
 * @property int $order_count
 * @property int $total_order
 * @property int $module_id
 * @property string $pickup_zone_id
 * @property int $order_place_to_schedule_interval
 * @property bool $featured
 * @property float $per_km_shipping_charge
 * @property bool $prescription_order
 * @property string|null $slug
 * @property float|null $maximum_shipping_charge
 * @property bool $cutlery
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property bool $announcement
 * @property string|null $announcement_message
 * @property string|null $comment
 */
class Store extends Model
{
    use ReportFilter, DemoMaskable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'logo',
        'latitude',
        'longitude',
        'address',
        'footer_text',
        'minimum_order',
        'comission',
        'schedule_order',
        'status',
        'vendor_id',
        'free_delivery',
        'rating',
        'cover_photo',
        'delivery',
        'take_away',
        'item_section',
        'tax',
        'zone_id',
        'reviews_section',
        'active',
        'off_day',
        'gst',
        'self_delivery_system',
        'pos_system',
        'minimum_shipping_charge',
        'delivery_time',
        'veg',
        'non_veg',
        'order_count',
        'total_order',
        'module_id',
        'pickup_zone_id',
        'order_place_to_schedule_interval',
        'featured',
        'per_km_shipping_charge',
        'prescription_order',
        'slug',
        'maximum_shipping_charge',
        'cutlery',
        'meta_title',
        'meta_description',
        'meta_image',
        'announcement',
        'announcement_message',
        'comment',
        'tin',
        'tin_expire_date',
        'tin_certificate_image',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'minimum_order' => 'float',
        'comission' => 'float',
        'tax' => 'float',
        'minimum_shipping_charge' => 'float',
        'maximum_shipping_charge' => 'float',
        'per_km_shipping_charge' => 'float',
        'schedule_order' => 'boolean',
        'free_delivery' => 'boolean',
        'vendor_id' => 'integer',
        'status' => 'integer',
        'delivery' => 'boolean',
        'take_away' => 'boolean',
        'zone_id' => 'integer',
        'module_id' => 'integer',
        'item_section' => 'boolean',
        'reviews_section' => 'boolean',
        'active' => 'boolean',
        'gst_status' => 'boolean',
        'pos_system' => 'boolean',
        'cutlery' => 'boolean',
        'self_delivery_system' => 'integer',
        'open' => 'integer',
        'gst_code' => 'string',
        'off_day' => 'string',
        'gst' => 'string',
        'veg' => 'integer',
        'non_veg' => 'integer',
        'order_place_to_schedule_interval' => 'integer',
        'featured' => 'integer',
        'items_count' => 'integer',
        'prescription_order' => 'boolean',
        'announcement' => 'integer',
        'rating_count' => 'integer',
        'reviews_comments_count' => 'integer',
        'package_id' => 'integer',
        'distance' => 'float',
        'meta_data' => 'array',
    ];

    /**
     * @var string[]
     */
    protected $appends = ['gst_status', 'gst_code', 'logo_full_url', 'cover_photo_full_url', 'meta_image_full_url', 'tin_certificate_image_full_url'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'gst',
    ];

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    /**
     * @return mixed
     */
    public function getNameAttribute($value)
    {
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] == 'name') {
                    return $translation['value'];
                }
            }
        }

        return $value;
    }

    public function getAddressAttribute($value): mixed
    {
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] == 'address') {
                    return $translation['value'];
                }
            }
        }

        return $value;
    }

    public function getSubSelfDeliveryAttribute(): mixed
    {
        if ($this->store_business_model == 'subscription' && isset($this->store_sub)) {
            return (int) $this->store_sub?->self_delivery;
            unset($this->store_sub);
        }

        return $this->self_delivery_system;
    }

    public function getChatPermissionAttribute(): mixed
    {
        if ($this->store_business_model == 'subscription' && isset($this->store_sub)) {
            return (int) $this->store_sub->chat;
            unset($this->store_sub);
        }

        return 0;
    }

    public function getReviewPermissionAttribute(): mixed
    {
        if ($this->store_business_model == 'subscription' && isset($this->store_sub)) {
            return (int) $this->store_sub->review;
            unset($this->store_sub);
        }

        return $this->reviews_section;
    }

    public function getIsValidSubscriptionAttribute(): mixed
    {
        if ($this->store_business_model == 'subscription' && isset($this->store_sub)) {
            return (int) 1;
            unset($this->store_sub);
        }

        return 0;
    }

    public function getModuleTypeAttribute(): mixed
    {
        return $this->module?->module_type;
    }

    public function getProductUploaadCheckAttribute(): mixed
    {
        if ($this->store_business_model == 'subscription' && isset($this->store_sub)) {

            if ($this->store_sub->max_product == 'unlimited') {
                return 'unlimited';
            } else {
                if ($this->module_type == 'rental') {
                    return $this->vehicles()->where('status', 1)->count() - $this->store_sub->max_product;
                }

                return $this->items()->where('status', 1)->withoutGlobalScope(\App\Scopes\StoreScope::class)->count() - $this->store_sub->max_product;
            }
            unset($this->store_sub);
        }

        return 'commission';
    }

    public function getLogoFullUrlAttribute()
    {
        $value = $this->logo;
        if (count($this->storage) > 0) {
            foreach ($this->storage as $storage) {
                if ($storage['key'] == 'logo') {
                    return Helpers::get_full_url('store', $value, $storage['value']);
                }
            }
        }

        return Helpers::get_full_url('store', $value, 'public');
    }

    public function getTinCertificateImageFullUrlAttribute()
    {
        $value = $this->tin_certificate_image;
        if (count($this->storage) > 0) {
            foreach ($this->storage as $storage) {
                if ($storage['key'] == 'tin_certificate_image') {
                    return Helpers::get_full_url('store', $value, $storage['value']);
                }
            }
        }

        return Helpers::get_full_url('store', $value, 'public');
    }

    public function getCoverPhotoFullUrlAttribute()
    {
        $value = $this->cover_photo;
        if (count($this->storage) > 0) {
            foreach ($this->storage as $storage) {
                if ($storage['key'] == 'cover_photo') {
                    return Helpers::get_full_url('store/cover', $value, $storage['value']);
                }
            }
        }

        return Helpers::get_full_url('store/cover', $value, 'public');
    }

    public function getMetaImageFullUrlAttribute()
    {
        $value = $this->meta_image;
        if (count($this->storage) > 0) {
            foreach ($this->storage as $storage) {
                if ($storage['key'] == 'meta_image') {
                    return Helpers::get_full_url('store', $value, $storage['value']);
                }
            }
        }

        return Helpers::get_full_url('store', $value, 'public');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPackage::class, 'package_id');
    }

    public function store_sub(): HasOne
    {
        return $this->hasOne(StoreSubscription::class)->where('status', 1)->latestOfMany();
    }

    public function store_subs(): HasMany
    {
        return $this->hasMany(StoreSubscription::class, 'store_id');
    }

    public function store_sub_trans(): HasOne
    {
        return $this->hasOne(SubscriptionTransaction::class)->latest();
    }

    public function store_all_sub_trans(): HasMany
    {
        return $this->hasMany(SubscriptionTransaction::class);
    }

    public function store_sub_update_application(): HasOne
    {
        return $this->hasOne(StoreSubscription::class)->latestOfMany();
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function itemsForReorder(): HasMany
    {
        return $this->items()->orderby('avg_rating', 'desc')->orderby('recommended', 'desc');
    }

    public function activeCoupons(): HasMany
    {
        return $this->hasMany(Coupon::class)->where('status', '=', 1)->whereDate('expire_date', '>=', date('Y-m-d'))->whereDate('start_date', '<=', date('Y-m-d'));
    }

    public function coupon(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(StoreSchedule::class)->orderBy('opening_time');
    }

    public function deliverymen(): HasMany
    {
        return $this->hasMany(DeliveryMan::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trips::class, 'provider_id');
    }

    public function todays_trip_earning()
    {
        return $this->hasMany(TripTransaction::class, 'provider_id')->whereDate('created_at', now());
    }

    public function this_week_trip_earning()
    {
        return $this->hasMany(TripTransaction::class, 'provider_id')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    public function this_month_trip_earning()
    {
        return $this->hasMany(TripTransaction::class, 'provider_id')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));
    }

    public function discount(): HasOne
    {
        return $this->hasOne(Discount::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function getPickupZones()
    {
        return Zone::whereIn('id', json_decode($this->pickup_zone_id))->get();
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class);
    }

    public function itemCampaigns(): HasMany
    {
        return $this->hasMany(ItemCampaign::class);
    }

    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(Review::class, Item::class);
    }

    public function vehicle_reviews(): HasMany
    {
        return $this->hasMany(VehicleReview::class, 'provider_id');
    }

    public function reviews_comments()
    {
        return $this->reviews()->whereNotNull('comment');
    }

    public function disbursement_method(): HasOne
    {
        return $this->hasOne(DisbursementWithdrawalMethod::class)->where('is_default', 1);
    }

    public function scopeWithoutModule($query, $moduleType)
    {
        return $query->whereHas('module', function ($q) use ($moduleType) {
            $q->whereNot('module_type', $moduleType);
        });
    }

    public function getScheduleOrderAttribute($value): bool
    {
        return (bool) (\App\CentralLogics\Helpers::schedule_order() ? $value : 0);
    }

    public function getRatingAttribute($value): array
    {
        $ratings = $value ? json_decode($value, true) : [];
        $rating5 = $ratings ? $ratings[5] : 0;
        $rating4 = $ratings ? $ratings[4] : 0;
        $rating3 = $ratings ? $ratings[3] : 0;
        $rating2 = $ratings ? $ratings[2] : 0;
        $rating1 = $ratings ? $ratings[1] : 0;

        return [$rating5, $rating4, $rating3, $rating2, $rating1];
    }

    public function getGstStatusAttribute(): bool
    {
        return (bool) ($this->gst ? json_decode($this->gst, true)['status'] : 0);
    }

    public function getGstCodeAttribute(): string
    {
        return (string) ($this->gst ? json_decode($this->gst, true)['code'] : '');
    }

    public function scopeModule($query, $module_id): mixed
    {
        return $query->where('module_id', $module_id);
    }

    public function scopeWithModuleType($query, $moduleType)
    {
        return $query->whereHas('module', function ($q) use ($moduleType) {
            $q->where('module_type', $moduleType);
        });
    }

    public function scopeDelivery($query): void
    {
        $query->where('delivery', 1);
    }

    public function scopeTakeaway($query): void
    {
        $query->where('take_away', 1);
    }

    public function scopeActive($query): mixed
    {
        $query = $query->where('status', 1)
            ->where(function ($query) {
                $query->where('store_business_model', 'commission')
                    ->orWhereHas('store_sub', function ($query) {
                        $query->where(function ($query) {
                            $query->where('max_order', 'unlimited')->orWhere('max_order', '>', 0);
                        });
                    });
            });

        return $query;
    }

    public function scopeFeatured($query): mixed
    {
        return $query->where('featured', '=', 1);
    }

    public function scopeOpened($query): mixed
    {
        return $query->where('active', 1);
    }

    public function scopeWithOpen($query, $longitude, $latitude): void
    {
        $query->selectRaw('*, IF(((select count(*) from `store_schedule` where `stores`.`id` = `store_schedule`.`store_id` and `store_schedule`.`day` = '.now()->dayOfWeek.' and `store_schedule`.`opening_time` < "'.now()->format('H:i:s').'" and `store_schedule`.`closing_time` >"'.now()->format('H:i:s').'") > 0), true, false) as open,ST_Distance_Sphere(point(longitude, latitude),point('.$longitude.', '.$latitude.')) as distance');
    }

    public function scopeWithOpenWithDeliveryTime($query, $longitude, $latitude): void
    {
        $query->selectRaw('*, IF(((select count(*) from `store_schedule` where `stores`.`id` = `store_schedule`.`store_id` and `store_schedule`.`day` = '.now()->dayOfWeek.' and `store_schedule`.`opening_time` < "'.now()->format('H:i:s').'" and `store_schedule`.`closing_time` >"'.now()->format('H:i:s').'") > 0), true, false) as open,ST_Distance_Sphere(point(longitude, latitude),point('.$longitude.', '.$latitude.')) as distance, CASE WHEN delivery_time IS NULL THEN 9999  WHEN delivery_time LIKE  "%hours%" THEN CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(delivery_time, "-", 1), " ", 1) AS UNSIGNED) * 60 WHEN delivery_time LIKE "%min%" OR delivery_time LIKE "%minute%" THEN CAST(SUBSTRING_INDEX(delivery_time, "-", 1) AS UNSIGNED) ELSE 9999 END AS min_delivery_time');
    }

    public function scopeWeekday($query): mixed
    {
        return $query->where('off_day', 'not like', '%'.now()->dayOfWeek.'%');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ZoneScope);

        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                return $query->where('locale', app()->getLocale());
            }]);
        });

        static::addGlobalScope('storage', function ($builder) {
            $builder->with('storage');
        });

        static::retrieved(function () {
            // Helpers::disableStoreForOrderCancellation();
            $current_date = date('Y-m-d');
            $check_daily_subscription_validity_check = Helpers::getSettingsDataFromConfig(settings: 'check_daily_subscription_validity_check');
            if (! $check_daily_subscription_validity_check) {
                Helpers::insert_business_settings_key('check_daily_subscription_validity_check', $current_date);
                $check_daily_subscription_validity_check = BusinessSetting::where('key', 'check_daily_subscription_validity_check')->first();
            }

            if ($check_daily_subscription_validity_check && $check_daily_subscription_validity_check?->value != $current_date) {

                Store::whereHas('store_subs', function ($query) use ($current_date) {
                    $query->where('status', 1)->whereDate('expiry_date', '<=', $current_date);
                })->update(['status' => 0,
                    'pos_system' => 1,
                    'self_delivery_system' => 1,
                    'reviews_section' => 1,
                    'free_delivery' => 0,
                    'store_business_model' => 'unsubscribed',
                ]);
                StoreSubscription::where('status', 1)->whereDate('expiry_date', '<=', $current_date)->update([
                    'status' => 0,
                ]);

                // if (config('mail.status') && Helpers::get_mail_status('subscription_deadline_mail_status_store') == '1') {
                //     $subscription_deadline_warning_days = BusinessSetting::where('key','subscription_deadline_warning_days')->first()?->value ?? 7;

                //     $expire_soon= StoreSubscription::with('store:id,name,email')->where('status',1)->whereDate('expiry_date', Carbon::today()->addDays($subscription_deadline_warning_days))->get();

                //     try {
                //         foreach($expire_soon as $store){
                //             Mail::to($store?->getRawOriginal('email'))->send(new SubscriptionDeadLineWarning($store->name));
                //         }
                //     } catch (\Exception $ex) {
                //         info($ex->getMessage());
                //     }
                // }

                $check_daily_subscription_validity_check->value = $current_date;
                $check_daily_subscription_validity_check->save();
            }
        });

    }

    public function scopeType($query, $type): mixed
    {
        if ($type == 'veg') {
            return $query->where('veg', true);
        } elseif ($type == 'non_veg') {
            return $query->where('non_veg', true);
        }

        return $query;

    }

    public function scopeHalal($query, $type): mixed
    {
        if ($type == 1) {
            return $query->whereHas('storeConfig', function ($query) {
                $query->where('halal_tag_status', 1);
            });
        }

        return $query;

    }

    private function generateSlug($name): string
    {
        $slug = Str::slug($name);
        if ($max_slug = static::where('slug', 'like', "{$slug}%")->latest('id')->value('slug')) {

            if ($max_slug == $slug) {
                return "{$slug}-2";
            }

            $max_slug = explode('-', $max_slug);
            $count = array_pop($max_slug);
            if (isset($count) && is_numeric($count)) {
                $max_slug[] = ++$count;

                return implode('-', $max_slug);
            }
        }

        return $slug;
    }

    public function storage()
    {
        return $this->morphMany(Storage::class, 'data');
    }

    protected static function boot(): void
    {
        parent::boot();
        static::created(function ($store) {
            $store->slug = $store->generateSlug($store->name);
            $store->save();
        });
        static::saved(function ($model) {
            Helpers::deleteCacheData('advertisement_');
            Helpers::deleteCacheData('banner_');

            if ($model->isDirty('logo')) {
                $value = Helpers::getDisk();

                DB::table('storages')->updateOrInsert([
                    'data_type' => get_class($model),
                    'data_id' => $model->id,
                    'key' => 'logo',
                ], [
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if ($model->isDirty('cover_photo')) {
                $value = Helpers::getDisk();

                DB::table('storages')->updateOrInsert([
                    'data_type' => get_class($model),
                    'data_id' => $model->id,
                    'key' => 'cover_photo',
                ], [
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if ($model->isDirty('meta_image')) {
                $value = Helpers::getDisk();

                DB::table('storages')->updateOrInsert([
                    'data_type' => get_class($model),
                    'data_id' => $model->id,
                    'key' => 'meta_image',
                ], [
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
        static::updated(function () {
            Helpers::deleteCacheData('advertisement_');
            Helpers::deleteCacheData('banner_');
        });
    }

    public function storeConfig(): HasOne
    {
        return $this->hasOne(StoreConfig::class);
    }

    /**
     * Get all of the comments for the Store
     */
    public function vehicle_identity(): HasManyThrough
    {
        return $this->hasManyThrough(VehicleIdentity::class, Vehicle::class, 'provider_id', 'vehicle_id', 'id', 'id');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'provider_id');
    }

    public function vehicleDriver(): HasMany
    {
        return $this->hasMany(VehicleDriver::class, 'provider_id');
    }

    public function scopeStoreModel($query, $type): mixed
    {
        if ($type == 'commission') {
            return $query->where('store_business_model', 'commission');
        } elseif ($type == 'subscribed') {
            return $query->where('store_business_model', 'subscription');
        } elseif ($type == 'unsubscribed') {
            return $query->where('store_business_model', 'unsubscribed');
        } elseif ($type == 'none') {
            return $query->where('store_business_model', 'none');
        }

        return $query;
    }

    public function orderTaxes(): MorphMany
    {
        return $this->morphMany(OrderTax::class, 'store');
    }
}
