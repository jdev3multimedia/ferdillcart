<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SurgePrice;
use App\Models\Zone;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;
use DatePeriod;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SurgePriceController extends Controller
{
    public function index(Request $request, $zone_id)
    {
        $zone = Zone::findOrFail($zone_id);
        $key = explode(' ', $request['search']);
        $surges = SurgePrice::where('zone_id', $zone_id)->when(isset($key), function($query) use($key) {
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('surge_price_name', 'like', "%{$value}%");
                }
            });
        })
        ->paginate(config('default_pagination'));
        return view('admin-views.zone.surge-setup', compact('zone', 'surges'));
    }

    public function create($zone_id)
    {
        $zone = Zone::findOrFail($zone_id);
        $language = getWebConfig('language');
        return view('admin-views.zone.surge-price.index', compact('zone', 'language'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'surge_price_name' => 'required|array',
            'surge_price_name.0' => 'required',
            'customer_note' => 'nullable|array',
            'module_ids' => 'required|array',
            'price' => [
                'required',
                'numeric',
                'min:0',
                Rule::when($request->price_type === 'percent', ['max:100']),
            ],
            'price_type' => 'required|in:percent,amount',
            'duration_type' => 'required|in:daily,weekly,custom',
            'daily_date_range' => 'required_if:duration_type,daily',
            'daily_time_range' => 'required_if:duration_type,daily',
            'weekly_date_range' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->duration_type === 'weekly' && $request->is_permanent != 1;
                })
            ],
            'weekly_time_range' => 'required_if:duration_type,weekly',
            'custom_days' => 'required_if:duration_type,custom',
            'custom_times' => 'required_if:duration_type,custom',
        ], [
            'surge_price_name.0.required' => translate('messages.default_surge_price_name_required'),
            'module_ids.required' => translate('messages.modules_required'),
            'daily_date_range.required_if' => translate('messages.daily_date_range_required'),
            'daily_time_range.required_if' => translate('messages.daily_time_range_required'),
            'weekly_date_range.required_if' => translate('messages.weekly_date_range_required'),
            'weekly_time_range.required_if' => translate('messages.weekly_time_range_required'),
            'custom_days.required_if' => translate('messages.custom_days_required'),
            'custom_times.required_if' => translate('messages.custom_times_required'),
            'price.max' => translate('When price type is percent, the price increase rate cannot be more than 100.'),
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $surge = new SurgePrice();
            $surge->surge_price_name = $request->surge_price_name[array_search('default', $request->lang)];
            $surge->customer_note = $request->customer_note[array_search('default', $request->lang)] ?? null;
            $surge->customer_note_status = $request->has('customer_note_status') ? 1 : 0;
            $surge->module_ids = $request->module_ids;
            $surge->zone_id = $request->zone_id;
            $surge->duration_type = $request->duration_type;
            $surge->price = $request->price;
            $surge->price_type = $request->price_type;

            if ($surge->duration_type === 'daily') {
                [$startDate, $endDate] = explode(' - ', $request->daily_date_range);
                [$startTime, $endTime] = explode(' - ', $request->daily_time_range);
                $surge->start_date = date('Y-m-d', strtotime($startDate));
                $surge->end_date = date('Y-m-d', strtotime($endDate));
                $surge->start_time = date('H:i:s', strtotime($startTime));
                $surge->end_time = date('H:i:s', strtotime($endTime));
            } elseif ($surge->duration_type === 'weekly') {
                [$startTime, $endTime] = explode(' - ', $request->weekly_time_range);
                $surge->start_time = date('H:i:s', strtotime($startTime));
                $surge->end_time = date('H:i:s', strtotime($endTime));
                $surge->weekly_days = $request->weekly_days ? explode(',', $request->weekly_days) : [];
                $surge->is_permanent = $request->is_permanent ?? 0;
                if($surge->is_permanent) {
                    $surge->start_date = null;
                    $surge->end_date = null;
                }else{
                    [$startDate, $endDate] = explode(' - ', $request->weekly_date_range);
                    $surge->start_date = date('Y-m-d', strtotime($startDate));
                    $surge->end_date = date('Y-m-d', strtotime($endDate));
                }
            } elseif ($surge->duration_type === 'custom') {
                $surge->custom_days = explode(',', $request->custom_days);
                $surge->custom_times = explode(',', $request->custom_times);
            }

            $surge->save();

            Helpers::add_or_update_translations($request, 'surge_price_name', 'surge_price_name', 'SurgePrice', $surge->id, $surge->surge_price_name);
            Helpers::add_or_update_translations($request, 'customer_note', 'customer_note', 'SurgePrice', $surge->id, $surge->customer_note);

            $this->insertSurgePriceDates($surge, $request->module_ids);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => translate('messages.surge_price_created_successfully'),
                ]);
            }

            Toastr::success(translate('messages.surge_price_created_successfully'));
            return back();
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                $message = $e->getMessage();

                if (str_contains(strtolower($message), 'time conflict')) {
                    return response()->json([
                        'status' => false,
                        'type' => 'conflict',
                        'message' => $message,
                    ], 409);
                }

                // Default error fallback
                return response()->json([
                    'status' => false,
                    'message' => 'Server Error: ' . $message,
                ], 500);
            }

            Toastr::error($e->getMessage());
            return back()->withInput();
        }
    }

    public function edit($id)
    {
        $surge = SurgePrice::withOutGlobalScopes(['translate'])->findOrFail($id);
        $language = getWebConfig('language');
        return view('admin-views.zone.surge-price.edit', compact('surge', 'language'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'surge_price_name' => 'required|array',
            'surge_price_name.0' => 'required',
            'customer_note' => 'nullable|array',
            'module_ids' => 'required|array',
            'price' => [
                'required',
                'numeric',
                'min:0',
                Rule::when($request->price_type === 'percent', ['max:100']),
            ],
            'price_type' => 'required|in:percent,amount',
            'duration_type' => 'required|in:daily,weekly,custom',
            'daily_date_range' => 'required_if:duration_type,daily',
            'daily_time_range' => 'required_if:duration_type,daily',
            'weekly_date_range' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->duration_type === 'weekly' && $request->is_permanent != 1;
                })
            ],
            'weekly_time_range' => 'required_if:duration_type,weekly',
            'custom_days' => 'required_if:duration_type,custom',
            'custom_times' => 'required_if:duration_type,custom',
        ], [
            'surge_price_name.0.required' => translate('messages.default_surge_price_name_required'),
            'module_ids.required' => translate('messages.modules_required'),
            'daily_date_range.required_if' => translate('messages.daily_date_range_required'),
            'daily_time_range.required_if' => translate('messages.daily_time_range_required'),
            'weekly_date_range.required_if' => translate('messages.weekly_date_range_required'),
            'weekly_time_range.required_if' => translate('messages.weekly_time_range_required'),
            'custom_days.required_if' => translate('messages.custom_days_required'),
            'custom_times.required_if' => translate('messages.custom_times_required'),
            'price.max' => translate('When price type is percent, the price increase rate cannot be more than 100.'),
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $surge = SurgePrice::findOrFail($id);
            $surge->surge_price_name = $request->surge_price_name[array_search('default', $request->lang)];
            $surge->customer_note = $request->customer_note[array_search('default', $request->lang)] ?? null;
            $surge->customer_note_status = $request->has('customer_note_status') ? 1 : 0;
            $surge->module_ids = $request->module_ids;
            $surge->duration_type = $request->duration_type;
            $surge->price = $request->price;
            $surge->price_type = $request->price_type;

            if ($surge->duration_type === 'daily') {
                [$startDate, $endDate] = explode(' - ', $request->daily_date_range);
                [$startTime, $endTime] = explode(' - ', $request->daily_time_range);
                $surge->start_date = date('Y-m-d', strtotime($startDate));
                $surge->end_date = date('Y-m-d', strtotime($endDate));
                $surge->start_time = date('H:i:s', strtotime($startTime));
                $surge->end_time = date('H:i:s', strtotime($endTime));
                $surge->weekly_days = null;
                $surge->custom_days = null;
                $surge->custom_times = null;
            } elseif ($surge->duration_type === 'weekly') {
                [$startTime, $endTime] = explode(' - ', $request->weekly_time_range);
                $surge->start_time = date('H:i:s', strtotime($startTime));
                $surge->end_time = date('H:i:s', strtotime($endTime));
                $surge->weekly_days = $request->weekly_days ? explode(',', $request->weekly_days) : [];
                $surge->is_permanent = $request->is_permanent ?? 0;
                if($surge->is_permanent) {
                    $surge->start_date = null;
                    $surge->end_date = null;
                }else{
                    [$startDate, $endDate] = explode(' - ', $request->weekly_date_range);
                    $surge->start_date = date('Y-m-d', strtotime($startDate));
                    $surge->end_date = date('Y-m-d', strtotime($endDate));
                }
                $surge->custom_days = null;
                $surge->custom_times = null;
            } elseif ($surge->duration_type === 'custom') {
                $surge->custom_days = explode(',', $request->custom_days);
                $surge->custom_times = explode(',', $request->custom_times);
                $surge->start_date = null;
                $surge->end_date = null;
                $surge->start_time = null;
                $surge->end_time = null;
                $surge->weekly_days = null;
            }

            $surge->save();
            $surge->details()->delete();

            Helpers::add_or_update_translations($request, 'surge_price_name', 'surge_price_name', 'SurgePrice', $surge->id, $surge->surge_price_name);
            Helpers::add_or_update_translations($request, 'customer_note', 'customer_note', 'SurgePrice', $surge->id, $surge->customer_note);

            $this->insertSurgePriceDates($surge, $request->module_ids);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => translate('messages.surge_price_updated_successfully'),
                ]);
            }

            Toastr::success(translate('messages.surge_price_updated_successfully'));
            return back();
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                $message = $e->getMessage();

                if (str_contains(strtolower($message), 'time conflict')) {
                    return response()->json([
                        'status' => false,
                        'type' => 'conflict',
                        'message' => $message,
                    ], 409);
                }

                return response()->json([
                    'status' => false,
                    'message' => 'Server Error: ' . $message,
                ], 500);
            }

            Toastr::error($e->getMessage());
            return back()->withInput();
        }
    }

    private function insertSurgePriceDates($surge, $moduleIds)
    {
        foreach ($moduleIds as $moduleId) {
            if ($surge->duration_type === 'daily') {
                $startDate = $surge->start_date;
                $endDate   = $surge->end_date;
                $startTime = $surge->start_time;
                $endTime   = $surge->end_time;

                $period = new DatePeriod(
                    new DateTime($startDate),
                    new DateInterval('P1D'),
                    (new DateTime($endDate))->modify('+1 day')
                );

                foreach ($period as $date) {
                    $dateStr = $date->format('Y-m-d');
                    $dayName = $date->format('l');

                    // Check if this date falls under a permanent weekly surge
                    if ($this->isBlockedByPermanentWeekly($surge->zone_id, $moduleId, $dayName, $startTime, $endTime)) {
                        abort(409, "Time conflict: Permanent weekly surge already exists on {$dayName} for module ID {$moduleId}");
                    }

                    $conflict = DB::table('surge_price_dates')
                        ->where('zone_id', $surge->zone_id)
                        ->where('module_id', $moduleId)
                        ->where('applicable_date', $dateStr)
                        ->where(function ($query) use ($startTime, $endTime) {
                            $query->whereBetween('start_time', [$startTime, $endTime])
                                ->orWhereBetween('end_time', [$startTime, $endTime])
                                ->orWhere(function ($q) use ($startTime, $endTime) {
                                    $q->where('start_time', '<=', $startTime)
                                        ->where('end_time', '>=', $endTime);
                                });
                        })->exists();

                    if ($conflict) {
                        abort(409, "Time conflict on {$dateStr} for module ID {$moduleId}");
                    }

                    DB::table('surge_price_dates')->insert([
                        'surge_price_id'  => $surge->id,
                        'zone_id'         => $surge->zone_id,
                        'module_id'       => $moduleId,
                        'applicable_date' => $dateStr,
                        'start_time'      => $startTime,
                        'end_time'        => $endTime,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }
            }

            elseif ($surge->duration_type === 'weekly') {
                $startTime = $surge->start_time;
                $endTime   = $surge->end_time;
                $weekdays  = $surge->weekly_days;
                $startDate = $surge->start_date;
                $endDate   = $surge->end_date;

                if ($surge->is_permanent) {
                    // Only check for conflicts, do not insert dates
                    foreach ($weekdays as $weekday) {
                        $conflict = DB::table('surge_prices')
                            ->where('zone_id', $surge->zone_id)
                            ->whereJsonContains('module_ids', $moduleId)
                            ->whereJsonContains('weekly_days', $weekday)
                            ->where(function ($query) use ($startTime, $endTime) {
                                $query->whereBetween('start_time', [$startTime, $endTime])
                                    ->orWhereBetween('end_time', [$startTime, $endTime])
                                    ->orWhere(function ($q) use ($startTime, $endTime) {
                                        $q->where('start_time', '<=', $startTime)
                                            ->where('end_time', '>=', $endTime);
                                    });
                            })
                            ->where('id', '!=', $surge->id)
                            ->exists();

                        if ($conflict) {
                            abort(409, "Time conflict: Permanent surge already exists for {$weekday} for module ID {$moduleId}");
                        }
                    }

                    $period = new DatePeriod(
                        new DateTime($startDate),
                        new DateInterval('P1D'),
                        (new DateTime($endDate))->modify('+1 day')
                    );

                    foreach ($period as $date) {
                        $dateStr = $date->format('Y-m-d');
                        $dayName = $date->format('l');

                        if (!in_array($dayName, $weekdays)) continue;

                        $conflict = DB::table('surge_price_dates')
                            ->where('zone_id', $surge->zone_id)
                            ->where('module_id', $moduleId)
                            ->where('applicable_date', $dateStr)
                            ->where(function ($query) use ($startTime, $endTime) {
                                $query->whereBetween('start_time', [$startTime, $endTime])
                                    ->orWhereBetween('end_time', [$startTime, $endTime])
                                    ->orWhere(function ($q) use ($startTime, $endTime) {
                                        $q->where('start_time', '<=', $startTime)
                                            ->where('end_time', '>=', $endTime);
                                    });
                            })->exists();

                        if ($conflict) {
                            abort(409, "Time conflict on {$dateStr} for module ID {$moduleId}");
                        }
                    }

                    // Nothing to insert
                    continue;
                }

                $startDate = $surge->start_date;
                $endDate   = $surge->end_date;

                $period = new DatePeriod(
                    new DateTime($startDate),
                    new DateInterval('P1D'),
                    (new DateTime($endDate))->modify('+1 day')
                );

                foreach ($period as $date) {
                    $dateStr = $date->format('Y-m-d');
                    $dayName = $date->format('l');

                    if (!in_array($dayName, $weekdays)) continue;

                    // Conflict with permanent weekly surge
                    if ($this->isBlockedByPermanentWeekly($surge->zone_id, $moduleId, $dayName, $startTime, $endTime)) {
                        abort(409, "Time conflict: Permanent weekly surge already exists on {$dayName} for module ID {$moduleId}");
                    }

                    $conflict = DB::table('surge_price_dates')
                        ->where('zone_id', $surge->zone_id)
                        ->where('module_id', $moduleId)
                        ->where('applicable_date', $dateStr)
                        ->where(function ($query) use ($startTime, $endTime) {
                            $query->whereBetween('start_time', [$startTime, $endTime])
                                ->orWhereBetween('end_time', [$startTime, $endTime])
                                ->orWhere(function ($q) use ($startTime, $endTime) {
                                    $q->where('start_time', '<=', $startTime)
                                        ->where('end_time', '>=', $endTime);
                                });
                        })->exists();

                    if ($conflict) {
                        abort(409, "Time conflict on {$dateStr} for module ID {$moduleId}");
                    }

                    DB::table('surge_price_dates')->insert([
                        'surge_price_id'  => $surge->id,
                        'zone_id'         => $surge->zone_id,
                        'module_id'       => $moduleId,
                        'applicable_date' => $dateStr,
                        'start_time'      => $startTime,
                        'end_time'        => $endTime,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }
            }

            elseif ($surge->duration_type === 'custom') {
                $customDays  = $surge->custom_days ?? [];
                $customTimes = $surge->custom_times ?? [];

                foreach ($customDays as $i => $day) {
                    $dateStr = date('Y-m-d', strtotime($day));
                    $dayName = date('l', strtotime($day));
                    [$startTime, $endTime] = explode(' - ', $customTimes[$i]);

                    $startTime = date('H:i:s', strtotime($startTime));
                    $endTime   = date('H:i:s', strtotime($endTime));

                    // Check against permanent weekly
                    if ($this->isBlockedByPermanentWeekly($surge->zone_id, $moduleId, $dayName, $startTime, $endTime)) {
                        abort(409, "Time conflict: Permanent weekly surge already exists on {$dayName} for module ID {$moduleId}");
                    }

                    $conflict = DB::table('surge_price_dates')
                        ->where('zone_id', $surge->zone_id)
                        ->where('module_id', $moduleId)
                        ->where('applicable_date', $dateStr)
                        ->where(function ($query) use ($startTime, $endTime) {
                            $query->whereBetween('start_time', [$startTime, $endTime])
                                ->orWhereBetween('end_time', [$startTime, $endTime])
                                ->orWhere(function ($q) use ($startTime, $endTime) {
                                    $q->where('start_time', '<=', $startTime)
                                        ->where('end_time', '>=', $endTime);
                                });
                        })->exists();

                    if ($conflict) {
                        abort(409, "Time conflict on {$dateStr} for module ID {$moduleId}");
                    }

                    DB::table('surge_price_dates')->insert([
                        'surge_price_id'  => $surge->id,
                        'zone_id'         => $surge->zone_id,
                        'module_id'       => $moduleId,
                        'applicable_date' => $dateStr,
                        'start_time'      => $startTime,
                        'end_time'        => $endTime,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }
            }
        }
    }

    private function isBlockedByPermanentWeekly($zoneId, $moduleId, $weekday, $startTime, $endTime)
    {
        return DB::table('surge_prices')
            ->where('zone_id', $zoneId)
            ->whereJsonContains('module_ids', $moduleId)
            ->where('duration_type', 'weekly')
            ->where('is_permanent', 1)
            ->whereJsonContains('weekly_days', $weekday)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->exists();
    }


    public function status(Request $request)
    {
        $surge = SurgePrice::findOrFail($request->id);
        $surge->status = $request->status;
        $surge->save();
        Toastr::success(translate('messages.surge_price_status_updated'));
        return back();
    }

    public function destroy(Request $request)
    {
        $surge = SurgePrice::findOrFail($request->id);
        $surge->details()->delete();
        $surge->translations()->delete();
        $surge->delete();
        Toastr::success(translate('messages.surge_price_deleted_successfully'));
        return back();
    }


}
