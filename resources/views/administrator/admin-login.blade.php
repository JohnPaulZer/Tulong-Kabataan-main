<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tulong Kabataan - Admin Login</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">
    <style>
        /* Import Google Font */


        /* --- Reset & Base Styles --- */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {

            background-color: #f3f4f6;
            /* Gray 100 */
            overflow: hidden;
            height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin: 0;
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            text-decoration: none;
            line-height: 1.6;

            /* Base Color */
            background-color: #f9fafb;

            /* The Pattern */
            background-image: radial-gradient(#4f46e5 0.75px, transparent 0.75px), radial-gradient(#4f46e5 0.75px, #f9fafb 0.75px);
            background-size: 30px 30px;
            background-position: 0 0, 15px 15px;

            /* This ensures the pattern is subtle (10% opacity equivalent) */
            background-blend-mode: multiply;
        }

        img {
            max-width: 100%;
            display: block;
        }

        /* ==================== TYPOGRAPHY SYSTEM ==================== */

        /* 1. Primary Font: Merriweather */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .heading-font,
        .section-title,
        .featured-title,
        .campaign-title,
        .btn,
        button,
        .raised,
        .campaign-count,
        .featured-badge,
        .pagination-number {
            font-family: 'Merriweather', serif;
            letter-spacing: -0.01em;
        }

        /* 2. Secondary Font Selectors */
        p,
        a,
        li,
        span,
        input,
        select,
        textarea,
        label,
        .body-font,
        .section-desc,
        .featured-desc,
        .filters-label,
        .goal,
        .pagination-info {
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            text-decoration: none;
        }

        /* --- Utility Classes (Replacing Tailwind Utilities) --- */
        .hidden {
            display: none !important;
        }

        /* --- Background Pattern --- */
        .bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
            background-image: radial-gradient(#3b82f6 0.5px, transparent 0.5px), radial-gradient(#3b82f6 0.5px, #f3f4f6 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            opacity: 0.1;
        }

        /* --- Dashboard Preview (Success Screen) --- */
        .dashboard-preview {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            z-index: 0;
            opacity: 0;
            transition: opacity 1s ease;
        }

        .dashboard-preview.visible {
            opacity: 1;
        }

        .preview-content {
            text-align: center;
        }

        .preview-content h1 {
            font-size: 1.875rem;
            /* 3xl */
            font-weight: 700;
            color: #1f2937;
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .preview-content p {
            color: #6b7280;
            margin-top: 0.5rem;
        }

        /* --- Login Container --- */
        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 530px;
            /* max-w-md */
            padding: 1.5rem;
        }

        .login-card {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            /* shadow-xl */
            overflow: hidden;
            border: 1px solid #f3f4f6;
            padding: 2rem;
        }

        /* Header Area */
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .logo-wrapper img {
            height: 6rem;
            /* h-24 */
            width: auto;
            object-fit: contain;
        }


        .login-header p {
            font-size: 15px;
            color: #000000;
            font-weight: 600;
            margin-top: 0.25rem;
        }

        /* --- Form Styles --- */
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            /* space-y-6 */
        }

        /* Error Message */
        .error-msg {
            background-color: #eff6ff;
            color: #2563eb;
            font-size: 0.875rem;
            padding: 0.75rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
        }

        .error-msg svg {
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
            flex-shrink: 0;
        }

        /* Input Groups */
        .input-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
            margin-left: 0.25rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon-left {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            padding-left: 0.75rem;
            display: flex;
            align-items: center;
            pointer-events: none;
        }

        .input-icon-left svg {
            height: 1.25rem;
            width: 1.25rem;
            color: #93c5fd;
            /* blue-300 */
        }

        .input-wrapper input {
            display: block;
            width: 100%;
            padding-left: 2.5rem;
            padding-right: 0.75rem;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            border: 1px solid #d1d5db;
            /* gray-300 */
            border-radius: 0.5rem;
            background-color: #f9fafb;
            /* gray-50 */
            color: #111827;
            font-size: 0.875rem;
            line-height: 1.25rem;
            transition: all 0.15s ease-in-out;
        }

        .input-wrapper input:focus {
            outline: none;
            background-color: white;
            border-color: #2563eb;
            /* blue-600 */
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .input-wrapper input::placeholder {
            color: #9ca3af;
        }

        /* Password Toggle Icon */
        .toggle-password {
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            padding-right: 0.75rem;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .toggle-password svg {
            height: 1.25rem;
            width: 1.25rem;
            color: #93c5fd;
            transition: color 0.2s;
        }

        .toggle-password svg:hover {
            color: #3b82f6;
        }

        .toggle-password svg.active-icon {
            color: #3b82f6;
        }

        /* --- Submit Button --- */
        .btn-primary {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: white;
            background-color: #2563eb;
            /* blue-600 */
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.15s;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            /* blue-700 */
            transform: translateY(-2px);
        }

        .btn-primary:focus {
            outline: none;
            box-shadow: 0 0 0 2px white, 0 0 0 4px #3b82f6;
        }

        /* Button States (handled via JS) */
        .btn-primary.loading {
            opacity: 0.75;
            cursor: not-allowed;
            transform: none;
        }

        .btn-primary.success {
            background-color: #16a34a;
            /* green-600 */
            transform: none;
        }

        .btn-primary.success:hover {
            background-color: #16a34a;
        }

        /* --- Footer --- */
        .forgot-password {
            margin-top: 1.5rem;
            text-align: center;
        }

        .forgot-password a {
            font-size: 0.75rem;
            color: #9ca3af;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-password a:hover {
            color: #2563eb;
        }

        .footer-copy {
            text-align: center;
            color: #9ca3af;
            font-size: 0.75rem;
            margin-top: 1.5rem;
        }

        /* --- Animations --- */

        /* Spinner */
        .loader {
            border: 3px solid #f3f3f3;
            border-radius: 50%;
            border-top: 3px solid white;
            /* Changed to white to match button text */
            width: 20px;
            height: 20px;
            margin-right: 0.5rem;
            animation: spin 1s linear infinite;
            display: none;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .5;
            }
        }

        /* Swipe Up Animation */
        @keyframes swipeUp {
            0% {
                transform: translateY(0);
                opacity: 1;
            }

            40% {
                transform: translateY(20px);
                /* Slight dip */
                opacity: 1;
            }

            100% {
                transform: translateY(-150vh);
                /* Fly off */
                opacity: 0;
            }
        }

        .animate-swipe-up {
            animation: swipeUp 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        }

        /* Admin-login-only polish. Public login/register pages are not loaded here. */
        :root {
            --admin-login-bg: #f6f8fb;
            --admin-login-surface: #ffffff;
            --admin-login-text: #0f172a;
            --admin-login-muted: #64748b;
            --admin-login-line: #dbe3ee;
            --admin-login-blue: #2563eb;
            --admin-login-blue-dark: #1d4ed8;
            --admin-login-blue-soft: #eff6ff;
            --admin-login-red: #dc2626;
            --admin-login-red-soft: #fef2f2;
            --admin-login-radius: 8px;
        }

        body.admin-login-page {
            min-height: 100vh;
            min-height: 100dvh;
            overflow: auto;
            padding: 24px;
            background:
                linear-gradient(135deg, rgba(37, 99, 235, 0.08), transparent 34%),
                linear-gradient(180deg, #f9fbff 0%, var(--admin-login-bg) 100%);
            color: var(--admin-login-text);
        }

        body.admin-login-page::before {
            content: "";
            position: fixed;
            inset: 0 auto 0 0;
            width: min(36vw, 440px);
            background: linear-gradient(180deg, #111827 0%, #10213f 58%, #0b1730 100%);
            clip-path: polygon(0 0, 88% 0, 100% 100%, 0 100%);
            pointer-events: none;
        }

        body.admin-login-page .bg-pattern {
            display: none;
        }

        body.admin-login-page .dashboard-preview {
            background: var(--admin-login-bg);
        }

        body.admin-login-page .login-container {
            max-width: 460px;
            padding: 0;
        }

        body.admin-login-page .login-card {
            position: relative;
            overflow: hidden;
            padding: clamp(28px, 4vw, 36px);
            border: 1px solid var(--admin-login-line);
            border-radius: var(--admin-login-radius);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.14);
        }

        body.admin-login-page .login-card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto;
            height: 4px;
            background: linear-gradient(90deg, var(--admin-login-blue), #059669);
        }

        body.admin-login-page .login-header {
            margin-bottom: 28px;
            text-align: left;
        }

        body.admin-login-page .logo-wrapper {
            justify-content: flex-start;
            margin-bottom: 18px;
        }

        body.admin-login-page .logo-wrapper img {
            height: 68px;
        }

        body.admin-login-page .login-header p {
            margin: 0;
            color: var(--admin-login-text);
            font-size: 22px;
            font-weight: 800;
            line-height: 1.2;
        }

        body.admin-login-page .login-header p::after {
            content: "Secure dashboard access";
            display: block;
            margin-top: 8px;
            color: var(--admin-login-muted);
            font-family: "Inter", sans-serif;
            font-size: 14px;
            font-weight: 500;
        }

        body.admin-login-page .login-form {
            gap: 18px;
        }

        body.admin-login-page .input-group label {
            margin: 0 0 8px;
            color: #334155;
            font-size: 13px;
            font-weight: 750;
        }

        body.admin-login-page .input-wrapper input {
            min-height: 46px;
            padding-top: 0;
            padding-bottom: 0;
            border: 1px solid #cbd5e1;
            border-radius: var(--admin-login-radius);
            background: #fff;
            color: var(--admin-login-text);
            font-size: 14px;
        }

        body.admin-login-page .input-wrapper input:hover {
            border-color: #94a3b8;
        }

        body.admin-login-page .input-wrapper input:focus {
            border-color: var(--admin-login-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.13);
        }

        body.admin-login-page .input-icon-left svg,
        body.admin-login-page .toggle-password svg {
            color: #64748b;
        }

        body.admin-login-page .toggle-password svg:hover,
        body.admin-login-page .toggle-password svg.active-icon {
            color: var(--admin-login-blue);
        }

        body.admin-login-page .error-msg {
            margin-bottom: 18px;
            border: 1px solid #fecaca;
            border-radius: var(--admin-login-radius);
            background: var(--admin-login-red-soft);
            color: var(--admin-login-red);
            font-weight: 650;
        }

        body.admin-login-page .btn-primary {
            min-height: 46px;
            margin-top: 4px;
            border-radius: var(--admin-login-radius);
            background: var(--admin-login-blue);
            font-family: "Inter", sans-serif;
            font-size: 14px;
            font-weight: 800;
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.2);
        }

        body.admin-login-page .btn-primary:hover {
            background: var(--admin-login-blue-dark);
            transform: translateY(-1px);
        }

        body.admin-login-page .btn-primary:focus {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.24);
        }

        body.admin-login-page .footer-copy {
            color: #64748b;
            font-size: 12px;
        }

        @media (max-width: 760px) {
            body.admin-login-page {
                align-items: flex-start;
                padding: 18px;
            }

            body.admin-login-page::before {
                width: 100%;
                height: 160px;
                clip-path: none;
            }

            body.admin-login-page .login-container {
                margin-top: 40px;
            }
        }
    </style>
