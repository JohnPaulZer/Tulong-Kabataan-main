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
    <!-- Google Fonts: Playfair Display & Open Sans -->
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
    <!-- Charts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/administrator/eventpage.css') }}">
</head>

<body>
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

            </button>
        </div>
    </header>

    <!-- Sidebar -->
    @include('administrator.partials.main-sidebar')

    <!-- Overlay (mobile) -->
    <div id="sidebarOverlay" class="overlay" aria-hidden="true"></div>

    <!-- Main -->
    <main class="main" role="main">
        <div class="content">

            <!-- Topbar -->
            <div class="topbar">

                <div style="display:flex;gap:8px;align-items:center">
                    <form action="{{ route('createevent') }}" method="GET">
                        <button class="create-btn"><i class="ri-add-line"></i> Create Event</button>
                    </form>
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
                        events.forEach(event => {
                            const statusText = event.querySelector('.status-badge')?.textContent?.toLowerCase() ||
                                '';

                            let status = '';
                            if (statusText.includes('available')) status = 'available';
                            else if (statusText.includes('ongoing')) status = 'ongoing';
                            else if (statusText.includes('ended')) status = 'ended';

                            event.style.display = (currentFilter === 'all' || status === currentFilter) ?
                                '' : 'none';
                        });
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
