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
            <span>{{ translate('Manage Page SEO') }}</span>
        </h1> 
    </div>
    <div class="bg-opacity-primary-10 rounded py-2 px-3 d-flex flex-wrap gap-1 align-items-center mb-20">
        <div class="gap-1 d-flex align-items-center">
            <i class="tio-light-on theme-clr-dark fs-16"></i>
            <p class="m-0 fs-12">{{ translate('Manage meta information to improve page performance in search results') }}</p>
        </div>
    </div>
    <!-- End Page Header -->
 
    <div class="card">
        <div class="card-header flex-wrap pt-3 pb-3 border-0 gap-2">
            <div class="search--button-wrapper mr-1">
                <h4 class="card-title fs-16 text-dark">{{ translate('SEO Setup List')}} <span class="badge badge-soft-dark ml-2 rounded-circle fs-12" id="itemCount">{{ count($pages) }}</span></h4>
                <form class="search-form min--260" onsubmit="event.preventDefault()">
                    <div class="input-group input--group">
                        <input id="datatableSearch_" type="search" name="search" class="form-control h--40px" placeholder="{{ translate('Search Page Name') }}" aria-label="Search" tabindex="1">

                        <button type="button" class="btn btn--secondary bg-modal-btn"><i class="tio-search text-muted"></i></button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <!-- Table -->
            <div class="table-responsive space-around-16 datatable-custom">
                <table class="table table-borderless table-thead-borderless table-align-middle table-nowrap card-table m-0">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0 min-w--120">{{ translate('SL') }}</th>
                            <th class="border-0">{{ translate('Pages') }}</th>
                            <th class="border-0 text-right">{{ translate('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pages as $key=> $page)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                    {{translate($page == 'contact_us_page' ? 'Help & Support Page' : $page)}}
                                </div>
                            </td>
                            <td>
                                <div class="text-right">
                                    <a href="{{ route('admin.business-settings.seo-settings.pageMetaData', ['page_name' => $page]) }}" class="btn {{ isset($pageMetaData[$page][0]) ? 'btn-outline-theme-dark' : 'btn-outline-success' }}">
                                        @if (isset($pageMetaData[$page][0]))
                                            <i class="tio-edit"></i> {{ translate('Edit Content') }}
                                        @else
                                            <i class="tio-add"></i> {{ translate('Add Content') }}
                                        @endif
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3">
                                <div class="empty--data">
                                    <img src="{{asset('public/assets/admin/img/modal/pending-order-off.png')}}" alt="public">
                                    <h5>
                                        {{translate('no_data_found')}}
                                    </h5>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                        <tr class="empty-data-row" style="display: none;">
                            <td colspan="3">
                                <div class="empty--data">
                                    <img src="{{asset('public/assets/admin/img/modal/pending-order-off.png')}}" alt="public">
                                    <h5>
                                        {{translate('no_data_found')}}
                                    </h5>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- End Table -->
        </div>
    </div>             
</div>

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
                        </div>
                        <div class="collapse mt-3 show" id="collapseGeneralSetup_01">
                            <div class="card rounded border p-3 card-body">
                                <div class="mb-3">
                                    <p class="m-0 fs-12 color-656566">
                                        <strong>{{ translate('Meta Data Setup') }}</strong> {{ translate('allows you to define how each page of your e-commerce site appears in:') }}
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

@push('script_2')
    <script>
        $('#datatableSearch_').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            var count = 0;
            $('tbody tr').each(function () {
                var row = $(this);
                if (row.hasClass('empty-data-row')) return;

                var text = row.find('td:eq(1)').text().toLowerCase();
                if (text.indexOf(value) > -1) {
                    row.show();
                    count++;
                } else {
                    row.hide();
                }
            });
            $('#itemCount').text(count);

            if (count === 0) {
                $('.empty-data-row').show();
            } else {
                $('.empty-data-row').hide();
            }
        });
    </script>
@endpush

