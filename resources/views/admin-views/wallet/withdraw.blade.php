@extends('layouts.admin.app')

@section('title',translate('Withdraw Request'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Heading -->
        <div class="page-header">
            <h1 class="page-header-title mr-3 mb-md-0">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/icons/wallet.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{ translate('messages.store_withdraw_transaction')}}
                </span>
            </h1>
        </div>
        <!-- Page Heading -->
        <div class="card mt-2">

            <!-- Header -->
            <div class="card-header flex-wrap py-2 border-0">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h4 class="mb-0">{{ translate('messages.transaction_History')}}</h4>
                    <span class="badge badge-soft-dark rounded-circle">{{$withdraw_req->total()}}</span>
                </div>
                <div class="search--button-wrapper justify-content-end">
                    <form class="search-form theme-style">

                        <div class="input-group input--group">
                            <input id="datatableSearch" name="search" type="search" value="{{ request()?->search ?? null}}" class="form-control h--40px" placeholder="{{translate('ex_:_search_store_name')}}" aria-label="{{translate('messages.search_here')}}">
                            <button type="submit" class="btn btn--secondary h--40px"><i class="tio-search"></i></button>
                        </div>

                    </form>
                    @if(request()->get('search'))
                    <button type="reset" class="btn btn--primary ml-2 location-reload-to-base" data-url="{{url()->full()}}">{{translate('messages.reset')}}</button>
                    @endif


                    <div class="max-sm-flex-1">
                        <select name="withdraw_status_filter" class="custom-select h--40px py-0 status-filter theme-style">
                            <option
                                value="all" {{session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'all'?'selected':''}}>
                                {{translate('messages.all')}}
                            </option>
                            <option
                                value="approved" {{session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'approved'?'selected':''}}>
                                {{translate('messages.approved')}}
                            </option>
                            <option
                                value="denied" {{session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'denied'?'selected':''}}>
                                {{translate('messages.denied')}}
                            </option>
                            <option
                                value="pending" {{session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'pending'?'selected':''}}>
                                {{translate('messages.pending')}}
                            </option>

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
                            <a id="export-excel" class="dropdown-item" href="{{route('admin.transactions.store.withdraw_export', ['type'=>'excel',request()->getQueryString()])}}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="{{route('admin.transactions.store.withdraw_export', ['type'=>'csv',request()->getQueryString()])}}">
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
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="datatable"
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                        <tr>
                            <th class="border-0">{{translate('SL')}}</th>
                            <th class="border-0">{{translate('messages.amount')}}</th>
                            <th class="border-0">{{ translate('messages.store') }}</th>
                            <th class="border-0">{{translate('messages.request_time')}}</th>
                            <th class="border-0">{{translate('messages.status')}}</th>
                            <th class="border-0">{{translate('messages.action')}}</th>
                        </tr>
                        </thead>
                        <tbody id="set-rows">
                        @foreach($withdraw_req as $k=>$wr)
                            <tr>
                                <td scope="row">{{$k+$withdraw_req->firstItem()}}</td>
                                <td>{{\App\CentralLogics\Helpers::format_currency($wr['amount'])}}</td>
                                <td>
                                    @if($wr->vendor)
                                    <a class="deco-none" title="{{ $wr->vendor->stores[0]->name }}"
                                        href="{{route('admin.store.view',[$wr->vendor->stores[0]->id,'module_id'=>$wr->vendor->stores[0]->module_id])}}">{{ Str::limit($wr->vendor->stores[0]->name, 20, '...') }}</a>
                                    @else
                                    {{translate('messages.store deleted!') }}
                                    @endif
                                </td>
                                <td>  {{ \App\CentralLogics\Helpers::time_date_format($wr->created_at) }} </td>
                                <td>
                                    @if($wr->approved==0)
                                        <label class="badge badge-soft-primary">{{ translate('messages.pending') }}</label>
                                    @elseif($wr->approved==1)
                                        <label class="badge badge-soft-success">{{ translate('messages.approved') }}</label>
                                    @else
                                        <label class="badge badge-soft-danger">{{ translate('messages.denied') }}</label>
                                    @endif
                                </td>
                                <td>
                                    @if($wr->vendor)

                                    <a href="#"
                                       data-id="{{$wr->id}}"
                                        class="btn action-btn btn--warning btn-outline-warning withdraw-info-show"><i class="tio-visible-outlined"></i>
                                    </a>
                                    @else
                                    {{translate('messages.store_deleted') }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if(count($withdraw_req) !== 0)
            <hr>
            @endif
            <div class="page-area">
                {!! $withdraw_req->links() !!}
            </div>
            @if(count($withdraw_req) === 0)
            <div class="empty--data">
                <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                <h5>
                    {{translate('no_data_found')}}
                </h5>
            </div>
            @endif
        </div>
    </div>


    <div class="withdraw-info-sidebar-wrap">
        <div class="withdraw-info-sidebar-overlay"></div>
        <div class="withdraw-info-sidebar">
            <div class="d-flex pb-3">
                <span class="circle bg-light withdraw-info-hide cursor-pointer">
                    <i class="tio-clear"></i>
                </span>
            </div>

             <div id="data-view">

             </div>

        </div>
    </div>
@endsection

@push('script_2')
    <script>
        "use strict";
        $('.withdraw-info-hide, .withdraw-info-sidebar-overlay').on('click', function () {
            $('.withdraw-info-sidebar, .withdraw-info-sidebar-overlay').removeClass('show');
        });

        $(document).on('click', '.withdraw-info-show', function () {
            let id = $(this).data('id');
            fetch_data(id)
        })


        $(document).on('click', '.show-approve-view', function () {
            let id = $(this).data('id');
            let url = "{{ route('admin.transactions.store.withdraw_status', ['data_id']) }}";
            url = url.replace('data_id', id);
            let htmlContent = `
            <form  class="withdraw_status_form" action="${url}" method="POST">
                    @csrf
                <div class="mt-5">
                    <h5 class="font-semibold text-center mb-3">{{translate('approval_note')}} </h5>
                    <textarea required name="note" id="" class="form-control" rows="6" maxlength="200" placeholder="{{translate('Type_a_note_about_request_approval')}}"></textarea>
                    <input name="approved" value="1" type="hidden">
                    <div class="mt-4 d-flex justify-content-center gap-3">
                        <button type="button"  data-id="${id}" class="btn btn-soft-secondary min-w-100px withdraw-info-show">
                            <i class="tio-arrow-backward"></i>
                            {{translate('back')}}
                        </button>
                        <button type="submit" class="btn btn-success set_disable min-w-100px">{{translate('complete')}}</button>
                    </div>
                </div>
              </form>`
            $('#data-view').empty().html(htmlContent);
        });

        $(document).on('click', '.show-deny-view', function () {
            let id = $(this).data('id');
            let url = "{{ route('admin.transactions.store.withdraw_status', ['data_id']) }}";
            url = url.replace('data_id', id);
            let htmlContent = `
            <form class="withdraw_status_form" action="${url}" method="POST">
                    @csrf
                <div class="mt-5">
                    <h5 class="font-semibold text-center mb-3">{{translate('denial_note')}} </h5>
                    <textarea required name="note" id="" class="form-control" rows="6" placeholder="{{translate('Type_a_note_about_request_denial')}}"></textarea>
                    <input name="approved" value="2" type="hidden">
                    <div class="mt-4 d-flex justify-content-center gap-3">
                        <button type="button"  data-id="${id}" class="btn btn-soft-secondary min-w-100px withdraw-info-show">
                            <i class="tio-arrow-backward"></i>
                            {{translate('back')}}
                        </button>
                        <button type="submit" class="btn btn-success set_disable min-w-100px">{{translate('complete')}}</button>
                    </div>
                </div>
              </form>`
            $('#data-view').empty().html(htmlContent);
        });

        $('.status-filter').on('change',function () {
            let type = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.transactions.store.status-filter')}}',
                data: {
                    withdraw_status_filter: type
                },
                beforeSend: function () {
                    $('#loading').show()
                },
                success: function (data) {
                    console.log(data)
                    location.reload();
                },
                complete: function () {
                    // $('#loading').hide()
                }
            });
        })

        function fetch_data(id) {
            $.ajax({
                url: "{{ route('admin.transactions.store.getWithdrawDetails') }}" + '?withdraw_id=' + id,
                type: "get",
                beforeSend: function () {
                    $('#data-view').empty();
                    $('#loading').show()
                },
                success: function(data) {
                    $('.withdraw-info-sidebar, .withdraw-info-sidebar-overlay').addClass('show');
                    $("#data-view").append(data.view);
                },
                complete: function () {
                    $('#loading').hide()
                }
            })
        }

$(document).on('submit', '.withdraw_status_form', function (event) {
    $(this).find('button[type="submit"]').attr('disabled', true);
});

    </script>
@endpush

