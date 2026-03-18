@foreach ($campaigns as $campaign)
    <div class="campaign-item" data-status="{{ $campaign->status }}">
        {{-- Campaign Header --}}
        <div class="campaign-header">
            <div>
                <h3 class="campaign-title">
                    {{ $campaign->title }}
                </h3>
                <small class="campaign-organizer">
                    Organized by: <span class="organizer-name">{{ $campaign->campaign_organizer ?? 'Unknown' }}</span>
                </small>

                {{-- One-Time Campaign --}}
                @if ($campaign->schedule_type === 'one_time')
                    <div class="campaign-schedule">
                        📅 <strong>One-Time Campaign</strong><br>
                        Start: <span class="time-value">
                            {{ $campaign->starts_at ? \Carbon\Carbon::parse($campaign->starts_at)->format('M d, Y') : 'N/A' }}
                        </span><br>
                        ⏳ End: <span class="time-value">
                            {{ $campaign->ends_at ? \Carbon\Carbon::parse($campaign->ends_at)->format('M d, Y') : 'N/A' }}
                        </span>
                    </div>
                @endif

                {{-- Recurring Campaign --}}
                @if ($campaign->schedule_type === 'recurring')
                    <div class="campaign-schedule">
                        🔄 <strong style="color:#059669;">Recurring Campaign</strong><br>
                        @php
                            $days = is_array($campaign->recurring_days)
                                ? $campaign->recurring_days
                                : (is_string($campaign->recurring_days)
                                    ? json_decode($campaign->recurring_days, true)
                                    : []);
                        @endphp

                        @if (!empty($days))
                            <div class="campaign-days">
                                @foreach ($days as $day)
                                    <span class="day-tag">
                                        {{ ucfirst($day) }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if ($campaign->recurring_time)
                            <div class="campaign-time">
                                ⏰ Time: <span class="time-value">
                                    {{ \Carbon\Carbon::parse($campaign->recurring_time)->format('h:i A') }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            @php
                $statusStyles = [
                    'active' => ['bg' => '#dcfce7', 'color' => '#166534', 'class' => 'green'],
                    'scheduled' => ['bg' => '#dbeafe', 'color' => '#1e40af', 'class' => 'blue'],
                    'ended' => ['bg' => '#fef9c3', 'color' => '#92400e', 'class' => 'yellow'],
                ];
                $style = $statusStyles[$campaign->status] ?? [
                    'bg' => '#fee2e2',
                    'color' => '#b91c1c',
                    'class' => 'red',
                ];
            @endphp

            <span class="usedash-status {{ $style['class'] }}">
                {{ ucfirst($campaign->status) }}
            </span>
        </div>

        {{-- Progress --}}
        @php
            $progress =
                $campaign->target_amount > 0
                    ? min(($campaign->current_amount / $campaign->target_amount) * 100, 100)
                    : 0;
            $progressColor =
                $campaign->status === 'active'
                    ? '#3b82f6'
                    : ($campaign->status === 'scheduled'
                        ? '#f59e0b'
                        : '#9ca3af');
        @endphp

        <div class="progress-label">
            <span class="progress-text">Progress</span>
            <span class="progress-percent">{{ number_format($progress, 0) }}%</span>
        </div>

        <div class="progress-bar">
            <div class="progress-fill" style="width:{{ $progress }}%; background:{{ $progressColor }};"></div>
        </div>

        <p class="progress-amount">
            ₱{{ number_format($campaign->current_amount, 2) }}
            <span class="progress-target">/ ₱{{ number_format($campaign->target_amount, 2) }}</span>
        </p>

        {{-- Actions --}}
        <div class="action-buttons">

            {{-- Update Progress Button --}}
            @if ($campaign->status !== 'ended')
                <button type="button" onclick="openUpdateProgressModal({{ $campaign->campaign_id }})"
                    class="btn btn-update">
                    Update Progress
                </button>
            @endif

            <button type="button" onclick="showCampaignDetails({{ $campaign->campaign_id }})" class="btn btn-view">
                View Details
            </button>

            {{-- PDF Export Button --}}
            <button type="button" onclick="exportDonationsPDF({{ $campaign->campaign_id }})" class="btn btn-pdf">
                Export PDF
            </button>

            @if ($campaign->status !== 'ended')
                <button type="button" class="endCampaignBtn btn btn-end" data-id="{{ $campaign->campaign_id }}">
                    End Now
                </button>
            @endif

            <form action="{{ route('campaigns.destroy', $campaign->campaign_id) }}" method="POST"
                class="deleteCampaignForm" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete">
                    Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Details Section (Hidden, will be shown as modal) --}}
    <div id="details-{{ $campaign->campaign_id }}" class="campaign-details usedash-card">
        {{-- Recent Donations --}}
        <div class="usedash-card-header">
            <h4 class="card-title">
                <i class="ri-hand-coin-line" style="color:#3b82f6;"></i> Recent Donations
            </h4>
        </div>

        @php
            $donationList = $donations[$campaign->campaign_id] ?? [];
        @endphp

        @if (count($donationList) > 0)
            <div class="donation-table-wrapper">
                <table class="donation-table">
                    <thead>
                        <tr>
                            <th>Donor</th>
                            <th>Amount</th>
                            <th>Reference #</th>
                            <th>Proof</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($donationList as $donation)
                            <tr>
                                <td>
                                    @if ($donation->is_anonymous)
                                        Anonymous
                                    @elseif($donation->user)
                                        {{ $donation->user->first_name }}
                                    @elseif(!empty($donation->donor_name))
                                        {{ $donation->donor_name }}
                                    @else
                                        Guest
                                    @endif
                                </td>

                                <td>₱{{ number_format($donation->amount, 2) }}</td>
                                <td>{{ $donation->reference_number ?? 'N/A' }}</td>
                                <td>
                                    @if ($donation->proof_image)
                                        <img src="{{ asset('storage/' . $donation->proof_image) }}"
                                            alt="Donation Proof" class="proof-image"
                                            onclick="viewProofImageWithDetails(
                                                '{{ asset('storage/' . $donation->proof_image) }}',
                                                '₱{{ number_format($donation->amount, 2) }}',
                                                '{{ $donation->reference_number ?? 'N/A' }}',
                                                '{{ $donation->is_anonymous ? 'Anonymous' : ($donation->user ? $donation->user->first_name : $donation->donor_name ?? 'Guest') }}'
                                            )">
                                    @else
                                        <span class="no-proof">No proof</span>
                                    @endif
                                </td>

                                <td>
                                    <form action="{{ route('donations.reportFake', $donation->donation_id) }}"
                                        method="POST" class="reportFakeForm">
                                        @csrf
                                        <button type="submit" class="report-fake-btn">
                                            Report Fake
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="color:#6b7280; font-size:13px; margin-top:6px;">No donations yet.</p>
        @endif

        {{-- Request Manual Donation (Modal Trigger) --}}
        <div>
            <button type="button" onclick="openManualDonationModal({{ $campaign->campaign_id }})"
                class="manual-donation-btn">
                Request Manual Donation
            </button>
        </div>
    </div>
