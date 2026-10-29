import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const mapElement = document.getElementById('report-map');

if (mapElement) {
    const latInput = document.getElementById('report-lat');
    const lngInput = document.getElementById('report-lng');
    const initial = [Number(mapElement.dataset.lat), Number(mapElement.dataset.lng)];
    const map = L.map(mapElement).setView(initial, 9);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>',
    }).addTo(map);

    const markerIcon = L.divIcon({ className: 'report-marker', html: '<span aria-hidden="true">📍</span>', iconSize: [32, 32], iconAnchor: [16, 30] });
    const marker = L.marker(initial, { draggable: true, icon: markerIcon }).addTo(map);
    const updateFields = (point) => {
        latInput.value = point.lat.toFixed(6);
        lngInput.value = point.lng.toFixed(6);
    };
    marker.on('dragend', () => updateFields(marker.getLatLng()));
    map.on('click', (event) => {
        marker.setLatLng(event.latlng);
        updateFields(event.latlng);
    });
    [latInput, lngInput].forEach((input) => input.addEventListener('change', () => {
        const point = [Number(latInput.value), Number(lngInput.value)];
        if (Number.isFinite(point[0]) && Number.isFinite(point[1])) marker.setLatLng(point);
    }));

    document.getElementById('check-duplicate')?.addEventListener('click', async () => {
        const output = document.getElementById('duplicate-result');
        output.textContent = 'Memeriksa laporan sekitar…';
        const response = await fetch('/lapor/cek-duplikat', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({ lat: latInput.value, lng: lngInput.value }),
        });
        if (!response.ok) { output.textContent = 'Pemeriksaan gagal. Anda tetap dapat mengirim laporan untuk diperiksa server.'; return; }
        const result = await response.json();
        output.textContent = result.kind === 'duplicate' ? `Ada laporan serupa (${result.code}). Laporanmu akan menambah dukungan.` : result.kind === 'same_reporter' ? 'Anda sudah melaporkan lokasi sekitar ini.' : 'Belum ditemukan laporan serupa.';
    });

    document.getElementById('use-my-location')?.addEventListener('click', () => {
        if (!navigator.geolocation) return alert('Perangkat ini tidak mendukung geolokasi. Pilih titik langsung di peta.');
        navigator.geolocation.getCurrentPosition(({ coords }) => {
            const point = { lat: coords.latitude, lng: coords.longitude };
            marker.setLatLng(point);
            map.setView(point, 13);
            updateFields(point);
        }, () => alert('Lokasi tidak dapat diakses. Pilih titik langsung di peta.'), { enableHighAccuracy: true, timeout: 10000 });
    });
}

const photoInput = document.getElementById('report-photos');
const preview = document.getElementById('photo-preview');
photoInput?.addEventListener('change', () => {
    preview.replaceChildren();
    const files = [...photoInput.files];
    if (files.length > 3 || files.some((file) => file.size > 5 * 1024 * 1024)) {
        preview.textContent = 'Pilih maksimal tiga foto, masing-masing paling besar 5 MB.';
        photoInput.value = '';
        return;
    }
    files.forEach((file) => {
        const image = document.createElement('img');
        image.src = URL.createObjectURL(file);
        image.alt = `Pratinjau ${file.name}`;
        image.className = 'aspect-square w-full rounded-md object-cover';
        preview.append(image);
    });
});
