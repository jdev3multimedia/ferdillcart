<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Models\Item;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Store;
use App\Library\Payer;
use App\Models\Coupon;
use App\Models\Vendor;
use App\Traits\Payment;
use App\Models\Campaign;
use App\Library\Receiver;
use App\Models\StoreWallet;
use App\Models\Notification;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\VendorEmployee;
use App\Models\BusinessSetting;
use App\Models\WithdrawRequest;
use App\Models\UserNotification;
use App\Models\WithdrawalMethod;
use App\CentralLogics\OrderLogic;
use App\CentralLogics\StoreLogic;
use App\Mail\WithdrawRequestMail;
use App\CentralLogics\CouponLogic;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Library\Payment as PaymentInfo;
use App\Models\SubscriptionTransaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Models\SubscriptionBillingAndRefundHistory;
use Modules\Rental\Emails\ProviderWithdrawRequestMail;

class VendorController extends Controller
{
    public function get_profile(Request $request)
    {
        $vendor = $request['vendor'];
        $min_amount_to_pay_store = BusinessSetting::where('key' , 'min_amount_to_pay_store')->first()->value ?? 0;
        $store = Helpers::store_data_formatting($vendor->stores[0], false);
        $discount=Helpers::get_store_discount($vendor->stores[0]);
        unset($store['discount']);
        $store['discount']=$discount;
        $store['schedules']=$store->schedules()->get();
        $store['module']=$store->module;
        $vendor['order_count'] =$vendor->orders->where('order_type','!=','pos')->whereNotIn('order_status',['canceled','failed'])->count();
        $vendor['todays_order_count'] =$vendor->todaysorders->where('order_type','!=','pos')->whereIn('order_status', ['refunded', 'delivered'])->count();
        $vendor['this_week_order_count'] =$vendor->this_week_orders->where('order_type','!=','pos')->whereIn('order_status', ['refunded', 'delivered'])->count();
        $vendor['this_month_order_count'] =$vendor->this_month_orders->where('order_type','!=','pos')->whereIn('order_status', ['refunded', 'delivered'])->count();
        $vendor['member_since_days'] = (int) $vendor->created_at->diffInDays();
        $vendor['cash_in_hands'] =$vendor->wallet?(float)$vendor->wallet->collected_cash:0;
        $vendor['balance'] =$vendor->wallet?(float)$vendor->wallet->balance:0;
        $vendor['total_earning'] =$vendor->wallet?(float)$vendor->wallet->total_earning:0;
        $vendor['todays_earning'] =(float)$vendor->todays_earning()->sum('store_amount');
        $vendor['this_week_earning'] =(float)$vendor->this_week_earning()->sum('store_amount');
        $vendor['this_month_earning'] =(float)$vendor->this_month_earning()->sum('store_amount');

            if($vendor['balance']  < 0){
                $vendor['balance']  = 0 ;
            }

        $vendor['Payable_Balance'] =(float) ($vendor?->wallet?->balance  < 0 ? abs($vendor?->wallet?->balance): 0 );

        $wallet_earning =  round($vendor?->wallet?->total_earning -($vendor?->wallet?->total_withdrawn + $vendor?->wallet?->pending_withdraw) , 8);
        $vendor['withdraw_able_balance'] =(float) $wallet_earning ;

        if(($vendor?->wallet?->balance > 0 && $vendor?->wallet?->collected_cash > 0 ) || ($vendor?->wallet?->collected_cash != 0 && $wallet_earning !=  0)){
            $vendor['adjust_able'] = true;
        }
        elseif($vendor?->wallet?->balance ==  $wallet_earning  ){
            $vendor['adjust_able'] = false;
        }
        else{
            $vendor['adjust_able'] = false;
        }

        $vendor['show_pay_now_button'] = false;
        $digital_payment = Helpers::get_business_settings('digital_payment');

        if ($min_amount_to_pay_store <= $vendor?->wallet?->collected_cash && $digital_payment['status'] == 1 &&  $vendor?->wallet?->collected_cash  >  $vendor?->wallet?->balance ){
            $vendor['show_pay_now_button'] = true;
        }

        $vendor['pending_withdraw'] =(float)$vendor?->wallet?->pending_withdraw ?? 0;
        $vendor['total_withdrawn'] = (float)$vendor?->wallet?->total_withdrawn ?? 0;

        if($vendor['balance'] > 0 ){
            $vendor['dynamic_balance'] =  (float) abs($wallet_earning);
                if($vendor?->wallet?->balance ==  $wallet_earning){
                    $vendor['dynamic_balance_type']  = translate('messages.Withdrawable_Balance') ;
                } else{
                    $vendor['dynamic_balance_type']  = translate('messages.Balance').' '.(translate('Unadjusted')) ;
                }

        } else{
            $vendor['dynamic_balance']   =  (float) abs($vendor?->wallet?->collected_cash) ?? 0;
            $vendor['dynamic_balance_type']  = translate('messages.Payable_Balance') ;
        }

        $Payable_Balance = $vendor?->wallet?->collected_cash  > 0 ? 1: 0;

        $cash_in_hand_overflow=  BusinessSetting::where('key' ,'cash_in_hand_overflow_store')->first()?->value;
        $cash_in_hand_overflow_store_amount =  BusinessSetting::where('key' ,'cash_in_hand_overflow_store_amount')->first()?->value;
        $val=  $cash_in_hand_overflow_store_amount - (($cash_in_hand_overflow_store_amount * 10)/100);

        $vendor['over_flow_warning'] = false;
        if($Payable_Balance == 1 &&  $cash_in_hand_overflow &&  $vendor?->wallet?->balance < 0 &&  $val <=  abs($vendor?->wallet?->collected_cash)  ){

            $vendor['over_flow_warning'] = true;
        }

        $vendor['over_flow_block_warning'] = false;
        if ($Payable_Balance == 1 &&  $cash_in_hand_overflow &&  $vendor?->wallet?->balance < 0 &&  $cash_in_hand_overflow_store_amount < abs($vendor?->wallet?->collected_cash)){
            $vendor['over_flow_block_warning'] = true;
        }



        $vendor["stores"] = $store;
        $st = Store::withoutGlobalScope('translate')->findOrFail($store['id']);
        $vendor["translations"] = $st->translations;
        if ($request['vendor_employee']) {
            $vendor_employee = $request['vendor_employee'];
            $role = $vendor_employee->role ? json_decode($vendor_employee->role->modules):[];
            $vendor["roles"] = $role;
            $vendor["employee_info"] = json_decode($request['vendor_employee']);
        }
        unset($vendor['orders']);
        unset($vendor['rating']);
        unset($vendor['todaysorders']);
        unset($vendor['this_week_orders']);
        unset($vendor['wallet']);
        unset($vendor['todaysorders']);
        unset($vendor['this_week_orders']);
        unset($vendor['this_month_orders']);

        $vendor['subscription_transactions']= (boolean) SubscriptionTransaction::where('store_id',$store->id)->count() > 0? true : false;
            if(isset($st?->store_sub_update_application)){
                    $vendor['subscription'] =$st?->store_sub_update_application;

                    if($vendor['subscription']->max_product== 'unlimited' ){
                        $max_product_uploads= -1;
                    }
                    else{
                        if($st?->module_type == 'rental'){
                            $max_product_uploads = $vendor['subscription']->max_product - $st?->vehicles()->count() > 0?  $vendor['subscription']->max_product - $st?->vehicles()->count() : 0 ;
                        } else{
                            $max_product_uploads= $vendor['subscription']->max_product - $st?->items()->count() > 0?  $vendor['subscription']->max_product - $st?->items()->count() : 0 ;
                        }
                    }

                    $pending_bill= SubscriptionBillingAndRefundHistory::where(['store_id'=>$store->id,
                                        'transaction_type'=>'pending_bill', 'is_success' =>0])?->sum('amount') ?? 0;
                    $vendor['subscription_other_data'] =  [
                        'total_bill'=>  (float) $vendor['subscription']->package?->price * ($vendor['subscription']->total_package_renewed + 1),
                        'max_product_uploads' => (int) $max_product_uploads,
                        'pending_bill' => (float) $pending_bill,
                    ];
                }


                if( $st?->storeConfig?->minimum_stock_for_warning > 0){
                    $items=  $st?->items()->where('stock' ,'<=' , $st?->storeConfig?->minimum_stock_for_warning );
                } else{
                    $items=  $st?->items()->where('stock',0 );
                }

                $out_of_stock_count=  $st?->module->module_type != 'food' ?  $items->orderby('stock')->latest()->count() : 0;
                $vendor['out_of_stock_count'] = (int) $out_of_stock_count;


        return response()->json($vendor, 200);
    }

