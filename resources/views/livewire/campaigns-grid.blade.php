<div>
    @php
        $visibleCampaigns = $campaigns->count();
    @endphp

    <!-- Top Bar -->
    <div class="tk-campaigns-toolbar">
        <span class="campaign-count">
            <i class="ri-search-line"></i>
            {{ $campaignCount }} {{ \Illuminate\Support\Str::plural('campaign', $campaignCount) }} found
        </span>
        <div class="tk-campaign-toolbar-actions">
            <div class="toolbar-field toolbar-sort">
                <label for="sort">
                    <i class="ri-arrow-down-s-line"></i>
                    Sort
                </label>
                <div class="toolbar-select-wrap">
                    <select id="sort" wire:model.live="sort">
                        <option value="latest">Latest</option>
                        <option value="oldest">Oldest</option>
                        <option value="most_funded">Most Funded</option>
                        <option value="ending_soon">Ending Soon</option>
                    </select>
                </div>
            </div>
            <div class="toolbar-field toolbar-perpage">
                <label for="perPage">
                    <i class="ri-apps-2-line"></i>
                    Show
                </label>
                <div class="toolbar-select-wrap">
                    <select id="perPage" wire:model.live="perPage">
                        <option value="12">12 per page</option>
                        <option value="24">24 per page</option>
                        <option value="36">36 per page</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Cards Grid -->
    <div @class([
        'tk-campaigns-grid',
        'tk-campaigns-grid--compact' => $visibleCampaigns > 0 && $visibleCampaigns < 3,
        'tk-campaigns-grid--empty' => $visibleCampaigns === 0,
    ])>
        @forelse($campaigns as $campaign)
            @php
                $targetAmount = (float) ($campaign->target_amount ?? 0);
                $currentAmount = (float) ($campaign->current_amount ?? 0);
                $progress = $targetAmount > 0
                    ? min(100, max(0, ($currentAmount / $targetAmount) * 100))
                    : 0;
                $progressLabel = number_format($progress, 0);
                $categoryLabel = $campaign->category ?? 'Community Project';
                $organizerName = $campaign->campaign_organizer ?: 'Campaign Organizer';
                $organizerInitials = collect(explode(' ', trim($organizerName)))
                    ->filter()
                    ->take(2)
                    ->map(fn ($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
                    ->implode('');
                $organizerInitials = $organizerInitials ?: 'TK';
                $views = (int) ($campaign->views ?? 0);
                $viewsLabel = $views >= 1000000
                    ? rtrim(rtrim(number_format($views / 1000000, 1), '0'), '.') . 'M'
                    : ($views >= 1000
                        ? rtrim(rtrim(number_format($views / 1000, 1), '0'), '.') . 'K'
                        : number_format($views));
                $description = trim(strip_tags((string) ($campaign->description ?? '')));
                $excerpt = $description
                    ? \Illuminate\Support\Str::limit($description, 112)
                    : 'Open this campaign to learn more about the support needed.';
                $donorCount = (int) ($campaign->donor_count ?? 0);
                $donorLabel = $donorCount === 1 ? '1 donor' : number_format($donorCount) . ' donors';
            @endphp

            <article class="tk-campaign-card" wire:key="campaign-{{ $campaign->id }}">
                <a class="campaign-card-media-link" href="{{ route('campaign.view', $campaign->campaign_id) }}">
                    <div class="campaign-img-wrap">
                        <img src="{{ $campaign->featured_image
                            ? file_url($campaign->featured_image)
                            : asset('img/camp.jpg') }}"
                            alt="{{ $campaign->title }}"
                            class="campaign-img"
                            loading="lazy">
                        <span class="views-badge">
                            <i class="ri-eye-line campaign-card-icon"></i>
                            {{ $viewsLabel }}
                        </span>
                    </div>
                </a>

                <div class="tk-campaign-content">
                    <div class="campaign-card-kicker">
                        <span class="campaign-category">{{ $categoryLabel }}</span>
                    </div>

                    <a class="campaign-title-link" href="{{ route('campaign.view', $campaign->campaign_id) }}">
                        <h3 class="campaign-title">{{ $campaign->title }}</h3>
                    </a>

                    <p class="campaign-excerpt">{{ $excerpt }}</p>

                    <div class="progress-container">
                        <div class="campaign-raised-row">
                            <div>
                                <span class="campaign-stat-label">Raised</span>
                                <p class="raised">&#8369;{{ number_format($currentAmount, 0) }}</p>
                            </div>
                            <span class="campaign-percent">{{ $progressLabel }}%</span>
                        </div>
                        <div class="progress-bar-bg" role="progressbar" aria-valuenow="{{ $progressLabel }}"
                            aria-valuemin="0" aria-valuemax="100"
                            aria-label="{{ $progressLabel }}% funded">
                            <div class="progress-bar" style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="campaign-goal-row">
                            <p class="goal">Goal: &#8369;{{ number_format($targetAmount, 0) }}</p>
                            <span class="campaign-donors">{{ $donorLabel }}</span>
                        </div>
                    </div>

                    <div class="campaign-card-footer">
                        <div class="campaign-organizer">
                            <span class="organizer-avatar">{{ $organizerInitials }}</span>
                            <span class="organizer-name">{{ $organizerName }}</span>
                        </div>
                        <a class="campaign-donate-link" href="{{ route('campaign.view', $campaign->campaign_id) }}"
                            aria-label="Donate to {{ $campaign->title }}">
                            <i class="ri-heart-fill campaign-card-icon"></i>
                            <span>Donate</span>
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="campaign-empty-state">
                <div class="campaign-empty-icon">
                    <i class="ri-heart-add-line"></i>
                </div>
                <h3>No active campaigns yet</h3>
                <p>New community campaigns will appear here as soon as they are published.</p>
            </div>
        @endforelse
    </div>

    <!-- Modern Pagination -->
    @if($campaigns->hasPages())
    <div class="modern-pagination">
        <div class="pagination-info">
            Showing <strong>{{ $campaigns->firstItem() ?? 0 }}-{{ $campaigns->lastItem() ?? 0 }}</strong> of {{ $campaigns->total() }}
        </div>

        <div class="pagination-controls">
            <!-- Previous Button -->
            @if($campaigns->onFirstPage())
                <span class="pagination-btn pagination-btn--disabled">
                    <i class="ri-arrow-left-s-line"></i>
                </span>
            @else
                <button wire:click="previousPage" class="pagination-btn pagination-btn--prev" onclick="handlePaginationClick()">
                    <i class="ri-arrow-left-s-line"></i>
                </button>
            @endif

            <!-- Page Numbers -->
            <div class="pagination-numbers">
                @foreach ($campaigns->links()->elements[0] as $page => $url)
                    @if ($page == $campaigns->currentPage())
                        <span class="pagination-number pagination-number--active">{{ $page }}</span>
                    @else
                        <button wire:click="gotoPage({{ $page }})" class="pagination-number" onclick="handlePaginationClick()">{{ $page }}</button>
                    @endif
                @endforeach
            </div>

            <!-- Next Button -->
            @if($campaigns->hasMorePages())
                <button wire:click="nextPage" class="pagination-btn pagination-btn--next" onclick="handlePaginationClick()">
                    <i class="ri-arrow-right-s-line"></i>
                </button>
            @else
                <span class="pagination-btn pagination-btn--disabled">
                    <i class="ri-arrow-right-s-line"></i>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>

<script>
function scrollToTop() {
    const container = document.getElementById('campaigns-container');
    if (container) {
        container.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

document.addEventListener('livewire:init', function() {
    // Flag to indicate pagination was clicked
    let paginationClicked = false;

    // Set flag when pagination buttons are clicked
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination-btn') ||
            e.target.closest('.pagination-number')) {
            paginationClicked = true;
        }
    });

    // Scroll when Livewire updates after pagination click
    Livewire.hook('message.processed', (message, component) => {
        if (paginationClicked && component && component.name === 'campaigns-grid') {
            setTimeout(() => {
                scrollToTop();
                paginationClicked = false;
            }, 200);
        }
    });
});


function handlePaginationClick() {
    setTimeout(scrollToTop, 450);
}
</script>
