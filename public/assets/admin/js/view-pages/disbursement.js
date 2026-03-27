"use strict";

$('input[name="disbursement_type"]').on('change', function(){
    if(this.value === 'manual'){
        $('.automated_disbursement_section').hide();

    }else{
        $('.automated_disbursement_section').show();
        $('.automated_disbursement_section').removeClass('d-none');
    }
})
$('#store_disbursement_time_period').on('change', function(){
    if(this.value === 'weekly'){
        $('#store_time_period_section').removeClass('col-12');
        $('#store_time_period_section').addClass('col-6');
        $('#store_week_day_section').removeClass('d-none');
    }else{
        $('#store_week_day_section').addClass('d-none');
        $('#store_time_period_section').removeClass('col-6');
        $('#store_time_period_section').addClass('col-12');
    }
})
$('#dm_disbursement_time_period').on('change', function(){
    if(this.value === 'weekly'){
        $('#dm_time_period_section').removeClass('col-12');
        $('#dm_time_period_section').addClass('col-6');
        $('#dm_week_day_section').removeClass('d-none');
    }else{
        $('#dm_week_day_section').addClass('d-none');
        $('#dm_time_period_section').removeClass('col-6');
        $('#dm_time_period_section').addClass('col-12');
    }
})

$(document).on('click', '.copy-to-clipboard', function () {
    let elementId=  $(this).data('id');
    let commandElement = document.getElementById(elementId);
    navigator.clipboard.writeText(commandElement.value);
    toastr.success('Copied to clipboard!');
});
