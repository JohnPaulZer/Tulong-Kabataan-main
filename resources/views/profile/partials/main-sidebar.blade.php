<style>
    body.profile-page,
    body.profile-dashboard-page,
    body.profile-events-page,
    body.profile-inkind-page {
        --profile-sidebar-w: 256px;
        --profile-shell-header-offset: 90px;
    }

    @media (min-width: 1024px) {
        body.profile-page:has(.header.is-hidden),
        body.profile-dashboard-page:has(.header.is-hidden),
        body.profile-events-page:has(.header.is-hidden),
        body.profile-inkind-page:has(.header.is-hidden) {
            --profile-shell-header-offset: 0px;
        }
    }

    @media (max-width: 1023px) {
        body.profile-page,
        body.profile-dashboard-page,
        body.profile-events-page,
        body.profile-inkind-page {
            --profile-shell-header-offset: 64px;
        }
    }

    @media (max-width: 767px) {
        body.profile-page,
        body.profile-dashboard-page,
        body.profile-events-page,
        body.profile-inkind-page {
            --profile-shell-header-offset: 58px;
        }
    }

    /* --- LAYOUT WRAPPER --- */
    .prof-flex,
    .usedash-flex,
    .usereve-prof-flex,
    .ikd-prof-flex {
        display: flex;
        width: 100%;
        box-sizing: border-box;
    }

    /* --- SIDEBAR (Desktop Default) --- */
    .prof-sidebar {
        width: var(--profile-sidebar-w);
        background: var(--white);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        height: calc(100vh - var(--profile-shell-header-offset));
        height: calc(100dvh - var(--profile-shell-header-offset));
        position: fixed;
        top: var(--profile-shell-header-offset);
        left: 0;
        overflow-y: auto;
        transition: width 0.25s ease, top 0.52s cubic-bezier(0.22, 1, 0.36, 1), height 0.52s cubic-bezier(0.22, 1, 0.36, 1);
        z-index: 10;
    }

    .prof-sidebar nav {
        margin-top: 30px;
    }

    .prof-sidebar a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 24px;
        text-decoration: none;
        color: var(--gray-600);
        border-radius: 8px;
        margin: 4px 16px;
        transition: background 0.15s ease, color 0.15s ease, padding 0.2s ease;
        white-space: nowrap;
        border-right: 4px solid transparent; 
    }

    .prof-sidebar a i {
        font-size: 18px;
    }

    .prof-sidebar a .caret {
        margin-left: auto;
        font-size: 18px;
        opacity: 0.7;
    }

    /* Active & Hover States */
    .prof-sidebar a.active {
        background: rgba(59, 130, 246, 0.1);
        border-right-color: var(--primary);
        color: var(--primary);
    }

    .prof-sidebar a:not(.active):hover {
        background: var(--gray-50);
        color: var(--primary);
    }

    /* --- MAIN CONTENT (Desktop Default) --- */
    .prof-main {
        /* Matches the desktop sidebar width */
        margin-left: var(--profile-sidebar-w); 
        padding: var(--profile-page-pad-y, 32px) var(--profile-page-pad-x, 36px) 42px;
        flex: 1;
        width: calc(100% - var(--profile-sidebar-w)); /* Explicit width calculation */
        box-sizing: border-box;
        transition: margin-left 0.25s ease, width 0.25s ease;
    }

    /* --- TABLET & MOBILE --- */
    @media (max-width: 1023px) {
        .prof-sidebar {
            display: none;
        }

        .prof-main {
            margin-left: 0;
            width: 100%;
            padding: var(--profile-page-pad-y, 22px) var(--profile-page-pad-x, 16px) calc(104px + env(safe-area-inset-bottom));
        }
    }
</style>
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
