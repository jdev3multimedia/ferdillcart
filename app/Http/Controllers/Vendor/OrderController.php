<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Order;
use App\Models\OrderCancelReason;
use App\Models\Store;
use App\Models\Coupon;
use App\Exports\OrderExport;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\CentralLogics\OrderLogic;
use App\CentralLogics\CouponLogic;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\OrderPayment;
use App\Traits\PlaceNewOrder;
use Brian2694\Toastr\Facades\Toastr;
use Maatwebsite\Excel\Facades\Excel;


class OrderController extends Controller
{
    use PlaceNewOrder;
    public function list($status)
    {
        $key = explode(' ', request()?->search);
        Order::where(['checked' => 0])->where('store_id',Helpers::get_store_id())->update(['checked' => 1]);

        $orders = Order::with(['customer'])
        ->when($status == 'searching_for_deliverymen', function($query){
            return $query->SearchingForDeliveryman();
        })
        ->when($status == 'confirmed', function($query){
            return $query->whereIn('order_status',['confirmed', 'accepted'])->whereNotNull('confirmed');
        })
        ->when($status == 'pending', function($query){
            if(config('order_confirmation_model') == 'store' || Helpers::get_store_data()->sub_self_delivery)
            {
                return $query->where('order_status','pending');
            }
            else
            {
                return $query->where('order_status','pending')->where('order_type', 'take_away');
            }
        })
        ->when($status == 'cooking', function($query){
            return $query->where('order_status','processing');
        })
        ->when($status == 'item_on_the_way', function($query){
            return $query->where('order_status','picked_up');
        })
        ->when($status == 'delivered', function($query){
            return $query->Delivered();
        })
        ->when($status == 'ready_for_delivery', function($query){
            return $query->where('order_status','handover');
        })
        ->when($status == 'refund_requested', function($query){
            return $query->RefundRequest();
        })
        ->when($status == 'refunded', function($query){
            return $query->Refunded();
        })
        ->when($status == 'scheduled', function($query){
            return $query->Scheduled()->where(function($q){
                if(config('order_confirmation_model') == 'store' || Helpers::get_store_data()->sub_self_delivery)
                {
                    $q->whereNotIn('order_status',['failed','canceled', 'refund_requested', 'refunded']);
                }
                else
                {
                    $q->whereNotIn('order_status',['pending','failed','canceled', 'refund_requested', 'refunded'])->orWhere(function($query){
                        $query->where('order_status','pending')->where('order_type', 'take_away');
                    });
                }

            });
        })
        ->when($status == 'all', function($query){
            return $query->where(function($query){
                $query->whereNotIn('order_status',(config('order_confirmation_model') == 'store'|| Helpers::get_store_data()->sub_self_delivery)?['failed','canceled', 'refund_requested', 'refunded']:[ 'accepted' ,'pending','failed','canceled', 'refund_requested', 'refunded'])
                ->orWhere(function($query){
                    return $query->where('order_status','pending')->where('order_type', 'take_away');
                });
            });
        })
        ->when(in_array($status, ['pending','confirmed']), function($query){
            return $query->OrderScheduledIn(30);
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
        ->StoreOrder()->NotDigitalOrder()
        ->where('store_id',\App\CentralLogics\Helpers::get_store_id())
        ->orderBy('schedule_at', 'desc')
        ->paginate(config('default_pagination'));
        $status = $status;
        return view('vendor-views.order.list', compact('orders', 'status'));
    }


    public function export_orders($file_type, $status, $type, Request $request)
    {
        $key = explode(' ', request()?->search);
        Order::where(['checked' => 0])->where('store_id',Helpers::get_store_id())->update(['checked' => 1]);
        $orders = Order::with(['customer'])
        ->when($status == 'searching_for_deliverymen', function($query){
            return $query->SearchingForDeliveryman();
        })
        ->when($status == 'confirmed', function($query){
            return $query->whereIn('order_status',['confirmed', 'accepted'])->whereNotNull('confirmed');
        })
        ->when($status == 'pending', function($query){
            if(config('order_confirmation_model') == 'store' || Helpers::get_store_data()->sub_self_delivery)
            {
                return $query->where('order_status','pending');
            }
            else
            {
                return $query->where('order_status','pending')->where('order_type', 'take_away');
            }
        })
        ->when($status == 'cooking', function($query){
            return $query->where('order_status','processing');
        })
        ->when($status == 'item_on_the_way', function($query){
            return $query->where('order_status','picked_up');
        })
        ->when($status == 'delivered', function($query){
            return $query->Delivered();
        })
        ->when($status == 'ready_for_delivery', function($query){
            return $query->where('order_status','handover');
        })
        ->when($status == 'refund_requested', function($query){
            return $query->RefundRequest();
        })
        ->when($status == 'refunded', function($query){
            return $query->Refunded();
        })
        ->when($status == 'scheduled', function($query){
            return $query->Scheduled()->where(function($q){
                if(config('order_confirmation_model') == 'store' || Helpers::get_store_data()->sub_self_delivery)
                {
                    $q->whereNotIn('order_status',['failed','canceled', 'refund_requested', 'refunded']);
                }
                else
                {
                    $q->whereNotIn('order_status',['pending','failed','canceled', 'refund_requested', 'refunded'])->orWhere(function($query){
                        $query->where('order_status','pending')->where('order_type', 'take_away');
                    });
                }

            });
        })
        ->when($status == 'all', function($query){
            return $query->where(function($query){
                $query->whereNotIn('order_status',(config('order_confirmation_model') == 'store'|| Helpers::get_store_data()->sub_self_delivery)?['failed','canceled', 'refund_requested', 'refunded']:['pending','failed','canceled', 'refund_requested', 'refunded'])
                ->orWhere(function($query){
                    return $query->where('order_status','pending')->where('order_type', 'take_away');
                });
            });
        })
        ->when(in_array($status, ['pending','confirmed']), function($query){
            return $query->OrderScheduledIn(30);
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
        ->StoreOrder()->NotDigitalOrder()
        ->where('store_id',\App\CentralLogics\Helpers::get_store_id())
        ->orderBy('schedule_at', 'desc')
        ->get();

        $data = [
            'orders'=>$orders,
            'type'=>$type,
            'status'=>$status,
            'order_status'=>isset($request->orderStatus)?implode(', ', $request->orderStatus):null,
            'search'=>$request->search??null,
            'from'=>$request->from_date??null,
            'to'=>$request->to_date??null,
            'zones'=>isset($request->zone)?Helpers::get_zones_name($request->zone):null,
            'stores'=>isset($request->vendor)?Helpers::get_stores_name(Helpers::get_store_id()):null,
        ];

    if ($file_type == 'excel') {
        return Excel::download(new OrderExport($data), 'Orders.xlsx');
    } else if ($file_type == 'csv') {
        return Excel::download(new OrderExport($data), 'Orders.csv');
    }

    }



    public function details(Request $request,$id)
    {
        $order = Order::with(['details','offline_payments','customer'=>function($query){
            return $query->withCount('orders');
        },'delivery_man'=>function($query){
            return $query->withCount('orders');
        }])->where(['id' => $id, 'store_id' => Helpers::get_store_id()])->first();
        if (isset($order)) {
            $reasons=OrderCancelReason::where('status', 1)->where('user_type' ,'store' )->get();
            return view('vendor-views.order.order-view', compact('order' ,'reasons'));
        } else {
            Toastr::info('No more orders!');
            return back();
        }
    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'order_status' => 'required|in:confirmed,processing,handover,delivered,canceled',
            'reason' =>'required_if:order_status,canceled',
        ],[
            'id.required' => 'Order id is required!'
        ]);

        $order = Order::where(['id' => $request->id, 'store_id' => Helpers::get_store_id()])->first();

        if($order->delivered != null)
        {
            Toastr::warning(translate('messages.cannot_change_status_after_delivered'));
            return back();
        }

        if($request['order_status']=='canceled' && !config('canceled_by_store'))
        {
            Toastr::warning(translate('messages.you_can_not_cancel_a_order'));
            return back();
        }

        if($request['order_status']=='canceled' && $order->confirmed)
        {
            Toastr::warning(translate('messages.you_can_not_cancel_after_confirm'));
            return back();
        }



        if($request['order_status']=='delivered' && $order->order_type != 'take_away' && !Helpers::get_store_data()->sub_self_delivery)
        {
            Toastr::warning(translate('messages.you_can_not_delivered_delivery_order'));
            return back();
        }

        if($request['order_status'] =="confirmed")
        {
            if(!Helpers::get_store_data()->sub_self_delivery && config('order_confirmation_model') == 'deliveryman' && $order->order_type != 'take_away')
            {
                Toastr::warning(translate('messages.order_confirmation_warning'));
                return back();
            }
        }

        if ($request->order_status == 'delivered') {
            $order_delivery_verification = (boolean)\App\Models\BusinessSetting::where(['key' => 'order_delivery_verification'])->first()->value;
            if($order_delivery_verification)
            {
                if($request->otp)
                {
                    if($request->otp != $order->otp)
                    {
                        Toastr::warning(translate('messages.order_varification_code_not_matched'));
                        return back();
                    }
                }
                else
                {
                    Toastr::warning(translate('messages.order_varification_code_is_required'));
                    return back();
                }
            }

            if($order->transaction  == null)
            {
                $unpaid_payment = OrderPayment::where('payment_status','unpaid')->where('order_id',$order->id)->first()?->payment_method;
                $unpaid_pay_method = 'digital_payment';
                if($unpaid_payment){
                    $unpaid_pay_method = $unpaid_payment;
                }
                if($order->payment_method == 'cash_on_delivery' || $unpaid_pay_method == 'cash_on_delivery')
                {
                    $ol = OrderLogic::create_transaction($order,'store', null);
                }
                else{
                    $ol = OrderLogic::create_transaction($order,'admin', null);
                }
                if(!$ol)
                {
                    Toastr::warning(translate('messages.faield_to_create_order_transaction'));
                    return back();
                }
                if($order->delivery_man_id){
                    Helpers::deliverymanLoyaltyPointHistory(deliveryManId:$order->delivery_man_id, amount: $order->order_amount, transactionType:'earn_on_order_completion' ,pointConversionType :'credit', reference: $order->id);
                }

            }

            $order->payment_status = 'paid';

            OrderLogic::update_unpaid_order_payment(order_id:$order->id, payment_method:$order->payment_method);

            $order->details->each(function($item, $key){
                if($item->item)
                {
                    $item->item->increment('order_count');
                }
            });
            if($order->is_guest == 0) {
            $order?->customer?->increment('order_count');
            }
        }
        if($request->order_status == 'canceled' || $request->order_status == 'delivered')
        {
            if($order->delivery_man)
            {
                $dm = $order->delivery_man;
                $dm->current_orders = $dm->current_orders>1?$dm->current_orders-1:0;
                $dm->save();
            }
            if($request->order_status == 'canceled'){

                $order->cancellation_reason = $request->reason;
                $order->canceled_by = 'store';

                $order?->store ?   Helpers::increment_order_count($order?->store) : '';

            }

            if($order->is_guest == 0){

                OrderLogic::refund_before_delivered($order);
            }

        }

        if($request->order_status == 'delivered')
        {
            $order->store->increment('order_count');
            if($order->delivery_man)
            {
                $order->delivery_man->increment('order_count');
            }

        }

        $order->order_status = $request->order_status;
        if($request->order_status == 'processing') {
            $order->processing_time = ($request?->processing_time) ? $request->processing_time : explode('-', $order['store']['delivery_time'])[0];
        }
        else if ($order->order_type != 'parcel' && in_array($request->order_status, ['picked_up']) ) {
            Helpers::sendOrderDeliveryVerificationOtp($order);
        }

        $order[$request['order_status']] = now();
        $order->save();
        if(!Helpers::send_order_notification($order))
        {
            Toastr::warning(translate('messages.push_notification_faild'));
        }

        Toastr::success(translate('messages.order_status_updated'));
        return back();
    }

    public function update_shipping(Request $request, $id)
    {
        $request->validate([
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required'
        ]);

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'floor' => $request->floor,
            'road' => $request->road,
            'house' => $request->house,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('customer_addresses')->where('id', $id)->update($address);
        Toastr::success('Delivery address updated!');
        return back();
    }

    public function generate_invoice($id)
    {
        $order = Order::where(['id' => $id, 'store_id' => Helpers::get_store_id()])->first();
        return view('vendor-views.order.invoice', compact('order'));
    }

    public function add_payment_ref_code(Request $request, $id)
    {
        Order::where(['id' => $id, 'store_id' => Helpers::get_store_id()])->update([
            'transaction_reference' => $request['transaction_reference']
        ]);

        Toastr::success('Payment reference code is added!');
        return back();
    }

    public function edit_order_amount(Request $request)
    {

        $request->validate([
            'order_amount' => 'required',

        ]);

        $order = Order::find($request->order_id);
        if(!$order){
            Toastr::error(translate('messages.Order_not_found'));
            return back();
        }
        if(!in_array($order->order_status, ['pending','confirmed','processing','picked_up','handover','accepted']) ){
            Toastr::error(translate('messages.Order_can_not_edit_a_completed_order'));
            return back();
        }
        $store = Store::find($order->store_id);
        $coupon = null;
        $free_delivery_by = null;
        if ($order->coupon_code) {
            $coupon = Coupon::active()->where(['code' => $order->coupon_code])->first();
            if (isset($coupon)) {
                $staus = CouponLogic::is_valide($coupon, $order->user_id, $order->store_id);
                if ($staus == 407) {
                    return response()->json([
                        'errors' => [
                            ['code' => 'coupon', 'message' => translate('messages.coupon_expire')]
                        ]
                    ], 407);
                } else if ($staus == 406) {
                    return response()->json([
                        'errors' => [
                            ['code' => 'coupon', 'message' => translate('messages.coupon_usage_limit_over')]
                        ]
                    ], 406);
                } else if ($staus == 404) {
                    return response()->json([
                        'errors' => [
                            ['code' => 'coupon', 'message' => translate('messages.not_found')]
                        ]
                    ], 404);
                }
            } else {
                return response()->json([
                    'errors' => [
                        ['code' => 'coupon', 'message' => translate('messages.not_found')]
                    ]
                ], 404);
            }
        }

        $product_price = $request->order_amount;
        $total_addon_price = 0;
        $store_discount_amount = $order->store_discount_amount;

        // $discount=$order->store_discount_amount;
        $discount_on_product_by = $order->discount_on_product_by ?? 'vendor' ;

        $store_discount = Helpers::get_store_discount($store);
        $store_discount =  $store_discount ? $store_discount : ['discount' => 0, 'max_discount' => 0, 'min_purchase' => 0];
        $admin_discount = Helpers::checkAdminDiscount(price: $product_price + $total_addon_price, discount: $store_discount['discount'], max_discount: $store_discount['max_discount'], min_purchase: $store_discount['min_purchase']);

        $discount = $admin_discount;

        if($admin_discount > 0 && $discount == $admin_discount ){
                $discount_on_product_by =  'admin' ;
            }


        $order->discount_on_product_by= $discount_on_product_by;
        $store_discount_amount=$discount;
        $additionalCharges=[];


        $coupon_discount_amount = $coupon ? CouponLogic::get_discount($coupon, $product_price + $total_addon_price - $store_discount_amount) : 0;
        $total_price = $product_price + $total_addon_price - $store_discount_amount - $coupon_discount_amount;
        $total_price = max($total_price, 0);
        //Added service charge


   $settings = BusinessSetting::whereIn('key', [
                'dm_tips_status',
                'additional_charge_status',
                'additional_charge',
                'extra_packaging_data',
            ])->pluck('value', 'key');

            $dm_tips_manage_status     = $settings['dm_tips_status'] ?? null;
            $additional_charge_status  = $settings['additional_charge_status'] ?? null;
            $additional_charge         = $settings['additional_charge'] ?? null;

            $extra_packaging_data_raw  = $settings['extra_packaging_data'] ?? '';
            $extra_packaging_data      = json_decode($extra_packaging_data_raw, true) ?? [];


            //Added DM TIPS
            if ($dm_tips_manage_status == 1) {
                $order->dm_tips =$order->dm_tips ?? $request->dm_tips ?? 0;
            } else{
                $order->dm_tips = 0;
            }

            //Added service charge
            $order->additional_charge =$order->additional_charge;

            if ($additional_charge_status == 1) {
                $order->additional_charge = $additional_charge ?? 0;
                // $additionalCharges['tax_on_additional_charge'] = $order->additional_charge;
            }

            // extra packaging charge

            // $order->extra_packaging_amount =  (!empty($extra_packaging_data) && $request?->extra_packaging_amount > 0 && $store && ($extra_packaging_data[$store->module->module_type] == '1') && ($store?->storeConfig?->extra_packaging_status == '1')) ? $store?->storeConfig?->extra_packaging_amount : 0;

            // if ($order->extra_packaging_amount > 0) {
            //     $additionalCharges['tax_on_packaging_charge'] =  $order->extra_packaging_amount;
            // }

            $taxData =  \Modules\TaxModule\Services\CalculateTaxService::getCalculatedTax(
                    amount: $total_price,
                    productIds: [],
                    taxPayer: 'prescription',
                    storeData: true,
                    additionalCharges: $additionalCharges,
                    addonIds: [],
                    orderId: null,
                    storeId:  $store->id
                );

                $tax_amount = $taxData['totalTaxamount'];
                $tax_included = $taxData['include'];
                $orderTaxIds = $taxData['orderTaxIds'] ?? [];
                $tax_status = $tax_included ?  'included' : 'excluded';

                $order->total_tax_amount = round($tax_amount, config('round_up_to_digit'));
                $order->tax_status = $tax_status;

        $free_delivery_over = BusinessSetting::where('key', 'free_delivery_over')->first()->value;
        if (isset($free_delivery_over)) {
            if ($free_delivery_over <= $product_price + $total_addon_price - $coupon_discount_amount - $store_discount_amount) {
                $order->delivery_charge = 0;
                $free_delivery_by = 'admin';
            }
        }

        if ($store->free_delivery) {
            $order->delivery_charge = 0;
            $free_delivery_by = 'vendor';
        }

        if ($coupon) {
            if ($coupon->coupon_type == 'free_delivery') {
                if ($coupon->min_purchase <= $product_price + $total_addon_price - $store_discount_amount) {
                    $order->delivery_charge = 0;
                    $free_delivery_by = 'admin';
                }
            }
            // $coupon->increment('total_uses');
        }

        $order->coupon_discount_amount = round($coupon_discount_amount, config('round_up_to_digit'));
        $order->coupon_discount_title = $coupon ? $coupon->title : '';

        $order->store_discount_amount = round($store_discount_amount, config('round_up_to_digit'));
        $order->order_amount = round($total_price + $order->total_tax_amount + $order->additional_charge + $order->delivery_charge, config('round_up_to_digit'));
        $order->free_delivery_by = $free_delivery_by;
        $order->order_amount = $order->order_amount + $order->dm_tips;
        $order->save();
            $order?->orderTaxes()?->delete();
            if (count($orderTaxIds)) {
                \Modules\TaxModule\Services\CalculateTaxService::updateOrderTaxData(
                    orderId: $order->id,
                    orderTaxIds: $orderTaxIds,
                );
            }
        Toastr::success(translate('messages.order_amount_updated'));
        return back();
    }
    public function edit_discount_amount(Request $request)
    {
        $request->validate([
            'discount_amount' => 'required',

        ]);

        $order = Order::find($request->order_id);
        if(!$order){
            Toastr::error(translate('messages.Order_not_found'));
            return back();
        }

        if(!in_array($order->order_status, ['pending','confirmed','processing','picked_up','handover','accepted']) ){
            Toastr::error(translate('messages.Order_can_not_edit_a_completed_order'));
            return back();
        }
        $product_price = $order['order_amount']-$order['delivery_charge']-$order['total_tax_amount']-$order['dm_tips'] - $order->additional_charge  +$order->store_discount_amount;


        if($request->discount_amount > $product_price)
        {
            Toastr::error(translate('messages.discount_amount_is_greater_then_product_amount'));
            return back();
        }
        $order->store_discount_amount = round($request->discount_amount, config('round_up_to_digit'));

        $order->discount_on_product_by= 'vendor';

        $settings = BusinessSetting::whereIn('key', [
                'dm_tips_status',
                'additional_charge_status',
                'additional_charge',
                'extra_packaging_data',
            ])->pluck('value', 'key');

            $dm_tips_manage_status     = $settings['dm_tips_status'] ?? null;
            $additional_charge_status  = $settings['additional_charge_status'] ?? null;
            $additional_charge         = $settings['additional_charge'] ?? null;

            $extra_packaging_data_raw  = $settings['extra_packaging_data'] ?? '';
            $extra_packaging_data      = json_decode($extra_packaging_data_raw, true) ?? [];


            //Added DM TIPS
            if ($dm_tips_manage_status == 1) {
                $order->dm_tips =$order->dm_tips ?? $request->dm_tips ?? 0;
            } else{
                $order->dm_tips = 0;
            }

            //Added service charge
            $order->additional_charge =$order->additional_charge;

            if ($additional_charge_status == 1) {
                $order->additional_charge = $additional_charge ?? 0;
                // $additionalCharges['tax_on_additional_charge'] = $order->additional_charge;
            }

            // // extra packaging charge

            // $order->extra_packaging_amount =  (!empty($extra_packaging_data) && $request?->extra_packaging_amount > 0 && $store && ($extra_packaging_data[$store->module->module_type] == '1') && ($store?->storeConfig?->extra_packaging_status == '1')) ? $store?->storeConfig?->extra_packaging_amount : 0;

            // if ($order->extra_packaging_amount > 0) {
            //     $additionalCharges['tax_on_packaging_charge'] =  $order->extra_packaging_amount;
            // }



         $taxData =  \Modules\TaxModule\Services\CalculateTaxService::getCalculatedTax(
                    amount: $product_price-$request->discount_amount,
                    productIds: [],
                    taxPayer: 'prescription',
                    storeData: true,
                    additionalCharges: [],
                    addonIds: [],
                    orderId: null,
                    storeId:  $order->store_id
                );

                $tax_amount = $taxData['totalTaxamount'];
                $tax_included = $taxData['include'];
                $orderTaxIds = $taxData['orderTaxIds'] ?? [];
                $tax_status = $tax_included ?  'included' : 'excluded';

                $order->total_tax_amount = round($tax_amount, config('round_up_to_digit'));
                $order->tax_status = $tax_status;



        $order->order_amount = $product_price+$order['delivery_charge']+ $order->total_tax_amount +$order['dm_tips'] + $order->additional_charge  -$order->store_discount_amount;
        $order->save();
        $order?->orderTaxes()?->delete();
            if (count($orderTaxIds)) {
                \Modules\TaxModule\Services\CalculateTaxService::updateOrderTaxData(
                    orderId: $order->id,
                    orderTaxIds: $orderTaxIds,
                );
            }
        Toastr::success(translate('messages.discount_amount_updated'));
        return back();
    }

    public function add_order_proof(Request $request, $id)
    {
        $order = Order::find($id);
        $img_names = $order->order_proof?json_decode($order->order_proof):[];
        $images = [];
        $total_file = count($request->order_proof) + count($img_names);
        if(!$img_names){
            $request->validate([
                'order_proof' => 'required|array|max:5',
            ]);
        }

        if ($total_file>5) {
            Toastr::error(translate('messages.order_proof_must_not_have_more_than_5_item'));
            return back();
        }

        if (!empty($request->file('order_proof'))) {
            foreach ($request->order_proof as $img) {
                $image_name = Helpers::upload('order/', 'png', $img);
                array_push($img_names, ['img'=>$image_name, 'storage'=> Helpers::getDisk()]);
            }
            $images = $img_names;
        }

        if(count($images)>0){
            $order->order_proof = json_encode($images);
        }
        $order->save();

        Toastr::success(translate('messages.order_proof_added'));
        return back();
    }


    public function remove_proof_image(Request $request)
    {
        $order = Order::find($request['id']);
        $array = [];
        $proof = isset($order->order_proof) ? json_decode($order->order_proof, true) : [];
        if (count($proof) < 2) {
            Toastr::warning(translate('all_image_delete_warning'));
            return back();
        }

        Helpers::check_and_delete('order/' , $request['name']);

        foreach ($proof as $image) {
            if ($image != $request['name']) {
                array_push($array, $image);
            }
        }
        Order::where('id', $request['id'])->update([
            'order_proof' => json_encode($array),
        ]);
        Toastr::success(translate('order_proof_image_removed_successfully'));
        return back();
    }
}
