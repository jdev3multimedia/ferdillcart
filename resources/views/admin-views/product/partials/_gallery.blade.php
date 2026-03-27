
@foreach($items as $key=>$item)
    <div class="col-12">
        <div class="card mb-3">
            <!-- Body -->
            <div class="card-body ml-2">
                <div class="table-responsive">
                    <div class="min-width-720">
                    <div class="d-flex">
                        <div>
                            <img class="avatar avatar-xxl avatar-4by3 onerror-image aspect-ratio-1 h-unset"

                            src="{{ $item['image_full_url'] ?? asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                alt="Image Description">
                        </div>
                        <div class="col-10">
                            <div class="d-flex align-items-center justify-content-between">
                                <h4 class="mb-0 ml-4">{{ $item?->getRawOriginal('name') }} </h4>
                                <div>
                                    <a target="_blank" href="{{ route('admin.item.edit',['id' => $item->id , 'product_gellary' => true ]) }}" class="btn btn--sm btn-outline-primary">
                                            {{ translate('messages.use_this_product_info') }}
                                    </a>
                                </div>
                            </div>
                            <table class="table table-borderless table-thead-bordered m-0">
                                <tbody>
                                    <tr>
                                        <td class="px-4 max-w--220px product-gallery-info">
                                            <h6 class="m-0 text-capitalize">{{ translate('General_Information') }}</h6>
                                        </td>
                                        <td class="px-4 product-gallery-info">
                                            <h6 class="m-0 text-capitalize">{{ translate('Available_Variations') }}</h6>
                                        </td>
                                        <td>
                                            <h6 class="m-0 text-capitalize">{{ translate('tags') }}</h6>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 max-w--220px product-gallery-info">
                                            <span class="d-block mb-1">
                                                <span>{{ translate('messages.Category') }}</span>
                                                <span>:</span>
                                                <strong>{{ Str::limit(($item?->category?->parent ? $item?->category?->parent?->name : $item?->category?->name )  ?? translate('messages.uncategorize')
                                                    , 20, '...') }}</strong>
                                            </span>
                                            <span class="d-block mb-1">
                                                <span>{{ translate('messages.Sub_Category') }}</span>
                                                <span>:</span>
                                                <strong>{{ Str::limit(($item?->category?->name )  ?? translate('messages.uncategorize')
                                                    , 20, '...') }}</strong>
                                            </span>
                                            @if ($item->module->module_type == 'grocery')
                                            <span class="d-block mb-1">
                                                <span>{{ translate('messages.Is_Organic') }}</span>
                                                <span>:</span>
                                                <strong> {{  $item->organic == 1 ?  translate('messages.yes') : translate('messages.no') }}</strong>
                                            </span>
                                            @endif
                                            @if ($item->module->module_type == 'food')
                                            <span class="d-block mb-1">
                                                <span>{{ translate('messages.Item_type') }} : </span>
                                                <span>:</span>
                                                <strong> {{  $item->veg == 1 ?  translate('messages.veg') : translate('messages.non_veg') }}</strong>
                                            </span>
                                            @else
                                                @if ($item?->unit)
                                                <span class="d-block mb-1">
                                                    <span>{{ translate('messages.Unit') }} : </span>
                                                    <span>:</span>
                                                    <strong> {{ $item?->unit?->unit  }}</strong>
                                                </span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="px-4 product-gallery-info">
                                            @if ($item->module->module_type == 'food')
                                                @if ($item->food_variations && is_array(json_decode($item['food_variations'], true)))
                                                    @foreach (json_decode($item->food_variations, true) as $variation)
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
                                                                    <span>{{ $value['label'] }}</span> <span>:</span>
                                                                    <strong>{{ \App\CentralLogics\Helpers::format_currency($value['optionPrice']) }}</strong>
                                                                </span>
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        @else
                                            @if ($item->variations && is_array(json_decode($item['variations'], true)))
                                                @foreach (json_decode($item['variations'], true) as $variation)
                                                    <span class="d-block mb-1 text-capitalize">
                                                        <span>{{ $variation['type'] }} </span>
                                                        <span>:</span>
                                                        <strong>{{ \App\CentralLogics\Helpers::format_currency($variation['price']) }}</strong>
                                                    </span>
                                                @endforeach
                                            @endif
                                    </td>
                                    @endif

                                        <td>
                                                @foreach($item->tags as $c) {{ $c->tag }}{{ !$loop->last ? ',' : '.'}} @endforeach
                                        </td>

                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                </tr>
                            </tbody>
                            </table>
                        </div>
                        </div>
                        <div>
                            <h6> {{ translate('description') }}:</h6>
                            <P class="m-0"> {{ $item?->getRawOriginal('description') }}</P>
                        </div>
                    </div>
                </div>


            </div>
            <!-- End Body -->
        </div>
    </div>
@endforeach
<script src="{{asset('public/assets/admin')}}/js/view-pages/common.js"></script>
