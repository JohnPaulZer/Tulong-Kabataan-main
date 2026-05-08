@php
    $adminNavItems = [
        [
            'label' => 'TKB Directory',
            'route' => 'admin.home',
            'icon' => 'ri-apps-2-line',
            'active' => ['admin.home'],
        ],
        [
            'label' => 'Campaign',
            'route' => 'campaign.page',
            'icon' => 'ri-megaphone-line',
            'active' => ['campaign.page', 'admin.campaign.*'],
        ],
        [
            'label' => 'Events',
            'route' => 'adminevent.page',
            'icon' => 'ri-calendar-event-line',
            'active' => ['adminevent.page', 'createevent', 'events.*', 'admin.volunteer.*'],
        ],
        [
            'label' => 'Account Verification',
            'route' => 'account.page',
            'icon' => 'ri-shield-check-line',
            'active' => ['account.page'],
        ],
        [
            'label' => 'In-kind Donation',
            'route' => 'admininkind.page',
            'icon' => 'ri-gift-line',
            'active' => ['admininkind.page'],
        ],
        [
            'label' => 'DNC Records',
            'route' => 'dnc.view',
            'icon' => 'ri-database-2-line',
            'active' => ['dnc.*'],
        ],
    ];
@endphp

<aside id="sidebar" class="sidebar admin-sidebar" aria-label="Administrator navigation">
    <div class="admin-sidebar__brand">
        <span class="admin-sidebar__brand-icon"><i class="ri-shield-user-line"></i></span>
        <span class="admin-sidebar__brand-text">Administrator</span>
    </div>

    <nav class="admin-sidebar__nav">
        @foreach ($adminNavItems as $item)
            <a class="side-link {{ request()->routeIs(...$item['active']) ? 'active' : '' }}"
                href="{{ route($item['route']) }}">
                <i class="{{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <a class="admin-sidebar__profile" href="{{ route('admin.logout') }}" aria-label="Log out administrator"
        data-admin-logout-trigger>
        <span class="admin-sidebar__avatar">A</span>
        <span class="admin-sidebar__profile-copy">
            <strong>Administrator</strong>
            <small>Super Admin</small>
        </span>
        <i class="ri-logout-box-r-line" aria-hidden="true"></i>
    </a>
</aside>

<div class="admin-logout-modal" id="adminLogoutModal" aria-hidden="true" hidden>
    <button type="button" class="admin-logout-modal__backdrop" data-admin-logout-cancel
        aria-label="Cancel logout"></button>

    <section class="admin-logout-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="adminLogoutTitle">
        <div class="admin-logout-modal__icon">
            <i class="ri-logout-box-r-line" aria-hidden="true"></i>
        </div>
        <h2 id="adminLogoutTitle">Sign out?</h2>
        <p>You will be returned to the administrator login page.</p>
        <div class="admin-logout-modal__actions">
            <button type="button" class="admin-logout-modal__btn admin-logout-modal__btn--secondary"
                data-admin-logout-cancel>
                Cancel
            </button>
            <a class="admin-logout-modal__btn admin-logout-modal__btn--primary" href="{{ route('admin.logout') }}">
                Sign out
            </a>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const trigger = document.querySelector('[data-admin-logout-trigger]');
        const modal = document.getElementById('adminLogoutModal');
        if (!trigger || !modal) return;

        const cancelButtons = modal.querySelectorAll('[data-admin-logout-cancel]');
        const confirmButton = modal.querySelector('.admin-logout-modal__btn--primary');

        function openLogoutModal(event) {
            event.preventDefault();
            modal.hidden = false;
            requestAnimationFrame(() => {
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                confirmButton?.focus();
            });
        }

        function closeLogoutModal() {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            setTimeout(() => {
                modal.hidden = true;
                trigger.focus();
            }, 180);
        }

        trigger.addEventListener('click', openLogoutModal);
        cancelButtons.forEach((button) => button.addEventListener('click', closeLogoutModal));

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                closeLogoutModal();
            }
        });
    });
</script>
