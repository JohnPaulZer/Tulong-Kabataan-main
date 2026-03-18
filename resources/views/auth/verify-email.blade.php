<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | Tulong Kabataan</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
</head>
<body>
    <div class="verify-container">
        <div class="verify-icon">
            <i class="ri-mail-send-line"></i>
        </div>
        
        <h1>Verify Your Email</h1>
        
        @if (session('message'))
            <div class="success-message">
                {{ session('message') }}
            </div>
        @endif

        <div class="message">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </div>

        <div class="loading" id="loadingMessage">
            <i class="ri-loader-4-line"></i>
            Verifying your email...
        </div>

        <div id="buttonContainer" style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <form method="POST" action="{{ route('verification.send') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn-primary">
                    RESEND VERIFICATION EMAIL
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn-secondary">
                <i class="ri-logout-box-r-line" style="margin-right:6px;"></i>
                Log Out
            </button>
        </form>


         
        </div>
    </div>

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