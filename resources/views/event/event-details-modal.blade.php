@php
    $startDate = \Carbon\Carbon::parse($event->start_date);
    $endDate = \Carbon\Carbon::parse($event->end_date);
    $deadline = $event->deadline ? \Carbon\Carbon::parse($event->deadline) : null;
    $statusLabel = match ($status) {
        'ongoing' => 'Ongoing Event',
        'ended' => 'Ended Event',
        'closed' => 'Registration Closed',
        default => 'Upcoming Event',
    };
@endphp

<div class="evt-details-modal active" role="dialog" aria-modal="true" aria-labelledby="evt-details-title">
    <div class="evt-details-modal-overlay"></div>
    <div class="evt-details-modal-content">
        <button class="evt-details-modal-close" type="button" onclick="closeEventModal()" aria-label="Close event details">
            <i class="ri-close-line"></i>
        </button>

        <div class="evt-details-modal-header">
            <div class="evt-details-modal-image">
                <img src="{{ file_url($event->photo, page_media_url('event_default_image', asset('img/bg2.jpg'))) }}" alt="{{ $event->title }}">
            </div>
            <div class="evt-details-modal-heading">
                <div class="evt-status-badge evt-status-{{ $status }}">
                    {{ $statusLabel }}
                </div>
                <h2 class="evt-details-modal-title" id="evt-details-title">{{ $event->title }}</h2>
            </div>
        </div>

        <div class="evt-details-modal-body">
            <div class="evt-details-modal-info">
                <div class="evt-detail-item">
                    <i class="ri-calendar-event-line"></i>
                    <div>
                        <span class="evt-detail-label">Date and time</span>
                        <span class="evt-detail-value">
                            {{ $startDate->format('M d, Y') }} &bull; {{ $startDate->format('h:i A') }} -
                            {{ $endDate->format('M d, Y') }} &bull; {{ $endDate->format('h:i A') }}
                        </span>
                    </div>
                </div>
                <div class="evt-detail-item">
                    <i class="ri-map-pin-line"></i>
                    <div>
                        <span class="evt-detail-label">Location</span>
                        <span class="evt-detail-value">{{ $event->location }}</span>
                    </div>
                </div>
                @if ($deadline)
                    <div class="evt-detail-item">
                        <i class="ri-timer-line"></i>
                        <div>
                            <span class="evt-detail-label">Registration deadline</span>
                            <span class="evt-detail-value">
                                {{ $deadline->format('M d, Y') }} &bull; {{ $deadline->format('h:i A') }}
                            </span>
                        </div>
                    </div>
                @endif
                <div class="evt-detail-item">
                    <i class="ri-group-line"></i>
                    <div>
                        <span class="evt-detail-label">Participants</span>
                        <span class="evt-detail-value">{{ number_format($participantCount) }} registered</span>
                    </div>
                </div>
            </div>

            @if ($event->lat && $event->lng)
                <div class="evt-details-map">
                    <div data-tk-map-static data-lat="{{ $event->lat }}" data-lng="{{ $event->lng }}"
                        data-title="{{ $event->title }}" data-description="{{ $event->location }}"
                        data-height="220px"></div>
                </div>
            @endif

            <section class="evt-details-modal-section">
                <h3>About this event</h3>
                <p>{{ $event->description }}</p>
            </section>

            @if ($event->volunteerRoles && $event->volunteerRoles->count() > 0)
                <section class="evt-details-modal-section">
                    <h3>Volunteer roles</h3>
                    <div class="evt-volunteer-roles">
                        @foreach ($event->volunteerRoles as $role)
                            <div class="evt-volunteer-role">
                                <h4>{{ $role->name }}</h4>
                                @if ($role->description)
                                    <p>{{ $role->description }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($event->coordinator_name || $event->coordinator_email || $event->coordinator_phone)
                <section class="evt-details-modal-section">
                    <h3>Event coordinator</h3>
                    <div class="evt-coordinator-info">
                        @if ($event->coordinator_name)
                            <div class="evt-coordinator-detail">
                                <i class="ri-user-line"></i>
                                <span>{{ $event->coordinator_name }}</span>
                            </div>
                        @endif
                        @if ($event->coordinator_email)
                            <div class="evt-coordinator-detail">
                                <i class="ri-mail-line"></i>
                                <span>{{ $event->coordinator_email }}</span>
                            </div>
                        @endif
                        @if ($event->coordinator_phone)
                            <div class="evt-coordinator-detail">
                                <i class="ri-phone-line"></i>
                                <span>{{ $event->coordinator_phone }}</span>
                            </div>
                        @endif
                    </div>
                </section>
            @endif
        </div>

        <div class="evt-details-modal-footer">
            <button class="evt-details-modal-close-btn" type="button" onclick="closeEventModal()">Close</button>

            @if ($isRegistered)
                <button class="evt-details-modal-register-btn" type="button" disabled>Already Registered</button>
            @elseif($status === 'ended')
                <button class="evt-details-modal-register-btn" type="button" disabled>Event Ended</button>
            @elseif($status === 'ongoing')
                <button class="evt-details-modal-register-btn" type="button" disabled>Event Ongoing</button>
            @elseif($status === 'closed')
                <button class="evt-details-modal-register-btn" type="button" disabled>Registration Closed</button>
            @elseif(!Auth::check())
                <a href="{{ route('login.register') }}" class="evt-details-modal-register-btn">
                    Login to Register
                </a>
            @else
                <button class="evt-details-modal-register-btn" type="button"
                    onclick='registerForEvent(@json((string) $event->event_id))'>
                    Register Now
                </button>
            @endif
        </div>
    </div>
</div>
