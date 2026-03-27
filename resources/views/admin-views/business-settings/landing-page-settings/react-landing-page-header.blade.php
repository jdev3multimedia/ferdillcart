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
        <div class="js-nav-scroller tabs-slide-wrap position-relative hs-nav-scroller-horizontal">
            @include('admin-views.business-settings.landing-page-settings.top-menu-links.react-landing-page-links')
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
    </div>

    <div class="card py-3 px-xxl-4 px-3 mb-20">
        <div class="d-flex flex-sm-nowrap flex-wrap gap-3 align-items-center justify-content-between">
            <div class="">
                <h3 class="mb-1">{{ translate('Hero Section') }}</h3>
                <p class="mb-0 gray-dark fs-12">
                    {{ translate('See how your Hero Section will look to customers.') }}
                </p>
            </div>
            <div class="max-w-300px ml-sm-auto">
                <button type="button" class="btn btn-outline-primary py-2 fs-12 px-3 offcanvas-trigger"
                    data-target="#hero_section">
                    <i class="tio-invisible"></i> {{ translate('Section Preview') }}
                </button>
            </div>
        </div>
    </div>


    <div class="tab-content">
        <div class="tab-pane fade show active">
            <!-- <form action="{{ route('admin.business-settings.react-landing-page-settings', 'header-section') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h5 class="card-title mb-3 mt-3">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <span>
                            <span class="card-header-icon mr-2"><i class="tio-settings-outlined"></i></span> <span>{{translate('Header Section')}}</span>
                        </span>
                    </div>
                </h5>
                <div class="card">
                    <div class="card-body">

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="row g-3">
                                    @if ($language)
                                    <div class="col-12 lang_form default-form">
                                        <div class="mb-2">
                                            <label for="header_title" class="form-label">{{translate('Title')}}({{ translate('messages.default') }})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_20_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span> <span class="form-label-secondary text-danger"
                            data-toggle="tooltip" data-placement="right"
                            data-original-title="{{ translate('messages.Required.')}}"> *
                            </span>
                                </label>
                                    <input id="header_title" type="text"  maxlength="20" name="header_title[]" value="{{ $header_title?->getRawOriginal('value')??'' }}" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                        </div>
                                        <div class="mb-2">
                                            <label for="header_sub_title" class="form-label">{{translate('Sub Title')}}({{ translate('messages.default') }})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_40_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span> <span class="form-label-secondary text-danger"
                            data-toggle="tooltip" data-placement="right"
                            data-original-title="{{ translate('messages.Required.')}}"> *
                            </span>
                                </label>
                                    <input id="header_sub_title" type="text"  maxlength="40" name="header_sub_title[]" value="{{ $header_sub_title?->getRawOriginal('value')??'' }}" class="form-control" placeholder="{{translate('messages.sub_title_here...')}}">
                                        </div>
                                        <div class="mb-2">
                                            <label for="header_tag_line" class="form-label">{{translate('Tag Line')}}({{ translate('messages.default') }})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_120_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span></label>
                                    <input id="header_tag_line" type="text"  maxlength="120" name="header_tag_line[]" value="{{ $header_tag_line?->getRawOriginal('value')??'' }}" class="form-control" placeholder="{{translate('messages.tag_line...')}}">
                                        </div>
                                    </div>
                                <input type="hidden" name="lang[]" value="default">
                                    @foreach(json_decode($language) as $lang)
                                    <?php
