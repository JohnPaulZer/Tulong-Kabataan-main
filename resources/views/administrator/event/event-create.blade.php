<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create New Event | Tulong Kabataan Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCxE6u2I1N_uFuYp8hZH2OSh_VEPo1N85M&libraries=places">
    </script>

    <style>
        /* --- 1. Variables & Reset --- */
        :root {
            --primary-color: #4f46e5;
            /* Modern Indigo */
            --primary-hover: #4338ca;
            --secondary-color: #64748b;
            --accent-bg: #eff6ff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.1);
            --radius-md: 0.75rem;
            --radius-lg: 1rem;
        }

        body {
            background: #f8fafc;
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            margin: 0;
            min-height: 100vh;
            line-height: 1.6;
        }

        /* --- 2. Header Banner --- */
        .header-banner {
            /* Fallback color + Gradient + Image */
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.95), rgba(124, 58, 237, 0.9)),
                url('../../img/inkind.png') center/cover no-repeat;
            color: white;
            text-align: center;
            padding: 8rem 1rem 10rem;
            /* Increased bottom padding for overlap */
            position: relative;
            clip-path: polygon(0 0, 100% 0, 100% 90%, 0 100%);
            /* Subtle angle at bottom */
        }

        .header-banner h1 {
            font-size: 2.75rem;
            font-weight: 700;
            letter-spacing: -0.025em;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-banner p {
            font-size: 1.125rem;
            opacity: 0.9;
            font-weight: 300;
            max-width: 600px;
            margin: 0 auto;
        }

        /* --- 3. Form Card --- */
        .form-card {
            background: rgba(255, 255, 255, 0.95);
            /* Glassmorphism effect only if supported */
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);

            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 1.5rem;
            box-shadow: var(--card-shadow);

            margin-top: -8rem;
            /* Pull up over banner */
            padding: 3.5rem;
            max-width: 950px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            z-index: 10;
        }

        .form-header {
            text-align: center;
            margin-bottom: 3rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 2rem;
        }

        .form-header h2 {
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.025em;
        }

        /* --- 4. Section Styling --- */
        .section-wrapper {
            margin-bottom: 3rem;
            position: relative;
        }

        .form-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-section-title i {
            background: var(--accent-bg);
            padding: 0.5rem;
            border-radius: 8px;
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        /* --- 5. Inputs & Controls --- */
        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            color: #475569;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            border-radius: 0.6rem;
            border: 1px solid var(--border-color);
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background-color: #fff;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            background-color: #fff;
        }

        .form-text {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        /* Input File Customization */
        input[type="file"]::file-selector-button {
            background-color: var(--accent-bg);
            color: var(--primary-color);
            border: none;
            padding: 0.5rem 1rem;
            margin-right: 1rem;
            border-radius: 0.4rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        input[type="file"]::file-selector-button:hover {
            background-color: #e0e7ff;
        }

        /* --- 6. Maps & Interactive Elements --- */
        #map {
            border-radius: var(--radius-lg);
            border: 2px solid #fff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            height: 400px;
            overflow: hidden;
        }

        .location-search-wrapper {
            position: relative;
            margin-bottom: 1rem;
        }

        .selected-location-box {
            background: #f1f5f9;
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }

        /* --- 7. Role Cards --- */
        .role-item {
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            margin-bottom: 1rem;
            position: relative;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .role-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }

        #add-role-btn {
            border-style: dashed;
            width: 100%;
            padding: 0.8rem;
        }

        /* --- 8. Buttons --- */
        .btn-primary {
            background: var(--primary-color);
            border: none;
            font-weight: 600;
            padding: 0.8rem 2.5rem;
            border-radius: 0.6rem;
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.25);
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(79, 70, 229, 0.3);
        }

        .btn-back {
            background: white;
            border: 1px solid var(--border-color);
            color: var(--secondary-color);
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            /* Pill shape */
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: #f8fafc;
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* --- 9. Utilities --- */
        .alert {
            border-radius: var(--radius-md);
            border: none;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        /* Mobile Responsive Adjustments */
        @media (max-width: 768px) {
            .header-banner {
                padding: 6rem 1rem 8rem;
                text-align: left;
            }

            .header-banner h1 {
                font-size: 2rem;
            }

            .form-card {
                margin-top: -4rem;
                padding: 1.5rem;
                border-radius: 1rem;
            }

            .form-section-title {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>

    @include('administrator.partials.loading-screen')

    <!-- Header Banner -->
    <div class="header-banner">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h1>Tulong Kabataan Admin</h1>
                    <p>Manage and create impactful community events. Organize your volunteers and logistics efficiently
                        from this dashboard.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container-fluid px-3 px-md-5 pb-5">

        <!-- Flash Messages (Preserved) -->
        <div class="max-width-950 mx-auto" style="max-width: 950px;">
            @if ($errors->any())
                <div class="alert alert-danger mb-4 shadow-sm">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-exclamation-octagon-fill me-2 fs-5"></i>
                        <strong>Submission Error</strong>
                    </div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('server_error'))
                <div class="alert alert-danger mb-4 shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('server_error') }}
                </div>
            @endif
        </div>

        <!-- Floating Form Card -->
        <div class="form-card">

            <!-- Back Button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ url()->previous() }}" class="btn btn-back">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
                <span class="badge bg-light text-secondary border">Event Creation</span>
            </div>

            <div class="form-header">
                <h2>Create New Event</h2>
                <p class="text-muted">Fill in the details below to launch a new initiative.</p>
            </div>

            <form action="{{ route('submitevent') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- 1. Basic Information -->
                <div class="section-wrapper">
                    <div class="form-section-title">
                        <i class="bi bi-info-circle-fill text-primary"></i> Basic Information
                    </div>

                    <div class="row g-4">
                        <div class="col-12">
                            <label for="title" class="form-label">Event Title <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="title" name="title"
                                placeholder="e.g., Typhoon Relief Operation – Barangay 1" required>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Event Description <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="4"
                                placeholder="Describe the purpose, activities, and goals..." required></textarea>
                        </div>

                        <div class="col-12">
                            <label for="photo" class="form-label">Cover Photo</label>
                            <input type="file" class="form-control" id="photo" name="photo"
                                accept="image/png, image/jpeg">
                            <div class="form-text"><i class="bi bi-image"></i> Accepted: JPG, PNG | Max size: 5MB</div>
                        </div>
                    </div>
                </div>

                <!-- 2. Event Details -->
                <div class="section-wrapper">
                    <div class="form-section-title">
                        <i class="bi bi-calendar-event-fill text-primary"></i> Schedule & Location
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date & Time <span
                                    class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="start_date" name="start_date"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date & Time <span
                                    class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="deadline" class="form-label">Registration Deadline <span
                                    class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="deadline" name="deadline" required>
                            <div class="form-text">Volunteers cannot join after this date.</div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label mb-2">Event Location <span class="text-danger">*</span></label>

                        <!-- Search Box -->
                        <div class="location-search-wrapper input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="location-search"
                                placeholder="Search for a venue, barangay, or landmark...">
                            <button class="btn btn-primary" type="button" id="search-btn">Find</button>
                        </div>

                        <!-- Map Container -->
                        <div id="map">
                            <div class="text-center py-5 d-flex flex-column justify-content-center align-items-center h-100"
                                id="map-loading" style="background:#f8fafc;">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="mt-3 text-muted small fw-bold">Locating you...</p>
                            </div>
                        </div>

                        <!-- Selected Location Display -->
                        <div class="selected-location-box mt-3">
                            <i class="fas fa-map-marker-alt text-danger me-2 fs-5"></i>
                            <div>
                                <strong class="d-block text-dark">Selected Location:</strong>
                                <span id="selected-location" class="text-muted">No location selected (Click on map or
                                    search)</span>
                            </div>
                        </div>

                        <!-- Hidden fields -->
                        <input type="hidden" id="location" name="location" required>
                        <input type="hidden" id="lat" name="lat" required>
                        <input type="hidden" id="lng" name="lng" required>
                    </div>
                </div>

                <!-- 3. Volunteer Management -->
                <div class="section-wrapper">
                    <div class="form-section-title">
                        <i class="bi bi-people-fill text-primary"></i> Volunteer Roles
                    </div>

                    <div id="roles-container">
                        <div class="role-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold m-0 text-primary">Role #1</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label text-muted small">Role Name</label>
                                    <input type="text" class="form-control" name="roles[0][name]"
                                        placeholder="e.g. Medical Team" required>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label text-muted small">Description</label>
                                    <textarea class="form-control" name="roles[0][description]" rows="1" placeholder="Responsibilities..."
                                        required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-role-btn">
                        <i class="bi bi-plus-circle me-1"></i> Add Another Role
                    </button>
                </div>

                <!-- 4. Coordinator Contact Information -->
                <div class="section-wrapper mb-5">
                    <div class="form-section-title">
                        <i class="bi bi-person-badge-fill text-primary"></i> Coordinator Details
                    </div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label for="coordinator_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="coordinator_name" name="coordinator_name"
                                placeholder="Juan Dela Cruz" required>
                        </div>
                        <div class="col-md-4">
                            <label for="coordinator_email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="coordinator_email"
                                name="coordinator_email" placeholder="juan@example.com" required>
                        </div>
                        <div class="col-md-4">
                            <label for="coordinator_phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="coordinator_phone"
                                name="coordinator_phone" placeholder="0912 345 6789" required>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="text-center pt-3 border-top">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-check-circle-fill me-2"></i> Create Event
                    </button>
                    <div class="mt-3">
                        <a href="{{ url()->previous() }}" class="text-decoration-none text-muted small">Cancel and
                            return</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let map;
        let marker;
        let geocoder;
        let autocomplete;

        function initMap() {
            // Try to get user's current location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        initializeMapWithLocation(userLocation);
                    },
                    (error) => {
                        console.log("Geolocation failed:", error.message);
                        const defaultLocation = {
                            lat: 12.8797,
                            lng: 121.7740
                        }; // Philippines Center
                        initializeMapWithLocation(defaultLocation);
                    }, {
                        timeout: 5000,
                        maximumAge: 600000,
                        enableHighAccuracy: true
                    }
                );
            } else {
                const defaultLocation = {
                    lat: 12.8797,
                    lng: 121.7740
                };
                initializeMapWithLocation(defaultLocation);
            }
        }

        function initializeMapWithLocation(location) {
            const loadingElement = document.getElementById("map-loading");
            if (loadingElement) {
                loadingElement.style.display = "none";
            }

            map = new google.maps.Map(document.getElementById("map"), {
                center: location,
                zoom: 14,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                styles: [ /* Optional: Add a custom map style here for aesthetics if desired */ ]
            });

            geocoder = new google.maps.Geocoder();

            placeMarker(location);
            reverseGeocode(location);

            map.addListener("click", (event) => {
                placeMarker(event.latLng);
                reverseGeocode(event.latLng);
            });

            const searchInput = document.getElementById("location-search");
            autocomplete = new google.maps.places.Autocomplete(searchInput, {
                componentRestrictions: {
                    country: "ph"
                },
                fields: ["formatted_address", "geometry"]
            });

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (place.geometry) {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                    placeMarker(place.geometry.location);
                    updateLocationFields(place.formatted_address, place.geometry.location);
                }
            });

            document.getElementById("search-btn").addEventListener("click", () => {
                const address = searchInput.value.trim();
                if (address) {
                    geocodeAddress(address);
                }
            });
        }

        function placeMarker(location) {
            if (marker) {
                marker.setMap(null);
            }
            marker = new google.maps.Marker({
                position: location,
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP
            });

            marker.addListener("dragend", () => {
                reverseGeocode(marker.getPosition());
            });
        }

        function geocodeAddress(address) {
            geocoder.geocode({
                address: address
            }, (results, status) => {
                if (status === "OK" && results[0]) {
                    map.setCenter(results[0].geometry.location);
                    map.setZoom(17);
                    placeMarker(results[0].geometry.location);
                    updateLocationFields(results[0].formatted_address, results[0].geometry.location);
                }
            });
        }

        function reverseGeocode(latLng) {
            geocoder.geocode({
                location: latLng
            }, (results, status) => {
                if (status === "OK" && results[0]) {
                    updateLocationFields(results[0].formatted_address, latLng);
                }
            });
        }

        function updateLocationFields(address, latLng) {
            document.getElementById("selected-location").textContent = address;
            document.getElementById("location").value = address;
            document.getElementById("lat").value = latLng.lat();
            document.getElementById("lng").value = latLng.lng();
        }

        // Add Role Script (Simple JS to add DOM elements)
        document.getElementById('add-role-btn').addEventListener('click', function() {
            const container = document.getElementById('roles-container');
            const count = container.children.length;

            const newRole = document.createElement('div');
            newRole.className = 'role-item';
            newRole.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold m-0 text-primary">Role #${count + 1}</h6>
                    <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none p-0" onclick="this.closest('.role-item').remove()">Remove</button>
                </div>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label text-muted small">Role Name</label>
                        <input type="text" class="form-control" name="roles[${count}][name]" placeholder="e.g. Logistics" required>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label text-muted small">Description</label>
                        <textarea class="form-control" name="roles[${count}][description]" rows="1" placeholder="Responsibilities..." required></textarea>
                    </div>
                </div>
            `;
            container.appendChild(newRole);
        });

        initMap();
    </script>
</body>

</html>
