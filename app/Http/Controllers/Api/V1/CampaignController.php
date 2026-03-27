<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\ItemCampaign;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    public function get_basic_campaigns(Request $request){
        Helpers::setZoneIds($request);
        $zone_id= $request->header('zoneId');
        try {
            $campaigns = Campaign::
            whereHas('module.zones',function($query)use($zone_id){
                $query->whereIn('zones.id', json_decode($zone_id, true));
            })
            ->when(config('module.current_module_data'), function($query)use($zone_id){
                $query->module(config('module.current_module_data')['id']);
                if(!config('module.current_module_data')['all_zone_service']) {
                    $query->whereHas('stores', function($q)use($zone_id){
                        $q->whereIn('zone_id', json_decode($zone_id, true));
                    });
                }
            })
            ->running()->active()->get();
            $campaigns=Helpers::basic_campaign_data_formatting($campaigns, true);
            return response()->json($campaigns, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
    public function basic_campaign_details(Request $request){
        Helpers::setZoneIds($request);
        $zone_id= $request->header('zoneId');
        $longitude= $request->header('longitude');
        $latitude= $request->header('latitude');
        $validator = Validator::make($request->all(), [
            'basic_campaign_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        try {
            $campaign = Campaign::with(['stores'=>function($q)use($zone_id,$longitude,$latitude){
                $q->withOpen($longitude??0,$latitude??0)->Active()->where('campaign_status','confirmed')->when(config('module.current_module_data'), function($query){
                    $query->where('module_id', config('module.current_module_data')['id'])->whereHas('zone.modules',function($query){
                        $query->where('modules.id', config('module.current_module_data')['id']);
                    });
                })->whereIn('zone_id', json_decode($zone_id, true));
                if(!config('module.current_module_data')['all_zone_service']) {
                    $q->whereIn('zone_id', json_decode($zone_id, true));
                }
            }])
            ->whereHas('module.zones', function($query)use($zone_id){
                $query->whereIn('zones.id', json_decode($zone_id, true));
            })
            ->running()
            ->active()
            ->where(fn($q) => $q->where('id', $request->basic_campaign_id)->orWhere('slug', $request->basic_campaign_id))
            ->first();

            $campaign=Helpers::basic_campaign_data_formatting($campaign, false);

            $campaign['stores'] = Helpers::store_data_formatting($campaign['stores'], true);

            return response()->json($campaign, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
    public function get_item_campaigns(Request $request){
        Helpers::setZoneIds($request);
        $zone_id= $request->header('zoneId');
        $item_campaign_default_status = \App\Models\BusinessSetting::where('key', 'item_campaign_default_status')->first()?->value ??  1;
        $item_campaign_sort_by_general = \App\Models\PriorityList::where('name', 'item_campaign_sort_by_general')->where('type','general')->first()?->value ?? '';
        try {
            $query = ItemCampaign::active()
            ->whereHas('module.zones', function($query)use($zone_id){
                $query->whereIn('zones.id', json_decode($zone_id, true));
            })
            ->whereHas('store', function($query)use($zone_id){
                $query->Active()->when(config('module.current_module_data'), function($query){
                    $query->where('module_id', config('module.current_module_data')['id'])->whereHas('zone.modules',function($query){
                        $query->where('modules.id', config('module.current_module_data')['id']);
                    });
                })->whereIn('zone_id', json_decode($zone_id, true));
            })
            ->running();

            if($item_campaign_default_status == 1){
                $query = $query->latest();

            } else{
                if ($item_campaign_sort_by_general == 'order_count') {
                    $query = $query->withCount([
                        'orderdetails' => function ($query) {
                                $query->whereHas('order', function ($query) {
                                    return $query->whereIn('order_status', ['delivered', 'refund_requested', 'refund_request_canceled']);
                                });
                        },
                    ])->orderByDesc('orderdetails_count');
                } elseif ($item_campaign_sort_by_general == 'a_to_z') {
                    $query = $query->orderBy('title');
                } elseif ($item_campaign_sort_by_general == 'z_to_a') {
                    $query = $query->orderByDesc('title');
                } elseif ($item_campaign_sort_by_general == 'end_first') {
                    $query = $query->orderBy('end_date');
                } elseif ($item_campaign_sort_by_general == 'latest_created') {
                    $query = $query->latest();
                }
            }

            $campaigns =  $query->get();
            $campaigns= Helpers::product_data_formatting($campaigns, true, false, app()->getLocale());
            return response()->json($campaigns, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
}
