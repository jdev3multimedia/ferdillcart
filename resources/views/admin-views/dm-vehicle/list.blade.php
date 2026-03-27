@extends('layouts.admin.app')

@section('title',translate('messages.Vehicle_List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-car"></i> {{translate('messages.vehicles_category_list')}} <span class="badge badge-soft-dark ml-2" id="itemCount">{{$vehicles->total()}}</span></h1>
                </div>

                <div class="col-sm-auto">
                    <a class="btn btn--primary" href="{{route('admin.users.delivery-man.vehicle.create')}}">
                        <i class="tio-add"></i> {{translate('messages.add_vehicle_category')}}
                    </a>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper">
                            <h5 class="card-title"></h5>
                            <form id="search-form">
                                <!-- Search -->
                                <div class="input--group input-group input-group-merge input-group-flush">
                                    <input id="datatableSearch" type="search" name="search"  value="{{request()?->search}}"  class="form-control" placeholder="{{ translate('Ex_:_Search_by_type...') }}" aria-label="Search here">
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
                               class="font-size-sm table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                               data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging":false
                               }'>
                            <thead class="thead-light">
                            <tr>
                                <th>{{ translate('messages.sl') }}</th>
                                <th >{{translate('messages.Type')}}</th>
                                <th >{{translate('messages.Total_Deliveryman')}}</th>
                                <th >{{translate('messages.minimum_coverage_area')}} ({{ translate('messages.km') }}) </th>
                                <th >{{translate('messages.Maximum_coverage_area')}} ({{ translate('messages.km') }})</th>
                                <th >{{translate('messages.Extra_charges')}}  ({{ \App\CentralLogics\Helpers::currency_symbol() }})</th>
                                <th>{{translate('messages.status')}}</th>
                                <th class="text-center">{{translate('messages.action')}}</th>
                            </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($vehicles as $key=>$vehicle)
                                <tr>
                                    <td>{{$key+$vehicles->firstItem()}}</td>
                                    <td>
                                        <span class="d-block text-body"><a href="{{route('admin.users.delivery-man.vehicle.view',[$vehicle->id])}}">{{Str::limit($vehicle['type'],25, '...')}}</a>
                                        </span>
                                    </td>
                                    <td>
                                        {{ $vehicle->delivery_man_count }}
                                    </td>
                                    <td>
                                        <span class="bg-gradient-light text-dark">
                                            {{ $vehicle->starting_coverage_area }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="bg-gradient-light text-dark">
                                            {{ $vehicle->maximum_coverage_area }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="bg-gradient-light text-dark">
                                         {{ \App\CentralLogics\Helpers::format_currency($vehicle->extra_charges) }}
                                        </span>
                                    </td>
                                    <td>
                                        <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$vehicle->id}}">
                                            <input type="checkbox"
                                            data-id="statusCheckbox{{$vehicle->id}}"
                                            data-type="status"
                                            data-image-on="{{ asset('/public/assets/admin/img/modal/mail-success.png') }}"
                                            data-image-off="{{ asset('/public/assets/admin/img/modal/mail-warning.png') }}"
                                            data-title-on="{{ translate('By_Turning_ON_Vehicle_Category!') }}"
                                            data-title-off="{{ translate('By_Turning_OFF_Vehicle_Category!') }}"
                                            data-text-on="<p>{{ translate('Turned_on_this_vehicle_category_extra_charge_will_be_added_on_the_delivery_charge_and_this_categories_deliverymen_can_receives_the_order.') }}</p>"
                                            data-text-off="<p>{{ translate('Turned_off_this_vehicle_category_extra_charge_will_not_be_added_on_the_delivery_charge_and_this_categories_deliverymen_can_not_receives_the_order') }}</p>"
                                            class="toggle-switch-input dynamic-checkbox" id="stocksCheckbox{{$vehicle->id}}" {{$vehicle->status?'checked':''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>

                            <form action="{{route('admin.users.delivery-man.vehicle.status',[$vehicle['id'],$vehicle->status?0:1])}}"
                                method="get" id="statusCheckbox{{$vehicle->id}}_form">
                                </form>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a href="#"
                                            data-id="{{$vehicle->id}}"
                                            data-vehicle_type="{{ $vehicle->type }}"
                                            data-status="{{ $vehicle->status}}"
                                            data-starting_coverage_area="{{ $vehicle->starting_coverage_area}}"
                                            data-maximum_coverage_area="{{ $vehicle->maximum_coverage_area}}"
                                            data-extra_charges="{{$vehicle->extra_charges}}"
                                            data-edit_button="{{route('admin.users.delivery-man.vehicle.edit',[$vehicle['id']])}}"
                                            data-delete_button="vehicle-{{$vehicle['id']}}"
                                            class="btn action-btn btn--warning btn-outline-warning vehicle-info-show" ><i class="tio-visible"></i>
                                            </a>
                                            <a class="btn btn-sm btn--primary btn-outline-primary action-btn"
                                                href="{{route('admin.users.delivery-man.vehicle.edit',[$vehicle['id']])}}" title="{{translate('messages.edit_vehicle_category')}}"><i class="tio-edit"></i>
                                            </a>
                                            <a class="btn btn-sm btn--danger btn-outline-danger action-btn form-alert" href="javascript:"
                                                data-id="vehicle-{{$vehicle['id']}}" data-message="{{translate('messages.Want_to_delete_this_vehicle_category')}}" title="{{translate('messages.delete_vehicle_category')}}"><i class="tio-delete-outlined"></i>
                                            </a>
                                            <form action="{{route('admin.users.delivery-man.vehicle.delete',['id' =>$vehicle['id']])}}"
                                                        method="post" id="vehicle-{{$vehicle['id']}}">
                                                @csrf @method('delete')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if(count($vehicles) === 0)
                        <div class="empty--data">
                            <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                            <h5>
                                {{translate('no_data_found')}}
                            </h5>
                        </div>
                        @endif
                        <div class="page-area px-4 pb-3">
                            <div class="d-flex align-items-center justify-content-end">
                                <div>
                                    {!! $vehicles->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>





    <div class="modal fade" id="vehicledetailList">
        <div class="modal-dialog status-warning-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true" class="tio-clear"></span>
                    </button>
                </div>
                <div class="modal-body pb-5 pt-0">
                    <div class="mx-auto mb-20">
                        <div class="mb-4" >

                            <div class="d-flex justify-content-center mb-2  align-items-center gap-2  fs-16">
                                <span  class="text-dark">{{translate('Vehicle_Type')}}</span>
                                :
                                <span id="vehicle_type" class="font-semibold text-dark">  </span>
                            </div>
                            <div class="d-flex justify-content-center mb-2 align-items-center gap-2">
                                <span  class="text-dark">{{translate('status')}}</span>
                                :
                                <span id="status"></span>
                            </div>

                            <div class="bg-light border mt-4 p-4 rounded text-dark">
                                <div class="d-flex justify-content-center  align-items-center gap-2">
                                    <span>{{translate('minimum_coverage_area')}} ({{ translate('messages.km') }})</span>
                                    :
                                    <span class="font-semibold text-dark" id="starting_coverage_area"></span>
                                </div>
                                <div class="d-flex justify-content-center mb-2 mt-2 align-items-center gap-2">
                                    <span>{{translate('maximum_coverage_area')}} ({{ translate('messages.km') }})</span>
                                    :
                                    <span class="font-semibold text-dark" id="maximum_coverage_area"></span>
                                </div>
                                <div class="d-flex justify-content-center  align-items-center gap-2">
                                    <span>{{translate('extra_charges')}} ({{\App\CentralLogics\Helpers::currency_symbol()}})</span>
                                    :
                                    <span class="font-semibold text-dark" id="extra_charges"></span>
                                </div>
                            </div>
                        </div>
                        <div class="btn--container mt-2 mb-2 justify-content-center">
                            <a href="#" id="delete_button" data-message="{{translate('messages.Want_to_delete_this_vehicle_category')}}" title="{{translate('messages.delete_vehicle')}}" class="btn btn--cancel min-w-120 form-alert">  {{translate("delete")}}  </a>
                            <a href="#"  id="edit_button" type="button" class="btn btn--primary min-w-120" >{{translate('Edit')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




@endsection

@push('script_2')
    <script>
        "use strict";

        $('.vehicle-info-show').on('click', function () {
            let data = $(this).data();
            $('.modal-body #id').text(data.id);
            $('.modal-body #vehicle_type').text(data.vehicle_type);
            $('.modal-body #starting_coverage_area').text(data.starting_coverage_area);
            $('.modal-body #maximum_coverage_area').text(data.maximum_coverage_area);
            $('.modal-body #extra_charges') .text(data.extra_charges);
            $('.modal-body #delete_button').attr('data-id',  data.delete_button);
            $('.modal-body #edit_button').attr('href',  data.edit_button);
                if(data.status == 1){
                    $('.modal-body #status').text('{{ translate('messages.Active') }}').addClass('badge badge-soft-success');
                } else{
                    $('.modal-body #status').text('{{ translate('messages.Inactive') }}').addClass('badge badge-soft-danger');
                }
        $('#vehicledetailList').modal('show');
    })

        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
                    .search(this.value)
                    .draw();
            });

            $('#column2_search').on('keyup', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });

            $('#column3_search').on('change', function () {
                datatable
                    .columns(3)
                    .search(this.value)
                    .draw();
            });

            $('#column4_search').on('keyup', function () {
                datatable
                    .columns(4)
                    .search(this.value)
                    .draw();
            });



        });
    </script>
@endpush
