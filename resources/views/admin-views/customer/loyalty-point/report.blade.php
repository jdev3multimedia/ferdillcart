@php use App\CentralLogics\Helpers; @endphp
@extends('layouts.admin.app')

@section('title',translate('messages.customer_loyalty_point_report'))

@push('css_or_js')

@endpush

@section('content')
    @php
        $from = session('from_date');
        $to = session('to_date');
    @endphp
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title mr-3">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/customer-loyalty.png')}}" class="w--26" alt="">
                </span>
                <span>
                     {{translate('messages.customer_loyalty_point_report')}}
                </span>
            </h1>
        </div>
        <!-- Page Header -->

        {{-- Filter Options Card --}}
        <div class="card mb-3">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <span>{{translate('messages.filter_options')}}</span>
                </h4>

                <form action="{{route('admin.users.customer.loyalty-point.set-date')}}" method="post">
                    @csrf
                    <div class="row justify-content-end align-items-end g-3">
                        <div class="col-lg-4">
                            @php
                                $transaction_status=request()->get('transaction_type');
                            @endphp
                            <label class="text-dark text-capitalize"
                                   for="add-fund-type">{{translate('messages.add_fund_type')}}</label>
                            <select name="transaction_type" id="add-fund-type"
                                    class="form-control js-select2-custom  set-filter" data-url="{{ url()->full() }}"
                                    data-filter="transaction_type"
                                    title="{{translate('messages.select_transaction_type')}}">
                                <option value="all">{{translate('messages.all_type')}}</option>
                                <option
                                    value="point_to_wallet" {{isset($transaction_status) && $transaction_status=='point_to_wallet'?'selected':''}}>{{translate('messages.point_to_wallet')}}</option>
                                <option
                                    value="order_place" {{isset($transaction_status) && $transaction_status=='order_place'?'selected':''}}>{{translate('messages.order_place')}}</option>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-dark text-capitalize"
                                   for="customer">{{translate('messages.customer')}}</label>
                            <select id='customer' name="customer_id" data-url="{{ url()->full() }}"
                                    data-filter="customer_id"
                                    data-placeholder="{{translate('messages.select_customer')}}"
                                    class="js-data-example-ajax form-control set-filter"
                                    title="{{translate('messages.select_customer')}}">
                                @if (request()->get('customer_id') && $customer_info = \App\Models\User::find(request()->get('customer_id')))
                                    <option value="{{$customer_info->id}}"
                                            selected>{{$customer_info->f_name.' '.$customer_info->l_name}}
                                        ({{$customer_info->phone}})
                                    </option>
                                @endif

                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label class="text-dark text-capitalize"
                                   for="filter">{{translate('messages.duration')}}</label>
                            <select class="form-control js-select2-custom  set-filter" name="filter"
                                    data-url="{{ url()->full() }}" data-filter="filter">
                                <option
                                    value="all_time" {{ isset($filter) && $filter == 'all_time' ? 'selected' : '' }}>
                                    {{ translate('messages.All Time') }}</option>
                                <option
                                    value="this_year" {{ isset($filter) && $filter == 'this_year' ? 'selected' : '' }}>
                                    {{ translate('messages.This Year') }}</option>
                                <option value="previous_year"
                                    {{ isset($filter) && $filter == 'previous_year' ? 'selected' : '' }}>
                                    {{ translate('messages.Previous Year') }}</option>
                                <option value="this_month"
                                    {{ isset($filter) && $filter == 'this_month' ? 'selected' : '' }}>
                                    {{ translate('messages.This Month') }}</option>
                                <option
                                    value="this_week" {{ isset($filter) && $filter == 'this_week' ? 'selected' : '' }}>
                                    {{ translate('messages.This Week') }}</option>
                                <option value="custom" {{ isset($filter) && $filter == 'custom' ? 'selected' : '' }}>
                                    {{ translate('messages.Custom') }}</option>
                            </select>
                        </div>
                        @if (isset($filter) && $filter == 'custom')
                            <div class="col-lg-4">

                                <input type="date" name="from" id="from_date" class="form-control"
                                       placeholder="{{ translate('Start Date') }}"
                                       {{ session()->has('from_date') ? 'value=' . session('from_date') : '' }} required>

                            </div>
                            <div class="col-lg-4">

                                <input type="date" name="to" id="to_date" class="form-control"
                                       placeholder="{{ translate('End Date') }}"
                                       {{ session()->has('to_date') ? 'value=' . session('to_date') : '' }} required>

                            </div>
                        @endif
                        <div class="col-lg-4">
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset location-reload-to-base"
                                        data-url="{{url()->full()}}">{{translate('messages.reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('messages.filter')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>

        {{-- Statistics Card --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    @php
                        $credit = (int)$data[0]->total_credit??0;
                        $debit = (int)$data[0]->total_debit??0;
                        $balance = $credit - $debit;
                    @endphp
                        <!--Debit earned-->
                    <div class="col-md-4">
                        <div class="color-card color-6">
                            <div class="img-box">
                                <img class="resturant-icon w--30"
                                     src="{{asset('public/assets/admin/img/customer-loyality/1.png')}}"
                                     alt="transactions">
                            </div>
                            <div>
                                <h2 class="title">
                                    {{$credit}}
                                </h2>
                                <div class="subtitle">
                                    {{translate('messages.points_Earned')}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Debit earned End-->

                    <!--credit earned-->
                    <div class="col-md-4">
                        <div class="color-card color-2">
                            <div class="img-box">
                                <img class="resturant-icon w--30"
                                     src="{{asset('public/assets/admin/img/customer-loyality/4.png')}}"
                                     alt="transactions">
                            </div>
                            <div>
                                <h2 class="title">
                                    {{$debit}}
                                </h2>
                                <div class="subtitle">
                                    {{translate('messages.points_Converted')}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--credit earned end-->

                    <!--balance earned-->
                    <div class="col-md-4">
                        <div class="color-card color-4">
                            <div class="img-box">
                                <img class="resturant-icon w--30"
                                     src="{{asset('public/assets/admin/img/customer-loyality/2.png')}}"
                                     alt="transactions">
                            </div>
                            <div>
                                <h2 class="title">
                                    {{$balance}}
                                </h2>
                                <div class="subtitle">
                                    {{translate('messages.current_Points_in_Wallet')}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--balance earned end-->
                </div>
            </div>

        </div>

        <!-- End Stats -->
        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header border-0">
                <h4 class="card-title">
                    <span>{{translate('messages.transactions')}}</span>
                </h4>
                <!-- Unfold -->
                <div class="hs-unfold mr-2">
                    <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40"
                       href="javascript:;"
                       data-hs-unfold-options='{
                                "target": "#usersExportDropdown",
                                "type": "css-animation"
                            }'>
                        <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                    </a>

                    <div id="usersExportDropdown"
                         class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                        <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                        <a id="export-excel" class="dropdown-item"
                           href="{{route('admin.users.customer.loyalty-point.export', ['type'=>'excel',request()->getQueryString()])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                 src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                 alt="Image Description">
                            {{ translate('messages.excel') }}
                        </a>
                        <a id="export-csv" class="dropdown-item"
                           href="{{route('admin.users.customer.loyalty-point.export', ['type'=>'csv',request()->getQueryString()])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                 src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                 alt="Image Description">
                            .{{ translate('messages.csv') }}
                        </a>
                    </div>
                </div>
                <!-- End Unfold -->
            </div>
            <!-- End Header -->

            <!-- Body -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="datatable"
                           class="table table-thead-bordered table-align-middle card-table table-nowrap">
                        <thead class="thead-light">
                        <tr>
                            <th class="border-0">{{translate('SL')}}</th>
                            <th class="border-0">{{translate('messages.transaction_ID')}}</th>
                            <th class="border-0">{{translate('messages.Customer_info')}}</th>
                            <th class="border-0">{{translate('messages.points_earned')}}</th>
                            <th class="border-0">{{translate('messages.points_converted')}}</th>
                            <th class="border-0">{{translate('messages.current_points_in_wallet')}}</th>
                            <th class="border-0">{{translate('messages.transaction_type')}}</th>
                            <th class="border-0">{{translate('messages.reference')}}</th>
                            <th class="border-0">{{translate('messages.created_at')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($transactions as $k=>$wt)
                            <tr scope="row">
                                <td>{{$k+$transactions->firstItem()}}</td>
                                <td>{{$wt->transaction_id}}</td>
                                <td><a class="text-dark"
                                       href="{{route('admin.users.customer.view',['user_id'=>$wt->user_id])}}">{{Str::limit($wt->user?$wt->user->f_name.' '.$wt->user->l_name:translate('messages.not_found'),20,'...')}}</a>
                                </td>
                                <td>{{$wt->credit}}</td>
                                <td>{{$wt->debit}}</td>
                                <td>{{$wt->balance}}</td>
                                <td>
                                    <span
                                        class="badge badge-soft-{{$wt->transaction_type=='point_to_wallet'?'success':'dark'}}">
                                        {{translate('messages.'.$wt->transaction_type)}}
                                    </span>
                                </td>
                                <td>{{$wt->reference}}</td>
                                <td>
                                    {{ Helpers::date_format($wt->created_at) }}
                                    <br>
                                    {{ Helpers::time_format($wt->created_at) }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End Body -->
            @if(count($transactions) !== 0)
                <hr>
            @endif
            <div class="page-area">
                {!! $transactions->withQueryString()->links() !!}
            </div>
            @if(count($transactions) === 0)
                <div class="empty--data">
                    <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                    <h5>
                        {{translate('no_data_found')}}
                    </h5>
                </div>
            @endif
        </div>
        <!-- End Card -->
    </div>
@endsection


@push('script_2')


    <script>
        "use strict";
        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{route('admin.users.customer.select-list')}}',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        all: true,
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                __port: function (params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });
    </script>
@endpush
