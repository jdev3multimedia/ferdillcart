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
                    <h3 class="mb-1">{{ translate('Testimonial Section') }}</h3>
                    <p class="mb-0 gray-dark fs-12">
                        {{ translate('See how your Testimonial Section will look to customers.') }}
                    </p>
                </div>
                <div class="max-w-300px ml-sm-auto">
                    <button type="button" class="btn btn-outline-primary py-2 fs-12 px-3 offcanvas-trigger"
                            data-target="#testimonialAdd_section">
                        <i class="tio-invisible"></i> {{ translate('Section Preview') }}
                    </button>
                </div>
            </div>
        </div>
        @php($testimonial_title = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'testimonial_title')->first())
        @php($testimonial_sub_title = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'testimonial_sub_title')->first())
        @php($testimonial_button_title = \App\Models\DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'testimonial_button_title')->first())
        @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
        @php($language = $language->value ?? null)
        @php($defaultLang = str_replace('_', '-', app()->getLocale()))
        @php($testimonial_section_status = \App\Models\DataSetting::where('type', 'react_landing_page')->where('key', "testimonial_section_status")->first())
        <div class="card py-3 px-xxl-4 px-3 mb-15 mt-4">
            <div class="row g-3 align-items-center justify-content-between">
                <div class="col-xxl-9 col-lg-8 col-md-7 col-sm-6">
                    <div class="">
                        <h3 class="mb-1">{{ translate('Show Testimonial Section') }}</h3>
                        <p class="mb-0 gray-dark fs-12">
                            {{ translate('If you turn of the availability status, this section will not show in the website') }}
                        </p>
                    </div>
                </div>
                <div class="col-xxl-3 col-lg-4 col-md-5 col-sm-6">
                    <div class="py-2 px-3 rounded d-flex justify-content-between border align-items-center w-300">
                        <h5 class="text-capitalize fw-normal mb-0">{{ translate('Status') }}</h5>

                        <form
                            action="{{ route('admin.business-settings.statusUpdate', ['type' => 'react_landing_page', 'key' => 'testimonial_section_status']) }}"
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
                                {{ $testimonial_section_status?->value ? 'checked' : '' }}>
                            <span class="toggle-switch-label text">
                                <span class="toggle-switch-indicator"></span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-content">
            <div class="tab-pane fade show active">
                <form action="{{ route('admin.business-settings.react-landing-page-settings', 'testimonial-title') }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card mb-20">
                        <div class="card-body">
                            <div class=" mb-20">
                                <h3 class="mb-1">{{ translate('Testimonial Content') }}</h3>
                                <p class="mb-0 gray-dark fs-12">
                                    {{ translate('Manage the main title for the customer reviews section.') }}
                                </p>
                            </div>
                            <div class="bg--secondary rounded p-xxl-4 p-3">
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
                                @if ($language)
                                    <div class="row g-1 lang_form" id="default-form">
                                        <div class="col-sm-12">
                                            <label for="testimonial_title" class="form-label">{{translate('Title')}}
                                                ({{ translate('messages.default') }})<span class="form-label-secondary"
                                                                                           data-toggle="tooltip"
                                                                                           data-placement="right"
                                                                                           data-original-title="{{ translate('Write_the_title_within_50_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span>
                                                <span class="form-label-secondary text-danger"
                                                      data-toggle="tooltip" data-placement="right"
                                                      data-original-title="{{ translate('messages.Required.')}}"> *
                                                    </span></label>
                                            <input id="testimonial_title" type="text" maxlength="50"
                                                   name="testimonial_title[]"
                                                   class="form-control"
                                                   value="{{$testimonial_title?->getRawOriginal('value') ?? ''}}"
                                                   placeholder="{{translate('messages.title_here...')}}" required>
                                            <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/50</span>
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="testimonial_sub_title"
                                                   class="form-label">{{translate('SubTitle')}}
                                                ({{ translate('messages.default') }})<span class="form-label-secondary"
                                                                                           data-toggle="tooltip"
                                                                                           data-placement="right"
                                                                                           data-original-title="{{ translate('Write_the_subtitle_within_20_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span>
                                                <span class="form-label-secondary text-danger"
                                                      data-toggle="tooltip" data-placement="right"
                                                      data-original-title="{{ translate('messages.Required.')}}"> *
                                                    </span></label>
                                            <input id="testimonial_sub_title" type="text" maxlength="200"
                                                   name="testimonial_sub_title[]" class="form-control"
                                                   value="{{$testimonial_sub_title?->getRawOriginal('value') ?? ''}}"
                                                   placeholder="{{translate('messages.title_here...')}}" required>
                                            <span
                                                class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="testimonial_button_title"
                                                   class="form-label">{{translate('Button Name')}}
                                                ({{ translate('messages.default') }})<span class="form-label-secondary"
                                                                                           data-toggle="tooltip"
                                                                                           data-placement="right"
                                                                                           data-original-title="{{ translate('Write_the_button_name_within_20_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span>
                                                <span class="form-label-secondary text-danger"
                                                      data-toggle="tooltip" data-placement="right"
                                                      data-original-title="{{ translate('messages.Required.')}}"> *
                                                    </span></label>
                                            <input id="testimonial_button_title" type="text" maxlength="20"
                                                   name="testimonial_button_title[]" class="form-control"
                                                   value="{{$testimonial_button_title?->getRawOriginal('value') ?? ''}}"
                                                   placeholder="{{translate('messages.Start Selling...')}}" required>
                                            <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/20</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">
                                    @foreach(json_decode($language) as $lang)
                                            <?php
                                            $testimonial_title_translate = [];
                                            $testimonial_sub_title_translate = [];
                                            $testimonial_button_title_translate = [];

                                            if (isset($testimonial_title->translations) && count($testimonial_title->translations)) {
                                                foreach ($testimonial_title->translations as $t) {
                                                    if ($t->locale == $lang && $t->key == 'testimonial_title') {
                                                        $testimonial_title_translate[$lang]['value'] = $t->value;
                                                    }
                                                }
                                            }

                                            if (isset($testimonial_sub_title->translations) && count($testimonial_sub_title->translations)) {
                                                foreach ($testimonial_sub_title->translations as $t) {
                                                    if ($t->locale == $lang && $t->key == 'testimonial_sub_title') {
                                                        $testimonial_sub_title_translate[$lang]['value'] = $t->value;
                                                    }
                                                }
                                            }

                                            if (isset($testimonial_button_title->translations) && count($testimonial_button_title->translations)) {
                                                foreach ($testimonial_button_title->translations as $t) {
                                                    if ($t->locale == $lang && $t->key == 'testimonial_button_title') {
                                                        $testimonial_button_title_translate[$lang]['value'] = $t->value;
                                                    }
                                                }
                                            }
                                            ?>
                                        <div class="row g-3 d-none lang_form" id="{{$lang}}-form">
                                            <div class="col-sm-12">
                                                <label for="testimonial_title{{$lang}}"
                                                       class="form-label">{{translate('Title')}}
                                                    ({{strtoupper($lang)}})<span class="form-label-secondary"
                                                                                 data-toggle="tooltip"
                                                                                 data-placement="right"
                                                                                 data-original-title="{{ translate('Write_the_title_within_50_characters') }}">
                                                                        <i class="tio-info color-A7A7A7"></i>
                                                                    </span></label>
                                                <input type="text" id="testimonial_title{{$lang}}" maxlength="50"
                                                       name="testimonial_title[]" class="form-control"
                                                       value="{{ $testimonial_title_translate[$lang]['value'] ?? '' }}"
                                                       placeholder="{{translate('messages.title_here...')}}">
                                                <span
                                                    class="text-right text-counting color-A7A7A7 d-block mt-1">0/50</span>
                                            </div>
                                            <div class="col-sm-12">
                                                <label for="testimonial_sub_title"
                                                       class="form-label">{{translate('SubTitle')}}
                                                    ({{ translate('messages.default') }})<span
                                                        class="form-label-secondary"
                                                        data-toggle="tooltip" data-placement="right"
                                                        data-original-title="{{ translate('Write_the_subtitle_within_20_characters') }}">
                                                                        <i class="tio-info color-A7A7A7"></i>
                                                                    </span></label>
                                                <input id="testimonial_sub_title" type="text" maxlength="200"
                                                       name="testimonial_sub_title[]" class="form-control"
                                                       value="{{ $testimonial_sub_title_translate[$lang]['value'] ?? '' }}"
                                                       placeholder="{{translate('messages.title_here...')}}">
                                                <span
                                                    class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                            </div>
                                            <div class="col-sm-12">
                                                <label for="testimonial_button_title"
                                                       class="form-label">{{translate('Button Name')}}
                                                    ({{ translate('messages.default') }})<span
                                                        class="form-label-secondary"
                                                        data-toggle="tooltip" data-placement="right"
                                                        data-original-title="{{ translate('Write_the_button_name_within_20_characters') }}">
                                                                        <i class="tio-info color-A7A7A7"></i>
                                                                    </span></label>
                                                <input id="testimonial_button_title" type="text" maxlength="20"
                                                       name="testimonial_button_title[]" class="form-control"
                                                       value="{{ $testimonial_button_title_translate[$lang]['value'] ?? '' }}"
                                                       placeholder="{{translate('messages.Start Selling...')}}">
                                                <span
                                                    class="text-right text-counting color-A7A7A7 d-block mt-1">0/20</span>
                                            </div>
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{$lang}}">
                                    @endforeach
                                @else
                                    <div class="row g-1">
                                        <div class="col-sm-12">
                                            <label for="testimonial_title"
                                                   class="form-label">{{translate('Title')}}</label>
                                            <input id="testimonial_title" type="text" name="testimonial_title[]"
                                                   value="{{ $testimonial_title->getRawOriginal('value') ?? '' }}"
                                            {{translate('messages.title_here...')}}">
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="testimonial_subtitle"
                                                   class="form-label">{{translate('SubTitle')}}
                                                ({{ translate('messages.default') }})<span class="form-label-secondary"
                                                                                           data-toggle="tooltip"
                                                                                           data-placement="right"
                                                                                           data-original-title="{{ translate('Write_the_subtitle_within_20_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span></label>
                                            <input id="testimonial_sub_title" type="text" maxlength="200"
                                                   name="testimonial_sub_title[]" class="form-control"
                                                   value="{{$testimonial_sub_title?->getRawOriginal('value') ?? ''}}"
                                                   placeholder="{{translate('messages.title_here...')}}">
                                            <span
                                                class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="testimonial_button_title"
                                                   class="form-label">{{translate('Button Name')}}
                                                ({{ translate('messages.default') }})<span class="form-label-secondary"
                                                                                           data-toggle="tooltip"
                                                                                           data-placement="right"
                                                                                           data-original-title="{{ translate('Write_the_button_name_within_20_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span></label>
                                            <input id="testimonial_button_title" type="text" maxlength="20"
                                                   name="testimonial_button_title[]" class="form-control"
                                                   value="{{$testimonial_button_title?->getRawOriginal('value') ?? ''}}"
                                                   placeholder="{{translate('messages.Start Selling...')}}">
                                            <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/20</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">
                                @endif
                            </div>
                            <div class="btn--container justify-content-end mt-20">
                                <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                                <button type="submit" class="btn btn--primary mb-2">{{translate('Save')}}</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="card mb-20">
                    <div class="card-header">
                        <div class="">
                            <h3 class="mb-1">{{ translate('Add Testimonial') }}</h3>
                            <p class="mb-0 gray-dark fs-12">
                                {{ translate('Add and manage individual customer testimonials.') }}
                            </p>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="custom-validation"
                              action="{{ route('admin.business-settings.react-landing-page-settings', 'testimonial-list') }}"
                              method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card p-xxl-3 mb-20 border-0">
                                <div class="row g-3">
                                    <div class="col-lg-8">
                                        <div class="bg--secondary rounded h-100 p-xxl-4 p-3">
                                            @if($language)
                                                <ul class="nav nav-tabs mb-4 border-bottom">
                                                    <li class="nav-item">
                                                        <a class="nav-link lang_link active" href="#"
                                                           id="testimonial-default-link">{{translate('messages.default')}}</a>
                                                    </li>
                                                    @foreach (json_decode($language) as $lang)
                                                        <li class="nav-item">
                                                            <a class="nav-link lang_link" href="#"
                                                               id="testimonial-{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                            @if($language)
                                                <div class="row g-3 lang_form" id="testimonial-default-form">
                                                    <div class="col-md-6">
                                                        <label for="name"
                                                               class="form-label">{{translate('Reviewer Name')}}
                                                            ({{ translate('messages.default') }})
                                                            <span class="form-label-secondary" data-toggle="tooltip"
                                                                  data-placement="right"
                                                                  data-original-title="{{ translate('Content...') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span>
                                                            <span class="form-label-secondary text-danger"
                                                                  data-toggle="tooltip" data-placement="right"
                                                                  data-original-title="{{ translate('messages.Required.')}}"> *
                                                    </span>
                                                        </label>
                                                        <input id="name" type="text" name="name[]" class="form-control"
                                                               placeholder="{{translate('Ex:  John Doe')}}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="designation"
                                                               class="form-label">{{translate('Designation')}}
                                                            ({{ translate('messages.default') }})
                                                            <span class="form-label-secondary" data-toggle="tooltip"
                                                                  data-placement="right"
                                                                  data-original-title="{{ translate('Content...') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span>

                                                        </label>
                                                        <input id="designation" type="text" name="designation[]"
                                                               class="form-control"
                                                               placeholder="{{translate('Ex:  CTO')}}">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label for="review"
                                                               class="form-label">{{translate('messages.review')}}
                                                            ({{ translate('messages.default') }})
                                                            <span
                                                                class="form-label-secondary" data-toggle="tooltip"
                                                                data-placement="right"
                                                                data-original-title="{{ translate('Write_the_title_within_140_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span>
                                                            <span class="form-label-secondary text-danger"
                                                                  data-toggle="tooltip" data-placement="right"
                                                                  data-original-title="{{ translate('messages.Required.')}}"> *
                                                    </span>
                                                        </label>
                                                        <textarea id="review" name="review[]" maxlength="200"
                                                                  placeholder="{{translate('Very Good Company')}}"
                                                                  class="form-control h92px" required></textarea>
                                                        <span
                                                            class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="lang[]" value="default">
                                                @foreach(json_decode($language) as $lang)
                                                    <div class="row g-3 d-none lang_form"
                                                         id="testimonial-{{$lang}}-form">
                                                        <div class="col-md-6">
                                                            <label for="name{{$lang}}"
                                                                   class="form-label">{{translate('Reviewer Name')}}
                                                                ({{strtoupper($lang)}})
                                                                <span class="form-label-secondary" data-toggle="tooltip"
                                                                      data-placement="right"
                                                                      data-original-title="{{ translate('Content...') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span>
                                                            </label>
                                                            <input id="name{{$lang}}" type="text" name="name[]"
                                                                   class="form-control"
                                                                   placeholder="{{translate('Ex:  John Doe')}}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="designation{{$lang}}"
                                                                   class="form-label">{{translate('Designation')}}
                                                                ({{strtoupper($lang)}})
                                                                <span class="form-label-secondary" data-toggle="tooltip"
                                                                      data-placement="right"
                                                                      data-original-title="{{ translate('Content...') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span>
                                                            </label>
                                                            <input id="designation{{$lang}}" type="text"
                                                                   name="designation[]"
                                                                   class="form-control"
                                                                   placeholder="{{translate('Ex:  CTO')}}">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label for="review{{$lang}}"
                                                                   class="form-label">{{translate('messages.review')}}
                                                                ({{strtoupper($lang)}})
                                                                <span
                                                                    class="form-label-secondary" data-toggle="tooltip"
                                                                    data-placement="right"
                                                                    data-original-title="{{ translate('Write_the_title_within_140_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span></label>
                                                            <textarea id="review{{$lang}}" name="review[]"
                                                                      maxlength="200"
                                                                      placeholder="{{translate('Very Good Company')}}"
                                                                      class="form-control h92px"></textarea>
                                                            <span
                                                                class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                                @endforeach
                                            @else
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="name"
                                                               class="form-label">{{translate('Reviewer Name')}}
                                                            <span class="form-label-secondary" data-toggle="tooltip"
                                                                  data-placement="right"
                                                                  data-original-title="{{ translate('Content...') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span>
                                                        </label>
                                                        <input id="name" type="text" name="name[]" class="form-control"
                                                               placeholder="{{translate('Ex:  John Doe')}}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="designation"
                                                               class="form-label">{{translate('Designation')}}
                                                            <span class="form-label-secondary" data-toggle="tooltip"
                                                                  data-placement="right"
                                                                  data-original-title="{{ translate('Content...') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span>
                                                        </label>
                                                        <input id="designation" type="text" name="designation[]"
                                                               class="form-control"
                                                               placeholder="{{translate('Ex:  CTO')}}">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label for="review"
                                                               class="form-label">{{translate('messages.review')}}<span
                                                                class="form-label-secondary" data-toggle="tooltip"
                                                                data-placement="right"
                                                                data-original-title="{{ translate('Write_the_title_within_140_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span></label>
                                                        <textarea id="review" name="review[]" maxlength="200"
                                                                  placeholder="{{translate('Very Good Company')}}"
                                                                  class="form-control h92px"></textarea>
                                                        <span
                                                            class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="lang[]" value="default">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="bg--secondary h-100 rounded p-md-4 p-3 d-center">
                                            <div class="text-center">
                                                <div class="mb-30">
                                                    <h5 class="mb-1">{{ translate('Reviewer Image') }}</h5>
                                                    <p class="mb-0 fs-12 gray-dark">{{ translate('Upload Reviewer Image') }}
                                                    </p>
                                                </div>
                                                <div class="mx-auto text-center error-wrapper">
                                                    <div class="upload-file_custom ratio-1 h-100px">
                                                        <input type="file" name="reviewer_image"
                                                               class="upload-file__input single_file_input"
                                                               accept=".webp, .jpg, .jpeg, .png, .gif" required>
                                                        <label class="upload-file__wrapper w-100 h-100 m-0">
                                                            <div class="upload-file-textbox text-center" style="">
                                                                <img width="22" class="svg"
                                                                     src="{{asset('public/assets/admin/img/document-upload.svg')}}"
                                                                     alt="img">
                                                                <h6
                                                                    class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                                    <span class="theme-clr">Click to upload</span>
                                                                    <br>
                                                                    Or drag and drop
                                                                </h6>
                                                            </div>
                                                            <img class="upload-file-img" loading="lazy" src=""
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
                                                                <button type="button" class="remove_btn btn icon-btn">
                                                                    <i class="tio-delete text-danger"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="fs-10 text-center mb-0 mt-lg-4 mt-3">
                                                    {{ translate('JPG, JPEG, PNG size : Max 2 MB')}} <span
                                                        class="font-medium text-title">{{ translate('(2:1)')}}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn--container justify-content-end mt-20">
                                    <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                                    <button type="submit" class="btn btn--primary mb-2">{{translate('Add')}}</button>
                                </div>
                            </div>
                        </form>

                        <div class="card">
                            <div class="card-header py-2 border-0">
                                <div class="d-flex w-100 flex-wrap gap-2 align-items-center justify-content-between">
                                    <h4 class="text-black m-0">Testimonial List</h4>
                                    <div class="search--button-wrapper flex-grow-0">
                                        <form class="search-form min--270">
                                            <!-- Search -->
                                            <div class="input-group input--group">
                                                <input id="" type="search" name="search" value="{{request('search')}}" class="form-control"
                                                       placeholder="Search Keywords" aria-label="Search here"
                                                       tabindex="1">
                                                <button type="submit" class="btn btn--secondary"><i
                                                        class="tio-search"></i></button>
                                            </div>
                                            <!-- End Search -->
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @php($search = request('search', ''))
                            @php($key = explode(' ', $search))
                            @php(
                                    $reviews = App\Models\ReactTestimonial::latest()
                                    ->when($search, function($query) use($key) {
                                        $query->where(function($q) use($key) {
                                            foreach($key as $value) {
                                                $q->orWhere('name', 'like', "%{$value}%")
                                                  ->orWhere('review', 'like', "%{$value}%");
                                            }
                                        });
                                    })
                                    ->paginate(config('default_pagination')))
                            <div class="card-body p-0">
                                <!-- Table -->
                                <div class="table-responsive datatable-custom">
                                    <table
                                        class="table table-borderless table-thead-borderless table-align-middle table-nowrap card-table m-0">
                                        <thead class="thead-light">
                                        <tr>
                                            <th class="border-top-0">{{translate('SL')}}</th>
                                            <th class="border-top-0">{{translate('Image')}}</th>
                                            <th class="border-top-0">{{translate('Reviewer Name')}}</th>
                                            <th class="border-top-0">{{translate('Designation')}}</th>
                                            <th class="border-top-0">{{translate('Reviews')}}</th>
                                            <th class="text-center border-top-0">{{translate('Status')}}</th>
                                            <th class="text-center border-top-0">{{translate('Action')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($reviews as $key => $review)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <img
                                                        src="{{ $review->reviewer_image_full_url ?? asset('/public/assets/admin/img/upload-3.png')}}"
                                                        data-onerror-image="{{asset('/public/assets/admin/img/upload-3.png')}}"
                                                        class="w-50px h-50px min-w-50px rounded onerror-image" alt="">
                                                </td>
                                                <td>
                                                    <div class="text--title">
                                                        {{ $review->name }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text--title">
                                                        {{ $review->designation }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div
                                                        class="text-wrap line-limit-3  max-w-400px min-w-176px text-title">
                                                        {{ $review->review }}
                                                    </div>
                                                </td>

                                                <td>
                                                    <label class="toggle-switch mx-auto toggle-switch-sm">
                                                        <input type="checkbox" data-id="status-{{ $review->id }}"
                                                               data-type="status"
                                                               data-image-on="{{ asset('/public/assets/admin/img/modal/this-criteria-on.png') }}"
                                                               data-image-off="{{ asset('/public/assets/admin/img/modal/this-criteria-off.png') }}"
                                                               data-title-on="{{ translate('by_turning_on_this_review') }} <strong>{{ translate('this_review') }}</strong>"
                                                               data-title-off="{{ translate('by_turning_off_this_review') }} <strong>{{ translate('this_review') }}</strong>"
                                                               data-text-on="<p>{{ translate('this_section_will_be_enabled_you_can_see_this_section_on_your_landing_page') }}</p>"
                                                               data-text-off="<p>{{ translate('this_section_will_be_disabled_you_can_enable_it_in_the_settings') }}</p>"
                                                               class="status toggle-switch-input dynamic-checkbox"
                                                               id="status-{{$review->id}}"
                                                            {{$review->status ? 'checked' : ''}}>
                                                        <span class="toggle-switch-label mx-auto">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                    </label>
                                                    <form
                                                        action="{{route('admin.business-settings.review-react-status', [$review->id, $review->status ? 0 : 1])}}"
                                                        method="get" id="status-{{$review->id}}_form">
                                                    </form>
                                                </td>

                                                <td>
                                                    <div class="btn--container justify-content-center">
                                                        <a class="btn action-btn btn-outline-theme-light"
                                                           href="{{route('admin.business-settings.review-react-edit', [$review['id']])}}">
                                                            <i class="tio-edit"></i>
                                                        </a>
                                                        <a class="btn action-btn btn--danger btn-outline-danger form-alert"
                                                           href="javascript:" data-id="review-{{$review['id']}}"
                                                           data-message="{{ translate('Want to delete this review ?') }}"
                                                           title="{{translate('messages.delete_review')}}"><i
                                                                class="tio-delete-outlined"></i>
                                                        </a>
                                                        <form
                                                            action="{{route('admin.business-settings.review-react-delete', [$review['id']])}}"
                                                            method="post" id="review-{{$review['id']}}">
                                                            @csrf @method('delete')
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    @if(count($reviews) === 0)
                                        <div class="empty--data">
                                            <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}"
                                                 alt="public">
                                            <h5>
                                                {{translate('no_data_found')}}
                                            </h5>
                                        </div>
                                    @endif
                                    <div class="page-area px-5 pb-3 mt-5">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <div>
                                                {!! $reviews->appends(request()->all())->links() !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Table -->
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <!-- Section View Offcanvas here -->
        <div id="testimonialAdd_section"
             class="custom-offcanvas offcanvas-750 offcanvas-xxl-1120 d-flex flex-column justify-content-between">
            <form action="{{ route('taxvat.store') }}" method="post">
                <div>
                    <div
                        class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                        <div class="py-1">
                            <h3 class="mb-0 line--limit-1">
                                {{ translate('messages.Deliveryman App Download Section Preview') }}</h3>
                        </div>
                        <button type="button"
                                class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"
                                aria-label="Close">
                            &times;
                        </button>
                    </div>
                    <div class="custom-offcanvas-body custom-offcanvas-body-100  p-20">
                        <section class="common-section-view bg-white border rounded-10 my-xl-2 mx-xl-2">
                            <div class="common-section-inner">
                                <div class="container p-0">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-xl-4 text-xl-start text-center">
                                            <h2 class="mb-xxl-2 mb-1 fs-24">
                                                {!! \App\CentralLogics\Helpers::highlightWords($testimonial_title?->value ?? 'Stories from Happy $Customers$') !!}
                                            </h2>
                                            <p class="text-title fs-12 mb-xxl-4 mb-xl-3 mb-3 px-xl-0 px-2">
                                                {{$testimonial_sub_title?->vaue ?? 'Hear from customers and partners who enjoy seamless shopping and fast
                                                delivery.'}}
                                            </p>
                                            <a href="#0"
                                               class="btn btn-primary-white base-bg-cmn fs-14 py-2 px-4 text-white font-weight-bold">
                                                {{$testimonial_button_title?->value ?? 'Get Started'}}
                                            </a>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="common-carousel-wrapper mx-xl-4 position-relative">
                                                <div class="testimonial-preview-slide owl-theme owl-carousel">
                                                    @php($reviews = App\Models\ReactTestimonial::latest()->take(10)->get())
                                                    @if(count($reviews)>0)
                                                        @foreach($reviews as $review)
                                                            <div class="items__">
                                                                <div
                                                                    class="shadow-testimonial-box border-0 p-xxl-4 p-3 text-center">
                                                                    <img
                                                                        src="{{ asset('/public/assets/admin/img/icons/testimonial-quote.png') }}"
                                                                        alt="" class="mb-20 min-w-40 mx-auto">
                                                                    <p class="fs-14 mb-3">{{$review->review}}</p>
                                                                    <div
                                                                        class="mx-auto w-60px border-bottom mb-3"></div>
                                                                    <div class="specialist text-center">
                                                                        <img wdith="42" height="42"
                                                                             src="{{ $review->reviewer_image_full_url ?? asset('/public/assets/admin/img/400x400/alamin-hasan.jpg') }}"
                                                                             alt=""
                                                                             class="rounded-pill w-42 mx-auto min-w-42 mb-2">
                                                                        <h2 class="mb-1 fs-14">
                                                                            {{$review?->name}}</h2>
                                                                        <p class="mb-0 color-22232466 fs-12">
                                                                            {{ $review?->designation }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="items__">
                                                            <div
                                                                class="shadow-testimonial-box border-0 p-xxl-4 p-3 text-center">
                                                                <img
                                                                    src="{{ asset('/public/assets/admin/img/icons/testimonial-quote.png') }}"
                                                                    alt="" class="mb-20 min-w-40 mx-auto">
                                                                <p class="fs-14 mb-3">Lorem ipsum dolor sit amet,
                                                                    consectetur
                                                                    adipiscing elit. Quisque diam pellentesque bibendum
                                                                    non
                                                                    dui
                                                                    volutpat fringilla </p>
                                                                <div class="mx-auto w-60px border-bottom mb-3"></div>
                                                                <div class="specialist text-center">
                                                                    <img wdith="42" height="42"
                                                                         src="{{ asset('/public/assets/admin/img/400x400/alamin-hasan.jpg') }}"
                                                                         alt=""
                                                                         class="rounded-pill w-42 mx-auto min-w-42 mb-2">
                                                                    <h2 class="mb-1 fs-14">
                                                                        {{ translate('messages.Alamin Hasan') }}</h2>
                                                                    <p class="mb-0 color-22232466 fs-12">
                                                                        {{ translate('messages.Food Specialist') }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="items__">
                                                            <div
                                                                class="shadow-testimonial-box border-0 p-xxl-4 p-3 text-center">
                                                                <img
                                                                    src="{{ asset('/public/assets/admin/img/icons/testimonial-quote.png') }}"
                                                                    alt="" class="mb-20 min-w-40 mx-auto">
                                                                <p class="fs-14 mb-3">Lorem ipsum dolor sit amet,
                                                                    consectetur
                                                                    adipiscing elit. Quisque diam pellentesque bibendum
                                                                    non
                                                                    dui
                                                                    volutpat fringilla </p>
                                                                <div class="mx-auto w-60px border-bottom mb-3"></div>
                                                                <div class="specialist text-center">
                                                                    <img wdith="42" height="42"
                                                                         src="{{ asset('/public/assets/admin/img/400x400/alamin-hasan.jpg') }}"
                                                                         alt=""
                                                                         class="rounded-pill w-42 mx-auto min-w-42 mb-2">
                                                                    <h2 class="mb-1 fs-14">
                                                                        {{ translate('messages.Alamin Hasan') }}</h2>
                                                                    <p class="mb-0 color-22232466 fs-12">
                                                                        {{ translate('messages.Food Specialist') }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="items__">
                                                            <div
                                                                class="shadow-testimonial-box border-0 p-xxl-4 p-3 text-center">
                                                                <img
                                                                    src="{{ asset('/public/assets/admin/img/icons/testimonial-quote.png') }}"
                                                                    alt="" class="mb-20 min-w-40 mx-auto">
                                                                <p class="fs-14 mb-3">Lorem ipsum dolor sit amet,
                                                                    consectetur
                                                                    adipiscing elit. Quisque diam pellentesque bibendum
                                                                    non
                                                                    dui
                                                                    volutpat fringilla </p>
                                                                <div class="mx-auto w-60px border-bottom mb-3"></div>
                                                                <div class="specialist text-center">
                                                                    <img wdith="42" height="42"
                                                                         src="{{ asset('/public/assets/admin/img/400x400/alamin-hasan.jpg') }}"
                                                                         alt=""
                                                                         class="rounded-pill w-42 mx-auto min-w-42 mb-2">
                                                                    <h2 class="mb-1 fs-14">
                                                                        {{ translate('messages.Alamin Hasan') }}</h2>
                                                                    <p class="mb-0 color-22232466 fs-12">
                                                                        {{ translate('messages.Food Specialist') }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="custom-owl-nav z-2">
                                                    <button type="button"
                                                            class="custom-prev__ btn border-0 outline-none p-2"><i
                                                            class="tio-chevron-left"></i></button>
                                                    <button type="button"
                                                            class="custom-next__ btn border-0 outline-none p-2"><i
                                                            class="tio-chevron-right"></i></button>
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

        <!-- How it Works -->
    @include('admin-views.business-settings.landing-page-settings.partial.how-it-work-react')
@endsection
