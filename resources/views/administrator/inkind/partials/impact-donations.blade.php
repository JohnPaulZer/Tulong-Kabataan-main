@php
    // Get only received donations
    $receivedDonations = $donations->filter(fn($d) => $d->status === 'Received');
    $groupedDonations = [];

    foreach ($receivedDonations as $donation) {
        if ($donation->user_id) {
            $donorKey = 'user_' . $donation->user_id;
        } elseif ($donation->donor_email) {
            $donorKey = 'email_' . $donation->donor_email;
        } elseif ($donation->donor_name) {
            $donorKey = 'name_' . $donation->donor_name;
        } else {
            $donorKey = 'anonymous_' . $donation->inkind_id;
        }

        if (!isset($groupedDonations[$donorKey])) {
            $groupedDonations[$donorKey] = [];
        }
        $groupedDonations[$donorKey][] = $donation;
    }
@endphp

@if (count($groupedDonations) > 0)
    @foreach ($groupedDonations as $donorKey => $donorDonations)
        @php
            $firstDonation = $donorDonations[0];
            $donorName = $firstDonation->donor_name ?? 'Anonymous Donor';
            $donorEmail = $firstDonation->donor_email ?? '';
            $totalItems = array_sum(array_map(fn($d) => intval($d->quantity), $donorDonations));

            // Get initials
            $initials = 'AN';
            if ($donorName !== 'Anonymous Donor') {
                $words = explode(' ', $donorName);
                if (count($words) >= 2) {
                    $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                } else {
                    $initials = strtoupper(substr($donorName, 0, 2));
                }
            }

            $colors = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444'];
            $avatarColor = $colors[array_rand($colors)];

            if ($firstDonation->user_id) {
                $donorType = 'Registered User';
            } elseif ($donorEmail) {
                $donorType = 'Guest Donor';
            } else {
                $donorType = 'Anonymous';
            }
        @endphp

        <div class="donation-group" data-user="{{ $donorKey }}">
            <div class="donation-group-header">
                <div class="donor-info">
                    <div class="donor-avatar" style="background: {{ $avatarColor }};">
                        @php
                            $avatar = null;
                            $user = $firstDonation->user;

                            if ($user && $user->profile_photo_url) {
                                $avatar = $user->profile_photo_url;
                                if (str_contains($avatar, 'googleusercontent.com') && !str_contains($avatar, 'sz=')) {
                                    $avatar .= (str_contains($avatar, '?') ? '&' : '?') . 'sz=64';
                                }
                                if (!Str::startsWith($avatar, 'http')) {
                                    $avatar = asset('storage/' . $avatar);
                                }
                            }
                        @endphp

                        @if ($avatar)
                            <img src="{{ $avatar }}" alt="Profile photo" referrerpolicy="no-referrer">
                        @else
                            <span class="avatar-initials">{{ $initials }}</span>
                        @endif
                    </div>

                    <div class="donor-details">
                        <div class="donor-name">
                            {{ $donorName === 'Anonymous Donor' && !$donorEmail ? 'Anonymous Donor' : $donorName }}
                        </div>
                        <div class="donor-meta">
                            {{ count($donorDonations) }} donation{{ count($donorDonations) > 1 ? 's' : '' }} •
                            {{ $totalItems }} total items
                            @if ($donorEmail)
                                <br>{{ $donorEmail }}
                            @endif
                            <br><span class="donor-type">{{ $donorType }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <button type="button" class="select-group-btn" data-user="{{ $donorKey }}">
                        Select All
                    </button>
                </div>
            </div>
            <div class="donation-items">
                @foreach ($donorDonations as $index => $donation)
                    @php
                        $formattedDate = 'Unknown date';
                        try {
                            $dateObj = new DateTime($donation->created_at ?? $donation->updated_at);
                            $formattedDate = $dateObj->format('M j, Y');
                        } catch (Exception $e) {
                        }

                        $locationName = $donation->dropOffPoint->name ?? 'N/A';
                    @endphp

                    <div class="donation-item" data-id="{{ $donation->inkind_id }}">
                        <div class="item-checkbox" data-id="{{ $donation->inkind_id }}"></div>
                        <div class="item-details">
                            <div class="item-header">
                                <strong class="item-name">{{ $donation->item_name ?: 'Unnamed Item' }}</strong>
                                <div class="item-meta">
                                    <span class="item-quantity"
                                        style="font-size: 13px; color: #16a34a; font-weight: 500;">
                                        {{ $donation->quantity ?: 0 }} items
                                    </span>
                                    <span class="item-category"
                                        style="font-size: 12px; color: #6b7280; background: #f3f4f6; padding: 2px 8px; border-radius: 12px;">
                                        {{ $donation->category ?: 'Uncategorized' }}
                                    </span>
                                </div>
                            </div>
                            <div class="item-footer">
                                <div class="item-date">
                                    <i class="ri-calendar-line"></i>
                                    <span>{{ $formattedDate }}</span>
                                </div>
                                <div class="item-location">
                                    <i class="ri-map-pin-line"></i>
                                    <span>{{ $locationName }}</span>
                                </div>
                            </div>
                        </div>
                        <input type="checkbox" id="donation-checkbox-{{ $donation->inkind_id }}"
                            name="selected_donations[]" value="{{ $donation->inkind_id }}" class="hidden-checkbox">
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@else
    <div id="no-donations" style="padding: 50px; text-align: center;">
        <i class="ri-inbox-line" style="font-size: 48px; color: #d1d5db; margin-bottom: 15px;"></i>
        <h4 style="color: #6b7280; margin-bottom: 10px;">No Available Donations</h4>
        <p style="color: #9ca3af; font-size: 14px;">
            No received donations found. All donations must be marked as "Received" to be included.
        </p>
    </div>
@endif

<div id="search-no-results" style="display: none; padding: 40px; text-align: center; color: #6b7280;">
    <i class="ri-search-line" style="font-size: 32px; margin-bottom: 12px;"></i>
    <p>No donations match your search criteria.</p>
</div>
