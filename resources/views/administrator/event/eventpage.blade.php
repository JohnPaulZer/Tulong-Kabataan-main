<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tulong Kabataan | Administrator Dashboard</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    <!-- Remixicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <!-- Charts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCxE6u2I1N_uFuYp8hZH2OSh_VEPo1N85M&libraries=places">
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('administrator.partials.admin-theme')
</head>

<body class="admin-page admin-event-page">
    @include('administrator.partials.loading-screen')
    @include('partials.universalmodal')
    @include('administrator.event.partials.eventmodal')

    <!-- Header -->
    <header class="header">
        <div class="header__inner">
            <div class="header__left">
                <button id="sidebarToggle" class="menu-btn" aria-label="Toggle menu">
                    <i class="ri-menu-line"></i>
                </button>
                <h1 class="brand">ADMINISTRATOR</h1>
            </div>

            <div class="logo-word"><img src="{{ asset('img/log.png') }}" alt=""
                    style="width: 120px; height: 60px; margin-top: 8px;">
            </div>

            <button class="notif" aria-label="Notifications">
                <i class="ri-notification-3-line"></i>
            </button>
        </div>
    </header>

    <!-- Sidebar -->
    @include('administrator.partials.main-sidebar')

    <!-- Overlay (mobile) -->
    <div id="sidebarOverlay" class="overlay" aria-hidden="true"></div>

    @php
        $oldRoles = old('roles', [['name' => '', 'description' => '']]);
        if (!is_array($oldRoles) || count($oldRoles) === 0) {
            $oldRoles = [['name' => '', 'description' => '']];
        }
    @endphp

    <div id="createEventModal" class="event-create-modal" aria-hidden="true" hidden>
        <button type="button" class="event-create-modal__backdrop" data-create-event-close
            aria-label="Close create event modal"></button>

        <section class="event-create-modal__dialog" role="dialog" aria-modal="true"
            aria-labelledby="createEventModalTitle">
            <header class="event-create-modal__header">
                <div>
                    <p class="event-create-modal__eyebrow">Administrator</p>
                    <h2 id="createEventModalTitle">Create Event</h2>
                    <p>Add event details one step at a time.</p>
                </div>
                <button type="button" class="event-create-modal__close" data-create-event-close
                    aria-label="Close create event modal">
                    <i class="ri-close-line"></i>
                </button>
            </header>

            <form id="createEventStepperForm" action="{{ route('submitevent') }}" method="POST"
                enctype="multipart/form-data" class="event-stepper-form">
                @csrf

                @if ($errors->any())
                    <div class="event-create-errors">
                        <strong>Please review the highlighted fields.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <nav class="event-stepper" aria-label="Create event steps">
                    <button type="button" class="event-stepper__item is-active" data-step-trigger="0"
                        aria-current="step">
                        <span class="event-stepper__circle">1</span>
                        <span class="event-stepper__label">Basic Info</span>
                    </button>
                    <button type="button" class="event-stepper__item" data-step-trigger="1">
                        <span class="event-stepper__circle">2</span>
                        <span class="event-stepper__label">Schedule</span>
                    </button>
                    <button type="button" class="event-stepper__item" data-step-trigger="2">
                        <span class="event-stepper__circle">3</span>
                        <span class="event-stepper__label">Roles</span>
                    </button>
                    <button type="button" class="event-stepper__item" data-step-trigger="3">
                        <span class="event-stepper__circle">4</span>
                        <span class="event-stepper__label">Contact</span>
                    </button>
                </nav>

                <div class="event-stepper__body">
                    <section class="event-step is-active" data-step="0">
                        <div class="event-step__heading">
                            <h3>Basic Information</h3>
                            <p>Name the event and add the main description.</p>
                        </div>

                        <div class="event-form-grid event-form-grid--single">
                            <div class="event-field">
                                <label for="create_event_title">Event Title <span>*</span></label>
                                <input type="text" id="create_event_title" name="title"
                                    value="{{ old('title') }}" placeholder="e.g., Typhoon Relief Operation" required>
                            </div>

                            <div class="event-field">
                                <label for="create_event_description">Event Description <span>*</span></label>
                                <textarea id="create_event_description" name="description" rows="4"
                                    placeholder="Describe the purpose, activities, and goals." required>{{ old('description') }}</textarea>
                            </div>

                            <div class="event-field">
                                <label for="create_event_photo">Cover Photo</label>
                                <input type="file" id="create_event_photo" name="photo"
                                    accept="image/png, image/jpeg">
                                <small>Accepted: JPG or PNG. Max size: 5MB.</small>
                            </div>

                            <div class="event-photo-preview" id="createEventPhotoPreview" hidden>
                                <div class="event-photo-preview__image">
                                    <img id="createEventPhotoPreviewImage" alt="Cover photo preview">
                                </div>
                                <div class="event-photo-preview__details">
                                    <strong id="createEventPhotoPreviewName">Selected cover photo</strong>
                                    <span id="createEventPhotoPreviewMeta"></span>
                                    <button type="button" id="createEventRemovePhoto">Remove photo</button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="event-step" data-step="1" hidden>
                        <div class="event-step__heading">
                            <h3>Schedule & Location</h3>
                            <p>Set the date range, registration deadline, and map location.</p>
                        </div>

                        <div class="event-form-grid">
                            <div class="event-field">
                                <label for="create_event_start">Start Date & Time <span>*</span></label>
                                <input type="datetime-local" id="create_event_start" name="start_date"
                                    value="{{ old('start_date') }}" required>
                            </div>
                            <div class="event-field">
                                <label for="create_event_end">End Date & Time <span>*</span></label>
                                <input type="datetime-local" id="create_event_end" name="end_date"
                                    value="{{ old('end_date') }}" required>
                            </div>
                            <div class="event-field">
                                <label for="create_event_deadline">Registration Deadline <span>*</span></label>
                                <input type="datetime-local" id="create_event_deadline" name="deadline"
                                    value="{{ old('deadline') }}" required>
                                <small>Volunteers cannot register after this date.</small>
                            </div>
                        </div>

                        <div class="event-field event-location-field">
                            <label for="create_event_location_search">Event Location <span>*</span></label>
                            <div class="event-location-search">
                                <i class="ri-search-line"></i>
                                <input type="text" id="create_event_location_search"
                                    placeholder="Search for a venue, barangay, or landmark">
                                <button type="button" id="createEventSearchBtn">Find</button>
                            </div>

                            <div id="createEventMap" class="event-create-map">
                                <div id="createEventMapLoading" class="event-create-map__loading">
                                    <i class="ri-loader-4-line"></i>
                                    <span>Loading map...</span>
                                </div>
                            </div>

                            <div class="event-selected-location">
                                <i class="ri-map-pin-line"></i>
                                <div>
                                    <strong>Selected Location</strong>
                                    <span id="createEventSelectedLocation">
                                        {{ old('location', 'No location selected') }}
                                    </span>
                                </div>
                            </div>

                            <input type="hidden" id="create_event_location" name="location"
                                value="{{ old('location') }}" required>
                            <input type="hidden" id="create_event_lat" name="lat" value="{{ old('lat') }}"
                                required>
                            <input type="hidden" id="create_event_lng" name="lng" value="{{ old('lng') }}"
                                required>
                        </div>
                    </section>

                    <section class="event-step" data-step="2" hidden>
                        <div class="event-step__heading">
                            <h3>Volunteer Roles</h3>
                            <p>Add the roles volunteers can choose from.</p>
                        </div>

                        <div id="createEventRolesContainer" class="event-roles-list">
                            @foreach ($oldRoles as $index => $role)
                                <div class="event-role-item">
                                    <div class="event-role-item__header">
                                        <h4>Role #{{ $index + 1 }}</h4>
                                        @if ($index > 0)
                                            <button type="button" class="event-role-remove">Remove</button>
                                        @endif
                                    </div>
                                    <div class="event-form-grid">
                                        <div class="event-field">
                                            <label>Role Name <span>*</span></label>
                                            <input type="text" name="roles[{{ $index }}][name]"
                                                value="{{ $role['name'] ?? '' }}" placeholder="e.g., Medical Team"
                                                required>
                                        </div>
                                        <div class="event-field">
                                            <label>Description <span>*</span></label>
                                            <textarea name="roles[{{ $index }}][description]" rows="2" placeholder="Responsibilities" required>{{ $role['description'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="event-add-role" id="createEventAddRole">
                            <i class="ri-add-line"></i>
                            Add Another Role
                        </button>
                    </section>

                    <section class="event-step" data-step="3" hidden>
                        <div class="event-step__heading">
                            <h3>Coordinator Details</h3>
                            <p>Add the person volunteers can contact about this event.</p>
                        </div>

                        <div class="event-form-grid">
                            <div class="event-field">
                                <label for="create_event_coordinator_name">Full Name <span>*</span></label>
                                <input type="text" id="create_event_coordinator_name" name="coordinator_name"
                                    value="{{ old('coordinator_name') }}" placeholder="Juan Dela Cruz" required>
                            </div>
                            <div class="event-field">
                                <label for="create_event_coordinator_email">Email Address <span>*</span></label>
                                <input type="email" id="create_event_coordinator_email" name="coordinator_email"
                                    value="{{ old('coordinator_email') }}" placeholder="juan@example.com" required>
                            </div>
                            <div class="event-field">
                                <label for="create_event_coordinator_phone">Phone Number <span>*</span></label>
                                <input type="text" id="create_event_coordinator_phone" name="coordinator_phone"
                                    value="{{ old('coordinator_phone') }}" placeholder="0912 345 6789" required>
                            </div>
                        </div>
                    </section>
                </div>

                <footer class="event-stepper__footer">
                    <button type="button" class="event-stepper__secondary" id="createEventPrevStep">
                        Previous
                    </button>
                    <div>
                        <button type="button" class="event-stepper__secondary" data-create-event-close>
                            Cancel
                        </button>
                        <button type="button" class="event-stepper__primary" id="createEventNextStep">
                            Next
                        </button>
                        <button type="submit" class="event-stepper__primary" id="createEventSubmit" hidden>
                            Create Event
                        </button>
                    </div>
                </footer>
            </form>
        </section>
    </div>

    <!-- Main -->
    <main class="main" role="main">
        <div class="content">

            <!-- Topbar -->
            <div class="topbar">
                <section class="page-header admin-page-heading">
                    <h1>Events</h1>
                    <p>Manage event listings, attendance, and volunteer participation.</p>
                </section>
                <div style="display:flex;gap:8px;align-items:center">
                    <button type="button" class="create-btn" id="openCreateEventModal">
                        <i class="ri-add-line"></i> Create Event
                    </button>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs" role="tablist" aria-label="Main tabs">
                <button class="tab" data-target="managePanel" role="tab" aria-selected="false">Manage
                    Events</button>
                <button class="tab" data-target="volunteersPanel" role="tab" aria-selected="false">Volunteer
                    Management</button>
            </div>



            <!-- Manage Events -->
            <section id="managePanel" class="tab-panel" aria-hidden="true" hidden>
                <!-- Event participation manager STATISTICS -->
                <main class="container">
                    <section class="summary-grid" id="eventStats">
                        <article class="summary-card">
                            <div class="card-head">
                                <div class="card-icon blue"><i class="ri-calendar-check-line"></i></div>
                                <div class="chip blue">Available</div>
                            </div>
                            <h4 class="card-label">Available Events</h4>
                            <div class="card-value" id="availableCount">0</div>
                        </article>

                        <article class="summary-card">
                            <div class="card-head">
                                <div class="card-icon green"><i class="ri-play-circle-line"></i></div>
                                <div class="chip green">Ongoing</div>
                            </div>
                            <h4 class="card-label">Ongoing Events</h4>
                            <div class="card-value" id="ongoingCount">0</div>
                        </article>

                        <article class="summary-card">
                            <div class="card-head">
                                <div class="card-icon red"><i class="ri-close-circle-line"></i></div>
                                <div class="chip red">Ended</div>
                            </div>
                            <h4 class="card-label">Ended Events</h4>
                            <div class="card-value" id="endedCount">0</div>
                        </article>
                    </section>

                    <section class="panel">
                        <div class="panel-header">
                            <h2>Event Participation Management</h2>
                            <div class="filters" role="tablist" aria-label="Event filters">
                                <button id="filterAvailable" class="filter-btn active">Available</button>
                                <button id="filterOngoing" class="filter-btn">Ongoing</button>
                                <button id="filterEnded" class="filter-btn">Ended</button>
                            </div>
                        </div>
                    </section>
                    @include('administrator.event.partials.event_list')
                    <div id="eventFilterEmpty" hidden>
                        @include('administrator.partials.empty-state', [
                            'icon' => 'ri-calendar-event-line',
                            'title' => 'No Events Found',
                            'message' => 'There are no events to display for this status.',
                        ])
                    </div>

                </main>
            </section>


            <!-- PANEL: Volunteers (placeholder) -->
            <section id="volunteersPanel" class="tab-panel" aria-hidden="true" hidden>
                <main>
                    <!-- Charts -->
                    <section class="charts-row">
                        <div class="card">
                            <div class="card-head"
                                style="display:flex;justify-content:space-between;align-items:center;">
                                <h3 style="margin:0;">Volunteer Participation</h3>
                                <select id="eventDropdown" class="dropdown"
                                    style="
                            padding:6px 10px;
                            border-radius:6px;
                            border:1px solid #ccc;
                            background:#fff;
                            font-size:14px;
                            color:#374151;">
                                    <option value="all" selected>All Events</option>
                                    {{-- Laravel: Dynamically add events --}}
                                    @foreach ($events as $event)
                                        <option value="{{ $event->event_id }}">{{ $event->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="volunteerParticipationChart" class="chart" role="img"
                                aria-label="Volunteer participation chart" style="height:350px;"></div>
                        </div>
                    </section>



                    <!-- Filter Controls -->
                    <div class="card" style="margin-bottom:12px">
                        <div class="controls">
                            <h3 style="margin:0">Volunteer Management</h3>
                            <div>

                            </div>
                        </div>
                    </div>

                    <!-- Volunteer list -->
                    @include('administrator.event.partials.volunteer_list')

                </main>
            </section>



            {{-- FILTER EVENTS --}}
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const filterButtons = document.querySelectorAll('.filters .filter-btn');

                    // Store current filter
                    let currentFilter = localStorage.getItem('activeEventFilter') || 'available';

                    // Filter function
                    function filterEvents() {
                        const events = document.querySelectorAll('.event-item');
                        const emptyState = document.getElementById('eventFilterEmpty');
                        let visibleCount = 0;

                        events.forEach(event => {
                            const statusText = event.querySelector('.status-badge')?.textContent?.toLowerCase() ||
                                '';

                            let status = '';
                            if (statusText.includes('available')) status = 'available';
                            else if (statusText.includes('ongoing')) status = 'ongoing';
                            else if (statusText.includes('ended')) status = 'ended';

                            event.style.display = (currentFilter === 'all' || status === currentFilter) ?
                                '' : 'none';

                            if (event.style.display !== 'none') {
                                visibleCount++;
                            }
                        });

                        if (emptyState) {
                            emptyState.hidden = events.length === 0 || visibleCount > 0;
                        }
                    }

                    // Button clicks
                    filterButtons.forEach(btn => {
                        btn.addEventListener('click', () => {
                            const status = btn.id.replace('filter', '').toLowerCase();
                            currentFilter = status;
                            localStorage.setItem('activeEventFilter', status);

                            // Update buttons
                            filterButtons.forEach(b => b.classList.remove('active'));
                            btn.classList.add('active');

                            // Filter immediately
                            filterEvents();
                        });
                    });

                    // Set initial button
                    const activeBtn = document.getElementById(
                        `filter${currentFilter.charAt(0).toUpperCase() + currentFilter.slice(1)}`);
                    if (activeBtn) {
                        filterButtons.forEach(btn => btn.classList.remove('active'));
                        activeBtn.classList.add('active');
                        filterEvents();
                    }

                    // Hook into AJAX - just reapply filter after updates
                    const observer = new MutationObserver(() => {
                        filterEvents();
                    });

                    const eventsList = document.getElementById('eventsList');
                    if (eventsList) {
                        observer.observe(eventsList, {
                            childList: true,
                            subtree: true
                        });
                    }

                    // Also reapply filter every second as safety
                    setInterval(filterEvents, 1000);
                });
            </script>


            {{-- CHART Volunteer SCRIPT --}}
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const dom = document.getElementById('volunteerParticipationChart');
                    const dropdown = document.getElementById('eventDropdown');
                    if (!dom) return;

                    const myChart = echarts.init(dom);


                    function loadChartData(eventId = 'all') {
                        fetch(`{{ route('admin.volunteer.data') }}?event_id=${eventId}`)
                            .then(response => response.json())
                            .then(data => {
                                const option = {
                                    title: {
                                        text: data.eventTitle ?
                                            `Volunteer Participation — ${data.eventTitle}` :
                                            'Volunteer Participation per Event',
                                        left: 'center',
                                        textStyle: {
                                            color: '#333',
                                            fontSize: 16
                                        }
                                    },
                                    tooltip: {
                                        trigger: 'axis',
                                        backgroundColor: 'rgba(255,255,255,0.9)',
                                        textStyle: {
                                            color: '#1f2937'
                                        }
                                    },
                                    grid: {
                                        top: 60,
                                        right: 30,
                                        bottom: 50,
                                        left: 60
                                    },
                                    xAxis: {
                                        type: 'category',
                                        data: data.labels,
                                        axisLine: {
                                            lineStyle: {
                                                color: '#ccc'
                                            }
                                        },
                                        axisTick: {
                                            show: false
                                        },
                                        axisLabel: {
                                            color: '#555'
                                        }
                                    },
                                    yAxis: {
                                        type: 'value',
                                        name: 'No. of Volunteers',
                                        nameTextStyle: {
                                            color: '#555'
                                        },
                                        axisLine: {
                                            show: false
                                        },
                                        axisTick: {
                                            show: false
                                        },
                                        splitLine: {
                                            lineStyle: {
                                                color: '#eee'
                                            }
                                        }
                                    },
                                    series: [{
                                        name: 'Volunteers',
                                        type: 'bar',
                                        data: data.values,
                                        barWidth: '50%',
                                        itemStyle: {
                                            color: 'rgba(87,181,231,0.8)',
                                            borderRadius: [6, 6, 0, 0]
                                        },
                                        emphasis: {
                                            itemStyle: {
                                                color: 'rgba(87,181,231,1)'
                                            }
                                        }
                                    }]
                                };
                                myChart.setOption(option, true);
                            })
                            .catch(err => console.error('Error loading data:', err));
                    }


                    loadChartData();


                    dropdown.addEventListener('change', () => {
                        const selectedEvent = dropdown.value;
                        loadChartData(selectedEvent);
                    });


                    setInterval(() => {
                        const selectedEvent = dropdown.value;
                        loadChartData(selectedEvent);
                    }, 10000);

                    window.addEventListener('resize', () => myChart.resize());
                });
            </script>


            {{-- CREATE EVENT MODAL STEPPER --}}
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const modal = document.getElementById('createEventModal');
                    const openBtn = document.getElementById('openCreateEventModal');
                    const closeBtns = document.querySelectorAll('[data-create-event-close]');
                    const form = document.getElementById('createEventStepperForm');
                    const steps = Array.from(document.querySelectorAll('.event-step'));
                    const stepButtons = Array.from(document.querySelectorAll('[data-step-trigger]'));
                    const prevBtn = document.getElementById('createEventPrevStep');
                    const nextBtn = document.getElementById('createEventNextStep');
                    const submitBtn = document.getElementById('createEventSubmit');
                    const rolesContainer = document.getElementById('createEventRolesContainer');
                    const addRoleBtn = document.getElementById('createEventAddRole');
                    const photoInput = document.getElementById('create_event_photo');
                    const photoPreview = document.getElementById('createEventPhotoPreview');
                    const photoPreviewImage = document.getElementById('createEventPhotoPreviewImage');
                    const photoPreviewName = document.getElementById('createEventPhotoPreviewName');
                    const photoPreviewMeta = document.getElementById('createEventPhotoPreviewMeta');
                    const removePhotoBtn = document.getElementById('createEventRemovePhoto');
                    let currentStep = 0;
                    let photoPreviewUrl = null;
                    let eventMap;
                    let eventMarker;
                    let eventGeocoder;
                    let eventAutocomplete;
                    let mapInitialized = false;

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

                        if (currentStep === 1) {
                            window.setTimeout(initCreateEventMap, 80);
                        }
                    }

                    function validateStep(index = currentStep) {
                        const step = steps[index];
                        const fields = Array.from(step.querySelectorAll('input:not([type="hidden"]), textarea, select'));

                        for (const field of fields) {
                            if (!field.checkValidity()) {
                                field.reportValidity();
                                return false;
                            }
                        }

                        if (index === 1) {
                            const location = document.getElementById('create_event_location');
                            const search = document.getElementById('create_event_location_search');
                            if (!location.value.trim()) {
                                search.setCustomValidity('Select a location from the map or search results.');
                                search.reportValidity();
                                search.addEventListener('input', () => search.setCustomValidity(''), {
                                    once: true
                                });
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
                        window.setTimeout(() => {
                            document.getElementById('create_event_title')?.focus();
                        }, 80);
                    }

                    function closeModal() {
                        modal.hidden = true;
                        modal.setAttribute('aria-hidden', 'true');
                        document.body.style.overflow = '';
                        openBtn?.focus();
                    }

                    function formatFileSize(bytes) {
                        if (!bytes) return '0 KB';
                        if (bytes < 1024 * 1024) return `${Math.round(bytes / 1024)} KB`;
                        return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
                    }

                    function clearPhotoPreview() {
                        if (photoPreviewUrl) {
                            URL.revokeObjectURL(photoPreviewUrl);
                            photoPreviewUrl = null;
                        }

                        if (photoPreview) {
                            photoPreview.hidden = true;
                        }

                        if (photoPreviewImage) {
                            photoPreviewImage.removeAttribute('src');
                        }

                        if (photoPreviewName) {
                            photoPreviewName.textContent = 'Selected cover photo';
                        }

                        if (photoPreviewMeta) {
                            photoPreviewMeta.textContent = '';
                        }
                    }

                    function updatePhotoPreview() {
                        const file = photoInput?.files?.[0];
                        clearPhotoPreview();

                        if (!file || !file.type.startsWith('image/')) {
                            return;
                        }

                        photoPreviewUrl = URL.createObjectURL(file);
                        photoPreviewImage.src = photoPreviewUrl;
                        photoPreviewName.textContent = file.name;
                        photoPreviewMeta.textContent = formatFileSize(file.size);
                        photoPreview.hidden = false;
                    }

                    function placeCreateEventMarker(location) {
                        if (!eventMap) return;
                        if (eventMarker) {
                            eventMarker.setMap(null);
                        }

                        eventMarker = new google.maps.Marker({
                            position: location,
                            map: eventMap,
                            draggable: true
                        });

                        eventMarker.addListener('dragend', () => {
                            reverseGeocodeCreateEvent(eventMarker.getPosition());
                        });
                    }

                    function updateCreateEventLocation(address, latLng) {
                        document.getElementById('createEventSelectedLocation').textContent = address;
                        document.getElementById('create_event_location').value = address;
                        document.getElementById('create_event_lat').value = latLng.lat();
                        document.getElementById('create_event_lng').value = latLng.lng();
                        document.getElementById('create_event_location_search').setCustomValidity('');
                    }

                    function reverseGeocodeCreateEvent(latLng) {
                        if (!eventGeocoder) return;
                        eventGeocoder.geocode({
                            location: latLng
                        }, (results, status) => {
                            if (status === 'OK' && results[0]) {
                                updateCreateEventLocation(results[0].formatted_address, latLng);
                            }
                        });
                    }

                    function geocodeCreateEventAddress(address) {
                        if (!eventGeocoder || !address) return;
                        eventGeocoder.geocode({
                            address
                        }, (results, status) => {
                            if (status === 'OK' && results[0]) {
                                const location = results[0].geometry.location;
                                eventMap.setCenter(location);
                                eventMap.setZoom(17);
                                placeCreateEventMarker(location);
                                updateCreateEventLocation(results[0].formatted_address, location);
                            }
                        });
                    }

                    function initCreateEventMap() {
                        if (mapInitialized || typeof google === 'undefined') return;
                        mapInitialized = true;

                        const mapElement = document.getElementById('createEventMap');
                        const loadingElement = document.getElementById('createEventMapLoading');
                        const latField = document.getElementById('create_event_lat');
                        const lngField = document.getElementById('create_event_lng');
                        const oldLat = Number(latField.value);
                        const oldLng = Number(lngField.value);
                        const hasOldCoords = Number.isFinite(oldLat) && Number.isFinite(oldLng) && latField.value &&
                            lngField.value;
                        const defaultLocation = hasOldCoords ? {
                            lat: oldLat,
                            lng: oldLng
                        } : {
                            lat: 12.8797,
                            lng: 121.7740
                        };

                        if (loadingElement) {
                            loadingElement.style.display = 'none';
                        }

                        eventMap = new google.maps.Map(mapElement, {
                            center: defaultLocation,
                            zoom: hasOldCoords ? 16 : 6,
                            mapTypeControl: false,
                            streetViewControl: false,
                            fullscreenControl: true
                        });

                        eventGeocoder = new google.maps.Geocoder();

                        if (hasOldCoords) {
                            placeCreateEventMarker(defaultLocation);
                        } else if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(
                                (position) => {
                                    const userLocation = {
                                        lat: position.coords.latitude,
                                        lng: position.coords.longitude
                                    };
                                    eventMap.setCenter(userLocation);
                                    eventMap.setZoom(14);
                                    placeCreateEventMarker(userLocation);
                                    reverseGeocodeCreateEvent(userLocation);
                                },
                                () => {},
                                {
                                    timeout: 5000,
                                    maximumAge: 600000,
                                    enableHighAccuracy: true
                                }
                            );
                        }

                        eventMap.addListener('click', (event) => {
                            placeCreateEventMarker(event.latLng);
                            reverseGeocodeCreateEvent(event.latLng);
                        });

                        const searchInput = document.getElementById('create_event_location_search');
                        eventAutocomplete = new google.maps.places.Autocomplete(searchInput, {
                            componentRestrictions: {
                                country: 'ph'
                            },
                            fields: ['formatted_address', 'geometry']
                        });

                        eventAutocomplete.addListener('place_changed', () => {
                            const place = eventAutocomplete.getPlace();
                            if (place.geometry) {
                                eventMap.setCenter(place.geometry.location);
                                eventMap.setZoom(17);
                                placeCreateEventMarker(place.geometry.location);
                                updateCreateEventLocation(place.formatted_address, place.geometry.location);
                            }
                        });

                        document.getElementById('createEventSearchBtn')?.addEventListener('click', () => {
                            geocodeCreateEventAddress(searchInput.value.trim());
                        });
                    }

                    function reindexRoles() {
                        rolesContainer.querySelectorAll('.event-role-item').forEach((role, index) => {
                            role.querySelector('h4').textContent = `Role #${index + 1}`;
                            const name = role.querySelector('input');
                            const description = role.querySelector('textarea');
                            if (name) name.name = `roles[${index}][name]`;
                            if (description) description.name = `roles[${index}][description]`;

                            const remove = role.querySelector('.event-role-remove');
                            if (remove) {
                                remove.hidden = index === 0;
                            }
                        });
                    }

                    function addRole() {
                        const index = rolesContainer.querySelectorAll('.event-role-item').length;
                        const wrapper = document.createElement('div');
                        wrapper.className = 'event-role-item';
                        wrapper.innerHTML = `
                            <div class="event-role-item__header">
                                <h4>Role #${index + 1}</h4>
                                <button type="button" class="event-role-remove">Remove</button>
                            </div>
                            <div class="event-form-grid">
                                <div class="event-field">
                                    <label>Role Name <span>*</span></label>
                                    <input type="text" name="roles[${index}][name]" placeholder="e.g., Logistics" required>
                                </div>
                                <div class="event-field">
                                    <label>Description <span>*</span></label>
                                    <textarea name="roles[${index}][description]" rows="2" placeholder="Responsibilities" required></textarea>
                                </div>
                            </div>
                        `;
                        rolesContainer.appendChild(wrapper);
                        reindexRoles();
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
                            const target = Number(button.dataset.stepTrigger);
                            if (target <= currentStep) {
                                setStep(target);
                                return;
                            }

                            if (validateStep()) {
                                setStep(Math.min(target, currentStep + 1));
                            }
                        });
                    });

                    photoInput?.addEventListener('change', updatePhotoPreview);
                    removePhotoBtn?.addEventListener('click', () => {
                        if (photoInput) {
                            photoInput.value = '';
                        }
                        clearPhotoPreview();
                        photoInput?.focus();
                    });

                    addRoleBtn?.addEventListener('click', addRole);
                    rolesContainer?.addEventListener('click', (event) => {
                        if (event.target.closest('.event-role-remove')) {
                            event.target.closest('.event-role-item')?.remove();
                            reindexRoles();
                        }
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

                    reindexRoles();
                    setStep(0, {
                        skipAnimation: true
                    });

                    @if ($errors->any())
                        openModal();
                    @endif
                });
            </script>


            <!-- Manage events -->
            <script>
                // Sidebar toggle & overlay
                (function() {
                    const toggle = document.getElementById('sidebarToggle');
                    const sidebar = document.getElementById('sidebar');
                    const overlay = document.getElementById('sidebarOverlay');
                    const DESKTOP_BP = 1024;

                    if (!toggle || !sidebar || !overlay) return;

                    function isDesktop() {
                        return window.innerWidth >= DESKTOP_BP;
                    }

                    function openSidebar() {
                        sidebar.classList.add('open');
                        overlay.classList.add('show');
                        sidebar.setAttribute('aria-hidden', 'false');
                        overlay.setAttribute('aria-hidden', 'false');
                        document.body.style.overflow = 'hidden';
                    }

                    function closeSidebar() {
                        sidebar.classList.remove('open');
                        overlay.classList.remove('show');
                        sidebar.setAttribute('aria-hidden', 'true');
                        overlay.setAttribute('aria-hidden', 'true');
                        document.body.style.overflow = '';
                    }

                    toggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (isDesktop()) return; // desktop shows sidebar by CSS
                        if (sidebar.classList.contains('open')) closeSidebar();
                        else openSidebar();
                    });
                    overlay.addEventListener('click', function() {
                        closeSidebar();
                    });
                    window.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') closeSidebar();
                    });
                    window.addEventListener('resize', function() {
                        if (isDesktop()) {
                            closeSidebar();
                            sidebar.setAttribute('aria-hidden', 'false');
                        } else {
                            sidebar.setAttribute('aria-hidden', 'true');
                        }
                    });

                    if (isDesktop()) {
                        sidebar.setAttribute('aria-hidden', 'false');
                    } else {
                        sidebar.setAttribute('aria-hidden', 'true');
                    }
                })();


                (function() {
                    const tabs = Array.from(document.querySelectorAll('.tabs .tab'));
                    const panels = Array.from(document.querySelectorAll('.tab-panel'));

                    function showPanel(id) {
                        panels.forEach(p => {
                            const is = p.id === id;
                            p.hidden = !is;
                            p.classList.toggle('active', is);
                            p.setAttribute('aria-hidden', is ? 'false' : 'true');
                        });
                    }

                    function setActiveTab(btn) {
                        tabs.forEach(t => {
                            const active = t === btn;
                            t.classList.toggle('active', active);
                            t.setAttribute('aria-selected', active ? 'true' : 'false');
                        });
                    }
                    tabs.forEach(tab => {
                        tab.addEventListener('click', function() {
                            setActiveTab(tab);
                            const target = tab.dataset.target;
                            if (target) showPanel(target);

                            setTimeout(() => window.dispatchEvent(new Event('resize')), 100);
                        });
                    });
                    // initialize
                    const initial = tabs.find(t => t.classList.contains('active')) || tabs[0];
                    if (initial) {
                        setActiveTab(initial);
                        showPanel(initial.dataset.target);
                    }
                })();

                (function initEventStatus() {
                    const el = document.getElementById('eventStatusChart');
                    if (!el) return;
                    const chart = echarts.init(el);
                    const option = {
                        animation: false,
                        series: [{
                            type: 'pie',
                            radius: ['40%', '70%'],
                            center: ['50%', '50%'],
                            data: [{
                                    value: 14,
                                    name: 'Draft',
                                    itemStyle: {
                                        color: 'rgba(87,181,231,1)'
                                    }
                                },
                                {
                                    value: 69,
                                    name: 'Active',
                                    itemStyle: {
                                        color: 'rgba(141,211,199,1)'
                                    }
                                },
                                {
                                    value: 42,
                                    name: 'Completed',
                                    itemStyle: {
                                        color: 'rgba(251,191,114,1)'
                                    }
                                },
                                {
                                    value: 22,
                                    name: 'Cancelled',
                                    itemStyle: {
                                        color: 'rgba(252,141,98,1)'
                                    }
                                }
                            ],
                            label: {
                                show: true,
                                position: 'outside',
                                formatter: '{b}\n{c} Events',
                                color: '#111827'
                            },
                            labelLine: {
                                show: true
                            }
                        }],
                        tooltip: {
                            trigger: 'item',
                            backgroundColor: 'rgba(255,255,255,0.95)',
                            textStyle: {
                                color: '#111827'
                            }
                        }
                    };
                    chart.setOption(option);
                    window.addEventListener('resize', () => chart.resize());
                })();
            </script>

            <!-- Volunteer Management -->


            {{-- SIDEBAR --}}
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const sidebarLinks = document.querySelectorAll('.side-link');

                    const currentPage = window.location.pathname.split('/').pop();

                    sidebarLinks.forEach(link => {

                        link.classList.remove('active');

                        if (link.getAttribute('href') && link.getAttribute('href').includes(currentPage)) {
                            link.classList.add('active');
                        }

                        link.addEventListener('click', function() {
                            sidebarLinks.forEach(l => l.classList.remove('active'));
                            link.classList.add('active');
                        });
                    });
                });
            </script>

            {{-- AJAX FOR MANAGE EVENT PARTICIPANTS + LIVE EVENTS --}}
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const listContainer = document.getElementById('eventsList');

                    // Track active bulk operations and disable AJAX during operations
                    let ajaxUpdateEnabled = true;
                    let activeOperations = new Set();

                    // --- Toast Helper ---
                    function showToast(message, isError = false) {
                        const toast = document.getElementById('toast');
                        if (!toast) return;

                        toast.textContent = message;
                        toast.style.background = isError ? '#dc2626' : '#16a34a';
                        toast.style.opacity = '1';
                        toast.style.pointerEvents = 'auto';

                        setTimeout(() => {
                            toast.style.opacity = '0';
                            setTimeout(() => (toast.style.pointerEvents = 'none'), 500);
                        }, 2500);
                    }

                    // --- Participant Status Update ---
                    window.updateParticipantStatus = function(element, newStatus) {
                        const id = element.dataset.id;
                        const row = element.closest('.participant-row');
                        const currentStatus = row.dataset.status;

                        if (currentStatus === newStatus) return;

                        // Update UI immediately
                        const badge = row.querySelector('.p-right .status-badge');
                        const nameBadge = row.querySelector('.participant-name .status-badge');

                        // Update the badge in p-right
                        if (badge) {
                            badge.classList.remove('registered', 'attended', 'missing');
                            if (newStatus === 'registered') {
                                badge.classList.add('registered');
                                badge.textContent = 'Registered';
                            } else if (newStatus === 'attended') {
                                badge.classList.add('attended');
                                badge.textContent = 'Attended';
                            } else if (newStatus === 'absent') {
                                badge.classList.add('missing');
                                badge.textContent = 'Missing';
                            }
                        }

                        // Update the small badge next to name
                        if (nameBadge) {
                            nameBadge.remove();
                        }
                        if (newStatus === 'attended') {
                            const nameDiv = row.querySelector('.participant-name');
                            nameDiv.innerHTML += '<span class="status-badge attended"></span>';
                        } else if (newStatus === 'absent') {
                            const nameDiv = row.querySelector('.participant-name');
                            nameDiv.innerHTML += '<span class="status-badge missing"></span>';
                        }

                        // Update row styling
                        row.style.background = '#fff';
                        if (newStatus === 'attended') {
                            row.style.background = '#ecfdf5';
                        } else if (newStatus === 'absent') {
                            row.style.background = '#fef2f2';
                        }
                        row.dataset.status = newStatus;

                        // Update avatar
                        const avatar = row.querySelector('.avatar-sm');
                        avatar.classList.remove('attended', 'missing');
                        if (newStatus === 'attended') {
                            avatar.classList.add('attended');
                        } else if (newStatus === 'absent') {
                            avatar.classList.add('missing');
                        }

                        // Update checkbox disabled state
                        const checkbox = row.querySelector('.participant-checkbox');
                        if (checkbox) {
                            checkbox.disabled = newStatus === 'attended' || newStatus === 'absent';
                        }

                        // Update backend
                        fetch(`/administrator/volunteer/${id}/update`, {
                                method: 'PUT',
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    status: newStatus
                                }),
                            })
                            .then((res) => {
                                if (!res.ok) throw new Error(`Server error: ${res.status}`);
                                return res.json();
                            })
                            .then((data) => {
                                if (data.success) {
                                    showToast(
                                        `Participant status updated to: ${newStatus === 'absent' ? 'Missing' : newStatus === 'attended' ? 'Attended' : 'Registered'}`
                                    );

                                    // Update button states after status change
                                    const participantsDiv = row.closest('.participants');
                                    if (participantsDiv) {
                                        const eventId = participantsDiv.dataset.eventId;
                                        updateMarkAllButtonsState(eventId);
                                        updateSelectionCount(eventId);
                                    }
                                } else {
                                    showToast('Failed to update participant status', true);
                                }
                            })
                            .catch((err) => {
                                console.error('Error:', err);
                                showToast('Error updating status', true);
                            });
                    };

                    function bindEventButtons() {
                        document.querySelectorAll('.link-btn').forEach((btn) => {
                            btn.addEventListener('click', () => {
                                const target = document.getElementById(btn.dataset.target);
                                if (target) {
                                    target.hidden = !target.hidden;
                                    if (!target.hidden)
                                        target.scrollIntoView({
                                            behavior: 'smooth',
                                            block: 'nearest'
                                        });
                                }
                            });
                        });

                        // View Details Button
                        document.querySelectorAll('.view-details-btn').forEach((btn) => {
                            btn.addEventListener('click', () => {
                                try {
                                    const modal = document.getElementById('eventDetailsModal');
                                    const eventData = JSON.parse(btn.dataset.event);
                                    if (!modal) return;

                                    // Fill modal fields
                                    document.getElementById('modalEventTitle').textContent = eventData
                                        .title || 'Untitled Event';
                                    document.getElementById('modalEventDesc').textContent = eventData
                                        .description || 'No description available.';
                                    document.getElementById('modalStartDate').textContent = new Date(
                                        eventData.start_date).toLocaleDateString();
                                    document.getElementById('modalEndDate').textContent = new Date(eventData
                                        .end_date).toLocaleDateString();
                                    document.getElementById('modalDeadline').textContent = eventData
                                        .deadline ?
                                        new Date(eventData.deadline).toLocaleDateString() :
                                        'No deadline set';
                                    document.getElementById('modalLocation').textContent = eventData
                                        .location || 'Not specified';
                                    document.getElementById('modalCoords').textContent =
                                        eventData.lat && eventData.lng ?
                                        `${eventData.lat}, ${eventData.lng}` : 'Not available';
                                    document.getElementById('modalCoordName').textContent = eventData
                                        .coordinator_name || 'N/A';
                                    document.getElementById('modalCoordEmail').textContent = eventData
                                        .coordinator_email || 'N/A';
                                    document.getElementById('modalCoordPhone').textContent = eventData
                                        .coordinator_phone || 'N/A';
                                    document.getElementById('modalRegistered').textContent = eventData
                                        .registrations?.length || 0;

                                    // Photo
                                    const photoEl = document.getElementById('modalEventPhoto');
                                    if (eventData.photo) {
                                        photoEl.src = `/storage/${eventData.photo}`;
                                        photoEl.style.display = 'block';
                                    } else {
                                        photoEl.style.display = 'none';
                                    }

                                    modal.style.display = 'flex';
                                } catch (err) {
                                    console.error('Error parsing event data:', err);
                                    showToast('Error loading event details', true);
                                }
                            });
                        });
                    }

                    // --- Live Event Fetch (UPDATED for badge system) ---
                    function fetchLiveEvents() {
                        // Don't fetch if AJAX updates are disabled (during bulk operations)
                        if (!ajaxUpdateEnabled) {
                            console.log('AJAX updates disabled, skipping fetch');
                            return;
                        }

                        // Remember open participant panels
                        const openSections = Array.from(
                            document.querySelectorAll('.participants:not([hidden])')
                        ).map((el) => el.id);

                        // Store all checkbox states and button states
                        const savedStates = {};

                        document.querySelectorAll('.participants').forEach(participantsDiv => {
                            const eventId = participantsDiv.id.split('-')[1];
                            const sectionId = `participants-${eventId}`;

                            // Skip if this event has an active operation
                            if (activeOperations.has(eventId)) {
                                console.log(`Skipping save for event ${eventId} - operation in progress`);
                                return;
                            }

                            // Save checkbox states
                            const checkedIds = [];
                            participantsDiv.querySelectorAll('.participant-checkbox:checked').forEach(cb => {
                                checkedIds.push(cb.value);
                            });

                            // Save participant statuses
                            const participantStatuses = [];
                            participantsDiv.querySelectorAll('.participant-row').forEach(row => {
                                participantStatuses.push({
                                    id: row.id.replace('row-', ''),
                                    status: row.dataset.status
                                });
                            });

                            // Save button states
                            const selectBtn = participantsDiv.querySelector('.btn-select-participants');
                            const markAllBtn = participantsDiv.querySelector('.btn-mark-all');
                            const markMissingBtn = participantsDiv.querySelector('.btn-mark-missing');
                            const dropdownMenu = document.getElementById(`dropdownMenu-${eventId}`);
                            const selectionCountDisplay = document.getElementById(
                                `selectionCountDisplay-${eventId}`);

                            const buttonStates = {
                                selectBtn: {
                                    text: selectBtn ? selectBtn.innerHTML : '',
                                    className: selectBtn ? selectBtn.className : ''
                                },
                                markAllBtn: {
                                    text: markAllBtn ? markAllBtn.innerHTML : '',
                                    className: markAllBtn ? markAllBtn.className : '',
                                    disabled: markAllBtn ? markAllBtn.disabled : false
                                },
                                markMissingBtn: {
                                    text: markMissingBtn ? markMissingBtn.innerHTML : '',
                                    className: markMissingBtn ? markMissingBtn.className : '',
                                    disabled: markMissingBtn ? markMissingBtn.disabled : false
                                }
                            };

                            // Save selection mode and dropdown visibility
                            const isSelectionMode = participantsDiv.classList.contains('selection-mode');
                            const dropdownVisible = dropdownMenu ? dropdownMenu.style.display !== 'none' : false;
                            const selectionCountVisible = selectionCountDisplay ? selectionCountDisplay.style
                                .display !== 'none' : false;

                            savedStates[sectionId] = {
                                checkedIds: checkedIds,
                                participantStatuses: participantStatuses,
                                buttonStates: buttonStates,
                                isSelectionMode: isSelectionMode,
                                dropdownVisible: dropdownVisible,
                                selectionCountVisible: selectionCountVisible
                            };
                        });

                        fetch('{{ route('events.live') }}', {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                            })
                            .then((res) => res.text())
                            .then((html) => {
                                // Replace the container
                                listContainer.innerHTML = html;

                                // Restore open panels
                                openSections.forEach((id) => {
                                    const el = document.getElementById(id);
                                    if (el) el.hidden = false;
                                });

                                // Restore states for each participants section
                                Object.keys(savedStates).forEach(sectionId => {
                                    const state = savedStates[sectionId];
                                    const participantsDiv = document.getElementById(sectionId);

                                    if (!participantsDiv) return;

                                    const eventId = sectionId.split('-')[1];
                                    const dropdownContainer = document.getElementById(
                                        `selectDropdown-${eventId}`);
                                    const dropdownMenu = document.getElementById(`dropdownMenu-${eventId}`);
                                    const selectionCountDisplay = document.getElementById(
                                        `selectionCountDisplay-${eventId}`);

                                    // Restore selection mode
                                    if (state.isSelectionMode) {
                                        participantsDiv.classList.add('selection-mode');
                                        participantsDiv.querySelectorAll('.p-checkbox').forEach(el => {
                                            el.style.display = 'block';
                                        });
                                    }

                                    // Restore dropdown visibility
                                    if (dropdownMenu && state.dropdownVisible) {
                                        dropdownMenu.style.display = 'block';
                                        if (dropdownContainer) {
                                            dropdownContainer.classList.add('selection-active');
                                        }
                                    }

                                    // Restore selection count display
                                    if (selectionCountDisplay && state.selectionCountVisible) {
                                        selectionCountDisplay.style.display = 'block';
                                    }

                                    // Restore checked checkboxes
                                    let restoredCount = 0;
                                    state.checkedIds.forEach(id => {
                                        const checkbox = participantsDiv.querySelector(
                                            `.participant-checkbox[value="${id}"]`);
                                        if (checkbox && !checkbox.disabled) {
                                            checkbox.checked = true;
                                            checkbox.onchange = () => updateSelectionCount(eventId);
                                            restoredCount++;
                                        }
                                    });

                                    // Update selection count if in selection mode
                                    if (state.isSelectionMode || state.dropdownVisible) {
                                        updateSelectionCount(eventId);
                                    }

                                    // Restore participant statuses (for visual consistency)
                                    state.participantStatuses.forEach(participant => {
                                        const row = participantsDiv.querySelector(
                                            `#row-${participant.id}`);
                                        if (row) {
                                            row.dataset.status = participant.status;

                                            // Set background color based on status
                                            if (participant.status === 'attended') {
                                                row.style.background = '#ecfdf5';
                                            } else if (participant.status === 'absent') {
                                                row.style.background = '#fef2f2';
                                            }

                                            // Update avatar
                                            const avatar = row.querySelector('.avatar-sm');
                                            avatar.classList.remove('attended', 'missing');
                                            if (participant.status === 'attended') {
                                                avatar.classList.add('attended');
                                            } else if (participant.status === 'absent') {
                                                avatar.classList.add('missing');
                                            }

                                            // Update checkbox disabled state
                                            const checkbox = row.querySelector('.participant-checkbox');
                                            if (checkbox) {
                                                checkbox.disabled = participant.status === 'attended' ||
                                                    participant.status === 'absent';
                                            }
                                        }
                                    });

                                    // Restore mark all button state - INCLUDING DISABLED STATE
                                    const markAllBtn = document.getElementById(`markAllBtn-${eventId}`);
                                    if (markAllBtn) {
                                        markAllBtn.innerHTML = state.buttonStates.markAllBtn.text;
                                        markAllBtn.className = state.buttonStates.markAllBtn.className;
                                        markAllBtn.disabled = state.buttonStates.markAllBtn.disabled ||
                                            activeOperations.has(eventId);
                                    }

                                    // Restore mark missing button state - INCLUDING DISABLED STATE
                                    const markMissingBtn = document.getElementById(
                                        `markAllMissingBtn-${eventId}`);
                                    if (markMissingBtn) {
                                        markMissingBtn.innerHTML = state.buttonStates.markMissingBtn.text;
                                        markMissingBtn.className = state.buttonStates.markMissingBtn.className;
                                        markMissingBtn.disabled = state.buttonStates.markMissingBtn.disabled ||
                                            activeOperations.has(eventId);
                                    }
                                });

                                // Rebind all event buttons
                                bindEventButtons();

                                // Simple rebind for bulk actions
                                document.querySelectorAll('.participants').forEach(participantsDiv => {
                                    const eventId = participantsDiv.id.split('-')[1];

                                    // Bind select participants button
                                    const selectBtn = participantsDiv.querySelector('.btn-select-participants');
                                    if (selectBtn && !selectBtn.onclick) {
                                        selectBtn.onclick = () => toggleSelectionMode(eventId);
                                    }

                                    // Bind mark all attended button - check if not disabled
                                    const markAllBtn = participantsDiv.querySelector('.btn-mark-all');
                                    if (markAllBtn && !markAllBtn.onclick && !markAllBtn.disabled) {
                                        markAllBtn.onclick = () => markAllAttended(eventId);
                                    }

                                    // Bind mark all missing button - check if not disabled
                                    const markMissingBtn = participantsDiv.querySelector('.btn-mark-missing');
                                    if (markMissingBtn && !markMissingBtn.onclick && !markMissingBtn.disabled) {
                                        markMissingBtn.onclick = () => markAllMissing(eventId);
                                    }

                                    // Bind checkboxes
                                    participantsDiv.querySelectorAll('.participant-checkbox').forEach(cb => {
                                        if (!cb.onchange) {
                                            cb.onchange = () => updateSelectionCount(eventId);
                                        }
                                    });

                                    // Bind dropdown menu buttons
                                    const dropdownMenu = document.getElementById(`dropdownMenu-${eventId}`);
                                    if (dropdownMenu) {
                                        const markAttendedBtn = dropdownMenu.querySelector(
                                            '.dropdown-item:first-child');
                                        const markMissingBtn = dropdownMenu.querySelector(
                                            '.dropdown-item:nth-child(2)');
                                        const cancelBtn = dropdownMenu.querySelector('.dropdown-item.cancel');

                                        if (markAttendedBtn && !markAttendedBtn.onclick) {
                                            markAttendedBtn.onclick = () => markSelectedAttended(eventId);
                                        }
                                        if (markMissingBtn && !markMissingBtn.onclick) {
                                            markMissingBtn.onclick = () => markSelectedMissing(eventId);
                                        }
                                        if (cancelBtn && !cancelBtn.onclick) {
                                            cancelBtn.onclick = () => cancelSelection(eventId);
                                        }
                                    }
                                });

                                // Initialize button states after rebinding
                                document.querySelectorAll('.participants').forEach(participantsDiv => {
                                    const eventId = participantsDiv.id.split('-')[1];
                                    updateMarkAllButtonsState(eventId);
                                });
                            })
                            .catch((err) => console.error('Live fetch error:', err));
                    }

                    // --- Close Modal Logic ---
                    document.addEventListener('click', (e) => {
                        const modal = document.getElementById('eventDetailsModal');
                        if (!modal) return;

                        // Close button click
                        if (e.target.id === 'closeEventModalBtn') {
                            modal.style.display = 'none';
                        }

                        // Click outside modal
                        if (e.target === modal) {
                            modal.style.display = 'none';
                        }
                    });

                    // --- SELECTION MODE FUNCTIONS ---

                    // Toggle selection mode with dropdown
                    window.toggleSelectionMode = function(eventId) {
                        const participantsDiv = document.getElementById(`participants-${eventId}`);
                        const dropdownContainer = document.getElementById(`selectDropdown-${eventId}`);
                        const dropdownMenu = document.getElementById(`dropdownMenu-${eventId}`);
                        const selectionCountDisplay = document.getElementById(`selectionCountDisplay-${eventId}`);

                        if (!participantsDiv || !dropdownContainer) return;

                        if (participantsDiv.classList.contains('selection-mode')) {
                            // Already in selection mode - toggle dropdown menu
                            if (dropdownMenu) {
                                dropdownMenu.style.display = dropdownMenu.style.display === 'none' ? 'block' : 'none';
                            }
                            dropdownContainer.classList.toggle('selection-active');
                            return;
                        }

                        // Enter selection mode
                        participantsDiv.classList.add('selection-mode');
                        dropdownContainer.classList.add('selection-active');

                        // Show dropdown menu
                        if (dropdownMenu) {
                            dropdownMenu.style.display = 'block';
                        }

                        // Show selection count display
                        if (selectionCountDisplay) {
                            selectionCountDisplay.style.display = 'block';
                        }

                        // Show checkboxes
                        participantsDiv.querySelectorAll('.p-checkbox').forEach(el => {
                            el.style.display = 'block';
                        });

                        // Make sure all checkboxes are unchecked when entering selection mode
                        participantsDiv.querySelectorAll('.participant-checkbox:not(:disabled)').forEach(cb => {
                            cb.checked = false;
                        });

                        // Update selection count
                        updateSelectionCount(eventId);

                        // Close dropdown when clicking outside
                        setTimeout(() => {
                            const closeDropdownHandler = function(e) {
                                if (!dropdownContainer.contains(e.target) && !participantsDiv.contains(e
                                        .target)) {
                                    if (dropdownMenu) {
                                        dropdownMenu.style.display = 'none';
                                        dropdownContainer.classList.remove('selection-active');
                                    }
                                    document.removeEventListener('click', closeDropdownHandler);
                                }
                            };
                            document.addEventListener('click', closeDropdownHandler);
                        }, 100);
                    };

                    // Cancel selection
                    window.cancelSelection = function(eventId) {
                        const participantsDiv = document.getElementById(`participants-${eventId}`);
                        const dropdownContainer = document.getElementById(`selectDropdown-${eventId}`);
                        const dropdownMenu = document.getElementById(`dropdownMenu-${eventId}`);
                        const selectionCountDisplay = document.getElementById(`selectionCountDisplay-${eventId}`);

                        if (!participantsDiv || !dropdownContainer) return;

                        // Exit selection mode
                        participantsDiv.classList.remove('selection-mode');
                        dropdownContainer.classList.remove('selection-active');

                        // Hide dropdown menu
                        if (dropdownMenu) {
                            dropdownMenu.style.display = 'none';
                        }

                        // Hide selection count display
                        if (selectionCountDisplay) {
                            selectionCountDisplay.style.display = 'none';
                        }

                        // Hide checkboxes
                        participantsDiv.querySelectorAll('.p-checkbox').forEach(el => {
                            el.style.display = 'none';
                        });

                        // Uncheck all checkboxes
                        participantsDiv.querySelectorAll('.participant-checkbox').forEach(cb => {
                            cb.checked = false;
                        });

                        // Update button states
                        updateMarkAllButtonsState(eventId);
                    };

                    // Update selection count
                    window.updateSelectionCount = function(eventId) {
                        const checkboxes = document.querySelectorAll(
                            `#participants-${eventId} .participant-checkbox:checked:not(:disabled)`
                        );
                        const selectionCount = document.getElementById(`selectionCount-${eventId}`);
                        const dropdownMenu = document.getElementById(`dropdownMenu-${eventId}`);

                        // Update selection count display
                        if (selectionCount) {
                            selectionCount.textContent = `${checkboxes.length} selected`;
                        }

                        // Update dropdown menu items if needed
                        if (dropdownMenu) {
                            const markAttendedBtn = dropdownMenu.querySelector('.dropdown-item:first-child');
                            const markMissingBtn = dropdownMenu.querySelector('.dropdown-item:nth-child(2)');

                            if (checkboxes.length > 0) {
                                // Update button text with count
                                if (markAttendedBtn) {
                                    markAttendedBtn.innerHTML =
                                        `<i class="ri-check-double-line"></i> Mark ${checkboxes.length} as Attended`;
                                }
                                if (markMissingBtn) {
                                    markMissingBtn.innerHTML =
                                        `<i class="ri-close-line"></i> Mark ${checkboxes.length} as Missing`;
                                }
                            } else {
                                // Reset button text
                                if (markAttendedBtn) {
                                    markAttendedBtn.innerHTML = `<i class="ri-check-double-line"></i> Mark as Attended`;
                                }
                                if (markMissingBtn) {
                                    markMissingBtn.innerHTML = `<i class="ri-close-line"></i> Mark as Missing`;
                                }
                            }
                        }
                    };

                    // --- BULK ACTION FUNCTIONS ---

                    // MARK ALL ATTENDED - SIMPLIFIED
                    window.markAllAttended = async function(eventId) {
                        // Check if operation is already in progress
                        if (activeOperations.has(eventId)) {
                            showToast('Please wait for the current operation to complete', true);
                            return;
                        }

                        const participantsDiv = document.getElementById(`participants-${eventId}`);
                        if (!participantsDiv) return;

                        // Get all participants that are NOT already attended
                        const participantRows = participantsDiv.querySelectorAll('.participant-row');
                        const nonAttendedRows = Array.from(participantRows).filter(row =>
                            row.dataset.status !== 'attended'
                        );

                        if (nonAttendedRows.length === 0) {
                            showToast('All participants are already marked as attended!', true);
                            return;
                        }

                        // Only show modal if there are participants to update
                        showConfirmModal(
                            'Mark All as Attended',
                            `Mark ALL ${nonAttendedRows.length} participants as attended?`,
                            () => proceedMarkAllAttended(eventId)
                        );
                    };

                    // Actual function to mark all - SIMPLIFIED
                    async function proceedMarkAllAttended(eventId) {
                        const button = document.getElementById(`markAllBtn-${eventId}`);
                        const otherButton = document.getElementById(`markAllMissingBtn-${eventId}`);

                        if (!button) {
                            showToast('Button not found!', true);
                            return;
                        }

                        // Disable AJAX updates during operation
                        ajaxUpdateEnabled = false;

                        // Mark operation as active
                        activeOperations.add(eventId);

                        const originalText = button.innerHTML;
                        const originalTextOther = otherButton ? otherButton.innerHTML : '';
                        button.innerHTML = '<i class="ri-loader-4-line spin"></i> Updating...';
                        button.disabled = true;

                        // Disable the other button as well (NEW: Disable both buttons when one is clicked)
                        if (otherButton) {
                            otherButton.innerHTML = originalTextOther;
                            otherButton.disabled = true;
                        }

                        const participantsDiv = document.getElementById(`participants-${eventId}`);
                        const participantRows = participantsDiv.querySelectorAll('.participant-row');

                        // Get all NON-attended registration IDs
                        const nonAttendedRows = Array.from(participantRows).filter(row =>
                            row.dataset.status !== 'attended'
                        );
                        const registrationIds = nonAttendedRows.map(row =>
                            row.id.replace('row-', '')
                        );

                        if (registrationIds.length === 0) {
                            showToast('All participants are already marked as attended', true);
                            button.innerHTML = originalText;
                            button.disabled = false;
                            if (otherButton) {
                                otherButton.disabled = false;
                            }
                            activeOperations.delete(eventId);
                            ajaxUpdateEnabled = true;
                            return;
                        }

                        let successCount = 0;
                        let errorCount = 0;

                        // Process all at once instead of chunks for simplicity
                        const promises = registrationIds.map(id => updateSingleParticipant(id, 'attended'));

                        try {
                            const results = await Promise.allSettled(promises);

                            results.forEach(result => {
                                if (result.status === 'fulfilled' && result.value.success) {
                                    successCount++;
                                } else {
                                    errorCount++;
                                }
                            });

                            // Update UI after all updates
                            registrationIds.forEach(id => {
                                const row = document.getElementById(`row-${id}`);
                                if (row) {
                                    row.dataset.status = 'attended';
                                    row.style.background = '#ecfdf5';

                                    // Update badge
                                    const badge = row.querySelector('.p-right .status-badge');
                                    if (badge) {
                                        badge.classList.remove('registered', 'missing');
                                        badge.classList.add('attended');
                                        badge.textContent = 'Attended';
                                    }

                                    // Update name badge
                                    const nameBadge = row.querySelector('.participant-name .status-badge');
                                    if (nameBadge) nameBadge.remove();
                                    const nameDiv = row.querySelector('.participant-name');
                                    nameDiv.innerHTML += '<span class="status-badge attended"></span>';

                                    // Update avatar
                                    const avatar = row.querySelector('.avatar-sm');
                                    avatar.classList.remove('missing');
                                    avatar.classList.add('attended');

                                    // Disable checkbox
                                    const checkbox = row.querySelector('.participant-checkbox');
                                    if (checkbox) {
                                        checkbox.disabled = true;
                                    }
                                }
                            });
                        } catch (error) {
                            console.error('Error in bulk update:', error);
                            errorCount = registrationIds.length;
                        }

                        // Show result
                        button.innerHTML = originalText;

                        // NEW: Keep both buttons disabled until AJAX refresh
                        // Don't re-enable buttons here - let AJAX refresh handle it
                        button.disabled = true;
                        if (otherButton) {
                            otherButton.disabled = true;
                        }

                        // Mark operation as complete
                        activeOperations.delete(eventId);

                        // Re-enable AJAX updates
                        ajaxUpdateEnabled = true;

                        // Update button states (they will remain disabled until AJAX refresh)
                        updateMarkAllButtonsState(eventId);

                        if (errorCount === 0) {
                            showToast(`✅ Successfully updated ${successCount} participants!`);
                        } else {
                            showToast(`Updated ${successCount} participants, but ${errorCount} failed.`, true);
                        }
                    }

                    // MARK ALL MISSING - SIMPLIFIED
                    window.markAllMissing = async function(eventId) {
                        // Check if operation is already in progress
                        if (activeOperations.has(eventId)) {
                            showToast('Please wait for the current operation to complete', true);
                            return;
                        }

                        const participantsDiv = document.getElementById(`participants-${eventId}`);
                        if (!participantsDiv) return;

                        // Get all participants that are NOT already marked as missing
                        const participantRows = participantsDiv.querySelectorAll('.participant-row');
                        const nonMissingRows = Array.from(participantRows).filter(row =>
                            row.dataset.status !== 'absent'
                        );

                        if (nonMissingRows.length === 0) {
                            showToast('All participants are already marked as missing!', true);
                            return;
                        }

                        // Show confirmation modal
                        showConfirmModal(
                            'Mark All as Missing',
                            `Mark ALL ${nonMissingRows.length} participants as missing?`,
                            () => proceedMarkAllMissing(eventId)
                        );
                    };

                    // Actual function to mark all as missing - SIMPLIFIED
                    async function proceedMarkAllMissing(eventId) {
                        const button = document.getElementById(`markAllMissingBtn-${eventId}`);
                        const otherButton = document.getElementById(`markAllBtn-${eventId}`);

                        if (!button) {
                            showToast('Button not found!', true);
                            return;
                        }

                        // Disable AJAX updates during operation
                        ajaxUpdateEnabled = false;

                        // Mark operation as active
                        activeOperations.add(eventId);

                        const originalText = button.innerHTML;
                        const originalTextOther = otherButton ? otherButton.innerHTML : '';
                        button.innerHTML = '<i class="ri-loader-4-line spin"></i> Updating...';
                        button.disabled = true;

                        // Disable the other button as well (NEW: Disable both buttons when one is clicked)
                        if (otherButton) {
                            otherButton.innerHTML = originalTextOther;
                            otherButton.disabled = true;
                        }

                        const participantsDiv = document.getElementById(`participants-${eventId}`);
                        const participantRows = participantsDiv.querySelectorAll('.participant-row');

                        // Get all registration IDs that are NOT already marked as missing
                        const nonMissingRows = Array.from(participantRows).filter(row =>
                            row.dataset.status !== 'absent'
                        );
                        const registrationIds = nonMissingRows.map(row =>
                            row.id.replace('row-', '')
                        );

                        if (registrationIds.length === 0) {
                            showToast('All participants are already marked as missing', true);
                            button.innerHTML = originalText;
                            button.disabled = false;
                            if (otherButton) {
                                otherButton.disabled = false;
                            }
                            activeOperations.delete(eventId);
                            ajaxUpdateEnabled = true;
                            return;
                        }

                        let successCount = 0;
                        let errorCount = 0;

                        // Process all at once
                        const promises = registrationIds.map(id => updateSingleParticipant(id, 'absent'));

                        try {
                            const results = await Promise.allSettled(promises);

                            results.forEach(result => {
                                if (result.status === 'fulfilled' && result.value.success) {
                                    successCount++;
                                } else {
                                    errorCount++;
                                }
                            });

                            // Update UI after all updates
                            registrationIds.forEach(id => {
                                const row = document.getElementById(`row-${id}`);
                                if (row) {
                                    row.dataset.status = 'absent';
                                    row.style.background = '#fef2f2';

                                    // Update badge
                                    const badge = row.querySelector('.p-right .status-badge');
                                    if (badge) {
                                        badge.classList.remove('registered', 'attended');
                                        badge.classList.add('missing');
                                        badge.textContent = 'Missing';
                                    }

                                    // Update name badge
                                    const nameBadge = row.querySelector('.participant-name .status-badge');
                                    if (nameBadge) nameBadge.remove();
                                    const nameDiv = row.querySelector('.participant-name');
                                    nameDiv.innerHTML += '<span class="status-badge missing"></span>';

                                    // Update avatar
                                    const avatar = row.querySelector('.avatar-sm');
                                    avatar.classList.remove('attended');
                                    avatar.classList.add('missing');

                                    // Disable checkbox
                                    const checkbox = row.querySelector('.participant-checkbox');
                                    if (checkbox) {
                                        checkbox.disabled = true;
                                    }
                                }
                            });
                        } catch (error) {
                            console.error('Error in bulk update:', error);
                            errorCount = registrationIds.length;
                        }

                        // Show result
                        button.innerHTML = originalText;

                        // NEW: Keep both buttons disabled until AJAX refresh
                        // Don't re-enable buttons here - let AJAX refresh handle it
                        button.disabled = true;
                        if (otherButton) {
                            otherButton.disabled = true;
                        }

                        // Mark operation as complete
                        activeOperations.delete(eventId);

                        // Re-enable AJAX updates
                        ajaxUpdateEnabled = true;

                        // Update button states (they will remain disabled until AJAX refresh)
                        updateMarkAllButtonsState(eventId);

                        if (errorCount === 0) {
                            showToast(`✅ Marked ${successCount} participants as missing!`);
                        } else {
                            showToast(`Marked ${successCount} participants as missing, but ${errorCount} failed.`,
                                true);
                        }
                    }

                    // Helper function to update single participant
                    async function updateSingleParticipant(id, status) {
                        try {
                            const response = await fetch(`/administrator/volunteer/${id}/update`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf
                                },
                                body: JSON.stringify({
                                    status: status
                                })
                            });

                            const contentType = response.headers.get("content-type");
                            if (!contentType || !contentType.includes("application/json")) {
                                throw new Error("Response is not JSON");
                            }

                            const data = await response.json();
                            return {
                                success: data.success
                            };
                        } catch (error) {
                            console.error('Error updating participant:', error);
                            return {
                                success: false,
                                error: error.message
                            };
                        }
                    }

                    // UPDATE MARK ALL BUTTONS STATE - FIXED TO DISABLE BOTH BUTTONS DURING OPERATION
                    window.updateMarkAllButtonsState = function(eventId) {
                        const participantsDiv = document.getElementById(`participants-${eventId}`);
                        if (!participantsDiv) return;

                        // Count non-attended participants
                        const participantRows = participantsDiv.querySelectorAll('.participant-row');
                        const nonAttendedCount = Array.from(participantRows).filter(row =>
                            row.dataset.status !== 'attended'
                        ).length;

                        // Count non-missing participants
                        const nonMissingCount = Array.from(participantRows).filter(row =>
                            row.dataset.status !== 'absent'
                        ).length;

                        // Update Mark All as Attended button
                        const markAllBtn = document.getElementById(`markAllBtn-${eventId}`);
                        if (markAllBtn) {
                            // Disable if no non-attended participants OR if operation is active
                            markAllBtn.disabled = nonAttendedCount === 0 || activeOperations.has(eventId);
                        }

                        // Update Mark All as Missing button
                        const markAllMissingBtn = document.getElementById(`markAllMissingBtn-${eventId}`);
                        if (markAllMissingBtn) {
                            // Disable if no non-missing participants OR if operation is active
                            markAllMissingBtn.disabled = nonMissingCount === 0 || activeOperations.has(eventId);
                        }
                    };

                    // MARK SELECTED AS ATTENDED
                    window.markSelectedAttended = function(eventId) {
                        // Check if operation is already in progress
                        if (activeOperations.has(eventId)) {
                            showToast('Please wait for the current operation to complete', true);
                            return;
                        }

                        const checkboxes = document.querySelectorAll(
                            `#participants-${eventId} .participant-checkbox:checked:not(:disabled)`
                        );

                        if (checkboxes.length === 0) {
                            showToast('Please select at least one participant', true);
                            return;
                        }

                        showConfirmModal(
                            'Mark Selected as Attended',
                            `Mark ${checkboxes.length} selected participants as attended?`,
                            () => proceedMarkSelected(eventId, 'attended')
                        );
                    };

                    // MARK SELECTED AS MISSING
                    window.markSelectedMissing = function(eventId) {
                        // Check if operation is already in progress
                        if (activeOperations.has(eventId)) {
                            showToast('Please wait for the current operation to complete', true);
                            return;
                        }

                        const checkboxes = document.querySelectorAll(
                            `#participants-${eventId} .participant-checkbox:checked:not(:disabled)`
                        );

                        if (checkboxes.length === 0) {
                            showToast('Please select at least one participant', true);
                            return;
                        }

                        showConfirmModal(
                            'Mark Selected as Missing',
                            `Mark ${checkboxes.length} selected participants as missing?`,
                            () => proceedMarkSelected(eventId, 'absent')
                        );
                    };

                    // PROCEED WITH MARKING SELECTED PARTICIPANTS
                    async function proceedMarkSelected(eventId, status) {
                        const checkboxes = document.querySelectorAll(
                            `#participants-${eventId} .participant-checkbox:checked:not(:disabled)`
                        );
                        const selectedIds = Array.from(checkboxes).map(cb => cb.value);

                        // Disable AJAX updates during operation
                        ajaxUpdateEnabled = false;

                        // Mark operation as active
                        activeOperations.add(eventId);

                        // Show loading in dropdown buttons
                        const dropdownMenu = document.getElementById(`dropdownMenu-${eventId}`);
                        const markAttendedBtn = dropdownMenu ? dropdownMenu.querySelector(
                            '.dropdown-item:first-child') : null;
                        const markMissingBtn = dropdownMenu ? dropdownMenu.querySelector(
                            '.dropdown-item:nth-child(2)') : null;

                        // Also disable the main bulk action buttons
                        const markAllBtn = document.getElementById(`markAllBtn-${eventId}`);
                        const markAllMissingBtn = document.getElementById(`markAllMissingBtn-${eventId}`);

                        const originalTextAttended = markAttendedBtn ? markAttendedBtn.innerHTML : '';
                        const originalTextMissing = markMissingBtn ? markMissingBtn.innerHTML : '';
                        const originalTextMarkAll = markAllBtn ? markAllBtn.innerHTML : '';
                        const originalTextMarkMissing = markAllMissingBtn ? markAllMissingBtn.innerHTML : '';

                        if (markAttendedBtn && status === 'attended') {
                            markAttendedBtn.innerHTML = '<i class="ri-loader-4-line spin"></i> Updating...';
                            markAttendedBtn.disabled = true;
                        }

                        if (markMissingBtn && status === 'absent') {
                            markMissingBtn.innerHTML = '<i class="ri-loader-4-line spin"></i> Updating...';
                            markMissingBtn.disabled = true;
                        }

                        // Disable main bulk buttons (NEW: Disable both)
                        if (markAllBtn) {
                            markAllBtn.disabled = true;
                        }
                        if (markAllMissingBtn) {
                            markAllMissingBtn.disabled = true;
                        }

                        let successCount = 0;
                        let errorCount = 0;

                        // Process all at once
                        const promises = selectedIds.map(id => updateSingleParticipant(id, status));

                        try {
                            const results = await Promise.allSettled(promises);

                            results.forEach(result => {
                                if (result.status === 'fulfilled' && result.value.success) {
                                    successCount++;
                                } else {
                                    errorCount++;
                                }
                            });

                            // Update UI after all updates
                            selectedIds.forEach(id => {
                                const row = document.getElementById(`row-${id}`);
                                if (row) {
                                    row.dataset.status = status;

                                    // Set background based on status
                                    if (status === 'attended') {
                                        row.style.background = '#ecfdf5';
                                    } else if (status === 'absent') {
                                        row.style.background = '#fef2f2';
                                    }

                                    // Update badge
                                    const badge = row.querySelector('.p-right .status-badge');
                                    if (badge) {
                                        badge.classList.remove('registered', 'attended', 'missing');
                                        if (status === 'registered') {
                                            badge.classList.add('registered');
                                            badge.textContent = 'Registered';
                                        } else if (status === 'attended') {
                                            badge.classList.add('attended');
                                            badge.textContent = 'Attended';
                                        } else if (status === 'absent') {
                                            badge.classList.add('missing');
                                            badge.textContent = 'Missing';
                                        }
                                    }

                                    // Update name badge
                                    const nameBadge = row.querySelector('.participant-name .status-badge');
                                    if (nameBadge) nameBadge.remove();
                                    if (status === 'attended') {
                                        const nameDiv = row.querySelector('.participant-name');
                                        nameDiv.innerHTML += '<span class="status-badge attended"></span>';
                                    } else if (status === 'absent') {
                                        const nameDiv = row.querySelector('.participant-name');
                                        nameDiv.innerHTML += '<span class="status-badge missing"></span>';
                                    }

                                    // Update avatar
                                    const avatar = row.querySelector('.avatar-sm');
                                    avatar.classList.remove('attended', 'missing');
                                    if (status === 'attended') {
                                        avatar.classList.add('attended');
                                    } else if (status === 'absent') {
                                        avatar.classList.add('missing');
                                    }

                                    // Disable checkbox
                                    const checkbox = row.querySelector('.participant-checkbox');
                                    if (checkbox) {
                                        checkbox.disabled = true;
                                        checkbox.checked = false;
                                    }
                                }
                            });
                        } catch (error) {
                            console.error('Error in selected update:', error);
                            errorCount = selectedIds.length;
                        }

                        // Restore dropdown button states
                        if (markAttendedBtn && status === 'attended') {
                            markAttendedBtn.innerHTML = originalTextAttended;
                            markAttendedBtn.disabled = false;
                        }

                        if (markMissingBtn && status === 'absent') {
                            markMissingBtn.innerHTML = originalTextMissing;
                            markMissingBtn.disabled = false;
                        }

                        // NEW: Keep main bulk buttons disabled until AJAX refresh
                        // Don't re-enable them here - let AJAX refresh handle it
                        if (markAllBtn) {
                            markAllBtn.innerHTML = originalTextMarkAll;
                            markAllBtn.disabled = true;
                        }
                        if (markAllMissingBtn) {
                            markAllMissingBtn.innerHTML = originalTextMarkMissing;
                            markAllMissingBtn.disabled = true;
                        }

                        // Mark operation as complete
                        activeOperations.delete(eventId);

                        // Re-enable AJAX updates
                        ajaxUpdateEnabled = true;

                        // Update UI after all updates
                        updateSelectionCount(eventId);
                        updateMarkAllButtonsState(eventId);

                        // Show result
                        if (errorCount === 0) {
                            const statusText = status === 'attended' ? 'attended' : 'missing';
                            showToast(`✅ Marked ${successCount} participants as ${statusText}!`);
                        } else {
                            showToast(`Marked ${successCount} participants, but ${errorCount} failed.`, true);
                        }

                        // Check if all checkboxes are now disabled (no more selectable participants)
                        const remainingCheckboxes = document.querySelectorAll(
                            `#participants-${eventId} .participant-checkbox:not(:disabled)`
                        );

                        if (remainingCheckboxes.length === 0) {
                            // Auto-exit selection mode if no more selectable participants
                            cancelSelection(eventId);
                        }
                    }

                    // Add this function to use your existing confirm modal
                    window.showConfirmModal = function(title, message, onConfirm) {
                        // Set modal content
                        const titleEl = document.getElementById('confirmModalTitle');
                        const messageEl = document.getElementById('confirmModalMessage');
                        const modalEl = document.getElementById('confirmModal');

                        if (!titleEl || !messageEl || !modalEl) {
                            console.error('Confirm modal elements not found!');
                            return;
                        }

                        titleEl.textContent = title;
                        messageEl.textContent = message;

                        // Show modal
                        modalEl.style.display = 'flex';

                        // Set up confirm button
                        const confirmBtn = document.getElementById('confirmActionBtn');
                        const cancelBtn = document.getElementById('cancelConfirmBtn');

                        // Remove any existing listeners to prevent duplicates
                        const newConfirmBtn = confirmBtn.cloneNode(true);
                        const newCancelBtn = cancelBtn.cloneNode(true);

                        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
                        cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

                        // Add new listeners
                        newConfirmBtn.onclick = function() {
                            modalEl.style.display = 'none';
                            if (typeof onConfirm === 'function') {
                                onConfirm();
                            }
                        };

                        newCancelBtn.onclick = function() {
                            modalEl.style.display = 'none';
                        };
                    };

                    // --- Initialize ---
                    bindEventButtons();

                    // Initialize button states on initial load
                    setTimeout(() => {
                        document.querySelectorAll('.participants').forEach(participantsDiv => {
                            const eventId = participantsDiv.id.split('-')[1];
                            updateMarkAllButtonsState(eventId);
                        });
                    }, 100);

                    setInterval(fetchLiveEvents, 8000);
                });
            </script>


            {{-- AJAX STATISTICS EVENT --}}
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    async function updateEventStats() {
                        try {
                            const res = await fetch(`{{ url('/administrator/events/statistics') }}`);
                            if (!res.ok) throw new Error(`Fetch failed: ${res.status}`);
                            const stats = await res.json();

                            // update numbers smoothly
                            document.getElementById('availableCount').textContent = stats.available ?? 0;
                            document.getElementById('ongoingCount').textContent = stats.ongoing ?? 0;
                            document.getElementById('endedCount').textContent = stats.ended ?? 0;
                        } catch (err) {
                            console.error('Error fetching event stats:', err);
                        }
                    }

                    // initial load
                    updateEventStats();


                    setInterval(updateEventStats, 8000);
                });
            </script>



            {{-- AJAX Volunteer --}}
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const container = document.getElementById('volunteerList');

                    // --- Toggle Volunteer Details ---
                    window.toggleVolunteerDetails = function(id) {
                        const el = document.getElementById('volunteer-' + id);
                        if (!el) return;
                        el.classList.toggle('visible');
                    };

                    // --- Bind Buttons ---
                    function bindVolunteerButtons() {
                        container.querySelectorAll('.link-btn').forEach(btn => {
                            btn.addEventListener('click', () => {
                                const id = btn.dataset.id;
                                if (id) toggleVolunteerDetails(id);
                            });
                        });
                    }

                    // --- Fetch Live Volunteers (Keep toggles open) ---
                    function fetchLiveVolunteers() {

                        const openSections = Array.from(
                            container.querySelectorAll('.details.visible')
                        ).map(el => el.id);


                        fetch('{{ route('volunteers.live') }}', {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                            })
                            .then(res => res.text())
                            .then(html => {
                                container.innerHTML = html;

                                openSections.forEach(id => {
                                    const el = document.getElementById(id);
                                    if (el) el.classList.add('visible');
                                });

                                bindVolunteerButtons();
                            })
                            .catch(err => console.error('Live volunteer fetch error:', err));
                    }


                    bindVolunteerButtons();
                    setInterval(fetchLiveVolunteers, 8000);
                });
            </script>



</body>

</html>
