@props([
    'coordinates' => [],   // [[lat, lng], ...] (from Activity::route_coordinates) or a JSON string
    'interactive' => true, // false → a static preview: no pan/zoom, no attribution anchors, no label.
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
></div>

@once
    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9/dist/leaflet.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const brandRed = getComputedStyle(document.documentElement).getPropertyValue('--color-kidical-red').trim() || '#E63A7B';

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

                const polyline = L.polyline(coords, { color: brandRed, weight: 5, opacity: 0.95 }).addTo(map);

                map.invalidateSize();
                map.fitBounds(polyline.getBounds(), { padding: [8, 8], maxZoom: 16 });

                // Departure pin. The "Vertrekpunt" label is for the full detail map only;
                // on the small preview the pin alone keeps it uncluttered.
                const label = interactive ? '<span class="activity-map-label">Vertrekpunt</span>' : '';
                const departureIcon = L.divIcon({
                    html: `<svg width="28" height="38" viewBox="0 0 28 38" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M14 1C6.82 1 1 6.82 1 14C1 24 14 37 14 37C14 37 27 24 27 14C27 6.82 21.18 1 14 1Z" fill="${brandRed}"/>
                        <circle cx="14" cy="14" r="5.5" fill="rgba(0,0,0,0.2)"/>
                        <circle cx="14" cy="14" r="3.5" fill="white"/>
                    </svg>${label}`,
                    className: 'activity-map-marker',
                    iconAnchor: [14, 37],
                    iconSize: [28, 38],
                });
                L.marker(coords[0], { icon: departureIcon }).addTo(map);
            });
        });
        </script>
    @endpush
@endonce
