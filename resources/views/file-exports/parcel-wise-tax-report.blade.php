<div class="row">
    <div class="col-lg-12 text-center ">
        <h1>{{ translate('Parcel_Tax_Reports') }}</h1>
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
                            {{ translate('total_orders') }} - {{ $data['summary']['total_orders'] ??0 }}
                            <br>
                            {{ translate('total_order_amount') }} - {{ $data['summary']['total_order_amount'] ??0 }}
                            <br>
                            {{ translate('total_tax') }} - {{ $data['summary']['total_tax'] ??0 }}
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

                    </th>
                    <th> </th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <th class="border-0">{{ translate('sl') }}</th>
                    <th class="border-0">{{ translate('Order ID') }}</th>
                    <th class="border-0">{{ translate('Total Order Amount') }}</th>
                    <th class="border-0">{{ translate('Tax Amount') }}</th>
            </thead>
            <tbody>
                @foreach ($data['orders'] as $key => $order)
                    <tr>
                        <td>
                            {{ $key +1 }}
                        </td>
                        <td>
                            {{ $order->id }}
                        </td>
                        <td>
                            {{ \App\CentralLogics\Helpers::format_currency($order->order_amount) }}
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <div class="d-flex fz-14 gap-3 align-items-center title-clr">
                                    {{ translate('Total:') }} <span>
                                        {{ \App\CentralLogics\Helpers::format_currency($order->total_tax_amount) }}</span>
                                </div>, <br>
                                @foreach ($order->orderTaxes as $tax)
                                    <div class="d-flex fz-11 gap-3 align-items-center">
                                        {{ $tax['tax_name'] }}:
                                        <span>{{ \App\CentralLogics\Helpers::format_currency($tax['tax_amount']) }}
                                        </span>
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
