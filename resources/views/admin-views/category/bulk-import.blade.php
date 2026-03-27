@extends('layouts.admin.app')

@section('title',translate('messages.category_bulk_import'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/category.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('messages.category_bulk_import')}}
                </span>
            </h1>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="export-steps-2">
                    <div class="row g-4">
                        <div class="col-sm-6 col-lg-4">
                            <div class="export-steps-item-2 h-100">
                                <div class="top">
                                    <div>
                                        <h3 class="fs-20">{{translate('Step_1')}}</h3>
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
                                        <h3 class="fs-20">{{translate('Step_2')}}</h3>
                                        <div>
                                            {{translate('Match_Spread_sheet_data_according_to_instruction')}}
                                        </div>
                                    </div>
                                    <img src="{{asset('/public/assets/admin/img/bulk-import-2.png')}}" alt="">
                                </div>
                                <h4>{{ translate('Instruction') }}</h4>
                                <ul class="m-0 pl-4">
                                    <li>
                                        {{ translate('Fill_up_the_data_according_to_the_format') }}
                                    </li>
                                    <li>
                                        {{ translate('For_parent_category_"position"_will_0_and_for_sub_category_it_will_be_1')}}
                                    </li>
                                    <li>
                                        {{ translate('By_default_status_will_be_1,_please_input_the_right_ids') }}
                                    </li>
                                    <li>
                                        {{ translate('For_a_category_parent_id_will_be_empty,_for_sub_category_it_will_be_the_category_id') }}
                                    </li>
                                    <li>
                                        {{ translate('For_a_sub_category_module_id_will_it`s_parents_module_id') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="export-steps-item-2 h-100">
                                <div class="top">
                                    <div>
                                        <h3 class="fs-20">{{translate('Step_3')}}</h3>
                                        <div>
                                            {{translate('Validate_data_and_complete_import')}}
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
                                        {{ translate('You_can_upload_your_category_images_in_category_folder_from_gallery_and_copy_image`s_path.') }}
                                     </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center pb-4">
                    <h3 class="mb-3 export--template-title font-regular">{{translate('download_spreadsheet_template')}}</h3>
                    <div class="btn--container justify-content-center export--template-btns">

                        <a href="{{asset('public/assets/categories_bulk_format.xlsx')}}" download="" class="btn btn--primary btn-outline-primary">{{ translate('Template with Existing Data') }}</a>
                        <a href="{{asset('public/assets/categories_bulk_without_data_format.xlsx')}}" download="" class="btn btn--primary">{{ translate('Template without Data') }}</a>

                    </div>
                </div>
            </div>
        </div>


        <form class="product-form" id="import_form" action="{{route('admin.category.bulk-import')}}" method="POST"
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
                                <h5 class="text-capitalize mb-3">{{ translate('Import_Category_file') }}</h5>
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
    </div>
@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin')}}/js/view-pages/category-import-export.js"></script>
<script>
    "use strict";
    $('#reset_btn').click(function(){
    $('#products_file').val('');
    $('.filename').text('{{translate('Must_be_Excel_files_using_our_Excel_template_above')}}');
})
    function myFunction(data) {
    Swal.fire({
    title: '{{ translate('Are you sure?') }}' ,
    text: "{{ translate('You_want_to_') }}" +data,
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
        // else {
        //     toastr.success("{{ translate('Cancelled') }}");
        // }
    })
}
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


$(document).on("click", ".update_or_import", function(e){
    e.preventDefault();
    let upload_type = $('input[name="upload_type"]:checked').val();
    myFunction(upload_type)
});



    $('.update_or_import').on('click', function (){
        let buttonValue = $('input[name="upload_type"]:checked').val();
        changeFormAction(buttonValue);
    })

    function changeFormAction(buttonValue) {
        var form = document.getElementById('import_form');
        if (buttonValue === 'update') {
            form.action = '{{ route('admin.category.bulk-update') }}';
        } else {
            form.action = '{{ route('admin.category.bulk-import') }}';
        }
    }
</script>
@endpush

