


<form action="{{ route('admin.addon.addon-category-update', [$addonCategory['id']]) }}" method="post" class="d-flex flex-column h-100">
    @method('PUT')
    @csrf
    <div>
        <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
            <h3 class="mb-0">{{ translate('Edit_Addon_Category') }}</h2>
                <button type="button"
                    class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"
                    aria-label="Close">&times;</button>
        </div>
        <div class="custom-offcanvas-body p-20">
            <div class="bg--secondary rounded p-20 mb-20">
                <div class="mb-15">
                    <h4 class="mb-0">{{ translate('Availability') }}</h4>
                    <p class="fz-12px">{{ translate('If_you_turn_off_this_status_this_addon_category_will_not_be_available') }}
                    </p>
                </div>
                <label class="border d-flex align-items-center bg-white-n justify-content-between rounded p-10px px-3">
                    {{ translate('Status') }}
                    <div class="toggle-switch ml-auto justify-content-end toggle-switch-sm" for="status">
                        <input type="checkbox" name="status" value="1" {{ $addonCategory['status'] ? 'checked' : '' }}
                            class="toggle-switch-input" id="status">
                        <span class="toggle-switch-label">
                            <span class="toggle-switch-indicator"></span>
                        </span>
                    </div>
                </label>
            </div>




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
                                <label class="input-label"
                                    for="exampleFormControlInput1">{{ translate('messages.Category_Name') }}
                                    ({{ translate('messages.default') }})
                                    <span class="form-label-secondary text-danger" data-toggle="tooltip"
                                        data-placement="right"
                                        data-original-title="{{ translate('messages.Required.') }}"> *
                                    </span>

                                </label>
                                <input type="text" name="name[]"
                                    value="{{ $addonCategory?->getRawOriginal('name') }}" class="form-control"
                                    placeholder="{{ translate('messages.new_category') }}" maxleng="255">
                            </div>
                            <input type="hidden" name="lang[]" value="default">
                            @foreach ($language as $key => $lang)
                                <?php
                                if (count($addonCategory['translations'])) {
                                    $translate = [];
                                    foreach ($addonCategory['translations'] as $t) {
                                        if ($t->locale == $lang && $t->key == 'name') {
                                            $translate[$lang]['name'] = $t->value;
                                        }
                                    }
                                }
                                ?>

                                <div class="form-group d-none lang_form1" id="{{ $lang }}-form1">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{ translate('messages.Category_Name') }}
                                        ({{ strtoupper($lang) }})
                                    </label>
                                    <input type="text" name="name[]" value="{{ $translate[$lang]['name'] ?? '' }}"
                                        class="form-control"
                                        placeholder="{{ translate('messages.Type_Category_Name') }}" maxlength="191">
                                </div>
                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                            @endforeach
                        @else
                            <div class="form-group">
                                <label class="input-label"
                                    for="exampleFormControlInput1">{{ translate('messages.Category_Name') }}</label>
                                <input type="text" name="name" class="form-control"
                                    placeholder="{{ translate('messages.new_category') }}"
                                    value="{{ $addonCategory?->getRawOriginal('name') }}" maxlength="191">
                            </div>
                            <input type="hidden" name="lang[]" value="default">
                        @endif

                    </div>

                </div>
                @if ($categoryWiseTax)
                    <div class="row">

                        <div class="col-12">
                            <span class="mb-2 d-block title-clr fw-normal">{{ translate('Select Tax Rate') }}</span>
                            <select name="tax_ids[]" required id="" class="form-control js-select2-custom1"
                                multiple="multiple" placeholder="Type & Select Tax Rate">
                                @foreach ($taxVats as $taxVat)
                                    <option {{ in_array($taxVat->id, $taxVatIds) ? 'selected' : '' }}
                                        value="{{ $taxVat->id }}"> {{ $taxVat->name }}
                                        ({{ $taxVat->tax_rate }}%)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
    <div class="align-items-center bg-white bottom-0 d-flex gap-3 justify-content-center mt-auto offcanvas-footer p-3 position-sticky">
        <button type="button" class="btn w-100 btn--secondary offcanvas-close h--40px">{{ translate('Cancel') }}</button>
        <button type="submit" class="btn w-100 btn--primary h--40px">{{ translate('Update') }}</button>
    </div>
</form>

