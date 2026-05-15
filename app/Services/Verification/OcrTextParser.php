<?php

namespace App\Services\Verification;

/**
 * Pure parsing logic for OCR text returned by any provider. Keeps the
 * provider classes thin and lets us unit-test text extraction without
 * making HTTP calls.
 *
 * The methods here are intentionally conservative: when a value cannot
 * be confidently extracted we return null and let the scoring service
 * fall back to "no data" rather than guessing. Bad data is worse than
 * no data because it pollutes the match score.
 */
class OcrTextParser
{
    public const TYPE_PHILID = 'philid';
    public const TYPE_DRIVERS_LICENSE = 'drivers_license';
    public const TYPE_UNKNOWN = 'unknown';

    public function detectIdType(string $rawText): string
    {
        $haystack = strtolower($rawText);
        $patterns = (array) config('id_verification.id_type_patterns', []);

        $score = ['philid' => 0, 'drivers_license' => 0];

        foreach ($patterns as $type => $needles) {
            foreach ((array) $needles as $needle) {
                if ($needle !== '' && str_contains($haystack, strtolower($needle))) {
                    $score[$type] = ($score[$type] ?? 0) + 1;
                }
            }
        }

        if ($score['philid'] === 0 && $score['drivers_license'] === 0) {
            return self::TYPE_UNKNOWN;
        }

        return $score['drivers_license'] > $score['philid']
            ? self::TYPE_DRIVERS_LICENSE
            : self::TYPE_PHILID;
    }

