

$(document).on('click', '.auto_fill_title', function (event) {
    const $button = $(this);
    const lang = $button.data('lang');
    const route = $button.data('route');
    const type = $button.data('type');
    const $editorContainer = $('#title-container-' + lang);

    const requestType = $('#request_type').val();
    const store_id = $('#store_id').val()

    let $input = (type === 'default')
        ? $('#default_name')
        : $('#' + lang + '_name');

    let name = $input.val();

    if (!name) {
        if($('#default_name').val()){
            name = $('#default_name').val();
        } else
        {
            toastr.error($button.data('error'));
            return;
        }
    }

    $button.prop('disabled', true);
    $button.find('.btn-text').text('');
    const $aiText = $button.find('.ai-text-animation');
    $aiText.removeClass('d-none').addClass('ai-text-animation-visible');
    let $wrapper = $input.closest('.outline-wrapper');
    $wrapper.addClass('outline-animating');
    const module_type = $('#module_type').val();

     $.ajax({
        url: route,
        type: 'GET',
        dataType: 'json',
        data: {
            name: name,
            langCode: lang,
            requestType: requestType,
            module_type:module_type,
            store_id:store_id
        },
        success: function (response) {
            let $targetInput;
            if(type === 'default'){
                $targetInput = $('#default_name');
                $targetInput.val(response.data.title);
            }else{
                $targetInput = $('#' + lang + '_name');
                $targetInput.val(response.data.title);
            }

            // Trigger validation to clear required errors
            if ($targetInput.length && $targetInput.closest('form').length) {
                $targetInput.closest('form').validate().element($targetInput);
            }

            replaceSVGs();
        },
        error: function (xhr, status, error) {

            if (xhr.responseJSON && xhr.responseJSON.message) {
                toastr.error(xhr.responseJSON.message);
            } else {
                toastr.error('An unexpected error occurred.');
            }
        },

        complete: function () {
            setTimeout(function () {
                $editorContainer.removeClass('animating');
                $wrapper.removeClass('outline-animating');
            }, 500);

            $button.prop('disabled', false);
            $button.find('.btn-text').text('Re-generate');
            $aiText.addClass('d-none').removeClass('ai-text-animation-visible');
        }
    });
});
