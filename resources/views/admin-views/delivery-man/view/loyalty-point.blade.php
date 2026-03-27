@extends('layouts.admin.app')

@section('title', translate('messages.Delivery Man Preview'))

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


        <div class="card mb-20">
            <div class="card-body">
                <div class="row g-xxl-4 g-3">
                    <div class="col-sm-6 col-lg-4">
                        <div class="card_earning-box theme-bg-opacity10 rounded-10 h-100">
                            <div class="box d-flex align-items-center">
                                <div class="icon w-60px h-60px rounded-circle d-center bg-white">
                                    <img src="{{asset('public/assets/admin/img/t-earning.png')}}" class="w--26" alt="">
                                </div>
                                <div>
                                    <h3 class="text-006AB4 mb-1 fs-26">{{ $total_loyalty_point }}</h3>
                                    <p class="text-dark fs-14 mb-0">{{ translate('messages.Total Earned') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card_earning-box card--bg-3 rounded-10 h-100">
                            <div class="box d-flex align-items-center">
                                <div class="icon w-60px h-60px rounded-circle d-center bg-white">
                                    <img src="{{asset('public/assets/admin/img/t-points.png')}}" class="w--26" alt="">
                                </div>
                                <div>
                                    <h3 class="text-00AA6D mb-1 fs-26">{{ $total_converted_loyalty_point }}</h3>
                                    <p class="text-dark fs-14 mb-0">{{ translate('Points Converted') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card_earning-box color-card color-4 rounded-10 h-100">
                            <div class="box d-flex align-items-center">
                                <div class="icon w-60px h-60px rounded-circle d-center bg-white">
                                    <img src="{{asset('public/assets/admin/img/Create_Cashback_Offer.png')}}" class="w--26"
                                        alt="">
                                </div>
                                <div>
                                    <h3 class="title mb-1 fs-26">{{ $deliveryMan['loyalty_point'] }}</h3>
                                    <p class="text-dark fs-14 mb-0">{{ translate('messages.Current Points in Wallet') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex-wrap pt-3 pb-3 border-0 gap-2">
                <div class="search--button-wrapper mr-1">
                    <h4 class="card-title fs-16 text-dark">{{ translate('messages.Loyalty Point History')}}</h4>
                    <form class="search-form min--260">
                        <div class="input-group input--group">
                            <input id="" type="search" name="search" class="form-control h--40px"
                                placeholder="{{ translate('messages.Search Transaction ID or Type') }}"
                                value="{{ request()->search }}" aria-label="Search" tabindex="1">

                            <button type="submit" class="btn btn--secondary bg-modal-btn"><i
                                    class="tio-search text-muted"></i></button>
                        </div>
                    </form>
                    <button type="button" class="btn btn--primary h-40px btn-outline-primary py-2 offcanvas-trigger"
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
                <div class="hs-unfold">
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
                            href="{{ route('admin.users.delivery-man.loyalty-point-export', ['type' => 'excel', 'id' => $deliveryMan->id, request()->getQueryString()]) }}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                src="{{ asset('public/assets/admin') }}/svg/components/excel.svg" alt="Image Description">
                            {{ translate('messages.excel') }}
                        </a>
                        <a id="export-csv" class="dropdown-item"
                            href="{{ route('admin.users.delivery-man.loyalty-point-export', ['type' => 'csv', 'id' => $deliveryMan->id, request()->getQueryString()]) }}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                alt="Image Description">
                            .{{ translate('messages.csv') }}
                        </a>
                    </div>
                </div>
                <!-- End Unfold -->
            </div>
            <div class="card-body p-0">
                <!-- Table -->
                <div class="table-responsive datatable-custom">
                    <table class="table table-border table-thead-borderless table-align-middle table-nowrap card-table m-0">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0 text-center">{{ translate('SL') }}</th>
                                <th class="border-0">{{ translate('Transaction ID') }}</th>
                                <th class="border-0">{{ translate('Date') }}</th>
                                <th class="border-0">{{ translate('Transaction Type') }}</th>
                                <th class="text-right pr-8 border-0">{{ translate('Points') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($loyalty_points as $key => $loyalty_point)
                                <tr>
                                    <td class="text-center">{{  $key + $loyalty_points->firstItem() }}</td>
                                    <td>
                                        <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                            {{ $loyalty_point->transaction_id }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                            {{ \App\CentralLogics\Helpers::date_format($loyalty_point->created_at) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                            {{ translate($loyalty_point->transaction_type) }}
                                            {{ $loyalty_point->transaction_type == 'converted_to_wallet' ? '(' . \App\CentralLogics\Helpers::currency_symbol() . ')' : ''}}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark text-right pr-6">
                                            {{ $loyalty_point->point_conversion_type == 'credit' ? '+' : '-' }}
                                            {{ $loyalty_point->point }} <br>
                                            @if ($loyalty_point->point_conversion_type == 'credit')
                                                <span type="button"
                                                    class="btn px-3 fs-12 py-1 badge-soft-success">{{ translate('credit') }}</span>
                                            @else
                                                <span type="button"
                                                    class="btn px-3 fs-12 py-1 badge-soft-danger">{{ translate('Debit') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if (count($loyalty_points) === 0)
                        <div class="empty--data">
                            <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
                            <h5>
                                {{translate('no_data_found')}}
                            </h5>
                        </div>
                    @endif
                </div>
                <!-- End Table -->
            </div>
            <div class="page-area px-4 pb-3">
                <div class="d-flex align-items-center justify-content-end">
                    <div>
                        {!! $loyalty_points->withQueryString()->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="transaction__list" class="custom-offcanvas d-flex flex-column justify-content-between">
        <div>
            <form
                action="{{ route('admin.users.delivery-man.preview', ['id' => $deliveryMan->id, 'tab' => 'loyalty-point']) }}"
                method="get">
                <div
                    class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                    <h3 class="mb-0">{{ translate('messages.Filter') }}</h2>
                        <button type="button"
                            class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"
                            aria-label="Close">&times;</button>
                </div>
                <div class="custom-offcanvas-body p-20">
                    <div class="mb-3">
                        <label for="point_conversion_type"
                            class="form-label">{{ translate('messages.Transaction Type') }}</label>
                        <select name="point_conversion_type" id="point_conversion_type" class="form-control js-select2-custom">
                            <option value="" {{ request()->point_conversion_type == '' ? 'selected' : '' }}>
                                {{ translate('messages.both') }}</option>
                            <option value="credit" {{ request()->point_conversion_type == 'credit' ? 'selected' : '' }}>{{ translate('messages.credit') }}</option>
                            <option value="debit" {{ request()->point_conversion_type == 'debit' ? 'selected' : '' }}>
                                {{ translate('messages.debit') }}</option>
                        </select>
                    </div>
                    @include('admin-views.partials._date-range')
                </div>
        </div>
        <div class="offcanvas-footer p-3 d-flex align-items-center justify-content-center gap-3">
            <button type="reset" class="btn w-100 btn--reset h--40px redirect-url"
                data-url="{{ route('admin.users.delivery-man.preview', ['id' => $deliveryMan->id, 'tab' => 'loyalty-point']) }}">{{ translate('messages.reset') }}</button>
            <button type="submit" class="btn w-100 btn--primary h--40px">{{ translate('messages.Filter') }}</button>
        </div>
        </form>
    </div>
    <div id="offcanvasOverlay" class="offcanvas-overlay"></div>


@endsection