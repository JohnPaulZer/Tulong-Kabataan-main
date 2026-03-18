document.addEventListener("DOMContentLoaded", function () {
    const editBtn = document.getElementById("editProfileBtn");
    const modal = document.getElementById("editProfileModal");
    const cancelBtn = document.getElementById("cancelEditProfile");

    editBtn.addEventListener("click", () => (modal.style.display = "flex"));
    cancelBtn.addEventListener("click", () => (modal.style.display = "none"));
});
