       <div class="col-lg-6">
           <div class="card shadow--card-2 border-0">
               <div class="card-body ">
                   @php($language = \App\CentralLogics\Helpers::get_business_settings('language'))
                   @php($product = isset($product) ? $product : null)
                   <div class="js-nav-scroller hs-nav-scroller-horizontal">
                       <ul class="nav nav-tabs mb-4">
                           <li class="nav-item">
                               <a class="nav-link lang_link active" href="#"
                                   id="default-link">{{ translate('Default') }}</a>
                           </li>
                           @foreach ($language ?? [] as $lang)
                               <li class="nav-item">
                                   <a class="nav-link lang_link " href="#"
                                       id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                               </li>
                           @endforeach
                       </ul>
                   </div>

                   <div class="lang_form" id="default-form">
                       <div class="form-group">
                           <div class="justify-content-between d-flex">
                               <label class="input-label" for="default_name">{{ translate('messages.name') }}
                                   ({{ translate('Default') }}) <span class="form-label-secondary text-danger"
                                       data-toggle="tooltip" data-placement="right"
                                       data-original-title="{{ translate('messages.Required.') }}"> *
                                   </span>
                               </label>
                            @if (isset($openai_config) && data_get($openai_config, 'status') == 1)
                            <button type="button" class="btn bg-white text-primary opacity-1 generate_btn_wrapper p-0 mb-2 auto_fill_title"
                                id="title-default-action-btn" data-type="default"
                                data-error="{{ translate('Please provide a product name so the AI can generate a suitable title.') }}"
                                data-lang="{{ \App\CentralLogics\Helpers::system_default_language() }}"
                                data-route="{{ route('admin.product.title-auto-fill') }}">
                                <div class="btn-svg-wrapper">
                                    <img width="18" height="18" class=""
                                        src="{{ asset('public/assets/admin/img/svg/blink-right-small.svg') }}" alt="">
                                </div>
                                <span class="ai-text-animation d-none" role="status">
                                    {{ translate('Just_a_second') }}
                                </span>
                                <span class="btn-text">{{ translate('Generate') }}</span>
                            </button>
                            @endif

                           </div>
                           <div class="error-wrapper">
                               <div class="outline-wrapper">
                                   <input type="text" name="name[]" id="default_name" class="form-control"
                                       value="{{ $product?->getRawOriginal('name') ?? old('name.0') }}"
                                       placeholder="{{ translate('messages.new_food') }}" required>
                               </div>
                           </div>
                       </div>
                       <input type="hidden" name="lang[]" value="default">
                       <div class="form-group mb-0 des_wrapper">

                           <div class="justify-content-between d-flex">
                               <label class="input-label"
                                   for="exampleFormControlInput1">{{ translate('messages.short_description') }}
                                   ({{ translate('Default') }}) <span class="form-label-secondary text-danger"
                                       data-toggle="tooltip" data-placement="right"
                                       data-original-title="{{ translate('messages.Required.') }}"> *
                                   </span></label>

                                   @if (isset($openai_config) && data_get($openai_config, 'status') == 1)
                                   <button type="button" class="btn bg-white text-primary opacity-1 generate_btn_wrapper p-0 mb-2 auto_fill_description"
                                       id="description-default-action-btn" data-type="default"
                                       data-error="{{ translate('Please provide a product description so the AI can generate a description.') }}"
                                       data-lang="{{ \App\CentralLogics\Helpers::system_default_language() }}"
                                       data-route="{{ route('admin.product.description-auto-fill') }}">
                                       <div class="btn-svg-wrapper">
                                            <img width="18" height="18" class=""
                                                src="{{ asset('public/assets/admin/img/svg/blink-right-small.svg') }}" alt="">
                                        </div>
                                        <span class="ai-text-animation d-none" role="status">
                                            {{ translate('Just_a_second') }}
                                        </span>
                                        <span class="btn-text">{{ translate('Generate') }}</span>
                                   </button>
                                   @endif

                           </div>

                           <div class="error-wrapper">
                               <div class="outline-wrapper">
                                    <textarea type="text" rows="5" name="description[]" maxlength="1200" id="description-default" class="form-control ckeditor min-height-154px" required>{{ $product?->getRawOriginal('description') ?? old('description.0') }}</textarea>
                               </div>
                           </div>

                       </div>
                   </div>

                   @foreach ($language ?? [] as $key => $lang)
                       <?php

                       if ($product && count($product['translations'])) {
                           $translate = [];
                           foreach ($product['translations'] as $t) {
                               if ($t->locale == $lang && $t->key == 'name') {
                                   $translate[$lang]['name'] = $t->value;
                               }
                               if ($t->locale == $lang && $t->key == 'description') {
                                   $translate[$lang]['description'] = $t->value;
                               }
                           }
                       }
                       ?>

                       <div class="d-none lang_form" id="{{ $lang }}-form">
                           <div class="form-group">

                               <div class="justify-content-between d-flex">
                                   <label class="input-label"
                                       for="{{ $lang }}_name">{{ translate('messages.name') }}
                                       ({{ strtoupper($lang) }})
                                   </label>

                                @if (isset($openai_config) && data_get($openai_config, 'status') == 1)

                                <button type="button" class="btn bg-white text-primary opacity-1 generate_btn_wrapper auto_fill_title"
                                    id="title-{{ $lang }}-action-btn" data-lang="{{ $lang }}"
                                    data-error="{{ translate('Please provide a product name so the AI can generate a suitable title or description.') }}"
                                    data-route="{{ route('admin.product.title-auto-fill') }}">
                                    <div class="btn-svg-wrapper">
                                        <img width="18" height="18" class=""
                                            src="{{ asset('public/assets/admin/img/svg/blink-right-small.svg') }}" alt="">
                                    </div>
                                    <span class="ai-text-animation d-none" role="status">
                                        {{ translate('Just_a_second') }}
                                    </span>
                                    <span class="btn-text">{{ translate('Generate') }}</span>
                                </button>
                                @endif
                               </div>

                               <div class="error-wrapper">
                                   <input type="text" name="name[]" id="{{ $lang }}_name"
                                       value="{{ isset($translate[$lang]['name']) ? $translate[$lang]['name'] : old('name.' . $key + 1) }}"
                                       class="form-control" placeholder="{{ translate('messages.new_food') }}">

                               </div>
                           </div>
                           <input type="hidden" name="lang[]" value="{{ $lang }}">
                           <div class="form-group mb-0">
                               <div class="justify-content-between d-flex">
                                   <label class="input-label"
                                       for="exampleFormControlInput1">{{ translate('messages.short_description') }}

                                       ({{ strtoupper($lang) }})</label>
                                      @if (isset($openai_config) && data_get($openai_config, 'status') == 1)
                                      <button type="button" class="btn bg-white text-primary opacity-1 generate_btn_wrapper auto_fill_description"
                                          id="description-default-action-btn"
                                          data-error="{{ translate('Please provide a product description so the AI can generate a description.') }}"
                                          data-lang="{{ $lang }}"
                                          data-route="{{ route('admin.product.description-auto-fill') }}">
                                            <div class="btn-svg-wrapper">
                                                <img width="18" height="18" class=""
                                                    src="{{ asset('public/assets/admin/img/svg/blink-right-small.svg') }}" alt="">
                                            </div>
                                            <span class="ai-text-animation d-none" role="status">
                                                {{ translate('Just_a_second') }}
                                            </span>
                                            <span class="btn-text">{{ translate('Generate') }}</span>
                                      </button>

                                       @endif
                               </div>

                               <div class="error-wrapper">
                                   <textarea type="text" name="description[]" id="description-{{ $lang }}" maxlength="1200"
                                       class="form-control ckeditor min-height-154px">{{ isset($translate[$lang]['description']) ? $translate[$lang]['description'] : old('description.' . $key + 1) }}</textarea>
                               </div>
                           </div>
                       </div>
                   @endforeach
               </div>

           </div>
       </div>
