@extends('layouts.admin.app')

@section('title',translate('Add new campaign'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/campaign.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.Add new campaign')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form enctype="multipart/form-data" class="custom-validation" data-ajax="true" id="campaign-form">
                    @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                    @php($language = $language->value ?? null)
                    @php($defaultLang = str_replace('_', '-', app()->getLocale()))
                    @if($language)
                        <ul class="nav nav-tabs mb-4">
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
                        <div class="lang_form" id="default-form">
                            <div class="form-group error-wrapper">
                                <label class="input-label" for="default_title">{{translate('messages.title')}} ({{ translate('messages.default') }})</label>
                                <input type="text" name="title[]" id="default_title" class="form-control" placeholder="{{translate('messages.new_campaign')}}"  required>
                            </div>
                            <input type="hidden" name="lang[]" value="default">
                            <div class="form-group error-wrapper">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short_description')}} ({{ translate('messages.default') }})</label>
                                <textarea type="text" name="description[]" class="form-control ckeditor" required></textarea>
                            </div>
                        </div>
                        @foreach(json_decode($language) as $lang)
                            <div class="d-none lang_form" id="{{$lang}}-form">
                                <div class="form-group error-wrapper">
                                    <label class="input-label" for="{{$lang}}_title">{{translate('messages.title')}} ({{strtoupper($lang)}})</label>
                                    <input type="text" name="title[]" id="{{$lang}}_title" class="form-control" placeholder="{{translate('messages.new_campaign')}}"  >
                                </div>
                                <input type="hidden" name="lang[]" value="{{$lang}}">
                                <div class="form-group error-wrapper">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short_description')}} ({{strtoupper($lang)}})</label>
                                    <textarea type="text" name="description[]" class="form-control ckeditor"></textarea>
                                </div>
                            </div>
                        @endforeach
                    @else
                    <div id="default-form">
                        <div class="form-group error-wrapper">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('messages.title')}} ({{ translate('messages.default') }})</label>
                            <input type="text" name="title[]" class="form-control" placeholder="{{translate('messages.new_food')}}">
                        </div>
                        <input type="hidden" name="lang[]" value="en">
                        <div class="form-group error-wrapper">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short_description')}}</label>
                            <textarea type="text" name="description[]" class="form-control ckeditor"></textarea>
                        </div>
                    </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="row g-3">
                                {{-- <div class="col-sm-12">
                                    <div>
                                        <label class="input-label">{{translate('messages.module')}}</label>
                                        <select name="module_id" id="module_id" required class="form-control js-select2-custom"  data-placeholder="{{translate('messages.select_module')}}" id="module_select">
                                            <option value="" selected disabled>{{translate('messages.select_module')}}</option>
                                            @foreach(\App\Models\Module::notParcel()->get() as $module)
                                                <option value="{{$module->id}}">{{$module->module_name}}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger">{{translate('messages.module_change_warning')}}</small>
                                    </div>
                                </div> --}}
                                <div class="col-sm-6">
                                    <div class="error-wrapper">
                                        <label class="input-label" for="title">{{translate('messages.start_date')}}</label>
                                        <input type="date" id="date_from" class="form-control" required="" name="start_date">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="error-wrapper">
                                        <label class="input-label" for="title">{{translate('messages.end_date')}}</label>
                                        <input type="date" id="date_to" class="form-control" required="" name="end_date">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="error-wrapper">
                                        <label class="input-label text-capitalize" for="title">{{translate('messages.daily_start_time')}}</label>
                                        <input type="time" id="start_time" class="form-control" name="start_time">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="error-wrapper">
                                        <label class="input-label text-capitalize" for="title">{{translate('messages.daily_end_time')}}</label>
                                        <input type="time" id="end_time" class="form-control" name="end_time">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="error-wrapper">
                                <div class="form-group mb-0 h-100 d-flex flex-column">
                                    <label>
                                        {{translate('messages.campaign_image')}}
                                        <small class="text-danger">* ( {{translate('messages.ratio')}} 900x300 )</small>
                                    </label>
                                    <div class="text-center py-3 my-auto">
                                        <img class="initial--4" id="viewer"
                                             src="{{asset('public/assets/admin/img/900x400/img1.jpg')}}" alt="campaign image"/>
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                               accept=".webp, .jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                        <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose_file')}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end mt-20">
                        <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin')}}/js/view-pages/basic-campaign-index.js"></script>
    <script>
    "use strict";
        $('#campaign-form').on('submit', function (e) {
            e.preventDefault();

            let $form = $(this);
            if (!$form.valid()) {
                return false;
            }

            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.campaign.store-basic')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('Campaign created successfully!',{
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('admin.campaign.list', 'basic')}}';
                        }, 2000);
                    }
                }
            });
        });

        $('#reset_btn').click(function(){
            $('#module_id').val(null).trigger('change');
            $('#viewer').attr('src','{{asset('public/assets/admin/img/900x400/img1.jpg')}}');
        })
    </script>
@endpush
