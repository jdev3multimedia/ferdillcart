@extends('layouts.admin.app')

@section('title', translate('Vendor Tax Report'))

@section('vendor_tax_report')
    active
@endsection
@section('content')
    <div class="content container-fluid">
        <!--- Vendor Tax Details Page -->
        <h2 class="mb-20 mt-5">{{ translate('Vendor Tax Report') }}</h3>
            <div class="bg--secondary rounded p-20">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-15">
                    <div>
                        <h5 class="mb-1">{{ $store->name }}</h3>
                            <p class="fz-12px mb-0">{{ translate('messages.Date') }}: {{ $startDate }} -
                                {{ $endDate }}</p>
                    </div>
                    <div class="hs-unfold mr-2 hungar-export">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-primary dropdown-toggle h--40px" href="javascript:;"
                            data-hs-unfold-options='{
                        "target": "#usersExportDropdown2", "type": "css-animation" }'>
                            <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                        </a>
                        <div id="usersExportDropdown2"
                            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                            <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                            <a id="export-excel" class="dropdown-item"
                                href="{{ route('admin.transactions.report.vendorTaxExport', ['export_type' => 'excel', 'id' => $store->store_id, request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item"
                                href="{{ route('admin.transactions.report.vendorTaxExport', ['export_type' => 'csv', 'id' => $store->store_id, request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                    alt="Image Description">
                                {{ translate('messages.csv') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div
                    class="text-capitalize d-flex align-items-center gap-3 justify-content-between flex-md-nowrap flex-wrap">
                    <div class="bg-white p-12 w-100 rounded d-flex align-items-center justify-content-between">
                        {{ translate('messages.total_order_amount') }} <span
                            class="title-clr">{{ \App\CentralLogics\Helpers::format_currency($totalOrderAmount) }}</span>
                    </div>
                    <div
                        class="text-capitalize bg-white p-12 w-100 rounded d-flex align-items-center justify-content-between">
                        {{ translate('messages.total_tax_amount') }} <span class="title-clr">
                            {{ \App\CentralLogics\Helpers::format_currency($totalTax) }}</span>
                    </div>
                </div>
            </div>
            <div class="card p-20 mt-5">
                <div class="table-responsive datatable-custom">
                    <table id="datatable"
                        class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table fz--14px">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{ translate('sl') }}</th>
                                <th class="border-0">{{ translate('messages.order_id') }}</th>
                                <th class="border-0">{{ translate('messages.order_amount') }}</th>
                                <th class="border-0">{{ translate('messages.tax_type') }}</th>
                                <th class="border-0">{{ translate('messages.tax_amount') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($orders as $key => $order)
                                <tr>
                                    <td>
                                        {{ $key + $orders->firstItem() }}
                                    </td>
                                    <td>

                                        <a
                                            href="{{ route($order->order_type == 'parcel' ? 'admin.parcel.order.details' : 'admin.order.details', ['id' => $order['id']]) }}">
                                            #{{ $order->id }}</a>

                                    </td>
                                    <td>
                                        {{ \App\CentralLogics\Helpers::format_currency($order->order_amount) }}
                                    </td>
                                    <td>

                                        {{ translate($order?->tax_type ?? 'order_wise') }}
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
                <!-- End Table -->
            </div>
    </div>



@endsection

@push('script_2')
@endpush
