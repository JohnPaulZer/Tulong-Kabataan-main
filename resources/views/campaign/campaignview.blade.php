<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign | Tulong Kabataan</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    <!-- Remixicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <!-- Google Fonts: Playfair Display & Open Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}?v=4">
    <link rel="stylesheet" href="{{ asset('css/campaign/campaignview.css') }}?v=4">
</head>

<body data-donation-route="{{ route('donations.store') }}" data-csrf-token="{{ csrf_token() }}"
    data-success-message="{{ session('success') }}" data-error-message="{{ session('error') }}">

    @include('partials.universalmodal')
    @include('partials.main-header')
    @include('administrator.partials.loading-screen')


    <main class="cpv-container">
        <button class="cpv-back-button" onclick="history.back()"><i class="ri-arrow-left-line"></i> Back</button>
        <h1 class="cpv-campaign-title">{{ $campaign->title }}</h1>

        <div class="cpv-campaign-grid">
            <!-- Campaign Image -->
            <div class="cpv-image-wrapper">
                <div class="cpv-campaign-image">
                    <img src="{{ $campaign->featured_image ? asset('storage/' . $campaign->featured_image) : asset('img/default-camp.jpg') }}"
                        alt="Campaign Cover" />
                </div>
            </div>

            <!-- Campaign Details -->
            <div class="cpv-details-wrapper">
                <div class="cpv-campaign-details">
                    <div class="cpv-campaign-stats">
                        <div>
                            <p class="cpv-raised-amount">₱ {{ number_format($campaign->current_amount, 2) }} PHP raised
                            </p>
                            <p class="cpv-goal-info">PHP {{ number_format($campaign->target_amount, 2) }} goal •
                                {{ $campaign->donor_count }} donations • {{ number_format($campaign->views) }} views
                            </p>
                        </div>
                        <div class="cpv-action-buttons">
                            <!-- <button class="cpv-btn-share" aria-label="Share">
                    <i class="ri-share-line" style="font-size: 18px;"></i>
                </button> -->
                            <button class="cpv-btn-donate show-donation-modal">Donate now</button>
                        </div>
                    </div>
                    <div class="cpv-progress-bar-wrapper">
                        @php
                            $progress =
                                $campaign->target_amount > 0
                                    ? ($campaign->current_amount / $campaign->target_amount) * 100
                                    : 0;
                        @endphp
                        <div class="cpv-progress-bar-bg">
                            <div class="cpv-progress-bar-fill" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                    <section class="cpv-about-section">
                        <h2>About This Campaign</h2>
                        <p>{{ $campaign->description }}</p>
                    </section>

                    <section class="cpv-organizer-box">
                        <h3 class="cpv-organizer-header">Campaign Organizer</h3>
                        <div class="cpv-organizer-info">
                            <div class="cpv-organizer-avatar-placeholder">
                                <i class="ri-user-line"></i>
                            </div>
                            <div>
                                <p class="cpv-name">{{ $campaign->campaign_organizer }}</p>
                                <p class="cpv-role">Organizer</p>
                            </div>
                        </div>
                    </section>

                    <!-- Donations -->
                    <div class="cpv-donations-wrapper">
                        <section class="cpv-recent-donations">
                            <div class="cpv-recent-donations-header">
                                <div><i class="ri-line-chart-line"></i> {{ number_format($campaign->donor_count) }}
                                    people just donated</div>
                                <div><i class="ri-eye-line"></i> {{ number_format($campaign->views) }}</div>
                            </div>

                            @forelse($campaign->donations->take(5) as $donation)
                                <div class="cpv-donation-item">
                                    <div class="cpv-donation-profile">

                                        <!-- Icon avatar -->
                                        <div class="donor-avatar">
                                            <i class="ri-hand-heart-line"></i>
                                        </div>

                                        <div>
                                            <p class="cpv-donor-name">
                                                @if ($donation->is_anonymous)
                                                    Anonymous
                                                @else
                                                    {{ $donation->donor_name ?? (optional($donation->user)->first_name ?? 'Anonymous') }}
                                                @endif
                                            </p>
                                            <p class="donation-modal-date" style="position:relative; top: -15px;">
                                                {{ $donation->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="cpv-donation-amount"> ₱{{ number_format($donation->amount, 2) }}</div>
                                </div>
                            @empty
                                <p style="margin-top:8px; color:#999;">No donations yet.</p>
                            @endforelse


                            <div class="cpv-donation-actions">
                                <button class="cpv-show-more-comments">See all donations</button>
                            </div>
                        </section>
                    </div>
                </div>
            </div>

            <!-- Updates Section -->
            <div class="cpv-updates-wrapper">
                <section class="cpv-updates-section">
                    <div class="cpv-updates-header">
                        <h2>Updates ({{ $campaign->updates->count() }})</h2>
                    </div>

                    @forelse($campaign->updates->take(1) as $update)
                        <div class="cpv-update-item">
                            <div class="cpv-update-header">
                                <div class="cpv-update-author">
                                    @php
                                        $organizerAvatar = $update->organizer
                                            ? $update->organizer->profile_photo_url
                                            : null;
                                        if (
                                            $organizerAvatar &&
                                            str_contains($organizerAvatar, 'googleusercontent.com') &&
                                            !str_contains($organizerAvatar, 'sz=')
                                        ) {
                                            $organizerAvatar .=
                                                (str_contains($organizerAvatar, '?') ? '&' : '?') . 'sz=64';
                                        }
                                        if ($organizerAvatar && !Str::startsWith($organizerAvatar, 'http')) {
                                            $organizerAvatar = asset('storage/' . $organizerAvatar);
                                        }
                                    @endphp
                                    @if ($organizerAvatar)
                                        <img src="{{ $organizerAvatar }}"
                                            alt="{{ $update->organizer->first_name ?? 'Organizer' }}"
                                            referrerpolicy="no-referrer" />
                                    @else
                                        <img src="{{ asset('img/log2.png') }}"
                                            alt="{{ $update->organizer->first_name ?? 'Organizer' }}" />
                                    @endif
                                    <div>
                                        <p class="cpv-update-author-info">
                                            {{ $update->organizer->first_name ?? 'Organizer' }}
                                            <span class="cpv-update-organizer-badge">Organizer</span>
                                        </p>
                                        <p class="cpv-update-date">
                                            @if ($update->created_at->diffInMinutes(now()) <= 5)
                                                <span style="color: #ff4444; font-weight: 600;">New</span>
                                            @else
                                                {{ $update->created_at->diffForHumans() }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="cpv-update-content">
                                <p class="cpv-update-text">{{ $update->message }}</p>

                                @if ($update->images && is_array($update->images) && count($update->images) > 0)
                                    <div class="cpv-update-images {{ count($update->images) === 1 ? 'single-image' : '' }}"
                                        data-update-id="{{ $update->update_id }}">
                                        <div class="cpv-update-images-carousel">
                                            @foreach ($update->images as $index => $image)
                                                <div class="cpv-update-image">
                                                    <img src="{{ asset('storage/' . $image) }}" alt="Update image"
                                                        onclick="showImageModal('{{ asset('storage/' . $image) }}')" />
                                                </div>
                                            @endforeach
                                        </div>

                                        @if (count($update->images) > 1)
                                            <div class="cpv-carousel-nav">
                                                <button class="cpv-carousel-btn prev-btn"
                                                    onclick="moveCarousel('{{ $update->update_id }}', -1)">
                                                    ‹
                                                </button>
                                                <button class="cpv-carousel-btn next-btn"
                                                    onclick="moveCarousel('{{ $update->update_id }}', 1)">
                                                    ›
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="cpv-no-updates">
                            <p>No updates yet for this campaign.</p>
                        </div>
                    @endforelse

                    @if ($campaign->updates->count() > 1)
                        <button class="cpv-see-more-updates" onclick="showAllUpdatesModal()">See more updates</button>
                    @endif
                </section>
            </div>

            <!-- All Updates Modal -->
            <div id="allUpdatesModal" class="modal-overlay" style="display: none;">
                <div class="modal-content all-updates-modal">
                    <button class="modal-close" onclick="closeAllUpdatesModal()">×</button>
                    <h2>All Updates ({{ $campaign->updates->count() }})</h2>

                    <div class="all-updates-container">
                        @foreach ($campaign->updates as $update)
                            <div class="cpv-update-item">
                                <div class="cpv-update-header">
                                    <div class="cpv-update-author">
                                        @php
                                            $organizerAvatar = $update->organizer
                                                ? $update->organizer->profile_photo_url
                                                : null;
                                            if (
                                                $organizerAvatar &&
                                                str_contains($organizerAvatar, 'googleusercontent.com') &&
                                                !str_contains($organizerAvatar, 'sz=')
                                            ) {
                                                $organizerAvatar .=
                                                    (str_contains($organizerAvatar, '?') ? '&' : '?') . 'sz=64';
                                            }
                                            if ($organizerAvatar && !Str::startsWith($organizerAvatar, 'http')) {
                                                $organizerAvatar = asset('storage/' . $organizerAvatar);
                                            }
                                        @endphp
                                        @if ($organizerAvatar)
                                            <img src="{{ $organizerAvatar }}"
                                                alt="{{ $update->organizer->first_name ?? 'Organizer' }}"
                                                referrerpolicy="no-referrer" />
                                        @else
                                            <img src="{{ asset('img/log2.png') }}"
                                                alt="{{ $update->organizer->first_name ?? 'Organizer' }}" />
                                        @endif
                                        <div>
                                            <p class="cpv-update-author-info">
                                                {{ $update->organizer->first_name ?? 'Organizer' }}
                                                <span class="cpv-update-organizer-badge">Organizer</span>
                                            </p>
                                            <p class="cpv-update-date">{{ $update->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="cpv-update-content">
                                    <p class="cpv-update-text">{{ $update->message }}</p>

                                    @if ($update->images && is_array($update->images) && count($update->images) > 0)
                                        <div class="cpv-update-images {{ count($update->images) === 1 ? 'single-image' : '' }}"
                                            data-update-id="{{ $update->update_id }}">
                                            <div class="cpv-update-images-carousel">
                                                @foreach ($update->images as $image)
                                                    <div class="cpv-update-image">
                                                        <img src="{{ asset('storage/' . $image) }}"
                                                            alt="Update image"
                                                            onclick="showImageModal('{{ asset('storage/' . $image) }}')" />
                                                    </div>
                                                @endforeach
                                            </div>

                                            @if (count($update->images) > 1)
                                                <div class="cpv-carousel-nav">
                                                    <button class="cpv-carousel-btn prev-btn"
                                                        onclick="moveCarousel('{{ $update->update_id }}', -1)">
                                                        ‹
                                                    </button>
                                                    <button class="cpv-carousel-btn next-btn"
                                                        onclick="moveCarousel('{{ $update->update_id }}', 1)">
                                                        ›
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Image Modal -->
            <div id="imageModal" class="modal-overlay" style="display: none;">
                <div class="modal-content image-modal">
                    <button class="modal-close" onclick="closeImageModal()">×</button>
                    <div class="image-modal-container">
                        <img id="modalImage" src="" alt="Enlarged update image" />
                    </div>
                </div>
            </div>

        </div>
    </main>

    @include('partials.main-footer')

    <!-- Donation Modal -->
    <div id="donationModal" class="modal-overlay" style="display:none;">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2>Donate to {{ $campaign->title }}</h2>

            <div class="qr-wrapper">
                <img src="{{ $campaign->qr_code ? asset('storage/' . $campaign->qr_code) : asset('img/default-qr.png') }}"
                    alt="GCash QR Code" class="qr-image">
            </div>

            <!-- GCash Number with Copy Icon -->
            <div class="gcash-number-container">
                <span class="gcash-number">{{ $campaign->gcash_number }}</span>
                <button class="copy-icon" id="copyGcashBtn" title="Copy to clipboard">
                    <i class="ri-file-copy-line"></i>
                </button>
            </div>

            <p class="donation-note">
                📌 Send your donation via QR code or GCash number
            </p>

            <button id="alreadyPaidBtn" class="donation-next-btn">I Already Paid</button>
        </div>
    </div>

    <!-- Proof Upload Modal (2nd step) -->
    <div id="proofModal" class="modal-overlay" style="display:none;">

        <div class="modal-content">
            <button type="button" class="back-to-qr-btn">
                <i class="ri-arrow-left-line"></i>
            </button>
            <span class="modal-close">&times;</span>
            <h2>Upload Donation Proof</h2>



            <form method="POST" action="{{ route('donations.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="campaign_id" value="{{ $campaign->campaign_id }}">

                <label>Amount Donated (PHP)</label>
                <input type="number" name="amount" step="0.01" min="0" required
                    onkeydown="return preventInvalidInput(event)" oninput="sanitizeAmount(this)">

                <label>Reference Number</label>
                <div class="reference-input-container">
                    <input type="text" name="reference_number" required id="referenceInput"
                        oninput="checkReferenceNumber(this.value)">
                    <div id="referenceStatus" class="reference-status"></div>
                    <small id="referenceError" class="reference-error">
                        Reference number has already been used
                    </small>
                </div>


                <label>Upload Screenshot</label>
                <input type="file" name="proof_image" accept="image/*" required>

                @guest
                    <label>Your Name</label>
                    <input type="text" name="donor_name" required>
                    <label>Your Email (optional)</label>
                    <input type="email" name="donor_email">
                @endguest

                <!-- ✅ Anonymous checkbox for everyone -->
                <label style="display:flex; align-items:center; gap:6px; margin-top:8px;">
                    <input type="checkbox" name="is_anonymous" value="1" id="anonymousCheckbox">
                    Donate anonymously
                </label>

                <button type="submit" class="donation-submit-btn" id="submitBtn">Submit Proof</button>
            </form>
        </div>
    </div>

    <!-- All Donations Modal -->
    <div id="allDonationsModal" class="modal-overlay" style="display:none;">
        <div class="modal-content all-donations-modal">
            <span class="modal-close">&times;</span>
            <h2>All Donations</h2>

            <div class="donations-list-container">
                @forelse($campaign->donations as $donation)
                    <div class="donation-modal-item">
                        <div class="donation-modal-profile">
                            <div class="donor-avatar">
                                <i class="ri-hand-heart-line"></i>
                            </div>
                            <div class="donation-modal-info">
                                <p class="donation-modal-name">
                                    @if ($donation->is_anonymous)
                                        Anonymous
                                    @else
                                        {{ $donation->donor_name ?? (optional($donation->user)->first_name ?? 'Anonymous') }}
                                    @endif
                                </p>
                                <p class="donation-modal-date">
                                    {{ $donation->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <div class="donation-modal-amount">₱{{ number_format($donation->amount, 2) }}</div>
                    </div>
                @empty
                    <div class="no-donations-message">
                        <i class="ri-inbox-line"></i>
                        <p>No donations yet</p>
                    </div>
                @endforelse
            </div>

            <div class="donations-summary">
                <p>Total Donations: <strong>₱{{ number_format($campaign->current_amount, 2) }}</strong></p>
                <p>Total Donors: <strong>{{ $campaign->donor_count }}</strong></p>
            </div>
        </div>
    </div>

</body>

<script src="{{ asset('js/campaignview/seemodal-script.js') }}"></script>
<script src="{{ asset('js/campaignview/donation-validation.js') }}"></script>
<script src="{{ asset('js/campaignview/donatemodal-script.js') }}"></script>
<script src="{{ asset('js/campaignview/image-modal.js') }}"></script>
<script src="{{ asset('js/campaignview/updates-modal.js') }}"></script>
<script src="{{ asset('js/campaignview/updates-carousel.js') }}"></script>

</html>