</head>

<body class="admin-login-page">

    <div class="bg-pattern"></div>

    <div id="dashboard-preview" class="dashboard-preview">
        <div class="preview-content">
            <h1>Welcome, Administrator.</h1>
            <p>Loading Dashboard...</p>
        </div>
    </div>

    <div id="login-container" class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-wrapper">
                    <img src="{{ asset('img/log.png') }}" alt="Tulong Kabataan Logo">
                </div>
                <p>Administrator Portal</p>
            </div>

            <!-- Error message container (hidden by default) -->
            <div id="error-msg" class="error-msg hidden">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
                <span>Invalid credentials. Please try again.</span>
            </div>

            <!-- Login Form -->
            <form id="login-form" method="POST" action="{{ route('login.submit') }}" class="login-form">
                @csrf

                <div class="input-group">
                    <label for="username">Username or Email</label>
                    <div class="input-wrapper">
                        <div class="input-icon-left">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="username" name="username" required
                            placeholder="Enter your username or email">
                    </div>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <div class="input-icon-left">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="password" id="password" name="password" required
                            placeholder="Enter your password">

                        <div class="toggle-password" onclick="togglePassword()">
                            <svg id="eye-icon-off" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye-icon-on" class="hidden active-icon" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29" />
                            </svg>
                        </div>
                    </div>
                </div>



                <button type="submit" id="login-btn" class="btn-primary">
                    <span class="loader" id="btn-loader"></span>
                    <span id="btn-text">Sign In</span>
                </button>
            </form>


        </div>

        <p class="footer-copy">
            &copy; {{ date('Y') }} Tulong Kabataan Network. All rights reserved.
        </p>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIconOff = document.getElementById('eye-icon-off');
            const eyeIconOn = document.getElementById('eye-icon-on');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIconOff.classList.add('hidden');
                eyeIconOn.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIconOff.classList.remove('hidden');
                eyeIconOn.classList.add('hidden');
            }
        }

        // Handle form submission with Laravel integration
        document.getElementById('login-form').addEventListener('submit', function(e) {
            const btn = document.getElementById('login-btn');
            const loader = document.getElementById('btn-loader');
            const btnText = document.getElementById('btn-text');
            const errorMsg = document.getElementById('error-msg');
            const container = document.getElementById('login-container');
            const dashboardPreview = document.getElementById('dashboard-preview');

            // Reset any previous error
            errorMsg.classList.add('hidden');

            // Show loading state
            btn.disabled = true;
            loader.style.display = 'block';
            btnText.textContent = 'Verifying...';
            btn.classList.add('loading');

            // The form will submit normally to Laravel
            // Laravel will handle validation and redirect
            // If there's an error, the page will reload with error messages
        });

        // Auto-focus on username field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
    </script>
</body>

</html>
