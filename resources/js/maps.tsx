import React from 'react';
import { createRoot, type Root } from 'react-dom/client';
import { AppMap } from './components/common/AppMap';
import { LocationPickerMap } from './components/common/LocationPickerMap';
import {
    isValidCoordinate,
    mapConfig,
    normalizeCoordinate,
    type Coordinate,
} from './config/mapConfig';
import type { MapMarkerData } from './components/common/MapMarker';

interface GeocodeResult {
    coordinate: Coordinate;
    label: string;
}

interface LocationDetail {
    coordinate: Coordinate;
    address?: string;
}

interface PickerMountOptions {
    elementId: string;
    initialCoordinate?: Coordinate | { lat?: number | string; lng?: number | string } | null;
    initialAddress?: string;
    latInputId?: string;
    lngInputId?: string;
    addressInputId?: string;
    selectedTextId?: string;
    searchInputId?: string;
    searchButtonId?: string;
    popupTitle?: string;
    height?: string;
    debounceSearch?: boolean;
    onSelect?: (detail: LocationDetail) => void;
}

interface StaticMountOptions {
    elementId: string;
    center?: Coordinate | { lat?: number | string; lng?: number | string } | null;
    markers?: MapMarkerData[];
    zoom?: number;
    height?: string;
    emptyText?: string;
}

const roots = new Map<string, Root>();

function getRoot(elementId: string): Root | null {
    const element = document.getElementById(elementId);
    if (!element) return null;

    if (!roots.has(elementId)) {
        roots.set(elementId, createRoot(element));
    }

    return roots.get(elementId) ?? null;
}

function coordinateToText(coordinate: Coordinate): string {
    return `${coordinate.latitude.toFixed(6)}, ${coordinate.longitude.toFixed(6)}`;
}

function updateInputValue(id: string | undefined, value: string) {
    if (!id) return;
    const field = document.getElementById(id) as HTMLInputElement | HTMLTextAreaElement | null;
    if (field) field.value = value;
}

function updateText(id: string | undefined, value: string) {
    if (!id) return;
    const element = document.getElementById(id);
    if (element) element.textContent = value;
}

function escapeHtml(value: string): string {
    const element = document.createElement('div');
    element.textContent = value;
    return element.innerHTML;
}

async function fetchJson(url: URL) {
    const response = await fetch(url.toString(), {
        headers: {
            Accept: 'application/json',
        },
    });

    if (!response.ok) {
        throw new Error('Map lookup failed.');
    }

    return response.json();
}

export async function searchAddress(query: string): Promise<GeocodeResult | null> {
    const results = await searchAddressSuggestions(query, 1);
    return results[0] ?? null;
}

export async function searchAddressSuggestions(query: string, limit = 5): Promise<GeocodeResult[]> {
    const trimmed = query.trim();
    if (!trimmed) return [];

    const url = new URL('/search', mapConfig.nominatimBaseUrl);
    url.searchParams.set('format', 'jsonv2');
    url.searchParams.set('limit', String(Math.max(1, Math.min(limit, 8))));
    url.searchParams.set('addressdetails', '1');
    url.searchParams.set('countrycodes', mapConfig.countryCodes);
    url.searchParams.set('q', trimmed);

    const results = await fetchJson(url);
    if (!Array.isArray(results)) return [];

    return results
        .map((result) => {
            const coordinate = normalizeCoordinate({
                latitude: result.lat,
                longitude: result.lon,
            });

            if (!coordinate) return null;

            return {
                coordinate,
                label: result.display_name || trimmed,
            };
        })
        .filter((result): result is GeocodeResult => result !== null);
}

export async function reverseGeocode(coordinate: Coordinate): Promise<string | null> {
    if (!isValidCoordinate(coordinate)) return null;

    const url = new URL('/reverse', mapConfig.nominatimBaseUrl);
    url.searchParams.set('format', 'jsonv2');
    url.searchParams.set('lat', String(coordinate.latitude));
    url.searchParams.set('lon', String(coordinate.longitude));
    url.searchParams.set('zoom', '18');
    url.searchParams.set('addressdetails', '1');

    const result = await fetchJson(url);
    return result.display_name || null;
}

