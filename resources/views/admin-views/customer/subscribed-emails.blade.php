@php use App\CentralLogics\Helpers; @endphp
@extends('layouts.admin.app')
@section('title', translate('Subscribed Emails'))
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title mr-3">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/email.png')}}" class="w--26" alt="">
                </span>
                <span>{{ translate('messages.Subscriber List') }}
                        {{-- <span class="badge badge-soft-dark ml-2" id="count">{{$subscribedCustomers->count() }}</span> --}}
                </span>
            </h1>
        </div>
        <!-- Page Header -->
        <div class="card mb-3">
            <div class="card-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{translate('Subscription Date')}}</label>
                            <div class="position-relative">
                                <span class="tio-calendar icon-absolute-on-right"></span>
                                <input type="text" readonly data-title="{{ translate('Select_Subscription_Date_Range') }}" name="join_date" value="{{ request()->get('join_date')  ?? null }}" class="date-range-picker form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{translate('Sort By')}}</label>
                            <select name="filter" data-placeholder="{{ translate('messages.Select Mail Sorting Order') }}" class="form-control js-select2-custom">
                                <option  value="" selected disabled > {{ translate('messages.Select Mail Sorting Order') }} </option>
                                <option  {{ request()->get('filter')  == 'oldest'?'selected':''}} value="oldest">{{ translate('messages.Sort by oldest') }}</option>
                                <option  {{ request()->get('filter')  == 'latest'?'selected':''}} value="latest">{{ translate('messages.Sort by newest') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{translate('Choose First')}}</label>
                            <input type="number" min="1" name="show_limit" class="form-control" value="{{ request()->get('show_limit')}}" class="form-control" placeholder="{{translate('Ex : 100')}}">
                        </div>
                    </div>
                    <div class="btn--container justify-content-end mt-20">
                        <button type="submit" class="btn btn--primary">{{translate('Filter')}}</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header border-0 py-2">

                <h4>{{ translate('messages.Mail List') }}
                    <span class="badge badge-soft-dark ml-2" id="count">{{$subscribedCustomers->count() }}</span>
                </h4>


                <div class="search--button-wrapper justify-content-end">
                    <form class="search-form">
                        <div class="input-group input--group">
                            <input type="search" name="search" class="form-control"
                                   placeholder="{{translate('ex_: search_email')}}"
                                   aria-label="{{translate('messages.search')}}" value="{{request()?->search}}">
                            <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                        </div>
                    </form>
                    @if(request()->get('search'))
                        <button type="reset" class="btn btn--primary ml-2 location-reload-to-base"
                                data-url="{{url()->full()}}">{{translate('messages.reset')}}</button>
                    @endif

                    <!-- Unfold -->
                    <div class="hs-unfold mr-2">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40"
                           href="javascript:"
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
                               href="{{route('admin.users.customer.subscriber-export', ['type'=>'excel',request()->getQueryString()])}}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                     src="{{ asset('public/assets/admin/svg/components/excel.svg') }}"
                                     alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item"
                               href="{{route('admin.users.customer.subscriber-export', ['type'=>'csv',request()->getQueryString()])}}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                     src="{{ asset('public/assets/admin/svg/components/placeholder-csv-format.svg') }}"
                                     alt="Image Description">
                                .{{ translate('messages.csv') }}
                            </a>
                        </div>
                    </div>
                    <!-- End Unfold -->
                </div>
            </div>
            <!-- End Header -->

            @php
            $count= 0;
            @endphp
            <div class="card-body p-0">
                <!-- Table -->
                <div class="table-responsive datatable-custom">
                    <table id="datatable"
                        class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table generalData"
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
                            <th class="border-0">{{ translate('messages.email') }}</th>
                            <th class="border-0">{{ translate('messages.created_at') }}</th>
                        </tr>
                        </thead>
                        <tbody id="set-rows">
                        @if (count($subscribedCustomers))
                            @foreach ($subscribedCustomers as $key => $customer)
                                <tr>
                                    <td>
                                        {{ (request()->get('show_limit') ?  $count++ : $key  )+ $subscribedCustomers->firstItem() }}
                                    </td>

                                    <td>
                                        {{ $customer->email }}
                                    </td>
                                    <td>  {{  Helpers::time_date_format($customer->created_at)}} </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>

                    </table>
                </div>
                @if(count($subscribedCustomers) !== 0)
                    <hr>
                @endif
                <div class="page-area">
                    {!! $subscribedCustomers->withQueryString()->links() !!}
                </div>
                @if(count($subscribedCustomers) === 0)
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
@endsection

