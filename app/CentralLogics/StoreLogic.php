<?php

namespace App\CentralLogics;

use Exception;
use App\Models\Store;
use App\Models\Review;
use App\Models\DataSetting;
use App\Models\PriorityList;

use App\Models\StoreSchedule;
use App\Models\BusinessSetting;
use App\Models\OrderTransaction;
use Illuminate\Support\Facades\DB;

class StoreLogic
{
    public static function get_stores( $zone_id, $filter_data, $type, $store_type, $limit = 10, $offset = 1, $featured=false,$longitude=0,$latitude=0,$filter=null,$rating_count=null)
    {

        $all_stores_default_status = BusinessSetting::where('key', 'all_stores_default_status')->first()?->value ?? 1;
        $all_stores_sort_by_general = PriorityList::where('name', 'all_stores_sort_by_general')->where('type','general')->first()?->value ?? '';
        $all_stores_sort_by_unavailable = PriorityList::where('name', 'all_stores_sort_by_unavailable')->where('type','unavailable')->first()?->value ?? '';
        $all_stores_sort_by_temp_closed = PriorityList::where('name', 'all_stores_sort_by_temp_closed')->where('type','temp_closed')->first()?->value ?? '';

        $query = Store::type($type)->
        WithOpenWithDeliveryTime($longitude??0,$latitude??0)
            ->withCount(['items','campaigns','reviews','orders'])
            ->with(['discount'=>function($q){
                return $q->validate();
            }])
            ->whereHas('module',function($query){
                return  $query->active();
            })
            ->Active();
        if(config('module.current_module_data')) {
            $query = $query->whereHas('zone.modules', function($query){
                return  $query->where('modules.id', config('module.current_module_data')['id']);
            })->module(config('module.current_module_data')['id'])
                ->when(!config('module.current_module_data')['all_zone_service'], function($query)use($zone_id){
                    return  $query->whereIn('zone_id', json_decode($zone_id,true));
                });
        } else {
            $query = $query->whereIn('zone_id', json_decode($zone_id,true));
        }

            if($all_stores_default_status != '1') {
                if($all_stores_sort_by_temp_closed == 'remove'){
                    $query = $query->where('active', '>', 0);
                }elseif($all_stores_sort_by_temp_closed == 'last'){
                    $query = $query->orderByDesc('active');
                }

                if($all_stores_sort_by_unavailable == 'remove'){
                    $query = $query->having('open', '>', 0);
                }elseif($all_stores_sort_by_unavailable == 'last'){
                    $query = $query->orderBy('open', 'desc');
                }

                if($all_stores_sort_by_general == 'rating') {
                    $query = $query->selectSub(function ($query) {
                        $query->selectRaw('AVG(reviews.rating)')
                            ->from('reviews')
                            ->join('items', 'items.id', '=', 'reviews.item_id')
                            ->whereColumn('items.store_id', 'stores.id')
                            ->groupBy('items.store_id');
                    }, 'avg_r')->orderBy('avg_r', 'desc');
                }elseif($all_stores_sort_by_general == 'review_count') {
                    $query = $query->orderByDesc('reviews_count');
                }elseif($all_stores_sort_by_general == 'order_count') {
                    $query = $query->orderBy('orders_count', 'desc');
                }elseif($all_stores_sort_by_general == 'latest_created') {
                    $query = $query->latest();
                }elseif($all_stores_sort_by_general == 'first_created') {
                    $query = $query->oldest();
                }elseif($all_stores_sort_by_general == 'a_to_z') {
                    $query = $query->orderBy('name');
                }elseif($all_stores_sort_by_general == 'z_to_a') {
                    $query = $query->orderByDesc('name');
                }
            }
            $query = $query->when($filter && in_array('free_delivery',$filter),function ($qurey){
                return $qurey->where('free_delivery',1);
            });
            $query = $query->when($filter && in_array('coupon', $filter), function ($query) {
                return $query->has('activeCoupons');
            });
            $query = $query->when($store_type == 'all' && $filter && !in_array('fast_delivery',$filter), function($q){
                return $q->orderBy('open', 'desc')->orderBy('distance');
            });
            $query = $query->when($filter && in_array('currently_open', $filter), function ($query) {
                return $query->having('open', '>', 0);
            });
            $query = $query->when($store_type == 'newly_joined', function($q){
                return $q->latest();
            });
            $query = $query->when($rating_count, function($query) use ($rating_count){
                return  $query->selectSub(function ($query) use ($rating_count){
                    return $query->selectRaw('AVG(reviews.rating)')
                        ->from('reviews')
                        ->join('items', 'items.id', '=', 'reviews.item_id')
                        ->whereColumn('items.store_id', 'stores.id')
                        ->groupBy('items.store_id')
                        ->havingRaw('AVG(reviews.rating) >= ?', [$rating_count]);
                }, 'avg_r')->having('avg_r', '>=', $rating_count);
            });
            $query = $query->when(($filter && in_array('top_rated',$filter) ) || $store_type == 'top_rated' ,function ($qurey){
                return $qurey->whereNotNull('rating')->whereRaw("LENGTH(rating) > 0");
            });
            $query = $query->when(($filter && in_array('popular',$filter)) || $store_type == 'popular'  ,function ($qurey){
                return  $qurey->withCount('orders')->orderBy('orders_count', 'desc');
            });
            $query = $query->when($filter && in_array('discounted',$filter),function ($qurey){
                return $qurey->where(function ($query) {
                    return $query->whereHas('items', function ($q) {
                        $q->Discounted();
                    });
                });
            });
            $query = $query->when($filter && in_array('open',$filter),function ($qurey){
                return $qurey->orderBy('open', 'desc');
            });
            $query = $query->when(($filter && in_array('nearby',$filter))   ,function ($qurey){
                return  $qurey->orderByDesc('distance');
            });
            $query = $query->when($filter_data=='delivery', function($q){
                return $q->delivery();
            });

            $query = $query->when($filter_data=='take_away', function($q){
                return $q->takeaway();
            });
            $query = $query->when($featured, function($query){
                return $query->featured();
            });
            $query = $query->when($filter && in_array('fast_delivery',$filter) , function($q) {
                return $q->orderBy('open', 'desc')->orderBy('min_delivery_time');
            });

            if($all_stores_default_status == '1') {
                $query = $query->orderBy('open', 'desc');
            }


        $paginator = $query->paginate($limit??50, ['*'], 'page', $offset??1);



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

    public static function get_latest_stores($zone_id, $limit = 50, $offset = 1, $type='all',$longitude=0,$latitude=0)
    {
    $latest_stores_default_status =BusinessSetting::where('key', 'latest_stores_default_status')->first()?->value ?? 1;
    $latest_stores_sort_by_general =PriorityList::where('name', 'latest_stores_sort_by_general')->where('type','general')->first()?->value ?? '';
    $latest_stores_sort_by_unavailable =PriorityList::where('name', 'latest_stores_sort_by_unavailable')->where('type','unavailable')->first()?->value ?? '';
    $latest_stores_sort_by_temp_closed =PriorityList::where('name', 'latest_stores_sort_by_temp_closed')->where('type','temp_closed')->first()?->value ?? '';




    $query = Store::withOpen($longitude??0,$latitude??0)
            ->withCount(['items','campaigns','reviews'])
            ->with(['discount'=>function($q){
                return $q->validate();
            }])
            ->when(config('module.current_module_data'), function($query)use($zone_id){
                $query->whereHas('zone.modules', function($query){
                    $query->where('modules.id', config('module.current_module_data')['id']);
                })->module(config('module.current_module_data')['id']);
                if(!config('module.current_module_data')['all_zone_service']) {
                    $query->whereIn('zone_id', json_decode($zone_id, true));
                }
            })
            ->Active()
            ->type($type);



            if($latest_stores_default_status == '1'){
                $query = $query->latest();
            } else{

                if($latest_stores_default_status != '1') {
                    if($latest_stores_sort_by_unavailable == 'remove'){
                        $query = $query->where('active', '>', 0);
                    }elseif($latest_stores_sort_by_unavailable == 'last'){
                        $query = $query->orderByDesc('active');
                    }

                    if($latest_stores_sort_by_temp_closed == 'remove'){
                        $query = $query->having('open', '>', 0);
                    }elseif($latest_stores_sort_by_temp_closed == 'last'){
                        $query = $query->orderBy('open', 'desc');
                    }

                    if($latest_stores_sort_by_general == 'rating') {
                        $query = $query->selectSub(function ($query) {
                            $query->selectRaw('AVG(reviews.rating)')
                                ->from('reviews')
                                ->join('items', 'items.id', '=', 'reviews.item_id')
                                ->whereColumn('items.store_id', 'stores.id')
                                ->groupBy('items.store_id');
                        }, 'avg_r')->orderBy('avg_r', 'desc');
                    }elseif($latest_stores_sort_by_general == 'review_count') {
                        $query = $query->orderByDesc('reviews_count');
                    }elseif($latest_stores_sort_by_general == 'order_count') {
                        $query = $query->orderBy('orders_count', 'desc');
                    }elseif($latest_stores_sort_by_general == 'latest_created') {
                        $query = $query->latest();
                    }elseif($latest_stores_sort_by_general == 'first_created') {
                        $query = $query->oldest();
                    }elseif($latest_stores_sort_by_general == 'a_to_z') {
                        $query = $query->orderBy('name');
                    }elseif($latest_stores_sort_by_general == 'z_to_a') {
                        $query = $query->orderByDesc('name');
                    }
                }
            }


            $paginator = $query->paginate($limit??50, ['*'], 'page', $offset??1);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit??50,
            'offset' => $offset??1,
            'stores' => $paginator->items()
        ];
    }

