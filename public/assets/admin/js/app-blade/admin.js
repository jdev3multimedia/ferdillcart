"use strict";

$(".blinkings").on("mouseover", () => $(".blinkings").removeClass("active"));
$(".blinkings").addClass("open-shadow");
setTimeout(() => {
    $(".blinkings").removeClass("active");
}, 10000);
setTimeout(() => {
    $(".blinkings").removeClass("open-shadow");
}, 5000);

$(document).on("click", ".next-tour", function () {
    next_tour();
});

function next_tour() {
    tour.next();
}

$(function () {
    let owl = $(".single-item-slider");
    owl.owlCarousel({
        autoplay: false,
        items: 1,
        onInitialized: counter,
        onTranslated: counter,
        autoHeight: true,
        dots: true,
    });

    function counter(event) {
        let element = event.target; // DOM element, in this example .owl-carousel
        let items = event.item.count; // Number of items
        let item = event.item.index + 1; // Position of the current item

        // it loop is true then reset counter from 1
        if (item > items) {
            item = item - items;
        }
        $(".slide-counter").html(+item + "/" + items);
    }
});

//slider inside arrow
$(function () {
    let owl = $(".single-item-slider2");
    owl.owlCarousel({
        autoplay: false,
        items: 1,
        onInitialized: counter,
        onTranslated: counter,
        autoHeight: true,
        dots: true,
        nav: false,
    });

    // Custom nav buttons
    $(".custom-next").click(function () {
        owl.trigger("next.owl.carousel");
    });
    $(".custom-prev").click(function () {
        owl.trigger("prev.owl.carousel");
    });
    function counter(event) {
        let element = event.target;
        let items = event.item.count;
        let item = event.item.index + 1;
        if (item > items) {
            item = item - items;
        }
        $(".slide-counter").html(+item + "/" + items);
    }
});

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

//Just toggle 
$(".view_toggle_btn").on("click", function () {
    var container = $(this).closest(".card-container");
    var details = container.find(".card-details-body");
    var icon = $(this).find("i");

    $(this).toggleClass("active");

    if (details.hasClass("d-none")) {
        details.removeClass("d-none").hide().slideDown(300);
    } else {
        details.slideUp(300, function () {
            details.addClass("d-none");
        });
    }
    icon.toggleClass("rotate-180deg");
});




