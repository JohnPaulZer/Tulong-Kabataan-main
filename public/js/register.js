// =========================
// registration form UX
// =========================
const form = document.getElementById("registerForm");
const submitBtn = document.getElementById("submitBtn");
const submitLabel = submitBtn?.querySelector(".register-submit-label");
const registerMessage = document.getElementById("registerMessage");
const firstNameInput = document.getElementById("first_name");
const lastNameInput = document.getElementById("last_name");
const emailInput = document.getElementById("email");
const emailFeedback = document.getElementById("emailFeedback");
const phoneInput = document.getElementById("phone");
const phoneFeedback = document.getElementById("phoneFeedback");
const passwordInput = document.getElementById("password");
const passwordFeedback = document.getElementById("passwordFeedback");
const birthdayInput = document.getElementById("birthday");
const birthdayFeedback = document.getElementById("birthdayFeedback");
const togglePassword = document.getElementById("togglePassword");
const turnstileFeedback = document.getElementById("turnstileFeedback");
const hasTurnstile = Boolean(document.querySelector(".cf-turnstile"));

const REGISTER_TIMEOUT_MS = 20000;
const CHECK_TIMEOUT_MS = 6000;
let isSubmitting = false;

const messageStyles = {
    success: "border-emerald-200 bg-emerald-50 text-emerald-800",
    warning: "border-amber-200 bg-amber-50 text-amber-800",
    error: "border-red-200 bg-red-50 text-red-800",
};

const feedbackMap = {
    email: { input: emailInput, feedback: emailFeedback },
    phone_number: { input: phoneInput, feedback: phoneFeedback },
    birthday: { input: birthdayInput, feedback: birthdayFeedback },
    password: { input: passwordInput, feedback: passwordFeedback },
    "cf-turnstile-response": { input: null, feedback: turnstileFeedback },
};

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
}

function showMessage(type, message) {
    if (!registerMessage) return;

    registerMessage.className = `mb-5 rounded-lg border px-4 py-3 text-sm font-medium ${messageStyles[type] || messageStyles.error}`;
    registerMessage.textContent = message;
    registerMessage.classList.remove("hidden");
}

function hideMessage() {
    registerMessage?.classList.add("hidden");
}

function setFieldError(field, message) {
    const target = feedbackMap[field];
    if (!target) return;

    target.input?.classList.add("input-error");
    if (target.feedback) {
        target.feedback.textContent = message || "";
    }
}

function clearFieldError(field) {
    const target = feedbackMap[field];
    if (!target) return;

    target.input?.classList.remove("input-error");
    if (target.feedback) {
        target.feedback.textContent = "";
    }
}

function turnstileToken() {
    return form?.querySelector('[name="cf-turnstile-response"]')?.value || "";
}

function resetTurnstile() {
    if (hasTurnstile && window.turnstile?.reset) {
        window.turnstile.reset();
    }
}

window.onRegisterTurnstileSuccess = function () {
    clearFieldError("cf-turnstile-response");
    checkFormValidity();
};

window.onRegisterTurnstileExpired = function () {
    setFieldError("cf-turnstile-response", "The security check expired. Please try again.");
    checkFormValidity();
};

window.onRegisterTurnstileError = function () {
    setFieldError("cf-turnstile-response", "The security check could not load. Please refresh and try again.");
    checkFormValidity();
};

function clearServerErrors() {
    Object.keys(feedbackMap).forEach(clearFieldError);
}

function applyValidationErrors(errors = {}) {
    Object.entries(errors).forEach(([field, messages]) => {
        const message = Array.isArray(messages) ? messages[0] : messages;
        setFieldError(field, message);
    });
}

function fetchWithTimeout(url, options = {}, timeoutMs = REGISTER_TIMEOUT_MS) {
    const controller = new AbortController();
    const timeout = window.setTimeout(() => controller.abort(), timeoutMs);

    return fetch(url, {
        ...options,
        signal: controller.signal,
    }).finally(() => window.clearTimeout(timeout));
}

function isFormReady() {
    const requiredFields = [
        firstNameInput,
        lastNameInput,
        emailInput,
        phoneInput,
        birthdayInput,
        passwordInput,
    ];

    const hasMissingValue = requiredFields.some((field) => !field || field.value.trim() === "");
    const hasFieldError = Object.values(feedbackMap).some(({ input }) => input?.classList.contains("input-error"));
    const hasMissingTurnstile = hasTurnstile && turnstileToken() === "";

    return !hasMissingValue && !hasFieldError && !hasMissingTurnstile;
}

