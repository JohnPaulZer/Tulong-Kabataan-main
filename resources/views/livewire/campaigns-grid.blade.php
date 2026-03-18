<div wire:poll.{{ $pollingInterval }}ms>
    <!-- Top Bar -->
    <div class="tk-campaigns-toolbar">
        <span class="campaign-count">{{ $campaignCount }} campaigns found</span>
        <div class="tk-campaign-toolbar-actions">
            <div class="toolbar-sort">
                <label for="sort">Sort:</label>
                <select id="sort" wire:model.live="sort">
                    <option value="latest">Latest</option>
                    <option value="oldest">Oldest</option>
                    <option value="most_funded">Most Funded</option>
                    <option value="ending_soon">Ending Soon</option>
                </select>
            </div>
            <div class="toolbar-perpage">
                <select wire:model.live="perPage">
                    <option value="12">12 per page</option>
                    <option value="24">24 per page</option>
                    <option value="36">36 per page</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Campaign Cards Grid - Now using vertical layout -->
    <div class="tk-campaigns-grid">
        @foreach($campaigns as $campaign)
            <div class="tk-campaign-card" wire:key="campaign-{{ $campaign->id }}">
                <a href="{{ route('campaign.view', $campaign->campaign_id) }}">
                    <div class="campaign-img-wrap">
                        <img src="{{ $campaign->featured_image
                            ? asset('storage/' . $campaign->featured_image)
                            : asset('img/default-camp.jpg') }}"
                            alt="{{ $campaign->title }}"
                            class="campaign-img"
                            loading="lazy">
                        <span class="views-badge">
                            <i class="ri-eye-line"></i>
                            {{ number_format($campaign->views) }} views
                        </span>
                    </div>
                </a>

                <div class="tk-campaign-content">
                    <a href="{{ route('campaign.view', $campaign->campaign_id) }}">
                        <h3 class="campaign-title">{{ $campaign->title }}</h3>
                    </a>

                    @php
                        $progress = $campaign->target_amount > 0
                            ? ($campaign->current_amount / $campaign->target_amount) * 100
                            : 0;
                    @endphp
                    <div class="progress-container">
                        <div class="progress-bar-bg">
                            <div class="progress-bar" style="width: {{ $progress }}%"></div>
                        </div>
                        <p class="raised">
                            ₱{{ number_format($campaign->current_amount, 0) }} raised
                            <span style="color: #9ca3af;">of</span>
                            <span class="goal">₱{{ number_format($campaign->target_amount, 0) }} goal</span>
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
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
