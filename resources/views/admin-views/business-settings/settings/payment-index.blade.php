@extends('layouts.admin.app')

@section('title',translate('messages.Business  Setup'))

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title mr-3">
            <span class="page-header-icon">
                <img src="{{ asset('public/assets/admin/img/business.png') }}" class="w--26" alt="">
            </span>
            <span>
                {{ translate('messages.business_settings') }}
            </span>
        </h1>
        @include('admin-views.business-settings.partials.nav-menu')
    </div>
    <!-- End Page Header -->

    <div class="card">
        <form action="{{ route('admin.business-settings.update-payment-setup') }}" method="post">
            @csrf
            <div class="card-header border-0 d-block" id="payment_options_section">
                <h4 class="mb-1 text-title">{{ translate('Payment Options') }}</h4>
                <p class="fs-12 m-0 color-758590">
                    {{ translate('Setup your business payment options from here') }}
                </p>
            </div>
            <div class="card-body">
                <div class="bg-light2 rounded p-xxl-20 p-3 mb-20">
                    <div class="bg-white rounded p-3 border">
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group m-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="CashOn_delivery" value="1"
                                            name="cash_on_delivery" {{ $cash_on_delivery_status ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="CashOn_delivery">
                                            <h5 class="mb-1">{{ translate('Cash On Delivery') }}</h5>
                                            <p class="mb-0 fs-12">
                                                {{ translate('Let your customers pay when they receive their orders. A convenient option for those who prefer to pay with cash.') }}
                                            </p>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group m-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="digital_payment" value="1"
                                            name="digital_payment" {{ $digital_payment_status ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="digital_payment">
                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                <h5 class="m-0">{{ translate('Digital Payment') }}</h5>
                                                @if($digital_payment_methods_count == 0)
                                                    <i class="tio-warning text-warning"></i>
                                                @endif
                                            </div>
                                            <p class="mb-0 fs-12">
                                                {{ translate('Enable customers to pay instantly using online payment gateways. To activate, please configure your payment gateway settings.') }}
                                            </p>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group m-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="Offline_payment" value="1"
                                            name="offline_payment" {{ $offline_payment_status ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="Offline_payment">
                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                <h5 class="m-0">{{ translate('Offline payment') }}</h5>
                                                @if($offline_payment_methods_count == 0)
                                                    <i class="tio-warning text-warning"></i>
                                                @endif
                                            </div>
                                            <p class="mb-0 fs-12">
                                                {{ translate('Let customers complete payment outside the system. After placing the order, they will upload the payment proof for verification by the admin.') }}
                                            </p>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-light rounded p-xxl-20 p-3">
                    <div class="fs-12 text-dark px-3 py-2 rounded bg-warning-10 mb-20">
                        <div class="d-flex gap-2 ">
                            <span class="text-warning lh-1 fs-14">
                                <i class="tio-info"></i>
                            </span>
                            <span>
                                {{ translate('To enable this feature, the following must be activated') }}
                            </span>
                        </div>
                        <ul class="mb-0">
                            <li>
                                {{ translate('Customer Wallet from the') }} <a target="_blank" rel="noopener noreferrer"
                                    style="text-decoration: underline;color: info;"
                                    href="{{ route('admin.business-settings.business-setup', ['tab' => 'customer']) }}#customer-wallet"
                                    class="font-semibold text-primary">{{ translate('Customer Wallet') }}</a>
                                {{ translate('page.') }}
                            </li>
                            <li>
                                {{ translate('At least one payment method from the payment options above') }}
                            </li>
                        </ul>
                    </div>
                    <div class="row g-3 align-items-center">
                        <div class="col-xxl-9 col-lg-8 col-md-7 col-sm-6">
                            <div>
                                <h4 class="mb-1" id="combined_payment_section">
                                    {{ translate('Allow Combined Payment') }}
                                </h4>
                                <p class="mb-0 fs-12">
                                    {{ translate('This feature enables customers to partially pay with their wallet balance and complete the payment using other available payment methods.') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-lg-4 col-md-5 col-sm-6">
                            @php($partial_payment = \App\Models\BusinessSetting::where('key', 'partial_payment_status')->first())
                            @php($partial_payment = $partial_payment ? $partial_payment->value : 0)
                            <div class="form-group mb-0">
                                <label
                                    class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                    <span class="pr-1 d-flex align-items-center switch--label">
                                        <span class="line--limit-1">
                                            {{ translate('messages.Status') }}
                                        </span>
                                    </span>
                                    <input type="checkbox" data-id="partial_payment" data-type="toggle"
                                        data-image-on="{{ asset('/public/assets/admin/img/modal/payment_on.png') }}"
                                        data-image-off="{{ asset('/public/assets/admin/img/modal/payment_off.png') }}"
                                        data-title-on="{{ translate('Are you sure turn on') }} <strong>{{ translate('Combined Payment?') }}</strong>"
                                        data-title-off="{{ translate('Are you sure turn off') }} <strong>{{ translate('Combined Payment?') }}</strong>"
                                        data-text-on="<p>{{ translate('Enabling partial payment will allow customers to pay partially.') }}</p>"
                                        data-text-off="<p>{{ translate('Disabling this feature will require customers to pay the full amount at checkout.') }}</p>"
                                        class="status toggle-switch-input dynamic-checkbox-toggle" value="1"
                                        name="partial_payment_status" id="partial_payment" {{ $partial_payment == 1 ? 'checked' : '' }}>
                                    <span class="toggle-switch-label text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="partial_payment-billbox">
                        @php($partial_payment_method = \App\Models\BusinessSetting::where('key', 'partial_payment_method')->first())
                        <div class="form-group mb-0 mt-20">
                            <label class="input-label text-capitalize d-flex alig-items-center"><span
                                    class="line--limit-1 font-weight-normal">{{ translate('Available Option to pay the remaining bill') }}
                                    <span class="text-danger">*</span>
                                    <span class="form-label-secondary" data-toggle="tooltip" data-placement="right"
                                        data-original-title="{{ translate('messages.Set_the_method(s)_that_customers_can_pay_the_remainder_after_partial_payment.') }}">
                                        <i class="tio-info text-muted"></i>
                                    </span>
                                </span>
                            </label>
                            <div class="py-2 px-3 rounded min-h-45px border bg-white">
                                <div class="row g-1">
                                    <div class="col-sm-6 col-md-4 col-xl-3">
                                        <label
                                            class="custom_checkbox d-flex align-items-center gap-1 flex-grow-1 m-0 pt-2px">
                                            <input type="checkbox" value="cod" id="partial_payment_cod"
                                                name="partial_payment_method[]" {{ $partial_payment_method ? ($partial_payment_method->value == 'cod' || $partial_payment_method->value == 'both' ? 'checked' : '') : '' }}>
                                            <span class="label-text">
                                                {{translate('Cash on Delivery (COD)')}}
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-sm-6 col-md-4 col-xl-3">
                                        <label
                                            class="custom_checkbox d-flex align-items-center gap-1 flex-grow-1 m-0 pt-2px">
                                            <input type="checkbox" value="digital_payment" id="partial_payment_digital"
                                                name="partial_payment_method[]" {{ $partial_payment_method ? ($partial_payment_method->value == 'digital_payment' || $partial_payment_method->value == 'both' ? 'checked' : '') : '' }}>
                                            <span class="label-text">
                                                {{translate('Digital Payment')}}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="fs-12 text-dark px-3 py-2 rounded bg-danger-10 mt-20" id="partial_payment_warning"
                        style="display:none;">
                        <div class="d-flex gap-2 ">
                            <span class="text-danger lh-1 fs-14">
                                <i class="tio-warning text-danger"></i>
                            </span>
                            <span>
                                {{ translate('Here') }} <strong id="warning_payment_methods"></strong>
                                {{ translate('is disable because this is not activated in the') }}
                                <strong>{{ translate('Payment Option setup') }}</strong>
                            </span>
                        </div>
                    </div>
                </div>

                @include('admin-views.partials._floating-submit-button')
            </div>
        </form>
    </div>
</div>

<div id="global_guideline_offcanvas"
    class="custom-offcanvas d-flex flex-column justify-content-between global_guideline_offcanvas">
    <div>
        <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
            <h3 class="mb-0">{{ translate('Payment Settings Guideline') }}</h3>
            <button type="button"
                class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary offcanvas-close fz-15px p-0"
                aria-label="Close">&times;</button>
        </div>

            <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                    <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed"
                        type="button" data-toggle="collapse" data-target="#payment_options_guide" aria-expanded="true">
                        <div
                            class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                            <i class="tio-down-ui"></i>
                        </div>
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Payment Options') }}</span>
                    </button>
                    <a href="#payment_options_section"
                        class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3 show" id="payment_options_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Payment Options') }}</h5>
                            <p class="fs-12 mb-0">
                                {{ translate('6ammart supports multiple payment methods to provide flexibility and convenience for customers while ensuring smooth transaction management for the platform and vendors.') }}
                            </p>
                            <ul class="fs-12">
                                <li><strong>{{ translate('Cash On Delivery') }}:</strong> {{ translate('Customers pay in cash when they receive their order.') }}</li>
                                <li><strong>{{ translate('Digital Payment') }}:</strong> {{ translate('Customers pay online using cards, mobile banking, or digital wallets. Payment is completed before the order is confirmed.') }}</li>
                                <li><strong>{{ translate('Offline Payment') }}:</strong> {{ translate('Customers place an order using manual payment methods such as bank transfer or mobile banking. Customers must provide a payment reference or proof. Admin or vendor approval may be required before order confirmation.') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                    <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed"
                        type="button" data-toggle="collapse" data-target="#combined_payment_guide" aria-expanded="true">
                        <div
                            class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                            <i class="tio-down-ui"></i>
                        </div>
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Combined Payment') }}</span>
                    </button>
                    <a href="#combined_payment_section"
                        class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3" id="combined_payment_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Combined Payment') }}</h5>
                            <p class="fs-12 mb-0">
                                {{ translate('The Partial Payment feature allows customers to split an order payment into two parts. An initial amount is paid using the customer’s wallet balance, and the remaining amount can be paid using Cash on Delivery (COD) or Digital Payment. This feature provides greater payment flexibility and helps customers place orders even when their wallet balance is insufficient for the full amount.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
<div id="offcanvasOverlay" class="offcanvas-overlay"></div>

@endsection

@push('script_2')
    <script>
        $(document).ready(function () {
            $('#CashOn_delivery, #digital_payment, #Offline_payment').on('change', function() {
                if (!$('#CashOn_delivery').is(':checked') && !$('#digital_payment').is(':checked') && !$('#Offline_payment').is(':checked')) {
                    toastr.error('{{ translate("At least one payment option must be selected") }}');
                    $(this).prop('checked', true);
                }
            });

            const partialPaymentBillbox = $('.partial_payment-billbox');

            function togglePartialPaymentBillbox() {
                if ($('#partial_payment').is(':checked')) {
                    partialPaymentBillbox.show();
                } else {
                    partialPaymentBillbox.hide();
                }
            }

            togglePartialPaymentBillbox();

            $(document).on('change', '#partial_payment', function () {
                togglePartialPaymentBillbox();
            });

            // Listen for modal confirmation (since common.js prevents default change)
            $(document).on('click', '.confirm-Toggle', function() {
                let toggle_id = $("#toggle-ok-button").attr("toggle-ok-button");
                if (toggle_id === 'partial_payment') {
                    setTimeout(function() {
                        togglePartialPaymentBillbox();
                    }, 0);
                }
            });

            function checkPaymentMethodDependency() {
                let codChecked = $('#CashOn_delivery').is(':checked');
                let digitalChecked = $('#digital_payment').is(':checked');
                let warningMethods = [];

                if (codChecked) {
                    $('#partial_payment_cod').prop('disabled', false);
                } else {
                    $('#partial_payment_cod').prop('disabled', true).prop('checked', false);
                    warningMethods.push('{{ translate("Cash on Delivery(COD)") }}');
                }

                if (digitalChecked) {
                    $('#partial_payment_digital').prop('disabled', false);
                } else {
                    $('#partial_payment_digital').prop('disabled', true).prop('checked', false);
                    warningMethods.push('{{ translate("Digital Payment") }}');
                }

                if (warningMethods.length > 0) {
                    $('#warning_payment_methods').text(warningMethods.join(' & '));
                    $('#partial_payment_warning').show();
                } else {
                    $('#partial_payment_warning').hide();
                }
            }

            checkPaymentMethodDependency();

            $('#CashOn_delivery, #digital_payment').on('change', function() {
                checkPaymentMethodDependency();
            });

            // Partial Payment Method Validation
            $('#partial_payment_cod, #partial_payment_digital').on('change', function () {
                if ($('#partial_payment').is(':checked')) {
                    if (!$('#partial_payment_cod').is(':checked') && !$('#partial_payment_digital').is(':checked')) {
                        toastr.error('{{ translate("At least one partial payment option must be selected") }}');
                        $(this).prop('checked', true);
                    }
                }
            });
        });
    </script>
@endpush