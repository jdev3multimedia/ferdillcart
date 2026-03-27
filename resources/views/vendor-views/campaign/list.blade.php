@extends('layouts.vendor.app')

@section('title',translate('messages.Campaign List'))

@push('css_or_js')

@endpush

@section('content')
@php($store_id = \App\CentralLogics\Helpers::get_store_id())
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/campaign.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.campaign_list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$campaigns->total()}}</span>
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <!-- Card -->
        <div class="card">
            <div class="card-header border-0 justify-content-end ">
                <form  class="min--250">
                    @csrf
                    <!-- Search -->
                    <div class="input-group input--group">
                        <input id="datatableSearch_"  value="{{request()?->search ?? ''}}" type="search" name="search" class="form-control" placeholder="{{translate('messages.ex_search_name')}}" aria-label="{{translate('messages.search')}}">
                        <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                    </div>
                    <!-- End Search -->
                </form>
            </div>
            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="columnSearchDatatable"
                        class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                        data-hs-datatables-options='{
                            "order": [],
                            "orderCellsTop": true,
                            "paging":false
                        }'>
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0">{{translate('messages.#')}}</th>
                            <th class="border-0 w-30p">{{translate('messages.title')}}</th>
                            <th class="border-0 w-25p">{{translate('messages.image')}}</th>
                            <th class="border-0 w-25p">{{translate('messages.date_duration')}}</th>
                            <th class="border-0 w-25p">{{translate('messages.time_duration')}}</th>
                            <th class="border-0 text-center">{{translate('messages.status')}}</th>
                            <th class="border-0 text-center">{{translate('messages.action')}}</th>
                        </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($campaigns as $key=>$campaign)
                        <tr>
                            <td>{{$key+$campaigns->firstItem()}}</td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{Str::limit($campaign['title'],25,'...')}}
                                </span>
                            </td>
                            <td>
                                <div class="overflow-hidden">
                                    <img class="img--vertical max--200 mw--200 onerror-image" src="{{ $campaign['image_full_url'] }}"
                                         data-onerror-image="{{asset('public/assets/admin/img/160x160/img2.jpg')}}"  alt="image">
                                </div>
                            </td>
                            <td>
                                <span class="bg-gradient-light text-dark">{{$campaign->start_date?$campaign->start_date->format('d M, Y'). ' - ' .$campaign->end_date->format('d M, Y'): 'N/A'}}</span>
                            </td>
                            <td>
                                <span class="bg-gradient-light text-dark">{{$campaign->start_time?date(config('timeformat'),strtotime($campaign->start_time)). ' - ' .date(config('timeformat'),strtotime($campaign->end_time)): 'N/A'}}</span>
                            </td>
                            <?php
                            $store_ids = [];
                            $store_status = '--';
                            foreach($campaign->stores as $store)
                                {
                                    if ($store->id == $store_id && $store->pivot) {
                                        $store_status = $store->pivot->campaign_status;
                                    }
                                    $store_ids[] = $store->id;
                                }
                             ?>
                            <td class="text-capitalize">
                                @if ($store_status == 'pending')
                                    <span class="badge badge-soft-info">
                                        {{ translate('messages.not_approved') }}
                                    </span>
                                @elseif($store_status == 'confirmed')
                                    <span class="badge badge-soft-success">
                                        {{ translate('messages.confirmed') }}
                                    </span>
                                @elseif($store_status == 'rejected')
                                    <span class="badge badge-soft-danger">
                                        {{ translate('messages.rejected') }}
                                    </span>
                                @else
                                    <span class="badge badge-soft-info">
                                        {{ translate(str_replace('_', ' ', $store_status)) }}
                                    </span>
                                @endif

                            </td>
                            <td class="text-center">
                                @if ($store_status == 'rejected')
                                    <span class="badge badge-pill badge-danger">{{ translate('Rejected') }}</span>
                                @else
                                    @if(in_array($store_id,$store_ids))

                                    <span type="button"
                                          data-id="campaign-{{$campaign['id']}}"
                                          data-message="{{translate('messages.alert_store_out_from_campaign')}}"
                                          title="You are already joined. Click to out from the campaign." class="badge btn--danger text-white  form-alert ">{{translate('messages.leave')}}</span>
                                    <form action="{{route('vendor.campaign.remove-store',[$campaign['id'],$store_id])}}"
                                            method="GET" id="campaign-{{$campaign['id']}}">
                                        @csrf
                                    </form>
                                    @else
                                    <span type="button" class="badge btn--primary text-white form-alert"
                                          data-id="campaign-{{$campaign['id']}}"
                                          data-message="{{translate('messages.alert_store_join_campaign')}}"
                                        title="Click to join the campaign">{{translate('messages.join')}}</span>
                                    <form action="{{route('vendor.campaign.add-store',[$campaign['id'],$store_id])}}"
                                            method="GET" id="campaign-{{$campaign['id']}}">
                                        @csrf
                                    </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if(count($campaigns) !== 0)
                <hr>
                @endif
                <table class="page-area">
                    <tfoot>
                    {!! $campaigns->links() !!}
                    </tfoot>
                </table>
                @if(count($campaigns) === 0)
                <div class="empty--data">
                    <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                    <h5>
                        {{translate('no_data_found')}}
                    </h5>
                </div>
                @endif
            </div>
            <!-- End Table -->
        </div>
        <!-- End Card -->
    </div>

@endsection

