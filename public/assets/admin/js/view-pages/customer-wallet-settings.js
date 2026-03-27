"use strict";
$('.section_visibility').on('click', function (){
    let id = $(this).data('id');
    section_visibility(id);
})
function section_visibility(id) {
    console.log($('#' + id).data('section'));
    if ($('#' + id).is(':checked')) {
        console.log('checked');
        $('.' + $('#' + id).data('section')).show();
    } else {
        console.log('unchecked');
        $('.' + $('#' + id).data('section')).hide();
    }
}

$('#reset_btn').click(function(){
    location.reload(true);
})
