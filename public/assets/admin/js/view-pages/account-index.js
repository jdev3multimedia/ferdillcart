"use strict";
$(document).on('ready', function () {
    // INITIALIZATION OF SELECT2
    // =======================================================
    $('.js-select2-custom').each(function () {
        var select2 = $.HSCore.components.HSSelect2.init($(this));
    });

    $('#type').on('change', function() {
        if($('#type').val() == 'store')
        {
            $('#store').removeAttr("disabled");
            $('#deliveryman').val("").trigger( "change" );
            $('#deliveryman').attr("disabled","true");
        }
        else if($('#type').val() == 'deliveryman')
        {
            $('#deliveryman').removeAttr("disabled");
            $('#store').val("").trigger( "change" );
            $('#store').attr("disabled","true");
        }
    });
});
$('#reset_btn').click(function(){
    $('#store').val(null).trigger('change');
    $('#deliveryman').val(null).trigger('change');
    $('#deliveryman').removeAttr("disabled");
    $('#store').val("").trigger( "change" );
    $('#store').attr("disabled","true");

})
