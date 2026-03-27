"use strict";

function readURL(input, viewer) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            $('#'+viewer).attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#coverImageUpload").change(function () {
    readURL(this, 'coverImageViewer');
});

$("#customFileUpload").change(function () {
    readURL(this, 'viewer');
});
