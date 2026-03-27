@extends('layouts.admin.app')

@section('title', translate('Vendor Tax Report'))

@section('vendor_tax_report')
    active
@endsection
@section('content')
    <div class="content container-fluid">


        <!--- Vendor Tax Report -->
        <h2 class="mb-20">{{ translate('Vendor Tax Report') }}</h3>
            <div class="card p-20 mb-20">
                <form action="" method="get">
                    <div class="row g-lg-4 g-3 align-items-end">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label">{{ translate('Date Range') }}</label>
                            <div class="position-relative">
                                @php
                                    $dataRange = Carbon\Carbon::parse($startDate)->format('m/d/Y') . ' - ' . Carbon\Carbon::parse($endDate)->format('m/d/Y');
                                @endphp
                                <i class="tio-calendar-month icon-absolute-on-right"></i>
                                <input type="text" data-title="{{ translate('Select_Date_Range') }}" name="dates" value="{{ $dataRange  ?? null }}" class="date-range-picker form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <span class="mb-2 d-block title-clr fw-normal">{{ translate('Select Vendor') }}</span>
                            <select name="store_id" data-placeholder="{{ translate('Select Vendor') }}"
                                class="js-data-example-ajax form-control  custom-select custom-select-color border rounded w-100">
                                @if (isset($store))
                                    <option value="{{ $store->id }}" selected>{{ $store->name }}</option>
                                @else
                                    <option value="all" selected>{{ translate('messages.all_vendors') }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="d-flex justify-content-end">
                                <button type="submit"
                                    class="btn min-w-135px btn--primary">{{ translate('Filter') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card p-20 mb-20">
                <div class="row g-lg-4 g-3">
                    <div class="col-md-6 col-xl-4">
                        <div
                            class="bg--secondary rounded p-15 d-flex align-items-center justify-content-between gap-2 flex-wrap">
                            <div class="d-flex align-items-center gap-2 font-semibold title-clr">
                                <img src="{{ asset('/public/assets/admin/img/t-total-order.png') }}" alt="img">
                                {{ translate('Total Orders') }}
                            </div>
                            <h3 class="theme-clr fw-bold mb-0">{{ $totalOrders }}</h3>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div
                            class="bg--secondary rounded p-15 d-flex align-items-center justify-content-between gap-2 flex-wrap">
                            <div class="d-flex align-items-center gap-2 font-semibold title-clr">
                                <img src="{{ asset('/public/assets/admin/img/t-toal-amount.png') }}" alt="img">
                                {{ translate('Total Order Amount') }}
                            </div>
                            <h3 class="text-success fw-bold mb-0">
                                {{ \App\CentralLogics\Helpers::format_currency($totalOrderAmount) }}</h3>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div
                            class="bg--secondary rounded p-15 d-flex align-items-center justify-content-between gap-2 flex-wrap">
                            <div class="d-flex align-items-center gap-2 font-semibold title-clr">
                                <img src="{{ asset('/public/assets/admin/img/t-tax-amount.png') }}" alt="img">
                                {{ translate('Total Tax Amount') }}
                            </div>
                            <h3 class="text-danger fw-bold mb-0">
                                {{ \App\CentralLogics\Helpers::format_currency($totalTax) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <!--- Vendor Tax Report Here -->
            <div class="card p-20 mt-5">
                <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-20">
                    <h4 class="mb-0">{{ translate('All Vendor Taxes') }}</h4>
                    <div class="search--button-wrapper justify-content-end">
                        <form class="search-form min--260">
                            <div class="input-group input--group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control h--40px"
                                    placeholder="{{ translate('Search by Vendor Name') }} "
                                    value="{{ request()?->search ?? null }}"
                                    aria-label="{{ translate('messages.search') }}">
                                <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                            </div>
                        </form>
                        @if (request()->get('search'))
                            <button type="reset" class="btn btn--primary ml-2 location-reload-to-base"
                                data-url="{{ url()->full() }}">{{ translate('messages.reset') }}</button>
                        @endif
                        <!-- Datatable Info -->
                        <div id="datatableCounterInfo" class="mr-2 mb-2 mb-sm-0 initial-hidden">
                            <div class="d-flex align-items-center">
                                <span class="font-size-sm mr-3">
                                    <span id="datatableCounter">0</span>
                                    {{ translate('messages.selected') }}
                                </span>
                            </div>
                        </div>
                        <div class="hs-unfold mr-2">
                            <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle h--40px" href="javascript:;"
                                data-hs-unfold-options='{
                            "target": "#usersExportDropdown", "type": "css-animation" }'>
                                <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                            </a>
                            <div id="usersExportDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                                <a id="export-excel" class="dropdown-item" href="{{ route('admin.transactions.report.vendorWiseTaxExport', ['export_type' => 'excel', request()->getQueryString()]) }}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                        alt="Image Description">
                                    {{ translate('messages.excel') }}
                                </a>
                                <a id="export-csv" class="dropdown-item" href="{{ route('admin.transactions.report.vendorWiseTaxExport', ['export_type' => 'csv', request()->getQueryString()]) }}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                        alt="Image Description">
                                    .{{ translate('messages.csv') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Table -->
                <div class="table-responsive datatable-custom">
                    <table id="datatable"
                        class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table fz--14px">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{ translate('sl') }}</th>
                                <th class="border-0">{{ translate('Vendor Info') }}</th>
                                <th class="border-0">{{ translate('Total Order') }}</th>
                                <th class="border-0">{{ translate('Total Order Amount') }}</th>
                                <th class="border-0">{{ translate('Tax Amount') }}</th>
                                <th class="border-0 text-end">{{ translate('Action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($stores as $key => $store)
                                <tr>
                                    <td>
                                        {{ $key + $stores->firstItem() }}
                                    </td>
                                    <td>
                                        <span class="fz-14 title-clr">
                                            <a href="{{route('admin.store.view', $store->store_id)}}" target="_blank" rel="noopener noreferrer"> {{ $store->store_name }}</a>

                                            <span  class="fz-11 d-block"> <a href="tel:{{ $store->store_phone }}"> {{ $store->store_phone }}</a></span>
                                        </span>
                                    </td>
                                    <td>
                                        {{ $store->total_orders }}
                                    </td>
                                    <td>
                                        {{ \App\CentralLogics\Helpers::format_currency($store->total_order_amount) }}
                                    </td>
                                    <td>
                                        @php($sum_tax_amount=collect($store->tax_data)->sum('total_tax_amount'))

                                        <div class="d-flex flex-column gap-1">
                                            @if ($store->store_total_tax_amount - $sum_tax_amount > 0)
                                            <div class="d-flex fz-14 gap-3 align-items-center title-clr">
                                              {{ translate('Total Tax:') }} <span>
                                                    {{ \App\CentralLogics\Helpers::format_currency($store->store_total_tax_amount - $sum_tax_amount) }}</span>
                                            </div>
                                            @endif
                                            @if ($sum_tax_amount > 0 )
                                            <div class="d-flex fz-14 gap-3 align-items-center title-clr">
                                                {{ translate('Sum of Taxes:') }} <span>
                                                    {{ \App\CentralLogics\Helpers::format_currency($sum_tax_amount) }}</span>
                                            </div>
                                            @foreach ($store->tax_data as $tax)
                                                <div class="d-flex fz-11 gap-3 align-items-center">
                                                    {{ $tax['tax_name'] }}:
                                                    <span>{{ \App\CentralLogics\Helpers::format_currency($tax['total_tax_amount']) }}
                                                    </span>
                                                </div>
                                            @endforeach

                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a class="btn btn-sm btn--primary action-btn btn-outline-primary" target="_blank"
                                                href="{{ route('admin.transactions.report.vendorTax', ['id' => $store->store_id , 'dates' => $dateRange]) }}">
                                                <i class="tio-invisible"></i>
                                            </a>
                                            <a class="btn btn-sm action-btn success-border btn-outline-varify text-success"
                                                href="{{ route('admin.transactions.report.vendorTaxExport', ['export_type' => 'excel', 'id' => $store->store_id ,request()->getQueryString()]) }}">
                                                <svg width="11" height="12" viewBox="0 0 11 12" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M9.87499 4.31958H7.37499V0.56958H3.625V4.31958H1.125L5.5 9.31957L9.87499 4.31958ZM0.5 10.5696H10.5V11.8196H0.5V10.5696Z"
                                                        fill="#04BB7B" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <!-- End Table -->
                @if (count($stores) !== 0)
                    <hr>
                @endif
                <div class="page-area">
                    {!! $stores->links() !!}
                </div>
                @if (count($stores) === 0)
                    <div class="empty--data">
                        <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
                        <h5>
                            {{ translate('no_data_found') }}
                        </h5>
                    </div>
                @endif
            </div>
            <!--- Vendor Tax Details Page -->
    </div>

@endsection

@push('script_2')
    <script>

        "use strict";

        $(document).on('ready', function() {
            $('.js-data-example-ajax').select2({
                ajax: {
                    url: '{{ url('/') }}/admin/store/get-stores',
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            all: true,

                            page: params.page
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    __port: function(params, success, failure) {
                        let $request = $.ajax(params);

                        $request.then(success);
                        $request.fail(failure);

                        return $request;
                    }
                }
            });
        });
    </script>
@endpush
