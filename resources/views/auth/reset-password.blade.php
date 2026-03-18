<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | Tulong Kabataan</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: "Open Sans", Arial, sans-serif;
            background: #474056;
            color: #fafaff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .reset-container {
            background: #29243b;
            padding: 40px;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44, 37, 57, 0.25);
            width: 100%;
            max-width: 430px;
            text-align: center;
            position: relative;
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo img {
            height: 65px;
        }

        h2 {
            font-family: "Playfair Display", serif;
            font-weight: 700;
            color: #f3e8ff;
            font-size: 1.4rem;
            margin-bottom: 25px;
            transition: opacity 0.3s ease;
        }

        label {
            display: block;
            text-align: left;
            margin-bottom: 6px;
            color: #c5c1d5;
            font-size: 0.9rem;
        }

        input {
            width: 100%;
            margin-bottom: 20px;
            padding: 12px 14px;
            background: #26243a;
            border: 1.5px solid #3d3652;
            color: #fafaff;
            border-radius: 8px;
            font-size: 0.95rem;
            box-sizing: border-box;
        }

        input:focus {
            border-color: #8663ec;
            outline: none;
        }

        .btn-main {
            width: 100%;
            padding: 13px;
            background: #8663ec;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.1s ease;
        }

        .btn-main:hover {
            background: #7a52e4;
            transform: translateY(-1px);
        }

        .alert-error {
            background: #ffe2e2;
            color: #8b0000;
            border: 1px solid #f5bcbc;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 15px;
            font-size: 0.95rem;
            animation: fadeIn 0.3s ease;
        }

        .success-box {
            background: #dfffe1;
            color: #146c2e;
            border: 1px solid #b8efb8;
            border-radius: 12px;
            padding: 40px 20px;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.25);
            animation: fadeIn 0.4s ease;
            margin-top: 10px;
        }

        .success-box i {
            font-size: 42px;
            margin-bottom: 14px;
            color: #146c2e;
        }

        .success-box p {
            margin: 5px 0;
            color: #105d24;
            font-weight: 500;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            body {
                padding: 0 16px;
            }

            .reset-container {
                padding: 28px;
                border-radius: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="reset-container">

        <div id="resetLogo" class="logo">
            <a  id="resetLogo" href="{{ route('landpage') }}">
                <img src="{{ asset('img/log1.png') }}" alt="Tulong Kabataan Logo">
            </a>
        </div>

       
        @if (session('success'))
            <div class="success-box" id="successMessage">
                <i class="ri-checkbox-circle-line"></i>
                <p style="font-size:1.05rem; color:#146c2e; font-weight:700;">
                    Your password has been reset successfully!
                </p>
                <p>You can now log in using your new password.</p>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.getElementById('resetForm');
                    const title = document.getElementById('resetTitle');
                    const logo = document.getElementById('resetLogo');
                    if (form) form.style.display = 'none';
                    if (title) title.style.display = 'none';
                    if (logo) logo.style.display = 'none';
                });

            </script>
        @endif


        @if ($errors->any())
            <div class="alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <h2 id="resetTitle">Reset Your Password</h2>

        <form id="resetForm" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <label>New Password</label>
            <input type="password" name="password" placeholder="Enter new password" required>

            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" placeholder="Confirm new password" required>

            <button type="submit" class="btn-main">Update Password</button>
        </form>
    </div>
</body>
</html>
