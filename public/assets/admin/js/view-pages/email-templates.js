

"use strict";

let editor = CKEDITOR.replace('ckeditor');

editor.on( 'change', function( evt ) {
    $('#mail-body').empty().html(evt.editor.getData());
});

$('input[data-id="mail-title"]').on('keyup', function() {
    let dataId = $(this).data('id');
    let value = $(this).val();
    $('#'+dataId).text(value);
});
$('input[data-id="mail-button"]').on('keyup', function() {
    let dataId = $(this).data('id');
    let value = $(this).val();
    $('#'+dataId).text(value);
});
$('input[data-id="mail-footer"]').on('keyup', function() {
    let dataId = $(this).data('id');
    let value = $(this).val();
    $('#'+dataId).text(value);
});
$('input[data-id="mail-copyright"]').on('keyup', function() {
    let dataId = $(this).data('id');
    let value = $(this).val();
    $('#'+dataId).text(value);
});

function readURL(input, viewer) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) {
            $('#' + viewer).attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$("#mail-logo").change(function() {
    readURL(this, 'logoViewer');
});

$("#mail-banner").change(function() {
    readURL(this, 'bannerViewer');
});

$("#mail-icon").change(function() {
    readURL(this, 'iconViewer');
});

$(".lang_link").click(function(event){
    event.preventDefault();
    $(".lang_link").removeClass('active');
    $(".lang_form").addClass('d-none');
    $(this).addClass('active');

    let form_id = this.id;
    let lang = form_id.substring(0, form_id.length - 5);

    $("#"+lang+"-form").removeClass('d-none');
    $("#"+lang+"-form1").removeClass('d-none');
    $("#"+lang+"-form2").removeClass('d-none');
    $("#"+lang+"-form3").removeClass('d-none');
    if(lang === 'default')
    {
        $(".default-form").removeClass('d-none');
    }
    else
    {
        $(".from_part_2").addClass('d-none');
    }
});

$('.check-mail-element').on('change', function() {
    let id = $(this).data('id');
        console.log(id);
        if ($('.' + id).is(':checked')) {
            $('#' + id).show();
        } else {
            $('#' + id).hide();
        }
});
document.getElementById('see-how-it-works').addEventListener('click', function() {
    $('#email-modal').show();
});

if(document.getElementById('rental-mail-route-selector')){
    document.getElementById('rental-mail-route-selector').addEventListener('change', function() {
        let value = this.value;
        location.href = baseUrl + '/admin/business-settings/rental-email-setup/' + value + '/' + (value === 'admin' ? 'provider-registration' : value === 'provider'?'registration':'new-order');
    });
}
if( document.getElementById('mail-route-selector')){
    document.getElementById('mail-route-selector').addEventListener('change', function() {
        let value = this.value;
        location.href = baseUrl + '/admin/business-settings/email-setup/' + value + '/' + (value === 'admin' ? 'forgot-password' : 'registration');
    });
}


