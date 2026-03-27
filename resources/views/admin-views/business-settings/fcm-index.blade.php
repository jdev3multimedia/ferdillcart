@extends('layouts.admin.app')

@section('title',translate('FCM Settings'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/firebase.png')}}" class="w--26" alt="">
                </span>
                <span>{{translate('messages.firebase_push_notification_setup')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <?php
        $mod_type = 'grocery';
        if(request('module_type')){
            $mod_type = request('module_type');
        }
        ?>
        <div class="card">
            <div class="card-header card-header-shadow pb-0">
                <div class="d-flex flex-wrap justify-content-between w-100 row-gap-1">
                    <ul class="nav nav-tabs nav--tabs border-0 gap-2">
                        <li class="nav-item mr-2 mr-md-4">
                            <a href="{{ route('admin.business-settings.fcm-index') }}" class="nav-link pb-2 px-0 pb-sm-3 active" data-slide="1">
                                <img src="{{asset('/public/assets/admin/img/notify.png')}}" alt="">
                                <span>{{translate('Push Notification')}}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.business-settings.fcm-config') }}" class="nav-link pb-2 px-0 pb-sm-3" data-slide="2">
                                <img src="{{asset('/public/assets/admin/img/firebase2.png')}}" alt="">
                                <span>{{translate('Firebase Configuration')}}</span>
                            </a>
                        </li>
                    </ul>
                    <div class="py-1">
                        <div class="tab--content">
                            <div class="item show text--primary-2 d-flex flex-wrap align-items-center" type="button" data-toggle="modal" data-target="#push-notify-modal">
                                <strong class="mr-2">{{translate('Read Documentation')}}</strong>
                                <div class="blinkings">
                                    <i class="tio-info-outined"></i>
                                </div>
                            </div>
                            <div class="item text--primary-2 d-flex flex-wrap align-items-center" type="button" data-toggle="modal" data-target="#firebase-modal">
                                <strong class="mr-2">{{translate('Where to get this information')}}</strong>
                                <div class="blinkings">
                                    <i class="tio-info-outined"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="push-notify">
                        @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                        @php($language = $language->value ?? null)
                        @php($defaultLang = 'en')
                        <div class="row justify-content-between">
                            <div class="col-sm-auto mb-5">
                                @if($language)
                                    @php($defaultLang = json_decode($language)[0])
                                    <ul class="nav nav-tabs border-0">
                                        @foreach(json_decode($language) as $lang)
                                            <li class="nav-item">
                                                <a class="nav-link lang_link {{$lang == $defaultLang? 'active':''}}" href="#" id="{{$lang}}-link">{{\App\CentralLogics\Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <div class="col-sm-auto mb-5">
                                <select name="module_type" class="form-control js-select2-custom set-filter"
                                data-url="{{url()->full()}}"
                                data-filter="module_type"
                                title="{{translate('messages.select_modules')}}">
                                    @foreach (config('module.module_type') as $module)
                                        <option
                                            value="{{$module}}" {{$mod_type == $module?'selected':''}}>
                                            {{ucfirst(translate($module))}}
                                        </option>
                                    @endforeach
                                </select>
                                <small>{{translate('*Select Module Here')}}</small>
                            </div>
                        </div>
                        <form action="{{route('admin.business-settings.update-fcm-messages')}}" method="post"
                                enctype="multipart/form-data">
                            @csrf

                            @if($language)
                            @php($defaultLang = json_decode($language)[0])
                            @foreach(json_decode($language) as $lang_key => $lang)

                                <div class="{{$lang != $defaultLang ? 'd-none':''}} lang_form" id="{{$lang}}-form">
                                    <div class="row">
                                        @php($opm=\App\Models\NotificationMessage::with('translations')->where('module_type',$mod_type)->where('key','order_pending_message')->first())
                                        @php($data=$opm?$opm:null)
                                        <?php
                                                if(isset($opm->translations) && count($opm->translations)){
                                                    $translate = [];
                                                    foreach($opm->translations as $t)
                                                    {
                                                        if($t->locale == $lang && $t->key=='order_pending_message'){
                                                            $translate[$lang]['message'] = $t->value;
                                                        }
                                                    }

                                                }
                                                ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                                    <span class="d-block form-label">
                                                        {{translate('messages.order_pending_message')}} ({{strtoupper($lang)}})
                                                    </span>
                                                @if ($lang == 'en')
                                                        <label class="switch--custom-label toggle-switch d-flex align-items-center"
                                                            for="pending_status">
                                                            <input type="checkbox"
                                                                   data-id="pending_status"
                                                                   data-type="toggle"
                                                                   data-image-on="{{ asset('/public/assets/admin/img/modal/pending-order-on.png') }}"
                                                                   data-image-off="{{ asset('/public/assets/admin/img/modal/pending-order-off.png') }}"
                                                                   data-title-on="{{ translate('By Turning ON Order') }} <strong>{{ translate('pending Message') }}</strong>"
                                                                   data-title-off="{{ translate('By Turning OFF Order') }} <strong>{{ translate('pending Message') }}</strong>"
                                                                   data-text-on="<p>{{ translate('User will get a clear message to know that the order is pending.') }}</p>"
                                                                   data-text-off="<p>{{ translate('User cannot get a clear message to know that the order is pending or not.') }}</p>"
                                                                   class="status toggle-switch-input add-required-attribute  dynamic-checkbox-toggle"
                                                                   name="pending_status"

                                                                   data-textarea-name="pending_messages"
                                                                value="1" id="pending_status" {{$data?($data['status']==1?'checked':''):''}}>
                                                            <span class="toggle-switch-label">
                                                                <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>

                                                @endif
                                                </div>
                                                <textarea name="pending_message[]" placeholder="{{translate('Write your message')}}" class="form-control pending_messages"
                                                @if ($lang == 'en')
                                                {{$data?($data['status']==1?'required':''):''}}
                                                @endif
                                                >{!! (isset($translate) && isset($translate[$lang]))?$translate[$lang]['message']:($data?$data['message']:'') !!}</textarea>
                                            </div>
                                        </div>

                                        @php($ocm=\App\Models\NotificationMessage::with('translations')->where('module_type',$mod_type)->where('key','order_confirmation_msg')->first())
                                        @php($data=$ocm?$ocm:'')
                                        <?php
                                        if(isset($ocm->translations)&&count($ocm->translations)){
                                                $translate_2 = [];
                                                foreach($ocm->translations as $t)
                                                {
                                                    if($t->locale == $lang && $t->key=='order_confirmation_msg'){
                                                        $translate_2[$lang]['message'] = $t->value;
                                                    }
                                                }

                                            }

                                        ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                                    <span class="d-block form-label">
                                                        {{translate('messages.order_confirmation_message')}}
                                                    </span>
                                                    @if ($lang == 'en')
                                                        <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                            for="confirm_status">
                                                            <input type="checkbox"
                                                                   data-id="confirm_status"
                                                                   data-type="toggle"
                                                                   data-image-on="{{ asset('/public/assets/admin/img/modal/pending-order-on.png') }}"
                                                                   data-image-off="{{ asset('/public/assets/admin/img/modal/pending-order-off.png') }}"
                                                                   data-title-on="{{ translate('By Turning ON Order') }} <strong>{{ translate('confirmation Message') }}</strong>"
                                                                   data-title-off="{{ translate('By Turning OFF Order') }} <strong>{{ translate('confirmation Message') }}</strong>"
                                                                   data-text-on="<p>{{ translate('User will get a clear message to know that the order is confirmed.') }}</p>"
                                                                   data-text-off="<p>{{ translate('User cannot get a clear message to know that the order is confirmed or not.') }}</p>"
                                                                   class="status toggle-switch-input add-required-attribute  dynamic-checkbox-toggle"
                                                                   name="confirm_status"
                                                                   data-textarea-name="confirm_message"

                                                                value="1" id="confirm_status" {{$data?($data['status']==1?'checked':''):''}}>
                                                            <span class="toggle-switch-label">
                                                                <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>

                                                    @endif
                                                </div>
                                                <textarea name="confirm_message[]"  placeholder="{{translate('Write your message')}}" class="form-control confirm_message"
                                                @if ($lang == 'en')
                                                {{$data?($data['status']==1?'required':''):''}}
                                                @endif >{!! (isset($translate_2) && isset($translate_2[$lang]))?$translate_2[$lang]['message']:($data?$data['message']:'') !!}</textarea>
                                            </div>
                                        </div>
                                        @if ($mod_type != 'parcel')


                                        @php($oprm=\App\Models\NotificationMessage::with('translations')->where('module_type',$mod_type)->where('key','order_processing_message')->first())

                                        @php($data=$oprm?$oprm:null)

                                        <?php
                                        if(isset($oprm->translations) && count($oprm->translations)){
                                                $translate_3 = [];
                                                foreach($oprm->translations as $t)
                                                {
                                                    if($t->locale == $lang && $t->key=='order_processing_message'){
                                                        $translate_3[$lang]['message'] = $t->value;
                                                    }
                                                }

                                            }

                                        ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                                    <span class="d-block form-label">
                                                        {{translate('messages.order_processing_message')}}
                                                    </span>
                                                    @if ($lang == 'en')
                                                        <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0" for="processing_status">
                                                            <input type="checkbox"
                                                                   data-id="processing_status"
                                                                   data-type="toggle"
                                                                   data-image-on="{{ asset('/public/assets/admin/img/modal/pending-order-on.png') }}"
                                                                   data-image-off="{{ asset('/public/assets/admin/img/modal/pending-order-off.png') }}"
                                                                   data-title-on="{{ translate('By Turning ON Order') }} <strong>{{ translate('processing Message') }}</strong>"
                                                                   data-title-off="{{ translate('By Turning OFF Order') }} <strong>{{ translate('processing Message') }}</strong>"
                                                                   data-text-on="<p>{{ translate('User will get a clear message to know that the order is processing.') }}</p>"
                                                                   data-text-off="<p>{{ translate('User cannot get a clear message to know that the order is processing or not.') }}</p>"
                                                                   class="status toggle-switch-input add-required-attribute  dynamic-checkbox-toggle"
                                                                   name="processing_status"
                                                                   data-textarea-name="processing_message"
                                                                   value="1" id="processing_status" {{$data?($data['status']==1?'checked':''):''}}>
                                                            <span class="toggle-switch-label">
                                                                <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>

                                                    @endif
                                                </div>
                                                <textarea name="processing_message[]"  placeholder="{{translate('Write your message')}}" class="form-control processing_message"                                           @if ($lang == 'en')
                                                {{$data?($data['status']==1?'required':''):''}}
                                                @endif
                                                >{!! (isset($translate_3) && isset($translate_3[$lang]))?$translate_3[$lang]['message']:($data?$data['message']:'') !!}</textarea>
                                            </div>
                                        </div>

                                        @php($dbs=\App\Models\NotificationMessage::with('translations')->where('module_type',$mod_type)->where('key','order_handover_message')->first())
                                        @php($data=$dbs?$dbs:'')
                                        <?php
                                        if(isset($dbs->translations) && count($dbs->translations)){
                                                $translate_4 = [];
                                                foreach($dbs->translations as $t)
                                                {
                                                    if($t->locale == $lang && $t->key=='order_handover_message'){
                                                        $translate_4[$lang]['message'] = $t->value;
                                                    }
                                                }

                                            }

                                        ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                                    <span class="d-block form-label">
                                                        {{translate('messages.order_Handover_message')}}
                                                    </span>
                                                    @if ($lang == 'en')
                                                        <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                                for="order_handover_message_status">
                                                            <input type="checkbox"
                                                                   data-id="order_handover_message_status"
                                                                   data-type="toggle"
                                                                   data-image-on="{{ asset('/public/assets/admin/img/modal/pending-order-on.png') }}"
                                                                   data-image-off="{{ asset('/public/assets/admin/img/modal/pending-order-off.png') }}"
                                                                   data-title-on="{{ translate('By Turning ON Order') }} <strong>{{ translate('Order Handover Message') }}</strong>"
                                                                   data-title-off="{{ translate('By Turning OFF Order') }} <strong>{{ translate('Order Handover Message') }}</strong>"
                                                                   data-text-on="<p>{{ translate('User will get a clear message to know that the order is handovered.') }}</p>"
                                                                   data-text-off="<p>{{ translate('User cannot get a clear message to know that the order is handovered or not.') }}</p>"
                                                                   class="status toggle-switch-input add-required-attribute  dynamic-checkbox-toggle"

                                                                   name="order_handover_message_status"
                                                                   data-textarea-name="order_handover_message"
                                                                   value="1"
                                                                    id="order_handover_message_status" {{$data?($data['status']==1?'checked':''):''}}>
                                                            <span class="toggle-switch-label">
                                                                <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>

                                                    @endif
                                                </div>
                                                <textarea name="order_handover_message[]"  placeholder="{{translate('Write your message')}}" class="form-control order_handover_message"                                           @if ($lang == 'en')
                                                {{$data?($data['status']==1?'required':''):''}}
                                                @endif
                                                >{!! (isset($translate_4) && isset($translate_4[$lang]))?$translate_4[$lang]['message']:($data?$data['message']:'') !!}</textarea>
                                            </div>
                                        </div>
                                        @endif


                                        @php($ofdm=\App\Models\NotificationMessage::with('translations')->where('module_type',$mod_type)->where('key','out_for_delivery_message')->first())
                                        @php($data=$ofdm?$ofdm:'')
                                        <?php
                                        if(isset($ofdm->translations) && count($ofdm->translations)){
                                                $translate_5 = [];
                                                foreach($ofdm->translations as $t)
                                                {
                                                    if($t->locale == $lang && $t->key=='out_for_delivery_message'){
                                                        $translate_5[$lang]['message'] = $t->value;
                                                    }
                                                }

                                            }

                                        ?>

                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                                    <span class="d-block form-label">
                                                        {{translate('messages.order_out_for_delivery_message')}}
                                                    </span>
                                                    @if ($lang == 'en')
                                                        <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                                for="out_for_delivery">
                                                            <input type="checkbox"
                                                                   data-id="out_for_delivery"
                                                                   data-type="toggle"
                                                                   data-image-on="{{ asset('/public/assets/admin/img/modal/pending-order-on.png') }}"
                                                                   data-image-off="{{ asset('/public/assets/admin/img/modal/pending-order-off.png') }}"
                                                                   data-title-on="{{ translate('By Turning ON Order') }} <strong>{{ translate('Out For Delivery Message') }}</strong>"
                                                                   data-title-off="{{ translate('By Turning OFF Order') }} <strong>{{ translate('Out For Delivery Message') }}</strong>"
                                                                   data-text-on="<p>{{ translate('User will get a clear message to know that the order is out for delivery.') }}</p>"
                                                                   data-text-off="<p>{{ translate('User cannot get a clear message to know that the order is out for delivery or not.') }}</p>"
                                                                   class="status toggle-switch-input add-required-attribute  dynamic-checkbox-toggle"
                                                                   name="out_for_delivery_status"
                                                                   data-textarea-name="out_for_delivery_message"
                                                                    value="1" id="out_for_delivery" {{$data?($data['status']==1?'checked':''):''}}>
                                                            <span class="toggle-switch-label">
                                                                <span class="toggle-switch-indicator"></span>
                                                                </span>
                                                        </label>
                                                    @endif
                                                </div>
                                                <textarea name="out_for_delivery_message[]"  placeholder="{{translate('Write your message')}}" class="form-control out_for_delivery_message"                                           @if ($lang == 'en')
                                                {{$data?($data['status']==1?'required':''):''}}
                                                @endif
                                                >{!! (isset($translate_5) && isset($translate_5[$lang]))?$translate_5[$lang]['message']:($data?$data['message']:'') !!}</textarea>
                                            </div>
                                        </div>

                                        @php($odm=\App\Models\NotificationMessage::with('translations')->where('module_type',$mod_type)->where('key','order_delivered_message')->first())
                                        @php($data=$odm?$odm:'')
                                        <?php
                                        if(isset($odm->translations)&&count($odm->translations)){
                                                $translate_6 = [];
                                                foreach($odm->translations as $t)
                                                {
                                                    if($t->locale == $lang && $t->key=='order_delivered_message'){
                                                        $translate_6[$lang]['message'] = $t->value;
                                                    }
                                                }

                                            }

                                        ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                                    <span class="d-block form-label">
                                                        {{translate('messages.order_delivered_message')}}
                                                    </span>
                                                    @if ($lang == 'en')
                                                        <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                                for="delivered_status">
                                                            <input type="checkbox"
                                                                   data-id="delivered_status"
                                                                   data-type="toggle"
                                                                   data-image-on="{{ asset('/public/assets/admin/img/modal/pending-order-on.png') }}"
                                                                   data-image-off="{{ asset('/public/assets/admin/img/modal/pending-order-off.png') }}"
                                                                   data-title-on="{{ translate('By Turning ON Order') }} <strong>{{ translate('delivered Message') }}</strong>"
                                                                   data-title-off="{{ translate('By Turning OFF Order') }} <strong>{{ translate('delivered Message') }}</strong>"
                                                                   data-text-on="<p>{{ translate('User will get a clear message to know that the order is delivered.') }}</p>"
                                                                   data-text-off="<p>{{ translate('User cannot get a clear message to know that the order is delivered or not.') }}</p>"
                                                                   class="status toggle-switch-input add-required-attribute  dynamic-checkbox-toggle"
                                                                   name="delivered_status"
                                                                   data-textarea-name="delivered_message"
                                                                    value="1" id="delivered_status" {{$data?($data['status']==1?'checked':''):''}}>
                                                            <span class="toggle-switch-label">
                                                                <span class="toggle-switch-indicator"></span>
                                                                </span>
                                                        </label>

                                                    @endif
                                                </div>
                                                <textarea name="delivered_message[]"  placeholder="{{translate('Write your message')}}" class="form-control delivered_message"                                           @if ($lang == 'en')
                                                {{$data?($data['status']==1?'required':''):''}}
                                                @endif
                                                >{!! (isset($translate_6) && isset($translate_6[$lang]))?$translate_6[$lang]['message']:($data?$data['message']:'') !!}</textarea>
                                            </div>
                                        </div>

                                        @php($dba=\App\Models\NotificationMessage::with('translations')->where('module_type',$mod_type)->where('key','delivery_boy_assign_message')->first())
                                        @php($data=$dba?$dba:'')
                                        <?php
                                        if(isset($dba->translations) && count($dba->translations)){
                                                $translate_7 = [];
                                                foreach($dba->translations as $t)
                                                {
                                                    if($t->locale == $lang && $t->key=='delivery_boy_assign_message'){
                                                        $translate_7[$lang]['message'] = $t->value;
                                                    }
                                                }

                                            }

                                        ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                                    <span class="d-block form-label">
                                                        {{translate('messages.deliveryman_assign_message')}}
                                                    </span>
                                                    @if ($lang == 'en')
                                                        <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                            for="delivery_boy_assign">
                                                            <input type="checkbox"
                                                                   data-id="delivery_boy_assign"
                                                                   data-type="toggle"
                                                                   data-image-on="{{ asset('/public/assets/admin/img/modal/pending-order-on.png') }}"
                                                                   data-image-off="{{ asset('/public/assets/admin/img/modal/pending-order-off.png') }}"
                                                                   data-title-on="{{ translate('By Turning ON Order') }} <strong>{{ translate('Delivery Man Assigned Message') }}</strong>"
                                                                   data-title-off="{{ translate('By Turning OFF Order') }} <strong>{{ translate('Delivery Man Assigned Message') }}</strong>"
                                                                   data-text-on="<p>{{ translate('User will get a clear message to know that the order is assigned to a delivery man.') }}</p>"
                                                                   data-text-off="<p>{{ translate('User cannot get a clear message to know that the order is assigned to a delivery man or not.') }}</p>"
                                                                   class="status toggle-switch-input add-required-attribute  dynamic-checkbox-toggle"
                                                                   data-textarea-name="delivery_boy_assign_message"
                                                                   name="delivery_boy_assign_status"
                                                                value="1"
                                                                id="delivery_boy_assign" {{$data?($data['status']==1?'checked':''):''}}>
                                                            <span class="toggle-switch-label">
                                                                <span class="toggle-switch-indicator"></span>
                                                            </span>
                                                        </label>

                                                    @endif
                                                </div>
                                                <textarea name="delivery_boy_assign_message[]"  placeholder="{{translate('Write your message')}}" class="form-control delivery_boy_assign_message"                                           @if ($lang == 'en')
                                                {{$data?($data['status']==1?'required':''):''}}
                                                @endif
                                                >{!! (isset($translate_7) && isset($translate_7[$lang]))?$translate_7[$lang]['message']:($data?$data['message']:'') !!}</textarea>
                                            </div>
                                        </div>

                                        @php($dbc=\App\Models\NotificationMessage::with('translations')->where('module_type',$mod_type)->where('key','delivery_boy_delivered_message')->first())

                                        @php($data=$dbc?$dbc:'')
                                        <?php
                                        if(isset($dbc->translations) && count($dbc->translations)){
                                                $translate_8 = [];
                                                foreach($dbc->translations as $t)
                                                {
                                                    if($t->locale == $lang && $t->key=='delivery_boy_delivered_message'){
                                                        $translate_8[$lang]['message'] = $t->value;
                                                    }
                                                }

                                            }

                                        ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                                    <span class="d-block form-label">
                                                        {{translate('messages.deliveryman_delivered_message')}}
                                                    </span>
                                                    @if ($lang == 'en')
                                                        <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                                for="delivery_boy_delivered">
                                                            <input type="checkbox"
                                                                   data-id="delivery_boy_delivered"
                                                                   data-type="toggle"
                                                                   data-image-on="{{ asset('/public/assets/admin/img/modal/pending-order-on.png') }}"
                                                                   data-image-off="{{ asset('/public/assets/admin/img/modal/pending-order-off.png') }}"
                                                                   data-title-on="{{ translate('By Turning ON Order') }} <strong>{{ translate('Delivery Man Delivered Message') }}</strong>"
                                                                   data-title-off="{{ translate('By Turning OFF Order') }} <strong>{{ translate('Delivery Man Delivered Message') }}</strong>"
                                                                   data-text-on="<p>{{ translate('User will get a clear message to know that the order is delivered by a delivery man.') }}</p>"
                                                                   data-text-off="<p>{{ translate('User cannot get a clear message to know that the order is delivered by a delivery man or not.') }}</p>"
                                                                   class="status toggle-switch-input add-required-attribute  dynamic-checkbox-toggle"
                                                                   name="delivery_boy_delivered_status"
                                                                   data-textarea-name="delivery_boy_delivered_message"
                                                                    value="1"
                                                                    id="delivery_boy_delivered" {{$data?($data['status']==1?'checked':''):''}}>
                                                            <span class="toggle-switch-label">
                                                                <span class="toggle-switch-indicator"></span>
                                                                </span>
                                                        </label>

                                                    @endif
                                                </div>

                                                <textarea name="delivery_boy_delivered_message[]"  placeholder="{{translate('Write your message')}}" class="form-control delivery_boy_delivered_message"                                           @if ($lang == 'en')
                                                {{$data?($data['status']==1?'required':''):''}}
                                                @endif
                                                >{!! (isset($translate_8) && isset($translate_8[$lang]))?$translate_8[$lang]['message']:($data?$data['message']:'') !!}</textarea>
                                            </div>
                                        </div>

                                        @php($ocm=\App\Models\NotificationMessage::with('translations')->where('module_type',$mod_type)->where('key','order_cancled_message')->first())
                                        @php($data=$ocm?$ocm:'')
                                        <?php
                                        if(isset($ocm->translations) && count($ocm->translations)){

                                                $translate_9 = [];
                                                foreach($ocm->translations as $t)
                                                {
                                                    if($t->locale == $lang && $t->key=='order_cancled_message'){
                                                        $translate_9[$lang]['message'] = $t->value;
                                                    }
                                                }

                                            }

                                        ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                                    <span class="d-block form-label">
                                                        {{translate('messages.order_canceled_message')}}
                                                    </span>
                                                    @if ($lang == 'en')
                                                        <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                                for="order_cancled_message">
                                                            <input type="checkbox"
                                                                   name="order_cancled_message_status"
                                                                   data-id="order_cancled_message"
                                                                   data-type="toggle"
                                                                   data-image-on="{{ asset('/public/assets/admin/img/modal/pending-order-on.png') }}"
                                                                   data-image-off="{{ asset('/public/assets/admin/img/modal/pending-order-off.png') }}"
                                                                   data-title-on="{{ translate('By Turning ON Order') }} <strong>{{ translate('canceled Message') }}</strong>"
                                                                   data-title-off="{{ translate('By Turning OFF Order') }} <strong>{{ translate('canceled Message') }}</strong>"
                                                                   data-text-on="<p>{{ translate('User will get a clear message to know that the order is canceled.') }}</p>"
                                                                   data-text-off="<p>{{ translate('User cannot get a clear message to know that the order is canceled or not.') }}</p>"
                                                                   class="status toggle-switch-input add-required-attribute  dynamic-checkbox-toggle"
                                                                   data-textarea-name="order_cancled_message"
                                                                    value="1"
                                                                    id="order_cancled_message" {{$data?($data['status']==1?'checked':''):''}}>
                                                            <span class="toggle-switch-label">
                                                                <span class="toggle-switch-indicator"></span>
                                                                </span>
                                                        </label>

                                                    @endif
                                                </div>

                                                <textarea name="order_cancled_message[]"  placeholder="{{translate('Write your message')}}" class="form-control order_cancled_message"                                           @if ($lang == 'en')
                                                {{$data?($data['status']==1?'required':''):''}}
                                                @endif
                                                >{!! (isset($translate_9) && isset($translate_9[$lang]))?$translate_9[$lang]['message']:($data?$data['message']:'') !!}</textarea>
                                            </div>
                                        </div>
                                        @if ($mod_type != 'parcel')
                                            @php($orm=\App\Models\NotificationMessage::with('translations')->where('module_type',$mod_type)->where('key','order_refunded_message')->first())
                                            @php($data=$orm?$orm:'')
                                            <?php
                                            if(isset($orm->translations)&&count($orm->translations)){
                                                    $translate_10 = [];
                                                    foreach($orm->translations as $t)
                                                    {
                                                        if($t->locale == $lang && $t->key=='order_refunded_message'){
                                                            $translate_10[$lang]['message'] = $t->value;
                                                        }
                                                    }

                                                }

                                            ?>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <div class="d-flex flex-wrap justify-content-between mb-2">
                                                        <span class="d-block form-label">
                                                            {{translate('messages.order_refunded_message')}}
                                                        </span>
                                                        @if ($lang == 'en')
                                                            <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                            for="order_refunded_message">
                                                                <input type="checkbox"
                                                                       data-id="order_refunded_message"
                                                                       data-type="toggle"
                                                                       data-image-on="{{ asset('/public/assets/admin/img/modal/pending-order-on.png') }}"
                                                                       data-image-off="{{ asset('/public/assets/admin/img/modal/pending-order-off.png') }}"
                                                                       data-title-on="{{ translate('By Turning ON Order') }} <strong>{{ translate('Order Refund Message') }}</strong>"
                                                                       data-title-off="{{ translate('By Turning OFF Order') }} <strong>{{ translate('Order Refund Message') }}</strong>"
                                                                       data-text-on="<p>{{ translate('User will get a clear message to know that the order is refunded.') }}</p>"
                                                                       data-text-off="<p>{{ translate('User cannot get a clear message to know that the order is refunded or not.') }}</p>"
                                                                       class="status toggle-switch-input add-required-attribute  dynamic-checkbox-toggle"
                                                                       name="order_refunded_message_status"
                                                                       data-textarea-name="order_refunded_message"
                                                                       value="1"
                                                                        id="order_refunded_message" {{$data?($data['status']==1?'checked':''):''}}>
                                                                <span class="toggle-switch-label">
                                                                    <span class="toggle-switch-indicator"></span>
                                                                    </span>
                                                            </label>
                                                        @endif
                                                    </div>

                                                    <textarea name="order_refunded_message[]"  placeholder="{{translate('Write your message')}}" class="form-control order_refunded_message"                                           @if ($lang == 'en')
                                                    {{$data?($data['status']==1?'required':''):''}}
                                                    @endif
                                                    >{!! (isset($translate_10) && isset($translate_10[$lang]))?$translate_10[$lang]['message']:($data?$data['message']:'') !!}</textarea>
                                                </div>
                                            </div>

                                            @php($rrcm=\App\Models\NotificationMessage::with('translations')->where('module_type',$mod_type)->where('key','refund_request_canceled')->first())
                                            @php($data=$rrcm?$rrcm:'')
                                            <?php
                                            if(isset($rrcm->translations) && count($rrcm->translations)){
                                                    $translate_11 = [];
                                                    foreach($rrcm->translations as $t)
                                                    {
                                                        if($t->locale == $lang && $t->key=='refund_request_canceled'){
                                                            $translate_11[$lang]['message'] = $t->value;
                                                        }
                                                    }
                                                }

                                            ?>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <div class="d-flex flex-wrap justify-content-between mb-2">
                                                        <span class="d-block form-label">
                                                            {{translate('messages.refund_request_canceled_message')}}
                                                        </span>
                                                        @if ($lang == 'en')
                                                            <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                            for="refund_request_canceled">
                                                                <input type="checkbox"
                                                                       data-id="refund_request_canceled"
                                                                       data-type="toggle"
                                                                       data-image-on="{{ asset('/public/assets/admin/img/modal/pending-order-on.png') }}"
                                                                       data-image-off="{{ asset('/public/assets/admin/img/modal/pending-order-off.png') }}"
                                                                       data-title-on="{{ translate('By Turning ON Order') }} <strong>{{ translate('Refund Request Cancel Message') }}</strong>"
                                                                       data-title-off="{{ translate('By Turning OFF Order') }} <strong>{{ translate('Refund Request Cancel Message') }}</strong>"
                                                                       data-text-on="<p>{{ translate('User will get a clear message to know that the order\'s refund request is canceled.') }}</p>"
                                                                       data-text-off="<p>{{ translate('User cannot get a clear message to know that the order\'s refund request is canceled or not.') }}</p>"
                                                                       class="status toggle-switch-input add-required-attribute  dynamic-checkbox-toggle"

                                                                       name="refund_request_canceled_status"

                                                                       data-textarea-name="refund_request_canceled"
                                                                        value="1"
                                                                        id="refund_request_canceled" {{$data?($data['status']==1?'checked':''):''}}>
                                                                <span class="toggle-switch-label">
                                                                    <span class="toggle-switch-indicator"></span>
                                                                    </span>
                                                            </label>
                                                        @endif
                                                    </div>
                                                    <textarea name="refund_request_canceled[]"  placeholder="{{translate('Write your message')}}" class="form-control refund_request_canceled"                                           @if ($lang == 'en')
                                                    {{$data?($data['status']==1?'required':''):''}}
                                                    @endif
                                                    >{!! (isset($translate_11) && isset($translate_11[$lang]))?$translate_11[$lang]['message']:($data?$data['message']:'') !!}</textarea>
                                                </div>
                                            </div>
                                        @endif
                                        @php($ooa=\App\Models\NotificationMessage::with('translations')->where('module_type',$mod_type)->where('key','offline_order_accept_message')->first())
                                        @php($data=$ooa?$ooa:'')
                                        <?php
                                        if(isset($ooa->translations) && count($ooa->translations)){

                                                $translate_12 = [];
                                                foreach($ooa->translations as $t)
                                                {
                                                    if($t->locale == $lang && $t->key=='offline_order_accept_message'){
                                                        $translate_12[$lang]['message'] = $t->value;
                                                    }
                                                }

                                            }

                                        ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                                    <span class="d-block form-label">
                                                        {{translate('messages.offline_order_accept_message')}}
                                                    </span>
                                                    @if ($lang == 'en')
                                                        <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                                for="offline_order_accept_message">
                                                            <input type="checkbox"

                                                                   data-id="offline_order_accept_message"
                                                                   data-type="toggle"
                                                                   data-image-on="{{ asset('/public/assets/admin/img/modal/pending-order-on.png') }}"
                                                                   data-image-off="{{ asset('/public/assets/admin/img/modal/pending-order-off.png') }}"
                                                                   data-title-on="{{ translate('By Turning ON Offline Order') }} <strong>{{ translate('accept Message') }}</strong>"
                                                                   data-title-off="{{ translate('By Turning OFF Offline Order') }} <strong>{{ translate('accept Message') }}</strong>"
                                                                   data-text-on="<p>{{ translate('User will get a clear message to know that the offline order is accepted.') }}</p>"
                                                                   data-text-off="<p>{{ translate('User cannot get a clear message to know that the offline order is accepted or not.') }}</p>"
                                                                   class="status toggle-switch-input add-required-attribute  dynamic-checkbox-toggle"
                                                                   name="offline_order_accept_message_status"
                                                                   data-textarea-name="offline_order_accept_message"
                                                                    value="1"
                                                                    id="offline_order_accept_message" {{$data?($data['status']==1?'checked':''):''}}>
                                                            <span class="toggle-switch-label">
                                                                <span class="toggle-switch-indicator"></span>
                                                                </span>
                                                        </label>

                                                    @endif
                                                </div>

                                                <textarea name="offline_order_accept_message[]"  placeholder="{{translate('Write your message')}}" class="form-control offline_order_accept_message"                                           @if ($lang == 'en')
                                                {{$data?($data['status']==1?'required':''):''}}
                                                @endif
                                                >{!! (isset($translate_12) && isset($translate_12[$lang]))?$translate_12[$lang]['message']:($data?$data['message']:'') !!}</textarea>
                                            </div>
                                        </div>
                                        @php($ood=\App\Models\NotificationMessage::with('translations')->where('module_type',$mod_type)->where('key','offline_order_deny_message')->first())
                                        @php($data=$ood?$ood:'')
                                        <?php
                                        if(isset($ood->translations) && count($ood->translations)){

                                                $translate_13 = [];
                                                foreach($ood->translations as $t)
                                                {
                                                    if($t->locale == $lang && $t->key=='offline_order_deny_message'){
                                                        $translate_13[$lang]['message'] = $t->value;
                                                    }
                                                }

                                            }

                                        ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <div class="d-flex flex-wrap justify-content-between mb-2">
                                                    <span class="d-block form-label">
                                                        {{translate('messages.offline_order_deny_message')}}
                                                    </span>
                                                    @if ($lang == 'en')
                                                        <label class="switch--custom-label toggle-switch d-flex align-items-center mb-0"
                                                                for="offline_order_deny_message">
                                                            <input type="checkbox"
                                                                   data-id="offline_order_deny_message"
                                                                   data-type="toggle"
                                                                   data-image-on="{{ asset('/public/assets/admin/img/modal/pending-order-on.png') }}"
                                                                   data-image-off="{{ asset('/public/assets/admin/img/modal/pending-order-off.png') }}"
                                                                   data-title-on="{{ translate('By Turning ON Offline Order') }} <strong>{{ translate('deny Message') }}</strong>"
                                                                   data-title-off="{{ translate('By Turning OFF Offline Order') }} <strong>{{ translate('deny Message') }}</strong>"
                                                                   data-text-on="<p>{{ translate('User will get a clear message to know that the offline order is denied.') }}</p>"
                                                                   data-text-off="<p>{{ translate('User cannot get a clear message to know that the offline order is denied or not.') }}</p>"
                                                                   class="status toggle-switch-input add-required-attribute  dynamic-checkbox-toggle"
                                                                   name="offline_order_deny_message_status"
                                                                   data-textarea-name="offline_order_deny_message"
                                                                   value="1"
                                                                    id="offline_order_deny_message" {{$data?($data['status']==1?'checked':''):''}}>
                                                            <span class="toggle-switch-label">
                                                                <span class="toggle-switch-indicator"></span>
                                                                </span>
                                                        </label>

                                                    @endif
                                                </div>

                                                <textarea name="offline_order_deny_message[]"  placeholder="{{translate('Write your message')}}" class="form-control offline_order_deny_message"                                           @if ($lang == 'en')
                                                {{$data?($data['status']==1?'required':''):''}}
                                                @endif
                                                >{!! (isset($translate_13) && isset($translate_13[$lang]))?$translate_13[$lang]['message']:($data?$data['message']:'') !!}</textarea>
                                            </div>
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{$lang}}">
                                        <input type="hidden" name="module_type" value="{{$mod_type}}">
                                    </div>
                                </div>
                                @endforeach
                            @endif
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Firebase Modal -->
        <div class="modal fade" id="push-notify-modal">
            <div class="modal-dialog status-warning-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true" class="tio-clear"></span>
                        </button>
                    </div>
                    <div class="modal-body pb-5 pt-0">
                        <div class="single-item-slider owl-carousel">
                            <div class="item">
                                <div class="mb-20">
                                    <div class="text-center">
                                        <img src="{{asset('/public/assets/admin/img/email-templates/3.png')}}" alt="" class="mb-20">
                                        <h5 class="modal-title">{{translate('Write_a_message_in_the_Notification_Body')}}</h5>
                                    </div>
                                    <p>
                                        {{ translate('you_can_add_your_message_using_placeholders_to_include_dynamic_content._Here_are_some_examples_of_placeholders_you_can_use:') }}
                                    </p>
                                    <ul>
                                        <li>
                                            {userName}: {{ translate('the_name_of_the_user.') }}
                                        </li>
                                        <li>
                                            {storeName}: {{ translate('the_name_of_the_store.') }}
                                        </li>
                                        <li>
                                            {orderId}: {{ translate('the_order_id.') }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="item">
                                <div class="mb-20">
                                    <div class="text-center">
                                        <img src="{{asset('/public/assets/admin/img/firebase/slide-4.png')}}" alt="" class="mb-20">
                                        <h5 class="modal-title">{{translate('Please Visit the Docs to Set FCM on Mobile Apps')}}</h5>
                                    </div>
                                    <div class="text-center">
                                        <p>
                                            {{translate('Please check the documentation below for detailed instructions on setting up your mobile app to receive Firebase Cloud Messaging (FCM) notifications.')}}
                                        </p>
                                        <a href="https://docs.6amtech.com/docs-six-am-mart/mobile-apps/mandatory-setup" target="_blank">{{translate('Click Here')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <div class="slide-counter"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection