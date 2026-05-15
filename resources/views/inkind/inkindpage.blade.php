<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In-Kind | Tulong Kabataan</title>
    <link rel="icon" href="{{ page_media_url('site_favicon', asset('img/log2.png')) }}" type="image/png">
    <!-- Remixicon -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="inkind-page">

    @include('partials.universalmodal')
    @include('partials.main-header')
    @include('administrator.partials.loading-screen')

    <main class="page-wrap">
        <section class="hero-section">
            <div class="image-container">
                <img src="{{ page_media_url('donation_hero_image', asset('img/bg1.jpg')) }}" alt="In-Kind Donations Hero" class="hero-image">
                <div class="overlay"></div>
            </div>
            <div class="content">
                <div class="text-container">
                    <h1 class="title">In-Kind Donations That Make a Difference</h1>
                    <p class="description">Contribute clothing, food, supplies, and more. Your donated items can help
                        those in need in your community.</p>
                    <div class="button-group" aria-label="In-kind donation actions">
                        <form action="{{ route('inkindmodal') }}" method="POST" class="donate-action-form">
                            @csrf
                            <button type="submit" id="openModalBtn" class="donate-btn">
                                <i class="ri-hand-heart-line" aria-hidden="true"></i>
                                <span>Donate Items Now</span>
                            </button>
                        </form>
                        <a href="{{ route('inkind.tracking') }}" class="learn-btn">
                            <i class="ri-line-chart-line" aria-hidden="true"></i>
                            <span>Donation Impact</span>
                        </a>
                    </div>
                </div>
            </div>
            @include('partials.wave-divider')
        </section>

        <section class="impact-statistics">
            <div class="constrain">
                <div class="grid">
                    <div class="stat-card stat-card-donors">
                        <div class="icon-container" aria-hidden="true"><i class="ri-user-heart-line icon"></i></div>
                        <div class="stat-text">
                            <div id="donors-count" class="stat-number">0</div>
                            <div class="stat-description">In-Kind Donors</div>
                        </div>
                    </div>
                    <div class="stat-card stat-card-donations">
                        <div class="icon-container" aria-hidden="true"><i class="ri-gift-line icon"></i></div>
                        <div class="stat-text">
                            <div id="donations-made-count" class="stat-number">0</div>
                            <div class="stat-description">Donations Made</div>
                        </div>
                    </div>
                    <div class="stat-card stat-card-upcoming">
                        <div class="icon-container" aria-hidden="true"><i class="ri-heart-pulse-line icon"></i></div>
                        <div class="stat-text">
                            <div id="upcoming-donations-count" class="stat-number">0</div>
                            <div class="stat-description">Upcoming Donations</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="donation-categories-v2">
            <div class="constrain">
                <div class="section-header">
                    <h2 class="section-title">What You Can Donate</h2>
                    <p class="section-description">
                        For Bicol calamities such as typhoons, floods, landslides, and volcanic activity, these are the
                        items families commonly need first.
                    </p>
                </div>
                <div class="category-grid-v2">
                    <!-- Food & Potable Water -->
                    <article class="category-card-v2 category-card-food">
                        <div class="category-card-header">
                            <div class="icon-circle red" aria-hidden="true"><i class="ri-shopping-basket-line"></i></div>
                            <div>
                                <h3 class="category-title-v2">Food & Potable Water</h3>
                                <p class="category-desc-v2">Ready-to-eat and shelf-stable food, plus safe drinking water
                                    for families in evacuation centers.</p>
                            </div>
                        </div>
                        <div class="needs-box red-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill red"></i> Bottled potable water</li>
                                <li><i class="ri-checkbox-circle-fill red"></i> Rice and easy-open canned goods</li>
                                <li><i class="ri-checkbox-circle-fill red"></i> Ready-to-eat food and biscuits</li>
                            </ul>
                        </div>
                    </article>
                    <!-- Hygiene & Sanitation Kits -->
                    <article class="category-card-v2 category-card-clothing">
                        <div class="category-card-header">
                            <div class="icon-circle blue" aria-hidden="true"><i class="ri-hand-sanitizer-line"></i></div>
                            <div>
                                <h3 class="category-title-v2">Hygiene & Sanitation Kits</h3>
                                <p class="category-desc-v2">Cleanliness essentials for evacuees during floods, typhoons,
                                    ashfall, and crowded shelters.</p>
                            </div>
                        </div>
                        <div class="needs-box blue-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill blue"></i> Soap, shampoo, toothbrush and toothpaste</li>
                                <li><i class="ri-checkbox-circle-fill blue"></i> Sanitary pads, diapers and tissue</li>
                                <li><i class="ri-checkbox-circle-fill blue"></i> Alcohol, masks and cleaning supplies</li>
                            </ul>
                        </div>
                    </article>
                    <!-- Sleeping & Evacuation Kits -->
                    <article class="category-card-v2 category-card-home">
                        <div class="category-card-header">
                            <div class="icon-circle yellow" aria-hidden="true"><i class="ri-hotel-bed-line"></i></div>
                            <div>
                                <h3 class="category-title-v2">Sleeping & Evacuation Kits</h3>
                                <p class="category-desc-v2">Basic comfort items for families staying in evacuation centers
                                    or whose homes were damaged.</p>
                            </div>
                        </div>
                        <div class="needs-box yellow-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill yellow"></i> Sleeping mats and blankets</li>
                                <li><i class="ri-checkbox-circle-fill yellow"></i> Towels and light bedding</li>
                                <li><i class="ri-checkbox-circle-fill yellow"></i> Clean clothes and rain protection</li>
                            </ul>
                        </div>
                    </article>
                    <!-- Kitchen & Family Kits -->
                    <article class="category-card-v2 category-card-school">
                        <div class="category-card-header">
                            <div class="icon-circle green" aria-hidden="true"><i class="ri-bowl-line"></i></div>
                            <div>
                                <h3 class="category-title-v2">Kitchen & Family Kits</h3>
                                <p class="category-desc-v2">Simple household items families can use when preparing meals
                                    after evacuation or displacement.</p>
                            </div>
                        </div>
                        <div class="needs-box green-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill green"></i> Plates, cups, spoons and forks</li>
                                <li><i class="ri-checkbox-circle-fill green"></i> Cooking pots, pans and ladles</li>
                                <li><i class="ri-checkbox-circle-fill green"></i> Water containers and pails</li>
                            </ul>
                        </div>
                    </article>
                    <!-- First Aid & Health -->
                    <article class="category-card-v2 category-card-medical">
                        <div class="category-card-header">
                            <div class="icon-circle purple" aria-hidden="true"><i class="ri-medicine-bottle-line"></i></div>
                            <div>
                                <h3 class="category-title-v2">First Aid & Health</h3>
                                <p class="category-desc-v2">Basic care items for minor injuries, common illness, and
                                    health monitoring while families wait for assistance.</p>
                            </div>
                        </div>
                        <div class="needs-box purple-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill purple"></i> First aid kits and antiseptics</li>
                                <li><i class="ri-checkbox-circle-fill purple"></i> Fever, pain and diarrhea medicine</li>
                                <li><i class="ri-checkbox-circle-fill purple"></i> Thermometers and basic health supplies</li>
                            </ul>
                        </div>
                    </article>
                    <!-- Emergency Power & Safety -->
                    <article class="category-card-v2 category-card-electronics">
                        <div class="category-card-header">
                            <div class="icon-circle indigo" aria-hidden="true"><i class="ri-flashlight-line"></i></div>
                            <div>
                                <h3 class="category-title-v2">Emergency Power & Safety</h3>
                                <p class="category-desc-v2">Useful tools for blackouts, communication gaps, cleanup, and
                                    safer movement after storms or eruptions.</p>
                            </div>
                        </div>
                        <div class="needs-box indigo-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill indigo"></i> Flashlights and batteries</li>
                                <li><i class="ri-checkbox-circle-fill indigo"></i> Power banks and charging cables</li>
                                <li><i class="ri-checkbox-circle-fill indigo"></i> Whistles, rope and battery radios</li>
                            </ul>
                        </div>
                    </article>

                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('partials.main-footer')
    <div id="modalPlaceholder"></div>

    <script>
        // Auto-hide toasts after ~4s
        setTimeout(() => {
            document.querySelectorAll('.toast').forEach(el => el.remove());
        }, 4000);

        // Count-up animation for stats
        function animateCount(element, target) {
            let start = parseInt(element.innerText.replace(/,/g, '')) || 0;
            let duration = 10000;
            let stepTime = 50;
            let increment = Math.max(1, Math.ceil((target - start) / (duration / stepTime)));

            let timer = setInterval(() => {
                start += increment;
                if (start >= target) {
                    start = target;
                    clearInterval(timer);
                }
                element.innerText = start.toLocaleString();
            }, stepTime);
        }

        function loadStats() {
            fetch('/stats')
                .then(res => res.json())
                .then(data => {
                    animateCount(document.getElementById('donors-count'), data.donors);
                    animateCount(document.getElementById('donations-made-count'), data.donationsMade);
                    animateCount(document.getElementById('upcoming-donations-count'), data.upcomingDonations);
                })
                .catch(err => console.error('Error loading stats:', err));
        }

        loadStats();
        setInterval(loadStats, 5000);

        // Simple toast function
        function showToast(message) {
            const toast = document.getElementById('toast');
            if (toast) {
                toast.textContent = message;
                toast.style.opacity = '1';
                setTimeout(() => toast.style.opacity = '0', 4000);
            }
        }

        // Check for session message on page load
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('toast_message'))
                showToast('{{ session('toast_message') }}');
            @endif
        });
    </script>

</body>

</html>