if (isset($header_title->translations) && count($header_title->translations)) {
    $header_title_translate = [];
    foreach ($header_title->translations as $t) {
        if ($t->locale == $lang && $t->key == 'header_title') {
            $header_title_translate[$lang]['value'] = $t->value;
        }
    }

}
if (isset($header_sub_title->translations) && count($header_sub_title->translations)) {
    $header_sub_title_translate = [];
    foreach ($header_sub_title->translations as $t) {
        if ($t->locale == $lang && $t->key == 'header_sub_title') {
            $header_sub_title_translate[$lang]['value'] = $t->value;
        }
    }

}
if (isset($header_tag_line->translations) && count($header_tag_line->translations)) {
    $header_tag_line_translate = [];
    foreach ($header_tag_line->translations as $t) {
        if ($t->locale == $lang && $t->key == 'header_tag_line') {
            $header_tag_line_translate[$lang]['value'] = $t->value;
        }
    }

}

                                        ?>
                                        <div class="col-12 d-none lang_form" id="{{$lang}}-form">
                                            <div class="mb-2">
                                                <label for="header_title{{$lang}}" class="form-label">{{translate('Title')}}({{strtoupper($lang)}})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_20_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span></label>
                                    <input id="header_title{{$lang}}" type="text"  maxlength="20" name="header_title[]" value="{{ $header_title_translate[$lang]['value']??'' }}" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                            </div>
                                            <div class="mb-2">
                                                <label for="header_sub_title{{$lang}}" class="form-label">{{translate('Sub Title')}}({{strtoupper($lang)}})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_40_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span></label>
                                    <input id="header_sub_title{{$lang}}" type="text"  maxlength="40" name="header_sub_title[]" value="{{ $header_sub_title_translate[$lang]['value']??'' }}" class="form-control" placeholder="{{translate('messages.sub_title_here...')}}">
                                            </div>
                                            <div class="mb-2">
                                                <label for="header_tag_line{{$lang}}" class="form-label">{{translate('Tag Line')}}({{strtoupper($lang)}})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_120_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span></label>
                                    <input id="header_tag_line{{$lang}}" type="text"  maxlength="120" name="header_tag_line[]" value="{{ $header_tag_line_translate[$lang]['value']??'' }}" class="form-control" placeholder="{{translate('messages.tag_line...')}}">
                                            </div>
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{$lang}}">
                                    @endforeach
                                @else
                                <div class="col-12">
                                    <div class="mb-2">
                                        <label for="header_title" class="form-label">{{translate('Title')}}</label>
                                        <input id="header_title" type="text" name="header_title[]" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                    </div>
                                    <div class="mb-2">
                                        <label for="header_sub_title" class="form-label">{{translate('Sub Title')}}</label>
                                        <input id="header_sub_title" type="text" name="header_sub_title[]" class="form-control" placeholder="{{translate('messages.sub_title_here...')}}">
                                    </div>
                                    <div class="mb-2">
                                        <label for="header_tag_line" class="form-label">{{translate('Tag Line')}}</label>
                                        <input id="header_tag_line" type="text" name="header_tag_line[]" class="form-control" placeholder="{{translate('messages.tag_line...')}}">
                                    </div>
                                </div>
                                    <input type="hidden" name="lang[]" value="default">
                                @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label d-block mb-2">
                                    {{ translate('messages.Icon') }} <span class="text--primary">{{ translate('(size: 1:1)') }}</span>
                                </label>
                                <label class="upload-img-3 m-0">
                                    <div class="position-relative">
                                    <div class="img">
                                        <img
                                        src="{{\App\CentralLogics\Helpers::get_full_url('header_icon', $header_icon?->value?? '', $header_icon?->storage[0]?->value ?? 'public','aspect_1')}}" data-onerror-image="{{asset('/public/assets/admin/img/aspect-1.png')}}" class="img__aspect-1 mw-100 min-w-135px onerror-image" alt="">
                                    </div>
                                    <input type="file"  name="image" hidden>
                                       @if (isset($header_icon['value']))
                                            <span id="header_icon" class="remove_image_button remove-image"
                                                  data-id="header_icon"
                                                  data-title="{{translate('Warning!')}}"
                                                  data-text="<p>{{translate('Are_you_sure_you_want_to_remove_this_image_?')}}</p>"
                                            > <i class="tio-clear"></i></span>
                                            @endif
                                        </div>
                                </label>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label d-block mb-2">
                                    {{translate('Banner')}}  <span class="text--primary">{{translate('(size: 1:1)')}} <span class="form-label-secondary text-danger"
                                        data-toggle="tooltip" data-placement="right"
                                        data-original-title="{{ translate('messages.Required.')}}"> *
                                        </span>
                                </span>
                                </label>
                                <label class="upload-img-3 m-0">
                                    <div class="position-relative">
                                    <div class="img">
                                        <img
                                            src="{{\App\CentralLogics\Helpers::get_full_url('header_banner', $header_banner?->value?? '', $header_banner?->storage[0]?->value ?? 'public','aspect_1')}}" data-onerror-image="{{asset('/public/assets/admin/img/aspect-1.png')}}"
                                            class="img__aspect-1 mw-100 min-w-135px onerror-image" alt="">
                                    </div>
                                        <input type="file" name="banner_image"  hidden>

                                        </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn--container justify-content-end mt-20">
                    <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                    <button type="submit"   class="btn btn--primary mb-2">{{translate('Save Information')}}</button>
                </div>
            </form> -->
            <form action="{{ route('admin.business-settings.react-landing-page-settings', 'header-section') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card mb-20">
                    <div class="card-body">
                        <h3 class="mb-20">{{ translate('Intro Section ') }}</h3>
                        <div class="bg--secondary rounded p-xxl-4 p-3">
                            @php($header_title = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'header_title')->first())
                            @php($header_sub_title = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'header_sub_title')->first())
                            @php($header_tag_line = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'header_tag_line')->first())
                            @php($header_icon = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'header_icon')->first())
                            @php($header_banner = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'header_banner')->first())
                            @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
                            @php($language = $language->value ?? null)
                            @php($defaultLang = str_replace('_', '-', app()->getLocale()))
                            @if($language)
                                <ul class="nav nav-tabs mb-4 border-bottom">
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
                                <div class="col-md-12">
                                    <div class="row g-3">
                                        @if ($language)
                                            <div class="col-12 lang_form default-form">
                                                <div class="mb-2">
                                                    <label for="header_title"
                                                        class="form-label">{{translate('Title')}}({{ translate('messages.default') }})<span
                                                            class="form-label-secondary" data-toggle="tooltip"
                                                            data-placement="right"
                                                            data-original-title="{{ translate('This is the main website headline, keep it short and impactful. Write it under 50 characters.') }}">
                                                            <i class="tio-info color-A7A7A7"></i>
                                                        </span> <span class="form-label-secondary text-danger"
                                                            data-toggle="tooltip" data-placement="right"
                                                            data-original-title="{{ translate('messages.Required.')}}">
                                                        </span>
                                                         <span class="form-label-secondary text-danger"
                                                                      data-toggle="tooltip" data-placement="right"
                                                                      data-original-title="{{ translate('messages.Required.')}}"> *
                                                    </span>
                                                    </label>
                                                    <input id="header_title" type="text" maxlength="50"
                                                        name="header_title[]"
                                                        value="{{ $header_title?->getRawOriginal('value') ?? '' }}"
                                                        class="form-control"
                                                        placeholder="{{translate('messages.title_here...')}}">
                                                    <span
                                                        class="text-right text-counting color-A7A7A7 d-block mt-1">0/50</span>
                                                </div>
                                                <div class="mb-2">
                                                    <label for="header_sub_title"
                                                        class="form-label">{{translate('Sub Title')}}({{ translate('messages.default') }})<span
                                                            class="form-label-secondary" data-toggle="tooltip"
                                                            data-placement="right"
                                                            data-original-title="{{ translate('Write_the_title_within_120_characters') }}">
                                                            <i class="tio-info color-A7A7A7"></i>
                                                        </span> <span class="form-label-secondary text-danger"
                                                            data-toggle="tooltip" data-placement="right"
                                                            data-original-title="{{ translate('messages.Required.')}}">
                                                        </span>
                                                        <span class="form-label-secondary text-danger"
                                                              data-toggle="tooltip" data-placement="right"
                                                              data-original-title="{{ translate('messages.Required.')}}"> *
                                                    </span>
                                                    </label>
                                                    <input id="header_sub_title" type="text" maxlength="120"
                                                        name="header_sub_title[]"
                                                        value="{{ $header_sub_title?->getRawOriginal('value') ?? '' }}"
                                                        class="form-control"
                                                        placeholder="{{translate('messages.sub_title_here...')}}">
                                                    <span
                                                        class="text-right text-counting color-A7A7A7 d-block mt-1">0/120</span>
                                                </div>
                                                <div class="mb-0">
                                                    <label for="header_tag_line"
                                                        class="form-label">{{translate('Tag Line')}}({{ translate('messages.default') }})<span
                                                            class="form-label-secondary" data-toggle="tooltip"
                                                            data-placement="right"
                                                            data-original-title="{{ translate('Write_the_title_within_120_characters') }}">
                                                            <i class="tio-info color-A7A7A7"></i>
                                                        </span></label>
                                                    <input id="header_tag_line" type="text" maxlength="120"
                                                        name="header_tag_line[]"
                                                        value="{{ $header_tag_line?->getRawOriginal('value') ?? '' }}"
                                                        class="form-control"
                                                        placeholder="{{translate('messages.tag_line...')}}">
                                                    <span
                                                        class="text-right text-counting color-A7A7A7 d-block mt-1">0/120</span>
                                                </div>
                                            </div>
                                            <input type="hidden" name="lang[]" value="default">
                                            @foreach(json_decode($language) as $lang)
                                                                                <?php
                                                if (isset($header_title->translations) && count($header_title->translations)) {
                                                    $header_title_translate = [];
                                                    foreach ($header_title->translations as $t) {
                                                        if ($t->locale == $lang && $t->key == 'header_title') {
                                                            $header_title_translate[$lang]['value'] = $t->value;
                                                        }
                                                    }

                                                }
                                                if (isset($header_sub_title->translations) && count($header_sub_title->translations)) {
                                                    $header_sub_title_translate = [];
                                                    foreach ($header_sub_title->translations as $t) {
                                                        if ($t->locale == $lang && $t->key == 'header_sub_title') {
                                                            $header_sub_title_translate[$lang]['value'] = $t->value;
                                                        }
                                                    }

                                                }
                                                if (isset($header_tag_line->translations) && count($header_tag_line->translations)) {
                                                    $header_tag_line_translate = [];
                                                    foreach ($header_tag_line->translations as $t) {
                                                        if ($t->locale == $lang && $t->key == 'header_tag_line') {
                                                            $header_tag_line_translate[$lang]['value'] = $t->value;
                                                        }
                                                    }

                                                }

                                                                                                                            ?>
                                                                                <div class="col-12 d-none lang_form" id="{{$lang}}-form">
                                                                                    <div class="mb-2">
                                                                                        <label for="header_title{{$lang}}"
                                                                                            class="form-label">{{translate('Title')}}({{strtoupper($lang)}})<span
                                                                                                class="form-label-secondary" data-toggle="tooltip"
                                                                                                data-placement="right"
                                                                                                data-original-title="{{ translate('This is the main website headline, keep it short and impactful. Write it under 50 characters.') }}">
                                                                                                <i class="tio-info color-A7A7A7"></i>
                                                                                            </span></label>
                                                                                        <input id="header_title{{$lang}}" type="text" maxlength="50"
                                                                                            name="header_title[]"
                                                                                            value="{{ $header_title_translate[$lang]['value'] ?? '' }}"
                                                                                            class="form-control"
                                                                                            placeholder="{{translate('messages.title_here...')}}">
                                                                                        <span
                                                                                            class="text-right text-counting color-A7A7A7 d-block mt-1">0/50</span>
                                                                                    </div>
                                                                                    <div class="mb-2">
                                                                                        <label for="header_sub_title{{$lang}}"
                                                                                            class="form-label">{{translate('Sub Title')}}({{strtoupper($lang)}})<span
                                                                                                class="form-label-secondary" data-toggle="tooltip"
                                                                                                data-placement="right"
                                                                                                data-original-title="{{ translate('Write_the_title_within_120_characters') }}">
                                                                                                <i class="tio-info color-A7A7A7"></i>
                                                                                            </span></label>
                                                                                        <input id="header_sub_title{{$lang}}" type="text" maxlength="120"
                                                                                            name="header_sub_title[]"
                                                                                            value="{{ $header_sub_title_translate[$lang]['value'] ?? '' }}"
                                                                                            class="form-control"
                                                                                            placeholder="{{translate('messages.sub_title_here...')}}">
                                                                                        <span
                                                                                            class="text-right text-counting color-A7A7A7 d-block mt-1">0/120</span>
                                                                                    </div>
                                                                                    <div class="mb-0">
                                                                                        <label for="header_tag_line{{$lang}}"
                                                                                            class="form-label">{{translate('Tag Line')}}({{strtoupper($lang)}})<span
                                                                                                class="form-label-secondary" data-toggle="tooltip"
                                                                                                data-placement="right"
                                                                                                data-original-title="{{ translate('Write_the_title_within_120_characters') }}">
                                                                                                <i class="tio-info color-A7A7A7"></i>
                                                                                            </span></label>
                                                                                        <input id="header_tag_line{{$lang}}" type="text" maxlength="120"
                                                                                            name="header_tag_line[]"
                                                                                            value="{{ $header_tag_line_translate[$lang]['value'] ?? '' }}"
                                                                                            class="form-control"
                                                                                            placeholder="{{translate('messages.tag_line...')}}">
                                                                                        <span
                                                                                            class="text-right text-counting color-A7A7A7 d-block mt-1">0/120</span>
                                                                                    </div>
                                                                                </div>
                                                                                <input type="hidden" name="lang[]" value="{{$lang}}">
                                            @endforeach
                                        @else
                                            <div class="col-12">
                                                <div class="mb-2">
                                                    <label for="header_title"
                                                        class="form-label">{{translate('Title')}}</label>
                                                    <input id="header_title" maxlength="50" type="text"
                                                        name="header_title[]" class="form-control"
                                                        placeholder="{{translate('messages.title_here...')}}">
                                                    <span
                                                        class="text-right text-counting color-A7A7A7 d-block mt-1">0/50</span>
                                                </div>
                                                <div class="mb-4">
                                                    <label for="header_sub_title"
                                                        class="form-label">{{translate('Sub Title')}}</label>
                                                    <input id="header_sub_title" maxlength="120" type="text"
                                                        name="header_sub_title[]" class="form-control"
                                                        placeholder="{{translate('messages.sub_title_here...')}}">
                                                    <span
                                                        class="text-right text-counting color-A7A7A7 d-block mt-1">0/120</span>
                                                </div>
                                                <div class="mb-0">
                                                    <label for="header_tag_line"
                                                        class="form-label">{{translate('Tag Line')}}</label>
                                                    <input id="header_tag_line" type="text" name="header_tag_line[]"
                                                        class="form-control"
                                                        placeholder="{{translate('messages.tag_line...')}}">
                                                    <span
                                                        class="text-right text-counting color-A7A7A7 d-block mt-1">0/120</span>
                                                </div>
                                            </div>
                                            <input type="hidden" name="lang[]" value="default">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h3 class="mb-20">{{ translate('Pick Location Section ') }}</h3>
                        <div class="bg--secondary rounded p-xxl-4 p-3">
                            @php($pick_location_title = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'pick_location_title')->first())
                            @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
                            @php($language = $language->value ?? null)
                            @php($defaultLang = str_replace('_', '-', app()->getLocale()))
                            @if($language)
                                <ul class="nav nav-tabs mb-4 border-bottom">
                                    <li class="nav-item">
                                        <a class="nav-link lang_link active" href="#"
                                            id="default-link-location">{{translate('messages.default')}}</a>
                                    </li>
                                    @foreach (json_decode($language) as $lang)
                                        <li class="nav-item">
                                            <a class="nav-link lang_link" href="#"
                                                id="{{ $lang }}-link-location">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="row">
                                <div class="col-lg-12">
                                    @if ($language)
                                        <div class="lang_form default-form">
                                            <div class="mb-0">
                                                <label for="pick_location_title"
                                                    class="form-label">{{translate('Title')}}({{ translate('messages.default') }})<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('This text appears as the label or heading above the location search bar. Write it under 50 characters.') }}">
                                                        <i class="tio-info color-A7A7A7"></i>
                                                    </span> <span class="form-label-secondary text-danger"
                                                        data-toggle="tooltip" data-placement="right"
                                                        data-original-title="{{ translate('messages.Required.')}}"> *
                                                    </span>
                                                </label>
                                                <input id="pick_location_title" type="text" maxlength="50"
                                                    name="pick_location_title[]"
                                                    value="{{ $pick_location_title?->getRawOriginal('value') ?? '' }}"
                                                    class="form-control"
                                                    placeholder="{{translate('messages.title_here...')}}">
                                                <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/50</span>
                                            </div>
                                        </div>
                                        @foreach(json_decode($language) as $lang)
                                                                        <?php
                                            if (isset($pick_location_title->translations) && count($pick_location_title->translations)) {
                                                $pick_location_title_translate = [];
                                                foreach ($pick_location_title->translations as $t) {
                                                    if ($t->locale == $lang && $t->key == 'pick_location_title') {
                                                        $pick_location_title_translate[$lang]['value'] = $t->value;
                                                    }
                                                }
                                            }
                                                                                            ?>
                                                                        <div class="d-none lang_form" id="{{$lang}}-form-location">
                                                                            <div class="mb-0">
                                                                                <label for="pick_location_title{{$lang}}"
                                                                                    class="form-label">{{translate('Title')}}({{strtoupper($lang)}})<span
                                                                                        class="form-label-secondary" data-toggle="tooltip"
                                                                                        data-placement="right"
                                                                                        data-original-title="{{ translate('This text appears as the label or heading above the location search bar. Write it under 50 characters.') }}">
                                                                                        <i class="tio-info color-A7A7A7"></i>
                                                                                    </span></label>
                                                                                <input id="pick_location_title{{$lang}}" type="text" maxlength="50"
                                                                                    name="pick_location_title[]"
                                                                                    value="{{ $pick_location_title_translate[$lang]['value'] ?? '' }}"
                                                                                    class="form-control"
                                                                                    placeholder="{{translate('messages.title_here...')}}">
                                                                                <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/50</span>
                                                                            </div>
                                                                        </div>
                                        @endforeach
                                    @else
                                        <div class="mb-0">
                                            <label for="pick_location_title"
                                                class="form-label">{{translate('Title')}}</label>
                                            <input id="pick_location_title" maxlength="50" type="text"
                                                name="pick_location_title[]" class="form-control"
                                                placeholder="{{translate('messages.title_here...')}}">
                                            <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/50</span>
                                        </div>
                                        <input type="hidden" name="lang[]" value="default">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn--container justify-content-end mt-20">
                    <button type="reset" class="btn min-w--120 btn--reset mb-2">{{translate('Reset')}}</button>
                    <button type="submit" class="btn min-w--120 btn--primary mb-2">{{translate('Save')}}</button>
                </div>
            </form>
            <form id="header_icon_form" action="{{ route('admin.remove_image') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{  $header_icon?->id}}">
                <input type="hidden" name="model_name" value="DataSetting">
                <input type="hidden" name="image_path" value="header_icon">
                <input type="hidden" name="field_name" value="value">
            </form>


        </div>
    </div>
