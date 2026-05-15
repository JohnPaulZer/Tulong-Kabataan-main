<?php

namespace App\Services\Verification;

use App\Models\User;
use App\Models\VerificationRequest;
use Carbon\Carbon;

/**
 * Computes a 0-100 confidence score for an ID submission by comparing
 * extracted OCR fields against the user's registered profile + the data
 * they typed into the verification form.
 *
 * Scoring weights (configurable via config/id_verification.php):
 *   - Name match           : 35 pts (fuzzy similarity vs typed + registered)
 *   - Birthdate match      : 25 pts (must match within a small window)
 *   - ID type match        : 15 pts (selected vs detected)
 *   - Address partial match: 10 pts (substring / token overlap)
 *   - Image quality passed : 10 pts
 *   - Expiry valid (DL)    :  5 pts (driver's license expiry > today)
 *
 * The breakdown returned alongside the score is persisted on the
 * VerificationRequest so admins can see exactly why a submission scored
 * the way it did.
 */
class IdScoringService
{
    public function score(
        VerificationRequest $request,
        ?User $user,
        array $providerResult,
        array $imageQuality
    ): array {
        $weights = (array) config('id_verification.scoring.weights', []);
        $extracted = (array) ($providerResult['extracted'] ?? []);

        $breakdown = [];
        $reasons   = [];
        $total     = 0;

        // -------------------------------------------------------------
        // Name (35 pts) — compare extracted full name to typed full name,
        // taking the better of typed-vs-extracted and registered-vs-extracted.
        // -------------------------------------------------------------
        $weight = (int) ($weights['name'] ?? 35);
        $typedName = trim(implode(' ', array_filter([
            $request->first_name ?? null,
            $request->middle_name ?? null,
            $request->last_name ?? null,
        ])));
        $registeredName = $user
            ? trim((string) ($user->first_name . ' ' . $user->last_name))
            : '';
        $extractedName = (string) ($extracted['full_name'] ?? '');

        $nameSim = max(
            $this->similarity($extractedName, $typedName),
            $this->similarity($extractedName, $registeredName),
        );
        $namePoints = (int) round($weight * $nameSim);
        if ($extractedName === '') {
            $reasons[] = 'Name could not be read from the ID.';
        } elseif ($nameSim < 0.6) {
            $reasons[] = 'Name on the ID does not closely match the registered profile.';
        }
        $total += $namePoints;
        $breakdown['name'] = ['weight' => $weight, 'points' => $namePoints, 'similarity' => round($nameSim, 3)];

        // -------------------------------------------------------------
        // Birthdate (25 pts) — exact match (date-only) or no points.
        // -------------------------------------------------------------
        $weight = (int) ($weights['birthdate'] ?? 25);
        $extractedDob = $this->toDate($extracted['birthdate'] ?? null);
        $typedDob = $this->toDate($request->dob);
        $registeredDob = $user ? $this->toDate($user->birthday ?? null) : null;

        $dobMatch = false;
        if ($extractedDob && ($typedDob || $registeredDob)) {
            $dobMatch = ($typedDob && $extractedDob->isSameDay($typedDob))
                || ($registeredDob && $extractedDob->isSameDay($registeredDob));
        }
        $dobPoints = $dobMatch ? $weight : 0;
        if (! $extractedDob) {
            $reasons[] = 'Birthdate could not be read from the ID.';
        } elseif (! $dobMatch) {
            $reasons[] = 'Birthdate on the ID does not match the registered profile.';
        }
        $total += $dobPoints;
        $breakdown['birthdate'] = ['weight' => $weight, 'points' => $dobPoints, 'matched' => $dobMatch];

        // -------------------------------------------------------------
        // ID type match (15 pts) — selected vs detected.
        // -------------------------------------------------------------
        $weight = (int) ($weights['id_type'] ?? 15);
        $detected = $extracted['id_type_detected'] ?? null;
        $selected = $request->id_type;
        $idTypeMatch = $detected !== null && $selected !== null && $detected === $selected;
        $idTypePoints = $idTypeMatch ? $weight : 0;
        if ($detected === OcrTextParser::TYPE_UNKNOWN || $detected === null) {
            $reasons[] = 'ID type could not be confidently detected.';
        } elseif (! $idTypeMatch) {
            $reasons[] = 'Detected ID type ('. $detected .') does not match the selected ID type ('. $selected .').';
        }
        $total += $idTypePoints;
        $breakdown['id_type'] = ['weight' => $weight, 'points' => $idTypePoints, 'detected' => $detected, 'selected' => $selected];

        // -------------------------------------------------------------
        // Address (10 pts) — partial / token overlap (PhilSys often
        // doesn't print address; treat missing as neutral).
        // -------------------------------------------------------------
        $weight = (int) ($weights['address'] ?? 10);
        $typedAddress = (string) ($request->address ?? '');
        $extractedAddress = (string) ($extracted['address'] ?? '');
        if ($typedAddress === '' || $extractedAddress === '') {
            $addressPoints = 0; // not penalized, just no bonus
            $addressSim = null;
        } else {
            $addressSim = $this->tokenOverlap($extractedAddress, $typedAddress);
            $addressPoints = (int) round($weight * $addressSim);
            if ($addressSim < 0.4) {
                $reasons[] = 'Address on the ID does not match the registered address.';
            }
        }
        $total += $addressPoints;
        $breakdown['address'] = ['weight' => $weight, 'points' => $addressPoints, 'similarity' => $addressSim];

        // -------------------------------------------------------------
        // Image quality (10 pts) — full points if quality check passed.
        // -------------------------------------------------------------
        $weight = (int) ($weights['image_quality'] ?? 10);
        $qualityPoints = ! empty($imageQuality['passed']) ? $weight : 0;
        $total += $qualityPoints;
        $breakdown['image_quality'] = [
            'weight' => $weight,
            'points' => $qualityPoints,
            'passed' => (bool) ($imageQuality['passed'] ?? false),
        ];

        // -------------------------------------------------------------
        // Expiry validity (5 pts, drivers license only).
        // -------------------------------------------------------------
        $weight = (int) ($weights['expiry_valid'] ?? 5);
        $expiryPoints = 0;
        $expiryMatch = null;
        if ($request->id_type === OcrTextParser::TYPE_DRIVERS_LICENSE) {
            $extractedExpiry = $this->toDate($extracted['expiration_date'] ?? null);
            $typedExpiry = $this->toDate($request->id_expiry ?? null);
            $effectiveExpiry = $extractedExpiry ?? $typedExpiry;

            if ($effectiveExpiry) {
                if ($effectiveExpiry->isFuture()) {
                    $expiryPoints = $weight;
                    $expiryMatch = true;
                } else {
                    $expiryMatch = false;
                    $reasons[] = 'Driver\'s license has expired.';
                }
            }
        }
        $total += $expiryPoints;
        $breakdown['expiry'] = ['weight' => $weight, 'points' => $expiryPoints, 'match' => $expiryMatch];

        // Authenticity bonus (KYC providers like Didit) — capped so that
        // full score requires the per-check signals to pass too.
        $authenticity = $providerResult['authenticity'] ?? [];
        $authenticityNotes = [];
        if (! empty($authenticity['verified'])) {
            $authenticityNotes[] = 'Provider verified document authenticity.';
        }
        if (isset($authenticity['liveness']) && $authenticity['liveness'] === false) {
            $reasons[] = 'Liveness check failed.';
        }
        if (isset($authenticity['face_match']) && $authenticity['face_match'] === false) {
            $reasons[] = 'Face match check failed.';
        }
        $breakdown['authenticity'] = $authenticity;
        if ($authenticityNotes) {
            $breakdown['authenticity_notes'] = $authenticityNotes;
        }

        // Cap at 100
        $total = max(0, min(100, $total));

        return [
            'score'     => $total,
            'breakdown' => $breakdown,
            'reasons'   => $reasons,
        ];
    }

