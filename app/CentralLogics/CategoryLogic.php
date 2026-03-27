<?php

namespace App\CentralLogics;

use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Item;
use App\Models\PriorityList;
use App\Models\Store;
use Illuminate\Support\Facades\DB;

class CategoryLogic
{
    public static function parents()
    {
        return Category::where('position', 0)->get();
    }

    public static function child($parent_id)
    {
        return Category::where(['parent_id' => $parent_id])->get();
    }

    public static function products($category_id, $zone_id, int $limit,int $offset, $type)
    {

        $category_sub_category_item_default_status = BusinessSetting::where('key', 'category_sub_category_item_default_status')->first()?->value ?? 1;
        $category_sub_category_item_sort_by_general = PriorityList::where('name', 'category_sub_category_item_sort_by_general')->where('type','general')->first()?->value ?? '';
        $category_sub_category_item_sort_by_unavailable = PriorityList::where('name', 'category_sub_category_item_sort_by_unavailable')->where('type','unavailable')->first()?->value ?? '';
        $category_sub_category_item_sort_by_temp_closed = PriorityList::where('name', 'category_sub_category_item_sort_by_temp_closed')->where('type','temp_closed')->first()?->value ?? '';

        $query = Item::
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
            ->whereHas('category',function($q)use($category_id){
                return $q->when(is_numeric($category_id),function ($qurey) use($category_id){
                    return $qurey->whereId($category_id)->orWhere('parent_id', $category_id);
                })
                    ->when(!is_numeric($category_id),function ($qurey) use($category_id){
                        $qurey->where('slug', $category_id);
                    });
            })

            ->select(['items.*'])
            ->selectSub(function ($subQuery) {
                $subQuery->selectRaw('active as temp_available')
                    ->from('stores')
                    ->whereColumn('stores.id', 'items.store_id');
            }, 'temp_available')
            ->active()->type($type);

            if ($category_sub_category_item_default_status == '1'){
                $query = $query->latest();
            } else {
                if(config('module.current_module_data')['module_type']  !== 'food'){
                    if($category_sub_category_item_sort_by_unavailable == 'remove'){
                        $query = $query->where('stock', '>', 0);
                    }elseif($category_sub_category_item_sort_by_unavailable == 'last'){
                        $query = $query->orderByRaw('CASE WHEN stock = 0 THEN 1 ELSE 0 END');
                    }
                }

                if($category_sub_category_item_sort_by_temp_closed == 'remove'){
                    $query = $query->having('temp_available', '>', 0);
                }elseif($category_sub_category_item_sort_by_temp_closed == 'last'){
                    $query = $query->orderByDesc('temp_available');
                }

                if ($category_sub_category_item_sort_by_general == 'rating') {
                    $query = $query->orderByDesc('avg_rating');
                } elseif ($category_sub_category_item_sort_by_general == 'review_count') {
                    $query = $query->withCount('reviews')->orderByDesc('reviews_count');

                } elseif ($category_sub_category_item_sort_by_general == 'a_to_z') {
                    $query = $query->orderBy('name');
                } elseif ($category_sub_category_item_sort_by_general == 'z_to_a') {
                    $query = $query->orderByDesc('name');
                } elseif ($category_sub_category_item_sort_by_general == 'order_count') {
                    $query = $query->orderByDesc('order_count');
                }

            }

            $paginator = $query->paginate($limit, ['*'], 'page', $offset);


        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
    }

