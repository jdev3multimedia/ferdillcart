<div class="d-flex flex-wrap justify-content-between align-items-center mb-5 mt-4 __gap-12px">
    <div class="js-nav-scroller hs-nav-scroller-horizontal mt-2">
        <!-- Nav -->
        <ul class="nav nav-tabs border-0 nav--tabs nav--pills">
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/registration') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','registration']) }}">
                    {{translate('New Store Registration')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/approve') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','approve']) }}">
                    {{translate('New_Store_Approval')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/deny') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','deny']) }}">
                    {{translate('New_Store_Rejection')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/suspend') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','suspend']) }}">
                    {{translate('Account_Suspend')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/unsuspend') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','unsuspend']) }}">
                    {{translate('Account_Unsuspend')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/withdraw-approve') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','withdraw-approve']) }}">
                    {{translate('Withdraw_Approval')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/withdraw-deny') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','withdraw-deny']) }}">
                    {{translate('Withdraw_Rejection')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/campaign-request') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','campaign-request']) }}">
                    {{translate('Campaign Join Request')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/campaign-approve') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','campaign-approve']) }}">
                    {{translate('Campaign_Join_Approval')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/campaign-deny') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','campaign-deny']) }}">
                    {{translate('Campaign_Join_Rejection')}}
                </a>
            </li>

            @if (\App\CentralLogics\Helpers::get_mail_status('product_approval'))
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/product-approved') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','product-approved']) }}">
                    {{translate('Product_approved')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/product-deny') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','product-deny']) }}">
                    {{translate('Product_Rejection')}}
                </a>
            </li>
            @endif

            @if (\App\CentralLogics\Helpers::subscription_check())
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/subscription-successful') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','subscription-successful']) }}">
                    {{translate('Subscription_Successful')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/subscription-renew') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','subscription-renew']) }}">
                    {{translate('Subscription_Renew')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/subscription-shift') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','subscription-shift']) }}">
                    {{translate('Subscription_Shift')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/subscription-cancel') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','subscription-cancel']) }}">
                    {{translate('Subscription_Cancel')}}
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/subscription-deadline') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','subscription-deadline']) }}">
                    {{translate('Subscription_Deadline_Warning')}}
                </a>
            </li> --}}
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/subscription-plan_upadte') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','subscription-plan_upadte']) }}">
                    {{translate('Subscription_Plan_Upadte')}}
                </a>
            </li>
            @endif

            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/advertisement-create') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','advertisement-create']) }}">
                    {{translate('Advertisement_Create_By_Admin')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/advertisement-approved') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','advertisement-approved']) }}">
                    {{translate('Advertisement_Approval')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/advertisement-deny') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','advertisement-deny']) }}">
                    {{translate('Advertisement_Deny')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/advertisement-resume') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','advertisement-resume']) }}">
                    {{translate('Advertisement_Resume')}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/business-settings/email-setup/store/advertisement-pause') ? 'active' : '' }}"
                href="{{ route('admin.business-settings.email-setup', ['store','advertisement-pause']) }}">
                    {{translate('Advertisement_Pause')}}
                </a>
            </li>
        </ul>
        <!-- End Nav -->
    </div>
</div>
