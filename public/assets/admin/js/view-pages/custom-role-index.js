"use strict";
$(document).ready(function() {
    let datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));
});

$('#reset-btn').on('click', function(){

    $('.check--item-wrapper .check-item .form-check-input').attr('checked', false)
})
$('#select-all').on('change', function(){
    if(this.checked === true) {
        $('.check--item-wrapper .check-item .form-check-input').attr('checked', true)
    } else {
        $('.check--item-wrapper .check-item .form-check-input').attr('checked', false)
    }
})
$('.check--item-wrapper .check-item .form-check-input').on('change', function(){
    if(this.checked === true) {
        $(this).attr('checked', true)
    } else {
        $(this).attr('checked', false)
    }
})



//Sub Select
$('.select-subwrapper .check-all').on('change', function() {
    const wrapper = $(this).closest('.select-subwrapper');
    const isChecked = $(this).is(':checked');
    
    wrapper.find('.check-item .form-check-input').prop('checked', isChecked);
});

// Handle individual checkbox update
$('.select-subwrapper .check-item .form-check-input').on('change', function() {
    const wrapper = $(this).closest('.select-subwrapper');
    const allCheckboxes = wrapper.find('.check-item .form-check-input');
    const allChecked = allCheckboxes.length === allCheckboxes.filter(':checked').length;

    // Automatically check/uncheck the "select all" based on child checkboxes
    wrapper.find('.check-all').prop('checked', allChecked);
});