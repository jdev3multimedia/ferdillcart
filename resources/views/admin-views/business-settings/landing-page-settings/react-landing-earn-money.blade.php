@php use App\CentralLogics\Helpers;use App\Models\BusinessSetting;use App\Models\DataSetting; @endphp
@extends('layouts.admin.app')

@section('title', translate('messages.react_landing_page'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header pb-0">
            <div class="d-flex flex-wrap justify-content-between">
                <h1 class="page-header-title">
                    <span class="page-header-icon">
                        <img src="{{ asset('public/assets/admin/img/landing.png') }}" class="w--20" alt="">
                    </span>
                    <span>
                        {{ translate('messages.react_landing_page') }}
                    </span>
                </h1>
                <div class="text--primary-2 py-1 d-flex flex-wrap align-items-center" type="button" data-toggle="modal"
                     data-target="#how-it-works">
                    <strong class="mr-2">{{ translate('See_how_it_works!') }}</strong>
                    <div>
                        <i class="tio-info-outined"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-4 mt-2">
            <div class="js-nav-scroller hs-nav-scroller-horizontal">
                @include('admin-views.business-settings.landing-page-settings.top-menu-links.react-landing-page-links')
            </div>
        </div>
        @php($join_seller_react_status = DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'join_seller_react_status')->first()?->value)
        @php($join_DM_react_status = DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'join_DM_react_status')->first()?->value)


        @php($earning_title = DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'earning_title')->first())
        @php($earning_sub_title = DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'earning_sub_title')->first())
        @php($earning_seller_title = DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'earning_seller_title')->first())
        @php($earning_seller_sub_title = DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'earning_seller_sub_title')->first())
        @php($earning_seller_button_name = DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'earning_seller_button_name')->first())
        @php($earning_seller_button_url = DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'earning_seller_button_url')->first())
        @php($earning_dm_title = DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'earning_dm_title')->first())
        @php($earning_dm_sub_title = DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'earning_dm_sub_title')->first())
        @php($earning_dm_button_name = DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'earning_dm_button_name')->first())
        @php($earning_dm_button_url = DataSetting::withoutGlobalScope('translate')->where('type', 'react_landing_page')->where('key', 'earning_dm_button_url')->first())
        @php($language = BusinessSetting::where('key', 'language')->first())
        @php($language = $language->value ?? null)
        @php($defaultLang = str_replace('_', '-', app()->getLocale()))
        @if ($language)
            <ul class="nav nav-tabs mb-4 border-0">
                <li class="nav-item">
                    <a class="nav-link lang_link active" href="#"
                       id="default-link">{{ translate('messages.default') }}</a>
                </li>
                @foreach (json_decode($language) as $lang)
                    <li class="nav-item">
                        <a class="nav-link lang_link" href="#"
                           id="{{ $lang }}-link">{{ Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                    </li>
                @endforeach
            </ul>
        @endif
        <div class="tab-content">
            <div class="tab-pane fade show active">
                <form action="{{ route('admin.business-settings.react-landing-page-settings', 'earning-title') }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    <h5 class="card-title mt-3 mb-3">
                        <span class="card-header-icon mr-2"><i class="tio-settings-outlined"></i></span>
                        <span>{{ translate('Download User App Section Content ') }}</span>
                    </h5>
                    <div class="card mb-3">
                        <div class="card-body">

                            @if ($language)
                                <div class="row g-3 lang_form default-form">
                                    <div class="col-sm-6">
                                        <label for="earning_title" class="form-label">{{ translate('Title') }}
                                            ({{ translate('messages.default') }})
                                            <span class="form-label-secondary" data-toggle="tooltip"
                                                  data-placement="right"
                                                  data-original-title="{{ translate('Write_the_title_within_40_characters') }}">
                                                <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                     alt="">
                                            </span></label>
                                        <input id="earning_title" type="text" maxlength="40" name="earning_title[]"
                                               class="form-control"
                                               value="{{ $earning_title?->getRawOriginal('value') ?? '' }}"
                                               placeholder="{{ translate('messages.title_here...') }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="earning_sub_title" class="form-label">{{ translate('Sub Title') }}
                                            ({{ translate('messages.default') }})
                                            <span class="form-label-secondary" data-toggle="tooltip"
                                                  data-placement="right"
                                                  data-original-title="{{ translate('Write_the_title_within_80_characters') }}">
                                                <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                     alt="">
                                            </span></label>
                                        <input id="earning_sub_title" type="text" maxlength="80"
                                               name="earning_sub_title[]" class="form-control"
                                               value="{{ $earning_sub_title?->getRawOriginal('value') ?? '' }}"
                                               placeholder="{{ translate('messages.sub_title_here...') }}">
                                    </div>
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                                @foreach (json_decode($language) as $lang)
                                        <?php
                                        if (isset($earning_title->translations) && count($earning_title->translations)) {
                                            $earning_title_translate = [];
                                            foreach ($earning_title->translations as $t) {
                                                if ($t->locale == $lang && $t->key == 'earning_title') {
                                                    $earning_title_translate[$lang]['value'] = $t->value;
                                                }
                                            }
                                        }
                                        if (isset($earning_sub_title->translations) && count($earning_sub_title->translations)) {
                                            $earning_sub_title_translate = [];
                                            foreach ($earning_sub_title->translations as $t) {
                                                if ($t->locale == $lang && $t->key == 'earning_sub_title') {
                                                    $earning_sub_title_translate[$lang]['value'] = $t->value;
                                                }
                                            }
                                        }
                                        ?>
                                    <div class="row g-3 d-none lang_form" id="{{ $lang }}-form">
                                        <div class="col-sm-6">
                                            <label for="earning_title{{ $lang }}"
                                                   class="form-label">{{ translate('Title') }} ({{ strtoupper($lang) }})<span
                                                    class="form-label-secondary" data-toggle="tooltip"
                                                    data-placement="right"
                                                    data-original-title="{{ translate('Write_the_title_within_40_characters') }}">
                                                    <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                         alt="">
                                                </span></label>
                                            <input id="earning_title{{ $lang }}" type="text" maxlength="40"
                                                   name="earning_title[]" class="form-control"
                                                   value="{{ $earning_title_translate[$lang]['value'] ?? '' }}"
                                                   placeholder="{{ translate('messages.title_here...') }}">
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="earning_sub_title{{ $lang }}"
                                                   class="form-label">{{ translate('Sub Title') }}
                                                ({{ strtoupper($lang) }})
                                                <span class="form-label-secondary" data-toggle="tooltip"
                                                      data-placement="right"
                                                      data-original-title="{{ translate('Write_the_title_within_80_characters') }}">
                                                    <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                         alt="">
                                                </span></label>
                                            <input type="text" id="earning_sub_title{{ $lang }}" maxlength="80"
                                                   name="earning_sub_title[]" class="form-control"
                                                   value="{{ $earning_sub_title_translate[$lang]['value'] ?? '' }}"
                                                   placeholder="{{ translate('messages.sub_title_here...') }}">
                                        </div>
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{ $lang }}">
                                @endforeach
                            @else
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label for="earning_title" class="form-label">{{ translate('Title') }}</label>
                                        <input id="earning_title" type="text" name="earning_title[]"
                                               class="form-control"
                                               placeholder="{{ translate('messages.title_here...') }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="earning_sub_title"
                                               class="form-label">{{ translate('Sub Title') }}</label>
                                        <input id="earning_sub_title" type="text" name="earning_sub_title[]"
                                               class="form-control"
                                               placeholder="{{ translate('messages.sub_title_here...') }}">
                                    </div>
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                            @endif
                            <div class="btn--container justify-content-end mt-20">
                                <button type="reset" class="btn btn--reset mb-2">{{ translate('Reset') }}</button>
                                <button type="submit" class="btn btn--primary mb-2">{{ translate('Save') }}</button>
                            </div>
                        </div>
                    </div>
                </form>

                <form action="{{ route('admin.business-settings.react-landing-page-settings', 'earning-seller-link') }}"
                      method="post" id="join_seller_react_status_form">
                    @csrf
                    <input type="hidden" name="join_seller_react_status" value="{{ $join_seller_react_status ?? 0 }}">
                </form>

                <form action="{{ route('admin.business-settings.react-landing-page-settings', 'earning-seller-link') }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mt-3 mb-3">
                                <span class="card-header-icon mr-2"><i class="tio-settings-outlined"></i></span>
                                <span>{{ translate('Seller Section Content') }}</span>
                            </h5>

                            <label class="toggle-switch justify-content-end  rounded">
                                <input type="checkbox" data-id="join_seller_react_status" data-type="status"
                                       data-image-on="{{ asset('/public/assets/admin/img/modal/seller-app-on.png') }}"
                                       data-image-off="{{ asset('/public/assets/admin/img/modal/seller-app-off.png') }}"
                                       data-title-on="<strong>{{ translate('messages.Want_to_enable_Seller_Section_Content?') }}</strong>"
                                       data-title-off="<strong>{{ translate('messages.Want_to_disable_Seller_Section_Content?') }}</strong>"
                                       data-text-on="<p>{{ translate('messages.If_you_enable_this,_Seller_Section_Content_will_be_visible.') }}</p>"
                                       data-text-off="<p>{{ translate('messages.If_you_disable_this,_Seller_Section_Content_will_not_be_visible.') }}</p>"
                                       class="status toggle-switch-input dynamic-checkbox" value="1" name=""
                                       id="join_seller_react_status" {{ $join_seller_react_status == 1 ? 'checked' : '' }}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>


                        <div class="card-body {{ $join_seller_react_status != 1 ? 'd-none' : '' }}">
                            <div class="row g-3">
                                <div class="col-12">
                                    @if ($language)
                                        <div class="row g-3 lang_form default-form">
                                            <div class="col-sm-6">
                                                <label for="earning_seller_title"
                                                       class="form-label">{{ translate('Title') }}
                                                    ({{ translate('messages.default') }})<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_30_characters') }}">
                                                        <img
                                                            src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <input id="earning_seller_title" type="text" maxlength="30"
                                                       name="earning_seller_title[]" class="form-control"
                                                       value="{{ $earning_seller_title?->getRawOriginal('value') ?? '' }}"
                                                       placeholder="{{ translate('messages.title_here...') }}">
                                            </div>

                                            <div class="col-sm-6">
                                                <label for="earning_seller_button_name"
                                                       class="form-label text-capitalize">
                                                    {{ translate('Button Name') }}({{ translate('messages.default') }})
                                                    <span class="form-label-secondary" data-toggle="tooltip"
                                                          data-placement="right"
                                                          data-original-title="{{ translate('Write_the_title_within_20_characters') }}">
                                                        <img
                                                            src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <input id="earning_seller_button_name" type="text" maxlength="20"
                                                       name="earning_seller_button_name[]"
                                                       value="{{ $earning_seller_button_name?->getRawOriginal('value') ?? '' }}"
                                                       placeholder="{{ translate('Ex: Order now') }}"
                                                       class="form-control h--45px">
                                            </div>

                                            <div class="col-sm-6">
                                                <label for="earning_seller_sub_title"
                                                       class="form-label">{{ translate('Sub Title') }}
                                                    ({{ translate('messages.default') }})<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_65_characters') }}">
                                                        <img
                                                            src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <textarea id="earning_seller_sub_title" maxlength="65"
                                                          name="earning_seller_sub_title[]" class="form-control"
                                                          placeholder="{{ translate('messages.sub_title_here...') }}"
                                                          rows="2">{{ $earning_seller_sub_title?->getRawOriginal('value') ?? '' }}</textarea>
                                            </div>

                                        </div>
                                        <input type="hidden" name="lang[]" value="default">
                                        @foreach (json_decode($language) as $lang)
                                                <?php
                                                if (isset($earning_seller_title->translations) && count($earning_seller_title->translations)) {
                                                    $earning_seller_title_translate = [];
                                                    foreach ($earning_seller_title->translations as $t) {
                                                        if ($t->locale == $lang && $t->key == 'earning_seller_title') {
                                                            $earning_seller_title_translate[$lang]['value'] = $t->value;
                                                        }
                                                    }
                                                }
                                                if (isset($earning_seller_sub_title->translations) && count($earning_seller_sub_title->translations)) {
                                                    $earning_seller_sub_title_translate = [];
                                                    foreach ($earning_seller_sub_title->translations as $t) {
                                                        if ($t->locale == $lang && $t->key == 'earning_seller_sub_title') {
                                                            $earning_seller_sub_title_translate[$lang]['value'] = $t->value;
                                                        }
                                                    }
                                                }
                                                if (isset($earning_seller_button_name->translations) && count($earning_seller_button_name->translations)) {
                                                    $earning_seller_button_name_translate = [];
                                                    foreach ($earning_seller_button_name->translations as $t) {
                                                        if ($t->locale == $lang && $t->key == 'earning_seller_button_name') {
                                                            $earning_seller_button_name_translate[$lang]['value'] = $t->value;
                                                        }
                                                    }
                                                }
                                                ?>
                                            <div class="row g-3 d-none lang_form" id="{{ $lang }}-form1">
                                                <div class="col-sm-6">
                                                    <label for="earning_seller_title{{ $lang }}"
                                                           class="form-label">{{ translate('Title') }}
                                                        ({{ strtoupper($lang) }})
                                                        <span class="form-label-secondary" data-toggle="tooltip"
                                                              data-placement="right"
                                                              data-original-title="{{ translate('Write_the_title_within_30_characters') }}">
                                                            <img
                                                                src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                                alt="">
                                                        </span></label>
                                                    <input id="earning_seller_title{{ $lang }}" type="text"
                                                           maxlength="30" name="earning_seller_title[]"
                                                           class="form-control"
                                                           value="{{ $earning_seller_title_translate[$lang]['value'] ?? '' }}"
                                                           placeholder="{{ translate('messages.title_here...') }}">
                                                </div>

                                                <div class="col-sm-6">
                                                    <label for="earning_seller_button_name{{ $lang }}"
                                                           class="form-label text-capitalize">
                                                        {{ translate('Button Name') }}({{ strtoupper($lang) }})
                                                        <span class="form-label-secondary" data-toggle="tooltip"
                                                              data-placement="right"
                                                              data-original-title="{{ translate('Write_the_title_within_20_characters') }}">
                                                            <img
                                                                src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                                alt="">
                                                        </span></label>
                                                    <input id="earning_seller_button_name{{ $lang }}"
                                                           type="text" maxlength="20"
                                                           name="earning_seller_button_name[]"
                                                           value="{{ $earning_seller_button_name_translate[$lang]['value'] ?? '' }}"
                                                           placeholder="{{ translate('Ex: Order now') }}"
                                                           class="form-control h--45px">
                                                </div>

                                                <div class="col-sm-6">
                                                    <label for="earning_seller_sub_title{{ $lang }}"
                                                           class="form-label">{{ translate('Sub Title') }}
                                                        ({{ strtoupper($lang) }})<span class="form-label-secondary"
                                                                                       data-toggle="tooltip"
                                                                                       data-placement="right"
                                                                                       data-original-title="{{ translate('Write_the_title_within_65_characters') }}">
                                                            <img
                                                                src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                                alt="">
                                                        </span></label>
                                                    <textarea id="earning_seller_sub_title{{ $lang }}" maxlength="65"
                                                              name="earning_seller_sub_title[]"
                                                              class="form-control"
                                                              placeholder="{{ translate('messages.sub_title_here...') }}"
                                                              rows="2">{{ $earning_seller_sub_title_translate[$lang]['value'] ?? '' }}</textarea>
                                                </div>


                                            </div>
                                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                                        @endforeach
                                    @else
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <label for="earning_seller_title"
                                                       class="form-label">{{ translate('Title') }}</label>
                                                <input id="earning_seller_title" type="text"
                                                       name="earning_seller_title[]" class="form-control"
                                                       placeholder="{{ translate('messages.title_here...') }}">
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="earning_seller_sub_title"
                                                       class="form-label">{{ translate('Sub Title') }}</label>
                                                <input id="earning_seller_sub_title" type="text"
                                                       name="earning_seller_sub_title[]" class="form-control"
                                                       placeholder="{{ translate('messages.sub_title_here...') }}">
                                            </div>

                                            <div class="col-sm-6">
                                                <label for="earning_seller_button_name"
                                                       class="form-label text-capitalize">
                                                    {{ translate('Button Name') }}

                                                </label>
                                                <input id="earning_seller_button_name" type="text"
                                                       placeholder="{{ translate('Ex: Order now') }}"
                                                       class="form-control h--45px" name="earning_seller_button_name[]">
                                            </div>
                                        </div>
                                        <input type="hidden" name="lang[]" value="default">
                                    @endif
                                </div>

                            </div>
                            <div class="btn--container justify-content-end mt-20">
                                <button type="reset" class="btn btn--reset mb-2">{{ translate('Reset') }}</button>
                                <button type="submit" class="btn btn--primary mb-2">{{ translate('Save') }}</button>
                            </div>
                        </div>
                    </div>
                </form>


                <form action="{{ route('admin.business-settings.react-landing-page-settings', 'earning-dm-link') }}"
                      method="post" id="join_DM_react_status_form">
                    @csrf
                    <input type="hidden" name="join_DM_react_status" value="{{ $join_DM_react_status ?? 0 }}">
                </form>


                <form action="{{ route('admin.business-settings.react-landing-page-settings', 'earning-dm-link') }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf


                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mt-3 mb-3">
                                <span class="card-header-icon mr-2"><i class="tio-settings-outlined"></i></span>
                                <span>{{ translate('Deliveryman_Section_Content') }}</span>
                            </h5>

                            <label class="toggle-switch justify-content-end  rounded">
                                <input type="checkbox" data-id="join_DM_react_status" data-type="status"
                                       data-image-on="{{ asset('/public/assets/admin/img/modal/seller-app-on.png') }}"
                                       data-image-off="{{ asset('/public/assets/admin/img/modal/seller-app-off.png') }}"
                                       data-title-on="<strong>{{ translate('messages.Want_to_enable_Deliveryman_Section_Content?') }}</strong>"
                                       data-title-off="<strong>{{ translate('messages.Want_to_disable_Deliveryman_Section_Content?') }}</strong>"
                                       data-text-on="<p>{{ translate('messages.If_you_enable_this,_Deliveryman_Section_Content_will_be_visible.') }}</p>"
                                       data-text-off="<p>{{ translate('messages.If_you_disable_this,_Deliveryman_Section_Content_will_not_be_visible.') }}</p>"
                                       class="status toggle-switch-input dynamic-checkbox" value="1" name=""
                                       id="join_DM_react_status" {{ $join_DM_react_status == 1 ? 'checked' : '' }}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>


                        <div class="card-body {{ $join_DM_react_status != 1 ? 'd-none' : '' }}">

                            <div class="row g-3">
                                <div class="col-12">
                                    @if ($language)
                                        <div class="row g-3 lang_form default-form">
                                            <div class="col-sm-6">
                                                <label for="earning_dm_title"
                                                       class="form-label">{{ translate('Title') }}
                                                    ({{ translate('messages.default') }})<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_30_characters') }}">
                                                        <img
                                                            src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <input id="earning_dm_title" type="text" maxlength="30"
                                                       name="earning_dm_title[]" class="form-control"
                                                       value="{{ $earning_dm_title?->getRawOriginal('value') ?? '' }}"
                                                       placeholder="{{ translate('messages.title_here...') }}">
                                            </div>


                                            <div class="col-sm-6">
                                                <label for="earning_dm_button_name" class="form-label text-capitalize">
                                                    {{ translate('Button Name') }}({{ translate('messages.default') }})
                                                    <span class="form-label-secondary" data-toggle="tooltip"
                                                          data-placement="right"
                                                          data-original-title="{{ translate('Write_the_title_within_20_characters') }}">
                                                        <img
                                                            src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <input id="earning_dm_button_name" type="text" maxlength="20"
                                                       name="earning_dm_button_name[]"
                                                       value="{{ $earning_dm_button_name?->getRawOriginal('value') ?? '' }}"
                                                       placeholder="{{ translate('Ex: Order now') }}"
                                                       class="form-control h--45px">
                                            </div>


                                            <div class="col-sm-6">
                                                <label for="earning_dm_sub_title"
                                                       class="form-label">{{ translate('Sub Title') }}
                                                    ({{ translate('messages.default') }})<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_65_characters') }}">
                                                        <img
                                                            src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <textarea id="earning_dm_sub_title" maxlength="65"
                                                          name="earning_dm_sub_title[]" class="form-control"
                                                          placeholder="{{ translate('messages.sub_title_here...') }}"
                                                          rows="2">{{ $earning_dm_sub_title?->getRawOriginal('value') ?? '' }}</textarea>

                                            </div>

                                        </div>
                                        <input type="hidden" name="lang[]" value="default">
                                        @foreach (json_decode($language) as $lang)
                                                <?php
                                                if (isset($earning_dm_title->translations) && count($earning_dm_title->translations)) {
                                                    $earning_dm_title_translate = [];
                                                    foreach ($earning_dm_title->translations as $t) {
                                                        if ($t->locale == $lang && $t->key == 'earning_dm_title') {
                                                            $earning_dm_title_translate[$lang]['value'] = $t->value;
                                                        }
                                                    }
                                                }
                                                if (isset($earning_dm_sub_title->translations) && count($earning_dm_sub_title->translations)) {
                                                    $earning_dm_sub_title_translate = [];
                                                    foreach ($earning_dm_sub_title->translations as $t) {
                                                        if ($t->locale == $lang && $t->key == 'earning_dm_sub_title') {
                                                            $earning_dm_sub_title_translate[$lang]['value'] = $t->value;
                                                        }
                                                    }
                                                }
                                                if (isset($earning_dm_button_name->translations) && count($earning_dm_button_name->translations)) {
                                                    $earning_dm_button_name_translate = [];
                                                    foreach ($earning_dm_button_name->translations as $t) {
                                                        if ($t->locale == $lang && $t->key == 'earning_dm_button_name') {
                                                            $earning_dm_button_name_translate[$lang]['value'] = $t->value;
                                                        }
                                                    }
                                                }
                                                ?>
                                            <div class="row g-3 d-none lang_form" id="{{ $lang }}-form3">
                                                <div class="col-sm-6">
                                                    <label for="earning_dm_title{{ $lang }}"
                                                           class="form-label">{{ translate('Title') }}
                                                        ({{ strtoupper($lang) }})
                                                        <span class="form-label-secondary" data-toggle="tooltip"
                                                              data-placement="right"
                                                              data-original-title="{{ translate('Write_the_title_within_30_characters') }}">
                                                            <img
                                                                src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                                alt="">
                                                        </span></label>
                                                    <input id="earning_dm_title{{ $lang }}" type="text"
                                                           maxlength="30" name="earning_dm_title[]" class="form-control"
                                                           value="{{ $earning_dm_title_translate[$lang]['value'] ?? '' }}"
                                                           placeholder="{{ translate('messages.title_here...') }}">
                                                </div>

                                                <div class="col-sm-6">
                                                    <label for="earning_dm_button_name{{ $lang }}"
                                                           class="form-label text-capitalize">
                                                        {{ translate('Button Name') }}({{ strtoupper($lang) }})
                                                        <span class="form-label-secondary" data-toggle="tooltip"
                                                              data-placement="right"
                                                              data-original-title="{{ translate('Write_the_title_within_20_characters') }}">
                                                            <img
                                                                src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                                alt="">
                                                        </span></label>
                                                    <input id="earning_dm_button_name{{ $lang }}" type="text"
                                                           maxlength="20" name="earning_dm_button_name[]"
                                                           value="{{ $earning_dm_button_name_translate[$lang]['value'] ?? '' }}"
                                                           placeholder="{{ translate('Ex: Order now') }}"
                                                           class="form-control h--45px">
                                                </div>


                                                <div class="col-sm-6">
                                                    <label for="earning_dm_sub_title{{ $lang }}"
                                                           class="form-label">{{ translate('Sub Title') }}
                                                        ({{ strtoupper($lang) }})<span class="form-label-secondary"
                                                                                       data-toggle="tooltip"
                                                                                       data-placement="right"
                                                                                       data-original-title="{{ translate('Write_the_title_within_65_characters') }}">
                                                            <img
                                                                src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                                alt="">
                                                        </span></label>
                                                    <textarea id="earning_dm_sub_title{{ $lang }}" maxlength="65"
                                                              name="earning_dm_sub_title[]"
                                                              class="form-control"
                                                              placeholder="{{ translate('messages.sub_title_here...') }}"
                                                              rows="2">{{ $earning_dm_sub_title_translate[$lang]['value'] ?? '' }}</textarea>

                                                </div>

                                            </div>
                                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                                        @endforeach
                                    @else
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <label for="earning_dm_title"
                                                       class="form-label">{{ translate('Title') }}</label>
                                                <input id="earning_dm_title" type="text" name="earning_dm_title[]"
                                                       class="form-control"
                                                       placeholder="{{ translate('messages.title_here...') }}">
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="earning_dm_sub_title"
                                                       class="form-label">{{ translate('Sub Title') }}</label>
                                                <input id="earning_dm_sub_title" type="text"
                                                       name="earning_dm_sub_title[]" class="form-control"
                                                       placeholder="{{ translate('messages.sub_title_here...') }}">
                                            </div>

                                            <div class="col-sm-6">
                                                <label for="earning_dm_button_name" class="form-label text-capitalize">
                                                    {{ translate('Button Name') }}

                                                </label>
                                                <input id="earning_dm_button_name" type="text"
                                                       placeholder="{{ translate('Ex: Order now') }}"
                                                       class="form-control h--45px" name="earning_dm_button_name[]">
                                            </div>

                                        </div>
                                        <input type="hidden" name="lang[]" value="default">
                                    @endif
                                </div>

                            </div>
                            <div class="btn--container justify-content-end mt-20">
                                <button type="reset" class="btn btn--reset mb-2">{{ translate('Reset') }}</button>
                                <button type="submit" class="btn btn--primary mb-2">{{ translate('Save') }}</button>
                            </div>
                        </div>
                    </div>
                </form>


            </div>
        </div>
    </div>
    <!-- How it Works -->
    @include('admin-views.business-settings.landing-page-settings.partial.how-it-work-react')
@endsection
