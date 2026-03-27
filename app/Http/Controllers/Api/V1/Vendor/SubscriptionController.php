<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Models\Store;
use App\Models\Module;
use App\Models\StoreWallet;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Mail\SubscriptionCancel;
use App\Models\StoreSubscription;
use Illuminate\Support\Facades\DB;
use App\Models\SubscriptionPackage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\SubscriptionTransaction;
use Illuminate\Support\Facades\Validator;
use App\Models\SubscriptionBillingAndRefundHistory;
use Modules\Rental\Emails\ProviderSubscriptionCancel;

class SubscriptionController extends Controller
{
    public function package_view(Request $request)
    {
        $module = Module::whereId($request->module_id)->first();
        $packages = SubscriptionPackage::where('status', 1)
            ->where('module_type', $module?->module_type == 'rental' && addon_published_status('Rental') ? 'rental' : 'all')
            ->latest()->get();
        return response()->json(['packages' => $packages], 200);
    }
    public function business_plan(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'store_id' => 'required',
            'payment' => 'nullable',
            'business_plan' => 'required|in:subscription,commission',
            'package_id' => 'nullable|required_if:business_plan,subscription',
            'payment_gateway' => 'nullable|required_if:business_plan,subscription',
            'payment_platform' => 'nullable|in:app,web'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $store = Store::Where('id', $request->store_id)->first();
        if ($request->business_plan == 'subscription' && $request->package_id != null) {
            $package = SubscriptionPackage::withoutGlobalScope('translate')->find($request->package_id);
            $pending_bill = SubscriptionBillingAndRefundHistory::where([
                'store_id' => $store->id,
                'transaction_type' => 'pending_bill',
                'is_success' => 0
            ])?->sum('amount') ?? 0;
            if (!in_array($request->payment_gateway, ['wallet', 'free_trial'])) {
                $url = $request->has('callback') ? $request['callback'] : session('callback');
                $data = [
                    'redirect_link' => Helpers::subscriptionPayment(store_id: $store->id, package_id: $package->id, payment_gateway: $request->payment_gateway, payment_platform: $request->payment_platform ?? 'web', url: $url, pending_bill: $pending_bill, type: $request?->type),
                ];

                return response()->json($data, 200);
            }

            if ($request->payment_gateway == 'wallet') {
                $wallet = StoreWallet::firstOrNew(['vendor_id' => $store->vendor_id]);
                $balance = BusinessSetting::where('key', 'wallet_status')->first()?->value == 1 ? $wallet?->balance ?? 0 : 0;

                if ($balance > $package?->price) {
                    $reference = 'wallet_payment_by_vendor';
                    $plan_data =   Helpers::subscription_plan_chosen(store_id: $store->id, package_id: $package->id, payment_method: 'wallet', discount: 0, pending_bill: $pending_bill, reference: $reference, type: $request?->type);
                    if ($plan_data != false) {
                        $wallet->total_withdrawn = $wallet?->total_withdrawn + $package->price;
                        $wallet?->save();
                    }
                } else {
                    return response()->json([
                        'errors' => ['message' => translate('messages.Insufficient_balance_in_wallet')]
                    ], 403);
                }
            }

            if ($request->payment_gateway == 'free_trial') {
                $plan_data =   Helpers::subscription_plan_chosen(store_id: $store->id, package_id: $package->id, payment_method: 'free_trial', discount: 0, pending_bill: $pending_bill, reference: 'free_trial', type: 'new_join');
            }

            $data = [
                'store_business_model' => 'subscription',
                'logo' => $store->logo,
                'message' => translate('messages.application_placed_successfully')
            ];
            return response()->json($data, 200);
        } elseif ($request->business_plan == 'commission') {
            $store->store_business_model = 'commission';
            $store->save();
            StoreSubscription::where(['store_id' => $store->id])->update([
                'status' => 0,
            ]);
            $data = [
                'store_business_model' => 'commission',
                'logo' => $store->logo,
                'message' => translate('messages.application_placed_successfully')
            ];
            return response()->json($data, 200);
        }

        return response()->json([], 403);
    }



