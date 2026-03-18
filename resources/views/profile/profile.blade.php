<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tulong Kabataan</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    <!-- Remixicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <!-- CropperJS CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <!-- CropperJS JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}?v=4">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}?v=4">
</head>

<body>
    <!-- Navigation & Header -->
    @include('partials.main-header')
    @include('administrator.partials.loading-screen')

    <div class="prof-flex">
        <!-- Sidebar -->
        @include('profile.partials.main-sidebar')

        <!-- Main -->
        <main class="prof-main">

            @if (auth()->check())
                @php
                    $status = strtolower(optional(auth()->user()->identityStatus)->status ?? '');
                @endphp

                <div id="verification-banner-container" data-status="{{ $status }}">
                    @if ($status === 'verified')
                        {{-- Nothing to show when already verified --}}
                    @elseif($status === 'pending')
                        <div class="verify-banner pending">
                            <strong>Verification in Progress:</strong>
                            <span>We've received your documents and they are currently under review. Please wait for
                                confirmation — this may take up to 24-48 hours.</span>
                        </div>
                    @elseif($status === 'reupload')
                        <div class="verify-banner reupload">
                            <strong>Action Needed:</strong>
                            <span>Verification is still pending. Admin has requested you to re-upload your
                                documents.</span>
                            <a href="{{ route('verify.page') }}" class="verify-btn">Re-upload Documents</a>
                        </div>
                    @else
                        <div class="verify-banner">
                            <div class="verify-text"><strong>Action Required:</strong>Verify your identity to
                                participate in campaign creation and access other features.</div>
                            <a href="{{ route('verify.page') }}" class="verify-btn">Verify Now</a>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Profile -->
            <div class="prof-card">
                <div class="prof-header">

                    <div class="prof-avatar" id="avatarWrapper">
                        @php
                            $avatar = optional(auth()->user())->profile_photo_url;

                            if (
                                $avatar &&
                                str_contains($avatar, 'googleusercontent.com') &&
                                !str_contains($avatar, 'sz=')
                            ) {
                                $avatar .= (str_contains($avatar, '?') ? '&' : '?') . 'sz=128';
                            }

                            if ($avatar && !Str::startsWith($avatar, 'http')) {
                                $avatar = asset('storage/' . $avatar);
                            }
                        @endphp

                        @if ($avatar)
                            <img class="user-avatar" id="userAvatar" src="{{ $avatar }}" alt="Profile photo">
                        @else
                            <i class="ri-user-line prof-initial"></i>
                        @endif

                        <div class="edit-icon">
                            <i class="ri-pencil-fill"></i>
                        </div>
                    </div>

                    <div>
                        <h1 style="display:flex; align-items:center; gap:4px; font-size:1.3em;">
                            {{ auth()->user()->first_name }}
                            <span id="verification-badge">
                                @if (optional($user->identityStatus)->status === 'Verified')
                                    <span class="verified-badge">
                                        <i class="ri-leaf-fill"></i>Verified
                                    </span>
                                @else
                                    <span class="not-verified-badge">
                                        <i class="ri-leaf-fill"></i>Not Verified
                                    </span>
                                @endif
                            </span>
                        </h1>

                        <p class="prof-email">{{ auth()->user()->email }}</p>

                        <div class="prof-actions" style="display:flex; gap:8px; margin-top:8px;">
                            <button class="prof-btn prof-btn-primary" id="editProfileBtn">Edit Profile</button>
                            <button class="prof-btn prof-btn-outline" id="changePasswordBtn">Change Password</button>
                        </div>

                        <form id="changePhotoForm" method="POST" enctype="multipart/form-data"
                            action="{{ route('profile.change-photo') }}" style="display:none;">
                            @csrf
                            <input type="file" id="photoInput" name="photo" accept="image/*">
                            <input type="hidden" id="croppedImage" name="cropped_image">
                        </form>
                    </div>
                </div>
            </div>

            <!-- Crop Modal -->
            <div id="cropModal" class="modal" style="display:none;">
                <div class="modal-content" style="text-align:center;">
                    <h2 class="modal-title">Adjust Your Profile Photo</h2>
                    <div style="max-width:300px; margin:auto;">
                        <img id="cropperPreview" style="max-width:100%; border-radius:50%;">
                    </div>
                    <div style="margin-top:12px;">
                        <button id="rotateLeft" class="prof-btn prof-btn-outline">↻ Rotate</button>
                        <button id="zoomIn" class="prof-btn prof-btn-outline">＋ Zoom In</button>
                        <button id="zoomOut" class="prof-btn prof-btn-outline">－ Zoom Out</button>
                    </div>
                    <div style="margin-top:18px;">
                        <button id="saveCropped" class="prof-btn prof-btn-primary">Save Changes</button>
                        <button id="cancelCrop" class="prof-btn prof-btn-outline cancel-btn">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Modal -->
            <div id="editProfileModal" class="modal" style="display:none;">
                <div class="modal-content" style="text-align:left;">
                    <h2 class="modal-title">Edit Your Profile</h2>
                    <!-- Message placeholder -->
                    <div id="editProfileMessage"
                        style="display:none; font-size:0.95em; font-weight:500; margin-bottom:12px;"></div>
                    <form id="editProfileForm" method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name"
                            value="{{ auth()->user()->first_name }}" required class="modal-input">

                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name"
                            value="{{ auth()->user()->last_name }}" required class="modal-input">

                        @php
                            $birthdayValue = auth()->user()->birthday
                                ? \Carbon\Carbon::parse(auth()->user()->birthday)->format('Y-m-d')
                                : '';
                            $birthdayReadonly = !empty($birthdayValue);
                        @endphp
                        <label for="birthday">Birthday @if (!$birthdayReadonly)
                                <span style="color:red">*</span>
                            @endif
                        </label>
                        <input type="date" id="birthday" name="birthday" value="{{ $birthdayValue }}"
                            class="modal-input"
                            @if ($birthdayReadonly) readonly style="background:#f3f4f6; cursor:not-allowed;"
                        @else
                            required @endif>

                        @php
                            $phoneNumberValue = auth()->user()->phone_number ?? '';
                            $phoneNumberReadonly = !empty($phoneNumberValue);
                        @endphp
                        <label for="phone_number">Phone Number @if (!$phoneNumberReadonly)
                                <span style="color:red">*</span>
                            @endif
                        </label>
                        <input type="tel" id="phone_number" name="phone_number" value="{{ $phoneNumberValue }}"
                            class="modal-input"
                            @if ($phoneNumberReadonly) readonly style="background:#f3f4f6; cursor:not-allowed;"
                            @else
                                required @endif>

                        <label for="email">Email Address</label>
                        <input type="email" id="email" value="{{ auth()->user()->email }}" readonly
                            class="modal-input" style="background:#f3f4f6; cursor:not-allowed;">

                        <div class="modal-actions">
                            <button type="submit" id="updateProfileBtn" class="prof-btn prof-btn-primary">
                                <span class="btn-text">Save Changes</span>
                                <i class="ri-loader-4-line spinner"
                                    style="display:none; margin-left:6px; font-size:1.2em; vertical-align:middle; animation:spin 1s linear infinite;"></i>
                            </button>
                            <button type="button" id="cancelEditProfile"
                                class="prof-btn prof-btn-outline">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Modal -->
            <div id="changePasswordModal" class="modal" style="display:none;">
                <div class="modal-content" style="text-align:left;">
                    @php
                        $hasPassword = !empty(auth()->user()->password);
                    @endphp
                    <h2 class="modal-title" id="passwordModalTitle">
                        {{ $hasPassword ? 'Change Password' : 'Set Up Password' }}</h2>

                    <!-- Global error message placeholder -->
                    <div id="changePasswordError"
                        style="color:#e74c3c; font-size:0.95em; font-weight:500; margin-bottom:12px; display:none;">
                    </div>

                    <form id="changePasswordForm" method="POST" action="{{ route('profile.change-password') }}">
                        @csrf
                        @method('PUT')

                        <!-- Show current password field only if user has existing password -->
                        <div id="currentPasswordField" @if (!$hasPassword) style="display:none;" @endif>
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password"
                                @if ($hasPassword) required @endif class="modal-input"
                                placeholder="Enter your current password">
                        </div>

                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required class="modal-input"
                            placeholder="Enter your new password">

                        <label for="new_password_confirmation">Confirm New Password</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                            required class="modal-input" placeholder="Confirm your new password">

                        <div class="modal-actions">
                            <button type="submit" id="updatePasswordBtn" class="prof-btn prof-btn-primary">
                                <span class="btn-text"
                                    id="passwordBtnText">{{ $hasPassword ? 'Update Password' : 'Set Up Password' }}</span>
                                <i class="ri-loader-4-line spinner"
                                    style="display:none; margin-left:6px; font-size:1.2em; vertical-align:middle; animation:spin 1s linear infinite;"></i>
                            </button>

                            <button type="button" id="cancelChangePassword"
                                class="prof-btn prof-btn-outline">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Profile Details Grid -->
            <div class="prof-grid" style="display:grid; gap:1.5rem;">
                <div class="prof-card soft-orange">
                    <h2><i class="ri-heart-fill"></i> Fundraising Overview</h2>

                    @php
                        $userCampaigns = $user->campaigns;
                        $activeCampaigns = $userCampaigns->where('status', 'active')->count();
                        $totalRaised = $userCampaigns->sum('current_amount');
                    @endphp

                    <p><strong>Active Campaigns:</strong> {{ $activeCampaigns }}</p>
                    <p><strong>Total Raised:</strong> ₱{{ number_format($totalRaised, 0) }}</p>
                    <p class="prof-note">Together, we're growing kindness — one donation at a time.</p>
                </div>

                <div class="prof-card soft-green">
                    <h2><i class="ri-calendar-event-fill"></i> Events Joined</h2>

                    @php

                        $eventRegistrations = $user->eventRegistrations;

                        if ($eventRegistrations) {
                            $eventRegistrations->load(['event', 'volunteerRoles']);
                        }
                    @endphp

                    @if ($eventRegistrations && $eventRegistrations->count() > 0)
                        <ul class="prof-activity">
                            @foreach ($eventRegistrations->take(3) as $registration)
                                {{-- Show only first 3 --}}
                                <li>
                                    <i class="ri-hand-heart-fill"></i>
                                    {{ $registration->event->title ?? 'Event' }} —
                                    {{ $registration->registered_role ?? ($registration->volunteerRoles->name ?? 'Participant') }}
                                </li>
                            @endforeach
                        </ul>

                        @if ($eventRegistrations->count() > 3)
                            <p><small>+ {{ $eventRegistrations->count() - 3 }} more events</small></p>
                        @endif
                    @else
                        <p>No events joined yet.</p>
                    @endif

                    <p class="prof-note">Joined hands to make every event meaningful and memorable.</p>
                </div>

                <div class="prof-card soft-blue">
                    <h2><i class="ri-hand-coin-fill"></i> Donation Summary</h2>

                    @php
                        $userDonations = $user->donations;

                        $scheduledCount = $userDonations->where('status', 'Scheduled')->count();
                        $distributedCount = $userDonations->where('status', 'Distributed')->count();
                        $totalCount = $userDonations->count();
                    @endphp

                    <p><strong>Scheduled In-Kind Donations:</strong> {{ $scheduledCount }}</p>
                    <p><strong>Distributed In-Kind Donations:</strong> {{ $distributedCount }}</p>
                    <p><strong>Total Donations Made:</strong> {{ $totalCount }}</p>
                    <p class="prof-note">Every gift, big or small, creates ripples of hope.</p>
                </div>

                <div class="prof-card soft-graygreen">
                    <h2><i class="ri-shield-check-fill"></i> Account Verification</h2>

                    @php

                        $user = auth()->user();

                        $identityStatus = $user->identityStatus;
                        $isVerified = $identityStatus && $identityStatus->status === 'Verified';
                        $verifiedDate = $identityStatus ? $identityStatus->created_at : null;
                        $memberSince = $user->created_at;

                        // Format dates
                        $verifiedYear = $verifiedDate ? $verifiedDate->format('Y') : null;
                        $memberDate = $memberSince ? $memberSince->format('F Y') : null;

                        $isFundraiser = $user->verificationRequests()->exists();
                    @endphp

                    {{-- Verification Status --}}
                    @if ($isVerified)
                        @if ($isFundraiser)
                            <p><i class="ri-user-check-fill"></i> Verified fundraiser since
                                <strong>{{ $verifiedYear ?? $verifiedDate->format('Y') }}</strong>
                            </p>
                        @else
                            <p><i class="ri-user-check-fill"></i> Verified since
                                <strong>{{ $verifiedYear ?? $verifiedDate->format('Y') }}</strong>
                            </p>
                        @endif
                    @else
                        <p><i class="ri-user-fill"></i>
                            @if ($user->verificationRequests()->where('status', 'pending')->exists())
                                Verification request pending
                            @else
                                Account not yet verified
                            @endif
                        </p>
                    @endif

                    {{-- Member Since --}}
                    <p><i class="ri-calendar-2-fill"></i> Member since
                        <strong>{{ $memberDate ?? $user->created_at->format('F Y') }}</strong>
                    </p>

                    {{-- Status Note --}}
                    @if ($isVerified)
                        <p class="prof-note">Trusted, transparent, and committed to making an impact.</p>
                    @elseif($user->verificationRequests()->where('status', 'pending')->exists())
                        <p class="prof-note">Verification under review. We'll notify you once completed.</p>
                    @else
                        <p class="prof-note">Complete verification to build trust with donors.</p>
                    @endif
                </div>
            </div>

        </main>
    </div>



    {{-- SIDE BAR --}}
    <script src="{{ asset('js/profile/sidebar.js') }}"></script>

    {{-- CHANGE PASSWORD --}}
    <script src="{{ asset('js/profile/changepassword.js') }}"></script>

    {{-- AJAX CHANGE PASSWORD --}}
    <script src="{{ asset('js/profile/ajaxpassword.js') }}"></script>

    {{-- UPDATE PROFILE PHOTO --}}
    <script src="{{ asset('js/profile/updateprofilephoto.js') }}"></script>

    {{-- UPDATE PROFILE MODAL --}}
    <script src="{{ asset('js/profile/updateprofilemodal.js') }}"></script>

    {{-- AJAX UPDATE PROFILE --}}
    <script src="{{ asset('js/profile/ajaxprofile.js') }}"></script>


    {{-- Verification Status Checker AJAX --}}
    <script>
        // Verification Status Checker
        document.addEventListener('DOMContentLoaded', function() {
            const bannerContainer = document.getElementById('verification-banner-container');
            const badgeContainer = document.getElementById('verification-badge');

            if (!bannerContainer && !badgeContainer) return;

            // Get initial status from banner or badge
            let currentStatus = bannerContainer ? bannerContainer.dataset.status :
                (badgeContainer.querySelector('.verified-badge') ? 'verified' : '');

            function checkStatus() {
                fetch('/check-verification-status')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status !== currentStatus) {
                            updateUI(data.status);
                            currentStatus = data.status;
                        }
                    })
                    .catch(error => {
                        console.log('Status check failed');
                    });
            }

            function updateUI(status) {
                // Update banner if exists
                if (bannerContainer) {
                    updateBanner(status);
                }

                // Update badge if exists
                if (badgeContainer) {
                    updateBadge(status);
                }
            }

            function updateBanner(status) {
                if (status === 'verified') {
                    bannerContainer.innerHTML = '';
                    bannerContainer.style.display = 'none';
                } else if (status === 'pending') {
                    bannerContainer.innerHTML = `
                <div class="verify-banner pending">
                    <strong>Verification in Progress:</strong>
                    <span>We've received your documents and they are currently under review. Please wait for confirmation — this may take up to 24-48 hours.</span>
                </div>
            `;
                } else if (status === 'reupload') {
                    bannerContainer.innerHTML = `
                <div class="verify-banner reupload">
                    <strong>Action Needed:</strong>
                    <span>Verification is still pending. Admin has requested you to re-upload your documents.</span>
                    <a href="{{ route('verify.page') }}" class="verify-btn">Re-upload Documents</a>
                </div>
            `;
                } else {
                    bannerContainer.innerHTML = `
                <div class="verify-banner">
                    <div class="verify-text"><strong>Action Required:</strong>Verify your identity to participate in campaign creation and access other features.</div>
                    <a href="{{ route('verify.page') }}" class="verify-btn">Verify Now</a>
                </div>
            `;
                }

                if (status !== 'verified') {
                    bannerContainer.style.display = 'block';
                }
                bannerContainer.dataset.status = status;
            }

            function updateBadge(status) {
                if (status === 'verified') {
                    badgeContainer.innerHTML = `
                <span class="verified-badge">
                    <i class="ri-leaf-fill"></i>Verified
                </span>
            `;
                } else {
                    badgeContainer.innerHTML = `
                <span class="not-verified-badge">
                    <i class="ri-leaf-fill"></i>Not Verified
                </span>
            `;
                }
            }

            // Check every 30 seconds
            setInterval(checkStatus, 30000);

            // Also check when user returns to the tab
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    checkStatus();
                }
            });
        });
    </script>


</body>

</html>
