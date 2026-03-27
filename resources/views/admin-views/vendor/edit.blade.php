@extends('layouts.admin.app')

@section('title', 'Update restaurant info')
@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/edit.png') }}" class="w--26" alt="">
                </span>
                <span>{{ translate('messages.update_store') }}</span>
            </h1>
        </div>
        @php
            $delivery_time_start = preg_match('([0-9]+[\-][0-9]+\s[min|hours|days])', $store->delivery_time ?? '')
                ? explode('-', $store->delivery_time)[0]
                : 10;
            $delivery_time_end = preg_match('([0-9]+[\-][0-9]+\s[min|hours|days])', $store->delivery_time ?? '')
                ? explode(' ', explode('-', $store->delivery_time)[1])[0]
                : 30;
            $delivery_time_type = preg_match('([0-9]+[\-][0-9]+\s[min|hours|days])', $store->delivery_time ?? '')
                ? explode(' ', explode('-', $store->delivery_time)[1])[1]
                : 'min';
        @endphp
        @php($language = \App\CentralLogics\Helpers::get_business_settings('language'))

        <!-- End Page Header -->

        <form class="validate-form global-ajax-form" action="{{ route('admin.store.update', [$store['id']]) }}" enctype="multipart/form-data" method="post">
            <div class="card mb-20">
                <div class="card-header">
                    <div class="mb-0">
                        <h3 class="mb-1">
                            {{ translate('Basic Information') }}
                        </h3>
                        <p class="mb-0 fs-12">
                            {{ translate('Here you setup your all business information.') }}
                        </p>
                    </div>
                </div>
                <div class="card-body p-xxl-20 p-3">
                    <div class="row g-3">
                        <div class="col-lg-7">
                            <div class="shadow-sm p-xxl-20 p-sm-3 p-0">
                                <div class="bg-light2 rounded p-3 mb-20">
                                    @if ($language)
                                    <ul class="nav nav-tabs mb-4">
                                        <li class="nav-item">
                                            <a class="nav-link lang_link active" href="#"
                                                id="default-link">{{ translate('Default') }}</a>
                                        </li>
                                        @foreach ($language as $lang)
                                            <li class="nav-item">
                                                <a class="nav-link lang_link" href="#"
                                                    id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @endif
                                    @if ($language)
                                        <div class="lang_form" id="default-form">
                                            <div class="form-group error-wrapper">
                                                <label class="input-label" for="default_name">{{ translate('messages.Business name') }}
                                                    ({{ translate('messages.Default') }}) <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="name[]" id="default_name" class="form-control"
                                                    placeholder="{{ translate('messages.store_name') }}"
                                                    value="{{ $store->getRawOriginal('name') }}" required>


                                            </div>
                                            <input type="hidden" name="lang[]" value="default">
                                            <div class="form-group mb-0 error-wrapper">
                                                <label class="input-label"
                                                    for="exampleFormControlInput1">{{ translate('messages.Business address') }}
                                                    ({{ translate('messages.default') }})  <span class="text-danger">*</span>
                                                </label>
                                                <textarea required type="text" name="address[]" rows="1" placeholder="{{ translate('messages.store') }}" required
                                                    class="form-control min-h-90px">{{ $store->getRawOriginal('address') }}</textarea>

                                            </div>
                                        </div>
                                        @foreach ($language as $lang)
                                            <?php
                                            if (count($store['translations'])) {
                                                $translate = [];
                                                foreach ($store['translations'] as $t) {
                                                    if ($t->locale == $lang && $t->key == 'name') {
                                                        $translate[$lang]['name'] = $t->value;
                                                    }
                                                    if ($t->locale == $lang && $t->key == 'address') {
                                                        $translate[$lang]['address'] = $t->value;
                                                    }
                                                }
                                            }
                                            ?>
                                            <div class="d-none lang_form" id="{{ $lang }}-form">
                                                <div class="form-group error-wrapper">
                                                    <label class="input-label"
                                                        for="{{ $lang }}_name">{{ translate('messages.Business name') }}
                                                        ({{ strtoupper($lang) }})
                                                    </label>
                                                    <input type="text" name="name[]" id="{{ $lang }}_name"
                                                        class="form-control" value="{{ $translate[$lang]['name'] ?? '' }}"
                                                        placeholder="{{ translate('messages.store_name') }}">

                                                </div>
                                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                                                <div class="form-group mb-0 error-wrapper">
                                                    <label class="input-label"
                                                        for="exampleFormControlInput1">{{ translate('messages.Business address') }}
                                                        ({{ strtoupper($lang) }})</label>
                                                    <textarea type="text" name="address[]" rows="1"  placeholder="{{ translate('messages.store') }}"
                                                        class="form-control min-h-90px">{{ $translate[$lang]['address'] ?? '' }}</textarea>

                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div id="default-form">
                                            <div class="form-group error-wrapper">
                                                <label class="input-label"
                                                    for="exampleFormControlInput1">{{ translate('messages.name') }}
                                                    ({{ translate('messages.default') }})</label>
                                                <input type="text" name="name[]" class="form-control"
                                                    placeholder="{{ translate('messages.store_name') }}" required>

                                            </div>
                                            <input type="hidden" name="lang[]" value="default">
                                            <div class="form-group mb-0 error-wrapper">
                                                <label class="input-label"
                                                    for="exampleFormControlInput1">{{ translate('messages.address') }}
                                                </label>
                                                <textarea type="text" name="address[]" rows="1"  placeholder="{{ translate('messages.store') }}"
                                                    class="form-control "></textarea>

                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group error-wrapper">
                                    <label class="input-label" for="choice_zones">{{ translate('messages.Business zone') }}
                                        <span class="form-label-secondary" data-toggle="tooltip"
                                            data-placement="right"
                                            data-original-title="{{ translate('messages.select_zone_for_map') }}">
                                           <!-- <i class="tio-info text-muted"></i> -->
                                        </span> <span class="text-danger">*</span>
                                    </label>
                                    <select name="zone_id" id="choice_zones"
                                        data-placeholder="{{ translate('messages.select_zone') }}"
                                        class="form-control js-select2-custom get_zone_data">
                                        @foreach (\App\Models\Zone::active()->get(['id', 'name']) as $zone)
                                            @if (isset(auth('admin')->user()->zone_id))
                                                @if (auth('admin')->user()->zone_id == $zone->id)
                                                    <option value="{{ $zone->id }}"
                                                        {{ $store->zone_id == $zone->id ? 'selected' : '' }}>
                                                        {{ $zone->name }}</option>
                                                @endif
                                            @else
                                                <option value="{{ $zone->id }}"
                                                    {{ $store->zone_id == $zone->id ? 'selected' : '' }}>
                                                    {{ $zone->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="position-relative">
                                    <label class="input-label"
                                        for="tax">{{ translate('Estimated Delivery Time ( Min & Maximum Time)') }}  <span class="text-danger">*</span>
                                    </label>

                                    <div class="floating--date-inner time-delivery-vendor bg-white rounded border d-flex align-items-center">
                                        <div class="item error-wrapper w-100">
                                            <input id="minimum_delivery_time" type="number"
                                                name="minimum_delivery_time"
                                                value="{{ $delivery_time_start }}"
                                                class="form-control  w-100 h--45px border-0 outline-0"
                                                placeholder="{{ translate('messages.Ex :') }} 30"
                                                pattern="^[0-9]{2}$" required
                                                value="{{ old('minimum_delivery_time') }}">

                                        </div>
                                        <div class="item error-wrapper border-left w-100">
                                            <input id="maximum_delivery_time" type="number"
                                                name="maximum_delivery_time"
                                                value="{{ $delivery_time_end }}"
                                                class="form-control w-100 h--45px border-0 outline-0"
                                                placeholder="{{ translate('messages.Ex :') }} 60"
                                                pattern="[0-9]{2}" required
                                                value="{{ old('maximum_delivery_time') }}">

                                        </div>
                                        <div class="item smaller">
                                            <select name="delivery_time_type" id="delivery_time_type"
                                                class="custom-select min-w-90 bg-light2 h--45px border-0 outline-0">
                                                <option value="min"
                                                    {{ $delivery_time_type == 'min' ? 'selected' : '' }}>
                                                    {{ translate('messages.minutes') }}</option>
                                                <option value="hours"
                                                    {{ $delivery_time_type == 'hours' ? 'selected' : '' }}>
                                                    {{ translate('messages.hours') }}</option>
                                                <option value="days"
                                                    {{ $delivery_time_type == 'days' ? 'selected' : '' }}>
                                                    {{ translate('messages.days') }}</option>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-lg-5">
                            <div class="bg-light2 rounded p-xxl-20 p-3">
                                <div class="mb-15">
                                    <h4 class="mb-1">
                                        {{ translate('Set Business Location on Map') }}
                                    </h4>
                                    <p class="mb-0 fs-12">
                                        {{ translate('Please mark the exact business location to help customers find it easily.') }}
                                    </p>
                                </div>
                                <div class="map-for-vndor map_custom-controls position-relative">
                                    <input id="pac-input" class="controls rounded initial-8" title="{{translate('messages.search_your_location_here')}}" type="text" placeholder="{{translate('messages.search_here')}}"/>
                                   <div id="map"></div>


                                   <div class="d-flex bg-white align-items-center gap-1 laglng-controller">
                                                <div id="latlng" class="d-flex">
                                                    <input type="text" id="latitude" name="latitude" class="border-0 p-0 m-0 text-center outline-0"
                                                placeholder="{{ translate('messages.Ex:') }} -94.22213"
                                                value="{{ $store->latitude }}" required readonly>
                                                    <span class="text-gray1">|</span>
                                                    <input type="text" name="longitude" class="border-0 p-0 m-0 text-center outline-0"
                                                placeholder="{{ translate('messages.Ex:') }} 103.344322" id="longitude"
                                                value="{{ $store->longitude }}" required readonly>
                                                </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}




                        <div class="col-lg-5">
                            <div class="bg-light2 rounded p-xxl-20 p-3">
                                <div class="mb-15">
                                    <h4 class="mb-1">
                                        {{ translate('Set Business Location on Map') }}
                                    </h4>
                                    <p class="mb-0 fs-12">
                                        {{ translate('Please mark the exact business location to help customers find it easily.') }}
                                    </p>
                                </div>
                                <div class="map-for-vndor map_custom-controls position-relative">
                                    <input id="pac-input" class="controls rounded initial-8" title="{{translate('messages.search_your_location_here')}}" type="text" placeholder="{{translate('messages.search_here')}}"/>
                                   <div id="map"></div>


                                   <div class="d-flex bg-white align-items-center gap-1 laglng-controller">
                                                <div id="latlng" class="d-flex">
                                                    <input type="text" id="latitude" name="latitude" class="border-0 p-0 m-0 text-center outline-0"
                                                placeholder="{{ translate('messages.Ex:') }} -94.22213"
                                                value="{{$store->latitude }}" readonly>
                                                    <span class="text-gray1">|</span>
                                                    <input type="text" name="longitude" class="border-0 p-0 m-0 text-center outline-0"
                                                placeholder="{{ translate('messages.Ex:') }} 103.344322" id="longitude"
                                                value="{{ $store->longitude }}" readonly>
                                                </div>
                                    </div>
                                   <div id="outOfZone" class="map-alert bg-dark d-flex align-items-center rounded-8 py-2 px-2 fs-12 text-white mb-2">
                                        <img class="" src="{{asset('public/assets/admin/img/icons/warning-cus.png')}}" alt="img"> {{ translate('Please place the marker inside the available zones.') }}
                                   </div>
                                </div>
                            </div>
                        </div>



                    </div>
                </div>
            </div>

            <div class="card mb-20">
                <div class="card-header">
                    <div class="mb-0">
                        <h3 class="mb-1">
                            {{ translate('General Setup') }}
                        </h3>
                        <p class="mb-0 fs-12">
                            {{ translate('Here you can manage time settings to match with your business criteria') }}
                        </p>
                    </div>
                </div>
                <div class="card-body p-xxl-20 p-3">
                    <div class="shadow-sm p-xxl-20 p-sm-3 p-0 mb-20">
                        <div class="mb-15">
                            <h4 class="mb-1">
                                {{ translate('Business Logo & Covers') }}
                            </h4>
                            <p class="mb-0 fs-12">
                                {{ translate('Format : Jpg, jpeg, png, gif, webp. Less Than 2MB') }}
                            </p>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">

                                <div class="bg-light2 rounded p-20">
                                    <div class="mb-15 text-center">
                                        <h4 class="mb-0">{{ translate('Business Cover') }} <span class="text-danger">*</span></h4>
                                    </div>
                                    <div class="mx-auto text-center error-wrapper">
                                        <div class="upload-file_custom ratio-2-1 h-100px">
                                            <input type="file" name="cover_photo"
                                                    class="upload-file__input single_file_input"
                                                    accept=".webp, .jpg, .jpeg, .png, .gif" {{ $store->cover_photo ? '' : 'required' }}>
                                            <label class="upload-file__wrapper w-100 h-100 m-0">
                                                <div class="upload-file-textbox text-center" style="">
                                                    <img width="22" class="svg"
                                                            src="{{asset('public/assets/admin/img/document-upload.svg')}}"
                                                            alt="img">
                                                    <h6
                                                        class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                        <span class="theme-clr">{{ translate('Add Image') }}</span>
                                                        <br class="mb-1">
                                                         {{ translate('Ratio (2:1)') }}
                                                    </h6>
                                                </div>
                                                <img class="upload-file-img" loading="lazy" src="{{ $store->cover_photo_full_url ?? asset('public/assets/admin/img/upload-img.png') }}"
                                                        data-default-src="" alt="" style="display: none;">
                                            </label>
                                            <div class="overlay">
                                                <div
                                                    class="d-flex gap-1 justify-content-center align-items-center h-100">
                                                    <button type="button"
                                                            class="btn btn-outline-info icon-btn view_btn">
                                                        <i class="tio-invisible"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-outline-info icon-btn edit_btn">
                                                        <i class="tio-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">

                                <div class="bg-light2 rounded p-20">
                                    <div class="mb-15 text-center">
                                        <h4 class="mb-0">{{ translate('Business Logo') }} <span class="text-danger">*</span></h4>
                                    </div>
                                    <div class="mx-auto text-center error-wrapper">
                                        <div class="upload-file_custom ratio-1 h-100px">
                                            <input type="file" name="logo"
                                                    class="upload-file__input single_file_input"
                                                    accept=".webp, .jpg, .jpeg, .png, .gif" {{ $store->logo ? '' : 'required' }}>
                                            <label class="upload-file__wrapper w-100 h-100 m-0">
                                                <div class="upload-file-textbox text-center" style="">
                                                    <img width="22" class="svg"
                                                            src="{{asset('public/assets/admin/img/document-upload.svg')}}"
                                                            alt="img">
                                                    <h6
                                                        class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                        <span class="theme-clr">{{ translate('Add Image') }}</span>
                                                        <br class="mb-1">
                                                       {{ translate('Ratio (1:1)') }}
                                                    </h6>
                                                </div>
                                                <img class="upload-file-img" loading="lazy" src="{{ $store->logo_full_url ?? asset('public/assets/admin/img/upload-img.png') }}"
                                                        data-default-src="" alt="" style="display: none;">
                                            </label>
                                            <div class="overlay">
                                                <div
                                                    class="d-flex gap-1 justify-content-center align-items-center h-100">
                                                    <button type="button"
                                                            class="btn btn-outline-info icon-btn view_btn">
                                                        <i class="tio-invisible"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-outline-info icon-btn edit_btn">
                                                        <i class="tio-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="shadow-sm p-xxl-20 p-sm-3 p-0 mb-20">
                        <div class="mb-20">
                            <h4 class="mb-1">
                                {{ translate('Business Owner Info') }}
                            </h4>
                            <p class="mb-0 fs-12">
                                {{ translate('Setup your business time zone and format from here') }}
                            </p>
                        </div>
                        <div class="bg-light2 rounded p-xxl-20 p-3">
                            <div class="row g-3">
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group mb-0 error-wrapper">
                                        <label class="input-label"
                                            for="f_name">{{ translate('messages.first_name') }}  <span class="text-danger">*</span></label>
                                        <input type="text" name="f_name" class="form-control"
                                            placeholder="{{ translate('messages.first_name') }}"
                                            value="{{ $store->vendor->f_name }}" required>

                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group mb-0 error-wrapper">
                                        <label class="input-label"
                                            for="l_name">{{ translate('messages.last_name') }}  <span class="text-danger">*</span></label>
                                        <input type="text" name="l_name" class="form-control"
                                            placeholder="{{ translate('messages.last_name') }}"
                                            value="{{ $store->vendor->l_name }}" required>

                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6 error-wrapper">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="phone">{{ translate('messages.phone') }}  <span class="text-danger">*</span></label>
                                        <input type="tel" id="phone" name="phone" class="form-control"
                                            placeholder="{{ translate('messages.Ex:') }} 017********"
                                            value="{{ $store->vendor->phone }}" required>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="shadow-sm p-xxl-20 p-sm-3 p-0 mb-20">
                        <div class="mb-20">
                            <h4 class="mb-1">
                                {{ translate('Account Information') }}
                            </h4>
                            <p class="mb-0 fs-12">
                                {{ translate('Setup your business time zone and format from here') }}
                            </p>
                        </div>
                        <div class="bg-light2 rounded p-xxl-20 p-3">
                            <div class="row g-3">
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group mb-0 error-wrapper">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.email') }}  <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control"
                                            placeholder="{{ translate('messages.Ex:') }} ex@example.com"
                                            value="{{ $store->email }}" required>

                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="js-form-message form-group mb-0 error-wrapper">
                                        <label class="input-label"
                                            for="signupSrPassword">{{ translate('password') }}<span
                                                class="form-label-secondary" data-toggle="tooltip" data-placement="right"
                                                data-original-title="{{ translate('messages.Must_contain_at_least_one_number_and_one_uppercase_and_lowercase_letter_and_symbol,_and_at_least_8_or_more_characters') }}"></span></label>

                                        <div class="input-group input-group-merge">
                                            <input type="password" class="js-toggle-password form-control"
                                                name="password" id="signupSrPassword"
                                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                                title="{{ translate('messages.Must_contain_at_least_one_number_and_one_uppercase_and_lowercase_letter_and_symbol,_and_at_least_8_or_more_characters') }}"
                                                placeholder="{{ translate('messages.password_length_placeholder', ['length' => '8+']) }}"
                                                aria-label="8+ characters required"
                                                data-msg="Your password is invalid. Please try again."
                                                data-hs-toggle-password-options='{
                                            "target": [".js-toggle-password-target-1", ".js-toggle-password-target-2"],
                                            "defaultClass": "tio-hidden-outlined",
                                            "showClass": "tio-visible-outlined",
                                            "classChangeTarget": ".js-toggle-passowrd-show-icon-1"
                                            }'>
                                            <div class="js-toggle-password-target-1 input-group-append">
                                                <a class="input-group-text" href="javascript:;">
                                                    <i class="js-toggle-passowrd-show-icon-1 tio-visible-outlined"></i>
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="js-form-message form-group mb-0 error-wrapper">
                                        <label class="input-label"
                                            for="signupSrConfirmPassword">{{ translate('messages.Confirm Password') }}</label>

                                        <div class="input-group input-group-merge">
                                            <input type="password" class="js-toggle-password form-control"
                                                name="confirmPassword" id="signupSrConfirmPassword"
                                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                                title="{{ translate('messages.Must_contain_at_least_one_number_and_one_uppercase_and_lowercase_letter_and_symbol,_and_at_least_8_or_more_characters') }}"
                                                placeholder="{{ translate('messages.password_length_placeholder', ['length' => '8+']) }}"
                                                aria-label="8+ characters required"
                                                data-msg="Password does not match the confirm password."
                                                data-hs-toggle-password-options='{
                                                    "target": [".js-toggle-password-target-1", ".js-toggle-password-target-2"],
                                                    "defaultClass": "tio-hidden-outlined",
                                                    "showClass": "tio-visible-outlined",
                                                    "classChangeTarget": ".js-toggle-passowrd-show-icon-2"
                                                    }'>
                                            <div class="js-toggle-password-target-2 input-group-append">
                                                <a class="input-group-text" href="javascript:;">
                                                    <i class="js-toggle-passowrd-show-icon-2 tio-visible-outlined"></i>
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="shadow-sm p-xxl-20 p-sm-3 p-0">
                            <div class="mb-20">
                                <h3 class="mb-1">{{ translate('Business TIN') }}</h3>
                                {{-- <p class="fz-12px mb-0">{{translate('Lorem ipsum dolor sit amet, consectetur adipiscing elit.')}}</p> --}}
                            </div>
                            <div class="row g-3">
                                <div class="col-md-8 col-xxl-9">
                                    <div class="bg-light2 rounded p-20 h-100">
                                        <div class="form-group error-wrapper">
                                            <label class="input-label mb-2 d-block title-clr fw-normal"
                                                for="exampleFormControlInput1">{{ translate('Taxpayer Identification Number(TIN)') }}
                                            </label>
                                            <input type="text" name="tin"
                                                placeholder="{{ translate('Type Your Taxpayer Identification Number(TIN)') }}"
                                                class="form-control" value="{{ $store->tin }}">
                                        </div>
                                        <div class="form-group mb-0 error-wrapper">
                                            <label class="input-label mb-2 d-block title-clr fw-normal"
                                                for="exampleFormControlInput1">{{ translate('Expire Date') }} </label>
                                            <input type="date" name="tin_expire_date" class="form-control"
                                                value="{{ $store->tin_expire_date }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xxl-3">
                                    <div class="bg-light2 rounded p-20 h-100 single-document-uploaderwrap">
                                        <div class="d-flex align-items-center gap-1 justify-content-center text-center mb-20">
                                            <div>
                                                <h4 class="mb-1 fz--14px">{{ translate('TIN Certificate') }}</h4>
                                                <p class="fz-12px mb-0">
                                                    {{ translate('pdf, doc, jpg. File size : max 2 MB') }}</p>
                                            </div>
                                            <!-- <div class="d-flex gap-3 align-items-center">
                                                <button type="button" id="doc_edit_btn"
                                                    class="w-30px h-30 rounded d-flex align-items-center justify-content-center btn--primary btn px-3 icon-btn">
                                                    <i class="tio-edit"></i>
                                                </button>
                                            </div> -->
                                        </div>
                                        <div class="error-wrapper max-w-280 mx-auto position-relative">
                                            <button type="button" id="doc_edit_btn"
                                                class="w-30px h-30 rounded d-flex align-items-center justify-content-center btn--primary btn px-3 icon-btn position-absolute edit__icon-fortin">
                                                <i class="tio-edit"></i>
                                            </button>
                                            <div id="file-assets"
                                                data-picture-icon="{{ asset('public/assets/admin/img/picture.svg') }}"
                                                data-document-icon="{{ asset('public/assets/admin/img/document.svg') }}"
                                                data-blank-thumbnail="{{ asset('public/assets/admin/img/picture.svg') }}">
                                            </div>
                                            <div class="d-flex justify-content-center" id="pdf-container">
                                                <div class="document-upload-wrapper d-none" id="doc-upload-wrapper">
                                                    <input type="file" name="tin_certificate_image"
                                                        class="document_input" accept=".doc, .pdf, .jpg, .png, .jpeg">
                                                    <div class="textbox">
                                                        <img width="40" height="40" class="svg"
                                                            src="{{ asset('public/assets/admin/img/doc-uploaded.png') }}"
                                                            alt="">
                                                        <p class="fs-12 mb-0">
                                                            {{ translate('messages.Select_a_file_or') }} <span
                                                                class="font-semibold">{{ translate('messages.Drag & Drop') }}</span>
                                                            {{ translate('messages.here') }}</p>
                                                    </div>
                                                </div>
                                                <div class="pdf-single" data-file-name="${file.name}"
                                                    data-file-url="{{ $store->tin_certificate_image_full_url ?? asset('public/assets/admin/img/upload-cloud.png') }}">
                                                    <div class="pdf-frame">
                                                        @php($imgPath = $store->tin_certificate_image_full_url ?? asset('public/assets/admin/img/upload-cloud.png'))
                                                        @if (Str::endsWith($imgPath, ['.pdf', '.doc', '.docx']))
                                                            @php($imgPath = asset('public/assets/admin/img/document.svg'))
                                                        @endif
                                                        <img class="pdf-thumbnail-alt" src="{{ $imgPath }}"
                                                            alt="File Thumbnail">
                                                    </div>
                                                    <div class="overlay">
                                                        <div class="pdf-info">
                                                            @if (Str::endsWith($imgPath, ['.pdf', '.doc', '.docx']))
                                                                <img src="{{ asset('public/assets/admin/img/document.svg') }}"
                                                                    width="34" alt="File Type Logo">
                                                            @else
                                                                <img src="{{ asset('public/assets/admin/img/picture.svg') }}"
                                                                    width="34" alt="File Type Logo">
                                                            @endif
                                                            <div class="file-name-wrapper">
                                                                <span
                                                                    class="file-name js-filename-truncate">{{ $store->tin_certificate_image }}</span>
                                                                <span
                                                                    class="opacity-50">{{ translate('Click to view the file') }}</span>
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
                    </div>
                    @if (request()->pending == 1)
                        <input type="hidden" name="approve_vendor" value="1">
                    @endif
                </div>
            </div>

             <div class="btn--container justify-content-end mt-4">
                <button type="reset" id="reset_btn" class="btn btn--reset min-w-120px">{{ translate('messages.reset') }}</button>
                <button type="submit" id="submitButton" class="btn btn--primary min-w-120px"><i class="tio-save"></i> {{ request()->pending == 1 ? translate('Update_&_Approve') : translate('messages.Save Information') }}</button>
            </div>
        </form>
    </div>

