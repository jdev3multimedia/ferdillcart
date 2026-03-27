<?php

namespace App\Services;

use App\CentralLogics\Helpers;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class AttributeService
{

    public function getAddData(Object $request): array
    {
        return [
            'name' => $request->name[array_search('default', $request->lang)],
        ];
    }

}
