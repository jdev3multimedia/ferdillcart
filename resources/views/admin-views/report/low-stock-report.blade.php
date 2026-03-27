@extends('layouts.admin.app')

@section('title',translate('stock_Update'))

@section('content')

<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/report.png')}}" class="w--22" alt="">
            </span>
            <span>
                {{translate('stock update')}}
            </span>
        </h1>
    </div>
    <!-- End Page Header -->
    <!-- Card -->
    <div class="card mt-3">
        <!-- Header -->
        <div class="card-header border-0 py-2">
            <div class="search--button-wrapper justify-content-end">
                <form class="search-form theme-style">
                    <!-- Search -->
                    <div class="input-group input--group">
                        <input id="datatableSearch" name="search" type="search" class="form-control" placeholder="{{translate('ex_:_search_name')}}" value="{{ request()?->search ?? null}}" aria-label="{{translate('messages.search_here')}}">
                        <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                    </div>
                    <!-- End Search -->
                </form>
                @if(request()->get('search'))
                <button type="reset" class="btn btn--primary ml-2 location-reload-to-base" data-url="{{url()->full()}}">{{translate('messages.reset')}}</button>
                @endif
                <div class="min--200">
                    <select name="zone_id" class="form-control js-select2-custom set-filter theme-style" data-url="{{ url()->full() }}" data-filter="zone_id" id="zone">
                        <option value="all">{{translate('All Zones')}}</option>
                        @foreach(\App\Models\Zone::orderBy('name')->get() as $z)
                        <option value="{{$z['id']}}" {{isset($zone) && $zone->id == $z['id']?'selected':''}}>
                            {{($z['name'])}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="min--200">
                    <select name="store_id" data-placeholder="{{translate('messages.select_store')}}" class="js-data-example-ajax form-control set-filter theme-style" data-url="{{ url()->full() }}" data-filter="store_id">
                        @if(isset($store))
                        <option value="{{$store->id}}" selected>{{$store->name}}</option>
                        @else
                        <option value="all" selected>{{translate('messages.all_stores')}}</option>
                        @endif
                    </select>
                </div>
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
                        <a id="export-excel" class="dropdown-item" href="{{route('admin.transactions.report.low-stock-wise-report-export', ['type'=>'excel',request()->getQueryString()])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                alt="Image Description">
                            {{ translate('messages.excel') }}
                        </a>
                        <a id="export-csv" class="dropdown-item" href="{{route('admin.transactions.report.low-stock-wise-report-export', ['type'=>'csv',request()->getQueryString()])}}">
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
            <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap card-table" data-hs-datatables-options='{
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
                <thead class="thead-light">
                    <tr>
                        <th class="border-0">{{translate('SL')}}</th>
                        <th class="border-0 w--2">{{translate('messages.name')}}</th>
                        <th class="border-0 w--2">{{translate('messages.store')}}</th>
                        <th class="border-0">{{translate('messages.zone')}}</th>
                        <th class="border-0">{{translate('Current stock')}}</th>
                        <th class="border-0">{{translate('messages.action')}}</th>
                    </tr>
                </thead>

                <tbody id="set-rows">

                    @foreach($items as $key=>$item)
                    <tr>
                        <td>{{$key+$items->firstItem()}}</td>
                        <td>
                            <a class="media align-items-center" href="{{route('admin.item.view',[$item['id'],'module_id'=>$item['module_id']])}}">
                                <img class="avatar avatar-lg mr-3 onerror-image"

                                 src="{{ $item['image_full_url'] ?? asset('public/assets/admin/img/160x160/img2.jpg') }}"

                                 data-onerror-image="{{asset('public/assets/admin/img/160x160/img2.jpg')}}" alt="{{$item->name}} image">
                                <div class="media-body">
                                    <h5 class="text-hover-primary mb-0 max-width-200px word-break line--limit-2">{{$item['name']}}</h5>
                                </div>
                            </a>
                        </td>
                        <td>
                            @if($item->store)
                            {{Str::limit($item->store?->name,25,'...')}}
                            @else
                            {{translate('messages.store_deleted')}}
                            @endif
                        </td>
                        <td>
                            @if($item->store)
                            {{$item->store->zone?->name}}
                            @else
                            {{translate('messages.not_found')}}
                            @endif
                        </td>
                        <td>
                            {{ $item->stock>=0?$item->stock:0 }}
                        </td>
                        <td>
                            <a class="btn action-btn btn--primary btn-outline-primary update-quantity" href="javascript:" title="{{translate('messages.edit_quantity')}}" data-id="{{ $item->id }}" data-toggle="modal" data-target="#update-quantity"><i class="tio-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if(count($items) !== 0)
            <hr>
            @endif
            <div class="page-area">
                {!! $items->links() !!}
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
@endsection




@push('script_2')



<script>
    "use strict";
    $('.update-quantity').on('click', function (){
        let val = $(this).data('id');
        $.get({
            url: '{{url('/')}}/admin/item/get-variations?id='+val,
            dataType: 'json',
            success: function (data) {

                $('.rest-part').empty().html(data.view);
            },
        });
    })



    $(document).on('keyup', '.update_qty', function () {
        update_qty()
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

    $(document).on('ready', function() {
        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{url('/')}}/admin/store/get-stores',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        // all:true,
                        @if(isset($zone))
                            zone_ids: [{{$zone->id}}],
                        @endif
                        @if(request('module_id'))
                        module_id: {{request('module_id')}}
                        ,
                        @endif
                        page: params.page
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
    });

        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.transactions.report.low-stock-search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
</script>


@endpush
