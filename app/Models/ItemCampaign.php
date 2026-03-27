<?php

namespace App\Models;

use App\CentralLogics\Helpers;
use App\Scopes\ZoneScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\TaxModule\Entities\Taxable;

class ItemCampaign extends Model
{
    use HasFactory;

    protected $casts = [
        'tax' => 'float',
        'price' => 'float',
        'discount' => 'float',
        'status' => 'integer',
        'store_id' => 'integer',
        'category_id' => 'integer',
        'module_id' => 'integer',
        'maximum_cart_quantity' => 'integer',
        'veg' => 'integer',
        'stock'=>'integer',
        'created_at'=>'datetime',
        'updated_at'=>'datetime',
        'start_date'=>'datetime',
        'end_date'=>'datetime',
        'start_time'=>'datetime',
        'end_time'=>'datetime',
    ];

    protected $appends = ['image_full_url'];

    public function carts()
    {
    return $this->morphMany(Cart::class, 'item');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function allergies()
    {
        return $this->belongsToMany(Allergy::class);
    }
    public function generic()
    {
        return $this->belongsToMany(GenericName::class,'item_campaign_generic_names');
    }
    public function nutritions()
    {
        return $this->belongsToMany(Nutrition::class);
    }

    public function getTitleAttribute($value){
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] == 'title') {
                    return $translation['value'];
                }
            }
        }

        return $value;
    }

    public function getDescriptionAttribute($value){
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] == 'description') {
                    return $translation['value'];
                }
            }
        }

        return $value;
    }

    public function getImageFullUrlAttribute(){
        $value = $this->image;
        if (count($this->storage) > 0) {
            foreach ($this->storage as $storage) {
                if ($storage['key'] == 'image') {
                    return Helpers::get_full_url('campaign',$value,$storage['value']);
                }
            }
        }

        return Helpers::get_full_url('campaign',$value,'public');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function orderdetails()
    {
        return $this->hasMany(OrderDetail::class)->latest();
    }

    public function scopeModule($query, $module_id)
    {
        return $query->where('module_id', $module_id);
    }

    public function scopeActive($query)
    {
        // return $query->where('status', '=', 1);
        return $query->where('status', 1)
        ->whereHas('store', function($query) {
            $query->where('status', 1)
                    ->where(function($query) {
                        $query->where('store_business_model', 'commission')
                                ->orWhereHas('store_sub', function($query) {
                                    $query->where(function($query) {
                                        $query->where('max_order', 'unlimited')->orWhere('max_order', '>', 0);
                                    });
                                });
                    });
            });
    }

    public function scopeRunning($query)
    {
        return $query->whereDate('end_date', '>=', date('Y-m-d'));
    }

    protected static function booted()
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
    }
    public function storage()
    {
        return $this->morphMany(Storage::class, 'data');
    }
    protected static function boot()
    {
        parent::boot();
        static::created(function ($itemcampaign) {
            $itemcampaign->slug = $itemcampaign->generateSlug($itemcampaign->title);
            $itemcampaign->save();
        });
        static::saved(function ($model) {
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
    }
    private function generateSlug($name)
    {
        $slug = Str::slug($name);
        if ($max_slug = static::where('slug', 'like',"{$slug}%")->latest('id')->value('slug')) {

            if($max_slug == $slug) return "{$slug}-2";

            $max_slug = explode('-',$max_slug);
            $count = array_pop($max_slug);
            if (isset($count) && is_numeric($count)) {
                $max_slug[]= ++$count;
                return implode('-', $max_slug);
            }
        }
        return $slug;
    }
         public function taxVats()
    {
        return $this->morphMany(Taxable::class, 'taxable');
    }
}
