const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("password");
const passwordIcon = togglePassword?.querySelector("i");

togglePassword?.addEventListener("click", () => {
    const type =
        passwordInput.getAttribute("type") === "password" ? "text" : "password";

    passwordInput.setAttribute("type", type);
    passwordIcon?.classList.toggle("ri-eye-line");
    passwordIcon?.classList.toggle("ri-eye-off-line");
});

const errorBox = document.getElementById("flash-error");

if (errorBox) {
    setTimeout(() => errorBox.classList.add("show"), 50);
    setTimeout(() => errorBox.classList.remove("show"), 3000);
}

try {
    sessionStorage.removeItem("tkLoadingMode");
} catch (error) {
    // Storage can be unavailable in private browsing modes.
}

function requestBrandLoader() {
    try {
        sessionStorage.setItem("tkLoadingMode", "brand");
    } catch (error) {
        // Storage can be unavailable in private browsing modes.
    }
}

document.getElementById("login-form")?.addEventListener("submit", requestBrandLoader);
document.querySelector('a[href*="/auth/google"]')?.addEventListener("click", requestBrandLoader);

window.addEventListener("pageshow", (event) => {
    if (event.persisted) location.reload();
});

const modal = document.getElementById("forgotPasswordModal");
const forgotPasswordLink = document.getElementById("forgotPasswordLink");
const closeBtn = document.querySelector(".close");

forgotPasswordLink?.addEventListener("click", (event) => {
    event.preventDefault();
    modal?.classList.add("active");
});

closeBtn?.addEventListener("click", () => {
    modal?.classList.remove("active");
});

window.addEventListener("click", (event) => {
    if (event.target === modal) {
        modal.classList.remove("active");
    }
});

const forgotForm = document.getElementById("forgotPasswordForm");
const resetMessage = document.getElementById("resetMessage");

if (forgotForm && resetMessage) {
    forgotForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        resetMessage.style.display = "none";
        resetMessage.style.background = "";
        resetMessage.style.color = "#146c2e";

        const formData = new FormData(this);
        const btn = forgotForm.querySelector(".btn-main");
        const originalText = btn.textContent;

        btn.disabled = true;
        btn.innerHTML = `<span class="login-spinner"></span> Sending...`;

        try {
            const response = await fetch(this.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": formData.get("_token"),
                },
                body: formData,
            });

            const data = await response.json();

            if (response.ok) {
                resetMessage.textContent = data.success;
                resetMessage.style.display = "block";
                forgotForm.reset();
            } else {
                resetMessage.textContent = data.error || "Something went wrong.";
                resetMessage.style.background = "#ffe2e2";
                resetMessage.style.color = "#8b0000";
                resetMessage.style.display = "block";
            }
        } catch (err) {
            console.error(err);
            resetMessage.textContent = "Server error. Please try again.";
            resetMessage.style.background = "#ffe2e2";
            resetMessage.style.color = "#8b0000";
            resetMessage.style.display = "block";
        }

        btn.disabled = false;
        btn.textContent = originalText;
    });
}
