<div class="plan-slider owl-theme owl-carousel owl-refresh">
    @forelse ($packages as $key=> $package)
        <label
            class="__plan-item {{ (count($packages) > 4 && $key == 2) || (count($packages) < 5 && $key == 1) ? 'active' : '' }} ">
            <input type="radio" name="package_id"  {{ (count($packages) > 4 && $key == 2) || (count($packages) < 5 && $key == 1) ? 'checked' : '' }} id="package_id{{ $key }}" value="{{ $package->id }}"
                class="d-none">
            <div class="inner-div">
                <div class="text-center">

                    <h3 class="title">{{ $package->package_name }}</h3>
                    <h2 class="price">
                        {{ \App\CentralLogics\Helpers::format_currency($package->price) }}
                    </h2>
                    <div class="day-count">{{ $package->validity }}
                        {{ translate('messages.days') }}</div>
                </div>
                <ul class="info">

                    @if ($package->pos)
                        <li>
                            <img src="{{ asset('/public/assets/landing/img/check-1.svg') }}" class="check"
                                alt="">
                            <img src="{{ asset('/public/assets/landing/img/check-2.svg') }}" class="check-white"
                                alt=""> <span>
                                {{ translate('messages.POS') }} </span>
                        </li>
                    @endif
                    @if ($package->mobile_app)
                        <li>
                            <img src="{{ asset('/public/assets/landing/img/check-1.svg') }}" class="check"
                                alt="">
                            <img src="{{ asset('/public/assets/landing/img/check-2.svg') }}" class="check-white"
                                alt=""> <span>
                                {{ translate('messages.mobile_app') }} </span>
                        </li>
                    @endif
                    @if ($package->chat)
                        <li>
                            <img src="{{ asset('/public/assets/landing/img/check-1.svg') }}" class="check"
                                alt="">
                            <img src="{{ asset('/public/assets/landing/img/check-2.svg') }}" class="check-white"
                                alt=""> <span>
                                {{ translate('messages.chatting_options') }} </span>
                        </li>
                    @endif
                    @if ($package->review)
                        <li>
                            <img src="{{ asset('/public/assets/landing/img/check-1.svg') }}" class="check"
                                alt="">
                            <img src="{{ asset('/public/assets/landing/img/check-2.svg') }}" class="check-white"
                                alt=""> <span>
                                {{ translate('messages.review_section') }} </span>
                        </li>
                    @endif
                    @if ($package->self_delivery)
                        <li>
                            <img src="{{ asset('/public/assets/landing/img/check-1.svg') }}" class="check"
                                alt="">
                            <img src="{{ asset('/public/assets/landing/img/check-2.svg') }}" class="check-white"
                                alt=""> <span>
                                {{ translate('messages.self_delivery') }} </span>
                        </li>
                    @endif
                    @if ($package->max_order == 'unlimited')
                        <li>
                            <img src="{{ asset('/public/assets/landing/img/check-1.svg') }}" class="check"
                                alt="">
                            <img src="{{ asset('/public/assets/landing/img/check-2.svg') }}" class="check-white"
                                alt=""> <span>
                                {{ isset($module) && $module == 'rental' ?  translate('messages.Unlimited_Trips') : translate('messages.Unlimited_Orders') }} </span>
                        </li>
                    @else
                        <li>
                            <img src="{{ asset('/public/assets/landing/img/check-1.svg') }}" class="check"
                                alt="">
                            <img src="{{ asset('/public/assets/landing/img/check-2.svg') }}" class="check-white"
                                alt=""> <span>
                                {{ $package->max_order }}
                                {{ isset($module) && $module == 'rental' ?  translate('messages.Trips') : translate('messages.Orders') }} </span>
                        </li>
                    @endif
                    @if ($package->max_product == 'unlimited')
                        <li>
                            <img src="{{ asset('/public/assets/landing/img/check-1.svg') }}" class="check"
                                alt="">
                            <img src="{{ asset('/public/assets/landing/img/check-2.svg') }}" class="check-white"
                                alt=""> <span>
                                {{ translate('messages.Unlimited_uploads') }} </span>
                        </li>
                    @else
                        <li>
                            <img src="{{ asset('/public/assets/landing/img/check-1.svg') }}" class="check"
                                alt="">
                            <img src="{{ asset('/public/assets/landing/img/check-2.svg') }}" class="check-white"
                                alt=""> <span>
                                {{ $package->max_product }}
                                {{ translate('messages.uploads') }} </span>
                        </li>
                    @endif
                </ul>
            </div>
        </label>

    @empty
    @endforelse
</div>
<script>
    $('.plan-slider').owlCarousel({
        loop: false,
        margin: 30,
        responsiveClass: true,
        nav: false,
        dots: false,
        items: 3,
        startPosition: 1,
        responsive: {
            0: {
                items: 1.1,
                margin: 10,
            },
            375: {
                items: 1.3,
                margin: 30,
            },
            576: {
                items: 1.7,
            },
            768: {
                items: 2.2,
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

        $(window).on('load', function() {
            $('input[name="business_plan"]').each(function() {
                if ($(this).is(':checked')) {
                    if ($(this).val() == 'subscription-base') {
                        $('#subscription-plan').show()
                    } else {
                        $('#subscription-plan').hide()
                    }
                }
            })
            $('input[name="package_id"]').each(function() {
                if ($(this).is(':checked')) {
                    $(this).closest('.__plan-item').addClass('active')
                }
            })
        })
        $('input[name="business_plan"]').on('change', function() {
            if ($(this).val() == 'subscription-base') {
                $('#subscription-plan').slideDown()
            } else {
                $('#subscription-plan').slideUp()
            }
        })
        $('input[name="package_id"]').on('change', function() {
            $('input[name="package_id"]').each(function() {
                $(this).closest('.__plan-item').removeClass('active')
            })
            $(this).closest('.__plan-item').addClass('active')
        })
        $('#reset-btn').on('click', function() {
            location.reload()
        })
    </script>
