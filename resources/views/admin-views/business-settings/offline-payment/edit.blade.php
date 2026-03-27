@extends('layouts.admin.app')
@section('title', translate('edit_Offline_Payment_Method'))

@push('css_or_js')

@endpush

@section('content')
<div class="content">
    <form action="{{ route('admin.business-settings.offline.update') }}" method="POST">
        @csrf
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                <div>
                    <h2 class="h1 mb-1 text-capitalize">
                        {{translate('Edit_Offline_Payment_Method')}}
                    </h2>
                    <h6 class="text-info fs-12 d-flex gap-2 align-items-center mb-0">
                        <i class="tio-back-ui fs-10"></i>
                        <a style="color: #245BD1;" href="{{ route('admin.business-settings.offline') }}">{{ translate('messages.Back to Offline Payment Mathods') }}</a>
                    </h6>
                </div>
                <button type="button" class="btn btn--primary btn-outline-primary d-flex gap-2 align-items-center offcanvas-trigger" id="bkashInfoModalButton">
                    <i class="tio-invisible"></i>
                    {{ translate('Section_View') }}
                </button>
            </div>
            <!-- End Page Title -->
    
            <div class="card card-body mb-20">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-20">
                    <div class="">
                        <h3 class="mb-1">{{translate('payment_information')}}</h3>
                        <p class="fs-12 mb-0">
                            {{ translate('messages.Configure the payment methods your customers will use to pay for their orders.') }}
                        </p>
                    </div>
                    <button class="btn btn--primary add-input-fields-group">
                        <i class="tio-add-circle"></i> {{ translate('Add_New_Field') }}
                    </button>
                </div>
                 <div class="__bg-F8F9FC-card mb-20">
                    <label for="method_name" class="input-label text-capitalize d-flex gap-1 align-items-center">
                        {{ translate('messages.payment_Method_Name') }}
                        <span class="tio-info text-light-gray fs-16" data-toggle="tooltip"
                            data-placement="right"
                            data-original-title="{{ translate('Specify the payment method name as it will appear in the system') }}">
                            </span>
                    </label>
                    <input id="method_name" type="text" class="form-control" placeholder="{{ translate('ex') }}: {{ translate('bkash') }}" name="method_name" required value="{{ $data->method_name }}">
                </div>
                <div>
    
                    <input type="hidden" name="id" value="{{ $data->id }}">
    
                    <div class="d-flex flex-column gap-3 input-fields-section" id="input-fields-section">
                        @foreach ($data->method_fields as $key=>$item)
                            @php($aRandomNumber = rand())
                            <div class="__bg-F8F9FC-card field-row-payment" id="{{ $aRandomNumber }}">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label for="input_name" class="input-label">{{ translate('Input field Name') }}</label>
                                            <input id="input_name" type="text" name="input_name[]" class="form-control" placeholder="{{ translate('Ex: Account Number') }}" required value="{{ ucwords(str_replace('_',' ',$item['input_name'])) }} ">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label for="input_data" class="input-label">{{ translate('Input field Data') }}</label>
                                            <input id="input_data" type="text" name="input_data[]" class="form-control" placeholder="{{ translate('Ex: 1235 5648 2314') }}" required value="{{ $item['input_data'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <div class="d-flex justify-content-end">
                                                <a href="javascript:" class="btn action-btn btn-danger remove-input-fields-group" data-id="{{ $aRandomNumber }}" title="Delete" >
                                                <i class="tio-delete-outlined"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
    
            <div class="card card-body">
                 <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-20">
                    <div class="">
                        <h3 class="mb-1">{{translate('required_Information_from_Customer')}}</h3>
                        <p class="fs-12 mb-0">
                            {{ translate('messages.Configure the payment methods your customers will use to pay for their orders.') }}
                        </p>
                    </div>
                    <button class="btn btn--primary add-customer-input-fields-group">
                        <i class="tio-add-circle"></i> {{ translate('Add_New_Field') }}
                    </button>
                </div>
                <div>
                    <div class="d-flex flex-column gap-3 customer-input-fields-section" id="customer-input-fields-section">
                        @foreach ($data->method_informations as $key=>$item)
                            @php($cRandomNumber = rand())
                            <div class="__bg-F8F9FC-card field-row-customer" id="{{ $cRandomNumber }}">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label for="customer_input" class="input-label">{{ translate('input_field_Name') }}</label>
                                            <input id="customer_input" type="text" name="customer_input[]" class="form-control" placeholder="{{ translate('ex') }}: {{ translate('payment_By') }}" required value="{{ ucwords(str_replace('_',' ',$item['customer_input'])) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label for="customer_placeholder" class="input-label">{{ translate('place_Holder') }}</label>
                                            <input id="customer_placeholder" type="text" name="customer_placeholder[]" class="form-control" placeholder="{{ translate('ex') }}: {{ translate('enter_name') }}" required value="{{ $item['customer_placeholder'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-0 h-100">
                                            <div class="d-flex justify-content-between gap-2 h-100">
                                                <div class="form-check text-start mb-3 align-content-end">
        
                                                    <label class="form-check-label text-dark" for="{{ $cRandomNumber+1 }}">
                                                        <input type="checkbox" class="form-check-input" id="{{ $cRandomNumber+1 }}" name="is_required[]" {{ (isset($item['is_required']) && $item['is_required']) == 1 ? 'checked':'' }}> {{ translate('is_Required') }} ?
                                                    </label>
                                                </div>
        
                                                <a class="btn action-btn btn-danger  remove-input-fields-group" data-id="{{ $cRandomNumber }}" title="Delete" >
                                                        <i class="tio-delete-outlined"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-sticky mt-2">
            <div class="container-fluid">
                <div class="d-flex flex-wrap gap-3 justify-content-center py-3">
                    <button type="reset" class="btn btn--reset min-w-120">{{ translate('Reset') }}</button>
                    <button type="submit"  class="btn btn--primary">
                        <i class="tio-save"></i>
                        {{ translate('Save_Information') }}</button>
                </div>
            </div>
        </div>
    </form>

