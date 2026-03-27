<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Exports\CustomerLoyaltyTransactionExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\LoyaltyPointTransaction;

class LoyaltyPointController extends Controller
{
    public function report(Request $request)
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }
        $from = session('from_date');
        $to = session('to_date');
        $filter = $request->query('filter', 'all_time');

        $data = LoyaltyPointTransaction::selectRaw('sum(credit) as total_credit, sum(debit) as total_debit')
        ->when(($request->from && $request->to),function($query)use($request){
            $query->whereBetween('created_at', [$request->from.' 00:00:00', $request->to.' 23:59:59']);
        })
        ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
            return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " P23:59:59"]);
        })
        ->when(isset($filter) && $filter == 'this_year', function ($query) {
            return $query->whereYear('created_at', now()->format('Y'));
        })
        ->when(isset($filter) && $filter == 'this_month', function ($query) {
            return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
        })
        ->when(isset($filter) && $filter == 'this_month', function ($query) {
            return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
        })
        ->when(isset($filter) && $filter == 'previous_year', function ($query) {
            return $query->whereYear('created_at', date('Y') - 1);
        })
        ->when(isset($filter) && $filter == 'this_week', function ($query) {
            return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
        })
        ->when(isset($request->transaction_type) && ($request->transaction_type != 'all'), function($query)use($request){
            $query->where('transaction_type',$request->transaction_type);
        })
        ->when(isset($request->customer_id) && is_numeric($request->customer_id), function($query)use($request){
            $query->where('user_id',$request->customer_id);
        })
        ->get();

        $transactions = LoyaltyPointTransaction::with('user')->
        when(($request->from && $request->to),function($query)use($request){
            $query->whereBetween('created_at', [$request->from.' 00:00:00', $request->to.' 23:59:59']);
        })
        ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
            return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
        })
        ->when(isset($filter) && $filter == 'this_year', function ($query) {
            return $query->whereYear('created_at', now()->format('Y'));
        })
        ->when(isset($filter) && $filter == 'this_month', function ($query) {
            return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
        })
        ->when(isset($filter) && $filter == 'this_month', function ($query) {
            return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
        })
        ->when(isset($filter) && $filter == 'previous_year', function ($query) {
            return $query->whereYear('created_at', date('Y') - 1);
        })
        ->when(isset($filter) && $filter == 'this_week', function ($query) {
            return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
        })
        ->when(isset($request->transaction_type) && ($request->transaction_type != 'all'), function($query)use($request){
            $query->where('transaction_type',$request->transaction_type);
        })
        ->when(isset($request->customer_id) && is_numeric($request->customer_id), function($query)use($request){
            $query->where('user_id',$request->customer_id);
        })
        ->latest()
        ->paginate(config('default_pagination'));

        return view('admin-views.customer.loyalty-point.report', compact('data','transactions','filter'));
    }
    public function export(Request $request)
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }
        $from = session('from_date');
        $to = session('to_date');
        $filter = $request->query('filter', 'all_time');

        $data = LoyaltyPointTransaction::selectRaw('sum(credit) as total_credit, sum(debit) as total_debit')
            ->when(($request->from && $request->to),function($query)use($request){
                $query->whereBetween('created_at', [$request->from.' 00:00:00', $request->to.' 23:59:59']);
            })
            ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })
            ->when(isset($filter) && $filter == 'this_year', function ($query) {
                return $query->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'previous_year', function ($query) {
                return $query->whereYear('created_at', date('Y') - 1);
            })
            ->when(isset($filter) && $filter == 'this_week', function ($query) {
                return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            })
        ->when(isset($request->transaction_type) && ($request->transaction_type != 'all'), function($query)use($request){
            $query->where('transaction_type',$request->transaction_type);
        })
        ->when(isset($request->customer_id) && is_numeric($request->customer_id), function($query)use($request){
            $query->where('user_id',$request->customer_id);
        })
            ->get();

        $transactions = LoyaltyPointTransaction::
        when(($request->from && $request->to),function($query)use($request){
            $query->whereBetween('created_at', [$request->from.' 00:00:00', $request->to.' 23:59:59']);
        })
            ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })
            ->when(isset($filter) && $filter == 'this_year', function ($query) {
                return $query->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'previous_year', function ($query) {
                return $query->whereYear('created_at', date('Y') - 1);
            })
            ->when(isset($filter) && $filter == 'this_week', function ($query) {
                return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            })
        ->when(isset($request->transaction_type) && ($request->transaction_type != 'all'), function($query)use($request){
            $query->where('transaction_type',$request->transaction_type);
        })
        ->when(isset($request->customer_id) && is_numeric($request->customer_id), function($query)use($request){
            $query->where('user_id',$request->customer_id);
        })
            ->latest()
        ->get();

        $data = [
            'transactions'=>$transactions,
            'data'=>$data,
            'from'=>$request->from??null,
            'to'=>$request->to??null,
            'transaction_type'=>$request->transaction_type??null,
            'customer'=>$request->customer_id?Helpers::get_customer_name($request->customer_id):null,

        ];

        if ($request->type == 'excel') {
            return Excel::download(new CustomerLoyaltyTransactionExport($data), 'CustomerLoyaltyTransactions.xlsx');
        } else if ($request->type == 'csv') {
            return Excel::download(new CustomerLoyaltyTransactionExport($data), 'CustomerLoyaltyTransactions.csv');
        }
    }

    public function set_date(Request $request)
    {
        session()->put('from_date', date('Y-m-d', strtotime($request['from'])));
        session()->put('to_date', date('Y-m-d', strtotime($request['to'])));
        return back();
    }
}
