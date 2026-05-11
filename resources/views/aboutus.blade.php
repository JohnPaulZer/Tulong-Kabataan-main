<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About Us | Tulong Kabataan</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png" />
    <!-- Remixicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="about-page">
    @include('administrator.partials.loading-screen')

    <!-- Navigation & Header -->
    @include('partials.main-header')

    <main class="about-main">
        <!-- Header -->
        <header class="page-header relative overflow-hidden">
            <div class="container relative z-10">
                <h1>About us</h1>
                <p>We are Tulong Kabataan, a youth-led organization dedicated to empowering young Filipinos through
                    education, advocacy, and community service. Our mission is to provide support, resources, and
                    opportunities for the next generation to become proactive leaders and changemakers in their
                    communities.</p>
            </div>
            @include('partials.wave-divider')
        </header>

        <!-- Our Story / Introduction -->
        <section id="our-story" class="about-story-section section-padding container">
            <div class="intro-section reveal">
                <div class="intro-content">
                    <span class="section-subtitle">How It Started</span>
                    <h2>Our Dream is Youth-Led Community Transformation</h2>
                    <p>Tulong Kabataan started as a small initiative by a group of passionate students wanting to bridge
                        the gap in local education and resource distribution. Today, we have grown into a nationwide
                        network.</p>
                    <p>We believe that the youth are not just the leaders of tomorrow, but the partners of today. Our
                        programs focus on leadership development, disaster response, and educational assistance.</p>
                </div>

                <div class="intro-side">
                    <div class="intro-image">
                        <img src="{{ asset('img/diss.jpg') }}" alt="Youth volunteers working together">
                    </div>
                    <div class="about-story-stats">
                        <div class="about-story-stat">
                            <strong>{{ $impactReports->count() }}</strong>
                            <span>Impact Reports</span>
                        </div>
                        <div class="about-story-stat">
                            <strong>{{ $endedEvents->count() }}</strong>
                            <span>Events Completed</span>
                        </div>
                        <div class="about-story-stat">
                            <strong>{{ $endedCampaigns->count() }}</strong>
                            <span>Campaigns Completed</span>
                        </div>
                        <div class="about-story-stat">
                            <strong>100%</strong>
                            <span>Community Focused</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="about-support-content reveal">
                <div class="about-support-copy">
                    <span class="section-subtitle">What Guides Us</span>
                    <h2>Built on trust, service, and youth action</h2>
                </div>
                <div class="about-support-details">
                    <div class="about-values-list">
                        <div class="about-value-item">
                            <i class="ri-check-double-line" aria-hidden="true"></i>
                            <span>Community-driven initiatives</span>
                        </div>
                        <div class="about-value-item">
                            <i class="ri-check-double-line" aria-hidden="true"></i>
                            <span>Transparent and accountable</span>
                        </div>
                        <div class="about-value-item">
                            <i class="ri-check-double-line" aria-hidden="true"></i>
                            <span>Inclusive volunteer network</span>
                        </div>
                    </div>
                    <p class="about-story-note">Every initiative is built with local partners, youth volunteers, and transparent reporting so supporters can see where their help creates change.</p>
                </div>
            </div>
        </section>

        <!-- Accomplishment Reports Grid -->
        <section class="about-reports-section section-padding container">

            <div class="about-section-heading text-center reveal">
                <span class="section-subtitle">2024 Highlights</span>
                <h2>Making A Difference Together</h2>
                <p>Explore the reports, events, and campaigns that reflect how youth volunteers and donors continue to serve communities with purpose.</p>
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
                                    <img src="{{ file_url($firstPhoto) }}" alt="{{ $report->title }}"
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
                                    <img src="{{ file_url($event->photo) }}" alt="{{ $event->title }}"
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
                                    <img src="{{ file_url($campaign->featured_image) }}"
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
                                    <img src="{{ file_url($firstImage) }}" alt="{{ $campaign->title }}"
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

        <section class="about-cta container">
            <div class="about-cta-card reveal">
                <h2>Be part of the next story of change</h2>
                <p>Your time, skills, and support can help us reach more young people and families in need.</p>
                <div class="about-cta-actions">
                    <a href="{{ route('campaignpage') }}" class="about-cta-btn primary">
                        <i class="ri-heart-add-line" aria-hidden="true"></i> Support a Campaign
                    </a>
                    <a href="{{ route('event.page') }}" class="about-cta-btn secondary">
                        <i class="ri-group-line" aria-hidden="true"></i> Join an Event
                    </a>
                </div>
            </div>
        </section>

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
