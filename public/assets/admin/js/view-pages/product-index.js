"use strict";
let element = "";
let count = 0;
let countRow = 0;
let module_id = "";
let parent_category_id = 0;
let module_data = null;
let stock = true;
let module_type = "";

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

$('.foodModalClose').on('click',function (){
    $('#food-modal').hide();
})

$('.foodModalShow').on('click',function (){
    $('#food-modal').show();
})

$('.attributeModalClose').on('click',function (){
    $('#attribute-modal').hide();
})

$('.attributeModalShow').on('click',function (){
    $('#attribute-modal').show();
})

$('#store_id').on('change', function () {
    let route = '{{url('/')}}/admin/store/get-addons?data[]=0&store_id='+$(this).val();
    let id = 'add_on';
    getRestaurantData(route, id);
});

function getRestaurantData(route, id) {
    $.get({
        url: route + id,
        dataType: 'json',
        success: function(data) {
            $('#' + id).empty().append(data.options);
        },
    });
}

function getRequest(route, id) {
    $.get({
        url: route,
        dataType: 'json',
        success: function(data) {
            $('#' + id).empty().append(data.options);
        },
    });
}

function readURL(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function(e) {
            $('#viewer').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#customFileEg1").change(function() {
    readURL(this);
});

$('#category_id').on('change', function () {
    parent_category_id = $(this).val();
    console.log(parent_category_id);
});
$(document).on('change', '.combination_update', function () {
    combination_update();
});
