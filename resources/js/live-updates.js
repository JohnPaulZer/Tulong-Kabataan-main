const pageMedia = new Map();
const pageMediaPollIntervalMs = 8000;
let pageMediaPollTimer = null;

function absoluteUrl(value) {
    if (!value) {
        return '';
    }

    try {
        return new URL(value, window.location.origin).href;
    } catch {
        return value;
    }
}

function urlCandidates(value) {
    const normalized = absoluteUrl(value);
    const candidates = new Set([value, normalized]);

    try {
        const url = new URL(normalized);
        candidates.add(`${url.pathname}${url.search}${url.hash}`);
    } catch {
        // Keep the original value when it is not a valid URL.
    }

    return Array.from(candidates).filter(Boolean);
}

function replaceAll(value, oldCandidates, newUrl) {
    if (!value || !newUrl) {
        return value;
    }

    return oldCandidates.reduce((current, candidate) => {
        return current.split(candidate).join(newUrl);
    }, value);
}

function updateAttribute(element, attribute, oldCandidates, newUrl) {
    const current = element.getAttribute(attribute);
    const next = replaceAll(current, oldCandidates, newUrl);

    if (next && next !== current) {
        element.setAttribute(attribute, next);
        return 1;
    }

    return 0;
}

function cacheBustUrl(value, version) {
    if (!value || !version) {
        return value;
    }

    try {
        const url = new URL(value, window.location.origin);
        url.searchParams.set('v', String(version).replace(/[^a-zA-Z0-9._:-]/g, ''));
        return url.href;
    } catch {
        const separator = value.includes('?') ? '&' : '?';
        return `${value}${separator}v=${encodeURIComponent(String(version))}`;
    }
}

function updateKeyedElements(key, newUrl) {
    if (!key || !newUrl) {
        return 0;
    }

    let updated = 0;

    document.querySelectorAll('[data-page-media-key]').forEach((element) => {
        if (element.dataset.pageMediaKey !== key) {
            return;
        }

        if (element.matches('img, iframe, video, audio, source')) {
            const current = element.getAttribute('src');
            if (current !== newUrl) {
                element.setAttribute('src', newUrl);
                updated += 1;
            }
        }

        if (element.matches('link')) {
            const current = element.getAttribute('href');
            if (current !== newUrl) {
                element.setAttribute('href', newUrl);
                updated += 1;
            }
        }

        const cssVariable = element.dataset.pageMediaCssVariable;
        if (cssVariable) {
            element.style.setProperty(cssVariable, `url("${newUrl}")`);
            updated += 1;
        }
    });

    return updated;
}

function replacePageMediaUrl(oldUrl, newUrl) {
    if (!oldUrl || !newUrl) {
        return 0;
    }

    const oldCandidates = urlCandidates(oldUrl);
    let updated = 0;

    document.querySelectorAll('img[src], source[srcset]').forEach((element) => {
        updated += updateAttribute(element, 'src', oldCandidates, newUrl);
        updated += updateAttribute(element, 'srcset', oldCandidates, newUrl);
    });

    document.querySelectorAll('link[rel~="icon"], link[rel~="preload"][as="image"]').forEach((element) => {
        updated += updateAttribute(element, 'href', oldCandidates, newUrl);
        updated += updateAttribute(element, 'imagesrcset', oldCandidates, newUrl);
    });

    document.querySelectorAll('[style]').forEach((element) => {
        const current = element.getAttribute('style');
        const next = replaceAll(current, oldCandidates, newUrl);

        if (next && next !== current) {
            element.setAttribute('style', next);
            updated += 1;
        }
    });

    return updated;
}

function applyPageMediaItem(item, previous = null, action = 'updated', sentAt = null) {
    if (!item?.key || !item?.url) {
        return 0;
    }

    const version = item.updated_at || sentAt || Date.now();
    const newUrl = cacheBustUrl(item.url, version);
    const oldUrl = previous?.applied_url || previous?.url;
    let changedElements = 0;

    if (oldUrl) {
        changedElements += replacePageMediaUrl(oldUrl, newUrl);
    }

    changedElements += updateKeyedElements(item.key, newUrl);
    pageMedia.set(item.key, { ...item, applied_url: newUrl });

    window.dispatchEvent(new CustomEvent('tk:page-media-changed', {
        detail: {
            action,
            item: { ...item, url: newUrl },
            changedElements,
            sentAt,
        },
    }));

    return changedElements;
}

async function loadPageMedia({ applyChanges = false, source = 'initial' } = {}) {
    const response = await fetch('/api/page-media', {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
    });

    if (!response.ok) {
        throw new Error('Unable to load page media.');
    }

    const data = await response.json();
    let changedElements = 0;

    Object.entries(data.media || {}).forEach(([key, item]) => {
        const previous = pageMedia.get(key);
        const changed = previous && (
            previous.url !== item.url ||
            previous.updated_at !== item.updated_at ||
            previous.status !== item.status
        );

        if (applyChanges && changed) {
            changedElements += applyPageMediaItem(item, previous, source);
            return;
        }

        pageMedia.set(key, {
            ...item,
            applied_url: previous?.applied_url || item.url,
        });
    });

    return { media: pageMedia, changedElements };
}

function handlePageMediaChanged(event) {
    const item = event?.item;
    if (!item?.key || !item?.url) {
        return;
    }

    applyPageMediaItem(item, pageMedia.get(item.key), event.action || 'updated', event.sent_at || null);
}

function startPageMediaPolling() {
    if (pageMediaPollTimer) {
        return;
    }

    pageMediaPollTimer = window.setInterval(() => {
        loadPageMedia({ applyChanges: true, source: 'poll' }).catch((error) => {
            console.warn(error.message || 'Live page media polling failed.');
        });
    }, pageMediaPollIntervalMs);

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            loadPageMedia({ applyChanges: true, source: 'poll' }).catch(() => {});
        }
    });
}

async function initLiveUpdates() {
    try {
        await loadPageMedia();
    } catch (error) {
        console.warn(error.message || 'Live page media cache could not be loaded.');
    }

    if (window.Echo) {
        window.Echo
            .channel('public.live')
            .listen('.page-media.changed', handlePageMediaChanged);
    }

    startPageMediaPolling();
}

window.TKLive = {
    pageMedia,
    refreshPageMedia: loadPageMedia,
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLiveUpdates, { once: true });
} else {
    initLiveUpdates();
}
