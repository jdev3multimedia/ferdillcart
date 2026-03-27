@extends('layouts.admin.app')

@section('title',translate('messages.system_module_setup'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/sms.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.sms_gateway_setup')}}
                </span>
            </h1>
            @include('admin-views.business-settings.partials.third-party-links')
        </div>
        <!-- End Page Header -->

        <div class="row g-3">

            @if ($published_status == 1)
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body  d-flex flex-wrap  justify-content-around">
                            <h4  class="w-50 flex-grow-1 module-warning-text">
                                <i class="tio-info-outined"></i>
                                {{ translate('Your_current_sms_settings_are_disabled,_because_you_have_enabled_sms_gateway_addon,_To_visit_your_currently_active_sms_gateway_settings_please_follow_the_link.') }}
                                </h4>
                                <div>
                                    <a href="{{!empty($payment_url) ? $payment_url : ''}}" class="btn btn-outline-primary"> <i class="tio-settings"></i> {{translate('settings')}}</a>
                                </div>
                        </div>
                    </div>
                </div>
            @endif
            @php($is_published = $published_status == 1 ? 'inactive' : '')
            @foreach($data_values as $gateway)
            <div class="col-md-6 digital_payment_methods  {{ $is_published }} mb-3" >
                <div class="card">
                    <div class="card-header">
                        <h4 class="page-title">{{translate($gateway->key_name)}}</h4>
                    </div>
                    <div class="card-body p-30">
                        <form action="{{route('admin.business-settings.third-party.sms-module-update',[$gateway->key_name])}}" method="POST"
                                id="{{$gateway->key_name}}-form" enctype="multipart/form-data">
                            @csrf
                            @method('post')
                        <div class="discount-type">
                                <div class="d-flex align-items-center gap-4 gap-xl-5 mb-30">
                                    <div class="custom-radio">
                                        <input class="{{ \App\Models\BusinessSetting::where('key', 'firebase_otp_verification')->first()?->value == 1 ? 'firebase-check' : '' }} "  type="radio" id="{{$gateway->key_name}}-active"
                                                name="status"
                                                value="1" {{$data_values->where('key_name',$gateway->key_name)->first()->live_values['status']?'checked':''}}>
                                        <label
                                            for="{{$gateway->key_name}}-active"> {{ translate('messages.Active') }}</label>
                                    </div>
                                    <div class="custom-radio">
                                        <input type="radio" id="{{$gateway->key_name}}-inactive"
                                                name="status"
                                                value="0" {{$data_values->where('key_name',$gateway->key_name)->first()->live_values['status']?'':'checked'}}>
                                        <label
                                            for="{{$gateway->key_name}}-inactive"> {{ translate('messages.Inactive') }}</label>
                                    </div>
                                </div>

                                <input name="gateway" value="{{$gateway->key_name}}" class="d-none">
                                <input name="mode" value="live" class="d-none">

                                @php($skip=['gateway','mode','status'])
                                @foreach($data_values->where('key_name',$gateway->key_name)->first()->live_values as $key=>$value)
                                    @if(!in_array($key,$skip))
                                        <div class="form-floating mb-30 mt-30 text-capitalize">
                                            <label for="{{$key}}" class="form-label">{{translate($key)}}  {{ $gateway->key_name == 'alphanet_sms' &&  $key == 'sender_id'? '('. translate('messages.Optional') .')' : '*'}}  </label>
                                            <input id="{{$key}}" type="text" class="form-control"
                                                   name="{{$key}}"
                                                   placeholder=" {{ $key == 'otp_template' ?  translate('Your_Security_Pin_is'). ' #OTP#' : translate($key) .' *'   }}    "
                                                   value="{{env('APP_ENV')=='demo'?'':$value}}">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn--primary demo_check">
                                {{ translate('messages.Update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

            </div>
        </div>


        <div class="modal fade" id="firebase-modal">
            <div class="modal-dialog status-warning-modal">
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
                                    <img src="{{ asset('/public/assets/admin/img/modal/order-delivery-verification-on.png') }}" class="mb-20">
                                    <h5 class="modal-title" >{{ translate('messages.Note') }} </h5>
                                </div>
                                <div class="text-center">
                                    <p class="text--danger" >
                                        {{ translate('Currently_Your_FireBase_OTP_System_is_Active.Users_won’t_get_any_OTP_from_this_SMS_Gateway' ) }}
                                    </p>
                                </div>
                            </div>
                            <div class="btn--container justify-content-center">
                                <button type="button"  data-dismiss="modal" class="btn btn--primary min-w-120 confirm-Status-Toggle" data-dismiss="modal" >{{translate('OK')}}</button>
                                {{-- <button type="button" class="btn btn--cancel min-w-120" data-dismiss="modal">
                                    {{translate("Cancel")}}
                                </button> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>











@endsection
@push('script_2')

    <script>

    $(document).on('click', '.firebase-check', function(event) {
        // event.preventDefault();
        $('#firebase-modal').modal('show');
    });

    </script>
@endpush



