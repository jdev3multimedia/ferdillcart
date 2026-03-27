@extends('layouts.vendor.app')
@section('title',translate('messages.Employee List'))
@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h1 class="page-header-title mb-2">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/role.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.employee_list')}}
                    <span class="badge badge-soft-dark ml-2" id="itemCount">{{$em->total()}}</span>
                </span>

            </h1>
            <a href="{{route('vendor.employee.add-new')}}" class="btn btn--primary mb-2">
                <i class="tio-add-circle"></i>
                <span class="text">{{translate('messages.add_new_employee')}}</span>
            </a>
        </div>
    </div>
    <!-- Page Heading -->

    <div class="card">
        <div class="card-header py-2 justify-content-end border-0">
            <div class="search--button-wrapper justify-content-end">
                <form  class="search-form">

                    <!-- Search -->
                    <div class="input-group input--group">
                        <input  value="{{  request()?->search ?? null }}"  type="search" name="search" class="form-control" placeholder="{{ translate('messages.Ex:') }} {{translate('Search by name or email..')}}" aria-label="Search">
                        <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                    </div>
                    <!-- End Search -->
                </form>
                <!-- Unfold -->
                <div class="hs-unfold mr-2">
                    <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle h--40px" href="javascript:;"
                        data-hs-unfold-options='{
                            "target": "#usersExportDropdown",
                            "type": "css-animation"
                        }'>
                        <i class="tio-download-to mr-1"></i> {{translate('messages.export')}}
                    </a>

                    <div id="usersExportDropdown"
                            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">

                        <span
                            class="dropdown-header">{{translate('messages.download_options')}}</span>
                        <a id="export-excel" class="dropdown-item" href="{{route('vendor.employee.export-employee', ['type'=>'excel',request()->getQueryString()])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{asset('public/assets/admin/svg/components/excel.svg')}}"
                                    alt="Image Description">
                            {{translate('messages.excel')}}
                        </a>
                        <a id="export-csv" class="dropdown-item" href="{{route('vendor.employee.export-employee', ['type'=>'csv',request()->getQueryString()])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{asset('public/assets/admin/svg/components/placeholder-csv-format.svg')}}"
                                    alt="Image Description">
                            .{{translate('messages.csv')}}
                        </a>

                    </div>
                </div>
                <!-- End Unfold -->
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="datatable"
                        class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                        data-hs-datatables-options='{
                            "order": [],
                            "orderCellsTop": true,
                            "paging":false
                        }'>
                    <thead class="thead-light">
                    <tr>
                        <th class="border-0">{{translate('messages.#')}}</th>
                        <th class="border-0">{{translate('messages.name')}}</th>
                        <th class="border-0">{{translate('messages.email')}}</th>
                        <th class="border-0">{{translate('messages.phone')}}</th>
                        <th class="border-0">{{translate('messages.Role')}}</th>
                        <th class="border-0 text-center">{{translate('messages.action')}}</th>
                    </tr>
                    </thead>
                    <tbody id="set-rows">
                    @foreach($em as $k=>$e)
                        <tr>
                            <th scope="row">{{ $k+$em->firstItem() }}</th>
                            <td class="text-capitalize text-break">{{ $e['f_name']}} {{$e['l_name'] }}</td>
                            <td>{{ $e['email'] }}</td>
                            <td>{{ $e['phone'] }}</td>
                            <td>{{ $e->role?$e->role['name']:translate('messages.role_deleted') }}</td>
                            <td>
                                @if (auth('vendor_employee')->id()  != $e['id'])
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary"
                                            href="{{route('vendor.employee.edit',[$e['id']])}}" title="{{translate('messages.edit_Employee')}}"><i class="tio-edit"></i>
                                        </a>
                                        <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                           data-id="employee-{{$e['id']}}"
                                           data-message="{{translate('messages.Want_to_delete_this_role')}}"
                                            title="{{translate('messages.delete_Employee')}}"><i class="tio-delete-outlined"></i>
                                        </a>
                                    </div>
                                    <form action="{{route('vendor.employee.delete',[$e['id']])}}"
                                            method="post" id="employee-{{$e['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                @else
                                    <div class="btn--container justify-content-center">
                                    {{ translate('N/A') }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if(count($em) !== 0)
        <div class="card-footer">
            <div class="page-area">
                <table>
                    <tfoot>
                    {!! $em->links() !!}
                    </tfoot>
                </table>
            </div>
        </div>
        @endif
        @if(count($em) === 0)
        <div class="empty--data">
            <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
            <h5>
                {{translate('no_data_found')}}
            </h5>
        </div>
        @endif
    </div>
</div>
@endsection

