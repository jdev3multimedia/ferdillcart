<?php

namespace App\Traits;

use App\Models\Cart;
use App\Models\Item;
use App\Models\User;
use App\Models\Zone;
use App\Models\Order;
use App\Models\Store;
use App\Models\Coupon;
use App\Models\DMVehicle;
use App\Models\OrderDetail;
use App\Models\ItemCampaign;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\ParcelCategory;
use App\Models\BusinessSetting;
use App\CentralLogics\OrderLogic;
use App\CentralLogics\CouponLogic;
use Illuminate\Support\Facades\DB;
use App\CentralLogics\ProductLogic;
use App\Mail\OrderVerificationMail;
use App\CentralLogics\CustomerLogic;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use MatanYadaev\EloquentSpatial\Objects\Point;
use App\Mail\CustomerRegistration;
use App\Mail\PlaceOrder;
use App\Models\AddOn;
use App\Models\SurgePrice;
use Carbon\Carbon;

trait PlaceNewOrder
{

    public function new_place_order(Request $request, $is_prescription = false)
    {
        $validator = Validator::make($request->all(), [
            // 'order_amount' => 'required',
            'payment_method' => 'required|in:cash_on_delivery,digital_payment,wallet,offline_payment',
            'order_type' => 'required|in:take_away,delivery,parcel',
            'store_id' => 'required_unless:order_type,parcel',
            'distance' => 'required_unless:order_type,take_away',
            'address' => 'required_unless:order_type,take_away',
            'longitude' => 'required_unless:order_type,take_away',
            'latitude' => 'required_unless:order_type,take_away',
            'parcel_category_id' => 'required_if:order_type,parcel',
            'receiver_details' => 'required_if:order_type,parcel',
            'charge_payer' => 'required_if:order_type,parcel|in:sender,receiver',
            'dm_tips' => 'nullable|numeric',
            'guest_id' => $request->user ? 'nullable' : 'required',
            'contact_person_name' => $request->user ? 'nullable' : 'required',
            'contact_person_number' => $request->user ? 'nullable' : 'required',
            'contact_person_email' => $request->user ? 'nullable' : 'required',
            'password' => $request->create_new_user ? ['required', Password::min(8)] : 'nullable',
            'order_attachment' => $is_prescription ? ['required'] : 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        try {
            DB::beginTransaction();
            $createNewUser =  $this->createNewUser($request);

            if (data_get($createNewUser, 'newUser') === true) {
                $request->is_guest = 0;
                $request->user = data_get($createNewUser, 'user');
            } elseif (data_get($createNewUser, 'status_code') === 403) {
                DB::rollBack();
                return response()->json([
                    'errors' => [
                        ['code' => data_get($createNewUser, 'code'), 'message' => data_get($createNewUser, 'message')]
                    ]
                ], data_get($createNewUser, 'status_code'));
            }

            $validationCheck =  $this->validationCheck($request);
            if (data_get($validationCheck, 'status_code') === 403) {
                DB::rollBack();
                return response()->json([
                    'errors' => [
                        ['code' => data_get($validationCheck, 'code'), 'message' => data_get($validationCheck, 'message')]
                    ]
                ], data_get($validationCheck, 'status_code'));
            }

            $schedule_at = $request->schedule_at ? \Carbon\Carbon::parse($request->schedule_at) : now();
            $zoneAndStore = $this->getZoneAndStore($request, $schedule_at);

            if (data_get($zoneAndStore, 'status_code') === 403) {
                DB::rollBack();
                return response()->json([
                    'errors' => [
                        ['code' => data_get($zoneAndStore, 'code'), 'message' => data_get($zoneAndStore, 'message')]
                    ]
                ], data_get($zoneAndStore, 'status_code'));
            }

            $store = $zoneAndStore['store'];
            $zone = $zoneAndStore['zone'];

            $zoneAndStoreValidationCheck = $this->zoneAndStoreValidationCheck($request, $schedule_at, $zone, $store);
            if (data_get($zoneAndStoreValidationCheck, 'status_code') === 403) {
                DB::rollBack();
                return response()->json([
                    'errors' => [
                        ['code' => data_get($zoneAndStoreValidationCheck, 'code'), 'message' => data_get($zoneAndStoreValidationCheck, 'message')]
                    ]
                ], data_get($zoneAndStoreValidationCheck, 'status_code'));
            }

            $coupon = null;
            $coupon_created_by = null;
            $delivery_charge = null;
            $free_delivery_by = null;
            $taxMap = [];
            $orderTaxIds = [];

            if ($request->order_type !== 'parcel') {

                $couponData = $this->getCouponData($request);
                if (data_get($couponData, 'status_code') === 403) {
                    DB::rollBack();
                    return response()->json([
                        'errors' => [
                            ['code' => data_get($couponData, 'code'), 'message' => data_get($couponData, 'message')]
                        ]
                    ], data_get($couponData, 'status_code'));
                } else {
                    $coupon = data_get($couponData, 'coupon');
                    $coupon_created_by = data_get($couponData, 'coupon_created_by');
                    $delivery_charge = data_get($couponData, 'delivery_charge');
                    $free_delivery_by = data_get($couponData, 'free_delivery_by');
                }
            }

            $module_wise_delivery_charge = $zone->modules()->where('modules.id', getModuleId($request->header('moduleId')))->first();

            $deliveryChargeData = $this->getDeliveryCharge($request, $zone, $store, $module_wise_delivery_charge, $delivery_charge, getModuleId($request->header('moduleId')));

            $delivery_charge = data_get($deliveryChargeData, 'delivery_charge', 0);
            $original_delivery_charge = data_get($deliveryChargeData, 'original_delivery_charge', 0);
            $vehicle_id = data_get($deliveryChargeData, 'vehicle_id', null);

            $address = [
                'contact_person_name' => $request->contact_person_name ? $request->contact_person_name : ($request->user ? $request->user->f_name . ' ' . $request->user->l_name : ''),
                'contact_person_number' => $request->contact_person_number ? $request->contact_person_number : ($request->user ? $request->user->phone : ''),
                'contact_person_email' => $request->contact_person_email ? $request->contact_person_email : ($request->user ? $request->user->email : ''),
                'address_type' => $request->address_type ? $request->address_type : 'Delivery',
                'address' => $request?->address ?? '',
                'floor' => $request?->floor ?? '',
                'road' => $request?->road ?? '',
                'house' => $request?->house ?? '',
                'longitude' => (string)$request->longitude,
                'latitude' => (string)$request->latitude,
            ];

            $total_addon_price = 0;
            $product_price = 0;
            $store_discount_amount = 0;
            $flash_sale_vendor_discount_amount = 0;
            $flash_sale_admin_discount_amount = 0;
            $coupon_discount_amount = 0;

            $product_data = [];
            $order_details = [];


            $lastId = Order::max('id') ?? 99999;
            $order = new Order();
            $order->id = $lastId + 1;


            $order_status = 'pending';
            if (($request->partial_payment && $request->payment_method != 'offline_payment') || $request->payment_method == 'wallet') {
                $order_status = 'confirmed';
            }
            if (in_array($request->payment_method, ['digital_payment', 'offline_payment'])) {
                $order_status = 'failed';
            }

            $order->bring_change_amount = $request['bring_change_amount'] ?? 0 ;

            $order->user_id = $request->user ? $request->user->id : $request['guest_id'];
            $order->order_amount = $request['order_amount'] ?? 0;
            $order->payment_status = ($request->partial_payment ? 'partially_paid' : ($request['payment_method'] == 'wallet' ? 'paid' : 'unpaid'));
            $order->order_status = $order_status;
            $order->coupon_code = $request['coupon_code'];
            $order->payment_method = $request->partial_payment ? 'partial_payment' : $request->payment_method;
            $order->transaction_reference = null;
            $order->order_note = $request['order_note'];
            $order->unavailable_item_note = $request['unavailable_item_note'];
            $order->delivery_instruction = $request['delivery_instruction'];
            $order->order_type = $request['order_type'];
            $order->store_id = $request['store_id'];
            $order->delivery_charge = round($delivery_charge, config('round_up_to_digit')) ?? 0;
            $order->original_delivery_charge = round($original_delivery_charge, config('round_up_to_digit'));
            $order->delivery_address = json_encode($address);
            $order->schedule_at = $schedule_at;
            $order->scheduled = $request->schedule_at ? 1 : 0;
            $order->cutlery = $request->cutlery ? 1 : 0;
            $order->is_guest = $request->user ? 0 : 1;
            $order->otp = rand(1000, 9999);
            $order->zone_id = isset($zone) ? $zone->id : end(json_decode($request->header('zoneId'), true));
            $order->module_id = getModuleId($request->header('moduleId'));
            $order->parcel_category_id = $request->parcel_category_id;
            $order->receiver_details = json_decode($request->receiver_details);

            if ($order_status == 'confirmed') {
                $order->confirmed = now();
            }
            $order->dm_vehicle_id = $vehicle_id;
            $order->pending = now();
            if (!empty($request->file('order_attachment')) && is_array($request->file('order_attachment'))) {
                $img_names = [];
                if (!empty($request->file('order_attachment'))) {
                    $images = [];
                    foreach ($request->order_attachment as $img) {
                        $image_name = Helpers::upload('order/', 'png', $img);
                        array_push($img_names, ['img' => $image_name, 'storage' => Helpers::getDisk()]);
                    }
                    $images = $img_names;
                }
            } else {
                $img_names = [];
                if (!empty($request->file('order_attachment'))) {
                    $images = [];
                    $image_name = Helpers::upload('order/', 'png', $request->file('order_attachment'));
                    array_push($img_names, ['img' => $image_name, 'storage' => Helpers::getDisk()]);

                    $images = $img_names;
                }
            }
            if (isset($images)) {
                $order->order_attachment = json_encode($images);
            }
            $order->distance = $request->distance;
            $order->created_at = now();
            $order->updated_at = now();
            $order->charge_payer = $request->charge_payer;
            $order->prescription_order = $is_prescription ? 1 : 0;
            $additionalCharges = [];


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
            $order->dm_tips = 0;
            if ($dm_tips_manage_status == 1) {
                $order->dm_tips = $request->dm_tips ?? 0;
            }

            //Added service charge
            $order->additional_charge = 0;

            if ($additional_charge_status == 1) {
                $order->additional_charge = $additional_charge ?? 0;
                // $additionalCharges['tax_on_additional_charge'] = $order->additional_charge;
            }

            // extra packaging charge

            $order->extra_packaging_amount =  (!empty($extra_packaging_data) && $request?->extra_packaging_amount > 0 && $store && ($extra_packaging_data[$store->module->module_type] == '1') && ($store?->storeConfig?->extra_packaging_status == '1')) ? $store?->storeConfig?->extra_packaging_amount : 0;

            if ($order->extra_packaging_amount > 0) {
                $additionalCharges['tax_on_packaging_charge'] =  $order->extra_packaging_amount;
            }

            if ($request->order_type !== 'parcel') {
                if ($is_prescription === false) {

                    $carts = Cart::where('user_id', $order->user_id)->where('is_guest', $order->is_guest)->where('module_id', getModuleId($request->header('moduleId')))
                        ->when(isset($request->is_buy_now) && $request->is_buy_now == 1 && $request->cart_id, function ($query) use ($request) {
                            return $query->where('id', $request->cart_id);
                        })
                        ->get()->map(function ($data) {
                            $data->add_on_ids = json_decode($data->add_on_ids, true);
                            $data->add_on_qtys = json_decode($data->add_on_qtys, true);
                            $data->variation = json_decode($data->variation, true);
                            return $data;
                        });

                    if (isset($request->is_buy_now) && $request->is_buy_now == 1) {
                        $carts = json_decode($request['cart'], true);
                    }

                    if (count($carts) == 0 && !$is_prescription) {
                        DB::rollBack();
                        return response()->json([
                            'errors' => [
                                ['code' => 'empty_cart', 'message' => translate('messages.You_can_not_place_empty_orders')]
                            ]
                        ], 403);
                    }

                    $order_details = $this->makeOrderDetails($carts, $request, $order, $store);

                    if (data_get($order_details, 'status_code') === 403) {
                        DB::rollBack();
                        return response()->json([
                            'errors' => [
                                ['code' => data_get($order_details, 'code'), 'message' => data_get($order_details, 'message')]
                            ]
                        ], data_get($order_details, 'status_code'));
                    }

                    $total_addon_price = $order_details['total_addon_price'];
                    $product_price = $order_details['product_price'];
                    $store_discount_amount = $order_details['store_discount_amount'];
                    $flash_sale_admin_discount_amount = $order_details['flash_sale_admin_discount_amount'];
                    $flash_sale_vendor_discount_amount = $order_details['flash_sale_vendor_discount_amount'];
                    $product_data = $order_details['product_data'];
                    $order_details = $order_details['order_details'];
                }


                $order->discount_on_product_by = $order_details['discount_on_product_by'] ?? 'vendor';

                $coupon_discount_amount = $coupon ? CouponLogic::get_discount($coupon, $product_price + $total_addon_price - $store_discount_amount - $flash_sale_admin_discount_amount - $flash_sale_vendor_discount_amount) : 0;

                $total_price = $product_price + $total_addon_price - $store_discount_amount - $flash_sale_admin_discount_amount - $flash_sale_vendor_discount_amount  - $coupon_discount_amount;

                if ($order->is_guest  == 0 && $order->user_id) {
                    $user = User::withcount('orders')->find($order->user_id);
                    $discount_data = Helpers::getCusromerFirstOrderDiscount(order_count: $user->orders_count, user_creation_date: $user->created_at,  refby: $user->ref_by, price: $total_price);
                    if (data_get($discount_data, 'is_valid') == true &&  data_get($discount_data, 'calculated_amount') > 0) {
                        $total_price = $total_price - data_get($discount_data, 'calculated_amount');
                        $order->ref_bonus_amount = data_get($discount_data, 'calculated_amount');
                    }
                }

                $total_price = max($total_price, 0);

                $order->tax_status = 'excluded';

                $totalDiscount = $store_discount_amount + $flash_sale_admin_discount_amount + $flash_sale_vendor_discount_amount  + $coupon_discount_amount +  $order->ref_bonus_amount;



                $finalCalculatedTax =  Helpers::getFinalCalculatedTax(
                    $order_details,
                    $additionalCharges,
                    $totalDiscount,
                    $total_price,
                    $store->id
                );

                $taxType=  data_get($finalCalculatedTax ,'taxType');
                $tax_amount = $finalCalculatedTax['tax_amount'];
                $tax_status = $finalCalculatedTax['tax_status'];
                $taxMap = $finalCalculatedTax['taxMap'];
                $orderTaxIds = data_get($finalCalculatedTax, 'taxData.orderTaxIds', []);

                $order->tax_status = $tax_status;
                $order->tax_type = $taxType;

                if (!$is_prescription  && $store->minimum_order > $product_price + $total_addon_price) {
                    DB::rollBack();
                    return response()->json([
                        'errors' => [
                            ['code' => 'order_time', 'message' => translate('messages.you_need_to_order_at_least') . $store->minimum_order . ' ' . Helpers::currency_code()]
                        ]
                    ], 406);
                }

                $businessSettings = BusinessSetting::whereIn('key', ['free_delivery_over', 'admin_free_delivery_status', 'admin_free_delivery_option'])->pluck('value', 'key');

                $free_delivery_over = (float) ($businessSettings['free_delivery_over'] ?? 0);
                $admin_free_delivery_status = (int) ($businessSettings['admin_free_delivery_status'] ?? 0);
                $admin_free_delivery_option = $businessSettings['admin_free_delivery_option'] ?? null;


                if ($admin_free_delivery_status === 1) {
                    $eligibleAmount = $product_price + $total_addon_price - $coupon_discount_amount - $store_discount_amount - $flash_sale_admin_discount_amount - $flash_sale_vendor_discount_amount;

                    if ($admin_free_delivery_option === 'free_delivery_to_all_store' || ($admin_free_delivery_option === 'free_delivery_by_order_amount' && $free_delivery_over > 0  && $eligibleAmount >= $free_delivery_over)) {
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
                        if ($coupon->min_purchase <= $product_price + $total_addon_price - $store_discount_amount - $flash_sale_admin_discount_amount - $flash_sale_vendor_discount_amount) {
                            $order->delivery_charge = 0;
                            $free_delivery_by = $coupon->created_by;
                        }
                    }
                    $coupon->increment('total_uses');
                }
                $order->coupon_created_by = $coupon_created_by;
                $order->coupon_discount_amount = round($coupon_discount_amount, config('round_up_to_digit'));
                $order->coupon_discount_title = $coupon ? $coupon->title : '';

                $order->store_discount_amount = round($store_discount_amount, config('round_up_to_digit'));
                $order->tax_percentage = 0;
                $order->total_tax_amount = round($tax_amount, config('round_up_to_digit'));
                $order->order_amount = round($total_price + $tax_amount + $order->delivery_charge, config('round_up_to_digit'));
                $order->free_delivery_by = $free_delivery_by;
            } else {

                $order->delivery_charge = round($original_delivery_charge, config('round_up_to_digit')) ?? 0;
                $order->original_delivery_charge = round($original_delivery_charge, config('round_up_to_digit'));
                $order->order_amount = round($order->delivery_charge, config('round_up_to_digit'));

                $productIds[] = [
                    'id' => 1,
                    'original_price' => $order->order_amount,
                    'quantity' => 1,
                    'category_id' =>  $request->parcel_category_id,
                    'discount' => 0,
                    'discount_type' => '',
                    'after_discount_final_price' => $order->order_amount,
                    'is_campaign_item' => false,
                ];

                $taxData =  \Modules\TaxModule\Services\CalculateTaxService::getCalculatedTax(
                    amount: $order->order_amount,
                    productIds: $productIds,
                    taxPayer: 'parcel',
                    storeData: true,
                    additionalCharges: $additionalCharges,
                    addonIds: [],
                    orderId: null,
                    storeId: null
                );


                $tax_amount = $taxData['totalTaxamount'];
                $tax_included = $taxData['include'];
                $orderTaxIds = $taxData['orderTaxIds'] ?? [];
                $tax_status = $tax_included ?  'included' : 'excluded';
                $order->total_tax_amount = round($tax_amount, config('round_up_to_digit'));

                $order->tax_status = $tax_status;
                $order->order_amount = round($order->delivery_charge + $tax_amount, config('round_up_to_digit'));
            }
            $order->flash_admin_discount_amount = round($flash_sale_admin_discount_amount, config('round_up_to_digit'));
            $order->flash_store_discount_amount = round($flash_sale_vendor_discount_amount, config('round_up_to_digit'));

            //DM TIPS
            $order->order_amount = $order->order_amount + $order->dm_tips + $order->additional_charge + $order->extra_packaging_amount;
            if ($request->payment_method == 'wallet' && $request->user->wallet_balance < $order->order_amount) {
                DB::rollBack();
                return response()->json([
                    'errors' => [
                        ['code' => 'order_amount', 'message' => translate('messages.insufficient_balance')]
                    ]
                ], 203);
            }
            if ($request->partial_payment && $request->user->wallet_balance > $order->order_amount) {
                DB::rollBack();
                return response()->json([
                    'errors' => [
                        ['code' => 'partial_payment', 'message' => translate('messages.order_amount_must_be_greater_than_wallet_amount')]
                    ]
                ], 203);
            }
            if (isset($module_wise_delivery_charge) && $request->payment_method == 'cash_on_delivery' && $module_wise_delivery_charge->pivot->maximum_cod_order_amount && $order->order_amount > $module_wise_delivery_charge->pivot->maximum_cod_order_amount) {
                DB::rollBack();
                return response()->json([
                    'errors' => [
                        ['code' => 'order_amount', 'message' => translate('messages.amount_crossed_maximum_cod_order_amount')]
                    ]
                ], 203);
            }


            $order->save();
            if ($request->order_type !== 'parcel') {
                $taxMapCollection = collect($taxMap);
                foreach ($order_details as $key => $item) {
                    $order_details[$key]['order_id'] = $order->id;

                    if ($item['item_id']) {
                        $item_id = $item['item_id'];
                    } else {
                        $item_id = $item['item_campaign_id'];
                    }
                    $index = $taxMapCollection->search(function ($tax) use ($item_id) {
                        return $tax['product_id'] == $item_id;
                    });
                    if ($index !== false) {
                        $matchedTax = $taxMapCollection->pull($index);
                        $order_details[$key]['tax_status'] = $matchedTax['include'] == 1 ? 'included' : 'excluded';
                        $order_details[$key]['tax_amount'] = $matchedTax['totalTaxamount'];
                    }
                }

                OrderDetail::insert($order_details);

                if (count($product_data) > 0) {
                    foreach ($product_data as $item) {
                        ProductLogic::update_stock($item['item'], $item['quantity'], $item['variant'])->save();
                        ProductLogic::update_flash_stock($item['item'], $item['quantity'])?->save();
                    }
                }
                $store->increment('total_order');
            }
            if (count($orderTaxIds)) {
                \Modules\TaxModule\Services\CalculateTaxService::updateOrderTaxData(
                    orderId: $order->id,
                    orderTaxIds: $orderTaxIds,
                );
            }
            if (!isset($request->is_buy_now) || (isset($request->is_buy_now) && $request->is_buy_now == 0)) {
                foreach ($carts ?? [] as $cart) {
                    $cart?->delete();
                }
            }
            if ($request->user) {
                $customer = $request->user;
                $customer->zone_id = $order->zone_id;
                $customer->save();
                if ($request->payment_method == 'wallet') CustomerLogic::create_wallet_transaction($order->user_id, $order->order_amount, 'order_place', $order->id);

                if ($request->partial_payment) {
                    if ($request->user->wallet_balance <= 0) {
                        DB::rollBack();
                        return response()->json([
                            'errors' => [
                                ['code' => 'order_amount', 'message' => translate('messages.insufficient_balance_for_partial_amount')]
                            ]
                        ], 203);
                    }
                    $p_amount = min($request->user->wallet_balance, $order->order_amount);
                    $unpaid_amount = $order->order_amount - $p_amount;
                    $order->partially_paid_amount = $p_amount;
                    $order->save();
                    CustomerLogic::create_wallet_transaction($order->user_id, $p_amount, 'partial_payment', $order->id);
                    OrderLogic::create_order_payment(order_id: $order->id, amount: $p_amount, payment_status: 'paid', payment_method: 'wallet');
                    OrderLogic::create_order_payment(order_id: $order->id, amount: $unpaid_amount, payment_status: 'unpaid', payment_method: $request->payment_method);
                }
            }
            if ($order->is_guest  == 0 && $order->user_id) {
                $this->createCashBackHistory($order->order_amount, $order->user_id, $order->id);
            }

            DB::commit();

            $this->sentOrderPlaceNotification($request, $order, $store);

            return response()->json([
                'message' => translate('messages.order_placed_successfully'),
                'order_id' => $order->id,
                'total_ammount' => $order->order_amount,
                'status' => $order->order_status,
                'created_at' => $order->created_at,
                'user_id' => (int) $order->user_id,
            ], 200);
        } catch (\Exception $exception) {

            info([$exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            DB::rollBack();
            return response()->json([$exception], 403);
        }

        return response()->json([
            'errors' => [
                ['code' => 'order_time', 'message' => translate('messages.failed_to_place_order')]
            ]
        ], 403);
    }



    private function createNewUser($request)
    {
        if (!$request->create_new_user) {
            return false;
        }

        $validationError = match (true) {
            !$request->password => [
                'status_code' => 403,
                'message'     => translate('messages.password_is_required'),
                'code'        => 'password',
            ],
            User::where('phone', $request->contact_person_number)->exists() => [
                'status_code' => 403,
                'message'     => translate('messages.phone_already_taken'),
                'code'        => 'phone_person_email',
            ],
            User::where('email', $request->contact_person_email)->exists() => [
                'status_code' => 403,
                'message'     => translate('messages.email_already_taken'),
                'code'        => 'contact_person_email',
            ],
            default => null,
        };

        if ($validationError) {
            return $validationError;
        }

        $user = new User();
        $user->f_name = $request->contact_person_name;
        $user->email = $request->contact_person_email;
        $user->phone = $request->contact_person_number;
        $user->password = bcrypt($request->password);
        $user->ref_code = Helpers::generate_referer_code($user);
        $user->login_medium = 'manual';
        $user->save();

        try {
            if (config('mail.status') && $request->contact_person_email && Helpers::get_mail_status('registration_mail_status_user') == '1' && Helpers::getNotificationStatusData('customer', 'customer_registration', 'mail_status')) {
                Mail::to($request->contact_person_email)->send(new CustomerRegistration($request->contact_person_name));
            }
        } catch (\Exception $exception) {
            info([$exception->getFile(), $exception->getLine(), $exception->getMessage()]);
        }
        if ($request->guest_id  && isset($user->id)) {

            $userStoreIds = Cart::where('user_id', $request->guest_id)
                ->join('items', 'carts.item_id', '=', 'items.id')
                ->pluck('items.store_id')
                ->toArray();

            Cart::where('user_id', $user->id)
                ->whereHas('item', function ($query) use ($userStoreIds) {
                    $query->whereNotIn('store_id', $userStoreIds);
                })
                ->delete();

            Cart::where('user_id', $request->guest_id)->update(['user_id' => $user->id, 'is_guest' => 0]);
        }

        return ['newUser' => true, 'user' => $user];
    }


    private function validationCheck($request)
    {
        $validationError = match (true) {
            $request->is_guest && !Helpers::get_mail_status('guest_checkout_status') => [
                'code'    => 'is_guest',
                'message' => translate('messages.Guest_order_is_not_active'),
                'status_code' => 403,
            ],

            $request->order_type === 'delivery' && !Helpers::get_business_settings('home_delivery_status') => [
                'code'    => 'order_type',
                'message' => translate('messages.home_delivery_is_not_active'),
                'status_code' => 403,
            ],

            $request->order_type === 'take_away' && !Helpers::get_business_settings('takeaway_status') => [
                'code'    => 'order_type',
                'message' => translate('messages.take_away_is_not_active'),
                'status_code' => 403,
            ],

            $request->partial_payment && !Helpers::get_business_settings('partial_payment_status') => [
                'code'    => 'order_method',
                'message' => translate('messages.partial_payment_is_not_active'),
                'status_code' => 403,
            ],

            $request->payment_method === 'offline_payment' && !Helpers::get_mail_status('offline_payment_status') => [
                'code'    => 'offline_payment_status',
                'message' => translate('messages.offline_payment_for_the_order_not_available_at_this_time'),
                'status_code' => 403,
            ],

            $request->payment_method === 'digital_payment' && !Helpers::get_business_settings('digital_payment')['status'] => [
                'code'    => 'digital_payment',
                'message' => translate('messages.digital_payment_for_the_order_not_available_at_this_time'),
                'status_code' => 403,
            ],
            $request->payment_method === 'cash_on_delivery' && !Helpers::get_business_settings('cash_on_delivery')['status'] => [
                'code'    => 'digital_payment',
                'message' => translate('messages.Cash_on_delivery_for_the_order_not_available_at_this_time'),
                'status_code' => 403,
            ],

            default => null,
        };

        if ($validationError) {
            return $validationError;
        }

        return null;
    }

    private function getVehicleExtraCharge($distance)
    {
        $data =  DMVehicle::active()->where(function ($query) use ($distance) {
            $query->where('starting_coverage_area', '<=', $distance)->where('maximum_coverage_area', '>=', $distance)
                ->orWhere(function ($query) use ($distance) {
                    $query->where('starting_coverage_area', '>=', $distance);
                });
        })
            ->orderBy('starting_coverage_area')->first();
        return ['extraCharge' => (float) (isset($data) ? $data->extra_charges  : 0), 'vehicle_id' => $data?->id];
    }
    private function getZoneAndStore($request, $schedule_at)
    {
        if ($request->latitude && $request->longitude) {
            if ($request->order_type == 'parcel') {
                $zone_ids = $request->header('zoneId') ? json_decode($request->header('zoneId'), true) : [];
                $zone = Zone::whereIn('id', $zone_ids)->whereContains('coordinates', new Point($request->latitude, $request->longitude, POINT_SRID))->wherehas('modules', function ($q) {
                    $q->where('module_type', 'parcel');
                })->first();


                $receiver_zone_id =  json_decode($request->receiver_details, true)['zone_id'];
                $receiverZone = Zone::where('id', $receiver_zone_id)->whereContains('coordinates', new Point(json_decode($request->receiver_details, true)['latitude'], json_decode($request->receiver_details, true)['longitude'], POINT_SRID))->first();
                if (!$receiverZone) {
                    return [
                        'status_code' => 403,
                        'code' => 'receiverZone',
                        'message' => translate('messages.out_of_coverage'),
                    ];
                }
            } else {
                $store = Store::with(['discount', 'store_sub'])->selectRaw('*, IF(((select count(*) from `store_schedule` where `stores`.`id` = `store_schedule`.`store_id` and `store_schedule`.`day` = ' . $schedule_at->format('w') . ' and `store_schedule`.`opening_time` < "' . $schedule_at->format('H:i:s') . '" and `store_schedule`.`closing_time` >"' . $schedule_at->format('H:i:s') . '") > 0), true, false) as open')->where('id', $request->store_id)->first();
                if ($store) {
                    $zone = Zone::where('id', $store->zone_id)->whereContains('coordinates', new Point($request->latitude, $request->longitude, POINT_SRID))->first();
                }
            }
        }
        return ['zone' => $zone ?? null, 'store' => $store ?? null];
    }


    private function zoneAndStoreValidationCheck($request, $schedule_at, $zone, $store)
    {
        $store_sub = $store?->store_sub;
        $validationError = match (true) {
            !$zone => [
                'code'    => 'zone',
                'message' => translate('messages.out_of_coverage_area'),
                'status_code' => 403,
            ],
            default => null,
        };

        if ($request->order_type !== 'parcel') {
            $validationError = match (true) {
                !$store => [
                    'code'    => 'store',
                    'message' => translate('messages.store_not_found'),
                    'status_code' => 403,
                ],
                $request->schedule_at && $schedule_at < now() => [
                    'code'    => 'order_time',
                    'message' => translate('messages.you_can_not_schedule_a_order_in_past'),
                    'status_code' => 403,
                ],
                $request->schedule_at && !$store->schedule_order => [
                    'code'    => 'schedule_at',
                    'message' => translate('messages.schedule_order_not_available'),
                    'status_code' => 403,
                ],
                $store->open == false => [
                    'code'    => 'order_time',
                    'message' => translate('messages.store_is_closed_at_order_time'),
                    'status_code' => 403,
                ],
                $store->store_business_model == 'unsubscribed' => [
                    'code'    => 'order-confirmation-model',
                    'message' => translate('messages.Sorry_the_store_is_unable_to_take_any_order_!'),
                    'status_code' => 403,
                ],

                $store->is_valid_subscription && $store_sub && $store_sub->max_order != "unlimited" && $store_sub->max_order <= 0 => [
                    'code'    => 'order-confirmation-error',
                    'message' => translate('messages.Sorry_the_store_is_unable_to_take_any_order_!'),
                    'status_code' => 403,
                ],
                default => null,
            };
        }

        if ($validationError) {
            return $validationError;
        }

        return null;
    }
    private function getCouponData($request)
    {

        if ($request['coupon_code']) {
            $coupon = Coupon::active()->where(['code' => $request['coupon_code']])->first();

            if (!$coupon) {
                return [
                    'status_code' => 403,
                    'code' => 'coupon',
                    'message' => translate('messages.coupon_expire'),
                ];
            }

            $status = $request->is_guest
                ? CouponLogic::is_valid_for_guest($coupon, $request['store_id'])
                : CouponLogic::is_valide($coupon, $request->user->id, $request['store_id']);

            $validationError = match ($status) {
                407 => [
                    'status_code' => 403,
                    'code' => 'coupon',
                    'message' => translate('messages.coupon_expire'),
                ],
                408 => [
                    'status_code' => 403,
                    'code' => 'coupon',
                    'message' => translate('messages.You_are_not_eligible_for_this_coupon'),
                ],
                406 => [
                    'status_code' => 403,
                    'code' => 'coupon',
                    'message' => translate('messages.coupon_usage_limit_over'),
                ],
                404 => [
                    'status_code' => 403,
                    'code' => 'coupon',
                    'message' => translate('messages.not_found'),
                ],
                default => null,
            };

            if ($validationError) {
                return $validationError;
            }

            $coupon_created_by = $coupon->created_by;

            if ($coupon->coupon_type === 'free_delivery') {
                $delivery_charge = 0;
                $free_delivery_by = $coupon_created_by;
                $coupon_created_by = null;
            }
        }

        return [
            'coupon' => $coupon ?? null,
            'coupon_created_by' => $coupon_created_by ?? null,
            'delivery_charge' => $delivery_charge ?? null,
            'free_delivery_by' => $free_delivery_by ?? null,
        ];
    }

    private function getDeliveryCharge($request, $zone, $store, $module_wise_delivery_charge, $delivery_charge, $moduleId)
    {
        $increased = 0;
        $schedule_at = $request->schedule_at ? \Carbon\Carbon::parse($request->schedule_at) : now();
        $surge = $this->getSurgePriceValue($zone->id, $moduleId, $schedule_at);
        if ($surge['price'] > 0) {
            $increased = $surge['price'];
        }
        $vehicleExtraCharge = $this->getVehicleExtraCharge($request->distance ?? 0);
        $extra_charges = $vehicleExtraCharge['extraCharge'];
        $vehicle_id = $vehicleExtraCharge['vehicle_id'];

        if ($request->order_type !== 'parcel') {

            if ($request['order_type'] === 'take_away') {
                return [
                    'vehicle_id' => null,
                    'original_delivery_charge' => 0,
                    'delivery_charge' => 0,
                ];
            }

            if ($store?->sub_self_delivery == 1) {
                $per_km_shipping_charge = $store->per_km_shipping_charge;
                $minimum_shipping_charge = $store->minimum_shipping_charge;
                $maximum_shipping_charge = $store->maximum_shipping_charge;
                $extra_charges = 0;
                $vehicle_id = null;
                $increased = 0;
            } elseif ($module_wise_delivery_charge) {
                $per_km_shipping_charge = $module_wise_delivery_charge->pivot->delivery_charge_type == 'distance' ? $module_wise_delivery_charge->pivot->per_km_shipping_charge : $module_wise_delivery_charge->pivot->fixed_shipping_charge;
                $minimum_shipping_charge = $module_wise_delivery_charge->pivot->delivery_charge_type == 'distance' ? $module_wise_delivery_charge->pivot->minimum_shipping_charge : $module_wise_delivery_charge->pivot->fixed_shipping_charge;
                $maximum_shipping_charge = $module_wise_delivery_charge->pivot->delivery_charge_type == 'distance' ? $module_wise_delivery_charge->pivot->maximum_shipping_charge : $module_wise_delivery_charge->pivot->fixed_shipping_charge;
            } else {
                // $per_km_shipping_charge = 0;
                // $minimum_shipping_charge = 0;
                // $maximum_shipping_charge = 0;
                return [
                    'vehicle_id' => null,
                    'original_delivery_charge' => 0,
                    'delivery_charge' => $delivery_charge,
                ];
            }

            $original_delivery_charge = (($request->distance * $per_km_shipping_charge) > $minimum_shipping_charge) ? $request->distance * $per_km_shipping_charge  : $minimum_shipping_charge;
            if ($maximum_shipping_charge  >= $minimum_shipping_charge  && $original_delivery_charge >  $maximum_shipping_charge) {
                $original_delivery_charge = $maximum_shipping_charge;
            } else {
                $original_delivery_charge = $original_delivery_charge;
            }

            if (!isset($delivery_charge)) {
                $delivery_charge = ($request->distance * $per_km_shipping_charge > $minimum_shipping_charge) ? $request->distance * $per_km_shipping_charge : $minimum_shipping_charge;
                if ($maximum_shipping_charge  >= $minimum_shipping_charge  && $delivery_charge >  $maximum_shipping_charge) {
                    $delivery_charge = $maximum_shipping_charge;
                } else {
                    $delivery_charge = $delivery_charge;
                }
            }
            $original_delivery_charge = $original_delivery_charge + $extra_charges;
            $delivery_charge = $delivery_charge + $extra_charges;
        } else {
            $parcel_category = ParcelCategory::find($request->parcel_category_id);
            if ($parcel_category?->parcel_minimum_shipping_charge) {
                $per_km_shipping_charge = $parcel_category->parcel_per_km_shipping_charge;
                $minimum_shipping_charge = $parcel_category->parcel_minimum_shipping_charge;
            } else {
                $businessSetting = BusinessSetting::whereIn('key', [
                    'parcel_per_km_shipping_charge',
                    'parcel_minimum_shipping_charge',
                ])->pluck('value', 'key');

                $per_km_shipping_charge = (float) ($businessSetting['parcel_per_km_shipping_charge'] ?? 0);
                $minimum_shipping_charge = (float) ($businessSetting['parcel_minimum_shipping_charge'] ?? 0);
            }

            $original_delivery_charge = (($request->distance * $per_km_shipping_charge) > $minimum_shipping_charge) ? ($request->distance * $per_km_shipping_charge) + $extra_charges : ($minimum_shipping_charge + $extra_charges);
        }

        if ($increased > 0) {
            if ($delivery_charge > 0) {
                $extra = $surge['price_type'] === 'percent'
                    ? ($delivery_charge * $surge['price']) / 100
                    : $surge['price'];

                $delivery_charge += $extra;
            }

            if ($original_delivery_charge > 0) {
                $extra = $surge['price_type'] === 'percent'
                    ? ($original_delivery_charge * $surge['price']) / 100
                    : $surge['price'];

                $original_delivery_charge += $extra;
            }
        }
        return [
            'delivery_charge' => $delivery_charge,
            'original_delivery_charge' => $original_delivery_charge ?? 0,
            'vehicle_id' => $vehicle_id ?? null,
        ];
    }

    private function sentOrderPlaceNotification($request, $order, $store)
    {
        $payments = $order->payments()->where('payment_method', 'cash_on_delivery')->exists();
        try {
            if (!in_array($order->payment_method, ['digital_payment', 'partial_payment', 'offline_payment'])  || $payments) {
                if ($store?->is_valid_subscription == 1 && $store?->store_sub?->max_order != "unlimited" && $store?->store_sub?->max_order > 0) {
                    $store?->store_sub?->decrement('max_order', 1);
                }
                Helpers::send_order_notification($order);

                $email = $order->is_guest == 1 ? $request->contact_person_email : $request->user?->email;
                $name = $order->is_guest == 1 ? $request->contact_person_name : $request->user?->f_name;
                if (config('mail.status') && $email && $order->order_status == 'pending') {
                    if ($order->order_status == 'pending'  &&  Helpers::get_mail_status('place_order_mail_status_user') == '1' && Helpers::getNotificationStatusData('customer', 'customer_order_notification', 'mail_status')) {
                        Mail::to($email)->send(new PlaceOrder($order->id));
                    }
                    if (config('order_delivery_verification') == 1 && Helpers::get_mail_status('order_verification_mail_status_user') == '1'  && Helpers::getNotificationStatusData('customer', 'customer_delivery_verification', 'mail_status')) {
                        Mail::to($email)->send(new OrderVerificationMail($order->otp, $name));
                    }
                }
            }
        } catch (\Exception $exception) {
            info([$exception->getFile(), $exception->getLine(), $exception->getMessage()]);
        }
        return true;
    }

    private function makeOrderDetails($carts, $request, $order, $store)
    {
        $total_addon_price = 0;
        $product_price = 0;
        $store_discount_amount = 0;
        $flash_sale_vendor_discount_amount = 0;
        $flash_sale_admin_discount_amount = 0;
        $product_data = [];
        $order_details = [];
        $discount_type = '';
        $discount_on_product_by = 'vendor';
        foreach ($carts as $c) {
            $variations = [];
            $isCampaign = false;
            if ($c['item_type'] === 'App\Models\ItemCampaign' || $c['item_type'] === 'AppModelsItemCampaign') {
                $product = ItemCampaign::with('module')->active()->find($c['item_id']);
                $isCampaign = true;
            } else {
                $product = Item::with('module')->active()->find($c['item_id']);
            }
            if ($product) {
                if ($product->store_id != $order->store_id) {
                    return [
                        'status_code' => 403,
                        'code' => 'different_stores',
                        'message' => translate('messages.Please_select_items_from_the_same_store'),
                    ];
                }

                if ($product?->pharmacy_item_details?->is_prescription_required == '1' && empty($request->file('order_attachment'))) {
                    return [
                        'status_code' => 403,
                        'code' => 'prescription',
                        'message' => translate('messages.prescription_is_required_for_this_order'),
                    ];
                }

                if ($product?->maximum_cart_quantity && $c['quantity'] > $product?->maximum_cart_quantity) {
                    return [
                        'status_code' => 403,
                        'code' => 'quantity',
                        'message' => translate('messages.maximum_cart_quantity_limit_over'),
                    ];
                }


                $foodVariation = false;
                if ($product?->module?->module_type == 'food') {
                    $foodVariation = true;
                    $product_variations = json_decode($product->food_variations, true);

                    if ($product_variations && count($product_variations)) {
                        $variation_data = Helpers::get_varient($product_variations, $c['variation']);
                        $price = $product['price'] + $variation_data['price'];
                        $variations = $variation_data['variations'];
                    } else {
                        $price = $product['price'];
                    }
                } else {
                    if (count(json_decode($product['variations'], true)) > 0 && count($c['variation']) > 0) {
                        $variant_data = Helpers::variation_price($product, json_encode($c['variation']));
                        $price = $variant_data['price'];
                        $stock = $variant_data['stock'];
                    } else {
                        $price = $product['price'];
                        $stock = $product?->stock;
                    }

                    if (config('module.' . $product->module->module_type)['stock']) {
                        if ($c['quantity'] > $stock) {

                            return [
                                'status_code' => 403,
                                'code' => 'stock',
                                'message' => $isCampaign ? $product?->title : $product?->name . ' ' . translate('messages.is_out_of_stock')
                            ];
                        }
                        $product_data[] = [
                            'item' => clone $product,
                            'quantity' => $c['quantity'],
                            'variant' => count($c['variation']) > 0 ? $c['variation'][0]['type'] : null
                        ];
                    }
                }

                $product = Helpers::product_data_formatting($product, false, false, app()->getLocale());
                $addon_data = Helpers::calculate_addon_price(AddOn::whereIn('id', $c['add_on_ids'])->get(), $c['add_on_qtys']);
                $product_discount = Helpers::product_discount_calculate($product, $price, $store, false);



                $discount_type = $product_discount['discount_type'];

                $or_d = [
                    'item_id' => $isCampaign ?  null : $c['item_id'],
                    'item_campaign_id' => $isCampaign ? $c['item_id'] : null,
                    'item_details' => json_encode($product),
                    'quantity' => $c['quantity'],
                    'price' => round($price, config('round_up_to_digit')),

                    'category_id' => collect(is_string($product->category_ids) ? json_decode($product->category_ids, true) : $product->category_ids)->firstWhere('position', 1)['id'] ?? null,
                    // 'tax_amount' => round(Helpers::tax_calculate($product, $price), config('round_up_to_digit')),
                    'tax_amount' => 0,
                    'tax_status' => null,

                    'discount_on_product_by' => $product_discount['discount_type'],
                    'discount_type' => $product_discount['discount_type'],
                    'discount_on_item' => $product_discount['discount_amount'],
                    'discount_percentage' => $product_discount['discount_percentage'],

                    'variant' => json_encode($c['variant']),
                    'variation' => $foodVariation ? json_encode($variations) : json_encode($c['variation']),
                    'add_ons' => json_encode($addon_data['addons']),

                    'total_add_on_price' => round($addon_data['total_add_on_price'], config('round_up_to_digit')),
                    'addon_discount' => 0,

                    'created_at' => now(),
                    'updated_at' => now()
                ];


                $total_addon_price += $or_d['total_add_on_price'];
                $product_price += $price * $or_d['quantity'];
                $store_discount_amount += $or_d['discount_type'] != 'flash_sale' ? $or_d['discount_on_item'] * $or_d['quantity'] : 0;
                $flash_sale_admin_discount_amount += $or_d['discount_type'] == 'flash_sale' ? $product_discount['admin_discount_amount'] * $or_d['quantity'] : 0;
                $flash_sale_vendor_discount_amount += $or_d['discount_type'] == 'flash_sale' ? $product_discount['vendor_discount_amount'] * $or_d['quantity'] : 0;
                $order_details[] = $or_d;
                $addon_data[] = $addon_data['addons'];
            } else {
                return [
                    'status_code' => 403,
                    'code' => 'not_found',
                    'message' => translate('messages.product_not_found'),
                ];
            }
        }



        $discount = $store_discount_amount;
        $storeDiscount = Helpers::get_store_discount($store);
        if (isset($storeDiscount) && $discount_type != 'flash_sale') {
            $admin_discount = Helpers::checkAdminDiscount(price: $product_price, discount: $storeDiscount['discount'], max_discount: $storeDiscount['max_discount'], min_purchase: $storeDiscount['min_purchase']);

            $discount = max($discount, $admin_discount);

            if ($admin_discount > 0 &&  $discount == $admin_discount) {
                $discount_on_product_by = 'store_discount';
                foreach ($order_details as $key => $detail_data) {
                    $order_details[$key]['discount_on_product_by'] = $discount_on_product_by;
                    $order_details[$key]['discount_type'] = 'precentage';
                    $order_details[$key]['discount_percentage'] = $storeDiscount['discount'];
                    $order_details[$key]['discount_on_item'] =  Helpers::checkAdminDiscount(price: $product_price, discount: $storeDiscount['discount'], max_discount: $storeDiscount['max_discount'], min_purchase: $storeDiscount['min_purchase'], item_wise_price: $detail_data['price'] * $detail_data['quantity']);
                    // $order_details[$key]['addon_discount'] = 0 ?? Helpers::checkAdminDiscount(price: $product_price , discount: $storeDiscount['discount'], max_discount: $storeDiscount['max_discount'], min_purchase: $storeDiscount['min_purchase'], item_wise_price: $detail_data['total_add_on_price']);
                }
            }
        }


        return [
            'order_details' => $order_details,
            'total_addon_price' => $total_addon_price,
            'product_price' => $product_price,
            'store_discount_amount' => $discount,
            'discount_on_product_by' => $discount_on_product_by == 'store_discount' ? 'admin' : 'vendor',
            'flash_sale_admin_discount_amount' => $flash_sale_admin_discount_amount,
            'flash_sale_vendor_discount_amount' => $flash_sale_vendor_discount_amount,
            'product_data' => $product_data

        ];
    }

    private function makePosOrderDetails($carts, $request, $store)
    {
        $total_addon_price = 0;
        $product_price = 0;
        $store_discount_amount = 0;
        $flash_sale_vendor_discount_amount = 0;
        $flash_sale_admin_discount_amount = 0;
        $product_data = [];
        $order_details = [];
        $discount_on_product_by = 'vendor';
        $discount_type = '';
        foreach ($carts as $c) {
            $variations = [];
            if (is_array($c)) {
                //                dd($c);
                $isCampaign = false;
                if (isset($c['item_type']) && ($c['item_type'] === 'App\Models\ItemCampaign' || $c['item_type'] === 'AppModelsItemCampaign')) {
                    $product = ItemCampaign::with('module')->active()->find($c['item_id']);
                    $isCampaign = true;
                } else {
                    $product = Item::with('module')->active()->find($c['item_id'] ?? $c['id']);
                }

                if ($product) {
                    if ($product->store_id != $store->id) {
                        return [
                            'status_code' => 403,
                            'code' => 'different_stores',
                            'message' => translate('messages.Please_select_items_from_the_same_store'),
                        ];
                    }

                    if ($product?->pharmacy_item_details?->is_prescription_required == '1' && empty($request->file('order_attachment'))) {
                        return [
                            'status_code' => 403,
                            'code' => 'prescription',
                            'message' => translate('messages.prescription_is_required_for_this_order'),
                        ];
                    }

                    if ($product?->maximum_cart_quantity && $c['quantity'] > $product?->maximum_cart_quantity) {
                        return [
                            'status_code' => 403,
                            'code' => 'quantity',
                            'message' => translate('messages.maximum_cart_quantity_limit_over'),
                        ];
                    }


                    $foodVariation = false;
                    if ($product?->module?->module_type == 'food') {
                        $foodVariation = true;
                        $product_variations = json_decode($product->food_variations, true);

                        if ($product_variations && count($product_variations)) {
                            $variation_data = Helpers::get_varient($product_variations, $c['variations']);
                            $price = $product['price'] + $variation_data['price'];
                            $variations = $variation_data['variations'];
                        } else {
                            $price = $product['price'];
                        }
                    } else {
                        if (count(json_decode($product['variations'], true)) > 0 && count($c['variations']) > 0) {
                            $variant_data = Helpers::pos_variation_price($product, json_encode($c['variations']));
                            $price = $variant_data['price'];
                            $stock = $variant_data['stock'];
                        } else {
                            $price = $product['price'];
                            $stock = $product?->stock;
                        }

                        if (config('module.' . $product->module->module_type)['stock']) {
                            if ($c['quantity'] > $stock) {

                                return [
                                    'status_code' => 403,
                                    'code' => 'stock',
                                    'message' => $isCampaign ? $product?->title : $product?->name . ' ' . translate('messages.is_out_of_stock')
                                ];
                            }

                            $product_data[] = [
                                'item' => clone $product,
                                'quantity' => $c['quantity'],
                                'variant' => count($c['variations']) > 0 ? $c['variations']['type'] : null
                            ];
                        }
                    }

                    $product = Helpers::product_data_formatting($product, false, false, app()->getLocale());
                    $addon_data = Helpers::calculate_addon_price(AddOn::whereIn('id', $c['add_ons'])->get(), $c['add_on_qtys']);
                    $product_discount = Helpers::product_discount_calculate($product, $price, $store, false);

                    $discount_type = $product_discount['discount_type'];

                    $or_d = [
                        'item_id' => $isCampaign ? null : $c['id'],
                        'item_campaign_id' => $isCampaign ? $c['id'] : null,
                        'item_details' => json_encode($product),
                        'quantity' => $c['quantity'],
                        'price' => round($price, config('round_up_to_digit')),

                        'category_id' => collect(is_string($product->category_ids) ? json_decode($product->category_ids, true) : $product->category_ids)->firstWhere('position', 1)['id'] ?? null,
                        // 'tax_amount' => round(Helpers::tax_calculate($product, $price), config('round_up_to_digit')),
                        'tax_amount' => 0,
                        'tax_status' => null,

                        'discount_on_product_by' => $product_discount['discount_type'],
                        'discount_type' => $product_discount['discount_type'],
                        'discount_on_item' => $product_discount['discount_amount'],
                        'discount_percentage' => $product_discount['discount_percentage'],

                        'variant' => json_encode($c['variant']),
                        'variation' => $foodVariation ? json_encode($variations) : json_encode($c['variations']),
                        'add_ons' => json_encode($addon_data['addons']),

                        'total_add_on_price' => round($addon_data['total_add_on_price'], config('round_up_to_digit')),
                        'addon_discount' => 0,

                        'created_at' => now(),
                        'updated_at' => now()
                    ];


                    $total_addon_price += $or_d['total_add_on_price'];
                    $product_price += $price * $or_d['quantity'];
                    $store_discount_amount += $or_d['discount_type'] != 'flash_sale' ? $or_d['discount_on_item'] * $or_d['quantity'] : 0;
                    $flash_sale_admin_discount_amount += $or_d['discount_type'] == 'flash_sale' ? $product_discount['admin_discount_amount'] * $or_d['quantity'] : 0;
                    $flash_sale_vendor_discount_amount += $or_d['discount_type'] == 'flash_sale' ? $product_discount['vendor_discount_amount'] * $or_d['quantity'] : 0;
                    $order_details[] = $or_d;
                    $addon_data[] = $addon_data['addons'];
                } else {
                    return [
                        'status_code' => 403,
                        'code' => 'not_found',
                        'message' => translate('messages.product_not_found'),
                    ];
                }
            }
        }



        $discount = $store_discount_amount;
        $storeDiscount = Helpers::get_store_discount($store);
        if (isset($storeDiscount) && $discount_type != 'flash_sale') {
            $admin_discount = Helpers::checkAdminDiscount(price: $product_price, discount: $storeDiscount['discount'], max_discount: $storeDiscount['max_discount'], min_purchase: $storeDiscount['min_purchase']);

            $discount = max($discount, $admin_discount);

            if ($admin_discount > 0 &&  $discount == $admin_discount) {
                $discount_on_product_by = 'store_discount';
                foreach ($order_details as $key => $detail_data) {
                    $order_details[$key]['discount_on_product_by'] = $discount_on_product_by;
                    $order_details[$key]['discount_type'] = 'precentage';
                    $order_details[$key]['discount_percentage'] = $storeDiscount['discount'];
                    $order_details[$key]['discount_on_item'] =  Helpers::checkAdminDiscount(price: $product_price, discount: $storeDiscount['discount'], max_discount: $storeDiscount['max_discount'], min_purchase: $storeDiscount['min_purchase'], item_wise_price: $detail_data['price'] * $detail_data['quantity']);

                    // $order_details[$key]['addon_discount'] =  Helpers::checkAdminDiscount(price: $product_price + $total_addon_price, discount: $storeDiscount['discount'], max_discount: $storeDiscount['max_discount'], min_purchase: $storeDiscount['min_purchase'], item_wise_price: $total_addon_price);
                }
            }
        }


        return [
            'order_details' => $order_details,
            'total_addon_price' => $total_addon_price,
            'product_price' => $product_price,
            'store_discount_amount' => $discount,
            'discount_on_product_by' => $discount_on_product_by == 'store_discount' ? 'admin' : 'vendor',
            'flash_sale_admin_discount_amount' => $flash_sale_admin_discount_amount,
            'flash_sale_vendor_discount_amount' => $flash_sale_vendor_discount_amount,
            'product_data' => $product_data

        ];
    }
    private function makeEditOrderDetails($carts, $request, $store)
    {
        $total_addon_price = 0;
        $product_price = 0;
        $store_discount_amount = 0;
        $flash_sale_vendor_discount_amount = 0;
        $flash_sale_admin_discount_amount = 0;
        $product_data = [];
        $order_details = [];
        $discount_on_product_by = 'vendor';
        if($carts->isEmpty()){
            return [
                'status_code' => 403,
                'code' => 'not_found',
                'message' => translate('Cart is empty'),
            ];
        }
        foreach ($carts as $c) {
            $variations = [];

            if (!isset($c['status']) || $c['status'] !== false) {
                $isCampaign = false;
                if (isset($c['item_type']) && ($c['item_type'] === 'App\Models\ItemCampaign' || $c['item_type'] === 'AppModelsItemCampaign')) {
                    $product = ItemCampaign::with('module')->active()->find($c['item_id']);
                    $isCampaign = true;
                } else {
                    $product = Item::with('module')->active()->find($c['item_id'] ?? $c['id']);
                }

                if ($product) {
                    if ($product->store_id != $store->id) {
                        return [
                            'status_code' => 403,
                            'code' => 'different_stores',
                            'message' => translate('messages.Please_select_items_from_the_same_store'),
                        ];
                    }

                    if ($product?->pharmacy_item_details?->is_prescription_required == '1' && empty($request->file('order_attachment'))) {
                        return [
                            'status_code' => 403,
                            'code' => 'prescription',
                            'message' => translate('messages.prescription_is_required_for_this_order'),
                        ];
                    }

                    if ($product?->maximum_cart_quantity && $c['quantity'] > $product?->maximum_cart_quantity) {
                        return [
                            'status_code' => 403,
                            'code' => 'quantity',
                            'message' => translate('messages.maximum_cart_quantity_limit_over'),
                        ];
                    }


                    $foodVariation = false;
                    if ($product?->module?->module_type == 'food') {
                        $foodVariation = true;
                        $product_variations = json_decode($product->food_variations, true);

                        if ($product_variations && count($product_variations)) {
                            $variation_data = Helpers::get_edit_varient($product_variations, json_decode($c['variation'], true));
                            $price = $product['price'] + $variation_data['price'];
                            $variations = $variation_data['variations'];
                        } else {
                            $price = $product['price'];
                        }
                    } else {
                        if (
                            is_array(json_decode($product['variations'], true)) && count(json_decode($product['variations'], true)) > 0 &&
                            is_array($c['variation']) && count($c['variation']) > 0
                        ) {
                            $variant_data = Helpers::variation_price($product, json_encode($c['variation']));
                            $price = $variant_data['price'];
                            $stock = $variant_data['stock'];
                        } else {
                            $price = $product['price'];
                            $stock = $product?->stock;
                        }

                        if (config('module.' . $product->module->module_type)['stock']) {
                            if ($c['quantity'] > $stock) {

                                return [
                                    'status_code' => 403,
                                    'code' => 'stock',
                                    'message' => $isCampaign ? $product?->title : $product?->name . ' ' . translate('messages.is_out_of_stock')
                                ];
                            }
                            $product_data[] = [
                                'item' => clone $product,
                                'quantity' => $c['quantity'],
                                'variant' => is_array($c['variation']) && count($c['variation']) > 0 ? $c['variation'][0]['type'] : null
                            ];
                        }
                    }

                    $product = Helpers::product_data_formatting($product, false, false, app()->getLocale());


                    $input = $c['add_ons'] ?? null;

                    $addonIds = [];
                    $addonQuantities = [];

                    if (is_string($input)) {
                        $decoded = json_decode($input, true);

                        if (is_array($decoded)) {
                            if (is_numeric(data_get($decoded,0))) {

                                $addonIds = $decoded;
                                $addonQuantities = $c['add_on_qtys'] ?? [];
                            } else {

                                $addonIds = array_column($decoded, 'id');
                                $addonQuantities = array_column($decoded, 'quantity');
                            }
                        }
                    } elseif (is_array($input)) {
                        if (is_numeric(data_get($input,0))) {

                            $addonIds = $input;
                            $addonQuantities = $c['add_on_qtys'] ?? [];
                        } else {

                            $addonIds = array_column($input, 'id');
                            $addonQuantities = array_column($input, 'quantity');
                        }
                    }

                    $addonIds = array_unique($addonIds);
                    $addon_data = Helpers::calculate_addon_price(
                        AddOn::whereIn('id', $addonIds)->get(),
                        $addonQuantities
                    );

                    $product_discount = Helpers::product_discount_calculate($product, $price, $store, false);


                    $discount_type = $product_discount['discount_type'];

                    $or_d = [
                        'cart_id' => $c['id'],
                        'item_id' => $isCampaign ? null : $c['item_id'],
                        'item_campaign_id' => $isCampaign ? $c['item_id'] : null,
                        'item_details' => json_encode($product),
                        'quantity' => $c['quantity'],
                        'price' => round($price, config('round_up_to_digit')),

                        'category_id' => collect(is_string($product->category_ids) ? json_decode($product->category_ids, true) : $product->category_ids)->firstWhere('position', 1)['id'] ?? null,
                        // 'tax_amount' => round(Helpers::tax_calculate($product, $price), config('round_up_to_digit')),
                        'tax_amount' => 0,
                        'tax_status' => null,

                        'discount_on_product_by' => $product_discount['discount_type'],
                        'discount_type' => $product_discount['discount_type'],
                        'discount_on_item' => $product_discount['discount_amount'],
                        'discount_percentage' => $product_discount['discount_percentage'],

                        'variant' => json_encode($c['variant']),
                        'variation' => $foodVariation ? json_encode($variations) : json_encode($c['variation']),
                        'add_ons' => json_encode($addon_data['addons']),

                        'total_add_on_price' => round($addon_data['total_add_on_price'], config('round_up_to_digit')),
                        'addon_discount' => 0,

                        'created_at' => now(),
                        'updated_at' => now()
                    ];


                    $total_addon_price += $or_d['total_add_on_price'];
                    $product_price += $price * $or_d['quantity'];
                    $store_discount_amount += $or_d['discount_type'] != 'flash_sale' ? $or_d['discount_on_item'] * $or_d['quantity'] : 0;
                    $flash_sale_admin_discount_amount += $or_d['discount_type'] == 'flash_sale' ? $product_discount['admin_discount_amount'] * $or_d['quantity'] : 0;
                    $flash_sale_vendor_discount_amount += $or_d['discount_type'] == 'flash_sale' ? $product_discount['vendor_discount_amount'] * $or_d['quantity'] : 0;
                    $order_details[] = $or_d;
                    $addon_data[] = $addon_data['addons'];
                } else {
                    return [
                        'status_code' => 403,
                        'code' => 'not_found',
                        'message' => translate('messages.product_not_found'),
                    ];
                }
            }
        }
        $discount = $store_discount_amount;
        $storeDiscount = Helpers::get_store_discount($store);
        if (isset($storeDiscount) && $discount_type != 'flash_sale') {
            $admin_discount = Helpers::checkAdminDiscount(price: $product_price, discount: $storeDiscount['discount'], max_discount: $storeDiscount['max_discount'], min_purchase: $storeDiscount['min_purchase']);

            $discount = max($discount, $admin_discount);


            if ($admin_discount > 0 &&  $discount == $admin_discount) {
                $discount_on_product_by = 'store_discount';
                foreach ($order_details as $key => $detail_data) {
                    $order_details[$key]['discount_on_product_by'] = $discount_on_product_by;
                    $order_details[$key]['discount_type'] = 'precentage';
                    $order_details[$key]['discount_percentage'] = $storeDiscount['discount'];
                    $order_details[$key]['discount_on_item'] =  Helpers::checkAdminDiscount(price: $product_price, discount: $storeDiscount['discount'], max_discount: $storeDiscount['max_discount'], min_purchase: $storeDiscount['min_purchase'], item_wise_price: $detail_data['price'] * $detail_data['quantity']);

                    // $order_details[$key]['addon_discount'] =  Helpers::checkAdminDiscount(price: $product_price + $total_addon_price, discount: $storeDiscount['discount'], max_discount: $storeDiscount['max_discount'], min_purchase: $storeDiscount['min_purchase'], item_wise_price: $total_addon_price);
                }
            }
        }
        return [
            'order_details' => $order_details,
            'total_addon_price' => $total_addon_price,
            'product_price' => $product_price,
            'store_discount_amount' => $discount,
            'discount_on_product_by' => $discount_on_product_by == 'store_discount' ? 'admin' : 'vendor',
            'flash_sale_admin_discount_amount' => $flash_sale_admin_discount_amount,
            'flash_sale_vendor_discount_amount' => $flash_sale_vendor_discount_amount,
            'product_data' => $product_data

        ];
    }

    public function getCalculatedTax($request)
    {
        if (gettype($request->is_prescription) == "string") {
            if ($request->is_prescription == "true") {
                $request->is_prescription = true;
            } else {
                $request->is_prescription = false;
            }
        }
        $product_price = $request->order_amount ?? 0;
        $coupon = null;
        $ref_bonus_amount = 0;
        $total_addon_price = 0;
        $store_discount_amount = 0;
        $flash_sale_admin_discount_amount = 0;
        $flash_sale_vendor_discount_amount = 0;
        $coupon_discount_amount = 0;
        $order_details = [];


        $order = new Order();
        $order->user_id = $request->user ? $request->user->id : $request['guest_id'];
        $order->is_guest = $request->user ? 0 : 1;
        $order->store_id = $request['store_id'];



        $additionalCharges = [];
        $settings = BusinessSetting::whereIn('key', [
            'additional_charge_status',
            'additional_charge',
            'extra_packaging_data',
        ])->pluck('value', 'key');


        $additional_charge_status  = $settings['additional_charge_status'] ?? null;
        $additional_charge         = $settings['additional_charge'] ?? null;

        $extra_packaging_data_raw  = $settings['extra_packaging_data'] ?? '';
        $extra_packaging_data      = json_decode($extra_packaging_data_raw, true) ?? [];

        if ($additional_charge_status == 1) {
            // $additionalCharges['tax_on_additional_charge'] = $additional_charge ?? 0;
        }


        if ($request->order_type !== 'parcel') {

            $store = Store::with(['discount', 'store_sub'])->where('id', $request->store_id)->first();

            $couponData = $this->getCouponData($request);
            if (data_get($couponData, 'status_code') === 403) {

                return response()->json([
                    'errors' => [
                        ['code' => data_get($couponData, 'code'), 'message' => data_get($couponData, 'message')]
                    ]
                ], data_get($couponData, 'status_code'));
            } else {
                $coupon = data_get($couponData, 'coupon');
            }


            if (!$request->is_prescription) {

                $extra_packaging_amount =  (!empty($extra_packaging_data) && $request?->extra_packaging_amount > 0 && $store && ($extra_packaging_data[$store->module->module_type] == '1') && ($store?->storeConfig?->extra_packaging_status == '1')) ? $store?->storeConfig?->extra_packaging_amount : 0;

                if ($extra_packaging_amount > 0) {
                    $additionalCharges['tax_on_packaging_charge'] =  $extra_packaging_amount;
                }

                $carts = Cart::where('user_id', $order->user_id)->where('is_guest', $order->is_guest)->where('module_id', getModuleId($request->header('moduleId')))
                    ->when(isset($request->is_buy_now) && $request->is_buy_now == 1 && $request->cart_id, function ($query) use ($request) {
                        return $query->where('id', $request->cart_id);
                    })
                    ->get()->map(function ($data) {
                        $data->add_on_ids = json_decode($data->add_on_ids, true);
                        $data->add_on_qtys = json_decode($data->add_on_qtys, true);
                        $data->variation = json_decode($data->variation, true);
                        return $data;
                    });

                if (isset($request->is_buy_now) && $request->is_buy_now == 1) {
                    $carts = json_decode($request['cart'], true);
                }

                $order_details = $this->makeOrderDetails($carts, $request, $order, $store);
                if (data_get($order_details, 'status_code') === 403) {

                    return response()->json([
                        'errors' => [
                            ['code' => data_get($order_details, 'code'), 'message' => data_get($order_details, 'message')]
                        ]
                    ], data_get($order_details, 'status_code'));
                }

                $total_addon_price = $order_details['total_addon_price'];
                $product_price = $order_details['product_price'];
                $store_discount_amount = $order_details['store_discount_amount'];
                $flash_sale_admin_discount_amount = $order_details['flash_sale_admin_discount_amount'];
                $flash_sale_vendor_discount_amount = $order_details['flash_sale_vendor_discount_amount'];
                $order_details = $order_details['order_details'];
            }

            $coupon_discount_amount = $coupon ? CouponLogic::get_discount($coupon, $product_price + $total_addon_price - $store_discount_amount - $flash_sale_admin_discount_amount - $flash_sale_vendor_discount_amount) : 0;
        }

        $total_price = $product_price + $total_addon_price - $store_discount_amount - $flash_sale_admin_discount_amount - $flash_sale_vendor_discount_amount  - $coupon_discount_amount;


        if ($order->is_guest  == 0 && $order->user_id) {
            $user = User::withcount('orders')->find($order->user_id);
            $discount_data = Helpers::getCusromerFirstOrderDiscount(order_count: $user->orders_count, user_creation_date: $user->created_at,  refby: $user->ref_by, price: $total_price);
            if (data_get($discount_data, 'is_valid') == true &&  data_get($discount_data, 'calculated_amount') > 0) {
                $total_price = $total_price - data_get($discount_data, 'calculated_amount');
                $ref_bonus_amount = data_get($discount_data, 'calculated_amount');
            }
        }

        $totalDiscount = $store_discount_amount + $flash_sale_admin_discount_amount + $flash_sale_vendor_discount_amount  + $coupon_discount_amount +  $ref_bonus_amount;

        if ($request->order_type != 'parcel' && $request->is_prescription == false) {

            $finalCalculatedTax =  Helpers::getFinalCalculatedTax($order_details, $additionalCharges, $totalDiscount, $total_price, $order->store_id, false);
            $data = [
                'tax_amount' => $finalCalculatedTax['tax_amount'],
                'tax_status' => $finalCalculatedTax['tax_status'],
                'tax_included' => $finalCalculatedTax['tax_included'],
            ];
        }

        if ($request->order_type == 'parcel' || $request->is_prescription == true) {

            if ($request->order_type == 'parcel') {
                $productIds[] = [
                    'id' => 1,
                    'original_price' => $product_price,
                    'quantity' => 1,
                    'category_id' =>  $request->parcel_category_id,
                    'discount' => 0,
                    'discount_type' => '',
                    'after_discount_final_price' => $product_price,
                    'is_campaign_item' => false,
                ];
            }

            $finalCalculatedTax =  \Modules\TaxModule\Services\CalculateTaxService::getCalculatedTax(
                amount: $product_price,
                productIds: $productIds ?? [],
                taxPayer: $request->is_prescription == true ? 'prescription' : 'parcel',
                storeData: true,
                additionalCharges: $additionalCharges,
                addonIds: [],
                orderId: null,
                storeId: null
            );
            $data = [
                'tax_amount' => $finalCalculatedTax['totalTaxamount'],
                'tax_included' => $finalCalculatedTax['include'],
                'tax_status' => $finalCalculatedTax['include'] ?  'included' : 'excluded'
            ];
        }



        return response()->json($data, 200);
    }
    public function setPosCalculatedTax($store, $storeData = false)
    {
        $additionalCharges = [];
        $settings = BusinessSetting::whereIn('key', [
            'additional_charge_status',
            'additional_charge',
            'extra_packaging_data',
        ])->pluck('value', 'key');

        $additional_charge_status  = $settings['additional_charge_status'] ?? null;
        $additional_charge         = $settings['additional_charge'] ?? null;

        // if ($additional_charge_status == 1) {
        //     $additionalCharges['tax_on_additional_charge'] = $additional_charge ?? 0;
        // }

        $carts = session()->get('cart');
        $order_details = $this->makePosOrderDetails($carts, null, $store);
        $total_addon_price = $order_details['total_addon_price'];
        $product_price = $order_details['product_price'];
        $store_discount_amount = $order_details['store_discount_amount'];
        $flash_sale_admin_discount_amount = $order_details['flash_sale_admin_discount_amount'];
        $flash_sale_vendor_discount_amount = $order_details['flash_sale_vendor_discount_amount'];
        $order_details = $order_details['order_details'];

        $totalDiscount = $store_discount_amount + $flash_sale_admin_discount_amount + $flash_sale_vendor_discount_amount;

        $price = $product_price + $total_addon_price - $totalDiscount ?? 0;
        $finalCalculatedTax =  Helpers::getFinalCalculatedTax(
            $order_details,
            $additionalCharges,
            $totalDiscount,
            $price,
            $store->id,
            $storeData
        );

        session()->put('tax_amount', $finalCalculatedTax['tax_amount']);
        session()->put('tax_included', $finalCalculatedTax['tax_included']);

        $data = [
            'tax_amount' => $finalCalculatedTax['tax_amount'],
            'tax_status' => $finalCalculatedTax['tax_status'],
            'tax_included' => $finalCalculatedTax['tax_included'],
        ];
        return response()->json($data, 200);
    }
    public function setOrderEditCalculatedTax($store, $storeData = false, $order_id = null)
    {
        if ($order_id) {
            $order = Order::find($order_id);
        }
        $coupon = null;
        $additionalCharges = [];
        $settings = BusinessSetting::whereIn('key', [
            'additional_charge_status',
            'additional_charge',
            'extra_packaging_data',
        ])->pluck('value', 'key');

        $additional_charge_status  = $settings['additional_charge_status'] ?? null;
        $additional_charge         = $settings['additional_charge'] ?? null;

        if ($additional_charge_status == 1) {
            // $additionalCharges['tax_on_additional_charge'] = $additional_charge ?? 0;
        }

        $carts = session()->get('order_cart');

        $order_details = $this->makeEditOrderDetails($carts, null, $store);

        if (data_get($order_details, 'status_code') === 403) {

            return response()->json([
                'errors' => [
                    ['code' => data_get($order_details, 'code'), 'message' => data_get($order_details, 'message')]
                ]
            ], data_get($order_details, 'status_code'));
        }

        $total_addon_price = $order_details['total_addon_price'];
        $product_price = $order_details['product_price'];
        $store_discount_amount = $order_details['store_discount_amount'];
        $flash_sale_admin_discount_amount = $order_details['flash_sale_admin_discount_amount'];
        $flash_sale_vendor_discount_amount = $order_details['flash_sale_vendor_discount_amount'];

        $discount_on_product_by= $order_details['discount_on_product_by'];
        $order_details = $order_details['order_details'];
        if ($order?->coupon_code) {
            $coupon = Coupon::where(['code' => $order->coupon_code])->first();
        }


        $coupon_discount_amount = $coupon ? CouponLogic::get_discount($coupon, $product_price + $total_addon_price - $store_discount_amount) : 0;

        $totalDiscount = $store_discount_amount + $flash_sale_admin_discount_amount + $flash_sale_vendor_discount_amount + $coupon_discount_amount;

        $price = $product_price + $total_addon_price - $totalDiscount;
        $finalCalculatedTax =  Helpers::getFinalCalculatedTax(
            $order_details,
            $additionalCharges,
            $totalDiscount,
            $price,
            $store->id,
            $storeData
        );
        session()->put('edit_tax_amount', $finalCalculatedTax['tax_amount']);
        session()->put('edit_tax_included', $finalCalculatedTax['tax_included']);
        session()->put('discount_on_product_by_session', $discount_on_product_by == 'admin' ? 'store_discount' : 'vendor');

        $data = [
            'tax_amount' => $finalCalculatedTax['tax_amount'],
            'tax_status' => $finalCalculatedTax['tax_status'],
            'tax_included' => $finalCalculatedTax['tax_included'],
            'store_discount_amount'=>$store_discount_amount,
            'discount_on_product_by'=>$discount_on_product_by == 'admin' ? 'store_discount' : 'vendor',
        ];
        return response()->json($data, 200);
    }

    public function getSurgePrice($zoneId, $moduleId, $datetime) {

        $data = $this->getSurgePriceValue($zoneId, $moduleId, $datetime);

        return response()->json($data, 200);
    }

    private function getSurgePriceValue($zoneId, $moduleId, $datetime)
    {
        $carbon = Carbon::parse($datetime);
        $dateStr = $carbon->format('Y-m-d');
        $timeStr = $carbon->format('H:i:s');
        $weekday = $carbon->format('l');

        // Check exact date in surge_price_dates table
        $surgeDate = DB::table('surge_price_dates')
            ->where('zone_id', $zoneId)
            ->where('module_id', $moduleId)
            ->where('applicable_date', $dateStr)
            ->where(function ($query) use ($timeStr) {
                $query->whereBetween('start_time', [$timeStr, $timeStr])
                    ->orWhere(function ($q) use ($timeStr) {
                        $q->where('start_time', '<=', $timeStr)
                            ->where('end_time', '>=', $timeStr);
                    });
            })
            ->first();

        if ($surgeDate) {
            $surge_price = SurgePrice::
            where('status',1)
                ->where('id', $surgeDate->surge_price_id)
                ->first();
            if($surge_price){
                return [
                    'title' => $surge_price->surge_price_name,
                    'customer_note' => $surge_price->customer_note,
                    'customer_note_status' => $surge_price->customer_note_status,
                    'price' => $surge_price->price,
                    'price_type' => $surge_price->price_type,
                ];
            }
        }

        //Check permanent weekly surge_prices
        $permanentSurge = SurgePrice::
            where('status', 1)
            ->where('zone_id', $zoneId)
            ->whereJsonContains('module_ids', $moduleId)
            ->where('duration_type', 'weekly')
            ->where('is_permanent', 1)
            ->whereJsonContains('weekly_days', $weekday)
            ->where(function ($query) use ($timeStr) {
                $query->whereBetween('start_time', [$timeStr, $timeStr])
                    ->orWhere(function ($q) use ($timeStr) {
                        $q->where('start_time', '<=', $timeStr)
                            ->where('end_time', '>=', $timeStr);
                    });
            })
            ->first();

        if ($permanentSurge) {
            return [
                'title' => $permanentSurge->surge_price_name,
                'customer_note' => $permanentSurge->customer_note,
                'customer_note_status' => $permanentSurge->customer_note_status,
                'price' => $permanentSurge->price,
                'price_type' => $permanentSurge->price_type,
            ];
        }

        return [
            'title' => '',
            'customer_note' => '',
            'customer_note_status' => 0,
            'price' => 0,
            'price_type' => 'amount',
        ];
    }
}
