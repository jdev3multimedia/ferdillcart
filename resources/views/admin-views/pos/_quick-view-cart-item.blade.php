<div class="modal-header p-0">
    <h4 class="modal-title product-title">
    </h4>
    <button class="close call-when-done" type="button" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="d-flex flex-row">
        @if (config('toggle_veg_non_veg'))
            <span
                class="badge badge-{{ $product->veg ? 'success' : 'danger' }} position-absolute">{{ $product->veg ? translate('messages.veg') : translate('messages.non_veg') }}</span>
        @endif
        @if (isset($stock) && $stock == 0)
        <span class="badge badge-danger position-absolute">{{ translate('messages.Out_of_Stock') }}</span>
        @endif

        <!-- Product gallery-->
        <div class="d-flex align-items-center justify-content-center active">
            <img class="img-responsive initial--30 onerror-image"
            src="{{ $product['image_full_url'] ?? asset('public/assets/admin/img/160x160/img2.jpg') }}"
                data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                data-zoom="{{ asset('storage/app/public/product') }}/{{ $product['image'] }}" alt="Product image"
                width="">
            <div class="cz-image-zoom-pane"></div>
        </div>
        <!-- Product details-->
        <div class="details pl-2">
            <a href="{{ route('admin.item.view', $product->id) }}"
                class="h3 mb-2 product-title text-break">{{ $product->name }}</a>

            @if (isset($product->module_id) && $product->module->module_type == 'food')
                <div class="mb-3 text-dark">
                    <span class="h3 font-weight-normal text-accent mr-1">
                        {{ \App\CentralLogics\Helpers::get_food_price_range($product, true) }}
                    </span>
                    @if ($product->discount > 0 || \App\CentralLogics\Helpers::get_store_discount($product->store))
                        <strike class="initial--18">
                            {{ \App\CentralLogics\Helpers::get_food_price_range($product) }}
                        </strike>
                    @endif
                </div>
            @else
                <div class="mb-3 text-dark">
                    <span class="h3 font-weight-normal text-accent mr-1">
                        {{ \App\CentralLogics\Helpers::get_price_range($product, true) }}
                    </span>
                    @if ($product->discount > 0 || \App\CentralLogics\Helpers::get_store_discount($product->store))
                        <strike class="initial--18">
                            {{ \App\CentralLogics\Helpers::get_price_range($product) }}
                        </strike>
                    @endif
                </div>
            @endif

            @if ($product->discount > 0 || \App\CentralLogics\Helpers::get_store_discount($product->store))
                <div class="mb-3 text-dark">
                    <strong>{{ translate('messages.discount') }} : </strong>
                    <strong
                        id="set-discount-amount">{{ \App\CentralLogics\Helpers::get_product_discount($product) }}</strong>
                </div>
            @endif

        </div>
    </div>
    <div class="row pt-2">
        <div class="col-12">
            <h2>{{ translate('messages.description') }}</h2>
            <span class="d-block text-dark text-break">
                {!! $product->description !!}
            </span>

            @if (in_array($product->module->module_type ,['food','grocery']))
                @if (count($product->nutritions) )
                    <h4 class="mt-2"> {{ translate('messages.Nutrition_Details') }}</h4>
                    <span class="d-block text-dark text-break">
                        @foreach($product->nutritions as $nutrition)
                        {{$nutrition->nutrition}}{{ !$loop->last ? ',' : '.'}}
                        @endforeach
                    </span>
                @endif
                @if (count($product->allergies))
                    <h4 class="mt-2"> {{ translate('messages.Allergie_Ingredients') }}</h4>
                    <span class="d-block text-dark text-break">
                        @foreach($product->allergies as $allergy)
                        {{$allergy->allergy}}{{ !$loop->last ? ',' : '.'}}
                        @endforeach
                    </span>
                @endif
            @endif

            @if (in_array($product->module->module_type ,['pharmacy']))
                @if ($product->generic->pluck('generic_name')->first())
                    <h4 class="mt-2"> {{ translate('generic_name') }}</h4>
                    <span class="d-block text-dark text-break">
                        {{ $product->generic->pluck('generic_name')->first() }}
                    </span>
                @endif
            @endif




            <form id="add-to-cart-form" class="mb-2">
                @csrf
                <input type="hidden" name="id" value="{{ $product->id }}">
                <input type="hidden" name="cart_item_key" value="{{ $item_key }}">

                @if ($product->module->module_type == 'food')
                    @if ($product->food_variations)

                        @php($values = [])
                        @php($selected_variations =  $cart_item['variations'] ?? []  )
                        @php($names = [])
                        @foreach ($selected_variations as $key => $var)
                            @if (isset($var['values']))
                                @php($names[$key] = $var['name'])
                                @foreach ($var['values'] as $k => $item)
                                    @php($values[$key] = $item)
                                @endforeach
                            @endif
                        @endforeach

                        @foreach (json_decode($product->food_variations) as $key => $choice)
                            @if (isset($choice->name) && isset($choice->values))
                                <div class="h3 p-0 pt-2">{{ $choice->name }} <small
                                        class="text-muted initial--18">
                                        ({{ $choice->required == 'on' ? translate('messages.Required') : translate('messages.optional') }})
                                    </small>
                                </div>
                                @if ($choice->min != 0 && $choice->max != 0)
                                    <small class="d-block mb-3">
                                        {{ translate('You_need_to_select_minimum_ ') }} {{ $choice->min }}
                                        {{ translate('to_maximum_ ') }} {{ $choice->max }}
                                        {{ translate('options') }}
                                    </small>
                                @endif

                                <input type="hidden" name="variations[{{ $key }}][min]"
                                    value="{{ $choice->min }}">
                                <input type="hidden" name="variations[{{ $key }}][max]"
                                    value="{{ $choice->max }}">
                                <input type="hidden" name="variations[{{ $key }}][required]"
                                    value="{{ $choice->required }}">
                                <input type="hidden" name="variations[{{ $key }}][name]"
                                    value="{{ $choice->name }}">

                                @foreach ($choice->values as $k => $option)
                                    <div class="form-check form--check d-flex pr-5 mr-5">
                                        <input class="form-check-input"
                                            type="{{ $choice->type == 'multi' ? 'checkbox' : 'radio' }}"
                                            id="choice-option-{{ $key }}-{{ $k }}"
                                            name="variations[{{ $key }}][values][label][]"
                                            value="{{ $option->label }}"
                                            @if (isset($values[$key])) {{ in_array($option->label, $values[$key]) ? 'checked' : '' }} @endif
                                            autocomplete="off">
                                        <label class="form-check-label"
                                            for="choice-option-{{ $key }}-{{ $k }}">{{ Str::limit($option->label, 20, '...') }}</label>
                                        <span
                                            class="ml-auto">{{ \App\CentralLogics\Helpers::format_currency($option->optionPrice) }}</span>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                @else
                    @foreach (json_decode($product->choice_options) as $choice)


                        <div class="h3 p-0 pt-2">{{ $choice->title }}
                        </div>

                        <div class="d-flex justify-content-left flex-wrap">
                            @foreach ($choice->options as $option)
                                <input class="btn-check item-stock-view-update" type="radio" id="{{ $choice->name }}-{{ $option }}"
                                    name="{{ $choice->name }}" value="{{ $option }}"
                                    {{ isset($selected_item) && array_key_exists($choice->name, $selected_item) && ltrim($option)  == $selected_item[$choice?->name] ? 'checked' : (trim($option) == $cart_item[$choice->name]  && !isset($selected_item) ? 'checked' : '') }}
                                    autocomplete="off">
                                <label class="btn btn-sm check-label mx-1 choice-input  text-break"
                                    for="{{ $choice->name }}-{{ $option }}">{{ Str::limit($option, 20, '...') }}</label>
                            @endforeach
                        </div>
                    @endforeach
                @endif
                @if (isset($stock) && $stock !== 0 || !isset($stock) )
                <!-- Quantity + Add to cart -->
                <div class="d-flex justify-content-between">
                    <div class="product-description-label mt-2 text-dark h3">{{ translate('messages.quantity') }}:
                    </div>
                    <div class="product-quantity d-flex align-items-center">
                        <div class="input-group input-group--style-2 pr-3 initial--19">
                            <span class="input-group-btn">
                                <button class="btn btn-number text-dark p--10 decrease-button-cart" type="button" data-type="minus">
                                    <i class="tio-remove  font-weight-bold"></i>
                                </button>
                            </span>
                            <input type="number" name="quantity" readonly
                                class="form-control  text-center cart-qty-field" placeholder="1"
                                value="{{ $cart_item['quantity'] }}" min="1" max="{{   (isset($stock) && $stock > 0) ?   ($product?->maximum_cart_quantity ?  min($stock, $product?->maximum_cart_quantity) : $stock)   :  $product?->maximum_cart_quantity ??  '9999999999' }}">
                            <span class="input-group-btn">
                                <button class="btn btn-number text-dark p--10 increase-button-cart" type="button" data-type="plus" >
                                    <i class="tio-add  font-weight-bold"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                @endif
                @php($add_ons = json_decode($product->add_ons))

                @if (count($add_ons) > 0 && $add_ons[0])
                    <div class="h3 p-0 pt-2">{{ translate('messages.addon') }}
                    </div>
                    <div class="d-flex justify-content-left flex-wrap">
                            @php ( $selected_addons= array_combine($cart_item['add_ons'] ,  $cart_item['add_on_qtys']) )
                        @foreach (\App\Models\AddOn::withoutGlobalScope(\App\Scopes\StoreScope::class)->whereIn('id', $add_ons)->active()->get() as $key => $add_on)



                            <div class="flex-column pb-2">
                                <input type="hidden" name="addon-price{{ $add_on->id }}"
                                    value="{{ $add_on->price }}">
                                <input class="btn-check addon-chek addon-quantity-input-toggle" type="checkbox" id="addon{{ $key }}"
                                     name="addon_id[]"
                                    value="{{ $add_on->id }}"
                                    {{ in_array($add_on->id, $cart_item['add_ons']) ? 'checked' : '' }}
                                    autocomplete="off">
                                <label
                                    class="d-flex align-items-center btn btn-sm check-label mx-1 addon-input text-break"
                                    for="addon{{ $key }}">{{ Str::limit($add_on->name, 20, '...') }} <br>
                                    {{ \App\CentralLogics\Helpers::format_currency($add_on->price) }}</label>
                                <label
                                    class="input-group addon-quantity-input mx-1 shadow bg-white rounded px-1" @if (in_array($add_on->id, $cart_item['add_ons'])) style="visibility:visible;" @endif
                                    for="addon{{ $key }}">
                                    <button class="btn btn-sm h-100 text-dark px-0 decrease-button" data-id="{{ $add_on->id }}"  type="button"
                                        ><i
                                            class="tio-remove  font-weight-bold"></i></button>
                                    <input id="addon_quantity_input{{ $add_on->id }}"   type="number" name="addon-quantity{{ $add_on->id }}"
                                        class="form-control text-center border-0 h-100 " placeholder="1"
                                        value="{{ in_array($add_on->id, $cart_item['add_ons']) ?  $selected_addons[$add_on->id]  : 1 }}"
                                        min="1" max="9999999999" readonly>
                                    <button class="btn btn-sm h-100 text-dark px-0 increase-button" data-id="{{ $add_on->id }}" type="button"
                                        ><i
                                            class="tio-add  font-weight-bold"></i></button>
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (isset($stock) && $stock !== 0 || !isset($stock) )
                <div class="row no-gutters d-none mt-2 text-dark" id="chosen_price_div">
                    <div class="col-2">
                        <div class="product-description-label">{{ translate('messages.Total Price') }}:</div>
                    </div>
                    <div class="col-10">
                        <div class="product-price">
                            <strong id="chosen_price"></strong>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-2">
                    <button class="btn btn--primary h--45px add-To-Cart" type="button">
                        <i class="tio-shopping-cart"></i>
                        {{ translate('messages.Update_To_Cart') }}
                    </button>
                </div>
                @else
                <div class="d-flex justify-content-center mt-2">
                    <button class="btn btn-secondary h--45px" type="button">
                        <i class="tio-shopping-cart"></i>
                        {{ translate('messages.Stock_Out') }}
                    </button>
                </div>

                @endif
            </form>
        </div>
    </div>
</div>
<script src="{{asset('public/assets/admin')}}/js/view-pages/common.js"></script>
<script type="text/javascript">
    "use strict";
    getVariantPrice();
    $('#add-to-cart-form input').on('change', function() {
        getVariantPrice();
    });
</script>

