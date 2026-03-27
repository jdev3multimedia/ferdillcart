<?php

namespace App\Services;

use App\Traits\FileManagerTrait;
use Illuminate\Support\Str;

class BrandService
{
    use FileManagerTrait;

    public function getAddData(Object $request): array
    {
        return [
            'status' => $request->brand_status ? 1 : 0,
            'name' => $request->name[array_search('default', $request->lang)],
            'image' => $this->upload('brand/', 'png', $request->file('image')),
        ];
    }
    public function getUpdateData(Object $request, object $brand): array
    {
        $slug = Str::slug($request->name[array_search('default', $request->lang)]);
        return [
            'status' => $request->brand_status ? 1 : 0,
            'slug' => $brand->slug? $brand->slug :"{$slug}{$brand->id}",
            'name' => $request->name[array_search('default', $request->lang)],
            'image' => $request->has('image') ? $this->updateAndUpload('brand/', $brand->image, 'png', $request->file('image')) : $brand->image,
        ];
    }

    public function getDropdownData(Object $data, object $request): array|object
    {

        $formattedData = $data->map(function ($brand) {
            return [
                'id' => $brand->id,
                'text' => $brand->name,
            ];
        });


        if(isset($request->all))
        {
            $formattedData[]=(object)['id'=>'all', 'text'=>translate('messages.all')];
        }

        return $formattedData;
    }

}
