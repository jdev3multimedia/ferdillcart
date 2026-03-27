@extends('layouts.admin.app')

@section('title',translate('messages.banner'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/3rd-party.png')}}" class="w--26" alt="">
            </span>
            <span>
                {{translate('messages.Other_Promotional_Content_Setup')}}
            </span>
        </h1>
    </div>
    <div class="mb-20 mt-2">
        <div class="js-nav-scroller hs-nav-scroller-horizontal">
            @include('admin-views.other-banners.partial.parcel-links')
        </div>
    </div>
    @php($language=\App\Models\BusinessSetting::where('key','language')->first())
    @php($language = $language->value ?? null)
    @php($defaultLang = str_replace('_', '-', app()->getLocale()))
    <div class="tab-content">
        <div class="tab-pane fade show active">
            <div class="card mb-3">
                <div class="card-body">
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
                        <form action="{{ route('admin.promotional-banner.why-choose-update',[$banner['id']]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                @if ($language)
                                <div class="col-6">
                                    <div class="row lang_form default-form">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">{{translate('Title')}} ({{ translate('messages.default') }})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_80_characters') }}">
                                                            <img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="">
                                                        </span></label>
                                                <input type="text"  maxlength="80" name="title[]" value="{{ $banner?->getRawOriginal('title')??'' }}" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">{{translate('messages.Short_Description')}} ({{ translate('messages.default') }})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_short_description_within_100_characters') }}">
                                                            <img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="">
                                                        </span></label>
                                                <textarea type="text"  maxlength="100" name="short_description[]" class="form-control" rows="3" {{translate('messages.short_description_here...')}}> {{ $banner?->getRawOriginal('short_description')??'' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">
                                    @foreach(json_decode($language) as $lang)
                                    <?php
                                    if(count($banner['translations'])){
                                        $translate = [];
                                        foreach($banner['translations'] as $t)
                                        {
                                            if($t->locale == $lang && $t->key=="title"){
                                                $translate[$lang]['title'] = $t->value;
                                            }
                                            if($t->locale == $lang && $t->key=="short_description"){
                                                $translate[$lang]['short_description'] = $t->value;
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="row d-none lang_form" id="{{$lang}}-form1">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">{{translate('Title')}} ({{strtoupper($lang)}})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_80_characters') }}">
                                                    <img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="">
                                                </span></label>
                                                <input type="text"  maxlength="80" name="title[]" value="{{ $translate[$lang]['title']??'' }}"class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label">{{translate('messages.Short_Description')}} ({{strtoupper($lang)}})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_short_description_within_100_characters') }}">
                                                    <img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="">
                                                </span></label>
                                                <textarea type="text"  maxlength="100" name="short_description[]" class="form-control" rows="3" {{translate('messages.short_description_here...')}}> {{ $translate[$lang]['short_description']??'' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                    @endforeach

                                </div>

                                @else
                                <div class="col-sm-6">
                                    <label class="form-label">{{translate('Title')}}<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_80_characters') }}">
                                                <img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="">
                                            </span></label>
                                    <input type="text"  maxlength="80" name="title[]" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                </div>
                                    <input type="hidden" name="lang[]" value="default">
                                @endif
                                <div class="col-sm-6">
                                    <div class="ml-5">
                                        <div>

                                            <label class="form-label">{{translate('image (1:1)')}}</label>
                                        </div>
                                        <label class="upload-img-3 m-0">
                                            <div class="img">
                                                <img
                                                src="{{ $banner['image_full_url'] ?? asset('/public/assets/admin/img/aspect-1.png') }}" data-onerror-image="{{asset('/public/assets/admin/img/aspect-1.png')}}" alt="" class="img__aspect-1 min-w-187px max-w-187px onerror-image">
                                            </div>
                                              <input type="file"  name="image" hidden>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="btn--container justify-content-end mt-20">
                                <button type="submit" class="btn btn--primary mb-2">{{translate('Update')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection
@push('script_2')
    <script src="{{asset('public/assets/admin')}}/js/view-pages/other-banners.js"></script>
@endpush
