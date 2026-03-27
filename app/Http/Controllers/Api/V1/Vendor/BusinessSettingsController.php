<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Http\Controllers\Controller;
use App\Models\StoreConfig;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;
use App\Models\StoreSchedule;
use App\Models\Translation;

class BusinessSettingsController extends Controller
{
   public function update_store_basic_info(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_number' => 'required|string|max:20',
            'logo' => 'nullable|image|max:2048',
            'cover_photo' => 'nullable|image|max:2048',
            'meta_title' => 'max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $store = $request['vendor']->store;

        $data_trans = json_decode($request->translations, true);

        if (count($data_trans) < 1) {
            return response()->json([
                'errors' => [['code' => 'translations', 'message' => translate('messages.Name and address in english is required')]]
            ], 403);
        }

        $store->name = $data_trans[0]['value'];
        $store->address = $data_trans[1]['value'];
        $store->phone = $request->contact_number;
        $store->logo = $request->has('logo') ? Helpers::update(dir:'store/', old_image:$store->logo, format:'png', image:$request->file('logo')) : $store->logo;
        $store->cover_photo = $request->has('cover_photo') ? Helpers::update(dir:'store/cover/', old_image:$store->cover_photo,format: 'png', image:$request->file('cover_photo')) : $store->cover_photo;

        $store->meta_title = $request->meta_title;
        $store->meta_description = $request->meta_description;
        $store->meta_image = $request->has('meta_image') ? Helpers::update(dir:'store/', old_image: $store->meta_image, format: 'png', image: $request->file('meta_image')) : $store->meta_image;
        $store->meta_data = Helpers::formatMetaData($request->all(), $store->meta_data);

        $store->save();

        foreach ($data_trans as $key => $i) {
            Translation::updateOrInsert(
                ['translationable_type' => 'App\Models\Store',
                'translationable_id' => $store->id,
                'locale' => $i['locale'],
                'key' => $i['key']],
                ['value' => $i['value']]
            );
        }

        if($store?->vendor?->userinfo) {
            $userinfo = $store->vendor->userinfo;
            $userinfo->f_name = $store->name;
            $userinfo->image = $store->logo;
            $userinfo->save();
        }

        return response()->json(['message' => translate('messages.store_basic_info_updated')], 200);
    }



    public function update_store_setup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery' => 'required|boolean',
            'prescription_order' => 'required|boolean',
            'take_away' => 'required|boolean',
            'schedule_order' => 'required|boolean',
            'veg' => 'required|boolean',
            'non_veg' => 'required|boolean',
            'minimum_order' => 'required|numeric',
            'gst' => 'required_if:gst_status,1',
            'minimum_delivery_time' => 'required|numeric',
            'maximum_delivery_time' => 'required|numeric',
            'delivery_time_type'=>'required|in:min,hours,days'

        ],[
            'gst.required_if' => translate('messages.gst_can_not_be_empty'),
        ]);
        $store = $request['vendor']->store;


        $validator->sometimes('per_km_delivery_charge', 'required_with:minimum_delivery_charge', function ($request) use($store) {
            return ($store->sub_self_delivery);
        });
        $validator->sometimes('minimum_delivery_charge', 'required_with:per_km_delivery_charge', function ($request) use($store) {
            return ($store->sub_self_delivery);
        });


        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        if(!$request->take_away && !$request->delivery)
        {
            return response()->json([
                'errors'=>[
                    ['code'=>'delivery_or_take_way', 'message'=>translate('messages.can_not_disable_both_take_away_and_delivery')]
                ]
            ],403);
        }

        if( \App\Models\BusinessSetting::where('key', 'toggle_veg_non_veg')->first()?->value == 1 && !$request->veg && !$request->non_veg)
        {
            return response()->json([
                'errors'=>[
                    ['code'=>'veg_non_veg', 'message'=>translate('messages.veg_non_veg_disable_by_admin')]
                ]
            ],403);
        }

        $store->delivery = $request->delivery;
        $store->prescription_order = $request->prescription_order;
        $store->take_away = $request->take_away;
        $store->schedule_order = $request->schedule_order;
        $store->veg = $request?->veg??0;
        $store->non_veg = $request?->non_veg ?? 0;
        $store->cutlery = $request->cutlery??0;
        $store->free_delivery = $request->free_delivery??0;
        $store->minimum_order = $request->minimum_order;
        $store->gst = json_encode(['status'=>$request->gst_status, 'code'=>$request->gst]);
        $store->minimum_shipping_charge = $store->sub_self_delivery?$request->minimum_delivery_charge??0: $store->minimum_shipping_charge;
        $store->per_km_shipping_charge = $store->sub_self_delivery?$request->per_km_delivery_charge??0: $store->per_km_shipping_charge;
        $store->maximum_shipping_charge = $store?$request->maximum_delivery_charge??0: $store->maximum_delivery_charge;
        $store->delivery_time = $request->minimum_delivery_time .'-'. $request->maximum_delivery_time.' '.$request->delivery_time_type;
        $store->order_place_to_schedule_interval = $request->order_place_to_schedule_interval;

        $store->save();

        $conf = StoreConfig::firstOrNew(
            ['store_id' =>  $store->id]
        );
        $conf->halal_tag_status = $request->halal_tag_status ?? 0;
        $conf->extra_packaging_status = $request->extra_packaging_status ?? 0;
        $conf->extra_packaging_amount = $request->extra_packaging_amount;
        $conf->minimum_stock_for_warning = $request->minimum_stock_for_warning ?? 0;
        $conf->save();

        return response()->json(['message'=>translate('messages.store_settings_updated')], 200);
    }

    public function add_schedule(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'opening_time'=>'required|date_format:H:i:s',
            'closing_time'=>'required|date_format:H:i:s|after:opening_time',
        ],[
            'closing_time.after'=>translate('messages.End time must be after the start time')
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)],400);
        }
        $store = $request['vendor']->stores[0];
        $temp = StoreSchedule::where('day', $request->day)->where('store_id',$store->id)
        ->where(function($q)use($request){
            return $q->where(function($query)use($request){
                return $query->where('opening_time', '<=' , $request->opening_time)->where('closing_time', '>=', $request->opening_time);
            })->orWhere(function($query)use($request){
                return $query->where('opening_time', '<=' , $request->closing_time)->where('closing_time', '>=', $request->closing_time);
            });
        })
        ->first();

        if(isset($temp))
        {
            return response()->json(['errors' => [
                ['code'=>'time', 'message'=>translate('messages.schedule_overlapping_warning')]
            ]], 400);
        }

        $store_schedule = StoreSchedule::insertGetId(['store_id'=>$store->id,'day'=>$request->day,'opening_time'=>$request->opening_time,'closing_time'=>$request->closing_time]);
        return response()->json(['message'=>translate('messages.Schedule added successfully'), 'id'=>$store_schedule], 200);
    }

    public function remove_schedule(Request $request, $store_schedule)
    {
        $store = $request['vendor']->stores[0];
        $schedule = StoreSchedule::where('store_id', $store->id)->find($store_schedule);
        if(!$schedule)
        {
            return response()->json([
                'error'=>[
                    ['code'=>'not-fond', 'message'=>translate('messages.Schedule not found')]
                ]
            ],404);
        }
        $schedule->delete();
        return response()->json(['message'=>translate('messages.Schedule removed successfully')], 200);
    }
}
