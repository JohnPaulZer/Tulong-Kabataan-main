import React, { useEffect } from 'react';
import { MapContainer, TileLayer, useMap, useMapEvents } from 'react-leaflet';
import L from 'leaflet';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
import { mapConfig, isValidCoordinate, type Coordinate } from '../../config/mapConfig';
import { MapMarker, type MapMarkerData } from './MapMarker';

L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

export interface AppMapProps {
    center?: Coordinate | null;
    zoom?: number;
    markers?: MapMarkerData[];
    selectedMarkerId?: string | number;
    readOnly?: boolean;
    clickToSelect?: boolean;
    markerDraggable?: boolean;
    height?: string;
    emptyText?: string;
    onSelectLocation?: (coordinate: Coordinate) => void;
}

function MapClickHandler({
    enabled,
    onSelectLocation,
}: {
    enabled: boolean;
    onSelectLocation?: (coordinate: Coordinate) => void;
}) {
    useMapEvents({
        click(event) {
            if (!enabled || !onSelectLocation) return;

            onSelectLocation({
                latitude: event.latlng.lat,
                longitude: event.latlng.lng,
            });
        },
    });

    return null;
}

function MapViewSync({ center, zoom }: { center: Coordinate; zoom: number }) {
    const map = useMap();

    useEffect(() => {
        map.invalidateSize();
        map.setView([center.latitude, center.longitude], zoom, {
            animate: true,
        });
    }, [center.latitude, center.longitude, map, zoom]);

    useEffect(() => {
        const timer = window.setTimeout(() => map.invalidateSize(), 120);
        return () => window.clearTimeout(timer);
    }, [map]);

    return null;
}

export function AppMap({
    center,
    zoom = mapConfig.defaultZoom,
    markers = [],
    selectedMarkerId,
    readOnly = true,
    clickToSelect = false,
    markerDraggable = false,
    height = '320px',
    emptyText = 'No map location selected yet.',
    onSelectLocation,
}: AppMapProps) {
    const validMarkers = markers.filter((marker) => isValidCoordinate(marker.coordinate));
    const selectedMarker = selectedMarkerId
        ? validMarkers.find((marker) => marker.id === selectedMarkerId)
        : validMarkers[0];
    const resolvedCenter =
        center && isValidCoordinate(center)
            ? center
            : selectedMarker?.coordinate && isValidCoordinate(selectedMarker.coordinate)
              ? selectedMarker.coordinate
              : mapConfig.defaultCenter;

    return (
        <div className="tk-map-shell" style={{ height }}>
            <MapContainer
                center={[resolvedCenter.latitude, resolvedCenter.longitude]}
                zoom={zoom}
                scrollWheelZoom={!readOnly}
                className="tk-map"
            >
                <TileLayer attribution={mapConfig.tileAttribution} url={mapConfig.tileUrl} />
                <MapViewSync center={resolvedCenter} zoom={zoom} />
                <MapClickHandler enabled={clickToSelect && !readOnly} onSelectLocation={onSelectLocation} />
                {validMarkers.map((marker) => (
                    <MapMarker
                        key={marker.id ?? `${marker.coordinate.latitude}-${marker.coordinate.longitude}`}
                        marker={marker}
                        draggable={!readOnly && markerDraggable}
                        onDragEnd={onSelectLocation}
                    />
                ))}
            </MapContainer>

            {validMarkers.length === 0 && readOnly ? <div className="tk-map-empty">{emptyText}</div> : null}
        </div>
    );
}
