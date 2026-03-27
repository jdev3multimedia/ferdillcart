@extends('layouts.admin.app')

@section('title', translate('messages.customer_settings'))

@push('css_or_js')
@endpush

@section('content')
@php use App\CentralLogics\Helpers; @endphp
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title mr-3">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/business.png') }}" class="w--26" alt="">
                </span>
                <span>
                    {{ translate('business_setup') }}
                </span>
            </h1>
            @include('admin-views.business-settings.partials.nav-menu')
        </div>
        <form action="{{ route('admin.customer.update-settings') }}" method="post" enctype="multipart/form-data"
            id="update-settings">
            @csrf
            <div class="row g-3">
                <div class="col-lg-12">
                    <div class="fs-12 color-656565 px-3 py-2 bg-opacity-10 rounded bg-info mb-20">
                        <div class="d-flex align-items-center gap-2 mb-0">
                            <span class="text-info fs-16">
                                <i class="tio-light-on"></i>
                            </span>
                            <span>
                                {{ translate('See all customer & manage them from') }} <a target="_blank" href="{{ route('admin.users.customer.list') }}" class="text-primary text-underline fw-semibold">{{ translate('All Customer List ') }}</a> {{ translate('page.') }}
                            </span>
                        </div>
                    </div>
                    <div class="card mb-20" id="guest_checkout_section">
                        <div class="card-body">
                            <div class="row g-3 align-items-center">
                                <div class="col-xxl-9 col-lg-8 col-md-7 col-sm-6">
                                    <div>
                                        <h4 class="mb-1">
                                            {{ translate('Guest Checkout') }}
                                        </h4>
                                        <p class="mb-0 fs-12">
                                            {{ translate('Customers can order as guests when this feature is enabled.') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-4 col-md-5 col-sm-6">
                                    @php($guest_checkout_status = $data['guest_checkout_status'] ?? 0)
                                    <div class="form-group mb-0">
                                        <label
                                            class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                    <span class="pr-1 d-flex align-items-center switch--label">
                                        <span class="line--limit-1">
                                            {{translate('messages.Status') }}
                                        </span>
                                    </span>
                                            <input type="checkbox" data-id="guest_checkout_status" data-type="toggle"
                                                    data-image-on="{{ asset('/public/assets/admin/img/modal/dm-tips-on.png') }}"
                                                    data-image-off="{{ asset('/public/assets/admin/img/modal/dm-tips-off.png') }}"
                                                    data-title-on="<strong>{{ translate('messages.Want_to_enable_guest_checkout?') }}</strong>"
                                                    data-title-off="<strong>{{ translate('messages.Want_to_disable_guest_checkout?') }}</strong>"
                                                    data-text-on="<p>{{ translate('messages.If_you_enable_this,_guest_checkout_will_be_visible_when_customer_is_not_logged_in.') }}</p>"
                                                    data-text-off="<p>{{ translate('messages.If_you_disable_this,_guest_checkout_will_not_be_visible_when_customer_is_not_logged_in.') }}</p>"
                                                    class="status toggle-switch-input dynamic-checkbox-toggle" value="1"
                                                    name="guest_checkout_status" id="guest_checkout_status" {{ $guest_checkout_status == 1 ? 'checked' : '' }}>
                                            <span class="toggle-switch-label text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="fs-12 color-656565 px-3 py-2 bg-opacity-10 rounded bg-info mt-20">
                                <div class="d-flex align-items-center gap-2 mb-0">
                                    <span class="text-info fs-16">
                                        <i class="tio-light-on"></i>
                                    </span>
                                    <span>
                                        {{ translate('Customers can order as guests when this feature is enabled.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-20" id="general_setup_section">
                        <div class="card-body">
                            <div class="mb-20">
                                <h4 class="mb-1">
                                    {{ translate('General Setup') }}
                                </h4>
                                <p class="mb-0 fs-12">
                                    {{ translate('Configure options to customize services for your customers.') }}
                                </p>
                            </div>
                            <div class="bg-light rounded p-xxl-20 p-3">
                                <div class="row g-3">
                                    <div class="col-sm-6 col-lg-4">
                                        @php($vnv = $data['toggle_veg_non_veg'] ?? 0)
                                        <div class="form-group mb-0">
                                            <span class="mb-10px d-flex align-items-center">
                                                <span class="text-title">
                                                    {{ translate('messages.Customer’s_Food_Preference') }}
                                                </span>
                                                <span class="form-label-secondary  d-flex" data-toggle="tooltip"
                                                    data-placement="right"
                                                    data-original-title="{{ translate('messages.If_this_feature_is_active,_customers_can_filter_food_according_to_their_preference_from_the_Customer_App_or_Website.') }}"><i class="tio-info text-muted ps--3"></i></span>
                                            </span>
                                            <label
                                                class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                                <span class="pr-1 d-flex align-items-center switch--label">
                                                    <span class="line--limit-1">
                                                        {{ translate('messages.Status') }}
                                                    </span>
                                                </span>
                                                    <input type="checkbox" data-id="vnv1" data-type="toggle"
                                                        data-image-on="{{ asset('/public/assets/admin/img/modal/veg-on.png') }}"
                                                        data-image-off="{{ asset('/public/assets/admin/img/modal/veg-off.png') }}"
                                                        data-title-on="{{ translate('messages.Want_to_enable_the') }} <strong>{{ translate('messages.‘Veg/Non-Veg’_feature?') }}</strong>"
                                                        data-title-off="{{ translate('messages.Want_to_disable') }} <strong>{{ translate('messages.the_Veg/Non-Veg_Feature?') }}</strong>"
                                                        data-text-on="<p>{{ translate('messages.If_you_enable_this,_customers_can_filter_food_items_by_choosing_food_from_the_Veg/Non-Veg_feature.') }}</p>"
                                                        data-text-off="<p>{{ translate('messages.If_you_disable_this,_the_Veg/Non-Veg_feature_will_be_hidden_in_the_Customer_App_&_Website.') }}</p>"
                                                        class="status toggle-switch-input dynamic-checkbox-toggle" value="1"
                                                        name="toggle_veg_non_veg" id="vnv1" {{ $vnv == 1 ? 'checked' : '' }}>
                                                <span class="toggle-switch-label text">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card mb-20 card-container" id="customer-wallet">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-2 flex-sm-nowrap flex-wrap">
                                <div>
                                    <h4 class="mb-1">{{translate('Customer Wallet')}}</h4>
                                    <p class="fs-12 m-0">{{translate('When active this feature customer can Earn & Buy through wallet. See customer wallet from Customers Details page.')}}</p>
                                </div>
                                <div class="d-flex flex-sm-nowrap flex-wrap justify-content-end justify-content-end align-items-center gap-3">
                                    <div class="view_toggle_btn fz--14px info-dark cursor-pointer text-decoration-underline font-semibold d-flex align-items-center gap-1">
                                        {{ translate('messages.view') }}
                                        <i class="tio-chevron-down fs-22"></i>
                                    </div>
                                    <div class="mb-0">
                                        <label class="toggle-switch toggle-switch-sm mb-0">
                                            <input type="checkbox"


                                                     data-id="wallet_status" data-type="toggle"
                                                        data-image-on="{{ asset('/public/assets/admin/img/modal/refund-on.png') }}"
                                                        data-image-off="{{ asset('/public/assets/admin/img/modal/refund-off.png') }}"
                                                        data-title-on="{{ translate('messages.Want_to_enable') }} <strong>{{ translate('messages.Wallet_feature?') }}</strong>"
                                                        data-title-off="{{ translate('messages.Want_to_disable') }} <strong>{{ translate('messages.Wallet_feature?') }}</strong>"
                                                        data-text-on="<p>{{ translate('messages.If_you_enable_this,_Customers_will_have_the_wallet_feature.') }}</p>"
                                                        data-text-off="<p>{{ translate('messages.If_you_disable_this,_the_Wallet_feature_will_be_hidden_from_the_Customer_App_and_Website.') }}</p>"
                                                        class="status toggle-switch-input dynamic-checkbox-toggle "


                                            name="wallet_status" id="wallet_status" value="1"
                                                    {{ isset($data['wallet_status']) && $data['wallet_status'] == 1 ? 'checked' : '' }}>
                                            <span class="toggle-switch-label text mb-0">
                                                <span
                                                    class="toggle-switch-indicator">
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-details-body {{ !isset($data['wallet_status']) || $data['wallet_status'] != 1  ? 'd-none' : '' }}">
                                <div class="bg-light2  rounded p-xxl-20 p-3 mt-20">
                                    <div class="row g-3">
                                         <div class="col-sm-6 col-lg-6">
                                            <div class="form-group mb-0">
                                                <span class="mb-2 d-flex align-items-center text-title">{{ translate('messages.refund_to_wallet') }}<span
                                                    class="input-label-secondary" data-toggle="tooltip"
                                                    data-placement="right"
                                                    data-original-title="{{ translate('messages.If_it’s_enabled,_Customers_will_automatically_receive_the_refunded_amount_in_their_wallets._But_if_it’s_disabled,_the_Admin_will_handle_the_Refund_Request_in_his_convenient_transaction_channel.') }}"><i class="tio-info text-muted ps--3"></i></span>
                                                </span>
                                                <label
                                                    class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control {{ isset($data['wallet_status']) && $data['wallet_status'] == 1 ? '' : 'text-muted' }}">
                                                    <span class="pr-2">{{ translate('messages.Status') }}</span>
                                                    <input type="checkbox"
                                                    {{ isset($data['wallet_status']) && $data['wallet_status'] == 1 ? '' : 'disabled' }}
                                                    data-id="refund_to_wallet" data-type="toggle"
                                                        data-image-on="{{ asset('/public/assets/admin/img/modal/refund-on.png') }}"
                                                        data-image-off="{{ asset('/public/assets/admin/img/modal/refund-off.png') }}"
                                                        data-title-on="{{ translate('messages.Want_to_enable') }} <strong>{{ translate('messages.Refund_to_Wallet_feature?') }}</strong>"
                                                        data-title-off="{{ translate('messages.Want_to_disable') }} <strong>{{ translate('messages.Refund_to_Wallet_feature?') }}</strong>"
                                                        data-text-on="<p>{{ translate('messages.If_you_enable_this,_Customers_will_automatically_receive_the_refunded_amount_in_their_wallets.') }}</p>"
                                                        data-text-off="<p>{{ translate('messages.If_you_disable_this,_the_Admin_will_handle_the_Refund_Request_in_his_convenient_transaction_channel.') }}</p>"
                                                        class="status toggle-switch-input dynamic-checkbox-toggle "
                                                        name="wallet_add_refund" id="refund_to_wallet" value="1"
                                                        {{ isset($data['wallet_add_refund']) && $data['wallet_add_refund'] == 1 ? 'checked' : '' }}>
                                                    <span class="toggle-switch-label text">
                                                        <span class="toggle-switch-indicator"></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-lg-6">
                                            <div class="form-group mb-0">
                                                <span class="mb-2 d-flex align-items-center text-title">{{ translate('Add Fund to Wallet') }}
                                                    <span class="input-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('messages.With_this_feature,_customers_can_add_fund_to_wallet_if_the_payment_module_is_available.') }}">
                                                        <i class="tio-info text-muted ps--3"></i>
                                                    </span>
                                                </span>
                                                <label
                                                    class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control {{ isset($data['wallet_status']) && $data['wallet_status'] == 1 ? '' : 'text-muted' }}">
                                                    <span class="pr-2">{{ translate('Status') }}
                                                    </span>
                                                    <input {{ isset($data['wallet_status']) && $data['wallet_status'] == 1 ? '' : 'disabled' }}
                                                    type="checkbox" data-id="add_fund_status" data-type="toggle"
                                                        data-image-on="{{ asset('/public/assets/admin/img/modal/wallet-on.png') }}"
                                                        data-image-off="{{ asset('/public/assets/admin/img/modal/wallet-off.png') }}"
                                                        data-title-on="{{ translate('messages.Want_to_enable') }} <strong>{{ translate('add_fund_to_Wallet_feature?') }}</strong>"
                                                        data-title-off="{{ translate('messages.Want_to_disable') }} <strong>{{ translate('add_fund_to_Wallet_feature?') }}</strong>"
                                                        data-text-on="<p>{{ translate('messages.If_you_enable_this,_Customers_can_add_fund_to_wallet_using_payment_module') }}</p>"
                                                        data-text-off="<p>{{ translate('messages.If_you_disable_this,_add_fund_to_wallet_will_be_hidden_from_the_Customer_App_&_Website.') }}</p>"
                                                        class="status toggle-switch-input dynamic-checkbox-toggle "
                                                        name="add_fund_status" id="add_fund_status" value="1"
                                                        {{ isset($data['add_fund_status']) && $data['add_fund_status'] == 1 ? 'checked' : '' }}>
                                                    <span class="toggle-switch-label text">
                                                        <span class="toggle-switch-indicator"></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="fs-12 color-656565 px-3 py-2 bg-opacity-10 rounded bg-info mt-20">
                                    <div class="d-flex align-items-center mb-0">
                                        <span class="text-info fs-16">
                                            <i class="tio-light-on"></i>
                                        </span>
                                        <ul class="mb-0 fs-12">
                                                <li>{{ translate('You can see customer wallet from Customers details page. Go to this path') }} <strong>{{ translate('Customers') }} > {{ translate('Customer List') }} > {{ translate('View Details') }}</strong></li>
                                                <li>
                                                    <p class="mb-0 mt-2 fs-12 color-656565">{{ translate('To add fund for a customer visit') }} <a target="_blank" href="{{ route('admin.users.customer.wallet.add-fund') }}" class="text-primary text-underline fw-semibold">{{ translate('Add Fund') }}</a> {{ translate('page.') }}</p>
                                                </li>
                                            </ul>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="card mb-20 card-container" id="loyalty_point_section">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-2 flex-sm-nowrap flex-wrap">
                                <div>
                                    <h4 class="mb-1">{{translate('Customer Loyalty Point')}}</h4>
                                    <p class="fs-12 m-0">{{translate('If enabled customers will earn a certain amount of points after each purchase.')}}</p>
                                </div>
                                <div class="d-flex flex-sm-nowrap flex-wrap justify-content-end justify-content-end align-items-center gap-3">
                                    <div class="view_toggle_btn fz--14px info-dark cursor-pointer text-decoration-underline font-semibold d-flex align-items-center gap-1">
                                        {{ translate('messages.view') }}
                                        <i class="tio-chevron-down fs-22"></i>
                                    </div>

                                    <div class="mb-0">
                                        <label class="toggle-switch toggle-switch-sm mb-0">
                                            <input type="checkbox" data-type="toggle" class="status toggle-switch-input" name="loyalty_point_status" id="loyalty_point_status"
                                                    data-section="loyalty-point-section" value="1"
                                                    {{ isset($data['loyalty_point_status']) && $data['loyalty_point_status'] == 1 ? 'checked' : '' }}>
                                            <span class="toggle-switch-label text mb-0">
                                                <span
                                                    class="toggle-switch-indicator">
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-details-body {{ !isset($data['loyalty_point_status']) || $data['loyalty_point_status'] != 1  ? 'd-none' : '' }}">
                                <div class="bg-light2  rounded p-xxl-20 p-3 mt-20">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-sm-6 col-lg-4">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="loyalty_point_exchange_rate">1
                                                    {{ \App\CentralLogics\Helpers::currency_code() }}
                                                    {{ translate('equivalent point amount') }}
                                                    <span class="input-label-secondary"
                                                        data-toggle="tooltip" data-placement="right"
                                                        data-original-title="{{ translate('Set the value of how many loyalty points are equal to 1 USD for converting points into wallet money.') }}"><i class="tio-info text-muted"></i>
                                                    </span>
                                                    <span class="text-danger"> *</span>
                                                </label>
                                                <input {{ isset($data['loyalty_point_status']) && $data['loyalty_point_status'] == 1 ? 'required' : 'readonly' }}
                                                id="loyalty_point_exchange_rate" type="number" class="form-control" name="loyalty_point_exchange_rate"  min="0"
                                                    value="{{ $data['loyalty_point_exchange_rate'] ?? '0' }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-4">
                                            <div class="form-group mb-0">
                                                <label class="input-label gap-0" for="loyalty_point_item_purchase_point">
                                                    {{ translate('Loyalty_Point_Earn_Per_Order') }} (%)
                                                    <span class="input-label-secondary"
                                                        data-toggle="tooltip" data-placement="right"
                                                        data-original-title="{{ translate('Specify the percentage of the total order amount that a customer will earn as loyalty points.') }}"><i class="tio-info text-muted"></i>
                                                    </span>
                                                     <span class="text-danger"> *</span>
                                                </label>
                                                <input {{ isset($data['loyalty_point_status']) && $data['loyalty_point_status'] == 1 ? 'required' : 'readonly' }} id="item_purchase_point"
                                                    type="number" class="form-control" name="loyalty_point_item_purchase_point"  min="0" value="{{ $data['loyalty_point_item_purchase_point'] ?? '0' }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-4">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="minimum_transfer_point">
                                                    {{ translate('Minimum_Point_Required_To_Convert') }}
                                                    <span class="input-label-secondary"
                                                        data-toggle="tooltip" data-placement="right"
                                                        data-original-title="{{ translate('Enter the minimum number of points a customer must collect before they can convert them into a wallet balance.') }}"><i class="tio-info text-muted"></i>
                                                    </span>
                                                     <span class="text-danger"> *</span>
                                                </label>
                                                <input {{ isset($data['loyalty_point_status']) && $data['loyalty_point_status'] == 1 ? 'required' : 'readonly' }} id="minimum_transfer_point"
                                                    type="number" class="form-control" name="loyalty_point_minimum_point" min="0" value="{{ $data['loyalty_point_minimum_point'] ?? '0' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="fs-12 color-656565 px-3 py-2 bg-opacity-10 rounded bg-info mt-20">
                                    <div class="d-flex align-items-center gap-2 mb-0">
                                        <span class="text-info fs-16">
                                            <i class="tio-light-on"></i>
                                        </span>
                                        <span>
                                            {{ translate('To see customer loyalty point report visit') }} <a target="_blank" href="{{ route('admin.users.customer.loyalty-point.report') }}" class="text-primary text-underline fw-semibold">{{ translate('Loyalty Point Report.') }}</a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card card-container" id="referral_earning_section">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-2 flex-sm-nowrap flex-wrap">
                                <div>
                                    <h4 class="mb-1">{{translate('Customer Referral Earning Settings')}}</h4>
                                    <p class="fs-12 m-0">{{translate('Customers will receive this wallet balance rewards for sharing their referral code')}}</p>
                                </div>
                                <div class="d-flex flex-sm-nowrap flex-wrap justify-content-end justify-content-end align-items-center gap-3">
                                    <div class="view_toggle_btn fz--14px info-dark cursor-pointer text-decoration-underline font-semibold d-flex align-items-center gap-1">
                                        {{ translate('messages.view') }}
                                        <i class="tio-chevron-down fs-22"></i>
                                    </div>
                                    <div class="mb-0">
                                        <label class="toggle-switch toggle-switch-sm mb-0">
                                            <input type="checkbox" data-type="toggle" class="status toggle-switch-input" name="ref_earning_status" id="ref_earning_status"
                                                        data-section="referrer-earning" value="1"
                                                        {{ isset($data['ref_earning_status']) && $data['ref_earning_status'] == 1 ? 'checked' : '' }}>
                                            <span class="toggle-switch-label text mb-0">
                                                <span
                                                    class="toggle-switch-indicator">
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="card-details-body {{ !isset($data['ref_earning_status']) || $data['ref_earning_status'] != 1  ? 'd-none' : '' }} mt-20">
                                <div class="bg-light rounded p-xxl-20 p-3">
                                    <div class="py-0">
                                        <div class="row g-3 align-items-end mb-3">

                                            <div class="align-self-center  col-md-4">
                                                <div class="text-left">
                                                    <h5 class="align-items-center">
                                                        <span>
                                                            {{ translate('Who_Share_the_code') }}
                                                        </span>
                                                    </h5>
                                                    <p class="fs-12 color-656565">
                                                        {{ translate('Customers earn wallet rewards for sharing their referral code. Rewards are given when friends sign up and complete their first order using the code.') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="bg-white rounded p-xxl-20 p-3 text-left">
                                                    <div class="card-body p-0">
                                                        <div class="form-group mb-0">
                                                            <label class="input-label" for="ref_earning_exchange_rate">
                                                                {{ translate('Earning Per Referral') }}
                                                                ({{ \App\CentralLogics\Helpers::currency_symbol() }})

                                                                <span class="input-label-secondary" data-toggle="tooltip"
                                                                    data-placement="right"
                                                                    data-original-title="{{ translate('Refer amount add to wallet option is disabled. Kindly turn on the option from Customer Wallet section to complete this settings') }}">
                                                                    <i class="tio-info text-muted"></i>
                                                                </span>
                                                                 <span class="text-danger"> *</span>
                                                            </label>
                                                            <input {{ isset($data['wallet_status']) && $data['wallet_status'] == 1 ? '' : 'readonly' }}
                                                            id="ref_earning_exchange_rate" type="number" step="{{ Helpers::getDecimalPlaces() }}" min="0" max="99999999999"
                                                                class="form-control" name="ref_earning_exchange_rate"
                                                                value="{{ $data['ref_earning_exchange_rate'] ?? '0' }}" data-toggle="tooltip" data-placement="right" data-original-title="Refer amount add to wallet option is disabled. Kindly turn on the option from Customer Wallet section to complete this settings">
                                                            @if (isset($data['wallet_status']) && $data['wallet_status'] != 1)
                                                            <p class="text-danger mt-1 mb-0 fs-12">{{ translate('Must Turn on') }} <strong>{{ translate('Add Fund to Wallet') }}</strong> {{ translate('option, otherwise customer can’t receive the reward amount.') }} </p>
                                                            @endif

                                                            </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-3 align-items-end">
                                            <div class="align-self-center col-md-4 text-center">
                                                <div class="text-left">

                                                    <h5 class="align-items-center">
                                                        <span>
                                                            {{ translate('Who_Use_the_code') }}
                                                        </span>
                                                    </h5>
                                                    <p class="fs-12 color-656565">
                                                        {{ translate('Customers get a signup and first purchase discount when they use the referral code for a limited time.') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="bg-white rounded p-xxl-20 p-3 text-left">
                                                    <div class="card-body p-0">
                                                        <div>
                                                            <div class="form-group">
                                                                <span
                                                                    class="mb-2 text-title d-flex align-items-center">{{ translate('Customer_will_get_Discount_on_first_order ') }}
                                                                    <span class="input-label-secondary" data-toggle="tooltip"
                                                                        data-placement="right"
                                                                        data-original-title="{{ translate('messages.Configure_discounts_for_newly_registered_users_who_sign_up_with_a_referral_code._Customize_the_discount_type_and_amount_to_incentivize_referrals_and_encourage_user_engagement.') }}">
                                                                        <i class="tio-info text-muted"></i>
                                                                    </span>
                                                                </span>
                                                                <label
                                                                    class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control {{ isset($data['wallet_status']) && $data['wallet_status'] == 1 ? '' : 'text-muted' }}">
                                                                    <span
                                                                        class="pr-2">{{ translate('Status ') }}
                                                                    </span>
                                                                    <input {{ isset($data['wallet_status']) && $data['wallet_status'] == 1 ? '' : 'disabled' }}
                                                                    type="checkbox" data-id="new_customer_discount_status"
                                                                        data-type="toggle"
                                                                        data-image-on="{{ asset('/public/assets/admin/img/modal/basic_campaign_on.png') }}"
                                                                        data-image-off="{{ asset('/public/assets/admin/img/modal/basic_campaign_off.png') }}"
                                                                        data-title-on="{{ translate('messages.Want_to_enable') }} <strong>{{ translate('messages.new_customer_discount?') }}</strong>"
                                                                        data-title-off="{{ translate('messages.Want_to_disable') }} <strong>{{ translate('messages.new_customer_discount?') }}</strong>"
                                                                        data-text-on="<p>{{ translate('messages.If_you_enable_this,_Customers_will_get_discount_on_first_order.') }}</p>"
                                                                        data-text-off="<p>{{ translate('mo.If_you_disable_this,_Customers_won’t_get_any_discount_on_first_order.') }}</p>"
                                                                        class="status toggle-switch-input dynamic-checkbox-toggle "
                                                                        name="new_customer_discount_status"
                                                                        id="new_customer_discount_status" value="1"
                                                                        {{ data_get($data, 'new_customer_discount_status') == 1 ? 'checked' : '' }}>
                                                                    <span class="toggle-switch-label text">
                                                                        <span class="toggle-switch-indicator"></span>
                                                                    </span>
                                                                </label>
                                                            </div>

                                                        </div>
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-0">
                                                                    <label class="input-label" for="new_customer_discount_amount">
                                                                        {{ translate('Discount_Amount') }}

                                                                        <span class="{{  data_get($data, 'new_customer_discount_amount_type') != 'amount'  ? '': 'd-none' }} " id="percentage">(%)</span>
                                                                        <span  class=" {{  data_get($data, 'new_customer_discount_amount_type') == 'amount' ? '': 'd-none' }} " id='cuttency_symbol'>({{ \App\CentralLogics\Helpers::currency_symbol() }})
                                                                        </span>


                                                                        <span
                                                                            class="input-label-secondary" data-toggle="tooltip"
                                                                            data-placement="right"
                                                                            data-original-title="{{ translate('Enter_the_discount_value_for_referral-based_new_user_registrations.') }}">
                                                                            <i class="tio-info text-muted"></i>
                                                                        </span>
                                                                         <span class="text-danger"> *</span>
                                                                    </label>
                                                                    <div class="d-flex align-items-center gap-0 border rounded overflow-hidden">
                                                                        <input id="new_customer_discount_amount" type="number" step="{{ Helpers::getDecimalPlaces() }}" min="0"
                                                                        {{  isset($data['wallet_status']) && $data['wallet_status'] == 1 && data_get($data, 'new_customer_discount_status') == 1 ? 'required' : 'readonly' }}
                                                                            class="form-control border-0 rounded-0" name="new_customer_discount_amount" max='{{  data_get($data, 'new_customer_discount_amount_type') != 'amount'  ? '100': '9999999999' }}'
                                                                            value="{{data_get($data, 'new_customer_discount_amount') ?? '0' }}">
                                                                        <select   name="new_customer_discount_amount_type"  class="bg-modal-btn custom-select border-0 rounded-0 w-auto"  id="new_customer_discount_amount_type"
                                                                            {{ isset($data['wallet_status']) && $data['wallet_status'] == 1 && data_get($data, 'new_customer_discount_status') == 1 ? 'required' : 'disabled' }}
                                                                            >
                                                                                <option {{ data_get($data, 'new_customer_discount_amount_type') == 'percentage' ? "selected": '' }} value="percentage">(%)</option>
                                                                                <option {{ data_get($data, 'new_customer_discount_amount_type') == 'amount' ? "selected": '' }}  value="amount">{{ \App\CentralLogics\Helpers::currency_symbol() }}</option>
                                                                            </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-0">
                                                                    <label class="input-label" for="new_customer_discount_amount_validity">
                                                                        {{ translate('validity') }}
                                                                        <span class="input-label-secondary" data-toggle="tooltip"
                                                                            data-placement="right"
                                                                            data-original-title="{{ translate('Set_how_long_the_discount_remains_active_after_registration.') }}">
                                                                            <i class="tio-info text-muted"></i>
                                                                        </span>
                                                                         <span class="text-danger"> *</span>
                                                                    </label>
                                                                    <div class="d-flex align-items-center gap-0 border rounded overflow-hidden">
                                                                        <input id="new_customer_discount_amount_validity" type="number"  min="0" max="999"
                                                                        {{ isset($data['wallet_status']) && $data['wallet_status'] == 1 && data_get($data, 'new_customer_discount_status') == 1 ? 'required' : 'readonly' }}
                                                                            class="form-control border-0 rounded-0" name="new_customer_discount_amount_validity"
                                                                            value="{{ data_get($data, 'new_customer_discount_amount_validity') ?? '0' }}">
                                                                        <select name="new_customer_discount_validity_type" class="bg-modal-btn custom-select border-0 rounded-0 w-auto" id="new_customer_discount_validity_type"  {{ isset($data['wallet_status']) && $data['wallet_status'] == 1 &&  data_get($data, 'new_customer_discount_status') == 1 ? 'required' : 'disabled' }}>
                                                                            <option {{ data_get($data, 'new_customer_discount_validity_type') == 'day' ? "selected": '' }} value="day">{{translate('messages.day')}}</option>
                                                                            <option {{ data_get($data, 'new_customer_discount_validity_type') == 'month' ? "selected": '' }}  value="month">{{translate('messages.month')}} </option>
                                                                            <option {{ data_get($data, 'new_customer_discount_validity_type') == 'year' ? "selected": '' }}  value="year">{{translate('messages.year')}} </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('admin-views.partials._floating-submit-button')
                </div>
            </div>
        </form>

        <!-- End Table -->
    </div>
    <div id="global_guideline_offcanvas" style="overflow-y: auto;"
         class="custom-offcanvas d-flex flex-column justify-content-between global_guideline_offcanvas">
        <div>
            <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                <h3 class="mb-0">{{ translate('Customer Settings Guideline') }}</h3>
                <button type="button"
                        class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary offcanvas-close fz-15px p-0"
                        aria-label="Close">&times;</button>
            </div>

            <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                    <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed"
                            type="button" data-toggle="collapse" data-target="#guest_checkout_guide" aria-expanded="true">
                        <div
                            class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                            <i class="tio-down-ui"></i>
                        </div>
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Guest Checkout') }}</span>
                    </button>
                    <a href="#guest_checkout_section"
                       class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3 show" id="guest_checkout_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Guest Checkout') }}</h5>
                            <p class="fs-12 mb-0">
                                {{ translate('The Guest Checkout feature allows customers to place orders without creating a full account or logging in. This streamlines the purchase process, improves user experience, and can increase order completion rates, especially for first-time or infrequent users.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                    <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed"
                            type="button" data-toggle="collapse" data-target="#food_preference_guide" aria-expanded="true">
                        <div
                            class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                            <i class="tio-down-ui"></i>
                        </div>
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Customer Food Preference') }}</span>
                    </button>
                    <a href="#general_setup_section"
                       class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3" id="food_preference_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Customer Food Preference') }}</h5>
                            <p class="fs-12 mb-0">
                                {{ translate('Enabling this option allows customers to view Veg/Non-Veg food preferences on the website and app.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                    <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed"
                            type="button" data-toggle="collapse" data-target="#customer_wallet_guide" aria-expanded="true">
                        <div
                            class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                            <i class="tio-down-ui"></i>
                        </div>
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Customer Wallet') }}</span>
                    </button>
                    <a href="#customer-wallet"
                       class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3" id="customer_wallet_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Customer Wallet') }}</h5>
                            <p class="fs-12 mb-0">
                                {{ translate('The Customer Wallet is a digital wallet feature that allows customers to store funds within the platform for quick and convenient transactions. Customers can add money to their wallet, make payments for orders, and even request or receive refunds directly through the wallet.') }}
                            </p>
                            <br>
                            <ul class="fs-12">
                                <li>
                                    <strong>{{ translate('Refund to Wallet') }}:</strong> {{ translate('When the wallet feature is enabled, customers can use their wallet balance to pay for orders. Refunds can also be sent directly to the wallet of the customer for easy use in future purchases.') }}
                                </li>
                                <li>
                                    <strong>{{ translate('Add funds to wallet') }}:</strong> {{ translate('If this option is enabled, customers can add money to their wallet using digital payment methods like bank transfer, mobile wallets, etc.') }}
                                </li>
                                <li>
                                    <strong>{{ translate('Minimum add Amount') }}:</strong> {{ translate('The smallest amount a customer can add to their wallet at one time.') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                    <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed"
                            type="button" data-toggle="collapse" data-target="#loyalty_point_guide" aria-expanded="true">
                        <div
                            class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                            <i class="tio-down-ui"></i>
                        </div>
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Customer Loyalty Point') }}</span>
                    </button>
                    <a href="#loyalty_point_section"
                       class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3" id="loyalty_point_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Customer Loyalty Point') }}</h5>
                            <p class="fs-12 mb-0">
                                {{ translate('Define how many loyalty points equal 1 unit of currency(Ex: $1 if the system default currency is dollars). It helps customers understand the value of their points when they want to convert them to wallet money.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                    <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed"
                            type="button" data-toggle="collapse" data-target="#referral_earning_guide" aria-expanded="true">
                        <div
                            class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                            <i class="tio-down-ui"></i>
                        </div>
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Customer Referral Earning Settings') }}</span>
                    </button>
                    <a href="#referral_earning_section"
                       class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3" id="referral_earning_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Customer Referral Earning Settings') }}</h5>
                            <ul class="fs-12">
                                <li>{{ translate('Customer referral earning settings allow you to specify the wallet balance reward that customers will receive for successfully sharing their unique referral code with new customers who then make a purchase.') }}</li>
                                <li>{{ translate('This setting allows the admin to set the amount (in the default currency) that a referring customer will earn. The reward is added to their wallet when someone they refer places and completes their first order on the platform.') }}</li>
                            </ul>
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
    "use strict";

    $('#loyalty_point_status').on('change', function() {
        if($('#loyalty_point_status').is(':checked')){
            // $('.card-details-body').removeClass('d-none');
            $('#loyalty_point_exchange_rate').removeAttr('readonly').attr('required', 'required');
            $('#item_purchase_point').removeAttr('readonly').attr('required', 'required');
            $('#minimum_transfer_point').removeAttr('readonly').attr('required', 'required');
        }else{
            $('#loyalty_point_exchange_rate').attr('readonly',true).removeAttr('required');
            $('#item_purchase_point').attr('readonly',true).removeAttr('required');
            $('#minimum_transfer_point').attr('readonly',true).removeAttr('required');
        }
    });

    $('#ref_earning_status').on('change', function() {
        if($('#ref_earning_status').is(':checked')){
            // $('.card-details-body').removeClass('d-none');
            $('#ref_earning_exchange_rate').removeAttr('readonly').attr('required', 'required');
            $('#new_customer_discount_status').removeAttr('disabled');

            if($('#new_customer_discount_status').is(':checked')){
                $('#new_customer_discount_amount').removeAttr('readonly').attr('required', 'required');
                $('#new_customer_discount_amount_validity').removeAttr('readonly').attr('required', 'required');
                $('#new_customer_discount_amount_type').removeAttr('disabled').attr('required', 'required');
                $('#new_customer_discount_validity_type').removeAttr('disabled').attr('required', 'required');
            }
        }else{
            $('#ref_earning_exchange_rate').attr('readonly',true).removeAttr('required');
            $('#new_customer_discount_status').attr('disabled',true);

            if($('#new_customer_discount_status').is(':checked')){
                $('#new_customer_discount_amount').attr('readonly',true).removeAttr('required');
                $('#new_customer_discount_amount_validity').attr('readonly',true).removeAttr('required');
                $('#new_customer_discount_amount_type').attr('disabled',true).removeAttr('required');
                $('#new_customer_discount_validity_type').attr('disabled',true).removeAttr('required');
            }

        }
    }).trigger('change');

    $('#new_customer_discount_amount_type').on('change', function() {
        if($('#new_customer_discount_amount_type').val() == 'amount')
        {
            $('#percentage').addClass('d-none');
            $('#cuttency_symbol').removeClass('d-none');
            $('#new_customer_discount_amount').attr('max',99999999999);

        }
        else
        {
            $('#percentage').removeClass('d-none');
            $('#cuttency_symbol').addClass('d-none');
            $('#new_customer_discount_amount').attr('max',100);

        }
    });

</script>
@endpush