@endforeach

<!-- Campaign Details Modal Container -->
<div id="campaignDetailsModal" class="campaign-details-modal">
    <div class="campaign-details-content">
        <button type="button" onclick="closeCampaignDetailsModal()" class="campaign-details-close">
            ✖
        </button>
        <h3 class="campaign-details-title">Campaign Details</h3>
        <div id="campaignDetailsContent">
            <!-- Content will be inserted here by JavaScript -->
        </div>
    </div>
</div>

<!-- Request Manual Donation Modal -->
<div id="manualDonationModal" class="modal-overlay">
    <div class="modal-content manual-donation-modal">
        <!-- Close button -->
        <button type="button" onclick="closeManualDonationModal()" class="modal-close">
            ✖
        </button>

        <div class="modal-header">
            <h3 class="modal-title">
                Request Manual Donation
            </h3>
            <p class="modal-subtitle">Upload your payment proof for verification</p>
        </div>

        <form id="manualDonationForm" method="POST" enctype="multipart/form-data" class="modal-form">
            @csrf
            <input type="hidden" name="campaign_id" id="manualCampaignId">

            <div class="form-group">
                <label for="manualAmount" class="form-label">
                    <span class="label-text">Amount (₱)</span>
                    <span class="label-required">*</span>
                </label>
                <input type="number" id="manualAmount" name="amount" step="0.01" min="1"
                    placeholder="Enter amount" required class="form-input">
                <div class="form-hint">Enter the donation amount</div>
            </div>

            <div class="form-group">
                <label for="manualReference" class="form-label">
                    <span class="label-text">Reference Number</span>
                    <span class="label-optional">(Optional)</span>
                </label>
                <input type="text" id="manualReference" name="reference_number"
                    placeholder="e.g., Bank transfer reference" class="form-input">
                <div class="form-hint">Transaction ID or reference number</div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <span class="label-text">Payment Proof</span>
                    <span class="label-required">*</span>
                </label>

                <!-- Selected file name display -->
                <div class="selected-file-display" id="selectedFileDisplay" style="display: none;">
                    <div class="selected-file-info">
                        <i class="ri-file-line"></i>
                        <span class="file-name" id="selectedFileName"></span>
                        <button type="button" onclick="removeImage()" class="remove-file-btn" title="Remove file">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                </div>

                <!-- File upload area -->
                <label for="manualProofUpload" class="file-upload-area" id="fileUploadAreaManual">
                    <div class="file-upload-content">
                        <div class="file-upload-icon">📎</div>
                        <div class="file-upload-text">
                            <div class="file-upload-title">Upload payment proof</div>
                            <div class="file-upload-subtitle">Click or drag & drop image here</div>
                            <div class="file-upload-info">Supports JPG, PNG • Max 5MB</div>
                        </div>
                    </div>
                    <input type="file" id="manualProofUpload" name="proof_image" accept="image/*" required
                        class="file-upload-input">
                </label>

                <div class="file-validation" id="fileValidation"></div>
            </div>

            <div class="form-actions">
                <button type="button" onclick="closeManualDonationModal()" class="btn-cancel">
                    Cancel
                </button>
                <button type="submit" class="btn-submit">
                    Submit Request
                </button>
            </div>
        </form>

        <div class="modal-footer">
            <div class="modal-note">
                <i class="ri-information-line" style="color:#3b82f6; margin-right: 4px;"></i>
                <span>Must be approved by an admin before appearing in your campaign progress.</span>
            </div>
        </div>
    </div>
</div>


{{-- PROOF IMAGE MODAL WITH DETAILS --}}
<div id="proofModal" class="proof-modal">
    <div class="proof-modal-content">
        <button type="button" onclick="closeProofModal()" class="proof-modal-close">
            ✖
        </button>

        <div class="proof-modal-header">
            <h3 class="proof-modal-title">Donation Proof Verification</h3>
        </div>

        <div class="proof-modal-body">
            <div class="proof-details">
                <div class="proof-detail-item">
                    <span class="proof-detail-label">Donor:</span>
                    <span class="proof-detail-value" id="proofDonorName">-</span>
                </div>
                <div class="proof-detail-item">
                    <span class="proof-detail-label">Amount:</span>
                    <span class="proof-detail-value" id="proofAmount">-</span>
                </div>
                <div class="proof-detail-item">
                    <span class="proof-detail-label">Reference #:</span>
                    <span class="proof-detail-value" id="proofReference">-</span>
                </div>
            </div>

            <div class="proof-image-container">
                <img id="proofModalImage" src="" alt="Donation Proof" class="proof-modal-image">
            </div>
        </div>
    </div>
