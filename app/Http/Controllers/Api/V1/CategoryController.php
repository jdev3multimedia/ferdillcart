<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\CategoryLogic;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    public function get_categories(Request $request,$search=null)
    {
        try {
        $category_list_default_status =Helpers::get_business_settings('category_list_default_status') ?? 1;
        $category_list_sort_by_general = Helpers::getPriorityList(name: 'category_list_sort_by_general', type: 'general');
            $zone_id=  $request->header('zoneId') ? json_decode($request->header('zoneId'), true) : [];
            $zoneIds = implode(',', $zone_id);
            $key = explode(' ', $search);
            $featured = $request->query('featured');

            // select('categories.*')
            //  ->selectRaw(" ( SELECT COUNT(*)  FROM items JOIN stores ON stores.id = items.store_id
            //                 WHERE items.is_approved = 1
            //                     AND JSON_CONTAINS(items.category_ids, JSON_OBJECT('id', CAST(categories.id AS CHAR)), '$')
            //                     AND JSON_CONTAINS(items.category_ids, JSON_OBJECT('position', 1), '$')
            //                     AND stores.zone_id IN ($zoneIds) $moduleCondition
            //             ) AS products_count,
            //             ( SELECT COALESCE(SUM(items.order_count), 0) FROM items
            //                 JOIN stores ON stores.id = items.store_id
            //                 WHERE items.is_approved = 1
            //                     AND JSON_CONTAINS(items.category_ids, JSON_OBJECT('id', CAST(categories.id AS CHAR)), '$')
            //                     AND JSON_CONTAINS(items.category_ids, JSON_OBJECT('position', 1), '$')
            //                     AND stores.zone_id IN ($zoneIds)  $moduleCondition
            //             ) AS total_order_count
            //         ")

            $categories = Category:: with(['childes' => function($query)  {
                $query->where('status',1)->select('id','name','image','slug','parent_id') ;
            }]);


            if($category_list_default_status != 1 && $category_list_sort_by_general == 'order_count' ){
                $moduleCondition = config('module.current_module_data') ? 'AND items.module_id = '.(int)config('module.current_module_data')['id'] : '';
                $categories= $categories->select('categories.*')
             ->selectRaw("(SELECT COALESCE(SUM(items.order_count), 0) FROM items
                            JOIN stores ON stores.id = items.store_id
                            WHERE items.is_approved = 1
                                AND JSON_CONTAINS(items.category_ids, JSON_OBJECT('id', CAST(categories.id AS CHAR)), '$')
                                AND JSON_CONTAINS(items.category_ids, JSON_OBJECT('position', 1), '$')
                                AND stores.zone_id IN ($zoneIds)  $moduleCondition ) AS total_order_count");
            }else{
              $categories= $categories->select('id','name','image','slug');
            }

             $categories= $categories->where(['position'=>0,'status'=>1])
            ->when(config('module.current_module_data'), function($query){
                $query->module(config('module.current_module_data')['id']);
            })
            ->when($featured, function($query){
                $query->featured();
            })
            ->when($search, function($query)use($key){
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%". $value."%");
                    }
                });
            });

            if($category_list_default_status  == 1){
                $categories= $categories->orderBy('priority','desc');
            } else {
                $categories = match ($category_list_sort_by_general) {
                        'latest' =>$categories->latest(),
                        'oldest' => $categories->oldest(),
                        'a_to_z' =>  $categories->orderby('name'),
                        'z_to_a' =>  $categories->orderby('name','desc'),
                        'order_count' =>  $categories->orderByDesc('total_order_count'),
                         default => $categories,
                    };
            }


        $categories = $categories->get()
            ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'image_full_url' => $category->image_full_url,
                        // 'products_count' => $category->products_count,
                        // 'order_count' => (int) $category->total_order_count ,
                        'slug' => $category->slug,
                        'childes' => $category->childes->map(function ($child) {
                            return [
                                'id' => $child->id,
                                'name' => $child->name,
                                'slug' => $child->slug,
                            ];
                        })
                    ];
                });

            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function get_childes($id)
    {
        try {
            $categories = Category::with('parent')->where(['parent_id' => $id,'status'=>1])->orderBy('priority','desc')->get();
            return response()->json($categories, 200);
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

        $data = CategoryLogic::products($id, $zone_id, $request['limit'], $request['offset'], $type);
        $data['products'] = Helpers::product_data_formatting($data['products'] , true, false, app()->getLocale());
        return response()->json($data, 200);
    }

    public function get_category_products(Request $request)
    {
        Helpers::setZoneIds($request);
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
            'category_ids' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $zone_id= $request->header('zoneId');

        $type = $request->query('type', 'all');
        $category_ids = $request['category_ids']?json_decode($request['category_ids']):'';

        $data = CategoryLogic::category_products($category_ids, $zone_id, $request['limit'], $request['offset'], $type);
        $data['products'] = Helpers::product_data_formatting($data['products'] , true, false, app()->getLocale());
        return response()->json($data, 200);
    }


    public function get_stores($id, Request $request)
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
        $longitude= $request->header('longitude');
        $latitude= $request->header('latitude');
        $type = $request->query('type', 'all');

        $data = CategoryLogic::stores($id, $zone_id, $request['limit'], $request['offset'], $type,$longitude,$latitude);
        $data['stores'] = Helpers::store_data_formatting($data['stores'] , true);
        return response()->json($data, 200);
    }

    public function get_category_stores(Request $request)
    {
        Helpers::setZoneIds($request);
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
            'category_ids' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $zone_id= $request->header('zoneId');
        $longitude= $request->header('longitude');
        $latitude= $request->header('latitude');
        $type = $request->query('type', 'all');
        $category_ids = $request['category_ids']?json_decode($request['category_ids']):'';

        $data = CategoryLogic::category_stores($category_ids, $zone_id, $request['limit'], $request['offset'], $type,$longitude,$latitude);
        $data['stores'] = Helpers::store_data_formatting($data['stores'] , true);
        return response()->json($data, 200);
    }



    public function get_all_products($id,Request $request)
    {
        Helpers::setZoneIds($request);
        $zone_id= $request->header('zoneId');

        try {
            return response()->json(Helpers::product_data_formatting(CategoryLogic::all_products($id, $zone_id), true, false, app()->getLocale()), 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function get_featured_category_products(Request $request)
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

        $data = CategoryLogic::featured_category_products($zone_id, $request['limit'], $request['offset'], $type);
        $data['products'] = Helpers::product_data_formatting($data['products'] , true, false, app()->getLocale());
        return response()->json($data, 200);
    }

    public function get_popular_category_list(){

        $avg_items=Item::where('order_count','>=', 1 )->avg('order_count') ?? 0;

        $items= Item::where('order_count','>', $avg_items )->pluck('category_ids');
        $get_popular_category_ids = $items->flatMap(function($categoryIds) {
            $categories = json_decode($categoryIds, true);
                return collect($categories)->pluck('id');
            })->unique();
        $categories= Category::when(config('module.current_module_data'), function($query){
            $query->module(config('module.current_module_data')['id']);
        })
        ->whereIn('id',$get_popular_category_ids->toArray())->where(['position'=>0,'status'=>1])->take(20)->get();
        return response()->json($categories, 200);
    }
}
