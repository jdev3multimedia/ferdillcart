<?php

namespace App\Http\Controllers\Api\V1\Vendor;

use App\Models\BusinessSetting;
use App\Models\DisbursementDetails;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\CentralLogics\Helpers;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function expense_report(Request $request)
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

        $expense = Expense::where('created_by', 'vendor')->where('store_id', $store_id)->where('amount', '>', 0)
            ->when(isset($from) &&  isset($to), function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:29']);
            })->when(isset($key), function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('order_id', 'like', "%{$value}%");
                    }
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total_size' => $expense->total(),
            'limit' => $limit,
            'offset' => $offset,
            'expense' => $expense->items()
        ];
        return response()->json($data, 200);
    }

    public function disbursement_report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $limit = $request['limit'] ?? 25;
        $offset = $request['offset'] ?? 1;

        $store_id = $request?->vendor?->stores[0]?->id;

        $total_disbursements = DisbursementDetails::where('store_id', $store_id)->orderBy('created_at', 'desc')->get();
        $paginator = DisbursementDetails::where('store_id', $store_id)->latest()->paginate($limit, ['*'], 'page', $offset);

        $paginator->each(function ($data) {
            $data->withdraw_method?->method_fields ?  $data->withdraw_method->method_fields = json_decode($data->withdraw_method?->method_fields, true) : '';
        });

        $data = [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'pending' => (float) $total_disbursements->where('status', 'pending')->sum('disbursement_amount'),
            'completed' => (float) $total_disbursements->where('status', 'completed')->sum('disbursement_amount'),
            'canceled' => (float) $total_disbursements->where('status', 'canceled')->sum('disbursement_amount'),
            'complete_day' => (int) BusinessSetting::where(['key' => 'store_disbursement_waiting_time'])->first()?->value,
            'disbursements' => $paginator->items()
        ];
        return response()->json($data, 200);
    }



    public function vendorTax(Request $request)
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



        $startDate = Carbon::createFromFormat('m/d/Y', trim($from));
        $endDate = Carbon::createFromFormat('m/d/Y', trim($to));
        $startDate = $startDate->startOfDay();
        $endDate = $endDate->endOfDay();

        // $start = microtime(true);
        $vendortaxData =   $this->getVendortaxData($store_id, $startDate, $endDate, $key);
        $summary =   $vendortaxData['summary'];
        $orders = $vendortaxData['orders'];

        $totalOrders = $summary->total_orders;
        $totalOrderAmount = $summary->total_order_amount;
        $totalTax = $summary->total_tax;
        $taxSummary = $vendortaxData['taxSummary'];
        $orders = $orders->paginate($limit, ['*'], 'page', $offset);

        // $time = microtime(true) - $start;
        // dd("Query took {$time} seconds", $stores);
        $data = [
            'total_size' => $orders->total(),
            'limit' => $limit,
            'offset' => $offset,
            'taxSummary' => $taxSummary,
            'totalOrders' => (int) $totalOrders,
            'totalOrderAmount' => (float) $totalOrderAmount,
            'totalTax' => (float)  $totalTax,
            'orders' => $orders->items()
        ];
        return response()->json($data, 200);

    }


    private function getVendortaxData($store_id, $startDate, $endDate, $search)
    {
        $summary = DB::table('orders')
            ->where('store_id', $store_id)
            ->whereIn('order_status', ['delivered', 'refund_requested', 'refund_request_canceled'])
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->when(count($search), fn($q) => $q->where(function ($q) use ($search) {
                foreach ($search as $value) {
                    $q->orWhere('id', 'like', "%{$value}%");
                }
            }))
            ->selectRaw('COUNT(*) as total_orders, SUM(order_amount) as total_order_amount, SUM(total_tax_amount) as total_tax')
            ->first();

        $orders = Order::with([
            'orderTaxes' => function (MorphMany $query) {
                $query->where('order_type', Order::class)
                    ->select('id', 'order_id', 'tax_name', 'tax_amount', 'tax_type');
            }
        ])

            ->where('store_id', $store_id)
            ->when(count($search), fn($q) => $q->where(function ($q) use ($search) {
                foreach ($search as $value) {
                    $q->orWhere('id', 'like', "%{$value}%");
                }
            }))
            ->whereIn('order_status', ['delivered', 'refund_requested', 'refund_request_canceled'])
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->select(['id', 'order_amount', 'total_tax_amount', 'order_type', 'created_at', 'order_status', 'payment_status'])
            ->latest('created_at');


        $taxSummary = DB::table('order_taxes')
          ->select(
                'tax_name',
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw("CONCAT(tax_rate) as tax_label")
            )
            ->where('order_type', Order::class)
            ->when(count($search), fn($q) => $q->where(function ($q) use ($search) {
                foreach ($search as $value) {
                    $q->orWhere('order_id', 'like', "%{$value}%");
                }
            }))
            ->whereIn('order_id', $orders->pluck('id')->toArray())
            ->where('store_id', $store_id)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('tax_name', 'tax_rate')
            ->get();


        return ['summary' => $summary, 'orders' => $orders, 'taxSummary' => $taxSummary ?? []];
    }
}
