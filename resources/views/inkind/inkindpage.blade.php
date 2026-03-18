<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In-Kind | Tulong Kabataan</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    <!-- Remixicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <!-- Google Fonts: Playfair Display & Open Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}?v=4">
    <link rel="stylesheet" href="{{ asset('css/inkind/inkindpage.css') }}">
</head>

<body>

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
                    <h1 class="title">Make a Difference with In-Kind Donations</h1>
                    <p class="description">Your donated items can change lives. Contribute clothing, food, supplies, and
                        more to help those in need in your community.</p>
                    <div class="button-group">
                        <form action="{{ route('inkindmodal') }}" method="POST">
                            @csrf
                            <button type="submit" id="openModalBtn" class="donate-btn">Donate Items Now</button>
                            <a href="{{ route('inkind.tracking') }}" class="learn-btn" type="button">Donation
                                Impact</a>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section class="impact-statistics">
            <div class="constrain">
                <div class="grid">
                    <div class="stat-card">
                        <div class="icon-container"><i class="ri-user-heart-line icon"></i></div>
                        <div class="stat-text">
                            <div id="donors-count" class="stat-number">0</div>
                            <div class="stat-description">In-Kind Donors</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="icon-container"><i class="ri-gift-line icon"></i></div>
                        <div class="stat-text">
                            <div id="donations-made-count" class="stat-number">0</div>
                            <div class="stat-description">Donations Made</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="icon-container"><i class="ri-heart-pulse-line icon"></i></div>
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
                <div class="section-header centered-header">
                    <h2 class="section-title">What You Can Donate</h2>
                    <p class="section-description">
                        We accept a variety of items that can help individuals and families in need. Check our<br>
                        current needs in each category.
                    </p>
                </div>
                <div class="category-grid-v2">
                    <!-- Food & Groceries -->
                    <div class="category-card-v2">
                        <div class="icon-circle red"><i class="ri-shopping-basket-line"></i></div>
                        <h3 class="category-title-v2">Food & Groceries</h3>
                        <p class="category-desc-v2">Non-perishable food items, canned goods, and pantry staples to
                            support food security programs.</p>
                        <div class="needs-box red-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill red"></i> Canned proteins (tuna, chicken)</li>
                                <li><i class="ri-checkbox-circle-fill red"></i> Rice and pasta</li>
                                <li><i class="ri-checkbox-circle-fill red"></i> Cooking oils</li>
                            </ul>
                        </div>
                    </div>
                    <!-- Clothing & Accessories -->
                    <div class="category-card-v2">
                        <div class="icon-circle blue"><i class="ri-t-shirt-line"></i></div>
                        <h3 class="category-title-v2">Clothing & Accessories</h3>
                        <p class="category-desc-v2">New or gently used clothing items for all ages, seasons, and sizes
                            to help those in need.</p>
                        <div class="needs-box blue-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill blue"></i> Children's clothing (all sizes)</li>
                                <li><i class="ri-checkbox-circle-fill blue"></i> Winter coats and jackets</li>
                                <li><i class="ri-checkbox-circle-fill blue"></i> New underwear and socks</li>
                            </ul>
                        </div>
                    </div>
                    <!-- Home Goods -->
                    <div class="category-card-v2">
                        <div class="icon-circle yellow"><i class="ri-home-line"></i></div>
                        <h3 class="category-title-v2">Home Goods</h3>
                        <p class="category-desc-v2">Household items, furniture, and appliances to help families
                            establish stable homes.</p>
                        <div class="needs-box yellow-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill yellow"></i> Bedding and towels</li>
                                <li><i class="ri-checkbox-circle-fill yellow"></i> Kitchen essentials</li>
                                <li><i class="ri-checkbox-circle-fill yellow"></i> Small appliances</li>
                            </ul>
                        </div>
                    </div>
                    <!-- School Supplies -->
                    <div class="category-card-v2">
                        <div class="icon-circle green"><i class="ri-book-open-line"></i></div>
                        <h3 class="category-title-v2">School Supplies</h3>
                        <p class="category-desc-v2">Educational materials and supplies to support students and promote
                            learning opportunities.</p>
                        <div class="needs-box green-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill green"></i> Backpacks</li>
                                <li><i class="ri-checkbox-circle-fill green"></i> Notebooks and paper</li>
                                <li><i class="ri-checkbox-circle-fill green"></i> Art supplies</li>
                            </ul>
                        </div>
                    </div>
                    <!-- Medical Supplies -->
                    <div class="category-card-v2">
                        <div class="icon-circle purple"><i class="ri-medicine-bottle-line"></i></div>
                        <h3 class="category-title-v2">Medical Supplies</h3>
                        <p class="category-desc-v2">Basic medical and hygiene items to support health and wellness for
                            vulnerable populations.</p>
                        <div class="needs-box purple-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill purple"></i> First aid kits</li>
                                <li><i class="ri-checkbox-circle-fill purple"></i> Personal hygiene items</li>
                                <li><i class="ri-checkbox-circle-fill purple"></i> Over-the-counter medications</li>
                            </ul>
                        </div>
                    </div>
                    <!-- Electronics -->
                    <div class="category-card-v2">
                        <div class="icon-circle indigo"><i class="ri-computer-line"></i></div>
                        <h3 class="category-title-v2">Electronics</h3>
                        <p class="category-desc-v2">Working electronic devices to bridge the digital divide and support
                            education and job searching.</p>
                        <div class="needs-box indigo-bg">
                            <div class="needs-title-v2">Current Needs:</div>
                            <ul>
                                <li><i class="ri-checkbox-circle-fill indigo"></i> Laptops and tablets</li>
                                <li><i class="ri-checkbox-circle-fill indigo"></i> Cell phones</li>
                                <li><i class="ri-checkbox-circle-fill indigo"></i> Chargers and accessories</li>
                            </ul>
                        </div>
                    </div>

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
