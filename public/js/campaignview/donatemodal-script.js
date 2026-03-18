document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    const donateBtn = document.querySelector(".show-donation-modal");
    const donationModal = document.getElementById("donationModal");
    const proofModal = document.getElementById("proofModal");
    const closeBtns = document.querySelectorAll(".modal-close");
    const alreadyPaidBtn = document.getElementById("alreadyPaidBtn");
    const copyBtn = document.getElementById("copyGcashBtn");
    const copyIcon = copyBtn.querySelector("i");
    const gcashNumber = document.querySelector(".gcash-number").textContent;

    // Open QR modal
    donateBtn.addEventListener("click", () => {
        donationModal.style.display = "flex";
        body.style.overflow = "hidden"; // Prevent body scroll
    });

    // Proceed to proof upload
    alreadyPaidBtn.addEventListener("click", () => {
        donationModal.style.display = "none";
        proofModal.style.display = "flex";
        // Keep overflow hidden since we're switching between modals
    });

    // Close modal(s)
    closeBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
            donationModal.style.display = "none";
            proofModal.style.display = "none";
            body.style.overflow = "auto"; // Restore body scroll
        });
    });

    const backToQrBtn = document.querySelector(".back-to-qr-btn");
    backToQrBtn.addEventListener("click", () => {
        proofModal.style.display = "none";
        donationModal.style.display = "flex";
        // Keep overflow hidden since we're switching between modals
    });

    // Copy GCash number functionality
    copyBtn.addEventListener("click", () => {
        navigator.clipboard
            .writeText(gcashNumber)
            .then(() => {
                copyIcon.className = "ri-check-line";
                copyBtn.classList.add("copied");
                showToast("GCash number copied to clipboard!", "success");

                setTimeout(() => {
                    copyIcon.className = "ri-file-copy-line";
                    copyBtn.classList.remove("copied");
                }, 3000);
            })
            .catch((err) => {
                console.error("Failed to copy: ", err);
                showToast("Failed to copy GCash number", "error");
            });
    });
});
