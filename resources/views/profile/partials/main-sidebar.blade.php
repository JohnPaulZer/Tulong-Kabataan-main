<aside class="prof-sidebar" id="profSidebar">
    <nav>
        <a href="{{ route('profile') }}" 
           class="{{ request()->routeIs('profile') ? 'active' : '' }}">
            <i class="ri-user-line"></i>
            <span class="link-text">Profile</span>
        </a>

        <a href="{{ route('profile.dash') }}" 
           class="{{ request()->routeIs('profile.dash') ? 'active' : '' }}">
            <i class="ri-dashboard-line"></i>
            <span class="link-text">Campaign</span>
            <i class="ri-arrow-down-s-line caret" aria-hidden="true"></i>
        </a>

        <a href="{{ route('profile.event') }}" 
           class="{{ request()->routeIs('profile.event') ? 'active' : '' }}">
            <i class="ri-calendar-event-line"></i>
            <span class="link-text">Events</span>
            <i class="ri-arrow-down-s-line caret" aria-hidden="true"></i>
        </a>

        <a href="{{ route('profile.inkind') }}" 
           class="{{ request()->routeIs('profile.inkind') ? 'active' : '' }}">
            <i class="ri-gift-line"></i>
            <span class="link-text">In-Kind Donation</span>
            <i class="ri-arrow-down-s-line caret" aria-hidden="true"></i>
        </a>
    </nav>
</aside>
