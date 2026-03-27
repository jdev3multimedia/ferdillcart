<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ParcelWiseTaxExport;
use App\Exports\VendorTaxExport;
use App\Exports\VendorWiseTaxExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Maatwebsite\Excel\Facades\Excel;

class VendorTaxReportController extends Controller
{
    public function __construct()
    {
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
    }
    public function vendorWiseTaxes(Request $request)
    {

        $dateRange = $request->dates ?? now()->subDays(6)->format('m/d/Y') . ' - ' . now()->format('m/d/Y');
        $key = explode(' ', $request['search']);

        list($startDate, $endDate) = explode(' - ', $dateRange);
        $startDate = Carbon::createFromFormat('m/d/Y', trim($startDate));
        $endDate = Carbon::createFromFormat('m/d/Y', trim($endDate));
        $startDate = $startDate->startOfDay();
        $endDate = $endDate->endOfDay();

        $store_id = $request->query('store_id', 'all');
        $store = is_numeric($store_id) ? Store::findOrFail($store_id) : null;

        // $start = microtime(true);

        $data = $this->vendorWiseTaxData($store, $startDate, $endDate, $key);
        $result = $data['result'];

        $totalOrders = $result->total_orders;
        $totalOrderAmount = $result->total_order_amount;
        $totalTax = $result->total_tax;

        $storeQuery = $data['storeQuery'];
        $storeQuery =  $storeQuery->paginate(config('default_pagination'))->withQueryString();
        $storeIds = $storeQuery->pluck('store_id')->toArray();

        $stores = $this->getOrderTaxData($startDate, $endDate, $storeIds, $storeQuery);
        // $time = microtime(true) - $start;
        // dd("Query took {$time} seconds", $stores);
        $startDate = Carbon::parse($startDate)->toIso8601String();
        $endDate = Carbon::parse($endDate)->toIso8601String();
        return view('admin-views.report.tax-report.vendor-tax-report', compact('totalOrders', 'totalOrderAmount', 'totalTax', 'store', 'stores', 'dateRange', 'startDate', 'endDate'));
    }

