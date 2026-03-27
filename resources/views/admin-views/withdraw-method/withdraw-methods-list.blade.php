@extends('layouts.admin.app')

@section('title', translate('messages.withdraw_method'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <div class="page-title-wrap d-flex justify-content-between flex-wrap align-items-center gap-3 mb-3">
                <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                    <img width="20" src="{{asset('/public/assets/admin/img/withdraw-icon.png')}}" alt="">
                    {{ translate('messages.withdraw_method_list')}}
                </h2>
            </div>
        </div>
        <!-- End Page Title -->

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="search--button-wrapper px-4 py-3 d-flex flex-wrap align-items-center justify-content-between">
                        <span class="fs-16 font-semibold text-title">
                            {{translate('Methods')}} <span class="badge badge-soft-dark rounded-circle ml-1">{{ $withdrawal_methods->total() }}</span>
                        </span>
                        <div class="d-flex align-items-center gap-2 flex-wrap">

                            <form class="search-form theme-style">
                            <div class="input-group input--group">
                                <input id="datatableSearch" name="search" type="search" class="form-control h--40px" placeholder="{{translate('Ex:_reference,_Name')}}" value="{{ request()?->search ?? null}}" aria-label="{{translate('messages.search_here')}}">
                                <button type="submit" class="btn bbtn btn--primary h--40px"><i class="tio-search"></i></button>
                            </div>
                        </form>

                        @if(request()->get('search'))
                            <button type="reset" class="btn btn--primary ml-2 location-reload-to-base" data-url="{{url()->full()}}">{{translate('messages.reset')}}</button>
                        @endif


                            <div class="">
                                <a href="{{route('admin.transactions.withdraw-method.create')}}" class="btn btn--primary fs-12 h--40px">
                                    <i class="tio-add"></i>
                                    {{ translate('messages.add_new_method')}}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="bg-table-head thead-50 text-capitalize">
                                <tr>
                                    <th class="fs-14 text-title font-semibold">{{ translate('messages.SL')}}</th>
                                    <th class="fs-14 text-title font-semibold">{{ translate('messages.method_name')}}</th>
                                    <th class="fs-14 text-title font-semibold">{{  translate('messages.method_fields') }}</th>
                                    <th class="fs-14 text-title font-semibold text-center">{{ translate('messages.active_status')}}</th>
                                    <th class="fs-14 text-title font-semibold text-center" >{{ translate('messages.default_method')}}</th>
                                    <th class="text-center fs-14 text-title font-semibold">{{ translate('messages.action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($withdrawal_methods as $key=>$withdrawal_method)
                                <tr>
                                    <td class="p-3 fs-14 text-title">{{$withdrawal_methods->firstitem()+$key}}</td>
                                    <td class="p-3 fs-14 text-title">{{$withdrawal_method['method_name']}}</td>
                                    {{--<td>
                                        <div class="max-text-2-line" style="--line-count: 4">
                                            @foreach($withdrawal_method['method_fields'] as $key=>$method_field)
                                                <b>{{ translate('messages.Name')}}:</b> {{ translate($method_field['input_name'])}} <br/>
                                                <b>{{ translate('messages.Type')}}:</b> {{ translate($method_field['input_type']) }} <br/>
                                                <b>{{ translate('messages.Placeholder')}}:</b> {{ $method_field['placeholder'] }} <br/>
                                                {{ $method_field['is_required'] ? translate('messages.Required') :  translate('messages.Optional') }}
                                                <br/>
                                                @break
                                            @endforeach
                                        </div>
                                        <a href="#" data-id="{{ $withdrawal_method->id }}" class="font-semibold d-flex gap-2 align-items-center text-capitalize mt-1 withdraw-info-show fs-12" >
                                            {{ translate('messages.see_all')}}
                                            <i class="tio-arrow-forward"></i>
                                        </a>
                                    </td>--}}
                                    <td class="p-3">
                                        <div class="d-flex gap-2 align-items-center  flex-wrap">
                                            @foreach($withdrawal_method['method_fields'] as $key=>$method_field)
                                            <div class="d-flex flex-wrap align-items-center __bg-FAFAFA py-1 px-2 rounded fs-12 gap-1">
                                                    <div class="d-flex align-items-center gap-1 text-title">
                                                        <b class="color-334257B2 font-regular">{{ translate('messages.Name')}}: </b> <span class="text--semititle">{{ translate($method_field['input_name'])}}</span>
                                                    </div>
                                                    <div class="line" style="width: 1px; height: 10px; background-color: #2223241A;"></div>
                                                    <div class="d-flex align-items-center gap-1 text-title">
                                                        <b class="color-334257B2 font-regular">{{ translate('messages.Type')}}:</b> <span class="text--semititle">{{ translate($method_field['input_type']) }}</span>
                                                    </div>
                                                    <div class="line" style="width: 1px; height: 10px; background-color: #2223241A;"></div>
                                                    <div class="d-flex align-items-center gap-1 text-title">
                                                        <b class="color-334257B2 font-regular">{{ translate('messages.Placeholder')}}:</b> <span class="text--semititle">{{ $method_field['placeholder'] }}</span>
                                                    </div>
                                                     <div class="line" style="width: 1px; height: 10px; background-color: #2223241A;"></div>
                                                    <div class="d-flex align-items-center gap-1 text-title">
                                                        <b class="color-334257B2 font-regular">{{ translate('Is_Required')}}:</b> <span class="text--semititle">{{ $method_field['is_required'] ? translate('Yes') :  translate('No') }}</span>
                                                    </div>

                                                    <br/>

                                                </div>
                                                @endforeach
                                            {{-- <a href="#" data-id="{{ $withdrawal_method->id }}" class="font-semibold d-flex gap-2 align-items-center text-capitalize mt-1 withdraw-info-show fs-12" >
                                                {{ translate('messages.see_all')}}
                                                <i class="tio-arrow-forward"></i>
                                            </a> --}}
                                        </div>
                                    </td>
                                    <td class="p-3 text-center">
                                        <label class="toggle-switch toggle-switch-sm">
                                            <input class="toggle-switch-input status featured-status"
                                                   data-id="{{$withdrawal_method->id}}"
                                                   type="checkbox" {{$withdrawal_method->is_active?'checked':''}}>
                                                   <span class="toggle-switch-label mx-auto">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                        </label>
                                    </td>
                                    <td class="p-3 text-center">
                                        <label class="toggle-switch mx-auto toggle-switch-sm">
                                            <input type="checkbox" class="default-method toggle-switch-input"
                                            id="{{$withdrawal_method->id}}" {{$withdrawal_method->is_default == 1?'checked':''}}>
                                                   <span class="toggle-switch-label mx-auto">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                        </label>
                                    </td>
                                    <td class="p-3">
                                        <div class="btn--container justify-content-center">
                                            <a href="{{route('admin.transactions.withdraw-method.edit',[$withdrawal_method->id])}}"
                                               class="btn action-btn btn-outline-theme-dark">
                                                <i class="tio-edit"></i>
                                            </a>

                                            @if(!$withdrawal_method->is_default)
                                                <a class="btn btn-sm btn--danger btn-outline-danger action-btn form-alert" href="javascript:"
                                                   title="{{ translate('messages.Delete')}}" data-id="delete-{{$withdrawal_method->id}}" data-message="{{ translate('Want to delete this item ?') }}">
                                                    <i class="tio-delete-outlined"></i>
                                                </a>
                                                <form action="{{route('admin.transactions.withdraw-method.delete',[$withdrawal_method->id])}}"
                                                      method="post" id="delete-{{$withdrawal_method->id}}">
                                                    @csrf @method('delete')
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if(count($withdrawal_methods)==0)
                            <div class="empty--data">
                                <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                        <h5>
                            {{translate('no_data_found')}}
                        </h5>
                            </div>
                       @endif
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-center justify-content-md-end">
                            <!-- Pagination -->
                            {{$withdrawal_methods->links()}}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
{{-- {{ dd(1) }} --}}


    <!-- Withdraw Method List Modal -->
    <div class="modal fade" id="withdrawMethodList" tabindex="-1" role="dialog" aria-labelledby="withdrawMethodListLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
             <div id="data-view"> </div>
            </div>
        </div>
    </div>


@endsection


@push('script_2')
  <script>
      "use strict";
      $(document).on('change', '.default-method', function () {
          let id = $(this).attr("id");
          let status = $(this).prop("checked") === true ? 1:0;

          $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
          $.ajax({
              url: "{{route('admin.transactions.withdraw-method.default-status-update')}}",
              method: 'POST',
              data: {
                  id: id,
                  status: status
              },
              success: function (data) {
                  if(data.success == true) {
                      toastr.success('{{ translate('messages.Default_Method_updated_successfully')}}');
                      setTimeout(function(){
                          location.reload();
                      }, 1000);
                  }
                  else if(data.success == false) {
                      toastr.error('{{ translate('messages.Default_Method_updated_failed.')}}');
                      setTimeout(function(){
                          location.reload();
                      }, 1000);
                  }
              }
          });
      });

      $('.featured-status').on('change', function () {
          let id = $(this).data('id');
          $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
          $.ajax({
              url: "{{route('admin.transactions.withdraw-method.status-update')}}",
              method: 'POST',
              data: {
                  id: id
              },
              success: function (data) {
                  toastr.success('{{ translate('messages.status_updated_successfully')}}');
              }
          });
      })


      function fetch_data(id) {
            $.ajax({
                url: "{{ route('admin.transactions.withdraw-method.getMethodInfo') }}" + '?id=' + id,
                type: "get",

                beforeSend: function () {
                    $('#data-view').empty();
                    $('#loading').show()
                },
                success: function(data) {
                    $("#withdrawMethodList").modal("show");
                    $("#data-view").append(data.view);
                },
                complete: function () {
                    $('#loading').hide()
                }
            })
        }



        $(document).on('click', '.withdraw-info-show', function () {
            let id = $(this).data('id');
            fetch_data(id)

        })


  </script>
@endpush
