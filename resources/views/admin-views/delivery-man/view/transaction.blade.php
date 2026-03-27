@extends('layouts.admin.app')

@section('title', translate('messages.Delivery Man Preview'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            @include('admin-views.delivery-man.partials._page_header')
            <div class="">
                @include('admin-views.delivery-man.partials._tab_menu')
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card mb-3 mb-lg-5">
            <div class="card-header flex-wrap pt-3 pb-0 border-0 gap-2">
                <div class="search--button-wrapper">
                    <h4 class="card-title fs-16 text-dark">{{ translate('messages.order_transactions')}}</h4>
                    <!-- <div class="min--260">
                                                    <input type="date" class="form-control set-filter" placeholder="{{ translate('mm/dd/yyyy') }}" data-url="{{route('admin.users.delivery-man.preview',['id'=>$deliveryMan->id, 'tab'=> 'transaction'])}}" data-filter="date" value="{{$date}}">
                                                </div> -->
                    <form class="search-form min--260">
                        <div class="input-group input--group">
                            <input id="datatableSearch_" type="search" name="search" class="form-control h--40px text-muted"
                                placeholder="{{ translate('messages.Search Order ID') }}" value="{{ request()->search }}"
                                aria-label="Search" tabindex="1">

                            <button type="submit" class="btn btn--secondary bg-modal-btn"><i
                                    class="tio-search text-muted"></i></button>
                        </div>
                    </form>
                    <button type="button"
                        class="btn btn--primary h-40px btn-outline-primary py-2 offcanvas-trigger position-relative"
                        data-target="#transaction__list">
                        <i class="tio-tune-horizontal"></i>
                        {{ translate('messages.Filter') }}
                        @if(request()->input('date_range') && request()->input('date_range') != 'all_time')
                            <span class="badge-danger rounded-circle position-absolute"
                                style="top: -3px; right: -3px; width: 10px; height: 10px; padding: 0;"></span>
                        @endif
                    </button>
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
                        <a id="export-excel" class="dropdown-item"
                            href="{{route('admin.users.delivery-man.earning-export', ['type' => 'excel', 'id' => $deliveryMan->id, request()->getQueryString()])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                src="{{ asset('public/assets/admin') }}/svg/components/excel.svg" alt="Image Description">
                            {{ translate('messages.excel') }}
                        </a>
                        <a id="export-csv" class="dropdown-item"
                            href="{{route('admin.users.delivery-man.earning-export', ['type' => 'csv', 'id' => $deliveryMan->id, request()->getQueryString()])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                alt="Image Description">
                            .{{ translate('messages.csv') }}
                        </a>
                    </div>
                </div>
                <!-- End Unfold -->
            </div>
            <!-- Body -->
            <div class="p-xxl-20 p-3">
                <div class="shadow-sm rounded">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="datatable"
                                class="table table-borderless table-thead-bordered table-nowrap justify-content-between table-align-middle card-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="border-0">{{translate('sl')}}</th>
                                        <th class="border-0">{{translate('messages.order_id')}}</th>
                                        <th class="border-0">{{translate('messages.date')}}</th>
                                        <th class="border-0">{{translate('messages.delivery_fee_earned')}}</th>
                                        <th class="border-0">{{translate('messages.delivery_tips')}}</th>
                                        <th class="border-0">{{translate('messages.total_amount')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @php($digital_transaction = \App\Models\OrderTransaction::where('delivery_man_id',
                                    $deliveryMan->id)
                                    ->when($date, function($query)use($date){
                                    return $query->whereDate('created_at', $date);
                                    })->paginate(25)) --}}
                                    @foreach($digital_transaction as $k => $dt)

                                        <tr>
                                            <td scope="row">{{$k + $digital_transaction->firstItem()}}</td>
                                            <td class="w--1"><a
                                                    class="line--limit-1 fs-14 text-dark max-w--220px min-w-135px text-wrap"
                                                    href="{{route((isset($dt->order) && $dt->order->order_type == 'parcel') ? 'admin.parcel.order.details' : 'admin.order.details', [$dt->order_id, 'module_id' => $dt->order->module_id])}}">{{$dt->order_id}}</a>
                                            </td>
                                            <td> {{\App\CentralLogics\Helpers::date_format($dt->created_at)}}</td>
                                            <td>{{ \App\CentralLogics\Helpers::format_currency($dt->original_delivery_charge) }}
                                            </td>
                                            <td>{{ \App\CentralLogics\Helpers::format_currency($dt->dm_tips) }}</td>
                                            <td>{{ \App\CentralLogics\Helpers::format_currency($dt->original_delivery_charge + $dt->dm_tips) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- End Body -->
                    <div class="card-footer">
                        {!!$digital_transaction->links()!!}
                    </div>
                    @if (count($digital_transaction) === 0)
                            <div class="empty--data">
                                <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}"
                                    alt="public">
                                <h5>
                                    {{ translate('no_data_found') }}
                                </h5>
                            </div>
                        @endif
                </div>
            </div>
        </div>
        <!-- End Card -->
    </div>


    <div id="offcanvasOverlay" class="offcanvas-overlay"></div>
    <div id="transaction__list" class="custom-offcanvas d-flex flex-column justify-content-between">
        <div>
            <form
                action="{{ route('admin.users.delivery-man.preview', ['id' => $deliveryMan->id, 'tab' => 'transaction']) }}"
                method="get">
                <div
                    class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                    <h3 class="mb-0">{{ translate('messages.Filter') }}</h2>
                        <button type="button"
                            class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"
                            aria-label="Close">&times;</button>
                </div>
                <div class="custom-offcanvas-body p-20">
                    @include('admin-views.partials._date-range')
                </div>
        </div>
        <div class="offcanvas-footer p-3 d-flex align-items-center justify-content-center gap-3">
            <button type="reset" class="btn w-100 btn--reset h--40px redirect-url"
                data-url="{{ route('admin.users.delivery-man.preview', ['id' => $deliveryMan->id, 'tab' => 'transaction']) }}">{{ translate('messages.reset') }}</button>
            <button type="submit" class="btn w-100 btn--primary h--40px">{{ translate('messages.Filter') }}</button>
        </div>
        </form>
    </div>
@endsection