    private function  vendorWiseTaxData($store, $startDate, $endDate, $search)
    {
        $query = DB::table('orders')
            ->selectRaw('COUNT(*) as total_orders,
                        SUM(order_amount) as total_order_amount,
                        SUM(total_tax_amount) as total_tax')
            ->whereIn('order_status', ['delivered', 'refund_requested', 'refund_request_canceled']);

        if (isset($store)) {
            $query->where('store_id', $store->id);
        }

        if (isset($startDate) && isset($endDate)) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if (isset($search)) {
            $query->whereExists(function ($subQuery) use ($search) {
                $subQuery->select(DB::raw(1))
                    ->from('stores')
                    ->whereRaw('stores.id = orders.store_id')
                    ->where(function ($q) use ($search) {
                        foreach ($search as $value) {
                            $q->orWhere('stores.name', 'like', "%{$value}%");
                        }
                    });
            });
        }

        $result = $query->first();

        $storeQuery = DB::table('stores as stores')
            ->selectRaw(' stores.id as store_id,
                            stores.name as store_name,
                            stores.phone as store_phone,
                            COUNT(DISTINCT orders.id) as total_orders,
                            SUM(orders.order_amount) as total_order_amount,
                            SUM(orders.total_tax_amount) as total_tax_amount ')
            ->join('orders as orders', function ($join) use ($startDate, $endDate) {
                $join->on('orders.store_id', '=', 'stores.id')
                    ->whereIn('orders.order_status', ['delivered', 'refund_requested', 'refund_request_canceled']);

                if ($startDate && $endDate) {
                    $join->whereBetween('orders.created_at', [$startDate, $endDate]);
                }
            })
            ->when(isset($store), fn($query) => $query->where('stores.id', $store->id))
            ->when(!empty($search), function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    foreach ($search as $searchTerm) {
                        $q->orWhere('stores.name', 'like', "%{$searchTerm}%");
                    }
                });
            })->groupBy('stores.id');
        return [
            'result' => $result,
            'storeQuery' => $storeQuery,
        ];
    }

    private function getOrderTaxData($startDate, $endDate, $storeIds, $storeQuery, $export = false)
    {
        $taxGrouped = [];
        $taxQuery = DB::table('order_taxes as order_taxes')
            ->selectRaw('orders.store_id, order_taxes.tax_name, SUM(order_taxes.tax_amount) as total_tax_amount')
            ->join('orders', 'order_taxes.order_id', '=', 'orders.id')
            ->where('order_taxes.order_type', 'App\\Models\\Order')
            ->whereIn('orders.order_status', ['delivered', 'refund_requested', 'refund_request_canceled'])
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('orders.created_at', [$startDate, $endDate]);
            })
            ->whereIn('orders.store_id',  $storeIds)
            ->groupBy('orders.store_id', 'order_taxes.tax_name')
            ->get();

        foreach ($taxQuery as $tax) {
            $taxGrouped[$tax->store_id][] = [
                'tax_name' => $tax->tax_name,
                'total_tax_amount' => (float)$tax->total_tax_amount,
            ];
        }
        if ($export) {

            $stores = $storeQuery->map(function ($store) use ($taxGrouped) {
                return (object)[
                    'store_id' => $store->store_id,
                    'store_name' => $store->store_name,
                    'store_phone' => $store->store_phone,
                    'store_total_tax_amount' => $store->total_tax_amount,
                    'total_orders' => (int)$store->total_orders,
                    'total_order_amount' => (float)$store->total_order_amount,
                    'tax_data' => $taxGrouped[$store->store_id] ?? [],
                ];
            });

            return $stores;
        }
        $stores = $storeQuery->getCollection()->map(function ($store) use ($taxGrouped) {
            return (object)[
                'store_id' => $store->store_id,
                'store_name' => $store->store_name,
                'store_phone' => $store->store_phone,
                'total_orders' => (int)$store->total_orders,
                'store_total_tax_amount' => $store->total_tax_amount,
                'total_order_amount' => (float)$store->total_order_amount,
                'tax_data' => $taxGrouped[$store->store_id] ?? [],
            ];
        });


        $stores = $storeQuery->setCollection($stores);
        return $stores;
    }

    public function vendorWiseTaxExport(Request $request)
    {
        $dateRange = $request->dates ?? now()->subDays(6)->format('m/d/Y') . ' - ' . now()->format('m/d/Y');
        $key = explode(' ', $request['search']);

        list($startDate, $endDate) = explode(' - ', $dateRange);
        $startDate = Carbon::createFromFormat('m/d/Y', trim($startDate));
        $endDate = Carbon::createFromFormat('m/d/Y', trim($endDate));
        $startDate = $startDate->startOfDay();
        $endDate = $endDate->endOfDay();

        $store_id = $request->query('store_id', 'all');
        $store = is_numeric($store_id) ? Store::findOrFail($store_id) : null;

        // $start = microtime(true);

        $data = $this->vendorWiseTaxData($store, $startDate, $endDate, $key);
        $summary = $data['result'];
        $storeQuery = $data['storeQuery'];
        $storeQuery =  $storeQuery->cursor();
        $storeIds = $storeQuery->pluck('store_id')->toArray();

        $stores = $this->getOrderTaxData($startDate, $endDate, $storeIds, $storeQuery, true);

        $startDate = Carbon::parse($startDate)->toIso8601String();
        $endDate = Carbon::parse($endDate)->toIso8601String();
        $data = [
            'stores' => $stores,
            'search' => $request->search ?? null,
            'from' => $startDate,
            'to' => $endDate,
            'summary' => $summary
        ];
        // dd($request->export_type);
        if ($request->export_type == 'excel') {
            return Excel::download(new VendorWiseTaxExport($data), 'VendorWiseTaxExport.xlsx');
        } else if ($request->export_type == 'csv') {
            return Excel::download(new VendorWiseTaxExport($data), 'VendorWiseTaxExport.csv');
        }
    }


    public function vendorTax(Request $request)
    {

        $dateRange = $request->dates ?? now()->subDays(6)->format('m/d/Y') . ' - ' . now()->format('m/d/Y');

        list($startDate, $endDate) = explode(' - ', $dateRange);
        $startDate = Carbon::createFromFormat('m/d/Y', trim($startDate));
        $endDate = Carbon::createFromFormat('m/d/Y', trim($endDate));
        $startDate = $startDate->startOfDay();
        $endDate = $endDate->endOfDay();

        $store_id = $request->id;
        $store = is_numeric($store_id) ? Store::select('id', 'name', 'phone')->findOrFail($store_id) : null;

        // $start = microtime(true);
        $vendortaxData =   $this->getVendortaxData($store->id, $startDate, $endDate);
        $summary =   $vendortaxData['summary'];
        $orders = $vendortaxData['orders'];

        $totalOrders = $summary->total_orders;
        $totalOrderAmount = $summary->total_order_amount;
        $totalTax = $summary->total_tax;

        $orders = $orders->paginate(config('default_pagination'))
            ->withQueryString();

        // $time = microtime(true) - $start;
        // dd("Query took {$time} seconds", $stores);
        $startDate = Carbon::parse($startDate)->format('d M, Y');
        $endDate = Carbon::parse($endDate)->format('d M, Y');
        return view('admin-views.report.tax-report.vendor-tax-detail-report', compact('totalOrders', 'totalOrderAmount', 'totalTax', 'store', 'orders', 'startDate', 'endDate'));
    }

    public function vendorTaxExport(Request $request)
    {
        $dateRange = $request->dates ?? now()->subDays(6)->format('m/d/Y') . ' - ' . now()->format('m/d/Y');
        list($startDate, $endDate) = explode(' - ', $dateRange);
        $startDate = Carbon::createFromFormat('m/d/Y', trim($startDate));
        $endDate = Carbon::createFromFormat('m/d/Y', trim($endDate));
        $startDate = $startDate->startOfDay();
        $endDate = $endDate->endOfDay();

        $store_id = $request->id;
        $store = is_numeric($store_id) ? Store::select('id', 'name', 'phone')->findOrFail($store_id) : null;

        // $start = microtime(true);
        $vendortaxData =   $this->getVendortaxData($store->id, $startDate, $endDate);
        $summary =   $vendortaxData['summary'];
        $orders = $vendortaxData['orders'];

        $orders = $orders->cursor();

        // $time = microtime(true) - $start;
        // dd("Query took {$time} seconds", $stores);
        $startDate = Carbon::parse($startDate)->format('d M, Y');
        $endDate = Carbon::parse($endDate)->format('d M, Y');

        $data = [
            'orders' => $orders,
            'search' => $request->search ?? null,
            'from' => $startDate,
            'to' => $endDate,
            'summary' => $summary
        ];
        // dd($request->export_type);
        if ($request->export_type == 'excel') {
            return Excel::download(new VendorTaxExport($data), $store->name .'s TaxExport.xlsx');
        } else if ($request->export_type == 'csv') {
            return Excel::download(new VendorTaxExport($data),  $store->name .'s TaxExport.csv');
        }
    }

        private function getVendortaxData($store_id, $startDate, $endDate)
    {
        $summary = DB::table('orders')
            ->where('store_id', $store_id)
            ->whereIn('order_status', ['delivered', 'refund_requested', 'refund_request_canceled'])
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->selectRaw('COUNT(*) as total_orders, SUM(order_amount) as total_order_amount, SUM(total_tax_amount) as total_tax')
            ->first();

        $orders = Order::with([
            'orderTaxes' => function (MorphMany $query) {
                $query->where('order_type', Order::class)
                    ->select('id', 'order_id', 'tax_name', 'tax_amount','tax_on','tax_type','taxable_type');
            }
        ])
            ->where('store_id', $store_id)
            ->whereIn('order_status', ['delivered', 'refund_requested', 'refund_request_canceled'])
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->select(['id', 'order_amount', 'total_tax_amount','order_type','tax_type' ,'created_at'])
            ->latest('created_at');

        return ['summary' => $summary, 'orders' => $orders];
    }
}
