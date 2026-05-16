// image-upload-validation.js
document.addEventListener("DOMContentLoaded", function () {
    /* ====== Image Upload Validation ====== */

    // Add error class for better selection
    function addErrorClass(dropElement) {
        dropElement.classList.add("validation-error");
    }

    function removeErrorClass(dropElement) {
        dropElement.classList.remove("validation-error");
    }

    function validateImageUploads() {
        let isValid = true;
        const missingFields = [];

        function hasUpload(input, pathName) {
            const hasSelectedFile = input?.files && input.files.length > 0;
            const hasRetainedPath = Boolean(
                document.querySelector(`input[type="hidden"][name="${pathName}"][value]`)
            );

            return hasSelectedFile || hasRetainedPath;
        }

        // Check cover image
        const coverInput = document.getElementById("featured_image");
        const coverDrop = document.getElementById("coverDrop");

        if (!hasUpload(coverInput, "featured_image_uploaded_path")) {
            coverDrop.style.borderColor = "#ef4444";
            coverDrop.style.backgroundColor = "#fef2f2";
            addErrorClass(coverDrop);
            missingFields.push("Cover Image");
            isValid = false;
        } else {
            coverDrop.style.borderColor = "";
            coverDrop.style.backgroundColor = "";
            removeErrorClass(coverDrop);
        }

        // Check QR code
        const qrInput = document.getElementById("qr_code");
        const qrDrop = document.getElementById("qrDrop");

        if (!hasUpload(qrInput, "qr_code_uploaded_path")) {
            qrDrop.style.borderColor = "#ef4444";
            qrDrop.style.backgroundColor = "#fef2f2";
            addErrorClass(qrDrop);
            missingFields.push("GCash QR Code");
            isValid = false;
        } else {
            qrDrop.style.borderColor = "";
            qrDrop.style.backgroundColor = "";
            removeErrorClass(qrDrop);
        }

        return { isValid, missingFields };
    }

    // Add form submit event listener
    const form = document.querySelector("form.campaign-form");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        const result = validateImageUploads();
        if (!result.isValid) {
            e.preventDefault();
            window.showNotificationModal?.(
                `Please upload the required file${result.missingFields.length > 1 ? "s" : ""}: ${result.missingFields.join(", ")}.`,
                "error",
                "Missing upload"
            );

            // Get all missing upload fields using class selector
            const errorFields = document.querySelectorAll(
                ".tk-drop.validation-error"
            );

            if (errorFields.length > 0) {
                // Scroll to first error field
                errorFields[0].scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                });

                // Add shake animation to ALL error fields
                errorFields.forEach((field) => {
                    field.style.animation = "shake 0.5s ease-in-out";
                    setTimeout(() => {
                        field.style.animation = "";
                    }, 500);
                });
            }
        }
    });

    // Clear validation when a user selects a file
    function clearValidationOnFileSelect(inputId, dropId) {
        const input = document.getElementById(inputId);
        const drop = document.getElementById(dropId);

        if (input && drop) {
            input.addEventListener("change", function () {
                if (this.files && this.files.length > 0) {
                    drop.style.borderColor = "";
                    drop.style.backgroundColor = "";
                    removeErrorClass(drop);
                }
            });
        }
    }

    clearValidationOnFileSelect("featured_image", "coverDrop");
    clearValidationOnFileSelect("qr_code", "qrDrop");

    // Add CSS for shake animation
    const style = document.createElement("style");
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .tk-drop.validation-error {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
        }
    `;
    document.head.appendChild(style);
});
