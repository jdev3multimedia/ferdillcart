

$(document).on('click', '.general_setup_auto_fill', function () {

    const $button = $(this);
    const lang = $button.data('lang');
    const route = $button.data('route');
    const name = $('#default_name').val();
    const $editor = $('#description-default' + '-editor');


    const description = $('#description-default').val();
    const $container = $('.general-setup-container');



    if (!name || !description) {
        toastr.error($button.data('error'));
        return;
    }


    $container.addClass('animating');
    $button.prop('disabled', true);
    $button.find('.btn-text').text('');
    const $aiText = $button.find('.ai-text-animation');
    $aiText.removeClass('d-none').addClass('ai-text-animation-visible');
    let $wrapper = $('.general_wrapper').find('.outline-wrapper');
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
            store_id:store_id,
            module_type:module_type,
            requestType: requestType
        },
        success: function (response) {

            if (response.data.category_id) {
                $('#category_id').append(new Option(response.data.category_name, response.data.category_id, true, true)).trigger('change');
            }
            if (response.data.condition_id) {
                $('#condition_id').append(new Option(response.data.common_conditions, response.data.condition_id, true, true)).trigger('change');
            }
            if (response.data.brand_id) {
                $('#brand_id').append(new Option(response.data.brand, response.data.brand_id, true, true)).trigger('change');
            }
            if (response.data.units) {
                 $('#unit').val(response.data.unit_id).trigger('change');
            }

            if (response.data.product_type) {
                let optionValue = response.data.product_type.toLowerCase() === 'veg' ? 1 : 0;
                    $('#veg').val(optionValue).trigger('change');
            }
            if (response.data.is_halal) {
                $('#is_halal').prop('checked', true);
            } else {
                $('#is_halal').prop('checked', false);
            }
            if (response.data.is_basic_medicine) {
                $('#is_basic_medicine').prop('checked', true);
            } else {
                $('#is_basic_medicine').prop('checked', false);
            }
            if (response.data.prescription_required) {
                $('#prescription_required').prop('checked', true);
            } else {
                $('#prescription_required').prop('checked', false);
            }
            if (response.data.is_organic) {
                $('#is_organic').prop('checked', true);
            } else {
                $('#is_organic').prop('checked', false);
            }
            if(response.data.generic_names){
                $('#generic_name_input').val(response.data.generic_names);
            }

            if(response.data.available_time_starts){
                $('#available_time_starts').val(response.data.available_time_starts);
                $('#available_time_ends').val(response.data.available_time_ends);
            }

            if (response.data.search_tags) {
                var $tagsInput = $('#tags');
                $tagsInput.tagsinput('removeAll');
                response.data.search_tags.forEach(function (tag) {
                    $tagsInput.tagsinput('add', tag);
                });
            }

            if (response.data.nutrition && response.data.nutrition.length) {
                let $select = $('#nutritions_input').empty();
                response.data.nutrition.forEach(tag => {
                    $select.append(new Option(tag, tag, true, true));
                });
                $select.trigger('change');
                $(".multiple-select2").select2DynamicDisplay();
            } else {
                let $select = $('#nutritions_input').empty();
                $select.trigger('change');
            }

            if (response.data.allergy && response.data.allergy.length) {
                let $select = $('#allergy_input').empty();
                response.data.allergy.forEach(tag => {
                    $select.append(new Option(tag, tag, true, true));
                });
                $select.trigger('change');
                $(".multiple-select2").select2DynamicDisplay();

            } else {
                let $select = $('#allergy_input').empty();
                $select.trigger('change');
            }

            if (response.data.addon && Object.keys(response.data.addon).length) {
                let $select = $('#add_on').empty();
                Object.entries(response.data.addon).forEach(([name, id]) => {
                    $select.append(new Option(name, id, true, true));
                });
                $select.trigger('change');
                 $(".multiple-select2").select2DynamicDisplay();
            }
            if (response.data.sub_category_id) {
                setTimeout(function() {
                      $('#sub-categories').append(new Option(response.data.sub_category_name, response.data.sub_category_id, true, true)).trigger('change');

                }, 400);
            }

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


