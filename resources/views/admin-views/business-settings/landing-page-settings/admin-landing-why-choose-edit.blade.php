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
    <div class="tab-content">
        <div class="tab-pane fade show active">
            <form action="{{ route('admin.business-settings.criteria-update',[$criteria['id']]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h5 class="card-title mb-3 mt-3">
                    <span class="card-header-icon mr-2"><i class="tio-settings-outlined"></i></span> <span>{{translate('Special Criteria List Section ')}}</span>
                </h5>
                <div class="card mb-3">
                    <div class="card-body">

                            <div class="row g-3">
                                @if ($language)
                                <div class="col-sm-6 lang_form default-form">
                                    <label for="title" class="form-label">{{translate('Title')}} ({{ translate('messages.default') }})<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_40_characters') }}">
                                                        <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span><span class="form-label-secondary text-danger"
                                                                 data-toggle="tooltip" data-placement="right"
                                                                 data-original-title="{{ translate('messages.Required.')}}"> *
                                                </span></label>
                                                <input required id="title" type="text" maxlength="40" name="title[]" value="{{ $criteria['title']??'' }}" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                                    @foreach(json_decode($language) as $lang)
                                    <?php
                                    if(count($criteria['translations'])){
                                        $translate = [];
                                        foreach($criteria['translations'] as $t)
                                        {
                                            if($t->locale == $lang && $t->key=="title"){
                                                $translate[$lang]['title'] = $t->value;
                                            }
                                        }
                                    }
                                ?>
                                    <div class="col-sm-6 d-none lang_form" id="{{$lang}}-form1">
                                        <label for="title{{$lang}}" class="form-label">{{translate('Title')}} ({{strtoupper($lang)}})</label>
                                        <input id="title{{$lang}}" type="text" name="title[]" value="{{ $translate[$lang]['title']??'' }}" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                    </div>
                                        <input type="hidden" name="lang[]" value="{{$lang}}">
                                    @endforeach
                                @else
                                <div class="col-sm-6">
                                    <label for="title" class="form-label">{{translate('Title')}}<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_40_characters') }}">
                                                        <img src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                            alt="">
                                                    </span></label>
                                                <input id="title" type="text" maxlength="40" name="title[]" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                </div>
                                    <input type="hidden" name="lang[]" value="default">
                                @endif
                                <div class="col-sm-6">
                                    <div>

                                        <label class="form-label mb-3">{{translate('Criteria Icon/ Image')}}<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate(IMAGE_FORMAT.' ' . 'Less Than 2MB') }}">
                                            <img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="">
                                        </span>
                                            <span class="form-label-secondary text-danger"
                                                  data-toggle="tooltip" data-placement="right"
                                                  data-original-title="{{ translate('messages.Required.')}}"> *
                                                </span>
                                            <div class="fs-12 opacity-70">
                                                {{ translate(IMAGE_FORMAT.' ' . 'Less Than 2MB') }}
                                            </div>
                                        </label>
                                    </div>
                                    <label class="upload-img-3 m-0">
                                        <div class="position-relative">
                                        <div class="img">
                                            <img
                                            src="{{ $criteria['image_full_url'] ?? asset('/public/assets/admin/img/aspect-1.png') }}"
                                        data-onerror-image="{{asset('/public/assets/admin/img/aspect-1.png')}}" alt="" class="img__aspect-1 min-w-187px max-w-187px onerror-image">
                                        </div>
                                          <input class="upload-file__input single_file_input" type="file"  name="image" hidden>
                                            @if (isset($criteria['image']))
                                                <span id="fixed_header_image" class="remove_image_button remove-image dynamic-checkbox"
                                                      data-id="fixed_header_image"
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
                                <button type="submit"   class="btn btn--primary mb-2">{{translate('messages.Update')}}</button>
                            </div>
                        </div>
                        </div>
                    </form>
                </div>

        </div>
    </div>
<form  id="fixed_header_image_form" action="{{ route('admin.remove_image') }}" method="post">
    @csrf
    <input type="hidden" name="id" value="{{  $criteria?->id}}" >
    <input type="hidden" name="model_name" value="AdminSpecialCriteria" >
    <input type="hidden" name="image_path" value="special_criteria" >
    <input type="hidden" name="field_name" value="image" >
</form>
    <!-- How it Works -->
    @include('admin-views.business-settings.landing-page-settings.partial.how-it-work')
@endsection

