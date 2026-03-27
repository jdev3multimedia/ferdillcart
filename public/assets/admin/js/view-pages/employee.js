"use strict";
$(document).on('ready', function () {
    // INITIALIZATION OF SHOW PASSWORD
    // =======================================================
    $('.js-toggle-password').each(function () {
        new HSTogglePassword(this).init()
    });


    // INITIALIZATION OF FORM VALIDATION
    // =======================================================
    $('.js-validate').each(function() {
        $.HSCore.components.HSValidation.init($(this), {
            rules: {
                confirmPassword: {
                    equalTo: '#signupSrPassword'
                }
            }
        });
    });
});

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#viewer').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#customFileUpload").change(function () {
    readURL(this);
});

$(".js-example-theme-single").select2({
    theme: "classic"
});

$(".js-example-responsive").select2({
    width: 'resolve'
});
