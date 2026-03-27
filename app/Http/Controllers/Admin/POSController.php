<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Traits\PlaceNewOrder;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\CentralLogics\CustomerLogic;
use App\CentralLogics\ProductLogic;
use App\Mail\OrderVerificationMail;
use App\Models\Store;
use App\Mail\PlaceOrder;
use App\Models\BusinessSetting;
use App\Models\DMVehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Scopes\StoreScope;
use Illuminate\Support\Facades\Config;

class POSController extends Controller
{
    use PlaceNewOrder;
    public function index(Request $request)
    {
        $time = Carbon::now()->toTimeString();
        $category = $request->query('category_id', 0);
        $module_id = Config::get('module.current_module_id');
        $store_id = $request->query('store_id', null);
        $categories = Category::active()->module(Config::get('module.current_module_id'))->get();
        $store = Store::active()->with('store_sub')->find($store_id);
        if(!$store && $request->has('store_id')){
            Toastr::error(translate('messages.Store_is_not_available'));
            return back();
        }
        $search = $request->query('search', false);
        $key = explode(' ', $search);

        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            if(!isset($cart['store_id']) || $cart['store_id'] != $store_id) {
                session()->forget('cart');
                session()->forget('tax_amount');
                session()->forget('tax_included');
                session()->forget('cart');
                session()->forget('address');
                session()->forget('cart_product_ids');
            }
        }

        if (empty(session('cart')) || count(session('cart')) === 0) {
            session()->forget('tax_amount');
            session()->forget('tax_included');
        }

