<?php

namespace App\Models;

use App\CentralLogics\Helpers;
use App\Scopes\ZoneScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class Banner
 *
 * @property int $id
 * @property string $title
 * @property string $type
 * @property string|null $image
 * @property bool $status
 * @property string $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $zone_id
 * @property int $module_id
 * @property bool $featured
 * @property string|null $default_link
 * @property string $created_by
 */
class Banner extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'type',
        'image',
        'status',
        'data',
        'zone_id',
        'module_id',
        'featured',
        'default_link',
        'created_by',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'data' => 'integer',
        'status' => 'boolean',
        'zone_id' => 'integer',
        'module_id' => 'integer',
        'featured' => 'boolean',
    ];

    protected $appends = ['image_full_url'];

    /**
     * @return MorphMany
     */
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function storage()
    {
        return $this->morphMany(Storage::class, 'data');
    }
    public function store()
    {
        return $this->belongsTo(Store::class, 'data');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getTitleAttribute($value): mixed
    {
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
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
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    /**
     * @return BelongsTo
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
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
     * @param $query
     * @return mixed
     */
    public function scopeActive($query): mixed
    {
        return $query->where('status', '=', 1);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeFeatured($query): mixed
    {
        return $query->where('featured', '=', 1);
    }

    public function getImageFullUrlAttribute(){
        $value = $this->image;
        if (count($this->storage) > 0) {
            foreach ($this->storage as $storage) {
                if ($storage['key'] == 'image') {
                    return Helpers::get_full_url('banner',$value,$storage['value']);
                }
            }
        }

        return Helpers::get_full_url('banner',$value,'public');
    }

    /**
     * @return void
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new ZoneScope);
        static::addGlobalScope('storage', function ($builder) {
            $builder->with('storage');
        });

        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }

    protected static function boot()
    {
        parent::boot();
        static::saved(function ($model) {
            Helpers::deleteCacheData('banners_');

            if($model->isDirty('image')){
                $value = Helpers::getDisk();

                DB::table('storages')->updateOrInsert([
                    'data_type' => get_class($model),
                    'data_id' => $model->id,
                    'key' => 'image',
                ], [
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
        static::created(function () {
            Helpers::deleteCacheData('banners_');
        });
        static::deleted(function(){
            Helpers::deleteCacheData('banners_');
        });

        static::updated(function(){
            Helpers::deleteCacheData('banners_');
        });

    }
}
