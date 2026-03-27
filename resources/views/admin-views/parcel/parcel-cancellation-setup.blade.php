@extends('layouts.admin.app')

@section('title', translate('Parcel Cancellation Setup'))
@section('parcel_cancellation')
    active
@endsection


@section('content')
    <div id="content-disable" class="content container-fluid">
        <div class="d-flex align-items-center mb-20 gap-2">
            <img width="22" height="22" src="{{ asset('public/assets/admin/img/parcel-cancellation-setup.png') }}"
                alt="cencellation-icon">
            <h2 class="mb-0 fs-24 lh-base">{{ translate('Parcel Cancellation Setup') }}</h2>
        </div>
        <div class="card mb-20">
            <div class="card-header rounded-10 flex-sm-nowrap flex-wrap gap-2">
                <div class="max-w-700">
                    <h4 class="mb-1 text-title">{{ translate('Parcel Cancellation Feature') }}</h4>
                    <p class="fs-12 m-0 text-title">
                        {{ translate('Enable and configure cancellation rules that apply after parcel pickup.') }}</p>
                </div>
                {{-- <label class="toggle-switch toggle-switch-sm">
                    <input type="checkbox" data-id="parcel_cancellation_status"
                        {{ $parcel_cancellation_status == 1 ? 'checked' : '' }}
                        data-image-off="{{ asset('public/assets/admin/img/off-danger.png') }}"
                        data-image-on="{{ asset('public/assets/admin/img/on-theme.png') }}"
                        data-title-on="<strong>{{ translate('Are you sure you want to enable the Parcel Cancellation feature?') }}</strong>"
                        data-title-off="<strong>{{ translate('Are you sure you want to Disable the Parcel Cancellation feature?') }}</strong>"
                        data-text-on="<p>{{ translate('If enabled, both customers and deliverymen can cancel delivery orders') }}</p>"
                        data-text-off="<p>{{ translate('If disable, both customers and deliverymen can’t cancel delivery orders') }}</p>"
                        class="status toggle-switch-input dynamic-checkbox" name="parcel_cancellation_status"
                        id="parcel_cancellation_status">

                    <span class="toggle-switch-label text mb-0">
                        <span class="toggle-switch-indicator"></span>
                    </span>
                    <form id="parcel_cancellation_status_form"
                        action="{{ route('admin.parcel.cancellationSettingsStatus') }}" method="get"> </form>

                </label> --}}
            </div>


            @if ($parcel_cancellation_status)
                <form action="{{ route('admin.parcel.cancellationSettingsUpdate') }}" method="post">
                    @csrf
                    @method('put')
                    <div class="card-body">
                        <div class="d-flex flex-column gap-20px">
                            <div class="row align-items-center g-3">
                                <div class="col-lg-4 col-md-5">
                                    <div class="max-w-353px">
                                        <h4 class="mb-1 text-title">{{ translate('Basic Setup') }}</h4>
                                        <p class="fs-12 m-0 color-758590">
                                            {{ translate('Setup additional delivery cancelation fee & return fee for customer and rider.') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-lg-8 col-md-7">
                                    <div class="__bg-FAFAFA rounded p--20">
                                        <div class="row g-3">

                                            <div class="col-sm-12">
                                                <div class="form-group m-0">
                                                    <div
                                                        class="d-flex align-items-center gap-1 justify-content-between flex-wrap mb-2">
                                                        <label for=""
                                                            class="fs-14 mb-0 color-222324">{{ translate('Return Fee (%)') }}
                                                         <span class="text-danger">* </span>
                                                        </label>
                                                        <label class="toggle-switch toggle-switch-sm-extra">
                                                            <input id="return_fee_status" type="checkbox" value='1'
                                                                name="return_fee_status" class="status toggle-switch-input"
                                                                {{ $parcel_cancellation_basic_setup['return_fee_status'] ?? null ? 'checked' : '' }}>
                                                            <span class="toggle-switch-label text mb-0">
                                                                <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <input type="number" name='return_fee'
                                                        {{ $parcel_cancellation_basic_setup['return_fee_status'] ?? null ? 'required' : '' }}
                                                        id="return_fee"
                                                        value="{{ $parcel_cancellation_basic_setup['return_fee'] ?? '' }}"
                                                        min="0" max="100" step="0.01"
                                                        class="form-control bg-white {{ $parcel_cancellation_basic_setup['return_fee_status'] ?? null ? '' : 'disabled' }}"
                                                        placeholder="Ex: 10">
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <label for="" class="fs-14 color-222324 mb-2">
                                                    {{ translate('If Delivery Man Cancels the Delivery After Pickup ') }}
                                                </label>
                                                <div class="d-flex bg-white min-h-45px rounded border w-100 py-2 px-3 flex-wrap flex-sm-nowrap gap-2">
                                                    <div class="d-flex align-item-center cursor-pointer w-100">
                                                        <div class="form-check m-0">
                                                            <input class="form-check-input checkbox-theme-16 single-select"
                                                                {{ $parcel_cancellation_basic_setup['do_not_charge_return_fee_on_deliveryman_cancel'] ?? null ? 'checked' : '' }}
                                                                type="checkbox" value="1"
                                                                name="do_not_charge_return_fee_on_deliveryman_cancel"
                                                                id="cancalation_address_">
                                                        </div>
                                                        <label class="form-check-label mb-0 ml-2 fs-14 lh-24" for="cancalation_address_">
                                                            {{ translate('Do not charge any return fee to customer ') }}
                                                        </label>
                                                    </div>
                                                    
                                                    <!-- <div class="d-flex align-item-center justify-content-between cursor-pointer">
                                                        <div class="form-check m-0">
                                                            <input class="form-check-input checkbox-theme-16 single-select"
                                                                {{ $parcel_cancellation_basic_setup['do_not_charge_return_fee_on_deliveryman_cancel'] ?? null ? 'checked' : '' }}
                                                                type="checkbox" value="1"
                                                                name="do_not_charge_return_fee_on_deliveryman_cancel"
                                                                id="cancalation_address_">
                                                        </div>
                                                        <label class="form-check-label mt-1 mb-0 ml-2 fs-14 " for="cancalation_address_">
                                                            {{ translate('Do not charge any return fee to customer if deliveryman cancel the order after pickup') }}
                                                        </label>
                                                    </div> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-center g-3">
                                <div class="col-lg-4 col-md-5">
                                    <div class="max-w-353px">
                                        <h4 class="mb-1 text-title">{{ translate('Parcel Return Time & Fee') }}</h4>
                                        <p class="fs-12 m-0 color-758590">
                                            {{ translate('When the toggle is turned ON the parcel return time and fee are activated for rider.') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-lg-8 col-md-7">
                                    <div class="__bg-FAFAFA rounded p--20">
                                        <div class="row g-3">
                                            <div class="col-sm-12">
                                                <div
                                                    class="d-flex align-items-center justify-content-between bg-white border px-3 py-2 rounded h-45px">
                                                    <label for=""
                                                        class="fs-14 fs-14 w-100 mb-0">{{ translate('Status') }}</label>
                                                    <label class="toggle-switch toggle-switch-sm-extra">
                                                        <input id="return_time_fee_status" name='status' type="checkbox"
                                                            class="status toggle-switch-input" value='1'
                                                            {{ $parcel_return_time_fee['status'] ?? null ? 'checked' : '' }}>
                                                        <span class="toggle-switch-label text mb-0">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-sm-6 col-md-12 col-lg-6">
                                                <div class="form-group m-0">
                                                    <label for="" class="fs-14 mb-2 color-222324">
                                                        {{ translate('Set Time') }}
                                                        <span class="fs-12 color-A7A7A7" data-toggle="tooltip"
                                                            data-placement="top"
                                                            data-original-title="{{ translate('Define the time period during which customers have to return a parcel') }}">
                                                            <i class="tio-info"></i>
                                                        </span>
                                                        <span class="text-danger">* </span>
                                                    </label>
                                                    <div class="d-flex align-items-center border rounded overflow-hidden">
                                                        <input type="number" name='parcel_return_time'
                                                            value="{{ $parcel_return_time_fee['parcel_return_time'] ?? '' }}"
                                                            min="0"
                                                            class="form-control disableClass bg-white border-0 rounded-0 {{ $parcel_return_time_fee['status'] ?? null ? '' : 'disabled' }} "
                                                            placeholder="Ex: 10">

                                                        <select name="return_time_type" id=""
                                                            class="custom-select bg-F3F4F5 w-auto border-0 rounded-0  disableClass {{ $parcel_return_time_fee['status'] ?? null ? '' : 'disabled' }}">
                                                            <option selected value="day">{{ translate('Day') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-12 col-lg-6">
                                                <div class="form-group m-0">
                                                    <label for="" class="fs-14 mb-2 color-222324">
                                                        {{ translate('Return Fee for Driver if Time Exceeds ') }}
                                                        ({{ \App\CentralLogics\Helpers::currency_symbol() }})
                                                        <span class="fs-12 color-A7A7A7" data-toggle="tooltip"
                                                            data-placement="top"
                                                            data-original-title="{{ translate('Set the return fee that will be applied if the allowed time period is exceeded') }}">
                                                            <i class="tio-info"></i>
                                                            <span class="text-danger">* </span>
                                                        </span>
                                                    </label>
                                                    <input type="number" name="return_fee_for_dm"
                                                        value="{{ $parcel_return_time_fee['return_fee_for_dm'] ?? '' }}"
                                                        min="0" max="999999999" step="0.01"
                                                        class="form-control bg-white disableClass {{ $parcel_return_time_fee['status'] ?? null ? '' : 'disabled' }}"
                                                        placeholder="Ex: 10">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btn--container justify-content-end mt-4">
                            <button type="reset"
                                class="btn min-w-120px btn--reset">{{ translate('messages.reset') }}</button>
                            <button type="submit"
                                class="btn min-w-120px btn--primary">{{ translate('messages.save') }}</button>
                        </div>
                    </div>
                </form>
            @endif

        </div>

        <form action="{{ route('admin.parcel.cancellationReason') }}" method="post" class="card mb-20">
            @csrf
            @method('post')
            <div class="card-header flex-sm-nowrap flex-wrap gap-2">
                <h3 class="m-0 text-title">{{ translate('Parcel cancellation reason') }}</h3>
            </div>
            <div class="card-body">
                @if ($language)
                    <ul class="nav nav-tabs border-0 mb-4">
                        <li class="nav-item">
                            <a class="nav-link lang_link active" href="#"
                                id="default-link">{{ translate('messages.default') }}</a>
                        </li>
                        @foreach ($language as $lang)
                            <li class="nav-item">
                                <a class="nav-link lang_link" href="#"
                                    id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif


                <div class="row g-3">
                    <div class="col-sm-6 lang_form" id="default-form">
                        <div class="form-group m-0">
                            <label class="fs-14 mb-2 color-222324">{{ translate('Parcel cancellation reason') }}
                                ({{ translate('Default') }})
                                <span class="fs-12 color-A7A7A7" data-toggle="tooltip"
                                    data-placement="top"
                                    data-original-title="{{ translate('Define the valid reasons for parcel cancellation, which will be shown to customers for selection') }}">
                                    <i class="tio-info"></i>
                                </span>
                                 <span class="text-danger">* </span>
                            </label>
                            <textarea rows="1" name="reason[]" data-target="#char-count"
                                class="form-control min-h-45px bg-white char-counter" maxlength="150" placeholder="Type Tittle"></textarea>
                            <div id="char-count" class="color-A7A7A7 mt-1 fs-14 text-right">0/150</div>
                            <input type="hidden" name="lang[]" value="default">
                        </div>
                    </div>

                    @if ($language)
                        @foreach ($language as $lang)
                            <div class="col-sm-6  lang_form d-none" id="{{ $lang }}-form">
                                <label class="fs-14 mb-2 color-222324 ">{{ translate('Parcel cancellation reason') }}
                                    ({{ strtoupper($lang) }})
                                     <span class="text-danger">* </span>
                                </label>
                                <textarea rows="1" name="reason[]" data-target="#feedback-count-{{ $lang }}"
                                    class="form-control min-h-45px bg-white char-counter" maxlength="150" placeholder="Type Tittle"></textarea>
                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                                <div id="feedback-count-{{ $lang }}" class="color-A7A7A7 mt-1 fs-14 text-right">
                                    0/150</div>
                            </div>
                        @endforeach
                    @endif
                    <div class="col-sm-6 col-lg-3">
                        <div class="form-group m-0">
                            <label for="" class="fs-14 mb-2 color-222324">
                                {{ translate('Cancellation type') }}
                                 <span class="text-danger">* </span>
                            </label>
                            <select name="cancellation_type" required id=""
                                class="custom-select fs-12 title-clr">
                                <option value="" selected disabled>{{ translate('Select Cancellation Type') }}
                                </option>
                                <option value="before_pickup">{{ translate('before_pickup') }}</option>
                                <option value="after_pickup">{{ translate('after_pickup') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="form-group m-0">
                            <label for="" class="fs-14 mb-2 color-222324">
                                {{ translate('User Type') }}
                                 <span class="text-danger">* </span>
                            </label>
                            <select name="user_type" required id="" class="custom-select fs-12 title-clr">
                                <option value="" selected disabled>{{ translate('Select User Type') }}</option>
                                <option value="customer">{{ translate('Customer') }}</option>
                                {{-- <option value="admin">{{ translate('Admin') }}</option> --}}
                                {{-- <option value="vendor">{{ translate('Vendor') }}</option> --}}
                                <option value="deliveryman">{{ translate('Deliveryman') }}</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="btn--container justify-content-end mt-4">
                    <button type="reset" class="btn min-w-120px btn--reset">{{ translate('messages.reset') }}</button>
                    <button type="submit"
                        class="btn min-w-120px btn--primary">{{ translate('messages.submit') }}</button>
                </div>
            </div>
        </form>

        <div class="card border-0">
            <div class="card-header border-0 flex-wrap gap-2">
                <h4 class="title-clr m-0">{{ translate('messages.parcel_cancellation_reason') }}</h4>
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <form class="search-form w-340-lg">
                        <div class="input-group input--group">
                            <input name="search" type="search" class="form-control" placeholder="Search by Reason"
                                value="{{ request()->get('search') ?? '' }}">
                            <button type="submit" class="btn btn--primary"><i class="tio-search"></i></button>
                        </div>
                    </form>
                    <div class="hs-unfold mr-2">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white text-title font-medium dropdown-toggle min-height-40"
                            href="javascript:;"
                            data-hs-unfold-options='{
                                "target": "#usersExportDropdown",
                                "type": "css-animation"
                            }'>
                            <i class="tio-download-to mr-1 text-title"></i> {{ translate('messages.export') }}
                        </a>
                        <div id="usersExportDropdown"
                            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                            <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                            <a id="export-excel" class="dropdown-item"
                                href="{{ route('admin.parcel.cancellationReasonExport', ['type' => 'excel', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item"
                                href="{{ route('admin.parcel.cancellationReasonExport', ['type' => 'csv', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                    alt="Image Description">
                                .{{ translate('messages.csv') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table m-0 table-borderless table-thead-bordered table-nowrap table-align-middle">
                    <thead class="bg-table-head">
                        <tr>
                            <th class="fs-14 text-title font-semibold top-border-table">
                                {{ translate('SL') }}
                            </th>
                            <th class="fs-14 text-title font-semibold top-border-table">
                                {{ translate('messages.reason') }}
                            </th>
                            <th class="fs-14 text-title font-semibold top-border-table">
                                {{ translate('messages.cancellation_type') }}
                            </th>
                            <th class="fs-14 text-title font-semibold top-border-table">
                                {{ translate('messages.user_type') }}
                            </th>
                            <th class="fs-14 text-title font-semibold top-border-table">
                                {{ translate('messages.status') }}
                            </th>
                            <th class="fs-14 text-center text-title font-semibold top-border-table">
                                {{ translate('messages.action') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($cancellationReasons as $key => $item)
                            <tr>
                                <td class="p-3 fs-14 title-clr font-medium">{{ $key + $cancellationReasons->firstItem() }}
                                </td>
                                <td class="p-3">
                                    <div class="max-w-700 fs-14 title-clr font-medium min-w-140">
                                        {{ Str::limit($item->reason, 25, '...') }}
                                    </div>
                                </td>
                                <td class="p-3 fs-14 title-clr font-medium min-w-140">
                                    {{ translate($item->cancellation_type) }}</td>
                                <td class="p-3 fs-14 title-clr font-regular min-w-140">{{ translate($item->user_type) }}
                                </td>
                                <td class="p-3">
                                    <label class="toggle-switch toggle-switch-sm">
                                        <input type="checkbox" class="status toggle-switch-input redirect-url"
                                            data-url="{{ route('admin.parcel.cancellationReasonStatus', [$item->id]) }}"
                                            {{ $item->status == 1 ? 'checked' : '' }}>
                                        <span class="toggle-switch-label text mb-0">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td class="p-3">
                                    <div class="btn--container justify-content-center">

                                        <a class="btn btn-sm text-end action-btn btn-outline-theme-dark text--info info-hover offcanvas-trigger get_data data-info-show"
                                            data-target="#offcanvas__customBtn3" data-id="{{ $item['id'] }}"
                                            data-url="{{ route('admin.parcel.cancellationReasonEdit', [$item['id']]) }}"
                                            href="javascript:" title="{{ translate('messages.edit_reason') }}"><i
                                                class="tio-edit"></i></a>


                                        <a class="btn action-btn btn--danger btn-outline-danger form-alert"
                                            href="javascript:" data-id="reason-{{ $item['id'] }}"
                                            data-message="{{ translate('Want to delete this cancellation reason?') }}"
                                            title="{{ translate('messages.delete_cancellation_reason') }}"><i
                                                class="tio-delete-outlined"></i>
                                        </a>

                                        <form action="{{ route('admin.parcel.cancellationReasonDelete', [$item['id']]) }}"
                                            method="post" id="reason-{{ $item['id'] }}">
                                            @csrf @method('delete')
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
            @if (count($cancellationReasons) !== 0)
                <hr>
            @endif
            <div class="page-area">
                {!! $cancellationReasons->withQueryString()->links() !!}
            </div>
            @if (count($cancellationReasons) === 0)
                <div class="empty--data">
                    <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
                    <h5>
                        {{ translate('no_data_found') }}
                    </h5>
                </div>
            @endif
        </div>
    </div>



    {{-- <!-- Confiramtion Modal -->
    <div class="modal shedule-modal fade" id="confirmation-modal-btn" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content pb-2 max-w-500">
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
                        <h3 class="mb-2">Are you sure?</h3>
                        <p class="mb-0">You ....................</p>
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0 gap-2">
                    <button type="button" class="btn min-w-120px btn--reset" data-dismiss="modal">No</button>
                    <button type="button" class="btn min-w-120px btn--primary">Yes</button>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Parcel Cancellation Modal -->
    {{-- <div class="modal shedule-modal fade" id="cancellation__permission" tabindex="-1"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content pb-2 max-w-500">
                <div class="modal-header">
                    <button type="button"
                        class="close bg-modal-btn w-30px h-30 rounded-circle position-absolute right-0 top-0 m-2 z-2"
                        data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center ">
                        <img src="{{ asset('public/assets/admin/img/off-danger.png') }}" alt="icon" class="mb-3">
                        <h3 class="mb-2 px-xl-4">Are you sure you want to Disable the Parcel Cancellation feature</h3>
                        <p class="mb-0 fs-12 max-w-320 mx-auto">If disable, both customers and deliverymen can’t cancel
                            delivery orders</p>
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0 gap-2">
                    <button type="button" class="btn min-w-120px btn--reset" data-dismiss="modal">No</button>
                    <button type="button" class="btn min-w-120px btn--primary">Yes</button>
                </div>
            </div>
        </div>
    </div> --}}

    <div id="offcanvas__customBtn3" class="custom-offcanvas d-flex flex-column justify-content-between">
        <div id="data-view" class="h-100">
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        initCharCounter();
        $('#return_fee_status').on('click', function() {
            if ($('#return_fee_status').is(':checked')) {
                $('#return_fee').removeClass('disabled').prop('required', true);
            } else {
                $('#return_fee').addClass('disabled').prop('required', false);
            }
        });
        $('#return_time_fee_status').on('click', function() {
            if ($('#return_time_fee_status').is(':checked')) {
                $('.disableClass').removeClass('disabled').prop('required', true);
            } else {
                $('.disableClass').addClass('disabled').prop('required', false);
            }
        });


        $(document).on('click', '.data-info-show', function() {
            let id = $(this).data('id');
            let url = $(this).data('url');
            $('#content-disable').addClass('disabled');
            fetch_data(id, url)
        })

        function fetch_data(id, url) {
            $.ajax({
                url: url,
                type: "get",
                beforeSend: function() {
                    $('#data-view').empty();
                    $('#loading').show()
                },
                success: function(data) {
                    console.log(data);

                    $("#data-view").append(data.view);
                    initLangTabs();
                    initCharCounter();
                    initSelect2Dropdowns();

                },
                complete: function() {
                    $('#loading').hide()
                }
            })
        }


        function initLangTabs() {
            const langLinks = document.querySelectorAll(".lang_link1");
            langLinks.forEach(function(langLink) {
                langLink.addEventListener("click", function(e) {
                    e.preventDefault();
                    langLinks.forEach(function(link) {
                        link.classList.remove("active");
                    });
                    this.classList.add("active");
                    document.querySelectorAll(".lang_form1").forEach(function(form) {
                        form.classList.add("d-none");
                    });
                    let form_id = this.id;
                    let lang = form_id.substring(0, form_id.length - 5);
                    $("#" + lang + "-form1").removeClass("d-none");
                    if (lang === "default") {
                        $(".default-form1").removeClass("d-none");
                    }
                });
            });
        }

        $('.offcanvas-trigger').on('click', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            $(target).addClass('open');
            $('#offcanvasOverlay').addClass('show');

        });

        function initSelect2Dropdowns() {
            $('.offcanvas-close, #offcanvasOverlay').on('click', function() {
                $('.custom-offcanvas').removeClass('open');
                $('#offcanvasOverlay').removeClass('show');
                $('#content-disable').removeClass('disabled');
            });
        }
    </script>
@endpush
