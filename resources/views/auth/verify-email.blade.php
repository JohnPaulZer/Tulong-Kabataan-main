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

        @if (session('error'))
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <div id="resendMessage" class="mb-5 hidden rounded-lg border px-4 py-3 text-sm font-medium"></div>

        @php
            $verificationEmailSent = filled(Auth::user()?->email_verification_sent_at);
        @endphp

        <p class="mb-7 text-base leading-relaxed text-slate-600 sm:text-lg">
            Thanks for signing up! Click the button below and we will send a secure verification link to {{ Auth::user()?->email }}. After that, open the email and click the link to verify your account.
        </p>

        <div class="mt-5 hidden font-semibold text-indigo-600" id="loadingMessage">
            <i class="ri-loader-4-line"></i>
            Verifying your email...
        </div>

        <div id="buttonContainer" class="flex flex-wrap justify-center gap-3">
            <form method="POST" action="{{ route('verification.send') }}" id="resendVerificationForm">
                @csrf
                <button type="submit" id="resendVerificationButton" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-3 font-heading text-sm font-semibold text-white transition hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:bg-slate-400 disabled:opacity-70">
                    <span id="resendVerificationLabel">{{ $verificationEmailSent ? 'Resend Verification Email' : 'Send Verification Email' }}</span>
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
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const resendForm = document.getElementById('resendVerificationForm');
        const resendButton = document.getElementById('resendVerificationButton');
        const resendLabel = document.getElementById('resendVerificationLabel');
        const resendMessage = document.getElementById('resendMessage');
        const buttonContainer = document.getElementById('buttonContainer');
        const loadingMessage = document.getElementById('loadingMessage');
        const CHECK_TIMEOUT_MS = 8000;
        const RESEND_TIMEOUT_MS = 15000;
        const defaultResendLabel = @json($verificationEmailSent ? 'Resend Verification Email' : 'Send Verification Email');
        let verificationEmailSent = @json($verificationEmailSent);
        let checkInterval;
        let checking = false;

        function fetchWithTimeout(url, options = {}, timeoutMs = CHECK_TIMEOUT_MS) {
            const controller = new AbortController();
            const timeout = window.setTimeout(() => controller.abort(), timeoutMs);

            return fetch(url, { ...options, signal: controller.signal })
                .finally(() => window.clearTimeout(timeout));
        }

        function showResendMessage(type, message) {
            if (!resendMessage) return;

            const styles = {
                success: 'border-emerald-200 bg-emerald-50 text-emerald-800',
                warning: 'border-amber-200 bg-amber-50 text-amber-800',
                error: 'border-red-200 bg-red-50 text-red-800',
            };

            resendMessage.className = `mb-5 rounded-lg border px-4 py-3 text-sm font-medium ${styles[type] || styles.error}`;
            resendMessage.textContent = message;
            resendMessage.classList.remove('hidden');
        }

        function redirectHomeSoon() {
            if (checkInterval) {
                clearInterval(checkInterval);
                checkInterval = null;
            }

            buttonContainer.style.display = 'none';
            loadingMessage.style.display = 'block';

            setTimeout(() => {
                window.location.replace('{{ route('landpage') }}');
            }, 1200);
        }

        async function checkEmailVerification() {
            if (checking || document.hidden) return;
            checking = true;

            try {
                const response = await fetchWithTimeout('{{ route('verification.check') }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin',
                });
                const data = await response.json();

                if (data.verified) {
                    redirectHomeSoon();
                }
            } catch (error) {
                // The page remains usable; the next poll or a manual refresh can recover.
            } finally {
                checking = false;
            }
        }

        resendForm?.addEventListener('submit', async (event) => {
            event.preventDefault();

            window.TKLoadingModal?.show();
            resendButton.disabled = true;
            resendLabel.textContent = 'Sending...';
            showResendMessage('warning', 'Sending a fresh verification email...');

            try {
                const response = await fetchWithTimeout(resendForm.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: new FormData(resendForm),
                    credentials: 'same-origin',
                }, RESEND_TIMEOUT_MS);
                const data = await response.json().catch(() => ({}));

                if (response.ok) {
                    verificationEmailSent = true;
                    showResendMessage('success', data.message || 'Verification link sent. Please check your inbox or spam folder.');
                    return;
                }

                if (response.status === 429) {
                    showResendMessage('error', data.message || 'Too many resend attempts. Please wait a moment before trying again.');
                } else {
                    showResendMessage('error', data.message || 'The verification email could not be sent. Please try again.');
                }
            } catch (error) {
                showResendMessage(
                    'warning',
                    error.name === 'AbortError'
                        ? 'Sending is taking longer than expected. Please wait a moment and check your inbox before trying again.'
                        : 'We could not reach the server. Please check your connection and try again.'
                );
            } finally {
                window.TKLoadingModal?.hide();
                resendButton.disabled = false;
                resendLabel.textContent = verificationEmailSent ? 'Resend Verification Email' : defaultResendLabel;
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            checkEmailVerification();
            checkInterval = setInterval(checkEmailVerification, 3000);
        });

        window.addEventListener('beforeunload', function() {
            if (checkInterval) {
                clearInterval(checkInterval);
                checkInterval = null;
            }
        });

        document.addEventListener('visibilitychange', function() {
            if (document.hidden && checkInterval) {
                clearInterval(checkInterval);
                checkInterval = null;
            } else if (!document.hidden && !checkInterval) {
                checkEmailVerification();
                checkInterval = setInterval(checkEmailVerification, 3000);
            }
        });
    </script>
</body>
</html>
