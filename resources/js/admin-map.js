/**
 * Admin dashboard map (App\Filament\Widgets\AvailabilityMap): a pin for each availability
 * check, using Leaflet with OpenStreetMap tiles. Registered as an Alpine component.
 */
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Cleveland, TN: shown when there are no pins yet.
const DEFAULT_CENTER = [35.1595, -84.8766];

// Brand-blue pin drawn in SVG, so no marker images need bundling.
const pin = L.divIcon({
    className: '',
    html: '<svg width="28" height="38" viewBox="0 0 28 38" aria-hidden="true"><path d="M14 37s12-12.6 12-22A12 12 0 0 0 2 15c0 9.4 12 22 12 22z" fill="#0053a6" stroke="#fff" stroke-width="2"/><circle cx="14" cy="15" r="4.5" fill="#ffc220"/></svg>',
    iconSize: [28, 38],
    iconAnchor: [14, 37],
    popupAnchor: [0, -32],
});

// Popup content built from text nodes: names and addresses are visitor input.
function popupFor(point) {
    const container = document.createElement('div');

    const name = document.createElement('strong');
    name.textContent = point.name;

    const address = document.createElement('div');
    address.textContent = point.address;

    const link = document.createElement('a');
    link.href = point.url;
    link.textContent = 'View';
    link.style.fontWeight = '600';

    container.append(name, address, link);

    return container;
}

function availabilityMap(points) {
    return {
        init() {
            const map = L.map(this.$el, { scrollWheelZoom: false });

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            }).addTo(map);

            const markers = points.map((point) =>
                L.marker([point.lat, point.lng], { icon: pin, title: point.name }).bindPopup(popupFor(point)),
            );

            if (markers.length) {
                const group = L.featureGroup(markers).addTo(map);
                map.fitBounds(group.getBounds(), { padding: [40, 40], maxZoom: 15 });
            } else {
                map.setView(DEFAULT_CENTER, 12);
            }
        },
    };
}

// Register whether this runs before Alpine starts (normal page load) or after.
const register = () => window.Alpine.data('availabilityMap', availabilityMap);

if (window.Alpine) register();
document.addEventListener('alpine:init', register);
