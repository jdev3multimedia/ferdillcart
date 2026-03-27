@extends('layouts.admin.app')

@section('title',translate('messages.new_page'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">

@endpush

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
            <div class="text--primary-2 py-1 d-flex flex-wrap align-items-center" type="button" data-toggle="modal" data-target="#how-it-works">
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
    <div class="card py-3 px-xxl-4 px-3 mb-20">
        <div class="d-flex flex-sm-nowrap flex-wrap gap-3 align-items-center justify-content-between">
            <div class="">
                <h3 class="mb-1">{{ translate('Popular Clients Section') }}</h3>
                <p class="mb-0 gray-dark fs-12">
                    {{ translate('See how your Popular Clients Section will look to customers.') }}
                </p>
            </div>
            <div class="max-w-300px ml-sm-auto">
                <button type="button" class="btn btn-outline-primary py-2 fs-12 px-3 offcanvas-trigger" data-target="#clients_section">
                    <i class="tio-invisible"></i> {{ translate('Section Preview') }}
                </button>
            </div>
        </div>
    </div>

    <div class="card mb-20">
        <div class="card-body">
            <div class="row g-3 justify-content-between align-items-center">
                <div class="col-lg-8 col-md-7 col-sm-7">
                    <div>
                        <h3 class="mb-1">{{translate('messages.Popular Clients Section') }}</h3>
                        <p class="m-0 fs-12 color-656566">{{ translate('If you turn of the availability status, this section will not show in the website') }}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5 col-sm-5">
                    <label class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                        <span class="pr-1 d-flex align-items-center switch--label">
                            <span class="line--limit-1 text--primary">
                                {{translate('messages.Status') }}
                            </span>
                        </span>
                        <input type="checkbox"  class="status toggle-switch-input" value="1" name="" id="" checked>
                        <span class="toggle-switch-label text">
                            <span class="toggle-switch-indicator"></span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-20">
        <div class="card-body">
            <div class="mb-20">
                <h3 class="mb-1">{{ translate('Popular Clients Section Content') }}</h3>
                <p class="mb-0 gray-dark fs-12">
                    {{ translate('Showcase your top clients and partners to build trust and credibility.') }}
                </p>
            </div>
            <div class="bg--secondary h-100 rounded p-md-4 p-3 mb-20">
                <ul class="nav nav-tabs mb-4 border-bottom">
                    <li class="nav-item">
                        <a class="nav-link lang_link active" href="#" id="default-link">{{translate('messages.default')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link lang_link" href="#" id="">{{translate('messages.English')}} ({{ translate('messages.EN') }})</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link lang_link" href="#" id="">{{translate('messages.Arabic')}} ({{ translate('messages.(AR)') }})</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link lang_link" href="#" id="">{{translate('messages.Spanish')}} ({{ translate('messages.(ES)') }})</a>
                    </li>
                </ul>
                <div class="row g-1 lang_form default-form">
                    <div class="col-sm-12">
                        <label for=""  class="form-label">{{translate('Title')}} ({{ translate('messages.default') }})
                        <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_20_characters') }}">
                                    <i class="tio-info color-A7A7A7"></i>
                                </span>
                                <span class="form-label-secondary text-danger"
                                data-toggle="tooltip" data-placement="right"
                                data-original-title="{{ translate('messages.Required.')}}"> 
                                </span>
                            </label>
                        <textarea id="" type="text"  maxlength="100" name="[]" class="form-control min-h-45px" value="" rows="1" placeholder="{{translate('messages.title_here...')}}"></textarea>
                        <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                    </div>
                    <div class="col-sm-12">
                        <label for=""  class="form-label">{{translate('Sub Title')}} ({{ translate('messages.default') }})
                        <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_sub_title_within_30_characters') }}">
                                    <i class="tio-info color-A7A7A7"></i>
                                </span><span class="form-label-secondary text-danger"
                                data-toggle="tooltip" data-placement="right"
                                data-original-title="{{ translate('messages.Required.')}}"> 
                                </span>
                        </label>
                        <textarea id="" type="text"  maxlength="200" name="" class="form-control min-h-45px" value="" rows="1" placeholder="{{translate('messages.sub_title_here...')}}"></textarea>
                        <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                    </div>
                </div>
            </div>
            <div class="bg--secondary h-100 rounded p-md-4 p-3">
                <div class="mb-20">
                    <h5 class="mb-1">{{ translate('Clients Section Image') }}</h5>
                    <p class="mb-0 gray-dark fs-12">
                        {{ translate('JPG, JPEG, PNG, Gif Image size : Max 2 MB') }}
                    </p>
                </div>
                 <!-- Product Image 2 -->
                <div class="d-flex flex-wrap __gap-12px __new-coba" id="coba">
                    
                </div>
            </div>
            <div class="btn--container justify-content-end mt-20">
                <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                <button type="submit"   class="btn btn--primary mb-2">{{translate('Save')}}</button>
            </div>
        </div>
        <!--- Old Code -->
        <!-- <div class="card">
            <form action="#0">
                <div class="card-header">
                    <div class="">
                        <h3 class="mb-1">{{ translate('Clients Section Image') }}</h3>
                        <p class="mb-0 gray-dark fs-12">
                            {{ translate('Showcase your top clients and partners to build trust and credibility.') }}
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-4 col-xl-4 col-xxl-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="w-100 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                        <h3 class="mb-0">{{ translate('Client 1') }}</h3>                                
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="bg--secondary h-100 rounded p-4">
                                        <div class="text-center py-1">                            
                                            <div class="mx-auto text-center">
                                                <div class="mb-30">
                                                    <h5 class="mb-1">{{ translate('Upload Image') }}</h5>
                                                    <p class="mb-0 fs-12 gray-dark">{{ translate('Upload 1st Card  Image') }}</p>
                                                </div>
                                                <div class="upload-file_custom">
                                                    <input type="file" name="image" class="upload-file__input single_file_input"
                                                            accept=".webp, .jpg, .jpeg, .png, .gif" required>
                                                    <label class="upload-file__wrapper ratio-1 m-0">
                                                        <div class="upload-file-textbox text-center">
                                                            <img width="22" class="svg" src="{{asset('public/assets/admin/img/document-upload.svg')}}" alt="img">
                                                            <h6 class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                                <span class="theme-clr">Click to upload</span>
                                                                <br>
                                                                Or drag and drop
                                                            </h6>
                                                        </div>
                                                        <img class="upload-file-img" loading="lazy" src="" data-default-src="" alt="" style="display: none;">
                                                    </label>
                                                    <div class="overlay">
                                                        <div class="d-flex gap-1 justify-content-center align-items-center h-100">
                                                            <button type="button" class="btn btn-outline-info icon-btn view_btn">
                                                                <i class="tio-invisible"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-info icon-btn edit_btn">
                                                                <i class="tio-edit"></i>
                                                            </button>
                                                            <button type="button" class="remove_btn btn icon-btn">
                                                                <i class="tio-delete text-danger"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="fs-10 text-center mb-0 mt-lg-4 mt-3">
                                                <span class="font-medium color-656566">{{ translate('Ratio (1:1)')}}</span>
                                            </p>
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4 col-xxl-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="w-100 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                        <h3 class="mb-0">{{ translate('Client 2') }}</h3>                                
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="bg--secondary h-100 rounded p-4">
                                        <div class="text-center py-1">                            
                                            <div class="mx-auto text-center">
                                                <div class="mb-30">
                                                    <h5 class="mb-1">{{ translate('Upload Image') }}</h5>
                                                    <p class="mb-0 fs-12 gray-dark">{{ translate('Upload 1st Card  Image') }}</p>
                                                </div>
                                                <div class="upload-file_custom">
                                                    <input type="file" name="image" class="upload-file__input single_file_input"
                                                            accept=".webp, .jpg, .jpeg, .png, .gif" required>
                                                    <label class="upload-file__wrapper ratio-1 m-0">
                                                        <div class="upload-file-textbox text-center">
                                                            <img width="22" class="svg" src="{{asset('public/assets/admin/img/document-upload.svg')}}" alt="img">
                                                            <h6 class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                                <span class="theme-clr">Click to upload</span>
                                                                <br>
                                                                Or drag and drop
                                                            </h6>
                                                        </div>
                                                        <img class="upload-file-img" loading="lazy" src="" data-default-src="" alt="" style="display: none;">
                                                    </label>
                                                    <div class="overlay">
                                                        <div class="d-flex gap-1 justify-content-center align-items-center h-100">
                                                            <button type="button" class="btn btn-outline-info icon-btn view_btn">
                                                                <i class="tio-invisible"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-info icon-btn edit_btn">
                                                                <i class="tio-edit"></i>
                                                            </button>
                                                            <button type="button" class="remove_btn btn icon-btn">
                                                                <i class="tio-delete text-danger"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="fs-10 text-center mb-0 mt-lg-4 mt-3">
                                                <span class="font-medium color-656566">{{ translate('Ratio (1:1)')}}</span>
                                            </p>
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4 col-xxl-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="w-100 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                        <h3 class="mb-0">{{ translate('Client 3') }}</h3>                                
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="bg--secondary h-100 rounded p-4">
                                        <div class="text-center py-1">                            
                                            <div class="mx-auto text-center">
                                                <div class="mb-30">
                                                    <h5 class="mb-1">{{ translate('Upload Image') }}</h5>
                                                    <p class="mb-0 fs-12 gray-dark">{{ translate('Upload 1st Card  Image') }}</p>
                                                </div>
                                                <div class="upload-file_custom">
                                                    <input type="file" name="image" class="upload-file__input single_file_input"
                                                            accept=".webp, .jpg, .jpeg, .png, .gif" required>
                                                    <label class="upload-file__wrapper ratio-1 m-0">
                                                        <div class="upload-file-textbox text-center">
                                                            <img width="22" class="svg" src="{{asset('public/assets/admin/img/document-upload.svg')}}" alt="img">
                                                            <h6 class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                                <span class="theme-clr">Click to upload</span>
                                                                <br>
                                                                Or drag and drop
                                                            </h6>
                                                        </div>
                                                        <img class="upload-file-img" loading="lazy" src="" data-default-src="" alt="" style="display: none;">
                                                    </label>
                                                    <div class="overlay">
                                                        <div class="d-flex gap-1 justify-content-center align-items-center h-100">
                                                            <button type="button" class="btn btn-outline-info icon-btn view_btn">
                                                                <i class="tio-invisible"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-info icon-btn edit_btn">
                                                                <i class="tio-edit"></i>
                                                            </button>
                                                            <button type="button" class="remove_btn btn icon-btn">
                                                                <i class="tio-delete text-danger"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="fs-10 text-center mb-0 mt-lg-4 mt-3">
                                                <span class="font-medium color-656566">{{ translate('Ratio (1:1)')}}</span>
                                            </p>
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4 col-xxl-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="w-100 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                        <h3 class="mb-0">{{ translate('Client 4') }}</h3>                                
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="bg--secondary h-100 rounded p-4">
                                        <div class="text-center py-1">                            
                                            <div class="mx-auto text-center">
                                                <div class="mb-30">
                                                    <h5 class="mb-1">{{ translate('Upload Image') }}</h5>
                                                    <p class="mb-0 fs-12 gray-dark">{{ translate('Upload 1st Card  Image') }}</p>
                                                </div>
                                                <div class="upload-file_custom">
                                                    <input type="file" name="image" class="upload-file__input single_file_input"
                                                            accept=".webp, .jpg, .jpeg, .png, .gif" required>
                                                    <label class="upload-file__wrapper ratio-1 m-0">
                                                        <div class="upload-file-textbox text-center">
                                                            <img width="22" class="svg" src="{{asset('public/assets/admin/img/document-upload.svg')}}" alt="img">
                                                            <h6 class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                                <span class="theme-clr">Click to upload</span>
                                                                <br>
                                                                Or drag and drop
                                                            </h6>
                                                        </div>
                                                        <img class="upload-file-img" loading="lazy" src="" data-default-src="" alt="" style="display: none;">
                                                    </label>
                                                    <div class="overlay">
                                                        <div class="d-flex gap-1 justify-content-center align-items-center h-100">
                                                            <button type="button" class="btn btn-outline-info icon-btn view_btn">
                                                                <i class="tio-invisible"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-info icon-btn edit_btn">
                                                                <i class="tio-edit"></i>
                                                            </button>
                                                            <button type="button" class="remove_btn btn icon-btn">
                                                                <i class="tio-delete text-danger"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="fs-10 text-center mb-0 mt-lg-4 mt-3">
                                                <span class="font-medium color-656566">{{ translate('Ratio (1:1)')}}</span>
                                            </p>
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4 col-xxl-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="w-100 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                        <h3 class="mb-0">{{ translate('Client 5') }}</h3>                                
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="bg--secondary h-100 rounded p-4">
                                        <div class="text-center py-1">                            
                                            <div class="mx-auto text-center">
                                                <div class="mb-30">
                                                    <h5 class="mb-1">{{ translate('Upload Image') }}</h5>
                                                    <p class="mb-0 fs-12 gray-dark">{{ translate('Upload 1st Card  Image') }}</p>
                                                </div>
                                                <div class="upload-file_custom">
                                                    <input type="file" name="image" class="upload-file__input single_file_input"
                                                            accept=".webp, .jpg, .jpeg, .png, .gif" required>
                                                    <label class="upload-file__wrapper ratio-1 m-0">
                                                        <div class="upload-file-textbox text-center">
                                                            <img width="22" class="svg" src="{{asset('public/assets/admin/img/document-upload.svg')}}" alt="img">
                                                            <h6 class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                                <span class="theme-clr">Click to upload</span>
                                                                <br>
                                                                Or drag and drop
                                                            </h6>
                                                        </div>
                                                        <img class="upload-file-img" loading="lazy" src="" data-default-src="" alt="" style="display: none;">
                                                    </label>
                                                    <div class="overlay">
                                                        <div class="d-flex gap-1 justify-content-center align-items-center h-100">
                                                            <button type="button" class="btn btn-outline-info icon-btn view_btn">
                                                                <i class="tio-invisible"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-info icon-btn edit_btn">
                                                                <i class="tio-edit"></i>
                                                            </button>
                                                            <button type="button" class="remove_btn btn icon-btn">
                                                                <i class="tio-delete text-danger"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="fs-10 text-center mb-0 mt-lg-4 mt-3">
                                                <span class="font-medium color-656566">{{ translate('Ratio (1:1)')}}</span>
                                            </p>
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4 col-xxl-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="w-100 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                        <h3 class="mb-0">{{ translate('Client 6') }}</h3>                                
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="bg--secondary h-100 rounded p-4">
                                        <div class="text-center py-1">                            
                                            <div class="mx-auto text-center">
                                                <div class="mb-30">
                                                    <h5 class="mb-1">{{ translate('Upload Image') }}</h5>
                                                    <p class="mb-0 fs-12 gray-dark">{{ translate('Upload 1st Card  Image') }}</p>
                                                </div>
                                                <div class="upload-file_custom">
                                                    <input type="file" name="image" class="upload-file__input single_file_input"
                                                            accept=".webp, .jpg, .jpeg, .png, .gif" required>
                                                    <label class="upload-file__wrapper ratio-1 m-0">
                                                        <div class="upload-file-textbox text-center">
                                                            <img width="22" class="svg" src="{{asset('public/assets/admin/img/document-upload.svg')}}" alt="img">
                                                            <h6 class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                                <span class="theme-clr">Click to upload</span>
                                                                <br>
                                                                Or drag and drop
                                                            </h6>
                                                        </div>
                                                        <img class="upload-file-img" loading="lazy" src="" data-default-src="" alt="" style="display: none;">
                                                    </label>
                                                    <div class="overlay">
                                                        <div class="d-flex gap-1 justify-content-center align-items-center h-100">
                                                            <button type="button" class="btn btn-outline-info icon-btn view_btn">
                                                                <i class="tio-invisible"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-info icon-btn edit_btn">
                                                                <i class="tio-edit"></i>
                                                            </button>
                                                            <button type="button" class="remove_btn btn icon-btn">
                                                                <i class="tio-delete text-danger"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="fs-10 text-center mb-0 mt-lg-4 mt-3">
                                                <span class="font-medium color-656566">{{ translate('Ratio (1:1)')}}</span>
                                            </p>
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4 col-xxl-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="w-100 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                        <h3 class="mb-0">{{ translate('Client 7') }}</h3>                                
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="bg--secondary h-100 rounded p-4">
                                        <div class="text-center py-1">                            
                                            <div class="mx-auto text-center">
                                                <div class="mb-30">
                                                    <h5 class="mb-1">{{ translate('Upload Image') }}</h5>
                                                    <p class="mb-0 fs-12 gray-dark">{{ translate('Upload 1st Card  Image') }}</p>
                                                </div>
                                                <div class="upload-file_custom">
                                                    <input type="file" name="image" class="upload-file__input single_file_input"
                                                            accept=".webp, .jpg, .jpeg, .png, .gif" required>
                                                    <label class="upload-file__wrapper ratio-1 m-0">
                                                        <div class="upload-file-textbox text-center">
                                                            <img width="22" class="svg" src="{{asset('public/assets/admin/img/document-upload.svg')}}" alt="img">
                                                            <h6 class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                                <span class="theme-clr">Click to upload</span>
                                                                <br>
                                                                Or drag and drop
                                                            </h6>
                                                        </div>
                                                        <img class="upload-file-img" loading="lazy" src="" data-default-src="" alt="" style="display: none;">
                                                    </label>
                                                    <div class="overlay">
                                                        <div class="d-flex gap-1 justify-content-center align-items-center h-100">
                                                            <button type="button" class="btn btn-outline-info icon-btn view_btn">
                                                                <i class="tio-invisible"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-info icon-btn edit_btn">
                                                                <i class="tio-edit"></i>
                                                            </button>
                                                            <button type="button" class="remove_btn btn icon-btn">
                                                                <i class="tio-delete text-danger"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="fs-10 text-center mb-0 mt-lg-4 mt-3">
                                                <span class="font-medium color-656566">{{ translate('Ratio (1:1)')}}</span>
                                            </p>
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4 col-xxl-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="w-100 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                        <h3 class="mb-0">{{ translate('Client 8') }}</h3>                                
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="bg--secondary h-100 rounded p-4">
                                        <div class="text-center py-1">                            
                                            <div class="mx-auto text-center">
                                                <div class="mb-30">
                                                    <h5 class="mb-1">{{ translate('Upload Image') }}</h5>
                                                    <p class="mb-0 fs-12 gray-dark">{{ translate('Upload 1st Card  Image') }}</p>
                                                </div>
                                                <div class="upload-file_custom">
                                                    <input type="file" name="image" class="upload-file__input single_file_input"
                                                            accept=".webp, .jpg, .jpeg, .png, .gif" required>
                                                    <label class="upload-file__wrapper ratio-1 m-0">
                                                        <div class="upload-file-textbox text-center">
                                                            <img width="22" class="svg" src="{{asset('public/assets/admin/img/document-upload.svg')}}" alt="img">
                                                            <h6 class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                                <span class="theme-clr">Click to upload</span>
                                                                <br>
                                                                Or drag and drop
                                                            </h6>
                                                        </div>
                                                        <img class="upload-file-img" loading="lazy" src="" data-default-src="" alt="" style="display: none;">
                                                    </label>
                                                    <div class="overlay">
                                                        <div class="d-flex gap-1 justify-content-center align-items-center h-100">
                                                            <button type="button" class="btn btn-outline-info icon-btn view_btn">
                                                                <i class="tio-invisible"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-info icon-btn edit_btn">
                                                                <i class="tio-edit"></i>
                                                            </button>
                                                            <button type="button" class="remove_btn btn icon-btn">
                                                                <i class="tio-delete text-danger"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="fs-10 text-center mb-0 mt-lg-4 mt-3">
                                                <span class="font-medium color-656566">{{ translate('Ratio (1:1)')}}</span>
                                            </p>
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4 col-xxl-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="w-100 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                        <h3 class="mb-0">{{ translate('Client 9') }}</h3>                                
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="bg--secondary h-100 rounded p-4">
                                        <div class="text-center py-1">                            
                                            <div class="mx-auto text-center">
                                                <div class="mb-30">
                                                    <h5 class="mb-1">{{ translate('Upload Image') }}</h5>
                                                    <p class="mb-0 fs-12 gray-dark">{{ translate('Upload 1st Card  Image') }}</p>
                                                </div>
                                                <div class="upload-file_custom">
                                                    <input type="file" name="image" class="upload-file__input single_file_input"
                                                            accept=".webp, .jpg, .jpeg, .png, .gif" required>
                                                    <label class="upload-file__wrapper ratio-1 m-0">
                                                        <div class="upload-file-textbox text-center">
                                                            <img width="22" class="svg" src="{{asset('public/assets/admin/img/document-upload.svg')}}" alt="img">
                                                            <h6 class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                                <span class="theme-clr">Click to upload</span>
                                                                <br>
                                                                Or drag and drop
                                                            </h6>
                                                        </div>
                                                        <img class="upload-file-img" loading="lazy" src="" data-default-src="" alt="" style="display: none;">
                                                    </label>
                                                    <div class="overlay">
                                                        <div class="d-flex gap-1 justify-content-center align-items-center h-100">
                                                            <button type="button" class="btn btn-outline-info icon-btn view_btn">
                                                                <i class="tio-invisible"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-info icon-btn edit_btn">
                                                                <i class="tio-edit"></i>
                                                            </button>
                                                            <button type="button" class="remove_btn btn icon-btn">
                                                                <i class="tio-delete text-danger"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="fs-10 text-center mb-0 mt-lg-4 mt-3">
                                                <span class="font-medium color-656566">{{ translate('Ratio (1:1)')}}</span>
                                            </p>
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4 col-xxl-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="w-100 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                        <h3 class="mb-0">{{ translate('Client 10') }}</h3>                                
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="bg--secondary h-100 rounded p-4">
                                        <div class="text-center py-1">                            
                                            <div class="mx-auto text-center">
                                                <div class="mb-30">
                                                    <h5 class="mb-1">{{ translate('Upload Image') }}</h5>
                                                    <p class="mb-0 fs-12 gray-dark">{{ translate('Upload 1st Card  Image') }}</p>
                                                </div>
                                                <div class="upload-file_custom">
                                                    <input type="file" name="image" class="upload-file__input single_file_input"
                                                            accept=".webp, .jpg, .jpeg, .png, .gif" required>
                                                    <label class="upload-file__wrapper ratio-1 m-0">
                                                        <div class="upload-file-textbox text-center">
                                                            <img width="22" class="svg" src="{{asset('public/assets/admin/img/document-upload.svg')}}" alt="img">
                                                            <h6 class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                                <span class="theme-clr">Click to upload</span>
                                                                <br>
                                                                Or drag and drop
                                                            </h6>
                                                        </div>
                                                        <img class="upload-file-img" loading="lazy" src="" data-default-src="" alt="" style="display: none;">
                                                    </label>
                                                    <div class="overlay">
                                                        <div class="d-flex gap-1 justify-content-center align-items-center h-100">
                                                            <button type="button" class="btn btn-outline-info icon-btn view_btn">
                                                                <i class="tio-invisible"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-info icon-btn edit_btn">
                                                                <i class="tio-edit"></i>
                                                            </button>
                                                            <button type="button" class="remove_btn btn icon-btn">
                                                                <i class="tio-delete text-danger"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="fs-10 text-center mb-0 mt-lg-4 mt-3">
                                                <span class="font-medium color-656566">{{ translate('Ratio (1:1)')}}</span>
                                            </p>
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4 col-xxl-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="w-100 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                        <h3 class="mb-0">{{ translate('Client 11') }}</h3>                                
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="bg--secondary h-100 rounded p-4">
                                        <div class="text-center py-1">                            
                                            <div class="mx-auto text-center">
                                                <div class="mb-30">
                                                    <h5 class="mb-1">{{ translate('Upload Image') }}</h5>
                                                    <p class="mb-0 fs-12 gray-dark">{{ translate('Upload 1st Card  Image') }}</p>
                                                </div>
                                                <div class="upload-file_custom">
                                                    <input type="file" name="image" class="upload-file__input single_file_input"
                                                            accept=".webp, .jpg, .jpeg, .png, .gif" required>
                                                    <label class="upload-file__wrapper ratio-1 m-0">
                                                        <div class="upload-file-textbox text-center">
                                                            <img width="22" class="svg" src="{{asset('public/assets/admin/img/document-upload.svg')}}" alt="img">
                                                            <h6 class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                                <span class="theme-clr">Click to upload</span>
                                                                <br>
                                                                Or drag and drop
                                                            </h6>
                                                        </div>
                                                        <img class="upload-file-img" loading="lazy" src="" data-default-src="" alt="" style="display: none;">
                                                    </label>
                                                    <div class="overlay">
                                                        <div class="d-flex gap-1 justify-content-center align-items-center h-100">
                                                            <button type="button" class="btn btn-outline-info icon-btn view_btn">
                                                                <i class="tio-invisible"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-info icon-btn edit_btn">
                                                                <i class="tio-edit"></i>
                                                            </button>
                                                            <button type="button" class="remove_btn btn icon-btn">
                                                                <i class="tio-delete text-danger"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="fs-10 text-center mb-0 mt-lg-4 mt-3">
                                                <span class="font-medium color-656566">{{ translate('Ratio (1:1)')}}</span>
                                            </p>
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4 col-xxl-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="w-100 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                        <h3 class="mb-0">{{ translate('Client 12') }}</h3>                                
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="bg--secondary h-100 rounded p-4">
                                        <div class="text-center py-1">                            
                                            <div class="mx-auto text-center">
                                                <div class="mb-30">
                                                    <h5 class="mb-1">{{ translate('Upload Image') }}</h5>
                                                    <p class="mb-0 fs-12 gray-dark">{{ translate('Upload 1st Card  Image') }}</p>
                                                </div>
                                                <div class="upload-file_custom">
                                                    <input type="file" name="image" class="upload-file__input single_file_input"
                                                            accept=".webp, .jpg, .jpeg, .png, .gif" required>
                                                    <label class="upload-file__wrapper ratio-1 m-0">
                                                        <div class="upload-file-textbox text-center">
                                                            <img width="22" class="svg" src="{{asset('public/assets/admin/img/document-upload.svg')}}" alt="img">
                                                            <h6 class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                                                                <span class="theme-clr">Click to upload</span>
                                                                <br>
                                                                Or drag and drop
                                                            </h6>
                                                        </div>
                                                        <img class="upload-file-img" loading="lazy" src="" data-default-src="" alt="" style="display: none;">
                                                    </label>
                                                    <div class="overlay">
                                                        <div class="d-flex gap-1 justify-content-center align-items-center h-100">
                                                            <button type="button" class="btn btn-outline-info icon-btn view_btn">
                                                                <i class="tio-invisible"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-info icon-btn edit_btn">
                                                                <i class="tio-edit"></i>
                                                            </button>
                                                            <button type="button" class="remove_btn btn icon-btn">
                                                                <i class="tio-delete text-danger"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="fs-10 text-center mb-0 mt-lg-4 mt-3">
                                                <span class="font-medium color-656566">{{ translate('Ratio (1:1)')}}</span>
                                            </p>
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end mt-20">
                        <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                        <button type="submit"   class="btn btn--primary mb-2">{{translate('Save')}}</button>
                    </div>
                </div>
            </form>
        </div>   -->
    </div>

<!-- Section View Offcanvas here -->
<div id="clients_section" class="custom-offcanvas offcanvas-750 d-flex flex-column justify-content-between">
    <form action="{{ route('taxvat.store') }}" method="post">
        <div>
            <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                <div class="py-1">
                    <h3 class="mb-0 line--limit-1">{{ translate('messages.Popular Clients Section Preview') }}</h3>
                </div>
                <button type="button" class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"aria-label="Close">
                    &times;
                </button>
            </div>
            <div class="custom-offcanvas-body custom-offcanvas-body-100  p-20">
                <section class="common-section-view bg-white border rounded-10">
                    <div class="mb-4 text-center">
                        <h2 class="mb-lg-1 mb-1 fs-24">
                            Our Popular <span class="text-base-clr">Clients</span> 
                        </h2>
                        <p class="text-title fs-14 m-0">
                            Trusted by leading brands for fast and reliable delivery servies.
                        </p>
                    </div>
                    <div class="common-carousel-wrapper position-relative">
                        <div class="clients-preview-slide owl-theme owl-carousel">
                            <div class="items__">
                                <div class="p-xxl-3 p-2 d-center h-135px bg--secondary rounded">
                                    <img wdith="110" height="100" src="{{ asset('/public/assets/admin/img/400x400/react-new-slide1.jpg') }}" alt="" class="rounded">                                                                       
                                </div>
                            </div>
                            <div class="items__">
                                <div class="p-xxl-3 p-2 d-center h-135px bg--secondary rounded">
                                    <img wdith="110" height="100" src="{{ asset('/public/assets/admin/img/400x400/react-new-slide2.jpg') }}" alt="" class="rounded">                                                                       
                                </div>
                            </div>
                            <div class="items__">
                                <div class="p-xxl-3 p-2 d-center h-135px bg--secondary rounded">
                                    <img wdith="110" height="100" src="{{ asset('/public/assets/admin/img/400x400/react-new-slide3.jpg') }}" alt="" class="rounded">                                                                       
                                </div>
                            </div>
                            <div class="items__">
                                <div class="p-xxl-3 p-2 d-center h-135px bg--secondary rounded">
                                    <img wdith="110" height="100" src="{{ asset('/public/assets/admin/img/400x400/react-new-slide4.jpg') }}" alt="" class="rounded">                                                                       
                                </div>
                            </div>
                            <div class="items__">
                                <div class="p-xxl-3 p-2 d-center h-135px bg--secondary rounded">
                                    <img wdith="110" height="100" src="{{ asset('/public/assets/admin/img/400x400/react-new-slide5.jpg') }}" alt="" class="rounded">                                                                       
                                </div>
                            </div>
                            <div class="items__">
                                <div class="p-xxl-3 p-2 d-center h-135px bg--secondary rounded">
                                    <img wdith="110" height="100" src="{{ asset('/public/assets/admin/img/400x400/react-new-slide6.jpg') }}" alt="" class="rounded">                                                                       
                                </div>
                            </div>
                            <div class="items__">
                                <div class="p-xxl-3 p-2 d-center h-135px bg--secondary rounded">
                                    <img wdith="110" height="100" src="{{ asset('/public/assets/admin/img/400x400/react-new-slide7.jpg') }}" alt="" class="rounded">                                                                       
                                </div>
                            </div>
                            <div class="items__">
                                <div class="p-xxl-3 p-2 d-center h-135px bg--secondary rounded">
                                    <img wdith="110" height="100" src="{{ asset('/public/assets/admin/img/400x400/react-new-slide8.jpg') }}" alt="" class="rounded">                                                                       
                                </div>
                            </div>
                        </div>
                        <div class="custom-owl-nav z-2">
                            <button type="button" class="custom-prev__ btn border-0 outline-none p-2"><i class="tio-chevron-left"></i></button>
                            <button type="button" class="custom-next__ btn border-0 outline-none p-2"><i class="tio-chevron-right"></i></button>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </form>
</div>
<div id="offcanvasOverlay" class="offcanvas-overlay"></div>
<!-- Section View Offcanvas end -->
@endsection

@push('script_2')
<script src="{{ asset('public/assets/admin/js/spartan-multi-image-picker.js') }}"></script>
<script>
    $(function() {
        $("#coba").spartanMultiImagePicker({
        fieldName: 'fileUpload[]',
        maxCount: 5,
        rowHeight: '176px',
        groupClassName: 'spartan_item_wrapper',
        placeholderImage: {
            image: '{{asset('public/assets/admin/img/new-component.png')}}',
            width: '100%'
        },
        dropFileLabel: "Drop file here or click to upload"
        });
    });
</script>
@endpush