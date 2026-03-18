<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset | Tulong Kabataan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; font-family:'Open Sans', Arial, sans-serif; background:#474056; color:#fafaff;">

    <div style="padding:40px 15px;">
        <table width="100%" cellspacing="0" cellpadding="0"
            style="max-width:500px; margin:auto; background:#29243b; border-radius:16px;
                   padding:36px 28px; box-shadow:0 10px 25px rgba(0,0,0,0.25); border-collapse:separate;">
            
            <tr>
                <td style="font-family:'Playfair Display',serif; color:#f3e8ff;
                           font-size:22px; font-weight:700; padding-bottom:16px; text-align:center;">
                    Reset Your Password
                </td>
            </tr>

            <tr>
                <td style="color:#bcb8cc; font-size:15px; line-height:1.7;
                           text-align:left; padding-bottom:28px;">
                    Hi {{ $user->first_name }},<br><br>
                    You recently requested to reset your password for your <strong>Tulong Kabataan</strong> account.  
                    Click the button below to securely create a new password.
                </td>
            </tr>

            <tr>
                <td align="center" style="padding:10px 0 36px;">
                    <a href="{{ $resetUrl }}" 
                        style="background:#8663ec; color:#fff; padding:14px 32px; border-radius:8px;
                               text-decoration:none; font-weight:600; font-size:15px;
                               display:inline-block; text-align:center;">
                        Reset My Password
                    </a>
                </td>
            </tr>

            <tr>
                <td style="color:#c5c1d5; font-size:14px; text-align:left;
                           line-height:1.7; padding-bottom:20px;">
                    This password reset link will expire in <strong>5 minutes</strong> for your security.<br>
                    If you didn’t request a password reset, you can safely ignore this email.
                </td>
            </tr>

            <tr>
                <td style="color:#8e8a9c; font-size:13px; text-align:center;
                           padding-top:22px; border-top:1px solid #3b3350;">
                    &copy; {{ date('Y') }} Tulong Kabataan. All rights reserved.
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