$(document).on("ready", function () {
    // ONLY DEV
    // =======================================================
    if (window.localStorage.getItem("hs-builder-popover") === null) {
        $("#builderPopover")
            .popover("show")
            .on("shown.bs.popover", function () {
                $(".popover").last().addClass("popover-dark");
            });

        $(document).on("click", "#closeBuilderPopover", function () {
            window.localStorage.setItem("hs-builder-popover", true);
            $("#builderPopover").popover("dispose");
        });
    } else {
        $("#builderPopover").on("show.bs.popover", function () {
            return false;
        });
    }
    // END ONLY DEV
    // =======================================================

    // BUILDER TOGGLE INVOKER
    // =======================================================
    $(".js-navbar-vertical-aside-toggle-invoker").click(function () {
        $(".js-navbar-vertical-aside-toggle-invoker i").tooltip("hide");
    });

    // INITIALIZATION OF NAVBAR VERTICAL NAVIGATION
    // =======================================================
    let sidebar = $(".js-navbar-vertical-aside").hsSideNav();

    // INITIALIZATION OF TOOLTIP IN NAVBAR VERTICAL MENU
    // =======================================================
    $(".js-nav-tooltip-link").tooltip({ boundary: "window" });

    $(".js-nav-tooltip-link").on("show.bs.tooltip", function (e) {
        if (!$("body").hasClass("navbar-vertical-aside-mini-mode")) {
            return false;
        }
    });

    // INITIALIZATION OF UNFOLD
    // =======================================================
    $(".js-hs-unfold-invoker").each(function () {
        let unfold = new HSUnfold($(this)).init();
    });

    // INITIALIZATION OF FORM SEARCH
    // =======================================================
    $(".js-form-search").each(function () {
        new HSFormSearch($(this)).init();
    });

    // INITIALIZATION OF SELECT2
    // =======================================================
    $(".js-select2-custom").each(function () {
        let select2 = $.HSCore.components.HSSelect2.init($(this));
    });

    // INITIALIZATION OF DATERANGEPICKER
    // =======================================================
    $(".js-daterangepicker").daterangepicker();
    $(".js-daterangepicker-times").daterangepicker({
        timePicker: true,
        startDate: moment().startOf("hour"),
        endDate: moment().startOf("hour").add(32, "hour"),
        locale: {
            format: "M/DD hh:mm A",
        },
    });
    let start = moment();
    let end = moment();

    function cb(start, end) {
        $(
            "#js-daterangepicker-predefined .js-daterangepicker-predefined-preview"
        ).html(start.format("MMM D") + " - " + end.format("MMM D, YYYY"));
    }

    $("#js-daterangepicker-predefined").daterangepicker(
        {
            startDate: start,
            endDate: end,
            ranges: {
                Today: [moment(), moment()],
                Yesterday: [
                    moment().subtract(1, "days"),
                    moment().subtract(1, "days"),
                ],
                "Last 7 Days": [moment().subtract(6, "days"), moment()],
                "Last 30 Days": [moment().subtract(29, "days"), moment()],
                "This Month": [
                    moment().startOf("month"),
                    moment().endOf("month"),
                ],
                "Last Month": [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month"),
                ],
            },
        },
        cb
    );

    cb(start, end);

    // INITIALIZATION OF CLIPBOARD
    // =======================================================
    $(".js-clipboard").each(function () {
        let clipboard = $.HSCore.components.HSClipboard.init(this);
    });
});
let tour = new Tour({
    backdrop: true,
    delay: true,
    redirect: true,
    name: "tour",
    steps: [
        {
            element: "#tourb-0",
            title: "Module",
            placement: "right",
            content: "From here you can switch to multiple modules.",
        },
        {
            element: "#tourb-1",
            title: "Module Selection",
            content: "You can select a module from here.",
        },
        {
            element: "#navbar-vertical-content",
            title: "Module Sidebar",
            content: "This is the module wise sidebar.",
        },
        {
            element: "#tourb-3",
            title: "Settings",
            content: "From here you can go to settings option.",
        },
        {
            element: "#tourb-4",
            title: "Settings Menu",
            content: "From here you can select any settings option.",
        },
        {
            element: "#navbar-vertical-content",
            title: "Settings Sidebar",
            content: "This is the settings sidebar. Different from module",
        },
        {
            element: "#tourb-6",
            title: "User Section",
            content: "You can manage all the users by selecting this option.",
        },
        {
            element: "#tourb-7",
            title: "Transaction and Report",
            content:
                "You can manage all the Transaction and Report by selecting this option.",
        },
        {
            element: "#tourb-8",
            title: "Dispatch Management",
            content:
                "You can manage all dispatch orders by selecting this option.",
        },
        {
            element: "#tourb-9",
            title: "Profile and Logout",
            content: "You can visit your profile or logut from this panel.",
            placement: "top",
        },
    ],
    onEnd: function () {
        $("body").css("overflow", "");
    },
    onShow: function () {
        $("body").css("overflow", "hidden");
    },
});
$(document).on("click", ".instruction-Modal-Close", function () {
    $("#instruction-modal").hide();
    tour.init();
    tour.start();
});

$(document).on("click", ".email-Modal-Close", function () {
    $("#email-modal").hide();
});

$(".store-filter").on("change", function () {
    const id = $(this).val();
    const url = $(this).data("url");
    let nurl = new URL(url);
    nurl.searchParams.delete("page");
    nurl.searchParams.set("store_id", id);
    location.href = nurl;
});

