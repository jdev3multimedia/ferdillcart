@extends('layouts.admin.app')

@section('title', translate('messages.add_new_addon'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/addon.png') }}" class="w--20" alt="">
                </span>
                <span>
                    {{ translate('messages.add_new_addon') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{ isset($addon) ? route('admin.addon.update', [$addon['id']]) : route('admin.addon.store') }}"
                    method="post">
                    @csrf
                    @if ($language)
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a class="nav-link lang_link active offcanvas-close" href="#"
                                    id="default-link">{{ translate('messages.default') }}</a>
                            </li>
                            @foreach ($language as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link offcanvas-close" href="#"
                                        id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="row">
                        <div class="col-sm-6 col-lg-4">
                            @if ($language)
                                <div class="form-group lang_form" id="default-form">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{ translate('messages.name') }}
                                        ({{ translate('messages.default') }})</label>
                                    <input type="text" name="name[]" class="form-control"
                                        placeholder="{{ translate('messages.new_addon') }}" maxlength="191">
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                                @foreach ($language as $lang)
                                    <div class="form-group d-none lang_form" id="{{ $lang }}-form">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.name') }}
                                            ({{ strtoupper($lang) }})
                                        </label>
                                        <input type="text" name="name[]" class="form-control"
                                            placeholder="{{ translate('messages.new_addon') }}" maxlength="191">
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{ $lang }}">
                                @endforeach
                            @else
                                <div class="form-group">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{ translate('messages.name') }}</label>
                                    <input type="text" name="name" class="form-control"
                                        placeholder="{{ translate('messages.new_addon') }}" value="{{ old('name') }}"
                                        maxlength="191">
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                            @endif
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label"
                                    for="exampleFormControlSelect1">{{ translate('messages.store') }}<span
                                        class="input-label-secondary"></span></label>
                                <select name="store_id" id="store_id" class="js-data-example-ajax form-control"
                                    data-placeholder="{{ translate('messages.select_store') }}">

                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label"
                                    for="exampleFormControlInput1">{{ translate('messages.price') }}</label>
                                <input type="number" min="0" max="999999999999.99" name="price" step="0.01"
                                    value="{{ old('price') }}" class="form-control" placeholder="100" required>
                            </div>
                        </div>


                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <span class="mb-2 d-block title-clr fw-normal">{{ translate('Category') }}</span>
                                <select name="category_id" required class="form-control js-select2-custom"
                                    placeholder="Select Category">
                                    <option selected disabled value=""> {{ translate('messages.select_category') }}</option>
                                    @foreach ($addonCategories as $addonCategory)
                                        <option value="{{ $addonCategory->id }}"> {{ $addonCategory->name }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>


                        @if ($productWiseTax)
                            <div class="col-sm-6 col-lg-4">
                                <div class="form-group">
                                    <span
                                        class="mb-2 d-block title-clr fw-normal">{{ translate('Select Tax Rate') }}</span>
                                    <select name="tax_ids[]" required id="tax__rate" class="form-control js-select2-custom"
                                        multiple="multiple" placeholder="Type & Select Tax Rate">
                                        @foreach ($taxVats as $taxVat)
                                            <option value="{{ $taxVat->id }}"> {{ $taxVat->name }}
                                                ({{ $taxVat->tax_rate }}%)
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>

                        @endif
                    </div>


                    <div class="btn--container justify-content-end">
                        <button type="reset" id="reset_btn"
                            class="btn btn--reset">{{ translate('messages.reset') }}</button>
                        <button type="submit"
                            class="btn btn--primary">{{ isset($addon) ? translate('messages.update') : translate('messages.add') }}</button>
                    </div>

                </form>
            </div>
        </div>

        <div class="card mt-1">
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper justify-content-end">
                    <h5 class="card-title"> {{ translate('messages.addon_list') }}<span
                            class="badge badge-soft-dark ml-2" id="itemCount">{{ $addons->total() }}</span>
                    </h5>
                    <div class="min--220">
                        <select name="store_id" id="store" data-url="{{ route('admin.addon.add-new') }}"
                            data-placeholder="{{ translate('messages.select_store') }}"
                            class="js-data-example-ajax form-control store-filter" title="Select Restaurant">
                            @if (isset($store))
                                <option value="{{ $store->id }}" selected>{{ $store->name }}</option>
                            @else
                                <option value="all" selected>{{ translate('messages.all_stores') }}</option>
                            @endif
                        </select>
                    </div>
                    <form class="search-form">
                        <!-- Search -->
                        <div class="input-group input--group">
                            <input type="search" name="search" value="{{ request()->search ?? null }}"
                                class="form-control min-height-45"
                                placeholder="{{ translate('messages.ex_:_addons_name') }}" aria-label="Search addons">
                            <button type="submit" class="btn btn--secondary min-height-45"><i class="tio-search"></i>
                            </button>
                        </div>
                        <!-- End Search -->
                    </form>
                    <div class="hs-unfold">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40"
                            href="javascript:;"
                            data-hs-unfold-options='{
                                    "target": "#usersExportDropdown",
                                    "type": "css-animation"
                                }'>
                            <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                        </a>
                    </div>
                    <div id="usersExportDropdown"
                        class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                        <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                        <a id="export-excel" class="dropdown-item"
                            href="
                            {{ route('admin.addon.export', ['type' => 'excel', request()->getQueryString()]) }}
                            ">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                alt="Image Description">
                            {{ translate('messages.excel') }}
                        </a>
                        <a id="export-csv" class="dropdown-item"
                            href="
                        {{ route('admin.addon.export', ['type' => 'csv', request()->getQueryString()]) }}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                alt="Image Description">
                            .{{ translate('messages.csv') }}
                        </a>
                    </div>


                    <!-- End Unfold -->
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive datatable-custom">
                <table id="datatable"
                    class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                    data-hs-datatables-options='{
                        "search": "#datatableSearch",
                        "entries": "#datatableEntries",
                        "isResponsive": false,
                        "isShowPaging": false,
                        "paging":false
                            }'>
                    <thead class="thead-light">
                        <tr>
                            <th>{{ translate('sl') }}</th>
                            <th>{{ translate('messages.name') }}</th>
                            <th>{{ translate('messages.price') }}</th>
                            <th>{{ translate('messages.store') }}</th>
                            @if ($productWiseTax)
                            <th>{{ translate('messages.Vat/Tax') }}</th>
                            @endif

                            <th class="text-center">{{ translate('messages.status') }}</th>
                            <th class="text-center">{{ translate('messages.action') }}</th>
                        </tr>
                    </thead>

                    <tbody id="set-rows">
                        @foreach ($addons as $key => $addon)
                            <tr>
                                <td>{{ $key + $addons->firstItem() }}</td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{ Str::limit($addon['name'], 20, '...') }}
                                    </span>
                                </td>
                                <td>{{ \App\CentralLogics\Helpers::format_currency($addon['price']) }}</td>
                                <td>{{ Str::limit($addon->store ? $addon->store->name : translate('messages.store_deleted'), 25, '...') }}
                                </td>


                                @if ($productWiseTax)
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        @forelse ($addon?->taxVats?->pluck('tax.name', 'tax.tax_rate')->toArray() as $key => $item)
                                            <span> {{ $item }} : <span class="font-bold">
                                                    ({{ $key }}%)
                                                </span> </span>
                                            <br>
                                        @empty
                                            <span> {{ translate('messages.no_tax') }} </span>
                                        @endforelse
                                    </span>
                                </td>
                                @endif
                                <td>
                                    <label class="toggle-switch toggle-switch-sm" for="stausCheckbox{{ $addon->id }}">
                                        <input type="checkbox"
                                            data-url="{{ route('admin.addon.status', [$addon['id'], $addon->status ? 0 : 1]) }}"
                                            class="toggle-switch-input redirect-url"
                                            id="stausCheckbox{{ $addon->id }}" {{ $addon->status ? 'checked' : '' }}>
                                        <span class="toggle-switch-label mx-auto">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn btn-sm text-end action-btn info--outline text--info info-hover offcanvas-trigger get_data data-info-show" data-target="#offcanvas__customBtn3" data-id="{{ $addon['id'] }}"  data-url="{{route('admin.addon.edit',[$addon['id']])}}" href="javascript:"
                                            title="{{ translate('messages.edit_addon') }}"><i class="tio-edit"></i></a>
                                        <a class="btn action-btn btn--danger btn-outline-danger form-alert"
                                            data-id="addon-{{ $addon['id'] }}"
                                            data-message="{{ translate('Want to delete this addon ?') }}"
                                            href="javascript:" title="{{ translate('messages.delete_addon') }}"><i
                                                class="tio-delete-outlined"></i></a>
                                        <form action="{{ route('admin.addon.delete', [$addon['id']]) }}" method="post"
                                            id="addon-{{ $addon['id'] }}">
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
        @if (count($addons) !== 0)
            <hr>
        @endif
        <div class="page-area">
            {!! $addons->links() !!}
        </div>
        @if (count($addons) === 0)
            <div class="empty--data">
                <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
                <h5>
                    {{ translate('no_data_found') }}
                </h5>
            </div>
        @endif
    </div>
    </div>





    <div id="offcanvas__customBtn3" class="custom-offcanvas d-flex flex-column justify-content-between">
        <div id="data-view" class="h-100">
        </div>
    </div>


