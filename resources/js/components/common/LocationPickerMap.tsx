import React from 'react';
import { AppMap } from './AppMap';
import { isValidCoordinate, mapConfig, type Coordinate } from '../../config/mapConfig';

export interface LocationPickerMapProps {
    selectedCoordinate?: Coordinate | null;
    zoom?: number;
    height?: string;
    popupTitle?: string;
    popupText?: string;
    readOnly?: boolean;
    onLocationChange?: (coordinate: Coordinate) => void;
}

export function LocationPickerMap({
    selectedCoordinate,
    zoom,
    height,
    popupTitle = 'Selected location',
    popupText,
    readOnly = false,
    onLocationChange,
}: LocationPickerMapProps) {
    const hasCoordinate = isValidCoordinate(selectedCoordinate);
    const markers = hasCoordinate
        ? [
              {
                  id: 'selected',
                  coordinate: selectedCoordinate,
                  title: popupTitle,
                  description: popupText,
              },
          ]
        : [];

    return (
        <AppMap
            center={hasCoordinate ? selectedCoordinate : mapConfig.defaultCenter}
            zoom={zoom ?? (hasCoordinate ? mapConfig.detailZoom : mapConfig.defaultZoom)}
            markers={markers}
            readOnly={readOnly}
            clickToSelect={!readOnly}
            markerDraggable={!readOnly}
            height={height}
            onSelectLocation={onLocationChange}
            emptyText="Click on the map to select a location."
        />
    );
}
