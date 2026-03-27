@extends('layouts.admin.app')

@section('title',translate('messages.Delivery Man Preview'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title text-break">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/delivery-man.png')}}" class="w--26" alt="">
            </span>
            <span>Deliveryman Preview</span>
        </h1>        

        <div class="">
            <div class="js-nav-scroller hs-nav-scroller-horizontal mt-3">
                <!-- Nav -->
                <ul class="nav nav-tabs nav--pills mb-3 border-0 nav--tabs">
                    <li class="nav-item">
                        <a class="nav-link " href="http://localhost/Backend-6amMart/admin/users/delivery-man/preview/6/info" aria-disabled="true">Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://localhost/Backend-6amMart/admin/users/delivery-man/preview/6/transaction" aria-disabled="true">Transaction</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="http://localhost/Backend-6amMart/admin/users/delivery-man/preview/6/order_list" aria-disabled="true">Order list</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="http://localhost/Backend-6amMart/admin/users/delivery-man/preview/6/conversation" aria-disabled="true">Conversations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="http://localhost/Backend-6amMart/admin/users/delivery-man/preview/6/disbursement" aria-disabled="true">Disbursements</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="http://localhost/Backend-6amMart/admin/users/delivery-man/preview/6/disbursement" aria-disabled="true">Loyalty Point</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="http://localhost/Backend-6amMart/admin/users/delivery-man/preview/6/disbursement" aria-disabled="true">Refer & Earn</a>
                    </li>
                </ul>
                <!-- End Nav -->
            </div>
        </div>
    </div>
    <!-- End Page Header -->


    <div class="card mb-20">
        <div class="card-body">
            <div class="row g-xxl-4 g-3">
                <div class="col-sm-6 col-lg-4">
                    <div class="card_earning-box card--bg-4 rounded-10 h-100">
                        <div class="box d-flex align-items-center">
                            <div class="icon w-60px h-60px rounded-circle d-center bg-white">
                                <img src="{{asset('public/assets/admin/img/e-referral-code.png')}}" class="w--26" alt="">
                            </div>
                            <div>
                                <div class="mb-1 d-flex align-items-center gap-2">
                                    <h3 class=" text-danger-dark mb-0 fs-18 code__copy max-w-150 line--limit-1">{{ translate('messages.H9FJ8F7KJ') }}</h3>
                                    <button type="button" class="btn p-0 m-0 outline-0">
                                        <i class="tio-copy theme-clr fs-16"></i>
                                    </button>
                                </div>
                                <p class="text-dark fs-14 mb-0">{{ translate('messages.Referral Code') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card_earning-box card--bg-3 rounded-10 h-100">
                        <div class="box d-flex align-items-center">
                            <div class="icon w-60px h-60px rounded-circle d-center bg-white">
                                <img src="{{asset('public/assets/admin/img/e-referred-total.png')}}" class="w--26" alt="">
                            </div>
                            <div>
                                <h3 class="text-00AA6D mb-1 fs-26">{{ translate('messages.20') }}</h3>
                                <p class="text-dark fs-14 mb-0">{{ translate('messages.Total Referred') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card_earning-box color-card color-5 rounded-10 h-100">
                        <div class="box d-flex align-items-center">
                            <div class="icon w-60px h-60px rounded-circle d-center bg-white">
                                <img src="{{asset('public/assets/admin/img/e-referral-earned.png')}}" class="w--26" alt="">
                            </div>
                            <div>
                                <h3 class="title mb-1 fs-26">{{ translate('messages.$15.00') }}</h3>
                                <p class="text-dark fs-14 mb-0">{{ translate('messages.Referral Earned') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <div class="card">
        <div class="card-header flex-wrap pt-3 pb-3 border-0 gap-2">
            <div class="search--button-wrapper mr-1">
                <h4 class="card-title fs-16 text-dark">{{ translate('messages.Refer & Earn History')}}</h4>
                <form class="search-form min--260">
                    <div class="input-group input--group">
                        <input id="datatableSearch_" type="search" name="search" class="form-control h--40px" placeholder="Search Keywords" value="" aria-label="Search" tabindex="1">

                        <button type="submit" class="btn btn--secondary bg-modal-btn"><i class="tio-search text-muted"></i></button>
                    </div>
                </form>
                <button type="button" class="btn btn--primary h-40px btn-outline-primary py-2 offcanvas-trigger"  data-target="#transaction__list">
                    <span class="dot-status d-center position-absolute p-0 rounded-circle bg-white"><i class="tio-circle text-danger fs-12"></i></span>
                    <i class="tio-tune-horizontal"></i> 
                    {{ translate('messages.Filter') }}
                </button>
            </div>
            <!-- Unfold -->
            <div class="hs-unfold">
                <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40" href="javascript:;"
                    data-hs-unfold-options='{
                            "target": "#usersExportDropdown",
                            "type": "css-animation"
                        }'>
                    <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                </a>

                <div id="usersExportDropdown"
                    class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                    <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                    <a id="export-excel" class="dropdown-item" href="">
                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                            src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                            alt="Image Description">
                        {{ translate('messages.excel') }}
                    </a>
                    <a id="export-csv" class="dropdown-item" href="">
                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                            src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                            alt="Image Description">
                        .{{ translate('messages.csv') }}
                    </a>
                </div>
            </div>
            <!-- End Unfold -->
        </div>
        <div class="card-body p-0">
            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table class="table table-border table-thead-borderless table-align-middle table-nowrap card-table m-0">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0 text-center">SL</th>
                            <th class="border-0">Transaction ID</th>
                            <th class="border-0">Date</th>
                            <th class="border-0 text-center">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">1</td>
                            <td>
                                <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                    84Ed788EFG7986
                                </div>
                            </td>
                            <td>
                                <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                    29 Sep 2022
                                </div>
                            </td>
                            <td>
                                <div class="text-center text-title">
                                   $ 376
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">1</td>
                            <td>
                                <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                    84Ed788EFG7986
                                </div>
                            </td>
                            <td>
                                <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                    29 Sep 2022
                                </div>
                            </td>
                            <td>
                                <div class="text-center text-title">
                                   $ 376
                                </div>
                            </td>
                        </tr> 
                        <tr>
                            <td class="text-center">1</td>
                            <td>
                                <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                    84Ed788EFG7986
                                </div>
                            </td>
                            <td>
                                <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                    29 Sep 2022
                                </div>
                            </td>
                            <td>
                                <div class="text-center text-title">
                                   $ 376
                                </div>
                            </td>
                        </tr> 
                        <tr>
                            <td class="text-center">1</td>
                            <td>
                                <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                    84Ed788EFG7986
                                </div>
                            </td>
                            <td>
                                <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                    29 Sep 2022
                                </div>
                            </td>
                            <td>
                                <div class="text-center text-title">
                                   $ 376
                                </div>
                            </td>
                        </tr> 
                        <tr>
                            <td class="text-center">1</td>
                            <td>
                                <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                    84Ed788EFG7986
                                </div>
                            </td>
                            <td>
                                <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                    29 Sep 2022
                                </div>
                            </td>
                            <td>
                                <div class="text-center text-title">
                                   $ 376
                                </div>
                            </td>
                        </tr>                                         
                    </tbody>
                </table>
                <div class="page-area border-top px-3 pt-3 pb-2 d-flex align-items-center gap-3 justify-content-between flex-wrap">
                    <div class="d-flex align-items-center gap-2 flex-wrap gap-2">
                        <select name="" id="" class="custom-select color-656566 fs-12 w-auto bg--secondary rounded py-0 h-auto" tabindex="10">
                            <option value="">20 Items</option>
                            <option value="">20 Items</option>
                            <option value="">2 Items</option>
                            <option value="">9 Items</option>
                        </select>
                        <p class="color-A7A7A7 fs-12 m-0">Showing 1 To 20 Of 100 Records</p>
                    </div>
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
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>   

</div>


<div id="transaction__list" class="custom-offcanvas d-flex flex-column justify-content-between">
    <div>
        <form action="#0" method="post">
            <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                <h3 class="mb-0">{{ translate('messages.Filter') }}</h2>
                    <button type="button"
                        class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"
                        aria-label="Close">&times;</button>
            </div>
            <div class="custom-offcanvas-body p-20">

                <div class="bg--secondary rounded p-20 mb-20">
                    <div class="d-flex flex-column gap-lg-4 gap-3">
                        <div>
                            <span class="mb-2 d-block title-clr fw-normal">Duration</span>
                            <select id="date_range_type" class="custom-select custom-select-color border rounded w-100">
                                <option value="All_Time">All Time</option>
                                <option value="this_week">This Week</option>
                                <option value="this_month">This Month</option>
                                <option value="this_year">This Year</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>

                        <div id="date_range" class="d-none">
                            <label class="form-label">Start Date</label>
                            <div class="position-relative">
                                <i class="tio-calendar-month icon-absolute-on-right"></i>
                                <input type="text" name="dates" class="form-control h-45 position-relative bg-white" placeholder="Select Date">
                            </div>
                        </div>
                    </div>    
                </div>
            </div>
    </div>
    <div class="offcanvas-footer p-3 d-flex align-items-center justify-content-center gap-3">
        <button type="reset" class="btn w-100 btn--reset h--40px">{{ translate('messages.reset') }}</button>
        <button type="submit" class="btn w-100 btn--primary h--40px">{{ translate('messages.Filter') }}</button>
    </div>
    </form>
</div>
<div id="offcanvasOverlay" class="offcanvas-overlay"></div>


@endsection

