@extends('layouts.admin.app')

@section('title', translate('store_setup'))


@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
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
        <form action="{{ route('admin.business-settings.update-store') }}" method="post" enctype="multipart/form-data">
            @csrf
            @php($name = \App\Models\BusinessSetting::where('key', 'business_name')->first())

            <div class="row g-3">
                @php($default_location = \App\Models\BusinessSetting::where('key', 'default_location')->first())
                @php($default_location = $default_location->value ? json_decode($default_location->value, true) : 0)
                <div class="col-lg-12">
                    <div class="card mb-20" id="general_setup_section">
                        <div class="card-body">
                            <div class="mb-20">
                                <div class="row g-1 align-items-center">
                                    <div class="col-xxl-9 col-lg-8 col-md-7 col-sm-6">
                                        <div>
                                            <h4 class="mb-1">
                                                {{ translate('General Setup') }}
                                            </h4>
                                            <p class="mb-0 fs-12">
                                                {{ translate('Manage the basic settings that control how vendors operate in your platform.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-light rounded p-xxl-20 p-3">
                                <div class="row g-3 align-items-end">
                                    <div class="col-lg-4 col-sm-6">
                                        @php($canceled_by_store = \App\Models\BusinessSetting::where('key', 'canceled_by_store')->first())
                                        @php($canceled_by_store = $canceled_by_store ? $canceled_by_store->value : 0)
                                        <div class="form-group mb-0">
                                            <label class="input-label text-capitalize d-flex alig-items-center"><span
                                                    class="line--limit-1 text-title">{{ translate('Can_a_Vendor_Cancel_Order?') }}
                                                </span><span class="input-label-secondary text--title" data-toggle="tooltip"
                                                    data-placement="right"
                                                    data-original-title="{{ translate('Admin_can_enable/disable_Vendor’s_order_cancellation_option.') }}">
                                                    <i class="tio-info text-muted"></i>
                                                </span>
                                            </label>
                                            <div class="form-group mb-0">
                                                <label class="toggle-switch h--45px align-items-center toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                                    <span class="pr-1 d-flex align-items-center switch--label">
                                                        <span class="line--limit-1 text-title">
                                                            {{translate('Can cancel') }}
                                                        </span>
                                                    </span>
                                                    <input type="checkbox" data-id="canceled_by_store" data-type="toggle"
                                                    data-image-on="{{ asset('/public/assets/admin/img/modal/info-warning.png') }}"
                                                    data-image-off="{{ asset('/public/assets/admin/img/modal/info-warning.png') }}"
                                                    data-title-on="<strong>{{ translate('Are you sure to allow vendor to cancel orders?') }}</strong>"
                                                    data-title-off="<strong>{{ translate('Are you sure to not allow vendor to cancel orders?') }}</strong>"
                                                    data-text-on="{{ translate('Vendors will be able to cancel orders directly from their panel if they cannot fulfill them.') }}"
                                                    data-text-off="{{ translate('Vendors will no longer have the option to cancel. They will need to contact the admin to request any order cancellations.') }}"
                                                    data-footer-text-on="<div class='text-center text-info mt-5'>{{ translate('Note : Don’t forget to save the information before leaving this page ') }}</div>"
                                                    data-footer-text-off="<div class='text-center text-info mt-5'>{{ translate('Note : Don’t forget to save the information before leaving this page ') }}</div>"
                                                    class="status toggle-switch-input dynamic-checkbox-toggle"
                                                    name="canceled_by_store" id="canceled_by_store" value="1"
                                                    {{ $canceled_by_store ? 'checked' : '' }}>

                                                    <span class="toggle-switch-label text">
                                                        <span class="toggle-switch-indicator"></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6">
                                        @php($store_self_registration = \App\Models\BusinessSetting::where('key', 'toggle_store_registration')->first())
                                        @php($store_self_registration = $store_self_registration ? $store_self_registration->value : 0)
                                        <div class="form-group mb-0">
                                            <span class="mb-2 d-flex align-items-center">
                                                <span class="text-title fs-14">
                                                    {{ translate('Vendor_self_registration') }}
                                                </span>
                                                <span class="form-label-secondary text-danger d-flex align-items-center gap-1"
                                                    data-toggle="tooltip" data-placement="right"
                                                    data-original-title="{{ translate('A_vendor_can_send_a_registration_request_through_their_vendor_or_customer.') }}"><i class="tio-info text-muted ps--3"></i>
                                                </span>
                                            </span>
                                            <label
                                                class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                                <span class="pr-1 d-flex align-items-center switch--label">
                                                    <span class="line--limit-1 text-title">
                                                        {{ translate('Self Registration') }}
                                                    </span>
                                                </span>

                                                <input type="checkbox" data-id="store_self_registration" data-type="toggle"
                                                    data-image-on="{{ asset('/public/assets/admin/img/modal/info-warning.png') }}"
                                                    data-image-off="{{ asset('/public/assets/admin/img/modal/info-warning.png') }}"
                                                    data-title-on="<strong>{{ translate('Are you sure to enable vendor Self Registration?') }}</strong>"
                                                    data-title-off="<strong>{{ translate('Are you sure to disable vendor Self Registration?') }}</strong>"
                                                    data-text-on="{{ translate('This allows new business owners to sign up and apply to sell on your platform by themselves.') }}"
                                                    data-text-off="{{ translate('After disable the self-registration link will be hidden. You will need to manually add every new vendor from the admin panel.') }}"
                                                    data-footer-text-on="<div class='text-center text-info mt-5'>{{ translate('Note : Don’t forget to save the information before leaving this page ') }}</div>"
                                                    data-footer-text-off="<div class='text-center text-info mt-5'>{{ translate('Note : Don’t forget to save the information before leaving this page ') }}</div>"
                                                    class="status toggle-switch-input dynamic-checkbox-toggle"
                                                    name="store_self_registration" id="store_self_registration" value="1"
                                                    {{ $store_self_registration ? 'checked' : '' }}>
                                                <span class="toggle-switch-label text">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-4">
                                        @php($product_gallery = \App\Models\BusinessSetting::where('key', 'product_gallery')->first()?->value ?? 0)
                                        <div class="form-group mb-0">
                                            <span class="mb-2 d-flex align-items-center">
                                                <span class="text-title">
                                                    {{translate('Product_Gallery') }}
                                                </span>
                                                <span class="form-label-secondary text-danger d-flex align-items-center gap-1"
                                                    data-toggle="tooltip" data-placement="right"
                                                    data-original-title="{{ translate('If_you_enable_this,_any_vendor_can_duplicate_product_and_create_a_new_product_by_use_this.')}}"><i class="tio-info text-muted ps--3"></i>
                                                </span>
                                            </span>
                                            <label
                                                class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                                <span class="pr-1 d-flex align-items-center switch--label">
                                                    <span class="line--limit-1 text-title">
                                                        {{translate('Gallery') }}
                                                    </span>
                                                </span>


                                                <input type="checkbox" data-id="product_gallery" data-type="toggle"
                                                    data-image-on="{{ asset('/public/assets/admin/img/modal/info-warning.png') }}"
                                                    data-image-off="{{ asset('/public/assets/admin/img/modal/info-warning.png') }}"
                                                    data-title-on="<strong>{{ translate('Are you sure to enable Product Gallery?') }}</strong>"
                                                    data-title-off="<strong>{{ translate('Are you sure to disable Product Gallery?') }}</strong>"
                                                    data-text-on="{{ translate('This allows vendors to duplicate products and create new products using the gallery.') }}"
                                                    data-text-off="{{ translate('If disabled, vendors will not be able to duplicate products or create new products using the gallery.') }}"
                                                    data-footer-text-on="<div class='text-center text-info mt-5'>{{ translate('Note : Don’t forget to save the information before leaving this page ') }}</div>"
                                                    data-footer-text-off="<div class='text-center text-info mt-5'>{{ translate('Note : Don’t forget to save the information before leaving this page ') }}</div>"
                                                    class="status toggle-switch-input dynamic-checkbox-toggle"
                                                    name="product_gallery" id="product_gallery" value="1"
                                                    {{ $product_gallery ? 'checked' : '' }}>
                                                <span class="toggle-switch-label text">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4 {{ $product_gallery == 1 ? ' ' : 'd-none' }}  access_all_products">
                                        @php($access_all_products = \App\Models\BusinessSetting::where('key', 'access_all_products')->first()?->value ?? 0)
                                        <div class="form-group mb-0">
                                            <span class="mb-2 d-flex align-items-center">
                                                <span class="text-title">
                                                    {{translate('access_all_products') }}
                                                </span>
                                                <span class="form-label-secondary text-danger d-flex align-items-center gap-1"
                                                    data-toggle="tooltip" data-placement="right"
                                                    data-original-title="{{ translate('If_you_enable_this_vendors_can_access_all_products_of_other_vendors.')}}"><i class="tio-info text-muted ps--3"></i>
                                                </span>
                                            </span>
                                            <label
                                                class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                                <span class="pr-1 d-flex align-items-center switch--label">
                                                    <span class="line--limit-1 text-title">
                                                        {{translate('Can Edit') }}
                                                    </span>
                                                </span>
                                                <input type="checkbox" data-id="access_all_products" data-type="toggle"
                                                    data-image-on="{{ asset('/public/assets/admin/img/modal/info-warning.png') }}"
                                                    data-image-off="{{ asset('/public/assets/admin/img/modal/info-warning.png') }}"
                                                    data-title-on="<strong>{{ translate('Are you sure to enable Access All Products?') }}</strong>"
                                                    data-title-off="<strong>{{ translate('Are you sure to disable Access All Products?') }}</strong>"
                                                    data-text-on="{{ translate('If you enable this, vendors can access all products of other available vendors') }}"
                                                    data-text-off="{{ translate('If you disable this, vendors can not access all products of other available vendors.') }}"
                                                    data-footer-text-on="<div class='text-center text-info mt-5'>{{ translate('Note : Don’t forget to save the information before leaving this page ') }}</div>"
                                                    data-footer-text-off="<div class='text-center text-info mt-5'>{{ translate('Note : Don’t forget to save the information before leaving this page ') }}</div>"
                                                    class="status toggle-switch-input dynamic-checkbox-toggle"
                                                    name="access_all_products" id="access_all_products" value="1"
                                                    {{ $access_all_products ? 'checked' : '' }}>
                                                <span class="toggle-switch-label text">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6">
                                        @php($store_review_reply = \App\Models\BusinessSetting::where('key', 'store_review_reply')->first())
                                        @php($store_review_reply = $store_review_reply ? $store_review_reply->value : 0)
                                        <div class="form-group mb-0">
                                            <span class="mb-2 d-flex align-items-center">
                                                <span class="text-title">
                                                    {{ translate('Vendor_Can_Reply_Review') }}
                                                </span>
                                                <span class="form-label-secondary text-danger d-flex align-items-center gap-1"
                                                        data-toggle="tooltip" data-placement="right"
                                                        data-original-title="{{ translate('If enabled, vendors can actively engage with the customers by responding to the reviews left for their orders') }}"><i class="tio-info text-muted ps--3"></i>
                                                </span>
                                            </span>
                                            <label
                                                class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                                <span class="pr-1 d-flex align-items-center switch--label">
                                                    <span class="line--limit-1 text-title">
                                                        {{ translate('Can Reply') }}
                                                    </span>
                                                </span>

                                                <input type="checkbox" data-id="store_review_reply" data-type="toggle"
                                                    data-image-on="{{ asset('/public/assets/admin/img/modal/info-warning.png') }}"
                                                    data-image-off="{{ asset('/public/assets/admin/img/modal/info-warning.png') }}"
                                                    data-title-on="<strong>{{ translate('Are you sure to enable Vendor Can Reply Review?') }}</strong>"
                                                    data-title-off="<strong>{{ translate('Are you sure to disable Vendor Can Reply Review?') }}</strong>"
                                                    data-text-on="{{ translate('If enabled, vendors can actively engage with the customers by responding to the reviews left for their orders.') }}"
                                                    data-text-off="{{ translate('If disabled, vendors can not reply to reviews left for their orders.') }}"
                                                    data-footer-text-on="<div class='text-center text-info mt-5'>{{ translate('Note : Don’t forget to save the information before leaving this page ') }}</div>"
                                                    data-footer-text-off="<div class='text-center text-info mt-5'>{{ translate('Note : Don’t forget to save the information before leaving this page ') }}</div>"
                                                    class="status toggle-switch-input dynamic-checkbox-toggle"
                                                    name="store_review_reply" id="store_review_reply" value="1"
                                                    {{ $store_review_reply ? 'checked' : '' }}>
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
                    <div class="card mb-20" id="product_approval_section">
                        <div class="card-body">
                            <div class="mb-20">
                                <div class="row g-1 align-items-center">
                                    <div class="col-xxl-9 col-lg-8 col-md-7 col-sm-6">
                                        <div>
                                            <h4 class="mb-1">
                                                {{ translate('Need Approval For') }}
                                            </h4>
                                            <p class="mb-0 fs-12">
                                                {{ translate('If enabled this option to require admin approval for products to be displayed on the user side.') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-md-5 col-sm-6">
                                        <div class="">
                                            @php($product_approval = \App\Models\BusinessSetting::where('key', 'product_approval')->first()?->value ?? 0)
                                            @php($product_approval_datas = \App\Models\BusinessSetting::where('key', 'product_approval_datas')->first()?->value ?? '')
                                            @php($product_approval_datas =json_decode($product_approval_datas , true))
                                            <div class="form-group mb-0">
                                                <label
                                                    class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                                    <span class="pr-1 d-flex align-items-center switch--label">
                                                        <span class="line--limit-1">
                                                            {{translate('Status') }}
                                                        </span>
                                                    </span>
                                                    <input type="checkbox"
                                                        data-id="product_approval"
                                                        data-type="toggle"
                                                        data-image-on="{{ asset('/public/assets/admin/img/modal/store-reg-on.png') }}"
                                                        data-image-off="{{ asset('/public/assets/admin/img/modal/store-reg-off.png') }}"
                                                        data-title-on="<strong>{{translate('Want_to_enable_product_approval?')}}</strong>"
                                                        data-title-off="<strong>{{translate('Want_to_disable_product_approval?')}}</strong>"
                                                        data-text-on="<p>{{ translate('If_you_enable_this,_option_to_require_admin_approval_for_products_to_be_displayed_on_the_user_side') }}</p>"
                                                        data-text-off="<p>{{ translate('If_you_disable_this,products_will_to_be_displayed_on_the_user_side_without_admin_approval.') }}</p>"
                                                        class="status toggle-switch-input dynamic-checkbox-toggle"
                                                        value="1"
                                                        name="product_approval" id="product_approval"
                                                        {{ $product_approval == 1 ? 'checked' : '' }}>
                                                    <span class="toggle-switch-label text">
                                                        <span class="toggle-switch-indicator"></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-light2 rounded p-xxl-20 p-3 mb-20 {{ $product_approval == 1 ? '' : 'd-none' }}" id="hide_show_approval_box">
                                <div class="bg-white rounded p-3 border">
                                    <div class="row g-3">
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group m-0">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="inlineCheckbox1" value="1" name="Add_new_product" {{  data_get($product_approval_datas,'Add_new_product',null) == 1 ? 'checked' :'' }}>
                                                    <label class="custom-control-label size-checkbox-20" for="inlineCheckbox1">
                                                        <h5 class="mb-1">{{ translate('Add New Product') }}</h5>
                                                        <p class="mb-0 fs-12">
                                                            {{ translate('If enabled, admin approval is required each time a vendor submits a new product. ') }}
                                                        </p>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group m-0">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input update_exinting_check" id="exinting_product" name="update_existing_products"
                                                    {{ (data_get($product_approval_datas,'Update_product_price',null) == 1 || data_get($product_approval_datas,'Update_product_variation',null) == 1 || data_get($product_approval_datas,'Update_anything_in_product_details',null) == 1) ? 'checked' : '' }}>
                                                    <label class="custom-control-label size-checkbox-20" for="exinting_product">
                                                        <h5 class="mb-1">{{ translate('Update Existing Product') }}</h5>
                                                        <p class="mb-0 fs-12">
                                                            {{ translate('If enabled, admin approval is required each time a vendor updates an existing product.') }}
                                                        </p>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="update-exinting-product-box d-none">
                                    <div class="mt-20 access_product_approval">
                                        <div class="mb-2">
                                            <span data-toggle="tooltip" data-placement="right"
                                                data-original-title="Specify which updates need approval.">
                                                {{ translate('Available Option for Update Existing Product') }} <span class="text-danger">*</span>
                                                <i class="tio-info text-muted"></i>
                                            </span>
                                        </div>
                                        <div class="bg-white rounded py-2 px-3 min-h-45px border">
                                            <div class="row g-1">
                                                <div class="col-xl-3 col-lg-4 col-sm-6">
                                                    <div class="custom-control custom-checkbox pt-2px">
                                                        <input class="mx-2 custom-control-input" type="checkbox"  {{  data_get($product_approval_datas,'Update_product_price',null) == 1 ? 'checked' :'' }} id="inlineCheckbox2" value="1" name="Update_product_price">
                                                        <label class=" custom-control-label" for="inlineCheckbox2">{{ translate('Update_product_price') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-lg-4 col-sm-6">
                                                    <div class="custom-control custom-checkbox pt-2px">
                                                        <input class="mx-2 custom-control-input" type="checkbox" {{  data_get($product_approval_datas,'Update_product_variation',null) == 1 ? 'checked' :'' }}  id="inlineCheckbox3" value="1" name="Update_product_variation">
                                                        <label class=" custom-control-label" for="inlineCheckbox3">{{ translate('Update_product_variation') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-lg-4 col-sm-6">
                                                    <div class="custom-control custom-checkbox pt-2px">
                                                        <input class="mx-2 custom-control-input" type="checkbox"  {{  data_get($product_approval_datas,'Update_anything_in_product_details',null) == 1 ? 'checked' :'' }} id="inlineCheckbox4" value="1" name="Update_anything_in_product_details">
                                                        <label class=" custom-control-label" for="inlineCheckbox4">{{ translate('Update_anything_in_product_details') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-20" id="cash_in_hand_section">
                        <div class="card-body">
                            <div class="mb-20">
                                <div>
                                    <h4 class="mb-1">
                                        {{ translate('Cash in Hand Controls') }}
                                    </h4>
                                    <p class="mb-0 fs-12">
                                        {{ translate('Setup your cash collection from here') }}
                                    </p>
                                </div>
                            </div>
                            <div class="bg-light rounded p-xxl-20 p-3">
                                <div class="row g-3">
                                    <div class="col-lg-4 col-sm-6">
                                        @php($cash_in_hand_overflow_store = \App\Models\BusinessSetting::where('key', 'cash_in_hand_overflow_store')->first())
                                        @php($cash_in_hand_overflow_store = $cash_in_hand_overflow_store ? $cash_in_hand_overflow_store->value : '')
                                        <div class="form-group mb-0">
                                            <span class="mb-2 d-flex align-items-center">
                                                <span class="text-title">
                                                    {{ translate('Cash_In_Hand_Overflow') }}
                                                </span>
                                                <span class="form-label-secondary text-danger d-flex align-items-center gap-1"
                                                        data-toggle="tooltip" data-placement="right"
                                                        data-original-title="{{ translate('If_enabled,_vendors_will_be_automatically_suspended_by_the_system_when_their_‘Cash_in_Hand’_limit_is_exceeded.') }}"><i class="tio-info text-muted ps--3"></i>
                                                </span>
                                            </span>
                                            <label
                                                class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                                    <span class="pr-1 d-flex align-items-center switch--label">
                                                        <span class="line--limit-1 text-title">
                                                            {{ translate('Cash_In_Hand_Overflow') }}
                                                        </span>
                                                    </span>

                                                    <input type="checkbox" data-id="cash_in_hand_overflow_store" data-type="toggle"
                                                    data-image-on="{{ asset('/public/assets/admin/img/modal/info-warning.png') }}"
                                                    data-image-off="{{ asset('/public/assets/admin/img/modal/info-warning.png') }}"
                                                    data-title-on="<strong>{{ translate('Are you sure to enable Cash in Hand Overflow Suspension?') }}</strong>"
                                                    data-title-off="<strong>{{ translate('Are you sure to disable Cash in Hand Overflow Suspension?') }}</strong>"
                                                    data-text-on="{{ translate('After enable vendor will be automatically suspended when their cash in hand exceeds the allowed limit.') }}"
                                                    data-text-off="{{ translate('After disable Vendors will not be suspended even if their cash in hand exceeds the set limit.') }}"
                                                    data-footer-text-on="<div class='text-center text-info mt-5'>{{ translate('Note : Don’t forget to save the information before leaving this page ') }}</div>"
                                                    data-footer-text-off="<div class='text-center text-info mt-5'>{{ translate('Note : Don’t forget to save the information before leaving this page ') }}</div>"
                                                    data-footer-text-off="<div class='text-center text-info mt-5'>{{ translate('Note : Don’t forget to save the information before leaving this page ') }}</div>"
                                                    class="status toggle-switch-input"
                                                    name="cash_in_hand_overflow_store" id="cash_in_hand_overflow_store" value="1"
                                                    {{ $cash_in_hand_overflow_store ? 'checked' : '' }}>
                                                <span class="toggle-switch-label text">
                                                        <span class="toggle-switch-indicator"></span>
                                                    </span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-sm-6">
                                        @php($cash_in_hand_overflow_store_amount = \App\Models\BusinessSetting::where('key', 'cash_in_hand_overflow_store_amount')->first())
                                        <div class="form-group mb-0">
                                            <label class=" input-label text-capitalize"
                                                   for="cash_in_hand_overflow_store_amount">
                                                    <span class="text-title">
                                                        {{ translate('Maximum_Amount_to_Hold_Cash_in_Hand') }} ({{ \App\CentralLogics\Helpers::currency_symbol() }})
                                                    </span>

                                                <span class="form-label-secondary"
                                                      data-toggle="tooltip" data-placement="right"
                                                      data-original-title="{{ translate('Enter_the_maximum_cash_amount_vendors_can_hold._If_this_number_exceeds,_vendors_will_be_suspended_and_not_receive_any_orders.') }}"><i class="tio-info text-muted ps--3"></i></span>
                                            </label>
                                            <input type="number" name="cash_in_hand_overflow_store_amount" class="form-control" data-toggle="tooltip"
                                                data-placement="top" data-original-title="{{ $cash_in_hand_overflow_store == 1 ? '' : translate('This field is disabled as Cash-in-Hand Overflow suspension is turned OFF') }}"
                                                   id="cash_in_hand_overflow_store_amount" min="0" step="{{ App\CentralLogics\Helpers::getDecimalPlaces() }}"
                                                   value="{{ $cash_in_hand_overflow_store_amount ? $cash_in_hand_overflow_store_amount->value : '' }}"  {{ $cash_in_hand_overflow_store  == 1 ? 'required' : 'readonly' }} >
                                            <span class="fs-12 text-info mt-1 d-none" id="amount_warning">{{ translate('Amount must be greater then Minimum Payable Amount') }}</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-sm-6">
                                        @php($min_amount_to_pay_store = \App\Models\BusinessSetting::where('key', 'min_amount_to_pay_store')->first())
                                        <div class="form-group mb-0">
                                            <label class=" input-label text-capitalize"
                                                   for="min_amount_to_pay_store">
                                                    <span class="text-title">
                                                        {{ translate('Minimum_Amount_To_Pay') }} ({{ \App\CentralLogics\Helpers::currency_symbol() }})

                                                    </span>

                                                <span class="form-label-secondary"
                                                      data-toggle="tooltip" data-placement="right"
                                                      data-original-title="{{ translate('Enter_the_minimum_cash_amount_vendors_can_pay') }}"><i class="tio-info text-muted ps--3"></i></span>
                                            </label>
                                            <input type="number" name="min_amount_to_pay_store" class="form-control"
                                                   id="min_amount_to_pay_store" min="0" step="{{ App\CentralLogics\Helpers::getDecimalPlaces() }}"
                                                   value="{{ $min_amount_to_pay_store ? $min_amount_to_pay_store->value : '' }}"  {{ $cash_in_hand_overflow_store  == 1 ? 'required' : 'readonly' }} >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="info-notes-bg px-3 py-2 rounded fz-11  gap-2 align-items-center d-flex mt-20">
                                <img src="{{asset('public/assets/admin/img/info-idea.svg')}}" alt="">
                                <span>
                                    {{translate('To setup vendor cash withdraw method visit')}}
                                    <span class="fz-12px font-semibold info-dark"><a style="color: #245BD1;" href={{ route('admin.transactions.withdraw-method.list') }} target="_blank" rel="noopener noreferrer">{{translate('Withdraw Method List')}}</a></span>
                                    {{translate('page.')}}
                                </span>
                            </div>
                        </div>
                    </div>

                    @includeIf('admin-views.partials._floating-submit-button')
                </div>
            </div>
        </form>
    </div>

    <div id="global_guideline_offcanvas"
        class="custom-offcanvas d-flex flex-column justify-content-between global_guideline_offcanvas">
        <!-- Guidline Offcanvas -->
        {{-- <div class="global_guideline_offcanvas" tabindex="-1" id="offcanvasSetupGuide" aria-labelledby="offcanvasSetupGuideLabel"
            style="--offcanvas-width: 500px"> --}}
        <div>
            <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                <h3 class="mb-0">{{ translate('messages.Store Setup Guideline') }}</h3>
                <button type="button"
                    class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary offcanvas-close fz-15px p-0"
                    aria-label="Close">&times;</button>
            </div>

            <div class="custom-offcanvas-body offcanvas-height-100 py-3 px-md-4 px-3">

                <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                    <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                        <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed"
                            type="button" data-toggle="collapse" data-target="#general_setup"
                            aria-expanded="true">
                            <div
                                class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                                <i class="tio-down-ui"></i>
                            </div>
                            <span
                                class="font-semibold text-left fs-14 text-title">{{ translate('General Setup') }}</span>
                        </button>
                        <a href="#general_setup_section"
                            class="text-info text-underline fs-12 text-nowrap offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                    </div>
                    <div class="collapse mt-3 show" id="general_setup">
                        <div class="card card-body">
                            <div class="">
                                <h5 class="mb-3">{{ translate('General Setup') }}</h5>
                                <p class="fs-12 mb-0">
                                    {{ translate('messages.Control vendor-related settings such as:') }}
                                </p>
                                <ul class="fs-12">
                                    <li>{{ translate('messages.Vendor registration availability') }}</li>
                                    <li>{{ translate('messages.Order cancellation permission') }}</li>
                                    <li>{{ translate('messages.Replying to customer reviews') }}</li>
                                    <li>{{ translate('messages.Access to the product gallery') }}</li>
                                    <li>{{ translate('messages.Access to all products') }}</li>
                                </ul>
                                <p class="fs-12 mb-0">
                                    {{ translate('messages.These settings are managed at the vendor level.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                    <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                        <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed"
                            type="button" data-toggle="collapse" data-target="#product_approval_guide"
                            aria-expanded="true">
                            <div
                                class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                                <i class="tio-down-ui"></i>
                            </div>
                            <span
                                class="font-semibold text-left fs-14 text-title">{{ translate('Product Approval') }}</span>
                        </button>
                        <a href="#product_approval_section"
                            class="text-info text-underline fs-12 text-nowrap offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                    </div>
                    <div class="collapse mt-3" id="product_approval_guide">
                        <div class="card card-body">
                            <div class="">
                                <h5 class="mb-3">{{ translate('Product Approval') }}</h5>
                                <p class="fs-12 mb-0">
                                    {{ translate('messages.This section manages which changes to products by vendors require admin approval before they are applied.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
                    <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                        <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed"
                            type="button" data-toggle="collapse" data-target="#cash_in_hand_guide"
                            aria-expanded="true">
                            <div
                                class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                                <i class="tio-down-ui"></i>
                            </div>
                            <span
                                class="font-semibold text-left fs-14 text-title">{{ translate('messages.Cash in Hand Controls') }}</span>
                        </button>
                        <a href="#cash_in_hand_section"
                            class="text-info text-underline fs-12 text-nowrap offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
                    </div>
                    <div class="collapse mt-3" id="cash_in_hand_guide">
                        <div class="card card-body">
                            <div class="">
                                <h5 class="mb-3">{{ translate('Cash in Hand Controls') }}</h5>
                                <p class="fs-12 mb-3">
                                    {{ translate('messages.Cash-in-hand control allows the platform to monitor and limit the amount of cash collected by vendors from Cash on Delivery (COD) orders. This feature helps reduce financial risk and ensures timely settlement between the vendor and the platform.') }}
                                </p>
                            </div>
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
        $(document).ready(function() {
            if ($('#exinting_product').is(':checked')) {
                $('.update-exinting-product-box').removeClass('d-none');
            } else {
                $('.update-exinting-product-box').addClass('d-none');
            }
            $('#exinting_product').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.update-exinting-product-box').removeClass('d-none');
                } else {
                    $('.update-exinting-product-box').addClass('d-none');
                }
            });

            $('form').on('submit', function(e) {
                if ($('#exinting_product').is(':checked')) {
                    let checked = 0;
                    if ($('#inlineCheckbox2').is(':checked')) {
                        checked++;
                    }
                    if ($('#inlineCheckbox3').is(':checked')) {
                        checked++;
                    }
                    if ($('#inlineCheckbox4').is(':checked')) {
                        checked++;
                    }
                    if (checked == 0) {
                        e.preventDefault();
                        toastr.error("{{ translate('Please select at least one option for Update Existing Product') }}");
                    }
                }
            });

            $('#cash_in_hand_overflow_store').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#cash_in_hand_overflow_store_amount').removeAttr('readonly').attr('required', true);
                    $('#cash_in_hand_overflow_store_amount').attr('data-original-title', '').tooltip('hide');
                    $('#min_amount_to_pay_store').removeAttr('readonly').attr('required', true);
                } else {
                    $('#cash_in_hand_overflow_store_amount').attr('readonly', true).removeAttr('required');
                    $('#cash_in_hand_overflow_store_amount').attr('data-original-title', "{{ translate('This field is disabled as Cash-in-Hand Overflow suspension is turned OFF') }}").tooltip('show');
                    $('#min_amount_to_pay_store').attr('readonly', true).removeAttr('required');
                }
            });

            $('#cash_in_hand_overflow_store_amount, #min_amount_to_pay_store').on('change keyup', function() {
                let maxAmount = parseFloat($('#cash_in_hand_overflow_store_amount').val());
                let minAmount = parseFloat($('#min_amount_to_pay_store').val());
                if (maxAmount <= minAmount) {
                    $('#amount_warning').removeClass('d-none');
                } else {
                    $('#amount_warning').addClass('d-none');
                }
            });

            $('form').on('submit', function(e) {
                let maxAmount = parseFloat($('#cash_in_hand_overflow_store_amount').val());
                let minAmount = parseFloat($('#min_amount_to_pay_store').val());
                if ($('#cash_in_hand_overflow_store').is(':checked') && maxAmount <= minAmount) {
                    e.preventDefault();
                    toastr.error("{{ translate('Amount must be greater then Minimum Payable Amount') }}");
                }
            });

            $('.offcanvas-close-btn').on('click', function() {
                $('.offcanvas-close').trigger('click');
            });

            $('#inlineCheckbox4').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#inlineCheckbox2').prop('checked', true);
                    $('#inlineCheckbox3').prop('checked', true);
                }
            });

            $('#inlineCheckbox2, #inlineCheckbox3').on('change', function() {
                if ($('#inlineCheckbox2').is(':checked') && $('#inlineCheckbox3').is(':checked')) {
                    $('#inlineCheckbox4').prop('checked', true);
                } else {
                    $('#inlineCheckbox4').prop('checked', false);
                }
            });
        });
    </script>
@endpush

