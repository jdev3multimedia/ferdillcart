@extends('layouts.admin.app')

@section('title',translate('messages.new_page'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">

<script type="text/javascript" src="{{asset('public/assets/admin/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/admin/js/daterangepicker.min.js')}}"></script>
@endpush

@section('content')
<div class="content container-fluid">
    <h2 class="mb-20 fs-24">Marketing Tool</h2>
    <div class="info-notes-bg px-2 py-2 rounded fs-12  gap-2 align-items-center d-flex mb-15">
       <img width="14" height="18" src="{{ asset('public/assets/admin/img/info-idea.svg') }}" class="w--20" alt="">
        <span>
            In this page you can add credentials to show your analytics on the platform. Make sure fill with proper data other wise you can’t see the analytics properly.
        </span>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <form action="#0">
                <div class="card view-details-container">
                    <div class="card-body p-20">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="">
                                <h3 class="black-color mb-1 d-block">Google Analytics</h3>
                                <p class="fs-12 text-c mb-1">To know more <a href="#0" class="theme-clr fs-12 font-semibold text-underline">Click Here.</a></p>
                            </div>
                            <div class="">
                                <div class="d-flex flex-sm-nowrap flex-wrap justify-content-end justify-content-end align-items-center gap-sm-3 gap-2">
                                    <div class="view-btn order-sm-0 order-3 fs-12 theme-clr cursor-pointer text-decoration-underline font-semibold d-flex align-items-center gap-1">
                                        View 
                                        <i class="tio-arrow-downward fs-12"></i>
                                    </div>
                                    <div class="mb-0">
                                        <label class="toggle-switch toggle-switch-sm mb-0" data-toggle="modal" data-target="#confirmation-modal-feature">                                           
                                            <input type="checkbox" class="status toggle-switch-input" name="status" value="">
                                            <span class="toggle-switch-label text mb-0">
                                                <span class="toggle-switch-indicator">
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="view-details mt-20">
                            <div class="bg--secondary rounded px-20 py-25"> 
                                <div class="form-group m-0">
                                    <label for="" class="fs-14 mb-10px text-title">Google Analytics Measurement ID</label>
                                    <div class="flex-xs-wrap d-flex align-items-center gap-3">
                                        <textarea name="gs" rows="1" class="form-control" placeholder="Enter the GA Measurement ID"></textarea>
                                        <button type="submit" class="btn py-1 min-w-100px h-40px btn--primary ">Save</button>
                                    </div>             
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <form action="#0">
                <div class="card view-details-container">
                    <div class="card-body p-20">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="">
                                <h3 class="black-color mb-1 d-block">Google Tag Manager</h3>
                                <p class="fs-12 text-c mb-1">To know more <a href="#0" class="theme-clr fs-12 font-semibold text-underline">Click Here.</a></p>
                            </div>
                            <div class="">
                                <div class="d-flex flex-sm-nowrap flex-wrap justify-content-end justify-content-end align-items-center gap-sm-3 gap-2">
                                    <div class="view-btn order-sm-0 order-3 fs-12 theme-clr cursor-pointer text-decoration-underline font-semibold d-flex align-items-center gap-1">
                                        View 
                                        <i class="tio-arrow-downward fs-12"></i>
                                    </div>
                                    <div class="mb-0">
                                        <label class="toggle-switch toggle-switch-sm mb-0" data-toggle="modal" data-target="#confirmation-modal-feature"> 
                                            <input type="checkbox" class="status toggle-switch-input" name="status" value="">
                                            <span class="toggle-switch-label text mb-0">
                                                <span class="toggle-switch-indicator">
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="view-details mt-20">
                            <div class="bg--secondary rounded px-20 py-25"> 
                                <div class="form-group m-0">
                                    <label for="" class="fs-14 mb-10px text-title">Google Analytics Measurement ID</label>
                                    <div class="flex-xs-wrap d-flex align-items-center gap-3">
                                        <textarea name="gs" rows="1" class="form-control" placeholder="Enter the GA Measurement ID"></textarea>
                                        <button type="submit" class="btn py-1 min-w-100px h-40px btn--primary ">Save</button>
                                    </div>             
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <form action="#0">
                <div class="card view-details-container">
                    <div class="card-body p-20">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="">
                                <h3 class="black-color mb-1 d-block">LinkedIn Insight Tag</h3>
                                <p class="fs-12 text-c mb-1">To know more <a href="#0" class="theme-clr fs-12 font-semibold text-underline">Click Here.</a></p>
                            </div>
                            <div class="">
                                <div class="d-flex flex-sm-nowrap flex-wrap justify-content-end justify-content-end align-items-center gap-sm-3 gap-2">
                                    <div class="view-btn order-sm-0 order-3 fs-12 theme-clr cursor-pointer text-decoration-underline font-semibold d-flex align-items-center gap-1">
                                        View 
                                        <i class="tio-arrow-downward fs-12"></i>
                                    </div>
                                    <div class="mb-0">
                                        <label class="toggle-switch toggle-switch-sm mb-0" data-toggle="modal" data-target="#confirmation-modal-feature"> 
                                            <input type="checkbox" class="status toggle-switch-input" name="status" value="">
                                            <span class="toggle-switch-label text mb-0">
                                                <span class="toggle-switch-indicator">
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="view-details mt-20">
                            <div class="bg--secondary rounded px-20 py-25"> 
                                <div class="form-group m-0">
                                    <label for="" class="fs-14 mb-10px text-title">Google Analytics Measurement ID</label>
                                    <div class="flex-xs-wrap d-flex align-items-center gap-3">
                                        <textarea name="gs" rows="1" class="form-control" placeholder="Enter the GA Measurement ID"></textarea>
                                        <button type="submit" class="btn py-1 min-w-100px h-40px btn--primary ">Save</button>
                                    </div>             
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <form action="#0">
                <div class="card view-details-container">
                    <div class="card-body p-20">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="">
                                <h3 class="black-color mb-1 d-block">Meta Pixel</h3>
                                <p class="fs-12 text-c mb-1">To know more <a href="#0" class="theme-clr fs-12 font-semibold text-underline">Click Here.</a></p>
                            </div>
                            <div class="">
                                <div class="d-flex flex-sm-nowrap flex-wrap justify-content-end justify-content-end align-items-center gap-sm-3 gap-2">
                                    <div class="view-btn order-sm-0 order-3 fs-12 theme-clr cursor-pointer text-decoration-underline font-semibold d-flex align-items-center gap-1">
                                        View 
                                        <i class="tio-arrow-downward fs-12"></i>
                                    </div>
                                    <div class="mb-0">
                                        <label class="toggle-switch toggle-switch-sm mb-0" data-toggle="modal" data-target="#confirmation-modal-feature"> 
                                            <input type="checkbox" class="status toggle-switch-input" name="status" value="">
                                            <span class="toggle-switch-label text mb-0">
                                                <span class="toggle-switch-indicator">
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="view-details mt-20">
                            <div class="bg--secondary rounded px-20 py-25"> 
                                <div class="form-group m-0">
                                    <label for="" class="fs-14 mb-10px text-title">Google Analytics Measurement ID</label>
                                    <div class="flex-xs-wrap d-flex align-items-center gap-3">
                                        <textarea name="gs" rows="1" class="form-control" placeholder="Enter the GA Measurement ID"></textarea>
                                        <button type="submit" class="btn py-1 min-w-100px h-40px btn--primary ">Save</button>
                                    </div>             
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <form action="#0">
                <div class="card view-details-container">
                    <div class="card-body p-20">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="">
                                <h3 class="black-color mb-1 d-block">Pinterest Pixel</h3>
                                <p class="fs-12 text-c mb-1">To know more <a href="#0" class="theme-clr fs-12 font-semibold text-underline">Click Here.</a></p>
                            </div>
                            <div class="">
                                <div class="d-flex flex-sm-nowrap flex-wrap justify-content-end justify-content-end align-items-center gap-sm-3 gap-2">
                                    <div class="view-btn order-sm-0 order-3 fs-12 theme-clr cursor-pointer text-decoration-underline font-semibold d-flex align-items-center gap-1">
                                        View 
                                        <i class="tio-arrow-downward fs-12"></i>
                                    </div>
                                    <div class="mb-0">
                                        <label class="toggle-switch toggle-switch-sm mb-0" data-toggle="modal" data-target="#confirmation-modal-feature"> 
                                            <input type="checkbox" class="status toggle-switch-input" name="status" value="">
                                            <span class="toggle-switch-label text mb-0">
                                                <span class="toggle-switch-indicator">
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="view-details mt-20">
                            <div class="bg--secondary rounded px-20 py-25"> 
                                <div class="form-group m-0">
                                    <label for="" class="fs-14 mb-10px text-title">Google Analytics Measurement ID</label>
                                    <div class="flex-xs-wrap d-flex align-items-center gap-3">
                                        <textarea name="gs" rows="1" class="form-control" placeholder="Enter the GA Measurement ID"></textarea>
                                        <button type="submit" class="btn py-1 min-w-100px h-40px btn--primary ">Save</button>
                                    </div>             
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <form action="#0">
                <div class="card view-details-container">
                    <div class="card-body p-20">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="">
                                <h3 class="black-color mb-1 d-block">Snapchat Pixel</h3>
                                <p class="fs-12 text-c mb-1">To know more <a href="#0" class="theme-clr fs-12 font-semibold text-underline">Click Here.</a></p>
                            </div>
                            <div class="">
                                <div class="d-flex flex-sm-nowrap flex-wrap justify-content-end justify-content-end align-items-center gap-sm-3 gap-2">
                                    <div class="view-btn order-sm-0 order-3 fs-12 theme-clr cursor-pointer text-decoration-underline font-semibold d-flex align-items-center gap-1">
                                        View 
                                        <i class="tio-arrow-downward fs-12"></i>
                                    </div>
                                    <div class="mb-0">
                                        <label class="toggle-switch toggle-switch-sm mb-0" data-toggle="modal" data-target="#confirmation-modal-feature"> 
                                            <input type="checkbox" class="status toggle-switch-input" name="status" value="">
                                            <span class="toggle-switch-label text mb-0">
                                                <span class="toggle-switch-indicator">
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="view-details mt-20">
                            <div class="bg--secondary rounded px-20 py-25"> 
                                <div class="form-group m-0">
                                    <label for="" class="fs-14 mb-10px text-title">Google Analytics Measurement ID</label>
                                    <div class="flex-xs-wrap d-flex align-items-center gap-3">
                                        <textarea name="gs" rows="1" class="form-control" placeholder="Enter the GA Measurement ID"></textarea>
                                        <button type="submit" class="btn py-1 min-w-100px h-40px btn--primary ">Save</button>
                                    </div>             
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <form action="#0">
                <div class="card view-details-container">
                    <div class="card-body p-20">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="">
                                <h3 class="black-color mb-1 d-block">TikTok Pixel</h3>
                                <p class="fs-12 text-c mb-1">To know more <a href="#0" class="theme-clr fs-12 font-semibold text-underline">Click Here.</a></p>
                            </div>
                            <div class="">
                                <div class="d-flex flex-sm-nowrap flex-wrap justify-content-end justify-content-end align-items-center gap-sm-3 gap-2">
                                    <div class="view-btn order-sm-0 order-3 fs-12 theme-clr cursor-pointer text-decoration-underline font-semibold d-flex align-items-center gap-1">
                                        View 
                                        <i class="tio-arrow-downward fs-12"></i>
                                    </div>
                                    <div class="mb-0">
                                        <label class="toggle-switch toggle-switch-sm mb-0" data-toggle="modal" data-target="#confirmation-modal-feature"> 
                                            <input type="checkbox" class="status toggle-switch-input" name="status" value="">
                                            <span class="toggle-switch-label text mb-0">
                                                <span class="toggle-switch-indicator">
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="view-details mt-20">
                            <div class="bg--secondary rounded px-20 py-25"> 
                                <div class="form-group m-0">
                                    <label for="" class="fs-14 mb-10px text-title">Google Analytics Measurement ID</label>
                                    <div class="flex-xs-wrap d-flex align-items-center gap-3">
                                        <textarea name="gs" rows="1" class="form-control" placeholder="Enter the GA Measurement ID"></textarea>
                                        <button type="submit" class="btn py-1 min-w-100px h-40px btn--primary ">Save</button>
                                    </div>             
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <form action="#0">
                <div class="card view-details-container">
                    <div class="card-body p-20">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="">
                                <h3 class="black-color mb-1 d-block">X (Twitter) Pixel</h3>
                                <p class="fs-12 text-c mb-1">To know more <a href="#0" class="theme-clr fs-12 font-semibold text-underline">Click Here.</a></p>
                            </div>
                            <div class="">
                                <div class="d-flex flex-sm-nowrap flex-wrap justify-content-end justify-content-end align-items-center gap-sm-3 gap-2">
                                    <div class="view-btn order-sm-0 order-3 fs-12 theme-clr cursor-pointer text-decoration-underline font-semibold d-flex align-items-center gap-1">
                                        View 
                                        <i class="tio-arrow-downward fs-12"></i>
                                    </div>
                                    <div class="mb-0">
                                        <label class="toggle-switch toggle-switch-sm mb-0" data-toggle="modal" data-target="#confirmation-modal-feature"> 
                                            <input type="checkbox" class="status toggle-switch-input" name="status" value="">
                                            <span class="toggle-switch-label text mb-0">
                                                <span class="toggle-switch-indicator">
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="view-details mt-20">
                            <div class="bg--secondary rounded px-20 py-25"> 
                                <div class="form-group m-0">
                                    <label for="" class="fs-14 mb-10px text-title">Google Analytics Measurement ID</label>
                                    <div class="flex-xs-wrap d-flex align-items-center gap-3">
                                        <textarea name="gs" rows="1" class="form-control" placeholder="Enter the GA Measurement ID"></textarea>
                                        <button type="submit" class="btn py-1 min-w-100px h-40px btn--primary ">Save</button>
                                    </div>             
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Confiramtion Feature Modal -->
<div class="modal shedule-modal fade" id="confirmation-modal-feature" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content pb-2 max-w-500">
            <div class="modal-header">
                <button type="button"
                    class="close bg-modal-btn w-30px h-30 rounded-circle position-absolute right-0 top-0 m-2 z-2"
                    data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center max-w-320 mx-auto">
                    <img src="{{asset('public/assets/admin/img/feature-status-on.png')}}" alt="icon" class="mb-3">
                    <h3 class="mb-2 px-xl-4">Turn ON Google Analytics</h3>
                    <p class="mb-0 fs-12">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam odio tellus, laoreet </p>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 gap-2">
                <button type="button" class="btn min-w-120px btn--reset" data-dismiss="modal">No</button>
                <button type="button" class="btn min-w-120px btn--primary">Yes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')

@endpush