@php
    $eventImage = !empty($event->photo) ? file_url($event->photo, asset('img/bg2.jpg')) : asset('img/bg2.jpg');
    $eventImageFallback = asset('img/bg2.jpg');
    $startDate = \Carbon\Carbon::parse($event->start_date);
    $endDate = \Carbon\Carbon::parse($event->end_date);
    $deadline = $event->deadline ? \Carbon\Carbon::parse($event->deadline) : null;
    $now = \Carbon\Carbon::now();
    $rolesForForm = isset($roles) ? $roles : $event->volunteerRoles;
    $participantCount = \App\Models\EventRegistration::where('event_id', $event->event_id)
        ->where('status', 'registered')
        ->count();

    if ($deadline && $now->greaterThan($deadline) && $now->lessThan($startDate)) {
        $statusLabel = 'Closed Event';
        $statusClasses = 'bg-slate-100 text-slate-700 ring-slate-200';
    } elseif ($now->between($startDate, $endDate)) {
        $statusLabel = 'Ongoing Event';
        $statusClasses = 'bg-amber-100 text-amber-800 ring-amber-200';
    } elseif ($now->greaterThan($endDate)) {
        $statusLabel = 'Ended Event';
        $statusClasses = 'bg-red-100 text-red-700 ring-red-200';
    } else {
        $statusLabel = 'Upcoming Event';
        $statusClasses = 'bg-lime-100 text-lime-800 ring-lime-200';
    }
@endphp

