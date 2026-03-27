"use strict";
$(document).on('ready', function (){
    $('.id_wise').hide();
    $('.date_wise').hide();
    $('#type').on('change', function()
    {
        $('.id_wise').hide();
        $('.date_wise').hide();
        $('.'+$(this).val()).show();
    })
});
$('#reset_btn').click(function(){
    $('#bulk__import').val(null);
})