</div>


<!-- Section View Offcanvas here -->
<div id="hero_section" class="custom-offcanvas offcanvas-750 d-flex flex-column justify-content-between">
    <form action="{{ route('taxvat.store') }}" method="post">
        <div>
            <div
                class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                <div class="py-1">
                    <h3 class="mb-0 line--limit-1">{{ translate('messages.Hero Section Preview') }}</h3>
                </div>
                <button type="button"
                    class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"
                    aria-label="Close">
                    &times;
                </button>
            </div>
            <div class="custom-offcanvas-body custom-offcanvas-body-100  p-20">
                <section class="common-section-view bg-white border rounded-10 my-xl-3 mx-xl-3">
                    <div class="common-section-inner cus-gradient rounded-10">
                        <div class="py-sm-5 py-3 px-sm-4 px-2 text-center">
                            <h2 class="mb-lg-3 mb-2 fs-24">
                                {!! \App\CentralLogics\Helpers::highlightWords(text:$header_title?->value ?? 'Your Everyday $Needs$, Delivered $Fast$') !!}
                            </h2>
                            <p class="text-title fs-14 mb-xl-4 mb-lg-4 mb-3">
                                {{ $header_sub_title?->value ?? 'Enter your address to enjoy fast delivery of groceries, food, medicine, parcels & more from your favorite local stores with 6amMart.' }}
                            </p>
                            <p class="fs-20 text-dark mb-20">
                                {{$header_tag_line?->value ?? 'Discover everything you need near you'}}
                            </p>
                            <div class="mx-auto max-w-650 bg-white rounded p-xxl-30 p-sm-2">
                                <div class="input-group bg--secondary border shadow-sm rounded">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg--secondary border-0 rounded-pill pl-sm-3 pl-2">
                                            <i class="tio-poi-outlined"></i>
                                        </span>
                                    </div>
                                    <input type="text"
                                        class="form-control border-0 bg--secondary pl-1 focus-0 outline-none shadow-0"
                                        placeholder="{{$pick_location_title?->value ?? 'Search location here....'}}" aria-label="Search location">
                                    <div
                                        class="input-group-append d-flex justify-content-sm-start justify-content-end align-items-center gap-2">
                                        <div class="cursor-pointer"><i class="tio-my-location text-base-clr fs-20"></i>
                                        </div>
                                        <button
                                            class="btn btn--primary px-sm-3 px-2 base-bg-cmn base-border-cmn rounded-pill px-3 fs-14 h-100"
                                            type="button">
                                            Discover
                                        </button>
                                    </div>
                                </div>
                            </div>
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
