@extends('layouts.vendor.app')

@section('title', translate('Item Preview'))

@push('css_or_js')
@endpush

@section('content')
    @php($store_data = \App\CentralLogics\Helpers::get_store_data())

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

                    @if ($store_data->module->module_type != 'food')
                        <a data-toggle="modal" data-id="{{ $product->id }}" data-target="#update-quantity"
                            class="btn btn--primary update-quantity">
                            {{ translate('messages.Update_Stock') }}
                        </a>
                    @endif
                    <a href="{{ route('vendor.item.edit', [$product['id']]) }}" class="btn btn--primary">
                        <i class="tio-edit"></i> {{ translate('messages.edit') }}
                    </a>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="review--information-wrapper mb-3">
            <div class="card h-100">
                <!-- Body -->
                <div class="card-body">
                    <div class="row align-items-md-center">
                        <div class="col-lg-5 col-md-6 mb-3 mb-md-0">
                            <div class="d-flex flex-wrap align-items-center food--media">
                                <img class="avatar avatar-xxl avatar-4by3 mr-4 onerror-image"
                                    src="{{ $product['image_full_url'] }}"
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
                                    <span class="progress-name mr-3">{{ translate('excellent') }}</span>
                                    <div class="progress flex-grow-1">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $total == 0 ? 0 : ($five / $total) * 100 }}%;"
                                            aria-valuenow="{{ $total == 0 ? 0 : ($five / $total) * 100 }}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
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
                                            aria-valuenow="{{ $total == 0 ? 0 : ($four / $total) * 100 }}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
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
                                            aria-valuenow="{{ $total == 0 ? 0 : ($three / $total) * 100 }}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
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
                                            aria-valuenow="{{ $total == 0 ? 0 : ($two / $total) * 100 }}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
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
                                            aria-valuenow="{{ $total == 0 ? 0 : ($one / $total) * 100 }}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                    <span class="ml-3">{{ $one }}</span>
                                </li>
                                <!-- End Review Ratings -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Card -->
        @if (\App\CentralLogics\Helpers::get_store_data()->review_permission)
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


                                    @if ($store_data->module->module_type != 'food')
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
                                    @if (\App\CentralLogics\Helpers::get_store_data()->module->module_type == 'food')
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

                                    @if ($product->module->module_type != 'food')
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
                                                            ({{ translate('messages.Min_select') }}:
                                                            {{ $variation['min'] }} -
                                                            {{ translate('messages.Max_select') }}:
                                                            {{ $variation['max'] }})
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
        @if (\App\CentralLogics\Helpers::get_store_data()->module->module_type == 'food')
            <td class="px-4">
                @if (config('module.' . $product->module->module_type)['add_on'])
                    @foreach (\App\Models\AddOn::whereIn('id', json_decode($product['add_ons'], true))->get() as $addon)
                        <span class="d-block mb-1 text-capitalize">
                            {{ $addon['name'] }} : {{ \App\CentralLogics\Helpers::format_currency($addon['price']) }}
                        </span>
                    @endforeach
                @endif
            </td>
        @endif
        @if ($product->tags)
            <td>
                @foreach ($product->tags as $c)
                    {{ $c->tag . ',' }}
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
        @php($store_review_reply = App\Models\BusinessSetting::where('key', 'store_review_reply')->first()->value ?? 0)
        <!-- Table -->
        <div class="table-responsive datatable-custom">
            <table id="columnSearchDatatable"
                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                data-hs-datatables-options='{
                            "order": [],
                            "orderCellsTop": true,
                            "paging": false
                        }'>
                <thead class="thead-light">
                    <tr>
                        <th class="border-0">{{ translate('messages.#') }}</th>
                        <th class="border-0">{{ translate('messages.Review_Id') }}</th>
                        <th class="border-0">{{ translate('messages.item') }}</th>
                        <th class="border-0">{{ translate('messages.reviewer') }}</th>
                        <th class="border-0">{{ translate('messages.review') }}</th>
                        <th class="border-0">{{ translate('messages.date') }}</th>
                        @if ($store_review_reply == '1')
                            <th class="text-center">{{ translate('messages.action') }}</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @foreach ($reviews as $key => $review)
                        <tr>
                            <td>{{ $key + $reviews->firstItem() }}</td>
                            <td>{{ $review->review_id }}</td>
                            <td>
                                @if ($review->item)
                                    <div class="position-relative media align-items-center">
                                        <a class=" text-hover-primary absolute--link"
                                            href="{{ route('vendor.item.view', [$review->item['id']]) }}">
                                            <img class="avatar avatar-lg mr-3  onerror-image"
                                                data-onerror-image="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                                src="{{ $review->item['image_full_url'] }}"
                                                alt="{{ $review->item->name }} image">
                                        </a>
                                        <div class="media-body">
                                            <h5 class="text-hover-primary important--link mb-0">
                                                {{ Str::limit($review->item['name'], 10) }}</h5>
                                            <!-- Static -->
                                            <a href="{{ route('vendor.order.details', ['id' => $review->order_id]) }}"
                                                class="fz--12 text-body important--link">{{ translate('Order ID') }}
                                                #{{ $review->order_id }}</a>
                                            <!-- Static -->
                                        </div>
                                    </div>
                                @else
                                    {{ translate('messages.Food_deleted!') }}
                                @endif
                            </td>
                            <td>
                                @if ($review->customer)
                                    <div>
                                        <h5 class="d-block text-hover-primary mb-1">
                                            {{ Str::limit($review->customer['f_name'] . ' ' . $review->customer['l_name']) }}
                                            <i class="tio-verified text-primary" data-toggle="tooltip"
                                                data-placement="top" title="Verified Customer"></i></h5>
                                        <span
                                            class="d-block font-size-sm text-body">{{ Str::limit($review->customer->phone) }}</span>
                                    </div>
                                @else
                                    {{ translate('messages.customer_not_found') }}
                                @endif
                            </td>
                            <td>
                                <div class="text-wrap w-18rem">
                                    <label class="rating">
                                        <i class="tio-star"></i>
                                        <span>{{ $review->rating }}</span>
                                    </label>
                                    <p data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="{{ $review?->comment }}">
                                        {{ Str::limit($review['comment'], 80) }}
                                    </p>
                                </div>
                            </td>
                            <td>
                                <span class="d-block">
                                    {{ \App\CentralLogics\Helpers::date_format($review->created_at) }}
                                </span>
                                <span class="d-block">
                                    {{ \App\CentralLogics\Helpers::time_format($review->created_at) }}</span>
                            </td>
                            @if ($store_review_reply == '1')
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn btn-sm btn--primary {{ $review->reply ? 'btn-outline-primary' : '' }}"
                                            data-toggle="modal" data-target="#reply-{{ $review->id }}"
                                            title="View Details">
                                            {{ $review->reply ? translate('view_reply') : translate('give_reply') }}
                                        </a>
                                    </div>
                                </td>
                            @endif
                            <div class="modal fade" id="reply-{{ $review->id }}">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header pb-4">
                                            <button type="button"
                                                class="payment-modal-close btn-close border-0 outline-0 bg-transparent"
                                                data-dismiss="modal">
                                                <i class="tio-clear"></i>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="position-relative media align-items-center">
                                                <a class="absolute--link"
                                                    href="{{ route('vendor.item.view', [$review->item['id']]) }}">
                                                </a>
                                                <img class="avatar avatar-lg mr-3  onerror-image"
                                                    data-onerror-image="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                                    src="{{ $review->item['image_full_url'] }}"
                                                    alt="{{ $review->item->name }} image">
                                                <div>
                                                    <h5 class="text-hover-primary mb-0">{{ $review->item['name'] }}</h5>
                                                    @if ($review->item['avg_rating'] == 5)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                        </div>
                                                    @elseif ($review->item['avg_rating'] < 5 && $review->item['avg_rating'] >= 4.5)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-half"></i></span>
                                                        </div>
                                                    @elseif ($review->item['avg_rating'] < 4.5 && $review->item['avg_rating'] >= 4)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->item['avg_rating'] < 4 && $review->item['avg_rating'] >= 3.5)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-half"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->item['avg_rating'] < 3.5 && $review->item['avg_rating'] >= 3)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->item['avg_rating'] < 3 && $review->item['avg_rating'] >= 2.5)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-half"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->item['avg_rating'] < 2.5 && $review->item['avg_rating'] > 2)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->item['avg_rating'] < 2 && $review->item['avg_rating'] >= 1.5)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-half"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->item['avg_rating'] < 1.5 && $review->item['avg_rating'] > 1)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->item['avg_rating'] < 1 && $review->item['avg_rating'] > 0)
                                                        <div class="rating">
                                                            <span><i class="tio-star-half"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->item['avg_rating'] == 1)
                                                        <div class="rating">
                                                            <span><i class="tio-star"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @elseif ($review->item['avg_rating'] == 0)
                                                        <div class="rating">
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                            <span><i class="tio-star-outlined"></i></span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="mt-2">
                                                @if ($review->customer)
                                                    <div>
                                                        <h5 class="d-block text-hover-primary mb-1">
                                                            {{ Str::limit($review->customer['f_name'] . ' ' . $review->customer['l_name']) }}
                                                            <i class="tio-verified text-primary" data-toggle="tooltip"
                                                                data-placement="top" title="Verified Customer"></i></h5>
                                                        <span
                                                            class="d-block font-size-sm text-body">{{ Str::limit($review->comment) }}</span>
                                                    </div>
                                                @else
                                                    {{ translate('messages.customer_not_found') }}
                                                @endif
                                            </div>
                                            <div class="mt-2">
                                                <form action="{{ route('vendor.review-reply', [$review['id']]) }}"
                                                    method="POST">
                                                    @csrf
                                                    <textarea id="reply" name="reply" required class="form-control" cols="30" rows="3"
                                                        placeholder="{{ translate('Write_your_reply_here') }}">{{ $review->reply ?? '' }}</textarea>
                                                    <div class="mt-3 btn--container justify-content-end">
                                                        <button
                                                            class="btn btn-primary">{{ $review->reply ? translate('update_reply') : translate('send_reply') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if (count($reviews) !== 0)
                <hr>
            @endif
            <table>
                <tfoot>
                    {!! $reviews->links() !!}
                </tfoot>
            </table>
            @if (count($reviews) === 0)
                <div class="empty--data">
                    <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
                    <h5>
                        {{ translate('no_data_found') }}
                    </h5>
                </div>
            @endif
        </div>
        <!-- End Table -->
    </div>
    <!-- End Card -->
    @endif
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

                    <form action="{{ route('vendor.item.stock-update') }}" method="post">
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

        $('.update-quantity').on('click', function() {
            let val = $(this).data('id');
            $.get({
                url: '{{ route('vendor.item.get_stock') }}',
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
