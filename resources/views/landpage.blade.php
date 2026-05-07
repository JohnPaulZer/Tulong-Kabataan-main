<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tulong Kabataan</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="land-page m-0 overflow-x-hidden bg-gray-50 font-body text-slate-900 antialiased">
    @include('administrator.partials.loading-screen')
    @include('partials.main-header')

    <main id="main-content" class="overflow-x-hidden">
        <section class="tk-land-hero relative isolate flex min-h-[calc(100svh-60px)] items-start overflow-hidden md:min-h-[700px] md:items-center" aria-labelledby="homepage-hero-title">
            <div class="tk-land-hero-bg absolute inset-0 -z-10 overflow-hidden">
                <img class="h-full w-full object-cover" src="{{ asset('img/bg1.jpg') }}"
                    alt="Community volunteers preparing relief support" decoding="async" fetchpriority="high" />
                <div class="tk-land-overlay absolute inset-0 bg-white/80"></div>
            </div>
            <div class="tk-land-shapes" aria-hidden="true">
                <span class="tk-land-shape"></span>
                <span class="tk-land-shape"></span>
                <span class="tk-land-shape"></span>
            </div>

            <div class="tk-land-hero-content relative z-10 mx-auto flex w-full max-w-[900px] flex-col items-center justify-center gap-8 px-4 pb-8 pt-16 text-center sm:px-6 sm:pt-20 md:px-8 md:py-24">
                <div class="max-w-[700px]">
                    <h1 class="font-heading text-[clamp(30px,9vw,40px)] font-bold leading-[1.12] text-gray-900 md:mb-6"
                        id="homepage-hero-title">Make a Difference Today</h1>
                    <p class="mx-auto mt-4 max-w-[36ch] text-base leading-relaxed text-gray-600 md:mt-0 md:max-w-[700px] md:text-xl">
                        Join thousands of donors and volunteers who are changing lives through our platform. Every
                        contribution matters.
                    </p>
                    <div class="mx-auto mt-6 grid w-full max-w-[380px] grid-cols-1 gap-2.5 sm:flex sm:max-w-none sm:justify-center sm:gap-4 md:mt-8">
                        <a href="{{ route('campaignpage') }}"
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-8 py-3 font-heading text-base font-semibold text-white no-underline transition hover:-translate-y-0.5 hover:shadow-[0_10px_22px_rgba(79,70,229,0.18)] focus-visible:outline-[3px] focus-visible:outline-offset-[3px] focus-visible:outline-indigo-300">
                            <i class="ri-heart-add-line" aria-hidden="true"></i> Start Donating
                        </a>
                        <a href="{{ route('event.page') }}"
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border-2 border-indigo-600 bg-white px-8 py-3 font-heading text-base font-semibold text-indigo-600 no-underline transition hover:-translate-y-0.5 hover:shadow-[0_10px_22px_rgba(79,70,229,0.18)] focus-visible:outline-[3px] focus-visible:outline-offset-[3px] focus-visible:outline-indigo-300">
                            <i class="ri-group-line" aria-hidden="true"></i> Join an Event
                        </a>
                    </div>
                </div>

                <div class="grid w-full grid-cols-2 gap-3 md:grid-cols-3 md:gap-6" aria-label="Tulong Kabataan impact statistics">
                    <div class="flex min-h-[124px] flex-col items-center justify-center rounded-lg bg-white/90 px-3 py-4 text-center shadow-[0_4px_24px_rgba(0,0,0,0.07)] transition hover:-translate-y-1 hover:shadow-[0_16px_32px_rgba(15,23,42,0.12)] md:min-h-40 md:px-4 md:py-6">
                        <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 text-2xl text-indigo-600 md:mb-4"
                            aria-hidden="true"><i class="ri-hand-heart-line"></i></div>
                        <div class="font-heading text-2xl font-bold text-gray-900 md:text-3xl">
                            &#8369;{{ $homepageStats['total_donations'] }}</div>
                        <div class="text-sm text-gray-600 md:text-base">Total Donations Raised</div>
                    </div>
                    <div class="flex min-h-[124px] flex-col items-center justify-center rounded-lg bg-white/90 px-3 py-4 text-center shadow-[0_4px_24px_rgba(0,0,0,0.07)] transition hover:-translate-y-1 hover:shadow-[0_16px_32px_rgba(15,23,42,0.12)] md:min-h-40 md:px-4 md:py-6">
                        <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 text-2xl text-indigo-600 md:mb-4"
                            aria-hidden="true"><i class="ri-team-line"></i></div>
                        <div class="font-heading text-2xl font-bold text-gray-900 md:text-3xl">
                            {{ $homepageStats['active_volunteers'] }}</div>
                        <div class="text-sm text-gray-600 md:text-base">Active Volunteers</div>
                    </div>
                    <div class="tk-land-stat col-span-2 flex min-h-[124px] w-full rounded-lg bg-white/90 px-3 py-4 text-center shadow-[0_4px_24px_rgba(0,0,0,0.07)] transition hover:-translate-y-1 hover:shadow-[0_16px_32px_rgba(15,23,42,0.12)] md:col-span-1 md:min-h-40 md:px-4 md:py-6">
                        <div class="flex w-full flex-col items-center justify-center">
                            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 text-2xl text-indigo-600 md:mb-4"
                                aria-hidden="true"><i class="ri-heart-line"></i></div>
                            <div class="font-heading text-2xl font-bold text-gray-900 md:text-3xl">
                                {{ $homepageStats['successful_campaigns'] }}</div>
                            <div class="text-sm text-gray-600 md:text-base">Successful Campaigns</div>
                        </div>
                    </div>
                </div>
            </div>
            @include('partials.wave-divider')
        </section>

        <section class="tk-land-impact mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8" aria-labelledby="impact-title">
            <div class="mx-auto mb-10 max-w-3xl text-center md:mb-16">
                <h2 class="font-heading text-[clamp(28px,8vw,40px)] font-bold leading-tight text-slate-800"
                    id="impact-title">Our Impact in Disaster Relief</h2>
                <p class="mx-auto mt-4 max-w-3xl text-base leading-relaxed text-slate-600 md:text-lg">
                    Together with our community, we've made significant strides in helping communities recover and
                    rebuild from natural disasters.
                </p>
            </div>

            <div class="flex flex-wrap items-start justify-center gap-10">
                <div class="tk-land-image-card flex max-w-[600px] flex-1 basis-[600px] items-center justify-center md:pt-12">
                    <img class="h-auto w-full rounded-lg object-cover shadow-[0_4px_24px_rgba(0,0,0,0.07)]"
                        src="{{ asset('img/diss.jpg') }}" alt="Volunteers distributing disaster relief supplies"
                        loading="lazy" decoding="async">
                </div>
                <div class="grid min-w-0 flex-1 basis-[500px] gap-8">
                    <div class="grid grid-cols-2 gap-3 md:gap-5" aria-label="Disaster relief commitments">
                        <div class="flex min-h-[108px] flex-col items-start justify-center rounded-lg bg-indigo-50 p-3 text-left transition hover:-translate-y-1 hover:shadow-[0_12px_24px_rgba(79,70,229,0.12)] md:min-h-[120px] md:p-6">
                            <div class="font-heading text-xl font-bold leading-tight text-indigo-600 md:text-2xl">Hope Delivered</div>
                            <div class="mt-1 text-sm leading-relaxed text-black md:text-lg">For twelve thousand families, hope was delivered.</div>
                        </div>
                        <div class="flex min-h-[108px] flex-col items-start justify-center rounded-lg bg-indigo-50 p-3 text-left transition hover:-translate-y-1 hover:shadow-[0_12px_24px_rgba(79,70,229,0.12)] md:min-h-[120px] md:p-6">
                            <div class="font-heading text-xl font-bold leading-tight text-indigo-600 md:text-2xl">Our Mission</div>
                            <div class="mt-1 text-sm leading-relaxed text-black md:text-lg">Bicolano youth, ready to help fellow Bicolanos.</div>
                        </div>
                        <div class="flex min-h-[108px] flex-col items-start justify-center rounded-lg bg-indigo-50 p-3 text-left transition hover:-translate-y-1 hover:shadow-[0_12px_24px_rgba(79,70,229,0.12)] md:min-h-[120px] md:p-6">
                            <div class="font-heading text-xl font-bold leading-tight text-indigo-600 md:text-2xl">Swift Response</div>
                            <div class="mt-1 text-sm leading-relaxed text-black md:text-lg">In times of need, we are never late.</div>
                        </div>
                        <div class="flex min-h-[108px] flex-col items-start justify-center rounded-lg bg-indigo-50 p-3 text-left transition hover:-translate-y-1 hover:shadow-[0_12px_24px_rgba(79,70,229,0.12)] md:min-h-[120px] md:p-6">
                            <div class="font-heading text-xl font-bold leading-tight text-indigo-600 md:text-2xl">Community Reach</div>
                            <div class="mt-1 text-sm leading-relaxed text-black md:text-lg">The aid of the youth, within reach of every community.</div>
                        </div>
                    </div>

                    <div class="grid gap-3 md:mt-6">
                        <div class="flex items-start gap-3 py-1">
                            <i class="ri-home-heart-line mt-1 shrink-0 text-[22px] text-indigo-500" aria-hidden="true"></i>
                            <div>
                                <h3 class="font-heading text-base font-semibold text-black md:text-lg">Immediate Shelter Support</h3>
                                <p class="mt-1 text-sm leading-relaxed text-black">Provided temporary housing and essential supplies to displaced families within 24 hours.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 py-1">
                            <i class="ri-medicine-bottle-line mt-1 shrink-0 text-[22px] text-indigo-500" aria-hidden="true"></i>
                            <div>
                                <h3 class="font-heading text-base font-semibold text-black md:text-lg">Medical Assistance</h3>
                                <p class="mt-1 text-sm leading-relaxed text-black">Deployed mobile medical units and supplied essential medications to affected areas.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 py-1">
                            <i class="ri-community-line mt-1 shrink-0 text-[22px] text-indigo-500" aria-hidden="true"></i>
                            <div>
                                <h3 class="font-heading text-base font-semibold text-black md:text-lg">Community Rebuilding</h3>
                                <p class="mt-1 text-sm leading-relaxed text-black">Coordinated long-term reconstruction efforts with local organizations and volunteers.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tk-land-cta basis-full rounded-lg bg-indigo-800 p-5 text-center text-white md:p-8">
                    <h3 class="font-heading text-2xl font-bold">Ready to Make a Difference?</h3>
                    <p class="mt-3 leading-relaxed">Join our emergency response network and help communities when they need it most.</p>
                    <div class="mx-auto mt-5 grid max-w-[420px] gap-2.5 sm:flex sm:max-w-none sm:justify-center sm:gap-4">
                        <a href="{{ route('campaignpage') }}"
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border-2 border-white bg-white px-8 py-3 font-heading text-base font-semibold text-indigo-600 no-underline transition hover:-translate-y-0.5 hover:shadow-[0_10px_22px_rgba(79,70,229,0.18)] focus-visible:outline-[3px] focus-visible:outline-offset-[3px] focus-visible:outline-indigo-300">
                            <i class="ri-money-dollar-circle-line" aria-hidden="true"></i> Donate Now
                        </a>
                        <a href="{{ route('event.page') }}"
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border-2 border-white bg-transparent px-8 py-3 font-heading text-base font-semibold text-white no-underline transition hover:-translate-y-0.5 hover:shadow-[0_10px_22px_rgba(79,70,229,0.18)] focus-visible:outline-[3px] focus-visible:outline-offset-[3px] focus-visible:outline-indigo-300">
                            <i class="ri-group-line" aria-hidden="true"></i> Join as Volunteer
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="tk-land-section-soft bg-white py-12 md:py-16" aria-labelledby="featured-campaigns-title">
            <div class="mx-auto mb-8 flex max-w-7xl flex-col items-start justify-between gap-3 px-4 sm:px-6 md:flex-row md:items-center lg:px-8">
                <div>
                    <h2 class="font-heading text-3xl font-bold text-slate-800" id="featured-campaigns-title">Featured Campaigns</h2>
                    <p class="mt-1 text-[15px] text-slate-500 md:text-base">Support active campaigns with the highest views and donations.</p>
                </div>
                <a href="{{ route('campaignpage') }}" class="font-heading font-bold text-indigo-600 no-underline transition hover:text-indigo-800">View all campaigns</a>
            </div>

            <div class="mx-auto flex max-w-7xl snap-x gap-4 overflow-x-auto px-4 pb-2 sm:px-6 md:grid md:grid-cols-2 md:gap-8 md:overflow-visible md:px-6 md:pb-0 lg:px-8">
                @forelse ($featuredCampaigns as $campaign)
                    @php
                        $imageUrl = $campaign->featured_image
                            ? asset('storage/' . $campaign->featured_image)
                            : asset('img/camp.jpg');
                        $progress = $campaign->target_amount > 0
                            ? min(100, round(($campaign->current_amount / $campaign->target_amount) * 100))
                            : 0;
                        $daysLeft = $campaign->ends_at ? (int) now()->diffInDays($campaign->ends_at, false) : null;
                        $timeLabel = is_null($daysLeft)
                            ? 'Open campaign'
                            : ($daysLeft < 0 ? 'Ended' : ($daysLeft === 0 ? 'Ends today' : $daysLeft . ' days left'));
                        $badgeClass = !is_null($daysLeft) && $daysLeft >= 0 && $daysLeft <= 7 ? 'urgent' : '';
                        $badgeLabel = $badgeClass ? 'Urgent' : ucfirst($campaign->status);
                        $layoutClass = '';

                        if ($loop->count === 3 && $loop->last) {
                            $layoutClass = 'md:col-span-2 md:w-[calc((100%_-_2rem)/2)] md:min-w-[min(340px,100%)] md:justify-self-center';
                        } elseif ($loop->count === 1) {
                            $layoutClass = 'md:col-span-2 md:max-w-[540px] md:justify-self-center';
                        }
                    @endphp

                    <article
                        class="flex w-[min(86vw,340px)] shrink-0 snap-start flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-[0_2px_8px_rgba(15,23,42,0.04)] transition hover:-translate-y-1.5 hover:border-indigo-200 hover:shadow-[0_18px_36px_rgba(15,23,42,0.12)] md:w-full md:shrink {{ $layoutClass }}">
                        <a href="{{ route('campaign.view', $campaign->campaign_id) }}" class="group block overflow-hidden"
                            aria-label="View campaign {{ $campaign->title }}">
                            <img class="h-[190px] w-full object-cover transition duration-200 group-hover:scale-[1.04] md:h-[200px]"
                                src="{{ $imageUrl }}" alt="{{ $campaign->title }} campaign image" loading="lazy"
                                decoding="async" />
                        </a>
                        <div class="flex flex-1 flex-col p-4 md:p-5">
                            <div class="mb-2 flex flex-wrap items-start justify-between gap-2">
                                <span @class([
                                    'inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs',
                                    'bg-blue-100 text-blue-800' => $badgeClass,
                                    'bg-indigo-100 text-indigo-800' => ! $badgeClass,
                                ])>
                                    <i class="ri-megaphone-line" aria-hidden="true"></i> {{ $badgeLabel }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    <i class="ri-timer-line" aria-hidden="true"></i> {{ $timeLabel }}
                                </span>
                            </div>
                            <h3 class="font-heading text-lg font-bold leading-snug md:text-xl">
                                <a href="{{ route('campaign.view', $campaign->campaign_id) }}" class="text-slate-800 no-underline transition hover:text-indigo-600">{{ $campaign->title }}</a>
                            </h3>
                            <p class="mt-2 text-[15px] leading-relaxed text-gray-600">
                                {{ \Illuminate\Support\Str::limit(strip_tags($campaign->description), 130) }}</p>
                            <div class="mt-auto pt-4" aria-label="{{ $progress }} percent funded">
                                <div class="mb-2 h-2 overflow-hidden rounded-lg bg-gray-200">
                                    <div class="h-2 rounded-l-lg bg-indigo-600" style="width: {{ $progress }}%;"></div>
                                </div>
                                <div class="flex flex-wrap justify-between gap-x-3 gap-y-1 text-sm text-gray-500">
                                    <span>&#8369;{{ number_format($campaign->current_amount, 0) }} raised</span>
                                    <span>of &#8369;{{ number_format($campaign->target_amount, 0) }} goal</span>
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-[minmax(0,1fr)_44px] items-stretch gap-2">
                                <a href="{{ route('campaign.view', $campaign->campaign_id) }}"
                                    class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 font-heading text-[15px] font-semibold text-white no-underline transition hover:-translate-y-0.5 hover:shadow-[0_10px_22px_rgba(79,70,229,0.18)] focus-visible:outline-[3px] focus-visible:outline-offset-[3px] focus-visible:outline-indigo-300">
                                    <i class="ri-heart-add-line" aria-hidden="true"></i> Donate Now
                                </a>
                                <button type="button"
                                    class="campaign-share-btn inline-flex min-h-[42px] items-center justify-center rounded-lg border border-gray-200 bg-transparent text-lg text-gray-700 transition hover:-translate-y-0.5 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600 focus-visible:outline-[3px] focus-visible:outline-offset-[3px] focus-visible:outline-indigo-300"
                                    aria-label="Share {{ $campaign->title }}"
                                    data-share-url="{{ route('campaign.view', $campaign->campaign_id) }}"
                                    data-share-title="{{ $campaign->title }}">
                                    <i class="ri-share-line" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="mx-auto w-full max-w-2xl rounded-lg border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-slate-500 md:col-span-2">
                        <h3 class="font-heading text-xl font-bold text-slate-800">No active campaigns yet</h3>
                        <p class="mt-2">New featured campaigns will appear here once they are published.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="tk-land-section-soft bg-white px-4 py-8 sm:px-6 md:py-16" aria-labelledby="trust-title">
            <div class="mx-auto mb-4 max-w-2xl text-center md:mb-10">
                <h2 class="font-heading text-3xl font-bold text-slate-800" id="trust-title">Your Trust & Security Matter</h2>
                <p class="mx-auto mt-3 max-w-xl text-[15px] leading-relaxed text-gray-600 md:text-base">
                    We're committed to creating a secure platform where you can donate and volunteer with confidence.
                </p>
            </div>
            <div class="mx-auto grid max-w-5xl grid-cols-2 gap-3 md:grid-cols-4 md:gap-8">
                <div class="rounded-lg bg-slate-100 px-3 py-4 text-center transition hover:-translate-y-1 hover:bg-white hover:shadow-[0_16px_32px_rgba(15,23,42,0.1)] md:p-8">
                    <i class="ri-shield-check-line mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-2xl text-indigo-600 md:mb-4 md:h-12 md:w-12 md:text-3xl"
                        aria-hidden="true"></i>
                    <h3 class="font-heading text-sm font-semibold md:text-[17px]">Secure Payments</h3>
                    <p class="mt-1 text-xs leading-relaxed text-gray-600 md:text-base">All transactions are encrypted and processed through trusted payment gateways.</p>
                </div>
                <div class="rounded-lg bg-slate-100 px-3 py-4 text-center transition hover:-translate-y-1 hover:bg-white hover:shadow-[0_16px_32px_rgba(15,23,42,0.1)] md:p-8">
                    <i class="ri-user-follow-line mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-2xl text-indigo-600 md:mb-4 md:h-12 md:w-12 md:text-3xl"
                        aria-hidden="true"></i>
                    <h3 class="font-heading text-sm font-semibold md:text-[17px]">Verified Campaigns</h3>
                    <p class="mt-1 text-xs leading-relaxed text-gray-600 md:text-base">All campaigns undergo thorough verification before being published on our platform.</p>
                </div>
                <div class="rounded-lg bg-slate-100 px-3 py-4 text-center transition hover:-translate-y-1 hover:bg-white hover:shadow-[0_16px_32px_rgba(15,23,42,0.1)] md:p-8">
                    <i class="ri-eye-line mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-2xl text-indigo-600 md:mb-4 md:h-12 md:w-12 md:text-3xl"
                        aria-hidden="true"></i>
                    <h3 class="font-heading text-sm font-semibold md:text-[17px]">Transparent Reporting</h3>
                    <p class="mt-1 text-xs leading-relaxed text-gray-600 md:text-base">Track exactly where your donations go and the impact they make in real-time.</p>
                </div>
                <div class="rounded-lg bg-slate-100 px-3 py-4 text-center transition hover:-translate-y-1 hover:bg-white hover:shadow-[0_16px_32px_rgba(15,23,42,0.1)] md:p-8">
                    <i class="ri-lock-line mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-2xl text-indigo-600 md:mb-4 md:h-12 md:w-12 md:text-3xl"
                        aria-hidden="true"></i>
                    <h3 class="font-heading text-sm font-semibold md:text-[17px]">Data Protection</h3>
                    <p class="mt-1 text-xs leading-relaxed text-gray-600 md:text-base">Your personal information is protected with industry-standard security measures.</p>
                </div>
            </div>
        </section>
    </main>

    @include('partials.main-footer')

    <div id="modal-container"></div>

    <script>
        document.querySelectorAll('a[href^="#"]').forEach((link) => {
            link.addEventListener('click', (event) => {
                const target = document.querySelector(link.getAttribute('href'));

                if (!target) {
                    return;
                }

                event.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        document.querySelectorAll('.campaign-share-btn').forEach((button) => {
            button.addEventListener('click', async () => {
                const shareUrl = button.dataset.shareUrl;
                const shareTitle = button.dataset.shareTitle || 'Tulong Kabataan campaign';

                try {
                    if (navigator.share) {
                        await navigator.share({ title: shareTitle, url: shareUrl });
                        return;
                    }

                    await navigator.clipboard.writeText(shareUrl);
                    button.setAttribute('aria-label', 'Campaign link copied');
                } catch (error) {
                    console.error('Unable to share campaign link:', error);
                }
            });
        });
    </script>
</body>

</html>
