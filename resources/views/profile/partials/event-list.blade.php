<!-- events-list.blade.php -->
@forelse($registrations as $reg)
    @php
        $event = $reg->event;
        $role = $reg->volunteerRoles;
        $now = \Carbon\Carbon::now();
        $start = \Carbon\Carbon::parse($event->start_date);
        $end = \Carbon\Carbon::parse($event->end_date);

        // Determine which status to display
        $statusToDisplay = strtolower($reg->status);

        // If status is 'registered', check event timing
        if ($statusToDisplay === 'registered') {
            if ($now->lt($start)) {
                $displayStatus = 'upcoming';
                $badgeClass = 'usereve-badge--green';
            } elseif ($now->between($start, $end)) {
                $displayStatus = 'ongoing';
                $badgeClass = 'usereve-badge--yellow';
            } else {
                $displayStatus = 'completed';
                $badgeClass = 'usereve-badge--gray';
            }
        } else {
            // Use the actual registration status
            $displayStatus = $statusToDisplay;
            $badgeClass = match ($statusToDisplay) {
                'registered' => 'usereve-badge--green',
                'ongoing' => 'usereve-badge--yellow',
                'attended' => 'usereve-badge--purple',
                'absent' => 'usereve-badge--red',
                default => 'usereve-badge--gray',
            };
        }

        $showUnregister = $now->lt($start->copy()->subHours(12));
    @endphp

    <div class="usereve-event-card" data-status="{{ $displayStatus }}">
        <div class="usereve-event-head">
            <div class="left">
                <div class="usereve-title-row">
                    <h3 class="usereve-event-title">{{ $event->title ?? 'Untitled Event' }}</h3>

                    <span class="usereve-badge {{ $badgeClass }}">
                        {{ ucfirst($displayStatus) }}
                    </span>

                    @if ($role)
                        <span class="usereve-badge usereve-badge--purple" style="background-color:#8b5cf6;color:#fff;">
                            {{ $role->name ?? 'Volunteer' }}
                        </span>
                    @endif
                </div>

                <div class="usereve-meta-row">
                    <span class="usereve-meta">
                        <i class="ri-calendar-line"></i>
                        {{ \Carbon\Carbon::parse($event->start_date)->format('F j, Y g:i A') }}
                        –
                        {{ \Carbon\Carbon::parse($event->end_date)->format('F j, Y g:i A') }}
                    </span>
                    <span class="usereve-meta">
                        <i class="ri-map-pin-line"></i> {{ $event->location }}
                    </span>
                </div>

                <p class="usereve-desc">
                    {{ $event->description ?? 'No description provided.' }}
                </p>

                <div class="usereve-meta-row">
                    <span class="usereve-meta">
                        <i class="ri-time-line"></i>
                        Registered:
                        {{ \Carbon\Carbon::parse($reg->registered_at ?? now())->format('M d, Y') }}
                    </span>
                </div>
            </div>

            <div class="right">
                <div class="usereve-btn-row">
                    @if ($showUnregister)
                        <button type="button" class="usereve-btn usereve-btn-danger usereve-rounded-button"
                            onclick="confirmUnregister({{ $event->event_id }}, '{{ addslashes($event->title) }}')">
                            Unregister
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="usereve-no-results-events">
        <i class="ri-search-line"></i>
        <h3>No events found</h3>
        <p>You haven't registered for any events yet.</p>
    </div>
@endforelse
