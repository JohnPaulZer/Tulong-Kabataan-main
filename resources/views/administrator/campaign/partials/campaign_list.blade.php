<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
    <h2 style="font-size:20px;font-weight:700;color:#111827;">Users Existing Campaigns</h2>
</div>

<!-- Campaign Details Modal -->
<div id="campaignDetailsModal" class="campaign-details-modal" style="display:none;">
    <div class="campaign-details-content">
        <button id="closeCampaignDetailsModal" class="campaign-details-close">&times;</button>

        <div class="campaign-details-header">
            <h2 class="campaign-details-title">Manual Donation Details</h2>
        </div>

        <div class="campaign-details-body">
            <!-- Details Section (Left) -->
            <div class="campaign-details-sidebar">
                <div class="campaign-detail-item">
                    <span class="campaign-detail-label">Request ID:</span>
                    <span class="campaign-detail-value" id="detailRequestId"></span>
                </div>
                <div class="campaign-detail-item">
                    <span class="campaign-detail-label">Requested By:</span>
                    <span class="campaign-detail-value" id="detailRequestedBy"></span>
                </div>
                <div class="campaign-detail-item">
                    <span class="campaign-detail-label">Amount:</span>
                    <span class="campaign-detail-value" id="detailAmount"></span>
                </div>
                <div class="campaign-detail-item">
                    <span class="campaign-detail-label">Reference #:</span>
                    <span class="campaign-detail-value" id="detailReference"></span>
                </div>
                <div class="campaign-detail-item">
                    <span class="campaign-detail-label">Status:</span>
                    <span class="campaign-detail-value" id="detailStatus"></span>
                </div>
                <div class="campaign-detail-item">
                    <span class="campaign-detail-label">Campaign:</span>
                    <span class="campaign-detail-value" id="detailCampaign"></span>
                </div>
                <div class="campaign-detail-item">
                    <span class="campaign-detail-label">Organizer:</span>
                    <span class="campaign-detail-value" id="detailOrganizer"></span>
                </div>
                <div class="campaign-detail-item">
                    <span class="campaign-detail-label">Requested At:</span>
                    <span class="campaign-detail-value" id="detailRequestedAt"></span>
                </div>
            </div>

            <!-- Proof Image Section (Right) -->
            <div class="campaign-proof-container">
                <div class="proof-main-image">
                    <img id="campaignProofImage" src="" alt="Proof Image" class="proof-image">
                    <div class="proof-caption" id="proofCaption">Payment Proof</div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Image Overlay Modal -->
<div id="imageOverlay" class="image-overlay" style="display: none;">
    <button class="overlay-close">&times;</button>
    <div class="overlay-image-container">
        <img id="overlayImage" src="" alt="">
        <div class="overlay-caption" id="overlayCaption"></div>
    </div>
</div>



