// qr-code-cropper-simple.js
document.addEventListener("DOMContentLoaded", function () {
    const qrInput = document.getElementById("qr_code");
    const qrDrop = document.getElementById("qrDrop");
    const qrPreview = document.getElementById("qrPreview");

    if (!qrInput || !qrDrop) return;

    // Track last QR validation message
    let lastQrValidationMessage = "";

    // Toast function
    function showToast(message, type = "success") {
        const toast = document.getElementById("toast");
        if (!toast) return;

        // Set message and style based on type
        toast.textContent = message;
        toast.style.background =
            type === "success"
                ? "#16a34a"
                : type === "warning"
                ? "#f59e0b"
                : type === "error"
                ? "#ef4444"
                : "#16a34a";

        // Show toast
        toast.style.opacity = "1";

        // Hide after 3 seconds
        setTimeout(() => {
            toast.style.opacity = "0";
        }, 3000);
    }

    // Load QR code scanner library
    function loadQRLibrary() {
        return new Promise((resolve, reject) => {
            if (window.jsQR) {
                resolve(window.jsQR);
                return;
            }

            const script = document.createElement("script");
            script.src = "https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js";
            script.onload = () => resolve(window.jsQR);
            script.onerror = () =>
                reject(new Error("Failed to load QR scanner library"));
            document.head.appendChild(script);
        });
    }

    // Load image into img element
    function loadImage(file) {
        return new Promise((resolve, reject) => {
            const url = URL.createObjectURL(file);
            const img = new Image();
            img.onload = () => {
                URL.revokeObjectURL(url);
                resolve(img);
            };
            img.onerror = reject;
            img.src = url;
        });
    }

    // Convert canvas to blob
    function toBlobFromCanvas(canvas, mime = "image/png") {
        return new Promise((res) => canvas.toBlob(res, mime, 0.95));
    }

    // Handle QR file processing
    async function handleQrFile(file) {
        const jsQR = await loadQRLibrary();
        const img = await loadImage(file);

        // Draw on canvas
        const c = document.createElement("canvas");
        c.width = img.width;
        c.height = img.height;
        const ctx = c.getContext("2d");
        ctx.drawImage(img, 0, 0);

        const data = ctx.getImageData(0, 0, c.width, c.height);
        const code = jsQR(data.data, c.width, c.height);

        if (code && code.location) {
            const loc = code.location;
            const xs = [
                loc.topLeftCorner.x,
                loc.topRightCorner.x,
                loc.bottomLeftCorner.x,
                loc.bottomRightCorner.x,
            ];
            const ys = [
                loc.topLeftCorner.y,
                loc.topRightCorner.y,
                loc.bottomLeftCorner.y,
                loc.bottomRightCorner.y,
            ];
            const minX = Math.floor(Math.min(...xs));
            const maxX = Math.ceil(Math.max(...xs));
            const minY = Math.floor(Math.min(...ys));
            const maxY = Math.ceil(Math.max(...ys));

            const w = maxX - minX;
            const h = maxY - minY;
            const padding = Math.round(Math.max(w, h) * 0.15);

            // Crop region
            const tmp = document.createElement("canvas");
            tmp.width = w + padding * 2;
            tmp.height = h + padding * 2;
            tmp.getContext("2d").drawImage(
                c,
                minX - padding,
                minY - padding,
                w + padding * 2,
                h + padding * 2,
                0,
                0,
                w + padding * 2,
                h + padding * 2
            );

            const blob = await toBlobFromCanvas(tmp);
            const fileOut = new File([blob], "cropped-qr-code.png", {
                type: "image/png",
            });
            const dt = new DataTransfer();
            dt.items.add(fileOut);
            qrInput.files = dt.files;

            // Show preview
            showQRPreview(blob, true);
            return true;
        }
        return false;
    }

    // Show QR preview
    function showQRPreview(blob, isCropped = false) {
        if (!qrPreview) return;

        const url = URL.createObjectURL(blob);
        qrPreview.style.display = "grid";
        qrPreview.innerHTML = `
            <div class="tk-thumb">
                <div class="tk-thumb-content">
                    <img src="${url}" alt="${
            isCropped ? "Cropped QR Code" : "QR Code"
        }">
                    <div class="tk-thumb-overlay">
                        <div class="tk-file-info">
                            <i class="ri-${
                                isCropped ? "crop-line" : "qr-code-line"
                            }"></i>
                            <span class="tk-file-name">${
                                isCropped ? "Cropped QR Code" : "QR Code"
                            }</span>
                            <span class="tk-file-size">${
                                isCropped ? "Auto-cropped" : "Original"
                            }</span>
                        </div>
                    </div>
                    <button type="button" class="tk-del" aria-label="Remove QR Code">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            </div>
        `;

        // Add delete functionality
        qrPreview
            .querySelector(".tk-del")
            .addEventListener("click", function () {
                qrInput.value = "";
                qrPreview.style.display = "none";
                qrPreview.innerHTML = "";
                qrDrop.style.borderColor = "";
                qrDrop.style.backgroundColor = "";
                lastQrValidationMessage = "";
            });
    }

    // Handle file selection
    async function handleFileSelect(file) {
        if (!file.type.startsWith("image/")) {
            showToast("Please upload an image file (JPG, PNG)", "error");
            lastQrValidationMessage = "Please upload an image file (JPG, PNG)";
            // REMOVED: Don't reset border after timeout for invalid files
            return false;
        }

        if (file.size > 5 * 1024 * 1024) {
            showToast("File size must be less than 5MB", "error");
            lastQrValidationMessage = "File size must be less than 5MB";
            // REMOVED: Don't reset border after timeout for invalid files
            return false;
        }

        // Show scanning state
        qrDrop.classList.add("scanning");
        qrDrop.querySelector(".tk-drop-btn").innerHTML =
            '<i class="ri-qr-scan-line"></i> Scanning QR Code...';

        try {
            const qrDetected = await handleQrFile(file);

            if (qrDetected) {
                showToast(
                    "QR Code detected and cropped automatically!",
                    "success"
                );
                qrDrop.style.borderColor = "#10b981";
                qrDrop.style.backgroundColor = "#ecfdf5";
                lastQrValidationMessage = "";
            } else {
                showToast(
                    "No QR code detected. Using original image.",
                    "warning"
                );
                // Use original file
                const dt = new DataTransfer();
                dt.items.add(file);
                qrInput.files = dt.files;
                showQRPreview(file, false);
                qrDrop.style.borderColor = "#f59e0b";
                qrDrop.style.backgroundColor = "#fffbeb";
                lastQrValidationMessage =
                    "No QR code detected. Using original image.";
                // REMOVED: Don't reset border after timeout for invalid QR
            }
            return true;
        } catch (error) {
            showToast("Error processing image: " + error.message, "error");
            lastQrValidationMessage =
                "Error processing image: " + error.message;
            // REMOVED: Don't reset border after timeout for errors
            return false;
        } finally {
            qrDrop.classList.remove("scanning");
            qrDrop.querySelector(".tk-drop-btn").innerHTML =
                '<i class="ri-upload-2-line"></i> Choose QR Code';
        }
    }

    // Add form submit validation
    const form = document.querySelector("form.campaign-form");
    if (form) {
        form.addEventListener("submit", function (e) {
            if (
                lastQrValidationMessage &&
                lastQrValidationMessage.includes("No QR code detected")
            ) {
                e.preventDefault();
                showToast(lastQrValidationMessage, "warning");
                // Ensure border stays orange on submit
                qrDrop.style.borderColor = "#f59e0b";
                qrDrop.style.backgroundColor = "#fffbeb";
            }
        });
    }

    // Initialize event listeners
    function initialize() {
        // File input change
        qrInput.addEventListener("change", async function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const success = await handleFileSelect(file);
            if (!success) {
                this.value = "";
                // REMOVED: The timeout that was resetting the border
            }
        });

        // Drag and drop
        ["dragenter", "dragover"].forEach((ev) => {
            qrDrop.addEventListener(ev, (e) => {
                e.preventDefault();
                e.stopPropagation();
                qrDrop.classList.add("is-drag");
            });
        });

        ["dragleave", "drop"].forEach((ev) => {
            qrDrop.addEventListener(ev, (e) => {
                e.preventDefault();
                e.stopPropagation();
                qrDrop.classList.remove("is-drag");
            });
        });

        qrDrop.addEventListener("drop", async (e) => {
            const files = Array.from(e.dataTransfer.files || []);
            if (files.length === 0) return;

            const file = files[0];
            const success = await handleFileSelect(file);

            if (!success) {
                qrInput.value = "";
            }
        });
    }

    // Add CSS for scanning state
    const style = document.createElement("style");
    style.textContent = `
        .tk-drop.scanning {
            border-color: #3b82f6 !important;
            background-color: #eff6ff !important;
        }

        .tk-drop.scanning .tk-drop-btn {
            background-color: #3b82f6 !important;
            color: white !important;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .tk-drop.scanning .tk-drop-btn i {
            animation: pulse 1.5s infinite;
        }
    `;
    document.head.appendChild(style);

    // Initialize
    initialize();
});
