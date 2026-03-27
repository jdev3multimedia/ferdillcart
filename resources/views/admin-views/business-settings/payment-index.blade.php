@extends('layouts.admin.app')

@section('title',translate('messages.Payment Method'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        @php
        $currency= \App\Models\BusinessSetting::where('key','currency')->first()?->value?? 'USD';
        $checkCurrency = \App\CentralLogics\Helpers::checkCurrency($currency);
        $currency_symbol =\App\CentralLogics\Helpers::currency_symbol();

    @endphp

    <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                {{translate('Payment_Methods_Setup')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3">
            <div class="js-nav-scroller hs-nav-scroller-horizontal mt-2">
                <!-- Nav -->
                <ul class="nav nav-tabs border-0 nav--tabs nav--pills">
                    <li class="nav-item">
                        <a class="nav-link   {{ Request::is('admin/business-settings/third-party/payment-method') ? 'active' : '' }}" href="{{ route('admin.business-settings.third-party.payment-method') }}"   aria-disabled="true">{{translate('Digital Payment')}}</a>
                    </li>
                    @if (\App\CentralLogics\Helpers::get_mail_status('offline_payment_status'))
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('admin/business-settings/offline-payment') ? 'active' : '' }}" href="{{route('admin.business-settings.offline')}}">{{ translate('Offline_Payment') }}</a>
                    </li>
                    @endif
                </ul>
                <!-- End Nav -->
            </div>
        </div>

        <div class="page-header">
            <div class="d-flex flex-wrap justify-content-end align-items-center flex-grow-1">
                <div class="blinkings trx_top active">
                    <i class="tio-info-outined"></i>
                    <div class="business-notes">
                        <h6><img src="{{asset('/public/assets/admin/img/notes.png')}}" alt=""> {{translate('Note')}}</h6>
                        <div>
                            {{translate('Without configuring this section functionality will not work properly. Thus the whole system will not work as it planned')}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="fs-12 px-3 py-12px rounded bg-warning-10 mb-3">
            <div class="d-flex gap-2 mb-1">
                <span class="text-warning lh-1 fs-14">
                    <i class="tio-info"></i>
                </span>
                <span class="text-dark">
                    {{ translate('Here you can configure payment gateways by obtaining the necessary credentials (e.g., API keys) from each respective payment gateway platform.') }}
                </span>
            </div>
            <ul class="mb-0 gap-1 d-flex flex-column">
                <li class="color-656565">
                    {{ translate('To use digital payments, you need to set up at least one payment method') }}
                </li>
                <li class="color-656565">
                    {{ translate('To make available these payment options, you must enable the Digital payment option from') }} <strong class="font-semibold text-primary"><a style="color: #245BD1;" href="{{route('admin.business-settings.business-setup')}}" target="_blank" rel="noopener noreferrer">{{ translate('Business Information') }}</a></strong> {{ translate('page') }}
                </li>
            </ul>
        </div>
        @php($digital_payment=\App\CentralLogics\Helpers::get_business_settings('digital_payment'))
        @if($digital_payment && $digital_payment['status'] ==1 && $checkCurrency !== true )
        <div class="fs-12 color-656565 px-3 py-2 rounded bg-danger-10 mt-20 mb-20">
            <div class="d-flex gap-2 ">
                <span class="text-danger lh-1 fs-14">
                    <i class="tio-warning text-danger"></i>
                </span>
                <span>
                    {{ translate($checkCurrency).' '. translate('Does_not_support_your_current') }}   {{ $currency }}({{$currency_symbol  }}). {{ translate('To change currency setup visit') }} <strong ><a class="text-primary" href="{{route('admin.business-settings.business-setup')}}#currency-setup" target="_blank" rel="noopener noreferrer">{{ translate('Currency') }}</a></strong> {{ translate('page') }}
                </span>
            </div>
        </div>
        @elseif ($digital_payment && $digital_payment['status'] ==1 && $data_values->where('is_active',1  )->count()  == 0)
        <br>
        <div>
            <div class="card">
                <div class="bg--3 px-5 pb-2 card-body d-flex flex-wrap justify-content-around">
                    <p class="w-50 fs-15 text-danger flex-grow-1 ">
                        <i class="tio-info-outined"></i>
                    {{ translate('Currently,_there_is_no_digital_payment_method_is_set_up_that_supports_') }}   {{ $currency }}({{$currency_symbol  }}),{{ translate('_thus_users_cannot_view_digital_payment_options_in_their_websites_and_apps_._You_must_activate_at_least_one_digital_payment_method_that_supports_') }}   {{ $currency }}({{$currency_symbol  }}) {{ translate('_otherwise,_all_users_will_be_unable_to_pay_via_digital_payments.') }}</p>

                </div>
            </div>
        </div>
        @endif 



        <!-- <div class="card border-0">
            <div class="card-header card-header-shadow">
                <h5 class="card-title align-items-center">
                    <img src="{{asset('/public/assets/admin/img/payment-method.png')}}" class="mr-1" alt="">
                    {{translate('Payment Method')}}
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('cash_on_delivery'))
                        <form action="{{route('admin.business-settings.third-party.payment-method-update',['cash_on_delivery'])}}"
                              method="post" id="cash_on_delivery_status_form">
                            @csrf
                            <label class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                <span class="pr-1 d-flex align-items-center switch--label">
                                    <span class="line--limit-1">
                                        {{translate('Cash On Delivery')}}
                                    </span>
                                    <span class="form-label-secondary text-danger d-flex" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If_enabled_Customers_will_be_able_to_select_COD_as_a_payment_method_during_checkout')}}"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="Veg/non-veg toggle"> * </span>
                                </span>
                                <input type="hidden" name="toggle_type" value="cash_on_delivery">
                                <input
                                    type="checkbox" id="cash_on_delivery_status"
                                    data-id="cash_on_delivery_status"
                                    data-type="status"
                                    data-image-on="{{ asset('/public/assets/admin/img/modal/digital-payment-on.png') }}"
                                    data-image-off="{{ asset('/public/assets/admin/img/modal/digital-payment-off.png') }}"
                                    data-title-on="{{ translate('By Turning ON Cash On Delivery Option') }}"
                                    data-title-off="{{ translate('By Turning OFF Cash On Delivery Option') }}"
                                    data-text-on="<p>{{ translate('Customers will not be able to select COD as a payment method during checkout. Please review your settings and enable COD if you wish to offer this payment option to customers.') }}</p>"
                                    data-text-off="<p>{{ translate('Customers will be able to select COD as a payment method during checkout.') }}</p>"
                                    class="status toggle-switch-input dynamic-checkbox"
                                    name="status" value="1" {{$config?($config['status']==1?'checked':''):''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <form action="{{route('admin.business-settings.third-party.payment-method-update',['digital_payment'])}}"
                              method="post" id="digital_payment_status_form">
                            @csrf
                            <label class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                <span class="pr-1 d-flex align-items-center switch--label">
                                    <span class="line--limit-1">
                                        {{translate('digital payment')}}
                                    </span>
                                    <span class="form-label-secondary text-danger d-flex" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If_enabled_Customers_will_be_able_to_select_digital_payment_as_a_payment_method_during_checkout')}}"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="Veg/non-veg toggle"> * </span>
                                </span>
                                <input type="hidden" name="toggle_type" value="digital_payment">
                                <input  type="checkbox" id="digital_payment_status"
                                        data-id="digital_payment_status"
                                        data-type="status"
                                        data-image-on="{{ asset('/public/assets/admin/img/modal/digital-payment-on.png') }}"
                                        data-image-off="{{ asset('/public/assets/admin/img/modal/digital-payment-off.png') }}"
                                        data-title-on="{{ translate('By Turning ON Digital Payment Option') }}"
                                        data-title-off="{{ translate('By Turning OFF Digital Payment Option') }}"
                                        data-text-on="<p>{{ translate('Customers will not be able to select digital payment as a payment method during checkout. Please review your settings and enable digital payment if you wish to offer this payment option to customers.') }}</p>"
                                        data-text-off="<p>{{ translate('Customers will be able to select digital payment as a payment method during checkout.') }}</p>"
                                        class="status toggle-switch-input dynamic-checkbox"
                                        name="status" value="1" {{$digital_payment?($digital_payment['status']==1?'checked':''):''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </form>
                    </div>
                    <div class="col-md-4">
                        @php($Offline_Payment=\App\CentralLogics\Helpers::get_business_settings('offline_payment_status'))
                        <form action="{{route('admin.business-settings.third-party.payment-method-update',['offline_payment_status'])}}"
                              method="post" id="offline_payment_status_form">
                            @csrf
                            <label class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                <span class="pr-1 d-flex align-items-center switch--label">
                                    <span class="line--limit-1">
                                        {{translate('Offline_Payment')}}
                                    </span>
                                    <span class="form-label-secondary text-danger d-flex" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If_enabled_Customers_will_be_able_to_select_offline_payment_as_a_payment_method_during_checkout')}}"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="Veg/non-veg toggle"> * </span>
                                </span>
                                <input type="hidden" name="toggle_type" value="offline_payment_status" >
                                <input  type="checkbox" id="offline_payment_status"
                                        data-id="offline_payment_status"
                                        data-type="status"
                                        data-image-on="{{ asset('/public/assets/admin/img/modal/digital-payment-on.png') }}"
                                        data-image-off="{{ asset('/public/assets/admin/img/modal/digital-payment-off.png') }}"
                                        data-title-on="{{ translate('By Turning ON Offline Payment Option') }}"
                                        data-title-off="{{ translate('By Turning OFF Offline Payment Option') }}"
                                        data-text-on="<p>{{ translate('Customers will not be able to select Offline Payment as a payment method during checkout. Please review your settings and enable Offline Payment if you wish to offer this payment option to customers.') }}</p>"
                                        data-text-off="<p>{{ translate('Customers will be able to select Offline Payment as a payment method during checkout.') }}</p>"
                                        class="status toggle-switch-input dynamic-checkbox"

                                        name="status" value="1" {{$Offline_Payment == 1?'checked':''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </form>
                    </div>
                </div>
            </div>
        </div> -->

         @if($published_status == 1)
            <br>
            <div>
                <div class="card">
                    <div class="card-body d-flex flex-wrap justify-content-around">
                        <h4 class="w-50 flex-grow-1 module-warning-text">
                            <i class="tio-info-outined"></i>
                            {{ translate('Your_current_payment_settings_are_disabled,_because_you_have_enabled_payment_gateway_addon,_To_visit_your_currently_active_payment_gateway_settings_please_follow_the_link.') }}</h4>
                        <div>
                            <a href="{{!empty($payment_url) ? $payment_url : ''}}" class="btn btn-outline-primary"> <i class="tio-settings"></i> {{translate('Settings')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        

        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20">
                    <h4 class="mb-0 flex-grow-1">{{ translate('Digital Payment Methods List') }}</h4>
                    <div class="d-flex align-items-stretch flex-wrap gap-3">
                        <div class="flex-grow-1">
                            <form action="{{ url()->current() }}" method="GET">
                                <!-- Search -->
                                <div class="input--group input-group input-group-merge input-group-flush w-340">
                                    <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{ translate('Search by payment method name') }}" aria-label="Search by payment method name" value="{{ request('search') }}" required="">
                                    <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                                </div>
                                <!-- End Search -->
                            </form>
                        </div>
                    </div>
                </div>
                @php($is_published = $published_status == 1 ? 'inactive' : '')
                <!-- Tab Content -->
                <div class="row digital_payment_methods  {{ $is_published }} g-3">
                    @foreach($data_values->sortByDesc('is_active') as $payment_key => $payment)
                        <div class="col-md-6 payment-card">
                            <div class="card">
                                <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.third-party.payment-method-update',['payment_method_status' => $payment->key_name]):'javascript:'}}" method="POST"
                                      id="{{$payment->key_name}}_form" enctype="multipart/form-data">
                                    @csrf
                                    @php($mode=$data_values->where('key_name',$payment->key_name)->first()->live_values['mode'])
                                    <input type="hidden" name="gateway" value="{{$payment->key_name}}">
                                    <input type="hidden" name="mode" value="{{$mode}}">
                                    <div class="d-flex p-20 w-100 flex-wrap align-content-around justify-content-between">
                                        <h5 class="m-0 align-content-center">
                                            <span class="text-capitalize fs-14 me--3 payment-name">{{str_replace('_',' ',$payment->key_name)}}</span>
                                            @if($mode=='test')
                                            <div class="badge theme-bg-opacity10 text-primary px-2">
                                                {{translate('Test')}}
                                            </div>
                                            @else
                                            <div class="badge theme-bg-opacity10 text-success px-2">
                                                {{translate('Live')}}
                                            </div>
                                            @endif
                                        </h5>
                                        <div class="d-flex align-items-center gap-xxl-20 gap-2">
                                            <label class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between rounded py-0">
                                                <input  type="checkbox" id="{{$payment->key_name}}"
                                                        data-id="{{$payment->key_name}}"
                                                        data-type="status"
                                                        data-image-on="{{ asset('/public/assets/admin/img/feature-status-on.png') }}"
                                                        data-image-off="{{ asset('/public/assets/admin/img/off-danger.png') }}"
                                                        data-title-on="{{ translate('Turn ON ') }} {{strtoupper(str_replace('_',' ',$payment->key_name))}} {{ translate('Payment Method') }}"
                                                        data-title-off="{{ translate('Turn OFF ') }} {{strtoupper(str_replace('_',' ',$payment->key_name))}} {{ translate('Payment Method') }}"
                                                        data-text-on="<p>{{ translate('By enabling ') }} {{strtoupper(str_replace('_',' ',$payment->key_name))}} {{ translate(' customers can securely pay with their ')}} {{strtoupper(str_replace('_',' ',$payment->key_name))}} {{ translate('accounts.') }}</p>"
                                                        data-text-off="<p>{{ translate('By disabling ') }} {{strtoupper(str_replace('_',' ',$payment->key_name))}} {{ translate(' customers will not be able to pay with their ')}} {{strtoupper(str_replace('_',' ',$payment->key_name))}} {{ translate('accounts.') }}</p>"
                                                        class="status toggle-switch-input dynamic-checkbox" 
                                                        name="status" value="1" {{$payment['is_active']==1?'checked':''}}>
                                                <span class="toggle-switch-label text">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                            <button type="button" class="btn bg-white action-btn btn-outline-warning offcanvas-trigger" data-target="#payment_setup_{{$payment->key_name}}">
                                                <i class="tio-settings d-flex"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                    <div id="payment_setup_{{$payment->key_name}}" class="custom-offcanvas d-flex flex-column justify-content-between">
                                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.third-party.payment-method-update'):'javascript:'}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="gateway" value="{{$payment->key_name}}">
                                            
                                            <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                                                <div class="py-1">
                                                    <h3 class="mb-0 line--limit-1">
                                                       {{translate('Setup')}} - <span class="text-capitalize">{{str_replace('_',' ',$payment->key_name)}}</span>
                                                    </h3>
                                                </div>
                                                <button type="button" class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0" aria-label="Close">
                                                    &times;
                                                </button>
                                            </div>
                                            <div class="custom-offcanvas-body p-20">
                                                <div class="p-xxl-20 p-3 bg-light rounded mb-20">
                                                    <div class="mb-3">
                                                        <h4 class="mb-1">
                                                            <span class="text-capitalize">{{str_replace('_',' ',$payment->key_name)}}</span>
                                                        </h4>
                                                        <p class="mb-0 fs-12">
                                                            {{ translate('If you turn off customer can’t pay through this payment gateway.') }}
                                                        </p>
                                                    </div>
                                                    <label class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                                        <span class="pr-1 d-flex align-items-center switch--label">
                                                            <span class="line--limit-1">
                                                                {{translate('Status')}}
                                                            </span>
                                                        </span>
                                                        <input type="checkbox" class="status toggle-switch-input" name="status" value="1" {{$payment['is_active']==1?'checked':''}}>
                                                        <span class="toggle-switch-label text">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                                @php($additional_data = $payment['additional_data'] != null ? json_decode($payment['additional_data']) : [])
                                                <div class="p-20 bg-light rounded mb-20">
                                                    <div class="mb-30">
                                                        <h4 class="mb-1">{{ translate('Choose Logo') }} <span class="text-danger">*</span> </h4>
                                                        <p class="mb-0 fs-12 gray-dark">
                                                            {{translate('It will show in website & app. ')}}
                                                        </p>
                                                    </div>
                                                    <div class="mx-auto text-center">
                                                        @include('admin-views.partials._image-uploader', [
                                                            'id' => 'image-input-'.$payment->key_name,
                                                            'name' => 'gateway_image',
                                                            'ratio' => '3:1',
                                                            'isRequired' => true,
                                                            'existingImage' => isset($additional_data->gateway_image) ? \App\CentralLogics\Helpers::get_full_url('payment_modules/gateway_image/', $additional_data->gateway_image, $additional_data->storage ?? 'public', 'upload_image') : null,
                                                            'imageExtension' => IMAGE_EXTENSION,
                                                            'imageFormat' => IMAGE_FORMAT,
                                                            'maxSize' => MAX_FILE_SIZE,
                                                            'textPosition' => 'bottom',
                                                        ])
                                                    </div>
                                                </div>

                                                <div class="p-xxl-20 p-3 bg-light rounded">
                                                    <div class="form-floating mb-20">
                                                        <label for="payment_gateway_title-{{$payment_key}}" class="form-label fs-14">{{translate('payment_gateway_title')}} <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="gateway_title" id="payment_gateway_title-{{$payment_key}}" placeholder="{{translate('payment_gateway_title')}}" value="{{$additional_data != null ? $additional_data->gateway_title : ''}}">
                                                    </div>

                                                    @php($mode=$data_values->where('key_name',$payment->key_name)->first()->live_values['mode'])
                                                    <div class="form-floating mb-20">
                                                         <label class="form-label fs-14 d-flex align-items-center gap-1">{{translate('Choose Use Type')}} <span class="text-danger">*</span>
                                                            <span class="" data-toggle="tooltip" data-placement="right" data-html="true" data-original-title="<div class='text-start'>{{ translate('When select live option: during use this from website/app need real required data. other wise this gateway can\'t work.') }} <br><br> {{ translate('When select Test option: during use this from website/app use fake required data to test payment gateway work properly or not.') }}</div>">
                                                                <i class="tio-info text-muted fs-14"></i>
                                                            </span>
                                                        </label>
                                                        <div class="restaurant-type-group bg-white border flex-nowrap">
                                                            <label class="form-check form--check w-100">
                                                                <input class="form-check-input" type="radio" {{$mode=='live'?'checked':''}} value="live" name="mode">
                                                                <span class="form-check-label">{{ translate('Live') }}</span>
                                                            </label>
                                                            <label class="form-check form--check w-100">
                                                                <input class="form-check-input" type="radio" {{$mode=='test'?'checked':''}} value="test" name="mode">
                                                                <span class="form-check-label">{{ translate('Test') }}</span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    @php($skip=['gateway','mode','status','supported_country', 'gateway_image'])
                                                    @foreach($data_values->where('key_name',$payment->key_name)->first()->live_values as $key=>$value)
                                                        @if(!in_array($key,$skip))
                                                            <div class="form-floating mb-20">
                                                                <label for="{{$payment_key}}-{{$key}}" class="form-label fs-14">{{ucwords(str_replace('_',' ',$key))}} <span class="text-danger">*</span></label>
                                                                <div class="custom-copy-text position-relative h--45px w-100 rounded overflow-hidden">
                                                                    <input type="text" id="{{$payment_key}}-{{$key}}" class="text-inside copy-text form-control rounded-1 pe-40" placeholder="{{ucwords(str_replace('_',' ',$key))}} *" name="{{$key}}" value="{{env('APP_ENV')=='demo'?'':$value}}" />
                                                                    <span class="copy-btn bg-white position-absolute end-cus-0 top-50 cursor-pointer text-primary me-3"><i class="tio-copy"></i></span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach

                                                    @if($payment['key_name'] == 'paystack')
                                                        <div class="form-floating mb-20">
                                                            <label for="Callback_Url" class="form-label">{{translate('Callback Url')}}</label>
                                                            <input id="Callback_Url" type="text" class="form-control" placeholder="{{translate('Callback Url')}} *" readonly value="{{env('APP_ENV')=='demo'?'': route('paystack.callback')}}">
                                                        </div>
                                                    @endif
                                                    
                                                    @php($supportedCountry = $payment->live_values)
                                                    @if ( $payment['key_name'] == 'mercadopago')
                                                        @php($supportedCountry = isset($supportedCountry['supported_country']) ? $supportedCountry['supported_country'] : ['argentina'])
                                                        <label for="{{ $payment->key_name }}-title" class="form-label">{{ translate('supported_Country') }} *</label>
                                                        <div class="mb-4">
                                                            <select class="form-control w-100" name="supported_country">
                                                                <option value="egypt" {{$supportedCountry == 'egypt'?'selected':''}}>{{ translate('Egypt') }}</option>
                                                                <option value="PAK" {{$supportedCountry == 'PAK'?'selected':''}}>{{ translate('Pakistan') }}</option>
                                                                <option value="KSA" {{$supportedCountry == 'KSA'?'selected':''}}>{{ translate('Saudi Arabia') }}</option>
                                                                <option value="oman" {{$supportedCountry == 'oman'?'selected':''}}>{{ translate('Oman') }}</option>
                                                                <option value="UAE" {{$supportedCountry == 'UAE'?'selected':''}}>{{ translate('UAE') }}</option>
                                                                <option value="argentina" {{$supportedCountry == 'argentina'?'selected':''}}>{{ translate('Argentina') }}</option>
                                                                <option value="brasil" {{$supportedCountry == 'brasil'?'selected':''}}>{{ translate('Brasil') }}</option>
                                                                <option value="mexico" {{$supportedCountry == 'mexico'?'selected':''}}>{{ translate('México') }}</option>
                                                                <option value="uruguay" {{$supportedCountry == 'uruguay'?'selected':''}}>{{ translate('Uruguay') }}</option>
                                                                <option value="colombia" {{$supportedCountry == 'colombia'?'selected':''}}>{{ translate('Colombia') }}</option>
                                                                <option value="chile" {{$supportedCountry == 'chile'?'selected':''}}>{{ translate('Chile') }}</option>
                                                                <option value="peru" {{$supportedCountry == 'peru'?'selected':''}}>{{ translate('Perú') }}</option>
                                                            </select>
                                                        </div>
                                                    @endif

                                                </div>
                                            </div>
                                            <div class="offcanvas-footer p-3 d-flex align-items-center justify-content-center gap-3">
                                                <button type="button" class="btn w-100 btn--reset h--40px reset offcanvas-close">{{translate('Reset')}}</button>
                                                <button type="submit" class="btn w-100 btn--primary h--40px">{{translate('Save')}}</button>
                                            </div>
                                        </form>
                                    </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- End Tab Content -->
            </div>
            @if(count($data_values) > 0)
            <div class="card-footer px-0">
                <div class="d-flex justify-content-end">
                    {!! $data_values->links() !!}
                </div>
            </div>
            @else
            <div class="empty--data">
                <img width="64" class="mb-2" src="{{asset('/public/assets/admin/svg/illustrations/no-data.svg')}}" alt="public">
                <p class="fs-16 mb-20">
                    {{translate('No Payment Method List')}}
                </p>
            </div>
            @endif
            </div>
            
        </div>

    </div>


    <div class="modal fade" id="payment-gateway-warning-modal">
        <div class="modal-dialog modal-dialog-centered status-warning-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true" class="tio-clear"></span>
                    </button>
                </div>
                <div class="modal-body pb-5 pt-0">
                    <div class="max-349 mx-auto mb-20">
                        <div>
                            <div class="text-center">
                                <img width="80" src="{{  asset('public/assets/admin/img/modal/gateway.png') }}" class="mb-20">
                                <h5 class="modal-title"></h5>
                            </div>
                            <div class="text-center" >
                                <h3 > {{ translate('Are_you_sure,_want_to_turn_Off')}} <span id="gateway_name"></span> {{ translate('_as_the_Digital_Payment_method?') }}</h3>
                                <div > <p>{{ translate('You_must_active_at_least_one_digital_payment_method_that_support')}} {{ $currency }} {{ translate('._Otherwise_customers_cannot_pay_via_digital_payments_from_the_app_and_websites._And_Also_restaurants_cannot_pay_you_digitally.') }}</h3></p></div>
                            </div>

                            <div class="text-center mb-4" >
                                <a class="text--underline" href="{{ route('admin.business-settings.business-setup') }}"> {{ translate('View_Currency_Settings.') }}</a>
                            </div>
                            </div>

                        <div class="btn--container justify-content-center">
                            <button data-dismiss="modal"  class="btn btn--cancel min-w-120" >{{translate("Cancel")}}</button>
                            <button data-dismiss="modal"  id="confirm-currency-change" type="button"  class="btn btn--primary min-w-120">{{translate('OK')}}</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- global guideline view Offcanvas here -->

    <div id="offcanvasOverlay" class="offcanvas-overlay"></div>
    <!-- global guideline view Offcanvas end -->

@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin/js/view-pages/business-settings-payment-page.js')}}"></script>
    <script>
        "use strict";


        $(document).on('click', '.open-warning-modal', function(event) {

            const elements = document.querySelectorAll('.open-warning-modal');
            const count = elements.length;

            if(elements.length === 1){

                let gateway = $(this).data('gateway');
                if ($(this).is(':checked') === false) {
                    event.preventDefault();
                    $('#payment-gateway-warning-modal').modal('show');
                    var formated_text=  gateway.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
                    $('#gateway_name').attr('data-gateway_key', gateway).html(formated_text);
                    $(this).data('originalEvent', event);
                }
            }


        });

    $(document).on('click', '#confirm-currency-change', function() {
    var gatewayName =   $('#gateway_name').data('gateway_key');
    if (gatewayName) {
    $('#span_on_' + gatewayName).removeClass('checked');
    }

    var originalEvent = $('.open-warning-modal[data-gateway="' + gatewayName + '"]').data('originalEvent');
    if (originalEvent) {
    var newEvent = $.Event(originalEvent);
    $(originalEvent.target).trigger(newEvent);
    }

    $('#payment-gateway-warning-modal').modal('hide');
    });

    $(".logo").change(function() {
    let viewer = $(this).data('id');
    if (this.files && this.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) {
            $('#' + viewer + '-image-preview').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
    }
    });


    @if(!isset($digital_payment) || $digital_payment['status']==0)
        $('.digital_payment_methods').hide();
    @endif
    </script>
@endpush
