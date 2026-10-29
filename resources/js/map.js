import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('apiKawalMap', () => ({
        map: null, markers: null, reports: [], mode: 'map', loading: false, error: '',
        filters: { status: '', priority_level: '', land_type: '', since: '', hotspots: false },
        init() {
            this.filters = { ...this.filters, ...JSON.parse(this.$el.dataset.mapFilters || '{}') };
            this.map = L.map('public-map').setView([-3.3, 105.4], 8);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18, attribution: '&copy; OpenStreetMap contributors' }).addTo(this.map);
            this.markers = L.layerGroup().addTo(this.map);
            this.load();
            this.$watch('mode', () => setTimeout(() => this.map.invalidateSize(), 100));
        },
        async load() {
            this.loading = true; this.error = '';
            const query = new URLSearchParams(Object.fromEntries(Object.entries(this.filters).filter(([key, value]) => value && key !== 'hotspots')));
            if (this.filters.hotspots) query.set('hotspots', '1');
            try {
                const response = await fetch(`/peta/data?${query}`);
                if (!response.ok) throw new Error('Data peta tidak dapat dimuat. Coba lagi.');
                const data = await response.json(); this.reports = data.reports;
                this.markers.clearLayers();
                const symbols = { baru: '!', terverifikasi: 'V', diteruskan: '→', dalam_penanganan: 'P', terkendali: '=', selesai: '✓', padam_sendiri: '0' };
                for (const item of data.reports) {
                    const color = item.level === 'kritis' ? '#991b1b' : item.level === 'tinggi' ? '#c2410c' : item.level === 'sedang' ? '#b45309' : '#166534';
                    const symbol = symbols[item.status] || '?';
                    const icon = L.divIcon({ className: '', html: `<span style="display:grid;place-items:center;width:34px;height:34px;background:${color};color:white;border:2px solid white;border-radius:50%;font-weight:bold;box-shadow:0 1px 5px #555">${symbol}</span>`, iconSize: [34, 34], iconAnchor: [17, 17] });
                    L.marker([item.lat, item.lng], { icon }).bindPopup(`<a href="/laporan/${encodeURIComponent(item.code)}"><strong>${item.code}</strong></a><br>${item.status_label}<br>Prioritas ${item.level} (${item.priority})`).addTo(this.markers);
                }
                for (const spot of data.hotspots) L.circleMarker([spot.lat, spot.lng], { radius: 5, color: '#7c3aed' }).bindPopup(`Hotspot: ${L.Util.escapeHTML(spot.source)}`).addTo(this.markers);
            } catch (error) {
                this.error = error.message || 'Terjadi gangguan saat memuat peta.';
            } finally {
                this.loading = false;
            }
        },
    }));
    window.Alpine.data('apiKawalRiskMap', () => ({
        map: null,
        async init() {
            this.map = L.map('risk-map').setView([-3.3, 105.4], 8);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18, attribution: '&copy; OpenStreetMap contributors' }).addTo(this.map);
            const response = await fetch('/api/v1/risk-areas');
            if (!response.ok) return;
            const { data } = await response.json();
            for (const cell of data) {
                const color = cell.level === 'tinggi' ? '#b91c1c' : cell.level === 'sedang' ? '#d97706' : '#15803d';
                L.rectangle([[cell.bbox.south, cell.bbox.west], [cell.bbox.north, cell.bbox.east]], { color, weight: 1, fillColor: color, fillOpacity: 0.28 })
                    .bindPopup(`Sel ${cell.cell_key}<br>Level ${cell.level}<br>Skor ${Math.round(cell.score)}<br>Hotspot ${cell.hotspot_count} · laporan ${cell.report_count}`).addTo(this.map);
            }
        },
    }));
    window.Alpine.data('apiKawalLandingMap', () => ({
        async init() {
            const map = L.map('landing-map', { scrollWheelZoom: false }).setView([-3.3, 105.4], 8);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18, attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
            const response = await fetch('/peta/data');
            if (!response.ok) return;
            const { reports } = await response.json();
            reports.slice(0, 80).forEach((item) => L.circleMarker([item.lat, item.lng], { radius: 7, color: item.level === 'kritis' || item.level === 'tinggi' ? '#b91c1c' : '#15803d' }).addTo(map));
        },
    }));
    window.Alpine.data('apiKawalOfficerMap', () => ({
        async init() {
            const map = L.map('officer-map').setView([-3.3, 105.4], 8);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18, attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
            const response = await fetch('/peta/data');
            if (!response.ok) return;
            const { reports } = await response.json();
            reports.filter((report) => ['terverifikasi', 'diteruskan', 'dalam_penanganan', 'terkendali'].includes(report.status)).forEach((report) => {
                L.circleMarker([report.lat, report.lng], { radius: 8, color: report.level === 'kritis' || report.level === 'tinggi' ? '#991b1b' : '#b45309' })
                    .bindPopup(`<a href="/laporan/${encodeURIComponent(report.code)}">${L.Util.escapeHTML(report.code)}</a><br>${L.Util.escapeHTML(report.status_label)} · ${report.priority}`).addTo(map);
            });
        },
    }));
});
