@extends('layouts.admin.app')

@section('title',translate('Item List'))

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
                            {{translate('messages.item_list')}} <span class="badge badge-soft-dark ml-2" id="foodCount">{{$items->total()}}</span>
                        </span>
                    </h1>
                </div>
            </div>

        </div>
        <!-- End Page Header -->
        <!-- Card -->

        @php
            $pharmacy =0;
            if (Config::get('module.current_module_type') == 'pharmacy'){
                $pharmacy =1;
            }
        @endphp
            <div class="card mb-3">
                <form action="" method="get">
                <div class="card-body">
                    <!-- Header -->
                    <div class="card-header border-0 px-0 pt-0 pb-xxl-3">
                        <h1 class="m-0">{{ translate('search_data') }}</h1>
                    </div>
                    <div class="row g-lg-3 g-2">
                        <div class="col-sm-6 col-md-3">
                            <div class="select-item">
                            <select name="store_id" id="store"
                            data-url="{{route("admin.store.get-stores")}}"
                            data-placeholder="{{translate('messages.select_store')}}" class="js-data-example-ajax form-control" required title="Select Store" >
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
                                <select name="zone_id" class="form-control js-select2-custom">
                                    <option value="" {{!request('zone_id')?'selected':''}}>{{ translate('messages.All_Zones') }}</option>
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
                        <div class="col-sm-6 col-md-{{ $pharmacy == 1 ? '2':'3' }}">
                            <div class="select-item">

                                <select name="category_id" id="category_id" data-placeholder="{{ translate('messages.select_category') }}" data-url="{{route("admin.category.get-all")}}" class="js-data-example-ajax form-control"  >
                                    @if($category)
                                    <option value="{{$category->id}}" selected>{{$category->name}}</option>
                                    @else
                                    <option value="all" selected>{{translate('messages.all_category')}}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-{{ $pharmacy == 1 ? '2':'3' }}">
                            <div class="select-item">
                                <select name="sub_category_id" class="form-control js-data-example-ajax "
                                data-url="{{ route('admin.item.get-categories') }}"
                                data-placeholder="{{ translate('messages.select_sub_category') }}" id="sub-categories">
                                    @if($sub_category)
                                    <option value="{{$sub_category->id}}" selected>{{$sub_category->name}}</option>
                                    @else
                                    <option value="all" selected>{{translate('messages.all_sub_category')}}</option>
                                    @endif

                                </select>
                            </div>
                        </div>
                        @if ($pharmacy == 1)
                            <div class="col-sm-6 col-md-2">
                                <div class="select-item">
                                <select name="condition_id" id="condition_id" class="form-control"
                                    data-url="{{ route('admin.common-condition.get-all') }}"
                                    data-placeholder="{{ translate('messages.Select_Condition') }}">
                                    @if($condition)
                                    <option value="{{$condition->id}}" selected>{{$condition->name}}</option>
                                    @else
                                    <option value="all" selected>{{translate('messages.all_conditions')}}</option>
                                    @endif
                                </select>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="btn--container justify-content-end mt-xl-4 mt-3">
                        <button type="reset" data-url="{{ url()->current() }}" class="btn btn--reset redirect-url">{{ translate('messages.reset') }}</button>
                        <button type="submit" class="btn btn--primary">{{ translate('messages.Filter') }}</button>
                    </div>
                </div>
                </form>
            </div>

        <div class="card">
            <!-- Header -->
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper justify-content-end">
                    <form class="search-form">

                        <!-- Search -->
                        <div class="input-group input--group">
                            <input id="datatableSearch" name="search" value="{{ request()?->search ?? null }}" type="search" class="form-control h--40px" placeholder="{{translate('ex_:_search_item_by_name')}}" aria-label="{{translate('messages.search_here')}}">
                            <button type="submit" class="btn btn--primary h--40px"><i class="tio-search"></i></button>
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
                            <a id="export-excel" class="dropdown-item" href="{{ route('admin.item.export', ['type' => 'excel', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="{{ route('admin.item.export', ['type' => 'csv', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                    alt="Image Description">
                                .{{ translate('messages.csv') }}
                            </a>

                        </div>
                    </div>
                    <!-- End Unfold -->
                    @if (Config::get('module.current_module_type') != 'food')
                    <div>
                        <a href="{{ route('admin.report.stock-report') }}" class="btn btn--primary font-regular">{{translate('messages.Low_Stock_List')}}</a>
                    </div>
                    @endif
                    @if (\App\CentralLogics\Helpers::get_mail_status('product_approval'))
                    <div>
                        <a href="{{ route('admin.item.approval_list') }}" class="btn btn--primary font-regular">{{translate('messages.New_Product_Request')}}</a>
                    </div>
                    @endif
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
                        @if (Config::get('module.current_module_type') != 'food')
                        <th class="text-title border-0">{{translate('messages.quantity')}}</th>
                        @endif
                        <th class="text-title border-0">{{translate('messages.store')}}</th>
                        <th class="text-title border-0 text-center">{{translate('messages.price')}}</th>

                        @if ($productWiseTax)
                        <th class="text-title border-0 ">{{ translate('messages.Vat/Tax') }}</th>
                        @endif

                        <th class="text-title border-0 text-center">{{translate('messages.status')}}</th>
                        <th class="text-title border-0 text-center">{{translate('messages.action')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($items as $key=>$item)
                        <tr>
                            <td>{{$key+$items->firstItem()}}</td>
                            <td>
                                <a class="media align-items-center" href="{{route('admin.item.view',[$item['id']])}}">
                                    <img class="avatar avatar-lg mr-3 onerror-image"

                                    src="{{ $item['image_full_url'] ?? asset('public/assets/admin/img/160x160/img2.jpg') }}"

                                    data-onerror-image="{{asset('public/assets/admin/img/160x160/img2.jpg')}}" alt="{{$item->name}} image">
                                    <div title="{{ $item['name'] }}" class="media-body">
                                        <h5 class="text-hover-primary mb-0">{{Str::limit($item['name'],20,'...')}}</h5>
                                    </div>
                                </a>
                            </td>
                            <td title="{{ $item?->category?->name }}">
                            {{Str::limit($item->category?$item->category->name:translate('messages.category_deleted'),20,'...')}}
                            </td>
                            @if (Config::get('module.current_module_type') != 'food')
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="text-hover-primary fw-medium mb-0">{{$item->stock}}</h5>
                                    <span data-toggle="modal"  data-id="{{ $item->id }}"  data-target="#update-quantity" class="text-primary tio-add-circle fs-22 cursor-pointer update-quantity"></span>
                                </div>
                            </td>
                            @endif
                            <td>
                                @if ($item->store)
                                <a title="{{ $item?->store?->name }}" href="{{route('admin.store.view', $item->store->id)}}" class="table-rest-info" alt="view store"> {{  Str::limit($item->store->name, 20, '...') }}</a>
                                @else
                                {{  translate('messages.store deleted!') }}
                                @endif

                            </td>
                            <td>
                                <div class="text-right mw--85px">
                                    {{\App\CentralLogics\Helpers::format_currency($item['price'])}}
                                </div>
                            </td>

                            @if ($productWiseTax)
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        @forelse ($item?->taxVats?->pluck('tax.name', 'tax.tax_rate')->toArray() as $key => $tax)
                                            <span> {{ $tax }} : <span class="font-bold">
                                                    ({{ $key }}%)
                                                </span> </span>
                                            <br>
                                        @empty
                                            <span> {{ translate('messages.no_tax') }} </span>
                                        @endforelse
                                    </span>
                                </td>
                                @endif


                            <td>
                                <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$item->id}}">
                                    <input type="checkbox" class="toggle-switch-input redirect-url" data-url="{{route('admin.item.status',[$item['id'],$item->status?0:1])}}" id="stocksCheckbox{{$item->id}}" {{$item->status?'checked':''}}>
                                    <span class="toggle-switch-label mx-auto">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="btn action-btn btn--primary btn-outline-primary"
                                        href="{{route('admin.item.edit',[$item['id']])}}" title="{{translate('messages.edit_item')}}"><i class="tio-edit"></i>
                                    </a>
                                    <a class="btn  action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                        data-id="food-{{$item['id']}}" data-message="{{translate('messages.Want_to_delete_this_item')}}" title="{{translate('messages.delete_item')}}"><i class="tio-delete-outlined"></i>
                                    </a>
                                    <form action="{{route('admin.item.delete',[$item['id']])}}"
                                            method="post" id="food-{{$item['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
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
            <!-- End Table -->
        </div>
        <!-- End Card -->
    </div>

    {{-- Add Quantity Modal --}}
    <div class="modal fade update-quantity-modal" id="update-quantity" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-0">

                    <form action="{{route('admin.item.stock-update')}}" method="post">
                        @csrf
                        <div class="mt-2 rest-part w-100"></div>
                        <div class="btn--container justify-content-end">
                            <button type="reset" data-dismiss="modal" aria-label="Close" class="btn btn--reset">{{translate('cancel')}}</button>
                            <button type="submit" id="submit_new_customer" class="btn btn--primary">{{translate('update_stock')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

   <input type="hidden" id="current_module_id" value="{{Config::get('module.current_module_id')}}">
@endsection

@push('script_2')
    <script>
        "use strict";
        $(document).on('ready', function () {
            let datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
            select: {
                style: 'multi',
                classMap: {
                checkAll: '#datatableCheckAll',
                counter: '#datatableCounter',
                counterInfo: '#datatableCounterInfo'
                }
            },
            });
        });

        $('#store').select2({
            ajax: {
                url: $('#store').data('url'),
                data: function (params) {
                    return {
                        q: params.term, // search term
                        all:true,
                        module_id:$('#current_module_id').val(),
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
                url: $('#category_id').data('url'),
                data: function (params) {
                    return {
                        q: params.term,
                        all:true,
                        module_id:$('#current_module_id').val(),
                        position:0,
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

        $('#category_id').on('change', function (e) {
            $('#sub-categories').val(null).trigger('change');
        })

        $('#sub-categories').select2({
            ajax: {
                url: $('#sub-categories').data('url'),
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        module_id: $('#current_module_id').val(),
                        parent_id: $('#category_id').val(),
                        sub_category: true
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#condition_id').select2({
            ajax: {
                url: $('#condition_id').data('url'),
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        all:true,
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('.update-quantity').on('click', function (){
            let val = $(this).data('id');
            $.get({
                url: '{{ route('admin.item.get_stock') }}',
                data: { id: val },
                dataType: 'json',
                success: function (data) {
                    $('.rest-part').empty().html(data.view);
                    update_qty();
                },
            });
        })

        function update_qty() {
            let total_qty = 0;
            let qty_elements = $('input[name^="stock_"]');
            for (let i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            if(qty_elements.length > 0)
            {

                $('input[name="current_stock"]').attr("readonly", 'readonly');
                $('input[name="current_stock"]').val(total_qty);
            }
            else{
                $('input[name="current_stock"]').attr("readonly", false);
            }
        }



    </script>
@endpush
