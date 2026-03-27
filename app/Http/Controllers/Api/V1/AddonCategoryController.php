<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AddonCategory;
use Illuminate\Support\Facades\Validator;
class AddonCategoryController extends Controller
{
    public function getList(Request $request)
    {
      $validator = Validator::make($request->all(), [
            'limit' => 'nullable|numeric',
            'offset' => 'nullable|numeric',
            'module_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $addonCategories = AddonCategory::where(function ($query)use ($request) {
            $query->where('module_id', $request->module_id)->orWhereNull('module_id');
        })->where('status', 1)->select('id', 'name')->latest()->paginate($request->limit ?? 50, ['*'], 'page', $request->offset ?? 1);
        return response()->json($addonCategories->items(), 200);
    }
}
