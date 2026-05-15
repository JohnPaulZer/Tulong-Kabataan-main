<?php

namespace App\Services\Verification;

use App\Models\VerificationRequest;
use Carbon\Carbon;

/**
 * Rule-based fraud / authenticity checks. These run AFTER OCR so we can
 * spot suspicious text content (sample/specimen/template), duplicate
 * uploads (by image hash or ID number), and obviously expired IDs.
 *
 * Each check produces zero or more "warnings" — short, actionable
 * strings that bubble up to the admin UI and the audit log. Severity is
 * encoded in the warning shape:
 *
 *   ['severity' => 'reject'|'manual_review'|'info', 'reason' => '...']
 *
 * The orchestrator inspects the highest severity to map to a final
 * decision: any 'reject' wins, otherwise any 'manual_review' wins.
 */
class IdFraudCheckService
{
    public const SEVERITY_REJECT = 'reject';
    public const SEVERITY_MANUAL = 'manual_review';
    public const SEVERITY_INFO   = 'info';

    public function __construct(private OcrTextParser $parser)
    {
    }

    public function evaluate(
        VerificationRequest $request,
        array $providerResult,
        ?string $fileHash,
        ?string $userId,
    ): array {
        $warnings = [];

        $rawText = (string) ($providerResult['raw_text'] ?? '');
        $extracted = (array) ($providerResult['extracted'] ?? []);
        $idTypeDetected = $extracted['id_type_detected'] ?? null;

        // 1) Sample/template/specimen keywords — strong signal of a non-real ID.
        $hits = $this->parser->findFraudKeywords($rawText);
        if ($hits) {
            $warnings[] = [
                'severity' => self::SEVERITY_REJECT,
                'reason'   => 'Document contains keywords that suggest it is a sample, template, or specimen ('
                    . implode(', ', $hits) . ').',
                'tag'      => 'fraud_keyword',
            ];
        }

        // 2) Browser / web screenshot indicators inside the OCR text. These
        // are tokens that show up when someone uploads a screenshot of a
        // Google Images result rather than a real ID photo.
        $screenshotMarkers = [
            'images.google',
            'google search',
            'all images',
            'safe search',
            'shutterstock',
            'getty images',
            'alamy',
            'imgur',
            'screenshot',
        ];
        $haystack = strtolower($rawText);
        $screenshotHits = [];
        foreach ($screenshotMarkers as $m) {
            if (str_contains($haystack, $m)) {
                $screenshotHits[] = $m;
            }
        }
        if ($screenshotHits) {
            $warnings[] = [
                'severity' => self::SEVERITY_REJECT,
                'reason'   => 'Image looks like a screenshot from a web page, not a real ID photo.',
                'tag'      => 'screenshot',
            ];
        }

        // 3) Duplicate image hash — if the same file bytes were already
        // uploaded by another user, it's almost certainly a fake.
        if ($fileHash) {
            $duplicateImage = VerificationRequest::where('id_front_hash', $fileHash)
                ->when($userId, fn ($q) => $q->where('user_id', '!=', $userId))
                ->first();
            if ($duplicateImage) {
                $warnings[] = [
                    'severity' => self::SEVERITY_REJECT,
                    'reason'   => 'This exact ID image has already been used by another account.',
                    'tag'      => 'duplicate_image',
                ];
            }
        }

        // 4) Duplicate ID number — covers the case where the image is
        // different but the same person/number is being recycled.
        $extractedIdNumber = (string) ($extracted['id_number'] ?? '');
        if ($extractedIdNumber !== '') {
            $hash = hash('sha256', $extractedIdNumber);
            $duplicateNumber = VerificationRequest::where('id_number_hash', $hash)
                ->when($userId, fn ($q) => $q->where('user_id', '!=', $userId))
                ->where('status', VerificationRequest::STATUS_APPROVED)
                ->first();
            if ($duplicateNumber) {
                $warnings[] = [
                    'severity' => self::SEVERITY_REJECT,
                    'reason'   => 'This ID number is already linked to a verified account.',
                    'tag'      => 'duplicate_number',
                ];
            }
        }

        // 5) Expired driver's license.
        if ($request->id_type === OcrTextParser::TYPE_DRIVERS_LICENSE) {
            $expiry = $extracted['expiration_date'] ?? $request->id_expiry ?? null;
            if ($expiry) {
                try {
                    $expiryDate = Carbon::parse($expiry);
                    if ($expiryDate->isPast()) {
                        $warnings[] = [
                            'severity' => self::SEVERITY_REJECT,
                            'reason'   => 'Driver\'s license is expired (expired on ' . $expiryDate->format('M d, Y') . ').',
                            'tag'      => 'expired',
                        ];
                    }
                } catch (\Throwable) {
                    // Bad date — handled elsewhere.
                }
            }
        }

        // 6) Missing ID number but document otherwise looks valid.
        if ($extractedIdNumber === '' && $idTypeDetected && $idTypeDetected !== OcrTextParser::TYPE_UNKNOWN) {
            $warnings[] = [
                'severity' => self::SEVERITY_MANUAL,
                'reason'   => 'ID number could not be read clearly — admin review recommended.',
                'tag'      => 'missing_id_number',
            ];
        }

        // 7) Unknown ID type — let admin look at it.
        if ($idTypeDetected === OcrTextParser::TYPE_UNKNOWN || $idTypeDetected === null) {
            $warnings[] = [
                'severity' => self::SEVERITY_MANUAL,
                'reason'   => 'ID type could not be confidently detected from the photo.',
                'tag'      => 'unknown_id_type',
            ];
        }

        // 8) Incomplete OCR (very little text returned).
        if (mb_strlen(trim($rawText)) > 0 && mb_strlen(trim($rawText)) < 30) {
            $warnings[] = [
                'severity' => self::SEVERITY_MANUAL,
                'reason'   => 'Very little text was readable from the document — admin review recommended.',
                'tag'      => 'incomplete_ocr',
            ];
        }

        return $warnings;
    }

    /**
     * Highest severity present in the warnings array. Reject > Manual > Info.
     */
    public static function highestSeverity(array $warnings): ?string
    {
        $order = [self::SEVERITY_INFO => 0, self::SEVERITY_MANUAL => 1, self::SEVERITY_REJECT => 2];
        $current = null;
        foreach ($warnings as $w) {
            $sev = $w['severity'] ?? null;
            if (! isset($order[$sev])) {
                continue;
            }
            if ($current === null || $order[$sev] > $order[$current]) {
                $current = $sev;
            }
        }
        return $current;
    }

    /**
     * Pull just the user-friendly reasons (no severity tag).
     */
    public static function reasons(array $warnings): array
    {
        return array_values(array_map(fn ($w) => (string) ($w['reason'] ?? ''), $warnings));
    }
}
