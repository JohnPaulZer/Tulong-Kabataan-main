<style>
    /* Loading Screen with Simple Progress Bar */
    .loading-screen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #f9fafb;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 1;
        transition: opacity 0.4s ease;
    }

    .loading-screen.fade-out {
        opacity: 0;
        pointer-events: none;
    }

    .loading-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 30px;
    }

    .loading-logo {
        width: 180px;
        height: auto;
        animation: subtlePulse 2s ease-in-out infinite;
    }

    .loading-progress {
        width: 200px;
        height: 3px;
        background: rgba(59, 130, 246, 0.15);
        border-radius: 3px;
        overflow: hidden;
        position: relative;
    }

    .loading-progress::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 30%;
        background: #3b82f6;
        border-radius: 3px;
        animation: progressMove 1.5s ease-in-out infinite;
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
</style>

<div id="loadingScreen" class="loading-screen">
    <div class="loading-content">
        <img src="{{ asset('img/log.png') }}" alt="Tulong Kabataan Logo" class="loading-logo">
        <div class="loading-progress"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loadingScreen = document.getElementById('loadingScreen');
        const MIN_TIME = 1200; // Minimum 1.2 seconds - GOOD for UX
        const MAX_TIME = 8000; // Maximum 8 seconds for slow networks
        const startTime = Date.now();
        let pageLoaded = false;
        let criticalResourcesLoaded = false;

        // Show immediately
        loadingScreen.style.display = 'flex';

        // Check for critical resources (optional but good practice)
        function checkCriticalResources() {
            // Check if key images are loaded
            const logo = document.querySelector('.logo-word img');
            if (logo) {
                if (logo.complete) {
                    criticalResourcesLoaded = true;
                } else {
                    logo.addEventListener('load', function() {
                        criticalResourcesLoaded = true;
                    });
                    logo.addEventListener('error', function() {
                        criticalResourcesLoaded = true; // Still proceed if image fails
                    });
                }
            } else {
                criticalResourcesLoaded = true;
            }
        }

        // Start checking resources
        setTimeout(checkCriticalResources, 100);

        // Handle page load
        window.addEventListener('load', function() {
            pageLoaded = true;
            tryHideLoader();
        });

        // Handle page before unload (for navigation)
        window.addEventListener('beforeunload', function() {
            // Reset state if user navigates away
            pageLoaded = false;
        });

        function tryHideLoader() {
            const elapsed = Date.now() - startTime;

            // Ensure MIN_TIME has passed AND page is loaded
            if (pageLoaded && elapsed >= MIN_TIME) {
                hideLoader();
            } else if (pageLoaded) {
                // Page loaded but MIN_TIME not reached yet
                const remaining = MIN_TIME - elapsed;
                setTimeout(hideLoader, remaining);
            }
            // If page not loaded yet, wait for load event
        }

        function hideLoader() {
            // Double-check page is really loaded
            if (document.readyState !== 'complete') {
                // Page not fully loaded, wait a bit more
                setTimeout(hideLoader, 100);
                return;
            }

            loadingScreen.classList.add('fade-out');
            setTimeout(function() {
                loadingScreen.style.display = 'none';
                // Optional: Dispatch event for other scripts
                document.dispatchEvent(new CustomEvent('loadingComplete'));
            }, 400);
        }

        // Safety timeout - MAX_TIME for very slow networks
        setTimeout(function() {
            if (loadingScreen.style.display === 'flex') {
                console.log('Loading screen timeout after', MAX_TIME, 'ms');
                hideLoader();
            }
        }, MAX_TIME);

        // Fallback: If DOMContentLoaded fires but load doesn't (some assets fail)
        setTimeout(function() {
            if (!pageLoaded && loadingScreen.style.display === 'flex') {
                console.log('Load event didn\'t fire, hiding loader anyway');
                pageLoaded = true;
                tryHideLoader();
            }
        }, 5000); // 5 seconds after DOMContentLoaded
    });
</script>