export function locateUser(): Promise<LocationDetail> {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject(new Error('Geolocation is not supported by this browser.'));
            return;
        }

        navigator.geolocation.getCurrentPosition(
            async (position) => {
                const coordinate = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                };
                const address = await reverseGeocode(coordinate).catch(() => null);
                resolve({
                    coordinate,
                    address: address ?? coordinateToText(coordinate),
                });
            },
            () => reject(new Error('Unable to get your current location.')),
            {
                enableHighAccuracy: true,
                timeout: 7000,
                maximumAge: 600000,
            }
        );
    });
}

function mountPicker(options: PickerMountOptions) {
    const root = getRoot(options.elementId);
    let selectedCoordinate = normalizeCoordinate(options.initialCoordinate);
    let selectedAddress = options.initialAddress || '';
    let reverseLookupRun = 0;
    let suggestionBox: HTMLDivElement | null = null;
    let suggestionResults: GeocodeResult[] = [];
    let activeSuggestionIndex = -1;
    let suggestionTimer: number | undefined;
    let suggestionRun = 0;

    function syncFields(coordinate: Coordinate | null, address?: string) {
        updateInputValue(options.latInputId, coordinate ? String(coordinate.latitude) : '');
        updateInputValue(options.lngInputId, coordinate ? String(coordinate.longitude) : '');

        if (address !== undefined) {
            updateInputValue(options.addressInputId, address);
        }

        updateText(
            options.selectedTextId,
            address || (coordinate ? coordinateToText(coordinate) : 'No location selected')
        );
    }

    function render() {
        if (!root) return;

        root.render(
            <LocationPickerMap
                selectedCoordinate={selectedCoordinate}
                popupTitle={options.popupTitle || 'Selected location'}
                popupText={selectedAddress || undefined}
                height={options.height}
                onLocationChange={(coordinate) => {
                    selectCoordinate(coordinate, {
                        reverse: true,
                    });
                }}
            />
        );
    }

    async function selectCoordinate(
        coordinate: Coordinate,
        selectionOptions: { address?: string; reverse?: boolean } = {}
    ): Promise<LocationDetail> {
        selectedCoordinate = coordinate;
        selectedAddress = selectionOptions.address ?? selectedAddress;
        syncFields(coordinate, selectedAddress);
        render();

        if (selectionOptions.reverse) {
            const runId = ++reverseLookupRun;
            const address = await reverseGeocode(coordinate).catch(() => null);
            if (runId === reverseLookupRun && address) {
                selectedAddress = address;
                syncFields(coordinate, address);
                render();
            }
        }

        const detail = {
            coordinate,
            address: selectedAddress || coordinateToText(coordinate),
        };
        options.onSelect?.(detail);
        return detail;
    }

    async function search(query?: string, silent = false): Promise<GeocodeResult | null> {
        const searchInput = options.searchInputId
            ? (document.getElementById(options.searchInputId) as HTMLInputElement | null)
            : null;
        const term = query ?? searchInput?.value ?? '';

        try {
            const result = await searchAddress(term);
            if (!result) {
                if (!silent) throw new Error('No matching location found.');
                return null;
            }

            await selectCoordinate(result.coordinate, {
                address: result.label,
                reverse: false,
            });
            if (searchInput) {
                searchInput.value = result.label;
            }
            hideSuggestions();
            return result;
        } catch (error) {
            if (!silent) throw error;
            return null;
        }
    }

    function clearCoordinate() {
        selectedCoordinate = null;
        selectedAddress = '';
        syncFields(null, '');
        render();
    }

    async function selectUserLocation() {
        const detail = await locateUser();
        await selectCoordinate(detail.coordinate, {
            address: detail.address,
            reverse: false,
        });
        return detail;
    }

    function wireSearch() {
        const searchInput = options.searchInputId
            ? (document.getElementById(options.searchInputId) as HTMLInputElement | null)
            : null;
        const searchButton = options.searchButtonId ? document.getElementById(options.searchButtonId) : null;

        const runSearch = () => {
            search().catch((error) => {
                console.warn(error.message);
            });
        };

        searchButton?.addEventListener('click', runSearch);
        searchInput?.addEventListener('keydown', (event) => {
            if (suggestionBox && !suggestionBox.hidden && suggestionResults.length > 0) {
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    setActiveSuggestion(activeSuggestionIndex + 1);
                    return;
                }

                if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    setActiveSuggestion(activeSuggestionIndex - 1);
                    return;
                }

                if (event.key === 'Enter' && activeSuggestionIndex >= 0) {
                    event.preventDefault();
                    selectSuggestion(activeSuggestionIndex);
                    return;
                }

                if (event.key === 'Escape') {
                    hideSuggestions();
                    return;
                }
            }

            if (event.key === 'Enter') {
                event.preventDefault();
                runSearch();
            }
        });

        if (searchInput) {
            ensureSuggestionBox(searchInput);
            searchInput.addEventListener('input', () => {
                window.clearTimeout(suggestionTimer);
                const value = searchInput.value.trim();
                if (value.length < 3) {
                    hideSuggestions();
                    return;
                }

                suggestionTimer = window.setTimeout(() => {
                    loadSuggestions(value);
                }, options.debounceSearch ? 450 : 350);
            });

            searchInput.addEventListener('focus', () => {
                if (suggestionResults.length > 0) {
                    showSuggestions();
                }
            });

            searchInput.addEventListener('blur', () => {
                window.setTimeout(hideSuggestions, 160);
            });

            window.addEventListener('resize', positionSuggestions);
            window.addEventListener('scroll', positionSuggestions, true);
        }
    }

    function ensureSuggestionBox(searchInput: HTMLInputElement) {
        if (suggestionBox) return;

        suggestionBox = document.createElement('div');
        suggestionBox.className = 'tk-map-suggestions';
        suggestionBox.hidden = true;
        suggestionBox.setAttribute('role', 'listbox');
        suggestionBox.setAttribute('aria-label', 'Location suggestions');
        suggestionBox.addEventListener('mousedown', (event) => {
            event.preventDefault();
        });

        document.body.appendChild(suggestionBox);
        searchInput.setAttribute('autocomplete', 'off');
        searchInput.setAttribute('aria-autocomplete', 'list');
    }

    function positionSuggestions() {
        const searchInput = options.searchInputId
            ? (document.getElementById(options.searchInputId) as HTMLInputElement | null)
            : null;
        if (!searchInput || !suggestionBox || suggestionBox.hidden) return;

        const rect = searchInput.getBoundingClientRect();
        suggestionBox.style.width = `${rect.width}px`;
        suggestionBox.style.left = `${rect.left + window.scrollX}px`;
        suggestionBox.style.top = `${rect.bottom + window.scrollY + 6}px`;
    }

    function showSuggestions() {
        if (!suggestionBox) return;
        suggestionBox.hidden = false;
        positionSuggestions();
    }

    function hideSuggestions() {
        if (!suggestionBox) return;
        suggestionBox.hidden = true;
        activeSuggestionIndex = -1;
    }

    function renderSuggestions(state: 'loading' | 'empty' | 'results' = 'results') {
        if (!suggestionBox) return;

        if (state === 'loading') {
            suggestionBox.innerHTML = '<div class="tk-map-suggestion tk-map-suggestion--status">Searching locations...</div>';
            showSuggestions();
            return;
        }

        if (state === 'empty') {
            suggestionBox.innerHTML = '<div class="tk-map-suggestion tk-map-suggestion--status">No matching locations found.</div>';
            showSuggestions();
            return;
        }

        suggestionBox.innerHTML = '';
        suggestionResults.forEach((result, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'tk-map-suggestion';
            button.setAttribute('role', 'option');
            button.setAttribute('aria-selected', String(index === activeSuggestionIndex));
            button.dataset.index = String(index);
            button.innerHTML = `
                <span class="tk-map-suggestion__icon" aria-hidden="true"></span>
                <span class="tk-map-suggestion__text">${escapeHtml(result.label)}</span>
            `;
            button.addEventListener('click', () => selectSuggestion(index));
            suggestionBox?.appendChild(button);
        });

        showSuggestions();
    }

    async function loadSuggestions(query: string) {
        const runId = ++suggestionRun;
        renderSuggestions('loading');

        try {
            const results = await searchAddressSuggestions(query, 5);
            if (runId !== suggestionRun) return;

            suggestionResults = results;
            activeSuggestionIndex = results.length > 0 ? 0 : -1;
            renderSuggestions(results.length > 0 ? 'results' : 'empty');
        } catch (error) {
            if (runId !== suggestionRun) return;

            console.warn(error instanceof Error ? error.message : error);
            suggestionResults = [];
            activeSuggestionIndex = -1;
            renderSuggestions('empty');
        }
    }

    function setActiveSuggestion(index: number) {
        if (!suggestionResults.length) return;

        activeSuggestionIndex = (index + suggestionResults.length) % suggestionResults.length;
        suggestionBox?.querySelectorAll<HTMLElement>('.tk-map-suggestion[role="option"]').forEach((button, buttonIndex) => {
            button.setAttribute('aria-selected', String(buttonIndex === activeSuggestionIndex));
            if (buttonIndex === activeSuggestionIndex) {
                button.scrollIntoView({
                    block: 'nearest',
                });
            }
        });
    }

    async function selectSuggestion(index: number) {
        const result = suggestionResults[index];
        const searchInput = options.searchInputId
            ? (document.getElementById(options.searchInputId) as HTMLInputElement | null)
            : null;
        if (!result) return;

        if (searchInput) {
            searchInput.value = result.label;
        }

        hideSuggestions();
        await selectCoordinate(result.coordinate, {
            address: result.label,
            reverse: false,
        });
    }

    syncFields(selectedCoordinate, selectedAddress);
    render();
    wireSearch();

    return {
        setCoordinate(coordinate: Coordinate | { lat?: number | string; lng?: number | string }, address?: string) {
            const normalized = normalizeCoordinate(coordinate);
            if (!normalized) return Promise.resolve(null);
            return selectCoordinate(normalized, {
                address,
                reverse: false,
            });
        },
        selectCoordinate,
        clearCoordinate,
        locateUser: selectUserLocation,
        search,
        invalidate() {
            render();
        },
    };
}

