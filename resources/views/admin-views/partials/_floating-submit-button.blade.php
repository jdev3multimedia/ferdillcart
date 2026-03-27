
@php
    $submitButtonText = $submitButtonText ?? 'Save Information';
    $resetButtonText = $resetButtonText ?? 'Reset';
@endphp
<div class="mt-0 footer-sticky">
    <div class="container-fluid">
        <div class="btn--container justify-content-end py-3">
            <button type="reset" class="btn btn--reset min-w-120px">{{ $resetButtonText }}</button>
            <button type="submit" id="submit"
                class="btn btn--primary call-demo min-w-120px"><i class="tio-save">x</i>
                {{ $submitButtonText }}</button>
        </div>
    </div>
</div>
