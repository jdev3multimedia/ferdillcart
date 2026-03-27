<!-- Page Header -->
        <div class="page-header">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                <div class="d-flex gap-2 mb-0">
                    <div class="page-header-icon">
                        <img src="{{ asset('public/assets/admin/img/delivery-man.png') }}" class="w--26" alt="">
                    </div>
                    <div>
                        <h1 class="page-header-title text-break mb-1">
                            <span class="text-dark">
                                {{ translate('messages.deliveryman_preview') }}
                            </span>
                        </h1>

                        <p class="mb-0 fs-12">{{ translate('messages.Join at') }} {{ \App\CentralLogics\Helpers::time_date_format($deliveryMan?->created_at) }}
                        </p>
                    </div>
                </div>

                @if ($deliveryMan?->application_status != 'approved')
                    <div class="btn-container">
                        <a class="btn btn-primary text-capitalize font-weight-medium fs-12" data-toggle="tooltip"
                            data-placement="top" data-original-title="{{ translate('messages.edit') }}"
                            href="{{ route('admin.users.delivery-man.edit', [$deliveryMan['id']]) }}">
                            <i class="tio-edit"></i>
                            {{ translate('messages.edit-information') }}
                        </a>

                        @if ($deliveryMan?->application_status != 'denied')
                            <a class="btn btn-danger text-capitalize font-weight-medium request-alert fs-12"
                                data-url="{{ route('admin.users.delivery-man.application', [$deliveryMan['id'], 'denied']) }}"
                                data-message="{{ translate('messages.you_want_to_deny_this_application') }}"
                                href="javascript:">
                                {{ translate('messages.reject') }}
                            </a>
                        @endif

                        <a class="btn btn-success text-capitalize font-weight-medium request-alert fs-12"
                            data-url="{{ route('admin.users.delivery-man.application', [$deliveryMan['id'], 'approved']) }}"
                            data-message="{{ translate('messages.you_want_to_approve_this_application') }}"
                            href="javascript:">
                            {{ translate('messages.approve') }}
                        </a>
                    </div>
                @endif
            </div>