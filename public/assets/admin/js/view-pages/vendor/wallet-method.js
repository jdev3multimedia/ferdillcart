"use strict";


$('.showMyModal').on('click', function (){
    let data = $(this).data('message');
    showMyModal(data);
})

function showMyModal(data) {
    $(".modal-body #hiddenValue").html(data);
    $('#exampleModal').modal('show');
}

$('.withdrawal-methods-disable').on('click', function (){
    toastr.info( $(this).data('message') , {
        CloseButton: true,
        ProgressBar: true
    });
})
