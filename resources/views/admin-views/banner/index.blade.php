@extends('layouts.admin.app')

@section('title',translate('messages.banner'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/banner.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.add_new_banner')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form id="banner_form" class="custom-validation" data-ajax="true">
                            
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    @if ($language)
                                    <ul class="nav nav-tabs mb-3 border-0">
                                        <li class="nav-item">
                                            <a class="nav-link lang_link active"
                                            href="#"
                                            id="default-link">{{translate('messages.default')}}</a>
                                        </li>
                                        @foreach ($language as $lang)
                                            <li class="nav-item">
                                                <a class="nav-link lang_link"
                                                    href="#"
                                                    id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="lang_form" id="default-form">
                                        <div class="form-group error-wrapper">
                                            <label class="input-label"
                                                for="default_title">{{ translate('messages.title') }}
                                                (Default)
                                            </label>
                                            <input type="text" name="title[]" id="default_title"
                                                class="form-control" placeholder="{{ translate('messages.new_banner') }}" required
                                            >
                                        </div>
                                        <input type="hidden" name="lang[]" value="default">
                                    </div>
                                        @foreach ($language as $lang)
                                            <div class="d-none lang_form"
                                                id="{{ $lang }}-form">
                                                <div class="form-group error-wrapper">
                                                    <label class="input-label"
                                                        for="{{ $lang }}_title">{{ translate('messages.title') }}
                                                        ({{ strtoupper($lang) }})
                                                    </label>
                                                    <input type="text" name="title[]" id="{{ $lang }}_title"
                                                        class="form-control" placeholder="{{ translate('messages.new_banner') }}">
                                                </div>
                                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                                            </div>
                                        @endforeach
                                    @else
                                        <div id="default-form">
                                            <div class="form-group error-wrapper">
                                                <label class="input-label"
                                                    for="exampleFormControlInput1">{{ translate('messages.title') }} ({{ translate('messages.default') }})</label>
                                                <input type="text" name="title[]" class="form-control"
                                                    placeholder="{{ translate('messages.new_banner') }}" required>
                                            </div>
                                            <input type="hidden" name="lang[]" value="default">
                                        </div>
                                    @endif
                                    <div class="form-group error-wrapper">
                                        <label class="input-label" for="title">{{translate('messages.zone')}}</label>
                                        <select name="zone_id" id="zone" class="form-control js-select2-custom" required>
                                            <option disabled selected>---{{translate('messages.select')}}---</option>
                                            @foreach($zones as $zone)
                                                @if(isset(auth('admin')->user()->zone_id))
                                                    @if(auth('admin')->user()->zone_id == $zone->id)
                                                        <option value="{{$zone->id}}" selected>{{$zone->name}}</option>
                                                    @endif
                                                @else
                                                    <option value="{{$zone['id']}}">{{$zone['name']}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group error-wrapper">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.banner_type')}}</label>
                                        <select name="banner_type" id="banner_type" class="form-control">
                                            <option value="store_wise">{{translate('messages.store_wise')}}</option>
                                            <option value="item_wise">{{translate('messages.item_wise')}}</option>
                                            <option value="default">{{translate('messages.default')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0 error-wrapper" id="store_wise">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.store')}}<span
                                                class="input-label-secondary"></span></label>
                                        <select name="store_id" id="store_id" class="js-data-example-ajax form-control"  title="{{translate('messages.select_store')}}">
                                            <option disabled selected>---{{translate('messages.select_store')}}---</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0 error-wrapper" id="item_wise">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.select_item')}}</label>
                                        <select name="item_id" id="choice_item" class="form-control js-select2-custom" placeholder="{{translate('messages.select_item')}}">

                                        </select>
                                    </div>
                                    <div class="form-group mb-0 error-wrapper" id="default">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.default_link')}}({{ translate('messages.optional') }})</label>
                                        <input type="text" name="default_link" class="form-control" placeholder="{{translate('messages.default_link')}}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="error-wrapper">
                                        <div class="h-100 d-flex flex-column">
                                            <label class="mt-auto mb-0 d-block text-center">{{translate('messages.banner_image')}} <small class="text-danger">* ( {{translate('messages.ratio')}} 3:1 )</small></label>
                                            <div class="text-center py-3 my-auto">
                                                <img class="img--vertical" id="viewer"
                                                    src="{{asset('public/assets/admin/img/900x400/img1.jpg')}}" alt="banner image"/>
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                                    accept=".webp, .jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                                <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose_file')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <div class="btn--container justify-content-end">
                                        <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                        <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper">
                            <h5 class="card-title">
                                {{translate('messages.banner_list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$banners->count()}}</span>
                            </h5>
                            <form  class="search-form">
                                <!-- Search -->
                                <div class="input-group input--group">
                                    <input id="datatableSearch" type="search" value="{{ request()->get('search')?? '' }}" name="search" class="form-control" placeholder="{{translate('messages.search_by_title')}}" aria-label="{{translate('messages.search_here')}}">
                                    <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                                </div>
                                <!-- End Search -->
                            </form>
                            @if(request()->get('search'))
                            <button type="reset" class="btn btn--primary ml-2 location-reload-to-base" data-url="{{url()->full()}}">{{translate('messages.reset')}}</button>
                            @endif

                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                data-hs-datatables-options='{
                                    "order": [],
                                    "orderCellsTop": true,
                                    "search": "#datatableSearch",
                                    "entries": "#datatableEntries",
                                    "isResponsive": false,
                                    "isShowPaging": false,
                                    "paging": false
                                }'
                                >
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0">{{ translate('messages.SL') }}</th>
                                    <th class="border-0">{{translate('messages.title')}}</th>
                                    <th class="border-0">{{translate('messages.type')}}</th>
                                    <th class="border-0 text-center">{{translate('messages.featured')}} <span class="input-label-secondary"
                                        data-toggle="tooltip" data-placement="right" data-original-title="{{translate('if_you_turn/off_on_this_featured,_it_will_effect_on_website_&_user_app')}}"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}"
                                            alt="public/img"></span></th>
                                    <th class="border-0 text-center">{{translate('messages.status')}}</th>
                                    <th class="border-0 text-center">{{translate('messages.action')}}</th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($banners as $key=>$banner)
                                <tr>
                                    <td>{{$key+$banners->firstItem()}}</td>
                                    <td>
                                        <span class="media align-items-center">
                                            <img class="img--ratio-3 w-auto h--50px rounded mr-2 onerror-image" src="{{ $banner['image_full_url'] }}"
                                                data-onerror-image="{{asset('/public/assets/admin/img/900x400/img1.jpg')}}" alt="{{$banner->name}} image">
                                            <div class="media-body">
                                                <h5 title="{{ $banner['title'] }}" class="text-hover-primary mb-0">{{Str::limit($banner['title'], 25, '...')}}</h5>
                                            </div>
                                        </span>
                                    <span class="d-block font-size-sm text-body">

                                    </span>
                                    </td>
                                    <td>{{translate('messages.'.$banner['type'])}}</td>

                                    <td  >
                                        <div class="d-flex justify-content-center">
                                            <label class="toggle-switch toggle-switch-sm" for="featuredCheckbox{{$banner->id}}">
                                            <input type="checkbox"
                                            data-id="featuredCheckbox{{$banner->id}}"
                                            data-type="status"
                                            data-image-on="{{ asset('/public/assets/admin/img/modal/basic_campaign_on.png') }}"
                                            data-image-off="{{ asset('/public/assets/admin/img/modal/basic_campaign_off.png') }}"
                                            data-title-on="{{ translate('By_Turning_ON_As_Featured!') }}"
                                            data-title-off="{{ translate('By_Turning_OFF_As_Featured!') }}"
                                            data-text-on="<p>{{ translate('If_you_turn_on_this_featured,_then_promotional_banner_will_show_on_website_and_user_app_with_store_or_item.') }}</p>"
                                            data-text-off="<p>{{ translate('If_you_turn_off_this_featured,_then_promotional_banner_won’t_show_on_website_and_user_app') }}</p>"
                                            class="toggle-switch-input  dynamic-checkbox" id="featuredCheckbox{{$banner->id}}" {{$banner->featured?'checked':''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        </div>
                                    </td>
                                    <form action="{{route('admin.banner.featured',[$banner['id'],$banner->featured?0:1])}}"
                                        method="get" id="featuredCheckbox{{$banner->id}}_form">
                                        </form>

                                    <td  >
                                        <div class="d-flex justify-content-center">
                                            <label class="toggle-switch toggle-switch-sm" for="statusCheckbox{{$banner->id}}">
                                            <input type="checkbox"
                                            data-id="statusCheckbox{{$banner->id}}"
                                            data-type="status"
                                            data-image-on="{{ asset('/public/assets/admin/img/modal/basic_campaign_on.png') }}"
                                            data-image-off="{{ asset('/public/assets/admin/img/modal/basic_campaign_off.png') }}"
                                            data-title-on="{{ translate('By_Turning_ON_Banner!') }}"
                                            data-title-off="{{ translate('By_Turning_OFF_Banner!') }}"
                                            data-text-on="<p>{{ translate('If_you_turn_on_this_status,_it_will_show_on_user_website_and_app.') }}</p>"
                                            data-text-off="<p>{{ translate('If_you_turn_off_this_status,_it_won’t_show_on_user_website_and_app') }}</p>"
                                            class="toggle-switch-input  dynamic-checkbox" id="statusCheckbox{{$banner->id}}" {{$banner->status?'checked':''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        </div>
                                    </td>

                                    <form action="{{route('admin.banner.status',[$banner['id'],$banner->status?0:1])}}"
                                        method="get" id="statusCheckbox{{$banner->id}}_form">
                                        </form>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn--primary btn-outline-primary" href="{{route('admin.banner.edit',[$banner['id']])}}" title="{{translate('messages.edit_banner')}}"><i class="tio-edit"></i>
                                            </a>
                                            <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:" data-id="banner-{{$banner['id']}}" data-message="{{ translate('Want to delete this banner ?') }}"><i class="tio-delete-outlined"></i>
                                            </a>
                                            <form action="{{route('admin.banner.delete',[$banner['id']])}}"
                                                        method="post" id="banner-{{$banner['id']}}">
                                                    @csrf @method('delete')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                    @if(count($banners) !== 0)
                    <hr>
                    @endif
                    <div class="page-area">
                        {!! $banners->links() !!}
                    </div>
                    @if(count($banners) === 0)
                    <div class="empty--data">
                        <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                        <h5>
                            {{translate('no_data_found')}}
                        </h5>
                    </div>
                    @endif
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin')}}/js/view-pages/banner-index.js"></script>
    <script>
        "use strict";
        var module_id = {{Config::get('module.current_module_id')}};

        function get_items()
        {
            var nurl = '{{url('/')}}/admin/item/get-items?module_id='+module_id;

            if(!Array.isArray(zone_id))
            {
                nurl += '&zone_id='+zone_id;
            }

            $.get({
                url: nurl,
                dataType: 'json',
                success: function (data) {
                    $('#choice_item').empty().append(data.options);
                }
            });
        }

        $(document).on('ready', function () {

            module_id = {{Config::get('module.current_module_id')}};
            get_items();

            $('.js-data-example-ajax').select2({
                ajax: {
                    url: '{{url('/')}}/admin/store/get-stores',
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            zone_ids: [zone_id],
                            page: params.page,
                            module_id: module_id
                        };
                    },
                    processResults: function (data) {
                        return {
                        results: data
                        };
                    },
                    __port: function (params, success, failure) {
                        var $request = $.ajax(params);

                        $request.then(success);
                        $request.fail(failure);

                        return $request;
                    }
                }
            });

        });

        $('#banner_form').on('submit', function (e) {
            e.preventDefault();

            let $form = $(this);
            if (!$form.valid()) {
                return false;
            }

            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: "{{route('admin.banner.store')}}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('{{translate("messages.banner_added_successfully")}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route("admin.banner.add-new")}}';
                        }, 2000);
                    }
                }
            });
        });



        $('#reset_btn').click(function(){
        $('#module_select').val(null).trigger('change');
        $('#zone').val(null).trigger('change');
        $('#store_id').val(null).trigger('change');
        $('#choice_item').val(null).trigger('change');
        $('#viewer').attr('src','{{asset('public/assets/admin/img/900x400/img1.jpg')}}');
    })
    </script>
@endpush
