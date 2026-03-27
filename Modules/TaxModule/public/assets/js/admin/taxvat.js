"use strict";
$(document).ready(function () {
    let originalData = {};

    $('.get_data').on('click', function () {
        originalData = {
            action: $(this).data('action'),
            name: $(this).data('name'),
            tax_rate: $(this).data('tax_rate'),
            is_active: $('#status_' +  $(this).data('id')).is(':checked'),
        };
        setDataOnModal(originalData)
    });

    $('.reset').on('click', function () {
        setDataOnModal(originalData)
    });

    function setDataOnModal(originalData){
        const modal = $('#editTaxData');
        modal.find('form').attr('action', originalData.action);
        modal.find('#tax_name').val(originalData.name);
        modal.find('#tax_rate').val(originalData.tax_rate);
        modal.find('#tax_status').prop('checked', originalData.is_active == 1);
    };
});

    document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".confirmStatus").forEach(checkbox => {
                checkbox.addEventListener("click", e => {
                    e.preventDefault();

                    const input = checkbox.querySelector(".toggle-switch-sm input");
                    const isChecked = input.checked;
                    const url = checkbox.dataset.url;

                    const title = checkbox.dataset[isChecked ? "off_title" : "on_title"];
                    const message = checkbox.dataset[isChecked ? "off_message" : "on_message"];

                    $('#confirmationTitle').text(title);
                    $('#confirmationMessage').text(message);
                    document.getElementById('seturl').dataset.url = url;
                    $('#exampleModal').modal('show');
                });
            });
        });


        document.getElementById('seturl').addEventListener('click', function() {
            const url = this.dataset.url;
            const is_active = this.dataset.is_active;

            if (!url) return console.error("No URL found for status change");

            $.get(url, {
                is_active
            }, function(response) {
                $('#exampleModal').modal('hide');
                $('#status_' + response.id).prop('checked', response.status);
                    sent_notification('successMessage' , response.message);
            }).fail(function(xhr) {
                console.error("Error updating status:", xhr.responseText);

            });
        });
