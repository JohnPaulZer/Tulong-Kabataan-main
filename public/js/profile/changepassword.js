document.addEventListener("DOMContentLoaded", function () {
    const changePasswordBtn = document.getElementById("changePasswordBtn");
    const modal = document.getElementById("changePasswordModal");
    const cancelBtn = document.getElementById("cancelChangePassword");

    changePasswordBtn.addEventListener("click", () => {
        modal.style.display = "flex";
    });

    cancelBtn.addEventListener("click", () => {
        modal.style.display = "none";

        const form = document.getElementById("changePasswordForm");
        form.reset();

        const errorBox = document.getElementById("changePasswordError");
        errorBox.style.display = "none";
        errorBox.textContent = "";

        // Reset borders and remove error highlights
        form.querySelectorAll(".modal-input").forEach((el) => {
            el.style.borderColor = "";
            el.classList.remove("error");
        });
    });

    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
            const form = document.getElementById("changePasswordForm");
            form.reset();
            const errorBox = document.getElementById("changePasswordError");
            errorBox.style.display = "none";
            errorBox.textContent = "";
            form.querySelectorAll(".modal-input").forEach((el) => {
                el.style.borderColor = "";
                el.classList.remove("error");
            });
        }
    });
});