@endsection

@push('script_2')
    @php($default_location =  \App\CentralLogics\Helpers::get_business_settings('default_location') ?? '')

    <script>
        const getAllModules ="{{ route('restaurant.get-all-modules') }}";
         const getModuleType ="{{ route('restaurant.get-module-type') }}";
         const checkModuleTypeUrl ="{{ route('restaurant.check-module-type') }}";
        const estimatedPickupText =
        "{{ translate('messages.Estimated_pickup_time') }} <span class='text-danger'>*</span>";
        const approxDeliveryText =
        "{{ translate('messages.approx_delivery_time') }} <span class='text-danger'>*</span>";

        window.mapConfig = {
            mapApiKey: "{{ \App\CentralLogics\Helpers::get_business_settings('map_api_key') }}",
            defaultLocation: {!! json_encode($default_location) !!},
            oldLat: parseFloat("{{ $store->latitude }}"),
            oldLng: parseFloat("{{ $store->longitude }}"),
            oldZoneId: "{{ $store->zone_id }}",
            oldAddress: @json($store->address),
            translations: {
                selectedLocation: "{{ translate('Selected Location') }}",
                clickMap: "{{ translate('Click_the_map_inside_the_red_marked_area_to_get_Lat/Lng!!!') }}",
                selectZone: "{{ translate('Select_Zone_From_The_Dropdown') }}",
                geolocationError: "{{ translate('Error:_Your_browser_doesnot_support_geolocation.') }}",
                outOfZone: "{{ translate('messages.out_of_coverage') }}",
            },
            urls: {
                zoneCoordinates: "{{ route('admin.zone.get-coordinates', ['id' => ':coordinatesZoneId']) }}",
                zoneGetZone: "{{ route('admin.zone.get-zone') }}",
            }
        };
    </script>

    <script src="{{ asset('public/assets/admin/js/file-preview/pdf.min.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/file-preview/pdf-worker.min.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/file-preview/edit-multiple-document-upload.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/view-pages/map-functionality.js') }}"></script>

    <script src="{{ asset('public/assets/admin/js/spartan-multi-image-picker.js') }}"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ \App\CentralLogics\Helpers::get_business_settings('map_api_key') }}&libraries=drawing,places,marker,geometry&v=3.61&language={{ str_replace('_', '-', app()->getLocale()) }}&callback=initMap"
        async defer>
    </script>

    <script>
        "use strict";
        $(document).on('ready', function() {
            @if (isset(auth('admin')->user()->zone_id))
                $('#choice_zones').trigger('change');
            @endif
        });


        $('#reset_btn').click(function() {
            $('#choice_zones').val(null).trigger('change');
            $('#latitude').val(null);
            $('#longitude').val(null);
        })
    </script>
@endpush
