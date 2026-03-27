<!DOCTYPE html>
<?php
$lang = \App\CentralLogics\Helpers::system_default_language();
$site_direction = \App\CentralLogics\Helpers::system_default_direction();
?>
<html lang="{{ $lang }}" class="{{ $site_direction === 'rtl' ? 'active' : '' }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ translate('Email_Template') }}</title>

    <link rel="stylesheet" href="{{asset('Modules/Rental/public/assets/css/admin/google-font.css')}}">
    <link rel="stylesheet" href="{{asset('Modules/Rental/public/assets/css/admin/trip-invoice.css')}}">
</head>


<body>

    <table dir="{{ $site_direction }}" class="main-table" style="min-width: 720px">
        <tbody>
            <tr>
                <td class="main-table-td">

                    <table class="bg-section p-10 w-100">
                        <tbody>
                            <tr>
                                <td class="p-10" style="text-align:center">
                                    <img class="mb-2 mail-img-2"
                                        src="{{ \App\CentralLogics\Helpers::get_full_url('business', $logo?->value ?? '', $logo?->storage[0]?->value ?? 'public', 'favicon') }}"
                                        alt="">
                                    <h3 class="mb-3 mt-0">{{ translate('Trip_Info') }}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table class="order-table w-100">
                                        <tbody>
                                            <tr>
                                                <td style="width:150px">
                                                    <h3 class="subtitle">{{ translate('Trip_Summary') }}</h3>
                                                    <div class="d-block">{{ translate('Trip') }}# {{ $trip->id }}
                                                    </div>
                                                    <div class="d-block">{{ \App\CentralLogics\Helpers::time_date_format($trip->schedule_at)	 }} {{ $trip->scheduled ? '('. translate('messages.scheduled') .')' : '' }} </div>
                                                    <div class="text-break mb-1">
                                                        <span class="opacity-70">{{ translate('messages.pickup_location') }}</span> <span>:</span>
                                                        <span>{{ $trip?->pickup_location['location_name'] }}</span>
                                                    </div>
                                                    <div class="text-break mb-1">
                                                        <span class="opacity-70">{{ translate('messages.destination_location') }}</span> <span>:</span>
                                                        <span>{{ $trip?->destination_location['location_name'] }}</span>
                                                    </div>
                                                </td>

                                                <td class="px-3" style="width:100px">
                                                    <h3 class="subtitle">{{ translate('User Info') }}</h3>
                                                    @php($address = $trip->user_info)
                                                    <div class="d-block">
                                                        {{ $address['contact_person_name'] ?? $trip?->customer?->f_name . ' ' . $trip?->customer?->l_name }}
                                                    </div>
                                                    <div class="d-block">
                                                        {{ $address['contact_person_number'] ?? $trip?->customer?->phone }}
                                                    </div>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="order-table w-100">
                                        <tbody>
                                            <tr>
                                                <?php
                                                $subtotal = 0;
                                                $total = 0;
                                                $sub_total = 0;
                                                $total_tax = 0;
                                                $total_shipping_cost = $trip->delivery_charge;
                                                $total_discount_on_product = 0;
                                                $extra_discount = 0;
                                                $total_addon_price = 0;
                                                ?>
                                                <td>
                                                    <table class="w-100">
                                                        <thead class="bg-section-2">
                                                            <tr>
                                                                <th class="text-left p-1 px-3">{{ translate('#') }}
                                                                </th>
                                                                <th class="text-left p-1 px-3">
                                                                    {{ translate('Vehicle') }}
                                                                </th>
                                                                <th class="text-left p-1 px-3">
                                                                    {{ translate('Hour/Km/Day') }}
                                                                </th>
                                                                <th class="text-right p-1 px-3">{{ translate('Fare') }}
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>


                                                            @foreach ($trip->trip_details as $key => $details)
                                                                <?php
                                                                $subtotal += $details['calculated_price'] ;
                                                                $item_details = $details->vehicle_details;
                                                                ?>
                                                                <tr>
                                                                    <td class="text-left p-1 px-3">
                                                                        {{ $key + 1 }}
                                                                    </td>
                                                                    <td class="text-left p-2 px-3">
                                                                        <span style="font-size: 14px;">
                                                                            {{ Str::limit($item_details['name'], 40, '...') }}
                                                                        </span>
                                                                        <br>

                                                                        <span>x {{ $details->quantity }}</span>
                                                                    </td>
                                                                    <td class=" p-2 px-3">


                                                                        <?php
                                                                            if($details->rental_type == 'hourly'){
                                                                                $getPrice=$details->vehicle_details['hourly_price'];
                                                                                $getType=$trip->estimated_hours.' '.translate('Hrs');
                                                                            }elseif ($details->rental_type == 'day_wise') {
                                                                                $getPrice=$details->vehicle_details['day_wise_price'];
                                                                                $getType=( (int) round($details->estimated_hours/ 24) ).' '.translate('Days');
                                                                            } else{
                                                                                $getPrice=$details->vehicle_details['distance_price'];
                                                                                $getType= $trip->distance .' '.translate('KM');
                                                                            }
                                                                        ?>

                                                                        {{ \App\CentralLogics\Helpers::format_currency($getPrice) }}  x   {{ $getType }}
                                                                    </td>
                                                                    <td class="text-right p-2 px-3">
                                                                        <h4>
                                                                            {{ \App\CentralLogics\Helpers::format_currency($details['calculated_price'] ) }}
                                                                        </h4>
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                            <tr>
                                                                <td colspan="4">
                                                                    <hr class="mt-0">
                                                                    <table class="w-100">

                                                                        <tr>
                                                                            <td style="width: 40%"></td>
                                                                            <td class="p-1 px-3">
                                                                                {{ translate('messages.price') }}
                                                                            </td>
                                                                            <td class="text-right p-1 px-3">
                                                                                {{ \App\CentralLogics\Helpers::format_currency($subtotal) }}
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td style="width: 40%"></td>
                                                                            <td class="p-1 px-3">
                                                                                {{ translate('messages.subtotal') }}
                                                                                @if ($trip->tax_status == 'included')
                                                                                    ({{ translate('messages.TAX_Included') }})
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-right p-1 px-3">
                                                                                {{ \App\CentralLogics\Helpers::format_currency($subtotal) }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="width: 40%"></td>
                                                                            <td class="p-1 px-3">
                                                                                {{ translate('messages.discount') }}
                                                                            </td>
                                                                            <td class="text-right p-1 px-3">
                                                                                {{ \App\CentralLogics\Helpers::format_currency($trip->discount_on_trip) }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="width: 40%"></td>
                                                                            <td class="p-1 px-3">
                                                                                {{ translate('messages.coupon_discount') }}
                                                                            </td>
                                                                            <td class="text-right p-1 px-3">
                                                                                {{ \App\CentralLogics\Helpers::format_currency($trip->coupon_discount_amount) }}
                                                                            </td>
                                                                        </tr>
                                                                        @if ($trip?->ref_bonus_amount > 0)
                                                                            <tr>
                                                                                <td style="width: 40%"></td>
                                                                                <td class="p-1 px-3">
                                                                                    {{ translate('messages.Referral_Discount') }}
                                                                                </td>
                                                                                <td class="text-right p-1 px-3">
                                                                                    {{ \App\CentralLogics\Helpers::format_currency($trip->ref_bonus_amount) }}
                                                                                </td>
                                                                            </tr>
                                                                        @endif



                                                                        @if ($trip->tax_status == 'excluded' || $trip->tax_status == null)
                                                                            <tr>
                                                                                <td style="width: 40%"></td>
                                                                                <td class="p-1 px-3">
                                                                                    {{ translate('messages.tax') }}
                                                                                </td>
                                                                                <td class="text-right p-1 px-3">
                                                                                    {{ \App\CentralLogics\Helpers::format_currency($trip->tax_amount) }}
                                                                                </td>
                                                                            </tr>
                                                                        @else

                                                                        @endif

                                                                        <tr>
                                                                            <td style="width: 40%"></td>
                                                                            <td class="p-1 px-3">
                                                                                {{ \App\CentralLogics\Helpers::get_business_data('additional_charge_name')??\App\CentralLogics\Helpers::get_business_data('additional_charge_name')??translate('messages.additional_charge') }}
                                                                            </td>
                                                                            <td class="text-right p-1 px-3">
                                                                                {{ \App\CentralLogics\Helpers::format_currency($trip->additional_charge) }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="width: 40%"></td>
                                                                            <td class="p-1 px-3">
                                                                                <h4>{{ translate('messages.total') }}
                                                                                </h4>
                                                                            </td>
                                                                            <td class="text-right p-1 px-3">
                                                                                <span
                                                                                    class="text-base">{{ \App\CentralLogics\Helpers::format_currency($trip->trip_amount) }}</span>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>

                </td>
            </tr>
            <tr>
                <td style="text-align:center">

                    <div class="copyright" style="text-align:center" id="">
                        {{ translate('Please') }}
                        <a class="text-base"
                            href="mailto:{{ $BusinessData['email_address'] }}">{{ translate('contact us') }}</a>
                        {{ translate('for any queries, we’re always happy to help.') }}
                    </div>
                    <div class="copyright" style="text-align:center" id="mail-copyright">
                        {{ $BusinessData['footer_text'] ?? translate('Copyright 2023 6ammart. All right reserved') }}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>


</body>

</html>
