<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <link rel="stylesheet" href="{{ asset('css/administrator/accountpage.css') }}">
</head>

<body>
    @include('administrator.partials.loading-screen')
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

    <main class="main">

        <div class="container">
            <section class="page-header">
                <h2>Account Verification</h2>
                <p>Review and manage user account decision requests</p>
            </section>

            <section class="stats-grid" aria-hidden="false">

                <!-- 🟦 Total Accounts -->
                <div class="stat-card" style="border-left-color:#3b82f6">
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <div class="icon-wrap" style="background:#eff6ff;border-radius:10px">
                            <i class="ri-group-line" style="color:#2563eb;font-size:20px"></i>
                        </div>
                        <span style="color:#2563eb;font-size:13px;font-weight:600">All</span>
                    </div>
                    <h3 style="margin-top:12px;color:#6b7280;font-size:13px">Total Registered Accounts</h3>
                    <p class="count">0</p>
                </div>

                <!-- 🟨 Pending Verification -->
                <div class="stat-card" style="border-left-color:#f59e0b">
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <div class="icon-wrap" style="background:#fffbeb;border-radius:10px">
                            <i class="ri-time-line" style="color:#f59e0b;font-size:20px"></i>
                        </div>
                        <span style="color:#f59e0b;font-size:13px;font-weight:600">Pending</span>
                    </div>
                    <h3 style="margin-top:12px;color:#6b7280;font-size:13px">Awaiting Review</h3>
                    <p class="count">0</p>
                </div>

                <!-- 🟩 Approved Accounts -->
                <div class="stat-card" style="border-left-color:#16a34a">
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <div class="icon-wrap" style="background:#dcfce7;border-radius:10px">
                            <i class="ri-checkbox-circle-line" style="color:#16a34a;font-size:20px"></i>
                        </div>
                        <span style="color:#16a34a;font-size:13px;font-weight:600">Approved</span>
                    </div>
                    <h3 style="margin-top:12px;color:#6b7280;font-size:13px">Verified Accounts</h3>
                    <p class="count">0</p>
                </div>

                <!-- 🟥 Rejected Accounts -->
                <div class="stat-card" style="border-left-color:#ef4444">
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <div class="icon-wrap" style="background:#fee2e2;border-radius:10px">
                            <i class="ri-close-circle-line" style="color:#ef4444;font-size:20px"></i>
                        </div>
                        <span style="color:#ef4444;font-size:13px;font-weight:600">Rejected</span>
                    </div>
                    <h3 style="margin-top:12px;color:#6b7280;font-size:13px">Declined Applications</h3>
                    <p class="count">0</p>
                </div>

            </section>


            <section class="card" aria-labelledby="requests-heading">
                <div class="card-header">
                    <div>
                        <h3 id="requests-heading" style="margin:0;font-size:16px;font-weight:600;color:#111827">
                            Account Verification Requests
                        </h3>
                    </div>
                    <div class="filter-buttons" role="tablist">
                        <button class="filter-btn {{ $status === 'pending' ? 'active' : '' }}" data-status="pending"
                            data-active="true">
                            Pending
                        </button>
                        <button class="filter-btn {{ $status === 'approved' ? 'active' : '' }}" data-status="approved">
                            Approved
                        </button>
                        <button class="filter-btn {{ $status === 'rejected' ? 'active' : '' }}" data-status="rejected">
                            Rejected
                        </button>
                        <button class="filter-btn {{ $status === 'reupload' ? 'active' : '' }}" data-status="reupload">
                            Reupload
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="search-row">
                        <div class="search-box">
                            <i class="ri-search-line"></i>
                            <input type="text" id="searchInput" placeholder="Search by name, email or ref..." />
                        </div>
                    </div>

                    <div class="account-list" id="accountList">
                        @forelse($requests as $req)
                            <div class="account-item" data-status="{{ $req->status }}"
                                style="border-left:5px solid
                    {{ $req->status === 'pending' ? '#f59e0b' : ($req->status === 'approved' ? '#16a34a' : ($req->status === 'rejected' ? '#ef4444' : '#3b82f6')) }}">

                                {{-- Compact Summary Row --}}
                                <div class="summary-row"
                                    style="display:flex;justify-content:space-between;align-items:center;cursor:pointer">
                                    <div style="display:flex;align-items:center;gap:10px">
                                        @php
                                            $avatar = optional($req->user)->profile_photo_url ?? null;
                                            if ($avatar) {
                                                // Handle Google profile photos
                                                if (
                                                    str_contains($avatar, 'googleusercontent.com') &&
                                                    !str_contains($avatar, 'sz=')
                                                ) {
                                                    $avatar .= (str_contains($avatar, '?') ? '&' : '?') . 'sz=64';
                                                }
                                                // Handle local storage photos
                                                if (!Str::startsWith($avatar, 'http')) {
                                                    $avatar = asset('storage/' . $avatar);
                                                }
                                            }
                                        @endphp

                                        @if ($avatar)
                                            <div class="avatar-large" style="background:#2563eb; overflow: hidden;">
                                                <img src="{{ $avatar }}"
                                                    alt="{{ $req->first_name }} {{ $req->last_name }}"
                                                    style="width: 100%; height: 100%; object-fit: cover;"
                                                    referrerpolicy="no-referrer">
                                            </div>
                                        @else
                                            <div class="avatar-large" style="background:#2563eb">
                                                {{ strtoupper(substr($req->first_name, 0, 1)) }}{{ strtoupper(substr($req->last_name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ strtoupper($req->first_name) }}
                                                {{ strtoupper($req->last_name) }}</strong>
                                            <span class="tag {{ $req->status }}">{{ ucfirst($req->status) }}</span>
                                            <div style="font-size:13px;color:#6b7280">
                                                Ref: #VER-{{ $req->request_id }} • Applied:
                                                {{ $req->created_at->format('M d, Y') }}
                                            </div>
                                        </div>
                                    </div>
                                    <i class="ri-arrow-down-s-line"></i>
                                </div>

                                {{-- Expandable Details --}}
                                <div class="details hidden" style="margin-top:12px">
                                    {{-- Typed Information --}}
                                    <div class="typed-info" style="margin:15px 0">
                                        <h5 style="margin:0 0 8px 0;font-size:14px">Typed Information</h5>
                                        <ul style="list-style:none;padding:0;margin:0;font-size:14px;color:#111827">
                                            <li><strong>Name:</strong> {{ strtoupper($req->first_name) }}
                                                {{ strtoupper($req->middle_name) }} {{ strtoupper($req->last_name) }}
                                            </li>
                                            <li><strong>Date of Birth:</strong>
                                                {{ $req->dob ? $req->dob->format('F d, Y') : 'N/A' }}</li>
                                            <li><strong>Sex:</strong> {{ strtoupper($req->sex) }}</li>
                                            <li><strong>ID Type:</strong> {{ ucfirst($req->id_type) }}</li>
                                            <li><strong>ID Number:</strong> {{ $req->id_number }}</li>
                                            <li><strong>ID Expiry:</strong>
                                                {{ $req->id_expiry ? $req->id_expiry->format('M d, Y') : 'N/A' }}</li>
                                        </ul>
                                    </div>

                                    {{-- Documents Section (kept from your screenshot) --}}
                                    <div class="documents" style="margin:15px 0">
                                        <h5 style="margin:0 0 8px 0;font-size:14px">Submitted Documents</h5>

                                        @if ($req->id_front_path)
                                            <div class="doc"
                                                style="display:flex;align-items:center;gap:12px;padding:10px;border-radius:8px;background:#f9fafb;margin-bottom:8px">
                                                <div class="icon-box"
                                                    style="background:#eff6ff;border-radius:8px;width:36px;height:36px;display:flex;align-items:center;justify-content:center">
                                                    <i class="ri-file-text-line" style="color:#2563eb"></i>
                                                </div>
                                                <div style="flex:1">
                                                    <div style="font-weight:600">ID Front</div>
                                                    <div style="font-size:12px;color:#6b7280">
                                                        {{ basename($req->id_front_path) }}</div>
                                                </div>
                                                <button type="button" class="btn-outline view-btn"
                                                    data-img="{{ asset('storage/' . $req->id_front_path) }}
                                                    "data-type="ID Front">
                                                    View
                                                </button>
                                            </div>
                                        @endif

                                        @if ($req->id_back_path)
                                            <div class="doc"
                                                style="display:flex;align-items:center;gap:12px;padding:10px;border-radius:8px;background:#f9fafb;margin-bottom:8px">
                                                <div class="icon-box"
                                                    style="background:#ecfdf5;border-radius:8px;width:36px;height:36px;display:flex;align-items:center;justify-content:center">
                                                    <i class="ri-file-text-line" style="color:#059669"></i>
                                                </div>
                                                <div style="flex:1">
                                                    <div style="font-weight:600">ID Back</div>
                                                    <div style="font-size:12px;color:#6b7280">
                                                        {{ basename($req->id_back_path) }}</div>
                                                </div>
                                                <button type="button" class="btn-outline view-btn"
                                                    data-img="{{ asset('storage/' . $req->id_back_path) }}"
                                                    data-type="ID Back">
                                                    View
                                                </button>
                                            </div>
                                        @endif

                                        @if ($req->face_photo_path)
                                            <div class="doc"
                                                style="display:flex;align-items:center;gap:12px;padding:10px;border-radius:8px;background:#f9fafb;margin-bottom:8px">
                                                <div class="icon-box"
                                                    style="background:#fef9c3;border-radius:8px;width:36px;height:36px;display:flex;align-items:center;justify-content:center">
                                                    <i class="ri-user-line" style="color:#ca8a04"></i>
                                                </div>
                                                <div style="flex:1">
                                                    <div style="font-weight:600">Facial Photo</div>
                                                    <div style="font-size:12px;color:#6b7280">
                                                        {{ basename($req->face_photo_path) }}</div>
                                                </div>
                                                <button type="button" class="btn-outline view-btn"
                                                    data-img="{{ asset('storage/' . $req->face_photo_path) }}"
                                                    data-type="Facial Photo">
                                                    View
                                                </button>
                                            </div>
                                        @endif

                                        @if ($req->selfie_path)
                                            <div class="doc"
                                                style="display:flex;align-items:center;gap:12px;padding:10px;border-radius:8px;background:#f9fafb">
                                                <div class="icon-box"
                                                    style="background:#e0f2fe;border-radius:8px;width:36px;height:36px;display:flex;align-items:center;justify-content:center">
                                                    <i class="ri-camera-line" style="color:#0284c7"></i>
                                                </div>
                                                <div style="flex:1">
                                                    <div style="font-weight:600">Selfie with ID</div>
                                                    <div style="font-size:12px;color:#6b7280">
                                                        {{ basename($req->selfie_path) }}</div>
                                                </div>
                                                <button type="button" class="btn-outline view-btn"
                                                    data-img="{{ asset('storage/' . $req->selfie_path) }}"
                                                    data-type="Selfie with ID">
                                                    View
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Notes --}}
                                    @if (in_array($req->status, ['pending', 'reupload']))
                                        @if (!empty($req->review_notes))
                                            <div class="notes" style="margin:15px 0">
                                                <h5 style="margin:0 0 8px 0;font-size:14px">Verification Notes</h5>
                                                <div
                                                    style="padding:10px;border:1px solid #e5e7eb;border-radius:6px;background:#f9fafb;color:#111827;font-size:14px">
                                                    {{ $req->review_notes }}
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    {{-- Actions --}}
                                    @if ($req->status === 'pending')
                                        <div class="account-actions"
                                            style="position:sticky;bottom:0;background:#fff;padding:10px;border-top:1px solid #e5e7eb;z-index:5">
                                            <form action="{{ route('decision') }}" method="POST"
                                                style="width:100%">
                                                @csrf
                                                <input type="hidden" name="request_id"
                                                    value="{{ $req->request_id }}">

                                                {{-- Notes --}}
                                                <textarea name="notes" rows="2" class="notes-input" placeholder="General Review Notes"
                                                    style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;margin-bottom:10px"></textarea>

                                                {{-- Reupload Reasons  --}}
                                                <div id="reuploadOptions" style="display:none; margin-bottom:10px">
                                                    <label
                                                        style="font-weight:600;font-size:14px;display:block;margin-bottom:6px">
                                                        Request Reupload For:
                                                    </label>
                                                    <div id="reuploadCheckboxes"
                                                        style="display:flex;flex-wrap:wrap;gap:12px;font-size:14px;color:#374151">
                                                        @if ($req->id_front_path)
                                                            <label><input type="checkbox" name="reupload_fields[]"
                                                                    value="id_front"> ID Front</label>
                                                        @endif
                                                        @if ($req->id_back_path)
                                                            <label><input type="checkbox" name="reupload_fields[]"
                                                                    value="id_back"> ID Back</label>
                                                        @endif
                                                        @if ($req->face_photo_path)
                                                            <label><input type="checkbox" name="reupload_fields[]"
                                                                    value="face_photo"> Facial Photo</label>
                                                        @endif
                                                        @if ($req->selfie_path)
                                                            <label><input type="checkbox" name="reupload_fields[]"
                                                                    value="selfie"> Selfie with ID</label>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Action Buttons --}}
                                                <div id="actionButtons" style="display:flex;gap:10px;flex-wrap:wrap">
                                                    <button type="submit" name="action" value="approved"
                                                        class="btn-approve" id="btnApprove">
                                                        </i> Approve
                                                    </button>
                                                    <button type="submit" name="action" value="rejected"
                                                        class="btn-reject" id="btnReject">
                                                        </i> Reject
                                                    </button>
                                                    <button type="button" id="btnShowReupload"
                                                        class="btn-info btn-show-reupload">
                                                        Request Reupload
                                                    </button>
                                                    <button type="submit" name="action" value="request_reupload"
                                                        id="btnSubmitReupload" class="btn-info" style="display:none;"
                                                        disabled>
                                                        </i> Confirm Reupload
                                                    </button>
                                                    <button type="button" id="btnCancelReupload" class="btn-reject"
                                                        style="display:none;">
                                                        </i> Cancel
                                                    </button>
                                                    <button type="button" class="btn-details btn-view-details">
                                                        </i> View Details
                                                    </button>
                                                </div>

                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p>No applications found.</p>
                        @endforelse
                    </div>

                </div>
            </section>

            <!-- Details View Modal -->
            <div id="detailsModal" class="proof-modal" style="display:none;">
                <div class="proof-modal-content">
                    <button id="closeDetailsModal" class="proof-modal-close">&times;</button>

                    <div class="proof-modal-header">
                        <h2 class="proof-modal-title">Account Verification Details</h2>
                    </div>

                    <div class="proof-modal-body">
                        <!-- Details Section (Left) -->
                        <div class="proof-details">
                            <div class="proof-detail-item">
                                <span class="proof-detail-label">Name:</span>
                                <span class="proof-detail-value" id="detailName"></span>
                            </div>
                            <div class="proof-detail-item">
                                <span class="proof-detail-label">Date of Birth:</span>
                                <span class="proof-detail-value" id="detailDob"></span>
                            </div>
                            <div class="proof-detail-item">
                                <span class="proof-detail-label">Sex:</span>
                                <span class="proof-detail-value" id="detailSex"></span>
                            </div>
                            <div class="proof-detail-item">
                                <span class="proof-detail-label">ID Type:</span>
                                <span class="proof-detail-value" id="detailIdType"></span>
                            </div>
                            <div class="proof-detail-item">
                                <span class="proof-detail-label">ID Number:</span>
                                <span class="proof-detail-value" id="detailIdNumber"></span>
                            </div>
                            <div class="proof-detail-item">
                                <span class="proof-detail-label">ID Expiry:</span>
                                <span class="proof-detail-value" id="detailIdExpiry"></span>
                            </div>
                            <div class="proof-detail-item">
                                <span class="proof-detail-label">Status:</span>
                                <span class="proof-detail-value" id="detailStatus"></span>
                            </div>
                            <div class="proof-detail-item">
                                <span class="proof-detail-label">Reference:</span>
                                <span class="proof-detail-value" id="detailReference"></span>
                            </div>
                            <div class="proof-detail-item">
                                <span class="proof-detail-label">Applied:</span>
                                <span class="proof-detail-value" id="detailApplied"></span>
                            </div>
                            <!-- Notes item will be shown/hidden dynamically -->
                            <div class="proof-detail-item" id="notesItem" style="display:none;">
                                <span class="proof-detail-label">Notes:</span>
                                <span class="proof-detail-value" id="detailNotes"></span>
                            </div>
                        </div>

                        <!-- Images Section (Right) -->
                        <div class="proof-image-container">
                            <!-- Carousel Container -->
                            <div class="carousel-container">
                                <!-- Main Carousel -->
                                <div class="carousel-main">
                                    <div class="carousel-slide" id="carouselSlides">
                                        <!-- Slides will be dynamically inserted here -->
                                    </div>

                                    <!-- Carousel Controls -->
                                    <button class="carousel-btn carousel-prev" id="carouselPrev">
                                        <i class="ri-arrow-left-s-line"></i>
                                    </button>
                                    <button class="carousel-btn carousel-next" id="carouselNext">
                                        <i class="ri-arrow-right-s-line"></i>
                                    </button>

                                    <!-- Carousel Indicators -->
                                    <div class="carousel-indicators" id="carouselIndicators">
                                        <!-- Indicators will be dynamically inserted here -->
                                    </div>
                                </div>

                                <!-- Thumbnail Gallery (shown if multiple images) -->
                                <div class="thumbnail-gallery" id="thumbnailGallery" style="display: none;">
                                    <h4 style="margin: 15px 0 10px 0; font-size: 14px; color: #374151;">Documents</h4>
                                    <div class="thumbnails-container" id="thumbnailsContainer">
                                        <!-- Thumbnails will be dynamically inserted here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="imageOverlay" class="image-overlay" style="display: none;">
                <button class="overlay-close">&times;</button>
                <div class="overlay-image-container">
                    <img id="overlayImage" src="" alt="">
                    <div class="overlay-caption" id="overlayCaption"></div>
                </div>
            </div>

            <!-- Keep the original image modal for viewing individual images -->
            <div id="imageModal" class="modal-backdrop"
                style="display:none;align-items:center;justify-content:center;">
                <div class="modal" style="max-width:800px;width:95%;padding:0">
                    <div
                        style="display:flex;justify-content:space-between;align-items:center;padding:10px 16px;border-bottom:1px solid #eee">
                        <h3 style="margin:0;font-size:16px">Document Preview</h3>
                        <button id="closeImageModal"
                            style="background:none;border:none;font-size:20px;cursor:pointer">&times;</button>
                    </div>
                    <div style="padding:10px;text-align:center;max-height:80vh;overflow:auto">
                        <img id="modalImage" src="" style="max-width:100%;border-radius:8px" />
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Sidebar Script --}}
    <script>
        /*
                                                                                                                                                                                                                                                                                                                                                                                  Sidebar toggle behaviour:
                                                                                                                                                                                                                                                                                                                                                                                */
        (function() {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');
            const main = document.getElementById('mainContent');

            function isLarge() {
                return window.innerWidth >= 1024;
            }

            function showSidebar() {
                if (isLarge()) {
                    sidebar.classList.remove('collapsed');
                    sidebar.classList.remove('visible');
                    overlay.classList.remove('visible');
                    main.classList.remove('fullwidth');
                } else {
                    sidebar.classList.add('visible');
                    overlay.classList.add('visible');
                    main.classList.add('fullwidth');
                }
            }

            function hideSidebar() {
                if (isLarge()) {
                    sidebar.classList.remove('collapsed');
                    overlay.classList.remove('visible');
                    main.classList.remove('fullwidth');
                } else {
                    sidebar.classList.remove('visible');
                    overlay.classList.remove('visible');
                    main.classList.remove('fullwidth');
                }
            }

            // Initialize state
            if (!isLarge()) {
                sidebar.classList.add('collapsed');
            } else {
                sidebar.classList.remove('collapsed');
            }

            toggle.addEventListener('click', function() {
                if (sidebar.classList.contains('visible') || sidebar.classList.contains('collapsed')) {
                    // toggle to show
                    if (sidebar.classList.contains('visible')) {
                        hideSidebar();
                        sidebar.classList.add('collapsed');
                    } else {
                        showSidebar();
                        sidebar.classList.remove('collapsed');
                    }
                } else {
                    // if neither, decide by size
                    if (isLarge()) sidebar.classList.add('collapsed');
                    else showSidebar();
                }
            });

            overlay.addEventListener('click', function() {
                hideSidebar();
                sidebar.classList.add('collapsed');
            });

            // adapt on resize
            window.addEventListener('resize', function() {
                if (isLarge()) {
                    sidebar.classList.remove('collapsed');
                    sidebar.classList.remove('visible');
                    overlay.classList.remove('visible');
                    main.classList.remove('fullwidth');
                } else {
                    sidebar.classList.add('collapsed');
                }
            });
        })();
    </script>

    {{-- SIDEBAR --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarLinks = document.querySelectorAll('.side-link');

            const currentPage = window.location.pathname.split('/').pop();

            sidebarLinks.forEach(link => {
                // Remove active from all
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


    {{-- ===============================VIEW IMAGE MODAL==============================  --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            const closeBtn = document.getElementById('closeImageModal');

            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    modalImg.src = btn.dataset.img;
                    modal.style.display = 'flex';
                });
            });

            closeBtn.addEventListener('click', () => modal.style.display = 'none');
            modal.addEventListener('click', e => {
                if (e.target === modal) modal.style.display = 'none';
            });
        });
    </script>



    {{-- =======================================Live SCRIPT FOR STATS============================ --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const statCards = document.querySelectorAll(".stat-card");

            function fetchAccountStats() {
                fetch("/administrator/accounts/stats", {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (!res.success) return;


                        if (statCards[0]) statCards[0].querySelector(".count").textContent = res.stats.total
                            .toLocaleString();
                        if (statCards[1]) statCards[1].querySelector(".count").textContent = res.stats.pending
                            .toLocaleString();
                        if (statCards[2]) statCards[2].querySelector(".count").textContent = res.stats.approved
                            .toLocaleString();
                        if (statCards[3]) statCards[3].querySelector(".count").textContent = res.stats.rejected
                            .toLocaleString();
                    })
                    .catch(err => console.error("Error fetching account stats:", err));
            }


            fetchAccountStats();


            setInterval(fetchAccountStats, 10000);
        });
    </script>

    {{-- =============================================AJAX LIVE WITH CAROUSEL MODAL ======================================= --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let lastClickedAction = null;
            let isUserInteracting = false;
            const accountList = document.getElementById("accountList");
            const detailsModal = document.getElementById('detailsModal');
            const closeDetailsModal = document.getElementById('closeDetailsModal');

            // =============================================
            // FILTER AND SEARCH VARIABLES
            // =============================================
            let currentFilter = "{{ $status }}"; // Get initial filter from PHP
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;
            let allAccountItemsHTML = ''; // Store all items HTML for client-side filtering

            // =============================================
            // CAROUSEL VARIABLES
            // =============================================
            let currentSlide = 0;
            let carouselImages = [];
            let keyboardNavHandler = null;

            // =============================================
            // IMAGE OVERLAY FUNCTIONALITY
            // =============================================
            function initImageOverlay() {
                const overlay = document.getElementById('imageOverlay');
                if (!overlay) return;

                // Add event listeners for overlay
                const overlayClose = overlay.querySelector('.overlay-close');
                if (overlayClose) {
                    overlayClose.addEventListener('click', closeImageOverlay);
                }

                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) {
                        closeImageOverlay();
                    }
                });

                // Add escape key handler
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && overlay.style.display === 'flex') {
                        closeImageOverlay();
                    }
                });
            }

            function openImageOverlay(src, label) {
                const overlay = document.getElementById('imageOverlay');
                const overlayImg = document.getElementById('overlayImage');
                const overlayCaption = document.getElementById('overlayCaption');

                if (overlay && overlayImg) {
                    overlayImg.src = src;
                    overlayCaption.textContent = label || '';
                    overlay.style.display = 'flex';

                    // Prevent scrolling on body
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeImageOverlay() {
                const overlay = document.getElementById('imageOverlay');
                if (overlay) {
                    overlay.style.display = 'none';
                    // Restore scrolling
                    document.body.style.overflow = '';
                }
            }

            // =============================================
            // STORE INITIAL ITEMS FOR CLIENT-SIDE FILTERING
            // =============================================
            function storeInitialItems() {
                allAccountItemsHTML = accountList.innerHTML;
                applyClientSideFilter();
            }

            // =============================================
            // CLIENT-SIDE FILTERING FUNCTION
            // =============================================
            function applyClientSideFilter() {
                if (!allAccountItemsHTML) return;

                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = allAccountItemsHTML;
                const allItems = tempDiv.querySelectorAll('.account-item');

                // Clear current list
                accountList.innerHTML = '';

                // Filter items based on currentFilter
                let hasVisibleItems = false;
                allItems.forEach(item => {
                    const itemStatus = item.getAttribute('data-status');

                    if (currentFilter === 'all' || itemStatus === currentFilter) {
                        accountList.appendChild(item.cloneNode(true));
                        hasVisibleItems = true;
                    }
                });

                // Show message if no items
                if (!hasVisibleItems) {
                    accountList.innerHTML = '<p>No applications found.</p>';
                }

                // Rebind all actions after filtering
                bindAllActions();
            }

            // =============================================
            // INITIALIZE MODAL LISTENERS
            // =============================================
            function initModalListeners() {
                if (closeDetailsModal) {
                    closeDetailsModal.addEventListener('click', () => {
                        detailsModal.style.display = 'none';
                        // Clean up keyboard navigation
                        if (keyboardNavHandler) {
                            document.removeEventListener('keydown', keyboardNavHandler);
                            keyboardNavHandler = null;
                        }
                    });
                }

                if (detailsModal) {
                    detailsModal.addEventListener('click', e => {
                        if (e.target === detailsModal) {
                            detailsModal.style.display = 'none';
                            // Clean up keyboard navigation
                            if (keyboardNavHandler) {
                                document.removeEventListener('keydown', keyboardNavHandler);
                                keyboardNavHandler = null;
                            }
                        }
                    });
                }

                // Escape key to close modal
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        detailsModal.style.display = 'none';
                        // Clean up keyboard navigation
                        if (keyboardNavHandler) {
                            document.removeEventListener('keydown', keyboardNavHandler);
                            keyboardNavHandler = null;
                        }
                    }
                });
            }

            // =============================================
            // INITIALIZE SEARCH FUNCTIONALITY (CLIENT-SIDE)
            // =============================================
            function initSearchFunctionality() {
                if (searchInput) {
                    searchInput.addEventListener('input', (e) => {
                        clearTimeout(searchTimeout);

                        searchTimeout = setTimeout(() => {
                            const searchTerm = e.target.value.trim().toLowerCase();

                            if (searchTerm.length >= 2 || searchTerm.length === 0) {
                                // Apply both filter and search
                                applyClientSideFilterAndSearch(searchTerm);
                            }
                        }, 300); // 300ms delay for smoother typing
                    });
                }
            }

            // =============================================
            // COMBINED FILTER AND SEARCH FUNCTION
            // =============================================
            function applyClientSideFilterAndSearch(searchTerm = '') {
                if (!allAccountItemsHTML) return;

                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = allAccountItemsHTML;
                const allItems = tempDiv.querySelectorAll('.account-item');

                // Clear current list
                accountList.innerHTML = '';

                // Filter items based on currentFilter AND search term
                let hasVisibleItems = false;
                allItems.forEach(item => {
                    const itemStatus = item.getAttribute('data-status');
                    const itemText = item.textContent.toLowerCase();

                    // Check if item matches filter
                    const matchesFilter = currentFilter === 'all' || itemStatus === currentFilter;

                    // Check if item matches search (if search term provided)
                    const matchesSearch = !searchTerm || itemText.includes(searchTerm);

                    if (matchesFilter && matchesSearch) {
                        accountList.appendChild(item.cloneNode(true));
                        hasVisibleItems = true;
                    }
                });

                // Show message if no items
                if (!hasVisibleItems) {
                    accountList.innerHTML = '<p>No applications found.</p>';
                }

                // Rebind all actions after filtering
                bindAllActions();
            }

            // =============================================
            // INITIALIZE FILTER FUNCTIONALITY (CLIENT-SIDE)
            // =============================================
            function initFilterFunctionality() {
                // Set initial filter from PHP or default to 'pending'
                const initialFilter = "{{ $status }}" || 'pending';
                currentFilter = initialFilter;

                // Highlight the active button on page load
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    if (btn.getAttribute('data-status') === initialFilter) {
                        btn.classList.add('active');
                    }
                });

                setTimeout(() => {
                    applyClientSideFilter();
                }, 50);

                // Handle filter button clicks
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const newFilter = btn.getAttribute('data-status');

                        // Don't do anything if clicking the already active filter
                        if (newFilter === currentFilter) return;

                        // Update active button
                        document.querySelectorAll('.filter-btn').forEach(b => {
                            b.classList.remove('active');
                        });
                        btn.classList.add('active');

                        // Update current filter
                        currentFilter = newFilter;

                        // Apply client-side filter instantly
                        applyClientSideFilterAndSearch(searchInput?.value.trim().toLowerCase() ||
                            '');

                        // Update URL without reloading page
                        updateURLWithFilter(newFilter);
                    });
                });
            }

            // =============================================
            // UPDATE URL WITH FILTER (NO PAGE RELOAD)
            // =============================================
            function updateURLWithFilter(filter) {
                const url = new URL(window.location);
                if (filter && filter !== 'all') {
                    url.searchParams.set('status', filter);
                } else {
                    url.searchParams.delete('status');
                }
                window.history.pushState({}, '', url);
            }

            // =============================================
            // CAROUSEL FUNCTIONS
            // =============================================
            function goToSlide(index) {
                if (carouselImages.length === 0) return;

                currentSlide = index;
                updateCarousel();
            }

            function nextSlide() {
                if (carouselImages.length === 0) return;

                currentSlide = (currentSlide + 1) % carouselImages.length;
                updateCarousel();
            }

            function prevSlide() {
                if (carouselImages.length === 0) return;

                currentSlide = (currentSlide - 1 + carouselImages.length) % carouselImages.length;
                updateCarousel();
            }

            function updateCarousel() {
                const carouselSlides = document.getElementById('carouselSlides');
                if (!carouselSlides) return;

                // Update slide position
                carouselSlides.style.transform = `translateX(-${currentSlide * 100}%)`;

                // Update indicators
                const indicators = document.querySelectorAll('.carousel-indicator');
                indicators.forEach((ind, idx) => {
                    ind.classList.toggle('active', idx === currentSlide);
                });

                // Update thumbnails
                const thumbnails = document.querySelectorAll('.thumbnail-item');
                thumbnails.forEach((thumb, idx) => {
                    thumb.classList.toggle('active', idx === currentSlide);
                });
            }

            // =============================================
            // HELPER FUNCTION TO OPEN IMAGE IN MODAL
            // =============================================
            function openImageModal(src) {
                const modal = document.getElementById('imageModal');
                const modalImg = document.getElementById('modalImage');
                if (modal && modalImg) {
                    modalImg.src = src;
                    modal.style.display = 'flex';
                }
            }

            // =============================================
            // BIND ALL ACTIONS (EVENT LISTENERS)
            // =============================================
            function bindAllActions() {
                // Toggle expandable details
                document.querySelectorAll(".summary-row").forEach(row => {
                    row.addEventListener("click", (e) => {
                        // Don't toggle if clicking on buttons
                        if (e.target.closest('button')) return;

                        const details = row.nextElementSibling;
                        const icon = row.querySelector("i");
                        if (details) {
                            details.classList.toggle("hidden");
                            if (icon) icon.classList.toggle("ri-arrow-up-s-line");
                            isUserInteracting = true;
                            clearInteractionAfterDelay();
                        }
                    });
                });

                // Bind image view buttons
                document.querySelectorAll('.view-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation(); // Prevent summary row toggle
                        const modal = document.getElementById('imageModal');
                        const modalImg = document.getElementById('modalImage');
                        if (modal && modalImg) {
                            modalImg.src = btn.dataset.img;
                            modal.style.display = 'flex';
                        }
                        isUserInteracting = true;
                        clearInteractionAfterDelay();
                    });
                });

                // View Details button click
                document.querySelectorAll('.btn-view-details').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const accountItem = btn.closest('.account-item');
                        const summaryRow = accountItem.querySelector('.summary-row');

                        // Extract data from the DOM
                        // First extract the full name (with middle name) from typed information
                        const typedInfo = accountItem.querySelector('.typed-info');

                        // Extract name with middle name from the first list item
                        const nameListItem = typedInfo?.querySelector('li:nth-child(1)');
                        let name = '';
                        if (nameListItem) {
                            // Get the full text and remove "Name:" prefix
                            const nameText = nameListItem.textContent || '';
                            name = nameText.replace('Name:', '').trim();
                        } else {
                            // Fallback to the name from strong element
                            name = accountItem.querySelector('strong')?.textContent || '';
                        }

                        const status = accountItem.getAttribute('data-status') || '';
                        const statusText = accountItem.querySelector('.tag')?.textContent || '';

                        // Extract typed information - other fields
                        const dob = typedInfo?.querySelector('li:nth-child(2) strong')?.nextSibling
                            ?.textContent?.trim() || 'N/A';
                        const sex = typedInfo?.querySelector('li:nth-child(3) strong')?.nextSibling
                            ?.textContent?.trim() || '';
                        const idType = typedInfo?.querySelector('li:nth-child(4) strong')
                            ?.nextSibling?.textContent?.trim() || '';
                        const idNumber = typedInfo?.querySelector('li:nth-child(5) strong')
                            ?.nextSibling?.textContent?.trim() || '';
                        const idExpiry = typedInfo?.querySelector('li:nth-child(6) strong')
                            ?.nextSibling?.textContent?.trim() || 'N/A';

                        // Extract reference and applied date - FIXED selector
                        const summaryContent = summaryRow.querySelector('div > div:nth-child(2)');
                        const refDiv = summaryContent?.querySelector('div:nth-child(3)');
                        const refText = refDiv?.textContent || '';
                        const reference = refText.split('•')[0]?.trim() || '';
                        const applied = refText.split('•')[1]?.replace('Applied:', '').trim() || '';

                        // Extract notes if any
                        const notesEl = accountItem.querySelector('.notes div');
                        const notes = notesEl?.textContent?.trim() || '';

                        // Get document images
                        const documents = accountItem.querySelector('.documents');
                        carouselImages = [];

                        if (documents) {
                            documents.querySelectorAll('.view-btn').forEach(viewBtn => {
                                const label = viewBtn.getAttribute('data-type') ||
                                    'Document'; // Get from data-type attribute
                                const src = viewBtn.getAttribute('data-img');
                                if (src) {
                                    carouselImages.push({
                                        label,
                                        src
                                    });
                                }
                            });
                        }


                        // Populate modal details
                        document.getElementById('detailName').textContent = name;
                        document.getElementById('detailDob').textContent = dob;
                        document.getElementById('detailSex').textContent = sex.toUpperCase();
                        document.getElementById('detailIdType').textContent = idType;
                        document.getElementById('detailIdNumber').textContent = idNumber;
                        document.getElementById('detailIdExpiry').textContent = idExpiry;
                        document.getElementById('detailStatus').textContent = statusText;
                        document.getElementById('detailStatus').className =
                            `proof-detail-value ${status}`;
                        document.getElementById('detailReference').textContent = reference;
                        document.getElementById('detailApplied').textContent = applied;

                        // Show/hide notes
                        const notesItem = document.getElementById('notesItem');
                        const detailNotes = document.getElementById('detailNotes');
                        if (notes) {
                            notesItem.style.display = 'flex';
                            detailNotes.textContent = notes;
                        } else {
                            notesItem.style.display = 'none';
                        }

                        // Populate carousel
                        const carouselSlides = document.getElementById('carouselSlides');
                        const carouselIndicators = document.getElementById('carouselIndicators');
                        const thumbnailsContainer = document.getElementById('thumbnailsContainer');
                        const thumbnailGallery = document.getElementById('thumbnailGallery');

                        carouselSlides.innerHTML = '';
                        carouselIndicators.innerHTML = '';
                        thumbnailsContainer.innerHTML = '';

                        if (carouselImages.length > 0) {
                            currentSlide = 0;

                            // Create slides
                            carouselImages.forEach((img, index) => {
                                // Create slide
                                const slide = document.createElement('div');
                                slide.className = 'carousel-item';
                                slide.innerHTML = `
                                <img src="${img.src}"
                                     alt="${img.label}"
                                     data-full="${img.src}"
                                     data-label="${img.label}"
                                     class="carousel-image">
                                <div class="carousel-caption">${img.label}</div>
                            `;
                                carouselSlides.appendChild(slide);

                                // Create indicator
                                const indicator = document.createElement('button');
                                indicator.className =
                                    `carousel-indicator ${index === 0 ? 'active' : ''}`;
                                indicator.setAttribute('data-index', index);
                                indicator.addEventListener('click', () => goToSlide(index));
                                carouselIndicators.appendChild(indicator);

                                // Create thumbnail
                                const thumbnail = document.createElement('div');
                                thumbnail.className =
                                    `thumbnail-item ${index === 0 ? 'active' : ''}`;
                                thumbnail.setAttribute('data-index', index);
                                thumbnail.innerHTML = `
                                <img src="${img.src}" alt="${img.label}" onclick="goToSlide(${index})">
                                <div class="thumbnail-label">${img.label}</div>
                            `;
                                thumbnailsContainer.appendChild(thumbnail);
                            });

                            // Show thumbnail gallery if multiple images
                            if (carouselImages.length > 1) {
                                thumbnailGallery.style.display = 'block';
                            } else {
                                thumbnailGallery.style.display = 'none';
                            }

                            // Set initial slide position
                            updateCarousel();

                            // Add event listeners to carousel buttons
                            const prevBtn = document.getElementById('carouselPrev');
                            const nextBtn = document.getElementById('carouselNext');

                            if (prevBtn && nextBtn) {
                                prevBtn.onclick = prevSlide;
                                nextBtn.onclick = nextSlide;
                            }

                            // Add click listeners to carousel images
                            setTimeout(() => {
                                document.querySelectorAll('.carousel-image').forEach(
                                    img => {
                                        img.addEventListener('click', (e) => {
                                            e.stopPropagation();
                                            const src = img.getAttribute('src');
                                            const label = img.getAttribute(
                                                'data-label') || img.alt;
                                            openImageOverlay(src, label);
                                        });
                                    });
                            }, 100);

                            // Add keyboard navigation
                            if (keyboardNavHandler) {
                                document.removeEventListener('keydown', keyboardNavHandler);
                            }

                            keyboardNavHandler = (e) => {
                                if (e.key === 'ArrowLeft') prevSlide();
                                if (e.key === 'ArrowRight') nextSlide();
                            };

                            document.addEventListener('keydown', keyboardNavHandler);

                        } else {
                            // No images message
                            carouselSlides.innerHTML = `
                            <div class="no-images">
                                <i class="ri-image-line"></i>
                                <p>No documents available</p>
                            </div>
                        `;
                            thumbnailGallery.style.display = 'none';
                        }

                        // Show modal
                        detailsModal.style.display = 'flex';
                        isUserInteracting = true;
                        clearInteractionAfterDelay();
                    });
                });

                // Reupload UI logic
                document.querySelectorAll(".account-item").forEach(item => {
                    // FIXED: Changed from class selector to ID selector
                    const btnShow = item.querySelector("#btnShowReupload");
                    const btnSubmit = item.querySelector("#btnSubmitReupload");
                    const btnCancel = item.querySelector("#btnCancelReupload");
                    const btnApprove = item.querySelector(".btn-approve");
                    const btnReject = item.querySelector(".btn-reject");
                    const box = item.querySelector("#reuploadOptions");
                    const checkboxes = item.querySelectorAll("#reuploadCheckboxes input[type='checkbox']");

                    if (btnShow && box) {
                        function resetReupload() {
                            checkboxes.forEach(cb => cb.checked = false);
                            box.style.display = "none";
                            if (btnSubmit) btnSubmit.style.display = "none";
                            if (btnCancel) btnCancel.style.display = "none";
                            if (btnShow) btnShow.style.display = "inline-flex";
                            if (btnApprove) btnApprove.style.display = "inline-flex";
                            if (btnReject) btnReject.style.display = "inline-flex";
                            if (btnSubmit) btnSubmit.disabled = true;
                        }

                        btnShow.addEventListener("click", e => {
                            e.stopPropagation();
                            isUserInteracting = true;
                            box.style.display = "block";
                            btnShow.style.display = "none";
                            if (btnSubmit) btnSubmit.style.display = "inline-flex";
                            if (btnCancel) btnCancel.style.display = "inline-flex";
                            if (btnApprove) btnApprove.style.display = "none";
                            if (btnReject) btnReject.style.display = "none";
                            clearInteractionAfterDelay();
                        });

                        if (btnCancel) btnCancel.addEventListener("click", resetReupload);

                        function toggleButton() {
                            const checked = Array.from(checkboxes).some(cb => cb.checked);
                            if (btnSubmit) btnSubmit.disabled = !checked;
                        }

                        checkboxes.forEach(cb => cb.addEventListener("change", toggleButton));
                        toggleButton();
                    }
                });

                // Decision form handling
                document.querySelectorAll("form[action*='decision'] button[name='action']").forEach(btn => {
                    btn.addEventListener("click", () => {
                        lastClickedAction = btn.value;
                        isUserInteracting = true;
                        clearInteractionAfterDelay();
                    });
                });

                // Form submission
                document.querySelectorAll("form[action*='decision']").forEach(form => {
                    form.addEventListener("submit", e => {
                        e.preventDefault();
                        const formData = new FormData(form);
                        if (lastClickedAction) formData.set("action", lastClickedAction);

                        fetch(form.getAttribute("action"), {
                                method: "POST",
                                headers: {
                                    "X-Requested-With": "XMLHttpRequest"
                                },
                                body: formData
                            })
                            .then(async res => {
                                const data = await res.json().catch(() => null);
                                if (!data) throw new Error("Invalid JSON response");

                                if (data.success) {
                                    let color = "#16a34a";
                                    if (data.status === "rejected") color = "#ef4444";
                                    if (data.status === "reupload") color = "#3b82f6";
                                    showToast(data.message, color);

                                    // Refresh via AJAX to get updated data
                                    refreshAccountListViaAJAX();
                                } else {
                                    showToast("Error: " + (data.message || "Action failed"),
                                        "#ef4444");
                                }
                            })
                            .catch(() => showToast("Server error, please try again.", "#ef4444"));
                    });
                });
            }

            // =============================================
            // REFRESH ACCOUNT LIST VIA AJAX (FOR AUTO-REFRESH)
            // =============================================
            function refreshAccountListViaAJAX() {
                const url = new URL("{{ route('account.page') }}", window.location.origin);

                // Remove any status parameter to get ALL data
                url.searchParams.delete('status');

                // Track which items are currently expanded before refresh
                const expandedItems = new Set();
                document.querySelectorAll('.account-item .summary-row').forEach((row, index) => {
                    const details = row.nextElementSibling;
                    if (details && !details.classList.contains('hidden')) {
                        expandedItems.add(index);
                    }
                });

                fetch(url, {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    })
                    .then(res => res.text())
                    .then(html => {
                        const temp = document.createElement("div");
                        temp.innerHTML = html;
                        const newList = temp.querySelector("#accountList");

                        if (newList) {
                            // Update our stored HTML with the new data
                            allAccountItemsHTML = newList.innerHTML;

                            // Apply current filter to the new data
                            applyClientSideFilterAndSearch(searchInput?.value.trim().toLowerCase() || '');

                            // Restore expanded states
                            document.querySelectorAll('.account-item .summary-row').forEach((row, index) => {
                                if (expandedItems.has(index)) {
                                    const details = row.nextElementSibling;
                                    const icon = row.querySelector("i");
                                    if (details) {
                                        details.classList.remove('hidden');
                                        if (icon) {
                                            icon.classList.remove('ri-arrow-down-s-line');
                                            icon.classList.add('ri-arrow-up-s-line');
                                        }
                                    }
                                }
                            });
                        }
                    })
                    .catch(() => showToast("Error refreshing list.", "#ef4444"));
            }
            // =============================================
            // AUTO-REFRESH CONTROL
            // =============================================
            function clearInteractionAfterDelay() {
                setTimeout(() => {
                    isUserInteracting = false;
                }, 8000);
            }

            // =============================================
            // INITIALIZATION
            // =============================================
            storeInitialItems(); // Store initial items for client-side filtering
            initImageOverlay();
            initModalListeners();
            initSearchFunctionality();
            initFilterFunctionality();
            bindAllActions();

            // Set up polling for auto-refresh (AJAX)
            setInterval(() => {
                if (!isUserInteracting) refreshAccountListViaAJAX();
            }, 7000);

            // =============================================
            // EXPOSE FUNCTIONS TO GLOBAL SCOPE
            // =============================================
            window.goToSlide = goToSlide;
            window.openImageModal = openImageModal;
            window.openImageOverlay = openImageOverlay;
            window.closeImageOverlay = closeImageOverlay;
        });
    </script>

    {{-- ====================================================TOAST MESSAGE ======================================== --}}
    <script>
        function showToast(message, color = "#16a34a") {
            const toast = document.getElementById("toast");
            toast.textContent = message;
            toast.style.background = color;
            toast.style.opacity = "1";
            toast.style.pointerEvents = "auto";

            setTimeout(() => {
                toast.style.opacity = "0";
                toast.style.pointerEvents = "none";
            }, 3000);
        }
    </script>



</body>

</html>
