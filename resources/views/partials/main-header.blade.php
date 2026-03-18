<header class="header">
    @include('partials.universalmodal')
    <link rel="stylesheet" href="{{ asset('css/main-header.css') }}?">
    <div class="header-container">
        <div class="header-left">
            <a href="{{ route('landpage') }}" class="logo">
                <img src="{{ asset('img/log.png') }}" alt="Tulong Kabataan" style="height: 40px; vertical-align: middle;">
            </a>
            <nav class="main-nav body-font">
                <a href="{{ route('campaignpage') }}">Campaigns</a>
                <a href="{{ route('event.page') }}">Events</a>
                <a href="{{ route('inkind.page') }}">Donate</a>
                <a href="{{ route('about.us') }}">About Us</a>
            </nav>
        </div>

        <div class="header-right">
            @guest
                <a href="{{ route('login.page') }}">
                    <button class="sign-in-btn body-font"><i class="ri-login-box-line"></i> Sign In</button>
                </a>
                <a href="{{ route('login.register') }}" class="register-btn body-font"><i class="ri-user-add-line"></i>
                    Register</a>
            @endguest

            @auth

                @php
                    $notifications = Auth::user()->notifications()->latest()->take(10)->get();

                    // Get all notifications and organize by type with their creation dates
                    $campaignNotifications = $notifications->filter(function ($notification) {
                        return in_array($notification->data['type'], [
                            'campaign_ended',
                            'campaign_published',
                            'recurring_campaign_published',
                            'recurring_campaign_paused',
                        ]);
                    });

                    $donationNotifications = $notifications->filter(function ($notification) {
                        return in_array($notification->data['type'], [
                            'new_donation',
                            'inkind_donation_confirmation',
                            'manual_donation_status',
                            'donation_distributed',
                        ]);
                    });

                    // Filter for ALL event notifications (both registration and reminders)
                    $eventNotifications = $notifications->filter(function ($notification) {
                        return in_array($notification->data['type'], ['event_registration', 'event_reminder']);
                    });

                    // Determine which section should come first based on newest notification
                    $newestCampaign = $campaignNotifications->first();
                    $newestDonation = $donationNotifications->first();
                    $newestEvent = $eventNotifications->first();

                    $sections = [];

                    if ($newestCampaign && $newestDonation && $newestEvent) {
                        // Compare creation dates to determine order
                        $notificationsByDate = [
                            ['type' => 'campaigns', 'date' => $newestCampaign->created_at],
                            ['type' => 'donations', 'date' => $newestDonation->created_at],
                            ['type' => 'events', 'date' => $newestEvent->created_at],
                        ];

                        usort($notificationsByDate, function ($a, $b) {
                            return $b['date']->gt($a['date']) ? 1 : -1;
                        });

                        $sections = array_column($notificationsByDate, 'type');
                    } elseif ($newestCampaign) {
                        $sections = ['campaigns'];
                    } elseif ($newestDonation) {
                        $sections = ['donations'];
                    } elseif ($newestEvent) {
                        $sections = ['events'];
                    }
                @endphp

                <!-- Notification Bell -->
                <div class="notif-menu" id="notificationBell">
                    <button class="icon-btn" id="notifBtn" aria-haspopup="true" aria-expanded="false">
                        <i class="ri-notification-3-line"></i>
                        <span class="badge" id="notificationBadge"
                            style="{{ auth()->user()->unreadNotifications()->count() > 0 ? '' : 'display: none;' }}">
                            {{ auth()->user()->unreadNotifications()->count() > 999 ? '999+' : auth()->user()->unreadNotifications()->count() }}
                        </span>
                    </button>

                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <div class="notif-header-top">
                                <h3>Notifications</h3>
                                <button class="mark-all-read-btn" id="markAllReadBtn">
                                    <i class="ri-check-double-line"></i>
                                    Mark all as read
                                </button>
                            </div>
                            <div class="notif-tabs">
                                <button class="notif-tab active" data-filter="all">
                                    All
                                    <span class="tab-dot" id="allTabBadge"
                                        style="{{ auth()->user()->unreadNotifications()->count() > 0 ? '' : 'display: none;' }}"></span>
                                </button>
                                <!-- ADD THIS: Events Tab -->
                                <button class="notif-tab" data-filter="events">
                                    Events
                                    <span class="tab-dot" id="eventsTabBadge"
                                        style="{{ $eventNotifications->where('read_at', null)->count() > 0 ? '' : 'display: none;' }}"></span>
                                </button>
                                <button class="notif-tab" data-filter="campaigns">
                                    Campaigns
                                    <span class="tab-dot" id="campaignsTabBadge"
                                        style="{{ $campaignNotifications->where('read_at', null)->count() > 0 ? '' : 'display: none;' }}"></span>
                                </button>
                                <button class="notif-tab" data-filter="donations">
                                    Donations
                                    <span class="tab-dot" id="donationsTabBadge"
                                        style="{{ $donationNotifications->where('read_at', null)->count() > 0 ? '' : 'display: none;' }}"></span>
                                </button>
                                <button class="notif-tab" data-filter="unread">
                                    Unread
                                    <span class="tab-dot" id="unreadTabBadge"
                                        style="{{ auth()->user()->unreadNotifications()->count() > 0 ? '' : 'display: none;' }}"></span>
                                </button>
                            </div>
                        </div>

                        <div class="notif-content" id="notificationsContent">
                            <!-- All notifications combined and sorted by newest first -->
                            @php
                                // Combine all notifications and sort by created_at (newest first)
                                $allNotifications = $notifications->sortByDesc('created_at');
                            @endphp

                            @if ($allNotifications->count() > 0)
                                @foreach ($allNotifications as $notification)
                                    @php
                                        $isCampaign = in_array($notification->data['type'], [
                                            'campaign_ended',
                                            'campaign_published',
                                            'recurring_campaign_published',
                                            'recurring_campaign_paused',
                                        ]);
                                        $isDonation = in_array($notification->data['type'], [
                                            'new_donation',
                                            'inkind_donation_confirmation',
                                            'manual_donation_status',
                                            'donation_distributed',
                                        ]);
                                        $isEvent = in_array($notification->data['type'], [
                                            'event_registration',
                                            'event_reminder',
                                        ]);

                                        $isVerification = $notification->data['type'] === 'verification_decision';
                                        $isManualDonation = $notification->data['type'] === 'manual_donation_status';
                                        $isDonationDistributed = $notification->data['type'] === 'donation_distributed'; // ADD THIS

                                        // Set icons based on notification type
                                        if ($isCampaign) {
                                            $icon = 'ri-flag-line';
                                            if ($notification->data['type'] === 'campaign_published') {
                                                $icon = 'ri-megaphone-line';
                                            } elseif ($notification->data['type'] === 'campaign_ended') {
                                                $icon = 'ri-flag-line';
                                            } elseif ($notification->data['type'] === 'recurring_campaign_published') {
                                                $icon = 'ri-refresh-line';
                                            } elseif ($notification->data['type'] === 'recurring_campaign_paused') {
                                                $icon = 'ri-pause-circle-line';
                                            }
                                        } elseif ($isEvent) {
                                            if ($notification->data['type'] === 'event_reminder') {
                                                $icon = 'ri-alarm-line';
                                            } else {
                                                $icon = 'ri-calendar-event-line';
                                            }
                                        } elseif ($isVerification) {
                                            // Use the icon from verification notification
                                            $icon = $notification->data['icon'] ?? 'ri-user-heart-line';
                                        } elseif ($isManualDonation) {
                                            // Use the icon from the manual donation notification data
                                            $icon = $notification->data['icon'] ?? 'ri-checkbox-circle-line';
                                        } elseif ($isDonationDistributed) {
                                            // ADD THIS - Use gift icon for donation distributed
                                            $icon = 'ri-gift-line';
                                        } else {
                                            // Use the icon from the notification data
                                            $icon = $notification->data['icon'] ?? 'ri-user-heart-line';
                                        }

                                        // Set URL
                                        $url = $notification->data['url'] ?? '#';
                                        if ($isDonation && str_contains($url, '/campaigns/')) {
                                            $campaignId = $notification->data['campaign_id'] ?? null;
                                            $url = $campaignId ? route('campaign.view', $campaignId) : '#';
                                        }
                                        // For manual donation status, use the campaign URL if available
                                        elseif ($isManualDonation) {
                                            // ADDED THIS
                                            $campaignId = $notification->data['campaign_id'] ?? null;
                                            if ($campaignId) {
                                                $url = route('campaign.view', $campaignId);
                                            } else {
                                                $url = route('campaignpage');
                                            }
                                        }
                                        // For in-kind donations, use the in-kind tracking route if available
                                        elseif ($notification->data['type'] === 'inkind_donation_confirmation') {
                                            $url =
                                                route('inkind.tracking') .
                                                '?donation=' .
                                                ($notification->data['inkind_id'] ?? '');
                                        }

                                        // Set notification class
                                        $notificationClass = '';
                                        if ($isCampaign) {
                                            $notificationClass = 'campaign-notification';
                                        } elseif ($isDonation) {
                                            $notificationClass = 'donation-notification';
                                        } elseif ($isEvent) {
                                            $notificationClass = 'event-notification';
                                        } elseif ($isVerification) {
                                            $notificationClass = 'verification-notification';
                                        }
                                    @endphp

                                    @if ($isVerification)
                                        <div class="notif-item {{ $notification->read_at ? '' : 'unread' }} {{ $notificationClass }}"
                                            data-notification-id="{{ $notification->id }}"
                                            data-notification-type="{{ $notification->data['type'] }}">
                                        @else
                                            <a href="{{ $url }}"
                                                class="notif-item {{ $notification->read_at ? '' : 'unread' }} {{ $notificationClass }}"
                                                data-notification-id="{{ $notification->id }}"
                                                data-notification-type="{{ $notification->data['type'] }}"
                                                onclick="notificationManager.handleNotificationClick('{{ $notification->id }}', event)">
                                    @endif
                                    <div class="notif-avatar">
                                        <i class="{{ $icon }}"></i>
                                    </div>
                                    <div class="notif-details">
                                        <div class="notif-text">
                                            {{-- For manual donation, show the full message --}}
                                            @if ($isManualDonation)
                                                {{ $notification->data['full_message'] ?? $notification->data['message'] }}
                                            @else
                                                {{ $notification->data['message'] }}
                                            @endif
                                        </div>
                                        <div class="notif-meta">{{ $notification->created_at->diffForHumans() }}</div>
                                        @if (isset($notification->data['amount']))
                                            <div class="notif-amount">
                                                ₱{{ number_format($notification->data['amount'], 2) }}</div>
                                        @endif
                                        @if ($notification->data['type'] === 'inkind_donation_confirmation' && isset($notification->data['details']))
                                            <div class="notif-details-text">{{ $notification->data['details'] }}</div>
                                        @endif
                                        {{-- Show status for manual donations --}}
                                        @if ($isManualDonation && isset($notification->data['status']))
                                            <div class="notif-status">
                                            </div>
                                        @endif
                                    </div>
                                    @if ($isVerification)
                        </div>
                    @else
                        </a>
                        @endif
                        @endforeach
                    @else
                        <!-- No notifications message -->
                        <div class="no-notifications-state">
                            <div style="text-align: center; padding: 40px 20px; color: #6b7280;">
                                <p style="margin: 0; font-size: 14px;">No notifications yet</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="notif-footer">
                        <a href="#" id="seeAllNotifications" data-modal-trigger>
                            See all notifications
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Menu (wrapped so dropdown anchors directly below the button) -->
            <div class="user-menu" id="userMenu">
                <button class="user-btn body-font" id="userMenuBtn" aria-haspopup="true" aria-expanded="false"
                    aria-controls="userDropdown">
                    @php
                        $avatar = optional(auth()->user())->profile_photo_url;
                        if (
                            $avatar &&
                            str_contains($avatar, 'googleusercontent.com') &&
                            !str_contains($avatar, 'sz=')
                        ) {
                            $avatar .= (str_contains($avatar, '?') ? '&' : '?') . 'sz=64';
                        }
                        if ($avatar && !Str::startsWith($avatar, 'http')) {
                            $avatar = asset('storage/' . $avatar);
                        }
                    @endphp
                    @if ($avatar)
                        <img class="user-avatar" src="{{ $avatar }}" alt="Profile photo"
                            referrerpolicy="no-referrer">
                    @else
                        <i class="ri-user-line"></i>
                    @endif
                    {{ auth()->user()->first_name ?? 'Account' }}
                    <i class="ri-arrow-down-s-line"></i>
                </button>

                <div class="user-dropdown" id="userDropdown">
                    <a href="{{ route('profile') }}">Profile</a>
                    <a href="{{ route('inkind.tracking') }}">Your impact</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-logout">Sign out</button>
                    </form>
                </div>
            </div>
        @endauth

        <!-- Mobile menu (visible only on mobile/tablet due to CSS above) -->
        <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Open menu">
            <i class="ri-menu-line"></i>
        </button>
    </div>
    </div>

    <!-- Site-wide mobile tray (unchanged) -->
    <div class="mobile-dropdown" id="mobileDropdown">
        <nav class="mobile-nav">
            <a href="{{ route('campaignpage') }}">Campaigns</a>
            <a href="{{ route('event.page') }}">Events</a>
            <a href="{{ route('inkind.page') }}">Donate</a>
            <a href="#">About Us</a>
        </nav>
    </div>

    @auth
        {{-- Notification-script --}}
        <script>
            class NotificationManager {
                constructor() {
                    this.pollInterval = null;
                    this.currentFilter = 'all';
                    this.modalCurrentFilter = 'all';
                    this.selectedNotifications = new Set();
                    this.init();
                }

                init() {
                    this.initializeBadgeState();
                    this.startPolling();
                    this.setupDropdown();
                    this.attachNotificationEvents();
                    this.setupFilterTabs();
                    this.setupMarkAllRead();
                    this.applyInitialFilter();
                    this.updateTabBadges();
                    this.setupModal();
                    this.setupDeleteFunctionality();
                    this.setupDeleteMode();
                }

                showToast(message, type = 'success') {
                    const toast = document.getElementById('toast');
                    if (!toast) return;

                    const colors = {
                        success: '#16a34a',
                        error: '#dc2626',
                        warning: '#d97706',
                        info: '#2563eb'
                    };

                    toast.style.background = colors[type] || colors.success;
                    toast.textContent = message;
                    toast.style.opacity = '1';
                    toast.style.pointerEvents = 'auto';

                    setTimeout(() => {
                        toast.style.opacity = '0';
                        toast.style.pointerEvents = 'none';
                    }, 3000);
                }

                setupDeleteMode() {
                    this.deleteBtn = document.getElementById('notificationsDeleteBtn');
                    this.cancelDeleteBtn = document.getElementById('notificationsCancelDeleteBtn');

                    if (this.deleteBtn) {
                        this.deleteBtn.addEventListener('click', () => {
                            if (this.isInDeleteMode()) {
                                // Already in delete mode - handle delete action
                                if (this.selectedNotifications.size > 0) {
                                    this.deleteSelectedNotifications();
                                } else {
                                    this.deleteAllNotifications();
                                }
                            } else {
                                // Enter delete mode
                                this.enterDeleteMode();
                            }
                        });
                    }

                    if (this.cancelDeleteBtn) {
                        this.cancelDeleteBtn.addEventListener('click', () => {
                            this.exitDeleteMode();
                        });
                    }
                }

                isInDeleteMode() {
                    const modalContent = document.querySelector('.notifications-modal-content');
                    return modalContent && modalContent.classList.contains('delete-mode');
                }

                enterDeleteMode() {
                    // Show checkboxes
                    const checkboxes = document.querySelectorAll('.notification-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.style.display = 'block';
                    });

                    // Set initial delete button text based on current selection
                    if (this.selectedNotifications.size > 0) {
                        this.deleteBtn.innerHTML =
                            `<i class="ri-delete-bin-line"></i> Delete (${this.selectedNotifications.size})`;
                    } else {
                        this.deleteBtn.innerHTML = `<i class="ri-delete-bin-line"></i> Delete All`;
                    }

                    // Show cancel button
                    this.cancelDeleteBtn.style.display = 'flex';

                    // Add delete mode class to modal for styling
                    const modalContent = document.querySelector('.notifications-modal-content');
                    modalContent.classList.add('delete-mode');
                }

                exitDeleteMode() {
                    // Hide checkboxes
                    const checkboxes = document.querySelectorAll('.notification-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.style.display = 'none';
                    });

                    // Uncheck all checkboxes
                    const checkboxInputs = document.querySelectorAll('.notification-select');
                    checkboxInputs.forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    // Clear selection
                    this.selectedNotifications.clear();

                    // Reset delete button to original state
                    this.deleteBtn.innerHTML = `<i class="ri-delete-bin-line"></i> Delete`;

                    // Hide cancel button
                    this.cancelDeleteBtn.style.display = 'none';

                    // Remove delete mode class
                    const modalContent = document.querySelector('.notifications-modal-content');
                    modalContent.classList.remove('delete-mode');
                }

                updateDeleteButtonText() {
                    // Only update if we're in delete mode
                    if (this.isInDeleteMode()) {
                        if (this.selectedNotifications.size > 0) {
                            this.deleteBtn.innerHTML =
                                `<i class="ri-delete-bin-line"></i> Delete (${this.selectedNotifications.size})`;
                        } else {
                            this.deleteBtn.innerHTML = `<i class="ri-delete-bin-line"></i> Delete All`;
                        }
                    }
                }

                setupDeleteFunctionality() {
                    this.setupCheckboxEvents();
                }

                setupCheckboxEvents() {
                    const modalContent = document.getElementById('notificationsModalContent');
                    if (modalContent) {
                        modalContent.addEventListener('change', (e) => {
                            if (e.target.classList.contains('notification-select')) {
                                this.handleCheckboxChange(e.target);
                            }
                        });
                    }
                }

                handleCheckboxChange(checkbox) {
                    const notificationId = checkbox.getAttribute('data-notification-id');

                    if (checkbox.checked) {
                        this.selectedNotifications.add(notificationId);
                    } else {
                        this.selectedNotifications.delete(notificationId);
                    }

                    // Only update button text if we're in delete mode
                    if (this.isInDeleteMode()) {
                        this.updateDeleteButtonText();
                    }
                }
                async deleteAllNotifications() {
                    try {
                        const response = await fetch('/notifications/delete-all', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        if (response.ok) {
                            this.clearAllNotifications();
                            this.selectedNotifications.clear();
                            this.showToast('All notifications deleted successfully', 'success');
                            this.closeModal();
                            this.exitDeleteMode();
                        } else {
                            throw new Error('Failed to delete notifications');
                        }
                    } catch (error) {
                        console.error('Error deleting all notifications:', error);
                        this.showToast('Failed to delete notifications', 'error');
                    }
                }

                async deleteSelectedNotifications() {
                    if (this.selectedNotifications.size === 0) {
                        this.showToast('No notifications selected', 'warning');
                        return;
                    }

                    try {
                        const response = await fetch('/notifications/delete-selected', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                notification_ids: Array.from(this.selectedNotifications)
                            })
                        });

                        if (response.ok) {
                            this.removeSelectedNotifications();
                            this.selectedNotifications.clear();
                            this.showToast('Selected notifications deleted successfully', 'success');
                            this.closeModal();
                            this.exitDeleteMode();
                        } else {
                            throw new Error('Failed to delete selected notifications');
                        }
                    } catch (error) {
                        console.error('Error deleting selected notifications:', error);
                        this.showToast('Failed to delete selected notifications', 'error');
                    }
                }

                clearAllNotifications() {
                    const modalContent = document.getElementById('notificationsModalContent');
                    const dropdownContent = document.getElementById('notificationsContent');

                    if (modalContent) {
                        modalContent.innerHTML = `
                <div class="notifications-modal-empty">
                    <p>No notifications yet</p>
                </div>
            `;
                    }

                    if (dropdownContent) {
                        dropdownContent.innerHTML = `
                <div class="no-notifications-state">
                    <div style="text-align: center; padding: 40px 20px; color: #6b7280;">
                        <p style="margin: 0; font-size: 14px;">No notifications yet</p>
                    </div>
                </div>
            `;
                    }

                    this.resetBadge();
                    this.updateTabBadges();
                }

                removeSelectedNotifications() {
                    this.selectedNotifications.forEach(notificationId => {
                        const modalItem = document.querySelector(
                            `.notification-select[data-notification-id="${notificationId}"]`);
                        if (modalItem) {
                            const wrapper = modalItem.closest('.notifications-modal-item-wrapper');
                            if (wrapper) wrapper.remove();
                        }

                        const dropdownItem = document.querySelector(
                            `.notif-item[data-notification-id="${notificationId}"]`);
                        if (dropdownItem) dropdownItem.remove();
                    });

                    this.updateEmptyStates();
                    this.updateBadgeCountFromDOM();
                    this.updateTabBadges();

                    // Update delete button text after removal
                    this.updateDeleteButtonText();
                }

                updateEmptyStates() {
                    const modalContent = document.getElementById('notificationsModalContent');
                    const dropdownContent = document.getElementById('notificationsContent');

                    const modalItems = modalContent?.querySelectorAll('.notifications-modal-item-wrapper');
                    const dropdownItems = dropdownContent?.querySelectorAll('.notif-item');

                    if (modalContent && (!modalItems || modalItems.length === 0)) {
                        modalContent.innerHTML = `
                <div class="notifications-modal-empty">
                    <p>No notifications yet</p>
                </div>
            `;
                    }

                    if (dropdownContent && (!dropdownItems || dropdownItems.length === 0)) {
                        dropdownContent.innerHTML = `
                <div class="no-notifications-state">
                    <div style="text-align: center; padding: 40px 20px; color: #6b7280;">
                        <p style="margin: 0; font-size: 14px;">No notifications yet</p>
                    </div>
                </div>
            `;
                    }
                }

                updateBadgeCountFromDOM() {
                    const unreadCount = document.querySelectorAll('.notif-item.unread').length;
                    const badge = document.getElementById('notificationBadge');

                    if (badge) {
                        badge.textContent = unreadCount > 999 ? '999+' : unreadCount;
                        badge.style.display = unreadCount > 0 ? 'flex' : 'none';
                    }
                }

                setupModal() {
                    this.setupModalTriggers();
                    this.setupModalClose();
                    this.setupModalMarkAllRead();
                    this.setupModalFilterTabs();
                    this.applyInitialModalFilter();
                    this.closeModal();
                }

                setupModalTriggers() {
                    const seeAllLink = document.getElementById('seeAllNotifications');
                    if (seeAllLink) {
                        seeAllLink.addEventListener('click', (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            this.closeNotificationDropdown();
                            this.openModal();
                        });
                    }
                }

                closeNotificationDropdown() {
                    const notifDropdown = document.getElementById('notifDropdown');
                    const notifBtn = document.getElementById('notifBtn');

                    if (notifDropdown) notifDropdown.classList.remove('show');
                    if (notifBtn) notifBtn.setAttribute('aria-expanded', 'false');
                }

                setupModalClose() {
                    const modal = document.getElementById('notificationsModal');
                    const overlay = document.getElementById('notificationsModalOverlay');
                    const closeBtn = document.getElementById('notificationsModalCloseBtn');

                    const closeModal = () => {
                        modal.style.display = 'none';
                        this.exitDeleteMode();
                    };

                    if (overlay) overlay.addEventListener('click', closeModal);
                    if (closeBtn) closeBtn.addEventListener('click', closeModal);

                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && modal.style.display !== 'none') {
                            closeModal();
                        }
                    });
                }

                setupModalMarkAllRead() {
                    const modalMarkAllBtn = document.getElementById('notificationsModalMarkAllReadBtn');
                    if (modalMarkAllBtn) {
                        modalMarkAllBtn.addEventListener('click', () => {
                            this.markAllAsRead();
                        });
                    }
                }

                setupModalFilterTabs() {
                    const modalTabs = document.querySelectorAll('.notifications-modal-tab');
                    modalTabs.forEach(tab => {
                        tab.addEventListener('click', () => {
                            modalTabs.forEach(t => t.classList.remove('active'));
                            tab.classList.add('active');
                            this.modalCurrentFilter = tab.getAttribute('data-filter');
                            this.filterModalNotifications(this.modalCurrentFilter);
                        });
                    });
                }

                applyInitialModalFilter() {
                    this.filterModalNotifications(this.modalCurrentFilter);
                    const modalTabs = document.querySelectorAll('.notifications-modal-tab');
                    modalTabs.forEach(tab => {
                        if (tab.getAttribute('data-filter') === this.modalCurrentFilter) {
                            tab.classList.add('active');
                        } else {
                            tab.classList.remove('active');
                        }
                    });
                }

                filterModalNotifications(filter) {
                    const modalWrappers = document.querySelectorAll(
                        '#notificationsModalContent .notifications-modal-item-wrapper');
                    const modalContent = document.getElementById('notificationsModalContent');

                    let hasVisibleItems = false;

                    modalWrappers.forEach(wrapper => {
                        const item = wrapper.querySelector('.notifications-modal-item');
                        if (!item) return;

                        if (filter === 'all') {
                            wrapper.style.display = 'flex';
                            hasVisibleItems = true;
                        } else if (filter === 'events') { // ADD THIS CASE
                            if (item.classList.contains('event-notification')) {
                                wrapper.style.display = 'flex';
                                hasVisibleItems = true;
                            } else {
                                wrapper.style.display = 'none';
                            }
                        } else if (filter === 'campaigns') {
                            if (item.classList.contains('campaign-notification')) {
                                wrapper.style.display = 'flex';
                                hasVisibleItems = true;
                            } else {
                                wrapper.style.display = 'none';
                            }
                        } else if (filter === 'donations') {
                            if (item.classList.contains('donation-notification')) {
                                wrapper.style.display = 'flex';
                                hasVisibleItems = true;
                            } else {
                                wrapper.style.display = 'none';
                            }
                        } else if (filter === 'unread') {
                            if (item.classList.contains('unread')) {
                                wrapper.style.display = 'flex';
                                hasVisibleItems = true;
                            } else {
                                wrapper.style.display = 'none';
                            }
                        }
                    });

                    const existingEmptyState = modalContent?.querySelector('.notifications-modal-empty');
                    if (existingEmptyState) existingEmptyState.remove();

                    if (!hasVisibleItems && modalContent) {
                        const emptyStateDiv = document.createElement('div');
                        emptyStateDiv.className = 'notifications-modal-empty';

                        let message = 'No notifications yet';
                        if (filter === 'events') message = 'No event notifications'; // ADD THIS
                        else if (filter === 'campaigns') message = 'No campaign notifications';
                        else if (filter === 'donations') message = 'No donation notifications';
                        else if (filter === 'unread') message = 'No unread notifications';

                        emptyStateDiv.innerHTML = `
            <div style="text-align: center; padding: 60px 20px; color: #6b7280;">
                <p style="margin: 0; font-size: 14px;">${message}</p>
            </div>
        `;
                        modalContent.appendChild(emptyStateDiv);
                    }
                }

                openModal() {
                    const modal = document.getElementById('notificationsModal');
                    if (modal) {
                        modal.style.display = 'block';
                        this.filterModalNotifications(this.modalCurrentFilter);
                        this.exitDeleteMode(); // Ensure normal mode when opening
                        this.resetDeleteButtonToDefault(); // Ensure button says "Delete"
                    }
                }

                handleModalNotificationClick(notificationId, event) {
                    event.preventDefault();
                    event.stopPropagation();

                    const notifItem = event.target.closest('.notifications-modal-item');
                    const url = notifItem.getAttribute('href');

                    if (notifItem.classList.contains('unread')) {
                        notifItem.classList.remove('unread');
                        this.markNotificationAsReadOnServer(notificationId);
                        this.decrementBadgeCount();
                        this.updateTabBadges();
                    }

                    this.closeModal();

                    if (url && url !== '#') {
                        window.location.href = url;
                    }
                }

                closeModal() {
                    const modal = document.getElementById('notificationsModal');
                    if (modal) modal.style.display = 'none';
                }

                updateTabBadges() {
                    const campaignUnread = document.querySelectorAll('.campaign-notification.unread').length;
                    const donationUnread = document.querySelectorAll('.donation-notification.unread').length;
                    const eventUnread = document.querySelectorAll('.event-notification.unread').length; // ADD THIS
                    const totalUnread = campaignUnread + donationUnread + eventUnread; // UPDATE THIS

                    const campaignsBadge = document.getElementById('campaignsTabBadge');
                    const donationsBadge = document.getElementById('donationsTabBadge');
                    const eventsBadge = document.getElementById('eventsTabBadge'); // ADD THIS
                    const unreadBadge = document.getElementById('unreadTabBadge');
                    const allBadge = document.getElementById('allTabBadge');

                    if (campaignsBadge) campaignsBadge.style.display = campaignUnread > 0 ? 'inline-block' : 'none';
                    if (donationsBadge) donationsBadge.style.display = donationUnread > 0 ? 'inline-block' : 'none';
                    if (eventsBadge) eventsBadge.style.display = eventUnread > 0 ? 'inline-block' : 'none'; // ADD THIS
                    if (unreadBadge) unreadBadge.style.display = totalUnread > 0 ? 'inline-block' : 'none';
                    if (allBadge) allBadge.style.display = totalUnread > 0 ? 'inline-block' : 'none';
                }

                applyInitialFilter() {
                    this.filterNotifications(this.currentFilter);
                    const tabs = document.querySelectorAll('.notif-tab');
                    tabs.forEach(tab => {
                        if (tab.getAttribute('data-filter') === this.currentFilter) {
                            tab.classList.add('active');
                        } else {
                            tab.classList.remove('active');
                        }
                    });
                }

                setupFilterTabs() {
                    const tabs = document.querySelectorAll('.notif-tab');
                    tabs.forEach(tab => {
                        tab.addEventListener('click', () => {
                            tabs.forEach(t => t.classList.remove('active'));
                            tab.classList.add('active');
                            this.currentFilter = tab.getAttribute('data-filter');
                            this.filterNotifications(this.currentFilter);
                        });
                    });
                }

                filterNotifications(filter) {
                    const notifItems = document.querySelectorAll('.notif-item');
                    const notifContent = document.getElementById('notificationsContent');

                    let hasVisibleItems = false;

                    notifItems.forEach(item => {
                        if (filter === 'all') {
                            item.style.display = 'flex';
                            hasVisibleItems = true;
                        } else if (filter === 'events') { // ADD THIS CASE
                            if (item.classList.contains('event-notification')) {
                                item.style.display = 'flex';
                                hasVisibleItems = true;
                            } else {
                                item.style.display = 'none';
                            }
                        } else if (filter === 'campaigns') {
                            if (item.classList.contains('campaign-notification')) {
                                item.style.display = 'flex';
                                hasVisibleItems = true;
                            } else {
                                item.style.display = 'none';
                            }
                        } else if (filter === 'donations') {
                            if (item.classList.contains('donation-notification')) {
                                item.style.display = 'flex';
                                hasVisibleItems = true;
                            } else {
                                item.style.display = 'none';
                            }
                        } else if (filter === 'unread') {
                            if (item.classList.contains('unread')) {
                                item.style.display = 'flex';
                                hasVisibleItems = true;
                            } else {
                                item.style.display = 'none';
                            }
                        }
                    });

                    const existingEmptyState = notifContent?.querySelector('.no-notifications-state');
                    if (existingEmptyState) existingEmptyState.remove();

                    if (!hasVisibleItems && notifContent) {
                        const emptyStateDiv = document.createElement('div');
                        emptyStateDiv.className = 'no-notifications-state';

                        let message = 'No notifications yet';
                        if (filter === 'events') message = 'No event notifications'; // ADD THIS
                        else if (filter === 'campaigns') message = 'No campaign notifications';
                        else if (filter === 'donations') message = 'No donation notifications';
                        else if (filter === 'unread') message = 'No unread notifications';

                        emptyStateDiv.innerHTML = `
            <div style="text-align: center; padding: 40px 20px; color: #6b7280;">
                <p style="margin: 0; font-size: 14px;">${message}</p>
            </div>
        `;
                        notifContent.appendChild(emptyStateDiv);
                    }
                }

                setupMarkAllRead() {
                    const markAllBtn = document.getElementById('markAllReadBtn');
                    if (markAllBtn) {
                        markAllBtn.addEventListener('click', () => {
                            this.markAllAsRead();
                        });
                    }
                }

                markAllAsRead() {
                    const unreadItems = document.querySelectorAll('.notif-item.unread');
                    const modalUnreadItems = document.querySelectorAll(
                        '#notificationsModalContent .notifications-modal-item.unread');

                    unreadItems.forEach(item => item.classList.remove('unread'));
                    modalUnreadItems.forEach(item => item.classList.remove('unread'));

                    this.resetBadge();
                    this.updateTabBadges();

                    if (this.currentFilter === 'unread') this.filterNotifications('unread');
                    if (this.modalCurrentFilter === 'unread') this.filterModalNotifications('unread');

                    this.markAllAsReadOnServer();
                }

                resetBadge() {
                    const badge = document.getElementById('notificationBadge');
                    if (badge) {
                        badge.textContent = '0';
                        badge.style.display = 'none';
                    }
                }

                async markAllAsReadOnServer() {
                    try {
                        await fetch('/notifications/mark-all-read', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({})
                        });
                    } catch (error) {
                        console.error('Error marking all notifications as read:', error);
                        this.showToast('Failed to mark notifications as read', 'error');
                    }
                }

                initializeBadgeState() {
                    const unreadCount = {{ auth()->user()->unreadNotifications()->count() }};
                    const badge = document.getElementById('notificationBadge');

                    if (badge) {
                        badge.textContent = unreadCount > 999 ? '999+' : unreadCount;
                        badge.style.display = unreadCount > 0 ? 'flex' : 'none';
                    }
                }

                startPolling() {
                    this.pollInterval = setInterval(() => {
                        this.refreshNotifications();
                    }, 3000);
                }

                async refreshNotifications() {
                    try {
                        const response = await fetch(window.location.href, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        });

                        if (response.ok) {
                            const text = await response.text();
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = text;

                            this.updateNotificationCount(tempDiv);
                            this.updateNotificationContent(tempDiv);
                            this.updateModalContent(tempDiv);
                        }
                    } catch (error) {
                        console.error('Notification polling error:', error);
                    }
                }

                updateNotificationCount(tempDiv) {
                    const newBadge = tempDiv.querySelector('#notificationBadge');
                    if (newBadge) {
                        const badge = document.getElementById('notificationBadge');
                        const newCount = parseInt(newBadge.textContent) || 0;

                        if (badge) {
                            const currentCount = parseInt(badge.textContent) || 0;
                            badge.textContent = newCount > 999 ? '999+' : newCount;

                            if (newCount !== currentCount) {
                                badge.style.display = newCount > 0 ? 'flex' : 'none';
                            }
                        }
                    }
                }

                updateNotificationContent(tempDiv) {
                    const newContent = tempDiv.querySelector('#notificationsContent');
                    if (newContent) {
                        const currentContent = document.getElementById('notificationsContent');

                        if (newContent.innerHTML !== currentContent.innerHTML) {
                            currentContent.innerHTML = newContent.innerHTML;
                            this.attachNotificationEvents();
                            this.filterNotifications(this.currentFilter);
                            this.updateTabBadges();
                        }
                    }
                }

                updateModalContent(tempDiv) {
                    const newModalContent = tempDiv.querySelector('#notificationsModalContent');
                    if (newModalContent) {
                        const currentModalContent = document.getElementById('notificationsModalContent');
                        const currentModalContentHTML = currentModalContent.innerHTML;

                        if (newModalContent.innerHTML !== currentModalContentHTML) {
                            // Store current delete mode state before updating
                            const wasInDeleteMode = this.isInDeleteMode();
                            const currentCheckboxStates = this.getCurrentCheckboxStates();

                            currentModalContent.innerHTML = newModalContent.innerHTML;

                            // Restore checkbox states and delete mode
                            this.restoreCheckboxStates(currentCheckboxStates);

                            if (wasInDeleteMode) {
                                this.enterDeleteMode();
                            } else {
                                // IMPORTANT: Only update delete button text if we're NOT in delete mode
                                // This ensures the button stays as "Delete" when user hasn't clicked it
                                this.resetDeleteButtonToDefault();
                            }

                            this.attachModalNotificationEvents();
                            this.filterModalNotifications(this.modalCurrentFilter);
                            this.setupCheckboxEvents();

                            // REMOVE this line - we're already handling the button text above
                            // this.updateDeleteButtonText();
                        }
                    }
                }

                resetDeleteButtonToDefault() {
                    if (this.deleteBtn) {
                        this.deleteBtn.innerHTML = `<i class="ri-delete-bin-line"></i> Delete`;
                    }
                }

                getCurrentCheckboxStates() {
                    const states = {};
                    document.querySelectorAll('.notification-select').forEach(checkbox => {
                        const notificationId = checkbox.getAttribute('data-notification-id');
                        states[notificationId] = checkbox.checked;
                    });
                    return states;
                }

                restoreCheckboxStates(states) {
                    Object.keys(states).forEach(notificationId => {
                        const checkbox = document.querySelector(
                            `.notification-select[data-notification-id="${notificationId}"]`);
                        if (checkbox) {
                            checkbox.checked = states[notificationId];
                            if (states[notificationId]) {
                                this.selectedNotifications.add(notificationId);
                            } else {
                                this.selectedNotifications.delete(notificationId);
                            }
                        }
                    });
                }

                attachModalNotificationEvents() {
                    document.querySelectorAll('#notificationsModalContent .notifications-modal-item').forEach(item => {
                        const notificationId = item.getAttribute('data-notification-id');
                        item.replaceWith(item.cloneNode(true));
                    });

                    document.querySelectorAll('#notificationsModalContent .notifications-modal-item').forEach(item => {
                        const notificationId = item.getAttribute('data-notification-id');

                        item.addEventListener('click', (e) => {
                            if (e.target.classList.contains('notification-select') || e.target.closest(
                                    '.notification-checkbox')) {
                                return;
                            }

                            if (item.classList.contains('unread')) {
                                this.handleModalNotificationClick(notificationId, e);
                            } else {
                                const campaignUrl = item.getAttribute('href');
                                if (campaignUrl && campaignUrl !== '#') {
                                    e.preventDefault();
                                    window.location.href = campaignUrl;
                                }
                            }
                        });
                    });
                }

                handleNotificationClick(notificationId, event) {
                    if (event) event.preventDefault();

                    const notifItem = event.target.closest('.notif-item');
                    const campaignUrl = notifItem.getAttribute('href');

                    if (notifItem.classList.contains('unread')) {
                        this.markAsRead(notificationId, event);
                        this.markNotificationAsReadOnServer(notificationId);
                    }

                    if (campaignUrl && campaignUrl !== '#') {
                        window.location.href = campaignUrl;
                    }
                }

                attachNotificationEvents() {
                    document.querySelectorAll('.notif-item').forEach(item => {
                        const notificationId = item.getAttribute('data-notification-id');
                        item.replaceWith(item.cloneNode(true));
                    });

                    document.querySelectorAll('.notif-item').forEach(item => {
                        const notificationId = item.getAttribute('data-notification-id');

                        item.addEventListener('click', (e) => {
                            if (item.classList.contains('unread')) {
                                this.handleNotificationClick(notificationId, e);
                            } else {
                                const campaignUrl = item.getAttribute('href');
                                if (campaignUrl && campaignUrl !== '#') {
                                    e.preventDefault();
                                    window.location.href = campaignUrl;
                                }
                            }
                        });
                    });
                }

                markAsRead(notificationId, event) {
                    if (event && event.target.closest) {
                        const notifItem = event.target.closest('.notif-item');
                        if (notifItem && notifItem.classList.contains('unread')) {
                            notifItem.classList.remove('unread');
                            this.decrementBadgeCount();
                            this.updateTabBadges();

                            if (this.currentFilter === 'unread') {
                                notifItem.style.display = 'none';
                            }
                        }
                    }
                }

                decrementBadgeCount() {
                    const badge = document.getElementById('notificationBadge');
                    if (badge) {
                        let currentCount = parseInt(badge.textContent) || 0;
                        if (currentCount > 0) {
                            currentCount--;
                            badge.textContent = currentCount > 999 ? '999+' : currentCount;

                            if (currentCount === 0) {
                                badge.style.display = 'none';
                            }
                        }
                    }
                }

                async markNotificationAsReadOnServer(notificationId) {
                    try {
                        await fetch(`/notifications/${notificationId}/read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({})
                        });
                    } catch (error) {
                        console.error('Error marking notification as read:', error);
                    }
                }

                setupDropdown() {
                    const notifContent = document.getElementById('notificationsContent');

                    if (notifContent) {
                        notifContent.addEventListener('wheel', (e) => {
                            e.stopPropagation();
                            const isAtTop = notifContent.scrollTop === 0;
                            const isAtBottom = notifContent.scrollTop + notifContent.clientHeight >= notifContent
                                .scrollHeight - 1;

                            if ((isAtTop && e.deltaY < 0) || (isAtBottom && e.deltaY > 0)) {
                                e.preventDefault();
                            }
                        });

                        notifContent.addEventListener('touchmove', (e) => e.stopPropagation());
                        notifContent.addEventListener('scroll', (e) => e.stopPropagation());
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                window.notificationManager = new NotificationManager();
            });
        </script>
    @endauth

    {{-- Mobile Dropdown & User Menu Script --}}
    <script>
        (function() {
            const userMenu = document.getElementById('userMenu');
            const userBtn = userMenu ? userMenu.querySelector('#userMenuBtn') : null;
            const userDD = userMenu ? userMenu.querySelector('#userDropdown') : null;

            const mobileBtn = document.getElementById('mobileMenuBtn');
            const mobileDD = document.getElementById('mobileDropdown');

            // Get notification elements
            const notifMenu = document.getElementById('notificationBell');
            const notifBtn = notifMenu ? notifMenu.querySelector('#notifBtn') : null;
            const notifDD = notifMenu ? notifMenu.querySelector('#notifDropdown') : null;

            function closeAll() {
                if (userDD) userDD.classList.remove('show');
                if (mobileDD) mobileDD.classList.remove('show');
                if (notifDD) notifDD.classList.remove('show');

                if (userBtn) userBtn.setAttribute('aria-expanded', 'false');
                if (mobileBtn) mobileBtn.setAttribute('aria-expanded', 'false');
                if (notifBtn) notifBtn.setAttribute('aria-expanded', 'false');
            }

            if (userDD) userDD.addEventListener('click', (e) => e.stopPropagation());
            if (mobileDD) mobileDD.addEventListener('click', (e) => e.stopPropagation());
            if (notifDD) notifDD.addEventListener('click', (e) => e.stopPropagation());

            // Toggle user dropdown
            if (userBtn && userDD) {
                userBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const willOpen = !userDD.classList.contains('show');
                    closeAll();
                    if (willOpen) {
                        userDD.classList.add('show');
                        userBtn.setAttribute('aria-expanded', 'true');
                    }
                });
            }

            // Toggle site mobile tray
            if (mobileBtn && mobileDD) {
                mobileBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const willOpen = !mobileDD.classList.contains('show');
                    closeAll();
                    if (willOpen) {
                        mobileDD.classList.add('show');
                        mobileBtn.setAttribute('aria-expanded', 'true');
                    }
                });
            }

            // Toggle notification dropdown
            if (notifBtn && notifDD) {
                notifBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const willOpen = !notifDD.classList.contains('show');
                    closeAll();
                    if (willOpen) {
                        notifDD.classList.add('show');
                        notifBtn.setAttribute('aria-expanded', 'true');
                    }
                });
            }

            // Click anywhere outside to close everything
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.user-menu') &&
                    !e.target.closest('.mobile-menu-btn') &&
                    !e.target.closest('.notif-menu')) {
                    closeAll();
                }
            });
        })();
    </script>

</header>


{{-- Notification Modal --}}
@auth
    <div id="notificationsModal" class="notifications-modal" style="display: none;">
        <div class="notifications-modal-overlay" id="notificationsModalOverlay"></div>
        <div class="notifications-modal-content">
            <div class="notifications-modal-header">
                <div class="notifications-modal-header-top">
                    <h3>All Notifications</h3>
                    <div class="notifications-bulk-actions">
                        <button class="notifications-mark-all-read-btn" id="notificationsModalMarkAllReadBtn">
                            <i class="ri-check-double-line"></i>
                            Mark all as read
                        </button>
                        <button class="notifications-delete-btn" id="notificationsDeleteBtn">
                            <i class="ri-delete-bin-line"></i>
                            Delete
                        </button>
                        <button class="notifications-cancel-delete-btn" id="notificationsCancelDeleteBtn"
                            style="display: none;">
                            <i class="ri-close-line"></i>
                            Cancel
                        </button>
                    </div>
                </div>
                <div class="notifications-modal-tabs">
                    <button class="notifications-modal-tab active" data-filter="all">
                        All
                    </button>
                    <button class="notifications-modal-tab" data-filter="events">
                        Events
                    </button>
                    <button class="notifications-modal-tab" data-filter="campaigns">
                        Campaigns
                    </button>
                    <button class="notifications-modal-tab" data-filter="donations">
                        Donations
                    </button>
                    <button class="notifications-modal-tab" data-filter="unread">
                        Unread
                    </button>
                </div>
                <button class="notifications-modal-close-btn" id="notificationsModalCloseBtn">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="notifications-modal-body" id="notificationsModalContent">
                @php
                    $allNotifications = Auth::user()->notifications()->latest()->get();
                @endphp

                @if ($allNotifications->count() > 0)
                    @foreach ($allNotifications as $notification)
                        @php
                            $isCampaign = in_array($notification->data['type'], [
                                'campaign_ended',
                                'campaign_published',
                                'recurring_campaign_published',
                                'recurring_campaign_paused',
                            ]);
                            $isDonation = in_array($notification->data['type'], [
                                'new_donation',
                                'inkind_donation_confirmation',
                                'manual_donation_status', // ADDED THIS
                                'donation_distributed',
                            ]);
                            $isEvent = in_array($notification->data['type'], ['event_registration', 'event_reminder']);
                            $isVerification = $notification->data['type'] === 'verification_decision';
                            $isManualDonation = $notification->data['type'] === 'manual_donation_status'; // ADDED THIS

                            // Set icons based on notification type
                            if ($isCampaign) {
                                $icon = 'ri-flag-line';
                                if ($notification->data['type'] === 'campaign_published') {
                                    $icon = 'ri-megaphone-line';
                                } elseif ($notification->data['type'] === 'campaign_ended') {
                                    $icon = 'ri-flag-line';
                                } elseif ($notification->data['type'] === 'recurring_campaign_published') {
                                    $icon = 'ri-refresh-line';
                                } elseif ($notification->data['type'] === 'recurring_campaign_paused') {
                                    $icon = 'ri-pause-circle-line';
                                }
                            } elseif ($isEvent) {
                                if ($notification->data['type'] === 'event_reminder') {
                                    $icon = 'ri-alarm-line';
                                } else {
                                    $icon = 'ri-calendar-event-line';
                                }
                            } elseif ($isVerification) {
                                $icon = $notification->data['icon'] ?? 'ri-user-heart-line';
                            } elseif ($isManualDonation) {
                                // ADDED THIS
                                // Use the icon from the manual donation notification data
                                $icon = $notification->data['icon'] ?? 'ri-checkbox-circle-line';
                            } else {
                                // Use the icon from the notification data
                                $icon = $notification->data['icon'] ?? 'ri-user-heart-line';
                            }

                            // Set URL
                            $url = $notification->data['url'] ?? '#';
                            if ($isDonation && str_contains($url, '/campaigns/')) {
                                $campaignId = $notification->data['campaign_id'] ?? null;
                                $url = $campaignId ? route('campaign.view', $campaignId) : '#';
                            }
                            // For manual donation status, use the campaign URL if available
                            elseif ($isManualDonation) {
                                // ADDED THIS
                                $campaignId = $notification->data['campaign_id'] ?? null;
                                if ($campaignId) {
                                    $url = route('campaign.view', $campaignId);
                                } else {
                                    $url = route('campaignpage');
                                }
                            }
                            // For in-kind donations, use the in-kind tracking route if available
                            elseif ($notification->data['type'] === 'inkind_donation_confirmation') {
                                $url =
                                    route('inkind.tracking') . '?donation=' . ($notification->data['inkind_id'] ?? '');
                            }

                            // Set notification class
                            $notificationClass = '';
                            if ($isCampaign) {
                                $notificationClass = 'campaign-notification';
                            } elseif ($isDonation) {
                                $notificationClass = 'donation-notification';
                            } elseif ($isEvent) {
                                $notificationClass = 'event-notification';
                            } elseif ($isVerification) {
                                $notificationClass = 'verification-notification';
                            }
                        @endphp

                        <div class="notifications-modal-item-wrapper" data-notification-id="{{ $notification->id }}">
                            <div class="notification-checkbox" style="display: none;">
                                <input type="checkbox" class="notification-select"
                                    data-notification-id="{{ $notification->id }}">
                            </div>

                            @if ($isVerification)
                                <div class="notifications-modal-item {{ $notification->read_at ? '' : 'unread' }} {{ $notificationClass }}"
                                    data-notification-id="{{ $notification->id }}">
                                @else
                                    <a href="{{ $url }}"
                                        class="notifications-modal-item {{ $notification->read_at ? '' : 'unread' }} {{ $notificationClass }}"
                                        data-notification-id="{{ $notification->id }}"
                                        onclick="notificationManager.handleModalNotificationClick('{{ $notification->id }}', event)">
                            @endif
                            <div class="notifications-modal-avatar">
                                <i class="{{ $icon }}"></i>
                            </div>
                            <div class="notifications-modal-details">
                                <div class="notifications-modal-text">
                                    {{-- For manual donation, show the full message --}}
                                    @if ($isManualDonation)
                                        {{ $notification->data['full_message'] ?? $notification->data['message'] }}
                                    @else
                                        {{ $notification->data['message'] }}
                                    @endif
                                </div>
                                <div class="notifications-modal-meta">
                                    {{ $notification->created_at->format('M j, Y • g:i A') }}</div>
                                @if (isset($notification->data['amount']))
                                    <div class="notifications-modal-amount">
                                        ₱{{ number_format($notification->data['amount'], 2) }}</div>
                                @endif
                                @if ($notification->data['type'] === 'inkind_donation_confirmation' && isset($notification->data['dropoff_location']))
                                    <div class="notifications-modal-details-text">
                                    </div>
                                @endif
                                {{-- Show status for manual donations --}}
                                @if ($isManualDonation && isset($notification->data['status']))
                                    <div class="notifications-modal-status">
                                    </div>
                                @endif
                            </div>
                            @if ($isVerification)
                        </div>
                    @else
                        </a>
                    @endif
            </div>
            @endforeach
        @else
            <div class="notifications-modal-empty">
                <p>No notifications yet</p>
            </div>
            @endif
        </div>
    </div>
    </div>
@endauth
