@extends('layouts.admin.app')

@section('title',translate('messages.new_page'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">

    <h3 class="mb-20">Surge Price</h3>
    
    <div class="card">
        <!-- Header -->
        <div class="card-header border-0 py-2">
            <div class="search--button-wrapper">
                <h4 class="mr-auto mb-0">Surge Price List</h4>
                <form class="search-form">
                    <div class="input-group input--group">
                        <input id="datatableSearch" name="search" type="search" class="form-control"
                            placeholder="Ex : Search Module by Name" aria-label="Search here" value="">
                        <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                    </div>
                </form>
                <div class="hs-unfold mr-2">
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
                <a href="#0" class="btn btn--primary">Create Surge Price</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive p-0 datatable-custom">
                <table id="columnSearchDatatable" class="table m-0 table-borderless table-thead-bordered table-align-middle">
                    <thead class="thead-light border-0">
                        <tr>
                            <th class="border-0 fs-14">Sl</th>
                            <th class="border-0 fs-14">Title</th>
                            <th class="border-0 fs-14">
                               <div class="min-w-135px">
                                   Selected Module
                               </div>
                            </th>
                            <th class="border-0 fs-14">
                               <div class="min-w-135px">
                                   Price Increase Rate
                               </div>
                            </th>
                            <th class="border-0 fs-14">
                               <div class="min-w-135px">
                                   Surge Price Schedule
                               </div>
                            </th>
                            <th class="border-0 fs-14">Duration</th>
                            <th class="border-0 fs-14">Status</th>
                            <th class="border-0 fs-14 text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody id="table-div">
                        <tr>
                            <td class="pl-4">1</td>
                            <td>Dhaka Zone</td>
                            <td>
                                <span class="d-block text-limit-2 max-w-220px">
                                   Food, Shop, Pharmacy, Grocery, Parcel, Rental
                                </span>
                            </td>
                            <td>
                                15%
                            </td>
                            <td>
                                Weekly
                            </td>
                            <td>
                                <span class="d-block max-w-220px min-w-176px">
                                    <span class="d-block text-title">8:00 am - 10:00 am</span>
                                    <span>
                                        15 May 2025 to 30 May 2025
                                        Sunday, Monday, Thrusday
                                    </span>
                                </span>
                            </td>
                            <td>
                                <label class="toggle-switch toggle-switch-sm" for="status">
                                    <input type="checkbox" class="toggle-switch-input" class="toggle-switch-input" id="status">
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a href="#0" class="btn action-btn btn-outline-theme-dark">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <a href="#0" class="btn action-btn btn-outline-theme-light">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="btn action-btn btn--danger btn-outline-danger" href="#0" data-toggle="modal" data-target="#exampleModal">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-4">2</td>
                            <td>Mirpur Zone</td>
                            <td>
                                <span class="d-block text-limit-2 max-w-220px">
                                   Food, Shop, Pharmacy, Grocery, Parcel, Rental
                                </span>
                            </td>
                            <td>
                                5%
                            </td>
                            <td>
                                Daily
                            </td>
                            <td>
                                <span class="d-block max-w-220px min-w-176px">
                                    <span class="d-block text-title">8:00 am - 10:00 am</span>
                                    <span>
                                        15 May 2025 to 30 May 2025
                                    </span>
                                </span>
                            </td>
                            <td>
                                <label class="toggle-switch toggle-switch-sm" for="status">
                                    <input type="checkbox" class="toggle-switch-input" class="toggle-switch-input" id="status">
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a href="#0" class="btn action-btn btn-outline-theme-dark">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <a href="#0" class="btn action-btn btn-outline-theme-light">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="btn action-btn btn--danger btn-outline-danger" href="#0">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-4">3</td>
                            <td>Banani Zone</td>
                            <td>
                                <span class="d-block text-limit-2 max-w-220px">
                                   Food, Pharmacy
                                </span>
                            </td>
                            <td>
                                3%
                            </td>
                            <td>
                                Weekly
                            </td>
                            <td>
                                <span class="d-block max-w-220px min-w-176px">
                                    <span class="d-block text-title">8:00 am - 10:00 am</span>
                                    <span>
                                        15 May 2025 to 30 May 2025
                                    </span>
                                </span>
                            </td>
                            <td>
                                <label class="toggle-switch toggle-switch-sm" for="status">
                                    <input type="checkbox" class="toggle-switch-input" class="toggle-switch-input" id="status">
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a href="#0" class="btn action-btn btn-outline-theme-dark">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <a href="#0" class="btn action-btn btn-outline-theme-light">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="btn action-btn btn--danger btn-outline-danger" href="#0" data-toggle="modal" data-target="#exampleModal">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-4">4</td>
                            <td>Uttara Zone</td>
                            <td>
                                <span class="d-block text-limit-2 max-w-220px">
                                   Shop, Pharmacy, Parcel, Rental
                                </span>
                            </td>
                            <td>
                                4%
                            </td>
                            <td>
                                Weekly
                            </td>
                            <td>
                                <span class="d-block max-w-220px min-w-176px">
                                    <span class="d-block text-title">8:00 am - 10:00 am</span>
                                    <span>
                                        15 May 2025 to 30 May 2025
                                        Sunday, Monday, Thrusday
                                    </span>
                                </span>
                            </td>
                            <td>
                                <label class="toggle-switch toggle-switch-sm" for="status">
                                    <input type="checkbox" class="toggle-switch-input" class="toggle-switch-input" id="status">
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a href="#0" class="btn action-btn btn-outline-theme-dark">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <a href="#0" class="btn action-btn btn-outline-theme-light">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="btn action-btn btn--danger btn-outline-danger" href="#0">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-4">5</td>
                            <td>Dhaka Zone</td>
                            <td>
                                <span class="d-block text-limit-2 max-w-220px">
                                   Food, Shop, Pharmacy, Grocery, Parcel, Rental
                                </span>
                            </td>
                            <td>
                                15%
                            </td>
                            <td>
                                Weekly
                            </td>
                            <td>
                                <span class="d-block max-w-220px min-w-176px">
                                    <span class="d-block text-title">8:00 am - 10:00 am</span>
                                    <span>
                                        15 May 2025 to 30 May 2025
                                        Sunday, Monday, Thrusday
                                    </span>
                                </span>
                            </td>
                            <td>
                                <label class="toggle-switch toggle-switch-sm" for="status">
                                    <input type="checkbox" class="toggle-switch-input" class="toggle-switch-input" id="status">
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a href="#0" class="btn action-btn btn-outline-theme-dark">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <a href="#0" class="btn action-btn btn-outline-theme-light">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="btn action-btn btn--danger btn-outline-danger" href="#0" data-toggle="modal" data-target="#exampleModal">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer page-area pt-0 border-0">
            <div class="d-flex justify-content-center justify-content-sm-end">
                <!-- Pagination -->
            </div>
        </div>
    </div>

</div>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content pb-4">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <img src="{{ asset('public/assets/admin/img/delete.png') }}" class="mb-20" alt="">
        <h3 class="title-clr mb-2">Want to Delete this surge Price?</h3>
        <p class="fz--14px max-w-400px mx-auto">Are you sure you want to delete this surge Price & remove it permanently?</p>
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