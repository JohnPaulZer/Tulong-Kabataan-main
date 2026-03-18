<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Accomplishment Report | Tulong Kabataan</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png" />
    <!-- Remixicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" />
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/landingpage.css') }}" />
    <style>
        /* * GLOBAL VARIABLES & RESET */
        :root {
            --primary-color: #003366;
            /* Deep Navy Blue */
            --primary-dark: #002244;
            /* Darker Navy */
            --secondary-color: #4a90e2;
            /* Bright Light Blue */
            --text-dark: #1e293b;
            /* Slate 800 */
            --text-light: #64748b;
            /* Slate 500 */
            --white: #ffffff;
            --bg-light: #f8fafc;
            /* Very pale blue-gray */
            --transition: all 0.3s ease;
            --font-heading: 'Poppins', sans-serif;
            --font-body: 'Lato', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            color: var(--text-dark);
            line-height: 1.6;
            background-color: var(--bg-light);
            overflow-x: hidden;
        }

        /* Utility Classes */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section-padding {
            padding: 80px 0;
        }

        .text-center {
            text-align: center;
        }

        h1,
        h2,
        h3,
        h4 {
            font-family: var(--font-heading);
            color: var(--primary-color);
            font-weight: 700;
        }

        p {
            color: var(--text-light);
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: var(--secondary-color);
            color: var(--white);
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: var(--primary-color);
            transform: translateY(-2px);
        }

        /* * HERO SECTION */
        .page-header {
            background: linear-gradient(rgba(0, 51, 102, 0.9), rgba(0, 51, 102, 0.8)), url('https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2670&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            padding: 100px 0 60px;
            text-align: center;
            color: var(--white);
        }

        .page-header h1 {
            color: var(--white);
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .page-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* * OUR STORY / INTRO SECTION */
        .intro-section {
            display: flex;
            align-items: center;
            gap: 50px;
            flex-wrap: wrap;
        }

        .intro-image {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .intro-image img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .intro-image::before {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            width: 100px;
            height: 100px;
            background-color: var(--secondary-color);
            z-index: -1;
            border-radius: 50%;
            opacity: 0.3;
        }

        .intro-content {
            flex: 1;
            min-width: 300px;
        }

        .section-subtitle {
            color: var(--secondary-color);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.9rem;
            margin-bottom: 10px;
            display: block;
        }

        .intro-content h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            line-height: 1.2;
            color: var(--primary-color);
        }

        /* * ACCOMPLISHMENT REPORT GRID */
        .report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .report-card {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1);
        }

        .report-image {
            width: 100%;
            height: 220px;
            overflow: hidden;
            position: relative;
        }

        .report-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .report-card:hover .report-image img {
            transform: scale(1.05);
        }

        .report-content {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .report-date {
            font-size: 0.85rem;
            color: var(--secondary-color);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            display: block;
        }

        .report-content h3 {
            font-size: 1.4rem;
            margin-bottom: 12px;
            color: var(--primary-color);
            line-height: 1.3;
        }

        .report-content p {
            font-size: 0.95rem;
            margin-bottom: 20px;
            color: var(--text-light);
        }

        .report-footer {
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .report-tag {
            background: #eef2f7;
            color: var(--primary-color);
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* * SIMPLE CTA SECTION */
        .simple-cta {
            background-color: var(--white);
            border-top: 1px solid #e2e8f0;
            padding: 60px 0;
            text-align: center;
        }

        .simple-cta h2 {
            margin-bottom: 15px;
            font-size: 2rem;
        }

        .simple-cta p {
            max-width: 500px;
            margin: 0 auto 30px;
        }



        /* * ANIMATIONS */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* * RESPONSIVE */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2.2rem;
            }

            .report-grid {
                grid-template-columns: 1fr;
            }

            .intro-section {
                flex-direction: column;
            }

            .intro-image,
            .intro-content {
                width: 100%;
            }

            .intro-content h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    @include('administrator.partials.loading-screen')

    <!-- Navigation & Header -->
    @include('partials.main-header')

    <main>
        <!-- Header -->
        <header class="page-header">
            <div class="container">
                <h1>About us</h1>
                <p>We are Tulong Kabataan, a youth-led organization dedicated to empowering young Filipinos through
                    education, advocacy, and community service. Our mission is to provide support, resources, and
                    opportunities for the next generation to become proactive leaders and changemakers in their
                    communities.</p>
            </div>
        </header>

        <!-- Our Story / Introduction -->
        <section id="our-story" class="section-padding container">
            <div class="intro-section reveal">
                <div class="intro-image">
                    <img src="{{ asset('img/diss.jpg') }}" alt="Youth volunteers working together">
                </div>
                <div class="intro-content">
                    <span class="section-subtitle">Who We Are</span>
                    <h2>Driving Change in Our Communities</h2>
                    <p>Tulong Kabataan started as a small initiative by a group of passionate students wanting to bridge
                        the gap in local education and resource distribution. Today, we have grown into a nationwide
                        network.</p>
                    <p>We believe that the youth are not just the leaders of tomorrow, but the partners of today. Our
                        programs focus on leadership development, disaster response, and educational assistance.</p>

                    <div style="margin-top: 20px;">
                        <div style="display: flex; gap: 15px; margin-bottom: 10px;">
                            <i class="ri-check-double-line"
                                style="color: var(--secondary-color); font-size: 1.2rem;"></i>
                            <span>Community-driven initiatives</span>
                        </div>
                        <div style="display: flex; gap: 15px; margin-bottom: 10px;">
                            <i class="ri-check-double-line"
                                style="color: var(--secondary-color); font-size: 1.2rem;"></i>
                            <span>Transparent and accountable</span>
                        </div>
                        <div style="display: flex; gap: 15px;">
                            <i class="ri-check-double-line"
                                style="color: var(--secondary-color); font-size: 1.2rem;"></i>
                            <span>Inclusive volunteer network</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Accomplishment Reports Grid -->
        <section class="section-padding container">

            <div class="text-center reveal" style="margin-bottom: 40px;">
                <span style="color: var(--secondary-color); font-weight: 700; letter-spacing: 1px;">2024
                    HIGHLIGHTS</span>
                <h2>Making A Difference Together</h2>
            </div>

            <div class="report-grid">
                <!-- Impact Reports -->
                @foreach ($impactReports as $report)
                    <article class="report-card reveal">
                        <div class="report-image">
                            @if (!empty($report->photos) && is_array($report->photos))
                                @php
                                    // Get first photo from array
                                    $firstPhoto = is_array($report->photos) ? $report->photos[0] : $report->photos;
                                @endphp
                                @if (Str::startsWith($firstPhoto, ['http://', 'https://']))
                                    <img src="{{ $firstPhoto }}" alt="{{ $report->title }}">
                                @else
                                    <img src="{{ Storage::url($firstPhoto) }}" alt="{{ $report->title }}"
                                        onerror="this.src='https://images.unsplash.com/photo-1469571486292-0ba58a3f068b?q=80&w=2670&auto=format&fit=crop'">
                                @endif
                            @else
                                <img src="https://images.unsplash.com/photo-1469571486292-0ba58a3f068b?q=80&w=2670&auto=format&fit=crop"
                                    alt="{{ $report->title }}">
                            @endif
                        </div>
                        <div class="report-content">
                            <span class="report-date">
                                {{ $report->created_at->format('F d, Y') }}
                            </span>
                            <h3>{{ $report->title ?? 'Untitled Report' }}</h3>
                            <p>{{ Str::limit($report->description ?? 'No description available', 150) }}</p>
                            <div class="report-footer">
                                <span class="report-tag">Impact Report</span>
                                @if ($report->donations && $report->donations->count() > 0)
                                    <span class="report-tag">{{ $report->donations->count() }} donations</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach

                <!-- Ended Events -->
                @foreach ($endedEvents as $event)
                    <article class="report-card reveal">
                        <div class="report-image">
                            @if ($event->photo)
                                @if (Str::startsWith($event->photo, ['http://', 'https://']))
                                    <img src="{{ $event->photo }}" alt="{{ $event->title }}">
                                @else
                                    <img src="{{ Storage::url($event->photo) }}" alt="{{ $event->title }}"
                                        onerror="this.src='https://images.unsplash.com/photo-1544027993-37dbfe43562a?q=80&w=2670&auto=format&fit=crop'">
                                @endif
                            @else
                                <img src="https://images.unsplash.com/photo-1544027993-37dbfe43562a?q=80&w=2670&auto=format&fit=crop"
                                    alt="{{ $event->title }}">
                            @endif
                        </div>
                        <div class="report-content">
                            <span class="report-date">
                                {{ $event->created_at->format('F d, Y') }}
                            </span>
                            <h3>{{ $event->title ?? 'Untitled Event' }}</h3>
                            <p>{{ Str::limit($event->description ?? 'No description available', 150) }}</p>
                            <div class="report-footer">
                                <span class="report-tag">Event</span>
                                @if ($event->location)
                                    <span class="report-tag">{{ $event->location }}</span>
                                @endif
                                @if ($event->registrations && $event->registrations->count() > 0)
                                    <span class="report-tag">{{ $event->registrations->count() }} volunteers</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach

                <!-- Ended Campaigns -->
                @foreach ($endedCampaigns as $campaign)
                    <article class="report-card reveal">
                        <div class="report-image">
                            @if ($campaign->featured_image)
                                @if (Str::startsWith($campaign->featured_image, ['http://', 'https://']))
                                    <img src="{{ $campaign->featured_image }}" alt="{{ $campaign->title }}">
                                @else
                                    <img src="{{ Storage::url($campaign->featured_image) }}"
                                        alt="{{ $campaign->title }}"
                                        onerror="this.src='https://images.unsplash.com/photo-1593113598332-cd288d649433?q=80&w=2670&auto=format&fit=crop'">
                                @endif
                            @elseif(!empty($campaign->images) && is_array($campaign->images))
                                @php
                                    $firstImage = $campaign->images[0];
                                @endphp
                                @if (Str::startsWith($firstImage, ['http://', 'https://']))
                                    <img src="{{ $firstImage }}" alt="{{ $campaign->title }}">
                                @else
                                    <img src="{{ Storage::url($firstImage) }}" alt="{{ $campaign->title }}"
                                        onerror="this.src='https://images.unsplash.com/photo-1593113598332-cd288d649433?q=80&w=2670&auto=format&fit=crop'">
                                @endif
                            @else
                                <img src="https://images.unsplash.com/photo-1593113598332-cd288d649433?q=80&w=2670&auto=format&fit=crop"
                                    alt="{{ $campaign->title }}">
                            @endif
                        </div>
                        <div class="report-content">
                            <span class="report-date">
                                {{ $campaign->created_at->format('F d, Y') }}
                            </span>
                            <h3>{{ $campaign->title ?? 'Untitled Campaign' }}</h3>
                            <p>{{ Str::limit($campaign->description ?? 'No description available', 150) }}</p>
                            <div class="report-footer">
                                <span class="report-tag">Campaign</span>
                                @if ($campaign->status === 'completed')
                                    <span class="report-tag success">Completed</span>
                                @endif
                                <span class="report-tag">₱{{ number_format($campaign->current_amount ?? 0, 0) }}
                                    raised</span>
                                @if ($campaign->donor_count && $campaign->donor_count > 0)
                                    <span class="report-tag">{{ $campaign->donor_count }} donors</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach

                <!-- If no data exists, show a message -->
                @if ($impactReports->isEmpty() && $endedEvents->isEmpty() && $endedCampaigns->isEmpty())
                    <div class="no-data-message">
                        <h3>No reports available yet</h3>
                        <p>Check back soon for updates on our activities and impact reports!</p>
                    </div>
                @endif
            </div>
        </section>

        <!-- Simple CTA Section -->

    </main>

    @include('partials.main-footer')

    <div id="modal-container"></div>

    <!-- Scripts for Scroll Animation -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const reveals = document.querySelectorAll('.reveal');

            const revealOnScroll = () => {
                const windowHeight = window.innerHeight;
                const elementVisible = 100;

                reveals.forEach((reveal) => {
                    const elementTop = reveal.getBoundingClientRect().top;
                    if (elementTop < windowHeight - elementVisible) {
                        reveal.classList.add('active');
                    }
                });
            };

            window.addEventListener('scroll', revealOnScroll);
            revealOnScroll(); // Trigger once on load
        });
    </script>
</body>

</html>
