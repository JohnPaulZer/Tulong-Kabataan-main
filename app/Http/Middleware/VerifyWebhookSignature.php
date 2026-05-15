<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = (string) config('security.webhooks.secret', '');

        if ($secret === '') {
            return response()->json([
                'message' => 'Webhook verification is not configured.',
            ], 403);
        }

        $signatureHeader = (string) config('security.webhooks.signature_header', 'X-TK-Signature');
        $timestampHeader = (string) config('security.webhooks.timestamp_header', 'X-TK-Timestamp');
        $signature = (string) $request->header($signatureHeader, '');
        $timestamp = (string) $request->header($timestampHeader, '');

        if (! ctype_digit($timestamp)) {
            return response()->json(['message' => 'Invalid webhook timestamp.'], 403);
        }

        $tolerance = (int) config('security.webhooks.tolerance_seconds', 300);
        if (abs(time() - (int) $timestamp) > $tolerance) {
            return response()->json(['message' => 'Expired webhook request.'], 403);
        }

        $payload = $timestamp . '.' . $request->getContent();
        $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($expected, $signature)) {
            return response()->json(['message' => 'Invalid webhook signature.'], 403);
        }

        return $next($request);
    }
}
