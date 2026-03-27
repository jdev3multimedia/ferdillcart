<?php

use App\Models\Admin;
use App\Models\Order;
use App\Models\Store;
use App\Models\AdminWallet;
use App\Models\DeliveryMan;
use App\Models\WalletPayment;
use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\DB;
use App\Mail\OrderVerificationMail;
use App\CentralLogics\CustomerLogic;
use App\Models\Module;
use Illuminate\Support\Facades\Mail;
use App\Models\SubscriptionBillingAndRefundHistory;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Rental\Entities\Trips;

if (! function_exists('translate')) {
    function translate($key, $replace = [])
    {
        if(strpos($key, 'validation.') === 0 || strpos($key, 'passwords.') === 0 || strpos($key, 'pagination.') === 0 || strpos($key, 'order_texts.') === 0) {
            return trans($key, $replace);
        }

        $key = strpos($key, 'messages.') === 0?substr($key,9):$key;
        $local = app()->getLocale();
        try {
            $lang_array = include(base_path('resources/lang/' . $local . '/messages.php'));
            $processed_key = ucfirst(str_replace('_', ' ', Helpers::remove_invalid_charcaters($key)));

            if (!array_key_exists($key, $lang_array)) {
                $lang_array[$key] = $processed_key;
                $str = "<?php return " . var_export($lang_array, true) . ";";
                file_put_contents(base_path('resources/lang/' . $local . '/messages.php'), $str);
                $result = $processed_key;
            } else {
                $result = trans('messages.' . $key, $replace);
            }
        } catch (\Exception $exception) {
            info($exception->getMessage());
            $result = trans('messages.' . $key, $replace);
        }

        return $result;
    }
}

if (! function_exists('collect_cash_fail')) {
    function collect_cash_fail($data){
        return 0;
    }
}
if (! function_exists('collect_cash_success')) {
    function collect_cash_success($data){

        try {
            $account_transaction = new AccountTransaction();
            if($data->attribute === 'store_collect_cash_payments'){
                $store = Store::where('vendor_id', $data->attribute_id)->first();
                $store->status = 1;
                $store->save();
                $user_data = $store?->vendor;
                $current_balance = $user_data?->wallet?->collected_cash ?? 0;
                $account_transaction->from_type = 'store';
                $account_transaction->from_id = $store?->vendor?->id;
                $account_transaction->created_by = 'store';
            }
            elseif($data->attribute === 'deliveryman_collect_cash_payments'){
                $user_data = DeliveryMan::findOrFail($data->attribute_id);
                $user_data->status = 1;
                $user_data->save();
                $current_balance = $user_data?->wallet?->collected_cash ?? 0;
                $account_transaction->from_type = 'deliveryman';
                $account_transaction->from_id = $user_data->id;
                $account_transaction->created_by = 'deliveryman';
            }
            else{
                return 0;
            }
            $account_transaction->method = $data->payment_method;
            $account_transaction->ref = $data->attribute;
            $account_transaction->amount = $data->payment_amount;
            $account_transaction->current_balance = $current_balance;

            DB::beginTransaction();
            $account_transaction->save();
            $user_data?->wallet?->decrement('collected_cash', $account_transaction->amount);
            AdminWallet::where('admin_id', Admin::where('role_id', 1)->first()->id)->increment('digital_received',  $account_transaction->amount );

            DB::commit();


        } catch (\Exception $exception) {
            info($exception->getMessage());
            DB::rollBack();

        }


        try {
            if($data->attribute == 'deliveryman_collect_cash_payments' && config('mail.status') &&  Helpers::getNotificationStatusData('deliveryman','deliveryman_collect_cash','mail_status') && Helpers::get_mail_status('cash_collect_mail_status_dm') == 1 ){
                Mail::to($user_data?->getRawOriginal('email'))->send(new \App\Mail\CollectCashMail($account_transaction,$user_data['f_name']));
            }
        } catch (\Exception $exception) {
            info($exception->getMessage());
        }
        return true;
    }
}



if (! function_exists('order_place')) {
    function order_place($data) {
        $order = Order::find($data->attribute_id);
        $order->order_status='confirmed';
        if($order->payment_method != 'partial_payment'){
            $order->payment_method=$data->payment_method;
        }
        // $order->transaction_reference=$data->transaction_ref;
        $order->payment_status='paid';
        $order->confirmed=now();
        $order->save();



        if( $order?->store?->is_valid_subscription == 1 && $order?->store?->store_sub?->max_order != "unlimited" && $order?->store?->store_sub?->max_order > 0){
            $order?->store?->store_sub?->decrement('max_order' , 1);
        }


        OrderLogic::update_unpaid_order_payment(order_id:$order->id, payment_method:$data->payment_method);
        try {
            Helpers::send_order_notification($order);
            $address = json_decode($order->delivery_address, true);


            if(Helpers::getNotificationStatusData('customer','customer_delivery_verification','mail_status')  && Helpers::get_mail_status('order_verification_mail_status_user') == 1 && config('mail.status')){

                if ( config('order_delivery_verification') == 1  && $order->is_guest == 0) {
                    Mail::to($order->customer?->getRawOriginal('email'))->send(new OrderVerificationMail($order->otp,$order->customer->f_name));
                }

                if ($order->is_guest == 1   && isset($address['contact_person_email'])) {
                    Mail::to($address['contact_person_email'])->send(new OrderVerificationMail($order->otp,$order?->customer?->f_name));
                }
            }
        } catch (\Exception $e) {
            info($e);
        }

    }

}