    public static function category_products($category_ids, $zone_id, int $limit,int $offset, $type, $filter=null, $min=false, $max=false, $rating_count=null, $brand_ids = null)
    {

        $category_sub_category_item_default_status = BusinessSetting::where('key', 'category_sub_category_item_default_status')->first()?->value ?? 1;
        $category_sub_category_item_sort_by_general = PriorityList::where('name', 'category_sub_category_item_sort_by_general')->where('type','general')->first()?->value ?? '';
        $category_sub_category_item_sort_by_unavailable = PriorityList::where('name', 'category_sub_category_item_sort_by_unavailable')->where('type','unavailable')->first()?->value ?? '';
        $category_sub_category_item_sort_by_temp_closed = PriorityList::where('name', 'category_sub_category_item_sort_by_temp_closed')->where('type','temp_closed')->first()?->value ?? '';

        $category_ids = isset($category_ids)?(is_array($category_ids)?$category_ids:json_decode($category_ids)):[];
        $brand_ids = isset($brand_ids)?(is_array($brand_ids)?$brand_ids:json_decode($brand_ids)):[];
        $filter = $filter?(is_array($filter)?$filter:str_getcsv(trim($filter, "[]"), ',')):'';

        $query = Item::
            whereHas('module.zones', function($query)use($zone_id){
                $query->whereIn('zones.id', json_decode($zone_id, true));
            })
            ->whereHas('store', function($query)use($zone_id , $filter){
                return $query->when(config('module.current_module_data'), function($query){
                    return  $query->where('module_id', config('module.current_module_data')['id'])->whereHas('zone.modules',function($query){
                        return  $query->where('modules.id', config('module.current_module_data')['id']);
                    });
                })->whereIn('zone_id', json_decode($zone_id, true))

                ->when($filter&&in_array('free_delivery',$filter),function ($qurey){
                    return $qurey->where('free_delivery',1);
                })

                ->when($filter&&in_array('coupon',$filter),function ($qurey){
                    return $qurey->has('activeCoupons');
                });

            })
            ->when(isset($category_ids) && (count($category_ids)>0), function($query)use($category_ids){
                return $query->whereHas('category',function($q)use($category_ids){
                    return $q->whereIn('id',$category_ids)->orWhereIn('parent_id', $category_ids);
                });
            })
            ->when(isset($brand_ids) && (count($brand_ids)>0), function($query)use($brand_ids){
                return $query->whereHas('ecommerce_item_details',function($q)use($brand_ids){
                    return $q->whereHas('brand',function($q)use($brand_ids){
                        return $q->whereIn('id',$brand_ids);
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

            $query = $query->when($rating_count, function($query) use ($rating_count){
                return $query->where('avg_rating', '>=' , $rating_count);
            });
            $query = $query->when($min && $max, function($query)use($min,$max){
                return $query->whereBetween('price',[$min,$max]);
            });
            $query = $query->when($filter&&in_array('top_rated',$filter),function ($qurey){
                return $qurey->withCount('reviews')->orderBy('reviews_count','desc');
            });
            $query = $query->when($filter&&in_array('popular',$filter),function ($qurey){
                return $qurey->popular();
            });
            $query = $query->when($filter&&in_array('high',$filter),function ($qurey){
                return  $qurey->orderByDesc('price');
            });
            $query = $query->when($filter&&in_array('low',$filter),function ($qurey){
                return $qurey->orderBy('price', 'asc');
            });
            $query = $query->when($filter&&in_array('discounted',$filter),function ($qurey){
                return $qurey->Discounted()->orderBy('discount','desc');
            });
            $query = $query->when($filter && in_array('available_now', $filter), function ($query) {
                $query->where(function ($q) {
                    $currentTime = now()->format('H:i:s');

                    $q->whereRaw("(available_time_starts < available_time_ends AND TIME(?) BETWEEN available_time_starts AND available_time_ends)", [$currentTime])
                    ->orWhereRaw("(available_time_starts > available_time_ends AND (TIME(?) >= available_time_starts OR TIME(?) <= available_time_ends))", [$currentTime, $currentTime]);
                });
            });

            if ($category_sub_category_item_default_status != '1'){
                $query = $query->latest();
            } else {

                if(config('module.current_module_data')['module_type']  !== 'food'){
                    if($category_sub_category_item_sort_by_unavailable == 'remove'){
                        $query = $query->where('stock', '>', 0);
                    }elseif($category_sub_category_item_sort_by_unavailable == 'last'){
                        $query = $query->orderByRaw('CASE WHEN stock = 0 THEN 1 ELSE 0 END');
                    }
                }

                if($category_sub_category_item_sort_by_temp_closed == 'remove'){
                    $query = $query->having('temp_available', '>', 0);
                }elseif($category_sub_category_item_sort_by_temp_closed == 'last'){
                    $query = $query->orderByDesc('temp_available');
                }

                if ($category_sub_category_item_sort_by_general == 'rating') {
                    $query = $query->orderByDesc('avg_rating');
                } elseif ($category_sub_category_item_sort_by_general == 'review_count') {
                    $query = $query->withCount('reviews')->orderByDesc('reviews_count');

                } elseif ($category_sub_category_item_sort_by_general == 'a_to_z') {
                    $query = $query->orderBy('name');
                } elseif ($category_sub_category_item_sort_by_general == 'z_to_a') {
                    $query = $query->orderByDesc('name');
                } elseif ($category_sub_category_item_sort_by_general == 'order_count') {
                    $query = $query->orderByDesc('order_count');
                }

            }


            $item_categories =  $query->pluck('category_id')->toArray();
            $paginator = $query->paginate($limit, ['*'], 'page', $offset);
            $item_categories = array_unique($item_categories);

            $categories = Category::withCount(['products','childes'])->with(['childes' => function($query)  {
                $query->withCount(['products','childes']);
            }])
            ->where(['position'=>0,'status'=>1])
            ->when(config('module.current_module_data'), function($query){
                $query->module(config('module.current_module_data')['id']);
            })
            ->whereIn('id',$item_categories)
            ->orderBy('priority','desc')->get();

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items(),
            'categories' => $categories,
        ];
    }


    public static function category_stores($category_ids, $zone_id, int $limit,int $offset, $type,$longitude=0,$latitude=0,$filter=null,$rating_count=null)
    {
        $category_ids = isset($category_ids)?(is_array($category_ids)?$category_ids:json_decode($category_ids)):[];
        $paginator = Store::
        WithOpenWithDeliveryTime($longitude??0,$latitude??0)
            ->withCount(['items','campaigns'])
            ->when(isset($category_ids) && (count($category_ids)>0), function($query)use($category_ids){
                return $query->whereHas('items.category',function($q)use($category_ids){
                    return $q->whereIn('id',$category_ids)->orWhereIn('parent_id', $category_ids);
                });
            })
            ->when(config('module.current_module_data'), function($query)use($zone_id){
                return  $query->whereHas('zone.modules', function($query){
                    return $query->where('modules.id', config('module.current_module_data')['id']);
                })->module(config('module.current_module_data')['id']);
                if(!config('module.current_module_data')['all_zone_service']) {
                    return  $query->whereIn('zone_id', json_decode($zone_id, true));
                }
            })
            ->active()->type($type)

            ->when($filter && in_array('free_delivery',$filter),function ($qurey){
                return $qurey->where('free_delivery',1);
            })
            ->when($filter && in_array('coupon',$filter),function ($qurey){
                return $qurey->has('activeCoupons');
            })

            ->when($rating_count, function($query) use ($rating_count){
                return $query->selectSub(function ($query) use ($rating_count){
                    return  $query->selectRaw('AVG(reviews.rating)')
                        ->from('reviews')
                        ->join('items', 'items.id', '=', 'reviews.item_id')
                        ->whereColumn('items.store_id', 'stores.id')
                        ->groupBy('items.store_id')
                        ->havingRaw('AVG(reviews.rating) >= ?', [$rating_count]);
                }, 'avg_r')->having('avg_r', '>=', $rating_count);
            })
            ->when($filter && in_array('top_rated',$filter),function ($qurey){
                return $qurey->whereNotNull('rating')->whereRaw("LENGTH(rating) > 0");
            })
            ->when($filter && in_array('discounted',$filter),function ($qurey){
                return  $qurey->where(function ($query) {
                    return  $query->whereHas('items', function ($q) {
                        return $q->Discounted();
                    });
                });
            })
            ->when($filter && in_array('currently_open',$filter),function ($qurey){
                return $qurey->having('open', '>', 0);
            })
            ->orderBy('open', 'desc')
            ->when($filter && in_array('popular',$filter),function ($qurey){
                return $qurey->withCount('orders')->orderBy('orders_count', 'desc');
            })
            ->when(($filter && in_array('nearby',$filter)) ,function ($qurey){
                return $qurey->orderBy('distance');
            })
            ->when($filter && in_array('fast_delivery',$filter),function ($qurey){
                return $qurey->orderBy('min_delivery_time');
            })
            ->latest()
            ->paginate($limit, ['*'], 'page', $offset);


        $paginator->each(function ($store) {
            $category_ids = DB::table('items')
                ->join('categories', 'items.category_id', '=', 'categories.id')
                ->selectRaw('
                CAST(categories.id AS UNSIGNED) as id,
                categories.parent_id
            ')
                ->where('items.store_id', $store->id)
                ->where('categories.status', 1)
                ->groupBy('id', 'categories.parent_id')
                ->get();

            $data = json_decode($category_ids, true);

            $mergedIds = [];

            foreach ($data as $item) {
                if ($item['id'] != 0) {
                    $mergedIds[] = $item['id'];
                }
                if ($item['parent_id'] != 0) {
                    $mergedIds[] = $item['parent_id'];
                }
            }

            $category_ids = array_values(array_unique($mergedIds));

            $store->category_ids = $category_ids;
            $store->discount_status = !empty($store->items->where('discount', '>', 0));
            unset($store['items']);
        });

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'stores' => $paginator->items()
        ];
    }


    public static function stores($category_id, $zone_id, int $limit,int $offset, $type,$longitude=0,$latitude=0)
    {
        $paginator = Store::
        withOpen($longitude??0,$latitude??0)
            ->withCount(['items','campaigns'])
            ->whereHas('items.category',function($q)use($category_id){
                return $q->when(is_numeric($category_id),function ($qurey) use($category_id){
                    return $qurey->whereId($category_id)->orWhere('parent_id', $category_id);
                })
                    ->when(!is_numeric($category_id),function ($qurey) use($category_id){
                        $qurey->where('slug', $category_id);
                    });
            })
            ->when(config('module.current_module_data'), function($query)use($zone_id){
                $query->whereHas('zone.modules', function($query){
                    $query->where('modules.id', config('module.current_module_data')['id']);
                })->module(config('module.current_module_data')['id']);
                if(!config('module.current_module_data')['all_zone_service']) {
                    $query->whereIn('zone_id', json_decode($zone_id, true));
                }
            })
            ->active()->type($type)
            ->latest()->paginate($limit, ['*'], 'page', $offset);


        $paginator->each(function ($store) {
            $category_ids = DB::table('items')
                ->join('categories', 'items.category_id', '=', 'categories.id')
                ->selectRaw('
                CAST(categories.id AS UNSIGNED) as id,
                categories.parent_id
            ')
                ->where('items.store_id', $store->id)
                ->where('categories.status', 1)
                ->groupBy('id', 'categories.parent_id')
                ->get();

            $data = json_decode($category_ids, true);

            $mergedIds = [];

            foreach ($data as $item) {
                if ($item['id'] != 0) {
                    $mergedIds[] = $item['id'];
                }
                if ($item['parent_id'] != 0) {
                    $mergedIds[] = $item['parent_id'];
                }
            }

            $category_ids = array_values(array_unique($mergedIds));

            $store->category_ids = $category_ids;
            $store->discount_status = !empty($store->items->where('discount', '>', 0));
            unset($store['items']);
        });

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'stores' => $paginator->items()
        ];
    }


    public static function all_products($id, $zone_id)
    {
        $cate_ids=[];
        array_push($cate_ids,(int)$id);
        foreach (CategoryLogic::child($id) as $ch1){
            array_push($cate_ids,$ch1['id']);
            foreach (CategoryLogic::child($ch1['id']) as $ch2){
                array_push($cate_ids,$ch2['id']);
            }
        }

        return Item::whereIn('category_id', $cate_ids)
            ->whereHas('module.zones', function($query)use($zone_id){
                $query->whereIn('zones.id', json_decode($zone_id, true));
            })
            ->whereHas('store', function($query)use($zone_id){
                $query->whereIn('zone_id', json_decode($zone_id, true))->whereHas('zone.modules',function($query){
                    $query->when(config('module.current_module_data'), function($query){
                        $query->where('modules.id', config('module.current_module_data')['id']);
                    });
                });
            })
            ->get();
    }


    public static function featured_category_products($zone_id, int $limit,int $offset, $type)
    {
        $paginator = Item::active()->type($type)
            ->whereHas('module.zones', function($query)use($zone_id){
                $query->whereIn('zones.id', json_decode($zone_id, true));
            })
            ->whereHas('store', function($query)use($zone_id){
                $query->whereIn('zone_id', json_decode($zone_id, true))->whereHas('zone.modules',function($query){
                    $query->when(config('module.current_module_data'), function($query){
                        $query->where('modules.id', config('module.current_module_data')['id']);
                    });
                });
            })
            ->where(function($query){
                $query->whereHas('category',function($q){
                    return $q->where(['featured' => 1 , 'status' => 1 , 'module_id' => config('module.current_module_data')['id']]);
                })
                ->orwherehas('category.parent',function($query){
                    $query->where(['featured' => 1 , 'status' => 1 , 'module_id' => config('module.current_module_data')['id']]);
                });
            })

            ->latest()->paginate($limit, ['*'], 'page', $offset);

        $item_categories = Item::active()->type($type)
            ->whereHas('module.zones', function($query)use($zone_id){
                $query->whereIn('zones.id', json_decode($zone_id, true));
            })
            ->whereHas('store', function($query)use($zone_id){
                $query->whereIn('zone_id', json_decode($zone_id, true))->whereHas('zone.modules',function($query){
                    $query->when(config('module.current_module_data'), function($query){
                        $query->where('modules.id', config('module.current_module_data')['id']);
                    });
                });
            })
            ->where(function($query){
                $query->whereHas('category',function($q){
                    return $q->where(['featured' => 1 , 'status' => 1 , 'module_id' => config('module.current_module_data')['id']]);
                })
                ->orwherehas('category.parent',function($query){
                    $query->where(['featured' => 1 , 'status' => 1 , 'module_id' => config('module.current_module_data')['id']]);
                });
            })


            ->pluck('category_id')->toArray();

        $item_categories = array_unique($item_categories);

        $categories = Category::where(['status' => 1])->whereIn('id',$item_categories)->get(['id','name','image']);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'categories' => $categories,
            'products' => $paginator->items()
        ];
    }
}
