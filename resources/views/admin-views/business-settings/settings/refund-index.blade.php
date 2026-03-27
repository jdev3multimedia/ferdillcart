@extends('layouts.admin.app')

@section('title', translate('refund_settings'))


@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title mr-3">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/business.png') }}" class="w--26" alt="">
                </span>
                <span>
                    {{ translate('messages.business_setup') }}
                </span>
            </h1>
            @include('admin-views.business-settings.partials.nav-menu')
        </div>
        <!-- End Page Header -->
    <div class="card mb-3" id="refund_mode_section">
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-xxl-9 col-lg-8 col-md-7 col-sm-6">
                        <div>
                            <h3 class="mb-1">
                                {{ translate('Refund Request Mode') }}
                            </h3>
                            <p class="mb-0 fs-12">
                                {{ translate('Customers can’t request a Refund if Admin doesn’t specify a cause for Refund. Admin MUST provide a Refund Reason.') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-lg-4 col-md-5 col-sm-6">
                        <div class="maintenance-mode-toggle-bar d-flex flex-wrap justify-content-between border rounded align-items-center py-2 px-3">
                            @php($config = $refund_active_status ?? null)
                            <h5 class="text-capitalize m-0 text-title font-weight-normal">
                                {{ translate('messages.Refund Request Mode') }}
                            </h5>
                            <label class="toggle-switch toggle-switch-sm">
                                <input type="checkbox" class="status toggle-switch-input refund-mode"
                                    {{ isset($config) && $config ? 'checked' : '' }}>
                                <span class="toggle-switch-label text mb-0">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    <div class="card" id="refund_reason_section">
            <div class="card-header">
                <div>
                    <h3 class="mb-1">
                        {{ translate('Add Refund Reason') }}
                    </h3>
                    <p class="mb-0 fs-12">
                        {{ translate('Users cannot cancel an order if the Admin does not specify a cause for cancellation even though ') }}
                    </p>
                </div>
            </div>
            <div class="card-body">
                <div class="report-card-inner mb-4 mw-100">
                    <div class="bg-light rounded p-xxl-20 p-2">
                        <form action="{{route('admin.refund.refund_reason')}}" method="post">
                            @csrf

                            @if($language)
                            <ul class="nav nav-tabs nav--tabs mt-0 mb-20 ">
                                <li class="nav-item">
                                    <a class="nav-link lang_link active"
                                    href="#"
                                    id="default-link">{{ translate('Default') }}</a>
                                </li>
                                @foreach ($language as $lang)
                                    <li class="nav-item">
                                        <a class="nav-link lang_link"
                                            href="#"
                                            id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            @endif
                            <div class="row align-items-end">
                                <div class="col-md-12 lang_form default-form">
                                    <label for="reason" class="form-label">{{translate('Refund Reason')}} ({{ translate('Default') }})
                                        <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write the related refund reasons that customers must select to request a refund. ') }}">
                                            <i class="tio-info text-muted"></i>
                                        </span>
                                          <span class="text-danger">*</span>
                                    </label>
                                    <textarea id="reason" type="text" class="form-control" rows="1" maxlength="150" name="reason[]"
                                                placeholder="{{ translate('Ex:_Item_is_Broken') }}"></textarea>
                                                <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/150</span>
                                                <input type="hidden" name="lang[]" value="default">
                                </div>
                                @if ($language)
                                @foreach ($language as $lang)
                                    <div class="col-md-12 d-none lang_form" id="{{$lang}}-form">
                                        <label for="reason{{$lang}}" class="form-label">{{translate('Refund Reason')}} ({{strtoupper($lang)}})

                                        </label>
                                        <textarea id="reason{{$lang}}" type="text" class="form-control" rows="1" maxlength="150" name="reason[]"
                                                placeholder="{{ translate('Ex:_Item_is_Broken') }}"></textarea>
                                                <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/150</span>
                                        <input type="hidden" name="lang[]" value="{{$lang}}">
                                    </div>
                                @endforeach
                                @endif

                                <div class="col-md-12">
                                    <div class="d-flex justify-content-end align-items-center gap-3 mt-20">
                                        <button type="resest" class="btn btn--reset h--45px min-w-120px">{{translate('messages.Reset')}}</button>
                                        <button type="submit" class="btn btn--primary m-0 h--45px min-w-120px">{{translate('messages.Save')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card border-0">
                    <div class="card-body mb-3">
                        <div class="d-flex gap-3 flex-wrap justify-content-between align-items-center mb-20">
                            <div class="">
                                <h4 class="mb-0">
                                    {{translate('Refund Reason List')}}
                                </h4>
                            </div>
                            <form class="search-form order-search-wrap min--260">
                                <!-- Search -->
                                <div class="input-group input--group">
                                    <input id="datatableSearch" name="search" value="{{ request()?->search ?? null }}"
                                        type="search" class="form-control h--40px"
                                        placeholder="{{ translate('ex_:search_here') }}"
                                        aria-label="{{ translate('messages.search_here') }}">
                                    <button type="submit" class="btn btn--secondary h--40px"><i
                                            class="tio-search"></i></button>
                                </div>
                                <!-- End Search -->
                            </form>
                        </div>
                        <!-- Table -->
                        <div class="card-body p-0">
                            <div class="table-responsive datatable-custom">
                                <table id="columnSearchDatatable"
                                    class="table table-borderless table-thead-bordered table-align-middle" data-hs-datatables-options='{
                                        "isResponsive": false,
                                        "isShowPaging": false,
                                        "paging":false,
                                    }'>
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="border-0">{{ translate('messages.SL') }}</th>
                                            <th class="border-0">{{translate('messages.Reason')}}</th>
                                            <th class="border-0">{{translate('messages.status')}}</th>
                                            <th class="border-0 text-center">{{translate('messages.action')}}</th>
                                        </tr>
                                    </thead>

                                    <tbody id="table-div">
                                    @foreach($reasons as $key=>$reason)
                                        <tr>
                                            <td class="text-dark">{{$key+$reasons->firstItem()}}</td>

                                            <td>
                                                <span class="d-block fs-14 min-w-176px line--limit-2 text-body text-dark" title="{{ $reason->reason }}">
                                                    {{Str::limit($reason->reason, 50,'...')}}
                                                </span>
                                            </td>
                                            <td>
                                                <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$reason->id}}">
                                                <input type="checkbox" data-url="{{route('admin.refund.reason_status',[$reason['id'],$reason->status?0:1])}}" class="toggle-switch-input redirect-url" id="stocksCheckbox{{$reason->id}}" {{$reason->status?'checked':''}}>
                                                    <span class="toggle-switch-label">
                                                        <span class="toggle-switch-indicator"></span>
                                                    </span>
                                                </label>
                                            </td>

                                            <td>
                                                <div class="btn--container justify-content-center">

                                                    <a class="btn btn-sm text-end action-btn info--outline text--info info-hover offcanvas-trigger data-info-show"
                                                        data-target="#offcanvas__customBtn3"
                                                        data-id="{{ $reason->id }}"
                                                        data-url="{{ route('admin.refund.reason-edit', [$reason->id]) }}"
                                                        href="javascript:"
                                                        title="{{ translate('messages.edit_refund_reason') }}"><i
                                                            class="tio-edit"></i></a>

                                                    <a class="btn btn-sm btn--danger btn-outline-danger action-btn form-alert" href="javascript:"
                                                    data-id="refund_reason-{{$reason['id']}}"
                                                    data-message="{{ translate('Want to delete this refund reason ?') }}"

                                                title="{{translate('messages.delete')}}">
                                                <i class="tio-delete-outlined"></i>
                                            </a>
                                                    <form action="{{route('admin.refund.reason_delete',[$reason['id']])}}"
                                                    method="post" id="refund_reason-{{$reason['id']}}">
                                                @csrf @method('delete')
                                            </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                 @if (count($reasons) === 0)
                                <div class="empty--data">
                                    <img src="{{ asset('/public/assets/admin/img/svg/no_record.svg') }}"
                                        alt="public">
                                    <p class="fs-12">
                                        {{ translate('No Refund Reason List') }}
                                    </p>
                                </div>
                            @endif

                            </div>
                            <div class="card-footer pt-0 border-0">
                                <div class="page-area px-4 pb-3">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <div>
                                            {!! $reasons->withQueryString()->links() !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                        <!-- End Table -->
                    </div>
                </div>
            </div>
        </div>

<div id="global_guideline_offcanvas"
    class="custom-offcanvas d-flex flex-column justify-content-between global_guideline_offcanvas">
    <div>
        <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
            <h3 class="mb-0">{{ translate('Refund Settings Guideline') }}</h3>
            <button type="button"
                class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary offcanvas-close fz-15px p-0"
                aria-label="Close">&times;</button>
        </div>

        <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
            <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed"
                    type="button" data-toggle="collapse" data-target="#refund_mode_guide" aria-expanded="true">
                    <div
                        class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                        <i class="tio-down-ui"></i>
                    </div>
                    <span class="font-semibold text-left fs-14 text-title">{{ translate('Refund Request Mode') }}</span>
                </button>
                <a href="#refund_mode_section"
                    class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
            </div>
            <div class="collapse mt-3 show" id="refund_mode_guide">
                <div class="card card-body">
                    <div class="">
                        <h5 class="mb-3">{{ translate('Refund Request Mode') }}</h5>
                        <p class="fs-12 mb-0">
                            {{ translate('This option enables customers to submit refund requests for their orders. When this option is turned OFF, customers will not be able to request a refund.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-3 px-3 bg-light rounded mb-3 mb-sm-20">
            <div class="d-flex gap-2 align-items-center justify-content-between overflow-hidden">
                <button class="btn-collapse d-flex gap-2 align-items-center bg-transparent border-0 p-0 collapsed"
                    type="button" data-toggle="collapse" data-target="#refund_reason_guide" aria-expanded="true">
                    <div
                        class="btn-collapse-icon w-35px h-35px bg-white d-flex align-items-center justify-content-center border icon-btn rounded-circle fs-12 lh-1">
                        <i class="tio-down-ui"></i>
                    </div>
                    <span class="font-semibold text-left fs-14 text-title">{{ translate('Refund Reason') }}</span>
                </button>
                <a href="#refund_reason_section"
                    class="text-info text-underline fs-12 text-nowrap offcanvas-close offcanvas-close-btn">{{ translate('messages.Let’s Setup') }}</a>
            </div>
            <div class="collapse mt-3" id="refund_reason_guide">
                <div class="card card-body">
                    <div class="">
                        <h5 class="mb-3">{{ translate('Refund Reason') }}</h5>
                        <p class="fs-12 mb-0">
                            {{ translate('This section allows the admin to manage refund cancellation reasons. Admin can create and configure cancellation reasons and control their active status. Customers will see these reasons as options when they request a refund.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <div id="offcanvasOverlay" class="offcanvas-overlay"></div>
    <div id="offcanvas__customBtn3" class="custom-offcanvas d-flex flex-column justify-content-between">
        <div id="data-view" class="h-100">
        </div>
    </div>

@endsection
@push('script_2')
    <script src="{{asset('public/assets/admin/js/view-pages/offcanvas-edit.js')}}"></script>
<script>

    $('.refund-mode').on('click', function(event){
        event.preventDefault();
        Swal.fire({
            title: '{{ translate('Are you sure?') }}' ,
            text: 'Be careful before you turn on/off Refund Request mode',
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#377dff',
            cancelButtonText: '{{translate('messages.no')}}',
            confirmButtonText: '{{translate('messages.yes')}}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $.get({
                    url: '{{ route('admin.refund.refund_mode') }}',
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('#loading').show();
                    },
                    success: function(data) {
                        toastr.success(data.message);
                    },
                    complete: function() {
                        location.reload();
                        $('#loading').hide();
                    },
                });
            }
        })

    });


    </script>

@endpush
