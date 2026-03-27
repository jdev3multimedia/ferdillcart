<?php

namespace App\Http\Controllers\Vendor;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\OrderTransaction;
use Illuminate\Support\Facades\DB;
use Modules\Rental\Entities\Trips;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        if(Helpers::get_store_data()->module_type == 'rental'){
            return to_route('vendor.providerDashboard');

        }
        $params = [
            'statistics_type' => $request['statistics_type'] ?? 'overall'
        ];
        session()->put('dash_params', $params);

        $data = self::dashboard_order_stats_data();
        $earning = [];
        $commission = [];
        $from = Carbon::now()->startOfYear()->format('Y-m-d');
        $to = Carbon::now()->endOfYear()->format('Y-m-d');
        $store_earnings = OrderTransaction::NotRefunded()->where(['vendor_id' => Helpers::get_store_data()->vendor_id])->select(
            DB::raw('IFNULL(sum(store_amount),0) as earning'),
            DB::raw('IFNULL(sum(admin_commission + admin_expense - delivery_fee_comission),0) as commission'),
            DB::raw('YEAR(created_at) year, MONTH(created_at) month')
        )->whereBetween('created_at', [$from, $to])->groupby('year', 'month')->get()->toArray();
        for ($inc = 1; $inc <= 12; $inc++) {
            $earning[$inc] = 0;
            $commission[$inc] = 0;
            foreach ($store_earnings as $match) {
                if ($match['month'] == $inc) {
                    $earning[$inc] = $match['earning'];
                    $commission[$inc] = $match['commission'];
                }
            }
        }

        $top_sell = Item::orderBy("order_count", 'desc')
            ->take(6)
            ->get();
        $most_rated_items = Item::where('avg_rating' ,'>' ,0)
        ->orderBy('avg_rating','desc')
        ->take(6)
        ->get();
        $data['top_sell'] = $top_sell;
        $data['most_rated_items'] = $most_rated_items;

        if( Helpers::get_store_data()?->storeConfig?->minimum_stock_for_warning > 0){
            $items=  Item::where('stock' ,'<=' , Helpers::get_store_data()->storeConfig->minimum_stock_for_warning );
        } else{
            $items=  Item::where('stock',0 );
        }

        $out_of_stock_count=  Helpers::get_store_data()->module->module_type != 'food' ?  $items->orderby('stock')->latest()->count() : null;

        $item = null;
        if($out_of_stock_count == 1 ){
            $item= $items->orderby('stock')->latest()->first();
        }

        return view('vendor-views.dashboard', compact('data', 'earning', 'commission', 'params','out_of_stock_count','item'));
    }

    public function store_data()
    {

        $store= Helpers::get_store_data();
        if($store->module_type == 'rental'){
            $type='trip';
            $new_pending_order=Trips::where(['checked' => 0])->where('provider_id', $store->id)->count();

        } else{
            $new_pending_order = DB::table('orders')->where(['checked' => 0])->where('store_id', $store->id)->where('order_status','pending');
            if(config('order_confirmation_model') != 'store' && !$store->sub_self_delivery)
            {
                $new_pending_order = $new_pending_order->where('order_type', 'take_away');
            }
            $new_pending_order = $new_pending_order->count();
            $new_confirmed_order = DB::table('orders')->where(['checked' => 0])->where('store_id', $store->id)->whereIn('order_status',['confirmed', 'accepted'])->whereNotNull('confirmed')->count();
            $type= 'store_order';
        }

        return response()->json([
            'success' => 1,
            'data' => ['new_pending_order' => $new_pending_order, 'new_confirmed_order' => $new_confirmed_order?? 0, 'order_type' =>$type]
        ]);
    }

    public function order_stats(Request $request)
    {
        $params = session('dash_params');
        foreach ($params as $key => $value) {
            if ($key == 'statistics_type') {
                $params['statistics_type'] = $request['statistics_type'];
            }
        }
        session()->put('dash_params', $params);

        $data = self::dashboard_order_stats_data();
        return response()->json([
            'view' => view('vendor-views.partials._dashboard-order-stats', compact('data'))->render()
        ], 200);
    }

    public function dashboard_order_stats_data()
    {
        $params = session('dash_params');
        $today = $params['statistics_type'] == 'today' ? 1 : 0;
        $this_month = $params['statistics_type'] == 'this_month' ? 1 : 0;

        $confirmed = Order::when($today, function ($query) {
            return $query->whereDate('created_at', Carbon::today());
        })->when($this_month, function ($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })->where(['store_id' => Helpers::get_store_id()])->whereIn('order_status',['confirmed', 'accepted'])->whereNotNull('confirmed')->StoreOrder()->NotDigitalOrder()->OrderScheduledIn(30)->count();

        $cooking = Order::when($today, function ($query) {
            return $query->whereDate('created_at', Carbon::today());
        })->when($this_month, function ($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })->where(['order_status' => 'processing', 'store_id' => Helpers::get_store_id()])->StoreOrder()->NotDigitalOrder()->count();

        $ready_for_delivery = Order::when($today, function ($query) {
            return $query->whereDate('created_at', Carbon::today());
        })->when($this_month, function ($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })->where(['order_status' => 'handover', 'store_id' => Helpers::get_store_id()])->StoreOrder()->NotDigitalOrder()->count();

        $item_on_the_way = Order::when($today, function ($query) {
            return $query->whereDate('created_at', Carbon::today());
        })->when($this_month, function ($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })->ItemOnTheWay()->where(['store_id' => Helpers::get_store_id()])->StoreOrder()->NotDigitalOrder()->count();

        $delivered = Order::when($today, function ($query) {
            return $query->whereDate('created_at', Carbon::today());
        })->when($this_month, function ($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })->where(['order_status' => 'delivered', 'store_id' => Helpers::get_store_id()])->StoreOrder()->NotDigitalOrder()->count();

        $refunded = Order::when($today, function ($query) {
            return $query->whereDate('created_at', Carbon::today());
        })->when($this_month, function ($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })->where(['order_status' => 'refunded', 'store_id' => Helpers::get_store_id()])->StoreOrder()->NotDigitalOrder()->count();

        $scheduled = Order::when($today, function ($query) {
            return $query->whereDate('created_at', Carbon::today());
        })->when($this_month, function ($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })->Scheduled()->where(['store_id' => Helpers::get_store_id()])->where(function($q){
            if(config('order_confirmation_model') == 'store')
            {
                $q->whereNotIn('order_status',['failed','canceled', 'refund_requested', 'refunded']);
            }
            else
            {
                $q->whereNotIn('order_status',['pending','failed','canceled', 'refund_requested', 'refunded'])->orWhere(function($query){
                    $query->where('order_status','pending')->where('order_type', 'take_away');
                });
            }

        })->StoreOrder()->NotDigitalOrder()->count();

        $all = Order::when($today, function ($query) {
            return $query->whereDate('created_at', Carbon::today());
        })->when($this_month, function ($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })->where(['store_id' => Helpers::get_store_id()])
        ->where(function($query){
            return $query->whereNotIn('order_status',(config('order_confirmation_model') == 'store'|| \App\CentralLogics\Helpers::get_store_data()->sub_self_delivery)?['failed','canceled', 'refund_requested', 'refunded']:['pending','failed','canceled', 'refund_requested', 'refunded'])
            ->orWhere(function($query){
                return $query->where('order_status','pending')->where('order_type', 'take_away');
            });
        })
        ->StoreOrder()->NotDigitalOrder()->count();

        $data = [
            'confirmed' => $confirmed,
            'cooking' => $cooking,
            'ready_for_delivery' => $ready_for_delivery,
            'item_on_the_way' => $item_on_the_way,
            'delivered' => $delivered,
            'refunded' => $refunded,
            'scheduled' => $scheduled,
            'all' => $all,
        ];

        return $data;
    }

    public function updateDeviceToken(Request $request)
    {
        $vendor = Vendor::find(Helpers::get_vendor_id());
        $vendor->firebase_token =  $request->token;

        $vendor->save();

        return response()->json(['Token successfully stored.']);
    }
}
