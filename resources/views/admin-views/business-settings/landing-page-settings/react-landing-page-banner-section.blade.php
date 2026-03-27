@extends('layouts.admin.app')

@section('title', translate('messages.react_landing_page'))

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
                <h3 class="mb-1">{{ translate('Banner Section') }}</h3>
                <p class="mb-0 gray-dark fs-12">
                    {{ translate('See how your Banner Section will look to customers.') }}
                </p>
            </div>
            <div class="max-w-300px ml-sm-auto">
                <button type="button" class="btn btn-outline-primary py-2 fs-12 px-3 offcanvas-trigger"
                    data-target="#banner_section">
                    <i class="tio-invisible"></i> {{ translate('Section Preview') }}
                </button>
            </div>
        </div>
    </div>
    @php($banner_section_status = \App\Models\DataSetting::where('type', 'react_landing_page')->where('key', "banner_section_status")->first())
    <div class="card py-3 px-xxl-4 px-3 mb-15 mt-4">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-xxl-9 col-lg-8 col-md-7 col-sm-6">
                <div class="">
                    <h3 class="mb-1">{{ translate('Show Banner Section') }}</h3>
                    <p class="mb-0 gray-dark fs-12">
                        {{ translate('If you turn of the availability status, this section will not show in the website') }}
                    </p>
                </div>
            </div>
            <div class="col-xxl-3 col-lg-4 col-md-5 col-sm-6">
                <div class="py-2 px-3 rounded d-flex justify-content-between border align-items-center w-300">
                    <h5 class="text-capitalize fw-normal mb-0">{{ translate('Status') }}</h5>

                    <form
                        action="{{ route('admin.business-settings.statusUpdate', ['type' => 'react_landing_page', 'key' => 'banner_section_status']) }}"
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
                            {{ $banner_section_status?->value ? 'checked' : '' }}>
                        <span class="toggle-switch-label text">
                                <span class="toggle-switch-indicator"></span>
                            </span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    @php($banner = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'banner')->first())
    <div class="card">
    <form class="custom-validation" action="{{ route('admin.business-settings.react-landing-page-settings', 'banner') }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="mb-4">
                <h3 class="mb-1">{{ translate('Banner') }}</h3>
                <p class="mb-0 fs-12 gray-dark">
                    {{ translate('Upload an image that represents your brand and makes users want to order immediately.') }}
                </p>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <div class="bg--secondary h-100 rounded p-md-4 p-3">
                        <div class="text-center py-2">
                            <div class="mb-4">
                                <h5 class="mb-1">{{ translate('Upload Banner Image') }}</h5>
                                <p class="mb-0 fs-12 gray-dark">{{ translate('Upload your Banner Image') }}</p>
                            </div>

                            <div class="mx-auto text-center error-wrapper">
                                <div class="upload-file_custom ratio-8-1 h-100px">
                                    <input type="file" name="banner"
                                           class="upload-file__input single_file_input"
                                           accept="{{IMAGE_EXTENSION}}"
                                           {{ $banner?->value ? '' : 'required' }}>

                                    <label class="upload-file__wrapper w-100 h-100 m-0">
                                        <div class="upload-file-textbox text-center"
                                             style="{{ $banner?->value ? 'display: none;' : '' }}">
                                            <img width="22" class="svg"
                                                 src="{{asset('public/assets/admin/img/document-upload.svg')}}"
                                                 alt="upload-placeholder">
                                            <h6 class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                <span class="theme-clr">Click to upload</span><br>Or drag and drop
                                            </h6>
                                        </div>

                                        <img class="upload-file-img"
                                             src="{{ $banner?->value
                                                 ? \App\CentralLogics\Helpers::get_full_url('banner_section', $banner->value, $banner->storage[0]?->value ?? 'public', 'aspect_1')
                                                 : '' }}"
                                             data-default-src="{{ $banner?->value
                                                 ? \App\CentralLogics\Helpers::get_full_url('banner_section', $banner->value, $banner->storage[0]?->value ?? 'public', 'aspect_1')
                                                 : '' }}"
                                             style="{{ $banner?->value ? 'display:block;' : 'display:none;' }}">
                                    </label>

                                    <div class="overlay">
                                        <div class="d-flex gap-1 justify-content-center align-items-center h-100">
                                            <button type="button"
                                                    class="btn btn-outline-info icon-btn view_btn">
                                                <i class="tio-invisible"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info icon-btn edit_btn">
                                                <i class="tio-edit"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p class="fs-10 text-center mb-0 mt-lg-4 mt-3">
                                {{ translate('JPG, JPEG, PNG, Gif Image size : Max 2 MB') }}
                                <span class="font-medium text-title">{{ translate('(8:1)') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="btn--container justify-content-end mt-20">
                <button type="reset" class="btn btn--reset mb-2">{{ translate('Reset') }}</button>
                <button type="submit" class="btn btn--primary mb-2">{{ translate('Save') }}</button>
            </div>
        </div>
    </form>
</div>

</div>

<!-- Section View Offcanvas here -->
<div id="banner_section" class="custom-offcanvas offcanvas-750 d-flex flex-column justify-content-between">
    <form action="{{ route('taxvat.store') }}" method="post">
        <div>
            <div
                class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                <div class="py-1">
                    <h3 class="mb-0 line--limit-1">{{ translate('messages.Banner Section Preview') }}</h3>
                </div>
                <button type="button"
                    class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"
                    aria-label="Close">
                    &times;
                </button>
            </div>
            <div class="custom-offcanvas-body custom-offcanvas-body-100  p-20">
                <section class="common-section-view bg-white border rounded-10">
                    <div class="container p-0">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="banner-thumb w-100 h-100 rounded-10">
                                    <img  height="80"
                                        src="{{ $banner?->value
                                                 ? \App\CentralLogics\Helpers::get_full_url('banner_section', $banner->value, $banner->storage[0]?->value ?? 'public', 'aspect_1')
                                                 :asset('/public/assets/admin/img/400x400/react-landing-banner.png') }}"
                                        alt="" class="rounded-10 initial--28">
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

@push('script_2')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var removeBtn = document.getElementById('remove_image_btn');
            var removeFlag = document.getElementById('image_remove');
            var fileInput = document.querySelector('input[name="download_user_app_image"]');
            var form = fileInput ? fileInput.closest('form') : null;

            if (removeBtn && removeFlag) {
                removeBtn.addEventListener('click', function () {
                    removeFlag.value = '1';
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