$(".payment-method-filter").on("change", function () {
    const id = $(this).val();
    const url = $(this).data("url");
    let nurl = new URL(url);
    nurl.searchParams.set("payment_method_id", id);
    location.href = nurl;
});

function set_filter(url, id, filter_by) {
    let nurl = new URL(url);
    nurl.searchParams.set(filter_by, id);
    location.href = nurl;
    tour.next();
}

$(".set-module").on("click", function () {
    const url = $(this).data("url");
    const id = $(this).data("module-id");
    const filter_by = $(this).data("filter");
    let nurl = new URL(url);
    nurl.searchParams.set(filter_by, id);
    location.href = nurl;
});

$(document).ready(function () {
    $("button[type=submit]").on("click", function () {
        setTimeout(function () {
            $("button[type=submit]").prop("disabled", true);
        }, 0);
        setTimeout(function () {
            $("button[type=submit]").prop("disabled", false);
        }, 1000);
    });
});

function getUrlParameter(sParam) {
    let sPageURL = window.location.search.substring(1);
    let sURLVariables = sPageURL.split("&");
    for (let i = 0; i < sURLVariables.length; i++) {
        let sParameterName = sURLVariables[i].split("=");
        if (sParameterName[0] === sParam) {
            return sParameterName[1];
        }
    }
}

$.fn.select2DynamicDisplay = function () {
    const limit = 50;
    function updateDisplay($element) {
        var $rendered = $element
            .siblings(".select2-container")
            .find(".select2-selection--multiple")
            .find(".select2-selection__rendered");
        var $container = $rendered.parent();
        var containerWidth = $container.width();
        var totalWidth = 0;
        var itemsToShow = [];
        var remainingCount = 0;

        // Get all selected items
        var selectedItems = $element.select2("data");

        // Create a temporary container to measure item widths
        var $tempContainer = $("<div>")
            .css({
                display: "inline-block",
                padding: "0 15px",
                "white-space": "nowrap",
                visibility: "hidden",
            })
            .appendTo($container);

        // Calculate the width of items and determine how many fit
        selectedItems.forEach(function (item) {
            var $tempItem = $("<span>")
                .text(item.text)
                .css({
                    display: "inline-block",
                    padding: "0 12px",
                    "white-space": "nowrap",
                })
                .appendTo($tempContainer);

            var itemWidth = $tempItem.outerWidth(true);

            if (totalWidth + itemWidth <= containerWidth - 40) {
                totalWidth += itemWidth;
                itemsToShow.push(item);
            } else {
                remainingCount = selectedItems.length - itemsToShow.length;
                return false;
            }
        });

        $tempContainer.remove();

        const $searchForm = $rendered.find(".select2-search");

        var html = "";
        itemsToShow.forEach(function (item) {
            html += `<li class="name">
                                    <span>${item.text}</span>
                                    <span class="close-icon" data-id="${item.id}"><i class="tio-clear"></i></span>
                                    </li>`;
        });
        if (remainingCount > 0) {
            html += `<li class="ms-auto">
                                    <div class="more">+${remainingCount}</div>
                                    </li>`;
        }

        if (selectedItems.length < limit) {
            html += $searchForm.prop("outerHTML");
        }

        $rendered.html(html);

        function debounce(func, wait) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        // Attach event listener with debouncing
        $(".select2-search input").on(
            "input",
            debounce(function () {
                const inputValue = $(this).val().toLowerCase();

                const $listItems = $(".select2-results__options li");

                $listItems.each(function () {
                    const itemText = $(this).text().toLowerCase();
                    $(this).toggle(itemText.includes(inputValue));
                });
            }, 100)
        );

        $(".select2-search input").on("keydown", function (e) {
            if (e.which === 13) {
                e.preventDefault();

                const inputValue = $(this).val();
                if (
                    !inputValue ||
                    itemsToShow.find((item) => item.text === inputValue) ||
                    selectedItems.find((item) => item.text === inputValue)
                ) {
                    $(this).val("");
                    return null;
                }

                if (inputValue) {
                    $element.append(
                        new Option(inputValue, inputValue, true, true)
                    );
                    $element.val([...$element.val(), inputValue]);
                    $(this).val("");
                    $(".multiple-select2").select2DynamicDisplay();
                }
            }
        });
    }
    return this.each(function () {
        var $this = $(this);

        $this.select2({
            tags: true,
            maximumSelectionLength: limit,
        });

        $this.on("change", function () {
        let selected_items = $this.val() || [];

        if (selected_items.includes("all") && selected_items.length > 1) {
            $this.val(["all"]).trigger("change.select2");
        }

        if (!selected_items.includes("all") && selected_items.length > 0) {
            $this.val(selected_items.filter(v => v !== "all")).trigger("change.select2");
        }
            updateDisplay($this);
        });

        // Initial display update
        updateDisplay($this);

        $(window).on("resize", function () {
            updateDisplay($this);
        });
        $(window).on("load", function () {
            updateDisplay($this);
        });

        // Handle the click event for the remove icon
        $(document).on(
            "click",
            ".select2-selection__rendered .close-icon",
            function (e) {
                e.stopPropagation();
                var $removeIcon = $(this);
                var itemId = $removeIcon.data("id");
                var $this2 = $removeIcon
                    .closest(".select2")
                    .siblings(".multiple-select2");
                $this2.val(
                    $this2.val().filter(function (id) {
                        return id != itemId;
                    })
                );
                $this2.trigger("change");
            }
        );
    });
};
$(".multiple-select2").select2DynamicDisplay();

