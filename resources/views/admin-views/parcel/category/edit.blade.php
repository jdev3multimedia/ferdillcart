@extends('layouts.admin.app')

@section('title', translate('messages.update_parcel_category'))


@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/edit.png') }}" class="w--26" alt="">
                </span>
                <span>
                    {{ translate('messages.update_parcel_category') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.parcel.category.update', [$parcel_category['id']]) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        @method('PUT')
                        @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
                        @php($language = $language->value ?? null)
                        @php($defaultLang = str_replace('_', '-', app()->getLocale()))
                        <div class="col-lg-12">
                            @if ($language)
                                @php($defaultLang = json_decode($language)[0])
                                <ul class="nav nav-tabs mb-4">
                                    <li class="nav-item">
                                        <a class="nav-link lang_link active" href="#"
                                            id="default-link">{{ translate('messages.default') }}</a>
                                    </li>
                                    @foreach (json_decode($language) as $lang)
                                        <li class="nav-item">
                                            <a class="nav-link lang_link" href="#"
                                                id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <div class="col-lg-6">
                            @if ($language)
                                <div class="lang_form" id="default-form">
                                    <div class="form-group">
                                        <label class="input-label" for="default_name">{{ translate('messages.name') }}
                                            ({{ translate('messages.default') }})</label>
                                        <input type="text" name="name[]" id="default_name" class="form-control"
                                            placeholder="{{ translate('messages.new_food') }}"
                                            value="{{ $parcel_category?->getRawOriginal('name') }}">
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.short_description') }}
                                            ({{ translate('messages.default') }})</label>
                                        <textarea type="text" name="description[]" class="form-control ckeditor">{!! $parcel_category?->getRawOriginal('description') !!}</textarea>
                                    </div>
                                </div>
                                @foreach (json_decode($language) as $lang)
                                    <?php
                                    if (count($parcel_category['translations'])) {
                                        $translate = [];
                                        foreach ($parcel_category['translations'] as $t) {
                                            if ($t->locale == $lang && $t->key == 'name') {
                                                $translate[$lang]['name'] = $t->value;
                                            }
                                            if ($t->locale == $lang && $t->key == 'description') {
                                                $translate[$lang]['description'] = $t->value;
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="d-none lang_form" id="{{ $lang }}-form">
                                        <div class="form-group">
                                            <label class="input-label"
                                                for="{{ $lang }}_name">{{ translate('messages.name') }}
                                                ({{ strtoupper($lang) }})</label>
                                            <input type="text" name="name[]" id="{{ $lang }}_name"
                                                class="form-control" placeholder="{{ translate('messages.new_food') }}"
                                                value="{{ $translate[$lang]['name'] ?? '' }}">
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{ $lang }}">
                                        <div class="form-group">
                                            <label class="input-label"
                                                for="exampleFormControlInput1">{{ translate('messages.short_description') }}
                                                ({{ strtoupper($lang) }})</label>
                                            <textarea type="text" name="description[]" class="form-control ckeditor">{!! $translate[$lang]['description'] ?? '' !!}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div id="default-form">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.name') }} (EN)</label>
                                        <input type="text" name="name[]" class="form-control"
                                            placeholder="{{ translate('messages.new_food') }}"
                                            value="{{ $parcel_category['name'] }}" required>
                                    </div>
                                    <input type="hidden" name="lang[]" value="en">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.short_description') }}</label>
                                        <textarea type="text" name="description[]" class="form-control ckeditor">{!! $parcel_category['description'] !!}</textarea>
                                    </div>
                                </div>
                            @endif
                            @if ($parcel_category->position == 0)
                            @endif
                        </div>
                        <div class="col-lg-6">
                            <div class="h-100 d-flex flex-column">
                                <label class="mb-0 mt-auto d-block text-center">
                                    {{ translate('messages.image') }}
                                    <small class="text-danger">* ( {{ translate('messages.ratio') }} 200x200 )</small>
                                </label>
                                <div class="text-center py-3 my-auto">
                                    <img class="img--130 onerror-image" id="viewer"
                                        src="{{ $parcel_category['image_full_url'] }}"
                                        data-onerror-image="{{ asset('/public/assets/admin/img/400x400/img2.jpg') }}" />
                                </div>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                        accept=".webp, .jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label"
                                        for="customFileEg1">{{ translate('messages.choose_file') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label
                                    class="input-label text-capitalize">{{ translate('messages.per_km_shipping_charge') }}</label>
                                <input type="number" step=".01" min="0"
                                    placeholder="{{ translate('messages.per_km_shipping_charge') }}" class="form-control"
                                    name="parcel_per_km_shipping_charge"
                                    value="{{ $parcel_category->parcel_per_km_shipping_charge }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label
                                    class="input-label text-capitalize">{{ translate('messages.minimum_shipping_charge') }}</label>
                                <input type="number" step=".01" min="0"
                                    placeholder="{{ translate('messages.minimum_shipping_charge') }}"
                                    class="form-control" name="parcel_minimum_shipping_charge"
                                    value="{{ $parcel_category->parcel_minimum_shipping_charge }}">
                            </div>
                        </div>
                        @if ($categoryWiseTax)
                                <div class="col-6">
                                    <span
                                        class="mb-2 d-block title-clr fw-normal">{{ translate('Select Tax Rate') }}</span>
                                    <select name="tax_ids[]" required id=""
                                        class="form-control js-select2-custom" multiple="multiple"
                                        placeholder="Type & Select Tax Rate">
                                        @foreach ($taxVats as $taxVat)
                                            <option {{ in_array($taxVat->id, $taxVatIds) ? 'selected' : '' }}
                                                value="{{ $taxVat->id }}"> {{ $taxVat->name }}
                                                ({{ $taxVat->tax_rate }}%)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                        @endif
                        <div class="col-12">
                            <div class="btn--container justify-content-end">
                                <button type="reset" id="reset_btn"
                                    class="btn btn--reset">{{ translate('messages.reset') }}</button>
                                <button type="submit"
                                    class="btn btn--primary">{{ translate('messages.update') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        "use strict";

        function readURL(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function() {
            readURL(this);
        });

        $(".lang_link").click(function(e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.substring(0, form_id.length - 5);
            console.log(lang);
            $("#" + lang + "-form").removeClass('d-none');
        });

        $('#reset_btn').click(function() {
            $('#module_id').val("{{ $parcel_category->module_id }}").trigger('change');
            $('#viewer').attr('src',
                "{{ asset('storage/app/public/parcel_category') }}/{{ $parcel_category['image'] }}");
        })
    </script>
@endpush
