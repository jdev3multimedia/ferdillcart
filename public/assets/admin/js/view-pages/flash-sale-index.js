"use strict";
$("#from").on("change", function () {
    $('#to').attr('min',$(this).val());
});

$("#to").on("change", function () {
    $('#from').attr('max',$(this).val());
});
$(document).on('ready', function () {
    $('#from').attr('min',(new Date()).toISOString().split('T')[0]);
    $('#to').attr('min',(new Date()).toISOString().split('T')[0]);
    // INITIALIZATION OF DATATABLES
    // =======================================================
    let datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

    $('#column1_search').on('keyup', function () {
        datatable
            .columns(1)
            .search(this.value)
            .draw();
    });


    $('#column3_search').on('change', function () {
        datatable
            .columns(2)
            .search(this.value)
            .draw();
    });


    // INITIALIZATION OF SELECT2
    // =======================================================
    $('.js-select2-custom').each(function () {
        let select2 = $.HSCore.components.HSSelect2.init($(this));
    });
});
document.getElementById('adminDiscount').addEventListener('input', function() {
    updateStoreDiscount(this.value, 'storeDiscount');
});

document.getElementById('storeDiscount').addEventListener('input', function() {
    updateAdminDiscount(this.value, 'adminDiscount');
});

function roundToThreeDecimalPlaces(value) {
    return Math.round(value * 1000) / 1000;
}

function updateStoreDiscount(adminDiscount, storeDiscountId) {
    adminDiscount = parseFloat(adminDiscount);
    adminDiscount = isNaN(adminDiscount) ? 0 : Math.min(100, Math.max(0, adminDiscount));
    const totalDiscount = 100;
    const storeDiscount = roundToThreeDecimalPlaces(totalDiscount - adminDiscount);
    document.getElementById(storeDiscountId).readOnly = true;
    document.getElementById(storeDiscountId).value = storeDiscount;
    document.getElementById('adminDiscount').required = true;
    document.getElementById('storeDiscount').required = true;
}

function updateAdminDiscount(storeDiscount, adminDiscountId) {
    storeDiscount = parseFloat(storeDiscount);
    storeDiscount = isNaN(storeDiscount) ? 0 : Math.min(100, Math.max(0, storeDiscount));
    const totalDiscount = 100;
    const adminDiscount = roundToThreeDecimalPlaces(totalDiscount - storeDiscount);
    document.getElementById(adminDiscountId).readOnly = true;
    document.getElementById(adminDiscountId).value = adminDiscount;
    document.getElementById('adminDiscount').required = true;
    document.getElementById('storeDiscount').required = true;
}
