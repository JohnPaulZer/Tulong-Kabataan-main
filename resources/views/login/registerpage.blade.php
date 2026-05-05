<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Tulong Kabataan</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
    @vite('resources/css/app.css')
</head>

<body
    class="m-0 flex h-screen w-screen items-center justify-center overflow-hidden bg-[url('/img/backlogin.png')] bg-cover bg-center bg-fixed font-body text-gray-800">
    <div
        class="flex h-[85vh] max-h-[700px] w-[90%] flex-col overflow-hidden rounded-2xl bg-white shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1),0_8px_10px_-6px_rgba(0,0,0,0.1)] md:h-[85vh] md:w-[85%] lg:h-auto lg:max-h-[85vh] lg:w-full lg:max-w-[880px] lg:flex-row">
        <div
            class="relative flex w-full shrink-0 flex-col items-start justify-between border-b border-gray-100 bg-white p-4 md:p-6 lg:min-w-[340px] lg:w-[40%] lg:border-b-0 lg:border-r lg:p-8">
            <a href="{{ route('landpage') }}" class="mb-4 no-underline lg:mb-10" aria-label="Tulong Kabataan homepage">
                <img src="{{ asset('img/log.png') }}" alt="TKA Logo" class="h-[50px] w-auto">
            </a>

            <a href="{{ route('login.page') }}" class="absolute right-4 top-4 no-underline lg:right-8 lg:top-7">
                <button type="button"
                    class="rounded-full border border-gray-300 bg-gray-100 px-3 py-1.5 font-heading text-xs font-medium text-gray-800 transition hover:bg-gray-200 lg:px-5 lg:py-2 lg:text-sm">
                    &larr; Back
                </button>
            </a>

            <div id="panelImage"
                class="h-20 w-full rounded-[14px] bg-cover bg-center transition-[background-image] duration-1000 md:h-[120px] lg:h-[260px]">
            </div>

            <div class="mt-3 hidden font-heading text-[1.18rem] font-medium tracking-[0.5px] text-gray-800 lg:block">
                Capturing Moments,<br>Creating Memories
            </div>

            <div class="mt-6 hidden gap-2 lg:flex" aria-hidden="true">
                <div class="dot active h-1 w-6 rounded-sm bg-blue-500 transition-colors"></div>
                <div class="dot h-1 w-6 rounded-sm bg-gray-300 transition-colors"></div>
                <div class="dot h-1 w-6 rounded-sm bg-gray-300 transition-colors"></div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto bg-white p-5 [scrollbar-width:thin] md:p-[30px] lg:px-10 lg:pb-10 lg:pt-[15px]">
            <h2 class="mt-2.5 mb-3 font-heading text-2xl font-semibold text-gray-800 lg:mb-4 lg:text-[2rem]">
                Create an account
            </h2>
            <div class="mb-8 text-base text-gray-500">
                Already have an account?
                <a href="{{ route('login.page') }}" class="ml-2 font-semibold text-blue-500 underline">Log in</a>
            </div>

            <form action="{{ route('register.acc') }}" method="POST" class="flex flex-col gap-[26px]">
                @csrf

                <div class="flex flex-col gap-4 md:flex-row md:gap-3">
                    <div class="relative w-full">
                        <input type="text" placeholder=" " name="first_name" required
                            class="peer w-full rounded-[7px] border border-gray-300 bg-white px-3.5 pb-2 pt-5 text-base text-gray-800 outline-none transition placeholder:text-transparent focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]">
                        <label
                            class="form-label pointer-events-none absolute left-2.5 top-0 z-10 -translate-y-1/2 bg-white px-1 text-[0.8rem] text-blue-500 transition-all duration-200 peer-placeholder-shown:left-3.5 peer-placeholder-shown:top-1/2 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 peer-focus:left-2.5 peer-focus:top-0 peer-focus:bg-white peer-focus:px-1 peer-focus:text-[0.8rem] peer-focus:text-blue-500">
                            First Name
                        </label>
                    </div>

                    <div class="relative w-full">
                        <input type="text" placeholder=" " name="last_name" required
                            class="peer w-full rounded-[7px] border border-gray-300 bg-white px-3.5 pb-2 pt-5 text-base text-gray-800 outline-none transition placeholder:text-transparent focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]">
                        <label
                            class="form-label pointer-events-none absolute left-2.5 top-0 z-10 -translate-y-1/2 bg-white px-1 text-[0.8rem] text-blue-500 transition-all duration-200 peer-placeholder-shown:left-3.5 peer-placeholder-shown:top-1/2 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 peer-focus:left-2.5 peer-focus:top-0 peer-focus:bg-white peer-focus:px-1 peer-focus:text-[0.8rem] peer-focus:text-blue-500">
                            Last Name
                        </label>
                    </div>
                </div>

                <div class="relative w-full">
                    <input type="email" id="email" placeholder=" " name="email" required
                        class="peer w-full rounded-[7px] border border-gray-300 bg-white px-3.5 pb-2 pt-5 text-base text-gray-800 outline-none transition placeholder:text-transparent focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]">
                    <label
                        class="form-label pointer-events-none absolute left-2.5 top-0 z-10 -translate-y-1/2 bg-white px-1 text-[0.8rem] text-blue-500 transition-all duration-200 peer-placeholder-shown:left-3.5 peer-placeholder-shown:top-1/2 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 peer-focus:left-2.5 peer-focus:top-0 peer-focus:bg-white peer-focus:px-1 peer-focus:text-[0.8rem] peer-focus:text-blue-500">
                        Email
                    </label>
                    <span id="emailFeedback"
                        class="absolute right-0 top-0 block -translate-y-[120%] whitespace-nowrap text-xs text-red-600"></span>
                </div>

                <div class="relative w-full">
                    <input type="tel" id="phone" placeholder=" " name="phone_number" required maxlength="20"
                        class="peer w-full rounded-[7px] border border-gray-300 bg-white px-3.5 pb-2 pt-5 text-base text-gray-800 outline-none transition placeholder:text-transparent focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]">
                    <label
                        class="form-label pointer-events-none absolute left-2.5 top-0 z-10 -translate-y-1/2 bg-white px-1 text-[0.8rem] text-blue-500 transition-all duration-200 peer-placeholder-shown:left-3.5 peer-placeholder-shown:top-1/2 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 peer-focus:left-2.5 peer-focus:top-0 peer-focus:bg-white peer-focus:px-1 peer-focus:text-[0.8rem] peer-focus:text-blue-500">
                        Contact Number
                    </label>
                    <span id="phoneFeedback"
                        class="absolute right-0 top-0 block -translate-y-[120%] whitespace-nowrap text-xs text-red-600"></span>
                </div>

                <div class="relative w-full">
                    <input type="date" id="birthday" name="birthday" required
                        class="peer w-full rounded-[7px] border border-gray-300 bg-white px-3.5 pb-2 pt-5 text-base text-gray-800 outline-none transition focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]">
                    <label
                        class="form-label pointer-events-none absolute left-2.5 top-0 z-10 -translate-y-1/2 bg-white px-1 text-[0.8rem] text-blue-500 transition-all duration-200">
                        Birthday
                    </label>
                    <span id="birthdayFeedback"
                        class="absolute right-0 top-0 block -translate-y-[120%] whitespace-nowrap text-xs text-red-600"></span>
                </div>

                <div class="relative w-full">
                    <input type="password" id="password" placeholder=" " name="password" required
                        class="peer w-full rounded-[7px] border border-gray-300 bg-white px-3.5 pb-2 pl-3.5 pr-10 pt-5 text-base text-gray-800 outline-none transition placeholder:text-transparent focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,0.1)]">
                    <label
                        class="form-label pointer-events-none absolute left-2.5 top-0 z-10 -translate-y-1/2 bg-white px-1 text-[0.8rem] text-blue-500 transition-all duration-200 peer-placeholder-shown:left-3.5 peer-placeholder-shown:top-1/2 peer-placeholder-shown:bg-transparent peer-placeholder-shown:px-0 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-500 peer-focus:left-2.5 peer-focus:top-0 peer-focus:bg-white peer-focus:px-1 peer-focus:text-[0.8rem] peer-focus:text-blue-500">
                        Password
                    </label>
                    <i class="password-toggle ri-eye-off-line absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-lg text-gray-500"
                        id="togglePassword"></i>
                    <span id="passwordFeedback"
                        class="absolute right-0 top-0 block -translate-y-[120%] whitespace-nowrap text-xs text-red-600"></span>
                </div>

                <button id="submitBtn" type="submit" disabled
                    class="mt-2 w-full rounded-[7px] border-0 bg-blue-500 py-3.5 font-heading text-lg font-semibold text-white transition hover:bg-blue-600 disabled:cursor-not-allowed">
                    Create account
                </button>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/register.js') }}"></script>
</body>

</html>
