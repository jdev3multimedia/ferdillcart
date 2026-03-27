@extends('layouts.admin.app')

@section('title',translate('Campaign List'))


@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <h1 class="page-header-title">
                    <span class="page-header-icon">
                        <img src="{{asset('public/assets/admin/img/campaign.png')}}" class="w--26" alt="">
                    </span>
                    <span>
                        {{translate('messages.campaign')}}
                    </span>
                </h1>
                <a class="btn btn--primary" href="{{route('admin.campaign.add-new', 'basic')}}">
                    <i class="tio-add-circle"></i> {{translate('messages.add_new_campaign')}}
                </a>
            </div>
        </div>
        <!-- End Page Header -->
        <!-- Card -->
        <div class="card">
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper">
                    <h5 class="card-title">
                        {{translate('messages.campaign_list')}}
                        <span class="badge badge-soft-dark ml-2" id="itemCount">{{$campaigns->total()}}</span>
                    </h5>
                    <form class="search-form">

                        <!-- Search -->
                        <div class="input-group input--group">
                            <input id="datatableSearch" type="search" name="search"  value="{{ request()?->search ?? null }}" class="form-control" placeholder="{{ translate('messages.Ex:_Search Title ...') }}" aria-label="Search here">
                            <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                        </div>
                        <!-- End Search -->
                    </form>
                    @if(request()->get('search'))
                    <button type="reset" class="btn btn--primary ml-2 location-reload-to-base" data-url="{{url()->full()}}">{{translate('messages.reset')}}</button>
                    @endif

                       <!-- Unfold -->
                       <div class="hs-unfold mr-2">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40" href="javascript:;"
                            data-hs-unfold-options='{
                                    "target": "#usersExportDropdown",
                                    "type": "css-animation"
                                }'>
                            <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                        </a>

                        <div id="usersExportDropdown"
                            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">

                            <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                            <a id="export-excel" class="dropdown-item" href="
                                {{ route('admin.campaign.basic_campaign_export', ['type' => 'excel', request()->getQueryString()]) }}
                                ">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="
                            {{ route('admin.campaign.basic_campaign_export', ['type' => 'csv', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                    alt="Image Description">
                                .{{ translate('messages.csv') }}
                            </a>
                        </div>
                    </div>
                    <!-- End Unfold -->
                </div>
            </div>
            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="columnSearchDatatable"
                        class="font-size-sm table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                        data-hs-datatables-options='{
                            "order": [],
                            "orderCellsTop": true,
                            "paging":false
                        }'>
                    <thead class="thead-light">
                    <tr>
                        <th class="border-0">{{translate('messages.#')}}</th>
                        <th class="border-0" >{{translate('messages.title')}}</th>
                        <th class="border-0" >{{translate('messages.date_duration')}}</th>
                        <th class="border-0" >{{translate('messages.time_duration')}}</th>
                        <th class="border-0">{{translate('messages.status')}}</th>
                        <th class="border-0 text-center">{{translate('messages.action')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($campaigns as $key=>$campaign)
                        <tr>
                            <td>{{$key+$campaigns->firstItem()}}</td>
                            <td>
                                <a href="{{route('admin.campaign.view',['basic',$campaign->id])}}" title="{{ $campaign['title'] }}" class="d-block text-body">{{Str::limit($campaign['title'],25, '...')}}</a>
                            </td>
                            <td>
                                <span class="bg-gradient-light text-dark">{{$campaign->start_date? \App\CentralLogics\Helpers::date_format($campaign?->start_date).'-'.  \App\CentralLogics\Helpers::date_format($campaign?->end_date): 'N/A'}}</span>
                            </td>
                            <td>
                                <span class="bg-gradient-light text-dark">{{$campaign->start_time? \App\CentralLogics\Helpers::time_format($campaign?->start_time).'-'.  \App\CentralLogics\Helpers::time_format($campaign?->end_time): 'N/A'}}</span>
                            </td>
                            <td>
                                <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$campaign->id}}">
                                    <input type="checkbox" data-url=""
                                    data-id="stocksCheckbox{{$campaign->id}}"
                                    data-type="status"
                                    data-image-on="{{ asset('/public/assets/admin/img/modal/basic_campaign_on.png') }}"
                                    data-image-off="{{ asset('/public/assets/admin/img/modal/basic_campaign_off.png') }}"
                                    data-title-on="{{ translate('By_Turning_ON_Campaign!') }}"
                                    data-title-off="{{ translate('By_Turning_OFF_Campaign!') }}"
                                    data-text-on="<p>{{ translate('If_you_turn_on_this_status,_it_will_show_on_user_website_and_app.') }}</p>"
                                    data-text-off="<p>{{ translate('If_you_turn_off_this_status,_it_won’t_show_on_user_website_and_app') }}</p>"
                                    class="toggle-switch-input dynamic-checkbox" id="stocksCheckbox{{$campaign->id}}" {{$campaign->status?'checked':''}}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>
                            <form action="{{route('admin.campaign.status',['basic',$campaign['id'],$campaign->status?0:1])}}"
                            method="get" id="stocksCheckbox{{$campaign->id}}_form">
                            </form>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="btn action-btn btn-outline-primary btn--primary"
                                        href="{{route('admin.campaign.edit',['basic',$campaign['id']])}}" title="{{translate('messages.edit_campaign')}}"><i class="tio-edit"></i>
                                    </a>
                                    <a class="btn action-btn btn-outline-danger btn--danger form-alert" href="javascript:" data-id="campaign-{{$campaign['id']}}" data-message="{{translate('messages.Want_to_delete_this_item')}}"
                                         title="{{translate('messages.delete_campaign')}}"><i class="tio-delete-outlined"></i>
                                    </a>
                                    <form action="{{route('admin.campaign.delete',[$campaign['id']])}}"
                                                    method="post" id="campaign-{{$campaign['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
                @if(count($campaigns) !== 0)
                <hr>
                @endif
                <div class="page-area">
                    {!! $campaigns->links() !!}
                </div>
                @if(count($campaigns) === 0)
                <div class="empty--data">
                    <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                    <h5>
                        {{translate('no_data_found')}}
                    </h5>
                </div>
                @endif
            <!-- End Table -->
        </div>
        <!-- End Card -->
    </div>

@endsection

@push('script_2')

@endpush
