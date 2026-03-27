@extends('layouts.admin.app')

@section('title',translate('messages.react_landing_page'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header pb-0">
            <div class="d-flex flex-wrap justify-content-between">
                <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/landing.png')}}" class="w--20" alt="">
                </span>
                    <span>
                    {{ translate('messages.react_landing_page') }}
                </span>
                </h1>
                <div class="text--primary-2 py-1 d-flex flex-wrap align-items-center" type="button" data-toggle="modal"
                     data-target="#how-it-works">
                    <strong class="mr-2">{{translate('See_how_it_works!')}}</strong>
                    <div>
                        <i class="tio-info-outined"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-20 mt-2">
            <div class="js-nav-scroller hs-nav-scroller-horizontal">
                @include('admin-views.business-settings.landing-page-settings.top-menu-links.react-landing-page-links')
            </div>
        </div>
        @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
        @php($language = $language->value ?? null)
        <div class="tab-content">
            <div class="tab-pane fade show active">
                <form class="custom-validation"
                      action="{{ route('admin.business-settings.review-react-update',[$review->id]) }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="card p-xxl-3 mb-20 border-0">
                        <div class="row g-3">
                            <div class="col-lg-8">
                                <div class="bg--secondary rounded h-100 p-xxl-4 p-3">
                                    @if($language)
                                        <ul class="nav nav-tabs mb-4 border-bottom">
                                            <li class="nav-item">
                                                <a class="nav-link lang_link active" href="#"
                                                   id="testimonial-default-link">{{translate('messages.default')}}</a>
                                            </li>
                                            @foreach (json_decode($language) as $lang)
                                                <li class="nav-item">
                                                    <a class="nav-link lang_link" href="#"
                                                       id="testimonial-{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if($language)
                                        <div class="row g-3 lang_form" id="testimonial-default-form">
                                            <div class="col-md-6">
                                                <label for="name"
                                                       class="form-label">{{translate('Reviewer Name')}}
                                                    ({{ translate('messages.default') }})
                                                    <span class="form-label-secondary" data-toggle="tooltip"
                                                          data-placement="right"
                                                          data-original-title="{{ translate('Content...') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span>
                                                    <span class="form-label-secondary text-danger"
                                                          data-toggle="tooltip" data-placement="right"
                                                          data-original-title="{{ translate('messages.Required.')}}"> *
                                                    </span>
                                                </label>
                                                <input id="name" type="text" name="name[]" value="{{$review?->name}}"
                                                       class="form-control"
                                                       placeholder="{{translate('Ex:  John Doe')}}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="designation"
                                                       class="form-label">{{translate('Designation')}}
                                                    ({{ translate('messages.default') }})
                                                    <span class="form-label-secondary" data-toggle="tooltip"
                                                          data-placement="right"
                                                          data-original-title="{{ translate('Content...') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span>

                                                </label>
                                                <input id="designation" value="{{$review?->designation}}" type="text"
                                                       name="designation[]"
                                                       class="form-control"
                                                       placeholder="{{translate('Ex:  CTO')}}">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="review"
                                                       class="form-label">{{translate('messages.review')}}
                                                    ({{ translate('messages.default') }})
                                                    <span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_140_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span>
                                                    <span class="form-label-secondary text-danger"
                                                          data-toggle="tooltip" data-placement="right"
                                                          data-original-title="{{ translate('messages.Required.')}}"> *
                                                    </span>
                                                </label>
                                                <textarea id="review" name="review[]" maxlength="200"
                                                          placeholder="{{translate('Very Good Company')}}"
                                                          class="form-control h92px"
                                                          required>{{$review?->review}}</textarea>
                                                <span
                                                    class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                            </div>
                                        </div>
                                        <input type="hidden" name="lang[]" value="default">
                                        @foreach(json_decode($language) as $lang)
                                                <?php
                                                $name_translate = [];
                                                $designation_translate = [];
                                                $review_translate = [];
                                                if (isset($review->translations) && count($review->translations)) {
                                                    foreach ($review->translations as $t) {
                                                        if ($t->locale == $lang && $t->key == 'name') {
                                                            $name_translate[$lang]['value'] = $t->value;
                                                        }
                                                    }
                                                }
                                                if (isset($review->translations) && count($review->translations)) {
                                                    foreach ($review->translations as $t) {
                                                        if ($t->locale == $lang && $t->key == 'designation') {
                                                            $designation_translate[$lang]['value'] = $t->value;
                                                        }
                                                    }
                                                }
                                                if (isset($review->translations) && count($review->translations)) {
                                                    foreach ($review->translations as $t) {
                                                        if ($t->locale == $lang && $t->key == 'review') {
                                                            $review_translate[$lang]['value'] = $t->value;
                                                        }
                                                    }
                                                }
                                                ?>
                                            <div class="row g-3 d-none lang_form"
                                                 id="testimonial-{{$lang}}-form">
                                                <div class="col-md-6">
                                                    <label for="name{{$lang}}"
                                                           class="form-label">{{translate('Reviewer Name')}}
                                                        ({{strtoupper($lang)}})
                                                        <span class="form-label-secondary" data-toggle="tooltip"
                                                              data-placement="right"
                                                              data-original-title="{{ translate('Content...') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span>
                                                    </label>
                                                    <input id="name{{$lang}}" type="text" name="name[]"
                                                           value="{{$name_translate[$lang]['value']??''}}"
                                                           class="form-control"
                                                           placeholder="{{translate('Ex:  John Doe')}}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="designation{{$lang}}"
                                                           class="form-label">{{translate('Designation')}}
                                                        ({{strtoupper($lang)}})
                                                        <span class="form-label-secondary" data-toggle="tooltip"
                                                              data-placement="right"
                                                              data-original-title="{{ translate('Content...') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span>
                                                    </label>
                                                    <input id="designation{{$lang}}" type="text"
                                                           value="{{$designation_translate[$lang]['value'] ?? ''}}"
                                                           name="designation[]"
                                                           class="form-control"
                                                           placeholder="{{translate('Ex:  CTO')}}">
                                                </div>
                                                <div class="col-md-12">
                                                    <label for="review{{$lang}}"
                                                           class="form-label">{{translate('messages.review')}}
                                                        ({{strtoupper($lang)}})
                                                        <span
                                                            class="form-label-secondary" data-toggle="tooltip"
                                                            data-placement="right"
                                                            data-original-title="{{ translate('Write_the_title_within_140_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span></label>
                                                    <textarea id="review{{$lang}}" name="review[]"
                                                              maxlength="200"
                                                              placeholder="{{translate('Very Good Company')}}"
                                                              class="form-control h92px">{{$review_translate[$lang]['value']??''}}</textarea>
                                                    <span
                                                        class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                                </div>
                                            </div>
                                            <input type="hidden" name="lang[]" value="{{$lang}}">
                                        @endforeach
                                    @else
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="name"
                                                       class="form-label">{{translate('Reviewer Name')}}
                                                    <span class="form-label-secondary" data-toggle="tooltip"
                                                          data-placement="right"
                                                          data-original-title="{{ translate('Content...') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span>
                                                </label>
                                                <input id="name" type="text" name="name[]" class="form-control"
                                                       placeholder="{{translate('Ex:  John Doe')}}">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="designation"
                                                       class="form-label">{{translate('Designation')}}
                                                    <span class="form-label-secondary" data-toggle="tooltip"
                                                          data-placement="right"
                                                          data-original-title="{{ translate('Content...') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span>
                                                </label>
                                                <input id="designation" type="text" name="designation[]"
                                                       class="form-control"
                                                       placeholder="{{translate('Ex:  CTO')}}">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="review"
                                                       class="form-label">{{translate('messages.review')}}<span
                                                        class="form-label-secondary" data-toggle="tooltip"
                                                        data-placement="right"
                                                        data-original-title="{{ translate('Write_the_title_within_140_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span></label>
                                                <textarea id="review" name="review[]" maxlength="200"
                                                          placeholder="{{translate('Very Good Company')}}"
                                                          class="form-control h92px"></textarea>
                                                <span
                                                    class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                            </div>
                                        </div>
                                        <input type="hidden" name="lang[]" value="default">
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="bg--secondary h-100 rounded p-md-4 p-3 d-center">
                                    <div class="text-center">
                                        <div class="mb-30">
                                            <h5 class="mb-1">{{ translate('Reviewer Image') }}</h5>
                                            <p class="mb-0 fs-12 gray-dark">{{ translate('Upload Reviewer Image') }}
                                            </p>
                                        </div>
                                        <div class="mx-auto text-center error-wrapper">
                                            <div class="upload-file_custom ratio-1 h-100px">
                                                <input type="file" name="reviewer_image"
                                                       class="upload-file__input single_file_input"
                                                       accept=".webp, .jpg, .jpeg, .png, .gif" {{$review?->reviewer_image ? '':'required'}}>
                                                <label class="upload-file__wrapper w-100 h-100 m-0">
                                                    <div class="upload-file-textbox text-center" style="">
                                                        <img width="22" class="svg"
                                                             src="{{asset('public/assets/admin/img/document-upload.svg')}}"
                                                             alt="img">
                                                        <h6
                                                            class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                            <span class="theme-clr">Click to upload</span>
                                                            <br>
                                                            Or drag and drop
                                                        </h6>
                                                    </div>
                                                    <img class="upload-file-img" loading="lazy"
                                                         src="{{$review->reviewer_image ? $review->reviewer_image_full_url:''}}"
                                                         data-default-src="{{$review->reviewer_image ? $review->reviewer_image_full_url:''}}"
                                                         alt="" style="display: none;">
                                                </label>
                                                <div class="overlay">
                                                    <div
                                                        class="d-flex gap-1 justify-content-center align-items-center h-100">
                                                        <button type="button"
                                                                class="btn btn-outline-info icon-btn view_btn">
                                                            <i class="tio-invisible"></i>
                                                        </button>
                                                        <button type="button"
                                                                class="btn btn-outline-info icon-btn edit_btn">
                                                            <i class="tio-edit"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="fs-10 text-center mb-0 mt-lg-4 mt-3">
                                            {{ translate('JPG, JPEG, PNG size : Max 2 MB')}} <span
                                                class="font-medium text-title">{{ translate('(2:1)')}}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btn--container justify-content-end mt-20">
                            <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                            <button type="submit" class="btn btn--primary mb-2">{{translate('Update')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- How it Works -->
    @include('admin-views.business-settings.landing-page-settings.partial.how-it-work-react')
@endsection
@push('script_2')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var removeBtn = document.getElementById('remove_image_btn');
            var removeFlag = document.getElementById('image_remove');
            var fileInput = document.querySelector('input[name="image"]');
            var form = fileInput ? fileInput.closest('form') : null;

            if (removeBtn && removeFlag) {
                removeBtn.addEventListener('click', function () {
                    removeFlag.value = '1';
                    if (fileInput) {
                        fileInput.removeAttribute('disabled');
                        fileInput.setAttribute('required', 'required');
                        fileInput.value = '';
                        fileInput.closest('.upload-file__wrapper').querySelector('.upload-file-textbox').style.display = 'block';
                    }
                });
            }

            if (form && removeFlag) {
                form.addEventListener('reset', function () {
                    removeFlag.value = '0';
                });
            }

            if (fileInput && removeFlag) {
                fileInput.addEventListener('change', function () {
                    removeFlag.value = '0';
                });
            }
        });
    </script>
@endpush
