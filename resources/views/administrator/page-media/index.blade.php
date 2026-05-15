<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tulong Kabataan | Page Media</title>
    <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
    @vite('resources/js/app.js')

    <style>
        :root {
            --primary: #3b82f6;
            --bg: #f9fafb;
            --text: #111827;
            --muted: #6b7280;
            --border: #e5e7eb;
            --card: #ffffff;
            --shadow: 0 1px 2px rgba(0, 0, 0, .06), 0 1px 3px rgba(0, 0, 0, .1);
            --header-h: 64px;
            --sidebar-w: 256px;
        }

        * { box-sizing: border-box; }
        html, body { min-height: 100%; }
        body { margin: 0; background: var(--bg); color: var(--text); font-family: Inter, Arial, sans-serif; }

        .header {
            position: fixed; inset: 0 0 auto 0; height: var(--header-h); background: #fff;
            z-index: 50; box-shadow: var(--shadow);
        }
        .header__inner { height: 100%; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; }
        .header__left { display: flex; align-items: center; gap: 12px; }
        .menu-btn {
            display: inline-grid; place-items: center; width: 32px; height: 32px; border: 0;
            border-radius: 8px; background: #1f2937; color: #fff; cursor: pointer;
        }
        .brand { margin: 0; color: #000; font-size: 25px; font-weight: 1000; letter-spacing: .06em; }
        .logo-word img { width: 120px; height: 60px; margin-top: 8px; object-fit: contain; }
        .notif {
            width: 40px; height: 40px; border: 0; border-radius: 10px; background: #fff; color: #000;
            display: inline-grid; place-items: center; cursor: pointer;
        }
        .sidebar {
            position: fixed; left: 0; top: var(--header-h); bottom: 0; width: var(--sidebar-w);
            padding: 16px; transform: translateX(-100%); transition: transform .3s ease;
            z-index: 40; overflow: auto; background: #1e3a8a; color: #fff;
        }
        .sidebar.open { transform: translateX(0); }
        .side-link {
            display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px;
            color: #d1d5db; text-decoration: none; white-space: nowrap;
        }
        .side-link:hover, .side-link.active { background: var(--primary); color: #fff !important; }
        .overlay { position: fixed; inset: 0; z-index: 30; display: none; background: rgba(0,0,0,.5); }
        .overlay.show { display: block; }
        .main { min-height: 100vh; padding-top: var(--header-h); }

        @media (min-width: 1024px) {
            .main { margin-left: var(--sidebar-w); }
            .sidebar { transform: translateX(0); }
            .menu-btn { display: none; }
            .overlay { display: none !important; }
            .header__inner { padding: 0 24px; }
        }

        .container { width: min(100%, 1240px); margin: 0 auto; padding: 24px 16px 48px; }
        .page-header { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 22px; }
        .page-header h1 { margin: 0 0 6px; font-size: 28px; font-weight: 800; }
        .page-header p { margin: 0; color: var(--muted); }
        .summary-chip {
            display: inline-flex; min-height: 38px; align-items: center; gap: 8px; padding: 0 14px;
            border: 1px solid #bfdbfe; border-radius: 999px; background: #eff6ff; color: #1d4ed8; font-weight: 800;
        }

        .toolbar {
            display: grid; grid-template-columns: minmax(0, 1fr); gap: 12px;
            margin-bottom: 14px; padding: 14px; border: 1px solid var(--border); border-radius: 14px; background: #fff;
            box-shadow: var(--shadow);
        }
        .search-field { position: relative; }
        .search-field i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b; }
        .toolbar input {
            width: 100%; min-height: 42px; border: 1px solid #cfd9e8; border-radius: 10px; background: #fff;
            padding: 0 12px 0 38px; color: var(--text); font: inherit;
        }
        .toolbar input:focus {
            outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(59,130,246,0.12);
        }

        .media-tabs {
            display: flex; gap: 8px; overflow-x: auto; padding: 2px 2px 12px; margin-bottom: 10px;
            scrollbar-width: thin;
        }
        .media-tab {
            flex: 0 0 auto; display: inline-flex; min-height: 42px; align-items: center; gap: 8px;
            border: 1px solid #cfd9e8; border-radius: 10px; background: #fff; color: #475569;
            padding: 0 12px; font: inherit; font-weight: 800; cursor: pointer;
            transition: background .16s ease, border-color .16s ease, color .16s ease, transform .16s ease;
        }
        .media-tab:hover { transform: translateY(-1px); border-color: #93c5fd; color: #1d4ed8; }
        .media-tab.active {
            border-color: var(--primary); background: #eff6ff; color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.10);
        }
        .media-tab small {
            display: inline-grid; min-width: 24px; height: 24px; place-items: center; border-radius: 999px;
            background: #e2e8f0; color: #334155; font-size: 12px; font-weight: 800;
        }
        .media-tab.active small { background: #dbeafe; color: #1d4ed8; }

        .media-group { margin-top: 24px; }
        .media-group[hidden] { display: none !important; }
        .group-heading { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .group-heading i { color: var(--primary); font-size: 22px; }
        .group-heading h2 { margin: 0; font-size: 18px; font-weight: 800; }
        .group-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 310px), 1fr)); gap: 16px; }

        .media-card {
            display: flex; min-width: 0; flex-direction: column; overflow: hidden; border: 1px solid var(--border);
            border-radius: 14px; background: var(--card); box-shadow: var(--shadow);
        }
        .media-preview {
            position: relative; display: grid; min-height: 178px; aspect-ratio: 16 / 9; place-items: center;
            overflow: hidden; background: #f1f5f9;
        }
        .media-preview img { width: 100%; height: 100%; object-fit: cover; }
        .media-preview--logo img { object-fit: contain; padding: 18px; background: #fff; }
        .status-pill {
            position: absolute; left: 12px; top: 12px; display: inline-flex; align-items: center; gap: 6px;
            border-radius: 999px; padding: 5px 10px; background: rgba(15,23,42,0.78); color: #fff;
            font-size: 12px; font-weight: 800;
        }
        .status-pill.default { background: rgba(71,85,105,0.86); }
        .status-pill.pending { background: rgba(180,83,9,0.92); }
        .media-card__body { display: grid; flex: 1; gap: 12px; padding: 16px; }
        .media-title-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
        .media-title-row h3 { margin: 0; font-size: 16px; font-weight: 800; line-height: 1.25; }
        .media-title-row button {
            flex: 0 0 auto; width: 36px; height: 36px; border: 1px solid #cfd9e8; border-radius: 10px;
            background: #fff; color: #334155; cursor: pointer;
        }
        .media-meta { display: grid; gap: 6px; color: #64748b; font-size: 13px; line-height: 1.4; }
        .media-meta span { display: inline-flex; min-width: 0; align-items: center; gap: 6px; }
        .media-meta strong { color: #334155; font-weight: 800; }
        .media-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: auto; }
        .btn {
            min-height: 38px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            border: 1px solid transparent; border-radius: 10px; padding: 0 12px; font: inherit; font-weight: 800;
            cursor: pointer; text-decoration: none; transition: transform .16s ease, background .16s ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary { background: var(--primary); border-color: var(--primary); color: #fff; }
        .btn-secondary { background: #fff; border-color: #cfd9e8; color: #334155; }
        .btn-danger { background: #fee2e2; border-color: #fecaca; color: #b91c1c; }
        .btn-success { background: #10b981; border-color: #10b981; color: #fff; }
        .btn[hidden] { display: none; }
        .btn:disabled { opacity: .55; cursor: not-allowed; transform: none; }
        .media-file-input { display: none; }

        .empty-results {
            display: none; min-height: 180px; place-items: center; border: 1px dashed #bfdbfe; border-radius: 14px;
            background: #fff; color: #64748b; text-align: center;
        }
        .empty-results.show { display: grid; }
        .empty-results i { display: block; margin-bottom: 8px; color: #94a3b8; font-size: 38px; }

        .toast {
            position: fixed; right: 20px; bottom: 20px; z-index: 220; max-width: min(360px, calc(100vw - 40px));
            border-radius: 12px; background: #111827; color: #fff; padding: 12px 16px;
            box-shadow: 0 16px 42px rgba(0,0,0,.22); opacity: 0; transform: translateY(10px);
            pointer-events: none; transition: all .22s ease;
        }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.success { background: #059669; }
        .toast.error { background: #dc2626; }

        .modal {
            position: fixed; inset: 0; z-index: 210; display: none; align-items: center; justify-content: center;
            padding: 18px; background: rgba(4,15,38,.58); backdrop-filter: blur(6px);
        }
        .modal.show { display: flex; }
        .modal-dialog {
            width: min(100%, 520px); border: 1px solid var(--border); border-radius: 16px; background: #fff;
            box-shadow: 0 28px 70px rgba(15,23,42,.26); overflow: hidden;
        }
        .modal-dialog--image { width: min(100%, 860px); }
        .modal-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 18px; border-bottom: 1px solid var(--border); }
        .modal-header h3 { margin: 0; font-size: 17px; font-weight: 800; }
        .modal-close {
            width: 38px; height: 38px; border: 1px solid #cfd9e8; border-radius: 10px; background: #fff;
            color: #334155; cursor: pointer; font-size: 20px;
        }
        .modal-body { padding: 18px; color: #64748b; line-height: 1.55; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; padding: 0 18px 18px; }
        .image-modal-frame { aspect-ratio: 16 / 9; display: grid; place-items: center; overflow: hidden; border-radius: 12px; background: #0f172a; }
        .image-modal-frame img { max-width: 100%; max-height: 72vh; object-fit: contain; }

        @media (max-width: 720px) {
            .toolbar { grid-template-columns: 1fr; }
            .page-header { align-items: stretch; }
            .summary-chip { justify-content: center; }
        }
    </style>
    @include('administrator.partials.admin-theme')
</head>

<body class="admin-page admin-page-media-page">
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
                <img src="{{ asset('img/log.png') }}" alt="">
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
                <div>
                    <h1>Page Media</h1>
                    <p>Manage public page images, logos, backgrounds, email artwork, and default fallbacks.</p>
                </div>
                <span class="summary-chip">
                    <i class="ri-image-line"></i>
                    <span id="customCount">{{ $totalCustom }}</span> custom media
                </span>
            </div>

            <div class="toolbar">
                <label class="search-field">
                    <i class="ri-search-line"></i>
                    <input id="mediaSearch" type="search" placeholder="Search media by name, page, or section">
                </label>
            </div>

            <div class="media-tabs" id="mediaTabs" role="tablist" aria-label="Page media sections">
                @foreach ($pageFilters as $filter)
                    <button type="button"
                        class="media-tab {{ $loop->first ? 'active' : '' }}"
                        data-tab-page="{{ $filter }}"
                        role="tab"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        <i class="ri-folder-image-line"></i>
                        <span>{{ $filter }}</span>
                        <small>{{ count($groups[$filter] ?? []) }}</small>
                    </button>
                @endforeach
            </div>

            <div id="mediaGroups">
                @foreach ($groups as $groupName => $items)
                    <section class="media-group" data-group="{{ $groupName }}" {{ $loop->first ? '' : 'hidden' }}>
                        <div class="group-heading">
                            <i class="ri-folder-image-line"></i>
                            <h2>{{ $groupName }}</h2>
                        </div>
                        <div class="group-grid">
                            @foreach ($items as $item)
                                @php
                                    $isLogo = str_contains($item['key'], 'logo') || $item['key'] === 'site_favicon';
                                    $search = strtolower($item['label'] . ' ' . $item['page_name'] . ' ' . $item['section_name'] . ' ' . $item['key']);
                                @endphp
                                <article class="media-card" id="media-{{ $item['key'] }}"
                                    data-key="{{ $item['key'] }}"
                                    data-page="{{ $item['page_name'] }}"
                                    data-section="{{ $item['section_name'] }}"
                                    data-search="{{ $search }}">
                                    <div class="media-preview {{ $isLogo ? 'media-preview--logo' : '' }}">
                                        <img data-role="preview-image" src="{{ $item['url'] }}" alt="{{ $item['label'] }}">
                                        <span data-role="status-pill" class="status-pill {{ $item['has_custom_image'] ? '' : 'default' }}">
                                            <i class="{{ $item['has_custom_image'] ? 'ri-checkbox-circle-line' : 'ri-restart-line' }}"></i>
                                            {{ $item['has_custom_image'] ? 'Custom' : 'Default' }}
                                        </span>
                                    </div>
                                    <div class="media-card__body">
                                        <div class="media-title-row">
                                            <h3>{{ $item['label'] }}</h3>
                                            <button type="button" data-action="preview" aria-label="Preview {{ $item['label'] }}">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                        </div>
                                        <div class="media-meta">
                                            <span><i class="ri-layout-line"></i><strong>Used in:</strong> {{ $item['section_name'] }}</span>
                                            <span><i class="ri-crop-line"></i><strong>Recommended:</strong> {{ $item['recommended_size'] }}</span>
                                            <span><i class="ri-calendar-line"></i><strong>Updated:</strong> <span data-role="updated-at">{{ $item['updated_at_human'] }}</span></span>
                                            <span><i class="ri-file-info-line"></i><strong>Type:</strong> <span data-role="type-label">{{ $item['image_type'] ?: 'Default asset' }}</span></span>
                                        </div>
                                        <div class="media-actions">
                                            <input class="media-file-input" type="file" accept="image/jpeg,image/png,image/webp,image/svg+xml" data-role="file-input">
                                            <button type="button" class="btn btn-secondary" data-action="choose">
                                                <i class="ri-upload-cloud-2-line"></i>
                                                Change
                                            </button>
                                            <button type="button" class="btn btn-success" data-action="save" hidden>
                                                <i class="ri-save-line"></i>
                                                Save
                                            </button>
                                            <button type="button" class="btn btn-danger" data-action="reset" {{ $item['has_custom_image'] ? '' : 'disabled' }}>
                                                <i class="ri-refresh-line"></i>
                                                Reset
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            <div id="emptyResults" class="empty-results">
                <div>
                    <i class="ri-search-eye-line"></i>
                    <p>No media items match your search.</p>
                </div>
            </div>
        </div>
    </main>

    <div class="toast" id="toast"></div>

    <div class="modal" id="confirmModal" aria-hidden="true">
        <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
            <div class="modal-header">
                <h3 id="confirmTitle">Reset media?</h3>
                <button type="button" class="modal-close" data-close-modal aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage">This will restore the default image for this media item.</p>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" data-close-modal>Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmResetBtn">
                    <i class="ri-refresh-line"></i>
                    Reset
                </button>
            </div>
        </div>
    </div>

    <div class="modal" id="imageModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog--image" role="dialog" aria-modal="true" aria-labelledby="imageModalTitle">
            <div class="modal-header">
                <h3 id="imageModalTitle">Image Preview</h3>
                <button type="button" class="modal-close" data-close-modal aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="image-modal-frame">
                    <img id="modalImage" src="" alt="">
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const mediaBaseUrl = @json(url('/administrator/page-media'));
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
        const maxBytes = 5120 * 1024;
        const pendingFiles = new Map();
        let resetKey = null;

        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const sidebarBtn = document.getElementById('sidebarToggle');
        sidebarBtn?.addEventListener('click', () => {
            sidebar?.classList.toggle('open');
            overlay?.classList.toggle('show');
        });
        overlay?.addEventListener('click', () => {
            sidebar?.classList.remove('open');
            overlay?.classList.remove('show');
        });

        function endpoint(key) {
            return `${mediaBaseUrl}/${encodeURIComponent(key)}`;
        }

        function toast(message, type = 'success') {
            const el = document.getElementById('toast');
            el.className = `toast show ${type}`;
            el.textContent = message;
            setTimeout(() => el.classList.remove('show'), 2800);
        }

        function modal(id, open) {
            const el = document.getElementById(id);
            el?.classList.toggle('show', open);
            el?.setAttribute('aria-hidden', open ? 'false' : 'true');
        }

        document.querySelectorAll('[data-close-modal]').forEach((button) => {
            button.addEventListener('click', () => {
                modal('confirmModal', false);
                modal('imageModal', false);
            });
        });

        document.querySelectorAll('.modal').forEach((dialog) => {
            dialog.addEventListener('click', (event) => {
                if (event.target === dialog) {
                    dialog.classList.remove('show');
                    dialog.setAttribute('aria-hidden', 'true');
                }
            });
        });

        document.querySelectorAll('.media-card').forEach((card) => {
            const key = card.dataset.key;
            const input = card.querySelector('[data-role="file-input"]');

            card.querySelector('[data-action="choose"]')?.addEventListener('click', () => input?.click());
            card.querySelector('[data-action="save"]')?.addEventListener('click', () => saveMedia(card));
            card.querySelector('[data-action="reset"]')?.addEventListener('click', () => openReset(card));
            card.querySelector('[data-action="preview"]')?.addEventListener('click', () => openPreview(card));
            input?.addEventListener('change', () => chooseFile(card, input.files?.[0]));
        });

        function chooseFile(card, file) {
            if (!file) return;

            if (!allowedTypes.includes(file.type)) {
                toast('Only JPG, PNG, WEBP, and secure SVG files are allowed.', 'error');
                return;
            }

            if (file.size > maxBytes) {
                toast('Image is too large. Maximum size is 5 MB.', 'error');
                return;
            }

            const previewUrl = URL.createObjectURL(file);
            const image = new Image();
            image.onload = () => {
                if ((image.naturalWidth && image.naturalWidth < 16) || (image.naturalHeight && image.naturalHeight < 16)) {
                    URL.revokeObjectURL(previewUrl);
                    toast('Image dimensions are too small.', 'error');
                    return;
                }

                pendingFiles.set(card.dataset.key, { file, previewUrl });
                card.querySelector('[data-role="preview-image"]').src = previewUrl;
                card.querySelector('[data-action="save"]').hidden = false;
                updateStatusPill(card, 'Pending', 'pending', 'ri-time-line');
            };
            image.onerror = () => {
                URL.revokeObjectURL(previewUrl);
                toast('The selected image could not be previewed.', 'error');
            };
            image.src = previewUrl;
        }

        async function saveMedia(card) {
            const key = card.dataset.key;
            const pending = pendingFiles.get(key);
            if (!pending) return;

            const saveButton = card.querySelector('[data-action="save"]');
            const changeButton = card.querySelector('[data-action="choose"]');
            saveButton.disabled = true;
            changeButton.disabled = true;
            saveButton.innerHTML = '<i class="ri-loader-4-line"></i> Saving';

            const formData = new FormData();
            formData.append('image', pending.file);

            try {
                const response = await fetch(endpoint(key), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });
                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Unable to save image.');
                }

                updateCard(card, data.item);
                pendingFiles.delete(key);
                URL.revokeObjectURL(pending.previewUrl);
                toast(data.message || 'Media updated.', 'success');
                refreshCustomCount();
            } catch (error) {
                toast(error.message || 'Upload failed.', 'error');
            } finally {
                saveButton.disabled = false;
                changeButton.disabled = false;
                saveButton.hidden = true;
                saveButton.innerHTML = '<i class="ri-save-line"></i> Save';
            }
        }

        function openReset(card) {
            resetKey = card.dataset.key;
            document.getElementById('confirmMessage').textContent = `Reset "${card.querySelector('h3').textContent}" to its default image?`;
            modal('confirmModal', true);
        }

        document.getElementById('confirmResetBtn').addEventListener('click', async () => {
            if (!resetKey) return;
            const card = document.getElementById(`media-${resetKey}`);
            const button = document.getElementById('confirmResetBtn');
            button.disabled = true;

            try {
                const response = await fetch(endpoint(resetKey), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Unable to reset media.');
                }

                updateCard(card, data.item);
                pendingFiles.delete(resetKey);
                toast(data.message || 'Media reset.', 'success');
                refreshCustomCount();
                modal('confirmModal', false);
            } catch (error) {
                toast(error.message || 'Reset failed.', 'error');
            } finally {
                button.disabled = false;
                resetKey = null;
            }
        });

        function openPreview(card) {
            const img = card.querySelector('[data-role="preview-image"]');
            document.getElementById('modalImage').src = img.src;
            document.getElementById('modalImage').alt = img.alt;
            document.getElementById('imageModalTitle').textContent = card.querySelector('h3').textContent;
            modal('imageModal', true);
        }

        function updateCard(card, item) {
            if (!card || !item) return;

            card.querySelector('[data-role="preview-image"]').src = item.url;
            card.querySelector('[data-role="updated-at"]').textContent = item.updated_at_human;
            card.querySelector('[data-role="type-label"]').textContent = item.image_type || 'Default asset';
            card.querySelector('[data-action="reset"]').disabled = !item.has_custom_image;
            updateStatusPill(
                card,
                item.has_custom_image ? 'Custom' : 'Default',
                item.has_custom_image ? '' : 'default',
                item.has_custom_image ? 'ri-checkbox-circle-line' : 'ri-restart-line'
            );
        }

        function updateStatusPill(card, text, className, icon) {
            const pill = card.querySelector('[data-role="status-pill"]');
            pill.className = `status-pill ${className}`;
            pill.innerHTML = `<i class="${icon}"></i> ${text}`;
        }

        function refreshCustomCount() {
            const count = document.querySelectorAll('.media-card [data-action="reset"]:not(:disabled)').length;
            document.getElementById('customCount').textContent = count;
        }

        const searchInput = document.getElementById('mediaSearch');
        const tabButtons = document.querySelectorAll('.media-tab');
        let activePage = document.querySelector('.media-tab.active')?.dataset.tabPage || document.querySelector('.media-group')?.dataset.group || '';

        searchInput.addEventListener('input', filterCards);
        tabButtons.forEach((button) => {
            button.addEventListener('click', () => {
                activePage = button.dataset.tabPage;
                tabButtons.forEach((tab) => {
                    const isActive = tab === button;
                    tab.classList.toggle('active', isActive);
                    tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });
                filterCards();
            });
        });

        function filterCards() {
            const search = searchInput.value.trim().toLowerCase();
            let visibleCards = 0;

            document.querySelectorAll('.media-group').forEach((group) => {
                const isActiveGroup = group.dataset.group === activePage;
                group.hidden = !isActiveGroup;

                group.querySelectorAll('.media-card').forEach((card) => {
                    const matchesSearch = !search || card.dataset.search.includes(search);
                    card.hidden = !isActiveGroup || !matchesSearch;
                    if (isActiveGroup && matchesSearch) visibleCards++;
                });
            });

            document.getElementById('emptyResults').classList.toggle('show', visibleCards === 0);
        }

        filterCards();
    </script>
</body>

</html>
