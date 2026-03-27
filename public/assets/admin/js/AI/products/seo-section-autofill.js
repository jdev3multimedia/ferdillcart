

$(document).on('click', '.seo_section_auto_fill', function () {
    const $button = $(this);
    const lang = $button.data('lang');
    const route = $button.data('route');
    const name = $('#default_name').val();
    const description = $('#description-default').val();
    const $container = $('.seo-section-container');
    $container.addClass('animating');
    $button.prop('disabled', true);
    $button.find('.btn-text').text('');
    const $aiText = $button.find('.ai-text-animation');
    $aiText.removeClass('d-none').addClass('ai-text-animation-visible');

    let $wrapper = $(this).closest('.seo_wrapper').find('.outline-wrapper');
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
            module_type:module_type,
            store_id:store_id
        },
        success: function (response) {
            console.log('Success:', response);

             var data = response.data;

             $('#meta_title').val(data.meta_title);
             $('#meta_description').val(data.meta_description);


             $('input[name="meta_index"][value="' + data.meta_index + '"]').prop('checked', true);


             $('input[name="meta_no_follow"]').prop('checked', data.meta_no_follow == 1);
             $('input[name="meta_no_image_index"]').prop('checked', data.meta_no_image_index == 1);
             $('input[name="meta_no_archive"]').prop('checked', data.meta_no_archive == 1);
             $('input[name="meta_no_snippet"]').prop('checked', data.meta_no_snippet == 1);

             $('input[name="meta_max_snippet"]').prop('checked', data.meta_max_snippet == 1);
             $('input[name="meta_max_video_preview"]').prop('checked', data.meta_max_video_preview == 1);
             $('input[name="meta_max_image_preview"]').prop('checked', data.meta_max_image_preview == 1);


             $('input[name="meta_max_snippet_value"]').val(data.meta_max_snippet_value);
             $('input[name="meta_max_video_preview_value"]').val(data.meta_max_video_preview_value);

             $('select[name="meta_max_image_preview_value"]').val(data.meta_max_image_preview_value);

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
