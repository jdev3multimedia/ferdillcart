@extends('layouts.landing.app')
@section('title', translate('messages.deliveryman_registration'))


@section('content')

<?php
  $country=\App\Models\BusinessSetting::where('key','country')->first();
$countryCode= strtolower($country?$country->value:'auto');

?>
    <section class="about-section py-5 position-relative">
        <div class="container">
            <!-- Page Header -->
            <div class="section-header">
                <h2 class="title mb-2">{{translate("messages.Deliveryman")}} <span class="text--base">{{translate("messages.Application")}}</span></h2>
            </div>
            <!-- End Page Header -->
                <form class="validate-form" action="{{ route('deliveryman.store') }}" method="post" enctype="multipart/form-data" id="form-id">
                    @csrf
                    <div class="card __card mb-3">
                        <div class="card-header">
                            <h5 class="card-title">
                                <svg width="20" x="0" y="0" viewBox="0 0 460.8 460.8" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g><g><g>
                                        <path d="M230.432,239.282c65.829,0,119.641-53.812,119.641-119.641C350.073,53.812,296.261,0,230.432,0
                                            S110.792,53.812,110.792,119.641S164.604,239.282,230.432,239.282z" fill="#020202" data-original="#000000" class=""></path>
                                        <path d="M435.755,334.89c-3.135-7.837-7.314-15.151-12.016-21.943c-24.033-35.527-61.126-59.037-102.922-64.784
                                            c-5.224-0.522-10.971,0.522-15.151,3.657c-21.943,16.196-48.065,24.555-75.233,24.555s-53.29-8.359-75.233-24.555
                                            c-4.18-3.135-9.927-4.702-15.151-3.657c-41.796,5.747-79.412,29.257-102.922,64.784c-4.702,6.792-8.882,14.629-12.016,21.943
                                            c-1.567,3.135-1.045,6.792,0.522,9.927c4.18,7.314,9.404,14.629,14.106,20.898c7.314,9.927,15.151,18.808,24.033,27.167
                                            c7.314,7.314,15.673,14.106,24.033,20.898c41.273,30.825,90.906,47.02,142.106,47.02s100.833-16.196,142.106-47.02
                                            c8.359-6.269,16.718-13.584,24.033-20.898c8.359-8.359,16.718-17.241,24.033-27.167c5.224-6.792,9.927-13.584,14.106-20.898
                                            C436.8,341.682,437.322,338.024,435.755,334.89z" fill="#020202" data-original="#000000" class=""></path>
                                    </g>
                                </g>
                            </g>
                            </svg>{{ translate('messages.deliveryman_info') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group mb-3">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.first_name') }}
                                        <small
                                            class="text-danger">*</small> </label>
                                        <input type="text" name="f_name" class="form-control __form-control"
                                            placeholder="{{ translate('messages.first_name') }}" required
                                            value="{{ old('f_name') }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-3">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.last_name') }}
                                        <small
                                            class="text-danger">*</small> </label>
                                        <input type="text" name="l_name" class="form-control __form-control"
                                            placeholder="{{ translate('messages.last_name') }}"
                                            value="{{ old('l_name') }}" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group mb-3">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.email') }}
                                        <small
                                            class="text-danger">*</small> </label>
                                        <input type="email" name="email" class="form-control __form-control"
                                            placeholder="{{ translate('messages.Ex:') }} ex@example.com" value="{{ old('email') }}" required>
                                    </div>
                                </div>
                                <div class="col-sm-4" id="earning">
                                    <div class="form-group mb-3">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.deliveryman_type') }}
                                        <small
                                            class="text-danger">*</small> </label>
                                        <select name="earning" class="form-control __form-control">
                                            <option value="1">{{ translate('messages.freelancer') }}</option>
                                            <option value="0">{{ translate('messages.salary_based') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4" id="ref_code" style="display: none;">
                                    <div class="form-group mb-3">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.referral_code') }}
                                        </label>
                                        <input type="text" id="referral_code"  name="referral_code" class="form-control __form-control"
                                            placeholder="{{ translate('messages.Ex: STAKXPFIDK') }}"
                                            value="">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="form-group mb-3">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.zone') }}
                                        <small
                                            class="text-danger">*</small> </label>
                                        <select name="zone_id" class="form-control __form-control" required
                                            data-placeholder="{{ translate('messages.select_zone') }}">
                                            <option value="" readonly="true" hidden="true">{{ translate('messages.select_zone') }}</option>
                                            @foreach (\App\Models\Zone::active()->get() as $zone)
                                                @if (isset(auth('admin')->user()->zone_id))
                                                    @if (auth('admin')->user()->zone_id == $zone->id)
                                                        <option value="{{ $zone->id }}" selected>{{ $zone->name }}
                                                        </option>
                                                    @endif
                                                @else
                                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                      <label class="input-label"
                                                for="exampleFormControlInput1">{{ translate('messages.Vehicle') }}
                                            <small
                                            class="text-danger">*</small> </label>
                                            <select name="vehicle_id" class="form-control __form-control" required
                                                data-placeholder="{{ translate('messages.select_vehicle') }}">
                                                <option value="" readonly="true" hidden="true">{{ translate('messages.select_vehicle') }}</option>
                                                @foreach (\App\Models\DMVehicle::where('status',1)->get(['id','type']) as $v)
                                                            <option value="{{ $v->id }}" >{{ $v->type }}
                                                            </option>
                                                @endforeach
                                            </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-3">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.identity_type') }}
                                            <small
                                            class="text-danger">*</small> </label>
                                        <select name="identity_type" class="form-control __form-control">
                                            <option value="passport">{{ translate('messages.passport') }}</option>
                                            <option value="driving_license">{{ translate('messages.driving_license') }}</option>
                                            <option value="nid">{{ translate('messages.nid') }}</option>
                                            <option value="restaurant_id">{{ translate('messages.store_id') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-3">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.identity_number') }}
                                        <small
                                            class="text-danger">*</small> </label>
                                        <input type="text" name="identity_number" class="form-control __form-control"
                                            value="{{ old('identity_number') }}" placeholder="{{ translate('messages.Ex:') }} DH-23434-LS" required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label">{{ translate('messages.identity_image') }}
                                            <small
                                            class="text-danger">*</small>
                                        </label>
                                        <div>
                                            <div class="row" id="coba"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card __card mb-3">
                        <div class="card-header">
                            <h5 class="card-title">
                                <svg width="20" x="0" y="0" viewBox="0 0 460.8 460.8" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g><g><g>
                                        <path d="M230.432,239.282c65.829,0,119.641-53.812,119.641-119.641C350.073,53.812,296.261,0,230.432,0
                                            S110.792,53.812,110.792,119.641S164.604,239.282,230.432,239.282z" fill="#020202" data-original="#000000" class=""></path>
                                        <path d="M435.755,334.89c-3.135-7.837-7.314-15.151-12.016-21.943c-24.033-35.527-61.126-59.037-102.922-64.784
                                            c-5.224-0.522-10.971,0.522-15.151,3.657c-21.943,16.196-48.065,24.555-75.233,24.555s-53.29-8.359-75.233-24.555
                                            c-4.18-3.135-9.927-4.702-15.151-3.657c-41.796,5.747-79.412,29.257-102.922,64.784c-4.702,6.792-8.882,14.629-12.016,21.943
                                            c-1.567,3.135-1.045,6.792,0.522,9.927c4.18,7.314,9.404,14.629,14.106,20.898c7.314,9.927,15.151,18.808,24.033,27.167
                                            c7.314,7.314,15.673,14.106,24.033,20.898c41.273,30.825,90.906,47.02,142.106,47.02s100.833-16.196,142.106-47.02
                                            c8.359-6.269,16.718-13.584,24.033-20.898c8.359-8.359,16.718-17.241,24.033-27.167c5.224-6.792,9.927-13.584,14.106-20.898
                                            C436.8,341.682,437.322,338.024,435.755,334.89z" fill="#020202" data-original="#000000" class=""></path>
                                    </g>
                                </g>
                            </g>
                            </svg>{{ translate('messages.login_info') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="input-label" for="phone">{{ translate('messages.phone') }}</label>
                                        <div class="input-group">
                                            <input type="tel" name="phone" id="phone" placeholder="{{ translate('messages.Ex:') }} 017********"
                                                class="form-control __form-control" value="{{ old('tel') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.password') }}
                                            <small
                                            class="text-danger">*</small>  <span class="form-label-secondary" data-toggle="tooltip" data-placement="right"
        data-original-title="{{ translate('messages.Must_contain_at_least_one_number_and_one_uppercase_and_lowercase_letter_and_symbol,_and_at_least_8_or_more_characters') }}"><img src="{{ asset('/public/assets/admin/img/info-circle.svg') }}" alt="{{ translate('messages.Must_contain_at_least_one_number_and_one_uppercase_and_lowercase_letter_and_symbol,_and_at_least_8_or_more_characters') }}"></span></label>
                                        <input type="password" name="password" class="form-control __form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="{{ translate('messages.Must_contain_at_least_one_number_and_one_uppercase_and_lowercase_letter_and_symbol,_and_at_least_8_or_more_characters') }}"
                                        placeholder="{{ translate('messages.password_length_placeholder', ['length' => '8+']) }}"
                                        aria-label="8+ characters required"
                                            value="{{ old('password') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex">
                                <div class="col-lg-6">
                                    <div class="form-group pt-3 mb-5">
                                        <label  class="input-label">{{ translate('messages.deliveryman_image') }}<small
                                            class="text-danger">* ( {{ translate('messages.ratio') }} 1:1 )</small></label>
                                        <label class="position-relative">
                                            <img class="__register-img mb-3 image--border h-140px" id="viewer"
                                                src="{{ asset('public/assets/admin/img/upload-img.png') }}"
                                                alt="delivery-man image" />
                                            <div class="icon-file-group">
                                                <div class="icon-file">
                                                    <input type="file" name="image" id="customFileEg1" class="form-control __form-control single_file_input"
                                                    accept=".webp, .jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                                    <img src="{{asset('/public/assets/admin/img/pen.png')}}" alt="">
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 col-12">
                                    {{-- recaptcha --}}
                                    @php($recaptcha = \App\CentralLogics\Helpers::get_business_settings('recaptcha'))
                                    @if(isset($recaptcha) && $recaptcha['status'] == 1)
                                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                                    @else
                                        <div class="row p-2">
                                            <div class="col-6 pr-0">
                                                <input type="text" class="form-control" name="custome_recaptcha"
                                                        id="custome_recaptcha" required placeholder="{{\__('Enter recaptcha value')}}" autocomplete="off" value="{{env('APP_DEBUG')?session('six_captcha'):''}}">
                                            </div>
                                            <div class="col-6" style="background-color: #FFFFFF; border-radius: 5px;">
                                                <img src="<?php echo $custome_recaptcha->inline(); ?>" style="width: 100%; border-radius: 4px;"/>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="cmn--btn border-0 outline-0" id="signInBtn">{{ translate('messages.submit') }}</button>
                    </div>
                </form>
        </div>

    </section>

@endsection

@push('script_2')

    <script>
        class FormValidation {
            constructor(formSelector = '.validate-form') {
                this.formSelector = formSelector;
                this.init();
            }

            init() {
                document.addEventListener('DOMContentLoaded', () => {
                    this.attachValidators();
                    this.initPasswordValidation();
                });
            }

            attachValidators() {
                const forms = document.querySelectorAll(this.formSelector);
                forms.forEach(form => {
                    if (form.dataset.validationInitialized === "true") return;

                    form.setAttribute('novalidate', true);

                    form.addEventListener('submit', (e) => {
                        let isFormValid = FormValidation.validateForm(form);
                        let isFileValid = true;

                        if (window.fileValidators) {
                            const formValidators = window.fileValidators.filter(validator =>
                                form.contains(validator.input)
                            );
                            if (formValidators.length > 0 && !FileUploadValidator.validateAll(formValidators)) {
                                isFileValid = false;
                            }
                        }

                        if (!isFormValid || !isFileValid) {
                            e.preventDefault();
                            e.stopPropagation();
                        }
                    });

                    form.querySelectorAll('input, textarea, select').forEach(input => {
                        input.addEventListener('input', () => {
                            FormValidation.validateInput(input);
                        });
                        input.addEventListener('change', () => {
                            FormValidation.validateInput(input);
                        });
                    });

                    form.dataset.validationInitialized = "true";
                });
            }

            initPasswordValidation() {
                let passwordInput = document.getElementById("signupSrPassword");
                let confirmPasswordInput = document.querySelector("input[name='confirmPassword'], input[name='confirm-password']");
                if (!passwordInput) {
                    passwordInput = document.querySelector("input[name='password']");
                }

                if (!passwordInput) return;

                let rulesContainer = document.getElementById("password-rules");

                if (!rulesContainer) {
                    rulesContainer = document.createElement('div');
                    rulesContainer.id = 'password-rules';
                    rulesContainer.className = 'gap-4 mt-2 small list-unstyled text-muted';
                    rulesContainer.style.display = 'none';
                    rulesContainer.innerHTML = `
                        <ul class="fs-12 d-flex flex-wrap gap-1 list-unstyled">
                            <li id="rule-length"><i class="text-danger">&#10060;</i> 8+ characters</li>
                            <li id="rule-lower"><i class="text-danger">&#10060;</i> Lowercase letter</li>
                            <li id="rule-upper"><i class="text-danger">&#10060;</i> Uppercase letter</li>
                            <li id="rule-number"><i class="text-danger">&#10060;</i> Number</li>
                            <li id="rule-symbol"><i class="text-danger">&#10060;</i> Symbol</li>
                        </ul>
                    `;
                    const container = passwordInput.closest('.form-group') || passwordInput.parentNode;
                    container.appendChild(rulesContainer);
                }

                const rules = {
                    length: rulesContainer.querySelector("#rule-length"),
                    lower: rulesContainer.querySelector("#rule-lower"),
                    upper: rulesContainer.querySelector("#rule-upper"),
                    number: rulesContainer.querySelector("#rule-number"),
                    symbol: rulesContainer.querySelector("#rule-symbol"),
                };

                passwordInput.addEventListener("input", function () {
                    const val = passwordInput.value;

                    if (val.length > 0) {
                        rulesContainer.style.display = "block";
                    } else {
                        rulesContainer.style.display = "none";
                    }

                    FormValidation.updateRule(rules.length, val.length >= 8);
                    FormValidation.updateRule(rules.lower, /[a-z]/.test(val));
                    FormValidation.updateRule(rules.upper, /[A-Z]/.test(val));
                    FormValidation.updateRule(rules.number, /\d/.test(val));
                    FormValidation.updateRule(rules.symbol, /[!@#$%^&*(),.?":{}|<>]/.test(val));
                });

                passwordInput.addEventListener("blur", function () {
                    if (passwordInput.value.length === 0) {
                        rulesContainer.style.display = "none";
                    }
                });

                if (confirmPasswordInput) {
                    const validateMatch = () => {
                        if (confirmPasswordInput.value && passwordInput.value !== confirmPasswordInput.value) {
                            FormValidation.showError(confirmPasswordInput, confirmPasswordInput.getAttribute('data-msg') || 'Password does not match');
                        } else {
                            FormValidation.clearError(confirmPasswordInput);
                        }
                    };

                    confirmPasswordInput.addEventListener('input', validateMatch);
                    passwordInput.addEventListener('input', () => {
                        if (confirmPasswordInput.value) validateMatch();
                    });
                }
            }

            static updateRule(element, isValid) {
                if (!element) return;
                const icon = element.querySelector("i");
                if (icon) {
                    icon.className = isValid ? "text-success" : "text-danger";
                    icon.innerHTML = isValid ? "&#10004;" : "&#10060;"; // ✓ or ✗
                }
            }

            static validateForm(form) {
                let isValid = true;
                const inputs = form.querySelectorAll('input, textarea, select');

                inputs.forEach(input => {
                    if (!FormValidation.validateInput(input)) {
                        isValid = false;
                    }
                });

                return isValid;
            }

            static validateInput(input) {
                if (input.type === 'hidden' || input.disabled) return true;

                let isValid = true;
                let errorMessage = '';

                FormValidation.clearError(input);

                if (input.type !== 'file' && input.hasAttribute('required') && !input.value.trim()) {
                    isValid = false;
                    errorMessage = 'This field is required.';
                }

                else if (input.type === 'email' && input.value.trim()) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(input.value.trim())) {
                        isValid = false;
                        errorMessage = 'Please enter a valid email address.';
                    }
                }

                else if ((input.name === 'confirmPassword' || input.name === 'confirm-password') && input.value.trim()) {
                    const passwordInput = document.querySelector("input[name='password']");
                    if (passwordInput && input.value !== passwordInput.value) {
                        isValid = false;
                        errorMessage = input.getAttribute('data-msg') || 'Password does not match.';
                    }
                }

                if (!isValid) {
                    FormValidation.showError(input, errorMessage);
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                }

                return isValid;
            }

            static showError(input, message) {
                const formGroup = input.closest('.form-group');
                const container = formGroup ? formGroup : input.parentNode;

                const inputName = input.getAttribute('name');
                let errorDiv = container.querySelector(`.form-validation-error[data-for="${inputName}"]`);

                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'form-validation-error text-danger mt-1 small';
                    errorDiv.setAttribute('data-for', inputName);

                    const inputGroup = input.closest('.input-group');

                    if (inputGroup) {
                        inputGroup.insertAdjacentElement('afterend', errorDiv);
                    } else {
                        input.insertAdjacentElement('afterend', errorDiv);
                    }
                }

                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
            }

            static clearError(input) {
                const formGroup = input.closest('.form-group');
                const container = formGroup ? formGroup : input.parentNode;

                const inputName = input.getAttribute('name');
                const errorDiv = container.querySelector(`.form-validation-error[data-for="${inputName}"]`);

                if (errorDiv) {
                    errorDiv.remove();
                }
            }
        }

        window.FormValidation = FormValidation;
        window.formValidation = new FormValidation();

        (function () {
            if (typeof window.FileUploadValidator !== "undefined") {
                return;
            }

            class FileUploadValidator {
                constructor(inputElement, options = {}) {
                    this.config = {
                        maxSize: 2, // MB
                        allowedTypes: ['webp', 'jpg', 'jpeg', 'png', 'gif'],
                        errorElementId: null,
                        required: true,
                        ...options
                    };

                    this.input = inputElement;

                    if (!this.input) {
                        console.error('File input element not found');
                        return;
                    }

                    this.errorElement = this.initErrorElement();
                    this.attachEventListeners();
                }

                initErrorElement() {
                    if (this.config.errorElementId) {
                        return document.getElementById(this.config.errorElementId);
                    }

                    const parentDiv = this.input.closest('.icon-file');
                    if(parentDiv){
                        let errorElement = parentDiv.parentElement.nextElementSibling;
                        if (!errorElement || !errorElement.classList.contains('file-upload-error')) {
                            errorElement = document.createElement('div');
                            errorElement.className = 'file-upload-error text-danger mt-1 small';
                            parentDiv.parentElement.after(errorElement);
                        }
                        return errorElement;
                    }

                    // Fallback
                    const parent = this.input.parentElement;
                    let errorElement = parent.nextElementSibling;
                    if (!errorElement || !errorElement.classList.contains('file-upload-error')) {
                        errorElement = document.createElement('div');
                        errorElement.className = 'file-upload-error text-danger mt-1 small';
                        parent.after(errorElement);
                    }
                    return errorElement;
                }

                attachEventListeners() {
                    this.input.addEventListener('change', () => {
                        this.validate();
                        if (this.input.files && this.input.files.length === 0) {
                            this.removePreview();
                        }
                    });
                }

                removePreview() {
                    this.input.value = '';
                    const viewer = document.getElementById('viewer');
                    if (viewer) {
                        viewer.src = '{{ asset('public/assets/admin/img/upload-img.png') }}';
                    }
                }

                clearError() {
                    if (this.errorElement) {
                        this.errorElement.textContent = '';
                        this.errorElement.style.display = 'none';
                    }
                    this.input.classList.remove('is-invalid');
                }

                showError(message) {
                    if (this.errorElement) {
                        this.errorElement.textContent = message;
                        this.errorElement.style.display = 'block';
                    }
                    this.input.classList.add('is-invalid');
                    return false;
                }

                validate() {
                    this.clearError();

                    if (!this.input.files || this.input.files.length === 0) {
                        if (this.config.required) {
                            return this.showError('{{ translate('messages.please_select_image') }}');
                        }
                        return true;
                    }

                    const file = this.input.files[0];

                    const fileExtension = file.name.split('.').pop().toLowerCase();
                    if (!this.config.allowedTypes.includes(fileExtension)) {
                        return this.showError(`{{ translate('messages.invalid_file_type') }} ${this.config.allowedTypes.join(', ')}`);
                    }

                    const fileSizeMB = file.size / (1024 * 1024);
                    if (fileSizeMB > this.config.maxSize) {
                        return this.showError(`{{ translate('messages.file_size_too_big. ') }}{{ translate('messages.max_file_size_is') }} ${this.config.maxSize}MB.`);
                    }

                    return true;
                }

                clear() {
                    this.input.value = '';
                    this.clearError();
                    this.removePreview();
                }

                static initByClass(className, options = {}) {
                    const inputs = document.querySelectorAll(`.${className}`);
                    const validators = [];

                    inputs.forEach(input => {
                        let maxSize = options.maxSize || 2;
                        if (input.dataset.maxSize) {
                            maxSize = parseFloat(input.dataset.maxSize);
                        }

                        let allowedTypes = options.allowedTypes || ['webp', 'jpg', 'jpeg', 'png', 'gif'];

                        if (input.dataset.allowedTypes) {
                            allowedTypes = input.dataset.allowedTypes.split(',').map(t => t.trim());
                        } else if (input.hasAttribute('accept')) {
                            const acceptTypes = input.getAttribute('accept')
                                .split(',')
                                .map(type => type.trim().replace(/^\./, '').toLowerCase())
                                .filter(type => type.length > 0);

                            if (acceptTypes.length > 0) {
                                allowedTypes = acceptTypes;
                            }
                        }

                        const elementOptions = {
                            maxSize: maxSize,
                            allowedTypes: allowedTypes,
                            required: input.hasAttribute('required')
                        };

                        validators.push(new FileUploadValidator(input, elementOptions));
                    });

                    return validators;
                }

                static validateAll(validators) {
                    let allValid = true;
                    validators.forEach(validator => {
                        if (validator && !validator.validate()) {
                            allValid = false;
                        }
                    });
                    return allValid;
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                window.fileValidators = FileUploadValidator.initByClass('single_file_input', {
                    allowedTypes: ['webp', 'jpg', 'jpeg', 'png', 'gif']
                });
            });

            window.validateFileInputs = function () {
                return FileUploadValidator.validateAll(window.fileValidators || []);
            };
            window.FileUploadValidator = FileUploadValidator;
        })();

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function() {
            readURL(this);
        });

        $('select[name="earning"]').on('change', function() {
            if (this.value === '1') {
                $('#ref_code').show();
            } else {
                $('#ref_code').hide();
                $('#referral_code').val('');
            }
        });

        $(document).ready(function() {
            if ($('select[name="earning"]').val() === '1') {
                $('#ref_code').show();
            } else {
                $('#ref_code').hide();
                $('#referral_code').val('');
            }
        });

    </script>

    <script src="{{ asset('public/assets/admin/js/spartan-multi-image-picker.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'identity_image[]',
                maxCount: 5,
                rowHeight: '120px',
                groupClassName: 'col-lg-2 col-md-4 col-sm-4 col-6',
                maxFileSize: '{{ MAX_FILE_SIZE * 1024 * 1024 }}',
                placeholderImage: {
                    image: '{{ asset('public/assets/admin/img/upload-img.png') }}',
                    width: '100%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {

                },
                onRenderedPreview: function(index) {

                },
                onRemoveRow: function(index) {

                },
                onExtensionErr: function(index, file) {
                    toastr.error('{{ translate('messages.please_only_input_png_or_jpg_type_file') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function(index, file) {
                    toastr.error('{{ translate('messages.file_size_too_big') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });

    </script>


    {{-- recaptcha scripts start --}}
    @if(isset($recaptcha) && $recaptcha['status'] == 1)
        <script src="https://www.google.com/recaptcha/api.js?render={{$recaptcha['site_key']}}"></script>
    @endif
    @if(isset($recaptcha) && $recaptcha['status'] == 1)
        <script>
            $(document).ready(function() {
                $('#signInBtn').click(function (e) {
                    e.preventDefault();

                    if (window.FormValidation && !window.FormValidation.validateForm(document.getElementById('form-id'))) {
                        return;
                    }

                    if (window.validateFileInputs && !window.validateFileInputs()) {
                        return;
                    }

                    if (typeof grecaptcha === 'undefined') {
                        toastr.error('Invalid recaptcha key provided. Please check the recaptcha configuration.');
                        return;
                    }
                    grecaptcha.ready(function () {
                        grecaptcha.execute('{{$recaptcha['site_key']}}', {action: 'submit'}).then(function (token) {
                            $('#g-recaptcha-response').value = token;
                            $('#form-id').submit();
                        });
                    });
                    window.onerror = function (message) {
                        var errorMessage = 'An unexpected error occurred. Please check the recaptcha configuration';
                        if (message.includes('Invalid site key')) {
                            errorMessage = 'Invalid site key provided. Please check the recaptcha configuration.';
                        } else if (message.includes('not loaded in api.js')) {
                            errorMessage = 'reCAPTCHA API could not be loaded. Please check the recaptcha API configuration.';
                        }
                        toastr.error(errorMessage)
                        return true;
                    };
                });
            });
        </script>
    @endif
    {{-- recaptcha scripts end --}}
@endpush
