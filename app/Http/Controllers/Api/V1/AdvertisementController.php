<?php
namespace App\Http\Controllers\Api\V1;

use App\Models\Advertisement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdvertisementController extends Controller
{
    public function get_adds(Request $request)
    {
        $zone_ids= $request->header('zoneId');
        $zone_ids=  json_decode($zone_ids, true)?? [];

        $cacheKey = 'advertisement_' . md5(implode('_', [
                json_encode($zone_ids),
                config('module.current_module_data')['id'] ?? 'default'
            ]));

        $Advertisement = Cache::remember($cacheKey, now()->addMinutes(20), function () use ($zone_ids) {
            $Advertisement = Advertisement::valid()
                ->when(config('module.current_module_data'), function($query) {
                    $query->where('module_id', config('module.current_module_data')['id']);
                })
                ->with('store')
                ->whereHas('store', function ($query) use ($zone_ids) {
                    if (!empty($zone_ids)) {
                        $query->whereIn('zone_id', $zone_ids);
                    }
                    $query->active();
                })
                ->orderByRaw('ISNULL(priority), priority ASC')
                ->get();

            try {
                $Advertisement->each(function ($advertisement) {
                    $advertisement->reviews_comments_count = (int) $advertisement?->store?->reviews_comments()->count();
                    $reviewsInfo = $advertisement?->store?->reviews()
                        ->selectRaw('avg(reviews.rating) as average_rating, count(reviews.id) as total_reviews, items.store_id')
                        ->groupBy('items.store_id')
                        ->first();

                    $advertisement->average_rating = (float)  $reviewsInfo?->average_rating ?? 0;
                });
            } catch (\Exception $e) {
                info($e->getMessage());
            }

            return $Advertisement;
        });

        return response()->json($Advertisement, 200);
    }

}
