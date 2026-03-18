<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Manual Donation Requests - {{ $campaign->title }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            margin: 15px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3B82F6;
        }

        .campaign-title {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .report-subtitle {
            font-size: 16px;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .date-info {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        th {
            background: linear-gradient(135deg, #3B82F6 0%, #2563eb 100%);
            color: white;
            padding: 14px 12px;
            text-align: left;
            font-weight: bold;
            font-size: 13px;
            border: none;
            position: sticky;
            top: 0;
        }

        td {
            padding: 14px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 12px;
            vertical-align: middle;
        }

        tr:hover {
            background-color: #f9fafb;
        }

        .proof-container {
            text-align: center;
            padding: 10px;
        }

        .proof-image {
            width: 250px;
            height: 500px;
            object-fit: contain;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: #f9fafb;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            display: block;
            margin: 0 auto;
        }

        .no-proof {
            width: 250px;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-style: italic;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            background: #f9fafb;
            font-size: 14px;
            text-align: center;
            padding: 20px;
        }

        .filename {
            font-size: 11px;
            color: #6b7280;
            margin-top: 8px;
            word-break: break-all;
            text-align: center;
        }

        .amount-cell {
            font-weight: bold;
            color: #059669;
            font-size: 13px;
            font-family: 'Courier New', monospace;
        }

        .reference-cell {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #1f2937;
            background: #f3f4f6;
            padding: 8px 10px;
            border-radius: 6px;
            font-size: 11px;
            display: inline-block;
        }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            min-width: 85px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-approved {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
        }

        .status-pending {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
        }

        .status-rejected {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);
        }

        .donor-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 12px;
        }

        /* Column widths */
        th:nth-child(1),
        td:nth-child(1) {
            width: 8%;
            text-align: center;
        }

        th:nth-child(2),
        td:nth-child(2) {
            width: 14%;
        }

        th:nth-child(3),
        td:nth-child(3) {
            width: 10%;
        }

        th:nth-child(4),
        td:nth-child(4) {
            width: 16%;
        }

        th:nth-child(5),
        td:nth-child(5) {
            width: 260px;
            /* Increased for 250px image + padding */
            text-align: center;
        }

        th:nth-child(6),
        td:nth-child(6) {
            width: 13%;
            text-align: center;
        }

        th:nth-child(7),
        td:nth-child(7) {
            width: 13%;
            text-align: center;
        }

        .footer {
            margin-top: 35px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }

        .summary {
            margin-top: 8px;
            font-weight: 600;
            color: #3B82F6;
            font-size: 13px;
        }

        /* Responsive for PDF */
        @page {
            margin: 20mm;
        }

        .total-row {
            background: #f8fafc;
            font-weight: bold;
            border-top: 2px solid #3B82F6;
            font-size: 13px;
        }

        .total-row td {
            padding: 16px 12px;
            font-size: 13px;
        }

        .request-id {
            font-weight: bold;
            color: #3B82F6;
        }

        /* Center alignment for all table cells except donor name */
        td:not(:nth-child(2)) {
            text-align: center;
        }

        th:not(:nth-child(2)) {
            text-align: center;
        }

        /* Better spacing */
        .spacer {
            height: 10px;
        }

        .new-page {
            page-break-before: always;
        }

        .page-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3B82F6;
        }

        .page-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="campaign-title">{{ $campaign->title }}</div>
        <div class="report-subtitle">Manual Donation Requests Verification</div>
        <div class="date-info">Report Generated: {{ $date }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Donor</th>
                <th>Amount</th>
                <th>Reference</th>
                <th>Proof Image</th>
                <th>Status</th>
                <th>Date Requested</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; @endphp
            @foreach ($campaign->manualRequests as $request)
                @php $totalAmount += $request->amount; @endphp
                <tr>
                    <td class="request-id">#{{ $request->request_id }}</td>
                    <td class="donor-name">{{ $request->creator->first_name ?? 'Unknown' }}
                        {{ $request->creator->last_name ?? '' }}</td>
                    <td class="amount-cell">{{ number_format($request->amount, 2) }}</td>
                    <td>
                        <span class="reference-cell">{{ $request->reference_number ?? 'N/A' }}</span>
                    </td>
                    <td class="proof-container">
                        @if ($request->proof_image)
                            @php
                                $filename = basename($request->proof_image);
                                $paths = [
                                    storage_path('app/public/manual_proofs/' . $filename),
                                    public_path('storage/manual_proofs/' . $filename),
                                    storage_path('app/public/' . $request->proof_image),
                                    public_path('storage/' . $request->proof_image),
                                ];

                                $imageFound = false;
                                $imageSrc = null;

                                foreach ($paths as $path) {
                                    if (file_exists($path) && is_file($path)) {
                                        try {
                                            $imageData = base64_encode(file_get_contents($path));
                                            $imageInfo = getimagesize($path);
                                            $mimeType = $imageInfo['mime'] ?? 'image/jpeg';
                                            $imageSrc = 'data:' . $mimeType . ';base64,' . $imageData;
                                            $imageFound = true;
                                            break;
                                        } catch (\Exception $e) {
                                            continue;
                                        }
                                    }
                                }
                            @endphp

                            @if ($imageFound)
                                <img src="{{ $imageSrc }}" class="proof-image" alt="Payment Proof">
                                <div class="filename">{{ $filename }}</div>
                            @else
                                <div class="no-proof">
                                    Image not found<br>
                                    <span style="font-size: 10px;">{{ $filename }}</span>
                                </div>
                            @endif
                        @else
                            <div class="no-proof">No proof uploaded</div>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge status-{{ strtolower($request->status) }}">
                            {{ ucfirst($request->status) }}
                        </span>
                    </td>
                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                </tr>
            @endforeach

            <!-- Total Row -->
            <tr class="total-row">
                <td colspan="2"><strong>TOTAL</strong></td>
                <td><strong>₱{{ number_format($totalAmount, 2) }}</strong></td>
                <td colspan="4"></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div style="font-size: 13px; font-weight: 600;">Tulong Kabataan Administrator System</div>
        <div class="summary">
            Total Requests: {{ $campaign->manualRequests->count() }} |
            Total Amount: ₱{{ number_format($totalAmount, 2) }}
        </div>
        <div style="margin-top: 8px; font-size: 11px;">
            Document ID: REPORT-{{ $campaign->campaign_id }}-{{ now()->format('YmdHis') }}
        </div>
    </div>
</body>

</html>
