<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications | Tulong Kabataan</title>
    <link rel="icon" href="{{ page_media_url('site_favicon', asset('img/log2.png')) }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="notifications-page">
    @include('partials.main-header')
    @include('administrator.partials.loading-screen')

    <main class="notifications-page-main">
        <section class="notifications-page-panel">
            <div>
                <p class="notifications-page-kicker">Notifications</p>
                <h1>All Notifications</h1>
                <p>Review platform updates, event reminders, donation activity, and account alerts.</p>
            </div>
            <button type="button" id="openNotificationsPageButton">
                <i class="ri-notification-3-line"></i>
                Open Notifications
            </button>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const openNotifications = () => window.notificationManager?.openModal();
            document.getElementById('openNotificationsPageButton')?.addEventListener('click', openNotifications);
            window.setTimeout(openNotifications, 100);
        });
    </script>
</body>

</html>
