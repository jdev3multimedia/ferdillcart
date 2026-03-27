<!DOCTYPE html>
<?php

$country= \App\CentralLogics\Helpers::get_business_settings('country');
$countryCode= strtolower($country?$country:'auto');
?>
<html dir="{{ session()->get('site_direction') }}" lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{session()->get('site_direction') === 'rtl'?'active':'' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">
    <!-- Title -->
    <title>@yield('title')</title>
    <!-- Favicon -->
    @php($logo=\App\Models\BusinessSetting::where(['key'=>'icon'])->first())
    <link rel="shortcut icon" href="">
    <link rel="icon" type="image/x-icon" href="{{\App\CentralLogics\Helpers::get_full_url('business', $logo?->value?? '', $logo?->storage[0]?->value ?? 'public','favicon')}}">
    <!-- Font -->
    <link href="{{asset('public/assets/admin/css/fonts.css')}}" rel="stylesheet">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/vendor.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/vendor/icon-set/style.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/custom.css')}}">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/owl.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/theme.minc619.css?v=1.0')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/bootstrap-tour-standalone.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/emogi-area.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/style.css')}}">

    <link rel="stylesheet" href="{{asset('public/assets/admin/intltelinput/css/intlTelInput.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/upload-single-image.css')}}">


    @stack('css_or_js')

    <script src="{{asset('public/assets/admin/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js')}}"></script>
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/toastr.css')}}">
</head>

<body class="footer-offset">
    @if (env('APP_MODE')=='demo')
        <div class="direction-toggle">
            <i class="tio-settings"></i>
            <span></span>
        </div>
    @endif

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="loading" class="initial-hidden">
                <div class="loader--inner">
                    <img width="200" src="{{asset('public/assets/admin/img/loader.gif')}}" alt="image">
                </div>
            </div>
        </div>
    </div>
</div>
@if (!isset($module_type))
@php($module_type = Config::get('module.current_module_type'))
@endif

<!-- Builder -->
@include('layouts.admin.partials._front-settings')
<!-- End Builder -->

<!-- JS Preview mode only -->
@include('layouts.admin.partials._header')

@if(Request::is('admin/payment/configuration*') || Request::is('admin/sms/configuration*') || Request::is('taxvat/*'))
@php($module_type = 'settings')
@endif

    @if($module_type == 'rental')
        @include("rental::admin.partials._sidebar_{$module_type}")
    @else
        @include("layouts.admin.partials._sidebar_{$module_type}")
    @endif

<!-- END ONLY DEV -->

<main id="content" role="main" class="main pointer-event">
    <!-- Content -->
@yield('content')
<!-- End Content -->

    <!-- Footer -->