@foreach ($campaigns as $campaign)
    <div
        style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:20px;margin-bottom:20px;box-shadow:0 1px 2px rgba(0,0,0,0.05);">
        <div style="display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:8px;">
            <div>
                <h3 style="margin:0;font-size:17px;font-weight:700;">{{ $campaign->title }}</h3>
                <p style="margin:2px 0 6px 0;font-size:14px;color:#374151;">
                    Organized by:
                    <strong>{{ $campaign->campaign_organizer ?? ($campaign->organizer->name ?? 'N/A') }}</strong>
                </p>
                <p style="margin:0;font-size:13px;color:#6b7280;">
                    @if ($campaign->schedule_type == 'one_time')
                        <i class="ri-calendar-2-line"></i> One-Time Campaign
                    @else
                        <i class="ri-refresh-line"></i> Recurring Campaign
                    @endif
                    <br>
                    <i class="ri-timer-line"></i> Start:
                    {{ $campaign->starts_at ? $campaign->starts_at->format('M d, Y') : 'N/A' }}
                    &nbsp; | &nbsp;
                    <i class="ri-hourglass-line"></i> End:
                    {{ $campaign->ends_at ? $campaign->ends_at->format('M d, Y') : 'N/A' }}
                </p>
            </div>
            <div>
                <span
                    style="
                    background: {{ $campaign->status == 'active' ? '#dcfce7' : ($campaign->status == 'completed' ? '#dbeafe' : '#fee2e2') }};
                    color: {{ $campaign->status == 'active' ? '#065f46' : ($campaign->status == 'completed' ? '#1e3a8a' : '#991b1b') }};
                    font-size:13px;font-weight:600;padding:4px 10px;border-radius:999px;">
                    {{ ucfirst($campaign->status) }}
                </span>
            </div>
        </div>

        <!-- Progress Bar -->
        @php
            $progress =
                $campaign->target_amount > 0
                    ? round(($campaign->current_amount / $campaign->target_amount) * 100, 1)
                    : 0;
        @endphp

        <div style="margin-top:14px;">
            <p style="font-size:14px;color:#111827;margin-bottom:4px;">Progress</p>
            <div style="position:relative;height:10px;background:#f3f4f6;border-radius:999px;overflow:hidden;">
                <div style="width:{{ $progress }}%;height:100%;background:#3b82f6;border-radius:999px;"></div>
                <span
                    style="position:absolute;right:8px;top:-22px;font-size:13px;font-weight:600;color:#111827;">{{ $progress }}%</span>
            </div>
            <p style="margin-top:6px;font-size:14px;font-weight:500;">₱{{ number_format($campaign->current_amount, 2) }}
                / ₱{{ number_format($campaign->target_amount, 2) }}</p>
        </div>

        <!-- Buttons -->
        <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;">
            <button class="btn btn-light" onclick="toggleRequests('req{{ $campaign->campaign_id }}')">
                <i class="ri-file-list-line"></i> View Requests
            </button>
            <a href="{{ route('admin.campaigns.export.pdf', $campaign->campaign_id) }}" class="btn btn-light"
                target="_blank" style="display:inline-flex;align-items:center;gap:5px; text-decoration: none;">
                <i class="ri-file-download-line"></i> Export PDF
            </a>
        </div>

        <!-- Manual Donation Requests -->
        <div id="req{{ $campaign->campaign_id }}" style="display:none;margin-top:20px;">
            <h4 style="font-size:16px;font-weight:700;margin-bottom:10px;">Manual Donation Requests</h4>

            @if ($campaign->manualRequests->isEmpty())
                <p style="color:#6b7280;">No manual donation requests found.</p>
            @else
                <div style="overflow-x:auto;">
                    <table
                        style="width:100%;border-collapse:collapse;background:#fff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
                        <thead style="background:#f9fafb;">
                            <tr>
                                <th style="padding:10px 14px;">ID</th>
                                <th style="padding:10px 14px;">Requested By</th>
                                <th style="padding:10px 14px;">Amount</th>
                                <th style="padding:10px 14px;">Reference #</th>
                                <th style="padding:10px 14px;">Proof</th>
                                <th style="padding:10px 14px;">Status</th>
                                <th style="padding:10px 14px;">Requested At</th>
                                <th style="padding:10px 14px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($campaign->manualRequests as $req)
                                <tr style="border-top:1px solid #e5e7eb;">
                                    <td style="padding:10px 14px;">{{ $req->request_id }}</td>
                                    <td style="padding:10px 14px;">{{ $req->creator->first_name ?? 'Unknown' }}</td>
                                    <td style="padding:10px 14px;">₱{{ number_format($req->amount, 2) }}</td>
                                    <td style="padding:10px 14px;">{{ $req->reference_number ?? 'N/A' }}</td>
                                    <td style="padding:10px 14px;">
                                        @if ($req->proof_image)
                                            <button type="button" class="btn-outline view-campaign-proof-btn"
                                                data-request-id="{{ $req->request_id }}"
                                                data-requested-by="{{ $req->creator->first_name ?? 'Unknown' }} {{ $req->creator->last_name ?? '' }}"
                                                data-amount="₱{{ number_format($req->amount, 2) }}"
                                                data-reference="{{ $req->reference_number ?? 'N/A' }}"
                                                data-status="{{ $req->status }}"
                                                data-campaign="{{ $campaign->title }}"
                                                data-organizer="{{ $campaign->campaign_organizer ?? ($campaign->organizer->name ?? 'N/A') }}"
                                                data-requested-at="{{ $req->created_at->format('M d, Y h:i A') }}"
                                                data-proof-image="{{ $req->proof_image ? asset('storage/' . $req->proof_image) : '' }}">
                                                <i class="ri-eye-line"></i> View
                                            </button>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td style="padding:10px 14px;" id="status-cell-{{ $req->request_id }}">
                                        @php
                                            $statusColors = [
                                                'approved' => ['bg' => '#dcfce7', 'color' => '#166534'],
                                                'pending' => ['bg' => '#fef9c3', 'color' => '#92400e'],
                                                'rejected' => ['bg' => '#fee2e2', 'color' => '#991b1b'],
                                            ];
                                            $s = strtolower($req->status);
                                            $badge = $statusColors[$s] ?? ['bg' => '#f3f4f6', 'color' => '#374151'];
                                        @endphp

                                        <span
                                            style="
                                            background: {{ $badge['bg'] }};
                                            color: {{ $badge['color'] }};
                                            font-size:13px;
                                            font-weight:600;
                                            padding:4px 12px;
                                            border-radius:999px;
                                            display:inline-flex;
                                            align-items:center;
                                            justify-content:center;
                                            min-width:90px;
                                            height:24px;
                                            text-align:center;
                                            white-space:nowrap;
                                            box-sizing:border-box;">
                                            {{ ucfirst($req->status) }}
                                        </span>
                                    </td>

                                    <td style="padding:10px 14px;">{{ $req->created_at->format('M d, Y h:i A') }}</td>
                                    <td style="padding:10px 14px;" id="action-cell-{{ $req->request_id }}">
                                        @if ($req->status === 'pending')
                                            <button class="btn btn-approve"
                                                onclick="showConfirmModal('{{ route('admin.manual.requests.approve', $req->request_id) }}', {{ $req->request_id }}, 'approved')">
                                                Approve
                                            </button>
                                            <button class="btn btn-reject"
                                                onclick="showConfirmModal('{{ route('admin.manual.requests.reject', $req->request_id) }}', {{ $req->request_id }}, 'rejected')">
                                                Reject
                                            </button>
                                        @else
                                            <button class="btn btn-disabled" disabled>No actions</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endforeach
