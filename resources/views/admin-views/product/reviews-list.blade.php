@extends('layouts.admin.app')

@section('title',translate('Review List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/items.png')}}" class="w--22" alt="">
                </span>
                <span>
                    {{translate('messages.item_reviews')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header border-0 py-2">
                <h5 class="card-title">
                    {{translate('messages.Review_list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$reviews->total()}}</span></h5>
                <div class="search--button-wrapper justify-content-end">
                    <form  class="search-form">
                        <!-- Search -->
                        <div class="input-group input--group">
                            <input id="datatableSearch" name="search" value="{{ request()?->search ?? null }}" type="search" class="form-control min-height-45" placeholder="{{translate('ex_:_search_item_Name,_customer_Name,_Rating')}}" aria-label="{{translate('messages.search_here')}}">
                            <button type="submit" class="btn btn--secondary min-height-45"><i class="tio-search"></i></button>
                        </div>
                        <!-- End Search -->
                    </form>

                    @if(request()->get('search'))
                    <button type="reset" class="btn btn--primary ml-2 location-reload-to-base" data-url="{{url()->full()}}">{{translate('messages.reset')}}</button>
                    @endif

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
                            <a id="export-excel" class="dropdown-item" href="{{ route('admin.item.reviews_export', ['type' => 'excel', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="{{ route('admin.item.reviews_export', ['type' => 'csv', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                    alt="Image Description">
                                .{{ translate('messages.csv') }}
                            </a>

                        </div>
                    </div>
                </div>
            </div>
            <!-- End Header -->

            <div class="card-body p-0">
                <!-- Table -->
                <div class="table-responsive datatable-custom">
                    <table id="columnSearchDatatable"
                           class="table table-borderless table-thead-bordered table-nowrap card-table"
                           data-hs-datatables-options='{
                                    "order": [],
                                    "orderCellsTop": true,
                                    "paging": false
                                }'>
                        <thead class="thead-light">
                        <tr>
                            <th>{{ translate('messages.sl') }}</th>
                            <th>{{ translate('messages.Review_Id') }}</th>
                            <th class="w-10p">{{translate('messages.item')}}</th>
                            <th class="w-20p">{{translate('messages.customer')}}</th>
                            <th class="w-30p">{{translate('messages.review')}}</th>
                            <th>{{translate('messages.date')}}</th>
                            <th class="w-30p text-center">{{translate('messages.store_reply')}}</th>
                            <th>{{translate('messages.action')}}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($reviews as $key=>$review)
                            <tr>
                                <td>{{$key+$reviews->firstItem()}}</td>
                                <td>{{$review->review_id}}</td>

                                <td class="d-flex">
                                    @if ($review->item)
                                        <a class="media align-items-center min-w-60px d-block mb-1" href="{{route('admin.item.view',[$review->item['id']])}}">
                                            <img class="avatar avatar-lg mr-3 onerror-image"
                                                 src="{{ $review->item['image_full_url'] ?? asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                                 data-onerror-image="{{asset('public/assets/admin/img/160x160/img2.jpg')}}"
                                                 alt="{{ $review->item['name'] }} image">
                                            {{--                                                    <div class="media-body">--}}
                                            {{--                                                        <h5 class="text-hover-primary mb-0">{{Str::limit($review->item['name'],20,'...')}}</h5>--}}
                                            {{--                                                    </div>--}}
                                        </a>
                                        <div class="py-2">
                                            <a class="media align-items-center mb-1" href="{{route('admin.item.view',[$review->item['id']])}}">
                                                <div class="media-body">
                                                    <h5 class="text-hover-primary mb-0">{{Str::limit($review->item['name'],20,'...')}}</h5>
                                                </div>
                                            </a>
                                            <a class="mr-5 text-body" href="{{route('admin.order.details',['id'=>$review->order_id])}}"> {{ translate('Order_ID') }}: {{$review->order_id}}</a>
                                        </div>
                                    @else
                                        {{translate('messages.Food_deleted!')}}
                                    @endif

                                </td>
                                <td>
                                    @if ($review->customer)
                                        <a href="{{route('admin.customer.view',[$review->user_id])}}">
                                            {{$review->customer?$review->customer->f_name:""}} {{$review->customer?$review->customer->l_name:""}}
                                        </a>
                                        <p>
                                            {{$review->customer?$review->customer->phone:""}}
                                        </p>
                                    @else
                                        {{translate('messages.customer_not_found')}}
                                    @endif
                                </td>
                                <td>
                                    <label class="rating">
                                        {{$review->rating}} <i class="tio-star m-sm-auto"></i>
                                    </label>
                                    <p class="text-wrap" data-toggle="tooltip" data-placement="left"
                                       data-original-title="{{ $review?->comment }}">{!! $review->comment?Str::limit($review->comment, 30, '...'):'' !!}</p>
                                </td>
                                <td class="text-uppercase">
                                    <div>
                                        {{ \App\CentralLogics\Helpers::date_format($review->created_at)  }}

                                    </div>
                                    <div>
                                        {{ \App\CentralLogics\Helpers::time_format($review->created_at)  }}
                                    </div>
                                </td>
                                <td>
                                    <p class="text-wrap text-center" data-toggle="tooltip" data-placement="top"
                                       data-original-title="{{ $review?->reply }}">{!! $review->reply?Str::limit($review->reply, 50, '...'): translate('messages.Not_replied_Yet') !!}</p>
                                </td>
                                <td>
                                    <label class="toggle-switch toggle-switch-sm" for="reviewCheckbox{{$review->id}}">
                                        <input type="checkbox"
                                               data-id="status-{{ $review['id'] }}" data-message="{{ $review->status ? translate('messages.you_want_to_hide_this_review_for_customer') : translate('messages.you_want_to_show_this_review_for_customer') }}"
                                               class="toggle-switch-input status_form_alert" id="reviewCheckbox{{ $review->id }}"
                                            {{ $review->status ? 'checked' : '' }}>
                                        <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                    </label>
                                    <form action="{{route('admin.item.reviews.status',[$review['id'],$review->status?0:1])}}" method="get" id="status-{{$review['id']}}">
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @if(count($reviews) === 0)
                        <div class="empty--data">
                            <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                            <h5>
                                {{translate('no_data_found')}}
                            </h5>
                        </div>
                    @endif
                    <div class="page-area px-4 pb-3">
                        <div class="d-flex align-items-center justify-content-end">
                            <div>
                                {!! $reviews->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Table -->
            </div>
        </div>
        <!-- End Card -->
    </div>

@endsection

@push('script_2')
    <script>
        "use strict";
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            let datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

        });

        $(".status_form_alert").on("click", function (e) {
            const id = $(this).data('id');
            const message = $(this).data('message');
            e.preventDefault();
            Swal.fire({
                title: '{{ translate('messages.are_you_sure') }}',
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
                    $('#' + id).submit()
                }
            })
        })


    </script>
@endpush
