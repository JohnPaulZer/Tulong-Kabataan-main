<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified | Tulong Kabataan</title>
    <link rel="icon" href="{{ page_media_url('site_favicon', asset('img/log2.png')) }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preload" as="image" href="{{ page_media_url('email_verification_background', asset('img/backlogin.png')) }}">
</head>

<body class="email-verified-page relative flex min-h-screen items-center justify-center overflow-hidden bg-cover bg-center bg-no-repeat px-4 py-8 font-body text-slate-950"
    style="--auth-bg: url('{{ page_media_url('email_verification_background', asset('img/backlogin.png')) }}');">
    <div class="absolute inset-0 bg-slate-950/25 backdrop-blur-[1px]"></div>
    <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-white/40 to-transparent"></div>

    <main class="relative z-10 w-full max-w-[500px] rounded-2xl border border-white/70 bg-white/90 p-6 text-center shadow-[0_20px_60px_rgba(15,23,42,0.35)] backdrop-blur-md sm:p-10">
        <div class="mb-5 text-6xl text-indigo-600">
            <i class="ri-mail-check-line"></i>
        </div>

        <h1 class="mb-4 font-heading text-3xl font-bold tracking-normal text-slate-950">Email Verified!</h1>

        <p class="mb-5 text-base leading-relaxed text-slate-600 sm:text-lg">
            Your email has been successfully verified.<br>
            You can safely return to the app or close this tab.
        </p>

        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            The main app tab will automatically redirect if it was open.
        </div>
    </main>
</body>

</html>
