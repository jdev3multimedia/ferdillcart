@extends('layouts.admin.app')

@section('title', $store->name)

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{ asset('public/assets/admin/css/croppie.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        @include('admin-views.vendor.view.partials._header', ['store' => $store])


        @if (isset($store->vendor->status) && $store->vendor->status == 1)
            <div class="row g-3 text-capitalize">
                <!-- Earnings (Monthly) Card Example -->
                <div class="col-md-4">
                    <div class="card h-100 card--bg-1">
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                            <h5 class="cash--subtitle text-white">
                                {{ translate('messages.collected_cash_by_store') }}
                            </h5>
                            <div class="d-flex align-items-center justify-content-center mt-3">
                                <div class="cash-icon mr-3">
                                    <img src="{{ asset('public/assets/admin/img/cash.png') }}" alt="img">
                                </div>
                                <h2 class="cash--title text-white">
                                    {{ \App\CentralLogics\Helpers::format_currency($wallet->collected_cash) }}</h2>
                            </div>
                        </div>
                        <div class="card-footer pt-0 bg-transparent border-0">
                            <button class="btn text-white text-capitalize bg--title h--45px w-100" id="collect_cash"
                                type="button" data-toggle="modal" data-target="#collect-cash"
                                title="Collect Cash">{{ translate('messages.collect_cash_from_store') }}
                            </button>
                            {{-- <a class="btn text-white text-capitalize bg--title h--45px w-100" href="{{$store->vendor->status ? route('admin.transactions.account-transaction.index') : '#'}}" title="{{translate('messages.goto_account_transaction')}}">{{translate('messages.collect_cash_from_store')}}</a> --}}
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="row g-3">
                        <!-- Panding Withdraw Card Example -->
                        <div class="col-sm-6">
                            <div class="resturant-card card--bg-2">
                                <h4 class="title">
                                    {{ \App\CentralLogics\Helpers::format_currency($wallet->pending_withdraw) }}</h4>
                                <div class="subtitle">{{ translate('messages.pending_withdraw') }}</div>
                                <img class="resturant-icon w--30"
                                    src="{{ asset('public/assets/admin/img/transactions/pending.png') }}" alt="transaction">
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-sm-6">
                            <div class="resturant-card card--bg-3">
                                <h4 class="title">
                                    {{ \App\CentralLogics\Helpers::format_currency($wallet->total_withdrawn) }}</h4>
                                <div class="subtitle">{{ translate('messages.total_withdrawal_amount') }}</div>
                                <img class="resturant-icon w--30"
                                    src="{{ asset('public/assets/admin/img/transactions/withdraw-amount.png') }}"
                                    alt="transaction">
                            </div>
                        </div>

                        <!-- Collected Cash Card Example -->
                        <div class="col-sm-6">
                            <div class="resturant-card card--bg-4">
                                <h4 class="title">
                                    {{ \App\CentralLogics\Helpers::format_currency($wallet->balance > 0 ? $wallet->balance : 0) }}
                                </h4>
                                <div class="subtitle">{{ translate('messages.withdraw_able_balance') }}</div>
                                <img class="resturant-icon w--30"
                                    src="{{ asset('public/assets/admin/img/transactions/withdraw-balance.png') }}"
                                    alt="transaction">
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-sm-6">
                            <div class="resturant-card card--bg-1">
                                <h4 class="title">
                                    {{ \App\CentralLogics\Helpers::format_currency($wallet->total_earning) }}</h4>
                                <div class="subtitle">{{ translate('messages.total_earning') }}</div>
                                <img class="resturant-icon w--30"
                                    src="{{ asset('public/assets/admin/img/transactions/earning.png') }}"
                                    alt="transaction">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title m-0 d-flex align-items-center">
                        <span class="card-header-icon mr-2">
                            <i class="tio-shop-outlined"></i>
                        </span>
                        <span class="ml-1">{{ translate('messages.store_info') }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-6">
                            <div class="resturant--info-address">
                                <div class="logo">
                                    <img class="onerror-image"
                                        data-onerror-image="{{ asset('public/assets/admin/img/100x100/1.png') }}"
                                        src="{{ $store->logo_full_url ?? asset('public/assets/admin/img/100x100/1.png') }}"
                                        alt="{{ $store->name }} Logo">
                                </div>
                                <ul class="address-info list-unstyled list-unstyled-py-3 text-dark">
                                    <li>
                                        <h5 class="name">{{ $store->name }}</h5>
                                    </li>
                                    <li>

                                        <i class="tio-city nav-icon"></i>
                                        <span>{{ translate('messages.address') }}</span> <span>:</span> &nbsp; <span>

                                            <a href="https://www.google.com/maps/search/?api=1&query={{ data_get($store, 'latitude', 0) }},{{ data_get($store, 'longitude', 0) }}"
                                                target="_blank">{{ $store->address }}</a></span>

                                    </li>

                                    <li>
                                        <i class="tio-email nav-icon"></i>
                                        <span>{{ translate('messages.email') }}</span> <span>:</span> &nbsp; <a
                                            href="mailto:{{ $store->email }}"><span>{{ $store->email }}</span></a>
                                    </li>
                                    <li>
                                        <i class="tio-call-talking  nav-icon"></i>
                                        <span>{{ translate('messages.phone') }}</span> <span>:</span> &nbsp; <a
                                            href="tel:{{ $store->phone }}"><span>{{ $store->phone }}</span></a>
                                    </li>
                                    <li>
                                        <i class="tio-map nav-icon"></i>
                                        <span>{{ translate('messages.Zone') }}</span> <span>:</span> &nbsp;
                                        <span>{{ $store?->zone?->name ?? translate('zone_deleted') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div id="map" class="single-page-map"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row pt-3 g-3">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title m-0 d-flex align-items-center">
                                <span class="card-header-icon mr-2">
                                    <i class="tio-user"></i>
                                </span>
                                <span class="ml-1">{{ translate('messages.owner_info') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="resturant--info-address">
                                <div class="avatar avatar-xxl avatar-circle avatar-border-lg">
                                    <img class="avatar-img onerror-image"
                                        data-onerror-image="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                        src="{{ $store->vendor->image_full_url ?? asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                        alt="Image Description">
                                </div>
                                <ul class="address-info address-info-2 list-unstyled list-unstyled-py-3 text-dark">
                                    <li>
                                        <h5 class="name">{{ $store->vendor->f_name }} {{ $store->vendor->l_name }}</h5>
                                    </li>
                                    <li>
                                        <i class="tio-email nav-icon"></i>
                                        <span class="pl-1"><a
                                                href="mailto:{{ $store->vendor->email }}">{{ $store->vendor->email }}</a>
                                        </span>
                                    </li>
                                    <li>
                                        <i class="tio-call-talking nav-icon"></i>
                                        <span class="pl-1"> <a href="tel:{{ $store->vendor->phone }}">
                                                {{ $store->vendor->phone }} </a></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title m-0 d-flex align-items-center">
                                <span class="card-header-icon mr-2">
                                    <i class="tio-crown"></i>
                                </span>
                                <span class="ml-1">{{ translate('messages.Business_Plan') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="resturant--info-address">
                                <ul class="address-info address-info-2 list-unstyled list-unstyled-py-3 text-dark">

                                    @if ($store->store_business_model == 'commission')
                                        <li>
                                            <span> <strong>{{ translate('messages.Business_Plan') }}</span></strong>
                                            <span>:</span> &nbsp; {{ translate($store->store_business_model) }}
                                        </li>
                                        @php($admin_commission = \App\Models\BusinessSetting::where(['key' => 'admin_commission'])->first()?->value)
                                        <li>
                                            <span><strong>{{ translate('messages.Commission_percentage') }}</strong></span>
                                            <span>:</span> &nbsp;
                                            {{ $store->comission > 0 ? $store->comission : $admin_commission }} %
                                        </li>
                                    @elseif ($store->store_business_model == 'subscription')
                                        <li>
                                            <span> <strong>{{ translate('messages.Business_Plan') }}</span></strong>
                                            <span>:</span> &nbsp; {{ translate($store->store_business_model) }} &nbsp;
                                            @if ($store?->store_sub_update_application->is_trial == '1')
                                                <small> <span
                                                        class="badge badge-info">{{ translate('messages.Free_trial') }}</span>
                                                </small>
                                            @endif
                                        </li>
                                        <li>
                                            <span> <strong>{{ translate('messages.Package_name') }}</strong></span>
                                            <span>:</span> &nbsp;
                                            {{ $store?->store_sub_update_application?->package?->package_name ?? translate('Pacakge_not_found!!!') }}
                                        </li>
                                    @elseif ($store->store_business_model == 'unsubscribed')
                                        <li>
                                            <span> <strong>{{ translate('messages.Business_Plan') }}</span></strong>
                                            <span>:</span> &nbsp; {{ translate($store->store_business_model) }} &nbsp;

                                            <small> <span
                                                    class="badge badge-danger">{{ translate('messages.Expired') }}</span>
                                            </small>

                                        </li>
                                        <li>
                                            <span> <strong>{{ translate('messages.Package_name') }}</strong></span>
                                            <span>:</span> &nbsp;
                                            {{ $store?->store_sub_update_application?->package?->package_name ?? translate('Pacakge_not_found!!!') }}
                                        </li>
                                    @elseif($store->store_business_model == 'none' && $store->package_id)
                                        <li>
                                            <span> <strong>{{ translate('messages.Business_Plan') }}</span></strong>
                                            <span>:</span> &nbsp; {{ translate('messages.Subscription') }}
                                        </li>
                                        <li>
                                            <span> <strong>{{ translate('messages.Package_Name') }}</span></strong>
                                            <span>:</span> &nbsp;
                                            {{ App\Models\SubscriptionPackage::where('id', $store->package_id)->first()?->package_name }}
                                        </li>
                                        <li>
                                            <span> <strong>{{ translate('Payment_status') }}</span></strong> <span>:</span>
                                            &nbsp; {{ translate('messages.payment_failed') }}
                                        </li>
                                    @else
                                        <li>
                                            <span> <strong>{{ translate('messages.Business_Plan') }}</span></strong>
                                            <span>:</span> &nbsp; {{ translate('Have_n’t_Selected_Yet.') }}
                                        </li>
                                    @endif




                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($store->tin)
                <div class="row pt-3 g-3">
                    <div class="col-12">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title m-0 d-flex align-items-center">
                                    <span class="card-header-icon mr-2">
                                        <i class="tio-user"></i>
                                    </span>
                                    <span class="ml-1">{{ translate('Business TIN') }}</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="resturant--info-address flex-sm-nowrap flex-wrap gap-2">
                                    <div class="pdf-single  cus-document-responsive"
                                        data-pdf-url="{{ $store->tin_certificate_image_full_url ?? asset('public/assets/admin/img/upload-cloud.png') }}">
                                        <div class="pdf-frame">
                                            @php($imgPath = $store->tin_certificate_image_full_url ?? asset('public/assets/admin/img/upload-cloud.png'))
                                            @if (Str::endsWith($imgPath, ['.pdf', '.doc', '.docx']))
                                                @php($imgPath = asset('public/assets/admin/img/document.svg'))
                                            @endif
                                            <img class="pdf-thumbnail-alt" src="{{ $imgPath }}"
                                                alt="File Thumbnail">
                                        </div>
                                        <div class="overlay">
                                            <a href="javascript:void(0);" class="download-btn" title="">
                                                <i class="tio-download-to"></i>
                                            </a>
                                            <div class="pdf-info d-flex gap-10px align-items-center">
                                                @if (Str::endsWith($imgPath, ['.pdf', '.doc', '.docx']))
                                                    <img src="{{ asset('public/assets/admin/img/document.svg') }}"
                                                        width="34" alt="File Type Logo">
                                                @else
                                                    <img src="{{ asset('public/assets/admin/img/picture.svg') }}"
                                                        width="34" alt="File Type Logo">
                                                @endif
                                                <div class="fs-13 text--title d-flex flex-column">
                                                    <span class="file-name js-filename-truncate"></span>
                                                    <span
                                                        class="opacity-50">{{ translate('Click to view the file') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex-column address-info address-info-2 list-unstyled list-unstyled-py-3">

                                        <div class=" d-flex justify-content-start gap-1">
                                            <span class="text-custom-nowrap text-wrap"><strong class=" text-dark">
                                                    {{ translate('Taxpayer Identification Number(TIN)') }}:
                                                </strong></span>
                                            <span class="pl-1">{{ $store->tin }}</span>
                                        </div>

                                        <div class=" d-flex justify-content-start gap-1">
                                            <span class="text-custom-nowrap text-wrap"><strong
                                                    class=" text-dark">{{ translate('Expire Date') }}: </strong></span>
                                            <span class="pl-1">{{ $store->tin_expire_date }}</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="store-details-banner mb-1 position-relative rounded-10">
            @if (isset($store->vendor->rejection_note))
            <div id="info_notes" class="bg--12 px-2 py-2 mb-4 rounded fz-11  gap-2 align-items-center d-flex ">
                    <span  class="mr-2">
                        {{$store->vendor->rejection_note }}
                    </span>
                </div>

            @endif
                <div class="banner overflow-hidden rounded-10">
                    <img src="{{ $store->cover_photo_full_url ?? asset('public/assets/admin/img/100x100/1.png') }}" alt="banner"
                        class="w-100">
                </div>
                <div class="store-details__inner overflow-hidden d-flex flex-sm-nowrap flex-wrap align-items-sm-end">
                    <div class="store-logo rounded-8 overflow-hidden h-150 ratio--1">
                        <img src="{{ $store->logo_full_url }}" alt="banner" class="w-100">
                    </div>
                    <div class="flex-grow-1">
                        <h2 class="mb-15 text-title">{{ $store->name }}</h2>
                        <div class="row g-3">
                            <div class="col-lg-3 col-md-6 col-sm-6 col-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="icon rounded-circle bg-theme1 d-center w-35px h-35px">
                                        <img width="20" height="20"
                                            src="{{ asset('/public/assets/admin/img/location-business.png') }}"
                                            alt="banner" class="object-contain">
                                    </div>
                                    <div>
                                        <h5 class="fs-14 font-semibold color-3C3C3C m-0">{{ translate('Business Zone') }}</h5>
                                        <span class="d-block fs-12 color-484848">{{ $store->address }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="icon rounded-circle bg-theme2 d-center w-35px h-35px">
                                        <img width="20" height="20"
                                            src="{{ asset('/public/assets/admin/img/plan-business.png') }}"
                                            alt="banner" class="object-contain">
                                    </div>
                                    <div>
                                        <h5 class="fs-14 font-semibold color-3C3C3C m-0">{{ translate('Business Plan') }}</h5>
                                         @if($store->store_business_model == 'none')
                                    <span class="d-block fs-12 color-484848">{{ translate($store?->package?->package_name ) }}</span><br>
                                    <span class="d-block fs-12 color-484848">{{ translate('payment_failed') }}</span>
                                @else
                                <span class="d-block fs-12 color-484848">{{ translate($store->store_business_model ) }}</span>
                                @endif



                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="icon rounded-circle bg-theme3 d-center w-35px h-35px">
                                        <img width="20" height="20"
                                            src="{{ asset('/public/assets/admin/img/pickup-business.png') }}"
                                            alt="banner" class="object-contain">
                                    </div>
                                    <div>
                                        <h5 class="fs-14 font-semibold color-3C3C3C m-0">{{ translate('Approx. Pickup Time') }}</h5>
                                        <span class="d-block fs-12 color-484848">{{  $store->delivery_time  }}</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row pt-3 g-3">
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-header">
                            <div>
                                <h4 class="text-title m-1">
                                    {{ translate('Registration Information') }}
                                </h4>
                                <p class="fs-12 m-0 color-334257B2">{{ translate('Here you can see all the information that Vendor submit during registration') }}</p>
                            </div>
                        </div>
                           <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="card __bg-FAFAFA border-0 h-100">
                            <div class="card-body">
                                <h5 class="mb-10px font-bold"> {{ translate('messages.General_Information') }}
                                </h5>
                                @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
                                @php($language = $language->value ?? null)
                                @php($defaultLang = 'en')
                                <div class="div">
                                    @if ($language)
                                        <ul class="nav nav-tabs mb-4">
                                            <li class="nav-item">
                                                <a class="nav-link lang_link active" href="#"
                                                   id="default-link">{{ translate('Default') }}</a>
                                            </li>
                                            @foreach (json_decode($language) as $lang)
                                                <li class="nav-item">
                                                    <a class="nav-link lang_link" href="#"
                                                       id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if ($language)
                                        <div class="lang_form" id="default-form">
                                            <div class="resturant--info-address">
                                                <ul class="address-info address-info-2 p-0 text-dark">
                                                    <li class="d-flex align-items-start">
                                                        <span class="label min-w-sm-auto">{{ translate('Vendor Name') }}</span>
                                                        <span>: {{$store->getRawOriginal('name')}} </span>
                                                    </li>
                                                    <li class="d-flex align-items-start">
                                                        <span class="label min-w-sm-auto">{{ translate('messages.Business Address') }}</span>
                                                        <span>: {{$store->getRawOriginal('address')}} </span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        @foreach (json_decode($language) as $lang)
                                            <?php
                                            $store?->load('translations');
                                                if(count($store?->translations ?? [])){
                                                    $translate = [];
                                                    foreach($store['translations'] as $t)
                                                    {
                                                        if($t->locale == $lang && $t->key=="name"){
                                                            $translate[$lang]['name'] = $t->value;
                                                        }
                                                          if($t->locale == $lang && $t->key=="address"){
                                                                $translate[$lang]['address'] = $t->value;
                                                            }
                                                    }
                                                }
                                            ?>
                                            <div class="d-none lang_form" id="{{ $lang }}-form">
                                                <div class="resturant--info-address">
                                                    <ul class="address-info address-info-2 p-0 text-dark">
                                                        <li class="d-flex align-items-start">
                                                            <span class="label min-w-sm-auto">{{ translate('Vendor Name') }}</span>
                                                            <span>: {{$translate[$lang]['name']??''}}</span>
                                                        </li>
                                                        <li class="d-flex align-items-start">
                                                            <span class="label min-w-sm-auto">{{ translate('messages.Business Address') }}</span>
                                                            <span>: {{ $translate[$lang]['address']??'' }} </span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div id="default-form">
                                            <div class="resturant--info-address">
                                                <ul class="address-info address-info-2 p-0 text-dark">
                                                    <li class="d-flex align-items-start">
                                                        <span class="label min-w-sm-auto">{{ translate('messages.Provider Name') }}</span>
                                                        <span>: {{ $store->name }}</span>
                                                    </li>
                                                    <li class="d-flex align-items-start">
                                                        <span class="label min-w-sm-auto">{{ translate('messages.Business Address') }}</span>
                                                        <span>: {{ $store->address }}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-6">
                        <div class="card __bg-FAFAFA border-0 h-100">
                            <div class="card-body">
                                <h5 class="mb-10px font-bold"> {{ translate('messages.Owner_Information') }}
                                </h5>
                                <div class="resturant--info-address">
                                    <ul class="address-info address-info-2 p-0 text-dark">
                                        <li class="d-flex align-items-start">
                                            <span class="label min-w-sm-auto">{{ translate('messages.First Name') }}</span>
                                            <span>: {{$store->vendor->f_name}} </span>
                                        </li>
                                        <li class="d-flex align-items-start">
                                            <span class="label min-w-sm-auto">{{ translate('messages.Last Name') }}</span>
                                            <span>: {{$store->vendor->l_name}}</span>
                                        </li>
                                        <li class="d-flex align-items-start">
                                            <span class="label min-w-sm-auto">{{ translate('messages.Phone') }}</span>
                                            <span>: {{$store->vendor->phone}}</span>
                                        </li>
                                    </ul>
                                </div>


                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card __bg-FAFAFA border-0 h-100">
                            <div class="card-body">
                                <h5 class="mb-10px font-bold"> {{ translate('messages.Login Information') }}
                                </h5>


                                <div class="resturant--info-address">
                                    <ul class="address-info address-info-2 p-0 text-dark">
                                        <li class="d-flex align-items-start">
                                            <span class="label min-w-sm-auto">{{ translate('messages.Email') }}</span>
                                            <span>: {{ $store->vendor->email }}</span>
                                        </li>
                                        <li class="d-flex align-items-start">
                                            <span class="label min-w-sm-auto">{{ translate('messages.Password') }}</span>
                                            <span>: {{ translate('*************') }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                    </div>
                </div>
            </div>


        @endif








    </div>

    <div class="modal fade" id="collect-cash" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('messages.collect_cash_from_store') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.transactions.account-transaction.store') }}" method='post'
                        id="add_transaction">
                        @csrf
                        <input type="hidden" name="type" value="store">
                        <input type="hidden" name="store_id" value="{{ $store->id }}">
                        <div class="form-group">
                            <label class="input-label">{{ translate('messages.payment_method') }} <span
                                    class="input-label-secondary text-danger">*</span></label>
                            <input class="form-control" type="text" name="method" id="method" required
                                maxlength="191" placeholder="{{ translate('messages.Ex_:_Card') }}">
                        </div>
                        <div class="form-group">
                            <label class="input-label">{{ translate('messages.reference') }}</label>
                            <input class="form-control" type="text" name="ref" id="ref" maxlength="191">
                        </div>
                        <div class="form-group">
                            <label class="input-label">{{ translate('messages.amount') }} <span
                                    class="input-label-secondary text-danger">*</span></label>
                            <input class="form-control" type="number" min=".01" step="0.01" name="amount"
                                id="amount" max="999999999999.99"
                                placeholder="{{ translate('messages.Ex_:_1000') }}">
                        </div>
                        <div class="btn--container justify-content-end">
                            <button type="submit" id="submit_new_customer"
                                class="btn btn--primary">{{ translate('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <!-- Page level plugins -->
    <script src="{{ asset('public/assets/admin/js/file-preview/details-multiple-document-upload.js') }}"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value }}&callback=initMap&libraries=marker&v=3.61">
    </script>
    <script>
        "use strict";

        $('.swal_fire_alert').on('click', function (event) {
            let url = $(this).data('url');
            let message = $(this).data('message');
            let title = $(this).data('title');
            let imageUrl = $(this).data('image_url');
            let cancelButtonText = $(this).data('cancel_button_text');
            let confirmButtonText = $(this).data('confirm_button_text');
            swalFire(url,title, message, imageUrl,cancelButtonText, confirmButtonText)
        })
        // Call the dataTables jQuery plugin
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });

        const myLatLng = {
            lat: {{ $store->latitude }},
            lng: {{ $store->longitude }}
        };
        let map;
        initMap();

        function initMap() {
        const mapId = "{{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value }}"

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: myLatLng,
                mapId: mapId
            });
            const { AdvancedMarkerElement } = google.maps.marker;

            new AdvancedMarkerElement({
                position: myLatLng,
                map,
                title: "{{ $store->name }}",
            });
        }

        $(document).on('ready', function() {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            let datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function() {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });

            $('#column2_search').on('keyup', function() {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });

            $('#column3_search').on('change', function() {
                datatable
                    .columns(3)
                    .search(this.value)
                    .draw();
            });

            $('#column4_search').on('keyup', function() {
                datatable
                    .columns(4)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function() {
                let select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });



        $('#add_transaction').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{ route('admin.transactions.account-transaction.store') }}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data.errors) {
                        for (let i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('{{ translate('messages.transaction_saved') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function() {
                            location.href = '{{ route('admin.store.view', $store->id) }}';
                        }, 2000);
                    }
                }
            });
        });
    </script>
@endpush
