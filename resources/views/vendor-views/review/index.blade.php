@extends('layouts.vendor.app')

@section('title',translate('messages.Review List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Heading -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/star.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.customers_reviews')}}
                </span>
            </h1>
        </div>
        <!-- Page Heading -->
        <!-- Card -->
        <div class="card">
            @php($store_review_reply = App\Models\BusinessSetting::where('key' , 'store_review_reply')->first()->value ?? 0)
            <div class="card-header flex-wrap py-2 border-0">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h4 class="mb-0">{{ translate('reviews') }}</h4>
                    <span class="badge badge-soft-dark rounded-circle">{{ $reviews->total() }}</span>
                </div>
                <div class="search--button-wrapper justify-content-end">

                    <form class="search-form">
                        <div class="input-group input--group">
                            <input name="search" type="search" value="{{ request()?->search }}" class="form-control h--40px" placeholder="{{ translate('Ex : Search by item name') }}" aria-label="Search here">
                            <button type="submit" class="btn btn--secondary h--40px"><i class="tio-search"></i></button>
                        </div>
                    </form>
                    <!-- Unfold -->
                    <div class="hs-unfold">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle btn export-btn font--sm"
                            href="javascript:;"
                            data-hs-unfold-options="{
                                &quot;target&quot;: &quot;#usersExportDropdown&quot;,
                                &quot;type&quot;: &quot;css-animation&quot;
                            }"
                            data-hs-unfold-target="#usersExportDropdown" data-hs-unfold-invoker="">
                            <i class="tio-download-to mr-1"></i> {{ translate('export') }}
                        </a>

                        <div id="usersExportDropdown"
                            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right hs-unfold-content-initialized hs-unfold-css-animation animated hs-unfold-reverse-y hs-unfold-hidden">

                            <span class="dropdown-header">{{ translate('download_options') }}</span>
                            <a id="export-excel" class="dropdown-item"
                                href="{{ route('vendor.reviewsExport', ['export_type' => 'excel', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin/svg/components/excel.svg') }}"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item"
                                href="{{ route('vendor.reviewsExport', ['export_type' => 'excel', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin/svg/components/placeholder-csv-format.svg') }}"
                                    alt="Image Description">
                                .{{ translate('messages.csv') }}
                            </a>

                        </div>
                    </div>
                    <!-- End Unfold -->
                </div>
                <!-- End Row -->
            </div>
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
                        <th class="border-0">{{translate('messages.#')}}</th>
                        <th class="border-0">{{translate('messages.Review_Id')}}</th>
                        <th class="border-0">{{translate('messages.item')}}</th>
                        <th class="border-0">{{translate('messages.reviewer')}}</th>
                        <th class="border-0">{{translate('messages.review')}}</th>
                        <th class="border-0">{{translate('messages.date')}}</th>
                        <th class="border-0">{{translate('messages.Reply_date')}}</th>
                        @if($store_review_reply == '1')
                            <th class="text-center">{{translate('messages.action')}}</th>
                        @endif
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($reviews as $key=>$review)
                        <tr>
                            <td>{{$key+$reviews->firstItem()}}</td>
                            <td>{{$review->review_id}}</td>
                            <td>
                                @if ($review->item)
                                    <div class="position-relative media align-items-center">
                                        <a class=" text-hover-primary absolute--link" href="{{route('vendor.item.view',[$review->item['id']])}}">
                                            <img class="avatar avatar-lg mr-3  onerror-image"  data-onerror-image="{{asset('public/assets/admin/img/160x160/img1.jpg')}}"
                                                 src="{{ $review->item['image_full_url'] }}" alt="{{$review->item->name}} image">
                                        </a>
                                        <div class="media-body">
                                            <h5 class="text-hover-primary important--link mb-0">{{Str::limit($review->item['name'],10)}}</h5>
                                            <!-- Static -->
                                            <a href="{{route('vendor.order.details',['id'=>$review->order_id])}}"  class="fz--12 text-body important--link">{{ translate('Order ID') }} #{{$review->order_id}}</a>
                                            <!-- Static -->
                                        </div>
                                    </div>
                                @else
                                    {{translate('messages.Food_deleted!')}}
                                @endif
                            </td>
                            <td>
                                @if($review->customer)
                                    <div>
                                        <h5 class="d-block text-hover-primary mb-1">{{Str::limit($review->customer['f_name']." ".$review->customer['l_name'])}} <i
                                                class="tio-verified text-primary" data-toggle="tooltip" data-placement="top"
                                                title="Verified Customer"></i></h5>
                                        <span class="d-block font-size-sm text-body">{{Str::limit($review->customer->phone)}}</span>
                                    </div>
                                @else
                                    {{translate('messages.customer_not_found')}}
                                @endif
                            </td>
                            <td>
                                <div class="text-wrap w-18rem">
                                    <label class="rating">
                                        <i class="tio-star"></i>
                                        <span>{{$review->rating}}</span>
                                    </label>
                                    <p data-toggle="tooltip" data-placement="bottom"
                                       data-original-title="{{ $review?->comment }}" >
                                        {{Str::limit($review['comment'], 80)}}
                                    </p>
                                </div>
                            </td>
                            <td>
                                <span class="d-block">
                                    {{ \App\CentralLogics\Helpers::date_format($review->created_at)  }}
                                </span>
                                <span class="d-block"> {{ \App\CentralLogics\Helpers::time_format($review->created_at)  }}</span>
                            </td>
                            <td>
                                @if ($review->replied_at)
                                    <span class="d-block">
                                        {{ \App\CentralLogics\Helpers::date_format($review->replied_at)  }}
                                    </span>
                                    <span class="d-block"> {{ \App\CentralLogics\Helpers::time_format($review->replied_at)  }}</span>

                                @else
                                    -------
                                @endif
                            </td>
                            @if($store_review_reply == '1')
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a  class="btn btn-sm btn--primary {{ $review->reply ? 'btn-outline-primary' : ''}}" data-toggle="modal" data-target="#reply-{{$review->id}}" title="View Details">
                                            {{ $review->reply ? translate('view_reply') : translate('give_reply')}}
                                        </a>
                                    </div>
                                </td>
                            @endif
                            <div class="modal fade" id="reply-{{$review->id}}">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header pb-4">
                                            <button type="button" class="payment-modal-close btn-close border-0 outline-0 bg-transparent" data-dismiss="modal">
                                                <i class="tio-clear"></i>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="position-relative media align-items-center">
                                                <a class="absolute--link" href="{{route('vendor.item.view',[$review->item['id']])}}">
                                                </a>
                                                <img class="avatar avatar-lg mr-3  onerror-image"  data-onerror-image="{{asset('public/assets/admin/img/160x160/img1.jpg')}}"
                                                     src="{{ $review->item['image_full_url'] }}" alt="{{$review->item->name}} image">
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
                                                @if($review->customer)
                                                    <div>
                                                        <h5 class="d-block text-hover-primary mb-1">{{Str::limit($review->customer['f_name']." ".$review->customer['l_name'])}} <i
                                                                class="tio-verified text-primary" data-toggle="tooltip" data-placement="top"
                                                                title="Verified Customer"></i></h5>
                                                        <span class="d-block font-size-sm text-body">{{$review->comment}}</span>
                                                    </div>
                                                @else
                                                    {{translate('messages.customer_not_found')}}
                                                @endif
                                            </div>
                                            <div class="mt-3">
                                                <form action="{{route('vendor.review-reply',[$review['id']])}}" method="POST">
                                                    @csrf
                                                    <textarea id="reply" name="reply" required class="form-control" cols="30" rows="3" placeholder="{{ translate('Write_your_reply_here') }}">{{ $review->reply ?? '' }}</textarea>
                                                    <div class="mt-3 btn--container justify-content-end">
                                                        <button class="btn btn-primary">{{ $review->reply ? translate('update_reply') : translate('send_reply')}}</button>
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
                @if(count($reviews) !== 0)
                <hr>
                @endif
                <table>
                    <tfoot>
                    {!! $reviews->links() !!}
                    </tfoot>
                </table>
                @if(count($reviews) === 0)
                <div class="empty--data">
                    <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                    <h5>
                        {{translate('no_data_found')}}
                    </h5>
                </div>
                @endif
            </div>
            <!-- End Table -->
        </div>
        <!-- End Card -->
    </div>

@endsection

@push('script_2')
    <script>
        "use strict";
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            let datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

        });
    </script>
@endpush
