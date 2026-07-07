@props([
    'coordinates' => [],   // [[lat, lng], ...] (from Activity::route_coordinates) or a JSON string
    'interactive' => true, // false → a static preview: no pan/zoom, no attribution anchors.
    'label' => null,       // optional place name shown in a popup (with a tail) on the departure pin.
    'eyebrow' => null,     // optional eyebrow above the popup label (e.g. "Vertrekpunt").
])

{{--
    Renders a ride's GPX route on a Leaflet map (CARTO light tiles, brand-red track,
    a departure pin). Shared by the ride detail page (interactive) and the chapter
    page's next-ride card (a static preview that sits inside the card link).

    The static preview is non-interactive AND drops Leaflet's attribution control on
    purpose: that control injects <a> tags, which would nest invalid anchors inside
    the card's single body link. The full, attributed map is one click away on detail.

    Pass the container's own size/appearance via class (e.g. `activity-info-map__route`
    or `next-ride__map`); this component only adds behaviour.
--}}
@php
    $coordsJson = is_string($coordinates) ? $coordinates : json_encode(array_values($coordinates));
@endphp

<div
    {{ $attributes->class('js-route-map') }}
    data-coordinates="{{ $coordsJson }}"
    data-interactive="{{ $interactive ? 'true' : 'false' }}"
    @if($label) data-label="{{ $label }}" @endif
    @if($eyebrow) data-eyebrow="{{ $eyebrow }}" @endif
></div>

@once
    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9/dist/leaflet.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rootStyle = getComputedStyle(document.documentElement);
            const brandRed = rootStyle.getPropertyValue('--color-kidical-red').trim() || '#E63A7B';
            const brandYellow = rootStyle.getPropertyValue('--color-kidical-yellow').trim() || '#f9d924';
            const brandInk = rootStyle.getPropertyValue('--color-kidical-ink').trim() || '#281a39';

            document.querySelectorAll('.js-route-map').forEach((el) => {
                const coords = JSON.parse(el.dataset.coordinates || '[]');
                if (!coords.length) return;

                const interactive = el.dataset.interactive !== 'false';

                const map = L.map(el, {
                    zoomControl: interactive,
                    scrollWheelZoom: false,
                    dragging: interactive,
                    doubleClickZoom: interactive,
                    boxZoom: interactive,
                    keyboard: interactive,
                    touchZoom: interactive,
                    tap: interactive,
                    attributionControl: interactive,
                });

                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    subdomains: 'abcd',
                    maxZoom: 19,
                }).addTo(map);

                if (coords.length > 1) {
                    // Yellow track over a dark casing — the ink outline gives the pale brand
                    // yellow real contrast against the light CARTO tiles.
                    L.polyline(coords, { color: brandInk, weight: 8, opacity: 0.9, lineCap: 'round', lineJoin: 'round' }).addTo(map);
                    const polyline = L.polyline(coords, { color: brandYellow, weight: 5, opacity: 1, lineCap: 'round', lineJoin: 'round' }).addTo(map);

                    map.invalidateSize();
                    map.fitBounds(polyline.getBounds(), { padding: [8, 8], maxZoom: 16 });
                } else {
                    // Single coordinate → a location-only map (non-ride activities):
                    // no track, just the pin on its neighbourhood.
                    map.invalidateSize();
                    map.setView(coords[0], 14);
                }

                // Departure pin.
                const departureIcon = L.divIcon({
                    html: `<svg width="28" height="38" viewBox="0 0 28 38" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M14 1C6.82 1 1 6.82 1 14C1 24 14 37 14 37C14 37 27 24 27 14C27 6.82 21.18 1 14 1Z" fill="${brandRed}"/>
                        <circle cx="14" cy="14" r="5.5" fill="rgba(0,0,0,0.2)"/>
                        <circle cx="14" cy="14" r="3.5" fill="white"/>
                    </svg>`,
                    className: 'activity-map-marker',
                    iconAnchor: [14, 37],
                    iconSize: [28, 38],
                    popupAnchor: [0, -34],
                });
                const marker = L.marker(coords[0], { icon: departureIcon }).addTo(map);

                // Departure label — an always-open popup with a tail pointing to the pin.
                // The caller renders the same text as an accessible chip in the DOM (see
                // .activity-facts__map-label--fallback), so this popup is purely visual.
                const labelText = el.dataset.label || '';
                if (labelText) {
                    const eyebrow = el.dataset.eyebrow
                        ? `<dt>${el.dataset.eyebrow}</dt>`
                        : '';
                    marker.bindPopup(`<dl class="map-pin-popup__label">${eyebrow}<dd>${labelText}</dd></dl>`, {
                        className: 'map-pin-popup',
                        closeButton: false,
                        autoClose: false,
                        closeOnClick: false,
                    }).openPopup();
                }
            });
        });
        </script>
    @endpush
@endonce
