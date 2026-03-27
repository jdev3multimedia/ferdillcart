"use strict";

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".confirmStatus").forEach(checkbox => {
        checkbox.addEventListener("click", e => {
            e.preventDefault();

            const env = checkbox.dataset["env"];

            if(env == 'demo'){
                toastr.info("Update option is disabled for demo!", {
                    CloseButton: true,
                    ProgressBar: true
                });
            }else{

                const input = checkbox.querySelector(".toggle-switch-sm input");
                const isChecked = input.checked;
                const url = checkbox.dataset.url;

                const title = checkbox.dataset[isChecked ? "off_title" : "on_title"];
                const message = checkbox.dataset[isChecked ? "off_message" : "on_message"];

                $('#confirmationTitle').text(title);
                $('#confirmationMessage').text(message);
                document.getElementById('seturl').dataset.url = url;
                $('#exampleModal').modal('show');
            }

        });
    });

    document.querySelectorAll(".check_additional_data").forEach(checkbox => {
        checkbox.addEventListener("change", () => {
            const id = checkbox.dataset.id;
            const select = document.getElementById('additional_charge_' + id);

            if (checkbox.checked) {
                select.removeAttribute('disabled');
                select.setAttribute('required', 'required');
            } else {
                select.setAttribute('disabled', 'disabled');
                select.removeAttribute('required');
            }

        });
        checkbox.dispatchEvent(new Event('change'));
    });
});


document.getElementById('seturl').addEventListener('click', function () {
    const url = this.dataset.url;
    const is_active = this.dataset.is_active;

    if (!url) return console.error("No URL found for status change");

    $.get(url, {
        is_active
    }, function (response) {

        $('#exampleModal').modal('hide');
        if (!$('#system_tax_id').val()) {
            $('#system_tax_id').val(response.id);
        }
        $('#vendor_tax_status').prop('checked', response.status);
        $('#tax_settings').toggleClass('disabled', response.status != 1);
        sent_notification('successMessage', response.message);
    }).fail(function (xhr) {
        console.error("Error updating status:", xhr.responseText);

    });
});

    function handleTaxStatusChange(value) {
        if (value === 'include') {
            $('#tax_rate_setup').addClass('disabled');
            $('#tax__rate1').attr('required', false);
            $('#tax__rate').attr('required', false);
        } else if (value === 'exclude') {
            $('#tax_rate_setup').removeClass('disabled');
            $('#tax__rate1').attr('required', true);
            if($('#tax_type').val() == 'order_wise'){
                $('#tax__rate').attr('required', true);
            }
        }
    }

$(document).ready(function () {
    $('input[name="tax_status"]').on('change', function () {
        handleTaxStatusChange(this.value);
    });



    // Run on page load using currently selected radio
    const currentValue = $('input[name="tax_status"]:checked').val();
    if (currentValue) {
        handleTaxStatusChange(currentValue);
    }
    $('#tax_type').on('change', function () {

        if (this.value == 'product_wise') {
            $('#tax_type_change_alert').removeClass('d-none').addClass('d-flex');
        } else {
            $('#tax_type_change_alert').removeClass('d-flex').addClass('d-none');
        }


        if ($('input[name="tax_status"]:checked').val() == 'exclude') {
            if (this.value === 'product_wise') {
                $('#info_notes').removeClass('d-none').addClass('d-flex');
                $('#info_for_item').removeClass('d-none');
                $('#info_for_category').addClass('d-none');
                $('#tax_rate_div').addClass('d-none');
                $('#tax__rate').attr('required', false);
            } else if (this.value === 'category_wise') {
                $('#tax_rate_div').addClass('d-none');
                $('#info_notes').removeClass('d-none').addClass('d-flex');
                $('#info_for_category').removeClass('d-none');
                $('#info_for_item').addClass('d-none');
                $('#tax__rate').attr('required', false);
            } else {
                $('#info_notes').addClass('d-none').removeClass('d-flex');
                $('#info_for_item').addClass('d-none');
                $('#info_for_category').addClass('d-none');
                $('#tax_rate_div').removeClass('d-none');
                $('#tax__rate').attr('required', true);
            }
        }
    }).trigger('change');
});
