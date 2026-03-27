"use strict";

let element = "";
let count =   $('.count_div').length;
let countRow = 0;
let mod_type="";
let removedImageKeys = [];

$(document).on('click','.function_remove_img' ,function(){
let key = $(this).data('key');
 let photo = $(this).data('photo');
 function_remove_img(key,photo);
});

function function_remove_img(key,photo) {
$('#product_images_' + key).addClass('d-none');
removedImageKeys.push(photo);
$('#removedImageKeysInput').val(removedImageKeys.join(','));
}

$(document).ready(function() {
    $('#organic').hide();
    if (mod_type == 'food') {
        $('#food_variation_section').show();
        $('#attribute_section').hide();
    } else {
        $('#food_variation_section').hide();
        $('#attribute_section').show();
    }
    if (mod_type == 'grocery') {
        $('#organic').show();
    }

    // INITIALIZATION OF SELECT2
    // =======================================================
    $('.js-select2-custom').each(function () {
        let select2 = $.HSCore.components.HSSelect2.init($(this));
    });
});

// function show_min_max(data) {
//     $('#min_max1_' + data).removeAttr("readonly");
//     $('#min_max2_' + data).removeAttr("readonly");
//     $('#min_max1_' + data).attr("required", "true");
//     $('#min_max2_' + data).attr("required", "true");
// }

// function hide_min_max(data) {
//     $('#min_max1_' + data).val(null).trigger('change');
//     $('#min_max2_' + data).val(null).trigger('change');
//     $('#min_max1_' + data).attr("readonly", "true");
//     $('#min_max2_' + data).attr("readonly", "true");
//     $('#min_max1_' + data).attr("required", "false");
//     $('#min_max2_' + data).attr("required", "false");
// }

// $(document).on('change', '.show_min_max', function () {
//     let data = $(this).data('count');
//     show_min_max(data);
// });

// $(document).on('change', '.hide_min_max', function () {
//     let data = $(this).data('count');
//     hide_min_max(data);
// });

$(document).on('change', '.show_min_max', function () {
    let count = $(this).data('count');
    toggleMinMaxRequired(count, true);
});

$(document).on('change', '.hide_min_max', function () {
    let count = $(this).data('count');
    toggleMinMaxRequired(count, false);
});

function toggleMinMaxRequired(count, required) {
    let $min = $('#min_max1_' + count);
    let $max = $('#min_max2_' + count);

    if (required) {
        $min.prop('readonly', false).prop('required', true);
        $max.prop('readonly', false).prop('required', true);
    } else {
        $min.prop('readonly', true).prop('required', false).val(null).trigger('change').removeClass('is-invalid');
        $max.prop('readonly', true).prop('required', false).val(null).trigger('change').removeClass('is-invalid');
        $('div.form-validation-error[data-for="options[' + count + '][min]"]').remove();
        $('div.form-validation-error[data-for="options[' + count + '][max]"]').remove();
    }
}




function new_option_name(value, data) {
    $("#new_option_name_" + data).empty();
    $("#new_option_name_" + data).text(value)
    console.log(value);
}

function removeOption(e) {
    element = $(e);
    element.parents('.view_new_option').remove();
}

$(document).on('click', '.delete_input_button', function () {
    let e = $(this);
    removeOption(e);
});

function deleteRow(e) {
    element = $(e);
    element.parents('.add_new_view_row_class').remove();
}

$(document).on('click', '.deleteRow', function () {
    let e = $(this);
    deleteRow(e);
});

$(document).on('click', '.add_new_row_button', function () {
    let data = $(this).data('count');
    add_new_row_button(data);
});

$(document).on('keyup', '.new_option_name', function () {
    let data = $(this).data('count');
    let value = $(this).val();
    new_option_name(value, data);
});

$(document).on('change', '.get-request', function () {
    let val= $(this).val();
    let route= $(this).data('url')+val;
    let id= $(this).data('id');
    getRequest(route, id);
})

function getRequest(route, id) {
    $.get({
        url: route,
        dataType: 'json',
        success: function (data) {
            $('#' + id).empty().append(data.options);
        },
    });
}

function readURL(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            $('#viewer').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#customFileEg1").change(function () {
    readURL(this);
    $('#image-viewer-section').show(1000)
});

$('#choice_attributes').on('change', function () {
    $('#customer_choice_options').html(null);
    $.each($("#choice_attributes option:selected"), function () {
        add_more_customer_choice_option($(this).val(), $(this).text());
    });
});

$(document).on('change', '.combination_update', function () {
    combination_update();
});

setTimeout(function () {
    $('.call-update-sku').on('change', function () {
        combination_update();
    });
}, 2000)

$('#colors-selector').on('change', function () {
    combination_update();
});

$('input[name="unit_price"]').on('keyup', function () {
    combination_update();
});
