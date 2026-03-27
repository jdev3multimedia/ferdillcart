<form action="{{ route('admin.parcel.cancellationReasonUpdate', [$reason['id']]) }}" method="post"
    class="d-flex flex-column h-100">
    @method('put')
    @csrf
    <div>
        <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
            <h3 class="mb-0">{{ translate('Edit_Reason') }}</h3>
            <button type="button"
                class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"
                aria-label="Close">&times;</button>
        </div>
        <div class="custom-offcanvas-body p-20">
            <div class="bg--secondary rounded p-20 mb-20">
                @if ($language)
                    <ul class="nav nav-tabs mb-4 border-0">
                        <li class="nav-item">
                            <a class="nav-link lang_link1 active" href="#"
                                id="default-link">{{ translate('messages.default') }}</a>
                        </li>
                        @foreach ($language as $lang)
                            <li class="nav-item">
                                <a class="nav-link lang_link1" href="#"
                                    id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <div class="row">
                    <div class="col-12">
                        @if ($language)
                            <div class="form-group lang_form1" id="default-form1">
                                <label class="fs-14 mb-2 color-222324">{{ translate('Parcel cancellation reason') }}
                                    ({{ translate('Default') }})
                                </label>
                                <textarea rows="1" name="reason[]" data-target="#edit-char-count"
                                    class="form-control min-h-45px bg-white char-counter" maxlength="150" placeholder="Type Tittle">{{ $reason?->getRawOriginal('reason') }}</textarea>
                                <div id="edit-char-count" class="color-A7A7A7 mt-1 fs-14 text-right">0/150</div>
                                <input type="hidden" name="lang[]" value="default">
                            </div>
                            @foreach ($language as $key => $lang)
                                <?php
                                if (count($reason['translations'])) {
                                    $translate = [];
                                    foreach ($reason['translations'] as $t) {
                                        if ($t->locale == $lang && $t->key == 'reason') {
                                            $translate[$lang]['name'] = $t->value;
                                        }
                                    }
                                }
                                ?>

                                <div class="form-group d-none lang_form1" id="{{ $lang }}-form1">
                                    <label
                                        class="fs-14 mb-2 color-222324 ">{{ translate('Parcel cancellation reason') }}
                                        ({{ strtoupper($lang) }})
                                    </label>
                                    <textarea rows="1" name="reason[]" data-target="#edit-feedback-count-{{ $lang }}"
                                        class="form-control min-h-45px bg-white char-counter" maxlength="150" placeholder="Type Tittle">{{ $translate[$lang]['name'] ?? '' }}</textarea>
                                    <input type="hidden" name="lang[]" value="{{ $lang }}">
                                    <div id="edit-feedback-count-{{ $lang }}"
                                        class="color-A7A7A7 mt-1 fs-14 text-right">
                                        0/150</div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="" class="fs-14 mb-2 color-222324">
                                {{ translate('Cancellation type') }}
                            </label>
                            <select name="cancellation_type" required id=""
                                class="custom-select fs-12 title-clr">
                                <option {{ $reason?->cancellation_type == 'before_pickup' ? 'selected' : '' }}
                                    value="before_pickup">{{ translate('before_pickup') }}</option>
                                <option {{ $reason?->cancellation_type == 'after_pickup' ? 'selected' : '' }}
                                    value="after_pickup">{{ translate('after_pickup') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="" class="fs-14 mb-2 color-222324">
                                {{ translate('User Type') }}
                            </label>
                            <select name="user_type" required id="" class="custom-select fs-12 title-clr">
                                <option {{ $reason?->user_type == 'customer' ? 'selected' : '' }} value="customer">
                                    {{ translate('Customer') }}</option>
                                <option {{ $reason?->user_type == 'deliveryman' ? 'selected' : '' }}
                                    value="deliveryman">{{ translate('Deliveryman') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div
        class="align-items-center bg-white bottom-0 d-flex gap-3 justify-content-center mt-auto offcanvas-footer p-3 position-sticky">
        <button type="button"
            class="btn w-100 btn--secondary offcanvas-close h--40px">{{ translate('Cancel') }}</button>
        <button type="submit" class="btn w-100 btn--primary h--40px">{{ translate('Update') }}</button>
    </div>
</form>
