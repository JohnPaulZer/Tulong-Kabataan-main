document.addEventListener("DOMContentLoaded", function () {
    const fileInput = document.getElementById("photoInput");
    const avatarWrapper = document.getElementById("avatarWrapper");
    const modal = document.getElementById("cropModal");
    const preview = document.getElementById("cropperPreview");
    const form = document.getElementById("changePhotoForm");
    const saveBtn = document.getElementById("saveCropped");
    const cancelBtn = document.getElementById("cancelCrop");
    const rotateBtn = document.getElementById("rotateLeft");
    const zoomInBtn = document.getElementById("zoomIn");
    const zoomOutBtn = document.getElementById("zoomOut");

    let cropper;

    avatarWrapper.addEventListener("click", () => fileInput.click());

    fileInput.addEventListener("change", (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            preview.src = event.target.result;
            modal.style.display = "flex";

            preview.onload = function () {
                if (cropper) cropper.destroy();

                preview.style.maxWidth = "100%";
                preview.style.maxHeight = "300px";
                preview.style.objectFit = "contain";
                preview.style.display = "block";
                preview.style.margin = "0 auto";

                cropper = new Cropper(preview, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: "move",
                    background: false,
                    minContainerWidth: 300,
                    minContainerHeight: 300,
                    zoomOnWheel: false,
                });
            };
        };
        reader.readAsDataURL(file);
    });

    rotateBtn.addEventListener("click", () => cropper.rotate(-90));
    zoomInBtn.addEventListener("click", () => cropper.zoom(0.1));
    zoomOutBtn.addEventListener("click", () => cropper.zoom(-0.1));

   saveBtn.addEventListener("click", () => {
    // Store original button text
    const originalText = saveBtn.innerHTML;

    // Show loading state
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="ri-loader-4-line spinner" style="margin-right: 5px; animation: spin 1s linear infinite;"></i> Saving...';

    cropper.getCroppedCanvas({ width: 300, height: 300 }).toBlob((blob) => {
        const formData = new FormData(form);
        formData.append("photo", blob, "cropped.png");

        fetch(form.action, {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN":
                    document.querySelector("input[name=_token]").value,
            },
        })
        .then((res) => {
            if (res.ok) {
                location.reload();
            } else {
                // Restore button if failed
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
                alert("Upload failed");
            }
        })
        .catch((error) => {
            // Restore button if error
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
            alert("Upload failed: " + error.message);
        });
    });
});
});
