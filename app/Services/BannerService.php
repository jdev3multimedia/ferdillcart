<?php

namespace App\Services;

use App\Traits\FileManagerTrait;
use Illuminate\Support\Facades\Config;

class BannerService
{
    use FileManagerTrait;

    public function getAddData(Object $request): array
    {
        return [
            'title' => $request->title[array_search('default', $request->lang)],
            'type' => $request->banner_type,
            'zone_id' => $request->zone_id,
            'image' => $this->upload('banner/', 'png', $request->file('image')),
            'data' => ($request->banner_type == 'store_wise')?$request->store_id:(($request->banner_type == 'item_wise')?$request->item_id:''),
            'module_id' => Config::get('module.current_module_id'),
            'default_link' => $request->default_link
        ];
    }
    public function getUpdateData(Object $request, object $banner): array
    {
        return [
            'title' => $request->title[array_search('default', $request->lang)],
            'type' => $request->banner_type,
            'zone_id' => $request->zone_id,
            'image' => $request->has('image') ? $this->updateAndUpload('banner/', $banner->image, 'png', $request->file('image')) : $banner->image,
            'data' => ($request->banner_type == 'store_wise')?$request->store_id:(($request->banner_type == 'item_wise')?$request->item_id:''),
            'module_id' => Config::get('module.current_module_id'),
            'default_link' => $request->default_link
        ];
    }

}