        $products = Item::withoutGlobalScope(StoreScope::class)->active()
        ->when($category, function($query)use($category){
            $query->whereHas('category',function($q)use($category){
                return $q->whereId($category)->orWhere('parent_id', $category);
            });
        })
        ->when($search, function($query)use($key){
            return $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })
        ->whereHas('store', function($query)use($store_id, $module_id){
            return $query->where(['id'=>$store_id, 'module_id'=>$module_id]);
        });
        if(Config::get('module.current_module_type') == 'food'){
            $products=  $products->available($time);
        }
        $products=  $products->latest()->paginate(10);
        return view('admin-views.pos.index', compact('categories', 'products','category', 'search', 'store', 'module_id'));
    }

    public function quick_view(Request $request)
    {
        $product = Item::withoutGlobalScope(StoreScope::class)->with('store')->findOrFail($request->product_id);

        return response()->json([
            'success' => 1,
            'view' => view('admin-views.pos._quick-view-data', compact('product'))->render(),
        ]);
    }

    public function quick_view_card_item(Request $request)
    {
        $product = Item::withoutGlobalScope(StoreScope::class)->findOrFail($request->product_id);
        $item_key = $request->item_key;
        $cart_item = session()->get('cart')[$item_key];

        return response()->json([
            'success' => 1,
            'view' => view('admin-views.pos._quick-view-cart-item', compact('product', 'cart_item', 'item_key'))->render(),
        ]);
    }

    public function variant_price(Request $request)
    {
        $product = Item::withoutGlobalScope(StoreScope::class)->with('store')->find($request->id);
        if($product->module->module_type == 'food'){
            $price = $product->price;
            $addon_price = 0;
            if ($request['addon_id']) {
                foreach ($request['addon_id'] as $id) {
                    $addon_price += $request['addon-price' . $id] * $request['addon-quantity' . $id];
                }
            }
            $product_variations = json_decode($product->food_variations, true);
            if ($request->variations && $product_variations && count($product_variations)) {

                $price_total =  $price + Helpers::food_variation_price($product_variations, $request->variations);
                $price= $price_total - Helpers::product_discount_calculate($product, $price_total, $product->store)['discount_amount'];
            } else {
                $price = $product->price - Helpers::product_discount_calculate($product, $product->price, $product->store)['discount_amount'];
            }
        }else{

            $str = '';
            $quantity = 0;
            $price = 0;
            $addon_price = 0;

            foreach (json_decode($product->choice_options) as $key => $choice) {
                if ($str != null) {
                    $str .= '-' . str_replace(' ', '', $request[$choice->name]);
                } else {
                    $str .= str_replace(' ', '', $request[$choice->name]);
                }
            }

            if($request['addon_id'])
            {
                foreach($request['addon_id'] as $id)
                {
                    $addon_price+= $request['addon-price'.$id]*$request['addon-quantity'.$id];
                }
            }

            if ($str != null) {
                $count = count(json_decode($product->variations));
                for ($i = 0; $i < $count; $i++) {
                    if (json_decode($product->variations)[$i]->type == $str) {
                        $price = json_decode($product->variations)[$i]->price - Helpers::product_discount_calculate($product, json_decode($product->variations)[$i]->price,$product->store)['discount_amount'];
                    }
                }
            } else {
                $price = $product->price - Helpers::product_discount_calculate($product, $product->price,$product->store)['discount_amount'];
            }
        }

        return array('price' => Helpers::format_currency(($price * $request->quantity)+$addon_price));
    }

    public function addDeliveryInfo(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'contact_person_name' => 'required',
            'contact_person_number' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => 'delivery',
            'address' => $request->address,
            'floor' => $request->floor,
            'road' => $request->road,
            'house' => $request->house,
            'distance' => $request->distance??0,
            'delivery_fee' => $request->delivery_fee?:0,
            'longitude' => (string)$request->longitude,
            'latitude' => (string)$request->latitude,
        ];

        $request->session()->put('address', $address);

        return response()->json([
            'data' => $address,
            'view' => view('admin-views.pos._address', compact('address'))->render(),
        ]);
    }

    public function item_stock_view(Request $request)
    {
        $product = Item::withoutGlobalScope(StoreScope::class)->with('store')->findOrFail($request->id);
        $selected_item = $request->all();
        $stock= $this->get_stocks($product,$selected_item);
            return response()->json([
                'view' => view('admin-views.pos._item-stock-view', compact('product','selected_item','stock' ))->render(),
            ]);
    }

    public function item_stock_view_update(Request $request)
    {
        $product = Item::withoutGlobalScope(StoreScope::class)->with('store')->findOrFail($request->id);
        $selected_item = $request->all();
        $item_key = $request->cart_item_key;
        $cart_item = session()->get('cart')[$item_key];
        $stock= $this->get_stocks($product,$selected_item);
        return response()->json([
            'success' => 1,
            'view' => view('admin-views.pos._quick-view-cart-item', compact('product', 'cart_item', 'item_key' ,'stock','selected_item'))->render(),
        ]);
    }


    private function get_stocks($product,$selected_item){
        try {
            if($product->module->module_type == 'food'){
                return null;
            }
            $choice_options=   json_decode($product?->choice_options, true);
            $variation=  json_decode($product?->variations, true);

            if(is_array($choice_options) && is_array($variation)  &&  count($choice_options) == 0 && count($variation) == 0 ){
                return $product->stock ?? null ;
            }

            $choiceNames = array_column($choice_options, 'name');
            $variations = array_map(function ($choiceName) use ($selected_item) {
                return str_replace(' ', '', $selected_item[$choiceName]);
            }, $choiceNames);
            $resultString = implode('-', $variations);
            $stockVariations = json_decode($product->variations, true);
            foreach ($stockVariations as $variation) {
                if ($variation['type'] == $resultString) {
                    $stock = $variation['stock'];
                    break;
                }
            }
        } catch (\Throwable $th) {
            info($th->getMessage());
        }

        return $stock ?? null ;
    }

    public function addToCart(Request $request)
    {
        $product = Item::withoutGlobalScope(StoreScope::class)->with('store')->find($request->id);
        $product_ids = $request->session()->has('cart_product_ids') ? $request->session()->get('cart_product_ids') : [];
        if($product->module->module_type == 'food'){
            $data = array();
            $data['id'] = $product->id;
            $str = '';
            $variations = [];
            $price = 0;
            $addon_price = 0;
            $variation_price=0;

            $product_variations = json_decode($product->food_variations, true);
            if ($request->variations && $product_variations && count($product_variations)) {
                foreach($request->variations  as $key=> $value ){

                    if($value['required'] == 'on' &&  isset($value['values']) == false){
                        return response()->json([
                            'data' => 'variation_error',
                            'message' => translate('Please select items from') . ' ' . $value['name'],
                        ]);
                    }
                    if(isset($value['values'])  && $value['min'] != 0 && $value['min'] > count($value['values']['label'])){
                        return response()->json([
                            'data' => 'variation_error',
                            'message' => translate('Please select minimum ').$value['min'].translate(' For ').$value['name'].'.',
                        ]);
                    }
                    if(isset($value['values']) && $value['max'] != 0 && $value['max'] < count($value['values']['label'])){
                        return response()->json([
                            'data' => 'variation_error',
                            'message' => translate('Please select maximum ').$value['max'].translate(' For ').$value['name'].'.',
                        ]);
                    }
                }
                $variation_data = Helpers::get_varient($product_variations, $request->variations);
                $variation_price = $variation_data['price'];
                $variations = $request->variations;
            }
            $data['variations'] = $variations;
            $data['variant'] = $str;

            $price = $product->price + $variation_price;
            $data['variation_price'] = $variation_price;
            $data['quantity'] = $request['quantity'];
            $data['price'] = $price;
            $data['name'] = $product->name;
            $data['discount'] = Helpers::product_discount_calculate($product, $price, $product->store)['discount_amount'];
            $data['image'] = $product->image;
            $data['image_full_url'] = $product->image_full_url;
            $data['storage'] = $product->storage?->toArray();
            $data['add_ons'] = [];
            $data['add_on_qtys'] = [];
            $data['maximum_cart_quantity'] = $product->maximum_cart_quantity;
            $data['stock_quantity'] = null;
            if ($request['addon_id']) {
                foreach ($request['addon_id'] as $id) {
                    $addon_price += $request['addon-price' . $id] * $request['addon-quantity' . $id];
                    $data['add_on_qtys'][] = $request['addon-quantity' . $id];
                }
                $data['add_ons'] = $request['addon_id'];
            }

            $data['addon_price'] = $addon_price;

            if ($request->session()->has('cart')) {
                $cart = $request->session()->get('cart', collect([]));
                if (isset($request->cart_item_key)) {
                    $cart[$request->cart_item_key] = $data;
                    $data = 2;
                } else {
                    $cart->push($data);
                }
            } else {
                $cart = collect([$data,'store_id'=>$product->store_id]);
                $request->session()->put('cart', $cart);
            }
            $product_ids[$product->id] = $request['quantity'];
            $request->session()->put('cart_product_ids', $product_ids);
        }else{

            $data = array();
            $data['id'] = $product->id;
            $str = '';
            $variations = [];
            $price = 0;
            $addon_price = 0;

            $selected_item = $request->all();
            $stock= $this->get_stocks($product,$selected_item);
            if($product?->maximum_cart_quantity > 0){
                if(((isset($stock) && min($stock, $product?->maximum_cart_quantity) < $request->quantity )||  $product?->maximum_cart_quantity <  $request->quantity  ) ){
                    return response()->json([
                        'data' => 0
                    ]);
                }
            }

            //Gets all the choice values of customer choice option and generate a string like Black-S-Cotton
            foreach (json_decode($product->choice_options) as $key => $choice) {
                $data[$choice->name] = $request[$choice->name];
                $variations[$choice->title] = $request[$choice->name];
                if ($str != null) {
                    $str .= '-' . str_replace(' ', '', $request[$choice->name]);
                } else {
                    $str .= str_replace(' ', '', $request[$choice->name]);
                }
            }
            $data['variations'] = $variations;
            $data['variant'] = $str;
            if ($request->session()->has('cart') && !isset($request->cart_item_key)) {
                if (count($request->session()->get('cart')) > 0) {
                    foreach ($request->session()->get('cart') as $key => $cartItem) {
                        if (is_array($cartItem) && $cartItem['id'] == $request['id'] && $cartItem['variant'] == $str) {
                            return response()->json([
                                'data' => 1
                            ]);
                        }
                    }

                }
            }
            //Check the string and decreases quantity for the stock
            if ($str != null) {
                $count = count(json_decode($product->variations));
                for ($i = 0; $i < $count; $i++) {
                    if (json_decode($product->variations)[$i]->type == $str) {
                        $price = json_decode($product->variations)[$i]->price;
                        $data['variations'] = json_decode($product->variations, true)[$i];
                    }
                }
            } else {
                $price = $product->price;
            }

            $data['stock_quantity'] = $stock;
            $data['quantity'] = $request['quantity'];
            $data['price'] = $price;
            $data['name'] = $product->name;
            $data['discount'] = Helpers::product_discount_calculate($product, $price,$product->store)['discount_amount'];
            $data['image'] = $product->image;
            $data['image_full_url'] = $product->image_full_url;
            $data['storage'] = $product->storage?->toArray();
            $data['add_ons'] = [];
            $data['add_on_qtys'] = [];
            $data['maximum_cart_quantity'] = $product->maximum_cart_quantity;

            if($request['addon_id'])
            {
                foreach($request['addon_id'] as $id)
                {
                    $addon_price+= $request['addon-price'.$id]*$request['addon-quantity'.$id];
                    $data['add_on_qtys'][]=$request['addon-quantity'.$id];
                }
                $data['add_ons'] = $request['addon_id'];
            }

            $data['addon_price'] = $addon_price;

            if ($request->session()->has('cart')) {
                $cart = $request->session()->get('cart', collect([]));

                if(!isset($cart['store_id']) || $cart['store_id'] != $product->store_id) {
                    return response()->json([
                        'data' => -1
                    ]);
                }
                if(isset($request->cart_item_key))
                {
                    $cart[$request->cart_item_key] = $data;
                    $data = 2;
                }
                else
                {
                    $cart->push($data);
                }


            } else {
                $cart = collect([$data]);
                $cart->put('store_id', $product->store_id);
                $request->session()->put('cart', $cart);
            }
            $product_ids[$product->id] = $request['quantity'];
            $request->session()->put('cart_product_ids', $product_ids);
        }

        $this->setPosCalculatedTax($product->store);
        return response()->json([
            'data' => $data
        ]);
    }

    public function single_items(Request $request)
    {
        $time = Carbon::now()->toTimeString();
        $category = $request->category_id??0;
        $module_id = Config::get('module.current_module_id');
        $store_id = $request->store_id;
        $categories = Category::active()->module(Config::get('module.current_module_id'))->get();
        $store = Store::active()->find($store_id);
        $keyword = $request->keyword??false;
        $key = explode(' ', $keyword);
        $products = Item::withoutGlobalScope(StoreScope::class)->active()
            ->when($category, function($query)use($category){
                $query->whereHas('category',function($q)use($category){
                    return $q->whereId($category)->orWhere('parent_id', $category);
                });
            })
            ->when($keyword, function($query)use($key){
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->whereHas('store', function($query)use($store_id, $module_id){
                return $query->where(['id'=>$store_id, 'module_id'=>$module_id]);
            });

            if(Config::get('module.current_module_type') == 'food'){
                $products=  $products->available($time);
            }
            $products=  $products->latest()->paginate(10);

        return view('admin-views.pos._single_product_list', compact('products','store'));
    }

    public function cart_items(Request $request)
    {
        $store = Store::find($request->store_id);
        return view('admin-views.pos._cart', compact('store'));
    }


    public function removeFromCart(Request $request)
    {
        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            if($request->session()->has('cart_product_ids')) {
                $item_id = $cart[$request->key]['id'];
                $product_ids = $request->session()->get('cart_product_ids');
                if (isset($product_ids[$item_id])) {
                    unset($product_ids[$item_id]);
                    $request->session()->put('cart_product_ids', $product_ids);
                }
            }

            $cart->forget($request->key);
            $request->session()->put('cart', $cart);

            $product = Item::withoutGlobalScope(StoreScope::class)->with('store')->find($item_id);
            if ($product && $product->store) {
                $this->setPosCalculatedTax($product->store);
            }

        }

        return response()->json([],200);
    }

    public function updateQuantity(Request $request)
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if ($key == $request->key) {
                $object['quantity'] = $request->quantity;
            }
            return $object;
        });
        $request->session()->put('cart', $cart);
        if($request->session()->has('cart_product_ids')) {
            $item_id = $cart[$request->key]['id'];
            $product_ids = $request->session()->get('cart_product_ids');
            if (isset($product_ids[$item_id])) {
                $product_ids[$item_id] = $request['quantity'];
                $request->session()->put('cart_product_ids', $product_ids);
            }
        }

        try {
            $product_id = $cart[$request->key]['id'];
            $product = Item::withoutGlobalScope(StoreScope::class)->with('store')->find($product_id);
            if ($product && $product->store) {
                $this->setPosCalculatedTax($product->store);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to recalculate tax after quantity update: ' . $e->getMessage());
        }

        return response()->json([],200);
    }

    public function emptyCart(Request $request)
    {
        session()->forget('cart');
        session()->forget('tax_amount');
        session()->forget('tax_included');
        session()->forget('address');
        session()->forget('cart_product_ids');
        return response()->json([], 200);
    }

    public function update_tax(Request $request)
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart['tax'] = $request->tax;
        $request->session()->put('cart', $cart);
        return back();
    }

    public function update_discount(Request $request)
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart['discount'] = $request->discount;
        $cart['discount_type'] = $request->type;
        $request->session()->put('cart', $cart);
        return back();
    }

    public function update_paid(Request $request)
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart['paid'] = $request->paid;
        $request->session()->put('cart', $cart);
        return back();
    }

    public function get_customers(Request $request){
        $key = explode(' ', $request['q']);
        $data = User::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%");
            }
        })
            ->limit(8)
            ->get([DB::raw('id, CONCAT(f_name, " ", l_name, " (", phone ,")") as text')]);

        return response()->json($data);
    }

    public function place_order(Request $request)
    {
        if(!$request->user_id){
            Toastr::error(translate('messages.no_customer_selected'));
            return back();
        }
        $customer = User::find($request->user_id);

        if(!$request->type){
            Toastr::error(translate('No payment method selected'));
            return back();
        }
        if ($request->session()->has('cart')) {
            if (count($request->session()->get('cart')) < 2) {
                Toastr::error(translate('messages.cart_empty_warning'));
                return back();
            }
        } else {
            Toastr::error(translate('messages.cart_empty_warning'));
            return back();
        }
        if ($request->session()->has('address')) {
            $address = $request->session()->get('address');
        }else {
            if(!isset($address['delivery_fee'])){
                Toastr::error(translate('messages.please_select_a_valid_delivery_location_on_the_map'));
                return back();
            }
            Toastr::error(translate('messages.delivery_information_warning'));
            return back();
        }
        if($request->type == 'wallet' && Helpers::get_business_settings('wallet_status', false) != 1)
        {
            Toastr::error(translate('messages.customer_wallet_disable_warning'));
            return back()->withInput()->with('customer', $customer);
        }

        $distance_data = isset($address) ? $address['distance'] : 0;

        $store = Store::with('store_sub')->find($request->store_id);


        if(!$store){
            Toastr::error(translate('messages.Sorry_the_store_is_not_available'));
            return back()->withInput()->with('customer', $customer);
        }


        $self_delivery_status = $store->self_delivery_system;
        $store_sub=$store?->store_sub;
        if ($store->is_valid_subscription) {

            $self_delivery_status = $store_sub->self_delivery;

            if($store_sub->max_order != "unlimited" && $store_sub->max_order <= 0){
                Toastr::error(translate('messages.The_store_has_reached_the_maximum_number_of_orders'));
                return back()->withInput()->with('customer', $customer);
            }
        } elseif($store->store_business_model == 'unsubscribed'){
            Toastr::error(translate('messages.The_store_is_not_subscribed_or_subscription_has_expired'));
            return back()->withInput()->with('customer', $customer);
        }

        $extra_charges = 0;
        $vehicle_id = null;

        if($self_delivery_status != 1){

            $data =  DMVehicle::where(function ($query) use ($distance_data) {
                $query->where('starting_coverage_area', '<=', $distance_data)->where('maximum_coverage_area', '>=', $distance_data)
                ->orWhere(function ($query) use ($distance_data) {
                    $query->where('starting_coverage_area', '>=', $distance_data);
                });
            })
            ->active()
                ->orderBy('starting_coverage_area')->first();

            $extra_charges = (float) (isset($data) ? $data->extra_charges  : 0);
            $vehicle_id = (isset($data) ? $data->id  : null);
        }



        $cart = $request->session()->get('cart');

        $total_addon_price = 0;
        $product_price = 0;
        $store_discount_amount = 0;

        $order_details = [];
        $product_data = [];
        $order = new Order();
        $order->id = 100000 + Order::count() + 1;
        if (Order::find($order->id)) {
            $order->id = Order::latest()->first()->id + 1;
        }
        $order->distance = isset($address) ? $address['distance'] : 0;
        $order->payment_status = $request->type == 'wallet'?'paid':'unpaid';
        $order->order_status = $request->type == 'wallet'?'confirmed':'pending';
        $order->order_type = 'delivery';
        $order->payment_method = $request->type;
        $order->store_id = $store->id;
        $order->module_id = $store->module_id;
        $order->user_id = $request->user_id;
        $order->dm_vehicle_id = $vehicle_id;
        $order->delivery_charge = isset($address)?$address['delivery_fee']+$extra_charges:0;
        $order->original_delivery_charge = isset($address)?$address['delivery_fee']+$extra_charges:0;
        $order->delivery_address = isset($address)?json_encode($address):null;
        $order->checked = 1;
        $order->zone_id = $store->zone_id;
        $order->schedule_at = now();
        $order->created_at = now();
        $order->updated_at = now();
        $order->otp = rand(1000, 9999);


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

        $order_details = $this->makePosOrderDetails($cart, null, $store);

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

        $total_price = $product_price + $total_addon_price - $store_discount_amount - $flash_sale_admin_discount_amount - $flash_sale_vendor_discount_amount;
        $totalDiscount = $store_discount_amount + $flash_sale_admin_discount_amount + $flash_sale_vendor_discount_amount;

        $order->flash_admin_discount_amount = round($flash_sale_admin_discount_amount, config('round_up_to_digit'));
        $order->flash_store_discount_amount = round($flash_sale_vendor_discount_amount, config('round_up_to_digit'));
        $finalCalculatedTax =  Helpers::getFinalCalculatedTax($order_details, $additionalCharges, $totalDiscount, $total_price, $store->id);

        $tax_amount = $finalCalculatedTax['tax_amount'];
        $tax_status = $finalCalculatedTax['tax_status'];
        $taxMap = $finalCalculatedTax['taxMap'];
        $orderTaxIds = data_get($finalCalculatedTax ,'taxData.orderTaxIds',[] );
        $taxType=  data_get($finalCalculatedTax ,'taxType');
        $order->tax_type = $taxType;

        $order->tax_status = $tax_status;

        try {
            $order->store_discount_amount= $store_discount_amount;
            $order->tax_percentage = 0;
            $order->total_tax_amount = $tax_amount;
            $order->order_amount = $total_price + $tax_amount + $order->delivery_charge;
            $order->adjusment = $request->amount - ($total_price + $tax_amount + $order->delivery_charge);
            $order->payment_method = $request->type == 'wallet'?'wallet':'cash_on_delivery';

            $max_cod_order_amount = BusinessSetting::where('key', 'max_cod_order_amount')->first();
            $max_cod_order_amount_value=  $max_cod_order_amount ? $max_cod_order_amount->value : 0;
            if( $max_cod_order_amount_value > 0 && $order->payment_method == 'cash_on_delivery' && $order->order_amount > $max_cod_order_amount_value){
            Toastr::error(translate('messages.You can not Order more then ').$max_cod_order_amount_value .Helpers::currency_symbol().' '. translate('messages.on COD order.')  );
            return back()->withInput()->with('customer', $customer);
            }

            if($request->type == 'wallet'){
                if($request->user_id){

                    if($customer->wallet_balance < $order->order_amount){
                        Toastr::error(translate('messages.insufficient_wallet_balance'));
                        return back()->withInput()->with('customer', $customer);
                    }else{
                        CustomerLogic::create_wallet_transaction($order->user_id, $order->order_amount, 'order_place', $order->id);

                        if (Helpers::getNotificationStatusData('customer','customer_pos_order_wallet_notification','push_notification_status') && $customer?->cm_firebase_token && $customer?->cm_firebase_token != '@' )  {
                            $notification_data = [
                                'title' => Helpers::format_currency($order->order_amount).' '. translate('amount is debited'),
                                'description' =>  Helpers::format_currency($order->order_amount).' '. translate('has been debited from your wallet balance for POS order ID') .' '.$order->id,
                                'order_id' => $order->id,
                                'image' => '',
                                'type' => 'add_fund',
                            ];
                            Helpers::send_push_notif_to_device($customer->cm_firebase_token, $notification_data);
                            DB::table('user_notifications')->insert([
                                'data' => json_encode($notification_data),
                                'user_id' => $order->user_id,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }

                    }
                }else{
                    Toastr::error(translate('messages.no_customer_selected'));
                    return back()->withInput()->with('customer', $customer);
                }
            };
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
                if (count($orderTaxIds)) {
                    \Modules\TaxModule\Services\CalculateTaxService::updateOrderTaxData(
                        orderId: $order->id,
                        orderTaxIds: $orderTaxIds,
                    );
                }
                if (count($product_data) > 0) {
                    foreach ($product_data as $item) {
                        ProductLogic::update_stock($item['item'], $item['quantity'], $item['variant'])->save();
                        ProductLogic::update_flash_stock($item['item'], $item['quantity'])?->save();
                    }
                }
                $store->increment('total_order');
            }

            session()->forget('cart');
            session()->forget('tax_amount');
            session()->forget('tax_include');
            session()->forget('address');
            session()->forget('cart_product_ids');
            session(['last_order' => $order->id]);
            Helpers::send_order_notification($order);

            //PlaceOrderMail
            try{
                if($order->order_status == 'pending' && config('mail.status') && Helpers::get_mail_status('place_order_mail_status_user') == '1' &&  Helpers::getNotificationStatusData('customer','customer_order_notification','mail_status'))
                {
                    Mail::to($order->customer?->getRawOriginal('email'))->send(new PlaceOrder($order->id));
                }
                if ($order->order_status == 'pending' && config('order_delivery_verification') == 1 && Helpers::get_mail_status('order_verification_mail_status_user') == '1' && Helpers::getNotificationStatusData('customer','customer_delivery_verification','mail_status')) {
                    Mail::to($order->customer?->getRawOriginal('email'))->send(new OrderVerificationMail($order->otp,$order->customer->f_name));
                }
            }catch (\Exception $ex) {
                info($ex->getMessage());
            }
            //PlaceOrderMail end
            Toastr::success(translate('messages.order_placed_successfully'));
            if ($store?->is_valid_subscription && $store_sub->max_order != "unlimited" && $store_sub->max_order > 0 ) {
                $store_sub->decrement('max_order' , 1);
            }
            return back();
        } catch (\Exception $e) {
            info(['Admin pos order error_____',$e]);
        }
        Toastr::warning(translate('messages.failed_to_place_order'));
        return back()->withInput()->with('customer', $customer);
    }


    public function generate_invoice($id)
    {
        $order = Order::with(['details', 'store' => function ($query) {
            return $query->withCount('orders');
        }, 'details.item' => function ($query) {
            return $query->withoutGlobalScope(StoreScope::class);
        }, 'details.campaign' => function ($query) {
            return $query->withoutGlobalScope(StoreScope::class);
        }])->where('id', $id)->first();

        return response()->json([
            'success' => 1,
            'view' => view('admin-views.pos.invoice', compact('order'))->render(),
        ]);
    }

    public function customer_store(Request $request)
    {
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'unique:users',
        ]);
        User::create([
            'f_name' => $request['f_name'],
            'l_name' => $request['l_name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'password' => bcrypt('password'),
            'is_from_pos' => 1
        ]);

        try {
            if (config('mail.status') && $request->email && Helpers::get_mail_status('pos_registration_mail_status_user') == '1' &&  Helpers::getNotificationStatusData('customer','customer_pos_registration','mail_status')) {
                Mail::to($request->email)->send(new \App\Mail\CustomerRegistrationPOS($request->f_name . ' ' . $request->l_name,$request['email'],'password'));
                Toastr::success(translate('mail_sent_to_the_user'));
            }
        } catch (\Exception $ex) {
            info($ex->getMessage());
        }
        Toastr::success(translate('customer_added_successfully'));
        return back();
    }

    public function extra_charge(Request $request)
    {
        $distance_data = $request->distancMileResult ?? 1;
        $self_delivery_status = $request->self_delivery_status;
        $extra_charges = 0;

        if($self_delivery_status != 1){

            $data=  DMVehicle::where(function($query)use($distance_data) {
                    $query->where('starting_coverage_area','<=' , $distance_data )->where('maximum_coverage_area','>=', $distance_data);
                })
                ->orWhere(function ($query) use ($distance_data) {
                    $query->where('starting_coverage_area', '>=', $distance_data);
                })
                ->active()
                ->orderBy('starting_coverage_area')->first();

                $extra_charges = (float) (isset($data) ? $data->extra_charges  : 0);
        }
            return response()->json($extra_charges,200);
    }

    public function getUserData(Request $request){
        if($request->customer_id){
            $user= User::where('id', $request->customer_id)->first();
            if ($user) {
                $user = [
                    'id' => $user->id,
                    'customer_name' => $user->f_name . ' ' . $user->l_name,
                    'customer_phone' => $user->phone,
                    'customer_wallet' => Helpers::format_currency($user->wallet_balance),
                    'customer_image' => $user->image_full_url,
                ];
                }
            return response()->json($user,200);
        }
        return response()->json([],200);
    }
}
