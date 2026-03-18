<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Event Reminder</title>
    <style>
        body {
            font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            /* MATCHED: Light Sky Blue from the background of the image */
            background-color: #BCE6FA;
            margin: 0;
            padding: 0;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 40px auto;
            /* MATCHED: White card for clean, pastel look against the blue bg */
            background-color: #FFFFFF;
            border-radius: 8px;
            /* Softer, blue-tinted shadow */
            box-shadow: 0 6px 18px rgba(0, 83, 138, 0.15);
            overflow: hidden;
        }

        .email-header {
            text-align: center;
            /* MATCHED: Medium Blue from the flower petals */
            background-color: #5B9BD5;
            padding: 25px 20px;
            color: #FFFFFF;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .email-body {
            padding: 30px 25px;
        }

        .email-body h2 {
            /* UPDATED: Almost Black/Ink for better contrast */
            color: #0B1120;
            font-size: 20px;
            margin-top: 0;
        }

        .email-body p {
            /* UPDATED: Dark Charcoal for crisp readability */
            color: #374151;
            line-height: 1.6;
            font-size: 16px;
            margin: 12px 0;
        }

        .event-details {
            /* MATCHED: Very pale blue/white mix */
            background-color: #F0F9FF;
            /* MATCHED: Border matches the header blue */
            border-left: 4px solid #5B9BD5;
            padding: 20px 15px;
            border-radius: 4px;
            margin: 25px 0;
        }

        .event-details h3 {
            margin: 0 0 10px;
            /* UPDATED: Almost Black */
            color: #0B1120;
            font-size: 20px;
            font-weight: 600;
        }

        .event-details p {
            margin: 6px 0;
            /* UPDATED: Dark Charcoal */
            color: #374151;
            font-size: 15px;
        }

        .event-details strong {
            /* UPDATED: Almost Black */
            color: #0B1120;
        }

        .registered-info {
            /* MATCHED: Light Gray-Blue background */
            background-color: #E6F2F8;
            border: 1px solid #CFE2F0;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            text-align: center;
            font-size: 15px;
            /* UPDATED: Dark Charcoal */
            color: #374151;
        }

        .registered-info strong {
            display: block;
            font-size: 16px;
            margin-top: 5px;
            /* UPDATED: Slightly darker blue for better contrast on light bg */
            color: #0369a1;
        }

        .divider {
            height: 1px;
            background-color: #E2E8F0;
            margin: 20px 0;
        }

        .email-footer {
            margin-top: 30px;
            /* UPDATED: Darker Gray for footer */
            color: #4B5563;
            font-size: 14px;
            text-align: center;
            line-height: 1.2;
        }

        .copyright-text {
            /* UPDATED: Dark Slate for visibility on the blue background */
            color: #1E293B;
            font-size: 14px;
            text-align: center;
            padding: 15px 0;
        }

        @media screen and (max-width: 600px) {
            .email-wrapper {
                margin: 20px 10px;
                border-radius: 0;
            }

            .email-body {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Main Table Background changed to match Body Sky Blue -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #BCE6FA;">
        <tr>
            <td align="center">
                <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td class="email-header">
                            <h1>Event Reminder</h1>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-body">
                            <h2>Hello, {{ $user->first_name }}! 👋</h2>
                            <p>This is a friendly reminder that you are registered for the
                                following upcoming event. Get ready!</p>

                            <div class="event-details">
                                <h3>{{ $event->title }}</h3>
                                <p><strong>Date & Time:</strong>
                                    {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y • h:i A') }}</p>
                                <p><strong>Location:</strong> {{ $event->location }}</p>
                            </div>

                            <div class="registered-info">
                                Your registered role for this event is:
                                <strong>{{ $registration->registered_role ?? 'N/A' }}</strong>
                            </div>

                            <p>We look forward to seeing you
                                there! Please check in at least 15 minutes before the start time.</p>

                            <p class="divider"></p>

                            <p class="email-footer">
                                Best regards,<br>
                                <strong>Tulong Kabataan</strong>
                            </p>
                        </td>
                    </tr>
                </table>
                <table width="600" cellpadding="0" cellspacing="0" border="0" class="copyright-table">
                    <tr>
                        <!-- Background matched to body color -->
                        <td align="center" class="copyright-text" style="background-color: #BCE6FA;">
                            &copy; 2025 Tulong Kabataan. All rights reserved.
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>

</html>