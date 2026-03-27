@extends('layouts.admin.app')

@section('title',translate('Food Bulk Export'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/items.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('messages.items_bulk_export')}}
                </span>
            </h1>
        </div>
        <div class="card mt-2 rest-part">
            <div class="card-body">
                <div class="export-steps-2">
                    <div class="row g-4">
                        <div class="col-sm-6 col-lg-4">
                            <div class="export-steps-item-2 h-100">
                                <div class="top">
                                    <div>
                                        <h3 class="fs-20">{{translate('Step 1')}}</h3>
                                        <div>
                                            {{translate('Select Data Type')}}
                                        </div>
                                    </div>
                                    <img src="{{asset('/public/assets/admin/img/bulk-export-1.png')}}" alt="">
                                </div>
                                <h4>{{ translate('Instruction') }}</h4>
                                <ul class="m-0 pl-4">
                                    <li>
                                       {{ translate('Select_data_type_in_which_order_you_want_your_data_sorted_while_downloading.') }}
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
                                            {{translate('Select Data Range by Date or ID and Export')}}
                                        </div>
                                    </div>
                                    <img src="{{asset('/public/assets/admin/img/bulk-export-2.png')}}" alt="">
                                </div>
                                <h4>{{ translate('Instruction') }}</h4>
                                <ul class="m-0 pl-4">

                                    <li>
                                        {{ translate('The_file_will_be_downloaded_in_.xls_format') }}
                                    </li>
                                    <li>
                                        {{ translate('Click_reset_if_you_want_to_clear_you_changes_and_want_to_download_in_default_sort_wise_data') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <form class="product-form" action="{{route('admin.item.bulk-export')}}" method="POST"
                        enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.type')}}<span
                                        class="input-label-secondary"></span></label>
                                <select name="type" id="type" data-placeholder="{{translate('messages.select_type')}}" class="form-control" required title="Select Type">
                                    <option value="all">{{translate('messages.all_data')}}</option>
                                    <option value="date_wise">{{translate('messages.date_wise')}}</option>
                                    <option value="id_wise">{{translate('messages.id_wise')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group id_wise">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.start_id')}}<span
                                        class="input-label-secondary"></span></label>
                                <input type="number" name="start_id" class="form-control">
                            </div>
                            <div class="form-group date_wise">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.from_date')}}<span
                                        class="input-label-secondary"></span></label>
                                <input type="date" name="from_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group id_wise">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.end_id')}}<span
                                        class="input-label-secondary"></span></label>
                                <input type="number" name="end_id" class="form-control">
                            </div>
                            <div class="form-group date_wise">
                                <label class="input-label text-capitalize" for="exampleFormControlSelect1">{{translate('messages.to_date')}}<span
                                        class="input-label-secondary"></span></label>
                                <input type="date" name="to_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="btn--container justify-content-end">
                                <button type="reset" id="reset-btn" class="btn btn--reset">{{translate('clear')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('export')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
<script>
    "use strict";
    $(document).on('ready', function (){
        $('.id_wise').hide();
        $('.date_wise').hide();
        $('#type').on('change', function()
        {
            $('.id_wise').hide();
            $('.date_wise').hide();
            $('.'+$(this).val()).show();
        })
        $('#reset-btn').on('click', function()
        {
            $('.id_wise').hide();
            $('.date_wise').hide();
        })
    });
</script>
@endpush
