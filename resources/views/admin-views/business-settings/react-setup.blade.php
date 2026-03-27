@extends('layouts.admin.app')

@section('title', translate('messages.react_site_setup'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-sm-0">
                    <h1 class="page-header-title">{{translate('React Site Setup')}}</h1>
                </div>
            </div>
        </div>
        @php($react_setup=\App\Models\BusinessSetting::where(['key'=>'react_setup'])->first())
        @php($react_setup=$react_setup?json_decode($react_setup->value, true):null)

        <!-- End Page Header -->
        <div class="card">

            <div class="card-body">
                <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.react-update'):'javascript:'}}" method="post"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="react_license_code" class="form-label text-capitalize">{{translate('React license code')}}</label>
                                <input id="react_license_code" type="text" placeholder="{{translate('React license code')}}" class="form-control h--45px" name="react_license_code"
                                    value="{{env('APP_MODE')!='demo'?( $react_setup['react_license_code'] ?? ''):''}}" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="react_domain" class="form-label text-capitalize">{{translate('React Domain')}}</label>
                                <input id="react_domain" type="text" placeholder="{{translate('React Domain')}}" class="form-control h--45px" name="react_domain"
                                    value="{{env('APP_MODE')!='demo'?( $react_setup['react_domain'] ?? ''):''}}" required>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"  class="btn btn--primary mb-2 call-demo">{{translate('messages.save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

@endpush
