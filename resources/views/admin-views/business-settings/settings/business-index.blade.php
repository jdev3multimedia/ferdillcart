@extends('layouts.admin.app')

@section('title', translate('business_setup'))

@section('content')
<div class="content">
    <form class="validate-form" action="{{ route('admin.business-settings.update-setup') }}" method="post" enctype="multipart/form-data">
            @csrf
            @php($name = \App\Models\BusinessSetting::where('key', 'business_name')->first())
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-header-title fs-24 mr-3">
                    <span class="page-header-icon">
                        <img src="{{ asset('public/assets/admin/img/business.png') }}" class="w--26" alt="">
                    </span>
                    <span>
                        {{ translate('business_settings') }}
                    </span>
                </h1>
                @include('admin-views.business-settings.partials.nav-menu')
            </div>
            <!-- End Page Header -->

            <div class="card mb-3" id="maintenance_mode_section">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-xxl-9 col-lg-8 col-md-7 col-sm-6">
                            <div>
                                <h3 class="mb-1">
                                    {{ translate('Maintenance Mode') }}
                                </h3>
                                <p class="mb-0 fs-12">
                                    {{ translate('Turn on the Maintenance Mode will temporarily deactivate your selected systems as of your chosen date and time.') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-lg-4 col-md-5 col-sm-6">
                            <div
                                class="maintenance-mode-toggle-bar d-flex flex-wrap justify-content-between border rounded align-items-center py-2 px-3">
                                @php($config = \App\CentralLogics\Helpers::get_business_settings('maintenance_mode'))
                                <h5 class="text-capitalize m-0 font-weight-normal fs-14 text-dark">
                                    {{ translate('maintenance_mode') }}
                                </h5>
                                <label class="toggle-switch toggle-switch-sm">
                                    <input type="checkbox"
                                    data-id="maintenance_mode"
                                    data-type="toggle"
                                    data-image-on="{{ asset('/public/assets/admin/img/modal/info-warning.png') }}"
                                    data-image-off="{{ asset('/public/assets/admin/img/modal/info-warning.png') }}"
                                    data-title-on="{{ translate('Are you sure to enable Maintenance Mode?') }}"
                                    data-title-off="{{ translate('Are you sure to disable Maintenance Mode?') }}"
                                    data-text-on="{{ translate('This will temporarily disable the selected systems at the scheduled date and time. You can turn it off anytime after maintenance is complete.') }}"
                                    data-text-off="{{ translate('This will make your app and website live. Customers will be able to browse your services and place orders again.') }}"
                                    data-footer-text-on="<div class='text-info'>{{ translate('Note : Don’t forget to save the information before leaving this page.') }}</div>"
                                    data-footer-text-off="<div class='text-info'>{{ translate('Note : Don’t forget to save the information before leaving this page.') }}</div>"
                                    class="status toggle-switch-input dynamic-checkbox-toggle"
                                    id="maintenance_mode"
                                    {{ isset($config) && $config ? 'checked' : '' }}>
                                    <span class="toggle-switch-label text mb-0">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-lg-12">
                    <div class="card" id="basic_information_section">
                        <div class="card-header">
                            <div>
                                <h3 class="mb-1">
                                    {{ translate('Basic Information') }}
                                </h3>
                                <p class="mb-0 fs-12">
                                    {{ translate('Here you setup your all business information.') }}
                                </p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-8 shadow-sm">
                                    <div class="p-xxl-20 p-xl-3 p-2 bg-white">
                                        <div class="row g-3">
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                        for="business_name">{{ translate('Business Name') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input id="business_name" type="text" name="business_name"
                                                        value="{{ $name->value ?? '' }}" class="form-control"
                                                        placeholder="{{ translate('Type your business name') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6">
                                                @php($email_address = \App\Models\BusinessSetting::where('key', 'email_address')->first())
                                                <div class="form-group mb-0">
                                                    <label class="form-label" for="email_address">{{ translate('Email') }}
                                                        <span class="text-danger">*</span></label>
                                                    <input id="email_address" type="email" value="{{ $email_address->value ?? '' }}"
                                                        name="email_address" class="form-control"
                                                        placeholder="{{ translate('Type your email') }}"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6">
                                                @php($phone = \App\Models\BusinessSetting::where('key', 'phone')->first())
                                                <div class="form-group mb-0">
                                                    <label class="form-label" for="phone">{{ translate('Phone') }}
                                                    </label>
                                                    <input type="tel" value="{{ $phone->value ?? '' }}" id="phone"
                                                        name="phone" class="form-control"
                                                        placeholder="{{ translate('Ex: +3264124565') }}" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6">
                                                @php($country = \App\Models\BusinessSetting::where('key', 'country')->first())
                                                <div class="form-group mb-0">
                                                    <label class="form-label text-capitalize"
                                                        for="country">{{ translate('Country') }} <span
                                                            class="text-danger">*</span></label>
                                                    <select id="country" name="country"
                                                        class="form-control  js-select2-custom">
                                                        @foreach (\App\CentralLogics\Helpers::getCountries() as $countryCode => $countryName)
                                                            <option value="{{ $countryCode }}"
                                                                {{ $countryCode == $country->value ? 'selected' : '' }}>
                                                                {{ $countryName }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-12">
                                                @php($address = \App\Models\BusinessSetting::where('key', 'address')->first())
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                        for="address">{{ translate('address') }} <span
                                                            class="text-danger">*</span>
                                                        <span class="" data-toggle="tooltip" data-placement="right"
                                                            data-original-title="The physical location of your business">
                                                            <i class="tio-info text-muted"></i>
                                                        </span>
                                                    </label>
                                                    <textarea type="text" id="address" name="address" class="form-control"
                                                        placeholder="{{ translate('Ex: address') }}" rows="1"
                                                        required>{{ $address->value ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <div class="">
                                                    <div class="position-relative">
                                                        <!-- <div class="d-flex mb-3 fs-12">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13 17H11V11H13V17ZM13 9H11V7H13V9Z"
                                                                    fill="#039D55" />
                                                            </svg>
                                                            <div class="w-0 flex-grow pl-2">
                                                                {{ translate('clicking_on_the_map_will_set_Latitude_and_Longitude_automatically') }}
                                                            </div>
                                                        </div> -->
                                                        <input id="pac-input" class="controls rounded" data-toggle="tooltip"
                                                            data-placement="right"
                                                            data-original-title="{{ translate('search_your_location_here') }}"
                                                            type="text"
                                                            placeholder="{{ translate('search_here') }}" />
                                                        <div id="location_map_canvas"
                                                            class="overflow-hidden rounded height-285px"></div>

                                                        <!-- latlong -->
                                                        <div
                                                            class="lat-long-adjust py-1 px-1 position-absolute bottom-0 mb-2 flex-sm-nowrap flex-wrap rounded bg-white d-flex justify-content-center align-items-center gap-1">
                                                            @php($default_location = \App\Models\BusinessSetting::where('key', 'default_location')->first())
                                                            @php($default_location = $default_location?->value ? json_decode($default_location->value, true) : 0)
                                                            <div class="form-group mb-0">
                                                                <input type="text" id="latitude" name="latitude"
                                                                    class="w-auto border-0 p-0 m-0 text-center"
                                                                    placeholder="{{ translate('Ex:') }} -94.22213"
                                                                    value="{{ $default_location ? $default_location['lat'] : 0 }}"
                                                                    required readonly>
                                                            </div>
                                                            <div class="line"></div>
                                                            <div class="form-group mb-0">
                                                                <input type="text" name="longitude"
                                                                    class="w-auto border-0 p-0 m-0 text-center"
                                                                    placeholder="{{ translate('Ex:') }} 103.344322"
                                                                    id="longitude"
                                                                    value="{{ $default_location ? $default_location['lng'] : 0 }}"
                                                                    required readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php($logo = \App\Models\BusinessSetting::where('key', 'logo')->first())

                                <div class="col-lg-4 shadow-sm">
                                    <div class="d-flex flex-column gap-4 shadow-sm h--37px">
                                        <div class="bg-light2 rounded p-20">
                                            <div class="mb-15">
                                                <h4 class="mb-1">{{ translate('Upload Logo') }} <span class="text-danger">*</span> </h4>
                                                <p class="mb-0 fs-12 gray-dark">
                                                    {{translate('Upload your business logo')}}
                                                </p>
                                            </div>
                                            @include('admin-views.partials._image-uploader', [
                                                    'id' => 'image-input',
                                                    'name' => 'logo',
                                                    'ratio' => '3:1',
                                                    'isRequired' => true,
                                                    'existingImage' => \App\CentralLogics\Helpers::get_full_url('business', $logo?->value ?? '', $logo?->storage[0]?->value ?? 'public', 'upload_image'),
                                                    'imageExtension' => IMAGE_EXTENSION,
                                                    'imageFormat' => IMAGE_FORMAT,
                                                    'maxSize' => MAX_FILE_SIZE,
                                                    'textPosition' => 'bottom',
                                                    ])
                                        </div>
                                        @php($icon = \App\Models\BusinessSetting::where('key', 'icon')->first())

                                        <div class="bg-light2 rounded p-20">
                                            <div class="text-start">
                                                <div class="mb-15">
                                                    <h4 class="mb-1">{{ translate('Favicon') }} <span class="text-danger">*</span> </h4>
                                                    <p class="mb-0 fs-12 gray-dark">
                                                        {{translate('Upload your website favicon')}}
                                                    </p>
                                                </div>
                                                @include('admin-views.partials._image-uploader', [
                                                    'id' => 'image-input',
                                                    'name' => 'icon',
                                                    'ratio' => '1:1',
                                                    'isRequired' => true,
                                                    'existingImage' => \App\CentralLogics\Helpers::get_full_url('business', $icon?->value ?? '', $icon?->storage[0]?->value ?? 'public', 'upload_image'),
                                                    'imageExtension' => IMAGE_EXTENSION,
                                                    'imageFormat' => IMAGE_FORMAT,
                                                    'maxSize' => MAX_FILE_SIZE,
                                                    'textPosition' => 'bottom',
                                                    ])
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="info-notes-bg px-3 py-2 rounded fz-11  gap-2 align-items-center d-flex mt-20">
                                <img src="{{asset('public/assets/admin/img/info-idea.svg')}}" alt="">
                                <span>
                                    {{translate('For the address setup you can simply drag the map to pick for the perfect')}}
                                    <strong class="text-title"> {{translate('Lat(Latitude) & Log(Longitude)')}}</strong>
                                    {{translate('value')}}.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">

                    <div class="card " id="general_settings_section">
                        <div class="card-header">
                            <div>
                                <h3 class="mb-1">
                                    {{ translate('General Setup') }}
                                </h3>
                                <p class="mb-0 fs-12">
                                    {{ translate('Here you can manage time settings to match with your business criteria') }}
                                </p>
                            </div>
                        </div>
                        <div class="card-body py-xxl-4 py-3 px-xxl-4 px-lg-3 px-0">
                            <div class="shadow-sm p-xxl-20 p-xl-3 p-2 bg-white mb-20">
                                <div class="mb-20">
                                    <h4 class="mb-1">
                                        {{ translate('Time Setup') }}
                                    </h4>
                                    <p class="mb-0 fs-12">
                                        {{ translate('Setup your business time zone and format from here') }}
                                    </p>
                                </div>
                                <div class="bg-light2 rounded p-xxl-20 p-3">
                                    <div class="row g-3">
                                        <div class="col-sm-6 col-md-4 col-xl-4">
                                            @php($tz = \App\Models\BusinessSetting::where('key', 'timezone')->first())
                                            @php($settings_timezone = $tz ? $tz->value : 0)
                                            <div class="form-group mb-0">
                                                <label class="input-label d-flex align-items-center gap-1">
                                                    {{ translate('time_zone') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select name="timezone" class="form-control js-select2-custom">
                                                    @foreach(timezone_identifiers_list() as $tz)
                                                        <?php
                                                            $dt = new DateTime('now', new DateTimeZone($tz));
                                                        $offset = $dt->getOffset(); // in seconds
                                                        $hours = intdiv($offset, 3600);
                                                        $minutes = abs(($offset % 3600) / 60);
                                                        $sign = $hours >= 0 ? '+' : '-';
                                                        $gmt = sprintf('GMT%s%02d:%02d', $sign, abs($hours), $minutes);
                                                        ?>
                                                        <option value="{{ $tz }}" {{ isset($settings_timezone) && $settings_timezone == $tz ? 'selected' : '' }}>
                                                            ({{ $gmt }}) {{ $tz }}
                                                        </option>
                                                    @endforeach
                                                <option value="US/Central" {{ isset($settings_timezone) && $settings_timezone == 'US/Central' ? 'selected' :  '' }}> (GMT-06:00) Central Time (US & Canada)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-4 col-xl-4">
                                            @php($tf = \App\Models\BusinessSetting::where('key', 'timeformat')->first())
                                            @php($tf = $tf ? $tf->value : '24')
                                            <div class="form-group mb-0">
                                                <label for="timeformat"
                                                    class="form-label text-capitalize">{{ translate('time_format') }}
                                                    <span class="text-danger">*</span></label>
                                                <div class="resturant-type-group bg-white border">
                                                    <label class="form-check form--check mr-2 mr-md-4">
                                                        <input class="form-check-input" type="radio" value="12"
                                                            name="timeformat" {{ $tf == '12' ? 'checked' : '' }}>
                                                        <span class="form-check-label">
                                                            {{translate('12 Hours')}}
                                                        </span>
                                                    </label>
                                                    <label class="form-check form--check mr-2 mr-md-4">
                                                        <input class="form-check-input" type="radio" value="24"
                                                            name="timeformat" {{ $tf == '24' ? 'checked' : '' }}>
                                                        <span class="form-check-label">
                                                            {{translate('24 Hours')}}
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="shadow-sm p-xxl-20 p-xl-3 p-2 bg-white mb-20" id="currency-setup">

                                <div class="mb-20">
                                    <h4 class="mb-1">
                                        {{ translate('Currency Setup') }}
                                    </h4>
                                    <p class="mb-0 fs-12">
                                        {{ translate('Here you can manage currency settings to match with your business criteria') }}
                                    </p>
                                </div>
                                <div class="bg-light2 rounded p-xxl-20 p-3">
                                    <div class="row g-3">
                                        <div class="col-sm-6 col-md-4 col-xl-4">
                                            @php($currency_code = \App\Models\BusinessSetting::where('key', 'currency')->first())
                                            <div class="form-group mb-0">
                                                <label class="form-label"
                                                    for="currency">{{ translate('Currency Symbol') }}</label>
                                                <select id="change_currency" name="currency"
                                                    class="form-control js-select2-custom">
                                                    @foreach (\App\Models\Currency::orderBy('currency_code')->get() as $currency)
                                                        <option value="{{ $currency['currency_code'] }}" {{ $currency_code ? ($currency_code->value == $currency['currency_code'] ? 'selected' : '') : '' }}>
                                                            {{ $currency['currency_code'] }}
                                                            ({{ $currency['currency_symbol'] }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-4 col-xl-4">
                                            @php($currency_symbol_position = \App\Models\BusinessSetting::where('key', 'currency_symbol_position')->first())
                                            <div class="form-group mb-0">
                                                <label class="form-label text-capitalize"
                                                    for="currency_symbol_position">{{ translate('Currency Position') }}
                                                </label>
                                                <div class="resturant-type-group bg-white border">
                                                    <label class="form-check form--check mr-2 mr-md-4">
                                                        <input class="form-check-input" type="radio" value="left"
                                                            name="currency_symbol_position" {{ $currency_symbol_position ? ($currency_symbol_position->value == 'left' ? 'checked' : '') : '' }}>
                                                        <span class="form-check-label">
                                                            ({{\App\CentralLogics\Helpers::currency_symbol()}})
                                                            {{translate('Left')}}
                                                        </span>
                                                    </label>
                                                    <label class="form-check form--check mr-2 mr-md-4">
                                                        <input class="form-check-input" type="radio" value="right"
                                                            name="currency_symbol_position" {{ $currency_symbol_position ? ($currency_symbol_position->value == 'right' ? 'checked' : '') : '' }}>
                                                        <span class="form-check-label">
                                                            ({{\App\CentralLogics\Helpers::currency_symbol()}})
                                                            {{translate('Right')}}
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-4 col-xl-4">
                                            @php($digit_after_decimal_point = \App\Models\BusinessSetting::where('key', 'digit_after_decimal_point')->first())
                                            <div class="form-group mb-0">
                                                <label class="form-label text-capitalize"
                                                    for="digit_after_decimal_point">{{ translate('Digit after decimal point') }}
                                                </label>
                                                <span class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('how_many_fractional_digit_to_show_after_decimal_value') }}">
                                                        <i class="tio-info text-muted"></i>
                                                </span>
                                                <input type="number" name="digit_after_decimal_point" class="form-control"
                                                    id="digit_after_decimal_point"
                                                    placeholder="{{ translate('ex_:_2') }}"
                                                    value="{{ $digit_after_decimal_point ? $digit_after_decimal_point->value : 0 }}"
                                                    min="0" max="4" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            @php($subscription_business_model = \App\Models\BusinessSetting::where('key', 'subscription_business_model')->first())
                            @php($subscription_business_model = $subscription_business_model ? $subscription_business_model->value : 0)

                            @php($commission_business_model = \App\Models\BusinessSetting::where('key', 'commission_business_model')->first())
                            @php($commission_business_model = $commission_business_model ? $commission_business_model->value : 0)
                            <div class="shadow-sm p-xxl-20 p-xl-3 p-2 bg-white mb-20" id="business_model_section">
                                <div class="mb-20">
                                    <h4 class="mb-1">
                                        {{ translate('Business Model Setup') }}
                                    </h4>
                                    <p class="mb-0 fs-12">
                                        {{ translate('Setup your business model from here') }}
                                    </p>
                                </div>
                                <div class="bg-light2 rounded p-xxl-20 p-3">
                                    <div class="row g-3">
                                        <div class="col-lg-12">
                                            <label class="form-label" for="footer_text">{{translate('Business Model')}}
                                                <span class="text-danger">*</span>
                                                <span class="form-label-secondary" data-toggle="tooltip"
                                                    data-placement="right" data-original-title="{{ translate('Choose the model that decides how you earn money and process orders.') }}">
                                                    <i class="tio-info text-muted"></i>
                                                </span>
                                            </label>
                                            <div class="bg-white rounded p-3 border mb-20">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="subs" name="subscription_business_model" {{ $subscription_business_model ? 'checked' : '' }} value="1">
                                                                <label class="custom-control-label" for="subs">
                                                                    <h5 class="mb-1">{{ translate('Subscription') }}</h5>
                                                                    <p class="mb-0 fs-12">
                                                                        {{ translate('By selecting subscription based business model stores can run business with you based on subscription package.') }}
                                                                    </p>
                                                                    <div
                                                                        class="d-flex p-2 px-3 rounded gap-2 bg-opacity-warning-10 mt-3">
                                                                        <i class="tio-info text-warning"></i>
                                                                        <p class="fz-12px mb-0">
                                                                            {{translate('To active subscription based business model 1st you need to add subscription package from')}}
                                                                            <a href="{{route('admin.business-settings.subscriptionackage.index')}}"
                                                                                class="fz-12px font-semibold info-dark text-underline">{{translate('Subscription Packages')}}</a>
                                                                        </p>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input"
                                                                    id="commission" name="commission_business_model" {{ $commission_business_model ? 'checked' : '' }} value="1">
                                                                <label class="custom-control-label" for="commission">
                                                                    <h5 class="mb-1">{{ translate('Commission') }}</h5>
                                                                    <p class="mb-0 fs-12">
                                                                        {{ translate('By selecting commission based business model stores can run business with you based on commission based payment per order.') }}
                                                                    </p>
                                                                    <div
                                                                        class="info-notes-bg px-3 py-2 rounded fz-11  gap-2 d-flex mt-20">
                                                                        <img src="{{asset('public/assets/admin/img/info-idea.svg')}}"
                                                                            alt="">
                                                                        <span>
                                                                            {{translate('To set different commission for commission based stores.')}}
                                                                            {{translate('Go to')}}: <span
                                                                                class="fz-12px font-semibold info-dark">{{translate('store List')}}
                                                                                > {{translate('store Details')}} >
                                                                                {{translate('Business Plan')}}</span>
                                                                        </span>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-sm-6 col-lg-6">
                                                    @php($admin_commission = \App\Models\BusinessSetting::where('key', 'admin_commission')->first())
                                                    <div class="form-group mb-0">
                                                        <label class="form-label text-capitalize" for="admin_commission">
                                                            {{ translate('Default_Commission_Rate_On_Order') }} (%)
                                                            <span class="text-danger">*</span>
                                                            <span class="form-label-secondary" data-toggle="tooltip"
                                                                data-placement="right"
                                                                data-original-title="{{ translate('Set_up_‘Default_Commission_Rate’_on_every_Order._Admin_can_also_set_store-wise_different_commission_rates_from_respective_store_settings.') }}">
                                                                <i class="tio-info text-muted"></i>
                                                            </span>
                                                        </label>
                                                        <input type="number" name="admin_commission" class="form-control"
                                                            id="admin_commission"
                                                            placeholder="{{ translate('Ex:_10') }}"
                                                            value="{{ $admin_commission ? $admin_commission->value : 0 }}"
                                                            min="0" max="100" required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-lg-6">
                                                    @php($delivery_charge_comission = \App\Models\BusinessSetting::where('key', 'delivery_charge_comission')->first())
                                                    <div class="form-group mb-0">
                                                        <label class="input-label text-capitalize d-flex alig-items-center"
                                                            for="delivery_charge_comission">
                                                            {{translate('Commission_Rate_On_Delivery_Charge')}} (%)
                                                            <span class="text-danger">*</span>
                                                            <span class="form-label-secondary ml-1" data-toggle="tooltip"
                                                                data-placement="right"
                                                                data-original-title="{{ translate('Set_a_default_‘Commission_Rate’_for_freelance_deliverymen_(under_admin)_on_every_deliveryman. ') }}">
                                                                <i class="tio-info text-muted"></i>
                                                            </span>
                                                        </label>
                                                        <input type="number" name="delivery_charge_comission"
                                                            class="form-control" id="delivery_charge_comission"
                                                            placeholder="{{ translate('Ex:_10') }}" min="0"
                                                            max="100" step="{{ \App\CentralLogics\Helpers::getDecimalPlaces() }}"
                                                            value="{{ $delivery_charge_comission ? $delivery_charge_comission->value : 0 }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="shadow-sm p-xxl-20 p-xl-3 p-2 bg-white mb-20" id="additional_charge_section">
                                <div class="row g-3">
                                    <div class="col-xxl-9 col-lg-8 col-md-7 col-sm-6">
                                        <div>
                                            <h4 class="mb-1">
                                                {{ translate('Additional Charge Setup') }}
                                            </h4>
                                            <p class="mb-0 fs-12">
                                                {{ translate('By switching this feature ON, Customer need to pay the amount you set. ') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-md-5 col-sm-6">
                                        @php($additional_charge_status = \App\Models\BusinessSetting::where('key', 'additional_charge_status')->first())
                                        @php($additional_charge_status = $additional_charge_status ? $additional_charge_status->value : 0)
                                        <div class="form-group mb-0">
                                            <label
                                                class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                                <span class="pr-1 d-flex align-items-center switch--label">
                                                    <span class="line--limit-1">
                                                        {{translate('Status') }}
                                                    </span>
                                                </span>
                                                <input type="checkbox" data-id="additional_charge_status" data-type="toggle"
                                                    data-image-on="{{ asset('/public/assets/admin/img/modal/dm-tips-on.png') }}"
                                                    data-image-off="{{ asset('/public/assets/admin/img/modal/dm-tips-off.png') }}"
                                                    data-title-on="<strong>{{ translate('Want_to_enable_additional_charge?') }}</strong>"
                                                    data-title-off="<strong>{{ translate('Want_to_disable_additional_charge?') }}</strong>"
                                                    data-text-on="<p>{{ translate('If_you_enable_this,_additional_charge_will_be_added_with_order_amount,_it_will_be_added_in_admin_wallet') }}</p>"
                                                    data-text-off="<p>{{ translate('If_you_disable_this,_additional_charge_will_not_be_added_with_order_amount.') }}</p>"
                                                    class="status toggle-switch-input dynamic-checkbox-toggle" value="1"
                                                    name="additional_charge_status" id="additional_charge_status" {{ $additional_charge_status == 1 ? 'checked' : '' }}>
                                                <span class="toggle-switch-label text">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-light2 rounded p-xxl-20 p-3 additional__body mt-20">
                                    <div class="row g-3">
                                        <div class="col-sm-6 col-lg-6">
                                            @php($additional_charge_name = \App\Models\BusinessSetting::where('key', 'additional_charge_name')->first())
                                            <div class="form-group mb-0">
                                                <label
                                                    class="form-label d-flex justify-content-between text-capitalize mb-1"
                                                    for="additional_charge_name">
                                                    <span
                                                        class="line--limit-1">{{ translate('additional_charge_name') }}
                                                        <span class="text-danger">*</span>
                                                    </span>
                                                </label>

                                                <input type="text" name="additional_charge_name" class="form-control"
                                                    id="additional_charge_name"
                                                    placeholder="{{ translate('Ex:_Processing_Fee') }}"
                                                    value="{{ $additional_charge_name ? $additional_charge_name->value : '' }}"
                                                    {{ isset($additional_charge_status) ? '' : 'readonly' }} required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-6">
                                            @php($additional_charge = \App\Models\BusinessSetting::where('key', 'additional_charge')->first())
                                            <div class="form-group mb-0">
                                                <label
                                                    class="form-label d-flex justify-content-between text-capitalize mb-1"
                                                    for="additional_charge">
                                                    <span class="line--limit-1">{{ translate('charge_amount') }}
                                                        ({{ \App\CentralLogics\Helpers::currency_symbol() }}) <span
                                                            class="text-danger">*</span>
                                                    </span>
                                                </label>

                                                <input type="number" name="additional_charge" class="form-control"
                                                    id="additional_charge" placeholder="{{ translate('Ex:_10') }}"
                                                    value="{{ $additional_charge ? $additional_charge->value : 0 }}" min="0"
                                                    step="{{ \App\CentralLogics\Helpers::getDecimalPlaces() }}" {{ isset($additional_charge_status) ? '' : 'readonly' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="additional_charge_note">
                                    <div class="info-notes-bg px-3 py-2 rounded fz-11  gap-2 d-flex mt-20">
                                        <img src="{{asset('public/assets/admin/img/info-idea.svg')}}" alt="">
                                        <span>
                                            {{translate('Only admin will get the additional amount & customer must pay the amount.')}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-20" id="others_setup_section">
                        <div class="card-body">
                            <div class="mb-20">
                                <h4 class="mb-1">
                                    {{ translate('Others Setup') }}
                                </h4>
                                <p class="mb-0 fs-12">
                                    {{ translate('Here you setup your business others setup') }}
                                </p>
                            </div>
                            <div class="bg-light rounded p-xxl-20 p-3">
                                <div class="row g-3">
                                    <div class="col-sm-6 col-lg-4">
                                        @php($country_picker_status = \App\Models\BusinessSetting::where('key', 'country_picker_status')->first())
                                        @php($country_picker_status = $country_picker_status ? $country_picker_status->value : 0)
                                        <div class="form-group mb-0">
                                            <span class="mb-10px d-flex align-items-center">
                                                <span class="text-title">
                                                    {{translate('Country Picker') }}
                                                </span>
                                                <span class="form-label-secondary text-danger d-flex" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('messages.If_you_enable_this_option,_in_all_phone_no_field_will_show_a_country_picker_list.')}}"><i class="tio-info text-muted ps--3"></i>
                                                </span>
                                            </span>
                                            <label class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                                <span class="pr-1 d-flex align-items-center switch--label">
                                                    <span class="line--limit-1">
                                                        {{translate('messages.Status') }}
                                                    </span>
                                                </span>
                                                <input type="checkbox" data-id="country_picker_status" data-type="toggle"
                                                       data-image-on="{{ asset('/public/assets/admin/img/modal/mail-success.png') }}"
                                                       data-image-off="{{ asset('/public/assets/admin/img/modal/mail-warning.png') }}"
                                                       data-title-on="<strong>{{ translate('messages.Want_to_enable_country_picker?') }}</strong>"
                                                       data-title-off="<strong>{{ translate('messages.Want_to_disable_country_picker?') }}</strong>"
                                                       data-text-on="<p>{{ translate('messages.If_you_enable_this,_user_can_select_country_from_country_picker') }}</p>"
                                                       data-text-off="<p>{{ translate('messages.If_you_disable_this,_user_can_not_select_country_from_country_picker,_default_country_will_be_selected') }}</p>"
                                                       class="status toggle-switch-input dynamic-checkbox-toggle" value="1"
                                                       name="country_picker_status" id="country_picker_status" {{ $country_picker_status == 1 ? 'checked' : '' }}>
                                                <span class="toggle-switch-label text">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="fs-12 text-dark px-3 py-2 rounded bg-warning-10 mt-20">
                                <div class="d-flex align-items-center gap-2 mb-0">
                                    <span class="text-warning fs-14">
                                        <i class="tio-info"></i>
                                    </span>
                                    <span class="color-656566">
                                        {{ translate('If you want to business multiple country you need to turn on country picker feature.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                            <div class="shadow-sm p-xxl-20 p-xl-3 p-2 bg-white" id="content_setup_section">
                                <div class="mb-20">
                                    <h4 class="mb-1">
                                        {{ translate('Copyright & Cookies Text') }}
                                    </h4>
                                    <p class="mb-0 fs-12">
                                        {{ translate('Add the necessary texts to display in required sections') }}
                                    </p>
                                </div>
                                <div class="bg-light2 rounded p-xxl-20 p-3">
                                    <div class="row g-3">
                                        <div class="col-md-6 col-xl-6">
                                            @php($footer_text = \App\Models\BusinessSetting::where('key', 'footer_text')->first())
                                            <div class="form-group mb-0">
                                                <label class="form-label"
                                                    for="footer_text">{{ translate('Copyright Text') }}
                                                    <span class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('make_visitors_aware_of_your_business‘s_rights_&_legal_information.') }}">
                                                        <i class="tio-info text-muted"></i>
                                                    </span>
                                                </label>
                                                <textarea type="text" id="footer_text" maxlength="100" name="footer_text"
                                                    class="form-control" rows="3"
                                                    placeholder="{{ translate('Ex_:_Copyright_Text') }}"
                                                    required>{{ $footer_text->value ?? '' }}</textarea>
                                                <span
                                                    class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-6">
                                            @php($cookies_text = \App\Models\BusinessSetting::where('key', 'cookies_text')->first())
                                            <div class="form-group mb-0">
                                                <label class="form-label" for="cookies_text">{{ translate('Cookies Text') }}
                                                </label>
                                                <span class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('make_visitors_aware_of_your_business‘s_rights_&_legal_information.') }}">
                                                        <i class="tio-info text-muted"></i>
                                                    </span>
                                                <textarea type="text" id="cookies_text" maxlength="100" name="cookies_text"
                                                    class="form-control " rows="3"
                                                    placeholder="{{ translate('Ex_:_Cookies_Text') }}"
                                                    required>{{ $cookies_text->value ?? '' }}</textarea>
                                                <span
                                                    class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin-views.partials._floating-submit-button')
    </form>
</div>



<div class="modal fade" id="currency-warning-modal">
    <div class="modal-dialog modal-dialog-centered status-warning-modal">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true" class="tio-clear"></span>
                </button>
            </div>
            <div class="modal-body pb-5 pt-0">
                <div class="max-349 mx-auto mb-20">
                    <div>
                        <div class="text-center">
                            <img width="80" src="{{  asset('public/assets/admin/img/modal/currency.png') }}"
                                class="mb-20">
                            <h5 class="modal-title"></h5>
                        </div>
                        <div class="text-center">
                            <h3> {{ translate('Are_you_sure_to_change_the_currency_?') }}</h3>
                            <div>
                                <p>{{ translate('If_you_enable_this_currency,_you_must_active_at_least_one_digital_payment_method_that_supports_this_currency._Otherwise_customers_cannot_pay_via_digital_payments_from_the_app_and_websites._And_Also_stores_cannot_pay_you_digitally') }}
                                </p>
                            </div>
                        </div>

                        <div class="text-center mb-4">
                            <a class="text--underline"
                                href="{{ route('admin.business-settings.third-party.payment-method') }}">
                                {{ translate('Go_to_payment_method_settings.') }}</a>
                        </div>
                    </div>

                    <div class="btn--container justify-content-center">
                        <button data-dismiss="modal" id="confirm-currency-change"
                            class="btn btn--cancel min-w-120">{{translate("Cancel")}}</button>
                        <button data-dismiss="modal" type="button"
                            class="btn btn--primary min-w-120">{{translate('OK')}}</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="global_guideline_offcanvas" class="custom-offcanvas d-flex flex-column justify-content-between global_guideline_offcanvas">
    <div>
        <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
            <h3 class="mb-0">{{ translate('Business Settings Guideline') }}</h3>
            <button type="button" class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary offcanvas-close fz-15px p-0" aria-label="Close">&times;</button>
        </div>

        <div class="custom-offcanvas-body offcanvas-height-100 py-3 px-md-4 px-3">
            <!-- Maintenance Mode -->
            <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                    <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed" type="button" data-toggle="collapse" data-target="#maintenance_mode_guide" aria-expanded="true">
                        <div class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                            <i class="tio-down-ui"></i>
                        </div>
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Maintenance mode') }}</span>
                    </button>
                    <a href="#maintenance_mode_section" class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3 show" id="maintenance_mode_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Maintenance mode') }}</h5>
                            <ul class="fs-12">
                                <li>{{ translate('Turning on Maintenance mode will temporarily close your online store. Use this when you need to make updates or fix issues.') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                    <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed" type="button" data-toggle="collapse" data-target="#basic_information_guide" aria-expanded="true">
                        <div class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                            <i class="tio-down-ui"></i>
                        </div>
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Basic Information') }}</span>
                    </button>
                    <a href="#basic_information_section" class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3" id="basic_information_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Basic Information') }}</h5>
                            <ul class="fs-12">
                                <li><strong>{{ translate('Company Name') }}:</strong> {{ translate('Enter your official company name. This name represents your business and is used across the system.') }}</li>
                                <li><strong>{{ translate('Email') }}:</strong> {{ translate('Add your company email address. This email is used for business communication and records.') }}</li>
                                <li><strong>{{ translate('Phone') }}:</strong> {{ translate('Provide a contact phone number so customers and partners can reach your business easily for urgent inquiries, support needs, or quick questions.') }}</li>
                                <li><strong>{{ translate('Country') }}:</strong> {{ translate('Select your country. This is important for legal, operational, marketing, and payment-related settings.') }}</li>
                                <li><strong>{{ translate('Address') }}:</strong> {{ translate('This address is used to locate the business’s physical location.') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- General Settings -->
            <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                    <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed" type="button" data-toggle="collapse" data-target="#general_settings_guide" aria-expanded="true">
                        <div class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                            <i class="tio-down-ui"></i>
                        </div>
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('General Settings') }}</span>
                    </button>
                    <a href="#general_settings_section" class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3" id="general_settings_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('General Settings') }}</h5>
                            <p class="fs-12 mb-0">{{ translate('General Setup is the foundational step, where you configure essential business details (time zone, available currency) to set up the business.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Model -->
            <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                    <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed" type="button" data-toggle="collapse" data-target="#business_model_guide" aria-expanded="true">
                        <div class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                            <i class="tio-down-ui"></i>
                        </div>
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Business Model') }}</span>
                    </button>
                    <a href="#business_model_section" class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3" id="business_model_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Business Model') }}</h5>
                            <ul class="fs-12">
                                <li><strong>{{ translate('Subscription-based model') }}:</strong> {{ translate('A subscription-based business model allows customers or vendors to access specific features, services, or system functionalities by paying a recurring fee (monthly, quarterly, or yearly). Instead of one-time payments, users remain active as long as their subscription is valid.') }}</li>
                                <li><strong>{{ translate('Commission-based model') }}:</strong> {{ translate('In the Commission-based model, the platform earns revenue by taking a predefined fixed percentage from each completed order. The commission is automatically deducted from the order value before the remaining amount is settled with the vendor or service provider.') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Charge Setup -->
            <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                    <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed" type="button" data-toggle="collapse" data-target="#additional_charge_guide" aria-expanded="true">
                        <div class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                            <i class="tio-down-ui"></i>
                        </div>
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Additional Charge Setup') }}</span>
                    </button>
                    <a href="#additional_charge_section" class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3" id="additional_charge_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Additional Charge Setup') }}</h5>
                            <p class="fs-12 mb-0">{{ translate('Use this option to add extra fees to customer orders based on specific, predefined conditions. These charges are added automatically at checkout and are visible to both customers and vendors.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Others Setup -->
            <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                    <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed" type="button" data-toggle="collapse" data-target="#others_setup_guide" aria-expanded="true">
                        <div class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                            <i class="tio-down-ui"></i>
                        </div>
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Others Setup') }}</span>
                    </button>
                    <a href="#others_setup_section" class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3" id="others_setup_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Others Setup') }}</h5>
                            <ul class="fs-12">
                                <li><strong>{{ translate('Country Picker') }}:</strong> {{ translate('This option allows users to pick their country code while typing a phone number.') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Setup -->
            <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                    <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed" type="button" data-toggle="collapse" data-target="#content_setup_guide" aria-expanded="true">
                        <div class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                            <i class="tio-down-ui"></i>
                        </div>
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Content Setup') }}</span>
                    </button>
                    <a href="#content_setup_section" class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3" id="content_setup_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Content Setup') }}</h5>
                            <ul class="fs-12">
                                <li><strong>{{ translate('Copyright text') }}:</strong> {{ translate('This is a short statement that shows your company owns the content on your website. It usually includes the copyright symbol (©), the year, and your company name.') }}</li>
                                <li><strong>{{ translate('Cookies Text') }}:</strong> {{ translate('This is a short message shown on the website to let visitors know that the site uses cookies to collect information and improve their browsing experience') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="offcanvasOverlay" class="offcanvas-overlay"></div>

@endsection



@push('script_2')

<script>
    "use strict";

    $(document).ready(function () {
        let selectedCurrency = "{{ $currency_code ? $currency_code->value : 'USD' }}";
        let currencyConfirmed = false;
        let updatingCurrency = false;

        $("#change_currency").change(function () {
            if (!updatingCurrency) check_currency($(this).val());
        });

        $("#confirm-currency-change").click(function () {
            currencyConfirmed = true;
            update_currency(selectedCurrency);
            $('#currency-warning-modal').modal('hide');
        });

        function check_currency(currency) {
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: "{{route('admin.system_currency')}}",
                method: 'GET',
                data: { currency: currency },
                success: function (response) {
                    if (response.data) {
                        $('#currency-warning-modal').modal('show');
                    } else {
                        update_currency(currency);
                    }
                }
            });
        }

        function update_currency(currency) {
            if (currencyConfirmed) {
                updatingCurrency = true;
                $("#change_currency").val(currency).trigger('change');
                updatingCurrency = false;
                currencyConfirmed = false;
            }
        }
    });

</script>

<script
    src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value }}&libraries=places,marker&v=3.61">
    </script>
<script>
    "use strict";

    @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
    @php($language = $language->value ?? null)
    let language = <?php echo $language; ?>;
    $('[id=language]').val(language);





    function readURL(input, viewer) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $('#' + viewer).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#customFileEg1").change(function () {
        readURL(this, 'viewer');
    });

    $("#favIconUpload").change(function () {
        readURL(this, 'iconViewer');
    });

    function initAutocomplete() {
        const mapId = "{{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value }}"

        var myLatLng = {
            lat: {{ $default_location ? $default_location['lat'] : '-33.8688' }},
            lng: {{ $default_location ? $default_location['lng'] : '151.2195' }}
            };
        const map = new google.maps.Map(document.getElementById("location_map_canvas"), {
            center: {
                lat: {{ $default_location ? $default_location['lat'] : '-33.8688' }},
                lng: {{ $default_location ? $default_location['lng'] : '151.2195' }}
                },
            zoom: 13,
            mapTypeId: "roadmap",
            mapId: mapId,
        });

        const { AdvancedMarkerElement } = google.maps.marker;

        var marker = new AdvancedMarkerElement({
            position: myLatLng,
            map: map,
        });

        var geocoder = geocoder = new google.maps.Geocoder();
        google.maps.event.addListener(map, 'click', function (mapsMouseEvent) {
            var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
            var coordinates = JSON.parse(coordinates);
            var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);
            marker.position = latlng;
            marker.map = map;
            map.panTo(latlng);

            markers.forEach((m) => {
                m.map = null;
            });
            markers = [];

            document.getElementById('latitude').value = coordinates['lat'];
            document.getElementById('longitude').value = coordinates['lng'];


            geocoder.geocode({
                'latLng': latlng
            }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                        document.getElementById('address').value = results[1].formatted_address;
                    }
                }
            });
        });
        // Create the search box and link it to the UI element.
        const input = document.getElementById("pac-input");
        const searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
        // Bias the SearchBox results towards current map's viewport.
        map.addListener("bounds_changed", () => {
            searchBox.setBounds(map.getBounds());
        });
        let markers = [];
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener("places_changed", () => {
            const places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }
            // Clear out the old markers.
            markers.forEach((m) => {
                m.map = null;
            });
            markers = [];
            marker.map = null;
            // For each place, get the icon, name and location.
            const bounds = new google.maps.LatLngBounds();
            places.forEach((place) => {
                if (!place.geometry || !place.geometry.location) {
                    console.log("Returned place contains no geometry");
                    return;
                }
                const { AdvancedMarkerElement } = google.maps.marker;
                var mrkr = new AdvancedMarkerElement({
                    map,
                    title: place.name,
                    position: place.geometry.location,
                });
                google.maps.event.addListener(mrkr, "click", function (event) {
                    document.getElementById('latitude').value = this.position.lat();
                    document.getElementById('longitude').value = this.position.lng();
                });

                markers.push(mrkr);

                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);
        });
    };

    $(document).on('ready', function () {
        initAutocomplete();
    });

    $(document).on("keydown", "input", function (e) {
        if (e.which === 13) e.preventDefault();
    });

    // Business Model Validation
    $('#subs, #commission').on('change', function (e) {
        
            if (!$('#subs').is(':checked') && !$('#commission').is(':checked')) {
                e.preventDefault();
                toastr.error('{{ translate("At least one business model must be selected") }}');
                $(this).prop('checked', true);
            }
        
    });

    $(document).on('click', '.confirm-Toggle', function () {
        setTimeout(function () {
            let toggle_id = $("#toggle-ok-button").attr("toggle-ok-button");
            if (toggle_id === "additional_charge_status") {
                if ($("#additional_charge_status").is(":checked")) {
                    $('.additional__body').slideDown();
                    $('#additional_charge_note').slideDown();
                } else {
                    $('.additional__body').slideUp();
                    $('#additional_charge_note').slideUp();
                }
            } else if (toggle_id === 'maintenance_mode') {
                var maintenance_mode = $('#maintenance_mode').is(':checked');
                $.ajax({
                    url: '{{ route('admin.maintenance-mode') }}',
                    method: 'get',
                    data: {
                        maintenance_mode: maintenance_mode ? 1 : 0
                    },
                    success: function (data) {
                        toastr.success(data.message);
                    },
                });
            }
        }, 0);
    });

    $(document).ready(function () {
        if ($('#additional_charge_status').is(':checked')) {
            $('.additional__body').show();
            $('#additional_charge_note').show();
        } else {
            $('.additional__body').hide();
            $('#additional_charge_note').hide();
        }
    });
</script>
@endpush
