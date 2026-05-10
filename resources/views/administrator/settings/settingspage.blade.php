<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tulong Kabataan | Admin Settings</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Merriweather:wght@700;900&display=swap"
        rel="stylesheet">
    @vite('resources/js/app.js')

    <style>
        :root {
            --primary: #3b82f6;
            --bg: #f9fafb;
            --text: #111827;
            --muted: #6b7280;
            --muted-700: #374151;
            --border: #e5e7eb;
            --card: #ffffff;
            --shadow: 0 1px 2px rgba(0, 0, 0, .06), 0 1px 3px rgba(0, 0, 0, .1);
            --radius: 12px;
            --header-h: 64px;
            --sidebar-w: 256px;
        }

        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); }

        /* Header */
        .header {
            position: fixed; top: 0; left: 0; right: 0; height: var(--header-h);
            background: #fff; z-index: 50; box-shadow: var(--shadow);
        }
        .header__inner {
            height: 100%; display: flex; align-items: center; justify-content: space-between;
            padding: 0 16px;
        }
        .header__left { display: flex; align-items: center; gap: 12px; }
        .menu-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border: 0; background: #1f2937; color: #fff;
            border-radius: 8px; cursor: pointer;
        }
        .brand { font-weight: 1000; font-size: 25px; letter-spacing: .06em; color: black; }
        .logo-word { font-family: "Pacifico", cursive; color: var(--primary); font-size: 24px; }
        .notif {
            position: relative; display: inline-flex; align-items: center; justify-content: center;
            width: 40px; height: 40px; border-radius: 10px; background: white; color: #000; border: 0;
            cursor: pointer;
        }

        /* Sidebar */
        .sidebar {
            position: fixed; left: 0; top: var(--header-h); bottom: 0; width: var(--sidebar-w);
            background: #1e3a8a; color: #fff; transform: translateX(-100%);
            transition: transform .3s ease; z-index: 40; padding: 16px; overflow: auto;
        }
        .sidebar.open { transform: translateX(0); }
        .sidebar nav { display: grid; gap: 8px; }
        .side-link {
            display: flex; align-items: center; gap: 12px; padding: 12px 16px;
            border-radius: 8px; background: transparent; border: 0; color: #d1d5db;
            text-decoration: none; cursor: pointer; white-space: nowrap;
        }
        .side-link:hover { background: #1e40af; }
        .side-link.active { background: var(--primary); color: #fff !important; }

        .overlay { position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 30; display: none; }
        .overlay.show { display: block; }

        .main { padding-top: var(--header-h); min-height: 100vh; }
        @media (min-width:1024px) {
            .main { margin-left: var(--sidebar-w); }
            .menu-btn { display: none; }
            .sidebar { transform: translateX(0); }
            .overlay { display: none !important; }
            .header__inner { padding: 0 24px; }
        }

        .container { max-width: 1100px; margin: 0 auto; padding: 24px 16px; }

        /* Page header */
        .page-header { margin-bottom: 20px; }
        .page-header h1 { margin: 0 0 6px; font-size: 26px; font-weight: 800; }
        .page-header p { margin: 0; color: var(--muted); }

        /* Tabs */
        .tabs-nav { display: flex; gap: 10px; margin-bottom: 24px; border-bottom: 2px solid var(--border); overflow-x: auto; }
        .tab-btn {
            background: none; border: 0; padding: 12px 20px; font-family: 'Merriweather', serif;
            font-size: 15px; font-weight: 700; color: var(--muted); cursor: pointer;
            border-bottom: 3px solid transparent; transition: all .25s;
        }
        .tab-btn:hover { color: var(--primary); }
        .tab-btn.active { color: var(--primary); border-bottom-color: var(--primary); }
        .tab-content { display: none; animation: fadeIn .25s ease; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px);} to { opacity: 1; transform: translateY(0);} }

        /* Cards */
        .card {
            background: var(--card); border: 1px solid var(--border); border-radius: 14px;
            box-shadow: var(--shadow); padding: 22px; margin-bottom: 18px;
        }
        .card h2 { margin: 0 0 4px; font-size: 18px; font-weight: 800; }
        .card .muted { color: var(--muted); font-size: 13px; margin: 0 0 16px; }

        .row { display: grid; grid-template-columns: 1fr; gap: 14px; }
        @media (min-width: 640px) { .row-2 { grid-template-columns: 1fr 1fr; } }

        label.field-label { display: block; font-size: 13px; font-weight: 700; color: var(--muted-700); margin-bottom: 6px; }
        input[type="text"], textarea, select {
            width: 100%; padding: 10px 12px; border: 1px solid #cfd9e8; border-radius: 10px;
            font-family: inherit; font-size: 14px; color: var(--text); background: #fff;
        }
        input:focus, textarea:focus, select:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(59,130,246,0.12);
        }
        textarea { min-height: 84px; resize: vertical; }

        /* Toggle row */
        .toggle-row {
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
            padding: 14px 0; border-bottom: 1px dashed var(--border);
        }
        .toggle-row:last-child { border-bottom: 0; }
        .toggle-row .copy strong { display: block; font-size: 14px; color: var(--text); }
        .toggle-row .copy small { color: var(--muted); font-size: 12px; }

        /* Switch */
        .switch { position: relative; display: inline-block; width: 46px; height: 26px; flex: 0 0 auto; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute; inset: 0; cursor: pointer; background: #cbd5e1; border-radius: 999px;
            transition: .25s;
        }
        .slider:before {
            content: ""; position: absolute; height: 20px; width: 20px; left: 3px; top: 3px;
            background: #fff; border-radius: 50%; transition: .25s; box-shadow: 0 1px 3px rgba(0,0,0,.2);
        }
        .switch input:checked + .slider { background: var(--primary); }
        .switch input:checked + .slider:before { transform: translateX(20px); }

        /* Buttons */
        .btn {
            min-height: 42px; padding: 0 16px; border-radius: 10px; font-weight: 700;
            cursor: pointer; border: 1px solid transparent; font-family: inherit; font-size: 14px;
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); box-shadow: 0 8px 18px rgba(59,130,246,.2);}
        .btn-primary:hover { filter: brightness(0.95); }
        .btn-secondary { background: #fff; color: var(--primary); border: 1px solid #cfd9e8; }
        .btn-danger { background: #dc2626; color: #fff; border-color: #dc2626; }
        .btn-warning { background: #f59e0b; color: #fff; border-color: #f59e0b; }
        .btn-success { background: #10b981; color: #fff; border-color: #10b981; }
        .btn-sm { min-height: 34px; padding: 0 12px; font-size: 13px; }

        .actions-bar { display: flex; justify-content: flex-end; gap: 10px; margin-top: 6px; }

        /* Users tab */
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 14px; margin-bottom: 18px;
        }
        .stat-card {
            background: var(--card); border: 1px solid var(--border); border-radius: 12px;
            padding: 16px; display: flex; align-items: center; gap: 12px;
        }
        .stat-card .icon {
            width: 44px; height: 44px; display: grid; place-items: center; border-radius: 10px;
            background: #eaf2ff; color: var(--primary); font-size: 22px;
        }
        .stat-card .big { font-size: 22px; font-weight: 800; }
        .stat-card .lbl { color: var(--muted); font-size: 12px; text-transform: uppercase; letter-spacing: .04em; }

        .toolbar {
            display: flex; gap: 10px; flex-wrap: wrap; align-items: center;
            padding: 14px; background: #fff; border: 1px solid var(--border);
            border-radius: 12px; margin-bottom: 14px;
        }
        .toolbar input[type="search"] { flex: 1 1 260px; min-height: 40px; }
        .filter-btn {
            padding: 8px 14px; border: 1px solid #cfd9e8; border-radius: 999px;
            background: #fff; font-weight: 700; color: #52627e; cursor: pointer; font-size: 13px;
        }
        .filter-btn.active { background: #eaf2ff; color: var(--primary); border-color: #a9c7ff; }

        .users-wrap {
            background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden;
        }
        table.users { width: 100%; border-collapse: separate; border-spacing: 0; }
        table.users th {
            text-align: left; background: #f7faff; color: #52627e; font-size: 12px;
            text-transform: uppercase; letter-spacing: .04em; padding: 12px 14px;
            border-bottom: 1px solid var(--border);
        }
        table.users td { padding: 14px; border-bottom: 1px solid #eef2f7; vertical-align: middle; }
        table.users tr:last-child td { border-bottom: 0; }
        .user-cell { display: flex; align-items: center; gap: 10px; }
        .avatar {
            width: 38px; height: 38px; border-radius: 999px;
            background: #e0e7ff; color: #4338ca; display: grid; place-items: center;
            font-weight: 700;
        }
        .badge {
            display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 11px;
            font-weight: 700; text-transform: uppercase; letter-spacing: .04em;
        }
        .badge.active { background: #dcfce7; color: #166534; }
        .badge.suspended { background: #fee2e2; color: #991b1b; }
        .badge.unverified { background: #fef3c7; color: #92400e; }
        .actions-col { display: flex; gap: 6px; flex-wrap: wrap; }

        .empty {
            padding: 40px 16px; text-align: center; color: var(--muted);
        }
        .empty i { font-size: 44px; opacity: .4; display: block; margin-bottom: 8px; }

        .toast {
            position: fixed; bottom: 20px; right: 20px; z-index: 200;
            background: #111827; color: #fff; padding: 12px 16px; border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,.2); opacity: 0; transform: translateY(10px);
            transition: all .25s; pointer-events: none;
        }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.success { background: #059669; }
        .toast.error { background: #dc2626; }

        .confirm-modal {
            position: fixed; inset: 0; background: rgba(4,15,38,.5); display: none;
            align-items: center; justify-content: center; z-index: 300; padding: 16px;
        }
        .confirm-modal.show { display: flex; }
        .confirm-dialog {
            background: #fff; border-radius: 14px; max-width: 420px; width: 100%;
            padding: 22px; text-align: center; box-shadow: 0 24px 60px rgba(0,0,0,.3);
        }
        .confirm-dialog h3 { margin: 0 0 6px; }
        .confirm-dialog p { color: var(--muted); margin: 0 0 18px; }
        .confirm-actions { display: flex; gap: 10px; justify-content: center; }
    </style>
    @include('administrator.partials.admin-theme')
</head>

<body class="admin-page admin-settings-page">
    @include('administrator.partials.loading-screen')

    <header class="header">
        <div class="header__inner">
            <div class="header__left">
                <button id="sidebarToggle" class="menu-btn" aria-label="Toggle menu">
                    <i class="ri-menu-line"></i>
                </button>
                <h1 class="brand">ADMINISTRATOR</h1>
            </div>
            <div class="logo-word">
                <img src="{{ asset('img/log.png') }}" alt="" style="width: 120px; height: 60px; margin-top: 8px;">
            </div>
            <button class="notif" aria-label="Notifications">
                <i class="ri-notification-3-line"></i>
            </button>
        </div>
    </header>

    @include('administrator.partials.main-sidebar')
    <div class="overlay" id="sidebarOverlay"></div>

    <main class="main">
        <div class="container">
            <div class="page-header">
                <h1>Settings</h1>
                <p>Manage what users see and can do on the public side of Tulong Kabataan.</p>
            </div>

            <nav class="tabs-nav" role="tablist" aria-label="Settings sections">
                <button type="button" class="tab-btn active" data-tab="general" role="tab" aria-selected="true">
                    <i class="ri-settings-3-line"></i> General
                </button>
                <button type="button" class="tab-btn" data-tab="features" role="tab">
                    <i class="ri-toggle-line"></i> Features
                </button>
                <button type="button" class="tab-btn" data-tab="maintenance" role="tab">
                    <i class="ri-tools-line"></i> Maintenance
                </button>
                <button type="button" class="tab-btn" data-tab="users" role="tab">
                    <i class="ri-user-settings-line"></i> User Accounts
                </button>
            </nav>

            <form id="settingsForm" onsubmit="return saveSettings(event)">
                @csrf

                {{-- ============ GENERAL TAB ============ --}}
                <section id="tab-general" class="tab-content active" role="tabpanel">
                    <div class="card">
                        <h2>Announcement Banner</h2>
                        <p class="muted">Show a banner at the top of the user-side landing page. Useful for news, alerts, or campaigns.</p>

                        <div class="toggle-row">
                            <div class="copy">
                                <strong>Show announcement banner</strong>
                                <small>Displays the banner on the user-facing pages.</small>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="announcement_enabled" value="1"
                                    {{ !empty($settings['site.announcement.enabled']) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="row row-2" style="margin-top: 14px;">
                            <div>
                                <label class="field-label" for="announcement_title">Title</label>
                                <input type="text" id="announcement_title" name="announcement_title"
                                    maxlength="120"
                                    value="{{ $settings['site.announcement.title'] ?? '' }}"
                                    placeholder="e.g. Typhoon relief donations open">
                            </div>
                            <div>
                                <label class="field-label" for="announcement_variant">Style</label>
                                <select id="announcement_variant" name="announcement_variant">
                                    @php $variant = $settings['site.announcement.variant'] ?? 'info'; @endphp
                                    <option value="info" {{ $variant === 'info' ? 'selected' : '' }}>Info (blue)</option>
                                    <option value="success" {{ $variant === 'success' ? 'selected' : '' }}>Success (green)</option>
                                    <option value="warning" {{ $variant === 'warning' ? 'selected' : '' }}>Warning (yellow)</option>
                                    <option value="danger" {{ $variant === 'danger' ? 'selected' : '' }}>Danger (red)</option>
                                </select>
                            </div>
                        </div>

                        <div style="margin-top: 14px;">
                            <label class="field-label" for="announcement_message">Message</label>
                            <textarea id="announcement_message" name="announcement_message" maxlength="500"
                                placeholder="What should users know?">{{ $settings['site.announcement.message'] ?? '' }}</textarea>
                        </div>
                    </div>
                </section>

                {{-- ============ FEATURES TAB ============ --}}
                <section id="tab-features" class="tab-content" role="tabpanel">
                    <div class="card">
                        <h2>User-side Features</h2>
                        <p class="muted">Turn features on or off for the public. Changes apply immediately.</p>

                        <div class="toggle-row">
                            <div class="copy">
                                <strong>Allow new user registration</strong>
                                <small>When off, the Sign Up page is disabled and new accounts cannot be created.</small>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="registration_enabled" value="1"
                                    {{ !empty($settings['user.registration.enabled']) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="toggle-row">
                            <div class="copy">
                                <strong>Enable Google login</strong>
                                <small>Show the "Sign in with Google" button on the login page.</small>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="google_login_enabled" value="1"
                                    {{ !empty($settings['user.google_login.enabled']) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="toggle-row">
                            <div class="copy">
                                <strong>Show chatbot assistant</strong>
                                <small>Floating chatbot button on the user-facing pages.</small>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="chatbot_enabled" value="1"
                                    {{ !empty($settings['user.chatbot.enabled']) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="toggle-row">
                            <div class="copy">
                                <strong>Public access to Campaigns</strong>
                                <small>When off, the Campaigns page is hidden from users.</small>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="campaigns_public" value="1"
                                    {{ !empty($settings['user.campaigns.public']) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="toggle-row">
                            <div class="copy">
                                <strong>Public access to Events</strong>
                                <small>When off, the Events page is hidden from users.</small>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="events_public" value="1"
                                    {{ !empty($settings['user.events.public']) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="toggle-row">
                            <div class="copy">
                                <strong>Public access to In-kind Donations</strong>
                                <small>When off, the In-kind page is hidden from users.</small>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="inkind_public" value="1"
                                    {{ !empty($settings['user.inkind.public']) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </section>

                {{-- ============ MAINTENANCE TAB ============ --}}
                <section id="tab-maintenance" class="tab-content" role="tabpanel">
                    <div class="card">
                        <h2>Maintenance Mode</h2>
                        <p class="muted">Block user-side pages and show a maintenance notice. Admin pages remain accessible.</p>

                        <div class="toggle-row">
                            <div class="copy">
                                <strong>Enable maintenance mode</strong>
                                <small>Users will see the maintenance message instead of the site.</small>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="maintenance_enabled" value="1"
                                    {{ !empty($settings['site.maintenance.enabled']) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div style="margin-top: 14px;">
                            <label class="field-label" for="maintenance_message">Maintenance message</label>
                            <textarea id="maintenance_message" name="maintenance_message" maxlength="500"
                                placeholder="We'll be back shortly...">{{ $settings['site.maintenance.message'] ?? '' }}</textarea>
                        </div>
                    </div>
                </section>

                <div class="actions-bar" id="saveBar">
                    <button type="button" class="btn btn-secondary" onclick="location.reload()">Discard</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line"></i> Save changes
                    </button>
                </div>
            </form>

            {{-- ============ USERS TAB ============ --}}
            <section id="tab-users" class="tab-content" role="tabpanel">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="icon"><i class="ri-team-line"></i></div>
                        <div>
                            <div class="big" id="stat-total">{{ $userStats['total'] }}</div>
                            <div class="lbl">Total users</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="icon" style="background:#dcfce7;color:#166534;"><i class="ri-user-follow-line"></i></div>
                        <div>
                            <div class="big" id="stat-active">{{ $userStats['active'] }}</div>
                            <div class="lbl">Active</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="icon" style="background:#fee2e2;color:#991b1b;"><i class="ri-user-forbid-line"></i></div>
                        <div>
                            <div class="big" id="stat-suspended">{{ $userStats['suspended'] }}</div>
                            <div class="lbl">Suspended</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="icon" style="background:#fef3c7;color:#92400e;"><i class="ri-mail-close-line"></i></div>
                        <div>
                            <div class="big" id="stat-unverified">{{ $userStats['unverified'] }}</div>
                            <div class="lbl">Unverified email</div>
                        </div>
                    </div>
                </div>

                <div class="toolbar">
                    <input type="search" id="userSearch" placeholder="Search name, email, or phone...">
                    <button class="filter-btn active" data-status="all">All</button>
                    <button class="filter-btn" data-status="active">Active</button>
                    <button class="filter-btn" data-status="suspended">Suspended</button>
                    <button class="filter-btn" data-status="unverified">Unverified</button>
                </div>

                <div class="users-wrap" id="usersWrap">
                    {{-- Rendered via AJAX --}}
                    <div class="empty"><i class="ri-loader-4-line"></i><p>Loading users...</p></div>
                </div>
            </section>
        </div>
    </main>

    <div class="toast" id="toast"></div>

    <div class="confirm-modal" id="confirmModal">
        <div class="confirm-dialog">
            <h3 id="confirmTitle">Are you sure?</h3>
            <p id="confirmMessage">This action cannot be undone.</p>
            <div class="confirm-actions">
                <button type="button" class="btn btn-secondary" onclick="closeConfirm()">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmOkBtn">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const routes = {
            updateSettings:   "{{ route('admin.settings.update') }}",
            listUsers:        "{{ route('admin.settings.users') }}",
            suspendUser: (id) => `/administrator/settings/users/${id}/suspend`,
            activateUser:(id) => `/administrator/settings/users/${id}/activate`,
            deleteUser:  (id) => `/administrator/settings/users/${id}`,
        };

        // --- Sidebar toggle (mobile) ---
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const sidebarBtn = document.getElementById('sidebarToggle');
        if (sidebarBtn) {
            sidebarBtn.addEventListener('click', () => {
                sidebar?.classList.toggle('open');
                overlay?.classList.toggle('show');
            });
            overlay?.addEventListener('click', () => {
                sidebar?.classList.remove('open');
                overlay?.classList.remove('show');
            });
        }

        // --- Tabs ---
        const tabs = document.querySelectorAll('.tab-btn');
        const panels = document.querySelectorAll('.tab-content');
        const saveBar = document.getElementById('saveBar');
        tabs.forEach(t => t.addEventListener('click', () => {
            tabs.forEach(x => x.classList.remove('active'));
            panels.forEach(x => x.classList.remove('active'));
            t.classList.add('active');
            const tab = t.dataset.tab;
            document.getElementById('tab-' + tab)?.classList.add('active');
            // Hide save bar on Users tab (it manages users inline)
            if (saveBar) saveBar.style.display = tab === 'users' ? 'none' : '';
            if (tab === 'users') loadUsers();
        }));

        // --- Toast helper ---
        const toast = document.getElementById('toast');
        function showToast(msg, type = 'success') {
            toast.className = 'toast show ' + type;
            toast.textContent = msg;
            setTimeout(() => toast.classList.remove('show'), 2500);
        }

        // --- Confirm helper ---
        const confirmModal = document.getElementById('confirmModal');
        const confirmOkBtn = document.getElementById('confirmOkBtn');
        function openConfirm(title, message, onOk, okLabel = 'Confirm', okClass = 'btn-danger') {
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmMessage').textContent = message;
            confirmOkBtn.className = 'btn ' + okClass;
            confirmOkBtn.textContent = okLabel;
            confirmOkBtn.onclick = () => { closeConfirm(); onOk(); };
            confirmModal.classList.add('show');
        }
        function closeConfirm() { confirmModal.classList.remove('show'); }
        confirmModal.addEventListener('click', (e) => {
            if (e.target === confirmModal) closeConfirm();
        });

        // --- Save settings ---
        async function saveSettings(e) {
            e.preventDefault();
            const form = document.getElementById('settingsForm');
            const fd = new FormData(form);

            // FormData omits unchecked checkboxes. Normalise to boolean flags.
            const checkboxes = form.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(cb => { if (!fd.has(cb.name)) fd.append(cb.name, '0'); });

            try {
                const res = await fetch(routes.updateSettings, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: fd,
                });
                const data = await res.json();
                if (data.success) showToast(data.message || 'Saved.', 'success');
                else showToast(data.message || 'Could not save.', 'error');
            } catch (err) {
                showToast('Network error.', 'error');
            }
            return false;
        }

        // --- Users tab ---
        let currentStatus = 'all';
        let currentSearch = '';
        let searchTimer;

        async function loadUsers() {
            const params = new URLSearchParams({ status: currentStatus, search: currentSearch });
            const wrap = document.getElementById('usersWrap');
            wrap.innerHTML = '<div class="empty"><i class="ri-loader-4-line"></i><p>Loading users...</p></div>';
            try {
                const res = await fetch(routes.listUsers + '?' + params.toString(), {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();
                if (data.success) {
                    wrap.innerHTML = data.html;
                    document.getElementById('stat-total').textContent = data.stats.total;
                    document.getElementById('stat-active').textContent = data.stats.active;
                    document.getElementById('stat-suspended').textContent = data.stats.suspended;
                    document.getElementById('stat-unverified').textContent = data.stats.unverified;
                    bindUserActions();
                }
            } catch (err) {
                wrap.innerHTML = '<div class="empty"><i class="ri-error-warning-line"></i><p>Failed to load users.</p></div>';
            }
        }

        document.querySelectorAll('.toolbar .filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.toolbar .filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentStatus = btn.dataset.status;
                loadUsers();
            });
        });
        document.getElementById('userSearch').addEventListener('input', (e) => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                currentSearch = e.target.value;
                loadUsers();
            }, 250);
        });

        function bindUserActions() {
            document.querySelectorAll('[data-action]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.userId;
                    const name = btn.dataset.userName || 'this user';
                    const action = btn.dataset.action;

                    if (action === 'suspend') {
                        openConfirm(
                            'Suspend user?',
                            `This will prevent ${name} from signing in.`,
                            () => userAction(id, 'suspend'),
                            'Suspend',
                            'btn-warning'
                        );
                    } else if (action === 'activate') {
                        openConfirm(
                            'Activate user?',
                            `Restore sign-in access for ${name}.`,
                            () => userAction(id, 'activate'),
                            'Activate',
                            'btn-success'
                        );
                    } else if (action === 'delete') {
                        openConfirm(
                            'Delete user?',
                            `Permanently delete ${name}. This cannot be undone.`,
                            () => userAction(id, 'delete'),
                            'Delete',
                            'btn-danger'
                        );
                    }
                });
            });
        }

        async function userAction(id, action) {
            let url, method;
            if (action === 'suspend')      { url = routes.suspendUser(id);  method = 'POST'; }
            else if (action === 'activate'){ url = routes.activateUser(id); method = 'POST'; }
            else if (action === 'delete')  { url = routes.deleteUser(id);   method = 'DELETE'; }

            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Done.', 'success');
                    loadUsers();
                } else {
                    showToast(data.message || 'Action failed.', 'error');
                }
            } catch (err) {
                showToast('Network error.', 'error');
            }
        }
    </script>
</body>

</html>
