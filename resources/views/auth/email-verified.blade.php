<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified | Tulong Kabataan</title>
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
            <i class="ri-mail-check-line"></i>
        </div>

        <h1>Email Verified!</h1>

        <div class="message">
            ✅ Your email has been successfully verified.<br>
            You can safely return to the app or close this tab.
        </div>

        <div class="success-message">
            The main app tab will automatically redirect if it was open.
        </div>
    </div>
</body>
</html>
