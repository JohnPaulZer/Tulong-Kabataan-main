<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tulong Kabataan</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    <!-- Remixicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/verfication.css') }}">
</head>

<body>
    @include('partials.main-header')

    @if ($errors->any())
        <div class="verification-container verification-page-wrap verification-pt-3">
            <div class="verification-alert verification-alert-danger">
                <ul class="verification-mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if (session('message'))
        <div class="verification-container verification-page-wrap verification-pt-3">
            <div class="verification-alert verification-alert-success">
                {{ session('message') }}
            </div>
        </div>
    @endif

    <div class="verification-container verification-page-wrap verification-py-4 verification-py-md-5">
        <div class="verification-card">
            <div
                class="verification-card-header verification-p-4 verification-d-flex verification-align-items-center verification-justify-content-between">
                <!-- LEFT: Back + Title -->
                <div class="verification-w-100">
                    <div
                        class="verification-d-flex verification-align-items-center verification-gap-2 verification-mb-2 verification-flex-wrap">
                        <button type="button" class="verification-btn verification-btn-back" id="pageBackBtn"
                            aria-label="Go back">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            <span class="verification-ms-1 verification-d-none verification-d-sm-inline">Back</span>
                        </button>
                    </div>
                    <h1 class="verification-h4 verification-mb-1">Identity Verification</h1>
                    <p class="verification-mb-0 verification-text-muted">Provide your legal details and upload your
                        documents.</p>
                </div>

                <!-- RIGHT: Security badge -->
                <span class="verification-badge verification-text-bg-light verification-ms-3">Secure • Encrypted</span>
            </div>

            <div class="verification-card-body verification-p-4 verification-p-md-5">

                @php
                    $vr = \App\Models\VerificationRequest::where('user_id', auth()->id())
                        ->latest('created_at')
                        ->first();
                    $reuploadFields = $vr && $vr->status === 'reupload' ? $vr->reupload_fields ?? [] : [];
                @endphp

                {{-- Show admin reupload message --}}
                @if ($vr && $vr->status === 'reupload')
                    <div class="verification-alert verification-alert-warning verification-mb-4">
                        <strong>⚠️ Reupload Required</strong><br>
                        {{ $vr->review_notes ?? 'Please reupload the requested documents.' }}
                    </div>
                @endif

                <!-- Wizard stepper -->
                <div class="verification-stepper"></div>

                <form action="{{ route('submit.verification') }}" method="POST" enctype="multipart/form-data"
                    id="kycForm" novalidate>
                    @csrf
                    @if ($vr && $vr->status === 'reupload')
                        <input type="hidden" name="mode" value="reupload">
                    @endif
                    <input type="hidden" id="_step" name="_step" value="1">

                    <!-- STEP 1: ADD ID -->
                    @if (!$vr || $vr->status !== 'reupload' || in_array('id_front', $reuploadFields) || in_array('id_back', $reuploadFields))
                        <section class="verification-step verification-show" id="step-1">
                            <h2
                                class="verification-h6 verification-text-uppercase verification-text-muted verification-mb-3">
                                Upload ID</h2>
                            <div class="verification-row verification-g-3 verification-g-md-4">

                                <!-- ID Type -->
                                <div class="verification-col-12 verification-col-md-6">
                                    <label for="id_type" class="verification-form-label verification-required">ID
                                        Type</label>
                                    <select name="id_type" id="id_type" class="verification-form-select"
                                        @if ($vr && $vr->status === 'reupload') disabled @endif>
                                        <option value="">-- Select --</option>
                                        <option value="philid" @if (old('id_type', $vr->id_type ?? '') === 'philid') selected @endif>PhilSys
                                            National ID</option>
                                        <option value="drivers_license"
                                            @if (old('id_type', $vr->id_type ?? '') === 'drivers_license') selected @endif>Driver's License</option>
                                    </select>
                                </div>

                                <!-- Upload Front -->
                                <div class="verification-col-12 verification-col-md-6">
                                    <label for="id_front" class="verification-form-label verification-required">Upload
                                        ID (Front)</label>
                                    <input type="file" name="id_front" id="id_front"
                                        class="verification-form-control" accept="image/*" capture="environment"
                                        @if ($vr && $vr->status === 'reupload') @if (in_array('id_front', $reuploadFields)) required @else disabled @endif
                                    @else required @endif />
                                    <div class="verification-preview-box verification-mt-2">
                                        <img id="preview_front" alt="Front Preview" class="verification-d-none" />
                                        <span class="verification-placeholder-text">No file selected</span>
                                    </div>
                                </div>

                                <!-- Upload Back -->
                                <div class="verification-col-12 verification-col-md-6" id="idBackWrapper"
                                    style="display:none;">
                                    <label for="id_back" class="verification-form-label">Upload ID (Back)</label>
                                    <input type="file" name="id_back" id="id_back"
                                        class="verification-form-control" accept="image/*" capture="environment"
                                        @if ($vr && $vr->status === 'reupload') @if (in_array('id_back', $reuploadFields)) required @else disabled @endif
                                        @endif />
                                    <div class="verification-preview-box verification-mt-2">
                                        <img id="preview_back" alt="Back Preview" class="verification-d-none" />
                                        <span class="verification-placeholder-text">No file selected</span>
                                    </div>
                                </div>

                                <!-- Expiry Date -->
                                <div class="verification-col-12 verification-col-md-6" id="idExpiryWrapper"
                                    style="display:none;">
                                    <label for="id_expiry" class="verification-form-label">ID Expiry Date</label>
                                    <input type="date" name="id_expiry" id="id_expiry"
                                        class="verification-form-control"
                                        @if ($vr && $vr->status === 'reupload') disabled @endif />
                                </div>

                            </div>
                        </section>
                    @endif

                    <!-- STEP 2: Facial Photo -->
                    @if (!$vr || $vr->status !== 'reupload' || in_array('face_photo', $reuploadFields))
                        <section class="verification-step" id="step-2">
                            <h2
                                class="verification-h6 verification-text-uppercase verification-text-muted verification-mb-3">
                                Facial Photo</h2>
                            <div class="verification-col-12 verification-col-md-8">
                                <label for="face_photo" class="verification-form-label verification-required">Take a
                                    face-only photo</label>
                                <input type="file" name="face_photo" id="face_photo"
                                    class="verification-form-control" accept="image/*" capture="user"
                                    @if ($vr && $vr->status === 'reupload') @if (in_array('face_photo', $reuploadFields)) required @else disabled @endif
                                @else required @endif />
                                <div class="verification-form-text">Remove hats/masks; good lighting required.</div>
                                <div class="verification-preview-box verification-mt-2">
                                    <img id="preview_face" alt="Face Preview" class="verification-d-none" />
                                    <span class="verification-placeholder-text">No file selected</span>
                                </div>
                            </div>
                        </section>
                    @endif

                    <!-- STEP 3: Selfie with ID -->
                    @if (!$vr || $vr->status !== 'reupload' || in_array('selfie', $reuploadFields))
                        <section class="verification-step" id="step-3">
                            <h2
                                class="verification-h6 verification-text-uppercase verification-text-muted verification-mb-3">
                                Selfie Holding ID</h2>
                            <div class="verification-col-12 verification-col-md-8">
                                <label for="selfie" class="verification-form-label verification-required">Upload
                                    selfie while holding your ID</label>
                                <input type="file" name="selfie" id="selfie"
                                    class="verification-form-control" accept="image/*" capture="user"
                                    @if ($vr && $vr->status === 'reupload') @if (in_array('selfie', $reuploadFields)) required @else disabled @endif
                                @else required @endif />
                                <div class="verification-form-text">Hold your ID next to your face. Both must be
                                    visible.</div>
                                <div class="verification-preview-box verification-mt-2">
                                    <img id="preview_selfie" alt="Selfie Preview" class="verification-d-none" />
                                    <span class="verification-placeholder-text">No file selected</span>
                                </div>
                            </div>
                        </section>
                    @endif

                    <!-- STEP 4: Personal Details -->
                    @if (!$vr || $vr->status !== 'reupload')
                        <section class="verification-step" id="step-4">
                            <h2
                                class="verification-h6 verification-text-uppercase verification-text-muted verification-mb-3">
                                ID & Personal Details</h2>
                            <div class="verification-row verification-g-3 verification-g-md-4">
                                <!-- ID Number -->
                                <div class="verification-col-12 verification-col-md-6">
                                    <label for="id_number" class="verification-form-label verification-required">ID
                                        Number</label>
                                    <input type="text" name="id_number" id="id_number"
                                        class="verification-form-control"
                                        value="{{ old('id_number', $vr->id_number ?? '') }}"
                                        @if ($vr && $vr->status === 'reupload') readonly @else required @endif />
                                    <div id="id_number_error"
                                        class="verification-invalid-feedback verification-d-block"></div>
                                </div>

                                <!-- Date of Birth -->
                                <div class="verification-col-12 verification-col-md-6">
                                    <label for="dob" class="verification-form-label verification-required">Date
                                        of Birth</label>
                                    <input type="date" name="dob" id="dob"
                                        class="verification-form-control" value="{{ old('dob', $vr->dob ?? '') }}"
                                        @if ($vr && $vr->status === 'reupload') readonly @else required @endif />
                                </div>

                                <!-- First / Middle / Last Name -->
                                <div class="verification-col-12 verification-col-md-4">
                                    <label for="first_name"
                                        class="verification-form-label verification-required">First Name</label>
                                    <input type="text" name="first_name" id="first_name"
                                        class="verification-form-control"
                                        value="{{ old('first_name', $vr->first_name ?? '') }}"
                                        @if ($vr && $vr->status === 'reupload') readonly @else required @endif />
                                </div>
                                <div class="verification-col-12 verification-col-md-4">
                                    <label for="middle_name" class="verification-form-label">Middle Name</label>
                                    <input type="text" name="middle_name" id="middle_name"
                                        class="verification-form-control"
                                        value="{{ old('middle_name', $vr->middle_name ?? '') }}"
                                        @if ($vr && $vr->status === 'reupload') readonly @endif />
                                </div>
                                <div class="verification-col-12 verification-col-md-4">
                                    <label for="last_name" class="verification-form-label verification-required">Last
                                        Name</label>
                                    <input type="text" name="last_name" id="last_name"
                                        class="verification-form-control"
                                        value="{{ old('last_name', $vr->last_name ?? '') }}"
                                        @if ($vr && $vr->status === 'reupload') readonly @else required @endif />
                                </div>

                                <!-- Sex -->
                                <div class="verification-col-12 verification-col-md-6">
                                    <label for="sex"
                                        class="verification-form-label verification-required">Sex</label>
                                    <select name="sex" id="sex" class="verification-form-select"
                                        @if ($vr && $vr->status === 'reupload') disabled @else required @endif>
                                        <option value="">-- Select --</option>
                                        <option value="M" @if (old('sex', $vr->sex ?? '') === 'M') selected @endif>Male
                                        </option>
                                        <option value="F" @if (old('sex', $vr->sex ?? '') === 'F') selected @endif>
                                            Female</option>
                                    </select>
                                </div>
                            </div>
                        </section>
                    @endif

                    <!-- Actions -->
                    <div
                        class="verification-mt-4 verification-d-flex verification-justify-content-between verification-align-items-center verification-actions-wrap">
                        <button type="button" class="verification-btn verification-btn-outline-secondary"
                            id="btnPrev">Back</button>
                        <div class="verification-d-flex verification-gap-2 verification-flex-wrap">
                            <button type="button" class="verification-btn verification-btn-primary verification-px-4"
                                id="btnNext">Next</button>
                            <button type="submit" class="verification-btn verification-btn-primary verification-px-4"
                                id="btnSubmit" style="display:none;">Submit Verification</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('partials.main-footer')

    <script>
        // ====== Back (previous page) button ======
        (function() {
            const backBtn = document.getElementById('pageBackBtn');
            if (!backBtn) return;
            backBtn.addEventListener('click', function() {
                if (history.length > 1) {
                    history.back();
                } else {
                    window.location.href = "{{ url()->previous() ?: url('/') }}";
                }
            }, {
                passive: true
            });
        })();

        // ====== Wizard Logic ======
        const reuploadMode = @json($vr && $vr->status === 'reupload');
        const reuploadFields = @json($reuploadFields);
        let step = 1;

        // Build step list dynamically
        let activeSteps = [];
        if (!reuploadMode || reuploadFields.includes("id_front") || reuploadFields.includes("id_back"))
            activeSteps.push({
                id: 1,
                label: "Add ID"
            });
        if (!reuploadMode || reuploadFields.includes("face_photo"))
            activeSteps.push({
                id: 2,
                label: "Facial Photo"
            });
        if (!reuploadMode || reuploadFields.includes("selfie"))
            activeSteps.push({
                id: 3,
                label: "Selfie w/ ID"
            });
        if (!reuploadMode)
            activeSteps.push({
                id: 4,
                label: "Details"
            });

        const maxStep = activeSteps.length;

        // Build stepper UI
        const stepperEl = document.querySelector(".verification-stepper");
        stepperEl.innerHTML = "";
        activeSteps.forEach((s, i) => {
            const stepDiv = document.createElement("div");
            stepDiv.className = "verification-step-item";
            stepDiv.dataset.step = s.id;
            stepDiv.innerHTML = `<div class="verification-step-dot">${i+1}</div><span>${s.label}</span>`;
            stepperEl.appendChild(stepDiv);

            if (i < activeSteps.length - 1) {
                const bar = document.createElement("div");
                bar.className = "verification-step-bar";
                stepperEl.appendChild(bar);
            }
        });
        const stepDots = stepperEl.querySelectorAll(".verification-step-item");

        // Panes
        const panes = {
            1: document.getElementById('step-1'),
            2: document.getElementById('step-2'),
            3: document.getElementById('step-3'),
            4: document.getElementById('step-4')
        };

        const btnPrev = document.getElementById('btnPrev');
        const btnNext = document.getElementById('btnNext');
        const btnSubmit = document.getElementById('btnSubmit');
        const hiddenStep = document.getElementById('_step');

        function setStep(n) {
            step = Math.max(1, Math.min(maxStep, n));
            hiddenStep.value = String(activeSteps[step - 1].id);

            Object.entries(panes).forEach(([id, el]) => {
                if (el) el.classList.toggle('verification-show', Number(id) === activeSteps[step - 1].id);
            });

            stepDots.forEach((dot, i) => {
                dot.classList.toggle('verification-active', i + 1 === step);
                dot.classList.toggle('verification-done', i + 1 < step);
            });

            btnPrev.style.display = (step === 1 ? 'none' : '');
            btnNext.style.display = step < maxStep ? '' : 'none';
            btnSubmit.style.display = step === maxStep ? '' : 'none';

            if (reuploadMode && maxStep === 1) {
                btnPrev.style.display = 'none';
                btnNext.style.display = 'none';
                btnSubmit.style.display = '';
            }
        }

        btnPrev.addEventListener('click', () => setStep(step - 1));
        btnNext.addEventListener('click', () => {
            if (validateStep(activeSteps[step - 1].id)) setStep(step + 1);
        });

        // Validation helpers
        function showError(input, message) {
            if (!input) return;
            let feedback = input.parentElement.querySelector('.verification-invalid-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.classList.add('verification-invalid-feedback', 'verification-d-block');
                input.parentElement.appendChild(feedback);
            }
            feedback.textContent = message;
            input.classList.add('verification-is-invalid');
        }

        function clearError(input) {
            if (!input) return;
            input.classList.remove('verification-is-invalid');
            const feedback = input.parentElement.querySelector('.verification-invalid-feedback');
            if (feedback) feedback.textContent = '';
        }

        function validateRequired(input, label) {
            if (!input || input.disabled || input.readOnly) return true;
            if (input.type === "file") {
                if (!input.files || input.files.length === 0) {
                    showError(input, label + " is required");
                    return false;
                }
            } else if (!input.value.trim()) {
                showError(input, label + " is required");
                return false;
            }
            clearError(input);
            return true;
        }

        function validateIdNumber() {
            const idType = document.getElementById('id_type');
            const idNumber = document.getElementById('id_number');
            const raw = idNumber?.value.trim();
            let regex, message = '';
            if (idType?.value === 'philid') {
                regex = /^(\d{4}-?\d{4}-?\d{4}|\d{4}-?\d{4}-?\d{4}-?\d{4})$/;
                message = 'PhilSys ID must be 12 or 16 digits.';
            } else if (idType?.value === 'drivers_license') {
                regex = /^[A-Za-z]\d{2}-?\d{2}-?\d{6}$/;
                message = "Driver’s License must look like E12-23-000386 or E1223000386.";
            }
            if (!regex || raw === '') {
                clearError(idNumber);
                return true;
            }
            if (!regex.test(raw)) {
                showError(idNumber, message);
                return false;
            }
            clearError(idNumber);
            return true;
        }

        function validateStep(stepId) {
            const idFront = document.getElementById('id_front');
            const idBack = document.getElementById('id_back');
            const facePhoto = document.getElementById('face_photo');
            const selfie = document.getElementById('selfie');
            const idNumber = document.getElementById('id_number');
            const firstName = document.getElementById('first_name');
            const lastName = document.getElementById('last_name');
            const dob = document.getElementById('dob');
            const sex = document.getElementById('sex');
            const idType = document.getElementById('id_type');

            if (stepId === 1) {
                if (!validateRequired(idType, "ID Type")) return false;
                if (reuploadMode) {
                    if (reuploadFields.includes("id_front") && !validateRequired(idFront, "Front ID")) return false;
                    if (reuploadFields.includes("id_back") && !validateRequired(idBack, "Back ID")) return false;
                } else {
                    if (!validateRequired(idFront, "Front ID")) return false;
                    if (idType?.value === 'drivers_license' && !validateRequired(idBack, "Back ID")) return false;
                }
                return true;
            }
            if (stepId === 2) return validateRequired(facePhoto, "Facial Photo");
            if (stepId === 3) return validateRequired(selfie, "Selfie with ID");
            if (stepId === 4) {
                return validateIdNumber() &&
                    validateRequired(idNumber, "ID Number") &&
                    validateRequired(firstName, "First Name") &&
                    validateRequired(lastName, "Last Name") &&
                    validateRequired(dob, "Date of Birth") &&
                    validateRequired(sex, "Sex");
            }
            return true;
        }

        // File preview
        function showPreview(input, imgEl) {
            const box = imgEl.parentElement;
            const placeholder = box.querySelector('.verification-placeholder-text');
            if (input.files && input.files[0]) {
                imgEl.src = URL.createObjectURL(input.files[0]);
                imgEl.classList.remove('verification-d-none');
                if (placeholder) placeholder.style.display = 'none';
            } else {
                imgEl.src = "";
                imgEl.classList.add('verification-d-none');
                if (placeholder) placeholder.style.display = 'block';
            }
        }
        const idFront = document.getElementById('id_front');
        const idBack = document.getElementById('id_back');
        const facePhoto = document.getElementById('face_photo');
        const selfie = document.getElementById('selfie');
        idFront?.addEventListener('change', () => showPreview(idFront, document.getElementById('preview_front')));
        idBack?.addEventListener('change', () => showPreview(idBack, document.getElementById('preview_back')));
        facePhoto?.addEventListener('change', () => showPreview(facePhoto, document.getElementById('preview_face')));
        selfie?.addEventListener('change', () => showPreview(selfie, document.getElementById('preview_selfie')));

        // Prevent invalid submit
        document.getElementById('kycForm').addEventListener('submit', function(e) {
            if (!validateStep(activeSteps[maxStep - 1].id)) {
                e.preventDefault();
                const firstInvalid = document.querySelector('.verification-is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }
        });

        // Init wizard
        setStep(1);

        // Toggle ID Back + Expiry fields based on ID type
        function toggleIdFields() {
            const idType = document.getElementById('id_type').value;
            const backWrapper = document.getElementById('idBackWrapper');
            const expiryWrapper = document.getElementById('idExpiryWrapper');
            if (idType === 'drivers_license') {
                backWrapper.style.display = '';
                expiryWrapper.style.display = '';
            } else {
                backWrapper.style.display = 'none';
                expiryWrapper.style.display = 'none';
            }
        }
        toggleIdFields();
        document.getElementById('id_type')?.addEventListener('change', toggleIdFields);

        // ... your existing toggleIdFields function ...

        // Auto-format ID number as user types
        function autoFormatIdNumber() {
            const idType = document.getElementById('id_type')?.value;
            const idNumberInput = document.getElementById('id_number');
            if (!idNumberInput || !idType) return;

            let value = idNumberInput.value.replace(/[^A-Za-z0-9]/g, '');

            if (idType === 'philid') {
                // Format as 1234-5678-9012 or 1234-5678-9012-3456
                if (value.length > 4) value = value.substring(0, 4) + '-' + value.substring(4);
                if (value.length > 9) value = value.substring(0, 9) + '-' + value.substring(9);
                if (value.length > 14) value = value.substring(0, 14) + '-' + value.substring(14);
                // Remove extra dashes if user deletes digits
                value = value.replace(/-+$/g, '');
            } else if (idType === 'drivers_license') {
                // Format as E12-23-000386
                if (value.length > 0) {
                    const firstChar = value.charAt(0).toUpperCase();
                    const digits = value.substring(1).replace(/\D/g, '');
                    let formatted = firstChar;

                    if (digits.length > 2) formatted += digits.substring(0, 2) + '-' + digits.substring(2);
                    else formatted += digits;

                    if (digits.length > 4) formatted = formatted.substring(0, 5) + '-' + formatted.substring(5);
                    if (digits.length > 10) formatted = formatted.substring(0, 10);

                    value = formatted;
                }
            }

            idNumberInput.value = value;
        }

        // Add event listeners
        const idNumberInput = document.getElementById('id_number');
        const idTypeSelect = document.getElementById('id_type');

        if (idNumberInput) {
            idNumberInput.addEventListener('input', autoFormatIdNumber);
        }

        if (idTypeSelect) {
            idTypeSelect.addEventListener('change', function() {
                // Reformat existing value when type changes
                setTimeout(autoFormatIdNumber, 10);
            });
        }

        // Initial format if there's already a value
        setTimeout(autoFormatIdNumber, 100);
    </script>

</body>

</html>