function checkFormValidity() {
    if (!submitBtn) return;
    submitBtn.disabled = isSubmitting || !isFormReady();
}

function setSubmitting(submitting) {
    isSubmitting = submitting;
    if (submitBtn) {
        submitBtn.disabled = submitting || !isFormReady();
        submitBtn.setAttribute("aria-busy", submitting ? "true" : "false");
    }
    if (submitLabel) {
        submitLabel.textContent = submitting ? "Creating account..." : "Create account";
    }
}

function validateEmail() {
    const email = emailInput?.value.trim() || "";

    if (email === "") {
        clearFieldError("email");
        checkFormValidity();
        return false;
    }

    if (!emailInput.checkValidity()) {
        setFieldError("email", "Enter a valid email address.");
        checkFormValidity();
        return false;
    }

    clearFieldError("email");
    checkFormValidity();
    return true;
}

async function checkEmailAvailability() {
    if (!validateEmail()) return;

    try {
        const response = await fetchWithTimeout(`/check-email?email=${encodeURIComponent(emailInput.value.trim())}`, {
            headers: { Accept: "application/json" },
        }, CHECK_TIMEOUT_MS);
        const data = await response.json();

        if (data.exists) {
            setFieldError("email", "Email already exists.");
        } else {
            clearFieldError("email");
        }
    } catch (error) {
        clearFieldError("email");
    }

    checkFormValidity();
}

function validatePhone() {
    const phone = phoneInput?.value.trim() || "";
    const phRegex = /^09\d{9}$/;

    if (phone === "") {
        clearFieldError("phone_number");
        checkFormValidity();
        return false;
    }

    if (!phRegex.test(phone)) {
        setFieldError("phone_number", "Enter an 11-digit phone number starting with 09.");
        checkFormValidity();
        return false;
    }

    clearFieldError("phone_number");
    checkFormValidity();
    return true;
}

async function checkPhoneAvailability() {
    if (!validatePhone()) return;

    try {
        const response = await fetchWithTimeout(`/check-phone?phone=${encodeURIComponent(phoneInput.value.trim())}`, {
            headers: { Accept: "application/json" },
        }, CHECK_TIMEOUT_MS);
        const data = await response.json();

        if (data.exists) {
            setFieldError("phone_number", "Phone number already exists.");
        } else {
            clearFieldError("phone_number");
        }
    } catch (error) {
        clearFieldError("phone_number");
    }

    checkFormValidity();
}

function validatePassword() {
    const password = passwordInput?.value.trim() || "";

    if (password === "") {
        clearFieldError("password");
        checkFormValidity();
        return false;
    }

    if (password.length < 8 || password.length > 20) {
        setFieldError("password", "Password must be 8 to 20 characters.");
        checkFormValidity();
        return false;
    }

    clearFieldError("password");
    checkFormValidity();
    return true;
}

function validateBirthday() {
    const birthdayValue = birthdayInput?.value || "";

    if (!birthdayValue) {
        setFieldError("birthday", "Please select your birthday.");
        checkFormValidity();
        return false;
    }

    const today = new Date();
    const birthDate = new Date(`${birthdayValue}T00:00:00`);
    if (birthDate > today) {
        setFieldError("birthday", "Birthday cannot be in the future.");
        checkFormValidity();
        return false;
    }

    const age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    const dayDiff = today.getDate() - birthDate.getDate();
    const isUnderage = age < 18 || (age === 18 && (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)));

    if (isUnderage) {
        setFieldError("birthday", "You must be at least 18 years old.");
        checkFormValidity();
        return false;
    }

    clearFieldError("birthday");
    checkFormValidity();
    return true;
}

function validateBeforeSubmit() {
    const valid = [
        validateEmail(),
        validatePhone(),
        validateBirthday(),
        validatePassword(),
    ].every(Boolean);

    if (!valid) {
        showMessage("error", "Please fix the highlighted fields before creating your account.");
        return false;
    }

    if (hasTurnstile && turnstileToken() === "") {
        setFieldError("cf-turnstile-response", "Please complete the security check.");
        showMessage("error", "Please complete the security check before creating your account.");
        return false;
    }

    clearFieldError("cf-turnstile-response");
    return true;
}

