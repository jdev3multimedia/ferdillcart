<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\StoreLogic;
use App\CentralLogics\CategoryLogic;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function get_stores(Request $request, $filter_data="all")
    {
        Helpers::setZoneIds($request);

        $type = $request->query('type', 'all');
        $store_type = $request->query('store_type', 'all');
        $zone_id= $request->header('zoneId');
        $longitude= $request->header('longitude');
        $latitude= $request->header('latitude');
        $stores = StoreLogic::get_stores( $zone_id, $filter_data, $type, $store_type, $request['limit'], $request['offset'], $request->query('featured'),$longitude,$latitude);
        $stores['stores'] = Helpers::store_data_formatting($stores['stores'], true);

        return response()->json($stores, 200);
    }

    public function get_latest_stores(Request $request, $filter_data="all")
    {
        Helpers::setZoneIds($request);

        $type = $request->query('type', 'all');

        $zone_id= $request->header('zoneId');
        $longitude= $request->header('longitude');
        $latitude= $request->header('latitude');
        $stores = StoreLogic::get_latest_stores($zone_id, $request['limit'], $request['offset'], $type,$longitude,$latitude);
        $stores['stores'] = Helpers::store_data_formatting($stores['stores'], true);

        return response()->json($stores, 200);
    }

    public function get_popular_stores(Request $request)
    {
        Helpers::setZoneIds($request);
        $type = $request->query('type', 'all');
        $zone_id= $request->header('zoneId');
        $longitude= $request->header('longitude');
        $latitude= $request->header('latitude');
        $stores = StoreLogic::get_popular_stores($zone_id, $request['limit'], $request['offset'], $type,$longitude,$latitude);
        $stores['stores'] = Helpers::store_data_formatting($stores['stores'], true);

        return response()->json($stores, 200);
    }

    public function get_discounted_stores(Request $request)
    {
        Helpers::setZoneIds($request);
        $type = $request->query('type', 'all');
        $zone_id= $request->header('zoneId');
        $longitude= $request->header('longitude');
        $latitude= $request->header('latitude');
        $stores = StoreLogic::get_discounted_stores($zone_id, $request['limit'], $request['offset'], $type,$longitude,$latitude);
        $stores['stores'] = Helpers::store_data_formatting($stores['stores'], true);

        return response()->json($stores, 200);
    }

    public function get_top_rated_stores(Request $request)
    {
        Helpers::setZoneIds($request);
        $type = $request->query('type', 'all');
        $zone_id= $request->header('zoneId');
        $longitude= $request->header('longitude');
        $latitude= $request->header('latitude');
        $stores = StoreLogic::get_top_rated_stores($zone_id, $request['limit'], $request['offset'], $type,$longitude,$latitude);
        $stores['stores'] = Helpers::store_data_formatting($stores['stores'], true);

        usort($stores['stores'], function ($a, $b) {
            $key = 'avg_rating';
            return $b[$key] - $a[$key];
        });

        return response()->json($stores, 200);
    }

    public function get_popular_store_items($id)
    {
        $items = Item::
        when(is_numeric($id),function ($qurey) use($id){
            $qurey->where('store_id', $id);
        })
        ->when(!is_numeric($id), function ($query) use ($id) {
            $query->whereHas('store', function ($q) use ($id) {
                $q->where('slug', $id);
            });
        })
        ->active()->popular()->limit(10)->get();
        $items = Helpers::product_data_formatting($items, true, true, app()->getLocale());

        return response()->json($items, 200);
    }

    public function get_details(Request $request,$id)
    {
        $longitude= $request->header('longitude');
        $latitude= $request->header('latitude');
        $store = StoreLogic::get_store_details($id,$longitude,$latitude);
        if($store)
        {
            $category_ids = DB::table('items')
            ->join('categories', 'items.category_id', '=', 'categories.id')
            ->selectRaw('categories.position as positions, IF((categories.position = "0"), categories.id, categories.parent_id) as categories')
            ->where('items.store_id', $store->id)
            ->where('categories.status',1)
            ->groupBy('categories','positions')
            ->get();

            $store = Helpers::store_data_formatting($store);
            $store['category_ids'] = array_map('intval', $category_ids->pluck('categories')->toArray());
            $store['category_details'] = Category::whereIn('id',$store['category_ids'])->get();
            $store['price_range']  = Item::withoutGlobalScopes()->where('store_id', $store->id)
            ->select(DB::raw('MIN(price) AS min_price, MAX(price) AS max_price'))
            ->get(['min_price','max_price'])->toArray();
        }
        return response()->json($store, 200);
    }

    public function get_searched_stores(Request $request)
    {
        Helpers::setZoneIds($request);
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $type = $request->query('type', 'all');

        $zone_id= $request->header('zoneId');
        $longitude= $request->header('longitude');
        $latitude= $request->header('latitude');
        $stores = StoreLogic::search_stores($request['name'], $zone_id, $request->category_id,$request['limit'], $request['offset'], $type,$longitude,$latitude);
        $stores['stores'] = Helpers::store_data_formatting($stores['stores'], true);
        return response()->json($stores, 200);
    }

    public function reviews(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $id = $request['store_id'];


        $reviews = Review::with(['customer', 'item'])
        ->whereHas('item', function($query)use($id){
            return $query->where('store_id', $id);
        })
        ->active()->latest()->get();

        $storage = [];
        foreach ($reviews as $temp) {
            $temp['attachment'] = json_decode($temp['attachment']);
            $temp['item_name'] = null;
            $temp['item_image'] = null;
            $temp['customer_name'] = null;
            // $temp->item=null;
            if($temp->item)
            {
                $temp['item_name'] = $temp->item->name;
                $temp['item_image'] = $temp->item->image;
                $temp['item_image_full_url'] = $temp->item->image_full_url;
                if(count($temp->item->translations)>0)
                {
                    $translate = array_column($temp->item->translations->toArray(), 'value', 'key');
                    $temp['item_name'] = $translate['name'];
                }
                unset($temp->item);
                $temp['item'] = Helpers::product_data_formatting($temp->item, false, false, app()->getLocale());
            }
            if($temp->customer)
            {
                $temp['customer_name'] = $temp->customer->f_name.' '.$temp->customer->l_name;
            }

            unset($temp['customer']);
            array_push($storage, $temp);
        }

        return response()->json($storage, 200);
    }


    public function get_recommended_stores(Request $request){


        Helpers::setZoneIds($request);
        $type = $request->query('type', 'all');
        $zone_id= $request->header('zoneId');
        $longitude= $request->header('longitude') ?? 0;
        $latitude= $request->header('latitude') ?? 0;
        $stores = StoreLogic::get_recommended_stores($zone_id, $request['limit'], $request['offset'], $type,$longitude,$latitude);
        $stores['stores'] = Helpers::store_data_formatting($stores['stores'], true);

        return response()->json($stores, 200);
    }

    public function get_combined_data(Request $request)
    {
        Helpers::setZoneIds($request);
        $zone_id = $request->header('zoneId');
        $data_type = $request->query('data_type', 'all');
        $type = $request->query('type', 'all');
        $limit = $request->query('limit', 10);
        $offset = $request->query('offset', 1);
        $longitude =  $request->header('longitude') ?? 0;
        $latitude =  $request->header('latitude') ?? 0;
        $filter = $request->query('filter', '');
        $filter = $filter?(is_array($filter)?$filter:str_getcsv(trim($filter, "[]"), ',')):'';
        $rating_count = $request->query('rating_count');

        switch ($data_type) {
            case 'searched':
                $validator = Validator::make($request->all(), ['name' => 'required']);
                if ($validator->fails()) {
                    return response()->json(['errors' => Helpers::error_processor($validator)], 403);
                }
                $name = $request->input('name');

                $paginator = StoreLogic::search_stores($name, $zone_id, $request->category_id, $limit, $offset, $type, $longitude, $latitude, $filter, $rating_count);
                break;

            case 'discounted':

                $paginator = StoreLogic::get_discounted_stores($zone_id, $limit, $offset, $type, $longitude, $latitude, $filter, $rating_count);
                break;

            case 'category':
                $validator = Validator::make($request->all(), [
                    'category_ids' => 'required|array',
                    'category_ids.*' => 'integer'
                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => Helpers::error_processor($validator)], 403);
                }

                $category_ids = $request->input('category_ids');

                $paginator = CategoryLogic::category_stores($category_ids, $zone_id, $limit, $offset, $type, $longitude, $latitude, $filter, $rating_count);
                break;

            default:
                $filter_data = $request->query('filter_data', 'all');
                $store_type = $request->query('store_type', 'all');
                $featured = $request->query('featured');
                $paginator = StoreLogic::get_stores($zone_id, $filter_data, $type, $store_type, $limit, $offset, $featured, $longitude, $latitude, $filter, $rating_count);
                break;
        }

        $paginator['stores'] = Helpers::store_data_formatting($paginator['stores'], true);
        return response()->json($paginator, 200);
    }

    public function get_top_offer_near_me(Request $request)
    {
        Helpers::setZoneIds($request);
        $type = $request->query('type', 'all');
        $zone_id= $request->header('zoneId');
        $longitude= $request->header('longitude');
        $latitude= $request->header('latitude');

        $stores = StoreLogic::get_top_offer_near_me(zone_id:$zone_id, limit:$request['limit'], offset: $request['offset'], type: $type, longitude:$longitude,latitude: $latitude,
                    name:$request->name, sort: $request->sort_by ,halal: $request->halal);
        $stores['stores'] = Helpers::store_data_formatting($stores['stores'], true);


        return response()->json($stores, 200);
    }

}
