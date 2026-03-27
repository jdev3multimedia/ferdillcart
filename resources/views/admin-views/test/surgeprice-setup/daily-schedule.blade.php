@extends('layouts.admin.app')

@section('title',translate('messages.new_page'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">

<script type="text/javascript" src="{{asset('public/assets/admin/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/admin/js/daterangepicker.min.js')}}"></script>
@endpush

@section('content')
<div class="content container-fluid">

    <h3 class="mb-20">Create New Surge Price</h3>
    <form action="#0">
        <div class="card mb-20">
            <div class="card-header">
                <h4 class="mb-0">Basic Setup</h4>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link lang_link active" href="#" id="default-link">{{translate('messages.default')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link lang_link" href="#" id="">English(EN)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link lang_link" href="#" id="">Arabic(SA)</a>
                    </li>
                </ul>
                <div class="form-group">
                    <label class="input-label" for="surgePrice_default">{{translate('messages.Surge Price name (Default)')}}
                        <span class="input-label-secondary text--title" data-toggle="tooltip" data-placement="right"
                            data-original-title="{{ translate('Surge Price name (Default)') }}">
                            <i class="tio-info fs-14 text-muted"></i>
                        </span>
                    </label>
                    <input type="text" placeholder="Type surge price name" class="form-control">
                </div>
                <div class="form-group mb-0">
                    <div class="d-flex align-items-center gap-1 flex-wrap justify-content-between mb-2">
                        <label class="input-label mb-0" for="surgePrice_default">{{translate('messages.Note for Customer (Default)')}}
                            <span class="input-label-secondary text--title" data-toggle="tooltip" data-placement="right"
                                data-original-title="{{ translate('Note for Customer (Default)') }}">
                                <i class="tio-info fs-14 text-muted"></i>
                            </span>
                        </label>
                        <label class="toggle-switch toggle-switch-sm" for="customerDefault">
                            <input type="checkbox" class="toggle-switch-input" id="customerDefault" checked>
                            <span class="toggle-switch-label">
                                <span class="toggle-switch-indicator"></span>
                            </span>
                        </label>
                    </div>
                    <input type="text" placeholder="Type note for customer" class="form-control">
                    <span class="text-right d-block mt-1">Character count 0/50</span>
                </div>
            </div>
        </div>
        <div class="card mb-20">
            <div class="card-header">
                <h4 class="mb-0">Module & Surge Price Setup</h4>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div>
                            <label class="mb-2 d-block title-clr fw-normal">{{ translate('Module') }}</label>
                            <select name="" id="module_selected" class="form-control h--45px js-select2-custom" multiple="multiple" placeholder="Module Select" data-placeholder="Module">
                                <option></option>
                                <option>{{ translate('messages.Food') }}</option>
                                <option>{{ translate('messages.Pharmacy') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="mb-2 d-block title-clr fw-normal">{{ translate('Price Increase Rate') }}</label>
                            <div class="custom-group-btn border">
                                <div class="flex-sm-grow-1 w-100">
                                    <input id="discount_input" type="number" name="discount_price" class="form-control border-0 pl-unset"
                                            placeholder="Ex: 5" min="0" step="0.001" value="">
                                </div>
                                <div class="flex-shrink-0">
                                    <select name="price_ncreaseRate" id="price_ncreaseRate" class="custom-select ltr border-0">
                                        <option value="percent" {{ old('price_ncreaseRate') == 'percent' ? 'selected' : '' }}>%</option>
                                        <option value="amount" {{ old('price_ncreaseRate') == 'amount' ? 'selected' : '' }}>
                                            {{ \App\CentralLogics\Helpers::currency_symbol() }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="space-active mb-20">
            <div class="card mb-20 shedule-checkbox_wrapper">
                <div class="card-header">
                    <h4 class="mb-0">Duration Setup</h4>
                </div>
                <div class="card-body">
                    <div class="form-group w-100 mb-20">
                        <label class="input-label" for="">Surge Price Schedule Type</label>
                        <div class="resturant-type-group shedule-checkbox-inner flex-md-nowrap border">
                            <label class="form-check w-100 form--check mr-2 mr-md-4">
                                <input class="form-check-input" type="radio" value="1" name="shedules_status" checked>
                                <span class="form-check-label">Daily Schedule</span>
                            </label>
                            <label class="form-check w-100 form--check mr-2 mr-md-4">
                                <input class="form-check-input" type="radio" value="0" name="shedules_status">
                                <span class="form-check-label">Weekly Schedule</span>
                            </label>
                            <label class="form-check w-100 form--check mr-2 mr-md-4">
                                <input class="form-check-input" type="radio" value="0" name="shedules_status">
                                <span class="form-check-label">Custom Schedule</span>
                            </label>
                        </div>
                    </div>
                    <div class="bg-light p-20 p-mobile-0 bg-mobile-transparent">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div>
                                    <h5 class="mb-1">Duration Setup</h5>
                                    <p class="fs-12 m-0">Select your suitable time within a time range you want add surge price</p>
                                </div>
                                <div class="bg--3 rounded px-3 py-2 d-inline-flex align-items-start gap-2 flex-wrap mt-20">
                                    <i class="tio-warning text-danger"></i>
                                    <p class="m-0 max-w-353px">{{ translate('This surge price overlaps with another. Please change the module or reschedule to fix it.') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="change-shedule-wrapper">
                                    <!-- Daily Schedule -->
                                    <div class="shedule_item">
                                        <div class="bg-white p-sm-3 d-flex flex-column gap-3">
                                            <div>
                                                <label class="form-label">{{ translate('Date Range') }}</label>
                                                <div class="position-relative date-range__custom">
                                                    <i class="tio-calendar-month icon-absolute-on-right"></i>
                                                    <input type="text" class="form-control h-45 position-relative bg-transparent"   name="dates-top" placeholder="{{ translate('messages.Select_Date') }}">
                                                </div>
                                            </div>
                                            <div class="time-range-wrapper">
                                                <label class="form-label">{{ translate('Time Range') }}</label>
                                                <div class="position-relative cursor-pointer">
                                                    <i class="tio-time icon-absolute-on-right"></i>  
                                                     <input type="text" class="form-control h-45 position-relative bg-transparent time-range-picker" name="time_range[]" placeholder="{{ translate('messages.Select_Time') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Weekly Schedule -->
                                     <div class="shedule_item">
                                         <div class="bg-white p-sm-3 d-flex flex-column gap-3">
                                             <div class="cursor-pointer" data-toggle="modal" data-target="#weeklySelectDays_btn">
                                                 <label class="form-label">{{ translate('Date Range') }}</label>
                                                 <div class="position-relative date-range__custom">
                                                     <i class="tio-calendar-month icon-absolute-on-right"></i>
                                                     <input type="text" class="form-control h-45 position-relative bg-transparent date-range-input-demo"
                                                         name="dates[]" placeholder="{{ translate('messages.Select_Date') }}">
                                                 </div>
                                             </div>
                                             <p class="fs-12 m-0">Every week from <span class="font-semibold">Sunday & Monday</span></p>
                                             <div class="time-range-wrapper">
                                                 <label class="form-label">{{ translate('Time Range') }}</label>
                                                 <div class="position-relative cursor-pointer">
                                                     <i class="tio-time icon-absolute-on-right"></i>  
                                                      <input type="text" class="form-control h-45 position-relative bg-transparent time-range-picker" name="time_range[]" placeholder="{{ translate('messages.Select_Time') }}">
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                    <!-- Custom Schedule -->
                                     <div class="shedule_item">
                                         <div class="bg-white p-sm-3 d-flex flex-column gap-3">
                                             <div class="cursor-pointer" data-toggle="modal" data-target="#surgeCustom_sheduleBtn">
                                                 <label class="form-label">{{ translate('Date & Time Select') }}</label>
                                                 <div class="position-relative">
                                                     <i class="tio-calendar-month icon-absolute-on-right"></i>
                                                     <input type="text" class="form-control h-45 position-relative bg-transparent"
                                                         name="" placeholder="{{ translate('messages.5 Days Repeated') }}">
                                                 </div>
                                             </div>
                                             <p class="fs-12 m-20">Date range <span class="font-semibold">4 Jan, 2022</span> to <span class="font-semibold">21 Jan, 2024</span></p>
                                             <div class="table-responsive p-0 date-table">
                                                 <table id="columnSearchDatatable" class="table m-0 table-borderless table-thead-bordered table-align-middle">
                                                     <thead class="thead-light border-0">
                                                         <tr>
                                                             <th class="border-0 fs-14">Sl</th>
                                                             <th class="border-0 fs-14">Title</th>
                                                             <th class="border-0 fs-14 text-center">Action</th>
                                                         </tr>
                                                     </thead>
                                                     <tbody>
                                                         <tr>
                                                             <td class="pl-4">1</td>
                                                             <td>
                                                                 <span class="d-block max-w-220px min-w-176px">
                                                                     <span class="d-block text-title">Thu, Jan 04</span>
                                                                     <span>
                                                                         11.00 AM - 11.00 AM
                                                                     </span>
                                                                 </span>
                                                             </td>
                                                             <td>
                                                                 <div class="btn--container justify-content-center">
                                                                     <a href="#0" class="btn action-btn btn-outline-theme-light" data-toggle="modal" data-target="#timeRange_btn">
                                                                         <i class="tio-edit"></i>
                                                                     </a>
                                                                     <a class="btn action-btn btn--danger btn-outline-danger" href="#0">
                                                                         <i class="tio-delete-outlined"></i>
                                                                     </a>
                                                                 </div>
                                                             </td>
                                                         </tr>
                                                         <tr>
                                                             <td class="pl-4">2</td>
                                                             <td>
                                                                 <span class="d-block max-w-220px min-w-176px">
                                                                     <span class="d-block text-title">Thu, Jan 04</span>
                                                                     <span>
                                                                         11.00 AM - 11.00 AM
                                                                     </span>
                                                                 </span>
                                                             </td>
                                                             <td>
                                                                 <div class="btn--container justify-content-center">
                                                                     <a href="#0" class="btn action-btn btn-outline-theme-light" data-toggle="modal" data-target="#timeRange_btn">
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
                                                             <td>
                                                                 <span class="d-block max-w-220px min-w-176px">
                                                                     <span class="d-block text-title">Thu, Jan 04</span>
                                                                     <span>
                                                                         11.00 AM - 11.00 AM
                                                                     </span>
                                                                 </span>
                                                             </td>
                                                             <td>
                                                                 <div class="btn--container justify-content-center">
                                                                     <a href="#0" class="btn action-btn btn-outline-theme-light" data-toggle="modal" data-target="#timeRange_btn">
                                                                         <i class="tio-edit"></i>
                                                                     </a>
                                                                     <a class="btn action-btn btn--danger btn-outline-danger" href="#0">
                                                                         <i class="tio-delete-outlined"></i>
                                                                     </a>
                                                                 </div>
                                                             </td>
                                                         </tr>
                                                         <tr>
                                                             <td class="pl-4">4</td>
                                                             <td>
                                                                 <span class="d-block max-w-220px min-w-176px">
                                                                     <span class="d-block text-title">Thu, Jan 04</span>
                                                                     <span>
                                                                         11.00 AM - 11.00 AM
                                                                     </span>
                                                                 </span>
                                                             </td>
                                                             <td>
                                                                 <div class="btn--container justify-content-center">
                                                                     <a href="#0" class="btn action-btn btn-outline-theme-light" data-toggle="modal" data-target="#timeRange_btn">
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
                                                             <td>
                                                                 <span class="d-block max-w-220px min-w-176px">
                                                                     <span class="d-block text-title">Thu, Jan 04</span>
                                                                     <span>
                                                                         11.00 AM - 11.00 AM
                                                                     </span>
                                                                 </span>
                                                             </td>
                                                             <td>
                                                                 <div class="btn--container justify-content-center">
                                                                     <a href="#0" class="btn action-btn btn-outline-theme-light" data-toggle="modal" data-target="#timeRange_btn">
                                                                         <i class="tio-edit"></i>
                                                                     </a>
                                                                     <a class="btn action-btn btn--danger btn-outline-danger" href="#0">
                                                                         <i class="tio-delete-outlined"></i>
                                                                     </a>
                                                                 </div>
                                                             </td>
                                                         </tr>
                                                     </tbody>
                                                 </table>
                                             </div>
                                         </div>
                                     </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn--container justify-content-end mt-20">
            <button type="reset" id="reset_btn" class="btn btn--reset min-w-120px">Reset</button>
            <button type="submit" class="btn btn--primary min-w-120px">Submit</button>
        </div>

       
    </form>




</div>

<!-- Weekly Schedule Select Days Modal -->
<div class="modal shedule-modal fade" id="weeklySelectDays_btn" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content pb-1">
      <div class="modal-header">
        <div>
            <h3 class="title-clr mb-0">Select Days</h3>
            <p class="fz-12 m-0">Your Surge price active date</p>
        </div>
        <button type="button" class="close bg-light w-30px h-30 rounded-circle" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal-body-inner">
            <div class="resturant-type-group bg-light rounded p-3 mb-3 gap-4">
                <label class="form-check form--check mr-2 mr-md-4">
                    <input class="form-check-input rounded" type="checkbox" value="1" name="days">
                    <span class="form-check-label">Saturday</span>
                </label>
                <label class="form-check form--check mr-2 mr-md-4">
                    <input class="form-check-input rounded" type="checkbox" value="0" name="days" checked>
                    <span class="form-check-label">Sunday</span>
                </label>
                <label class="form-check form--check mr-2 mr-md-4">
                    <input class="form-check-input rounded" type="checkbox" value="0" name="days">
                    <span class="form-check-label">Monday</span>
                </label>
                <label class="form-check form--check mr-2 mr-md-4">
                    <input class="form-check-input rounded" type="checkbox" value="0" name="days">
                    <span class="form-check-label">Tuesday</span>
                </label>
                <label class="form-check form--check mr-2 mr-md-4">
                    <input class="form-check-input rounded" type="checkbox" value="0" name="days">
                    <span class="form-check-label">Wednesday</span>
                </label>
                <label class="form-check form--check mr-2 mr-md-4">
                    <input class="form-check-input rounded" type="checkbox" value="0" name="days">
                    <span class="form-check-label">Thursday</span>
                </label>
                <label class="form-check form--check mr-2 mr-md-4">
                    <input class="form-check-input rounded" type="checkbox" value="0" name="days">
                    <span class="form-check-label">Friday</span>
                </label>
            </div>
            <div class="bg-light rounded p-3">
                <div class="mb-20">
                    <h5 class="title-clr mb-0">Date Range</h5>
                    <p class="fz-12">Select the date range you want to repeat this cycle every week</p>
                </div>
                <div class="mb-20">
                    <label class="form-label">{{ translate('Date Range') }}</label>
                    <div class="position-relative date-range__custom">
                        <i class="tio-calendar-month icon-absolute-on-right"></i>
                        <input type="text" class="form-control h-45 position-relative bg-transparent"  name="dates" placeholder="{{ translate('messages.Select_Date') }}">
                    </div>
                </div>
                <label class="form-check form--check mr-2 mr-md-4">
                    <input class="form-check-input rounded" type="checkbox" value="1" name="assign">
                    <span class="form-check-label">Assign this surge price permanently</span>
                </label>
            </div>
        </div>
      </div>
      <div class="modal-footer justify-content-end border-0 pt-0 gap-2">
        <button type="button" class="btn min-w-120px btn--reset" data-dismiss="modal">No</button>
        <button type="button" class="btn min-w-120px btn--primary yes_date">Yes</button>
      </div>
    </div>
  </div>
</div>

<!-- Custom Schedule Select Calender Modal -->
<div class="modal shedule-modal fade" id="surgeCustom_sheduleBtn" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog modal-lg" modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header px-4 pt-4">
        <div>
            <h3 class="title-clr mb-0">Select Days</h3>
            <p class="fz-12 m-0">Your Surge price active date</p>
        </div>
        <button type="button" class="close bg-light w-30px h-30 rounded-circle" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="calendar">
                    <div class="d-flex align-items-center gap-4 justify-content-center">
                        <pre class="left"><i class="tio-chevron-left fs-24 cursor-pointer"></i></pre>
                        <div class="header-display">
                            <p class="display fs-14 font-semibold">""</p>
                        </div>
                        <pre class="right"><i class="tio-chevron-right fs-24 cursor-pointer"></i></pre>
                    </div>

                    <div class="week">
                    <div>Su</div>
                    <div>Mo</div>
                    <div>Tu</div>
                    <div>We</div>
                    <div>Th</div>
                    <div>Fr</div>
                    <div>Sa</div>
                    </div>
                    <div class="days"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="selected-listall">
                    <h5 class="mb-20">Selected Days List</h5>
                    <div class="d-flex align-items-center justify-content-md-start justify-content-between gap-1 mb-2">
                        <span class="fs-12 text-title opacity-50 text-uppercase min-w-110px">Date</span>
                        <span class="fs-12 text-title opacity-50 text-uppercase pe-30 me-3">Time</span>
                    </div>
                    <div class="selected-list-inner d-flex flex-column gap-3">
                        
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer justify-content-end border-0 pt-0 pb-4 gap-2">
        <button type="button" class="btn min-w-120px btn--reset" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn min-w-120px btn--primary">Submit</button>
      </div>
    </div>
  </div>
</div>

<!-- Custom Time Edit Modal -->
<div class="modal shedule-modal fade" id="timeRange_btn" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content pb-1">
      <div class="modal-header">
        <div>
            <h3 class="title-clr mb-0">Thu, Jan 04</h3>
        </div>
        <button type="button" class="close bg-light w-30px h-30 rounded-circle" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal-body-inner">            
            <div class="bg-light rounded p-3">
               <div class="time-range-wrapper">
                    <label class="form-label">{{ translate('Change Time') }}</label>
                    <div class="position-relative cursor-pointer">
                        <i class="tio-time icon-absolute-on-right"></i>  
                            <input type="text" class="form-control h-45 position-relative bg-transparent time-range-picker" name="time_range[]" placeholder="{{ translate('messages.Select_Time') }}">
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer justify-content-end border-0 pt-0 gap-2">
        <button type="button" class="btn min-w-120px btn--reset" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn min-w-120px btn--primary">Update</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('script_2')
    <script>

        $(function () {
            function initDateRangePicker(selector) {
                let $input = $(selector);
                $input.daterangepicker({
                    minDate: new Date(),
                    startDate: moment().startOf('hour'),
                    endDate: moment().startOf('hour').add(10, 'day'),
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear'
                    }
                });
                $input.on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(
                        picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY')
                    );
                });
                $input.on('cancel.daterangepicker', function () {
                    $(this).val('');
                    $(this).attr('placeholder', $(this).data('placeholder'));
                });
                // Set placeholder from data-placeholder if input is empty
                if (!$input.val()) {
                    $input.attr('placeholder', $input.data('placeholder'));
                }
            }
            // Initialize both inputs
            initDateRangePicker('input[name="dates"]');
            initDateRangePicker('input[name="dates2"]');
        });

        $(function() {
            //Schedule Modal Date Select 
            $(document).ready(function () {
                let selectedDateRange = "";

                // Initialize daterangepicker inside modal
                $('#weeklySelectDays_btn input[name="dates"]').daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear'
                    }
                });

                // Store the selected date range
                $('#weeklySelectDays_btn input[name="dates"]').on('apply.daterangepicker', function (ev, picker) {
                    selectedDateRange = picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD');
                    $(this).val(selectedDateRange);
                });

                $('#weeklySelectDays_btn input[name="dates"]').on('cancel.daterangepicker', function (ev, picker) {
                    $(this).val('');
                    selectedDateRange = '';
                });

                // On clicking the Yes button in modal, insert selected date range into main input
                $('.yes_date').on('click', function () {
                    if (selectedDateRange) {
                        // Target the input inside the shedule_item section
                        $('.shedule_item .date-range-input-demo').val(selectedDateRange);
                    }

                    // Close the modal
                    $('#weeklySelectDays_btn').modal('hide');
                });
            });

            //Schedule checkbox tabs
            $(document).ready(function () {
                function updateScheduleVisibility() {
                    const radios = $('.shedule-checkbox-inner .form-check-input');
                    const items = $('.change-shedule-wrapper .shedule_item');
                    items.hide(); 
                    radios.each(function(index) {
                        if ($(this).is(':checked')) {
                            items.eq(index).show(); 
                        }
                    });
                }

                // On page load
                updateScheduleVisibility();

                // On radio change
                $('.shedule-checkbox-inner .form-check-input').on('change', function () {
                    updateScheduleVisibility();
                });
            });

            //Select2 Init
            $('.js-select').each(function () {
                let select2 = $.HSCore.components.HSSelect2.init($(this));
            });

            //Select Placeholder
            $(document).ready(function() {
                $('#module_selected').select2({
                    placeholder: 'Module'
                });
            });

            //Time Range Picker
            $('.time-range-picker').each(function () {
                const $input = $(this);

                $input.daterangepicker({
                    timePicker: true,
                    timePicker24Hour: false,
                    timePickerIncrement: 5,
                    locale: {
                        format: 'h:mm A'
                    },
                    singleDatePicker: false,
                    showDropdowns: false,
                    autoUpdateInput: false
                }, function(start, end) {
                    $input.val(start.format('h:mm A') + ' - ' + end.format('h:mm A'));
                });

                // Hide calendar and show only time
                $input.on('show.daterangepicker', function(ev, picker) {
                    picker.container.find('.calendar-table').hide();
                    picker.container.find('.calendar-time').css('visibility', 'visible');
                });
            });

            //Day Remove
            $(document).on('click', '.selected-list-inner .removeDay', function () {
                $(this).closest('.selected-list-item').remove();
            });

            //Calender & Time Range Added            
            let display = document.querySelector(".display");
            let days = document.querySelector(".days");
            let previous = document.querySelector(".left");
            let next = document.querySelector(".right");
            let selectedListInner = document.querySelector(".selected-list-inner");

            let date = new Date();
            let year = date.getFullYear();
            let month = date.getMonth();
            let selectedDates = new Set(); // To keep unique selected dates

            function displayCalendar() {
                days.innerHTML = ""; // clear calendar

                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const firstDayIndex = firstDay.getDay();
                const numberOfDays = lastDay.getDate();

                let formattedDate = date.toLocaleString("en-US", {
                    month: "long",
                    year: "numeric"
                });

                display.innerHTML = `${formattedDate}`;

                // empty placeholders
                for (let x = 0; x < firstDayIndex; x++) {
                    const div = document.createElement("div");
                    days.appendChild(div);
                }

                for (let i = 1; i <= numberOfDays; i++) {
                    let div = document.createElement("div");
                    let currentDate = new Date(year, month, i);
                    let dateStr = currentDate.toDateString();

                    div.dataset.date = dateStr;
                    div.textContent = i;

                    if (currentDate.toDateString() === new Date().toDateString()) {
                        div.classList.add("current-date");
                    }

                    if (selectedDates.has(dateStr)) {
                        div.classList.add("active");
                    }

                    days.appendChild(div);
                }

                bindDateClickEvents();
            }
            function bindDateClickEvents() {
                const dayElements = document.querySelectorAll(".days div[data-date]");
                dayElements.forEach((day) => {
                    day.addEventListener("click", (e) => {
                        const clickedDate = e.target.dataset.date;

                        if (!selectedDates.has(clickedDate)) {
                            selectedDates.add(clickedDate);
                            e.target.classList.add("active");
                            appendSelectedItem(clickedDate);
                        }
                    });
                });
            }
            function appendSelectedItem(dateString) {
                const item = document.createElement("div");
                item.className = "selected-list-item d-flex flex-sm-nowrap flex-wrap align-items-center justify-content-between justify-content-md-start gap-2 bg-light rounded py-2 px-2";

                item.innerHTML = `
                    <span class="fs-12 text-title text-nowrap after-date">
                        <div class="display-selected">
                            <p class="selected m-0 p-0">${dateString}</p>
                        </div>
                    </span>
                    <div class="d-flex align-items-center gap-3">
                        <div class="position-relative cursor-pointer">
                            <i class="tio-time icon-absolute-on-right fs-12"></i>  
                            <input type="text" class="form-control position-relative fs-10 h-32px bg-white time-range-picker" name="time_range[]" placeholder="Select Time">
                        </div>
                        <button type="button" class="removeDay text-danger btn p-0"><i class="tio-clear-circle-outlined fs-20"></i></button>
                    </div>
                `;

                // remove button
                item.querySelector(".removeDay").addEventListener("click", () => {
                    selectedDates.delete(dateString);
                    item.remove();
                    document.querySelectorAll(`.days div[data-date="${dateString}"]`).forEach(el => el.classList.remove("active"));
                });

                selectedListInner.appendChild(item);

                $(item).find('.time-range-picker').daterangepicker({
                    timePicker: true,
                    timePicker24Hour: false,
                    timePickerIncrement: 5,
                    locale: {
                        format: 'hh:mm A'
                    },
                    singleDatePicker: false,
                    autoApply: true,
                    startDate: moment().startOf('hour'),
                    endDate: moment().startOf('hour').add(1, 'hour'),
                }, function(start, end, label) {
                    // Optional: set formatted value
                    const formatted = start.format("hh:mm A") + ' - ' + end.format("hh:mm A");
                    $(this.element).val(formatted);
                }).on('show.daterangepicker', function(ev, picker) {
                    picker.container.addClass('calendar-table-custom');
                });
            }
            // Navigation
            previous.addEventListener("click", () => {
                if (month === 0) {
                    month = 11;
                    year -= 1;
                } else {
                    month--;
                }
                date.setMonth(month);
                displayCalendar();
            });
            next.addEventListener("click", () => {
                if (month === 11) {
                    month = 0;
                    year += 1;
                } else {
                    month++;
                }
                date.setMonth(month);
                displayCalendar();
            });
            // Initial calendar load
            displayCalendar();
            // Default: select current date
            const todayStr = new Date().toDateString();
            selectedDates.add(todayStr);
            appendSelectedItem(todayStr);


        });

        //init daterange picker
        $(function() {
            $('input[name="dates-top"]').daterangepicker({
                autoUpdateInput: false, // 
                minDate: new Date(),
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(10, 'day'),
                drops: 'up'
            });
        });

    </script>

@endpush