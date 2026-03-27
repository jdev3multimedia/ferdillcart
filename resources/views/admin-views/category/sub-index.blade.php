@extends('layouts.admin.app')

@section('title',translate('messages.Add new sub category'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/edit.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('messages.add_new_sub_category')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.category.store')}}" method="post">
                @csrf
                    <div class="row">
                    @if($language)
                        @php($defaultLang = $language[0])
                        <div class="col-sm-12">
                            <ul class="nav nav-tabs mb-4">
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
                        </div>
                        <div class="form-group lang_form col-sm-6" id="default-form">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}} ({{ translate('messages.default') }}) <span class="form-label-secondary text-danger"
                                data-toggle="tooltip" data-placement="right"
                                data-original-title="{{ translate('messages.Required.')}}"> *
                                </span>
                            </label>
                            <input type="text" name="name[]" class="form-control" placeholder="{{translate('messages.new_sub_category')}}" maxlength="191"  >
                        </div>
                        <input type="hidden" name="lang[]" value="default">
                        @foreach($language as $lang)
                            <div class="form-group d-none lang_form col-sm-6" id="{{$lang}}-form">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}} ({{strtoupper($lang)}})</label>
                                <input type="text" name="name[]" class="form-control" placeholder="{{translate('messages.new_sub_category')}}" maxlength="191"  >
                            </div>
                            <input type="hidden" name="lang[]" value="{{$lang}}">
                        @endforeach
                    @else
                        <div class="form-group col-sm-6">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}}</label>
                            <input type="text" name="name" class="form-control" placeholder="{{translate('messages.new_sub_category')}}" value="{{old('name')}}" maxlength="191">
                        </div>
                        <input type="hidden" name="lang[]" value="default">
                    @endif
                        <div class="form-group col-sm-6">
                            <label class="input-label"
                                for="exampleFormControlSelect1">{{translate('messages.main_category')}}
                                <span class="input-label-secondary">*</span></label>
                            <select id="exampleFormControlSelect1" name="parent_id" class="form-control js-select2-custom" required>
                                <option value="" selected disabled>{{translate('Select Main Category')}}</option>
                                @foreach($mainCategories as $category)
                                    <option value="{{$category['id']}}" >{{$category['name']}} ({{Str::limit($category->module->module_name, 15, '...')}})</option>
                                @endforeach
                            </select>
                        </div>
                        <input name="position" value="1" hidden>

                          <div class="form-group col-sm-6">
                                <label class="input-label" for="">
                                    {{ translate('messages.Priority') }}
                                </label>
                                <select required name="priority"
                                    data-original-title="{{ translate('messages.Select_Priority') }}"
                                    class="custom-select">
                                    <option value="0">{{ translate('messages.Normal') }}</option>
                                    <option value="1">{{ translate('messages.Medium') }}</option>
                                    <option value="2">{{ translate('messages.High') }}</option>
                                </select>
                            </div>

                        <div class="col-sm-12">
                            <div class="btn--container justify-content-end">
                                <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('messages.add')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mt-2">
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper">
                    <h5 class="card-title">{{translate('messages.sub_category_list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$categories->total()}}</span></h5>

                    <form   class="search-form">
                        <!-- Search -->
                        <div class="input-group input--group">
                            <input id="datatableSearch" data-reload_url="{{url()->full()}}" name="search" value="{{ request()?->search ?? null }}"  type="search" class="form-control" placeholder="{{translate('messages.ex_:_search_sub_categories')}}" aria-label="{{translate('messages.ex_:_sub_categories')}}">
                            <input type="hidden" name="position" value="1">
                            <input type="hidden" name="sub_category" value="1">
                            <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                        </div>
                        <!-- End Search -->
                    </form>
                    @if(request()->get('search'))
                    <button type="reset" class="btn btn--primary ml-2 location-reload-to-category" data-url="{{url()->full()}}">{{translate('messages.reset')}}</button>
                    @endif
                    <!-- Unfold -->
                    <div class="hs-unfold mr-2">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40" href="javascript:;"
                            data-hs-unfold-options='{
                                    "target": "#usersExportDropdown",
                                    "type": "css-animation"
                                }'>
                            <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                        </a>

                        <div id="usersExportDropdown"
                            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">

                            <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                            <a id="export-excel" class="dropdown-item" href="{{ route('admin.category.export-categories', ['type' => 'excel', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="{{ route('admin.category.export-categories', ['type' => 'csv', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                    alt="Image Description">
                                .{{ translate('messages.csv') }}
                            </a>

                        </div>
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
                                <th class="border-0">{{translate('messages.id')}}</th>
                                <th class="border-0 w--1">{{translate('messages.main_category')}}</th>
                                <th class="border-0 text-center">{{translate('messages.sub_category')}}</th>
                                <th class="border-0 text-center">{{translate('messages.status')}}</th>
                                <th class="border-0 text-center">{{translate('messages.featured')}}</th>
                                <th class="border-0 text-center">{{translate('messages.priority')}}</th>
                                <th class="border-0 text-center">{{translate('messages.action')}}</th>
                            </tr>
                        </thead>

                        <tbody id="table-div">
                        @foreach($categories as $key=>$category)
                            <tr>
                                <td>{{$key+$categories->firstItem()}}</td>
                                <td>{{$category->id}}</td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{ $category?->parent?->name ? Str::limit($category->parent['name'],20,'...') : translate('Invalid_Category') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="d-block font-size-sm text-body">
                                        {{Str::limit($category?->name,20,'...')}}
                                    </span>
                                </td>
                                <td>
                                    <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$category->id}}">
                                    <input type="checkbox" data-url="{{route('admin.category.status',[$category['id'],$category->status?0:1])}}" class="toggle-switch-input redirect-url" id="stocksCheckbox{{$category->id}}" {{$category->status?'checked':''}}>
                                        <span class="toggle-switch-label mx-auto">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td>
                                        <label class="toggle-switch toggle-switch-sm"
                                            for="featuredCheckbox{{ $category->id }}">
                                            <input type="checkbox" data-id="featuredCheckbox{{ $category->id }}"
                                                data-type="status"
                                                data-image-on="{{ asset('/public/assets/admin/img/status-ons.png') }}"
                                                data-image-off="{{ asset('/public/assets/admin/img/off-danger.png') }}"
                                                data-title-on="{{ translate('Do you want to Featured this sub category ?') }}"
                                                data-title-off="{{ translate('Don’t you want to Featured this sub category?') }}"
                                                data-text-on="<p>{{ translate('If you turn on this sub category as a featured category it will show in customer app landing page.') }}"
                                                data-text-off="<p>{{ translate('If you turn off this sub category from featured category it will not show in customer app landing page.') }}</p>"
                                                class="toggle-switch-input dynamic-checkbox"
                                                id="featuredCheckbox{{ $category->id }}"
                                                {{ $category->featured ? 'checked' : '' }}>
                                            <span class="toggle-switch-label mx-auto">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>

                                        <form
                                            action="{{ route('admin.category.featured', [$category['id'], $category->featured ? 0 : 1]) }}"
                                            method="get" id="featuredCheckbox{{ $category->id }}_form">
                                        </form>
                                    </td>
                                <td>
                                    <form action="{{route('admin.category.priority',$category->id)}}" class="priority-form">
                                        <select name="priority" id="priority" class="form-control priority-select form--control-select mx-auto {{$category->priority == 0 ? 'text-title':''}} {{$category->priority == 1 ? 'text-info':''}} {{$category->priority == 2 ? 'text-success':''}}">
                                            <option value="0" {{$category->priority == 0?'selected':''}}>{{translate('messages.normal')}}</option>
                                            <option value="1" {{$category->priority == 1?'selected':''}}>{{translate('messages.medium')}}</option>
                                            <option value="2" {{$category->priority == 2?'selected':''}}>{{translate('messages.high')}}</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                             <a class="btn action-btn btn-outline-theme-dark offcanvas-trigger data-info-show" href="javascript:void(0)"
                                                data-id="{{ $category['id'] }}"
                                                data-url="{{ route('admin.category.edit', [$category['id']]) }}"

                                            data-target="#offcanvas__categoryBtn">
                                                <i class="tio-edit"></i>
                                            </a>
                                        <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                           data-id="category-{{$category['id']}}" data-message="{{ translate('Want to delete this category') }}" title="{{translate('messages.delete_category')}}"><i class="tio-delete-outlined"></i>
                                        </a>
                                        <form action="{{route('admin.category.delete',[$category['id']])}}" method="post" id="category-{{$category['id']}}">
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
            @if(count($categories) !== 0)
            <hr>
            @endif
            <div class="page-area">
                {!! $categories->appends(request()->query())->links() !!}
            </div>
            @if(count($categories) === 0)
            <div class="empty--data">
                <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                <h5>
                    {{translate('no_data_found')}}
                </h5>
            </div>
            @endif
        </div>
    </div>
        <div id="offcanvas__categoryBtn" class="custom-offcanvas d-flex flex-column justify-content-between">
         <div id="data-view" class="h-100">
        </div>
    </div>
    <div id="offcanvasOverlay" class="offcanvas-overlay"></div>



@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin')}}/js/view-pages/sub-category-index.js"></script>
@endpush
