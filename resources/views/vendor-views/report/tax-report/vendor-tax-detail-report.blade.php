@extends('layouts.vendor.app')

@section('title', translate('Vendor Tax Report'))

@section('vendor_tax_report')
    active
@endsection
@section('content')
    <div class="content container-fluid">


        <!--- Tax Report -->
        <h2 class="mb-20">{{ translate('Tax Report') }}</h3>
            <div class="card p-20 mb-20">
                <form action="" method="get">

                    <div class="row g-lg-4 g-3 align-items-end justify-content-between">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label">{{ translate('Date Range') }}</label>
                            <div class="position-relative">

                                <i class="tio-calendar-month icon-absolute-on-right"></i>
                                <input type="text" data-title="{{ translate('Select_Date_Range') }}" name="dates"
                                    value="{{ $dataRange ?? null }}" class="form-control">
                            </div>
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
                    <div class="col-md-6 col-xl-3">
                        <div class="bg-opacity-warning-5 h-100 rounded p-24">
                            <img src="{{ asset('/public/assets/admin/img/tax/1.png') }}" alt="img" class="mb-20">
                            <h2 class="cus-warning-clr mb-1">{{ $totalOrders }}</h2>
                            <span class="font-medium mb-0">{{ translate('Total Orders') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="bg-opacity-primary-5 h-100 rounded p-24">
                            <img src="{{ asset('/public/assets/admin/img/tax/2.png') }}" alt="img" class="mb-20">
                            <h2 class="theme-clr mb-1"> {{ \App\CentralLogics\Helpers::format_currency($totalOrderAmount) }}
                            </h2>
                            <span class="font-medium mb-0">{{ translate('Total Order Amount') }}</span>
                        </div>
                    </div>
                    <div class="col-lg-12 col-xl-6">
                        <div class="bg-opacity-warning-5 h-100 rounded p-24 d-flex flex-sm-nowrap flex-wrap gap-3">
                            <div class="w-xxl-100 w-sm-50">
                                <img src="{{ asset('/public/assets/admin/img/tax/3.png') }}" alt="img" class="mb-20">
                                <h2 class="text-success mb-1">{{ \App\CentralLogics\Helpers::format_currency($totalTax) }}
                                </h2>
                                <span class="font-medium mb-0">{{ translate('Total Tax Amount') }}</span>
                            </div>
                            <div class="tax-report-vat w-100">
                                <div class="d-flex flex-column gap-1">
                                    @foreach ($taxSummary as $taxdata)
                                        <div
                                            class="d-content-between gap-2 bg-white-n rounded py-2 px-2 fz-12px font-semibold">
                                            {{ $taxdata->tax_name }} <span
                                                class="title-clr">{{ \App\CentralLogics\Helpers::format_currency($taxdata->total_tax) }}</span>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card p-20">
                <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-20">
                    <h4 class="mb-0">{{ translate('All Taxes') }}

                    </h4>
                    <div class="search--button-wrapper justify-content-end">
                        <form class="search-form min--260">
                            <div class="input-group input--group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control h--40px"
                                    placeholder="{{ translate('messages.Ex:') }} 10010"
                                    value="{{ request()?->search ?? null }}"
                                    aria-label="{{ translate('messages.search') }}">

                                <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                            </div>
                        </form>

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
                                <a id="export-excel" class="dropdown-item"
                                    href="{{ route('vendor.report.vendorTaxExport', ['export_type' => 'excel', request()->getQueryString()]) }}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                        alt="Image Description">
                                    {{ translate('messages.excel') }}
                                </a>
                                <a id="export-csv" class="dropdown-item"
                                    href="{{ route('vendor.report.vendorTaxExport', ['export_type' => 'csv', request()->getQueryString()]) }}">
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
                                <th class="border-0">{{ translate('messages.Order_Id') }}</th>
                                <th class="border-0">{{ translate('messages.Order_Date') }}</th>
                                <th class="border-0">{{ translate('messages.Order_Amount') }}</th>
                                <th class="border-0">{{ translate('messages.Tax_Type') }}</th>
                                <th class="border-0">{{ translate('messages.Tax_Amount') }}</th>
                                <th class="border-0 text-center">{{ translate('messages.Action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($orders as $key => $order)
                                <tr>
                                    <td>
                                        {{ $key + $orders->firstItem() }}
                                    </td>
                                    <td>

                                        <a href="{{ route('vendor.order.details', ['id' => $order['id']]) }}">
                                            #{{ $order->id }}</a>

                                    </td>
                                    <td>
                                        {{ \App\CentralLogics\Helpers::date_format($order->created_at) }}
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
                                    <td class="text-center">

                                        <a class="btn btn-sm mx-auto btn--primary action-btn btn-outline-primary offcanvas-trigger"
                                            data-order_id="{{ $order['id'] }}"
                                            data-order_status="{{ translate($order['order_status']) }}"
                                            data-payment_status="{{ translate($order['payment_status']) }}"
                                            data-order_date="{{ \App\CentralLogics\Helpers::date_format($order['created_at']) }}"
                                            data-order_amount="{{ \App\CentralLogics\Helpers::format_currency($order['order_amount']) }}"
                                            data-order_tax_amount="{{ \App\CentralLogics\Helpers::format_currency($order['total_tax_amount']) }}"
                                            data-tax_type="{{ $order['tax_type'] }}"
                                            data-order_taxes='@json($order->orderTaxes)' href="#0"
                                            data-target="#offcanvas__customBtn__adminDetails">
                                            <i class="tio-invisible"></i>
                                        </a>
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
            </div>
    </div>


    <div id="offcanvas__customBtn__adminDetails" class="custom-offcanvas d-flex flex-column justify-content-between">
        <div>
            <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                <h3 class="mb-0">{{ translate('Details') }}</h2>
                    <button type="button"
                        class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"
                        aria-label="Close">&times;</button>
            </div>
            <div class="custom-offcanvas-body p-20">
                <div class="bg--secondary rounded p-20 mb-20 w-100">
                    <div class="mb-15">
                        <div class="d-flex align-items-center gap-3 mb-xl-3 mb-3">
                            <h4 class="mb-0" id="order_id"></h4> <span
                                class="bg-opacity-info-10 rounded py-2 px-3 font-semibold fz-12px theme-clr"
                                id="order_status"></span>
                        </div>
                        <span class="fz--14px title-clr d-block mb-1" id="order_date"></span>
                        <div class="d-flex align-items-center gap-3">
                            <span class="fz--14px title-clr">{{ translate('Payment Status') }}: </span> <span
                                class="bg-opacity-success-10 rounded py-2 px-3 font-semibold fz-12px text-success"
                                id="payment_status"></span>
                        </div>
                    </div>
                    <div
                        class="border d-flex align-items-center bg-white-n justify-content-between rounded p-12 mb-20 fz--14px">
                        {{ translate('Order Amount') }}
                        <span class="title-clr font-semibold" id="order_amount"></span>
                    </div>
                    <div class="bg-white-n rounded p-12">
                        <div id="order_taxes">
                        </div>

                        <div class="d-flex align-items-center fz--14px border-top pt-2 justify-content-between">
                            {{ translate('messages.Total_Tax_Amount') }}
                            <span class="title-clr font-semibold" id="order_tax_amount"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="offcanvasOverlay" class="offcanvas-overlay"></div>

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin') }}/js/offcanvas.js"></script>
    <script>
        "use strict";

        $(function() {
            $('input[name="dates"]').daterangepicker({
                startDate: moment('{{ $startDate }}'),
                endDate: moment('{{ $endDate }}'),
                maxDate: moment(),
                locale: {
                    format: 'MM/DD/YYYY'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [
                        moment().subtract(1, 'month').startOf('month'),
                        moment().subtract(1, 'month').endOf('month')
                    ]
                }
            });
        });


        document.querySelectorAll('[data-order_id]').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.dataset.order_id;
                const orderStatus = this.dataset.order_status;
                const paymentStatus = this.dataset.payment_status;
                const orderDate = this.dataset.order_date;
                const orderAmount = this.dataset.order_amount;
                const orderTaxAmount = this.dataset.order_tax_amount;
                const orderTaxes = JSON.parse(this.dataset.order_taxes || '[]');

                document.getElementById('order_id').textContent = `Order ID #${orderId}`;
                document.getElementById('payment_status').textContent = paymentStatus;
                document.getElementById('order_status').textContent = orderStatus;
                document.getElementById('order_date').textContent = `Date: ${orderDate}`;
                document.getElementById('order_amount').textContent = orderAmount;
                document.getElementById('order_tax_amount').textContent = orderTaxAmount;

                const taxContainer = document.getElementById('order_taxes');
                taxContainer.innerHTML = '';

                const taxType = this.dataset.tax_type;

                let basicLabel;
                switch (taxType) {
                    case 'category_wise':
                        basicLabel = 'Category Tax';
                        break;
                    case 'product_wise':
                        basicLabel = 'Product Tax';
                        break;
                    default:
                        basicLabel = 'Order Tax';
                }

                const taxLabels = {
                    basic: basicLabel,
                    tax_on_packaging_charge: 'Packaging Charge',
                };

                const grouped = {};

                orderTaxes.forEach(tax => {
                    if (!grouped[tax.tax_on]) grouped[tax.tax_on] = {};
                    if (!grouped[tax.tax_on][tax.tax_name]) grouped[tax.tax_on][tax.tax_name] = 0;
                    grouped[tax.tax_on][tax.tax_name] += parseFloat(tax.tax_amount);
                });

                Object.keys(grouped).forEach(taxOn => {
                    if (!taxLabels[taxOn]) return;

                    const sectionTitle = document.createElement('div');
                    sectionTitle.className = 'fw-semibold mt-2';
                    sectionTitle.textContent = taxLabels[taxOn];
                    taxContainer.appendChild(sectionTitle);

                    Object.entries(grouped[taxOn]).forEach(([name, amount]) => {
                        const taxRow = document.createElement('div');
                        taxRow.className =
                            'd-flex align-items-center fz-12px justify-content-between mb-2';
                        taxRow.innerHTML = `
                    ${name}
                    <span class="title-clr font-semibold">${amount.toFixed(2)}</span>
                `;
                        taxContainer.appendChild(taxRow);
                    });
                });

                document.getElementById('offcanvas__customBtn__adminDetails').classList.add('show');
            });
        });
    </script>
@endpush
