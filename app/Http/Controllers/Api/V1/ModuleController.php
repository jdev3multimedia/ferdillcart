<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ZoneModuleException;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Zone;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->hasHeader('zoneId') || empty($request->header('zoneId'))) {
            $zone = Zone::where('status', 1)->where('is_default', 1)->first() ?? Zone::first();

            if (! $zone) {
                throw new ZoneModuleException(translate('No zone is available'));
            }
            $request->headers->set('zoneId', json_encode([$zone->id]));
        }
        if ($request->hasHeader('zoneId')) {
            $zone_id = json_decode($request->header('zoneId'), true);

            $zone_id = is_array($zone_id) ? $zone_id : [$zone_id];

            $modules = Module::with('zones')
                ->withCount([
                    'items',
                    'stores' => function ($query) use ($zone_id) {
                        $query->whereIn('zone_id', $zone_id);
                    },
                ])
                ->whereHas('zones', function ($query) use ($zone_id) {
                    $query->whereIn('zone_id', $zone_id);
                })
                ->active()
                ->get();
        } else {
            $modules = Module::withCount([
                'items',
                'stores' => function ($query) use ($request) {
                    $query->when($request->zone_id, function ($q) use ($request) {
                        $q->where('zone_id', $request->zone_id);
                    });
                },
            ])
                ->when($request->zone_id, function ($query) use ($request) {
                    $query->whereHas('zones', function ($query) use ($request) {
                        $query->where('zone_id', $request->zone_id);
                    })->notParcel();
                })
                ->active()
                ->get();
        }

        $modules = array_map(function ($item) {
            return $item;
        }, $modules->toArray());

        return response()->json($modules);
    }
}
