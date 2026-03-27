@php use App\CentralLogics\Helpers;use App\Models\DataSetting; @endphp
@extends('layouts.admin.app')

@section('title',translate('messages.react_landing_page'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">

@endpush

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
                    <h3 class="mb-1">{{ translate('Gallery Section') }}</h3>
                    <p class="mb-0 gray-dark fs-12">
                        {{ translate('See how your Gallery Section will look to customers.') }}
                    </p>
                </div>
                <div class="max-w-300px ml-sm-auto">
                    <button type="button" class="btn btn-outline-primary py-2 fs-12 px-3 offcanvas-trigger"
                            data-target="#gallery_section">
                        <i class="tio-invisible"></i> {{ translate('Section Preview') }}
                    </button>
                </div>
            </div>
        </div>

        @php($gallery_section_status = \App\Models\DataSetting::where('type', 'react_landing_page')->where('key', "gallery_section_status")->first())
        <div class="card py-3 px-xxl-4 px-3 mb-15 mt-4">
            <div class="row g-3 align-items-center justify-content-between">
                <div class="col-xxl-9 col-lg-8 col-md-7 col-sm-6">
                    <div class="">
                        <h3 class="mb-1">{{ translate('Show Gallery Section') }}</h3>
                        <p class="mb-0 gray-dark fs-12">
                            {{ translate('If you turn of the availability status, this section will not show in the website') }}
                        </p>
                    </div>
                </div>
                <div class="col-xxl-3 col-lg-4 col-md-5 col-sm-6">
                    <div class="py-2 px-3 rounded d-flex justify-content-between border align-items-center w-300">
                        <h5 class="text-capitalize fw-normal mb-0">{{ translate('Status') }}</h5>

                        <form
                            action="{{ route('admin.business-settings.statusUpdate', ['type' => 'react_landing_page', 'key' => 'gallery_section_status']) }}"
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
                                {{ $gallery_section_status?->value ? 'checked' : '' }}>
                            <span class="toggle-switch-label text">
                                <span class="toggle-switch-indicator"></span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-20">
            <form action="{{ route('admin.business-settings.react-landing-page-settings', 'gallery-section') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="mb-20">
                        <h3 class="mb-1">{{translate('messages.Gallery Content') }}</h3>
                        <p class="m-0 fs-12 color-656566">{{ translate('Showcase high-quality food images to attract users visually.') }}</p>
                    </div>
                    @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
                    @php($language = $language->value ?? null)
                    @php($defaultLang = str_replace('_', '-', app()->getLocale()))
                    @php($gallery_content_title = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'gallery_content_title')->first())
                    @php($gallery_content_sub_title = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'gallery_content_sub_title')->first())
                    <?php
                    $keys = ['gallery_section_status', 'gallery_section_title', 'gallery_section_sub_title',
                        'gallery_image_1', 'gallery_image_2', 'gallery_image_3', 'gallery_image_4', 'gallery_image_5', 'gallery_image_6'];
                    $reactHeaders = DataSetting::withoutGlobalScope('translate')
                        ->with(['translations', 'storage'])
                        ->where('type', 'react_landing_page')
                        ->whereIn('key', $keys)
                        ->get()
                        ->keyBy('key');
                    $gallery_section_status = $reactHeaders['gallery_section_status'] ?? null;
                    $gallery_section_title = $reactHeaders['gallery_section_title'] ?? null;
                    $gallery_section_sub_title = $reactHeaders['gallery_section_sub_title'] ?? null;
                    $gallery_image_1 = $reactHeaders['gallery_image_1'] ?? null;
                    $gallery_image_2 = $reactHeaders['gallery_image_2'] ?? null;
                    $gallery_image_3 = $reactHeaders['gallery_image_3'] ?? null;
                    $gallery_image_4 = $reactHeaders['gallery_image_4'] ?? null;
                    ?>
                    <div class="bg--secondary h-100 rounded p-md-4 p-3">
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
                                            <label for="gallery_content_title"
                                                   class="form-label">{{translate('Title')}}
                                                ({{ translate('messages.default') }})
                                                <span class="form-label-secondary" data-toggle="tooltip"
                                                      data-placement="right"
                                                      data-original-title="{{ translate('Write_the_title_within_50_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span><span class="form-label-secondary text-danger"
                                                             data-toggle="tooltip" data-placement="right"
                                                             data-original-title="{{ translate('messages.Required.')}}"> *
                                                </span>
                                            </label>
                                            <input id="gallery_content_title" type="text" maxlength="50"
                                                   name="gallery_content_title[]"
                                                   value="{{ $gallery_content_title?->getRawOriginal('value') ?? '' }}"
                                                   class="form-control"
                                                   placeholder="{{translate('messages.title_here...')}}">
                                            <span
                                                class="text-right text-counting color-A7A7A7 d-block mt-1">0/50</span>
                                        </div>
                                        <div class="col-12">
                                            <label for="gallery_content_sub_title"
                                                   class="form-label">{{translate('Sub Title')}}
                                                ({{ translate('messages.default') }})
                                                <span class="form-label-secondary" data-toggle="tooltip"
                                                      data-placement="right"
                                                      data-original-title="{{ translate('Write_the_title_within_200_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span><span class="form-label-secondary text-danger"
                                                             data-toggle="tooltip" data-placement="right"
                                                             data-original-title="{{ translate('messages.Required.')}}"> *
                                                </span>
                                            </label>
                                            <input id="gallery_content_sub_title" type="text" maxlength="200"
                                                   name="gallery_content_sub_title[]"
                                                   value="{{ $gallery_content_sub_title?->getRawOriginal('value') ?? '' }}"
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
                                        $gallery_content_title_translate = [];
                                        $gallery_content_sub_title_translate = [];

                                        if (isset($gallery_content_title->translations) && count($gallery_content_title->translations)) {
                                            foreach ($gallery_content_title->translations as $t) {
                                                if ($t->locale == $lang && $t->key == 'gallery_content_title') {
                                                    $gallery_content_title_translate[$lang]['value'] = $t->value;
                                                }
                                            }
                                        }

                                        if (isset($gallery_content_sub_title->translations) && count($gallery_content_sub_title->translations)) {
                                            foreach ($gallery_content_sub_title->translations as $t) {
                                                if ($t->locale == $lang && $t->key == 'gallery_content_sub_title') {
                                                    $gallery_content_sub_title_translate[$lang]['value'] = $t->value;
                                                }
                                            }
                                        }
                                        ?>
                                    <div class="col-md-12 d-none lang_form" id="{{$lang}}-form">
                                        <div class="row g-1">
                                            <div class="col-12">
                                                <label for="gallery_content_title{{$lang}}"
                                                       class="form-label">{{translate('Title')}}
                                                    ({{strtoupper($lang)}})<span class="form-label-secondary"
                                                                                 data-toggle="tooltip"
                                                                                 data-placement="right"
                                                                                 data-original-title="{{ translate('Write_the_title_within_50_characters') }}">
                                                        <i class="tio-info color-A7A7A7"></i>
                                                    </span>
                                                </label>
                                                <input id="gallery_content_title{{$lang}}" type="text" maxlength="50"
                                                       name="gallery_content_title[]"
                                                       value="{{ $gallery_content_title_translate[$lang]['value'] ?? '' }}"
                                                       class="form-control"
                                                       placeholder="{{translate('messages.title_here...')}}">
                                                <span
                                                    class="text-right text-counting color-A7A7A7 d-block mt-1">0/50</span>
                                            </div>
                                            <div class="col-12">
                                                <label for="gallery_content_sub_title{{$lang}}"
                                                       class="form-label">{{translate('Sub Title')}}
                                                    ({{strtoupper($lang)}})<span class="form-label-secondary"
                                                                                 data-toggle="tooltip"
                                                                                 data-placement="right"
                                                                                 data-original-title="{{ translate('Write_the_title_within_200_characters') }}">
                                                        <i class="tio-info color-A7A7A7"></i>
                                                    </span>
                                                </label>
                                                <input id="gallery_content_sub_title{{$lang}}" type="text"
                                                       maxlength="200"
                                                       name="gallery_content_sub_title[]"
                                                       value="{{ $gallery_content_sub_title_translate[$lang]['value'] ?? '' }}"
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
                                        <label for="gallery_content_title"
                                               class="form-label">{{translate('Title')}}</label>
                                        <input id="gallery_content_title" maxlength="50" type="text"
                                               name="gallery_content_title[]" class="form-control"
                                               placeholder="{{translate('messages.title_here...')}}">
                                        <span
                                            class="text-right text-counting color-A7A7A7 d-block mt-1">0/50</span>
                                    </div>
                                    <div class="mb-4">
                                        <label for="gallery_content_sub_title"
                                               class="form-label">{{translate('Sub Title')}}</label>
                                        <input id="gallery_content_sub_title" maxlength="200" type="text"
                                               name="gallery_content_sub_title[]" class="form-control"
                                               placeholder="{{translate('messages.sub_title_here...')}}">
                                        <span
                                            class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                    </div>
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                            @endif
                        </div>
                    </div>
                    <div class="btn--container justify-content-end mt-20">
                        <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                        <button type="submit" class="btn btn--primary mb-2">{{translate('Save')}}</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">

            <div class="card-header">
                <div class="">
                    <h3 class="mb-0">{{ translate('Gallery Section Image') }}</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php
                    function ordinalSuffix($number)
                    {
                        if (!in_array($number % 100, [11, 12, 13])) {
                            switch ($number % 10) {
                                case 1:
                                    return $number . 'st';
                                case 2:
                                    return $number . 'nd';
                                case 3:
                                    return $number . 'rd';
                            }
                        }
                        return $number . 'th';
                    }
                    ?>
                    @for($i = 1; $i<=4;$i++)
                            <?php

                            $imageVar = "gallery_image_{$i}";
                            $image = $$imageVar ?? null;
                            $imageUrl = $image?->value
                                ? Helpers::get_full_url(
                                    'gallery_section',
                                    $image->value,
                                    $image->storage[0]->value ?? 'public',
                                    'upload_1_1',
                                )
                                : '';
                            $label = translate(ordinalSuffix($i) . ' ' . 'Image');
                            ?>

                        @php($status = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', "gallery_image_{$i}_status")->first())
                        <div class="col-md-6">
                            <form class="custom-validation"
                                  action="{{ route('admin.business-settings.react-landing-page-settings', 'gallery-section-images') }}"
                                  method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{$i}}" name="gallery_tab">
                                <div class="card">
                                    <div class="card-header">
                                        <div
                                            class="w-100 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                            <h3 class="mb-0">{{ $label }}</h3>
                                            <label
                                                class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between gap-4">
                                        <span class="w-auto switch--label text-nowrap fs-14 text-title">
                                            {{translate('messages.Status') }}
                                        </span>
                                                <input type="checkbox" class="status toggle-switch-input" value="1"
                                                       name="gallery_image_{{$i}}_status"
                                                       id="" {{$status?->value ? 'checked' : ''}}>
                                                <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="bg--secondary h-100 rounded p-4 mb-20">
                                            <div class="text-center py-1">
                                                <div class="mx-auto text-center error-wrapper">
                                                    <div class="mb-4">
                                                        <h5 class="mb-1">{{ translate('Upload Image') }}</h5>
                                                        <p class="mb-0 fs-12 gray-dark">{{ translate('Upload ') . $label }}</p>
                                                    </div>
                                                    <div class="upload-file_custom">
                                                        <input type="file" id="gallery_image_input_{{ $i }}"
                                                               name="gallery_image_{{ $i }}"
                                                               class="upload-file__input single_file_input"
                                                               accept=".webp, .jpg, .jpeg, .png, .gif" {{$image?->value ? '': 'required'}}>
                                                        <label class="upload-file__wrapper ratio-1 m-0">
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
                                                            <img class="upload-file-img" loading="lazy"
                                                                 src="{{ $imageUrl }}"
                                                                 data-default-src="{{ $imageUrl ??'' }}"
                                                                 alt=""
                                                            >
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
                                                                <input type="hidden"
                                                                       name="gallery_image_{{$i}}_remove"
                                                                       id="gallery_image_{{$i}}"
                                                                       value="1"
                                                                       disabled>

                                                                <button type="button"
                                                                        class="remove_btn btn icon-btn"
                                                                        data-card="{{$i}}">
                                                                    <i class="tio-delete text-danger"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="fs-10 text-center mb-0 mt-lg-4 mt-3">
                                                    {{ translate('JPG, JPEG, PNG, Gif Image size : Max 2 MB')}} <span
                                                        class="font-medium text-title">{{ translate('(1:1)')}}</span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="btn--container justify-content-end mt-20">
                                            <button type="reset"
                                                    class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                                            <button type="submit"
                                                    class="btn btn--primary mb-2">{{translate('Save')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endfor

                </div>
            </div>
        </div>
    </div>


    <!-- Section View Offcanvas here -->
    <div id="gallery_section" class="custom-offcanvas offcanvas-750 d-flex flex-column justify-content-between">
        <form action="{{ route('taxvat.store') }}" method="post">
            <div>
                <div
                    class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                    <div class="py-1">
                        <h3 class="mb-0 line--limit-1">{{ translate('messages.Gallery Section Preview') }}</h3>
                    </div>
                    <button type="button"
                            class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"
                            aria-label="Close">
                        &times;
                    </button>
                </div>
                <div class="custom-offcanvas-body custom-offcanvas-body-100  p-20">
                    <section class="common-section-view bg-white border rounded-10">
                        <div class="mb-4 text-center">
                            <h2 class="mb-lg-1 mb-1 fs-24">
                                {!! Helpers::highlightWords($gallery_content_title?->value ?? 'See $6amMart$ in Action') !!}
                            </h2>
                            <p class="text-title fs-14 m-0">
                                {{$gallery_content_sub_title?->value ?? 'Explore how customers use our services, vendors grow their business'}}
                            </p>
                        </div>
                        <div class="container p-0">
                            <div class="row g-xl-20 g-1">
                                <div class="col-xl-4 col-lg-6 col-6">
                                    <div class="bg-ECEEF1 w-100 gallery-thumb-h-450 rounded-10">
                                        <img src="{{ $gallery_image_1?->value
                                ? Helpers::get_full_url(
                                    'gallery_section',
                                    $gallery_image_1->value,
                                    $gallery_image_1->storage[0]->value ?? 'public',
                                    'upload_1_1',
                                )
                                :asset('/public/assets/admin/img/400x400/ract-gallery1.jpg') }}"
                                             alt="" class="rounded-10 w-100 h-100">
                                    </div>
                                </div>
                                <div class="col-xl-8 col-lg-6 col-6">
                                    <div class="row g-xl-20 g-1">
                                        <div class="col-xl-6 col-6">
                                            <div class="bg-ECEEF1 w-100 gallery-thumb-h-220 rounded-10">
                                                <img
                                                    src="{{ $gallery_image_2?->value
                                ? Helpers::get_full_url(
                                    'gallery_section',
                                    $gallery_image_2->value,
                                    $gallery_image_2->storage[0]->value ?? 'public',
                                    'upload_1_1',
                                )
                                :asset('/public/assets/admin/img/400x400/ract-gallery2.jpg') }}"
                                                    alt="" class="rounded-10 w-100 h-100">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-6">
                                            <div class="bg-ECEEF1 w-100 gallery-thumb-h-220 rounded-10">
                                                <img
                                                    src="{{ $gallery_image_3?->value
                                ? Helpers::get_full_url(
                                    'gallery_section',
                                    $gallery_image_3->value,
                                    $gallery_image_3->storage[0]->value ?? 'public',
                                    'upload_1_1',
                                )
                                :asset('/public/assets/admin/img/400x400/ract-gallery3.jpg') }}"
                                                    alt="" class="rounded-10 w-100 h-100">
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="bg-ECEEF1 w-100 gallery-thumb-h-220 rounded-10">
                                                <img
                                                    src="{{ $gallery_image_4?->value
                                ? Helpers::get_full_url(
                                    'gallery_section',
                                    $gallery_image_4->value,
                                    $gallery_image_4->storage[0]->value ?? 'public',
                                    'upload_1_1',
                                )
                                :asset('/public/assets/admin/img/400x400/ract-gallery4.jpg') }}"
                                                    alt="" class="rounded-10 w-100 h-100">
                                            </div>
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
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.remove_btn').forEach(function (button) {
            button.addEventListener('click', function () {
                const cardNumber = this.dataset.card;
                const fileInput = document.getElementById('gallery_image_input_' + cardNumber);
                const removeFlag = document.getElementById('gallery_image_' + cardNumber);

                if (removeFlag) {
                    removeFlag.removeAttribute('disabled');
                    removeFlag.value = '1';
                }

                if (fileInput) {
                    fileInput.removeAttribute('disabled');
                    fileInput.setAttribute('required', 'required');
                    fileInput.value = '';

                    const wrapper = fileInput.closest('.upload-file__wrapper');
                    if (wrapper) {
                        const textbox = wrapper.querySelector('.upload-file-textbox');
                        if (textbox) textbox.style.display = 'block';
                    }

                    const img = wrapper.querySelector('.upload-file-img');
                    if (img) img.style.display = 'none';
                }
            });
        });

        document.querySelectorAll('.single_file_input').forEach(function (input) {
            input.addEventListener('change', function () {
                const index = this.id.split('gallery_image_input_')[1];
                const removeFlag = document.getElementById('gallery_image_' + index);
                if (removeFlag) removeFlag.value = '0';
                this.removeAttribute('required');
            });
        });
    });
</script>
@push('script_2')

@endpush
