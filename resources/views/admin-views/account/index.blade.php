@extends('layouts.admin.app')

@section('title',translate('messages.account_transaction'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/collect-cash.png')}}" class="w--22" alt="">
            </span>
            <span>
                {{translate('messages.collect_cash_transaction')}}
            </span>
        </h1>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{route('admin.transactions.account-transaction.store')}}" method='post' id="add_transaction">
                @csrf
                <div class="row g-3">
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group mb-0">
                        <label class="form-label" for="type">{{translate('messages.collect_from')}}<span class="input-label-secondary"></span></label>
                            <select name="type" id="type" class="form-control">
                                <option value="deliveryman">{{translate('messages.deliveryman')}}</option>
                                <option value="store">{{translate('messages.store')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label" for="store">{{translate('messages.store')}}<span class="input-label-secondary"></span></label>
                            <select id="store" name="store_id" data-placeholder="{{translate('messages.select_store')}}" class="form-control" title="Select Restaurant" disabled>

                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label" for="deliveryman">{{translate('messages.deliveryman')}}<span class="input-label-secondary"></span></label>
                            <select id="deliveryman" name="deliveryman_id" data-placeholder="{{translate('messages.select_deliveryman')}}" class="form-control" title="Select deliveryman">

                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label" for="method">{{translate('messages.payment_method')}}<span class="input-label-secondary"></span></label>
                            <input class="form-control" type="text" name="method" id="method" required maxlength="191" placeholder="{{translate('messages.Ex_:_Card')}}">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label" for="ref">{{translate('messages.reference')}}<span class="input-label-secondary"></span></label>
                            <input  class="form-control" type="text" name="ref" id="ref" maxlength="191">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label" for="amount">{{translate('messages.amount')}} {{ \App\CentralLogics\Helpers::currency_symbol() }}<span class="input-label-secondary" id="account_info"></span></label>
                            <input class="form-control" type="number" min=".01" step="0.01" name="amount" id="amount" max="999999999999.99" placeholder="{{translate('messages.Ex_:_1000')}}">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="btn--container justify-content-end">
                            <button class="btn btn--reset" type="reset" id="reset_btn">{{translate('messages.reset')}}</button>

                            <button class="btn btn--primary" type="submit">{{translate('messages.collect_cash')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-2 border-0">
                    <div class="search--button-wrapper">
                        <h5 class="card-title d-flex gap-2 align-items-center">
                            <span>
                                {{ translate('messages.transaction_history')}}
                            </span>
                            <span class="badge badge-soft-secondary" id="itemCount">
                                {{ $account_transaction->total() }}
                            </span>
                        </h5>

                        <form class="search-form theme-style">
                            <div class="input-group input--group">
                                <input id="datatableSearch" name="search" type="search" class="form-control h--40px" placeholder="{{translate('Ex:_reference,_Name')}}" value="{{ request()?->search ?? null}}" aria-label="{{translate('messages.search_here')}}">
                                <button type="submit" class="btn btn--secondary h--40px"><i class="tio-search"></i></button>
                            </div>
                        </form>

                        @if(request()->get('search'))
                            <button type="reset" class="btn btn--primary ml-2 location-reload-to-base" data-url="{{url()->full()}}">{{translate('messages.reset')}}</button>
                        @endif


                        <div class="hs-unfold mr-2">
                            <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40" href="javascript:;"
                                data-hs-unfold-options='{
                                        "target": "#usersExportDropdown",
                                        "type": "css-animation"
                                    }'>
                                <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                            </a>

                            <div id="usersExportDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                                <a id="export-excel" class="dropdown-item" href="{{route('admin.transactions.account-transaction.export', ['type'=>'excel',request()->getQueryString()])}}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                        alt="Image Description">
                                    {{ translate('messages.excel') }}
                                </a>
                                <a id="export-csv" class="dropdown-item" href="{{route('admin.transactions.account-transaction.export', ['type'=>'csv',request()->getQueryString()])}}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                        alt="Image Description">
                                    .{{ translate('messages.csv') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="datatable"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0">{{translate('SL')}}</th>
                                    <th class="border-0">{{ translate('messages.collect_from') }}</th>
                                    <th class="border-0">{{ translate('messages.type') }}</th>
                                    <th class="border-0">{{translate('messages.received_at')}}</th>
                                    <th class="border-0">{{translate('messages.amount')}}</th>
                                    <th class="border-0">{{translate('messages.reference')}}</th>
                                    <th class="border-0 text-center">{{translate('messages.action')}}</th>
                                </tr>
                            </thead>
                            <tbody id="set-rows">
                            @foreach($account_transaction as $k=>$at)
                                <tr>
                                    <td>{{$k+$account_transaction->firstItem()}}</td>
                                    <td>
                                        @if($at->store)
                                        <a href="{{route('admin.store.view',[$at->store['id'],'module_id'=>$at->store['module_id']])}}">{{ Str::limit($at->store->name, 20, '...') }}</a>
                                        @elseif($at->deliveryman)
                                        <a href="{{route('admin.users.delivery-man.preview',[$at->deliveryman->id])}}">{{ $at->deliveryman->f_name }} {{ $at->deliveryman->l_name }}</a>
                                        @else
                                            {{translate('messages.not_found')}}
                                        @endif
                                    </td>
                                    <td><label class="text-uppercase">{{translate($at['from_type'])}}</label></td>
                                    <td>{{\App\CentralLogics\Helpers::time_date_format($at->created_at)}}</td>
                                    <td><div class="pl-4">
                                        {{\App\CentralLogics\Helpers::format_currency($at['amount'])}}
                                    </div></td>
                                    <td><div title="{{ translate($at['ref']) }}" class="pl-4">
                                        {{Str::limit(translate($at['ref']),40,'...')}}

                                    </div></td>
                                    <td>
                                        <div class="btn--container justify-content-center"> <a href="#"
                                            data-payment_method="{{ $at->method }}"
                                            data-ref="{{translate($at['ref'])}}"
                                            data-amount="{{\App\CentralLogics\Helpers::format_currency($at['amount'])}}"
                                            data-date="{{\App\CentralLogics\Helpers::time_date_format($at->created_at)}}"
                                            data-type="{{ $at->from_type == 'deliveryman' ?  translate('DeliveryMan_Info') : translate('Store_Info') }}"
                                            data-phone="{{ $at->store ?  $at?->store?->phone : $at?->deliveryman?->phone  }}"
                                            data-address="{{ $at->store ?  $at?->store?->address : $at?->deliveryman?->last_location?->location ?? translate('address_not_found') }}"
                                            data-latitude="{{ $at->store ?  $at?->store?->latitude : $at?->deliveryman?->last_location?->location ?? 0 }}"
                                            data-longitude="{{ $at->store ?  $at?->store?->longitude : $at?->deliveryman?->last_location?->location ?? 0 }}"
                                            data-name="{{ $at->store ?  $at?->store?->name : $at?->deliveryman?->f_name.' '.$at?->deliveryman?->l_name }}"

                                            class="btn action-btn btn--warning btn-outline-warning withdraw-info-show" ><i class="tio-visible"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(count($account_transaction) !== 0)
                <hr>
                @endif
                <div class="page-area">
                    {!! $account_transaction->links() !!}
                </div>
                @if(count($account_transaction) === 0)
                <div class="empty--data">
                    <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                    <h5>
                        {{translate('no_data_found')}}
                    </h5>
                </div>
                @endif
            </div>
        </div>
     </div>
</div>


<div class="sidebar-wrap">
    <div class="withdraw-info-sidebar-overlay"></div>
    <div class="withdraw-info-sidebar">
        <div class="d-flex pb-3">
            <span class="circle bg-light withdraw-info-hide cursor-pointer">
                <i class="tio-clear"></i>
            </span>
        </div>

        <div class="d-flex flex-column align-items-center gap-1 mb-4">
            <h3 class="mb-3">{{translate('account_Transaction_Information')}}</h3>
            <div class="d-flex gap-2 align-items-center fs-12">
                <span>{{translate('method')}}:</span>
                <span id="payment_method" class="text-dark font-semibold"></span>
            </div>
            <div class="d-flex gap-2 align-items-center fs-12">
                <span>{{translate('amount')}}:</span>
                <span class="text-dark font-bold" id="amount"> </span>
            </div>
            <div class="d-flex gap-2 align-items-center fs-12">
                <span>{{translate('request_time')}}:</span>
                <span id="date"></span>
            </div>
            <div class="d-flex gap-2 align-items-center fs-12">
                <span>{{translate('reference')}}:</span>
                <span id="ref"></span>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 font-medium" id="type"></h6>
            </div>
            <div class="card-body">
                <div class="key-val-list d-flex flex-column gap-2" style="--min-width: 60px">
                    <div class="key-val-list-item d-flex gap-3">
                        <span>{{translate('name')}}:</span>
                        <span id="name"></span>
                    </div>
                    <div class="key-val-list-item d-flex gap-3">
                        <span>{{translate('phone')}}:</span>
                        <a href="tel:" id="phone" class="text-dark"></a>
                    </div>
                    <div class="key-val-list-item d-flex gap-3">
                        <span>{{translate('address')}}:</span>
                        <a id="address" target="_blank"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
    "use strict";
    $('.withdraw-info-hide, .withdraw-info-sidebar-overlay').on('click', function () {
        $('.withdraw-info-sidebar, .withdraw-info-sidebar-overlay').removeClass('show');
    });
    $('.withdraw-info-show').on('click', function () {

        let data = $(this).data();
        console.log(data)
            $('.sidebar-wrap #payment_method').text(data.payment_method);
            $('.sidebar-wrap #amount').text(data.amount);
            $('.sidebar-wrap #type').text(data.type);
            $('.sidebar-wrap #date').text(data.date);
            $('.sidebar-wrap #ref').text(data.ref);
            $('.sidebar-wrap #name') .text(data.name);
            $('.sidebar-wrap #phone').text(data.phone).attr('href', 'tel:' + data.phone);
            $('.sidebar-wrap #address').text(data.address).attr('href', "https://www.google.com/maps/search/?api=1&query=" + data.latitude + "," + data.longitude);
            // $('#deliverymanReviewModal').modal('show');

            $('.withdraw-info-sidebar, .withdraw-info-sidebar-overlay').addClass('show');

    })
</script>

<script src="{{asset('public/assets/admin')}}/js/view-pages/account-index.js"></script>
<script>
    "use strict";

    $('#store').select2({
        ajax: {
            url: '{{url('/')}}/admin/store/get-stores',
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data) {
                return {
                results: data
                };
            },
            __port: function (params, success, failure) {
                var $request = $.ajax(params);

                $request.then(success);
                $request.fail(failure);

                return $request;
            }
        }
    });

    $('#deliveryman').select2({
        ajax: {
            url: '{{url('/')}}/admin/users/delivery-man/get-deliverymen',
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data) {
                return {
                results: data
                };
            },
            __port: function (params, success, failure) {
                var $request = $.ajax(params);

                $request.then(success);
                $request.fail(failure);

                return $request;
            }
        }
    });

    $('#store').on('change', function() {
        $.get({
            url: '{{url('/')}}/admin/store/get-account-data/'+this.value,
            dataType: 'json',
            success: function (data) {
                $('#account_info').html('({{translate('messages.cash_in_hand')}}: '+data.cash_in_hand+' {{translate('messages.total_earning')}}: '+data.earning_balance+')');
            },
        });
    })

    $('#deliveryman').on('change', function() {
        $.get({
            url: '{{url('/')}}/admin/users/delivery-man/get-account-data/'+this.value,
            dataType: 'json',
            success: function (data) {
                $('#account_info').html('({{translate('messages.cash_in_hand')}}: '+data.cash_in_hand+' {{translate('messages.total_earning')}}: '+data.earning_balance+')');
            },
        });
    })

    $('#add_transaction').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.post({
            url: '{{route('admin.transactions.account-transaction.store')}}',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                if (data.errors) {
                    for (var i = 0; i < data.errors.length; i++) {
                        toastr.error(data.errors[i].message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                } else {
                    toastr.success('{{translate('messages.transaction_saved')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                    setTimeout(function () {
                        location.href = '{{route('admin.transactions.account-transaction.index')}}';
                    }, 2000);
                }
            }
        });
    });


</script>
@endpush
