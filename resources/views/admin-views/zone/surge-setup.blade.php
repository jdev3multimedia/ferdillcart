@extends('layouts.admin.app')

@section('title',translate('messages.surge_price_list'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

@if(count($zone->surge_prices) > 0 )
    <div class="content container-fluid">

        <h3 class="mb-20">{{translate('messages.Surge_Price')}}</h3>
        <div class="card">
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper">
                    <h5 class="card-title">
                        {{translate('messages.Surge_Price_List')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$surges->total()}}</span>
                    </h5>
                    <form class="search-form">
                        <!-- Search -->
                        <div class="input-group input--group">
                            <input id="datatableSearch_" type="search" name="search" class="form-control"
                                    placeholder="{{translate('messages.Search_Surge_Price')}}"  value="{{ request()?->search ?? null }}" aria-label="{{translate('messages.search')}}" required>
                            <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                        </div>
                        <!-- End Search -->
                    </form>
                    <a href="{{route('admin.business-settings.zone.surge-price.create',[$zone['id']])}}" class="btn btn--primary">{{ translate('Create Surge Price') }}</a>
                </div>
            </div>
            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="columnSearchDatatable"
                        class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0 fs-14">{{ translate('Sl') }}</th>
                            <th class="border-0 fs-14">{{ translate('Title') }}</th>
                            <th class="border-0 fs-14">
                            <div class="min-w-135px">
                                {{ translate('Selected Module') }}
                            </div>
                            </th>
                            <th class="border-0 fs-14">
                            <div class="min-w-135px">
                                {{ translate('Price Increase Rate') }}
                            </div>
                            </th>
                            <th class="border-0 fs-14">
                            <div class="min-w-135px">
                                {{ translate('Surge Price Schedule') }}
                            </div>
                            </th>
                            <th class="border-0 fs-14">{{ translate('Duration') }}</th>
                            <th class="border-0 fs-14">{{ translate('Status') }}</th>
                            <th class="border-0 fs-14 text-center">{{ translate('Action') }}</th>
                        </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($surges as $key=>$surge)
                        <tr>
                            <td class="pl-4">{{$key+$surges->firstItem()}}</td>
                            <td>{{{$surge['surge_price_name']}}}</td>
                            <td>
                                @php($names = \App\models\Module::whereIn('id', $surge->module_ids)->pluck('module_name')->implode(', '))
                                <span class="d-block text-limit-2 max-w-220px">
                                    {{$names}}
                                </span>
                            </td>
                            <td>
                                {{{$surge['price']}}}{{ $surge['price_type'] === 'percent' ? '%' : \App\CentralLogics\Helpers::currency_symbol() }}
                            </td>
                            <td class="text-capitalize">
                                {{{$surge['duration_type']}}}
                            </td>
                            <td>
                                @if($surge->duration_type === 'daily')
                                    <span class="d-block max-w-220px min-w-176px">
                                        <span class="d-block text-title">{{ \App\CentralLogics\Helpers::time_format($surge->start_time)  }} - {{ \App\CentralLogics\Helpers::time_format($surge->end_time)  }}</span>
                                        <span class="text-wrap">
                                            {{ \App\CentralLogics\Helpers::date_format($surge->start_date)  }} {{ translate('to')}} {{ \App\CentralLogics\Helpers::date_format($surge->end_date)  }}
                                        </span>
                                    </span>
                                @elseif($surge->duration_type === 'weekly')
                                    <span class="d-block max-w-220px min-w-176px">
                                        <span class="d-block text-title">{{ \App\CentralLogics\Helpers::time_format($surge->start_time)  }} - {{ \App\CentralLogics\Helpers::time_format($surge->end_time)  }}</span>
                                        <span class="text-wrap">
                                            @if($surge->is_permanent)
                                            {{ translate('messages.permanent') }}
                                            @else
                                            {{ \App\CentralLogics\Helpers::date_format($surge->start_date)  }} {{ translate('to')}} {{ \App\CentralLogics\Helpers::date_format($surge->end_date)  }}
                                            @endif
                                            @if($surge->weekly_days && count($surge->weekly_days) > 0)
                                                {{ '('.implode(', ', $surge->weekly_days).')' }}
                                            @endif
                                        </span>
                                    </span>
                                @elseif($surge->duration_type === 'custom')
                                    <span class="d-block max-w-220px min-w-176px">
                                        <span class="text-wrap">
                                            @php($customDays = $surge->custom_days)
                                            @php($start = \Carbon\Carbon::parse(reset($customDays)))
                                            @php($end = \Carbon\Carbon::parse(end($customDays)))
                                            {{ \App\CentralLogics\Helpers::date_format($start) }} {{ translate('to') }} {{ \App\CentralLogics\Helpers::date_format($end) }}
                                        </span>
                                    </span>
                                @endif
                            </td>
                            <td>
                                <label class="toggle-switch toggle-switch-sm" for="status-{{$surge['id']}}">
                                    <input type="checkbox" class="toggle-switch-input dynamic-checkbox"
                                            data-id="status-{{$surge['id']}}"
                                            data-type="status"
                                            data-image-on='{{ asset('public/assets/admin/img/status-ons.png') }}'
                                            data-image-off="{{ asset('public/assets/admin/img/status-ons.png') }}"
                                            data-title-on="{{translate('Turn_On_The_Status?')}}"
                                            data-title-off="{{translate('Turn_Off_The_Status?')}}"
                                            data-text-on="<p>{{translate('Are_you_sure,_do_you_want_to_turn_on_the_Surge_Price_status_from_your_system.')}}</p>"
                                            data-text-off="<p>{{translate('Are_you_sure,_do_you_want_to_turn_off_the_Surge_Price_status_from_your_system.')}}</p>"
                                            id="status-{{$surge['id']}}" {{$surge->status?'checked':''}}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                                <form action="{{route('admin.business-settings.zone.surge-price.status',[$surge['id'],$surge->status?0:1])}}" method="get" id="status-{{$surge['id']}}_form">
                                </form>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="btn action-btn btn--primary btn-outline-primary"
                                        href="{{route('admin.business-settings.zone.surge-price.edit',[$surge['id']])}}" title="{{translate('messages.edit_surge')}}"><i class="tio-edit"></i>
                                    </a>
                                    <a class="btn  action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                        data-id="surge-{{$surge['id']}}" data-message="{{translate('messages.Are_you_sure_you_want_to_delete_this_surge_Price_&_remove_it_permanently?')}}" title="{{translate('messages.delete_surge')}}"><i class="tio-delete-outlined"></i>
                                    </a>
                                    <form action="{{route('admin.business-settings.zone.surge-price.delete',[$surge['id']])}}"
                                            method="post" id="surge-{{$surge['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if(count($surges) !== 0)
            <hr>
            @endif
            <div class="page-area">
                {!! $surges->withQueryString()->links() !!}
            </div>
            @if(count($surges) === 0)
            <div class="empty--data">
                <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                <h5>
                    {{translate('no_data_found')}}
                </h5>
            </div>
            @endif
        </div>
    </div>
@else
<h3 class="mt-3 px-2">{{translate('messages.Surge_Price')}}</h3>
        <table id="#0" class="table m-0 table-borderless table-thead-bordered table-align-middle">
            <tbody id="table-div">
                <tr>
                    <td colspan="">
                        <div class="bg-light rounded table-column p-5 text-center">
                            <div class="pt-5">
                                <img class="mb-20" src="{{asset('public/assets/admin/img/price-emty.png')}}" alt="status">
                                <h4 class="mb-3">{{ translate('Currently you don’t have any Surge Price') }}</h4>
                                <p class="mb-20 fs-12 mx-auto max-w-400px">{{ translate('To enable surge pricing, you must create at least one Surge Price. In this page you see all the surge price you added.') }}</p>
                                <a href="{{route('admin.business-settings.zone.surge-price.create',[$zone['id']])}}" class="btn btn--primary">
                                    {{ translate('Create Surge Price') }}
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
@endif



        @endsection

        @push('script_2')
        @endpush
