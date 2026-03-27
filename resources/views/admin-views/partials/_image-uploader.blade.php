@php
    $aspectRatio = match ($ratio ?? '1:1') {
        '1:1' => 'ratio-1',
        '2:1' => 'ratio-2-1',
        '3:1' => 'ratio-3-1',
        default => 'ratio-1',
    };
    $imageExtension = $imageExtension ?? IMAGE_EXTENSION;
    $maxSize = $maxSize ?? MAX_FILE_SIZE;
    $isRequired = $isRequired ?? false;
    $existingImage = $existingImage ?? '';
    $ratio = $ratio ?? '1:1';
    $id = $id ?? 'image-input';
    $name = $name ?? 'image';
    $imageFormat = $imageFormat ?? IMAGE_FORMAT;
    $pixel = isset($pixel) && $pixel !== '' ? $pixel . ' px' : null;
    $size = $pixel ?? $ratio;
    $textPosition = $textPosition ?? 'top';
@endphp
<div class="mx-auto text-center">
    @if ($textPosition == 'top')
        <p class="mb-2 fs-12 gray-dark">
            {{ translate(($imageFormat) . '. Less Than ' . $maxSize . 'MB')}} <span
                class="font-medium text-title">{{ translate('(' . $size . ')')}}</span>
        </p>
    @endif
    <div class="upload-file_custom {{ $aspectRatio }} h-100px">
        <input type="hidden" name="{{ $name }}_deleted" class="image-delete-flag" value="0">
        <input class="upload-file__input single_file_input" type="file" id="{{ $id }}" name="{{ $name }}"
            accept="{{ $imageExtension }}" {{ !$existingImage && $isRequired ? 'required' : '' }}
            data-max-size="{{ $maxSize }}">
        @if (!$isRequired)
            <button type="button" class="remove_btn remove_btn_outside btn icon-btn btn-circle btn-danger fs-14 lh-1"
                style="--size: 20px;">
                <i class="tio-clear"></i>
            </button>
        @endif
        <label for="{{ $id }}" class="upload-file__wrapper w-100 h-100 m-0 {{ $aspectRatio }}">
            <div class="upload-file-textbox text-center">
                <img width="27" class="svg" src="{{ asset('public/assets/admin/img/document-upload.svg') }}" alt="img">
                <h6 class="mt-1 color-656566 fw-medium fs-10 lh-base text-center">
                    <span class="theme-clr">{{ translate('Add') }}</span>
                    @if ($isRequired)
                        <span class="text-danger d-none">*</span>
                    @endif
                </h6>
            </div>
            <img class="upload-file-img" loading="lazy" src="{{ $existingImage }}" data-default-src="" alt=""
                style="display: none;">
        </label>
        <div class="overlay">
            <div class="d-flex gap-1 justify-content-center align-items-center h-100">
                <button type="button" class="btn btn-outline-info icon-btn view_btn">
                    <i class="tio-invisible"></i>
                </button>
                <button type="button" class="btn btn-outline-info icon-btn edit_btn">
                    <i class="tio-camera-enhance"></i>
                </button>
            </div>
        </div>
    </div>
    @if ($textPosition == 'bottom')
        <p class="mt-3 mb-2 fs-12 gray-dark">
            {{ translate(strtoupper($imageFormat) . '. Less Than ' . $maxSize . 'MB')}} <span
                class="font-medium text-title">{{ translate('(' . $size . ')')}}</span>
        </p>
    @endif
</div>