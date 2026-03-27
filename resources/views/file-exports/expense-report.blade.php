<div class="row">
    <div class="col-lg-12 text-center "><h1 >{{ translate('expense_reports') }}</h1></div>
    <div class="col-lg-12">



    <table>
        <thead>
            <tr>
                <th>{{ translate('Search_Criteria') }}</th>
                <th></th>
                <th></th>
                <th>
                    @if(isset($data['module']))
                    {{ translate('module' )}} - {{ $data['module']?translate($data['module']):translate('all') }}
                    <br>
                    @endif

                    {{ translate('zone' )}} - {{ $data['zone']??translate('all') }}
                    <br>
                    {{ (isset($data['module_type']) && $data['module_type'] == 'rental')?translate('provider'):translate('vendor')}} - {{ $data['store']??translate('all') }}
                    @if (!isset($data['type']) )
                    <br>
                    {{ translate('customer' )}} - {{ $data['customer']??translate('all') }}
                    @endif
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
                <th> </th>
                <th></th>
                <th></th>
                <th></th>
                </tr>
        <tr>
            <th>{{ translate('sl') }}</th>
            @if (isset($data['module_type']))
            <th>{{$data['module_type'] == 'rental'? translate('trip_id') : translate('messages.order_id') }}</th>
            @elseif(addon_published_status('Rental'))
                <th>{{ translate('messages.order_id') }}</th>
                <th>{{ translate('trip_id') }}</th>
            @endif
            <th>{{translate('Date & Time')}}</th>
            <th>{{ translate('Expense Type') }}</th>
            <th>{{ translate('Customer Name') }}</th>
            <th>{{translate('expense amount')}}</th>
        </thead>
        <tbody>
        @foreach($data['expenses'] as $key => $exp)
            <tr>
                <td>{{ $key+1}}</td>
                @if (isset($data['module_type']))
                    <td>
                        @if ($exp->order && $data['module_type'] != 'rental')
                            {{ $exp['order_id'] }}
                        @elseif ($exp->trip && $data['module_type'] == 'rental')
                            {{ $exp['trip_id'] }}
                        @endif
                    </td>
                @elseif(addon_published_status('Rental'))
                    <td>{{ $exp['order_id'] }}</td>
                    <td>{{ $exp['trip_id'] }}</td>
                @endif
                <td>
                    {{date('Y-m-d '.config('timeformat'),strtotime($exp->created_at))}}
                </td>
                <td>{{translate("messages.{$exp['type']}")}}</td>
                <td class="text-center">
                    @if ($exp->order)

                    @if($exp->order?->is_guest)
                    @php($customer_details = json_decode($exp->order['delivery_address'],true))
                    <strong>{{$customer_details['contact_person_name']}}</strong>

                    @elseif($exp->order?->customer)

                    {{$exp->order?->customer['f_name'].' '.$exp->order?->customer['l_name']}}
                    @else
                        <label
                            class="badge badge-danger">{{translate('messages.invalid_customer_data')}}</label>
                    @endif

                    @elseif($exp->trip)
                    @if ($exp?->trip?->customer)

                        {{ $exp?->trip?->customer?->fullName }}

                        @elseif($exp?->trip?->user_info['contact_person_name'])
                            <div class="font-medium">
                                {{$exp?->trip?->user_info['contact_person_name'] }}
                            </div>
                        @else
                            {{ translate('messages.Guest_user') }}
                        @endif


                    @elseif ($exp['type'] == 'add_fund_bonus')
                    {{ $exp->user->f_name.' '.$exp->user->l_name }}
                    @else
                    <label class="badge badge-danger">{{translate('messages.invalid_customer_data')}}</label>

                    @endif
                </td>
                <td>{{\App\CentralLogics\Helpers::format_currency($exp['amount'])}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
