<?php

namespace App\CentralLogics;

use App\Models\User;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Store;
use App\Models\Vendor;
use App\Models\AdminWallet;
use App\Models\DeliveryMan;
use App\Models\StoreWallet;
use Illuminate\Support\Str;
use App\Models\OrderPayment;
use App\Models\BusinessSetting;
use App\Models\OrderTransaction;
use App\Models\DeliveryManWallet;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\DB;
use App\CentralLogics\CustomerLogic;
use App\Models\ParcelPenaltyFee;
use App\Models\DeliverymanReferralHistory;
use App\Models\ParcelReturnFees;
use App\Models\ParcelCancellation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Modules\Rental\Entities\PartialPayment;

class OrderLogic
{
    public static function gen_unique_id()
    {
        return rand(1000, 9999) . '-' . Str::random(5) . '-' . time();
    }

    public static function track_order($order_id)
    {
        return Helpers::order_data_formatting(Order::with(['details', 'delivery_man.rating'])->where(['id' => $order_id])->first(), false);
    }

    public static function updated_order_calculation($order)
    {
        return true;
    }
    public static function create_transaction($order, $received_by = false, $status = null)
    {
        $type = $order->order_type;
        $dm_tips_manage_status = BusinessSetting::where('key', 'dm_tips_status')->first()->value;
        $admin_subsidy = 0;
        $amount_admin = 0;
        $store_d_amount = 0;
        $admin_coupon_discount_subsidy = 0;
        $store_subsidy = 0;
        $store_coupon_discount_subsidy = 0;
        $store_discount_amount = 0;
        $flash_admin_discount_amount = 0;
        $flash_store_discount_amount = 0;
        $comission_on_store_amount = 0;
        $ref_bonus_amount = 0;
        $subscription_mode = 0;
        $commission_percentage = 0;
        $store_amount = 0;

        $store = $order?->store;
        $store_sub = $order?->store?->store_sub;
        // free delivery by admin
        if ($order->free_delivery_by == 'admin') {
            $admin_subsidy = $order->original_delivery_charge;
            Helpers::expenseCreate(amount: $order->original_delivery_charge, type: 'free_delivery', datetime: now(), created_by: $order->free_delivery_by, order_id: $order->id);
        }
        // free delivery by store
        if ($order->free_delivery_by == 'vendor') {
            $store_subsidy = $order->original_delivery_charge;
            Helpers::expenseCreate(amount: $order->original_delivery_charge, type: 'free_delivery', datetime: now(), created_by: $order->free_delivery_by, order_id: $order->id, store_id: $order->store->id);
        }
        // coupon discount by Admin
        if ($order->coupon_created_by == 'admin') {
            $admin_coupon_discount_subsidy = $order->coupon_discount_amount;
            Helpers::expenseCreate(amount: $admin_coupon_discount_subsidy, type: 'coupon_discount', datetime: now(), created_by: $order->coupon_created_by, order_id: $order->id);
        }
        // 1st order discount by Admin
        if ($order->ref_bonus_amount > 0) {
            $ref_bonus_amount = $order->ref_bonus_amount;
            Helpers::expenseCreate(amount: $ref_bonus_amount, type: 'referral_discount', datetime: now(), created_by: 'admin', order_id: $order->id);
        }
        // coupon discount by store
        if ($order->coupon_created_by == 'vendor') {
            $store_coupon_discount_subsidy = $order->coupon_discount_amount;
            Helpers::expenseCreate(amount: $store_coupon_discount_subsidy, type: 'coupon_discount', datetime: now(), created_by: $order->coupon_created_by, order_id: $order->id, store_id: $order->store->id);
        }

        if ($order?->cashback_history) {
            self::cashbackToWallet($order);
        }

        if ($type == 'parcel') {
            $comission = \App\Models\BusinessSetting::where('key', 'parcel_commission_dm')->first();
            $dm_tips = $dm_tips_manage_status ? $order->dm_tips : 0;
            $comission = isset($comission) ? $comission->value : 0;
            $order_amount = $order->order_amount - $dm_tips - $order->additional_charge - $order->extra_packaging_amount - $order->total_tax_amount;
            $dm_commission = $comission ? ($order_amount / 100) * $comission : 0;
            $comission_amount = $order_amount - $dm_commission;
        } else {
            $comission = isset($order->store->comission) == null ? \App\Models\BusinessSetting::where('key', 'admin_commission')->first()->value : $order->store->comission;
            $dm_tips = $dm_tips_manage_status ? $order->dm_tips : 0;
            // $order_amount = $order->order_amount - $order->delivery_charge - $order->total_tax_amount - $dm_tips;

            if ($order->store_discount_amount > 0  && $order->discount_on_product_by == 'vendor') {
                if ($store->store_business_model == 'subscription' && isset($store_sub)) {
                    $store_d_amount =  $order->store_discount_amount;
                    Helpers::expenseCreate(amount: $store_d_amount, type: 'discount_on_product', datetime: now(), created_by: 'vendor', order_id: $order->id, store_id: $order->store->id);
                } else {
                    $amount_admin = $comission ? ($order->store_discount_amount / 100) * $comission : 0;
                    $store_d_amount =  $order->store_discount_amount - $amount_admin;
                    Helpers::expenseCreate(amount: $store_d_amount, type: 'discount_on_product', datetime: now(), created_by: 'vendor', order_id: $order->id, store_id: $order->store->id);
                    Helpers::expenseCreate(amount: $amount_admin, type: 'discount_on_product', datetime: now(), created_by: 'admin', order_id: $order->id);
                }
            }

            if ($order->store_discount_amount > 0  && $order->discount_on_product_by == 'admin') {
                $store_discount_amount = $order->store_discount_amount;
                Helpers::expenseCreate(amount: $store_discount_amount, type: 'discount_on_product', datetime: now(), created_by: 'admin', order_id: $order->id);
            }

            if ($order->flash_admin_discount_amount > 0) {
                $flash_admin_discount_amount = $order->flash_admin_discount_amount;
                Helpers::expenseCreate(amount: $flash_admin_discount_amount, type: 'flash_sale_discount', datetime: now(), created_by: 'admin', order_id: $order->id);
            }

            if ($order->flash_store_discount_amount > 0) {
                $flash_store_discount_amount = $order->flash_store_discount_amount;
                Helpers::expenseCreate(amount: $flash_store_discount_amount, type: 'flash_sale_discount', datetime: now(), created_by: 'vendor', order_id: $order->id, store_id: $order->store->id);
            }


            $order_amount = $order->order_amount - $order->additional_charge - $order->extra_packaging_amount - $order->delivery_charge - $order->total_tax_amount - $dm_tips + $flash_admin_discount_amount + $order->coupon_discount_amount + $store_discount_amount + $flash_store_discount_amount + $ref_bonus_amount;
            // comission in delivery charge
            $delivery_charge_comission = BusinessSetting::where('key', 'delivery_charge_comission')->first();
            $delivery_charge_comission_percentage = $delivery_charge_comission ? $delivery_charge_comission->value : 0;
            $comission_on_delivery = $delivery_charge_comission_percentage * ($order->original_delivery_charge / 100);

            if ($order->store->sub_self_delivery) {
                $comission_on_actual_delivery_fee = 0;
            } else {

                $comission_on_actual_delivery_fee = ($order->delivery_charge > 0) ? $comission_on_delivery : 0;
            }

            if ($order->free_delivery_by == 'admin') {
                if ($order->store->sub_self_delivery) {
                    $comission_on_actual_delivery_fee = 0;
                    $store_amount = $order->original_delivery_charge ?? 0;
                } else {
                    $comission_on_actual_delivery_fee = ($order->original_delivery_charge > 0) ? $comission_on_delivery : 0;
                }
            }

            //final comission
            if ($store->store_business_model == 'subscription' && isset($store_sub)) {
                $comission_on_store_amount = 0;
                $subscription_mode = 1;
                $commission_percentage = 0;
            } else {
                $comission_on_store_amount = ($comission ? ($order_amount / 100) * $comission : 0);
                $subscription_mode = 0;
                $commission_percentage = $comission;
            }

            $comission_amount = $comission_on_store_amount + $comission_on_actual_delivery_fee;
            $dm_commission = $order->original_delivery_charge - $comission_on_actual_delivery_fee;
        }
        $store_amount = $store_amount + $order_amount + $order->total_tax_amount + $order->extra_packaging_amount - $comission_on_store_amount - $store_coupon_discount_subsidy - $flash_store_discount_amount;
        try {
            OrderTransaction::insert([
                'vendor_id' => $type == 'parcel' ? null : $order->store->vendor->id,
                'delivery_man_id' => $order->delivery_man_id,
                'order_id' => $order->id,
                'order_amount' => $order->order_amount,
                'store_amount' => $type == 'parcel' ? 0 : $store_amount,
                // 'store_amount'=>$type=='parcel' ? 0 : $order_amount + $order->total_tax_amount - $comission_on_store_amount,
                'admin_commission' => $comission_amount + $order->additional_charge - $admin_subsidy - $admin_coupon_discount_subsidy - $ref_bonus_amount - $store_discount_amount,
                'delivery_charge' => $order->delivery_charge,
                'original_delivery_charge' => $dm_commission,
                'tax' => $order->total_tax_amount,
                'received_by' => $received_by ? $received_by : 'admin',
                'zone_id' => $order->zone_id,
                'module_id' => $order->module_id,
                'admin_expense' => $admin_subsidy + $admin_coupon_discount_subsidy + $store_discount_amount + $flash_admin_discount_amount + $amount_admin + $ref_bonus_amount,
                'store_expense' => $store_subsidy + $store_coupon_discount_subsidy + $flash_store_discount_amount,
                'status' => $status,
                'dm_tips' => $dm_tips,
                'created_at' => now(),
                'updated_at' => now(),
                'delivery_fee_comission' => isset($comission_on_actual_delivery_fee) ? $comission_on_actual_delivery_fee : 0,
                'discount_amount_by_store' => $store_coupon_discount_subsidy + $store_d_amount + $store_subsidy,
                'additional_charge' => $order->additional_charge,
                'extra_packaging_amount' => $order->extra_packaging_amount,
                'ref_bonus_amount' => $order->ref_bonus_amount,
                // for store business model
                'is_subscribed' => $subscription_mode,
                'commission_percentage' => $commission_percentage,
            ]);
            $adminWallet = AdminWallet::firstOrNew(
                ['admin_id' => Admin::where('role_id', 1)->first()->id]
            );

            $adminWallet->total_commission_earning = $adminWallet->total_commission_earning + $comission_amount + $order->additional_charge - $admin_subsidy - $admin_coupon_discount_subsidy - $store_discount_amount - $flash_admin_discount_amount - $ref_bonus_amount;

            if ($type != 'parcel') {
                $vendorWallet = StoreWallet::firstOrNew(
                    ['vendor_id' => $order->store->vendor->id]
                );
                if ($order->store->sub_self_delivery) {
                    $vendorWallet->total_earning = $vendorWallet->total_earning + $order->delivery_charge + $dm_tips;
                } else {
                    $adminWallet->delivery_charge = $adminWallet->delivery_charge + $order->delivery_charge;
                }
                // $vendorWallet->total_earning = $vendorWallet->total_earning+($order_amount + $order->total_tax_amount - $comission_on_store_amount);
                $vendorWallet->total_earning = $vendorWallet->total_earning + $store_amount;
            }
            if ($order->delivery_man && ($type == 'parcel' || ($order->store && !$order->store->sub_self_delivery))) {
                $dmWallet = DeliveryManWallet::firstOrNew(
                    ['delivery_man_id' => $order->delivery_man_id]
                );
                if ($order->delivery_man->earning == 1) {
                    $dmWallet->total_earning = $dmWallet->total_earning + $dm_commission + $dm_tips;
                } else {
                    $adminWallet->total_commission_earning = $adminWallet->total_commission_earning + $dm_commission + $dm_tips;
                }
            } else {
                $adminWallet->total_commission_earning = $adminWallet->total_commission_earning + $dm_commission + $dm_tips;
            }

            try {
                DB::beginTransaction();
                $unpaid_payment = OrderPayment::where('payment_status', 'unpaid')->where('order_id', $order->id)->first()?->payment_method;
                $unpaid_pay_method = 'digital_payment';
                if ($unpaid_payment) {
                    $unpaid_pay_method = $unpaid_payment;
                }
                if ($received_by == 'admin') {
                    $adminWallet->digital_received = $adminWallet->digital_received + ($order->order_amount - $order->partially_paid_amount);
                } else if ($received_by == 'store' && $type != 'parcel' && ($order->payment_method == "cash_on_delivery" || $unpaid_pay_method == 'cash_on_delivery')) {
                    $store_over_flow =  true;
                    $vendorWallet->collected_cash = $vendorWallet->collected_cash + ($order->order_amount - $order->partially_paid_amount);
                } else if ($received_by == false) {
                    $adminWallet->manual_received = $adminWallet->manual_received + ($order->order_amount - $order->partially_paid_amount);
                } else if ($received_by == 'deliveryman' && $order->delivery_man && $order->delivery_man->type == 'zone_wise') {
                    $dmWallet->collected_cash = $dmWallet->collected_cash + ($order->order_amount - $order->partially_paid_amount);
                    $dm_over_flow =  true;
                }

                $adminWallet->save();
                if ($type != 'parcel') {
                    $vendorWallet->save();
                }
                if (isset($dmWallet)) {
                    $dmWallet->save();
                }


                if (isset($store_over_flow)) {
                    self::create_account_transaction_for_collect_cash(old_collected_cash: $vendorWallet->collected_cash, from_type: 'store', from_id: $order->store->vendor->id, amount: $order->order_amount - $order->partially_paid_amount, order_id: $order->id);
                }
                if (isset($dm_over_flow)) {
                    self::create_account_transaction_for_collect_cash(old_collected_cash: $dmWallet->collected_cash, from_type: 'deliveryman', from_id: $order->delivery_man_id, amount: $order->order_amount - $order->partially_paid_amount, order_id: $order->id);
                }

                self::update_unpaid_order_payment(order_id: $order->id, payment_method: $order->payment_method);

                DB::commit();

                if($order->delivery_man_id && $order->delivery_man->earning == 1){
                   $deliveryMan = $order->delivery_man;
                   if($deliveryMan->ref_by &&  $deliveryMan->orders()->whereIn('order_status', ['delivered'])->count() == 0 ){
                    self::deliverymanReferalTransaction(deliveryManId: $order->delivery_man_id, referType:'referrerBonus', reference: $order->id, referrerId : $deliveryMan->ref_by);
                    self::deliverymanReferalTransaction(deliveryManId: $deliveryMan->ref_by, referType:'referral', reference: $order->id, referrerId : $order->delivery_man_id);
                  }
                }
                if ($order->is_guest  == 0) {
                    $ref_status = BusinessSetting::where('key', 'ref_earning_status')->first()->value;
                    if (isset($order->customer->ref_by) && $order->customer->order_count == 0  && $ref_status == 1) {
                        $ref_code_exchange_amt = BusinessSetting::where('key', 'ref_earning_exchange_rate')->first()->value;
                        $referar_user = User::where('id', $order->customer->ref_by)->first();
                        $refer_wallet_transaction = CustomerLogic::create_wallet_transaction($referar_user->id, $ref_code_exchange_amt, 'referrer', $order->customer->phone);

                        $notification_data = [
                            'title' => translate('messages.Congratulation'),
                            'description' => translate('You have received') . ' ' . Helpers::format_currency($ref_code_exchange_amt) . ' ' . translate('in your wallet as') . ' ' . $order?->customer?->f_name . ' ' . $order?->customer?->l_name . ' ' . translate('you referred completed thier first order'),
                            'order_id' => 1,
                            'image' => '',
                            'type' => 'referral_code',
                        ];

                        if (Helpers::getNotificationStatusData('customer', 'customer_referral_bonus_earning', 'push_notification_status') && $referar_user?->cm_firebase_token) {
                            Helpers::send_push_notif_to_device($referar_user?->cm_firebase_token, $notification_data);
                            DB::table('user_notifications')->insert([
                                'data' => json_encode($notification_data),
                                'user_id' => $referar_user?->id,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }


                        try {
                            Helpers::add_fund_push_notification($referar_user->id);
                            if (config('mail.status') && Helpers::get_mail_status('add_fund_mail_status_user') == '1' && Helpers::getNotificationStatusData('customer', 'customer_add_fund_to_wallet', 'mail_status')) {
                                Mail::to($referar_user?->getRawOriginal('email'))->send(new \App\Mail\AddFundToWallet($refer_wallet_transaction));
                            }
                        } catch (\Exception $ex) {
                            info($ex->getMessage());
                        }
                    }

                    $create_loyalty_point_transaction = CustomerLogic::create_loyalty_point_transaction($order->user_id, $order->id, $order->order_amount, 'order_place');
                    if ($create_loyalty_point_transaction > 0) {
                        $notification_data = [
                            'title' => translate('messages.Congratulation'),
                            'description' => translate('You_have_received') . ' ' . $create_loyalty_point_transaction . ' ' . translate('points_as_loyalty_point'),
                            'order_id' => $order->id,
                            'image' => '',
                            'type' => 'loyalty_point',
                        ];

                        if (Helpers::getNotificationStatusData('customer', 'customer_loyalty_point_earning', 'push_notification_status') && $order->customer?->cm_firebase_token) {
                            Helpers::send_push_notif_to_device($order->customer?->cm_firebase_token, $notification_data);
                            DB::table('user_notifications')->insert([
                                'data' => json_encode($notification_data),
                                'user_id' => $order->user_id,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                DB::rollBack();
                info($e->getMessage());
                return false;
            }
        } catch (\Exception $e) {
            info($e->getMessage());
            return false;
        }

        return true;
    }

    public static function create_transaction_parcel_cancel($order, $received_by = false)
    {
        $dm_tips_manage_status = BusinessSetting::where('key', 'dm_tips_status')->first()->value;
        $admin_subsidy = 0;
        $admin_coupon_discount_subsidy = 0;
        $store_discount_amount = 0;
        $flash_admin_discount_amount = 0;
        $ref_bonus_amount = 0;

        $return_fee = $order?->parcelCancellation?->return_fee ?? 0;
        // free delivery by admin
        if ($order->free_delivery_by == 'admin') {
            $admin_subsidy = $order->original_delivery_charge;
            Helpers::expenseCreate(amount: $order->original_delivery_charge, type: 'free_delivery', datetime: now(), created_by: $order->free_delivery_by, order_id: $order->id);
        }

        // coupon discount by Admin
        if ($order->coupon_created_by == 'admin') {
            $admin_coupon_discount_subsidy = $order->coupon_discount_amount;
            Helpers::expenseCreate(amount: $admin_coupon_discount_subsidy, type: 'coupon_discount', datetime: now(), created_by: $order->coupon_created_by, order_id: $order->id);
        }

        $comission = \App\Models\BusinessSetting::where('key', 'parcel_commission_dm')->first();
        $dm_tips = $dm_tips_manage_status ? $order->dm_tips : 0;
        $comission = isset($comission) ? $comission->value : 0;
        $order_amount = $order->order_amount - $dm_tips - $order->additional_charge - $order->total_tax_amount;

        $dm_commission = $comission ? ($order_amount / 100) * $comission : 0;
        $comission_amount = $order_amount - $dm_commission;


        DB::beginTransaction();

        $order->order_status = 'returned';
        $order->payment_status = 'paid';
        $order->save();

        $order->parcelCancellation->return_fee_payment_status = 'paid';
        $order->parcelCancellation->save();


        try {

            $adminWallet = AdminWallet::firstOrNew(
                ['admin_id' => Admin::where('role_id', 1)->first()->id]
            );

            $adminWallet->total_commission_earning = $adminWallet->total_commission_earning + $comission_amount + $order->additional_charge - $admin_subsidy - $admin_coupon_discount_subsidy - $store_discount_amount - $flash_admin_discount_amount - $ref_bonus_amount;


            if ($order->delivery_man) {
                $dmWallet = DeliveryManWallet::firstOrNew(
                    ['delivery_man_id' => $order->delivery_man_id]
                );
                if ($order->delivery_man->earning == 1) {
                    $dmWallet->total_earning = $dmWallet->total_earning + $dm_commission + $dm_tips + $return_fee;
                    $dmWallet->collected_cash = $dmWallet->collected_cash +$return_fee;
                    self::createReturnFeeLog($order, $return_fee);
                } else {
                    $adminWallet->total_commission_earning = $adminWallet->total_commission_earning + $dm_commission + $dm_tips + $return_fee;
                }
            } else {
                $adminWallet->total_commission_earning = $adminWallet->total_commission_earning + $dm_commission + $dm_tips + $return_fee;
            }

            if ($received_by == 'admin') {
                $adminWallet->digital_received = $adminWallet->digital_received + ($order->order_amount - $order->partially_paid_amount);
            } else if ($received_by == false) {
                $adminWallet->manual_received = $adminWallet->manual_received + ($order->order_amount - $order->partially_paid_amount);
            } else if ($received_by == 'deliveryman' && $order->delivery_man && $order->delivery_man->type == 'zone_wise') {
                $dmWallet->collected_cash = $dmWallet->collected_cash + ($order->order_amount - $order->partially_paid_amount);
                $dm_over_flow =  true;
            }

            $adminWallet->save();

              OrderTransaction::insert([
                'vendor_id' => null,
                'delivery_man_id' => $order->delivery_man_id,
                'order_id' => $order->id,
                'order_amount' => $order->order_amount,
                'store_amount' => 0,
                'admin_commission' => $comission_amount + $order->additional_charge - $admin_subsidy - $admin_coupon_discount_subsidy - $ref_bonus_amount - $store_discount_amount,
                'delivery_charge' => $order->delivery_charge,
                'original_delivery_charge' => $dm_commission,
                'tax' => $order->total_tax_amount,
                'received_by' => $received_by ? $received_by : 'admin',
                'zone_id' => $order->zone_id,
                'module_id' => $order->module_id,
                'admin_expense' => $admin_subsidy + $admin_coupon_discount_subsidy + $store_discount_amount + $flash_admin_discount_amount  + $ref_bonus_amount,
                'store_expense' => 0,
                'status' => null,
                'dm_tips' => $dm_tips,
                'created_at' => now(),
                'updated_at' => now(),
                'delivery_fee_comission' => 0,
                'discount_amount_by_store' => 0,
                'additional_charge' => $order->additional_charge,
                'extra_packaging_amount' => $order->extra_packaging_amount ?? 0,
                'ref_bonus_amount' => $order->ref_bonus_amount ?? 0,
                // for store business model
                'is_subscribed' => 0,
                'commission_percentage' => $comission,
            ]);



            if($order->parcelCancellation->return_date){
                $returnDate = Carbon::parse($order->parcelCancellation->return_date);
                if ($returnDate->isPast() && isset($dmWallet)) {
                     $dmWallet->collected_cash = $dmWallet->collected_cash + $order->parcelCancellation->dm_penalty_fee??0;
                     self::createParcelPenaltyLog($order, $order->parcelCancellation->dm_penalty_fee??0);
                }
            }

            if (isset($dmWallet)) {
                $dmWallet->save();
            }



            if (isset($dm_over_flow)) {
                self::create_account_transaction_for_collect_cash(old_collected_cash: $dmWallet->collected_cash, from_type: 'deliveryman', from_id: $order->delivery_man_id, amount: $order->order_amount - $order->partially_paid_amount, order_id: $order->id);
            }

            self::update_unpaid_order_payment(order_id: $order->id, payment_method: $order->payment_method);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            info($e->getMessage());
            return false;
        }

        return true;
    }

    public static function refund_before_delivered($order)
    {
        $adminWallet = AdminWallet::firstOrNew(
            ['admin_id' => Admin::where('role_id', 1)->first()->id]
        );
        if ($order->payment_method == 'cash_on_delivery') {
            return false;
        }
        if (($order->payment_status == "paid")) {

            $adminWallet->digital_received = $adminWallet->digital_received - $order->order_amount;
            $adminWallet->save();
            if (BusinessSetting::where('key', 'wallet_add_refund')->first()->value == 1 && $order->is_guest  == 0) {
                CustomerLogic::create_wallet_transaction($order->user_id, $order->order_amount, 'order_refund', $order->id);
            }
        } elseif (($order->payment_status == "partially_paid")) {

            $adminWallet->digital_received = $adminWallet->digital_received - $order->partially_paid_amount;
            $adminWallet->save();
            if (BusinessSetting::where('key', 'wallet_add_refund')->first()->value == 1  &&  $order->is_guest  == 0) {
                CustomerLogic::create_wallet_transaction($order->user_id, $order->partially_paid_amount, 'order_refund', $order->id);
            }
        }
        return true;
    }

    public static function refund_order($order)
    {
        $order_transaction = $order->transaction;
        if ($order_transaction == null || $order->store == null) {
            return false;
        }
        $received_by = $order_transaction->received_by;

        $adminWallet = AdminWallet::firstOrNew(
            ['admin_id' => Admin::where('role_id', 1)->first()->id]
        );

        $vendorWallet = StoreWallet::firstOrNew(
            ['vendor_id' => $order->store->vendor->id]
        );

        $adminWallet->total_commission_earning = $adminWallet->total_commission_earning - $order_transaction->admin_commission + $order_transaction->delivery_fee_comission;

        $vendorWallet->total_earning = $vendorWallet->total_earning - $order_transaction->store_amount;

        $refund_amount = $order->order_amount - $order->additional_charge - $order->extra_packaging_amount;

        $status = 'refunded_with_delivery_charge';
        if ($order->order_status == 'delivered' || $order->order_status == 'refund_requested') {
            $refund_amount = $order->order_amount - $order->additional_charge - $order->extra_packaging_amount - $order->delivery_charge - $order->dm_tips;
            $status = 'refunded_without_delivery_charge';
        } else {
            $adminWallet->delivery_charge = $adminWallet->delivery_charge - $order_transaction->delivery_charge;
        }
        try {
            DB::beginTransaction();
            $partially_paid = OrderPayment::where('payment_method', 'cash_on_delivery')->where('order_id', $order->id)->exists() ?? false;

            if ($partially_paid) {
                $refund_amount = $refund_amount - $order->partially_paid_amount;
            }
            if ($received_by == 'admin') {
                if ($order->delivery_man_id && $order->payment_method != "cash_on_delivery") {
                    $adminWallet->digital_received = $adminWallet->digital_received - $refund_amount;
                } else {
                    $adminWallet->manual_received = $adminWallet->manual_received - $refund_amount;
                }
            } else if ($received_by == 'store') {
                $vendorWallet->collected_cash = $vendorWallet->collected_cash - $refund_amount;
            }

            $order_transaction->status = $status;
            $order_transaction->save();
            $adminWallet->save();
            $vendorWallet->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            info($e->getMessage());
            return false;
        }
        return true;
    }

    public static function create_order_payment($order_id, $amount, $payment_status, $payment_method)
    {
        $payment = new OrderPayment();
        $payment->order_id = $order_id;
        $payment->amount = $amount;
        $payment->payment_status = $payment_status;
        $payment->payment_method = $payment_method;
        if ($payment->save()) {
            return true;
        }

        return false;
    }

    public static function update_unpaid_order_payment($order_id, $payment_method)
    {
        $payment = OrderPayment::where('payment_status', 'unpaid')->where('order_id', $order_id)->first();
        if ($payment) {
            $payment->payment_status = 'paid';
            if ($payment_method != 'partial_payment') {
                $payment->payment_method = $payment_method;
            }
            if ($payment->save()) {
                return true;
            }

            return false;
        }
        return true;
    }

    public static function update_unpaid_trip_payment($trip_id, $payment_method)
    {
        $payment = PartialPayment::where('payment_status', 'unpaid')->where('trip_id', $trip_id)->first();
        if ($payment) {
            $payment->payment_status = 'paid';
            if ($payment_method != 'partial_payment') {
                $payment->payment_method = $payment_method;
            }
            $payment->save();
        }
        return true;
    }



    public static function create_account_transaction_for_collect_cash($old_collected_cash, $from_type, $from_id, $amount, $order_id)
    {
        $account_transaction = new AccountTransaction();
        $account_transaction->from_type = $from_type;
        $account_transaction->from_id = $from_id;
        $account_transaction->created_by = $from_type;
        $account_transaction->method = 'cash_collection';
        $account_transaction->ref = $order_id;
        $account_transaction->amount = $amount ?? 0;
        $account_transaction->current_balance = $old_collected_cash ?? 0;
        $account_transaction->type = 'cash_in';
        $account_transaction->save();


        if ($from_type  ==  'store') {
            $vendor = Vendor::find($from_id);
            $Payable_Balance = $vendor?->wallet?->collected_cash   > 0 ? 1 : 0;
            $cash_in_hand_overflow = BusinessSetting::where('key', 'cash_in_hand_overflow_store')->first()?->value;
            $cash_in_hand_overflow_store_amount = BusinessSetting::where('key', 'cash_in_hand_overflow_store_amount')->first()?->value;

            if ($Payable_Balance == 1 &&  $cash_in_hand_overflow && $vendor?->wallet?->balance < 0 &&  $cash_in_hand_overflow_store_amount <= abs($vendor?->wallet?->collected_cash)) {
                $rest = Store::where('vendor_id', $vendor->id)->first();
                $rest->status = 0;
                $rest->save();
            }
        } elseif ($from_type  ==  'deliveryman') {
            $cash_in_hand_overflow = BusinessSetting::where('key', 'cash_in_hand_overflow_delivery_man')->first()?->value;
            $cash_in_hand_overflow_delivery_man = BusinessSetting::where('key', 'dm_max_cash_in_hand')->first()?->value;
            // $val=  $cash_in_hand_overflow_delivery_man - (($cash_in_hand_overflow_delivery_man * 10)/100);

            $dm = DeliveryMan::find($from_id);
            $wallet_balance = $dm?->wallet?->total_earning - ($dm?->wallet?->total_withdrawn + $dm?->wallet?->pending_withdraw + $dm?->wallet?->collected_cash);
            $over_flow_balance =  $dm?->wallet?->collected_cash;
            $Payable_Balance =  $over_flow_balance   > 0 ? 1 : 0;
            if ($Payable_Balance == 1 &&  $cash_in_hand_overflow  && $wallet_balance < 0 &&  $cash_in_hand_overflow_delivery_man < abs($over_flow_balance)) {
                $dm->status = 0;
                try {
                if(Helpers::getNotificationStatusData('deliveryman','deliveryman_account_block','push_notification_status') &&  isset($dm->fcm_token))
                {
                    $data = [
                        'title' => translate('messages.suspended'),
                        'description' => translate('Your account has been temporarily suspended due to exceeding the cash limit'),
                        'order_id' => '',
                        'image' => '',
                        'type'=> 'block'
                    ];
                    Helpers::send_push_notif_to_device($dm->fcm_token, $data);

                    DB::table('user_notifications')->insert([
                        'data'=> json_encode($data),
                        'delivery_man_id'=>$dm->id,
                        'created_at'=>now(),
                        'updated_at'=>now()
                    ]);
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
                $dm->auth_token = null;
                $dm->save();
            }
            
        }
        return true;
    }


    public static function cashbackToWallet($order)
    {

        $refer_wallet_transaction = CustomerLogic::create_wallet_transaction($order?->cashback_history?->user_id, $order?->cashback_history?->calculated_amount, 'CashBack', $order->id);
        if ($refer_wallet_transaction != false) {
            Helpers::expenseCreate(amount: $order?->cashback_history?->calculated_amount, type: 'CashBack', datetime: now(), created_by: 'admin', order_id: $order->id);
            $order?->cashback_history?->cashBack?->increment('total_used');

            $notification_data = [
                'title' => translate('messages.Congratulation_you_have_received') . ' ' . $order?->cashback_history?->calculated_amount . ' ' . translate('cashback'),
                'description' => translate('The_cashback_amount_successfully_added_to_your_wallet'),
                'order_id' => $order->id,
                'image' => '',
                'type' => 'cashback',
            ];

            if ($order->customer?->cm_firebase_token && Helpers::getNotificationStatusData('customer', 'customer_cashback', 'push_notification_status')) {
                Helpers::send_push_notif_to_device($order->customer?->cm_firebase_token, $notification_data);
                DB::table('user_notifications')->insert([
                    'data' => json_encode($notification_data),
                    'user_id' => $order->customer?->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        return true;
    }

    public static function makeValidationForParcelReturn($request , $order) {
        $validationError = match (true) {
            !$order => [
                'code'    => 'order',
                'message' => translate('messages.order_not_found'),
                'status_code' => 403,
            ],
            $order->order_type != 'parcel' => [
                'code'    => 'parcel',
                'message' => translate('messages.Only_parcel_order_can_be_returned'),
                'status_code' => 403,
            ],
            $order->order_status != 'canceled' => [
                'code'    => 'parcel',
                'message' => translate('messages.You_can_return_only_canceled_parcel_orders'),
                'status_code' => 403,
            ],
           !$order->parcelCancellation => [
                'code'    => 'order',
                'message' => translate('messages.You_have_not_requested_for_parcel_return'),
                'status_code' => 403,
            ],
           $order->parcelCancellation->return_otp && $order->parcelCancellation->return_otp != $request->return_otp => [
                'code'    => 'order',
                'message' => translate('messages.Invalid_return_otp'),
                'status_code' => 403,
            ],

            default => null,
        };

        if ($validationError) {
            return $validationError;
        }

        return null;
    }


    public static function cancelParcelOrder($order, $cancel_by, $request)
    {
        if (in_array($order->order_status, ['canceled', 'delivered','returned'])) {
            return ['status_code' => 403, 'code' => 'complete_order', 'message' => translate('messages.you_can_not_cancel_a_completed_order')];
        }
        $code = 'success';
        $msg= translate('Parcel_canceled_successfully');
        $parcel_cancellation_basic_setup = Helpers::get_business_settings('parcel_cancellation_basic_setup');

        $return_fee_status = $parcel_cancellation_basic_setup['return_fee_status'] ?? 0;
        $return_fee = $parcel_cancellation_basic_setup['return_fee'] ?? 0;
        $do_not_charge_return_fee_on_deliveryman_cancel = $parcel_cancellation_basic_setup['do_not_charge_return_fee_on_deliveryman_cancel'] ?? 0;

        $orderOldStatus = $order->order_status;
        $order->order_status = 'canceled';
        $order->canceled = now();
        $order->canceled_by = $cancel_by;
        $order->save();

        $parcelCancellation = ParcelCancellation::where('order_id', $order->id)->firstOrNew();
        $parcelCancellation->order_id = $order->id;
        $parcelCancellation->cancel_by = $cancel_by;
        $parcelCancellation->note = $request->note ?? null;
        $parcelCancellation->reason = json_encode($request->reason);

        if (in_array($orderOldStatus, ['picked_up']) ) {
            $parcelCancellation->before_pickup = 0;
            $parcelCancellation->return_otp = random_int(1000, 9999);

            if($return_fee_status == 1 && $return_fee > 0 ){
                if((in_array($cancel_by,['deliveryman', 'admin_for_deliveryman']) && $do_not_charge_return_fee_on_deliveryman_cancel == 1)){
                    $parcelCancellation->return_fee = 0;
                }else{
                    $chargeAmount = $order['delivery_charge'] + $order['total_tax_amount'] + $order['additional_charge'] - $order['coupon_discount_amount'] - $order['ref_bonus_amount'];
                    $parcelCancellation->return_fee = ($chargeAmount * $return_fee) / 100;
                }
            }

            $parcel_return_time_fee = Helpers::get_business_settings('parcel_return_time_fee');
            $parcel_return_time_fee_status = $parcel_return_time_fee['status']?? 0;
            $return_fee_for_dm= $parcel_return_time_fee['return_fee_for_dm'] ?? 0;

            if($parcel_return_time_fee_status == 1 && $return_fee_for_dm > 0){
                $parcelCancellation->dm_penalty_fee = $return_fee_for_dm;
                $parcelCancellation->return_date = now()->addDays((int) $parcel_return_time_fee['parcel_return_time'] ?? 1);
            }
        } else{
            if($order->payment_status == 'paid' && $order->is_guest  == 0){
                if(Helpers::get_business_settings('wallet_status') == 1 && Helpers::get_business_settings('wallet_add_refund') == 1){
                   $refunded=  self::refund_before_delivered($order);
                    if($refunded){
                        self::parcelRefundNotification($order,true);
                    }
                } else {
                    $parcelCancellation->is_delivery_charge_refundable = 1;
                    $code = 'wallet_failed';
                    $msg= translate('messages.Parcel_canceled_successfully_contact_admin_for_refund');
                }
            } elseif($order->payment_status == 'paid' && $order->is_guest  == 1){
                $code = 'wallet_failed';
                $msg= translate('messages.Parcel_canceled_successfully_contact_admin_for_refund');
                $parcelCancellation->is_delivery_charge_refundable = 1;
            }
        }

        $parcelCancellation->save();
        Helpers::send_order_notification($order);

        return ['status_code' => 200, 'code' => $code, 'message' => $msg];
    }

    public static function deliveryManCancelParcelTransaction($order){

        $return_fee = $order?->parcelCancellation?->return_fee ?? 0;
        DB::beginTransaction();

        $order->order_status = 'returned';
        $order->save();

        $order->parcelCancellation->return_fee_payment_status = 'paid';

            if($order->payment_status == 'paid' && $order->is_guest  == 0){
                if(Helpers::get_business_settings('wallet_status') == 1 && Helpers::get_business_settings('wallet_add_refund') == 1){
                   $refunded= self::refund_before_delivered($order);
                    if($refunded){
                        self::parcelRefundNotification($order,true);
                    }
                } else {
                    $order->parcelCancellation->is_delivery_charge_refundable = 1;
                }
            } elseif($order->payment_status == 'paid' && $order->is_guest  == 1){
                $order->parcelCancellation->is_delivery_charge_refundable = 1;
            }

        $order->parcelCancellation->save();

        try {

            $adminWallet = AdminWallet::firstOrNew(
                ['admin_id' => Admin::where('role_id', 1)->first()->id]
            );

            if ($order->delivery_man) {
                $dmWallet = DeliveryManWallet::firstOrNew(
                    ['delivery_man_id' => $order->delivery_man_id]
                );
                if ($order->delivery_man->earning == 1) {
                    $dmWallet->total_earning = $dmWallet->total_earning + $return_fee;
                    $dmWallet->collected_cash = $dmWallet->collected_cash + $return_fee;
                    self::createReturnFeeLog($order, $return_fee);
                } else {
                    $adminWallet->total_commission_earning = $adminWallet->total_commission_earning  + $return_fee;
                }
            } else {
                $adminWallet->total_commission_earning = $adminWallet->total_commission_earning + $return_fee;
            }

            $adminWallet->save();

            if($order->parcelCancellation->return_date){
                $returnDate = Carbon::parse($order->parcelCancellation->return_date);
                if ($returnDate->isPast() && isset($dmWallet)) {
                     $dmWallet->collected_cash = $dmWallet->collected_cash + $order->parcelCancellation->dm_penalty_fee??0;
                     self::createParcelPenaltyLog($order, $order->parcelCancellation->dm_penalty_fee??0);
                }
            }



            if (isset($dmWallet)) {
                $dmWallet->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            info($e->getMessage());
            return false;
        }

        return true;

    }

    public static function parcelRefundNotification($order,$wallet=true){
        try {
            if(Helpers::getNotificationStatusData('customer','customer_refund_request_approval','push_notification_status') && $order?->customer?->cm_firebase_token){
                $data = [
                    'title' => translate('messages.order_refunded'),
                    'description' => $wallet ? translate('Your Parcel\'s delivery charge has been refunded to your wallet') : translate('Your Parcel\'s delivery charge has been marked as Refunded'),
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'order_status',
                    'order_status' => $order->order_status,
                ];
                Helpers::send_push_notif_to_device($order?->customer?->cm_firebase_token, $data);
                DB::table('user_notifications')->insert([
                    'data' => json_encode($data),
                    'user_id' => $order->user_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            // if(config('mail.status') && $order?->customer?->email && Helpers::get_mail_status('refund_order_mail_status_user') == '1'  &&  Helpers::getNotificationStatusData('customer','customer_refund_request_approval','mail_status') ){
            //     Mail::to($order->customer?->getRawOriginal('email'))->send(new \App\Mail\RefundedOrderMail($order->id));
            // }
            } catch (\Throwable $th) {
                info($th->getMessage());
            }
            return true;
    }


    public static function deliverymanReferalTransaction($deliveryManId, $referType, $referrerId, $reference)
    {

        $settings = array_column(BusinessSetting::whereIn('key', ['dm_referal_status', 'dm_referal_amount','dm_referal_bonus'])->get()->toArray(), 'value', 'key');

        if (data_get($settings, 'dm_referal_status')  != 1 ) {
             return ['status_code' => 403, 'code' =>  'Referal', 'message' => translate('referal_option_is_not_enabled')];
        } elseif (  $referType  == 'referral' && data_get($settings, 'dm_referal_amount')  <= 0 ) {
             return ['status_code' => 403, 'code' =>  'Referal', 'message' => translate('referal_option_is_not_enabled')];
        } elseif ( $referType  == 'referrerBonus' && data_get($settings, 'dm_referal_bonus')  <= 0 ) {
             return ['status_code' => 403, 'code' =>  'Referal', 'message' => translate('referal_option_is_not_enabled')];
        }


        $deliveryMan = DeliveryMan::find($deliveryManId);
        if(!$deliveryMan){
            return ['status_code' => 403, 'code' =>  'wallet', 'message' => translate('delivery_man_not_found')];
        } elseif($deliveryMan->earning != 1){
              return ['status_code' => 403, 'code' =>  'Referal', 'message' => translate('wallet_not_enabled')];
        }

        $referralHistory =  new DeliverymanReferralHistory();
        $referralHistory->delivery_man_id = $deliveryMan->id;


        $amount = $referType  == 'referrerBonus' ? data_get($settings, 'dm_referal_bonus', 0) : data_get($settings, 'dm_referal_amount', 0);

        $referralHistory->amount = $amount;

        $referralHistory->referrer_id = $referrerId;
        $referralHistory->refer_type = $referType;
        $referralHistory->reference = $reference;
        $referralHistory->transaction_id = Str::uuid();

        $dmWallet = DeliveryManWallet::firstOrNew(['delivery_man_id' => $deliveryMan->id]);
        $dmWallet->total_earning = $dmWallet->total_earning + $amount;

        try {
            DB::beginTransaction();
            $referralHistory->save();
            $referralHistory->transaction_id = Helpers::generate_transaction_id($referralHistory);
            $referralHistory->save();
            $dmWallet->save();
            DB::commit();

        } catch (\Exception $exception) {
            info(["line___{$exception->getLine()}", $exception->getMessage()]);
            DB::rollback();
             return ['status_code' => 403, 'code' =>  'loyalty_point', 'message' => translate('messages.something_went_wrong')];
        }

            try {
            $data = [
                'title' => translate('Referal Bonus'),
                'description' =>  translate('Congratulations! You have received a referal bonus of ') . Helpers::format_currency($amount) ,
                'data_id' => $referralHistory->id,
                'image' => '',
                'type' => 'deliveryman_referral',
            ];
                if(Helpers::getNotificationStatusData('deliveryman','deliveryman_referral_bonus','push_notification_status') && $deliveryMan->fcm_token ){
                    Helpers::send_push_notif_to_device($deliveryMan->fcm_token, $data);
                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'delivery_man_id' => $deliveryMan->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

            } catch (\Exception $exception) {
                info(["line___{$exception->getLine()}", $exception->getMessage()]);
            }
        return true;
    }


    public static  function createReturnFeeLog($order, $returnFee){
            $returnFeeLog = new ParcelReturnFees();
            $returnFeeLog->order_id = $order->id;
            $returnFeeLog->delivery_man_id = $order->delivery_man_id ?? null;
            $returnFeeLog->amount = $returnFee;
            $returnFeeLog->save();

            $returnFeeLog->transaction_id =Helpers::generate_transaction_id($returnFeeLog);
            $returnFeeLog->save();
        return $returnFeeLog;
    }




    public static   function createParcelPenaltyLog($order, $penaltyFee){
        if($order->delivery_man_id){
            $log = new ParcelPenaltyFee();
            $log->order_id = $order->id;
            $log->delivery_man_id = $order->delivery_man_id;
            $log->amount = $penaltyFee;
            $log->save();
            $log->transaction_id =Helpers::generate_transaction_id($log);
            $log->save();
        }
        return $log ?? null;
    }

}
