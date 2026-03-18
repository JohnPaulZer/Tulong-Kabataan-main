<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tulong Kabataan</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    <!-- Remixicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile/profilevent.css') }}">
</head>

<body>
    @include('profile.partials.universalmodal')
    @include('partials.main-header')
    @include('administrator.partials.loading-screen')

    <div class="usereve-prof-flex">
        <!-- Sidebar -->
        @include('profile.partials.main-sidebar')

        <!-- Main -->
        <main class="prof-main">
            <div class="usereve-container">
                <!-- Header -->
                <header class="usereve-page-header">
                    <div>
                        <h1 class="usereve-page-title">My Volunteer Events</h1>
                        <p class="usereve-page-sub">Manage your event registrations and track your volunteer engagement
                        </p>
                    </div>
                </header>

                <!-- Stats -->
                <section class="usereve-stats-grid" id="userStatsSection">
                    <div class="usereve-card">
                        <div class="usereve-card-head">
                            <div class="usereve-icon-box usereve-icon-blue">
                                <i class="ri-calendar-check-line usereve-ri-xl" style="color:var(--blue-600)"></i>
                            </div>
                            <span id="usereve-joined-trend" class="usereve-trend usereve-pos"></span>
                        </div>
                        <h3 id="usereve-joined-count" class="usereve-metric">0</h3>
                        <p class="usereve-muted">Events Joined</p>
                    </div>

                    <div class="usereve-card">
                        <div class="usereve-card-head">
                            <div class="usereve-icon-box usereve-icon-green">
                                <i class="ri-time-line usereve-ri-xl" style="color:var(--green-600)"></i>
                            </div>
                            <span id="usereve-attended-trend" class="usereve-trend usereve-pos"></span>
                        </div>
                        <h3 id="usereve-attended-count" class="usereve-metric">0</h3>
                        <p class="usereve-muted">Attended Events</p>
                    </div>

                    <div class="usereve-card">
                        <div class="usereve-card-head">
                            <div class="usereve-icon-box usereve-icon-purple">
                                <i class="ri-calendar-todo-line usereve-ri-xl" style="color:var(--purple-600)"></i>
                            </div>
                            <span id="usereve-missed-trend" class="usereve-trend usereve-text-blue"></span>
                        </div>
                        <h3 id="usereve-missed-count" class="usereve-metric">0</h3>
                        <p class="usereve-muted">Missed Events</p>
                    </div>
                </section>

                <!-- Filters + Events LIST -->
                <section class="usereve-panel" id="my-events" tabindex="-1" style="scroll-margin-top: 90px;">
                    <div class="usereve-panel-top">
                        <div class="usereve-tab-group">
                            <button class="usereve-tab usereve-is-active" data-filter="registered">My Registered
                                Events</button>
                            <button class="usereve-tab" data-filter="ongoing">Ongoing Events</button>
                            <button class="usereve-tab" data-filter="attended">Attended</button>
                            <button class="usereve-tab" data-filter="absent">Unattended</button>
                        </div>

                        <div class="usereve-toolbar">
                            <div class="usereve-search">
                                <input type="text" id="eventSearchInput" placeholder="Search events..." />
                                <div class="usereve-icon-left"><i class="ri-search-line"></i></div>
                            </div>
                        </div>
                    </div>

                    <!-- Events List -->
                    <div id="eventListContainer">
                        <div class="usereve-stack">
                            @include('profile.partials.event-list')
                        </div>
                    </div>

                </section>
            </div>
        </main>
    </div>


    <!-- LIVE UPDATES (COMBINED STATS, EVENTS & SEARCH) -->
    <script>
        let currentActiveFilter = 'registered';
        let currentSearchTerm = '';

        // Function to refresh events and stats
        async function refreshEvents() {
            try {
                const response = await fetch('{{ route('profile.refreshEvents') }}', {
                    headers: {
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest"
                    }
                });

                const data = await response.json();
                if (!data.success) return;

                // Update event list
                const container = document.getElementById('eventListContainer');
                if (container && data.html) {
                    // Store current filter and search state
                    const currentFilter = currentActiveFilter;
                    const currentSearch = currentSearchTerm;

                    // Update content
                    container.innerHTML = '<div class="usereve-stack">' + data.html + '</div>';

                    // Restore filter and search state
                    currentActiveFilter = currentFilter;
                    currentSearchTerm = currentSearch;

                    // Update active tab UI
                    updateActiveTabUI();

                    // Apply filters (this will handle showing/hiding no results message)
                    applyFilters();

                    // Re-setup event listeners
                    setupFilterButtons();
                    setupSearchInput();

                    // Set up unregister buttons
                    setupUnregisterButtons();
                }

                // Update stats
                if (data.stats) {
                    updateStats(data.stats);
                }

            } catch (error) {
                console.error('Refresh error:', error);
            }
        }

        // Function to update stats cards
        function updateStats(stats) {
            if (!stats) return;

            // Update counts
            document.getElementById('usereve-joined-count').textContent = stats.joined || 0;
            document.getElementById('usereve-attended-count').textContent = stats.attended || 0;
            document.getElementById('usereve-missed-count').textContent = stats.absent || 0;

            // Update trend texts
            document.getElementById('usereve-joined-trend').textContent = 'No upcoming events';
            document.getElementById('usereve-attended-trend').textContent = 'No events attended yet';
            document.getElementById('usereve-missed-trend').textContent = 'Perfect attendance';
        }

        // Function to update active tab UI
        function updateActiveTabUI() {
            const filterButtons = document.querySelectorAll('.usereve-tab[data-filter]');
            filterButtons.forEach(btn => {
                btn.classList.remove('usereve-is-active');
                if (btn.dataset.filter === currentActiveFilter) {
                    btn.classList.add('usereve-is-active');
                }
            });
        }

        // Function to setup unregister buttons
        function setupUnregisterButtons() {
            document.querySelectorAll('.usereve-btn-danger').forEach(btn => {
                if (btn.onclick && btn.onclick.toString().includes('confirmUnregister')) {
                    // Already has onclick handler, skip
                    return;
                }

                // Extract event ID and title from onclick attribute if exists
                const onclickAttr = btn.getAttribute('onclick');
                if (onclickAttr && onclickAttr.includes('confirmUnregister')) {
                    // Parse the onclick attribute
                    const match = onclickAttr.match(/confirmUnregister\((\d+),\s*'([^']+)'\)/);
                    if (match) {
                        const eventId = match[1];
                        const eventTitle = match[2];
                        btn.onclick = (e) => {
                            e.preventDefault();
                            confirmUnregister(eventId, eventTitle);
                        };
                    }
                }
            });
        }

        // Function to apply both filter and search
        function applyFilters() {
            const container = document.getElementById('eventListContainer');
            if (!container) return;

            const stackContainer = container.querySelector('.usereve-stack');
            if (!stackContainer) return;

            const eventCards = stackContainer.querySelectorAll('.usereve-event-card');
            const serverNoResultsMsg = stackContainer.querySelector('.usereve-no-results-events, .usereve-empty');

            // If there's a "no results" message from the server (initial empty state)
            if (serverNoResultsMsg) {
                // Hide it by default, we'll show it later if needed
                serverNoResultsMsg.style.display = 'none';
            }

            // If there are no event cards at all
            if (eventCards.length === 0) {
                showNoResultsMessage(true); // Show the "no events" message
                return;
            }

            let visibleCount = 0;

            eventCards.forEach(card => {
                const status = card.dataset.status || '';
                const title = card.querySelector('.usereve-event-title')?.textContent.toLowerCase() || '';
                const description = card.querySelector('.usereve-desc')?.textContent.toLowerCase() || '';
                const location = card.querySelector('.usereve-meta:nth-child(2)')?.textContent.toLowerCase() || '';

                // Apply tab filter
                let shouldShow = true;
                switch (currentActiveFilter) {
                    case 'registered':
                        shouldShow = status === 'upcoming' || status === 'ongoing' ||
                            status === 'completed' || status === 'registered';
                        break;
                    case 'ongoing':
                        shouldShow = status === 'ongoing';
                        break;
                    case 'attended':
                        shouldShow = status === 'attended';
                        break;
                    case 'absent':
                        shouldShow = status === 'absent';
                        break;
                }

                // Apply search filter if search term exists
                if (shouldShow && currentSearchTerm) {
                    const searchLower = currentSearchTerm.toLowerCase();
                    const matchesSearch = title.includes(searchLower) ||
                        description.includes(searchLower) ||
                        location.includes(searchLower);
                    shouldShow = matchesSearch;
                }

                // Set display property
                card.style.display = shouldShow ? 'flex' : 'none';
                if (shouldShow) visibleCount++;
            });

            // Show/hide no results message based on visible count
            showNoResultsMessage(visibleCount === 0);
        }

        // Function to show/hide no results message
        function showNoResultsMessage(show) {
            const container = document.getElementById('eventListContainer');
            if (!container) return;

            const stackContainer = container.querySelector('.usereve-stack');
            if (!stackContainer) return;

            // Remove any existing JavaScript-added no-results messages
            const jsNoResults = stackContainer.querySelectorAll('.usereve-no-results, [class*="no-results"]');
            jsNoResults.forEach(el => {
                // Only remove messages that don't come from server
                if (!el.classList.contains('usereve-no-results-events') &&
                    !el.classList.contains('usereve-empty')) {
                    el.remove();
                }
            });

            // Check for server-side empty message
            const serverNoResultsMsg = stackContainer.querySelector('.usereve-no-results-events, .usereve-empty');

            if (show) {
                // If there's already a server-side message, show it
                if (serverNoResultsMsg) {
                    serverNoResultsMsg.style.display = 'block';
                } else {
                    // Otherwise create a new message
                    const noResultsMsg = document.createElement('div');
                    noResultsMsg.className = 'usereve-no-results-events';
                    noResultsMsg.innerHTML = `
                    <i class="ri-search-line"></i>
                    <h3>No events found</h3>
                    <p>Try selecting a different filter or search term</p>
                `;
                    stackContainer.appendChild(noResultsMsg);
                }
            } else {
                // Hide server-side message if it exists
                if (serverNoResultsMsg) {
                    serverNoResultsMsg.style.display = 'none';
                }
                // Remove any JavaScript-added messages
                const jsMessages = stackContainer.querySelectorAll('.usereve-no-results-events:not(.usereve-empty)');
                jsMessages.forEach(el => el.remove());
            }
        }

        // Function to setup filter buttons
        function setupFilterButtons() {
            const filterButtons = document.querySelectorAll('.usereve-tab[data-filter]');
            if (filterButtons.length === 0) return;

            filterButtons.forEach(btn => {
                // Remove existing listeners to prevent duplicates
                btn.removeEventListener('click', filterClickHandler);
                // Add new listener
                btn.addEventListener('click', filterClickHandler);
            });
        }

        // Separate click handler function
        function filterClickHandler() {
            const filter = this.dataset.filter;
            if (!filter) return;

            const filterButtons = document.querySelectorAll('.usereve-tab[data-filter]');
            filterButtons.forEach(b => b.classList.remove('usereve-is-active'));
            this.classList.add('usereve-is-active');

            currentActiveFilter = filter;
            applyFilters();
        }

        // Function to setup search input
        function setupSearchInput() {
            const searchInput = document.getElementById('eventSearchInput');
            if (!searchInput) return;

            // Clear any existing event listeners by cloning and replacing
            const newSearchInput = searchInput.cloneNode(true);
            searchInput.parentNode.replaceChild(newSearchInput, searchInput);

            // Add event listener to new input
            newSearchInput.addEventListener('input', function() {
                currentSearchTerm = this.value.trim();
                applyFilters();
            });

            // Add clear button functionality
            newSearchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    this.value = '';
                    currentSearchTerm = '';
                    applyFilters();
                }
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Apply initial filter immediately
            applyFilters();
            setupFilterButtons();
            setupSearchInput();
            refreshEvents();

            // Auto-refresh every 5 seconds
            setInterval(refreshEvents, 5000);

            // Refresh when tab becomes visible
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) refreshEvents();
            });

            // Add keyboard shortcut for search (Ctrl/Cmd + F)
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    const searchInput = document.getElementById('eventSearchInput');
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                }
            });
        });

        // Expose functions for debugging
        window.applyFilters = applyFilters;
        window.currentSearchTerm = () => currentSearchTerm;
    </script>

    <!-- JS: AJAX Unregister + Toast Integration -->
    <script>
        function showToast(message, color = '#16a34a') {
            const toast = document.getElementById('toast');
            if (!toast) return;
            toast.textContent = message;
            toast.style.background = color;
            toast.style.opacity = '1';
            setTimeout(() => toast.style.opacity = '0', 3000);
        }

        function confirmUnregister(eventId, title) {
            const modal = document.getElementById('confirmModal');
            const confirmBtn = document.getElementById('confirmActionBtn');
            const cancelBtn = document.getElementById('cancelConfirmBtn');
            const titleEl = document.getElementById('confirmModalTitle');
            const msgEl = document.getElementById('confirmModalMessage');

            // Configure modal content
            titleEl.textContent = 'Confirm Unregistration';
            msgEl.textContent = `Are you sure you want to unregister from "${title}"?`;
            confirmBtn.textContent = 'Unregister';
            confirmBtn.style.background = '#dc2626';
            modal.style.display = 'flex';

            cancelBtn.onclick = () => modal.style.display = 'none';

            confirmBtn.onclick = async () => {
                try {
                    const response = await fetch(`/events/${eventId}/unregister`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    modal.style.display = 'none';

                    if (data.success) {
                        // Instantly remove the event card (no animation)
                        const card = document.querySelector(
                            `.usereve-event-card button[onclick*="${eventId}"]`
                        )?.closest('.usereve-event-card');

                        if (card) card.remove();

                        showToast('Successfully unregistered from the event.');
                    } else {
                        showToast(data.message || 'Unregistration failed.', '#dc2626');
                    }
                } catch (error) {
                    console.error(error);
                    modal.style.display = 'none';
                    showToast('Something went wrong. Please try again.', '#dc2626');
                }
            };
        }
    </script>

    <!-- JS: Countdowns -->
    <script id="usereve-countdown-timers">
        document.addEventListener('DOMContentLoaded', function() {
            function startCountdown(elementId, hoursFromNow) {
                const el = document.getElementById(elementId);
                if (!el) return;

                const targetTime = Date.now() + hoursFromNow * 60 * 60 * 1000;

                function render() {
                    const now = Date.now();
                    const distance = targetTime - now;

                    if (distance <= 0) {
                        el.textContent = 'Expired';
                        return;
                    }
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

                    el.textContent = days > 0 ? `${days}d ${hours}h ${minutes}m` : `${hours}h ${minutes}m`;
                }

                render();
                setInterval(render, 60000); // update each minute
            }

            // Same visual values as original page
            startCountdown('usereve-countdown-1', 62.5);
            startCountdown('usereve-countdown-2', 8.25);
        });
    </script>


    <!-- JS: Sidebar dropdown for "Event Volunteer" -->
    <script id="usereve-sidebar-dropdown">
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.querySelector('.menu-parent[aria-controls="ev-submenu"]');
            const submenu = document.getElementById('ev-submenu');
            const linkMyEvents = submenu ? submenu.querySelector('a[href="#my-events"]') : null;

            // Toggle open/close
            if (toggle && submenu) {
                toggle.addEventListener('click', () => {
                    const open = toggle.getAttribute('aria-expanded') === 'true';
                    toggle.setAttribute('aria-expanded', String(!open));
                    submenu.classList.toggle('open', !open);
                });
            }

            // Open submenu and highlight if URL already points to #my-events
            if (location.hash === '#my-events') {
                toggle?.setAttribute('aria-expanded', 'true');
                submenu?.classList.add('open');
                linkMyEvents?.classList.add('usereve-active');
                document.getElementById('my-events')?.focus({
                    preventScroll: true
                });
            }

            // Scroll-spy: highlight "My Events" when the section is in view
            const targetSection = document.getElementById('my-events');
            if (targetSection && linkMyEvents && 'IntersectionObserver' in window) {
                const io = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        linkMyEvents.classList.toggle('usereve-active', entry.isIntersecting);
                        if (entry.isIntersecting) {
                            toggle?.setAttribute('aria-expanded', 'true');
                            submenu?.classList.add('open');
                        }
                    });
                }, {
                    rootMargin: '-40% 0px -55% 0px',
                    threshold: 0.01
                });
                io.observe(targetSection);
            }
        });
    </script>

    <!-- PATCH: tap-to-expand the icon rail on mobile -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rail = document.querySelector('.usereve-prof-sidebar');
            if (!rail) return;

            // First tap inside the rail opens it; second tap activates link/button
            rail.addEventListener('click', function(e) {
                if (!window.matchMedia('(max-width:768px)').matches) return;
                if (!rail.classList.contains('open')) {
                    rail.classList.add('open');
                    if (e.target.closest('a,button')) e.preventDefault();
                }
            });

            // Close when tapping outside
            document.addEventListener('click', function(e) {
                if (!window.matchMedia('(max-width:768px)').matches) return;
                if (!rail.contains(e.target)) rail.classList.remove('open');
            });

            // Close when resizing back to desktop
            window.addEventListener('resize', function() {
                if (!window.matchMedia('(max-width:768px)').matches) rail.classList.remove('open');
            });
        });
    </script>


</body>

</html>
