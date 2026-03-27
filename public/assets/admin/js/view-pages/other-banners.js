"use strict";
$(document).ready(function() {

    $(".__upload-img, .upload-img-4, .upload-img-2, .upload-img-5, .upload-img-1, .upload-img").each(function(){
        var targetedImage = $(this).find('.img');
        var targetedImageSrc = $(this).find('.img img');
        function proPicURL(input) {
            if (input.files && input.files[0]) {
                var uploadedFile = new FileReader();
                uploadedFile.onload = function (e) {
                    targetedImageSrc.attr('src', e.target.result);
                    targetedImage.addClass('image-loaded');
                    targetedImage.hide();
                    targetedImage.fadeIn(650);
                }
                uploadedFile.readAsDataURL(input.files[0]);
            }
        }
        $(this).find('input').on('change', function () {
            proPicURL(this);
        })
    })
});

$(".form-check-input").click(function() {
    if ($(this).val() == 'image') {
        $("#image").removeClass('d-none');
        $("#video").addClass('d-none');
        $("#video_content").addClass('d-none');


    } else if($(this).val() == 'video_content'){
        $("#video_content").removeClass('d-none');
        $("#video").addClass('d-none');
        $("#image").addClass('d-none');
    }
    else {
        $("#video").removeClass('d-none');
        $("#image").addClass('d-none');
        $("#video_content").addClass('d-none');
    }
});
