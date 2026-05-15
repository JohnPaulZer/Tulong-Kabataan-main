<?php

namespace App\Services\Verification;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * Local (no-API) image quality checks. Runs BEFORE any external OCR / KYC
 * call so we can short-circuit obviously bad uploads without burning a
 * provider quota and without exposing API keys to garbage data.
 *
 * Algorithms used here are deliberately lightweight (built on top of
 * GD which ships with PHP) and operate on a sampled subset of pixels:
 *
 *   - Brightness  -> mean luminance over sampled pixels
 *   - Sharpness   -> variance of horizontal pixel deltas (Laplacian-like)
 *   - Resolution  -> width/height vs configured minimums
 *   - Aspect      -> simple sanity check that it looks like an ID, not a
 *                    portrait selfie or a tall receipt
 *
 * A "report" array is returned from analyze() and persisted on the
 * VerificationRequest so admins can see why something failed quality.
 */
class ImageQualityService
{
    public function analyze(string $absolutePath, ?UploadedFile $sourceFile = null): array
    {
        $cfg = (array) config('id_verification.quality', []);

        if (! function_exists('imagecreatefromstring') || ! is_file($absolutePath)) {
            // GD not available — degrade gracefully: don't block the upload.
            return [
                'passed'     => true,
                'reason'     => null,
                'metrics'    => [],
                'gd_missing' => true,
            ];
        }

        $imageData = @file_get_contents($absolutePath);
        if ($imageData === false) {
            return [
                'passed'  => false,
                'reason'  => 'Image could not be read.',
                'metrics' => [],
            ];
        }

        $image = @imagecreatefromstring($imageData);
        if (! $image) {
            return [
                'passed'  => false,
                'reason'  => 'Image is corrupt or in an unsupported format.',
                'metrics' => [],
            ];
        }

        try {
            $width  = imagesx($image);
            $height = imagesy($image);

            $brightness = $this->meanBrightness($image, $width, $height, (int) ($cfg['sample_pixels'] ?? 4000));
            $sharpness  = $this->sharpnessScore($image, $width, $height, (int) ($cfg['sample_pixels'] ?? 4000));
            $aspect     = $width > 0 ? $height / $width : 0;

            $metrics = [
                'width'      => $width,
                'height'     => $height,
                'brightness' => round($brightness, 2),
                'sharpness'  => round($sharpness, 2),
                'aspect'     => round($aspect, 3),
                'bytes'      => $sourceFile?->getSize() ?? strlen($imageData),
            ];

            $minW = (int) ($cfg['min_width'] ?? 400);
            $minH = (int) ($cfg['min_height'] ?? 250);

            if ($width < $minW || $height < $minH) {
                return $this->fail('Image resolution is too small to read clearly.', $metrics);
            }

            // ID cards are landscape-ish (~1.4–1.7 aspect). Allow a wide range
            // because users may photograph slightly off-axis.
            if ($aspect < 0.35 || $aspect > 2.2) {
                return $this->fail('Image does not look like an ID card.', $metrics);
            }

            $minBrightness = (int) ($cfg['min_brightness'] ?? 35);
            $maxBrightness = (int) ($cfg['max_brightness'] ?? 235);
            if ($brightness < $minBrightness) {
                return $this->fail('Image is too dark.', $metrics);
            }
            if ($brightness > $maxBrightness) {
                return $this->fail('Image is too bright or washed out.', $metrics);
            }

            $minSharpness = (float) ($cfg['min_sharpness'] ?? 12.0);
            if ($sharpness < $minSharpness) {
                return $this->fail('Image is too blurry.', $metrics);
            }

            return [
                'passed'  => true,
                'reason'  => null,
                'metrics' => $metrics,
            ];
        } catch (\Throwable $e) {
            Log::warning('[IdVerification] Image quality check failed', [
                'error' => $e::class,
            ]);
            // Don't block uploads on internal failures.
            return [
                'passed'  => true,
                'reason'  => null,
                'metrics' => [],
                'error'   => 'analysis_failed',
            ];
        } finally {
            if (is_resource($image) || $image instanceof \GdImage) {
                imagedestroy($image);
            }
        }
    }

    private function fail(string $reason, array $metrics): array
    {
        return [
            'passed'  => false,
            'reason'  => $reason,
            'metrics' => $metrics,
        ];
    }

    private function meanBrightness(\GdImage $image, int $width, int $height, int $samples): float
    {
        $total = 0.0;
        $count = 0;
        $samples = max(50, min($samples, $width * $height));

        for ($i = 0; $i < $samples; $i++) {
            $x = random_int(0, $width - 1);
            $y = random_int(0, $height - 1);
            $rgb = imagecolorat($image, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            // Standard luminance approximation
            $total += 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
            $count++;
        }

        return $count > 0 ? $total / $count : 0.0;
    }

    /**
     * Approximate sharpness as the variance of |luminance(x+1) - luminance(x)|
     * over sampled pixels. Higher = sharper. This is a lightweight proxy for
     * a Laplacian variance and works well enough to flag heavily blurred
     * uploads without requiring the imagick / opencv extensions.
     */
    private function sharpnessScore(\GdImage $image, int $width, int $height, int $samples): float
    {
        if ($width < 4 || $height < 4) {
            return 0.0;
        }

        $deltas = [];
        $samples = max(50, min($samples, ($width - 1) * $height));

        for ($i = 0; $i < $samples; $i++) {
            $x = random_int(0, $width - 2);
            $y = random_int(0, $height - 1);

            $a = imagecolorat($image, $x, $y);
            $b = imagecolorat($image, $x + 1, $y);

            $la = $this->luminance($a);
            $lb = $this->luminance($b);
            $deltas[] = abs($la - $lb);
        }

        if (empty($deltas)) {
            return 0.0;
        }

        $mean = array_sum($deltas) / count($deltas);
        $varianceSum = 0.0;
        foreach ($deltas as $d) {
            $varianceSum += ($d - $mean) ** 2;
        }

        return sqrt($varianceSum / count($deltas));
    }

    private function luminance(int $rgb): float
    {
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }
}