    public function active_status(Request $request)
    {
        $store = $request->vendor->stores[0];
        $store->active = $store->active?0:1;
        $store->save();
        return response()->json(['message' => $store->active?translate('messages.store_opened'):translate('messages.store_temporarily_closed')], 200);
    }

    public function get_earning_data(Request $request)
    {
        $vendor = $request['vendor'];
        $data= StoreLogic::get_earning_data($vendor->id);
        return response()->json($data, 200);
    }

    public function update_profile(Request $request)
    {
        $vendor = $request['vendor'];
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => 'required|unique:vendors,phone,'.$vendor->id,
            'password' => ['nullable', Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()],
        ], [
            'f_name.required' => translate('messages.first_name_is_required'),
            'l_name.required' => translate('messages.Last name is required!'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $image = $request->file('image');

        if ($request->has('image')) {
            $imageName = Helpers::update('vendor/', $vendor->image, 'png', $request->file('image'));
        } else {
            $imageName = $vendor->image;
        }

        if ($request['password'] != null && strlen($request['password']) > 5) {
            $pass = bcrypt($request['password']);
        } else {
            $pass = $vendor->password;
        }
        $vendor->f_name = $request->f_name;
        $vendor->l_name = $request->l_name;
        $vendor->phone = $request->phone;
        $vendor->image = $imageName;
        $vendor->password = $pass;
        $vendor->updated_at = now();
        $vendor->save();

        return response()->json(['message' => translate('messages.profile_updated_successfully')], 200);
    }

    public function get_current_orders(Request $request)
    {
        $vendor = $request['vendor'];

        $orders = Order::whereHas('store.vendor', function($query) use($vendor){
            $query->where('id', $vendor->id);
        })
        ->with('customer')

        ->where(function($query)use($vendor){
            if(config('order_confirmation_model') == 'store' || $vendor->stores[0]->sub_self_delivery)
            {
                $query->whereIn('order_status', ['accepted','pending','confirmed', 'processing', 'handover','picked_up']);
            }
            else
            {
                $query->whereIn('order_status', ['confirmed', 'processing', 'handover','picked_up'])
                ->orWhere(function($query){
                    $query->whereNotNull('confirmed')->where('order_status', 'accepted');
                })
                ->orWhere(function($query){
                    $query->where('payment_status','paid')->where('order_status', 'accepted');
                })
                ->orWhere(function($query){
                    $query->where('order_status','pending')->where('order_type', 'take_away');
                });
            }
        })
        ->Notpos()
        ->NotDigitalOrder()

        ->orderBy('schedule_at', 'desc')
        ->get();
        $orders= Helpers::order_data_formatting($orders, true);
        return response()->json($orders, 200);
    }

    public function get_completed_orders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
            'status' => 'required|in:all,refunded,delivered',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $vendor = $request['vendor'];

        $paginator = Order::whereHas('store.vendor', function($query) use($vendor){
            $query->where('id', $vendor->id);
        })
        ->with('customer')
        ->when($request->status == 'all', function($query){
            return $query->whereIn('order_status', ['refunded', 'delivered']);
        })
        ->when($request->status != 'all', function($query)use($request){
            return $query->where('order_status', $request->status);
        })
        ->Notpos()
        ->latest()
        ->paginate($request['limit'], ['*'], 'page', $request['offset']);
        $orders= Helpers::order_data_formatting($paginator->items(), true);
        $data = [
            'total_size' => $paginator->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'orders' => $orders
        ];
        return response()->json($data, 200);
    }

