<div class="row">
    <div class="col-lg-12 text-center ">
        <h1> {{ translate('Admin_Tax_Report') }}</h1>
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
                    <th class="border-0">{{ translate('messages.sl') }}</th>
                    <th class="border-0">{{ translate('Income Source') }}</th>
                    <th class="border-0">{{ translate('Total Income') }}</th>
                    <th class="border-0">{{ translate('Total Tax') }}</th>
            </thead>
            <tbody>
                @php
                    $count = 1;
                @endphp
                @foreach ($data['taxData'] as $key => $item)
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
                                </div>,<br>

                                @foreach ($item['taxes'] as $taxName => $taxItems)
                                    @foreach ($taxItems as $tax)
                                        <div class="d-flex fz-11 gap-3 align-items-center">
                                            {{ $taxName }} ({{ $tax['tax_rate'] }}%) :
                                            <span>{{ \App\CentralLogics\Helpers::format_currency($tax['total_tax_amount']) }}</span>
                                        </div>,<br>
                                    @endforeach
                                @endforeach

                            </div>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
