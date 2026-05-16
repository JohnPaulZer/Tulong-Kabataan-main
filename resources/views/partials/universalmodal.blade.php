@php
    $tkModalHasErrors = session('error') || $errors->any();
    $tkModalTitle = $errors->any() ? 'Please fix these fields' : 'Something went wrong';
    $tkModalMessages = $errors->any() ? $errors->all() : array_filter([(string) session('error')]);
@endphp

<div id="toast"
    style="position:fixed; top:40px; left:50%; transform:translateX(-50%);
            background:#16a34a; color:#fff; padding:12px 20px; border-radius:8px;
            box-shadow:0 2px 8px rgba(0,0,0,0.2); font-size:14px; font-weight:500;
            opacity:0; pointer-events:none; transition:opacity 0.3s ease;
            z-index:20000;">
</div>

<style>
    body.tk-modal-open {
        overflow: hidden !important;
        touch-action: none;
    }
</style>

<div id="notificationModal"
    style="display:{{ $tkModalHasErrors ? 'flex' : 'none' }}; position:fixed; inset:0; background:rgba(15,23,42,0.55);
            justify-content:center; align-items:center; z-index:20010; padding:24px;
            overflow-y:auto;">
    <div
        style="background:#fff; width:min(420px,100%); max-height:calc(100vh - 48px); border-radius:10px; box-shadow:0 18px 45px rgba(15,23,42,0.25); overflow:auto;">
        <div style="display:flex; align-items:center; gap:10px; padding:18px 20px 10px;">
            <span id="notificationModalIcon"
                style="display:inline-flex; width:34px; height:34px; border-radius:999px; align-items:center; justify-content:center; background:#fee2e2; color:#dc2626; font-size:20px;">
                <i class="ri-error-warning-line"></i>
            </span>
            <h3 id="notificationModalTitle" style="margin:0; font-size:18px; font-weight:700; color:#111827;">
                {{ $tkModalTitle }}
            </h3>
        </div>
        <div id="notificationModalMessage"
            style="padding:0 20px 18px; color:#475569; font-size:14px; line-height:1.55;">
            @if ($tkModalHasErrors)
                <strong>Reason:</strong>
                @if (count($tkModalMessages) > 1)
                    <ul style="margin:8px 0 0 18px; padding:0;">
                        @foreach ($tkModalMessages as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @else
                    {{ $tkModalMessages[0] ?? 'Please try again.' }}
                @endif
            @else
                Please try again.
            @endif
        </div>
        <div style="display:flex; justify-content:flex-end; padding:14px 20px 18px; border-top:1px solid #e5e7eb;">
            <button id="notificationModalClose" type="button"
                style="padding:9px 16px; background:#2563eb; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:650;">
                OK
            </button>
        </div>
    </div>
</div>

<div id="confirmModal"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.5); justify-content:center; align-items:center;
            z-index:9999;">

    <div
        style="background:#fff; padding:20px; border-radius:8px; width:350px; text-align:center; box-shadow:0 2px 10px rgba(0,0,0,0.2);">

        <h3 id="confirmModalTitle" style="margin-bottom:10px; font-size:18px; font-weight:600; color:#1f2937;">
            Confirm Action
        </h3>
        <p id="confirmModalMessage" style="color:#374151; font-size:14px; margin-bottom:20px;">
            Are you sure you want to continue?
        </p>

        <div style="display:flex; justify-content:center; gap:12px;">
            <button id="cancelConfirmBtn"
                style="padding:8px 16px; background:#f3f4f6; border:none; border-radius:6px; cursor:pointer;">
                Cancel
            </button>
            <button id="confirmActionBtn"
                style="padding:8px 16px; background:#ef4444; color:#fff; border:none; border-radius:6px; cursor:pointer;">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        if (window.__tkNotificationModalBound) return;
        window.__tkNotificationModalBound = true;

        const modalSelector = [
            '#notificationModal',
            '#confirmModal',
            '.modal',
            '.modal-overlay',
            '.notifications-modal',
            '.logout-confirm-modal',
            '.campaign-details-modal',
            '.proof-modal',
            '.event-modal-backdrop',
            '.evt-details-modal',
            '[id$="Modal"]',
        ].join(',');

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderModalMessage(message, type) {
            const items = Array.isArray(message) ? message : [message || 'Please try again.'];
            const label = type === 'error' || type === 'warning' ? '<strong>Reason:</strong> ' : '';

            if (items.length > 1) {
                return label + '<ul style="margin:8px 0 0 18px; padding:0;">'
                    + items.map((item) => '<li>' + escapeHtml(item) + '</li>').join('')
                    + '</ul>';
            }

            return label + escapeHtml(items[0]).replace(/\n/g, '<br>');
        }

        function isVisibleModal(el) {
            if (!el || el === document.body || el === document.documentElement) return false;

            const style = window.getComputedStyle(el);
            if (style.display === 'none' || style.visibility === 'hidden' || style.opacity === '0') {
                return false;
            }

            return el.offsetWidth > 0 || el.offsetHeight > 0 || style.position === 'fixed';
        }

        function syncModalScrollLock() {
            const hasOpenModal = Array.from(document.querySelectorAll(modalSelector)).some(isVisibleModal);
            document.body.classList.toggle('tk-modal-open', hasOpenModal);
        }

        window.TKSyncModalScrollLock = syncModalScrollLock;

        function keepOverlaysAtBodyRoot() {
            ['toast', 'notificationModal', 'confirmModal'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el && el.parentElement !== document.body) {
                    document.body.appendChild(el);
                }
            });
        }

        function setModalType(type) {
            const icon = document.getElementById('notificationModalIcon');
            if (!icon) return;

            const styles = {
                success: ['#dcfce7', '#16a34a', 'ri-checkbox-circle-line'],
                warning: ['#fef3c7', '#d97706', 'ri-alert-line'],
                error: ['#fee2e2', '#dc2626', 'ri-error-warning-line'],
                info: ['#dbeafe', '#2563eb', 'ri-information-line'],
            };
            const [bg, color, iconClass] = styles[type] || styles.info;
            icon.style.background = bg;
            icon.style.color = color;
            icon.innerHTML = '<i class="' + iconClass + '"></i>';
        }

        window.showNotificationModal = function (message, type = 'error', title = null) {
            keepOverlaysAtBodyRoot();

            const modal = document.getElementById('notificationModal');
            const titleEl = document.getElementById('notificationModalTitle');
            const messageEl = document.getElementById('notificationModalMessage');

            if (!modal || !titleEl || !messageEl) {
                return;
            }

            const titles = {
                success: 'Success',
                warning: 'Please check this',
                error: 'Something went wrong',
                info: 'Notice',
            };

            setModalType(type);
            titleEl.textContent = title || titles[type] || titles.info;
            messageEl.innerHTML = renderModalMessage(message, type);
            modal.style.display = 'flex';
            syncModalScrollLock();
        };

        function closeModal() {
            const modal = document.getElementById('notificationModal');
            if (modal) modal.style.display = 'none';
            syncModalScrollLock();
        }

        document.addEventListener('DOMContentLoaded', function () {
            keepOverlaysAtBodyRoot();
            syncModalScrollLock();

            const modal = document.getElementById('notificationModal');
            document.getElementById('notificationModalClose')?.addEventListener('click', closeModal);
            modal?.addEventListener('click', function (event) {
                if (event.target === modal) closeModal();
            });
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') closeModal();
            });

            @if (session('error'))
                window.showNotificationModal(@json(session('error')), 'error');
            @endif

            @if ($errors->any())
                window.showNotificationModal(@json($errors->all()), 'error', 'Please fix these fields');
            @endif

            const observer = new MutationObserver(syncModalScrollLock);
            observer.observe(document.body, {
                attributes: true,
                attributeFilter: ['class', 'style'],
                childList: true,
                subtree: true,
            });
        });
    })();
</script>