$(function () {
    $(".date-range-picker").daterangepicker({
        // "timePicker": true,
        ranges: {
            Today: [moment(), moment()],
            Yesterday: [
                moment().subtract(1, "days"),
                moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
                moment().subtract(1, "month").startOf("month"),
                moment().subtract(1, "month").endOf("month"),
            ],
        },
        maxDate: moment(),
        startDate: $(this).data("startDate"),
        endDate: $(this).data("endDate"),
        autoUpdateInput: false,
        locale: {
            cancelLabel: "Clear",
        },
        alwaysShowCalendars: true,
    });

    $(".date-range-picker").attr("placeholder", "Select date");

    $(".date-range-picker").on("apply.daterangepicker", function (ev, picker) {
        $(this).val(
            picker.startDate.format("MM/DD/YYYY") +
                " - " +
                picker.endDate.format("MM/DD/YYYY")
        );
    });

    $(".date-range-picker").on("cancel.daterangepicker", function (ev, picker) {
        $(this).val("");
        picker.setStartDate(moment()); // Reset the start date
        picker.setEndDate(moment());
    });

    $(".date-range-picker").on("show.daterangepicker", function (ev, picker) {
        let title = $(this).data("title") || "Select a Date";
        if (!$(".daterangepicker .calendar-title").length) {
            $(".daterangepicker").prepend(
                `<div class=" text-center mb-2 mt-4 calendar-title"> ${title}  </div>`
            );
        } else {
            $(".daterangepicker .calendar-title").text(title);
        }
    });
});

$(document).ready(function () {
    // --- select2 dropdown icon add
    $("select.js-select2-custom, select.multiple-select2")
        .on("select2:open", function () {
            setTimeout(() => {
                $(this)
                    .next(".select2")
                    .find(".select2-selection--multiple")
                    .addClass("custom-select");
            }, 10);
        })
        .trigger("select2:open")
        .select2("close");
});

