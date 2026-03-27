@extends('layouts.admin.app')

@section('title',translate('messages.Edit Surge Price'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">

<script type="text/javascript" src="{{asset('public/assets/admin/js/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/assets/admin/js/daterangepicker.min.js')}}"></script>
@endpush

@section('content')
<div class="content container-fluid">

    <h3 class="mb-20">{{translate('Edit Surge Price') }}</h3>
      <form action="{{ route('admin.business-settings.zone.surge-price.update', $surge->id) }}" method="post" id="surge_form">
        @csrf
        <div class="card mb-20">
            <div class="card-header">
                <h4 class="mb-0">{{translate('Basic Setup')}}</h4>
            </div>
            <div class="card-body">
                @if ($language)
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link lang_link active" href="#"
                                id="default-link">{{ translate('messages.default') }}</a>
                        </li>
                        @foreach ($language as $lang)
                            <li class="nav-item">
                                <a class="nav-link lang_link" href="#"
                                    id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <div class="lang_form" id="default-form">
                    <div class="form-group">
                        <label class="input-label" for="surgePrice_default">{{translate('messages.Surge Price name')}} ( {{ translate('messages.Default') }})
                            <span class="text-danger">*</span>
                        </label>
                        <input autocomplete="off" type="text" name="surge_price_name[]" placeholder="Type surge price name" class="form-control" value="{{$surge?->getRawOriginal('surge_price_name')}}">
                    </div>
                    <div class="form-group mb-0">
                        <div class="d-flex align-items-center gap-1 flex-wrap justify-content-between mb-2">
                            <label class="input-label mb-0" for="surgePrice_default">{{translate('messages.Note for Customer')}} ( {{ translate('messages.Default') }})
                            </label>
                            <label class="toggle-switch toggle-switch-sm" for="customerDefault">
                                <input autocomplete="off" type="checkbox" name="customer_note_status" class="toggle-switch-input" id="customerDefault" value="1" {{ $surge?->customer_note_status ? 'checked' : '' }}>
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                        <input autocomplete="off" type="text" name="customer_note[]" placeholder="Type note for customer" class="form-control" value="{{$surge?->getRawOriginal('customer_note')}}">
                        <span class="text-right d-block mt-1">{{ translate('Character count')}} 0/50</span>
                    </div>
                    <input autocomplete="off" type="hidden" name="lang[]" value="default">
                </div>
                @foreach ($language as $lang)
                    <?php
                        if(count($surge['translations'])){
                            $translate = [];
                            foreach($surge['translations'] as $t)
                            {
                                if($t->locale == $lang && $t->key=="surge_price_name"){
                                    $translate[$lang]['surge_price_name'] = $t->value;
                                }
                                if($t->locale == $lang && $t->key=="customer_note"){
                                    $translate[$lang]['customer_note'] = $t->value;
                                }
                            }
                        }
                    ?>
                    <div class="d-none lang_form" id="{{ $lang }}-form">
                        <div class="form-group">
                            <label class="input-label" for="surgePrice_default">{{translate('messages.Surge Price name')}} ({{ strtoupper($lang) }})
                                <span class="text-danger">*</span>
                            </label>
                            <input autocomplete="off" type="text" name="surge_price_name[]" placeholder="Type surge price name" class="form-control" value="{{$translate[$lang]['surge_price_name']??''}}">
                        </div>
                        <div class="form-group mb-0">
                            <div class="d-flex align-items-center gap-1 flex-wrap justify-content-between mb-2">
                                <label class="input-label mb-0" for="surgePrice_default">{{translate('messages.Note for Customer')}} ({{ strtoupper($lang) }})
                                </label>
                            </div>
                            <input autocomplete="off" type="text" name="customer_note[]" placeholder="Type note for customer" class="form-control" value="{{$translate[$lang]['customer_note']??''}}">
                            <span class="text-right d-block mt-1">{{ translate('Character count')}} 0/50</span>
                        </div>
                        <input autocomplete="off" type="hidden" name="lang[]" value="{{ $lang }}">
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card mb-20">
            <div class="card-header">
                <h4 class="mb-0">{{translate('Module & Surge Price Setup')}}</h4>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div>
                            <label class="mb-2 d-block title-clr fw-normal">{{ translate('Module') }} <span class="text-danger">*</span></label>
                            @php($modules = \App\Models\Module::
                                    whereHas('zones', function ($query) use ($surge) {
                                $query->where('zone_id', $surge->zone_id);
                            })
                            ->where('module_type','!=','rental')->get())
                            <select name="module_ids[]" id="module_selected" class="form-control h--45px js-select2-custom" multiple="multiple" placeholder="Module Select" data-placeholder="Module">
                                <option></option>
                                @foreach($modules as $module)
                                    <option value="{{ $module->id }}" {{ in_array($module->id, $surge->module_ids) ? 'selected' : '' }}>{{ translate($module->module_name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="mb-2 d-block title-clr fw-normal">{{ translate('Price Increase Rate') }} <span class="text-danger">*</span></label>
                            <div class="custom-group-btn border">
                                <div class="flex-sm-grow-1 w-100">
                                    <input autocomplete="off" id="price" type="number" name="price" class="form-control border-0 pl-unset"
                                            placeholder="Ex: 5" min="0" step="0.001" value="{{ $surge->price }}">
                                </div>
                                <div class="flex-shrink-0">
                                    <select name="price_type" id="price_type" class="custom-select ltr border-0">
                                        <option value="percent" {{ $surge->price_type == 'percent' ? 'selected' : '' }}>%</option>
                                        <option value="amount" {{ $surge->price_type == 'amount' ? 'selected' : '' }}>
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
        <div class="card mb-20 shedule-checkbox_wrapper">
            <div class="card-header">
                <h4 class="mb-0">{{ translate('Duration Setup')}}</h4>
            </div>
            <div class="card-body">
                <div class="form-group w-100 mb-20">
                    <label class="input-label" for="">{{ translate('Surge Price Schedule Type') }} <span class="text-danger">*</span></label>
                    <div class="resturant-type-group shedule-checkbox-inner flex-md-nowrap border">
                        <label class="form-check w-100 form--check mr-2 mr-md-4">
                            <input autocomplete="off" class="form-check-input" type="radio" value="daily" name="duration_type" {{ $surge->duration_type == 'daily' ? 'checked' : '' }}>
                            <span class="form-check-label">{{ translate('Daily Schedule') }}</span>
                        </label>
                        <label class="form-check w-100 form--check mr-2 mr-md-4">
                            <input autocomplete="off" class="form-check-input" type="radio" value="weekly" name="duration_type" {{ $surge->duration_type == 'weekly' ? 'checked' : '' }}>
                            <span class="form-check-label">{{ translate('Weekly Schedule') }}</span>
                        </label>
                        <label class="form-check w-100 form--check mr-2 mr-md-4">
                            <input autocomplete="off" class="form-check-input" type="radio" value="custom" name="duration_type" {{ $surge->duration_type == 'custom' ? 'checked' : '' }}>
                            <span class="form-check-label">{{ translate('Custom Schedule') }}</span>
                        </label>
                    </div>
                </div>
                <div class="bg-light p-20 p-mobile-0 bg-mobile-transparent">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div>
                                <h5 class="mb-1">{{ translate('Duration Setup') }}</h5>
                                <p class="fs-12 m-0">{{ translate('Select your suitable time within a time range you want add surge price') }}</p>
                            </div>
                            {{-- <div class="bg--3 rounded px-3 py-2 d-inline-flex align-items-start gap-2 flex-wrap mt-20">
                                <i class="tio-warning text-danger"></i>
                                <p class="m-0 max-w-353px">{{ translate('This surge price overlaps with another. Please change the module or reschedule to fix it.') }}</p>
                            </div> --}}
                        </div>
                        <div class="col-md-6">
                            <div class="change-shedule-wrapper">
                                <!-- Daily Schedule -->
                                <div class="shedule_item">
                                    <div class="bg-white p-sm-3 d-flex flex-column gap-3">
                                        @php($dateRange = Carbon\Carbon::parse($surge->start_date)->format('m/d/Y') . ' - ' . Carbon\Carbon::parse($surge->end_date)->format('m/d/Y'))
                                        <div>
                                            <label class="form-label">{{ translate('Date Range') }} <span class="text-danger">*</span></label>
                                            <div class="position-relative date-range__custom">
                                                <i class="tio-calendar-month icon-absolute-on-right"></i>
                                                <input autocomplete="off" type="text" class="form-control h-45 position-relative bg-transparent no-type"  name="daily_date_range" placeholder="{{ translate('messages.Select_Date') }}" value="{{ $surge->duration_type == 'daily' ? $dateRange : '' }}">
                                            </div>
                                        </div>
                                        @php($timeRange = \Carbon\Carbon::parse($surge->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::parse($surge->end_time)->format('g:i A'))
                                        <div class="time-range-wrapper">
                                            <label class="form-label">{{ translate('Time Range') }} <span class="text-danger">*</span></label>
                                            <div class="position-relative cursor-pointer">
                                                <i class="tio-time icon-absolute-on-right"></i>
                                                <input autocomplete="off" type="text" class="form-control h-45 position-relative bg-transparent time-range-picker no-type" name="daily_time_range" placeholder="{{ translate('messages.Select_Time') }}" value="{{ $surge->duration_type == 'daily' ? $timeRange : '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Weekly Schedule -->
                                 <div class="shedule_item">
                                     <div class="bg-white p-sm-3 d-flex flex-column gap-3">
                                        @php($weeklyDateRange = Carbon\Carbon::parse($surge->start_date)->format('m/d/Y') . ' - ' . Carbon\Carbon::parse($surge->end_date)->format('m/d/Y'))
                                         <div class="cursor-pointer" data-toggle="modal" data-target="#weeklySelectDays_btn">
                                             <label class="form-label">{{ translate('Date Range') }} <span class="text-danger">*</span></label>
                                             <div class="position-relative date-range__custom">
                                                 <i class="tio-calendar-month icon-absolute-on-right"></i>
                                                 <input autocomplete="off" type="text" class="form-control h-45 position-relative bg-transparent date-range-input-demo no-type"
                                                     name="weekly_date_range" placeholder="{{ translate('messages.Select_Date') }}" value="{{ $surge->duration_type == 'weekly' && !$surge->is_permanent ? $weeklyDateRange : '' }}">
                                             </div>
                                         </div>
                                        <?php
                                            $weeklyDays = $surge->weekly_days ?? [];
                                            $weekly_days_count = count($weeklyDays);
                                            $days = '';

                                            if ($weekly_days_count === 1) {
                                                $days = $weeklyDays[0];
                                            } elseif ($weekly_days_count === 2) {
                                                $days = $weeklyDays[0] . ' & ' . $weeklyDays[1];
                                            } elseif ($weekly_days_count > 2) {
                                                $days = implode(', ', array_slice($weeklyDays, 0, -1)) . ' & ' . end($weeklyDays);
                                            }
                                        ?>
                                        @if($days)
                                            <p class="fs-12 m-0 weekly-summary">
                                                {{ translate('Every week from') }} <span class="font-semibold" id="selected-weekdays-text">{{ $days }}</span>
                                            </p>
                                        @else
                                            <p class="fs-12 m-0 weekly-summary d-none">
                                                {{ translate('Every week from') }} <span class="font-semibold" id="selected-weekdays-text"></span>
                                            </p>
                                        @endif
                                        @php($weeklyTimeRange = \Carbon\Carbon::parse($surge->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::parse($surge->end_time)->format('g:i A'))
                                         <div class="time-range-wrapper">
                                             <label class="form-label">{{ translate('Time Range') }} <span class="text-danger">*</span></label>
                                             <div class="position-relative cursor-pointer">
                                                 <i class="tio-time icon-absolute-on-right"></i>
                                                  <input autocomplete="off" type="text" class="form-control h-45 position-relative bg-transparent time-range-picker no-type" name="weekly_time_range" placeholder="{{ translate('messages.Select_Time') }}" value="{{ $surge->duration_type == 'weekly' ? $weeklyTimeRange : '' }}">
                                             </div>
                                         </div>
                                         <input autocomplete="off" type="hidden" name="weekly_days" id="weekly_days" value="{{implode(',', $surge->weekly_days??[]) }}">
                                         <input autocomplete="off" type="hidden" name="is_permanent" id="is_permanent" value="{{ $surge->is_permanent ? 1 : 0 }}">
                                     </div>
                                 </div>
                                <!-- Custom Schedule -->
                                 <div class="shedule_item">
                                     <div class="bg-white p-sm-3 d-flex flex-column gap-3">
                                         <div class="cursor-pointer" data-toggle="modal" data-target="#surgeCustom_sheduleBtn">
                                             <label class="form-label">{{ translate('Date & Time Select') }} <span class="text-danger">*</span></label>
                                             <div class="position-relative">
                                                <?php
                                                    $selectedCount = isset($surge->custom_days) ? count($surge->custom_days) : 0;
                                                    $placeholderText = $surge->duration_type == 'custom'
                                                            ? $selectedCount . ' day' . ($selectedCount > 1 ? 's' : '') . ' selected'
                                                            : translate('messages.Select Date & Time');
                                                            $minDate = null;
                                                            $maxDate = null;

                                                            if (!empty($surge->custom_days)) {
                                                                $dates = array_map(function ($dateStr) {
                                                                    return \Carbon\Carbon::parse($dateStr);
                                                                }, $surge->custom_days);

                                                                $minDate = $dates ? $dates[0] : null;
                                                                $maxDate = $dates ? $dates[0] : null;

                                                                foreach ($dates as $date) {
                                                                    if ($date->lessThan($minDate)) {
                                                                        $minDate = $date;
                                                                    }
                                                                    if ($date->greaterThan($maxDate)) {
                                                                        $maxDate = $date;
                                                                    }
                                                                }
                                                            }
                                                ?>
                                                 <i class="tio-calendar-month icon-absolute-on-right"></i>
                                                 <input autocomplete="off" type="text" id="custom_schedule_input" class="form-control h-45 position-relative bg-transparent no-type" name="" placeholder="{{ $placeholderText }}">
                                             </div>
                                         </div>
                                         <input autocomplete="off" type="hidden" name="custom_days" id="custom_days" value="{{ implode(',', array_map('trim', $surge->custom_days?? [])) }}">
                                         <input autocomplete="off" type="hidden" name="custom_times" id="custom_times" value="{{ implode(',', array_map('trim', $surge->custom_times?? [])) }}">
                                        @if($surge->duration_type == 'custom' && $surge->details->count() > 0)
                                            <p class="fs-12 m-20" id="custom-date-range-text">
                                                {{ translate('messages.Date_range') }} <span class="font-semibold" id="custom-date-min">{{ $minDate->format('M d, Y') }}</span> - <span class="font-semibold" id="custom-date-max">{{ $maxDate->format('M d, Y') }}</span>
                                            </p>
                                        @else
                                           <p class="fs-12 m-20 d-none" id="custom-date-range-text">
                                                {{ translate('messages.Date_range') }} <span class="font-semibold" id="custom-date-min"></span> - <span class="font-semibold" id="custom-date-max"></span>
                                            </p>
                                        @endif
                                        @if($surge->duration_type == 'custom' && $surge->details->count() > 0)
                                            <div class="table-responsive p-0 date-table" id="custom-schedule-table">
                                                <table id="columnSearchDatatable" class="table m-0 table-borderless table-thead-bordered table-align-middle">
                                                    <thead class="thead-light border-0">
                                                        <tr>
                                                            <th class="border-0 fs-14">{{ translate('messages.SL') }}</th>
                                                            <th class="border-0 fs-14">{{ translate('messages.Title') }}</th>
                                                            <th class="border-0 fs-14 text-center">{{ translate('messages.Action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="customScheduleTableBody">
                                                        @foreach($surge->details->unique('applicable_date')->values() as $key => $schedule)
                                                        @php($scheduleTimeRange = \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::parse($schedule->end_time)->format('g:i A'))
                                                        <tr data-index="{{ $key }}">
                                                            <td class="pl-4">{{ $key + 1 }}</td>
                                                            <td>
                                                                <span class="d-block max-w-220px min-w-176px">
                                                                    <span class="d-block text-title">{{ \Carbon\Carbon::parse($schedule->applicable_date)->format('D, M d') }}</span>
                                                                    <span>{{ $scheduleTimeRange }}</span>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="btn--container justify-content-center">
                                                                    <a href="#0" class="btn action-btn btn-outline-theme-light btn-edit-schedule" data-toggle="modal" data-target="#timeRange_btn">
                                                                        <i class="tio-edit"></i>
                                                                    </a>
                                                                    <a class="btn action-btn btn--danger btn-outline-danger btn-remove-schedule" href="#0">
                                                                        <i class="tio-delete-outlined"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="table-responsive p-0 date-table d-none" id="custom-schedule-table">
                                                <table id="columnSearchDatatable" class="table m-0 table-borderless table-thead-bordered table-align-middle">
                                                    <thead class="thead-light border-0">
                                                        <tr>
                                                            <th class="border-0 fs-14">{{ translate('messages.SL') }}</th>
                                                            <th class="border-0 fs-14">{{ translate('messages.Title') }}</th>
                                                            <th class="border-0 fs-14 text-center">{{ translate('messages.Action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="customScheduleTableBody">
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                     </div>
                                 </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn--container justify-content-end mt-20">
            <button type="reset" id="reset_btn" class="btn btn--reset min-w-120px">{{ translate('messages.Reset') }}</button>
            <button type="submit" class="btn btn--primary min-w-120px">{{ translate('messages.Submit') }}</button>
        </div>

       
    </form>




</div>

<!-- Weekly Schedule Select Days Modal -->
<div class="modal shedule-modal fade" id="weeklySelectDays_btn" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content pb-1">
      <div class="modal-header">
        <div>
            <h3 class="title-clr mb-0">{{ translate('messages.Select_Days') }}</h3>
            <p class="fz-12 m-0">{{ translate('messages.Your_Surge_price_active_date') }}</p>
        </div>
        <button type="button" class="close bg-light w-30px h-30 rounded-circle" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal-body-inner">
            <div class="resturant-type-group bg-light rounded p-3 mb-3 gap-4">
                <label class="form-check form--check mr-2 mr-md-4">
                    <input autocomplete="off" class="form-check-input rounded" type="checkbox" value="1" name="days" {{ in_array('Saturday', $surge->weekly_days??[]) ? 'checked' : '' }}>
                    <span class="form-check-label">{{ translate('messages.Saturday') }}</span>
                </label>
                <label class="form-check form--check mr-2 mr-md-4">
                    <input autocomplete="off" class="form-check-input rounded" type="checkbox" value="0" name="days" {{ in_array('Sunday', $surge->weekly_days??[]) ? 'checked' : '' }}>
                    <span class="form-check-label">{{ translate('messages.Sunday') }}</span>
                </label>
                <label class="form-check form--check mr-2 mr-md-4">
                    <input autocomplete="off" class="form-check-input rounded" type="checkbox" value="0" name="days" {{ in_array('Monday', $surge->weekly_days??[]) ? 'checked' : '' }}>
                    <span class="form-check-label">{{ translate('messages.Monday') }}</span>
                </label>
                <label class="form-check form--check mr-2 mr-md-4">
                    <input autocomplete="off" class="form-check-input rounded" type="checkbox" value="0" name="days" {{ in_array('Tuesday', $surge->weekly_days??[]) ? 'checked' : '' }}>
                    <span class="form-check-label">{{ translate('messages.Tuesday') }}</span>
                </label>
                <label class="form-check form--check mr-2 mr-md-4">
                    <input autocomplete="off" class="form-check-input rounded" type="checkbox" value="0" name="days" {{ in_array('Wednesday', $surge->weekly_days??[]) ? 'checked' : '' }}>
                    <span class="form-check-label">{{ translate('messages.Wednesday') }}</span>
                </label>
                <label class="form-check form--check mr-2 mr-md-4">
                    <input autocomplete="off" class="form-check-input rounded" type="checkbox" value="0" name="days" {{ in_array('Thursday', $surge->weekly_days??[]) ? 'checked' : '' }}>
                    <span class="form-check-label">{{ translate('messages.Thursday') }}</span>
                </label>
                <label class="form-check form--check mr-2 mr-md-4">
                    <input autocomplete="off" class="form-check-input rounded" type="checkbox" value="0" name="days" {{ in_array('Friday', $surge->weekly_days??[]) ? 'checked' : '' }}>
                    <span class="form-check-label">{{ translate('messages.Friday') }}</span>
                </label>
            </div>
            <div class="bg-light rounded p-3">
                <div class="mb-20">
                    <h5 class="title-clr mb-0">{{ translate('messages.Date_Range') }}</h5>
                    <p class="fz-12">{{ translate('messages.Select_the_date_range_you_want_to_repeat_this_cycle_every_week') }}</p>
                </div>
                <div class="mb-20">
                    <label class="form-label">{{ translate('messages.Date_Range') }} <span class="text-danger">*</span></label>
                    <div class="position-relative date-range__custom">
                        <i class="tio-calendar-month icon-absolute-on-right"></i>
                        <input autocomplete="off" type="text" class="form-control h-45 position-relative {{ $surge->is_permanent ? 'bg-transparent' : '' }} no-type"  name="dates" placeholder="{{ translate('messages.Select_Date') }}" value="{{ $surge->duration_type && !$surge->is_permanent == 'weekly' ? $weeklyDateRange : '' }}" id="weekly_modal_date" {{ $surge->is_permanent ? 'disabled' : '' }}>
                    </div>
                </div>
                <label class="form-check form--check mr-2 mr-md-4">
                    <input autocomplete="off" class="form-check-input rounded" type="checkbox" value="1" name="assign" {{ $surge->is_permanent ? 'checked' : '' }} id="weekly_is_permanent">
                    <span class="form-check-label">{{ translate('messages.Assign_this_surge_price_permanently') }}</span>
                </label>
            </div>
        </div>
      </div>
      <div class="modal-footer justify-content-end border-0 pt-0 gap-2">
        <button type="button" class="btn min-w-120px btn--reset" data-dismiss="modal">{{ translate('messages.No') }}</button>
        <button type="button" class="btn min-w-120px btn--primary yes_date">{{ translate('messages.Yes') }}</button>
      </div>
    </div>
  </div>
</div>

<!-- Custom Schedule Select Calender Modal -->
<div class="modal shedule-modal fade" id="surgeCustom_sheduleBtn" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog modal-lg" modal-dialog-centered>
    <div class="modal-content">
      <div class="modal-header px-4 pt-4">
        <div>
            <h3 class="title-clr mb-0">{{ translate('messages.Select_Date') }}</h3>
            <p class="fz-12 m-0">{{ translate('messages.Your_Surge_price_active_date') }}</p>
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
                    <h5 class="mb-20">{{ translate('messages.Selected_Days_List') }}</h5>
                    <div class="d-flex align-items-center justify-content-md-start justify-content-between gap-1 mb-2">
                        <span class="fs-12 text-title opacity-50 text-uppercase min-w-110px">{{ translate('messages.Date') }}</span>
                        <span class="fs-12 text-title opacity-50 text-uppercase pe-30 me-3">{{ translate('messages.Time') }}</span>
                    </div>
                    <div class="selected-list-inner d-flex flex-column gap-3">
                        {{-- @foreach($surge->details->unique('applicable_date')->values() as $key => $schedule)
                        <div class="selected-list-item d-flex flex-sm-nowrap flex-wrap align-items-center justify-content-between justify-content-md-start gap-2 bg-light rounded py-2 px-2">
                            <span class="fs-12 text-title text-nowrap after-date">
                                <div class="display-selected">
                                    <p class="selected m-0 p-0">{{ \Carbon\Carbon::parse($schedule->applicable_date)->format('D M d Y') }}</p>
                                </div>
                            </span>
                            <input autocomplete="off" type="hidden" name="custom_dates[]" id="custom_dates" value="{{ \Carbon\Carbon::parse($schedule->applicable_date)->format('D M d Y') }}">
                             @php($scheduleTimeRange = \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::parse($schedule->end_time)->format('g:i A'))
                            <div class="d-flex align-items-center gap-3">
                                <div class="position-relative cursor-pointer">
                                    <i class="tio-time icon-absolute-on-right fs-12"></i>  
                                    <input autocomplete="off" type="text" class="form-control position-relative fs-10 h-32px bg-white time-range-picker" name="custom_time_range[]" placeholder="Select Time" value="{{ $scheduleTimeRange }}">
                                </div>
                                <button type="button" class="removeDay text-danger btn p-0"><i class="tio-clear-circle-outlined fs-20"></i></button>
                            </div>
                        </div>
                        @endforeach --}}
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer justify-content-end border-0 pt-0 pb-4 gap-2">
        <button type="button" class="btn min-w-120px btn--reset" data-dismiss="modal">{{ translate('messages.Cancel') }}</button>
        <button type="button" class="btn min-w-120px btn--primary">{{ translate('messages.Submit') }}</button>
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
            <h3 class="title-clr mb-0" id="edit-time-date-label">{{ translate('messages.Selected_Date') }}</h3>
        </div>
        <button type="button" class="close bg-light w-30px h-30 rounded-circle" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">          
        <div class="bg-light rounded p-3">
            <div class="time-range-wrapper">
                <label class="form-label">{{ translate('Change Time') }}</label>
                <div class="position-relative cursor-pointer">
                    <i class="tio-time icon-absolute-on-right"></i>  
                    <input autocomplete="off" type="text" class="form-control h-45 position-relative bg-transparent time-range-picker no-type" id="edit-time-range-input" placeholder="{{ translate('messages.Select_Time') }}">
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer justify-content-end border-0 pt-0 gap-2">
        <button type="button" class="btn min-w-120px btn--reset" data-dismiss="modal">{{ translate('messages.Cancel') }}</button>
        <button type="button" class="btn min-w-120px btn--primary" id="update-time-btn">{{ translate('messages.Update') }}</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="error-modal">
    <div class="modal-dialog status-warning-modal">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true" class="tio-clear"></span>
                </button>
            </div>
            <div class="modal-body pb-5 pt-0">
                <div class="max-349 mx-auto mb-20">
                    <div>
                        <div class="text-center">
                            <img id="toggle-image" alt="" src="{{ asset('public/assets/admin/img/modal-error.png') }}" class="mb-20">
                            <h5 class="modal-title" id="toggle-title">{{ translate('Surge Price setup Overlap!') }}</h5>
                        </div>
                        <div class="text-center" id="toggle-message">
                            <p>{{ translate('This surge price overlaps with another. Please change the module or reschedule to fix it.') }}</p>
                        </div>
                    </div>
                    <div class="btn--container justify-content-center">
                        <button type="button" id="toggle-ok-button" class="btn btn--primary min-w-120" data-dismiss="modal" >{{translate('Okay')}}</button>
                    </div>
                </div>
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
            initDateRangePicker('input[name="daily_date_range"]');
        });

        $(function() {
            //Weekly Modal Date Select 
            $(document).ready(function () {
                let selectedDateRange = "";

                // Initialize daterangepicker inside modal
                $('#weeklySelectDays_btn input[name="dates"]').daterangepicker({
                    autoUpdateInput: false,
                    minDate: new Date(),
                    locale: {
                        cancelLabel: 'Clear'
                    }
                });

                // Store the selected date range
                $('#weeklySelectDays_btn input[name="dates"]').on('apply.daterangepicker', function (ev, picker) {
                    selectedDateRange = picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD');
                    $(this).val(selectedDateRange);
                });

                $('#weeklySelectDays_btn input[name="dates"]').on('cancel.daterangepicker', function (ev, picker) {
                    $(this).val('');
                    selectedDateRange = '';
                });

                // On clicking the Yes button in modal, insert selected date range into main input
                $('.yes_date').on('click', function () {
                    let selectedDateRange = $('#weeklySelectDays_btn input[name="dates"]').val();
                    let selectedDays = [];
                    $('input[name="days"]:checked').each(function () {
                        selectedDays.push($(this).siblings('.form-check-label').text().trim());
                    });

                    let isPermanent = $('input[name="assign"]').is(':checked') ? 1 : 0;

                    // === Validation ===
                    if (selectedDays.length === 0) {
                        toastr.error('Please select at least one day.');
                        return;
                    }

                    if (!selectedDateRange && !isPermanent) {
                        toastr.error('Please select a date range.');
                        return;
                    }
                    

                    
                    // Bind to hidden inputs
                    $('#weekly_days').val(selectedDays.join(','));
                    $('#is_permanent').val(isPermanent);
                    $('.shedule_item .date-range-input-demo').val(selectedDateRange);
                    
                    // Target the input inside the schedule_item section
                    function formatSelectedDays(days) {
                        if (days.length === 1) {
                            return days[0];
                        } else if (days.length === 2) {
                            return days[0] + ' & ' + days[1];
                        } else {
                            return days.slice(0, -1).join(', ') + ' & ' + days[days.length - 1];
                        }
                    }
                    // Update weekday text
                    $('#selected-weekdays-text').text(formatSelectedDays(selectedDays));

                    // Show or hide the paragraph based on date
                    if (selectedDays) {
                        $('.weekly-summary').removeClass('d-none');
                    } else {
                        $('.weekly-summary').addClass('d-none');
                    }

                    // Close the modal
                    $('#weeklySelectDays_btn').modal('hide');
                });

                $('#weekly_is_permanent').on('change', function () {
                    if ($(this).is(':checked')) {
                        $('#weekly_modal_date')
                            .prop('disabled', true)
                            .addClass('bg-transparent').val('');
                             $('.shedule_item .date-range-input-demo').val('');
                    } else {
                        $('#weekly_modal_date')
                            .prop('disabled', false)
                            .removeClass('bg-transparent');
                    }
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
            @if($surge->duration_type === 'custom' && is_array($surge->custom_days))
                @foreach($surge->custom_days as $day)
                    selectedDates.add("{{ $day }}");
                @endforeach
            @endif

            function displayCalendar() {
                days.innerHTML = ""; // clear calendar

                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const firstDayIndex = firstDay.getDay();
                const numberOfDays = lastDay.getDate();
                const minDate = new Date(); // today
                minDate.setHours(0, 0, 0, 0);

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
                    currentDate.setHours(0, 0, 0, 0);
                    let dateStr = currentDate.toDateString();

                    div.dataset.date = dateStr;
                    div.textContent = i;

                    if (currentDate < minDate) {
                        div.classList.add("disabled");
                    } else {
                        // if (currentDate.toDateString() === new Date().toDateString()) {
                        //     div.classList.add("active");
                        // }

                        if (selectedDates.has(dateStr)) {
                            div.classList.add("active");
                        }
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

            function appendSelectedItem(dateString, timeRange = '') {
                const item = document.createElement("div");
                item.className = "selected-list-item d-flex flex-sm-nowrap flex-wrap align-items-center justify-content-between justify-content-md-start gap-2 bg-light rounded py-2 px-2";

                item.innerHTML = `
                    <span class="fs-12 text-title text-nowrap after-date">
                        <div class="display-selected">
                            <p class="selected m-0 p-0">${dateString}</p>
                        </div>
                    </span>
                    <input autocomplete="off" type="hidden" name="custom_dates[]" id="custom_dates" value="${dateString}">
                    <div class="d-flex align-items-center gap-3">
                        <div class="position-relative cursor-pointer">
                            <i class="tio-time icon-absolute-on-right fs-12"></i>  
                            <input autocomplete="off" type="text" class="form-control position-relative fs-10 h-32px bg-white time-range-picker" name="custom_time_range[]" placeholder="Select Time" value="${timeRange}">
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

                const timePicker = $(item).find('.time-range-picker');

                let start = moment().startOf('hour');
                let end = moment().startOf('hour').add(1, 'hour');

                if (timeRange && timeRange.includes(' - ')) {
                    const [startTimeStr, endTimeStr] = timeRange.split(' - ');
                    const parsedStart = moment(startTimeStr, 'hh:mm A');
                    const parsedEnd = moment(endTimeStr, 'hh:mm A');

                    // Use parsed times if valid
                    if (parsedStart.isValid() && parsedEnd.isValid()) {
                        start = parsedStart;
                        end = parsedEnd;
                    }
                }

                timePicker.daterangepicker({
                    timePicker: true,
                    timePicker24Hour: false,
                    // timePickerIncrement: 5,
                    locale: {
                        format: 'hh:mm A'
                    },
                    singleDatePicker: false,
                    // autoApply: true,
                    // startDate: start,
                    // endDate: end,
                }, function(start, end, label) {
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
            // const todayStr = new Date().toDateString();
            // selectedDates.add(todayStr);
            // appendSelectedItem(todayStr);

            const customSelectedDates = {!! json_encode($surge->custom_days ?? []) !!};
            const selectTimes = {!! json_encode($surge->custom_times ?? []) !!};

            const savedSelections = {};

            customSelectedDates.forEach((date, index) => {
                savedSelections[date] = selectTimes[index] || '';
            });

            customSelectedDates.forEach(date => {
                console.log(savedSelections[date]);
                
                if (!customSelectedDates.includes(date)) {
                    customSelectedDates.push(date);
                    appendSelectedItem(date, savedSelections[date]);
                } else {
                    appendSelectedItem(date, savedSelections[date]);
                }
            });



            $('#surgeCustom_sheduleBtn .btn--primary').on('click', function () {
                let customDates = [];
                let customTimes = [];

                $('.selected-list-inner .selected-list-item').each(function () {
                    const date = $(this).find('input[name="custom_dates[]"]').val();
                    const time = $(this).find('input[name="custom_time_range[]"]').val();

                    if (date && time) {
                        customDates.push(date.trim());
                        customTimes.push(time.trim());
                    }
                });

                // Validate: if empty, prevent modal close
                if (customDates.length === 0 || customTimes.includes("")) {
                    toastr.error('Please select both date and time for all custom entries.');
                    return;
                }

                // Assign to hidden inputs
                $('#custom_days').val(customDates); 
                $('#custom_times').val(customTimes); 

                const selectedCount = customDates.length;
                const placeholderText = selectedCount + ' day' + (selectedCount > 1 ? 's' : '') + ' selected';

                $('#custom_schedule_input').attr('placeholder', placeholderText);

                // Update date range display
                const sortedDates = customDates.map(date => new Date(date)).sort((a, b) => a - b);

                const minDate = sortedDates[0];
                const maxDate = sortedDates[sortedDates.length - 1];

                const options = { day: 'numeric', month: 'short', year: 'numeric' };

                const formattedMin = minDate.toLocaleDateString('en-US', options);
                const formattedMax = maxDate.toLocaleDateString('en-US', options);

                $('#custom-date-min').text(formattedMin);
                $('#custom-date-max').text(formattedMax);
                $('#custom-date-range-text').removeClass('d-none');
                $('#custom-schedule-table').removeClass('d-none');

                // Dynamically render table
                renderCustomScheduleTable(customDates, customTimes);

                // Close the modal
                $('#surgeCustom_sheduleBtn').modal('hide');
            });

            function renderCustomScheduleTable(dates, times) {
                const tbody = $('#customScheduleTableBody');
                tbody.empty();

                dates.forEach((date, index) => {
                    const time = times[index] || '';
                    const sl = index + 1;

                    const formattedDate = new Date(date).toLocaleDateString('en-US', {
                        weekday: 'short', month: 'short', day: '2-digit'
                    }); // Example: "Mon, Jul 14"

                    const row = `
                        <tr data-index="${index}">
                            <td class="pl-4">${sl}</td>
                            <td>
                                <span class="d-block max-w-220px min-w-176px">
                                    <span class="d-block text-title">${formattedDate}</span>
                                    <span>${time}</span>
                                </span>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a href="#0" class="btn action-btn btn-outline-theme-light btn-edit-schedule" data-toggle="modal" data-target="#timeRange_btn">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="btn action-btn btn--danger btn-outline-danger btn-remove-schedule" href="#0">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }

            $(document).on('click', '.btn-remove-schedule', function (e) {
                e.preventDefault();
                
                // Get the index from the row
                const $row = $(this).closest('tr');
                const index = $row.data('index');

                // Get the date string from the corresponding .selected-list-item
                const $selectedItem = $('.selected-list-inner .selected-list-item').eq(index);
                const dateStr = $selectedItem.find('input[name="custom_dates[]"]').val();

                // Remove from selectedDates Set and unhighlight the calendar
                if (dateStr) {
                    selectedDates.delete(dateStr);
                    document.querySelectorAll(`.days div[data-date="${dateStr}"]`).forEach(el => el.classList.remove("active"));
                }

                // Remove the selected-list-item
                $selectedItem.remove(); 

                // Reconstruct updated values
                let updatedDates = [];
                let updatedTimes = [];

                $('.selected-list-inner .selected-list-item').each(function () {
                    const date = $(this).find('input[name="custom_dates[]"]').val();
                    const time = $(this).find('input[name="custom_time_range[]"]').val();

                    if (date && time) {
                        updatedDates.push(date.trim());
                        updatedTimes.push(time.trim());
                    }
                });

                // Update hidden fields
                $('#custom_days').val(updatedDates);
                $('#custom_times').val(updatedTimes);

                // Update placeholder
                const selectedCount = updatedDates.length;
                const placeholderText = selectedCount > 0
                    ? selectedCount + ' day' + (selectedCount > 1 ? 's' : '') + ' selected'
                    : '{!! translate("messages.Select Date & Time") !!}';

                $('#custom_schedule_input').attr('placeholder', placeholderText);
                

                // Update date range display
                if(updatedDates.length > 0) {
                    const sortedDates = updatedDates.map(date => new Date(date)).sort((a, b) => a - b);
    
                    const minDate = sortedDates[0];
                    const maxDate = sortedDates[sortedDates.length - 1];
    
                    const options = { day: 'numeric', month: 'short', year: 'numeric' };
    
                    const formattedMin = minDate.toLocaleDateString('en-US', options);
                    const formattedMax = maxDate.toLocaleDateString('en-US', options);
    
                    $('#custom-date-min').text(formattedMin);
                    $('#custom-date-max').text(formattedMax);
                }


                // Update table again
                renderCustomScheduleTable(updatedDates, updatedTimes);

                // If none left, hide range and table
                if (updatedDates.length === 0) {
                    $('#custom-date-range-text').addClass('d-none');
                    $('#custom-schedule-table').addClass('d-none');
                }
            });



            let currentEditIndex = null;

            $(document).on('click', '.btn-edit-schedule', function () {
                const $row = $(this).closest('tr');
                currentEditIndex = $row.data('index');

                const $targetItem = $('.selected-list-inner .selected-list-item').eq(currentEditIndex);
                const dateStr = $targetItem.find('input[name="custom_dates[]"]').val();
                const timeStr = $targetItem.find('input[name="custom_time_range[]"]').val();

                // Update modal label
                $('#edit-time-date-label').text(new Date(dateStr).toDateString());

                const $editInput = $('#edit-time-range-input');

                // Parse existing time range if available
                let startTime = moment().startOf('hour');
                let endTime = moment().startOf('hour').add(1, 'hour');

                if (timeStr && timeStr.includes(' - ')) {
                    const [start, end] = timeStr.split(' - ');
                    startTime = moment(start.trim(), 'hh:mm A');
                    endTime = moment(end.trim(), 'hh:mm A');
                }

                // Set value manually for display
                const formatted = startTime.format("hh:mm A") + ' - ' + endTime.format("hh:mm A");
                $editInput.val(formatted);

                // Re-initialize time picker
                $editInput.daterangepicker({
                    timePicker: true,
                    timePicker24Hour: false,
                    timePickerIncrement: 5,
                    locale: {
                        format: 'hh:mm A'
                    },
                    singleDatePicker: false,
                    autoApply: true,
                    startDate: startTime,
                    endDate: endTime
                }, function (start, end) {
                    const updated = start.format("hh:mm A") + ' - ' + end.format("hh:mm A");
                    $editInput.val(updated);
                }).on('show.daterangepicker', function (ev, picker) {
                    picker.container.addClass('calendar-table-custom');
                });

                // Show the modal
                $('#timeRange_btn').modal('show');
            });


            $('#update-time-btn').on('click', function () {
                const newTime = $('#edit-time-range-input').val();

                if (!newTime) {
                    toastr.error('Please select a time.');
                    return;
                }

                if (currentEditIndex !== null) {
                    const $item = $('.selected-list-inner .selected-list-item').eq(currentEditIndex);

                    // Update both visible and hidden time input
                    $item.find('input[name="custom_time_range[]"]').val(newTime);

                   // Update the visible time input
                    const $timeInput = $item.find('.time-range-picker');
                    $timeInput.val(newTime);

                    // Update the Daterangepicker instance if available
                    const pickerInstance = $timeInput.data('daterangepicker');
                    if (pickerInstance && newTime.includes(' - ')) {
                        const [start, end] = newTime.split(' - ');
                        const startTime = moment(start.trim(), 'hh:mm A');
                        const endTime = moment(end.trim(), 'hh:mm A');
                        pickerInstance.setStartDate(startTime);
                        pickerInstance.setEndDate(endTime);
                    }

                    // Rebuild and re-render updated schedule
                    let updatedDates = [];
                    let updatedTimes = [];

                    $('.selected-list-inner .selected-list-item').each(function () {
                        const date = $(this).find('input[name="custom_dates[]"]').val();
                        const time = $(this).find('input[name="custom_time_range[]"]').val();
                        if (date && time) {
                            updatedDates.push(date.trim());
                            updatedTimes.push(time.trim());
                        }
                    });

                    // Update hidden fields
                    $('#custom_days').val(updatedDates);
                    $('#custom_times').val(updatedTimes);

                    // Update table display
                    renderCustomScheduleTable(updatedDates, updatedTimes);

                    // Update placeholder
                    const selectedCount = updatedDates.length;
                    const placeholderText = selectedCount > 0
                        ? selectedCount + ' day' + (selectedCount > 1 ? 's' : '') + ' selected'
                        : '{{ translate("messages.Select Date & Time") }}';
                    $('#custom_schedule_input').attr('placeholder', placeholderText);

                    // Update date range
                    if (updatedDates.length > 0) {
                        const sorted = updatedDates.map(d => new Date(d)).sort((a, b) => a - b);
                        const options = { day: 'numeric', month: 'short', year: 'numeric' };
                        $('#custom-date-min').text(sorted[0].toLocaleDateString('en-US', options));
                        $('#custom-date-max').text(sorted[sorted.length - 1].toLocaleDateString('en-US', options));
                        $('#custom-date-range-text').removeClass('d-none');
                        $('#custom-schedule-table').removeClass('d-none');
                    }

                    // Close modal
                    $('#timeRange_btn').modal('hide');
                }
            });


            $('#surge_form').on('submit', function (e) {
                e.preventDefault();

                let $form = $(this);
                let formData = new FormData(this);

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        if (res.status) {
                            toastr.success(res.message);
                            setTimeout(() => {
                                window.location.href = "{{ route('admin.business-settings.zone.surge-price.list', [$surge['zone_id']]) }}";
                            }, 1500);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            Object.values(errors).forEach(errorList => {
                                errorList.forEach(err => toastr.error(err));
                            });
                        } else if (xhr.status === 409) {
                            $('#error-modal').modal('show');
                        } else {
                            toastr.error(xhr.responseJSON?.message || 'An unexpected error occurred.');
                        }
                    }
                });
            });
        });

    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const maxLength = 50;

        // Function to update character count
        function updateCharCount(input) {
            const countSpan = input.closest('.form-group').querySelector('.text-right span, .text-right');
            const length = input.value.length;

            // Trim extra characters if pasted
            if (length > maxLength) {
                input.value = input.value.substring(0, maxLength);
            }

            countSpan.textContent = `Character count ${input.value.length}/${maxLength}`;
        }

        // Find all customer_note[] inputs
        const customerNoteInputs = document.querySelectorAll('input[name="customer_note[]"]');

        customerNoteInputs.forEach(input => {
            // Update on input
            input.addEventListener('input', function () {
                updateCharCount(input);
            });

            // Initial update if pre-filled
            updateCharCount(input);
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('input.no-type');

    inputs.forEach(input => {
        input.addEventListener('keydown', function (e) {
            e.preventDefault();
        });
        input.style.cursor = 'pointer'; 
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const priceInput = document.getElementById('price');
    const priceTypeSelect = document.getElementById('price_type');

    function updateMaxLimit() {
        if (priceTypeSelect.value === 'percent') {
            priceInput.max = '100';

            if (parseFloat(priceInput.value) > 100) {
                priceInput.value = '100';
            }

            priceInput.addEventListener('input', percentLimiter);
        } else {
            priceInput.removeAttribute('max');
            priceInput.removeEventListener('input', percentLimiter);
        }
    }

    function percentLimiter(e) {
        if (parseFloat(priceInput.value) > 100) {
            priceInput.value = '100';
        }
    }

    priceTypeSelect.addEventListener('change', updateMaxLimit);
    updateMaxLimit();
});
</script>
@endpush