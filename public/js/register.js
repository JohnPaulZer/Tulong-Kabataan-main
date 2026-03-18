// =========================
// form validation
// =========================
const submitBtn = document.getElementById("submitBtn");
const emailInput = document.getElementById("email");
const emailFeedback = document.getElementById("emailFeedback");
const phoneInput = document.getElementById("phone");
const phoneFeedback = document.getElementById("phoneFeedback");
const passwordInput = document.getElementById("password");
const passwordFeedback = document.getElementById("passwordFeedback");
const togglePassword = document.getElementById("togglePassword");

function checkFormValidity() {
    const hasEmailError =
        emailInput.classList.contains("input-error") ||
        emailInput.value.trim() === "";
    const hasPhoneError =
        phoneInput.classList.contains("input-error") ||
        phoneInput.value.trim() === "";
    const hasPasswordError =
        passwordInput.classList.contains("input-error") ||
        passwordInput.value.trim() === "";

    if (!hasEmailError && !hasPhoneError && !hasPasswordError) {
        submitBtn.disabled = false;
        submitBtn.style.background = "";
        submitBtn.style.cursor = "pointer";
    } else {
        submitBtn.disabled = true;
        submitBtn.style.background = "#888";
        submitBtn.style.cursor = "not-allowed";
    }
}

// EMAIL VALIDATION
emailInput.addEventListener("blur", () => {
    const email = emailInput.value.trim();
    const gmailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;

    if (email === "") {
        emailFeedback.textContent = "";
        emailInput.classList.remove("input-error");
        checkFormValidity();
        return;
    }

    if (!gmailRegex.test(email)) {
        emailFeedback.textContent = "Invalid Email";
        emailInput.classList.add("input-error");
        checkFormValidity();
        return;
    }

    fetch(`/check-email?email=${encodeURIComponent(email)}`)
        .then((res) => res.json())
        .then((data) => {
            if (data.exists) {
                emailFeedback.textContent = "Email already exists!";
                emailInput.classList.add("input-error");
            } else {
                emailFeedback.textContent = "";
                emailInput.classList.remove("input-error");
            }
            checkFormValidity();
        })
        .catch((err) => console.error(err));
});

// PHONE VALIDATION
phoneInput.addEventListener("input", () => {
    phoneInput.value = phoneInput.value.replace(/\s+/g, "");
    phoneInput.classList.remove("input-error");
    phoneFeedback.textContent = "";
    checkFormValidity();
});

phoneInput.addEventListener("blur", () => {
    const phone = phoneInput.value.trim();
    const phRegex = /^09\d{9}$/;

    if (!phRegex.test(phone) && phone.length > 0) {
        phoneFeedback.textContent = "Invalid phone number!";
        phoneInput.classList.add("input-error");
        checkFormValidity();
        return;
    }

    if (phone.length === 11) {
        fetch(`/check-phone?phone=${encodeURIComponent(phone)}`)
            .then((res) => res.json())
            .then((data) => {
                if (data.exists) {
                    phoneFeedback.textContent = "Phone number already exists!";
                    phoneInput.classList.add("input-error");
                } else {
                    phoneFeedback.textContent = "";
                    phoneInput.classList.remove("input-error");
                }
                checkFormValidity();
            })
            .catch((err) => console.error(err));
    } else {
        checkFormValidity();
    }
});

// PASSWORD VALIDATION
passwordInput.addEventListener("input", () => {
    passwordInput.classList.remove("input-error");
    passwordFeedback.textContent = "";
    checkFormValidity();
});

passwordInput.addEventListener("blur", () => {
    const password = passwordInput.value.trim();
    if (password.length === 0) return;

    if (password.length < 8 || password.length > 20) {
        passwordInput.classList.add("input-error");
        passwordFeedback.textContent = "Password must be 8 to 20 characters!";
    } else {
        passwordInput.classList.remove("input-error");
        passwordFeedback.textContent = "";
    }
    checkFormValidity();
});

// BIRTHDAY VALIDATION
const birthdayInput = document.getElementById("birthday");
const birthdayFeedback = document.getElementById("birthdayFeedback");

birthdayInput.addEventListener("change", () => {
    const birthdayValue = birthdayInput.value;
    birthdayInput.classList.remove("input-error");
    birthdayFeedback.textContent = "";

    if (!birthdayValue) {
        birthdayFeedback.textContent = "Please select your birthday.";
        birthdayInput.classList.add("input-error");
        checkFormValidity();
        return;
    }

    const today = new Date();
    const birthDate = new Date(birthdayValue);
    if (birthDate > today) {
        birthdayFeedback.textContent = "Birthday cannot be in the future!";
        birthdayInput.classList.add("input-error");
        checkFormValidity();
        return;
    }

    const age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    const dayDiff = today.getDate() - birthDate.getDate();

    const isUnderage =
        age < 18 ||
        (age === 18 && (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)));

    if (isUnderage) {
        birthdayFeedback.textContent = "You must be at least 18 years old.";
        birthdayInput.classList.add("input-error");
    } else {
        birthdayFeedback.textContent = "";
        birthdayInput.classList.remove("input-error");
    }

    checkFormValidity();
});

// PASSWORD TOGGLE
togglePassword.addEventListener("click", () => {
    const type =
        passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);
    togglePassword.classList.toggle("ri-eye-line");
    togglePassword.classList.toggle("ri-eye-off-line");
});

// =========================
// image carousel
// =========================
document.getElementById("valid-id")?.addEventListener("change", function (e) {
    const fileName = e.target.files.length ? e.target.files[0].name : "";
    document.getElementById("file-name").textContent = fileName;
});

const images = ["img/eyy.png", "img/yagit.png", "img/diss.jpg"];

let currentImage = 0;
const imageEl = document.getElementById("panelImage");
const dots = document.querySelectorAll(".dot");

function updateImage() {
    imageEl.style.backgroundImage = `url('${images[currentImage]}')`;
    dots.forEach((dot, idx) => {
        dot.classList.toggle("active", idx === currentImage);
    });
}

setInterval(() => {
    currentImage = (currentImage + 1) % images.length;
    updateImage();
}, 4000);

updateImage();

// Initial check
checkFormValidity();
