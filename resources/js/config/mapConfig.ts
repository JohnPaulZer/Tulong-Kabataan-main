export interface Coordinate {
    latitude: number;
    longitude: number;
}

const mapTilerKey = import.meta.env.VITE_MAPTILER_API_KEY ?? '';
const configuredTileUrl = import.meta.env.VITE_MAP_TILE_URL ?? '';
const configuredTileAttribution = import.meta.env.VITE_MAP_TILE_ATTRIBUTION ?? '';

const mapTilerTileUrl = mapTilerKey
    ? `https://api.maptiler.com/maps/streets-v2/{z}/{x}/{y}.png?key=${mapTilerKey}`
    : '';

export const defaultMapCenter: Coordinate = {
    latitude: 12.8797,
    longitude: 121.7740,
};

export const mapConfig = {
    defaultCenter: defaultMapCenter,
    defaultZoom: 6,
    detailZoom: 16,
    tileUrl: configuredTileUrl || mapTilerTileUrl || 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    tileAttribution:
        configuredTileAttribution ||
        (mapTilerTileUrl
            ? '&copy; <a href="https://www.maptiler.com/copyright/">MapTiler</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            : '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'),
    nominatimBaseUrl: import.meta.env.VITE_NOMINATIM_BASE_URL || 'https://nominatim.openstreetmap.org',
    countryCodes: 'ph',
};

export function isValidCoordinate(coordinate?: Partial<Coordinate> | null): coordinate is Coordinate {
    if (!coordinate) return false;

    const latitude = Number(coordinate.latitude);
    const longitude = Number(coordinate.longitude);

    return (
        Number.isFinite(latitude) &&
        Number.isFinite(longitude) &&
        latitude >= -90 &&
        latitude <= 90 &&
        longitude >= -180 &&
        longitude <= 180
    );
}

export function normalizeCoordinate(
    coordinate?: Partial<Coordinate> | { lat?: number | string; lng?: number | string } | null
): Coordinate | null {
    if (!coordinate) return null;

    const latitude = 'latitude' in coordinate ? coordinate.latitude : coordinate.lat;
    const longitude = 'longitude' in coordinate ? coordinate.longitude : coordinate.lng;
    const normalized = {
        latitude: Number(latitude),
        longitude: Number(longitude),
    };

    return isValidCoordinate(normalized) ? normalized : null;
}
