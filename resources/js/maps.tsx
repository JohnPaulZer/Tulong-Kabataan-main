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
    const trimmed = query.trim();
    if (!trimmed) return null;

    const url = new URL('/search', mapConfig.nominatimBaseUrl);
    url.searchParams.set('format', 'jsonv2');
    url.searchParams.set('limit', '1');
    url.searchParams.set('addressdetails', '1');
    url.searchParams.set('countrycodes', mapConfig.countryCodes);
    url.searchParams.set('q', trimmed);

    const results = await fetchJson(url);
    const first = Array.isArray(results) ? results[0] : null;
    if (!first) return null;

    const coordinate = normalizeCoordinate({
        latitude: first.lat,
        longitude: first.lon,
    });

    if (!coordinate) return null;

    return {
        coordinate,
        label: first.display_name || trimmed,
    };
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
            if (event.key === 'Enter') {
                event.preventDefault();
                runSearch();
            }
        });

        if (searchInput && options.debounceSearch) {
            let timer: number | undefined;
            searchInput.addEventListener('input', () => {
                window.clearTimeout(timer);
                const value = searchInput.value.trim();
                if (value.length < 5) return;

                timer = window.setTimeout(() => {
                    search(value, true);
                }, 900);
            });
        }
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
