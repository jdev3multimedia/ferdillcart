"use strict";
document.addEventListener("DOMContentLoaded", function() {

    async function renderFileThumbnail(element) {
        const fileUrl = element.getAttribute("data-pdf-url");
        const canvas = element.querySelector(".pdf-preview");
        const thumbnail = element.querySelector(".pdf-thumbnail");
        const fileNameSpan = element.querySelector(".file-name");
        const downloadButton = element.querySelector(".download-btn");

        const fullFileName = fileUrl.split('/').pop();
        const fileExtension = fullFileName.split('.').pop().toLowerCase();
        const fileNameWithoutExtension = fullFileName.replace(/\.[^/.]+$/, '');

        const truncatedFileName =
            fileNameWithoutExtension.length > 20 ?
                `${fileNameWithoutExtension.substring(0, 17)}...` :
                fileNameWithoutExtension;
        const displayedFileName = `${truncatedFileName}.${fileExtension}`;

        fileNameSpan.textContent = displayedFileName;
        downloadButton.setAttribute("title", fullFileName);

        if (fileExtension === "pdf") {
            const ctx = canvas.getContext("2d");

            try {
                const loadingTask = pdfjsLib.getDocument(fileUrl);
                const pdf = await loadingTask.promise;
                const page = await pdf.getPage(1);

                const viewport = page.getViewport({
                    scale: 0.5
                });
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                await page.render({
                    canvasContext: ctx,
                    viewport
                }).promise;

                thumbnail.src = canvas.toDataURL();
            } catch (error) {
                console.error("Error rendering PDF thumbnail:", error);
                thumbnail.src = $('#file-assets').data('default-thumbnail');
            }
        } else if (["jpg", "jpeg", "png", "gif", "bmp"].includes(fileExtension)) {
            thumbnail.src = fileUrl;
        } else {
            const fileIconPath = $('#file-assets').data('document-path') + `/${fileExtension}.png`;
            const fallbackIconPath =
                $('#file-assets').data('default-thumbnail');

            const iconExists = await checkFileIconExistence(fileIconPath);

            thumbnail.src = iconExists ? fileIconPath : fallbackIconPath;
        }

        thumbnail.style.display = "block";
        canvas.style.display = "none";
    }

    async function checkFileIconExistence(iconPath) {
        return new Promise((resolve) => {
            const img = new Image();
            img.onload = () => resolve(true);
            img.onerror = () => resolve(false);
            img.src = iconPath;
        });
    }

    document.querySelectorAll(".pdf-single").forEach(renderFileThumbnail);

    $(document).on("click", ".pdf-single", function () {
        const fileUrl = $(this).data("pdf-url");
        window.open(fileUrl, "_blank");
    });

    $(document).on("click", ".download-btn", function (event) {
        event.stopPropagation();

        const fileUrl = $(this).closest(".pdf-single").data("pdf-url");
        const link = document.createElement("a");
        link.href = fileUrl;
        link.download = fileUrl.split("/").pop();
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

});
