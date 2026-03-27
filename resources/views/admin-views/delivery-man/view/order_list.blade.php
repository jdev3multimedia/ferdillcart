@extends('layouts.admin.app')

@section('title', translate('messages.Delivery Man Preview'))


@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        @include('admin-views.delivery-man.partials._page_header')

        <div class="">
            @include('admin-views.delivery-man.partials._tab_menu')
        </div>
    </div>
    <!-- End Page Header -->

    <div class="card">
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-sm-6 col-xl-3">
                    <div class="color-card flex-column align-items-center justify-content-center color-2">
                        <div class="img-box">
                            <img class="resturant-icon w--30"
                                src="{{asset('/public/assets/admin/img/icons/order-icon-1.png')}}" alt="transactions">
                        </div>

                        <div class="d-flex flex-column align-items-center">
                            <h2 class="title"> {{$deliveryMan->orders->count()}} </h2>
                            <div class="subtitle">
                                {{translate('messages.total_order')}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="color-card flex-column align-items-center justify-content-center color-5">
                        <div class="img-box">
                            <img class="resturant-icon w--30"
                                src="{{asset('/public/assets/admin/img/icons/order-icon-2.png')}}" alt="transactions">
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <h2 class="title">
                                {{\App\CentralLogics\Helpers::format_currency($deliveryMan->total_ongoing_orders->sum('order_amount'))}}
                            </h2>
                            <div class="subtitle">
                                {{translate('messages.ongoing_order')}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="color-card flex-column align-items-center justify-content-center color-7">
                        <div class="img-box">
                            <img class="resturant-icon w--30"
                                src="{{asset('/public/assets/admin/img/icons/order-icon-3.png')}}" alt="transactions">
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <h2 class="title">
                                {{\App\CentralLogics\Helpers::format_currency($deliveryMan->total_delivered_orders->sum('order_amount'))}}

                            </h2>
                            <div class="subtitle">
                                {{translate('messages.completed_order')}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="color-card flex-column align-items-center justify-content-center color-4">
                        <div class="img-box">
                            <img class="resturant-icon w--30"
                                src="{{asset('/public/assets/admin/img/icons/order-icon-4.png')}}" alt="transactions">
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <h2 class="title"> {{$deliveryMan->total_canceled_orders->count()}} </h2>
                            <div class="subtitle">
                                {{translate('messages.cancel_order')}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3 mb-lg-5 mt-2">
        <div class="card-header py-2 border-0 gap-2">
            <div class="search--button-wrapper">
                <h4 class="card-title">{{ translate('messages.order_list')}}
                    <span class="badge badge-soft-dark ml-2" id="itemCount">
                        {{$order_lists->total()}}
                    </span>
                </h4>
            </div>
        </div>
        <!-- Body -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="datatable"
                    class="table table-borderless table-thead-bordered table-nowrap justify-content-between table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0">{{translate('SL')}}</th>
                            <th class="border-0">{{translate('messages.order_id')}}</th>
                            <th class="border-0">{{translate('messages.contact_info')}}</th>
                            <th class="border-0">{{translate('messages.total_items')}}</th>
                            <th class="border-0">{{translate('messages.total_amount')}}</th>
                            <th class="border-0">{{translate('messages.delivery_date')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order_lists as $key => $order)
                        <tr>
                            <td scope="row">{{$key + $order_lists->firstItem()}}</td>
                            <td><a
                                    href="{{route((isset($order->order) && $order->order_type == 'parcel') ? 'admin.parcel.order.details' : 'admin.order.details', [$order->id, 'module_id' => $order->transaction?->module_id])}}">{{$order->id}}</a>
                            </td>
                            <td>


                                @if($order->is_guest)
                                @php($customer_details = json_decode($order['delivery_address'], true))
                                    <strong
                                        title="{{$customer_details['contact_person_name']}}">{{$customer_details['contact_person_name']}}</strong>
                                    <div>{{$customer_details['contact_person_number']}}</div>
                                @elseif($order->customer)

                                    <a class="text-body"
                                        title="{{$order->customer['f_name'] . ' ' . $order->customer['l_name']}}"
                                        href="{{route('admin.customer.view', [$order['user_id']])}}">
                                        <strong>
                                            <div> {{$order->customer['f_name'] . ' ' . $order->customer['l_name']}}</div>
                                        </strong>
                                    </a>
                                    <a href="tel:{{$order->customer['phone']}}">
                                        <div>{{$order->customer['phone']}}</div>
                                    </a>
                                @else
                                <label
                                    class="badge badge-danger">{{translate('messages.invalid_customer_data')}}</label>
                                @endif
                            </td>
                            <td>{{$order?->details()?->count() }}</td>
                            <td>
                                {{\App\CentralLogics\Helpers::format_currency($order['order_amount'])}}
                            </td>
                            <td>
                                <div>
                                    {{ \App\CentralLogics\Helpers::date_format($order->created_at) }}
                                </div>
                                <div class="d-block text-uppercase">
                                    {{ \App\CentralLogics\Helpers::time_format($order->created_at) }}
                                </div>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
                @if(count($order_lists) !== 0)
                    <hr>
                @endif
                <div class="page-area">
                    {!! $order_lists->links() !!}
                </div>
                @if(count($order_lists) === 0)
                    <div class="empty--data">
                        <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                        <h5>
                            {{translate('no_data_found')}}
                        </h5>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection