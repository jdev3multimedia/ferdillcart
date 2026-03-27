@extends('layouts.admin.app')

@section('title',translate('messages.Order List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        @php($parcel_order = Request::is('admin/parcel/orders*'))
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-xl-12 col-md-12 col-sm-12 mb-3 mb-sm-0">
                    <h1 class="page-header-title text-capitalize m-0">
                        <span class="page-header-icon">
                            <img src="{{asset('public/assets/admin/img/fi_273177.svg')}}" class="w--26" alt="">
                        </span>
                        <span>
                        {{translate('messages.Verify_Offline_Payments')}}
                            <span class="badge badge-soft-dark ml-2">{{$orders->total()}}</span>
                        </span>
                    </h1>
                    <span class="badge badge-soft-danger text-start text-body fw-medium gap-1 mt-20 mb-20 border py-2 px-3 d-flex align-itmes">
                       <i class="tio-warning text-danger"></i> {{ translate('For_offline_payments_please_verify_if_the_payments_are_safely_received_to_your_account._Customer_id_not_liable_if_you_confirm_and_deliver_the_orders_without_checking_payments_transactions')}}
                    </span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="js-nav-scroller hs-nav-scroller-horizontal">
                        <!-- Nav -->
                        <ul class="nav nav-tabs mb-3 border-0 nav--tabs nav--pills">
                            <li class="nav-item">
                                <a class="nav-link {{ $status ==  'all' ? 'active' : ''}}" href="{{ route('admin.order.offline_verification_list', ['all']) }}"   aria-disabled="true">{{translate('messages.All')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status ==  'pending' ? 'active' : ''}}" href="{{ route('admin.order.offline_verification_list', ['pending']) }}"  aria-disabled="true">{{translate('messages.Pending')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status ==  'verified' ? 'active' : ''}}" href="{{ route('admin.order.offline_verification_list', ['verified']) }}"  aria-disabled="true">{{translate('messages.verified')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status ==  'denied' ? 'active' : ''}}" href="{{ route('admin.order.offline_verification_list', ['denied']) }}"  aria-disabled="true">{{translate('messages.Denied')}}</a>
                            </li>
                        </ul>
                        <!-- End Nav -->
                    </div>
                </div>
            </div>
            <!-- End Row -->
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header gap-2 flex-wrap pb-0 pt-3 border-0">
                <h5 class="m-0">{{translate('messages.Offline Payments List')}}</h5>
                <div class="search--button-wrapper justify-content-end">
                    <form class="search-form min--260">
                        <!-- Search -->
                        <div class="input-group input--group rounded overflow-hidden">
                            <input id="datatableSearch_" type="search" name="search" class="form-control h--40px"
                                    placeholder="{{ translate('messages.Ex:') }} 10010" value="{{ request()?->search ?? null}}" aria-label="{{translate('messages.search')}}">
                            <button type="submit" class="btn bg-modal-btn rounded-0"><i class="tio-search"></i></button>

                        </div>
                        <!-- End Search -->
                    </form>
                    @if(request()->get('search'))
                    <button type="reset" class="btn btn--primary ml-2 location-reload-to-base" data-url="{{url()->full()}}">{{translate('messages.reset')}}</button>
                    @endif


                    <!-- Datatable Info -->
                    <div id="datatableCounterInfo" class="mr-2 mb-2 mb-sm-0 initial-hidden">
                        <div class="d-flex align-items-center">
                                <span class="font-size-sm mr-3">
                                <span id="datatableCounter">0</span>
                                {{translate('messages.selected')}}
                                </span>
                        </div>
                    </div>
                    <!-- End Datatable Info -->

                    <!-- Unfold -->
                    <div class="hs-unfold mr-2">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle h--40px" href="javascript:;"
                            data-hs-unfold-options='{
                                "target": "#usersExportDropdown",
                                "type": "css-animation"
                            }'>
                            <i class="tio-download-to mr-1"></i> {{translate('messages.export')}}
                        </a>

                        <div id="usersExportDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                            <span class="dropdown-header">{{translate('messages.options')}}</span>
                            <div class="dropdown-divider"></div>
                            <span class="dropdown-header">{{translate('messages.download_options')}}</span>
                            <a id="export-excel" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{asset('public/assets/admin')}}/svg/components/excel.svg"
                                        alt="Image Description">
                                {{translate('messages.excel')}}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{asset('public/assets/admin')}}/svg/components/placeholder-csv-format.svg"
                                        alt="Image Description">
                                .{{translate('messages.csv')}}
                            </a>

                        </div>
                    </div>

                    <!-- End Unfold -->
                </div>
            </div>
            <!-- End Header -->

            <div class="card-body">
                <div class="shadow-sm">
                    <!-- Table -->
                    <div class="table-responsive m-0 datatable-custom">
                        <table id="datatable"
                                class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table fz--14px"
                                data-hs-datatables-options='{
                                "columnDefs": [{
                                    "targets": [0],
                                    "orderable": false
                                }],
                                "order": [],
                                "info": {
                                "totalQty": "#datatableWithPaginationInfoTotalQty"
                                },
                                "search": "#datatableSearch",
                                "entries": "#datatableEntries",
                                "isResponsive": false,
                                "isShowPaging": false,
                                "paging": false
                            }'>
                            <thead class="thead-light">
                            <tr>
                                <th class="border-0">
                                    {{translate('messages.sl')}}
                                </th>
                                <th class="table-column-pl-0 border-0">{{translate('messages.order_id')}}</th>
                                <th class="border-0">{{translate('messages.order_date')}}</th>
                                <th class="border-0">{{translate('messages.customer_information')}}</th>
                                <th class="border-0">{{translate('messages.total_amount')}}</th>
                                <th class="text-center border-0">{{translate('messages.Payment_Method')}}</th>
                                <th class="text-center border-0">{{translate('messages.actions')}}</th>
                            </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($orders as $key=>$order)

                                <tr class="status-{{$order['order_status']}} class-all">
                                    <td class="text-title">
                                        {{$key+$orders->firstItem()}}
                                    </td>
                                    <td class="table-column-pl-0">
                                        <a href="{{route($parcel_order?'admin.parcel.order.details':'admin.order.details',['id'=>$order['id']])}}" class="text-title">{{$order['id']}}</a>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="text-title">
                                                {{date('d M Y',strtotime($order['created_at']))}}
                                            </div>
                                            <div class="d-block text-uppercase text-title">
                                                {{date(config('timeformat'),strtotime($order['created_at']))}}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($order->customer)
                                            <a class="text-title text-capitalize" href="{{route('admin.customer.view',[$order['user_id']])}}">
                                                <strong>{{$order->customer['f_name'].' '.$order->customer['l_name']}}</strong>
                                                <div>{{$order->customer['phone']}}</div>
                                            </a>
                                        @elseif($order->is_guest)
                                            @php($customer_details = json_decode($order['delivery_address'],true))
                                            <strong>{{$customer_details['contact_person_name']}}</strong>
                                            <div>{{$customer_details['contact_person_number']}}</div>
                                        @else
                                            <label class="badge badge-danger">{{translate('messages.invalid_customer_data')}}</label>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="text-right mw--85px">
                                            <div class="text-title">
                                                {{\App\CentralLogics\Helpers::format_currency($order['order_amount'])}}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-capitalize text-title text-center">
                                        {{
                                            optional(json_decode($order?->offline_payments?->payment_info ?? '', true))['method_name']
                                            ?? 'N/A'
                                        }}
                                    </td>
                                    <td>
                                        @if ($order?->offline_payments?->status == 'pending')
                                            <div class="btn--container justify-content-center">
                                                <button  type="button" class="btn btn--primary btn-sm fs-12 px-3" data-toggle="modal" data-target="#verifyViewModal-{{ $key }}" >{{ translate('messages.Verify_Payment') }}</button>
                                            </div>

                                            @elseif($order?->offline_payments?->status == 'verified')
                                            <div class="btn--container justify-content-center">
                                                <button  type="button" class="btn btn--primary btn-sm fs-12 px-3" data-toggle="modal" data-target="#verifyViewModal-{{ $key }}" >{{ translate('messages.verified') }}</button>
                                            </div>
                                            @elseif($order?->offline_payments?->status == 'denied')
                                            <div class="btn--container justify-content-center">
                                                <button  type="button" class="btn py-2 badge-soft-danger btn-sm fs-13 px-3" data-toggle="modal" data-target="#verifyViewModal-{{ $key }}" >{{ translate('messages.Recheck_Verification') }}</button>
                                            </div>
                                        @endif

                                        @if(!$order?->offline_payments)
                                            <div class="btn--container justify-content-center">
                                                <button  type="button" class="btn btn--primary btn-sm fs-12 px-3" data-toggle="modal" data-target="#verifyViewModal-{{ $key }}" >{{ translate('messages.Verify_Payment') }}</button>
                                            </div>
                                        @endif

                                    </td>
                                </tr>

                                        <!-- End Card -->
                    <div class="modal fade" id="verifyViewModal-{{ $key }}" tabindex="-1" aria-labelledby="verifyViewModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-end  border-0 pt-3 px-3">
                                    <button type="button" class="close border bg-modal-btn rounded-circle" data-dismiss="modal">
                                        <span aria-hidden="true" class="tio-clear"></span>
                                    </button>
                                </div>
                            <div class="modal-body pt-0">
                            <div class="d-flex align-items-center flex-column gap-1 mb-xxl-5 mb-4 text-center">
                                <h2 class="mb-0">
                                    {{ translate('Payment Verification') }}

                                    @if(optional($order->offline_payments)->status === 'verified')
                                        <span class="badge badge-soft-success mt-3 mb-3">
                                        {{ translate('messages.verified') }}
                                    </span>
                                    @endif
                                </h2>

                                @unless(optional($order->offline_payments)->status === 'verified')
                                    <p class="text-danger mb-0 mt-0">
                                        {{ translate('Please check and verify the payment information before confirming the order.') }}
                                    </p>
                                @endunless
                            </div>

                            <div class="card border-0">
                                <div class="bg-light2 p-xxl-20 p-3 rounded">
                                    <div class="adjust-information-payment flex-md-nowrap flex-wrap">
                                        <div class="bg-white p-3 rounded w-100">
                                            <h4 class="mb-3 fs-16">{{ translate('messages.customer_information') }}</h4>
                                            <div class="d-flex flex-column gap-2">
                                                @if($order->customer)
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="customer-namekey">{{translate('Name')}}</span>:
                                                    <span class="text-dark"> <a class="text-dark text-capitalize" href="{{route('admin.customer.view',[$order['user_id']])}}"> {{$order->customer['f_name'].' '.$order->customer['l_name']}}  </a>  </span>
                                                </div>

                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="customer-namekey">{{translate('Contact')}}</span>:
                                                    <span class="text-dark">{{$order->customer['phone']}}  </span>
                                                </div>

                                                @elseif($order->is_guest)
                                                    @php($customer_details = json_decode($order['delivery_address'],true))

                                                    <div class="d-flex align-items-center gap-2">
                                                        <span>{{translate('Name')}}</span>:
                                                        <span class="text-dark"> {{$customer_details['contact_person_name']}}</span>
                                                    </div>

                                                    <div class="d-flex align-items-center gap-2">
                                                        <span>{{translate('Phone')}}</span>:
                                                        <span class="text-dark">  {{$customer_details['contact_person_number']}}</span>
                                                    </div>

                                                @else
                                                    <label class="badge badge-danger">{{translate('messages.invalid_customer_data')}}</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="bg-white p-3 rounded h-100 w-100">
                                            <div class="">
                                                <h4 class="mb-3 fs-16">{{ translate('messages.Payment_Information') }}</h4>
                                                @if($order?->offline_payments)
                                                    <div class="row g-1">
                                                        @foreach (json_decode($order?->offline_payments?->payment_info ?? '[]') as $key=>$item)
                                                            @if ($key != 'method_id')
                                                         <?php
                                                                $key = match ($key) {
                                                                    'method_name'    => 'Payment Method',
                                                                    'name'           => 'Payment By',
                                                                    'date'           => 'Date',
                                                                    'transaction_id' => 'Transaction ID',
                                                                    default          => $key,
                                                                };
                                                            ?>
                                                            <div class="col-sm-12">
                                                                <div class="d-flex align-items-center gap-3">
                                                                    <span class="namekey"> {{translate($key)}}</span>:
                                                                    <span class="text-dark text-break">{{ $item }}</span>
                                                                </div>
                                                            </div>
                                                            @endif
                                                        @endforeach
                                                    </div>

                                                    {{-- <div class="d-flex flex-column gap-2 mt-3">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="namekey">{{translate('Customer_Note')}}</span>:
                                                            <span class="text-dark text-break">{{$order->offline_payments?->customer_note ?? translate('messages.N/A')}} </span>
                                                        </div>

                                                    </div> --}}
                                                @else
                                                    <div class="row g-1">
                                                        <div class="col-sm-12">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="namekey">{{translate('Payment Method')}}</span>:
                                                                <span class="text-dark text-break">{{translate('messages.N/A')}} </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($order?->offline_payments?->status != 'verified')
                            <div class="btn--container justify-content-end mt-xxl-5 mt-4 pt-xxl-1">
                                @if ($order?->offline_payments?->status != 'denied')
                                    <button type="button" class="btn btn--reset offline_payment_cancelation_note" data-toggle="modal" data-target="#offline_payment_cancelation_note" data-id="{{ $order['id'] }}" class="btn btn--reset">{{translate('Payment_didn’t_Receive')}}</button>
                                @elseif ($order?->offline_payments?->status == 'denied')
                                    <button type="button" data-url="{{ route('admin.order.offline_payment', [ 'id' => $order['id'], 'verify' => 'switched_to_cod', ]) }}" data-message="{{ translate('messages.Make_the_payment_switched_to_cod_for_this_order') }}" class="btn btn--reset route-alert">{{translate('Switched_to_COD')}}</button>
                                @endif
                                @if($order?->offline_payments)
                                    <button type="button" data-url="{{ route('admin.order.offline_payment', [ 'id' => $order['id'], 'verify' => 'yes', ]) }}" data-message="{{ translate('messages.Make_the_payment_verified_for_this_order') }}" class="btn btn--primary route-alert">{{translate('Yes,_Payment_Received')}}</button>
                                @else
                                        <button type="button" class="btn btn--primary btn-sm form-alert"
                                                data-id="order-{{$order['id']}}"
                                                data-cancel-btn="{{ translate('messages.Cancel') }}"
                                                data-confirm-btn="{{ translate('messages.Confirm') }}"
                                                data-image-url="{{ asset('public/assets/admin/img/tughrik.png') }}"
                                                data-title="{{ translate('Switch to Cash on Delivery?') }}"
                                                data-message="{{ translate('The customer’s offline payment has failed. Before switching this order to Cash on Delivery (COD), please confirm the payment issue with the customer to avoid any misunderstandings.') }}">
                                            {{ translate('messages.Switch to COD') }}
                                        </button>
                                    <form action="{{route('admin.order.switch_to_cod',[$order['id']])}}"
                                          method="post" id="order-{{$order['id']}}">
                                        @csrf
                                    </form>
                                @endif
                            </div>
                        @endif
                                </div>
                            </div>
                        </div>
                    </div>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- End Table -->
                    @if(count($orders) !== 0)

                    @endif
                    <div class="page-area border-top">
                        {!! $orders->appends($_GET)->links() !!}
                    </div>
                    @if(count($orders) === 0)
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

            <!-- Modal -->
    <!-- <div class="modal fade" id="offline_payment_cancelation_note" tabindex="-1" role="dialog"
        aria-labelledby="offline_payment_cancelation_note_l" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="offline_payment_cancelation_note_l">{{ translate('messages.Add_Offline_Payment_Rejection_Note') }}</h5>
                    <button type="button" class="close border bg-modal-btn rounded-circle" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.order.offline_payment') }}" method="get">
                        <input type="hidden" name="id" id="myorderId">
                        <input type="text" required class="form-control" name="note" value="{{ old('note') }}"
                            placeholder="{{ translate('transaction_id_mismatched') }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{  translate('Cancel') }}</button>
                    <button type="submit" class="btn btn--danger btn-outline-danger">{{ translate('messages.Confirm_Rejection') }} </button>
                    </form>
                </div>
            </div>
        </div>
    </div> -->
    <div class="modal fade" id="offline_payment_cancelation_note" tabindex="-1" role="dialog"
        aria-labelledby="offline_payment_cancelation_note_l" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-600" role="document">
            <div class="modal-content">
                <div class="modal-header px-2 pt-2">
                    <button type="button" class="close min-w-28 border bg-modal-btn rounded-circle" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.order.offline_payment') }}" method="get">
                        <div class="cont mb-4 text-center pb-xxl-1">
                            <img width="60px" height="60px" src="{{asset('/public/assets/admin/img/delete-confirmation.png')}}" alt="public" class="mb-20">
                            <h3 class="mb-xl-2 mb-1">
                                {{translate('Are you sure the payment was not received?')}}
                            </h3>
                            <p class="mb-0 fs-14 max-w-420 mx-auto">
                                Please insert a <span class="text-title">Denied</span> note for this payment request to inform the customer.
                            </p>
                        </div>
                        <div class="bg-light2 rounded p-3">
                            <label class="form-label">
                                Denied Note
                                <span class="custom-tooltip" data-title="payment request to inform the customer ">
                                    <i class="tio-info text-muted"></i>
                                </span>
                            </label>
                            <input type="hidden" name="id" id="myorderId">
                            <textarea type="text" rows="1" required class="form-control" maxlength="100" name="note" value="{{ old('note') }}"
                            placeholder="{{ translate('transaction_id_mismatched') }}"></textarea>
                            <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                        </div>
                </div>
                <div class="modal-footer border-0 pt-2">
                    <button type="button" class="btn btn--reset h-40px min-w-120px py-2 fs-14" data-dismiss="modal">{{  translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary h-40px min-w-120px py-2 fs-14">{{ translate('messages.Submit') }} </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<!-- End Modal -->
@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin')}}/js/view-pages/offline-verification-list.js"></script>
    <script>
        "use strict";
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            let datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        className: 'd-none'
                    },
                    {
                        extend: 'excel',
                        className: 'd-none',
                        action: function (e, dt, node, config)
                        {
                            window.location.href = '{{route("admin.order.export",['status'=>$status,'file_type'=>'excel','type'=>$parcel_order?'parcel':'order', request()->getQueryString()])}}';
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'd-none',
                        action: function (e, dt, node, config)
                        {
                            window.location.href = '{{route("admin.order.export",['status'=>$status,'file_type'=>'csv','type'=>$parcel_order?'parcel':'order', request()->getQueryString()])}}';
                        }
                    },
                    // {
                    //     extend: 'pdf',
                    //     className: 'd-none'
                    // },
                    {
                        extend: 'print',
                        className: 'd-none'
                    },
                ],
                select: {
                    style: 'multi',
                    selector: 'td:first-child input[type="checkbox"]',
                    classMap: {
                        checkAll: '#datatableCheckAll',
                        counter: '#datatableCounter',
                        counterInfo: '#datatableCounterInfo'
                    }
                },
                language: {
                    zeroRecords: '<div class="text-center p-4">' +
                        '<img class="w-7rem mb-3" src="{{asset('public/assets/admin')}}/svg/illustrations/sorry.svg" alt="Image Description">' +

                        '</div>'
                }
            });
        });
    </script>

@endpush