function mountStatic(options: StaticMountOptions) {
    const root = getRoot(options.elementId);
    let markers = (options.markers ?? []).filter((marker) => isValidCoordinate(marker.coordinate));
    let center = normalizeCoordinate(options.center) ?? markers[0]?.coordinate ?? mapConfig.defaultCenter;
    let zoom = options.zoom ?? (markers.length ? mapConfig.detailZoom : mapConfig.defaultZoom);

    function render() {
        root?.render(
            <AppMap
                center={center}
                markers={markers}
                zoom={zoom}
                readOnly
                height={options.height}
                emptyText={options.emptyText}
            />
        );
    }

    render();

    return {
        setMarkers(nextMarkers: MapMarkerData[], nextCenter?: Coordinate | null, nextZoom?: number) {
            markers = nextMarkers.filter((marker) => isValidCoordinate(marker.coordinate));
            center = nextCenter && isValidCoordinate(nextCenter) ? nextCenter : markers[0]?.coordinate ?? center;
            zoom = nextZoom ?? zoom;
            render();
        },
        setView(nextCenter: Coordinate, nextZoom?: number) {
            if (!isValidCoordinate(nextCenter)) return;
            center = nextCenter;
            zoom = nextZoom ?? zoom;
            render();
        },
        invalidate() {
            render();
        },
    };
}

