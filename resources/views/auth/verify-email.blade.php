<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Email | Tulong Kabataan</title>
    <link rel="icon" href="{{ page_media_url('site_favicon', asset('img/log2.png')) }}" type="image/png">
    <link rel="preload" as="image" href="{{ page_media_url('email_verification_background', asset('img/backlogin.png')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="verify-email-page relative flex min-h-screen items-center justify-center overflow-hidden bg-cover bg-center bg-no-repeat px-4 py-8 font-body text-slate-950"
    style="--auth-bg: url('{{ page_media_url('email_verification_background', asset('img/backlogin.png')) }}');">
    @include('administrator.partials.loading-screen')

    <div class="absolute inset-0 bg-slate-950/25 backdrop-blur-[1px]"></div>
    <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-white/40 to-transparent"></div>

    <main class="relative z-10 w-full max-w-[500px] rounded-2xl border border-white/70 bg-white/90 p-6 text-center shadow-[0_20px_60px_rgba(15,23,42,0.35)] backdrop-blur-md sm:p-10">
        <div class="mb-5 text-6xl text-indigo-600">
            <i class="ri-mail-send-line"></i>
        </div>
        
        <h1 class="mb-4 font-heading text-3xl font-bold tracking-normal text-slate-950">Verify Your Email</h1>
        
        @if (session('message'))
            <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('message') }}
            </div>
        @endif

        @if (session('mail_error'))
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                {{ session('mail_error') }}
            </div>
        @endif

        <p class="mb-7 text-base leading-relaxed text-slate-600 sm:text-lg">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </p>

        <div class="mt-5 hidden font-semibold text-indigo-600" id="loadingMessage">
            <i class="ri-loader-4-line"></i>
            Verifying your email...
        </div>

        <div id="buttonContainer" class="flex flex-wrap justify-center gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-3 font-heading text-sm font-semibold text-white transition hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                    RESEND VERIFICATION EMAIL
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" data-logout-confirm>
                @csrf
                <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-5 py-3 font-heading text-sm font-semibold text-slate-600 transition hover:border-indigo-300 hover:text-indigo-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                    <i class="ri-logout-box-r-line"></i>
                    Log Out
                </button>
            </form>
        </div>
    </main>

    @include('partials.logout-confirm-modal')

    <script>
        // Check if user has verified their email every 2 seconds
        let checkInterval;
        
        function checkEmailVerification() {
            fetch('/check-verification-status', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.verified) {
                    // Stop checking
                    clearInterval(checkInterval);
                    
                    // Show loading message
                    document.getElementById('buttonContainer').style.display = 'none';
                    document.getElementById('loadingMessage').style.display = 'block';
                    
                    // Redirect to landing page after a short delay
                    setTimeout(() => {
                        window.location.replace('{{ route('landpage') }}');
                    }, 1500);
                }
            })
            .catch(error => {
                console.log('Checking verification status...');
            });
        }
        
        // Check immediately when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Check if coming from verification link (has verified session)
            @if(session('verified'))
                // Show loading immediately
                document.getElementById('buttonContainer').style.display = 'none';
                document.getElementById('loadingMessage').style.display = 'block';
                
                // Redirect to landing page
                setTimeout(() => {
                    window.location.replace('{{ route('landpage') }}');
                }, 1500);
                return;
            @endif

            // Otherwise start normal polling
            checkEmailVerification();
            checkInterval = setInterval(checkEmailVerification, 2000);
        });

        // Stop checking when user leaves the page
        window.addEventListener('beforeunload', function() {
            if (checkInterval) {
                clearInterval(checkInterval);
            }
        });

        // Handle visibility change (when user switches tabs)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                if (checkInterval) {
                    clearInterval(checkInterval);
                }
            } else {
                // Resume checking when user comes back to tab
                @if(!session('verified'))
                    checkEmailVerification();
                    checkInterval = setInterval(checkEmailVerification, 2000);
                @endif
            }
        });
    </script>
</body>
</html>
