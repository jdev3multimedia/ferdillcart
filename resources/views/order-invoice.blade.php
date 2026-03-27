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

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;1,400&display=swap');

        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            font-size: 13px;
            line-height: 21px;
            color: #737883;
            background: #f7fbff;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            color: #334257;
            margin: 0;
        }

        * {
            box-sizing: border-box
        }

        :root {
            --base: #006161
        }

        .main-table {
            width: 720px;
            background: #FFFFFF;
            margin: 0 auto;
            padding: 40px;
        }

        .main-table-td {}

        img {
            max-width: 100%;
        }

        .cmn-btn {
            background: var(--base);
            color: #fff;
            padding: 8px 20px;
            display: inline-block;
            text-decoration: none;
        }

        .mb-1 {
            margin-bottom: 5px;
        }

        .mb-2 {
            margin-bottom: 10px;
        }

        .mb-3 {
            margin-bottom: 15px;
        }

        .mb-4 {
            margin-bottom: 20px;
        }

        .mb-5 {
            margin-bottom: 25px;
        }

        hr {
            border-color: rgba(0, 170, 109, 0.3);
            margin: 16px 0
        }

        .border-top {
            border-top: 1px solid rgba(0, 170, 109, 0.3);
            padding: 15px 0 10px;
            display: block;
        }

        .d-block {
            display: block;
        }

        .privacy {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
        }

        .privacy a {
            text-decoration: none;
            color: #334257;
            position: relative;
            margin-left: auto;
            margin-right: auto;
        }

        .privacy a span {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #334257;
            display: inline-block;
            margin: 0 7px;
        }

        .social {
            margin: 15px 0 8px;
            display: block;
        }

        .copyright {
            text-align: center;
            display: block;
        }

        div {
            display: block;
        }

        .text-center {
            text-align: center;
        }

        .text-base {
            color: #006161;
            font-weight: 700
        }

        .font-medium {
            font-family: 500;
        }

        .font-bold {
            font-family: 700;
        }

        a {
            text-decoration: none;
        }

        .bg-section {
            background: #E3F5F1;
        }

        .p-10 {
            padding: 10px;
        }

        .mt-0 {
            margin-top: 0;
        }

        .w-100 {
            width: 100%;
        }

        .order-table {
            padding: 10px;
            background: #fff;
        }

        .order-table tr td {
            vertical-align: top
        }

        .order-table .subtitle {
            margin: 0;
            margin-bottom: 10px;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .bg-section-2 {
            background: #F8F9FB;
        }

        .p-1 {
            padding: 5px;
        }

        .p-2 {
            padding: 10px;
        }

        .px-3 {
            padding-inline: 15px
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .m-0 {
            margin: 0;
        }

        .mail-img-1 {
            width: 140px;
            height: 60px;
            object-fit: contain
        }

        .mail-img-2 {
            max-width: 130px;
            max-height: 45px;
        }

        .mail-img-3 {
            width: 100%;
            height: 172px;
            object-fit: cover
        }

        .social img {
            width: 24px;
        }
    </style>

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
                                    <h3 class="mb-3 mt-0">{{ translate('Order_Info') }}</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table class="order-table w-100">
                                        <tbody>
                                            <tr>
                                                <td style="width:150px">
                                                    <h3 class="subtitle">{{ translate('Order_Summary') }}</h3>
                                                    <div class="d-block">{{ translate('Order') }}# {{ $order->id }}
                                                    </div>
                                                    <div class="d-block">{{ $order->created_at }}</div>
                                                </td>
                                                <td class="px-3" style="width:100px">
                                                    <h3 class="subtitle">{{ translate('Delivery_Address') }}</h3>
                                                    @if ($order->delivery_address)
                                                        @php($address = json_decode($order->delivery_address, true))
                                                        <div class="d-block">
                                                            {{ $address['contact_person_name'] ?? $order->customer?->f_name . ' ' . $order->customer?->l_name }}
                                                        </div>
                                                        <div class="d-block">
                                                            {{ $address['contact_person_number'] ?? null }}
                                                        </div>
                                                        <div class="d-block">
                                                            {{ $address['address'] ?? null }}
                                                        </div>
                                                    @endif
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
                                                $total_shipping_cost = $order->delivery_charge;
                                                $total_discount_on_product = 0;
                                                $extra_discount = 0;
                                                $total_addon_price = 0;
                                                ?>
                                                <td>
                                                    <table class="w-100">
                                                        <thead class="bg-section-2">
                                                            <tr>
                                                                <th class="text-left p-1 px-3">
                                                                    {{ translate('Product') }}</th>
                                                                <th class="text-right p-1 px-3">
                                                                    {{ translate('Price') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if ($order->order_type == 'parcel')
                                                                <tr>
                                                                    <td class="text-left p-2 px-3">
                                                                        {{ Str::limit($order->parcel_category ? $order->parcel_category->name : translate('messages.parcel_category_not_found'), 25, '...') }}
                                                                    </td>
                                                                    <td class="text-right p-2 px-3">
                                                                        <h4>
                                                                            {{ \App\CentralLogics\Helpers::format_currency($order->delivery_charge) }}
                                                                        </h4>
                                                                    </td>
                                                                </tr>
                                                            @else
                                                                @foreach ($order->details as $key => $details)
                                                                    <?php
                                                                    $subtotal = $details['price'] * $details->quantity;
                                                                    $item_details = json_decode($details->item_details, true);
                                                                    ?>
                                                                    <tr>
                                                                        {{-- <td class="text-left p-2">
                                                                    {{ $key+1 }}
                                                                </td> --}}
                                                                        <td class="text-left p-2 px-3">
                                                                            <div style="font-size: 14px;">
                                                                                {{ Str::limit($item_details['name'], 40, '...') }}
                                                                            </div>
                                                                            <br>
                                                                            @if (count(json_decode($details['variation'], true)) > 0)
                                                                                <span style="font-size: 12px;">
                                                                                    {{ translate('messages.variation') }}
                                                                                    :
                                                                                    @foreach (json_decode($details['variation'], true) as $variation)
                                                                                        @if (isset($variation['name']) && isset($variation['values']))
                                                                                            <span
                                                                                                class="d-block text-capitalize">
                                                                                                <strong>{{ $variation['name'] }}
                                                                                                    - </strong>
                                                                                                @foreach ($variation['values'] as $value)
                                                                                                    {{ $value['label'] }}
                                                                                                    @if ($value !== end($variation['values']))
                                                                                                        ,
                                                                                                    @endif
                                                                                                @endforeach
                                                                                            </span>
                                                                                        @else
                                                                                            @if (isset(json_decode($details['variation'], true)[0]))
                                                                                                @foreach (json_decode($details['variation'], true)[0] as $key1 => $variation)
                                                                                                    <div
                                                                                                        class="font-size-sm text-body">
                                                                                                        <span>{{ $key1 }}
                                                                                                            : </span>
                                                                                                        <span
                                                                                                            class="font-weight-bold">{{ $variation }}</span>
                                                                                                    </div>
                                                                                                @endforeach
                                                                                            @endif
                                                                                        @endif
                                                                                    @endforeach
                                                                                </span>
                                                                            @endif
                                                                            @foreach (json_decode($details['add_ons'], true) as $key2 => $addon)
                                                                                @if ($key2 == 0)
                                                                                    <br><span
                                                                                        style="font-size: 12px;"><u>{{ translate('messages.addons') }}
                                                                                        </u></span>
                                                                                @endif
                                                                                <div style="font-size: 12px;">
                                                                                    <span>{{ Str::limit($addon['name'], 20, '...') }}
                                                                                        : </span>
                                                                                    <span class="font-weight-bold">
                                                                                        {{ $addon['quantity'] }} x
                                                                                        {{ \App\CentralLogics\Helpers::format_currency($addon['price']) }}
                                                                                    </span>
                                                                                </div>
                                                                                @php($total_addon_price += $addon['price'] * $addon['quantity'])
                                                                            @endforeach
                                                                            <span>x {{ $details->quantity }}</span>
                                                                        </td>
                                                                        <td class="text-right p-2 px-3">
                                                                            <div>
                                                                                {{ \App\CentralLogics\Helpers::format_currency($subtotal) }}
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                    $sub_total += $details['price'] * $details['quantity'];
                                                                    $total_tax += $details['tax'];
                                                                    $total_discount_on_product += $details['discount'];
                                                                    $total += $subtotal;
                                                                    ?>
                                                                @endforeach
                                                            @endif
                                                            <tr>
                                                                <td colspan="2">
                                                                    <hr class="mt-0">
                                                                    <table class="w-100">
                                                                        @if ($order->order_type != 'parcel')
                                                                            <tr>

                                                                                <td style="width: 40%"></td>
                                                                                <td class="p-1 px-3">
                                                                                    {{ translate('messages.item_price') }}
                                                                                </td>
                                                                                <td class="text-right p-1 px-3">
                                                                                    <?php
                                                                                    if ($order->prescription_order == 1) {
                                                                                        $sub_total = $order->order_amount + $order->store_discount_amount + $order->coupon_discount_amount + $order->ref_bonus_amount - $order->extra_packaging_amount - $order->total_tax_amount - $order->delivery_charge - $order->additional_charge - $order->dm_tips;
                                                                                        $sub_total = max($sub_total, 0);
                                                                                    }
                                                                                    ?>
                                                                                    {{ \App\CentralLogics\Helpers::format_currency($sub_total) }}

                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="width: 40%"></td>
                                                                                <td class="p-1 px-3">
                                                                                    {{ translate('messages.addon_cost') }}
                                                                                </td>
                                                                                <td class="text-right p-1 px-3">
                                                                                    {{ \App\CentralLogics\Helpers::format_currency($total_addon_price) }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="width: 40%"></td>
                                                                                <td class="p-1 px-3">
                                                                                    {{ translate('messages.subtotal') }}
                                                                                    @if ($order->tax_status == 'included')
                                                                                        ({{ translate('messages.TAX_Included') }})
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-right p-1 px-3">
                                                                                    {{ \App\CentralLogics\Helpers::format_currency($sub_total + $total_addon_price) }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="width: 40%"></td>
                                                                                <td class="p-1 px-3">
                                                                                    {{ translate('messages.discount') }}
                                                                                </td>
                                                                                <td class="text-right p-1 px-3">
                                                                                    {{ \App\CentralLogics\Helpers::format_currency($order->store_discount_amount) }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="width: 40%"></td>
                                                                                <td class="p-1 px-3">
                                                                                    {{ translate('messages.coupon_discount') }}
                                                                                </td>
                                                                                <td class="text-right p-1 px-3">
                                                                                    {{ \App\CentralLogics\Helpers::format_currency($order->coupon_discount_amount) }}
                                                                                </td>
                                                                            </tr>
                                                                            @if ($order?->ref_bonus_amount > 0)
                                                                                <tr>
                                                                                    <td style="width: 40%"></td>
                                                                                    <td class="p-1 px-3">
                                                                                        {{ translate('messages.Referral_Discount') }}
                                                                                    </td>
                                                                                    <td class="text-right p-1 px-3">
                                                                                        {{ \App\CentralLogics\Helpers::format_currency($order->ref_bonus_amount) }}
                                                                                    </td>
                                                                                </tr>
                                                                            @endif

                                                                            @if ($order?->extra_packaging_amount > 0)
                                                                                <tr>
                                                                                    <td style="width: 40%"></td>
                                                                                    <td class="p-1 px-3">
                                                                                        {{ translate('messages.Extra_Packaging_Amount') }}
                                                                                    </td>
                                                                                    <td class="text-right p-1 px-3">
                                                                                        {{ \App\CentralLogics\Helpers::format_currency($order->extra_packaging_amount) }}
                                                                                    </td>
                                                                                </tr>
                                                                            @endif
                                                                        @endif

                                                                        @if (($order->tax_status == 'excluded' && $order->total_tax_amount > 0) || $order->tax_status == null)
                                                                            <tr>
                                                                                <td style="width: 40%"></td>
                                                                                <td class="p-1 px-3">
                                                                                    {{ translate('messages.tax') }}
                                                                                </td>
                                                                                <td class="text-right p-1 px-3">
                                                                                    {{ \App\CentralLogics\Helpers::format_currency($order->total_tax_amount) }}
                                                                                </td>
                                                                            </tr>
                                                                        @else
                                                                            <tr>
                                                                                <td style="width: 40%"></td>
                                                                                <td class="p-1 px-3">
                                                                                    {{ translate('messages.tax') }}
                                                                                </td>
                                                                                <td class="text-right p-1 px-3">
                                                                                    {{ \App\CentralLogics\Helpers::format_currency($total_tax) }}
                                                                                </td>
                                                                            </tr>
                                                                        @endif

                                                                        @if ($order->order_type != 'parcel')
                                                                            <tr>
                                                                                <td style="width: 40%"></td>
                                                                                <td class="p-1 px-3">
                                                                                    {{ translate('messages.delivery_charge') }}
                                                                                </td>
                                                                                <td class="text-right p-1 px-3">
                                                                                    {{ \App\CentralLogics\Helpers::format_currency($order->delivery_charge) }}
                                                                                </td>
                                                                            </tr>
                                                                        @endif


                                                                        <tr>
                                                                            <td style="width: 40%"></td>
                                                                            <td class="p-1 px-3">
                                                                                {{ translate('messages.delivery_man_tips') }}
                                                                            </td>
                                                                            <td class="text-right p-1 px-3">
                                                                                {{ \App\CentralLogics\Helpers::format_currency($order->dm_tips) }}
                                                                            </td>
                                                                        </tr>



                                                                        <tr>
                                                                            <td style="width: 40%"></td>
                                                                            <td class="p-1 px-3">
                                                                                <h4>{{ translate('messages.total') }}
                                                                                    {{ $order->order_type == 'parcel' && $order->tax_status == 'included' ? '(' . translate('messages.TAX_Included') . ')' : '' }}
                                                                                </h4>
                                                                            </td>
                                                                            <td class="text-right p-1 px-3">
                                                                                <span
                                                                                    class="text-base">{{ \App\CentralLogics\Helpers::format_currency($order->order_amount) }}</span>
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
