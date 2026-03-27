@extends('layouts.landing.app')
@section('title', translate('messages.vendor_registration'))
@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('public/assets/admin/css/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/admin/css/view-pages/vendor-registration.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/landing/css/select2.min.css') }}"/>


    <link rel="stylesheet" href="{{ asset('public/assets/admin/vendor/icon-set/style.css') }}">

    <style>
        .password-feedback {
            display: none;
            width: 100%;
            margin-top: .25rem;
            font-size: .875em;

        }

        .valid {
            color: green;
        }

        .invalid {
            color: red;
        }

        .pickup-zone-container {
            display: none;
        }
    </style>
@endpush
@section('content')
    <section class="m-0 py-5">
        <div class="container">
            <!-- Page Header -->
            <div class="section-header">
                <h2 class="title mb-2">{{ translate('messages.vendor') }} <span
                        class="text--base">{{ translate('application') }}</span></h2>
            </div>
            @php($language = \App\CentralLogics\Helpers::get_business_settings('language'))
            <!-- End Page Header -->

            <!-- Stepper -->
            <div class="stepper">
                <div id="show-step1" class="stepper-item active">
                    <div class="step-name">{{ translate('General Info') }}</div>
                </div>
                <div class="stepper-item" id="show-step2">
                    <div class="step-name">{{ translate('Business Plan') }}</div>
                </div>
                <div class="stepper-item">
                    <div class="step-name">{{ translate('Complete') }}</div>
                </div>
            </div>
            <!-- Stepper -->


            <form enctype="multipart/form-data" id="form-id">
                <div id="reg-form-div">
                    <div class="card __card mb-3">
                        <div class="card-header">
                            <h5 class="card-title">
                                {{ translate('messages.vendor_info') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="card __card bg-F8F9FC mb-3">
                                <div class="card-body p-4">
                                    @if ($language)
                                        <div class="js-nav-scroller tabs-slide-wrap position-relative hs-nav-scroller-horizontal">
                                            <ul class="nav nav-tabs tabs-inner text-nowrap mb-4 store-apply-navs">
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
                                            <div class="arrow-area">
                                                <div class="button-prev align-items-center">
                                                    <button type="button"
                                                        class="btn btn-click-prev mr-auto border-0 btn-primary rounded-circle fs-12 p-2 d-center">
                                                        <i class="tio-chevron-left fs-24"></i>
                                                    </button>
                                                </div>
                                                <div class="button-next align-items-center">
                                                    <button type="button"
                                                        class="btn btn-click-next ml-auto border-0 btn-primary rounded-circle fs-12 p-2 d-center">
                                                        <i class="tio-chevron-right fs-24"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="row g-3">
                                        @if ($language)
                                            <div class="lang_form " id="default-form">
                                                <input type="hidden" name="lang[]" value="default">
                                                <div class="row g-2">
                                                    <div class="col-lg-6">
                                                        <div class="mb-4 mb-lg-0">
                                                            <div class="form-group">
                                                                <label class="input-label"
                                                                       for="default_name">{{ translate('messages.name') }}
                                                                    ({{ translate('messages.Default') }})<span
                                                                        class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" name="name[]"
                                                                       value="{{ old('name.0') }}" id="default_name"
                                                                       class="form-control __form-control"
                                                                       placeholder="{{ translate('messages.vendor_name') }}"
                                                                       maxlength="250" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="mb-4 mb-lg-0">
                                                            <div class="form-group mb-0">
                                                                <label class="input-label"
                                                                       for="address">{{ translate('messages.address') }}
                                                                    ({{ translate('messages.default') }})<span
                                                                        class="text-danger">*</span></label>
                                                                <textarea type="text" id="address" name="address[]"
                                                                          placeholder="{{ translate('Ex: ABC Company') }}"
                                                                          class="form-control __form-control">{{ old('address.0') }}</textarea>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                            @foreach ($language as $key => $lang)
                                                <div class="d-none lang_form" id="{{ $lang }}-form">
                                                    <input type="hidden" name="lang[]" value="{{ $lang }}">
                                                    <div class="row g-2">
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <label class="input-label"
                                                                       for="{{ $lang }}_name">{{ translate('messages.name') }}
                                                                    ({{ strtoupper($lang) }})
                                                                </label>
                                                                <input type="text" name="name[]"
                                                                       value="{{ old('name.' . $key + 1) }}"
                                                                       id="{{ $lang }}_name"
                                                                       class="form-control __form-control"
                                                                       placeholder="{{ translate('messages.vendor_name') }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <div class="form-group mb-0">
                                                                <label class="input-label"
                                                                       for="address{{ $lang }}">{{ translate('messages.address') }}
                                                                    ({{ strtoupper($lang) }})
                                                                </label>
                                                                <textarea type="text" id="address{{ $lang }}"
                                                                          name="address[]"
                                                                          placeholder="{{ translate('Ex: ABC Company') }}"
                                                                          class="form-control __form-control">{{ old('address.' . $key + 1) }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif

                                    </div>
                                </div>
                            </div>
                            @php($zones = \App\Models\Zone::active()->get(['id', 'name']))
                            <div class="row g-4 mb-30">
                                <div class="col-lg-6">
                                    <div class="form-group mb-4">
                                        <label class="input-label"
                                               title="{{ translate('messages.Select the zone from where the business will be operated') }}"
                                               for="choice_zones">{{ translate('messages.business_zone') }}<span
                                                class="text-danger">*</span> <span class="form-label-secondary"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="right"
                                                                                   data-original-title="{{ translate('messages.Select the zone from where the business will be operated') }}"><img
                                                    src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                                    alt="{{ translate('messages.Select the zone from where the business will be operated') }}"></span></label>
                                        <select name="zone_id" id="choice_zones" required
                                                class="form-control __form-control js-select2-custom js-example-basic-single"
                                                data-placeholder="{{ translate('messages.select_zone') }}">
                                            <option value="" selected disabled>
                                                {{ translate('messages.select_zone') }}</option>
                                            @foreach ($zones as $zone)
                                                @if (isset(auth('admin')->user()->zone_id))
                                                    @if (auth('admin')->user()->zone_id == $zone->id)
                                                        <option value="{{ $zone->id }}" selected>{{ $zone->name }}
                                                        </option>
                                                    @endif
                                                @else
                                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-4 overflow-hidden">
                                        <label for="module_id"
                                               class="input-label">{{ translate('messages.business_module') }}<span
                                                class="text-danger">*</span>
                                            <small
                                                class="text-danger">({{ translate('messages.Select_zone_first') }}
                                                )</small></label>
                                        <select name="module_id" required id="module_id"
                                                class="js-data-example-ajax form-control __form-control overflow-hidden"
                                                data-placeholder="{{ translate('messages.select_module') }}">
                                        </select>
                                    </div>
                                    <div class="form-group mb-4 pickup-zone-container pickup-zone-tag"
                                         id="pickup-zone-container">
                                        <label class="input-label"
                                               title="{{ translate('messages.Select zones from where customer can choose their pickup locations for trip booking') }}"
                                               for="choice_zones">{{ translate('messages.pickup_zone') }}<span
                                                class="text-danger">*</span> <span class="form-label-secondary"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="right"
                                                                                   data-original-title="{{ translate('messages.Select zones from where customer can choose their pickup locations for trip booking') }}"><img
                                                    src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                                    alt="{{ translate('messages.Select zones from where customer can choose their pickup locations for trip booking') }}"></span></label>
                                        <select name="pickup_zone_id[]" required class="form-control multiple-select2"
                                                data-placeholder="{{ translate('messages.select_zone') }}"
                                                multiple="multiple">
                                            <option value="" disabled>
                                                {{ translate('messages.select_zone') }}</option>
                                            @foreach ($zones as $zone)
                                                @if (isset(auth('admin')->user()->zone_id))
                                                    @if (auth('admin')->user()->zone_id == $zone->id)
                                                        <option value="{{ $zone->id }}" selected>{{ $zone->name }}
                                                        </option>
                                                    @endif
                                                @else
                                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="input-label module-select-time"
                                               for="minimum_delivery_time">{{ translate('messages.approx_delivery_time') }}
                                            <span
                                                class="text-danger">*</span></label>
                                        <div class=" __form-control custom-group-btn">
                                            <div class="item flex-sm-grow-1">
                                                <div class="d-flex align-items-center gap-1">
                                                    <label class="floating-label"
                                                           for="min">{{ translate('messages.min') }}:</label>
                                                    <input type="number" id="minimum_delivery_time"
                                                           name="minimum_delivery_time"
                                                           class="form-control p-0 border-0"
                                                           placeholder="10" value="{{ old('minimum_delivery_time') }}">
                                                </div>
                                            </div>
                                            <div class="item flex-sm-grow-1">
                                                <div class="d-flex align-items-center gap-1">
                                                    <label class="floating-label"
                                                           for="max">{{ translate('messages.max') }}:</label>
                                                    <input type="number" name="maximum_delivery_time"
                                                           id="max_delivery_time" class="form-control p-0 border-0"
                                                           placeholder="20" value="{{ old('maximum_delivery_time') }}">
                                                </div>
                                            </div>
                                            <div class="item flex-shrink-0">
                                                <select name="delivery_time_type"
                                                        class="form-select custom-select border-0" required>
                                                    <option value="min">{{ translate('messages.minutes') }}
                                                    </option>
                                                    <option value="hours">{{ translate('messages.hours') }}</option>
                                                    <option value="days">{{ translate('messages.days') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="rounded mb-3 map_custom-controls position-relative">
                                        <input id="pac-input" class="controls rounded initial-8" title="{{translate('messages.search_your_location_here')}}" type="text" placeholder="{{translate('messages.search_here')}}"/>
                                        <div class="h-280" id="map"></div>


                                            <div class="d-flex bg-white align-items-center gap-1 laglng-controller">
                                                <div id="latlng" class="d-flex">
                                                    <input type="text" class="border-0 outline-0" id="latitude" name="latitude" placeholder="{{ translate('messages.Ex:_-94.22213') }} " value="{{ old('latitude') }}" required readonly>
                                                    <span class="text-gray1">|</span>
                                                    <input type="text" class="border-0 outline-0" name="longitude" placeholder="{{ translate('messages.Ex:_103.344322') }} "   id="longitude" value="{{ old('longitude') }}" required readonly>
                                                </div>
                                            </div>
                                            <div id="outOfZone" class="map-alert bg-dark d-flex align-items-center rounded-8 py-2 px-2 fs-12 text-white mb-2 text-center">
                                            <img class="" src="{{asset('public/assets/admin/img/icons/warning-cus.png')}}" alt="img"> {{ translate('messages.Please place the marker inside the available zones.') }}
                                            </div>

                                    </div>
                                </div>
                                <div class="d-flex flex-column text-sm-start text-center flex-sm-row align-items-sm-start align-items-center gap-4">
                                        <div class="form-group col-lg-3 d-flex flex-column justify-content-between">
                                            <label class="input-label pt-2 mb-2">
                                                <div class="lh-1">{{ translate('messages.cover') }}<span
                                                        class="text-danger">*</span></div>
                                                <div class="fs-12 opacity-70">
                                                    {{ translate(IMAGE_FORMAT.' ' . 'Less Than 2MB') }}
                                                    <strong> {{ translate('(Ratio 2:1)') }}
                                                    </strong>
                                                </div>
                                            </label>
                                            <label class="image--border position-relative h-110 min-w-220 max-w-220 mx-mobile-auto">
                                                <img class="__register-img h-110" id="coverImageViewer"
                                                     src="{{ asset('public/assets/admin/img/upload-img.png') }}"
                                                     alt="Product thumbnail" style="display: none"/>
                                                <div class="upload-file__textbox p-2 h-100">
                                                    <img width="34" height="34"
                                                         src="{{ asset('public/assets/admin/img/document-upload.png') }}"
                                                         alt="" class="svg">
                                                    <h6 class="mt-2 text-center font-semibold fs-12">
                                                        <span
                                                            class="text-info">{{ translate('messages.Click to upload') }}</span>
                                                        <br>
                                                        {{ translate('messages.or drag and drop') }}
                                                    </h6>
                                                </div>
                                                <div class="icon-file-group d-none">
                                                    <div class="icon-file">
                                                        <input type="file" name="cover_photo" id="coverImageUpload"
                                                               class="form-control __form-control"
                                                               accept="{{ IMAGE_EXTENSION }}">
                                                        <img src="{{ asset('public/assets/admin/img/pen.png') }}"
                                                             alt="">
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="form-group d-flex flex-column justify-content-between">
                                            <label class="input-label pt-2 mb-2">
                                                <div class="lh-1">{{ translate('messages.logo') }}<span
                                                        class="text-danger">*</span></div>
                                                <div class="fs-12 opacity-70">
                                                    {{ translate(IMAGE_FORMAT.' ' . 'Less Than 2MB') }}
                                                    <strong> {{ translate('(Ratio 1:1)') }}
                                                    </strong>
                                                </div>
                                            </label>
                                            <label
                                                class="image--border position-relative img--100px w-100 h-110 max-w-110 mx-mobile-auto">
                                                <img class="__register-img h-110" id="logoImageViewer"
                                                     src="{{ asset('public/assets/admin/img/upload-img.png') }}"
                                                     alt="Product thumbnail" style="display: none"/>
                                                <div class="upload-file__textbox p-2 h-100">
                                                    <img width="34" height="34"
                                                         src="{{ asset('public/assets/admin/img/document-upload.png') }}"
                                                         alt="" class="svg">
                                                    <h6 class="mt-2 text-center font-semibold fs-12">
                                                        <span
                                                            class="text-info">{{ translate('messages.Click to upload') }}</span>
                                                        <br>
                                                        {{ translate('messages.or drag and drop') }}
                                                    </h6>
                                                </div>
                                                <div class="icon-file-group d-none">
                                                    <div class="icon-file">
                                                        <input type="file" name="logo" id="customFileEg1"
                                                               class="form-control __form-control"
                                                               accept="{{ IMAGE_EXTENSION }}">
                                                        <img src="{{ asset('public/assets/admin/img/pen.png') }}"
                                                             alt="">
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                            </div>
                            <div class="card __card bg-F8F9FC mb-4">
                                <div class="card-header">
                                    <div>
                                        <h5 class="card-title mb-4">
                                            {{ translate('messages.owner_information') }}
                                        </h5>
                                        <p class="fs-12 mb-0">
                                            {{ translate('messages.Insert_Owner\'s_General_Information') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-4 col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="input-label"
                                                       for="f_name">{{ translate('messages.first_name') }}<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" id="f_name" name="f_name"
                                                       class="form-control __form-control"
                                                       placeholder="{{ translate('messages.first_name') }}"
                                                       value="{{ old('f_name') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="input-label"
                                                       for="l_name">{{ translate('messages.last_name') }}<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" id="l_name" name="l_name"
                                                       class="form-control __form-control"
                                                       placeholder="{{ translate('messages.last_name') }}"
                                                       value="{{ old('l_name') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4 col-sm-12">
                                            <div class="form-group">
                                                <label class="input-label"
                                                       for="phone">{{ translate('messages.phone') }}<span
                                                        class="text-danger">*</span></label>
                                                <input type="tel" id="phone" name="phone"
                                                       class="form-control __form-control"
                                                       placeholder="{{ translate('messages.Ex:') }} 017********"
                                                       value="{{ old('phone') }}" required>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-20 mb-3">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h4 class="fs-5 mb-2">{{ translate('Business TIN') }}</h4>
                                        {{-- <p class="fz-12px mb-0">{{translate('Lorem ipsum dolor sit amet, consectetur adipiscing elit.')}}</p> --}}
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-8 col-xxl-8">
                                            <div class="card __card bg-F8F9FC rounded p-20 h-100">
                                                <div class="card-body">
                                                    <div class="form-group mb-3">
                                                        <label class="input-label mb-2 d-block title-clr fw-normal"
                                                               for="exampleFormControlInput1">{{ translate('Taxpayer Identification Number(TIN)') }}
                                                        </label>
                                                        <input type="text" name="tin"
                                                               placeholder="{{ translate('Type Your Taxpayer Identification Number(TIN)') }}"
                                                               class=" form-control __form-control">
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <label class="input-label mb-2 d-block title-clr fw-normal"
                                                               for="exampleFormControlInput1">{{ translate('Expire Date') }}
                                                        </label>
                                                        <input type="date" name="tin_expire_date"
                                                               class="form-control __form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xxl-4">
                                            <div class="bg--secondary rounded p-20 h-100 single-document-uploaderwrap">
                                                <div
                                                    class="d-flex align-items-center gap-1 justify-content-between mb-20 mb-4">
                                                    <div>
                                                        <h4 class="mb-2 fs-5">{{ translate('TIN Certificate') }}</h4>
                                                        <p class="fs-6 mb-0">
                                                            {{ translate('pdf, doc, jpg. File size : max 2 MB') }}</p>
                                                    </div>
                                                    <div class="d-flex gap-3 align-items-center">
                                                        <button type="button" id="doc_edit_btn"
                                                            data-default-image="{{ asset('public/assets/admin/img/doc-uploaded.png') }}"
                                                                class="w-30px h-30 min-w-30px rounded d-flex align-items-center justify-content-center action-btn btn cmn--btn px-3 icon-btn">
                                                            <i class="tio-edit"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div id="file-assets"
                                                         data-picture-icon="{{ asset('public/assets/admin/img/picture.svg') }}"
                                                         data-document-icon="{{ asset('public/assets/admin/img/document.svg') }}"
                                                         data-blank-thumbnail="{{ asset('public/assets/admin/img/picture.svg') }}">
                                                    </div>
                                                    <!-- Upload box -->
                                                    <div class="d-flex justify-content-center" id="pdf-container">
                                                        <div class="document-upload-wrapper" id="doc-upload-wrapper">
                                                            <input type="file" name="tin_certificate_image"
                                                                   class="document_input"
                                                                   accept=".doc, .pdf, .jpg, .png, .jpeg">
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
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card __card bg-F8F9FC mb-3">
                                <div class="card-header">
                                    <div>
                                        <h5 class="card-title mb-2">
                                            {{ translate('messages.account_information') }}
                                        </h5>
                                        <p class="fs-12 mb-0">
                                            {{ translate('Insert_Owner\'s_account_information') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-4 col-sm-12 col-lg-4">
                                            <div class="form-group">
                                                <label class="input-label"
                                                       for="email">{{ translate('messages.email') }}<span
                                                        class="text-danger">*</span></label>
                                                <input type="email" id="email" name="email"
                                                       class="form-control __form-control"
                                                       placeholder="{{ translate('messages.Ex:') }} ex@example.com"
                                                       value="{{ old('email') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12 col-lg-4">
                                            <div class="form-group">
                                                <label class="input-label"
                                                       title="{{ translate('messages.Must_contain_at_least_one_number_and_one_uppercase_and_lowercase_letter_and_symbol,_and_at_least_8_or_more_characters') }}"
                                                       for="exampleInputPassword">{{ translate('messages.password') }}
                                                    <span
                                                        class="text-danger">*</span>
                                                    &nbsp;
                                                    <span class="form-label-secondary" data-toggle="tooltip"
                                                          data-placement="right"
                                                          data-original-title="{{ translate('messages.Must_contain_at_least_one_number_and_one_uppercase_and_lowercase_letter_and_symbol,_and_at_least_8_or_more_characters') }}"><img
                                                            src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                                            alt="{{ translate('messages.Must_contain_at_least_one_number_and_one_uppercase_and_lowercase_letter_and_symbol,_and_at_least_8_or_more_characters') }}"></span>

                                                </label>
                                                <label class="position-relative m-0 d-block">
                                                    <input type="password" name="password"
                                                           placeholder="{{ translate('messages.password_length_placeholder', ['length' => '8+']) }}"
                                                           class="form-control __form-control form-control __form-control-user"
                                                           minlength="6" id="exampleInputPassword" required
                                                           value="{{ old('password') }}">
                                                    <span class="show-password">
                                                        <span class="icon-2">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                 viewBox="0 0 24 24" stroke-width="1.5"
                                                                 stroke="currentColor" class="size-6">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                                            </svg>
                                                        </span>
                                                        <span class="icon-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                 viewBox="0 0 24 24" stroke-width="1.5"
                                                                 stroke="currentColor" class="size-6">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                                            </svg>
                                                        </span>
                                                    </span>
                                                </label>
                                                <div id="password-feedback" class="pass password-feedback">
                                                    {{ translate('messages.password_not_matched') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-12 col-lg-4">
                                            <div class="form-group">
                                                <label class="input-label"
                                                       for="exampleRepeatPassword">{{ translate('messages.confirm_password') }}
                                                    <span
                                                        class="text-danger">*</span></label>
                                                <label class="position-relative m-0 d-block">
                                                    <input type="password" name="confirm-password"
                                                           class="form-control __form-control form-control __form-control-user"
                                                           minlength="6" id="exampleRepeatPassword"
                                                           placeholder="{{ translate('messages.password_length_placeholder', ['length' => '8+']) }}"
                                                           required value="{{ old('confirm-password') }}">
                                                    <span class="show-password">
                                                        <span class="icon-2">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                 viewBox="0 0 24 24" stroke-width="1.5"
                                                                 stroke="currentColor" class="size-6">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                                            </svg>
                                                        </span>
                                                        <span class="icon-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                 viewBox="0 0 24 24" stroke-width="1.5"
                                                                 stroke="currentColor" class="size-6">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                                            </svg>
                                                        </span>
                                                    </span>
                                                </label>
                                                <div class="pass invalid-feedback">
                                                    {{ translate('messages.password_not_matched') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-md-6 col-lg-4">
                                            @php($recaptcha = \App\CentralLogics\Helpers::get_business_settings('recaptcha'))
                                            @if (isset($recaptcha) && $recaptcha['status'] == 1)
                                                <input type="hidden" name="g-recaptcha-response"
                                                       id="g-recaptcha-response">
                                            @else
                                                <div class="row g-3">
                                                    <div class="col-6">
                                                        <input type="text" class="form-control"
                                                               name="custome_recaptcha" id="custome_recaptcha" required
                                                               placeholder="{{ translate('Enter recaptcha value') }}"
                                                               autocomplete="off"
                                                               value="{{ env('APP_DEBUG') ? session('six_captcha') : '' }}">
                                                    </div>
                                                    <div class="col-6 recap-img-div">
                                                        <img src="{!! $custome_recaptcha->inline() ?? '' !!}"
                                                             alt="image"
                                                             class="recap-img"/>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end pt-4 d-flex flex-wrap justify-content-end gap-3">
                                <button type="reset" id='reset-btn'
                                        class="cmn--btn btn--secondary shadow-none rounded-md border-0 outline-0">{{ translate('Reset') }}</button>
                                <button
                                    type="{{ \App\CentralLogics\Helpers::subscription_check() == 1 ? 'button' : 'submit' }}"
                                    id="show-business-plan-div"
                                    class="cmn--btn rounded-md border-0 outline-0 btn-disable">{{ \App\CentralLogics\Helpers::subscription_check() == 1 ? translate('Next') : translate('messages.submit') }}</button>
                            </div>
                        </div>
                    </div>

                </div>

                @if (\App\CentralLogics\Helpers::subscription_check())
                    <div class="d-none" id="business-plan-div">
                        <div class="card __card mb-3">
                            <div class="card-header border-0">
                                <h5 class="card-title text-center">
                                    {{ translate('Choose Your Business Plan') }}
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    @if (\App\CentralLogics\Helpers::commission_check())
                                        <div class="col-sm-6">
                                            <label class="plan-check-item pb-3 pb-sm-0">
                                                <input type="radio" name="business_plan" value="commission-base"
                                                       class="d-none" checked>
                                                <div class="plan-check-item-inner">
                                                    <div
                                                        class="d-flex gap-3 justify-content-between align-items-center mb-10">
                                                        <h5 class="mb-0">{{ translate('Commision_Base') }}</h5>
                                                        <span class="checkmark">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                 height="16" fill="currentColor" class="bi bi-check2"
                                                                 viewBox="0 0 16 16">
                                                                <path
                                                                    d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <p>
                                                        {{ translate('vendor will pay') }} {{ $admin_commission }}%
                                                        {{ translate('commission to') }} {{ $business_name }}
                                                        {{ translate('from each order. You will get access of all the features and options  in vendor panel , app and interaction with user.') }}
                                                    </p>
                                                </div>
                                            </label>
                                        </div>
                                    @endif
                                    <div class="col-sm-6">
                                        <label class="plan-check-item">
                                            <input type="radio" name="business_plan" value="subscription-base"
                                                   class="d-none">
                                            <div class="plan-check-item-inner">
                                                <div
                                                    class="d-flex gap-3 justify-content-between align-items-center mb-10">
                                                    <h5 class="mb-0">{{ translate('Subscription_Base') }}</h5>
                                                    <span class="checkmark">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                             height="16" fill="currentColor" class="bi bi-check2"
                                                             viewBox="0 0 16 16">
                                                            <path
                                                                d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <p>
                                                    {{ translate('Run vendor by puchasing subsciption packages. You will have access the features of in vendor panel , app and interaction with user according to the subscription packages.') }}
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div id="subscription-plan">
                                    <br>
                                    <div class="card-header px-0 m-0 border-0">
                                        <h5 class="card-title text-center">
                                            {{ translate('Choose Subscription Package') }}
                                        </h5>
                                    </div>
                                    <div id='show_sub_packages'>
                                        @include('vendor-views.auth._package_data', [
                                            'packages' => $packages,
                                        ])
                                    </div>


                                </div>
                            </div>
                            <div class="text-end pt-5 d-flex flex-wrap p-4 justify-content-end gap-3">
                                <button type="button" id="back-to-form"
                                        class="cmn--btn btn--secondary shadow-none rounded-md border-0 outline-0">{{ translate('Back') }}</button>
                                <button type="submit"
                                        class="cmn--btn rounded-md border-0 outline-0 btn-disable">{{ translate('Next') }}</button>
                            </div>
                        </div>
                    </div>
                @endif
            </form>
        </div>


    </section>

<div class="d-none" id="default-text-data"
     data-default-filesize="{{ translate('File size must be less than') }}"
     data-default-allowedformat="{{ translate('Invalid file type. Allowed: PDF, DOC, JPG, PNG') }}">
</div>
@endsection
@push('script_2')

    @php($default_location = \App\Models\BusinessSetting::where('key', 'default_location')->first())
    @php($default_location = $default_location->value ? json_decode($default_location->value, true) : 0)

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
            oldLat: parseFloat("{{ old('latitude') }}"),
            oldLng: parseFloat("{{ old('longitude') }}"),
            oldZoneId: "{{ old('zone_id') }}",
            oldAddress: @json(old('address.0')),
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
    <script src="{{ asset('public/assets/admin/js/file-preview/store-join-us.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/view-pages/map-functionality.js') }}"></script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ \App\CentralLogics\Helpers::get_business_settings('map_api_key') }}&libraries=drawing,places,marker,geometry&v=3.61&language={{ str_replace('_', '-', app()->getLocale()) }}&callback=initMap"
        async defer>
    </script>


    @if (isset($recaptcha) && $recaptcha['status'] == 1)
        <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptcha['site_key'] }}"></script>
    @endif


<script>
$("#form-id").on('submit', function(e) {
    e.preventDefault();

    @if (isset($recaptcha) && $recaptcha['status'] == 1)
    grecaptcha.ready(function() {
        grecaptcha.execute('{{ $recaptcha['site_key'] }}', {action: 'submit'}).then(function(token) {

            if ($("#g-recaptcha-response").length === 0) {
                $('<input>').attr({
                    type: 'hidden',
                    id: 'g-recaptcha-response',
                    name: 'g-recaptcha-response',
                    value: token
                }).appendTo('#form-id');
            } else {
                $("#g-recaptcha-response").val(token);
            }

            submitForm();
        });
    });
    @else
    submitForm();
    @endif
});

function submitForm() {

    const radios = document.querySelectorAll('input[name="business_plan"]');
    let selectedValue = null;
    for (const radio of radios) {
        if (radio.checked) {
            selectedValue = radio.value;
            break;
        }
    }

    if (!selectedValue) {
        toastr.error("{{ translate('messages.please_select_business_plan') }}");
        return;
    }

    if (selectedValue === 'subscription-base') {
        const package_radios = document.querySelectorAll('input[name="package_id"]');
        let selectedpValue = null;
        for (const pradio of package_radios) {
            if (pradio.checked) {
                selectedpValue = pradio.value;
                break;
            }
        }

        if (!selectedpValue) {
            toastr.error("{{ translate('You_must_select_a_package') }}");
            return;
        }
    }

    $('.btn-disable').attr('disabled', true);

    let formData = new FormData(document.getElementById('form-id'));
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    });

    $.post({
        url: '{{ route('restaurant.store') }}',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $('#loading').show();
        },
        success: function (data) {
            $('#loading').hide();
            if (data.errors) {
                $('.btn-disable').attr('disabled', false);
                for (let i = 0; i < data.errors.length; i++) {
                    toastr.error(data.errors[i].message, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            } else {
                toastr.success("{{ translate('your_store_registration_is_successful') }}", {
                    CloseButton: true,
                    ProgressBar: true
                });
                setTimeout(function () {
                    location.href = data.redirect_url;
                }, 1000);
            }
        }
    });
}
</script>



    <script>

        $(document).on('keyup', 'input[name="password"]', function () {
            const password = $(this).val();
            const feedback = $('#password-feedback');

            const minLength = password.length >= 8;
            const hasLowerCase = /[a-z]/.test(password);
            const hasUpperCase = /[A-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSymbol = /[!@#$%^&*(),.?":{}|<>]/.test(password);

            if (minLength && hasLowerCase && hasUpperCase && hasNumber && hasSymbol) {
                feedback.text("{{ translate('Password is valid') }}");
                feedback.removeClass('invalid').addClass('valid');
            } else {
                feedback.text("{{ translate('Password format is invalid') }}");
                feedback.removeClass('valid').addClass('invalid');
            }
        });



        $('#show-business-plan-div').on('click', function (e) {
            const logo = $('input[name="logo"]')[0];
            const cover = $('input[name="cover_photo"]')[0];
            const tin_certificate_image = $('input[name="tin_certificate_image"]')[0];

            const maxFileSize = 2 * 1024 * 1024; // 2MB in bytes

            if (!$('#default_name').val()) {
                toastr.error("{{ translate('Vendor_name_is_required') }}");
                e.preventDefault();
            } else if (!$('#address').val()) {
                toastr.error("{{ translate('Vendor_address_is_required') }}");
                e.preventDefault();
            } else if (!logo.files.length) {
                toastr.error("{{ translate('Vendor_logo_required') }}");
                e.preventDefault();
            } else if (!cover.files.length) {
                toastr.error("{{ translate('Vendor_cover_photo_required') }}");
                e.preventDefault();
            } else if (logo.files[0].size > maxFileSize) {
                toastr.error("{{ translate('Vendor_logo_must_be_less_than_2MB') }}");
                e.preventDefault();
            } else if (tin_certificate_image.files.length && tin_certificate_image.files[0].size > maxFileSize) {
                toastr.error("{{ translate('Tin_certificate_must_be_less_than_2MB') }}");
                e.preventDefault();
            } else if (cover.files[0].size > maxFileSize) {
                toastr.error("{{ translate('Vendor_cover_photo_must_be_less_than_2MB') }}");
                e.preventDefault();
            } else if (!$('#choice_zones').val()) {
                toastr.error("{{ translate('You_must_select_a_zone') }}");
                e.preventDefault();
            } else if (!$('#module_id').val()) {
                toastr.error("{{ translate('You_must_select_a_module') }}");
                e.preventDefault();
            } else if (!$('#latitude').val() || !$('#longitude').val()) {
                toastr.error("{{ translate('Must_click_on_the_map_for_lat/long') }}");
                e.preventDefault();
            } else if (!$('#minimum_delivery_time').val()) {
                toastr.error("{{ translate('minimum_time_is_required') }}");
                e.preventDefault();
            } else if (!$('#max_delivery_time').val()) {
                toastr.error("{{ translate('max_time_is_required') }}");
                e.preventDefault();
            } else if (!$('#f_name').val()) {
                toastr.error("{{ translate('first_name_is_required') }}");
                e.preventDefault();
            } else if (!$('#l_name').val()) {
                toastr.error("{{ translate('last_name_is_required') }}");
                e.preventDefault();
            } else if ($('#phone').val().length < 5) {
                toastr.error("{{ translate('valid_phone_number_is_required') }}");
                e.preventDefault();
            } else if (!$('#email').val()) {
                toastr.error("{{ translate('email_is_required') }}");
                e.preventDefault();
            } else if (!$('#exampleInputPassword').val()) {
                toastr.error("{{ translate('password_is_required') }}");
                e.preventDefault();
            } else if ($('#exampleRepeatPassword').val() !== $('#exampleInputPassword').val()) {
                toastr.error("{{ translate('confirm_password_does_not_match') }}");
                e.preventDefault();
            } else if (!isPasswordStrong($('#exampleRepeatPassword').val()) && !isPasswordStrong($('#exampleInputPassword').val())) {
                toastr.error("{{ translate('Password format is invalid') }}");
                e.preventDefault();
            } else {
                e.preventDefault();
                $.get({
                    url: '{{ route('admin.zone.check-location') }}',
                    dataType: 'json',
                    data: {
                        zone_id: $('#choice_zones').val(),
                        latitude: $('#latitude').val(),
                        longitude: $('#longitude').val()
                    },
                    beforeSend: function () {
                        $('#loading').show();
                    },
                    success: function (data) {
                        $('#loading').hide();
                        if (data.errors) {
                            for (let i = 0; i < data.errors.length; i++) {
                                toastr.error(data.errors[i].message, {
                                    CloseButton: true,
                                    ProgressBar: true
                                });
                            }
                        } else {
                            @if (isset($recaptcha) && $recaptcha['status'] == 1)
                                if (typeof grecaptcha === 'undefined') {
                                    toastr.error('Invalid recaptcha key provided. Please check the recaptcha configuration.');
                                    return;
                                }
                                grecaptcha.ready(function () {
                                    grecaptcha.execute('{{$recaptcha['site_key']}}', {action: 'submit'}).then(function (token) {
                                        $('#g-recaptcha-response').value = token;

                                    });
                                });
                                window.onerror = function (message) {
                                    var errorMessage = 'An unexpected error occurred. Please check the recaptcha configuration';
                                    if (message.includes('Invalid site key')) {
                                        errorMessage = 'Invalid site key provided. Please check the recaptcha configuration.';
                                    } else if (message.includes('not loaded in api.js')) {
                                        errorMessage = 'reCAPTCHA API could not be loaded. Please check the recaptcha API configuration.';
                                    }
                                    toastr.error(errorMessage)
                                    return true;
                                };
                            @endif


                            @if (\App\CentralLogics\Helpers::subscription_check())
                            $('#business-plan-div').removeClass('d-none');
                            $('#reg-form-div').addClass('d-none');
                            $('#show-step2').addClass('active');
                            $('#show-step1').removeClass('active');
                            $(window).scrollTop(0);
                            @endif
                        }
                    },
                    error: function () {
                        $('#loading').hide();
                    }
                });
            }
        });

        function isPasswordStrong(password) {
            const minLength = password.length >= 8;
            const hasLowerCase = /[a-z]/.test(password);
            const hasUpperCase = /[A-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSymbol = /[!@#$%^&*(),.?":{}|<>]/.test(password);
            return minLength && hasLowerCase && hasUpperCase && hasNumber && hasSymbol;
        }


        $('#back-to-form').on('click', function () {
            $('#business-plan-div').addClass('d-none');
            $('#reg-form-div').removeClass('d-none');
            $('#show-step1').addClass('active');
            $('#show-step2').removeClass('active');
            $(window).scrollTop(0);
        })

    </script>
    <script src="{{ asset('public/assets/landing/js/select2.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            function handleImageUpload(inputSelector, imgViewerSelector, textBoxSelector) {
                const inputElement = $(inputSelector);
                inputElement.on('change', function () {
                    const file = this.files[0];
                    if (file) {

                            let acceptAttr = $(this).attr('accept') || '';
                            let validTypes = [];

                            if (acceptAttr) {
                                validTypes = acceptAttr.split(',').map(type => type.trim().toLowerCase());
                            }

                            if (validTypes.length === 0) {
                                validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                            }

                            const fileType = file.type.toLowerCase();
                            const fileExt = '.' + file.name.split('.').pop().toLowerCase();

                            const isValidType = validTypes.some(type => {
                                if (type.endsWith('/*')) {
                                    return fileType.startsWith(type.replace('/*', ''));
                                }

                                if (type.startsWith('image/') || type.includes('/')) {
                                    return fileType === type;
                                }
                                return fileExt === type;
                            });

                            if (!isValidType) {
                                if (typeof toastr !== 'undefined') {
                                    toastr.error("{{ translate('messages.Invalid file type. Please upload a supported image.') }}");
                                }

                                $(this).val('');
                                $(imgViewerSelector)
                                    .attr('src', '{{ asset('public/assets/admin/img/upload-img.png') }}')
                                    .hide();
                                $(textBoxSelector).show();
                                return;
                            }

                        const maxSize = 2 * 1024 * 1024; // 2 MB in bytes
                        if (file.size > maxSize) {
                            if (typeof toastr !== 'undefined') {
                                toastr.error("{{ translate('messages.Image size must be less than 2 MB') }}");
                            }

                            $(this).val('');
                            $(imgViewerSelector)
                                .attr('src', '{{ asset('public/assets/admin/img/upload-img.png') }}')
                                .hide();
                            $(textBoxSelector).show();
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function (e) {
                            $(imgViewerSelector).attr('src', e.target.result).show();
                            $(textBoxSelector).hide();
                        };
                        reader.readAsDataURL(file);
                    } else {
                        $(imgViewerSelector)
                            .attr('src', '{{ asset('public/assets/admin/img/upload-img.png') }}')
                            .hide();
                        $(textBoxSelector).show();
                    }
                });

                // Handle drag-and-drop functionality
                const dropZone = inputElement.closest('.image--border');

                dropZone.on('dragover', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                });

                dropZone.on('dragleave', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                });

                dropZone.on('drop', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const file = e.originalEvent.dataTransfer.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            $(imgViewerSelector).attr('src', e.target.result).show();
                            $(textBoxSelector).hide();
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            handleImageUpload(
                '#coverImageUpload',
                '#coverImageViewer',
                '#coverImageViewer ~ .upload-file__textbox'
            );

            handleImageUpload(
                '#customFileEg1',
                '#logoImageViewer',
                '#logoImageViewer ~ .upload-file__textbox'
            );
        });
    </script>

    <script>
        $.fn.select2DynamicDisplay = function () {
            const limit = 10000;

            function updateDisplay($element) {
                var $rendered = $element
                    .siblings(".select2-container")
                    .find(".select2-selection--multiple")
                    .find(".select2-selection__rendered");
                var $container = $rendered.parent();
                var containerWidth = $container.width();
                var totalWidth = 0;
                var itemsToShow = [];
                var remainingCount = 0;

                // Get all selected items
                var selectedItems = $element.select2("data");

                var $tempContainer = $("<div>")
                    .css({
                        display: "inline-block",
                        padding: "0 15px",
                        "white-space": "nowrap",
                        visibility: "hidden",
                    })
                    .appendTo($container);

                selectedItems.forEach(function (item) {
                    var $tempItem = $("<span>")
                        .text(item.text)
                        .css({
                            display: "inline-block",
                            padding: "0 12px",
                            "white-space": "nowrap",
                        })
                        .appendTo($tempContainer);

                    var itemWidth = $tempItem.outerWidth(true);

                    if (totalWidth + itemWidth <= containerWidth - 40) {
                        totalWidth += itemWidth;
                        itemsToShow.push(item);
                    } else {
                        remainingCount = selectedItems.length - itemsToShow.length;
                        return false;
                    }
                });

                $tempContainer.remove();

                const $searchForm = $rendered.find(".select2-search");

                var html = "";
                itemsToShow.forEach(function (item) {
                    html += `<li class="name">
                                        <span>${item.text}</span>
                                        <span class="close-icon" data-id="${item.id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                            </svg>
                                        </span>
                                        </li>`;
                });
                if (remainingCount > 0) {
                    html += `<li class="ms-auto">
                                        <div class="more">+${remainingCount}</div>
                                        </li>`;
                }

                if (selectedItems.length < limit) {
                    html += $searchForm.prop("outerHTML");
                }

                $rendered.html(html);

                function debounce(func, wait) {
                    let timeout;
                    return function (...args) {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(this, args), wait);
                    };
                }

                $(".select2-search input").on(
                    "input",
                    debounce(function () {
                        const inputValue = $(this).val().toLowerCase();
                        const $listItems = $(".select2-results__options li");
                        let matches = 0;

                        $listItems.each(function () {
                            const itemText = $(this).text().toLowerCase();
                            const isMatch = itemText.includes(inputValue);
                            $(this).toggle(isMatch);
                            if (isMatch) matches++;
                        });

                        if (matches === 0) {
                            $(".select2-results__options").append(
                                '<li class="no-results">No results found</li>'
                            );
                        } else {
                            $(".no-results").remove();
                        }
                    }, 100)
                );

                $(".select2-search input").on("keydown", function (e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        const inputValue = $(this).val().toLowerCase();
                        const $listItems = $(".select2-results__options li:not(.no-results)");
                        const matchedItem = $listItems.filter(function () {
                            return $(this).text().toLowerCase() === inputValue;
                        });

                        if (matchedItem.length > 0) {
                            matchedItem.trigger("mouseup"); // Select the matched item
                        }

                        $(this).val("");
                    }
                });
            }

            return this.each(function () {
                var $this = $(this);

                $this.select2({
                    tags: true,
                    maximumSelectionLength: limit,
                });

                // Bind change event to update display
                $this.on("change", function () {
                    updateDisplay($this);
                });

                // Initial display update
                updateDisplay($this);

                $(window).on("resize", function () {
                    updateDisplay($this);
                });
                $(window).on("load", function () {
                    updateDisplay($this);
                });

                // Handle the click event for the remove icon
                $(document).on(
                    "click",
                    ".select2-selection__rendered .close-icon",
                    function (e) {
                        e.stopPropagation();
                        var $removeIcon = $(this);
                        var itemId = $removeIcon.data("id");
                        var $this2 = $removeIcon
                            .closest(".select2")
                            .siblings(".multiple-select2");
                        $this2.val(
                            $this2.val().filter(function (id) {
                                return id != itemId;
                            })
                        );
                        $this2.trigger("change");
                    }
                );
            });
        };
        $(".multiple-select2").select2DynamicDisplay();
    </script>

    <script>
        const container = document.querySelector('.tabs-inner');
        const btnPrevWrap = document.querySelector('.button-prev');
        const btnNextWrap = document.querySelector('.button-next');
        const item = document.querySelector('.tabs-slide_items');

        document.querySelectorAll('.tabs-slide_items').forEach(el => {
            el.style.flex = '0 0 auto';
        });
        function updateArrows() {
            if (!container || !btnPrevWrap || !btnNextWrap) return;

            const hasOverflow = container.scrollWidth > container.clientWidth;
            if (!hasOverflow) {
                btnPrevWrap.style.display = 'none';
                btnNextWrap.style.display = 'none';
                return;
            }
            const scrollLeft = container.scrollLeft;
            const maxScroll = container.scrollWidth - container.clientWidth;

            if (scrollLeft > 2) {
                btnPrevWrap.style.display = 'flex';
            } else {
                btnPrevWrap.style.display = 'none';
            }

            if (scrollLeft < maxScroll - 2) {
                btnNextWrap.style.display = 'flex';
            } else {
                btnNextWrap.style.display = 'none';
            }
        }
        document.querySelector('.btn-click-prev')?.addEventListener('click', () => {
            const itemWidth = item?.offsetWidth || 100;
            container.scrollBy({ left: -itemWidth, behavior: 'smooth' });
        });
        document.querySelector('.btn-click-next')?.addEventListener('click', () => {
            const itemWidth = item?.offsetWidth || 100;
            container.scrollBy({ left: itemWidth, behavior: 'smooth' });
        });

        container.addEventListener('scroll', updateArrows);
        ['load', 'resize'].forEach(evt => window.addEventListener(evt, updateArrows));
        new MutationObserver(updateArrows).observe(container, { childList: true, subtree: true });
        new ResizeObserver(updateArrows).observe(container);

        // Initial update
        updateArrows();




    </script>
@endpush
