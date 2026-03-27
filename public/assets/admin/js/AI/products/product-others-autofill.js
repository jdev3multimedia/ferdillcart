

$(document).on('click', '.price_others_auto_fill', function () {
    const $button = $(this);
    const lang = $button.data('lang');
    const route = $button.data('route');


    const $container = $('.price-others-container');
    const description = $('#description-default').val();

    const name = $('#default_name').val();
    if (!name || !description) {
        toastr.error($button.data('error'));
        return;
    }
    
    $container.addClass('animating');
    $button.prop('disabled', true);
    $button.find('.btn-text').text('');
    const $aiText = $button.find('.ai-text-animation');
    $aiText.removeClass('d-none').addClass('ai-text-animation-visible');

    let $wrapper = $(this).closest('.price_wrapper').find('.outline-wrapper');
    $wrapper.addClass('outline-animating');
    $wrapper.find('.bg-animate').addClass('active');


    const requestType = $('#request_type').val();
    const store_id = $('#store_id').val();
    const module_type = $('#module_type').val();

    $.ajax({
        url: route,
        type: 'GET',
        dataType: 'json',
        data: {
            name: name,
            description: description,
            requestType: requestType,
            module_type: module_type,
            store_id:store_id
        },
        success: function (response) {

            if (response.data.unit_price) {
                $('#unit_price').val(response.data.unit_price);
            }
            if (response.data.discount_amount) {
                $('#discount').val(response.data.discount_amount);
            }
            if (response.data.minimum_order_quantity) {
                $('#cart_quantity').val(response.data.minimum_order_quantity);
            }


            if($('#quantity').val() == ''){
                $('#quantity').val(Math.floor(Math.random() * 10) + 1);
            }



            let $select = $('select[name="tax_ids[]"]');

            let options = $select.find('option').map(function() {
                return $(this).val();
            }).get();
            let randomValue = options[Math.floor(Math.random() * options.length)];
            $select.val([randomValue]).trigger('change');

            replaceSVGs();

        },
        error: function (xhr, status, error) {
            console.error('Error:', error);
            if (xhr.responseJSON && xhr.responseJSON.message) {
                toastr.error(xhr.responseJSON.message);
            } else {
                toastr.error('An unexpected error occurred.');
            }
        },
        complete: function () {
            setTimeout(function () {
                $container.removeClass('animating');
                $wrapper.removeClass('outline-animating');
                $wrapper.find('.bg-animate').removeClass('active');
            }, 500);

            $button.prop('disabled', false);
            $button.find('.btn-text').text('Re-generate');
            $aiText.addClass('d-none').removeClass('ai-text-animation-visible');
        }
    });
});
