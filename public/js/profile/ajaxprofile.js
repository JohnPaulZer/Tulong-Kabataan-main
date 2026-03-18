document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("editProfileForm");
    const btn = document.getElementById("updateProfileBtn");
    const btnText = btn.querySelector(".btn-text");
    const spinner = btn.querySelector(".spinner");
    const messageBox = document.getElementById("editProfileMessage");

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // reset message
        messageBox.style.display = "none";
        messageBox.innerHTML = "";
        messageBox.style.background = "";
        messageBox.style.border = "";
        messageBox.style.color = "";

        btn.disabled = true;
        btnText.textContent = "Saving...";
        spinner.style.display = "inline-block";

        const formData = new FormData(form);

        fetch(form.action, {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN":
                    document.querySelector("input[name=_token]").value,
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then(async (res) => {
                spinner.style.display = "none";
                btn.disabled = false;
                btnText.textContent = "Save Changes";

                const data = await res.json().catch(() => ({}));

                if (res.ok && data.success) {
                    messageBox.innerHTML = `<div style="background:#e8f8ee; border:1px solid #b7e2c0; color:#1e7e34; padding:10px 14px 6px; border-radius:6px;">${
                        data.message || "Profile updated successfully!"
                    }</div>`;
                    messageBox.style.display = "block";
                    setTimeout(() => location.reload(), 1000);
                } else {
                    messageBox.innerHTML = `<div style="background:#fdeaea; border:1px solid #f5c2c2; color:#d93025; padding:10px 14px 6px; border-radius:6px;">${
                        data.message ||
                        "An error occurred while updating your profile."
                    }</div>`;
                    messageBox.style.display = "block";
                }
            })
            .catch((err) => {
                spinner.style.display = "none";
                btn.disabled = false;
                btnText.textContent = "Save Changes";
                messageBox.innerHTML = `<div style="background:#fdeaea; border:1px solid #f5c2c2; color:#d93025; padding:10px 14px 6px; border-radius:6px;">Network error. Please try again later.</div>`;
                messageBox.style.display = "block";
                console.error(err);
            });
    });
});
