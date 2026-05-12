<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>Login | Tulong Kabataan</title>
    <link rel="icon" href="{{asset ('img/log2.png')}}" type="image/png">
    <link rel="preload" as="image" href="{{ asset('img/backlogin.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="login-page relative flex min-h-screen items-center justify-center overflow-hidden bg-[url('/img/backlogin.png')] bg-cover bg-center bg-no-repeat px-4 py-8 font-body text-slate-950"
    style="--auth-bg: url('{{ asset('img/backlogin.png') }}');">
    <div class="fixed inset-0 bg-slate-950/25 backdrop-blur-[1px]"></div>
    <div class="fixed inset-x-0 top-0 h-40 bg-gradient-to-b from-white/40 to-transparent"></div>

    <main class="relative z-10 w-full max-w-[430px] rounded-2xl border border-white/70 bg-white/90 p-6 shadow-[0_20px_60px_rgba(15,23,42,0.35)] backdrop-blur-md sm:p-8 lg:p-10">
        <div class="mb-6 flex justify-center">
            <a href="{{ route('landpage') }}" class="inline-flex rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-4">
                <img src="{{ asset('img/log.png') }}" alt="TKA Logo" class="h-[60px] w-auto">
            </a>
        </div>
        <h1 class="mb-6 text-center font-heading text-2xl font-bold tracking-normal text-slate-950">Log in to your account</h1>

        @if(session('error'))
            <div id="flash-error" class="error-box absolute -top-16 left-1/2 z-20 w-[calc(100%-2rem)] max-w-[380px] -translate-x-1/2 rounded-lg bg-red-500 px-5 py-3 text-center text-sm font-semibold text-white opacity-0 shadow-lg transition-opacity duration-500">
                {{ session('error') }}
            </div>
        @endif

        @php
            $inputClass = 'peer w-full rounded-lg border border-slate-400/80 bg-white/80 px-3 pb-2 pt-5 text-base text-slate-800 outline-none transition placeholder:text-transparent focus:border-indigo-600 focus:bg-white focus:shadow-[0_0_0_3px_rgba(79,70,229,0.14)] focus-visible:ring-2 focus-visible:ring-indigo-300 focus-visible:ring-offset-1';
            $labelClass = 'form-label pointer-events-none absolute left-2.5 top-0 z-10 -translate-y-1/2 bg-white px-1 text-[0.8rem] text-indigo-600 transition-all duration-200 peer-placeholder-shown:left-3 peer-placeholder-shown:top-1/2 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-placeholder-shown:text-base peer-placeholder-shown:text-slate-500 peer-focus:left-2.5 peer-focus:top-0 peer-focus:bg-white peer-focus:px-1 peer-focus:text-[0.8rem] peer-focus:text-indigo-600';
        @endphp

        <form action="{{ route('login.account') }}" method="POST" id="login-form" class="space-y-5">
            @csrf
            <div class="relative">
                <input type="email" id="email" name="email" placeholder="Email" required class="{{ $inputClass }}">
                <label class="{{ $labelClass }}" for="email">Email</label>
            </div>

            <div class="relative">
                <input type="password" id="password" name="password" placeholder="Password" required class="{{ $inputClass }} pr-11">
                <label class="{{ $labelClass }}" for="password">Password</label>
                <button type="button" id="togglePassword" class="absolute right-3 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full text-xl text-slate-400 transition hover:bg-slate-100 hover:text-indigo-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                    <i class="ri-eye-off-line pointer-events-none"></i>
                </button>
            </div>

            <div class="flex justify-end">
                <a href="#" id="forgotPasswordLink" class="text-sm font-medium text-slate-600 underline underline-offset-2 transition hover:text-indigo-600 focus-visible:rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                    Forgot Password?
                </a>
            </div>

            <button type="submit" class="btn-main w-full rounded-lg bg-indigo-600 px-4 py-3 font-heading text-base font-semibold text-white transition hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:bg-slate-400 disabled:opacity-70">
                Log In
            </button>
        </form>

        <div id="forgotPasswordModal" class="login-modal invisible fixed inset-0 z-[200] hidden h-full w-full items-center justify-center bg-slate-950/60 px-4 opacity-0 backdrop-blur-sm transition-opacity duration-200">
            <div class="login-modal-content relative w-full max-w-[400px] -translate-y-2 scale-[0.98] rounded-2xl border border-slate-700 bg-slate-800 px-6 py-7 text-left text-slate-50 opacity-0 shadow-[0_20px_55px_rgba(0,0,0,0.35)] transition duration-200 sm:px-7">
                <button type="button" class="close absolute right-4 top-3 inline-flex h-9 w-9 items-center justify-center rounded-full text-2xl leading-none text-slate-400 transition hover:bg-slate-700 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300">
                    &times;
                </button>
                <h2 class="mb-5 text-center font-heading text-[1.4rem] font-bold tracking-normal text-sky-50">Reset Password</h2>

                <div id="resetMessage" class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-sm text-emerald-800" style="display: none;">
                    Your password has been reset. Please check your email for the new password.
                    (It may get in the spam folder.)
                </div>

                <form id="forgotPasswordForm" method="POST" action="{{ route('forgot.password') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="reset-email" class="mb-2 block text-sm font-medium tracking-wide text-slate-300">Your Email</label>
                        <input type="email" id="reset-email" name="email" placeholder="name@email.com" required class="w-full rounded-lg border border-slate-600 bg-slate-950 px-3.5 py-3 text-sm text-slate-50 outline-none transition placeholder:text-slate-500 focus:border-indigo-400 focus-visible:ring-2 focus-visible:ring-indigo-300">
                    </div>
                    <button type="submit" class="btn-main w-full rounded-lg bg-indigo-600 px-4 py-3 font-heading text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-800 disabled:cursor-not-allowed disabled:bg-slate-500 disabled:opacity-70">
                        Submit
                    </button>
                </form>
            </div>
        </div>

        <div class="my-5 flex items-center gap-3 text-sm text-slate-500">
            <span class="h-px flex-1 bg-slate-300"></span>
            <span>or</span>
            <span class="h-px flex-1 bg-slate-300"></span>
        </div>

        <a href="{{ route('google-auth') }}" class="social-btn flex w-full items-center justify-center gap-2 rounded-lg border border-slate-700 bg-slate-800 px-4 py-3 font-heading text-sm font-medium text-white transition hover:bg-slate-950 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/google/google-original.svg" alt="Google" class="h-[21px] w-[21px]">
            Continue with Google
        </a>

        <div class="mt-4 text-center text-sm text-slate-700">
            Don't have an account?
            <a href="{{ route('login.register') }}" class="font-semibold text-indigo-600 underline underline-offset-2 transition hover:text-indigo-700 focus-visible:rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                Register
            </a>
        </div>
    </main>

    <script src="{{ asset ('js/login.js') }}"></script>

</body>

</html>
