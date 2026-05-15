<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>Reset Password | Tulong Kabataan</title>
    <link rel="icon" href="{{ page_media_url('site_favicon', asset('img/log2.png')) }}" type="image/png">
    <link rel="preload" as="image" href="{{ page_media_url('reset_password_background', asset('img/backlogin.png')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="reset-password-page relative flex min-h-screen items-center justify-center overflow-hidden bg-cover bg-center bg-no-repeat px-4 py-8 font-body text-slate-50"
    style="--auth-bg: url('{{ page_media_url('reset_password_background', asset('img/backlogin.png')) }}');">
    <div class="absolute inset-0 bg-slate-950/25 backdrop-blur-[1px]"></div>
    <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-white/40 to-transparent"></div>

    <main class="relative z-10 w-full max-w-[430px] rounded-2xl border border-slate-700 bg-slate-800 p-6 text-slate-50 shadow-[0_20px_60px_rgba(0,0,0,0.35)] sm:p-8 lg:p-10">
        @if (session('success'))
            <section class="text-center">
                <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-indigo-500/15 text-4xl text-indigo-300">
                    <i class="ri-checkbox-circle-line"></i>
                </div>
                <h1 class="mb-3 font-heading text-2xl font-bold tracking-normal text-sky-50">Password Updated</h1>
                <p class="mx-auto mb-6 max-w-[34ch] text-sm leading-relaxed text-slate-300">
                    Your password has been reset successfully. You can now log in using your new password.
                </p>

                <div class="grid gap-3">
                    <a href="{{ route('login.page') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 font-heading text-sm font-semibold text-white transition hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-800">
                        <i class="ri-login-box-line"></i>
                        Back to Login
                    </a>
                    <a href="{{ route('landpage') }}" class="inline-flex w-full items-center justify-center rounded-lg border border-slate-600 bg-slate-950 px-4 py-3 font-heading text-sm font-semibold text-slate-100 transition hover:border-indigo-400 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-800">
                        Return Home
                    </a>
                </div>
            </section>
        @else
            <div class="mb-6 flex justify-center">
                <a href="{{ route('landpage') }}" class="inline-flex rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-4">
                    <img src="{{ page_media_url('website_logo', asset('img/log.png')) }}" alt="Tulong Kabataan Logo" class="h-[60px] w-auto">
                </a>
            </div>

            <div class="mb-6 text-center">
                <h1 class="font-heading text-2xl font-bold tracking-normal text-sky-50">Create a New Password</h1>
                <p class="mt-2 text-sm leading-relaxed text-slate-300">
                    Choose a password with at least 8 characters.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-red-400/40 bg-red-950/45 px-4 py-3 text-sm text-red-100">
                    <div class="mb-1 flex items-center gap-2 font-heading font-semibold">
                        <i class="ri-error-warning-line text-base"></i>
                        Please check the fields below.
                    </div>
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $inputClass = 'peer w-full rounded-lg border border-slate-600 bg-slate-950 px-3 pb-2 pt-5 text-base text-slate-50 outline-none transition placeholder:text-transparent focus:border-indigo-400 focus:bg-slate-950 focus:shadow-[0_0_0_3px_rgba(129,140,248,0.2)] focus-visible:ring-2 focus-visible:ring-indigo-300';
                $labelClass = 'form-label pointer-events-none absolute left-2.5 top-0 z-10 -translate-y-1/2 bg-slate-800 px-1 text-[0.8rem] text-slate-300 transition-all duration-200 peer-placeholder-shown:left-3 peer-placeholder-shown:top-1/2 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-placeholder-shown:text-base peer-placeholder-shown:text-slate-400 peer-focus:left-2.5 peer-focus:top-0 peer-focus:bg-slate-800 peer-focus:px-1 peer-focus:text-[0.8rem] peer-focus:text-indigo-300';
            @endphp

            <form id="resetForm" method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ old('token', $token ?? request('token')) }}">
                <input type="hidden" name="email" value="{{ old('email', $email ?? request('email')) }}">

                <div>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="New Password" autocomplete="new-password" required class="reset-password-input {{ $inputClass }} pr-11 @error('password') input-error @enderror">
                        <label class="{{ $labelClass }}" for="password">New Password</label>
                        <button type="button" class="reset-password-toggle absolute right-3 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full text-xl text-slate-400 transition hover:bg-slate-700 hover:text-indigo-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300" aria-label="Show password">
                            <i class="ri-eye-off-line pointer-events-none"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 min-h-4 text-xs font-medium text-red-300">{{ $message }}</p>
                    @else
                        <p class="mt-1 min-h-4 text-xs text-slate-400">Use at least 8 characters.</p>
                    @enderror
                </div>

                <div>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" autocomplete="new-password" required class="reset-password-input {{ $inputClass }} pr-11 @error('password_confirmation') input-error @enderror">
                        <label class="{{ $labelClass }}" for="password_confirmation">Confirm Password</label>
                        <button type="button" class="reset-password-toggle absolute right-3 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full text-xl text-slate-400 transition hover:bg-slate-700 hover:text-indigo-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300" aria-label="Show password confirmation">
                            <i class="ri-eye-off-line pointer-events-none"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1 min-h-4 text-xs font-medium text-red-300">{{ $message }}</p>
                    @else
                        <p class="mt-1 min-h-4 text-xs text-slate-400">Re-enter the same password.</p>
                    @enderror
                </div>

                <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-3 font-heading text-base font-semibold text-white transition hover:-translate-y-0.5 hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-800 disabled:cursor-not-allowed disabled:bg-slate-500 disabled:opacity-70">
                    Update Password
                </button>
            </form>

            <div class="mt-5 text-center text-sm text-slate-300">
                Remember your password?
                <a href="{{ route('login.page') }}" class="font-semibold text-indigo-300 underline underline-offset-2 transition hover:text-indigo-200 focus-visible:rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-800">
                    Log in
                </a>
            </div>
        @endif
    </main>

    <script>
        document.querySelectorAll('.reset-password-toggle').forEach((button) => {
            button.addEventListener('click', () => {
                const input = button.parentElement.querySelector('.reset-password-input');
                const icon = button.querySelector('i');
                const isPassword = input.type === 'password';

                input.type = isPassword ? 'text' : 'password';
                button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
                icon.classList.toggle('ri-eye-line', isPassword);
                icon.classList.toggle('ri-eye-off-line', !isPassword);
            });
        });
    </script>
</body>

</html>
