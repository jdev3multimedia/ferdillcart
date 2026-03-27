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
                <h3 class="mb-1">{{ translate('Footer Section') }}</h3>
                <p class="mb-0 gray-dark fs-12">
                    {{ translate('See how your Footer Section will look to customers.') }}
                </p>
            </div>
            <div class="max-w-300px ml-sm-auto">
                <button type="button" class="btn btn-outline-primary py-2 fs-12 px-3 offcanvas-trigger" data-target="#footerPreview_section">
                    <i class="tio-invisible"></i> {{ translate('Section Preview') }}
                </button>
            </div>
        </div>
    </div>
    <!-- @php($fixed_newsletter_title=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','react_landing_page')->where('key','fixed_newsletter_title')->first())
    @php($fixed_newsletter_sub_title=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','react_landing_page')->where('key','fixed_newsletter_sub_title')->first())
    @php($fixed_footer_description=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','react_landing_page')->where('key','fixed_footer_description')->first())
    @php($fixed_promotional_banner=\App\Models\DataSetting::withoutGlobalScope('translate')->where('type','react_landing_page')->where('key','fixed_promotional_banner')->first())
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
    @endif -->
    <div class="tab-content">
        <div class="tab-pane fade show active">
            <!-- <form action="{{ route('admin.business-settings.react-landing-page-settings', 'fixed-banner') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h5 class="card-title mt-3 mb-3">
                    <span class="card-header-icon mr-2"><i class="tio-settings-outlined"></i></span> <span>{{translate('promotional_Banner')}}</span>
                </h5>
                <div class="card mb-3">
                    <div class="card-body">

                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="form-label d-block mb-2">
                                    {{translate('Banner')}}  <span class="text--primary">{{translate('(size: 2:1)')}}</span>
                                </label>
                                <label class="upload-img-3 m-2 d-block">
                                    <div class="position-relative">
                                    <div class="img">
                                        <img
                                        src="{{\App\CentralLogics\Helpers::get_full_url('promotional_banner', $fixed_promotional_banner?->value?? '', $fixed_promotional_banner?->storage[0]?->value ?? 'public','upload_image_4')}}"

                                        data-onerror-image="{{asset('/public/assets/admin/img/upload-4.png')}}" class="vertical-img mw-100 vertical onerror-image" alt="">
                                    </div>
                                        <input type="file" name="fixed_promotional_banner"  hidden>
                                           @if (isset($fixed_promotional_banner['value']))

                                            <span id="promotional_banner" class="remove_image_button remove-image"
                                                  data-id="promotional_banner"
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
                            <button type="submit"   class="btn btn--primary mb-2">{{translate('Save')}}</button>
                        </div>
                    </div>
                </div>
            </form>
            <form  id="promotional_banner_form" action="{{ route('admin.remove_image') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{  $fixed_promotional_banner?->id}}" >
                <input type="hidden" name="model_name" value="DataSetting" >
                <input type="hidden" name="image_path" value="promotional_banner" >
                <input type="hidden" name="field_name" value="value" >
            </form> -->
            <form action="{{ route('admin.business-settings.react-landing-page-settings', 'fixed-newsletter') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="mb-20">
                            <h4 class="mb-1">{{ translate('Newsletter ') }}</h4>
                            <p class="mb-0 fs-12">{{ translate('Manage the title and subtitle for the email newsletter sign-up section.') }}</p>
                        </div>
                        <div class="bg--secondary rounded p-xxl-4 p-3">
                            @if($language)
                                <ul class="nav nav-tabs mb-4 border-bottom">
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
                            @if ($language)
                            <div class="row g-1 lang_form default-form">
                                <div class="col-sm-12">
                                    <label for="fixed_newsletter_title"  class="form-label">{{translate('Title')}} ({{ translate('messages.default') }})
                                    <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_30_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span>
                                            <span class="form-label-secondary text-danger"
                                            data-toggle="tooltip" data-placement="right"
                                            data-original-title="{{ translate('messages.Required.')}}">
                                            </span>
                                        <span class="form-label-secondary text-danger"
                                              data-toggle="tooltip" data-placement="right"
                                              data-original-title="{{ translate('messages.Required.')}}"> *
                                                    </span>

                                        </label>
                                    <input id="fixed_newsletter_title" type="text"  maxlength="30" name="fixed_newsletter_title[]" class="form-control" value="{{$fixed_newsletter_title?->getRawOriginal('value')??''}}" placeholder="{{translate('messages.title_here...')}}" required>
                                    <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/30</span>
                                </div>
                                <div class="col-sm-12">
                                    <label for="fixed_newsletter_sub_title"  class="form-label">{{translate('Sub Title')}} ({{ translate('messages.default') }})
                                    <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_100_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span><span class="form-label-secondary text-danger"
                                            data-toggle="tooltip" data-placement="right"
                                            data-original-title="{{ translate('messages.Required.')}}">
                                            </span>
                                        <span class="form-label-secondary text-danger"
                                              data-toggle="tooltip" data-placement="right"
                                              data-original-title="{{ translate('messages.Required.')}}"> *
                                                    </span>
                                    </label>
                                    <input id="fixed_newsletter_sub_title" type="text"  maxlength="100" name="fixed_newsletter_sub_title[]" class="form-control" value="{{$fixed_newsletter_sub_title?->getRawOriginal('value')??''}}" placeholder="{{translate('messages.sub_title_here...')}}" required>
                                    <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                                </div>
                            </div>
                            <input type="hidden" name="lang[]" value="default">
                                @foreach(json_decode($language) as $lang)
                                <?php
                                if(isset($fixed_newsletter_title->translations)&&count($fixed_newsletter_title->translations)){
                                        $fixed_newsletter_title_translate = [];
                                        foreach($fixed_newsletter_title->translations as $t)
                                        {
                                            if($t->locale == $lang && $t->key=='fixed_newsletter_title'){
                                                $fixed_newsletter_title_translate[$lang]['value'] = $t->value;
                                            }
                                        }

                                    }
                                if(isset($fixed_newsletter_sub_title->translations)&&count($fixed_newsletter_sub_title->translations)){
                                        $fixed_newsletter_sub_title_translate = [];
                                        foreach($fixed_newsletter_sub_title->translations as $t)
                                        {
                                            if($t->locale == $lang && $t->key=='fixed_newsletter_sub_title'){
                                                $fixed_newsletter_sub_title_translate[$lang]['value'] = $t->value;
                                            }
                                        }

                                    }
                                    ?>
                                    <div class="row g-1 d-none lang_form" id="{{$lang}}-form">
                                        <div class="col-sm-12">
                                            <label for="fixed_newsletter_title{{$lang}}" class="form-label">{{translate('Title')}} ({{strtoupper($lang)}})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_30_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span></label>
                                    <input id="fixed_newsletter_title{{$lang}}" type="text"  maxlength="30" name="fixed_newsletter_title[]" class="form-control" value="{{ $fixed_newsletter_title_translate[$lang]['value']?? '' }}" placeholder="{{translate('messages.title_here...')}}">
                                    <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/30</span>
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="fixed_newsletter_sub_title{{$lang}}" class="form-label">{{translate('Sub Title')}} ({{strtoupper($lang)}})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_100_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span></label>
                                    <input id="fixed_newsletter_sub_title{{$lang}}" type="text"  maxlength="100" name="fixed_newsletter_sub_title[]" class="form-control" value="{{ $fixed_newsletter_sub_title_translate[$lang]['value']?? '' }}" placeholder="{{translate('messages.sub_title_here...')}}">
                                    <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                @endforeach
                            @else
                                <div class="row g-3">
                                    <div class="col-sm-12">
                                        <label for="fixed_newsletter_title" class="form-label">{{translate('Title')}}</label>
                                        <input id="fixed_newsletter_title" type="text" maxlength="30" name="fixed_newsletter_title[]" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                        <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/30</span>
                                    </div>
                                    <div class="col-sm-12">
                                        <label for="fixed_newsletter_sub_title" class="form-label">{{translate('Sub Title')}}</label>
                                        <input id="fixed_newsletter_sub_title" type="text" maxlength="100" name="fixed_newsletter_sub_title[]" class="form-control" placeholder="{{translate('messages.sub_title_here...')}}">
                                        <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                                    </div>
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="mb-20">
                            <h4 class="mb-1">{{ translate('Footer Article ') }}</h4>
                            <p class="mb-0 fs-12">{{ translate('Set the main description or tagline for your company in the footer.') }}</p>
                        </div>
                        <div class="bg--secondary rounded p-xxl-4 p-3">
                            @if($language)
                                <ul class="nav nav-tabs mb-4 border-bottom">
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
                            <div class="row g-3">
                                <div class="col-12">
                                    @if ($language)
                                <div class="row g-3 lang_form default-form">
                                    <div class="col-12">
                                        <label for="fixed_footer_description" class="form-label">{{translate('short_Description')}} ({{ translate('messages.default') }})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_120_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span></label>
                                        <input id="fixed_footer_description" type="text"  maxlength="120" name="fixed_footer_description[]" class="form-control" value="{{$fixed_footer_description?->getRawOriginal('value')??''}}" placeholder="{{translate('messages.title_here...')}}">
                                        <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/120</span>
                                    </div>
                                </div>
                                    @foreach(json_decode($language) as $lang)
                                    <?php
                                    if(isset($fixed_footer_description->translations)&&count($fixed_footer_description->translations)){
                                            $fixed_footer_description_translate = [];
                                            foreach($fixed_footer_description->translations as $t)
                                            {
                                                if($t->locale == $lang && $t->key=='fixed_footer_description'){
                                                    $fixed_footer_description_translate[$lang]['value'] = $t->value;
                                                }
                                            }

                                        }
                                        ?>
                                        <div class="row g-3 d-none lang_form" id="{{$lang}}-form1">
                                            <div class="col-12">
                                                <label for="fixed_footer_description{{$lang}}" class="form-label">{{translate('short_Description')}} ({{strtoupper($lang)}})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_120_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span></label>
                                        <input id="fixed_footer_description{{$lang}}" type="text"  maxlength="120" name="fixed_footer_description[]" class="form-control" value="{{ $fixed_footer_description_translate[$lang]['value']?? '' }}" placeholder="{{translate('messages.title_here...')}}">
                                        <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/120</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="fixed_footer_description" class="form-label">{{translate('short_Description')}}</label>
                                            <input id="fixed_footer_description" type="text" name="fixed_footer_description[]" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                        </div>
                                    </div>
                                @endif
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
<div id="footerPreview_section" class="custom-offcanvas offcanvas-750 d-flex flex-column justify-content-between">
    <form action="{{ route('taxvat.store') }}" method="post">
        <div>
            <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                <div class="py-1">
                    <h3 class="mb-0 line--limit-1">{{ translate('messages.Footer Section Preview') }}</h3>
                </div>
                <button type="button" class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"aria-label="Close">
                    &times;
                </button>
            </div>
            <div class="custom-offcanvas-body custom-offcanvas-body-100  p-20">
               <section class="common-section-view bg-white border rounded-10">
                    <div class="container p-0">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="banner-thumb w-100 h-100 rounded-10">
                                    <img src="{{ asset('/public/assets/admin/img/400x400/footer-preview.png') }}" alt="" class="w-100">
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
