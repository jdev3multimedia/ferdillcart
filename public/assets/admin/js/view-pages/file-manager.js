"use strict";
function readURL(input) {
    $("#files").html("");
    for (var i = 0; i < input.files.length; i++) {
        if (input.files && input.files[i]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $("#files").append(
                    '<div class="col-md-2 col-sm-4 m-1"><img class="initial--7" id="viewer" src="' +
                        e.target.result +
                        '"/></div>'
                );
            };
            reader.readAsDataURL(input.files[i]);
        }
    }
}

$("#customFileUpload").change(function () {
    readURL(this);
});

$("#customZipFileUpload").change(function (e) {
    var fileName = e.target.files[0].name;
    // $('#zipFileLabel').html(fileName);
});

$(".copy-test").on("click", function () {
    let copyText = $(this).data("file-path");
    copy_test(copyText);
});

function copy_test(copyText) {
    navigator.clipboard.writeText(copyText);

    toastr.success("File path copied successfully!", {
        CloseButton: true,
        ProgressBar: true,
    });
}

$(".form-submit-warning").on("submit", function () {
    form_submit_warrning(e);
});