function initializeTooltipWithHoverContent() {
    let activeTooltip = null;
    $('[data-toggle="tooltip"][data-html="true"]')
        .tooltip({
            html: true,
            trigger: "manual",
        })
        .on("mouseenter", function () {
            let _this = this;
            if (activeTooltip && activeTooltip !== _this) {
                $(activeTooltip).tooltip("dispose");
            }
            activeTooltip = _this;
            $(_this).tooltip("show");
            let tooltipElement = $(".tooltip");
            tooltipElement.off("mouseenter mouseleave").on({
                mouseenter: function () {
                    $(activeTooltip)
                        .tooltip("dispose")
                        .tooltip({
                            html: true,
                            trigger: "manual",
                        })
                        .tooltip("show");
                },
                mouseleave: function () {
                    setTimeout(function () {
                        if (!$(".tooltip:hover").length) {
                            $(activeTooltip).tooltip("dispose");
                            activeTooltip = null;
                        }
                    }, 200);
                },
            });
        })
        .on("mouseleave", function () {
            let _this = this;
            setTimeout(function () {
                if (!$(".tooltip:hover").length) {
                    $(_this).tooltip("dispose");
                    if (activeTooltip === _this) {
                        activeTooltip = null;
                    }
                }
            }, 200);
        });
}
$(document).ready(function () {
    initializeTooltipWithHoverContent();
});



//Tour & Guideline Button
$(document).on('click', '.tour-guide_btn', function () {
    const $parent = $(this).closest('.toggle-tour');
    $parent.toggleClass('active');
});


document.addEventListener("DOMContentLoaded", function () {
    // ---- tabindex priority set logic
    function assignTabIndexes() {
        document.querySelectorAll("form").forEach((form) => {
            let tabIndex = 1;

            // Reset old tabindex
            form.querySelectorAll("[tabindex]").forEach(el => el.removeAttribute("tabindex"));

            form.querySelectorAll("input, textarea, select").forEach((el) => {
                if (
                    el.disabled ||
                    el.type === "hidden" ||
                    el.hasAttribute("readonly")
                ) return;

                if (el.classList.contains("ckeditor")) return;
                if (el.classList.contains("js-select2-custom")) return;

                el.setAttribute("tabindex", tabIndex++);
            });

            form.querySelectorAll(".select2-selection").forEach((el) => {
                el.setAttribute("tabindex", tabIndex++);
            });

            if (typeof CKEDITOR !== "undefined") {
                Object.values(CKEDITOR.instances).forEach((editor) => {
                    const editable = editor.editable();
                    if (editable && editable.$ && form.contains(editable.$)) {
                        editable.$.setAttribute("tabindex", tabIndex++);
                    }
                });
            }
        });
    }

    assignTabIndexes();

    const observer = new MutationObserver(() => {
        assignTabIndexes();
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    // ---- tabindex priority set logic ends
});
function initCharCounter(selector = '.char-counter') {
    $(selector).each(function () {
        const $input = $(this);
        const maxLength = $input.attr('maxlength');
        const target = $input.data('target');
        const $counter = $(target);
        if (!maxLength || !$counter.length) return;
        $input.on('input', function () {
            const currentLength = $input.val().length;
            $counter.text(`${currentLength}/${maxLength}`);
        }).trigger('input');
    });
}

//New Character Count code ,
function initTextMaxLimit(selector = 'input[data-maxlength], textarea[data-maxlength], input[maxlength], textarea[maxlength]') {
    const fields = document.querySelectorAll(selector);

    fields.forEach(function (field) {
        const maxLength = parseInt(field.getAttribute('data-maxlength') || field.getAttribute('maxlength'), 10);
        const counter = field.parentElement.querySelector('.text-counting');

        const updateCounter = () => {
            if (field.value.length > maxLength) {
                field.value = field.value.slice(0, maxLength);
            }
            if (counter) {
                counter.textContent = `${field.value.length}/${maxLength}`;
            }
        };

        field.addEventListener('input', updateCounter);
        updateCounter();
    });
    }
    document.addEventListener('DOMContentLoaded', function () {
        initTextMaxLimit();
    });
