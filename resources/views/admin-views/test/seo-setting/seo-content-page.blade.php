@extends('layouts.admin.app')

@section('title',translate('messages.SEO Setup'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title text-break">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/seo-setting.png')}}" class="w--26" alt="">
            </span>
            <span>Manage Page SEO</span>
        </h1> 
    </div>
    <!-- End Page Header -->
 
    <div class="card">
        <div class="card-header flex-sm-nowrap flex-wrap pt-3 pb-3 gap-2">
            <div class="">
                <h4 class="fs-16 text-dark">{{ translate('messages.Meta Data Setup')}}</h4>            
                <p class="fs-12 m-0">{{ translate('messages.Include Meta Information to improve search engine visibility and social media sharing')}}</p>            
            </div>
            <a href="#0" class="theme-clr text-nowrap text-underline fs-14 font-weight-medium">
                Back to List
            </a>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-xxl-8 col-lg-7">
                    <div class="bg-light2 rounded p-sm-4 p-3 h-100">
                        <ul class="nav nav-tabs mb-20">
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
                        <div class="d-flex flex-column gap-2">
                            <div class="form-group m-0">
                                <label for="" class>
                                    {{ translate('Meta Title (EN)') }} 
                                    <span data-toggle="tooltip" data-placement="right"
                                    data-original-title="{{ translate('Content This Title appears in browser tabs, search results, and link previews. Use a short ,clear, and keyword-focused title(recommended: 80-100 characters)') }}"><i class="tio-info text-muted fs-14"></i></span>
                                </label>
                                <textarea type="text" rows="1" maxlength="100" placeholder="Let’s" class="form-control"></textarea>
                                <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                            </div>
                            <div class="form-group m-0">
                                <label for="" class>
                                    {{ translate('Meta Description (EN)') }} 
                                    <span data-toggle="tooltip" data-placement="right"
                                    data-original-title="{{ translate('A brief summary that appears under your page title in search results. Keep it compelling and relevant (recommended: 120-160 characters)') }}"><i class="tio-info text-muted fs-14"></i></span>
                                </label>
                                <textarea type="text" rows="1" maxlength="200" placeholder="Manage your business Smartly" class="form-control"></textarea>
                                <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-lg-5">
                    <div class="bg-light2 d-center rounded p-4 h-100">
                        <div class="">
                            <div class="mb-30 text-center">
                                <h4 class="mb-1">{{ translate('Meta Image') }} </h4>
                                <p class="mb-0 fs-12 gray-dark">
                                    {{translate('Upload a rectangular image ')}}
                                </p>
                            </div>
                            <div class="mx-auto text-center">
                                <div class="upload-file_custom ratio-2-1 h-100px">
                                    <input class="upload-file__input single_file_input" type="file" id="" name="" accept="">
                                    <label for="" class="upload-file__wrapper w-100 h-100 m-0">
                                        <div class="upload-file-textbox text-center">
                                            <img width="22" class="svg" src="{{ asset('public/assets/admin/img/document-upload.svg') }}" alt="img">
                                            <h6 class="mt-1 fw-medium fs-10 lh-base text-center">
                                                <span class="theme-clr">{{ translate('Add') }}</span>
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
                                <p class="mt-20 fs-12 mb-0 gray-dark">
                                    JPG, JPEG, PNG size : Max 2 MB <strong class="text-dark">(2:1)</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bg-light2 rounded p-sm-4 p-3 h-100">
                        <div class="resturant-type-group gap-2 py-3 px-3 bg-white rounded mb-20">
                            <label class="form-check flex-grow-1 form--check">
                                <input class="form-check-input" type="radio" value="1" name="index_status" checked>
                                <span class="form-check-label">Index</span>
                                <span class="ms-4px" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Allow search engines to put this web page on their list or index & show it on search results') }}">
                                    <i class="tio-info text-muted fs-14"></i>
                                </span>
                            </label>
                            <label class="form-check flex-grow-1 form--check">
                                <input class="form-check-input" type="radio" value="0" name="index_status">
                                <span class="form-check-label">No Index</span>
                                <span class="ms-4px" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Disallow search engines from putting this web page on their list or index, and do not show it on search results') }}">
                                    <i class="tio-info text-muted fs-14"></i>
                                </span>
                            </label>
                        </div>
                        <div class="bg-white rounded follow-type-group py-3 px-3">
                            <label class="custom_checkbox d-flex align-items-center gap-1 flex-grow-1 m-0">
                                <input type="checkbox" value="1" name="follow" checked>
                                <span class="text-nowrap label-text">No Follow</span>
                                <span class="ms-4px" data-toggle="tooltip" data-placement="right"
                                    data-original-title="{{ translate('Instruct search engines not to follow links from this webpage.') }}">
                                    <i class="tio-info text-muted fs-14"></i>
                                </span>
                            </label>
                            <label class="custom_checkbox d-flex align-items-center gap-1 flex-grow-1 m-0">
                                <input type="checkbox" value="1" name="follow" checked>
                                <span class="text-nowrap label-text">No Index</span>
                                <span class="ms-4px" data-toggle="tooltip" data-placement="right"
                                    data-original-title="{{ translate('Disallow search engines from putting this web page on their list or index, and do not show it on search results') }}">
                                    <i class="tio-info text-muted fs-14"></i>
                                </span>
                            </label>
                            <label class="custom_checkbox d-flex align-items-center gap-1 flex-grow-1 m-0">
                                <input type="checkbox" value="1" name="follow" checked>
                                <span class="text-nowrap label-text">No Image Index</span>
                                <span class="ms-4px" data-toggle="tooltip" data-placement="right"
                                    data-original-title="{{ translate(' Prevent images from being listed or indexed by search engines') }}">
                                    <i class="tio-info text-muted fs-14"></i>
                                </span>
                            </label>
                            <label class="custom_checkbox d-flex align-items-center gap-1 flex-grow-1 m-0">
                                <input type="checkbox" value="1" name="follow" checked>
                                <span class="text-nowrap label-text">No Snippet</span>
                                <span class="ms-4px" data-toggle="tooltip" data-placement="right"
                                    data-original-title="{{ translate('Instruct search engines not to show a summary or snippet of this webpage s content in search results.') }}">
                                    <i class="tio-info text-muted fs-14"></i>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bg-light2 rounded p-sm-4 p-3 h-100">
                        <div class="bg-white rounded py-3 px-3">
                            <div class="row g-1 align-items-center mb-3">
                                <div class="col-sm-6">
                                    <label class="custom_checkbox d-flex align-items-center gap-1 flex-grow-1 m-0">
                                        <input type="checkbox" value="1" name="follow" checked>
                                        <span class="label-text">Max Snippet</span>
                                        <span class="ms-4px" data-toggle="tooltip" data-placement="right"
                                            data-original-title="{{ translate('Determine the maximum length of a snippet or preview text of the webpage.') }}">
                                            <i class="tio-info text-muted fs-14"></i>
                                        </span>
                                    </label>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group m-0">
                                        <input type="text" placeholder="0" class="form-control min-h-35px h--35px">
                                    </div>
                                </div>
                            </div>
                            <div class="row g-1 align-items-center mb-3">
                                <div class="col-sm-6">
                                    <label class="custom_checkbox d-flex align-items-center gap-1 flex-grow-1 m-0">
                                        <input type="checkbox" value="1" name="follow" checked>
                                        <span class="label-text">Max Video Preview</span>
                                        <span class="ms-4px" data-toggle="tooltip" data-placement="right"
                                            data-original-title="{{ translate('Determine the maximum duration of a video preview that search engines will display') }}">
                                            <i class="tio-info text-muted fs-14"></i>
                                        </span>
                                    </label>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group m-0">
                                        <input type="text" placeholder="10" class="form-control min-h-35px h--35px">
                                    </div>
                                </div>
                            </div>
                            <div class="row g-1 align-items-center">
                                <div class="col-sm-6">
                                    <label class="custom_checkbox d-flex align-items-center gap-1 flex-grow-1 m-0">
                                        <input type="checkbox" value="1" name="follow" checked>
                                        <span class="label-text">Max Image Preview</span>
                                        <span class="ms-4px" data-toggle="tooltip" data-placement="right"
                                            data-original-title="{{ translate('Determine the maximum size or dimensions of an image preview that search engines will display') }}">
                                            <i class="tio-info text-muted fs-14"></i>
                                        </span>
                                    </label>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group m-0">
                                        <select name="sizeSelect" class="min-h-35px h--35px custom-select py-1" id="">
                                            <option value="">Large</option>
                                            <option value="">Medium</option>
                                            <option value="">Small</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           <div class="btn--container justify-content-end mt-4">
                <button type="reset" id="reset_btn" class="btn btn--reset min-w-120px">Reset</button>
                <button type="submit" class="btn btn--primary min-w-120px">Save</button>
            </div>
        </div>
    </div>             

</div>
<div class="tour-guide-items offcanvas-trigger text-capitalize fs-14 text-title cursor-pointer" data-target="#global_guideline_offcanvas">{{ translate('Guideline') }}</div>

<!-- global guideline view Offcanvas here -->
<div id="global_guideline_offcanvas" class="custom-offcanvas d-flex flex-column justify-content-between">
    <form action="{{ route('taxvat.store') }}" method="post">
        <div>
            <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                <div class="py-1">
                    <h3 class="mb-0 line--limit-1">{{ translate('messages.Meta Data Setup') }}</h3>
                </div>
                <button type="button" class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"aria-label="Close">
                    &times;
                </button>
            </div>
            <div class="custom-offcanvas-body custom-offcanvas-body-100  p-20">
                <div class="">
                    <div class="py-3 px-3 bg-light rounded mb-3">
                        <div class="d-flex gap-3 align-items-center justify-content-between overflow-hidden">
                            <button class="btn-collapse line--limit-1 d-flex gap-3 align-items-center bg-transparent border-0 p-0 collapse show" type="button"
                                    data-toggle="collapse" data-target="#collapseGeneralSetup_01" aria-expanded="true">
                                <div class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                                    <i class="tio-down-ui top-01 color-656566"></i>
                                </div>
                                <span class="font-semibold text-left fs-14 text-title line--limit-1">{{ translate('What is Metadata Setup for pages?') }}</span>
                            </button>
                            <!-- <a href="javascript:void(0)" class="fs-12 text-nowrap theme-clr text-underline">
                                {{translate('Let’s Setup')}}
                            </a> -->
                        </div>
                        <div class="collapse mt-3 show" id="collapseGeneralSetup_01">
                            <div class="card rounded border p-3 card-body">
                                <div class="mb-3">
                                    <p class="m-0 fs-12 color-656566">
                                        <strong>Meta Data Setup</strong> allows you to define how each page of your e-commerce site appears in:
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <ul class="mb-0 list-group pl-3 d-flex flex-column gap-1px">
                                        <li class="fs-12 color-656566"><strong>{{translate('Search engines')}}</strong> {{translate(' (Google, Bing, etc.)')}}</li>
                                        <li class="fs-12 color-656566"><strong>{{translate('Social media shares')}}</strong> {{translate(' (Facebook, WhatsApp, Twitter, LinkedIn)')}}</li>
                                    </ul>
                                </div>
                                <p class="m-0 fs-12 color-656566">
                                    <strong>{{ translate('Important Note:') }}</strong> {{ translate('Metadata does not change page content, but it strongly affects visibility, traffic, and click-through rate.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-12 p-sm-20 bg-light rounded mb-3">
                        <div class="d-flex gap-3 align-items-center justify-content-between overflow-hidden">
                            <button class="btn-collapse line--limit-1 d-flex gap-3 align-items-center bg-transparent border-0 p-0 collapsed" type="button"
                                    data-toggle="collapse" data-target="#collapseGeneralSetup_032" aria-expanded="true">
                                <div class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1 collapsed">
                                    <i class="tio-down-ui top-01 color-656566"></i>
                                </div>
                                <span class="font-semibold text-left fs-14 text-title line--limit-1">{{ translate('Why Set Up Metadata for Pages?') }}</span>
                            </button>
                        </div>
                        <div class="collapse mt-3" id="collapseGeneralSetup_032">
                            <div class="card rounded border p-3 card-body"> 
                                <div class="mb-3">
                                    <p class="m-0 font-weight-medium color-656566 fs-12">{{translate('Different e-commerce pages serve other purposes, so they need different SEO behaviour. Overall, This Setup Is Important for')}}</p>
                                </div>                               
                                <div class="mb-3">
                                    <h6 class="mb-2 fs-12 color-656566">{{translate('Calculate Tax Included in Product Price')}}</h6>
                                    <ul class="mb-0 list-group pl-3 d-flex flex-column gap-1px">
                                        <li class="fs-12 color-656566">{{translate('Improves Google ranking')}}</li>
                                        <li class="fs-12 color-656566">{{translate('Increases organic traffic')}}</li>
                                        <li class="fs-12 color-656566">{{translate('Controls which pages appear in search')}}</li>
                                        <li class="fs-12 color-656566">{{translate('Improves social media sharing previews')}}</li>
                                        <li class="fs-12 color-656566">{{translate('Prevents private pages from being indexed')}}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-12 p-sm-20 bg-light rounded mb-3">
                        <div class="d-flex gap-3 align-items-center justify-content-between overflow-hidden">
                            <button class="btn-collapse line--limit-1 d-flex gap-3 align-items-center bg-transparent border-0 p-0 collapsed" type="button"
                                    data-toggle="collapse" data-target="#collapseGeneralSetup_033" aria-expanded="true">
                                <div class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1 collapsed">
                                    <i class="tio-down-ui top-01 color-656566"></i>
                                </div>
                                <span class="font-semibold text-left fs-14 text-title line--limit-1">{{ translate('How to Set up Metadata for Pages?') }}</span>
                            </button>
                        </div>
                        <div class="collapse mt-3" id="collapseGeneralSetup_033">
                            <div class="card rounded border p-3 card-body"> 
                                <div class="mb-3">
                                    <h6 class="mb-2 fs-12 color-656566">{{translate('Before activation')}}</h6>
                                    <ul class="mb-0 list-group pl-3 d-flex flex-column gap-1px">
                                        <li class="fs-12 color-656566">{{translate('Add Page A Specific, Meaningful Text')}}</li>                                            
                                        <li class="fs-12 color-656566">{{translate('Avoid copying the same text across pages, and Use keywords naturally.')}}</li>                                            
                                    </ul>
                                </div>
                                <div class="mb-3">
                                    <h6 class="mb-2 fs-12 color-656566">{{translate('Upload Meta Image')}}</h6>
                                    <ul class="mb-0 list-group pl-3 d-flex flex-column gap-1px">
                                        <li class="fs-12 color-656566">{{translate('Used for social sharing previews')}}</li>                                            
                                        <li class="fs-12 color-656566">{{translate('Maintain the Recommended Ratio & Size')}}</li>                                            
                                    </ul>
                                </div>
                                <div class="mb-3">
                                    <h6 class="mb-2 fs-12 color-656566">{{translate('Select the necessary options as per instructions')}}</h6>
                                    <ul class="mb-0 list-group pl-3 d-flex flex-column gap-1px">
                                        <li class="fs-12 color-656566"><strong class="text-dark">{{translate('Index:')}}</strong> {{translate('Allow search engines to show this page')}}</li>                                            
                                        <li class="fs-12 color-656566"><strong class="text-dark">{{translate('No Index:')}}</strong> {{translate('Hide page from search results')}}</li>                                            
                                        <li class="fs-12 color-656566"><strong class="text-dark">{{translate('No Follow:')}}</strong> {{translate('Prevents search engines from following links on this page')}}</li>                                            
                                        <li class="fs-12 color-656566"><strong class="text-dark">{{translate('No Image Index:')}}</strong> {{translate('Prevents images from appearing in Google Image search. Use for private/system pages')}}</li>                                            
                                        <li class="fs-12 color-656566"><strong class="text-dark">{{translate('Max Snippet:')}}</strong> {{translate('Controls text shown in Google results')}}</li>                                            
                                        <li class="fs-12 color-656566"><strong class="text-dark">{{translate('Max Video Preview:')}}</strong> {{translate('Video preview length')}}</li>                                            
                                        <li class="fs-12 color-656566"><strong class="text-dark">{{translate('Max Image Preview:')}}</strong> {{translate('Small / Larges')}}</li>                                            
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div id="offcanvasOverlay" class="offcanvas-overlay"></div>
<!-- global guideline view Offcanvas end -->
@endsection

