@extends('layouts.admin.app')

@section('title', translate('Delivery Man Preview'))

@section('content')
    <div class="content container-fluid pb-0">
        @include('admin-views.delivery-man.partials._page_header')

        <div class="">
            @include('admin-views.delivery-man.partials._tab_menu')
        </div>
    </div>
    <!-- End Page Header -->

    <div class="content container-fluid pt-0">
        <div class="card">
            <div class="card-body pb-5">
                @if ($deliveryMan->application_status == 'approved')
                    <div
                        class="d-flex mb-xxl-4 mb-3 justify-content-between align-items-center gap-2 flex-wrap position-relative z-index-2">
                        <h4 class="card-title text-dark align-items-center flex-wrap gap-2">
                            {{ translate('messages.deliveryman Details') }}
                        </h4>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="javascript:"
                                class="btn request-alert py-2 {{ $deliveryMan->status ? 'btn--danger' : 'btn-success' }} align-items-center d-flex"
                                data-url="{{ route('admin.users.delivery-man.status', [$deliveryMan['id'], $deliveryMan->status ? 0 : 1]) }}"
                                data-message="{{ $deliveryMan->status ? translate('messages.you_want_to_suspend_this_deliveryman') : translate('messages.you_want_to_unsuspend_this_deliveryman') }}">
                                {{ $deliveryMan->status ? translate('messages.suspend_this_delivery_man') : translate('messages.unsuspend_this_delivery_man') }}
                            </a>
                            <div class="hs-unfold">

                                <div class="dropdown">
                                    <button class="btn btn--primary dropdown_after gap-0 fs-14 dropdown-toggle"
                                        type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <img src="{{ asset('public/assets/admin/img/icons/bx_edit.png') }}" alt=""
                                            class="mr-1">
                                        {{ translate('Edit') }}

                                    </button>
                                    <div class="dropdown-menu min-w-220 dropdown-menu-right text-capitalize"
                                        aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item fs-14 font-weight-medium text-dark"
                                            href="{{ route('admin.users.delivery-man.edit', [$deliveryMan->id]) }}">{{ translate('messages.Edit Information') }}</a>
                                        <a class="dropdown-item fs-14 font-weight-medium text-dark" data-toggle="modal"
                                            data-target="#work_switcher" href="javascript:">
                                            {{ translate('messages.Edit Delivery Type') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                @endif
                <div
                    class="d-flex flex-column flex-lg-nowrap flex-wrap flex-md-row align-items-center gap-3 border rounded p-3">
                    <div class="d-flex gap-3 justify-content-center position-relative w-115 rounded">
                        <img class="rounded" data-onerror-image="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}"
                            src="{{ $deliveryMan['image_full_url'] }}" width="115" height="115"
                            alt="Delivery man image">
                        <span
                            class="suspend-badge bg-danger py-0 px-2 mb-2 fs-13 lh-1 text-white rounded position-absolute bottom-0 start-0">{{ !$deliveryMan['status'] && $deliveryMan['application_status'] == 'approved' ? translate('messages.suspended') : '' }}</span>
                    </div>

                    <div class="flex-grow-1">
                        <div class="mb-3">
                            <h4 title="{{ $deliveryMan['f_name'] . ' ' . $deliveryMan['l_name'] }}"
                                class="d-flex justify-content-center justify-content-md-start mb-1 gap-2">
                                {{ $deliveryMan['f_name'] . ' ' . $deliveryMan['l_name'] }}
                                @if ($deliveryMan->application_status == 'approved')
                                    @if ($deliveryMan['status'])
                                        @if ($deliveryMan['active'])
                                            <label
                                                class=" mb-0 badge badge-soft-primary">{{ translate('messages.online') }}</label>
                                        @else
                                            <label
                                                class=" mb-0 badge badge-soft-danger">{{ translate('messages.offline') }}</label>
                                        @endif
                                    @else
                                        <label
                                            class=" mb-0 badge badge-danger">{{ translate('messages.suspended') }}</label>
                                    @endif
                                @else
                                    <label
                                        class=" mb-0 badge badge-soft-{{ $deliveryMan->application_status == 'pending' ? 'info' : 'danger' }}">{{ translate('messages.' . $deliveryMan->application_status) }}</label>
                                @endif
                            </h4>
                            <div class="fs-12 text-title d-flex justify-content-center justify-content-md-start">
                                @if ($deliveryMan->application_status == 'approved')
                                    <a href="mailto:{{ $deliveryMan['email'] }}" class="text-title">
                                        {{ $deliveryMan['email'] }}</a>
                                    <span class="d-block mx-2 text-muted">|</span>
                                    <a href="tel:{{ $deliveryMan['phone'] }}" class="text-title">
                                        {{ $deliveryMan['phone'] }}</a>
                                @endif
                            </div>
                        </div>
                        <div
                            class="bg-light2 d-flex align-items-center flex-xxl-nowrap flex-wrap rider_overview-info rounded">
                            <div class="d-flex justify-content-center justify-content-md-start gap-3">
                                <div class="">
                                    <h6 class="fs-13 mb-1 font-weight-normal text-dark">
                                        {{ translate('messages.Job_Type') }} </h6>
                                    <p class="mb-0 fs-14 font-weight-bold text-dark ">
                                        {{ $deliveryMan->earning ? translate('messages.freelancer') : translate('messages.salary_based') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-muted line-30"></div>
                            <div class="d-flex justify-content-center justify-content-md-start gap-3">
                                <div class="">
                                    <h6 class="fs-13 mb-1 font-weight-normal text-dark">
                                        {{ translate('messages.Vehicle_Type') }}</h6>
                                    <p class="mb-0 fs-14 font-weight-bold text-dark ">
                                        {{ $deliveryMan?->vehicle?->type ?? translate('messages.Unknown Vehicle') }}</p>
                                </div>
                            </div>
                            <div class="text-muted line-30"></div>
                            <div class="d-flex justify-content-center justify-content-md-start gap-3">
                                <div class="">
                                    <h6 class="fs-13 mb-1 font-weight-normal text-dark">{{ translate('messages.Zone') }}
                                    </h6>
                                    <p class="mb-0 fs-14 font-weight-bold text-dark ">
                                        {{ isset($deliveryMan->zone) ? $deliveryMan->zone->name : translate('zone_deleted') }}
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                    @if ($deliveryMan->application_status == 'approved')
                        @php($total = $deliveryMan->reviews->count())
                        <div
                            class="d-flex flex-column flex-sm-nowrap flex-wrap flex-sm-row gap-3 flex-grow-1 border-lg-left">
                            @if ($total > 0)
                                <div class="d-flex flex-column align-items-center justify-content-center px-4">
                                    <img class=""
                                        src="{{ asset('public/assets/admin/img/icons/rating-stars.png') }}" alt="">

                                    <div class="d-block">
                                        <div class="rating--review">
                                            <h3 class="title mb-0">
                                                {{ count($deliveryMan->rating) > 0 ? number_format($deliveryMan->rating[0]->average, 1) : 0 }}<span
                                                    class="out-of">/5</span></h3>
                                            <div class="info">
                                                <span>{{ translate('messages._of') }} {{ $deliveryMan->reviews->count() }}
                                                    {{ translate('messages.reviews') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <ul
                                    class="list-unstyled list-unstyled-py-2 mb-0 rating--review-right py-3 flex-grow-1 review-color-progress">

                                    <!-- Review Ratings -->
                                    <li class="d-flex align-items-center font-size-sm">
                                        @php($five = \App\CentralLogics\Helpers::dm_rating_count($deliveryMan['id'], 5))
                                        <span class="progress-name mr-3">{{ translate('excellent') }}</span>
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $total == 0 ? 0 : ($five / $total) * 100 }}%;"
                                                aria-valuenow="{{ $total == 0 ? 0 : ($five / $total) * 100 }}"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="ml-3">{{ $five }}</span>
                                    </li>
                                    <!-- End Review Ratings -->

                                    <!-- Review Ratings -->
                                    <li class="d-flex align-items-center font-size-sm">
                                        @php($four = \App\CentralLogics\Helpers::dm_rating_count($deliveryMan['id'], 4))
                                        <span class="progress-name mr-3">{{ translate('good') }}</span>
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $total == 0 ? 0 : ($four / $total) * 100 }}%;"
                                                aria-valuenow="{{ $total == 0 ? 0 : ($four / $total) * 100 }}"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="ml-3">{{ $four }}</span>
                                    </li>
                                    <!-- End Review Ratings -->

                                    <!-- Review Ratings -->
                                    <li class="d-flex align-items-center font-size-sm">
                                        @php($three = \App\CentralLogics\Helpers::dm_rating_count($deliveryMan['id'], 3))
                                        <span class="progress-name mr-3">{{ translate('average') }}</span>
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $total == 0 ? 0 : ($three / $total) * 100 }}%;"
                                                aria-valuenow="{{ $total == 0 ? 0 : ($three / $total) * 100 }}"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="ml-3">{{ $three }}</span>
                                    </li>
                                    <!-- End Review Ratings -->

                                    <!-- Review Ratings -->
                                    <li class="d-flex align-items-center font-size-sm">
                                        @php($two = \App\CentralLogics\Helpers::dm_rating_count($deliveryMan['id'], 2))
                                        <span class="progress-name mr-3">{{ translate('below_average') }}</span>
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $total == 0 ? 0 : ($two / $total) * 100 }}%;"
                                                aria-valuenow="{{ $total == 0 ? 0 : ($two / $total) * 100 }}"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="ml-3">{{ $two }}</span>
                                    </li>
                                    <!-- End Review Ratings -->

                                    <!-- Review Ratings -->
                                    <li class="d-flex align-items-center font-size-sm">
                                        @php($one = \App\CentralLogics\Helpers::dm_rating_count($deliveryMan['id'], 1))
                                        <span class="progress-name mr-3">{{ translate('poor') }}</span>
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $total == 0 ? 0 : ($one / $total) * 100 }}%;"
                                                aria-valuenow="{{ $total == 0 ? 0 : ($one / $total) * 100 }}"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="ml-3">{{ $one }}</span>
                                    </li>
                                    <!-- End Review Ratings -->
                                </ul>
                            @else
                                <div class="d-flex flex-column align-items-center justify-content-center px-4 m-auto">
                                    <img width="75" class=""
                                        src="{{ asset('public/assets/admin/img/icons/no_rating.png') }}" alt="">
                                    <p class="mb-0 font-weight-normal">
                                        {{ translate('messages.no_review/rating_given_yet') }}
                                    </p>
                                </div>
                            @endif
                        </div>


                    @endif
                </div>


                <div class="border rounded p-xxl-20 p-3 mt-20">
                    <div class="d-flex gap-2 align-items-center mb-20">
                        @if ($deliveryMan->application_status == 'approved')
                            <h5 class="mb-0 fs-16 fw-bold">{{ translate('Identity_Documents') }}</h5>
                        @else
                            <h5 class="mb-0 fs-16 fw-bold">{{ translate('Registration_Information') }}</h5>
                        @endif
                    </div>
                    <div class="row g-3">
                        @if ($deliveryMan->application_status == 'pending')
                            <div class="col-lg-4">
                                <div class="bg-light2 rounded p-3 h-100 d-flex flex-column gap-2">

                                    <div class="key-val-list-item d-flex gap-3">
                                        <div class="text-title fs-14 identity__info">
                                            {{ translate('messages.First_Name') }} </div>:
                                        <div class="text-dark fs-14">{{ $deliveryMan['f_name'] }}</div>
                                    </div>
                                    <div class="key-val-list-item d-flex gap-3">
                                        <div class="text-title fs-14 identity__info">{{ translate('messages.Last_Name') }}
                                        </div>:
                                        <div class="text-dark fs-14">{{ $deliveryMan['l_name'] }}</div>
                                    </div>
                                    <div class="key-val-list-item d-flex gap-3">
                                        <div class="text-title fs-14 identity__info">{{ translate('messages.email') }}
                                        </div>:
                                        <div class="text-dark fs-14">{{ $deliveryMan['email'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-lg-4">
                            <div class="bg-light2 rounded p-3 h-100 d-flex flex-column gap-2">

                                <div class="key-val-list-item d-flex gap-3">
                                    <div class="text-title fs-14 identity__info">{{ translate('Identity_Type') }}</div>:
                                    <div class="text-dark fs-14">{{ translate($deliveryMan->identity_type) }}</div>
                                </div>
                                <div class="key-val-list-item d-flex gap-3">
                                    <div class="text-title fs-14 identity__info">
                                        {{ translate('messages.identification_number') }}</div>:
                                    <div class="text-dark fs-14">{{ $deliveryMan->identity_number }}</div>
                                </div>
                            </div>
                        </div>
                        @if ($deliveryMan->application_status == 'pending')
                            <div class="col-lg-4">
                                <div class="bg-light2 rounded p-3 h-100 d-flex flex-column gap-2">

                                    <div class="key-val-list-item d-flex gap-3">
                                        <div class="text-title fs-14 identity__info">{{ translate('messages.Phone') }}
                                        </div>:
                                        <div class="text-dark fs-14">{{ $deliveryMan->phone }}</div>
                                    </div>
                                    <div class="key-val-list-item d-flex gap-3">
                                        <div class="text-title fs-14 identity__info">{{ translate('messages.Password') }}
                                        </div>:
                                        <div class="text-dark fs-14">**********</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class=" {{ $deliveryMan->application_status == 'pending' ? 'col-12' : 'col-lg-8' }} ">
                            <div class="bg-light2 rounded p-3 h-100 identity_documnet_body tabs-slide-wrap">

                                <div class="tabs-inner d-flex gap-3 identity_documnet_wrap">
                                    @foreach ($deliveryMan->identity_image_full_url as $key => $img)
                                        <button class="btn  p-0" data-toggle="modal"
                                            data-target="#image-{{ $key }}">
                                            <div class="gallary-card">
                                                <img class="rounded mx-h150 mx-w-100"
                                                    data-onerror-image="{{ asset('/public/assets/admin/img/900x400/img1.jpg') }}"
                                                    src="{{ $img }}" width="275" height="150"
                                                    alt="">
                                            </div>
                                        </button>
                                        <div class="modal fade" id="image-{{ $key }}" tabindex="-1"
                                            role="dialog" aria-labelledby="myModlabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="myModlabel">
                                                            {{ translate('messages.Identity_Image') }}</h4>
                                                        <button type="button" class="close" data-dismiss="modal"><span
                                                                aria-hidden="true">&times;</span><span
                                                                class="sr-only">{{ translate('messages.Close') }}</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <img data-onerror-image="{{ asset('/public/assets/admin/img/900x400/img1.jpg') }}"
                                                            src="{{ $img }}" class="w-100 onerror-image">
                                                    </div>
                                                    <div class="modal-footer">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="arrow-area">
                                    <div class="button-prev align-items-center">
                                        <button type="button"
                                            class="btn btn-click-prev mr-auto border-0 btn-primary rounded-circle fs-12 p-2 d-center">
                                            <i class="tio-chevron-left fs-24"></i>
                                        </button>
                                    </div>
                                    <div class="button-next align-items-center">
                                        <button type="button"
                                            class="btn btn-click-next ml-auto border-0 btn-primary rounded-circle fs-12 p-2 d-center">
                                            <i class="tio-chevron-right fs-24"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>



    <div class="content container-fluid pt-0">
        <div class="card">
            <div class="card-body">
                @if ($deliveryMan->application_status == 'approved')
                    <div class="row g-3 color-card-custom">
                        <div class="col-lg-3">
                            <div class="color-card h-100 align-items-center justify-content-center">
                                <div
                                    class="box d-flex flex-column text-center justify-content-center align-items-center gap-3">
                                    <div class="img-box">
                                        <img class="resturant-icon w--30"
                                            src="{{ asset('public/assets/admin/img/icons/color-icon-1.png') }}"
                                            alt="img">
                                    </div>
                                    <div>
                                        <h2 class="title fs-24 fw-bold mb-1">
                                            {{ count($deliveryMan['order_transaction']) }}
                                        </h2>
                                        <div class="subtitle text-title">
                                            {{ translate('messages.total_delivered_orders') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="row g-3 row-3">


                                <!-- Collected Cash Card Example -->
                                <div class="col-sm-6 col-xxl-4 col-xl-6 col-lg-6">
                                    <div class="color-card color-2">
                                        <div class="img-box">
                                            <img class="resturant-icon w--30"
                                                src="{{ asset('/public/assets/admin/img/icons/color-icon-2.png') }}"
                                                alt="transactions">
                                        </div>
                                        <div>
                                            <h2 class="title fs-24 fw-bold mb-1">
                                                {{ \App\CentralLogics\Helpers::format_currency($deliveryMan->wallet ? $deliveryMan->wallet->collected_cash : 0.0) }}
                                            </h2>
                                            <div class="subtitle text-title">
                                                {{ translate('messages.cash_in_hand') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Earning Card Example -->
                                <div class="col-sm-6 col-xxl-4 col-xl-6 col-lg-6">
                                    <div class="color-card color-3">
                                        <div class="img-box">
                                            <img class="resturant-icon w--30"
                                                src="{{ asset('/public/assets/admin/img/icons/color-icon-3.png') }}"
                                                alt="transactions">
                                        </div>
                                        <div>
                                            <h2 class="title fs-24 fw-bold mb-1">
                                                {{ \App\CentralLogics\Helpers::format_currency($deliveryMan->wallet ? $deliveryMan->wallet->total_earning : 0.0) }}
                                            </h2>
                                            <div class="subtitle text-title">
                                                {{ translate('messages.total_earning') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Earning Card Example -->

                                <?php
                                $balance = 0;
                                if ($deliveryMan->wallet) {
                                    $balance = $deliveryMan->wallet->total_earning - ($deliveryMan->wallet->total_withdrawn + $deliveryMan->wallet->pending_withdraw + $deliveryMan->wallet->collected_cash);
                                }

                                ?>
                                @if ($deliveryMan->earning)
                                    @if ($balance > 0)
                                        <div class="col-sm-6 col-lg-4">
                                            <div class="color-card colxxl-4">
                                                <div class="img-box">
                                                    <img class="resturant-icon w--30"
                                                        src="{{ asset('/public/assets/admin/img/icons/group.png') }}"
                                                        alt="transactions">
                                                </div>
                                                <div>
                                                    <h2 class="title fs-24 fw-bold mb-1">
                                                        {{ \App\CentralLogics\Helpers::format_currency(abs($balance)) }}
                                                    </h2>
                                                    <div class="subtitle text-title">
                                                        {{ translate('messages.Withdraw_Able_Balance') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($balance < 0)
                                        <div class="col-sm-6 col-xxl-4 col-xl-6 col-lg-6">
                                            <div class="color-card color-4">
                                                <div class="img-box">
                                                    <img class="resturant-icon w--30"
                                                        src="{{ asset('/public/assets/admin/img/icons/color-icon-4.png') }}"
                                                        alt="transactions">
                                                </div>
                                                <div>
                                                    <h2 class="title fs-24 fw-bold mb-1">
                                                        {{ \App\CentralLogics\Helpers::format_currency(abs($deliveryMan->wallet->collected_cash)) }}
                                                    </h2>
                                                    <div class="subtitle text-title">
                                                        {{ translate('messages.Payable_Balance') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-sm-6 col-xxl-4 col-xl-6 col-lg-6">
                                            <div class="color-card color-4">
                                                <div class="img-box">
                                                    <img class="resturant-icon w--30"
                                                        src="{{ asset('/public/assets/admin/img/icons/group.png') }}"
                                                        alt="transactions">
                                                </div>
                                                <div>
                                                    <h2 class="title fs-24 fw-bold mb-1">
                                                        {{ \App\CentralLogics\Helpers::format_currency(0) }}
                                                    </h2>
                                                    <div class="subtitle text-title">
                                                        {{ translate('messages.Balance') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif


                                    <div class="col-sm-6 col-xxl-4 col-xl-6 col-lg-6">
                                        <div class="color-card color-5">
                                            <div class="img-box">
                                                <img class="resturant-icon w--30"
                                                    src="{{ asset('/public/assets/admin/img/icons/color-icon-5.png') }}"
                                                    alt="transactions">
                                            </div>
                                            <div>
                                                <h2 class="title fs-24 fw-bold mb-1">
                                                    {{ \App\CentralLogics\Helpers::format_currency($deliveryMan->wallet ? $deliveryMan->wallet->total_withdrawn : 0.0) }}
                                                </h2>
                                                <div class="subtitle text-title">
                                                    {{ translate('messages.Total_withdrawn') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-xxl-4 col-xl-6 col-lg-6">
                                        <div class="color-card color-6">
                                            <div class="img-box">
                                                <img class="resturant-icon w--30"
                                                    src="{{ asset('/public/assets/admin/img/icons/color-icon-6.png') }}"
                                                    alt="transactions">
                                            </div>
                                            <div>
                                                <h2 class="title fs-24 fw-bold mb-1">
                                                    {{ \App\CentralLogics\Helpers::format_currency($deliveryMan->wallet ? $deliveryMan->wallet->pending_withdraw : 0.0) }}
                                                </h2>
                                                <div class="subtitle text-title">
                                                    {{ translate('messages.Pending_withdraw') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xxl-4 col-xl-6 col-lg-6">
                                        <div class="color-card color-9">
                                            <div class="img-box">
                                                <img class="resturant-icon w--30"
                                                    src="{{ asset('/public/assets/admin/img/icons/loyalty-star.png') }}"
                                                    alt="transactions">
                                            </div>
                                            <div>
                                                <h2 class="title text--039D55 fs-24 fw-bold mb-1">
                                                    {{ (int) $deliveryMan->loyalty_point }}
                                                </h2>
                                                <div class="subtitle text-title">
                                                    {{ translate('messages.Loyalty Point') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>


    @if ($deliveryMan->application_status == 'approved')
        <div class="content container-fluid pt-0">
            <div class="card">
                <!-- Header -->
                <div class="card-header flex-sm-nowrap flex-wrap gap-2 pt-3 pb-0 border-0">
                    <h5 class="card-header-title d-flex align-items-center gap-2 text-nowrap line--limite-1">
                        {{ translate('messages.review_list') }}
                        <span class="badge badge-soft-dark ml-2" id="itemCount">
                            {{ $reviews->total() }}
                        </span>
                    </h5>
                    <div class="search--button-wrapper justify-content-end">
                        <form class="search-form min--260">
                            <div class="input-group input--group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control h--40px"
                                    placeholder="{{ translate('messages.search here') }}"
                                    value="{{ request()->search }}" aria-label="Search" tabindex="1">

                                <button type="submit" class="btn btn--secondary bg-modal-btn"><i
                                        class="tio-search text-muted"></i></button>
                            </div>
                        </form>
                        <!-- Unfold -->
                        <div class="hs-unfold mr-2">
                            <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40"
                                href="javascript:;"
                                data-hs-unfold-options='{
                                    "target": "#usersExportDropdown",
                                    "type": "css-animation"
                                }'>
                                <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                            </a>

                            <div id="usersExportDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                                <a id="export-excel" class="dropdown-item"
                                    href="{{ route('admin.users.delivery-man.review-export', ['type' => 'excel', 'id' => $deliveryMan->id, request()->getQueryString()]) }}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                        alt="Image Description">
                                    {{ translate('messages.excel') }}
                                </a>
                                <a id="export-csv" class="dropdown-item"
                                    href="{{ route('admin.users.delivery-man.review-export', ['type' => 'csv', 'id' => $deliveryMan->id, request()->getQueryString()]) }}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                        alt="Image Description">
                                    .{{ translate('messages.csv') }}
                                </a>
                            </div>
                        </div>
                        <!-- End Unfold -->
                    </div>
                </div>
                <!-- End Header -->

                <!-- New Table -->

                <div class="p-xxl-20 p-3">
                    <div class="card-body shadow-sm rounded p-0">
                        <div class="table-responsive datatable-custom">
                            <table id="datatable" class="table table-border table-thead-bordered table-nowrap card-table"
                                data-hs-datatables-options='{
                            "columnDefs": [{
                                "targets": [0, 3, 6],
                                "orderable": false
                            }],
                            "order": [],
                            "info": {
                            "totalQty": "#datatableWithPaginationInfoTotalQty"
                            },
                            "search": "#datatableSearch",
                            "entries": "#datatableEntries",
                            "pageLength": 25,
                            "isResponsive": false,
                            "isShowPaging": false,
                            "pagination": "datatablePagination"
                        }'>
                                <thead class="thead-light">
                                    <tr>
                                        <th class="border-0 fs-14">{{ translate('messages.SL') }}</th>
                                        <th class="border-0 fs-14">{{ translate('messages.order_ID') }}</th>
                                        <th class="border-0 fs-14">{{ translate('messages.customer') }}</th>
                                        <th class="border-0 fs-14">{{ translate('messages.Rating') }}</th>
                                        <th class="border-0 fs-14">{{ translate('messages.Review ID') }}</th>
                                        <th class="border-0 fs-14">{{ translate('messages.review') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reviews as $k => $review)
                                        <tr>
                                            <td class="fs-14 text-dark">{{ $k + $reviews->firstItem() }}</td>
                                            <td>
                                                <a class="line--limit-1 fs-14 text-dark max-w--220px min-w-135px text-wrap"
                                                    href="{{ route('admin.order.all-details', ['id' => $review->order_id]) }}">{{ $review->order_id }}</a>
                                            </td>
                                            <td>
                                                @if ($review->customer)
                                                    <a class="d-flex align-items-center"
                                                        href="{{ route('admin.customer.view', [$review['user_id']]) }}">
                                                        <span
                                                            class="text-dark fs-14 line--limit-1 max-w--220px min-w-135px text-wrap">
                                                            {{ $review->customer ? $review->customer['f_name'] . ' ' . $review->customer['l_name'] : '' }}
                                                        </span>
                                                    </a>
                                                @else
                                                    {{ translate('messages.customer_not_found') }}
                                                @endif
                                            </td>
                                            <td>
                                                <div class="">
                                                    <div class="d-flex gap-1 align-items-center">
                                                        <span class="d-inline-block mt-half">{{ $review->rating }}</span>
                                                        <i class="tio-star text-warning"></i>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="text-dark fs-14 line--limit-1 max-w--220px min-w-135px text-wrap">
                                                    {{ $review->id }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fs-14 line--limit-2 max-w-390 min-w-220 text-dark text-wrap">
                                                    {{ $review['comment'] }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- End Table -->
                        @if (count($reviews) !== 0)
                            <hr>
                        @endif
                        <div class="page-area">
                            {!! $reviews->links() !!}
                        </div>
                        @if (count($reviews) === 0)
                            <div class="empty--data">
                                <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}"
                                    alt="public">
                                <h5>
                                    {{ translate('no_data_found') }}
                                </h5>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    @endif

    </div>


    <div class="modal fade" id="work_switcher">
        <div class="modal-dialog modal-dialog-centered max-w-500px">
            <div class="modal-content">
                <div class="modal-header pr-3">
                    <button type="button" class="close border bg-modal-btn rounded-circle" data-dismiss="modal">
                        <span aria-hidden="true" class="tio-clear text-light-gray"></span>
                    </button>
                </div>
                <div class="modal-body px-sm-4 px-3 pb-5 pt-0">
                    <div class="text-center">
                        <div>
                            <div class="text-center mb-20">
                                <img width="80"
                                    src="{{ asset('public/assets/admin/img/icons/deliveryman-type.png') }}"
                                    class="">
                                <h5 class="modal-title m-0"></h5>
                            </div>
                            <div class="text-center mb-4">
                                <h3 class="font-weight-normal text-dark">
                                    {{ translate('This deliveryman is currently on') }} <br>
                                    <strong>{{ $deliveryMan->earning ? translate('messages.freelancer') : translate('messages.salary_based') }}</strong>
                                </h3>
                            </div>
                        </div>
                        <div class="bg-light2 rounded p-sm-4 p-3">
                            <p class="fs-14 mb-20 text-body">{{ translate('Do you want to change the delivery type?') }}
                            </p>
                            <div class="btn--container justify-content-center p-0">
                                <a href="{{ route('admin.users.delivery-man.earning', ['id' => $deliveryMan->id, 'status' => $deliveryMan->earning ? 0 : 1]) }}"
                                    class="btn btn--primary min-w-120">
                                    {{ $deliveryMan->earning ? translate('Switch to Salary Based') : translate('Switch to Freelanced Based') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        "use strict";
        $('.request-alert').on('click', function() {
            let url = $(this).data('url');
            let message = $(this).data('message');
            request_alert(url, message);
        })

        function request_alert(url, message) {
            Swal.fire({
                title: '{{ translate('messages.are_you_sure') }}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: '{{ translate('messages.no') }}',
                confirmButtonText: '{{ translate('messages.yes') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    location.href = url;
                }
            })
        }
    </script>
@endpush
