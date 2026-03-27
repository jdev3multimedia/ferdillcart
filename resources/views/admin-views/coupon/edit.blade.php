@extends('layouts.admin.app')

@section('title',translate('edit_coupon'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/edit.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.coupon_update')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.coupon.update',[$coupon['id']])}}" method="post" class="custom-validation">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            @if($language)
                                <ul class="nav nav-tabs mb-4">
                                    <li class="nav-item">
                                        <a class="nav-link lang_link active"
                                        href="#"
                                        id="default-link">{{translate('messages.default')}}</a>
                                    </li>
                                    @foreach ($language as $lang)
                                        <li class="nav-item">
                                            <a class="nav-link lang_link"
                                                href="#"
                                                id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="lang_form" id="default-form">
                                    <div class="form-group error-wrapper">
                                        <label class="input-label" for="default_title">{{translate('messages.title')}} ({{translate('messages.default')}})</label>
                                        <input type="text" name="title[]" id="default_title" class="form-control" placeholder="{{translate('messages.new_coupon')}}" value="{{$coupon?->getRawOriginal('title')}}"  >
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">
                                </div>
                                @foreach($language as $lang)
                                    <?php
                                        if(count($coupon['translations'])){
                                            $translate = [];
                                            foreach($coupon['translations'] as $t)
                                            {
                                                if($t->locale == $lang && $t->key=="title"){
                                                    $translate[$lang]['title'] = $t->value;
                                                }
                                            }
                                        }
                                    ?>
                                    <div class="d-none lang_form" id="{{$lang}}-form">
                                        <div class="form-group error-wrapper">
                                            <label class="input-label" for="{{$lang}}_title">{{translate('messages.title')}} ({{strtoupper($lang)}})</label>
                                            <input type="text" name="title[]" id="{{$lang}}_title" class="form-control" placeholder="{{translate('messages.new_coupon')}}" value="{{$translate[$lang]['title']??''}}"  required>
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{$lang}}">
                                    </div>
                                @endforeach
                            @else
                            <div id="default-form">
                                <div class="form-group error-wrapper">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.title')}} ({{ translate('messages.default') }})</label>
                                    <input type="text" name="title[]" class="form-control" placeholder="{{translate('messages.new_coupon')}}" value="{{$coupon['title']}}" maxlength="100">
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                            </div>
                            @endif
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0 error-wrapper">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.coupon_type')}}</label>
                                <select name="coupon_type" id="coupon_type" class="form-control" required>
                                    <option value="store_wise" {{$coupon['coupon_type']=='store_wise'?'selected':''}}>{{translate('messages.store_wise')}}</option>
                                    <option value="zone_wise" {{$coupon['coupon_type']=='zone_wise'?'selected':''}}>{{translate('messages.zone_wise')}}</option>
                                    <option value="free_delivery" {{$coupon['coupon_type']=='free_delivery'?'selected':''}}>{{translate('messages.free_delivery')}}</option>
                                    <option value="first_order" {{$coupon['coupon_type']=='first_order'?'selected':''}}>{{translate('messages.first_order')}}</option>
                                    <option value="default" {{$coupon['coupon_type']=='default'?'selected':''}}>{{translate('messages.default')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6" id="store_wise">
                            <div class="form-group m-0 error-wrapper">
                                    <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.store')}}<span
                                            class="input-label-secondary"></span></label>
                                    <select name="store_ids[]" class="js-data-example-ajax form-control"  title="Select Restaurant">
                                    @if($coupon->coupon_type == 'store_wise')
                                    @php($store=\App\Models\Store::find(json_decode($coupon->data)[0]))
                                        @if($store)
                                        <option value="{{$store->id}}">{{$store->name}}</option>
                                        @endif
                                    @else
                                    <option selected>{{ translate('Select Store') }}</option>
                                    @endif
                                    </select>
                                </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6"  id="zone_wise">
                            <div class="form-group m-0 error-wrapper">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.select_zone')}}</label>
                                <select name="zone_ids[]" id="choice_zones"
                                    class="form-control multiple-select2"
                                    multiple="multiple" placeholder="{{translate('messages.select_zone')}}">
                                @foreach($zones as $zone)
                                    <option value="{{$zone->id}}" {{($coupon->coupon_type=='zone_wise'&&json_decode($coupon->data))?(in_array($zone->id, json_decode($coupon->data))?'selected':''):''}}>{{$zone->name}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-4 col-lg-3 col-sm-6 error-wrapper" id="customer_wise" style="display: {{$coupon['coupon_type'] =='zone_wise' || $coupon['coupon_type'] =='first_order' ?'none':'block'}}">
                            <label class="input-label" for="select_customer">{{translate('messages.select_customer')}}</label>
                            <select name="customer_ids[]" id="select_customer"
                                class="form-control multiple-select2"
                                multiple="multiple" placeholder="{{translate('messages.select_customer')}}">
                                <option value="all" {{in_array('all', json_decode($coupon->customer_id))?'selected':''}}>{{translate('messages.all')}} </option>
                                @foreach(\App\Models\User::get(['id','f_name','l_name']) as $user)
                                <option value="{{$user->id}}" {{in_array($user->id, json_decode($coupon->customer_id))?'selected':''}}>{{$user->f_name.' '.$user->l_name}}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0 error-wrapper">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.code')}}</label>
                                <input type="text" name="code" class="form-control" value="{{$coupon['code']}}"
                                       placeholder="{{\Illuminate\Support\Str::random(8)}}" required maxlength="100">
                            </div>
                        </div>
                        <div id="limit_for_same_user" class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0 error-wrapper">
                                <label class="input-label" for="limit">{{translate('messages.limit_for_same_user')}}</label>
                                <input type="number" name="limit" id="coupon_limit" data-value="{{$coupon['limit']}}" value="{{$coupon['limit']}}" class="form-control" max="100"
                                       placeholder="{{ translate('EX: 10') }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0 error-wrapper">
                                <label class="input-label" for="">{{translate('messages.start_date')}}</label>
                                <input type="date" name="start_date" class="form-control" id="date_from" placeholder="{{translate('messages.select_date')}}" value="{{date('Y-m-d',strtotime($coupon['start_date']))}}">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0 error-wrapper">
                                <label class="input-label" for="date_to">{{translate('messages.expire_date')}}</label>
                                <input type="date" name="expire_date" class="form-control" placeholder="{{translate('messages.select_date')}}" id="date_to" value="{{date('Y-m-d',strtotime($coupon['expire_date']))}}"
                                       data-hs-flatpickr-options='{
                                     "dateFormat": "Y-m-d"
                                   }'>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0 error-wrapper">
                                <label class="input-label" for="discount_type">{{translate('messages.discount_type')}}</label>
                                <select name="discount_type" id="discount_type" class="form-control">
                                    <option value="amount" {{$coupon['discount_type']=='amount'?'selected':''}}>{{translate('messages.amount')}} ({{ \App\CentralLogics\Helpers::currency_symbol() }})
                                    </option>
                                    <option value="percent" {{$coupon['discount_type']=='percent'?'selected':''}}>
                                        {{translate('messages.percent')}} (%)
                                    </option>
                                </select>
                            </div>
                        </div>
                            <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0 error-wrapper">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.min_purchase')}} ({{ \App\CentralLogics\Helpers::currency_symbol() }})</label>
                                <input type="number" id="min_purchase" name="min_purchase" step="0.01" value="{{$coupon['min_purchase']}}"
                                       min="0" max="999999999999.99" class="form-control"
                                       placeholder="100">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0 error-wrapper">
                                <label class="input-label" for="discount">{{translate('messages.discount')}}
                                    <span class="input-label-secondary text--title" data-toggle="tooltip"
                                        data-placement="right"
                                        data-original-title="{{ translate('Currently you need to manage discount with the Restaurant.') }}">
                                        <i class="tio-info-outined"></i>
                                    </span>
                                </label>
                                <input type="number" id="discount" min="1" max="999999999999.99" step="0.01" value="{{$coupon['discount']}}"
                                       name="discount" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <div class="form-group m-0 error-wrapper">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.max_discount')}} ({{ \App\CentralLogics\Helpers::currency_symbol() }})</label>
                                <input type="number" min="0" max="999999999999.99" step="0.01" value="{{$coupon['max_discount']}}" name="max_discount" id="max_discount" class="form-control" {{$coupon['discount_type']=='amount'?'readonly="readonly"':''}}>
                            </div>
                        </div>

                    </div>
                    <div class="btn--container justify-content-end mt-4">
                        <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                    </div>
                </form>
            </div>
            <!-- End Table -->
        </div>
    </div>
    <input type="hidden" id="min-purchase-toast" value="{{ translate('messages.Discount amount cannot be greater than minimum purchase amount') }}">

@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin')}}/js/view-pages/coupon-edit.js"></script>
    <script>
        "use strict";
        coupon_type_change('{{$coupon->coupon_type}}');

        $(document).on('ready', function () {
            let module_id = 0;
            $('#date_from').attr('max','{{date("Y-m-d",strtotime($coupon["expire_date"]))}}');
            $('#date_to').attr('min','{{date("Y-m-d",strtotime($coupon["start_date"]))}}');
            @if($coupon['discount_type']=='amount')
            $('#max_discount').attr("readonly","true");
            $('#max_discount').val(0);
            @endif


            $('.js-data-example-ajax').select2({
                ajax: {
                    url: '{{url('/')}}/admin/store/get-stores',
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page,
                            module_id: module_id
                        };
                    },
                    processResults: function (data) {
                        return {
                        results: data
                        };
                    },
                    __port: function (params, success, failure) {
                        let $request = $.ajax(params);

                        $request.then(success);
                        $request.fail(failure);

                        return $request;
                    }
                }
            });
            // INITIALIZATION OF FLATPICKR
            // =======================================================
            $('.js-flatpickr').each(function () {
                $.HSCore.components.HSFlatpickr.init($(this));
            });
        });



    </script>
@endpush
