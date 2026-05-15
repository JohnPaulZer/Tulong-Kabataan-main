<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign | Tulong Kabataan</title>
    <link rel="icon" href="{{ page_media_url('site_favicon', asset('img/log2.png')) }}" type="image/png">
    <!-- Remixicon -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="campaign-view-page" data-donation-route="{{ route('donations.store') }}" data-csrf-token="{{ csrf_token() }}"
    data-success-message="{{ session('success') }}" data-error-message="{{ session('error') }}">

    @include('partials.universalmodal')
    @include('partials.main-header')
    @include('administrator.partials.loading-screen')

    @php
        $coverImage = $campaign->featured_image
            ? file_url($campaign->featured_image)
            : page_media_url('campaign_default_image', asset('img/camp.jpg'));

        $targetAmount = (float) ($campaign->target_amount ?? 0);
        $currentAmount = (float) ($campaign->current_amount ?? 0);
        $progress = $targetAmount > 0 ? min(100, max(0, ($currentAmount / $targetAmount) * 100)) : 0;
        $progressLabel = number_format($progress, 0);

        $categoryLabel = $campaign->category ?? 'Community Project';

        if ($campaign->ends_at && now()->lt($campaign->ends_at)) {
            $daysLeft = max(0, (int) now()->startOfDay()->diffInDays($campaign->ends_at->copy()->startOfDay(), false));
            $daysLeftLabel = $daysLeft === 0 ? 'Ends today' : $daysLeft . ' days left';
        } elseif ($campaign->ends_at && now()->gte($campaign->ends_at)) {
            $daysLeftLabel = 'Ended';
        } elseif (($campaign->schedule_type ?? '') === 'recurring') {
            $daysLeftLabel = 'Recurring';
        } else {
            $daysLeftLabel = 'Ongoing';
        }

        $organizer = $campaign->organizer;
        $organizerName = $campaign->campaign_organizer
            ?: trim(($organizer->first_name ?? '') . ' ' . ($organizer->last_name ?? ''));
        $organizerName = $organizerName ?: 'Campaign Organizer';
        $organizerEmail = $organizer->email ?? null;

        $organizerAvatar = $organizer->profile_photo_url ?? null;
        if ($organizerAvatar && str_contains($organizerAvatar, 'googleusercontent.com') && !str_contains($organizerAvatar, 'sz=')) {
            $organizerAvatar .= (str_contains($organizerAvatar, '?') ? '&' : '?') . 'sz=96';
        }
        if ($organizerAvatar && !\Illuminate\Support\Str::startsWith($organizerAvatar, ['http://', 'https://'])) {
            $organizerAvatar = file_url($organizerAvatar);
        }

        $descriptionParagraphs = array_values(array_filter(
            preg_split('/\R{2,}/', trim((string) $campaign->description)) ?: [],
            fn ($paragraph) => trim($paragraph) !== ''
        ));
        $shareUrl = route('campaign.view', $campaign->campaign_id);
    @endphp

    <main class="mx-auto w-full max-w-[1180px] px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
        <div class="mb-6 flex items-center justify-between gap-4">
            <a href="{{ route('campaignpage') }}"
                class="inline-flex items-center gap-2 rounded-lg px-1 py-2 text-sm font-medium text-slate-600 no-underline transition hover:text-indigo-600 focus-visible:outline-[3px] focus-visible:outline-offset-[3px] focus-visible:outline-indigo-300">
                <i class="ri-arrow-left-line text-lg" aria-hidden="true"></i>
                <span>Back to Community Projects</span>
            </a>

            <div class="flex shrink-0 items-center gap-2">
                <button type="button"
                    class="campaign-share-btn inline-flex size-10 items-center justify-center rounded-full border border-slate-200 bg-white text-lg text-slate-600 shadow-[0_1px_2px_rgba(15,23,42,0.04)] transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600 focus-visible:outline-[3px] focus-visible:outline-offset-[3px] focus-visible:outline-indigo-300"
                    aria-label="Share this campaign" data-share-url="{{ $shareUrl }}"
                    data-share-title="{{ $campaign->title }}">
                    <i class="ri-share-line" aria-hidden="true"></i>
                </button>
                <button type="button"
                    class="inline-flex size-10 items-center justify-center rounded-full border border-slate-200 bg-white text-lg text-slate-600 shadow-[0_1px_2px_rgba(15,23,42,0.04)] transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 focus-visible:outline-[3px] focus-visible:outline-offset-[3px] focus-visible:outline-rose-200"
                    aria-label="Favorite this campaign">
                    <i class="ri-heart-line" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_370px] lg:items-start">
            <div class="min-w-0">
                <div class="mb-4">
                    <span
                        class="inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                        {{ $categoryLabel }}
                    </span>
                </div>

                <h1 class="max-w-3xl font-heading text-[clamp(1.85rem,4vw,2.75rem)] font-bold leading-tight text-slate-900">
                    {{ $campaign->title }}
                </h1>

                <figure class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_16px_38px_rgba(15,23,42,0.08)]">
                    <img src="{{ $coverImage }}" alt="{{ $campaign->title }} campaign cover"
                        class="aspect-[16/10] w-full object-cover sm:aspect-[16/9]" loading="eager" decoding="async">
                </figure>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
                        <i class="ri-eye-line text-indigo-600" aria-hidden="true"></i>
                        {{ number_format($campaign->views) }} Views
                    </span>
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
                        <i class="ri-team-line text-indigo-600" aria-hidden="true"></i>
                        {{ number_format($campaign->donor_count) }} Donors
                    </span>
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
                        <i class="ri-time-line text-indigo-600" aria-hidden="true"></i>
                        {{ $daysLeftLabel }}
                    </span>
                </div>

                <section class="mt-9">
                    <h2 class="font-heading text-2xl font-bold text-slate-900">About the Campaign</h2>
                    <div class="mt-4 space-y-4 text-[15px] leading-7 text-slate-600 sm:text-base">
                        @forelse ($descriptionParagraphs as $paragraph)
                            <p>{!! nl2br(e($paragraph)) !!}</p>
                        @empty
                            <p>No campaign description has been added yet.</p>
                        @endforelse
                    </div>
                </section>

                <section class="mt-10">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="font-heading text-2xl font-bold text-slate-900">Latest Updates</h2>
                        @if ($campaign->updates->count() > 0)
                            <span class="text-sm font-medium text-slate-500">{{ $campaign->updates->count() }} total</span>
                        @endif
                    </div>

                    @forelse($campaign->updates->take(1) as $update)
                        @php
                            $updateAvatar = $update->organizer ? $update->organizer->profile_photo_url : null;
                            if ($updateAvatar && str_contains($updateAvatar, 'googleusercontent.com') && !str_contains($updateAvatar, 'sz=')) {
                                $updateAvatar .= (str_contains($updateAvatar, '?') ? '&' : '?') . 'sz=64';
                            }
                            if ($updateAvatar && !\Illuminate\Support\Str::startsWith($updateAvatar, ['http://', 'https://'])) {
                                $updateAvatar = file_url($updateAvatar);
                            }
                        @endphp

                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_10px_26px_rgba(15,23,42,0.06)]">
                            <div class="cpv-update-header mb-4">
                                <div class="cpv-update-author flex items-center gap-3">
                                    @if ($updateAvatar)
                                        <img src="{{ $updateAvatar }}"
                                            alt="{{ $update->organizer->first_name ?? 'Organizer' }}"
                                            class="size-11 rounded-full object-cover ring-2 ring-white" referrerpolicy="no-referrer">
                                    @else
                                        <img src="{{ page_media_url('site_favicon', asset('img/log2.png')) }}"
                                            alt="{{ $update->organizer->first_name ?? 'Organizer' }}"
                                            class="size-11 rounded-full object-cover ring-2 ring-white">
                                    @endif
                                    <div class="min-w-0">
                                        <p class="cpv-update-author-info flex flex-wrap items-center gap-2 text-sm font-bold text-slate-800">
                                            {{ $update->organizer->first_name ?? 'Organizer' }}
                                            <span
                                                class="cpv-update-organizer-badge rounded-full bg-indigo-50 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-normal text-indigo-700">Organizer</span>
                                        </p>
                                        <p class="cpv-update-date mt-1 text-xs text-slate-500">
                                            @if ($update->created_at->diffInMinutes(now()) <= 5)
                                                <span class="font-semibold text-rose-600">New</span>
                                            @else
                                                {{ $update->created_at->diffForHumans() }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="cpv-update-content">
                                <p class="cpv-update-text text-[15px] leading-7 text-slate-600">{{ $update->message }}</p>

                                @if ($update->images && is_array($update->images) && count($update->images) > 0)
                                    <div class="cpv-update-images {{ count($update->images) === 1 ? 'single-image' : '' }}"
                                        data-update-id="{{ $update->update_id }}">
                                        <div class="cpv-update-images-carousel">
                                            @foreach ($update->images as $image)
                                                <div class="cpv-update-image">
                                                    <img src="{{ file_url($image) }}" alt="Campaign update image"
                                                        onclick="showImageModal('{{ file_url($image) }}')" />
                                                </div>
                                            @endforeach
                                        </div>

                                        @if (count($update->images) > 1)
                                            <div class="cpv-carousel-nav">
                                                <button class="cpv-carousel-btn prev-btn"
                                                    onclick="moveCarousel('{{ $update->update_id }}', -1)"
                                                    aria-label="Previous update image">
                                                    &lsaquo;
                                                </button>
                                                <button class="cpv-carousel-btn next-btn"
                                                    onclick="moveCarousel('{{ $update->update_id }}', 1)"
                                                    aria-label="Next update image">
                                                    &rsaquo;
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-slate-200 bg-white/80 p-8 text-center shadow-[0_8px_22px_rgba(15,23,42,0.04)]">
                            <div class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-indigo-50 text-3xl text-indigo-600">
                                <i class="ri-megaphone-line" aria-hidden="true"></i>
                            </div>
                            <h3 class="mt-4 font-heading text-lg font-bold text-slate-900">No updates yet</h3>
                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                                The organizer has not posted any updates yet. Check back soon for campaign progress.
                            </p>
                        </div>
                    @endforelse

                    @if ($campaign->updates->count() > 1)
                        <button class="cpv-see-more-updates mt-4 inline-flex min-h-11 w-full items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-indigo-600 transition hover:border-indigo-200 hover:bg-indigo-50 focus-visible:outline-[3px] focus-visible:outline-offset-[3px] focus-visible:outline-indigo-300"
                            onclick="showAllUpdatesModal()">See more updates</button>
                    @endif
                </section>
            </div>

            <aside class="space-y-5 lg:sticky lg:top-[112px]">
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_14px_34px_rgba(15,23,42,0.08)]">
                    <p class="font-heading text-3xl font-bold text-indigo-700">&#8369;{{ number_format($campaign->current_amount, 0) }}</p>
                    <p class="mt-1 text-sm text-slate-500">raised of &#8369;{{ number_format($campaign->target_amount, 0) }} goal</p>

                    <div class="mt-4" aria-label="{{ $progressLabel }} percent funded">
                        <div class="mb-2 flex items-center justify-between text-xs font-medium text-slate-500">
                            <span>{{ $progressLabel }}% funded</span>
                            <span>{{ number_format(max(0, 100 - $progress), 0) }}% to go</span>
                        </div>
                        <div class="h-2.5 overflow-hidden rounded-full bg-slate-200">
                            <div class="h-full rounded-full bg-indigo-600 transition-all duration-700"
                                style="width: {{ $progress }}%;"></div>
                        </div>
                    </div>

                    <button type="button"
                        class="show-donation-modal mt-5 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 font-heading text-sm font-bold text-white shadow-[0_12px_22px_rgba(79,70,229,0.24)] transition hover:bg-indigo-700 focus-visible:outline-[3px] focus-visible:outline-offset-[3px] focus-visible:outline-indigo-300">
                        <i class="ri-hand-heart-line text-lg" aria-hidden="true"></i>
                        Donate Now
                    </button>

                    <p class="mt-4 flex items-center justify-center gap-2 text-center text-xs font-medium text-slate-500">
                        <i class="ri-shield-check-line text-indigo-600" aria-hidden="true"></i>
                        Donations are reviewed and protected by Tulong Kabataan.
                    </p>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_10px_26px_rgba(15,23,42,0.06)]">
                    <h2 class="text-sm font-bold text-slate-900">Organizer</h2>
                    <div class="mt-4 flex items-center gap-3">
                        @if ($organizerAvatar)
                            <img src="{{ $organizerAvatar }}" alt="{{ $organizerName }}"
                                class="size-12 rounded-full object-cover ring-2 ring-indigo-50" referrerpolicy="no-referrer">
                        @else
                            <div class="flex size-12 items-center justify-center rounded-full bg-indigo-50 text-xl font-bold text-indigo-700">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($organizerName, 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-slate-900">{{ $organizerName }}</p>
                            <p class="text-xs text-slate-500">Campaign Organizer</p>
                        </div>
                    </div>

                    @if ($organizerEmail)
                        <a href="mailto:{{ $organizerEmail }}"
                            class="mt-4 inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 no-underline transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700 focus-visible:outline-[3px] focus-visible:outline-offset-[3px] focus-visible:outline-indigo-300">
                            <i class="ri-mail-line" aria-hidden="true"></i>
                            Contact Organizer
                        </a>
                    @else
                        <button type="button"
                            class="mt-4 inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-500"
                            disabled>
                            <i class="ri-mail-line" aria-hidden="true"></i>
                            Contact Organizer
                        </button>
                    @endif
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_10px_26px_rgba(15,23,42,0.06)]">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <h2 class="text-sm font-bold text-slate-900">Recent Donations</h2>
                        <span class="text-xs font-medium text-slate-500">{{ number_format($campaign->donor_count) }} total</span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($campaign->donations->take(5) as $donation)
                            @php
                                $donorName = $donation->is_anonymous
                                    ? 'Anonymous'
                                    : ($donation->donor_name ?? (optional($donation->user)->first_name ?? 'Anonymous'));
                                $donorInitials = collect(explode(' ', trim($donorName)))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn ($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
                                    ->implode('');
                                $donorInitials = $donorInitials ?: 'D';
                            @endphp
                            <div class="flex items-center justify-between gap-3 py-3">
                                <div class="flex min-w-0 items-center gap-3">
                                    <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                                        {{ $donorInitials }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-slate-900">{{ $donorName }}</p>
                                        <p class="text-xs text-slate-500">{{ $donation->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <p class="shrink-0 text-sm font-bold text-indigo-700">&#8369;{{ number_format($donation->amount, 0) }}</p>
                            </div>
                        @empty
                            <div class="py-6 text-center">
                                <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-400">
                                    <i class="ri-inbox-line" aria-hidden="true"></i>
                                </div>
                                <p class="mt-3 text-sm font-medium text-slate-500">No donations yet</p>
                            </div>
                        @endforelse
                    </div>

                    <button class="cpv-show-more-comments mt-3 inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg border border-transparent px-4 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50 focus-visible:outline-[3px] focus-visible:outline-offset-[3px] focus-visible:outline-indigo-300">
                        <i class="ri-arrow-right-line" aria-hidden="true"></i>
                        See all donations
                    </button>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white/85 p-4 shadow-[0_6px_18px_rgba(15,23,42,0.04)]">
                    <div class="flex gap-3">
                        <div class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                            <i class="ri-lock-2-line" aria-hidden="true"></i>
                        </div>
                        <p class="text-xs leading-5 text-slate-500">
                            Donation proofs and reference numbers are checked before campaign records are finalized.
                        </p>
                    </div>
                </section>
            </aside>
        </div>

        <!-- All Updates Modal -->
        <div id="allUpdatesModal" class="modal-overlay" style="display: none;">
            <div class="modal-content all-updates-modal">
                <button class="modal-close" onclick="closeAllUpdatesModal()" aria-label="Close all updates modal">&times;</button>
                <h2>All Updates ({{ $campaign->updates->count() }})</h2>

                <div class="all-updates-container">
                    @foreach ($campaign->updates as $update)
                        @php
                            $updateAvatar = $update->organizer ? $update->organizer->profile_photo_url : null;
                            if ($updateAvatar && str_contains($updateAvatar, 'googleusercontent.com') && !str_contains($updateAvatar, 'sz=')) {
                                $updateAvatar .= (str_contains($updateAvatar, '?') ? '&' : '?') . 'sz=64';
                            }
                            if ($updateAvatar && !\Illuminate\Support\Str::startsWith($updateAvatar, ['http://', 'https://'])) {
                                $updateAvatar = file_url($updateAvatar);
                            }
                        @endphp

                        <div class="cpv-update-item">
                            <div class="cpv-update-header">
                                <div class="cpv-update-author">
                                    @if ($updateAvatar)
                                        <img src="{{ $updateAvatar }}"
                                            alt="{{ $update->organizer->first_name ?? 'Organizer' }}"
                                            referrerpolicy="no-referrer" />
                                    @else
                                        <img src="{{ page_media_url('site_favicon', asset('img/log2.png')) }}"
                                            alt="{{ $update->organizer->first_name ?? 'Organizer' }}" />
                                    @endif
                                    <div>
                                        <p class="cpv-update-author-info">
                                            {{ $update->organizer->first_name ?? 'Organizer' }}
                                            <span class="cpv-update-organizer-badge">Organizer</span>
                                        </p>
                                        <p class="cpv-update-date">{{ $update->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="cpv-update-content">
                                <p class="cpv-update-text">{{ $update->message }}</p>

                                @if ($update->images && is_array($update->images) && count($update->images) > 0)
                                    <div class="cpv-update-images {{ count($update->images) === 1 ? 'single-image' : '' }}"
                                        data-update-id="{{ $update->update_id }}">
                                        <div class="cpv-update-images-carousel">
                                            @foreach ($update->images as $image)
                                                <div class="cpv-update-image">
                                                    <img src="{{ file_url($image) }}" alt="Campaign update image"
                                                        onclick="showImageModal('{{ file_url($image) }}')" />
                                                </div>
                                            @endforeach
                                        </div>

                                        @if (count($update->images) > 1)
                                            <div class="cpv-carousel-nav">
                                                <button class="cpv-carousel-btn prev-btn"
                                                    onclick="moveCarousel('{{ $update->update_id }}', -1)"
                                                    aria-label="Previous update image">
                                                    &lsaquo;
                                                </button>
                                                <button class="cpv-carousel-btn next-btn"
                                                    onclick="moveCarousel('{{ $update->update_id }}', 1)"
                                                    aria-label="Next update image">
                                                    &rsaquo;
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Image Modal -->
        <div id="imageModal" class="modal-overlay" style="display: none;">
            <div class="modal-content image-modal">
                <button class="modal-close" onclick="closeImageModal()" aria-label="Close image modal">&times;</button>
                <div class="image-modal-container">
                    <img id="modalImage" src="" alt="Enlarged update image" />
                </div>
            </div>
        </div>
    </main>

    @include('partials.main-footer')

    <!-- Donation Modal -->
    <div id="donationModal" class="modal-overlay" style="display:none;">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2>Donate to {{ $campaign->title }}</h2>

            <div class="qr-wrapper">
                <img src="{{ $campaign->qr_code ? file_url($campaign->qr_code) : page_media_url('default_placeholder_image', asset('img/camp.jpg')) }}"
                    alt="GCash QR Code" class="qr-image">
            </div>

            <!-- GCash Number with Copy Icon -->
            <div class="gcash-number-container">
                <span class="gcash-number">{{ $campaign->gcash_number }}</span>
                <button class="copy-icon" id="copyGcashBtn" title="Copy to clipboard">
                    <i class="ri-file-copy-line"></i>
                </button>
            </div>

            <p class="donation-note">
                Send your donation via QR code or GCash number
            </p>

            <button id="alreadyPaidBtn" class="donation-next-btn">I Already Paid</button>
        </div>
    </div>

    <!-- Proof Upload Modal (2nd step) -->
    <div id="proofModal" class="modal-overlay" style="display:none;">

        <div class="modal-content">
            <button type="button" class="back-to-qr-btn">
                <i class="ri-arrow-left-line"></i>
            </button>
            <span class="modal-close">&times;</span>
            <h2>Upload Donation Proof</h2>

            <form method="POST" action="{{ route('donations.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="campaign_id" value="{{ $campaign->campaign_id }}">

                <label>Amount Donated (PHP)</label>
                <input type="number" name="amount" step="0.01" min="1" required
                    onkeydown="return preventInvalidInput(event)" oninput="sanitizeAmount(this)">

                <label>Reference Number</label>
                <div class="reference-input-container">
                    <input type="text" name="reference_number" required id="referenceInput"
                        inputmode="numeric" pattern="[0-9]{13}" maxlength="13"
                        oninput="checkReferenceNumber(this.value)">
                    <div id="referenceStatus" class="reference-status"></div>
                    <small id="referenceError" class="reference-error">
                        Reference number has already been used
                    </small>
                </div>

                <label>Upload Screenshot</label>
                <input type="file" name="proof_image" accept="image/*" required>

                @guest
                    <label>Your Name</label>
                    <input type="text" name="donor_name" required>
                    <label>Your Email (optional)</label>
                    <input type="email" name="donor_email">
                @endguest

                <label style="display:flex; align-items:center; gap:6px; margin-top:8px;">
                    <input type="checkbox" name="is_anonymous" value="1" id="anonymousCheckbox">
                    Donate anonymously
                </label>

                <button type="submit" class="donation-submit-btn" id="submitBtn">Submit Proof</button>
            </form>
        </div>
    </div>

    <!-- All Donations Modal -->
    <div id="allDonationsModal" class="modal-overlay" style="display:none;">
        <div class="modal-content all-donations-modal">
            <span class="modal-close">&times;</span>
            <h2>All Donations</h2>

            <div class="donations-list-container">
                @forelse($campaign->donations as $donation)
                    @php
                        $donorName = $donation->is_anonymous
                            ? 'Anonymous'
                            : ($donation->donor_name ?? (optional($donation->user)->first_name ?? 'Anonymous'));
                    @endphp
                    <div class="donation-modal-item">
                        <div class="donation-modal-profile">
                            <div class="donor-avatar">
                                <i class="ri-hand-heart-line"></i>
                            </div>
                            <div class="donation-modal-info">
                                <p class="donation-modal-name">{{ $donorName }}</p>
                                <p class="donation-modal-date">
                                    {{ $donation->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <div class="donation-modal-amount">&#8369;{{ number_format($donation->amount, 2) }}</div>
                    </div>
                @empty
                    <div class="no-donations-message">
                        <i class="ri-inbox-line"></i>
                        <p>No donations yet</p>
                    </div>
                @endforelse
            </div>

            <div class="donations-summary">
                <p>Total Donations: <strong>&#8369;{{ number_format($campaign->current_amount, 2) }}</strong></p>
                <p>Total Donors: <strong>{{ $campaign->donor_count }}</strong></p>
            </div>
        </div>
    </div>

</body>

<script src="{{ asset('js/campaignview/seemodal-script.js') }}"></script>
<script src="{{ asset('js/campaignview/donation-validation.js') }}"></script>
<script src="{{ asset('js/campaignview/donatemodal-script.js') }}"></script>
<script src="{{ asset('js/campaignview/image-modal.js') }}"></script>
<script src="{{ asset('js/campaignview/updates-modal.js') }}"></script>
<script src="{{ asset('js/campaignview/updates-carousel.js') }}"></script>
<script>
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
                if (typeof showToast === 'function') {
                    showToast('Campaign link copied', 'success');
                }
            } catch (error) {
                console.error('Unable to share campaign link:', error);
            }
        });
    });
</script>

</html>
