@extends('layouts.admin.app')

@section('title', translate('Order Details'))


@section('content')

    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">
                        <span class="page-header-icon">
                            <img src="{{ asset('/public/assets/admin/img/shopping-basket.png') }}" class="w--20"
                                alt="">
                        </span>
                        <span>
                            {{ translate('Parcel_details') }}
                        </span>

                    </h1>
                </div>

                <div class="col-sm-auto">
                    <a class="btn-icon btn-sm btn-soft-secondary rounded-circle mr-1"
                        href="{{ route('admin.order.details', [$order['id'] - 1]) }}" data-toggle="tooltip"
                        data-placement="top" title="{{ translate('Previous order') }}">
                        <i class="tio-chevron-left"></i>
                    </a>
                    <a class="btn-icon btn-sm btn-soft-secondary rounded-circle"
                        href="{{ route('admin.order.details', [$order['id'] + 1]) }}" data-toggle="tooltip"
                        data-placement="top" title="{{ translate('Next order') }}">
                        <i class="tio-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- Page Header -->

        <div class="row flex-xl-nowrap" id="printableArea">
            <div class="col-lg-8 order-print-area-left">
                <!-- Card -->
                <div class="card mb-3 mb-lg-5">
                    <!-- Header -->
                    <div class="card-header border-0 align-items-start flex-wrap">
                        <div class="order-invoice-left d-flex d-sm-block justify-content-between">
                            <div>
                                <h1 class="page-header-title d-flex align-items-center __gap-5px">
                                    {{ translate('messages.order') }} #{{ $order['id'] }}


                                </h1>
                                <span class="mt-2 d-block d-flex align-items-center __gap-5px">
                                    <i class="tio-date-range"></i>
                                    {{ date('d M Y ' . config('timeformat'), strtotime($order['created_at'])) }}
                                </span>

                                @if ($order->schedule_at && $order->scheduled)
                                    <h6 class="text-capitalize d-flex align-items-center __gap-5px">
                                        <span>{{ translate('messages.scheduled_at') }}</span>
                                        <span>:</span> <label
                                            class="fz--10 badge badge-soft-warning">{{ date('d M Y ' . config('timeformat'), strtotime($order['schedule_at'])) }}</label>
                                    </h6>
                                @endif
                                @if ($order->coupon)
                                    <h6 class="text-capitalize d-flex align-items-center __gap-5px">
                                        <span>{{ translate('messages.coupon') }}</span>
                                        <span>:</span> <label
                                            class="fz--10 badge badge-soft-primary">{{ $order->coupon_code }}
                                            ({{ translate('messages.' . $order->coupon->coupon_type) }})</label>
                                    </h6>
                                @endif
                                <div class="hs-unfold mt-1">
                                    <h5>
                                        <button
                                            class="btn py-1 px-2 order--details-btn-sm btn--primary btn-outline-primary btn--sm font-regular d-flex align-items-center __gap-5px"
                                            data-toggle="modal" data-target="#locationModal"><i class="tio-poi"></i>
                                            {{ translate('messages.show_locations_on_map') }}</button>
                                    </h5>
                                </div>
                                @if ($order['delivery_instruction'])
                                    <div class="__bg-FAFAFA fs-12 rounded p-10px mt-2 mb-3">
                                        <strong class="text-title">{{ translate('messages.delivery_instruction') }}
                                            :</strong> {{ $order['delivery_instruction'] }}
                                    </div>
                                    <!-- New Note -->
                                @endif

                                <!-- New Note -->
                                @if (
                                    $order->parcelCancellation?->return_fee > 0 &&
                                        !in_array($order->parcelCancellation?->cancel_by, ['deliveryman', 'admin_for_deliveryman']))
                                    <div
                                        class="bg-danger-5 p-10px rounded d-flex align-items-center justify-content-between gap-1 mt-3">
                                        <span
                                            class="text-title text-capitalize fs-12">{{ translate('Customer will pay parcel & return fee') }}</span>
                                        <h4 class="m-0 text-title text-nowrap">
                                            {{ \App\CentralLogics\Helpers::format_currency($order->parcelCancellation?->return_fee + $order->order_amount) }}
                                        </h4>
                                    </div>
                                @endif
                                <!-- New Note End -->

                                @if ($order['unavailable_item_note'])
                                    <h6 class="w-100 badge-soft-warning mt-3 p-1 rounded">
                                        <span class="text-dark">
                                            {{ translate('messages.order_unavailable_item_note') }} :
                                        </span>
                                        {{ $order['unavailable_item_note'] }}
                                    </h6>
                                @endif

                                @if ($order['order_note'])
                                    <h6>
                                        {{ translate('messages.order_note') }} :
                                        {{ $order['order_note'] }}
                                    </h6>
                                @endif

                            </div>
                            <div class="d-sm-none">
                                <a class="btn btn--primary print--btn font-regular d-flex align-items-center __gap-5px"
                                    href={{ route('admin.order.generate-invoice', [$order['id']]) }}>
                                    <i class="tio-print mr-sm-1"></i>
                                    <span>{{ translate('messages.print_invoice') }}</span>
                                </a>
                            </div>
                        </div>
                        <div class="order-invoice-right mt-3 mt-sm-0">
                            <div class="btn--container flex-wrap ml-auto align-items-end justify-content-end">


                                <a class="btn btn--primary print--btn font-regular py-2 px-3 d-none d-sm-block"
                                    href={{ route('admin.order.generate-invoice', [$order['id']]) }}>
                                    <i class="tio-print mr-sm-1"></i>
                                    <span>{{ translate('messages.print_invoice') }}</span>
                                </a>
                            </div>
                            <div class="text-right mt-3 order-invoice-right-contents text-capitalize">
                                <h6>
                                    <span>{{ translate('status') }}</span> <span>:</span>
                                    @if ($order['order_status'] == 'pending')
                                        <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.pending') }}
                                        </span>
                                    @elseif($order['order_status'] == 'confirmed')
                                        <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.confirmed') }}
                                        </span>
                                    @elseif($order['order_status'] == 'processing')
                                        <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.processing') }}
                                        </span>
                                    @elseif($order['order_status'] == 'picked_up')
                                        <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.out_for_delivery') }}
                                        </span>
                                    @elseif($order['order_status'] == 'delivered')
                                        <span class="badge badge-soft-success ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.delivered') }}
                                        </span>
                                    @elseif($order['order_status'] == 'failed')
                                        <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.payment_failed') }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                                            {{ translate(str_replace('_', ' ', $order['order_status'])) }}
                                        </span>
                                    @endif
                                </h6>
                                <h6 class="text-capitalize">
                                    <span>{{ translate('messages.payment_method') }}</span> <span>:</span>
                                    <span>{{ translate(str_replace('_', ' ', $order['payment_method'])) }}</span>
                                </h6>

                                <!-- offline_payment -->
                                @if ($order?->offline_payments)
                                    <span>{{ translate('Payment_verification') }}</span> <span>:</span>
                                    @if ($order?->offline_payments->status == 'pending')
                                        <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.pending') }}
                                        </span>
                                    @elseif ($order?->offline_payments->status == 'verified')
                                        <span class="badge badge-soft-success ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.verified') }}
                                        </span>
                                    @elseif ($order?->offline_payments->status == 'denied')
                                        <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.denied') }}
                                        </span>
                                    @endif

                                    @foreach (json_decode($order->offline_payments->payment_info) as $key => $item)
                                        @if ($key != 'method_id')
                                            <h6 class="">
                                                <div class="d-flex justify-content-sm-end text-capitalize">
                                                    <span class="title-color">{{ translate($key) }} :</span>
                                                    <strong>{{ $item }}</strong>
                                                </div>
                                            </h6>
                                        @endif
                                    @endforeach
                                @endif

                                <h6 class="">
                                    @if ($order['transaction_reference'] == null)
                                        <span>{{ translate('messages.reference_code') }}</span> <span>:</span>
                                        <button class="btn btn-outline-primary btn-sm py-half fs-12" data-toggle="modal"
                                            data-target=".bd-example-modal-sm">
                                            {{ translate('messages.add') }}
                                        </button>
                                    @else
                                        <span>{{ translate('messages.reference_code') }}</span> <span>:</span>
                                        <span>{{ $order['transaction_reference'] }}</span>
                                    @endif
                                </h6>

                                <h6 class="text-capitalize">
                                    <span>{{ translate('Order Type') }}</span>
                                    <span>:</span> <label
                                        class="fz--10 badge badge-soft-primary m-0">{{ translate(str_replace('_', ' ', $order['order_type'])) }}</label>
                                </h6>
                                <h6 class="text-capitalize">
                                    <span>{{ translate('Paid By') }}</span>
                                    <span>:</span> <label
                                        class="fz--10 badge badge-soft-secondary m-0">{{ translate($order->charge_payer) }}</label>
                                </h6>
                                <h6>
                                    <span>{{ translate('payment_status') }}</span> <span>:</span>
                                    @if ($order['payment_status'] == 'paid')
                                        <span class="badge badge-soft-success ml-sm-3">
                                            {{ translate('messages.paid') }}
                                        </span>
                                    @elseif ($order['payment_status'] == 'partially_paid')
                                        @if ($order->payments()->where('payment_status', 'unpaid')->exists())
                                            <strong class="text-danger">{{ translate('messages.partially_paid') }}</strong>
                                        @else
                                            <strong class="text-success">{{ translate('messages.paid') }}</strong>
                                        @endif
                                    @else
                                        <strong class="text-danger">{{ translate('messages.unpaid') }}</strong>
                                    @endif

                                </h6>
                            </div>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body px-0">

                        <div class="mx-3">
                            <div class="media align-items-center cart--media pb-2">
                                <div class="avatar avatar-xl mr-3"
                                    title="{{ $order->parcel_category ? $order->parcel_category->name : translate('messages.parcel_category_not_found') }}">
                                    <img class="img-fluid onerror-image"
                                        src="{{ $order->parcel_category?->image_full_url ?? asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                        data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}">
                                </div>
                                <div class="media-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <strong>
                                                {{ Str::limit($order->parcel_category ? $order->parcel_category->name : translate('messages.parcel_category_not_found'), 25, '...') }}</strong><br>
                                            <div class="font-size-sm text-body">
                                                <span>{{ $order->parcel_category ? $order->parcel_category->description : translate('messages.parcel_category_not_found') }}</span>
                                            </div>
                                        </div>

                                        <div class="col col-md-2 align-self-center">
                                            <h6>{{ translate('messages.distance') }}</h6>
                                            <span>{{ $order->distance }} {{ translate('km') }}</span>
                                        </div>
                                        <div class="col col-md-1 align-self-center">

                                        </div>

                                        <div class="col col-md-3 align-self-center text-right">
                                            <h6>{{ translate('messages.delivery_charge') }}</h6>
                                            <span>{{ \App\CentralLogics\Helpers::format_currency($order['delivery_charge']) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-2">
                        </div>



                        <div class="row justify-content-md-end mb-3 mt-4 mx-0">
                            <div class="col-12">
                                <dl class="row text-right px-3">

                                    @if (($order->tax_status == 'excluded' && $order['total_tax_amount'] > 0) || $order->tax_status == null)
                                        <dt class="col-6 col-sm-8 p-0 font-regular">{{ translate('messages.vat/tax') }}:
                                        </dt>
                                        <dd class="col-6 col-sm-4 p-0 text-right">
                                            +
                                            {{ \App\CentralLogics\Helpers::format_currency($order['total_tax_amount']) }}

                                        </dd>
                                    @endif

                                    <dt class="col-6 col-sm-8 p-0 font-regular">
                                        {{ translate('messages.delivery_man_tips') }}
                                    </dt>
                                    <dd class="col-6 col-sm-4 p-0">
                                        + {{ \App\CentralLogics\Helpers::format_currency($order['dm_tips']) }}</dd>
                                    <dt class="col-6 col-sm-8 p-0 font-regular text-truncate">
                                        {{ \App\CentralLogics\Helpers::get_business_data('additional_charge_name') ?? (\App\CentralLogics\Helpers::get_business_data('additional_charge_name') ?? translate('messages.additional_charge')) }}
                                        <hr>
                                    </dt>

                                    <dd class="col-6 col-sm-4 p-0">
                                        + {{ \App\CentralLogics\Helpers::format_currency($order['additional_charge']) }}
                                        <hr>
                                    </dd>

                                    <dt class="col-6 col-sm-8 p-0 fs-16">
                                        <div class="d-flex align-items-center gap-2 justify-content-end">
                                            {{ translate('messages.total') }}
                                            {{ $order->tax_status == 'included' ? '(' . translate('messages.TAX_Included') . ')' : '' }}

                                            @if (in_array($order->parcelCancellation?->cancel_by, ['deliveryman', 'admin_for_deliveryman']))
                                                <span class="form-label-secondary text-danger d-flex"
                                                    data-toggle="tooltip" data-placement="right"
                                                    data-original-title="{{ translate('No delivery fee will be charged if Delivery Man cancels the order') }}"><img
                                                        src="{{ asset('public/assets/admin/img/info-circle.svg') }}"
                                                        alt="Veg/non-veg toggle"> </span>
                                            @endif

                                            </span>
                                            @if ($order->parcelCancellation?->return_fee > 0 && $order->charge_payer != 'receiver')
                                                @if ($order->payment_method != 'cash_on_delivery')
                                                    @if ($order->payment_status == 'paid')
                                                        <span class="badge border-0 fs-10 badge-soft-success">
                                                            {{ translate('messages.Paid') }}
                                                        </span>
                                                    @else
                                                        <span class="badge border-0 fs-10 badge-soft-danger">
                                                            {{ translate('Due') }}
                                                        </span>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                    </dt>
                                    <dd class="col-6 col-sm-4 p-0 font-semibold text-title">
                                        {{ \App\CentralLogics\Helpers::format_currency($order['delivery_charge'] + $order['total_tax_amount'] + $order['dm_tips'] + $order['additional_charge'] - $order['coupon_discount_amount'] - $order['ref_bonus_amount']) }}
                                    </dd>
                                    @if ($order->parcelCancellation?->return_fee > 0)

                                        <dt class="col-6 col-sm-8 p-0 fs-16">
                                            <div
                                                class="d-flex fs-12 font-regular color-222324CC align-items-center gap-2 justify-content-end">
                                                {{ translate('messages.return_fee') }}
                                                @if ($order?->parcelCancellation?->return_fee_payment_status == 'paid')
                                                    <span class="badge border-0 fs-10 badge-soft-success">
                                                        {{ translate('messages.Paid') }}
                                                    </span>
                                                @else
                                                    <span class="badge border-0 fs-10 badge-soft-danger">
                                                        {{ translate('Due') }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if (
                                                $order->parcelCancellation?->return_fee > 0 &&
                                                    !in_array($order->parcelCancellation?->cancel_by, ['deliveryman', 'admin_for_deliveryman']))
                                                <hr>
                                            @endif
                                        </dt>
                                        <dd class="col-6 col-sm-4 p-0">
                                            <div class="fs-14 text-title">
                                                {{ \App\CentralLogics\Helpers::format_currency($order?->parcelCancellation?->return_fee) }}
                                            </div>
                                            @if (
                                                $order->parcelCancellation?->return_fee > 0 &&
                                                    !in_array($order->parcelCancellation?->cancel_by, ['deliveryman', 'admin_for_deliveryman']))
                                                <hr>
                                            @endif
                                        </dd>
                                    @endif

                                    @if (
                                        $order->parcelCancellation?->return_fee > 0 &&
                                            !in_array($order->parcelCancellation?->cancel_by, ['deliveryman', 'admin_for_deliveryman']))
                                        <dt class="col-6 col-sm-8 p-0 fs-16">
                                            <div
                                                class="d-flex fs-16 font-semibold font-regular text-title align-items-center gap-2 justify-content-end">
                                                {{ translate('Sub Total') }}

                                                @if ($order?->parcelCancellation?->return_fee_payment_status == 'paid')
                                                    <span class="badge border-0 fs-10 badge-soft-success">
                                                        {{ translate('messages.Paid') }}
                                                    </span>
                                                @else
                                                    <span class="badge border-0 fs-10 badge-soft-danger">
                                                        {{ translate('Due') }}
                                                    </span>
                                                @endif


                                            </div>
                                        </dt>
                                        <dd class="col-6 col-sm-4 p-0">
                                            <div class="fs-16 text-title font-semibold">
                                                {{ \App\CentralLogics\Helpers::format_currency($order['delivery_charge'] + $order['total_tax_amount'] + $order['dm_tips'] + $order['additional_charge'] - $order['coupon_discount_amount'] - $order['ref_bonus_amount'] + $order?->parcelCancellation?->return_fee ?? 0) }}
                                            </div>
                                        </dd>
                                    @endif





                                    @if ($order?->payments)
                                        @foreach ($order?->payments as $payment)
                                            @if ($payment->payment_status == 'paid')
                                                @if ($payment->payment_method == 'cash_on_delivery')
                                                    <dt class="col-6 col-sm-8 p-0 font-regular">
                                                        {{ translate('messages.Paid_with_Cash') }}
                                                        ({{ translate('COD') }})
                                                        :
                                                    </dt>
                                                @else
                                                    <dt class="col-6 col-sm-8 p-0 font-regular">
                                                        {{ translate('messages.Paid_by') }}
                                                        {{ translate($payment->payment_method) }} :
                                                    </dt>
                                                @endif
                                            @else
                                                <dt class="col-6 col-sm-8 p-0 font-regular">{{ translate('Due_Amount') }}
                                                    ({{ $payment->payment_method == 'cash_on_delivery' ? translate('messages.COD') : translate($payment->payment_method) }})
                                                    :</dt>
                                            @endif
                                            <dd class="col-6 col-sm-4 p-0 text-right">
                                                {{ \App\CentralLogics\Helpers::format_currency($payment->amount) }}
                                            </dd>
                                        @endforeach
                                    @endif
                                </dl>
                                <!-- End Row -->
                            </div>

                        </div>
                        <!-- End Row -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-4 order-print-area-right">

                     @if (
                                    !in_array($order['order_status'], [
                                        'refund_requested',
                                        'refunded',
                                        'refund_request_canceled',
                                        'delivered',
                                        'canceled',

                                        'returned',
                                    ]))

                <div class="card mb-2">
                    <div class="card-body">



                        <h5 class="card-title mb-10px text-start fw-medium fs-12 d-flex align-items-center gap-1">
                            <img class="svg" src="{{ asset('public/assets/admin/img/icons/shop-bag.svg') }}"
                                alt="{{ translate('img') }}">
                            {{ translate('Parcel_Status') }}

                            @if ($order?->parcelCancellation?->is_refunded == 1)
                                <span class='ml-2 badge badge-soft-primary'>
                                    {{ translate('Refunded') }}
                                </span>
                            @endif

                        </h5>
                        @if ($order?->offline_payments?->status == 'denied' && $order?->offline_payments?->note)
                            <div class="mb-15 text-left rounded badge badge-soft-danger py-2 px-3">
                                <h2 class="fs-12 text-danger font-weight-semibold mb-1">
                                    {{ translate('# Denied Note:') }}
                                </h2>
                                <p class="fs-12 mb-0 text-body text-break font-weight-medium">
                                    {{ $order?->offline_payments?->note }}</p>
                            </div>
                        @endif




                        @if ($order->is_unpaid_order)
                            <div class="text-center bg-light2 rounded p-xxl-20 p-3">
                                <h4 class="text-danger fs-14px fw-medium mb-2">
                                    {{ translate('messages.Payment_failed!') }}</h4>
                                @php($isCashOnDelivery = App\CentralLogics\Helpers::get_business_settings('cash_on_delivery')['status'] ?? false)
                                @php($isZoneCashOnDelivery = $order?->zone->cash_on_delivery)
                                @if ($isCashOnDelivery && $isZoneCashOnDelivery)
                                    <p class="fs-12 text-dark mb-20">
                                        {{ translate('messages.the customer\'s payment couldn\'t be processed. Please switch to COD.') }}
                                    </p>
                                @endif
                                <div class="btn--container justify-content-center">
                                    @if ($isCashOnDelivery && $isZoneCashOnDelivery)
                                        <button type="button" class="btn btn--primary btn-sm form-alert"
                                            data-id="order-{{ $order['id'] }}"
                                            data-cancel-btn="{{ translate('messages.Cancel') }}"
                                            data-confirm-btn="{{ translate('messages.Confirm') }}"
                                            data-image-url="{{ asset('public/assets/admin/img/tughrik.png') }}"
                                            data-title="{{ translate('Switch to Cash on Delivery?') }}"
                                            data-message="{{ translate('The customer’s digital payment has failed. Before switching this order to Cash on Delivery (COD), please confirm the payment issue with the customer to avoid any misunderstandings.') }}">
                                            {{ translate('messages.Switch to COD') }}</button>
                                        <form action="{{ route('admin.order.switch_to_cod', [$order['id']]) }}"
                                            method="post" id="order-{{ $order['id'] }}">
                                            @csrf
                                        </form>
                                    @endif

                                    <button type="button"
                                        class="btn btn-outline-secondary  trigger-reason offcanvas-trigger {{ $order['order_status'] == 'canceled' ? 'disabled' : '' }} {{ $order['order_status'] == 'canceled' ? 'active' : '' }}"
                                        data-target="#percel-cancellation_offcanvas">{{ translate('messages.Cancel Order') }}</button>

                                </div>

                            </div>
                        @else
                            @if ($order?->payment_method == 'offline_payment' && !in_array($order->order_status, ['canceled']))
                                <div class="bg-light2 rounded p-xxl-20 p-3">
                                    <div class="card-body p-0 text-center">
                                        <h2 class="fs-14 fw-medium mb-3">
                                            {{ $order?->offline_payments?->status == 'verified' ? translate('Payment_Verified') : translate('Payment_Verification') }}
                                        </h2>

                                        @if ($order?->offline_payments?->status == 'pending')
                                            <p class="text-danger fs-12 mb-20">
                                                {{ translate('Please_Verify_the_payment_before_confirm_order.') }}</p>
                                            <div class="btn--container justify-content-center">
                                                <button type="button" class="btn btn--primary btn-sm"
                                                    data-toggle="modal"
                                                    data-target="#verifyViewModal">{{ translate('messages.Verify_Payment') }}</button>





                                                     <button type="button"
                                        class="btn btn-outline-secondary  trigger-reason offcanvas-trigger {{ $order['order_status'] == 'canceled' ? 'disabled' : '' }} {{ $order['order_status'] == 'canceled' ? 'active' : '' }}"
                                        data-target="#percel-cancellation_offcanvas">{{ translate('messages.Cancel Order') }}</button>


                                            </div>
                                            {{-- </div> --}}
                                        @elseif($order?->offline_payments?->status == 'verified')
                                            <div class="btn--container justify-content-center">
                                                <button type="button" class="btn btn--primary btn-sm"
                                                    data-toggle="modal"
                                                    data-target="#verifyViewModal">{{ translate('messages.Payment_Details') }}</button>
                                            </div>
                                        @elseif($order?->offline_payments?->status == 'denied')
                                            <div class="btn--container justify-content-center">
                                                <button type="button" class="btn btn--primary btn-sm"
                                                    data-toggle="modal"
                                                    data-target="#verifyViewModal">{{ translate('messages.Recheck_Verification') }}</button>
                                                 <button type="button"
                                        class="btn btn-outline-secondary  trigger-reason offcanvas-trigger {{ $order['order_status'] == 'canceled' ? 'disabled' : '' }} {{ $order['order_status'] == 'canceled' ? 'active' : '' }}"
                                        data-target="#percel-cancellation_offcanvas">{{ translate('messages.Cancel Order') }}</button>

                                            </div>
                                        @elseif(!$order?->offline_payments)
                                            <p class="text-danger fs-12 mb-20">
                                                {{ translate('Please_Verify_the_payment_before_confirm_order.') }}</p>
                                            <div class="btn--container justify-content-center">
                                                <button type="button" class="btn btn--primary btn-sm"
                                                    data-toggle="modal"
                                                    data-target="#verifyViewModal">{{ translate('messages.Verify_Payment') }}</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if (
                                $order->payment_method != 'offline_payment' ||
                                    ($order?->offline_payments && $order?->offline_payments?->status == 'verified'))
                                @if (
                                    !in_array($order['order_status'], [
                                        'refund_requested',
                                        'refunded',
                                        'refund_request_canceled',
                                        'delivered',
                                        'canceled',
                                        'failed',
                                        'returned',
                                    ]))

                                    <div class="hs-unfold w-100">
                                        <div class="dropdown">
                                            <button @disabled(in_array($order['order_status'], [
                                                    'refund_requested',
                                                    'refunded',
                                                    'refund_request_canceled',
                                                    'delivered',
                                                    'returned',
                                                ]))
                                                {{ $order['order_status'] == 'canceled' && $order?->parcelCancellation && $order->parcelCancellation->before_pickup == 1 ? 'disabled' : '' }}
                                                class="form-control h--45px dropdown-toggle d-flex justify-content-between align-items-center w-100"
                                                type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <?php
                                                $message = match ($order['order_status']) {
                                                    'pending' => translate('messages.pending'),
                                                    'confirmed' => translate('messages.confirmed'),
                                                    'accepted' => translate('messages.confirmed'),
                                                    'processing' => translate('messages.processing'),
                                                    'handover' => translate('messages.confirmed'),
                                                    'picked_up' => translate('messages.out_for_delivery'),
                                                    'delivered' => translate('messages.delivered'),
                                                    'canceled' => translate('messages.canceled'),
                                                    'returned' => translate('messages.returned'),
                                                    default => translate('messages.status'),
                                                };
                                                ?>
                                                {{ $message }}
                                            </button>

                                            <div class="dropdown-menu text-capitalize"
                                                aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item {{ $order['order_status'] == 'pending' ? 'active' : '' }} {{ $order['order_status'] == 'canceled' ? 'disabled' : '' }} route-alert"
                                                    data-url="{{ route('admin.order.status', ['id' => $order['id'], 'order_status' => 'pending']) }}"
                                                    data-message="{{ translate('Change status to pending ?') }}"
                                                    href="javascript:">{{ translate('messages.pending') }}</a>
                                                <a class="dropdown-item {{ in_array($order['order_status'], ['accepted', 'confirmed', 'handover']) ? 'active' : '' }} route-alert {{ $order['order_status'] == 'canceled' ? 'disabled' : '' }}"
                                                    data-url="{{ route('admin.order.status', ['id' => $order['id'], 'order_status' => 'confirmed']) }}"
                                                    data-message="{{ translate('Change status to confirmed ?') }}"
                                                    href="javascript:">{{ translate('messages.confirmed') }}</a>

                                                <a class="dropdown-item {{ $order['order_status'] == 'picked_up' ? 'active' : '' }} route-alert {{ $order['order_status'] == 'canceled' ? 'disabled' : '' }}"
                                                    data-url="{{ route('admin.order.status', ['id' => $order['id'], 'order_status' => 'picked_up']) }}"
                                                    data-message="{{ translate('Change status to out for delivery ?') }}"
                                                    href="javascript:">{{ translate('messages.out_for_delivery') }}</a>
                                                <a class="dropdown-item {{ $order['order_status'] == 'delivered' ? 'active' : '' }} route-alert {{ $order['order_status'] == 'canceled' ? 'disabled' : '' }}"
                                                    data-url="{{ route('admin.order.status', ['id' => $order['id'], 'order_status' => 'delivered']) }}"
                                                    data-message="{{ translate('Change status to delivered (payment status will be paid if not)?') }}"
                                                    href="javascript:">{{ translate('messages.delivered') }}</a>
                                                <a class="dropdown-item trigger-reason offcanvas-trigger {{ $order['order_status'] == 'canceled' ? 'disabled' : '' }} {{ $order['order_status'] == 'canceled' ? 'active' : '' }}"
                                                    data-target="#percel-cancellation_offcanvas">{{ translate('messages.canceled') }}</a>
                                                @if (
                                                    $order['order_status'] == 'canceled' &&
                                                        $order?->parcelCancellation &&
                                                        $order->parcelCancellation->before_pickup == 0)
                                                    <a class="dropdown-item route-alert"
                                                        data-url="{{ route('admin.order.parcelReturn', ['id' => $order['id'], 'order_status' => 'returned']) }}"
                                                        data-message="{{ translate('Return_the_parcel ?') }}"
                                                        href="javascript:">{{ translate('messages.return_parcel') }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (
                                    !in_array($order['order_status'], [
                                        'refund_requested',
                                        'refunded',
                                        'refund_request_canceled',
                                        'delivered',
                                        'canceled',
                                        'failed',
                                        'returned',
                                    ]) && $order->delivery_man_id == null)
                                    <div class="w-100 text-center mt-3">
                                        <button type="button" class="btn btn--primary w-100" data-toggle="modal"
                                            data-target="#myModal" data-lat='21.03' data-lng='105.85'>
                                            {{ translate('messages.assign_delivery_man_manually') }}
                                        </button>
                                    </div>
                                @endif

                                @if ($order?->parcelCancellation && $order?->parcelCancellation?->is_delivery_charge_refundable == 1)
                                    @if ($order?->parcelCancellation?->is_refunded == 0)
                                        <div class="w-100 text-center mt-3">
                                            <button type="button" class="btn btn--primary w-100" data-toggle="modal"
                                                data-target="#manually_parcel_amount_refund">
                                                {{ translate('Manually_Refund_To_User') }}
                                            </button>
                                        </div>
                                    @endif
                                @endif
                            @endif
                            
                        @endif
                    </div>
                </div>
                @endif

                @if ($order->parcelCancellation)

                    <div class="card mb-2">
                        <!-- Canceled New -->
                        <div class="card-body">
                            @if ($order->parcelCancellation?->return_otp != null)
                                <div
                                    class="__bg-FAFAFA p-2 rounded d-flex align-items-center justify-content-between gap-1">
                                    <span class="text-title fs-12">{{ translate('Parcel Returned OTP') }}</span>
                                    <h3 class="m-0 text-title text-nowrap">{{ $order->parcelCancellation?->return_otp }}
                                    </h3>
                                </div>
                            @endif
                            <ul class="delivery--information-single mt-3 ">
                                <li>
                                    <span class="name">{{ translate('Canceled_By') }} </span>
                                    <span class="info"> {{ translate($order->canceled_by) }} </span>
                                </li>

                            </ul>
                            @if ($order->parcelCancellation?->return_fee > 0)
                                <div
                                    class="bg-FF40401A p-10px text-capitalize rounded d-flex align-items-center justify-content-between gap-1 mt-3">

                                    @if (
                                        $order->charge_payer == 'receiver' &&
                                            !in_array($order->parcelCancellation?->cancel_by, ['deliveryman', 'admin_for_deliveryman']))
                                        <span
                                            class="text-title fs-12">{{ translate('Customer will pay both parcel & return fee') }}</span>
                                        <h4 class="m-0 text-title text-nowrap">
                                            {{ \App\CentralLogics\Helpers::format_currency($order->parcelCancellation?->return_fee + $order->order_amount) }}
                                        </h4>
                                    @else
                                        <span
                                            class="text-title fs-12">{{ translate('Customer will pay return fee') }}</span>
                                        <h4 class="m-0 text-title text-nowrap">
                                            {{ \App\CentralLogics\Helpers::format_currency($order->parcelCancellation?->return_fee) }}
                                        </h4>
                                    @endif

                                </div>
                            @endif
                            <div class="p-10px __bg-FAFAFA mt-3">
                                @if (is_array($order->parcelCancellation?->reason) && count($order->parcelCancellation?->reason) > 0)
                                    <div class="fs-12">
                                        <span class="text-title font-medium">{{ translate('Cancel Reason') }}</span> <br>
                                        <ul>
                                            @foreach ($order->parcelCancellation?->reason as $reason)
                                                <li class="mr-1">{{ $reason }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if ($order->parcelCancellation?->note)
                                    <div class="fs-12 mt-3">
                                        <span class="text-title font-medium">{{ translate('Comment') }}</span> <br>
                                        <p class="ml-2"> {{ $order->parcelCancellation?->note }} </p>
                                    </div>
                                @endif
                            </div>
                            @if ($order->parcelCancellation?->before_pickup === 0)
                                <div class="mt-3 d-flex gap-2 text-title mt-3">
                                    <i class="tio-calendar-month mt-1"></i>
                                    <div class="fs-12 text-title">
                                        {{ translate('Estimated Return Date & Time:') }} <span>
                                            {{ $order->parcelCancellation?->set_return_date == 0 ? translate('Not Set Yet') : \App\CentralLogics\Helpers::time_date_format($order->parcelCancellation?->return_date) }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                @if ($order->delivery_man)
                    <div class="card mt-2">
                        <div class="card-body">
                            <h5 class="card-title mb-10px d-flex flex-wrap align-items-center">
                                <span class="card-header-icon">
                                    <i class="tio-user"></i>
                                </span>
                                <span>{{ translate('messages.deliveryman') }}</span>
                                @if (
                                    !in_array($order['order_status'], [
                                        'refund_requested',
                                        'refunded',
                                        'refund_request_canceled',
                                        'delivered',
                                        'canceled',
                                        'returned',
                                    ]))
                                    <a type="button" href="#myModal"
                                        class="text--base fs-12 font-midium cursor-pointer ml-auto" data-toggle="modal"
                                        data-target="#myModal">
                                        {{ translate('messages.change') }}
                                    </a>
                                @endif
                            </h5>
                            <a class="media align-items-center deco-none customer--information-single __bg-FAFAFA rounded p-10px mb-10px"
                                href="{{ !$order?->store?->sub_self_delivery ? route('admin.users.delivery-man.preview', [$order->delivery_man['id']]) : '#' }}">
                                <div class="avatar avatar-circle">
                                    <img class="avatar-img onerror-image"
                                        data-onerror-image="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                        src="{{ $order->delivery_man?->image_full_url ?? asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                        alt="Image Description">
                                </div>
                                <div class="media-body">
                                    <span
                                        class="text-body d-block text-hover-primary mb-1">{{ $order->delivery_man['f_name'] . ' ' . $order->delivery_man['l_name'] }}</span>

                                    <span class="text--title font-semibold d-flex align-items-center">
                                        <i class="tio-shopping-basket-outlined mr-2"></i>
                                        {{ $order->delivery_man->orders_count }}
                                        {{ translate('messages.orders_delivered') }}
                                    </span>

                                    <span class="text--title font-semibold d-flex align-items-center">
                                        <i class="tio-call-talking-quiet mr-2"></i>
                                        {{ $order->delivery_man['phone'] }}
                                    </span>

                                    <span class="text--title font-semibold d-flex align-items-center">
                                        <i class="tio-email-outlined mr-2"></i>
                                        {{ $order->delivery_man['email'] }}
                                    </span>

                                </div>
                            </a>
                            @php($address = $order->dm_last_location)
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-1 font-regular">{{ translate('messages.last_location') }}</h5>
                            </div>
                            @if (isset($address))
                                <span class="d-block">
                                    <a target="_blank" class="base--clr fs-12"
                                        href="http://maps.google.com/maps?z=12&t=m&q=loc:{{ $address['latitude'] }}+{{ $address['longitude'] }}">
                                        <i class="tio-map color-222324CC"></i> {{ $address['location'] }}<br>
                                    </a>
                                </span>
                            @else
                                <span class="d-block text-lowercase qcont">
                                    {{ translate('messages.location_not_found') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endif





                <div class="card mt-2">
                    <div class="card-body pt-3">
                        @if ($order->customer && $order->is_guest == 0)
                            <h5 class="card-title mb-10px">
                                <span class="card-header-icon">
                                    <i class="tio-user"></i>
                                </span>
                                <span>{{ translate('customer_information') }}</span>
                            </h5>

                            <a class="media align-items-center deco-none customer--information-single __bg-FAFAFA rounded p-10px mb-10px"
                                href="{{ route('admin.users.customer.view', [$order->customer['id']]) }}">
                                <div class="avatar avatar-circle">
                                    <img class="avatar-img onerror-image"
                                        data-onerror-image="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                        src="{{ $order->customer->image_full_url }}" alt="Image Description">
                                </div>
                                <div class="media-body">
                                    <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                        {{ $order->customer['f_name'] . ' ' . $order->customer['l_name'] }}
                                    </span>
                                    <span>{{ $order->customer->orders_count }} {{ translate('messages.orders') }}</span>
                                    <span class="text--title font-semibold d-flex align-items-center">
                                        <i class="tio-call-talking-quiet mr-2"></i>
                                        <span>{{ $order->customer['phone'] }}</span>
                                    </span>
                                    <span class="text--title d-flex align-items-center">
                                        <i class="tio-email mr-2"></i> <span>{{ $order->customer['email'] }}</span>
                                    </span>
                                </div>
                            </a>
                        @elseif($order->is_guest)
                            <span class="badge badge-soft-success py-2 d-block qcont mb-3">
                                {{ translate('Guest_user') }}
                            </span>
                        @else
                            <span class="badge badge-soft-danger py-2 d-block qcont">
                                {{ translate('Customer Not found!') }}
                            </span>
                        @endif


                    </div>
                </div>




                <!-- Dlivery Info Card -->
                <div class="card mb-2 mt-2">
                    <div class="card-body">
                        @if ($order->delivery_address)
                            @php($address = json_decode($order->delivery_address, true))
                            <div class="d-flex justify-content-between align-items-center mb-10px">
                                <h5 class="card-title">
                                    <span class="card-header-icon">
                                        <i class="tio-user"></i>
                                    </span>
                                    <span>{{ translate('messages.sender') }}</span>
                                </h5>

                            </div>
                            @if (isset($address))

                                <div class="delivery--information-single __bg-FAFAFA p-10px rounded mb-10px">
                                    <span class="name">{{ translate('messages.name') }}</span>
                                    <span
                                        class="info">{{ data_get($address, 'contact_person_name', translate('messages.N/A')) }}</span>
                                    <span class="name">{{ translate('messages.contact') }}</span>
                                    <a class="deco-none info"
                                        href="tel:{{ data_get($address, 'contact_person_number', translate('messages.N/A')) }}">
                                        {{ data_get($address, 'contact_person_number', translate('messages.N/A')) }}</a>
                                    @if (data_get($address, 'house') != '')
                                        <span class="name">{{ translate('House') }}</span> <span
                                            class="info">{{ data_get($address, 'house', translate('messages.N/A')) }}</span>
                                    @endif
                                    @if (data_get($address, 'floor') != '')
                                        <span class="name">{{ translate('Floor') }}</span> <span
                                            class="info">{{ data_get($address, 'floor', translate('messages.N/A')) }}</span>
                                    @endif

                                    @if (data_get($address, 'road') != '')
                                        <span class="name">{{ translate('Road') }}</span> <span
                                            class="info">{{ data_get($address, 'road', translate('messages.N/A')) }}</span>
                                    @endif

                                    @if (isset($address['address']))
                                        @if (data_get($address, 'latitude', null) && data_get($address, 'longitude', null))
                                            <a target="_blank" class="d-flex align-items-center base--clr fs-12"
                                                href="http://maps.google.com/maps?z=12&t=m&q=loc:{{ $address['latitude'] }}+{{ $address['longitude'] }}">
                                                <i class="tio-poi color-222324CC"></i>{{ $address['address'] }}
                                            </a>
                                        @else
                                            <i class="tio-poi color-222324CC"></i>{{ $address['address'] }}
                                        @endif
                                    @endif
                                </div>

                            @endif
                        @endif
                        <!-- Polish Version-->
                        @if ($order->receiver_details)
                            <hr>
                            @php($receiver_details = $order->receiver_details)
                            <h5 class="card-title mb-10px">
                                <span class="card-header-icon">
                                    <i class="tio-user"></i>
                                </span>
                                <span>{{ translate('messages.receiver_info') }}</span>
                            </h5>
                            @if (isset($receiver_details))
                                <span class="delivery--information-single __bg-FAFAFA p-10px mb-10px rounded">
                                    <span class="name">{{ translate('messages.name') }}</span>
                                    <span class="info">{{ $receiver_details['contact_person_name'] }}</span>
                                    <span class="name">{{ translate('messages.contact') }}</span>
                                    <a class="deco-none info d-flex"
                                        href="tel:{{ $receiver_details['contact_person_number'] }}">
                                        {{ $receiver_details['contact_person_number'] }}</a>
                                    @if (data_get($receiver_details, 'floor') != '')
                                        <span class="name">{{ translate('Floor') }}</span> <span
                                            class="info">{{ data_get($receiver_details, 'floor', translate('messages.N/A')) }}</span>
                                    @endif
                                    @if (data_get($receiver_details, 'house') != '')
                                        <span class="name">{{ translate('House') }}</span> <span
                                            class="info">{{ data_get($receiver_details, 'house', translate('messages.N/A')) }}</span>
                                    @endif

                                    @if (data_get($receiver_details, 'road') != '')
                                        <span class="name">{{ translate('Road') }}</span> <span
                                            class="info">{{ data_get($receiver_details, 'road', translate('messages.N/A')) }}</span>
                                    @endif

                            @endif
                            @if (isset($receiver_details['address']))
                                @if (isset($receiver_details['latitude']) && isset($receiver_details['longitude']))
                                    <a class="base--clr fs-12 d-flex" target="_blank"
                                        href="http://maps.google.com/maps?z=12&t=m&q=loc:{{ $receiver_details['latitude'] }}+{{ $receiver_details['longitude'] }}">
                                        <i class="tio-poi color-222324CC"></i>{{ $receiver_details['address'] }}
                                    </a>
                                @else
                                    <i class="tio-poi color-222324CC"></i>{{ $receiver_details['address'] }}
                                @endif


                            @endif
                            </span>
                        @endif
                    </div>
                </div>





                <!-- Customer Card -->
                @php($data = isset($order->order_proof) ? json_decode($order->order_proof, true) : [])
                @if (in_array($order->order_status, ['handover', 'delivered', 'picked_up']) || ($data != null && count($data) > 0))
                    <!-- order proof -->
                    <div class="card mb-2 mt-2">
                        <div class="card-header border-0 mb-10px text-center pb-0">
                            <h5 class="m-0 fs-14 color-222324CC">{{ translate('messages.delivery_proof') }} </h5>
                            @if (in_array($order->order_status, ['handover', 'delivered', 'picked_up']))
                                <button class="btn btn-outline-primary btn-sm px-3 py-1 fs-14" data-toggle="modal"
                                    data-target=".order-proof-modal"> {{ translate('messages.add') }} </button>
                            @endif
                        </div>
                        <div class="card-body pt-0">
                            @if ($data)
                                <div class="__bg-FAFAFA p-10px rounded">
                                    <label class="input-label" for="order_proof">{{ translate('messages.image') }} :
                                    </label>
                                    <div class="row g-1">
                                        @foreach ($data as $key => $img)
                                            @php($img = is_array($img) ? $img : ['img' => $img, 'storage' => 'public'])
                                            <div class="col-3">
                                                <img class="img__aspect-1 rounded border w-100 onerror-image"
                                                    data-toggle="modal" data-target="#imagemodal{{ $key }}"
                                                    data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                                    src="{{ \App\CentralLogics\Helpers::get_full_url('order', $img['img'], $img['storage']) }}">
                                            </div>
                                            <div class="modal fade" id="imagemodal{{ $key }}" tabindex="-1"
                                                role="dialog" aria-labelledby="order_proof_{{ $key }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title"
                                                                id="order_proof_{{ $key }}">
                                                                {{ translate('order_proof_image') }}
                                                            </h4>
                                                            <button type="button" class="close"
                                                                data-dismiss="modal"><span
                                                                    aria-hidden="true">&times;</span><span
                                                                    class="sr-only">{{ translate('messages.cancel') }}</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <img src="{{ \App\CentralLogics\Helpers::get_full_url('order', $img['img'], $img['storage']) }}"
                                                                class="initial--22 w-100">
                                                        </div>
                                                        @php($storage = $img['storage'] ?? 'public')
                                                        @php($file = $storage == 's3' ? base64_encode('order/' . $img['img']) : base64_encode('public/order/' . $img['img']))
                                                        <div class="modal-footer">
                                                            <a class="btn btn-primary"
                                                                href="{{ route('admin.file-manager.download', [$file, $storage]) }}"><i
                                                                    class="tio-download"></i>
                                                                {{ translate('messages.download') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif


            </div>
        </div>
        <!-- End Row -->
    </div>

    <!-- Modal -->
    <div class="modal fade" id="refund_cancelation_note" tabindex="-1" role="dialog"
        aria-labelledby="refund_cancelation_note_l" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="refund_cancelation_note_l">
                        {{ translate('messages.add_Order Rejection_Note') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.refund.order_refund_rejection') }}" method="post">
                        @method('PUT')
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <input type="text" class="form-control" name="admin_note" value="{{ old('admin_note') }}"
                            placeholder="Fake Order">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ translate('close') }}</button>
                    <button type="submit" class="btn btn-danger">{{ translate('messages.Confirm_Order Rejection') }}
                    </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="mySmallModalLabel">{{ translate('messages.reference_code_add') }}
                    </h5>
                    <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal"
                        aria-label="Close">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                </div>

                <form action="{{ route('admin.order.add-payment-ref-code', [$order['id']]) }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <!-- Input Group -->
                        <div class="form-group">
                            <input type="text" name="transaction_reference" class="form-control"
                                placeholder="{{ translate('messages.Ex:') }} Code123" required>
                        </div>
                        <!-- End Input Group -->
                        <div class="text-right">
                            <button class="btn btn--primary">{{ translate('messages.submit') }}</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- End Modal -->
    <!-- Modal -->
    <div class="modal fade order-proof-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="mySmallModalLabel">{{ translate('messages.add_delivery_proof') }}
                    </h5>
                    <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal"
                        aria-label="Close">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                </div>

                <form action="{{ route('admin.order.add-order-proof', [$order['id']]) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="flex-grow-1 mx-auto">
                            <div class="d-flex flex-wrap __gap-12px __new-coba" id="coba">
                                @php($proof = isset($order->order_proof) ? json_decode($order->order_proof, true) : 0)
                                @if ($proof)

                                    @foreach ($proof as $key => $photo)
                                        @php($photo = is_array($photo) ? $photo : ['img' => $photo, 'storage' => 'public'])
                                        <div class="spartan_item_wrapper min-w-176px max-w-176px">
                                            <img class="img--square"
                                                src="{{ \App\CentralLogics\Helpers::get_full_url('order', $photo['img'], $photo['storage']) }}"
                                                alt="order image">
                                            <div class="pen spartan_remove_row"><i class="tio-edit"></i></div>
                                            <a href="{{ route('admin.order.remove-proof-image', ['id' => $order['id'], 'name' => $photo['img']]) }}"
                                                class="spartan_remove_row"><i class="tio-add-to-trash"></i></a>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="text-right mt-2">
                            <button class="btn btn--primary">{{ translate('messages.submit') }}</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!-- Modal -->
    <div id="shipping-address-modal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalTopCoverTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-top-cover bg-dark text-center">
                    <figure class="position-absolute right-0 bottom-0 left-0 mb--1">
                        <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                            viewBox="0 0 1920 100.1">
                            <path fill="#fff" d="M0,0c0,0,934.4,93.4,1920,0v100.1H0L0,0z" />
                        </svg>
                    </figure>

                    <div class="modal-close">
                        <button type="button" class="btn btn-icon btn-sm btn-ghost-light" data-dismiss="modal"
                            aria-label="Close">
                            <svg width="16" height="16" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor"
                                    d="M11.5,9.5l5-5c0.2-0.2,0.2-0.6-0.1-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9-0.1l-5,5l-5-5C4.3,2.3,3.9,2.4,3.6,2.6l-1,1 C2.4,3.9,2.3,4.3,2.5,4.5l5,5l-5,5c-0.2,0.2-0.2,0.6,0.1,0.9l1,1c0.3,0.3,0.7,0.3,0.9,0.1l5-5l5,5c0.2,0.2,0.6,0.2,0.9-0.1l1-1 c0.3-0.3,0.3-0.7,0.1-0.9L11.5,9.5z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <!-- End Header -->

                <div class="modal-top-cover-icon">
                    <span class="icon icon-lg icon-light icon-circle icon-centered shadow-soft">
                        <i class="tio-location-search"></i>
                    </span>
                </div>

                @if (isset($address))
                    <form action="{{ route('admin.order.update-shipping', [$order['id']]) }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('messages.type') }}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="address_type"
                                        value="{{ $address['address_type'] }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('messages.contact') }}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="contact_person_number"
                                        value="{{ $address['contact_person_number'] }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('messages.name') }}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="contact_person_name"
                                        value="{{ $address['contact_person_name'] }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('House') }}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="house"
                                        value="{{ isset($address['house']) ? $address['house'] : '' }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('Floor') }}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="floor"
                                        value="{{ isset($address['floor']) ? $address['floor'] : '' }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('Road') }}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="road"
                                        value="{{ isset($address['road']) ? $address['road'] : '' }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('messages.address') }}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="address"
                                        value="{{ $address['address'] }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('messages.latitude') }}
                                </label>
                                <div class="col-md-4 js-form-message">
                                    <input type="text" class="form-control" name="latitude" id="latitude"
                                        value="{{ $address['latitude'] }}">
                                </div>
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{ translate('messages.longitude') }}
                                </label>
                                <div class="col-md-4 js-form-message">
                                    <input type="text" class="form-control" name="longitude" id="longitude"
                                        value="{{ $address['longitude'] }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <input id="pac-input" class="controls rounded initial-8"
                                    title="{{ translate('messages.search_your_location_here') }}" type="text"
                                    placeholder="{{ translate('messages.search_here') }}" />
                                <div class="mb-2 h-200px" id="map"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn--reset"
                                data-dismiss="modal">{{ translate('messages.close') }}</button>
                            <button type="submit"
                                class="btn btn--primary">{{ translate('messages.save_changes') }}</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!--Dm assign Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{ translate('messages.assign_deliveryman') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5 my-2">
                            <ul class="list-group overflow-auto initial--23">
                                @foreach ($deliveryMen as $dm)
                                    <li class="list-group-item">
                                        <span class="dm_list" role='button' data-id="{{ $dm['id'] }}">
                                            <img class="avatar avatar-sm avatar-circle mr-1 onerror-image"
                                                data-onerror-image="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                                src="{{ $dm['image_full_url'] }}" alt="{{ $dm['name'] }}">
                                            {{ $dm['name'] }}
                                        </span>

                                        <a class="btn btn-primary btn-xs float-right add-delivery-man"
                                            data-id="{{ $dm['id'] }}">{{ translate('messages.assign') }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-7 modal_body_map">
                            <div class="location-map" id="dmassign-map">
                                <div class="initial--24" id="map_canvas"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!--Show locations on map Modal -->
    <div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="locationModalLabel">{{ translate('messages.location_data') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 modal_body_map">
                            <div class="location-map" id="location-map">
                                <div class="initial--25" id="location_map_canvas"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->











    @if ($order?->payment_method == 'offline_payment')
        <div class="modal fade" id="verifyViewModal" tabindex="-1" aria-labelledby="verifyViewModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-end  border-0 pt-3 px-3">
                        <button type="button" class="close border rounded-circle bg-modal-btn" data-dismiss="modal">
                            <span aria-hidden="true" class="tio-clear"></span>
                        </button>
                    </div>
                    <div class="modal-body pt-0">
                        <div class="d-flex align-items-center flex-column gap-1 mb-xxl-5 mb-4 text-center">

                            <h2 class="mb-0">
                                {{ translate('Payment Verification') }}

                                @if (optional($order->offline_payments)->status === 'verified')
                                    <span class="badge badge-soft-success mt-3 mb-3">
                                        {{ translate('messages.verified') }}
                                    </span>
                                @endif
                            </h2>

                            @unless (optional($order->offline_payments)->status === 'verified')
                                <p class="text-danger mb-0 mt-0">
                                    {{ translate('Please check and verify the payment information before confirming the order.') }}
                                </p>
                            @endunless

                        </div>


                        <div class="card border-0">
                            <div class="bg-light2 p-xxl-20 p-3 rounded">
                                <div class="adjust-information-payment flex-md-nowrap flex-wrap">
                                    <div class="bg-white p-3 rounded h-100 w-100">
                                        <h4 class="mb-3 fs-16">{{ translate('messages.customer_information') }}</h4>
                                        <div class="d-flex flex-column gap-2">
                                            @if ($order->is_guest)
                                                @php($customer_details = json_decode($order['delivery_address'], true))

                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="customer-namekey">{{ translate('Name') }}</span>:
                                                    <span class="text-dark">
                                                        {{ $customer_details['contact_person_name'] }}</span>
                                                </div>

                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="customer-namekey">{{ translate('Phone') }}</span>:
                                                    <span class="text-dark">
                                                        {{ $customer_details['contact_person_number'] }}</span>
                                                </div>
                                            @elseif($order->customer)
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="customer-namekey">{{ translate('Name') }}</span>:
                                                    <span class="text-dark"> <a class="text-dark text text-capitalize"
                                                            href="{{ route('admin.customer.view', [$order['user_id']]) }}">
                                                            {{ $order->customer['f_name'] . ' ' . $order->customer['l_name'] }}
                                                        </a> </span>
                                                </div>

                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="customer-namekey">{{ translate('Phone') }}</span>:
                                                    <span class="text-dark">{{ $order->customer['phone'] }} </span>
                                                </div>
                                            @else
                                                <label
                                                    class="badge badge-danger">{{ translate('messages.invalid_customer_data') }}</label>
                                            @endif

                                        </div>
                                    </div>
                                    @if ($order?->offline_payments)
                                        <div class="bg-white p-3 rounded h-100 w-100">
                                            <div class="">
                                                <h4 class="mb-3 fs-16">{{ translate('messages.Payment_Information') }}
                                                </h4>
                                                <div class="row g-1">
                                                    @foreach (json_decode($order?->offline_payments?->payment_info ?? '[]') as $key => $item)
                                                        @if ($key != 'method_id')
                                                            <div class="col-sm-12">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="namekey"> {{ translate($key) }}</span>:
                                                                    <span
                                                                        class="text-dark text-break">{{ $item }}</span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>

                                                <div class="d-flex flex-column gap-2 mt-4">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="namekey">{{ translate('Customer_Note') }}</span>:
                                                        <span
                                                            class="text-dark text-break">{{ $order->offline_payments?->customer_note ?? translate('messages.N/A') }}
                                                        </span>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-white p-3 rounded h-100 w-100">
                                            <h4 class="mb-3 fs-16">{{ translate('messages.Payment_Information') }}</h4>
                                            <div class="row g-1">
                                                <div class="col-sm-12">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="namekey"> {{ translate('Payment_Method') }}</span>:
                                                        <span
                                                            class="text-dark text-break">{{ translate('messages.N/A') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if ($order?->offline_payments?->status != 'verified')
                            <div class="btn--container justify-content-end mt-xxl-5 mt-4 pt-xxl-1">
                                @if ($order?->offline_payments?->status != 'denied')
                                    <button type="button" class="btn btn--reset offline_payment_cancelation_note"
                                        data-toggle="modal" data-target="#offline_payment_cancelation_note"
                                        data-id="{{ $order['id'] }}"
                                        class="btn btn--reset">{{ translate('Payment_didn’t_Receive') }}</button>
                                @elseif ($order?->offline_payments?->status == 'denied')
                                    <button type="button"
                                        data-url="{{ route('admin.order.offline_payment', ['id' => $order['id'], 'verify' => 'switched_to_cod']) }}"
                                        data-message="{{ translate('messages.Make_the_payment_switched_to_cod_for_this_order') }}"
                                        class="btn btn--reset route-alert">{{ translate('Switched_to_COD') }}</button>
                                @endif
                                @if ($order?->offline_payments)
                                    <button type="button"
                                        data-url="{{ route('admin.order.offline_payment', ['id' => $order['id'], 'verify' => 'yes']) }}"
                                        data-message="{{ translate('messages.Make_the_payment_verified_for_this_order') }}"
                                        class="btn btn--primary route-alert">{{ translate('Yes,_Payment_Received') }}</button>
                                @else
                                    <button type="button" class="btn btn--primary btn-sm form-alert"
                                        data-id="order-{{ $order['id'] }}"
                                        data-cancel-btn="{{ translate('messages.Cancel') }}"
                                        data-confirm-btn="{{ translate('messages.Confirm') }}"
                                        data-image-url="{{ asset('public/assets/admin/img/tughrik.png') }}"
                                        data-title="{{ translate('Switch to Cash on Delivery?') }}"
                                        data-message="{{ translate('The customer’s offline payment has failed. Before switching this order to Cash on Delivery (COD), please confirm the payment issue with the customer to avoid any misunderstandings.') }}">
                                        {{ translate('messages.Switch to COD') }}
                                    </button>
                                    <form action="{{ route('admin.order.switch_to_cod', [$order['id']]) }}"
                                        method="post" id="order-{{ $order['id'] }}">
                                        @csrf
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="offline_payment_cancelation_note" tabindex="-1" role="dialog"
            aria-labelledby="offline_payment_cancelation_note_l" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-600" role="document">
                <div class="modal-content">
                    <div class="modal-header px-2 pt-2">
                        <button type="button" class="close min-w-28 rounded-circle border bg-modal-btn"
                            data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.order.offline_payment') }}" method="get">
                        <div class="modal-body">
                            <div class="cont mb-4 text-center pb-xxl-1">
                                <img width="60px" height="60px"
                                    src="{{ asset('/public/assets/admin/img/delete-confirmation.png') }}" alt="public"
                                    class="mb-20">
                                <h3 class="mb-xl-2 mb-1">
                                    {{ translate('Are you sure the payment was not received?') }}
                                </h3>
                                <p class="mb-0 fs-14 max-w-420 mx-auto">
                                    {{ translate('Please insert a Denied note for this payment request to inform the customer') }}
                                </p>
                            </div>
                            <div class="bg-light2 p-3 rounded">
                                <label class="form-label">
                                    {{ translate('Denied Note') }}
                                    <span class="custom-tooltip" data-title="payment request to inform the customer ">
                                        <i class="tio-info text-muted"></i>
                                    </span>
                                </label>
                                <input type="hidden" name="id" value="{{ $order->id }}">
                                <textarea type="text" rows="1" maxlength="100" required class="form-control" name="note"
                                    value="{{ old('note') }}" placeholder="{{ translate('transaction_id_mismatched') }}"></textarea>
                                <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-2">
                            <button type="button" class="btn btn--reset h-40px min-w-120px py-2 fs-14"
                                data-dismiss="modal">{{ translate('close') }}</button>
                            <button type="submit"
                                class="btn btn-primary h-40px min-w-120px py-2 fs-14">{{ translate('messages.Confirm_Rejection') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endif







    <!-- End Modal -->
    <div class="modal fade" id="manually_parcel_amount_refund" tabindex="-1" role="dialog"
        aria-labelledby="offline_payment_cancelation_note_l" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">
                        {{ translate('Parcel_Amount_Refund') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.order.parcelRefund') }}" method="post">
                        @csrf
                        @method('put')
                        <input type="hidden" name="id" value="{{ $order->id }}">
                        <input type="number" min="0" step="0.0001" max="{{ round($order->order_amount, 2) }}"
                            required class="form-control" name="refund_amount" value="{{ $order->order_amount }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ translate('close') }}</button>
                    <button type="submit"
                        class="btn btn--danger btn-outline-danger">{{ translate('messages.Confirm_Refund') }}
                    </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Parcel cancellation Offcanvas -->
    <div id="percel-cancellation_offcanvas" class="custom-offcanvas d-flex flex-column justify-content-between">
        <form action="{{ route('admin.order.CancelParcel') }}" method="post">
            <div>
                @method('put')
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div
                    class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                    <h3 class="mb-0">{{ translate('messages.Parcel cancellation') }}</h2>
                        <button type="button"
                            class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"
                            aria-label="Close">&times;</button>
                </div>
                <div class="custom-offcanvas-body p-20">
                    <div class="mb-20">
                        <label for="" class="text-title fs-14 mb-2">
                            {{ translate('Delivery Cancelled From') }} <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex align-items-center gap-4 border rounded py-2 px-3">
                            <div class="custom-control custom-radio w-100">
                                <input type="radio"
                                    data-cancellation_type="{{ in_array($order->order_status, ['picked_up', 'delivered']) ? 'after_pickup' : 'before_pickup' }}"
                                    data-url="{{ route('admin.order.parcelCancellationReason') }}" id="customer_er"
                                    name="delivery_cancelled_by" class="custom-control-input" value="customer" checked>
                                <label class="custom-control-label text-capitalize"
                                    for="customer_er">{{ translate('messages.Customer') }}</label>
                            </div>
                            <div class="custom-control custom-radio w-100">
                                <input type="radio" id="delivery"
                                    data-cancellation_type="{{ in_array($order->order_status, ['picked_up', 'delivered']) ? 'after_pickup' : 'before_pickup' }}"
                                    data-url="{{ route('admin.order.parcelCancellationReason') }}"
                                    name="delivery_cancelled_by" class="custom-control-input" value="deliveryman">
                                <label class="custom-control-label text-capitalize"
                                    for="delivery">{{ translate('messages.Deliveryman') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-20 pb-2">
                        <h4 class="mb-10px">{{ translate('messages.Please select cancellation reason') }}</h4>
                        <div id="data-view"> </div>
                    </div>
                    <div>
                        <h4 class="mb-10px">{{ translate('Comment') }}</h4>
                        <textarea name="note" data-target="#char-count" class="form-control char-counter" maxlength="100"
                            placeholder="{{ translate('messages.Type here your cancel reason...') }}" rows="3"></textarea>
                        <span id="char-count" class="text-right color-A7A7A7 d-block mt-1">0/100</span>
                    </div>
                </div>
            </div>
            <div class="offcanvas-footer p-3 d-flex align-items-center justify-content-center gap-3">
                <button type="button"
                    class="btn w-100 btn--reset offcanvas-close">{{ translate('messages.Continue Delivery') }}</button>
                <button type="submit" class="btn w-100 btn--primary">{{ translate('messages.Submit') }}</button>
            </div>
        </form>
    </div>
    <div id="offcanvasOverlay" class="offcanvas-overlay"></div>
@endsection

@push('script_2')
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value }}&libraries=places,marker&v=3.61">
    </script>
    <script>
        $(document).on('click', 'input[name="delivery_cancelled_by"], .trigger-reason', function() {
            let $input;

            if ($(this).is('input[name="delivery_cancelled_by"]')) {
                $input = $(this);
            } else {
                $input = $('input[name="delivery_cancelled_by"]:checked');
            }

            if ($input.length) {
                let type = $input.val();
                let url = $input.data('url');
                let cancellation_type = $input.data('cancellation_type');
                fetch_data(type, url, cancellation_type);
            }
        });

        function fetch_data(type, url, cancellation_type) {
            $.ajax({
                url: url,
                type: "get",
                data: {
                    user_type: type,
                    cancellation_type: cancellation_type

                },
                beforeSend: function() {
                    $('#data-view').empty();
                    $('#loading').show()
                },
                success: function(data) {
                    $("#data-view").append(data.view);
                },
                complete: function() {
                    $('#loading').hide()
                }
            })
        }

        $('.js-select2-custom').each(function() {
            var select2 = $.HSCore.components.HSSelect2.init($(this));
        });
        initCharCounter();

        $('.add-delivery-man').on('click', function() {
            id = $(this).data('id');
            $.ajax({
                type: "GET",
                url: '{{ url('/') }}/admin/order/add-delivery-man/{{ $order['id'] }}/' + id,
                success: function(data) {
                    location.reload();
                    console.log(data)
                    toastr.success('Successfully added', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                error: function(response) {
                    console.log(response);
                    toastr.error(response.responseJSON.message, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        })

        function last_location_view() {
            toastr.warning('Only available when order is out for delivery!', {
                CloseButton: true,
                ProgressBar: true
            });
        }

        var deliveryMan = <?php echo json_encode($deliveryMen); ?>;
        var map = null;
        const mapId = "{{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value }}"
        var myLatlng = new google.maps.LatLng({{ $address['latitude'] }}, {{ $address['longitude'] }});
        var dmbounds = new google.maps.LatLngBounds(null);
        var locationbounds = new google.maps.LatLngBounds(null);
        var dmMarkers = [];
        dmbounds.extend(myLatlng);
        locationbounds.extend(myLatlng);
        var myOptions = {
            center: myLatlng,
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapId: mapId,

            panControl: true,
            mapTypeControl: false,
            panControlOptions: {
                position: google.maps.ControlPosition.RIGHT_CENTER
            },
            zoomControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.LARGE,
                position: google.maps.ControlPosition.RIGHT_CENTER
            },
            scaleControl: false,
            streetViewControl: false,
            streetViewControlOptions: {
                position: google.maps.ControlPosition.RIGHT_CENTER
            }
        };

        function initializeGMap() {

            map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

            var infowindow = new google.maps.InfoWindow();

            map.fitBounds(dmbounds);
            for (var i = 0; i < deliveryMan.length; i++) {
                if (deliveryMan[i].lat) {
                    // var contentString = "<div style='float:left'><img style='max-height:40px;wide:auto;' src='{{ asset('storage/app/public/delivery-man') }}/"+deliveryMan[i].image+"'></div><div style='float:right; padding: 10px;'><b>"+deliveryMan[i].name+"</b><br/> "+deliveryMan[i].location+"</div>";
                    var point = new google.maps.LatLng(deliveryMan[i].lat, deliveryMan[i].lng);
                    dmbounds.extend(point);
                    map.fitBounds(dmbounds);
                    var activeIconContent = document.createElement("img");
                    activeIconContent.src = "{{ asset('public/assets/admin/img/delivery_boy_map.png') }}";
                    activeIconContent.alt = "Active DM";
                    activeIconContent.style.width = '100%';
                    activeIconContent.style.height = '100%';
                    activeIconContent.style.borderRadius = '50%';
                    var marker = new google.maps.marker.AdvancedMarkerElement({
                        position: point,
                        map: map,
                        title: deliveryMan[i].location,
                        content: activeIconContent
                    });
                    dmMarkers[deliveryMan[i].id] = marker;
                    google.maps.event.addListener(marker, 'click', (function(marker, i) {
                        return function() {
                            infowindow.setContent(
                                "<div style='float:left'><img style='max-height:40px;wide:auto;' src='" +
                                deliveryMan[i].image_link +
                                "'></div><div style='float:right; padding: 10px;'><b>" + deliveryMan[i]
                                .name + "</b><br/> " + deliveryMan[i].location + "</div>");
                            infowindow.open(map, marker);
                        }
                    })(marker, i));
                }

            };
        }

        function initMap() {
            let map = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                center: {
                    lat: {{ isset($order->store) ? $order->store->latitude : '23.757989' }},
                    lng: {{ isset($order->store) ? $order->store->longitude : '90.360587' }}
                },
                mapId: mapId,
            });

            let zonePolygon = null;

            //get current location block
            let infoWindow = new google.maps.InfoWindow();
            // Try HTML5 geolocation.
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        myLatlng = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        infoWindow.setPosition(myLatlng);
                        infoWindow.setContent("Location found.");
                        infoWindow.open(map);
                        map.setCenter(myLatlng);
                    },
                    () => {
                        handleLocationError(true, infoWindow, map.getCenter());
                    }
                );
            } else {
                // Browser doesn't support Geolocation
                handleLocationError(false, infoWindow, map.getCenter());
            }
            //-----end block------
            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
            let markers = [];
            const bounds = new google.maps.LatLngBounds();
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }
                // Clear out the old markers.
                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];
                // For each place, get the icon, name and location.
                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    console.log(place.geometry.location);
                    if (!google.maps.geometry.poly.containsLocation(
                            place.geometry.location,
                            zonePolygon
                        )) {
                        toastr.error('{{ translate('messages.out_of_coverage') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        return false;
                    }

                    document.getElementById('latitude').value = place.geometry.location.lat();
                    document.getElementById('longitude').value = place.geometry.location.lng();

                    // Create a marker for each place.
                    markers.push(
                        new google.maps.marker.AdvancedMarkerElement({
                            map,
                            title: place.name,
                            position: place.geometry.location,
                        })
                    );

                    if (place.geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });


        }

        $(document).ready(function() {

            // Re-init map before show modal
            $('#myModal').on('shown.bs.modal', function(event) {
                initMap();
                var button = $(event.relatedTarget);
                $("#dmassign-map").css("width", "100%");
                $("#map_canvas").css("width", "100%");
            });

            // Trigger map resize event after modal shown
            $('#myModal').on('shown.bs.modal', function() {
                initializeGMap();
                google.maps.event.trigger(map, "resize");
                map.setCenter(myLatlng);
            });

            // Address change modal modal shown
            $('#shipping-address-modal').on('shown.bs.modal', function() {
                initMap();
                // google.maps.event.trigger(map, "resize");
                // map.setCenter(myLatlng);
            });


            function initializegLocationMap() {
                map = new google.maps.Map(document.getElementById("location_map_canvas"), myOptions);

                var infowindow = new google.maps.InfoWindow();

                @if ($order->customer && isset($address))
                    var activeIconContent = document.createElement("img");
                    activeIconContent.src = "{{ asset('public/assets/admin/img/customer_location.png') }}";
                    activeIconContent.alt = "Active DM";
                    activeIconContent.style.width = '100%';
                    activeIconContent.style.height = '100%';
                    activeIconContent.style.borderRadius = '50%';
                    var marker = new google.maps.marker.AdvancedMarkerElement({
                        position: new google.maps.LatLng({{ $address['latitude'] }},
                            {{ $address['longitude'] }}),
                        map: map,
                        title: "{{ $order->customer->f_name }} {{ $order->customer->l_name }}",
                        content: activeIconContent
                    });

                    google.maps.event.addListener(marker, 'click', (function(marker) {
                        return function() {
                            infowindow.setContent(
                                "<div style='float:left'><img style='max-height:40px;wide:auto;' src='{{ $order?->customer?->image_full_url ?? asset('public/assets/admin/img/160x160/img1.jpg') }}'></div><div style='float:right; padding: 10px;'><b>{{ $order->customer->f_name }} {{ $order->customer->l_name }}</b><br />{{ $address['address'] }}</div>"
                            );
                            infowindow.open(map, marker);
                        }
                    })(marker));
                    locationbounds.extend(marker.position);
                @endif
                @if ($order->delivery_man && $order->dm_last_location)
                    var activeIconContent = document.createElement("img");
                    activeIconContent.src = "{{ asset('public/assets/admin/img/delivery_boy_map.png') }}";
                    activeIconContent.alt = "Active DM";
                    activeIconContent.style.width = '100%';
                    activeIconContent.style.height = '100%';
                    activeIconContent.style.borderRadius = '50%';
                    var dmmarker = new google.maps.marker.AdvancedMarkerElement({
                        position: new google.maps.LatLng({{ $order->dm_last_location['latitude'] }},
                            {{ $order->dm_last_location['longitude'] }}),
                        map: map,
                        title: "{{ $order->delivery_man->f_name }} {{ $order->delivery_man->l_name }}",
                        content: activeIconContent
                    });

                    google.maps.event.addListener(dmmarker, 'click', (function(dmmarker) {
                        return function() {
                            infowindow.setContent(
                                "<div style='float:left'><img style='max-height:40px;wide:auto;' src='{{ $order?->delivery_man?->image_full_url ?? asset('public/assets/admin/img/160x160/img1.jpg') }}'></div> <div style='float:right; padding: 10px;'><b>{{ $order->delivery_man->f_name }} {{ $order->delivery_man->l_name }}</b><br /> {{ $order->dm_last_location['location'] }}</div>"
                            );
                            infowindow.open(map, dmmarker);
                        }
                    })(dmmarker));
                    locationbounds.extend(dmmarker.position);
                @endif


                @if (isset($receiver_details))
                    var Receivermarker = new google.maps.marker.AdvancedMarkerElement({
                        position: new google.maps.LatLng({{ $receiver_details['latitude'] }},
                            {{ $receiver_details['longitude'] }}),
                        map: map,
                        title: "{{ Str::limit($receiver_details['contact_person_name'], 15, '...') }}",
                        // icon: "{{ asset('public/assets/admin/img/restaurant_map.png') }}"
                    });

                    google.maps.event.addListener(Receivermarker, 'click', (function(Receivermarker) {
                        return function() {
                            infowindow.open(map, Receivermarker);
                        }
                    })(Receivermarker));
                    locationbounds.extend(Receivermarker.position);
                @endif

                google.maps.event.addListenerOnce(map, 'idle', function() {
                    map.fitBounds(locationbounds);
                });
            }

            // Re-init map before show modal
            $('#locationModal').on('shown.bs.modal', function(event) {
                initializegLocationMap();
            });


            $('.dm_list').on('click', function() {
                var id = $(this).data('id');
                map.panTo(dmMarkers[id].position);
                map.setZoom(13);
                dmMarkers[id].setAnimation(google.maps.Animation.BOUNCE);
                window.setTimeout(() => {
                    dmMarkers[id].setAnimation(null);
                }, 3);
            });
        })
    </script>

    <script src="{{ asset('public/assets/admin/js/spartan-multi-image-picker.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'order_proof[]',
                maxCount: 6 -
                    {{ $order->order_proof && is_array($order->order_proof) ? count(json_decode($order->order_proof)) : 0 }},
                rowHeight: '176px !important',
                groupClassName: 'spartan_item_wrapper min-w-176px max-w-176px',
                maxFileSize: '',
                placeholderImage: {
                    image: "{{ asset('public/assets/admin/img/upload-img.png') }}",
                    width: '176px'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {

                },
                onRenderedPreview: function(index) {

                },
                onRemoveRow: function(index) {

                },
                onExtensionErr: function(index, file) {
                    toastr.error(
                        "{{ translate('messages.please_only_input_png_or_jpg_type_file') }}", {
                            CloseButton: true,
                            ProgressBar: true
                        });
                },
                onSizeErr: function(index, file) {
                    toastr.error("{{ translate('messages.file_size_too_big') }}", {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
    </script>
@endpush
