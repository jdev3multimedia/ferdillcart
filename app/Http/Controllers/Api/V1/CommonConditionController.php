<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\BusinessSetting;
use App\Models\Item;
use App\Models\PriorityList;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\CommonCondition;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CommonConditionController extends Controller
{
    public function get_conditions(Request $request,$search=null)
    {
        try {
            $common_condition_default_status = BusinessSetting::where('key', 'common_condition_default_status')->first()?->value ?? 1;
            $common_condition_sort_by_general = PriorityList::where('name', 'common_condition_sort_by_general')->where('type','general')->first()?->value ?? '';
            $key = explode(' ', $search);
            $conditions = CommonCondition::Active()->withCount(['items'])
            ->when($search, function($query)use($key){
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%". $value."%");
                    }
                });
            })
            ->when($common_condition_default_status  != 1 &&  $common_condition_sort_by_general == 'latest', function ($query) {
                $query->latest();
            })
            ->when($common_condition_default_status  != 1 &&  $common_condition_sort_by_general == 'oldest', function ($query) {
                $query->oldest();
            })
            ->when($common_condition_default_status  != 1 &&  $common_condition_sort_by_general == 'a_to_z', function ($query) {
                $query->orderby('name');
            })
            ->when($common_condition_default_status  != 1 &&  $common_condition_sort_by_general == 'z_to_a', function ($query) {
                $query->orderby('name','desc');
            })
            ->get();


            if($common_condition_default_status  != 1 &&  $common_condition_sort_by_general == 'order_count'){
                foreach ($conditions as $condition) {
                    $productCountQuery = Item::active()
                        ->whereHas('pharmacy_item_details',function($q)use($condition){
                            return $q->whereHas('common_condition',function($q)use($condition){
                                return $q->when(is_numeric($condition->id),function ($qurey) use($condition){
                                    return $qurey->whereId($condition->id);
                                })
                                    ->when(!is_numeric($condition->id),function ($qurey) use($condition){
                                        $qurey->where('slug', $condition->id);
                                    });
                            });
                        })
                        ->withCount('orders');

                    $orderCount = $productCountQuery->sum('order_count');

                    $condition['order_count'] = $orderCount;
                }

                $conditions = $conditions->sortByDesc('order_count')->values()->all();
            }

            return response()->json($conditions, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function get_products($id, Request $request)
    {
        Helpers::setZoneIds($request);
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $zone_id= $request->header('zoneId');

        $type = $request->query('type', 'all');
        $limit = $request['limit'];
        $offset = $request['offset'];

        $paginator = Item::
        whereHas('module.zones', function($query)use($zone_id){
            $query->whereIn('zones.id', json_decode($zone_id, true));
        })
        ->whereHas('store', function($query)use($zone_id){
            $query->whereIn('zone_id', json_decode($zone_id, true))->whereHas('zone.modules',function($query){
                $query->when(config('module.current_module_data'), function($query){
                    $query->where('modules.id', config('module.current_module_data')['id']);
                });
            });
        })
        ->whereHas('pharmacy_item_details',function($q)use($id){
            return $q->whereHas('common_condition',function($q)use($id){
                return $q->when(is_numeric($id),function ($qurey) use($id){
                    return $qurey->whereId($id);
                })
                ->when(!is_numeric($id),function ($qurey) use($id){
                    $qurey->where('slug', $id);
                });
            });
        })
        ->active()->type($type)->latest()->paginate($limit, ['*'], 'page', $offset);
        $data=[
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
        $data['products'] = Helpers::product_data_formatting($data['products'] , true, false, app()->getLocale());
        return response()->json($data, 200);
    }
    public function getCommonConditionList(){
        $conditions = CommonCondition::Active()->get(['id','name']);
        return response()->json($conditions, 200);
    }
}