@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin') }}/js/view-pages/addon-index.js"></script>
    <script>
        "use strict";
function getStoreSelect2Config(showAll) {
    return {
        ajax: {
            url: '{{ url('/') }}/admin/store/get-stores',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                const data = {
                    q: params.term,
                    module_type: 'food',
                    module_id: {{ Config::get('module.current_module_id') }},
                    page: params.page || 1
                };

                if (showAll) {
                    data.all = true;
                }

                return data;
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        placeholder: 'Select a store',
        allowClear: true
    };
}

        $('#store').select2(getStoreSelect2Config(true));
        $('#store_id').select2(getStoreSelect2Config(false));

            $(document).on('click', '.data-info-show', function() {
            let id = $(this).data('id');
            let url = $(this).data('url');
            fetch_data(id, url)
        })

        function fetch_data(id, url) {
            $.ajax({
                url: url,
                type: "get",
                beforeSend: function() {
                    $('#data-view').empty();
                    $('#loading').show()
                },
                success: function(data) {
                    $("#data-view").append(data.view);
                    initLangTabs();
                    initSelect2Dropdowns();
                    $('#store_id1').select2(getStoreSelect2Config(false));
                },
                complete: function() {
                    $('#loading').hide()
                }
            })
        }


        function initLangTabs() {
            const langLinks = document.querySelectorAll(".lang_link1");
            langLinks.forEach(function(langLink) {
                langLink.addEventListener("click", function(e) {
                    e.preventDefault();
                    langLinks.forEach(function(link) {
                        link.classList.remove("active");
                    });
                    this.classList.add("active");
                    document.querySelectorAll(".lang_form1").forEach(function(form) {
                        form.classList.add("d-none");
                    });
                    let form_id = this.id;
                    let lang = form_id.substring(0, form_id.length - 5);
                    $("#" + lang + "-form1").removeClass("d-none");
                    if (lang === "default") {
                        $(".default-form1").removeClass("d-none");
                    }
                });
            });
        }

        function initSelect2Dropdowns() {
            $('.js-select2-custom1').select2({
                placeholder: 'Select tax rate',
                allowClear: true
            });
             $('.offcanvas-close, #offcanvasOverlay').on('click', function () {
        $('.custom-offcanvas').removeClass('open');
        $('#offcanvasOverlay').removeClass('show');
            });
        }
    </script>
@endpush
