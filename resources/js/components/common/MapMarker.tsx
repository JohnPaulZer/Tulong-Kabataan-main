import React from 'react';
import { Marker, Popup } from 'react-leaflet';
import type { Coordinate } from '../../config/mapConfig';

export interface MapMarkerData {
    id?: string | number;
    coordinate: Coordinate;
    title?: string;
    description?: string;
}

interface MapMarkerProps {
    marker: MapMarkerData;
    draggable?: boolean;
    onDragEnd?: (coordinate: Coordinate) => void;
}

export function MapMarker({ marker, draggable = false, onDragEnd }: MapMarkerProps) {
    const hasPopup = Boolean(marker.title || marker.description);

    return (
        <Marker
            draggable={draggable}
            position={[marker.coordinate.latitude, marker.coordinate.longitude]}
            eventHandlers={
                draggable && onDragEnd
                    ? {
                          dragend(event) {
                              const position = event.target.getLatLng();
                              onDragEnd({
                                  latitude: position.lat,
                                  longitude: position.lng,
                              });
                          },
                      }
                    : undefined
            }
        >
            {hasPopup ? (
                <Popup>
                    <div className="tk-map-popup">
                        {marker.title ? <strong>{marker.title}</strong> : null}
                        {marker.description ? <span>{marker.description}</span> : null}
                    </div>
                </Popup>
            ) : null}
        </Marker>
    );
}
