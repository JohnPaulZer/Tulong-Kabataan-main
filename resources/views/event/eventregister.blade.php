<style>
    /* ======================================
       THEME VARIABLES
       ====================================== */
    :root {
        --evt-primary: #4f46e5;
        --evt-primary-hover: #4338ca;
        --evt-primary-soft: rgba(79, 70, 229, 0.1);
        --evt-text-main: #1f2937;
        --evt-text-muted: #6b7280;
        --evt-bg-input: #f9fafb;
        --evt-border: #d1d5db;
        --evt-radius: 12px;
        --evt-radius-sm: 8px;
    }

    /* ======================================
       MODAL CONTAINER
       ====================================== */
    .event-modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.65);
        backdrop-filter: blur(4px);
        z-index: 5000;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        box-sizing: border-box;
    }

    .event-modal {
        background: #ffffff;
        width: 100%;
        max-width: 800px;
        height: 90vh;
        max-height: 800px;
        border-radius: var(--evt-radius);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        font-family: 'Poppins', sans-serif;
    }

    /* ======================================
       1. HEADER (FIXED TOP)
       ====================================== */
    .event-modal-header {
        background: var(--evt-primary);
        color: #fff;
        padding: 20px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }

    .event-modal-header h2 {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 80%;
    }

    .event-modal-close {
        background: transparent;
        border: none;
        color: white;
        font-size: 2rem;
        cursor: pointer;
        line-height: 1;
        transition: transform 0.2s;
    }

    .event-modal-close:hover {
        transform: scale(1.1);
    }

    /* ======================================
       2. BODY (SCROLLABLE AREA)
       ====================================== */
    .event-modal-body {
        padding: 25px;
        overflow-y: auto;
        flex-grow: 1;
        scrollbar-width: thin;
        scrollbar-color: var(--evt-border) transparent;
    }

    .event-modal-body::-webkit-scrollbar {
        width: 8px;
    }

    .event-modal-body::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 4px;
    }

    /* ======================================
       EVENT DETAILS SECTION
       ====================================== */
    .event-details-card {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding-bottom: 25px;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 25px;
    }

    .event-img-wrapper {
        flex: 1 1 100%;
        min-height: 200px;
        max-height: 300px;
        border-radius: var(--evt-radius-sm);
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    @media (min-width: 600px) {
        .event-img-wrapper {
            flex: 0 0 280px;
            height: auto;
        }
    }

    .event-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .event-info-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-width: 0;
    }

    .event-info-wrapper h3 {
        margin: 0 0 10px 0;
        color: var(--evt-primary);
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1.2;
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    .event-meta {
        display: flex;
        flex-direction: column;
        gap: 8px;
        color: var(--evt-text-muted);
        font-size: 0.95rem;
        margin-bottom: 15px;
    }

    .event-meta span {
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .event-meta i {
        color: var(--evt-primary);
        margin-top: 3px;
    }

    .event-desc {
        font-size: 0.95rem;
        line-height: 1.6;
        color: var(--evt-text-main);
        margin: 0;
        overflow-wrap: break-word;
    }

    /* ======================================
       FORM STYLES
       ====================================== */
    .form-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--evt-text-main);
        margin-bottom: 15px;
        display: block;
        border-left: 4px solid var(--evt-primary);
        padding-left: 10px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 15px;
        margin-bottom: 25px;
    }

    @media (min-width: 600px) {
        .form-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .form-group {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .form-group label {
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 6px;
        color: var(--evt-text-main);
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--evt-border);
        border-radius: var(--evt-radius-sm);
        font-size: 0.95rem;
        background-color: var(--evt-bg-input);
        color: var(--evt-text-main);
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--evt-primary);
        box-shadow: 0 0 0 3px var(--evt-primary-soft);
        background-color: #fff;
    }

    .form-control[readonly] {
        background-color: #f3f4f6;
        cursor: not-allowed;
        color: black;
    }

    /* ======================================
       REMINDER TOGGLE SECTION
       ====================================== */
    .reminder-box {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: var(--evt-radius-sm);
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-top: 20px;
    }

    @media (min-width: 500px) {
        .reminder-box {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
    }

    .toggle-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
        flex-shrink: 0;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    input:checked+.slider {
        background-color: var(--evt-primary);
    }

    input:checked+.slider:before {
        transform: translateX(22px);
    }

    /* ======================================
       3. FOOTER (ACTIONS)
       ====================================== */
    .event-modal-footer {
        padding: 15px 25px;
        border-top: 1px solid #e5e7eb;
        background: #fff;
        display: flex;
        flex-direction: column-reverse;
        gap: 10px;
        flex-shrink: 0;
        z-index: 10;
    }

    @media (min-width: 400px) {
        .event-modal-footer {
            flex-direction: row;
            justify-content: flex-end;
        }
    }

    .btn {
        padding: 12px 24px;
        border-radius: var(--evt-radius-sm);
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        font-family: inherit;
        text-align: center;
    }

    .btn-cancel {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .btn-cancel:hover {
        background-color: #e5e7eb;
        color: #1f2937;
    }

    .btn-submit {
        background-color: var(--evt-primary);
        color: white;
        box-shadow: 0 4px 6px var(--evt-primary-soft);
    }

    .btn-submit:hover {
        background-color: var(--evt-primary-hover);
        transform: translateY(-1px);
    }
</style>

<div class="event-modal-backdrop">

    <div class="event-modal">

        <div class="event-modal-header">
            <h2>Event Registration</h2>
            <button class="event-modal-close">&times;</button>
        </div>

        <form action="/submit-registration" method="POST"
            style="display: flex; flex-direction: column; height: 100%; overflow: hidden;">
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->event_id }}">

            <div class="event-modal-body">

                {{-- Event Details Card --}}
                <div class="event-details-card">
                    <div class="event-img-wrapper">
                        <img src="{{ asset('storage/' . $event->photo) }}" alt="{{ $event->title }}">
                    </div>
                    <div class="event-info-wrapper">
                        <h3>{{ $event->title }}</h3>
                        <div class="event-meta">
                            <span>
                                <i class="ri-calendar-line"></i>
                                <div>
                                    {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y • h:i A') }}
                                    - {{ \Carbon\Carbon::parse($event->end_date)->format('h:i A') }}
                                </div>
                            </span>
                            <span>
                                <i class="ri-map-pin-line"></i> {{ $event->location }}
                            </span>
                        </div>
                        <p class="event-desc">{{ Str::limit($event->description, 150) }}</p>
                    </div>
                </div>

                {{-- Personal Information --}}
                <span class="form-section-title">Your Information</span>
                <div class="form-grid">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" class="form-control" name="first_name"
                            value="{{ auth()->user()->first_name ?? '' }}" readonly>
                    </div>
                    <div class="form-group">
                        @php
                            $lastNameValue = auth()->user()->last_name ?? '';
                            $lastNameReadonly = !empty($lastNameValue);
                        @endphp
                        <label>Last Name @if (!$lastNameReadonly)
                                <span style="color:red">*</span>
                            @endif
                        </label>
                        <input type="text" class="form-control" name="last_name" value="{{ $lastNameValue }}"
                            @if ($lastNameReadonly) readonly
                @else
                    required
                    placeholder="Enter your last name" @endif>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email"
                            value="{{ auth()->user()->email ?? '' }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        @php
                            $phoneValue = auth()->user()->phone_number ?? '';
                            $isReadonly = !empty($phoneValue);
                        @endphp
                        <input type="tel" class="form-control" name="phone" value="{{ $phoneValue }}"
                            {{ $isReadonly ? 'readonly' : '' }} {{ !$isReadonly ? 'required' : '' }}
                            placeholder="{{ $isReadonly ? '' : 'Enter your phone number' }}">
                    </div>

                    @php
                        $userBirthdate = auth()->user()->birthday ?? null;
                        $calculatedAge = null;
                        if ($userBirthdate) {
                            try {
                                $calculatedAge = \Carbon\Carbon::parse($userBirthdate)->age;
                            } catch (\Exception $e) {
                                $calculatedAge = '';
                            }
                        }
                        $ageReadonly = !empty($calculatedAge);
                    @endphp
                    <div class="form-group">
                        <label>Age @if (!$ageReadonly)
                                <span style="color:red">*</span>
                            @endif
                        </label>
                        <input type="number" class="form-control" name="age" value="{{ $calculatedAge }}"
                            @if ($ageReadonly) readonly
            @else
                required
                placeholder="Enter your age"
                min="1"
                max="120" @endif>
                    </div>

                    <div class="form-group">
                        <label>Messenger Link <span style="color:red">*</span></label>
                        <input type="url" class="form-control" name="messenger_link"
                            placeholder="https://m.me/yourprofile" required>
                    </div>

                    <div class="form-group">
                        <label>Sex <span style="color:red">*</span></label>
                        <select class="form-control" name="sex" required>
                            <option value="">-- Select --</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Address <span style="color:red">*</span></label>
                        <input type="text" class="form-control" name="address" required>
                    </div>
                </div>

                {{-- Role Selection --}}
                <span class="form-section-title">Volunteer Role</span>
                <div class="form-group" style="margin-bottom: 25px;">
                    <label>Choose a role for this event</label>
                    {{-- Ensure the select itself is 100% width --}}
                    <select class="form-control" name="vroles_id" required style="width: 100%; max-width: 100%;">
                        <option value="">-- Select a role --</option>
                        @foreach ($event->volunteerRoles as $role)
                            {{-- Keep the full text in the title for hovering --}}
                            <option value="{{ $role->vroles_id }}" title="{{ $role->name }} - {{ $role->description }}">
                                {{-- REDUCED LIMIT: Changed 65 to 45 to force the menu width to be smaller --}}
                                {{ Str::limit($role->name . ' - ' . $role->description, 45) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Reminder Section --}}
                <div class="reminder-box">
                    <label class="toggle-wrapper">
                        <div class="switch">
                            <input type="checkbox" id="remind_me" name="remind_me" value="1">
                            <span class="slider"></span>
                        </div>
                        <span style="font-weight: 500; color: #4b5563;">Remind me before event</span>
                    </label>

                    <select id="reminder_minutes" name="reminder_minutes" class="form-control" style="width: auto;">
                        <option value="">-- Select Time --</option>
                        <option value="1">1 minutes before</option>
                        <option value="15">15 minutes before</option>
                        <option value="30">30 minutes before</option>
                        <option value="60">1 hour before</option>
                        <option value="1440">24 hours before</option>
                    </select>
                </div>
            </div>

            <div class="event-modal-footer">
                <button type="button" class="btn btn-cancel">Cancel</button>
                <button type="submit" class="btn btn-submit">Confirm Registration</button>
            </div>
        </form>
    </div>
</div>
