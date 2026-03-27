"use strict";
$(document).ready(function() {

    $('#bonus_type').on('change', function() {
        if($('#bonus_type').val() == 'amount')
        {
            $('#maximum_bonus_amount').attr("readonly","true");
            $('#maximum_bonus_amount').val(null);
            $('#percentage').addClass('d-none');
            $('#cuttency_symbol').removeClass('d-none');
        }
        else
        {
            $('#maximum_bonus_amount').removeAttr("readonly");
            $('#percentage').removeClass('d-none');
            $('#cuttency_symbol').addClass('d-none');
        }
    });

    $('#date_from').attr('min',(new Date()).toISOString().split('T')[0]);
    $('#date_to').attr('min',(new Date()).toISOString().split('T')[0]);

    // INITIALIZATION OF SELECT2
    // =======================================================
    $('.js-select2-custom').each(function () {
        let select2 = $.HSCore.components.HSSelect2.init($(this));
    });
});

$("#date_from").on("change", function () {
    $('#date_to').attr('min',$(this).val());
});

$("#date_to").on("change", function () {
    $('#date_from').attr('max',$(this).val());
});
$('#reset_btn').click(function(){
    $('#module_select').val(null).trigger('change');
    $('#store_id').val(null).trigger('change');
    $('#store_wise').show();
    $('#zone_wise').hide();
})
