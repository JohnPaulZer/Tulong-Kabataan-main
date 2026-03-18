// public/js/campaignview/donation-validation.js
let isReferenceValid = false;
let currentReferenceNumber = "";

function checkReferenceNumber(refNumber) {
    const referenceInput = document.getElementById("referenceInput");
    const referenceStatus = document.getElementById("referenceStatus");
    const referenceError = document.getElementById("referenceError");
    const submitBtn = document.getElementById("submitBtn");

    currentReferenceNumber = refNumber;

    if (refNumber.length < 5) {
        referenceStatus.style.display = "none";
        referenceError.style.display = "none";
        referenceInput.classList.remove("input-error", "input-success");
        submitBtn.disabled = true;
        isReferenceValid = false;
        return;
    }

    referenceStatus.style.display = "none";
    referenceError.style.display = "none";
    referenceInput.classList.remove("input-error", "input-success");
    submitBtn.disabled = true;

    // Get values from data attributes
    const donationRoute = document.body.getAttribute("data-donation-route");
    const csrfToken = document.body.getAttribute("data-csrf-token");

    fetch(donationRoute, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({
            reference_number: refNumber,
        }),
    })
        .then((res) => res.json())
        .then((data) => {
            if (refNumber !== currentReferenceNumber) return;

            if (data.exists) {
                referenceStatus.style.display = "none";
                referenceError.style.display = "block";
                referenceInput.classList.add("input-error");
                referenceInput.classList.remove("input-success");
                submitBtn.disabled = true;
                isReferenceValid = false;
            } else {
                referenceStatus.style.display = "none";
                referenceError.style.display = "none";
                referenceInput.classList.add("input-success");
                referenceInput.classList.remove("input-error");
                submitBtn.disabled = false;
                isReferenceValid = true;
            }
        })
        .catch((err) => {
            console.error("Reference check failed:", err);
            if (refNumber === currentReferenceNumber) {
                referenceStatus.style.display = "none";
                referenceInput.classList.remove("input-error", "input-success");
                submitBtn.disabled = false;
                isReferenceValid = true;
            }
        });
}

function preventInvalidInput(event) {
    const invalidKeys = [69, 189, 109, 107, 187];
    if (invalidKeys.includes(event.keyCode)) {
        event.preventDefault();
        return false;
    }
    return true;
}

function sanitizeAmount(input) {
    input.value = input.value.replace(/[eE\-+]/g, "");
    if (parseFloat(input.value) < 0) {
        input.value = "0";
    }
}

function showToast(message, type = "success") {
    const toast = document.getElementById("toast");
    if (!toast) return;

    toast.textContent = message;
    if (type === "error") toast.style.background = "#dc2626";
    else if (type === "warning") toast.style.background = "#f59e0b";
    else toast.style.background = "#16a34a";

    toast.style.opacity = "1";
    setTimeout(() => {
        toast.style.opacity = "0";
    }, 3000);
}

document.addEventListener("DOMContentLoaded", function () {
    // Check for server-side success/error messages from data attributes
    const successMessage = document.body.getAttribute("data-success-message");
    const errorMessage = document.body.getAttribute("data-error-message");

    if (successMessage) {
        showToast(successMessage, "success");
        setTimeout(() => {
            const proofModal = document.getElementById("proofModal");
            if (proofModal) proofModal.style.display = "none";
        }, 2000);
    }

    if (errorMessage) {
        showToast(errorMessage, "error");
    }
});
