"use strict";
$(document).on('ready', function () {

    $('.js-select2-custom').each(function () {
        let select2 = $.HSCore.components.HSSelect2.init($(this));
    });
});


var forms = document.querySelectorAll('.priority-form');

forms.forEach(function(form) {
    var select = form.querySelector('.priority-select');

    select.addEventListener('change', function() {
        form.submit();
    });
});

$("#customFileEg1").change(function() {
    readURL(this);
    $('#viewer').show(1000)
});

    $('.location-reload-to-brand, .location-reload-to-category').on('click', function() {
        let nurl = new URL($(this).data('url'));
        nurl.searchParams.delete('search');
        location.href = nurl;
    });

$('#reset_btn').click(function(){
    $('#exampleFormControlSelect1').val(null).trigger('change');
        $('#viewer').attr('src', $(this).data('image'));
})


