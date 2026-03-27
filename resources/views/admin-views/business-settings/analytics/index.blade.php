@extends('layouts.admin.app')

@section('title', translate('Analytics_Script'))

@push('css_or_js')
@endpush



@section('analytics_Script')
    active
@endsection

@section('content')

    <div class="content container-fluid">
        <div class="mb-3 mb-sm-20">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                {{ translate('Marketing_Tool') }}
            </h2>
        </div>

        <div id="info_notes" class="info-notes-bg px-2 py-2 mb-4 rounded fz-11  gap-2 align-items-center d-flex ">
            <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_13899_104013)">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M10.3125 2.53979V1.28979C10.3125 1.11729 10.1725 0.977295 10 0.977295C9.8275 0.977295 9.6875 1.11729 9.6875 1.28979V2.53979C9.6875 2.71229 9.8275 2.85229 10 2.85229C10.1725 2.85229 10.3125 2.71229 10.3125 2.53979Z"
                        fill="#245BD1" />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M5.34578 4.31882L4.47078 3.44382C4.34891 3.32195 4.15078 3.32195 4.02891 3.44382C3.90703 3.5657 3.90703 3.76382 4.02891 3.8857L4.90391 4.7607C5.02578 4.88257 5.22391 4.88257 5.34578 4.7607C5.46766 4.63882 5.46766 4.4407 5.34578 4.31882Z"
                        fill="#245BD1" />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M3.125 9.10229H1.875C1.7025 9.10229 1.5625 9.24229 1.5625 9.41479C1.5625 9.58729 1.7025 9.72729 1.875 9.72729H3.125C3.2975 9.72729 3.4375 9.58729 3.4375 9.41479C3.4375 9.24229 3.2975 9.10229 3.125 9.10229Z"
                        fill="#245BD1" />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M4.90391 14.0688L4.02891 14.9438C3.90703 15.0657 3.90703 15.2638 4.02891 15.3857C4.15078 15.5076 4.34891 15.5076 4.47078 15.3857L5.34578 14.5107C5.46766 14.3888 5.46766 14.1907 5.34578 14.0688C5.22391 13.9469 5.02578 13.9469 4.90391 14.0688Z"
                        fill="#245BD1" />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M14.6539 14.5107L15.5289 15.3857C15.6508 15.5076 15.8489 15.5076 15.9708 15.3857C16.0927 15.2638 16.0927 15.0657 15.9708 14.9438L15.0958 14.0688C14.9739 13.9469 14.7758 13.9469 14.6539 14.0688C14.532 14.1907 14.532 14.3888 14.6539 14.5107Z"
                        fill="#245BD1" />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M16.875 9.72729H18.125C18.2975 9.72729 18.4375 9.58729 18.4375 9.41479C18.4375 9.24229 18.2975 9.10229 18.125 9.10229H16.875C16.7025 9.10229 16.5625 9.24229 16.5625 9.41479C16.5625 9.58729 16.7025 9.72729 16.875 9.72729Z"
                        fill="#245BD1" />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M15.0958 4.7607L15.9708 3.8857C16.0927 3.76382 16.0927 3.5657 15.9708 3.44382C15.8489 3.32195 15.6508 3.32195 15.5289 3.44382L14.6539 4.31882C14.532 4.4407 14.532 4.63882 14.6539 4.7607C14.7758 4.88257 14.9739 4.88257 15.0958 4.7607Z"
                        fill="#245BD1" />
                    <path
                        d="M7.5 16.6023V15.6648C7.5 14.9773 7.1875 14.321 6.625 13.9148C5.25 12.8835 4.375 11.2585 4.375 9.41477C4.375 6.10227 7.25 3.44602 10.625 3.82102C13.2188 4.10227 15.2812 6.16477 15.5938 8.75852C15.8438 10.8835 14.9062 12.7898 13.375 13.9148C12.8125 14.321 12.5 14.9773 12.5 15.6648V16.6023H7.5Z"
                        fill="#BED2FE" />
                    <path
                        d="M7.5 16.2898H12.5V18.2273C12.5 18.5398 12.25 18.7898 11.9375 18.7898H11.25C11.25 19.4773 10.6875 20.0398 10 20.0398C9.3125 20.0398 8.75 19.4773 8.75 18.7898H8.0625C7.75 18.7898 7.5 18.5398 7.5 18.2273V16.2898Z"
                        fill="#245BD1" />
                </g>
                <defs>
                    <clipPath id="clip0_13899_104013">
                        <rect width="20" height="20" fill="white" transform="translate(0 0.664795)" />
                    </clipPath>
                </defs>
            </svg>

            <span id="">
                {{ translate('in_this_page_you_can_add_credentials_to_show_your_analytics_on_the_platform_make_sure_fill_with_proper_data_other_wise_you_can_not_see_the_analytics_properly') }}
            </span>
        </div>

        <div class="row g-3">
            @foreach ($analyticsTools as $tool)
                @php($data = $analyticsData[$tool['key']] ?? null)

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.business-settings.marketing.analyticUpdate') }}" method="post">
                                @csrf
                                <div class="view-details-container">
                                    <div class="d-flex justify-content-between align-items-center gap-3">
                                        <div>
                                            <h2 class="mb-1">{{ translate($tool['title']) }}</h2>
                                            <p class="mb-0 fs-12">
                                                {{ translate('to_know_more_click') }}
                                                <a data-toggle="modal" href="#{{ $tool['modal'] }}"
                                                    class="fw-semibold text-info-dark text-underline text-decoration-underline text-nowrap">
                                                    {{ translate('How it works') }}.
                                                </a>
                                            </p>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <div
                                                class="view-btn order-sm-0 order-3 fz--14px text-primary cursor-pointer text-decoration-underline font-semibold d-flex align-items-center gap-1">
                                                {{ translate('messages.view') }}
                                                <i class="tio-arrow-downward"></i>
                                            </div>
                                            <label class="toggle-switch toggle-switch-sm mb-0">
                                                <input type="checkbox" data-id="{{ $tool['key'] }}-status"
                                                    data-type="toggle"
                                                    data-image-on="{{ asset('public/assets/admin/img/svg/' . $tool['icon']) }}"
                                                    data-image-off="{{ asset('public/assets/admin/img/svg/' . $tool['icon']) }}"
                                                    data-title-on="<strong>{{ translate('turn_on_' . $tool['key']) }}?</strong>"
                                                    data-title-off="<strong>{{ translate('turn_off_' . $tool['key']) }}?</strong>"
                                                    data-text-on="<p>{{ translate('are_you_sure_to_turn_on_the_' . $tool['key']) }}? {{ translate('enable_this_option_to_make_the_marketing_tool_available_for_website_utilization.') }}</p>"
                                                    data-text-off="<p>{{ translate('are_you_sure_to_turn_off_the_' . $tool['key']) }}? {{ translate('disable_this_option_to_make_the_marketing_tool_unavailable_for_website_utilization.') }}</p>"
                                                    class="status toggle-switch-input dynamic-checkbox" name="status"
                                                    id="{{ $tool['key'] }}-status" value="1"
                                                    {{ $data?->is_active == 1 ? 'checked' : '' }}>
                                                <span class="toggle-switch-label text mb-0">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>


                                    <div class="view-details mt-20">
                                        <div class="bg--secondary rounded px-20 py-25">
                                            <div class="form-group m-0">
                                                <label for="" class="fs-14 mb-10px text-title">{{ translate($tool['title'] . '_ID') }}</label>
                                                  <input type="hidden" name="type" value="{{ $tool['key'] }}">
                                                <div class="flex-xs-wrap d-flex align-items-center gap-3">
                                                    <textarea  name="script_id" rows="1" class="form-control" placeholder="{{ translate('Enter the ID') }}">{!! $data?->script_id ?? '' !!}</textarea>
                                                    <button type="{{ env('APP_MODE') != 'demo' ? 'submit' : 'button' }}"
                                                        class="btn py-1 min-w-100px h-40px btn--primary {{ env('APP_MODE') != 'demo' ? '' : 'call-demo-alert' }}"> {{ translate('save') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </form>

                            <form action="{{ route('admin.business-settings.marketing.analyticStatus') }}"
                                id="{{ $tool['key'] }}-status_form" method="get">
                                <input type="hidden" name="type" value="{{ $tool['key'] }}">
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @includeif('admin-views.business-settings.analytics._information-modal')
    @includeif('admin-views.business-settings.analytics._google-analytics-modal')
    @includeif('admin-views.business-settings.analytics._google-tag-manager-modal')
    @includeif('admin-views.business-settings.analytics._linkedin-insight-modal')
    @includeif('admin-views.business-settings.analytics._facebook-meta-pixel-modal')
    @includeif('admin-views.business-settings.analytics._pinterest-tag-modal')
    @includeif('admin-views.business-settings.analytics._snapchat-tag-modal')
    @includeif('admin-views.business-settings.analytics._tiktok-tag-modal')
    @includeif('admin-views.business-settings.analytics._twitter-modal')

@endsection

