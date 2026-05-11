const MODAL_ID = 'tkLoadingModal';
const AUTH_ENTRY_PATHS = new Set(['/login', '/register', '/auth/google']);

let modalElement = null;

function normalizePath(pathname) {
    const cleanPath = pathname.replace(/\/+$/, '');
    return cleanPath || '/';
}

function isSkeletonLoaderVisible() {
    const loadingScreen = document.getElementById('loadingScreen');

    if (!loadingScreen || loadingScreen.classList.contains('fade-out')) {
        return false;
    }

    return window.getComputedStyle(loadingScreen).display !== 'none';
}

function buildModal() {
    const modal = document.createElement('div');
    modal.id = MODAL_ID;
    modal.className = 'tk-loading-modal';
    modal.setAttribute('aria-hidden', 'true');
    modal.innerHTML = `
        <div class="tk-loading-modal__backdrop" aria-hidden="true"></div>
        <section class="tk-loading-modal__dialog" role="dialog" aria-modal="true" aria-live="polite" aria-label="Loading">
            <img src="/img/log.png" alt="Tulong Kabataan" class="tk-loading-modal__logo">
            <div class="tk-loading-modal__progress" aria-hidden="true"></div>
        </section>
    `;

    document.body.appendChild(modal);
    return modal;
}

function ensureModal() {
    modalElement = modalElement || document.getElementById(MODAL_ID) || buildModal();
    return modalElement;
}

function showLoadingModal() {
    if (!document.body || isSkeletonLoaderVisible()) {
        return;
    }

    const modal = ensureModal();
    document.body.classList.add('tk-loading-modal-open');
    modal.classList.add('is-active');
    modal.setAttribute('aria-hidden', 'false');
}

function hideLoadingModal() {
    const modal = document.getElementById(MODAL_ID);

    if (!modal) {
        return;
    }

    modal.classList.remove('is-active');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('tk-loading-modal-open');
}

function isPlainNavigationClick(event) {
    return event.button === 0 && !event.metaKey && !event.ctrlKey && !event.shiftKey && !event.altKey;
}

function shouldShowForLink(link, event) {
    if (!link || !isPlainNavigationClick(event) || link.hasAttribute('download')) {
        return false;
    }

    const target = link.getAttribute('target');

    if (target && target.toLowerCase() !== '_self') {
        return false;
    }

    try {
        const url = new URL(link.href, window.location.href);

        if (url.origin !== window.location.origin) {
            return false;
        }

        return AUTH_ENTRY_PATHS.has(normalizePath(url.pathname));
    } catch (error) {
        return false;
    }
}

function shouldShowForForm(form, submitter = null) {
    if (!form || form.dataset.loadingModal === 'false' || form.hasAttribute('data-no-loading-modal')) {
        return false;
    }

    const method = (submitter?.getAttribute('formmethod') || form.getAttribute('method') || 'get').toLowerCase();

    if (method === 'dialog') {
        return false;
    }

    const target = submitter?.getAttribute('formtarget') || form.getAttribute('target');

    return !target || target.toLowerCase() === '_self';
}

function bindLoadingModalTriggers() {
    document.addEventListener('click', (event) => {
        const link = event.target.closest?.('a[href]');

        if (!shouldShowForLink(link, event)) {
            return;
        }

        window.setTimeout(() => {
            if (!event.defaultPrevented) {
                showLoadingModal();
            }
        }, 0);
    }, true);

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!shouldShowForForm(form, event.submitter)) {
            return;
        }

        window.setTimeout(() => {
            if (!event.defaultPrevented) {
                showLoadingModal();
            }
        }, 0);
    }, true);

    window.addEventListener('pageshow', hideLoadingModal);
}

window.TKLoadingModal = {
    show: showLoadingModal,
    hide: hideLoadingModal,
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindLoadingModalTriggers, { once: true });
} else {
    bindLoadingModalTriggers();
}
