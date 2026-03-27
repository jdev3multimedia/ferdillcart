<?php

namespace App\Models;

use App\CentralLogics\Helpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class Module
 *
 * @property int $id
 * @property string $module_name
 * @property string $module_type
 * @property string|null $thumbnail
 * @property bool $status
 * @property int $stores_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $icon
 * @property int $theme_id
 * @property string|null $description
 * @property bool $all_zone_service
 */
class Module extends Model
{
    use HasFactory;
    protected $with = ['translations','storage'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'module_name',
        'module_type',
        'thumbnail',
        'status',
        'stores_count',
        'icon',
        'theme_id',
        'description',
        'all_zone_service',
    ];


    /**
     * @var string[]
     */
    protected $casts = [
        'id'=>'integer',
        'stores_count'=>'integer',
        'theme_id'=>'integer',
        'status'=>'string',
        'all_zone_service'=>'integer'
    ];

    protected $appends = ['icon_full_url','thumbnail_full_url'];

    /**
     * @return HasMany
     */
    public function stores(): HasMany
    {
        return $this->hasMany(Store::class);
    }

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

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
    public function getModuleNameAttribute($value): mixed
    {
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] == 'module_name') {
                    return $translation['value'];
                }
            }
        }

        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getDescriptionAttribute($value): mixed
    {
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] == 'description') {
                    return $translation['value'];
                }
            }
        }

        return $value;
    }


    /**
     * @param $query
     * @return mixed
     */
    public function scopeParcel($query): mixed
    {
        return $query->where('module_type', 'parcel');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeNotParcel($query): mixed
    {
        return $query->where('module_type', '!=' ,'parcel');
    }
    public function scopeNotRental($query): mixed
    {
        return $query->where('module_type', '!=' ,'rental');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive($query): mixed
    {
        return $query->where('status', '=', 1);
    }

    public function getIconFullUrlAttribute(){
        $value = $this->icon;
        if (count($this->storage) > 0) {
            foreach ($this->storage as $storage) {
                if ($storage['key'] == 'icon') {
                    return Helpers::get_full_url('module',$value,$storage['value']);
                }
            }
        }

        return Helpers::get_full_url('module',$value,'public');
    }
    public function getThumbnailFullUrlAttribute(){
        $value = $this->thumbnail;
        if (count($this->storage) > 0) {
            foreach ($this->storage as $storage) {
                if ($storage['key'] == 'thumbnail') {
                    return Helpers::get_full_url('module',$value,$storage['value']);
                }
            }
        }

        return Helpers::get_full_url('module',$value,'public');
    }

    public function storage()
    {
        return $this->morphMany(Storage::class, 'data');
    }

    private function generateSlug($name)
    {
        $slug = Str::slug($name);
        if ($max_slug = static::where('slug', 'like', "{$slug}%")->latest('id')->value('slug')) {

            if ($max_slug == $slug) return "{$slug}-2";

            $max_slug = explode('-', $max_slug);
            $count = array_pop($max_slug);
            if (isset($count) && is_numeric($count)) {
                $max_slug[] = ++$count;
                return implode('-', $max_slug);
            }
        }
        return $slug;
    }

    protected static function booted()
    {
        static::addGlobalScope('storage', function ($builder) {
            $builder->with('storage');
        });
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function($query){
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }

    /**
     * @return BelongsToMany
     */
    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(Zone::class)
            ->withPivot([
                'per_km_shipping_charge',
                'minimum_shipping_charge',
                'maximum_shipping_charge',
                'maximum_cod_order_amount',
                'delivery_charge_type',
                'fixed_shipping_charge'
            ])
            ->using(ModuleZone::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::created(function ($item) {
            $item->slug = $item->generateSlug($item->module_name);
            $item->save();
        });
        static::saved(function ($model) {
            if($model->isDirty('icon')){
                $value = Helpers::getDisk();

                DB::table('storages')->updateOrInsert([
                    'data_type' => get_class($model),
                    'data_id' => $model->id,
                    'key' => 'icon',
                ], [
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if($model->isDirty('thumbnail')){
                $value = Helpers::getDisk();

                DB::table('storages')->updateOrInsert([
                    'data_type' => get_class($model),
                    'data_id' => $model->id,
                    'key' => 'thumbnail',
                ], [
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

    }

    public static function regenerateSlugs($force = false)
    {
        static::chunkById(100, function ($modules) use ($force) {
            foreach ($modules as $module) {
                // Skip if slug already exists (unless forced)
                if (!$force && !empty($module->slug)) {
                    continue;
                }

                $module->slug = $module->generateSlug($module->module_name);
                $module->save();
            }
        });
    }
}