</div>

{{-- Section View Offcanvas --}}
<div id="sectionViewModal" class="custom-offcanvas d-flex flex-column justify-content-between">
    <div>
        <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
            <div class="py-1">
                <h3 class="mb-0">{{ translate('messages.Section_View') }}</h3>
            </div>
            <button type="button" class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"aria-label="Close">
                &times;
            </button>
        </div>
        <div class="custom-offcanvas-body custom-offcanvas-body-100 p-20">
            <div class=""style="pointer-events: none;">
                <div class="d-flex align-items-center flex-column gap-2 text-center">
                    <img width="68" src="{{asset('public/assets/admin/img/offline_payment-new.png')}}" alt="">
                    <p class="fs-12 text-title mb-0">
                            {{ translate('messages.Pay your bill using any of the payment method below')}} <br> {{ translate('messages.and input the required information.') }}
                        </p>
                    <h5 class="font-medium mb-0">
                        {{translate('messages.Amount')}} : xxx
                    </h5>
                </div>
                <div class="card card-body mt-20 mb-20 overflow-wrap-anywhere" id="offline_payment_top_part">
                    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-3">
                        <h5 class="font-medium mb-0" id="payment_modal_method_name"><span></span></h5>
                        <div class="fs-12 text--primary bg--primary bg-opacity-5 rounded px-2 py-1 d-flex align-items-center gap-2">
                            {{translate('messages.Pay on this account')}}
                            <i class="tio-checkmark-circle"></i>
                        </div>
                    </div>

                    <div class="d-flex text-wrap flex-column gap-2" id="methodNameDisplay"> </div>
                    <div class="d-flex text-wrap flex-column gap-2" id="displayDataDiv"> </div>
                </div>
                <h5 class="font-medium mb-2">{{ translate('messages.Payment Info') }}</h5>

                <div class="__bg-F8F9FC-card mb-3">
                    <div class="d-flex flex-column gap-3 mb-3 overflow-wrap-anywhere" id="customer-info-display-div">

                    </div>
                    <div class="d-flex flex-column gap-3">
                        <textarea name="payment_note" id="payment_note" class="form-control bg-white"
                            readonly rows="5" placeholder="Note"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="offcanvasOverlay" class="offcanvas-overlay"></div>
@endsection


@push('script_2')
<script src="{{asset('public/assets/admin/js/view-pages/offline-payment.js')}}"></script>

<script>
    "use strict"
    $(document).on('click', '.add-input-fields-group', function () {
        let id = Math.floor((Math.random() + 1 )* 9999);
        let new_field = `<div class="__bg-F8F9FC-card field-row-payment" id="`+id+`" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="input_name" class="input-label">{{ translate('Input field Name') }}</label>
                                        <input type="text" name="input_name[]" class="form-control" placeholder="{{ translate('Ex: Account Number') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="input_data" class="input-label">{{ translate('Input field Data') }}</label>
                                        <input type="text" name="input_data[]" class="form-control" placeholder="{{ translate('Ex: 1235 5648 2314') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <div class="d-flex justify-content-end">
                                            <a href="javascript:" class="btn action-btn btn-danger remove-input-fields-group" data-id="`+id+`" title="Delete" >
                                                 <i class="tio-delete-outlined"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;

        $('#input-fields-section').append(new_field);
        $('#'+id).fadeIn();
    });

    $(document).on('click', '.add-customer-input-fields-group', function () {

        let id = Math.floor((Math.random() + 1 )* 9999);
        let new_field = `<div class="__bg-F8F9FC-card field-row-customer" id="`+id+`" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label">{{ translate('Input field Name') }}</label>
                                        <input type="text" name="customer_input[]" class="form-control" placeholder="{{ translate('ex') }}: {{ translate('payment_By') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="customer_placeholder" class="input-label">{{ translate('Place Holder') }}</label>
                                        <input type="text" name="customer_placeholder[]" class="form-control" placeholder="{{ translate('ex') }}: {{ translate('Enter Name') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0 h-100">
                                        <div class="d-flex justify-content-between gap-2 h-100">
                                            <div class="form-check text-start mb-3 align-content-end">
    
                                                <label class="form-check-label text-dark" for="`+id+1+`">
                                                    <input type="checkbox" class="form-check-input" id="`+id+1+`" name="is_required[]"> {{ translate('is_Required') }} ?
                                                </label>
                                            </div>
    
                                            <a class="btn action-btn btn-danger remove-input-fields-group" data-id="`+id+`" title="Delete" >
                                                 <i class="tio-delete-outlined"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;

        $('#customer-input-fields-section').append(new_field);
        $('#'+id).fadeIn();
    });

    $(document).on('click', '.remove-input-fields-group', function () {
        $('#'+$(this).data('id')).remove();

    });

</script>
@endpush
