@extends('layouts.admin.app')

@section('title',translate('messages.language'))



@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header pb-0 mb-20">
            <h1 class="font-bold mb-0">{{ translate('messages.Language') }}</h1>
        </div>
        <!-- End Page Header -->

        <div class="card card-body mb-20">
            <div class="mb-20">
                <h3 class="mb-1">
                    {{ translate('messages.Add New Language') }}
                </h3>
                <p class="fs-12 mb-0">
                    {{ translate('messages.Setup new languages in your system, Website & apps to make order from versatile customers.') }}
                </p>
            </div>
            <form action="{{route('admin.business-settings.language.add-new')}}" method="post"
                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                @csrf
                <div class="__bg-F8F9FC-card mb-20">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="country-code"
                                        class="input-label">{{translate('messages.language')}}</label>
                                <select id="country-code" name="code" class="form-control custom-select js-select2-custom">
                                    @foreach(\App\CentralLogics\Helpers::getLanguages() as $key => $value)
                                                <option value="{{$key}}">{{$value}}</option>
                                            @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="direction" class="input-label">{{translate('messages.direction')}}</label>
                                <div class="resturant-type-group bg-white border">
                                    <label class="form-check form--check mr-2 mr-md-4">
                                        <input class="form-check-input" type="radio" value="ltr" name="direction" checked>
                                        <span class="form-check-label">
                                                {{translate('messages.Left to Right')}}
                                        </span>
                                    </label>
                                    <label class="form-check form--check mr-2 mr-md-4">
                                        <input class="form-check-input" type="radio" value="rtl" name="direction">
                                        <span class="form-check-label">
                                            {{translate('messages.Right to Left')}}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn--container justify-content-end">
                    <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                    <button type="submit" class="btn btn--primary">{{translate('messages.Submit')}}</button>
                </div>
            </form>
        </div>

        <div class="card card-body">
            <div class="d-flex gap-3 flex-wrap justify-content-between align-items-center mb-20">
                <h4 class="mb-0">{{ translate('messages.Language_List') }}</h4>
            </div>
            <div class="table-responsive datatable-custom" id="table-div">
                <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                    data-hs-datatables-options='{
                        "columnDefs": [{
                            "targets": [],
                            "width": "5%",
                            "orderable": false
                        }],
                        "order": [],
                        "info": {
                        "totalQty": "#datatableWithPaginationInfoTotalQty"
                        },

                        "entries": "#datatableEntries",

                        "isResponsive": false,
                        "isShowPaging": false,
                        "paging":false
                    }'>
                    <thead class="thead-light">
                    <tr>
                        <th>{{ translate('SL')}}</th>
                        <th>{{translate('Id')}}</th>
                        <th>{{translate('Language')}}</th>
                        <th>{{translate('Code')}}</th>
                        <th class="text-center">{{translate('Status')}}</th>
                        <th class="text-center">{{translate('Action')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($language=App\Models\BusinessSetting::where('key','system_language')->first())
                    @if($language)
                    @foreach(json_decode($language['value'],true) as $key =>$data)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>#{{$data['id']}}</td>
                            <td>
                                <span>{{ \App\CentralLogics\Helpers::get_language_name($data['code']) }}</span>
                                @if ($data['default'])
                                <span class="text-info bg-info bg-opacity-10 rounded px-2 py-1 fs-12 font-medium">
                                    {{ translate('messages.Default') }}
                                </span>
                                @endif
                            </td>
                            <td>{{$data['code']}}</td>
                            <td>
                                @if ($data['default'])
                                <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$data['id']}}">
                                    <input type="checkbox"  class="toggle-switch-input update-lang-status" id="stocksCheckbox{{$data['id']}}" {{$data['status']==1?'checked':''}} disabled>
                                    <span class="toggle-switch-label mx-auto">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                                @else
                                <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$data['id']}}">
                                    <input type="checkbox"
                                            data-url="{{route('admin.business-settings.language.update-status')}}"
                                            data-id="{{$data['code']}}"
                                            class="toggle-switch-input status-update" id="stocksCheckbox{{$data['id']}}" {{$data['status']==1?'checked':''}}>
                                    <span class="toggle-switch-label mx-auto">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-3 justify-content-center">
                                    <a class="btn btn-sm btn--primary btn-outline-primary d-flex gap-2 align-items-center px-3 py-2 {{( ($key == 0 ||  $key == 1 ) && env('APP_MODE') == 'demo') ? 'call-demo-lang' : ''}}"
                                        data-key="{{ $key }}"
                                        data-env-mode="{{ env('APP_MODE') }}"
                                        href="{{( ($key == 0 ||  $key == 1 ) && env('APP_MODE') == 'demo') ? 'javascript:' :route('admin.business-settings.language.translate',[$data['code']]) }}">
                                        <img width="14" height="14" class="svg" src="{{asset('public/assets/admin/img/svg/language-exchange.svg')}}" alt="public">
                                        <span class="fs-12">{{ translate('messages.View') }}</span>

                                    </a>
                                    @if ($data['code']=='en' && $data['default'])
                                    @else
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm btn--primary btn-outline-primary action-btn h-100" data-toggle="dropdown" aria-expanded="false">
                                                <i class="tio-more-vertical fs-24"></i>
                                            </button>
                                            <ul class="dropdown-menu w--180px" dir="ltr">
                                                @if ($data['default'])
                                                @else
                                                <a href="{{route('admin.business-settings.language.update-default-status', ['code'=>$data['code']])}}" class="dropdown-item d-flex gap-2 align-items-center cursor-pointer">
                                                    <i class="tio-checkmark-circle-outlined"></i>
                                                    {{ translate('messages.Mark As Default') }}
                                                </a>    
                                                @endif
                                                @if ($data['code']=='en')
                                                @else
                                                <a class="dropdown-item d-flex gap-2 align-items-center cursor-pointer call-demo-lang offcanvas-trigger"
                                                    data-key="{{ $key }}"
                                                    data-env-mode="{{ env('APP_MODE') }}"
                                                    data-target="{{ ( ($key == 0 ||  $key == 1 ) && env('APP_MODE') == 'demo') ? '' :'#lang-offcanvas-update-'.$data['code'] }}">
                                                    <i class="tio-edit"></i>
                                                    {{ translate('messages.Edit') }}
                                                </a>
                                                @endif
                                                @if ($data['code']=='en')
                                                @else
                                                    <a class="dropdown-item d-flex gap-2 align-items-center cursor-pointer call-demo-lang {{( ($key == 0 ||  $key == 1 ) && env('APP_MODE') == 'demo') ? '' : 'delete'}}"
                                                        data-key="{{ $key }}"
                                                        data-env-mode="{{ env('APP_MODE') }}"
                                                        id="{{( ($key == 0 ||  $key == 1 ) && env('APP_MODE') == 'demo')  ? 'javascript:' :route('admin.business-settings.language.delete',[$data['code']])}}">
                                                        <i class="tio-delete-outlined"></i>
                                                        {{ translate('messages.Delete') }}
                                                    </a>

                                                @endif
                                            </ul>
                                        </div>
                                    @endif
                                    
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($language)
        @foreach(json_decode($language['value'],true) as $key =>$data)
        <div id="lang-offcanvas-update-{{$data['code']}}" class="custom-offcanvas d-flex flex-column justify-content-between">
            <form action="{{route('admin.business-settings.language.update')}}" method="post">
                @csrf
                <input type="hidden" name="code" value="{{$data['code']}}">
                <input type="hidden" name="old_code" value="{{$data['code']}}">

                <div>
                    <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                        <div class="py-1">
                            <h3 class="mb-0">{{ translate('messages.Edit_Language') }}</h3>
                        </div>
                        <button type="button" class="btn-close w-25px h-25px border bg-white rounded-circle d-center text-dark offcanvas-close fz-15px p-0" aria-label="Close">
                            &times;
                        </button>
                    </div>
                    <div class="custom-offcanvas-body custom-offcanvas-body-100  p-20">
                        <div class="mb-9">
                            <div class="p-12 p-sm-20 bg-light rounded mb-3">
                                <div class="form-group">
                                    <label for="" class="input-label">
                                        {{translate('Language')}}
                                    </label>
                                    <input readonly type="text" class="form-control" placeholder="{{translate('Language_Name')}}" value="{{\App\CentralLogics\Helpers::get_language_name($data['code'])}}">
                                </div>
                                <div class="form-group">
                                    <label for="direction-update" class="input-label">{{translate('Direction')}}</label>
                                    <div class="resturant-type-group bg-white border">
                                        <label class="form-check form--check mr-2 mr-md-4">
                                            <input class="form-check-input" type="radio" value="ltr" name="direction"
                                            {{isset($data['direction'])?$data['direction']=='ltr'?'checked':'':''}} >
                                            <span class="form-check-label">
                                                 {{translate('LTR')}}
                                            </span>
                                        </label>
                                        <label class="form-check form--check mr-2 mr-md-4">
                                            <input class="form-check-input" type="radio" value="rtl" name="direction"
                                            {{isset($data['direction'])?$data['direction']=='rtl'?'checked':'':''}}>
                                            <span class="form-check-label">
                                                {{translate('RTL')}}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="offcanvas-footer d-flex gap-3 justify-content-center align-items-center bg-white bottom-0 mt-auto p-3">
                        <button type="reset" class="btn btn--reset w-100">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn--primary w-100">
                            {{translate('update')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endforeach
        <div id="offcanvasOverlay" class="offcanvas-overlay"></div>
    @endif
    <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-6">
                <button type="button" class="close position-absolute top-0 right-0 m-3" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="mb-3">
                    <img src="{{asset('public/assets/admin/img/modal/delete.png')}}" alt="delete" class="w-12">
                </div>
                <h4 class="modal-title mb-2">{{translate('Want to delete this Language')}}?</h4>
                <p class="mb-4">{{translate('Deleting a language will remove all associated content. This action cannot be undone.')}}</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="" id="delete-link" class="btn btn-danger" style="background-color: #FF4040; border-color: #FF4040;">{{translate('Yes, Delete')}}</a>
                    <button type="button" class="btn btn-secondary" style="background-color: #E8EAED; border-color: #E8EAED; color: #000;" data-dismiss="modal">{{translate('No, Cancel')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <script>
        "use strict"
        $(".delete").click(function (e) {
            e.preventDefault();
            let link = $(this).attr("id");
            $('#delete-modal').modal('show');
            $('#delete-link').attr('href', link);
        });

        $(".update-lang-status").click(function (e) {
            e.preventDefault();
            toastr.warning('{{translate('default language can not be updated! to update change the default language first!')}}');
        });

        $(".call-demo-lang").click(function (e) {
            e.preventDefault();
            let key = $(this).data('key');
            let mode = $(this).data('env-mode');

            if(  (key === 0 ||  key === 1 ) &&  mode === 'demo' ){
                toastr.info('{{ translate('Update option is disabled for demo!') }}', {
                    CloseButton: true,
                    ProgressBar: true
                });
            }
        });

        $(".status-update").click(function () {
            $.get({
                url: $(this).data('url'),
                data: {
                    code: $(this).data('id'),
                },

                success: function () {
                    toastr.success('{{translate('status_updated_successfully')}}');
                    setTimeout(function () {
                        window.location.href =
                            '{{ route('admin.business-settings.language.index') }}';
                    }, 1200);
                }
            });
        });

        $(".update-default").click(function () {
            window.location.href = $(this).data('url');
        });
    </script>
@endpush
