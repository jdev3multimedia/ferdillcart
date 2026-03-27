<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Store;
use App\Models\StoreConfig;
use App\Models\Translation;
use Illuminate\Http\Request;
use App\Models\StoreSchedule;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\StoreNotificationSetting;
use App\Models\Zone;
use Illuminate\Support\Facades\Validator;

class BusinessSettingsController extends Controller
{

    private $store;

    public function store_index()
    {


        $store = Helpers::get_store_data();
        $store = Store::withoutGlobalScope('translate')->findOrFail($store->id);

        if($store->module_type == 'rental' ){
            $zones=Zone::active()->get(['id','name']);
            return view('rental::provider.settings.settings', compact('store','zones'));
        }
        return view('vendor-views.business-settings.restaurant-index', compact('store'));
    }

    public function store_setup(Store $store, Request $request)
    {
        $request->validate([
            'gst' => 'required_if:gst_status,1',
            'extra_packaging_amount' => 'required_if:extra_packaging_status,1',
            'per_km_delivery_charge'=>'required_with:minimum_delivery_charge',
            'minimum_delivery_charge'=>'required_with:per_km_delivery_charge'
        ], [
            'gst.required_if' => translate('messages.gst_can_not_be_empty'),
            'extra_packaging_amount.required_if' => translate('messages.extra_packaging_amount_can_not_be_empty'),
        ]);

        if(isset($request->maximum_shipping_charge) && ($request->minimum_delivery_charge > $request->maximum_shipping_charge)){
            Toastr::error(translate('Maximum delivery charge must be greater than minimum delivery charge.'));
                return back();
        }

        if($store->module_type == 'rental' && addon_published_status('Rental')){
            $store->pickup_zone_id =json_encode($request->pickup_zones ?? []);
            $store->schedule_order = $request->schedule_order ?? 0;
        }

        $store->minimum_order = $request->minimum_order??0;
        $store->gst = json_encode(['status'=>$request->gst_status, 'code'=>$request->gst]);
        // $store->delivery_charge = $store->self_delivery_system?$request->delivery_charge??0: $store->delivery_charge;
        $store->minimum_shipping_charge = $store->sub_self_delivery?$request->minimum_delivery_charge??0: $store->minimum_shipping_charge;
        $store->per_km_shipping_charge = $store->sub_self_delivery?$request->per_km_delivery_charge??0: $store->per_km_shipping_charge;
        $store->per_km_shipping_charge = $store->sub_self_delivery?$request->per_km_delivery_charge??0: $store->per_km_shipping_charge;
        $store->maximum_shipping_charge = $store->sub_self_delivery?$request->maximum_shipping_charge??0: $store->maximum_shipping_charge;
        $store->order_place_to_schedule_interval = $request->order_place_to_schedule_interval;
        $store->delivery_time = $request->minimum_delivery_time .'-'. $request->maximum_delivery_time.' '.$request->delivery_time_type;
        $store->save();
        $conf = StoreConfig::firstOrNew(
            ['store_id' =>  $store->id]
        );
        $conf->extra_packaging_amount = $request->extra_packaging_amount ?? 0;
        $conf->extra_packaging_status = $request->extra_packaging_status ?? 0;
        $conf->minimum_stock_for_warning = $request->minimum_stock_for_warning ?? 0;
        $conf->save();
        if($store->module_type == 'rental' && addon_published_status('Rental')){
            Toastr::success(translate('messages.provider settings updated!'));
        }else{
            Toastr::success(translate('messages.store_settings_updated'));
        }
        return back();
    }
    public function updateStoreMetaData(Store $store, Request $request)
    {
        if ($request->has('meta_image_deleted') && $request->meta_image_deleted == 1) {
            Helpers::check_and_delete('store/', $store->meta_image);
            $store->meta_image = null;
        }
        $store->meta_image = $request->has('meta_image') ? Helpers::update('store/', $store->meta_image, 'png', $request->file('meta_image')) : $store->meta_image;
        
        $store->meta_title = $request->meta_title;
        $store->meta_description = $request->meta_description;
        $store->meta_data = Helpers::formatMetaData($request->all(), $store->meta_data);
        $store->save();
        if($store->module->module_type == 'rental' && addon_published_status('Rental')){
            Toastr::success(translate('messages.provider_meta_data_updated!'));
        }else{
            Toastr::success(translate('messages.store').' '.translate('messages.meta_data_updated'));
        }

        return back();
    }
    public function store_status(Store $store, Request $request)
    {
        if($request->menu == "schedule_order" && !Helpers::schedule_order())
        {
            Toastr::warning(translate('messages.schedule_order_disabled_warning'));
            return back();
        }

        if((($request->menu == "delivery" && $store->take_away==0) || ($request->menu == "take_away" && $store->delivery==0)) &&  $request->status == 0 )
        {
            Toastr::warning(translate('messages.can_not_disable_both_take_away_and_delivery'));
            return back();
        }

        if((($request->menu == "veg" && $store->non_veg==0) || ($request->menu == "non_veg" && $store->veg==0)) &&  $request->status == 0 )
        {
            Toastr::warning(translate('messages.veg_non_veg_disable_warning'));
            return back();
        }

        if($request->menu == "announcement" &&  $request->status == 1 &&  !isset($store->announcement_message) )
        {
            Toastr::warning(translate('messages.You_need_to_add_announcement_message_first'));
            return back();
        }

        if($request->menu == 'free_delivery' &&(($store->store_business_model == 'subscription' && $store?->store_sub?->self_delivery == 0) || ($store->store_business_model == 'unsubscribed'))){
            Toastr::error(translate('your_subscription_plane_does_not_have_this_feature'));
            return back();
        }


        if($request->menu == 'halal_tag_status' || $request->menu == 'extra_packaging_status' || $request->menu == 'extra_packaging_amount' ){

            $conf = StoreConfig::firstOrNew(
                ['store_id' =>  $store->id]
            );
            $conf[$request->menu] = $request->status;
            $conf->save();
            if($store->module->module_type == 'rental' && addon_published_status('Rental')){
                Toastr::success(translate('messages.provider settings updated!'));
            }else{
                Toastr::success(translate('messages.store settings updated!'));
            }
            return back();
        }


        $store[$request->menu] = $request->status;
        $store->save();
        if($store->module->module_type == 'rental' && addon_published_status('Rental')){
            Toastr::success(translate('messages.provider settings updated!'));
        }else{
            Toastr::success(translate('messages.store settings updated!'));
        }
        return back();
    }

