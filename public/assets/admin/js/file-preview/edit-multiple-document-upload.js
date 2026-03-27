"use strict";
$(document).ready(function () {
    const pdfContainer = $("#pdf-container");
    const documentUploadWrapper = $("#doc-upload-wrapper");
    const uploadedFiles = new Map();

    const fileAssets = $("#file-assets");
    const pictureIcon = fileAssets.data("picture-icon");
    const documentIcon = fileAssets.data("document-icon");
    const blankThumbnail = fileAssets.data("blank-thumbnail");

    $(".document_input").on("change", function (event) {
        const input = this;
        const isMultiple = input.hasAttribute("multiple");
        const isArrayName = input.name.endsWith("[]");
        const MAX_FILES = isMultiple || isArrayName ? 5 : 1;

        const files = Array.from(input.files);
        const currentFiles = pdfContainer.find(".pdf-single").length;

        if (currentFiles + files.length > MAX_FILES) {
            return;
        }

        if (!isMultiple && !isArrayName && files.length > 0) {
            // Replace the upload wrapper for single file mode
            uploadedFiles.clear();
            $(".pdf-single").remove();
            documentUploadWrapper.hide();
        }

        files.forEach((file) => {
            if (!uploadedFiles.has(file.name)) {
                uploadedFiles.set(file.name, file);

                const fileURL = URL.createObjectURL(file);
                const fileType = file.type;
                const iconSrc = fileType.startsWith("image/") ? pictureIcon : documentIcon;

                const pdfSingle = $(`
                <div class="pdf-single" data-file-name="${file.name}" data-file-url="${fileURL}">
                    <div class="pdf-frame">
                        <canvas class="pdf-preview d--none"></canvas>
                        <img class="pdf-thumbnail" src="${blankThumbnail}" alt="File Thumbnail">
                    </div>
                    <div class="overlay">
                        <div class="pdf-info">
                            <img src="${iconSrc}" width="34" alt="File Type Logo">
                            <div class="file-name-wrapper">
                                <span class="file-name js-filename-truncate">${file.name}</span>
                                <span class="opacity-50">Click to view the file</span>
                            </div>
                        </div>
                    </div>
                </div>
            `);

                pdfContainer.append(pdfSingle);
                renderFileThumbnail(pdfSingle, fileType);
            }
        });

        toggleUploadWrapper(MAX_FILES);
    });

    pdfContainer.on("click", ".pdf-single", function () {
        const fileURL = $(this).data("file-url");
        window.open(fileURL, "_blank");
    });

    function toggleUploadWrapper(max = 5) {
        const currentFiles = pdfContainer.find(".pdf-single").length;
        documentUploadWrapper.toggle(currentFiles < max);
    }

    async function renderFileThumbnail(element, fileType) {
        const fileUrl = element.data("file-url");
        const canvas = element.find(".pdf-preview")[0];
        const thumbnail = element.find(".pdf-thumbnail")[0];

        try {
            if (fileType.startsWith("image/")) {
                thumbnail.src = fileUrl;
            } else if (fileType === "application/pdf") {
                const ctx = canvas.getContext("2d");
                const loadingTask = pdfjsLib.getDocument(fileUrl);
                const pdf = await loadingTask.promise;
                const page = await pdf.getPage(1);

                const viewport = page.getViewport({ scale: 0.5 });
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                await page.render({ canvasContext: ctx, viewport }).promise;
                thumbnail.src = canvas.toDataURL();
            } else {
                thumbnail.src = blankThumbnail;
            }

            $(thumbnail).show();
            $(canvas).hide();
        } catch (error) {
            console.error("Error rendering file thumbnail:", error);
        }
    }

// $("#doc_edit_btn").on("click", function () {
//     $(".pdf-single").remove();
//     uploadedFiles.clear();
//     documentUploadWrapper.show();
//     $(".document_input").val("").click();
// });

    $("#doc_edit_btn").on("click", function () {
        const input = $(".document_input");

        // Temporarily bind a one-time change handler
        input.one("change", function (e) {
            const files = Array.from(this.files);
            if (files.length > 0) {
                // Remove old preview and reset state
                $(".pdf-single").remove();
                uploadedFiles.clear();
                documentUploadWrapper.hide();

                // Trigger the input change again to upload new files
                $(this).trigger("change");
            } else {
                // If no file is selected, reset input
                this.value = "";
            }
        });

        // Clear file input before opening dialog (to allow same file re-selection)
        input.val("");
        input[0].click(); // open file dialog
    });





    $("#doc_download_btn").on("click", function () {
        const pdfSingle = pdfContainer.find(".pdf-single").first();
        const fileUrl = pdfSingle.data("file-url");
        const fileName = pdfSingle.data("file-name");

        if (!fileUrl || !fileName) return;

        const link = document.createElement("a");
        link.href = fileUrl;
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    $("form").on("submit", function (e) {
        const formData = new FormData(this);
        uploadedFiles.forEach((file, fileName) => {
            const input = $(".document_input")[0];
            const isArray = input.name.endsWith("[]");
            const fieldName = isArray ? "documents[]" : "document";
            formData.append(fieldName, file, fileName);
        });

        console.log("Files submitted:", Array.from(uploadedFiles.keys()));
    });

    $("#reset_btn").click(function () {
        $(".pdf-single").remove();
        uploadedFiles.clear();
        documentUploadWrapper.show();
    });
});
