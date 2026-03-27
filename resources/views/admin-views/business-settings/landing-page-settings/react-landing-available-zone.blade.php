@extends('layouts.admin.app')

@section('title',translate('messages.react_landing_page'))

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
                    <h3 class="mb-1">{{ translate('Available zone section') }}</h3>
                    <p class="mb-0 gray-dark fs-12">
                        {{ translate('See how your Available zone Section will look to customers.') }}
                    </p>
                </div>
                <div class="max-w-300px ml-sm-auto">
                    <button type="button" class="btn btn-outline-primary py-2 fs-12 px-3 offcanvas-trigger"
                            data-target="#AvailableZone_section">
                        <i class="tio-invisible"></i> {{ translate('Section Preview') }}
                    </button>
                </div>
            </div>
        </div>
        <div class="card shadow-none border-0 bg-opacity-primary-10 mb-20">
            <div class="card-body d-flex gap-2 align-items-center">
                <img width="20" src="{{asset('public/assets/admin/img/info-idea.svg')}}" alt="img">
                <p class="fs-12 color-656566 m-0">
                    {{ translate('Customize the section by adding a title, short description, and images in the') }} <a
                        href="{{ route('admin.business-settings.zone.home') }}" target="_blank"
                        class="text--underline text-006AE5">{{ translate('Zone Setup') }}</a> {{ translate('section. All created zones will be automatically displayed on the React Landing Page. The zones will be based on the Zone Display Name.') }}
                </p>
            </div>
        </div>
        @php($available_zone_title=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','react_landing_page')->where('key','available_zone_title')->first())
        @php($available_zone_short_description=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','react_landing_page')->where('key','available_zone_short_description')->first())
        @php($available_zone_image=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','react_landing_page')->where('key','available_zone_image')->first())
        @php($available_zone_status=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','react_landing_page')->where('key','available_zone_status')->first())
        @php($available_zone_status = $available_zone_status ? $available_zone_status->value : 0)
        @php($zones = App\Models\Zone::where('status', 1)->select('name')->get())
        @php($language=\App\Models\BusinessSetting::where('key','language')->first())
        @php($language = $language->value ?? null)
        @php($defaultLang = str_replace('_', '-', app()->getLocale()))



            <div class="card py-3 px-xxl-4 px-3 mb-15 mt-4">
                <div class="row g-3 align-items-center justify-content-between">
                    <div class="col-xxl-9 col-lg-8 col-md-7 col-sm-6">
                        <div>
                            <h3 class="mb-1">{{translate('messages.Show available zone') }}</h3>
                            <p class="m-0 fs-12 color-656566">{{ translate('To view a list of all active zones on your React Landing Page Enable the') }}  {{ translate('')}} {{ translate('`Available Zones`') }} {{translate('feature') }}</p>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-lg-4 col-md-5 col-sm-6">
                        <div class="py-2 px-3 rounded d-flex justify-content-between border align-items-center w-300">
                            <h5 class="text-capitalize fw-normal mb-0">{{ translate('Status') }}</h5>

                            <form
                                action="{{ route('admin.business-settings.statusUpdate', ['type' => 'react_landing_page', 'key' => 'available_zone_status']) }}"
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
                                    {{ $available_zone_status ? 'checked' : '' }}>
                                <span class="toggle-switch-label text">
                                <span class="toggle-switch-indicator"></span>
                            </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        <form id="zone-setup-form"
              action="{{ route('admin.business-settings.react-landing-page-settings', 'available-zone-section') }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-lg-12">
                    <div class="card shadow--card-2">
                        <div class="card-body">
                            <div class="mb-20">
                                <h3 class="mb-1">{{ translate('Available Zone Content ') }}</h3>
                                <p class="mb-0 fs-12">{{ translate('Manage delivery zones or cities available for your service.') }}</p>
                            </div>
                            <div class="bg--secondary rounded p-xxl-4 p-3">
                                @if($language)
                                    <ul class="nav nav-tabs mb-4">
                                        <li class="nav-item">
                                            <a class="nav-link lang_link active"
                                               href="#"
                                               id="default-link">{{ translate('Default') }}</a>
                                        </li>
                                        @foreach (json_decode($language) as $lang)
                                            <li class="nav-item">
                                                <a class="nav-link lang_link"
                                                   href="#"
                                                   id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                @if ($language)
                                    <div class="lang_form"
                                         id="default-form">
                                        <div class="form-group mb-2">
                                            <label class="input-label"
                                                   for="default_title">{{ translate('messages.title') }}
                                                ({{ translate('messages.Default') }})<span class="form-label-secondary"
                                                                                           data-toggle="tooltip"
                                                                                           data-placement="right"
                                                                                           data-original-title="{{ translate('Write_the_title_within_50_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span>
                                            </label>
                                            <input type="text" name="available_zone_title[]" maxlength="50"
                                                   id="default_title"
                                                   class="form-control" placeholder="{{ translate('messages.title') }}"
                                                   value="{{$available_zone_title?->getRawOriginal('value')}}">
                                            <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/50</span>
                                        </div>
                                        <input type="hidden" name="lang[]" value="default">
                                        <div class="form-group mb-0">
                                            <label class="input-label"
                                                   for="exampleFormControlInput1">{{ translate('messages.sub title') }}
                                                ({{ translate('messages.default') }})<span class="form-label-secondary"
                                                                                           data-toggle="tooltip"
                                                                                           data-placement="right"
                                                                                           data-original-title="{{ translate('Write_the_short_description_within_1000_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span></label>
                                            <textarea type="text" name="available_zone_short_description[]"
                                                      maxlength="1000"
                                                      placeholder="{{translate('messages.short_description')}}"
                                                      class="form-control min-h-90px ckeditor">{{$available_zone_short_description?->getRawOriginal('value')}}</textarea>
                                        </div>
                                    </div>
                                    @foreach (json_decode($language) as $lang)
                                            <?php
                                            if (isset($available_zone_title->translations) && count($available_zone_title->translations)) {
                                                $available_zone_title_translate = [];
                                                foreach ($available_zone_title->translations as $t) {
                                                    if ($t->locale == $lang && $t->key == 'available_zone_title') {
                                                        $available_zone_title_translate[$lang]['value'] = $t->value;
                                                    }
                                                }

                                            }
                                            if (isset($available_zone_short_description->translations) && count($available_zone_short_description->translations)) {
                                                $available_zone_short_description_translate = [];
                                                foreach ($available_zone_short_description->translations as $t) {
                                                    if ($t->locale == $lang && $t->key == 'available_zone_short_description') {
                                                        $available_zone_short_description_translate[$lang]['value'] = $t->value;
                                                    }
                                                }

                                            }
                                            ?>
                                        <div class="d-none lang_form"
                                             id="{{ $lang }}-form">
                                            <div class="form-group mb-2">
                                                <label class="input-label"
                                                       for="{{ $lang }}_title">{{ translate('messages.title') }}
                                                    ({{ strtoupper($lang) }})<span class="form-label-secondary"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="right"
                                                                                   data-original-title="{{ translate('Write_the_title_within_50_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span>
                                                </label>
                                                <input type="text" name="available_zone_title[]" maxlength="50"
                                                       id="{{ $lang }}_title"
                                                       class="form-control"
                                                       value="{{ $available_zone_title_translate[$lang]['value']??'' }}"
                                                       placeholder="{{ translate('messages.title') }}">
                                                <span
                                                    class="text-right text-counting color-A7A7A7 d-block mt-1">0/50</span>
                                            </div>
                                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                                            <div class="form-group mb-0">
                                                <label class="input-label"
                                                       for="exampleFormControlInput1">{{ translate('messages.sub title') }}
                                                    ({{ strtoupper($lang) }})<span class="form-label-secondary"
                                                                                   data-toggle="tooltip"
                                                                                   data-placement="right"
                                                                                   data-original-title="{{ translate('Write_the_short_description_within_200_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span></label>
                                                <textarea type="text" name="available_zone_short_description[]"
                                                          maxlength="1000"
                                                          placeholder="{{translate('messages.short_description')}}"
                                                          class="form-control min-h-90px ckeditor">{{ $available_zone_short_description_translate[$lang]['value']??'' }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div id="default-form">
                                        <div class="form-group">
                                            <label class="input-label"
                                                   for="exampleFormControlInput1">{{ translate('messages.title') }}
                                                ({{ translate('messages.default') }})</label>
                                            <input type="text" name="available_zone_title[]" class="form-control"
                                                   placeholder="{{ translate('messages.title') }}">
                                        </div>
                                        <input type="hidden" name="lang[]" value="default">
                                        <div class="form-group mb-0">
                                            <label class="input-label"
                                                   for="exampleFormControlInput1">{{ translate('messages.short_description') }}
                                            </label>
                                            <textarea type="text" name="available_zone_short_description[]"
                                                      placeholder="{{translate('messages.short_description')}}"
                                                      class="form-control min-h-90px ckeditor"></textarea>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="btn--container justify-content-end mt-20">
                                <button class="btn btn--reset " type="reset">{{translate('reset')}}</button>
                                <button class="btn btn--primary" type="submit">{{translate('Save')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
             
            </div>
        </form>
    </div>


    <!-- Section View Offcanvas here -->
    <div id="AvailableZone_section"
         class="custom-offcanvas offcanvas-750 offcanvas-xxl-950 d-flex flex-column justify-content-between">
        <form action="{{ route('taxvat.store') }}" method="post">
            <div>
                <div
                    class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                    <div class="py-1">
                        <h3 class="mb-0 line--limit-1">{{ translate('messages.Available zone section Preview') }}</h3>
                    </div>
                    <button type="button"
                            class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"
                            aria-label="Close">
                        &times;
                    </button>
                </div>
                <div class="custom-offcanvas-body custom-offcanvas-body-100  p-20">
                    <section class="common-section-view bg-white border rounded-10 my-xl-0 mx-xl-0">
                        <div
                            class="common-section-inner d-flex flex-xl-nowrap justify-content-xl-between justify-content-center flex-wrap align-items-center gap-xxl-20 bg-fafafa rounded-10 p-3">
                            <div class="max-w-400px">
                                <h2 class="mb-xl-2 mb-1 fs-24">
                                    {!! \App\CentralLogics\Helpers::highlightWords(text:$available_zone_title?->value ?? 'Available Delivery $Areas / Zone$') !!}
                                </h2>
                                @if($available_zone_short_description?->value)
                                    <p class="text-title fs-12 mb-20">
                                        {!! nl2br($available_zone_short_description?->value) !!}
                                    </p>
                                @else
                                    <p class="text-title fs-12 mb-20">
                                        We offer delivery services across a wide range of regions. To see if we deliver
                                        to your area, check our list of available delivery zones or use our delivery
                                    </p>
                                    <ul class="zone-all-area d-flex flex-wrap gap-2 list-checked">
                                        <li class="text-title py-1 pr-xxl-2">✅ 25+ Active Zones</li>
                                        <li class="text-title py-1 pr-xxl-2">🚚 Fast Coverage Expanding</li>
                                        <li class="text-title py-1 pr-xxl-2">⏱ Avg Delivery 30 Min</li>
                                        <li class="text-title py-1 pr-xxl-2">📦 Parcel, Grocery, Food & More</li>
                                    </ul>
                                @endif

                            </div>
                            <div class="max-w-390">
                                <div
                                    class="border-0 bg-white shadow-sm rounded p-xl-3 p-2 d-flex flex-wrap gap-3 available-zone-tag">
                                    @foreach($zones as $zone)
                                        <a href="javascript:void(0)"
                                           class="text-title d-inline-block fs-14 border rounded-10 py-2 px-xl-4 px-3 text-center"
                                           data-toggle="tooltip"
                                           data-placement="top" data-html="true" data-title="
                                    <div class='text-left'>
                                        <span class='fs-16 d-block font-semibold text-white'>Barisal City</span>
                                        <span class='fs-12 text-white-80'>Modules are Grocery, Pharmacy, Food, Shop.</span>
                                    </div>">
                                            {{$zone->name}}
                                        </a>
                                    @endforeach
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
@push('script_2')
    <script src="{{asset('public/assets/admin/ckeditor/ckeditor.js')}}"></script>
    <script>
        "use strict";
        $(document).ready(function () {
            $('.ckeditor').ckeditor();
        });
    </script>
    <script>
        // Form on reset
        const prevImage = $('#viewer').attr('src');
        $('#zone-setup-form').on('reset', function () {
            $('#customFileEg1').val(null);
            $('#viewer').attr('src', prevImage);
        })

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
    </script>
@endpush

