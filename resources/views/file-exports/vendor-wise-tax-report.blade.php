<div class="row">
    <div class="col-lg-12 text-center ">
        <h1>{{ translate('Vendor_Vat_Reports') }}</h1>
    </div>
    <div class="col-lg-12">



        <table>
            <thead>
                <tr>
                    <th>{{ translate('Search_Criteria') }}</th>
                    <th></th>
                    <th></th>
                    <th>

                        @if (isset($data['summary']))
                            <br>
                            {{ translate('total_orders') }} - {{  \App\CentralLogics\Helpers::format_currency($data['summary']->total_orders ??0) }}
                            <br>
                            {{ translate('total_order_amount') }} - {{  \App\CentralLogics\Helpers::format_currency($data['summary']->total_order_amount ??0) }}
                            <br>
                            {{ translate('total_tax') }} - {{  \App\CentralLogics\Helpers::format_currency($data['summary']->total_tax ??0) }}
                        @endif
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

                        {{ translate('Search_Bar_Content') }}- {{ $data['search'] ?? translate('N/A') }}
                        <br>

                    </th>
                    <th> </th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <th class="border-0">{{ translate('sl') }}</th>
                    <th class="border-0">{{ translate('Vendor Info') }}</th>
                    <th class="border-0">{{ translate('Total Order') }}</th>
                    <th class="border-0">{{ translate('Total Order Amount') }}</th>
                    <th class="border-0">{{ translate('Tax Amount') }}</th>
            </thead>
            <tbody>
                @foreach ($data['stores'] as $key => $store)
                    <tr>
                        <td>
                            {{ $key +1 }}
                        </td>
                        <td>
                            <span class="fz-14 title-clr">
                                {{ $store->store_name }}
                                <span class="fz-11 d-block">{{ $store->store_phone }}</span>
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
                                            </div> <br>
                                            @endif
                                            @if ($sum_tax_amount > 0 )
                                            <div class="d-flex fz-14 gap-3 align-items-center title-clr">
                                                {{ translate('Sum of Taxes:') }} <span>
                                                    {{ \App\CentralLogics\Helpers::format_currency($sum_tax_amount) }}</span>
                                            </div><br>
                                            @foreach ($store->tax_data as $tax)
                                                <div class="d-flex fz-11 gap-3 align-items-center">
                                                    {{ $tax['tax_name'] }}:
                                                    <span>{{ \App\CentralLogics\Helpers::format_currency($tax['total_tax_amount']) }}
                                                    </span>
                                                </div> <br>
                                            @endforeach

                                            @endif
                                        </div>
                                    </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
