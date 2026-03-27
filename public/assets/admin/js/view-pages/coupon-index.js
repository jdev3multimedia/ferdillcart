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
    $('#min_purchase').on('click', function() {
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

    $('#date_from').attr('min',(new Date()).toISOString().split('T')[0]);
    $('#date_to').attr('min',(new Date()).toISOString().split('T')[0]);
    let datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'), {
        select: {
            style: 'multi',
            classMap: {
                checkAll: '#datatableCheckAll',
                counter: '#datatableCounter',
                counterInfo: '#datatableCounterInfo'
            }
        },
    });

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
$('#zone_wise').hide();
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
    } else {
        $('#discount_type').removeAttr("disabled").attr("required", true);
        $('#max_discount, #discount').removeAttr("readonly");
    }

    if ($('#discount_type').val() === 'amount') {
        $('#max_discount').val(0).attr("readonly", true);
    } else if($('#discount_type').val() === 'percent') {
        $('#max_discount').removeAttr("readonly");
    }
}


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

    $('#reset_btn').click(function(){
        $('#module_select').val(null).trigger('change');
        $('#store_id').val(null).trigger('change');
        $('#store_wise').show();
        $('#zone_wise').hide();
        $('#coupon_title').val('');
        $('#coupon_code').val(null);
        $('#coupon_limit').val(null);
        $('#date_from').val(null);
        $('#date_to').val(null);
        $('#discount_type').val('amount');
        $('#discount').val(null);
        $('#max_discount').val(0);
        $('#min_purchase').val(0);
        $('#select_customer').val(null).trigger('change');
    })


    function validateDiscount() {
        let discountType = $('#discount_type').val();
        let discountInput = $('#discount');
        let minPurchase = parseFloat($('#min_purchase').val()) || 0;
        let discountValue = parseFloat(discountInput.val()) || 0;

        if (discountType === 'amount' && discountValue > minPurchase) {
            discountInput.val(discountValue);
            // toastr.error($('#min-purchase-toast').val());
        }
    }