</div>

<!-- Update Progress Modal - Enhanced -->
<div id="updateProgressModal" class="modal-overlay">
    <div class="modal-content update-progress-modal">
        <button type="button" onclick="closeUpdateProgressModal()" class="modal-close">
            ✖
        </button>

        <div class="modal-header">
            <h3 class="modal-title">
                Update Progress
            </h3>
            <p class="modal-subtitle">Share your campaign progress with supporters</p>
        </div>

        <form id="updateProgressForm" method="POST" enctype="multipart/form-data" class="modal-form">
            @csrf
            <input type="hidden" name="campaign_id" id="updateCampaignId">

            <div class="form-group">
                <label for="updateMessage" class="form-label">
                    <span class="label-text">Message</span>
                    <span class="label-required">*</span>
                </label>
                <textarea id="updateMessage" name="message"
                    placeholder="Share exciting news about your campaign progress, milestones reached, or upcoming plans..." required
                    class="form-textarea" rows="5"></textarea>
                <div class="form-hint">Tell your supporters what's happening with your campaign</div>
            </div>

            <div class="form-group">
                <label for="updateImages" class="form-label">
                    <span class="label-text">Upload Images</span>
                    <span class="label-optional">(Optional)</span>
                </label>

                <!-- File upload area -->
                <label for="updateImages" class="file-upload-area" id="fileUploadArea">
                    <div class="file-upload-content">
                        <div class="file-upload-icon">🖼️</div>
                        <div class="file-upload-text">
                            <div class="file-upload-title">Drop images here or click to browse</div>
                            <div class="file-upload-subtitle">Supports JPG, PNG, GIF • Max 5MB each</div>
                        </div>
                    </div>
                    <input type="file" id="updateImages" name="images[]" multiple accept="image/*"
                        class="file-upload-input">
                </label>

                <!-- File selection indicator -->
                <div id="fileSelectionIndicator" class="file-selection-indicator">
                    <strong>📁 Selected files:</strong> <span id="selectedFilesCount">0</span> images
                </div>

                <div class="file-upload-note">You can select multiple images. First image will be used as featured.
                </div>
            </div>

            <div class="form-actions">
                <button type="button" onclick="closeUpdateProgressModal()" class="btn-cancel">
                    Cancel
                </button>
                <button type="submit" class="btn-submit">
                    Post Update
                </button>
            </div>
        </form>
    </div>
</div>

{{-- TOAST NOTIFICATION --}}
@include('partials.universalmodal')

