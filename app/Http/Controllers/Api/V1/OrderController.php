<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\CustomerLogic;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Store;
use App\Models\Refund;
use App\Mail\PlaceOrder;
use App\Mail\RefundRequest;
use App\Models\OrderPayment;
use App\Models\RefundReason;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Models\BusinessSetting;
use App\Models\CashBackHistory;
use App\Models\OfflinePayments;
use App\Models\AutomatedMessage;
use App\Models\OrderCancelReason;
use Illuminate\Support\Facades\DB;
use App\CentralLogics\ProductLogic;
use App\Http\Controllers\Controller;
use App\Models\OfflinePaymentMethod;
use Illuminate\Support\Facades\Mail;
use App\Models\ParcelDeliveryInstruction;
use App\Traits\PlaceNewOrder;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    use PlaceNewOrder;
    public function track_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'contact_number' => $request->user ? 'nullable' : 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $user_id = $request?->user?->id;

        if ($request['contact_number'] && (substr($request['contact_number'], 0, 1) !== '+')) {
            $request['contact_number'] = '+' . $request['contact_number'];
        }

        $order = Order::with(['store', 'store.store_sub', 'delivery_man.rating', 'parcel_category', 'refund', 'payments','parcelCancellation','reviews'])->withCount('details')
            ->where('id', $request['order_id'])
            ->when($request->user, function ($query) use ($user_id) {
                return $query->where('user_id', $user_id)->where('is_guest', 0);
            })
            ->when(!$request->user, function ($query) use ($request) {
                return $query->whereJsonContains('delivery_address->contact_person_number', $request['contact_number'])->where('is_guest', 1);
            })
            ->Notpos()->first();
        if ($order) {
            $order['store'] = $order['store'] ? Helpers::store_data_formatting($order['store']) : $order['store'];
            $order['delivery_address'] = $order['delivery_address'] ? json_decode($order['delivery_address']) : $order['delivery_address'];
            $order['delivery_man'] = $order['delivery_man'] ? Helpers::deliverymen_data_formatting([$order['delivery_man']]) : $order['delivery_man'];
            $order['refund_cancellation_note'] = $order['refund'] ? $order['refund']['admin_note'] : null;
            $order['refund_customer_note'] = $order['refund'] ? $order['refund']['customer_note'] : null;
            $order['min_delivery_time'] =  $order->store ? (int) explode('-', $order->store?->delivery_time)[0] ?? 0 : 0;
            $order['max_delivery_time'] =  $order->store ? (int) explode('-', $order->store?->delivery_time)[1] ?? 0 : 0;
            $order['offline_payment'] =  isset($order->offline_payments) ? Helpers::offline_payment_formater($order->offline_payments) : null;

            unset($order['offline_payments']);
            unset($order['details']);
        } else {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => translate('messages.not_found')]
                ]
            ], 404);
        }
        return response()->json($order, 200);
    }

    public function get_order_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
            'guest_id' => $request->user ? 'nullable' : 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $user_id = $request->user ? $request->user->id : $request['guest_id'];

        $paginator = Order::with(['store', 'delivery_man.rating', 'parcel_category', 'refund:order_id,admin_note,customer_note'])->withCount('details')->where(['user_id' => $user_id])
        ->whereIn('order_status', ['delivered', 'canceled', 'refund_requested', 'refund_request_canceled', 'refunded', 'failed','returned'])
            ->when(isset($request->user), function ($query) {
                $query->where('is_guest', 0);
            })

            ->Notpos()->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);
        $orders = array_map(function ($data) {
            $data['delivery_address'] = $data['delivery_address'] ? json_decode($data['delivery_address']) : $data['delivery_address'];
            $data['store'] = $data['store'] ? Helpers::store_data_formatting($data['store']) : $data['store'];
            $data['delivery_man'] = $data['delivery_man'] ? Helpers::deliverymen_data_formatting([$data['delivery_man']]) : $data['delivery_man'];
            $data['refund_cancellation_note'] = $data['refund'] ? $data['refund']['admin_note'] : null;
            $data['refund_customer_note'] = $data['refund'] ? $data['refund']['customer_note'] : null;
            return $data;
        }, $paginator->items());
        $data = [
            'total_size' => $paginator->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'orders' => $orders
        ];
        return response()->json($data, 200);
    }


    public function get_running_orders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
            'guest_id' => $request->user ? 'nullable' : 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $user_id = $request->user ? $request->user->id : $request['guest_id'];

        $paginator = Order::with(['store', 'delivery_man.rating', 'parcel_category'])
            ->when(isset($request->user), function ($query) {
                $query->where('is_guest', 0);
            })
            ->withCount('details')
            ->where(['user_id' => $user_id])->whereNotIn('order_status', ['delivered', 'canceled', 'refund_requested', 'refund_request_canceled', 'refunded', 'failed','returned'])
            ->Notpos()->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);

        $orders = array_map(function ($data) {
            $data['delivery_address'] = $data['delivery_address'] ? json_decode($data['delivery_address']) : $data['delivery_address'];
            $data['store'] = $data['store'] ? Helpers::store_data_formatting($data['store']) : $data['store'];
            $data['delivery_man'] = $data['delivery_man'] ? Helpers::deliverymen_data_formatting([$data['delivery_man']]) : $data['delivery_man'];
            return $data;
        }, $paginator->items());
        $data = [
            'total_size' => $paginator->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'orders' => $orders
        ];
        return response()->json($data, 200);
    }

    public function get_order_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $user_id = $request?->user?->id;

        $order = Order::with('details', 'offline_payments', 'parcel_category','parcelCancellation')
            ->when(isset($request->user), function ($query) {
                $query->where('is_guest', 0);
            })
            ->when($request->user, function ($query) use ($user_id) {
                return $query->where('user_id', $user_id);
            })->findOrFail($request->order_id);

        $details = isset($order->details) ? $order->details : null;
        if ($details != null && $details->count() > 0) {
            $details = Helpers::order_details_data_formatting($details);
            $details[0]['is_guest'] = (int)$order->is_guest;
            return response()->json($details, 200);
        } else if ($order->order_type == 'parcel' || $order->prescription_order == 1) {
            $order->delivery_address = json_decode($order->delivery_address, true);
            if ($order->prescription_order && $order->order_attachment) {
                $order->order_attachment = is_array($order->order_attachment)? $order->order_attachment : json_decode($order->order_attachment, true);
            }
            return response()->json(($order), 200);
        }

        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('messages.not_found')]
            ]
        ], 404);
    }

    public function cancel_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'guest_id' => $request->user ? 'nullable' : 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $user_id = $request->user ? $request->user->id : $request['guest_id'];

        if($request->note == null && $request->reason == null) {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => translate('You Must Enter Note Or Reason')]
                ]
            ], 403);
        }

        $order = Order::where(['user_id' => $user_id, 'id' => $request['order_id']])
            ->when(isset($request->user), function ($query) {
                $query->where('is_guest', 0);
            })->Notpos()->first();

        if (!$order) {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => translate('messages.not_found')]
                ]
            ], 403);
        } elseif ($order->order_type == 'parcel') {
            $cancel_parcel_order = OrderLogic::cancelParcelOrder($order, 'customer', $request);
            if (data_get($cancel_parcel_order, 'status_code') != 200) {
                return response()->json([
                    'errors' => [
                        ['code' => data_get($cancel_parcel_order, 'code'), 'message' => data_get($cancel_parcel_order, 'message')]
                    ]
                ], data_get($cancel_parcel_order, 'status_code'));
            } else {
                return response()->json(['message' => data_get($cancel_parcel_order, 'message')], 200);
            }
        } else if ($order->order_status == 'pending' || $order->order_status == 'failed' || $order->order_status == 'canceled') {
            if (config('module.' . $order->module->module_type)['stock']) {
                foreach ($order->details as $detail) {
                    $variant = json_decode($detail['variation'], true);
                    $item = $detail->item;
                    if ($detail->campaign) {
                        $item = $detail->campaign;
                    }
                    ProductLogic::update_stock($item, -$detail->quantity, count($variant) ? $variant[0]['type'] : null)->save();
                }
            }
            if($order->is_guest == 0){

                OrderLogic::refund_before_delivered($order);
            }
            $order->order_status = 'canceled';
            $order->canceled = now();
            $order->cancellation_reason = $request->reason;
            $order->cancellation_note = $request->note;
            $order->canceled_by = 'customer';
            $order->save();
            $order?->store ?
            Helpers::increment_order_count($order?->store) : '';

            Helpers::send_order_notification($order);
            return response()->json(['message' => translate('messages.order_canceled_successfully')], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('messages.you_can_not_cancel_after_confirm')]
            ]
        ], 403);
    }

    public function refund_request(Request $request)
    {
        if (BusinessSetting::where(['key' => 'refund_active_status'])->first()->value == false) {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => translate('You can not request for a refund')]
                ]
            ], 403);
        }
        $validator = Validator::make($request->all(), [
            'customer_reason' => 'required|string|max:254',
            'refund_method' => 'nullable|string|max:100',
            'customer_note' => 'nullable|string|max:65535',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $order = Order::where(['user_id' => $request->user->id, 'id' => $request['order_id']])
            ->when(isset($request->user), function ($query) {
                $query->where('is_guest', 0);
            })
            ->Notpos()->first();
        if (!$order) {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => translate('messages.not_found')]
                ]
            ], 404);
        } else if ($order->order_status == 'delivered' && $order->payment_status == 'paid') {

            $id_img_names = [];
            if (!empty($request->file('image'))) {
                foreach ($request->image as $img) {
                    $image = Helpers::upload('refund/', 'png', $img);
                    array_push($id_img_names, ['img' => $image, 'storage' => Helpers::getDisk()]);
                }
                $image = json_encode($id_img_names);
            } else {
                $image = json_encode([]);
            }
            $refund_amount = round($order->order_amount - $order->delivery_charge - $order->dm_tips, config('round_up_to_digit'));
            $refund = new Refund();
            $refund->order_id = $order->id;
            $refund->user_id = $order->user_id;
            $refund->order_status = $order->order_status;
            $refund->refund_status = 'pending';
            $refund->refund_method = $request->refund_method ?? 'wallet';
            $refund->customer_reason = $request->customer_reason;
            $refund->customer_note = $request->customer_note;
            $refund->refund_amount = $refund_amount;
            $refund->image = $image;

            $order->order_status = 'refund_requested';
            $order->refund_requested = now();
            DB::beginTransaction();
            $refund->save();
            $order->save();
            DB::commit();
            $admin = Admin::where('role_id', 1)->first();
            $mail_status = Helpers::get_mail_status('refund_request_mail_status_admin');
            try {
                if (config('mail.status') && $admin['email'] && $mail_status == '1' && Helpers::getNotificationStatusData('admin', 'order_refund_request', 'mail_status')) {
                    Mail::to($admin?->getRawOriginal('email'))->send(new RefundRequest($order->id));
                }
            } catch (\Exception $exception) {
                info([$exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            }
            return response()->json(['message' => translate('messages.refund_request_placed_successfully')], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('Something went wrong')]
            ]
        ], 403);
    }

    public function update_payment_method(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guest_id' => $request->user ? 'nullable' : 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $config = Helpers::get_business_settings('cash_on_delivery');
        if ($config['status'] == 0) {
            return response()->json([
                'errors' => [
                    ['code' => 'cod', 'message' => translate('messages.Cash on delivery order not available at this time')]
                ]
            ], 403);
        }

        $user_id = $request->user ? $request->user->id : $request['guest_id'];
        $order = Order::where(['user_id' => $user_id, 'id' => $request['order_id']])->Notpos()->first();
        if ($order) {
            if ($order->payment_method != 'partial_payment') {
                Order::where(['user_id' => $user_id, 'id' => $request['order_id']])->update([
                    'payment_method' => 'cash_on_delivery',
                    'order_status' => 'pending',
                    'pending' => now()
                ]);
            } else {
                Order::where(['user_id' => $user_id, 'id' => $request['order_id']])->update([
                    'order_status' => 'pending',
                    'pending' => now(),
                ]);
                $payment = OrderPayment::where('payment_status', 'unpaid')->where('order_id', $request['order_id'])->first();
                if ($payment) {
                    $payment->payment_method = 'cash_on_delivery';
                }
                $payment->save();
            }

            $order = Order::where(['user_id' => $user_id, 'id' => $request['order_id']])->Notpos()->first();

            $order_mail_status = Helpers::get_mail_status('place_order_mail_status_user');
            $order_verification_mail_status = Helpers::get_mail_status('order_verification_mail_status_user');
            $address = json_decode($order->delivery_address, true);

            try {
                Helpers::send_order_notification($order);

                if ($order->is_guest == 0 && config('mail.status') && $order_mail_status == '1' && $order->customer && Helpers::getNotificationStatusData('customer', 'customer_order_notification', 'mail_status')) {
                    Mail::to($order->customer?->getRawOriginal('email'))->send(new PlaceOrder($order->id));
                }
                if ($order->is_guest == 1 && config('mail.status') && $order_mail_status == '1' && isset($address['contact_person_email']) && Helpers::getNotificationStatusData('customer', 'customer_order_notification', 'mail_status')) {
                    Mail::to($address['contact_person_email'])->send(new PlaceOrder($order->id));
                }
            } catch (\Exception $exception) {
                info([$exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            }
            return response()->json(['message' => translate('messages.payment_method_updated_successfully')], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('messages.not_found')]
            ]
        ], 404);
    }

    public function refund_reasons()
    {
        $refund_reasons = RefundReason::where('status', 1)->get();
        return response()->json([
            'refund_reasons' => $refund_reasons
        ], 200);
    }

    public function cancellation_reason(Request $request)
    {
        $limit = $request->query('limit', 25);
        $offset = $request->query('offset', 1);

        $reasons = OrderCancelReason::where('status', 1)->when($request->type, function ($query) use ($request) {
            $query->where('user_type', $request->type);
        })->paginate($limit, ['*'], 'page', $offset);

        $data = [
            'total_size' => $reasons->total(),
            'limit' => $limit,
            'offset' => $offset,
            'data' => $reasons->items()
        ];
        return response()->json($data, 200);
    }

    public function parcel_instructions(Request $request)
    {
        $limit = $request->query('limit', 25);
        $offset = $request->query('offset', 1);

        $instructions = ParcelDeliveryInstruction::where('status', 1)->paginate($limit, ['*'], 'page', $offset);

        $data = [
            'total_size' => $instructions->total(),
            'limit' => $limit,
            'offset' => $offset,
            'data' => $instructions->items()
        ];
        return response()->json($data, 200);
    }

    public function most_tips()
    {
        $data = Order::whereNot('dm_tips', 0)->get()->mode('dm_tips');
        $data = ($data && (count($data) > 0)) ? $data[0] : null;
        return response()->json([
            'most_tips_amount' => $data
        ], 200);
    }

    public function offline_payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'method_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $config = Helpers::get_mail_status('offline_payment_status');
        if ($config == 0) {
            return response()->json([
                'errors' => [
                    ['code' => 'offline_payment_status', 'message' => translate('messages.offline_payment_for_the_order_not_available_at_this_time')]
                ]
            ], 403);
        }
        $order = Order::findOrFail($request->order_id);

        $offline_payment_info = [];
        $method = OfflinePaymentMethod::where(['id' => $request->method_id, 'status' => 1])->first();
        try {
            if (isset($method)) {
                $fields = array_column($method->method_informations, 'customer_input');
                $values = $request->all();

                $offline_payment_info['method_id'] = $request->method_id;
                $offline_payment_info['method_name'] = $method->method_name;
                foreach ($fields as $field) {
                    if (key_exists($field, $values)) {
                        $offline_payment_info[$field] = $values[$field];
                    }
                }
            }

            $OfflinePayments = OfflinePayments::firstOrNew(['order_id' => $order->id]);

            $OfflinePayments->payment_info = json_encode($offline_payment_info);
            $OfflinePayments->customer_note = $request->customer_note;
            $OfflinePayments->method_fields = json_encode($method?->method_fields);
            DB::beginTransaction();
            $OfflinePayments->save();

            $order->order_status = 'pending';
            $order->payment_method = 'offline_payment';
            $order->save();
            DB::commit();

            $data = [
                'title' => translate('Order_Notification'),
                'description' => translate('New order alert, confirm to proceed'),
                'order_id' => $order->id,
                'image' => '',
                'module_id' => $order->module_id,
                'order_type' => $order->order_type,
                'zone_id' => $order->zone_id,
                'type' => 'new_order',
            ];
            Helpers::send_push_notif_to_topic($data, 'admin_message', 'order_request', url('/') . '/admin/order/list/all');

            return response()->json([
                'payment' => 'success'
            ], 200);
        } catch (\Exception $exception) {
            info([$exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            DB::rollBack();
            return response()->json(['payment' => $exception->getMessage()], 403);
        }
    }


    public function update_offline_payment_info(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $order = Order::where('id', $request->order_id)->firstOrfail();

        $info = OfflinePayments::where('order_id', $request->order_id)->firstOrfail();
        $old_data =   json_decode($info->payment_info, true);
        $method_id = data_get($old_data, 'method_id', null);
        $offline_payment_info = [];
        $method = OfflinePaymentMethod::where('id', $method_id)->first();
        if (isset($method)) {
            $fields = array_column($method->method_informations, 'customer_input');
            $values = $request->all();

            $offline_payment_info['method_id'] = $method->id;
            $offline_payment_info['method_name'] = $method->method_name;
            foreach ($fields as $field) {
                if (key_exists($field, $values)) {
                    $offline_payment_info[$field] = $values[$field];
                }
            }
        }

        $info->customer_note = $request->customer_note ?? $info->customer_note;
        $info->payment_info = json_encode($offline_payment_info);
        $info->status = 'pending';
        $info->save();

        if($request->update_payment_info){

            if($order->is_guest){
                 $user_fcm = $order->guest->fcm_token;
            }else{
                 $user_fcm = $order?->customer?->cm_firebase_token;
            }
            if (Helpers::getNotificationStatusData('customer','customer_order_notification','push_notification_status') && $user_fcm) {
                $data = [
                    'title' => translate('Payment_Info'),
                    'description' => translate('Your_offline_payment_info_updated_successfully'),
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'order_status',
                ];
                Helpers::send_push_notif_to_device($user_fcm, $data);
                DB::table('user_notifications')->insert([
                    'data' => json_encode($data),
                    'user_id' => $order->user_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }




        } else {
            Helpers::send_order_notification($order);
        }


        return response()->json(['payment' => 'Payment_Info_Updated_successfully'], 200);
    }



    public function order_again(Request $request)
    {
        Helpers::setZoneIds($request);

        $longitude = $request->header('longitude') ?? 0;
        $latitude = $request->header('latitude') ?? 0;

        $zone_id = json_decode($request->header('zoneId'), true);
        $data = Store::withOpen($longitude, $latitude)->wherehas('orders', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id)->where('is_guest', 0)->latest();
        })
            ->where('module_id', getModuleId($request->header('moduleId')))
            ->withcount('items')
            ->with(['itemsForReorder'])
            ->Active()
            ->whereIn('zone_id', $zone_id)
            ->take(20)

            ->orderBy('open', 'desc')
            ->get()
            ->map(function ($data) {
                $data->items = $data->itemsForReorder->take(5);
                unset($data->itemsForReorder);
                return $data;
            });

        return response()->json(Helpers::store_data_formatting($data, true), 200);
    }


    private function createCashBackHistory($order_amount, $user_id, $order_id)
    {
        $cashBack =  Helpers::getCalculatedCashBackAmount(amount: $order_amount, customer_id: $user_id);
        if (data_get($cashBack, 'calculated_amount') > 0) {
            $CashBackHistory = new CashBackHistory();
            $CashBackHistory->user_id = $user_id;
            $CashBackHistory->order_id = $order_id;
            $CashBackHistory->calculated_amount = data_get($cashBack, 'calculated_amount');
            $CashBackHistory->cashback_amount = data_get($cashBack, 'cashback_amount');
            $CashBackHistory->cash_back_id = data_get($cashBack, 'id');
            $CashBackHistory->cashback_type = data_get($cashBack, 'cashback_type');
            $CashBackHistory->min_purchase = data_get($cashBack, 'min_purchase');
            $CashBackHistory->max_discount = data_get($cashBack, 'max_discount');
            $CashBackHistory->save();

            $CashBackHistory?->order()->update([
                'cash_back_id' => $CashBackHistory->id
            ]);
        }
        return true;
    }


    public function automatedMessage(Request $request)
    {
        $limit = $request->query('limit', 25);
        $offset = $request->query('offset', 1);
        $messages = AutomatedMessage::orderBy('id', 'desc')->where('status', 1)->select(['id', 'message'])
            ->paginate($limit, ['*'], 'page', $offset);
        $messages->makeHidden(['translations']);
        $data = [
            'total_size' => $messages->total(),
            'limit' => $limit,
            'offset' => $offset,
            'data' => $messages->items()
        ];

        return response()->json($data, 200);
    }


    public function place_order(Request $request)
    {
        return $this->new_place_order($request);
    }
    public function prescription_place_order(Request $request)
    {
        return $this->new_place_order($request, true);
    }

    public function getTaxFromCart(Request $request)
    {
        return $this->getCalculatedTax($request);
    }

    public function getSurgePriceAmount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zone_id' => 'required',
            'module_id' => 'required',
            'date_time' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        return $this->getSurgePrice($request->zone_id, $request->module_id, $request->date_time);
    }


    public function parcelReturn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'order_status' => 'required|in:returned',
            'return_otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $order = Order::where(['id' => $request->order_id])->with('parcelCancellation')->first();


        $validationCheck =  OrderLogic::makeValidationForParcelReturn($request,$order);
        if (data_get($validationCheck, 'status_code') === 403) {

            return response()->json([
                'errors' => [
                    ['code' => data_get($validationCheck, 'code'), 'message' => data_get($validationCheck, 'message')]
                ]
            ], data_get($validationCheck, 'status_code'));
        }

        if( in_array($order->parcelCancellation->cancel_by ,['deliveryman', 'admin_for_deliveryman']  )){
            OrderLogic::deliveryManCancelParcelTransaction($order,'customer');
        } else{
            OrderLogic::create_transaction_parcel_cancel($order, $order->payment_status == 'paid' ? 'admin' : 'deliveryman' );
        }

        return response()->json(['message' => translate('messages.Parcel_returned_successfully')], 200);
    }

    public function walletPayment(Request $request){
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $order = Order::where(['id' => $request->order_id])->first();
        if($order->payment_status == 'paid'){
            return response()->json(['message' => translate('messages.Order_payment_successfully')], 200);
        }

        $walletTransaction = CustomerLogic::create_wallet_transaction($order->user_id, $order->order_amount, 'order_place', $order->id);
        if($walletTransaction){
            $order->order_status = 'confirmed';
            $order->payment_status = 'paid';
            $order->payment_method = 'wallet';
            $order->update();

            return response()->json(['message' => translate('messages.Order_payment_successfully')], 200);
        }

        return response()->json(['message' => translate('messages.some_thing_went_wrong')], 400);
    }
}
