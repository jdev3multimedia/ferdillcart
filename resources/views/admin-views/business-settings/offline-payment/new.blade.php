@extends('layouts.admin.app')
@section('title', translate('add_Offline_Payment_Method'))

@push('css_or_js')

@endpush

@section('content')
    <!-- Main Content -->

    <div class="content">
        <form action="{{ route('admin.business-settings.offline.store') }}" method="POST">
            @csrf
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                    <div>
                        <h2 class="h1 mb-1 text-capitalize">
                            {{translate('Add_Offline_Payment_Method')}}
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
                <div class="card card-body mb-20">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-20">
                        <div class="">
                            <h3 class="mb-1">{{translate('payment_information')}}</h3>
                            <p class="fs-12 mb-0">
                                {{ translate('messages.Configure the payment methods your customers will use to pay for their orders.') }}
                            </p>
                        </div>
                        <button class="btn btn--primary" id="add-more-field-payment">
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
                        <input type="text" class="form-control text-break" id="method_name" placeholder="{{ translate('ex:_bkash') }}" name="method_name" required>
                    </div>
                    <div class="d-flex flex-column gap-3" id="custom-field-section-payment"></div>
                </div>
                <div class="card card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-20">
                        <div class="">
                            <h3 class="mb-1">{{translate('messages.Information Required From Customer')}}</h3>
                            <p class="fs-12 mb-0">
                                {{ translate('messages.Specify the data you need from customers when they choose this offline payment method.') }}
                            </p>
                        </div>
                        <button class="btn btn--primary" id="add-more-field-customer">
                            <i class="tio-add-circle"></i> {{ translate('Add_New_Field') }}
                        </button>
                    </div>
                    <div class="customer-input-fields-section d-flex flex-column gap-3" id="custom-field-section-customer"></div>
                    <div class="__bg-F8F9FC-card mb-20">
                        <label for="payment_note" class="input-label text-capitalize d-flex gap-1 align-items-center">{{translate('Payment Note')}} </label>
                        <div class="form-floating">
                            <textarea class="form-control" name="payment_note" id="payment_note"
                                placeholder="{{ translate('Ex: Miler') }}"  disabled></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-sticky mt-2">
                <div class="container-fluid">
                    <div class="d-flex flex-wrap gap-3 justify-content-center py-3">
                        <button type="reset" class="btn btn--reset min-w-120">{{ translate('Reset') }}</button>
                        <button type="submit"  class="btn btn--primary demo_check">
                            <i class="tio-save"></i>
                            {{ translate('Save_Information') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- End Main Content -->

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
                <div class="" style="pointer-events: none;">
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
        "use strict";
        jQuery(document).ready(function ($) {
            let counter = 0;
            let counterPayment = 0;

            $('#add-more-field-customer').on('click', function (event) {
                if(counter < 14) {
                    event.preventDefault();

                    $('#custom-field-section-customer').append(
                        `<div id="field-row-customer--${counter}" class="__bg-F8F9FC-card field-row-customer">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label>{{translate('Input field Name')}}</label>
                                        <input type="text" class="form-control" name="customer_input[${counter}]"
                                        placeholder="{{ translate('ex') }}: {{ translate('payment_By') }}" value="" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label>{{translate('Placeholder')}}</label>
                                        <input type="text" class="form-control" name="customer_placeholder[${counter}]"
                                        placeholder="{{ translate('ex') }}: {{ translate('Enter Name') }}" value="" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0 h-100">
                                        <div class="d-flex justify-content-between gap-2 h-100">
                                            <div class="form-check text-start mb-3 align-content-end">
                                            <input class="form-check-input" type="checkbox" value="1" name="is_required[${counter}]" id="flexCheckDefault__${counter}" checked>
                                            <label class="form-check-label" for="flexCheckDefault__${counter}">
                                                {{translate('is_required_?')}}
                                            </label>
                                        </div>
                                        <span class="btn action-btn btn-danger remove-field"  data-id="${counter}" style="cursor: pointer;">
                                            <i class="tio-delete-outlined"></i>
                                        </span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>`
                    );

                    $(".js-select").select2();

                    counter++;
                } else {
                    Swal.fire({
                        title: '{{translate('Reached maximum')}}',
                        confirmButtonText: '{{translate('ok')}}',
                    });
                }
            })

            $('#add-more-field-payment').on('click', function (event) {
                if(counterPayment < 14) {
                    event.preventDefault();

                    $('#custom-field-section-payment').append(
                        `<div id="field-row-payment--${counterPayment}" class="__bg-F8F9FC-card field-row-payment">
                            <div class="row g-3">
                                <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="input-label">{{ translate('Input field Name') }}</label>
                                    <input type="text" name="input_name[]" class="form-control" placeholder="{{ translate('Ex: Account Number') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label for="input_data" class="input-label">{{ translate('Input data') }}</label>
                                    <input type="text" name="input_data[]" class="form-control" placeholder="{{ translate('Ex: 1235 5648 2314') }}" required>
                                </div>
                            </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                    <div class="d-flex justify-content-end">
                                        <span class="btn action-btn btn-danger remove-field-payment" data-id="${counterPayment}"  style="cursor: pointer;">
                                            <i class="tio-delete-outlined"></i>
                                        </span>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>`
                    );

                    $(".js-select").select2();

                    counterPayment++;
                } else {
                    Swal.fire({
                        title: '{{translate('Reached maximum')}}',
                        confirmButtonText: '{{translate('ok')}}',
                    });
                }
            })

            $('form').on('reset', function () {
                if(counter > 1) {
                    $('#custom-field-section-payment').html("");
                    $('#custom-field-section-customer').html("");
                    $('#method_name').val("");
                    $('#payment_note').val("");
                }

                counter = 1;
            })

            $(document).on('click', '.remove-field-payment', function () {
                let fieldRowId=  $(this).data('id');
                $( `#field-row-payment--${fieldRowId}` ).remove();
                counterPayment--;

            });
            $(document).on('click', '.remove-field', function () {
                let fieldRowId=  $(this).data('id');
                $( `#field-row-customer--${fieldRowId}` ).remove();
                counter--;

            });
        });

    </script>


@endpush