    public function get_canceled_orders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $vendor = $request['vendor'];

        $paginator = Order::whereHas('store.vendor', function($query) use($vendor){
            $query->where('id', $vendor->id);
        })
        ->with('customer')
        ->where('order_status', 'canceled')
        ->Notpos()
        ->latest()
        ->paginate($request['limit'], ['*'], 'page', $request['offset']);
        $orders= Helpers::order_data_formatting($paginator->items(), true);
        $data = [
            'total_size' => $paginator->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'orders' => $orders
        ];
        return response()->json($data, 200);
    }

    public function update_order_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'reason' =>'required_if:status,canceled',
            'status' => 'required|in:confirmed,processing,handover,delivered,canceled',
            'order_proof' =>'array|max:5',
        ]);

        $validator->sometimes('otp', 'required', function ($request) {
            return (Config::get('order_delivery_verification')==1 && $request['status']=='delivered');
        });

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $vendor = $request['vendor'];

        $order = Order::whereHas('store.vendor', function($query) use($vendor){
            $query->where('id', $vendor->id);
        })
        ->where('id', $request['order_id'])
        ->Notpos()
        ->first();

        if($request['order_status']=='canceled')
        {
            if(!config('canceled_by_store'))
            {
                return response()->json([
                    'errors' => [
                        ['code' => 'status', 'message' => translate('messages.you_can_not_cancel_a_order')]
                    ]
                ], 403);
            }
            else if($order->confirmed)
            {
                return response()->json([
                    'errors' => [
                        ['code' => 'status', 'message' => translate('messages.you_can_not_cancel_after_confirm')]
                    ]
                ], 403);
            }
        }

        if($request['status'] =="confirmed" && !$vendor->stores[0]->sub_self_delivery && config('order_confirmation_model') == 'deliveryman' && $order->order_type != 'take_away')
        {
            return response()->json([
                'errors' => [
                    ['code' => 'order-confirmation-model', 'message' => translate('messages.order_confirmation_warning')]
                ]
            ], 403);
        }

        if($order->picked_up != null)
        {
            return response()->json([
                'errors' => [
                    ['code' => 'status', 'message' => translate('messages.You_can_not_change_status_after_picked_up_by_delivery_man')]
                ]
            ], 403);
        }

        if($request['status']=='delivered' && $order->order_type != 'take_away' && !$vendor->stores[0]->sub_self_delivery)
        {
            return response()->json([
                'errors' => [
                    ['code' => 'status', 'message' => translate('messages.you_can_not_delivered_delivery_order')]
                ]
            ], 403);
        }
        if(Config::get('order_delivery_verification')==1 && $request['status']=='delivered' && $order->otp != $request['otp'])
        {
            return response()->json([
                'errors' => [
                    ['code' => 'otp', 'message' => 'Not matched']
                ]
            ], 403);
        }

        if ($request->status == 'delivered' && $order->transaction == null) {
            $unpaid_payment = OrderPayment::where('payment_status','unpaid')->where('order_id',$order->id)->first()?->payment_method;
            $unpaid_pay_method = 'digital_payment';
            if($unpaid_payment){
                $unpaid_pay_method = $unpaid_payment;
            }
            if($order->payment_method == 'cash_on_delivery' || $unpaid_pay_method == 'cash_on_delivery')
            {
                $ol = OrderLogic::create_transaction($order,'store', null);
            }
            else
            {
                $ol = OrderLogic::create_transaction($order,'admin', null);
            }
              if($order->delivery_man_id){
                    Helpers::deliverymanLoyaltyPointHistory(deliveryManId:$order->delivery_man_id, amount: $order->order_amount, transactionType:'earn_on_order_completion' ,pointConversionType :'credit', reference: $order->id);
                }


            $order->payment_status = 'paid';
            OrderLogic::update_unpaid_order_payment(order_id:$order->id, payment_method:$order->payment_method);
        }

        if($request->status == 'delivered')
        {
            $order->details->each(function($item, $key){
                if($item->item)
                {
                    $item->item->increment('order_count');
                }
            });

            if($order->is_guest == 0){
                $order->customer->increment('order_count');
            }
            $order?->store?->increment('order_count');


            $img_names = [];
            $images = [];
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
        }
        if($request->status == 'canceled' || $request->status == 'delivered')
        {
            if($order->delivery_man)
            {
                $dm = $order->delivery_man;
                $dm->current_orders = $dm->current_orders>1?$dm->current_orders-1:0;
                $dm->save();
            }
            $order->cancellation_reason=$request->reason;
            $order->canceled_by='store';
        } else if ($order->order_type != 'parcel' && in_array($request->status, ['picked_up']) ) {
            Helpers::sendOrderDeliveryVerificationOtp($order);
        }

        $order->order_status = $request['status'];
        if($order->order_status == 'processing') {
            $order->processing_time = ($request?->processing_time) ? $request->processing_time : explode('-', $order['store']['delivery_time'])[0];
        }
        $order[$request['status']] = now();
        $order->save();
        Helpers::send_order_notification($order);

        return response()->json(['message' => 'Status updated'], 200);
    }

    public function get_order_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $vendor = $request['vendor'];

        $order = Order::whereHas('store.vendor', function($query) use($vendor){
            $query->where('id', $vendor->id);
        })
        ->with(['customer','details'])
        ->where('id', $request['order_id'])
        ->Notpos()
        ->first();
        if(!$order){
            return response()->json(['errors'=>[['code'=>'order_id', 'message'=>trans('messages.order_data_not_found')]]],404);
        }
        $details = isset($order->details)?$order->details:null;
        if ($details != null && $details->count() > 0) {
            $details = $details = Helpers::order_details_data_formatting($details);
            $details[0]['is_guest'] = (int)$order->is_guest;
            return response()->json($details, 200);
        } else if ($order->order_type == 'parcel' || $order->prescription_order == 1) {
            $order->delivery_address = json_decode($order->delivery_address, true);
            if($order->prescription_order && $order->order_attachment){
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

    public function get_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $vendor = $request['vendor'];

        $order = Order::whereHas('store.vendor', function($query) use($vendor){
            $query->where('id', $vendor->id);
        })
        ->with(['customer','details','delivery_man','payments'])
        ->where('id', $request['order_id'])
        ->first();
        if(!$order){
            return response()->json(['errors'=>[['code'=>'order_id', 'message'=>trans('messages.order_data_not_found')]]],404);
        }
        return response()->json(Helpers::order_data_formatting($order),200);
    }

    public function get_all_orders(Request $request)
    {
        $vendor = $request['vendor'];

        $orders = Order::whereHas('store.vendor', function($query) use($vendor){
            $query->where('id', $vendor->id);
        })
        ->with('customer')
        ->Notpos()
        ->NotDigitalOrder()

        ->orderBy('schedule_at', 'desc')
        ->get();
        $orders= Helpers::order_data_formatting($orders, true);
        return response()->json($orders, 200);
    }

    public function update_fcm_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        if (!$request->hasHeader('vendorType')) {
            $errors = [];
            array_push($errors, ['code' => 'vendor_type', 'message' => translate('messages.vendor_type_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }
        $vendor_type= $request->header('vendorType');
        $vendor = $request['vendor'];
        if($vendor_type == 'owner'){
            Vendor::where(['id' => $vendor['id']])->update([
                'firebase_token' => $request['fcm_token']
            ]);
        }else{
            VendorEmployee::where(['id' => $request['vendor_employee']->id])->update([
                'firebase_token' => $request['fcm_token']
            ]);

        }

        return response()->json(['message'=>'successfully updated!'], 200);
    }

    public function get_notifications(Request $request){
        $vendor = $request['vendor'];

        $notifications = Notification::active()->where(function($q) use($vendor){
            $q->whereNull('zone_id')->orWhere('zone_id', $vendor->stores[0]->zone_id);
        })->where('tergat', 'store')->where('created_at', '>=', \Carbon\Carbon::today()->subDays(7))->get();

        $notifications->append('data');

        $user_notifications = UserNotification::where('vendor_id', $vendor->id)->where('created_at', '>=', \Carbon\Carbon::today()->subDays(7))->get();

        $notifications =  $notifications->merge($user_notifications);

        try {
            return response()->json($notifications, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function get_basic_campaigns(Request $request)
    {
        $vendor = $request['vendor'];
        $store_id = $vendor->stores[0]->id;
        $module_id = $vendor->stores[0]->module_id;

        $campaigns=Campaign::with('stores')->module($module_id)->Running()->latest()->get();
        $data = [];

        foreach ($campaigns as $item) {
            $store_ids = count($item->stores)?$item->stores->pluck('id')->toArray():[];
            $store_joining_status = count($item->stores)?$item->stores->pluck('pivot')->toArray():[];
            if($item->start_date)
            {
                $item['available_date_starts']=$item->start_date->format('Y-m-d');
                unset($item['start_date']);
            }
            if($item->end_date)
            {
                $item['available_date_ends']=$item->end_date->format('Y-m-d');
                unset($item['end_date']);
            }

            if (count($item['translations'])>0 ) {
                $translate = array_column($item['translations']->toArray(), 'value', 'key');
                $item['title'] = $translate['title'];
                $item['description'] = $translate['description'];
            }

            $item['vendor_status'] = null;
            foreach($store_joining_status as $status){
                if($status['store_id'] == $store_id){
                    $item['vendor_status'] =  $status['campaign_status'];
                }

            }

            $item['is_joined'] = in_array($store_id, $store_ids)?true:false;
            unset($item['stores']);
            array_push($data, $item);
        }
        // $data = CampaignLogic::get_basic_campaigns($vendor->stores[0]->id, $request['limite'], $request['offset']);
        return response()->json($data, 200);
    }

    public function remove_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'campaign_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $campaign = Campaign::where('status', 1)->find($request->campaign_id);
        if(!$campaign)
        {
            return response()->json([
                'errors'=>[
                    ['code'=>'campaign', 'message'=>'Campaign not found or upavailable!']
                ]
            ]);
        }
        $store = $request['vendor']->stores[0];
        $campaign->stores()->detach($store);
        $campaign->save();
        return response()->json(['message'=>translate('messages.you_are_successfully_removed_from_the_campaign')], 200);
    }
    public function addstore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'campaign_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $campaign = Campaign::where('status', 1)->find($request->campaign_id);
        if(!$campaign)
        {
            return response()->json([
                'errors'=>[
                    ['code'=>'campaign', 'message'=>'Campaign not found or upavailable!']
                ]
            ]);
        }
        $store = $request['vendor']->stores[0];
        $campaign->stores()->attach($store);
        $campaign->save();
        try
        {
            $admin= Admin::where('role_id', 1)->first();
            $mail_status = Helpers::get_mail_status('campaign_request_mail_status_admin');
            if(config('mail.status') && $mail_status == '1' && Helpers::getNotificationStatusData('admin','campaign_join_request','mail_status' )) {
                Mail::to($admin?->getRawOriginal('email'))->send(new \App\Mail\CampaignRequestMail($store->name));
            }
            $mail_status = Helpers::get_mail_status('campaign_request_mail_status_store');
            if(config('mail.status') && $mail_status == '1' &&  Helpers::getNotificationStatusData('store','store_campaign_join_request','mail_status',$store->id )) {
                Mail::to($store->vendor?->getRawOriginal('email'))->send(new \App\Mail\VendorCampaignRequestMail($store->name,'pending'));
            }
        }
        catch(\Exception $e)
        {
            info($e->getMessage());
        }
        return response()->json(['message'=>translate('messages.you_are_successfully_joined_to_the_campaign')], 200);
    }

    public function get_items(Request $request)
    {
        $limit=$request->limit?$request->limit:25;
        $offset=$request->offset?$request->offset:1;

        $type = $request->query('type', 'all');
        $category_id = $request->category_id??0;
        $paginator = Item::with('tags');

          if($category_id != 0)
        {
            $paginator = $paginator->whereHas('category',function($q)use($category_id){
                return $q->whereId($category_id)->orWhere('parent_id', $category_id);
            });
        }

        $key = isset($request['search']) ? explode(' ', $request['search']) : [];
        $paginator = $paginator->when(isset($key), function ($query) use ($key) {
            return $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        });

       $paginator = $paginator->type($type)->Approved()->where('store_id', $request['vendor']->stores[0]->id)->latest()->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'items' => Helpers::product_data_formatting($paginator->items(), true, true, app()->getLocale())
        ];

        return response()->json($data, 200);
    }

    public function update_bank_info(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|max:191',
            'branch' => 'required|max:191',
            'holder_name' => 'required|max:191',
            'account_no' => 'required|max:191'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $bank = $request['vendor'];
        $bank->bank_name = $request->bank_name;
        $bank->branch = $request->branch;
        $bank->holder_name = $request->holder_name;
        $bank->account_no = $request->account_no;
        $bank->save();

        return response()->json(['message'=>translate('messages.bank_info_updated_successfully'),200]);
    }

    public function withdraw_list(Request $request)
    {
        $withdraw_req = WithdrawRequest::where('vendor_id', $request['vendor']->id)->latest()->get();

        $temp = [];
        $status = [
            0=>'Pending',
            1=>'Approved',
            2=>'Denied'
        ];
        foreach($withdraw_req as $item)
        {
            $item['status'] = $status[$item->approved];
            $item['requested_at'] = $item->created_at->format('Y-m-d H:i:s');

        if($item->type == 'disbursement'){

            $item['bank_name'] = $item->disbursementMethod ? $item->disbursementMethod->method_name : translate('Account');
        } else {
            $item['bank_name'] = $item->method ? $item->method->method_name : translate('Account');

        }
            $item['detail']=json_decode($item->withdrawal_method_fields,true);

            unset($item['created_at']);
            unset($item['approved']);
            $temp[] = $item;
        }

        return response()->json($temp, 200);
    }

    public function request_withdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'id'=> 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $method = WithdrawalMethod::find($request['id']);
        $fields = array_column($method->method_fields, 'input_name');
        $values = $request->all();

        $method_data = [];
        foreach ($fields as $field) {
            if(key_exists($field, $values)) {
                $method_data[$field] = $values[$field];
            }
        }

        $w = $request['vendor']?->wallet;
        if ($w?->balance >= $request['amount']) {
            $data = [
                'vendor_id' => $w?->vendor_id,
                'amount' => $request['amount'],
                'transaction_note' => null,
                'withdrawal_method_id' => $request['id'],
                'withdrawal_method_fields' => json_encode($method_data),
                'approved' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ];
            try
            {
                DB::table('withdraw_requests')->insert($data);
                $w?->increment('pending_withdraw', $request['amount']);
                $mail_status = Helpers::get_mail_status('withdraw_request_mail_status_admin');
                $admin= \App\Models\Admin::where('role_id', 1)->first();
                $wallet_transaction = WithdrawRequest::where('vendor_id',$w->vendor_id)->latest()->first();
                if($request['vendor']?->stores[0]?->module?->module_type !== 'rental' &&  config('mail.status') && $mail_status == '1'  &&  Helpers::getNotificationStatusData('admin','withdraw_request','mail_status' )) {
                    Mail::to($admin?->getRawOriginal('email'))->send(new WithdrawRequestMail('admin_mail',$wallet_transaction));
                }
                elseif($request['vendor']?->stores[0]?->module?->module_type == 'rental' && addon_published_status('Rental') && config('mail.status') && Helpers::get_mail_status('rental_withdraw_request_mail_status_admin') == '1' &&   Helpers::getRentalNotificationStatusData('admin','provider_withdraw_request','mail_status') ){
                    Mail::to($admin?->getRawOriginal('email'))->send(new ProviderWithdrawRequestMail('pending',$wallet_transaction));
                }
                return response()->json(['message'=>translate('messages.withdraw_request_placed_successfully')],200);
            }
            catch(\Exception $e)
            {
                info($e->getMessage());
                return response()->json($e);
            }
        }
        return response()->json([
            'errors'=>[
                ['code'=>'amount', 'message'=>translate('messages.insufficient_balance')]
            ]
        ],403);
    }

    public function remove_account(Request $request)
    {
        $vendor = $request['vendor'];

        if(Order::where('store_id', $vendor->stores[0]->id)->whereIn('order_status', ['pending','accepted','confirmed','processing','handover','picked_up'])->count())
        {
            return response()->json(['errors'=>[['code'=>'on-going', 'message'=>translate('messages.Please_complete_your_ongoing_and_accepted_orders')]]],203);
        }

        if($vendor->wallet && $vendor->wallet->collected_cash > 0)
        {
            return response()->json(['errors'=>[['code'=>'hand_in_cash', 'message'=>translate('messages.You_have_cash_in_hand,_you_have_to_pay_the_due_to_delete_your_account')]]],203);
        }

        Helpers::check_and_delete('vendor/' , $vendor['image']);


        Helpers::check_and_delete('store/' , $vendor->stores[0]->logo);



        Helpers::check_and_delete('store/cover/' , $vendor->stores[0]->cover_photo);

        foreach($vendor->stores[0]->deliverymen as $dm) {

            Helpers::check_and_delete('delivery-man/' , $dm['image']);


            foreach (json_decode($dm['identity_image'], true) as $img) {
                Helpers::check_and_delete('delivery-man/' , $img);

            }
        }
        $vendor->stores[0]->deliverymen()->delete();
        $vendor->stores()->delete();
        if($vendor->userinfo){
            $vendor->userinfo->delete();
        }
        $vendor->delete();
        return response()->json([]);
    }
    public function edit_order_amount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
            $orderTaxIds=[];
            $additionalCharges=[];
        if($request->order_amount){
            $vendor = $request['vendor'];
            $vendor_store = Helpers::store_data_formatting($vendor->stores[0], false);
            $order = Order::find($request->order_id);
            if ($order->store_id != $vendor_store->id) {
                return response()->json([
                    'errors' => [
                        ['code' => 'order', 'message' => translate('Order not found')]
                    ]
                ], 403);
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



        $discount_on_product_by = $order->discount_on_product_by ?? 'vendor' ;

        $store_discount = Helpers::get_store_discount($store);
        $store_discount =  $store_discount ? $store_discount : ['discount' => 0, 'max_discount' => 0, 'min_purchase' => 0];
        $admin_discount = Helpers::checkAdminDiscount(price: $product_price + $total_addon_price, discount: $store_discount['discount'], max_discount: $store_discount['max_discount'], min_purchase: $store_discount['min_purchase']);

        $discount =$admin_discount;

        if($admin_discount > 0 ){
                $discount_on_product_by =  'admin' ;
            }

        $order->discount_on_product_by= $discount_on_product_by;
        $store_discount_amount=$discount;



        $coupon_discount_amount = $coupon ? CouponLogic::get_discount($coupon, $product_price + $total_addon_price - $store_discount_amount) : 0;


        $total_price = $product_price + $total_addon_price - $store_discount_amount - $coupon_discount_amount;

        $total_price = max($total_price, 0);



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
                $order->dm_tips = $order->dm_tips ?? $request->dm_tips ?? 0;
            }else{
                $order->dm_tips = 0;
            }

            $order->additional_charge =$order->additional_charge;

            if ($additional_charge_status == 1) {
                $order->additional_charge = $additional_charge ?? 0;
            }

                    $taxData =  \Modules\TaxModule\Services\CalculateTaxService::getCalculatedTax(
                    amount: $total_price ,
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
                $coupon->increment('total_uses');
            }

            $order->coupon_discount_amount = round($coupon_discount_amount, config('round_up_to_digit'));
            $order->coupon_discount_title = $coupon ? $coupon->title : '';

            $order->store_discount_amount = round($store_discount_amount, config('round_up_to_digit'));

            $order->order_amount = round($total_price + $order->total_tax_amount + $order->delivery_charge, config('round_up_to_digit'));
            $order->free_delivery_by = $free_delivery_by;
            $order->order_amount = $order->order_amount + $order->dm_tips + $order->additional_charge;
            $order->save();
        }

        if($request->discount_amount){
            $vendor = $request['vendor'];
            $vendor_store = Helpers::store_data_formatting($vendor->stores[0], false);
            $order = Order::find($request->order_id);
            if ($order->store_id != $vendor_store->id) {
                return response()->json([
                    'errors' => [
                        ['code' => 'order', 'message' => translate('Order not found')]
                    ]
                ], 403);
            }
            $order = Order::find($request->order_id);
            $product_price = $order['order_amount'] + $order->store_discount_amount -$order['delivery_charge']-$order['total_tax_amount']-$order['dm_tips'] - $order->additional_charge;


            if($request->discount_amount > $product_price)
            {
                return response()->json([
                    'errors' => [
                        ['code' => 'order', 'message' => translate('messages.discount_amount_is_greater_then_product_amount')]
                    ]
                ], 403);
            }




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
                    amount: $product_price-$request->discount_amount,
                    productIds: [],
                    taxPayer: 'prescription',
                    storeData: true,
                    additionalCharges: $additionalCharges,
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

                $order->discount_on_product_by= 'vendor';
            $order->store_discount_amount = round($request->discount_amount, config('round_up_to_digit'));
            $order->order_amount = $product_price+$order['delivery_charge']+$order['total_tax_amount']+$order['dm_tips'] -$order->store_discount_amount  +$order->additional_charge;
            $order->save();
        }
            $order?->orderTaxes()?->delete();
            if (count($orderTaxIds)) {
                \Modules\TaxModule\Services\CalculateTaxService::updateOrderTaxData(
                    orderId: $order->id,
                    orderTaxIds: $orderTaxIds,
                );
            }

        return response()->json(['message'=>translate('messages.order_updated_successfully')],200);
    }






    public function send_order_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $vendor = $request['vendor'];

        $order = Order::where('id' ,$request->order_id)
        ->whereHas('store.vendor', function($query) use($vendor){
            $query->where('id', $vendor->id);
        })
        ->with('customer')

        ->where(function($query)use($vendor){
            if(config('order_confirmation_model') == 'store' || $vendor->stores[0]->sub_self_delivery)
            {
                $query->whereIn('order_status', ['accepted','pending','confirmed', 'processing', 'handover','picked_up']);
            }
            else
            {
                $query->whereIn('order_status', ['confirmed', 'processing', 'handover','picked_up'])
                ->orWhere(function($query){
                    $query->where('payment_status','paid')->where('order_status', 'accepted');
                })
                ->orWhere(function($query){
                    $query->where('order_status','pending')->where('order_type', 'take_away');
                });
            }
        })
        ->Notpos()
        ->NotDigitalOrder()

        ->first();
        if(!$order)
        {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => translate('messages.not_found')]
                ]
            ], 404);
        }
        $value = translate('your_order_is_ready_to_be_delivered,_plesae_share_your_otp_with_delivery_man.').' '.translate('otp:').$order->otp.', '.translate('order_id:').$order->id;
        try {

            $fcm_token= $order->is_guest == 0 ? $order?->customer?->cm_firebase_token : $order?->guest?->fcm_token;

            if ($value && $fcm_token && Helpers::getNotificationStatusData('customer','customer_delivery_verification','push_notification_status')) {
                $data = [
                    'title' => translate('messages.order_ready_to_be_delivered'),
                    'description' => $value,
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'otp',
                ];
                Helpers::send_push_notif_to_device($fcm_token , $data);
                DB::table('user_notifications')->insert([
                    'data' => json_encode($data),
                    'user_id' => $order->user_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            info($e->getMessage());
            return response()->json(['message' => translate('messages.push_notification_faild')], 403);
        }
        return response()->json([], 200);
    }

    public function update_announcment(Request $request)
    {
        $vendor = $request['vendor']->stores[0];
        $validator = Validator::make($request->all(), [
            'announcement_status' => 'required',
            'announcement_message' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $vendor->announcement = $request->announcement_status;
        $vendor->announcement_message = $request->announcement_message;
        $vendor->save();

        return response()->json(['message' => translate('messages.profile_updated_successfully')], 200);
    }


    Public function make_payment(Request $request){
        $validator = Validator::make($request->all(), [
            'payment_gateway' => 'required',
            'amount' => 'required|numeric|min:.001',
            'callback' => 'required'
        ]);

        $vendor = $request['vendor'];
        $store=  $vendor->stores[0];


        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $store =Store::findOrfail($store->id);

        $payer = new Payer(
            $store->name ,
            $store->email,
            $store->phone,
            ''
        );
        $store_logo= BusinessSetting::where(['key' => 'logo'])->first();
        $additional_data = [
            'business_name' => BusinessSetting::where(['key'=>'business_name'])->first()?->value,
            'business_logo' => \App\CentralLogics\Helpers::get_full_url('business',$store_logo?->value,$store_logo?->storage[0]?->value ?? 'public' )
        ];
        $payment_info = new PaymentInfo(
            success_hook: 'collect_cash_success',
            failure_hook: 'collect_cash_fail',
            currency_code: Helpers::currency_code(),
            payment_method: $request->payment_gateway,
            payment_platform: 'app',
            payer_id: $store->vendor_id,
            receiver_id: '100',
            additional_data:  $additional_data,
            payment_amount: $request->amount ,
            external_redirect_link: $request->has('callback')?$request['callback']:session('callback'),
            attribute: 'store_collect_cash_payments',
            attribute_id: $store->vendor_id,
        );

        $receiver_info = new Receiver('Admin','example.png');
        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        $data = [
            'redirect_link' => $redirect_link,
        ];
        return response()->json($data, 200);

    }


    public function make_wallet_adjustment(Request $request){
        $vendor = $request['vendor'];

        $wallet = StoreWallet::firstOrNew(
            ['vendor_id' =>$vendor->id]
        );

        $wallet_earning =  $wallet->total_earning -($wallet->total_withdrawn + $wallet->pending_withdraw);
        $adj_amount =  $wallet->collected_cash - $wallet_earning;


        if($wallet->collected_cash == 0 || $wallet_earning == 0  || ($wallet_earning  == $wallet->balance ) ){
            return response()->json(['message' => translate('messages.Already_Adjusted')], 201);
        }

        if($adj_amount > 0 ){
            $wallet->total_withdrawn =  $wallet->total_withdrawn + $wallet_earning ;
            $wallet->collected_cash =   $wallet->collected_cash - $wallet_earning ;

            $data = [
                'vendor_id' => $vendor->id,
                'amount' => $wallet_earning,
                'transaction_note' => "Store_wallet_adjustment_partial",
                'withdrawal_method_id' => null,
                'withdrawal_method_fields' => null,
                'approved' => 1,
                'type' => 'adjustment',
                'created_at' => now(),
                'updated_at' => now()
            ];

        } else{
            $data = [
                'vendor_id' => $vendor->id,
                'amount' => $wallet->collected_cash ,
                'transaction_note' => "Store_wallet_adjustment_full",
                'withdrawal_method_id' => null,
                'withdrawal_method_fields' => null,
                'approved' => 1,
                'type' => 'adjustment',
                'created_at' => now(),
                'updated_at' => now()
            ];
            $wallet->total_withdrawn =  $wallet->total_withdrawn + $wallet->collected_cash ;
            $wallet->collected_cash =   0;

        }

        $wallet->save();
        DB::table('withdraw_requests')->insert($data);

        return response()->json(['message' => translate('messages.store_wallet_adjustment_successfull')], 200);
    }

    public function wallet_payment_list(Request $request)
    {
        $limit= $request['limit'] ?? 25;
        $offset = $request['offset'] ?? 1;
        $vendor = $request['vendor'];

        $key = isset($request['search']) ? explode(' ', $request['search']) : [];
        $paginator = AccountTransaction::
        when(isset($key), function ($query) use ($key) {
            return $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('ref', 'like', "%{$value}%");
                }
            });
        })
            ->where('type', 'collected')
            ->where('created_by' , 'store')
            ->where('from_id', $vendor->id)
            ->where('from_type', 'store')
            ->latest()

            ->paginate($limit, ['*'], 'page', $offset);

        $temp= [];

        foreach( $paginator->items() as $item)
        {
            $item['status'] = 'approved';
            $item['payment_time'] = \App\CentralLogics\Helpers::time_date_format($item->created_at);

            $temp[] = $item;
        }
        $data = [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'transactions' => $temp,
        ];

        return response()->json($data, 200);
    }
}
