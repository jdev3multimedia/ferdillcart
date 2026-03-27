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
            @include('admin-views.business-settings.landing-page-settings.top-menu-links.react-landing-page-links')
        </div>
    </div>
    <div class="card py-3 px-xxl-4 px-3 mb-20">
        <div class="d-flex flex-sm-nowrap flex-wrap gap-3 align-items-center justify-content-between">
            <div class="">
                <h3 class="mb-1">{{ translate('Deliveryman App Download Section') }}</h3>
                <p class="mb-0 gray-dark fs-12">
                    {{ translate('See how your Deliveryman App Download Section will look to customers.') }}
                </p>
            </div>
            <div class="max-w-300px ml-sm-auto">
                <button type="button" class="btn btn-outline-primary py-2 fs-12 px-3 offcanvas-trigger" data-target="#deliveryMan-downloadApp_section">
                    <i class="tio-invisible"></i> {{ translate('Section Preview') }}
                </button>
            </div>
        </div>
    </div>

    <div class="card mb-20">
        <form action="#0">
            <div class="card-body">
                <div class="mb-20">
                    <h3 class="mb-1">{{ translate('Deliveryman App Download Section Content ') }}</h3>
                    <p class="mb-0 fs-12">{{ translate('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam odio tellus, laoreet ') }}</p>
                </div>
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="bg--secondary rounded h-100 p-xxl-4 p-3">
                            <ul class="nav nav-tabs mb-4 border-bottom">
                                <li class="nav-item">
                                    <a class="nav-link lang_link active" href="#" id="default-link">{{translate('messages.default')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link lang_link" href="#" id="">{{translate('messages.English')}} ({{ translate('messages.EN') }})</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link lang_link" href="#" id="">{{translate('messages.Arabic')}} ({{ translate('messages.(AR)') }})</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link lang_link" href="#" id="">{{translate('messages.Spanish')}} ({{ translate('messages.(ES)') }})</a>
                                </li>
                            </ul>
                            <div class="row g-3">
                                <div class="col-md-12 lang_form default-form">
                                    <div class="row g-1">
                                        <div class="col-12">
                                            <label for="download_user_app_title" class="form-label">{{translate('Title')}} ({{ translate('messages.default') }})
                                                <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_100_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span><span class="form-label-secondary text-danger"
                                                data-toggle="tooltip" data-placement="right"
                                                data-original-title="{{ translate('messages.Required.')}}"> *
                                                </span>
                                            </label>
                                            <input id="download_user_app_title" type="text"  maxlength="100" name="download_user_app_title[]" value="{{ $download_user_app_title?->getRawOriginal('value')??'' }}" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                            <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="download_user_app_sub_title" class="form-label">{{translate('Sub Title')}} ({{ translate('messages.default') }})
                                            <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_200_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span><span class="form-label-secondary text-danger"
                                            data-toggle="tooltip" data-placement="right"
                                            data-original-title="{{ translate('messages.Required.')}}"> *
                                            </span>
                                            </label>
                                            <textarea type="text" name="available_zone_short_description[]" placeholder="{{translate('messages.subtitle')}}" class="form-control min-h-90px ckeditor"></textarea>
                                        </div>
                                        <div class="col-12">
                                            <label for="download_button_name" class="form-label">{{translate('Button Name')}} ({{ translate('messages.default') }})
                                            <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_button_name_within_20_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span><span class="form-label-secondary text-danger"
                                            data-toggle="tooltip" data-placement="right"
                                            data-original-title="{{ translate('messages.Required.')}}"> *
                                            </span>
                                            </label>
                                            <input id="download_button_name" type="text"  maxlength="20" name="download_button_name[]" value="{{ $download_user_app_sub_title?->getRawOriginal('value')??'' }}" class="form-control" placeholder="{{translate('messages.Selling Start...')}}">
                                            <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/20</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="bg--secondary h-100 rounded p-md-4 p-3 d-center">
                            <div class="text-center">
                                <div class="mb-4">
                                    <h5 class="mb-1">{{ translate('Upload Zone Section Image') }}</h5>
                                    <p class="mb-0 fs-12 gray-dark">{{ translate('Upload your Zone Section  Image') }}</p>
                                </div>
                                <div class="mx-auto text-center">
                                    <div class="upload-file_custom ratio-1 h-100px">
                                        <input type="file" name="image" class="upload-file__input single_file_input"
                                                accept=".webp, .jpg, .jpeg, .png, .gif" required>
                                        <label class="upload-file__wrapper w-100 h-100 m-0">
                                            <div class="upload-file-textbox text-center" style="">
                                                <img width="22" class="svg" src="{{asset('public/assets/admin/img/document-upload.svg')}}" alt="img">
                                                <h6 class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                    <span class="theme-clr">Click to upload</span>
                                                    <br>
                                                    Or drag and drop
                                                </h6>
                                            </div>
                                            <img class="upload-file-img" loading="lazy" src="" data-default-src="" alt="" style="display: none;">
                                        </label>
                                        <div class="overlay">
                                            <div class="d-flex gap-1 justify-content-center align-items-center h-100">
                                                <button type="button" class="btn btn-outline-info icon-btn view_btn">
                                                    <i class="tio-invisible"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-info icon-btn edit_btn">
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
                                    {{ translate('JPG, JPEG, PNG, Gif Image size : Max 2 MB')}} <span class="font-medium text-title">{{ translate('(1:1)')}}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn--container justify-content-end mt-20">
                    <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                    <button type="submit"   class="btn btn--primary mb-2">{{translate('Save')}}</button>
                </div>
            </div>
        </form>
    </div>

    @php($language=\App\Models\BusinessSetting::where('key','language')->first())
    @php($language = $language->value ?? null)
    @php($defaultLang = str_replace('_', '-', app()->getLocale()))
    <!-- @if($language)
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
    @endif -->
    <div class="tab-content">
        <div class="tab-pane fade show active">
            @php($download_user_app_title=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','react_landing_page')->where('key','download_user_app_title')->first())
            @php($download_user_app_sub_title=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','react_landing_page')->where('key','download_user_app_sub_title')->first())
            @php($download_user_app_links = \App\Models\DataSetting::where(['key'=>'download_user_app_links','type'=>'react_landing_page'])->first())
            @php($download_user_app_links = isset($download_user_app_links->value)?json_decode($download_user_app_links->value, true):null)
            <form action="{{ route('admin.business-settings.react-landing-page-settings', 'download-app-section') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="mb-20">
                            <h3 class="mb-1">{{ translate('Deliveryman App Download Section Button ') }}</h3>
                            <p class="mb-0 fs-12">{{ translate('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam odio tellus, laoreet ') }}</p>
                        </div>
                        <div class="bg--secondary rounded p-xxl-4 p-3 mb-20">
                            <ul class="nav nav-tabs mb-4 border-bottom">
                                <li class="nav-item">
                                    <a class="nav-link lang_link active" href="#" id="default-link">{{translate('messages.default')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link lang_link" href="#" id="">{{translate('messages.English')}} ({{ translate('messages.EN') }})</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link lang_link" href="#" id="">{{translate('messages.Arabic')}} ({{ translate('messages.(AR)') }})</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link lang_link" href="#" id="">{{translate('messages.Spanish')}} ({{ translate('messages.(ES)') }})</a>
                                </li>
                            </ul>
                            <div class="row g-4">
                                <div class="col-12">
                                    @if ($language)
                                    <div class="lang_form default-form">
                                        <div class="row g-1">
                                            <div class="col-12">
                                                <label for="download_user_app_title" class="form-label">{{translate('Title')}} ({{ translate('messages.default') }})
                                                <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_100_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span><span class="form-label-secondary text-danger"
                                                data-toggle="tooltip" data-placement="right"
                                                data-original-title="{{ translate('messages.Required.')}}"> *
                                                </span>
    
                                            </label>
                                        <input id="download_user_app_title" type="text"  maxlength="100" name="download_user_app_title[]" value="{{ $download_user_app_title?->getRawOriginal('value')??'' }}" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                        <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                                            </div>
                                            <div class="col-12">
                                                <label for="download_user_app_sub_title" class="form-label">{{translate('Sub Title')}} ({{ translate('messages.default') }})
                                                <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_200_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span><span class="form-label-secondary text-danger"
                                                data-toggle="tooltip" data-placement="right"
                                                data-original-title="{{ translate('messages.Required.')}}"> *
                                                </span>
    
                                            </label>
                                        <input id="download_user_app_sub_title" type="text"  maxlength="200" name="download_user_app_sub_title[]" value="{{ $download_user_app_sub_title?->getRawOriginal('value')??'' }}" class="form-control" placeholder="{{translate('messages.sub_title_here...')}}">
                                        <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">
                                        @foreach(json_decode($language) as $lang)
                                        <?php
                                        if(isset($download_user_app_title->translations)&&count($download_user_app_title->translations)){
                                                $download_user_app_title_translate = [];
                                                foreach($download_user_app_title->translations as $t)
                                                {
                                                    if($t->locale == $lang && $t->key=='download_user_app_title'){
                                                        $download_user_app_title_translate[$lang]['value'] = $t->value;
                                                    }
                                                }
    
                                            }
                                        if(isset($download_user_app_sub_title->translations)&&count($download_user_app_sub_title->translations)){
                                                $download_user_app_sub_title_translate = [];
                                                foreach($download_user_app_sub_title->translations as $t)
                                                {
                                                    if($t->locale == $lang && $t->key=='download_user_app_sub_title'){
                                                        $download_user_app_sub_title_translate[$lang]['value'] = $t->value;
                                                    }
                                                }
    
                                            }
                                            ?>
                                        <div class="col-md-12 d-none lang_form" id="{{$lang}}-form1">
                                            <div class="row g-1">
                                                <div class="col-12">
                                                    <label for="download_user_app_title{{$lang}}" class="form-label">{{translate('Title')}} ({{strtoupper($lang)}})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_100_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span></label>
                                        <input id="download_user_app_title{{$lang}}" type="text"  maxlength="100" name="download_user_app_title[]" value="{{ $download_user_app_title_translate[$lang]['value']??'' }}" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                        <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                                                </div>
                                                <div class="col-12">
                                                    <label for="download_user_app_sub_title{{$lang}}" class="form-label">{{translate('Sub Title')}} ({{strtoupper($lang)}})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_200_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span></label>
                                        <input type="text" id="download_user_app_sub_title{{$lang}}" maxlength="200" name="download_user_app_sub_title[]" value="{{ $download_user_app_sub_title_translate[$lang]['value']??'' }}" class="form-control" placeholder="{{translate('messages.sub_title_here...')}}">
                                        <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                                </div>
                                            </div>
                                        </div>
                                            <input type="hidden" name="lang[]" value="{{$lang}}">
                                        @endforeach
                                    @else
                                    <div class="col-md-12">
                                        <div class="row g-1">
                                            <div class="col-12">
                                                <label for="download_user_app_title" class="form-label">{{translate('Title')}}</label>
                                                <input type="text" id="download_user_app_title" name="download_user_app_title[]" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                            </div>
                                            <div class="col-12">
                                                <label for="download_user_app_sub_title" class="form-label">{{translate('Sub Title')}}</label>
                                                <input id="download_user_app_sub_title" type="text" name="download_user_app_sub_title[]" class="form-control" placeholder="{{translate('messages.sub_title_here...')}}">
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
                                        <div class="d-flex mb-20 align-items-center gap-2 flex-wrap justify-content-between">
                                            <h4 class="mb-0">
                                                <img src="{{asset('public/assets/admin/img/playstore.png')}}" class="mr-2" alt="">
                                                {{translate('Playstore Button')}}
                                            </h4>
                                            <label class="toggle-switch toggle-switch-sm m-0">
                                                <input type="checkbox" name="playstore_url_status"
                                                        id="play-store-dm-status"
                                                        data-id="play-store-dm-status"
                                                        data-type="toggle"
                                                        data-image-on="{{ asset('/public/assets/admin/img/modal/play-store-on.png') }}"
                                                        data-image-off="{{ asset('/public/assets/admin/img/modal/play-store-off.png') }}"
                                                        data-title-on="{{ translate('want_to_enable_the_play_store_button_for_user_app') }}"
                                                        data-title-off="{{ translate('want_to_disable_the_play_store_button_for_user_app') }}"
                                                        data-text-on="<p>{{ translate('if_enabled,_the_user_app_download_button_will_be_visible_on_react_landing_page') }}</p>"
                                                        data-text-off="<p>{{ translate('if_disabled,_this_button_will_be_hidden_from_the_react_landing_page') }}</p>"
                                                        class="status toggle-switch-input dynamic-checkbox-toggle"
        
                                                        value="1" {{(isset($download_user_app_links) && $download_user_app_links['playstore_url_status'])?'checked':''}}>
                                                <span class="toggle-switch-label text mb-0">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="__bg-F8F9FC-card">
                                            <div class="form-group mb-md-0">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label for="playstore_url" class="form-label text-capitalize m-0">
                                                        {{translate('Download Link')}}
                                                        <span class="input-label-secondary text--title" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('When_disabled,_the_Play_Store_download_button_will_be_hidden_from_the_React_landing_page.') }}">
                                                            <i class="tio-info color-A7A7A7"></i>
                                                        </span>
                                                    </label>
                                                </div>
                                                <input type="url" {{(isset($download_user_app_links) && $download_user_app_links['playstore_url_status'])?'required':''}} id="playstore_url" placeholder="{{translate('Ex: https://play.google.com/store/apps')}}" class="form-control h--45px" name="playstore_url" value="{{ $download_user_app_links['playstore_url']??''}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-white rounded p-xxl-4 p-2">
                                        <div class="d-flex mb-20 align-items-center gap-2 flex-wrap justify-content-between">                                        
                                            <h4 class="m-0">
                                                <img src="{{asset('public/assets/admin/img/ios.png')}}" class="mr-2" alt="">
                                                {{translate('App Store Button')}}
                                            </h4>
                                            <label class="toggle-switch toggle-switch-sm m-0">
                                                <input type="checkbox" name="apple_store_url_status"
                                                        data-id="apple-dm-status"
                                                        data-type="toggle"
                                                        data-image-on="{{ asset('/public/assets/admin/img/modal/apple-on.png') }}"
                                                        data-image-off="{{ asset('/public/assets/admin/img/modal/apple-off.png') }}"
                                                        data-title-on="{{ translate('want_to_enable_the_app_store_button_for_user_app') }}"
                                                        data-title-off="{{ translate('want_to_disable_the_app_store_button_for_user_app') }}"
                                                        data-text-on="<p>{{ translate('if_enabled,_the_user_app_download_button_will_be_visible_on_react_landing_page') }}</p>"
                                                        data-text-off="<p>{{ translate('if_disabled,_this_button_will_be_hidden_from_the_react_landing_page') }}</p>"
                                                        class="status toggle-switch-input dynamic-checkbox-toggle"
                                                        id="apple-dm-status"  value="1" {{(isset($download_user_app_links) && $download_user_app_links['apple_store_url_status'])?'checked':''}}>
                                                <span class="toggle-switch-label text mb-0">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="__bg-F8F9FC-card">
                                            <div class="form-group mb-md-0">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label for="apple_store_url" class="form-label text-capitalize m-0">
                                                        {{translate('Download Link')}}
                                                        <span class="input-label-secondary text--title" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('When_disabled,_the_User_app_download_button_will_be_hidden_on_React_Landing_page.') }}">
                                                            <i class="tio-info color-A7A7A7"></i>
                                                        </span>
                                                    </label>
                                                </div>
                                                <input type="url" id="apple_store_url" {{(isset($download_user_app_links) && $download_user_app_links['apple_store_url_status'])?'required':''}} placeholder="{{translate('Ex: https://www.apple.com/app-store/')}}" class="form-control h--45px" name="apple_store_url" value="{{ $download_user_app_links['apple_store_url']??''}}">
                                            </div>
                                        </div>
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

        </div>
    </div>
</div>



<!-- Section View Offcanvas here -->
<div id="deliveryMan-downloadApp_section" class="custom-offcanvas offcanvas-750 offcanvas-xxl-1120 d-flex flex-column justify-content-between">
    <form action="{{ route('taxvat.store') }}" method="post">
        <div>
            <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                <div class="py-1">
                    <h3 class="mb-0 line--limit-1">{{ translate('messages.Deliveryman App Download Section Preview') }}</h3>
                </div>
                <button type="button" class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"aria-label="Close">
                    &times;
                </button>
            </div>
            <div class="custom-offcanvas-body custom-offcanvas-body-100  p-20">
               <section class="common-section-view p-xl-5 p-0 bg-white border rounded-10 my-xl-2 mx-xl-2">
                    <div class="common-section-inner bg-fafafa p-xxl-4 p-3 d-flex flex-xl-nowrap justify-content-lg-between justify-content-center flex-wrap align-items-center gap-xxl-20">                        
                        <div class="d-flex align-items-center justify-content-xl-start justify-content-center flex-xl-nowrap flex-wrap gap-xxl-20 max-w-650 mb-xxl-0 mb-3">
                            <div class="selling-thumb min-w-176px max-w-250">
                                <img width="180" src="{{ asset('/public/assets/admin/img/400x400/deliveryman-dirver.png') }}" alt="Google Play" class="object-contain w-100 h-100">
                            </div>
                           <div class="mt-xl-0 mt-2 max-w-353px">
                                <h2 class="mb-xxl-2 mb-1 fs-24">
                                    Deliver More. <span class="text-base-clr">Earn</span> More.
                                </h2>
                                <p class="text-title fs-12 mb-xxl-4 mb-xl-3 mb-3">
                                    Join 6amMart as a delivery partner and turn every ride into income. Work on your own schedule and get paid faster, every day
                                </p>
                                <ul class="list-checked d-flex flex-column gap-1 mb-xxl-4 mb-xl-3 mb-3">
                                    <li class="d-flex fs-14 align-items-center gap-2 text-title fw-medium">
                                        <img width="16" src="{{asset('public/assets/admin/img/icons/6check.png')}}" alt="check" class="svg">
                                        Choose your ride – car, bike, or bicycle
                                    </li>
                                    <li class="d-flex fs-14 align-items-center gap-2 text-title fw-medium">
                                        <img width="16" src="{{asset('public/assets/admin/img/icons/6check.png')}}" alt="check" class="svg">
                                        Flexible hours that fit your lifestyle
                                    </li>
                                    <li class="d-flex fs-14 align-items-center gap-2 text-title fw-medium">
                                        <img width="16" src="{{asset('public/assets/admin/img/icons/6check.png')}}" alt="check" class="svg">
                                        Instant payouts & bonus opportunities
                                    </li>
                                </ul>
                                <a href="#0" class="btn btn-primary-white base-bg-cmn fs-12 text-white fw-medium">
                                    Start Earning <i class="tio-arrow-forward pl-1 text-white"></i>
                                </a>
                           </div>
                        </div>
                        <div>
                            <div class="bg-white py-3 px-sm-5 px-1 rounded-10">
                                <div class="mb-20 text-center">
                                    <h3 class="mb-1">{{ translate('messages.Download the Driver App') }}</h3>
                                    <p class="mb-0 fs-12">{{ translate('messages.Take control of your rides anywhere.') }}</p>
                                </div>
                                <div class="scan-wrap max-w-150 mx-auto bg-fafafa mb-3 p-xxl-3 p-2 text-center">
                                    <div class="scan bg-white d-center border w-80px h-80px mx-auto rounded p-1">
                                        <img src="{{ asset('/public/assets/admin/img/400x400/app-scan.png') }}" alt="Google Play" class="object-cover w-100 h-100">
                                    </div>
                                    <p class="mb-0 fs-12 mt-1 text-center">{{ translate('messages.Scan to DownLoad') }}</p>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-sm-start justify-content-center flex-sm-nowrap flex-wrap align-items-center gap-x-xl-10 app-manage">
                                        <!-- Google Play Button -->
                                        <a href="#" class="btn btn-primary d-flex align-items-center mr-2 px-3 py-2 bg-000 rounded mb-sm-0 mb-1">                                    
                                            <img width="24" height="24" src="{{ asset('/public/assets/admin/img/icons/playstore.png') }}" alt="Google Play" class="mr-1">
                                            <div class="text-left">
                                                <small class="d-block text-white mb-0 fs-12">GET IT ON</small>
                                                <strong class="d-block text-white fs-14">Google Play</strong>
                                            </div>
                                        </a>
                                        <a href="#" class="btn btn-primary d-flex align-items-center px-3 py-2 bg-000 rounded">
                                            <img width="24" height="24" src="{{ asset('/public/assets/admin/img/icons/apple-icon.png') }}" alt="App Store" class="mr-1">
                                            <div class="text-left">
                                                <small class="d-block text-white mb-0 fs-12">Download ON</small>
                                                <strong class="d-block text-white fs-14">App Store</strong>
                                            </div>
                                        </a>
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

@push('script_2')
<script src="{{asset('public/assets/admin/ckeditor/ckeditor.js')}}"></script>
<script>
    "use strict";
    $(document).ready(function () {
        $('.ckeditor').ckeditor();
    });
</script>
@endpush

