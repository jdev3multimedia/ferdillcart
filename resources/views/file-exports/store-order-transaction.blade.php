@php
    $tripOrOrder = $data['is_provider'] ? 'trip' : 'order';
    $storeOrProvider = $data['is_provider'] ? 'provider' : 'store';
@endphp
<div class="row">
    <div class="col-lg-12 text-center "><h1 > {{translate($data['is_provider'] ? 'Provider_Trip_Transactions' : 'Store_Order_Transactions')}}
    </h1></div>
    <div class="col-lg-12">

    <table>
        <thead>
            <tr>
                <th>{{ translate('Filter_Criteria') }}</th>
                <th></th>
                <th>
                    {{ translate('Search_Bar_Content')  }}: {{ $data['search'] ?? translate('N/A') }}
                </th>
                <th> </th>
                </tr>


        <tr>
            <th>{{ translate('sl') }}</th>
            <th>{{ translate($tripOrOrder.'_ID') }}</th>
            <th>{{ translate($tripOrOrder.'_Time') }}</th>
            <th>{{ translate('Total_'.$tripOrOrder.'_amount') }}</th>
            <th>{{ translate($storeOrProvider.'_Earnings') }}</th>
            <th>{{ translate('Admin_Earnings') }}</th>
            @if($data['is_provider'])
                <th>{{ translate('Additional_charge') }}</th>
            @else
                <th>{{ translate('Delivery_Fee') }}</th>
            @endif
            <th>{{ translate('Vat/Tax') }}</th>

        </thead>
        <tbody>
        @foreach($data['data'] as $key => $tr)
            <tr>
        <td>{{ $loop->index+1}}</td>
        <td>{{ $data['is_provider'] ? $tr->trip_id : $tr->order_id }}</td>
        <td>{{ $tr->created_at->format('Y-m-d '.config('timeformat')) ??  translate('N/A') }}</td>

        <td>
            {{ \App\CentralLogics\Helpers::format_currency($data['is_provider'] ? $tr->trip_amount : $tr->order_amount) }}
        </td>
        <td>
            {{ \App\CentralLogics\Helpers::format_currency($tr->store_amount - $tr->tax) }}
        </td>
        <td>
            {{ \App\CentralLogics\Helpers::format_currency($tr->admin_commission) }}
        </td>
        <td>
            {{ \App\CentralLogics\Helpers::format_currency($data['is_provider'] ? $tr->additional_charge : $tr->delivery_charge) }}
        </td>
        <td>
            {{ \App\CentralLogics\Helpers::format_currency($tr->tax) }}
        </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
