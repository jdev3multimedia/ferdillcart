<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Item;
use App\Models\Brand;
use App\Models\PriorityList;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function get_brands(Request $request,$search=null)
    {
        Helpers::setZoneIds($request);

        try {
            $brand_default_status = BusinessSetting::where('key', 'brand_default_status')->first()?->value ?? 1;
            $brand_sort_by_general = PriorityList::where('name', 'brand_sort_by_general')->where('type','general')->first()?->value ?? '';
            $key = explode(' ', $search);

            $zone_id= $request->header('zoneId');
            $module_id= getModuleId($request->header('moduleId'));

            $brands = Brand::Active()
            ->where(function($query) use($module_id){
                $query->whereNull('module_id')->orWhere('module_id',  $module_id);
            })
            ->withCount(['items' => function($query) use($zone_id, $module_id) {
                $query->whereHas('item.store', function($q) use($zone_id, $module_id) {
                    $q->when(isset($zone_id) ,function ($query) use($zone_id){
                        $query->whereIn('zone_id', json_decode($zone_id, true));
                    })
                    ->where('module_id', $module_id);
                });
            }])
            ->when($search, function($query)use($key){
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%". $value."%");
                    }
                });
            })
            ->when($brand_default_status  != 1 &&  $brand_sort_by_general == 'latest', function ($query) {
                $query->latest();
            })
            ->when($brand_default_status  != 1 &&  $brand_sort_by_general == 'oldest', function ($query) {
                $query->oldest();
            })
            ->when($brand_default_status  != 1 &&  $brand_sort_by_general == 'a_to_z', function ($query) {
                $query->orderby('name');
            })
            ->when($brand_default_status  != 1 &&  $brand_sort_by_general == 'z_to_a', function ($query) {
                $query->orderby('name','desc');
            })
            ->get();

            if($brand_default_status  != 1 &&  $brand_sort_by_general == 'order_count'){
                foreach ($brands as $brand) {
                    $productCountQuery = Item::active()
                        ->whereHas('ecommerce_item_details',function($q)use($brand){
                            return $q->whereHas('brand',function($q)use($brand){
                                return $q->when(is_numeric($brand->id),function ($qurey) use($brand){
                                    return $qurey->whereId($brand->id);
                                })
                                    ->when(!is_numeric($brand->id),function ($qurey) use($brand){
                                        $qurey->where('slug', $brand->id);
                                    });
                            });
                        })
                        ->withCount('orders');

                    $orderCount = $productCountQuery->sum('order_count');

                    $brand['order_count'] = $orderCount;
                }

                $brands = $brands->sortByDesc('order_count')->values()->all();
            }
            return response()->json($brands, 200);
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

        $brand_item_default_status = BusinessSetting::where('key', 'brand_item_default_status')->first()?->value ?? 1;
        $brand_item_sort_by_general = PriorityList::where('name', 'brand_item_sort_by_general')->where('type','general')->first()?->value ?? '';
        $brand_item_sort_by_unavailable = PriorityList::where('name', 'brand_item_sort_by_unavailable')->where('type','unavailable')->first()?->value ?? '';
        $brand_item_sort_by_temp_closed = PriorityList::where('name', 'brand_item_sort_by_temp_closed')->where('type','temp_closed')->first()?->value ?? '';

        $zone_id= $request->header('zoneId');

        $type = $request->query('type', 'all');
        $limit = $request['limit'];
        $offset = $request['offset'];

        $query = Item::
        whereHas('module.zones', function($query)use($zone_id){
            $query->whereIn('zones.id', json_decode($zone_id, true));
        })
        ->when(config('module.current_module_data'), function($query){
            $query->where('module_id', config('module.current_module_data')['id']);
        })
        ->whereHas('store', function($query)use($zone_id){
            $query->whereIn('zone_id', json_decode($zone_id, true))->whereHas('zone.modules',function($query){
                $query->when(config('module.current_module_data'), function($query){
                    $query->where('modules.id', config('module.current_module_data')['id']);
                });
            });
        })
        ->whereHas('ecommerce_item_details',function($q)use($id){
            return $q->whereHas('brand',function($q)use($id){
                return $q->when(is_numeric($id),function ($qurey) use($id){
                    return $qurey->whereId($id);
                })
                ->when(!is_numeric($id),function ($qurey) use($id){
                    $qurey->where('slug', $id);
                });
            });
        })
        ->select(['items.*'])
        ->selectSub(function ($subQuery) {
            $subQuery->selectRaw('active as temp_available')
                ->from('stores')
                ->whereColumn('stores.id', 'items.store_id');
        }, 'temp_available')
        ->active()->type($type);

        if ($brand_item_default_status == '1'){
            $query = $query->latest();
        } else {
            if(config('module.current_module_data')['module_type']  !== 'food'){
                if($brand_item_sort_by_unavailable == 'remove'){
                    $query = $query->where('stock', '>', 0);
                }elseif($brand_item_sort_by_unavailable == 'last'){
                    $query = $query->orderByRaw('CASE WHEN stock = 0 THEN 1 ELSE 0 END');
                }
            }

            if($brand_item_sort_by_temp_closed == 'remove'){
                $query = $query->having('temp_available', '>', 0);
            }elseif($brand_item_sort_by_temp_closed == 'last'){
                $query = $query->orderByDesc('temp_available');
            }

            if ($brand_item_sort_by_general == 'rating') {
                $query = $query->orderByDesc('avg_rating');
            } elseif ($brand_item_sort_by_general == 'review_count') {
                $query = $query->withCount('reviews')->orderByDesc('reviews_count');

            } elseif ($brand_item_sort_by_general == 'a_to_z') {
                $query = $query->orderBy('name');
            } elseif ($brand_item_sort_by_general == 'z_to_a') {
                $query = $query->orderByDesc('name');
            } elseif ($brand_item_sort_by_general == 'order_count') {
                $query = $query->orderByDesc('order_count');
            }

        }

        $paginator = $query->paginate($limit, ['*'], 'page', $offset);
        $data=[
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
        $data['products'] = Helpers::product_data_formatting($data['products'] , true, false, app()->getLocale());
        return response()->json($data, 200);
    }
}