    /**
     * Levenshtein-based similarity normalized to 0..1 with token-set
     * preprocessing. Symmetric and case-insensitive. Handles small OCR
     * mistakes (O/0, I/1, S/5) by mapping characters before comparing.
     */
    public function similarity(string $a, string $b): float
    {
        $a = $this->canon($a);
        $b = $this->canon($b);
        if ($a === '' || $b === '') {
            return 0.0;
        }
        if ($a === $b) {
            return 1.0;
        }

        $maxLen = max(strlen($a), strlen($b));
        if ($maxLen === 0) {
            return 0.0;
        }

        // levenshtein chokes on long strings — limit to 255 chars.
        $a = substr($a, 0, 255);
        $b = substr($b, 0, 255);

        $distance = levenshtein($a, $b);
        $sim = 1.0 - ($distance / $maxLen);

        // Token-set bonus: same tokens, different order, should score high
        $tokenSim = $this->tokenOverlap($a, $b);
        return max($sim, $tokenSim);
    }

    public function tokenOverlap(string $a, string $b): float
    {
        $tokens = function (string $s): array {
            $s = $this->canon($s);
            $parts = preg_split('/\s+/', $s) ?: [];
            return array_values(array_unique(array_filter($parts, fn ($t) => mb_strlen($t) >= 2)));
        };

        $ta = $tokens($a);
        $tb = $tokens($b);
        if (! $ta || ! $tb) {
            return 0.0;
        }

        $intersect = array_intersect($ta, $tb);
        $union = array_unique(array_merge($ta, $tb));
        return count($union) === 0 ? 0.0 : count($intersect) / count($union);
    }

    private function canon(string $s): string
    {
        $s = OcrTextParser::normalize($s);
        // Conservative OCR repair (only inside numbers we'd already see in
        // the same context). For names we map 0->O / 1->I / 5->S.
        $s = preg_replace_callback('/\b\w*\d\w*\b/', function (array $m) {
            $word = $m[0];
            if (preg_match('/^[A-Z0-9\-]+$/', $word)) {
                return $word; // looks like an ID number — leave alone
            }
            return strtr($word, ['0' => 'O', '1' => 'I', '5' => 'S']);
        }, $s) ?? $s;
        return $s;
    }

    private function toDate(mixed $value): ?Carbon
    {
        if (! $value) {
            return null;
        }
        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
