@php
    $modalInfrastructure = old('infrastructure', []);
    $modalNeeds = old('needs', []);
    $modalInfrastructure = is_array($modalInfrastructure) ? $modalInfrastructure : [];
    $modalNeeds = is_array($modalNeeds) ? $modalNeeds : [];
@endphp

<style>
    body.admin-dnc-page .dnc-create-modal {
        position: fixed;
        inset: 0;
        z-index: 5000;
        display: grid;
        place-items: center;
        padding: 24px;
    }

    body.admin-dnc-page .dnc-create-modal[hidden] {
        display: none !important;
    }

    body.admin-dnc-page .dnc-create-modal__backdrop {
        position: absolute;
        inset: 0;
        border: 0;
        background: rgba(15, 23, 42, 0.56);
    }

    body.admin-dnc-page .dnc-create-modal__dialog {
        position: relative;
        z-index: 1;
        width: min(1040px, 100%);
        max-height: 92vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #d9e2ef;
        border-radius: 8px;
        background: #fff;
    }

    body.admin-dnc-page .dnc-create-modal__header {
        min-height: 76px;
        padding: 18px 20px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        border-bottom: 1px solid #d9e2ef;
    }

    body.admin-dnc-page .dnc-create-modal__eyebrow {
        margin: 0 0 4px;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    body.admin-dnc-page .dnc-create-modal__header h2 {
        margin: 0;
        color: #0f172a;
        font-size: 20px;
        font-weight: 750;
        line-height: 1.25;
    }

    body.admin-dnc-page .dnc-create-modal__header p {
        margin: 4px 0 0;
        color: #64748b;
        font-size: 14px;
    }

    body.admin-dnc-page .dnc-create-modal__close {
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        border: 1px solid #c7d3e3;
        border-radius: 8px;
        background: #fff;
        color: #334155;
        cursor: pointer;
    }

    body.admin-dnc-page .dnc-stepper-form {
        min-height: 0;
        display: flex;
        flex: 1 1 auto;
        flex-direction: column;
    }

    body.admin-dnc-page .dnc-create-errors {
        margin: 16px 20px 0;
        padding: 12px 14px;
        border: 1px solid #fecaca;
        border-left: 3px solid #dc2626;
        border-radius: 8px;
        background: #fff1f2;
        color: #991b1b;
        font-size: 13px;
    }

    body.admin-dnc-page .dnc-create-errors ul {
        margin: 8px 0 0;
        padding-left: 18px;
    }

    body.admin-dnc-page .dnc-stepper {
        padding: 18px 28px 16px;
        display: flex;
        align-items: flex-start;
        gap: 0;
        border-bottom: 1px solid #d9e2ef;
        background: #f8fafc;
    }

    body.admin-dnc-page .dnc-stepper__item {
        position: relative;
        min-width: 0;
        min-height: 58px;
        padding: 0;
        display: flex;
        flex: 1 1 0;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        gap: 7px;
        border: 0;
        background: transparent;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    body.admin-dnc-page .dnc-stepper__item:not(:last-child)::after,
    body.admin-dnc-page .dnc-stepper__item:not(:last-child)::before {
        content: "";
        position: absolute;
        top: 15px;
        left: calc(50% + 23px);
        right: calc(-50% + 23px);
        height: 2px;
        border-radius: 999px;
    }

    body.admin-dnc-page .dnc-stepper__item:not(:last-child)::after {
        background: #d9e2ef;
    }

    body.admin-dnc-page .dnc-stepper__item:not(:last-child)::before {
        z-index: 1;
        background: #135de8;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 240ms ease;
    }

    body.admin-dnc-page .dnc-stepper__item.is-complete:not(:last-child)::before {
        transform: scaleX(1);
    }

    body.admin-dnc-page .dnc-stepper__circle {
        position: relative;
        z-index: 2;
        width: 32px;
        height: 32px;
        display: grid;
        place-items: center;
        box-sizing: border-box;
        border: 1px solid #d9e2ef;
        border-radius: 999px;
        background: #fff;
        color: #64748b;
        font-size: 12px;
        line-height: 1;
        transition: background 180ms ease, border-color 180ms ease, color 180ms ease, transform 180ms ease;
    }

    body.admin-dnc-page .dnc-stepper__label {
        max-width: 100%;
        color: #475569;
        line-height: 1.2;
        text-align: center;
        white-space: nowrap;
    }

    body.admin-dnc-page .dnc-stepper__item.is-active .dnc-stepper__circle,
    body.admin-dnc-page .dnc-stepper__item.is-complete .dnc-stepper__circle {
        border-color: #135de8;
        background: #135de8;
        color: transparent;
    }

    body.admin-dnc-page .dnc-stepper__item.is-active .dnc-stepper__circle {
        transform: scale(1.04);
    }

    body.admin-dnc-page .dnc-stepper__item.is-active .dnc-stepper__circle::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: #fff;
        transform: translate(-50%, -50%);
    }

    body.admin-dnc-page .dnc-stepper__item.is-complete .dnc-stepper__circle::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 10px;
        height: 6px;
        border-left: 2px solid #fff;
        border-bottom: 2px solid #fff;
        transform: translate(-50%, -58%) rotate(-45deg);
        transform-origin: center;
    }

    body.admin-dnc-page .dnc-stepper__item.is-active .dnc-stepper__label,
    body.admin-dnc-page .dnc-stepper__item.is-complete .dnc-stepper__label {
        color: #135de8;
    }

    body.admin-dnc-page .dnc-stepper__body {
        min-height: 0;
        padding: 20px;
        overflow: auto;
    }

    body.admin-dnc-page .dnc-step[hidden] {
        display: none !important;
    }

    body.admin-dnc-page .dnc-step.is-active {
        animation: dncStepForward 260ms ease both;
    }

    body.admin-dnc-page .dnc-stepper-form[data-direction="backward"] .dnc-step.is-active {
        animation-name: dncStepBackward;
    }

    body.admin-dnc-page .dnc-stepper-form[data-motion="none"] .dnc-step.is-active {
        animation: none;
    }

    @keyframes dncStepForward {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes dncStepBackward {
        from {
            opacity: 0;
            transform: translateX(20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    body.admin-dnc-page .dnc-step__heading {
        margin-bottom: 16px;
    }

    body.admin-dnc-page .dnc-step__heading h3 {
        margin: 0;
        color: #0f172a;
        font-size: 17px;
        font-weight: 750;
    }

    body.admin-dnc-page .dnc-step__heading p {
        margin: 4px 0 0;
        color: #64748b;
        font-size: 14px;
    }

    body.admin-dnc-page .dnc-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    body.admin-dnc-page .dnc-form-grid--three {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    body.admin-dnc-page .dnc-field label,
    body.admin-dnc-page .dnc-check-group__title {
        display: block;
        margin: 0 0 7px;
        color: #334155;
        font-size: 13px;
        font-weight: 700;
    }

    body.admin-dnc-page .dnc-field label span {
        color: #dc2626;
    }

    body.admin-dnc-page .dnc-field input,
    body.admin-dnc-page .dnc-field textarea,
    body.admin-dnc-page .dnc-field select {
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;
        border: 1px solid #c7d3e3;
        border-radius: 8px;
        background: #fff;
        color: #0f172a;
        font-size: 14px;
    }

    body.admin-dnc-page .dnc-field textarea {
        min-height: 92px;
        resize: vertical;
    }

    body.admin-dnc-page .dnc-field input:focus,
    body.admin-dnc-page .dnc-field textarea:focus,
    body.admin-dnc-page .dnc-field select:focus {
        outline: none;
        border-color: #135de8;
        box-shadow: 0 0 0 3px rgba(19, 93, 232, 0.12);
    }

    body.admin-dnc-page .dnc-divider {
        height: 1px;
        margin: 18px 0;
        background: #d9e2ef;
    }

    body.admin-dnc-page .dnc-check-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    body.admin-dnc-page .dnc-check-grid label {
        min-height: 40px;
        padding: 9px 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #d9e2ef;
        border-radius: 8px;
        background: #f8fafc;
        color: #334155;
        font-size: 13px;
    }

    body.admin-dnc-page .dnc-stepper__footer {
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border-top: 1px solid #d9e2ef;
        background: #fff;
    }

    body.admin-dnc-page .dnc-stepper__footer > div {
        display: flex;
        gap: 8px;
    }

    body.admin-dnc-page .dnc-stepper__primary,
    body.admin-dnc-page .dnc-stepper__secondary {
        min-height: 38px;
        padding: 0 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    body.admin-dnc-page .dnc-stepper__primary {
        border: 1px solid #135de8;
        background: #135de8;
        color: #fff;
    }

    body.admin-dnc-page .dnc-stepper__secondary {
        border: 1px solid #c7d3e3;
        background: #fff;
        color: #334155;
    }

    @media (max-width: 760px) {
        body.admin-dnc-page .dnc-create-modal {
            padding: 12px;
        }

        body.admin-dnc-page .dnc-stepper {
            padding: 14px 16px 12px;
            overflow-x: auto;
        }

        body.admin-dnc-page .dnc-stepper__item {
            min-width: 76px;
        }

        body.admin-dnc-page .dnc-stepper__label {
            font-size: 11px;
        }

        body.admin-dnc-page .dnc-form-grid,
        body.admin-dnc-page .dnc-form-grid--three,
        body.admin-dnc-page .dnc-check-grid {
            grid-template-columns: 1fr;
        }

        body.admin-dnc-page .dnc-stepper__footer {
            align-items: stretch;
            flex-direction: column;
        }

        body.admin-dnc-page .dnc-stepper__footer > div {
            flex-direction: column;
        }

        body.admin-dnc-page .dnc-stepper__primary,
        body.admin-dnc-page .dnc-stepper__secondary {
            width: 100%;
        }
    }
</style>

<div id="createDncModal" class="dnc-create-modal" aria-hidden="true" hidden>
    <button type="button" class="dnc-create-modal__backdrop" data-create-dnc-close
        aria-label="Close add DNC record modal"></button>

    <section class="dnc-create-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="createDncModalTitle">
        <header class="dnc-create-modal__header">
            <div>
                <p class="dnc-create-modal__eyebrow">DNC Assessment</p>
                <h2 id="createDncModalTitle">Add DNC Record</h2>
                <p>Complete the assessment one section at a time.</p>
            </div>
            <button type="button" class="dnc-create-modal__close" data-create-dnc-close
                aria-label="Close add DNC record modal">
                <i class="ri-close-line"></i>
            </button>
        </header>

        <form id="createDncStepperForm" action="{{ route('dnc.submit') }}" method="POST" class="dnc-stepper-form">
            @csrf

            @if ($errors->any() || session('error'))
                <div class="dnc-create-errors">
                    <strong>Please review the record details.</strong>
                    @if (session('error'))
                        <p>{{ session('error') }}</p>
                    @endif
                    @if ($errors->any())
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            <nav class="dnc-stepper" aria-label="Add DNC record steps">
                <button type="button" class="dnc-stepper__item is-active" data-dnc-step-trigger="0"
                    aria-current="step">
                    <span class="dnc-stepper__circle">1</span>
                    <span class="dnc-stepper__label">General</span>
                </button>
                <button type="button" class="dnc-stepper__item" data-dnc-step-trigger="1">
                    <span class="dnc-stepper__circle">2</span>
                    <span class="dnc-stepper__label">Damage</span>
                </button>
                <button type="button" class="dnc-stepper__item" data-dnc-step-trigger="2">
                    <span class="dnc-stepper__circle">3</span>
                    <span class="dnc-stepper__label">Needs</span>
                </button>
                <button type="button" class="dnc-stepper__item" data-dnc-step-trigger="3">
                    <span class="dnc-stepper__circle">4</span>
                    <span class="dnc-stepper__label">Capacities</span>
                </button>
                <button type="button" class="dnc-stepper__item" data-dnc-step-trigger="4">
                    <span class="dnc-stepper__circle">5</span>
                    <span class="dnc-stepper__label">Priority</span>
                </button>
            </nav>

            <div class="dnc-stepper__body">
                <section class="dnc-step is-active" data-dnc-step="0">
                    <div class="dnc-step__heading">
                        <h3>General Information</h3>
                        <p>Identify the assessment, location, and affected population.</p>
                    </div>

                    <div class="dnc-form-grid">
                        <div class="dnc-field">
                            <label for="dnc_date">Date of Assessment <span>*</span></label>
                            <input type="date" id="dnc_date" name="date" required
                                value="{{ old('date', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_assessor">Assessor / Team</label>
                            <input type="text" id="dnc_assessor" name="assessor" value="{{ old('assessor') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_event">Disaster / Event <span>*</span></label>
                            <input type="text" id="dnc_event" name="event" required
                                value="{{ old('event') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_province">Province <span>*</span></label>
                            <input type="text" id="dnc_province" name="province" required
                                value="{{ old('province') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_municipality">Municipality <span>*</span></label>
                            <input type="text" id="dnc_municipality" name="municipality" required
                                value="{{ old('municipality') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_barangay">Barangay / Sitio / Evacuation Center <span>*</span></label>
                            <input type="text" id="dnc_barangay" name="barangay" required
                                value="{{ old('barangay') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_households">Total Households Affected</label>
                            <input type="number" min="0" id="dnc_households" name="households"
                                value="{{ old('households') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_individuals">Total Individuals</label>
                            <input type="number" min="0" id="dnc_individuals" name="individuals"
                                value="{{ old('individuals') }}">
                        </div>
                    </div>

                    <div class="dnc-divider"></div>
                    <div class="dnc-check-group__title">Population Breakdown</div>
                    <div class="dnc-form-grid dnc-form-grid--three">
                        <div class="dnc-field">
                            <label for="dnc_pop_male">Male</label>
                            <input type="number" min="0" id="dnc_pop_male" name="pop_male"
                                value="{{ old('pop_male') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_pop_female">Female</label>
                            <input type="number" min="0" id="dnc_pop_female" name="pop_female"
                                value="{{ old('pop_female') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_pop_children">Children</label>
                            <input type="number" min="0" id="dnc_pop_children" name="pop_children"
                                value="{{ old('pop_children') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_pop_elderly">Elderly</label>
                            <input type="number" min="0" id="dnc_pop_elderly" name="pop_elderly"
                                value="{{ old('pop_elderly') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_pop_pwds">PWDs</label>
                            <input type="number" min="0" id="dnc_pop_pwds" name="pop_pwds"
                                value="{{ old('pop_pwds') }}">
                        </div>
                    </div>
                </section>

                <section class="dnc-step" data-dnc-step="1" hidden>
                    <div class="dnc-step__heading">
                        <h3>Damage Assessment</h3>
                        <p>Record damaged houses, infrastructure, livelihood, and facilities.</p>
                    </div>

                    <div class="dnc-form-grid">
                        <div class="dnc-field">
                            <label for="dnc_houses_full">Houses Fully Damaged</label>
                            <input type="number" min="0" id="dnc_houses_full" name="houses_full"
                                value="{{ old('houses_full') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_houses_partial">Houses Partially Damaged</label>
                            <input type="number" min="0" id="dnc_houses_partial" name="houses_partial"
                                value="{{ old('houses_partial') }}">
                        </div>
                    </div>

                    <div class="dnc-divider"></div>
                    <div class="dnc-check-group">
                        <div class="dnc-check-group__title">Infrastructure Affected</div>
                        <div class="dnc-check-grid">
                            @foreach (['Roads', 'Bridges', 'Schools', 'Health Centers', 'Water/Electric Lines'] as $item)
                                <label>
                                    <input type="checkbox" name="infrastructure[]" value="{{ $item }}"
                                        {{ in_array($item, $modalInfrastructure) ? 'checked' : '' }}>
                                    {{ $item }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="dnc-divider"></div>
                    <div class="dnc-form-grid dnc-form-grid--three">
                        <div class="dnc-field">
                            <label for="dnc_crop_type">Crop Losses Type</label>
                            <input type="text" id="dnc_crop_type" name="crop_type"
                                value="{{ old('crop_type') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_crop_hectares">Crop Losses Hectares</label>
                            <input type="number" step="0.01" min="0" id="dnc_crop_hectares"
                                name="crop_hectares" value="{{ old('crop_hectares') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_livestock_type">Livestock Lost Type</label>
                            <input type="text" id="dnc_livestock_type" name="livestock_type"
                                value="{{ old('livestock_type') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_livestock_number">Livestock Lost Number</label>
                            <input type="number" min="0" id="dnc_livestock_number" name="livestock_number"
                                value="{{ old('livestock_number') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_tools_destroyed">Tools / Equipment Destroyed</label>
                            <input type="text" id="dnc_tools_destroyed" name="tools_destroyed"
                                value="{{ old('tools_destroyed') }}">
                        </div>
                    </div>

                    <div class="dnc-divider"></div>
                    <div class="dnc-form-grid">
                        <div class="dnc-field">
                            <label for="dnc_facilities_affected">Facilities Affected?</label>
                            <select id="dnc_facilities_affected" name="facilities_affected">
                                <option value="">Select...</option>
                                <option value="Yes" {{ old('facilities_affected') === 'Yes' ? 'selected' : '' }}>
                                    Yes</option>
                                <option value="No" {{ old('facilities_affected') === 'No' ? 'selected' : '' }}>
                                    No</option>
                            </select>
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_facilities_notes">Facilities Notes</label>
                            <textarea id="dnc_facilities_notes" name="facilities_notes" rows="2">{{ old('facilities_notes') }}</textarea>
                        </div>
                    </div>
                </section>

                <section class="dnc-step" data-dnc-step="2" hidden>
                    <div class="dnc-step__heading">
                        <h3>Needs</h3>
                        <p>Select immediate needs and add any notes.</p>
                    </div>

                    @foreach ([
                        'Basic Needs' => ['Food', 'Drinking Water', 'Shelter Materials', 'Clothing/Hygiene Kits'],
                        'Health Needs' => ['Medicines', 'Medical Support'],
                        'Protection Needs' => ['Psychosocial Support', 'Child-Friendly Spaces', 'Dignity Kits'],
                        'Livelihood Needs' => ['Seeds & Farming Tools', 'Fishing Equipment', 'Cash-for-Work / Grants'],
                    ] as $group => $items)
                        <div class="dnc-check-group">
                            <div class="dnc-check-group__title">{{ $group }}</div>
                            <div class="dnc-check-grid">
                                @foreach ($items as $item)
                                    <label>
                                        <input type="checkbox" name="needs[]" value="{{ $item }}"
                                            {{ in_array($item, $modalNeeds) ? 'checked' : '' }}>
                                        {{ $item }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="dnc-divider"></div>
                    @endforeach

                    <div class="dnc-field">
                        <label for="dnc_needs_other">Other Needs / Notes</label>
                        <textarea id="dnc_needs_other" name="needs_other" rows="3">{{ old('needs_other') }}</textarea>
                    </div>
                </section>

                <section class="dnc-step" data-dnc-step="3" hidden>
                    <div class="dnc-step__heading">
                        <h3>Capacities</h3>
                        <p>Capture local resources and community-led support.</p>
                    </div>

                    <div class="dnc-form-grid">
                        <div class="dnc-field">
                            <label for="dnc_groups">Community Groups Active</label>
                            <input type="text" id="dnc_groups" name="groups" value="{{ old('groups') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_facilities">Facilities Available</label>
                            <input type="text" id="dnc_facilities" name="facilities"
                                value="{{ old('facilities') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_skills">Skills / Human Resources</label>
                            <input type="text" id="dnc_skills" name="skills" value="{{ old('skills') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_initiatives">Community-led Initiatives</label>
                            <textarea id="dnc_initiatives" name="initiatives" rows="3">{{ old('initiatives') }}</textarea>
                        </div>
                    </div>
                </section>

                <section class="dnc-step" data-dnc-step="4" hidden>
                    <div class="dnc-step__heading">
                        <h3>Prioritization</h3>
                        <p>Set urgency and identify the top needs.</p>
                    </div>

                    <div class="dnc-form-grid">
                        <div class="dnc-field">
                            <label for="dnc_priority">Urgency Level <span>*</span></label>
                            <select id="dnc_priority" name="priority" required>
                                <option value="">Select...</option>
                                <option value="High" {{ old('priority') === 'High' ? 'selected' : '' }}>High
                                </option>
                                <option value="Medium" {{ old('priority') === 'Medium' ? 'selected' : '' }}>
                                    Medium</option>
                                <option value="Low" {{ old('priority') === 'Low' ? 'selected' : '' }}>Low
                                </option>
                            </select>
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_solutions">Suggested Local Solutions</label>
                            <textarea id="dnc_solutions" name="solutions" rows="2">{{ old('solutions') }}</textarea>
                        </div>
                    </div>

                    <div class="dnc-divider"></div>
                    <div class="dnc-form-grid dnc-form-grid--three">
                        <div class="dnc-field">
                            <label for="dnc_top_need_1">Top Need #1 <span>*</span></label>
                            <input type="text" id="dnc_top_need_1" name="top_need_1" required
                                value="{{ old('top_need_1') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_top_need_2">Top Need #2</label>
                            <input type="text" id="dnc_top_need_2" name="top_need_2"
                                value="{{ old('top_need_2') }}">
                        </div>
                        <div class="dnc-field">
                            <label for="dnc_top_need_3">Top Need #3</label>
                            <input type="text" id="dnc_top_need_3" name="top_need_3"
                                value="{{ old('top_need_3') }}">
                        </div>
                    </div>
                </section>
            </div>

            <footer class="dnc-stepper__footer">
                <button type="button" class="dnc-stepper__secondary" id="createDncPrevStep">
                    Previous
                </button>
                <div>
                    <button type="button" class="dnc-stepper__secondary" data-create-dnc-close>
                        Cancel
                    </button>
                    <button type="button" class="dnc-stepper__primary" id="createDncNextStep">
                        Next
                    </button>
                    <button type="submit" class="dnc-stepper__primary" id="createDncSubmit" hidden>
                        Save Record
                    </button>
                </div>
            </footer>
        </form>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('createDncModal');
        const openBtn = document.getElementById('openCreateDncModal');
        const closeBtns = document.querySelectorAll('[data-create-dnc-close]');
        const form = document.getElementById('createDncStepperForm');
        const steps = Array.from(document.querySelectorAll('[data-dnc-step]'));
        const stepButtons = Array.from(document.querySelectorAll('[data-dnc-step-trigger]'));
        const prevBtn = document.getElementById('createDncPrevStep');
        const nextBtn = document.getElementById('createDncNextStep');
        const submitBtn = document.getElementById('createDncSubmit');
        let currentStep = 0;

        function setStep(index, options = {}) {
            const nextStep = Math.max(0, Math.min(index, steps.length - 1));
            const direction = nextStep >= currentStep ? 'forward' : 'backward';
            currentStep = nextStep;

            if (form) {
                form.dataset.direction = direction;
                form.dataset.motion = options.skipAnimation ? 'none' : 'slide';
            }

            steps.forEach((step, stepIndex) => {
                const active = stepIndex === currentStep;
                step.hidden = !active;
                step.classList.toggle('is-active', active);
            });

            stepButtons.forEach((button, buttonIndex) => {
                const active = buttonIndex === currentStep;
                const complete = buttonIndex < currentStep;
                button.classList.toggle('is-active', active);
                button.classList.toggle('is-complete', complete);
                button.setAttribute('aria-current', active ? 'step' : 'false');
            });

            prevBtn.hidden = currentStep === 0;
            nextBtn.hidden = currentStep === steps.length - 1;
            submitBtn.hidden = currentStep !== steps.length - 1;
        }

        function validateStep(index = currentStep) {
            const step = steps[index];
            const fields = Array.from(step.querySelectorAll('input, textarea, select'));

            for (const field of fields) {
                if (!field.checkValidity()) {
                    field.reportValidity();
                    return false;
                }
            }

            return true;
        }

        function openModal() {
            modal.hidden = false;
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            setStep(currentStep, {
                skipAnimation: true
            });
            window.setTimeout(() => document.getElementById('dnc_event')?.focus(), 80);
        }

        function closeModal() {
            modal.hidden = true;
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            openBtn?.focus();
        }

        openBtn?.addEventListener('click', openModal);
        closeBtns.forEach((button) => button.addEventListener('click', closeModal));

        nextBtn?.addEventListener('click', () => {
            if (validateStep()) {
                setStep(currentStep + 1);
            }
        });

        prevBtn?.addEventListener('click', () => setStep(currentStep - 1));

        stepButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const target = Number(button.dataset.dncStepTrigger);
                if (target <= currentStep) {
                    setStep(target);
                    return;
                }

                if (validateStep()) {
                    setStep(Math.min(target, currentStep + 1));
                }
            });
        });

        form?.addEventListener('submit', (event) => {
            for (let index = 0; index < steps.length; index++) {
                setStep(index, {
                    skipAnimation: true
                });
                if (!validateStep(index)) {
                    event.preventDefault();
                    return;
                }
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.hidden) {
                closeModal();
            }
        });

        setStep(0, {
            skipAnimation: true
        });

        @if ($errors->any() || session('error'))
            openModal();
        @endif
    });
</script>
