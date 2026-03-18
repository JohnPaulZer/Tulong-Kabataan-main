<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking & Needs | Tulong Kabataan</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,400&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}?v=4">

    <style>
        /* --- 1. CORE RESET & VARIABLES --- */
        :root {
            --primary: #4f46e5;
            --primary-light: #e0e7ff;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-light: #f8fafc;
            /* Centralized Layout Variables */
            --max-width: 1200px;
            --gutter: clamp(1rem, 5vw, 2rem);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            /* Crucial for alignment */
        }

        body {
            margin: 0;
            padding: 0;
            font-family: "Lato", sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .heading-font,
        .btn {
            font-family: "Poppins", sans-serif;
            letter-spacing: -0.01em;
        }

        /* --- 2. LAYOUT UTILITIES --- */
        .container {
            width: 100%;
            max-width: var(--max-width);
            margin: 0 auto;
            padding: 0 var(--gutter);
        }

        /* Fix for nested containers */
        .container .container {
            padding: 0;
            max-width: 100%;
            width: 100%;
        }

        /* --- 3. PAGE HEADER --- */
        .tracking-header {
            text-align: center;
            background: linear-gradient(135deg, rgba(237, 233, 254, 1), rgba(224, 231, 255, 0.7)),
                url('{{ asset('img/inkind.png') }}');
            background-size: cover;
            background-position: center;
            padding: clamp(80px, 15vh, 150px) 0;
            margin-bottom: 3rem;
            border-bottom: 1px solid rgba(79, 70, 229, 0.1);
        }

        .tracking-title {
            font-size: clamp(1.8rem, 5vw, 3.5rem);
            font-weight: 700;
            margin: 0 0 1rem;
            color: #1e293b;
            line-height: 1.2;
        }

        .tracking-desc {
            font-size: clamp(1rem, 2vw, 1.15rem);
            color: #475569;
            max-width: 700px;
            margin: 0 auto;
        }

        /* --- 4. RELIEF OPERATIONS SLIDER --- */
        .ops-section {
            margin-bottom: 4rem;
            position: relative;
        }

        .ops-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .ops-slider-wrapper {
            position: relative;
            overflow: hidden;
            padding: 10px 5px;
            width: 100%;
        }

        .ops-track {
            display: flex;
            gap: 20px;
            transition: transform 0.4s ease-in-out;
        }

        .ops-card {
            flex: 0 0 calc(33.333% - 14px);
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .ops-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .ops-img-container {
            width: 100%;
            height: 200px;
            position: relative;
        }

        .ops-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ops-date-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.95);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .ops-content {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .ops-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .ops-desc {
            font-size: 0.9rem;
            color: var(--text-gray);
            margin-bottom: 1rem;
            flex-grow: 1;
        }

        .ops-footer {
            border-top: 1px solid #f1f5f9;
            padding-top: 1rem;
            font-size: 0.9rem;
            color: var(--primary);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Slider Navigation Buttons */
        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            color: var(--primary);
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .slider-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .slider-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .slider-btn.prev {
            left: -10px;
        }

        .slider-btn.next {
            right: -10px;
        }

        /* --- 5. DASHBOARD & CARDS --- */
        .dashboard-grid {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            margin-bottom: 4rem;
            width: 100%;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            padding: clamp(1.5rem, 4vw, 2.5rem);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            width: 100%;
            margin: 0;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .card-header i {
            font-size: 1.5rem;
            color: var(--primary);
            background: #e0e7ff;
            padding: 8px;
            border-radius: 8px;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
        }

        /* --- 6. LOCATION LIST (FIXED SCROLLING) --- */
        .location-container {
            max-height: 400px;
            /* Constrain height */
            overflow-y: auto;
            /* ALLOW SCROLLING ALWAYS */
            padding-right: 10px;
            /* Space for scrollbar */
            transition: all 0.3s ease;
        }

        .location-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .location-item {
            padding: 0.5rem 0;
        }

        .loc-details h4 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }

        /* Custom Scrollbar Styling */
        .location-container::-webkit-scrollbar {
            width: 6px;
        }

        .location-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .location-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .location-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge.active {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-badge.inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* --- 7. CTA SECTION --- */
        .cta-section {
            background: #fff;
            text-align: center;
            padding: 3rem 1rem;
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
            border: 2px dashed var(--primary);
            width: 100%;
        }

        .cta-btn {
            background: var(--primary);
            color: white;
            padding: 12px 32px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
            transition: background 0.3s;
        }

        .cta-btn:hover {
            background: #4338ca;
        }

        /* --- 8. MODAL STYLES --- */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
        }

        .modal.is-open {
            display: block;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-container {
            background: #fff;
            border-radius: 12px;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            background: var(--primary);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .modal-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .modal-body {
            padding: 1.5rem;
            overflow-y: auto;
        }

        .modal-content-section {
            margin-bottom: 1.5rem;
        }

        .category-group {
            margin-bottom: 1rem;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            padding: 1rem;
        }

        .category-title {
            margin-top: 0;
            display: flex;
            justify-content: space-between;
            color: var(--text-dark);
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .category-total {
            font-size: 0.75rem;
            background: var(--primary-light);
            color: var(--primary);
            padding: 2px 8px;
            border-radius: 4px;
        }

        .donations-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .donation-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px dashed #eee;
        }

        .donation-item:last-child {
            border-bottom: none;
        }

        .donation-quantity {
            font-weight: 700;
            color: var(--primary);
        }

        .photos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .photo-item img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            border: 1px solid #eee;
        }

        /* --- 9. RESPONSIVE BREAKPOINTS --- */
        @media (max-width: 900px) {
            .ops-card {
                flex: 0 0 calc(50% - 10px);
            }
        }

        @media (max-width: 600px) {
            .ops-card {
                flex: 0 0 100%;
            }

            .slider-btn {
                width: 35px;
                height: 35px;
                font-size: 1.2rem;
            }

            .slider-btn.prev {
                left: 0;
            }

            .slider-btn.next {
                right: 0;
            }

            .donation-item {
                flex-direction: column;
                gap: 4px;
            }

            .category-title {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }
    </style>
</head>

<body>

    @include('partials.main-header')
    @include('administrator.partials.loading-screen')

    <header class="tracking-header">
        <div class="container">
            <h1 class="tracking-title">Donation Impact Tracker</h1>
            <p class="tracking-desc">
                Transparency is key. See exactly where in-kind donations are being distributed and track the remaining
                items needed to reach our campaign goals.
            </p>
        </div>
    </header>

    <main class="container">

        <section class="ops-section">
            <div class="ops-header">
                <h2 class="section-title">Recent Relief Operations</h2>
                <p class="body-font" style="color: var(--text-gray);">See how your in-kind donations are making a
                    tangible impact on the ground.</p>
            </div>

            <div class="container" style="position: relative;">
                <button class="slider-btn prev" id="sliderPrevBtn"><i class="ri-arrow-left-s-line"></i></button>
                <button class="slider-btn next" id="sliderNextBtn"><i class="ri-arrow-right-s-line"></i></button>

                <div class="ops-slider-wrapper">
                    <div class="ops-track" id="opsTrack">

                        @forelse($impactReports as $report)
                            <article class="ops-card" data-report-id="{{ $report->impact_report_id }}">
                                <div class="ops-img-container">
                                    @if ($report->photos && count($report->photos) > 0)
                                        <img src="{{ asset('storage/' . $report->photos[0]) }}"
                                            alt="{{ $report->title }}" class="ops-img">
                                    @else
                                        <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=600&auto=format&fit=crop"
                                            alt="{{ $report->title }}" class="ops-img">
                                    @endif
                                    <span class="ops-date-badge">
                                        {{ \Carbon\Carbon::parse($report->report_date)->format('M d, Y') }}
                                    </span>
                                </div>
                                <div class="ops-content">
                                    <h3 class="ops-title">{{ $report->title }}</h3>
                                    <p class="ops-desc">{{ Str::limit($report->description, 100) }}</p>
                                    <div class="ops-footer">
                                        <i class="ri-gift-line"></i>
                                        @if ($report->donations->count() > 0)
                                            {{ $report->donations->sum('quantity') }} Items Distributed
                                        @else
                                            No items recorded
                                        @endif
                                    </div>
                                    @if ($report->donations->count() > 0)
                                        <div class="ops-footer" style="margin-top: 8px; font-size: 0.85rem;">
                                            <i class="ri-user-line"></i>
                                            {{ $report->donations->unique('donor_name')->count() }} Donors
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @empty
                            @for ($i = 0; $i < 3; $i++)
                                <article class="ops-card">
                                    <div class="ops-img-container">
                                        <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=600&auto=format&fit=crop"
                                            alt="No reports yet" class="ops-img">
                                        <span class="ops-date-badge">Coming Soon</span>
                                    </div>
                                    <div class="ops-content">
                                        <h3 class="ops-title">No Reports Yet</h3>
                                        <p class="ops-desc">Impact reports will appear here once created.</p>
                                        <div class="ops-footer">
                                            <i class="ri-inbox-line"></i> Waiting for data
                                        </div>
                                    </div>
                                </article>
                            @endfor
                        @endforelse

                    </div>
                </div>
            </div>
        </section>

        <div class="dashboard-grid">
            <article class="card">
                <div class="card-header">
                    <i class="ri-map-2-line"></i>
                    <h2 class="card-title">Distribution Centers</h2>
                </div>

                <div class="location-container">
                    <ul class="location-list">
                        @forelse($dropOffPoints as $point)
                            <li class="location-item">
                                <div class="loc-details">
                                    <h4>{{ $point->name }}</h4>

                                    @if ($point->address)
                                        <p class="text-sm text-gray-600 mt-1">
                                            <i class="ri-map-pin-line mr-1"></i> {{ $point->address }}
                                        </p>
                                    @endif

                                    @if ($point->schedule_datetime)
                                        <p class="text-sm text-gray-600 mt-1">
                                            <i class="ri-calendar-line mr-1"></i>
                                            {{ $point->schedule_datetime }}
                                        </p>
                                    @endif

                                    <span class="status-badge {{ $point->is_active ? 'active' : 'inactive' }} mt-2">
                                        {{ $point->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </li>

                            @if (!$loop->last)
                                <hr style="border:0; border-top:1px solid #f1f5f9; margin: 10px 0;">
                            @endif
                        @empty
                            <li class="location-item">
                                <div class="loc-details">
                                    <h4>No distribution centers available</h4>
                                    <p class="text-sm text-gray-600 mt-1">Please check back later for updates.</p>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </article>
        </div>

        <div class="modal" id="reportModal" aria-hidden="true">
            <div class="modal-overlay" tabindex="-1" data-micromodal-close>
                <div class="modal-container" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
                    <header class="modal-header">
                        <h2 class="modal-title" id="modalTitle"></h2>
                        <button class="modal-close" aria-label="Close modal" data-micromodal-close>
                            <i class="ri-close-line"></i>
                        </button>
                    </header>
                    <div class="modal-body" id="modalBody">
                    </div>
                </div>
            </div>
        </div>

        <section class="cta-section">
            <h2 class="font-heading" style="margin-bottom: 0.5rem;">Can you fill these gaps?</h2>
            <p style="color: #64748b; margin-bottom: 1.5rem;">
                Every item counts. Click below to schedule your drop-off or pickup.
            </p>
            <a href="{{ route('inkindmodal') }}" class="cta-btn">
                Proceed to Donation Form <i class="ri-arrow-right-line" style="vertical-align: middle;"></i>
            </a>
        </section>

    </main>

    @include('partials.main-footer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const track = document.getElementById('opsTrack');
            const prevBtn = document.getElementById('sliderPrevBtn');
            const nextBtn = document.getElementById('sliderNextBtn');
            const cards = document.querySelectorAll('.ops-card');

            let currentIndex = 0;

            function getCardsPerView() {
                if (window.innerWidth <= 600) return 1;
                if (window.innerWidth <= 900) return 2;
                return 3;
            }

            function updateSlider() {
                if (!cards.length) return;
                const cardWidth = cards[0].offsetWidth;
                const gap = 20;

                const moveAmount = (cardWidth + gap) * currentIndex;
                track.style.transform = `translateX(-${moveAmount}px)`;

                const cardsPerView = getCardsPerView();
                const maxIndex = Math.max(0, cards.length - cardsPerView);

                prevBtn.disabled = currentIndex === 0;
                nextBtn.disabled = currentIndex >= maxIndex;
                prevBtn.style.opacity = currentIndex === 0 ? '0.5' : '1';
                nextBtn.style.opacity = currentIndex >= maxIndex ? '0.5' : '1';
            }

            if (nextBtn && prevBtn) {
                nextBtn.addEventListener('click', () => {
                    const cardsPerView = getCardsPerView();
                    const maxIndex = Math.max(0, cards.length - cardsPerView);
                    if (currentIndex < maxIndex) {
                        currentIndex++;
                        updateSlider();
                    }
                });

                prevBtn.addEventListener('click', () => {
                    if (currentIndex > 0) {
                        currentIndex--;
                        updateSlider();
                    }
                });
            }

            window.addEventListener('resize', () => {
                currentIndex = 0;
                updateSlider();
            });

            // Wait for images to load before calculating widths
            window.onload = updateSlider;
            updateSlider();
        });

        // Modal functionality
        const modal = document.getElementById('reportModal');
        let isModalOpen = false;

        function openModal() {
            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden';
            isModalOpen = true;
            modal.querySelector('.modal-close').focus();
        }

        function closeModal() {
            modal.classList.remove('is-open');
            document.body.style.overflow = 'auto';
            isModalOpen = false;
        }

        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });

        document.querySelector('.modal-close').addEventListener('click', closeModal);

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isModalOpen) closeModal();
        });

        async function loadReportDetails(reportId) {
            try {
                const response = await fetch(`/api/impact-reports/${reportId}`);
                const data = await response.json();

                document.getElementById('modalTitle').textContent = data.title;
                const modalBody = document.getElementById('modalBody');

                const donationsByCategory = {};
                if (data.donations && data.donations.length > 0) {
                    data.donations.forEach(donation => {
                        const category = donation.category || 'Uncategorized';
                        if (!donationsByCategory[category]) {
                            donationsByCategory[category] = [];
                        }
                        donationsByCategory[category].push(donation);
                    });
                }

                let donationsHTML = '';
                if (Object.keys(donationsByCategory).length > 0) {
                    donationsHTML = `
                <div class="modal-content-section">
                    <strong>Donated Items by Category:</strong>
                    ${Object.keys(donationsByCategory).map(category => {
                        const categoryDonations = donationsByCategory[category];
                        const totalQuantity = categoryDonations.reduce((sum, d) => sum + parseInt(d.quantity), 0);
                        return `
                                    <div class="category-group">
                                        <h4 class="category-title">
                                            <i class="ri-price-tag-3-line"></i> ${category}
                                            <span class="category-total">${totalQuantity} units total</span>
                                        </h4>
                                        <ul class="donations-list">
                                            ${categoryDonations.map(donation => `
                                            <li class="donation-item">
                                                <div class="donation-info">
                                                    <span class="donation-name">${donation.item_name}</span>
                                                    ${donation.donor_name ? `<small class="donation-donor"><i class="ri-user-line"></i> ${donation.donor_name}</small>` : ''}
                                                </div>
                                                <span class="donation-quantity">${donation.quantity} units</span>
                                            </li>
                                        `).join('')}
                                        </ul>
                                    </div>
                            `;
                    }).join('')}
                </div>`;
                } else {
                    donationsHTML = `
                <div class="modal-content-section">
                    <strong>Donated Items:</strong>
                    <div class="empty-state"><i class="ri-inbox-line"></i><p>No items recorded in this report.</p></div>
                </div>`;
                }

                let photosHTML = '';
                if (data.photos && data.photos.length > 0) {
                    photosHTML = `
                <div class="modal-content-section">
                    <strong>Report Photos (${data.photos.length}):</strong>
                    <div class="photos-grid">
                        ${data.photos.map(photo => `
                                <div class="photo-item"><img src="/storage/${photo}" alt="Report Photo" onclick="window.open('/storage/${photo}', '_blank')"></div>
                            `).join('')}
                    </div>
                </div>`;
                }

                modalBody.innerHTML = `
            <div class="modal-content-section">
                <div style="display:inline-block; background:var(--primary); color:white; padding:0.3rem 0.8rem; border-radius:6px; font-size:0.85rem;">
                    <i class="ri-calendar-line"></i> ${data.report_date_formatted}
                </div>
            </div>
            <div class="modal-content-section">
                <strong>Report Description:</strong>
                <p style="background:#f9f9f9; padding:10px; border-left:4px solid var(--primary);">${data.description}</p>
            </div>
            ${donationsHTML}
            ${photosHTML}
        `;
                openModal();
            } catch (error) {
                console.error('Error loading report:', error);
            }
        }

        document.querySelectorAll('.ops-card').forEach(card => {
            card.style.cursor = 'pointer';
            card.addEventListener('click', function() {
                const reportId = this.dataset.reportId;
                if (reportId) loadReportDetails(reportId);
            });
        });
    </script>
</body>

</html>
