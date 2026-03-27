<div class="tabs-slide-wrap position-relative">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 mt-3 __gap-12px">
        <div class="js-nav-scroller hs-nav-scroller-horizontal mt-2">
            <!-- Nav -->
            <ul class="nav nav-tabs tabs-inner border-0 nav--tabs nav--pills">
                <li class="nav-item">
                    <a class="nav-link  {{ Request::is('admin/business-settings/business-setup') ?'active':'' }}" href="{{ route('admin.business-settings.business-setup') }}"   aria-disabled="true">{{translate('messages.business_info')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link  {{ Request::is('admin/business-settings/business-setup/payment') ?'active':'' }}" href="{{ route('admin.business-settings.business-setup',  ['tab' => 'payment']) }}"   aria-disabled="true">{{translate('Payment')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/business-settings/business-setup/store') ?'active':'' }}" href="{{ route('admin.business-settings.business-setup',  ['tab' => 'store']) }}"  aria-disabled="true">{{translate('Vendor')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/business-settings/business-setup/order') ?'active':'' }}" href="{{ route('admin.business-settings.business-setup',  ['tab' => 'order']) }}"  aria-disabled="true">{{translate('Order')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/business-settings/business-setup/refund-settings') ?'active':'' }}" href="{{ route('admin.business-settings.business-setup',  ['tab' => 'refund-settings']) }}"  aria-disabled="true">{{translate('Refund')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/business-settings/business-setup/deliveryman') ?'active':'' }}" href="{{ route('admin.business-settings.business-setup',  ['tab' => 'deliveryman']) }}"  aria-disabled="true">{{translate('Deliveryman')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/business-settings/business-setup/customer') ?'active':'' }}" href="{{ route('admin.business-settings.business-setup',  ['tab' => 'customer']) }}"  aria-disabled="true">{{translate('Customer')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/business-settings/business-setup/priority') ?'active':'' }}" href="{{ route('admin.business-settings.business-setup',  ['tab' => 'priority']) }}"  aria-disabled="true">{{translate('Priority Setup')}}</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/business-settings/business-setup/landing-page') ?'active':'' }}" href="{{ route('admin.business-settings.business-setup',  ['tab' => 'landing-page']) }}"  aria-disabled="true">{{translate('messages.landing_page')}}</a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/business-settings/business-setup/disbursement') ?'active':'' }}" href="{{ route('admin.business-settings.business-setup',  ['tab' => 'disbursement']) }}"  aria-disabled="true">{{translate('Disbursement')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/business-settings/business-setup/automated-message') ?'active':'' }}" href="{{ route('admin.business-settings.business-setup',  ['tab' => 'automated-message']) }}"  aria-disabled="true">{{translate('Automated Message')}}</a>
                </li>
            </ul>
            <!-- End Nav -->
             <div class="arrow-area">
                <div class="button-prev top-18 align-items-center">
                    <button type="button"
                        class="btn btn-click-prev mr-auto border-0 btn-primary rounded-circle fs-12 p-2 d-center">
                        <i class="tio-chevron-left fs-24"></i>
                    </button>
                </div>
                <div class="button-next top-18 align-items-center">
                    <button type="button"
                        class="btn btn-click-next ml-auto border-0 btn-primary rounded-circle fs-12 p-2 d-center">
                        <i class="tio-chevron-right fs-24"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- @if (!(Request::is('admin/business-settings/language') || Request::is('admin/business-settings/business-setup/refund-settings') || Request::is('admin/business-settings/business-setup/automated-message')))
        <div class="d-flex flex-wrap justify-content-end align-items-center flex-grow-1">
            <div class="blinkings active">
                <i class="tio-info-outined"></i>
                <div class="business-notes">
                    <h6><img src="{{asset('/public/assets/admin/img/notes.png')}}" alt=""> {{translate('Note')}}</h6>
                    <div>
                        @if (Request::is('admin/business-settings/business-setup/refund-settings'))
                        {{ translate('messages.*If_the_Admin_enables_the_‘Refund_Request_Mode’,_customers_can_request_a_refund.') }}
                        @else
                        {{translate('messages.don’t_forget_to_click_the_‘Save Information’_button_below_to_save_changes.')}}
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif -->
    </div>
</div>
