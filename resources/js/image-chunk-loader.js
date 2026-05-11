const PLACEHOLDER =
    'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="1" height="1"%3E%3C/svg%3E';

const queue = [];
let activeLoads = 0;
let flushTimer = null;

const chunkSize = Number.parseInt(document.documentElement.dataset.imageChunkSize || '4', 10);
const maxConcurrent = Number.isFinite(chunkSize) && chunkSize > 0 ? chunkSize : 4;
const chunkDelay = 80;

function isSkippableImage(img) {
    const src = img.getAttribute('src') || '';

    return (
        img.dataset.noChunk === 'true' ||
        img.dataset.tkChunkLoaded === 'true' ||
        img.dataset.tkChunkLoading === 'true' ||
        img.getAttribute('fetchpriority') === 'high' ||
        img.getAttribute('loading') === 'eager' ||
        src.startsWith('data:') ||
        src.startsWith('blob:') ||
        src === PLACEHOLDER
    );
}

function queueImage(img) {
    if (queue.includes(img) || img.dataset.tkChunkLoaded === 'true') return;

    queue.push(img);
    scheduleFlush();
}

function scheduleFlush() {
    if (flushTimer) return;

    flushTimer = window.setTimeout(() => {
        flushTimer = null;
        flushQueue();
    }, chunkDelay);
}

function flushQueue() {
    while (activeLoads < maxConcurrent && queue.length > 0) {
        const img = queue.shift();
        loadImage(img);
    }

    if (queue.length > 0) {
        scheduleFlush();
    }
}

function loadImage(img) {
    if (!img || img.dataset.tkChunkLoaded === 'true') return;

    const src = img.dataset.tkSrc;
    const srcset = img.dataset.tkSrcset;

    if (!src && !srcset) return;

    activeLoads += 1;
    img.dataset.tkChunkLoading = 'true';

    const done = () => {
        activeLoads = Math.max(0, activeLoads - 1);
        img.dataset.tkChunkLoaded = 'true';
        delete img.dataset.tkChunkLoading;
        scheduleFlush();
    };

    img.addEventListener('load', done, { once: true });
    img.addEventListener('error', done, { once: true });

    if (srcset) {
        img.setAttribute('srcset', srcset);
        delete img.dataset.tkSrcset;
    }

    if (src) {
        img.setAttribute('src', src);
        delete img.dataset.tkSrc;
    }
}

const observer = 'IntersectionObserver' in window
    ? new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            observer.unobserve(entry.target);
            queueImage(entry.target);
        });
    }, {
        rootMargin: '500px 0px',
        threshold: 0.01,
    })
    : null;

function prepareImage(img) {
    if (!(img instanceof HTMLImageElement) || isSkippableImage(img)) return;

    img.loading = img.loading || 'lazy';
    img.decoding = img.decoding || 'async';

    if (img.complete && img.naturalWidth > 0) {
        img.dataset.tkChunkLoaded = 'true';
        return;
    }

    const src = img.getAttribute('src');
    const srcset = img.getAttribute('srcset');

    if (!src && !srcset) return;

    if (src) {
        img.dataset.tkSrc = src;
        img.setAttribute('src', PLACEHOLDER);
    }

    if (srcset) {
        img.dataset.tkSrcset = srcset;
        img.removeAttribute('srcset');
    }

    if (observer) {
        observer.observe(img);
    } else {
        queueImage(img);
    }
}

function prepareImages(root = document) {
    root.querySelectorAll?.('img').forEach(prepareImage);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => prepareImages(), { once: true });
} else {
    prepareImages();
}

new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
            if (node instanceof HTMLImageElement) {
                prepareImage(node);
                return;
            }

            if (node instanceof Element) {
                prepareImages(node);
            }
        });

        if (
            mutation.type === 'attributes' &&
            mutation.target instanceof HTMLImageElement &&
            (mutation.attributeName === 'src' || mutation.attributeName === 'srcset')
        ) {
            prepareImage(mutation.target);
        }
    });
}).observe(document.documentElement, {
    childList: true,
    subtree: true,
    attributes: true,
    attributeFilter: ['src', 'srcset'],
});
