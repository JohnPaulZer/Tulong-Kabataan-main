<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Donate Items | Tulong Kabataan</title>
    <link rel="icon" href="{{ page_media_url('site_favicon', asset('img/log2.png')) }}" type="image/png" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="inkind-modal-page">
    @include('partials.main-header')

    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="hero-media" aria-hidden="true">
            <img src="{{ page_media_url('donation_hero_image', asset('img/bg1.jpg')) }}" alt="" class="hero-image">
            <div class="hero-overlay"></div>
        </div>
        <div class="hero-content">
            <a href="{{ route('inkind.page') }}" class="hero-back-link" aria-label="Back to In-Kind Donations">
                <i class="ri-arrow-left-line" aria-hidden="true"></i>
                <span>Back</span>
            </a>
            <h1>Donate Your Items</h1>
            <p>Share usable goods with communities that need them. Complete the form below and choose your preferred drop-off location.</p>
        </div>
        @include('partials.wave-divider')
    </section>

    <!-- Donation Form -->
    <div class="tk-container">
        <div class="tk-page-head">
            <div class="tk-page-head-icon">
                <i class="ri-gift-2-line"></i>
            </div>
            <h2 class="tk-title">Donate Your Items</h2>
            <p class="tk-sub">Complete the form below to submit your in-kind donation. All fields marked with <span class="tk-req">*</span> are required.</p>
            <div class="tk-progress-bar">
                <div class="tk-progress-step active">
                    <span class="step-num">1</span>
                    <span class="step-label">Details</span>
                </div>
                <div class="tk-progress-line"></div>
                <div class="tk-progress-step">
                    <span class="step-num">2</span>
                    <span class="step-label">Items</span>
                </div>
                <div class="tk-progress-line"></div>
                <div class="tk-progress-step">
                    <span class="step-num">3</span>
                    <span class="step-label">Drop-off</span>
                </div>
                <div class="tk-progress-line"></div>
                <div class="tk-progress-step">
                    <span class="step-num">4</span>
                    <span class="step-label">Confirm</span>
                </div>
            </div>
        </div>

        <form class="donation-form" id="donationForm" action="{{ route('inkind.donate') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Step 1: Donor Information -->
            <div class="tk-step" data-step="1">
                @if (!auth()->check())
                    <section class="tk-section">
                        <div class="tk-sec-head">
                            <h3 class="tk-sec-title"><i class="ri-user-line"></i> Donor Information</h3>
                        </div>
                        <div class="tk-grid-2">
                            <div class="tk-field">
                                <label class="tk-label" for="donor_name">Full Name</label>
                                <input type="text" id="donor_name" name="donor_name" class="tk-input" placeholder="Your full name">
                                <span class="tk-help">Leave blank to donate anonymously</span>
                            </div>
                            <div class="tk-field">
                                <label class="tk-label" for="donor_email">Email <span class="tk-req">*</span></label>
                                <input type="email" id="donor_email" name="donor_email" class="tk-input"
                                    placeholder="Your email address" required>
                            </div>
                        </div>
                        <div class="tk-grid-2">
                            <div class="tk-field">
                                <label class="tk-label" for="donor_phone">Phone Number</label>
                                <input type="tel" id="donor_phone" name="donor_phone" class="tk-input"
                                    placeholder="09XXXXXXXXX" pattern="^09\d{9}$" maxlength="11"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,11)">
                            </div>
                        </div>
                    </section>
                @else
                    <input type="hidden" name="donor_name"
                        value="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}">
                    <input type="hidden" name="donor_email" value="{{ auth()->user()->email }}">
                    <input type="hidden" name="donor_phone" value="{{ auth()->user()->phone_number }}">
                    <section class="tk-section">
                        <div class="tk-sec-head">
                            <h3 class="tk-sec-title"><i class="ri-user-line"></i> Donor Information</h3>
                        </div>
                        <div class="tk-info-card">
                            <div class="tk-info-row">
                                <span class="tk-info-label"><i class="ri-user-3-line"></i> Name</span>
                                <span class="tk-info-value">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
                            </div>
                            <div class="tk-info-row">
                                <span class="tk-info-label"><i class="ri-mail-line"></i> Email</span>
                                <span class="tk-info-value">{{ auth()->user()->email }}</span>
                            </div>
                            <div class="tk-info-row">
                                <span class="tk-info-label"><i class="ri-phone-line"></i> Phone</span>
                                <span class="tk-info-value">{{ auth()->user()->phone_number ?? 'Not provided' }}</span>
                            </div>
                        </div>
                    </section>
                @endif

                <input type="hidden" name="user_id" value="{{ auth()->check() ? auth()->user()->user_id : '' }}">

                <div class="tk-step-nav">
                    <div></div>
                    <button type="button" class="tk-btn primary tk-next-btn" data-next="2">
                        Next: Donation Items <i class="ri-arrow-right-line"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: Items Section -->
            <div class="tk-step" data-step="2" style="display: none;">
                <section class="tk-section">
                    <div class="tk-sec-head">
                        <h3 class="tk-sec-title"><i class="ri-gift-line"></i> Donation Items</h3>
                        <button type="button" id="addItemBtn" class="tk-btn-add">
                            <i class="ri-add-line"></i> Add Item
                        </button>
                    </div>

                    <div id="itemsContainer">
                        <div class="item-row" data-item-index="0">
                            <div class="item-header">
                                <h4 class="item-number">Item #1</h4>
                                <button type="button" class="remove-item-btn" style="display: none;">
                                    <i class="ri-delete-bin-line"></i> Remove
                                </button>
                            </div>

                            <div class="tk-field">
                                <label class="tk-label" for="items[0][item_name]">Item Name <span class="tk-req">*</span></label>
                                <input type="text" id="items[0][item_name]" name="items[0][item_name]" class="tk-input"
                                    placeholder="Item Name" required>
                            </div>

                            <div class="tk-grid-2">
                                <div class="tk-field">
                                    <label class="tk-label" for="items[0][category]">Category <span class="tk-req">*</span></label>
                                    <select id="items[0][category]" name="items[0][category]" class="tk-input" required>
                                        <option value="">Select a category</option>
                                        <option value="Food & Potable Water">Food & Potable Water</option>
                                        <option value="Hygiene & Sanitation Kits">Hygiene & Sanitation Kits</option>
                                        <option value="Sleeping & Evacuation Kits">Sleeping & Evacuation Kits</option>
                                        <option value="Kitchen & Family Kits">Kitchen & Family Kits</option>
                                        <option value="First Aid & Health">First Aid & Health</option>
                                        <option value="Emergency Power & Safety">Emergency Power & Safety</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="tk-field">
                                    <label class="tk-label" for="items[0][quantity]">Quantity <span class="tk-req">*</span></label>
                                    <input type="number" id="items[0][quantity]" name="items[0][quantity]" class="tk-input"
                                        placeholder="Number of items" required min="1"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                            </div>

                            <div class="tk-field">
                                <label class="tk-label" for="items[0][description]">Description</label>
                                <textarea id="items[0][description]" name="items[0][description]" rows="3" class="tk-input tk-textarea"
                                    placeholder="Describe the item's condition, brand, size, etc."></textarea>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="tk-step-nav">
                    <button type="button" class="tk-btn outline tk-prev-btn" data-prev="1">
                        <i class="ri-arrow-left-line"></i> Back
                    </button>
                    <button type="button" class="tk-btn primary tk-next-btn" data-next="3">
                        Next: Drop-off Location <i class="ri-arrow-right-line"></i>
                    </button>
                </div>
            </div>

            <!-- Step 3: Drop-off Location -->
            <div class="tk-step" data-step="3" style="display: none;">
                <section class="tk-section">
                    <div class="tk-sec-head">
                        <h3 class="tk-sec-title"><i class="ri-map-pin-line"></i> Drop-off Location</h3>
                    </div>
                    <div class="tk-field">
                        <label class="tk-label" for="dropoff_id">Preferred Drop-off Location <span class="tk-req">*</span></label>
                        <select id="dropoff_id" name="dropoff_id" class="tk-input" required>
                            <option value="">Select a location</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->dropoff_id }}" {{ !$location->is_active ? 'disabled' : '' }}>
                                    {{ $location->name }} - {{ $location->address }}
                                    @if (!$location->is_active)
                                        (Currently unavailable)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="tk-availability">
                        <span class="tk-availability-title"><i class="ri-time-line"></i> Location Availability</span>
                        <ul class="tk-availability-list">
                            @foreach ($locations as $location)
                                <li><span class="tk-dot"></span> {{ $location->name }}: {{ $location->schedule_datetime ?? 'Not specified' }}</li>
                            @endforeach
                        </ul>
                    </div>
                </section>

                <div class="tk-step-nav">
                    <button type="button" class="tk-btn outline tk-prev-btn" data-prev="2">
                        <i class="ri-arrow-left-line"></i> Back
                    </button>
                    <button type="button" class="tk-btn primary tk-next-btn" data-next="4">
                        Next: Confirmation <i class="ri-arrow-right-line"></i>
                    </button>
                </div>
            </div>

            <!-- Step 4: Confirmation -->
            <div class="tk-step" data-step="4" style="display: none;">
                <section class="tk-section">
                    <div class="tk-sec-head">
                        <h3 class="tk-sec-title"><i class="ri-checkbox-circle-line"></i> Confirmation</h3>
                    </div>

                    <div class="tk-review-summary" id="reviewSummary"></div>

                    <div class="tk-checks">
                        <label class="tk-check">
                            <input type="checkbox" id="condition-confirmation" required>
                            <span>I confirm these items are in good, usable condition</span>
                        </label>
                        <label class="tk-check">
                            <input type="checkbox" id="review-confirmation" required>
                            <span>I understand that I will be the one to deliver the in-kind donation to the selected drop-off point.</span>
                        </label>
                    </div>
                </section>

                <div class="tk-step-nav">
                    <button type="button" class="tk-btn outline tk-prev-btn" data-prev="3">
                        <i class="ri-arrow-left-line"></i> Back
                    </button>
                    <button type="submit" class="tk-btn primary">
                        <i class="ri-send-plane-fill"></i> Submit Donation
                    </button>
                </div>
            </div>
        </form>
    </div>

    @include('partials.main-footer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('donationForm');
            const steps = document.querySelectorAll('.tk-step');
            const progressSteps = document.querySelectorAll('.tk-progress-step');
            const progressLines = document.querySelectorAll('.tk-progress-line');
            let currentStep = 1;

            function validateStep(stepNum) {
                const stepEl = document.querySelector(`.tk-step[data-step="${stepNum}"]`);
                const requiredFields = stepEl.querySelectorAll('[required]');
                let valid = true;

                requiredFields.forEach(field => {
                    if (field.type === 'checkbox') {
                        return;
                    }
                    if (!field.value.trim()) {
                        field.classList.add('tk-input-error');
                        valid = false;
                    } else {
                        field.classList.remove('tk-input-error');
                    }
                    if (field.type === 'email' && field.value.trim() && !field.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                        field.classList.add('tk-input-error');
                        valid = false;
                    }
                });

                if (!valid) {
                    const firstError = stepEl.querySelector('.tk-input-error');
                    if (firstError) {
                        firstError.focus();
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
                return valid;
            }

            function goToStep(stepNum) {
                steps.forEach(s => {
                    s.style.display = 'none';
                    s.classList.remove('tk-step-active');
                });

                const target = document.querySelector(`.tk-step[data-step="${stepNum}"]`);
                target.style.display = 'block';
                setTimeout(() => target.classList.add('tk-step-active'), 10);

                progressSteps.forEach((ps, idx) => {
                    const num = idx + 1;
                    ps.classList.remove('active', 'completed');
                    if (num < stepNum) {
                        ps.classList.add('completed');
                    } else if (num === stepNum) {
                        ps.classList.add('active');
                    }
                });

                progressLines.forEach((line, idx) => {
                    if (idx + 1 < stepNum) {
                        line.classList.add('filled');
                    } else {
                        line.classList.remove('filled');
                    }
                });

                currentStep = stepNum;

                if (stepNum === 4) {
                    buildReviewSummary();
                }

                document.querySelector('.tk-page-head').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            document.querySelectorAll('.tk-next-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const nextStep = parseInt(this.dataset.next);
                    if (validateStep(currentStep)) {
                        goToStep(nextStep);
                    }
                });
            });

            document.querySelectorAll('.tk-prev-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const prevStep = parseInt(this.dataset.prev);
                    goToStep(prevStep);
                });
            });

            progressSteps.forEach((ps, idx) => {
                ps.addEventListener('click', function() {
                    const targetStep = idx + 1;
                    if (targetStep < currentStep) {
                        goToStep(targetStep);
                    } else if (targetStep > currentStep) {
                        let canGo = true;
                        for (let i = currentStep; i < targetStep; i++) {
                            if (!validateStep(i)) { canGo = false; break; }
                        }
                        if (canGo) goToStep(targetStep);
                    }
                });
            });

            function buildReviewSummary() {
                const summary = document.getElementById('reviewSummary');
                let html = '';

                const donorName = document.querySelector('[name="donor_name"]');
                const donorEmail = document.querySelector('[name="donor_email"]');
                const name = donorName ? donorName.value || 'Anonymous' : 'Anonymous';
                const email = donorEmail ? donorEmail.value : '';

                html += `<div class="tk-review-group">
                    <h4 class="tk-review-heading"><i class="ri-user-line"></i> Donor</h4>
                    <p>${name}${email ? ' &middot; ' + email : ''}</p>
                </div>`;

                const items = document.querySelectorAll('.item-row');
                html += `<div class="tk-review-group">
                    <h4 class="tk-review-heading"><i class="ri-gift-line"></i> Items (${items.length})</h4>
                    <ul>`;
                items.forEach((item, i) => {
                    const itemName = item.querySelector(`[name="items[${i}][item_name]"]`)?.value || 'Unnamed';
                    const category = item.querySelector(`[name="items[${i}][category]"]`)?.value || '';
                    const qty = item.querySelector(`[name="items[${i}][quantity]"]`)?.value || '';
                    html += `<li><strong>${itemName}</strong> &middot; ${category} &middot; Qty: ${qty}</li>`;
                });
                html += `</ul></div>`;

                const dropoff = document.getElementById('dropoff_id');
                const selectedOption = dropoff.options[dropoff.selectedIndex];
                html += `<div class="tk-review-group">
                    <h4 class="tk-review-heading"><i class="ri-map-pin-line"></i> Drop-off</h4>
                    <p>${selectedOption ? selectedOption.text : 'Not selected'}</p>
                </div>`;

                summary.innerHTML = html;
            }

            let itemCount = 1;
            const itemsContainer = document.getElementById('itemsContainer');
            const addItemBtn = document.getElementById('addItemBtn');

            addItemBtn.addEventListener('click', function() {
                const newIndex = itemCount;
                const newItem = document.createElement('div');
                newItem.className = 'item-row';
                newItem.setAttribute('data-item-index', newIndex);

                newItem.innerHTML = `
                    <div class="item-header">
                        <h4 class="item-number">Item #${newIndex + 1}</h4>
                        <button type="button" class="remove-item-btn">
                            <i class="ri-delete-bin-line"></i> Remove
                        </button>
                    </div>

                    <div class="tk-field">
                        <label class="tk-label" for="items[${newIndex}][item_name]">Item Name <span class="tk-req">*</span></label>
                        <input type="text" id="items[${newIndex}][item_name]" name="items[${newIndex}][item_name]" class="tk-input" placeholder="Item Name" required>
                    </div>

                    <div class="tk-grid-2">
                        <div class="tk-field">
                            <label class="tk-label" for="items[${newIndex}][category]">Category <span class="tk-req">*</span></label>
                            <select id="items[${newIndex}][category]" name="items[${newIndex}][category]" class="tk-input" required>
                                <option value="">Select a category</option>
                                <option value="Food & Potable Water">Food & Potable Water</option>
                                <option value="Hygiene & Sanitation Kits">Hygiene & Sanitation Kits</option>
                                <option value="Sleeping & Evacuation Kits">Sleeping & Evacuation Kits</option>
                                <option value="Kitchen & Family Kits">Kitchen & Family Kits</option>
                                <option value="First Aid & Health">First Aid & Health</option>
                                <option value="Emergency Power & Safety">Emergency Power & Safety</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="tk-field">
                            <label class="tk-label" for="items[${newIndex}][quantity]">Quantity <span class="tk-req">*</span></label>
                            <input type="number" id="items[${newIndex}][quantity]" name="items[${newIndex}][quantity]" class="tk-input" placeholder="Number of items" required min="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>

                    <div class="tk-field">
                        <label class="tk-label" for="items[${newIndex}][description]">Description</label>
                        <textarea id="items[${newIndex}][description]" name="items[${newIndex}][description]" rows="3" class="tk-input tk-textarea" placeholder="Describe the item's condition, brand, size, etc."></textarea>
                    </div>
                `;

                itemsContainer.appendChild(newItem);
                itemCount++;

                document.querySelectorAll('.remove-item-btn').forEach(btn => {
                    btn.style.display = 'inline-flex';
                });

                newItem.querySelector('.remove-item-btn').addEventListener('click', function() {
                    newItem.remove();
                    updateItemNumbers();
                });
            });

            function updateItemNumbers() {
                const items = document.querySelectorAll('.item-row');
                items.forEach((item, index) => {
                    const header = item.querySelector('h4');
                    header.textContent = `Item #${index + 1}`;
                    item.setAttribute('data-item-index', index);
                    if (items.length === 1) {
                        item.querySelector('.remove-item-btn').style.display = 'none';
                    }
                });
                itemCount = items.length;
            }

            document.querySelectorAll('.remove-item-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const itemRow = this.closest('.item-row');
                    if (document.querySelectorAll('.item-row').length > 1) {
                        itemRow.remove();
                        updateItemNumbers();
                    }
                });
            });

            const dropdown = document.getElementById('dropoff_id');
            if (dropdown) {
                const options = dropdown.options;
                const maxChars = 45;
                for (let i = 0; i < options.length; i++) {
                    let text = options[i].text;
                    if (text.length > maxChars) {
                        options[i].text = text.substring(0, maxChars) + '...';
                        options[i].title = text;
                    }
                }
            }
        });
    </script>
    <script>
        (function() {
            if (!window.visualViewport) return;
            const threshold = 150;
            let initialHeight = window.visualViewport.height;

            function onResize() {
                const diff = initialHeight - window.visualViewport.height;
                if (diff > threshold) {
                    document.body.classList.add('keyboard-open');
                } else {
                    document.body.classList.remove('keyboard-open');
                }
            }

            window.visualViewport.addEventListener('resize', onResize);
            window.addEventListener('orientationchange', function() {
                setTimeout(function() { initialHeight = window.visualViewport.height; }, 200);
            });
        })();
    </script>
</body>

</html>
