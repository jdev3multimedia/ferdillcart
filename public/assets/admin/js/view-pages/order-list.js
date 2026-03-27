"use strict";
$(document).ready(function() {

    // INITIALIZATION OF SELECT2
    // =======================================================
    $('.js-select2-custom').each(function () {
        let select2 = $.HSCore.components.HSSelect2.init($(this));
    });

    let zone_id = [];
    $('#zone_ids').on('change', function(){
        if($(this).val())
        {
            zone_id = $(this).val();
        }
        else
        {
            zone_id = [];
        }
    });

    $('.refund-filter').on('change', function(){
        window.location.href = $(this).val();
    });

    $('.filter-button-show').on('click', function(){
        $('#datatableFilterSidebar,.hs-unfold-overlay').show(500)
    });

    $('.filter-button-hide').on('click', function(){
        $('#datatableFilterSidebar,.hs-unfold-overlay').hide(500)
    });


    // INITIALIZATION OF TAGIFY
    // =======================================================
    $('.js-tagify').each(function () {
        let tagify = $.HSCore.components.HSTagify.init($(this));
    });

    $("#date_from").on("change", function () {
        $('#date_to').attr('min',$(this).val());
    });

    $("#date_to").on("change", function () {
        $('#date_from').attr('max',$(this).val());
    });
});
