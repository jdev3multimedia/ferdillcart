@extends('layouts.admin.app')

@section('title', translate('Admin Tax Report'))

@section('tax_report')
    active
@endsection

@section('content')
    <div class="content container-fluid">


        <!--- Admin Tax Report -->
        <h2 class="mb-20">{{ translate('messages.Admin Tax Report') }}</h3>
            <!--- Tax Details Page -->
            <h2 class="mb-20 mt-5">{{ translate('messages.Tax Details') }}</h2>
            <div class="bg--secondary rounded p-20">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-15">
                    <div>
                        <h5 class="mb-1">{{ translate($taxSource) }} {{ translate('Taxes') }}</h5>
                        <p class="fz-12px mb-0">{{ translate('Date:') }} {{ $startDate }} - {{ $endDate }}</p>
                    </div>
                    <div class="hs-unfold mr-2 hungar-export">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-primary dropdown-toggle h--40px" href="javascript:;"
                            data-hs-unfold-options='{
                        "target": "#usersExportDropdown4", "type": "css-animation" }'>
                            <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                        </a>
                        <div id="usersExportDropdown4"
                            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                            <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                            <a id="export-excel" class="dropdown-item" href="{{ route('admin.transactions.report.getTaxDetailsExport',['source'=> $taxSource ,'export_type' => 'excel', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="{{ route('admin.transactions.report.getTaxDetailsExport',['source'=> $taxSource ,'export_type' => 'excel', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                    alt="Image Description">
                                .{{ translate('messages.csv') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="bg-white p-12 w-100 rounded d-flex align-items-center justify-content-between">
                            {{ translate('Total_Transactions') }} <h4 class="theme-clr fw-bold mb-0">{{ $total_count }}
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-white p-12 w-100 rounded d-flex align-items-center justify-content-between">
                            {{ translate('Total_Amount') }} <h4 class="theme-clr fw-bold mb-0">
                                {{ \App\CentralLogics\Helpers::format_currency($total_amount) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-white p-12 w-100 rounded d-flex align-items-center justify-content-between">
                            {{ translate('Total Tax Percentage') }} <h4 class="cus-warning-light-clr fw-bold mb-0">
                                {{ $total_tax_rate }} %</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-white p-12 w-100 rounded d-flex align-items-center justify-content-between">
                            {{ translate('Total Tax Amount') }} <h4 class="cus-warning-clr fw-bold mb-0">
                                {{ \App\CentralLogics\Helpers::format_currency($total_tax_amount) }}</h4>
                        </div>
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
                                <th class="border-0">{{ translate('Transaction ID') }}</th>
                                <th class="border-0">{{ translate('Amount') }}</th>
                                <th class="border-0">{{ translate('Tax Amount') }}</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($taxData as $key => $item)
                                <tr>
                                    <td>
                                        {{ $key + $taxData->firstItem() }}
                                    </td>
                                    <td>
                                        {{ $item->id }}
                                    </td>
                                    <td>
                                        {{ \App\CentralLogics\Helpers::format_currency($item->paid_amount) }}
                                    </td>

                                    <td>
                                        @php
                                            $taxSummary = collect($item['calculated_taxes']);
                                            $totalTaxRate = $taxSummary->sum('tax_rate');
                                            $totalTaxAmount = $taxSummary->sum('tax_amount');
                                        @endphp

                                        <div class="d-flex flex-column gap-1">
                                            <div class="d-flex fz-14 gap-3 align-items-center title-clr">
                                                {{ translate('Total') }} ({{ $totalTaxRate }}%):
                                                <span>
                                                    {{ \App\CentralLogics\Helpers::format_currency($totalTaxAmount) }}</span>
                                            </div>

                                            @foreach ($item['calculated_taxes'] as $taxItems)
                                                <div class="d-flex fz-11 gap-3 align-items-center">
                                                    {{ $taxItems['tax_name'] }} ({{ $taxItems['tax_rate'] }}%) :
                                                    <span>{{ \App\CentralLogics\Helpers::format_currency($taxItems['tax_amount']) }}</span>
                                                </div>
                                            @endforeach

                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                @if (count($taxData) !== 0)
                    <hr>
                @endif
                <div class="page-area">
                    {!! $taxData->withQueryString()->links() !!}
                </div>
                @if (count($taxData) === 0)
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
