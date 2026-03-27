"use strict";

$(document).on('ready', function () {
    // INITIALIZATION OF DATATABLES
    // =======================================================
    var datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
        select: {
            style: 'multi',
            classMap: {
                checkAll: '#datatableCheckAll',
                counter: '#datatableCounter',
                counterInfo: '#datatableCounterInfo'
            }
        },
    });

    $('#datatableSearch').on('mouseup', function (e) {
        var $input = $(this),
            oldValue = $input.val();

        if (oldValue == "") return;

        setTimeout(function () {
            var newValue = $input.val();

            if (newValue == "") {
                // Gotcha
                datatable.search('').draw();
            }
        }, 1);
    });

    $('#toggleColumn_index').change(function (e) {
        datatable.columns(0).visible(e.target.checked)
    })
    $('#toggleColumn_name').change(function (e) {
        datatable.columns(1).visible(e.target.checked)
    })

    $('#toggleColumn_vendor').change(function (e) {
        datatable.columns(3).visible(e.target.checked)
    })

    $('#toggleColumn_status').change(function (e) {
        datatable.columns(4).visible(e.target.checked)
    })
    $('#toggleColumn_price').change(function (e) {
        datatable.columns(2).visible(e.target.checked)
    })
    $('#toggleColumn_action').change(function (e) {
        datatable.columns(5).visible(e.target.checked)
    })


    // INITIALIZATION OF SELECT2
    // =======================================================
    $('.js-select2-custom').each(function () {
        var select2 = $.HSCore.components.HSSelect2.init($(this));
    });
});

$('#reset_btn').click(function () {
    $('#store_id').val(null).trigger('change');
})
