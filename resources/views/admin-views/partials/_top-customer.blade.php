<div class="card-header border-0 order-header-shadow">
    <h5 class="card-title d-flex justify-content-between">
        <span>{{ translate('messages.top_customers') }}</span>
    </h5>
    <a href="{{ route('admin.users.customer.list') }}"
        class="fz-12px font-medium text-006AE5">{{ translate('view_all') }}</a>
</div>

<div class="card-body">

    @if (count($top_customers) > 0)
        <div class="top--selling">

            @forelse($top_customers as $key=>$item)
                <a class="grid--card" href="{{ route('admin.users.customer.view', [$item['id']]) }}">
                    <img class="onerror-image"
                        data-onerror-image="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}"
                        src="{{ $item->image_full_url ?? asset('public/assets/admin/img/160x160/img1.jpg') }}">
                    <div class="cont pt-2">
                        <h6 class="mb-1">{{ $item['f_name'] ?? translate('Not exist') }}</h6>
                        <span>{{ $item['phone'] ?? '' }}</span>
                    </div>
                    <div class="ml-auto">
                        <span class="badge badge-soft">{{ translate('Orders') }} : {{ $item['order_count'] }}</span>
                    </div>
                </a>
            @empty
            @endforelse

        </div>
    @else
        <!-- <div class="empty--data">
            <img src="{{ asset('/public/assets/admin/svg/illustrations/empty-state.svg') }}" alt="public">
            <h5>
                {{ translate('no_data_found') }}
            </h5>
        </div> -->
        <div class="empty--data d-flex flex-column align-items-center justify-content-center h-100 w-100">
            <img src="{{ asset('/public/assets/admin/img/no-customer.png') }}" alt="public">
            <h5 class="secondary-clr">
                {{ translate('No customer available') }}
            </h5>
        </div>
    @endif

</div>

<script src="{{ asset('public/assets/admin') }}/js/view-pages/common.js"></script>
