$(function () {
     // --- Custom rules
    $.validator.addMethod("strongPassword", function (value, element) {
        return this.optional(element) ||
            /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(value);
    });

    $.validator.addMethod("fileSize", function (value, element, maxSize) {
        if (!element.files || element.files.length === 0) return true;

        for (let i = 0; i < element.files.length; i++) {
            if (element.files[i].size > maxSize) {
                return false;
            }
        }
        return true;
    }, function (params, element) {
        return $("#text-validate-translate").data("file-size-larger");
    });

    $.validator.addMethod("maxTextLength", function(value, element, max) {
        if (!value) return true; 
        return value.length <= max;
    }, function(params, element) {
        return $("#text-validate-translate").data("max-limit-crossed");
    });

    // --- scroll to error field
    async function scrollToErrorField(field) {
        if (!field) return;

        const $field = $(field);
        const $scrollTarget = field.type === "file"
            ? ($field.closest(".error-wrapper").length ? $field.closest(".error-wrapper") : $field)
            : $field;

        const revealPromises = [];

        $scrollTarget.parents().each(function () {
            const $parent = $(this);
            const parentClasses = $parent[0].classList;

            // --- Tab ---
            if ($parent.hasClass("tab-pane") && !$parent.hasClass("active")) {
                const id = $parent.attr("id");
                const $tabTrigger = $(
                    `[data-bs-toggle="tab"][href="#${id}"],[data-bs-toggle="tab"][data-bs-target="#${id}"],` +
                    `[data-toggle="tab"][href="#${id}"],[data-toggle="tab"][data-target="#${id}"]`
                );
                if ($tabTrigger.length) {
                    $tabTrigger.trigger("click");
                    revealPromises.push(new Promise(res => {
                        $parent.one("shown.bs.tab", res);
                        setTimeout(res, 300);
                    }));
                }
            }

            // --- Collapse ---
            if ($parent.hasClass("collapse") && !$parent.hasClass("show")) {
                const id = $parent.attr("id");
                const $collapseTrigger = $(
                    `[data-bs-toggle="collapse"][data-bs-target="#${id}"],` +
                    `[data-toggle="collapse"][data-target="#${id}"]`
                );
                if ($collapseTrigger.length) {
                    $collapseTrigger.trigger("click");
                    revealPromises.push(new Promise(res => {
                        $parent.one("shown.bs.collapse", res);
                        setTimeout(res, 300);
                    }));
                }
            }

            // --- Custom Lang Tab ---
            if ($parent.hasClass("lang_form") && $parent.hasClass("d-none")) {
                $(".lang_form").addClass("d-none");
                $(".lang_link").removeClass("active");
                const tabId = $parent.attr("id");
                $("#" + tabId).removeClass("d-none");
                $("#" + tabId.replace("-form", "-link")).addClass("active");
                revealPromises.push(Promise.resolve());
            }

            // --- FLoating Hidden Field ---
            if (parentClasses.contains("floating--date") && !parentClasses.contains("active")) {
                const toggler = $parent.parent()[0]?.querySelector(".floating-date-toggler");
                if (toggler) {
                    toggler.click();
                    revealPromises.push(new Promise(res => {
                        const checkActive = () => {
                            if ($parent.hasClass("active")) {
                                res(); 
                            } else {
                                setTimeout(checkActive, 50);
                            }
                        };
                        checkActive();
                    }));
                }
            }


            // --- Hidden div show ---
            if ($parent.is(":hidden")) $parent.show();
        });

        await Promise.all(revealPromises);

        setTimeout(() => {
            if ($scrollTarget.is(":visible")) {
                $scrollTarget[0].scrollIntoView({ behavior: "smooth", block: "center" });
                try { field.focus({ preventScroll: true }); } catch (e) {}
            }
        }, 200);
    }


    $(".custom-validation").each(function () {
        let $form = $(this);
        let fileRules = {};
        
         $form.find("input, textarea, select").each(function (i) {
            const $field = $(this);
            const rawName = $field.attr("name");
            if (!rawName) return;

            let uniqueName = rawName;
            if (/\[\]$/.test(rawName)) uniqueName = rawName.replace(/\[\]$/, `[${i}]`);

            $field.attr("data-orig-name", rawName);   
            $field.attr("data-unique-name", uniqueName);
            
            if ($field.attr("type") === "file") {
                let bytes = 2 * 1024 * 1024;
                const maxSize = $field.data("max-size");
                if (maxSize) {
                    const value = Number(maxSize);
                    if (!isNaN(value)) bytes = value * 1024 * 1024;
                }

                fileRules[rawName] = { fileSize: bytes };
            }
        });


        $form.validate({
            ignore: ":not(.error-wrapper :input)",
            rules: $.extend({}, fileRules),
            messages: {
                email: { email: "Please enter a valid email" },
                password: { 
                    required: $("#text-validate-translate").data("required"),
                    strongPassword: $("#text-validate-translate").data("password-validation")
                },
                confirmPassword: { 
                    required: $("#text-validate-translate").data("required"),
                    equalTo: $("#text-validate-translate").data("passwords-do-not-match")
                }
            },
            errorPlacement: function(error, element) {
                var $wrap = element.closest('.error-wrapper');

                if (!error.text().trim()) return;

                if ($wrap.length && $wrap.find('.text-counter').length && error.text() === $("#text-validate-translate").data("max-limit-crossed")) {
                    return; 
                }

                if ($wrap.length) $wrap.append(error);
                else element.after(error);
            },

            invalidHandler: function (event, validator) {
                if (validator.errorList.length) {
                    scrollToErrorField(validator.errorList[0].element);
                }
            },
            submitHandler: function(form) {
                
                let $form = $(form);

                if ($form.data("ajax") === true) {
                    let formData = new FormData(form);
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#answers').html(response);
                        },
                        error: function(xhr) {
                            alert($("#text-validate-translate").data("something-went-wrong"));
                        }
                    });
                } else {
                    form.submit();
                }
            }
        });

        $form.find("input, textarea, select").each(function () {
            const $field = $(this);
            let type = $field.attr("type") || ($field.is("textarea") ? "textarea" : "select");

            if ($field.prop("required")) $field.rules("add", { required: true });

            if (type === "text" || $field.is("textarea")) {
                let maxLen = $field.is("[maxlength]") ? parseInt($field.attr("maxlength"), 10) : NaN;
                if (isNaN(maxLen) || maxLen <= 0) maxLen = $field.is("textarea") ? 500 : 255;

                $field.rules("add", { maxTextLength: maxLen });

                const $counter = $field.closest(".error-wrapper").find(".text-counter");
                if ($counter.length) {
                    const initLen = $field.val().length;
                   $counter.text(`${initLen}/${maxLen}`);
                    $counter.toggleClass("text-danger", initLen > maxLen);

                    $field.on("input", function() {
                        const len = $(this).val().length;
                        $counter.text(`${len}/${maxLen}`);
                        $counter.toggleClass("text-danger", len > maxLen);
                        $form.validate().element(this);
                    });

                }
                else {
                    $field.on("input", function() {
                        $form.validate().element(this);
                    });
                }
            }

            if (type === "email") $field.rules("add", { email: true });

            if (type === "password") $field.rules("add", { strongPassword: true });

            if ($field.attr("name") === "confirmPassword") {
                const $mainPassword = $form.find('input[type="password"]').not('[name="confirmPassword"]').first();
                if ($mainPassword.length) $field.rules("add", { equalTo: $mainPassword });
            }

            if ($field.is("select.select2-hidden-accessible")) {
                $field.on("change.select2", function () {
                    $form.validate().element(this);
                });
            }
        });

        $form.find('input[type="file"]').on('change', function () {
            $form.validate().element(this);
        });

        $form.on("reset", function () {
            $form.validate().resetForm();
        });
    });

});
