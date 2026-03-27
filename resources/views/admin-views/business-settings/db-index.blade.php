@extends('layouts.admin.app')

@section('title', translate('DB_clean'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/cloud-database.png')}}" class="w--26" alt="">
            </span>
            <span>
                {{translate('Clean database')}}
            </span>
        </h1>
    </div>
    <!-- End Page Header -->
        <div class="alert alert--danger alert-danger mb-3" role="alert">
            <span class="alert--icon"><i class="tio-info"></i></span>
            <strong class="text--title">{{translate('note_:')}}</strong>
            <span>
                {{translate('This_page_contains_sensitive_information.Make_sure_before_changing.')}}
            </span>
        </div>
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.business-settings.clean-db')}}" method="post"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="check--item-wrapper clean--database-checkgroup mt-0">
                        @foreach($tables as $key=>$table)
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="tables[]" value="{{$table}}" class="form-check-input" id="{{$table}}">
                                    <label class="form-check-label text-dark {{Session::get('direction') === "rtl" ? 'mr-3' : ''}};" for="{{$table}}">{{ Str::limit($table, 20) }} <span class="badge-pill badge-secondary mx-2">{{$rows[$key]}}</span></label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="btn--container justify-content-end mt-4">
                        <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" class="btn btn--primary call-demo">{{translate('Clear')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



@endsection

@push('script_2')
<script>
    "use strict";

    let store_dependent = ['stores','store_schedule', 'discounts' ,'campaign_store' ,'store_configs' ,'store_notification_settings' ,'store_subscriptions','store_wallets','disbursements' ,'disbursement_details','disbursement_withdrawal_methods'] ;
    let order_dependent = ['order_delivery_histories','d_m_reviews', 'delivery_histories', 'track_deliverymen', 'order_details', 'reviews','order_transactions','offline_payments','order_payments','order_references','refunds','cash_back_histories','expenses'];
    let zone_dependent = ['stores','vendors', 'orders'];
    $(document).ready(function () {
        $('.form-check-input').on('change', function(event){
            if($(this).is(':checked')){
                if(event.target.id === 'zones' || event.target.id === 'stores' || event.target.id === 'vendors') {
                    checked_stores(true);
                }

                if(event.target.id === 'zones' || event.target.id === 'orders') {
                    checked_orders(true);
                }
            } else {
                if(store_dependent.includes(event.target.id)) {
                    if(check_store() || check_zone()){
                        console.log('store_checked');
                        $(this).prop('checked', true);
                    }
                } else if(order_dependent.includes(event.target.id)) {
                    if(check_orders() || check_zone()){
                        $(this).prop('checked', true);
                    }
                } else if(zone_dependent.includes(event.target.id)) {
                    if(check_zone()){
                        $(this).prop('checked', true);
                    }
                }
            }

        });


    })

    function checked_stores(status) {
        store_dependent.forEach(function(value){
            $('#'+value).prop('checked', status);
        });
        $('#vendors').prop('checked', status);

    }

    function checked_orders(status) {
        order_dependent.forEach(function(value){
            $('#'+value).prop('checked', status);
        });
        $('#orders').prop('checked', status);
    }



    function check_zone() {
        if($('#zones').is(':checked')) {
            toastr.warning("{{translate('messages.table_unchecked_warning',['table'=>'zones'])}}");
            return true;
        }
        return false;
    }

    function check_orders() {
        if($('#orders').is(':checked')) {
            toastr.warning("{{translate('messages.table_unchecked_warning',['table'=>'orders'])}}");
            return true;
        }
        return false;
    }

    function check_store() {
        if($('#stores').is(':checked') || $('#vendors').is(':checked')) {
            toastr.warning("{{translate('messages.table_unchecked_warning',['table'=>'stores/vendors'])}}");
            return true;
        }
        return false;
    }

    $("form").on('submit',function(e) {
        e.preventDefault();
        Swal.fire({
            title: '{{translate('Are you sure?')}}',
            text: "{{translate('Sensitive_data! Make_sure_before_changing.')}}",
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: '{{ translate('messages.no') }}',
            confirmButtonText: '{{ translate('messages.yes') }}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                this.submit();
            }else{
                e.preventDefault();
                toastr.success("{{translate('Cancelled')}}");
                location.reload();
            }
        })
    });
</script>
@endpush
