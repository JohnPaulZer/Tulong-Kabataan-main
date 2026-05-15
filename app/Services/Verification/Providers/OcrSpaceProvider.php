<?php

namespace App\Services\Verification\Providers;

use App\Services\Verification\Contracts\VerificationProvider;
use App\Services\Verification\OcrTextParser;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * OCR.Space free OCR provider. The free tier (no card required) gives
 * 25,000 requests/month and accepts files up to 1MB. Larger files are
 * downscaled before being sent (handled inside IdVerificationService
 * before the path reaches us).
 *
 * IMPORTANT: This provider only OCRs text — it CANNOT prove an ID is
 * authentic. The orchestrator + scoring service apply rule-based fraud
 * checks on top, and the configured thresholds keep auto-approval
 * conservative when this provider is in use.
 */
class OcrSpaceProvider implements VerificationProvider
{
    public function __construct(private OcrTextParser $parser)
    {
    }

    public function name(): string
    {
        return 'ocr_space';
    }

    public function isConfigured(): bool
    {
        return ! empty(config('id_verification.providers.ocr_space.api_key'));
    }

    public function supportsAuthenticity(): bool
    {
        return false;
    }

    public function verify(string $frontAbsolutePath, ?string $backAbsolutePath = null, array $context = []): array
    {
        $cfg = (array) config('id_verification.providers.ocr_space');
        $apiKey = (string) ($cfg['api_key'] ?? '');

        if ($apiKey === '') {
            return $this->failure('OCR.Space API key not configured.');
        }

        $rawTextParts = [];
        $rawPayload = [];
        $referenceId = null;

        foreach (array_filter([$frontAbsolutePath, $backAbsolutePath]) as $path) {
            if (! is_file($path)) {
                continue;
            }
            try {
                $response = Http::asMultipart()
                    ->withHeaders(['apikey' => $apiKey])
                    ->timeout((int) ($cfg['timeout'] ?? 30))
                    ->attach(
                        'file',
                        fopen($path, 'r'),
                        // OCR.Space inspects the filename extension to route
                        // the upload to the right parser. The temp files
                        // produced by IdVerificationService::prepareForProvider
                        // are JPEGs, so we always send a .jpg name even if
                        // the source path ended in .webp.
                        $this->filenameForUpload($path),
                    )
                    ->post((string) ($cfg['base_url'] ?? 'https://api.ocr.space/parse/image'), [
                        ['name' => 'language',                 'contents' => (string) ($cfg['language'] ?? 'eng')],
                        ['name' => 'isOverlayRequired',        'contents' => 'false'],
                        ['name' => 'OCREngine',                'contents' => (string) ($cfg['engine'] ?? 2)],
                        ['name' => 'scale',                    'contents' => 'true'],
                        ['name' => 'detectOrientation',        'contents' => 'true'],
                        ['name' => 'isTable',                  'contents' => 'false'],
                    ]);
            } catch (ConnectionException $e) {
                return $this->failure('Could not reach OCR provider.', $e);
            } catch (\Throwable $e) {
                return $this->failure('OCR provider request failed.', $e);
            }

            if (! $response->ok()) {
                Log::warning('[IdVerification][OCRSpace] HTTP error', [
                    'status' => $response->status(),
                ]);
                return $this->failure('OCR provider returned an error.');
            }

            $payload = $response->json();
            $rawPayload[] = $payload;
            $referenceId = $referenceId ?: ($payload['SearchablePDFURL'] ?? null);

            if (! empty($payload['IsErroredOnProcessing'])) {
                $errorMessages = $payload['ErrorMessage'] ?? [];
                $errorDetails = $payload['ErrorDetails'] ?? null;
                $detail = is_array($errorMessages) ? implode('; ', $errorMessages) : (string) $errorMessages;
                if ($errorDetails) {
                    $detail .= ' [' . (is_array($errorDetails) ? implode('; ', $errorDetails) : $errorDetails) . ']';
                }
                Log::warning('[IdVerification][OCRSpace] Processing error', [
                    'errors' => $detail,
                ]);
                return $this->failure('OCR provider could not process the image: ' . trim($detail));
            }

            foreach ((array) ($payload['ParsedResults'] ?? []) as $result) {
                $text = (string) ($result['ParsedText'] ?? '');
                if ($text !== '') {
                    $rawTextParts[] = $text;
                }
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
            'reference_id' => $referenceId,
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
            'raw'          => $rawPayload,
            'error'        => null,
        ];
    }

    private function failure(string $error, ?\Throwable $e = null): array
    {
        if ($e) {
            Log::warning('[IdVerification][OCRSpace] failure', ['error' => $e::class]);
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

    /**
     * OCR.Space picks a parser based on the uploaded filename's extension.
     * Re-encoded temp files come from tempnam() and have no extension, so
     * we sniff the actual format and add the matching one.
     */
    private function filenameForUpload(string $path): string
    {
        $base = basename($path);
        if (preg_match('/\.(jpe?g|png|gif|bmp|tiff?|pdf)$/i', $base)) {
            return $base;
        }

        $h = @fopen($path, 'rb');
        $header = $h ? (fread($h, 12) ?: '') : '';
        if ($h) {
            fclose($h);
        }

        $ext = match (true) {
            str_starts_with($header, "\xFF\xD8\xFF")              => 'jpg',
            str_starts_with($header, "\x89PNG\r\n\x1A\n")         => 'png',
            str_starts_with($header, 'GIF8')                       => 'gif',
            str_starts_with($header, '%PDF-')                      => 'pdf',
            default                                                 => 'jpg',
        };

        return $base . '.' . $ext;
    }
}
