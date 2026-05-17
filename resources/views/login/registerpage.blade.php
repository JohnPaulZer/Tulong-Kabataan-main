<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register | Tulong Kabataan</title>
    <link rel="icon" href="{{ page_media_url('site_favicon', asset('img/log2.png')) }}" type="image/png">
    <link rel="preload" as="image" href="{{ page_media_url('register_background', asset('img/backlogin.png')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="register-page relative m-0 flex h-screen w-screen items-center justify-center overflow-hidden bg-cover bg-center bg-fixed font-body text-gray-800"
    style="--auth-bg: url('{{ page_media_url('register_background', asset('img/backlogin.png')) }}');">
    <div class="fixed inset-0 bg-white/40 backdrop-blur-[1px]" aria-hidden="true"></div>

    <div
        class="relative z-10 mx-4 flex h-[85vh] max-h-[700px] w-[calc(100%-2rem)] flex-col overflow-hidden rounded-2xl bg-white shadow-[0_20px_40px_-18px_rgba(15,23,42,0.35),0_10px_18px_-12px_rgba(15,23,42,0.2)] md:mx-0 md:h-[85vh] md:w-[85%] lg:h-auto lg:max-h-[85vh] lg:w-full lg:max-w-[1040px] lg:flex-row">
        <div
            class="relative flex w-full shrink-0 flex-row items-center justify-between border-b border-gray-100 bg-white p-4 md:flex-col md:items-start md:justify-between md:p-6 lg:min-w-[360px] lg:w-[42%] lg:border-b-0 lg:border-r lg:p-8">
            <a href="{{ route('landpage') }}" class="mb-0 no-underline md:mb-4 lg:mb-10"
                aria-label="Tulong Kabataan homepage">
                <img src="{{ page_media_url('website_logo', asset('img/log.png')) }}" alt="TKA Logo" class="h-10 w-auto md:h-[50px]">
            </a>

            <a href="{{ route('login.page') }}" class="static no-underline md:absolute md:right-6 md:top-6 lg:right-8 lg:top-7">
                <button type="button"
                    class="rounded-full border border-gray-300 bg-gray-100 px-3 py-1.5 font-heading text-xs font-medium text-gray-800 transition hover:bg-gray-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-offset-2 lg:px-5 lg:py-2 lg:text-sm">
                    &larr; Back
                </button>
            </a>

            @php
                $panelImages = [
                    page_media_url('register_panel_image_1', asset('img/eyy.png')),
                    page_media_url('register_panel_image_2', asset('img/yagit.png')),
                    page_media_url('register_panel_image_3', asset('img/diss.jpg')),
                ];
            @endphp
            <div id="panelImage"
                class="hidden w-full rounded-[14px] bg-cover bg-center transition-[background-image] duration-1000 md:block md:h-[120px] lg:h-[260px]"
                data-images="{{ json_encode($panelImages) }}">
            </div>

            <div class="mt-3 hidden font-heading text-[1.18rem] font-semibold tracking-[0.3px] text-gray-800 lg:block">
                Start helping<br>communities today
            </div>

            <div class="mt-6 hidden gap-2 lg:flex" aria-hidden="true">
                <div class="dot active h-1 w-6 rounded-sm bg-indigo-600 transition-colors"></div>
                <div class="dot h-1 w-6 rounded-sm bg-gray-300 transition-colors"></div>
                <div class="dot h-1 w-6 rounded-sm bg-gray-300 transition-colors"></div>
            </div>
        </div>

        <div
            class="flex-1 overflow-y-auto bg-white p-5 [scrollbar-width:thin] md:p-[30px] lg:w-[58%] lg:px-12 lg:pb-10 lg:pt-8">
            <h2 class="mt-0 mb-3 font-heading text-2xl font-semibold text-gray-800 lg:mb-4 lg:text-[2rem]">
                Create an account
            </h2>
            <div class="mb-8 text-base text-gray-500">
                Already have an account?
                <a href="{{ route('login.page') }}"
                    class="ml-2 font-semibold text-indigo-600 underline decoration-indigo-300 underline-offset-2 transition hover:text-indigo-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-offset-2">
                    Log in
                </a>
            </div>

            @php
                $inputClass =
                    'peer w-full rounded-lg border border-gray-300 bg-white px-3.5 pb-2 pt-5 text-base text-gray-800 outline-none transition placeholder:text-transparent focus:border-indigo-600 focus:shadow-[0_0_0_3px_rgba(79,70,229,0.14)] focus-visible:ring-2 focus-visible:ring-indigo-300 focus-visible:ring-offset-1';
                $labelClass =
                    'form-label pointer-events-none absolute left-2.5 top-0 z-10 -translate-y-1/2 bg-white px-1 text-[0.8rem] text-indigo-600 transition-all duration-200 peer-placeholder-shown:left-3.5 peer-placeholder-shown:top-1/2 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 peer-focus:left-2.5 peer-focus:top-0 peer-focus:bg-white peer-focus:px-1 peer-focus:text-[0.8rem] peer-focus:text-indigo-600';
                $staticLabelClass =
                    'form-label pointer-events-none absolute left-2.5 top-0 z-10 -translate-y-1/2 bg-white px-1 text-[0.8rem] text-indigo-600 transition-all duration-200';
                $feedbackClass = 'mt-1 block min-h-4 text-xs leading-4 text-red-600';
            @endphp

            <div id="registerMessage" class="mb-5 hidden rounded-lg border px-4 py-3 text-sm font-medium"></div>

            @if (session('error'))
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register.acc') }}" method="POST" id="registerForm" class="flex flex-col gap-5">
                @csrf

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <div class="relative">
                            <input type="text" id="first_name" placeholder=" " name="first_name" value="{{ old('first_name') }}" required class="{{ $inputClass }} @error('first_name') input-error @enderror">
                            <label class="{{ $labelClass }}">First Name</label>
                        </div>
                    </div>

                    <div>
                        <div class="relative">
                            <input type="text" id="last_name" placeholder=" " name="last_name" value="{{ old('last_name') }}" required class="{{ $inputClass }} @error('last_name') input-error @enderror">
                            <label class="{{ $labelClass }}">Last Name</label>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="relative">
                        <input type="email" id="email" placeholder=" " name="email" value="{{ old('email') }}" required class="{{ $inputClass }} @error('email') input-error @enderror">
                        <label class="{{ $labelClass }}">Email</label>
                    </div>
                    <span id="emailFeedback" class="{{ $feedbackClass }}">@error('email'){{ $message }}@enderror</span>
                </div>

                <div>
                    <div class="relative">
                        <input type="tel" id="phone" placeholder=" " name="phone_number" value="{{ old('phone_number') }}" required
                            inputmode="numeric" pattern="09[0-9]{9}" maxlength="11" class="{{ $inputClass }} @error('phone_number') input-error @enderror">
                        <label class="{{ $labelClass }}">Contact Number</label>
                    </div>
                    <span id="phoneFeedback" class="{{ $feedbackClass }}">@error('phone_number'){{ $message }}@enderror</span>
                </div>

                <div>
                    <div class="relative">
                        <input type="date" id="birthday" name="birthday" value="{{ old('birthday') }}" required class="{{ $inputClass }} @error('birthday') input-error @enderror">
                        <label class="{{ $staticLabelClass }}">Birthday</label>
                    </div>
                    <span id="birthdayFeedback" class="{{ $feedbackClass }}">@error('birthday'){{ $message }}@enderror</span>
                </div>

                <div>
                    <div class="relative">
                        <input type="password" id="password" placeholder=" " name="password" required
                            class="{{ $inputClass }} pr-10 @error('password') input-error @enderror">
                        <label class="{{ $labelClass }}">Password</label>
                        <i class="password-toggle ri-eye-off-line absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-lg text-gray-500 transition hover:text-indigo-600"
                            id="togglePassword"></i>
                    </div>
                    <span id="passwordFeedback" class="{{ $feedbackClass }}">@error('password'){{ $message }}@enderror</span>
                </div>

                <button id="submitBtn" type="submit" disabled
                    class="mt-2 w-full rounded-lg border-0 bg-indigo-600 py-3.5 font-heading text-lg font-semibold text-white transition hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:bg-gray-400 disabled:opacity-70 disabled:hover:bg-gray-400">
                    <span class="register-submit-label">Create account</span>
                </button>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/register.js') }}"></script>
</body>

</html>
