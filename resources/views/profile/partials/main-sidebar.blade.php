<style>
    /* --- LAYOUT WRAPPER --- */
    .prof-flex {
        display: flex;
        width: 100%;
        box-sizing: border-box;
    }

    /* --- SIDEBAR (Desktop Default) --- */
    .prof-sidebar {
        width: 256px;
        background: var(--white);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        height: calc(100vh - 70px);
        height: calc(100dvh - 70px);
        position: fixed;
        top: 70px;
        left: 0;
        overflow-y: auto;
        transition: width 0.25s ease;
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
        margin-left: 256px; 
        padding: 24px;
        padding-top: 32px; /* Extra top space usually looks better */
        flex: 1;
        width: calc(100% - 256px); /* Explicit width calculation */
        box-sizing: border-box;
        transition: margin-left 0.25s ease, width 0.25s ease;
    }

    /* --- TABLET & MOBILE (Max-width: 768px) --- */
    @media (max-width: 768px) {
        /* Sidebar shrinks to icon-only */
        .prof-sidebar {
            width: 64px;
            border-right: 1px solid var(--gray-200);
        }

        .prof-sidebar nav {
            margin-top: 50px;
        }

        .prof-sidebar a {
            gap: 0;
            justify-content: center;
            padding: 12px;
            margin: 6px;
            border-radius: 12px;
        }

        .prof-sidebar a i:first-child {
            margin: 0;
        }

        .prof-sidebar a .link-text,
        .prof-sidebar a .caret {
            display: none;
        }

        /* Sidebar Expansion on Hover/Click */
        .prof-sidebar:hover,
        .prof-sidebar.open {
            width: 256px;
        }

        .prof-sidebar:hover a,
        .prof-sidebar.open a {
            justify-content: flex-start;
            gap: 12px;
            padding: 12px 24px;
            margin: 4px 16px;
        }

        .prof-sidebar:hover a .link-text,
        .prof-sidebar.open a .link-text,
        .prof-sidebar:hover a .caret,
        .prof-sidebar.open a .caret {
            display: inline;
        }

        /* --- MAIN CONTENT UPDATE FOR TABLET/MOBILE --- */
        .prof-main {
            /* Matches the shrunken sidebar width */
            margin-left: 64px; 
            width: calc(100% - 64px);
            padding: 16px;
        }
    }

    /* --- LOWER DEVICES / SMALL MOBILE (Max-width: 480px) --- */
    @media (max-width: 480px) {
        .prof-main {
            /* Keep margin 64px, but reduce padding to give content room */
            padding: 12px;
            padding-top: 16px;
        }
        
        /* Optional: If sidebar icons feel too big on tiny screens */
        .prof-sidebar {
            width: 60px;
        }
        .prof-main {
            margin-left: 60px;
            width: calc(100% - 60px);
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