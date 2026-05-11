<style>
    .loading-screen {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: stretch;
        justify-content: center;
        background: #f8fafc;
        opacity: 1;
        transition: opacity 0.28s ease;
    }

    .loading-screen.fade-out {
        opacity: 0;
        pointer-events: none;
    }

    .loading-content,
    .loading-skeleton {
        width: 100%;
    }

    .loading-content {
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 30px;
    }

    .loading-screen[data-mode="brand"] .loading-content {
        display: flex;
    }

    .loading-logo {
        width: 180px;
        height: auto;
        animation: subtlePulse 2s ease-in-out infinite;
    }

    .loading-progress {
        position: relative;
        width: 200px;
        height: 3px;
        overflow: hidden;
        border-radius: 3px;
        background: rgba(59, 130, 246, 0.15);
    }

    .loading-progress::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 30%;
        border-radius: 3px;
        background: #3b82f6;
        animation: progressMove 1.5s ease-in-out infinite;
    }

    .loading-skeleton {
        display: block;
        min-height: 100vh;
        padding: 18px 16px calc(110px + env(safe-area-inset-bottom));
        overflow: hidden;
        background: #f8fafc;
    }

    .loading-screen[data-mode="brand"] .loading-skeleton {
        display: none;
    }

    .skeleton-shell {
        width: min(100%, 1180px);
        margin: 0 auto;
    }

    .skeleton-header,
    .skeleton-hero,
    .skeleton-card,
    .skeleton-line,
    .skeleton-chip,
    .skeleton-avatar,
    .skeleton-bottom-nav {
        position: relative;
        overflow: hidden;
        background: #e5e7eb;
    }

    .skeleton-header::after,
    .skeleton-hero::after,
    .skeleton-card::after,
    .skeleton-line::after,
    .skeleton-chip::after,
    .skeleton-avatar::after,
    .skeleton-bottom-nav::after {
        content: '';
        position: absolute;
        inset: 0;
        transform: translateX(-100%);
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.72), transparent);
        animation: skeletonShimmer 1.25s ease-in-out infinite;
    }

    .skeleton-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 58px;
        margin-bottom: 22px;
    }

    .skeleton-brand {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .skeleton-avatar {
        width: 42px;
        height: 42px;
        border-radius: 14px;
    }

    .skeleton-header {
        width: 150px;
        height: 20px;
        border-radius: 999px;
    }

    .skeleton-actions {
        display: flex;
        gap: 10px;
    }

    .skeleton-chip {
        width: 76px;
        height: 34px;
        border-radius: 999px;
    }

    .skeleton-hero {
        height: clamp(190px, 34vh, 330px);
        margin-bottom: 22px;
        border-radius: 16px;
    }

    .skeleton-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .skeleton-card {
        min-height: 150px;
        padding: 18px;
        border-radius: 14px;
        background: #eef2f7;
    }

    .skeleton-card .skeleton-line {
        background: #dbe1ea;
    }

    .skeleton-line {
        height: 14px;
        margin-bottom: 12px;
        border-radius: 999px;
    }

    .skeleton-line.short {
        width: 45%;
    }

    .skeleton-line.medium {
        width: 72%;
    }

    .skeleton-line.long {
        width: 92%;
    }

    .skeleton-bottom-nav {
        display: none;
    }

    @keyframes subtlePulse {
        0%,
        100% {
            transform: scale(0.97);
        }

        50% {
            transform: scale(1);
        }
    }

    @keyframes progressMove {
        0% {
            left: -30%;
            width: 30%;
        }

        50% {
            width: 60%;
        }

        100% {
            left: 100%;
            width: 30%;
        }
    }

    @keyframes skeletonShimmer {
        100% {
            transform: translateX(100%);
        }
    }

    @media (max-width: 767px) {
        .loading-skeleton {
            padding: 14px 12px calc(100px + env(safe-area-inset-bottom));
        }

        .skeleton-top {
            height: 46px;
            margin-bottom: 18px;
        }

        .skeleton-avatar {
            width: 34px;
            height: 34px;
            border-radius: 12px;
        }

        .skeleton-header {
            width: 124px;
            height: 18px;
        }

        .skeleton-chip {
            width: 34px;
            height: 34px;
        }

        .skeleton-actions .skeleton-chip:nth-child(2) {
            display: none;
        }

        .skeleton-hero {
            height: 240px;
            border-radius: 14px;
        }

        .skeleton-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .skeleton-card {
            min-height: 118px;
            border-radius: 12px;
        }

        .skeleton-bottom-nav {
            position: fixed;
            left: 50%;
            bottom: max(12px, env(safe-area-inset-bottom));
            transform: translateX(-50%);
            display: block;
            width: min(calc(100% - 20px), 410px);
            height: 76px;
            border-radius: 24px;
            background: #e5e7eb;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
        }
    }
</style>

<div id="loadingScreen" class="loading-screen" data-mode="skeleton">
    <div class="loading-content" aria-label="Loading">
        <img src="{{ asset('img/log.png') }}" alt="Tulong Kabataan Logo" class="loading-logo">
        <div class="loading-progress"></div>
    </div>

    <div class="loading-skeleton" aria-hidden="true">
        <div class="skeleton-shell">
            <div class="skeleton-top">
                <div class="skeleton-brand">
                    <div class="skeleton-avatar"></div>
                    <div class="skeleton-header"></div>
                </div>
                <div class="skeleton-actions">
                    <div class="skeleton-chip"></div>
                    <div class="skeleton-chip"></div>
                </div>
            </div>

            <div class="skeleton-hero"></div>

            <div class="skeleton-grid">
                <div class="skeleton-card">
                    <div class="skeleton-line medium"></div>
                    <div class="skeleton-line long"></div>
                    <div class="skeleton-line short"></div>
                </div>
                <div class="skeleton-card">
                    <div class="skeleton-line short"></div>
                    <div class="skeleton-line long"></div>
                    <div class="skeleton-line medium"></div>
                </div>
                <div class="skeleton-card">
                    <div class="skeleton-line medium"></div>
                    <div class="skeleton-line long"></div>
                    <div class="skeleton-line short"></div>
                </div>
            </div>
        </div>

        <div class="skeleton-bottom-nav"></div>
    </div>
</div>

<script>
    (function() {
        const loadingScreen = document.getElementById('loadingScreen');
        const startTime = Date.now();
        const mode = 'skeleton';
        let pageLoaded = document.readyState === 'complete';

        if (loadingScreen) {
            loadingScreen.dataset.mode = mode;
            loadingScreen.style.display = 'flex';
        }

        const minTime = 420;
        const maxTime = 2600;

        function hideLoader() {
            if (!loadingScreen || loadingScreen.classList.contains('fade-out')) {
                return;
            }

            loadingScreen.classList.add('fade-out');

            setTimeout(function() {
                loadingScreen.style.display = 'none';
                document.dispatchEvent(new CustomEvent('loadingComplete'));
            }, 280);
        }

        function tryHideLoader() {
            const elapsed = Date.now() - startTime;

            if (!pageLoaded && document.readyState !== 'complete') {
                return;
            }

            if (elapsed >= minTime) {
                hideLoader();
                return;
            }

            setTimeout(hideLoader, minTime - elapsed);
        }

        if (pageLoaded) {
            tryHideLoader();
        } else {
            window.addEventListener('load', function() {
                pageLoaded = true;
                tryHideLoader();
            });
        }

        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                hideLoader();
            }
        });

        setTimeout(function() {
            pageLoaded = true;
            hideLoader();
        }, maxTime);
    })();
</script>
