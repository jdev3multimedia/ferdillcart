<div class="d-flex flex-column gap-3">
     @foreach ($reasons as $key => $reason)

        <div class="d-flex align-item-center justify-content-between cursor-pointer">
            <label class="form-check-label fs-14 m-0" for="cancalation_address_{{ $key }}">
                {{ $reason['reason'] }}
            </label>
            <div class="form-check m-0">
                <input class="form-check-input checkbox-theme-20 single-select" type="checkbox" value="{{ $reason['reason'] }}"
                    name="reason[]" id="cancalation_address_{{ $key }}">
            </div>
        </div>
    @endforeach

</div>
