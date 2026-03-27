

"use strict";
$("#date_from").on("change", function () {
    $('#date_to').attr('min',$(this).val());
});

$("#date_to").on("change", function () {
    $('#date_from').attr('max',$(this).val());
});

$(document).on('ready', function () {
    $('#discount_type').on('change', function() {
     if($('#discount_type').val() === 'amount')
        {
            $('#max_discount').prop("readonly", true).val(0);
        }
        else
        {
            $('#max_discount').removeAttr("readonly");
        }
    });

    $('#coupon_type').on('change', function () {
        if($(this).val() ==='first_order')
        {
            $('#coupon_limit').attr("readonly","true").val(1);
            $('#customer_wise').hide();
        }
        else{
            $('#coupon_limit').val('').removeAttr("readonly");
            $('#customer_wise').show();
        }
        if($(this).val() ==='free_delivery')
        {
            $('#discount_type').prop("disabled", true).val("").trigger("change");
            $('#max_discount').val(0).prop("readonly", true);
            $('#discount').val(0).prop("readonly", true);
        }
        else{
            $('#max_discount').removeAttr("readonly");
            $('#discount').removeAttr("readonly");
            $('#discount_type').removeAttr("disabled").attr("required","true").val('percent');
        }
    });

});
