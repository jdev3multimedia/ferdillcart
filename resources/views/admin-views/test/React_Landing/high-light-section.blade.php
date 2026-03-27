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
                <h3 class="mb-1">{{ translate('High-light Section') }}</h3>
                <p class="mb-0 gray-dark fs-12">
                    {{ translate('See how your High-light Section will look to customers.') }}
                </p>
            </div>
            <div class="max-w-300px ml-sm-auto">
                <button type="button" class="btn btn-outline-primary py-2 fs-12 px-3 offcanvas-trigger" data-target="#high-light_section">
                    <i class="tio-invisible"></i> {{ translate('Section Preview') }}
                </button>
            </div>
        </div>
    </div>

    <div class="card mb-20">
        <form action="#0">
            <div class="card-body">
                <div class="mb-20">
                    <h3 class="mb-1">{{ translate('High-light Content Section ') }}</h3>
                    <p class="mb-0 fs-12">{{ translate('Showcase the key features or achievements of your platform to build trust and engagement.') }}</p>
                </div>
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="bg--secondary rounded h-100 p-xxl-4 p-3">
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
                            <div class="row g-3">
                                <div class="col-md-12 lang_form default-form">
                                    <div class="row g-1">
                                        <div class="col-12">
                                            <label for="high_light_title" class="form-label">{{translate('Title')}} ({{ translate('messages.default') }})
                                                <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_50_characters') }}">
                                                    <i class="tio-info color-A7A7A7"></i>
                                                </span><span class="form-label-secondary text-danger"
                                                data-toggle="tooltip" data-placement="right"
                                                data-original-title="{{ translate('messages.Required.')}}"> *
                                                </span>
                                            </label>
                                            <input id="high_light_title" type="text"  maxlength="50" name="high_light_title[]" value="" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                            <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                                        </div>
                                        <div class="col-12">
                                            <label for="high_light_sub_title" class="form-label">{{translate('Sub Title')}} ({{ translate('messages.default') }})
                                            <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_200_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span><span class="form-label-secondary text-danger"
                                            data-toggle="tooltip" data-placement="right"
                                            data-original-title="{{ translate('messages.Required.')}}"> *
                                            </span>
                                            </label>
                                            <textarea id="high_light_sub_title" rows="2" type="text"  maxlength="200" name="high_light_sub_title[]" value="" class="form-control" placeholder="{{translate('messages.sub_title_here...')}}"></textarea>
                                            <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                        </div>
                                        <div class="col-12">
                                            <label for="download_button_name" class="form-label">{{translate('Button Name')}} ({{ translate('messages.default') }})
                                            <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_button_name_within_20_characters') }}">
                                                <i class="tio-info color-A7A7A7"></i>
                                            </span><span class="form-label-secondary text-danger"
                                            data-toggle="tooltip" data-placement="right"
                                            data-original-title="{{ translate('messages.Required.')}}"> *
                                            </span>
                                            </label>
                                            <input id="download_button_name" type="text"  maxlength="20" name="download_button_name[]" value="" class="form-control" placeholder="{{translate('messages.Button Name')}}">
                                            <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/20</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="bg--secondary h-100 rounded p-md-4 p-3 d-center">
                            <div class="text-center">
                                <div class="mb-4">
                                    <h5 class="mb-1">{{ translate('Upload High-light Section Image') }}</h5>
                                    <p class="mb-0 fs-12 gray-dark">{{ translate('Upload your High-light Section  Image') }}</p>
                                </div>
                                <div class="mx-auto text-center">
                                    <div class="upload-file_custom ratio-1 h-100px">
                                        <input type="file" name="image" class="upload-file__input single_file_input"
                                                accept=".webp, .jpg, .jpeg, .png, .gif" required>
                                        <label class="upload-file__wrapper w-100 h-100 m-0">
                                            <div class="upload-file-textbox text-center" style="">
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
                                    {{ translate('JPG, JPEG, PNG size : Max 2 MB')}} <span class="font-medium text-title">{{ translate('(1:1)')}}</span>
                                </p>
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
    </div>
</div>



<!-- Section View Offcanvas here -->
<div id="high-light_section" class="custom-offcanvas offcanvas-750 d-flex flex-column justify-content-between">
    <form action="{{ route('taxvat.store') }}" method="post">
        <div>
            <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                <div class="py-1">
                    <h3 class="mb-0 line--limit-1">{{ translate('messages.High-light Section Preview') }}</h3>
                </div>
                <button type="button" class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"aria-label="Close">
                    &times;
                </button>
            </div>
            <div class="custom-offcanvas-body custom-offcanvas-body-100  p-20">
               <section class="common-section-view bg-white border rounded-10 my-xl-3 mx-xl-3">
                    <div class="common-section-inner highlight-gradient rounded-20 py-sm-4 py-3 px-xxl-5 px-xl-3 px-sm-3 px-3">
                        <div class="d-flex align-items-center gap-3 flex-wrap justify-content-xl-between justify-content-center text-xl-start text-center flex-xl-nowrap">
                            <div class="d-flex gap-xxl-20 align-items-center flex-xl-nowrap flex-wrap">
                                <div class="w-100px min-w-100px h-100px d-center mb-xl-0 mb-2 mx-auto">
                                    <img src="{{ asset('/public/assets/admin/img/400x400/high-light-car.png') }}" alt="" class="object-contain">
                                </div>
                                <div>
                                    <h2 class="mb-lg-2 mb-1 fs-20 text-white">
                                        Ride Anytime, Anywhere
                                    </h2>
                                    <p class="fs-12 m-0 text-white">
                                        6amMart makes it easy to rent vehicles quickly and affordably.
                                    </p>
                                </div>
                            </div>
                            <a href="#0" class="btn btn-primary-white bg-white fs-12 text-base-clr fw-medium">
                                Book a Rental <i class="tio-arrow-forward pl-1 text-base-clr"></i>
                            </a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </form>
</div>
<div id="offcanvasOverlay" class="offcanvas-overlay"></div>
<!-- Section View Offcanvas end -->


    <!-- How it Works -->
    @include('admin-views.business-settings.landing-page-settings.partial.how-it-work-react')
@endsection