function mountDeclarativeStaticMaps(rootElement: ParentNode = document) {
    rootElement.querySelectorAll<HTMLElement>('[data-tk-map-static]:not([data-tk-map-mounted])').forEach((element) => {
        if (!element.id) {
            element.id = `tk-map-${Math.random().toString(36).slice(2)}`;
        }

        const coordinate = normalizeCoordinate({
            latitude: element.dataset.lat,
            longitude: element.dataset.lng,
        });
        const title = element.dataset.title || 'Location';
        const description = element.dataset.description || '';

        element.dataset.tkMapMounted = 'true';
        mountStatic({
            elementId: element.id,
            center: coordinate,
            zoom: Number(element.dataset.zoom) || mapConfig.detailZoom,
            height: element.dataset.height || '260px',
            emptyText: element.dataset.emptyText || 'No map location available.',
            markers: coordinate
                ? [
                      {
                          id: element.id,
                          coordinate,
                          title,
                          description,
                      },
                  ]
                : [],
        });
    });
}

const TKLeafletMaps = {
    mapConfig,
    coordinateToText,
    normalizeCoordinate,
    isValidCoordinate,
    searchAddress,
    searchAddressSuggestions,
    reverseGeocode,
    locateUser,
    mountPicker,
    mountStatic,
    mountDeclarativeStaticMaps,
};

window.TKLeafletMaps = TKLeafletMaps;

document.addEventListener('DOMContentLoaded', () => {
    mountDeclarativeStaticMaps();

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node instanceof HTMLElement) {
                    mountDeclarativeStaticMaps(node);
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
});

declare global {
    interface Window {
        TKLeafletMaps: typeof TKLeafletMaps;
    }
}
