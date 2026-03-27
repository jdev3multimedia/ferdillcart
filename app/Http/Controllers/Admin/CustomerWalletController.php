<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\WalletTransaction;
use App\CentralLogics\CustomerLogic;
use App\Exports\CustomerWalletTransactionExport;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;


class CustomerWalletController extends Controller
{
    public function add_fund_view()
    {
        if (BusinessSetting::where('key', 'wallet_status')->first()->value != 1) {
            Toastr::error(trans('messages.customer_wallet_disable_warning_admin'));
            return back();
        }
        return view('admin-views.customer.wallet.add_fund');
    }

    public function add_fund(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id'=>'exists:users,id',
            'amount'=>'numeric|min:.01',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $wallet_transaction = CustomerLogic::create_wallet_transaction($request->customer_id, $request->amount, 'add_fund_by_admin',$request->reference);

        if($wallet_transaction)
        {
            try{
                Helpers::add_fund_push_notification($request->customer_id);
                if(config('mail.status') && Helpers::get_mail_status('add_fund_mail_status_user') == '1' &&  Helpers::getNotificationStatusData('customer','customer_add_fund_to_wallet','mail_status') ) {
                    Mail::to($wallet_transaction->user?->getRawOriginal('email'))->send(new \App\Mail\AddFundToWallet($wallet_transaction));
                }
            }catch(\Exception $ex)
            {
                info($ex->getMessage());
            }

            return response()->json([], 200);
        }

        return response()->json(['errors'=>[
            'message'=>trans('messages.failed_to_create_transaction')
        ]], 200);
    }

    public function report(Request $request)
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }
        $from = session('from_date');
        $to = session('to_date');
        $filter = $request->query('filter', 'all_time');
        $key = [];
        if ($request->search) {
            $key = explode(' ', $request['search']);
        }
        $data = WalletTransaction::selectRaw('sum(credit+admin_bonus) as total_credit, sum(debit) as total_debit, SUM(IF(transaction_type = "add_fund_by_admin", credit, 0)) as add_fund_total,SUM(IF(transaction_type = "order_refund", credit, 0)) as order_refund_total,SUM(IF(transaction_type = "loyalty_point", credit, 0)) as loyalty_point_total,SUM(IF(transaction_type = "order_place", credit, 0)) as order_place_total')
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
        ->when(count($key) > 0, function($query) use($key){
            $query->wherehas('user',    function ($query) use ($key) {
                foreach ($key as $value) {
                    $query->where(function($query) use($value){
                        $query->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                    });
                };
            });
       })
        ->get();

        $transactions = WalletTransaction::with('user')->
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
        ->when(count($key) > 0, function($query) use($key){
            $query->wherehas('user',    function ($query) use ($key) {
                foreach ($key as $value) {
                    $query->where(function($query) use($value){
                        $query->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                    });
                };
            });
       })
        ->latest()
        ->paginate(config('default_pagination'));

        return view('admin-views.customer.wallet.report', compact('data','transactions','filter'));
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
        $key = [];
        if ($request->search) {
            $key = explode(' ', $request['search']);
        }

        $data = WalletTransaction::selectRaw('sum(credit) as total_credit, sum(debit) as total_debit')
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
        ->when(count($key) > 0, function($query) use($key){
            $query->wherehas('user',    function ($query) use ($key) {
                foreach ($key as $value) {
                    $query->where(function($query) use($value){
                        $query->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                    });
                };
            });
       })
       ->get();

        $transactions = WalletTransaction::
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
        ->when(count($key) > 0, function($query) use($key){
            $query->wherehas('user',    function ($query) use ($key) {
                foreach ($key as $value) {
                    $query->where(function($query) use($value){
                        $query->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                    });
                };
            });
       })
        ->latest()
        ->get();

        $data = [
            'transactions'=>$transactions,
            'data'=>$data,
            'from'=>$request->from??null,
            'to'=>$request->to??null,
            'transaction_type'=>$request->transaction_type??null,
            'customer'=>$request->customer_id?Helpers::get_customer_name($request->customer_id):$request['search']?? null,

        ];

        if ($request->type == 'excel') {
            return Excel::download(new CustomerWalletTransactionExport($data), 'CustomerWalletTransactions.xlsx');
        } else if ($request->type == 'csv') {
            return Excel::download(new CustomerWalletTransactionExport($data), 'CustomerWalletTransactions.csv');
        }
    }

    public function set_date(Request $request)
    {
        session()->put('from_date', date('Y-m-d', strtotime($request['from'])));
        session()->put('to_date', date('Y-m-d', strtotime($request['to'])));
        return back();
    }

}
