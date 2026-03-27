@extends('layouts.vendor.app')

@section('title', translate('messages.Order Details'))


@section('content')
    <?php

    $tax_included =0;
    if (count($order->details) > 0) {
        $campaign_order = isset($order?->details[0]?->item_campaign_id ) ? true : false;
    }
    $max_processing_time = explode('-', $order['store']['delivery_time'])[0];
    ?>
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">
                        <span class="page-header-icon">
                            <img src="{{ asset('/public/assets/admin/img/shopping-basket.png') }}" class="w--20"
                                alt="">
                        </span>
                        <span>
                            {{ translate('order_details') }} <span
                                class="badge badge-soft-dark rounded-circle ml-1">{{ $order->details->count() }}</span>
                        </span>
                    </h1>
                </div>

                <div class="col-sm-auto">
                    <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle mr-1"
                        href="{{ route('vendor.order.details', [$order['id'] - 1]) }}" data-toggle="tooltip"
                        data-placement="top" title="Previous order">
                        <i class="tio-chevron-left"></i>
                    </a>
                    <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle"
                        href="{{ route('vendor.order.details', [$order['id'] + 1]) }}" data-toggle="tooltip"
                        data-placement="top" title="Next order">
                        <i class="tio-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row" id="printableArea">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <!-- Card -->
                <div class="card mb-3 mb-lg-5">
                    <!-- Header -->
                    <div class="card-header border-0 align-items-start flex-wrap">
                        <div class="order-invoice-left d-flex d-sm-flex justify-content-between">
                            <div>
                                <h1 class="page-header-title">
                                    {{ translate('messages.order') }} #{{ $order['id'] }}
                                    @if ($order->edited)
                                        <span class="badge badge-soft-danger ml-sm-3">
                                            {{ translate('messages.edited') }}
                                        </span>
                                    @endif
                                </h1>
                                <span class="mt-2 d-block">
                                    <i class="tio-date-range"></i>
                                    {{ date('d M Y ' . config('timeformat'), strtotime($order['created_at'])) }}
                                </span>
                                @if ($order->schedule_at && $order->scheduled)
                                    <h6 class="text-capitalize">
                                        {{ translate('messages.scheduled_at') }}
                                        : <label
                                            class="fz--10 badge badge-soft-warning">{{ date('d M Y ' . config('timeformat'), strtotime($order['schedule_at'])) }}</label>
                                    </h6>
                                @endif
                                @if($order['cancellation_reason'])
                                <h6>
                                    <span class="text-danger">{{ translate('messages.order_cancellation_reason') }} :</span>
                                    {{ $order['cancellation_reason'] }}
                                </h6>
                                @endif

                           
                                <!-- New Note -->
                                @if ($order['bring_change_amount'] > 0)
                                <div class="info-notes-bg px-3 color-222324CC py-2 rounded fs-12  gap-2 mt-2">
                                    {{ translate('Please_bring') }} <strong class="text-title"> {{  \App\CentralLogics\Helpers::format_currency($order['bring_change_amount']) }}</strong> {{ translate('in_change_when_making_the_delivery') }}.
                                </div>
                                @endif
                                <!-- New Note End -->
                                @if ($order['unavailable_item_note'])
                                    <h6 class="w-100 badge-soft-warning p-1 rounded mt-2">
                                        <span class="text-dark">
                                            {{ translate('messages.order_unavailable_item_note') }} :
                                        </span>
                                        {{ $order['unavailable_item_note'] }}
                                    </h6>
                                @endif
                                @if ($order['delivery_instruction'])
                                    <h6 class="w-100 badge-soft-warning p-1 rounded mt-2">
                                        <span class="text-dark">
                                            {{ translate('messages.order_delivery_instruction') }} :
                                        </span>
                                        {{ $order['delivery_instruction'] }}
                                    </h6>
                                @endif
                                @if ($order['order_note'])
                                    <h6>
                                        {{ translate('messages.order_note') }} :
                                        {{ $order['order_note'] }}
                                    </h6>
                                @endif
                            </div>
                            <div class="d-sm-none">
                                <a class="btn btn--primary print--btn font-regular"
                                    href={{ route('vendor.order.generate-invoice', [$order['id']]) }}>
                                    <i class="tio-print mr-sm-1"></i> <span>{{ translate('messages.print_invoice') }}</span>
                                </a>
                            </div>
                        </div>


                        <div class="order-invoice-right mt-3 mt-sm-0">
                            <div class="btn--container ml-auto align-items-center justify-content-end">
                                <a class="btn btn--primary print--btn font-regular d-none d-sm-block"
                                    href={{ route('vendor.order.generate-invoice', [$order['id']]) }}>
                                    <i class="tio-print mr-sm-1"></i> <span>{{ translate('messages.print_invoice') }}</span>
                                </a>
                            </div>
                            <div class="text-right mt-3 order-invoice-right-contents text-capitalize">
                                <h6>
                                    {{ translate('messages.payment_status') }} :
                                    @if ($order['payment_status'] == 'paid')
                                        <span class="badge badge-soft-success ml-sm-3">
                                            {{ translate('messages.paid') }}
                                        </span>
                                        @elseif ($order['payment_status'] == 'partially_paid')

                                        @if ($order->payments()->where('payment_status','unpaid')->exists())
                                        <span class="text-danger">{{ translate('messages.partially_paid') }}</span>
                                        @else
                                        <span class="text-success">{{ translate('messages.paid') }}</span>
                                        @endif
                                    @else
                                        <span class="badge badge-soft-danger ml-sm-3">
                                            {{ translate('messages.unpaid') }}
                                        </span>
                                    @endif
                                </h6>
                                @if ($order->store && $order->store->module->module_type == 'food')
                                <h6>
                                    <span>{{ translate('cutlery') }}</span> <span>:</span>
                                    @if ($order['cutlery'] == '1')
                                        <span class="badge badge-soft-success ml-sm-3">
                                            {{ translate('messages.yes') }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger ml-sm-3">
                                            {{ translate('messages.no') }}
                                        </span>
                                    @endif

                                </h6>
                                @endif
                                <h6 class="text-capitalize">
                                    {{ translate('messages.payment_method') }} :
                                    {{ translate(str_replace('_', ' ', $order['payment_method'])) }}
                                </h6>
                                @if ($order['transaction_reference'])
                                    <h6 class="">
                                        {{ translate('messages.reference_code') }} :
                                        <button class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                            data-target=".bd-example-modal-sm">
                                            {{ translate('messages.add') }}
                                        </button>
                                    </h6>
                                @endif
                                <h6 class="text-capitalize">{{ translate('messages.order_type') }}
                                    : <label
                                        class="fz--10 badge m-0 badge-soft-primary">{{ translate(str_replace('_', ' ', $order['order_type'])) }}</label>
                                </h6>
                                <h6>
                                    {{ translate('messages.order_status') }} :
                                    @if ($order['order_status'] == 'pending')
                                        <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.pending') }}
                                        </span>
                                    @elseif($order['order_status'] == 'confirmed')
                                        <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.confirmed') }}
                                        </span>
                                    @elseif($order['order_status'] == 'processing')
                                        <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.processing') }}
                                        </span>
                                    @elseif($order['order_status'] == 'picked_up')
                                        <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.out_for_delivery') }}
                                        </span>
                                    @elseif($order['order_status'] == 'delivered')
                                        <span class="badge badge-soft-success ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.delivered') }}
                                        </span>
                                    @elseif($order['order_status'] == 'failed')
                                        <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.payment_failed') }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                                            {{ str_replace('_', ' ', $order['order_status']) }}
                                        </span>
                                    @endif
                                </h6>
                                @if ($order->order_attachment)
                                        @php
                                            $order_images = json_decode($order->order_attachment,true) ?? [];
                                        @endphp
                                    {{-- @if (is_array($order_images)) --}}
                                        <h5 class="text-dark">
                                            {{ translate('messages.prescription') }}:
                                        </h5>
                                        <div class="d-flex flex-wrap flex-md-row-reverse __gap-15px" >
                                            @foreach ($order_images as $key => $item)
                                            @php($item = is_array($item)?$item:['img'=>$item,'storage'=>'public'])
                                                <div>
                                                    <button class="btn w-100 px-0" data-toggle="modal"
                                                        data-target="#prescriptionimagemodal{{ $key }}"
                                                        title="{{ translate('messages.order_attachment') }}">
                                                        <div class="gallary-card ml-auto">
                                                            <img  src="{{\App\CentralLogics\Helpers::get_full_url('order',$item['img'],$item['storage']) }}"
                                                                alt="{{ translate('messages.prescription') }}"
                                                                class="initial--22 object-cover">
                                                        </div>
                                                    </button>
                                                </div>
                                                <div class="modal fade" id="prescriptionimagemodal{{ $key }}" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel">
                                                                    {{ translate('messages.prescription') }}</h4>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal"><span
                                                                        aria-hidden="true">&times;</span><span
                                                                        class="sr-only">{{ translate('messages.cancel') }}</span></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src="{{\App\CentralLogics\Helpers::get_full_url('order',$item['img'],$item['storage']) }}"
                                                                    class="initial--22 w-100" alt="image">
                                                            </div>
                                                            @php($storage = $item['storage']??'public')
                                                            @php($file = $storage == 's3'?base64_encode('order/' . $item['img']):base64_encode('public/order/' . $item['img']))
                                                            <div class="modal-footer">
                                                                <a class="btn btn-primary"
                                                                    href="{{ route('admin.file-manager.download', [$file,$storage]) }}"><i
                                                                        class="tio-download"></i>
                                                                    {{ translate('messages.download') }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body px-0">
                        <?php
                        $total_addon_price = 0;
                        $product_price = 0;
                        $store_discount_amount = 0;
                        $admin_flash_discount_amount = $order['flash_admin_discount_amount'];
                        $ref_bonus_amount = $order['ref_bonus_amount'];
                        $extra_packaging_amount = $order['extra_packaging_amount'];
                        $store_flash_discount_amount = $order['flash_store_discount_amount'];

                        if ($order->prescription_order == 1) {
                            $product_price = $order['order_amount'] - $order['delivery_charge'] - $order['total_tax_amount'] - $order['dm_tips'] - $order['additional_charge'] + $order['store_discount_amount'];
                            if($order->tax_status == 'included'){
                                $product_price += $order['total_tax_amount'];
                            }
                        }

                        $total_addon_price = 0;
                        ?>
                        <div class="table-responsive">
                            <table
                                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table dataTable no-footer mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="border-0">{{ translate('messages.#') }}</th>
                                        <th class="border-0">{{ translate('messages.item_details') }}</th>
                                        @if ($order->store->module->module_type == 'food')
                                            <th class="border-0">{{ translate('messages.addons') }}</th>
                                        @endif
                                        <th class="text-right  border-0">{{ translate('messages.price') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->details as $key => $detail)
                                        @if (isset($detail->item_id))
                                            @php($detail->item = json_decode($detail->item_details, true))
                                            @php($product = \App\Models\Item::where(['id' => $detail->item['id']])->first())
                                            <!-- Media -->
                                            <tr>
                                                <td>
                                                    <div>
                                                        {{ $key + 1 }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="media media--sm">
                                                        <a class="avatar avatar-xl mr-3"
                                                            href="{{ route('vendor.item.view', $detail->item['id']) }}">
                                                            <img class="img-fluid rounded onerror-image"
                                                            src="{{ $product->image_full_url  ?? asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                                                 data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                                                alt="Image Description">
                                                        </a>
                                                        <div class="media-body">
                                                            <div>
                                                                <strong
                                                                    class="line--limit-1">{{ Str::limit($detail->item['name'], 25, '...') }}</strong>
                                                                <h6>
                                                                    {{ $detail['quantity'] }} x
                                                                    {{ \App\CentralLogics\Helpers::format_currency($detail['price']) }}
                                                                </h6>
                                                                @if ($order->store && $order->store->module->module_type == 'food')
                                                                    @if (isset($detail['variation']) ? json_decode($detail['variation'], true) : [])
                                                                        @foreach (json_decode($detail['variation'], true) as $variation)
                                                                            @if (isset($variation['name']) && isset($variation['values']))
                                                                                <span class="d-block text-capitalize">
                                                                                    <strong>
                                                                                        {{ $variation['name'] }} -
                                                                                    </strong>
                                                                                </span>
                                                                                @foreach ($variation['values'] as $value)
                                                                                    <span class="d-block text-capitalize">
                                                                                        &nbsp; &nbsp;
                                                                                        {{ $value['label'] }} :
                                                                                        <strong>{{ \App\CentralLogics\Helpers::format_currency($value['optionPrice']) }}</strong>
                                                                                    </span>
                                                                                @endforeach
                                                                            @else
                                                                                @if (isset(json_decode($detail['variation'], true)[0]))
                                                                                    <strong><u>
                                                                                            {{ translate('messages.Variation') }}
                                                                                            : </u></strong>
                                                                                    @foreach (json_decode($detail['variation'], true)[0] as $key1 => $variation)
                                                                                        <div
                                                                                            class="font-size-sm text-body">
                                                                                            <span>{{ $key1 }}
                                                                                                : </span>
                                                                                            <span
                                                                                                class="font-weight-bold">{{ $variation }}</span>
                                                                                        </div>
                                                                                    @endforeach
                                                                                @endif
                                                                                {{-- @break --}}
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                @else
                                                                    @if (count(json_decode($detail['variation'], true)) > 0)
                                                                        <strong><u>{{ translate('messages.variation') }}
                                                                                :
                                                                            </u></strong>
                                                                    <?php
                                                                        $detailsVariation = isset(json_decode($detail['variation'], true)[0]) ? json_decode($detail['variation'], true)[0] : json_decode($detail['variation'], true);
                                                                    ?>
                                                                        @foreach ($detailsVariation as $key1 => $variation)
                                                                            @if ($key1 != 'stock' || ($order->store && config('module.' . $order->store->module->module_type)['stock']))
                                                                                <div class="font-size-sm text-body">
                                                                                        <span>{{ $key1 }} :
                                                                                        </span>
                                                                                    <span class="font-weight-bold">
                                                                                        {{ Str::limit(implode(', ', (array) $variation), 15, '...') }}
                                                                                    </span>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                @if ($order->store->module->module_type == 'food')
                                                    <td>
                                                        <div>
                                                            @foreach (json_decode($detail['add_ons'], true) as $key2 => $addon)
                                                                @if ($key2 == 0)
                                                                    <strong><u>{{ translate('messages.addons') }} :
                                                                        </u></strong>
                                                                @endif
                                                                <div class="font-size-sm text-body">
                                                                    <span>{{ Str::limit($addon['name'], 25, '...') }} :
                                                                    </span>
                                                                    <span class="font-weight-bold">
                                                                        {{ $addon['quantity'] }} x
                                                                        {{ \App\CentralLogics\Helpers::format_currency($addon['price']) }}
                                                                    </span>
                                                                </div>
                                                                @php($total_addon_price += $addon['price'] * $addon['quantity'])
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                @endif
                                                <td>
                                                    <div class="text-right">
                                                        @php($amount = $detail['price'] * $detail['quantity'])
                                                        <h5>{{ \App\CentralLogics\Helpers::format_currency($amount) }}</h5>
                                                    </div>
                                                </td>
                                            </tr>
                                            @php($product_price += $amount)
                                            @php($store_discount_amount += $detail['discount_on_item'] * $detail['quantity'])
                                            <!-- End Media -->
                                        @elseif(isset($detail->item_campaign_id))
                                            @php($detail->campaign = json_decode($detail->item_details, true))
                                            @php($campaign = \App\Models\ItemCampaign::where(['id' => $detail->campaign['id']])->first())
                                            <!-- Media -->
                                            <tr>
                                                <td>
                                                    <div>
                                                        {{ $key + 1 }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="media media--sm">
                                                        <div class="avatar avatar-xl mr-3">
                                                            <img class="img-fluid onerror-image"
                                                            src="{{$campaign?->image_full_url ?? asset('public/assets/admin/img/160x160/img2.jpg') }}"

                                                                 data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                                                alt="Image Description">
                                                        </div>
                                                        <div class="media-body">
                                                            <div>
                                                                <strong
                                                                    class="line--limit-1">{{ Str::limit($detail->campaign['name'], 25, '...') }}</strong>

                                                                <h6>
                                                                    {{ $detail['quantity'] }} x
                                                                    {{ \App\CentralLogics\Helpers::format_currency($detail['price']) }}
                                                                </h6>

                                                                @if (count(json_decode($detail['variation'], true)) > 0)
                                                                    <strong><u>{{ translate('messages.variation') }} :
                                                                        </u></strong>
                                                                    @foreach (json_decode($detail['variation'], true)[0] as $key1 => $variation)
                                                                        @if ($key1 != 'stock')
                                                                            <div class="font-size-sm text-body">
                                                                                <span>{{ $key1 }} : </span>
                                                                                <span
                                                                                    class="font-weight-bold">{{ Str::limit($variation, 25, '...') }}</span>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                @endif

                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                @if ($order->store->module->module_type == 'food')
                                                    <td>
                                                        @foreach (json_decode($detail['add_ons'], true) as $key2 => $addon)
                                                            @if ($key2 == 0)
                                                                <strong><u>{{ translate('messages.addons') }} :
                                                                    </u></strong>
                                                            @endif
                                                            <div class="font-size-sm text-body">
                                                                <span>{{ Str::limit($addon['name'], 20, '...') }} : </span>
                                                                <span class="font-weight-bold">
                                                                    {{ $addon['quantity'] }} x
                                                                    {{ \App\CentralLogics\Helpers::format_currency($addon['price']) }}
                                                                </span>
                                                            </div>
                                                            @php($total_addon_price += $addon['price'] * $addon['quantity'])
                                                        @endforeach
                                                    </td>
                                                @endif
                                                <td>
                                                    <div class="text-right">
                                                        @php($amount = $detail['price'] * $detail['quantity'])
                                                        <h5>{{ \App\CentralLogics\Helpers::format_currency($amount) }}</h5>
                                                    </div>
                                                </td>
                                            </tr>
                                            @php($product_price += $amount)
                                            @php($store_discount_amount += $detail['discount_on_item'] * $detail['quantity'])
                                            <!-- End Media -->
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mx-3">
                            <hr>
                        </div>
                        <?php

                        $coupon_discount_amount = $order['coupon_discount_amount'];

                        $total_price = $product_price + $total_addon_price - $store_discount_amount - $coupon_discount_amount - $admin_flash_discount_amount -$ref_bonus_amount -$extra_packaging_amount -$store_flash_discount_amount;

                        $total_tax_amount = $order['total_tax_amount'];
                        if($order->tax_status == 'included'){
                                $total_tax_amount=0;
                            }
                        $tax_included = \App\Models\BusinessSetting::where(['key'=>'tax_included'])->first() ?  \App\Models\BusinessSetting::where(['key'=>'tax_included'])->first()->value : 0;

                        $store_discount_amount = $order['store_discount_amount'];

                        ?>
                        <div class="row justify-content-md-end mb-3 mx-0 mt-4">
                            <div class="col-md-9 col-lg-8">
                                <dl class="row text-right">
                                    <dt class="col-6">{{ translate('messages.items_price') }}:</dt>
                                    <dd class="col-6">{{ \App\CentralLogics\Helpers::format_currency($product_price) }}
                                    </dd>
                                    @if ($order->store->module->module_type == 'food')
                                        <dt class="col-6">{{ translate('messages.addon_cost') }}:</dt>

                                        <dd class="col-6">
                                            {{ \App\CentralLogics\Helpers::format_currency($total_addon_price) }}
                                            <hr>
                                        </dd>
                                    @endif

                                    <dt class="col-6">{{ translate('messages.subtotal') }}
                                        @if ($order->tax_status == 'included' ||  $tax_included ==  1)
                                        ({{ translate('messages.TAX_Included') }})
                                        @endif
                                        :</dt>

                                    <dd class="col-6">
                                        @if ($order->prescription_order == 1 && in_array($order['order_status'],['pending','confirmed','processing','accepted']))
                                            <button class="btn btn-sm" type="button" data-toggle="modal"
                                                data-target="#edit-order-amount"><i class="tio-edit"></i></button>
                                        @endif
                                        {{ \App\CentralLogics\Helpers::format_currency($product_price + $total_addon_price) }}
                                    </dd>
                                    <dt class="col-6">{{ translate('messages.discount') }}:</dt>
                                    <dd class="col-6">
                                        @if ($order->prescription_order == 1 && in_array($order['order_status'],['pending','confirmed','processing','accepted']))
                                            <button class="btn btn-sm" type="button" data-toggle="modal"
                                                data-target="#edit-discount-amount"><i class="tio-edit"></i></button>
                                        @endif
                                        - {{ \App\CentralLogics\Helpers::format_currency($store_discount_amount + $admin_flash_discount_amount  +$store_flash_discount_amount) }}
                                    </dd>



                                    <dt class="col-6">{{ translate('messages.coupon_discount') }}:</dt>
                                    <dd class="col-6">
                                        - {{ \App\CentralLogics\Helpers::format_currency($coupon_discount_amount) }}</dd>

                                    @if ($ref_bonus_amount > 0)
                                    <dt class="col-6">{{ translate('messages.Referral_Discount') }}:</dt>
                                    <dd class="col-6">
                                        - {{ \App\CentralLogics\Helpers::format_currency($ref_bonus_amount) }}</dd>

                                    @endif

                                    @if ($order->tax_status == 'excluded' || $order->tax_status == null  )
                                    <dt class="col-sm-6">{{ translate('messages.vat/tax') }}:</dt>
                                    <dd class="col-sm-6">
                                        +
                                        {{ \App\CentralLogics\Helpers::format_currency($total_tax_amount) }}
                                    </dd>
                                    @endif
                                    <dt class="col-6">{{ translate('messages.delivery_man_tips') }}</dt>
                                    <dd class="col-6">
                                        + {{ \App\CentralLogics\Helpers::format_currency($order->dm_tips) }}</dd>
                                    <dt class="col-6">{{ translate('messages.delivery_fee') }}:</dt>
                                    <dd class="col-6">
                                        @php($del_c = $order['delivery_charge'])
                                        + {{ \App\CentralLogics\Helpers::format_currency($del_c) }}
                                        <hr>
                                    </dd>
                                    <dt class="col-6">{{ \App\CentralLogics\Helpers::get_business_data('additional_charge_name')??translate('messages.additional_charge') }}:</dt>
                                    <dd class="col-6">
                                        @php($additional_charge = $order['additional_charge'])
                                        + {{ \App\CentralLogics\Helpers::format_currency($additional_charge) }}
                                    </dd>
                                    @if ($extra_packaging_amount > 0)
                                    <dt class="col-6">{{ translate('messages.Extra_Packaging_Amount') }}:</dt>
                                    <dd class="col-6">
                                        + {{ \App\CentralLogics\Helpers::format_currency($extra_packaging_amount) }}</dd>
                                    @endif
                                    @if ($order['partially_paid_amount'] > 0)

                                    <dt class="col-6">{{ translate('messages.partially_paid_amount') }}:</dt>
                                    <dd class="col-6">
                                        @php($partially_paid_amount = $order['partially_paid_amount'])
                                            {{ \App\CentralLogics\Helpers::format_currency($partially_paid_amount) }}
                                    </dd>
                                    <dt class="col-6">{{ translate('messages.due_amount') }}:</dt>
                                    @if ($order['payment_method'] == 'partial_payment')

                                    <dd class="col-6">
                                            {{ \App\CentralLogics\Helpers::format_currency($order->order_amount-$partially_paid_amount) }}
                                    </dd>
                                    @else
                                    <dd class="col-6">
                                            {{ \App\CentralLogics\Helpers::format_currency(0) }}
                                    </dd>
                                    @endif
                                    @endif

                                    <dt class="col-6">{{ translate('messages.total') }}:</dt>
                                    <dd class="col-6">
                                        {{ \App\CentralLogics\Helpers::format_currency($product_price + $del_c + $total_tax_amount + $total_addon_price + $additional_charge - $coupon_discount_amount - $store_discount_amount - $admin_flash_discount_amount  - $ref_bonus_amount + $extra_packaging_amount-$store_flash_discount_amount + $order->dm_tips) }}
                                    </dd>
                                    @if ($order?->payments)
                                        @foreach ($order?->payments as $payment)
                                            @if ($payment->payment_status == 'paid')
                                                @if ( $payment->payment_method == 'cash_on_delivery')

                                                <dt class="col-sm-6">{{ translate('messages.Paid_with_Cash') }} ({{  translate('COD')}}) :</dt>
                                                @else

                                                <dt class="col-sm-6">{{ translate('messages.Paid_by') }} {{  translate($payment->payment_method)}} :</dt>
                                                @endif
                                            @else

                                            <dt class="col-sm-6">{{ translate('Due_Amount') }} ({{  $payment->payment_method == 'cash_on_delivery' ?  translate('messages.COD') : translate($payment->payment_method) }}) :</dt>
                                            @endif
                                        <dd class="col-sm-6">
                                            {{ \App\CentralLogics\Helpers::format_currency($payment->amount) }}
                                        </dd>
                                        @endforeach
                                    @endif
                                </dl>
                                <!-- End Row -->
                            </div>
                        </div>
                        <!-- End Row -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-4">
                <!-- Card -->
                @if ($order->order_status != 'refund_requested' &&
                    $order->order_status != 'refunded' &&
                    $order->order_status != 'delivered')
                    <div class="card mb-2">
                        <!-- Header -->
                        <div class="card-header justify-content-center text-center px-0 mx-4">
                            <h5 class="card-header-title text-capitalize">
                                <span>{{ translate('messages.order_setup') }}</span>
                            </h5>
                        </div>
                        <!-- End Header -->

                        <!-- Body -->

                        <div class="card-body">
                            <!-- Order Status Flow Starts -->
                            @php($order_delivery_verification = (bool) \App\Models\BusinessSetting::where(['key' => 'order_delivery_verification'])->first()->value)
                            <div class="mb-4">
                                <div class="row g-1">
                                    <div class="{{ config('canceled_by_store') ? 'col-6' : 'col-12' }}">
                                        <a class="btn btn--primary w-100 fz--13 px-2 {{ $order['order_status'] == 'pending' ? '' : 'd-none' }} route-alert"
                                           data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'confirmed']) }}"
                                           data-message="{{ translate('messages.confirm_this_order_?') }}"
                                            href="javascript:">{{ translate('messages.confirm_this_order') }}</a>
                                    </div>
                                    @if (config('canceled_by_store'))
                                        <div class="col-6">
                                            <a class="btn btn--danger w-100 fz--13 px-2 cancelled-status {{ $order['order_status'] == 'pending' ? '' : 'd-none' }}"
                                               >{{ translate('Cancel Order') }}</a>
                                        </div>
                                    @endif
                                </div>
                                    @if ($order->store && $order->store->module->module_type == 'food')
                                        <a class="btn btn--primary w-100 order-status-change-alert {{ $order['order_status'] == 'confirmed' || $order['order_status'] == 'accepted' ? '' : 'd-none' }}"

                                           data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'processing']) }}"
                                           data-message="{{ translate('Change status to cooking ?') }}"
                                           data-verification="false"
                                           data-processing-time="{{ $max_processing_time }}"
                                           href="javascript:">{{ translate('messages.proceed_for_processing') }}</a>
                                    @else
                                    <a class="btn btn--primary w-100 route-alert  {{ $order['order_status'] == 'confirmed' || $order['order_status'] == 'accepted' ? '' : 'd-none' }}"
                                       data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'processing']) }}"
                                       data-message="{{ translate('messages.proceed_for_processing') }}"
                                    href="javascript:">{{ translate('messages.proceed_for_processing') }}</a>
                                    @endif
                                <a class="btn btn--primary w-100 route-alert {{ $order['order_status'] == 'processing' ? '' : 'd-none' }}"
                                   data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'handover']) }}"
                                   data-message="{{ translate('messages.make_ready_for_handover') }}"
                                    href="javascript:">{{ translate('messages.make_ready_for_handover') }}</a>
                                 @if($order['order_status'] == 'handover'|| ($order['order_status'] == 'picked_up' && $order->store->sub_self_delivery == 1))
                                    <a class="btn  w-100
                                    {{ ($order['order_type'] == 'take_away' || $order->store->sub_self_delivery == 1)  ?  'btn--primary order-status-change-alert'  :  'btn--secondary  self-delivery-warning' }} "
                                       data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'delivered']) }}"
                                       data-message="{{ translate('messages.Change status to delivered (payment status will be paid if not)?') }}"
                                       data-verification="{{ $order_delivery_verification ? 'true' : 'false' }}"
                                        href="javascript:">{{ translate('messages.make_delivered') }}</a>
                                 @endif

                            </div>
                        </div>

                        <!-- End Body -->
                    </div>
                @endif
                <!-- End Card -->
                @if ($order->order_status == 'canceled')
                <ul class="delivery--information-single mt-3">
                    <li>
                        <span class=" badge badge-soft-danger "> {{ translate('messages.Cancel_Reason') }} :</span>
                        <span class="info">  {{ $order->cancellation_reason }} </span>
                    </li>

                    <li>
                        <span class="name">{{ translate('Cancel_Note') }} </span>
                        <span class="info">  {{ $order->cancellation_note ?? translate('messages.N/A')}} </span>
                    </li>
                    <li>
                        <span class="name">{{ translate('Canceled_By') }} </span>
                        <span class="info">  {{ translate($order->canceled_by) }} </span>
                    </li>
                    @if ($order->payment_status == 'paid' || $order->payment_status == 'partially_paid' )
                            @if ( $order?->payments)
                                @php( $pay_infos =$order->payments()->where('payment_status','paid')->get())
                                @foreach ($pay_infos as $pay_info)
                                    <li>
                                        <span class="name">{{ translate('Amount_paid_by') }} {{ translate($pay_info->payment_method) }} </span>
                                        <span class="info">  {{ \App\CentralLogics\Helpers::format_currency($pay_info->amount)  }} </span>
                                    </li>
                                @endforeach
                            @else
                            <li>
                                <span class="name">{{ translate('Amount_paid_by') }} {{ translate($order->payment_method) }} </span>
                                <span class="info ">  {{ \App\CentralLogics\Helpers::format_currency($order->order_amount)  }} </span>
                            </li>
                            @endif
                    @endif

                    @if ($order->payment_status == 'paid' || $order->payment_status == 'partially_paid')
                        @if ( $order?->payments)
                            @php( $amount =$order->payments()->where('payment_status','paid')->sum('amount'))
                                <li>
                                    <span class="name">{{ translate('Amount_Returned_To_Wallet') }} </span>
                                    <span class="info">  {{ \App\CentralLogics\Helpers::format_currency($amount)  }} </span>
                                </li>
                        @else
                        <li>
                            <span class="name">{{ translate('Amount_Returned_To_Wallet') }} </span>
                            <span class="info">  {{ \App\CentralLogics\Helpers::format_currency($order->order_amount)  }} </span>
                        </li>
                        @endif
                    @endif
                </ul>
                <hr class="w-100">
            @endif
                @if ($order['order_type'] != 'take_away')
                    <!-- Card -->
                    <div class="card mb-2">
                        <!-- Header -->
                        <div class="card-header">
                            <h4 class="card-header-title">
                                <span class="card-header-icon"><i class="tio-user"></i></span>
                                <span>{{ translate('messages.Delivery Man') }}</span>
                            </h4>
                        </div>
                        <!-- End Header -->

                        <!-- Body -->
                        <div class="card-body">
                            @if ($order->delivery_man)
                                <div class="media align-items-center customer--information-single" href="javascript:">
                                    <div class="avatar avatar-circle">
                                        <img class="avatar-img onerror-image"
                                             data-onerror-image="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                             src="{{ $order->delivery_man->image_full_url }}"
                                            alt="Image Description">
                                    </div>
                                    <div class="media-body">
                                        <span
                                            class="text-body d-block text-hover-primary mb-1">{{ $order->delivery_man['f_name'] . ' ' . $order->delivery_man['l_name'] }}</span>

                                        <span class="text--title font-semibold d-flex align-items-center">
                                            <i class="tio-shopping-basket-outlined mr-2"></i>
                                            {{ $order->delivery_man->orders_count }}
                                            {{ translate('messages.orders_delivered') }}
                                        </span>

                                        <span class="text--title font-semibold d-flex align-items-center">
                                            <i class="tio-call-talking-quiet mr-2"></i>
                                            {{ $order->delivery_man['phone'] }}
                                        </span>

                                        <span class="text--title font-semibold d-flex align-items-center">
                                            <i class="tio-email-outlined mr-2"></i>
                                            {{ $order->delivery_man['email'] }}
                                        </span>
                                    </div>
                                </div>

                                @if ($order['order_type'] != 'take_away')
                                    <hr>
                                    @php($address = $order->dm_last_location)
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5>{{ translate('messages.last_location') }}</h5>
                                    </div>
                                    @if (isset($address))
                                        <span class="d-block">
                                            <a target="_blank"
                                                href="http://maps.google.com/maps?z=12&t=m&q=loc:{{ $address['latitude'] }}+{{ $address['longitude'] }}">
                                                <i class="tio-map"></i> {{ $address['location'] }}<br>
                                            </a>
                                        </span>
                                    @else
                                        <span class="d-block text-lowercase qcont">
                                            {{ translate('messages.location_not_found') }}
                                        </span>
                                    @endif
                                @endif
                            @else
                                <span class="badge badge-soft-danger py-2 d-block qcont">
                                    {{ translate('messages.deliveryman_not_found') }}
                                </span>
                            @endif
                        </div>
                        <!-- End Body -->
                    </div>
                @endif
                <!-- End Card -->

                <!-- order proof -->
                <div class="card mb-2 mt-2">
                    <div class="card-header border-0 text-center pb-0">
                        <h4 class="m-0">{{ translate('messages.delivery_proof') }} </h4>
                        @if ($order['store']['sub_self_delivery'])

                        <button class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                            data-target=".order-proof-modal">
                                            {{ translate('messages.add') }}
                                        </button>
                        @endif
                    </div>
                    @php($data = isset($order->order_proof) ? json_decode($order->order_proof, true) : 0)
                    <div class="card-body pt-2">
                        @if ($data)
                        <label class="input-label"
                            for="order_proof">{{ translate('messages.image') }} : </label>
                        <div class="row g-3">
                                @foreach ($data as $key => $img)
                                @php($img = is_array($img)?$img:['img'=>$img,'storage'=>'public'])
                                    <div class="col-3">
                                        <img class="img__aspect-1 rounded border w-100 onerror-image" data-toggle="modal"
                                            data-target="#imagemodal{{ $key }}"
                                             data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                             src="{{\App\CentralLogics\Helpers::get_full_url('order',$img['img'],$img['storage']) }}"
                                             alt="image">
                                    </div>
                                    <div class="modal fade" id="imagemodal{{ $key }}" tabindex="-1"
                                        role="dialog" aria-labelledby="order_proof_{{ $key }}"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title"
                                                        id="order_proof_{{ $key }}">
                                                        {{ translate('order_proof_image') }}</h4>
                                                    <button type="button" class="close"
                                                        data-dismiss="modal"><span
                                                            aria-hidden="true">&times;</span><span
                                                            class="sr-only">{{ translate('messages.cancel') }}</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <img src="{{\App\CentralLogics\Helpers::get_full_url('order',$img['img'],$img['storage']) }}"
                                                        class="initial--22 w-100" alt="img">
                                                </div>
                                                @php($storage = $img['storage']??'public')
                                                @php($file = $storage == 's3'?base64_encode('order/' . $img['img']):base64_encode('public/order/' . $img['img']))
                                                <div class="modal-footer">
                                                    <a class="btn btn-primary"
                                                        href="{{ route('admin.file-manager.download', [$file,$storage]) }}"><i
                                                            class="tio-download"></i>
                                                        {{ translate('messages.download') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @endif
                    </div>
                </div>

                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header">
                        <h4 class="card-header-title">
                            <span class="card-header-icon"><i class="tio-user"></i></span>
                            <span>{{ translate('messages.customer') }}</span>
                        </h4>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    @if ($order->customer)
                        <div class="card-body">

                            <div class="media align-items-center customer--information-single" href="javascript:">
                                <div class="avatar avatar-circle">
                                    <img class="avatar-img onerror-image "
                                         data-onerror-image="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                         src="{{ $order->customer->image_full_url }}"
                                        alt="Image Description">
                                </div>
                                <div class="media-body">
                                    <span
                                        class="text-body d-block text-hover-primary mb-1">{{ $order->customer['f_name'] . ' ' . $order->customer['l_name'] }}</span>

                                    <span class="text--title font-semibold d-flex align-items-center">
                                        <i class="tio-shopping-basket-outlined mr-2"></i>
                                        {{ $order->customer->orders_count }}
                                        {{ translate('messages.orders_delivered') }}
                                    </span>

                                    <span class="text--title font-semibold d-flex align-items-center">
                                        <i class="tio-call-talking-quiet mr-2"></i>
                                        {{ $order->customer['phone'] }}
                                    </span>

                                    <span class="text--title font-semibold d-flex align-items-center">
                                        <i class="tio-email-outlined mr-2"></i>
                                        {{ $order->customer['email'] }}
                                    </span>

                                </div>
                            </div>
                            <hr>




                            @if ($order->delivery_address)
                                @php($address = json_decode($order->delivery_address, true))
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>{{ translate('messages.delivery_info') }}</h5>
                                </div>
                                @if (isset($address))
                                    <span class="delivery--information-single d-block">
                                        <div class="d-flex">
                                            <span class="name">{{ translate('messages.name') }}:</span>
                                            <span class="info">{{ $address['contact_person_name'] }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <span class="name">{{ translate('messages.contact') }}:</span>
                                            <a class="info deco-none"
                                                href="tel:{{ $address['contact_person_number'] }}">
                                                {{ $address['contact_person_number'] }}</a>
                                        </div>
                                        <div class="d-flex">
                                            <span class="name">{{ translate('Floor') }}:</span>
                                            <span
                                                class="info">{{ isset($address['floor']) ? $address['floor'] : '' }}</span>
                                        </div>

                                        <div class="d-flex mb-2">
                                            <span class="name">{{ translate('House') }}:</span>
                                            <span
                                                class="info">{{ isset($address['house']) ? $address['house'] : '' }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <span class="name">{{ translate('Road') }}:</span>
                                            <span
                                                class="info">{{ isset($address['road']) ? $address['road'] : '' }}</span>
                                        </div>
                                        @if ($order['order_type'] != 'take_away' && isset($address['address']))
                                            @if (isset($address['latitude']) && isset($address['longitude']))
                                                <a target="_blank"
                                                    href="http://maps.google.com/maps?z=12&t=m&q=loc:{{ $address['latitude'] }}+{{ $address['longitude'] }}">
                                                    <i class="tio-map"></i>{{ $address['address'] }}<br>
                                                </a>
                                            @else
                                                <i class="tio-map"></i>{{ $address['address'] }}<br>
                                            @endif
                                        @endif
                                    </span>
                                @endif
                            @endif
                        </div>

                    @elseif($order->is_guest)
                        <div class="card-body">
                            <span class="badge badge-soft-success py-2 mb-2 d-block qcont">
                                {{ translate('Guest_user') }}
                            </span>
                            @if ($order->delivery_address)
                            @php($address = json_decode($order->delivery_address, true))
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{ translate('messages.delivery_info') }}</h5>
                            </div>
                            @if (isset($address))
                                <span class="delivery--information-single d-block">
                                    <div class="d-flex">
                                        <span class="name">{{ translate('messages.name') }}:</span>
                                        <span class="info">{{ $address['contact_person_name'] }}</span>
                                    </div>
                                    <div class="d-flex">
                                        <span class="name">{{ translate('messages.contact') }}:</span>
                                        <a class="info deco-none"
                                            href="tel:{{ $address['contact_person_number'] }}">
                                            {{ $address['contact_person_number'] }}</a>
                                    </div>
                                    <div class="d-flex">
                                        <span class="name">{{ translate('Floor') }}:</span>
                                        <span
                                            class="info">{{ isset($address['floor']) ? $address['floor'] : '' }}</span>
                                    </div>

                                    <div class="d-flex mb-2">
                                        <span class="name">{{ translate('House') }}:</span>
                                        <span
                                            class="info">{{ isset($address['house']) ? $address['house'] : '' }}</span>
                                    </div>

                                    <div class="d-flex">
                                        <span class="name">{{ translate('Road') }}:</span>
                                        <span
                                            class="info">{{ isset($address['road']) ? $address['road'] : '' }}</span>
                                    </div>

                                    @if ($order['order_type'] != 'take_away' && isset($address['address']))
                                    <hr>
                                        @if (isset($address['latitude']) && isset($address['longitude']))
                                            <a target="_blank"
                                                href="http://maps.google.com/maps?z=12&t=m&q=loc:{{ $address['latitude'] }}+{{ $address['longitude'] }}">
                                                <i class="tio-map"></i>{{ $address['address'] }}<br>
                                            </a>
                                        @else
                                            <i class="tio-map"></i>{{ $address['address'] }}<br>
                                        @endif
                                    @endif
                                </span>
                            @endif
                        @endif

                        </div>
                    @else
                        <div class="card-body">
                            <span class="badge badge-soft-danger py-2 d-block qcont">
                                {{ translate('Customer Not found!') }}
                            </span>
                        </div>
                    @endif
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>
        </div>
        <!-- End Row -->
    </div>



        <!-- Modal -->
        <div class="modal fade order-proof-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="mySmallModalLabel">{{ translate('messages.add_delivery_proof') }}</h5>
                    <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal"
                        aria-label="Close">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                </div>

                <form action="{{ route('vendor.order.add-order-proof', [$order['id']]) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <!-- Input Group -->
                        <div class="flex-grow-1 mx-auto">

                            <div class="d-flex flex-wrap __gap-12px __new-coba" id="coba">
                                @php($proof = isset($order->order_proof) ? json_decode($order->order_proof, true) : 0)
                                @if ($proof)

                                @foreach ($proof as $key => $photo)
                                @php($photo = is_array($photo)?$photo:['img'=>$photo,'storage'=>'public'])

                                            <div class="spartan_item_wrapper min-w-176px max-w-176px">
                                                <img class="img--square"
                                                    src="{{\App\CentralLogics\Helpers::get_full_url('order',$photo['img'],$photo['storage']) }}"
                                                    alt="order image">

                                                <div class="pen spartan_remove_row"><i class="tio-edit"></i></div>
                                                <a href="{{ route('vendor.order.remove-proof-image', ['id' => $order['id'], 'name' => $photo]) }}"
                                                    class="spartan_remove_row"><i class="tio-add-to-trash"></i></a>
                                            </div>
                                        @endforeach
                                @endif
                            </div>
                        </div>
                        <!-- End Input Group -->
                        <div class="text-right mt-2">
                            <button class="btn btn--primary">{{ translate('messages.submit') }}</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- End Modal -->

    <div class="modal fade" id="edit-order-amount" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('messages.update_order_amount') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('vendor.order.update-order-amount') }}" method="POST" class="row">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div class="form-group col-12">
                            <label for="order_amount">{{ translate('messages.order_amount') }}</label>
                            <input id="order_amount" type="number" class="form-control" name="order_amount" min="0"
                                value="{{ round($order['order_amount'] - $order['total_tax_amount']  - $order['additional_charge'] -  $order['delivery_charge'] + $order['store_discount_amount'] - $order['dm_tips'] ,6) }}" step=".01">
                        </div>

                        <div class="form-group col-sm-12">
                            <button class="btn btn-sm btn-primary"
                                type="submit">{{ translate('messages.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit-discount-amount" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('messages.update_discount_amount') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('vendor.order.update-discount-amount') }}" method="POST" class="row">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div class="form-group col-12">
                            <label for="discount_amount">{{ translate('messages.discount_amount') }}</label>
                            <input type="number" id="discount_amount" class="form-control" name="discount_amount" min="0"
                                value="{{ $order['store_discount_amount'] }}" step=".01">
                        </div>

                        <div class="form-group col-sm-12">
                            <button class="btn btn-sm btn-primary"
                                type="submit">{{ translate('messages.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- End Content -->


@endsection
@push('script_2')
    <script src="{{ asset('public/assets/admin/js/spartan-multi-image-picker.js') }}"></script>
    <script type="text/javascript">
        "use strict";


        $('.self-delivery-warning').on('click',function (event ){
            event.preventDefault();
            toastr.info(
                "{{ translate('messages.Self_Delivery_is_Disable') }}", {
                    CloseButton: true,
                    ProgressBar: true
                });
        });



        $('.cancelled-status').on('click',function (){
            Swal.fire({
                title: '{{ translate('messages.are_you_sure') }}',
                text: '{{ translate('messages.Change status to canceled ?') }}',
                type: 'warning',
                html:
                    `   <select class="form-control js-select2-custom mx-1" name="reason" id="reason">
                    @foreach ($reasons as $r)
                    <option value="{{ $r->reason }}">
                            {{ $r->reason }}
                    </option>
                    @endforeach

                    </select>`,
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: '{{ translate('messages.no') }}',
                confirmButtonText: '{{ translate('messages.yes') }}',
                reverseButtons: true,
                onOpen: function () {
                    $('.js-select2-custom').select2({
                        minimumResultsForSearch: 5,
                        width: '100%',
                        placeholder: "Select Reason",
                        language: "en",
                    });
                }
            }).then((result) => {
                if (result.value) {
                    let reason = document.getElementById('reason').value;
                    location.href = '{!! route('vendor.order.status', ['id' => $order['id'],'order_status' => 'canceled']) !!}&reason='+reason,'{{ translate('Change status to canceled ?') }}';
                }
            })

        });

        $('.order-status-change-alert').on('click',function (){
            let route = $(this).data('url');
            let message = $(this).data('message');
            let verification = $(this).data('verification');
            let processing = $(this).data('processing-time') ?? false;

            if (verification) {
                Swal.fire({
                    title: '{{ translate('Enter order verification code') }}',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#FC6A57',
                    confirmButtonText: '{{ translate('messages.submit') }}',
                    showLoaderOnConfirm: true,
                    preConfirm: (otp) => {
                        location.href = route + '&otp=' + otp;
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                })
            } else if (processing) {
                Swal.fire({
                    title: '{{ translate('messages.Are you sure ?') }}',
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#FC6A57',
                    cancelButtonText: '{{ translate('messages.Cancel') }}',
                    confirmButtonText: '{{ translate('messages.submit') }}',
                    inputPlaceholder: "{{ translate('Enter processing time') }}",
                    input: 'text',
                    html: message + '<br/>'+'<label>{{ translate('Enter Processing time in minutes') }}</label>',
                    inputValue: processing,
                    preConfirm: (processing_time) => {
                        location.href = route + '&processing_time=' + processing_time;
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                })
            } else {
                Swal.fire({
                    title: '{{ translate('messages.Are you sure ?') }}',
                    text: message,
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#FC6A57',
                    cancelButtonText: '{{ translate('messages.No') }}',
                    confirmButtonText: '{{ translate('messages.Yes') }}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        location.href = route;
                    }
                })
            }

        });

        $(function() {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'order_proof[]',
                maxCount: 6-{{ ($order->order_proof && is_array($order->order_proof))?count(json_decode($order->order_proof)):0 }},
                rowHeight: '176px !important',
                groupClassName: 'spartan_item_wrapper min-w-176px max-w-176px',
                maxFileSize: '',
                placeholderImage: {
                    image: "{{ asset('public/assets/admin/img/upload-img.png') }}",
                    width: '176px'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {

                },
                onRenderedPreview: function(index) {

                },
                onRemoveRow: function(index) {

                },
                onExtensionErr: function() {
                    toastr.error(
                        "{{ translate('messages.please_only_input_png_or_jpg_type_file') }}", {
                            CloseButton: true,
                            ProgressBar: true
                        });
                },
                onSizeErr: function() {
                    toastr.error("{{ translate('messages.file_size_too_big') }}", {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
    </script>
@endpush
