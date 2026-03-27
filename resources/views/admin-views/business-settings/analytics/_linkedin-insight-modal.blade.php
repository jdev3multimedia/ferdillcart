<div class="modal fade" id="modalForLinkedInInsight" tabindex="-1" aria-labelledby="modalForLinkedInInsight"
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
                                            src="{{ asset('public/assets/admin/img/svg/linkedin.svg') }}"
                                            loading="lazy" alt="">
                                    </div>
                                    <div class="text-dark mb-3">
                                        <h3 class="lh-base">
                                            {{ translate('how_to_get_the_linkedin_partner_id') }}
                                        </h3>
                                        <p class="opacity-75">
                                            {{ translate('to_find_your_linkedin_partner_id,_go_to_your_ad_account_in_campaign_manager.') }}
                                            {{ translate('in_the_left_menu,_click_data_and_then_sources.') }}
                                            {{ translate('next_click_on_insight_tag.') }}
                                            {{ translate('after_that,_select_the_i_will_use_a_tag_manager_dropdown_and_copy_your_partner_id_from_the_box_provided.') }}
                                        </p>
                                    </div>

                                    <div class="text-dark mb-3">
                                        <h3 class="lh-base">
                                            {{ translate('where_to_use_the_linkedin_partner_id') }}
                                        </h3>
                                        <p class="opacity-75">
                                            {{ translate('open_the_marketing_tools_feature_in_your_admin_panel_and_follow_the_directions:') }}
                                        </p>
                                        <ol class="d-flex flex-column gap-2 opacity-75">
                                            <li>
                                                {{ translate('go_to_the_linkedin_partner_id_section_under_marketing_tools.') }}
                                            </li>
                                            <li>
                                                {{ translate('turn_on_the_toggle_button.') }}
                                            </li>
                                            <li>
                                                {{ translate('paste_your_linkedin_partner_id_into_the_input_box_and_click_submit.') }}
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
