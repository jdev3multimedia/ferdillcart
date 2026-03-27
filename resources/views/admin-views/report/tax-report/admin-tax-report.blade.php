@extends('layouts.admin.app')

@section('title', translate('Admin Tax Report'))

@section('tax_report')
    active
@endsection

@section('content')
    <div class="content container-fluid">
        <!--- Admin Tax Report -->
        <h2 class="mb-20">{{ translate('messages.Generate Tax Report') }}</h3>
            <div class="card p-20 mb-20">
                <div class="mb-20">
                    <h3 class="mb-1">{{ translate('messages.Admin Tax Report') }}</h3>

                    <p class="mb-1 fz-12">
                        {{ translate('To generate you tax report please select & input following field and submit for the result') }}.
                    </p>
                @if (addon_published_status('Rental'))
                <div id="" class="info-notes-bg px-2 py-2 rounded fz-11  gap-2 align-items-center d-flex ">
                <svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_13899_104013)">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M10.3125 2.53979V1.28979C10.3125 1.11729 10.1725 0.977295 10 0.977295C9.8275 0.977295 9.6875 1.11729 9.6875 1.28979V2.53979C9.6875 2.71229 9.8275 2.85229 10 2.85229C10.1725 2.85229 10.3125 2.71229 10.3125 2.53979Z"
                            fill="#245BD1" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M5.34578 4.31882L4.47078 3.44382C4.34891 3.32195 4.15078 3.32195 4.02891 3.44382C3.90703 3.5657 3.90703 3.76382 4.02891 3.8857L4.90391 4.7607C5.02578 4.88257 5.22391 4.88257 5.34578 4.7607C5.46766 4.63882 5.46766 4.4407 5.34578 4.31882Z"
                            fill="#245BD1" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M3.125 9.10229H1.875C1.7025 9.10229 1.5625 9.24229 1.5625 9.41479C1.5625 9.58729 1.7025 9.72729 1.875 9.72729H3.125C3.2975 9.72729 3.4375 9.58729 3.4375 9.41479C3.4375 9.24229 3.2975 9.10229 3.125 9.10229Z"
                            fill="#245BD1" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M4.90391 14.0688L4.02891 14.9438C3.90703 15.0657 3.90703 15.2638 4.02891 15.3857C4.15078 15.5076 4.34891 15.5076 4.47078 15.3857L5.34578 14.5107C5.46766 14.3888 5.46766 14.1907 5.34578 14.0688C5.22391 13.9469 5.02578 13.9469 4.90391 14.0688Z"
                            fill="#245BD1" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M14.6539 14.5107L15.5289 15.3857C15.6508 15.5076 15.8489 15.5076 15.9708 15.3857C16.0927 15.2638 16.0927 15.0657 15.9708 14.9438L15.0958 14.0688C14.9739 13.9469 14.7758 13.9469 14.6539 14.0688C14.532 14.1907 14.532 14.3888 14.6539 14.5107Z"
                            fill="#245BD1" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M16.875 9.72729H18.125C18.2975 9.72729 18.4375 9.58729 18.4375 9.41479C18.4375 9.24229 18.2975 9.10229 18.125 9.10229H16.875C16.7025 9.10229 16.5625 9.24229 16.5625 9.41479C16.5625 9.58729 16.7025 9.72729 16.875 9.72729Z"
                            fill="#245BD1" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M15.0958 4.7607L15.9708 3.8857C16.0927 3.76382 16.0927 3.5657 15.9708 3.44382C15.8489 3.32195 15.6508 3.32195 15.5289 3.44382L14.6539 4.31882C14.532 4.4407 14.532 4.63882 14.6539 4.7607C14.7758 4.88257 14.9739 4.88257 15.0958 4.7607Z"
                            fill="#245BD1" />
                        <path
                            d="M7.5 16.6023V15.6648C7.5 14.9773 7.1875 14.321 6.625 13.9148C5.25 12.8835 4.375 11.2585 4.375 9.41477C4.375 6.10227 7.25 3.44602 10.625 3.82102C13.2188 4.10227 15.2812 6.16477 15.5938 8.75852C15.8438 10.8835 14.9062 12.7898 13.375 13.9148C12.8125 14.321 12.5 14.9773 12.5 15.6648V16.6023H7.5Z"
                            fill="#BED2FE" />
                        <path
                            d="M7.5 16.2898H12.5V18.2273C12.5 18.5398 12.25 18.7898 11.9375 18.7898H11.25C11.25 19.4773 10.6875 20.0398 10 20.0398C9.3125 20.0398 8.75 19.4773 8.75 18.7898H8.0625C7.75 18.7898 7.5 18.5398 7.5 18.2273V16.2898Z"
                            fill="#245BD1" />
                    </g>
                    <defs>
                        <clipPath id="clip0_13899_104013">
                            <rect width="20" height="20" fill="white" transform="translate(0 0.664795)" />
                        </clipPath>
                    </defs>
                </svg>

                <span id="info_for_item">
                    {{ translate('You will get combine tax report in combine. You can view') }}
                    <a href="{{  route('admin.transactions.rental.report.getTaxReport') }}"
                        class="font-semibold theme-clr text-decoration-underline">{{ translate('Tax Report for Rental Module') }}.</a>
                    {{ translate('Separately from here.') }}
                </span>
                </div>

                @endif
                </div>
                <div class="bg--secondary rounded p-20 mb-20">
                    <form action="" method="get">
                        <div class="row g-lg-4 g-md-3 g-2">
                            <div class="col-md-6">
                                <div class="d-flex flex-column gap-lg-4 gap-3">
                                    <div>
                                        <span
                                            class="mb-2 d-block title-clr fw-normal">{{ translate('messages.Date Range Type') }}</span>
                                        <select name="date_range_type" id="date_range_type"
                                            class="custom-select custom-select-color border rounded w-100">
                                            <option value="">{{ translate('Select Date Range') }}</option>
                                            <option value="this_fiscal_year"
                                                {{ $date_range_type == 'this_fiscal_year' ? 'selected' : '' }}>
                                                {{ translate('This Fiscal Year') }}
                                            </option>
                                            <option value="custom" {{ $date_range_type == 'custom' ? 'selected' : '' }}>
                                                {{ translate('Custom') }}
                                            </option>

                                        </select>
                                    </div>
                                    <div class="{{ $date_range_type == 'custom' ? '' : 'd-none' }}" id="date_range">
                                        <label class="form-label">{{ translate('Date Range') }}</label>
                                        <div class="position-relative">
                                            <i class="tio-calendar-month icon-absolute-on-right"></i>
                                            <input type="text" class="form-control h-45 position-relative bg-transparent"
                                                name="dates" placeholder="{{ translate('messages.Select_Date') }}">
                                        </div>
                                    </div>

                                    <div>
                                        <span
                                            class="mb-2 d-block title-clr fw-normal">{{ translate('Select How to calculate tax') }}</span>
                                        <select name="calculate_tax_on" id="calculate_tax_on" required
                                            class="custom-select custom-select-color border rounded w-100">
                                            <option disabled selected value="">
                                                {{ translate('Select Calculate Tax') }}</option>
                                            <option {{ $calculate_tax_on == 'all_source' ? 'selected' : '' }}
                                                value="all_source">
                                                {{ translate('messages.Same Tax for All Income Source') }}
                                            </option>
                                            <option {{ $calculate_tax_on == 'individual_source' ? 'selected' : '' }}
                                                value="individual_source">
                                                {{ translate('Different Tax for Different Income Source') }}
                                            </option>

                                        </select>
                                    </div>
                                    <div class="{{ $calculate_tax_on == 'individual_source' ? '' : 'd-none' }}"
                                        id="calculate_commission_tax">
                                        <span
                                            class="mb-2 d-block title-clr fw-normal">{{ translate('Tax on Order Commission') }}</span>
                                        <div class="select-class-closest">
                                            <select name="tax_on_order_commission[]" id="select_customer_fiscal1"
                                                class="form-control js-select2-custom" multiple="multiple"
                                                placeholder="Type & Select Tax Rate">

                                            </select>
                                        </div>
                                    </div>
                                    <div class="{{ $calculate_tax_on == 'individual_source' ? '' : 'd-none' }}"
                                        id="calculate_delivery_charge_tax">
                                        <span
                                            class="mb-2 d-block title-clr fw-normal">{{ translate('Tax on Delivery Charge Commission') }}</span>
                                        <div class="select-class-closest">
                                            <select name="tax_on_delivery_charge_commission[]" id="select_customer_fiscal2"
                                                class="form-control js-select2-custom" multiple="multiple"
                                                placeholder="Type & Select Tax Rate">

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex flex-column gap-lg-4 gap-3">
                                    <div class="{{ $calculate_tax_on == 'individual_source' ? '' : 'd-none' }}"
                                        id="calculate_service_charge_tax">
                                        <span
                                            class="mb-2 d-block title-clr fw-normal">{{ translate('Tax on Service charge') }}</span>
                                        <div class="select-class-closest">
                                            <select name="tax_on_service_charge[]" id="select_customer_fiscal-3"
                                                class="form-control js-select2-custom" multiple="multiple"
                                                placeholder="Type & Select Tax Rate">

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-column gap-lg-4 gap-3 mt-3">
                                    <div class="{{ $calculate_tax_on == 'individual_source' ? '' : 'd-none' }}"
                                        id="calculate_subscription_tax">
                                        <span
                                            class="mb-2 d-block title-clr fw-normal">{{ translate('Tax on Subscription') }}</span>
                                        <div class="select-class-closest">
                                            <select name="tax_on_subscription[]" id="select_customer_fiscal-6"
                                                class="form-control js-select2-custom" multiple="multiple"
                                                placeholder="Type & Select Tax Rate">

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-lg-4 gap-3">
                                    <div class="{{ $calculate_tax_on == 'individual_source' ? 'd-none' : '' }}"
                                        id="calculate_tax_rate">
                                        <span
                                            class="mb-2 d-block title-clr fw-normal">{{ translate('Select Tax Rates') }}</span>
                                        <div class="select-class-closest">
                                            <select {{ $calculate_tax_on == 'individual_source' ? '' : 'required' }}
                                                name="tax_rate[]" id="select_customer_fiscal-5"
                                                class="form-control js-select2-custom" multiple="multiple"
                                                placeholder="Type & Select Tax Rate">

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="d-flex align-items-center justify-content-end gap-2">
                    <button type="reset" id="reset_button_id"
                        class="btn bg--secondary h--42px title-clr px-4">{{ translate('Reset') }}</button>
                    <button type="submit" class="btn btn--primary">{{ translate('Submit') }}</button>
                </div>
                </form>
            </div>
            <div class="card p-20 mb-20">
                <div class="row g-lg-4 g-3">
                    <div class="col-md-6">
                        <div class="bg-opacity-primary-10 rounded p-20 d-flex align-items-center gap-2 flex-wrap">
                            <div class="d-flex align-items-center gap-3 title-clr">
                                <img src="{{ asset('/public/assets/admin/img/t-toal-amount.png') }}" alt="img">
                                {{ translate('Total Income') }}
                            </div>
                            <h3 class="theme-clr fw-bold mb-0">
                                {{ \App\CentralLogics\Helpers::format_currency($totalBase) }} </h3>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-opacity-warning-10 rounded p-20 d-flex align-items-center gap-2 flex-wrap">
                            <div class="d-flex align-items-center gap-3 title-clr">
                                <img src="{{ asset('/public/assets/admin/img/t-tax-amount.png') }}" alt="img">
                                {{ translate('Total Tax') }}
                            </div>
                            <h3 class="text-danger fw-bold mb-0">
                                {{ \App\CentralLogics\Helpers::format_currency($totalTax) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <!--- Vendor Tax Report Here -->
            <div class="card p-20 mb-20">
                <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-20">
                    <h4 class="mb-0">{{ translate('Tax Report List') }}</h4>
                    <div class="search--button-wrapper justify-content-end">


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
                            <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle h--40px"
                                href="javascript:;"
                                data-hs-unfold-options='{
                            "target": "#usersExportDropdown__admin", "type": "css-animation" }'>
                                <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                            </a>
                            <div id="usersExportDropdown__admin"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                                <a id="export-excel" class="dropdown-item"
                                    href="{{ route('admin.transactions.report.adminTaxReportExport', ['export_type' => 'excel', request()->getQueryString()]) }}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                        alt="Image Description">
                                    {{ translate('messages.excel') }}
                                </a>
                                <a id="export-csv" class="dropdown-item"
                                    href="{{ route('admin.transactions.report.adminTaxReportExport', ['export_type' => 'csv', request()->getQueryString()]) }}">
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
                                <th class="border-0">{{ translate('messages.sl') }}</th>
                                <th class="border-0">{{ translate('Income Source') }}</th>
                                <th class="border-0">{{ translate('Total Income') }}</th>
                                <th class="border-0">{{ translate('Total Tax') }}</th>
                                <th class="border-0 text-center">{{ translate('Action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $count = 1;
                            @endphp
                            @forelse ($combinedResults as $key => $item)
                                <tr>
                                    <td>
                                        {{ $count++ }}

                                    </td>
                                    <td>
                                        {{ translate($key) }}
                                    </td>
                                    <td>

                                        {{ \App\CentralLogics\Helpers::format_currency($item['total_base_amount']) }}
                                    </td>
                                    <td>
                                        @php
                                            $totalTaxAmount = collect($item['taxes'] ?? [])
                                                ->flatten(1)
                                                ->sum('total_tax_amount');
                                            $totalTax = collect($item['taxes'] ?? [])
                                                ->flatten(1)
                                                ->sum('tax_rate');
                                        @endphp
                                        <div class="d-flex flex-column gap-1">
                                            <div class="d-flex fz-14 gap-3 align-items-center title-clr">
                                                {{ translate('Total') }} ({{ $totalTax }}%): <span>
                                                    {{ \App\CentralLogics\Helpers::format_currency($totalTaxAmount) }}</span>
                                            </div>

                                            @foreach ($item['taxes'] as $taxName => $taxItems)
                                                @foreach ($taxItems as $tax)
                                                    <div class="d-flex fz-11 gap-3 align-items-center">
                                                        {{ $taxName }} ({{ $tax['tax_rate'] }}%) :
                                                        <span>{{ \App\CentralLogics\Helpers::format_currency($tax['total_tax_amount']) }}</span>
                                                    </div>
                                                @endforeach
                                            @endforeach

                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a class="btn btn-sm theme-border action-btn theme-hover theme-clr"
                                                target="_blank"
                                                href="{{ route('admin.transactions.report.getTaxDetails', ['source' => $key, request()->getQueryString()]) }}">
                                                <i class="tio-invisible"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-5">
                                        <div class="text-center max-w-700 mx-auto py-5">
                                            <img src="{{ asset('/public/assets/admin/img/tax-error.png') }}"
                                                alt="img" class="mb-20">
                                            <h4 class="mb-2">{{ translate('No Tax Report Generated') }}</h4>
                                            <p class="mb-0 fz-12px">
                                                {{ translate('To generate your tax report please select & input above field and submit for the result') }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
    </div>



@endsection

@push('script_2')
    <script>
        "use strict";

        $(document).on('ready', function() {
            function updateUI() {
                if ($('#date_range_type').val() == 'custom') {
                    $('#date_range').removeClass('d-none');
                } else {
                    $('#date_range').addClass('d-none');
                }

                if ($('#calculate_tax_on').val() == 'individual_source') {
                    $('#calculate_commission_tax').removeClass('d-none');
                    $('#calculate_delivery_charge_tax').removeClass('d-none');
                    $('#calculate_service_charge_tax').removeClass('d-none');
                    // $('#calculate_packaging_charge_tax').removeClass('d-none');
                    $('#calculate_subscription_tax').removeClass('d-none');
                    $('#calculate_tax_rate').addClass('d-none').find('select').attr('required', false);
                } else {
                    $('#calculate_tax_rate').removeClass('d-none').find('select').attr('required', true);
                    $('#calculate_commission_tax').addClass('d-none');
                    $('#calculate_delivery_charge_tax').addClass('d-none');
                    $('#calculate_service_charge_tax').addClass('d-none');
                    // $('#calculate_packaging_charge_tax').addClass('d-none');
                    $('#calculate_subscription_tax').addClass('d-none');
                }
            }
            updateUI();
            $('#date_range_type').on('change', updateUI);
            $('#calculate_tax_on').on('change', updateUI);
            $('#reset_button_id').on('click', function() {
                $('.js-select2-custom').val(null).trigger('change');
                setTimeout(() => {
                    updateUI();
                }, 1);
            });
        });


        $(function() {
            $('input[name="dates"]').daterangepicker({
                startDate: moment('{{ $startDate }}'),
                endDate: moment('{{ $endDate }}'),
                maxDate: moment(),
                locale: {
                    format: 'MM/DD/YYYY'
                }
            });
        });


        $(document).on('ready', function() {
            const selectedTax = @json($selectedTax);
            Object.entries(selectedTax).forEach(([key, taxArray]) => {
                const $select = $(`select[name="${key}[]"]`);
                if (!$select.length || !Array.isArray(taxArray)) return;
                taxArray.forEach(tax => {
                    if (!tax.id || !tax.name) return;

                    const displayText = `${tax.name} (${tax.tax_rate}%)`;
                    const option = new Option(displayText, tax.id, true, true);
                    $select.append(option);
                });
                $select.trigger('change');
                setTimeout(function() {
                    $select.select2({
                        placeholder: "Select Tax Rate",
                        dropdownParent: $select.closest('.select-class-closest'),
                        ajax: {
                            url: '{{ route('admin.transactions.report.getTaxList') }}',
                            data: function(params) {
                                return {
                                    q: params.term,
                                    page: params.page
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: data
                                };
                            }
                        }
                    });
                }, 5);
            });
        });
    </script>
@endpush
