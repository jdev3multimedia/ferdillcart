@extends('layouts.admin.app')

@section('title',translate('messages.admin_landing_page'))

@section('content')
<div class="content container-fluid">
    <div class="page-header pb-0">
        <div class="d-flex flex-wrap justify-content-between">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/landing.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{ translate('messages.admin_landing_pages') }}
                </span>
            </h1>
            <div class="text--primary-2 py-1 d-flex flex-wrap align-items-center" type="button" data-toggle="modal" data-target="#how-it-works">
                <strong class="mr-2">{{translate('See_how_it_works!')}}</strong>
                <div>
                    <i class="tio-info-outined"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-20 mt-2">
        <div class="js-nav-scroller hs-nav-scroller-horizontal">
            @include('admin-views.business-settings.landing-page-settings.top-menu-links.admin-landing-page-links')
        </div>
    </div>
    @php($earning_title=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','admin_landing_page')->where('key','earning_title')->first())
    @php($earning_sub_title=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','admin_landing_page')->where('key','earning_sub_title')->first())
    @php($earning_seller_image=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','admin_landing_page')->where('key','earning_seller_image')->first())
    @php($earning_delivery_image=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','admin_landing_page')->where('key','earning_delivery_image')->first())
    @php($language=\App\Models\BusinessSetting::where('key','language')->first())
    @php($language = $language->value ?? null)
    @php($defaultLang = str_replace('_', '-', app()->getLocale()))
    @if($language)
        <ul class="nav nav-tabs mb-4 border-0">
            <li class="nav-item">
                <a class="nav-link lang_link active"
                href="#"
                id="default-link">{{translate('messages.default')}}</a>
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
    <div class="tab-content">
        <div class="tab-pane fade show active">
            <form action="{{ route('admin.business-settings.admin-landing-page-settings', 'earning-title') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h5 class="card-title mb-3">
                    <span class="card-header-icon mr-2"><i class="tio-settings-outlined"></i></span> <span>{{translate('Download User App Section Content ')}}</span>
                </h5>
                <div class="card mb-3">
                    <div class="card-body">
                        @if ($language)
                            <div class="row g-3 lang_form" id="default-form">
                                <div class="col-sm-6">
                                    <label for="earning_title" class="form-label">{{translate('Title')}} ({{ translate('messages.default') }})<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_40_characters') }}">
                                                        <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span>
                                        <span class="form-label-secondary text-danger"
                                              data-toggle="tooltip" data-placement="right"
                                              data-original-title="{{ translate('messages.Required.')}}"> *
                                                </span></label>
                                                <input required id="earning_title" type="text" maxlength="40" name="earning_title[]" class="form-control" value="{{$earning_title?->getRawOriginal('value')}}" placeholder="{{translate('messages.title_here...')}}">
                                </div>
                                <div class="col-sm-6">
                                    <label for="sub-text" class="form-label">{{translate('Sub Title')}} ({{ translate('messages.default') }})<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_80_characters') }}">
                                                        <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span>
                                        <span class="form-label-secondary text-danger"
                                              data-toggle="tooltip" data-placement="right"
                                              data-original-title="{{ translate('messages.Required.')}}"> *
                                                </span></label>
                                                <input required id="sub-text" type="text" maxlength="80" name="earning_sub_title[]" class="form-control" value="{{$earning_sub_title?->getRawOriginal('value')}}" placeholder="{{translate('messages.sub_title_here...')}}">
                                </div>
                            </div>
                            <input type="hidden" name="lang[]" value="default">
                                @foreach(json_decode($language) as $lang)
                                <?php
                                if(isset($earning_title->translations)&&count($earning_title->translations)){
                                        $earning_title_translate = [];
                                        foreach($earning_title->translations as $t)
                                        {
                                            if($t->locale == $lang && $t->key=='earning_title'){
                                                $earning_title_translate[$lang]['value'] = $t->value;
                                            }
                                        }

                                    }
                                if(isset($earning_sub_title->translations)&&count($earning_sub_title->translations)){
                                        $earning_sub_title_translate = [];
                                        foreach($earning_sub_title->translations as $t)
                                        {
                                            if($t->locale == $lang && $t->key=='earning_sub_title'){
                                                $earning_sub_title_translate[$lang]['value'] = $t->value;
                                            }
                                        }

                                    }
                                    ?>
                                    <div class="row g-3 d-none lang_form" id="{{$lang}}-form">
                                        <div class="col-sm-6">
                                            <label for="earning_title" class="form-label">{{translate('Title')}} ({{strtoupper($lang)}})<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_40_characters') }}">
                                                        <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <input id="earning_title" type="text" maxlength="40" name="earning_title[]" class="form-control" value="{{ $earning_title_translate[$lang]['value']?? '' }}" placeholder="{{translate('messages.title_here...')}}">
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="sub-title" class="form-label">{{translate('Sub Title')}} ({{strtoupper($lang)}})<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_80_characters') }}">
                                                        <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <input id="sub-title" type="text" maxlength="80" name="earning_sub_title[]" class="form-control" value="{{ $earning_sub_title_translate[$lang]['value']?? '' }}" placeholder="{{translate('messages.sub_title_here...')}}">
                                        </div>
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                @endforeach
                            @else
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label for="earning-title" class="form-label">{{translate('Title')}}<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_40_characters') }}">
                                                        <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <input  id="earning-title" type="text" maxlength="40" name="earning_title[]" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="earning-sub-title" class="form-label">{{translate('Sub Title')}}<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_80_characters') }}">
                                                        <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <input id="earning-sub-title" type="text" maxlength="80" name="earning_sub_title[]" class="form-control" placeholder="{{translate('messages.sub_title_here...')}}">
                                    </div>
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                            @endif
                        <div class="btn--container justify-content-end mt-20">
                            <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                            <button type="submit"   class="btn btn--primary mb-2">{{translate('Save')}}</button>
                        </div>
                    </div>
                </div>
            </form>
            <form action="{{ route('admin.business-settings.admin-landing-page-settings', 'earning-seller-link') }}" method="POST" enctype="multipart/form-data">
                @php($seller_app_links = \App\Models\DataSetting::where(['key'=>'seller_app_earning_links','type'=>'admin_landing_page'])->first())
                @php($seller_app_links = isset($seller_app_links->value)?json_decode($seller_app_links->value, true):null)

                @csrf
                <h5 class="card-title mb-3">
                    <span class="card-header-icon mr-2"><i class="tio-settings-outlined"></i></span> <span>{{translate('Download_Store_App_Section')}}</span>
                </h5>
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="form-label d-block mb-3">
                                    {{translate('Banner')}}  <span class="text--primary">{{translate('(size: 3:1)')}}</span>
                                    <span class="form-label-secondary text-danger"
                                          data-toggle="tooltip" data-placement="right"
                                          data-original-title="{{ translate('messages.Required.')}}"> *
                                                </span>
                                    <div class="fs-12 opacity-70">
                                        {{ translate(IMAGE_FORMAT.' ' . 'Less Than 2MB') }}
                                    </div>
                                </label>
                                <label class="upload-img-3 m-0 d-block">
                                    <div class="position-relative">
                                    <div class="img">
                                        <img  src="{{\App\CentralLogics\Helpers::get_full_url('earning', $earning_seller_image?->value?? '', $earning_seller_image?->storage[0]?->value ?? 'public','upload_image_4')}}"


                                        data-onerror-image="{{asset('/public/assets/admin/img/upload-4.png')}}"
                                        class="vertical-img mw-100 vertical onerror-image" alt="">

                                    </div>
                                    <input class="upload-file__input single_file_input" accept="{{IMAGE_EXTENSION}}" type="file" name="earning_seller_image"  hidden>
                                    @if (isset($earning_seller_image['value']))
                                            <span id="earning_seller_img" class="remove_image_button remove-image dynamic-checkbox"
                                            data-id="earning_seller_img" data-image-off="{{ asset('/public/assets/admin/img/delete-confirmation.png') }}"
                                            data-title="{{translate('Warning!')}}"
                                            data-text="<p>{{translate('Are_you_sure_you_want_to_remove_this_image_?')}}</p>"
                                                > <i class="tio-clear"></i></span>
                                            @endif
                                        </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <h5 class="card-title mb-2">
                                    <img src="{{asset('public/assets/admin/img/playstore.png')}}" class="mr-2" alt="">
                                    {{translate('Playstore Button')}}
                                </h5>
                                <div class="__bg-F8F9FC-card">
                                    <div class="form-group mb-md-0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label  for="playstore_url" class="form-label text-capitalize m-0">
                                                {{translate('Download Link')}}
                                                <span class="input-label-secondary text--title" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('When_disabled,_the_Play_Store_download_button_will_be_hidden_from_the_landing_page') }}">
                                                    <i class="tio-info-outined"></i>
                                                </span>
                                            </label>
                                            <label  class="toggle-switch toggle-switch-sm m-0">
                                                <input type="checkbox" name="playstore_url_status"
                                                       data-id="play-store-seller-status"
                                                       data-type="toggle"
                                                       data-image-on='{{asset('/public/assets/admin/img/modal')}}/play-store-on.png'
                                                       data-image-off="{{asset('/public/assets/admin/img/modal')}}/play-store-off.png"
                                                       data-title-on="{{translate('Want_to_enable_the_Play_Store_button_for_Store_App?')}}"
                                                       data-title-off="{{translate('Want_to_disable_the_Play_Store_button_for_Store_App?')}}"
                                                       data-text-on="<p>{{translate('If_enabled,_the_Store_app_download_button_will_be_visible_on_the_Landing_page.')}}</p>"
                                                       data-text-off="<p>{{translate('If_disabled,_this_button_will_be_hidden_from_the_landing_page.')}}</p>"
                                                       id="play-store-seller-status" class="status toggle-switch-input dynamic-checkbox-toggle" value="1" {{(isset($seller_app_links) && $seller_app_links['playstore_url_status'])?'checked':''}}>
                                                <span class="toggle-switch-label text mb-0">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                        <input id="playstore_url" type="url" placeholder="{{translate('Ex: https://play.google.com/store/apps')}}" class="form-control h--45px" name="playstore_url" value="{{ $seller_app_links['playstore_url'] ?? ''}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="card-title mb-2">
                                    <img src="{{asset('public/assets/admin/img/ios.png')}}" class="mr-2" alt="">
                                    {{translate('App Store Button')}}
                                </h5>
                                <div class="__bg-F8F9FC-card">
                                    <div class="form-group mb-md-0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="apple_store_url" class="form-label text-capitalize m-0">
                                                {{translate('Download Link')}}
                                                <span class="input-label-secondary text--title" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('When_disabled,_the_App_Store_download_button_will_be_hidden_from_the_landing_page') }}">
                                                    <i class="tio-info-outined"></i>
                                                </span>
                                            </label>
                                            <label class="toggle-switch toggle-switch-sm m-0">
                                                <input type="checkbox" name="apple_store_url_status"

                                                       data-id="apple-seller-status"
                                                       data-type="toggle"
                                                       data-image-on='{{asset('/public/assets/admin/img/modal')}}/apple-on.png'
                                                       data-image-off="{{asset('/public/assets/admin/img/modal')}}/apple-off.png"
                                                       data-title-on="{{translate('Want_to_enable_the_App_Store_button_for_Store_App?')}}"
                                                       data-title-off="{{translate('Want_to_disable_the_App_Store_button_for_Store_App')}}"
                                                       data-text-on="<p>{{translate('If_enabled,_the_Store_app_download_button_will_be_visible_on_the_Landing_page.')}}</p>"
                                                       data-text-off="<p>{{translate('If_disabled,_this_button_will_be_hidden_from_the_landing_page.')}}</p>"
                                                       id="apple-seller-status" class="status toggle-switch-input dynamic-checkbox-toggle"

                                                       value="1" {{(isset($seller_app_links) && $seller_app_links['apple_store_url_status'])?'checked':''}}>
                                                <span class="toggle-switch-label text mb-0">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                        <input type="url" id="apple_store_url" placeholder="{{translate('Ex: https://www.apple.com/app-store/')}}" class="form-control h--45px" name="apple_store_url" value="{{ $seller_app_links['apple_store_url'] ?? ''}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btn--container justify-content-end mt-20">
                            <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                            <button type="submit"   class="btn btn--primary mb-2">{{translate('Save')}}</button>
                        </div>
                    </div>
                </div>
            </form>
            <form  id="earning_seller_img_form" action="{{ route('admin.remove_image') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{  $earning_seller_image?->id}}" >
                {{-- <input type="hidden" name="json" value="1" > --}}
                <input type="hidden" name="model_name" value="DataSetting" >
                <input type="hidden" name="image_path" value="earning" >
                <input type="hidden" name="field_name" value="value" >
            </form>
            <form action="{{ route('admin.business-settings.admin-landing-page-settings', 'earning-dm-link') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @php($dm_app_links = \App\Models\DataSetting::where(['key'=>'dm_app_earning_links','type'=>'admin_landing_page'])->first())
                @php($dm_app_links = isset($dm_app_links->value)?json_decode($dm_app_links->value, true):null)

                <h5 class="card-title mt-3 mb-3">
                    <span class="card-header-icon mr-2"><i class="tio-settings-outlined"></i></span> <span>{{translate('Download_Deliveryman_App_Section')}}</span>
                </h5>
                <div class="card">
                    <div class="card-body">

                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="form-label d-block mb-3">
                                    {{translate('Banner')}}  <span class="text--primary">{{translate('(size: 3:1)')}}</span>
                                    <span class="form-label-secondary text-danger"
                                          data-toggle="tooltip" data-placement="right"
                                          data-original-title="{{ translate('messages.Required.')}}"> *
                                                </span>
                                    <div class="fs-12 opacity-70">
                                        {{ translate(IMAGE_FORMAT.' ' . 'Less Than 2MB') }}
                                    </div>
                                </label>
                                <label class="upload-img-3 m-0 d-block">
                                    <div class="position-relative">
                                    <div class="img">

                                        <img src="{{\App\CentralLogics\Helpers::get_full_url('earning', $earning_delivery_image?->value?? '', $earning_delivery_image?->storage[0]?->value ?? 'public','upload_image_4')}}"

                                        data-onerror-image="{{asset('/public/assets/admin/img/upload-4.png')}}" class="vertical-img mw-100 vertical onerror-image" alt="">
                                    </div>
                                        <input class="upload-file__input single_file_input" accept="{{IMAGE_EXTENSION}}" type="file" name="earning_delivery_image"  hidden>
                                            @if (isset($earning_delivery_image['value']))
                                            <span id="earning_delivery_img" class="remove_image_button  remove-image dynamic-checkbox"
                                                  data-id="earning_delivery_img"
                                                  data-image-off="{{ asset('/public/assets/admin/img/delete-confirmation.png') }}"
                                                  data-title="{{translate('Warning!')}}"
                                                  data-text="<p>{{translate('Are_you_sure_you_want_to_remove_this_image_?')}}</p>"
                                                > <i class="tio-clear"></i></span>
                                            @endif
                                        </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <h5 class="card-title mb-2">
                                    <img src="{{asset('public/assets/admin/img/playstore.png')}}" class="mr-2" alt="">
                                    {{translate('Playstore Button')}}
                                </h5>
                                <div class="__bg-F8F9FC-card">
                                    <div class="form-group mb-md-0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label  for="playstore_url_dm" class="form-label text-capitalize m-0">
                                                {{translate('Download Link')}}
                                                <span class="input-label-secondary text--title" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('When_disabled,_the_Play_Store_download_button_will_be_hidden_from_the_landing_page') }}">
                                                    <i class="tio-info-outined"></i>
                                                </span>
                                            </label>
                                            <label class="toggle-switch toggle-switch-sm m-0">
                                                <input type="checkbox" name="playstore_url_status"
                                                       data-id="play-store-dm-status"
                                                       data-type="toggle"
                                                       data-image-on="{{ asset('/public/assets/admin/img/modal/play-store-on.png') }}"
                                                       data-image-off="{{ asset('/public/assets/admin/img/modal/play-store-off.png') }}"
                                                       data-title-on="{{ translate('Want_to_enable_the_Play_Store_button_for_Deliveryman_App?') }}"
                                                       data-title-off="{{ translate('Want_to_disable_the_Play_Store_button_for_Deliveryman_App?') }}"
                                                       data-text-on="<p>{{ translate('If_enabled,_the_Deliveryman_app_download_button_will_be_visible_on_the_Landing_page.') }}</p>"
                                                       data-text-off="<p>{{ translate('If_disabled,_this_button_will_be_hidden_from_the_landing_page.') }}</p>"
                                                       id="play-store-dm-status"
                                                       class="status toggle-switch-input dynamic-checkbox-toggle"


                                                       value="1" {{(isset($dm_app_links) && $dm_app_links['playstore_url_status'])?'checked':''}}>
                                                <span class="toggle-switch-label text mb-0">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                        <input id="playstore_url_dm" type="url" placeholder="{{translate('Ex: https://play.google.com/store/apps')}}" class="form-control h--45px" name="playstore_url" value="{{ $dm_app_links['playstore_url'] ?? ''}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="card-title mb-2">
                                    <img src="{{asset('public/assets/admin/img/ios.png')}}" class="mr-2" alt="">
                                    {{translate('App Store Button')}}
                                </h5>
                                <div class="__bg-F8F9FC-card">
                                    <div class="form-group mb-md-0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="apple_store_url_dm" class="form-label text-capitalize m-0">
                                                {{translate('Download Link')}}
                                                <span class="input-label-secondary text--title" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('When_disabled,_the_App_Store_download_button_will_be_hidden_from_the_landing_page') }}">
                                                    <i class="tio-info-outined"></i>
                                                </span>
                                            </label>
                                            <label class="toggle-switch toggle-switch-sm m-0">
                                                <input type="checkbox" name="apple_store_url_status"
                                                       data-id="apple-dm-status"
                                                       data-type="toggle"
                                                       data-image-on="{{ asset('/public/assets/admin/img/modal/apple-on.png') }}"
                                                       data-image-off="{{ asset('/public/assets/admin/img/modal/apple-off.png') }}"
                                                       data-title-on="{{ translate('Want_to_enable_the_App_Store_button_for_Deliveryman_App?') }}"
                                                       data-title-off="{{ translate('Want_to_disable_the_App_Store_button_for_Deliveryman_App?') }}"
                                                       data-text-on="<p>{{ translate('If_enabled,_the_Deliveryman_app_download_button_will_be_visible_on_the_Landing_page.') }}</p>"
                                                       data-text-off="<p>{{ translate('If_disabled,_this_button_will_be_hidden_from_the_landing_page.') }}</p>"
                                                       id="apple-dm-status"
                                                       class="status toggle-switch-input dynamic-checkbox-toggle"


                                                       value="1" {{(isset($dm_app_links) && $dm_app_links['apple_store_url_status'])?'checked':''}}>
                                                <span class="toggle-switch-label text mb-0">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                        <input id="apple_store_url_dm" type="url" placeholder="{{translate('Ex: https://www.apple.com/app-store/')}}" class="form-control h--45px" name="apple_store_url" value="{{ $dm_app_links['apple_store_url']?? ''}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btn--container justify-content-end mt-20">
                            <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                            <button type="submit"   class="btn btn--primary mb-2">{{translate('Save')}}</button>
                        </div>
                    </div>
                </div>
            </form>
             <form  id="earning_delivery_img_form" action="{{ route('admin.remove_image') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{  $earning_delivery_image?->id}}" >
                <input type="hidden" name="model_name" value="DataSetting" >
                <input type="hidden" name="image_path" value="earning" >
                <input type="hidden" name="field_name" value="value" >
            </form>

        </div>
    </div>
</div>
    <!-- How it Works -->
    @include('admin-views.business-settings.landing-page-settings.partial.how-it-work')
@endsection

