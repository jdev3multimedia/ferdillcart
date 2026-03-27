@extends('layouts.admin.app')

@section('title',translate('messages.add_new_brand'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/category.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('messages.Brand_Setup')}}
                </span>
            </h1>
        </div>

        <div class="card mt-2">
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper">
                    <h5 class="card-title">{{translate('messages.All_Brand_List')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$brands->total()}}</span></h5>
                    <div class="d-flex gap-3 flex-wrap">
                        <form  class="search-form">
                            <!-- Search -->
                            <div class="input-group input--group">
                                <input id="datatableSearch" name="search" value="{{ request()?->search ?? null }}"  type="search" class="form-control" placeholder="{{translate('messages.search_by_name')}}" aria-label="{{translate('messages.Brands')}}">
                                <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                            </div>
                            <!-- End Search -->
                        </form>

                        <button  type="button" class="btn btn-primary withdraw-info-show2"><i class="tio-add"></i> {{translate('messages.add_new_brand')}}</button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive datatable-custom">
                    <table id="columnSearchDatatable"
                        class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                        data-hs-datatables-options='{
                            "search": "#datatableSearch",
                            "entries": "#datatableEntries",
                            "isResponsive": false,
                            "isShowPaging": false,
                            "paging":false,
                        }'>
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{translate('sl')}}</th>
                                <th class="border-0 w--1">{{translate('messages.Brand_Info')}}</th>
                                <th class="border-0 text-center">{{translate('messages.Total_Products')}}</th>
                                <th class="border-0 text-center">{{translate('messages.status')}}</th>
                                <th class="border-0 text-center">{{translate('messages.action')}}</th>
                            </tr>
                        </thead>

                        <tbody id="table-div">
                        @foreach($brands as $key=>$brand)
                            <tr>
                                <td>{{$key+$brands->firstItem()}}</td>
                                <td>
                                    <div class="media align-items-center">
                                        <img class="avatar avatar-lg mr-3 onerror-image"
                                        src="{{$brand['image_full_url'] ?? asset('public/assets/admin/img/160x160/img2.jpg') }}"  alt="{{$brand->name}} image">
                                        <div  class="media-body">
                                            <h5   class="text-hover-primary mb-0">{{Str::limit($brand['name'],20,'...')}}
                                                @if($brand->module_id == null)
                                                    <span class="ml-2 badge badge-soft-success">{{translate('messages.All_module')}}</span>
                                                @endif
                                            </h5>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center">
                                    <span class="d-block font-size-sm text-body">
                                        {{ $brand->items->count()}}
                                    </span>
                                </td>
                                <td>
                                    <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$brand->id}}">
                                    <input type="checkbox" data-url="{{route('admin.brand.status',[$brand['id'],$brand->status?0:1])}}" class="toggle-switch-input redirect-url" id="stocksCheckbox{{$brand->id}}" {{$brand->status?'checked':''}}>
                                        <span class="toggle-switch-label mx-auto">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        @if ($brand->module_id == null)
                                        <button  title="{{translate('Module_Assign')}}" class="btn action-btn btn--primary btn-outline-primary withdraw-info-show" type="button" data-brand_id="{{ $brand['id'] }}"
                                        data-image_src="{{ $brand['image_full_url'] }}"
                                        data-name="{{ $brand['name'] }}"
                                            ><i class="tio-apps"></i>
                                        </button>
                                        @endif
                                        <a class="btn action-btn withdraw-info-show3 btn--primary btn-outline-primary"
                                        data-id="{{$brand['id']}}"
                                        href="#" title="{{translate('messages.edit_brand')}}">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:" data-id="brand-{{$brand['id']}}" data-message="{{ translate('messages.Want to delete this brand') }}"  title="{{translate('messages.delete_brand')}}"><i class="tio-delete-outlined"></i>
                                    </a>
                                    <form action="{{route('admin.brand.delete',[$brand['id']])}}" method="post" id="brand-{{$brand['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if(count($brands) !== 0)
            <hr>
            @endif
            <div class="page-area">
                {!! $brands->links() !!}
            </div>
            @if(count($brands) === 0)
            <div class="empty--data">
                <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                <h5>
                    {{translate('no_data_found')}}
                </h5>
            </div>
            @endif
        </div>
    </div>


    <div class="modal fade" id="module-change-modal">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="text-center">{{ translate('Update_Module') }}</h3>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true" class="tio-clear"></span>
                    </button>
                </div>
                <div class="modal-body pb-5 pt-0">
                    <div class="max-349 mx-auto mb-20">
                        <div>
                            <div class="text-center">
                                <h5 class="modal-title"> </h5>
                            </div>

                        </div>
                        <div class="btn--container justify-content-center">
                            <button type="button" class="btn btn-outline-info min-w-120" data-toggle="modal" data-target="#Keep_only_this_module_confirmation" data-dismiss="modal" >{{translate('Keep_only_this_module')}}</button>
                            <button type="button" class="btn btn-outline-warning min-w-120" data-toggle="modal"  data-target="#make_a_new_brand_confirmation"  data-dismiss="modal">
                                {{translate("Make it a new Brand")}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add New Brand Offcanvas --}}
    <div class="withdraw-info-sidebar-wrap2">
        <div class="withdraw-info-sidebar withdraw-info-sidebar2 p-0" style="--width: 500px">
            <form action="{{route('admin.brand.store')}}" method="post" enctype="multipart/form-data" class="h-100">
                @csrf
                <div class="d-flex flex-column h-100">
                    <div class="d-flex p-3 justify-content-between mb-3 bg-light">
                        <h4 class="mb-0">{{translate('add_New_Brand')}}</h4>
                        <span class="circle bg-light withdraw-info-hide2 cursor-pointer">
                            <i class="tio-clear"></i>
                        </span>
                    </div>


                    <div class="p-3">
                        <div class="bg-light p-3 rounded">
                            <h4>{{translate('messages.status')}}</h4>
                            <p class="fs-12">{{ translate('messages.If you turn off the switch the brand will not active or visible in customer app & website.') }}</p>

                            <div class="maintenance-mode-toggle-bar d-flex flex-wrap justify-content-between border rounded align-items-center py-2 px-3">
                                <h5 class="text-capitalize m-0 text--primary">{{translate('messages.Status')}}</h5>

                                <label class="toggle-switch toggle-switch-sm">
                                    <input type="checkbox" name="brand_status" checked class="status toggle-switch-input">
                                    <span class="toggle-switch-label text mb-0">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="bg-light p-3 rounded">
                            @if($language)
                                <ul class="nav nav-tabs mb-4">
                                    <li class="nav-item">
                                        <a class="nav-link lang_link active" href="#" id="default-link">{{translate('messages.default')}}</a>
                                    </li>
                                    @foreach ($language as $lang)
                                        <li class="nav-item">
                                            <a class="nav-link lang_link" href="#" id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            @if($language)
                                <div class="form-group lang_form" id="default-form">
                                    <label class="input-label">
                                        {{translate('messages.name')}} ({{ translate('messages.default') }})
                                        <small class="text-danger">*</small>
                                        {{-- <i class="tio-info text-muted" data-toggle="tooltip" title="hello title"></i> --}}
                                    </label>
                                    <input type="text" name="name[]" value="{{ old('name.0') }}"  class="form-control" placeholder="{{translate('messages.new_brand')}}" maxlength="191">
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                                @foreach($language as $key => $lang)
                                    <div class="form-group d-none lang_form" id="{{$lang}}-form">
                                        <label class="input-label">
                                            {{translate('messages.name')}} ({{strtoupper($lang)}})
                                            <small class="text-danger">*</small>
                                            {{-- <i class="tio-info text-muted" data-toggle="tooltip" title="hello title"></i> --}}
                                        </label>
                                        <input type="text" name="name[]" value="{{ old('name.'.$key+1) }}"  class="form-control" placeholder="{{translate('messages.new_brand')}}" maxlength="191">
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                @endforeach
                            @else
                                <div class="form-group">
                                    <label class="input-label">
                                        {{translate('messages.name')}}
                                        <small class="text-danger">*</small>
                                        {{-- <i class="tio-info text-muted" data-toggle="tooltip" title="hello title"></i> --}}
                                    </label>
                                    <input type="text" name="name" class="form-control" placeholder="{{translate('messages.type_brand_name')}}" value="{{old('name')}}" maxlength="191">
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                            @endif
                        </div>

                        <div class="bg-light p-3 rounded my-4">
                            <h4>{{translate('messages.Brand Logo')}} <small class="text-danger">*</small></h4>
                            <p class="fs-12">{{ translate('messages.It will show in website & app.') }}</p>
                            <div class="d-flex justify-content-center">
                                <label class="text-center position-relative d-inline-block mb-3">
                                    <img class="img--176 border" id="viewer"
                                            @if(isset($category))
                                                src="{{asset('storage/app/public/category')}}/{{$category['image']}}"
                                            @else
                                                src="{{asset('public/assets/admin/img/upload-img.png')}}"
                                            @endif
                                            alt="image"/>
                                    <div class="icon-file-group">
                                        <div class="icon-file">
                                            <input type="file" name="image" id="customFileEg1" class="custom-file-input read-url"
                                                    accept=".webp, .jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" >
                                            <i class="tio-edit"></i>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <p class="text-center fs-12">{{translate('messages.JPG, JPEG, PNG Less Than 1MB (Ratio 1 : 1)')}}</p>
                        </div>

                    </div>

                    <div class="bg-white bottom-0 d-flex gap-3 mt-auto p-3 position-sticky shadow-lg">
                        <button  type="reset" id="reset_btn" class="btn btn-secondary btn-block ">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn-primary btn-block mt-0" >{{ translate('messages.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    {{-- edit --}}
    <div class="withdraw-info-sidebar-wrap2">
        <div class="withdraw-info-sidebar withdraw-info-sidebar3 p-0" style="--width: 500px">
            <div id="data-view">

            </div>
        </div>
    </div>

    {{-- Offcanvas --}}
    <div class="withdraw-info-sidebar-wrap">
        <div class="withdraw-info-sidebar-overlay"></div>
        <div class="withdraw-info-sidebar p-0" style="--width: 500px">
            <form action="{{ route('admin.brand.moduleUpadte') }}" method="post" class="h-100">
                @csrf
                <div class="d-flex flex-column h-100">
                    <div class="d-flex p-3 justify-content-between mb-3 bg-light">
                        <h4 class="mb-0">{{translate('Module Assign')}}</h4>
                        <span class="circle bg-light withdraw-info-hide cursor-pointer">
                            <i class="tio-clear"></i>
                        </span>
                    </div>


                    <div class="p-3">
                        <div class="bg-light rounded p-3 mb-3">
                            <div class="text-center mb-3">
                                <div class="d-flex justify-content-center align-items-center mb-4">
                                    <img class="rounded" src="brand-logo.png" id="brand_img_src" alt="Brand Logo" width="50">
                                    <h5 id="brand_name"  class="mt-2 ml-2"></h5>
                                </div>

                                <div class="alert fs-12 alert-primary-light text-dark mb-0  mt-md-0 add_text_mute mt-2"  role="alert">
                                    <img src="{{ asset('/public/assets/admin/img/lnfo_light.png') }}" alt="">
                                    {{translate('Currently, this brand is active in all modules of the')}} <b>{{ Config::get('module.current_module_name') }}</b> {{ translate('Module_Type') }}
                                </div>
                            </div>
                        </div>

                        <input type="text" hidden  name="brand_id"  id="brand_id">
                        <div class="bg-light p-3 rounded mb-3">
                            <h4 class="card-title mb-2 font-medium">{{translate('Assign Brand')}}</h4>
                            <small class="card-text">{{ translate('Select your preferred assign option for this brand') }}</small>

                            <div class="bg-white p-3 rounded mt-4 mb-3">
                                <div class="radio-card selected mb-4 media gap-3" data-value="module-only">
                                    <input class="mt-2" type="radio" id="only-brands" name="type" value="only_this_module" checked>
                                    <label for="only-brands" class="media-body">
                                        <strong>{{ translate('Use this Brand only for this module’s product') }}</strong>
                                        <br>
                                        <small class="text-muted mt-1 mb-0">
                                        {{ translate(' This brand will only use for') }} <strong>{{ Config::get('module.current_module_name') }}</strong> {{ translate('Module and will be removed from other module’s product.') }}
                                        </small>
                                    </label>
                                </div>

                                <div class="radio-card media gap-3"  data-value="all-modules">
                                    <input class="mt-2" type="radio" id="same-brands" name="type" value="copy_this_brand">
                                    <label for="same-brands" class="media-body">
                                        <strong>{{ translate('Create the same brand for other modules also') }}</strong>
                                        <br>
                                        <small class="text-muted mt-1 mb-0">
                                            {{ translate('This brand will be created automatically for every module. And the products in each module will automatically be assigned to that brand.') }}
                                        </small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto shadow-lg p-3 bg-white d-flex gap-3">
                        <button  type="reset" class="btn btn-secondary btn-block withdraw-info-hide">
                            {{translate("Cancel")}}
                        </button>
                        <button type="submit" class="btn btn-primary btn-block mt-0" >{{translate('Transfer')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin')}}/js/view-pages/brand-index.js"></script>
    <script>
        "use strict";
        $('.withdraw-info-hide, .withdraw-info-sidebar-overlay, .withdraw-info-hide2').on('click', function () {
            $('.withdraw-info-sidebar, .withdraw-info-sidebar-overlay, .withdraw-info-sidebar2').removeClass('show');
        });

        $(document).on('click', '.withdraw-info-show', function () {
            $('#brand_img_src').attr('src', $(this).data('image_src'));
            $('#brand_name').text($(this).data('name'));
            $('#brand_id').val($(this).data('brand_id'));
            $('.withdraw-info-sidebar, .withdraw-info-sidebar-overlay').addClass('show');
        });
        $(document).on('click', '.withdraw-info-show2', function () {
            $('.withdraw-info-sidebar2, .withdraw-info-sidebar-overlay').addClass('show');
        });

        $(document).on('click', '.withdraw-info-show3', function () {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('admin.brand.getBrandData')}}",
                type: "get",
                data: { id: id },
                beforeSend: function () {
                    $('#data-view').empty();
                    $('#loading').show()
                },
                success: function(data) {
                    $('.withdraw-info-sidebar3, .withdraw-info-sidebar-overlay').addClass('show');
                    $("#data-view").append(data.view);
                },
                complete: function () {
                    $('#loading').hide()
                }
            })

        });
        $(document).on('submit', '.withdraw_status_form', function (event) {
            $(this).find('button[type="submit"]').attr('disabled', true);
});

        $('#reset_btn').click(function(){
            $('#viewer').attr('src', "{{asset('public/assets/admin/img/upload-img.png')}}");
        })
    </script>
@endpush
