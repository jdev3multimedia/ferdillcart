"use strict";
$(document).on('ready', function () {
    $('#min_purchase').data('previous-value', $('#min_purchase').val());
    $('#discount').data('previous-value', $('#discount').val());


    $('#discount_type').on('change', function() {
        discount_check();
    });
    $('#discount').on('click', function() {
        discount_check();
    });


    function discount_check(){
        if($('#discount_type').val() == 'amount')
        {
            $('#max_discount').attr("readonly","true");
            $('#max_discount').val(0);
            $('#discount').attr('max', $('#min_purchase').val() || 0);
            validateDiscount();
        }
        else
        {
            if($('#discount_type').val() == 'percent'){
                $('#max_discount').removeAttr("readonly");
            }
            $('#discount').attr('max', 100);
        }
    }

    let module_id = 0;
    $('#module_select').on('change', function(){
        if($(this).val())
        {
            module_id = $(this).val();
        }
    });
});
$("#date_from").on("change", function () {
    $('#date_to').attr('min',$(this).val());
});

$("#date_to").on("change", function () {
    $('#date_from').attr('max',$(this).val());
});

$('#coupon_type').on('change',function () {
    let coupon_type = $(this).val();
    coupon_type_change(coupon_type)
})

function coupon_type_change(coupon_type) {
    $('#zone_wise, #store_wise, #customer_wise').hide();
    $('#coupon_limit').attr("readonly", false);
     $('#limit_for_same_user').removeClass('d-none');
    switch (coupon_type) {
        case 'zone_wise':
            $('#zone_wise').show();
            break;

        case 'store_wise':
            $('#store_wise').show();
            $('#customer_wise').show();
            break;

        case 'first_order':
            $('#coupon_limit').val(1).attr("readonly", true);
            $('#limit_for_same_user').addClass('d-none');
            break;

        default:
            $('#customer_wise').show();
            $('#coupon_limit').val($('#coupon_limit').data('value')).attr("readonly", false);
            $('#limit_for_same_user').removeClass('d-none');
            break;
    }

    if (coupon_type === 'free_delivery') {
        $('#discount_type').attr("disabled", true).val("").trigger("change");
        $('#max_discount, #discount').val(0).attr("readonly", true);
        $('#discount').removeAttr("required");
        $('#discount').removeAttr("min");
    } else {
        $('#discount_type').removeAttr("disabled").attr("required", true);
        $('#max_discount, #discount').removeAttr("readonly");
        $('#discount').attr("required", true);
        $('#discount').attr("min", 1);
    }

    if ($('#discount_type').val() === 'amount') {
        $('#max_discount').val(0).attr("readonly", true);
    } else if($('#discount_type').val() === 'percent') {
        $('#max_discount').removeAttr("readonly");
    }
}
$('#reset_btn').click(function(){
    location.reload(true);
})
$('#select_customer').on('change', function () {
    let customer = $(this).val();
    if (Array.isArray(customer) && customer.includes("all")) {
        $('.select_customer_option').prop('disabled', true);
        customer = ["all"];
        $(this).val(customer);
    } else {
        $('.select_customer_option').prop('disabled', false);
    }
    });
    function validateDiscount() {
        let discountType = $('#discount_type').val();
        let discountInput = $('#discount');
        let minPurchase = parseFloat($('#min_purchase').val()) || 0;
        let discountValue = parseFloat(discountInput.val()) || 0;

        if (discountType === 'amount' && discountValue > minPurchase) {
            discountInput.val(discountValue);
            toastr.error($('#min-purchase-toast').val());
        }
    }
