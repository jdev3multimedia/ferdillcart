"use strict";

$("#order_place").on("keydown", function (e) {
    if (e.keyCode === 13) {
        e.preventDefault();
    }
});
$("#insertPayableAmount").on("keydown", function (e) {
    if (e.keyCode === 13) {
        e.preventDefault();
    }
});

$(document).on("click", ".print-Div", function () {
    let printContents = document.getElementById("printableArea").innerHTML;
    let originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
});

$(document).on("click", ".addon-quantity-input-toggle", function (event) {
    let cb = $(event.target);
    if (cb.is(":checked")) {
        cb.siblings(".addon-quantity-input").css({ visibility: "visible" });
    } else {
        cb.siblings(".addon-quantity-input").css({ visibility: "hidden" });
    }
});

function cartQuantityInitialize() {
    $(".btn-number").click(function (e) {
        e.preventDefault();

        let fieldName = $(this).attr("data-field");
        let type = $(this).attr("data-type");
        let input = $("input[name='" + fieldName + "']");
        let currentVal = parseInt(input.val());

        if (!isNaN(currentVal)) {
            if (type === "minus") {
                if (currentVal > input.attr("min")) {
                    input.val(currentVal - 1).change();
                }
                if (parseInt(input.val()) === input.attr("min")) {
                    $(this).attr("disabled", true);
                }
            } else if (type === "plus") {
                if (currentVal < input.attr("max")) {
                    input.val(currentVal + 1).change();
                }
                if (parseInt(input.val()) === input.attr("max")) {
                    $(this).attr("disabled", true);
                }
            }
        } else {
            input.val(0);
        }
    });

    $(".input-number").focusin(function () {
        $(this).data("oldValue", $(this).val());
    });

    $(".input-number").change(function () {
        let minValue = parseInt($(this).attr("min"));
        let maxValue = parseInt($(this).attr("max"));
        let valueCurrent = parseInt($(this).val());
        let name = $(this).attr("name");
        if (valueCurrent >= minValue) {
            $(
                ".btn-number[data-type='minus'][data-field='" + name + "']"
            ).removeAttr("disabled");
        } else {
            Swal.fire({
                icon: "error",
                title: "Cart",
                text: "Sorry, the minimum value was reached",
            });
            $(this).val($(this).data("oldValue"));
        }
        if (valueCurrent <= maxValue) {
            $(
                ".btn-number[data-type='plus'][data-field='" + name + "']"
            ).removeAttr("disabled");
        } else {
            Swal.fire({
                icon: "error",
                title: "Cart",
                text: "Sorry, stock limit exceeded.",
            });
            $(this).val($(this).data("oldValue"));
        }
    });
    $(".input-number").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if (
            $.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
            // Allow: Ctrl+A
            (e.keyCode === 65 && e.ctrlKey === true) ||
            // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)
        ) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if (
            (e.shiftKey || e.keyCode < 48 || e.keyCode > 57) &&
            (e.keyCode < 96 || e.keyCode > 105)
        ) {
            e.preventDefault();
        }
    });
}

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

$(document).on("click", ".decrease-button", function () {
    let addonId = $(this).data("id");
    let addon_quantity_input = $('input[name="addon-quantity' + addonId + '"]');
    let currentValue = parseInt(addon_quantity_input.val(), 10);
    if (currentValue > 1) {
        addon_quantity_input.val(currentValue - 1);
        getVariantPrice();
    }
});

$(document).on("click", ".increase-button", function () {
    let addonId = $(this).data("id");
    let addon_quantity_input = $('input[name="addon-quantity' + addonId + '"]');
    let currentValue = parseInt(addon_quantity_input.val(), 10);
    addon_quantity_input.val(currentValue + 1);
    getVariantPrice();
});

$(document).on("click", ".decrease-button-cart", function () {
    let addon_quantity_input = $('input[name="quantity"]');
    let currentValue = parseInt(addon_quantity_input.val(), 10);
    if (currentValue > 1) {
        addon_quantity_input.val(currentValue - 1);
        getVariantPrice();
    }
});

$(document).on("click", ".increase-button-cart", function () {
    let addon_quantity_input = $('input[name="quantity"]');
    let currentValue = parseInt(addon_quantity_input.val(), 10);
    let maxValue = parseInt(addon_quantity_input.attr("max"));
    if (maxValue - 1 >= currentValue) {
        addon_quantity_input.val(currentValue + 1);
        getVariantPrice();
    } else {
        Swal.fire({
            icon: "error",
            title: "Cart",
            text: "Sorry, stock limit exceeded.",
        });
    }
});

$(".js-select2-custom").each(function () {
    let select2 = $.HSCore.components.HSSelect2.init($(this));
});
$("#delivery_address").on("click", function () {
    initMap();
});
// initMap();
$("#customer").change(function () {
    if ($(this).val()) {
        $("#customer_id").val($(this).val());
    }
});

$("#payment_card").on("change", function () {
    $("#paid_section").hide();
});
$("#payment_cash").on("change", function () {
    $("#paid_section").show();
});

$(document).on("change", "#discount_input_type", function () {
    let discountInput = $("#discount_input");
    let discountInputType = $(this);
    let maxLimit = discountInputType.val() === "percent" ? 100 : 1000000000;
    discountInput.attr("max", maxLimit);
});

function handleLocationError(browserHasGeolocation, infoWindow, pos) {
    infoWindow.setPosition(pos);
    infoWindow.setContent(
        browserHasGeolocation
            ? "Error: {{ translate('The Geolocation service failed') }}."
            : "Error: {{ translate('Your browser doesn`t support geolocation') }}."
    );
    infoWindow.open(map);
}
