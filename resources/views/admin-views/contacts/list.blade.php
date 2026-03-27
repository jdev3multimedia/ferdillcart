@extends('layouts.admin.app')

@section('title',translate('messages.Contact Messages'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
            <!-- Page Title -->
            <div class="mb-3">
                <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                    <img width="20" src="{{asset('/public/assets/back-end/img/message.png')}}" alt="">
                    {{translate('messages.all_message_lists')}}
                </h2>
            </div>
            <!-- End Page Title -->
        <!-- End Page Header -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper">
                            <h5 class="card-title">
                                {{translate('messages.message_lists')}} <span class="badge badge-soft-dark ml-2" id="itemCount">{{$contacts->total()}}</span>
                            </h5>
                            <form class="search-form">
                                <div class="input-group input--group">
                                    <input  type="search" name="search" class="form-control"
                                    placeholder="{{translate('ex_: search_by_name,_email,_or_subject')}}" aria-label="{{translate('messages.search')}}" value="{{request()?->search}}" >
                                    <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                                </div>
                            </form>
                           @if(request()->get('search'))
                                <button type="reset" class="btn btn--primary ml-2 location-reload-to-base" data-url="{{url()->full()}}">{{translate('messages.reset')}}</button>
                                @endif


                            <!-- Unfold -->
                            <div class="hs-unfold mr-2">
                                <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40"
                                   href="javascript:"
                                   data-hs-unfold-options='{
                                                        "target": "#usersExportDropdown",
                                                        "type": "css-animation"
                                                    }'>
                                    <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                                </a>

                                <div id="usersExportDropdown"
                                     class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                    <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                                    <a id="export-excel" class="dropdown-item"
                                       href="{{route('admin.users.contact.exportList', ['type'=>'excel',request()->getQueryString()])}}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                             src="{{ asset('public/assets/admin/svg/components/excel.svg') }}"
                                             alt="Image Description">
                                        {{ translate('messages.excel') }}
                                    </a>
                                    <a id="export-csv" class="dropdown-item"
                                       href="{{route('admin.users.contact.exportList', ['type'=>'csv',request()->getQueryString()])}}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                             src="{{ asset('public/assets/admin/svg/components/placeholder-csv-format.svg') }}"
                                             alt="Image Description">
                                        .{{ translate('messages.csv') }}
                                    </a>
                                </div>
                            </div>
                            <!-- End Unfold -->


                        </div>


                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                               data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging":false
                               }'>
                            <thead class="thead-light">
                            <tr class="text-center">
                                <th class="border-0">{{translate('messages.sl')}}</th>
                                <th class="border-0">{{translate('messages.name')}}</th>
                                <th class="border-0">{{translate('messages.email')}}</th>
                                <th class="border-0">{{translate('messages.subject')}}</th>
                                <th class="border-0">{{translate('messages.Seen/Unseen')}}</th>
                                <th class="border-0">{{translate('messages.action')}}</th>
                            </tr>

                            </thead>

                            <tbody id="set-rows">
                            @foreach($contacts as $key=>$contact)
                                <tr>
                                    <td class="text-center">
                                        <span class="mr-3">
                                            {{$key+1}}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="font-size-sm text-body mr-3">
                                            {{Str::limit($contact['name'],20,'...')}}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="font-size-sm text-body mr-3">
                                            {{$contact['email']}}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="font-size-sm text-body mr-3 white--space-initial max-w-180px mx-auto">
                                            {{Str::limit($contact['subject'],40,'...')}}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="font-size-sm text-body mr-3">
                                            @if($contact->seen==1)
                                            <label class="badge badge-soft-success mb-0">{{translate('messages.Seen')}}</label>
                                        @else
                                            <label class="badge badge-soft-info mb-0">{{translate('messages.Not_Seen_Yet')}}</label>
                                        @endif
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn--primary btn-outline-primary" href="{{route('admin.users.contact.contact-view',[$contact['id']])}}" title="{{translate('messages.edit')}}"><i class="tio-invisible"></i>
                                            </a>
                                            <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:" data-id="contact-{{$contact['id']}}" data-message="{{ translate('messages.Want to delete this message?') }}" title="{{translate('messages.delete')}}"><i class="tio-delete-outlined"></i>
                                            </a>
                                            <form action="{{route('admin.users.contact.contact-delete',[$contact['id']])}}"
                                                    method="post" id="contact-{{$contact['id']}}">
                                                @csrf @method('delete')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(count($contacts) !== 0)
                    <hr>
                    @endif
                    <div class="page-area">
                        {!! $contacts->links() !!}
                    </div>
                    @if(count($contacts) === 0)
                    <div class="empty--data">
                        <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                        <h5>
                            {{translate('messages.no_data_found')}}
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
    <script src="{{asset('public/assets/admin')}}/js/view-pages/contact-index.js"></script>
@endpush
