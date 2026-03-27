<div class="card-header border-0 order-header-shadow">
    <h5 class="card-title d-flex justify-content-between">
        <span>{{ translate('top selling') }} @if (Config::get('module.current_module_type') == 'food')
                {{ translate('messages.foods') }}
            @else
                {{ translate('messages.items') }}
            @endif
        </span>
    </h5>
    @php($params = session('dash_params'))
    @if ($params['zone_id'] != 'all')
        @php($zone_name = \App\Models\Zone::where('id', $params['zone_id'])->first()->name)
    @else
        @php($zone_name = translate('messages.all'))
    @endif
    <a href="{{ route('admin.item.list') }}" class="fz-12px font-medium text-006AE5">{{ translate('view_all') }}</a>
</div>

<div class="card-body">

    @if (count($top_sell) > 0)
        <div class="top--selling">
            @foreach ($top_sell as $key => $item)
                <a class="grid--card" href="{{ route('admin.item.view', [$item['id']]) }}">
                    <img class="initial--28 onerror-image"
                        src="{{ $item['image_full_url'] ?? asset('public/assets/admin/img/placeholder-2.png') }}"
                        data-onerror-image="{{ asset('public/assets/admin/img/placeholder-2.png') }}"
                        alt="{{ $item->name }} image">
                    <div class="cont pt-2" title="{{ $item?->name }}">
                        <span class="fz--13">{{ Str::limit($item['name'], 20, '...') }}</span>
                    </div>
                    <div class="ml-auto">
                        <span class="badge badge-soft">
                            {{ translate('messages.sold') }} : {{ $item['order_count'] }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <!-- <div class="empty--data">
            <img src="{{ asset('/public/assets/admin/svg/illustrations/empty-state.svg') }}" alt="public">
            <h5>
                {{ translate('no_data_found') }}
            </h5>
        </div> -->
        <div class="empty--data d-flex flex-column align-items-center justify-content-center h-100 w-100">
            <img src="{{ asset('/public/assets/admin/img/no-items.png') }}" alt="public">
            <h5 class="secondary-clr">
                {{ translate('No items available') }}
            </h5>
        </div>
    @endif
</div>

<script src="{{ asset('public/assets/admin') }}/js/view-pages/common.js"></script>
