<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Scopes\ZoneScope;
use App\Models\DeliveryMan;
use App\Models\Translation;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Exports\ParcelCancellationReasonExport;
use App\Models\BusinessSetting;
use App\Exports\ParcelOrderExport;
use App\Http\Controllers\Controller;
use App\Models\ParcelCancellationReason;
use Brian2694\Toastr\Facades\Toastr;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Config;
use App\Models\ParcelDeliveryInstruction;


class ParcelController extends Controller
{
    public function orders(Request $request, $status)
    {

        $key = isset($request->search) ? explode(' ', $request->search) : null;
        if (session()->has('zone_filter') == false) {
            session()->put('zone_filter', 0);
        }

        if (session()->has('order_filter')) {
            $request = json_decode(session('order_filter'));
        }
        Order::withOutGlobalScope(ZoneScope::class)->where(['checked' => 0, 'order_type' => 'parcel'])->update(['checked' => 1]);

        $orders = Order::withOutGlobalScope(ZoneScope::class)->with(['customer', 'store'])
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%")
                            ->orWhere('order_status', 'like', "%{$value}%")
                            ->orWhere('transaction_reference', 'like', "%{$value}%");
                    }
                });
            })
            ->when(isset($request->zone), function ($query) use ($request) {
                return $query->whereIn('zone_id', $request->zone);
            })
            ->when($status == 'scheduled', function ($query) {
                return $query->whereRaw('created_at <> schedule_at');
            })
            ->when($status == 'searching_for_deliverymen', function ($query) {
                return $query->SearchingForDeliveryman();
            })
            ->when($status == 'pending', function ($query) {
                return $query->Pending();
            })
            ->when($status == 'accepted', function ($query) {
                return $query->AccepteByDeliveryman();
            })
            ->when($status == 'processing', function ($query) {
                return $query->Preparing();
            })
            ->when($status == 'item_on_the_way', function ($query) {
                return $query->ItemOnTheWay();
            })
            ->when($status == 'delivered', function ($query) {
                return $query->Delivered();
            })
            ->when($status == 'canceled', function ($query) {
                return $query->Canceled();
            })
            ->when($status == 'failed', function ($query) {
                return $query->failed();
            })
            ->when($status == 'refunded', function ($query) {
                return $query->Refunded();
            })
            ->when($status == 'scheduled', function ($query) {
                return $query->Scheduled();
            })
            ->when($status == 'on_going', function ($query) {
                return $query->Ongoing();
            })
            ->when(($status != 'all' && $status != 'scheduled' && $status != 'canceled' && $status != 'refund_requested' && $status != 'refunded' && $status != 'delivered' && $status != 'failed'), function ($query) {
                return $query->OrderScheduledIn(30);
            })
            ->when(isset($request->vendor), function ($query) use ($request) {
                return $query->whereHas('store', function ($query) use ($request) {
                    return $query->whereIn('id', $request->vendor);
                });
            })
            ->when(isset($request->orderStatus) && $status == 'all', function ($query) use ($request) {
                return $query->whereIn('order_status', $request->orderStatus);
            })
            ->when(isset($request->scheduled) && $status == 'all', function ($query) {
                return $query->scheduled();
            })
            ->when(isset($request->order_type), function ($query) use ($request) {
                return $query->where('order_type', $request->order_type);
            })
            ->when(isset($request->from_date) && isset($request->to_date) && $request->from_date != null && $request->to_date != null, function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->from_date . " 00:00:00", $request->to_date . " 23:59:59"]);
            })
            ->when(isset($request->payment_status) && $request->payment_status == 'paid', function ($query) {
                return $query->where('payment_status', 'paid');
            })
            ->when(isset($request->payment_status) && $request->payment_status == 'unpaid', function ($query) {
                return $query->where('payment_status', 'unpaid');
            })
            ->when(isset($request->payment_by) && $request->payment_by == 'sender', function ($query) {
                return $query->where('charge_payer', 'sender');
            })
            ->when(isset($request->payment_by) && $request->payment_by == 'receiver', function ($query) {
                return $query->where('charge_payer', 'receiver');
            })
            ->with('parcel_category')
            ->ParcelOrder()
            ->module(Config::get('module.current_module_id'))
            ->orderBy('schedule_at', 'desc')
            ->paginate(config('default_pagination'));

        $orderstatus = isset($request->orderStatus) ? $request->orderStatus : [];
        $scheduled = isset($request->scheduled) ? $request->scheduled : 0;
        $vendor_ids = isset($request->vendor) ? $request->vendor : [];
        $zone_ids = isset($request->zone) ? $request->zone : [];
        $from_date = isset($request->from_date) ? $request->from_date : null;
        $to_date = isset($request->to_date) ? $request->to_date : null;
        $order_type = isset($request->order_type) ? $request->order_type : null;
        $payment_status = isset($request->payment_status) ? $request->payment_status : null;
        $payment_by = isset($request->payment_by) ? $request->payment_by : null;
        $total = $orders->total();

        return view('admin-views.order.parcel-list', compact('orders', 'status', 'orderstatus', 'scheduled', 'vendor_ids', 'zone_ids', 'from_date', 'to_date', 'total', 'payment_by', 'payment_status', 'order_type'));
    }



    public function parcel_orders_export(Request $request, $status, $file_type)
    {

        $key = isset($request->search) ? explode(' ', $request->search) : ($request['amp;search'] ? explode(' ', $request['amp;search']) : null);
        if (session()->has('zone_filter') == false) {
            session()->put('zone_filter', 0);
        }

        if (session()->has('order_filter')) {
            $request = json_decode(session('order_filter'));
        }

        Order::withOutGlobalScope(ZoneScope::class)->where(['checked' => 0, 'order_type' => 'parcel'])->update(['checked' => 1]);

        $orders = Order::withOutGlobalScope(ZoneScope::class)->with(['customer', 'store'])
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%")
                            ->orWhere('order_status', 'like', "%{$value}%")
                            ->orWhere('transaction_reference', 'like', "%{$value}%");
                    }
                });
            })
            ->when(isset($request->zone), function ($query) use ($request) {
                return $query->where('zone_id', $request->zone);
            })
            ->when($status == 'scheduled', function ($query) {
                return $query->whereRaw('created_at <> schedule_at');
            })
            ->when($status == 'searching_for_deliverymen', function ($query) {
                return $query->SearchingForDeliveryman();
            })
            ->when($status == 'pending', function ($query) {
                return $query->Pending();
            })
            ->when($status == 'accepted', function ($query) {
                return $query->AccepteByDeliveryman();
            })
            ->when($status == 'processing', function ($query) {
                return $query->Preparing();
            })
            ->when($status == 'item_on_the_way', function ($query) {
                return $query->ItemOnTheWay();
            })
            ->when($status == 'delivered', function ($query) {
                return $query->Delivered();
            })
            ->when($status == 'canceled', function ($query) {
                return $query->Canceled();
            })
            ->when($status == 'failed', function ($query) {
                return $query->failed();
            })
            ->when($status == 'refunded', function ($query) {
                return $query->Refunded();
            })
            ->when($status == 'scheduled', function ($query) {
                return $query->Scheduled();
            })
            ->when($status == 'on_going', function ($query) {
                return $query->Ongoing();
            })
            ->when(($status != 'all' && $status != 'scheduled' && $status != 'canceled' && $status != 'refund_requested' && $status != 'refunded' && $status != 'delivered' && $status != 'failed'), function ($query) {
                return $query->OrderScheduledIn(30);
            })
            ->when(isset($request->vendor), function ($query) use ($request) {
                return $query->whereHas('store', function ($query) use ($request) {
                    return $query->whereIn('id', $request->vendor);
                });
            })
            ->when(isset($request->orderStatus) && $status == 'all', function ($query) use ($request) {
                return $query->whereIn('order_status', $request->orderStatus);
            })
            ->when(isset($request->scheduled) && $status == 'all', function ($query) {
                return $query->scheduled();
            })
            ->when(isset($request->order_type), function ($query) use ($request) {
                return $query->where('order_type', $request->order_type);
            })
            ->when(isset($request->from_date) && isset($request->to_date) && $request->from_date != null && $request->to_date != null, function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->from_date . " 00:00:00", $request->to_date . " 23:59:59"]);
            })
            ->when(isset($request->payment_status) && $request->payment_status == 'paid', function ($query) {
                return $query->where('payment_status', 'paid');
            })
            ->when(isset($request->payment_status) && $request->payment_status == 'unpaid', function ($query) {
                return $query->where('payment_status', 'unpaid');
            })
            ->when(isset($request->payment_by) && $request->payment_by == 'sender', function ($query) {
                return $query->where('charge_payer', 'sender');
            })
            ->when(isset($request->payment_by) && $request->payment_by == 'receiver', function ($query) {
                return $query->where('charge_payer', 'receiver');
            })

            ->ParcelOrder()
            ->module(Config::get('module.current_module_id'))
            ->orderBy('schedule_at', 'desc')
            ->get();



        $data = [
            'orders' => $orders,
            'type' => 'parcel',
            'status' => $status,
            'order_status' => isset($request->orderStatus) ? implode(', ', $request->orderStatus) : null,
            'search' => $request->search ?? null,
            'from' => $request->from_date ?? null,
            'to' => $request->to_date ?? null,
            'zones' => isset($request->zone) ? Helpers::get_zones_name($request->zone) : null,
        ];

        if ($file_type == 'excel') {
            return Excel::download(new ParcelOrderExport($data), 'ParcelOrders.xlsx');
        }
        return Excel::download(new ParcelOrderExport($data), 'ParcelOrders.csv');
    }

    public function order_details(Request $request, $id)
    {
        $order = Order::withOutGlobalScope(ZoneScope::class)->with(['customer' => function ($query) {
            return $query->withCount('orders');
        }, 'delivery_man' => function ($query) {
            return $query->withCount('orders');
        },'parcelCancellation'])->where(['id' => $id])->ParcelOrder()->first();
        if (isset($order)) {

            $isUnpaid = false;

            if (
                in_array($order->order_status, ['pending','failed']) &&
                !in_array($order->payment_method, ['cash_on_delivery', 'wallet'])
            ) {
                // CASE 1: partial payment
                if ($order->payment_method == 'partial_payment') {
                    if ($order->payment_method === 'partial_payment') {
                        $isUnpaid = $order->payments()
                            ->where('payment_status', 'unpaid')
                            ->whereNotIn('payment_method', ['cash_on_delivery', 'wallet'])
                            ->exists();
                    }

                }

                // CASE 2: offline payment
                elseif ($order->payment_method == 'offline_payment') {
                    if ($order?->offline_payments?->count() == 0) {
                        $isUnpaid = true;
                    }
                }

                else {
                    $isUnpaid = true;
                }
            }

            $order->is_unpaid_order = $isUnpaid ? true : false;



            $deliveryMen = DeliveryMan::withOutGlobalScope(ZoneScope::class)->where('zone_id', $order->zone_id)->where(function ($query) use ($order) {
                $query->where('vehicle_id', $order->dm_vehicle_id)->orWhereNull('vehicle_id');
            })->available()->active()->get();
            $category = $request->query('category_id', 0);
            $categories = [];
            $products = [];
            $editing = false;
            $deliveryMen = Helpers::deliverymen_list_formatting($deliveryMen);
            $keyword = null;

            return view('admin-views.order.parcel-order-view', compact('order', 'deliveryMen', 'categories', 'products', 'category', 'keyword', 'editing'));
        } else {
            Toastr::info(translate('messages.no_more_orders'));
            return back();
        }
    }

    public function settings()
    {
        $instructions = ParcelDeliveryInstruction::orderBy('id', 'desc')
            ->paginate(config('default_pagination'));

        $settings = BusinessSetting::whereIn('key', [
            'parcel_per_km_shipping_charge',
            'parcel_minimum_shipping_charge',
            'parcel_commission_dm'
        ])->pluck('value', 'key');

        $parcelPerKmShippingCharge = $settings['parcel_per_km_shipping_charge'] ?? null;
        $parcelMinimumShippingCharge = $settings['parcel_minimum_shipping_charge'] ?? null;
        $parcelCommissionDm = $settings['parcel_commission_dm'] ?? [null];

        $language = Helpers::get_business_settings('language') ?? [];

        return view('admin-views.parcel.settings', compact('instructions', 'parcelPerKmShippingCharge', 'parcelMinimumShippingCharge', 'parcelCommissionDm', 'language'));
    }

    public function update_settings(Request $request)
    {
        $request->validate([
            'parcel_per_km_shipping_charge' => 'required|numeric|min:0',
            'parcel_minimum_shipping_charge' => 'required|numeric|min:0',
            'parcel_commission_dm' => 'required|numeric|min:0',
        ], [
            'parcel_commission_dm.required' => translate('validation.required', ['attribute' => translate('messages.deliveryman_commission')]),
            'parcel_commission_dm.numeric' => translate('validation.numeric', ['attribute' => translate('messages.deliveryman_commission')]),
            'parcel_commission_dm.min' => translate('validation.min', ['attribute' => translate('messages.deliveryman_commission')]),

            'parcel_per_km_shipping_charge.required' => translate('validation.required', ['attribute' => translate('messages.per_km_shipping_charge')]),
            'parcel_per_km_shipping_charge.numeric' => translate('validation.numeric', ['attribute' => translate('messages.per_km_shipping_charge')]),
            'parcel_per_km_shipping_charge.min' => translate('validation.min', ['attribute' => translate('messages.per_km_shipping_charge')]),

            'parcel_minimum_shipping_charge.required' => translate('validation.required', ['attribute' => translate('messages.minimum_shipping_charge')]),
            'parcel_minimum_shipping_charge.numeric' => translate('validation.numeric', ['attribute' => translate('messages.minimum_shipping_charge')]),
            'parcel_minimum_shipping_charge.min' => translate('validation.min', ['attribute' => translate('messages.minimum_shipping_charge')]),
        ]);
        Helpers::businessUpdateOrInsert(['key' => 'parcel_per_km_shipping_charge'], ['value' => $request->parcel_per_km_shipping_charge]);
        Helpers::businessUpdateOrInsert(['key' => 'parcel_minimum_shipping_charge'], ['value' => $request->parcel_minimum_shipping_charge]);
        Helpers::businessUpdateOrInsert(['key' => 'parcel_commission_dm'], ['value' => $request->parcel_commission_dm]);

        Toastr::success(translate('messages.parcel_settings_updated'));
        return back();
    }

    public function dispatch_list($status, Request $request)
    {

        $key = isset($request->search) ? explode(' ', $request->search) : ($request['amp;search'] ? explode(' ', $request['amp;search']) : null);
        $module_id = $request->query('module_id', null);

        if (session()->has('order_filter')) {
            $request = json_decode(session('order_filter'));
            $zone_ids = isset($request->zone) ? $request->zone : 0;
        }

        Order::where(['checked' => 0])->update(['checked' => 1]);

        $orders = Order::with(['customer', 'store'])
            ->when(isset($module_id), function ($query) use ($module_id) {
                return $query->module($module_id);
            })
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%")
                            ->orWhere('order_status', 'like', "%{$value}%")
                            ->orWhere('transaction_reference', 'like', "%{$value}%");
                    }
                });
            })
            ->when(isset($request->zone), function ($query) use ($request) {
                return $query->whereHas('store', function ($query) use ($request) {
                    return $query->whereIn('zone_id', $request->zone);
                });
            })
            ->when($status == 'searching_for_deliverymen', function ($query) {
                return $query->SearchingForDeliveryman();
            })
            ->when($status == 'on_going', function ($query) {
                return $query->Ongoing();
            })
            ->when(isset($request->vendor), function ($query) use ($request) {
                return $query->whereHas('store', function ($query) use ($request) {
                    return $query->whereIn('id', $request->vendor);
                });
            })
            ->when(isset($request->from_date) && isset($request->to_date) && $request->from_date != null && $request->to_date != null, function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->from_date . " 00:00:00", $request->to_date . " 23:59:59"]);
            })
            ->ParcelOrder()
            ->module(Config::get('module.current_module_id'))
            ->OrderScheduledIn(30)
            ->orderBy('schedule_at', 'desc')
            ->paginate(config('default_pagination'));

        $orderstatus = isset($request->orderStatus) ? $request->orderStatus : [];
        $scheduled = isset($request->scheduled) ? $request->scheduled : 0;
        $vendor_ids = isset($request->vendor) ? $request->vendor : [];
        $zone_ids = isset($request->zone) ? $request->zone : [];
        $from_date = isset($request->from_date) ? $request->from_date : null;
        $to_date = isset($request->to_date) ? $request->to_date : null;
        $total = $orders->total();

        return view('admin-views.order.distaptch_list', compact('orders', 'status', 'orderstatus', 'scheduled', 'vendor_ids', 'zone_ids', 'from_date', 'to_date', 'total'));
    }
    public function parcel_dispatch_list($module, $status, Request $request)
    {
        $key = isset($request->search) ? explode(' ', $request->search) : ($request['amp;search'] ? explode(' ', $request['amp;search']) : null);
        $module_id = $request->query('module_id', null);
        $zone_ids = [];
        if (session()->has('order_filter')) {
            $request = json_decode(session('order_filter'));
            $zone_ids = isset($request->zone) ? $request->zone : 0;
        }


        Order::where(['checked' => 0])->update(['checked' => 1]);

        $orders = Order::with(['customer', 'store'])
            ->whereHas('module', function ($query) use ($module) {
                $query->where('id', $module);
            })
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%")
                            ->orWhere('order_status', 'like', "%{$value}%")
                            ->orWhere('transaction_reference', 'like', "%{$value}%");
                    }
                });
            })
            ->when(isset($module_id), function ($query) use ($module_id) {
                return $query->module($module_id);
            })
            ->when(isset($request->zone), function ($query) use ($zone_ids) {
                return $query->whereIn('zone_id', $zone_ids);
            })
            ->when($status == 'searching_for_deliverymen', function ($query) {
                return $query->SearchingForDeliveryman();
            })
            ->when($status == 'on_going', function ($query) {
                return $query->Ongoing();
            })
            ->when(isset($request->vendor), function ($query) use ($request) {
                return $query->whereHas('store', function ($query) use ($request) {
                    return $query->whereIn('id', $request->vendor);
                });
            })
            ->when(isset($request->from_date) && isset($request->to_date) && $request->from_date != null && $request->to_date != null, function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->from_date . " 00:00:00", $request->to_date . " 23:59:59"]);
            })
            ->ParcelOrder()
            ->OrderScheduledIn(30)
            ->orderBy('schedule_at', 'desc')
            ->paginate(config('default_pagination'));

        $orderstatus = isset($request->orderStatus) ? $request->orderStatus : [];
        $scheduled = isset($request->scheduled) ? $request->scheduled : 0;
        $vendor_ids = isset($request->vendor) ? $request->vendor : [];
        $zone_ids = isset($request->zone) ? $request->zone : [];
        $from_date = isset($request->from_date) ? $request->from_date : null;
        $to_date = isset($request->to_date) ? $request->to_date : null;
        $total = $orders->total();
        $parcel = true;
        return view('admin-views.order.distaptch_list', compact('orders', 'module', 'status', 'orderstatus', 'scheduled', 'vendor_ids', 'zone_ids', 'from_date', 'to_date', 'total', 'parcel'));
    }

    public function instruction(Request $request)
    {
        $request->validate([
            'instruction' => 'required|max:191',
            'instruction.0' => 'required',
        ], [
            'instruction.0.required' => translate('default_instruction_is_required'),
        ]);

        $instruction = new ParcelDeliveryInstruction();
        $instruction->instruction = $request->instruction[array_search('default', $request->lang)];
        $instruction->save();
        $data = [];
        $default_lang = str_replace('_', '-', app()->getLocale());
        foreach ($request->lang as $index => $key) {
            if ($default_lang == $key && !($request->instruction[$index])) {
                if ($key != 'default') {
                    array_push($data, array(
                        'translationable_type' => 'App\Models\ParcelDeliveryInstruction',
                        'translationable_id' => $instruction->id,
                        'locale' => $key,
                        'key' => 'instruction',
                        'value' => $instruction->instruction,
                    ));
                }
            } else {
                if ($request->instruction[$index] && $key != 'default') {
                    array_push($data, array(
                        'translationable_type' => 'App\Models\ParcelDeliveryInstruction',
                        'translationable_id' => $instruction->id,
                        'locale' => $key,
                        'key' => 'instruction',
                        'value' => $request->instruction[$index],
                    ));
                }
            }
        }
        Translation::insert($data);
        Toastr::success(translate('Delivery Instruction Added Successfully'));
        return back();
    }
    public function instruction_edit(Request $request)
    {
        $request->validate([
            'instruction' => 'required|max:191',
            'instruction.0' => 'required',
        ], [
            'instruction.0.required' => translate('default_instruction_is_required'),
        ]);
        $instruction = ParcelDeliveryInstruction::findOrFail($request->instruction_id);
        $instruction->instruction = $request->instruction[array_search('default', $request->lang1)];
        $instruction->save();

        $default_lang = str_replace('_', '-', app()->getLocale());
        foreach ($request->lang1 as $index => $key) {
            if ($default_lang == $key && !($request->instruction[$index])) {
                if ($key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\ParcelDeliveryInstruction',
                            'translationable_id' => $instruction->id,
                            'locale' => $key,
                            'key' => 'instruction'
                        ],
                        ['value' => $instruction->instruction]
                    );
                }
            } else {
                if ($request->instruction[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\ParcelDeliveryInstruction',
                            'translationable_id' => $instruction->id,
                            'locale' => $key,
                            'key' => 'instruction'
                        ],
                        ['value' => $request->instruction[$index]]
                    );
                }
            }
        }


        Toastr::success(translate('Delivery Instruction Updated Successfully'));
        return back();
    }
    public function instruction_delete(Request $request)
    {
        $instruction = ParcelDeliveryInstruction::findOrFail($request->id);
        $instruction?->translations()?->delete();
        $instruction->delete();
        Toastr::success(translate('Delivery Instruction Deleted Successfully'));
        return back();
    }
    public function instruction_status(Request $request)
    {
        $instruction = ParcelDeliveryInstruction::findOrFail($request->id);
        $instruction->status = $request->status;
        $instruction->save();
        Toastr::success(translate('messages.status_updated'));
        return back();
    }

    public function cancellationSettings(Request $request)
    {
        $parcel_cancellation_status =1?? Helpers::get_business_settings('parcel_cancellation_status');
        $parcel_cancellation_basic_setup = Helpers::get_business_settings('parcel_cancellation_basic_setup');
        $parcel_return_time_fee = Helpers::get_business_settings('parcel_return_time_fee');
        $language = Helpers::get_business_settings('language') ?? [];
        $key = isset($request->search) ? explode(' ', $request->search) : null;

        $cancellationReasons = ParcelCancellationReason::select('id', 'reason', 'cancellation_type', 'user_type', 'status')
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('reason', 'like', "%{$value}%")->orWhere('cancellation_type', 'like', "%{$value}%")->orWhere('user_type', 'like', "%{$value}%");
                    }
                });
            })
            ->paginate(config('default_pagination'));

        return view('admin-views.parcel.parcel-cancellation-setup', compact('parcel_cancellation_status', 'parcel_cancellation_basic_setup', 'parcel_return_time_fee', 'language', 'cancellationReasons'));
    }

    public function cancellationSettingsStatus()
    {

        $status = Helpers::get_business_settings('parcel_cancellation_status');
        if (isset($status) == false) {
            Helpers::businessUpdateOrInsert(['key' => 'parcel_cancellation_status'], ['value' => 1]);
        } else {
            $status = $status == 1 ? 0 : 1;
            Helpers::businessUpdateOrInsert(['key' => 'parcel_cancellation_status'], ['value' => $status]);
        }
        Toastr::success(translate('messages.parcel_cancellation_status_updated'));
        return back();
    }

    public function cancellationSettingsUpdate(Request $request)
    {
        $configs = [
            'parcel_cancellation_basic_setup' => [
                'return_fee_status' => $request->input('return_fee_status', 0),
                'return_fee'        => $request->input('return_fee', 0),
                'do_not_charge_return_fee_on_deliveryman_cancel'=> $request->input('do_not_charge_return_fee_on_deliveryman_cancel', 0),
            ],
            'parcel_return_time_fee' => [
                'status'             => $request->input('status', 0),
                'parcel_return_time' => $request->input('parcel_return_time', 0),
                'return_time_type'   => $request->input('return_time_type', 'day'),
                'return_fee_for_dm'  => $request->input('return_fee_for_dm', 0),
            ],
        ];

        foreach ($configs as $key => $data) {
            $setting = BusinessSetting::firstOrNew(['key' => $key]);
            $setting->value = json_encode($data);
            $setting->save();
        }
        Toastr::success(translate('messages.parcel_cancellation_setup_updated'));
        return back();
    }

    public function cancellationReason(Request $request)
    {
        $request->validate([
            'reason.0' => 'required',
            'reason.*' => 'max:150',
            'cancellation_type' => 'required|in:before_pickup,after_pickup',
            'user_type' => 'required|in:admin,deliveryman,vendor,customer',
        ], [
            'reason.0.required' => translate('default_reason_is_required'),
            'reason.*.max' => translate('reason_may_not_be_greater_than_150_characters'),
            'cancellation_type.required' => translate('default_cancellation_type_is_required'),
            'user_type.required' => translate('default_user_type_is_required'),
        ]);

        $cancellation_reason = new ParcelCancellationReason();
        $cancellation_reason->reason = $request->reason[array_search('default', $request->lang)];
        $cancellation_reason->cancellation_type = $request->cancellation_type;
        $cancellation_reason->user_type = $request->user_type;
        $cancellation_reason->save();

        Helpers::add_or_update_translations(request: $request, key_data: 'reason', name_field: 'reason', model_name: ParcelCancellationReason::class, data_id: $cancellation_reason->id, data_value: $cancellation_reason->reason, model_class: true);
        Toastr::success(translate('parcel_cancellation_reason_added_successfully'));
        return back();
    }
    public function cancellationReasonStatus(ParcelCancellationReason $reason)
    {
        $reason->status = !$reason->status;
        $reason->save();
        Toastr::success(translate('messages.status_updated'));
        return back();
    }
    public function cancellationReasonDelete(ParcelCancellationReason $reason)
    {
        $reason->translations()->delete();
        $reason->delete();
        Toastr::success(translate('messages.cancellation_reason_deleted_successfully'));
        return back();
    }
    public function cancellationReasonUpdate(ParcelCancellationReason $reason, Request $request)
    {
        $reason->reason = $request->reason[array_search('default', $request->lang)];
        $reason->cancellation_type = $request->cancellation_type;
        $reason->user_type = $request->user_type;
        $reason->save();
        Helpers::add_or_update_translations(request: $request, key_data: 'reason', name_field: 'reason', model_name: ParcelCancellationReason::class, data_id: $reason->id, data_value: $reason->reason, model_class: true);

        Toastr::success(translate('messages.cancellation_reason_updated_successfully'));
        return back();
    }

    public function cancellationReasonEdit(ParcelCancellationReason $reason)
    {
        $reason = $reason->load('translations');
        $language = Helpers::get_business_settings('language') ?? [];
        return response()->json([
            'view' => view('admin-views.parcel.parcel-cancellation-reason-edit', compact('reason', 'language'))->render(),
        ]);
    }
    public function cancellationReasonExport(Request $request)
    {
        $key = isset($request->search) ? explode(' ', $request->search) : null;
        $cancellationReasons = ParcelCancellationReason::select('id', 'reason', 'cancellation_type', 'user_type', 'status')
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('reason', 'like', "%{$value}%")->orWhere('cancellation_type', 'like', "%{$value}%")->orWhere('user_type', 'like', "%{$value}%");
                    }
                });
            })
            ->get();

        $data = [
            'data' => $cancellationReasons,
            'search' => $request['search'] ?? null,
        ];

        if ($request['type'] == 'csv') {
            return Excel::download(new ParcelCancellationReasonExport($data), 'Parcel_Cancellation_Reason.csv');
        }
        return Excel::download(new ParcelCancellationReasonExport($data), 'Parcel_Cancellation_Reason.xlsx');
    }
}
