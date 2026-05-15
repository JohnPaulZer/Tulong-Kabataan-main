<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Events | Tulong Kabataan</title>
    <link rel="icon" href="{{ page_media_url('site_favicon', asset('img/log2.png')) }}" type="image/png" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="event-page">

    @include('partials.universalmodal')
    @include('partials.main-header')
    @include('administrator.partials.loading-screen')

    <section class="evt-hero-section">
        <div class="evt-hero-image">
            <img src="{{ page_media_url('event_hero_image', asset('img/bg1.jpg')) }}" alt="Events Hero" />
            <div class="evt-overlay"></div>
        </div>
        <div class="evt-hero-content">
            <div class="evt-text-container">
                <span class="evt-hero-badge">Volunteer Opportunities</span>
                <h1>Find Your Next Volunteer Opportunity</h1>
                <p>Join thousands of volunteers making a difference in their communities through our events platform.
                </p>
            </div>
        </div>
        @include('partials.wave-divider', ['surface' => 'white'])
    </section>

    <section class="evt-tabs-navigation">
        <div class="evt-container">
            <div class="evt-tabs-scroll">
                <div class="evt-tab-buttons">
                    <button type="button" class="evt-tab-button evt-active">All Events</button>
                    <button type="button" class="evt-tab-button">Ongoing Events</button>
                    <button type="button" class="evt-tab-button">Past Events</button>
                </div>
            </div>
        </div>
    </section>

    <section class="evt-view-toggle-calendar">
        <div class="evt-container evt-view-toggle-calendar-stack">
            <div class="evt-view-toggle-calendar-top">
                <div class="evt-view-toggle">
                    <button class="evt-view-toggle-button evt-active">
                        <i class="ri-apps-2-line"></i>
                        <span>Grid</span>
                    </button>
                    <button class="evt-view-toggle-button">
                        <i class="ri-calendar-line"></i>
                        <span>Calendar</span>
                    </button>
                </div>
                <div class="evt-calendar-info">
                    <i class="ri-calendar-event-line"></i>
                    <span class="evt-text-sm">Showing events for May 2025</span>
                </div>
            </div>

                <div class="evt-mini-calendar">
                <div class="evt-calendar-navigation">
                    <div class="evt-calendar-month-nav">
                        <button class="evt-calendar-nav-button" aria-label="Previous calendar range"><i class="ri-arrow-left-s-line"></i></button>
                        <div class="evt-calendar-month">May 2025</div>
                        <button class="evt-calendar-nav-button" aria-label="Next calendar range"><i class="ri-arrow-right-s-line"></i></button>
                    </div>
                    <div class="evt-calendar-days"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="evt-events-grid">
        <div class="evt-container evts-container">
            @foreach ($events as $event)
                @php
                    $now = \Carbon\Carbon::now();
                    $start = \Carbon\Carbon::parse($event->start_date);
                    $end = \Carbon\Carbon::parse($event->end_date);
                    $isOngoing = $now->between($start, $end);
                    $isPast = $now->greaterThan($end);

                    $status = $eventStatuses[$event->event_id] ?? 'upcoming';
                    $isRegistered = \Auth::check() && in_array($event->event_id, $registeredEventIds ?? []);
                    $statusLabel = match ($status) {
                        'ongoing' => 'Ongoing',
                        'ended' => 'Ended',
                        'closed' => 'Registration Closed',
                        default => 'Upcoming',
                    };

                    // Determine event type for filtering
                    $eventType = 'all';
                    if ($isOngoing) {
                        $eventType = 'ongoing';
                    } elseif ($isPast) {
                        $eventType = 'past';
                    }
                @endphp

                <article @class([
                    'evt-event-card',
                    'evt-event-card--ongoing' => $status === 'ongoing',
                    'evt-event-card--ended' => $status === 'ended',
                    'evt-event-card--closed' => $status === 'closed',
                ]) data-event-id="{{ $event->event_id }}"
                    data-event-date="{{ $start->format('Y-m-d') }}" data-event-type="{{ $eventType }}">
                    <div class="evt-card-image">
                        <img src="{{ file_url($event->photo, page_media_url('event_default_image', asset('img/bg2.jpg'))) }}" alt="Event photo">
                        <span class="evt-card-status evt-status-chip evt-status-chip--{{ $status }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <div class="evt-card-content">
                        <div class="evt-card-header">
                            <h3 class="evt-card-title">{{ $event->title }}</h3>
                        </div>
                        <p class="evt-card-description">{{ Str::limit($event->description, 100) }}</p>
                        <div class="evt-card-details">
                            <span class="evt-card-date">
                                <i class="ri-time-line"></i>
                                <span>
                                    {{ $start->format('M d, Y') }} &bull; {{ $start->format('h:i A') }}
                                    @if (!$isOngoing && !$isPast)
                                        - {{ $end->format('M d, Y') }} &bull; {{ $end->format('h:i A') }}
                                    @endif
                                </span>
                            </span>
                            <span class="evt-card-location">
                                <i class="ri-map-pin-line"></i>
                                <span>{{ $event->location }}</span>
                            </span>
                        </div>
                        <div class="evt-card-footer">
                            <div class="evt-card-actions">
                                @if ($status === 'ongoing')
                                    @if ($isRegistered)
                                        <button class="evt-register-button evt-register-button--ongoing" disabled>Already
                                            Registered (Ongoing)</button>
                                    @else
                                        <button class="evt-register-button evt-register-button--ongoing" disabled>Ongoing</button>
                                    @endif
                                @elseif($status === 'ended')
                                    <button class="evt-register-button evt-register-button--ended" disabled>Event
                                        Ended</button>
                                @elseif($status === 'closed')
                                    <button class="evt-register-button evt-register-button--closed" disabled>Registration
                                        Closed</button>
                                @elseif($isRegistered)
                                    <button class="evt-register-button evt-register-button--registered" disabled>Already
                                        Registered</button>
                                @elseif (!\Auth::check())
                                    <a href="{{ route('login.register') }}" class="evt-register-button evt-register-button--primary"
                                        id="evt-btn-login-register">
                                        Login/Register to Volunteer
                                    </a>
                                @else
                                    <button class="evt-register-button evt-register-button--primary">Register Now</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach

            <!-- Empty state messages -->
            <div id="no-all-events" class="evt-empty-state">
                <i class="ri-calendar-event-line"></i>
                <h3>No events found</h3>
                <p>Volunteer opportunities will appear here once events are available.</p>
            </div>
            <div id="no-ongoing-events" class="evt-empty-state">
                <i class="ri-time-line"></i>
                <h3>No ongoing events found</h3>
                <p>There are no events happening right now. Please check upcoming opportunities.</p>
            </div>
            <div id="no-past-events" class="evt-empty-state">
                <i class="ri-calendar-check-line"></i>
                <h3>No past events found</h3>
                <p>Completed volunteer activities will be listed here after events end.</p>
            </div>
        </div>
    </section>

    @include('partials.main-footer')

    <div id="modal-container"></div>

    <div id="evt-calendar-tooltip" class="evt-calendar-tooltip">
        {{-- ADDED ARROW DIV BELOW --}}
        <div class="evt-tooltip-arrow"></div>
        <div class="tooltip-img-container">
            <img id="tooltip-img" src="" alt="Event">
        </div>
        <div class="tooltip-content">
            <div id="tooltip-title" class="tooltip-title"></div>
            <div id="tooltip-desc" class="tooltip-desc"></div>

            <a href="#" id="tooltip-event-link" class="evt-register-button evt-register-button--primary">
                View Event Details
            </a>
            <div id="tooltip-nav" class="tooltip-nav" style="display:none;">
                <button id="tooltip-prev" class="tooltip-nav-btn"><i class="ri-arrow-left-s-line"></i></button>
                <span id="tooltip-counter" class="tooltip-counter">1 / 1</span>
                <button id="tooltip-next" class="tooltip-nav-btn"><i class="ri-arrow-right-s-line"></i></button>
            </div>
        </div>
    </div>



    <script>
        // ==================== CALENDAR STATE AND CONFIGURATION ====================
        const calendarMonthEl = document.querySelector('.evt-calendar-month');
        const daysContainer = document.querySelector('.evt-calendar-days');
        const prevBtn = document.querySelectorAll('.evt-calendar-nav-button')[0];
        const nextBtn = document.querySelectorAll('.evt-calendar-nav-button')[1];
        const gridToggleBtn = document.querySelectorAll('.evt-view-toggle-button')[0];
        const calendarToggleBtn = document.querySelectorAll('.evt-view-toggle-button')[1];

        /* Calendar State */
        let today = new Date();
        let currentYear = today.getFullYear();
        let currentMonth = today.getMonth();
        let startDay = today.getDate(); // Used for week sliding in Grid view
        let isFullMonth = false; // Default: Grid View (1 Week)

        /* Data from backend */
        const calendarEventsAll = @json($calendarEvents);
        const eventPhotoFallback = @json(page_media_url('event_default_image', asset('img/bg2.jpg')));
        const localStorageBaseUrl = @json(url('storage'));

        function resolveEventPhotoUrl(photo) {
            if (!photo) {
                return eventPhotoFallback;
            }

            if (/^https?:\/\//i.test(photo) || photo.startsWith('/')) {
                return photo;
            }

            return `${localStorageBaseUrl}/${String(photo).replace(/^\/+/, '')}`;
        }

        function getEventStatusKey(now, startDate, endDate, deadlineValue = null) {
            const deadline = deadlineValue ? new Date(deadlineValue) : null;

            if (now >= startDate && now <= endDate) return 'ongoing';
            if (now > endDate) return 'ended';
            if (deadline && now > deadline) return 'closed';
            return 'upcoming';
        }

        function getEventTypeFromStatus(statusKey) {
            if (statusKey === 'ongoing') return 'ongoing';
            if (statusKey === 'ended') return 'past';
            return 'all';
        }

        function getEventStatusLabel(statusKey) {
            if (statusKey === 'ongoing') return 'Ongoing';
            if (statusKey === 'ended') return 'Ended';
            if (statusKey === 'closed') return 'Registration Closed';
            return 'Upcoming';
        }

        function syncEventCardStatus(card, statusKey) {
            if (!card) return;

            card.classList.remove('evt-event-card--ongoing', 'evt-event-card--ended', 'evt-event-card--closed');
            if (statusKey !== 'upcoming') {
                card.classList.add(`evt-event-card--${statusKey}`);
            }

            let chip = card.querySelector('.evt-card-status');
            if (!chip) {
                const imageWrap = card.querySelector('.evt-card-image');
                chip = document.createElement('span');
                chip.className = 'evt-card-status evt-status-chip';
                imageWrap?.appendChild(chip);
            }

            chip.className = `evt-card-status evt-status-chip evt-status-chip--${statusKey}`;
            chip.textContent = getEventStatusLabel(statusKey);
        }

        function getButtonHtmlForStatusKey(statusKey, isRegistered = false) {
            if (statusKey === 'ongoing') {
                const label = isRegistered ? 'Already Registered (Ongoing)' : 'Ongoing';
                return `<button class="evt-register-button evt-register-button--ongoing" disabled>${label}</button>`;
            }

            if (statusKey === 'ended') {
                return `<button class="evt-register-button evt-register-button--ended" disabled>Event Ended</button>`;
            }

            if (statusKey === 'closed') {
                return `<button class="evt-register-button evt-register-button--closed" disabled>Registration Closed</button>`;
            }

            if (isRegistered) {
                return `<button class="evt-register-button evt-register-button--registered" disabled>Already Registered</button>`;
            }

            const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

            if (!isLoggedIn) {
                return `<a href="{{ route('login.register') }}" class="evt-register-button evt-register-button--primary" id="evt-btn-login-register">Login/Register to Volunteer</a>`;
            }

            return `<button class="evt-register-button evt-register-button--primary">Register Now</button>`;
        }

        // ==================== MODAL LIFECYCLE HELPERS ====================
        let eventModalScrollState = null;

        function getEventModalContainer() {
            return document.getElementById('modal-container');
        }

        function lockEventPageScroll() {
            if (eventModalScrollState) return;

            const body = document.body;
            const html = document.documentElement;
            const scrollY = window.scrollY || html.scrollTop || 0;
            const scrollbarWidth = window.innerWidth - html.clientWidth;

            eventModalScrollState = {
                scrollY,
                bodyOverflow: body.style.overflow,
                bodyPosition: body.style.position,
                bodyTop: body.style.top,
                bodyWidth: body.style.width,
                bodyPaddingRight: body.style.paddingRight,
                htmlOverflow: html.style.overflow,
            };

            body.classList.add('event-modal-open');
            body.style.overflow = 'hidden';
            body.style.position = 'fixed';
            body.style.top = `-${scrollY}px`;
            body.style.width = '100%';
            if (scrollbarWidth > 0) {
                body.style.paddingRight = `${scrollbarWidth}px`;
            }
            html.style.overflow = 'hidden';
        }

        function unlockEventPageScroll() {
            if (!eventModalScrollState) return;

            const body = document.body;
            const html = document.documentElement;
            const restoreY = eventModalScrollState.scrollY || 0;

            body.classList.remove('event-modal-open');
            body.style.overflow = eventModalScrollState.bodyOverflow;
            body.style.position = eventModalScrollState.bodyPosition;
            body.style.top = eventModalScrollState.bodyTop;
            body.style.width = eventModalScrollState.bodyWidth;
            body.style.paddingRight = eventModalScrollState.bodyPaddingRight;
            html.style.overflow = eventModalScrollState.htmlOverflow;
            eventModalScrollState = null;

            window.scrollTo(0, restoreY);
        }

        function closeRegistrationModal() {
            const container = getEventModalContainer();
            if (container) {
                container.innerHTML = '';
            }
            syncEventModalState();
        }

        function refreshEventModalMaps(container) {
            if (!container) return;

            window.TKLeafletMaps?.mountDeclarativeStaticMaps(container);
            [80, 220, 420].forEach((delay) => {
                window.setTimeout(() => {
                    window.dispatchEvent(new Event('tk:event-modal-opened'));
                    window.dispatchEvent(new Event('resize'));
                }, delay);
            });
        }

        function initializeRegistrationModalControls(container) {
            const backdrop = container?.querySelector('.event-modal-backdrop');
            if (!backdrop || backdrop.dataset.ready === 'true') return;

            backdrop.dataset.ready = 'true';

            const dialog = backdrop.querySelector('.event-modal');
            if (dialog) {
                dialog.setAttribute('tabindex', '-1');
                window.setTimeout(() => dialog.focus({
                    preventScroll: true
                }), 50);
            }

            backdrop.querySelectorAll('.event-modal-close, .btn-cancel').forEach((button) => {
                button.addEventListener('click', closeRegistrationModal);
            });

            backdrop.addEventListener('click', (event) => {
                if (event.target === backdrop) {
                    closeRegistrationModal();
                }
            });

            const form = backdrop.querySelector('form[action="/submit-registration"]');
            form?.addEventListener('submit', (event) => {
                window.setTimeout(() => {
                    if (event.defaultPrevented) return;

                    const submitButton = form.querySelector('.btn-submit');
                    if (!submitButton) return;

                    submitButton.disabled = true;
                    submitButton.classList.add('is-loading');
                    submitButton.innerHTML = '<span class="spinner"></span> Confirming...';
                }, 0);
            });
        }

        function syncEventModalState() {
            const container = getEventModalContainer();
            const confirmModal = document.getElementById('confirmModal');
            const hasConfirmModalOpen = Boolean(confirmModal && window.getComputedStyle(confirmModal).display !== 'none');
            const hasOpenModal = Boolean(container?.querySelector('.event-modal-backdrop, .evt-details-modal')) ||
                hasConfirmModalOpen;

            if (hasOpenModal) {
                lockEventPageScroll();
                initializeRegistrationModalControls(container);
                refreshEventModalMaps(container);
            } else {
                unlockEventPageScroll();
            }
        }

        function handleEventModalKeydown(event) {
            if (event.key !== 'Escape') return;

            const container = getEventModalContainer();
            if (container?.querySelector('.event-modal-backdrop')) {
                closeRegistrationModal();
                return;
            }

            if (container?.querySelector('.evt-details-modal')) {
                closeEventModal();
            }
        }

        function initializeEventModalLifecycle() {
            const container = getEventModalContainer();
            if (!container || container.dataset.lifecycleReady === 'true') return;

            container.dataset.lifecycleReady = 'true';
            document.addEventListener('keydown', handleEventModalKeydown);

            const observer = new MutationObserver(() => {
                syncEventModalState();
            });

            observer.observe(container, {
                childList: true,
                subtree: false
            });

            const confirmModal = document.getElementById('confirmModal');
            if (confirmModal) {
                const confirmObserver = new MutationObserver(syncEventModalState);
                confirmObserver.observe(confirmModal, {
                    attributes: true,
                    attributeFilter: ['class', 'style']
                });
            }

            syncEventModalState();
        }


        // ==================== CALENDAR UTILITY FUNCTIONS ====================
        /**
         * Get number of days in a month
         */
        function getDaysInMonth(year, month) {
            return new Date(year, month + 1, 0).getDate();
        }

        /**
         * Format date as YYYY-MM-DD for consistent key usage
         */
        function formatDateKey(year, month, day) {
            const m = (month + 1).toString().padStart(2, '0');
            const d = day.toString().padStart(2, '0');
            return `${year}-${m}-${d}`;
        }

        /**
         * Build event lookup object for efficient date-based event retrieval
         */
        function buildEventDayLookup(eventsArray, filterType = 'all') {
            const lookup = {};
            const now = new Date();

            (eventsArray || []).forEach(ev => {
                if (!ev || !ev.start_date || !ev.end_date) return;
                const start = new Date(ev.start_date);
                const end = new Date(ev.end_date);
                let include = false;

                if (filterType === 'all') include = true;
                else if (filterType === 'ongoing') include = now >= start && now <= end;
                else if (filterType === 'past') include = now > end;

                if (include) {
                    // We use the start date for calendar marker lookup
                    const key = formatDateKey(start.getFullYear(), start.getMonth(), start.getDate());
                    if (!lookup[key]) lookup[key] = []; // Initialize array

                    // This object structure is confirmed to include 'id' (which maps to event_id)
                    lookup[key].push({
                        title: ev.title,
                        description: ev.description,
                        photo: ev.photo,
                        id: ev.id // Using 'id' from the PHP array mapping
                    });
                }
            });
            return lookup;
        }

        // Build event lookups for different filter types
        const eventDaysAll = buildEventDayLookup(calendarEventsAll, 'all');
        const eventDaysOngoing = buildEventDayLookup(calendarEventsAll, 'ongoing');
        const eventDaysPast = buildEventDayLookup(calendarEventsAll, 'past');

        function centerMiniCalendarOnMobile() {
            const calendarRail = document.querySelector('.evt-mini-calendar');
            const calendarNavigation = document.querySelector('.evt-calendar-navigation');

            if (!calendarRail || !calendarNavigation) {
                return;
            }

            if (!window.matchMedia('(max-width: 768px)').matches) {
                calendarRail.scrollLeft = 0;
                return;
            }

            const maxScrollLeft = calendarRail.scrollWidth - calendarRail.clientWidth;
            if (maxScrollLeft <= 0) {
                calendarRail.scrollLeft = 0;
                return;
            }

            const centeredScrollLeft = Math.max(0, (calendarNavigation.offsetWidth - calendarRail.clientWidth) / 2);
            calendarRail.scrollLeft = Math.min(maxScrollLeft, centeredScrollLeft);
        }

        function scheduleMiniCalendarCentering() {
            window.requestAnimationFrame(() => {
                window.requestAnimationFrame(centerMiniCalendarOnMobile);
            });
        }


        // ==================== CALENDAR RENDERING FUNCTIONS ====================
        /**
         * Main calendar rendering function
         */
        function renderCalendar() {
            const monthName = new Date(currentYear, currentMonth).toLocaleString('default', {
                month: 'long'
            });
            calendarMonthEl.textContent = `${monthName} ${currentYear}`;

            // Update calendar info text based on view
            updateCalendarInfoText(monthName);

            const daysInMonth = getDaysInMonth(currentYear, currentMonth);
            daysContainer.innerHTML = '';

            // Determine active lookup based on current tab
            const activeTab = document.querySelector('.evt-tab-button.evt-active')?.textContent?.trim();
            let lookup = eventDaysAll;
            if (activeTab === 'Ongoing Events') lookup = eventDaysOngoing;
            else if (activeTab === 'Past Events') lookup = eventDaysPast;

            // Render week day headers
            renderWeekDayHeaders();

            // Render calendar days based on view mode
            if (isFullMonth) {
                renderFullMonthView(daysInMonth, lookup);
            } else {
                renderGridView(daysInMonth, lookup);
            }

            // Attach tooltip and click handlers to all days
            setupTooltipHandlers(lookup);
            scheduleMiniCalendarCentering();
        }

        /**
         * Update the calendar info text based on current view
         */
        function updateCalendarInfoText(monthName) {
            const calendarInfo = document.querySelector('.evt-calendar-info span');
            if (calendarInfo) {
                if (isFullMonth) {
                    calendarInfo.textContent = `Showing events for ${monthName} ${currentYear}`;
                } else {
                    // For grid view, show the date range of the visible week
                    const startDate = new Date(currentYear, currentMonth, startDay);
                    const endDate = new Date(currentYear, currentMonth, Math.min(startDay + 6, getDaysInMonth(currentYear,
                        currentMonth)));

                    const startFormatted = startDate.toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric'
                    });
                    const endFormatted = endDate.toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric'
                    });

                    calendarInfo.textContent = `Showing events for ${startFormatted} - ${endFormatted}, ${currentYear}`;
                }
            }
        }

        /**
         * Render week day headers (Sun, Mon, Tue, etc.)
         */
        function renderWeekDayHeaders() {
            const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            weekDays.forEach(wd => {
                const el = document.createElement('div');
                el.textContent = wd;
                el.className = 'evt-calendar-weekday';
                daysContainer.appendChild(el);
            });
        }

        /**
         * Render full month calendar view
         */
        function renderFullMonthView(daysInMonth, lookup) {
            daysContainer.classList.remove('simple-view');

            // Add empty cells for days before the first day of month
            let firstWeekday = new Date(currentYear, currentMonth, 1).getDay();
            for (let i = 0; i < firstWeekday; i++) {
                const emptyEl = document.createElement('div');
                emptyEl.className = 'evt-calendar-empty-day';
                daysContainer.appendChild(emptyEl);
            }

            // Render all days of the month
            for (let dayNum = 1; dayNum <= daysInMonth; dayNum++) {
                renderDayCell(dayNum, lookup);
            }
        }

        /**
         * Render grid (week) calendar view
         */
        function renderGridView(daysInMonth, lookup) {
            daysContainer.classList.add('simple-view');
            startDay = Math.min(Math.max(startDay, 1), daysInMonth);

            // Render only the current week (7 days)
            for (let i = 0; i < 7; i++) {
                let dayNum = startDay + i;
                if (dayNum > daysInMonth) break;
                renderDayCell(dayNum, lookup);
            }
        }

        /**
         * Render individual day cell
         */
        function renderDayCell(dayNum, lookup) {
            const key = formatDateKey(currentYear, currentMonth, dayNum);
            const dayEl = document.createElement('div');
            dayEl.className = 'evt-calendar-day';
            dayEl.dataset.day = dayNum;
            dayEl.dataset.dateKey = key;

            // Check if this is today's date
            const today = new Date();
            const isToday = currentYear === today.getFullYear() &&
                currentMonth === today.getMonth() &&
                dayNum === today.getDate();

            // Add today class if it's the current date
            if (isToday) {
                dayEl.classList.add('evt-calendar-today');
            }

            let contentHtml = `<span class="evt-calendar-daynum">${dayNum}</span>`;

            if (lookup[key] && lookup[key].length > 0) {
                const events = lookup[key];
                const firstEvent = events[0];
                const count = events.length;

                // Title displayed (No Image)
                contentHtml += `<div class="evt-cal-title">${firstEvent.title}</div>`;

                // If more than 1 event, show indicator badge
                if (count > 1) {
                    contentHtml += `<div class="evt-more-indicator">+${count - 1} more</div>`;
                }

                dayEl.classList.add('has-event');
            } else {
                contentHtml += `<div class="evt-calendar-day-empty"></div>`;
            }

            dayEl.innerHTML = contentHtml;
            daysContainer.appendChild(dayEl);
        }


        // ==================== TOOLTIP LOGIC ====================
        const tooltipEl = document.getElementById('evt-calendar-tooltip');
        const tooltipImg = document.getElementById('tooltip-img');
        const tooltipTitle = document.getElementById('tooltip-title');
        const tooltipDesc = document.getElementById('tooltip-desc');
        const tooltipNav = document.getElementById('tooltip-nav');
        const tooltipPrev = document.getElementById('tooltip-prev');
        const tooltipNext = document.getElementById('tooltip-next');
        const tooltipCounter = document.getElementById('tooltip-counter');
        const tooltipEventLink = document.getElementById('tooltip-event-link');

        let currentTooltipEvents = [];
        let currentTooltipIndex = 0;
        let hideTooltipTimer = null;

        /**
         * Set up tooltip event handlers for calendar days
         */
        function setupTooltipHandlers(lookup) {
            // Select all calendar day elements
            const days = daysContainer.querySelectorAll('.evt-calendar-day');

            days.forEach(day => {
                const dateKey = day.dataset.dateKey;
                const events = lookup[dateKey];

                // --- TOOLTIP LOGIC (only for days with events) ---
                if (events && events.length > 0) {
                    day.addEventListener('mouseenter', (e) => {
                        clearTimeout(hideTooltipTimer);
                        currentTooltipEvents = events;
                        currentTooltipIndex = 0; // Reset to first event
                        updateTooltipContent();

                        // Position Tooltip
                        positionTooltip(day);
                    });

                    day.addEventListener('mouseleave', () => {
                        hideTooltipTimer = setTimeout(() => {
                            tooltipEl.classList.remove('active');
                        }, 150);
                    });
                }

                // --- CLICK LOGIC: Filter events below based on date ---
                day.addEventListener('click', function() {
                    // 1. Reset active day highlight
                    daysContainer.querySelectorAll('.evt-calendar-day.active').forEach(d => d.classList
                        .remove('active'));

                    // 2. Highlight clicked day
                    this.classList.add('active');

                    // 3. Trigger filter
                    const dayNum = parseInt(this.dataset.day);
                    filterEventsByDate(currentYear, currentMonth, dayNum);
                });
            });
        }

        /**
         * Position tooltip relative to calendar day (with Flip and Arrow Logic)
         */
        function positionTooltip(dayElement) {
            const rect = dayElement.getBoundingClientRect();
            const tooltipRect = tooltipEl.getBoundingClientRect();

            // 1. Calculate Horizontal Position (Center of Day)
            let leftPos = rect.left + (rect.width / 2) - (tooltipRect.width / 2);

            // Boundary Check (X-Axis)
            if (leftPos < 10) leftPos = 10;
            if (leftPos + tooltipRect.width > window.innerWidth) {
                leftPos = window.innerWidth - tooltipRect.width - 10;
            }

            // 2. Calculate Vertical Position (Default: TOP)
            let topPos = rect.top - tooltipRect.height - 12; // 12px gap
            let positionClass = 'position-top';

            // Boundary Check (Y-Axis) - Flip to bottom if no space on top
            if (topPos < 10) {
                topPos = rect.bottom + 12; // Move below day
                positionClass = 'position-bottom';
            }

            // 3. Apply Classes for Arrow Styling
            tooltipEl.classList.remove('position-top', 'position-bottom');
            tooltipEl.classList.add(positionClass);

            // 4. Apply Position
            tooltipEl.style.top = `${topPos}px`;
            tooltipEl.style.left = `${leftPos}px`;
            tooltipEl.classList.add('active');

            // 5. Dynamic Arrow Positioning
            // The arrow must point to the center of the day, even if the tooltip is shifted
            const arrow = tooltipEl.querySelector('.evt-tooltip-arrow');
            if (arrow) {
                const dayCenter = rect.left + (rect.width / 2);
                let arrowLeft = dayCenter - leftPos - 7; // 7 is half of arrow width (14px)

                // Clamp arrow inside tooltip (keep it from detaching)
                arrowLeft = Math.max(6, Math.min(arrowLeft, tooltipRect.width - 20));

                arrow.style.left = `${arrowLeft}px`;
            }
        }

        /**
         * Update tooltip content with current event data
         */
        function updateTooltipContent() {
            const evt = currentTooltipEvents[currentTooltipIndex];

            tooltipImg.src = resolveEventPhotoUrl(evt.photo);
            tooltipTitle.textContent = evt.title;
            tooltipDesc.textContent = evt.description || "No description available.";

            // ✅ FIXED: Update the link without replacing the element
            if (evt.id) {
                tooltipEventLink.href = '#';
                tooltipEventLink.style.display = 'inline-flex';
                tooltipEventLink.textContent = 'View Event Details';

                // Store the current event ID in a data attribute for the click handler
                tooltipEventLink.dataset.eventId = evt.id;
            } else {
                // Fallback for safety
                tooltipEventLink.href = '#';
                tooltipEventLink.style.display = 'none';
                console.warn("Event ID is missing for event:", evt.title);
            }

            // Handle Multiple Events
            if (currentTooltipEvents.length > 1) {
                tooltipNav.style.display = 'flex';
                tooltipCounter.textContent = `${currentTooltipIndex + 1} / ${currentTooltipEvents.length}`;

                tooltipPrev.disabled = currentTooltipIndex === 0;
                tooltipNext.disabled = currentTooltipIndex === currentTooltipEvents.length - 1;
            } else {
                tooltipNav.style.display = 'none';
            }
        }

        /**
         * Handle tooltip link click to open modal
         */
        function handleTooltipLinkClick(e) {
            e.preventDefault();
            e.stopPropagation();

            const eventId = this.dataset.eventId;
            if (eventId) {
                // Close tooltip
                tooltipEl.classList.remove('active');

                // Show modal for this event
                showEventModal(eventId);
            }
        }

        // Set up the tooltip link click event listener once
        tooltipEventLink.addEventListener('click', handleTooltipLinkClick);

        // Keep tooltip open if mouse is over it
        tooltipEl.addEventListener('mouseenter', () => {
            clearTimeout(hideTooltipTimer);
        });

        tooltipEl.addEventListener('mouseleave', () => {
            tooltipEl.classList.remove('active');
        });

        // Navigation Logic for tooltip
        tooltipPrev.addEventListener('click', (e) => {
            e.stopPropagation();
            if (currentTooltipIndex > 0) {
                currentTooltipIndex--;
                updateTooltipContent();
            }
        });

        tooltipNext.addEventListener('click', (e) => {
            e.stopPropagation();
            if (currentTooltipIndex < currentTooltipEvents.length - 1) {
                currentTooltipIndex++;
                updateTooltipContent();
            }
        });
        // ==================== EVENT FILTERING FUNCTIONS ====================
        /**
         * Filter events by specific date
         */
        function filterEventsByDate(year, month, day) {
            const key = formatDateKey(year, month, day);
            const activeTab = document.querySelector('.evt-tab-button.evt-active')?.textContent?.trim();

            // Hide all empty state messages first
            document.querySelectorAll('.evt-empty-state').forEach(state => state.style.display = 'none');

            const eventCards = eventsContainer.querySelectorAll('.evt-event-card');
            let hasVisibleCards = false;

            eventCards.forEach(card => {
                const cardDate = card.dataset.eventDate;
                const eventType = card.dataset.eventType;

                // Check if card matches both date AND current tab filter
                let showCard = (cardDate === key);

                // Also apply tab filter
                switch (activeTab) {
                    case 'Ongoing Events':
                        showCard = showCard && (eventType === 'ongoing');
                        break;
                    case 'Past Events':
                        showCard = showCard && (eventType === 'past');
                        break;
                        // For 'All Events', no additional filter needed
                }

                if (showCard) {
                    card.style.display = 'flex';
                    hasVisibleCards = true;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show appropriate empty state if no cards are visible
            if (!hasVisibleCards) {
                if (activeTab === 'All Events') {
                    document.getElementById('no-all-events').style.display = 'block';
                } else if (activeTab === 'Ongoing Events') {
                    document.getElementById('no-ongoing-events').style.display = 'block';
                } else if (activeTab === 'Past Events') {
                    document.getElementById('no-past-events').style.display = 'block';
                }
            }
        }

        /**
         * Show all events in current view
         */
        function showAllEvents() {
            const activeTab = document.querySelector('.evt-tab-button.evt-active')?.textContent?.trim();
            filterEventsByType(activeTab);
        }


        // ==================== TAB SWITCHING LOGIC ====================
        const tabButtons = document.querySelectorAll('.evt-tab-button');
        const eventsContainer = document.querySelector('.evts-container');
        const tabsScroll = document.querySelector('.evt-tabs-scroll');

        function centerEventTabInRail(tabButton) {
            if (!tabsScroll || !tabButton || window.innerWidth > 768) {
                return;
            }

            const targetLeft = tabButton.offsetLeft - ((tabsScroll.clientWidth - tabButton.offsetWidth) / 2);
            const maxScrollLeft = tabsScroll.scrollWidth - tabsScroll.clientWidth;
            const nextScrollLeft = Math.max(0, Math.min(targetLeft, maxScrollLeft));

            tabsScroll.scrollTo({
                left: nextScrollLeft,
                behavior: 'smooth'
            });
        }

        function initializeTabsHorizontalScroll() {
            if (!tabsScroll || tabsScroll.dataset.scrollReady === 'true') {
                return;
            }

            tabsScroll.dataset.scrollReady = 'true';

            let isPointerDown = false;
            let hasDragged = false;
            let startX = 0;
            let startScrollLeft = 0;

            const canScroll = () => window.innerWidth <= 768 && tabsScroll.scrollWidth > tabsScroll.clientWidth + 4;

            tabsScroll.addEventListener('wheel', function(event) {
                if (!canScroll()) {
                    return;
                }

                const delta = Math.abs(event.deltaX) > Math.abs(event.deltaY) ? event.deltaX : event.deltaY;
                if (!delta) {
                    return;
                }

                tabsScroll.scrollLeft += delta;
                event.preventDefault();
            }, {
                passive: false
            });

            tabsScroll.addEventListener('pointerdown', function(event) {
                if (!canScroll()) {
                    return;
                }

                if (event.pointerType === 'mouse' && event.button !== 0) {
                    return;
                }

                isPointerDown = true;
                hasDragged = false;
                startX = event.clientX;
                startScrollLeft = tabsScroll.scrollLeft;
            });

            tabsScroll.addEventListener('pointermove', function(event) {
                if (!isPointerDown || !canScroll()) {
                    return;
                }

                const deltaX = event.clientX - startX;
                if (!hasDragged && Math.abs(deltaX) < 5) {
                    return;
                }

                hasDragged = true;
                tabsScroll.scrollLeft = startScrollLeft - deltaX;
                event.preventDefault();
            });

            const stopDragging = () => {
                isPointerDown = false;
                window.setTimeout(() => {
                    hasDragged = false;
                }, 0);
            };

            tabsScroll.addEventListener('pointerup', stopDragging);
            tabsScroll.addEventListener('pointercancel', stopDragging);
            tabsScroll.addEventListener('pointerleave', stopDragging);

            tabsScroll.addEventListener('click', function(event) {
                if (!hasDragged) {
                    return;
                }

                event.preventDefault();
                event.stopPropagation();
            }, true);
        }

        /**
         * Initialize tab switching functionality
         */
        function initializeTabSwitching() {
            tabButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    tabButtons.forEach(b => b.classList.remove('evt-active'));
                    this.classList.add('evt-active');
                    centerEventTabInRail(this);
                    const tabText = this.textContent.trim();

                    // Filter events based on selected tab
                    filterEventsByType(tabText);

                    // Reset calendar active state and re-render for new filter
                    daysContainer.querySelectorAll('.evt-calendar-day.active').forEach(d => d.classList
                        .remove('active'));
                    showAllEvents();
                    renderCalendar();
                });
            });
        }

        /**
         * Filter events by type (All, Ongoing, Past)
         */
        function filterEventsByType(filterType) {
            const eventCards = eventsContainer.querySelectorAll('.evt-event-card');
            let hasVisibleCards = false;
            let hasOngoingCards = false;
            let hasPastCards = false;

            // Hide all empty state messages first
            document.querySelectorAll('.evt-empty-state').forEach(state => state.style.display = 'none');

            eventCards.forEach(card => {
                const eventType = card.dataset.eventType;
                let showCard = false;

                switch (filterType) {
                    case 'All Events':
                        showCard = true;
                        break;
                    case 'Ongoing Events':
                        showCard = (eventType === 'ongoing');
                        break;
                    case 'Past Events':
                        showCard = (eventType === 'past');
                        break;
                    default:
                        showCard = true;
                }

                if (showCard) {
                    card.style.display = 'flex';
                    hasVisibleCards = true;

                    if (eventType === 'ongoing') hasOngoingCards = true;
                    if (eventType === 'past') hasPastCards = true;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show appropriate empty state message
            if (!hasVisibleCards) {
                document.getElementById('no-all-events').style.display = 'block';
            } else if (filterType === 'Ongoing Events' && !hasOngoingCards) {
                document.getElementById('no-ongoing-events').style.display = 'block';
            } else if (filterType === 'Past Events' && !hasPastCards) {
                document.getElementById('no-past-events').style.display = 'block';
            }
        }

        // ==================== VIEW TOGGLE LOGIC ====================
        /**
         * Initialize view toggle functionality (Grid/Calendar)
         */
        function initializeViewToggle() {
            gridToggleBtn.addEventListener('click', () => {
                isFullMonth = false; // 1 Week
                startDay = 1; // Reset to start of month
                gridToggleBtn.classList.add('evt-active');
                calendarToggleBtn.classList.remove('evt-active');
                renderCalendar();
            });

            calendarToggleBtn.addEventListener('click', () => {
                isFullMonth = true; // Full Month
                calendarToggleBtn.classList.add('evt-active');
                gridToggleBtn.classList.remove('evt-active');
                renderCalendar();
            });
        }


        // ==================== CALENDAR NAVIGATION LOGIC ====================
        /**
         * Handle month change with cleanup
         */
        function handleMonthChange() {
            // Reset active day highlight and clear any active filter before changing view
            daysContainer.querySelectorAll('.evt-calendar-day.active').forEach(d => d.classList.remove('active'));
            showAllEvents();
            renderCalendar();
        }

        /**
         * Initialize calendar navigation buttons
         */
        function initializeCalendarNavigation() {
            prevBtn.addEventListener('click', () => {
                if (isFullMonth) {
                    currentMonth--;
                    if (currentMonth < 0) {
                        currentMonth = 11;
                        currentYear--;
                    }
                } else {
                    // Grid view: Slide by 1 week
                    startDay -= 7;
                    if (startDay < 1) {
                        currentMonth--;
                        if (currentMonth < 0) {
                            currentMonth = 11;
                            currentYear--;
                        }
                        startDay = 1; // Simplified jump to start of prev month
                    }
                }
                handleMonthChange();
            });

            nextBtn.addEventListener('click', () => {
                if (isFullMonth) {
                    currentMonth++;
                    if (currentMonth > 11) {
                        currentMonth = 0;
                        currentYear++;
                    }
                } else {
                    // Grid view: Slide by 1 week
                    const maxDays = getDaysInMonth(currentYear, currentMonth);
                    startDay += 7;
                    if (startDay > maxDays) {
                        currentMonth++;
                        if (currentMonth > 11) {
                            currentMonth = 0;
                            currentYear++;
                        }
                        startDay = 1;
                    }
                }
                handleMonthChange();
            });
        }


        // ==================== FORM VALIDATION FOR MODAL ====================
        /**
         * Set up modal form validation observer
         */
        function setupModalFormValidation() {
            const modalContainer = document.getElementById('modal-container');

            if (modalContainer) {
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                            const form = modalContainer.querySelector(
                                'form[action="/submit-registration"]');
                            if (form) {
                                console.log('Modal form detected, setting up validation');
                                attachFormValidation(form);
                            }
                        }
                    });
                });

                observer.observe(modalContainer, {
                    childList: true,
                    subtree: true
                });
            }
        }

        /**
         * Attach validation to form elements
         */
        function attachFormValidation(form) {
            const reminderToggle = document.getElementById('remind_me');
            const reminderSelect = document.getElementById('reminder_minutes');
            const submitBtn = form.querySelector('.register-btn');
            const messengerLink = form.querySelector('input[name="messenger_link"]');
            const sexSelect = form.querySelector('select[name="sex"]');
            const addressInput = form.querySelector('input[name="address"]');
            const roleSelect = form.querySelector('select[name="vroles_id"]');

            if (!reminderToggle || !reminderSelect || !submitBtn) {
                return;
            }

            // Function to update reminder select disabled state
            function updateReminderSelectState() {
                if (reminderToggle.checked) {
                    reminderSelect.disabled = false;
                    reminderSelect.style.opacity = '1';
                    reminderSelect.style.cursor = 'pointer';
                } else {
                    reminderSelect.disabled = true;
                    reminderSelect.style.opacity = '0.6';
                    reminderSelect.style.cursor = 'not-allowed';
                    // Clear selection when disabled
                    reminderSelect.value = '';
                    removeError(reminderSelect);
                }
            }

            // Initialize the state
            updateReminderSelectState();

            // Change submit button to type="button" to prevent default form submission
            submitBtn.type = 'button';

            // Handle button click for validation
            submitBtn.addEventListener('click', function(e) {
                let hasErrors = false;

                // Reset all error styles first
                resetErrorStyles();

                // Check if reminder is toggled ON but no time is selected
                if (reminderToggle.checked && !reminderSelect.value) {
                    showError(reminderSelect);
                    hasErrors = true;
                }

                // Validate sex selection
                if (!sexSelect.value) {
                    showError(sexSelect);
                    hasErrors = true;
                }

                // Validate address
                if (!addressInput.value.trim()) {
                    showError(addressInput);
                    hasErrors = true;
                }

                // Validate role selection
                if (!roleSelect.value) {
                    showError(roleSelect);
                    hasErrors = true;
                }

                if (hasErrors) {
                    // Focus on first error field
                    const firstErrorField = form.querySelector('.field-error');
                    if (firstErrorField) {
                        firstErrorField.focus();
                    }
                    return false;
                }

                if (!form.reportValidity()) {
                    return false;
                }

                // If validation passes, submit the form
                window.TKLoadingModal?.show();
                form.submit();
            });

            // Remove error styling when user interacts with fields
            const allFields = [reminderSelect, messengerLink, sexSelect, addressInput, roleSelect];
            allFields.forEach(field => {
                if (field) {
                    field.addEventListener('change', function() {
                        if (this.value && this.value.trim()) {
                            removeError(this);
                        }
                    });
                    field.addEventListener('input', function() {
                        if (this.value && this.value.trim()) {
                            removeError(this);
                        }
                    });
                }
            });

            // Update reminder select state when toggle changes
            reminderToggle.addEventListener('change', function() {
                updateReminderSelectState();
                if (!this.checked) {
                    removeError(reminderSelect);
                }
            });
        }

        function showError(field) {
            field.style.borderColor = '#dc3545';
            field.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
            field.style.backgroundColor = '#fff5f5';
            field.classList.add('field-error');
        }

        function removeError(field) {
            field.style.borderColor = '';
            field.style.boxShadow = '';
            field.style.backgroundColor = '';
            field.classList.remove('field-error');
        }

        function resetErrorStyles() {
            const errorFields = document.querySelectorAll('.field-error');
            errorFields.forEach(field => {
                removeError(field);
            });
        }


        // ==================== EVENT REGISTRATION HANDLERS ====================
        /**
         * Initialize event registration button handlers
         */
        function initializeRegistrationHandlers() {
            document.querySelectorAll('.evt-register-button').forEach(element => {
                if (element.id === 'evt-btn-login-register') {
                    return;
                }

                // Store the original HTML content of the button
                const originalButtonHtml = element.innerHTML;

                element.addEventListener('click', function() {
                    const button = this;
                    const eventId = button.closest('.evt-event-card').dataset.eventId;

                    button.disabled = true;
                    button.classList.add('loading');

                    button.innerHTML =
                        '<span class="spinner"></span> Loading...';

                    fetch(`/event-register/${eventId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.text();
                        })
                        .then(data => {
                            const container = document.getElementById('modal-container');
                            container.innerHTML = data;
                            syncEventModalState();

                            button.disabled = false;
                            button.classList.remove('loading');
                            button.innerHTML = originalButtonHtml; // Restore original button text

                            const closeBtn = container.querySelector('.event-modal-close');
                            const cancelBtn = container.querySelector('.btn-cancel');

                            [closeBtn, cancelBtn].forEach(btn => {
                                if (btn) btn.addEventListener('click', () => {
                                    // Clear container content to hide the modal
                                    closeRegistrationModal();
                                });
                            });
                        })
                        .catch(error => {
                            console.error('Registration fetch error:', error);

                            button.disabled = false;
                            button.classList.remove('loading');
                            button.innerHTML = originalButtonHtml; // Restore original button text

                            // Optional: Display an error message to the user here
                        });
                });
            });
        }


        // ==================== EVENT CARD MODAL HANDLERS ====================
        /**
         * Initialize event card click handlers to show modal
         */
        function initializeEventCardHandlers() {
            document.querySelectorAll('.evt-event-card').forEach(card => {
                card.style.cursor = 'pointer';
                card.addEventListener('click', function(e) {
                    if (e.target.closest('.evt-register-button')) return;
                    const eventId = this.dataset.eventId;
                    showEventModal(eventId);
                });
            });
        }

        /**
         * Show event modal by fetching content from server
         */
        function showEventModal(eventId) {
            // Show loading
            const modalContainer = document.getElementById('modal-container');
            modalContainer.innerHTML = `
        <div class="evt-details-modal active">
            <div class="evt-details-modal-overlay"></div>
            <div class="evt-details-modal-content">
                <div class="event-modal-loading">
                    <div class="spinner"></div>
                    <p>Loading event details...</p>
                </div>
            </div>
        </div>
    `;
            syncEventModalState();

            // Load modal content
            fetch(`/event-modal/${eventId}`)
                .then(response => response.text())
                .then(html => {
                    modalContainer.innerHTML = html;
                    syncEventModalState();

                    // Re-attach event listeners after modal content is loaded
                    attachModalEventListeners();
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalContainer.innerHTML = `
                <div class="evt-details-modal active">
                    <div class="evt-details-modal-overlay"></div>
                    <div class="evt-details-modal-content">
                        <div class="event-modal-error">
                            <i class="ri-error-warning-line"></i>
                            <h3>Error Loading Event</h3>
                            <p>Failed to load event details. Please try again.</p>
                            <button class="evt-details-modal-close-btn" onclick="closeEventModal()">Close</button>
                        </div>
                    </div>
                </div>
            `;
                    syncEventModalState();
                });
        }

        /**
         * Close event modal
         */
        function closeEventModal() {
            const modalContainer = document.getElementById('modal-container');
            modalContainer.innerHTML = '';
            syncEventModalState();
        }

        /**
         * Handle registration from modal
         */
        function registerForEvent(eventId) {
            closeEventModal();
            // Find and click the register button
            const eventCard = document.querySelector(`.evt-event-card[data-event-id="${eventId}"]`);
            if (eventCard) {
                const registerButton = eventCard.querySelector('.evt-register-button');
                if (registerButton) registerButton.click();
            }
        }

        /**
         * Attach event listeners to modal elements
         */
        function attachModalEventListeners() {
            // Close modal on escape key
            document.addEventListener('keydown', handleModalEscapeKey);

            // Close modal when clicking overlay
            document.addEventListener('click', handleModalOverlayClick);
        }

        /**
         * Handle escape key to close modal
         */
        function handleModalEscapeKey(event) {
            if (event.key === 'Escape') {
                closeEventModal();
                // Remove the event listener after closing
                document.removeEventListener('keydown', handleModalEscapeKey);
            }
        }

        /**
         * Handle overlay click to close modal
         */
        function handleModalOverlayClick(event) {
            if (event.target.classList.contains('evt-details-modal-overlay')) {
                closeEventModal();
                // Remove the event listener after closing
                document.removeEventListener('click', handleModalOverlayClick);
            }
        }
        // ==================== TOAST NOTIFICATION SYSTEM ====================
        /**
         * Show universal toast notification
         */
        function showUniversalToast(message, type = 'success', timeout = 4000) {
            const toastEl = document.getElementById('toast');
            if (!toastEl) return;

            if (toastEl.style.opacity === '1') {
                return;
            }

            let icon;
            let bgColor; // 👈 New variable to hold background color

            switch (type) {
                case 'success':
                    icon = '';
                    bgColor = '#16a34a'; // Static green for success
                    break;
                case 'warning':
                    icon = '';
                    bgColor = '#eab308'; // Amber for warning
                    break;
                case 'error':
                case 'danger':
                    icon = '';
                    bgColor = '#dc2626'; // Bright red for error/danger
                    break;
                default:
                    icon = '';
                    bgColor = '#3b82f6'; // Default blue (info)
            }

            toastEl.style.backgroundColor = bgColor;

            toastEl.innerHTML = `<span>${icon}</span><span id="toast-message">${message}</span>`;

            // Add minimal structural alignment needed for the content
            toastEl.style.display = 'flex';
            toastEl.style.alignItems = 'center';
            toastEl.style.gap = '10px';

            toastEl.style.opacity = '1';

            setTimeout(() => {
                toastEl.style.opacity = '0';
                // Hide display property completely after fade for cleanliness
                setTimeout(() => {
                    toastEl.style.display = 'none';
                }, 300);
            }, timeout);
        }

        /**
         * Check for session messages from server
         */
        function checkForSessionMessages() {
            const sessionMessages = [{
                    type: 'success',
                    message: "{{ session('success') }}"
                },
                {
                    type: 'warning',
                    message: "{{ session('warning') }}"
                }
            ];

            @if ($errors->any())
                let errorMessage = 'Validation Error: ';
                // Concatenate all errors into one string
                @foreach ($errors->all() as $error)
                    errorMessage += '{{ $error }} ';
                @endforeach
                sessionMessages.push({
                    type: 'error',
                    message: errorMessage.trim()
                });
            @endif

            sessionMessages.forEach(msg => {
                if (msg.message && msg.message.length > 0) {
                    showUniversalToast(msg.message, msg.type);
                }
            });
        }


        // ==================== BACKGROUND CLICK HANDLER ====================
        /**
         * Handle clicks outside calendar to reset filters
         */
        function initializeBackgroundClickHandler() {
            document.addEventListener('click', function(e) {
                // Check if the click was OUTSIDE the calendar area
                if (!e.target.closest('.evt-calendar-days') && !e.target.closest('.evt-mini-calendar') && !e.target
                    .closest('.evt-calendar-tooltip')) {
                    const active = daysContainer.querySelector('.evt-calendar-day.active');
                    if (active) active.classList.remove('active');
                    showAllEvents(); // Show all events for the current tab
                }
            });
        }


        // ==================== INITIALIZATION ====================
        /**
         * Initialize all functionality when DOM is loaded
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize modal lifecycle handling
            initializeEventModalLifecycle();

            // Initialize modal form validation
            setupModalFormValidation();

            // Initialize calendar functionality
            initializeTabsHorizontalScroll();
            initializeTabSwitching();
            initializeViewToggle();
            initializeCalendarNavigation();
            initializeRegistrationHandlers();
            initializeEventCardHandlers();
            initializeBackgroundClickHandler();

            // Check for notifications
            checkForSessionMessages();

            // Initial render
            window.addEventListener('resize', renderCalendar);
            renderCalendar();
            showAllEvents();
        });
    </script>

    <script>
        // Function to handle the reminder toggle logic
        function setupReminderToggle() {
            const remindMeCheckbox = document.getElementById('remind_me');
            const reminderDropdown = document.getElementById('reminder_minutes');

            if (!remindMeCheckbox || !reminderDropdown) {
                return;
            }

            // Set initial state (disabled by default since checkbox is unchecked)
            reminderDropdown.disabled = true;
            reminderDropdown.style.opacity = '0.6';
            reminderDropdown.style.cursor = 'not-allowed';

            // Add event listener to toggle checkbox
            remindMeCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    // Enable dropdown when checked
                    reminderDropdown.disabled = false;
                    reminderDropdown.style.opacity = '1';
                    reminderDropdown.style.cursor = 'pointer';

                    // Set default value if none selected
                    if (!reminderDropdown.value) {
                        reminderDropdown.value = '15';
                    }
                } else {
                    // Disable dropdown when unchecked
                    reminderDropdown.disabled = true;
                    reminderDropdown.style.opacity = '0.6';
                    reminderDropdown.style.cursor = 'not-allowed';

                    // Clear selection when disabled
                    reminderDropdown.value = '';
                }
            });

            // Handle form submission validation
            const form = document.querySelector('form[action="/submit-registration"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (remindMeCheckbox.checked && !reminderDropdown.value) {
                        e.preventDefault();
                        alert('Please select a reminder time when "Remind me before event" is enabled.');
                        reminderDropdown.focus();
                    }
                });
            }
        }

        // Use MutationObserver to detect when modal is added
        document.addEventListener('DOMContentLoaded', function() {
            const modalContainer = document.getElementById('modal-container');

            if (modalContainer) {
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                            const modal = modalContainer.querySelector('.event-modal-backdrop');
                            if (modal) {
                                setTimeout(setupReminderToggle, 50);
                            }
                        }
                    });
                });

                observer.observe(modalContainer, {
                    childList: true,
                    subtree: false
                });
            }

            // Also try to set it up immediately
            setTimeout(setupReminderToggle, 100);
        });
    </script>

    <script>
        // ==================== AJAX POLLING FOR LIVE UPDATES ====================
        let lastUpdateTime = new Date().toISOString();
        let isPolling = false;
        let pollInterval = 30000; // 30 seconds
        let userRegisteredEvents = new Set(); // Track user registrations

        /**
         * Start polling for event updates
         */
        function startEventPolling() {
            if (isPolling) return;

            console.log('Starting event polling...');
            isPolling = true;

            // Initialize userRegisteredEvents from existing cards
            initializeRegisteredEvents();

            // Initial poll
            pollForEventUpdates();

            // Set up interval
            setInterval(pollForEventUpdates, pollInterval);
        }

        /**
         * Initialize registered events from existing cards
         */
        function initializeRegisteredEvents() {
            const registeredButtons = document.querySelectorAll('.evt-register-button[disabled]');
            registeredButtons.forEach(button => {
                if (button.textContent.includes('Already Registered')) {
                    const eventCard = button.closest('.evt-event-card');
                    if (eventCard) {
                        const eventId = eventCard.dataset.eventId;
                        userRegisteredEvents.add(eventId);
                    }
                }
            });
            console.log('Initial registered events:', Array.from(userRegisteredEvents));
        }

        /**
         * Poll server for event updates
         */
        function pollForEventUpdates() {
            if (isPolling === false) return;

            fetch(`/events/updates?since=${lastUpdateTime}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.events && data.events.length > 0) {
                        console.log(`Received ${data.events.length} event update(s)`);

                        // Process each updated event
                        data.events.forEach(eventData => {
                            processEventUpdate(eventData);
                        });

                        // Update last update time
                        lastUpdateTime = data.lastUpdate;
                    }
                })
                .catch(error => {
                    console.error('Polling error:', error);
                });
        }

        /**
         * Process an individual event update
         */
        function processEventUpdate(eventData) {
            // Update registration status from server response
            if (eventData.is_registered) {
                userRegisteredEvents.add(eventData.event_id.toString());
            }

            // Find existing event card
            const existingCard = document.querySelector(`[data-event-id="${eventData.event_id}"]`);

            if (existingCard) {
                // Update existing card - preserve registration status
                updateExistingEventCard(existingCard, eventData);
            } else {
                // Check if this is a new event (created after page load)
                const eventCreatedTime = new Date(eventData.created_at);
                const pageLoadTime = new Date(window.pageLoadTime || document.querySelector('.evt-event-card')?.dataset
                    .loadTime);

                if (eventCreatedTime > pageLoadTime) {
                    // This is a new event - add it
                    addNewEventCard(eventData);
                }
            }
        }

        /**
         * Update an existing event card with new data
         */
        function updateExistingEventCard(card, eventData) {
            // Get current button to check registration status
            const currentButton = card.querySelector('.evt-register-button');
            const isCurrentlyRegistered = currentButton ?
                (currentButton.textContent.includes('Already Registered') || userRegisteredEvents.has(eventData.event_id
                    .toString())) :
                false;

            // Update basic information
            const title = card.querySelector('.evt-card-title');
            const description = card.querySelector('.evt-card-description');
            const date = card.querySelector('.evt-card-date');
            const location = card.querySelector('.evt-card-location');
            const image = card.querySelector('.evt-card-image img');

            if (title) title.textContent = eventData.title;
            if (description) description.textContent = eventData.description.substring(0, 100) +
                (eventData.description.length > 100 ? '...' : '');
            if (image) {
                image.src = resolveEventPhotoUrl(eventData.photo);
                image.alt = eventData.title;
            }

            // Update dates and status
            const now = new Date();
            const startDate = new Date(eventData.start_date);
            const endDate = new Date(eventData.end_date);
            const isOngoing = now >= startDate && now <= endDate;
            const isPast = now > endDate;
            const statusKey = getEventStatusKey(now, startDate, endDate, eventData.deadline);

            // Update data attributes
            card.dataset.eventDate = startDate.toISOString().split('T')[0];

            // Update event type
            card.dataset.eventType = getEventTypeFromStatus(statusKey);
            syncEventCardStatus(card, statusKey);

            // Update date display
            if (date) {
                const formattedStart = startDate.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                }) + ' • ' + startDate.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                let dateHtml = `<i class="ri-time-line"></i> <span>${startDate.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                })} at ${startDate.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                })}`;

                if (!isOngoing && !isPast) {
                    const formattedEnd = endDate.toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric'
                    }) + ' • ' + endDate.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    dateHtml += ` - ${endDate.toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric'
                    })} at ${endDate.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit'
                    })}`;
                }

                dateHtml += `</span>`;
                date.innerHTML = dateHtml;
            }

            // Update location
            if (location) {
                location.innerHTML = `<i class="ri-map-pin-line"></i> <span>${eventData.location || 'TBA'}</span>`;
            }

            // Update button status - with registration preservation
            updateEventButtonStatus(card, eventData, now, startDate, endDate, isCurrentlyRegistered);

            // Apply current filter
            applyCurrentFilter(card);
        }

        /**
         * Add a new event card
         */
        function addNewEventCard(eventData) {
            const eventsContainer = document.querySelector('.evts-container');
            if (!eventsContainer) return;

            // Determine status
            const now = new Date();
            const startDate = new Date(eventData.start_date);
            const endDate = new Date(eventData.end_date);
            const isOngoing = now >= startDate && now <= endDate;
            const isPast = now > endDate;
            const statusKey = getEventStatusKey(now, startDate, endDate, eventData.deadline);

            // Determine event type
            const eventType = getEventTypeFromStatus(statusKey);

            // Check registration status
            const isRegistered = eventData.is_registered || userRegisteredEvents.has(eventData.event_id.toString());

            // Format dates for display
            const formattedStart = startDate.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            }) + ' • ' + startDate.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });

            let dateDisplay = `${startDate.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            })} at ${startDate.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            })}`;
            if (!isOngoing && !isPast) {
                const formattedEnd = endDate.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                }) + ' • ' + endDate.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                dateDisplay += ` - ${endDate.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                })} at ${endDate.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                })}`;
            }

            // Create button HTML based on status AND registration
            let buttonHtml = getButtonHtmlForStatus(eventData, now, startDate, endDate, isRegistered);

            // Create the card HTML
            const cardHtml = `
        <div class="evt-event-card ${statusKey === 'upcoming' ? '' : `evt-event-card--${statusKey}`}" data-event-id="${eventData.event_id}"
             data-event-date="${startDate.toISOString().split('T')[0]}"
             data-event-type="${eventType}"
             data-load-time="${now.toISOString()}">
            <div class="evt-card-image">
                <img src="${resolveEventPhotoUrl(eventData.photo)}" alt="${eventData.title}">
                <span class="evt-card-status evt-status-chip evt-status-chip--${statusKey}">${getEventStatusLabel(statusKey)}</span>
            </div>
            <div class="evt-card-content">
                <div class="evt-card-header">
                    <h3 class="evt-card-title">${eventData.title}</h3>
                </div>
                <p class="evt-card-description">
                    ${eventData.description.substring(0, 100)}${eventData.description.length > 100 ? '...' : ''}
                </p>
                <div class="evt-card-details">
                    <span class="evt-card-date">
                        <i class="ri-time-line"></i> <span>${dateDisplay}</span>
                    </span>
                    <span class="evt-card-location">
                        <i class="ri-map-pin-line"></i> <span>${eventData.location || 'TBA'}</span>
                    </span>
                </div>
                <div class="evt-card-footer">
                    <div class="evt-card-actions">
                        ${buttonHtml}
                    </div>
                </div>
            </div>
        </div>
    `;

            // Add to container
            eventsContainer.insertAdjacentHTML('beforeend', cardHtml);

            // Get the new card and attach handlers
            const newCard = eventsContainer.querySelector(`[data-event-id="${eventData.event_id}"]`);
            if (newCard) {
                attachCardEventHandlers(newCard);
                applyCurrentFilter(newCard);

                // Animate the new card
                newCard.style.opacity = '0';
                newCard.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    newCard.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    newCard.style.opacity = '1';
                    newCard.style.transform = 'translateY(0)';
                }, 10);
            }

            // Update calendar data
            updateCalendarData(eventData, 'add');
        }

        /**
         * Update event button status - with registration preservation
         */
        function updateEventButtonStatus(card, eventData, now, startDate, endDate, isCurrentlyRegistered = false) {
            const buttonContainer = card.querySelector('.evt-card-actions');
            if (!buttonContainer) return;

            const statusKey = getEventStatusKey(now, startDate, endDate, eventData.deadline);

            // Use provided registration status or check our local set
            const isRegistered = isCurrentlyRegistered || userRegisteredEvents.has(eventData.event_id.toString());

            const newButtonHtml = getButtonHtmlForStatusKey(statusKey, isRegistered);

            // Get current button to compare
            const currentButton = buttonContainer.querySelector('.evt-register-button');
            const currentHtml = currentButton ? currentButton.outerHTML : '';

            // Only update if button has actually changed
            if (currentHtml.trim() !== newButtonHtml.trim()) {
                buttonContainer.innerHTML = newButtonHtml;

                // Re-attach event handler if it's a register button
                const newButton = buttonContainer.querySelector('.evt-register-button');
                if (newButton && !newButton.disabled && newButton.tagName === 'BUTTON') {
                    attachRegistrationHandler(newButton);
                }
            }
        }

        /**
         * Get button HTML for status with registration check
         */
        function getButtonHtmlForStatus(eventData, now, startDate, endDate, isRegistered = false) {
            return getButtonHtmlForStatusKey(getEventStatusKey(now, startDate, endDate, eventData.deadline), isRegistered);
        }

        /**
         * Attach card event handlers
         */
        function attachCardEventHandlers(card) {
            // Click handler for modal
            card.style.cursor = 'pointer';
            card.addEventListener('click', function(e) {
                if (e.target.closest('.evt-register-button')) return;
                const eventId = this.dataset.eventId;
                showEventModal(eventId);
            });

            // Attach registration handler
            const registerButton = card.querySelector('.evt-register-button:not([disabled])');
            if (registerButton && registerButton.tagName === 'BUTTON') {
                attachRegistrationHandler(registerButton);
            }
        }

        /**
         * Attach registration handler - also updates registration tracking
         */
        function attachRegistrationHandler(button) {
            button.addEventListener('click', function(e) {
                e.stopPropagation();

                const originalButtonHtml = button.innerHTML;
                const eventCard = button.closest('.evt-event-card');
                const eventId = eventCard.dataset.eventId;

                button.disabled = true;
                button.classList.add('loading');
                button.innerHTML = '<span class="spinner"></span> Loading...';

                fetch(`/event-register/${eventId}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.text();
                    })
                    .then(data => {
                        const container = document.getElementById('modal-container');
                        container.innerHTML = data;
                        syncEventModalState();

                        // Update registration tracking
                        userRegisteredEvents.add(eventId);
                        console.log('Added to registered events:', eventId);

                        // Update button to show registered status after modal closes
                        const observer = new MutationObserver(function() {
                            if (container.innerHTML === '') {
                                // Modal closed, update button
                                const updatedButton = eventCard.querySelector('.evt-register-button');
                                if (updatedButton) {
                                    updatedButton.outerHTML =
                                        `<button class="evt-register-button evt-register-button--registered" disabled>Already Registered</button>`;
                                }
                                observer.disconnect();
                            }
                        });
                        observer.observe(container, {
                            childList: true
                        });

                        // Re-attach modal close handlers
                        const closeBtn = container.querySelector('.event-modal-close');
                        const cancelBtn = container.querySelector('.btn-cancel');
                        if (closeBtn || cancelBtn) {
                            [closeBtn, cancelBtn].forEach(btn => {
                                if (btn) btn.addEventListener('click', () => {
                                    closeRegistrationModal();
                                });
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Registration fetch error:', error);
                        button.disabled = false;
                        button.classList.remove('loading');
                        button.innerHTML = originalButtonHtml;
                    });
            });
        }

        /**
         * Apply current filter to card
         */
        function applyCurrentFilter(card) {
            const activeTab = document.querySelector('.evt-tab-button.evt-active')?.textContent?.trim();
            const eventType = card.dataset.eventType;
            let showCard = true;

            switch (activeTab) {
                case 'Ongoing Events':
                    showCard = (eventType === 'ongoing');
                    break;
                case 'Past Events':
                    showCard = (eventType === 'past');
                    break;
                    // 'All Events' shows everything
            }

            card.style.display = showCard ? 'flex' : 'none';

            // Update empty states
            updateEmptyStates();
        }

        /**
         * Update calendar data
         */
        function updateCalendarData(eventData, action) {
            // Update the global calendarEventsAll array
            if (window.calendarEventsAll) {
                if (action === 'add') {
                    // Add new event to calendar
                    window.calendarEventsAll.push({
                        id: eventData.event_id,
                        title: eventData.title,
                        description: eventData.description,
                        photo: eventData.photo,
                        start_date: eventData.start_date,
                        end_date: eventData.end_date
                    });
                } else if (action === 'update') {
                    // Update existing event in calendar
                    const index = window.calendarEventsAll.findIndex(e => e.id == eventData.event_id);
                    if (index !== -1) {
                        window.calendarEventsAll[index] = {
                            id: eventData.event_id,
                            title: eventData.title,
                            description: eventData.description,
                            photo: eventData.photo,
                            start_date: eventData.start_date,
                            end_date: eventData.end_date
                        };
                    }
                }

                // Rebuild event lookups
                window.eventDaysAll = buildEventDayLookup(window.calendarEventsAll, 'all');
                window.eventDaysOngoing = buildEventDayLookup(window.calendarEventsAll, 'ongoing');
                window.eventDaysPast = buildEventDayLookup(window.calendarEventsAll, 'past');

                // Re-render calendar if it's visible
                if (document.querySelector('.evt-mini-calendar').offsetParent !== null) {
                    renderCalendar();
                }
            }
        }

        /**
         * Update empty states
         */
        function updateEmptyStates() {
            const activeTab = document.querySelector('.evt-tab-button.evt-active')?.textContent?.trim();
            const eventCards = document.querySelectorAll('.evt-event-card');

            // Hide all empty states
            document.querySelectorAll('.evt-empty-state').forEach(state => state.style.display = 'none');

            let hasVisibleCards = false;
            let hasOngoingCards = false;
            let hasPastCards = false;

            eventCards.forEach(card => {
                if (card.style.display !== 'none') {
                    hasVisibleCards = true;
                    const eventType = card.dataset.eventType;
                    if (eventType === 'ongoing') hasOngoingCards = true;
                    if (eventType === 'past') hasPastCards = true;
                }
            });

            // Show appropriate empty state
            if (!hasVisibleCards) {
                if (activeTab === 'All Events') {
                    document.getElementById('no-all-events').style.display = 'block';
                } else if (activeTab === 'Ongoing Events') {
                    document.getElementById('no-ongoing-events').style.display = 'block';
                } else if (activeTab === 'Past Events') {
                    document.getElementById('no-past-events').style.display = 'block';
                }
            }
        }

        // ==================== INITIALIZE POLLING ====================
        document.addEventListener('DOMContentLoaded', function() {
            // Store page load time
            window.pageLoadTime = new Date().toISOString();

            // Start polling after a short delay (let the page load first)
            setTimeout(() => {
                startEventPolling();
            }, 5000); // Start after 5 seconds

            // Also poll when user returns to tab
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden && isPolling) {
                    // User returned to tab, poll immediately
                    pollForEventUpdates();
                }
            });
        });
    </script>
</body>

</html>