    public function active_status(Request $request)
    {
        $store = Helpers::get_store_data();
        $store->active = !$store->active;
        $store->save();
        return response()->json(['message' => $store->active?($store->module->module_type == 'rental' ? translate('provider') : translate('store')).' '.translate('messages.opened'):($store->module->module_type == 'rental' ? translate('provider') : translate('store')).' '.translate('messages.temporarily_closed')], 200);
    }

    public function add_schedule(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'start_time'=>'required|date_format:H:i',
            'end_time'=>'required|date_format:H:i|after:start_time',
        ],[
            'end_time.after'=>translate('messages.End time must be after the start time')
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        $temp = StoreSchedule::where('day', $request->day)->where('store_id',Helpers::get_store_id())
        ->where(function($q)use($request){
            return $q->where(function($query)use($request){
                return $query->where('opening_time', '<=' , $request->start_time)->where('closing_time', '>=', $request->start_time);
            })->orWhere(function($query)use($request){
                return $query->where('opening_time', '<=' , $request->end_time)->where('closing_time', '>=', $request->end_time);
            });
        })
        ->first();

        if(isset($temp))
        {
            return response()->json(['errors' => [
                ['code'=>'time', 'message'=>translate('messages.schedule_overlapping_warning')]
            ]]);
        }

        $store = Helpers::get_store_data();
        $store_schedule = StoreSchedule::insert(['store_id'=>Helpers::get_store_id(),'day'=>$request->day,'opening_time'=>$request->start_time,'closing_time'=>$request->end_time]);
        return response()->json([
            'view' => view('vendor-views.business-settings.partials._schedule', compact('store'))->render(),
        ]);
    }

    public function remove_schedule($store_schedule)
    {
        $store = Helpers::get_store_data();
        $schedule = StoreSchedule::where('store_id', $store->id)->find($store_schedule);
        if(!$schedule)
        {
            return response()->json([],404);
        }
        $schedule->delete();
        return response()->json([
            'view' => view('vendor-views.business-settings.partials._schedule', compact('store'))->render(),
        ]);
    }


    public function site_direction_vendor(Request $request){
        session()->put('site_direction_vendor', ($request->status == 1?'ltr':'rtl'));
        return response()->json();
    }

    public function notification_index()
    {
        $module_type=Helpers::get_store_data()->module->module_type;
        if(StoreNotificationSetting::where('store_id',Helpers::get_store_id())->count() == 0 ){
            $module_type == 'rental' ? Helpers::storeRentalNotificationDataSetup(Helpers::get_store_id()) : Helpers::storeNotificationDataSetup(Helpers::get_store_id());
        }
        $data= StoreNotificationSetting::where('store_id',Helpers::get_store_id())->where('module_type',  $module_type == 'rental' ?'rental':'all' )->get();
        $business_name= BusinessSetting::where('key','business_name')->first()?->value;
        return view('vendor-views.business-settings.notification-index', compact('business_name' ,'data', 'module_type'));
    }

    public function notification_status_change($key, $type){
        $data= StoreNotificationSetting::where('store_id',Helpers::get_store_id())->where('key',$key)->first();
        if(!$data){
            Toastr::error(translate('messages.Notification_settings_not_found'));
            return back();
        }
        if($type == 'Mail' ) {
            $data->mail_status =  $data->mail_status == 'active' ? 'inactive' : 'active';
        }
        elseif($type == 'push_notification' ) {
            $data->push_notification_status =  $data->push_notification_status == 'active' ? 'inactive' : 'active';
        }
        elseif($type == 'SMS' ) {
            $data->sms_status =  $data->sms_status == 'active' ? 'inactive' : 'active';
        }
        $data?->save();

        Toastr::success(translate('messages.Notification_settings_updated'));
        return back();
    }
}