    public static function get_popular_stores($zone_id, $limit = 50, $offset = 1, $type = 'all',$longitude=0,$latitude=0)
    {
        $popular_store_default_status = BusinessSetting::where('key', 'popular_store_default_status')->first()?->value ?? 1;
        $popular_store_sort_by_general = PriorityList::where('name', 'popular_store_sort_by_general')->where('type','general')->first()?->value ?? '';
        $popular_store_sort_by_unavailable = PriorityList::where('name', 'popular_store_sort_by_unavailable')->where('type','unavailable')->first()?->value ??'';
        $popular_store_sort_by_temp_closed = PriorityList::where('name', 'popular_store_sort_by_temp_closed')->where('type','temp_closed')->first()?->value ??'';
        $popular_store_sort_by_rating = PriorityList::where('name', 'popular_store_sort_by_rating')->where('type','rating')->first()?->value ??'';

        $query = Store::withOpen($longitude??0,$latitude??0)
            ->withCount(['items','campaigns'])
            ->with(['discount'=>function($q){
                return $q->validate();
            }])
            ->when(config('module.current_module_data'), function($query)use($zone_id){
                $query->whereHas('zone.modules', function($query){
                    $query->where('modules.id', config('module.current_module_data')['id']);
                })->module(config('module.current_module_data')['id']);
                if(!config('module.current_module_data')['all_zone_service']) {
                    $query->whereIn('zone_id', json_decode($zone_id, true));
                }
            })
            ->type($type)
            ->withCount('reviews')
            ->withCount('orders')->Active();

            if($popular_store_default_status == '1') {
                $query = $query->orderBy('open', 'desc')
                        ->orderBy('distance')
                        ->orderBy('orders_count', 'desc');
            }else{
                if($popular_store_sort_by_temp_closed == 'remove'){
                    $query = $query->where('active', '>', 0);
                }elseif($popular_store_sort_by_temp_closed == 'last'){
                    $query = $query->orderByDesc('active');
                }

                if($popular_store_sort_by_unavailable == 'remove'){
                    $query = $query->having('open', '>', 0);
                }elseif($popular_store_sort_by_unavailable == 'last'){
                    $query = $query->orderBy('open', 'desc');
                }

                $rating_threshold = 0;
                if($popular_store_sort_by_rating && ($popular_store_sort_by_rating != 'none')){
                    $rating_threshold = match($popular_store_sort_by_rating) {
                        'four_plus' => 4,
                        'three_half_plus' => 3.5,
                        'three_plus' => 3,
                        'two_plus' => 2,
                        default => 0
                    };
                }

                if($rating_threshold > 0 || $popular_store_sort_by_general == 'rating') {
                    $query = $query->selectSub(function ($query) use ($rating_threshold) {
                        $query->selectRaw('AVG(reviews.rating)')
                            ->from('reviews')
                            ->join('items', 'items.id', '=', 'reviews.item_id')
                            ->whereColumn('items.store_id', 'stores.id')
                            ->groupBy('items.store_id')
                            ->when($rating_threshold > 0, function($q) use ($rating_threshold) {
                                return $q->havingRaw('AVG(reviews.rating) >= ?', [$rating_threshold]);
                            });
                    }, 'store_rating');

                    if($rating_threshold > 0) {
                        $query->having('store_rating', '>=', $rating_threshold);
                    }

                    if($popular_store_sort_by_general == 'rating') {
                        $query->orderBy('store_rating', 'desc');
                    }
                } elseif($popular_store_sort_by_general == 'review_count') {
                    $query = $query->orderByDesc('reviews_count');
                } elseif($popular_store_sort_by_general == 'order_count') {
                    $query = $query->orderBy('orders_count', 'desc');
                } elseif($popular_store_sort_by_general == 'nearest_first') {
                    $query = $query->orderBy('distance');
                }
            }

        $paginator = $query->paginate($limit??50, ['*'], 'page', $offset??1);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit??50,
            'offset' => $offset??1,
            'stores' => $paginator->items()
        ];
    }

    public static function get_discounted_stores($zone_id, $limit = 50, $offset = 1, $type = 'all',$longitude=0,$latitude=0,$filter=null,$rating_count=null)
    {
        $paginator = Store::WithOpenWithDeliveryTime($longitude??0,$latitude??0)
            ->withCount(['items','campaigns'])
            ->with(['discount'=>function($q){
                return $q->validate();
            }])
            ->when(config('module.current_module_data'), function($query)use($zone_id){
                return   $query->whereHas('zone.modules', function($query){
                    return $query->where('modules.id', config('module.current_module_data')['id']);
                })->module(config('module.current_module_data')['id']);
                if(!config('module.current_module_data')['all_zone_service']) {
                    return  $query->whereIn('zone_id', json_decode($zone_id, true));
                }
            })
            ->where(function ($query) {
                return  $query->whereHas('items', function ($q) {
                    $q->Discounted();
                });
            })
            ->Active()
            ->type($type)
            ->when($filter && in_array('free_delivery',$filter),function ($qurey){
                return $qurey->where('free_delivery',1);
            })
            ->when($filter && in_array('coupon',$filter),function ($qurey){
                return $qurey->has('activeCoupons');
            })
            ->when($rating_count, function($query) use ($rating_count){
                return  $query->selectSub(function ($query) use ($rating_count){
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
            ->when($filter && in_array('currently_open',$filter),function ($qurey){
                return $qurey->having('open', '>', 0);
            })
            ->orderBy('open', 'desc')
            ->when($filter && in_array('popular',$filter),function ($qurey){
                return $qurey->withCount('orders')->orderBy('orders_count', 'desc');
            })
            ->when(($filter && in_array('nearby',$filter))   ,function ($qurey){
                return  $qurey->orderBy('distance');
            })
            ->when($filter && in_array('fast_delivery',$filter),function ($qurey){
                return $qurey->orderBy('min_delivery_time');
            })
            ->paginate($limit??50, ['*'], 'page', $offset??1);

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
            'limit' => $limit??50,
            'offset' => $offset??1,
            'stores' => $paginator->items()
        ];
    }

    public static function get_top_rated_stores($zone_id, $limit = 50, $offset = 1, $type = 'all',$longitude=0,$latitude=0)
    {
        $paginator = Store::withOpen($longitude??0,$latitude??0)->whereNotNull('rating')
            ->withCount(['items','campaigns'])
            ->with(['discount'=>function($q){
                return $q->validate();
            }])
            ->when(config('module.current_module_data'), function($query)use($zone_id){
                $query->whereHas('zone.modules', function($query){
                    $query->where('modules.id', config('module.current_module_data')['id']);
                })->module(config('module.current_module_data')['id']);
                if(!config('module.current_module_data')['all_zone_service']) {
                    $query->whereIn('zone_id', json_decode($zone_id, true));
                }
            })
            ->Active()
            ->type($type)
            ->whereRaw("LENGTH(rating) > 0")
            ->paginate($limit??50, ['*'], 'page', $offset??1);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit??50,
            'offset' => $offset??1,
            'stores' => $paginator->items()
        ];
    }

    public static function get_store_details($store_id,$longitude=0,$latitude=0)
    {
        return Store::withOpen($longitude??0,$latitude??0)->with(['discount'=>function($q){
            return $q->validate();
        }, 'campaigns', 'schedules','activeCoupons','store_sub'])
            ->withCount(['items','campaigns','reviews_comments'])
            ->when(config('module.current_module_data'), function($query){
                $query->module(config('module.current_module_data')['id']);
            })
            ->when(is_numeric($store_id),function ($qurey) use($store_id){
                $qurey->where('id', $store_id);
            })
            ->when(!is_numeric($store_id),function ($qurey) use($store_id){
                $qurey->where('slug', $store_id);
            })
            ->first();
    }

    public static function calculate_store_rating($ratings)
    {
        $total_submit = $ratings[0]+$ratings[1]+$ratings[2]+$ratings[3]+$ratings[4];
        $positive_submit = $ratings[0]+$ratings[1]+$ratings[2];
        $rating = ($ratings[0]*5+$ratings[1]*4+$ratings[2]*3+$ratings[3]*2+$ratings[4])/($total_submit?$total_submit:1);
        $positive_rating = $total_submit>0?(($positive_submit*100)/$total_submit):0;
        return ['rating'=>round($rating,2), 'total'=>$total_submit, 'positive_rating'=>$positive_rating];
    }

    public static function update_store_rating($ratings, $product_rating)
    {
        $store_ratings = [1=>0 , 2=>0, 3=>0, 4=>0, 5=>0];
        if($ratings)
        {
            $store_ratings[1] = $ratings[4];
            $store_ratings[2] = $ratings[3];
            $store_ratings[3] = $ratings[2];
            $store_ratings[4] = $ratings[1];
            $store_ratings[5] = $ratings[0];
            $store_ratings[$product_rating] = $ratings[5-$product_rating] + 1;
        }
        else
        {
            $store_ratings[$product_rating] = 1;
        }
        return json_encode($store_ratings);
    }

    public static function search_stores($name, $zone_id, $category_id= null,$limit = 10, $offset = 1, $type = 'all',$longitude=0,$latitude=0,$filter=null,$rating_count=null,$category_ids=null)
    {
        $key = explode(' ', $name);
        $paginator = Store::WithOpenWithDeliveryTime($longitude??0,$latitude??0)
        ->whereHas('zone.modules', function($query){
            return $query->where('modules.id', config('module.current_module_data')['id']);
        })
        ->withCount(['items','campaigns'])->with(['discount'=>function($q){
            return $q->validate();
        }])->weekday()
        ->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
            $relationships = [
                'translations' => 'value',
                'items.nutritions' => 'nutrition',
                'items.allergies' => 'allergy',
                'items.generic' => 'generic_name',
                'items.ecommerce_item_details.brand' => 'name',
                'items.pharmacy_item_details.common_condition' => 'name'
            ];
            return  $q->applyRelationShipSearch(relationships:$relationships ,searchParameter:$key);
        })
            ->when(config('module.current_module_data'), function($query)use($zone_id){
                return   $query->module(config('module.current_module_data')['id']);
                if(!config('module.current_module_data')['all_zone_service']) {
                    return   $query->whereIn('zone_id', json_decode($zone_id, true));
                }
            })
            ->when($category_id, function($query)use($category_id){
                return $query->whereHas('items.category', function($q)use($category_id){
                    return $q->whereId($category_id)->orWhere('parent_id', $category_id);
                });
            })
            ->when($category_ids && is_array($category_ids), function($query)use($category_ids){
                return $query->whereHas('items.category', function($q)use($category_ids){
                    return $q->whereIn('id',$category_ids)->orWhereIn('parent_id', $category_ids);
                });
            })
            ->active()
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
                return  $qurey->whereNotNull('rating')->whereRaw("LENGTH(rating) > 0");
            })
            ->when($filter && in_array('discounted',$filter),function ($qurey){
                return  $qurey->where(function ($query) {
                    return $query->whereHas('items', function ($q) {
                        return  $q->Discounted();
                    });
                });
            })
            ->when($filter && in_array('free_delivery',$filter),function ($qurey){
                return $qurey->where('free_delivery',1);
            })
            ->when($filter && in_array('coupon',$filter),function ($qurey){
                return $qurey->has('activeCoupons');
            })
            ->when($filter && in_array('currently_open',$filter),function ($qurey){
                return $qurey->having('open', '>', 0);
            })
            ->orderBy('open', 'desc')
            ->when($filter && in_array('popular',$filter),function ($qurey){
                return $qurey->withCount('orders')->orderBy('orders_count', 'desc');
            })
            ->when(($filter && in_array('nearby',$filter))   ,function ($qurey){
                return  $qurey->orderBy('distance');
            })
            ->when($filter && in_array('fast_delivery',$filter),function ($qurey){
                return $qurey->orderBy('min_delivery_time');
            })
            ->type($type)->paginate($limit, ['*'], 'page', $offset);


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

    public static function get_overall_rating($reviews)
    {
        $totalRating = count($reviews);
        $rating = 0;
        foreach ($reviews as $key => $review) {
            $rating += $review->rating;
        }
        if ($totalRating == 0) {
            $overallRating = 0;
        } else {
            $overallRating = number_format($rating / $totalRating, 2);
        }

        return [$overallRating, $totalRating];
    }

    public static function get_earning_data($vendor_id)
    {
        $monthly_earning = OrderTransaction::whereMonth('created_at', date('m'))->NotRefunded()->where('vendor_id', $vendor_id)->sum('store_amount');
        $weekly_earning = OrderTransaction::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->NotRefunded()->where('vendor_id', $vendor_id)->sum('store_amount');
        $daily_earning = OrderTransaction::whereDate('created_at', now())->NotRefunded()->where('vendor_id', $vendor_id)->sum('store_amount');

        return['monthely_earning'=>(float)$monthly_earning, 'weekly_earning'=>(float)$weekly_earning, 'daily_earning'=>(float)$daily_earning];
    }

    public static function format_export_stores($stores)
    {
        $storage = [];
        foreach($stores as $item)
        {
            if($item->stores->count()<1)
            {
                break;
            }
            $storage[] = [
                'Id'=>$item->stores[0]->id,
                'OwnerId'=>$item->id,
                'OwnerFirstName'=>$item->f_name,
                'OwnerLastName'=>$item->l_name,
                'ProviderName'=>$item->stores[0]->name,
                'Phone'=>$item->phone,
                'Email'=>$item->email,
                'Logo'=>$item->stores[0]->logo,
                'CoverPhoto'=>$item->stores[0]->cover_photo,
                'Latitude'=>$item->stores[0]->latitude,
                'Longitude'=>$item->stores[0]->longitude,
                'Address'=>$item->stores[0]->address ?? null,
                'ZoneId'=>$item->stores[0]->zone_id,
                'ModuleId'=>$item->stores[0]->module_id,
                'Comission'=>$item->stores[0]->comission ?? 0,
                'Tax'=>$item->stores[0]->tax ?? 0,
                'PickupTime'=>$item->stores[0]->delivery_time ?? '20-30',
                'ScheduleTrip'=> $item->stores[0]->schedule_order == 1 ? 'yes' : 'no',
                'Status'=> $item->stores[0]->status == 1 ? 'active' : 'inactive',
                'ReviewsSection'=> $item->stores[0]->reviews_section == 1 ? 'active' : 'inactive',
                'storeOpen'=> $item->stores[0]->active == 1 ? 'yes' : 'no',
            ];
        }

        return $storage;
    }

    public static function insert_schedule(int $store_id, array $days=[0,1,2,3,4,5,6], String $opening_time='00:00:00', String $closing_time='23:59:59')
    {
        $data = array_map(function($item)use($store_id, $opening_time, $closing_time){
            return     ['store_id'=>$store_id,'day'=>$item,'opening_time'=>$opening_time,'closing_time'=>$closing_time];
        },$days);
        try{
            StoreSchedule::upsert($data,['store_id','day','opening_time','closing_time']);
            return true;
        }catch(Exception $e)
        {
            return $e;
        }
        return false;

    }

    public static function format_store_sales_export_data($items)
    {
        $data = [];
        foreach($items as $key=>$item)
        {

            $data[]=[
                '#'=>$key+1,
                translate('messages.name')=>$item->name,
                translate('messages.quantity')=>$item->orders->sum('quantity'),
                translate('messages.gross_sale')=>$item->orders->sum('price'),
                translate('messages.discount_given')=>$item->orders->sum('discount_on_item'),

            ];
        }
        return $data;
    }

    public static function format_store_summary_export_data($stores)
    {
        $data = [];
        foreach($stores as $key=>$store)
        {
            $delivered = $store->orders->where('order_status', 'delivered')->count();
            $canceled = $store->orders->where('order_status', 'canceled')->count();
            $refunded = $store->orders->where('order_status', 'refunded')->count();
            $total = $store->orders->count();
            $refund_requested = $store->orders->whereNotNull('refund_requested')->count();
            $data[]=[
                '#'=>$key+1,
                translate('Store')=>$store->name,
                translate('Total Order')=>$total,
                translate('Delivered Order')=>$delivered,
                translate('Total Amount')=>$store->orders->where('order_status','delivered')->sum('order_amount'),
                translate('Completion Rate')=>($store->orders->count() > 0 && $delivered > 0)? number_format((100*$delivered)/$store->orders->count(), config('round_up_to_digit')): 0,
                translate('Ongoing Rate')=>($store->orders->count() > 0 && $delivered > 0)? number_format((100*($store->orders->count()-($delivered+$canceled)))/$store->orders->count(), config('round_up_to_digit')): 0,
                translate('Cancelation Rate')=>($store->orders->count() > 0 && $canceled > 0)? number_format((100*$canceled)/$store->orders->count(), config('round_up_to_digit')): 0,
                translate('Refund Request')=>$refunded,

            ];
        }
        return $data;
    }

    public static function get_recommended_stores($zone_id, $limit = 50, $offset = 1, $type = 'all',$longitude=0,$latitude=0)
    {
        $recommended_store_default_status = \App\Models\BusinessSetting::where('key', 'recommended_store_default_status')->first();
        $recommended_store_default_status = $recommended_store_default_status ? $recommended_store_default_status->value : 1;
        $recommended_store_sort_by_general = \App\Models\PriorityList::where('name', 'recommended_store_sort_by_general')->where('type','general')->first();
        $recommended_store_sort_by_general = $recommended_store_sort_by_general ? $recommended_store_sort_by_general->value : '';
        $recommended_store_sort_by_unavailable = \App\Models\PriorityList::where('name', 'recommended_store_sort_by_unavailable')->where('type','unavailable')->first();
        $recommended_store_sort_by_unavailable = $recommended_store_sort_by_unavailable ? $recommended_store_sort_by_unavailable->value : '';
        $recommended_store_sort_by_temp_closed = \App\Models\PriorityList::where('name', 'recommended_store_sort_by_temp_closed')->where('type','temp_closed')->first();
        $recommended_store_sort_by_temp_closed = $recommended_store_sort_by_temp_closed ? $recommended_store_sort_by_temp_closed->value : '';
        $recommended_store_sort_by_rating = \App\Models\PriorityList::where('name', 'recommended_store_sort_by_rating')->where('type','rating')->first();
        $recommended_store_sort_by_rating = $recommended_store_sort_by_rating ? $recommended_store_sort_by_rating->value : '';

        $shuffle=null;
        if(config('module.current_module_data')){
            $shuffle= DataSetting::where(['key' => 'shuffle_recommended_store' , 'type' => config('module.current_module_data')['id']])?->first()?->value;
        }
        $query = Store::withOpen($longitude??0,$latitude??0)
            ->withCount(['items','campaigns'])
            ->wherehas('storeConfig', function ($q){
                $q->where(['is_recommended_deleted'=> 0 , 'is_recommended' => 1]);
            })
            ->when(config('module.current_module_data'), function($query)use($zone_id){
                $query->whereHas('zone.modules', function($query){
                    $query->where('modules.id', config('module.current_module_data')['id']);
                })->module(config('module.current_module_data')['id']);
                if(!config('module.current_module_data')['all_zone_service']) {
                    $query->whereIn('zone_id', json_decode($zone_id, true));
                }
            })
            ->type($type)
            ->when($shuffle == 1 , function($q){
                $q->inRandomOrder();
            })
            ->withCount('reviews')
            ->withCount('orders')->Active();

        if($recommended_store_default_status == '1') {

        }else{

            if($recommended_store_sort_by_temp_closed == 'remove'){
                $query = $query->where('active', '>', 0);
            }elseif($recommended_store_sort_by_temp_closed == 'last'){
                $query = $query->orderByDesc('active');
            }

            if($recommended_store_sort_by_unavailable == 'remove'){
                $query = $query->having('open', '>', 0);
            }elseif($recommended_store_sort_by_unavailable == 'last'){
                $query = $query->orderBy('open', 'desc');
            }

            if($recommended_store_sort_by_rating && ($recommended_store_sort_by_rating != 'none')){
                $rating_count = 0;
                if($recommended_store_sort_by_rating == 'four_plus'){
                    $rating_count = 4;
                }
                if($recommended_store_sort_by_rating == 'three_half_plus'){
                    $rating_count = 3.5;
                }
                if($recommended_store_sort_by_rating == 'three_plus'){
                    $rating_count = 3;
                }
                if($recommended_store_sort_by_rating == 'two_plus'){
                    $rating_count = 2;
                }

                $query = $query->selectSub(function ($query) use ($rating_count){
                    $query->selectRaw('AVG(reviews.rating)')
                        ->from('reviews')
                        ->join('items', 'items.id', '=', 'reviews.item_id')
                        ->whereColumn('items.store_id', 'stores.id')
                        ->groupBy('items.store_id')
                        ->havingRaw('AVG(reviews.rating) >= ?', [$rating_count]);
                }, 'avg_r')->having('avg_r', '>=', $rating_count);
            }

            if($recommended_store_sort_by_general == 'rating') {
                $query = $query->selectSub(function ($query) {
                    $query->selectRaw('AVG(reviews.rating)')
                        ->from('reviews')
                        ->join('items', 'items.id', '=', 'reviews.item_id')
                        ->whereColumn('items.store_id', 'stores.id')
                        ->groupBy('items.store_id');
                }, 'avg_rat')->orderBy('avg_rat', 'desc');
            }elseif($recommended_store_sort_by_general == 'review_count') {
                $query = $query->orderByDesc('reviews_count');
            }elseif($recommended_store_sort_by_general == 'order_count') {
                $query = $query->orderBy('orders_count', 'desc');
            }

        }
        $paginator = $query->paginate($limit??50, ['*'], 'page', $offset??1);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit??50,
            'offset' => $offset??1,
            'stores' => $paginator->items()
        ];
    }

    public static function get_top_offer_near_me($zone_id, $limit = 50, $offset = 1, $type = 'all',$longitude=0,$latitude=0 , $name = null ,$sort = null, $halal = null)
    {

        $top_offer_near_me_stores_default_status = BusinessSetting::where('key', 'top_offer_near_me_stores_default_status')->first()?->value ?? 1;
        $top_offer_near_me_stores_sort_by_general = PriorityList::where('name', 'top_offer_near_me_stores_sort_by_general')->where('type','general')->first()?->value ?? '';
        $top_offer_near_me_stores_sort_by_unavailable = PriorityList::where('name', 'top_offer_near_me_stores_sort_by_unavailable')->where('type','unavailable')->first()?->value ?? '';
        $top_offer_near_me_stores_sort_by_temp_closed = PriorityList::where('name', 'top_offer_near_me_stores_sort_by_temp_closed')->where('type','temp_closed')->first()?->value ?? '';



        $query = Store::withOpen($longitude??0,$latitude??0)
            ->withCount(['items','campaigns','reviews'])
            ->with('discount')
            ->whereHas('discount' , function($q){
                $q->validate();
            })
            ->when(config('module.current_module_data'), function($query)use($zone_id){
                $query->whereHas('zone.modules', function($query){
                    $query->where('modules.id', config('module.current_module_data')['id']);
                })->module(config('module.current_module_data')['id']);
                if(!config('module.current_module_data')['all_zone_service']) {
                    $query->whereIn('zone_id', json_decode($zone_id, true));
                }
            })
            ->type($type)->Active()->Halal($halal);
            if($name){
                $key = explode(' ', $name);
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                    $relationships = [
                        'translations' => 'value',
                        // 'items.nutritions' => 'nutrition',
                        // 'items.allergies' => 'allergy',
                        // 'items.generic' => 'generic_name',
                        // 'items.ecommerce_item_details.brand' => 'name',
                        // 'items.pharmacy_item_details.common_condition' => 'name'
                    ];
                    return  $q->applyRelationShipSearch(relationships:$relationships ,searchParameter:$key);
                }) ->orderByRaw("CASE WHEN name = ? THEN 1 WHEN name LIKE ? THEN 2 ELSE 3 END, LENGTH(name) ASC, name ASC ", [$name, "%{$name}%"]);
            }

            if($sort)
            {
                $query->orderBy('name',$sort);

            } else{
                if($top_offer_near_me_stores_default_status== 1){
                    $query= $query->orderByDesc('open')->orderby('distance');
                }else{

                    if($top_offer_near_me_stores_sort_by_temp_closed == 'remove'){
                        $query = $query->where('active', '>', 0);
                    }elseif($top_offer_near_me_stores_sort_by_temp_closed == 'last'){
                        $query = $query->orderByDesc('active');
                    }

                    if($top_offer_near_me_stores_sort_by_unavailable == 'remove'){
                        $query = $query->having('open', '>', 0);
                    }elseif($top_offer_near_me_stores_sort_by_unavailable == 'last'){
                        $query = $query->orderBy('open', 'desc');
                    }

                    if($top_offer_near_me_stores_sort_by_general == 'rating') {
                        $query = $query->selectSub(function ($query) {
                            $query->selectRaw('AVG(reviews.rating)')
                                ->from('reviews')
                                ->join('items', 'items.id', '=', 'reviews.item_id')
                                ->whereColumn('items.store_id', 'stores.id')
                                ->groupBy('items.store_id');
                        }, 'avg_rat')->orderBy('avg_rat', 'desc');
                    }elseif($top_offer_near_me_stores_sort_by_general == 'review_count') {
                        $query = $query->orderByDesc('reviews_count');
                    }elseif($top_offer_near_me_stores_sort_by_general == 'asc_discount') {


                        $query = $query->selectSub(function ($query) {
                            $query->selectRaw('MAX(discounts.discount)')
                                ->from('discounts')
                                ->whereColumn('discounts.store_id', 'stores.id');
                        }, 'discount')
                        ->orderBy('discount', 'asc');

                    }elseif($top_offer_near_me_stores_sort_by_general == 'desc_discount') {
                        $query = $query->selectSub(function ($query) {
                            $query->selectRaw('MAX(discounts.discount)')
                                ->from('discounts')
                                ->whereColumn('discounts.store_id', 'stores.id');
                        }, 'discount')
                        ->orderBy('discount', 'desc');
                    }
                }
            }

        $paginator= $query->paginate($limit??50, ['*'], 'page', $offset??1);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit??50,
            'offset' => $offset??1,
            'stores' => $paginator->items()
        ];
    }
}
