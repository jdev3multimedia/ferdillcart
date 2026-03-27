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
                <h3 class="mb-1">{{ translate('FAQ Section') }}</h3>
                <p class="mb-0 gray-dark fs-12">
                    {{ translate('See how your FAQ Section will look to customers.') }}
                </p>
            </div>
            <div class="max-w-300px ml-sm-auto">
                <button type="button" class="btn btn-outline-primary py-2 fs-12 px-3 offcanvas-trigger" data-target="#faqPreview_section">
                    <i class="tio-invisible"></i> {{ translate('Section Preview') }}
                </button>
            </div>
        </div>
    </div>


    <div class="card mb-20">
        <div class="card-header">
            <div class="">
                <h3 class="mb-1">{{ translate('FAQ Content Section ') }}</h3>
                <p class="mb-0 fs-12">{{ translate('Manage the main title and subtitle for the Frequently Asked Questions section.	') }}</p>
            </div>
        </div>
        <div class="card-body"> 
            <div class="card p-xxl-4 p-3 mb-20 border-0">
                <form action="#0">
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <div class="bg--secondary rounded p-xxl-4 p-3">
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
                                                    data-original-title="{{ translate('messages.Required.')}}"> 
                                                    </span>
                                                </label>
                                                <input id="high_light_title" type="text"  maxlength="50" name="high_light_title[]" value="" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                                <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/50</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end gap-3 mt-20">
                        <button type="reset" class="btn btn--reset">{{translate('Reset')}}</button>
                        <button type="submit"   class="btn btn--primary">{{translate('Save')}}</button>
                    </div>
                </form>
            </div>  
            <div class="card mb-20 border-0">
                <div class="card-body p-xxl-4 p-3">
                    <div class="mb-20">
                        <h4 class="mb-1">{{ translate('FAQ Q&A Setup ') }}</h4>
                        <p class="mb-0 fs-12">{{ translate('Add and manage individual questions and answers for each user type.') }}</p>
                    </div>
                    <form action="#0">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <div class="form-group mb-20">
                                    <label for="" class="mb-2 d-block text-title">User Type</label>
                                    <select name="" id="" class="custom-select">
                                        <option value="Select User Type" selected disabled>Select User Type</option>
                                        <option value="">any type here</option>
                                        <option value="">any type here</option>
                                        <option value="">any type here</option>
                                        <option value="">any type here</option>
                                    </select>
                                </div>
                                <div class="bg--secondary rounded p-xxl-4 p-3">
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
                                                    <label for="high_light_title" class="form-label">{{translate('Question')}} ({{ translate('messages.default') }})
                                                        <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_150_characters') }}">
                                                            <i class="tio-info color-A7A7A7"></i>
                                                        </span><span class="form-label-secondary text-danger"
                                                        data-toggle="tooltip" data-placement="right"
                                                        data-original-title="{{ translate('messages.Required.')}}"> 
                                                        </span>
                                                    </label>
                                                    <input id="high_light_title" type="text"  maxlength="150" name="high_light_title[]" value="" class="form-control" placeholder="{{translate('messages.Question Here...')}}">
                                                    <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/150</span>
                                                </div>
                                                <div class="col-12">
                                                    <label for="high_light_sub_title" class="form-label">{{translate('Answer')}} ({{ translate('messages.default') }})
                                                    <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_200_characters') }}">
                                                        <i class="tio-info color-A7A7A7"></i>
                                                    </span><span class="form-label-secondary text-danger"
                                                    data-toggle="tooltip" data-placement="right"
                                                    data-original-title="{{ translate('messages.Required.')}}"> 
                                                    </span>
                                                    </label>
                                                    <textarea id="high_light_sub_title" rows="1" type="text"  maxlength="500" name="high_light_sub_title[]" value="" class="form-control min-h-45px" placeholder="{{translate('messages.Answer Here...')}}"></textarea>
                                                    <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/500</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btn--container justify-content-end gap-3 mt-20">
                            <button type="reset" class="btn btn--reset">{{translate('Reset')}}</button>
                            <button type="submit"   class="btn btn--primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card border-0">
                <div class="card-header py-2 border-0">
                    <div class="d-flex w-100 flex-wrap gap-2 align-items-center justify-content-between">
                        <h4 class="text-black m-0">FAQ Q/A List</h4>
                        <div class="search--button-wrapper flex-grow-0">
                            <!-- Search -->
                            <div class="input-group input--group">
                                <input id="" type="search" name="search" value="" class="form-control" placeholder="Search Keywords" aria-label="Search here" tabindex="5">
                                <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table class="table table-borderless table-thead-borderless table-align-middle table-nowrap card-table m-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-top-0">SL</th>
                                    <th class="border-top-0">Question</th>
                                    <th class="border-top-0">Answer</th>
                                    <th class="border-top-0">User Type</th>
                                    <th class="text-center border-top-0">Status</th>
                                    <th class="text-center border-top-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>
                                        <div class="text-wrap line-limit-2  max-w--220px min-w-160 text-title">
                                            Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-wrap line-limit-3  max-w-400px min-w-176px text-title">
                                            Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text--title">
                                            Customer
                                        </div>
                                    </td>
                                    <td>
                                        <label class="toggle-switch mx-auto toggle-switch-sm">
                                            <input type="checkbox" class="status toggle-switch-input dynamic-checkbox" id="status-1" checked="" tabindex="">
                                            <span class="toggle-switch-label mx-auto">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>

                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn-outline-theme-light" href="#0">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:" data-id="review-1" data-message="Want to delete this review ?" title="Delete review"><i class="tio-delete-outlined"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>
                                        <div class="text-wrap line-limit-2  max-w--220px min-w-160 text-title">
                                            Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-wrap line-limit-3  max-w-400px min-w-176px text-title">
                                            Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text--title">
                                            Customer
                                        </div>
                                    </td>
                                    <td>
                                        <label class="toggle-switch mx-auto toggle-switch-sm">
                                            <input type="checkbox" class="status toggle-switch-input dynamic-checkbox" id="status-1" checked="" tabindex="">
                                            <span class="toggle-switch-label mx-auto">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>

                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn-outline-theme-light" href="#0">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:" data-id="review-1" data-message="Want to delete this review ?" title="Delete review"><i class="tio-delete-outlined"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>  
                                <tr>
                                    <td>3</td>
                                    <td>
                                        <div class="text-wrap line-limit-2  max-w--220px min-w-160 text-title">
                                            Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-wrap line-limit-3  max-w-400px min-w-176px text-title">
                                            Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text--title">
                                            Customer
                                        </div>
                                    </td>
                                    <td>
                                        <label class="toggle-switch mx-auto toggle-switch-sm">
                                            <input type="checkbox" class="status toggle-switch-input dynamic-checkbox" id="status-1" checked="" tabindex="">
                                            <span class="toggle-switch-label mx-auto">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>

                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn-outline-theme-light" href="#0">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:" data-id="review-1" data-message="Want to delete this review ?" title="Delete review"><i class="tio-delete-outlined"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>  
                                <tr>
                                    <td>4</td>
                                    <td>
                                        <div class="text-wrap line-limit-2  max-w--220px min-w-160 text-title">
                                            Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-wrap line-limit-3  max-w-400px min-w-176px text-title">
                                            Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, Lorem ipsum dolor sit amet, 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text--title">
                                            Customer
                                        </div>
                                    </td>
                                    <td>
                                        <label class="toggle-switch mx-auto toggle-switch-sm">
                                            <input type="checkbox" class="status toggle-switch-input dynamic-checkbox" id="status-1" checked="" tabindex="">
                                            <span class="toggle-switch-label mx-auto">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>

                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn-outline-theme-light" href="#0">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:" data-id="review-1" data-message="Want to delete this review ?" title="Delete review"><i class="tio-delete-outlined"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>                                                  
                            </tbody>
                        </table>
                        <div class="page-area px-3 pt-3 pb-2 d-flex align-items-center gap-3 justify-content-between flex-wrap">
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
    </div>




 



