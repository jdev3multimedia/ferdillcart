<div class="card-header border-0 order-header-shadow">
    <h5 class="card-title d-flex justify-content-between">
        <span>
            {{ translate('most rated') }}@if (Config::get('module.current_module_type') == 'food')
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
    @if (count($top_rated_foods) > 0)
        <div class="rated--products">
            @forelse($top_rated_foods as $key=>$item)
                <a href="{{ route('admin.item.view', [$item['id']]) }}">
                    <div class="rated-media d-flex align-items-center">
                        <img src="{{ $item['image_full_url'] ?? asset('public/assets/admin/img/100x100/2.png') }}"
                            class="onerror-image"
                            data-onerror-image="{{ asset('public/assets/admin/img/100x100/2.png') }}"
                            alt="{{ Str::limit($item->name ?? translate('messages.Item deleted!'), 20, '...') }}">
                        <span class="line--limit-1 w-0 flex-grow-1" title="{{ $item?->name }}">
                            {{ Str::limit($item->name ?? translate('messages.Item deleted!'), 20, '...') }}
                        </span>
                        <div>
                            <span class="text-FF6D6D">{{ $item['rating_count'] }} <i class="tio-heart"></i></span>
                        </div>
                    </div>
                </a>
            @empty
            @endforelse
        </div>
    @else
        <!-- <div class="empty--data">
            <img src="{{ asset('/public/assets/admin/img/illustrations/empty-state.svg') }}" alt="public">
            <h5>
                {{ translate('no_data_found') }}
            </h5>
        </div> -->
        <div class="empty--data d-flex flex-column align-items-center justify-content-center h-100 w-100">
            <img src="{{ asset('/public/assets/admin/img/no-items.png') }}" alt="public">
            <h5 class="secondary-clr">
                {{ translate('No stores available') }}
            </h5>
        </div>
    @endif







</div>

<script src="{{ asset('public/assets/admin') }}/js/view-pages/common.js"></script>
