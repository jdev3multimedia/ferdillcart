

$(document).on('click', '.other_variation_setup_auto_fill', function () {
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
            store_id: store_id
        },
        success: function (response) {
            console.log('Success:', response);
            const selectedValues = response.data.choice_attributes.map(attr => ({
                id: attr.id.toString(),
                name: attr.name,
                variation: attr.options.join(',')
            }));
            render_variations_from_response(selectedValues);

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
                 update_stock_eq();
            }, 500);

            $button.prop('disabled', false);
            $button.find('.btn-text').text('Re-generate');
            $aiText.addClass('d-none').removeClass('ai-text-animation-visible');
        }
    });
});


function render_variations_from_response(selectedValues) {

    let selectedIds = selectedValues.map(item => item.id);

    $('#choice_attributes option')
        .prop('selected', false)
        .filter(function () {
            return selectedIds.includes($(this).val());
        })
        .prop('selected', true)
        .trigger('change');
    $('#customer_choice_options').html(null);



    $('#variant_combination').html(null);


    selectedValues.forEach(item => {
        addMoreCustomerChoiceOptionWithAI(item.id, item.name, item.variation);
    });

    combination_update();

}

function addMoreCustomerChoiceOptionWithAI(index, name, variation) {
    let nameSplit = name.split(" ").join("");
    let genHtml = `<div class="col-md-6">
                            <div class="form-group ">
                            <input type="hidden" name="choice_no[]" value="${index}">
                                <label class="form-label">${nameSplit}</label>
                                <input type="text" name="choice[]" value="${nameSplit}" hidden>
                                <div class="">
                                    <input type="text" class="form-control combination_update" name="choice_options_${index}[]"
                                    placeholder="${$("#message-enter-choice-values").data("text")}"
                                    data-role="tagsinput" value="${variation}">
                                </div>
                            </div>
                        </div>`;

    document.getElementById("customer_choice_options")
        .insertAdjacentHTML("beforeend", genHtml);

    document.querySelectorAll("input[data-role=tagsinput], select[multiple][data-role=tagsinput]")
        .forEach(function (input) {
            $(input).tagsinput();
        });
}

  function update_stock_eq() {
            let total_qty = 0;
            let qty_elements = $('input[name^="stock_"]');
            for (let i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            if (qty_elements.length > 0) {

                $('input[name="current_stock"]').attr("readonly", 'readonly');
                $('input[name="current_stock"]').val(total_qty);
            } else {
                $('input[name="current_stock"]').attr("readonly", false);
            }
        }


