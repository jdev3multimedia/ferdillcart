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
    @php($counter = \App\Models\DataSetting::where(['key'=>'counter_section','type'=>'admin_landing_page'])->first())
    @php($counter = isset($counter->value)?json_decode($counter->value, true):null)
    <div class="tab-content">
        <div class="tab-pane fade show active">
            <form action="{{ route('admin.business-settings.admin-landing-page-settings', 'download-counter-section') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h5 class="card-title mb-3 mt-3">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <span>
                            <span class="card-header-icon mr-2">
                                <i class="tio-settings-outlined"></i>
                            </span>
                            <span>{{translate('Counter Section')}}</span>
                        </span>
                        <label class="toggle-switch toggle-switch-sm m-0">
                            <input type="checkbox" name="status" id="counter_status"
                                   data-id="counter_status"
                                   data-type="toggle"
                                   data-image-on="{{ asset('/public/assets/admin/img/modal/counter-on.png') }}"
                                   data-image-off="{{ asset('/public/assets/admin/img/modal/counter-off.png') }}"
                                   data-title-on="{{ translate('By Turning ON Counter Section') }}"
                                   data-title-off="{{ translate('By Turning OFF Counter Section') }}"
                                   data-text-on="<p>{{ translate('Counter Section is enabled. You can now access its features and functionality') }}</p>"
                                   data-text-off="<p>{{ translate('Counter section will be disabled. You can enable it in the settings to access its features and functionality') }}</p>"
                                   class="status toggle-switch-input dynamic-checkbox-toggle"

                                   value="1" {{(isset($counter) && $counter['status'])?'checked':''}}>
                            <span class="toggle-switch-label text mb-0">
                                <span class="toggle-switch-indicator"></span>
                            </span>
                        </label>
                    </div>
                </h5>
                <div class="card">
                    <div class="card-body">

                        <div class="row g-3">
                            <div class="col-sm-6 col-lg-3">
                                <label for="app_download_count_numbers" class="form-label">{{translate('Total App Download')}}</label>
                                <input id="app_download_count_numbers" type="number" min="0" max="9999999999" name="app_download_count_numbers" value="{{ $counter['app_download_count_numbers'] ?? '' }}" placeholder="{{translate('Ex: 500')}}" class="form-control">
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <label for="seller_count_numbers" class="form-label">{{translate('Total Seller')}}</label>
                                <input id="seller_count_numbers" type="number" min="0" max="9999999999" name="seller_count_numbers" value="{{ $counter['seller_count_numbers'] ?? '' }}" placeholder="{{translate('Ex: 500')}}" class="form-control">
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <label for="deliveryman_count_numbers" class="form-label">{{translate('Total Delivery Man')}}</label>
                                <input id="deliveryman_count_numbers" type="number" min="0" max="9999999999" name="deliveryman_count_numbers" value="{{ $counter['deliveryman_count_numbers'] ?? '' }}" placeholder="{{translate('Ex: 500')}}" class="form-control">
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <label for="customer_count_numbers" class="form-label">{{translate('Total Customer')}}</label>
                                <input id="customer_count_numbers" type="number" min="0" max="9999999999" name="customer_count_numbers" value="{{ $counter['customer_count_numbers'] ?? '' }}" placeholder="{{translate('Ex: 500')}}" class="form-control">
                            </div>
                        </div>
                        <div class="btn--container justify-content-end mt-20">
                            <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                            <button type="submit"   class="btn btn--primary mb-2">{{translate('Save')}}</button>
                        </div>
                    </div>
                </div>
            </form>
            @php($download_user_app_title=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','admin_landing_page')->where('key','download_user_app_title')->first())
            @php($download_user_app_sub_title=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','admin_landing_page')->where('key','download_user_app_sub_title')->first())
            @php($download_user_app_image=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','admin_landing_page')->where('key','download_user_app_image')->first())
            @php($download_user_app_links = \App\Models\DataSetting::where(['key'=>'download_user_app_links','type'=>'admin_landing_page'])->first())
            @php($download_user_app_links = isset($download_user_app_links->value)?json_decode($download_user_app_links->value, true):null)
            <form action="{{ route('admin.business-settings.admin-landing-page-settings', 'download-app-section') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h5 class="card-title mb-3 mt-3">
                    <span class="card-header-icon mr-2"><i class="tio-settings-outlined"></i></span> <span>{{translate('Download User App Section Content')}}</span>
                </h5>
                <div class="card">
                    <div class="card-body">

                        <div class="row g-4">
                            <div class="col-md-6">
                                @if ($language)
                                <div class="col-md-12 lang_form default-form">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="download_user_app_title" class="form-label">{{translate('Title')}} ({{ translate('messages.default') }})<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_20_characters') }}">
                                                        <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <input id="download_user_app_title" type="text" maxlength="20" name="download_user_app_title[]" value="{{ $download_user_app_title?->getRawOriginal('value') }}" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                        </div>
                                        <div class="col-12">
                                            <label for="download_user_app_sub_title" class="form-label">{{translate('Sub Title')}} ({{ translate('messages.default') }})<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_50_characters') }}">
                                                        <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <input id="download_user_app_sub_title" type="text" maxlength="50" name="download_user_app_sub_title[]" value="{{ $download_user_app_sub_title?->getRawOriginal('value') }}" class="form-control" placeholder="{{translate('messages.sub_title_here...')}}">
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
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label for="download_user_app_title{{$lang}}" class="form-label">{{translate('Title')}} ({{strtoupper($lang)}})<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_20_characters') }}">
                                                        <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <input id="download_user_app_title{{$lang}}" type="text" maxlength="20" name="download_user_app_title[]" value="{{ $download_user_app_title_translate[$lang]['value']??'' }}" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                            </div>
                                            <div class="col-12">
                                                <label for="download_user_app_sub_title{{$lang}}" class="form-label">{{translate('Sub Title')}} ({{strtoupper($lang)}})<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_50_characters') }}">
                                                        <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <input id="download_user_app_sub_title{{$lang}}" type="text" maxlength="50" name="download_user_app_sub_title[]" value="{{ $download_user_app_sub_title_translate[$lang]['value']??'' }}" class="form-control" placeholder="{{translate('messages.sub_title_here...')}}">
                                            </div>
                                        </div>
                                    </div>
                                        <input type="hidden" name="lang[]" value="{{$lang}}">
                                    @endforeach
                                @else
                                <div class="col-md-12">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="download_user_app_title" class="form-label">{{translate('Title')}}<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_20_characters') }}">
                                                        <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <input id="download_user_app_title" type="text" maxlength="20" name="download_user_app_title[]" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                        </div>
                                        <div class="col-12">
                                            <label for="download_user_app_sub_title" class="form-label">{{translate('Sub Title')}}<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_50_characters') }}">
                                                        <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <input id="download_user_app_sub_title" type="text" maxlength="50" name="download_user_app_sub_title[]" class="form-control" placeholder="{{translate('messages.sub_title_here...')}}">
                                        </div>
                                    </div>
                                </div>
                                    <input type="hidden" name="lang[]" value="default">
                                @endif
                            </div>
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <label class="form-label d-block mb-3">
                                        {{ translate('messages.Banner') }}  <span class="text--primary">{{ translate('(size: 1:1)') }}</span>
                                        <div class="fs-12 opacity-70">
                                            {{ translate(IMAGE_FORMAT.' ' . 'Less Than 2MB') }}
                                        </div>
                                    </label>
                                    <label class="upload-img-3 m-0">
                                        <div class="position-relative">
                                            <div class="img">
                                                <img
                                                    src="{{\App\CentralLogics\Helpers::get_full_url('download_user_app_image', $download_user_app_image?->value?? '', $download_user_app_image?->storage[0]?->value ?? 'public','aspect_1')}}"

                                                    data-onerror-image="{{asset('/public/assets/admin/img/aspect-1.png')}}" alt="" class="img__aspect-1 min-w-187px max-w-187px onerror-image">
                                            </div>
                                            <input accept="{{IMAGE_EXTENSION}}" class="upload-file__input single_file_input" type="file"  name="image" hidden>
                                            @if (isset($download_user_app_image['value']))
                                                <span id="download_user_app_image" class="remove_image_button remove-image dynamic-checkbox"
                                                      data-id="download_user_app_image"
                                                      data-image-off="{{ asset('/public/assets/admin/img/delete-confirmation.png') }}"
                                                      data-title="{{translate('Warning!')}}"
                                                      data-text="<p>{{translate('Are_you_sure_you_want_to_remove_this_image_?')}}</p>"
                                                > <i class="tio-clear"></i></span>
                                            @endif
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <h5 class="card-title mb-2">
                                    <img src="{{asset('public/assets/admin/img/playstore.png')}}" class="mr-2" alt="">
                                    {{translate('Playstore Button')}}
                                </h5>
                                <div class="__bg-F8F9FC-card">
                                    <div class="form-group mb-md-0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="playstore_url" class="form-label text-capitalize m-0">
                                                {{translate('Download Link')}}</label>

                                            <label class="toggle-switch toggle-switch-sm m-0">
                                                <input type="checkbox" name="playstore_url_status"
                                                       id="play-store-dm-status"
                                                       data-id="play-store-dm-status"
                                                       data-type="toggle"
                                                       data-image-on="{{ asset('/public/assets/admin/img/modal/play-store-on.png') }}"
                                                       data-image-off="{{ asset('/public/assets/admin/img/modal/play-store-off.png') }}"
                                                       data-title-on="{{ translate('Playstore Button Enabled for Delivery Man') }}"
                                                       data-title-off="{{ translate('Playstore Button Disabled for Delivery Man') }}"
                                                       data-text-on="<p>{{ translate('Playstore button is enabled now everyone can use or see the button') }}</p>"
                                                       data-text-off="<p>{{ translate('Playstore button is disabled now no one can use or see the button') }}</p>"
                                                       class="status toggle-switch-input dynamic-checkbox-toggle"

                                                       value="1" {{(isset($download_user_app_links) && $download_user_app_links['playstore_url_status'])?'checked':''}}>
                                                <span class="toggle-switch-label text mb-0">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                        <input id="playstore_url" type="text" placeholder="{{translate('Ex: https://play.google.com/store/apps')}}" class="form-control h--45px" name="playstore_url" value="{{$download_user_app_links['playstore_url']??''}}">
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
                                                {{translate('Download Link')}} </label>

                                            <label class="toggle-switch toggle-switch-sm m-0">
                                                <input type="checkbox" name="apple_store_url_status"
                                                       id="apple-dm-status"
                                                       data-id="apple-dm-status"
                                                       data-type="toggle"
                                                       data-image-on="{{ asset('/public/assets/admin/img/modal/apple-on.png') }}"
                                                       data-image-off="{{ asset('/public/assets/admin/img/modal/apple-off.png') }}"
                                                       data-title-on="{{ translate('App Store Button Enabled for Delivery Man') }}"
                                                       data-title-off="{{ translate('App Store Button Disabled for Delivery Man') }}"
                                                       data-text-on="<p>{{ translate('App Store button is enabled now everyone can use or see the button') }}</p>"
                                                       data-text-off="<p>{{ translate('App Store button is disabled now no one can use or see the button') }}</p>"
                                                       class="status toggle-switch-input dynamic-checkbox-toggle"
                                                       value="1" {{(isset($download_user_app_links) && $download_user_app_links['apple_store_url_status'])?'checked':''}}>
                                                <span class="toggle-switch-label text mb-0">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                        <input id="apple_store_url" type="text" placeholder="{{translate('Ex: https://www.apple.com/app-store/')}}" class="form-control h--45px" name="apple_store_url" value="{{$download_user_app_links['apple_store_url']?? ''}}">
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
             <form  id="download_user_app_image_form" action="{{ route('admin.remove_image') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{  $download_user_app_image?->id}}" >
                <input type="hidden" name="model_name" value="DataSetting" >
                <input type="hidden" name="image_path" value="download_user_app_image" >
                <input type="hidden" name="field_name" value="value" >
            </form>


        </div>
    </div>
</div>
    <!-- How it Works -->
    @include('admin-views.business-settings.landing-page-settings.partial.how-it-work')
@endsection
