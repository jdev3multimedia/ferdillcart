<div class="row">
    <div class="col-lg-12 text-center ">
        <h1>{{ translate($data['taxSource']) }} {{ translate('Tax_Details_Report') }}</h1>
    </div>
    <div class="col-lg-12">



        <table>
            <thead>
                <tr>
                    <th>{{ translate('Search_Criteria') }}</th>
                    <th></th>
                    <th></th>
                    <th>

                        <br>
                        {{ translate('total_tax_amount') }} - {{\App\CentralLogics\Helpers::format_currency($data['total_tax_amount']) ?? 0 }}
                        <br>
                        {{ translate('total_amount') }} - {{ \App\CentralLogics\Helpers::format_currency($data['total_amount']) }}

                        @if ($data['from'])
                            <br>
                            {{ translate('from') }} -
                            {{ $data['from'] ? Carbon\Carbon::parse($data['from'])->format('d M Y') : '' }}
                        @endif
                        @if ($data['to'])
                            <br>
                            {{ translate('to') }} -
                            {{ $data['to'] ? Carbon\Carbon::parse($data['to'])->format('d M Y') : '' }}
                        @endif
                        <br>

                        {{-- {{ translate('Search_Bar_Content') }}- {{ $data['search'] ?? translate('N/A') }} --}}
                        <br>

                    </th>
                    <th> </th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    @php
                        $taxSource = $data['taxSource'];
                        if ($taxSource == 'admin_commission') {
                            $col = 'Commission';
                        } elseif ($taxSource == 'delivery_commission') {
                            $col = 'Delivery_Commission';
                        } else {
                            $col = 'Additional_Charge';
                        }

                    @endphp
                    <th class="border-0">{{ translate('sl') }}</th>
                    <th class="border-0">{{ translate('Order') }}</th>
                    <th class="border-0">{{ translate($col) }}</th>
                    <th class="border-0">{{ translate('Tax Amount') }}</th>
            </thead>
            <tbody>
                @foreach ($data['taxData'] as $key => $item)
                    <tr>
                        <td>
                            {{ $key + 1 }}
                        </td>
                        <td>
                            <h6>{{ \App\CentralLogics\Helpers::format_currency($item->order_amount) }}</h6><br>
                            <small>#{{ $item->order_id }} </small>
                        </td>
                        <td>
                            @if ($taxSource == 'admin_commission')
                                {{ \App\CentralLogics\Helpers::format_currency($item->admin_commission + $item->admin_expense - $item->delivery_fee_comission - $item->additional_charge) }}
                            @elseif ($taxSource == 'delivery_commission')
                                {{ \App\CentralLogics\Helpers::format_currency($item->delivery_fee_comission) }}
                            @else
                                {{ \App\CentralLogics\Helpers::format_currency($item->additional_charge) }}
                            @endif
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
                                </div>,
                                <br>
                                @foreach ($item['calculated_taxes'] as $taxItems)
                                    <div class="d-flex fz-11 gap-3 align-items-center">
                                        {{ $taxItems['tax_name'] }} ({{ $taxItems['tax_rate'] }}%) :
                                        <span>{{ \App\CentralLogics\Helpers::format_currency($taxItems['tax_amount']) }}</span>
                                    </div>, <br>
                                @endforeach

                            </div>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
