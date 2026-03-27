@extends('layouts.admin.app')

@section('title', request()->product_gellary == 1 ? translate('Add item') : translate('Edit item'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('public/assets/admin/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/admin/css/AI/animation/product/ai-sidebar.css') }}" rel="stylesheet">
@endpush

@section('content')


    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header d-flex flex-wrap __gap-15px justify-content-between align-items-center">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/edit.png') }}" class="w--22" alt="">
                </span>
                <span>
                    {{ request()->product_gellary == 1 ? translate('Add_item') : translate('item_update') }}
                </span>
            </h1>
            <div class="d-flex align-items-end flex-wrap">
                @if (Config::get('module.current_module_type') == 'food')
                    <div class="text--primary-2 py-1 d-flex flex-wrap align-items-center foodModalShow" type="button">
                        <strong class="mr-2">{{ translate('See_how_it_works!') }}</strong>
                        <div>
                            <i class="tio-info-outined"></i>
                        </div>
                    </div>
                @else
                    <div class="text--primary-2 py-1 d-flex flex-wrap align-items-center attributeModalShow" type="button">
                        <strong class="mr-2">{{ translate('See_how_it_works!') }}</strong>
                        <div>
                            <i class="tio-info-outined"></i>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @php($openai_config = \App\CentralLogics\Helpers::get_business_settings('openai_config'))
        <!-- End Page Header -->
        <form id="product_form" enctype="multipart/form-data" class="validate-form" data-ajax="true">
            <input type="hidden" id="module_type" value="{{ Config::get('module.current_module_type') }}">
            @if (request()->product_gellary == 1)
                @php($route = route('admin.item.store', ['product_gellary' => request()->product_gellary]))
                @php($product->price = 0)
            @else
                @php($route = route('admin.item.update', [isset($temp_product) && $temp_product == 1 ? $product['item_id'] : $product['id']]))
            @endif

            <input type="hidden" class="route_url"
                value="{{ $route ?? route('admin.item.update', [isset($temp_product) && $temp_product == 1 ? $product['item_id'] : $product['id']]) }}">
            <input type="hidden" value="{{ $temp_product ?? 0 }}" name="temp_product">
            <input type="hidden" value="{{ $product['id'] ?? null }}" name="item_id">
            <input type="hidden" id="request_type" value="admin">


            <div class="row g-2">

                @includeif('admin-views.product.partials._title_and_discription')
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-wrap align-items-center">
                            <div class="w-100 d-flex gap-3 flex-wrap flex-lg-nowrap">
                                <div class="flex-grow-1 mx-auto overflow-x-auto scrollbar-primary">
                                    <label class="text-dark d-block">
                                        {{ translate('messages.item_image') }}
                                        <small>( {{ translate('messages.ratio') }} 1:1 )</small>
                                    </label>
                                    <div class="d-flex __gap-12px __new-coba overflow-x-auto pb-2" id="coba">

                                        <input type="hidden" id="removedImageKeysInput" name="removedImageKeys"
                                            value="">
                                        @foreach ($product->images as $key => $photo)
                                            @php($photo = is_array($photo) ? $photo : ['img' => $photo, 'storage' => 'public'])
                                            <div id="product_images_{{ $key }}"
                                                class="spartan_item_wrapper min-w-176px max-w-176px">
                                                <img class="img--square onerror-image"
                                                    src="{{ \App\CentralLogics\Helpers::get_full_url('product', $photo['img'] ?? '', $photo['storage']) }}"
                                                    data-onerror-image="{{ asset('public/assets/admin/img/upload-img.png') }}"
                                                    alt="Product image">
                                                <a href="#" data-key={{ $key }}
                                                    data-photo="{{ $photo['img'] }}"
                                                    class="spartan_remove_row function_remove_img"><i
                                                        class="tio-add-to-trash"></i></a>

                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex-grow-1 mx-auto pb-2 flex-shrink-0">
                                    <label class="text-dark d-block">
                                        {{ translate('messages.item_thumbnail') }}
                                        <small class="text-danger">* ( {{ translate('messages.ratio') }} 1:1 )</small>
                                    </label>
                                    <label class="d-inline-block m-0 position-relative error-wrapper">
                                        <img class="img--176 border onerror-image" id="viewer"
                                            src="{{ $product['image_full_url'] ?? asset('public/assets/admin/img/upload-img.png') }}"
                                            data-onerror-image="{{ asset('public/assets/admin/img/upload-img.png') }}"
                                            alt="thumbnail" />
                                        <div class="icon-file-group">
                                            <div class="icon-file">
                                                <input type="file" name="image" id="customFileEg1"
                                                    class="custom-file-input read-url"
                                                    accept=".webp, .jpg, .png, .jpeg, .webp, .gif, .bmp, .tif, .tiff|image/*">
                                                <i class="tio-edit"></i>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @includeif('admin-views.product.partials._category_and_general')
                @includeif('admin-views.product.partials._price_and_stock')

                @if (Config::get('module.current_module_type') == 'food')

                    <div class="col-lg-12" id="food_variation_section">
                        <div class="variation_wrapper">
                            <div class="outline-wrapper">
                                <div class="card shadow--card-2 border-0 bg-animate">

                                    <div class="card-header flex-wrap">
                                        <h5 class="card-title">
                                            <span class="card-header-icon mr-2">
                                                <i class="tio-canvas-text"></i>
                                            </span>
                                            <span>{{ translate('messages.food_variations') }}</span>
                                        </h5>
                                        <div>

                                            <a class="btn text--primary-2" id="add_new_option_button">
                                                {{ translate('add_new_variation') }}
                                                <i class="tio-add"></i>
                                            </a>
                                            @if (isset($openai_config) && data_get($openai_config, 'status') == 1)
                                                <button type="button"
                                                    class="btn bg-white text-primary opacity-1 generate_btn_wrapper variation_setup_auto_fill"
                                                    id="variation_setup_auto_fill"
                                                    data-route="{{ route('admin.product.variation-setup-auto-fill') }}"
                                                    data-error="{{ translate('Please provide an item name and description so the AI can generate a suitable food variations.') }}"
                                                    data-lang="en">
                                                    <div class="btn-svg-wrapper">
                                                        <img width="18" height="18" class=""
                                                            src="{{ asset('public/assets/admin/img/svg/blink-right-small.svg') }}"
                                                            alt="">
                                                    </div>
                                                    <span class="ai-text-animation d-none" role="status">
                                                        {{ translate('Just_a_second') }}
                                                    </span>
                                                    <span class="btn-text">{{ translate('Generate') }}</span>
                                                </button>
                                            @endif
                                        </div>

                                    </div>
                                    <div class="card-body">
                                        <div id="add_new_option">
                                            @if (isset($product->food_variations) && count(json_decode($product->food_variations, true)) > 0)
                                                @foreach (json_decode($product->food_variations, true) as $key_choice_options => $item)
                                                    @if (isset($item['price']))
                                                        @break

                                                    @else
                                                        @include(
                                                            'admin-views.product.partials._new_variations',
                                                            [
                                                                'item' => $item,
                                                                'key' => $key_choice_options + 1,
                                                            ]
                                                        )
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>

                                        <!-- Empty Variation -->
                                        @if (!isset($product->food_variations) || count(json_decode($product->food_variations, true)) < 1)
                                            <div id="empty-variation">
                                                <div class="text-center">
                                                    <img src="{{ asset('/public/assets/admin/img/variation.png') }}"
                                                        alt="">
                                                    <div>{{ translate('No variation added') }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                @if (Config::get('module.current_module_type') != 'food')

                    <div class="col-md-12" id="attribute_section">
                        <div class="variation_wrapper">
                            <div class="outline-wrapper">
                                <div class="card shadow--card-2 border-0 bg-animate">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <span class="card-header-icon"><i class="tio-canvas-text"></i></span>
                                            <span>{{ translate('attribute') }}</span>
                                        </h5>
                                        @if (isset($openai_config) && data_get($openai_config, 'status') == 1)
                                            <button type="button"
                                                class="btn bg-white text-primary opacity-1 generate_btn_wrapper p-0 mb-2 other_variation_setup_auto_fill"
                                                id="other_variation_setup_auto_fill"
                                                data-route="{{ route('admin.product.generate-other-variation-data') }}"
                                                data-error="{{ translate('Please provide an item name and description so the AI can generate a suitable variations.') }}"
                                                data-lang="en">
                                                <div class="btn-svg-wrapper">
                                                    <img width="18" height="18" class=""
                                                        src="{{ asset('public/assets/admin/img/svg/blink-right-small.svg') }}"
                                                        alt="">
                                                </div>
                                                <span class="ai-text-animation d-none" role="status">
                                                    {{ translate('Just_a_second') }}
                                                </span>
                                                <span class="btn-text">{{ translate('Generate') }}</span>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="card-body pb-0">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <div class="form-group mb-0">
                                                    <label class="input-label"
                                                        for="exampleFormControlSelect1">{{ translate('messages.attribute') }}<span
                                                            class="input-label-secondary"></span></label>
                                                    <select name="attribute_id[]" id="choice_attributes"
                                                        class="form-control js-select2-custom" multiple="multiple">
                                                        @foreach (\App\Models\Attribute::orderBy('name')->get() as $attribute)
                                                            <option value="{{ $attribute['id'] }}"
                                                                {{ in_array($attribute->id, json_decode($product['attributes'], true)) ? 'selected' : '' }}>
                                                                {{ $attribute['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <div class="customer_choice_options d-flex __gap-24px"
                                                        id="customer_choice_options">
                                                        @include('admin-views.product.partials._choices', [
                                                            'choice_no' => json_decode($product['attributes']),
                                                            'choice_options' => json_decode(
                                                                $product['choice_options'],
                                                                true),
                                                        ])
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="variant_combination" id="variant_combination">
                                                    @include(
                                                        'admin-views.product.partials._edit-combinations',
                                                        [
                                                            'combinations' => json_decode(
                                                                $product['variations'],
                                                                true),
                                                            'stock' => config(
                                                                'module.' . $product->module->module_type)['stock'],
                                                        ]
                                                    )
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if (Config::get('module.current_module_type') == 'ecommerce')
                    @includeIf('admin-views.business-settings.landing-page-settings.partial._meta_data', ['item' => $product])
                @endif

                <div class="col-md-12">
                    <div class="btn--container justify-content-end">
                        <button type="reset" id="reset_btn"
                            class="btn btn--reset">{{ translate('messages.reset') }}</button>
                        <button type="submit"
                            class="btn btn--primary">{{ isset($temp_product) && $temp_product == 1 ? translate('Edit_&_Approve') : translate('messages.submit') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal" id="food-modal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close foodModalClose" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/xG8fO7TXPbk"
                            title="YouTube video player" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="attribute-modal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close attributeModalClose" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/xG8fO7TXPbk"
                            title="YouTube video player" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span id="message-enter-choice-values" data-text="{{ translate('enter_choice_values') }}"></span>
    @includeif('admin-views.product.partials._ai_sidebar')

@endsection


@push('script_2')
    <script>
        let count = $('.count_div').length;
    </script>

    <script src="{{ asset('public/assets/admin') }}/js/tags-input.min.js"></script>
    <script src="{{ asset('public/assets/admin/js/spartan-multi-image-picker.js') }}"></script>


    <script src="{{ asset('public/assets/admin/js/AI/products/product-title-autofill.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/AI/products/product-description-autofill.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/AI/products/general-setup-autofill.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/AI/products/product-others-autofill.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/AI/products/seo-section-autofill.js') }}"></script>
    @if (Config::get('module.current_module_type') == 'food')
        <script src="{{ asset('public/assets/admin/js/AI/products/variation-setup-auto-fill.js') }}"></script>
    @else
        <script src="{{ asset('public/assets/admin/js/AI/products/other-variation-setup-auto-fill.js') }}"></script>
    @endif

    <script src="{{ asset('public/assets/admin/js/AI/products/ai-sidebar.js') }}"></script>

    <script src="{{ asset('/public/assets/admin/js/AI/products/compressor/image-compressor.js') }}"></script>
    <script src="{{ asset('/public/assets/admin/js/AI/products/compressor/compressor.min.js') }}"></script>


    <script>
        "use strict";
        let removedImageKeys = [];
        let element = "";


        $(document).on('click', '.function_remove_img', function() {
            let key = $(this).data('key');
            let photo = $(this).data('photo');
            function_remove_img(key, photo);
        });

        function function_remove_img(key, photo) {
            $('#product_images_' + key).addClass('d-none');
            removedImageKeys.push(photo);
            $('#removedImageKeysInput').val(removedImageKeys.join(','));
        }

        $(document).on('change', '.show_min_max', function () {
            let count = $(this).data('count');
            toggleMinMaxRequired(count, true);
        });

        $(document).on('change', '.hide_min_max', function () {
            let count = $(this).data('count');
            toggleMinMaxRequired(count, false);
        });

        function toggleMinMaxRequired(count, required) {
            let $min = $('#min_max1_' + count);
            let $max = $('#min_max2_' + count);

            if (required) {
                $min.prop('readonly', false).prop('required', true);
                $max.prop('readonly', false).prop('required', true);
            } else {
                $min.prop('readonly', true).prop('required', false).val(null).trigger('change').removeClass('is-invalid');
                $max.prop('readonly', true).prop('required', false).val(null).trigger('change').removeClass('is-invalid');
                $('div.form-validation-error[data-for="options[' + count + '][min]"]').remove();
                $('div.form-validation-error[data-for="options[' + count + '][max]"]').remove();
            }
        }



        $(document).ready(function() {
            $("#add_new_option_button").click(function(e) {
                add_new_option_button();
            });
        });


        function add_new_option_button() {
            $('#empty-variation').hide();
            count++;
            let add_option_view = `
                                <div class="__bg-F8F9FC-card view_new_option mb-2">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <label class="form-check form--check">
                                                <input id="options[` + count + `][required]" name="options[` + count + `][required]" class="form-check-input" type="checkbox">
                                                <span class="form-check-label">{{ translate('Required') }}</span>
                                            </label>
                                            <div>
                                                <button type="button" class="btn btn-danger btn-sm delete_input_button"
                                                    title="{{ translate('Delete') }}">
                                                    <i class="tio-add-to-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-xl-4 col-lg-6">
                                                <label for="">{{ translate('name') }}</label>
                                                <input required name=options[` + count +
                `][name] class="form-control new_option_name" type="text" data-count="` +
                count +
                `">
                                            </div>

                                            <div class="col-xl-4 col-lg-6">
                                                <div>
                                                    <label class="input-label text-capitalize d-flex align-items-center"><span class="line--limit-1">{{ translate('messages.selcetion_type') }} </span>
                                                    </label>
                                                    <div class="resturant-type-group px-0">
                                                        <label class="form-check form--check mr-2 mr-md-4">
                                                            <input class="form-check-input show_min_max" data-count="` +
                count + `" type="radio" value="multi"
                                                            name="options[` + count + `][type]" id="type` + count +
                `" checked
                                                            >
                                                            <span class="form-check-label">
                                                                {{ translate('Multiple Selection') }}
                                </span>
                            </label>

                            <label class="form-check form--check mr-2 mr-md-4">
                                <input class="form-check-input hide_min_max" data-count="` + count + `" type="radio" value="single"
                                name="options[` + count + `][type]" id="type` + count +
                `"
                                                            >
                                                            <span class="form-check-label">
                                                                {{ translate('Single Selection') }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6">
                    <div class="row g-2">
                        <div class="col-6">
                            <label for="">{{ translate('Min') }}</label>
                                                        <input id="min_max1_` + count + `" required  name="options[` +
                count + `][min]" class="form-control" type="number" min="1">
                                                    </div>
                                                    <div class="col-6">
                                                        <label for="">{{ translate('Max') }}</label>
                                                        <input id="min_max2_` + count + `"   required name="options[` +
                count + `][max]" class="form-control" type="number" min="1">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="option_price_` + count + `" >
                                            <div class="bg-white border rounded p-3 pb-0 mt-3">
                                                <div  id="option_price_view_` + count +
                `">
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
                                                    <button type="button" class="btn btn--primary btn-outline-primary add_new_row_button" data-count="` +
                count + `">{{ translate('Add_New_Option') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;

            $("#add_new_option").append(add_option_view);



        }


        function new_option_name(value, data) {
            $("#new_option_name_" + data).empty();
            $("#new_option_name_" + data).text(value)
            console.log(value);
        }

        function removeOption(e) {
            element = $(e);
            element.parents('.view_new_option').remove();
            combination_update();
        }

        $(document).on('click', '.delete_input_button', function() {
            let e = $(this);
            removeOption(e);
        });

        function deleteRow(e) {
            element = $(e);
            element.parents('.add_new_view_row_class').remove();
            combination_update();
        }

        $(document).on('click', '.deleteRow', function() {
            let e = $(this);
            deleteRow(e);
        });
        let countRow = 0;

        function add_new_row_button(data) {
            // count = data;
            countRow = 1 + $('#option_price_view_' + data).children('.add_new_view_row_class').length;
            let add_new_row_view = `
            <div class="row add_new_view_row_class mb-3 position-relative pt-3 pt-sm-0">
                <div class="col-md-4 col-sm-5">
                        <label for="">{{ translate('Option_name') }}</label>
                        <input class="form-control" required type="text" name="options[` + data + `][values][` +
                countRow + `][label]" id="">
                    </div>
                    <div class="col-md-4 col-sm-5">
                        <label for="">{{ translate('Additional_price') }}</label>
                        <input class="form-control"  required type="number" min="0" step="0.01" name="options[` +
                data +
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

        $(document).on('click', '.add_new_row_button', function() {
            let data = $(this).data('count');
            add_new_row_button(data);
        });

        $(document).on('keyup', '.new_option_name', function() {
            let data = $(this).data('count');
            let value = $(this).val();
            new_option_name(value, data);
        });

        $('#store_id').on('change', function() {
            let route = '{{ url('/') }}/admin/store/get-addons?data[]=0&store_id=';
            let store_id = $(this).val();
            let id = 'add_on';
            getStoreData(route, store_id, id);
        });

        function getStoreData(route, store_id, id) {
            $.get({
                url: route + store_id,
                dataType: 'json',
                success: function(data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }

        function getRequest(route, id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function(data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function() {
            readURL(this);
            $('#image-viewer-section').show(1000)
        });

        $(document).ready(function() {
            @if (count(json_decode($product['add_ons'], true)) > 0)
                getStoreData(
                    '{{ url('/') }}/admin/store/get-addons?@foreach (json_decode($product['add_ons'], true) as $addon)data[]={{ $addon }}& @endforeach store_id=',
                    '{{ $product['store_id'] }}', 'add_on');
            @else
                getStoreData('{{ url('/') }}/admin/store/get-addons?data[]=0&store_id=',
                    '{{ $product['store_id'] }}', 'add_on');
            @endif
        });

        let module_id = {{ $product->module_id }};
        let module_type = "{{ $product->module->module_type }}";
        let parent_category_id = {{ $category ? $category->id : 0 }};
        <?php
        $module_data = config('module.' . $product->module->module_type);
        unset($module_data['description']);
        ?>
        let module_data = {{ str_replace('"', '', json_encode($module_data)) }};
        let stock = {{ $product->module->module_type == 'food' ? 'false' : 'true' }};
        input_field_visibility_update();

        function modulChange(id) {
            $.get({
                url: "{{ url('/') }}/admin/module/" + id,
                dataType: 'json',
                success: function(data) {
                    module_data = data.data;
                    stock = module_data.stock;
                    input_field_visibility_update();
                    combination_update();
                },
            });
            module_id = id;
        }

        function input_field_visibility_update() {
            if (module_data.stock) {
                $('#stock_input').show();
            } else {
                $('#stock_input').hide();
            }
            if (module_data.add_on) {
                $('#addon_input').show();
            } else {
                $('#addon_input').hide();
            }

            if (module_data.item_available_time) {
                $('#time_input').show();
            } else {
                $('#time_input').hide();
            }

            if (module_data.veg_non_veg) {
                $('#veg_input').show();
            } else {
                $('#veg_input').hide();
            }

            if (module_data.unit) {
                $('#unit_input').show();
            } else {
                $('#unit_input').hide();
            }
            if (module_data.common_condition) {
                $('#condition_input').show();
            } else {
                $('#condition_input').hide();
            }
            if (module_data.brand) {
                $('#brand_input').show();
            } else {
                $('#brand_input').hide();
            }
            if (module_type == 'food') {
                $('#food_variation_section').show();
                $('#attribute_section').hide();
            } else {
                $('#food_variation_section').hide();
                $('#attribute_section').show();
            }
            if (module_data.organic) {
                $('#organic').show();
            } else {
                $('#organic').hide();
            }
            if (module_data.basic) {
                $('#basic').show();
            } else {
                $('#basic').hide();
            }
            if (module_data.nutrition) {
                $('#nutrition').show();
            } else {
                $('#nutrition').hide();
            }
            if (module_data.allergy) {
                $('#allergy').show();
            } else {
                $('#allergy').hide();
            }
        }

        $('#category_id').on('change', function() {
            parent_category_id = $(this).val();
            let subCategoriesSelect = $('#sub-categories');
            subCategoriesSelect.empty();
            subCategoriesSelect.append(
                '<option value="" selected>{{ translate('messages.select_sub_category') }}</option>');
        });

        $('.foodModalClose').on('click', function() {
            $('#food-modal').hide();
        })

        $('.foodModalShow').on('click', function() {
            $('#food-modal').show();
        })

        $('.attributeModalClose').on('click', function() {
            $('#attribute-modal').hide();
        })

        $('.attributeModalShow').on('click', function() {
            $('#attribute-modal').show();
        })

        $(document).on('ready', function() {
            $('.js-select2-custom').each(function() {
                let select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });

        $('#condition_id').select2({
            ajax: {
                url: '{{ url('/') }}/admin/common-condition/get-all',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#brand_id').select2({
            ajax: {
                url: '{{ url('/') }}/admin/brand/get-all',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#store_id').select2({
            ajax: {
                url: '{{ url('/') }}/admin/store/get-stores',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        module_id: module_id
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#category_id').select2({
            ajax: {
                url: '{{ url('/') }}/admin/item/get-categories?parent_id=0',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        module_id: module_id
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#sub-categories').select2({
            ajax: {
                url: '{{ url('/') }}/admin/item/get-categories',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        module_id: module_id,
                        parent_id: parent_category_id,
                        sub_category: true
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#choice_attributes').on('change', function() {
            $('#customer_choice_options').html(null);
            combination_update();
            $.each($("#choice_attributes option:selected"), function() {
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
        });

        function add_more_customer_choice_option(i, name) {
            let n = name;

            $('#customer_choice_options').append(
                `<div class="__choos-item"><div><input type="hidden" name="choice_no[]" value="${i}"><input type="text" class="form-control d-none" name="choice[]" value="${n}" placeholder="{{ translate('messages.choice_title') }}" readonly> <label class="form-label">${n}</label> </div><div><input type="text" class="form-control combination_update" name="choice_options_${i}[]" placeholder="{{ translate('messages.enter_choice_values') }}" data-role="tagsinput"></div></div>`
            );
            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

        setTimeout(function() {
            $('.call-update-sku').on('change', function() {
                combination_update();
            });
        }, 2000)

        $('#colors-selector').on('change', function() {
            combination_update();
        });

        $('input[name="unit_price"]').on('keyup', function() {
            combination_update();
        });

        function combination_update() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('admin.item.variant-combination') }}",
                data: $('#product_form').serialize() + '&stock=' + stock,
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(data) {
                    $('#loading').hide();
                    $('#variant_combination').html(data.view);
                    if (data.length < 1) {
                        $('input[name="current_stock"]').attr("readonly", false);
                        $('input[name="current_stock"]').val(0);
                    }
                    update_qty();
                }
            });
        }

        $(document).on('change', '.combination_update', function() {
            combination_update();
        });
        // $('#product_form').on('keydown', function(e) {
        //        if (e.key === 'Enter') {
        //        e.preventDefault(); // Prevent submission on Enter
        //        }
        //    });

        $('#product_form').on('submit', function(e) {
            e.preventDefault();
            if(typeof FormValidation != 'undefined' && !FormValidation.validateForm(this)) {
                return false;
            }

            let formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: $('.route_url').val(),
                data: $('#product_form').serialize(),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(data) {
                    console.log(data);
                    $('#loading').hide();
                    if (data.errors) {
                        for (let i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    }
                    if (data.product_approval) {
                        toastr.success(data.product_approval, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                    if (data.success) {
                        toastr.success(data.success, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function() {
                            location.href =
                                '{{ route('admin.item.list') }}';
                        }, 2000);
                    }
                }
            });
        });

        $('#reset_btn').click(function() {
            location.reload(true);
        })

        update_qty();

        function update_qty() {
            let total_qty = 0;
            let qty_elements = $('input[name^="stock_"]');
            for (let i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val() || 0);
            }
            if (qty_elements.length > 0) {

                $('input[name="current_stock"]').attr("readonly", true);
                $('input[name="current_stock"]').val(total_qty);
            } else {
                $('input[name="current_stock"]').attr("readonly", false);
            }
        }
        
        $(document).on('keyup', 'input[name^="stock_"]', function() {
            let total_qty = 0;
            let qty_elements = $('input[name^="stock_"]');
            for (let i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val() || 0);
            }
            $('input[name="current_stock"]').val(total_qty);
        });

        function initImagePicker() {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'item_images[]',
                maxCount: 5,
                rowHeight: '176px !important',
                groupClassName: 'spartan_item_wrapper min-w-176px max-w-176px',
                maxFileSize: 1024 * 1024 * {{ MAX_FILE_SIZE }},
                placeholderImage: {
                    image: "{{ asset('public/assets/admin/img/upload-img.png') }}",
                    width: '176px'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {
                    setTimeout(function() {
                        let $newInput = $("#coba .spartan_item_wrapper").last();
                        if ($newInput.length) {
                            $newInput[0].scrollIntoView({
                                behavior: "smooth",
                                inline: "end",
                                block: "nearest"
                            });
                        }
                    }, 50);
                },
                onExtensionErr: function(index, file) {
                    toastr.error("{{ translate('messages.please_only_input_png_or_jpg_type_file') }}", {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function(index, file) {
                    toastr.error("{{ translate('messages.file_size_too_big') }}", {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }

        $(function() {
            initImagePicker();
        });

        $('#reset_btn').click(function() {
            $('#module_id').val(null).trigger('change');
            $('#store_id').val(null).trigger('change');
            $('#category_id').val(null).trigger('change');
            $('#sub-categories').val(null).trigger('change');
            $('#unit').val(null).trigger('change');
            $('#veg').val(0).trigger('change');
            $('#add_on').val(null).trigger('change');
            $('#discount_type').val(null).trigger('change');
            $('#choice_attributes').val(null).trigger('change');
            $('#customer_choice_options').empty().trigger('change');
            $('#variant_combination').empty().trigger('change');
            $('#viewer').attr('src', "{{ asset('public/assets/admin/img/upload.png') }}");
            $("#coba").empty();
            initImagePicker();
        })
    </script>
@endpush
