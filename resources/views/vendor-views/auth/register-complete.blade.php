@extends('layouts.landing.app')
@section('title', translate('messages.vendor_registration'))
@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('public/assets/admin/css/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/admin/css/view-pages/vendor-registration.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/landing/css/select2.min.css') }}"/>
@endpush
@section('content')
    <section class="m-0 py-5">
        <div class="container">
            <!-- Page Header -->
            <div class="section-header">
                <h2 class="title mb-2">{{ translate('messages.vendor') }} <span class="text--base">{{translate('application')}}</span></h2>
            </div>
            <!-- End Page Header -->

            <!-- Stepper -->
                <div class="stepper">
                    <div style class="stepper-item active">
                        <div class="step-name">{{ translate('General Info') }}</div>
                    </div>
                    <div class="stepper-item active">
                        <div class="step-name">{{ translate('Business Plan') }}</div>
                    </div>
                    <div  class="stepper-item active">
                        <div class="step-name  {{  isset($payment_status) && $payment_status == 'fail' ? 'text-danger' : '' }}">{{ translate('Complete') }}</div>
                    </div>
                </div>
            <!-- Stepper -->


            <div class="reg-form js-validate">
                <div class="card __card mb-3">
                    <div class="card-header border-0 pb-0 text-center pt-5">
                            @if ( isset($payment_status) && $payment_status == 'fail')
                            <img src="{{asset('/public/assets/landing/img/Failed.gif')}}" width="40" alt="" class="mb-4">
                            <h5 class="card-title text-center">
                                {{ translate('Transaction Failed!') }}
                            </h5>
                            @else
                            <img src="{{asset('/public/assets/landing/img/Success.gif')}}" width="40" alt="" class="mb-4">
                            <h5 class="card-title text-center">
                                {{ translate('Congratulations!') }}
                            </h5>

                            @endif


                    </div>
                    <div class="card-body p-4 pb-5">
                        <div class="register-congrats-txt">
                            @if (isset($type) && $type == 'commission')
                            {{ translate('You’ve opted for our commission-based plan. Admin will review the details and activate your account shortly. To explore the site.') }}
                            <a href="{{ route('home',['new_user'=> true]) }}" class="text-base font-bold">{{ translate('visit_here') }}</a>

                            @elseif( isset($payment_status) && $payment_status == 'fail')
                            {{ translate('Sorry, Your Transaction can’t be completed. Please choose another payment method.') }}
                            <a href="{{ route('restaurant.back',['store_id' => $store_id ?? null]) }}" class="text-base font-bold">{{ translate('Try_again') }}</a>
                            @else
                            {{ translate('Thank you for your subscription purchase! Your payment was successfully processed. Please note that your subscription will be activated once it has been approved by our Admin Team. To explore the site') }}
                            <a href="{{ route('home',['new_user'=> true]) }}" class="text-base font-bold">{{ translate('visit_here') }}</a>
                            @endif

                        </div>

                        {{-- @if (! (isset($payment_status) && $payment_status == 'fail'))
                        <div class="text-center py-2">
                            {{ translate('or') }}
                        </div>
                        <div class="text-center">
                            <a href="{{ route('home',['new_user'=> true]) }}" class="text-base font-bold">{{ translate('Continue to Home Page') }}</a>
                        </div>
                        @endif --}}
                    </div>
                </div>
            </div>
        </div>
    </section>

    @endsection
    @push('script_2')
    <script>
        @if (! (isset($payment_status) && $payment_status == 'fail'))
        document.addEventListener("DOMContentLoaded", function() {
            var homeLink = document.getElementById('home-link');
            var newUrl = "{{ route('home',['new_user'=> true]) }}";
            homeLink.setAttribute('href', newUrl);
        });
        @endif
    </script>
    @endpush
