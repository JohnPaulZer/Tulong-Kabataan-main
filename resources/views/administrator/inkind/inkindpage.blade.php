    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Tulong Kabataan | Administrator Dashboard</title>
        <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png" />
        <!-- Remixicon -->
        <!-- Charts -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('administrator.partials.admin-theme')
    </head>

    <body class="admin-page admin-inkind-page">

        @include('partials.universalmodal')
        <!-- Header --> 
        <header class="header">
            <div class="header__inner">
                <div class="header__left">
                    <button id="sidebarToggle" class="menu-btn" aria-label="Toggle menu">
                        <i class="ri-menu-line"></i>
                    </button>
                    <h1 class="brand">ADMINISTRATOR</h1>
                </div>

                <div class="logo-word">
                    <img src="{{ asset('img/log.png') }}" alt=""
                        style="width: 120px; height: 60px; margin-top: 8px" />
                </div>

                <button class="notif" aria-label="Notifications">
                    <i class="ri-notification-3-line"></i>
                </button>
            </div>
        </header>

        <!-- Sidebar -->
        @include('administrator.partials.loading-screen')
        @include('administrator.partials.main-sidebar')

        <!-- Overlay (mobile) -->
        <div id="sidebarOverlay" class="overlay" aria-hidden="true"></div>
        <main class="main">
            <section class="page-header admin-page-heading">
                <h1>In-Kind Donations</h1>
                <p>Track donated goods, drop-off locations, and distribution reports.</p>
            </section>

            <!-- In-Kind Donation Statistics -->
            <section class="stats-grid">
                <div class="card stat">
                    <div class="stat-body">
                        <div>
                            <p class="muted">Total In-Kind Items</p>
                            <p class="big" id="stat-total-items">{{ $totalItems }}</p>
                            <p class="muted accent" id="stat-total-change">+0% from last month</p>
                        </div>
                        <div class="stat-icon gift">
                            <i class="ri-gift-line"></i>
                        </div>
                    </div>
                </div>

                <div class="card stat">
                    <div class="stat-body">
                        <div>
                            <p class="muted">Upcoming Donations</p>
                            <p class="big" id="stat-upcoming">{{ $upcoming }}</p>
                            <p class="muted warning" id="stat-upcoming-note">Requires attention</p>
                        </div>
                        <div class="stat-icon truck">
                            <i class="ri-truck-line"></i>
                        </div>
                    </div>
                </div>

                <div class="card stat">
                    <div class="stat-body">
                        <div>
                            <p class="muted">Active Locations</p>
                            <p class="big" id="stat-active">{{ $dropoffs->where('is_active', true)->count() }}</p>
                            <p class="muted accent">
                                <span id="stat-total">{{ $dropoffs->count() }}</span> locations available
                            </p>
                        </div>
                        <div class="stat-icon map">
                            <i class="ri-map-pin-line"></i>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Donation Trends Chart -->
            <section class="card">
                <h2>In-Kind Donation Trends</h2>
                <div style="height:240px;">
                    <canvas id="donationChart"></canvas>
                </div>
            </section>

            <!-- Donation by category Chart -->
            <section class="card" style="margin-top: 20px;">
                <div style="height:240px;">
                    <h2>Donations by Category</h2>
                    <canvas id="categoryChart"></canvas>
                </div>
            </section>


            <section class="card locations-card">
                <div class="card-header">
                    <h2>In-Kind Donation Delivery Locations</h2>


                    <!-- Rest of your locations-grid remains the same -->
                    <div class="locations-grid">
                        @forelse($dropoffs as $dropoff)
                            @php
                                $dropoffPayload = [
                                    'name' => $dropoff->name,
                                    'address' => $dropoff->address,
                                    'schedule' => $dropoff->schedule_datetime,
                                    'lat' => $dropoff->latitude,
                                    'lng' => $dropoff->longitude,
                                ];
                            @endphp
                            <article class="location" data-id="{{ $dropoff->dropoff_id }}"
                                onclick='openLocationModal(@js((string) $dropoff->dropoff_id), @js($dropoffPayload))'>

                                <div class="location-head"
                                    style="display:flex;justify-content:space-between;align-items:center">
                                    <h3>{{ $dropoff->name }}</h3>
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <form action="{{ route('location.toggle', $dropoff->dropoff_id) }}"
                                            method="POST" onsubmit="event.preventDefault(); return false;"
                                            onclick="event.stopPropagation()">
                                            @csrf
                                            @method('PUT')
                                            <label class="switch">
                                                <input type="checkbox" onchange="toggleActive(this)"
                                                    {{ $dropoff->is_active ? 'checked' : '' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <span class="status-text"
                                                style="font-size:12px; color: {{ $dropoff->is_active ? '#16a34a' : '#ef4444' }}">
                                                {{ $dropoff->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </form>
                                    </div>
                                </div>

                                <p class="muted small">{{ $dropoff->address }}</p>
                                <p class="muted xsmall">Available: {{ $dropoff->schedule_datetime }}</p>
                            </article>
                        @empty
                            @include('administrator.partials.empty-state', [
                                'icon' => 'ri-map-pin-line',
                                'title' => 'No Drop-Off Locations Yet',
                                'message' => 'Add a delivery location so donors know where to bring in-kind donations.',
                            ])
                        @endforelse
                    </div>

                    <div style="margin-top: 20px;">
                        <button type="button" class="btn primary" onclick="openLocationModal()">
                            <span class="btn-icon"><i class="ri-add-line"></i></span> Add New Location
                        </button>
                    </div>
                </div>

            </section>

            <!-- Map View Modal -->
            <div id="mapViewModal" class="modal" style="display:none;">
                <div class="modal-content" style="width: 80%; max-width: 900px; max-height: 80vh; overflow: hidden;">
                    <span class="close-btn" onclick="closeMapViewModal()">&times;</span>
                    <h3 id="mapViewTitle">Location Map</h3>
                    <div id="viewMap" style="height: 400px; width: 100%; border-radius: 8px;"></div>
                    <div style="margin-top: 15px; display: flex; justify-content: flex-end; gap: 10px;">
                        <button onclick="closeMapViewModal()" class="btn">Close</button>
                        <button onclick="openDirections()" class="btn primary">
                            <i class="ri-navigation-line"></i> Get Directions
                        </button>
                    </div>
                </div>
            </div>

            <!-- Location Add/Edit Modal -->
            <div id="locationModal" class="modal" style="display:none;">
                <div class="modal-content" style="width: 90%; max-width: 900px; max-height: 80vh; overflow-y: auto;">
                    <span class="close-btn" onclick="closeLocationModal()">&times;</span>
                    <h3 id="modalTitle">Add New Location</h3>

                    <div id="modal-message-location"
                        style="display:none; padding:10px; margin-bottom:15px; border-radius:6px; font-size:14px;">
                    </div>

                    <form id="modalDropoffForm">
                        @csrf
                        <input type="hidden" name="dropoff_id" id="modal_dropoff_id">
                        <input type="hidden" name="latitude" id="modal_latitude">
                        <input type="hidden" name="longitude" id="modal_longitude">

                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                                <div style="flex: 1; min-width: 200px;">
                                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Location Name
                                        *</label>
                                    <input type="text" name="name" id="modal_name" placeholder="Location Name"
                                        required class="styled-input" style="width: 100%;">
                                </div>

                                <div style="flex: 1; min-width: 200px;">
                                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Schedule
                                        *</label>
                                    <input type="text" name="schedule_datetime" id="modal_schedule_datetime"
                                        placeholder="Mon-Fri 9AM-5PM" required class="styled-input"
                                        style="width: 100%;">
                                </div>
                            </div>

                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Address *</label>
                                <input type="text" name="address" id="modal_address" placeholder="Full Address"
                                    required class="styled-input" style="width: 100%;">
                            </div>

                            <!-- Map Section -->
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Map Location
                                    *</label>
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 10px;">
                                    Search for a place, click on the map, or drag the marker to set location
                                </div>

                                <!-- Map Search Box -->
                                <div style="margin-bottom: 10px; position: relative;">
                                    <input type="text" id="modalMapSearchBox"
                                        placeholder="Search for places, addresses, landmarks..."
                                        style="width: 100%; padding: 10px 12px 10px 40px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                                    <i class="ri-search-line"
                                        style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6b7280;"></i>
                                </div>

                                <!-- Map Container -->
                                <div id="modal_map"
                                    style="height: 300px; width: 100%; border-radius: 8px; border: 1px solid #d1d5db; margin-bottom: 10px;">
                                </div>

                                <!-- Coordinates Display -->
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <div style="flex: 1;">
                                        <label
                                            style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 3px;">Latitude</label>
                                        <input type="text" id="modal_latDisplay" placeholder="Latitude"
                                            class="styled-input" style="width: 100%;" readonly>
                                    </div>
                                    <div style="flex: 1;">
                                        <label
                                            style="font-size: 12px; color: #6b7280; display: block; margin-bottom: 3px;">Longitude</label>
                                        <input type="text" id="modal_lngDisplay" placeholder="Longitude"
                                            class="styled-input" style="width: 100%;" readonly>
                                    </div>
                                    <button type="button" class="btn small" onclick="locateUserInModal()"
                                        style="margin-top: 20px;">
                                        <i class="ri-user-location-line"></i> Use My Location
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: 20px; justify-content: flex-end;">
                            <button type="button" class="btn" onclick="closeLocationModal()">Cancel</button>
                            <button type="submit" id="modalSubmitBtn" class="btn primary">Add Location</button>
                            <button type="button" id="modalDeleteBtn" class="btn btn-danger" style="display:none;"
                                onclick="confirmDeleteInModal()">
                                <i class="ri-delete-bin-line"></i> Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>




            <div class="table-toolbar">
                <h2>Recent Donations</h2>

                <div class="toolbar-actions">

                    <button onclick="openImpactModal()">
                        <i class="ri-file-chart-line"></i> Add Impact Report
                    </button>

                    <!-- Search bar first -->
                    <div class="search">
                        <i class="ri-search-line search-icon"></i>
                        <input type="text" placeholder="Search donations..." id="donationSearch" />
                    </div>

                    <!-- Minimal Date Filter -->
                    <div class="date-filter" style="position: relative;">
                        <i class="ri-calendar-line"
                            style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6b7280;"></i>
                        <input type="date" id="dateFilter"
                            style="padding: 8px 12px 8px 40px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; min-width: 150px;"
                            onchange="applyDateFilter()" />
                        <button onclick="clearDateFilter()"
                            style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #6b7280; cursor: pointer; display: none;"
                            id="clearDateBtn">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>


                    <!-- Filter Tabs -->
                    <div class="filter-tabs">
                        <button class="filter-btn active" data-status="all" onclick="filterDonations('all')">
                            All Donations
                        </button>
                        <button class="filter-btn" data-status="Scheduled" onclick="filterDonations('Scheduled')">
                            Scheduled
                        </button>
                        <button class="filter-btn" data-status="Received" onclick="filterDonations('Received')">
                            Received
                        </button>
                        <button class="filter-btn" data-status="Distributed"
                            onclick="filterDonations('Distributed')">
                            Distributed
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-wrap">
                <table class="donations-table" cellspacing="0">
                    <thead>
                        <tr>

                            <th>Donor</th>
                            <th>Phone</th>
                            <th>Item</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody id="donations-body">
                        @forelse ($donations as $donation)
                            <tr id="donation-row-{{ $donation->inkind_id }}"
                                data-donation-id="{{ $donation->inkind_id }}"
                                data-donor-name="{{ $donation->donor_name ?? '' }}"
                                data-donor-email="{{ $donation->donor_email ?? '' }}"
                                data-donor-phone="{{ $donation->donor_phone ?? '' }}"
                                data-item-name="{{ $donation->item_name ?? '' }}"
                                data-category="{{ $donation->category ?? '' }}"
                                data-quantity="{{ $donation->quantity ?? '' }}"
                                data-status="{{ $donation->status ?? '' }}">

                                <td>
                                    <div class="donor">
                                        <div class="avatar blue">
                                            @php
                                                // Get the user from the donation
                                                $user = $donation->user;

                                                // Get avatar URL if user exists
                                                $avatar = optional($user)->profile_photo_url ?? null;

                                                if ($avatar) {
                                                    // Process Google avatar URL
                                                    if (
                                                        str_contains($avatar, 'googleusercontent.com') &&
                                                        !str_contains($avatar, 'sz=')
                                                    ) {
                                                        $avatar .= (str_contains($avatar, '?') ? '&' : '?') . 'sz=64';
                                                    }

                                                    // Handle local storage URLs
                                                    if (!Str::startsWith($avatar, 'http')) {
                                                        $avatar = file_url($avatar);
                                                    }
                                                }
                                            @endphp

                                            @if ($avatar)
                                                <img class="user-avatar" src="{{ $avatar }}"
                                                    alt="Profile photo" referrerpolicy="no-referrer">
                                            @else
                                                {{ strtoupper(substr($donation->donor_name ?? 'A', 0, 2)) }}
                                            @endif
                                        </div>
                                        <div class="donor-meta">
                                            <div class="donor-name">
                                                {{ $donation->donor_name ?? 'Anonymous' }}
                                            </div>
                                            <div class="muted xsmall">
                                                {{ $donation->donor_email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="donation-phone">{{ $donation->donor_phone }}</td>
                                <td class="donation-item-name">{{ $donation->item_name }}</td>
                                <td class="donation-category">{{ $donation->category }}</td>
                                <td class="donation-quantity">{{ $donation->quantity }}</td>
                                <td class="donation-location">{{ $donation->dropOffPoint->name ?? 'N/A' }}</td>
                                <td>
                                    <span id="status-badge-{{ $donation->inkind_id }}"
                                        class="badge {{ strtolower($donation->status) }}"
                                        style="display:inline-block;min-width:90px;text-align:center;white-space:nowrap;">
                                        {{ $donation->status }}
                                    </span>
                                </td>

                                <td>
                                    @if ($donation->status === 'Distributed')
                                        <select disabled
                                            style="padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; background: #f3f4f6; color: #6b7280; cursor: not-allowed;">
                                            <option selected>No action required</option>
                                        </select>
                                    @else
                                        <select
                                            data-donation-status-control="{{ $donation->inkind_id }}"
                                            data-current-status="{{ $donation->status }}"
                                            onchange='updateDonationStatus(@json((string) $donation->inkind_id), this.value, this)'
                                            style="padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; cursor: pointer;">
                                            <option value="Scheduled"
                                                {{ $donation->status == 'Scheduled' ? 'selected' : '' }}>Scheduled
                                            </option>
                                            <option value="Received"
                                                {{ $donation->status == 'Received' ? 'selected' : '' }}>Received
                                            </option>
                                        </select>
                                    @endif

                                    <button class="btn btn-sm" data-id="{{ $donation->inkind_id }}"
                                        onclick='openEditModal(@json((string) $donation->inkind_id))'>
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    @include('administrator.partials.empty-state', [
                                        'icon' => 'ri-gift-line',
                                        'title' => 'No Donations Yet',
                                        'message' => 'There are no in-kind donations to display at the moment.',
                                        'class' => 'admin-empty-state--table',
                                    ])
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            <!-- Impact Report Modal -->
            <div id="impactReportModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeImpactModal()">&times;</span>
                    <h2>Create Impact Report</h2>

                    <div id="impact-message" class="message-box"></div>

                    <form id="impactReportForm">
                        <div class="form-group">
                            <label for="impact_title">Report Title *</label>
                            <input type="text" id="impact_title" name="title"
                                placeholder="e.g., School Supplies Distribution">
                        </div>

                        <div class="form-group">
                            <label for="impact_date">Report Date *</label>
                            <input type="date" id="impact_date" name="report_date">
                        </div>

                        <div class="form-group">
                            <label for="impact_description">Description *</label>
                            <textarea id="impact_description" name="description" rows="4"
                                placeholder="Describe the impact of these donations..."></textarea>
                        </div>

                        <!-- Photo Upload Section -->
                        <div class="form-group">
                            <label>Photos (Optional)</label>
                            <div id="photo-upload-area" onclick="document.getElementById('impact_photo').click()">
                                <i class="ri-camera-line"></i>
                                <p>Click to upload photos</p>
                                <p class="subtext">JPG, PNG, GIF (Max 2MB each, up to 5 photos)</p>
                            </div>
                            <input type="file" id="impact_photo" name="photos[]" multiple accept="image/*"
                                onchange="previewImpactPhoto(this)">
                            <div id="impact_photo_preview"></div>
                        </div>

                        <!-- Donation Selection Section -->
                        <div class="form-group">
                            <label class="donation-selection-label">Select Donations to Include *</label>
                            <div class="donation-selection-subtitle">Select one or more received donations that
                                contributed to this impact</div>

                            <!-- Search and Controls -->
                            <div class="search-controls-container">
                                <!-- Search -->
                                <div class="search-box">
                                    <input type="text" id="impactDonationSearch" placeholder="Search donations..."
                                        oninput="filterDonationsInModal(this.value)">
                                    <i class="ri-search-line"></i>
                                </div>

                                <!-- Quick Actions -->
                                <div class="quick-actions">
                                    <button type="button" class="action-btn select-all-btn"
                                        onclick="selectAllDonations()">
                                        <i class="ri-checkbox-circle-line"></i> Select All
                                    </button>
                                    <button type="button" class="action-btn clear-all-btn"
                                        onclick="clearAllDonations()">
                                        <i class="ri-checkbox-blank-line"></i> Clear All
                                    </button>
                                </div>
                            </div>

                            <!-- Donations Container (Will be updated dynamically) -->
                            <div id="donations-container">
                                <!-- Dynamic content will be loaded here -->
                                <div class="loading-state">
                                    <div class="spinner"></div>
                                    <p>Loading donations...</p>
                                </div>
                            </div>

                            <!-- Summary -->
                            <div class="summary-container">
                                <div class="summary-stats">
                                    <div class="summary-item">
                                        <div class="summary-icon donor-icon">
                                            <i class="ri-user-line"></i>
                                        </div>
                                        <div>
                                            <div class="summary-label">Donors</div>
                                            <div id="selected-donors-count" class="summary-value">0</div>
                                        </div>
                                    </div>

                                    <div class="summary-item">
                                        <div class="summary-icon items-icon">
                                            <i class="ri-gift-line"></i>
                                        </div>
                                        <div>
                                            <div class="summary-label">Items</div>
                                            <div id="selected-items-count" class="summary-value">0</div>
                                        </div>
                                    </div>

                                    <div class="summary-item">
                                        <div class="summary-icon categories-icon">
                                            <i class="ri-price-tag-3-line"></i>
                                        </div>
                                        <div>
                                            <div class="summary-label">Categories</div>
                                            <div id="selected-categories-count" class="summary-value">0</div>
                                        </div>
                                    </div>
                                </div>

                                <div id="selected-count" class="selected-count">
                                    0 selected
                                </div>
                            </div>
                        </div>

                        <div class="form-buttons">
                            <button type="button" class="btn btn-secondary"
                                onclick="closeImpactModal()">Cancel</button>
                            <button type="submit" id="impactSubmitBtn" class="btn btn-primary">
                                <i class="ri-file-chart-line"></i> Create Impact Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>


            {{-- MODAL SECTION FOR EDITING --}}

            <div id="editDonationModal" class="modal" style="display:none;">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeEditModal()">&times;</span>
                    <h3>Edit Donation</h3>

                    <div id="modal-message"
                        style="display:none; padding:10px; margin-bottom:15px; border-radius:6px; font-size:14px;">
                    </div>


                    <form id="editDonationForm">
                        @csrf
                        <input type="hidden" id="edit_donation_id" name="id">

                        <label>Donor Name</label>
                        <input type="text" id="edit_donor_name" name="donor_name">

                        <label>Email</label>
                        <input type="text" id="edit_donor_email" name="donor_email">

                        <label>Phone</label>
                        <input type="text" id="edit_donor_phone" name="donor_phone">

                        <label>Item</label>
                        <input type="text" id="edit_item_name" name="item_name">

                        <label>Category</label>
                        <select id="edit_category" name="category">
                            <option value="Food & Groceries">Food & Groceries</option>
                            <option value="Clothing & Accessories">Clothing & Accessories</option>
                            <option value="Home Goods">Home Goods</option>
                            <option value="School Supplies">School Supplies</option>
                            <option value="Medical Supplies">Medical Supplies</option>
                            <option value="Electronics">Electronics</option>
                            <option value="Other">Other</option>
                        </select>

                        <label>Quantity</label>
                        <input type="number" id="edit_quantity" name="quantity">

                        <button type="button" class="btn primary" onclick="saveEditDonation()">Save</button>
                        <button type="button" class="btn" onclick="closeEditModal()">Cancel</button>
                    </form>
                </div>
            </div>


            <div class="table-footer">
                <div class="muted">Showing 1 to 5 of 1,284 donations</div>
                <div class="pagination">
                    <button class="page">Previous</button>
                    <button class="page active">1</button>
                    <button class="page">2</button>
                    <button class="page">3</button>
                    <button class="page">Next</button>
                </div>
            </div>
            </section>
        </main>

        <script>
            function inkindJsArg(value) {
                return JSON.stringify(String(value ?? ''));
            }

            function inkindEscapeHtml(value) {
                const div = document.createElement('div');
                div.textContent = value ?? '';
                return div.innerHTML;
            }

            function setDonationRowData(row, donation) {
                if (!row || !donation) return;

                row.dataset.donorName = donation.donor_name ?? '';
                row.dataset.donorEmail = donation.donor_email ?? '';
                row.dataset.donorPhone = donation.donor_phone ?? '';
                row.dataset.itemName = donation.item_name ?? '';
                row.dataset.category = donation.category ?? '';
                row.dataset.quantity = donation.quantity ?? '';
                row.dataset.status = donation.status ?? '';
            }
        </script>


        {{-- ====================================PAGINATION========================================= --}}
        <script>
            // Configuration
            let currentPage = 1;
            const itemsPerPage = 10; // Change this based on your needs

            // Initialize pagination
            document.addEventListener('DOMContentLoaded', function() {
                setupPagination();
                loadPage(currentPage);
            });

            function setupPagination() {
                const paginationContainer = document.querySelector('.pagination');
                if (!paginationContainer) return;

                // Add event listeners to existing buttons
                const prevBtn = paginationContainer.querySelector('.page:first-child');
                const nextBtn = paginationContainer.querySelector('.page:last-child');
                const pageBtns = paginationContainer.querySelectorAll('.page:not(:first-child):not(:last-child)');

                if (prevBtn) {
                    prevBtn.addEventListener('click', function() {
                        if (currentPage > 1) {
                            loadPage(currentPage - 1);
                        }
                    });
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', function() {
                        loadPage(currentPage + 1);
                    });
                }

                pageBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const pageNum = parseInt(this.textContent);
                        if (!isNaN(pageNum)) {
                            loadPage(pageNum);
                        }
                    });
                });
            }

            async function loadPage(page) {
                try {
                    // Show loading state
                    showLoading(true);

                    // Get current filters
                    const searchTerm = document.getElementById('donationSearch')?.value || '';
                    const activeFilterBtn = document.querySelector('.filter-btn.active');
                    const statusFilter = activeFilterBtn?.dataset.status || 'all';
                    const dateFilter = document.getElementById('dateFilter')?.value || '';

                    // Prepare request data
                    const requestData = {
                        page: page,
                        per_page: itemsPerPage,
                        search: searchTerm,
                        status: statusFilter,
                        date: dateFilter,
                        _token: '{{ csrf_token() }}'
                    };

                    // Make API call
                    const response = await fetch('/administrator/donations/paginate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(requestData)
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Update table body
                        updateTable(data.donations);

                        // Update pagination controls
                        updatePaginationControls(data.pagination);

                        // Update showing text
                        updateShowingText(data.pagination);

                        currentPage = page;
                    } else {
                        throw new Error(data.message || 'Failed to load page');
                    }
                } catch (error) {
                    console.error('Pagination error:', error);
                    UniversalModalManager.showToast('Failed to load donations: ' + error.message, 'error');
                } finally {
                    showLoading(false);
                }
            }

            function updateTable(donations) {
                const tbody = document.getElementById('donations-body');

                if (donations.length === 0) {
                    tbody.innerHTML = `
            <tr>
                <td colspan="8">
                    <div class="admin-empty-state admin-empty-state--table">
                        <div class="admin-empty-state__icon" aria-hidden="true">
                            <i class="ri-search-line"></i>
                        </div>
                        <h3>No Donations Found</h3>
                        <p>No in-kind donations match the current search or filter.</p>
                    </div>
                </td>
            </tr>
        `;
                    return;
                }

                let html = '';

                donations.forEach(donation => {
                    const id = String(donation.id ?? donation.inkind_id ?? '');
                    const status = donation.status || 'Scheduled';
                    const jsId = inkindJsArg(id);

                    // Build avatar HTML
                    const avatarHtml = donation.avatar_url ?
                        `<img class="user-avatar" src="${inkindEscapeHtml(donation.avatar_url)}" alt="Profile photo" referrerpolicy="no-referrer">` :
                        `<span>${donation.donor_name ? inkindEscapeHtml(donation.donor_name.substring(0, 2).toUpperCase()) : 'AN'}</span>`;

                    // Build status dropdown based on current status
                    let statusDropdown = '';
                    if (status === 'Distributed') {
                        statusDropdown = `
                <select disabled style="padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; background: #f3f4f6; color: #6b7280; cursor: not-allowed;">
                    <option selected>No action required</option>
                </select>
            `;
                    } else {
                        statusDropdown = `
                <select data-donation-status-control="${inkindEscapeHtml(id)}" data-current-status="${inkindEscapeHtml(status)}" onchange='updateDonationStatus(${jsId}, this.value, this)' style="padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; cursor: pointer;">
                    <option value="Scheduled" ${status === 'Scheduled' ? 'selected' : ''}>Scheduled</option>
                    <option value="Received" ${status === 'Received' ? 'selected' : ''}>Received</option>
                </select>
            `;
                    }

                    // Build the row HTML
                    html += `
            <tr id="donation-row-${inkindEscapeHtml(id)}"
                data-donation-id="${inkindEscapeHtml(id)}"
                data-date="${inkindEscapeHtml(donation.created_at || '')}"
                data-donor-name="${inkindEscapeHtml(donation.donor_name || '')}"
                data-donor-email="${inkindEscapeHtml(donation.donor_email || '')}"
                data-donor-phone="${inkindEscapeHtml(donation.donor_phone || '')}"
                data-item-name="${inkindEscapeHtml(donation.item_name || '')}"
                data-category="${inkindEscapeHtml(donation.category || '')}"
                data-quantity="${inkindEscapeHtml(donation.quantity ?? '')}"
                data-status="${inkindEscapeHtml(status)}">
                <td>
                    <div class="donor">
                        <div class="avatar blue">
                            ${avatarHtml}
                        </div>
                        <div class="donor-meta">
                            <div class="donor-name">
                                ${inkindEscapeHtml(donation.donor_name || 'Anonymous')}
                            </div>
                            <div class="muted xsmall">
                                ${inkindEscapeHtml(donation.donor_email || '')}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="donation-phone">${inkindEscapeHtml(donation.donor_phone || '')}</td>
                <td class="donation-item-name">${inkindEscapeHtml(donation.item_name || '')}</td>
                <td class="donation-category">${inkindEscapeHtml(donation.category || '')}</td>
                <td class="donation-quantity">${inkindEscapeHtml(donation.quantity ?? '')}</td>
                <td class="donation-location">${inkindEscapeHtml(donation.dropoff_name || 'N/A')}</td>
                <td>
                    <span id="status-badge-${inkindEscapeHtml(id)}" class="badge ${inkindEscapeHtml(status.toLowerCase())}" style="display:inline-block;min-width:90px;text-align:center;white-space:nowrap;">
                        ${inkindEscapeHtml(status)}
                    </span>
                </td>
                <td>
                    ${statusDropdown}
                    <button class="btn btn-sm" data-id="${inkindEscapeHtml(id)}" onclick='openEditModal(${jsId})'>
                        Edit
                    </button>
                </td>
            </tr>
        `;
                });

                tbody.innerHTML = html;
            }

            function updatePaginationControls(pagination) {
                const paginationContainer = document.querySelector('.pagination');
                if (!paginationContainer) return;

                let html = '';

                // Previous button
                html +=
                    `<button class="page ${pagination.current_page === 1 ? 'disabled' : ''}" ${pagination.current_page === 1 ? 'disabled' : ''}>Previous</button>`;

                // Page numbers
                const maxVisiblePages = 3;
                let startPage = Math.max(1, pagination.current_page - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(pagination.last_page, startPage + maxVisiblePages - 1);

                // Adjust if we're near the end
                if (endPage - startPage + 1 < maxVisiblePages) {
                    startPage = Math.max(1, endPage - maxVisiblePages + 1);
                }

                // First page if not visible
                if (startPage > 1) {
                    html += `<button class="page" data-page="1">1</button>`;
                    if (startPage > 2) html += `<span class="page-dots">...</span>`;
                }

                // Visible pages
                for (let i = startPage; i <= endPage; i++) {
                    html +=
                        `<button class="page ${i === pagination.current_page ? 'active' : ''}" data-page="${i}">${i}</button>`;
                }

                // Last page if not visible
                if (endPage < pagination.last_page) {
                    if (endPage < pagination.last_page - 1) html += `<span class="page-dots">...</span>`;
                    html += `<button class="page" data-page="${pagination.last_page}">${pagination.last_page}</button>`;
                }

                // Next button
                html +=
                    `<button class="page ${pagination.current_page === pagination.last_page ? 'disabled' : ''}" ${pagination.current_page === pagination.last_page ? 'disabled' : ''}>Next</button>`;

                paginationContainer.innerHTML = html;

                // Reattach event listeners
                setTimeout(() => {
                    const pageBtns = paginationContainer.querySelectorAll('.page');
                    pageBtns.forEach(btn => {
                        if (!btn.disabled && !btn.classList.contains('disabled') && !btn.classList.contains(
                                'page-dots')) {
                            btn.addEventListener('click', function() {
                                const pageNum = this.dataset.page ? parseInt(this.dataset.page) :
                                    this.textContent === 'Previous' ? currentPage - 1 :
                                    this.textContent === 'Next' ? currentPage + 1 :
                                    parseInt(this.textContent);

                                if (!isNaN(pageNum)) {
                                    loadPage(pageNum);
                                }
                            });
                        }
                    });
                }, 0);
            }

            function updateShowingText(pagination) {
                const showingElement = document.querySelector('.table-footer .muted');
                if (!showingElement) return;

                const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
                const end = Math.min(pagination.current_page * pagination.per_page, pagination.total);

                if (pagination.total === 0) {
                    showingElement.textContent = 'No donations to display';
                    return;
                }

                showingElement.textContent = `Showing ${start} to ${end} of ${pagination.total} donations`;
            }

            function showLoading(show) {
                const tbody = document.getElementById('donations-body');
                const pagination = document.querySelector('.pagination');

                if (show) {
                    // Save current content
                    tbody.dataset.originalContent = tbody.innerHTML;

                    tbody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px;">
                    <div class="spinner" style="display: inline-block; width: 30px; height: 30px; border: 3px solid #e5e7eb; border-top-color: #3b82f6; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    <p style="margin-top: 15px; color: #6b7280;">Loading donations...</p>
                </td>
            </tr>
        `;

                    if (pagination) pagination.style.opacity = '0.5';
                } else {
                    if (pagination) pagination.style.opacity = '1';
                }
            }

            // Add some CSS for the loading spinner
            const style = document.createElement('style');
            style.textContent = `
    @keyframes spin { to { transform: rotate(360deg); } }
    .page.disabled { opacity: 0.5; cursor: not-allowed; }
    .page-dots { padding: 8px 12px; color: #6b7280; }
`;
            document.head.appendChild(style);

            // Refresh function to reload current page
            function refreshDonations() {
                loadPage(currentPage);
            }

            // Modify existing filter functions to use pagination
            document.addEventListener('DOMContentLoaded', function() {
                // Update search input to trigger pagination
                const searchInput = document.getElementById('donationSearch');
                if (searchInput) {
                    let searchTimeout;
                    searchInput.addEventListener('input', function() {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => {
                            loadPage(1);
                        }, 500); // Debounce 500ms
                    });
                }

                // Update filter tabs to trigger pagination
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        currentPage = 1;
                        loadPage(1);
                    });
                });

                // Update date filter to trigger pagination
                const dateFilter = document.getElementById('dateFilter');
                if (dateFilter) {
                    dateFilter.addEventListener('change', function() {
                        currentPage = 1;
                        loadPage(1);
                    });
                }
            });
        </script>

        <!--  Sidebar toggle behaviour -->
        <script>
            (function() {
                const sidebar = document.getElementById("sidebar");
                const toggle = document.getElementById("sidebarToggle");
                const overlay = document.getElementById("sidebarOverlay");
                const main = document.getElementById("mainContent");

                function isLarge() {
                    return window.innerWidth >= 1024;
                }

                function showSidebar() {
                    if (isLarge()) {
                        sidebar.classList.remove("collapsed");
                        sidebar.classList.remove("visible");
                        overlay.classList.remove("visible");
                        main.classList.remove("fullwidth");
                    } else {
                        sidebar.classList.add("visible");
                        overlay.classList.add("visible");
                        main.classList.add("fullwidth");
                    }
                }

                function hideSidebar() {
                    if (isLarge()) {
                        sidebar.classList.remove("collapsed");
                        overlay.classList.remove("visible");
                        main.classList.remove("fullwidth");
                    } else {
                        sidebar.classList.remove("visible");
                        overlay.classList.remove("visible");
                        main.classList.remove("fullwidth");
                    }
                }

                // Initialize state
                if (!isLarge()) {
                    sidebar.classList.add("collapsed");
                } else {
                    sidebar.classList.remove("collapsed");
                }

                toggle.addEventListener("click", function() {
                    if (
                        sidebar.classList.contains("visible") ||
                        sidebar.classList.contains("collapsed")
                    ) {
                        // toggle to show
                        if (sidebar.classList.contains("visible")) {
                            hideSidebar();
                            sidebar.classList.add("collapsed");
                        } else {
                            showSidebar();
                            sidebar.classList.remove("collapsed");
                        }
                    } else {
                        // if neither, decide by size
                        if (isLarge()) sidebar.classList.add("collapsed");
                        else showSidebar();
                    }
                });

                overlay.addEventListener("click", function() {
                    hideSidebar();
                    sidebar.classList.add("collapsed");
                });

                // adapt on resize
                window.addEventListener("resize", function() {
                    if (isLarge()) {
                        sidebar.classList.remove("collapsed");
                        sidebar.classList.remove("visible");
                        overlay.classList.remove("visible");
                        main.classList.remove("fullwidth");
                    } else {
                        sidebar.classList.add("collapsed");
                    }
                });
            })();
        </script>

        {{-- CHART SCRIPT --}}
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const ctx = document.getElementById("donationChart").getContext("2d");
                let donationChart;

                function fetchInKindDonations() {
                    fetch("/administrator/donations/chart-data", {
                            headers: {
                                "X-Requested-With": "XMLHttpRequest"
                            }
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (!res.success || !res.data) return;

                            const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct",
                                "Nov", "Dec"
                            ];
                            const monthlyTotals = Array(12).fill(0);

                            res.data.forEach(row => {
                                const index = row.month - 1;
                                if (index >= 0 && index < 12) monthlyTotals[index] = row.total_items;
                            });


                            if (donationChart) {
                                donationChart.data.datasets[0].data = monthlyTotals;
                                donationChart.update("none");
                                return;
                            }


                            donationChart = new Chart(ctx, {
                                type: "line",
                                data: {
                                    labels: months,
                                    datasets: [{
                                        label: "Items Donated",
                                        data: monthlyTotals,
                                        borderColor: "#3b82f6",
                                        backgroundColor: "rgba(59,130,246,0.1)",
                                        borderWidth: 3,
                                        fill: true,
                                        tension: 0.4,
                                        pointRadius: 4,
                                        pointHoverRadius: 6,
                                        pointBackgroundColor: "#3b82f6"
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: true,
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            backgroundColor: "#ffffff",
                                            borderColor: "#e5e7eb",
                                            borderWidth: 1,
                                            displayColors: false,
                                            padding: 10,
                                            titleColor: "#111827",
                                            bodyColor: "#111827",
                                            titleFont: {
                                                size: 14,
                                                weight: "bold"
                                            },
                                            bodyFont: {
                                                size: 14
                                            },
                                            callbacks: {
                                                title: ctx => ctx[0].label,
                                                label: ctx => ctx.formattedValue.replace(
                                                    /\B(?=(\d{3})+(?!\d))/g, ",") + " items"
                                            }
                                        },
                                        title: {
                                            display: true,
                                            text: "Monthly In-Kind Donations",
                                            color: "#111827",
                                            font: {
                                                size: 16,
                                                weight: "bold"
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: {
                                                display: false
                                            },
                                            ticks: {
                                                color: "#6b7280"
                                            }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                color: "#6b7280",
                                                callback: val => val.toLocaleString()
                                            },
                                            grid: {
                                                color: "#f3f4f6"
                                            }
                                        }
                                    }
                                }
                            });
                        })
                        .catch(err => console.error("Chart load error:", err));
                }


                fetchInKindDonations();


                setInterval(fetchInKindDonations, 10000);
            });
        </script>

        {{-- CATEGORY CHART SCRIPT --}}
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const ctx2 = document.getElementById("categoryChart").getContext("2d");
                let categoryChart;

                function fetchCategoryData() {
                    fetch("/administrator/donations/category-chart-data", {
                            headers: {
                                "X-Requested-With": "XMLHttpRequest"
                            }
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (!res.success || !res.data) return;

                            const labels = res.data.map(d => d.category);
                            const values = res.data.map(d => d.total_items);
                            const colors = [
                                "#3b82f6", "#10b981", "#f59e0b", "#ef4444", "#8b5cf6", "#06b6d4"
                            ];


                            if (categoryChart) {
                                categoryChart.data.labels = labels;
                                categoryChart.data.datasets[0].data = values;
                                categoryChart.update("none");
                                return;
                            }


                            categoryChart = new Chart(ctx2, {
                                type: "bar",
                                data: {
                                    labels,
                                    datasets: [{
                                        label: "Total Items",
                                        data: values,
                                        backgroundColor: colors.slice(0, labels.length),
                                        borderColor: "#1e3a8a",
                                        borderWidth: 1,
                                        borderRadius: 6
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: true,
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        title: {
                                            display: true,
                                            text: "Total Items Donated per Category",
                                            color: "#111827",
                                            font: {
                                                size: 16,
                                                weight: "bold"
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: "#fff",
                                            borderColor: "#e5e7eb",
                                            borderWidth: 1,
                                            titleColor: "#111827",
                                            bodyColor: "#111827",
                                            callbacks: {
                                                label: ctx =>
                                                    `${ctx.label}: ${ctx.formattedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ",")} items`
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: {
                                                display: false
                                            },
                                            ticks: {
                                                color: "#6b7280"
                                            }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                color: "#6b7280",
                                                callback: val => val.toLocaleString()
                                            },
                                            grid: {
                                                color: "#f3f4f6"
                                            }
                                        }
                                    }
                                }
                            });
                        })
                        .catch(err => console.error("Category chart load error:", err));
                }

                // Initial + auto refresh
                fetchCategoryData();
                setInterval(fetchCategoryData, 10000);
            });
        </script>


        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const sidebarLinks = document.querySelectorAll(".side-link");
                // Get current page filename
                const currentPage = window.location.pathname.split("/").pop();

                sidebarLinks.forEach((link) => {
                    // Remove active from all
                    link.classList.remove("active");
                    // If href matches current page, set active
                    if (
                        link.getAttribute("href") &&
                        link.getAttribute("href").includes(currentPage)
                    ) {
                        link.classList.add("active");
                    }
                    // Also allow click to set active visually (for hash or # links)
                    link.addEventListener("click", function() {
                        sidebarLinks.forEach((l) =>
                            l.classList.remove("active")
                        );
                        link.classList.add("active");
                    });
                });
            });
        </script>


        {{-- =========================In-KIND TABLE SCRIPT================================ --}}
        <script>
            function updateDonationStatus(id, status, control = null) {
                id = String(id ?? '');
                const previousStatus = control?.dataset.currentStatus || '';

                fetch("{{ route('donations.updateStatus') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-Requested-With": "XMLHttpRequest"
                        },
                        body: JSON.stringify({
                            id: id,
                            status: status
                        })
                    })
                    .then(async response => {
                        const data = await response.json().catch(() => ({}));
                        if (!response.ok) {
                            throw new Error(data.message || 'Failed to update status.');
                        }
                        return data;
                    })
                    .then(data => {
                        if (data.success) {
                            const badge = document.getElementById("status-badge-" + id);
                            if (badge) {
                                badge.textContent = data.new_status;

                                // Remove old classes
                                badge.classList.remove("scheduled", "received", "distributed", "cancelled");

                                // Add new class
                                badge.classList.add(data.new_status.toLowerCase());
                            }

                            const row = document.getElementById("donation-row-" + id);
                            if (row) row.dataset.status = data.new_status;
                            if (control) control.dataset.currentStatus = data.new_status;

                            // If status is now Distributed, disable the dropdown
                            if (data.new_status === 'Distributed') {
                                const select = control || document.querySelector(
                                    `[data-donation-status-control="${id}"]`);
                                if (select) {
                                    // Create disabled dropdown
                                    const newSelect = document.createElement('select');
                                    newSelect.disabled = true;
                                    newSelect.style.padding = '4px 8px';
                                    newSelect.style.border = '1px solid #d1d5db';
                                    newSelect.style.borderRadius = '6px';
                                    newSelect.style.fontSize = '14px';
                                    newSelect.style.background = '#f3f4f6';
                                    newSelect.style.color = '#6b7280';
                                    newSelect.style.cursor = 'not-allowed';

                                    const option = document.createElement('option');
                                    option.selected = true;
                                    option.textContent = 'No action required';
                                    newSelect.appendChild(option);

                                    // Replace the old select with disabled one
                                    select.parentNode.replaceChild(newSelect, select);
                                }
                            }

                            UniversalModalManager.showToast(`Donation marked as ${data.new_status}.`, 'success');
                            if (typeof refreshDonations === 'function') {
                                refreshDonations();
                            }
                        } else {
                            if (control && previousStatus) control.value = previousStatus;
                            UniversalModalManager.showToast('Failed to update status.', 'error');
                        }
                    })
                    .catch(error => {
                        if (control && previousStatus) control.value = previousStatus;
                        console.error("Error:", error);
                        UniversalModalManager.showToast(error.message || 'Failed to update status.', 'error');
                    });
            }
        </script>

        {{-- ===============================EDIT MODAL FUNCTION================================= --}}
        <script>
            function showModalMessage(message, type = 'success') {
                UniversalModalManager.showToast(message, type);
            }

            function openEditModal(id) {
                id = String(id ?? '');
                const row = document.getElementById("donation-row-" + id);
                if (!row) {
                    showModalMessage('Unable to find this donation row. Please refresh the page.', 'error');
                    return;
                }

                document.getElementById("edit_donation_id").value = id;
                document.getElementById("edit_donor_name").value = row.dataset.donorName || '';
                document.getElementById("edit_donor_email").value = row.dataset.donorEmail || '';
                document.getElementById("edit_donor_phone").value = row.dataset.donorPhone || '';
                document.getElementById("edit_item_name").value = row.dataset.itemName || '';
                document.getElementById("edit_category").value = row.dataset.category || '';
                document.getElementById("edit_quantity").value = row.dataset.quantity || '';

                // Hide message when opening modal
                document.getElementById('modal-message').style.display = 'none';

                document.getElementById("editDonationModal").style.display = "flex";
            }

            function closeEditModal() {
                document.getElementById("editDonationModal").style.display = "none";
                document.getElementById('modal-message').style.display = 'none';
            }

            function saveEditDonation() {
                const donationId = document.getElementById("edit_donation_id").value;

                const payload = {
                    donor_name: document.getElementById("edit_donor_name").value,
                    donor_email: document.getElementById("edit_donor_email").value,
                    donor_phone: document.getElementById("edit_donor_phone").value,
                    item_name: document.getElementById("edit_item_name").value,
                    category: document.getElementById("edit_category").value,
                    quantity: parseInt(document.getElementById("edit_quantity").value)
                };

                fetch(`/administrator/kind-donations/${donationId}/update`, {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "X-Requested-With": "XMLHttpRequest",
                        },
                        body: JSON.stringify(payload),
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log("Update response:", data);

                        if (data.success) {
                            // Update row cells directly
                            const row = document.getElementById(`donation-row-${donationId}`);
                            if (row) {
                                setDonationRowData(row, data.donation);
                                row.querySelector(".donor-name").textContent = data.donation.donor_name ?? "Anonymous";
                                row.querySelector(".muted.xsmall").textContent = data.donation.donor_email ?? "";
                                row.querySelector(".donation-phone").textContent = data.donation.donor_phone ?? "";
                                row.querySelector(".donation-item-name").textContent = data.donation.item_name ?? "";
                                row.querySelector(".donation-category").textContent = data.donation.category ?? "";
                                row.querySelector(".donation-quantity").textContent = data.donation.quantity ?? "";
                            }

                            // Show success message instead of alert
                            showModalMessage(data.message || 'Donation updated successfully!', 'success');


                            setTimeout(() => {
                                closeEditModal();
                            }, 500);
                        } else {
                            showModalMessage("Failed to update donation: " + (data.message || "Unknown error"), 'error');
                        }
                    })
                    .catch(err => {
                        console.error("Error:", err);
                        showModalMessage("Error updating donation. Please try again.", 'error');
                    });
            }
        </script>

        {{-- ====================================FILTER TABS========================================= --}}
        <script>
            function filterDonations(status) {

                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('active');
                });

                const activeButton = (typeof event !== 'undefined' && event.target?.closest?.('.filter-btn')) ||
                    document.querySelector(`.filter-btn[data-status="${status}"]`);
                if (activeButton) activeButton.classList.add('active');


                const rows = document.querySelectorAll('#donations-body tr');

                rows.forEach(row => {
                    if (status === 'all') {

                        row.style.display = '';
                    } else {

                        const statusBadge = row.querySelector('[id^="status-badge-"]');
                        const rowStatus = statusBadge ? statusBadge.textContent.trim() : '';


                        if (rowStatus === status) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            }
        </script>

        {{-- ====================================LIVE UPDATE DONATIONS========================================= --}}

        <script>
            let currentFilter = 'all'; // Track current filter
            let lastDonationCount = {{ $donations->count() }};



            function refreshDonations() {
                fetch('/administrator/donations/latest', {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {

                            if (data.stats) {
                                const s = data.stats;

                                const totalItems = document.getElementById('stat-total-items');
                                const totalChange = document.getElementById('stat-total-change');
                                const upcoming = document.getElementById('stat-upcoming');
                                const upcomingNote = document.getElementById('stat-upcoming-note');

                                if (totalItems) totalItems.textContent = s.total_items;
                                if (totalChange) totalChange.textContent = `+${s.percentage}% from last month`;
                                if (upcoming) upcoming.textContent = s.upcoming;
                                if (upcomingNote) {
                                    upcomingNote.textContent =
                                        s.upcoming > 0 ? 'Incoming donations scheduled' : 'No pending donations';
                                }
                            }


                            if (data.donations.length > lastDonationCount) {
                                updateDonationsTable(data.donations);
                                showNotification('New donation received!');
                                lastDonationCount = data.donations.length;
                            } else if (data.donations.length !== lastDonationCount) {
                                updateDonationsTable(data.donations);
                                lastDonationCount = data.donations.length;
                            }
                        }
                    })
                    .catch(err => console.error('Error refreshing donations:', err));
            }

            // Function to update the table
            function updateDonationsTable(donations) {
                const tbody = document.getElementById('donations-body');
                let html = '';

                donations.forEach(donation => {
                    const id = String(donation.inkind_id ?? donation.id ?? '');
                    const status = donation.status || 'Scheduled';
                    const jsId = inkindJsArg(id);
                    const dropoffName = donation.dropoff_name || donation.drop_off_point?.name || 'N/A';
                    const statusDropdown = status === 'Distributed' ?
                        `<select disabled style="padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; background: #f3f4f6; color: #6b7280; cursor: not-allowed;">
                            <option selected>No action required</option>
                        </select>` :
                        `<select data-donation-status-control="${inkindEscapeHtml(id)}" data-current-status="${inkindEscapeHtml(status)}" onchange='updateDonationStatus(${jsId}, this.value, this)'>
                            <option value="Scheduled" ${status === 'Scheduled' ? 'selected' : ''}>Scheduled</option>
                            <option value="Received" ${status === 'Received' ? 'selected' : ''}>Received</option>
                        </select>`;

                    html += `
                <tr id="donation-row-${inkindEscapeHtml(id)}"
                    data-donation-id="${inkindEscapeHtml(id)}"
                    data-donor-name="${inkindEscapeHtml(donation.donor_name || '')}"
                    data-donor-email="${inkindEscapeHtml(donation.donor_email || '')}"
                    data-donor-phone="${inkindEscapeHtml(donation.donor_phone || '')}"
                    data-item-name="${inkindEscapeHtml(donation.item_name || '')}"
                    data-category="${inkindEscapeHtml(donation.category || '')}"
                    data-quantity="${inkindEscapeHtml(donation.quantity ?? '')}"
                    data-status="${inkindEscapeHtml(status)}">
                    <td>
                        <div class="donor">
                            <div class="avatar blue">
                                ${donation.donor_name ? inkindEscapeHtml(donation.donor_name.substring(0, 2).toUpperCase()) : 'AN'}
                            </div>
                            <div class="donor-meta">
                                <div class="donor-name">
                                    ${inkindEscapeHtml(donation.donor_name || 'Anonymous')}
                                </div>
                                <div class="muted xsmall">
                                    ${inkindEscapeHtml(donation.donor_email || '')}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="donation-phone">${inkindEscapeHtml(donation.donor_phone || '')}</td>
                    <td class="donation-item-name">${inkindEscapeHtml(donation.item_name || '')}</td>
                    <td class="donation-category">${inkindEscapeHtml(donation.category || '')}</td>
                    <td class="donation-quantity">${inkindEscapeHtml(donation.quantity ?? '')}</td>
                    <td class="donation-location">${inkindEscapeHtml(dropoffName)}</td>
                    <td>
                        <span id="status-badge-${inkindEscapeHtml(id)}"
                            class="badge ${inkindEscapeHtml(status.toLowerCase())}"
                            style="display:inline-block;min-width:90px;text-align:center;white-space:nowrap;">
                            ${inkindEscapeHtml(status)}
                        </span>
                    </td>
                    <td>
                        ${statusDropdown}
                        <button class="btn btn-sm" data-id="${inkindEscapeHtml(id)}" onclick='openEditModal(${jsId})'>
                            Edit
                        </button>
                    </td>
                </tr>
            `;
                });

                tbody.innerHTML = html;

                // Reapply current filter
                if (currentFilter !== 'all') {
                    filterDonations(currentFilter);
                }
            }

            // Show notification
            function showNotification(message) {
                UniversalModalManager.showToast(message, 'info', 3000);
            }

            // Update filter function to track current filter
            const originalFilterDonations = filterDonations;
            filterDonations = function(status) {
                currentFilter = status;
                originalFilterDonations(status);
            };

            // Start auto-refresh every 10 seconds
            setInterval(refreshDonations, 5000);

            // Optional: Refresh on page visibility change
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    refreshDonations();
                }
            });
        </script>

        {{-- ====================CONSOLIDATED LEAFLET MAPS MODULE============================ --}}
        <script>
            const LeafletMapsManager = {
                defaultLocation: {
                    latitude: 12.8797,
                    longitude: 121.7740
                },
                userLocation: null,
                defaultZoom: 6,
                detailZoom: 16,
                instances: {},
                currentDirectionsCoordinate: null,

                initMap: function(elementId, options = {}) {
                    if (!window.TKLeafletMaps) return null;

                    const normalizedCenter = window.TKLeafletMaps.normalizeCoordinate(options.center);
                    const defaultCenter = normalizedCenter || this.userLocation || this.defaultLocation;
                    const isViewMap = elementId === 'viewMap';
                    const controller = isViewMap ?
                        window.TKLeafletMaps.mountStatic({
                            elementId,
                            center: defaultCenter,
                            zoom: options.zoom || this.detailZoom,
                            height: options.height || '400px',
                            markers: [{
                                id: elementId,
                                coordinate: defaultCenter,
                                title: options.title || 'Location'
                            }]
                        }) :
                        window.TKLeafletMaps.mountPicker({
                            elementId,
                            initialCoordinate: defaultCenter,
                            latInputId: 'modal_latitude',
                            lngInputId: 'modal_longitude',
                            addressInputId: options.addressFieldId || 'modal_address',
                            searchInputId: options.searchInputId || null,
                            popupTitle: options.title || 'Drop-off location',
                            height: options.height || '300px',
                            debounceSearch: true,
                            onSelect: ({
                                coordinate,
                                address
                            }) => {
                                this.updateCoordinateFields(coordinate);
                                if (options.addressFieldId && address) {
                                    const addressField = document.getElementById(options.addressFieldId);
                                    if (addressField) addressField.value = address;
                                }
                            }
                        });

                    this.instances[elementId] = {
                        controller
                    };
                    return this.instances[elementId];
                },

                updateCoordinateFields: function(coordinate) {
                    const latField = document.getElementById('modal_latitude');
                    const lngField = document.getElementById('modal_longitude');
                    const latDisplay = document.getElementById('modal_latDisplay');
                    const lngDisplay = document.getElementById('modal_lngDisplay');

                    if (latField) latField.value = coordinate.latitude;
                    if (lngField) lngField.value = coordinate.longitude;
                    if (latDisplay) latDisplay.value = coordinate.latitude.toFixed(6);
                    if (lngDisplay) lngDisplay.value = coordinate.longitude.toFixed(6);
                },

                updateCoordinates: function(mapInstanceId, lat, lng) {
                    const coordinate = window.TKLeafletMaps?.normalizeCoordinate({
                        latitude: lat,
                        longitude: lng
                    });
                    if (!coordinate) return;

                    const instance = this.instances[mapInstanceId];
                    if (instance?.controller?.setCoordinate) {
                        const addressValue = document.getElementById('modal_address')?.value || '';
                        instance.controller.setCoordinate(coordinate, addressValue);
                    }

                    this.updateCoordinateFields(coordinate);
                },

                clearCoordinates: function() {
                    const latField = document.getElementById('modal_latitude');
                    const lngField = document.getElementById('modal_longitude');
                    const latDisplay = document.getElementById('modal_latDisplay');
                    const lngDisplay = document.getElementById('modal_lngDisplay');

                    if (latField) latField.value = '';
                    if (lngField) lngField.value = '';
                    if (latDisplay) latDisplay.value = '';
                    if (lngDisplay) lngDisplay.value = '';

                    const instance = this.instances['modal_map'];
                    if (instance?.controller?.clearCoordinate) {
                        const addressField = document.getElementById('modal_address');
                        const addressValue = addressField?.value || '';
                        instance.controller.clearCoordinate();
                        if (addressField && addressValue) addressField.value = addressValue;
                    }
                },

                reverseGeocode: function(lat, lng, addressFieldId) {
                    const coordinate = window.TKLeafletMaps?.normalizeCoordinate({
                        latitude: lat,
                        longitude: lng
                    });
                    if (!coordinate) return Promise.resolve(null);

                    return window.TKLeafletMaps.reverseGeocode(coordinate)
                        .then((address) => {
                            const addressField = document.getElementById(addressFieldId);
                            if (address && addressField) addressField.value = address;
                            return address;
                        })
                        .catch(() => null);
                },

                locateUser: function(mapInstanceId, addressFieldId = null) {
                    return window.TKLeafletMaps.locateUser().then((detail) => {
                        this.userLocation = detail.coordinate;
                        this.updateCoordinates(mapInstanceId, detail.coordinate.latitude, detail.coordinate.longitude);
                        if (addressFieldId && detail.address) {
                            const addressField = document.getElementById(addressFieldId);
                            if (addressField) addressField.value = detail.address;
                        }
                        return detail;
                    });
                },

                showLocationOnMap: function(lat, lng, title) {
                    const coordinate = window.TKLeafletMaps?.normalizeCoordinate({
                        latitude: lat,
                        longitude: lng
                    });
                    if (!coordinate) return;

                    this.currentDirectionsCoordinate = coordinate;
                    const marker = {
                        id: 'view-location',
                        coordinate,
                        title: title || 'Location Map'
                    };
                    const instance = this.instances['viewMap'];
                    if (instance?.controller?.setMarkers) {
                        instance.controller.setMarkers([marker], coordinate, this.detailZoom);
                    } else if (window.TKLeafletMaps) {
                        this.instances['viewMap'] = {
                            controller: window.TKLeafletMaps.mountStatic({
                                elementId: 'viewMap',
                                center: coordinate,
                                zoom: this.detailZoom,
                                height: '400px',
                                markers: [marker]
                            })
                        };
                    }

                    document.getElementById('mapViewTitle').textContent = title || 'Location Map';
                    document.getElementById('mapViewModal').style.display = 'flex';
                    setTimeout(() => this.instances['viewMap']?.controller?.invalidate?.(), 120);
                },

                initializeUserLocation: function() {
                    window.TKLeafletMaps?.locateUser()
                        .then((detail) => {
                            this.userLocation = detail.coordinate;
                        })
                        .catch(() => {});
                }
            };

            window.addEventListener('DOMContentLoaded', () => {
                LeafletMapsManager.initializeUserLocation();
            });

            window.LeafletMapsManager = LeafletMapsManager;

            window.closeMapViewModal = function() {
                document.getElementById('mapViewModal').style.display = 'none';
            };

            window.openDirections = function() {
                const coordinate = LeafletMapsManager.currentDirectionsCoordinate;
                if (!coordinate) return;

                const url = `https://www.openstreetmap.org/directions?to=${coordinate.latitude}%2C${coordinate.longitude}`;
                window.open(url, '_blank', 'noopener,noreferrer');
            };
        </script>

        {{-- ====================CONSOLIDATED MODAL MANAGER============================ --}}
        <script>
            const UniversalModalManager = {
                showToast: function(message, type = 'success', duration = 3000) {
                    const toast = document.getElementById('toast');
                    if (!toast) {
                        console.error('Toast element not found');
                        return;
                    }

                    switch (type) {
                        case 'error':
                            toast.style.background = '#ef4444';
                            break;
                        case 'warning':
                            toast.style.background = '#f59e0b';
                            break;
                        case 'info':
                            toast.style.background = '#3b82f6';
                            break;
                        case 'success':
                        default:
                            toast.style.background = '#16a34a';
                            break;
                    }

                    toast.textContent = message;
                    toast.style.opacity = '1';
                    setTimeout(() => toast.style.opacity = '0', duration);
                },

                showConfirm: function(title, message, confirmText = 'Confirm', cancelText = 'Cancel') {
                    return new Promise((resolve) => {
                        document.getElementById('confirmModalTitle').textContent = title;
                        document.getElementById('confirmModalMessage').textContent = message;

                        const confirmBtn = document.getElementById('confirmActionBtn');
                        const cancelBtn = document.getElementById('cancelConfirmBtn');

                        if (confirmText !== 'Confirm') confirmBtn.textContent = confirmText;
                        if (cancelText !== 'Cancel') cancelBtn.textContent = cancelText;

                        document.getElementById('confirmModal').style.display = 'flex';

                        const handleConfirm = () => {
                            cleanup();
                            resolve(true);
                        };

                        const handleCancel = () => {
                            cleanup();
                            resolve(false);
                        };

                        const cleanup = () => {
                            document.getElementById('confirmModal').style.display = 'none';
                            confirmBtn.removeEventListener('click', handleConfirm);
                            cancelBtn.removeEventListener('click', handleCancel);
                            confirmBtn.textContent = 'Confirm';
                            cancelBtn.textContent = 'Cancel';
                        };

                        confirmBtn.addEventListener('click', handleConfirm);
                        cancelBtn.addEventListener('click', handleCancel);
                    });
                }
            };

            window.ModalManager = UniversalModalManager;
        </script>

        {{-- ====================SIMPLIFIED LOCATION MODAL FUNCTIONS============================ --}}
        <script>
            let isEditMode = false;
            let currentDropoffId = null;
            let modalMapInitialized = false;

            function initModalMap() {
                if (modalMapInitialized) return;

                LeafletMapsManager.initMap('modal_map', {
                    addressFieldId: 'modal_address',
                    searchInputId: 'modalMapSearchBox',
                    title: 'Drop-off location',
                    height: '300px'
                });

                const addressInput = document.getElementById('modal_address');
                if (addressInput) {
                    addressInput.addEventListener('input', function() {
                        if (!this.value.trim()) {
                            LeafletMapsManager.clearCoordinates();
                        }
                    });
                }

                modalMapInitialized = true;
            }

            function openLocationModal(id = null, locationData = {}) {
                isEditMode = !!id;
                currentDropoffId = id;

                // Initialize modal map
                initModalMap();

                // Set modal content
                document.getElementById('modalTitle').textContent = isEditMode ? 'Edit Location' : 'Add New Location';
                document.getElementById('modal_dropoff_id').value = id || '';
                document.getElementById('modal_name').value = locationData.name || '';
                document.getElementById('modal_address').value = locationData.address || '';
                document.getElementById('modal_schedule_datetime').value = locationData.schedule || '';

                // Update map if coordinates exist
                if (locationData.lat && locationData.lng && locationData.lat !== 'null' && locationData.lng !== 'null') {
                    LeafletMapsManager.updateCoordinates('modal_map', locationData.lat, locationData.lng);
                } else {
                    LeafletMapsManager.clearCoordinates();
                }

                // Update UI
                document.getElementById('modalSubmitBtn').textContent = isEditMode ? 'Update Location' : 'Add Location';
                document.getElementById('modalDeleteBtn').style.display = isEditMode ? 'inline-block' : 'none';
                document.getElementById('modal-message-location').style.display = 'none';

                // Show modal
                document.getElementById('locationModal').style.display = 'flex';
                setTimeout(() => LeafletMapsManager.instances['modal_map']?.controller?.invalidate?.(), 120);
            }

            function closeLocationModal() {
                document.getElementById('locationModal').style.display = 'none';
            }

            async function locateUserInModal() {
                try {
                    await LeafletMapsManager.locateUser('modal_map', 'modal_address');
                    UniversalModalManager.showToast('Location updated successfully!', 'success');
                } catch (error) {
                    UniversalModalManager.showToast(error.message, 'error');
                }
            }

            // Form submission
            document.getElementById('modalDropoffForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const requiredFields = ['name', 'address', 'schedule_datetime'];

                // Validate required fields
                for (const field of requiredFields) {
                    if (!formData.get(field)?.trim()) {
                        UniversalModalManager.showToast(`Please fill in the ${field.replace('_', ' ')} field`,
                            'error');
                        return;
                    }
                }

                // Get address value separately to check if it's empty
                const address = document.getElementById('modal_address').value.trim();
                const lat = formData.get('latitude');
                const lng = formData.get('longitude');

                // If address is provided but coordinates are missing, show warning
                if (address && (!lat || !lng || isNaN(lat) || isNaN(lng))) {
                    const confirmed = await ModalManager.showConfirm(
                        'Missing Location Coordinates',
                        'You have entered an address but no location coordinates are set. The map pin will not be visible. Do you want to continue without map coordinates?'
                    );

                    if (!confirmed) {
                        UniversalModalManager.showToast('Location submission cancelled', 'info');
                        return;
                    }
                }

                // Submit form
                const submitBtn = document.getElementById('modalSubmitBtn');
                const originalText = submitBtn.innerHTML;

                try {
                    submitBtn.innerHTML = '<i class="ri-loader-4-line"></i> Saving...';
                    submitBtn.disabled = true;

                    const url = isEditMode ?
                        "{{ route('location.update', ['dropoff_id' => '__ID__']) }}".replace('__ID__',
                            currentDropoffId) :
                        "{{ route('location.add') }}";

                    if (isEditMode) formData.append('_method', 'PUT');

                    const response = await fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (!response.ok) throw new Error(data.message || 'Request failed');

                    ModalManager.showToast(
                        isEditMode ? 'Location updated successfully!' : 'Location added successfully!',
                        'success'
                    );

                    setTimeout(() => location.reload(), 1000);

                } catch (error) {
                    UniversalModalManager.showToast(error.message, 'error');
                } finally {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });

            async function confirmDeleteInModal() {
                const confirmed = await UniversalModalManager.showConfirm(
                    'Confirm Deletion',
                    'Are you sure you want to delete this location? This action cannot be undone.',
                    'Delete',
                    'Cancel'
                );

                if (confirmed) {
                    try {
                        const response = await fetch(
                            "{{ route('location.delete', ['dropoff_id' => '__ID__']) }}".replace('__ID__',
                                currentDropoffId), {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }
                        );

                        if (!response.ok) throw new Error('Delete failed');

                        UniversalModalManager.showToast('Location deleted successfully!', 'success');
                        setTimeout(() => location.reload(), 1000);

                    } catch (error) {
                        UniversalModalManager.showToast(error.message, 'error');
                    }
                }
            }
        </script>


        {{-- ====================TOGGLE ACTIVE FUNCTION============================ --}}
        <script>
            async function toggleActive(checkbox) {
                const form = checkbox.closest('form');
                const statusText = form.querySelector('.status-text');

                // Show loading state
                const originalText = statusText.textContent;

                checkbox.disabled = true;

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-HTTP-Method-Override': 'PUT',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            _method: 'PUT'
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error('Failed to update status');
                    }

                    // Update the status text
                    const isActive = data.updated.is_active;
                    statusText.textContent = isActive ? 'Active' : 'Inactive';
                    statusText.style.color = isActive ? '#16a34a' : '#ef4444';

                    // Update statistics
                    const activeStat = document.getElementById('stat-active');
                    const totalStat = document.getElementById('stat-total');

                    if (activeStat) {
                        activeStat.textContent = data.stats.active;
                    }
                    if (totalStat) {
                        totalStat.textContent = data.stats.total;
                    }

                } catch (error) {
                    // Revert checkbox on error
                    checkbox.checked = !checkbox.checked;
                    statusText.textContent = originalText;
                    UniversalModalManager.showToast('Failed to update status. Please try again.', 'error');
                    console.error('Toggle error:', error);
                } finally {
                    checkbox.disabled = false;
                }
            }
        </script>



        {{-- ====================Impact Report SCRIPT============================ --}}
        <script>
            // Impact Report Variables
            let selectedPhotos = [];
            let selectedDonations = new Set();
            let selectedDonationsData = {};
            let allDonations = []; // Store all donations data

            // Function to load donations dynamically
            async function loadImpactDonations() {
                const container = document.getElementById('donations-container');

                // Show loading state
                container.innerHTML = `
            <div style="text-align: center; padding: 40px;">
                <div class="spinner" style="display: inline-block; width: 30px; height: 30px; border: 3px solid #e5e7eb; border-top-color: #3b82f6; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <p style="margin-top: 15px; color: #6b7280;">Loading donations...</p>
            </div>
            <style>
                @keyframes spin { to { transform: rotate(360deg); } }
            </style>
        `;

                try {
                    const response = await fetch('{{ route('donations.getReceived') }}', {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        allDonations = data.donations; // Store for search/filter
                        renderDonations(data.html);
                        updateSelectionSummary(); // Reset summary
                        setupDonationEventListeners(); // Re-attach event listeners
                    } else {
                        container.innerHTML = `
                    <div style="text-align: center; padding: 50px; color: #6b7280;">
                        <i class="ri-information-line" style="font-size: 36px; margin-bottom: 15px;"></i>
                        <p>Failed to load donations. Please try again.</p>
                    </div>
                `;
                    }
                } catch (error) {
                    console.error('Error loading donations:', error);
                    container.innerHTML = `
                <div style="text-align: center; padding: 50px; color: #6b7280;">
                    <i class="ri-error-warning-line" style="font-size: 36px; margin-bottom: 15px;"></i>
                    <p>Error loading donations. Please refresh the page.</p>
                </div>
            `;
                }
            }

            // Function to render donations HTML
            function renderDonations(html) {
                const container = document.getElementById('donations-container');
                container.innerHTML = html;

                // If there's a "no donations" message, hide it initially
                const noDonations = container.querySelector('#no-donations');
                if (noDonations) {
                    noDonations.style.display = 'none';
                }
            }

            // Modal Functions
            async function openImpactModal() {
                // Clear previous data
                document.getElementById('impact-message').style.display = 'none';
                document.getElementById('impactReportForm').reset();
                document.getElementById('impact_photo_preview').style.display = 'none';

                // Reset summary counters
                resetSummaryCounters();

                // Set today's date
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('impact_date').value = today;

                // Reset file input
                document.getElementById('impact_photo').value = '';

                // Clear arrays and sets
                selectedPhotos = [];
                selectedDonations.clear();
                selectedDonationsData = {};

                // Reset search input
                document.getElementById('impactDonationSearch').value = '';

                // Show modal
                document.getElementById('impactReportModal').style.display = 'flex';

                // Load donations dynamically
                await loadImpactDonations();
            }

            function closeImpactModal() {
                document.getElementById('impactReportModal').style.display = 'none';
            }

            function resetSummaryCounters() {
                document.getElementById('selected-donors-count').textContent = '0';
                document.getElementById('selected-items-count').textContent = '0';
                document.getElementById('selected-categories-count').textContent = '0';
                document.getElementById('selected-count').textContent = '0 selected';
            }

            // Setup event listeners for donation items
            function setupDonationEventListeners() {
                // Add click event to donation items
                document.querySelectorAll('.donation-item').forEach(item => {
                    // Remove any existing listeners first
                    const newItem = item.cloneNode(true);
                    item.parentNode.replaceChild(newItem, item);

                    newItem.addEventListener('click', function(e) {
                        // Don't trigger if clicking on the checkbox
                        if (e.target.closest('.item-checkbox')) {
                            return;
                        }
                        const id = this.dataset.id;
                        const checkbox = document.getElementById(`donation-checkbox-${id}`);
                        const isChecked = !checkbox.checked;
                        checkbox.checked = isChecked;
                        toggleDonation(id, isChecked);
                    });
                });

                // Add click event to checkbox visual indicators
                document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const id = this.dataset.id;
                        const hiddenCheckbox = document.getElementById(`donation-checkbox-${id}`);
                        const isChecked = !hiddenCheckbox.checked;
                        hiddenCheckbox.checked = isChecked;
                        toggleDonation(id, isChecked);
                    });
                });

                // Add click event to group select buttons
                document.querySelectorAll('.select-group-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const donorKey = this.dataset.user;
                        toggleUserSelection(donorKey);
                    });
                });
            }

            // Search function for donations in modal
            function filterDonationsInModal(searchTerm) {
                const term = searchTerm.toLowerCase().trim();
                let foundAny = false;

                document.querySelectorAll('#donations-container .donation-group').forEach(group => {
                    const groupText = group.textContent.toLowerCase();
                    const groupVisible = term === '' || groupText.includes(term);

                    if (groupVisible) {
                        group.style.display = '';
                        foundAny = true;
                    } else {
                        group.style.display = 'none';
                    }
                });

                // Show/hide no results message
                const noResults = document.getElementById('search-no-results');
                const noDonations = document.getElementById('no-donations');

                if (noResults) {
                    noResults.style.display = foundAny || term === '' ? 'none' : 'block';
                }

                if (noDonations && term !== '') {
                    noDonations.style.display = 'none';
                }
            }

            // Donation Selection Functions
            function toggleDonation(id, isChecked) {
                const item = document.querySelector(`.donation-item[data-id="${id}"]`);
                if (!item) return;

                const checkbox = item.querySelector('.item-checkbox');
                const hiddenCheckbox = document.getElementById(`donation-checkbox-${id}`);

                if (isChecked) {
                    selectedDonations.add(id);
                    checkbox.innerHTML = '<i class="ri-check-line" style="color: white; font-size: 12px;"></i>';
                    checkbox.style.background = '#3b82f6';
                    checkbox.style.borderColor = '#3b82f6';

                    // Ensure hidden checkbox is checked
                    if (hiddenCheckbox) {
                        hiddenCheckbox.checked = true;
                    }

                    // Get quantity and category from the rendered item
                    const quantityText = item.querySelector('span[style*="color: #16a34a"]')?.textContent || '0';
                    const categoryText = item.querySelector('span[style*="background: #f3f4f6"]')?.textContent || 'Unknown';

                    selectedDonationsData[id] = {
                        quantity: parseInt(quantityText.match(/\d+/)?.[0] || 0),
                        category: categoryText
                    };
                } else {
                    selectedDonations.delete(id);
                    checkbox.innerHTML = '';
                    checkbox.style.background = '';
                    checkbox.style.borderColor = '#d1d5db';

                    // Ensure hidden checkbox is unchecked
                    if (hiddenCheckbox) {
                        hiddenCheckbox.checked = false;
                    }

                    delete selectedDonationsData[id];
                }

                updateSelectionSummary();
            }

            function updateSelectionSummary() {
                // Count unique donors
                const selectedGroups = new Set();
                selectedDonations.forEach(id => {
                    const item = document.querySelector(`.donation-item[data-id="${id}"]`);
                    const group = item?.closest('.donation-group');
                    if (group) selectedGroups.add(group.dataset.user);
                });

                // Count total items and categories
                let totalItems = 0;
                const categories = new Set();

                Object.values(selectedDonationsData).forEach(data => {
                    totalItems += data.quantity;
                    categories.add(data.category);
                });

                // Update display
                document.getElementById('selected-donors-count').textContent = selectedGroups.size;
                document.getElementById('selected-items-count').textContent = totalItems;
                document.getElementById('selected-categories-count').textContent = categories.size;
                document.getElementById('selected-count').textContent = `${selectedDonations.size} selected`;
            }

            function toggleUserSelection(donorKey) {
                const group = document.querySelector(`.donation-group[data-user="${donorKey}"]`);
                if (!group) return;

                const checkboxes = group.querySelectorAll('input[type="checkbox"]');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);

                checkboxes.forEach(cb => {
                    const id = cb.value;
                    const isNowChecked = !allChecked;
                    cb.checked = isNowChecked;

                    // Update the visual checkbox
                    const item = document.querySelector(`.donation-item[data-id="${id}"]`);
                    if (item) {
                        const visualCheckbox = item.querySelector('.item-checkbox');
                        if (isNowChecked) {
                            visualCheckbox.innerHTML =
                                '<i class="ri-check-line" style="color: white; font-size: 12px;"></i>';
                            visualCheckbox.style.background = '#3b82f6';
                            visualCheckbox.style.borderColor = '#3b82f6';
                        } else {
                            visualCheckbox.innerHTML = '';
                            visualCheckbox.style.background = '';
                            visualCheckbox.style.borderColor = '#d1d5db';
                        }
                    }

                    toggleDonation(id, isNowChecked);
                });
            }

            function selectAllDonations() {
                document.querySelectorAll('#donations-container input[type="checkbox"]').forEach(cb => {
                    const id = cb.value;
                    cb.checked = true;
                    toggleDonation(id, true);
                });
            }

            function clearAllDonations() {
                document.querySelectorAll('#donations-container input[type="checkbox"]').forEach(cb => {
                    const id = cb.value;
                    cb.checked = false;
                    toggleDonation(id, false);
                });
            }

            // Photo Functions
            function previewImpactPhoto(input) {
                if (!input.files) return;

                const files = Array.from(input.files);

                // Check if adding these files exceeds 5
                if (selectedPhotos.length + files.length > 5) {
                    ModalManager.showToast('Maximum 5 photos allowed', 'error');
                    return;
                }

                files.forEach(file => {
                    if (file.size > 2 * 1024 * 1024) {
                        ModalManager.showToast(`Photo ${file.name} exceeds 2MB limit`, 'error');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        selectedPhotos.push({
                            file: file,
                            url: e.target.result,
                            name: file.name
                        });
                        updatePhotoPreview();
                    };
                    reader.readAsDataURL(file);
                });

                input.value = '';
            }

            function updatePhotoPreview() {
                const previewContainer = document.getElementById('impact_photo_preview');

                if (selectedPhotos.length === 0) {
                    previewContainer.style.display = 'none';
                    const uploadArea = document.getElementById('photo-upload-area');
                    uploadArea.innerHTML = `
                <i class="ri-camera-line" style="font-size: 36px; color: #6b7280; margin-bottom: 12px;"></i>
                <p style="color: #374151; margin-bottom: 5px; font-weight: 500;">Click to upload photos</p>
                <p style="font-size: 13px; color: #9ca3af;">JPG, PNG, GIF (Max 2MB each, up to 5 photos)</p>
            `;
                    return;
                }

                previewContainer.style.display = 'block';
                previewContainer.innerHTML = '';

                selectedPhotos.forEach((photo, i) => {
                    const div = document.createElement('div');
                    div.style.marginBottom = '10px';
                    div.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px;">
                    <img src="${photo.url}" alt="Preview" style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px;">
                    <div style="flex: 1;">
                        <div style="font-size: 14px; color: #374151;">${photo.name}</div>
                        <div style="font-size: 12px; color: #6b7280;">${(photo.file.size / 1024).toFixed(2)} KB</div>
                    </div>
                    <button type="button" onclick="removePhoto(${i})" style="padding: 4px 8px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                        <i class="ri-close-line"></i> Remove
                    </button>
                </div>
            `;
                    previewContainer.appendChild(div);
                });

                const uploadArea = document.getElementById('photo-upload-area');
                uploadArea.innerHTML = `
            <i class="ri-camera-line" style="font-size: 24px; color: #6b7280; margin-bottom: 8px;"></i>
            <p style="color: #374151; margin-bottom: 5px; font-weight: 500;">${selectedPhotos.length} photo(s) selected</p>
            <p style="font-size: 12px; color: #9ca3af;">Click to add more photos (Max 5 total)</p>
        `;
            }

            function removePhoto(index) {
                selectedPhotos.splice(index, 1);
                updatePhotoPreview();
            }

            // Form Submission
            document.getElementById('impactReportForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const submitBtn = document.getElementById('impactSubmitBtn');
                const originalText = submitBtn.innerHTML;

                // Validation
                const title = document.getElementById('impact_title').value.trim();
                const date = document.getElementById('impact_date').value;
                const description = document.getElementById('impact_description').value.trim();

                if (!title || !date || !description) {
                    ModalManager.showToast('Please fill in all required fields', 'error');
                    return;
                }

                if (selectedDonations.size === 0) {
                    ModalManager.showToast('Please select at least one donation', 'error');
                    return;
                }

                // Show loading
                submitBtn.innerHTML = '<i class="ri-loader-4-line"></i> Creating...';
                submitBtn.disabled = true;

                try {
                    // Prepare the data object
                    const formData = new FormData();
                    formData.append('title', title);
                    formData.append('report_date', date);
                    formData.append('description', description);

                    // Append each donation ID
                    selectedDonations.forEach(id => {
                        formData.append('selected_donations[]', id);
                    });

                    // Add photos
                    selectedPhotos.forEach(photo => {
                        formData.append('photos[]', photo.file);
                    });

                    const response = await fetch('{{ route('impact-reports.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        ModalManager.showToast('Impact report created successfully!', 'success');


                        selectedDonations.forEach(id => {
                            const badge = document.getElementById(`status-badge-${id}`);
                            if (badge) {
                                badge.textContent = 'Distributed';
                                badge.classList.remove('scheduled', 'received');
                                badge.classList.add('distributed');

                                // Also update the dropdown
                                const select = document.querySelector(`[data-donation-status-control="${id}"]`);
                                if (select) {
                                    // Create disabled dropdown
                                    const newSelect = document.createElement('select');
                                    newSelect.disabled = true;
                                    newSelect.style.padding = '4px 8px';
                                    newSelect.style.border = '1px solid #d1d5db';
                                    newSelect.style.borderRadius = '6px';
                                    newSelect.style.fontSize = '14px';
                                    newSelect.style.background = '#f3f4f6';
                                    newSelect.style.color = '#6b7280';
                                    newSelect.style.cursor = 'not-allowed';

                                    const option = document.createElement('option');
                                    option.selected = true;
                                    option.textContent = 'No action required';
                                    newSelect.appendChild(option);

                                    // Replace the old select with disabled one
                                    select.parentNode.replaceChild(newSelect, select);
                                }
                            }
                        });

                        closeImpactModal();
                        // Refresh the page to show new report

                    } else {
                        throw new Error(data.message || 'Failed to create report');
                    }
                } catch (error) {
                    ModalManager.showToast(error.message, 'error');
                } finally {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });
        </script>


        {{-- ====================================SEARCH FUNCTION========================================= --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('donationSearch');

                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase().trim();

                        // Get all donation rows
                        const rows = document.querySelectorAll('#donations-body tr');
                        let visibleCount = 0;

                        rows.forEach(row => {
                            let rowText = '';

                            // Collect all text content from the row (excluding action buttons)
                            const cells = row.querySelectorAll('td');
                            cells.forEach((cell, index) => {
                                // Skip the last cell (actions column) if you don't want to search in actions
                                if (index < cells.length - 1) {
                                    rowText += ' ' + cell.textContent.toLowerCase();
                                }
                            });

                            // Check if row contains search term
                            const isVisible = rowText.includes(searchTerm);

                            // Apply display based on visibility AND current filter
                            if (isVisible) {
                                // Check if it matches current filter
                                const statusBadge = row.querySelector('[id^="status-badge-"]');
                                const rowStatus = statusBadge ? statusBadge.textContent.trim() : '';

                                const currentFilterBtn = document.querySelector('.filter-btn.active');
                                const currentFilter = currentFilterBtn ? currentFilterBtn.dataset
                                    .status : 'all';

                                if (currentFilter === 'all' || rowStatus === currentFilter) {
                                    row.style.display = '';
                                    visibleCount++;
                                } else {
                                    row.style.display = 'none';
                                }
                            } else {
                                row.style.display = 'none';
                            }
                        });



                    });

                    // Also trigger search when filter changes (optional)
                    document.querySelectorAll('.filter-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            // Trigger search again to apply both filter and search
                            if (searchInput.value) {
                                searchInput.dispatchEvent(new Event('input'));
                            }
                        });
                    });
                }
            });
        </script>


    </body>

    </html>
