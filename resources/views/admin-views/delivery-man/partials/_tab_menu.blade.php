
@if ($deliveryMan->application_status == 'approved')

<div class="js-nav-scroller hs-nav-scroller-horizontal mt-3">
    <!-- Nav -->
    <ul class="nav nav-tabs nav--pills mb-3 border-0 nav--tabs">
        <li class="nav-item">
            <a class="nav-link {{request()?->tab == 'info' ||  !request()?->tab ? 'active' : ''}}"
                href="{{ route('admin.users.delivery-man.preview', ['id' => $deliveryMan->id, 'tab' => 'info']) }}"
                aria-disabled="true">{{ translate('messages.info') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{request()?->tab == 'transaction' ? 'active' : ''}}"
                href="{{ route('admin.users.delivery-man.preview', ['id' => $deliveryMan->id, 'tab' => 'transaction']) }}"
                aria-disabled="true">{{ translate('messages.transaction') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{request()?->tab == 'order_list' ? 'active' : ''}}"
                href="{{ route('admin.users.delivery-man.preview', ['id' => $deliveryMan->id, 'tab' => 'order_list']) }}"
                aria-disabled="true">{{ translate('messages.order_list') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{request()?->tab == 'conversation' ? 'active' : ''}}"
                href="{{ route('admin.users.delivery-man.preview', ['id' => $deliveryMan->id, 'tab' => 'conversation']) }}"
                aria-disabled="true">{{ translate('messages.conversations') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{request()?->tab == 'disbursement' ? 'active' : ''}}"
                href="{{ route('admin.users.delivery-man.preview', ['id' => $deliveryMan->id, 'tab' => 'disbursement']) }}"
                aria-disabled="true">{{ translate('messages.disbursements') }}</a>
        </li>
        @if ($deliveryMan->earning)
        <li class="nav-item">
            <a class="nav-link {{request()?->tab == 'loyalty-point' ? 'active' : ''}}"
                href="{{ route('admin.users.delivery-man.preview', ['id' => $deliveryMan->id, 'tab' => 'loyalty-point']) }}"
                aria-disabled="true">{{ translate('Loyalty Points') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{request()?->tab == 'referal-earn' ? 'active' : ''}}"
                href="{{ route('admin.users.delivery-man.preview', ['id' => $deliveryMan->id, 'tab' => 'referal-earn']) }}"
                aria-disabled="true">{{ translate('Refer & Earn') }}</a>
        </li>
        @endif
    </ul>
    <!-- End Nav -->
</div>
@endif
