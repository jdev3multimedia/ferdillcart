@extends('layouts.admin.app')

@section('title', translate('messages.react_landing_page'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header pb-0">
            <div class="d-flex flex-wrap justify-content-between">
                <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/landing.png')}}" class="w--20" alt="">
                </span>
                    <span>
                    {{ translate('messages.react_landing_page') }}
                </span>
                </h1>
                <div class="text--primary-2 py-1 d-flex flex-wrap align-items-center" type="button" data-toggle="modal"
                     data-target="#how-it-works">
                    <strong class="mr-2">{{translate('See_how_it_works!')}}</strong>
                    <div>
                        <i class="tio-info-outined"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-20 mt-2">
            <div class="js-nav-scroller hs-nav-scroller-horizontal">
                @include('admin-views.business-settings.landing-page-settings.top-menu-links.react-landing-page-links')
            </div>
        </div>
        <div class="card py-3 px-xxl-4 px-3 mb-20">
            <div class="d-flex flex-sm-nowrap flex-wrap gap-3 align-items-center justify-content-between">
                <div class="">
                    <h3 class="mb-1">{{ translate('User App Download Section') }}</h3>
                    <p class="mb-0 gray-dark fs-12">
                        {{ translate('See how your User App Download Section will look to customers.') }}
                    </p>
                </div>
                <div class="max-w-300px ml-sm-auto">
                    <button type="button" class="btn btn-outline-primary py-2 fs-12 px-3 offcanvas-trigger"
                            data-target="#user-downloadApp_section">
                        <i class="tio-invisible"></i> {{ translate('Section Preview') }}
                    </button>
                </div>
            </div>
        </div>
        @php($download_user_app_section_status = \App\Models\DataSetting::where('type', 'react_landing_page')->where('key', "download_user_app_section_status")->first())
        <div class="card py-3 px-xxl-4 px-3 mb-15 mt-4">
            <div class="row g-3 align-items-center justify-content-between">
                <div class="col-xxl-9 col-lg-8 col-md-7 col-sm-6">
                    <div class="">
                        <h3 class="mb-1">{{ translate('Show User App Download Section') }}</h3>
                        <p class="mb-0 gray-dark fs-12">
                            {{ translate('If you turn of the availability status, this section will not show in the website') }}
                        </p>
                    </div>
                </div>
                <div class="col-xxl-3 col-lg-4 col-md-5 col-sm-6">
                    <div class="py-2 px-3 rounded d-flex justify-content-between border align-items-center w-300">
                        <h5 class="text-capitalize fw-normal mb-0">{{ translate('Status') }}</h5>

                        <form
                            action="{{ route('admin.business-settings.statusUpdate', ['type' => 'react_landing_page', 'key' => 'download_user_app_section_status']) }}"
                            method="get" id="CheckboxStatus_form">
                        </form>
                        <label class="toggle-switch toggle-switch-sm" for="CheckboxStatus">
                            <input type="checkbox" data-id="CheckboxStatus" data-type="status"
                                   data-image-on="{{ asset('/public/assets/admin/img/status-ons.png') }}"
                                   data-image-off="{{ asset('/public/assets/admin/img/off-danger.png') }}"
                                   data-title-on="{{ translate('Do you want turn on this section ?') }}"
                                   data-title-off="{{ translate('Do you want to turn off this section ?') }}"
                                   data-text-on="<p>{{ translate('If you turn on this section will be show in react landing page.') }}"
                                   data-text-off="<p>{{ translate('If you turn off this section will not be show in react landing page.') }}</p>"
                                   class="toggle-switch-input  status dynamic-checkbox" id="CheckboxStatus"
                                {{ $download_user_app_section_status?->value ? 'checked' : '' }}>
                            <span class="toggle-switch-label text">
                                <span class="toggle-switch-indicator"></span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-20">
            <form class="custom-validation"
                  action="{{ route('admin.business-settings.react-landing-page-settings', 'download-app-section') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="mb-20">
                        <h3 class="mb-1">{{ translate('User App Download Section Content ') }}</h3>
                        <p class="mb-0 fs-12">
                            {{ translate('Encourage users to download the app for a seamless experience and instant access.') }}
                        </p>
                    </div>
                    @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
                    @php($language = $language->value ?? null)
                    @php($defaultLang = str_replace('_', '-', app()->getLocale()))
                    @php($download_user_app_title = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'download_user_app_title')->first())
                    @php($download_user_app_sub_title = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'download_user_app_sub_title')->first())
                    @php($download_user_app_image = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'download_user_app_image')->first())
                    <div class="row g-3">
                        <div class="col-lg-8">
                            <div class="bg--secondary rounded h-100 p-xxl-4 p-3 mb-20">
                                @if($language)
                                    <ul class="nav nav-tabs mb-4 border-0">
                                        <li class="nav-item">
                                            <a class="nav-link lang_link active" href="#"
                                               id="default-link">{{translate('messages.default')}}</a>
                                        </li>
                                        @foreach (json_decode($language) as $lang)
                                            <li class="nav-item">
                                                <a class="nav-link lang_link" href="#"
                                                   id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                <div class="row g-3">
                                    @if ($language)
                                        <div class="col-md-12 lang_form default-form">
                                            <div class="row g-1">
                                                <div class="col-12">
                                                    <label for="download_user_app_title"
                                                           class="form-label">{{translate('Title')}}
                                                        ({{ translate('messages.default') }})
                                                        <span class="form-label-secondary" data-toggle="tooltip"
                                                              data-placement="right"
                                                              data-original-title="{{ translate('Write_the_title_within_100_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span><span class="form-label-secondary text-danger"
                                                             data-toggle="tooltip" data-placement="right"
                                                             data-original-title="{{ translate('messages.Required.')}}"> *
                                                </span>
                                                    </label>
                                                    <input id="download_user_app_title" type="text" maxlength="100"
                                                           name="download_user_app_title[]"
                                                           value="{{ $download_user_app_title?->getRawOriginal('value') ?? '' }}"
                                                           class="form-control"
                                                           placeholder="{{translate('messages.title_here...')}}">
                                                    <span
                                                        class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                                                </div>
                                                <div class="col-12">
                                                    <label for="download_user_app_sub_title"
                                                           class="form-label">{{translate('Sub Title')}}
                                                        ({{ translate('messages.default') }})
                                                        <span class="form-label-secondary" data-toggle="tooltip"
                                                              data-placement="right"
                                                              data-original-title="{{ translate('Write_the_sub_title_within_200_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span><span class="form-label-secondary text-danger"
                                                             data-toggle="tooltip" data-placement="right"
                                                             data-original-title="{{ translate('messages.Required.')}}"> *
                                                </span>
                                                    </label>
                                                    <input id="download_user_app_sub_title" type="text" maxlength="200"
                                                           name="download_user_app_sub_title[]"
                                                           value="{{ $download_user_app_sub_title?->getRawOriginal('value') ?? '' }}"
                                                           class="form-control"
                                                           placeholder="{{translate('messages.sub_title_here...')}}">
                                                    <span
                                                        class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="lang[]" value="default">

                                        @foreach(json_decode($language) as $lang)
                                                <?php
                                                $download_user_app_title_translate = [];
                                                $download_user_app_sub_title_translate = [];

                                                if (isset($download_user_app_title->translations) && count($download_user_app_title->translations)) {
                                                    foreach ($download_user_app_title->translations as $t) {
                                                        if ($t->locale == $lang && $t->key == 'download_user_app_title') {
                                                            $download_user_app_title_translate[$lang]['value'] = $t->value;
                                                        }
                                                    }
                                                }

                                                if (isset($download_user_app_sub_title->translations) && count($download_user_app_sub_title->translations)) {
                                                    foreach ($download_user_app_sub_title->translations as $t) {
                                                        if ($t->locale == $lang && $t->key == 'download_user_app_sub_title') {
                                                            $download_user_app_sub_title_translate[$lang]['value'] = $t->value;
                                                        }
                                                    }
                                                }
                                                ?>
                                            <div class="col-md-12 d-none lang_form" id="{{$lang}}-form">
                                                <div class="row g-1">
                                                    <div class="col-12">
                                                        <label for="download_user_app_title{{$lang}}"
                                                               class="form-label">{{translate('Title')}}
                                                            ({{strtoupper($lang)}})<span class="form-label-secondary"
                                                                                         data-toggle="tooltip"
                                                                                         data-placement="right"
                                                                                         data-original-title="{{ translate('Write_the_title_within_100_characters') }}">
                                                        <i class="tio-info color-A7A7A7"></i>
                                                    </span>
                                                        </label>
                                                        <input id="download_user_app_title{{$lang}}" type="text"
                                                               maxlength="100"
                                                               name="download_user_app_title[]"
                                                               value="{{ $download_user_app_title_translate[$lang]['value'] ?? '' }}"
                                                               class="form-control"
                                                               placeholder="{{translate('messages.title_here...')}}">
                                                        <span
                                                            class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                                                    </div>
                                                    <div class="col-12">
                                                        <label for="download_user_app_sub_title{{$lang}}"
                                                               class="form-label">{{translate('Sub Title')}}
                                                            ({{strtoupper($lang)}})<span class="form-label-secondary"
                                                                                         data-toggle="tooltip"
                                                                                         data-placement="right"
                                                                                         data-original-title="{{ translate('Write_the_sub_title_within_200_characters') }}">
                                                        <i class="tio-info color-A7A7A7"></i>
                                                    </span>
                                                        </label>
                                                        <input id="download_user_app_sub_title{{$lang}}" type="text"
                                                               maxlength="200"
                                                               name="download_user_app_sub_title[]"
                                                               value="{{ $download_user_app_sub_title_translate[$lang]['value'] ?? '' }}"
                                                               class="form-control"
                                                               placeholder="{{translate('messages.sub_title_here...')}}">
                                                        <span
                                                            class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="lang[]" value="{{$lang}}">
                                        @endforeach
                                    @else
                                        <div class="col-12">
                                            <div class="mb-2">
                                                <label for="download_user_app_title"
                                                       class="form-label">{{translate('Title')}}</label>
                                                <input id="download_user_app_title" maxlength="100" type="text"
                                                       name="download_user_app_title[]" class="form-control"
                                                       placeholder="{{translate('messages.title_here...')}}">
                                                <span
                                                    class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                                            </div>
                                            <div class="mb-4">
                                                <label for="download_user_app_sub_title"
                                                       class="form-label">{{translate('Sub Title')}}</label>
                                                <input id="download_user_app_sub_title" maxlength="200" type="text"
                                                       name="download_user_app_sub_title[]" class="form-control"
                                                       placeholder="{{translate('messages.sub_title_here...')}}">
                                                <span
                                                    class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                            </div>
                                        </div>
                                        <input type="hidden" name="lang[]" value="default">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="bg--secondary h-100 rounded p-md-4 p-3 d-center">
                                <div class="text-center">
                                    <div class="mb-4">
                                        <h5 class="mb-1">{{ translate('Upload Image') }}</h5>
                                        <p class="mb-0 fs-12 gray-dark">
                                            {{ translate('Upload your User App Download Section  Image') }}</p>
                                    </div>
                                    <div class="mx-auto text-center error-wrapper">
                                        <div class="upload-file_custom ratio-1 h-100px">
                                            <input type="file" name="image"
                                                   class="upload-file__input single_file_input"
                                                   accept=".webp, .jpg, .jpeg, .png, .gif" {{ $download_user_app_image?->value ? '' : 'required' }}>
                                            <label class="upload-file__wrapper w-100 h-100 m-0">
                                                <div class="upload-file-textbox text-center">
                                                    <img width="22" class="svg"
                                                         src="{{asset('public/assets/admin/img/document-upload.svg')}}"
                                                         alt="img">
                                                    <h6 class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                        <span class="theme-clr">Click to upload</span>
                                                        <br>
                                                        Or drag and drop
                                                    </h6>
                                                </div>
                                                <img class="upload-file-img" loading="lazy" src="{{ $download_user_app_image?->value
    ? \App\CentralLogics\Helpers::get_full_url('download_user_app_image', $download_user_app_image->value, $download_user_app_image->storage[0]?->value ?? 'public', 'aspect_1')
    : '' }}" data-default-src="{{ $download_user_app_image?->value
    ? \App\CentralLogics\Helpers::get_full_url('download_user_app_image', $download_user_app_image->value, $download_user_app_image->storage[0]?->value ?? 'public', 'aspect_1')
    : ''  }}" alt="">
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

                                    <p class="fs-10 text-center mb-0 mt-lg-4 mt-3">
                                        {{ translate('JPG, JPEG, PNG size : Max 2 MB')}} <span
                                            class="font-medium text-title">{{ translate('(1:1)')}}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end mt-20">
                        <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                        <button type="submit" class="btn btn--primary mb-2">{{translate('Save')}}</button>
                    </div>
                </div>
            </form>
        </div>

        @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
        @php($language = $language->value ?? null)
        @php($defaultLang = str_replace('_', '-', app()->getLocale()))

        <div class="tab-content">
            <div class="tab-pane fade show active">
                @php($download_user_app_button_title = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'download_user_app_button_title')->first())
                @php($download_user_app_button_sub_title = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'download_user_app_button_sub_title')->first())
                @php($download_user_app_links = \App\Models\DataSetting::where(['key' => 'download_user_app_links', 'type' => 'react_landing_page'])->first())
                @php($download_user_app_links = isset($download_user_app_links->value) ? json_decode($download_user_app_links->value, true) : null)
                <form
                    action="{{ route('admin.business-settings.react-landing-page-settings', 'download-app-button-section') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-20">
                                <h3 class="mb-1">{{ translate('User App Download Section Button ') }}</h3>
                                <p class="mb-0 fs-12">
                                    {{ translate('Manage mobile app download area including QR codes and app store buttons.') }}
                                </p>
                            </div>
                            <div class="bg--secondary rounded p-xxl-4 p-3 mb-20">
                                @if($language)
                                    <ul class="nav nav-tabs mb-4 border-0">
                                        <li class="nav-item">
                                            <a class="nav-link lang_link active" href="#"
                                               id="default-link">{{translate('messages.default')}}</a>
                                        </li>
                                        @foreach (json_decode($language) as $lang)
                                            <li class="nav-item">
                                                <a class="nav-link lang_link" href="#"
                                                   id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                <div class="row g-4">
                                    <div class="col-12">
                                        @if ($language)
                                            <div class="col-md-12 lang_form default-form">
                                                <div class="row g-1">
                                                    <div class="col-12">
                                                        <label for="download_user_app_button_title"
                                                               class="form-label">{{translate('Title')}}
                                                            ({{ translate('messages.default') }})
                                                            <span class="form-label-secondary" data-toggle="tooltip"
                                                                  data-placement="right"
                                                                  data-original-title="{{ translate('Write_the_title_within_100_characters') }}">
                                                            <i class="tio-info color-A7A7A7"></i>
                                                        </span><span class="form-label-secondary text-danger"
                                                                     data-toggle="tooltip" data-placement="right"
                                                                     data-original-title="{{ translate('messages.Required.')}}"> *
                                                        </span>

                                                        </label>
                                                        <input id="download_user_app_button_title" type="text"
                                                               maxlength="100"
                                                               name="download_user_app_button_title[]"
                                                               value="{{ $download_user_app_button_title?->getRawOriginal('value') ?? '' }}"
                                                               class="form-control"
                                                               placeholder="{{translate('messages.title_here...')}}">
                                                        <span
                                                            class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                                                    </div>
                                                    <div class="col-12">
                                                        <label for="download_user_app_button_sub_title"
                                                               class="form-label">{{translate('Sub Title')}}
                                                            ({{ translate('messages.default') }})
                                                            <span class="form-label-secondary" data-toggle="tooltip"
                                                                  data-placement="right"
                                                                  data-original-title="{{ translate('Write_the_sub_title_within_200_characters') }}">
                                                            <i class="tio-info color-A7A7A7"></i>
                                                        </span><span class="form-label-secondary text-danger"
                                                                     data-toggle="tooltip" data-placement="right"
                                                                     data-original-title="{{ translate('messages.Required.')}}"> *
                                                        </span>

                                                        </label>
                                                        <input id="download_user_app_button_sub_title" type="text"
                                                               maxlength="200"
                                                               name="download_user_app_button_sub_title[]"
                                                               value="{{ $download_user_app_button_sub_title?->getRawOriginal('value') ?? '' }}"
                                                               class="form-control"
                                                               placeholder="{{translate('messages.sub_title_here...')}}">
                                                        <span
                                                            class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="lang[]" value="default">
                                            @foreach(json_decode($language) as $lang)
                                                    <?php
                                                    $download_user_app_button_title_translate = [];
                                                    $download_user_app_button_sub_title_translate = [];

                                                    if (isset($download_user_app_button_title->translations) && count($download_user_app_button_title->translations)) {
                                                        foreach ($download_user_app_button_title->translations as $t) {
                                                            if ($t->locale == $lang && $t->key == 'download_user_app_button_title') {
                                                                $download_user_app_button_title_translate[$lang]['value'] = $t->value;
                                                            }
                                                        }
                                                    }

                                                    if (isset($download_user_app_button_sub_title->translations) && count($download_user_app_button_sub_title->translations)) {
                                                        foreach ($download_user_app_button_sub_title->translations as $t) {
                                                            if ($t->locale == $lang && $t->key == 'download_user_app_button_sub_title') {
                                                                $download_user_app_button_sub_title_translate[$lang]['value'] = $t->value;
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                <div class="col-md-12 d-none lang_form" id="{{$lang}}-form1">
                                                    <div class="row g-1">
                                                        <div class="col-12">
                                                            <label for="download_user_app_button_title{{$lang}}"
                                                                   class="form-label">{{translate('Title')}}
                                                                ({{strtoupper($lang)}})<span
                                                                    class="form-label-secondary"
                                                                    data-toggle="tooltip" data-placement="right"
                                                                    data-original-title="{{ translate('Write_the_title_within_100_characters') }}">
                                                                <i class="tio-info color-A7A7A7"></i>
                                                            </span></label>
                                                            <input id="download_user_app_button_title{{$lang}}"
                                                                   type="text" maxlength="100"
                                                                   name="download_user_app_button_title[]"
                                                                   value="{{ $download_user_app_button_title_translate[$lang]['value'] ?? '' }}"
                                                                   class="form-control"
                                                                   placeholder="{{translate('messages.title_here...')}}">
                                                            <span
                                                                class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                                                        </div>
                                                        <div class="col-12">
                                                            <label for="download_user_app_button_sub_title{{$lang}}"
                                                                   class="form-label">{{translate('Sub Title')}}
                                                                ({{strtoupper($lang)}})<span
                                                                    class="form-label-secondary"
                                                                    data-toggle="tooltip" data-placement="right"
                                                                    data-original-title="{{ translate('Write_the_sub_title_within_200_characters') }}">
                                                                <i class="tio-info color-A7A7A7"></i>
                                                            </span></label>
                                                            <input type="text"
                                                                   id="download_user_app_button_sub_title{{$lang}}"
                                                                   maxlength="200"
                                                                   name="download_user_app_button_sub_title[]"
                                                                   value="{{ $download_user_app_button_sub_title_translate[$lang]['value'] ?? '' }}"
                                                                   class="form-control"
                                                                   placeholder="{{translate('messages.sub_title_here...')}}">
                                                            <span
                                                                class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="lang[]" value="{{$lang}}">
                                            @endforeach
                                        @else
                                            <div class="col-md-12">
                                                <div class="row g-1">
                                                    <div class="col-12">
                                                        <label for="download_user_app_button_title"
                                                               class="form-label">{{translate('Title')}}</label>
                                                        <input type="text" id="download_user_app_button_title"
                                                               name="download_user_app_button_title[]"
                                                               class="form-control"
                                                               placeholder="{{translate('messages.title_here...')}}">
                                                    </div>
                                                    <div class="col-12">
                                                        <label for="download_user_app_button_sub_title"
                                                               class="form-label">{{translate('Sub Title')}}</label>
                                                        <input id="download_user_app_button_sub_title" type="text"
                                                               name="download_user_app_button_sub_title[]"
                                                               class="form-control"
                                                               placeholder="{{translate('messages.sub_title_here...')}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="lang[]" value="default">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="bg--secondary rounded p-xxl-4 p-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="bg-white rounded p-xxl-4 p-2">
                                            <div
                                                class="d-flex mb-20 align-items-center gap-2 flex-wrap justify-content-between">
                                                <h4 class="mb-0">
                                                    <img src="{{asset('public/assets/admin/img/playstore.png')}}"
                                                         class="mr-2" alt="">
                                                    {{translate('Playstore Button')}}
                                                </h4>
                                                <label class="toggle-switch toggle-switch-sm m-0">
                                                    <input type="checkbox" name="playstore_url_status"
                                                           id="play-store-dm-status" data-id="play-store-dm-status"
                                                           data-type="toggle"
                                                           data-image-on="{{ asset('/public/assets/admin/img/modal/play-store-on.png') }}"
                                                           data-image-off="{{ asset('/public/assets/admin/img/modal/play-store-off.png') }}"
                                                           data-title-on="{{ translate('want_to_enable_the_play_store_button_for_user_app') }}"
                                                           data-title-off="{{ translate('want_to_disable_the_play_store_button_for_user_app') }}"
                                                           data-text-on="<p>{{ translate('if_enabled,_the_user_app_download_button_will_be_visible_on_react_landing_page') }}</p>"
                                                           data-text-off="<p>{{ translate('if_disabled,_this_button_will_be_hidden_from_the_react_landing_page') }}</p>"
                                                           class="status toggle-switch-input dynamic-checkbox-toggle"
                                                           value="1"
                                                        {{(isset($download_user_app_links) && $download_user_app_links['playstore_url_status']) ? 'checked' : ''}}>
                                                    <span class="toggle-switch-label text mb-0">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                </label>
                                            </div>
                                            <div class="__bg-F8F9FC-card">
                                                <div class="form-group mb-md-0">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <label for="playstore_url"
                                                               class="form-label text-capitalize m-0">
                                                            {{translate('Download Link')}}
                                                            <span class="input-label-secondary text--title"
                                                                  data-toggle="tooltip" data-placement="right"
                                                                  data-original-title="{{ translate('When_disabled,_the_Play_Store_download_button_will_be_hidden_from_the_React_landing_page.') }}">
                                                            <i class="tio-info color-A7A7A7"></i>
                                                        </span>
                                                        </label>
                                                    </div>
                                                    <input type="url"
                                                           {{(isset($download_user_app_links) && $download_user_app_links['playstore_url_status']) ? 'required' : ''}}
                                                           id="playstore_url"
                                                           placeholder="{{translate('Ex: https://play.google.com/store/apps')}}"
                                                           class="form-control h--45px" name="playstore_url"
                                                           value="{{ $download_user_app_links['playstore_url'] ?? ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-white rounded p-xxl-4 p-2">
                                            <div
                                                class="d-flex mb-20 align-items-center gap-2 flex-wrap justify-content-between">
                                                <h4 class="m-0">
                                                    <img src="{{asset('public/assets/admin/img/ios.png')}}" class="mr-2"
                                                         alt="">
                                                    {{translate('App Store Button')}}
                                                </h4>
                                                <label class="toggle-switch toggle-switch-sm m-0">
                                                    <input type="checkbox" name="apple_store_url_status"
                                                           data-id="apple-dm-status" data-type="toggle"
                                                           data-image-on="{{ asset('/public/assets/admin/img/modal/apple-on.png') }}"
                                                           data-image-off="{{ asset('/public/assets/admin/img/modal/apple-off.png') }}"
                                                           data-title-on="{{ translate('want_to_enable_the_app_store_button_for_user_app') }}"
                                                           data-title-off="{{ translate('want_to_disable_the_app_store_button_for_user_app') }}"
                                                           data-text-on="<p>{{ translate('if_enabled,_the_user_app_download_button_will_be_visible_on_react_landing_page') }}</p>"
                                                           data-text-off="<p>{{ translate('if_disabled,_this_button_will_be_hidden_from_the_react_landing_page') }}</p>"
                                                           class="status toggle-switch-input dynamic-checkbox-toggle"
                                                           id="apple-dm-status"
                                                           value="1" {{(isset($download_user_app_links) && $download_user_app_links['apple_store_url_status']) ? 'checked' : ''}}>
                                                    <span class="toggle-switch-label text mb-0">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                </label>
                                            </div>
                                            <div class="__bg-F8F9FC-card">
                                                <div class="form-group mb-md-0">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <label for="apple_store_url"
                                                               class="form-label text-capitalize m-0">
                                                            {{translate('Download Link')}}
                                                            <span class="input-label-secondary text--title"
                                                                  data-toggle="tooltip" data-placement="right"
                                                                  data-original-title="{{ translate('When_disabled,_the_User_app_download_button_will_be_hidden_on_React_Landing_page.') }}">
                                                            <i class="tio-info color-A7A7A7"></i>
                                                        </span>
                                                        </label>
                                                    </div>
                                                    <input type="url" id="apple_store_url"
                                                           {{(isset($download_user_app_links) && $download_user_app_links['apple_store_url_status']) ? 'required' : ''}}
                                                           placeholder="{{translate('Ex: https://www.apple.com/app-store/')}}"
                                                           class="form-control h--45px" name="apple_store_url"
                                                           value="{{ $download_user_app_links['apple_store_url'] ?? ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="btn--container justify-content-end mt-20">
                                <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                                <button type="submit" class="btn btn--primary mb-2">{{translate('Save')}}</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div id="user-downloadApp_section"
         class="custom-offcanvas offcanvas-750 offcanvas-xxl-950 d-flex flex-column justify-content-between">
        <form action="{{ route('taxvat.store') }}" method="post">
            <div>
                <div
                    class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                    <div class="py-1">
                        <h3 class="mb-0 line--limit-1">{{ translate('messages.User App Download Section Preview') }}</h3>
                    </div>
                    <button type="button"
                            class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"
                            aria-label="Close">
                        &times;
                    </button>
                </div>
                <div class="custom-offcanvas-body custom-offcanvas-body-100  p-20">
                    <section class="common-section-view bg-white border rounded-10 my-xl-2 mx-xl-2">
                        <div
                            class="common-section-inner d-flex flex-xl-nowrap justify-content-xl-between justify-content-center flex-wrap align-items-center gap-xxl-20 cus-gradient rounded-10 p-3">
                            <div>
                                <div class="mb-xl-4 mb-3">
                                    <h2 class="mb-1 fs-20">
                                        {!!\App\CentralLogics\Helpers::highlightWords($download_user_app_title?->value ?? 'Get $6amMart$ on Your Phone')!!}
                                    </h2>
                                    <p class="text-title fs-12 mb-0">
                                        {{$download_user_app_sub_title?->value ?? 'Order groceries, food, medicine & more instantly — anytime, anywhere.'}}
                                    </p>
                                </div>
                                <div
                                    class="bg-white rounded-10 px-xl-3 px-1 py-3 border d-flex flex-xl-nowrap flex-wrap justify-content-xl-start justify-content-center align-items-center gap-xxl-20">
                                    <div class="scan-wrap mb-xl-0 mb-3 w-xl-auto w-100">
                                        <div class="scan d-center border w-100px h-100px mx-auto rounded p-1">
                                            <img src="{{ asset('/public/assets/admin/img/400x400/app-scan.png') }}"
                                                 alt="Google Play" class="object-cover">
                                        </div>
                                        <p class="mb-0 fs-12 mt-1 text-center">{{ translate('messages.Scan to DownLoad') }}
                                        </p>
                                    </div>
                                    <div>
                                        <div class="mb-3 text-xl-start text-center">
                                            <h4 class="mb-0">{{ $download_user_app_button_title?->value ?? 'Download the Customer App'}}</h4>
                                            <p class="mb-0 fs-12">{{ $download_user_app_button_sub_title?->value ?? 'Smart shopping starts here.'}}
                                            </p>
                                        </div>
                                        <div
                                            class="d-flex justify-content-sm-start justify-content-center flex-sm-nowrap flex-wrap align-items-center gap-x-xl-10 app-manage">
                                            <!-- Google Play Button -->
                                            <a href="#"
                                               class="btn btn-primary d-flex align-items-center mr-2 px-3 py-2 bg-000 rounded mb-sm-0 mb-1">
                                                <img width="24" height="24"
                                                     src="{{ asset('/public/assets/admin/img/icons/playstore.png') }}"
                                                     alt="Google Play" class="mr-1">
                                                <div class="text-left">
                                                    <small class="d-block text-white mb-0 fs-12">GET IT ON</small>
                                                    <strong class="d-block text-white fs-14">Google Play</strong>
                                                </div>
                                            </a>
                                            <a href="#"
                                               class="btn btn-primary d-flex align-items-center px-3 py-2 bg-000 rounded">
                                                <img width="24" height="24"
                                                     src="{{ asset('/public/assets/admin/img/icons/apple-icon.png') }}"
                                                     alt="App Store" class="mr-1">
                                                <div class="text-left">
                                                    <small class="d-block text-white mb-0 fs-12">Download ON</small>
                                                    <strong class="d-block text-white fs-14">App Store</strong>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="user-app-thumb max-w-280 d-center mt-xl-0 mt-3">
                                <img src="{{ $download_user_app_image?->value ? \App\CentralLogics\Helpers::get_full_url(
                                'download_user_app_image', $download_user_app_image->value, $download_user_app_image->storage[0]?->value ?? 'public', 'aspect_1')
                                : asset('/public/assets/admin/img/400x400/user-app.png') }}" alt="user-app"
                                     class="object-contain w-100">
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </form>
    </div>
    <div id="offcanvasOverlay" class="offcanvas-overlay"></div>
    <!-- Section View Offcanvas end -->

    <!-- How it Works -->
    @include('admin-views.business-settings.landing-page-settings.partial.how-it-work-react')
@endsection
@push('script_2')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var removeBtn = document.getElementById('remove_image_btn');
            var removeFlag = document.getElementById('image_remove');
            var fileInput = document.querySelector('input[name="image"]');
            var form = fileInput ? fileInput.closest('form') : null;

            if (removeBtn && removeFlag) {
                removeBtn.addEventListener('click', function () {
                    removeFlag.value = '1';
                    if (fileInput) {
                        fileInput.removeAttribute('disabled');
                        fileInput.setAttribute('required', 'required');
                        fileInput.value = '';
                        fileInput.closest('.upload-file__wrapper').querySelector('.upload-file-textbox').style.display = 'block';
                    }
                });
            }

            if (form && removeFlag) {
                form.addEventListener('reset', function () {
                    removeFlag.value = '0';
                });
            }

            if (fileInput && removeFlag) {
                fileInput.addEventListener('change', function () {
                    removeFlag.value = '0';
                });
            }
        });
    </script>
@endpush
