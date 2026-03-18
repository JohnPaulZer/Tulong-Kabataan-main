<section id="volunteerList" class="vol-list">
    @if (isset($volunteers) && count($volunteers) > 0)
        @foreach ($volunteers as $vol)
            <article class="vol-item volunteer-item" data-status="active">
                <div class="vol-top">
                    <div class="vol-left">
                        <div class="avatar blue">
                            {{ strtoupper(substr($vol['first_name'], 0, 1)) }}{{ strtoupper(substr($vol['last_name'], 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:700">{{ $vol['first_name'] }} {{ $vol['last_name'] }}</div>
                            <div style="color:var(--muted);font-size:13px">{{ $vol['email'] }}</div>
                        </div>
                    </div>

                    <div class="vol-right">
                        <button class="link-btn" data-id="{{ md5($vol['email']) }}">
                            View Details
                        </button>
                        <button class="link-btn view-profile-btn" data-email="{{ $vol['email'] }}">
                            View Profile
                        </button>
                    </div>
                </div>

                <div id="volunteer-{{ md5($vol['email']) }}" class="details" aria-hidden="true">
                    <div class="details-grid">
                        <div>
                            <h4 style="margin:0 0 8px 0">Event Participation Summary</h4>
                            <div style="display:flex;gap:8px;flex-wrap:wrap">
                                <span class="chip" style="background:#ecfdf5;color:#065f46">
                                    ✅ Attended: <strong>{{ $vol['attended'] }}</strong>
                                </span>
                                <span class="chip" style="background:#fff7ed;color:#b45309">
                                    ⚠️ Missed: <strong>{{ $vol['missed'] }}</strong>
                                </span>
                                <span class="chip" style="background:#e0f2fe;color:#1d4ed8">
                                    📅 Total Joined: <strong>{{ $vol['total'] }}</strong>
                                </span>
                            </div>
                        </div>

                        <div>
                            <h4 style="margin:0 0 8px 0">Recent Events</h4>
                            @if ($vol['recent']->isNotEmpty())
                                @foreach ($vol['recent'] as $reg)
                                    <div style="margin-bottom:8px">
                                        <div style="font-weight:700">{{ $reg->event->title }}</div>
                                        <div style="color:var(--muted);font-size:13px">
                                            {{ \Carbon\Carbon::parse($reg->event->start_date)->format('F j, Y') }}
                                            • {{ ucfirst($reg->status) }}
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p style="font-size:13px;color:var(--muted);margin:0">No recent events.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </article>
        @endforeach
    @else
        <p style="color:var(--muted);font-size:14px;">No volunteer data available.</p>
    @endif
</section>

<!-- Volunteer Profile Modal -->
<div id="volunteerProfileModal"
    style="display:none; position:fixed; inset:0;
            background:rgba(0,0,0,0.55);
            align-items:center; justify-content:center;
            z-index:1000; backdrop-filter:blur(2px);">
    <div class="modal-card"
        style="
        background:#fff;
        border-radius:16px;
        width:460px;
        max-width:90%;
        padding:28px;
        position:relative;
        box-shadow:0 10px 30px rgba(0,0,0,0.1);
        animation:fadeInUp .25s ease;">

        <button id="closeProfileModal"
            style="position:absolute; top:14px; right:14px;
                   background:none; border:none; font-size:22px;
                   color:#9ca3af; cursor:pointer;">&times;</button>

        <h2 style="margin:0 0 20px 0; font-size:22px; font-weight:700; color:#1f2937;">
            Volunteer Profile
        </h2>

        <div id="profileContent" style="display:none;"></div>
    </div>
</div>






{{-- PROFILE MODAL SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('volunteerProfileModal');
        const modalContent = document.getElementById('profileContent');
        const closeBtn = document.getElementById('closeProfileModal');

        function openProfileModal(email) {
            // Hide old content until loaded
            modalContent.style.display = 'none';
            modal.style.display = 'flex';
            modalContent.innerHTML = '';

            // Fetch volunteer profile
            fetch(`/administrator/volunteers/profile/${encodeURIComponent(email)}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.success) throw new Error('Volunteer not found');

                    const v = data.volunteer;
                    modalContent.innerHTML = `
          <div style="display:grid;gap:10px;font-size:15px;color:#374151;animation:fadeInUp .3s ease;">
            <div><strong>Name:</strong> ${v.first_name} ${v.last_name}</div>
            <div><strong>Email:</strong> ${v.email}</div>
            <div><strong>Phone:</strong> ${v.phone ?? '—'}</div>
            <div><strong>Messenger Link:</strong> ${
              v.messenger_link
                ? `<a href="${v.messenger_link}" target="_blank" style="color:#2563eb;text-decoration:none;">${v.messenger_link}</a>`
                : '—'
            }</div>
            <div><strong>Age:</strong> ${v.age ?? '—'}</div>
            <div><strong>Sex:</strong> ${v.sex ? v.sex.charAt(0).toUpperCase() + v.sex.slice(1) : '—'}</div>
            <div><strong>Address:</strong> ${v.address ?? '—'}</div>
          </div>
        `;
                    modalContent.style.display = 'block';
                })
                .catch(err => {
                    console.error(err);
                    modalContent.innerHTML = `<p style="color:#dc2626;">Error loading profile.</p>`;
                    modalContent.style.display = 'block';
                });
        }

        // Bind view-profile buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('view-profile-btn')) {
                const email = e.target.dataset.email;
                if (email) openProfileModal(email);
            }
        });

        // Close modal
        closeBtn.addEventListener('click', () => (modal.style.display = 'none'));
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.style.display = 'none';
        });
    });
</script>
