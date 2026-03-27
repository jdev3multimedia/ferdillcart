

$(document).on('click', '.variation_setup_auto_fill', function () {
    const $button = $(this);
    const lang = $button.data('lang');
    const route = $button.data('route');
    const name = $('#default_name').val();
    const description = $('#description-default').val();
    const $editor = $('#description-' + lang + '-editor');
    if (!name || !description) {
        toastr.error($button.data('error'));
        return;
    }
    const $container = $('.variation-setup-container');

    $container.addClass('animating');
    $button.prop('disabled', true);
    $button.find('.btn-text').text('');
    const $aiText = $button.find('.ai-text-animation');
    $aiText.removeClass('d-none').addClass('ai-text-animation-visible');

    let $wrapper = $(this).closest('.variation_wrapper').find('.outline-wrapper');
    $wrapper.addClass('outline-animating');
    $wrapper.find('.bg-animate').addClass('active');

    const requestType = $('#request_type').val();
    const store_id = $('#store_id').val()
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
            console.log('Success:', response);
            render_variations_from_response(response.data);

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


    function render_variations_from_response(variations) {


        if (!Array.isArray(variations) || !variations.length) return;

        $("#add_new_option").empty();
        $('#empty-variation').hide();

        variations.forEach(function (variation, vIndex) {
            add_new_option_button();

            let vCount = count;
            if (variation.required) {
                $(`#options\\[${vCount}\\]\\[required\\]`).prop("checked", true);
            }

            $(`input[name="options[${vCount}][name]"]`).val(variation.variation_name);

            $(`input[name="options[${vCount}][type]"][value="${variation.selection_type}"]`).prop("checked", true);

            if ($('#stock_type').val() !== 'unlimited') {
                $('.count_stock').each(function () {
                    $(this).val($('#item_stock').val() || Math.floor(Math.random() * 10) + 1);
                });
            }


            if (variation.selection_type === 'single') {
                $(`#min_max1_${vCount}`).val('').prop('readonly', true).prop('required', false);
                $(`#min_max2_${vCount}`).val('').prop('readonly', true).prop('required', false);
            } else {
               $(`#min_max1_${vCount}`).val(variation.min);
                $(`#min_max2_${vCount}`).val(variation.max);
            }

            if (variation.options && variation.options.length) {
                    variation.options.forEach(function (opt, oIndex) {
                        if (oIndex > 0) {
                            $(`#add_new_button_${vCount} .add_new_row_button`).trigger("click");
                        }

                        let $lastRow = $(`#option_price_view_${vCount} .add_new_view_row_class`).last();

                        $lastRow.find('input[name$="[label]"]').val(opt.option_name);
                        $lastRow.find('input[name$="[optionPrice]"]').val(opt.option_price);

                        if (opt.total_stock !== undefined) {
                            $lastRow.find('input[name$="[total_stock]"]').val(opt.total_stock);
                        }
                    });
                    $('#remove_all_old_variations').val(1);
            }
                if ($('#stock_type').val() !== 'unlimited') {
                    $('.count_stock').each(function () {
                        $(this).val($('#item_stock').val() || Math.floor(Math.random() * 10) + 1);
                    });
            }
        });
    }