    public function transaction(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
            'from' => 'required',
            'to' => 'required',
        ]);

        $key = explode(' ', $request['search']);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $limit = $request['limite'] ?? 25;
        $offset = $request['offset'] ?? 1;
        $from = $request->from;
        $to = $request->to;
        $store_id = $request->vendor->stores[0]->id;

        $transactions =  SubscriptionTransaction::where('store_id', $store_id)->latest()
            ->with('store:id,name', 'package:id,package_name')
            ->when(isset($key), function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->Where('id', 'like', "%{$value}%");
                    }
                    $q->orWhereHas('store', function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->where('name', 'like', "%{$value}%");
                        }
                    });
                });
            })
            ->when(isset($from) &&  isset($to), function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:29']);
            })

            ->paginate($limit, ['*'], 'page', $offset);

        $data = [
            'total_size' => $transactions->total(),
            'limit' => $limit,
            'offset' => $offset,
            'transactions' => $transactions->items()
        ];
        return response()->json($data, 200);
    }

    public function cancelSubscription(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'store_id' => 'required',
            'subscription_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        StoreSubscription::where(['id' => $request->subscription_id, 'store_id' => $request->store_id])->update([
            'is_canceled' => 1,
            'canceled_by' => 'store',
        ]);

        try {
            $store = Store::where('id', $request->store_id)->first();
            if($store?->module?->module_type == 'rental' && addon_published_status('Rental')){
                if( Helpers::getRentalNotificationStatusData('provider','provider_subscription_cancel','push_notification_status',$store->id)  &&  $store?->vendor?->firebase_token){
                    $data = [
                        'title' => translate('subscription_canceled'),
                        'description' => translate('Your_subscription_has_been_canceled'),
                        'order_id' => '',
                        'image' => '',
                        'type' => 'subscription',
                        'order_status' => '',
                    ];
                    Helpers::send_push_notif_to_device($store?->vendor?->firebase_token, $data);
                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'vendor_id' => $store?->vendor_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                if (config('mail.status') && Helpers::get_mail_status('rental_subscription_cancel_mail_status_provider') == '1' &&  Helpers::getRentalNotificationStatusData('provider','provider_subscription_cancel','mail_status' ,$store?->id)) {
                    Mail::to($store?->getRawOriginal('email'))->send(new ProviderSubscriptionCancel($store->name));
                }
            } else{
                if (Helpers::getNotificationStatusData('store', 'store_subscription_cancel', 'push_notification_status', $store->id)  &&  $store?->vendor?->firebase_token) {
                    $data = [
                        'title' => translate('subscription_canceled'),
                        'description' => translate('Your_subscription_has_been_canceled'),
                        'order_id' => '',
                        'image' => '',
                        'type' => 'subscription',
                        'order_status' => '',
                    ];
                    Helpers::send_push_notif_to_device($store?->vendor?->firebase_token, $data);
                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'vendor_id' => $store?->vendor_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                if (config('mail.status') && Helpers::get_mail_status('subscription_cancel_mail_status_store') == '1' && Helpers::getNotificationStatusData('store', 'store_subscription_cancel', 'mail_status', $store?->id)) {
                    Mail::to($store?->getRawOriginal('email'))->send(new SubscriptionCancel($store->name));
                }
            }
        } catch (\Exception $ex) {
            info($ex->getMessage());
        }

        return response()->json(['success'], 200);
    }

    public function checkProductLimits(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'store_id' => 'required',
            'package_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $disable_item_count = 0;
        if (data_get(Helpers::subscriptionConditionsCheck(store_id: $request->store_id, package_id: $request->package_id), 'disable_item_count') > 0) {
            $disable_item_count = (int) (data_get(Helpers::subscriptionConditionsCheck(store_id: $request->store_id, package_id: $request->package_id), 'disable_item_count', 0));
        }

        $store = Store::where('id', $request->store_id)->with('store_sub_update_application')->first();
        $store_subscription = $store->store_sub_update_application;
        $cash_backs = [];

        if ($store->store_business_model == 'subscription' &&  $store_subscription->status == 1 && $store_subscription->is_canceled == 0 && $store_subscription->is_trial == 0  && $store_subscription->package_id !=  $request->package_id) {
            $cash_backs = Helpers::calculateSubscriptionRefundAmount(store: $store, return_data: true);
        }

        return  response()->json([
            'disable_item_count' => $disable_item_count,
            'back_amount' => (float)data_get($cash_backs, 'back_amount', 0),
            'days' => (int) data_get($cash_backs, 'days', 0)
        ], 200);
    }
}
