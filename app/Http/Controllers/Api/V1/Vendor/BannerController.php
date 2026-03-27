<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Models\Banner;
use App\Models\Translation;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    public function list(Request $request)
    {
        $vendor = $request['vendor'];

        $addons = Banner::where('created_by','store')->where('data', $vendor->stores[0]->id)->latest()->get();

        return response()->json(Helpers::addon_data_formatting($addons, true, true, app()->getLocale()),200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'image' => 'required|mimes:webp,jpg,jpeg,png,bmp,tiff|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $vendor = $request['vendor'];


        $data = json_decode($request->translations, true);

        $banner = new Banner;
        $banner->title = $data[0]['value'];
        $banner->type = 'store_wise';
        $banner->zone_id = $vendor->stores[0]->zone_id;
        $banner->image = Helpers::upload('banner/', 'png', $request->file('image'));
        $banner->data = $vendor->stores[0]->id;
        $banner->module_id = $vendor->stores[0]->module_id;
        $banner->default_link = $request->default_link;
        $banner->created_by = 'store';
        $banner->save();


        foreach ($data as $key=>$item) {
            Translation::updateOrInsert(
                ['translationable_type' => Banner::class,
                    'translationable_id' => $banner->id,
                    'locale' => $item['locale'],
                    'key' => $item['key']],
                ['value' => $item['value']]
            );
        }

        return response()->json(['message' => translate('messages.banner_added_successfully')], 200);
    }

    public function edit($id)
    {
        $banner = Banner::withoutGlobalScope('translate')->with('translations')->find($id);

        if ($banner) {
            return response()->json($banner, 200);
        }
        return response()->json(['message' => translate('messages.banner_not_found.')], 400);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'image' => 'max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }


        $data = json_decode($request->translations, true);

        $banner = Banner::find($request->id);
        $banner->title = $data[0]['value'];
        $banner->image = $request->has('image') ? Helpers::update('banner/', $banner->image, 'png', $request->file('image')) : $banner->image;
        $banner->default_link = $request->default_link;
        $banner->save();

        foreach ($data as $key=>$item) {
            Translation::updateOrInsert(
                ['translationable_type' => Banner::class,
                    'translationable_id' => $banner->id,
                    'locale' => $item['locale'],
                    'key' => $item['key']],
                ['value' => $item['value']]
            );
        }

        return response()->json(['message' => translate('messages.banner_updated_successfully')], 200);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $banner = Banner::findOrFail($request->id);

        Helpers::check_and_delete('banner/' , $banner['image']);

        $banner->translations()->delete();
        $banner->delete();

        return response()->json(['message' => translate('messages.banner_deleted_successfully')], 200);
    }

    public function status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $banner = Banner::findOrFail($request->id);
        $banner->status = $request->status;
        $banner->save();

        return response()->json(['message' => translate('messages.banner_status_updated')], 200);
    }

    public function search(Request $request){

        $vendor = $request['vendor'];
        $limit = $request['limite']??25;
        $offset = $request['offset']??1;

        $key = explode(' ', $request['search']);
        $banners=Banner::where('created_by','store')->where('store_id', $vendor->stores[0]->id)->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->latest()->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total_size' => $banners->total(),
            'limit' => $limit,
            'offset' => $offset,
            'banners' => $banners->items()
        ];

        return response()->json([$data],200);
    }
}