emailInput?.addEventListener("input", () => {
    clearFieldError("email");
    hideMessage();
    checkFormValidity();
});
emailInput?.addEventListener("blur", checkEmailAvailability);

phoneInput?.addEventListener("input", () => {
    phoneInput.value = phoneInput.value.replace(/\D+/g, "").slice(0, 11);
    clearFieldError("phone_number");
    hideMessage();
    checkFormValidity();
});
phoneInput?.addEventListener("blur", checkPhoneAvailability);

passwordInput?.addEventListener("input", () => {
    clearFieldError("password");
    hideMessage();
    checkFormValidity();
});
passwordInput?.addEventListener("blur", validatePassword);

birthdayInput?.addEventListener("change", () => {
    hideMessage();
    validateBirthday();
});

[firstNameInput, lastNameInput].forEach((input) => {
    input?.addEventListener("input", () => {
        hideMessage();
        checkFormValidity();
    });
});

togglePassword?.addEventListener("click", () => {
    const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);
    togglePassword.classList.toggle("ri-eye-line");
    togglePassword.classList.toggle("ri-eye-off-line");
});

form?.addEventListener("submit", async (event) => {
    event.preventDefault();

    if (isSubmitting) return;
    clearServerErrors();

    if (!validateBeforeSubmit()) {
        checkFormValidity();
        return;
    }

    let willRedirect = false;
    setSubmitting(true);
    window.TKLoadingModal?.show();
    showMessage("warning", "Creating your account...");

    try {
        const response = await fetchWithTimeout(form.action, {
            method: "POST",
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": csrfToken(),
            },
            body: new FormData(form),
            credentials: "same-origin",
        });

        const data = await response.json().catch(() => ({}));

        if (response.ok) {
            const messageType = data.status === "email_sending_failed" ? "warning" : "success";
            showMessage(messageType, data.message || "Account created. Please continue to email verification.");

            const redirectUrl = data.redirect || (data.status === "verification_pending" ? "/email/verify" : "");
            if (redirectUrl) {
                willRedirect = true;
                window.location.replace(redirectUrl);
            } else {
                window.TKLoadingModal?.hide();
            }
            return;
        }

        if (response.status === 422 && data.errors) {
            applyValidationErrors(data.errors);
            showMessage("error", data.message || "Please check the highlighted fields.");
            resetTurnstile();
        } else if (response.status === 419) {
            showMessage("error", "Your session expired. Please refresh the page and try again.");
            resetTurnstile();
        } else if (response.status === 429) {
            showMessage("error", data.message || "Too many attempts. Please wait a moment before trying again.");
            resetTurnstile();
        } else {
            showMessage("error", data.message || "Registration failed. Please try again.");
            resetTurnstile();
        }
    } catch (error) {
        const timedOut = error.name === "AbortError";
        showMessage(
            "warning",
            timedOut
                ? "This is taking longer than expected. Please wait a moment, then try again."
                : "We could not reach the server. Please check your connection and try again."
        );
        resetTurnstile();
    } finally {
        if (!willRedirect) {
            window.TKLoadingModal?.hide();
            setSubmitting(false);
            checkFormValidity();
        }
    }
});

// =========================
// image carousel
// =========================
document.getElementById("valid-id")?.addEventListener("change", function (e) {
    const fileName = e.target.files.length ? e.target.files[0].name : "";
    document.getElementById("file-name").textContent = fileName;
});

let currentImage = 0;
const imageEl = document.getElementById("panelImage");
const dots = document.querySelectorAll(".dot");
let images = [];

try {
    images = JSON.parse(imageEl?.dataset.images || "[]").filter(Boolean);
} catch (error) {
    images = [];
}

function updateImage() {
    if (!imageEl || images.length === 0) return;

    imageEl.style.backgroundImage = `url('${images[currentImage]}')`;
    dots.forEach((dot, idx) => {
        const isActive = idx === currentImage;
        dot.classList.toggle("active", isActive);
        dot.classList.toggle("bg-indigo-600", isActive);
        dot.classList.toggle("bg-gray-300", !isActive);
    });
}

if (images.length > 0) {
    setInterval(() => {
        currentImage = (currentImage + 1) % images.length;
        updateImage();
    }, 4000);

    updateImage();
}

checkFormValidity();
