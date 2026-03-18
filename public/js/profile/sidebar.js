// Close the banner
document.addEventListener("DOMContentLoaded", () => {
    const closeBtn = document.getElementById("profCloseBanner");
    const banner = document.getElementById("profVerificationBanner");
    if (closeBtn && banner) {
        closeBtn.addEventListener("click", () => {
            banner.style.display = "none";
        });
    }
});

// Mobile: allow tap to expand/collapse sidebar (since hover isn't reliable on touch)
(function () {
    const sidebar = document.getElementById("profSidebar");
    if (!sidebar) return;

    sidebar.addEventListener("click", function (e) {
        if (window.matchMedia("(max-width: 768px)").matches) {
            const wasOpen = sidebar.classList.contains("open");
            if (!wasOpen) {
                sidebar.classList.add("open");
                e.preventDefault();
            }
        }
    });

    document.addEventListener("click", function (e) {
        if (window.matchMedia("(max-width: 768px)").matches) {
            if (!sidebar.contains(e.target)) sidebar.classList.remove("open");
        }
    });

    window.addEventListener("resize", function () {
        if (!window.matchMedia("(max-width: 768px)").matches) {
            sidebar.classList.remove("open");
        }
    });
})();
