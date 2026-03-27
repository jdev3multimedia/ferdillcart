<!-- Header -->
<div class="card-header">
    <h5 class="card-header-title text-capitalize">
        <i class="tio-star"></i> {{translate('messages.top_rated_items')}}
    </h5>
    <a href="{{ route('vendor.item.list') }}" class="fz-12px font-medium text-006AE5">{{ translate('view_all') }}</a>

</div>
<!-- End Header -->

<!-- Body -->
<div class="card-body">
    @if (count($most_rated_items) > 0)
    <div class="row g-2">
        @foreach($most_rated_items as $key=>$item)
        <div class="col-md-4 col-6">
            <div class="grid-card top--rated-food pb-4 cursor-pointer redirect-url"
                 data-url="{{route('vendor.item.view',[$item['id']])}}">
                <div class="text-center">
                    <img class="rounded onerror-image" src="{{ $item['image_full_url'] }}"
                    data-onerror-image="{{asset('public/assets/admin/img/100x100/2.png')}}" alt="{{Str::limit($item->name??translate('messages.Item deleted!'),20,'...')}}">
                </div>

                <div class="text-center mt-3">
                    <h5 class="name m-0 mb-1">{{Str::limit($item->name??translate('messages.Item deleted!'),20,'...')}}</h5>
                    <div class="rating">
                        <span class="text-warning"><i class="tio-star"></i> {{round($item['avg_rating'],1)}}</span>
                        <span class="text--title">({{$item['rating_count']}}  {{ translate('messages.reviews') }})</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div>

    @else
    <div class="empty--data">
        <img src="{{ asset('/public/assets/admin/svg/illustrations/empty-state.svg') }}" alt="public">
        <h5>
            {{ translate('no_data_found') }}
        </h5>
    </div>

    @endif
</div>
<!-- End Body -->
<script src="{{asset('public/assets/admin')}}/js/view-pages/common.js"></script>
