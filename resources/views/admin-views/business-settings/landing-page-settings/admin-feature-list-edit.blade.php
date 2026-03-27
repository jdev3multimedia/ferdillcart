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
                <strong class="mr-2">{{translate('How the Setting Works')}}</strong>
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
    @php($feature_title=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','admin_landing_page')->where('key','feature_title')->first())
    @php($feature_title=$feature_title?$feature_title:'')
    @php($feature_short_description=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','admin_landing_page')->where('key','feature_short_description')->first())
    @php($feature_short_description=$feature_short_description?$feature_short_description:'')
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
            <form action="{{ route('admin.business-settings.feature-update',[$feature['id']]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row g-4">
                            @if ($language)
                            <div class="col-md-6 lang_form default-form">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="title" class="form-label">{{translate('Title')}} ({{ translate('messages.default') }})<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_20_characters') }}">
                                                        <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span>
                                            <span class="form-label-secondary text-danger"
                                                  data-toggle="tooltip" data-placement="right"
                                                  data-original-title="{{ translate('messages.Required.')}}"> *
                                                </span></label>
                                                <input required id="title" type="text" maxlength="20" name="title[]" value="{{ $feature['title'] }}" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                    </div>
                                    <div class="col-12">
                                        <label for="sub_title" class="form-label">{{translate('Sub Title')}} ({{ translate('messages.default') }})<span
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
                                                <input required id="sub_title" type="text" maxlength="80" name="sub_title[]" value="{{ $feature['sub_title'] }}" class="form-control" placeholder="{{translate('messages.sub_title_here...')}}">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="lang[]" value="default">
                                @foreach(json_decode($language) as $lang)
                                <?php
                                if(count($feature['translations'])){
                                    $translate = [];
                                    foreach($feature['translations'] as $t)
                                    {
                                        if($t->locale == $lang && $t->key=="title"){
                                            $translate[$lang]['title'] = $t->value;
                                        }
                                        if($t->locale == $lang && $t->key=="sub_title"){
                                            $translate[$lang]['sub_title'] = $t->value;
                                        }
                                    }
                                }
                            ?>
                                <div class="col-md-6 d-none lang_form" id="{{$lang}}-form1">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="title" class="form-label">{{translate('Title')}} ({{strtoupper($lang)}})<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_20_characters') }}">
                                                        <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <input id="title" type="text" maxlength="20" name="title[]" value="{{ $translate[$lang]['title']??'' }}" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                        </div>
                                        <div class="col-12">
                                            <label for="sub_title" class="form-label">{{translate('Sub Title')}} ({{strtoupper($lang)}})<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_80_characters') }}">
                                                        <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <input id="sub_title" type="text" maxlength="80" name="sub_title[]" value="{{ $translate[$lang]['sub_title']??'' }}" class="form-control" placeholder="{{translate('messages.sub_title_here...')}}">
                                        </div>
                                    </div>
                                </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                @endforeach
                            @else
                            <div class="col-md-6">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="title" class="form-label">{{translate('Title')}}</label>
                                        <input id="title" type="text" name="title[]" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                    </div>
                                    <div class="col-12">
                                        <label for="sub_title"   class="form-label">{{translate('Sub Title')}}</label>
                                        <input  id="sub_title" type="text" name="sub_title[]" class="form-control" placeholder="{{translate('messages.sub_title_here...')}}">
                                    </div>
                                </div>
                            </div>
                                <input type="hidden" name="lang[]" value="default">
                            @endif

                            <div class="col-md-6">
                                <label class="form-label d-block mb-3">
                                    {{ translate('messages.Image') }}  <span class="text--primary">{{translate('(size:_1:1)')}}</span>
                                    <span class="form-label-secondary text-danger"
                                          data-toggle="tooltip" data-placement="right"
                                          data-original-title="{{ translate('messages.Required.')}}"> *
                                                </span>
                                    <div class="fs-12 opacity-70">
                                        {{ translate(IMAGE_FORMAT.' ' . 'Less Than 2MB') }}
                                    </div>
                                </label>
                                <label class="upload-img-3 m-0">
                                        <div class="position-relative">
                                        <div class="img">
                                            <img class="onerror-image" src="{{ $feature->image_full_url ?? '',
                                                asset('/public/assets/admin/img/upload-3.png') }}"

                                            data-onerror-image="{{asset('/public/assets/admin/img/upload-3.png')}}" alt="">
                                        </div>
                                            <input class="upload-file__input single_file_input" accept="{{IMAGE_EXTENSION}}" type="file" name="image"  hidden>
                                            @if (isset($feature->image))
                                            <span id="feature_image" class="remove_image_button remove-image dynamic-checkbox"
                                                  data-id="feature_image"
                                                  data-image-off="{{ asset('/public/assets/admin/img/delete-confirmation.png') }}"
                                                  data-title="{{translate('Warning!')}}"
                                                  data-text="<p>{{translate('Are_you_sure_you_want_to_remove_this_image_?')}}</p>"
                                                > <i class="tio-clear"></i></span>
                                            @endif
                                        </div>
                                    </label>
                            </div>
                        </div>
                        <div class="btn--container justify-content-end mt-20">
                            <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                            <button type="submit" class="btn btn--primary mb-2">{{translate('messages.Update')}}</button>
                        </div>
                    </div>
                </div>
            </form>
            <form  id="feature_image_form" action="{{ route('admin.remove_image') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{  $feature?->id}}" >
                <input type="hidden" name="model_name" value="AdminFeature" >
                <input type="hidden" name="image_path" value="admin_feature" >
                <input type="hidden" name="field_name" value="image" >
            </form>


        </div>
    </div>
</div>
    <!-- How it Works -->
@include('admin-views.business-settings.landing-page-settings.partial.how-it-work')
@endsection

