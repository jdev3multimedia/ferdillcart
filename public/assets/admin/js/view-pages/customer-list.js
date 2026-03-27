"use strict";
$(document).on('ready', function () {
    $('.js-select2-custom').each(function () {
        let select2 = $.HSCore.components.HSSelect2.init($(this));
    });
});

function readURL(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            $('#viewer').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#customFileEg1").change(function () {
    readURL(this);
});

$('#reset_btn').click(function(){
    $('#module_id').val(null).trigger('change');
    $('#viewer').attr('src', "{{asset('public/assets/admin/img/900x400/img1.jpg')}}");
})
$(document).on('ready', function() {

    $('.js-nav-scroller').each(function() {
        new HsNavScroller($(this)).init()
    });


    let datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
        dom: 'Bfrtip',
        buttons: [{
            extend: 'copy',
            className: 'd-none'
        },
            {
                extend: 'print',
                className: 'd-none'
            },
        ],
        select: {
            style: 'multi',
            selector: 'td:first-child input[type="checkbox"]',
            classMap: {
                checkAll: '#datatableCheckAll',
                counter: '#datatableCounter',
                counterInfo: '#datatableCounterInfo'
            }
        },
    });

    $('#export-copy').click(function() {
        datatable.button('.buttons-copy').trigger()
    });

    $('#export-excel').click(function() {
        datatable.button('.buttons-excel').trigger()
    });

    $('#export-csv').click(function() {
        datatable.button('.buttons-csv').trigger()
    });

    $('#export-pdf').click(function() {
        datatable.button('.buttons-pdf').trigger()
    });

    $('#export-print').click(function() {
        datatable.button('.buttons-print').trigger()
    });

    $('#datatableSearch').on('mouseup', function(e) {
        let $input = $(this),
            oldValue = $input.val();

        if (oldValue == "") return;

        setTimeout(function() {
            let newValue = $input.val();

            if (newValue == "") {
                // Gotcha
                datatable.search('').draw();
            }
        }, 1);
    });

    $('#toggleColumn_name').change(function(e) {
        datatable.columns(1).visible(e.target.checked)
    })

    $('#toggleColumn_email').change(function(e) {
        datatable.columns(2).visible(e.target.checked)
    })

    $('#toggleColumn_total_order').change(function(e) {
        datatable.columns(3).visible(e.target.checked)
    })


    $('#toggleColumn_status').change(function(e) {
        datatable.columns(4).visible(e.target.checked)
    })

    $('#toggleColumn_actions').change(function(e) {
        datatable.columns(5).visible(e.target.checked)
    })


    $('.js-tagify').each(function() {
        let tagify = $.HSCore.components.HSTagify.init($(this));
    });
});


