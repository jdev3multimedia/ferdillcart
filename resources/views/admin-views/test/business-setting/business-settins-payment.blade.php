@extends('layouts.admin.app')

@section('title',translate('messages.Business  Setup'))

@section('content')
<div class="content container-fluid">
   
    <div class="card">
            <form action="#0">
            <div class="card-header border-0">
                <h4 class="mb-1 text-title">{{ translate('Payment Options') }}</h4>
                <p class="fs-12 m-0 color-758590">
                    {{ translate('Setup your business time zone and format from here') }}
                </p>
            </div>
            <div class="card-body">
                <div class="bg-light2 rounded p-xxl-20 p-3 mb-20">
                    <div class="bg-white rounded p-3 border">
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group m-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="CashOn_delivery" name="">
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
                                        <input type="checkbox" class="custom-control-input" id="digital_payment" name="">
                                        <label class="custom-control-label" for="digital_payment">
                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                <h5 class="m-0">{{ translate('Digital Payment') }}</h5>
                                                <i class="tio-warning text-warning"></i>
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
                                        <input type="checkbox" class="custom-control-input" id="Offline_payment" name="">
                                        <label class="custom-control-label" for="Offline_payment">
                                            <h5 class="mb-1">{{ translate('Offline payment') }}</h5>
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
                                {{ translate('Customer Wallet from the') }} <a href="#" class="font-semibold text-primary">{{ translate('Customer Wallet') }}</a>  {{ translate('page.') }}
                            </li>
                            <li>
                                {{ translate('messages.At least one payment method from the payment options above') }} 
                            </li>
                        </ul>
                    </div>
                    <div class="row g-3 align-items-center">
                        <div class="col-xxl-9 col-lg-8 col-md-7 col-sm-6">
                            <div>
                                <h4 class="mb-1">
                                    {{ translate('Partial Payment') }}
                                </h4>
                                <p class="mb-0 fs-12">
                                    {{ translate('By switching this feature ON, Customer can pay with wallet balance & partially pay from other payment gateways. ') }}
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
                                        data-image-on="{{ asset('/public/assets/admin/img/modal/schedule-on.png') }}"
                                        data-image-off="{{ asset('/public/assets/admin/img/modal/schedule-off.png') }}"
                                        data-title-on="{{ translate('messages.Want_to_enable') }} <strong>{{ translate('messages.partial_payment_?') }}</strong>"
                                        data-title-off="{{ translate('messages.Want_to_disable') }} <strong>{{ translate('messages.partial_payment_?') }}</strong>"
                                        data-text-on="<p>{{ translate('messages.If_you_enable_this,_customers_can_choose_partial_payment_during_checkout.') }}</p>"
                                        data-text-off="<p>{{ translate('messages.If_you_disable_this,_the_partial_payment_feature_will_be_hidden.') }}</p>"
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
                                    <span class="form-label-secondary" data-toggle="tooltip"
                                    data-placement="right"
                                    data-original-title="{{ translate('messages.Set_the_method(s)_that_customers_can_pay_the_remainder_after_partial_payment.') }}">
                                    <i class="tio-info text-muted"></i>
                                </span>
                                </span>
                            </label>
                            <div class="py-2 px-3 rounded min-h-45px border bg-white">
                                <div class="row g-1">
                                    <div class="col-sm-6 col-md-4 col-xl-3">
                                        <label class="custom_checkbox d-flex align-items-center gap-1 flex-grow-1 m-0 pt-2px">
                                            <input type="checkbox" value="cod"
                                                name="partial_payment_method" {{ $partial_payment_method ? ($partial_payment_method->value == 'cod' ? 'checked' : '') : '' }}>
                                            <span class="label-text">
                                                {{translate('cod')}}
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-sm-6 col-md-4 col-xl-3">
                                        <label class="custom_checkbox d-flex align-items-center gap-1 flex-grow-1 m-0 pt-2px">
                                            <input type="checkbox" value="digital_payment"
                                                name="partial_payment_method" {{ $partial_payment_method ? ($partial_payment_method->value == 'digital_payment' ? 'checked' : '') : '' }}>
                                            <span class="label-text">
                                                {{translate('digital_payment')}}
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-sm-6 col-md-4 col-xl-3">
                                        <label class="custom_checkbox d-flex align-items-center gap-1 flex-grow-1 m-0 pt-2px">
                                            <input type="checkbox" value="both"
                                                name="partial_payment_method" {{ $partial_payment_method ? ($partial_payment_method->value == 'both' ? 'checked' : '') : '' }}>
                                            <span class="label-text">
                                                {{translate('both')}}
                                            </span>
                                        </label>
                                    </div>                                                        
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="fs-12 text-dark px-3 py-2 rounded bg-danger-10 mt-20">
                        <div class="d-flex gap-2 ">
                            <span class="text-danger lh-1 fs-14">
                                <i class="tio-warning text-danger"></i>
                            </span>
                            <span>
                                {{ translate('Here') }} <strong>{{ translate('Cash on Delivery(COD)') }}</strong> {{ translate('is disable because this is not activated in the') }} <strong>{{ translate('Payment Option setup') }}</strong>
                            </span>
                        </div>
                    </div>
                    <div class="fs-12 text-dark px-3 py-2 rounded bg-danger-10 mt-20">
                        <div class="d-flex gap-2 ">
                            <span class="text-danger lh-1 fs-14">
                                <i class="tio-warning text-danger"></i>
                            </span>
                            <span>
                                {{ translate('Here') }} <strong>{{ translate('Digital Payment') }}</strong> {{ translate('is disable because this is not activated in the') }} <strong>{{ translate('Payment Option setup') }}</strong>
                            </span>
                        </div>
                    </div>
               </div>
               <div class="btn--container justify-content-end mt-20">
                    <button type="reset" class="btn btn--reset min-w-120px">{{ translate('messages.reset') }}</button>
                    <button type="{{ env('APP_MODE') != 'demo' ? 'submit' : 'button' }}"
                        class="btn btn--primary call-demo min-w-120px"><i class="tio-save">x</i>
                        {{ translate('save_information') }}</button>
                </div>
            </div>
        </form>
    </div>             




 



</div>

@endsection

