<style>
    :root {
        --primary-color: #4f46e5;
        --primary-dark: #3730a3;
        --footer-bg: #1a1a2c;
        --footer-text: #b3b3cc;
    }

    .footer,
    .footer *,
    .footer *:before,
    .footer *:after {
        box-sizing: border-box;
    }

    .footer {
        position: relative;
        background: var(--footer-bg);
        color: #fff;
        margin-top: 94px;
        padding: 60px 0 30px 0;
        font-family: 'Inter', sans-serif;
    }

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px;
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    .footer-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 40px;
    }

    .footer-content-left {
        max-width: 450px;
    }

    .footer-logo {
        display: inline-block;
    }

    .footer-logo img {
        height: 70px;
        margin-bottom: 16px;
        display: block;
    }

    .footer-description {
        color: var(--footer-text);
        font-size: 15px;
        line-height: 1.7;
        margin: 0;
    }

    .footer-socials {
        display: flex;
        gap: 12px;
    }

    .footer-socials a {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: #fff;
        font-size: 20px;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .footer-socials a:hover {
        background: var(--primary-color);
        transform: translateY(-4px);
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
    }

    .footer hr {
        border: none;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        width: 100%;
        margin: 10px 0;
    }

    .footer-bottom {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        padding-top: 10px;
    }

    .footer-bottom p {
        color: #64748b;
        font-size: 14px;
        margin: 0;
    }

    .footer-links {
        display: flex;
        gap: 30px;
    }

    .footer-links a {
        color: #94a3b8;
        font-size: 14px;
        text-decoration: none;
        transition: color 0.2s;
        position: relative;
    }

    .footer-links a:hover {
        color: #fff;
    }

    .footer-links a::after {
        content: '';
        position: absolute;
        width: 0;
        height: 1px;
        bottom: -2px;
        left: 0;
        background-color: var(--primary-color);
        transition: width 0.3s;
    }

    .footer-links a:hover::after {
        width: 100%;
    }

    @media (max-width: 900px) {
        .footer-top {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .footer-content-left {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            max-width: 100%;
        }

        .footer-bottom {
            flex-direction: column-reverse;
            text-align: center;
        }

        .footer-links {
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px;
        }
    }

    @media (max-width: 850px) {
        .footer {
            margin-top: 47px;
        }
    }
</style>

<footer class="footer">
    @include('partials.footer-wave-divider')
    <div class="footer-container">
        <div class="footer-top">
            <div class="footer-content-left">
                <a href="{{ route('landpage') }}" class="footer-logo" aria-label="Tulong Kabataan homepage">
                    <img src="{{ asset('img/log1.png') }}" alt="Tulong Kabataan Logo">
                </a>
                <p class="footer-description">
                    Empowering the next generation. Connecting generosity with community needs through secure donations and meaningful volunteer opportunities across the Bicol region.
                </p>
            </div>

            <div class="footer-socials">
                <a href="https://www.facebook.com/tulongkabataanbicol" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                    <i class="ri-facebook-fill"></i>
                </a>
                <a href="https://x.com/TulongKab_Bicol" target="_blank" rel="noopener noreferrer" aria-label="Twitter/X">
                    <i class="ri-twitter-x-fill"></i>
                </a>
                <a href="#" aria-label="Instagram">
                    <i class="ri-instagram-line"></i>
                </a>
            </div>
        </div>

        <hr>

        <div class="footer-bottom">
            <p>&copy; 2025 Tulong Kabataan. All rights reserved.</p>
            <div class="footer-links">
                <a href="{{ route('privacy.policy') }}">Privacy Policy</a>
                <a href="{{ route('terms.service') }}">Terms of Service</a>
                <a href="{{ route('cookie.policy') }}">Cookie Policy</a>
                <a href="{{ route('contact.us') }}">Contact Us</a>
                <a href="{{ route('sitemap') }}">Sitemap</a>
            </div>
        </div>
    </div>
</footer>

<div class="tk-floating-actions" aria-label="Floating page actions">
    <button type="button" class="back-to-top-btn" id="backToTopBtn" aria-label="Back to top">
        <i class="ri-arrow-up-line" aria-hidden="true"></i>
    </button>

    @include('partials.chatbot')
</div>

<script>
    (function() {
        const backToTopBtn = document.getElementById('backToTopBtn');

        if (!backToTopBtn) {
            return;
        }

        const toggleBackToTop = () => {
            backToTopBtn.classList.toggle('is-visible', window.scrollY > 320);
        };

        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        window.addEventListener('scroll', toggleBackToTop, {
            passive: true
        });

        toggleBackToTop();
    })();
</script>
