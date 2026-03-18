<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tulong Kabataan | Administrator Dashboard</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    <!-- Remixicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <!-- Google Fonts: Playfair Display & Open Sans -->
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Playfair+Display:wght@700;800&display=swap"
        rel="stylesheet">
    <!-- Charts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>

    <style>
        :root {
            --primary: #3b82f6;
            --danger: #ef4444;
            --edit: #d97706;
            --bg: #f9fafb;
            --text: #111827;
            --muted: #6b7280;
            --muted-700: #374151;
            --border: #e5e7eb;
            --card: #ffffff;
            --shadow: 0 1px 3px rgba(0, 0, 0, .08), 0 4px 6px rgba(0, 0, 0, .05);
            --radius: 12px;
            --header-h: 64px;
            --sidebar-w: 256px;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-h);
            background: #fff;
            z-index: 50;
            box-shadow: var(--shadow);
        }

        .header__inner {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
        }

        .header__left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .menu-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: 0;
            background: #1f2937;
            color: #fff;
            border-radius: 8px;
            cursor: pointer;
        }

        .menu-btn:hover {
            background: #374151;
        }

        .brand {
            font-weight: 800;
            font-size: 22px;
            color: black;
        }

        .logo-word {
            font-family: "Pacifico", cursive;
            color: var(--primary);
            font-size: 22px;
        }

        .notif {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: white;
            color: #000;
            border: 0;
            cursor: pointer;
        }

        

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: var(--header-h);
            bottom: 0;
            width: var(--sidebar-w);
            background: #1e3a8a;
            color: #fff;
            transform: translateX(-100%);
            transition: transform .3s ease;
            z-index: 40;
            padding: 16px;
            overflow: auto;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .sidebar nav {
            display: grid;
            gap: 8px;
        }

        .side-btn,
        .side-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 8px;
            background: transparent;
            border: 0;
            color: #d1d5db;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
            transition: background .2s;
        }

        .side-btn.primary {
            background: var(--primary);
            color: #fff;
        }

        .side-btn:hover,
        .side-link:hover {
            background: #1e40af;
        }

        /* Sidebar overlay (mobile) */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 30;
            display: none;
        }

        .overlay.show {
            display: block;
        }

        @media (min-width:1024px) {
            .main {
                margin-left: var(--sidebar-w);
            }

            .menu-btn {
                display: none;
            }

            .sidebar {
                transform: translateX(0);
            }

            .overlay {
                display: none !important;
            }

            .header__inner {
                padding: 0 24px;
            }
        }

        /* Main */
        .main {
            margin-top: 64px;
            padding: 20px;
            transition: margin-left .28s ease;
            margin-left: 260px;
            min-height: calc(100vh - 64px);
        }

        .main.fullwidth {
            margin-left: 0;
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-110%);
            }

            .sidebar.visible {
                transform: translateX(0);
            }

            .overlay.visible {
                display: block;
            }

            .main {
                margin-left: 0;
            }
        }

        .main {
            margin-left: 256px;
            padding: 24px;
            flex: 1;
        }

        /* Nav */
        .nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            text-decoration: none;
            color: #374151;
        }

        .nav-link:hover {
            background: #f3f4f6;
            color: #111827;
        }

        .nav-link.active {
            background: var(--primary);
            color: #fff;
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Page header */
        .page-header {
            margin-bottom: 20px;
        }

        .page-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            color: #111827;
        }

        .page-header .muted {
            margin: 6px 0 0;
            color: var(--muted);
        }

        /* Cards */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
            box-shadow: var(--shadow);
            transition: all 0.25s ease;
        }

        /* ✅ No hover effect on card */
        .cards-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media(min-width:900px) {
            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(min-width:1200px) {
            .cards-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Search panel */
        /* Search panel */
        .search-panel {
            margin-bottom: 20px;
            /* ✅ adds spacing below search row */
        }

        .search-panel .search-row {
            display: flex;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-field {
            position: relative;
            flex: 1;
            min-width: 220px;
        }

        .search-field input {
            width: 100%;
            padding: 10px 12px 10px 38px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            font-size: 14px;
        }

        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 18px;
        }

        .actions {
            display: flex;
            gap: 8px;
        }


        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 36px;
            padding: 0 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            background: transparent;
            color: #374151;
            transition: all .2s ease;
        }

        .btn .icon-left {
            font-size: 15px;
        }

        .btn.primary {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .btn.primary:hover {
            background: #2563eb;
            border-color: #2563eb;
            box-shadow: 0 2px 6px rgba(37, 99, 235, .3);
        }

        .btn.outline {
            background: #fff;
            border: 1px solid #d1d5db;
            color: #374151;
        }

        .btn.outline:hover {
            background: #f3f4f6;
            color: var(--primary);
            border-color: var(--primary);
        }

        .btn.icon-only {
            width: 36px;
            padding: 0;
            font-size: 16px;
        }

        /* Edit */
        .btn.edit {
            background: #fff;
            border: 1px solid var(--edit);
            color: var(--edit);
        }

        .btn.edit:hover {
            background: var(--edit);
            color: #fff;
            box-shadow: 0 2px 6px rgba(217, 119, 6, .3);
        }

        /* Delete */
        .btn.danger {
            background: #fff;
            border: 1px solid var(--danger);
            color: var(--danger);
        }

        .btn.danger:hover {
            background: var(--danger);
            color: #fff;
            box-shadow: 0 2px 6px rgba(239, 68, 68, .3);
        }

        /* Record Card */
        .record {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .record-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 6px;
        }

        .record-left {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .icon-box {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-box.red {
            background: #fee2e2;
            color: #b91c1c;
        }

        .icon-box.orange {
            background: #fff7ed;
            color: #ea580c;
        }

        .icon-box.blue {
            background: #eff6ff;
            color: #1e3a8a;
        }

        .icon-box.yellow {
            background: #fffbeb;
            color: #b45309;
        }

        .icon-box.green {
            background: #ecfdf5;
            color: #065f46;
        }

        .icon-box.purple {
            background: #f3e8ff;
            color: #6d28d9;
        }

        .pill {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .red-pill {
            background: #fee2e2;
            color: #991b1b;
        }

        .orange-pill {
            background: #fff7ed;
            color: #b45309;
        }

        .blue-pill {
            background: #eff6ff;
            color: #1e3a8a;
        }

        .yellow-pill {
            background: #fffbeb;
            color: #92400e;
        }

        .green-pill {
            background: #ecfdf5;
            color: #065f46;
        }

        .purple-pill {
            background: #f3e8ff;
            color: #6d28d9;
        }

        .record-body .row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            padding: 2px 0;
        }

        .record-body .label {
            color: var(--muted);
            font-size: 13px;
        }

        .value {
            font-weight: 600;
        }

        .red-text {
            color: #b91c1c;
        }

        .orange-text {
            color: #ea580c;
        }

        .blue-text {
            color: #1e3a8a;
        }

        .green-text {
            color: #0f766e;
        }

        .purple-text {
            color: #6d28d9;
        }

        .yellow-text {
            color: #b45309;
        }

        /* Record Card Actions */
        .record-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: flex-end;
            margin-top: 12px;
        }

        /* Pagination */
        .pagination-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .pagination {
            display: flex;
            gap: 8px;
        }

        .muted {
            color: var(--muted);
        }

        .small {
            font-size: 13px;
            color: var(--muted);
        }

        /* Responsive */
        @media(max-width:880px) {
            .main {
                margin-left: 0;
                padding: 16px;
            }

            .layout {
                padding-top: 0;
            }

            .header {
                position: static;
            }
        }

        .side-link.active,
        .side-btn.active {
            background: var(--primary);
            color: #fff !important;
        }


        /* Overlay (blur background) */
        .toast-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(6px);
            display: none;
            z-index: 999;
        }

        /* Modal box */
        .toast-confirm {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            width: 340px;
            max-width: 90%;
            text-align: center;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .2);
            z-index: 1000;
            display: none;
            /* hidden by default */
        }

        /* Title & message */
        .toast-confirm h3 {
            margin: 0 0 8px;
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }

        .toast-confirm p {
            margin: 0 0 20px;
            font-size: 14px;
            color: #6b7280;
        }

        /* Buttons row */
        .toast-confirm .actions {
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .toast-confirm button {
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }

        /* Cancel button */
        .toast-confirm .cancel {
            background: #f3f4f6;
            color: #111827;
        }

        .toast-confirm .cancel:hover {
            background: #e5e7eb;
        }

        /* Delete button */
        .toast-confirm .delete {
            background: #ef4444;
            color: #fff;
        }

        .toast-confirm .delete:hover {
            background: #dc2626;
        }

        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 3px solid rgba(59, 130, 246, 0.3);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

</head>

<body>

    @include('administrator.partials.loading-screen')
    <!-- Header -->
    <header class="header">
        <div class="header__inner">
            <div class="header__left">
                <button id="sidebarToggle" class="menu-btn" aria-label="Toggle menu">
                    <i class="ri-menu-line"></i>
                </button>
                <h1 class="brand">ADMINISTRATOR</h1>
            </div>

            <div class="logo-word"><img src="{{ asset('img/log.png') }}" alt=""
                    style="width: 120px; height: 60px; margin-top: 8px;">
            </div>

            <button class="notif" aria-label="Notifications">
               
            </button>
        </div>
    </header>

    <!-- Sidebar -->
    @include('administrator.partials.main-sidebar')

    <!-- Overlay (mobile) -->
    <div id="sidebarOverlay" class="overlay" aria-hidden="true"></div>

    <main class="main">
        <div class="container">
            <!-- Page Header -->
            <section class="page-header">
                <h1>DNC Records</h1>
                <p class="muted">Damage, Needs, and Capabilities assessment records</p>
            </section>

            <!-- Filters and Search -->
            <section class="search-panel card">
                <div class="search-row">
                    <div class="search-field">
                        <i class="ri-search-line search-icon"></i>
                        <input id="recordSearch" type="text" placeholder="Search records..." />
                    </div>

                    <div class="actions">



                        <form action="{{ route('dnc.add') }}" method="GET">
                            <button class="btn primary">
                                <i class="ri-add-line icon-left"></i>
                                Add Record
                            </button>
                        </form>
                    </div>
                </div>
            </section>


            <!-- Records Grid -->
            <section id="recordsGrid" class="grid cards-grid">
                @foreach ($records as $record)
                    <article class="record card">
                        <div class="record-head">
                            <div class="record-left">
                                <div
                                    class="icon-box
                    @if ($record->priority == 'High') red
                    @elseif($record->priority == 'Medium') orange
                    @else green @endif">
                                    <i class="ri-alert-line ri-lg"></i>
                                </div>
                                <div>
                                    <h3>{{ $record->event }}</h3>
                                    <p class="muted small">
                                        {{ $record->province }}, {{ $record->municipality }}, {{ $record->barangay }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <span
                                    class="pill
                    @if ($record->priority == 'High') red-pill
                    @elseif($record->priority == 'Medium') orange-pill
                    @else green-pill @endif">
                                    {{ $record->priority }}
                                </span>
                            </div>
                        </div>

                        <div class="record-body">
                            <div class="row">
                                <span class="label">Damage Level:</span>
                                <span class="value {{ $record->houses_full > 50 ? 'red-text' : 'orange-text' }}">
                                    {{ $record->houses_full + $record->houses_partial }} houses damaged
                                </span>
                            </div>
                            <div class="row">
                                <span class="label">Affected Population:</span>
                                <span class="value">
                                    {{ $record->individuals ?? $record->households }} people
                                </span>
                            </div>
                            <div class="row">
                                <span class="label">Primary Need:</span>
                                <span class="value">{{ $record->top_need_1 }}</span>
                            </div>
                        </div>

                        <div class="record-actions">
                            <!-- View Details Button -->
                            <a href="{{ route('dnc.show', $record->dnc_id) }}" class="btn primary block"
                                style="text-decoration: none">
                                <i class="ri-eye-line"></i> View Details
                            </a>

                            <!-- Edit Button -->
                            <a href="{{ route('dnc.edit', $record->dnc_id) }}" class="btn outline icon-only"
                                aria-label="Edit">
                                <i class="ri-edit-line"></i>
                            </a>

                            <!-- Delete Button -->
                            <form id="delete-form-{{ $record->dnc_id }}"
                                action="{{ route('dnc.destroy', $record->dnc_id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn danger icon-only delete-btn"
                                    data-id="{{ $record->dnc_id }}" aria-label="Delete">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </section>

            <!-- Overlay -->
            <div id="toast-overlay" class="toast-overlay"></div>
            <!-- Confirmation Modal -->
            <div id="toast-confirm" class="toast-confirm">
                <h3>Confirm Deletion</h3>
                <p>Are you sure you want to delete this record?</p>
                <div class="actions">
                    <button class="cancel">Cancel</button>
                    <button class="delete">Delete</button>
                </div>
            </div>



            <!-- Pagination -->
            <section class="pagination-row">
                <div class="muted">
                    Showing <strong>{{ $records->firstItem() }}</strong>
                    to <strong>{{ $records->lastItem() }}</strong>
                    of <strong>{{ $records->total() }}</strong> records
                </div>
                <div class="pagination">
                    <button class="btn outline">Previous</button>
                    <button class="btn primary">1</button>
                    <button class="btn outline">2</button>
                    <button class="btn outline">3</button>
                    <button class="btn outline">Next</button>
                </div>
            </section>
        </div>
    </main>

    <script>
        // Simplified AJAX pagination without controller changes
        document.addEventListener('DOMContentLoaded', function() {
            let isLoading = false;

            async function loadPage(page) {
                if (isLoading) return;

                isLoading = true;
                const grid = document.getElementById('recordsGrid');
                const originalContent = grid.innerHTML;

                try {
                    // Show loading
                    grid.innerHTML = `
                        <div style="text-align: center; padding: 60px; grid-column: 1 / -1;">
                            <div class="loading-spinner" style="
                                display: inline-block;
                                width: 40px;
                                height: 40px;
                                border: 3px solid rgba(59, 130, 246, 0.2);
                                border-radius: 50%;
                                border-top-color: #3b82f6;
                                animation: spin 0.8s linear infinite;
                                margin-bottom: 12px;
                            "></div>
                            <div style="font-size: 14px; color: #666; font-weight: 500;">Loading records...</div>
                        </div>
                    `;


                    const url = new URL(window.location.href);
                    url.searchParams.set('page', page);

                    const response = await fetch(url.toString());
                    const text = await response.text();

                    // Parse the full page response
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(text, 'text/html');

                    // Extract the records grid
                    const newGrid = doc.getElementById('recordsGrid');
                    const newPaginationInfo = doc.querySelector('.pagination-row .muted');

                    if (newGrid) {
                        grid.innerHTML = newGrid.innerHTML;

                        // Update pagination info
                        if (newPaginationInfo) {
                            document.querySelector('.pagination-row .muted').innerHTML = newPaginationInfo
                                .innerHTML;
                        }

                        // Re-attach all event listeners
                        reattachEventListeners();

                        // Update URL
                        history.pushState({
                            page: page
                        }, '', url.toString());

                        // Update pagination UI
                        updatePaginationUI(page);
                    }

                } catch (error) {
                    console.error('Error loading page:', error);
                    grid.innerHTML = originalContent;
                } finally {
                    isLoading = false;
                }
            }

            function updatePaginationUI(currentPage) {
                const lastPage = {{ $records->lastPage() }};
                const pageBtns = document.querySelectorAll('.pagination .btn');

                pageBtns.forEach(btn => {
                    btn.classList.remove('primary');
                    btn.classList.add('outline');

                    const btnText = btn.textContent.trim();
                    const pageNum = parseInt(btnText);

                    // Highlight current page
                    if (pageNum === currentPage) {
                        btn.classList.remove('outline');
                        btn.classList.add('primary');
                    }

                    // Disable Previous/Next if needed
                    if (btnText === 'Previous') {
                        btn.disabled = currentPage === 1;
                        btn.style.opacity = currentPage === 1 ? '0.5' : '1';
                    } else if (btnText === 'Next') {
                        btn.disabled = currentPage === lastPage;
                        btn.style.opacity = currentPage === lastPage ? '0.5' : '1';
                    }

                    // Hide non-existent pages
                    if (!isNaN(pageNum) && pageNum > lastPage) {
                        btn.style.display = 'none';
                    } else {
                        btn.style.display = 'inline-flex';
                    }
                });
            }

            function reattachEventListeners() {
                // Re-attach delete handlers
                document.querySelectorAll('.delete-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const recordId = this.dataset.id;
                        const toast = document.getElementById('toast-confirm');
                        const overlay = document.getElementById('toast-overlay');

                        overlay.style.display = 'block';
                        toast.style.display = 'block';

                        toast.querySelector('.cancel').onclick = function() {
                            toast.style.display = 'none';
                            overlay.style.display = 'none';
                        };

                        toast.querySelector('.delete').onclick = function() {
                            document.getElementById('delete-form-' + recordId).submit();
                        };

                        overlay.onclick = function() {
                            toast.style.display = 'none';
                            overlay.style.display = 'none';
                        };
                    });
                });

                // Re-attach pagination handlers
                attachPaginationHandlers();
            }

            function attachPaginationHandlers() {
                const paginationDiv = document.querySelector('.pagination');
                if (!paginationDiv) return;

                paginationDiv.addEventListener('click', function(e) {
                    if (e.target.classList.contains('btn')) {
                        e.preventDefault();
                        const btn = e.target;
                        const btnText = btn.textContent.trim();
                        const currentPage = parseInt(document.querySelector('.pagination .btn.primary')
                            ?.textContent || 1);
                        const lastPage = {{ $records->lastPage() }};

                        let targetPage = currentPage;

                        if (btnText === 'Previous' && currentPage > 1) {
                            targetPage = currentPage - 1;
                        } else if (btnText === 'Next' && currentPage < lastPage) {
                            targetPage = currentPage + 1;
                        } else if (!isNaN(parseInt(btnText))) {
                            targetPage = parseInt(btnText);
                        }

                        if (targetPage !== currentPage && targetPage >= 1 && targetPage <= lastPage) {
                            loadPage(targetPage);
                        }
                    }
                });
            }

            // Handle browser back/forward
            window.addEventListener('popstate', function(event) {
                const urlParams = new URLSearchParams(window.location.search);
                const page = parseInt(urlParams.get('page') || 1);
                loadPage(page);
            });

            // Initial setup
            attachPaginationHandlers();
            updatePaginationUI({{ $records->currentPage() }});
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Search/filter functionality
            (function searchRecords() {
                const input = document.getElementById('recordSearch');
                const cardsContainer = document.getElementById('recordsGrid');
                if (!input || !cardsContainer) return;
                const cards = Array.from(cardsContainer.querySelectorAll('.record'));

                input.addEventListener('input', function() {
                    const q = this.value.trim().toLowerCase();
                    cards.forEach(card => {
                        const title = (card.querySelector('h3')?.textContent || '')
                            .toLowerCase();
                        const location = (card.querySelector('p')?.textContent || '')
                            .toLowerCase();
                        if (!q || title.includes(q) || location.includes(q)) {
                            card.style.display = '';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            })();

            // Notification bell animation (brief pulse)
            (function notificationBell() {
                const bell = document.getElementById('notificationBell');
                if (!bell) return;
                bell.addEventListener('click', function() {
                    bell.classList.add('pulse');
                    setTimeout(() => bell.classList.remove('pulse'), 900);
                });
            })();

            // Small accessibility: allow Enter key on search to focus first visible card (example)
            (function enterKeyExample() {
                const input = document.getElementById('recordSearch');
                const cardsContainer = document.getElementById('recordsGrid');
                if (!input || !cardsContainer) return;
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        const firstVisible = cardsContainer.querySelector(
                            '.record:not([style*="display: none"])');
                        if (firstVisible) firstVisible.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                });
            })();

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarLinks = document.querySelectorAll('.side-link');

            const currentPage = window.location.pathname.split('/').pop();

            sidebarLinks.forEach(link => {

                link.classList.remove('active');

                if (link.getAttribute('href') && link.getAttribute('href').includes(currentPage)) {
                    link.classList.add('active');
                }

                link.addEventListener('click', function() {
                    sidebarLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                });
            });
        });
    </script>



    {{-- TOAST SCRIPT --}}
    <script>
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const recordId = this.dataset.id;
                const toast = document.getElementById('toast-confirm');
                const overlay = document.getElementById('toast-overlay');


                overlay.style.display = 'block';
                toast.style.display = 'block';


                toast.querySelector('.cancel').onclick = function() {
                    toast.style.display = 'none';
                    overlay.style.display = 'none';
                };


                toast.querySelector('.delete').onclick = function() {
                    document.getElementById('delete-form-' + recordId).submit();
                    toast.style.display = 'none';
                    overlay.style.display = 'none';
                };


                overlay.onclick = function() {
                    toast.style.display = 'none';
                    overlay.style.display = 'none';
                };
            });
        });
    </script>


</body>

</html>