@include('layouts.admin.partials._footer')
<!-- End Footer -->

    <div class="d-none" id="text-validate-translate"
        data-required="{{ translate('this_field_is_required') }}"
        data-something-went-wrong="{{ translate('something_went_wrong!') }}"
        data-max-limit-crossed="{{ translate('max_limit_crossed') }}"
        data-file-size-larger="{{ translate('file_size_is_larger') }}"
        data-passwords-do-not-match="{{ translate('passwords_do_not_match') }}"
        data-valid-email="{{ translate('please_enter_a_valid_email') }}"
        data-password-validation="{{ translate('password_must_be_8+_chars_with_upper,_lower,_number_&_symbol') }}"
    ></div>

    <div class="modal fade" id="popup-modal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center">
                                <h2 class="update_notification_text">
                                    <i class="tio-shopping-cart-outlined"></i> {{translate('messages.You have new order, Check Please.')}}
                                </h2>
                                <hr>
                                <button class="btn btn-primary check-order">{{translate('messages.Ok, let me check')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="toggle-modal">
        <div class="modal-dialog modal-dialog-centered status-warning-modal">
            <div class="modal-content">
                <div class="modal-header px-2 pt-2">
                    <button type="button" class="close btn btn--reset btn-circle" data-dismiss="modal">
                        <span aria-hidden="true" class="tio-clear fs-20 opacity-70"></span>
                    </button>
                </div>
                <div class="modal-body pb-4">
                    <div class="max-349 mx-auto mb-20 mt-2">
                        <div class="mb-30">
                            <div class="text-center mb-1">
                                <img id="toggle-image" alt="" class="mb-20 initial--10">
                                <h3 class="modal-title" id="toggle-title"></h3>
                            </div>
                            <div class="text-center fs-14" id="toggle-message">
                            </div>
                        </div>
                        <div class="btn--container justify-content-center">
                            <button id="reset_btn" type="reset" class="btn btn--reset min-w-120" data-dismiss="modal">
                                {{translate("No")}}
                            </button>
                            <button type="button" id="toggle-ok-button" class="btn btn--primary min-w-120 confirm-Toggle" data-dismiss="modal" >{{translate('Yes')}}</button>
                        </div>
                        <div class="text-center mt-3" id="toggle-footer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="toggle-status-modal">
        <div class="modal-dialog modal-dialog-centered status-warning-modal">
            <div class="modal-content">
                <div class="modal-header px-2 pt-2">
                    <button type="button" class="close btn btn--reset btn-circle" data-dismiss="modal">
                        <span aria-hidden="true" class="tio-clear fs-20 opacity-70"></span>
                    </button>
                </div>
                <div class="modal-body pb-3 pt-0">
                    <div class="max-349 mx-auto mb-20">
                        <div class="mb-3">
                            <div class="text-center">
                                <img id="toggle-status-image" alt="" class="mb-20 initial--10">
                                <h5 class="modal-title" id="toggle-status-title"></h5>
                            </div>
                            <div class="text-center" id="toggle-status-message">
                            </div>
                        </div>
                        <div class="btn--container justify-content-center">
                            <button id="reset_btn" type="reset" class="btn btn--reset min-w-120" data-dismiss="modal">
                                {{translate("No")}}
                            </button>
                            <button type="button" id="toggle-status-ok-button" class="btn btn--primary min-w-120 confirm-Status-Toggle" data-dismiss="modal" >{{translate('Yes')}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="instruction-modal">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close instruction-Modal-Close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/0sus46BflpU" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                      </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="email-modal">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close email-Modal-Close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/_BIHsClZtOE" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                      </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="new-dynamic-submit-model">
        <div class="modal-dialog modal-dialog-centered modal-dialog-centered status-warning-modal">
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
                                <img id="image-src" class="mb-20">
                                <h5 class="modal-title" id="toggle-title"></h5>
                            </div>
                            <div class="text-center" id="toggle-message">
                                <h3 id="modal-title"></h3>
                                <div id="modal-text"></div>
                            </div>

                            </div>
                            <div class="mb-4 d-none" id="note-data">
                                <textarea class="form-control" placeholder="{{ translate('your_note_here') }}" id="get-text-note" cols="5" ></textarea>
                            </div>
                        <div class="btn--container justify-content-center">
                            <div id="hide-buttons">
                                <div class="d-flex justify-content-center flex-wrap gap-3">
                                    <button data-dismiss="modal" id="cancel6_btn_text" class="btn btn--cancel min-w-120" >{{translate("Not_Now")}}</button>
                                    <button type="button" id="new-dynamic-ok-button" class="btn btn-primary confirm-model min-w-120">{{translate('Yes')}}</button>
                                </div>
                            </div>

                            <button data-dismiss="modal"  type="button" id="new-dynamic-ok-button-show" class="btn btn--primary  d-none min-w-120">{{translate('Okay')}}</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--- Global Image -->
    <div id="imageModal" class="imageModal modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-end gap-3 border-0 p-2">
                    <button type="button" class="modal_img-btn border-0 btn-circle rounded-circle bg-section2 shadow-none fs-8 m-0"
                            data-dismiss="modal" aria-label="Close">
                            <i class="tio-clear"></i>
                    </button>
                </div>
                <div class="modal-body text-center p-3 pt-0">
                    <div class="imageModal_img_wrapper">
                        <img src="" class="img-fluid imageModal_img" alt="{{ translate('Preview_Image') }}">
                        <div class="imageModal_btn_wrapper m-1">
                            <a href="javascript:" class="btn icon-btn px-1 py-1 download_btn" title="{{ translate('Download') }}" download>
                                <i class="tio-arrow-large-downward"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-none" id="default-text-data"
         data-default-image-src="{{ asset('public/assets/admin/img/upload-img.png') }}"
    ></div>



<?php
$current_module_type_for_search = null;
if(in_array(config('module.current_module_type'),config('module.module_type') )){
    $current_module_type_for_search = config('module.current_module_type');
}
?>


<!-- ========== END MAIN CONTENT ========== -->

<!-- ========== END SECONDARY CONTENTS ========== -->
<script src="{{asset('public/assets/admin')}}/js/custom.js"></script>
<script src="{{asset('public/assets/admin')}}/js/firebase.min.js"></script>
<!-- JS Implementing Plugins -->

@stack('script')
<!-- JS Front -->

<script src="{{asset('public/assets/admin')}}/js/vendor.min.js"></script>
<script src="{{asset('public/assets/admin')}}/js/jquery.validate.min.js"></script>
<script src="{{asset('public/assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset('public/assets/admin')}}/js/sweet_alert.js"></script>
<script src="{{asset('public/assets/admin')}}/js/bootstrap-tour-standalone.min.js"></script>
<script src="{{asset('public/assets/admin/js/owl.min.js')}}"></script>
<script src="{{asset('public/assets/admin')}}/js/emogi-area.js"></script>
<script src="{{asset('public/assets/admin')}}/js/toastr.js"></script>
<script src="{{asset('public/assets/admin/js/app-blade/admin.js')}}"></script>
<script src="{{asset('public/assets/admin/js/form-validate.js')}}"></script>
<script src="{{asset('public/assets/admin/js/upload-single-image.js')}}"></script>
<script src="{{asset('public/assets/admin/js/multiple-file-upload.js')}}"></script>


{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{translate($error)}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif
<!-- JS Plugins Init. -->


@stack('script_2')
<script>
    let baseUrl = '{{ url('/') }}';
</script>

<script src="{{asset('public/assets/admin/js/view-pages/common.js')}}"></script>
<script src="{{asset('public/assets/admin/js/keyword-highlighted.js')}}"></script>
<audio id="myAudio">
    <source src="{{asset('public/assets/admin/sound/notification.mp3')}}" type="audio/mpeg">
</audio>
<script>
    var audio = document.getElementById("myAudio");
    function playAudio() {
        audio.play();
    }

    function pauseAudio() {
        audio.pause();
    }
"use strict";


    @php($hasModules = \App\Models\Module::Active()->exists())

    @if(!$hasModules)
    $('#instruction-modal').show();
    @endif

    $('.restart-Tour').on('click',function (){
        @if($hasModules)
            tour.restart();
            $('body').css('overflow','hidden')
        @endif
    });


    $('.log-out').on('click',function (){

        Swal.fire({
            title: '{{ translate('Do you want to sign out?') }}',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonColor: '#FC6A57',
            cancelButtonColor: '#363636',
            confirmButtonText: `{{ translate('yes')}}`,
            cancelButtonText: `{{ translate('Cancel')}}`,
            }).then((result) => {
            if (result.value) {
            location.href='{{route('logout')}}';
            }
        })

    });


    function route_alert(route, message, title="{{translate('messages.are_you_sure')}}") {
        Swal.fire({
            title: title,
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: '{{ translate('messages.no') }}',
            confirmButtonText: '{{ translate('messages.Yes') }}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                location.href = route;
            }
        })
    }

    $('.form-alert').on('click', function () {
        let id = $(this).data('id');
        let title = $(this).data('title');
        let message = $(this).data('message');
        let image = $(this).data('image-url');
        let cancel = $(this).data('cancel-btn');
        let confirm = $(this).data('confirm-btn');

        if (!title || title === "") {
            title = '{{ translate('messages.Are you sure?') }}';
        }
        if (!cancel || cancel === "") {
            cancel = '{{ translate('messages.no') }}';
        }
        if (!confirm || confirm === "") {
            confirm = '{{ translate('messages.Yes') }}';
        }
        if (!image || image === "") {
            image = "{{ asset('public/assets/admin/img/off-danger.png') }}";
        }

        Swal.fire({
            title: title,
            imageUrl: image,
            imageWidth: 80,
            imageHeight: 80,
            imageAlt: 'Custom icon',
            text: message,
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: cancel,
            confirmButtonText: confirm,
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $('#' + id).submit();
            }
        });
    });

    $('.canceled-status').on('click',function (){
        let route = $(this).data('url');
        let message = $(this).data('message');
        let processing = $(this).data('processing')??false;
        cancelled_status(route, message, processing);
    })

    function cancelled_status(route, message, processing = false) {
        Swal.fire({
            //text: message,
            title: '<?php echo e(translate('messages.Are you sure ?')); ?>',
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: '<?php echo e(translate('messages.Cancel')); ?>',
            confirmButtonText: '<?php echo e(translate('messages.submit')); ?>',
            inputPlaceholder: "<?php echo e(translate('Enter_a_reason')); ?>",
            input: 'text',
            html: message + '<br/>'+'<label><?php echo e(translate('Enter_a_reason')); ?></label>',
            inputValue: processing,
            preConfirm: (note) => {
                location.href = route + '&note=' + note;
            },
            allowOutsideClick: () => !Swal.isLoading()
        })
    }

    function set_mail_filter(url, id, filter_by) {
        Swal.fire({
            title: '{{ translate('messages.Are you sure?') }}',
            text: 'Please save changes before switching template',
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: '{{ translate('messages.no') }}',
            confirmButtonText: '{{ translate('messages.Yes') }}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                let nurl = new URL(url);
                nurl.searchParams.set(filter_by, id);
                location.href = nurl;
            }
        })
    }


    function copy_text(copyText) {
        navigator.clipboard.writeText(copyText);
        toastr.success('{{translate('messages.text_copied')}}', {
            CloseButton: true,
            ProgressBar: true
        });
    }

    $(document).on('ready', function(){
        $(".direction-toggle").on("click", function () {
            if($('html').hasClass('active')){
                $('html').removeClass('active')
                setDirection(1);
            }else {
                setDirection(0);
                $('html').addClass('active')
            }
        });

        if ($('html').attr('dir') === "rtl") {
            $(".direction-toggle").find('span').text('Toggle LTR')
        } else {
            $(".direction-toggle").find('span').text('Toggle RTL')
        }

        function setDirection(status) {
            if (status === 1) {
                $("html").attr('dir', 'ltr');
                $(".direction-toggle").find('span').text('Toggle RTL')
            } else {
                $("html").attr('dir', 'rtl');
                $(".direction-toggle").find('span').text('Toggle LTR')
            }
            $.get({
                    url: '{{ route('admin.business-settings.site_direction') }}',
                    dataType: 'json',
                    data: {
                        status: status,
                    },
                    success: function() {
                        alert(ok);
                    },

                });
            }
        });

    @php($fcm_credentials = \App\CentralLogics\Helpers::get_business_settings('fcm_credentials'))
    let firebaseConfig = {
        apiKey: "{{isset($fcm_credentials['apiKey']) ? $fcm_credentials['apiKey'] : ''}}",
        authDomain: "{{isset($fcm_credentials['authDomain']) ? $fcm_credentials['authDomain'] : ''}}",
        projectId: "{{isset($fcm_credentials['projectId']) ? $fcm_credentials['projectId'] : ''}}",
        storageBucket: "{{isset($fcm_credentials['storageBucket']) ? $fcm_credentials['storageBucket'] : ''}}",
        messagingSenderId: "{{isset($fcm_credentials['messagingSenderId']) ? $fcm_credentials['messagingSenderId'] : ''}}",
        appId: "{{isset($fcm_credentials['appId']) ? $fcm_credentials['appId'] : ''}}",
        measurementId: "{{isset($fcm_credentials['measurementId']) ? $fcm_credentials['measurementId'] : ''}}"
    };
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    function startFCM() {
        messaging
            .requestPermission()
            .then(function() {
                return messaging.getToken();
            })
            .then(function(token) {
                // console.log('FCM Token:', token);
                // Send the token to your backend to subscribe to topic
                subscribeTokenToBackend(token, 'admin_message');
            }).catch(function(error) {
            console.error('Error getting permission or token:', error);
        });
    }

    function subscribeTokenToBackend(token, topic) {
        fetch('{{url('/')}}/subscribeToTopic', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ token: token, topic: topic })
        }).then(response => {
            if (response.status < 200 || response.status >= 400) {
                return response.text().then(text => {
                    throw new Error(`Error subscribing to topic: ${response.status} - ${text}`);
                });
            }
            console.log(`Subscribed to "${topic}"`);
        }).catch(error => {
            console.error('Subscription error:', error);
        });
    }



    function conversationList() {
        $.ajax({
                url: "{{ route('admin.message.list') }}",
                success: function(data) {
                    $('#conversation-list').empty();
                    $("#conversation-list").append(data.html);
                    let user_id = getUrlParameter('user');
                $('.customer-list').removeClass('conv-active');
                $('#customer-' + user_id).addClass('conv-active');
                }
            })
    }

    function conversationView() {
        let conversation_id = getUrlParameter('conversation');
        let user_id = getUrlParameter('user');
        let url= '{{url('/')}}/admin/message/view/'+conversation_id+'/' + user_id;
        $.ajax({
            url: url,
            success: function(data) {
                $('#view-conversation').html(data.view);
            }
        })
    }



    function vendorConversationView() {
        let conversation_id = getUrlParameter('conversation');
        let user_id = getUrlParameter('user');
        let url= '{{url('/')}}/admin/store/message/'+conversation_id+'/' + user_id;
        $.ajax({
            url: url,
            success: function(data) {
                $('#vendor-view-conversation').html(data.view);
            }
        })
    }

    function dmConversationView() {
        let conversation_id = getUrlParameter('conversation');
        let user_id = getUrlParameter('user');
        let url= '{{url('/')}}/admin/users/delivery-man/message/'+conversation_id+'/' + user_id;
        $.ajax({
            url: url,
            success: function(data) {
                $('#dm-view-conversation').html(data.view);
            }
        })
    }

    let new_order_type='store_order';
    let new_module_id=null;
    let admin_zone_id=null;
    let admin_role_id=null;

    @php($order_notification_type = \App\CentralLogics\Helpers::get_business_settings('order_notification_type') ??'manual')
    messaging.onMessage(function(payload) {
        console.log(payload.data)
        if(payload.data.order_id && payload.data.type == "order_request"){
                @php($admin_order_notification = \App\CentralLogics\Helpers::get_business_settings('admin_order_notification') ?? 0)
                @if (\App\CentralLogics\Helpers::module_permission_check('order') && $admin_order_notification && $order_notification_type == 'firebase')
                new_order_type = payload.data.order_type
                new_module_id = payload.data.module_id
                admin_zone_id = '<?php echo auth()->guard('admin')->user()->zone_id ;?>';
                admin_role_id = '<?php echo auth()->guard('admin')->user()->role_id ;?>';
                if(new_order_type === 'trip'){
                    document.querySelector('.update_notification_text').textContent = "{{translate('messages.You have new trip, Check Please.')}}";
                }
                if(admin_role_id === '1'){
                    playAudio();
                    $('#popup-modal').appendTo("body").modal('show');
                }
                if((admin_role_id !== '1') && (admin_zone_id === payload.data.zone_id)){
                    playAudio();
                    $('#popup-modal').appendTo("body").modal('show');
                }
                @endif

        }else{
            if (window.location.href.includes('message/list?conversation')) {
                let conversation_id = getUrlParameter('conversation');
                let user_id = getUrlParameter('user');
                let url = '{{url('/')}}/admin/message/view/' + conversation_id + '/' + user_id;
                console.log(url);
                $.ajax({
                    url: url,
                    success: function (data) {
                        $('#view-conversation').html(data.view);
                    }
                })
            }
            toastr.success('New message arrived', {
                CloseButton: true,
                ProgressBar: true
            });
            if($('#conversation-list').scrollTop() === 0){
                conversationList();
            }
        }
    });

    @if(\App\CentralLogics\Helpers::module_permission_check('order') && $order_notification_type == 'manual')
        @php($admin_order_notification=\App\CentralLogics\Helpers::get_business_settings('admin_order_notification') ?? 0)
        @if($admin_order_notification)
            setInterval(function () {
                $.get({
                    url: '{{route('admin.get-store-data')}}',
                    dataType: 'json',
                    success: function (response) {
                        let data = response.data;
                        new_order_type = data.type;
                        new_module_id = data.module_id;
                        if(new_order_type === 'trip'){
                            document.querySelector('.update_notification_text').textContent = "{{translate('messages.You have new trip, Check Please.')}}";
                        }
                        if (data.new_order > 0) {
                            playAudio();
                            $('#popup-modal').appendTo("body").modal('show');
                        }else{
                            $('#popup-modal').appendTo("body").modal('hide');
                        }
                    },
                });
            }, 10000);
        @endif
    @endif

    $(document).on('click', '.check-order', function () {
        if(new_order_type === 'parcel')
        {
            location.href = '{{url('/')}}/admin/parcel/orders/all?module_id=' + new_module_id;
        }
        else if(new_order_type === 'trip')
        {
            location.href = '{{url('/')}}/admin/rental/trip?module_id=' + new_module_id;
        }
        else
        {
            location.href = '{{url('/')}}/admin/order/list/all?module_id=' + new_module_id;
        }
    });

    startFCM();
    conversationList();
    if(getUrlParameter('conversation')){
        conversationView();
        vendorConversationView();
        dmConversationView();
    }


    $(document).on('click', '.call-demo', function (e) {
            @if(getEnvMode() == 'demo')
                toastr.warning('{{ translate('Update option is disabled for demo!') }}', {
                    CloseButton: true,
                    ProgressBar: true
                });
                e.preventDefault();
            @endif
        });

    $('.request_alert').on('click', function (event) {
            let url = $(this).data('url');
            let message = $(this).data('message');
            request_alert(url, message)
        })

        function request_alert(url, message) {
            Swal.fire({
                title: '{{translate('messages.are_you_sure')}}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: '{{translate('messages.no')}}',
                confirmButtonText: '{{translate('messages.yes')}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    location.href = url;
                }
            })
        }
