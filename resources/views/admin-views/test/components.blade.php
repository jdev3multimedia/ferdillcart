@extends('layouts.admin.app')

@section('title',translate('messages.new_page'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">

<script type="text/javascript" src="{{asset('public/assets/admin/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/admin/js/daterangepicker.min.js')}}"></script>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="row g-3">
        <div class="col-md-3 col-sm-6">
            <div class="bg-light rounded p-3 h-100">
                <h1 class="mb-2 text-capitalize">Modal</h1>
                <div class="btn--container">
                    <button type="button" class="btn btn--primary" data-toggle="modal"
                        data-target="#confirmation-modal-btn">
                        Confirmation Modal
                    </button>
                    <button type="button" class="btn btn--primary" data-toggle="modal"
                        data-target="#confirmation-modal-feature">
                        Confirmation Modal Feature
                    </button>
                    <button type="button" class="btn btn--primary" data-toggle="modal"
                        data-target="#confirmation-reason-btn">
                        Confirmation Reason Modal Feature
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="bg-light rounded p-3 h-100">
                <h1 class="mb-2 text-capitalize">Button</h1>
                <div class="btn--container">
                    <a href="#0" class="btn action-btn btn-outline-theme-light">
                        <i class="tio-edit"></i>
                    </a>
                    <a class="btn action-btn btn--danger btn-outline-danger" href="#0">
                        <i class="tio-delete-outlined"></i>
                    </a>
                    <a class="btn action-btn btn-outline-theme-dark" href="#0">
                        <i class="tio-edit"></i>
                    </a>
                    <a class="btn action-btn btn--primary btn-outline-primary" href="#0">
                        <i class="tio-edit"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="bg-light rounded p-3 h-100">
                <h1 class="mb-2 text-capitalize">pagination</h1>
                <div class="page-area">
                    <nav>
                        <ul class="pagination m-0">
                            <li class="page-item disabled" aria-disabled="true" aria-label="« Previous">
                                <span class="page-link" aria-hidden="true"><i class="tio-chevron-left"></i></span>
                            </li>
                            <li class="page-item active" aria-current="page"><span class="page-link">1</span></li>
                            <li class="page-item"><a class="page-link" href="">2</a></li>
                            <li class="page-item">
                                <a class="page-link" href="" rel="next" aria-label="Next »"><i class="tio-chevron-right"></i></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="bg-light rounded p-3 h-100">
                <h1 class="mb-2 text-capitalize">pagination</h1>
                <div class="d-flex aign-items-center gap-4">
                    <p class="text-dark m-0 lh-1">1-5 of 13</p>
                    <div class="d-flex align-items-center gap-3">
                        <a class="text-dark fs-16 disabled" href=""><i class="tio-chevron-left"></i></a>
                        <a class="text-dark fs-16" href=""><i class="tio-chevron-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="bg-light rounded p-3 h-100">
                <h1 class="mb-2 text-capitalize">pagination</h1>
                <div class="d-flex align-items-center gap-24 flex-wrap">
                    <div class="d-flex aign-items-center gap-4">
                        <p class="text-dark m-0 lh-1">1-5 of 13</p>
                        <div class="d-flex align-items-center gap-3">
                            <a class="text-dark fs-16 disabled" href=""><i class="tio-chevron-left"></i></a>
                            <a class="text-dark fs-16" href=""><i class="tio-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="page-area">
                        <nav>
                            <ul class="pagination m-0">
                                <li class="page-item disabled" aria-disabled="true" aria-label="« Previous">
                                    <span class="page-link" aria-hidden="true"><i class="tio-chevron-left"></i></span>
                                </li>
                                <li class="page-item active" aria-current="page"><span class="page-link">1</span></li>
                                <li class="page-item"><a class="page-link" href="">2</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="" rel="next" aria-label="Next »"><i class="tio-chevron-right"></i></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="bg-light rounded p-3 h-100">
                <h1 class="mb-2 text-capitalize">Search With Icon</h1>
                <form class="search-form">
                    <div class="input-group input--group">
                        <input name="search" type="search" class="form-control" placeholder="Search by Vendor name, owner info..." value="">
                        <button type="submit" class="btn btn--primary"><i class="tio-search"></i></button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="bg-light rounded p-3 h-100">
                <h1 class="mb-2 text-capitalize">Export Button</h1>
                <div class="hs-unfold mr-2">
                    <a class="js-hs-unfold-invoker btn btn-sm btn-white text-title font-medium dropdown-toggle min-height-40" href="javascript:;"
                        data-hs-unfold-options='{
                                "target": "#usersExportDropdown",
                                "type": "css-animation"
                            }'>
                        <i class="tio-download-to mr-1 text-title"></i> {{ translate('messages.export') }}
                    </a>
                    <div id="usersExportDropdown"
                        class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                        <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                        <a id="export-excel" class="dropdown-item" href="{{route('admin.business-settings.module.export', ['type'=>'excel',request()->getQueryString()])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                alt="Image Description">
                            {{ translate('messages.excel') }}
                        </a>
                        <a id="export-csv" class="dropdown-item" href="{{route('admin.business-settings.module.export', ['type'=>'csv',request()->getQueryString()])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                alt="Image Description">
                            .{{ translate('messages.csv') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>




<!-- Confiramtion Modal -->
<div class="modal shedule-modal fade" id="confirmation-modal-btn" tabindex="-1" aria-labelledby="exampleModalLabel"
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
                <div class="text-center">
                    <img src="{{asset('public/assets/admin/img/delete-confirmation.png')}}" alt="icon" class="mb-3">
                    <h3 class="mb-2">Are you sure?</h3>
                    <p class="mb-0">You want to sent notification to the customer.</p>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 gap-2">
                <button type="button" class="btn min-w-120px btn--reset" data-dismiss="modal">No</button>
                <button type="button" class="btn min-w-120px btn--primary">Yes</button>
            </div>
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
                    <h3 class="mb-2 px-xl-4">Do you want to
                    Featured this category?</h3>
                    <p class="mb-0 fs-12">If you turn on this category as a featured category it will show in customer app landing page.</p>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 gap-2">
                <button type="button" class="btn min-w-120px btn--reset" data-dismiss="modal">No</button>
                <button type="button" class="btn min-w-120px btn--primary">Yes</button>
            </div>
        </div>
    </div>
</div>


<!-- Confiramtion Reason Modal -->
<div class="modal shedule-modal fade" id="confirmation-reason-btn" tabindex="-1" aria-labelledby="exampleModalLabel"
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
                <div class="text-center">
                    <img src="{{asset('public/assets/admin/img/delete-confirmation.png')}}" alt="icon" class="mb-3">
                    <h3 class="mb-2">Are you sure?</h3>
                    <p class="mb-0">You want to sent notification to the customer.</p>
                </div>
                <div class="px-3 mt-4">
                    <h5 class="mb-2">Reason</h5>
                    <textarea name="reason_from" id="" class="form-control" rows="2" placeholder="Type here the denied reason..."></textarea>
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