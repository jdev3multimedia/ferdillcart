@extends('layouts.vendor.app')

@section('title', translate('messages.settings'))

@push('css_or_js')
<link rel="stylesheet" href="{{asset('public/assets/admin/css/custom.css')}}">
<link rel="stylesheet" href="{{asset('public/assets/admin/css/upload-single-image.css')}}">
@endpush


@section('content')
    <div class="content container-fluid config-inline-remove-class">
        <!-- Page Heading -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/config.png') }}" class="w--30" alt="">
                </span>
                <span>
                    {{ translate('messages.store_setup') }}
                </span>
            </h1>
        </div>
        <!-- Page Heading -->
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="d-flex flex-row justify-content-between align-items-center">
                    <h4 class="card-title align-items-center d-flex">
                        <img src="{{ asset('public/assets/admin/img/store.png') }}" class="w--20 mr-1" alt="">
                        <span>{{ translate('messages.store_temporarily_closed_title') }}</span>
                    </h4>
                    <label class="switch toggle-switch-lg m-0" for="restaurant-open-status">
                        <input type="checkbox" id="restaurant-open-status"
                            class="toggle-switch-input restaurant-open-status" {{ $store->active ? '' : 'checked' }}>
                        <span class="toggle-switch-label">
                            <span class="toggle-switch-indicator"></span>
                        </span>
                    </label>
                </div>
            </div>
        </div>


        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon">
                        <i class="tio-settings-outlined"></i>
                    </span>
                    <span>
                        {{ translate('messages.store_settings') }}
                    </span>
                </h5>
            </div>
            <form action="" method="get">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                            <div class="">
                                <label
                                    class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control"
                                    for="schedule_order">
                                    <span class="pr-2">{{ translate('messages.scheduled_order') }}<span
                                            class="input-label-secondary" data-toggle="tooltip" data-placement="right"
                                            data-original-title="{{ translate('When_enabled,_store_owner_can_take_scheduled_orders_from_customers.') }}"><img
                                                src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                                alt="{{ translate('messages.scheduled_order_hint') }}"></span></span>
                                    <input type="checkbox" class="toggle-switch-input redirect-url "
                                        data-url="{{ route('vendor.business-settings.toggle-settings', [$store->id, $store->schedule_order ? 0 : 1, 'schedule_order']) }}"
                                        id="schedule_order" {{ $store->schedule_order ? 'checked' : '' }}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                            <div class="">
                                <label
                                    class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control"
                                    for="delivery">
                                    <span class="pr-2">{{ translate('messages.delivery') }}<span
                                            class="input-label-secondary" data-toggle="tooltip" data-placement="right"
                                            data-original-title="{{ translate('When_enabled,_customers_can_make_home_delivery_orders_from_this_store.') }}"><img
                                                src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                                alt="{{ translate('messages.home_delivery_hint') }}"></span></span>
                                    <input type="checkbox" name="delivery" class="toggle-switch-input redirect-url "
                                        data-url="{{ route('vendor.business-settings.toggle-settings', [$store->id, $store->delivery ? 0 : 1, 'delivery']) }}"
                                        id="delivery" {{ $store->delivery ? 'checked' : '' }}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                            <div class="">
                                <label
                                    class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control"
                                    for="take_away">
                                    <span class="pr-2 text-capitalize">{{ translate('messages.take_away') }}<span
                                            class="input-label-secondary" data-toggle="tooltip" data-placement="right"
                                            data-original-title="{{ translate('When_enabled,_customers_can_place_takeaway_orders_from_this_store.') }}"><img
                                                src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                                alt="{{ translate('messages.take_away_hint') }}"></span></span>
                                    <input type="checkbox" class="toggle-switch-input redirect-url "
                                        data-url="{{ route('vendor.business-settings.toggle-settings', [$store->id, $store->take_away ? 0 : 1, 'take_away']) }}"
                                        id="take_away" {{ $store->take_away ? 'checked' : '' }}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        @if ($store->module->module_type == 'pharmacy')
                            @php($prescription_order_status = \App\Models\BusinessSetting::where('key', 'prescription_order_status')->first())
                            @php($prescription_order_status = $prescription_order_status ? $prescription_order_status->value : 0)
                            @if ($prescription_order_status)
                                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                                    <div class="">
                                        <label
                                            class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control"
                                            for="prescription_order">
                                            <span
                                                class="pr-2 text-capitalize">{{ translate('messages.prescription_order') }}:</span>
                                            <input type="checkbox" class="toggle-switch-input redirect-url"
                                                data-url="{{ route('vendor.business-settings.toggle-settings', [$store->id, $store->prescription_order ? 0 : 1, 'prescription_order']) }}"
                                                id="prescription_order" {{ $store->prescription_order ? 'checked' : '' }}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                        @endif
                        @if ($store->sub_self_delivery == 1)
                            <div class="col-lg-4 col-sm-6">
                                <div class="  m-0">
                                    <label
                                        class="toggle-switch toggle-switch-sm d-flex justify-content-between border rounded px-3 form-control"
                                        for="free_delivery">
                                        <span class="pr-2">
                                            {{ translate('messages.free_delivery') }}
                                            <span data-toggle="tooltip" data-placement="right"
                                                data-original-title="{{ translate('If this option is on, customers will get free delivery') }}"
                                                class="input-label-secondary"><img
                                                    src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                    alt="i"></span>
                                        </span>
                                        <input type="checkbox" name="free_delivery"
                                            class="toggle-switch-input redirect-url"
                                            data-url="{{ route('vendor.business-settings.toggle-settings', [$store->id, $store->free_delivery ? 0 : 1, 'free_delivery']) }}"
                                            id="free_delivery" {{ $store->free_delivery ? 'checked' : '' }}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endif
                        @if ($toggle_veg_non_veg && config('module.' . $store->module->module_type)['veg_non_veg'])
                            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                                <div class="">
                                    <label
                                        class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control"
                                        for="veg">
                                        <span class="pr-2 text-capitalize">{{ translate('messages.veg') }}</span>
                                        <input type="checkbox" class="toggle-switch-input redirect-url"
                                            data-url="{{ route('vendor.business-settings.toggle-settings', [$store->id, $store->veg ? 0 : 1, 'veg']) }}"
                                            id="veg" {{ $store->veg ? 'checked' : '' }}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                                <div class="">
                                    <label
                                        class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control"
                                        for="non_veg">
                                        <span class="pr-2 text-capitalize">{{ translate('messages.non_veg') }}</span>
                                        <input type="checkbox" class="toggle-switch-input redirect-url"
                                            data-url="{{ route('vendor.business-settings.toggle-settings', [$store->id, $store->non_veg ? 0 : 1, 'non_veg']) }}"
                                            id="non_veg" {{ $store->non_veg ? 'checked' : '' }}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endif
                        @if (config('module.' . $store->module->module_type)['cutlery'])
                            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                                <div class="">
                                    <label
                                        class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control"
                                        for="cutlery">
                                        <span class="pr-2 text-capitalize">{{ translate('messages.cutlery') }}</span>
                                        <input type="checkbox" class="toggle-switch-input redirect-url"
                                            data-url="{{ route('vendor.business-settings.toggle-settings', [$store->id, $store->cutlery ? 0 : 1, 'cutlery']) }}"
                                            id="cutlery" {{ $store->cutlery ? 'checked' : '' }}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endif
                        @if (config('module.' . $store->module->module_type)['halal'])
                            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                                <div class="">
                                    <label
                                        class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control"
                                        for="halal_tag_status">
                                        <span class="pr-2 text-capitalize">{{ translate('messages.halal_tag_status') }}

                                            <span data-toggle="tooltip" data-placement="right"
                                                data-original-title="{{ translate('If_enabled,_customers_can_see_halal_tag_on_product') }}"
                                                class="input-label-secondary"><img
                                                    src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                    alt="i"></span>

                                        </span>
                                        <input type="checkbox" class="toggle-switch-input redirect-url"
                                            data-url="{{ route('vendor.business-settings.toggle-settings', [$store->id, $store->storeConfig?->halal_tag_status ? 0 : 1, 'halal_tag_status']) }}"
                                            id="halal_tag_status"
                                            {{ $store->storeConfig?->halal_tag_status ? 'checked' : '' }}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon">
                        <i class="tio-settings-outlined"></i>
                    </span>
                    <span>
                        {{ translate('messages.Store Basic Settings') }}
                    </span>
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('vendor.business-settings.update-setup', [$store['id']]) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row align-items-end g-2">

                        <div class=" col-md-4">
                            <label class="input-label text-capitalize"
                                for="minimum_order">{{ translate('messages.minimum_order_amount') }}<span
                                    class="input-label-secondary" data-toggle="tooltip" data-placement="right"
                                    data-original-title="{{ translate('Specify_the_minimum_order_amount_required_for_customers_when_ordering_from_this_store.') }}"><img
                                        src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                        alt="{{ translate('messages.self_delivery_hint') }}"></span></label>
                            <input type="number" id="minimum_order" name="minimum_order" step="0.01" min="0"
                                max="999999999" class="form-control" placeholder="100"
                                value="{{ $store->minimum_order > 0 ? $store->minimum_order : '' }}">
                        </div>
                        @if (config('module.' . $store->module->module_type)['order_place_to_schedule_interval'])
                            <div class=" col-md-4">
                                <label class="input-label text-capitalize"
                                    for="order_place_to_schedule_interval">{{ translate('messages.minimum_processing_time') }}<span
                                        class="form-label-secondary" data-toggle="tooltip" data-placement="right"
                                        data-original-title="{{ translate('messages.minimum_processing_time_warning') }}"><img
                                            src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                            alt="{{ translate('messages.minimum_processing_time_warning') }}"></span></label>
                                <input type="text" id="order_place_to_schedule_interval"
                                    name="order_place_to_schedule_interval" class="form-control"
                                    value="{{ $store->order_place_to_schedule_interval }}">
                            </div>
                        @endif
                        <div class=" col-md-4">
                            <label class="input-label text-capitalize"
                                for="minimum_delivery_time">{{ translate('messages.approx_delivery_time') }}<span
                                    class="input-label-secondary" data-toggle="tooltip" data-placement="right"
                                    data-original-title="{{ translate('Set_the_total_time_to_deliver_products.') }}"><img
                                        src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                        alt="{{ translate('Set_the_total_time_to_deliver_products.') }}"></span></label>
                            <div class="input-group">
                                <input type="number" id="minimum_delivery_time" name="minimum_delivery_time"
                                    class="form-control" placeholder="Min: 10"
                                    value="{{ explode('-', $store->delivery_time)[0] }}"
                                    title="{{ translate('messages.minimum_delivery_time') }}">
                                <input type="number" name="maximum_delivery_time" class="form-control"
                                    placeholder="Max: 20"
                                    value="{{ explode(' ', explode('-', $store->delivery_time)[1])[0] }}"
                                    title="{{ translate('messages.maximum_delivery_time') }}">
                                <select name="delivery_time_type" class="form-control text-capitalize" required>
                                    <option value="min"
                                        {{ explode(' ', explode('-', $store->delivery_time)[1])[1] == 'min' ? 'selected' : '' }}>
                                        {{ translate('messages.minutes') }}</option>
                                    <option value="hours"
                                        {{ explode(' ', explode('-', $store->delivery_time)[1])[1] == 'hours' ? 'selected' : '' }}>
                                        {{ translate('messages.hours') }}</option>
                                    <option value="days"
                                        {{ explode(' ', explode('-', $store->delivery_time)[1])[1] == 'days' ? 'selected' : '' }}>
                                        {{ translate('messages.days') }}</option>
                                </select>
                            </div>
                        </div>

                        @if ($store->sub_self_delivery)
                            <div class="col-sm-4 col-12">
                                <div class=" ">
                                    <label class="input-label text-capitalize"
                                        for="minimum_shipping_charge">{{ translate('messages.minimum_shipping_charge') }}
                                        ({{ \App\CentralLogics\Helpers::currency_symbol() }})
                                    </label>
                                    <input type="number" id="minimum_shipping_charge" min="0" max="99999999.99"
                                        step="0.01" name="minimum_delivery_charge" class="form-control shipping_input"
                                        value="{{ $store?->minimum_shipping_charge ?? '' }}">
                                </div>
                            </div>

                            <div class="col-sm-4 col-12">
                                <div class="">
                                    <label class="input-label text-capitalize"
                                        for="per_km_delivery_charge">{{ translate('messages.delivery_charge_per_km') }}
                                        ({{ \App\CentralLogics\Helpers::currency_symbol() }})</label>
                                    <input type="number" id="per_km_delivery_charge" name="per_km_delivery_charge"
                                        step="0.01" min="0" max="999999999" class="form-control"
                                        placeholder="100" value="{{ $store->per_km_shipping_charge ?? '0' }}">
                                </div>
                            </div>
                            <div class="col-sm-4 col-12">
                                <div class="">
                                    <label class="input-label text-capitalize"
                                        for="maximum_shipping_charge">{{ translate('messages.maximum_delivery_charge') }}
                                        ({{ \App\CentralLogics\Helpers::currency_symbol() }})
                                        <span data-toggle="tooltip" data-placement="right"
                                            data-original-title="{{ translate('It will add a limite on total delivery charge.') }}"
                                            class="input-label-secondary"><img
                                                src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                                alt="{{ translate('messages.maximum_delivery_charge') }}"></span>
                                    </label>
                                    <input type="number" id="maximum_shipping_charge" name="maximum_shipping_charge"
                                        step="0.01" min="0" max="999999999" class="form-control"
                                        placeholder="10000" value="{{ $store->maximum_shipping_charge ?? '' }}">
                                </div>
                            </div>
                        @endif

                        @if ($store->module->module_type != 'food')
                            <div class="col-sm-4 col-12">
                                <div class="">
                                    <label class="input-label text-capitalize"
                                        for="minimum_stock_for_warning">{{ translate('messages.Minimum_stock_for_warning') }}
                                        <span data-toggle="tooltip" data-placement="right"
                                            data-original-title="{{ translate('When_the_stock_of_a_product_reaches_its_minimum_value_that_you_have_set,_you_will_receive_a_warning_to_update_the_stock._Additionally,_these_products_will_appear_in_the_Admin’s_Low_Stock_list.') }}"
                                            class="input-label-secondary"><img
                                                src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                                alt="{{ translate('messages.Minimum_stock_for_warning') }}"></span>
                                    </label>
                                    <input type="number" id="minimum_stock_for_warning" name="minimum_stock_for_warning"
                                        min="0" max="999999999" class="form-control"
                                        placeholder="{{ translate('messages.Ex: 5') }}"
                                        value="{{ $store?->storeConfig?->minimum_stock_for_warning ?? '' }}">
                                </div>
                            </div>
                        @endif

                        <div class="col-sm-{{ $store->module->module_type != 'food' ? '4' : '6' }} col-12">
                            <div class="">
                                <label class="d-flex justify-content-between switch toggle-switch-sm text-dark"
                                    for="gst_status">
                                    <span>{{ translate('messages.GST') }} <span class="form-label-secondary"
                                            data-toggle="tooltip" data-placement="right"
                                            data-original-title="{{ translate('messages.If GST is enable, GST number will show in invoice') }}"><img
                                                src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                                alt="{{ translate('messages.gst_status') }}"></span></span>
                                    <input type="checkbox" class="toggle-switch-input" name="gst_status" id="gst_status"
                                        value="1" {{ $store->gst_status ? 'checked' : '' }}>
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                                <input type="text" id="gst" name="gst" class="form-control"
                                    value="{{ $store->gst_code }}" {{ isset($store->gst_status) ? '' : 'readonly' }}>
                            </div>
                        </div>

                        @php($extra_packaging_data = \App\Models\BusinessSetting::where('key', 'extra_packaging_data')->first()?->value ?? '')
                        @php($extra_packaging_data = json_decode($extra_packaging_data, true))
                        @if (!empty($extra_packaging_data) && $extra_packaging_data[$store->module->module_type] == '1')
                            <div class="col-sm-{{ $store->module->module_type != 'food' ? '4' : '6' }}">
                                <div class="">
                                    <label class="d-flex justify-content-between switch toggle-switch-sm text-dark"
                                        for="extra_packaging_status">
                                        <span>{{ translate('messages.extra_packaging_charge_amount') }} <span
                                                class="form-label-secondary" data-toggle="tooltip" data-placement="right"
                                                data-original-title="{{ translate('By_enabling_the_status_customer_will_get_the_option_for_choosing_extra_packaging_charge_when_placing_order._for_extra_package_offer') }}"><img
                                                    src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                                    alt="{{ translate('By_enabling_the_status_customer_will_get_the_option_for_choosing_extra_packaging_charge_when_placing_order._for_extra_package_offer') }}"></span></span>
                                        <input type="checkbox" data-id="extra_packaging_status" data-type="status"
                                            data-image-on="{{ asset('/public/assets/admin/img/modal/schedule-on.png') }}"
                                            data-image-off="{{ asset('/public/assets/admin/img/modal/schedule-off.png') }}"
                                            data-title-on="{{ translate('Want_to_enable_extra_packaging_status_for_this_restaurant?') }}"
                                            data-title-off="{{ translate('Want_to_disable_extra_packaging_status_for_this_restaurant?') }}"
                                            data-text-on="<p>{{ translate('If_enabled,_customers_have_to_pay_extra_packaging_charge_on_order') }}"
                                            data-text-off="<p>{{ translate('If_disabled,_customers_do_not_have_to_pay_extra_packaging_charge_on_order.') }}</p>"
                                            class="toggle-switch-input dynamic-checkbox-toggle"
                                            name="extra_packaging_status" value="1" id="extra_packaging_status"
                                            {{ $store->storeConfig?->extra_packaging_status == 1 ? 'checked' : '' }}>

                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                    <input type="number" id="extra_packaging_amount" name="extra_packaging_amount"
                                        step="0.01" min="0" max="9999999999" class="form-control"
                                        placeholder="100"
                                        {{ $store->storeConfig?->extra_packaging_status == 1 ? 'required' : 'readonly' }}
                                        value="{{ $store->storeConfig?->extra_packaging_amount }}">
                                </div>
                            </div>
                        @endif

                        <div class="col-12">
                            <div class="btn--container mt-3 justify-content-end">
                                <button type="reset" class="btn btn--reset">{{ translate('messages.reset') }}</button>
                                <button type="submit"
                                    class="btn btn--primary">{{ translate('messages.update') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon">
                        <img class="w--22" src="{{ asset('public/assets/admin/img/store.png') }}" alt="">
                    </span>
                    <span class="p-md-1"> {{ translate('messages.store_meta_data') }}</span>
                </h5>
            </div>
            @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
            @php($language = $language->value ?? null)
            @php($defaultLang = 'en')
            <div class="card-body">
                <form action="{{ route('vendor.business-settings.update-meta-data', [$store['id']]) }}" method="post"
                    enctype="multipart/form-data" class="col-12">
                    @csrf
                    @include('admin-views.business-settings.landing-page-settings.partial._meta_data', ['submit' => true])
                </form>
            </div>
        </div>
        @if (!config('module.' . $store->module->module_type)['always_open'])
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">
                        <span class="card-header-icon">
                            <i class="tio-date-range"></i>
                        </span>
                        <span>
                            {{ translate('messages.Daily time schedule') }}
                        </span>
                    </h5>
                </div>
                <div class="card-body" id="schedule">
                    @include('vendor-views.business-settings.partials._schedule', $store)
                </div>
            </div>
        @endif
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ translate('messages.Create Schedule For ') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="javascript:" method="post" id="add-schedule">
                            @csrf
                            <input type="hidden" name="day" id="day_id_input">
                            <div class=" ">
                                <label for="recipient-name"
                                    class="col-form-label">{{ translate('messages.Start time') }}:</label>
                                <input type="time" id="recipient-name" class="form-control" name="start_time"
                                    required>
                            </div>
                            <div class=" ">
                                <label for="message-text"
                                    class="col-form-label">{{ translate('messages.End time') }}:</label>
                                <input type="time" id="message-text" class="form-control" name="end_time" required>
                            </div>
                            <div class="btn--container mt-4 justify-content-end">
                                <button type="reset" class="btn btn--reset">{{ translate('messages.reset') }}</button>
                                <button type="submit"
                                    class="btn btn--primary">{{ translate('messages.Submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create schedule modal -->

@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin/js/upload-single-image.js')}}"></script>
    <script>
        "use strict";

        $(document).on('click', '.restaurant-open-status', function(event) {


            event.preventDefault();
            Swal.fire({
                title: '{{ translate('messages.are_you_sure') }}',
                text: '{{ $store->active ? translate('messages.you_want_to_temporarily_close_this_store') : translate('messages.you_want_to_open_this_store') }}',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#00868F',
                cancelButtonText: '{{ translate('messages.no') }}',
                confirmButtonText: '{{ translate('messages.yes') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {

                    $.get({
                        url: '{{ route('vendor.business-settings.update-active-status') }}',
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            $('#loading').show();
                        },
                        success: function(data) {
                            toastr.success(data.message);
                        },
                        complete: function() {
                            location.reload();
                            $('#loading').hide();
                        },
                    });
                } else {
                    event.checked = !event.checked;
                }
            })

        });



        $(document).on('click', '.delete-schedule', function() {
            let route = $(this).data('url');
            Swal.fire({
                title: '{{ translate('Want_to_delete_this_schedule?') }}',
                text: '{{ translate('If_you_select_Yes,_the_time_schedule_will_be_deleted.') }}',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#00868F',
                cancelButtonText: '{{ translate('messages.no') }}',
                confirmButtonText: '{{ translate('messages.yes') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.get({
                        url: route,
                        beforeSend: function() {
                            $('#loading').show();
                        },
                        success: function(data) {
                            if (data.errors) {
                                for (let i = 0; i < data.errors.length; i++) {
                                    toastr.error(data.errors[i].message, {
                                        CloseButton: true,
                                        ProgressBar: true
                                    });
                                }
                            } else {
                                $('#schedule').empty().html(data.view);
                                toastr.success(
                                    '{{ translate('messages.Schedule removed successfully') }}', {
                                        CloseButton: true,
                                        ProgressBar: true
                                    });
                            }
                        },
                        error: function() {
                            toastr.error('{{ translate('messages.Schedule not found') }}', {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        },
                        complete: function() {
                            $('#loading').hide();
                        },
                    });
                }
            })
        });


        function readURL(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#customFileEg1").change(function() {
            readURL(this);
        });

        $(document).on('ready', function() {
            $("#gst_status").on('change', function() {
                if ($("#gst_status").is(':checked')) {
                    $('#gst').removeAttr('readonly');
                } else {
                    $('#gst').attr('readonly', true);
                }
            });
        });

        $('#exampleModal').on('show.bs.modal', function(event) {
            let button = $(event.relatedTarget);
            let day_name = button.data('day');
            let day_id = button.data('dayid');
            let modal = $(this);
            modal.find('.modal-title').text('{{ translate('messages.Create Schedule For ') }} ' + day_name);
            modal.find('.modal-body input[name=day]').val(day_id);
        })

        $('#add-schedule').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{ route('vendor.business-settings.add-schedule') }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(data) {
                    if (data.errors) {
                        for (let i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        $('#schedule').empty().html(data.view);
                        $('#exampleModal').modal('hide');
                        toastr.success('{{ translate('messages.Schedule added successfully') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                },
                error: function(XMLHttpRequest) {
                    toastr.error(XMLHttpRequest.responseText, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                complete: function() {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush
