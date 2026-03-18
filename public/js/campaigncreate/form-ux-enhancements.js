// form-ux-enhancements.js
document.addEventListener("DOMContentLoaded", function () {
    /* ====== Form UX Enhancements ====== */
    const scheduleType = document.getElementById("schedule_type");
    const segButtons = document.querySelectorAll(".seg-opt");
    const recurringDaysContainer = document.getElementById(
        "recurring_days_container"
    );
    const oneTimeDates = document.getElementById("one_time_dates");
    const startsAt = document.getElementById("starts_at");
    const endsAt = document.getElementById("ends_at");
    const schedulePreview = document.getElementById("schedule_preview");
    const scheduleText = document.getElementById("schedule_text");
    const form = document.querySelector("form.campaign-form");

    // Segment button functionality
    if (segButtons.length > 0) {
        segButtons.forEach((btn) => {
            btn.addEventListener("click", () => {
                segButtons.forEach((b) => {
                    b.classList.remove("is-active");
                    b.setAttribute("aria-selected", "false");
                });
                btn.classList.add("is-active");
                btn.setAttribute("aria-selected", "true");
                scheduleType.value = btn.dataset.value;
                toggleScheduleUI();
            });
        });
    }

    function toggleScheduleUI() {
        const isRecurring = scheduleType.value === "recurring";
        if (recurringDaysContainer)
            recurringDaysContainer.style.display = isRecurring
                ? "grid"
                : "none";
        if (oneTimeDates)
            oneTimeDates.style.display = isRecurring ? "none" : "grid";
        updateSchedulePreview();
    }

    function updateSchedulePreview() {
        if (
            !schedulePreview ||
            !scheduleText ||
            scheduleType.value !== "one_time"
        )
            return;

        if (!startsAt.value) {
            schedulePreview.classList.add("immediate");
            schedulePreview.classList.remove("scheduled");
            scheduleText.textContent = "Campaign will be published immediately";
        } else {
            const startDate = new Date(startsAt.value);
            const now = new Date();
            if (startDate <= now) {
                schedulePreview.classList.add("immediate");
                schedulePreview.classList.remove("scheduled");
                scheduleText.textContent =
                    "Campaign will be published immediately (start time is in the past)";
            } else {
                schedulePreview.classList.add("scheduled");
                schedulePreview.classList.remove("immediate");
                const opts = {
                    year: "numeric",
                    month: "long",
                    day: "numeric",
                    hour: "2-digit",
                    minute: "2-digit",
                };
                scheduleText.textContent = `Campaign is scheduled for: ${startDate.toLocaleDateString(
                    "en-US",
                    opts
                )}`;
            }
        }
    }

    function toDatetimeLocalValue(d) {
        const pad = (n) => String(n).padStart(2, "0");
        return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    }

    function setMinDatetimes() {
        const now = new Date();
        now.setSeconds(0, 0);
        const iso = toDatetimeLocalValue(now);
        if (startsAt) startsAt.min = iso;
        if (endsAt) endsAt.min = iso;
    }

    // Event listeners for date inputs
    if (startsAt) {
        startsAt.addEventListener("change", () => {
            if (startsAt.value && endsAt) endsAt.min = startsAt.value;
            updateSchedulePreview();
        });
    }

    // Form validation for date ranges
    if (form) {
        form.addEventListener("submit", (e) => {
            if (startsAt && startsAt.value && endsAt && endsAt.value) {
                const s = new Date(startsAt.value);
                const en = new Date(endsAt.value);
                if (en < s) {
                    e.preventDefault();
                    alert(
                        "End Date & Time must be the same or later than Start Date & Time."
                    );
                    return false;
                }
            }
        });
    }

    // Character counters
    const titleEl = document.getElementById("title");
    const titleCount = document.getElementById("titleCount");
    if (titleEl && titleCount) {
        const upd = () => (titleCount.textContent = titleEl.value.length);
        titleEl.addEventListener("input", upd);
        upd();
    }

    const descEl = document.getElementById("description");
    const descCount = document.getElementById("descCount");
    if (descEl && descCount) {
        const upd = () => (descCount.textContent = descEl.value.length);
        descEl.addEventListener("input", upd);
        upd();
    }

    // Goal amount helper
    const goalEl = document.getElementById("target_amount");
    const goalHelper = document.getElementById("goalHelper");
    if (goalEl && goalHelper) {
        goalEl.addEventListener("input", () => {
            const v = Number(goalEl.value || 0);
            goalHelper.textContent = v
                ? `Goal preview: ₱${v.toLocaleString()}`
                : "Tip: set a realistic goal donors can help achieve.";
        });
    }

    // Initialize
    toggleScheduleUI();
    setMinDatetimes();
    updateSchedulePreview();
});
