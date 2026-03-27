


<form action="{{  route('admin.refund.reason-update') }}" method="post" class="d-flex flex-column h-100">
         @csrf
        @method('put')
    <div>
        <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
            <h3 class="mb-0">{{ translate('Edit_Refund_Reason') }}</h2>
                <button type="button"
                        class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary offcanvas-close fz-15px p-0"
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
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{ translate('Refund Reason') }}
                                    ({{ translate('messages.default') }})
                                    <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write the related refund reasons that customers must select to request a refund. ') }}">
                                            <i class="tio-info text-muted"></i>
                                        </span>
                                    <span class="form-label-secondary text-danger" data-toggle="tooltip"
                                          data-placement="right"
                                          data-original-title="{{ translate('messages.Required.') }}"> *
                                    </span>

                                </label>
                                <textarea id="reason" type="text" rows="5" class="form-control " name="reason[]" maxlength="150"
                                    placeholder="{{ translate('Ex:Enter_the_reason') }}">{{ $reason?->getRawOriginal('reason') }}</textarea>


                                         <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/150</span>
                            </div>
                            <input type="hidden" name="lang[]" value="default">
                            @foreach ($language as $key => $lang)
                         <?php
                            if($reason?->translations){
                                $translate = [];
                                foreach($reason?->translations as $t)
                                {
                                    if($t->locale == $lang && $t->key=="reason"){
                                        $translate[$lang]['reason'] = $t->value;
                                    }
                                }
                            }
                            ?>

                                <div class="form-group d-none lang_form1" id="{{ $lang }}-form1">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{ translate('Refund Reason') }}
                                        ({{ strtoupper($lang) }})
                                    </label>



                               <textarea id="reason{{ $lang }}" type="text" rows="5" class="form-control" name="reason[]" maxlength="150"
                                            placeholder="{{ translate('Ex:Enter_the_reason') }}">{{ $translate[$lang]['reason'] ?? null }}</textarea>
                                             <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/150</span>
                                </div>
                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                            @endforeach

                        @endif
                        <input type="hidden" name="reason_id" value="{{ $reason->id }}" />
                    </div>

                </div>

            </div>

        </div>
    </div>
    <div class="align-items-center bg-white bottom-0 d-flex gap-3 justify-content-center mt-auto offcanvas-footer p-3 position-sticky">
        <button type="button" class="btn w-100 btn--secondary offcanvas-close h--40px">{{ translate('Cancel') }}</button>
        <button type="submit" class="btn w-100 btn--primary h--40px">{{ translate('Update') }}</button>
    </div>
</form>