if (! function_exists('trip_payment_success')) {
    function trip_payment_success($data) {
        $trip = Trips::find($data->attribute_id);
        if($trip->payment_method != 'partial_payment'){
            $trip->payment_method=$data->payment_method;
        }
        $trip->transaction_reference=$data->transaction_ref;
        $trip->payment_status='paid';
        $trip->save();

        if( $trip?->provider?->is_valid_subscription == 1 && $trip?->provider?->store_sub?->max_order != "unlimited" && $trip?->provider?->store_sub?->max_order > 0){
            $trip?->provider?->store_sub?->decrement('max_order' , 1);
        }

        if ($trip->trip_status == 'completed' && $trip->payment_status == 'paid' && !$trip->trip_transaction) {
            Helpers::createTransactionForTrip($trip, 'admin');
        }
        Helpers::sendTripPaymentNotificationCustomerMain($trip);
        OrderLogic::update_unpaid_trip_payment(trip_id:$trip->id, payment_method:$data->payment_method);
    }

}



if (! function_exists('trip_payment_fail')) {
    function trip_payment_fail($data) {
        $trip = Trips::find($data->attribute_id);
        $trip->trip_status='payment_failed';
        if($trip->payment_method != 'partial_payment'){
            $trip->payment_method=$data->payment_method;
        }
        $trip->payment_failed=now();
        $trip->save();
        return true;
    }
}



if (! function_exists('order_failed')) {
    function order_failed($data) {
        $order = Order::find($data->attribute_id);
        $order->order_status='failed';
        if($order->payment_method != 'partial_payment'){
            $order->payment_method=$data->payment_method;
        }
        $order->failed=now();
        $order->save();
    }
}

if (! function_exists('wallet_success')) {
    function wallet_success($data) {
        $order = WalletPayment::find($data->attribute_id);
        $order->payment_method=$data->payment_method;
        // $order->transaction_reference=$data->transaction_ref;
        $order->payment_status='success';
        $order->save();
        $wallet_transaction = CustomerLogic::create_wallet_transaction($data->payer_id, $data->payment_amount, 'add_fund',$data->payment_method);
        if($wallet_transaction)
        {
            try{
                Helpers::add_fund_push_notification($data->payer_id);
                if(config('mail.status') && Helpers::get_mail_status('add_fund_mail_status_user') == '1' &&  Helpers::getNotificationStatusData('customer','customer_add_fund_to_wallet','mail_status')) {
                    Mail::to($wallet_transaction->user?->getRawOriginal('email'))->send(new \App\Mail\AddFundToWallet($wallet_transaction));
                }
            }catch(\Exception $ex)
            {
                info($ex->getMessage());
            }
        }
    }
}

if (! function_exists('wallet_success')) {
    function wallet_failed($data) {
        $order = WalletPayment::find($data->attribute_id);
        $order->payment_status='failed';
        $order->payment_method=$data->payment_method;
        $order->save();
    }
}

if (!function_exists('addon_published_status')) {
    function addon_published_status($module_name): int
    {
        try {
            $path = base_path("Modules/{$module_name}/Addon/info.php");

            if (!file_exists($path)) {
                return 0;
            }

            $full_data = include $path;

            return (isset($full_data['is_published']) && $full_data['is_published'] == 1) ? 1 : 0;

        } catch (\Throwable $exception) {
            info($exception->getMessage());
            return 0;
        }
    }
}



if (!function_exists('config_settings')) {
    function config_settings($key, $settings_type)
    {
        try {
            $config = DB::table('addon_settings')->where('key_name', $key)
                ->where('settings_type', $settings_type)->first();
        } catch (Exception $exception) {
            return null;
        }
        return (isset($config)) ? $config : null;
    }


    if (! function_exists('sub_success')) {
        function sub_success($data){
            $type='renew';
            if($data->attribute == 'store_subscription_payment'){
                $type='new_plan';
                }
                elseif($data->attribute == 'store_subscription_new_join'){
                    $type='new_join';
                }

                $pending_bill= SubscriptionBillingAndRefundHistory::where(['store_id'=>$data->payer_id,
                'transaction_type'=>'pending_bill', 'is_success' =>0])?->sum('amount')?? 0;
                Helpers::subscription_plan_chosen(store_id:$data->payer_id,package_id:$data->attribute_id,payment_method:$data->payment_method,discount:0,pending_bill:$pending_bill,reference:$data->attribute,type: $type);
                if($type !== 'new_join'){
                    Toastr::success(  $type == 'renew' ?  translate('Subscription_Package_Renewed_Successfully.'): translate('Subscription_Package_Shifted_Successfully.')  );
                }

            return true;
        }
    }

    if (! function_exists('sub_fail')) {
        function sub_fail($data){
            return true;
        }
    }
    if (! function_exists('getEnvMode')) {
         function getEnvMode()
            {
                $configKey='envAppMode_conf';
                if (Config::has($configKey)) {
                    $data = Config::get($configKey);
                } else {
                    $data = env('APP_MODE')??'demo';
                    Config::set($configKey, $data);
                }
                return $data;
            }
    }


    if (! function_exists('getModuleId')) {
         function getModuleId($value)
            {
                $module = is_numeric($value)
                ? Module::where('id', $value)->first()
                : Module::where('slug', $value)->first();
                return $module?->id;
            }
    }



}
