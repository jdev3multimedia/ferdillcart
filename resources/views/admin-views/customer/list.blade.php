@extends('layouts.admin.app')

@section('title', translate('Customer List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title mr-3">
                <span class="page-header-icon">
                    <img src="{{asset('/public/assets/admin/img/people.png')}}" class="w--26" alt="">
                </span>
                <span>
                     {{ translate('messages.customers') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card mb-3">
            <div class="card-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{translate('Order Date')}}</label>
                            <div class="position-relative">
                                <span class="tio-calendar icon-absolute-on-right"></span>
                                <input type="text" data-title="{{ translate('Select_Order_Date_Range') }}" data-startDate="09/04/2024"  data-endDate="09/24/2024" readonly name="order_date" value="{{ request()->get('order_date')  ?? null }}" class="date-range-picker form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{translate('Customer Joining Date')}}</label>
                            <div class="position-relative">
                                <span class="tio-calendar icon-absolute-on-right"></span>
                                <input type="text" data-title="{{ translate('Select_Customer_Joining_Date_Range') }}" readonly name="join_date" value="{{ request()->get('join_date') ?? null }}" class="date-range-picker form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{translate('Customer status')}}</label>
                            <select name="filter" data-placeholder="{{ translate('messages.Select_Status') }}" class="form-control js-select2-custom ">
                                <option  value="" selected disabled > {{ translate('messages.Select_Status') }} </option>
                                <option  {{ request()->get('filter')  == 'all'?'selected':''}} value="all">{{ translate('messages.All_Customers') }}</option>
                                <option  {{ request()->get('filter')  == 'active'?'selected':''}} value="active">{{ translate('messages.Active_Customers') }}</option>
                                <option  {{ request()->get('filter')  == 'blocked'?'selected':''}} value="blocked">{{ translate('messages.Inactive_Customers') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{translate('Sort By')}}</label>
                            <select name="order_wise"  data-placeholder="{{ translate('messages.Select Customer Sorting Order') }}"

                            class="form-control js-select2-custom">
                                <option value="" selected disabled > {{ translate('messages.Select Customer Sorting Order') }} </option>
                                <option  {{ request()->get('order_wise')  == 'top'?'selected':''}}  value="top">{{ translate('messages.Sort by order count') }}</option>
                                <option {{ request()->get('order_wise')  == 'order_amount'?'selected':''}}  value="order_amount">{{ translate('messages.Sort by order amount') }}</option>
                                <option {{ request()->get('order_wise')  == 'oldest'?'selected':''}}  value="oldest">{{ translate('messages.Sort by oldest') }}</option>
                                <option {{ request()->get('order_wise')  == 'latest'?'selected':''}}  value="latest">{{ translate('messages.Sort by newest') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{translate('Choose First')}}</label>
                            <input type="number" min="1" name="show_limit" class="form-control" value="{{ request()->get('show_limit')}}" placeholder="{{translate('Ex : 100')}}">
                        </div>
                        <div class="col-md-4">
                            <label class="d-md-block">&nbsp;</label>
                            <div class="btn--container justify-content-end">
                                <button type="submit" class="btn btn--primary">{{translate('Filter')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header border-0  py-2">
                <h3>
                    {{ translate('messages.customer_list') }} <span class="badge badge-soft-dark ml-2" id="count">{{ $customers->total() }}</span>
                </h3>
                <div class="search--button-wrapper justify-content-end">


                    <form class="search-form">
                        <!-- Search -->
                        <div class="input-group input--group">
                            <input id="datatableSearch_" type="search" name="search" class="form-control min-height-40"
                                value="{{ request()->get('search') }}" placeholder="{{ translate('ex:_name_email_or_phone') }}"
                                aria-label="Search" >
                            <button type="submit" class="btn btn--secondary min-height-40"><i class="tio-search"></i></button>

                        </div>
                        <!-- End Search -->
                    </form>
                    @if(request()->get('search'))
                    <button type="reset" class="btn btn--primary ml-2 location-reload-to-base" data-url="{{url()->full()}}">{{translate('messages.reset')}}</button>
                    @endif

                    <!-- Unfold -->
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
                            <a id="export-excel" class="dropdown-item" href="{{route('admin.customer.export', ['type'=>'excel',request()->getQueryString()])}}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="{{route('admin.customer.export', ['type'=>'csv',request()->getQueryString()])}}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                    alt="Image Description">
                                .{{ translate('messages.csv') }}
                            </a>
                        </div>
                    </div>
                </div>
                <!-- End Row -->
            </div>
            <!-- End Header -->

            <div class="card-body p-0">
                <!-- Table -->
                <div class="table-responsive datatable-custom">
                    <table id="datatable"
                        class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table" data-hs-datatables-options='{
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
                            "pageLength": 25,
                            "isResponsive": false,
                            "isShowPaging": false,
                            "paging":false
                        }'>
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0">
                                    {{ translate('sl') }}
                                </th>
                                <th class="table-column-pl-0 border-0">{{ translate('messages.name') }}</th>
                                <th class="border-0">{{ translate('messages.contact_information') }}</th>
                                <th class="border-0">{{ translate('messages.total_order') }}</th>
                                <th class="border-0">{{ translate('messages.total_order_amount') }}</th>
                                <th class="border-0">{{ translate('messages.Joining_date') }}</th>
                                <th class="border-0">{{ translate('messages.active') }}/{{ translate('messages.inactive') }}</th>
                                <th class="border-0">{{ translate('messages.actions') }}</th>
                            </tr>
                        </thead>
                        @php
                            $count= 0;
                        @endphp
                        <tbody id="set-rows">
                            @foreach ($customers as $key => $customer)

                                <tr class="">
                                    <td class="">
                                        {{ (request()->get('show_limit') ?  $count++ : $key  )+ $customers->firstItem() }}
                                    </td>
                                    <td class="table-column-pl-0">
                                        <div class="d-flex align-items-center gap-2">
                                            <img class="rounded aspect-1-1 object-cover" width="40" data-onerror-image="{{asset('public/assets/admin/img/160x160/img1.jpg')}}" src="{{ $customer->image_full_url }}" alt="Image Description">
                                            <a href="{{ route('admin.users.customer.view', [$customer['id']]) }}" class="text--hover max-w-400px min-w-220">
                                                {{ $customer['f_name'] ?  $customer['f_name'] . ' ' . $customer['l_name'] : translate('messages.Incomplete_Profile') }}
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <a href="mailto:{{ $customer['email'] }}">
                                                {{ $customer['email'] }}
                                            </a>
                                        </div>
                                        <div>
                                            <a href="tel:{{ $customer['phone'] }}">
                                                {{ $customer['phone'] }}
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <label class="badge">
                                            {{ $customer->orders_count }}
                                        </label>
                                    </td>
                                    <td>
                                        <label class="badge">
                                            {{  \App\CentralLogics\Helpers::format_currency( $customer->orders()->sum('order_amount'))}}
                                        </label>
                                    </td>
                                    <td>
                                        <label class="badge">
                                            {{  \App\CentralLogics\Helpers::date_format( $customer->created_at)}}
                                        </label>
                                    </td>
                                    <td>
                                        <label class="toggle-switch toggle-switch-sm ml-xl-4" for="stocksCheckbox{{ $customer->id }}">
                                            <input type="checkbox" data-url="{{ route('admin.users.customer.status', [$customer->id, $customer->status ? 0 : 1]) }}" data-message="{{ $customer->status? translate('messages.you_want_to_block_this_customer'): translate('messages.you_want_to_unblock_this_customer') }}"
                                                class="toggle-switch-input status_change_alert" id="stocksCheckbox{{ $customer->id }}"
                                                {{ $customer->status ? 'checked' : '' }}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <a class="btn action-btn btn--warning btn-outline-warning"
                                            href="{{ route('admin.users.customer.view', [$customer['id']]) }}"
                                            title="{{ translate('messages.view_customer') }}"><i
                                                class="tio-visible-outlined"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- End Table -->
            </div>

            @if(count($customers) !== 0)
            <hr>
            @endif
            <div class="page-area">
                {!! $customers->withQueryString()->links() !!}
            </div>
            @if(count($customers) === 0)
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
    <script src="{{asset('public/assets/admin')}}/js/view-pages/customer-list.js"></script>
    <script>
        "use strict";

        $('.status_change_alert').on('click', function (event) {
            let url = $(this).data('url');
            let message = $(this).data('message');
            status_change_alert(url, message, event)
        })

        function status_change_alert(url, message, e) {
            e.preventDefault();
            Swal.fire({
                title: '{{ translate('messages.Are you sure?') }}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: '{{ translate('messages.no') }}',
                confirmButtonText: '{{ translate('messages.Yes') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    location.href = url;
                }
            })
        }

    </script>
@endpush
