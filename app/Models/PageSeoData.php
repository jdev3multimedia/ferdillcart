<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\CentralLogics\Helpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PageSeoData extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['image_full_url'];
    protected $with = ['storage', 'translations'];

    protected $casts = [
        'status' => 'integer',
        'meta_data' => 'array',
    ];



    public function getImageFullUrlAttribute()
    {
        $value = $this->image;
        if (count($this->storage) > 0) {
            foreach ($this->storage as $storage) {
                if ($storage['key'] == 'image') {
                    return Helpers::get_full_url('page_meta_data', $value, $storage['value']);
                }
            }
        }
        return Helpers::get_full_url('page_meta_data', $value, 'public');
    }


    // public function getNameAttribute($value)
    // {
    //     if (count($this->translations) > 0) {

    //         foreach ($this->translations as $translation) {
    //             if ($translation['key'] == 'name') {
    //                 return $translation['value'];
    //             }
    //         }
    //     }

    //     return $value;
    // }

    // public function getDescriptionAttribute($value)
    // {
    //     if (count($this->translations) > 0) {
    //         foreach ($this->translations as $translation) {
    //             if ($translation['key'] == 'description') {
    //                 return $translation['value'];
    //             }
    //         }
    //     }

    //     return $value;
    // }

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

        public function storage()
    {
        return $this->morphMany(Storage::class, 'data');
    }

    protected static function booted()
    {
        static::created(function ($data) {
            $data->slug = $data->generateSlug($data->name);
            $data->save();
        });


        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                return $query->where('locale', app()->getLocale());
            }]);
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


}
