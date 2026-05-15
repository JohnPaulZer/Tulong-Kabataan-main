<?php

namespace App\Services\Verification\Providers;

use App\Services\Verification\Contracts\VerificationProvider;
use App\Services\Verification\OcrTextParser;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Google Cloud Vision OCR provider — uses the public DOCUMENT_TEXT_DETECTION
 * feature. Free tier covers 1,000 image requests / month. Like OCR.Space,
 * this provider only extracts text — authenticity / liveness are NOT
 * available, so the orchestrator's rule-based fraud checks remain the
 * decisive layer.
 */
class GoogleVisionProvider implements VerificationProvider
{
    public function __construct(private OcrTextParser $parser)
    {
    }

    public function name(): string
    {
        return 'google_vision';
    }

    public function isConfigured(): bool
    {
        return ! empty(config('id_verification.providers.google_vision.api_key'));
    }

    public function supportsAuthenticity(): bool
    {
        return false;
    }

    public function verify(string $frontAbsolutePath, ?string $backAbsolutePath = null, array $context = []): array
    {
        $cfg = (array) config('id_verification.providers.google_vision');
        $apiKey = (string) ($cfg['api_key'] ?? '');

        if ($apiKey === '') {
            return $this->failure('Google Vision API key not configured.');
        }

        $images = [];
        foreach (array_filter([$frontAbsolutePath, $backAbsolutePath]) as $path) {
            if (! is_file($path)) {
                continue;
            }
            $bytes = @file_get_contents($path);
            if ($bytes === false) {
                continue;
            }
            $images[] = [
                'image' => ['content' => base64_encode($bytes)],
                'features' => [['type' => 'DOCUMENT_TEXT_DETECTION', 'maxResults' => 1]],
            ];
        }

        if (empty($images)) {
            return $this->failure('No images available to send to Google Vision.');
        }

        try {
            $response = Http::timeout((int) ($cfg['timeout'] ?? 30))
                ->post(
                    rtrim((string) $cfg['base_url'], '/') . '?key=' . urlencode($apiKey),
                    ['requests' => $images]
                );
        } catch (ConnectionException $e) {
            return $this->failure('Could not reach Google Vision.', $e);
        } catch (\Throwable $e) {
            return $this->failure('Google Vision request failed.', $e);
        }

        if (! $response->ok()) {
            Log::warning('[IdVerification][GoogleVision] HTTP error', ['status' => $response->status()]);
            return $this->failure('Google Vision returned an error.');
        }

        $payload = $response->json();
        $rawTextParts = [];
        foreach ((array) ($payload['responses'] ?? []) as $r) {
            if (isset($r['fullTextAnnotation']['text'])) {
                $rawTextParts[] = (string) $r['fullTextAnnotation']['text'];
            } elseif (isset($r['textAnnotations'][0]['description'])) {
                $rawTextParts[] = (string) $r['textAnnotations'][0]['description'];
            }
        }

        $rawText = trim(implode("\n", $rawTextParts));

        if ($rawText === '') {
            return $this->failure('No readable text found on the document.');
        }

        $idType = $this->parser->detectIdType($rawText);

        return [
            'success'      => true,
            'provider'     => $this->name(),
            'reference_id' => null,
            'raw_text'     => $rawText,
            'extracted'    => [
                'full_name'        => $this->parser->extractName($rawText, $idType),
                'birthdate'        => $this->parser->extractBirthdate($rawText),
                'id_number'        => $this->parser->extractIdNumber($rawText, $idType),
                'address'          => $this->parser->extractAddress($rawText),
                'expiration_date'  => $this->parser->extractExpirationDate($rawText),
                'sex'              => $this->parser->extractSex($rawText),
                'nationality'      => $this->parser->extractNationality($rawText),
                'id_type_detected' => $idType,
            ],
            'authenticity' => [],
            'raw'          => $payload,
            'error'        => null,
        ];
    }

    private function failure(string $error, ?\Throwable $e = null): array
    {
        if ($e) {
            Log::warning('[IdVerification][GoogleVision] failure', ['error' => $e::class]);
        }

        return [
            'success'      => false,
            'provider'     => $this->name(),
            'reference_id' => null,
            'raw_text'     => '',
            'extracted'    => [],
            'authenticity' => [],
            'raw'          => null,
            'error'        => $error,
        ];
    }
}
