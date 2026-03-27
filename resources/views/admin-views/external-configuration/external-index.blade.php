@extends('layouts.admin.app')

@section('title', translate('ride_sharing_setup_and_integration'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header d-flex flex-wrap gap-3 align-items-center justify-content-between mb-3">
            <div>
                <h1 class="page-header-title m-0">
                    <span>
                        {{translate('ride_sharing_setup_and_integration')}}
                    </span>
                </h1>
                <p class="m-0">
                    {{translate('connect_drivemond_system_with_6ammart')}}
                </p>
            </div>
            <div class="text--primary-2 py-1 d-flex flex-wrap align-items-center" type="button" data-toggle="modal"
                 data-target="#how-it-works">
                <strong class="mr-2">{{translate('how_the_setting_works')}}</strong>
                <div>
                    <i class="tio-info-outined"></i>
                </div>
            </div>
        </div>
        <!-- Page Header -->

        <!-- End Page Header -->
        <form action="{{ route('admin.business-settings.external-system.update-drivemond-configuration') }}"
              method="post"
              enctype="multipart/form-data">
            @csrf
            <div class="row g-2">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @php($activationMode = \App\Models\ExternalConfiguration::where('key', 'activation_mode')->first())
                            @php($activationMode = $activationMode ? $activationMode->value : 0)
                            <div class="border rounded d-flex flex-wrap gap-2 align-items-center p-3 p-sm-4">
                                <div class="w-160px flex-grow-1">
                                    <h5>{{translate('Activation Mode')}}</h5>
                                    <p class="fs-12 m-0">
                                        {{translate('Enable the switch to activate the purchased Software- Drivemond ride-sharing in the 6amMart system. You must input the correct information to make sure the functionality works properly.')}}
                                    </p>
                                </div>
                                <label class="toggle-switch toggle-switch-sm">
                                    <input type="checkbox" value="1" class="toggle-switch-input" name="activation_mode"
                                           id="websocket" {{ $activationMode == 1 ? 'checked' : '' }}>
                                    <span class="toggle-switch-label text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </div>
                            <div class="row g-4 mt-2">
                                <div class="col-md-12">
                                    @php($drivemondBaseUrl = \App\Models\ExternalConfiguration::where('key', 'drivemond_base_url')->first())
                                    <div class="p-3 p-sm-4 bg-soft-secondary rounded">
                                        <label class="form-label">{{ translate('Ride Sharing System Base URL') }}
                                            <i class="tio-info-outined text-primary"
                                               title="{{translate("Need to get the purchased software - Drivemond Ride Sharing‘s Base URL to insert it into this input field.")}}"
                                               data-toggle="tooltip"></i>
                                        </label>
                                        <input type="url" id="drivemondBaseUrl" name="drivemond_base_url"
                                               value="{{ $drivemondBaseUrl->value ?? '' }}"
                                               class="form-control"
                                               placeholder="{{ translate('Ex: https://drivemond.com') }}"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    @php($drivemondToken = \App\Models\ExternalConfiguration::where('key', 'drivemond_token')->first())
                                    <div class="p-3 p-sm-4 bg-soft-secondary rounded">
                                        <label class="form-label">{{ translate('Ride Sharing System Token') }}
                                            <i class="tio-info-outined text-primary"
                                               title="{{translate("From the purchased software - Drivemond Ride Sharing Admin panel’s Ecommerce Setup & Integration page, Copy the System token and insert it into this input field.")}}"
                                               data-toggle="tooltip"></i>
                                        </label>
                                        <input id="drivemondToken" maxlength="64" minlength="64" type="text"
                                               value="{{ $drivemondToken->value ?? '' }}" name="drivemond_token"
                                               class="form-control"
                                               placeholder="{{ translate('enter_drivemond_token') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    @php($systemSelfToken = \App\Models\ExternalConfiguration::where('key', 'system_self_token')->first())
                                    <div class="p-3 p-sm-4 bg-soft-secondary rounded">
                                        <label
                                            class="form-label">{{ (\App\CentralLogics\Helpers::get_business_data('business_name') ?? "6amMart" ) . ' ' .translate('System Token') }}
                                            <i class="tio-info-outined text-primary"
                                               title="{{ translate("Click on the Generate Token button, It will automatically generate the 6amMart System token and insert it into the input field.") }}"
                                               data-toggle="tooltip"></i>
                                        </label>
                                        <div class="input-group input-token-group">
                                            <div class="position-relative">
                                                <input id="systemSelfToken" maxlength="64" minlength="64" type="text"
                                                       value="{{ $systemSelfToken->value ?? '' }}"
                                                       name="system_self_token" class="form-control"
                                                       placeholder="{{ translate('generate_system_self_token') }}"
                                                       required>
                                                <a href="javascript:void(0)" class="generate-code text-primary"
                                                   id="copyButton"><i class="tio-copy"></i> </a>
                                            </div>
                                            <a href="javascript:void(0)" class="btn btn--primary input-group-text"
                                               id="generateSystemSelfToken">{{translate("generate_token")}} <i
                                                    class="tio-code"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="btn--container justify-content-end mt-20">
                                <button type="reset" id="reset_btn"
                                        class="btn btn--reset">{{ translate('messages.reset') }}</button>
                                <button type="submit" id="submit"
                                        class="btn btn--primary">{{ translate('messages.save_information') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- How It Works Modal --}}
        <div class="modal fade how-it-works-modal" id="how-it-works">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header px-2 pt-2">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true" class="tio-clear"></span>
                        </button>
                    </div>
                    <div class="modal-body pb-5 pt-0 px-lg-5">
                        <h4 class="mb-3">{{translate('How does it works')}} ?</h4>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="">
                                    <img src="{{asset('/public/assets/admin/img/how-it-works/Step-1.svg')}}" alt=""
                                         class="mb-20">
                                    <div class="how-it-count">
                                        <span>1</span>
                                    </div>
                                    <h5 class="mb-2">{{translate('Ride Sharing System Base URL Insertion')}}</h5>
                                    <p>
                                        {{translate("At first, Need to insert the Base URL of the deploy Software- Drivemond ride-sharing.")}}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="">
                                    <img src="{{asset('/public/assets/admin/img/how-it-works/step-3.svg')}}" alt=""
                                         class="mb-20">
                                    <div class="how-it-count">
                                        <span>2</span>
                                    </div>
                                    <h5 class="mb-2">{{translate('Ride Sharing System Token Input')}}</h5>
                                    <p>
                                        {{translate("Visit the Drivemond Ride Sharing")}} <a
                                            href="{{\App\CentralLogics\Helpers::get_external_data('drivemond_base_url')?  (\App\CentralLogics\Helpers::get_external_data('drivemond_base_url').'/admin/auth/login') : "#"}}"
                                            class="text-underline text-primary">{{translate("Admin Panel")}}</a>
                                        <br>
                                        {{translate('Go to “ Business Management Section → Ecommerce Setup & Integration”')}}
                                        <br>
                                        {{translate("Copy the Generated ")}}
                                        <strong>{{translate("Drivemond System Token ")}}</strong>{{translate("and paste it here to the Ride Sharing System Token input field.")}}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="">
                                    <img src="{{asset('/public/assets/admin/img/how-it-works/step-2.svg')}}" alt=""
                                         class="mb-20">
                                    <div class="how-it-count">
                                        <span>3</span>
                                    </div>
                                    <h5 class="mb-2">{{(\App\CentralLogics\Helpers::get_business_data('business_name') ?? "6amMart" ) . ' ' .translate('System Token Generate')}}</h5>
                                    <p>
                                        {{translate("At last,  Click on the  ")}}
                                        <strong>{{translate("Generate Token ")}}</strong>
                                        {{translate("button for automatic token generation & paste it Into the input field of ")}}
                                        {{(\App\CentralLogics\Helpers::get_business_data('business_name') ?? "6amMart" ) . ' ' .translate('System Token Generate')}}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="pb-1">
                            <i class="text-dark">{{translate('Note :  Follow the same steps on Drivemond to successfully connect 6amMart with Drivemond')}}</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
