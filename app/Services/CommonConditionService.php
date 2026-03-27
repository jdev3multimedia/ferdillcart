<?php

namespace App\Services;

use Composer\DependencyResolver\Request;
use Illuminate\Support\Str;

class CommonConditionService
{

    public function getAddData(Object $request): array
    {
        return [
            'name' => $request->name[array_search('default', $request->lang)],
        ];
    }
    public function getUpdateData(Object $request, object $condition): array
    {
        $slug = Str::slug($request->name[array_search('default', $request->lang)]);
        return [
            'slug' => $condition->slug? $condition->slug :"{$slug}{$condition->id}",
            'name' => $request->name[array_search('default', $request->lang)],
        ];
    }

    public function getDropdownData(Object $data, object $request): array|object
    {

        $formattedData = $data->map(function ($condition) {
            return [
                'id' => $condition->id,
                'text' => $condition->name,
            ];
        });


        if(isset($request->all))
        {
            $formattedData[]=(object)['id'=>'all', 'text'=>translate('messages.all')];
        }

        return $formattedData;
    }

}
