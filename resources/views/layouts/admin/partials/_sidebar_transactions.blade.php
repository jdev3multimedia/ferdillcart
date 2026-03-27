<div id="sidebarMain" class="d-none">
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-brand-wrapper justify-content-between">
                <!-- Logo -->
                @php($store_logo = \App\Models\BusinessSetting::where(['key' => 'logo'])->first())
                <a class="navbar-brand" href="{{ route('admin.transactions.store.withdraw_list') }}" aria-label="Front">
                    <img class="navbar-brand-logo initial--36 onerror-image onerror-image"
                        data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                        src="{{ \App\CentralLogics\Helpers::get_full_url('business', $store_logo?->value ?? '', $store_logo?->storage[0]?->value ?? 'public', 'favicon') }}"
                        alt="Logo">
                    <img class="navbar-brand-logo-mini initial--36 onerror-image onerror-image"
                        data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                        src="{{ \App\CentralLogics\Helpers::get_full_url('business', $store_logo?->value ?? '', $store_logo?->storage[0]?->value ?? 'public', 'favicon') }}"
                        alt="Logo">
                </a>
                <!-- End Logo -->

                <!-- Navbar Vertical Toggle -->
                <button type="button"
                    class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                    <i class="tio-clear tio-lg"></i>
                </button>
                <!-- End Navbar Vertical Toggle -->

                <div class="navbar-nav-wrap-content-left">
                    <!-- Navbar Vertical Toggle -->
                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker close">
                        <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip"
                            data-placement="right" title="Collapse"></i>
                        <i class="tio-last-page navbar-vertical-aside-toggle-full-align"
                            data-template='<div class="tooltip d-none d-sm-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->
                </div>

            </div>

            <!-- Content -->
            <div class="navbar-vertical-content bg--005555" id="navbar-vertical-content">
                <form autocomplete="off" class="sidebar--search-form">
                    <div class="search--form-group">
                        <button type="button" class="btn"><i class="tio-search"></i></button>
                        <input autocomplete="false" name="qq" type="text" class="form-control form--control"
                            placeholder="{{ translate('Search Menu...') }}" id="search">

                        <div id="search-suggestions" class="flex-wrap mt-1"></div>
                    </div>
                </form>
                <ul class="navbar-nav navbar-nav-lg nav-tabs">
                    <!-- Business Section-->
                    <li class="nav-item">
                        <small class="nav-subtitle"
                            title="{{ translate('messages.business_section') }}">{{ translate('messages.business_management') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    <!-- withdraw -->
                    @if (\App\CentralLogics\Helpers::module_permission_check('withdraw_list'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/transactions/store/withdraw*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.transactions.store.withdraw_list') }}"
                                title="{{ translate('messages.store_withdraws') }}">
                                <i class="tio-table nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('messages.Withdraw Requests') }}</span>
                            </a>
                        </li>
                    @endif
                    @if (\App\CentralLogics\Helpers::module_permission_check('withdraw_list'))
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/transactions/delivery-man/withdraw*') ? 'active' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.transactions.delivery-man.withdraw_list') }}" title="{{ translate('messages.deliveryman_withdraws') }}">
                            <i class="tio-table nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('deliveryman_withdraws') }}</span>
                        </a>
                    </li>
                    @endif
                    <!-- End withdraw -->
                    @if (\App\CentralLogics\Helpers::module_permission_check('disbursement'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/transactions/store-disbursement*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.transactions.store-disbursement.list', ['status' => 'all']) }}"
                                title="{{ translate('messages.store_disbursement') }}">
                                <i class="tio-wallet-outlined nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('messages.store_disbursement') }}</span>
                            </a>
                        </li>
                    @endif
                    @if (\App\CentralLogics\Helpers::module_permission_check('disbursement'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/transactions/dm-disbursement*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.transactions.dm-disbursement.list', ['status' => 'all']) }}"
                                title="{{ translate('messages.dm_disbursement') }}">
                                <i class="tio-saving-outlined nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('messages.delivery_man_disbursement') }}</span>
                            </a>
                        </li>
                    @endif
                    <!-- account -->
                    @if (\App\CentralLogics\Helpers::module_permission_check('collect_cash'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/transactions/account-transaction*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.transactions.account-transaction.index') }}"
                                title="{{ translate('messages.collect_cash') }}">
                                <i class="tio-money nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('messages.collect_cash') }}</span>
                            </a>
                        </li>
                    @endif
                    <!-- End account -->

                    <!-- provide_dm_earning -->
                    @if (\App\CentralLogics\Helpers::module_permission_check('provide_dm_earning'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/transactions/provide-deliveryman-earnings*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.transactions.provide-deliveryman-earnings.index') }}"
                                title="{{ translate('messages.deliverymen_earning_provide') }}">
                                <i class="tio-send nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Delivery Man Payments') }}</span>
                            </a>
                        </li>
                    @endif
                    <!-- End provide_dm_earning -->

                    @if (\App\CentralLogics\Helpers::module_permission_check('settings'))
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/transactions/withdraw-method*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.transactions.withdraw-method.list') }}"
                                title="{{ translate('messages.withdraw_method') }}">
                                <i class="tio-savings nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('messages.withdraw_method') }}</span>
                            </a>
                        </li>
                    @endif

                    <!-- Report -->
                    @if (\App\CentralLogics\Helpers::module_permission_check('report'))
                        <li class="nav-item">
                            <small class="nav-subtitle"
                                title="{{ translate('messages.report_and_analytics') }}">{{ translate('messages.report_and_analytics') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/transactions/report/day-wise-report') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.transactions.report.day-wise-report') }}"
                                title="{{ translate('messages.transection_report') }}">
                                <span class="tio-chart-pie-1 nav-icon"></span>
                                <span class="text-truncate">{{ translate('messages.transection_report') }}</span>
                            </a>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/transactions/report/item-wise-report') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.transactions.report.item-wise-report') }}"
                                title="{{ translate('messages.item_report') }}">
                                <span class="tio-chart-bar-1 nav-icon"></span>
                                <span class="text-truncate">{{ translate('messages.item_report') }}</span>
                            </a>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/transactions/report/store*') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.transactions.report.store-summary-report') }}"
                                title="{{ translate('messages.store_wise_report') }}">
                                <span class="tio-home nav-icon"></span>
                                <span class="text-truncate">{{ translate('messages.store_wise_report') }}</span>
                            </a>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/transactions/report/expense-report') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.transactions.report.expense-report') }}"
                                title="{{ translate('messages.expense_report') }}">
                                <span class="tio-money nav-icon"></span>
                                <span class="text-truncate">{{ translate('messages.expense_report') }}</span>
                            </a>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/transactions/report/disbursement-report') ? 'active' : '' }}">
                            <a class="nav-link " href="{{ route('admin.transactions.report.disbursement_report') }}"
                                title="{{ translate('messages.disbursement_report') }}">
                                <span class="tio-saving nav-icon"></span>
                                <span class="text-truncate">{{ translate('messages.disbursement_report') }}</span>
                            </a>
                        </li>


                <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/transactions/report/order-report') ? 'active' : '' }}">
                    <a class="nav-link " href="{{ route('admin.transactions.report.order-report') }}" title="{{ translate('messages.order_report') }}">
                        <span class="tio-chart-bar-4 nav-icon"></span>
                        <span class="text-truncate text-capitalize">{{ translate('messages.order_report') }}</span>
                    </a>
                </li>
                <li class="navbar-vertical-aside-has-menu @yield('tax_report')">
                    <a class="nav-link " href="{{ route('admin.transactions.report.getTaxReport') }}" title="{{ translate('Admin_Tax_Report') }}">
                        <span class="tio-albums nav-icon"></span>
                        <span class="text-truncate text-capitalize">{{ translate('Admin_Tax_Report') }}</span>
                    </a>
                </li>
                <li class="navbar-vertical-aside-has-menu @yield('vendor_tax_report')">
                    <a class="nav-link " href="{{ route('admin.transactions.report.vendorWiseTaxes') }}" title="{{ translate('Vendor_Vat_Report') }}">
                        <span class="tio-american-express nav-icon"></span>
                        <span class="text-truncate text-capitalize">{{ translate('Vendor_Vat_Report') }}</span>
                    </a>
                </li>
                <li class="navbar-vertical-aside-has-menu @yield('parcel_tax_report')">
                    <a class="nav-link " href="{{ route('admin.transactions.report.parcel-wise-taxes') }}" title="{{ translate('Parcel_Tax_Report') }}">
                        <span class="tio-american-express nav-icon"></span>
                        <span class="text-truncate text-capitalize">{{ translate('Parcel_Tax_Report') }}</span>
                    </a>
                </li>
                @endif
                    @if (addon_published_status('Rental'))
                        <!-- Rental Report -->
                        @if (\App\CentralLogics\Helpers::module_permission_check('rental_report'))
                            <li class="nav-item">
                                <small class="nav-subtitle"
                                    title="{{ translate('messages.rental_report_and_analytics') }}">{{ translate('messages.rental_report_and_analytics') }}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/transactions/rental/report/transaction-report') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.transactions.rental.report.transaction-report') }}"
                                    title="{{ translate('messages.transection_report') }}">
                                    <span class="tio-chart-pie-1 nav-icon"></span>
                                    <span class="text-truncate">{{ translate('messages.transaction_report') }}</span>
                                </a>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/transactions/rental/report/vehicle-report') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.transactions.rental.report.vehicle-wise-report') }}"
                                    title="{{ translate('messages.vehicle_report') }}">
                                    <span class="tio-chart-bar-1 nav-icon"></span>
                                    <span class="text-truncate">{{ translate('messages.vehicle_report') }}</span>
                                </a>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{!Request::is('admin/transactions/rental/report/provider-wise-taxes*') && Request::is('admin/transactions/rental/report/provider-wise*') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.transactions.rental.report.provider-summary-report') }}"
                                    title="{{ translate('messages.provider_wise_report') }}">
                                    <span class="tio-home nav-icon"></span>
                                    <span
                                        class="text-truncate">{{ translate('messages.provider_wise_report') }}</span>
                                </a>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/transactions/rental/report/trip-report') ? 'active' : '' }}">
                                <a class="nav-link "
                                    href="{{ route('admin.transactions.rental.report.trip-report') }}"
                                    title="{{ translate('messages.trip_report') }}">
                                    <span class="tio-chart-bar-4 nav-icon"></span>
                                    <span
                                        class="text-truncate text-capitalize">{{ translate('messages.trip_report') }}</span>
                                </a>
                            </li>
                            @if (Route::has('admin.transactions.rental.report.getTaxReport'))
                            <li class="navbar-vertical-aside-has-menu @yield('trip_tax_report')">
                                <a class="nav-link " href="{{ route('admin.transactions.rental.report.getTaxReport') }}"
                                    title="{{ translate('Trip_Tax_Report') }}">
                                    <span class="tio-albums nav-icon"></span>
                                    <span class="text-truncate text-capitalize">{{ translate('Trip_Tax_Report') }}</span>
                                </a>
                            </li>
                            <li class="navbar-vertical-aside-has-menu @yield('provider_tax_report')">
                                <a class="nav-link " href="{{ route('admin.transactions.rental.report.providerWiseTaxes') }}"
                                    title="{{ translate('Provider_Vat_Report') }}">
                                    <span class="tio-american-express nav-icon"></span>
                                    <span
                                        class="text-truncate text-capitalize">{{ translate('Provider_Vat_Report') }}</span>
                                </a>
                            </li>
                            @endif
                        @endif
                    @endif
                    <li class="nav-item py-5">

                    </li>

                    @includeIf('layouts.admin.partials._logout_modal')
                </ul>
            </div>
            <!-- End Content -->
        </div>
    </aside>
</div>

<div id="sidebarCompact" class="d-none">

</div>


@push('script_2')
    <script>
        $(window).on('load', function() {
            if ($(".navbar-vertical-content li.active").length) {
                $('.navbar-vertical-content').animate({
                    scrollTop: $(".navbar-vertical-content li.active").offset().top - 150
                }, 10);
            }
        });

        var $rows = $('#navbar-vertical-content li');
        $('#search-sidebar-menu').keyup(function() {
            var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

            $rows.show().filter(function() {
                var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
                return !~text.indexOf(val);
            }).hide();
        });
        $(document).ready(function() {
            const $searchInput = $('#search');
            const $suggestionsList = $('#search-suggestions');
            const $rows = $('#navbar-vertical-content li');
            const $subrows = $('#navbar-vertical-content li ul li');
            const suggestions = [];
            const focusInput = () => updateSuggestions($searchInput.val());
            const hideSuggestions = () => $suggestionsList.slideUp(700);
            const showSuggestions = () => $suggestionsList.slideDown(700);
            let clickSuggestion = function() {
                let suggestionText = $(this).text();
                $searchInput.val(suggestionText);
                hideSuggestions();
                filterItems(suggestionText.toLowerCase());
                updateSuggestions(suggestionText);
            };
            let filterItems = (val) => {
                let unmatchedItems = $rows.show().filter((index, element) => !~$(element).text().replace(
                    /\s+/g, ' ').toLowerCase().indexOf(val));
                let matchedItems = $rows.show().filter((index, element) => ~$(element).text().replace(/\s+/g,
                    ' ').toLowerCase().indexOf(val));
                unmatchedItems.hide();
                matchedItems.each(function() {
                    let $submenu = $(this).find($subrows);
                    let keywordCountInRows = 0;
                    $rows.each(function() {
                        let rowText = $(this).text().toLowerCase();
                        let valLower = val.toLowerCase();
                        let keywordCountRow = rowText.split(valLower).length - 1;
                        keywordCountInRows += keywordCountRow;
                    });
                    if ($submenu.length > 0) {
                        $subrows.show();
                        $submenu.each(function() {
                            let $submenu2 = !~$(this).text().replace(/\s+/g, ' ')
                                .toLowerCase().indexOf(val);
                            if ($submenu2 && keywordCountInRows <= 2) {
                                $(this).hide();
                            }
                        });
                    }
                });
            };
            let updateSuggestions = (val) => {
                $suggestionsList.empty();
                suggestions.forEach(suggestion => {
                    if (suggestion.toLowerCase().includes(val.toLowerCase())) {
                        $suggestionsList.append(
                            `<span class="search-suggestion badge badge-soft-light m-1 fs-14">${suggestion}</span>`
                        );
                    }
                });
                // showSuggestions();
            };
            $searchInput.focus(focusInput);
            $searchInput.on('input', function() {
                updateSuggestions($(this).val());
            });
            $suggestionsList.on('click', '.search-suggestion', clickSuggestion);
            $searchInput.keyup(function() {
                filterItems($(this).val().toLowerCase());
            });
            $searchInput.on('focusout', hideSuggestions);
            $searchInput.on('focus', showSuggestions);
        });
    </script>
@endpush
