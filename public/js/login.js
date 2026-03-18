const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("password");
togglePassword?.addEventListener("click", function () {
    const type =
        passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);
    this.classList.toggle("ri-eye-line");
    this.classList.toggle("ri-eye-off-line");
});

const errorBox = document.getElementById("flash-error");
if (errorBox) {
    // fade in
    setTimeout(() => errorBox.classList.add("show"), 50);

    // fade out after 3 seconds
    setTimeout(() => errorBox.classList.remove("show"), 3000);
}

window.addEventListener("pageshow", function (e) {
    if (e.persisted) location.reload();
});

// ========================FORGOT PASSWORD MODAL========================
const modal = document.getElementById("forgotPasswordModal");
const link = document.getElementById("forgotPasswordLink");
const closeBtn = document.querySelector(".close");

// Open modal when "Forgot Password?" is clicked
if (link) {
    link.addEventListener("click", (e) => {
        e.preventDefault();
        modal.classList.add("active");
    });
}

// Close modal when X is clicked
if (closeBtn) {
    closeBtn.addEventListener("click", () => {
        modal.classList.remove("active");
    });
}

// Close modal when clicking outside of it
window.addEventListener("click", (e) => {
    if (e.target === modal) {
        modal.classList.remove("active");
    }
});

// ========================AJAX FORGOT PASSWORD========================
const forgotForm = document.getElementById("forgotPasswordForm");
const resetMessage = document.getElementById("resetMessage");

forgotForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    resetMessage.style.display = "none";
    resetMessage.classList.add("show");
    resetMessage.style.color = "#146c2e";

    const formData = new FormData(this);

    // 🔹 NEW: get the button and show loading state
    const btn = forgotForm.querySelector(".btn-main");
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner"></span> Sending...`;

    try {
        const response = await fetch(this.action, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
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

    // 🔹 NEW: restore button after request finishes
    btn.disabled = false;
    btn.textContent = originalText;
});
