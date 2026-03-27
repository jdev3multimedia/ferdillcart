"use strict";
$(document).ready(function() {

    $('#bonus_type').on('change', function() {
        if($('#bonus_type').val() == 'amount')
        {
            $('#maximum_bonus_amount').attr("readonly","true");
            $('#maximum_bonus_amount').val(null);
            $('#percentage').addClass('d-none');
            $('#cuttency_symbol').removeClass('d-none');
            $('#bonus_amount').prop('max',99999999999);
        }
        else
        {
            $('#maximum_bonus_amount').removeAttr("readonly");
            $('#percentage').removeClass('d-none');
            $('#bonus_amount').prop('max',100);
            $('#cuttency_symbol').addClass('d-none');
        }
    });

    // INITIALIZATION OF FLATPICKR
    // =======================================================
    $('.js-flatpickr').each(function () {
        $.HSCore.components.HSFlatpickr.init($(this));
    });
});

$("#date_from").on("change", function () {
    $('#date_to').attr('min',$(this).val());
});

$("#date_to").on("change", function () {
    $('#date_from').attr('max',$(this).val());
});

$('#reset_btn').click(function(){
    location.reload(true);
})
