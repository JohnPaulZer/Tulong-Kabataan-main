function initDonationsModal() {
    const seeAllButton = document.querySelector(".cpv-show-more-comments");
    const allDonationsModal = document.getElementById("allDonationsModal");
    const closeButtons = document.querySelectorAll(".modal-close");
    const body = document.body;

    if (seeAllButton && allDonationsModal) {
        // Open modal when "See all" is clicked
        seeAllButton.addEventListener("click", () => {
            // Scroll to top if not already at top
            if (window.scrollY > 0) {
                window.scrollTo({
                    top: 0,
                    behavior: "smooth",
                });
            }

            allDonationsModal.style.display = "flex";
            // Prevent body scroll
            body.style.overflow = "hidden";
        });

        // Close modal when close button is clicked
        closeButtons.forEach((btn) => {
            btn.addEventListener("click", () => {
                allDonationsModal.style.display = "none";
                // Restore body scroll
                body.style.overflow = "auto";
            });
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    initDonationsModal();
});
