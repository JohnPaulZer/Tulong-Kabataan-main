<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In-Kind | Tulong Kabataan</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
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
                <img src="{{ asset('img/bg1.jpg') }}" alt="In-Kind Donations Hero" class="hero-image">
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
                        We accept a variety of items that can help individuals and families in need. Check our current
                        needs in each category.
                    </p>
                </div>
                <div class="category-grid-v2">
                    <!-- Food & Groceries -->
                    <article class="category-card-v2 category-card-food">
                        <div class="category-card-header">
                            <div class="icon-circle red" aria-hidden="true"><i class="ri-shopping-basket-line"></i></div>
                            <div>
                                <h3 class="category-title-v2">Food & Groceries</h3>
                                <p class="category-desc-v2">Non-perishable food items, canned goods, and pantry staples to
                                    support food security programs.</p>
                            </div>
                        </div>
                        <div class="needs-box red-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill red"></i> Canned proteins (tuna, chicken)</li>
                                <li><i class="ri-checkbox-circle-fill red"></i> Rice and pasta</li>
                                <li><i class="ri-checkbox-circle-fill red"></i> Cooking oils</li>
                            </ul>
                        </div>
                    </article>
                    <!-- Clothing & Accessories -->
                    <article class="category-card-v2 category-card-clothing">
                        <div class="category-card-header">
                            <div class="icon-circle blue" aria-hidden="true"><i class="ri-t-shirt-line"></i></div>
                            <div>
                                <h3 class="category-title-v2">Clothing & Accessories</h3>
                                <p class="category-desc-v2">New or gently used clothing items for all ages, seasons, and sizes
                                    to help those in need.</p>
                            </div>
                        </div>
                        <div class="needs-box blue-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill blue"></i> Children's clothing (all sizes)</li>
                                <li><i class="ri-checkbox-circle-fill blue"></i> Winter coats and jackets</li>
                                <li><i class="ri-checkbox-circle-fill blue"></i> New underwear and socks</li>
                            </ul>
                        </div>
                    </article>
                    <!-- Home Goods -->
                    <article class="category-card-v2 category-card-home">
                        <div class="category-card-header">
                            <div class="icon-circle yellow" aria-hidden="true"><i class="ri-home-line"></i></div>
                            <div>
                                <h3 class="category-title-v2">Home Goods</h3>
                                <p class="category-desc-v2">Household items, furniture, and appliances to help families
                                    establish stable homes.</p>
                            </div>
                        </div>
                        <div class="needs-box yellow-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill yellow"></i> Bedding and towels</li>
                                <li><i class="ri-checkbox-circle-fill yellow"></i> Kitchen essentials</li>
                                <li><i class="ri-checkbox-circle-fill yellow"></i> Small appliances</li>
                            </ul>
                        </div>
                    </article>
                    <!-- School Supplies -->
                    <article class="category-card-v2 category-card-school">
                        <div class="category-card-header">
                            <div class="icon-circle green" aria-hidden="true"><i class="ri-book-open-line"></i></div>
                            <div>
                                <h3 class="category-title-v2">School Supplies</h3>
                                <p class="category-desc-v2">Educational materials and supplies to support students and promote
                                    learning opportunities.</p>
                            </div>
                        </div>
                        <div class="needs-box green-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill green"></i> Backpacks</li>
                                <li><i class="ri-checkbox-circle-fill green"></i> Notebooks and paper</li>
                                <li><i class="ri-checkbox-circle-fill green"></i> Art supplies</li>
                            </ul>
                        </div>
                    </article>
                    <!-- Medical Supplies -->
                    <article class="category-card-v2 category-card-medical">
                        <div class="category-card-header">
                            <div class="icon-circle purple" aria-hidden="true"><i class="ri-medicine-bottle-line"></i></div>
                            <div>
                                <h3 class="category-title-v2">Medical Supplies</h3>
                                <p class="category-desc-v2">Basic medical and hygiene items to support health and wellness for
                                    vulnerable populations.</p>
                            </div>
                        </div>
                        <div class="needs-box purple-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill purple"></i> First aid kits</li>
                                <li><i class="ri-checkbox-circle-fill purple"></i> Personal hygiene items</li>
                                <li><i class="ri-checkbox-circle-fill purple"></i> Over-the-counter medications</li>
                            </ul>
                        </div>
                    </article>
                    <!-- Electronics -->
                    <article class="category-card-v2 category-card-electronics">
                        <div class="category-card-header">
                            <div class="icon-circle indigo" aria-hidden="true"><i class="ri-computer-line"></i></div>
                            <div>
                                <h3 class="category-title-v2">Electronics</h3>
                                <p class="category-desc-v2">Working electronic devices to bridge the digital divide and support
                                    education and job searching.</p>
                            </div>
                        </div>
                        <div class="needs-box indigo-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill indigo"></i> Laptops and tablets</li>
                                <li><i class="ri-checkbox-circle-fill indigo"></i> Cell phones</li>
                                <li><i class="ri-checkbox-circle-fill indigo"></i> Chargers and accessories</li>
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
