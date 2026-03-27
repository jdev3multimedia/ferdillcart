@extends('layouts.admin.app')

@section('title',translate('messages.new_page'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">

<script type="text/javascript" src="{{asset('public/assets/admin/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/admin/js/daterangepicker.min.js')}}"></script>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="d-flex align-items-center mb-20 gap-2">
        <img width="22" height="22" src="{{ asset('public/assets/admin/img/d-withdraw.png') }}"
            alt="cencellation-icon">
        <h2 class="mb-0 fs-24 lh-base">Deliveryman Withdraw Transaction</h2>
    </div>
    <div class="card">
        <div class="card-header border-0 flex-wrap gap-2 p--20">
            <h4 class="title-clr m-0">Transaction History</h4>
            <div class="d-flex align-items-center flex-wrap gap-3">
                <form class="search-form">
                    <div class="input-group input--group">
                        <input name="search" type="search" class="form-control" placeholder="Search here..." value="">
                        <button type="submit" class="btn btn--primary"><i class="tio-search"></i></button>
                    </div>
                </form>
                <select name="transaction" id="" class="custom-select w-auto min-w-140 lh--12 h-40px">
                    <option value="all">All</option>
                    <option value="">...</option>
                    <option value="">...</option>
                    <option value="">...</option>
                    <option value="">...</option>
                </select>
                <div class="hs-unfold">
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
        <div class="table-responsive">
            <table class="table m-0 table-borderless table-thead-bordered table-nowrap table-align-middle">
                <thead class="bg-table-head">
                    
                </thead>
                <tbody>
                    <tr>
                        <td class="px-3 py-4 fs-14 title-clr font-medium">1</td>
                        <td class="px-3 py-4">
                            <div class="fs-14 title-clr ">
                                $745.34
                            </div>
                        </td>


                        <td >
                            <div class="fs-14 title-clr font-medium">
                                <span class="badge badge-soft-success">
                                    Approved
                                </span>
                            </div>
                        </td>
                        <td >

                                <a href="javascript:void(0)" class="btn btn-sm btn--primary btn-outline-primary action-btn offcanvas-trigger" data-target="#transaction_quick_view">
                                    <i class="tio-invisible"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-3 py-4 fs-14 title-clr font-medium">1</td>
                        <td class="px-3 py-4">
                            <div class="fs-14 title-clr ">
                                $745.34
                            </div>
                        </td>
                        <td class="px-3 py-4">
                            <div>
                                <a href="javascript:void(0)" class="d-flex align-items-center gap-2">
                                    <img width="40" height="40" src="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}" alt="img" class="w-40px h-40px rounded-circle">
                                    <div class="info">
                                        <div class="text-title fs-14">
                                            Steven Paull
                                        </div>
                                        <div class="color-334257B2 fs-12 font-light">
                                            0175459680
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </td>
                        <td class="px-3 py-4">
                            <div class="fs-14 title-clr ">
                                27 Nov 2023 01:46:pm
                            </div>
                        </td>
                        <td class="px-3 py-4">
                            <div class="fs-14 title-clr font-medium">
                                <span class="badge badge-soft-danger">
                                    Denied
                                </span>
                            </div>
                        </td>
                        <td class="px-3 py-4">
                            <div class="btn--container justify-content-center">
                                <a href="javascript:void(0)" class="btn btn-sm btn--primary btn-outline-primary action-btn offcanvas-trigger" data-target="#transaction_quick_view">
                                    <i class="tio-invisible"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-3 py-4 fs-14 title-clr font-medium">1</td>
                        <td class="px-3 py-4">
                            <div class="fs-14 title-clr ">
                                $745.34
                            </div>
                        </td>
                        <td class="px-3 py-4">
                            <div>
                                <a href="javascript:void(0)" class="d-flex align-items-center gap-2">
                                    <img width="40" height="40" src="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}" alt="img" class="w-40px h-40px rounded-circle">
                                    <div class="info">
                                        <div class="text-title fs-14">
                                            Steven Paull
                                        </div>
                                        <div class="color-334257B2 fs-12 font-light">
                                            0175459680
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </td>
                        <td class="px-3 py-4">
                            <div class="fs-14 title-clr ">
                                27 Nov 2023 01:46:pm
                            </div>
                        </td>
                        <td class="px-3 py-4">
                            <div class="fs-14 title-clr font-medium">
                                <span class="badge badge-soft-info">
                                    Pending
                                </span>
                            </div>
                        </td>
                        <td class="px-3 py-4">
                            <div class="btn--container justify-content-center">
                                <a href="javascript:void(0)" class="btn btn-sm btn--primary btn-outline-primary action-btn offcanvas-trigger" data-target="#transaction_quick_view">
                                    <i class="tio-invisible"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-3 py-4 fs-14 title-clr font-medium">1</td>
                        <td class="px-3 py-4">
                            <div class="fs-14 title-clr ">
                                $745.34
                            </div>
                        </td>
                        <td class="px-3 py-4">
                            <div>
                                <a href="javascript:void(0)" class="d-flex align-items-center gap-2">
                                    <img width="40" height="40" src="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}" alt="img" class="w-40px h-40px rounded-circle">
                                    <div class="info">
                                        <div class="text-title fs-14">
                                            Steven Paull
                                        </div>
                                        <div class="color-334257B2 fs-12 font-light">
                                            0175459680
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </td>
                        <td class="px-3 py-4">
                            <div class="fs-14 title-clr ">
                                27 Nov 2023 01:46:pm
                            </div>
                        </td>
                        <td class="px-3 py-4">
                            <div class="fs-14 title-clr font-medium">
                                <span class="badge badge-soft-success">
                                    Approved
                                </span>
                            </div>
                        </td>
                        <td class="px-3 py-4">
                            <div class="btn--container justify-content-center">
                                <a href="javascript:void(0)" class="btn btn-sm btn--primary btn-outline-primary action-btn offcanvas-trigger" data-target="#transaction_quick_view">
                                    <i class="tio-invisible"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Transaction Quick View Offcanvas -->
<div id="transaction_quick_view" class="custom-offcanvas custom-offcanvas__xs d-flex flex-column justify-content-between">
    <div>
        <form action="#0" method="post">
            <div
                class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                <h3 class="mb-0"></h2>
                <button type="button" class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0" aria-label="Close">
                    &times;
                </button>
            </div>
            <div class="custom-offcanvas-body p-20">
                <div class="mb-20 text-center">
                    <h3 class="text-title mb-3">Withdraw Information</h3>
                    <div class="d-flex align-items-center gap-1 flex-wrap justify-content-center mb-2">
                        <span class="fs-14 color-334257B2">Withdraw Amount:</span>
                        <span class="fs-14 font-semibold color-334257B2">$ 736.36</span>
                        <div class="btn bg-opacity-theme-10 py-1 fs-12 font-semibold px-2 rounded theme-border theme-clr">
                            Pending
                        </div>
                        {{--<span class="badge badge-soft-success">
                            Approved
                        </span>--}}
                        {{--<span class="badge badge-soft-danger">
                            Denied
                        </span>--}}
                    </div>
                    <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap color-334257B2 fs-12">
                        Request time:
                        27 Nov 2023 01:46:pm
                    </div>
                </div>
                <div class="card mb-20">
                    <div class="card-head font-semibold border-bottom text-title m-0 fs-12 py-xl-3 py-2 px-xxl-4 px-3">
                        Deliveryman Info
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2 mb-3">
                            <div class="d-flex  gap-2">
                                <div class="fs-12 w-60px before-cmn-style text-pragraph">Name</div>
                                <div class="fs-12 text-pragraph">Jon Doe P</div>
                            </div>
                            <div class="d-flex  gap-2">
                                <div class="fs-12 w-60px before-cmn-style text-pragraph">Email</div>
                                <div class="fs-12 text-title">pharmacy.store1@demo.com</div>
                            </div>
                            <div class="d-flex  gap-2">
                                <div class="fs-12 w-60px before-cmn-style text-pragraph">Phone</div>
                                <div class="fs-12 text-title">+101747410000</div>
                            </div>
                        </div>
                        <div class="bg-light rounded p-3 d-flex align-items-center gap-2 flex-wrap ">
                            <span class="color-334257B2 fs-12">Deliveryman Balance:</span>
                            <h4 class="m-0 text-primary fs-16">$ 363.64</h4>
                        </div>
                    </div>
                </div>
                <div class="card mb-20">
                    <div class="card-head font-semibold border-bottom text-title m-0 fs-12 py-xl-3 py-2 px-xxl-4 px-3">
                        Payment Info
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex  gap-2">
                                <div class="fs-12 w-60px before-cmn-style text-pragraph">Method</div>
                                <div class="fs-12 text-pragraph">Bkash</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="mb-2 text-title font-medium">Approved Note</h4>
                    <div class="bg-light rounded p-3 d-flex align-items-center gap-2 fs-14 flex-wrap ">
                        Store wallet adjustment partial
                    </div>
                </div>
                {{--<div>
                    <h4 class="mb-2 text-title font-medium">Denied Note</h4>
                    <div class="bg-light rounded p-3 d-flex align-items-center gap-2 fs-14 flex-wrap ">
                        Store wallet adjustment partial
                    </div>
                </div>--}}
            </div>
        </div>
    <div class="offcanvas-footer py-3 px-sm-4 px-3 d-flex align-items-center justify-content-center gap-3">
        <button type="reset" class="btn w-100 bg--soft-danger-10 text-danger fs-14 fw-medium h--40px">{{ translate('messages.Deny') }}</button>
        <button type="submit" class="btn w-100 btn--primary h--40px">{{ translate('messages.Approve') }}</button>
    </div>
    </form>
</div>
<div id="offcanvasOverlay" class="offcanvas-overlay"></div>
<!-- Transaction Quick View Offcanvas End -->

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
                    <p class="mb-0 fs-12">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam odio tellus,
                        laoreet </p>
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
