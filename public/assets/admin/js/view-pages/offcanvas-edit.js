

"use strict";


$(document).on('click', '.data-info-show', function () {
    let id = $(this).data('id');
    let url = $(this).data('url');
    fetch_data(id, url)
})

function fetch_data(id, url) {
    $.ajax({
        url: url,
        type: "get",
        beforeSend: function () {
            $('#data-view').empty();
            $('#loading').show()
        },
        success: function (data) {
            $("#data-view").append(data.view);
            initLangTabs();
            initSelect2Dropdowns();
            initTextMaxLimit();

        },
        complete: function () {
            $('#loading').hide()
        }
    })
}

function initLangTabs() {
    const langLinks = document.querySelectorAll(".lang_link1");
    langLinks.forEach(function (langLink) {
        langLink.addEventListener("click", function (e) {
            e.preventDefault();
            langLinks.forEach(function (link) {
                link.classList.remove("active");
            });
            this.classList.add("active");
            document.querySelectorAll(".lang_form1").forEach(function (form) {
                form.classList.add("d-none");
            });
            let form_id = this.id;
            let lang = form_id.substring(0, form_id.length - 5);
            $("#" + lang + "-form1").removeClass("d-none");
            if (lang === "default") {
                $(".default-form1").removeClass("d-none");
            }
        });
    });
}

function initSelect2Dropdowns() {
    $('.offcanvas-close, #offcanvasOverlay').on('click', function () {
        $('.custom-offcanvas').removeClass('open');
        $('#offcanvasOverlay').removeClass('show');
    });
}
