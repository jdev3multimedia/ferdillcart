@extends('layouts.admin.app')

@section('title', translate('Item Preview'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex flex-wrap justify-content-between">
                <h1 class="page-header-title text-break">
                    <span class="page-header-icon">
                        <img src="{{ asset('public/assets/admin/img/items.png') }}" class="w--22" alt="">
                    </span>
                    <span>{{ $product['name'] }}</span>
                </h1>
                <div>
                    @if (Config::get('module.current_module_type') != 'food')
                        <a data-toggle="modal" data-id="{{ $product->id }}" data-target="#update-quantity"
                            class="btn btn--primary update-quantity">
                            {{ translate('messages.Update_Stock') }}
                        </a>
                    @endif

                    <a href="{{ route('admin.item.edit', [$product['id']]) }}" class="btn btn--primary">
                        <i class="tio-edit"></i> {{ translate('messages.edit_info') }}
                    </a>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row review--information-wrapper g-2 mb-3">
            <div class="col-lg-9">
                <div class="card h-100">
                    <!-- Body -->
                    <div class="card-body">
                        <div class="row align-items-md-center">
                            <div class="col-lg-5 col-md-6 mb-3 mb-md-0">
                                <div class="d-flex flex-wrap align-items-center food--media">
                                    <img class="avatar avatar-xxl avatar-4by3 mr-4 onerror-image"
                                        src="{{ $product['image_full_url'] ?? asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                        data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                        alt="Image Description">
                                    <div class="d-block">
                                        <div class="rating--review">
                                            <h1 class="title">{{ number_format($product->avg_rating, 1) }}<span
                                                    class="out-of">/5</span></h1>


                                            <div class="rating">
                                                @foreach (range(1, 5) as $i)
                                                    <span>
                                                        @if ($product->avg_rating >= $i)
                                                            <i class="tio-star"></i>
                                                        @elseif ($product->avg_rating >= $i - 0.5)
                                                            <i class="tio-star-half"></i>
                                                        @else
                                                            <i class="tio-star-outlined"></i>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>

                                            <div class="info">
                                                <span>{{ translate('messages.of') }} {{ $product->reviews->count() }}
                                                    {{ translate('messages.reviews') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7 col-md-6 mx-auto">
                                <ul class="list-unstyled list-unstyled-py-2 mb-0 rating--review-right py-3">
                                    @php($total = $product->rating ? array_sum(json_decode($product->rating, true)) : 0)
                                    <!-- Review Ratings -->
                                    <li class="d-flex align-items-center font-size-sm">
                                        @php($five = $product->rating ? json_decode($product->rating, true)[5] : 0)
                                        <span class="progress-name mr-3">{{ translate('excellent_') }}</span>
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $total == 0 ? 0 : ($five / $total) * 100 }}%;"
                                                aria-valuenow="{{ $total == 0 ? 0 : ($five / $total) * 100 }}"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="ml-3">{{ $five }}</span>
                                    </li>
                                    <!-- End Review Ratings -->

                                    <!-- Review Ratings -->
                                    <li class="d-flex align-items-center font-size-sm">
                                        @php($four = $product->rating ? json_decode($product->rating, true)[4] : 0)
                                        <span class="progress-name mr-3">{{ translate('good') }}</span>
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $total == 0 ? 0 : ($four / $total) * 100 }}%;"
                                                aria-valuenow="{{ $total == 0 ? 0 : ($four / $total) * 100 }}"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="ml-3">{{ $four }}</span>
                                    </li>
                                    <!-- End Review Ratings -->

                                    <!-- Review Ratings -->
                                    <li class="d-flex align-items-center font-size-sm">
                                        @php($three = $product->rating ? json_decode($product->rating, true)[3] : 0)
                                        <span class="progress-name mr-3">{{ translate('average') }}</span>
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $total == 0 ? 0 : ($three / $total) * 100 }}%;"
                                                aria-valuenow="{{ $total == 0 ? 0 : ($three / $total) * 100 }}"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="ml-3">{{ $three }}</span>
                                    </li>
                                    <!-- End Review Ratings -->

                                    <!-- Review Ratings -->
                                    <li class="d-flex align-items-center font-size-sm">
                                        @php($two = $product->rating ? json_decode($product->rating, true)[2] : 0)
                                        <span class="progress-name mr-3">{{ translate('below_average') }}</span>
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $total == 0 ? 0 : ($two / $total) * 100 }}%;"
                                                aria-valuenow="{{ $total == 0 ? 0 : ($two / $total) * 100 }}"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="ml-3">{{ $two }}</span>
                                    </li>
                                    <!-- End Review Ratings -->

                                    <!-- Review Ratings -->
                                    <li class="d-flex align-items-center font-size-sm">
                                        @php($one = $product->rating ? json_decode($product->rating, true)[1] : 0)
                                        <span class="progress-name mr-3">{{ translate('poor') }}</span>
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $total == 0 ? 0 : ($one / $total) * 100 }}%;"
                                                aria-valuenow="{{ $total == 0 ? 0 : ($one / $total) * 100 }}"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="ml-3">{{ $one }}</span>
                                    </li>
                                    <!-- End Review Ratings -->
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- End Body -->
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        @if ($product->store)
                            <a class="resturant--information-single"
                                href="{{ route('admin.store.view', $product->store_id) }}">
                                <img class="img--120 rounded mx-auto mb-3 onerror-image"
                                    data-onerror-image="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                    src="{{ $product->store->logo_full_url ?? asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                    alt="Image Description">
                                <div class="text-center">
                                    <h5 class="text-capitalize text--title font-semibold text-hover-primary d-block mb-1">
                                        {{ $product->store['name'] }}
                                    </h5>
                                    <span class="text--title">
                                        <i class="tio-poi"></i> {{ $product->store['address'] }}
                                    </span>
                                </div>
                            </a>
                        @else
                            <span class="badge-info">{{ translate('messages.store_deleted') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Description Card Start -->
        <div class="card mb-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless table-thead-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th class="px-4 border-0">
                                    <h4 class="m-0 text-capitalize">{{ translate('short_description') }}</h4>
                                </th>
                                @if (in_array($product->module->module_type, ['food', 'grocery']))
                                    <th class="px-4 border-0">
                                        <h4 class="m-0 text-capitalize">{{ translate('Nutrition') }}</h4>
                                    </th>
                                    <th class="px-4 border-0">
                                        <h4 class="m-0 text-capitalize">{{ translate('Allergy') }}</h4>
                                    </th>
                                @endif
                                @if (Config::get('module.current_module_type') != 'food')
                                    <th class="px-4 border-0">
                                        <h4 class="m-0 text-capitalize">{{ translate('Stock') }}</h4>
                                    </th>
                                @endif

                                @if (in_array($product->module->module_type, ['pharmacy']))
                                    <th class="px-4 border-0">
                                        <h4 class="m-0 text-capitalize">{{ translate('Generic_Name') }}</h4>
                                    </th>
                                @endif

                                <th class="px-4 border-0">
                                    <h4 class="m-0 text-capitalize">{{ translate('price') }}</h4>
                                </th>
                                <th class="px-4 border-0">
                                    <h4 class="m-0 text-capitalize">{{ translate('variations') }}</h4>
                                </th>
                                @if ($product->module->module_type == 'food')
                                    <th class="px-4 border-0">
                                        <h4 class="m-0 text-capitalize">{{ translate('addons') }}</h4>
                                    </th>
                                @endif
                                <th class="px-4 border-0">
                                    <h4 class="m-0 text-capitalize">{{ translate('tags') }}</h4>
                                </th>
                                @if ($productWiseTax)
                                    <th class="px-4 border-0">
                                        <h4 class="m-0 text-capitalize">{{ translate('Tax/Vat') }}</h4>
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-4 max-w--220px">
                                    <div class="">
                                        {!! $product['description'] !!}
                                    </div>
                                </td>
                                @if (in_array($product->module->module_type, ['food', 'grocery']))
                                    <td class="px-4">
                                        @if ($product->nutritions)
                                            @foreach ($product->nutritions as $nutrition)
                                                {{ $nutrition->nutrition }}{{ !$loop->last ? ',' : '.' }}
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class="px-4">
                                        @if ($product->allergies)
                                            @foreach ($product->allergies as $allergy)
                                                {{ $allergy->allergy }}{{ !$loop->last ? ',' : '.' }}
                                            @endforeach
                                        @endif
                                    </td>
                                @endif
                                @if (Config::get('module.current_module_type') != 'food')
                                    <td class="px-4">{{ $product->stock }}</td>
                                @endif
                                @if (in_array($product->module->module_type, ['pharmacy']))
                                    <td class="px-4">
                                        @if ($product->generic->pluck('generic_name')->first())
                                            {{ $product->generic->pluck('generic_name')->first() }}
                                        @endif
                                    </td>

                                @endif
                                <td class="px-4">
                                    <span class="d-block mb-1">
                                        <span>{{ translate('messages.price') }} : </span>
                                        <strong>{{ \App\CentralLogics\Helpers::format_currency($product['price']) }}</strong>
                                    </span>
                                    <span class="d-block mb-1">
                                        <span>{{ translate('messages.discount') }} :</span>

                                        <strong>  {{$product['discount_type'] == 'percent' ? $product['discount'] . ' %' : \App\CentralLogics\Helpers::format_currency($product['discount']) }}   </strong>
                                    </span>
                                    @if (config('module.' . $product->module->module_type)['item_available_time'])
                                        <span class="d-block mb-1">
                                            {{ translate('messages.available_time_starts') }} :
                                            <strong>{{ date(config('timeformat'), strtotime($product['available_time_starts'])) }}</strong>
                                        </span>
                                        <span class="d-block mb-1">
                                            {{ translate('messages.available_time_ends') }} :
                                            <strong>{{ date(config('timeformat'), strtotime($product['available_time_ends'])) }}</strong>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4">
                                    @if ($product->module->module_type == 'food')
                                        @if ($product->food_variations && is_array(json_decode($product['food_variations'], true)))
                                            @foreach (json_decode($product->food_variations, true) as $variation)
                                                @if (isset($variation['price']))
                                                    <span class="d-block mb-1 text-capitalize">
                                                        <strong>
                                                            {{ translate('please_update_the_food_variations.') }}
                                                        </strong>
                                                    </span>
                                                    @break

                                                @else
                                                    <span class="d-block text-capitalize">
                                                        <strong>
                                                            {{ $variation['name'] }} -
                                                        </strong>
                                                        @if ($variation['type'] == 'multi')
                                                            {{ translate('messages.multiple_select') }}
                                                        @elseif($variation['type'] == 'single')
                                                            {{ translate('messages.single_select') }}
                                                        @endif
                                                        @if ($variation['required'] == 'on')
                                                            - ({{ translate('messages.required') }})
                                                        @endif
                                                    </span>

                                                    @if ($variation['min'] != 0 && $variation['max'] != 0)
                                                        ({{ translate('messages.Min_select') }}: {{ $variation['min'] }} -
                                                        {{ translate('messages.Max_select') }}: {{ $variation['max'] }})
                                                    @endif

                                                    @if (isset($variation['values']))
                                                        @foreach ($variation['values'] as $value)
                                                            <span class="d-block text-capitalize">
                                                                &nbsp; &nbsp; {{ $value['label'] }} :
                                                                <strong>{{ \App\CentralLogics\Helpers::format_currency($value['optionPrice']) }}</strong>
                                                            </span>
                                                        @endforeach
                                                    @endif
                                                @endif
                                            @endforeach
                                        @endif
                                    @else
                                        @if ($product->variations && is_array(json_decode($product['variations'], true)))
                                            @foreach (json_decode($product['variations'], true) as $variation)
                                                <span class="d-block mb-1 text-capitalize">
                                                    {{ $variation['type'] }} :
                                                    {{ \App\CentralLogics\Helpers::format_currency($variation['price']) }}
                                                </span>
                                            @endforeach
                                        @endif
                                </td>
                                @endif
                                @if ($product->module->module_type == 'food')

                                    <td class="px-4">
                                        @if (config('module.' . $product->module->module_type)['add_on'])
                                            @foreach (\App\Models\AddOn::whereIn('id', json_decode($product['add_ons'], true))->get() as $addon)
                                                <span class="d-block mb-1 text-capitalize">
                                                    {{ $addon['name'] }} :
                                                    {{ \App\CentralLogics\Helpers::format_currency($addon['price']) }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </td>
                                @endif
                                @if ($product->tags)
                                    <td>
                                        @foreach ($product->tags as $c)
                                            {{ $c->tag }}{{ !$loop->last ? ',' : '.' }}
                                        @endforeach
                                    </td>
                                @endif

                                @if ($productWiseTax)
                                    <td>

                                        <span class="d-block font-size-sm text-body">
                                            @forelse ($product?->taxVats?->pluck('tax.name', 'tax.tax_rate')->toArray() as $key => $tax)
                                                <span> {{ $tax }} : <span class="font-bold">
                                                        ({{ $key }}%)
                                                    </span> </span>
                                                <br>
                                            @empty
                                                <span> {{ translate('messages.no_tax') }} </span>
                                            @endforelse
                                        </span>
                                    </td>
                                @endif

                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Description Card End -->
        <!-- Card -->
        <div class="card">
            <div class="card-header border-0">
                <h4 class="card-title">{{ translate('messages.product_reviews') }}</h4>



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
                        <a id="export-excel" class="dropdown-item"
                            href="{{ route('admin.item.item_wise_reviews_export', ['type' => 'excel', 'store' => $product->store?->name, 'id' => $product['id'], request()->getQueryString()]) }}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                alt="Image Description">
                            {{ translate('messages.excel') }}
                        </a>
                        <a id="export-csv" class="dropdown-item"
                            href="{{ route('admin.item.item_wise_reviews_export', ['type' => 'csv', 'store' => $product->store?->name, 'id' => $product['id'], request()->getQueryString()]) }}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                alt="Image Description">
                            .{{ translate('messages.csv') }}
                        </a>

                    </div>
                </div>



            </div>

            <div class="table-responsive datatable-custom">
                <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap card-table"
                    data-hs-datatables-options='{
                     "columnDefs": [{
                        "targets": [0, 3, 6],
                        "orderable": false
                      }],
                     "order": [],
                     "info": {
                       "totalQty": "#datatableWithPaginationInfoTotalQty"
                     },
                     "search": "#datatableSearch",
                     "entries": "#datatableEntries",
                     "pageLength": 25,
                     "isResponsive": false,
                     "isShowPaging": false,
                     "pagination": "datatablePagination"
                   }'>
                    <thead class="thead-light">
                        <tr>
                            <th>{{ translate('messages.Review_Id') }}</th>
                            <th>{{ translate('messages.reviewer') }}</th>
                            <th>{{ translate('messages.review') }}</th>
                            <th>{{ translate('messages.date') }}</th>
                            <th class="w-20p text-center">{{ translate('messages.restaurant_reply') }}</th>
                            <th>{{ translate('messages.status') }}</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($reviews as $review)
                            <tr>
                                <td>{{ $review->review_id }}</td>
                                <td>
                                    @if ($review->customer)
                                        <a class="d-flex align-items-center"
                                            href="{{ route('admin.customer.view', [$review['user_id']]) }}">
                                            <div class="avatar avatar-circle">
                                                <img class="avatar-img onerror-image"
                                                    data-onerror-image="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                                    width="75" height="75"
                                                    src="{{ $review->customer->image_full_url ?? asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                                    alt="Image Description">
                                            </div>
                                            <div class="ml-3">
                                                <span
                                                    class="d-block h5 text-hover-primary mb-0">{{ $review->customer['f_name'] . ' ' . $review->customer['l_name'] }}
                                                    <i class="tio-verified text-primary" data-toggle="tooltip"
                                                        data-placement="top" title="Verified Customer"></i></span>
                                                <span
                                                    class="d-block font-size-sm text-body">{{ $review->customer->email }}</span>
                                            </div>
                                        </a>
                                    @else
                                        {{ translate('messages.customer_not_found') }}
                                    @endif
                                    <a class="ml-8 text-body"
                                        href="{{ route('admin.order.details', ['id' => $review->order_id]) }}">
                                        {{ translate('Order_ID') }}: {{ $review->order_id }}</a>
                                </td>
                                <td>
                                    <div class="text-wrap mw-400">
                                        <label class="m-0 rating">
                                            {{ $review->rating }} <i class="tio-star"></i>
                                        </label>

                                        <p data-toggle="tooltip" data-placement="left"
                                            data-original-title="{{ $review['comment'] }}" class="line--limit-1">
                                            {{ $review['comment'] }}
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    {{ \App\CentralLogics\Helpers::time_date_format($review->created_at) }}
                                </td>
                                <td>
                                    <p class="text-wrap text-center" data-toggle="tooltip" data-placement="top"
                                        data-original-title="{{ $review?->reply }}">{!! $review->reply ? Str::limit($review->reply, 50, '...') : translate('messages.Not_replied_Yet') !!}</p>
                                </td>
                                <td>
                                    <label class="toggle-switch toggle-switch-sm"
                                        for="reviewCheckbox{{ $review->id }}">
                                        <input type="checkbox" data-id="status-{{ $review['id'] }}"
                                            data-message="{{ $review->status ? translate('messages.you_want_to_hide_this_review_for_customer') : translate('messages.you_want_to_show_this_review_for_customer') }}"
                                            class="toggle-switch-input status_form_alert"
                                            id="reviewCheckbox{{ $review->id }}"
                                            {{ $review->status ? 'checked' : '' }}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                    <form
                                        action="{{ route('admin.item.reviews.status', [$review['id'], $review->status ? 0 : 1]) }}"
                                        method="get" id="status-{{ $review['id'] }}">
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if (count($reviews) === 0)
                <div class="empty--data">
                    <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
                    <h5>
                        {{ translate('no_data_found') }}
                    </h5>
                </div>
            @endif
            <!-- Footer -->
        </div>
        <!-- End Card -->
    </div>

    {{-- Add Quantity Modal --}}
    <div class="modal fade update-quantity-modal" id="update-quantity" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-0">

                    <form action="{{ route('admin.item.stock-update') }}" method="post">
                        @csrf
                        <div class="mt-2 rest-part w-100"></div>
                        <div class="btn--container justify-content-end">
                            <button type="reset" data-dismiss="modal" aria-label="Close"
                                class="btn btn--reset">{{ translate('cancel') }}</button>
                            <button type="submit" id="submit_new_customer"
                                class="btn btn--primary">{{ translate('update_stock') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        "use strict";
        $(".status_form_alert").on("click", function(e) {
            const id = $(this).data('id');
            const message = $(this).data('message');
            e.preventDefault();
            Swal.fire({
                title: '{{ translate('messages.are_you_sure') }}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: '{{ translate('messages.no') }}',
                confirmButtonText: '{{ translate('messages.yes') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#' + id).submit()
                }
            })
        })

        $('.update-quantity').on('click', function() {
            let val = $(this).data('id');
            $.get({
                url: '{{ route('admin.item.get_stock') }}',
                data: {
                    id: val
                },
                dataType: 'json',
                success: function(data) {
                    $('.rest-part').empty().html(data.view);
                    update_qty();
                },
            });
        })

        function update_qty() {
            let total_qty = 0;
            let qty_elements = $('input[name^="stock_"]');
            for (let i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            if (qty_elements.length > 0) {

                $('input[name="current_stock"]').attr("readonly", 'readonly');
                $('input[name="current_stock"]').val(total_qty);
            } else {
                $('input[name="current_stock"]').attr("readonly", false);
            }
        }
    </script>
@endpush
