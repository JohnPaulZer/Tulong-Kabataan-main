<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification | Tulong Kabataan</title>
    <link rel="icon" href="{{ page_media_url('site_favicon', asset('img/log2.png')) }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preload" as="image" href="{{ page_media_url('email_verification_background', asset('img/backlogin.png')) }}">
</head>

<body class="email-verified-page relative flex min-h-screen items-center justify-center overflow-hidden bg-cover bg-center bg-no-repeat px-4 py-8 font-body text-slate-950"
    style="--auth-bg: url('{{ page_media_url('email_verification_background', asset('img/backlogin.png')) }}');">
    <div class="absolute inset-0 bg-slate-950/25 backdrop-blur-[1px]"></div>
    <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-white/40 to-transparent"></div>

    @php
        $status = $status ?? 'success';
        $isSuccess = $status === 'success';
        $title = match ($status) {
            'expired' => 'Verification Link Expired',
            'already_used' => 'Verification Link Already Used',
            'invalid' => 'Verification Failed',
            default => 'Email Verified!',
        };
        $icon = $isSuccess ? 'ri-mail-check-line' : 'ri-error-warning-line';
        $accent = $isSuccess ? 'text-indigo-600' : 'text-amber-600';
        $box = $isSuccess
            ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
            : 'border-amber-200 bg-amber-50 text-amber-800';
        $message = $message ?? 'Your email has been successfully verified.';
    @endphp

    <main class="relative z-10 w-full max-w-[500px] rounded-2xl border border-white/70 bg-white/90 p-6 text-center shadow-[0_20px_60px_rgba(15,23,42,0.35)] backdrop-blur-md sm:p-10">
        <div class="mb-5 text-6xl {{ $accent }}">
            <i class="{{ $icon }}"></i>
        </div>

        <h1 class="mb-4 font-heading text-3xl font-bold tracking-normal text-slate-950">{{ $title }}</h1>

        <p class="mb-5 text-base leading-relaxed text-slate-600 sm:text-lg">
            {{ $message }}
        </p>

        <div class="mb-6 rounded-lg border px-4 py-3 text-sm font-medium {{ $box }}">
            @if ($isSuccess)
                Redirecting you back to Tulong Kabataan.
            @else
                Please request a fresh verification email from your account.
            @endif
        </div>

        <div class="flex flex-wrap justify-center gap-3">
            @auth
                <a href="{{ route('verification.notice') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-3 font-heading text-sm font-semibold text-white no-underline transition hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                    Go to Verification Page
                </a>
            @endauth

            <a href="{{ route('login.page') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-3 font-heading text-sm font-semibold text-slate-600 no-underline transition hover:border-indigo-300 hover:text-indigo-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                Log In
            </a>
        </div>
    </main>
</body>

</html>
