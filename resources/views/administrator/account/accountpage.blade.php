<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tulong Kabataan | Administrator Dashboard</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    <!-- Remixicon -->
    <!-- Charts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('administrator.partials.admin-theme')
</head>

<body class="admin-page admin-account-page">
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

            <div class="logo-word"><img src="{{ asset('img/log.png') }}" alt="">
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

    <main id="mainContent" class="main">

        <div class="container">
            <section class="page-header">
                <h1>Account Verification</h1>
                <p>Review and manage user account decision requests</p>
            </section>

            {{-- Verification Provider Switcher Panel --}}
            <section class="provider-panel" style="margin-bottom:24px;padding:16px 20px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px">
                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:12px">
                    <h3 style="margin:0;font-size:15px;display:flex;align-items:center;gap:8px">
                        <i class="ri-shield-keyhole-line" style="color:#2563eb"></i>
                        Verification Provider
                    </h3>
                    <div id="providerStatus" style="font-size:12px;color:#6b7280">Loading...</div>
                </div>

                <div id="providerButtons" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                    <button type="button" class="provider-btn" data-provider="didit"
                        style="padding:8px 16px;border-radius:8px;border:2px solid #e2e8f0;background:#fff;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s">
                        <i class="ri-shield-check-line"></i> Didit
                        <span style="font-size:10px;display:block;font-weight:400;color:#6b7280">Face match + Auto-approve</span>
                    </button>
                    <button type="button" class="provider-btn" data-provider="ocr_space"
                        style="padding:8px 16px;border-radius:8px;border:2px solid #e2e8f0;background:#fff;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s">
                        <i class="ri-file-text-line"></i> OCR.Space
                        <span style="font-size:10px;display:block;font-weight:400;color:#6b7280">Text only + Manual review</span>
                    </button>
                    <button type="button" class="provider-btn" data-provider="google_vision"
                        style="padding:8px 16px;border-radius:8px;border:2px solid #e2e8f0;background:#fff;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s">
                        <i class="ri-google-line"></i> Google Vision
                        <span style="font-size:10px;display:block;font-weight:400;color:#6b7280">Text only + Manual review</span>
                    </button>
                </div>

                <div id="providerQuota" style="margin-top:12px;font-size:12px;color:#374151"></div>
                <div id="providerNote" style="margin-top:8px;font-size:11px;color:#6b7280;font-style:italic"></div>
            </section>

            <section class="stats-grid" aria-hidden="false">

                <!-- 🟦 Total Accounts -->
                <div class="stat-card stat-card--all">
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
                <div class="stat-card stat-card--pending">
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
                <div class="stat-card stat-card--approved">
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
                <div class="stat-card stat-card--rejected">
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


            <section class="card verification-requests" aria-labelledby="requests-heading">
                <div class="card-header">
                    <div>
                        <h3 id="requests-heading">
                            Account Verification Requests
                        </h3>
                        <p>Review submitted identity details and documents.</p>
                    </div>
                    <div class="filter-buttons" role="tablist">
                        <button class="filter-btn {{ $status === 'pending' ? 'active' : '' }}" data-status="pending"
                            data-active="true" type="button">
                            Pending
                        </button>
                        <button class="filter-btn {{ $status === 'approved' ? 'active' : '' }}" data-status="approved"
                            type="button">
                            Approved
                        </button>
                        <button class="filter-btn {{ $status === 'rejected' ? 'active' : '' }}" data-status="rejected"
                            type="button">
                            Rejected
                        </button>
                        <button class="filter-btn {{ $status === 'reupload' ? 'active' : '' }}" data-status="reupload"
                            type="button">
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
                            <div class="account-item" data-status="{{ $req->status }}">

                                {{-- Compact Summary Row --}}
                                <div class="summary-row" role="button" tabindex="0" aria-expanded="false"
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
                                                    $avatar = file_url($avatar);
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
                                    @php
                                        $diditCredentials = $req->diditCredentialSnapshot();
                                        $hasDiditCredentials = ($req->provider_used ?? null) === 'didit'
                                            && !empty($diditCredentials);
                                        $credentialName = $req->extracted_full_name
                                            ?: ($diditCredentials['full_name'] ?? null);
                                        $credentialBirthdate = $req->extracted_birthdate
                                            ?: ($diditCredentials['birthdate'] ?? null);
                                        $credentialIdType = $req->id_type_detected
                                            ?: ($diditCredentials['document_type_code'] ?? $diditCredentials['document_type'] ?? null);
                                        $credentialIdTypeLabel = match ($credentialIdType) {
                                            'philid' => 'PhilSys ID',
                                            'drivers_license' => "Driver's License",
                                            'unknown', null, '' => $diditCredentials['document_type'] ?? 'N/A',
                                            default => ucfirst(str_replace('_', ' ', (string) $credentialIdType)),
                                        };
                                        $credentialIdNumber = $req->extracted_id_number
                                            ?: ($diditCredentials['document_number'] ?? $diditCredentials['personal_number'] ?? null);
                                        $credentialExpiry = $req->extracted_expiration_date
                                            ?: ($diditCredentials['expiration_date'] ?? null);
                                        $credentialSex = $req->extracted_sex
                                            ?: ($diditCredentials['gender'] ?? null);
                                        $credentialNationality = $req->extracted_nationality
                                            ?: ($diditCredentials['nationality'] ?? null);
                                        $credentialAddress = $req->extracted_address
                                            ?: ($diditCredentials['formatted_address'] ?? $diditCredentials['address'] ?? null);
                                        $formatCredentialDate = function ($value) {
                                            if (empty($value)) {
                                                return 'N/A';
                                            }

                                            try {
                                                return \Carbon\Carbon::parse($value)->format('M d, Y');
                                            } catch (\Throwable) {
                                                return (string) $value;
                                            }
                                        };
                                        $formatDiditScore = function ($value) {
                                            if (!is_numeric($value)) {
                                                return null;
                                            }

                                            $score = (float) $value;
                                            if ($score <= 1) {
                                                $score *= 100;
                                            }

                                            return number_format($score, 0) . '%';
                                        };
                                        $hasLocalDocuments = !empty($req->id_front_path)
                                            || !empty($req->id_back_path)
                                            || !empty($req->face_photo_path)
                                            || !empty($req->selfie_path);
                                    @endphp

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

                                    @if ($hasDiditCredentials)
                                        <div class="didit-credentials"
                                            style="margin:15px 0;border:1px solid #bbf7d0;border-radius:8px;background:#f0fdf4;padding:14px">
                                            <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:10px">
                                                <h5 style="margin:0;font-size:14px;display:flex;align-items:center;gap:6px;color:#14532d">
                                                    <i class="ri-shield-check-line"></i>
                                                    Approved Credentials from Didit
                                                </h5>
                                                <span style="background:#16a34a;color:#fff;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700">
                                                    {{ ucfirst((string) ($diditCredentials['decision_status'] ?? $diditCredentials['id_status'] ?? $req->status)) }}
                                                </span>
                                            </div>
                                            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px;font-size:13px;color:#14532d">
                                                <div>
                                                    <div style="font-size:11px;text-transform:uppercase;color:#166534">Verified Name</div>
                                                    <div style="font-weight:700;color:#052e16">{{ $credentialName ?: 'N/A' }}</div>
                                                </div>
                                                <div>
                                                    <div style="font-size:11px;text-transform:uppercase;color:#166534">Date of Birth</div>
                                                    <div style="font-weight:700;color:#052e16">{{ $formatCredentialDate($credentialBirthdate) }}</div>
                                                </div>
                                                <div>
                                                    <div style="font-size:11px;text-transform:uppercase;color:#166534">Document Type</div>
                                                    <div style="font-weight:700;color:#052e16">{{ $credentialIdTypeLabel }}</div>
                                                </div>
                                                <div>
                                                    <div style="font-size:11px;text-transform:uppercase;color:#166534">Document Number</div>
                                                    <div style="font-weight:700;color:#052e16;word-break:break-all">{{ $credentialIdNumber ?: 'N/A' }}</div>
                                                </div>
                                                <div>
                                                    <div style="font-size:11px;text-transform:uppercase;color:#166534">Expiration</div>
                                                    <div style="font-weight:700;color:#052e16">{{ $formatCredentialDate($credentialExpiry) }}</div>
                                                </div>
                                                <div>
                                                    <div style="font-size:11px;text-transform:uppercase;color:#166534">Sex</div>
                                                    <div style="font-weight:700;color:#052e16">{{ $credentialSex ?: 'N/A' }}</div>
                                                </div>
                                                @if (!empty($credentialNationality))
                                                    <div>
                                                        <div style="font-size:11px;text-transform:uppercase;color:#166534">Nationality</div>
                                                        <div style="font-weight:700;color:#052e16">{{ $credentialNationality }}</div>
                                                    </div>
                                                @endif
                                                @if (!empty($diditCredentials['issuing_state_name']) || !empty($diditCredentials['issuing_state']))
                                                    <div>
                                                        <div style="font-size:11px;text-transform:uppercase;color:#166534">Issuing State</div>
                                                        <div style="font-weight:700;color:#052e16">
                                                            {{ $diditCredentials['issuing_state_name'] ?? $diditCredentials['issuing_state'] }}
                                                        </div>
                                                    </div>
                                                @endif
                                                @if (!empty($credentialAddress))
                                                    <div style="grid-column:1/-1">
                                                        <div style="font-size:11px;text-transform:uppercase;color:#166534">Address</div>
                                                        <div style="font-weight:700;color:#052e16">{{ $credentialAddress }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;font-size:12px">
                                                @if (!empty($diditCredentials['id_status']))
                                                    <span style="background:#dcfce7;color:#166534;border:1px solid #86efac;border-radius:999px;padding:4px 9px">
                                                        ID: {{ ucfirst((string) $diditCredentials['id_status']) }}
                                                    </span>
                                                @endif
                                                @if (!empty($diditCredentials['liveness_status']))
                                                    <span style="background:#dcfce7;color:#166534;border:1px solid #86efac;border-radius:999px;padding:4px 9px">
                                                        Liveness: {{ ucfirst((string) $diditCredentials['liveness_status']) }}
                                                        @if ($formatDiditScore($diditCredentials['liveness_score'] ?? null))
                                                            ({{ $formatDiditScore($diditCredentials['liveness_score']) }})
                                                        @endif
                                                    </span>
                                                @endif
                                                @if (!empty($diditCredentials['face_match_status']))
                                                    <span style="background:#dcfce7;color:#166534;border:1px solid #86efac;border-radius:999px;padding:4px 9px">
                                                        Face Match: {{ ucfirst((string) $diditCredentials['face_match_status']) }}
                                                        @if ($formatDiditScore($diditCredentials['face_match_score'] ?? null))
                                                            ({{ $formatDiditScore($diditCredentials['face_match_score']) }})
                                                        @endif
                                                    </span>
                                                @endif
                                            </div>
                                            <div style="margin-top:10px;font-size:12px;color:#166534">
                                                Captured by Didit hosted verification. Stored here as the admin audit record for this approval.
                                            </div>
                                        </div>
                                    @endif

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
                                                    data-img="{{ route('accounts.verification-document', [(string) $req->getKey(), 'id_front']) }}"
                                                    data-type="ID Front">
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
                                                    data-img="{{ route('accounts.verification-document', [(string) $req->getKey(), 'id_back']) }}"
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
                                                    data-img="{{ route('accounts.verification-document', [(string) $req->getKey(), 'face_photo']) }}"
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
                                                    data-img="{{ route('accounts.verification-document', [(string) $req->getKey(), 'selfie']) }}"
                                                    data-type="Selfie with ID">
                                                    View
                                                </button>
                                            </div>
                                        @endif

                                        @if (!$hasLocalDocuments && ($req->provider_used ?? null) === 'didit')
                                            <div class="doc"
                                                style="display:flex;align-items:center;gap:12px;padding:10px;border-radius:8px;background:#f9fafb;border:1px dashed #cbd5e1">
                                                <div class="icon-box"
                                                    style="background:#eefdf5;border-radius:8px;width:36px;height:36px;display:flex;align-items:center;justify-content:center">
                                                    <i class="ri-shield-check-line" style="color:#16a34a"></i>
                                                </div>
                                                <div style="flex:1">
                                                    <div style="font-weight:600">Documents captured by Didit</div>
                                                    <div style="font-size:12px;color:#6b7280">
                                                        This verification used Didit's hosted capture flow, so no local uploaded file is stored.
                                                    </div>
                                                </div>
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

                                    {{-- Automated Review Panel --}}
                                    @if (!empty($req->confidence_score) || !empty($req->id_type_detected) || !empty($req->fraud_warnings) || !empty($req->extracted_full_name) || $req->decision_source)
                                        @php
                                            $score = (int) ($req->confidence_score ?? 0);
                                            $autoApprove = (int) config('id_verification.scoring.auto_approve', 85);
                                            $manualReview = (int) config('id_verification.scoring.manual_review', 60);
                                            $scoreColor = $score >= $autoApprove ? '#16a34a' : ($score >= $manualReview ? '#ca8a04' : '#dc2626');
                                            $sourceLabel = $req->decision_source === 'auto'
                                                ? 'Automated Review'
                                                : ($req->decision_source === 'admin' ? 'Manual Admin Decision' : 'Pending Auto Review');
                                            $providerLabel = $req->provider_used ?: 'none';
                                            $providerDisplay = match ($providerLabel) {
                                                'didit' => 'Didit',
                                                'ocr_space' => 'OCR.Space',
                                                'google_vision' => 'Google Vision',
                                                default => ucfirst(str_replace('_', ' ', $providerLabel)),
                                            };
                                        @endphp
                                        <div class="auto-review-panel"
                                            style="margin:15px 0;border:1px solid #e5e7eb;border-radius:8px;background:#fafafa;padding:14px">
                                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;flex-wrap:wrap;gap:8px">
                                                <h5 style="margin:0;font-size:14px;display:flex;align-items:center;gap:6px">
                                                    <i class="ri-shield-keyhole-line" style="color:#2563eb"></i>
                                                    Automated Review
                                                </h5>
                                                @if (!empty($req->confidence_score))
                                                    <span style="background:{{ $scoreColor }};color:#fff;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600">
                                                        Score: {{ $score }}/100
                                                    </span>
                                                @endif
                                            </div>
                                            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px;font-size:13px;color:#374151">
                                                <div>
                                                    <div style="font-size:11px;color:#6b7280;text-transform:uppercase">Decision Source</div>
                                                    <div style="font-weight:600">{{ $sourceLabel }}</div>
                                                </div>
                                                <div>
                                                    <div style="font-size:11px;color:#6b7280;text-transform:uppercase">Provider Used</div>
                                                    <div style="font-weight:600">{{ $providerDisplay }}</div>
                                                </div>
                                                @if (!empty($req->provider_reference_id))
                                                    <div>
                                                        <div style="font-size:11px;color:#6b7280;text-transform:uppercase">Provider Reference</div>
                                                        <div style="font-weight:600;word-break:break-all">{{ $req->provider_reference_id }}</div>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div style="font-size:11px;color:#6b7280;text-transform:uppercase">Detected ID Type</div>
                                                    <div style="font-weight:600">
                                                        @if ($req->id_type_detected === 'philid')
                                                            PhilSys ID
                                                        @elseif($req->id_type_detected === 'drivers_license')
                                                            Driver's License
                                                        @elseif($req->id_type_detected === 'unknown')
                                                            <span style="color:#dc2626">Unknown</span>
                                                        @else
                                                            —
                                                        @endif
                                                    </div>
                                                </div>
                                                <div>
                                                    <div style="font-size:11px;color:#6b7280;text-transform:uppercase">Selected ID Type</div>
                                                    <div style="font-weight:600">{{ ucfirst(str_replace('_', ' ', (string) $req->id_type)) }}</div>
                                                </div>
                                            </div>

                                            @if (!empty($req->decision_reason))
                                                <div style="margin-top:12px;padding:10px;background:#fff;border-radius:6px;border-left:3px solid {{ $scoreColor }};font-size:13px;color:#1f2937">
                                                    <strong>Reason:</strong> {{ $req->decision_reason }}
                                                </div>
                                            @endif

                                            @if (!empty($req->fraud_warnings))
                                                <div style="margin-top:12px">
                                                    <div style="font-size:12px;font-weight:600;color:#dc2626;margin-bottom:6px">
                                                        <i class="ri-alarm-warning-line"></i> Fraud Warnings
                                                    </div>
                                                    <ul style="margin:0;padding-left:20px;font-size:13px;color:#7f1d1d">
                                                        @foreach ($req->fraud_warnings as $warn)
                                                            <li>
                                                                <span style="text-transform:uppercase;font-size:10px;background:#fee2e2;color:#991b1b;padding:1px 6px;border-radius:4px;margin-right:4px">
                                                                    {{ $warn['severity'] ?? 'info' }}
                                                                </span>
                                                                {{ $warn['reason'] ?? '' }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            @if (!empty($req->extracted_full_name) || !empty($req->extracted_birthdate) || !empty($req->extracted_id_number) || !empty($req->extracted_address))
                                                <div style="margin-top:12px">
                                                    <div style="font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Extracted from ID</div>
                                                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:8px;font-size:13px">
                                                        @if (!empty($req->extracted_full_name))
                                                            <div><strong>Name:</strong> {{ $req->extracted_full_name }}</div>
                                                        @endif
                                                        @if (!empty($req->extracted_birthdate))
                                                            <div><strong>Birthdate:</strong>
                                                                {{ \Carbon\Carbon::parse($req->extracted_birthdate)->format('M d, Y') }}
                                                            </div>
                                                        @endif
                                                        @if (!empty($req->extracted_id_number))
                                                            <div><strong>ID Number:</strong> {{ $req->extracted_id_number }}</div>
                                                        @endif
                                                        @if (!empty($req->extracted_expiration_date))
                                                            <div><strong>Expiration:</strong>
                                                                {{ \Carbon\Carbon::parse($req->extracted_expiration_date)->format('M d, Y') }}
                                                            </div>
                                                        @endif
                                                        @if (!empty($req->extracted_sex))
                                                            <div><strong>Sex:</strong> {{ $req->extracted_sex }}</div>
                                                        @endif
                                                        @if (!empty($req->extracted_nationality))
                                                            <div><strong>Nationality:</strong> {{ $req->extracted_nationality }}</div>
                                                        @endif
                                                        @if (!empty($req->extracted_address))
                                                            <div style="grid-column: 1/-1"><strong>Address:</strong>
                                                                {{ $req->extracted_address }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            @if (!empty($req->score_breakdown))
                                                <details style="margin-top:12px">
                                                    <summary style="cursor:pointer;font-size:12px;color:#2563eb;font-weight:600">Score breakdown</summary>
                                                    <table style="width:100%;font-size:12px;margin-top:8px;border-collapse:collapse">
                                                        @foreach ($req->score_breakdown as $key => $row)
                                                            @if (is_array($row) && isset($row['weight']))
                                                                <tr style="border-bottom:1px solid #f3f4f6">
                                                                    <td style="padding:4px 8px;text-transform:capitalize;color:#374151">
                                                                        {{ str_replace('_', ' ', $key) }}
                                                                    </td>
                                                                    <td style="padding:4px 8px;text-align:right;font-weight:600">
                                                                        {{ $row['points'] ?? 0 }} / {{ $row['weight'] }}
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </table>
                                                </details>
                                            @endif

                                            @if (!empty($req->image_quality_report) && isset($req->image_quality_report['metrics']))
                                                <details style="margin-top:8px">
                                                    <summary style="cursor:pointer;font-size:12px;color:#2563eb;font-weight:600">Image quality metrics</summary>
                                                    <pre style="margin-top:6px;background:#f3f4f6;padding:8px;border-radius:6px;font-size:11px;overflow:auto">{{ json_encode($req->image_quality_report['metrics'], JSON_PRETTY_PRINT) }}</pre>
                                                </details>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Actions --}}
                                    @if ($req->status === 'pending' || ($req->status === 'approved' && $req->wasAutoDecided()) || ($req->status === 'rejected' && $req->wasAutoDecided()))
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

                                                @if ($req->wasAutoDecided() && $req->status !== 'pending')
                                                    <div style="background:#fef3c7;color:#92400e;border-left:3px solid #f59e0b;padding:8px 10px;border-radius:6px;font-size:12px;margin-bottom:10px">
                                                        <i class="ri-information-line"></i>
                                                        This account was auto-{{ $req->status }} by the system. Use the
                                                        buttons below to override.
                                                    </div>
                                                @endif

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
                                                        @if ($req->selfie_path)
                                                            <label><input type="checkbox" name="reupload_fields[]"
                                                                    value="selfie"> Selfie</label>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Action Buttons --}}
                                                <div id="actionButtons" style="display:flex;gap:10px;flex-wrap:wrap">
                                                    <button type="submit" name="action" value="approved"
                                                        class="btn-approve" id="btnApprove">
                                                        <i class="ri-check-line" aria-hidden="true"></i> Approve
                                                    </button>
                                                    <button type="submit" name="action" value="rejected"
                                                        class="btn-reject" id="btnReject">
                                                        <i class="ri-close-line" aria-hidden="true"></i> Reject
                                                    </button>
                                                    <button type="button" id="btnShowReupload"
                                                        class="btn-info btn-show-reupload">
                                                        <i class="ri-upload-cloud-2-line" aria-hidden="true"></i>
                                                        Request Reupload
                                                    </button>
                                                    <button type="submit" name="action" value="request_reupload"
                                                        id="btnSubmitReupload" class="btn-info" style="display:none;"
                                                        disabled>
                                                        <i class="ri-send-plane-line" aria-hidden="true"></i> Confirm
                                                        Reupload
                                                    </button>
                                                    <button type="button" id="btnCancelReupload" class="btn-reject"
                                                        style="display:none;">
                                                        <i class="ri-arrow-go-back-line" aria-hidden="true"></i> Cancel
                                                    </button>
                                                    <button type="button" class="btn-details btn-view-details">
                                                        <i class="ri-layout-right-line" aria-hidden="true"></i> View
                                                        Details
                                                    </button>
                                                </div>

                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            @include('administrator.partials.empty-state', [
                                'icon' => 'ri-shield-user-line',
                                'title' => 'No Applications Found',
                                'message' => 'There are no account verification requests to display at the moment.',
                            ])
                        @endforelse
                    </div>

                </div>
            </section>

            <!-- Details View Modal -->
            <div id="detailsModal" class="proof-modal" style="display:none;">
                <div class="proof-modal-content">
                    <button id="closeDetailsModal" class="proof-modal-close" aria-label="Close details">
                        &times;
                    </button>

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
                                    <button class="carousel-btn carousel-prev" id="carouselPrev"
                                        aria-label="Previous document">
                                        <i class="ri-arrow-left-s-line"></i>
                                    </button>
                                    <button class="carousel-btn carousel-next" id="carouselNext"
                                        aria-label="Next document">
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
                <button class="overlay-close" aria-label="Close full-size preview">&times;</button>
                <div class="overlay-image-container">
                    <img id="overlayImage" src="" alt="">
                    <div class="overlay-caption" id="overlayCaption"></div>
                </div>
            </div>

            <!-- Keep the original image modal for viewing individual images -->
            <div id="imageModal" class="modal-backdrop document-preview" style="display:none;">
                <div class="modal document-preview__dialog">
                    <div class="document-preview__header">
                        <h3>Document Preview</h3>
                        <button id="closeImageModal" class="document-preview__close" aria-label="Close preview">
                            &times;
                        </button>
                    </div>
                    <div class="document-preview__body">
                        <img id="modalImage" src="" alt="Selected verification document" />
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
                    accountList.innerHTML = `
                        <div class="admin-empty-state">
                            <div class="admin-empty-state__icon" aria-hidden="true">
                                <i class="ri-shield-user-line"></i>
                            </div>
                            <h3>No Applications Found</h3>
                            <p>There are no account verification requests to display at the moment.</p>
                        </div>
                    `;
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
                    accountList.innerHTML = `
                        <div class="admin-empty-state">
                            <div class="admin-empty-state__icon" aria-hidden="true">
                                <i class="ri-search-line"></i>
                            </div>
                            <h3>No Matching Applications</h3>
                            <p>No verification requests match the current filter or search.</p>
                        </div>
                    `;
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
                    function toggleDetails(e) {
                        // Don't toggle if clicking on buttons
                        if (e.target.closest('button')) return;

                        const details = row.nextElementSibling;
                        const icon = row.querySelector("i");
                        if (details) {
                            details.classList.toggle("hidden");
                            const isOpen = !details.classList.contains("hidden");
                            row.setAttribute("aria-expanded", isOpen ? "true" : "false");
                            if (icon) {
                                icon.classList.toggle("ri-arrow-up-s-line", isOpen);
                                icon.classList.toggle("ri-arrow-down-s-line", !isOpen);
                            }
                            isUserInteracting = true;
                            clearInteractionAfterDelay();
                        }
                    }

                    row.addEventListener("click", toggleDetails);
                    row.addEventListener("keydown", (e) => {
                        if (e.key === "Enter" || e.key === " ") {
                            e.preventDefault();
                            toggleDetails(e);
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
                            <div class="admin-empty-state admin-empty-state--compact">
                                <div class="admin-empty-state__icon" aria-hidden="true">
                                    <i class="ri-image-line"></i>
                                </div>
                                <h3>No Documents Available</h3>
                                <p>There are no uploaded documents to preview.</p>
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



    {{-- ===================== PROVIDER SWITCHER LOGIC ===================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const buttons = document.querySelectorAll('.provider-btn');
            const statusEl = document.getElementById('providerStatus');
            const quotaEl = document.getElementById('providerQuota');
            const noteEl = document.getElementById('providerNote');
            let currentProvider = null;

            function loadProvider() {
                fetch("{{ route('accounts.verification-provider') }}", {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    currentProvider = data.active;
                    updateUI(data);
                })
                .catch(() => {
                    statusEl.textContent = 'Could not load provider info';
                });
            }

            function updateUI(data) {
                // Highlight active button
                buttons.forEach(btn => {
                    const p = btn.dataset.provider;
                    const isActive = p === data.active;
                    const info = data.providers[p];
                    btn.style.borderColor = isActive ? '#2563eb' : '#e2e8f0';
                    btn.style.background = isActive ? '#eff6ff' : '#fff';
                    btn.style.opacity = (info && info.configured) ? '1' : '0.5';
                    if (info && !info.configured) {
                        btn.title = 'API key not configured';
                    } else {
                        btn.title = '';
                    }
                });

                // Status text
                const activeInfo = data.providers[data.active];
                const name = activeInfo ? activeInfo.name : data.active;
                statusEl.innerHTML = `Active: <strong>${name}</strong>` +
                    (data.enabled ? ' <span style="color:#16a34a">● Enabled</span>' : ' <span style="color:#dc2626">● Disabled</span>');

                // Quota + tracked request display for every provider.
                const providerEntries = Object.entries(data.providers || {});
                if (providerEntries.length) {
                    quotaEl.innerHTML = `
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:10px">
                            ${providerEntries.map(([key, info]) => {
                                const q = info.quota || {};
                                const stats = info.stats || {};
                                const isActive = key === data.active;
                                const dailyLimit = Number(q.daily_limit || 0);
                                const monthlyLimit = Number(q.monthly_limit || 0);
                                const dailyUsed = Number(q.daily_used || 0);
                                const monthlyUsed = Number(q.monthly_used || 0);
                                const dailyPct = dailyLimit > 0 ? Math.min(100, Math.round((dailyUsed / dailyLimit) * 100)) : 0;
                                const monthlyPct = monthlyLimit > 0 ? Math.min(100, Math.round((monthlyUsed / monthlyLimit) * 100)) : 0;
                                const barColor = monthlyPct >= 90 ? '#dc2626' : monthlyPct >= 70 ? '#f59e0b' : '#16a34a';

                                return `
                                    <div style="padding:10px;border:1px solid ${isActive ? '#2563eb' : '#e5e7eb'};border-radius:8px;background:${isActive ? '#eff6ff' : '#fff'}">
                                        <div style="display:flex;justify-content:space-between;gap:8px;align-items:center;margin-bottom:8px">
                                            <strong>${info.name || key}</strong>
                                            ${isActive ? '<span style="font-size:10px;color:#2563eb;font-weight:700">ACTIVE</span>' : ''}
                                        </div>
                                        <div>Today: <strong>${dailyUsed}</strong> / ${dailyLimit || 'No cap'}</div>
                                        <div style="margin-top:4px">This month: <strong>${monthlyUsed}</strong> / ${monthlyLimit || 'No cap'}</div>
                                        <div style="height:6px;background:#e5e7eb;border-radius:3px;margin-top:6px;overflow:hidden">
                                            <div style="width:${monthlyPct}%;height:100%;background:${barColor};border-radius:3px"></div>
                                        </div>
                                        <div style="margin-top:8px;color:#4b5563">
                                            Tracked requests: <strong>${stats.month || 0}</strong> this month, <strong>${stats.total || 0}</strong> total
                                        </div>
                                        <div style="margin-top:4px;color:#6b7280">
                                            Pending ${stats.pending || 0} / Approved ${stats.approved || 0} / Rejected ${stats.rejected || 0}
                                        </div>
                                    </div>`;
                            }).join('')}
                        </div>`;
                } else {
                    quotaEl.innerHTML = '';
                }

                // Note
                if (activeInfo && !activeInfo.primary) {
                    noteEl.textContent = 'This provider only extracts text — auto-approval is disabled. All submissions go to admin for manual review.';
                } else if (activeInfo && activeInfo.primary) {
                    noteEl.textContent = 'Didit supports face matching — submissions with a high score can be auto-approved without admin review.';
                } else {
                    noteEl.textContent = '';
                }
            }

            buttons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const provider = btn.dataset.provider;
                    if (provider === currentProvider) return;

                    if (!confirm(`Switch verification provider to ${btn.textContent.trim().split('\n')[0]}?`)) return;

                    fetch("{{ route('accounts.verification-provider.update') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ provider: provider, enabled: true })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message || 'Verification provider updated.');
                            loadProvider();
                        } else {
                            alert(data.message || 'Failed to switch provider');
                        }
                    })
                    .catch(() => alert('Network error'));
                });
            });

            loadProvider();
        });
    </script>

</body>

</html>
