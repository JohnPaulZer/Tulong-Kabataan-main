<div class="evt-details-modal active">
    <div class="evt-details-modal-overlay"></div>
    <div class="evt-details-modal-content">
        <button class="evt-details-modal-close" onclick="closeEventModal()">
            <i class="ri-close-line"></i>
        </button>

        <div class="evt-details-modal-header">
            <div class="evt-details-modal-image">
                <img src="{{ asset('storage/' . $event->photo) }}" alt="{{ $event->title }}">
            </div>
        </div>

        <div class="evt-details-modal-body">
            <h2 class="evt-details-modal-title">{{ $event->title }}</h2>

            <!-- Event Status Badge -->
            <div class="evt-status-badge evt-status-{{ $status }}">
                {{ ucfirst($status) }} Event
            </div>

            <div class="evt-details-modal-info">
                <div class="evt-detail-item">
                    <i class="ri-time-line"></i>
                    <span>
                        {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y • h:i A') }} -
                        {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y • h:i A') }}
                    </span>
                </div>
                <div class="evt-detail-item">
                    <i class="ri-map-pin-line"></i>
                    <span>{{ $event->location }}</span>
                </div>
                @if ($event->deadline)
                    <div class="evt-detail-item">
                        <i class="ri-calendar-line"></i>
                        <span>Registration deadline:
                            {{ \Carbon\Carbon::parse($event->deadline)->format('M d, Y • h:i A') }}</span>
                    </div>
                @endif
                <div class="evt-detail-item">
                    <i class="ri-group-line"></i>
                    <span>{{ $participantCount }} participants registered</span>
                </div>
            </div>

            <div class="evt-details-modal-section">
                <h3>About this event</h3>
                <p>{{ $event->description }}</p>
            </div>

            <!-- Volunteer Roles -->
            @if ($event->volunteerRoles && $event->volunteerRoles->count() > 0)
                <div class="evt-details-modal-section">
                    <h3>Volunteer Roles Available</h3>
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
                </div>
            @endif

            <!-- Coordinator Info -->
            @if ($event->coordinator_name || $event->coordinator_email || $event->coordinator_phone)
                <div class="evt-details-modal-section">
                    <h3>Event Coordinator</h3>
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
                </div>
            @endif
        </div>

        <div class="evt-details-modal-footer">
            <button class="evt-details-modal-close-btn" onclick="closeEventModal()">Close</button>

            @if ($isRegistered)
                <button class="evt-details-modal-register-btn" disabled>Already Registered</button>
            @elseif($status === 'ended')
                <button class="evt-details-modal-register-btn" disabled>Event Ended</button>
            @elseif($status === 'ongoing')
                <button class="evt-details-modal-register-btn" disabled>Event Ongoing</button>
            @elseif($status === 'closed')
                <button class="evt-details-modal-register-btn" disabled>Registration Closed</button>
            @elseif(!Auth::check())
                <a href="{{ route('login.register') }}" class="evt-details-modal-register-btn">
                    Login to Register
                </a>
            @else
                <button class="evt-details-modal-register-btn" onclick="registerForEvent({{ $event->event_id }})">
                    Register Now
                </button>
            @endif
        </div>
    </div>
</div>
