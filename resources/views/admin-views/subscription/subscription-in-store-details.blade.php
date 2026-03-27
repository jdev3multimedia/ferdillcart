@extends('layouts.admin.app')

@section('title',translate('messages.disbursement'))

@push('css_or_js')

@endpush

@section('content')

    <div class="content container-fluid">
        <div class="page-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center py-2">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-start">
                        <img src="{{asset('/public/assets/admin/img/store.png')}}" width="24" alt="img">
                        <div class="w-0 flex-grow pl-2">
                            <h1 class="page-header-title">{{translate('Green Mart Subscription')}}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="js-nav-scroller hs-nav-scroller-horizontal mb-4">
            <ul class="nav nav-tabs border-0 nav--tabs nav--pills">
                <li class="nav-item">
                    <a href="" class="nav-link">Overview</a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">Orders</a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">Foods</a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">Reviews</a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">Discounts</a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">Transactions</a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">Disbursements</a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link active">Subscription</a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link">Settings</a>
                </li>
            </ul>
        </div>
        <div class="card mb-20">
            <div class="card-header border-0 align-items-center">
                <h4 class="card-title align-items-center gap-2">
                    <span class="card-header-icon">
                        <img src="{{asset('public/assets/admin/img/billing.png')}}" alt="">
                    </span>
                    <span class="text-title">Billing</span>
                </h4>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6 col-lg-4">
                        <a class="__card-2 __bg-1 flex-row align-items-center gap-4" href="#">
                            <img src="{{asset('public/assets/admin/img/expiring.png')}}" alt="report/new" class="w-60px">
                            <div class="w-0 flex-grow-1 py-md-3">
                                <span class="text-body">Expire Date</span>
                                <h4 class="title m-0">20 Jun 2024</h4>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <a class="__card-2 __bg-8 flex-row align-items-center gap-4" href="#">
                            <img src="{{asset('public/assets/admin/img/total-bill.png')}}" alt="report/new" class="w-60px">
                            <div class="w-0 flex-grow-1 py-md-3">
                                <span class="text-body">Total Bill</span>
                                <h4 class="title m-0">$ 2,000</h4>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <a class="__card-2 __bg-4 flex-row align-items-center gap-4" href="#">
                            <img src="{{asset('public/assets/admin/img/number.png')}}" alt="report/new" class="w-60px">
                            <div class="w-0 flex-grow-1 py-md-3">
                                <span class="text-body">Number of Uses</span>
                                <h4 class="title m-0">2</h4>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header border-0 align-items-center">
                <h4 class="card-title align-items-center gap-2">
                    <span class="card-header-icon">
                        <img width="25" src="{{asset('public/assets/admin/img/subscription-plan/subscribed-user.png')}}" alt="">
                    </span>
                    <span>Package Overview</span>
                </h4>
            </div>
            <div class="card-body pt-0">
                <div class="__bg-F8F9FC-card __plan-details">
                    <div class="d-flex flex-wrap flex-md-nowrap justify-content-between __plan-details-top">
                        <div class="left">
                            <h3 class="name">Basic Plan</h3>
                            <div class="font-medium text--title">Most popular plan for small business or startup</div>
                        </div>
                        <h3 class="right">$70 /<small class="font-medium text--title">3 month</small></h3>
                    </div>

                    <div class="check--grid-wrapper mt-3 max-w-850px">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{asset('/public/assets/admin/img/subscription-plan/check.png')}}" alt="">
                                <span class="form-check-label text-dark">400 Order</span>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{asset('/public/assets/admin/img/subscription-plan/check.png')}}" alt="">
                                <span class="form-check-label text-dark">POS Access</span>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{asset('/public/assets/admin/img/subscription-plan/check.png')}}" alt="">
                                <span class="form-check-label text-dark">400 Order</span>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{asset('/public/assets/admin/img/subscription-plan/check.png')}}" alt="">
                                <span class="form-check-label text-dark">400 Products Upload</span>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{asset('/public/assets/admin/img/subscription-plan/check.png')}}" alt="">
                                <span class="form-check-label text-dark">Mobile App Access</span>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{asset('/public/assets/admin/img/subscription-plan/check.png')}}" alt="">
                                <span class="form-check-label text-dark">400 Order</span>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{asset('/public/assets/admin/img/subscription-plan/check.png')}}" alt="">
                                <span class="form-check-label text-dark">POS Access</span>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{asset('/public/assets/admin/img/subscription-plan/check.png')}}" alt="">
                                <span class="form-check-label text-dark">400 Order</span>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{asset('/public/assets/admin/img/subscription-plan/check.png')}}" alt="">
                                <span class="form-check-label text-dark">400 Products Upload</span>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{asset('/public/assets/admin/img/subscription-plan/check.png')}}" alt="">
                                <span class="form-check-label text-dark">Mobile App Access</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn--container justify-content-end mt-20">
                    <button type="button" class="btn btn--danger text-white">Cancel Subscription</button>
                    <button type="button" data-toggle="modal" data-target="#plan-modal" class="btn btn--primary">Change/Renew Subscription Plan</button>
                </div>
            </div>
        </div>


        <div class="modal fade show" id="plan-modal">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header px-3 pt-3">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true" class="tio-clear"></span>
                        </button>
                    </div>
                    <div class="modal-body px-4 pt-0">
                        <div>
                            <div class="text-center">
                                <h2 class="modal-title">Change Subscription Plan</h2>
                            </div>
                            <div class="text-center text-14 mb-4 pb-3">
                                Renew or shift your plan to get better experience!
                            </div>
                            <div class="plan-slider owl-theme owl-carousel owl-refresh">
                                <div class="__plan-item hover">
                                    <div class="inner-div">
                                        <div class="text-center">
                                            <h3 class="title">Commission Base</h3>
                                            <h2 class="price">15%</h2>
                                        </div>
                                        <div class="py-5 mt-4">
                                            <div class="info-text text-center">
                                                Store will pay 15% commission to 6amMart from each order. You will get access of all the features and options  in store panel , app and interaction with user.
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="button" class="btn btn--primary" data-dismiss="modal" data-toggle="modal" data-target="#shift-modal">Shift in this plan</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="__plan-item hover">
                                    <div class="inner-div">
                                        <div class="text-center">
                                            <h3 class="title">BASIC</h3>
                                            <h2 class="price">15%</h2>
                                            <div class="day-count">60 days</div>
                                        </div>
                                        <ul class="info">
                                            <li>
                                                <i class="tio-checkmark-circle"></i> <span>Free Support 24/7</span>
                                            </li>
                                            <li>
                                                <i class="tio-checkmark-circle"></i> <span>Databases</span>
                                            </li>
                                            <li>
                                                <i class="tio-checkmark-circle"></i> <span>Email</span>
                                            </li>
                                            <li>
                                                <i class="tio-checkmark-circle"></i> <span>Unlimited Traffic</span>
                                            </li>
                                        </ul>
                                        <div class="text-center">
                                            <button type="button" class="btn btn--primary" data-dismiss="modal" data-toggle="modal" data-target="#shift-modal">Shift in this plan</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="__plan-item hover active">
                                    <div class="inner-div">
                                        <div class="text-center">
                                            <h3 class="title">STANDARED</h3>
                                            <h2 class="price">15%</h2>
                                            <div class="day-count">60 days</div>
                                        </div>
                                        <ul class="info">
                                            <li>
                                                <i class="tio-checkmark-circle"></i> <span>Free Support 24/7</span>
                                            </li>
                                            <li>
                                                <i class="tio-checkmark-circle"></i> <span>Databases</span>
                                            </li>
                                            <li>
                                                <i class="tio-checkmark-circle"></i> <span>Email</span>
                                            </li>
                                            <li>
                                                <i class="tio-checkmark-circle"></i> <span>Unlimited Traffic</span>
                                            </li>
                                        </ul>
                                        <div class="text-center">
                                            <button type="button" class="btn btn--primary" data-dismiss="modal" data-toggle="modal" data-target="#renew-modal">Renew</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="__plan-item hover">
                                    <div class="inner-div">
                                        <div class="text-center">
                                            <h3 class="title">PREMIUM</h3>
                                            <h2 class="price">15%</h2>
                                            <div class="day-count">60 days</div>
                                        </div>
                                        <ul class="info">
                                            <li>
                                                <i class="tio-checkmark-circle"></i> <span>Free Support 24/7</span>
                                            </li>
                                            <li>
                                                <i class="tio-checkmark-circle"></i> <span>Databases</span>
                                            </li>
                                            <li>
                                                <i class="tio-checkmark-circle"></i> <span>Email</span>
                                            </li>
                                            <li>
                                                <i class="tio-checkmark-circle"></i> <span>Unlimited Traffic</span>
                                            </li>
                                        </ul>
                                        <div class="text-center">
                                            <button type="button" class="btn btn--primary" data-dismiss="modal" data-toggle="modal" data-target="#shift-modal">Shift in this plan</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade show" id="shift-modal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header px-3 pt-3">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true" class="tio-clear"></span>
                        </button>
                    </div>
                    <div class="modal-body px-4 pt-0">
                        <div>
                            <div class="text-center mb-4 pb-2">
                                <h2 class="modal-title">Shift to New Subscription Plan</h2>
                            </div>
                            <div class="change-plan-wrapper align-items-center">
                                <div class="__plan-item">
                                    <div class="inner-div">
                                        <div class="text-center">
                                            <h3 class="title">BASIC</h3>
                                            <h2 class="price">15%</h2>
                                            <div class="day-count">60 days</div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Plan Seperator Arrow -->
                                <div class="plan-seperator-arrow mx-auto">
                                    <img src="{{asset('public/assets/admin/img/exchange.svg')}}" alt="" class="w-100">
                                </div>
                                <!-- Plan Seperator Arrow -->
                                <div class="__plan-item active">
                                    <div class="inner-div">
                                        <div class="text-center">
                                            <h3 class="title">STANDARD</h3>
                                            <h2 class="price">15%</h2>
                                            <div class="day-count">60 days</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4 mb-lg-5 subscription__plan-info-wrapper bg-ECEEF1 rounded-20">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="subscription__plan-info">
                                            <div class="info">
                                                Validity
                                            </div>
                                            <h4 class="subtitle">60 Days</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="subscription__plan-info">
                                            <div class="info">
                                                Price
                                            </div>
                                            <h4 class="subtitle">$1,199.00</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="subscription__plan-info">
                                            <div class="info">
                                                Bill status
                                            </div>
                                                                    <h4 class="subtitle">Renew</h4>
                                                            </div>
                                    </div>
                                </div>
                            </div>
                            <h4 class="mb-4">Pay Via Online <span class="font-regular text-body">(Faster & secure way to pay bill)</span></h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="payment-item">
                                        <input type="radio" class="d-none" name="payment">
                                        <div class="payment-item-inner">
                                            <div class="check">
                                                <img src="{{asset('/public/assets/admin/img/check-1.png')}}" class="uncheck" alt="">
                                                <img src="{{asset('/public/assets/admin/img/check-2.png')}}" class="check" alt="">
                                            </div>
                                            <span>Bkash</span>
                                            <img class="ml-auto" src="{{asset('/public/assets/admin/img/bkash1.png')}}" width="30" alt="">
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="payment-item">
                                        <input type="radio" class="d-none" name="payment">
                                        <div class="payment-item-inner">
                                            <div class="check">
                                                <img src="{{asset('/public/assets/admin/img/check-1.png')}}" class="uncheck" alt="">
                                                <img src="{{asset('/public/assets/admin/img/check-2.png')}}" class="check" alt="">
                                            </div>
                                            <span>Marcado pago</span>
                                            <img class="ml-auto" src="{{asset('/public/assets/admin/img/marcado1.png')}}" width="30" alt="">
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="payment-item">
                                        <input type="radio" class="d-none" name="payment">
                                        <div class="payment-item-inner">
                                            <div class="check">
                                                <img src="{{asset('/public/assets/admin/img/check-1.png')}}" class="uncheck" alt="">
                                                <img src="{{asset('/public/assets/admin/img/check-2.png')}}" class="check" alt="">
                                            </div>
                                            <span>SSL COMMERZ</span>
                                            <img class="ml-auto" src="{{asset('/public/assets/admin/img/sslcomz1.png')}}" width="60" alt="">
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="payment-item">
                                        <input type="radio" class="d-none" name="payment">
                                        <div class="payment-item-inner">
                                            <div class="check">
                                                <img src="{{asset('/public/assets/admin/img/check-1.png')}}" class="uncheck" alt="">
                                                <img src="{{asset('/public/assets/admin/img/check-2.png')}}" class="check" alt="">
                                            </div>
                                            <span>PayStack</span>
                                            <img class="ml-auto" src="{{asset('/public/assets/admin/img/paystack1.png')}}" width="30" alt="">
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="btn--container justify-content-end mt-20">
                                <button type="reset" data-dismiss="modal" class="btn btn--reset">Cancel</button>
                                <button type="submit" class="btn btn--primary">Renew Subscription Plan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade show" id="renew-modal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header px-3 pt-3">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true" class="tio-clear"></span>
                        </button>
                    </div>
                    <div class="modal-body px-4 pt-0">
                        <div>
                            <div class="text-center mb-4 pb-2">
                                <h2 class="modal-title">Renew Subscription Plan</h2>
                            </div>
                            <div class="change-plan-wrapper align-items-center">
                                <div class="__plan-item active">
                                    <div class="inner-div">
                                        <div class="text-center">
                                            <h3 class="title">STANDARD</h3>
                                            <h2 class="price">15%</h2>
                                            <div class="day-count">60 days</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4 mb-lg-5 subscription__plan-info-wrapper bg-ECEEF1 rounded-20">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="subscription__plan-info">
                                            <div class="info">
                                                Validity
                                            </div>
                                            <h4 class="subtitle">365 Days</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="subscription__plan-info">
                                            <div class="info">
                                                Price
                                            </div>
                                            <h4 class="subtitle">$1,199.00</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="subscription__plan-info">
                                            <div class="info">
                                                Bill status
                                            </div>
                                            <h4 class="subtitle">Renew</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h4 class="mb-4">Pay Via Online <span class="font-regular text-body">(Faster & secure way to pay bill)</span></h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="payment-item">
                                        <input type="radio" class="d-none" name="payment">
                                        <div class="payment-item-inner">
                                            <div class="check">
                                                <img src="{{asset('/public/assets/admin/img/check-1.png')}}" class="uncheck" alt="">
                                                <img src="{{asset('/public/assets/admin/img/check-2.png')}}" class="check" alt="">
                                            </div>
                                            <span>Bkash</span>
                                            <img class="ml-auto" src="{{asset('/public/assets/admin/img/bkash1.png')}}" width="30" alt="">
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="payment-item">
                                        <input type="radio" class="d-none" name="payment">
                                        <div class="payment-item-inner">
                                            <div class="check">
                                                <img src="{{asset('/public/assets/admin/img/check-1.png')}}" class="uncheck" alt="">
                                                <img src="{{asset('/public/assets/admin/img/check-2.png')}}" class="check" alt="">
                                            </div>
                                            <span>Marcado pago</span>
                                            <img class="ml-auto" src="{{asset('/public/assets/admin/img/marcado1.png')}}" width="30" alt="">
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="payment-item">
                                        <input type="radio" class="d-none" name="payment">
                                        <div class="payment-item-inner">
                                            <div class="check">
                                                <img src="{{asset('/public/assets/admin/img/check-1.png')}}" class="uncheck" alt="">
                                                <img src="{{asset('/public/assets/admin/img/check-2.png')}}" class="check" alt="">
                                            </div>
                                            <span>SSL COMMERZ</span>
                                            <img class="ml-auto" src="{{asset('/public/assets/admin/img/sslcomz1.png')}}" width="60" alt="">
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="payment-item">
                                        <input type="radio" class="d-none" name="payment">
                                        <div class="payment-item-inner">
                                            <div class="check">
                                                <img src="{{asset('/public/assets/admin/img/check-1.png')}}" class="uncheck" alt="">
                                                <img src="{{asset('/public/assets/admin/img/check-2.png')}}" class="check" alt="">
                                            </div>
                                            <span>PayStack</span>
                                            <img class="ml-auto" src="{{asset('/public/assets/admin/img/paystack1.png')}}" width="30" alt="">
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="btn--container justify-content-end mt-20">
                                <button type="reset" data-dismiss="modal" class="btn btn--reset">Cancel</button>
                                <button type="submit" class="btn btn--primary">Renew Subscription Plan</button>
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
        $('.plan-slider').owlCarousel({
            loop: false,
            margin: 30,
            responsiveClass:true,
            nav:false,
            dots:false,
            items: 3,
            center: true,
            autoplay:true,
            autoplayTimeout:2500,
            autoplayHoverPause:true,

            responsive:{
                0: {
                    items:1.1,
                    margin: 10,
                },
                375: {
                    items:1.3,
                    margin: 30,
                },
                576: {
                    items:1.7,
                },
                768: {
                    items:2.2,
                    margin: 40,
                },
                992: {
                    items: 3,
                    margin: 40,
                },
                1200: {
                    items: 4,
                    margin: 40,
                }
            }
        })
    </script>
@endpush

