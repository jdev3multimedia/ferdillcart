@extends('layouts.admin.app')

@section('title',translate('Update_Business_Module'))

@push('css_or_js')
<link rel="stylesheet" href="{{asset('public/assets/admin/css/radio-image.css')}}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/module.png')}}" alt="">
                </span>
                <span>
                    {{translate('Edit_Business_Module')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body pb-0">
                <form action="{{route('admin.business-settings.module.update',[$module['id']])}}" method="post" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    @if($language)
                        <ul class="nav nav-tabs mb-4 border-0">
                            <li class="nav-item">
                                <a class="nav-link lang_link active"
                                href="#"
                                id="default-link">{{translate('messages.default')}}</a>
                            </li>
                            @foreach ($language as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link"
                                        href="#"
                                        id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="lang_form" id="default-form">
                            <div class="form-group" >
                                <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.Business_Module_name')}} ({{ translate('messages.default') }})</label>
                                <input type="text" name="module_name[]" class="form-control" maxlength="191" value="{{$module?->getRawOriginal('module_name')}}">
                            </div>
                            <div class="form-group">
                                <label class="input-label d-flex" for="module_type">{{translate('messages.description')}} ({{ translate('messages.default') }})<span class="form-label-secondary text-danger d-flex"
                                    data-toggle="tooltip" data-placement="right"
                                    data-original-title="{{ translate('messages.Write_a_short_description_of_your_new_business_module_within_100_words_(550_characters)')}}"><img
                                        src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                        alt="{{ translate('messages.veg_non_veg') }}"></span></label>
                                <textarea  data-value="{!! $module->description ?? '' !!}" id="description"  class="ckeditor form-control" name="description[]">{!! $module?->getRawOriginal('description') ?? '' !!}</textarea>
                            </div>
                        </div>

                        <input type="hidden" name="lang[]" value="default">
                        @foreach($language as $lang)
                            <?php
                                if(count($module['translations'])){
                                    $translate = [];
                                    foreach($module['translations'] as $t)
                                    {
                                        if($t->locale == $lang && $t->key=="module_name"){
                                            $translate[$lang]['module_name'] = $t->value;
                                        }

                                        if($t->locale == $lang && $t->key=="description"){
                                            $translate[$lang]['description'] = $t->value;
                                        }
                                    }
                                }
                            ?>
                            <div class="d-none lang_form" id="{{$lang}}-form">
                                <div class="form-group" >
                                    <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.Business_Module_name')}} ({{strtoupper($lang)}})</label>
                                    <input type="text" name="module_name[]" class="form-control" maxlength="191" value="{{$translate[$lang]['module_name']??''}}">
                                </div>
                                <div class="form-group">
                                    <label class="input-label d-flex" for="module_type">{{translate('messages.description')}} ({{strtoupper($lang)}})<span class="form-label-secondary text-danger d-flex"
                                        data-toggle="tooltip" data-placement="right"
                                        data-original-title="{{ translate('messages.Write_a_short_description_of_your_new_business_module_within_100_words_(550_characters)')}}"><img
                                            src="{{ asset('/public/assets/admin/img/info-circle.svg') }}"
                                            alt="{{ translate('messages.veg_non_veg') }}"></span></label>
                                    <textarea  data-value="{!! $translate[$lang]['description']??'' !!}" id="description{{ $lang }}" class="ckeditor form-control" name="description[]">{!! $translate[$lang]['description']??'' !!}</textarea>
                                </div>
                            </div>

                            <input type="hidden" name="lang[]" value="{{$lang}}">
                        @endforeach
                    @else
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.Business_Module_name')}}</label>
                            <input type="text" name="module_name" class="form-control" placeholder="{{translate('messages.new_category')}}" value="{{old('name')}}" maxlength="191">
                        </div>
                        <div class="form-group">
                            <label class="input-label" for="module_type">{{translate('messages.description')}}</label>
                            <textarea  data-value="{!! $module->description !!}" id="description" class="ckeditor form-control" name="description">{!! $module->description !!}</textarea>
                        </div>
                        <input type="hidden" name="lang[]" value="default">
                    @endif
                </div>
            </div>
                <br>
                <h5 class="mb-3">{{translate('module_setup')}}</h5>

                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <h6 class="mb-3">{{translate('business_module_type')}} <span class="badge badge-danger">{{ translate('not_editable') }}</span></h6>
                                    <div class="card">
                                        <div class="card-body p-0">
                                            <div class="module-radio-group">
                                            @foreach (config('module.module_type') as $key)
                                            @if($key != 'rental'  )
                                            <label class="form-check form--check">
                                                <input class="form-check-input" disabled type="radio" name="module_type" value="{{$key}}" {{$key==$module->module_type?'checked':''}}>
                                                <span class="form-check-label">
                                                    {{translate($key)}}
                                                </span>
                                            </label>
                                            @elseif($key == 'rental' && addon_published_status('Rental')  )
                                            <label class="form-check form--check">
                                                <input class="form-check-input" disabled type="radio" name="module_type" value="{{$key}}" {{$key==$module->module_type?'checked':''}}>
                                                <span class="form-check-label">
                                                    {{translate($key)}}
                                                </span>
                                            </label>
                                            @endif
                                            @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mt-1" id="module_des_card">
                                        <div class="card-body" id="module_description">{{config('module.'.$module->module_type)['description']}}</div>
                                    </div>
                                </div>
                            </div>
                        <div class="col-lg-6">
                            <h6 class="mb-3">{{translate('Chose related images')}}</h6>
                            <div class="card module-logo-card mb-3">
                                <div class="card-body">
                                    <div class="row h-100">
                                        <div class="col-sm-6">
                                            <div class="form-group m-0 h-100 d-flex flex-column justify-content-center align-items-center">
                                                <label>
                                                    {{translate('messages.icon')}}
                                                    <small class="text-danger">* ( {{translate('messages.ratio')}} 1:1)</small>
                                                </label>
                                                <label class="text-center my-auto position-relative">
                                                    <img class="img--176 h-unset aspect-ratio-1 image--border" id="viewer" data-onerror-image="{{asset('public/assets/admin/img/upload-img.png')}}" src="{{ $module['icon_full_url'] }}"
                                                    alt="image" />
                                                    <div class="icon-file-group">
                                                        <div class="icon-file">
                                                            <input type="file" name="icon" id="customFileEg1" class="custom-file-input" accept=".webp, .jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                            <i class="tio-edit"></i>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group m-0 h-100 d-flex flex-column justify-content-center align-items-center">
                                                <label>
                                                    {{translate('messages.thumbnail')}}
                                                    <small class="text-danger">* ( {{translate('messages.ratio')}} 1:1)</small>
                                                </label>
                                                <label class="text-center my-auto position-relative">
                                                    <img class="img--176 h-unset aspect-ratio-1 image--border" id="viewer2" data-onerror-image="{{asset('public/assets/admin/img/upload-img.png')}}" src="{{ $module['thumbnail_full_url'] }}"
                                                    alt="image" />
                                                    <div class="icon-file-group">
                                                        <div class="icon-file">
                                                            <input type="file" name="thumbnail" id="customFileEg2" class="custom-file-input" accept=".webp, .jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                            <i class="tio-edit"></i>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn--container justify-content-end mt-20">
                <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                <button type="submit" class="btn btn--primary">{{translate('messages.Save_changes')}}</button>
            </div>
        </form>
            <!-- End Table -->
    </div>
    </div>

@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin/ckeditor/ckeditor.js')}}"></script>
    <script>
        "use strict";


        function readURL(input, id) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();

                reader.onload = function (e) {
                    $('#'+id).attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this,'viewer');
        });

        $("#customFileEg2").change(function () {
            readURL(this,'viewer2');
        });



        $(document).ready(function () {
            @if ($module->module_type=='parcel')
                $('#module_des_card').hide();
            @endif
            $('.ckeditor').ckeditor();
        });

        $('#reset_btn').click(function(){
            $('.ckeditor').each(function() {
                CKEDITOR.instances[$(this).attr('id')].setData($(this).data('value'));
            });

            $('#viewer').attr('src','{{ $module['icon_full_url'] }}');
            $('#viewer2').attr('src','{{ $module['thumbnail_full_url'] }}');
        })
</script>
@endpush