<div class="event-modal-backdrop fixed inset-0 z-[5000] flex items-center justify-center bg-slate-950/60 p-3 backdrop-blur-sm sm:p-5"
    role="presentation">
    <section
        class="event-modal flex max-h-[92vh] w-full max-w-[760px] flex-col overflow-hidden rounded-xl border border-slate-300 bg-white shadow-[0_24px_70px_rgba(15,23,42,0.28)]"
        role="dialog" aria-modal="true" aria-labelledby="event-registration-title">
        <form action="/submit-registration" method="POST" class="event-registration-form flex min-h-0 flex-1 flex-col overflow-hidden">
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->event_id }}">

            <div class="event-modal-body min-h-0 flex-1 overflow-y-auto bg-white">
                <header class="event-modal-hero relative min-h-[220px] overflow-hidden bg-slate-900 sm:min-h-[260px]">
                    <img class="absolute inset-0 h-full w-full object-cover" src="{{ $eventImage }}"
                        alt="{{ $event->title }}"
                        onerror="this.onerror=null; this.src='{{ $eventImageFallback }}'; this.classList.add('object-contain', 'p-10', 'bg-slate-100');">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/82 via-slate-950/30 to-slate-950/10"></div>

                    <button type="button"
                        class="event-modal-close absolute right-4 top-4 inline-flex h-9 w-9 items-center justify-center border border-slate-300 bg-white/95 text-xl text-slate-700 shadow-sm transition hover:bg-white hover:text-slate-950 focus:outline-none focus:ring-4 focus:ring-white/40"
                        aria-label="Close event registration">
                        <i class="ri-close-line" aria-hidden="true"></i>
                    </button>

                    <div class="absolute bottom-5 left-5 right-5">
                        <span
                            class="inline-flex items-center rounded-sm px-2.5 py-1 text-[11px] font-extrabold uppercase tracking-wide ring-1 {{ $statusClasses }}">
                            {{ $statusLabel }}
                        </span>
                        <h2 id="event-registration-title"
                            class="m-0 mt-3 max-w-[680px] text-2xl font-extrabold leading-tight text-white drop-shadow sm:text-3xl">
                            {{ $event->title }}
                        </h2>
                    </div>
                </header>

                <div class="space-y-5 px-5 py-5 sm:px-7">
                    <section class="event-modal-summary border border-slate-300 bg-white p-4" aria-label="Event information">
                        <div class="grid gap-x-6 gap-y-5 sm:grid-cols-2">
                            <div class="flex gap-3">
                                <span class="mt-0.5 text-indigo-600">
                                    <i class="ri-calendar-event-line" aria-hidden="true"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="m-0 text-[10px] font-extrabold uppercase tracking-wide text-slate-500">
                                        Date &amp; Time
                                    </p>
                                    <p class="m-0 mt-1 text-sm font-semibold leading-5 text-slate-900">
                                        {{ $startDate->format('M d, Y') }} &bull;
                                        {{ $startDate->format('h:i A') }} - {{ $endDate->format('h:i A') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <span class="mt-0.5 text-indigo-600">
                                    <i class="ri-timer-line" aria-hidden="true"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="m-0 text-[10px] font-extrabold uppercase tracking-wide text-slate-500">
                                        Registration Deadline
                                    </p>
                                    <p class="m-0 mt-1 text-sm font-semibold leading-5 text-slate-900">
                                        {{ $deadline ? $deadline->format('M d, Y') : 'No deadline set' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-3 sm:border-t sm:border-slate-200 sm:pt-5">
                                <span class="mt-0.5 text-indigo-600">
                                    <i class="ri-map-pin-line" aria-hidden="true"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="m-0 text-[10px] font-extrabold uppercase tracking-wide text-slate-500">
                                        Location
                                    </p>
                                    <p class="m-0 mt-1 text-sm font-semibold leading-5 text-slate-900">
                                        {{ $event->location }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-3 sm:border-t sm:border-slate-200 sm:pt-5">
                                <span class="mt-0.5 text-indigo-600">
                                    <i class="ri-team-line" aria-hidden="true"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="m-0 text-[10px] font-extrabold uppercase tracking-wide text-slate-500">
                                        Participants
                                    </p>
                                    <p class="m-0 mt-1 text-sm font-semibold leading-5 text-slate-900">
                                        {{ number_format($participantCount) }} registered
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="event-modal-description" aria-label="Event description">
                        <p class="m-0 text-sm leading-6 text-slate-600">
                            {{ Str::limit($event->description, 240) }}
                        </p>
                    </section>

                    <section class="event-modal-venue" aria-label="Event venue">
                        <h3 class="mb-2 text-[11px] font-extrabold uppercase tracking-wide text-slate-700">
                            Event Venue
                        </h3>

                        @if (!is_null($event->lat) && !is_null($event->lng))
                            <div
                                class="overflow-hidden border border-slate-300 bg-white [&_.tk-map-shell]:!h-[132px] [&_.tk-map-shell]:!min-h-[132px] [&_.tk-map-shell]:!rounded-none [&_.tk-map-shell]:!border-0 sm:[&_.tk-map-shell]:!h-[150px] sm:[&_.tk-map-shell]:!min-h-[150px]">
                                <div data-tk-map-static data-lat="{{ $event->lat }}" data-lng="{{ $event->lng }}"
                                    data-title="{{ $event->title }}" data-description="{{ $event->location }}"
                                    data-height="150px"></div>
                            </div>
                        @else
                            <div
                                class="flex min-h-32 items-center gap-3 border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-sm text-slate-500">
                                <i class="ri-map-pin-line text-xl text-indigo-500" aria-hidden="true"></i>
                                <span>Map location is not available for this event yet.</span>
                            </div>
                        @endif
                    </section>

                    <section class="event-modal-fields border border-slate-200 bg-slate-50 p-4" aria-label="Registration information">
                        <h3 class="mb-4 text-[11px] font-extrabold uppercase tracking-wide text-slate-700">
                            Registration Information
                        </h3>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="flex min-w-0 flex-col gap-1.5">
                                <label for="event_first_name" class="text-xs font-semibold text-slate-700">First Name</label>
                                <input id="event_first_name" type="text"
                                    class="form-control min-h-10 border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                                    name="first_name" value="{{ auth()->user()->first_name ?? '' }}" readonly>
                            </div>

                            <div class="flex min-w-0 flex-col gap-1.5">
                                @php
                                    $lastNameValue = auth()->user()->last_name ?? '';
                                    $lastNameReadonly = !empty($lastNameValue);
                                @endphp
                                <label for="event_last_name" class="text-xs font-semibold text-slate-700">
                                    Last Name
                                    @if (!$lastNameReadonly)
                                        <span class="text-red-600">*</span>
                                    @endif
                                </label>
                                <input id="event_last_name" type="text"
                                    class="form-control min-h-10 border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                                    name="last_name" value="{{ $lastNameValue }}"
                                    @if ($lastNameReadonly) readonly
                                    @else
                                        required
                                        placeholder="Enter your last name" @endif>
                            </div>

                            <div class="flex min-w-0 flex-col gap-1.5">
                                <label for="event_email" class="text-xs font-semibold text-slate-700">Email</label>
                                <input id="event_email" type="email"
                                    class="form-control min-h-10 border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                                    name="email" value="{{ auth()->user()->email ?? '' }}" readonly>
                            </div>

                            <div class="flex min-w-0 flex-col gap-1.5">
                                @php
                                    $phoneValue = auth()->user()->phone_number ?? '';
                                    $isReadonly = !empty($phoneValue);
                                @endphp
                                <label for="event_phone" class="text-xs font-semibold text-slate-700">
                                    Phone
                                    @if (!$isReadonly)
                                        <span class="text-red-600">*</span>
                                    @endif
                                </label>
                                <input id="event_phone" type="tel"
                                    class="form-control min-h-10 border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                                    name="phone" value="{{ $phoneValue }}" {{ $isReadonly ? 'readonly' : '' }}
                                    {{ !$isReadonly ? 'required' : '' }}
                                    inputmode="numeric" pattern="09[0-9]{9}" maxlength="11"
                                    placeholder="{{ $isReadonly ? '' : '09XXXXXXXXX' }}">
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
                            <div class="flex min-w-0 flex-col gap-1.5">
                                <label for="event_age" class="text-xs font-semibold text-slate-700">
                                    Age
                                    @if (!$ageReadonly)
                                        <span class="text-red-600">*</span>
                                    @endif
                                </label>
                                <input id="event_age" type="number"
                                    class="form-control min-h-10 border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                                    name="age" value="{{ $calculatedAge }}"
                                    @if ($ageReadonly) readonly
                                    @else
                                        required
                                        placeholder="Enter your age"
                                        min="10"
                                        max="120" @endif>
                            </div>

                            <div class="flex min-w-0 flex-col gap-1.5">
                                <label for="event_messenger" class="text-xs font-semibold text-slate-700">
                                    Messenger Profile
                                </label>
                                <input id="event_messenger" type="text"
                                    class="form-control min-h-10 border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                                    name="messenger_link" placeholder="Messenger name or profile link">
                            </div>

                            <div class="flex min-w-0 flex-col gap-1.5">
                                <label for="event_sex" class="text-xs font-semibold text-slate-700">
                                    Sex <span class="text-red-600">*</span>
                                </label>
                                <select id="event_sex"
                                    class="form-control min-h-10 border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                                    name="sex" required>
                                    <option value="">Select sex</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>

                            <div class="flex min-w-0 flex-col gap-1.5">
                                <label for="event_role" class="text-xs font-semibold text-slate-700">
                                    Volunteer Role <span class="text-red-600">*</span>
                                </label>
                                <select id="event_role"
                                    class="form-control min-h-10 border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                                    name="vroles_id" required>
                                    <option value="">Select a role</option>
                                    @foreach ($rolesForForm as $role)
                                        <option value="{{ $role->vroles_id }}"
                                            title="{{ $role->name }}{{ $role->description ? ' - ' . $role->description : '' }}">
                                            {{ $role->name }}{{ $role->description ? ' - ' . Str::limit($role->description, 80) : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex min-w-0 flex-col gap-1.5 sm:col-span-2">
                                <label for="event_address" class="text-xs font-semibold text-slate-700">
                                    Address <span class="text-red-600">*</span>
                                </label>
                                <input id="event_address" type="text"
                                    class="form-control min-h-10 border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                                    name="address" placeholder="Enter your complete address" required>
                            </div>
                        </div>

                        <div class="event-modal-reminder mt-4 flex flex-col gap-3 border border-indigo-100 bg-white p-3 sm:flex-row sm:items-center sm:justify-between">
                            <label class="flex cursor-pointer items-center gap-3 text-sm font-semibold text-slate-700" for="remind_me">
                                <span class="relative inline-flex h-7 w-12 shrink-0 items-center rounded-full bg-slate-300 transition has-[:checked]:bg-indigo-600">
                                    <input type="checkbox" id="remind_me" name="remind_me" value="1"
                                        class="peer sr-only">
                                    <span
                                        class="ml-1 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
                                </span>
                                <span>Remind me before event</span>
                            </label>

                            <select id="reminder_minutes" name="reminder_minutes"
                                class="form-control min-h-10 border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 sm:w-56">
                                <option value="">Select reminder time</option>
                                <option value="1">1 minute before</option>
                                <option value="15">15 minutes before</option>
                                <option value="30">30 minutes before</option>
                                <option value="60">1 hour before</option>
                                <option value="1440">24 hours before</option>
                            </select>
                        </div>
                    </section>
                </div>
            </div>

            <footer
                class="event-modal-footer flex shrink-0 flex-col gap-2 border-t border-slate-300 bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-7">
                <button type="button"
                    class="btn btn-cancel inline-flex min-h-11 items-center justify-center border border-slate-400 bg-white px-7 text-sm font-semibold text-slate-700 transition hover:border-slate-500 hover:bg-slate-50 hover:text-slate-950 focus:outline-none focus:ring-4 focus:ring-slate-100">
                    Cancel
                </button>
                <button type="submit"
                    class="btn btn-submit register-btn inline-flex min-h-11 items-center justify-center gap-2 bg-indigo-600 px-7 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-100 disabled:cursor-wait disabled:opacity-75">
                    <i class="ri-arrow-right-line" aria-hidden="true"></i>
                    Register Now
                </button>
            </footer>
        </form>
    </section>
</div>
