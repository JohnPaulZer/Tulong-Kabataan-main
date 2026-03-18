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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset ('css/login.css') }}">
    </head>

<body>

    <div class="login-container">
        <div class="logo">
               <a href="{{ route('landpage') }}"><img src="img/log.png" alt="TKA Logo"></a>
        </div>
        <h2 style="text-align:center;">Log in to your account</h2>

          @if(session('error')) 
        <div id="flash-error" class="error-box">
            {{ session('error') }}
        </div>
        @endif

        <form action="{{ route('login.account') }}" method="POST" id="login-form">
            @csrf
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Email" required>
                <label class="form-label" for="email">Email</label>
            </div>
            
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <label class="form-label" for="password">Password</label>
                <i class="ri-eye-off-line password-toggle" id="togglePassword"></i>
            </div>

            <div class="forgot-password">
                <a href="#" id="forgotPasswordLink">Forgot Password?</a>
            </div>
            <button type="submit" class="btn-main">Log In</button>
        </form>

       <!-- FORGOT PASSWORD MODAL -->
    <div id="forgotPasswordModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Reset Password</h2>

            <div id="resetMessage" class="alert-message" style="display: none;">
                Your password has been reset. Please check your email for the new password.
                (It may get in the spam folder.)
            </div>

            <form id="forgotPasswordForm" method="POST" action="{{ route('forgot.password') }}">
                @csrf
                <div class="form-group">
                    <label for="reset-email">Your Email</label>
                    <input type="email" id="reset-email" name="email" placeholder="name@email.com" required>
                </div>
                <button type="submit" class="btn-main">Submit</button>
            </form>
        </div>
    </div>


       
        <div class="or">or</div>
        <a href="{{ route('google-auth') }}" class="social-btn" type="button" style="text-decoration: none;">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/google/google-original.svg" alt="Google">
            Continue with Google
        </a>
        
        <div class="subtext">
            Don't have an account? <a href="{{ route('login.register') }}">Register</a>
        </div>
    </div>
       
    <script src="{{ asset ('js/login.js') }}"></script>

</body>
</html>