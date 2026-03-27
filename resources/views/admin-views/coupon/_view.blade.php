 <div class="modal-body">
     <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-15">
         <div>
             <h3 class="title-clr mb-0">{{ $coupon['title'] }}
                 {{ in_array($coupon['coupon_type'], ['free_delivery']) ? translate('messages.Free Delivery') : ($coupon['discount_type'] == 'amount' ? '(' . \App\CentralLogics\Helpers::format_currency($coupon['discount']) . ')' : '(' . $coupon['discount'] . '%)') }}
             </h3>
             <div class="d-flex align-items-center gap-1">
                 <span class="fs-14">{{ translate('Duration:') }}</span>
                 <p class="fs-14 m-0 text-title">
                     {{ \App\CentralLogics\Helpers::date_format($coupon['start_date']) . ' - ' . \App\CentralLogics\Helpers::date_format($coupon['expire_date']) }}
                 </p>
             </div>
         </div>

         @if (!in_array($coupon['coupon_type'], ['free_delivery']))
             <div class="bg-warning-10 py-2 px-3 rounded text-center">
                 <h2 class="mb-0 text_FF7500">
                     {{ $coupon['discount_type'] == 'amount' ? \App\CentralLogics\Helpers::format_currency($coupon['discount']) : $coupon['discount'] . '%' }}
                 </h2>
                 <p class="fs-16 text_FF7500 m-0">{{ translate('Discount') }}</p>
             </div>
         @endif
     </div>
     <!-- <ul class="coupon-details-list d-flex flex-wrap bg-light rounded p-3 mb-3">
         <li class="d-flex flex-sm-nowrap flex-wrap list-none li align-items-center gap-1">
             <span class="fs-14 w-135px d-block min-w-135px">{{ translate('messages.coupon_type') }} </span>
             <span>:</span>
             <span class="fs-14 text-title">{{ translate($coupon['coupon_type']) }}</span>
         </li>
         @if ($coupon['coupon_type'] == 'store_wise')
             <li class="d-flex flex-sm-nowrap flex-wrap list-none align-items-center gap-1">
                 <span class="fs-14 w-135px d-block min-w-135px">{{ translate('Selected Store') }} </span>
                 <span>:</span>
                 <span class="fs-14 text-title">{{ $coupon?->store?->name }}</span>
             </li>
         @elseif(count($zoneData) > 0)
             <li class="d-flex flex-sm-nowrap flex-wrap list-none align-items-center gap-1">
                 <span class="fs-14 w-135px d-block min-w-135px">{{ translate('Selected Zones') }} </span>
                 <span>:</span>
                 <span class="fs-14 text-title">
                     @foreach ($zoneData ?? [] as $zone)
                         {{ $zone->name }} {{ !$loop->last ? ',' : '' }}
                     @endforeach
                 </span>
             </li>


         @endif

         <li class="d-flex flex-sm-nowrap flex-wrap list-none align-items-center gap-1">
             <span class="fs-14 w-135px d-block min-w-135px">{{ translate('Limit for same user') }} </span>
             <span>:</span>
             <span class="fs-14 text-title">{{ $coupon['limit'] }}</span>
         </li>
         <li class="d-flex flex-sm-nowrap flex-wrap list-none align-items-center gap-1">
             <span
                 class="fs-14 w-135px d-block min-w-135px">{{ translate('Max discount') }}({{ \App\CentralLogics\Helpers::currency_symbol() }})
             </span>
             <span>:</span>
             <span
                 class="fs-14 text-title">{{ \App\CentralLogics\Helpers::format_currency($coupon['max_discount']) }}</span>
         </li>
         <li class="d-flex flex-sm-nowrap flex-wrap list-none align-items-center gap-1">
             <span
                 class="fs-14 w-135px d-block min-w-135px">{{ translate('Min purchase') }}({{ \App\CentralLogics\Helpers::currency_symbol() }})
             </span>
             <span>:</span>
             <span
                 class="fs-14 text-title">{{ \App\CentralLogics\Helpers::format_currency($coupon['min_purchase']) }}</span>
         </li>
         <li class="d-flex flex-sm-nowrap flex-wrap list-none gap-1">
             <span class="fs-14 w-135px d-block min-w-135px">{{ translate('selected customer') }} </span>
             <span>:</span>
             <span class="fs-14 text-title">
                 @if ($selectedCustomers == 'all')
                     {{ translate('All customers') }}
                 @else
                     @forelse ($selectedCustomers??[] as $customer)
                         {{ $customer->f_name }} {{ $customer->l_name }} {{ !$loop->last ? ',' : '' }}
                     @empty
                         {{ translate('All customers') }}
                     @endforelse
                 @endif
             </span>
         </li>
     </ul> -->
     <ul class="coupon-details-list d-flex flex-md-nowrap flex-wrap bg-light rounded p-3 mb-3">
        <div class="d-flex flex-column gap-2">
            <li class="d-flex flex-sm-nowrap flex-wrap list-none li align-items-center gap-1">
                <span class="fs-14 w-135px d-block min-w-135px">{{ translate('messages.coupon_type') }} </span>
                <span>:</span>
                <span class="fs-14 text-title">{{ translate($coupon['coupon_type']) }}</span>
            </li>
            @if ($coupon['coupon_type'] == 'store_wise')
                <li class="d-flex flex-sm-nowrap flex-wrap list-none align-items-center gap-1">
                    <span class="fs-14 w-135px d-block min-w-135px">{{ translate('Selected Store') }} </span>
                    <span>:</span>
                    <span class="fs-14 text-title">{{ $coupon?->store?->name }}</span>
                </li>
            @elseif(count($zoneData) > 0)
                <li class="d-flex flex-sm-nowrap flex-wrap list-none align-items-center gap-1">
                    <span class="fs-14 w-135px d-block min-w-135px">{{ translate('Selected Zones') }} </span>
                    <span>:</span>
                    <span class="fs-14 text-title">
                        @foreach ($zoneData ?? [] as $zone)
                            {{ $zone->name }} {{ !$loop->last ? ',' : '' }}
                        @endforeach
                    </span>
                </li>
   
   
            @endif
   
            <li class="d-flex flex-sm-nowrap flex-wrap list-none align-items-center gap-1">
                <span class="fs-14 w-135px d-block min-w-135px">{{ translate('Limit for same user') }} </span>
                <span>:</span>
                <span class="fs-14 text-title">{{ $coupon['limit'] }}</span>
            </li>
        </div>
        <div class="d-flex flex-column gap-2">
            <li class="d-flex flex-sm-nowrap flex-wrap list-none align-items-center gap-1">
                <span
                    class="fs-14 w-135px d-block min-w-135px">{{ translate('Max discount') }}({{ \App\CentralLogics\Helpers::currency_symbol() }})
                </span>
                <span>:</span>
                <span
                    class="fs-14 text-title">{{ \App\CentralLogics\Helpers::format_currency($coupon['max_discount']) }}</span>
            </li>
            <li class="d-flex flex-sm-nowrap flex-wrap list-none align-items-center gap-1">
                <span
                    class="fs-14 w-135px d-block min-w-135px">{{ translate('Min purchase') }}({{ \App\CentralLogics\Helpers::currency_symbol() }})
                </span>
                <span>:</span>
                <span
                    class="fs-14 text-title">{{ \App\CentralLogics\Helpers::format_currency($coupon['min_purchase']) }}</span>
            </li>
            <li class="d-flex flex-sm-nowrap flex-wrap list-none gap-1">
                <span class="fs-14 w-135px d-block min-w-135px">{{ translate('selected customer') }} </span>
                <span>:</span>
                <span class="fs-14 text-title">
                    @if ($selectedCustomers == 'all')
                        {{ translate('All customers') }}
                    @else
                        @forelse ($selectedCustomers??[] as $customer)
                            {{ $customer->f_name }} {{ $customer->l_name }} {{ !$loop->last ? ',' : '' }}
                        @empty
                            {{ translate('All customers') }}
                        @endforelse
                    @endif
                </span>
            </li>
        </div>
     </ul>
     <div class="bg-light rounded p-3">
         <h5 class="title-clr mb-15">{{ translate('messages.Coupon Code') }}</h5>
         <div class="custom-copy-text position-relative h--45px w-100 rounded overflow-hidden">
             <input type="text" id="coupon_code" class="text-inside form-control rounded-0 pe-30"
                 value="{{ $coupon['code'] }}" />
             <span data-id="coupon_code"
                 class="copy-btn copy-to-clipboard bg-primary text-white d-flex align-items-center justify-content-center w-40px h--45px position-absolute end-cus-0 top-50 cursor-pointer text-primary"><i
                     class="tio-copy text-white"></i></span>
         </div>
     </div>
 </div>
