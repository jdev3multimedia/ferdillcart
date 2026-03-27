@extends('layouts.admin.app')

@section('title',  translate('Create Withdraw Method'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-4 withdraw-header-sticky z-2">
            <div class="page-title-wrap d-flex justify-content-between flex-wrap align-items-center gap-3">
                <h2 class="page-title m-0">
                    <img width="20" src="{{asset('/public/assets/admin/img/withdraw-icon.png')}}" alt="">
                    {{ translate('Create Withdraw Method')}}
                </h2>
                <!-- BUTTON -->
                <button class="btn btn--primary" id="add-more-field">
                    <i class="tio-add-circle"></i> {{ translate('messages.Add_New_Field')}}
                </button>
            </div>
        </div>
        <!-- End Page Title -->

        <div class="row">
            <div class="col-md-12">
                <form action="{{route('admin.transactions.withdraw-method.store')}}" method="POST">
                    @csrf
                    <div class="card card-body">
                        <div class="bg-1079801A p--20 rounded">
                            <div class="form-floating">
                                <label class="text-title">
                                    {{ translate('messages.method_name')}}
                                    <span class="input-label-secondary text-danger">*</span>
                                </label>

                                <input type="text" class="form-control d-flex" name="method_name" id="method_name"
                                placeholder=" {{ translate('messages.Ex:_Bank')}}" value="" required>
                            </div>
                             <div class="d-flex justify-content-start mt-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input checkbox-theme single-select" type="checkbox" value="1" name="is_default" id="flexCheckDefaultMethod">
                                    <label class="form-check-label" for="flexCheckDefaultMethod">
                                        {{ translate('Mark this Method as Default')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <!-- HERE CUSTOM FIELDS WILL BE ADDED -->
                        <div id="custom-field-section">
                            <div class="card card-body">
                                <div class="bg-1079801A p--20 rounded">
                                    <div class="row gy-2 align-items-center">
                                        <div class="col-md-4 col-12">
                                            <label class="text-title">{{ translate('messages.Input_Field_Type')}} <span
                                                class="input-label-secondary text-danger">*</span></label>
                                            <select class="form-control js-select  js-select2-custom" name="field_type[]" required>
                                                {{-- <option value="" selected disabled>{{ translate('messages.Input_Field_Type')}} *</option> --}}
                                                <option value="string">{{ translate('messages.Text')}}</option>
                                                <option value="number">{{ translate('messages.Number')}}</option>
                                                <option value="date">{{ translate('messages.Date')}}</option>
                                                <option value="email">{{ translate('messages.Email')}}</option>
                                                <option value="phone">{{ translate('messages.Phone')}}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="form-floating">
                                                <label class="text-title">{{ translate('messages.field_name')}} <span
                                                class="input-label-secondary text-danger">*</span></label>
                                                <input type="text" class="form-control" name="field_name[]"
                                                        placeholder="{{ translate('messages.Ex:_Account_name')}} " value="" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="form-floating">
                                                <label class="text-title">{{ translate('messages.placeholder_text')}} <span
                                                class="input-label-secondary text-danger">*</span></label>
                                                <input type="text" class="form-control" name="placeholder_text[]"
                                                        placeholder="{{ translate('messages.Ex:_John')}} " value="" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-12">
                                            <div class="d-flex align-items-center justify-content-between pt-1">
                                                <div class="form-check">
                                                    <input class="form-check-input checkbox-theme single-select" type="checkbox" value="1" name="is_required[0]" id="flexCheckDefault__0" checked>
                                                    <label class="form-check-label" for="flexCheckDefault__0">
                                                        {{ translate('messages.Is_required_')}}
                                                    </label>
                                                </div>
                                                {{-- <span class="btn w-30px h-30 py-1 px-1 btn-danger remove-field" data-id="${counter}">
                                                    <i class="tio-delete"></i>
                                                </span> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>

                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="reset" class="btn btn--reset min-w-120px mx-2">{{ translate('messages.Reset')}}</button>
                            <button type="submit" class="btn btn--primary min-w-120px demo_check">{{ translate('messages.Submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection


@push('script_2')
    <script>
        "use strict";
        let counter = 0;

        jQuery(document).ready(function ($) {
            counter = 1;

            $('#add-more-field').on('click', function (event) {
                if(counter < 15) {
                    event.preventDefault();

                    $('#custom-field-section').append(
                        `<div class="card card-body mt-3" id="field-row--${counter}">
                            <div class="bg-1079801A p--20 rounded">
                                <div class="row gy-2 align-items-center">
                                    <div class="col-md-4 col-12">
                                        <label class="text-title">{{ translate('messages.Input_Field_Type')}} <span
                                                class="input-label-secondary text-danger">*</span></label>
                                        <select class="form-control js-select js-select2-custom" name="field_type[]" required>

                                            <option value="string">{{ translate('messages.Text')}}</option>
                                            <option value="number">{{ translate('messages.Number')}}</option>
                                            <option value="date">{{ translate('messages.Date')}}</option>
                                            <option value="email">{{ translate('messages.Email')}}</option>
                                            <option value="phone">{{ translate('messages.Phone')}}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="form-floating">
                                            <label class="text-title">{{ translate('messages.field_name')}} <span
                                                class="input-label-secondary text-danger">*</span></label>
                                            <input type="text" class="form-control" name="field_name[]"
                                                placeholder="{{ translate('messages.Ex:_Bank')}}" value="" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="form-floating">
                                            <label class="text-title">{{ translate('messages.placeholder_text')}} <span
                                                class="input-label-secondary text-danger">*</span></label>
                                            <input type="text" class="form-control" name="placeholder_text[]"
                                                placeholder="{{ translate('messages.Ex:_John')}}" value="" required>
                                        </div>
                                    </div>
                                    {{--<div class="col-md-2 col-12">
                                        <div class="form-check">
                                            <input class="form-check-input checkbox-theme single-select" type="checkbox" value="1" name="is_required[${counter}]" id="flexCheckDefault__${counter}" checked>
                                            <label class="form-check-label" for="flexCheckDefault__${counter}">
                                                {{ translate('messages.Is_required_')}}
                                            </label>
                                        </div>
                                    </div>--}}
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center justify-content-between pt-1">
                                            <div class="form-check">
                                                <input class="form-check-input checkbox-theme single-select" type="checkbox" value="1" name="is_required[${counter}]" id="flexCheckDefault__${counter}" checked>
                                                <label class="form-check-label" for="flexCheckDefault__${counter}">
                                                    {{ translate('messages.Is_required_')}}
                                                </label>
                                            </div>
                                            <span class="btn w-30px h-30 py-1 px-1 btn-danger remove-field" data-id="${counter}">
                                                <i class="tio-delete"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`
                        );

                    $(".js-select").select2();

                    const newRow = document.getElementById(`field-row--${counter}`);
                    if (newRow) {
                        setTimeout(function () {
                            const targetTop = newRow.getBoundingClientRect().top + window.pageYOffset - 100;
                            try {
                                window.scrollTo({ top: targetTop, behavior: 'smooth' });
                            } catch (e) {
                                if (typeof $ !== 'undefined' && $.fn && $.fn.animate) {
                                    $('html, body').stop().animate({ scrollTop: targetTop }, 400);
                                } else {
                                    window.scrollTo(0, targetTop);
                                }
                            }


                        }, 100);
                    }

                    counter++;
                } else {
                    Swal.fire({
                        title: '{{ translate('messages.Reached_maximum')}}',
                        confirmButtonText: '{{ translate('messages.ok')}}',
                    });
                }
            })

            $('form').on('reset', function (event) {
                if(counter > 1) {
                    $('#custom-field-section').html(`
                         <div id="custom-field-section">
                            <div class="card card-body">
                                <div class="bg-1079801A p--20 rounded">
                                    <div class="row gy-2 align-items-center">
                                        <div class="col-md-4 col-12">
                                            <label class="text-title">{{ translate('messages.Input_Field_Type')}} <span
                                                class="input-label-secondary text-danger">*</span></label>
                                            <select class="form-control js-select  js-select2-custom" name="field_type[]" required>
                                                {{-- <option value="" selected disabled>{{ translate('messages.Input_Field_Type')}} *</option> --}}
                                                <option value="string">{{ translate('messages.Text')}}</option>
                                                <option value="number">{{ translate('messages.Number')}}</option>
                                                <option value="date">{{ translate('messages.Date')}}</option>
                                                <option value="email">{{ translate('messages.Email')}}</option>
                                                <option value="phone">{{ translate('messages.Phone')}}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="form-floating">
                                                <label class="text-title">{{ translate('messages.field_name')}} <span
                                                class="input-label-secondary text-danger">*</span></label>
                                                <input type="text" class="form-control" name="field_name[]"
                                                        placeholder="{{ translate('messages.Ex:_Account_name')}} " value="" required>
                                            </div>
                                        </div>
                                        {{--<div class="col-md-4 col-12">
                                            <div class="form-floating">
                                                <label class="text-title">{{ translate('messages.placeholder_text')}} <span
                                                class="input-label-secondary text-danger">*</span></label>
                                                <input type="text" class="form-control" name="placeholder_text[]"
                                                        placeholder="{{ translate('messages.Ex:_John')}} " value="" required>
                                            </div>
                                        </div>--}}
                                        <div class="col-md-12 col-12">
                                            <div class="d-flex align-items-center justify-content-between pt-1">
                                                <div class="form-floating">
                                                    <label class="text-title">{{ translate('messages.placeholder_text')}} <span
                                                    class="input-label-secondary text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="placeholder_text[]"
                                                            placeholder="{{ translate('messages.Ex:_John')}} " value="" required>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input checkbox-theme single-select" type="checkbox" value="1" name="is_required[0]" id="flexCheckDefault__0" checked>
                                                    <label class="form-check-label" for="flexCheckDefault__0">
                                                        {{ translate('messages.Is_required_')}}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                    $('#method_name').val("");
                }

                counter = 1;
            })

            $(document).on('click', '.remove-field', function () {
                const fieldRowId = $(this).data('id');
                const rowEl = document.getElementById(`field-row--${fieldRowId}`);
                if (!rowEl) { counter--; return; }
                const duration = 250;
                if (typeof $ !== 'undefined' && $.fn && $.fn.slideUp) {
                    $(rowEl).slideUp(duration, function(){ this.remove(); });
                } else {
                    const h = rowEl.offsetHeight;
                    rowEl.style.height = h + 'px';
                    rowEl.style.transition = `height ${duration}ms ease, opacity ${duration}ms ease`;
                    rowEl.offsetHeight;
                    rowEl.style.height = '0px';
                    rowEl.style.opacity = '0';
                    setTimeout(function(){ rowEl.remove(); }, duration + 50);
                }
                counter--;
            });
        });
    </script>

     <script>
        jQuery(function($){
            const $sticky = $('.withdraw-header-sticky').first();
            if(!$sticky.length) return;
            const origTop = $sticky.offset().top;
            function update(){
                const st = $(window).scrollTop();
                $sticky.toggleClass('scrolling', st >= origTop);
            }
            $(window).on('scroll', update);
            update();
        });
    </script>
@endpush

