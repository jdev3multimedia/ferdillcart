"use strict";
$(document).ready(function () {
    const pdfContainer = $("#pdf-container");
    const documentUploadWrapper = $("#doc-upload-wrapper");
    const fileAssets = $("#file-assets");
    const uploadedFiles = new Map();

    const pictureIcon = fileAssets.data("picture-icon");
    const documentIcon = fileAssets.data("document-icon");
    const blankThumbnail = fileAssets.data("blank-thumbnail");

    const MAX_FILES = 1; // Single file upload
    const MAX_SIZE_MB = 2;
    const MAX_SIZE = MAX_SIZE_MB * 1024 * 1024;

    // === Reusable: Render Thumbnail ===
    async function renderFileThumbnail(element, file) {
        const fileUrl = URL.createObjectURL(file);
        const fileType = file.type;
        const canvas = element.find(".pdf-preview")[0];
        const thumbnail = element.find(".pdf-thumbnail")[0];

        try {
            if (fileType.startsWith("image/")) {
                thumbnail.src = fileUrl;
            } else if (fileType === "application/pdf") {
                const loadingTask = pdfjsLib.getDocument(fileUrl);
                const pdf = await loadingTask.promise;
                const page = await pdf.getPage(1);
                const viewport = page.getViewport({ scale: 0.5 });

                canvas.width = viewport.width;
                canvas.height = viewport.height;
                const ctx = canvas.getContext("2d");

                await page.render({ canvasContext: ctx, viewport }).promise;
                thumbnail.src = canvas.toDataURL();
            } else {
                thumbnail.src = blankThumbnail;
            }

            $(thumbnail).show();
            $(canvas).hide();
        } catch (error) {
            console.error("Thumbnail render error:", error);
            thumbnail.src = blankThumbnail;
        }
    }

    // === Reusable: Create Preview Element ===
    function createPreviewElement(file) {
        const fileUrl = URL.createObjectURL(file);
        const fileType = file.type;
        const iconSrc = fileType.startsWith("image/") ? pictureIcon : documentIcon;

        return $(`
            <div class="pdf-single" data-file-name="${file.name}" data-file-url="${fileUrl}">
                <div class="pdf-frame">
                    <canvas class="pdf-preview d--none"></canvas>
                    <img class="pdf-thumbnail" src="${blankThumbnail}" alt="Thumbnail">
                </div>
                <div class="overlay">
                    <div class="pdf-info">
                        <img src="${iconSrc}" width="34" alt="File Type">
                        <div class="file-name-wrapper">
                            <span class="file-name js-filename-truncate">${file.name}</span>
                            <span class="opacity-50">Click to view</span>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    // === Reusable: Toggle Upload Box ===
    function toggleUploadWrapper() {
        const currentCount = pdfContainer.find(".pdf-single").length;
        documentUploadWrapper.toggle(currentCount < MAX_FILES);
    }

    // === Validate File ===
function validateFile(file) {
    const $defaultText = $("#default-text-data");
    const defaultFileSizeMsg = $defaultText.data("default-filesize"); // "File size must be less than"
    const defaultAllowedFormatMsg = $defaultText.data("default-allwedformat"); // "Invalid file type. Allowed: PDF, DOC, JPG, PNG"

    const acceptAttr = $(".document_input").attr("accept") || "";
    const validTypes = acceptAttr
        ? acceptAttr.split(",").map(t => t.trim().toLowerCase())
        : [".pdf", ".doc", ".docx", ".jpg", ".jpeg", ".png"];

    const fileExt = "." + file.name.split(".").pop().toLowerCase();
    const fileType = file.type.toLowerCase();

    const isValidType = validTypes.some(type => {
        if (type.includes("/")) return fileType === type;
        return fileExt === type;
    });

    if (!isValidType) {
        const errorMsg = defaultAllowedFormatMsg || "Invalid file type. Allowed: PDF, DOC, JPG, PNG";
        toastr?.error(errorMsg) ?? alert(errorMsg);
        return false;
    }

    if (file.size > MAX_SIZE) {
        const errorMsg = `${defaultFileSizeMsg || 'File size must be less than'} ${MAX_SIZE_MB} MB.`;
        toastr?.error(errorMsg) ?? alert(errorMsg);
        return false;
    }

    return true;
}

    // === Core File Change Handler ===
    function handleFileSelection(files) {
        // Clear old state
        $(".pdf-single").remove();
        uploadedFiles.clear();

        if (files.length === 0) {
            toggleUploadWrapper();
            return;
        }

        const file = files[0]; // Single file only

        if (!validateFile(file)) {
            $(".document_input").val("");
            toggleUploadWrapper();
            return;
        }

        uploadedFiles.set(file.name, file);
        const previewEl = createPreviewElement(file);
        pdfContainer.append(previewEl);
        renderFileThumbnail(previewEl, file);

        documentUploadWrapper.hide();
        toggleUploadWrapper();
    }

    // === Main Change Event (Shared) ===
    $(".document_input").on("change", function () {
        handleFileSelection(Array.from(this.files));
    });

    // === Edit Button: Open File Picker + Handle Cancel ===
    $("#doc_edit_btn").on("click", function () {
        const input = $(".document_input")[0];

        // Reset UI
        $(".pdf-single").remove();
        uploadedFiles.clear();
        documentUploadWrapper.show();
        input.value = "";

        let hasChanged = false;

        const changeHandler = function () {
            hasChanged = true;
            // Let main handler do the work
            $(".document_input").off("change", changeHandler);
            $(".document_input").off("focusout", focusoutHandler);
        };

        const focusoutHandler = function () {
            setTimeout(() => {
                if (!hasChanged && (!input.files || input.files.length === 0)) {
                    // Cancelled → reset UI
                    $(".pdf-single").remove();
                    uploadedFiles.clear();
                    documentUploadWrapper.show();
                }
                // Cleanup
                $(".document_input").off("change", changeHandler);
                $(".document_input").off("focusout", focusoutHandler);
            }, 100);
        };

        $(".document_input")
            .on("change", changeHandler)
            .on("focusout", focusoutHandler);

        input.click();
    });

    // === Click Preview to Open File ===
    pdfContainer.on("click", ".pdf-single", function () {
        const fileUrl = $(this).data("file-url");
        if (fileUrl) window.open(fileUrl, "_blank");
    });

    // === Download Button (Optional) ===
    $("#doc_download_btn").on("click", function () {
        const preview = pdfContainer.find(".pdf-single").first();
        const fileUrl = preview.data("file-url");
        const fileName = preview.data("file-name");

        if (!fileUrl || !fileName) return;

        const link = document.createElement("a");
        link.href = fileUrl;
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // === Reset Button ===
    $("#reset-btn").on("click", function () {
        $(".pdf-single").remove();
        uploadedFiles.clear();
        documentUploadWrapper.show();
        $(".document_input").val("");
    });

    // === Form Submit: Append File ===
    $("form").on("submit", function () {
        const formData = new FormData(this);
        const input = $(".document_input")[0];

        // Remove old files
        formData.delete(input.name);
        formData.delete(input.name + "[]");

        // Append current file if exists
        if (uploadedFiles.size > 0) {
            uploadedFiles.forEach((file, name) => {
                formData.append(input.name, file, name);
            });
        } else if (input.files && input.files[0]) {
            formData.append(input.name, input.files[0]);
        }

        // Optional: Debug
        // for (let [k, v] of formData.entries()) console.log(k, v);
    });
});
