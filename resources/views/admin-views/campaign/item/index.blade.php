@extends('layouts.admin.app')

@section('title',translate('messages.Add new campaign'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{asset('public/assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
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
                    {{translate('messages.add_new_campaign')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <form id="campaign_form"
                enctype="multipart/form-data" class="custom-validation" data-ajax="true">
            <div class="row g-2">
                @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                @php($language = $language->value ?? null)
                @php($defaultLang = str_replace('_', '-', app()->getLocale()))
                @if($language)
                <div class="col-12">
                    <ul class="nav nav-tabs mb-3 border-0">
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
                </div>
                @endif
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon">
                                    <i class="tio-fastfood"></i>
                                </span>
                                <span>{{ translate('Item Info') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($language)
                            <div class="lang_form"
                            id="default-form">
                                <div class="form-group error-wrapper">
                                    <label class="input-label"
                                        for="default_title">{{ translate('messages.title') }}
                                        (Default)
                                    </label>
                                    <input type="text" name="title[]" id="default_title" required
                                        class="form-control" placeholder="{{ translate('messages.new_item') }}"
                                    >
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                                <div class="form-group mb-0 error-wrapper">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{ translate('messages.short_description') }} ({{ translate('messages.default') }})</label>
                                    <textarea type="text" name="description[]" class="form-control min-h-90px ckeditor" required></textarea>
                                </div>
                            </div>
                                @foreach (json_decode($language) as $lang)
                                    <div class="d-none lang_form"
                                        id="{{ $lang }}-form">
                                        <div class="form-group error-wrapper">
                                            <label class="input-label"
                                                for="{{ $lang }}_title">{{ translate('messages.title') }}
                                                ({{ strtoupper($lang) }})
                                            </label>
                                            <input type="text" name="title[]" id="{{ $lang }}_title"
                                                class="form-control" placeholder="{{ translate('messages.new_item') }}">
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{ $lang }}">
                                        <div class="form-group mb-0 error-wrapper">
                                            <label class="input-label"
                                                for="exampleFormControlInput1">{{ translate('messages.short_description') }} ({{ strtoupper($lang) }})</label>
                                            <textarea type="text" name="description[]" class="form-control min-h-90px ckeditor"></textarea>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div id="default-form">
                                    <div class="form-group error-wrapper">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.title') }} ({{ translate('messages.default') }})</label>
                                        <input type="text" name="title[]" class="form-control"
                                            placeholder="{{ translate('messages.new_item') }}" >
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">
                                    <div class="form-group mb-0 error-wrapper">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.short_description') }}</label>
                                        <textarea type="text" name="description[]" class="form-control min-h-90px ckeditor"></textarea>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon">
                                    <i class="tio-comment-image-outlined"></i>
                                </span>
                                <span>{{ translate('Item Image') }}</span>
                            </h5>
                        </div>
                        <div class="error-wrapper">
                            <div class="card-body d-flex flex-column">
                                <label>
                                    {{translate('messages.item_image')}}
                                    <small class="text-danger">* ( {{translate('messages.ratio')}} 1:1 )</small>
                                </label>
    
                                <div id="image-viewer-section" class="text-center py-3 my-auto">
                                    <img class="img--120" id="viewer"
                                            src="{{asset('public/assets/admin/img/100x100/2.png')}}" alt="banner image"/>
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
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon">
                                    <i class="tio-dashboard-outlined"></i>
                                </span>
                                <span>{{ translate('Item Details') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0 error-wrapper">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.store')}}<span
                                                class="input-label-secondary">*</span></label>
                                        <select name="store_id" class="js-data-example-ajax form-control" id="store_id"  data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Select Store') }}" required>
                                        <option selected disabled>{{ translate('Select Store') }}</option>

                                        </select>
                                    </div>

                                </div>
                                <div class="col-md-3 col-sm-6" id="stock_input">
                                    <div class="form-group mb-0 error-wrapper">
                                        <label class="input-label" for="total_stock">{{translate('messages.total_stock')}}</label>
                                        <input type="number" class="form-control" name="current_stock" id="quantity">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-3" id="maximum_cart_quantity">
                                    <div class="form-group mb-0 error-wrapper">
                                        <label class="input-label"
                                            for="maximum_cart_quantity">{{ translate('messages.maximum_cart_quantity') }}</label>
                                        <input type="number" class="form-control" name="maximum_cart_quantity" min="0" id="cart_quantity">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6" id="addon_input">
                                    <div class="form-group mb-0 error-wrapper">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.addon')}}<span
                                                class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('messages.store_required_warning')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.store_required_warning')}}"></span></label>
                                        <select name="addon_ids[]" id="add_on" class="form-control js-select2-custom" multiple="multiple">

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0 error-wrapper">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.category')}}<span
                                                class="input-label-secondary">*</span></label>
                                        <select name="category_id" class="js-data-example-ajax form-control" id="category_id" required>
                                            <option value="">---{{translate('messages.select')}}---</option>
                                            @php($categories=\App\Models\Category::where(['position' => 0])->get())
                                            @foreach($categories as $category)
                                                <option value="{{$category['id']}}">{{$category['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0 error-wrapper">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.sub_category')}}
                                            <span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('messages.category_required_warning')}}">
                                                <img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{translate('messages.category_required_warning')}}">
                                            </span>
                                        </label>
                                        <select name="sub_category_id" id="sub-categories" class="js-data-example-ajax form-control">

                                        </select>
                                    </div>
                                </div>
                                @if(Config::get('module.current_module_type') == 'pharmacy')
                                    <div class="col-sm-6" id="generic_name">
                                        <label class="input-label" for="sub-categories">
                                            {{translate('generic_name')}}
                                            <span class="input-label-secondary" title="{{ translate('Specify the medicine`s active ingredient that makes it work') }}" data-toggle="tooltip">
                                            <i class="tio-info-outined"></i>
                                        </span>
                                        </label>
                                        <div class="error-wrapper">
                                            <div class="dropdown suggestion_dropdown">
                                                <input type="text" class="form-control" name="generic_name" placeholder="{{ translate('messages.Type your content here') }}" autocomplete="off">
                                                @if(count(\App\Models\GenericName::select(['generic_name'])->get())>0)
                                                    <div class="dropdown-menu">
                                                        @foreach (\App\Models\GenericName::select(['generic_name'])->get() as $generic_name)
                                                            <div class="dropdown-item">{{ $generic_name->generic_name }}</div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="row g-2">
                                @if(Config::get('module.current_module_type') == 'grocery' || Config::get('module.current_module_type') == 'food')

                                <div class="col-sm-6 error-wrapper" id="nutrition">
                                    <label class="input-label" for="sub-categories">
                                        {{translate('Nutrition')}}
                                        <span class="input-label-secondary" title="{{ translate('Specify the necessary keywords relating to energy values for the item.') }}" data-toggle="tooltip">
                                                <i class="tio-info-outined"></i>
                                            </span>
                                    </label>
                                    <select name="nutritions[]" class="form-control multiple-select2" data-placeholder="{{ translate('messages.Type your content and press enter') }}" multiple>
                                        @foreach (\App\Models\Nutrition::select(['nutrition'])->get() as $nutrition)
                                            <option value="{{ $nutrition->nutrition }}">{{ $nutrition->nutrition }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-sm-6 error-wrapper" id="allergy">
                                    <label class="input-label" for="sub-categories">
                                        {{translate('Allegren Ingredients')}}
                                        <span class="input-label-secondary" title="{{ translate('Specify the ingredients of the item which can make a reaction as an allergen.') }}" data-toggle="tooltip">
                                                <i class="tio-info-outined"></i>
                                            </span>
                                    </label>
                                    <select name="allergies[]" class="form-control multiple-select2" data-placeholder="{{ translate('messages.Type your content and press enter') }}" multiple>
                                        @foreach (\App\Models\Allergy::select(['allergy'])->get() as $allergy)
                                            <option value="{{ $allergy->allergy }}">{{ $allergy->allergy }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon"><i class="tio-dollar-outlined"></i></span>
                                <span>{{ translate('messages.amount') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0 error-wrapper">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.price')}}</label>
                                        <input type="number" min=".01" max="999999999" step="0.01" value="1" name="price" class="form-control"
                                                placeholder="{{ translate('messages.Ex:') }} 100" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0 error-wrapper">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.discount')}}</label>
                                        <input type="number" min="0" max="999999999" value="0" name="discount" class="form-control"
                                                placeholder="{{ translate('messages.Ex:') }} 100" >
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0 error-wrapper">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.discount_type')}}<span class="input-label-secondary text--title" data-toggle="tooltip"
                                            data-placement="right"
                                            data-original-title="{{ translate('Currently you need to manage discount with store.') }}">
                                            <i class="tio-info-outined"></i>
                                        </span></label>
                                        <select name="discount_type" class="form-control js-select2-custom">
                                            <option value="percent">{{translate('messages.percent')}}</option>
                                            <option value="amount">{{translate('messages.amount')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0 initial-hidden error-wrapper" id="veg_input">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.item_type')}}</label>
                                        <select name="veg" class="form-control js-select2-custom">
                                            <option value="0">{{translate('messages.non_veg')}}</option>
                                            <option value="1">{{translate('messages.veg')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0 error-wrapper" id="unit_input">
                                        <label class="input-label text-capitalize" for="unit">{{translate('messages.unit')}}</label>
                                        <select name="unit" class="form-control js-select2-custom">
                                            @foreach (\App\Models\Unit::all() as $unit)
                                                <option value="{{$unit->id}}">{{$unit->unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($productWiseTax)
                <div class="col-lg-12">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-header flex-wrap">
                            <h5 class="card-title">
                                <span class="card-header-icon mr-2">
                                    <i class="tio-canvas-text"></i>
                                </span>
                                <span>{{ translate('messages.Tax_Information') }}</span>
                            </h5>
                        </div>
                        <div class="card-body error-wrapper">
                                <span class="mb-2 d-block title-clr fw-normal">{{ translate('Select Tax Rate') }}</span>
                                <select name="tax_ids[]" required id="tax__rate" class="form-control js-select2-custom"
                                    multiple="multiple" placeholder="Type & Select Tax Rate">
                                    @foreach ($taxVats as $taxVat)
                                        <option value="{{ $taxVat->id }}"> {{ $taxVat->name }}
                                            ({{ $taxVat->tax_rate }}%)
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                    </div>

                    @endif
                <div class="col-md-12" id="attribute_section">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon">
                                    <i class="tio-canvas-text"></i>
                                </span>
                                <span>{{ translate('Add Attribute') }}</span>
                            </h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="form-group mb-0 error-wrapper">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.attribute')}}<span
                                                class="input-label-secondary"></span></label>
                                        <select name="attribute_id[]" id="choice_attributes"
                                                class="form-control js-select2-custom"
                                                multiple="multiple">
                                            @foreach(\App\Models\Attribute::orderBy('name')->get() as $attribute)
                                                <option value="{{$attribute['id']}}">{{$attribute['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="customer_choice_options" id="customer_choice_options">

                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="variant_combination" id="variant_combination">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12" id="food_variation_section">
                    <div class="card" id="food_variation_div">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon">
                                    <i class="tio-canvas-text"></i>
                                </span>
                                <span>{{ translate('messages.food_variations') }}</span>
                            </h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <div id="add_new_option">
                                    </div>
                                    <br>
                                    <div class="mt-2">
                                        <a class="btn btn-outline-success"
                                            id="add_new_option_button">{{ translate('add_new_variation') }}</a>
                                    </div> <br><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon"><i class="tio-date-range"></i></span>
                                <span>{{ translate('messages.time_schedule') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0 mb-0 error-wrapper">
                                        <label class="input-label" for="title">{{translate('messages.start_date')}}</label>
                                        <input type="date" id="date_from" class="form-control" required="" name="start_date">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0 error-wrapper">
                                        <label class="input-label" for="title">{{translate('messages.end_date')}}</label>
                                        <input type="date" id="date_to" class="form-control" required="" name="end_date">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0 error-wrapper">
                                        <label class="input-label" for="title">{{translate('messages.start_time')}}</label>
                                        <input type="time" id="start_time" class="form-control" name="start_time" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group mb-0 error-wrapper">
                                        <label class="input-label" for="title">{{translate('messages.end_time')}}</label>
                                        <input type="time" id="end_time" class="form-control" name="end_time" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="btn--container justify-content-end mt-20">
                        <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin')}}/js/tags-input.min.js"></script>
    <script>
        "use strict";
        let module_data = "";
        let module_type = "";
        let element = "";
        let countRow = 0;
        $(document).ready(function () {
            $('#food_variation_section').hide();
        });

        $('#choice_attributes').on('change', function () {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function () {
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
        });

        $('#store_id').on('change', function () {
            let route = '{{url('/')}}/admin/store/get-addons?data[]=0&store_id='+$(this).val();
            let id = 'add_on';
            getStoreData(route, id);
        });

        function getStoreData(route, id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }


        function add_more_customer_choice_option(i, name) {
            let n = name.split(' ').join('');
            $('#customer_choice_options').append('<div class="row gy-1"><div class="col-sm-3"><input type="hidden" name="choice_no[]" value="' + i + '"><input type="text" class="form-control" name="choice[]" value="' + n + '" placeholder="{{translate('messages.choice_title')}}" readonly></div><div class="col-sm-9"><input type="text" class="form-control combination_update" name="choice_options_' + i + '[]" placeholder="{{translate('messages.enter_choice_values')}}" data-role="tagsinput"></div></div>');
            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

        $('#sub-categories').on('change', function () {
            let route = '{{url('/')}}/admin/item/get-categories?parent_id='+$(this).val();
            let id = 'sub-sub-categories';
            getRequest(route, id);
        });

        function getRequest(route, id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });


        function show_item(type) {
            if (type === 'product') {
                $("#type-product").show();
                $("#type-category").hide();
            } else {
                $("#type-product").hide();
                $("#type-category").show();
            }
        }
        $("#date_from").on("change", function () {
            $('#date_to').attr('min',$(this).val());
        });

        $("#date_to").on("change", function () {
            $('#date_from').attr('max',$(this).val());
        });


        var module_id = {{Config::get('module.current_module_id')}};
        var parent_category_id = 0;
        var stock = 0;

        function modulChange(id)
        {
            $.get({
                url: "{{url('/')}}/admin/business-settings/module/show/"+id,
                dataType: 'json',
                success: function (data) {
                    module_data = data;
                    stock = module_data.data.stock;
                    module_type = data.type;
                    if(stock)
                    {
                        $('#stock_input').show();
                    }
                    else
                    {
                        $('#stock_input').hide();
                    }
                    if(module_data.add_on)
                    {
                        $('#addon_input').show();
                    }
                    else{
                        $('#addon_input').hide();
                    }

                    if(module_data.item_available_time)
                    {
                        $('#time_input').hide();
                    }
                    else{
                        $('#time_input').show();
                    }

                    if(module_data.veg_non_veg)
                    {
                        $('#veg_input').show();
                    }
                    else{
                        $('#veg_input').hide();
                    }
                    if(module_data.unit)
                    {
                        $('#unit_input').show();
                    }
                    else{
                        $('#unit_input').hide();
                    }
                    if (module_type == 'food') {
                        $('#food_variation_section').show();
                        $('#attribute_section').hide();
                    } else {
                        $('#food_variation_section').hide();
                        $('#attribute_section').show();
                    }
                    combination_update();
                },
            });
            module_id = id;
        }

        modulChange({{Config::get('module.current_module_id')}})

        $('#category_id').on('change', function () {
            parent_category_id = $(this).val();
            console.log(parent_category_id);
        });

        function combination_update() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '{{route('admin.item.variant-combination')}}',
                data: $('#campaign_form').serialize()+'&stock='+stock,
                success: function (data) {
                    $('#variant_combination').html(data.view);
                    if (data.length < 1) {
                        $('input[name="current_stock"]').attr("readonly", false);
                    }
                }
            });
        }

        $(document).on('change', '.combination_update', function () {
            combination_update();
        });

        $('#store_id').select2({
            ajax: {
                url: '{{url('/')}}/admin/store/get-stores',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        module_id: module_id
                    };
                },
                processResults: function (data) {
                    return {
                    results: data
                    };
                },
                __port: function (params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#category_id').select2({
            ajax: {
                url: '{{url('/')}}/admin/item/get-categories?parent_id=0',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        module_id: module_id
                    };
                },
                processResults: function (data) {
                    return {
                    results: data
                    };
                },
                __port: function (params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#sub-categories').select2({
            ajax: {
                url: '{{url('/')}}/admin/item/get-categories',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        module_id: module_id,
                        parent_id: parent_category_id,
                        sub_category: true
                    };
                },
                processResults: function (data) {
                    return {
                    results: data
                    };
                },
                __port: function (params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $(document).ready(function(){
            $('#date_from').attr('min',(new Date()).toISOString().split('T')[0]);
            $('#date_to').attr('min',(new Date()).toISOString().split('T')[0]);
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
        $('#campaign_form').on('submit', function(e) {
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
                url: '{{ route('admin.campaign.store-item') }}',
                data: $('#campaign_form').serialize(),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(data) {
                    $('#loading').hide();
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('{{ translate('messages.Campaign uploaded successfully') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function() {
                            location.href =
                                '{{ route('admin.campaign.list', 'item') }}';
                        }, 2000);
                    }
                }
            });
        });

        $(".lang_link").click(function(e){
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.substring(0, form_id.length - 5);
            console.log(lang);
            $("#"+lang+"-form").removeClass('d-none');
        })

            $('#reset_btn').click(function(){
                location.reload(true);
            })


    var count = 0;
    var mod_type="food";
    $(document).ready(function() {
        $("#add_new_option_button").click(function(e) {
            count++;
            var add_option_view = `
                <div class="card view_new_option mb-2" >
                    <div class="card-header">
                        <label for="" id=new_option_name_` + count + `> {{ translate('add_new') }}</label>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-lg-3 col-md-6">
                                <label for="">{{ translate('name') }}</label>
                                <input required name=options[` + count +
                `][name] class="form-control new_option_name" type="text" data-count="`+
                count +`">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label class="input-label text-capitalize d-flex alig-items-center"><span class="line--limit-1">{{ translate('messages.selcetion_type') }} </span>
                                    </label>
                                    <div class="resturant-type-group border">
                                        <label class="form-check form--check mr-2 mr-md-4">
                                            <input class="form-check-input show_min_max" data-count="`+count+`" type="radio" value="multi"
                                            name="options[` + count + `][type]" id="type` + count + `" checked">
                                            <span class="form-check-label">
                                                {{ translate('Multiple') }}
                                            </span>
                                        </label>

                                        <label class="form-check form--check mr-2 mr-md-4">
                                            <input class="form-check-input hide_min_max" data-count="`+count+`" type="radio" value="single"
                                            name="options[` + count + `][type]" id="type` + count + `">
                                            <span class="form-check-label">
                                                {{ translate('Single') }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="row g-2">
                                    <div class="col-sm-6 col-md-4">
                                        <label for="">{{ translate('Min') }}</label>
                                        <input id="min_max1_` + count + `" required  name="options[` + count + `][min]" class="form-control" type="number" min="1">
                                    </div>
                                    <div class="col-sm-6 col-md-4">
                                        <label for="">{{ translate('Max') }}</label>
                                        <input id="min_max2_` + count + `"   required name="options[` + count + `][max]" class="form-control" type="number" min="1">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="d-md-block d-none">&nbsp;</label>
                                            <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <input id="options[` + count + `][required]" name="options[` +
                count + `][required]" type="checkbox">
                                                <label for="options[` + count + `][required]" class="m-0">{{ translate('Required') }}</label>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-danger btn-sm delete_input_button"
                                                    title="{{ translate('Delete') }}">
                                                    <i class="tio-add-to-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="option_price_` + count + `" >
                            <div class="border rounded p-3 pb-0 mt-3">
                                <div  id="option_price_view_` + count + `">
                                    <div class="row g-3 add_new_view_row_class mb-3">
                                        <div class="col-md-4 col-sm-6">
                                            <label for="">{{ translate('Option_name') }}</label>
                                            <input class="form-control" required type="text" name="options[` +
                count +
                `][values][0][label]" id="">
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <label for="">{{ translate('Additional_price') }}</label>
                                            <input class="form-control" required type="number" min="0" step="0.01" name="options[` +
                count + `][values][0][optionPrice]" id="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3 p-3 mr-1 d-flex "  id="add_new_button_` + count +
                `">
                                    <button type="button" class="btn btn-outline-primary add_new_row_button" data-count="`+
                count +`" >{{ translate('Add_New_Option') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;

            $("#add_new_option").append(add_option_view);
        });
    });

    function show_min_max(data) {
        $('#min_max1_' + data).removeAttr("readonly");
        $('#min_max2_' + data).removeAttr("readonly");
        $('#min_max1_' + data).attr("required", "true");
        $('#min_max2_' + data).attr("required", "true");
    }

    function hide_min_max(data) {
        $('#min_max1_' + data).val(null).trigger('change');
        $('#min_max2_' + data).val(null).trigger('change');
        $('#min_max1_' + data).attr("readonly", "true");
        $('#min_max2_' + data).attr("readonly", "true");
        $('#min_max1_' + data).attr("required", "false");
        $('#min_max2_' + data).attr("required", "false");
    }

    $(document).on('change', '.show_min_max', function () {
        let data = $(this).data('count');
        show_min_max(data);
    });

    $(document).on('change', '.hide_min_max', function () {
        let data = $(this).data('count');
        hide_min_max(data);
    });




    function new_option_name(value, data) {
        $("#new_option_name_" + data).empty();
        $("#new_option_name_" + data).text(value)
        console.log(value);
    }

    function removeOption(e) {
        element = $(e);
        element.parents('.view_new_option').remove();
    }

    function deleteRow(e) {
        element = $(e);
        element.parents('.add_new_view_row_class').remove();
    }

    $(document).on('click', '.delete_input_button', function () {
        let e = $(this);
        removeOption(e);
    });


    $(document).on('click', '.deleteRow', function () {
        let e = $(this);
        deleteRow(e);
    });

    $(document).on('keyup', '.new_option_name', function () {
        let data = $(this).data('count');
        let value = $(this).val();
        new_option_name(value, data);
    });



    function add_new_row_button(data) {
        count = data;
        countRow = 1 + $('#option_price_view_' + data).children('.add_new_view_row_class').length;
        var add_new_row_view = `
        <div class="row add_new_view_row_class mb-3 position-relative pt-3 pt-sm-0">
            <div class="col-md-4 col-sm-5">
                    <label for="">{{ translate('Option_name') }}</label>
                    <input class="form-control" required type="text" name="options[` + count + `][values][` +
            countRow + `][label]" id="">
                </div>
                <div class="col-md-4 col-sm-5">
                    <label for="">{{ translate('Additional_price') }}</label>
                    <input class="form-control"  required type="number" min="0" step="0.01" name="options[` +
            count +
            `][values][` + countRow + `][optionPrice]" id="">
                </div>
                <div class="col-sm-2 max-sm-absolute">
                    <label class="d-none d-sm-block">&nbsp;</label>
                    <div class="mt-1">
                        <button type="button" class="btn btn-danger btn-sm deleteRow"
                            title="{{ translate('Delete') }}">
                            <i class="tio-add-to-trash"></i>
                        </button>
                    </div>
            </div>
        </div>`;
        $('#option_price_view_' + data).append(add_new_row_view);

    }

    $(document).on('click', '.add_new_row_button', function () {
        let data = $(this).data('count');
        add_new_row_button(data);
    });

</script>
@endpush
