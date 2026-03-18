document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("changePasswordForm");
    const modal = document.getElementById("changePasswordModal");
    const errorBox = document.getElementById("changePasswordError");
    const updateBtn = document.getElementById("updatePasswordBtn");
    const spinner = updateBtn.querySelector(".spinner");
    const btnText = updateBtn.querySelector(".btn-text");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        // Reset previous states
        errorBox.style.display = "none";
        errorBox.innerHTML = "";
        form.querySelectorAll(".modal-input").forEach((el) =>
            el.classList.remove("error")
        );

        const newPassword = form.querySelector('[name="new_password"]');
        const confirmPassword = form.querySelector(
            '[name="new_password_confirmation"]'
        );

        if (newPassword.value !== confirmPassword.value) {
            newPassword.classList.add("error");
            confirmPassword.classList.add("error");
            errorBox.innerHTML = `
        <div class="error-list">
          <ul><li>The new password and confirmation do not match.</li></ul>
        </div>`;
            errorBox.style.display = "block";
            return;
        }

        // Disable button + spinner
        updateBtn.disabled = true;
        spinner.style.display = "inline-block";
        btnText.textContent = "Updating...";

        try {
            const res = await fetch(form.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN":
                        document.querySelector("input[name=_token]").value,
                    Accept: "application/json",
                },
                body: new FormData(form),
            });

            const data = await res.json();

            updateBtn.disabled = false;
            spinner.style.display = "none";
            btnText.textContent = "Update Password";

            if (data.status === "error") {
                const errorMessages = Object.entries(data.errors).map(
                    ([field, msgs]) => ({
                        field,
                        msgs,
                    })
                );

                let listHTML = '<div class="error-list"><ul>';
                errorMessages.forEach((err) => {
                    err.msgs.forEach((msg) => (listHTML += `<li>${msg}</li>`));

                    if (err.field === "new_password_confirmation") {
                        form.querySelector(
                            '[name="new_password"]'
                        ).classList.add("error");
                        form.querySelector(
                            '[name="new_password_confirmation"]'
                        ).classList.add("error");
                    } else {
                        const input = form.querySelector(
                            `[name="${err.field}"]`
                        );
                        if (input) input.classList.add("error");
                    }
                });
                listHTML += "</ul></div>";

                errorBox.innerHTML = listHTML;
                errorBox.style.display = "block";
            } else if (data.status === "success") {
                errorBox.innerHTML = `<div style="color:#2ecc71;">${data.message}</div>`;
                errorBox.style.display = "block";
                setTimeout(() => {
                    form.reset();
                    errorBox.style.display = "none";
                    modal.style.display = "none";
                }, 1000);
            }
        } catch (err) {
            console.error(err);
            updateBtn.disabled = false;
            spinner.style.display = "none";
            btnText.textContent = "Update Password";
            errorBox.innerHTML = `
        <div class="error-list"><ul><li>Network error. Please try again later.li></ul></div>`;
            errorBox.style.display = "block";
        }
    });
});
