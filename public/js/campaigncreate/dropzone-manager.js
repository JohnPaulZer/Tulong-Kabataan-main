// dropzone-manager.js
document.addEventListener("DOMContentLoaded", function () {
    /* ====== Dropzones with Professional Previews ====== */
    function makeDropzone(
        zoneEl,
        inputEl,
        previewEl,
        single = false,
        max = 10
    ) {
        const pickBtn = zoneEl.querySelector(".tk-drop-btn");
        const openPicker = () => inputEl.click();
        pickBtn.addEventListener("click", openPicker);

        // Drag and drop events
        ["dragenter", "dragover"].forEach((ev) =>
            zoneEl.addEventListener(ev, (e) => {
                e.preventDefault();
                e.stopPropagation();
                zoneEl.classList.add("is-drag");
            })
        );

        ["dragleave", "drop"].forEach((ev) =>
            zoneEl.addEventListener(ev, (e) => {
                e.preventDefault();
                e.stopPropagation();
                zoneEl.classList.remove("is-drag");
            })
        );

        zoneEl.addEventListener("drop", (e) => {
            const files = Array.from(e.dataTransfer.files || []);
            handleFiles(files);
        });

        inputEl.addEventListener("change", () =>
            handleFiles(Array.from(inputEl.files || []), true)
        );

        function handleFiles(newFiles) {
            if (!newFiles.length) return;

            if (single) {
                inputEl.files = fileListFrom(newFiles.slice(0, 1));
                renderPreviews([inputEl.files[0]]);
                return;
            }
            const existing = Array.from(inputEl.files || []);
            const merged = existing.concat(newFiles).slice(0, max);
            inputEl.files = fileListFrom(merged);
            renderPreviews(merged);
        }

        function renderPreviews(files) {
            if (!files.length) {
                previewEl.style.display = "none";
                previewEl.innerHTML = "";
                return;
            }
            previewEl.style.display = "grid";
            previewEl.innerHTML = "";
            files.forEach((f, idx) => {
                if (!f.type.startsWith("image/")) return;
                const url = URL.createObjectURL(f);
                const card = document.createElement("div");
                card.className = "tk-thumb";
                card.innerHTML = `
                    <div class="tk-thumb-content">
                        <img src="${url}" alt="${f.name}">
                        <div class="tk-thumb-overlay">
                            <div class="tk-file-info">
                                <i class="ri-image-line"></i>
                                <span class="tk-file-name">${f.name}</span>
                                <span class="tk-file-size">${formatFileSize(
                                    f.size
                                )}</span>
                            </div>
                        </div>
                        <button type="button" class="tk-del" aria-label="Remove">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                `;
                card.querySelector(".tk-del").addEventListener("click", (e) => {
                    e.stopPropagation();
                    const arr = Array.from(inputEl.files || []);
                    arr.splice(idx, 1);
                    inputEl.files = fileListFrom(arr);
                    renderPreviews(arr);
                });
                previewEl.appendChild(card);
            });
        }

        function fileListFrom(files) {
            const dt = new DataTransfer();
            files.forEach((f) => dt.items.add(f));
            return dt.files;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return "0 Bytes";
            const k = 1024;
            const sizes = ["Bytes", "KB", "MB", "GB"];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return (
                parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i]
            );
        }
    }

    // Initialize dropzones
    const coverDrop = document.getElementById("coverDrop");
    const coverInput = document.getElementById("featured_image");
    const coverPreview = document.getElementById("coverPreview");
    if (coverDrop && coverInput && coverPreview) {
        makeDropzone(coverDrop, coverInput, coverPreview, true, 1);
    }

    const galleryDrop = document.getElementById("galleryDrop");
    const galleryInput = document.getElementById("images");
    const galleryPreview = document.getElementById("galleryPreview");
    if (galleryDrop && galleryInput && galleryPreview) {
        makeDropzone(galleryDrop, galleryInput, galleryPreview, false, 10);
    }

    const qrDrop = document.getElementById("qrDrop");
    const qrInput = document.getElementById("qr_code");
    const qrPreview = document.getElementById("qrPreview");
    if (qrDrop && qrInput && qrPreview) {
        makeDropzone(qrDrop, qrInput, qrPreview, true, 1);
    }
});
