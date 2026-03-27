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
                <h3 class="mb-1">{{ translate('Gallery Section') }}</h3>
                <p class="mb-0 gray-dark fs-12">
                    {{ translate('See how your Gallery Section will look to customers.') }}
                </p>
            </div>
            <div class="max-w-300px ml-sm-auto">
                <button type="button" class="btn btn-outline-primary py-2 fs-12 px-3 offcanvas-trigger" data-target="#gallery_section">
                    <i class="tio-invisible"></i> {{ translate('Section Preview') }}
                </button>
            </div>
        </div>
    </div>

    <div class="card mb-20">
        <div class="card-body">
            <div class="mb-20">
                <h3 class="mb-1">{{translate('messages.Gallery Content') }}</h3>
                <p class="m-0 fs-12 color-656566">{{ translate('Showcase high-quality food images to attract users visually.') }}</p>
            </div>
            <div class="bg--secondary h-100 rounded p-md-4 p-3">
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
                        <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_50_characters') }}">
                                    <i class="tio-info color-A7A7A7"></i>
                                </span>
                                <span class="form-label-secondary text-danger"
                                data-toggle="tooltip" data-placement="right"
                                data-original-title="{{ translate('messages.Required.')}}"> 
                                </span>
                            </label>
                        <textarea id="" type="text"  maxlength="50" name="[]" class="form-control min-h-45px" value="" rows="1" placeholder="{{translate('messages.title_here...')}}"></textarea>
                        <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/50</span>
                    </div>
                    <div class="col-sm-12">
                        <label for=""  class="form-label">{{translate('Sub Title')}} ({{ translate('messages.default') }})
                        <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_sub_title_within_200_characters') }}">
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
            <div class="btn--container justify-content-end mt-20">
                <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                <button type="submit"   class="btn btn--primary mb-2">{{translate('Save')}}</button>
            </div>
        </div>
    </div>

    <div class="card">
        <form action="#0">
            <div class="card-header">
                <div class="">
                    <h3 class="mb-0">{{ translate('Gallery Section Image') }}</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="w-100 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                    <h3 class="mb-0">{{ translate('1st Card') }}</h3>
                                    <label class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between gap-4">
                                        <span class="w-auto switch--label text-nowrap fs-14 text-title">
                                            {{translate('messages.Status') }}                                    
                                        </span>
                                        <input type="checkbox"  class="status toggle-switch-input" value="1" name="" id="" checked>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="bg--secondary h-100 rounded p-4 mb-20">
                                    <div class="text-center py-1">                            
                                        <div class="mx-auto text-center">
                                            <div class="mb-4">
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
                                            {{ translate('JPG, JPEG, PNG, Gif Image size : Max 2 MB')}} <span class="font-medium text-title">{{ translate('(1:1)')}}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="btn--container justify-content-end mt-20">
                                    <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                                    <button type="submit"   class="btn btn--primary mb-2">{{translate('Save')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="w-100 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                    <h3 class="mb-0">{{ translate('2nd Card') }}</h3>
                                    <label class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between gap-4">
                                        <span class="w-auto switch--label text-nowrap fs-14 text-title">
                                            {{translate('messages.Status') }}                                    
                                        </span>
                                        <input type="checkbox"  class="status toggle-switch-input" value="1" name="" id="" checked>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="bg--secondary h-100 rounded p-4 mb-20">
                                    <div class="text-center py-1">                            
                                        <div class="mx-auto text-center">
                                            <div class="mb-4">
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
                                            {{ translate('JPG, JPEG, PNG, Gif Image size : Max 2 MB')}} <span class="font-medium text-title">{{ translate('(1:1)')}}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="btn--container justify-content-end mt-20">
                                    <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                                    <button type="submit"   class="btn btn--primary mb-2">{{translate('Save')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="w-100 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                    <h3 class="mb-0">{{ translate('3rd Card') }}</h3>
                                    <label class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between gap-4">
                                        <span class="w-auto switch--label text-nowrap fs-14 text-title">
                                            {{translate('messages.Status') }}                                    
                                        </span>
                                        <input type="checkbox"  class="status toggle-switch-input" value="1" name="" id="" checked>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="bg--secondary h-100 rounded p-4 mb-20">
                                    <div class="text-center py-1">                            
                                        <div class="mx-auto text-center">
                                            <div class="mb-4">
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
                                            {{ translate('JPG, JPEG, PNG, Gif Image size : Max 2 MB')}} <span class="font-medium text-title">{{ translate('(1:1)')}}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="btn--container justify-content-end mt-20">
                                    <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                                    <button type="submit"   class="btn btn--primary mb-2">{{translate('Save')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="w-100 d-flex align-items-center gap-2 flex-wrap justify-content-between">
                                    <h3 class="mb-0">{{ translate('4th Card') }}</h3>
                                    <label class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between gap-4">
                                        <span class="w-auto switch--label text-nowrap fs-14 text-title">
                                            {{translate('messages.Status') }}                                    
                                        </span>
                                        <input type="checkbox"  class="status toggle-switch-input" value="1" name="" id="" checked>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="bg--secondary h-100 rounded p-4 mb-20">
                                    <div class="text-center py-1">                            
                                        <div class="mx-auto text-center">
                                            <div class="mb-4">
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
                                            {{ translate('JPG, JPEG, PNG, Gif Image size : Max 2 MB')}} <span class="font-medium text-title">{{ translate('(1:1)')}}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="btn--container justify-content-end mt-20">
                                    <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                                    <button type="submit"   class="btn btn--primary mb-2">{{translate('Save')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>  
</div>


<!-- Section View Offcanvas here -->
<div id="gallery_section" class="custom-offcanvas offcanvas-750 d-flex flex-column justify-content-between">
    <form action="{{ route('taxvat.store') }}" method="post">
        <div>
            <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                <div class="py-1">
                    <h3 class="mb-0 line--limit-1">{{ translate('messages.Gallery Section Preview') }}</h3>
                </div>
                <button type="button" class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"aria-label="Close">
                    &times;
                </button>
            </div>
            <div class="custom-offcanvas-body custom-offcanvas-body-100  p-20">
                <section class="common-section-view bg-white border rounded-10">
                    <div class="mb-4 text-center">
                        <h2 class="mb-lg-1 mb-1 fs-24">
                            See <span class="text-base-clr">6amMart</span> in Action
                        </h2>
                        <p class="text-title fs-14 m-0">
                            Explore how customers use our services, vendors grow their business
                        </p>
                    </div>
                    <div class="container p-0">
                        <div class="row g-xl-20 g-1">
                            <div class="col-xl-4 col-lg-6 col-6">
                                <div class="bg-ECEEF1 w-100 gallery-thumb-h-450 rounded-10">
                                    <img src="{{ asset('/public/assets/admin/img/400x400/ract-gallery1.jpg') }}" alt="" class="rounded-10 w-100 h-100">
                                </div>
                            </div>
                            <div class="col-xl-8 col-lg-6 col-6">
                                <div class="row g-xl-20 g-1">
                                    <div class="col-xl-6 col-6">
                                        <div class="bg-ECEEF1 w-100 gallery-thumb-h-220 rounded-10">
                                            <img src="{{ asset('/public/assets/admin/img/400x400/ract-gallery2.jpg') }}" alt="" class="rounded-10 w-100 h-100">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-6">
                                        <div class="bg-ECEEF1 w-100 gallery-thumb-h-220 rounded-10">
                                            <img src="{{ asset('/public/assets/admin/img/400x400/ract-gallery3.jpg') }}" alt="" class="rounded-10 w-100 h-100">
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="bg-ECEEF1 w-100 gallery-thumb-h-220 rounded-10">
                                            <img src="{{ asset('/public/assets/admin/img/400x400/ract-gallery4.jpg') }}" alt="" class="rounded-10 w-100 h-100">
                                        </div>
                                    </div>
                                </div>
                            </div>
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

@endpush