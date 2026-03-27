@extends('layouts.admin.app')

@section('title',translate('New_Item_requests'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center g-2">
                <div class="col-md-9 col-12">
                    <h1 class="page-header-title">
                        <span class="page-header-icon">
                            <img src="{{asset('public/assets/admin/img/items.png')}}" class="w--22" alt="">
                        </span>
                        <span>
                            {{translate('messages.New_Item_requests')}} <span class="badge badge-soft-dark ml-2" id="foodCount">{{$items->total()}}</span>
                        </span>
                    </h1>
                </div>

            </div>

        </div>
        @php
            $pharmacy =0;
            if (Config::get('module.current_module_type') == 'pharmacy'){
                $pharmacy =1;
            }
            @endphp
        <!-- End Page Header -->
        <div class="card mb-3">
            <!-- Header -->
            <div class="card-header py-2 border-0">
                <h1>{{ translate('search_data') }}</h1>
            </div>

            <div class="row mr-1 ml-2 mb-2">
                <div class="col-sm-6 col-md-3">
                    <div class="select-item">
                        <select name="store_id" id="store" data-url="{{url()->full()}}" data-placeholder="{{translate('messages.select_store')}}" class="js-data-example-ajax form-control store-filter" required title="Select Store" oninvalid="this.setCustomValidity('{{translate('messages.please_select_store')}}')">
                            @if($store)
                            <option value="{{$store->id}}" selected>{{$store->name}}</option>
                            @else
                            <option value="all" selected>{{translate('messages.all_stores')}}</option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    @if(!isset(auth('admin')->user()->zone_id))
                        <div class="select-item">
                            <select name="zone_id" class="form-control js-select2-custom set-filter"
                                    data-url="{{url()->full()}}" data-filter="zone_id">
                                <option value="all" {{!request('zone_id')?'selected':''}}>{{ translate('messages.All_Zones') }}</option>
                                @foreach(\App\Models\Zone::orderBy('name')->get(['id','name']) as $z)
                                    <option
                                            value="{{$z['id']}}" {{request()?->zone_id == $z['id']?'selected':''}}>
                                        {{$z['name']}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="select-item">

                        <select name="category_id" id="category_id" data-placeholder="{{ translate('messages.select_category') }}"
                                class="js-data-example-ajax form-control set-filter" id="category_id"
                                data-url="{{url()->full()}}" data-filter="category_id">
                            @if($category)
                                <option value="{{$category->id}}" selected>{{$category->name}}</option>
                            @else
                                <option value="all" selected>{{translate('messages.all_category')}}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="select-item">
                        <select name="sub_category_id" class="form-control js-select2-custom set-filter" data-placeholder="{{ translate('messages.select_sub_category') }}" id="sub-categories" data-url="{{url()->full()}}" data-filter="sub_category_id">
                            @if (count($sub_categories) == 0 && $category )
                            <option selected>{{translate('messages.No_Subcategory')}}</option>
                            @else
                            <option value="all" selected>{{translate('messages.all_sub_category')}}</option>
                           @endif
                            @foreach($sub_categories as $z)
                                <option
                                        value="{{$z['id']}}" {{ request()?->sub_category_id == $z['id']?'selected':''}}>
                                    {{$z['name']}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>


            </div>
            <form  class="search-form" method="get" >
            <div class="row mr-1 ml-2 mb-5">

                <div class="col-sm-6 col-md-3">
                    <div class="select-item">
                        <select name="filter" class="form-control js-select2-custom set-filter"
                        data-url="{{url()->full()}}" data-filter="filter">
                            <option {{ !isset($filter)? 'selected' : '' }} >{{ translate('messages.All_Types') }}</option>
                            <option value="pending" {{ isset($filter) && $filter == 'pending' ? 'selected' : '' }} >{{ translate('messages.pending') }}</option>
                            <option value="rejected" {{ isset($filter) && $filter == 'rejected' ? 'selected' : '' }} >{{ translate('messages.rejected') }}</option>
                            <option value="custom" {{ isset($filter) && $filter == 'custom' ? 'selected' : '' }} >{{ translate('messages.Custom_Date') }}</option>
                        </select>
                    </div>
                </div>
                @if (isset($filter) && $filter == 'custom')
                <div class="col-sm-6 col-md-3">
                    <input type="date" name="from" id="from_date" class="form-control"
                        placeholder="{{ translate('Start Date') }}"
                    value="{{ request()?->from ?? ''}}" required>
                </div>
                <div class="col-sm-6 col-md-3">
                    <input type="date" name="to" id="to_date" class="form-control"
                    placeholder="{{ translate('End Date') }}"
                        value="{{ request()?->to ?? ''}}"  required>
                    </div>
                <div class="col-sm-6 col-md-3 ml-auto">
                    <button type="submit"
                        class="btn btn-primary btn-block h--45px">{{ translate('Filter') }}</button>
                    </div>
                    @endif

                </div>
            </form>

        </div>
        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper justify-content-end">
                    <form class="search-form">
                    {{-- @csrf --}}
                        <!-- Search -->
                        <div class="input-group input--group">
                            <input id="datatableSearch" name="search" value="{{ request()?->search ?? null }}" type="search" class="form-control h--40px" placeholder="{{translate('ex_:_search_item_name')}}" aria-label="{{translate('messages.search_here')}}">
                            <button type="submit" class="btn btn--primary h--40px"><i class="tio-search"></i></button>
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
                            <a id="export-excel" class="dropdown-item" href="{{ route('admin.item.export', ['type' => 'excel', 'table'=>'TempProduct' , request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="{{ route('admin.item.export', ['type' => 'csv',  'table'=>'TempProduct' , request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                    alt="Image Description">
                                .{{ translate('messages.csv') }}
                            </a>

                        </div>
                    </div>
                    <!-- End Unfold -->


                </div>
                <!-- End Row -->
            </div>
            <!-- End Header -->

            <!-- Table -->
            <div class="table-responsive datatable-custom" id="table-div">
                <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                    data-hs-datatables-options='{
                        "columnDefs": [{
                            "targets": [],
                            "width": "5%",
                            "orderable": false
                        }],
                        "order": [],
                        "info": {
                        "totalQty": "#datatableWithPaginationInfoTotalQty"
                        },

                        "entries": "#datatableEntries",

                        "isResponsive": false,
                        "isShowPaging": false,
                        "paging":false
                    }'>
                    <thead class="bg-table-head">
                    <tr>
                        <th class="text-title border-0">{{translate('sl')}}</th>
                        <th class="text-title border-0">{{translate('messages.name')}}</th>
                        <th class="text-title border-0">{{translate('messages.category')}}</th>
                        <th class="text-title border-0">{{translate('messages.store')}}</th>
                        <th class="text-title border-0">{{translate('messages.price')}}</th>
                        <!-- <th class="text-title border-0">{{translate('messages.Vat/Tax')}}</th> -->
                        <th class="text-title border-0">{{translate('messages.status')}}</th>
                        <th class="text-title border-0 text-center">{{translate('messages.action')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($items as $key=>$item)
                        <tr>
                            <td>{{$key+$items->firstItem()}}</td>
                            <td>
                                <a class="media align-items-center" href="{{route('admin.item.requested_item_view',['id'=> $item['id']])}}">
                                    <img class="avatar avatar-lg mr-3 onerror-image"

                                    src="{{ $item['image_full_url'] }}"

                                    data-onerror-image="{{asset('public/assets/admin/img/160x160/img2.jpg')}}" alt="{{$item->name}} image">
                                    <div class="media-body">
                                        <h5 class="text-hover-primary mb-0">{{Str::limit($item['name'],20,'...')}}</h5>
                                    </div>
                                </a>
                            </td>
                            <td>
                            {{Str::limit($item->category?$item->category->name:translate('messages.category_deleted'),20,'...')}}
                            </td>

                            <td>
                                @if ($item->store)
                                <a href="{{route('admin.store.view', $item->store->id)}}" class="table-rest-info" alt="view store"> {{  Str::limit($item->store->name, 20, '...') }}</a>
                                @else
                                {{  translate('messages.store deleted!') }}
                                @endif

                            </td>
                            <td>
                                <div class="mw--85px">
                                    {{\App\CentralLogics\Helpers::format_currency($item['price'])}}
                                </div>
                            </td>
                            <!-- <td>
                                <div class="color-677788 fs-12">
                                    <span>VAT: <strong>(5%)</strong></span> <br>
                                    <span>GST: <strong>(7%)</strong></span>
                                </div>
                            </td> -->
                            <td>
                                @if ($item->is_rejected == 1)
                                <span class="badge badge-soft-danger text-capitalize">
                                    {{ translate('messages.rejected') }}
                                </span>
                                @else
                                <span class="badge badge-soft-info text-capitalize">
                                    {{ translate('messages.pending') }}
                                </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="ml-2 btn btn-sm btn--warning btn-outline-warning action-btn" data-toggle="tooltip" data-placement="top"
                                    data-original-title="{{ translate('messages.View') }}" href="{{route('admin.item.requested_item_view',['id'=> $item['id']])}}">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <a class="btn action-btn btn--primary btn-outline-primary request_alert" data-toggle="tooltip" data-placement="top"
                                    data-original-title="{{ translate('messages.approve') }}"
                                    data-url="{{route('admin.item.approved',[ 'id'=> $item['id']])}}" data-message="{{translate('messages.you_want_to_approve_this_product')}}"
                                        href="javascript:"><i class="tio-done font-weight-bold"></i> </a>
                                    @if($item->is_rejected == 0)
                                        <a class="btn action-btn btn--danger btn-outline-danger cancelled_status" data-toggle="tooltip" data-placement="top"
                                        data-original-title="{{ translate('messages.deny') }}" data-url="{{ route('admin.item.deny', ['id'=> $item['id']]) }}" data-message="{{ translate('you_want_to_deny_this_product') }}"
                                        href="javascript:"><i class="tio-clear font-weight-bold"></i></a>
                                    @endif
                                    <a class="btn action-btn btn--primary btn-outline-primary"
                                        href="{{route('admin.item.edit',[$item['id'], 'temp_product' => true])}}" title="{{translate('messages.edit_item')}}"><i class="tio-edit"></i>
                                    </a>
                                    <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                        data-id="food-{{$item['id']}}" data-message="{{translate('messages.Want_to_delete_this_item')}}" title="{{translate('messages.delete_item')}}"><i class="tio-delete-outlined"></i>
                                    </a>
                                    <form action="{{route('admin.item.delete',[$item['id']])}}"
                                            method="post" id="food-{{$item['id']}}">
                                        @csrf @method('delete')
                                        <input type="hidden" value="1" name="temp_product" >
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if(count($items) !== 0)
                <hr>
                @endif
                <div class="page-area">
                        <tfoot class="border-top">
                        {!! $items->withQueryString()->links() !!}
                </div>
                @if(count($items) === 0)
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

@push('script_2')
    <script>
        "use strict";
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
        let datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
          select: {
            style: 'multi',
            classMap: {
              checkAll: '#datatableCheckAll',
              counter: '#datatableCounter',
              counterInfo: '#datatableCounterInfo'
            }
          },
          language: {
            zeroRecords: '<div class="text-center p-4">' +
                '<img class="w-7rem mb-3" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">' +

                '</div>'
          }
        });

        $('#datatableSearch').on('mouseup', function (e) {
          let $input = $(this),
            oldValue = $input.val();

          if (oldValue == "") return;

          setTimeout(function(){
            let newValue = $input.val();

            if (newValue == ""){
              // Gotcha
              datatable.search('').draw();
            }
          }, 1);
        });

        $('#toggleColumn_index').change(function (e) {
          datatable.columns(0).visible(e.target.checked)
        })
        $('#toggleColumn_name').change(function (e) {
          datatable.columns(1).visible(e.target.checked)
        })

        $('#toggleColumn_type').change(function (e) {
          datatable.columns(2).visible(e.target.checked)
        })

        $('#toggleColumn_vendor').change(function (e) {
          datatable.columns(3).visible(e.target.checked)
        })

        $('#toggleColumn_status').change(function (e) {
          datatable.columns(5).visible(e.target.checked)
        })
        $('#toggleColumn_price').change(function (e) {
          datatable.columns(4).visible(e.target.checked)
        })
        $('#toggleColumn_action').change(function (e) {
          datatable.columns(6).visible(e.target.checked)
        })

            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                let select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });

        $('#store').select2({
            ajax: {
                url: '{{url('/')}}/admin/store/get-stores',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        all:true,
                        module_id:{{Config::get('module.current_module_id')}},
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                    results: data
                    };
                },
                __port: function (params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#category_id').select2({
            ajax: {
                url: '{{route("admin.category.get-all")}}',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        all:true,
                        module_id:{{Config::get('module.current_module_id')}},
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                    results: data
                    };
                },
                __port: function (params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $(".request_alert").on("click", function () {
            const url = $(this).data('url');
            const message = $(this).data('message');
            Swal.fire({
                title: '{{translate('messages.are_you_sure')}}',
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
                    location.href = url;
                }
            })
        })

        $(".cancelled_status").on("click", function () {
            const route = $(this).data('url');
            const message = $(this).data('message');
            const processing = false;
            Swal.fire({
                    //text: message,
                    title: '{{ translate('messages.Are you sure ?') }}',
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#FC6A57',
                    cancelButtonText: '{{ translate('messages.Cancel') }}',
                    confirmButtonText: '{{ translate('messages.submit') }}',
                    inputPlaceholder: "{{ translate('Enter_a_reason') }}",
                    input: 'text',
                    html: message + '<br/>'+'<label>{{ translate('Enter_a_reason') }}</label>',
                    inputValue: processing,
                    preConfirm: (note) => {
                        location.href = route + '&note=' + note;
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                })
        })

        $('#from_date,#to_date').change(function() {
    let fr = $('#from_date').val();
    let to = $('#to_date').val();
    if (fr != '' && to != '') {
        if (fr > to) {
            $('#from_date').val('');
            $('#to_date').val('');
            toastr.error('Invalid date range!', Error, {
                CloseButton: true,
                ProgressBar: true
            });
        }
    }

})
    </script>
@endpush
