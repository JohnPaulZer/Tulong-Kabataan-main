<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! config('security.headers.enabled', true)) {
            return $response;
        }

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self), payment=()');

        if (config('security.headers.csp_enabled', true)) {
            $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy());
        }

        if (
            config('security.headers.hsts_enabled', true) &&
            ($request->isSecure() || app()->environment('production'))
        ) {
            $maxAge = (int) config('security.headers.hsts_max_age', 31536000);
            $response->headers->set('Strict-Transport-Security', "max-age={$maxAge}; includeSubDomains; preload");
        }

        return $response;
    }

    private function contentSecurityPolicy(): string
    {
        $configured = trim((string) config('security.headers.csp', ''));
        if ($configured !== '') {
            return $configured;
        }

        $connectExtra = $this->tokens((string) config('security.headers.csp_extra_connect', ''));
        $imgExtra = $this->tokens((string) config('security.headers.csp_extra_img', ''));

        return implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "frame-ancestors 'none'",
            "object-src 'none'",
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://unpkg.com http://localhost:* http://127.0.0.1:*",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://unpkg.com http://localhost:* http://127.0.0.1:*",
            "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net",
            "img-src 'self' data: blob: https: http://localhost:* http://127.0.0.1:* {$imgExtra}",
            "connect-src 'self' https://nominatim.openstreetmap.org https://*.tile.openstreetmap.org http://localhost:* http://127.0.0.1:* ws://localhost:* ws://127.0.0.1:* {$connectExtra}",
            "media-src 'self' https:",
            "form-action 'self'",
        ]);
    }

    private function tokens(string $value): string
    {
        return collect(explode(',', $value))
            ->map(fn (string $token) => trim($token))
            ->filter()
            ->implode(' ');
    }
}