</div>




<!-- Section View Offcanvas here -->
<div id="faqPreview_section" class="custom-offcanvas offcanvas-750 d-flex flex-column justify-content-between">
    <form action="{{ route('taxvat.store') }}" method="post">
        <div>
            <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                <div class="py-1">
                    <h3 class="mb-0 line--limit-1">{{ translate('messages.Faq Section Preview') }}</h3>
                </div>
                <button type="button" class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"aria-label="Close">
                    &times;
                </button>
            </div>
            <div class="custom-offcanvas-body custom-offcanvas-body-100  p-20">
               <section class="common-section-view bg-white border rounded-10 my-xl-2 mx-xl-2">
                    <div class="common-section-inner0">                        
                        <h2 class="mb-md-4 mb-3 fs-24 text-center">
                            Got Questions? We’ve Got <span class="text-base-clr">Answers</span>
                        </h2>
                        <ul class="nav nav-tabs rounded-10 border-0 question-tabs max-w-595 mx-auto mb-20" id="myTab" role="tablist">
                            <li class="nav-item w-100" role="presentation">
                                <button class="nav-link text-nowrap text-xl-start text-center d-xl-flex flex-xl-nowrap flex-wrap align-items-center gap-2 active" id="home-tab" data-toggle="tab" data-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_12693_439)">
                                        <path d="M17.1126 10.6758H15.5659C15.7236 11.1074 15.8097 11.5732 15.8097 12.0588V17.9045C15.8097 18.1069 15.7745 18.3012 15.7104 18.4819H18.2674C19.2226 18.4819 19.9997 17.7048 19.9997 16.7496V13.5629C19.9998 11.9709 18.7046 10.6758 17.1126 10.6758Z" fill="#222324" fill-opacity="0.4"/>
                                        <path d="M4.19005 12.0588C4.19005 11.5732 4.27618 11.1074 4.43384 10.6758H2.88712C1.29516 10.6758 0 11.9709 0 13.5629V16.7497C0 17.7048 0.777072 18.4819 1.73227 18.4819H4.28938C4.22528 18.3011 4.19005 18.1069 4.19005 17.9045V12.0588Z" fill="#222324" fill-opacity="0.4"/>
                                        <path d="M11.7688 9.17188H8.23282C6.64086 9.17188 5.3457 10.467 5.3457 12.059V17.9047C5.3457 18.2236 5.60422 18.4821 5.92313 18.4821H14.0785C14.3974 18.4821 14.656 18.2236 14.656 17.9047V12.059C14.656 10.467 13.3608 9.17188 11.7688 9.17188Z" fill="#222324" fill-opacity="0.4"/>
                                        <path d="M9.99946 1.51562C8.08493 1.51562 6.52734 3.07321 6.52734 4.98778C6.52734 6.28642 7.2441 7.4206 8.30262 8.01607C8.80469 8.29849 9.38352 8.4599 9.99946 8.4599C10.6154 8.4599 11.1942 8.29849 11.6963 8.01607C12.7549 7.4206 13.4716 6.28638 13.4716 4.98778C13.4716 3.07325 11.914 1.51562 9.99946 1.51562Z" fill="#222324" fill-opacity="0.4"/>
                                        <path d="M3.90332 4.75391C2.47149 4.75391 1.30664 5.91875 1.30664 7.35059C1.30664 8.78243 2.47149 9.94728 3.90332 9.94728C4.26653 9.94728 4.61239 9.87204 4.92657 9.73681C5.46977 9.50294 5.91766 9.08895 6.19481 8.5704C6.38935 8.20645 6.50001 7.79126 6.50001 7.35059C6.50001 5.91879 5.33516 4.75391 3.90332 4.75391Z" fill="#222324" fill-opacity="0.4"/>
                                        <path d="M16.0967 4.75391C14.6648 4.75391 13.5 5.91875 13.5 7.35059C13.5 7.79129 13.6107 8.20649 13.8052 8.5704C14.0823 9.08899 14.5302 9.50298 15.0734 9.73681C15.3876 9.87204 15.7335 9.94728 16.0967 9.94728C17.5285 9.94728 18.6934 8.78243 18.6934 7.35059C18.6934 5.91875 17.5285 4.75391 16.0967 4.75391Z" fill="#222324" fill-opacity="0.4"/>
                                        </g>
                                        <defs>
                                        <clipPath id="clip0_12693_439">
                                        <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                        </defs>
                                    </svg>
                                    <span class="d-xl-inline-block d-block">I’m a Customer</span>
                                </button>
                            </li>
                            <li class="nav-item w-100" role="presentation">
                                <button class="nav-link text-nowrap text-xl-start text-center d-xl-flex flex-xl-nowrap flex-wrap align-items-center gap-2" id="profile-tab" data-toggle="tab" data-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">
                                    <svg width="17" height="20" viewBox="0 0 17 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4.87537 8.4797C5.05757 9.66403 5.97274 11.1714 7.47592 11.7014C8.09293 11.9167 8.76792 11.9209 9.38493 11.6973C10.8633 11.1631 11.8074 9.65989 11.9938 8.4797C12.1925 8.46314 12.4534 8.18569 12.735 7.18771C13.1201 5.82531 12.7102 5.6224 12.3623 5.65553C12.4286 5.46919 12.4783 5.2787 12.5114 5.0965C13.0994 1.56421 11.3602 1.44413 11.3602 1.44413C11.3602 1.44413 11.0703 0.88923 10.3125 0.470988C9.80317 0.168694 9.09506 -0.0632022 8.16333 0.015477C7.86104 0.0279 7.57531 0.0900152 7.30614 0.176976C6.96244 0.292925 6.64772 0.462706 6.36199 0.661474C6.01415 0.880948 5.68287 1.15425 5.393 1.46483C4.93334 1.93691 4.52338 2.54563 4.34532 3.30344C4.19624 3.87076 4.22937 4.46292 4.3536 5.10064C4.38673 5.28698 4.43642 5.47333 4.50268 5.65967C4.15483 5.62654 3.74487 5.82945 4.12999 7.19185C4.41572 8.18569 4.6766 8.46314 4.87537 8.4797Z" fill="white"/>
                                        <path d="M4.87537 8.4797C5.05757 9.66403 5.97274 11.1714 7.47592 11.7014C8.09293 11.9167 8.76792 11.9209 9.38493 11.6973C10.8633 11.1631 11.8074 9.65989 11.9938 8.4797C12.1925 8.46314 12.4534 8.18569 12.735 7.18771C13.1201 5.82531 12.7102 5.6224 12.3623 5.65553C12.4286 5.46919 12.4783 5.2787 12.5114 5.0965C13.0994 1.56421 11.3602 1.44413 11.3602 1.44413C11.3602 1.44413 11.0703 0.88923 10.3125 0.470988C9.80317 0.168694 9.09506 -0.0632022 8.16333 0.015477C7.86104 0.0279 7.57531 0.0900152 7.30614 0.176976C6.96244 0.292925 6.64772 0.462706 6.36199 0.661474C6.01415 0.880948 5.68287 1.15425 5.393 1.46483C4.93334 1.93691 4.52338 2.54563 4.34532 3.30344C4.19624 3.87076 4.22937 4.46292 4.3536 5.10064C4.38673 5.28698 4.43642 5.47333 4.50268 5.65967C4.15483 5.62654 3.74487 5.82945 4.12999 7.19185C4.41572 8.18569 4.6766 8.46314 4.87537 8.4797Z" fill="#222324" fill-opacity="0.4"/>
                                        <path d="M15.2657 13.1147C13.1496 12.5764 11.4311 11.3672 11.4311 11.3672L10.0894 15.6117L9.8368 16.4109L9.83266 16.3985L9.61318 17.0776L8.90507 15.0693C10.6443 12.6426 8.55308 12.6675 8.43714 12.6716C8.32119 12.6675 6.22998 12.6426 7.9692 15.0693L7.26109 17.0776L7.04162 16.3985L7.03748 16.4109L6.78487 15.6117L5.43905 11.3672C5.43905 11.3672 3.72053 12.5764 1.60447 13.1147C0.0267462 13.5164 -0.047792 15.3384 0.0143232 16.237C0.0143232 16.237 0.105425 17.4586 0.196528 17.9969C0.196528 17.9969 3.27744 19.9971 8.43714 20.0012C13.5968 20.0012 16.6777 17.9969 16.6777 17.9969C16.7688 17.4586 16.8599 16.237 16.8599 16.237C16.9179 15.3384 16.8434 13.5164 15.2657 13.1147Z" fill="white"/>
                                        <path d="M15.2657 13.1147C13.1496 12.5764 11.4311 11.3672 11.4311 11.3672L10.0894 15.6117L9.8368 16.4109L9.83266 16.3985L9.61318 17.0776L8.90507 15.0693C10.6443 12.6426 8.55308 12.6675 8.43714 12.6716C8.32119 12.6675 6.22998 12.6426 7.9692 15.0693L7.26109 17.0776L7.04162 16.3985L7.03748 16.4109L6.78487 15.6117L5.43905 11.3672C5.43905 11.3672 3.72053 12.5764 1.60447 13.1147C0.0267462 13.5164 -0.047792 15.3384 0.0143232 16.237C0.0143232 16.237 0.105425 17.4586 0.196528 17.9969C0.196528 17.9969 3.27744 19.9971 8.43714 20.0012C13.5968 20.0012 16.6777 17.9969 16.6777 17.9969C16.7688 17.4586 16.8599 16.237 16.8599 16.237C16.9179 15.3384 16.8434 13.5164 15.2657 13.1147Z" fill="#222324" fill-opacity="0.4"/>
                                    </svg>
                                    <span class="d-xl-inline-block d-block">I’m a Seller</span>
                                </button>
                            </li>
                            <li class="nav-item w-100" role="presentation">
                                <button class="nav-link text-nowrap text-xl-start text-center d-xl-flex flex-xl-nowrap flex-wrap align-items-center gap-2" id="contact-tab" data-toggle="tab" data-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_17786_46463)">
                                        <path d="M4.04897 7.30627V7.7548C4.04897 8.17602 4.22072 8.5889 4.5203 8.8879C4.76121 9.12809 4.9767 9.34362 5.18968 9.55685V11.0147L4.26273 11.4968L5.34973 13.8115C5.46232 14.0513 5.75939 14.1385 5.98371 13.9976L7.53285 13.0249L5.63085 11.1229V9.99876L6.00267 10.3713C6.30124 10.6704 6.71398 10.8419 7.1349 10.8419H7.92577C8.3467 10.8419 8.75944 10.6704 9.05801 10.3714L9.43384 9.99479V11.1239L7.53285 13.0249L9.08207 13.9976C9.30639 14.1385 9.60338 14.0513 9.71597 13.8115L10.803 11.4968L9.87502 11.0142V9.55287C10.0867 9.34089 10.3009 9.12662 10.5404 8.88775C10.8399 8.5889 11.0117 8.17601 11.0117 7.7548V7.30989C11.4479 7.2156 11.7766 6.8266 11.7766 6.3579C11.7766 5.90145 11.4573 5.51945 11.0312 5.41779C11.048 5.3601 11.0574 5.29971 11.0574 5.23774V4.93679C8.70609 4.23461 6.35469 4.23461 4.0033 4.93679V5.23774C4.0033 5.301 4.01256 5.36277 4.03001 5.42155C3.60542 5.52647 3.29102 5.90511 3.29102 6.3579C3.29102 6.82269 3.61447 7.20827 4.04897 7.30627ZM4.49014 5.87592L4.99404 6.10486C5.2538 6.22287 5.54436 6.24086 5.8225 6.17708C6.96106 5.9159 8.09962 5.9159 9.23825 6.17708C9.51635 6.24086 9.80688 6.22287 10.0667 6.10486L10.5705 5.87594V7.7548C10.5705 8.05969 10.446 8.35883 10.2287 8.5754C9.81756 8.98562 9.42177 9.38241 8.7458 10.0596C8.52938 10.2763 8.23052 10.4007 7.92577 10.4007H7.1349C6.83016 10.4007 6.5313 10.2763 6.31488 10.0596C5.78989 9.53355 5.36484 9.10717 4.83194 8.57554C4.61465 8.35883 4.49014 8.05969 4.49014 7.7548V5.87592Z" fill="white"/>
                                        <path d="M4.04897 7.30627V7.7548C4.04897 8.17602 4.22072 8.5889 4.5203 8.8879C4.76121 9.12809 4.9767 9.34362 5.18968 9.55685V11.0147L4.26273 11.4968L5.34973 13.8115C5.46232 14.0513 5.75939 14.1385 5.98371 13.9976L7.53285 13.0249L5.63085 11.1229V9.99876L6.00267 10.3713C6.30124 10.6704 6.71398 10.8419 7.1349 10.8419H7.92577C8.3467 10.8419 8.75944 10.6704 9.05801 10.3714L9.43384 9.99479V11.1239L7.53285 13.0249L9.08207 13.9976C9.30639 14.1385 9.60338 14.0513 9.71597 13.8115L10.803 11.4968L9.87502 11.0142V9.55287C10.0867 9.34089 10.3009 9.12662 10.5404 8.88775C10.8399 8.5889 11.0117 8.17601 11.0117 7.7548V7.30989C11.4479 7.2156 11.7766 6.8266 11.7766 6.3579C11.7766 5.90145 11.4573 5.51945 11.0312 5.41779C11.048 5.3601 11.0574 5.29971 11.0574 5.23774V4.93679C8.70609 4.23461 6.35469 4.23461 4.0033 4.93679V5.23774C4.0033 5.301 4.01256 5.36277 4.03001 5.42155C3.60542 5.52647 3.29102 5.90511 3.29102 6.3579C3.29102 6.82269 3.61447 7.20827 4.04897 7.30627ZM4.49014 5.87592L4.99404 6.10486C5.2538 6.22287 5.54436 6.24086 5.8225 6.17708C6.96106 5.9159 8.09962 5.9159 9.23825 6.17708C9.51635 6.24086 9.80688 6.22287 10.0667 6.10486L10.5705 5.87594V7.7548C10.5705 8.05969 10.446 8.35883 10.2287 8.5754C9.81756 8.98562 9.42177 9.38241 8.7458 10.0596C8.52938 10.2763 8.23052 10.4007 7.92577 10.4007H7.1349C6.83016 10.4007 6.5313 10.2763 6.31488 10.0596C5.78989 9.53355 5.36484 9.10717 4.83194 8.57554C4.61465 8.35883 4.49014 8.05969 4.49014 7.7548V5.87592Z" fill="#222324" fill-opacity="0.4"/>
                                        <path d="M12.3428 11.9596L11.2634 11.5537L10.1163 13.9949C9.89204 14.4754 9.29039 14.6503 8.84865 14.3684L7.53396 13.542L6.21922 14.3684C5.76375 14.6531 5.17275 14.4688 4.95159 13.9949L3.80453 11.5508L2.16337 12.1684C1.21628 12.5243 0.589844 13.4302 0.589844 14.442V18.2155C0.589844 18.7537 1.02513 19.189 1.56045 19.189H3.14571V16.4567C3.14571 16.3361 3.24574 16.2361 3.3663 16.2361C3.48984 16.2361 3.58689 16.3361 3.58689 16.4567V19.189H11.4781V19.1596C11.3134 18.9508 11.2192 18.6919 11.2192 18.4067V13.1743C11.2192 12.5331 11.7163 12.0067 12.3428 11.9596Z" fill="white"/>
                                        <path d="M12.3428 11.9596L11.2634 11.5537L10.1163 13.9949C9.89204 14.4754 9.29039 14.6503 8.84865 14.3684L7.53396 13.542L6.21922 14.3684C5.76375 14.6531 5.17275 14.4688 4.95159 13.9949L3.80453 11.5508L2.16337 12.1684C1.21628 12.5243 0.589844 13.4302 0.589844 14.442V18.2155C0.589844 18.7537 1.02513 19.189 1.56045 19.189H3.14571V16.4567C3.14571 16.3361 3.24574 16.2361 3.3663 16.2361C3.48984 16.2361 3.58689 16.3361 3.58689 16.4567V19.189H11.4781V19.1596C11.3134 18.9508 11.2192 18.6919 11.2192 18.4067V13.1743C11.2192 12.5331 11.7163 12.0067 12.3428 11.9596Z" fill="#222324" fill-opacity="0.4"/>
                                        <path d="M5.67745 3.74357C5.67745 2.52886 5.98037 1.4818 6.448 0.890625C5.04506 1.21122 4.00391 2.46415 4.00391 3.96416V4.47592C4.55979 4.31416 5.11859 4.19062 5.67745 4.10827V3.74357Z" fill="white"/>
                                        <path d="M5.67745 3.74357C5.67745 2.52886 5.98037 1.4818 6.448 0.890625C5.04506 1.21122 4.00391 2.46415 4.00391 3.96416V4.47592C4.55979 4.31416 5.11859 4.19062 5.67745 4.10827V3.74357Z" fill="#222324" fill-opacity="0.4"/>
                                        <path d="M6.11938 4.04978C6.58993 3.99389 7.06055 3.96743 7.53113 3.96743C7.99877 3.96743 8.47233 3.99389 8.94287 4.04978V3.7439C8.94287 2.01449 8.28994 0.808594 7.7017 0.808594H7.35761C6.77232 0.808594 6.11939 2.01449 6.11939 3.7439L6.11938 4.04978Z" fill="white"/>
                                        <path d="M6.11938 4.04978C6.58993 3.99389 7.06055 3.96743 7.53113 3.96743C7.99877 3.96743 8.47233 3.99389 8.94287 4.04978V3.7439C8.94287 2.01449 8.28994 0.808594 7.7017 0.808594H7.35761C6.77232 0.808594 6.11939 2.01449 6.11939 3.7439L6.11938 4.04978Z" fill="#222324" fill-opacity="0.4"/>
                                        <path d="M9.38383 4.10827C9.9427 4.19062 10.5015 4.31416 11.0574 4.47592V3.96416C11.0574 2.46415 10.0162 1.21122 8.61328 0.890625C9.08092 1.4818 9.38383 2.52886 9.38383 3.74357L9.38383 4.10827Z" fill="white"/>
                                        <path d="M9.38383 4.10827C9.9427 4.19062 10.5015 4.31416 11.0574 4.47592V3.96416C11.0574 2.46415 10.0162 1.21122 8.61328 0.890625C9.08092 1.4818 9.38383 2.52886 9.38383 3.74357L9.38383 4.10827Z" fill="#222324" fill-opacity="0.4"/>
                                        <path d="M14.834 12.3945H16.2387V14.276H14.834V12.3945Z" fill="white"/>
                                        <path d="M14.834 12.3945H16.2387V14.276H14.834V12.3945Z" fill="#222324" fill-opacity="0.4"/>
                                        <path d="M18.6288 12.3945H16.678V14.4967C16.678 14.6185 16.5792 14.7173 16.4574 14.7173H14.6116C14.4898 14.7173 14.391 14.6185 14.391 14.4967V12.3945H12.4401C12.0083 12.3945 11.6582 12.7446 11.6582 13.1764V18.4073C11.6582 18.8391 12.0083 19.1892 12.4401 19.1892H18.6288C19.0606 19.1892 19.4107 18.8391 19.4107 18.4073V13.1764C19.4107 12.7446 19.0606 12.3945 18.6288 12.3945ZM17.2265 17.5796H13.6421C13.5203 17.5796 13.4215 17.4808 13.4215 17.359C13.4215 17.2372 13.5203 17.1384 13.6421 17.1384H17.2265C17.3483 17.1384 17.4471 17.2372 17.4471 17.359C17.4471 17.4808 17.3483 17.5796 17.2265 17.5796ZM17.2265 16.3512H13.6421C13.5203 16.3512 13.4215 16.2523 13.4215 16.1306C13.4215 16.0088 13.5203 15.91 13.6421 15.91H17.2265C17.3483 15.91 17.4471 16.0088 17.4471 16.1306C17.4471 16.2523 17.3483 16.3512 17.2265 16.3512Z" fill="white"/>
                                        <path d="M18.6288 12.3945H16.678V14.4967C16.678 14.6185 16.5792 14.7173 16.4574 14.7173H14.6116C14.4898 14.7173 14.391 14.6185 14.391 14.4967V12.3945H12.4401C12.0083 12.3945 11.6582 12.7446 11.6582 13.1764V18.4073C11.6582 18.8391 12.0083 19.1892 12.4401 19.1892H18.6288C19.0606 19.1892 19.4107 18.8391 19.4107 18.4073V13.1764C19.4107 12.7446 19.0606 12.3945 18.6288 12.3945ZM17.2265 17.5796H13.6421C13.5203 17.5796 13.4215 17.4808 13.4215 17.359C13.4215 17.2372 13.5203 17.1384 13.6421 17.1384H17.2265C17.3483 17.1384 17.4471 17.2372 17.4471 17.359C17.4471 17.4808 17.3483 17.5796 17.2265 17.5796ZM17.2265 16.3512H13.6421C13.5203 16.3512 13.4215 16.2523 13.4215 16.1306C13.4215 16.0088 13.5203 15.91 13.6421 15.91H17.2265C17.3483 15.91 17.4471 16.0088 17.4471 16.1306C17.4471 16.2523 17.3483 16.3512 17.2265 16.3512Z" fill="#222324" fill-opacity="0.4"/>
                                        </g>
                                        <defs>
                                        <clipPath id="clip0_17786_46463">
                                        <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                        </defs>
                                    </svg>
                                    <span class="d-xl-inline-block d-block">I’m a Rider</span>
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <div class="accordion d-flex flex-column gap-3 question-accordion" id="faqAccordion">
                                    <div class="card">
                                        <div class="card-header-cus" id="headingOne">            
                                            <button class="btn btn-link text-wrap text-title" type="button" data-toggle="collapse" data-target="#collapseOne"
                                            aria-expanded="true" aria-controls="collapseOne">
                                                How do I place an order on 6amMart from start to finish?
                                            </button>
                                        </div>

                                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#faqAccordion">
                                            <div class="card-body max-w-700 pt-0">
                                            To place an order, open the 6amMart app or website, browse or search for the products you need, and add them to your cart. At checkout, enter your delivery address and payment details, then confirm your order to complete the process.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header-cus" id="headingTwo">
                                            <button class="btn btn-link text-wrap text-title collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo"
                                            aria-expanded="false" aria-controls="collapseTwo">
                                                What payment methods can I use, and are they secure?
                                            </button>
                                        </div>
                                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#faqAccordion">
                                            <div class="card-body max-w-700 pt-0">
                                                To place an order, open the 6amMart app or website, browse or search for the products you need, and add them to your cart. At checkout, enter your delivery address and payment details, then confirm your order to complete the process.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header-cus" id="headingThree">
                                            <button class="btn btn-link text-wrap text-title collapsed" type="button" data-toggle="collapse" data-target="#collapseThree"
                                            aria-expanded="false" aria-controls="collapseThree">
                                                Can I schedule my delivery for a specific date and time?
                                            </button>
                                        </div>
                                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#faqAccordion">
                                            <div class="card-body max-w-700 pt-0">
                                                To place an order, open the 6amMart app or website, browse or search for the products you need, and add them to your cart. At checkout, enter your delivery address and payment details, then confirm your order to complete the process.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header-cus" id="headingFour">
                                            <button class="btn btn-link text-wrap text-title collapsed" type="button" data-toggle="collapse" data-target="#collapseFour"
                                            aria-expanded="false" aria-controls="collapseFour">
                                                How can I track my order after placing it?
                                            </button>
                                        </div>
                                        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#faqAccordion">
                                            <div class="card-body max-w-700 pt-0">
                                                To place an order, open the 6amMart app or website, browse or search for the products you need, and add them to your cart. At checkout, enter your delivery address and payment details, then confirm your order to complete the process.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card disabled">
                                        <div class="card-header-cus" id="headingFive">
                                            <button class="btn btn-link text-wrap text-title collapsed" type="button" data-toggle="collapse" data-target="#collapseFive"
                                            aria-expanded="false" aria-controls="collapseFive">
                                                What is the return and refund policy for my orders?
                                            </button>
                                        </div>
                                        <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#faqAccordion">
                                            <div class="card-body max-w-700 pt-0">
                                                To place an order, open the 6amMart app or website, browse or search for the products you need, and add them to your cart. At checkout, enter your delivery address and payment details, then confirm your order to complete the process.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg--secondary rounded-10 py-xl-4 py-3 px-xl-4 px-3">
                                        <div class="d-flex align-items-center gap-3 flex-wrap justify-content-xl-between justify-content-center text-xl-start text-center flex-xl-nowrap">
                                            <div class="d-xl-flex gap-xxl-20 align-items-center flex-xl-nowrap flex-wrap">
                                                <div class="w-50px bg-white h-50px min-h-50 rounded-circle d-center mx-auto mb-xl-0 mb-2">
                                                    <img src="{{ asset('/public/assets/admin/img/icons/faq-question.png') }}" alt="" class="object-contain">
                                                </div>
                                                <div>
                                                    <h2 class="mb-lg-1 mb-1 fs-20 text-title">
                                                       Still have questions?
                                                    </h2>
                                                    <p class="fs-14 m-0 color-8a8a8a">
                                                        We’re just a click away if you have more questions.
                                                    </p>
                                                </div>
                                            </div>
                                            <a href="#0" class="btn btn-primary base-border-cmn base-bg-cmn rounded-10 fs-12 px-4 fw-medium">
                                                <span class="text-white py-1 d-block">Contact Us</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="accordion d-flex flex-column gap-3 question-accordion" id="faqAccordion2">
                                    <div class="card">
                                        <div class="card-header-cus" id="headingOne01">            
                                            <button class="btn btn-link text-wrap text-title" type="button" data-toggle="collapse" data-target="#collapseOne01"
                                            aria-expanded="true" aria-controls="collapseOne01">
                                                How do I place an order on 6amMart from start to finish?
                                            </button>
                                        </div>

                                        <div id="collapseOne01" class="collapse show" aria-labelledby="headingOne01" data-parent="#faqAccordion2">
                                            <div class="card-body max-w-700 pt-0">
                                                To place an order, open the 6amMart app or website, browse or search for the products you need, and add them to your cart. At checkout, enter your delivery address and payment details, then confirm your order to complete the process.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header-cus" id="headingTwo02">
                                            <button class="btn btn-link text-wrap text-title collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo02"
                                            aria-expanded="false" aria-controls="collapseTwo02">
                                                What payment methods can I use, and are they secure?
                                            </button>
                                        </div>
                                        <div id="collapseTwo02" class="collapse" aria-labelledby="headingTwo02" data-parent="#faqAccordion2">
                                            <div class="card-body max-w-700 pt-0">
                                                To place an order, open the 6amMart app or website, browse or search for the products you need, and add them to your cart. At checkout, enter your delivery address and payment details, then confirm your order to complete the process.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header-cus" id="headingThree03">
                                            <button class="btn btn-link text-wrap text-title collapsed" type="button" data-toggle="collapse" data-target="#collapseThree03"
                                            aria-expanded="false" aria-controls="collapseThree03">
                                                Can I schedule my delivery for a specific date and time?
                                            </button>
                                        </div>
                                        <div id="collapseThree03" class="collapse" aria-labelledby="headingThree03" data-parent="#faqAccordion2">
                                            <div class="card-body max-w-700 pt-0">
                                                To place an order, open the 6amMart app or website, browse or search for the products you need, and add them to your cart. At checkout, enter your delivery address and payment details, then confirm your order to complete the process.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header-cus" id="headingFour04">
                                            <button class="btn btn-link text-wrap text-title collapsed" type="button" data-toggle="collapse" data-target="#collapseFour04"
                                            aria-expanded="false" aria-controls="collapseFour04">
                                                How can I track my order after placing it?
                                            </button>
                                        </div>
                                        <div id="collapseFour04" class="collapse" aria-labelledby="headingFour04" data-parent="#faqAccordion2">
                                            <div class="card-body max-w-700 pt-0">
                                                To place an order, open the 6amMart app or website, browse or search for the products you need, and add them to your cart. At checkout, enter your delivery address and payment details, then confirm your order to complete the process.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card disabled">
                                        <div class="card-header-cus" id="headingFive05">
                                            <button class="btn btn-link text-wrap text-title collapsed" type="button" data-toggle="collapse" data-target="#collapseFive05"
                                            aria-expanded="false" aria-controls="collapseFive05">
                                                What is the return and refund policy for my orders?
                                            </button>
                                        </div>
                                        <div id="collapseFive05" class="collapse" aria-labelledby="headingFive05" data-parent="#faqAccordion2">
                                            <div class="card-body max-w-700 pt-0">
                                                To place an order, open the 6amMart app or website, browse or search for the products you need, and add them to your cart. At checkout, enter your delivery address and payment details, then confirm your order to complete the process.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg--secondary rounded-10 py-xl-4 py-3 px-xl-4 px-3">
                                        <div class="d-flex align-items-center gap-3 flex-wrap justify-content-xl-between justify-content-center text-xl-start text-center flex-xl-nowrap">
                                            <div class="d-xl-flex gap-xxl-20 align-items-center flex-xl-nowrap flex-wrap">
                                                <div class="w-50px bg-white h-50px min-h-50 rounded-circle d-center mx-auto mb-xl-0 mb-2">
                                                    <img src="{{ asset('/public/assets/admin/img/icons/faq-question.png') }}" alt="" class="object-contain">
                                                </div>
                                                <div>
                                                    <h2 class="mb-lg-1 mb-1 fs-20 text-title">
                                                       Still have questions?
                                                    </h2>
                                                    <p class="fs-14 m-0 color-8a8a8a">
                                                        We’re just a click away if you have more questions.
                                                    </p>
                                                </div>
                                            </div>
                                            <a href="#0" class="btn btn-primary base-border-cmn base-bg-cmn rounded-10 fs-12 px-4 fw-medium">
                                                <span class="text-white py-1 d-block">Contact Us</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                <div class="accordion d-flex flex-column gap-3 question-accordion" id="faqAccordion3">
                                    <div class="card">
                                        <div class="card-header-cus" id="headingOne-cus01">            
                                            <button class="btn btn-link text-wrap text-title" type="button" data-toggle="collapse" data-target="#collapseOne-cus01"
                                            aria-expanded="true" aria-controls="collapseOne-cus01">
                                                How do I place an order on 6amMart from start to finish?
                                            </button>
                                        </div>

                                        <div id="collapseOne-cus01" class="collapse show" aria-labelledby="headingOne-cus01" data-parent="#faqAccordion3">
                                            <div class="card-body max-w-700 pt-0">
                                                To place an order, open the 6amMart app or website, browse or search for the products you need, and add them to your cart. At checkout, enter your delivery address and payment details, then confirm your order to complete the process.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header-cus" id="headingTwo-cus02">
                                            <button class="btn btn-link text-wrap text-title collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo-cus02"
                                            aria-expanded="false" aria-controls="collapseTwo-cus02">
                                                What payment methods can I use, and are they secure?
                                            </button>
                                        </div>
                                        <div id="collapseTwo-cus02" class="collapse" aria-labelledby="headingTwo-cus02" data-parent="#faqAccordion3">
                                            <div class="card-body max-w-700 pt-0">
                                                To place an order, open the 6amMart app or website, browse or search for the products you need, and add them to your cart. At checkout, enter your delivery address and payment details, then confirm your order to complete the process.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header-cus" id="headingThree-cus03">
                                            <button class="btn btn-link text-wrap text-title collapsed" type="button" data-toggle="collapse" data-target="#collapseThree-cus03"
                                            aria-expanded="false" aria-controls="collapseThree-cus03">
                                                Can I schedule my delivery for a specific date and time?
                                            </button>
                                        </div>
                                        <div id="collapseThree-cus03" class="collapse" aria-labelledby="headingThree-cus03" data-parent="#faqAccordion3">
                                            <div class="card-body max-w-700 pt-0">
                                                To place an order, open the 6amMart app or website, browse or search for the products you need, and add them to your cart. At checkout, enter your delivery address and payment details, then confirm your order to complete the process.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header-cus" id="headingFour-cus04">
                                            <button class="btn btn-link text-wrap text-title collapsed" type="button" data-toggle="collapse" data-target="#collapseFour-cus04"
                                            aria-expanded="false" aria-controls="collapseFour-cus04">
                                                How can I track my order after placing it?
                                            </button>
                                        </div>
                                        <div id="collapseFour-cus04" class="collapse" aria-labelledby="headingFour-cus04" data-parent="#faqAccordion3">
                                            <div class="card-body max-w-700 pt-0">
                                                To place an order, open the 6amMart app or website, browse or search for the products you need, and add them to your cart. At checkout, enter your delivery address and payment details, then confirm your order to complete the process.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card disabled">
                                        <div class="card-header-cus" id="headingFive-cus05">
                                            <button class="btn btn-link text-wrap text-title collapsed" type="button" data-toggle="collapse" data-target="#collapseFive-cus05"
                                            aria-expanded="false" aria-controls="collapseFive-cus05">
                                                What is the return and refund policy for my orders?
                                            </button>
                                        </div>
                                        <div id="collapseFive-cus05" class="collapse" aria-labelledby="headingFive-cus05" data-parent="#faqAccordion3">
                                            <div class="card-body max-w-700 pt-0">
                                                To place an order, open the 6amMart app or website, browse or search for the products you need, and add them to your cart. At checkout, enter your delivery address and payment details, then confirm your order to complete the process.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg--secondary rounded-10 py-xl-4 py-3 px-xl-4 px-3">
                                        <div class="d-flex align-items-center gap-3 flex-wrap justify-content-xl-between justify-content-center text-xl-start text-center flex-xl-nowrap">
                                            <div class="d-xl-flex gap-xxl-20 align-items-center flex-xl-nowrap flex-wrap">
                                                <div class="w-50px bg-white h-50px min-h-50 rounded-circle d-center mx-auto mb-xl-0 mb-2">
                                                    <img src="{{ asset('/public/assets/admin/img/icons/faq-question.png') }}" alt="" class="object-contain">
                                                </div>
                                                <div>
                                                    <h2 class="mb-lg-1 mb-1 fs-20 text-title">
                                                       Still have questions?
                                                    </h2>
                                                    <p class="fs-14 m-0 color-8a8a8a">
                                                        We’re just a click away if you have more questions.
                                                    </p>
                                                </div>
                                            </div>
                                            <a href="#0" class="btn btn-primary base-border-cmn base-bg-cmn rounded-10 fs-12 px-4 fw-medium">
                                                <span class="text-white py-1 d-block">Contact Us</span>
                                            </a>
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

    <!-- How it Works -->
    @include('admin-views.business-settings.landing-page-settings.partial.how-it-work-react')
@endsection

