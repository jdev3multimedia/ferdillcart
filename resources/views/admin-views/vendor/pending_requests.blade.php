@extends('layouts.admin.app')

@section('title',translate('messages.new_joining_requests'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title"><i class="tio-filter-list"></i> {{translate('messages.new_joining_requests')}}</h1>
            <div class="page-header-select-wrapper">

                @if(!isset(auth('admin')->user()->zone_id))
                <div class="select-item">
                    <select name="zone_id" class="form-control js-select2-custom set-filter" data-url="{{url()->full()}}" data-filter="zone_id">
                        <option value="" {{!request('zone_id')?'selected':''}}>{{ translate('messages.All_Zones') }}</option>
                        @foreach(\App\Models\Zone::orderBy('name')->get() as $z)
                            <option
                                    value="{{$z['id']}}" {{isset($zone) && $zone->id == $z['id']?'selected':''}}>
                                {{$z['name']}}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="js-nav-scroller hs-nav-scroller-horizontal mt-2">
                        <!-- Nav -->
                        <ul class="nav nav-tabs mb-3 border-0 nav--tabs nav--pills">
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('admin.store.pending-requests') }}"   aria-disabled="true">{{translate('messages.pending_stores')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.store.deny-requests') }}"  aria-disabled="true">{{translate('messages.denied_stores')}}</a>
                            </li>
                        </ul>
                        <!-- End Nav -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header py-2">
                <div class="search--button-wrapper">
                    <h4 class="card-title text-title">{{translate('messages.stores_list')}} <span class="badge badge-soft-dark ml-2" id="itemCount">{{$stores->total()}}</span></h4>

                    <div class="d-flex align-items-center gap-3 flex-sm-nowrap flex-wrap">
                        <form action="javascript:" id="search-form" class="search-form w-100">
                            @csrf
                            <div class="input-group input--group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control"
                                        placeholder="{{translate('ex_:_Search_Store_Name')}}" value="{{isset($search_by) ? $search_by : ''}}" aria-label="{{translate('messages.search')}}" required>
                                <button type="submit" class="btn btn--primary"><i class="tio-search"></i></button>
                            </div>
                        </form>
                        <div>
                            <div class="hs-unfold mr-2">
                                <a class="js-hs-unfold-invoker btn btn-sm btn-white d-inline-flex text-title font-medium dropdown-toggle min-height-40" href="javascript:;"
                                    data-hs-unfold-options='{
                                            "target": "#usersExportDropdown",
                                            "type": "css-animation"
                                        }'>
                                    <i class="tio-download-to mr-1 text-title"></i> {{ translate('messages.export') }}
                                </a>
                                <div id="usersExportDropdown"
                                    class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                    <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                                    <a id="export-excel" class="dropdown-item" href="{{route('admin.business-settings.module.export', ['type'=>'excel',request()->getQueryString()])}}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                            alt="Image Description">
                                        {{ translate('messages.excel') }}
                                    </a>
                                    <a id="export-csv" class="dropdown-item" href="{{route('admin.business-settings.module.export', ['type'=>'csv',request()->getQueryString()])}}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                            alt="Image Description">
                                        .{{ translate('messages.csv') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Header -->

            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="columnSearchDatatable"
                        class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                        data-hs-datatables-options='{
                            "order": [],
                            "orderCellsTop": true,
                            "paging":false

                        }'>
                    <thead class="bg-table-head">
                    <tr>
                        <th class="border-0">{{translate('sl')}}</th>
                        <th class="border-0">{{translate('messages.store_information')}}</th>
                        <th class="border-0">{{translate('messages.module')}}</th>
                        <th class="border-0">{{translate('messages.owner_information')}}</th>
                        <th class="border-0">{{translate('messages.zone')}}</th>
                        <th class="text-uppercase border-0">{{translate('messages.status')}}</th>
                        <th class="border-0 text-center">{{translate('messages.action')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($stores as $key=>$store)
                        <tr>
                            <td>{{$key+$stores->firstItem()}}</td>
                            <td>
                                <div>
                                    <a href="{{route('admin.store.view', $store->id)}}" class="table-rest-info" alt="view store">
                                        <img class="img--60 rounded broder onerror-image" data-onerror-image="{{asset('public/assets/admin/img/160x160/img1.jpg')}}"
                                        src="{{ $store['logo_full_url'] ?? asset('public/assets/admin/img/160x160/img1.jpg') }}" >
                                        <div class="info"><div class="text--title">
                                            {{Str::limit($store->name,20,'...')}}
                                            </div>
                                            <div class="font-light">
                                                {{translate('messages.id')}}:{{$store->id}}
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{Str::limit($store->module->module_name,20,'...')}}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{Str::limit($store->vendor->f_name.' '.$store->vendor->l_name,20,'...')}}
                                </span>
                                <div>
                                    {{$store['phone']}}
                                </div>
                            </td>
                            <td>
                                {{$store->zone?$store->zone->name:translate('messages.zone_deleted')}}
                            </td>

                            <td>
                                @if(isset($store->vendor->status))
                                    @if($store->vendor->status)
                                        <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$store->id}}">
                                            <input type="checkbox" data-url="{{route('admin.store.status',[$store->id,$store->status?0:1])}}" data-message="{{translate('messages.you_want_to_change_this_store_status')}}" class="toggle-switch-input status_change_alert" id="stocksCheckbox{{$store->id}}" {{$store->status?'checked':''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                    @else
                                    <span class="badge badge-soft-danger">{{translate('messages.denied')}}</span>
                                    @endif
                                @else
                                    <span class="badge badge-soft-danger">{{translate('messages.pending')}}</span>
                                @endif
                            </td>

                            <td>
                                <div class="btn--container justify-content-center">

                                    <a class="btn action-btn btn-outline-theme-dark"
                                    href="{{route('admin.store.edit',[$store['id'],'pending'=>1])}}" title="{{translate('messages.edit_store')}}"><i class="tio-edit"></i>
                                    </a>


                                    @if($store->vendor->status == 0)
                                        <a class="btn action-btn btn--primary btn-outline-primary float-right swal_fire_alert" data-toggle="tooltip" data-placement="top"
                                        data-original-title="{{ translate('messages.approve') }}"
                                       data-title="{{translate('messages.are_you_sure_?')}}"
                                       data-image_url="{{ asset('public/assets/admin/img/off-danger.png') }}"
                                       data-confirm_button_text="{{ translate('messages.yes') }}"
                                       data-cancel_button_text="{{ translate('messages.No') }}"
                                       data-message="{{translate('messages.you_want_to_approve_the_vendor_joining_request.')}}"
                                        data-url="{{route('admin.store.application',[$store['id'],1])}}"
                                            href="javascript:"><i class="tio-done font-weight-bold"></i></a>
                                    @endif
                                    @if (!isset($store->vendor->status))
                                        <button class="btn action-btn btn--danger btn-outline-danger float-right"
                                        data-original-title="{{ translate('Reject') }}" data-toggle="modal" data-target="#confirmation-reason-btn{{ $store->id }}"
                                        data-message="{{translate('messages.you_want_to_deny_this_application')}}"
                                            href="javascript:"><i class="tio-clear font-weight-bold"></i></button>
                                    @endif
                                </div>



    <!-- Confiramtion Reason Modal -->
    <div class="modal shedule-modal fade" id="confirmation-reason-btn{{ $store->id }}" tabindex="-1"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content pb-2 max-w-500">
                <form action="{{ route('admin.store.application', [$store['id'], 0]) }}" method="get">
                <div class="modal-header">
                    <button type="button"
                        class="close bg-modal-btn w-30px h-30 rounded-circle position-absolute right-0 top-0 m-2 z-2"
                        data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <img src="{{ asset('public/assets/admin/img/delete-confirmation.png') }}" alt="icon"
                            class="mb-3">
                        <h3 class="mb-2">{{ translate('messages.Are_you_sure_?') }}</h3>
                        <p class="mb-0">{{ translate('You want to deny this joining application?') }}</p>
                    </div>
                    <div class="px-3 mt-4">
                        <h5 class="mb-2">{{ translate('messages.Reason') }}</h5>
                        <textarea name="rejection_note" id="" class="form-control" rows="2" required
                            placeholder="{{ translate('messages.Type_here_the_denied_reason...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0 gap-2">
                    <button type="button" class="btn min-w-120px btn--reset" data-dismiss="modal">{{ translate('messages.No') }}</button>
                    <button type="submit" class="btn min-w-120px btn--primary">{{ translate('messages.Yes') }}</button>
                </div>
            </form>
            </div>
        </div>
    </div>


                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
            <!-- End Table -->
                @if(count($stores) !== 0)
                <hr>
                @endif
                {{-- <div class="d-flex align-items-center justify-content-end gap-24 flex-wrap px-3 pb-3">
                    <div class="d-flex aign-items-center gap-4">
                        <p class="text-dark m-0 lh-1">1-5 of 13</p>
                        <div class="d-flex align-items-center gap-3">
                            <a class="text-dark fs-16 disabled" href=""><i class="tio-chevron-left"></i></a>
                            <a class="text-dark fs-16" href=""><i class="tio-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="page-area">
                        <p>Your Pagination hare</p>
                    </div>
                    <div class="page-area">
                        {!! $stores->withQueryString()->links() !!}
                    </div>
                </div> --}}
                <div class="page-area">
                    {!! $stores->withQueryString()->links() !!}
                </div>
                @if(count($stores) === 0)
                <div class="empty--data">
                    <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                    <h5>
                        {{translate('no_data_found')}}
                    </h5>
                </div>
                @endif
        </div>
        <!-- End Card -->
    </div>

@endsection

@push('script_2')
    <script>
        "use strict";
        $('.status_change_alert').on('click', function (event) {
            let url = $(this).data('url');
            let message = $(this).data('message');
            status_change_alert(url, message, event)
        })
        function status_change_alert(url, message, e) {
            e.preventDefault();
            Swal.fire({
                title: '{{ translate('Are you sure?') }}' ,
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: '{{translate('messages.no')}}',
                confirmButtonText: '{{translate('messages.yes')}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    location.href=url;
                }
            })
        }
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            let datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });

            $('#column2_search').on('keyup', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });

            $('#column3_search').on('keyup', function () {
                datatable
                    .columns(3)
                    .search(this.value)
                    .draw();
            });

            $('#column4_search').on('keyup', function () {
                datatable
                    .columns(4)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                let select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
         $('.swal_fire_alert').on('click', function (event) {
            let url = $(this).data('url');
            let message = $(this).data('message');
            let title = $(this).data('title');
            let imageUrl = $(this).data('image_url');
            let cancelButtonText = $(this).data('cancel_button_text');
            let confirmButtonText = $(this).data('confirm_button_text');
            swalFire(url,title, message, imageUrl,cancelButtonText, confirmButtonText)
        })

        $('#search-form').on('submit', function () {
            let formData = new FormData(this);
            set_filter('{!! url()->full() !!}',formData.get('search'),'search_by')
        });
    </script>
@endpush