    /**
     * Normalize a piece of OCR text for comparison: uppercased, ASCII-ish,
     * collapsed whitespace, common OCR confusions handled conservatively.
     */
    public static function normalize(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $v = (string) $value;
        // Strip diacritics
        $v = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $v) ?: $v;
        $v = strtoupper($v);
        // Remove non-alphanumeric except space and dash
        $v = preg_replace('/[^A-Z0-9\-\s]/', ' ', $v) ?? $v;
        $v = preg_replace('/\s+/', ' ', $v) ?? $v;
        return trim($v);
    }

    /**
     * Try to pull a full name out of OCR text. PhilSys IDs put the name
     * after labels like "Last Name" / "Apelyido". Driver's licenses have
     * "Last Name, First Name, Middle Name" lines. We don't try to be
     * clever — better to return null than the wrong name.
     */
    public function extractName(string $rawText, ?string $idType = null): ?string
    {
        $lines = $this->lines($rawText);

        // Each row holds every Tagalog AND English variant for a single
        // logical field. The PhilSys card prints both at once, e.g.
        //   "Apelyido/Last Name"
        //   "Mga Pangalan/Given Names"
        //   "Gitnang Apelyido/Middle Name"
        $surnameLabels = ['last name', 'apelyido', 'surname', 'family name'];
        $givenLabels   = [
            'first name', 'first names', 'given name', 'given names',
            'pangalan', 'mga pangalan', 'pangalan/given names', 'given',
        ];
        $middleLabels  = [
            'middle name', 'middle names', 'middle initial',
            'gitnang pangalan', 'gitnang apelyido', 'gitnang',
        ];

        // Common header / card-title labels we should never mistake for a
        // name. PhilSys cards print "PAMBANSANG PAGKAKAKILANLAN" prominently
        // and our old longest-line fallback was happily grabbing it.
        $labelTokens = [
            'date of birth', 'birth date', 'birthday', 'kapanganakan', 'date issued',
            'expiration date', 'expiry date', 'sex', 'kasarian', 'nationality', 'address',
            'tirahan', 'place of birth', 'philsys', 'philid', 'national id', 'pcn', 'psn',
            'last name', 'first name', 'middle name', 'apelyido', 'pangalan',
            'mga pangalan', 'gitnang apelyido', 'gitnang pangalan',
            'given name', 'given names', 'restriction', 'license no',
            'driver\'s license', 'drivers license', 'land transportation office',
            'republic of the philippines', 'republika ng pilipinas',
            'philippine identification', 'philippine identification system',
            'philippine identification card', 'pambansang pagkakakilanlan',
            'pambansang', 'pagkakakilanlan',
            'petsa ng kapanganakan', 'petsa', 'kapanganakan/date of birth',
        ];

        $isLabel = function (string $text) use ($labelTokens): bool {
            $lc = strtolower(trim($text));
            // also normalize "/", "\" and double spaces
            $lc = preg_replace('/[\\/\\\\]+/', '/', $lc) ?? $lc;
            $lc = preg_replace('/\s+/', ' ', $lc) ?? $lc;
            foreach ($labelTokens as $needle) {
                if ($lc === $needle || str_starts_with($lc, $needle . ' ')
                    || str_starts_with($lc, $needle . '/')
                    || str_starts_with($lc, $needle . ':')) {
                    return true;
                }
                // Tagalog/English bilingual: "apelyido/last name"
                if (str_contains($lc, $needle . '/')) {
                    return true;
                }
            }
            return false;
        };

        // Tokenize a label line into the labels it contains. Handles
        // bilingual pairs split by "/" or "|", and the ":" / "-" inline
        // form. Returns the lowercased label tokens.
        $labelsOnLine = function (string $line): array {
            $lc = strtolower(trim($line));
            // Drop trailing colon and any inline value after it (handled below).
            $bare = $lc;
            if (str_contains($bare, ':')) {
                $bare = trim(strstr($bare, ':', true));
            }
            $parts = preg_split('/[\\/|]+/u', $bare) ?: [$bare];
            return array_values(array_filter(array_map(fn ($p) => trim($p), $parts), fn ($p) => $p !== ''));
        };

        $matchesAny = function (array $tokens, array $labelSet): bool {
            foreach ($tokens as $t) {
                if (in_array($t, $labelSet, true)) {
                    return true;
                }
            }
            return false;
        };

        // Strategy 1: collect surname, given, middle from labelled lines.
        $captured = ['surname' => null, 'given' => null, 'middle' => null];
        foreach ($lines as $i => $line) {
            $next = trim($lines[$i + 1] ?? '');

            // Inline form: "Last Name: DOE" (still useful for some OCR layouts)
            if (preg_match('/^(.+?)\s*:\s*(.+)$/u', $line, $m)) {
                $labelPart = strtolower(trim($m[1]));
                // Bilingual labels on the same side of the colon, e.g.
                // "Apelyido/Last Name: DOE" — split on "/".
                $labelTokens2 = preg_split('/[\\/|]+/u', $labelPart) ?: [$labelPart];
                $labelTokens2 = array_map('trim', $labelTokens2);
                $valuePart = trim($m[2]);

                if (! $isLabel($valuePart)) {
                    if ($matchesAny($labelTokens2, $surnameLabels)) {
                        $captured['surname'] = $captured['surname'] ?? $valuePart;
                    }
                    if ($matchesAny($labelTokens2, $givenLabels)) {
                        $captured['given'] = $captured['given'] ?? $valuePart;
                    }
                    if ($matchesAny($labelTokens2, $middleLabels)) {
                        $captured['middle'] = $captured['middle'] ?? $valuePart;
                    }
                }
            }

            // Bilingual label-only line: next line is the value.
            // Example: "Apelyido/Last Name" \n "ALBANIA"
            $tokens = $labelsOnLine($line);
            if ($tokens && $next !== '' && ! $isLabel($next) && $this->looksLikeNameToken($next)) {
                if ($matchesAny($tokens, $surnameLabels)) {
                    $captured['surname'] = $captured['surname'] ?? $next;
                }
                if ($matchesAny($tokens, $givenLabels)) {
                    $captured['given'] = $captured['given'] ?? $next;
                }
                if ($matchesAny($tokens, $middleLabels)) {
                    $captured['middle'] = $captured['middle'] ?? $next;
                }
            }
        }

        $assembled = trim(implode(' ', array_filter([
            $captured['given'],
            $captured['middle'],
            $captured['surname'],
        ])));
        if ($assembled !== '' && $this->looksLikeName($assembled)) {
            return $assembled;
        }
        // If at least surname + given were found we trust them even when
        // the assembled string is short (e.g. one-token first name).
        if ($captured['given'] && $captured['surname']) {
            return trim($captured['given'] . ' '
                . ($captured['middle'] ? $captured['middle'] . ' ' : '')
                . $captured['surname']);
        }

        // Strategy 2: longest line of mostly uppercase letters that isn't an
        // obvious header/label. Skips the card title now that it's in the
        // blocklist above.
        $best = null;
        foreach ($lines as $line) {
            $t = trim($line);
            if (mb_strlen($t) < 6 || mb_strlen($t) > 80) {
                continue;
            }
            if ($isLabel($t)) {
                continue;
            }
            if (! $this->looksLikeName($t)) {
                continue;
            }
            if ($best === null || mb_strlen($t) > mb_strlen($best)) {
                $best = $t;
            }
        }

        return $best;
    }

    public function extractBirthdate(string $rawText): ?string
    {
        return $this->extractDateNear($rawText, [
            'date of birth', 'birth date', 'kapanganakan', 'dob', 'b.date', 'birthday',
        ]);
    }

    public function extractExpirationDate(string $rawText): ?string
    {
        return $this->extractDateNear($rawText, [
            'expiration date', 'expiry date', 'exp date', 'expires', 'expiration',
        ]);
    }

    public function extractIdNumber(string $rawText, ?string $idType = null): ?string
    {
        $upper = strtoupper($rawText);

        // PhilSys PCN — 16 digits, often shown as 4-4-4-4 or 4-4-4
        if ($idType === self::TYPE_PHILID || $idType === null) {
            if (preg_match('/(\d{4}\s*-?\s*\d{4}\s*-?\s*\d{4}\s*-?\s*\d{4})/u', $upper, $m)) {
                return $this->cleanIdNumber($m[1]);
            }
            if (preg_match('/(\d{4}\s*-?\s*\d{4}\s*-?\s*\d{4})/u', $upper, $m)) {
                return $this->cleanIdNumber($m[1]);
            }
        }

        // Driver's License — letter + 2 digits + 2 digits + 6 digits, e.g. E12-23-000386
        if ($idType === self::TYPE_DRIVERS_LICENSE || $idType === null) {
            if (preg_match('/([A-Z])\s*(\d{2})\s*-?\s*(\d{2})\s*-?\s*(\d{6})/u', $upper, $m)) {
                return $m[1] . $m[2] . '-' . $m[3] . '-' . $m[4];
            }
        }

        return null;
    }

    public function extractAddress(string $rawText): ?string
    {
        $lines = $this->lines($rawText);
        $labels = ['address', 'tirahan', 'residential address'];

        foreach ($lines as $i => $line) {
            $lc = strtolower(trim($line));
            // Strip a leading "tirahan/", "address/" or vice-versa from the
            // label line itself when both are present (PhilSys: "Tirahan/Address").
            $cleanedLine = preg_replace('#^(?:tirahan|address|residential address)\s*/\s*(?:tirahan|address|residential address)\s*[:]?\s*#i', '', $line) ?? $line;

            foreach ($labels as $label) {
                if (str_starts_with($lc, $label) || str_contains($lc, '/' . $label) || str_contains($lc, $label . '/')) {
                    // Inline value after the label
                    $part = trim((string) preg_replace(
                        '/^(?:[\\/]?(?:tirahan|address|residential address)\s*[\\/]?)+\s*[:]?\s*/i',
                        '',
                        $cleanedLine
                    ));
                    $next = trim($lines[$i + 1] ?? '');
                    // If the line was JUST the label, take the next line.
                    $candidate = $part !== '' ? trim($part . ' ' . $next) : $next;
                    $candidate = trim((string) preg_replace('#^[/\\\\:\s]+#', '', $candidate));
                    if (mb_strlen($candidate) >= 8) {
                        return $candidate;
                    }
                }
            }
        }

        return null;
    }

    public function extractSex(string $rawText): ?string
    {
        if (preg_match('/(?:sex|kasarian|gender)\s*:?\s*([MF])\b/i', $rawText, $m)) {
            return strtoupper($m[1]);
        }
        return null;
    }

    public function extractNationality(string $rawText): ?string
    {
        if (preg_match('/(?:nationality|citizenship)\s*:?\s*([A-Za-z]+)/i', $rawText, $m)) {
            return strtoupper($m[1]);
        }
        return null;
    }

    /**
     * Detect tampering / fake IDs based on suspicious keywords.
     * Returns matched keywords (empty if clean).
     */
    public function findFraudKeywords(string $rawText): array
    {
        $haystack = strtolower($rawText);
        $hits = [];
        foreach ((array) config('id_verification.fraud_keywords', []) as $keyword) {
            $needle = strtolower((string) $keyword);
            if ($needle !== '' && str_contains($haystack, $needle)) {
                $hits[] = $keyword;
            }
        }
        return array_values(array_unique($hits));
    }

    // -----------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------

    private function lines(string $rawText): array
    {
        $cleaned = str_replace(["\r\n", "\r"], "\n", $rawText);
        return array_values(array_filter(array_map('trim', explode("\n", $cleaned)), fn ($l) => $l !== ''));
    }

    private function looksLikeName(string $text): bool
    {
        // 2+ words, mostly letters/spaces/dashes/periods, no digits
        if (preg_match('/\d/', $text)) {
            return false;
        }
        if (! preg_match('/^[A-Za-zÑñ\.\-\s,\']+$/u', $text)) {
            return false;
        }
        $words = preg_split('/\s+/', trim($text)) ?: [];
        return count($words) >= 2;
    }

    /**
     * A more permissive name-token check used when we already know the
     * preceding line was a "Last Name"-style label. Single-word values
     * like "DOE" or "DELA-CRUZ" are valid in that context.
     */
    private function looksLikeNameToken(string $text): bool
    {
        if (preg_match('/\d/', $text)) {
            return false;
        }
        return (bool) preg_match('/^[A-Za-zÑñ\.\-\s,\']{2,}$/u', $text);
    }

    private function extractDateNear(string $rawText, array $labels): ?string
    {
        $lines = $this->lines($rawText);

        foreach ($lines as $i => $line) {
            $lc = strtolower($line);
            foreach ($labels as $label) {
                if (str_contains($lc, $label)) {
                    // Try same-line, then next line
                    foreach ([$line, $lines[$i + 1] ?? ''] as $source) {
                        $iso = $this->parseDate($source);
                        if ($iso) {
                            return $iso;
                        }
                    }
                }
            }
        }

        // Fallback: any date in the text
        return $this->parseDate($rawText);
    }

    private function parseDate(string $text): ?string
    {
        // Patterns we accept: YYYY-MM-DD, YYYY/MM/DD, MM/DD/YYYY, DD-MM-YYYY,
        // and "MONTH DD, YYYY" / "DD MONTH YYYY"
        $patterns = [
            '/(\d{4})[\-\/](\d{1,2})[\-\/](\d{1,2})/'                                => 'ymd',
            '/(\d{1,2})[\-\/](\d{1,2})[\-\/](\d{4})/'                                => 'mdy',
            '/(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|SEPT|OCT|NOV|DEC)[A-Z\.]*\s+(\d{1,2})[,\s]+(\d{4})/i' => 'monthFirst',
            '/(\d{1,2})\s+(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|SEPT|OCT|NOV|DEC)[A-Z\.]*\s+(\d{4})/i'   => 'dayFirst',
        ];

        foreach ($patterns as $regex => $kind) {
            if (preg_match($regex, $text, $m)) {
                try {
                    return $this->coerceDate($kind, $m);
                } catch (\Throwable) {
                    continue;
                }
            }
        }

        return null;
    }

    private function coerceDate(string $kind, array $m): ?string
    {
        $months = [
            'JAN' => 1, 'FEB' => 2, 'MAR' => 3, 'APR' => 4, 'MAY' => 5, 'JUN' => 6,
            'JUL' => 7, 'AUG' => 8, 'SEP' => 9, 'SEPT' => 9, 'OCT' => 10, 'NOV' => 11, 'DEC' => 12,
        ];

        switch ($kind) {
            case 'ymd':
                $y = (int) $m[1]; $mo = (int) $m[2]; $d = (int) $m[3]; break;
            case 'mdy':
                // Heuristic: PH IDs use either MM/DD/YYYY or DD/MM/YYYY. We pick
                // MM/DD/YYYY first; if month > 12, swap.
                $mo = (int) $m[1]; $d = (int) $m[2]; $y = (int) $m[3];
                if ($mo > 12 && $d <= 12) { [$mo, $d] = [$d, $mo]; }
                break;
            case 'monthFirst':
                $mo = $months[strtoupper(substr($m[1], 0, 3))] ?? null;
                $d = (int) $m[2]; $y = (int) $m[3]; break;
            case 'dayFirst':
                $d = (int) $m[1]; $y = (int) $m[3];
                $mo = $months[strtoupper(substr($m[2], 0, 3))] ?? null;
                break;
            default:
                return null;
        }

        if (! $mo || ! checkdate($mo, $d, $y) || $y < 1900 || $y > 2100) {
            return null;
        }

        return sprintf('%04d-%02d-%02d', $y, $mo, $d);
    }

    private function cleanIdNumber(string $value): string
    {
        $compact = preg_replace('/[^0-9]/', '', $value) ?? '';
        if (strlen($compact) === 16) {
            return implode('-', str_split($compact, 4));
        }
        if (strlen($compact) === 12) {
            return implode('-', str_split($compact, 4));
        }
        return $compact;
    }
}
