

"use strict";

$('input[name="landing_integration_via"]').on('change', function() {
    $(`.__input-tab`).removeClass('active')
    $(`#${this.value}`).addClass('active')
})
