@extends('layouts.admin.app')

@section('title', translate('Parcel Tax Report'))

@section('parcel_tax_report')
    active
@endsection
@section('content')
    <div class="content container-fluid">


        <!--- Vendor Tax Report -->
        <h2 class="mb-20">{{ translate('Parcel Tax Report') }}</h3>
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
                            <div class="d-flex justify-content-start">
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
                    <h4 class="mb-0">{{ translate('All Taxes') }}</h4>
                    <div class="search--button-wrapper justify-content-end">
                        <div class="hs-unfold mr-2">
                            <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle h--40px" href="javascript:;"
                                data-hs-unfold-options='{
                            "target": "#usersExportDropdown", "type": "css-animation" }'>
                                <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                            </a>
                            <div id="usersExportDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                                <a id="export-excel" class="dropdown-item" href="{{ route('admin.transactions.report.parcel-wise-tax-export', ['export_type' => 'excel', request()->getQueryString()]) }}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                        alt="Image Description">
                                    {{ translate('messages.excel') }}
                                </a>
                                <a id="export-csv" class="dropdown-item" href="{{ route('admin.transactions.report.parcel-wise-tax-export', ['export_type' => 'csv', request()->getQueryString()]) }}">
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
                                <th class="border-0">{{ translate('Order Id') }}</th>
                                <th class="border-0">{{ translate('Total Order Amount') }}</th>
                                <th class="border-0">{{ translate('Tax Amount') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($orders as $key => $order)
                                <tr>
                                    <td>
                                        {{ $key + $orders->firstItem() }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.parcel.order.details', ['id' => $order['id']]) }}">{{ $order->id }}</a>
                                    </td>
                                    <td>
                                        {{ \App\CentralLogics\Helpers::format_currency($order->order_amount) }}
                                    </td>
                                    <td>
                                        <?php
                                        if ($order?->tax_type == 'category_wise') {
                                            $tax_type = 'category_tax';
                                        } elseif ($order?->tax_type == 'product_wise') {
                                            $tax_type = 'product_tax';
                                        } else {
                                            $tax_type = 'order_wise';
                                        }

                                        $taxLabels = [
                                            'basic' => translate($tax_type),
                                            'tax_on_packaging_charge' => translate('Packaging Charge'),
                                        ];

                                        $groupedByTaxOn = $order->orderTaxes->groupBy('tax_on');
                                        $totalTaxAmount = $order->orderTaxes->sum('tax_amount');
                                        ?>

                                        <div class="d-flex flex-column gap-1">
                                            @if (count($order->orderTaxes) > 0)
                                                <div class="fw-bold">
                                                    {{ translate('Total Tax') }}:
                                                    {{ \App\CentralLogics\Helpers::format_currency($totalTaxAmount) }}
                                                </div>

                                                @foreach ($groupedByTaxOn as $taxOn => $taxGroup)
                                                    @if (isset($taxLabels[$taxOn]))
                                                        <div class="mt-2 text-capitalize fw-semibold">
                                                            {{ $taxLabels[$taxOn] }}:</div>

                                                        @php

                                                            $taxByName = $taxGroup
                                                                ->groupBy('tax_name')
                                                                ->map(function ($group) {
                                                                    return $group->sum('tax_amount');
                                                                });
                                                        @endphp

                                                        @foreach ($taxByName as $name => $amount)
                                                            <div class="d-flex fz-11 gap-3 align-items-center">
                                                                <span>{{ $name }}</span>
                                                                <span>{{ \App\CentralLogics\Helpers::format_currency($amount) }}</span>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            @else
                                                <div class="d-flex fz-14 gap-3 align-items-center title-clr">
                                                    {{ translate('Tax Amount:') }} <span>
                                                        {{ \App\CentralLogics\Helpers::format_currency($order->total_tax_amount) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <!-- End Table -->
                @if (count($orders) !== 0)
                    <hr>
                @endif
                <div class="page-area">
                    {!! $orders->links() !!}
                </div>
                @if (count($orders) === 0)
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
