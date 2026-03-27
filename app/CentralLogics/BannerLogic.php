<?php

namespace App\CentralLogics;

use App\Models\Banner;
use App\Models\Item;
use App\Models\Store;
use Illuminate\Support\Facades\Cache;

class BannerLogic
{
    public static function get_banners($zone_id, $featured = false)
    {
        $moduleData = config('module.current_module_data');
        $moduleId = isset($moduleData['id']) ? $moduleData['id'] : 'default';
        $cacheKey = 'banners_' . md5($zone_id . '_' . ($featured ? 'featured' : 'non_featured') . '_' . $moduleId);

        $banners = Cache::remember($cacheKey, now()->addMinutes(20), function () use ($zone_id, $featured,$moduleId) {
            $banners = Banner::active()
                ->when($featured, function ($query) {
                    $query->featured();
                })
                ->whereHas('module', function ($query) {
                    $query->active();
                })
                ->where('created_by', 'admin')
                ->where(function ($query) use ($zone_id) {
                    $query->where(function ($query) use ($zone_id) {
                        $query->whereIn('type', ['store_wise', 'item_wise'])
                            ->whereIn('zone_id', json_decode($zone_id, true));
                    })->orWhere('type', 'default');
                });

            if (config('module.current_module_data')) {
                $banners = $banners->whereHas('zone.modules', function ($query) use ($moduleId) {
                    $query->where('modules.id',$moduleId);
                }) ->module($moduleId);
            }

               return  $banners = $banners->get();
        });

        $data = [];
        foreach ($banners??[] as $banner) {
            if ($banner->type == 'store_wise') {
                $store = Store::active()
                    ->when(config('module.current_module_data'), function ($query) {
                        $query->whereHas('zone.modules', function ($query) {
                            $query->where('modules.id', config('module.current_module_data')['id']);
                        });
                    })
                    ->find($banner->data);
                if ($store) {
                    $data[] = [
                        'id' => $banner->id,
                        'title' => $banner->title,
                        'type' => $banner->type,
                        'image' => $banner->image,
                        'link' => null,
                        'store' => $store ? Helpers::store_data_formatting($store, false) : null,
                        'item' => null,
                        'image_full_url' => $banner->image_full_url
                    ];
                }
            }
            if ($banner->type == 'item_wise') {
                $item = Item::active()
                    ->when(config('module.current_module_data'), function ($query) use ($zone_id) {
                        $query->whereHas('module.zones', function ($query) use ($zone_id) {
                            $query->whereIn('zones.id', json_decode($zone_id, true));
                        });
                    })
                    ->find($banner->data);
                $data[] = [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'type' => $banner->type,
                    'image' => $banner->image,
                    'link' => null,
                    'store' => null,
                    'item' => $item ? Helpers::product_data_formatting($item, false, false, app()->getLocale()) : null,
                    'image_full_url' => $banner->image_full_url
                ];
            }
            if ($banner->type == 'default') {
                $data[] = [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'type' => $banner->type,
                    'image' => $banner->image,
                    'link' => $banner->default_link,
                    'store' => null,
                    'item' => null,
                    'image_full_url' => $banner->image_full_url
                ];
            }
            if ($banner->type == null) {
                $data[] = [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'type' => $banner->type,
                    'image' => $banner->image,
                    'link' => null,
                    'store' => null,
                    'item' => null,
                    'image_full_url' => $banner->image_full_url
                ];
            }
        }
        return $data;
    }
}