</script>
        <script src="{{asset('public/assets/admin/intltelinput/js/intlTelInput.min.js')}}"></script>

        <script>

    const inputs = document.querySelectorAll('input[type="tel"]');

    inputs.forEach(input => {
        window.intlTelInput(input, {
            initialCountry: "{{$countryCode}}",
            utilsScript: "{{ asset('public/assets/admin/intltelinput/js/utils.js') }}",
            autoInsertDialCode: true,
            nationalMode: false,
            formatOnDisplay: false,
        });
    });


  function keepNumbersAndPlus(inputString) {
    let regex = /[0-9+]/g;
    let filteredString = inputString.match(regex);
    let result = filteredString ? filteredString.join('') : '';
    return result;
}

$(document).on('keyup', 'input[type="tel"]', function () {
        let input = $(this).val();
        $(this).val(keepNumbersAndPlus(input));
        });


  //external configuration
    $("#generateSystemSelfToken").on("click", function () {
        generateRandomToken(64);
    });
    if(document.getElementById('copyButton')){

        document.getElementById('copyButton').addEventListener('click', function() {
            const input = document.getElementById('systemSelfToken');

            // Select the input field text
            input.select();
            input.setSelectionRange(0, 99999); // For mobile devices

            // Copy the text inside the input field to the clipboard
            navigator.clipboard.writeText(input.value).then(function() {
                toastr.success('Text copied to clipboard: ' + input.value);
            }).catch(function(error) {
                toastr.error('Failed to copy text: ', error);
            });
        });
    }

    function generateRandomToken(length) {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let token = '';
        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * characters.length);
            token += characters.charAt(randomIndex);
        }
        $('#systemSelfToken').val(token)
    }


            //search option
            $(document).ready(function () {
            $('#searchForm input[name="search"]').keyup(function () {
                var searchKeyword = $(this).val().trim();

                if (searchKeyword.length >= 1) {
                    $.ajax({
                        type: 'POST',
                        url: $('#searchForm').attr('action'),
                        data: {search: searchKeyword, _token: $('input[name="_token"]').val()},
                        success: function (response) {
                            if (response.length === 0) {
                                let htmlContent = '';

                                @if (!$current_module_type_for_search)
                                    htmlContent += '<div class="bg--13 d-inline-block fs-12 fw-500 mb-2 px-2 py-1 rounded text-italic">' + @json(translate('* To get module-specific results, please search within the module.')) + '</div>';
                                @endif

                                htmlContent += '<div class="fs-16 fw-500 mb-2">' + @json(translate('Search Result')) + '</div>' +
                                    '<div class="search-list h-300 d-flex flex-column gap-2 justify-content-center align-items-center fs-16">' +
                                        '<img width="30" class="h-auto" src="' + @json(asset('/public/assets/admin/img/modal/no-search-found.png')) + '" alt="">' + ' ' +
                                        @json(translate('No result found')) +
                                    '</div>';

                                $('#searchResults').html(htmlContent);

                            } else {
                                var resultHtml = '';
                                response.forEach(function (route) {
                                    var separator = route.fullRoute.includes('?') ? '&' : '?';
                                    var fullRouteWithKeyword = route.fullRoute + separator + 'keyword=' + encodeURIComponent(searchKeyword);
                                    var keywordRegex = searchKeyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                                        keywordRegex = new RegExp('(' + keywordRegex + ')', 'gi');
                                    var highlightedRouteName = route.routeName.replace(keywordRegex, '<mark>$1</mark>');
                                    var highlightedURI = route.URI.replace(keywordRegex, '<mark>$1</mark>');
                                    resultHtml += '<a href="' + fullRouteWithKeyword + '" class="search-list-item d-flex flex-column" data-route-name="' + route.routeName + '" data-route-uri="' + route.URI + '" data-route-full-url="' + route.fullRoute + '" aria-current="true">';
                                    resultHtml += '<h5>' + highlightedRouteName + '</h5>';
                                    resultHtml += '<p class="text-muted fs-12 mb-0">' + highlightedURI + '</p>';
                                    resultHtml += '</a>';
                                });

                                let htmlContent = '';

                                @if (!$current_module_type_for_search)
                                    htmlContent += '<div class="bg--13 d-inline-block fs-12 fw-500 mb-2 px-2 py-1 rounded text-italic">' + @json(translate('* To get module-specific results, please search within the module.')) + '</div>';
                                @endif
                                htmlContent +='<div class="fs-16 fw-500 mb-2">' + @json(translate('Search Result')) + '</div>' + '<div class="search-list d-flex flex-column">' + resultHtml + '</div>';

                                $('#searchResults').html(htmlContent);
                                $('.search-list-item').click(function () {
                                    var routeName = $(this).data('route-name');
                                    var routeUri = $(this).data('route-uri');
                                    var routeFullUrl = $(this).data('route-full-url');

                                    $.ajax({
                                        type: 'POST',
                                        url: '{{ route('admin.store.clicked.route') }}',
                                        data: {
                                            routeName: routeName,
                                            routeUri: routeUri,
                                            routeFullUrl: routeFullUrl,
                                            searchKeyword: searchKeyword,
                                            moduleId: '{{ config('module.current_module_id') ?? null }}',
                                            _token: $('input[name="_token"]').val()
                                        },
                                        success: function (response) {

                                        },
                                        error: function (xhr, status, error) {
                                            console.error(xhr.responseText);
                                        }
                                    });
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
                else {
                    getRecentSearch()
                }
            });
        });

        document.addEventListener('keydown', function(event) {
            if (event.ctrlKey && event.key === 'k') {
                event.preventDefault();
                document.getElementById('modalOpener').click();
            }
        });

        $(document).ready(function () {
            $("#staticBackdrop").on("shown.bs.modal", function () {
                getRecentSearch()
            });
        });


        function getRecentSearch(){
            $(this).find("#searchForm input[type=search]").val('');
                $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('Loading recent searches')}}...</div>');
                $(this).find("#searchForm input[type=search]").focus();

                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.recent.search') }}',
                    success: function (response) {
                        if (response.length === 0) {
                            $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('It appears that you have not yet searched.')}}.</div>');
                        } else {
                            var resultHtml = '';
                            response.forEach(function (route) {
                                resultHtml += '<a href="' + route.route_full_url + '" class="search-list-item d-flex flex-column" data-route-name="' + route.route_name + '" data-route-uri="' + route.route_uri + '" data-route-full-url="' + route.route_full_url + '" aria-current="true">';
                                resultHtml += '<h5>' + route.route_name + '</h5>';
                                resultHtml += '<p class="text-muted fs-12  mb-0">' + route.route_uri + '</p>';
                                resultHtml += '</a>';
                            });
                            $('#searchResults').html('<div class="recent-search fs-16 fw-500 animate">' +
                                @json(translate('Recent Search')) + '<div class="search-list d-flex flex-column mt-2">' + resultHtml + '</div></div>');

                            $('.search-list-item').click(function () {
                                var routeName = $(this).data('route-name');
                                var routeUri = $(this).data('route-uri');
                                var routeFullUrl = $(this).data('route-full-url');
                                var searchKeyword = $('input[type=search]').val().trim();

                                $.ajax({
                                    type: 'POST',
                                    url: '{{ route('admin.store.clicked.route') }}',
                                    data: {
                                        routeName: routeName,
                                        routeUri: routeUri,
                                        routeFullUrl: routeFullUrl,
                                        searchKeyword: searchKeyword,
                                        _token: $('input[name="_token"]').val()
                                    },
                                    success: function (response) {
                                        console.log(response.message);
                                    },
                                    error: function (xhr, status, error) {
                                        console.error(xhr.responseText);
                                    }
                                });
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        $('#searchResults').html('<div class="text-center text-muted py-5">{{translate('Error loading recent searches')}}.</div>');
                    }
                });
        }





        $("#staticBackdrop").on("hidden.bs.modal", function () {
            $('#searchResults').empty();
        });

        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('search', function() {
            if (!this.value.trim()) {
                $('#searchResults').html('<div class="text-center text-muted py-5"></div>');
            }
        });

        $('#searchForm').submit(function (event) {
            event.preventDefault();
        });



</script>

<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('public/assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>

    <!-- Landing Tab Menu -->
    <script>
        const container = document.querySelector('.tabs-inner');
        const btnPrevWrap = document.querySelector('.button-prev');
        const btnNextWrap = document.querySelector('.button-next');
        const item = document.querySelector('.tabs-slide_items');

        document.querySelectorAll('.tabs-slide_items').forEach(el => {
            el.style.flex = '0 0 auto';
        });
        function updateArrows() {
            if (!container || !btnPrevWrap || !btnNextWrap) return;

            const hasOverflow = container.scrollWidth > container.clientWidth;
            if (!hasOverflow) {
                btnPrevWrap.style.display = 'none';
                btnNextWrap.style.display = 'none';
                return;
            }
            const scrollLeft = container.scrollLeft;
            const maxScroll = container.scrollWidth - container.clientWidth;

            if (scrollLeft > 2) {
                btnPrevWrap.style.display = 'flex';
            } else {
                btnPrevWrap.style.display = 'none';
            }

            if (scrollLeft < maxScroll - 2) {
                btnNextWrap.style.display = 'flex';
            } else {
                btnNextWrap.style.display = 'none';
            }
        }
        document.querySelector('.btn-click-prev')?.addEventListener('click', () => {
            const itemWidth = item?.offsetWidth || 100;
            container.scrollBy({ left: -itemWidth, behavior: 'smooth' });
        });
        document.querySelector('.btn-click-next')?.addEventListener('click', () => {
            const itemWidth = item?.offsetWidth || 100;
            container.scrollBy({ left: itemWidth, behavior: 'smooth' });
        });

        container.addEventListener('scroll', updateArrows);
        ['load', 'resize'].forEach(evt => window.addEventListener(evt, updateArrows));
        new MutationObserver(updateArrows).observe(container, { childList: true, subtree: true });
        new ResizeObserver(updateArrows).observe(container);

        // Initial update
        updateArrows();

    </script>
</body>
</html>
