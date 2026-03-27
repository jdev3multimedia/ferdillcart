<div class="modal fade" id="modalForSnapchatPixel" tabindex="-1" aria-labelledby="modalForSnapchatPixel" aria-hidden="true">
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
                                            src="{{ asset('public/assets/admin/img/svg/snapchat.svg') }}"
                                            loading="lazy" alt="">
                                    </div>
                                    <div class="text-dark mb-3">
                                        <h3 class="lh-base">
                                            {{ translate('how_to_get_the_snapchat_pixel_id') }}
                                        </h3>
                                        <p class="opacity-75">
                                            {{ translate('to_get_your_snapchat_pixel_id,_log_in_to_your_snapchat_ads_manager.') }}
                                            {{ translate('click_on_business_in_the_top_bar_and_select_business_details_from_the_dropdown_menu.') }}
                                            {{ translate('from_the_left_hand_menu,_go_to_the_pixels_section.') }}
                                            {{ translate('if_you_have_already_created_a_pixel,_select_it_from_the_available_list') }}
                                            {{ translate('your_pixel_id_will_be_displayed_at_the_top_of_the_page,_copy_it_by_clicking_on_it.') }}
                                        </p>
                                    </div>

                                    <div class="text-dark mb-3">
                                        <h3 class="lh-base">
                                            {{ translate('where_to_use_the_snapchat_pixel_id') }}
                                        </h3>
                                        <p class="opacity-75">
                                            {{ translate('open_the_marketing_tools_feature_in_your_admin_panel_and_follow_the_steps:') }}
                                        </p>
                                        <ol class="d-flex flex-column gap-2 opacity-75">
                                            <li>
                                                {{ translate('go_to_the_snapchat_pixel_id_section_under_marketing_tools.') }}
                                            </li>
                                            <li>
                                                {{ translate('turn_on_the_toggle_button.') }}
                                            </li>
                                            <li>
                                                {{ translate('paste_your_snapchat_pixel_id_into_the_input_box_and_click_submit.') }}
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
