<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Donations Report - {{ $campaign->title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
            color: #1e293b;
        }

        .header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 25px;
            text-align: center;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
            font-weight: 400;
        }

        .summary {
            background: #f8fafc;
            padding: 20px;
            border: 1px solid #e2e8f0;
            margin-bottom: 25px;
            border-radius: 10px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .summary-item {
            background: white;
            padding: 18px;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .summary-label {
            font-size: 14px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-value {
            font-size: 20px;
            font-weight: 700;
            color: #1e40af;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            table-layout: fixed;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        th {
            background: #1e40af;
            color: white;
            padding: 16px 10px;
            text-align: center;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 14px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
            vertical-align: middle;
            word-wrap: break-word;
            text-align: center;
            background: white;
        }

        tr:hover td {
            background: #f8fafc;
        }

        .col-amount {
            font-weight: 600;
            color: #1e40af;
            font-size: 15px;
        }

        .total {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            font-weight: 700;
            padding: 22px;
            text-align: center;
            margin-top: 25px;
            border-radius: 10px;
            color: white;
            font-size: 20px;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        /* IMPROVED IMAGE STYLES */
        .proof-image {
            width: 200px;
            height: 350px;
            object-fit: contain;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            display: block;
            margin: 0 auto;
        }

        .proof-container {
            text-align: center;
            padding: 10px;
            width: 210px;
        }

        .no-proof {
            color: #94a3b8;
            font-style: italic;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 350px;
            border: 2px dashed #cbd5e1;
            border-radius: 6px;
            background: #f8fafc;
        }

        .reference-cell {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            background: #f1f5f9;
            padding: 10px;
            border-radius: 6px;
            word-break: break-all;
            font-size: 13px;
            color: #334155;
            border: 1px solid #e2e8f0;
        }

        /* Improved column widths */
        th:nth-child(1),
        td:nth-child(1) {
            width: 18%;
        }

        th:nth-child(2),
        td:nth-child(2) {
            width: 22%;
        }

        th:nth-child(3),
        td:nth-child(3) {
            width: 10%;
        }

        th:nth-child(4),
        td:nth-child(4) {
            width: 15%;
        }

        th:nth-child(5),
        td:nth-child(5) {
            width: 210px;
        }

        th:nth-child(6),
        td:nth-child(6) {
            width: 15%;
        }

        /* Verification Notes */
        .verification-notes {
            margin-top: 30px;
            padding: 20px;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            border-left: 4px solid #0ea5e9;
        }

        .verification-notes h4 {
            margin: 0 0 12px 0;
            color: #0369a1;
            font-size: 16px;
            font-weight: 600;
        }

        .verification-notes p {
            margin: 8px 0;
            font-size: 13px;
            color: #475569;
            line-height: 1.6;
        }

        .verification-notes strong {
            color: #1e40af;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>DONATIONS REPORT - {{ strtoupper($campaign->title) }}</h1>
        <p style="margin: 5px 0 0 0; font-size: 16px; opacity: 0.9;">Verification Report - Compare Reference Numbers with
            Proof Images</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Campaign</div>
                <div class="summary-value">{{ $campaign->title }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Target Amount</div>
                <div class="summary-value">₱{{ number_format($campaign->target_amount) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Amount Raised</div>
                <div class="summary-value">₱{{ number_format($campaign->current_amount) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Donations</div>
                <div class="summary-value">{{ $donations->count() }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Donor Name</th>
                <th>Donor Email</th>
                <th>Amount</th>
                <th>Reference Number</th>
                <th>Proof Image</th>
                <th>Donation Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($donations as $donation)
                @php
                    if ($donation->is_anonymous) {
                        $donorName = 'Anonymous';
                        $donorEmail = '-';
                    } else {
                        if ($donation->user) {
                            $donorName = $donation->user->first_name . ' ' . $donation->user->last_name;
                            $donorEmail = $donation->user->email;
                        } elseif (!empty($donation->donor_name)) {
                            $donorName = $donation->donor_name;
                            $donorEmail = $donation->donor_email ?? '-';
                        } else {
                            $donorName = 'Guest Donor';
                            $donorEmail = '-';
                        }
                    }

                    $reference = $donation->reference_number ?? 'N/A';
                @endphp

                <tr>
                    <td>{{ $donorName }}</td>
                    <td>{{ $donorEmail }}</td>
                    <td class="col-amount">{{ number_format($donation->amount, 2) }}</td>
                    <td class="reference-cell">{{ $reference }}</td>
                    <td class="proof-container">
                        @if ($donation->proof_image)
                            @php
                                $imageFound = false;
                                $possiblePaths = [
                                    storage_path('app/public/donation_proofs/' . basename($donation->proof_image)),
                                    storage_path('app/donation_proofs/' . basename($donation->proof_image)),
                                    storage_path('app/public/' . $donation->proof_image),
                                    storage_path('app/' . $donation->proof_image),
                                    public_path('storage/donation_proofs/' . basename($donation->proof_image)),
                                    public_path('storage/' . $donation->proof_image),
                                ];
                            @endphp

                            @foreach ($possiblePaths as $proofPath)
                                @if (file_exists($proofPath) && is_file($proofPath))
                                    @php
                                        $imageData = base64_encode(file_get_contents($proofPath));
                                        $imageInfo = getimagesize($proofPath);
                                        $mimeType = $imageInfo['mime'] ?? 'image/jpeg';
                                        $imageSrc = 'data:' . $mimeType . ';base64,' . $imageData;
                                        $imageFound = true;
                                    @endphp
                                    <img src="{{ $imageSrc }}" class="proof-image" alt="Donation Proof">
                                    <div style="font-size: 12px; color: #666; margin-top: 5px;">
                                        {{ basename($donation->proof_image) }}
                                    </div>
                                    @break
                                @endif
                            @endforeach

                            @if (!$imageFound)
                                <span class="no-proof">Image not found: {{ basename($donation->proof_image) }}</span>
                            @endif
                        @else
                            <span class="no-proof">No proof uploaded</span>
                        @endif
                    </td>
                    <td>{{ $donation->created_at->format('M j, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total Raised: ₱{{ number_format($donations->sum('amount'), 2) }}
    </div>

    <!-- Verification Notes -->
    <div style="margin-top: 30px; padding: 15px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 6px;">
        <h4 style="margin: 0 0 10px 0; color: #0369a1;">Verification Instructions:</h4>
        <p style="margin: 5px 0; font-size: 13px; color: #475569;">
            • Compare the <strong>Reference Number</strong> in the table with the reference number shown in the
            <strong>Proof Image</strong><br>
            • Ensure the amounts match between the transaction record and the proof image<br>
            • Verify the donor information corresponds with the transaction details
        </p>
    </div>
</body>

</html>
