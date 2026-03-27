<div class="modal fade" id="modalForPinterestPixel" tabindex="-1" aria-labelledby="modalForPinterestPixel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered max-w-655px">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0 d-flex justify-content-end">
                <button type="button" class="close border-0 btn-circle bg-section2 shadow-none" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-20 py-0 mb-30">
                <div class="swiper instruction-carousel pb-3">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="swiper-slide">
                                <div class="">
                                    <div class="d-flex justify-content-center mb-5">
                                        <img height="60"
                                            src="{{ asset('public/assets/admin/img/svg/pinterest.svg') }}"
                                            loading="lazy" alt="">
                                    </div>
                                    <div class="text-dark mb-3">
                                        <h3 class="lh-base">
                                            {{ translate('how_to_get_the_pinterest_tag_id') }}
                                        </h3>
                                        <p class="opacity-75">
                                            {{ translate('to_get_your_pinterest_tag_id,_log_in_to_your_pinterest_ads_manager.') }}
                                            {{ translate('go_to_conversions_management_interface_and_then_click_tag_manager_from_the_left_navigation_bar,_where_you_will_find_your_tag_id.') }}
                                            {{ translate('copy_it_from_there.') }}
                                        </p>
                                    </div>

                                    <div class="text-dark mb-3">
                                        <h3 class="lh-base">
                                            {{ translate('where_to_use_the_pinterest_tag_id') }}
                                        </h3>
                                        <p class="opacity-75">
                                            {{ translate('open_the_marketing_tools_feature_in_your_admin_panel_and_follow_the_steps:') }}
                                        </p>
                                        <ol class="d-flex flex-column gap-2 opacity-75">
                                            <li>
                                                {{ translate('go_to_the_pinterest_tag_id_section_under_marketing_tools.') }}
                                            </li>
                                            <li>
                                                {{ translate('turn_on_the_toggle_button.') }}
                                            </li>
                                            <li>
                                                {{ translate('paste_your_pinterest_tag_id_into_the_input_box_and_click_submit.') }}
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
