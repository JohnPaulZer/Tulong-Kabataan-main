<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Your Email | Tulong Kabataan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">
</head>
<body style="margin:0; font-family: 'Lato', sans-serif; background:#dcf2fd; color:#1a2e40;">

    <div style="padding:40px 15px;"> <table width="100%" cellspacing="0" cellpadding="0"
            style="max-width:500px; margin:auto; background:#ffffff; border-radius:16px;
                   padding:36px 28px; box-shadow:0 10px 25px rgba(0,0,0,0.1); border-collapse:separate;">
            
            <tr>
                <td style="font-family: 'Lato', sans-serif; color:#0d3b66;
                           font-size:22px; font-weight:700; padding-bottom:16px; text-align:center;">
                    Verify Your Email
                </td>
            </tr>

            <tr>
                <td style="color:#1a2e40; font-size:15px; line-height:1.7;
                           text-align:left; padding-bottom:28px;">
                    Hi {{ $user->first_name }},<br><br>
                    Welcome to <strong style="color:#0d3b66;">Tulong Kabataan</strong>!  
                    Please confirm your email address by clicking the button below to complete your registration.
                </td>
            </tr>

            <tr>
              
                <td align="center" style="padding:10px 0 36px;">
                    <a href="{{ $verifyUrl }}" 
                        style="background:#00509d; color:#ffffff; padding:14px 32px; border-radius:8px;
                               text-decoration:none; font-weight:600; font-size:15px;
                               display:inline-block; text-align:center; box-shadow: 0 4px 10px rgba(0, 80, 157, 0.4);">
                        Verify Email Address
                    </a>
                </td>
            </tr>

            <tr>
                <td style="color:#4a5a6a; font-size:14px; text-align:left;
                           line-height:1.7; padding-bottom:20px;">
                    This verification link will expire in <strong style="color:#1a2e40;">5 minutes</strong>.<br>
                    If you didn’t register for Tulong Kabataan, you can safely ignore this email.
                </td>
            </tr>

            <tr>
                <td style="color:#6b7c93; font-size:13px; text-align:center;
                           padding-top:22px; border-top:1px solid #dce4ec;">
                    &copy; {{ date('Y') }} Tulong Kabataan. All rights reserved.
                </td>
            </tr>
        </table>
    </div>

</body>
</html>