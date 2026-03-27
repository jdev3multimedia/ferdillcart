<div class="row">
    <div class="col-lg-12 text-center "><h1 >{{ translate('messages.order_report') }}</h1></div>
    <div class="col-lg-12">



    <table>
        <thead>
            <tr>
                <th>{{ translate('filter_criteria') }} -</th>
                <th></th>
                <th></th>
                <th>
                    {{ translate('module' )}} - {{ $data['module']?translate($data['module']):translate('all') }}
                    <br>
                    {{ translate('zone' )}} - {{ $data['zone']??translate('all') }}
                    <br>
                    {{ translate('store' )}} - {{ $data['store']??translate('all') }}
                    <br>
                    {{ translate('customer' )}} - {{ $data['customer']??translate('all') }}
                    @if ($data['from'])
                    <br>
                    {{ translate('from' )}} - {{ $data['from']?Carbon\Carbon::parse($data['from'])->format('d M Y'):'' }}
                    @endif
                    @if ($data['to'])
                    <br>
                    {{ translate('to' )}} - {{ $data['to']?Carbon\Carbon::parse($data['to'])->format('d M Y'):'' }}
                    @endif
                    <br>
                    {{ translate('filter')  }}- {{  translate($data['filter']) }}
                    <br>
                    {{ translate('Search_Bar_Content')  }}- {{ $data['search'] ??translate('N/A') }}

                </th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <th>{{ translate('messages.sl') }}</th>
                <th>{{ translate('messages.order_id') }}</th>
                <th>{{ translate('messages.customer_name') }}</th>
                <th>{{ translate('messages.store_name') }}</th>
                <th>{{ translate('messages.item_price') }}</th>
                <th>{{ translate('messages.item_discount') }}</th>
                <th>{{ translate('messages.coupon_discount') }}</th>
                <th>{{ translate('messages.referral_discount') }}</th>
                <th>{{ translate('messages.discounted_amount') }}</th>
                <th>{{  \App\CentralLogics\Helpers::get_business_data('additional_charge_name')??translate('messages.additional_charge')  }}</th>
                <th>{{ translate('messages.extra_packaging_amount') }}</th>
                <th>{{ translate('messages.tax') }}</th>
                <th>{{ translate('messages.total_amount') }}</th>
                <th>{{ translate('messages.payment_status') }}</th>
                <th>{{ translate('messages.order_type') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach($data['orders'] as $key => $order)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $order->id }}</td>
                <td>
                    @if ($order->customer)
                        {{ $order->customer['f_name'] . ' ' . $order->customer['l_name'] }}
                    @else
                        {{ translate('not_found') }}
                    @endif
                </td>
                <td>
                    @if($order->store)
                        {{$order->store->name}}
                    @else
                        {{ translate('messages.not_found') }}
                    @endif
                </td>
                <td>{{ \App\CentralLogics\Helpers::number_format_short($order['order_amount'] - $order->additional_charge -$order['dm_tips']-$order['total_tax_amount']-$order['delivery_charge']+$order['coupon_discount_amount'] + $order['store_discount_amount'] + $order['ref_bonus_amount'] - $order['extra_packaging_amount'] +$order['flash_admin_discount_amount'] +$order['flash_store_discount_amount'] ) }}</td>
                @if ($order->discount_type == 'flash_sale')
                <td>{{ \App\CentralLogics\Helpers::number_format_short($order['flash_admin_discount_amount'] +$order['flash_store_discount_amount'] ) }}</td>
                @else
                <td>{{ \App\CentralLogics\Helpers::number_format_short($order->details()->sum(DB::raw('discount_on_item * quantity')) ) }}</td>

                @endif
                <td>{{ \App\CentralLogics\Helpers::number_format_short($order['coupon_discount_amount']) }}</td>
                <td>{{ \App\CentralLogics\Helpers::number_format_short($order['ref_bonus_amount']) }}</td>
                <td>{{ \App\CentralLogics\Helpers::number_format_short($order['coupon_discount_amount'] + $order['store_discount_amount'] + $order['ref_bonus_amount'] ) }}</td>
                <td>{{ \App\CentralLogics\Helpers::number_format_short($order['additional_charge']) }}</td>
                <td>{{ \App\CentralLogics\Helpers::number_format_short($order['extra_packaging_amount']) }}</td>
                <td>{{ \App\CentralLogics\Helpers::number_format_short($order['total_tax_amount']) }}</td>
                <td>{{ \App\CentralLogics\Helpers::number_format_short($order['order_amount']) }}</td>
                <td>{{ translate($order->payment_status) }}</td>
                <td>{{ translate($order->order_type) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
