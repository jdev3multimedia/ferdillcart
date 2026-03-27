@extends('layouts.admin.app')

@section('title',translate('Item Bulk Import'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('public/assets/admin/css/tags-input.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/items.png')}}" class="w--22" alt="">
                </span>
                <span>
                    {{translate('messages.items_bulk_import')}}
                </span>
            </h1>
        </div>
        <!-- Content Row -->
        <div class="card">
            <div class="card-body">
                <div class="export-steps-2">
                    <div class="row g-4">
                        <div class="col-sm-6 col-lg-4">
                            <div class="export-steps-item-2 h-100">
                                <div class="top">
                                    <div>
                                        <h3 class="fs-20">{{translate('Step 1')}}</h3>
                                        <div>
                                            {{translate('Download_Excel_File')}}
                                        </div>
                                    </div>
                                    <img src="{{asset('/public/assets/admin/img/bulk-import-1.png')}}" alt="">
                                </div>
                                <h4>{{ translate('Instruction') }}</h4>
                                <ul class="m-0 pl-4">
                                    <li>
                                        {{ translate('Download_the_format_file_and_fill_it_with_proper_data.') }}
                                    </li>
                                    <li>
                                        {{ translate('You_can_download_the_example_file_to_understand_how_the_data_must_be_filled.') }}
                                    </li>
                                    <li>
                                        {{ translate('Have_to_upload_excel_file.') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="export-steps-item-2 h-100">
                                <div class="top">
                                    <div>
                                        <h3 class="fs-20">{{translate('Step 2')}}</h3>
                                        <div>
                                            {{translate('Match_Spread_sheet_data_according_to_instruction')}}
                                        </div>
                                    </div>
                                    <img src="{{asset('/public/assets/admin/img/bulk-import-2.png')}}" alt="">
                                </div>
                                  <h4>{{ translate('Instruction') }}</h4>
                                <ul class="m-0 pl-4">
                                    <li>
                                        {{ translate('Fill_up_the_data_according_to_the_format_and_validations.') }}
                                    </li>
                                    <li>
                                        {{ translate('You_can_get_store_id_module_id_and_unit_id_from_their_list_please_input_the_right_ids.') }}
                                    </li>
                                    <li>
                                        {{ translate('For_ecommerce_item_avaliable_time_start_and_end_will_be_00:00:00_and_23:59:59') }}
                                    </li>
                                    <li>
                                        {{ translate('If_you_want_to_create_a_product_with_variation,_just_create_variations_from_the_generate_variation_section_below_and_click_generate_value.') }}
                                    </li>
                                    <li>
                                        {{ translate('Copy_the_value_and_paste_the_the_spread_sheet_file_column_name_variation_in_the_selected_product_row.') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="export-steps-item-2 h-100">
                                <div class="top">
                                    <div>
                                        <h3 class="fs-20">{{translate('Step 3')}}</h3>
                                        <div>
                                            {{translate('Validate data and complete import')}}
                                        </div>
                                    </div>
                                    <img src="{{asset('/public/assets/admin/img/bulk-import-3.png')}}" alt="">
                                </div>
                                  <h4>{{ translate('Instruction') }}</h4>
                                <ul class="m-0 pl-4">
                                    <li>
                                       {{ translate('In_the_Excel_file_upload_section,_first_select_the_upload_option.') }}
                                    </li>
                                    <li>
                                       {{ translate('Upload_your_file_in_.xls,_.xlsx_format.') }}
                                    </li>
                                    <li>
                                       {{ translate('Finally_click_the_upload_button.') }}
                                    </li>
                                    <li>
                                       {{ translate('You_can_upload_your_product_images_in_product_folder_from_gallery_and_copy_image`s_path.') }}
                                    </li>
                                    <li>
                                       {{ translate('Image_file_name_must_be_in_30_character.') }}
                                    </li>


                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center pb-4">
                    <h3 class="mb-3 export--template-title font-regular">{{translate('download_spreadsheet_template')}}</h3>
                    <div class="btn--container justify-content-center export--template-btns">
                        @if($module_type== 'food')
                        <a href="{{asset('public/assets/foods_bulk_format.xlsx')}}" download="" class="btn btn--primary btn-outline-primary">{{translate('With Current Data')}}</a>
                        @else
                        <a href="{{asset('public/assets/items_bulk_format.xlsx')}}" download="" class="btn btn--primary btn-outline-primary">{{translate('With Current Data')}}</a>
                            @endif
                        <a href="{{asset('public/assets/items_bulk_format_nodata.xlsx')}}" download="" class="btn btn--primary">{{translate('Without Any Data')}}</a>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <form class="product-form" id="import_form" action="{{route('admin.item.bulk-import')}}" method="POST"
                enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="button" id="btn_value">
            <div class="card mt-2 rest-part">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <h5 class="text-capitalize mb-3">{{ translate('Select_Data_Upload_type') }}</h5>
                            <div class="module-radio-group border rounded">
                                <label class="form-check form--check">
                                    <input class="form-check-input "   value="import" type="radio" name="upload_type" checked>
                                    <span class="form-check-label py-20">
                                        {{ translate('Upload_New_Data') }}
                                    </span>
                                </label>
                                <label class="form-check form--check">
                                    <input class="form-check-input " value="update" type="radio" name="upload_type">
                                    <span class="form-check-label py-20">
                                        {{ translate('Update_Existing_Data') }}
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <h5 class="text-capitalize mb-3">{{ translate('Import_items_file') }}</h5>
                            <label class="uploadDnD d-block">
                                <div class="form-group inputDnD input_image input_image_edit position-relative">
                                    <div class="upload-text">
                                        <div>
                                            <img src="{{asset('/public/assets/admin/img/bulk-import-3.png')}}" alt="">
                                        </div>
                                        <div class="filename">{{translate('Must_be_Excel_files_using_our_Excel_template_above')}}</div>
                                    </div>
                                    <input type="file" name="products_file" class="form-control-file text--primary font-weight-bold action-upload-section-dot-area" id="products_file">
                                </div>
                            </label>

                        </div>
                    </div>
                    <div class="btn--container justify-content-end mt-20">
                        <button id="reset_btn" type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="button" class="btn btn--primary update_or_import">{{translate('messages.Upload')}}</button>
                    </div>
                </div>
            </div>
        </form>
        <form action="javascript:" method="post" id="item_form" enctype="multipart/form-data">
            <div id="food_variation_section" style="display: none">
                <div class="card mt-2 rest-part">
                    <div class="card-header">
                        <h5 class="card-title">
                            <span>{{ translate('messages.food_variations_generator') }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-12">
                                <div id="add_new_option">
                                </div>
                                <br>
                                <div class="mt-2 text-center">
                                    <a class="btn btn--primary"
                                        id="add_new_option_button">{{ translate('add_new_variation') }}</a>
                                </div> <br><br>
                            </div>
                        </div>
                        <div class="btn--container justify-content-end mb-3">
                            <button type="submit" class="btn btn--warning">{{translate('Generate Value')}}</button>
                        </div>
                        <textarea name="" id="food_variation_outpot" class="form-control" rows="5" readonly></textarea>
                        <div class="btn--container justify-content-end mt-2 mb-2">
                            <button type="reset" class="btn btn--reset">{{translate('Reset')}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <br>
        <form action="javascript:" method="post" id="item_form_2" enctype="multipart/form-data">
            <div id="attribute_section" style="display: none">
                <h4 class="mb-3">{{translate('Generate Variation')}}</h4>
                <div class="card card mt-2 rest-part">
                    <div class="card-header border-0 p-0">
                        <div class="alert w-100 alert-soft-primary alert-dismissible fade show d-flex m-0" role="alert">
                            <div>
                                <img src="{{asset('/public/assets/admin/img/icons/intel.png')}}" width="22" alt="">
                            </div>
                            <div class="w-0 flex-grow-1 pl-3">
                                <strong>{{ translate('Attention!') }}</strong>
                              {{ translate('You_must_generate_variations_from_this_generator_if_you_want_to_add_variations_to_your_products.You_must_copy_from_the_specific_filed_and_past_it_to_the_specific_column_at_your_excel_sheet.Otherwise_you_might_get_500_error_if_you_swap_or_entered_invalid_data.And_if_you_want_to_make_it_empty_then_you_have_to_enter_an_empty_array_[_]_.') }}
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <label class="input-label m-0">{{ translate('messages.attribute') }}<span class="input-label-secondary"></span></label>
                            <button type="submit" class="btn btn--primary">{{translate('generate value')}}</button>
                        </div>
                        <div class="row g-2">
                            <div class="col-lg-6">
                                <div class="form-group mb-0">
                                    <select name="attribute_id[]" id="choice_attributes"
                                        class="form-control js-select2-custom" multiple="multiple">
                                        @foreach (\App\Models\Attribute::orderBy('name')->get() as $attribute)
                                            <option value="{{ $attribute['id'] }}">{{ $attribute['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="customer_choice_options pt-3" id="customer_choice_options">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="variant_combination" id="variant_combination">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="">{{ translate('messages.Generated_varient') }} <span class="form-label-secondary text-danger " data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.This_field_is_for_geenrated_variation._copy_them_&_paste_into_excel_sheet') }} "><img src="{{ asset('/public/assets/admin/img/info-circle.svg') }}" alt="Veg non veg"> * </span></label>
                                <textarea name="" id="variation_output" class="form-control" rows="5" readonly></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="">{{ translate('messages.Generated_choice_option') }} <span class="form-label-secondary text-danger " data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.Choice_option_is_required_if_you_are_using_product_variation') }}"><img src="{{ asset('/public/assets/admin/img/info-circle.svg') }}" alt="Veg non veg"> * </span></label>
                                <textarea name="" id="choice_output" class="form-control" rows="5" readonly></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="">{{ translate('messages.Generated_attributes_field') }} <span class="form-label-secondary text-danger " data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.Attributes_is_required_if_you_are_using_product_variation') }}"><img src="{{ asset('/public/assets/admin/img/info-circle.svg') }}" alt="Veg non veg"> * </span></label>
                                <textarea name="" id="attributes" class="form-control" rows="5" readonly></textarea>
                            </div>
                        </div>

                        <div class="btn--container justify-content-end mt-2 mb-2">
                            <button type="reset" class="btn btn--reset">{{translate('Reset')}}</button>
                        </div>


                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin') }}/js/tags-input.min.js"></script>
    <script src="{{asset('public/assets/admin')}}/js/view-pages/product-import.js"></script>
<script>
    "use strict";
    $('.update_or_import').on("click", function () {
    let upload_type = $('input[name="upload_type"]:checked').val();
    myFunction(upload_type)
});
$('#reset_btn').click(function(){
    $('#products_file').val('');
    $('.filename').text('{{translate('Must_be_Excel_files_using_our_Excel_template_above')}}');
})
    $(".action-upload-section-dot-area").on("change", function () {
        if (this.files && this.files[0]) {
            let reader = new FileReader();
            reader.onload = () => {
                let imgName = this.files[0].name;
                $(this).closest(".uploadDnD").find('.filename').text(imgName);
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    $(document).ready(function() {
        @if($module_type== 'food')
            $('#food_variation_section').show();
            $('#attribute_section').hide();
        @else
            $('#food_variation_section').hide();
            $('#attribute_section').show();
        @endif
        $("#add_new_option_button").click(function(e) {
            count++;
            let add_option_view = `
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
                                                name="options[` + count + `][type]" id="type` + count +
                `" checked
                                                >
                                                <span class="form-check-label">
                                                    {{ translate('Multiple Selection') }}
                </span>
            </label>

            <label class="form-check form--check mr-2 mr-md-4">
                <input class="form-check-input hide_min_max" data-count="`+count+`" type="radio" value="single"
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
                                            <div class="d-flex align-items-center justify-content-between pt-2">
                                            <div class="form-check form--check">
                                                <input class="form-check-input" id="options[` + count + `][required]" name="options[` +
                count + `][required]" type="checkbox">
                                                <label for="options[` + count + `][required]" class="m-0">{{ translate('Required') }}</label>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-outline-danger btn-sm delete_input_button"
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
                            <div class="__bg-F8F9FC-card border rounded p-3 pb-0 mt-3">
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
                                <div id="add_new_button_` + count +
                `">
                                   <button type="button" class="text-success bg-transparent border-0 p-0 add_new_row_button" data-count="`+
                count +`" > <i class="tio-add-square"></i> {{ translate('Add_New_Option') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;

            $("#add_new_option").append(add_option_view);
        });
    });

    function add_new_row_button(data) {
        count = data;
        countRow = 1 + $('#option_price_view_' + data).children('.add_new_view_row_class').length;
        let add_new_row_view = `
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

    $('#choice_attributes').on('change', function() {
        $('#customer_choice_options').html(null);
        $('#variant_combination').html(null);
        $.each($("#choice_attributes option:selected"), function() {
            if ($(this).val().length > 50) {
                toastr.error(
                    '{{ translate('validation.max.string', ['attribute' => translate('messages.variation'), 'max' => '50']) }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                return false;
            }
            add_more_customer_choice_option($(this).val(), $(this).text());
        });
    });

    function add_more_customer_choice_option(i, name) {
        let n = name;
        $('#customer_choice_options').append(
            '<div class="row gy-1"><div class="col-sm-3"><input type="hidden" name="choice_no[]" value="' + i +
            '"><input type="text" class="form-control" name="choice[]" value="' + n +
            '" placeholder="{{ translate('messages.choice_title') }}" readonly></div><div class="col-sm-9"><input type="text" class="form-control combination_update" name="choice_options_' +
            i +
            '[]" placeholder="{{ translate('messages.enter_choice_values') }}" data-role="tagsinput"></div></div>'
        );
        $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
    }

    function combination_update() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "{{ route('admin.item.variant-combination') }}",
            data: $('#item_form_2').serialize() + '&stock=' + true,
            beforeSend: function() {
                $('#loading').show();
            },
            success: function(data) {
                $('#loading').hide();
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

    $('#item_form_2').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.post({
            url: '{{ route('admin.item.variation-generate') }}',
            data: $('#item_form_2').serialize(),
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
                    for (let i = 0; i < data.errors.length; i++) {
                        toastr.error(data.errors[i].message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                } else {
                    $('#variation_output').val(data.variation)
                    $('#choice_output').val(data.choice_options)
                    $('#attributes').val(data.attributes)
                }
            }
        });
    });

    $('#item_form').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.post({
            url: '{{ route('admin.item.food-variation-generate') }}',
            data: $('#item_form').serialize(),
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
                    for (let i = 0; i < data.errors.length; i++) {
                        toastr.error(data.errors[i].message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                } else {
                    $('#food_variation_outpot').val(data.variation)
                }
            }
        });
    });

    function myFunction(data) {
        Swal.fire({
        title: '{{ translate('Are you sure?') }}' ,
        text: "{{ translate('You_want_to_') }}" +data + " {{ translate('Data.') }}",
        type: 'warning',
        showCancelButton: true,
        cancelButtonColor: 'default',
        confirmButtonColor: '#FC6A57',
        cancelButtonText: '{{translate('messages.no')}}',
        confirmButtonText: '{{translate('messages.yes')}}',
        reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $('#btn_value').val(data);
                $("#import_form").submit();
            }
        })
    }
        </script>
@endpush