<script>
    // Global state management
    let modalStack = [];
    let polling = null;
    let scrollTimeout = null;
    let isRefreshing = false;
    let activeFilter = 'all';
    let expandedDetails = new Set();
    let searchTerm = '';

    // NEW: Track which modal is currently open
    let currentOpenModal = null;

    // NEW: Track if we should force refresh after modal close
    let shouldForceRefresh = false;

    // Export donations as PDF
    function exportDonationsPDF(campaignId) {
        showToast('Generating PDF report...', 'success');
        window.location.href = `/campaigns/${campaignId}/export-donations-pdf?t=${Date.now()}`;
    }

    // Search functionality
    function initializeSearch() {
        const searchInput = document.getElementById('campaignSearch');
        if (!searchInput) return;

        let searchTimeout;
        searchInput.addEventListener('input', function(e) {
            searchTerm = e.target.value.toLowerCase().trim();
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applySearchAndFilter();
            }, 300);
        });

        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                e.target.value = '';
                searchTerm = '';
                applySearchAndFilter();
            }
        });
    }

    function applySearchAndFilter() {
        const campaigns = document.querySelectorAll('.campaign-item');
        let visibleCount = 0;

        campaigns.forEach(campaign => {
            const title = campaign.querySelector('.campaign-title')?.textContent?.toLowerCase() || '';
            const organizer = campaign.querySelector('.organizer-name')?.textContent?.toLowerCase() || '';
            const status = campaign.dataset.status || '';

            const matchesSearch = !searchTerm ||
                title.includes(searchTerm) ||
                organizer.includes(searchTerm);

            const matchesFilter = activeFilter === 'all' || status === activeFilter;

            const shouldShow = matchesSearch && matchesFilter;
            campaign.style.display = shouldShow ? 'block' : 'none';

            if (shouldShow) visibleCount++;
        });

        showNoResultsMessage(visibleCount === 0 && (searchTerm || activeFilter !== 'all'));
    }

    function showNoResultsMessage(show) {
        let noResultsMsg = document.getElementById('no-campaigns-message');

        if (show && !noResultsMsg) {
            noResultsMsg = document.createElement('div');
            noResultsMsg.id = 'no-campaigns-message';
            noResultsMsg.style.textAlign = 'center';
            noResultsMsg.style.padding = '2rem';
            noResultsMsg.style.color = '#6b7280';
            noResultsMsg.innerHTML = `
                <i class="ri-search-line" style="font-size:2rem; margin-bottom:1rem; display:block; color:#3b82f6;"></i>
                <p style="margin:0; font-size:14px;">
                    ${searchTerm ? `No campaigns found for "${searchTerm}"` : 'No campaigns match the current filter'}
                </p>
                ${searchTerm ? '<button onclick="clearSearch()" style="margin-top:0.5rem; padding:4px 12px; background:#3b82f6; color:white; border:none; border-radius:4px; cursor:pointer; font-size:12px;">Clear Search</button>' : ''}
            `;

            const container = document.getElementById('campaigns-container');
            container.appendChild(noResultsMsg);
        } else if (!show && noResultsMsg) {
            noResultsMsg.remove();
        }
    }

    function clearSearch() {
        const searchInput = document.getElementById('campaignSearch');
        if (searchInput) {
            searchInput.value = '';
            searchTerm = '';
            applySearchAndFilter();
            searchInput.focus();
        }
    }

    // Show proof image with donation details in modal
    function viewProofImageWithDetails(imageUrl, amount, reference, donorName) {
        document.getElementById("proofModalImage").src = imageUrl;
        document.getElementById("proofAmount").textContent = amount;
        document.getElementById("proofReference").textContent = reference;
        document.getElementById("proofDonorName").textContent = donorName;
        showModal('proofModal');
    }

    // Display campaign details in modal
    function showCampaignDetails(campaignId) {
        const detailsElement = document.getElementById(`details-${campaignId}`);
        const modal = document.getElementById('campaignDetailsModal');
        const content = document.getElementById('campaignDetailsContent');

        if (detailsElement && modal && content) {
            const detailsContent = detailsElement.cloneNode(true);
            detailsContent.style.display = 'block';
            detailsContent.classList.remove('campaign-details');
            detailsContent.classList.add('campaign-details-modal-content');

            content.innerHTML = '';
            content.appendChild(detailsContent);
            showModal('campaignDetailsModal');
            reattachModalEventListeners();
        }
    }

    // Show modal and stop background polling
    function showModal(modalId) {
        stopPolling();
        currentOpenModal = modalId;
        if (!modalStack.includes(modalId)) {
            modalStack.push(modalId);
        }
        document.getElementById(modalId).style.display = "flex";
    }

    // Close modal and restart polling with refresh
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
        const index = modalStack.indexOf(modalId);
        if (index > -1) {
            modalStack.splice(index, 1);
        }
        currentOpenModal = null;

        if (modalStack.length === 0) {
            startPolling();

            // NEW: Force immediate refresh when modal closes
            if (shouldForceRefresh) {
                shouldForceRefresh = false;
                setTimeout(() => {
                    if (!isRefreshing) {
                        forceRefreshCampaigns();
                    }
                }, 100);
            }
        }
    }

    // Individual modal close functions
    function closeCampaignDetailsModal() {
        closeModal('campaignDetailsModal');
    }

    function closeManualDonationModal() {
        closeModal('manualDonationModal');
        // Reset form properly
        setTimeout(() => {
            resetManualDonationForm();
        }, 300);
    }

    function closeProofModal() {
        document.getElementById("proofModalImage").src = "";
        document.getElementById("proofAmount").textContent = "-";
        document.getElementById("proofReference").textContent = "-";
        document.getElementById("proofDonorName").textContent = "-";
        closeModal('proofModal');
    }

    function closeConfirmModal() {
        closeModal('confirmModal');
    }

    // Close Update Progress Modal
    function closeUpdateProgressModal() {
        closeModal('updateProgressModal');
        setTimeout(() => {
            document.getElementById("updateProgressForm").reset();
            const fileInput = document.getElementById('updateImages');
            if (fileInput) {
                fileInput.value = '';
            }
            // Reset file selection indicator
            const fileIndicator = document.getElementById('fileSelectionIndicator');
            const filesCount = document.getElementById('selectedFilesCount');
            if (fileIndicator && filesCount) {
                filesCount.textContent = '0';
                fileIndicator.classList.remove('show');
            }
        }, 300);
    }

    // Enhanced modal event listeners for nested modals
    document.addEventListener('DOMContentLoaded', function() {
        const modals = ['campaignDetailsModal', 'manualDonationModal', 'proofModal', 'confirmModal',
            'updateProgressModal'
        ];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                // FIX: Don't clone the modal as it breaks event listeners
                document.getElementById(modalId).addEventListener('click', function(e) {
                    if (e.target === this && modalStack[modalStack.length - 1] === modalId) {
                        closeModal(modalId);
                    }
                });
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modalStack.length > 0) {
                closeModal(modalStack[modalStack.length - 1]);
            }
        });
    });

    // Open manual donation modal for specific campaign (nested on top)
    function openManualDonationModal(campaignId) {
        document.getElementById("manualCampaignId").value = campaignId;
        const form = document.getElementById("manualDonationForm");
        form.action = `/campaigns/${campaignId}/manual-request`;

        // Reset form and clear preview
        resetManualDonationForm();

        showModal('manualDonationModal');
    }

    // Open Update Progress Modal
    function openUpdateProgressModal(campaignId) {
        document.getElementById("updateCampaignId").value = campaignId;
        showModal('updateProgressModal');

        // Reset form and file tracking
        setTimeout(() => {
            document.getElementById("updateProgressForm").reset();
            const fileInput = document.getElementById('updateImages');
            if (fileInput) {
                fileInput.value = '';
            }
            // Reset file selection indicator
            const fileIndicator = document.getElementById('fileSelectionIndicator');
            const filesCount = document.getElementById('selectedFilesCount');
            if (fileIndicator && filesCount) {
                filesCount.textContent = '0';
                fileIndicator.classList.remove('show');
            }
        }, 100);
    }

    // Universal confirmation modal for various actions
    let formToSubmit = null;
    let confirmActionType = null;
    let confirmCampaignId = null;

    function openConfirmModal(form, options = {}) {
        formToSubmit = form || null;
        confirmActionType = options.type || null;
        confirmCampaignId = options.campaignId || null;

        document.getElementById("confirmModalTitle").textContent = options.title || "Confirm Action";
        document.getElementById("confirmModalMessage").textContent = options.message || "Are you sure?";

        const confirmBtn = document.getElementById("confirmActionBtn");
        confirmBtn.textContent = options.buttonText || "Confirm";
        confirmBtn.style.background = options.buttonColor || "#ef4444";

        showModal('confirmModal');
    }

    // Show toast notification
    function showToast(message, type = "success") {
        const toast = document.getElementById("toast");
        toast.textContent = message;
        toast.style.background = type === "error" ? "#dc2626" : "#16a34a";
        toast.style.opacity = "1";
        toast.style.display = "block";

        setTimeout(() => {
            toast.style.opacity = "0";
            setTimeout(() => toast.style.display = "none", 500);
        }, 3000);
    }

    // Safe JSON parser with error handling
    async function safeJson(res) {
        const text = await res.text();
        try {
            return JSON.parse(text);
        } catch {
            console.error("Non-JSON response:", text);
            return {
                success: false,
                error: "Unexpected server response"
            };
        }
    }

    // Re-attach event listeners inside modal content
    function reattachModalEventListeners() {
        document.querySelectorAll('#campaignDetailsContent .proof-image').forEach(img => {
            img.addEventListener('click', function() {
                const row = this.closest('tr');
                const donor = row.cells[0].textContent.trim();
                const amount = row.cells[1].textContent.trim();
                const reference = row.cells[2].textContent.trim();
                const imageUrl = this.getAttribute('src');
                viewProofImageWithDetails(imageUrl, amount, reference, donor);
            });
        });

        document.querySelectorAll('#campaignDetailsContent .manual-donation-btn').forEach(button => {
            button.addEventListener('click', function() {
                const campaignId = this.getAttribute('onclick').match(/\((\d+)\)/)[1];
                openManualDonationModal(campaignId);
            });
        });
    }

    // ============================================
    // UPDATED: Handle manual donation form submission
    // ============================================
    // ============================================
    // UPDATED: Handle manual donation form submission (FIXED VERSION)
    // ============================================
    function handleManualDonation(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        // Validate amount
        const amount = formData.get('amount');
        if (!amount || parseFloat(amount) <= 0) {
            showToast('Please enter a valid amount', 'error');
            return;
        }

        // Validate proof image
        const proofImage = formData.get('proof_image');
        if (!proofImage || proofImage.size === 0) {
            showToast('Please upload a payment proof image', 'error');
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Submitting...';
        submitBtn.disabled = true;

        showToast('Submitting donation request...', 'success');

        fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast(data.message || "Manual donation request submitted!", "success");

                    // Reset the button BEFORE closing modals
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;

                    setTimeout(() => {
                        closeManualDonationModal();
                        closeCampaignDetailsModal();
                        resetManualDonationForm();
                        shouldForceRefresh = true;
                    }, 1000);
                } else {
                    showToast(data.error || "Something went wrong.", "error");
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error("Manual donation error:", error);
                showToast("Request failed. Please try again.", "error");
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
    }
    // Handle Update Progress Form Submission (FIXED VERSION)
    function handleUpdateProgress(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Posting...';
        submitBtn.disabled = true;

        showToast('Posting update...', 'success');

        fetch("{{ route('campaign.updates.store') }}", {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                }
            })
            .then(safeJson)
            .then(data => {
                if (data.success) {
                    showToast(data.message || "Update posted successfully!", "success");

                    // Reset button BEFORE closing modal
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;

                    setTimeout(() => {
                        closeUpdateProgressModal();
                    }, 1000);
                    // NEW: Set flag to force refresh
                    shouldForceRefresh = true;
                } else {
                    showToast(data.error || "Failed to post update.", "error");
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error("Update progress error:", error);
                showToast("Failed to post update.", "error");
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
    }

    // Handle campaign deletion with optimistic UI update
    function handleDeleteCampaign() {
        const form = formToSubmit;
        const campaignItem = form.closest('.campaign-item');

        if (campaignItem) {
            campaignItem.style.transition = "all 0.3s ease";
            campaignItem.style.opacity = "0.5";

            setTimeout(() => {
                campaignItem.style.transform = "translateX(-100%)";
                campaignItem.style.opacity = "0";
                setTimeout(() => {
                    if (campaignItem.parentNode) {
                        campaignItem.remove();
                    }
                }, 300);
            }, 100);
        }

        showToast("Campaign deleted successfully!", "success");

        const formData = new FormData(form);
        formData.append("_method", "DELETE");

        fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                }
            })
            .then(safeJson)
            .then(data => {
                if (!data.success) {
                    console.error("Delete failed on server:", data.error);
                }
            })
            .catch(error => {
                console.error("Delete error:", error);
            });
    }

    // Handle campaign end with optimistic UI update
    function handleEndCampaign() {
        const campaignItem = document.querySelector(`[data-campaign-id="${confirmCampaignId}"]`) ||
            document.querySelector(`.campaign-item [data-id="${confirmCampaignId}"]`)?.closest('.campaign-item');

        if (campaignItem) {
            const statusElement = campaignItem.querySelector('.usedash-status');
            const progressFill = campaignItem.querySelector('.progress-fill');
            const endBtn = campaignItem.querySelector('.endCampaignBtn');

            if (statusElement) {
                statusElement.textContent = "Ended";
                statusElement.className = "usedash-status yellow";
            }
            if (progressFill) {
                progressFill.style.background = "#9ca3af";
            }
            campaignItem.dataset.status = "ended";

            if (endBtn) endBtn.style.display = 'none';
        }

        showToast("Campaign ended successfully!", "success");

        const formData = new FormData();
        formData.append("_method", "PATCH");

        fetch(`/campaigns/${confirmCampaignId}/end`, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
                body: formData
            })
            .then(safeJson)
            .then(data => {
                if (!data.success) {
                    console.error("End campaign failed on server:", data.error);
                }
            })
            .catch(error => {
                console.error("End campaign error:", error);
            });
    }

    // Handle fake donation reporting with optimistic UI update
    function handleReportFake() {
        const form = formToSubmit;
        const row = form.closest("tr");

        if (modalStack.includes('proofModal')) {
            closeProofModal();
        }

        if (row) {
            row.style.transition = "opacity 0.3s ease";
            row.style.opacity = "0.5";

            setTimeout(() => {
                row.style.opacity = "0";
                setTimeout(() => {
                    if (row.parentNode) {
                        row.remove();
                    }

                    const tbody = form.closest('tbody');
                    if (tbody && tbody.children.length === 0) {
                        const tableWrapper = tbody.closest('.donation-table-wrapper');
                        if (tableWrapper) {
                            const noDonationsMsg = document.createElement('p');
                            noDonationsMsg.style.color = '#6b7280';
                            noDonationsMsg.style.fontSize = '13px';
                            noDonationsMsg.style.marginTop = '6px';
                            noDonationsMsg.textContent = 'No donations yet.';
                            tableWrapper.parentNode.insertBefore(noDonationsMsg, tableWrapper
                                .nextSibling);
                            tableWrapper.remove();
                        }
                    }
                }, 300);
            }, 100);
        }

        showToast("Donation successfully reported as fake and removed from campaign records!", "success");

        const formData = new FormData(form);

        fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                }
            })
            .then(safeJson)
            .then(data => {
                if (data.success) {
                    // NEW: Set flag to force refresh
                    shouldForceRefresh = true;
                } else {
                    console.error("Report fake failed on server:", data.error);
                }
            })
            .catch(error => {
                console.error("Report fake error:", error);
            });
    }

    // Handle confirmation modal actions
    function handleConfirmAction() {
        if (confirmActionType === "delete" && formToSubmit) {
            handleDeleteCampaign();
        } else if (confirmActionType === "end" && confirmCampaignId) {
            handleEndCampaign();
        } else if (confirmActionType === "reportFake" && formToSubmit) {
            handleReportFake();
        }
        closeConfirmModal();
    }

    // ============================================
    // UPDATED: Attach event listeners
    // ============================================
    function attachEventListeners() {
        // Manual donation form
        const manualForm = document.getElementById("manualDonationForm");
        if (manualForm) {
            manualForm.removeEventListener("submit", handleManualDonation);
            manualForm.addEventListener("submit", handleManualDonation);
        }

        // Initialize manual donation upload
        initializeManualDonationUpload();

        // Update progress form
        const updateForm = document.getElementById("updateProgressForm");
        if (updateForm) {
            updateForm.removeEventListener("submit", handleUpdateProgress);
            updateForm.addEventListener("submit", handleUpdateProgress);
        }

        // Proof image click handlers
        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("proof-image")) {
                e.preventDefault();
                const row = e.target.closest('tr');
                const donor = row.cells[0].textContent.trim();
                const amount = row.cells[1].textContent.trim();
                const reference = row.cells[2].textContent.trim();
                const imageUrl = e.target.getAttribute("src");
                viewProofImageWithDetails(imageUrl, amount, reference, donor);
            }
        });

        // Confirm modal buttons
        const cancelBtn = document.getElementById("cancelConfirmBtn");
        if (cancelBtn) {
            cancelBtn.removeEventListener("click", closeConfirmModal);
            cancelBtn.addEventListener("click", closeConfirmModal);
        }

        const confirmActionBtn = document.getElementById("confirmActionBtn");
        if (confirmActionBtn) {
            confirmActionBtn.removeEventListener("click", handleConfirmAction);
            confirmActionBtn.addEventListener("click", handleConfirmAction);
        }

        // Manual proof upload label click
        const proofUpload = document.getElementById("manualProofUpload");
        const proofLabel = proofUpload?.previousElementSibling;
        if (proofLabel && proofLabel.tagName === 'LABEL') {
            proofLabel.addEventListener("click", () => proofUpload.click());
        }
    }

    // File Upload Functionality for Update Progress
    function initializeImageUpload() {
        const fileUploadArea = document.getElementById('fileUploadArea');
        const imageInput = document.getElementById('updateImages');
        const fileIndicator = document.getElementById('fileSelectionIndicator');
        const filesCount = document.getElementById('selectedFilesCount');

        if (fileUploadArea && imageInput) {
            // Original change handler - shows file count
            imageInput.addEventListener('change', function(e) {
                if (this.files && this.files.length > 0) {
                    filesCount.textContent = this.files.length;
                    fileIndicator.classList.add('show');
                } else {
                    filesCount.textContent = '0';
                    fileIndicator.classList.remove('show');
                }
            });

            // Drag & drop functionality
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                fileUploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                fileUploadArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                fileUploadArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                fileUploadArea.classList.add('drag-over');
            }

            function unhighlight() {
                fileUploadArea.classList.remove('drag-over');
            }

            // Handle dropped files
            fileUploadArea.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                imageInput.files = files;

                // Trigger change event
                const event = new Event('change');
                imageInput.dispatchEvent(event);
            });
        }
    }

    // ============================================
    // MANUAL DONATION UPLOAD FIXES
    // ============================================

    // Reset manual donation form
    function resetManualDonationForm() {
        const form = document.getElementById("manualDonationForm");
        if (form) {
            form.reset();
        }

        // Clear file name display
        const fileDisplay = document.getElementById('selectedFileDisplay');
        const fileName = document.getElementById('selectedFileName');
        const fileInput = document.getElementById('manualProofUpload');
        const validationDiv = document.getElementById('fileValidation');
        const fileUploadArea = document.getElementById('fileUploadAreaManual');

        if (fileDisplay) fileDisplay.style.display = 'none';
        if (fileName) fileName.textContent = '';
        if (fileInput) {
            fileInput.value = '';
            fileInput.required = true;
        }
        if (validationDiv) validationDiv.innerHTML = '';
        if (fileUploadArea) fileUploadArea.classList.remove('has-file');
    }

    // Remove image
    function removeImage() {
        const fileDisplay = document.getElementById('selectedFileDisplay');
        const fileName = document.getElementById('selectedFileName');
        const fileInput = document.getElementById('manualProofUpload');
        const validationDiv = document.getElementById('fileValidation');
        const fileUploadArea = document.getElementById('fileUploadAreaManual');

        if (fileDisplay) fileDisplay.style.display = 'none';
        if (fileName) fileName.textContent = '';
        if (fileInput) {
            fileInput.value = '';
            fileInput.required = true;
        }
        if (validationDiv) validationDiv.innerHTML = '';
        if (fileUploadArea) fileUploadArea.classList.remove('has-file');
    }

    // Initialize manual donation image upload
    function initializeManualDonationUpload() {
        const fileUploadArea = document.getElementById('fileUploadAreaManual');
        const imageInput = document.getElementById('manualProofUpload');
        const fileDisplay = document.getElementById('selectedFileDisplay');
        const fileName = document.getElementById('selectedFileName');
        const validationDiv = document.getElementById('fileValidation');

        if (fileUploadArea && imageInput) {
            // Handle file selection
            imageInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                    const maxSize = 5 * 1024 * 1024; // 5MB

                    // Validate file type
                    if (!validTypes.includes(file.type)) {
                        validationDiv.innerHTML =
                            '<span style="color:#ef4444;">❌ Invalid file type. Please upload JPG, PNG or GIF.</span>';
                        this.value = '';
                        if (fileDisplay) fileDisplay.style.display = 'none';
                        fileUploadArea.classList.remove('has-file');
                        return;
                    }

                    // Validate file size
                    if (file.size > maxSize) {
                        validationDiv.innerHTML =
                            '<span style="color:#ef4444;">❌ File too large. Maximum size is 5MB.</span>';
                        this.value = '';
                        if (fileDisplay) fileDisplay.style.display = 'none';
                        fileUploadArea.classList.remove('has-file');
                        return;
                    }

                    // Clear validation
                    validationDiv.innerHTML = '<span style="color:#16a34a;">✓ File selected</span>';

                    // Show file name
                    if (fileDisplay && fileName) {
                        fileName.textContent = file.name;
                        fileDisplay.style.display = 'block';
                        fileUploadArea.classList.add('has-file');
                    }

                    // Remove required attribute since we have an image
                    this.required = false;
                }
            });

            // Drag & drop functionality
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                fileUploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                fileUploadArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                fileUploadArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                fileUploadArea.classList.add('drag-over');
            }

            function unhighlight() {
                fileUploadArea.classList.remove('drag-over');
            }

            // Handle dropped files
            fileUploadArea.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;

                if (files && files[0]) {
                    imageInput.files = files;

                    // Trigger change event
                    const event = new Event('change');
                    imageInput.dispatchEvent(event);
                }
            });
        }
    }
    // Event delegation for dynamic elements
    document.addEventListener("submit", function(e) {
        if (e.target.classList.contains("deleteCampaignForm")) {
            e.preventDefault();
            openConfirmModal(e.target, {
                title: "Confirm Deletion",
                message: "Are you sure you want to delete this campaign?",
                buttonText: "Delete",
                buttonColor: "#ef4444",
                type: "delete"
            });
        }

        if (e.target.classList.contains("reportFakeForm")) {
            e.preventDefault();
            openConfirmModal(e.target, {
                title: "Report Fake Donation",
                message: "Are you sure you want to mark this donation as fake? This will remove it from the campaign records.",
                buttonText: "Report Fake",
                buttonColor: "#b91c1c",
                type: "reportFake"
            });
        }
    });

    document.addEventListener("click", function(e) {
        if (e.target.matches(".endCampaignBtn")) {
            e.preventDefault();
            const campaignId = e.target.getAttribute("data-id");
            openConfirmModal(null, {
                title: "End Campaign",
                message: "Are you sure you want to end this campaign? This action cannot be undone.",
                buttonText: "End Now",
                buttonColor: "#f59e0b",
                type: "end",
                campaignId: campaignId
            });
        }
    });

    // NEW: Force refresh function that bypasses modal checks
    function forceRefreshCampaigns() {
        if (isRefreshing) {
            setTimeout(forceRefreshCampaigns, 100);
            return;
        }

        const container = document.getElementById('campaigns-container');
        if (!container) return;

        isRefreshing = true;

        const containerScroll = container.scrollTop;
        const tableScrolls = {};
        document.querySelectorAll('.donation-table-wrapper').forEach((tbl, idx) => {
            tableScrolls[idx] = tbl.scrollTop;
        });

        const timestamp = Date.now();

        fetch(`{{ route('profile.dash.data') }}?t=${timestamp}&force=true`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text();
            })
            .then(html => {
                requestAnimationFrame(() => {
                    container.innerHTML = html;
                    applySearchAndFilter();
                    restoreExpandedDetails();
                    container.scrollTop = containerScroll;
                    document.querySelectorAll('.donation-table-wrapper').forEach((tbl, idx) => {
                        if (tableScrolls[idx] !== undefined) tbl.scrollTop = tableScrolls[idx];
                    });
                    attachEventListeners();
                    setTimeout(initializeImageUpload, 100);
                    showDataUpdatedFeedback();
                });
            })
            .catch(err => {
                console.error('Failed to force refresh campaigns:', err);
            })
            .finally(() => {
                isRefreshing = false;
            });
    }

    // Original refresh campaigns data
    function refreshCampaigns() {
        // Don't refresh if ANY modal is open
        if (modalStack.length > 0) {
            return;
        }

        if (isRefreshing) {
            return;
        }

        const container = document.getElementById('campaigns-container');
        if (!container) return;

        isRefreshing = true;

        const containerScroll = container.scrollTop;
        const tableScrolls = {};
        document.querySelectorAll('.donation-table-wrapper').forEach((tbl, idx) => {
            tableScrolls[idx] = tbl.scrollTop;
        });

        const timestamp = Date.now();

        fetch(`{{ route('profile.dash.data') }}?t=${timestamp}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text();
            })
            .then(html => {
                requestAnimationFrame(() => {
                    container.innerHTML = html;
                    applySearchAndFilter();
                    restoreExpandedDetails();
                    container.scrollTop = containerScroll;
                    document.querySelectorAll('.donation-table-wrapper').forEach((tbl, idx) => {
                        if (tableScrolls[idx] !== undefined) tbl.scrollTop = tableScrolls[idx];
                    });
                    attachEventListeners();
                    setTimeout(initializeImageUpload, 100);
                    showDataUpdatedFeedback();
                });
            })
            .catch(err => {
                console.error('Failed to refresh campaigns:', err);
            })
            .finally(() => {
                isRefreshing = false;
            });
    }

    // Visual feedback for data updates
    function showDataUpdatedFeedback() {
        const container = document.getElementById('campaigns-container');
        if (!container) return;

        container.style.transition = 'none';
        container.style.opacity = '0.98';

        requestAnimationFrame(() => {
            container.style.transition = 'opacity 0.3s ease';
            container.style.opacity = '1';
        });

        setTimeout(() => {
            container.style.transition = '';
        }, 300);
    }

    // Polling function for background updates - UPDATED
    function fetchCampaigns() {
        // Stop polling if ANY modal is open
        if (modalStack.length > 0) {
            return;
        }

        if (isRefreshing) {
            return;
        }

        const container = document.getElementById('campaigns-container');
        if (!container) return;

        const containerScroll = container.scrollTop;
        const tableScrolls = {};
        document.querySelectorAll('.donation-table-wrapper').forEach((tbl, idx) => {
            tableScrolls[idx] = tbl.scrollTop;
        });

        fetch("{{ route('profile.dash.data') }}?polling=true")
            .then(response => response.text())
            .then(html => {
                // Check if modal opened during fetch
                if (modalStack.length === 0 && !isRefreshing) {
                    // Don't replace the entire container - modals might be in use
                    // Instead, update only if no modal is open
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;

                    // Update only campaign items, preserve modals
                    const newCampaigns = tempDiv.querySelectorAll('.campaign-item');
                    const oldCampaigns = container.querySelectorAll('.campaign-item');

                    // Simple update strategy
                    if (newCampaigns.length === oldCampaigns.length) {
                        newCampaigns.forEach((newCampaign, index) => {
                            if (oldCampaigns[index]) {
                                oldCampaigns[index].outerHTML = newCampaign.outerHTML;
                            }
                        });
                    }

                    applySearchAndFilter();
                    restoreExpandedDetails();
                    container.scrollTop = containerScroll;
                    document.querySelectorAll('.donation-table-wrapper').forEach((tbl, idx) => {
                        if (tableScrolls[idx] !== undefined) tbl.scrollTop = tableScrolls[idx];
                    });
                    attachEventListeners();
                    initializeImageUpload();
                    initializeManualDonationUpload();
                    initializeSearch();
                }
            })
            .catch(err => console.error('Polling error:', err));
    }

    // Start background polling
    function startPolling() {
        if (!polling && modalStack.length === 0) {
            polling = setInterval(fetchCampaigns, 3000);
        }
    }

    // Stop background polling
    function stopPolling() {
        if (polling) {
            clearInterval(polling);
            polling = null;
        }
    }

    // Initialize application
    document.addEventListener('DOMContentLoaded', function() {
        startPolling();
        attachEventListeners();
        initializeSearch();
        initializeImageUpload();
        initializeManualDonationUpload();

        const originalRefresh = refreshCampaigns;
        window.refreshCampaigns = function() {
            originalRefresh();
            setTimeout(() => {
                initializeSearch();
                initializeImageUpload();
                initializeManualDonationUpload();
            }, 100);
        };
    });

    // Handle scroll events to pause polling
    document.addEventListener("scroll", (e) => {
        if (e.target.closest(".donation-table-wrapper")) {
            stopPolling();
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(startPolling, 2000);
        }
    }, true);

    // Utility functions
    function viewProofImage(imageUrl) {
        viewProofImageWithDetails(imageUrl, '-', '-', '-');
    }

    function toggleDetails(id) {
        const section = document.getElementById("details-" + id);
        if (section) {
            const isHidden = section.style.display === "none" || section.style.display === "";
            section.style.display = isHidden ? "block" : "none";
            if (isHidden) expandedDetails.add(id);
            else expandedDetails.delete(id);
        }
    }

    function closeAllDetails() {
        expandedDetails.forEach(id => {
            const section = document.getElementById("details-" + id);
            if (section) section.style.display = "none";
        });
        expandedDetails.clear();
    }

    function showAllDonations(campaignId) {
        const details = document.querySelectorAll(`#details-${campaignId} tbody tr`);
        details.forEach(row => row.style.display = "table-row");
        const btn = document.querySelector(`#details-${campaignId} button`);
        if (btn) btn.remove();
    }

    function showFileName(input, id) {
        if (input.files && input.files.length > 0) {
            const label = document.getElementById('file-label-' + id);
            if (label) label.querySelector('span').textContent = "📎 " + input.files[0].name;
        }
    }

    // Updated applyFilter to work with search
    function applyFilter() {
        applySearchAndFilter();
    }

    function restoreExpandedDetails() {
        expandedDetails.forEach(id => {
            const section = document.getElementById("details-" + id);
            if (section) section.style.display = "block";
        });
    }

    // Filter initialization
    const filterEl = document.getElementById('campaignFilter');
    if (filterEl) {
        filterEl.addEventListener('change', function() {
            activeFilter = this.value;
            applyFilter();
        });
    }
</script>
