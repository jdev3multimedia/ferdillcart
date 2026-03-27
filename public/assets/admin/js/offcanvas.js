// Open offcanvas
$(document).ready(function () {
    $('.offcanvas-trigger').on('click', function (e) {
        e.preventDefault();
        var target = $(this).data('target');
        $(target).addClass('open');
        $('#offcanvasOverlay').addClass('show');
    });

    // Close offcanvas on close button or overlay click
    $('.offcanvas-close, #offcanvasOverlay').on('click', function () {
        $('.custom-offcanvas').removeClass('open');
        $('#offcanvasOverlay').removeClass('show');
    });
});

//View Details
$(".view-btn").on("click", function () {
    var container = $(this).closest(".view-details-container");
    var details = container.find(".view-details");
    var icon = $(this).find("i");

    $(this).toggleClass("active");
    details.slideToggle(300);
    icon.toggleClass("rotate-180deg");
});
$(".section-toggle").on("change", function () {
    if ($(this).is(':checked')) {
        $(this).closest(".view-details-container").find(".view-details").slideDown(300);
    } else {
        $(this).closest(".view-details-container").find(".view-details").slideUp(300);
    }
});

//Select2 Data Add
$(document).ready(function() {
    $('#tax__rate').select2({
        placeholder: 'Select tax rate',
        allowClear: true // Optional: adds a clear (×) button
    });
    $('#service__charge').select2({
        placeholder: 'Select tax rate',
        allowClear: true // Optional: adds a clear (×) button
    });
    $('.service__charge').select2({
        placeholder: 'Select tax rate',
        allowClear: true // Optional: adds a clear (×) button
    });
});

//Custom Slider Menu
function checkNavOverflow() {
    try {
        $(".step-integration-inner").each(function () {
            let $nav = $(this);
            let $btnNext = $nav
                .closest(".position-relative")
                .find(".slide-cus__next");
            let $btnPrev = $nav
                .closest(".position-relative")
                .find(".slide-cus__prev");
            let isRTL = $("html").attr("dir") === "rtl";
            let navScrollWidth = $nav[0].scrollWidth;
            let navClientWidth = $nav[0].clientWidth;
            let scrollLeft = Math.abs($nav.scrollLeft());

            if (isRTL) {
                let maxScrollLeft = navScrollWidth - navClientWidth;
                let scrollRight = maxScrollLeft - scrollLeft;

                $btnNext.toggle(scrollRight > 1);
                $btnPrev.toggle(scrollLeft > 1);
            } else {
                $btnNext.toggle(
                    navScrollWidth > navClientWidth &&
                    scrollLeft + navClientWidth < navScrollWidth
                );
                $btnPrev.toggle(scrollLeft > 1);
            }
        });
    } catch (error) {
        console.error(error);
    }
}
$(".step-integration-inner").each(function () {
    let $nav = $(this);
    checkNavOverflow($nav);
    $(window).on("resize", function () {
        checkNavOverflow($nav);
    });
    $nav.on("scroll", function () {
        checkNavOverflow($nav);
    });
    $nav.siblings(".slide-cus__next").on("click", function () {
        let scrollWidth = $nav.find("li").outerWidth(true);
        let isRTL = $("html").attr("dir") === "rtl";
        if (isRTL) {
            $nav.animate(
                { scrollLeft: $nav.scrollLeft() - scrollWidth },
                300,
                function () {
                    checkNavOverflow($nav);
                }
            );
        } else {
            $nav.animate(
                { scrollLeft: $nav.scrollLeft() + scrollWidth },
                300,
                function () {
                    checkNavOverflow($nav);
                }
            );
        }
    });
    $nav.siblings(".slide-cus__prev").on("click", function () {
        let scrollWidth = $nav.find("li").outerWidth(true);
        let isRTL = $("html").attr("dir") === "rtl";

        if (isRTL) {
            $nav.animate(
                { scrollLeft: $nav.scrollLeft() + scrollWidth },
                300,
                function () {
                    checkNavOverflow($nav);
                }
            );
        } else {
            $nav.animate(
                { scrollLeft: $nav.scrollLeft() - scrollWidth },
                300,
                function () {
                    checkNavOverflow($nav);
                }
            );
        }
    });
});

//Custom Copy Text
let copyText = document.querySelector(".custom-copy-text");
if(copyText){
    copyText.querySelector(".copy-btn").addEventListener("click", function () {
        let input = copyText.querySelector("input.text-inside");
        input.select();
        document.execCommand("copy");
        copyText.classList.add("active");
        window.getSelection().removeAllRanges();
        setTimeout(function () {
            copyText.classList.remove("active");
        }, 2500);
    });
}
