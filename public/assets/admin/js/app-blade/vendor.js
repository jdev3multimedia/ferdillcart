"use strict";
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

    $(".trial-close").on("click", function () {
        $(this).closest(".trial").slideUp();
    });

    $.fn.select2DynamicDisplay = function () {
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
            html += $searchForm.prop("outerHTML");

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
            });

            // Bind change event to update display
            $this.on("change", function () {
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