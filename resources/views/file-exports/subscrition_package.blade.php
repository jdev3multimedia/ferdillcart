
<div class="row">
    <div class="col-lg-12 text-center "><h1 > {{translate('subscription_package_list')}}
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
            <th>{{ translate('Package_Name') }}</th>
            <th>{{ translate('Price') }}</th>
            <th>{{ translate('Duration') }}</th>
            <th>{{ translate('Current_Subscriber') }}</th>
            <th>{{ translate('Status') }}</th>

        </thead>
        <tbody>
        @foreach($data['data'] as $key => $package)
            <tr>
        <td>{{ $loop->index+1}}</td>
        <td>{{ $package->package_name }}</td>
        <td>
            {{ \App\CentralLogics\Helpers::format_currency($package->price) }}
        </td>
        <td>{{$package->validity}} {{ translate('days') }}</td>
        <td>{{$package->current_subscribers_count ?? 0}}</td>
        <td>{{$package->status == 1 ? translate('messages.Activate') : translate('messages.Inactivate') }}</td>

            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
