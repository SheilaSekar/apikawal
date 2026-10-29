import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('apiKawalMap', () => ({
        map: null, markers: null, reports: [], mode: 'map',
        filters: { status: '', priority_level: '', land_type: '', since: '', hotspots: false },
        init() {
            this.map = L.map('public-map').setView([-3.3, 105.4], 8);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18, attribution: '&copy; OpenStreetMap contributors' }).addTo(this.map);
            this.markers = L.layerGroup().addTo(this.map);
            this.load();
            this.$watch('mode', () => setTimeout(() => this.map.invalidateSize(), 100));
        },
        async load() {
            const query = new URLSearchParams(Object.fromEntries(Object.entries(this.filters).filter(([key, value]) => value && key !== 'hotspots')));
            if (this.filters.hotspots) query.set('hotspots', '1');
            const response = await fetch(`/peta/data?${query}`);
            if (!response.ok) return;
            const data = await response.json(); this.reports = data.reports;
            this.markers.clearLayers();
            for (const item of data.reports) {
                const color = item.level === 'kritis' ? '#991b1b' : item.level === 'tinggi' ? '#c2410c' : item.level === 'sedang' ? '#b45309' : '#166534';
                const icon = L.divIcon({ className: '', html: `<span style="display:grid;place-items:center;width:34px;height:34px;background:${color};color:white;border:2px solid white;border-radius:50%;font-weight:bold;box-shadow:0 1px 5px #555">${item.status==='baru'?'!':item.status==='selesai'?'✓':'•'}</span>`, iconSize: [34, 34], iconAnchor: [17, 17] });
                L.marker([item.lat, item.lng], { icon }).bindPopup(`<a href="/laporan/${encodeURIComponent(item.code)}"><strong>${item.code}</strong></a><br>${item.status_label}<br>Prioritas ${item.level} (${item.priority})`).addTo(this.markers);
            }
            for (const spot of data.hotspots) L.circleMarker([spot.lat, spot.lng], { radius: 5, color: '#7c3aed' }).bindPopup(`Hotspot: ${spot.source}`).addTo(this.markers);
        },
    }));
});
