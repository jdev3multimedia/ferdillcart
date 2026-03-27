@extends('layouts.admin.app')

@section('title', translate('messages.delivery_man_settings'))


@section('content')
@php use App\CentralLogics\Helpers; @endphp
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex align-items-center justify-content-between gap-1 w-100">
                <h1 class="page-header-title mr-3">
                    <span class="page-header-icon">
                        <img src="{{ asset('public/assets/admin/img/business.png') }}" class="w--26" alt="">
                    </span>
                    <span>
                        {{translate('business_setup')}}
                    </span>
                </h1>

            </div>
            @include('admin-views.business-settings.partials.nav-menu')
        </div>
        <!-- Page Header -->

        <!-- End Page Header -->
        <form action="{{ route('admin.business-settings.update-dm') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row g-2">
                <div class="col-lg-12">
                    <div class="card mb-20" id="basic_setup_section">
                        <div class="card-body">
                            <div class="mb-20">
                                <h3 class="mb-1">
                                    {{ translate('Basic Setup') }}
                                </h3>
                                <p class="mb-0 fs-12">
                                    {{ translate('Enable the required options to allow deliverymen to access these features from the app') }}
                                </p>
                            </div>
                            <div class="rounded p-xxl-20 p-3 bg-light">
                                <div class="row g-3">
                                    <div class="col-sm-6 col-lg-4">
                                        @php($toggle_dm_registration =  Helpers::get_business_settings('toggle_dm_registration') )
                                        <div class="form-group mb-0">
                                            <span class="d-flex align-items-center mb-2">
                                                <span class="text-dark pr-1">
                                                    {{ translate('messages.Deliveryman_Self_Registration') }}
                                                </span>
                                                <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.With_this_feature,_deliverymen_can_register_themselves_from_the_Customer_App,_Website_or_Deliveryman_App_or_Admin_Landing_Page._The_admin_will_receive_an_email_notification_and_can_accept_or_reject_the_request.') }}">
                                                    <i class="tio-info text-light-gray"></i>
                                                </span>
                                            </span>
                                            <label
                                                class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                                <span class="pr-1 d-flex align-items-center switch--label">
                                                    <span class="line--limit-1">
                                                        {{ translate('messages.Status') }}
                                                    </span>
                                                </span>
                                                <input type="checkbox"
                                                       data-id="dm_self_registration1"
                                                       data-type="toggle"
                                                       data-image-on="{{ asset('/public/assets/admin/img/modal/dm-self-reg-on.png') }}"
                                                       data-image-off="{{ asset('/public/assets/admin/img/modal/dm-self-reg-off.png') }}"
                                                       data-title-on="{{ translate('messages.Want_to_enable') }} <strong>{{ translate('messages.Deliveryman_Self_Registration?') }}</strong>"
                                                       data-title-off="{{ translate('messages.Want_to_disable') }} <strong>{{ translate('messages.Deliveryman_Self_Registration?') }}</strong>"
                                                       data-text-on="<p>{{ translate('messages.If_you_enable_this,_users_can_register_as_Deliverymen_from_the_Customer_App,_Website_or_Deliveryman_App_or_Admin_Landing_Page.') }}</p>"
                                                       data-text-off="<p>{{ translate('messages.If_you_disable_this,_this_feature_will_be_hidden_from_the_Customer_App,_Website_or_Deliveryman_App_or_Admin_Landing_Page.') }}</p>"
                                                       class="status toggle-switch-input dynamic-checkbox-toggle"

                                                       value="1"
                                                    name="toggle_dm_registration" id="dm_self_registration1"
                                                    {{ $toggle_dm_registration == 1 ? 'checked' : '' }}>
                                                <span class="toggle-switch-label text">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        @php($dm_maximum_orders =  Helpers::get_business_settings('dm_maximum_orders')   )
                                        <div class="form-group mb-0">
                                            <label class="form-label text-capitalize"
                                                for="dm_maximum_orders">
                                                <div class="d-flex align-items-center">
                                                    <span class="line--limit-1 flex-grow pr-1">{{ translate('Maximum Assigned Order Limit') }} </span>
                                                    <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.Set_the_maximum_order_limit_a_Deliveryman_can_take_at_a_time.') }}">
                                                        <i class="tio-info text-light-gray"></i>
                                                    </span>
                                                    <span class="text-danger">*</span>
                                                </div>
                                            </label>
                                            <input type="number" name="dm_maximum_orders" class="form-control"
                                                id="dm_maximum_orders" min="1"
                                                value="{{ $dm_maximum_orders ?? 1 }}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        @php($canceled_by_deliveryman = Helpers::get_business_settings('canceled_by_deliveryman'))
                                        <div class="form-group mb-0">
                                            <label class="input-label text-capitalize d-flex align-items-center"><span
                                                    class="line--limit-1 pr-1">{{ translate('Deliveryman can cancel Order?') }}</span>
                                                <span class="form-label-secondary"
                                                data-toggle="tooltip" data-placement="right"
                                                data-original-title="{{ translate('Admin can enable/disable Deliveryman’s order cancellation option in the respective app.') }}"><i class="tio-info text-light-gray"></i></span></label>

                                            <label
                                                class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                                <span class="pr-1 d-flex align-items-center switch--label">
                                                    <span class="line--limit-1">
                                                        {{ translate('messages.Can cancel') }}
                                                    </span>
                                                </span>
                                                <input type="checkbox" class="status toggle-switch-input" value="1"
                                                    name="canceled_by_deliveryman" id="canceled_by_deliveryman" {{ $canceled_by_deliveryman == 1 ? 'checked' : '' }}>
                                                <span class="toggle-switch-label text">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($toggle_dm_registration)
                            <div class="fs-12 text-dark px-3 py-2 bg-opacity-10 rounded bg-info mt-20">
                                <div class="d-flex align-items-center gap-2 mb-0">
                                    <span class="text-info fs-16">
                                        <i class="tio-light-on"></i>
                                    </span>
                                    <span>
                                        {{ translate('You may setup') }} <strong>{{ translate('Registration Form ') }}</strong> {{ translate('from') }} <a target="_blank" href="{{ route('deliveryman.create') }}" class="text-primary">{{ translate('Deliveryman Registration Form') }}</a> {{ translate('page to work properly.') }}
                                    </span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card mb-20">
                        <div class="card-body">
                            <div class="row g-3 align-items-center">
                                <div class="col-xxl-9 col-lg-8 col-md-7 col-sm-6">
                                    <div>
                                        <h3 class="mb-1">
                                            {{ translate('Tips For Deliveryman') }}
                                        </h3>
                                        <p class="mb-0 fs-12">
                                            {{ translate('Customers can give tips to deliverymen during checkout from the Customer App & Website.') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-lg-4 col-md-5 col-sm-6">
                                    @php($dm_tips_status = Helpers::get_business_settings('dm_tips_status'))
                                    <div class="form-group mb-0">
                                        <label class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                            <span class="line--limit-1 switch--label">
                                                {{ translate('messages.Status') }}
                                            </span>
                                            <input type="checkbox"
                                                    data-id="dm_tips_status"
                                                    data-type="toggle"
                                                    data-image-on="{{ asset('/public/assets/admin/img/modal/dm-tips-on.png') }}"
                                                    data-image-off="{{ asset('/public/assets/admin/img/modal/dm-tips-off.png') }}"
                                                    data-title-on="{{ translate('messages.Want_to_enable') }} <strong>{{ translate('messages.Tips_for_Deliveryman_feature?') }}</strong>"
                                                    data-title-off="{{ translate('messages.Want_to_disable') }} <strong>{{ translate('messages.Tips_for_Deliveryman_feature?') }}</strong>"
                                                    data-text-on="<p>{{ translate('messages.If_you_enable_this,_Customers_can_give_tips_to_a_deliveryman_during_checkout.') }}</p>"
                                                    data-text-off="<p>{{ translate('messages.If_you_disable_this,_the_Tips_for_Deliveryman_feature_will_be_hidden_from_the_Customer_App_and_Website.') }}</p>"
                                                    class="status toggle-switch-input dynamic-checkbox-toggle"
                                                    value="1"
                                                name="dm_tips_status" id="dm_tips_status"
                                                {{ $dm_tips_status == '1' ? 'checked' : '' }}>
                                            <span class="toggle-switch-label text">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="fs-12 text-dark px-3 py-2 rounded bg-warning-10 mt-20">
                                <div class="d-flex align-items-center gap-2 mb-0">
                                    <span class="text-warning fs-14">
                                        <i class="tio-info"></i>
                                    </span>
                                    <span class="color-656566">
                                        {{ translate('Admins do not receive any commission from tips given to deliverymen; these goes entirely to the deliveryman earning.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-20" id="app_setup_section">
                        <div class="card-body">
                            <div class="mb-20">
                                <h3 class="mb-1">
                                    {{ translate('Deliveryman App Setup') }}
                                </h3>
                                <p class="mb-0 fs-12">
                                    {{ translate('Set up all necessary app configurations from here') }}
                                </p>
                            </div>
                            <div class="rounded p-xxl-20 p-3 bg-light">
                                <div class="row g-3">
                                    <div class="col-sm-6 col-lg-4">
                                        @php($show_dm_earning = Helpers::get_business_settings('show_dm_earning')  )
                                        <div class="form-group mb-0">
                                            <span class="d-flex align-items-center mb-2">
                                                <span class="text-dark pr-1">
                                                    {{ translate('messages.Show Earnings in App') }}
                                                </span>
                                                <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.With_this_feature,_Deliverymen_can_see_their_earnings_on_a_specific_order_while_accepting_it.') }}">
                                                    <i class="tio-info text-light-gray"></i>
                                                </span>
                                            </span>
                                            <label
                                                class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                                <span class="pr-1 d-flex align-items-center switch--label">
                                                    <span class="line--limit-1">
                                                        {{ translate('Status') }}
                                                    </span>
                                                </span>
                                                <input type="checkbox"
                                                        data-id="show_dm_earning"
                                                        data-type="toggle"
                                                        data-image-on="{{ asset('/public/assets/admin/img/modal/show-earning-in-apps-on.png') }}"
                                                        data-image-off="{{ asset('/public/assets/admin/img/modal/show-earning-in-apps-off.png') }}"
                                                        data-title-on="{{ translate('messages.Want_to_enable') }} <strong>{{ translate('messages.Show_Earnings_in_App?') }}</strong>"
                                                        data-title-off="{{ translate('messages.Want_to_disable') }} <strong>{{ translate('messages.Show_Earnings_in_App?') }}</strong>"
                                                        data-text-on="<p>{{ translate('messages.If_you_enable_this,_Deliverymen_can_see_their_earning_per_order_request_from_the_Order_Details_page_in_the_Deliveryman_App.') }}</p>"
                                                        data-text-off="<p>{{ translate('messages.If_you_disable_this,_the_feature_will_be_hidden_from_the_Deliveryman_App.') }}</p>"
                                                        class="status toggle-switch-input dynamic-checkbox-toggle"

                                                        value="1"
                                                    name="show_dm_earning" id="show_dm_earning"
                                                    {{ $show_dm_earning == 1 ? 'checked' : '' }}>
                                                <span class="toggle-switch-label text">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        @php($dm_picture_upload_status = Helpers::get_business_settings('dm_picture_upload_status'))
                                        <div class="form-group mb-0">
                                            <span class="d-flex align-items-center mb-2">
                                                <span class="text-dark pr-1">
                                                    {{ translate('messages.Take Picture for Delivery Completing') }}
                                                </span>
                                                <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.If_enabled,_deliverymen_will_see_an_option_to_take_pictures_of_the_delivered_products_when_he_swipes_the_delivery_confirmation_slide.') }}">
                                                    <i class="tio-info text-light-gray"></i>
                                                </span>
                                            </span>
                                            <label
                                                class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                                <span class="pr-1 d-flex align-items-center switch--label">
                                                    <span class="line--limit-1">
                                                        {{ translate('messages.Status') }}
                                                    </span>
                                                </span>
                                                <input type="checkbox"
                                                        data-id="dm_picture_upload_status"
                                                        data-type="toggle"
                                                        data-image-on="{{ asset('/public/assets/admin/img/modal/dm-self-reg-on.png') }}"
                                                        data-image-off="{{ asset('/public/assets/admin/img/modal/dm-self-reg-off.png') }}"
                                                        data-title-on="{{ translate('messages.Want_to_enable') }} <strong>{{ translate('messages.picture_upload_before_complete?') }}</strong>"
                                                        data-title-off="{{ translate('messages.Want_to_disable') }} <strong>{{ translate('messages.picture_upload_before_complete?') }}</strong>"
                                                        data-text-on="<p>{{ translate('messages.If_you_enable_this,_delivery_man_can_upload_order_proof_before_order_delivery.') }}</p>"
                                                        data-text-off="<p>{{ translate('messages.If_you_disable_this,_this_feature_will_be_hidden_from_the_delivery_man_app.') }}</p>"
                                                        class="status toggle-switch-input dynamic-checkbox-toggle"
                                                        value="1"
                                                    name="dm_picture_upload_status" id="dm_picture_upload_status"
                                                    {{ $dm_picture_upload_status == 1 ? 'checked' : '' }}>
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
                    <div class="card" id="cash_in_hand_section">
                        <div class="card-body">
                            <div class="mb-20">
                                <h3 class="mb-1">
                                    {{ translate('Cash in Hand Controls') }}
                                </h3>
                                <p class="mb-0 fs-12">
                                    {{ translate('Configure the necessary settings for cash-in-hand management for deliverymen.') }}
                                </p>
                            </div>
                            <div class="rounded p-xxl-20 p-3 bg-light2">
                                <div class="row g-3">
                                    <div class="col-sm-6 col-lg-4">
                                        @php($cash_in_hand_overflow = Helpers::get_business_settings('cash_in_hand_overflow_delivery_man'))
                                        <div class="form-label  mb-0 ">
                                            <span class="d-flex align-items-center mb-2">
                                                <span class="text-dark pr-1">
                                                    {{ translate('messages.Suspend on Cash In Hand Overflow') }}
                                                </span>
                                                <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.If_enabled,_delivery_men_will_be_automatically_suspended_by_the_system_when_their_‘Cash_in_Hand’_limit_is_exceeded.') }}">
                                                    <i class="tio-info text-light-gray"></i>
                                                </span>
                                            </span>
                                            <label
                                                class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                                <span class="pr-1 d-flex align-items-center switch--label">
                                                    <span class="line--limit-1">
                                                        {{ translate('messages.Status') }}
                                                    </span>
                                                </span>
                                                <input type="checkbox"
                                                       data-id="cash_in_hand_overflow"
                                                       data-type="toggle"
                                                       data-image-on="{{ asset('/public/assets/admin/img/modal/show-earning-in-apps-on.png') }}"
                                                       data-image-off="{{ asset('/public/assets/admin/img/modal/show-earning-in-apps-off.png') }}"
                                                       data-title-on="{{ translate('Want_to_enable') }} <strong>{{ translate('Cash_In_Hand_Overflow') }}</strong>?"
                                                       data-title-off="{{ translate('Want_to_disable') }} <strong>{{ translate('Cash_In_Hand_Overflow') }}</strong>?"
                                                       data-text-on="<p>{{ translate('If_enabled,_delivery_men_have_to_provide_collected_cash_by_themselves.') }}</p>"
                                                       data-text-off="<p>{{ translate('If_disabled,_delivery_men_do_not_have_to_provide_collected_cash_by_themselves.') }}</p>"
                                                       class="status toggle-switch-input dynamic-checkbox-toggle"
                                                       value="1"
                                                       name="cash_in_hand_overflow_delivery_man" id="cash_in_hand_overflow"
                                                    {{ $cash_in_hand_overflow == 1 ? 'checked' : '' }}>
                                                <span class="toggle-switch-label text">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-4">
                                        @php($dm_max_cash_in_hand =  Helpers::get_business_settings('dm_max_cash_in_hand') )
                                        <div class="form-label mb-0">
                                            <label class="d-flex text-capitalize"
                                                   for="dm_max_cash_in_hand">
                                                <span class="line--limit-1">
                                                    {{translate('Cash In hand Max Amount')}} ({{ Helpers::currency_symbol() }})
                                                </span>
                                                <span data-toggle="tooltip" data-placement="right" data-original-title="{{translate('Deliveryman_can_not_accept_any_orders_when_the_Cash_In_Hand_limit_exceeds_and_must_deposit_the_amount_to_the_admin_before_accepting_new_orders')}}" class="input-label-secondary"><i class="tio-info text-light-gray"></i></span>
                                           <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" name="dm_max_cash_in_hand" class="form-control"
                                                   id="dm_max_cash_in_hand" min="0" step="{{ Helpers::getDecimalPlaces() }}"
                                                   value="{{ $dm_max_cash_in_hand ?? '' }}" {{ $cash_in_hand_overflow  == 1 ? 'required' : 'readonly' }} >

                                                 <small id="dm_max_cash_in_hand_error" class="d-none">
                                                     <span class="text-info">{{ translate('Amount must be greater then Minimum Payable Amount') }}</span>
                                                </small>

                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        @php($min_amount_to_pay_dm = Helpers::get_business_settings('min_amount_to_pay_dm')  )
                                        <div class="form-label mb-0">
                                            <label class="text-capitalize"
                                                   for="min_amount_to_pay_dm">
                                                <span>
                                                    {{ translate('Minimum Payable Amount') }} ({{ Helpers::currency_symbol() }})

                                                </span>

                                                <span class="form-label-secondary"
                                                      data-toggle="tooltip" data-placement="right"
                                                      data-original-title="{{ translate('Enter_the_minimum_cash_amount_delivery_men_can_pay') }}"><i class="tio-info text-light-gray"></i></span>
                                            <span class="text-danger">*</span>
                                                    </label>
                                            <input type="number" name="min_amount_to_pay_dm" class="form-control"
                                                   id="min_amount_to_pay_dm" min="0" step="{{ Helpers::getDecimalPlaces() }}"
                                                   value="{{ $min_amount_to_pay_dm ?? '' }}"  {{ $cash_in_hand_overflow  == 1 ? 'required' : 'readonly' }} >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="fs-12 text-dark px-3 py-2 bg-opacity-10 rounded bg-info mt-20">
                                <div class="d-flex align-items-center gap-2 mb-0">
                                    <span class="text-info fs-16">
                                        <i class="tio-light-on"></i>
                                    </span>
                                    <span>
                                        {{ translate('Configure the maximum cash amount a delivery person can hold and set a minimum payment threshold for better financial oversight.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php($dm_loyality_point_status = Helpers::get_business_settings('dm_loyality_point_status')  )
                    @php($dm_loyality_point_per_order = Helpers::get_business_settings('dm_loyality_point_per_order')  )
                    @php($dm_loyality_point_conversion_rate = Helpers::get_business_settings('dm_loyality_point_conversion_rate')  )
                    @php($dm_min_loyality_point_to_convert = Helpers::get_business_settings('dm_min_loyality_point_to_convert')  )

                    <div class="card mt-20 card-container" id="loyalty_point_section">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-2 flex-sm-nowrap flex-wrap">
                                <div>
                                    <h4 class="mb-1">{{translate('Loyalty Point')}}</h4>
                                    <p class="fs-12 m-0">{{translate('If enabled, deliverymen will earn a certain number of points for each successful delivery.')}}</p>
                                </div>
                                <div class="d-flex flex-sm-nowrap flex-wrap justify-content-end justify-content-end align-items-center gap-3">
                                    <div class="view_toggle_btn fz--14px info-dark cursor-pointer text-decoration-underline font-semibold d-flex align-items-center gap-1">
                                        {{ translate('messages.view') }}
                                        <i class="tio-chevron-down fs-22"></i>
                                    </div>
                                    <div class="mb-0">
                                        <label class="toggle-switch toggle-switch-sm mb-0">
                                            <input type="checkbox" data-type="toggle" class="status toggle-switch-input" name="dm_loyality_point_status" id="dm_loyality_point_status" value="1" {{ $dm_loyality_point_status == 1 ? 'checked' : '' }}>
                                            <span class="toggle-switch-label text mb-0">
                                                <span
                                                    class="toggle-switch-indicator">
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-details-body {{ !$dm_loyality_point_status ? 'd-none' : '' }} ">
                                <div class="bg-light2  rounded p-xxl-20 p-3 mt-20">
                                    <div class="row g-3">
                                        <div class="col-sm-6 col-lg-4">
                                            <div class="form-group mb-0">
                                                <label class="form-label text-capitalize" for="dm_loyality_point_per_order">
                                                    <div class="d-flex align-items-center">
                                                        <span class="line--limit-1 flex-grow pr-1">{{ translate('Loyalty Point Earn Per Order') }} </span>
                                                        <span class="input-label-secondary"
                                                        data-toggle="tooltip" data-placement="right"
                                                        data-original-title="{{ translate('Specify the percentage of the total order amount that a deliveryman will earn as loyalty points.') }}"><i class="tio-info text-muted"></i>
                                                    </span>
                                                        <span class="text-danger">*</span>
                                                    </div>
                                                </label>
                                                <input type="number" name="dm_loyality_point_per_order" class="form-control" min="0"   max="9999999999"  id="dm_loyality_point_per_order" placeholder="1" value="{{ $dm_loyality_point_per_order ?? ''}}" {{ $dm_loyality_point_status == 1 ? 'required':'readonly' }}>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-4">
                                            <div class="form-group mb-0">
                                                <label class="form-label text-capitalize" for="dm_loyality_point_conversion_rate">
                                                    <div class="d-flex align-items-center">
                                                        <span class="line--limit-1 flex-grow pr-1">{{ Helpers::currency_symbol() }} {{ translate('1.00 Equivalent To Points') }} </span>
                                                        <span class="input-label-secondary"
                                                        data-toggle="tooltip" data-placement="right"
                                                        data-original-title="{{ translate('Set the value of how many loyalty points are equal to 1 USD for converting points into wallet money.') }}"><i class="tio-info text-muted"></i>
                                                    </span>
                                                    <span class="text-danger">*</span>
                                                    </div>
                                                </label>
                                                <input type="number" name="dm_loyality_point_conversion_rate"  min="0" max="999999999"  class="form-control" id="dm_loyality_point_conversion_rate" placeholder="100" value="{{ $dm_loyality_point_conversion_rate ?? ''}}" {{ $dm_loyality_point_status == 1 ? 'required':'readonly' }}>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-4">
                                            <div class="form-group mb-0">
                                                <label class="form-label text-capitalize" for="dm_min_loyality_point_to_convert">
                                                    <div class="d-flex align-items-center">
                                                        <span class="line--limit-1 flex-grow pr-1">{{ translate('Minimum Point Required To Convert') }} </span>
                                                        <span class="input-label-secondary"
                                                        data-toggle="tooltip" data-placement="right"
                                                        data-original-title="{{ translate('Enter the minimum number of points a deliveryman must collect before they can convert them into a wallet balance.') }}"><i class="tio-info text-muted"></i>
                                                    </span>
                                                        <span class="text-danger">*</span>
                                                    </div>
                                                </label>
                                                <input type="number" name="dm_min_loyality_point_to_convert" min="0" max="999999999"  class="form-control" id="dm_min_loyality_point_to_convert" placeholder="200" value="{{ $dm_min_loyality_point_to_convert ?? '' }}" {{ $dm_loyality_point_status == 1 ? 'required':'readonly' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    @php($dm_referal_status = Helpers::get_business_settings('dm_referal_status')  )
                    @php($dm_referal_amount = Helpers::get_business_settings('dm_referal_amount')  )
                    @php($dm_referal_bonus = Helpers::get_business_settings('dm_referal_bonus')  )

                    <div class="card mt-20 card-container" id="referral_earning_section">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-2 flex-sm-nowrap flex-wrap">
                                <div>
                                    <h4 class="mb-1">{{translate('Deliveryman Referral Earning Settings')}}</h4>
                                    <p class="fs-12 m-0">{{translate('Allow Drivers to refer your app to friends and family using a unique code and earn rewards.')}}</p>
                                </div>
                                <div class="d-flex flex-sm-nowrap flex-wrap justify-content-end justify-content-end align-items-center gap-3">
                                    <div class="view_toggle_btn fz--14px info-dark cursor-pointer text-decoration-underline font-semibold d-flex align-items-center gap-1">
                                        {{ translate('messages.view') }}
                                        <i class="tio-chevron-down fs-22"></i>
                                    </div>
                                    <div class="mb-0">
                                        <label class="toggle-switch toggle-switch-sm mb-0">
                                            <input type="checkbox" data-type="toggle" class="status toggle-switch-input" name="dm_referal_status" id="dm_referal_status" value="1" {{ $dm_referal_status == 1 ? 'checked' : '' }} >
                                            <span class="toggle-switch-label text mb-0">
                                                <span
                                                    class="toggle-switch-indicator">
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-details-body {{ !$dm_referal_status ? 'd-none' : '' }}">
                                <div class="bg-light2 d-flex flex-column gap-4 rounded p-xxl-20 p-3 mt-20">
                                    <div class="row g-3">
                                        <div class="col-md-6 col-lg-4">
                                            <div>
                                                <h4 class="mb-1">{{translate('Who Share the Code')}}</h4>
                                                <p class="fs-12 m-0">{{translate('Set the reward amount that drivers will earn for each successful referral. The reward will be given to the person who uses the referral code during signup and completes their first order.')}}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-8">
                                            <div class="bg-white rounded p-xxl-20 p-2">
                                                <div class="form-group mb-0">
                                                    <label class="form-label text-capitalize" for="dm_referal_amount">
                                                        <div class="d-flex align-items-center">
                                                            <span class="line--limit-1 flex-grow pr-1">{{ translate('Earning Per Referral') }} ({{ Helpers::currency_symbol() }})  <span class="text-danger">*</span> </span>
                                                        </div>
                                                    </label>
                                                    <input type="number" name="dm_referal_amount"   min="0" max="999999999" step="{{ Helpers::getDecimalPlaces() }}" class="form-control " id="dm_referal_amount" placeholder="100" value="{{ $dm_referal_amount??'' }}" {{ $dm_referal_status ? 'required' : 'readonly' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6 col-lg-4">
                                            <div>
                                                <h4 class="mb-1">{{translate('Who Use the Code')}}</h4>
                                                <p class="fs-12 m-0">{{translate('Set the reward amount that drivers receive when signing up with a referral code & completes first order')}}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-8">
                                            <div class="bg-white rounded p-xxl-20 p-2">
                                                <div class="form-group mb-0">
                                                    <label class="form-label text-capitalize" for="dm_referal_bonus">
                                                        <div class="d-flex align-items-center">
                                                            <span class="line--limit-1 flex-grow pr-1">{{ translate('Bonus In Wallet') }} ({{ Helpers::currency_symbol() }}) <span class="text-danger">*</span> </span>
                                                        </div>
                                                    </label>
                                                    <input type="number" name="dm_referal_bonus" min="0" max="999999999" step="{{ Helpers::getDecimalPlaces() }}" class="form-control " id="dm_referal_bonus" placeholder="100" value="{{ $dm_referal_bonus  ?? ''}}" {{ $dm_referal_status ? 'required' : 'readonly' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('admin-views.partials._floating-submit-button')
        </form>
    </div>
    <div id="global_guideline_offcanvas" style="overflow-y: auto;"
         class="custom-offcanvas d-flex flex-column justify-content-between global_guideline_offcanvas">
        <div>
            <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                <h3 class="mb-0">{{ translate('Deliveryman Settings Guideline') }}</h3>
                <button type="button"
                        class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary offcanvas-close fz-15px p-0"
                        aria-label="Close">&times;</button>
            </div>

            <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                    <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed"
                            type="button" data-toggle="collapse" data-target="#basic_setup_guide" aria-expanded="true">
                        <div
                            class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                            <i class="tio-down-ui"></i>
                        </div>
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Basic Setup') }}</span>
                    </button>
                    <a href="#basic_setup_section"
                       class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3 show" id="basic_setup_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Basic Setup') }}</h5>
                            <p class="fs-12 mb-0">
                                {{ translate('Control deliveryman-related settings, such as: ') }}
                            </p>
                            <ul class="fs-12">
                                <li>{{ translate('Deliveryman registration availability') }}</li>
                                <li>{{ translate('Maximum number of active orders') }}</li>
                                <li>{{ translate('Allowing tips from customers') }}</li>
                                <li>{{ translate('Order cancellation permission') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                    <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed"
                            type="button" data-toggle="collapse" data-target="#app_setup_guide" aria-expanded="true">
                        <div
                            class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                            <i class="tio-down-ui"></i>
                        </div>
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Deliveryman App Setup') }}</span>
                    </button>
                    <a href="#app_setup_section"
                       class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3" id="app_setup_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Deliveryman App Setup') }}</h5>
                            <p class="fs-12 mb-0">
                                {{ translate('The Deliveryman App Setup helps the admin adjust crucial settings for deliverymen’s app usage. You can control: ') }}
                            </p>
                            <ul class="fs-12">
                                <li>{{ translate('Deliverymen can see their earnings') }}</li>
                                <li>{{ translate('Delivery photo is required as proof') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                    <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed"
                            type="button" data-toggle="collapse" data-target="#cash_in_hand_guide" aria-expanded="true">
                        <div
                            class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                            <i class="tio-down-ui"></i>
                        </div>
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Cash-in-Hand Controls') }}</span>
                    </button>
                    <a href="#cash_in_hand_section"
                       class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3" id="cash_in_hand_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Cash-in-Hand Controls') }}</h5>
                            <p class="fs-12 mb-0">
                                {{ translate('Cash-in-hand control allows the platform to monitor and limit the amount of cash collected by deliverymen from Cash on Delivery (COD) orders. This feature helps reduce financial risk and ensures timely settlement between the deliveryman and the platform.') }}
                            </p>
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
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Loyalty Point Setup') }}</span>
                    </button>
                    <a href="#loyalty_point_section"
                       class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3" id="loyalty_point_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Loyalty Point Setup') }}</h5>
                            <p class="fs-12 mb-0">
                                {{ translate('Allowing the option that deliverymen will get points when they complete the order delivery successfully.') }}
                            </p>
                            <ul class="fs-12">
                                <li>{{ translate('Loyalty Points earn per order: this setup is for setting the value of how much the deliveryman will earn when completing the delivery') }}</li>
                                <li>{{ translate('$1.00 Equivalent to Points: This section is for calculating the wallet value of loyalty points when converting') }}</li>
                                <li>{{ translate('Minimum Point Required to Convert: This setup is for inserting the minimum number of points a deliveryman must have to convert points into a wallet balance.') }}</li>
                            </ul>
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
                        <span class="font-semibold text-left fs-14 text-title">{{ translate('Referral Earning') }}</span>
                    </button>
                    <a href="#referral_earning_section"
                       class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                </div>
                <div class="collapse mt-3" id="referral_earning_guide">
                    <div class="card card-body">
                        <div class="">
                            <h5 class="mb-3">{{ translate('Referral Earning') }}</h5>
                            <ul class="fs-12">
                                <li>{{ translate('Deliveryman referral earning settings allow you to specify the wallet balance reward that the deliveryman will receive for successfully sharing their unique referral code with a new deliveryman for registration.') }}</li>
                                <li>{{ translate('This setting allows the admin to set the amount (in the default currency) that a referring deliveryman will earn. The reward is added to their wallet when someone they refer places and completes their registration on the platform.') }}</li>
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
        $(document).on('ready', function () {

            function toggleFields(checkbox, fields) {
                if ($(checkbox).is(':checked')) {
                    $(fields).attr('required', true).removeAttr('readonly');
                } else {
                    $(fields).attr('required', false).attr('readonly', true);
                }
            }

            $('#dm_referal_status').on('change', function () {
                toggleFields(this, '#dm_referal_amount, #dm_referal_bonus');
            }).trigger('change');

            $('#dm_loyality_point_status').on('change', function () {
                toggleFields(this, '#dm_loyality_point_per_order, #dm_loyality_point_conversion_rate, #dm_min_loyality_point_to_convert');
            }).trigger('change');

        });
        function validateDmCash() {
            const maxCash = Number($('#dm_max_cash_in_hand').val()) || 0;
            const minPay  = Number($('#min_amount_to_pay_dm').val()) || 0;
            const isInvalid = maxCash < minPay;
            $('#dm_max_cash_in_hand_error').toggleClass('d-none', !isInvalid);
            $('#submit').prop('disabled', isInvalid);
        }

        $('#dm_max_cash_in_hand, #min_amount_to_pay_dm').on('input', validateDmCash);
        $('form').on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                return false;
            }
        });

    </script>
@endpush
