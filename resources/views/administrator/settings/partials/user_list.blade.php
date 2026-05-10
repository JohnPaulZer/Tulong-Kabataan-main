@php
    $items = $users ?? collect();
@endphp

@if ($items->count() === 0)
    <div class="empty">
        <i class="ri-user-search-line"></i>
        <p>No users match the current filters.</p>
    </div>
@else
    <table class="users" role="table">
        <thead>
            <tr>
                <th>User</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Joined</th>
                <th style="width: 220px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $u)
                @php
                    $initials = strtoupper(substr($u->first_name ?? '?', 0, 1) . substr($u->last_name ?? '', 0, 1));
                    $status = $u->status ?? 'active';
                    $isUnverified = empty($u->email_verified_at);
                    $fullName = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: 'Unnamed user';
                @endphp
                <tr>
                    <td>
                        <div class="user-cell">
                            @if ($u->profile_photo_url)
                                <img src="{{ $u->profile_photo_url }}" alt=""
                                    style="width:38px;height:38px;border-radius:999px;object-fit:cover;">
                            @else
                                <div class="avatar">{{ $initials ?: '?' }}</div>
                            @endif
                            <div>
                                <div style="font-weight:700;">{{ $fullName }}</div>
                                <div style="color:#6b7280;font-size:12px;">{{ $u->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $u->phone_number ?? '—' }}</td>
                    <td>
                        @if ($status === 'suspended')
                            <span class="badge suspended">Suspended</span>
                        @elseif ($isUnverified)
                            <span class="badge unverified">Unverified</span>
                        @else
                            <span class="badge active">Active</span>
                        @endif
                    </td>
                    <td>{{ optional($u->created_at)->format('M d, Y') ?? '—' }}</td>
                    <td>
                        <div class="actions-col">
                            @if ($status === 'suspended')
                                <button type="button" class="btn btn-success btn-sm"
                                    data-action="activate"
                                    data-user-id="{{ $u->user_id }}"
                                    data-user-name="{{ $fullName }}">
                                    <i class="ri-user-follow-line"></i> Activate
                                </button>
                            @else
                                <button type="button" class="btn btn-warning btn-sm"
                                    data-action="suspend"
                                    data-user-id="{{ $u->user_id }}"
                                    data-user-name="{{ $fullName }}">
                                    <i class="ri-user-forbid-line"></i> Suspend
                                </button>
                            @endif
                            <button type="button" class="btn btn-danger btn-sm"
                                data-action="delete"
                                data-user-id="{{ $u->user_id }}"
                                data-user-name="{{ $fullName }}">
                                <i class="ri-delete-bin-line"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($items->lastPage() > 1)
        <div style="padding:12px 14px;color:#6b7280;font-size:13px;">
            Showing {{ $items->firstItem() }}–{{ $items->lastItem() }} of {{ $items->total() }} users.
        </div>
    @endif
@endif
