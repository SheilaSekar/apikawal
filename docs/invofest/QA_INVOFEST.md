# QA prototipe INVOFEST

## Pemeriksaan otomatis yang dijalankan

- `node --test invofest/tests/*.test.js` — 9 tes lulus: domain, seed, batas laporan/jam, duplikat, suara verifikasi, perubahan status/log.
- `node invofest/tools/check-static.mjs` — 11 halaman memuat `lang=id`, viewport, skip link, main landmark, shell, fallback banner darurat, dan seluruh stylesheet/script/favicon lokal.
- `node --check` pada modul entry, page, store, dan seed — sintaks JavaScript valid.
- Leaflet dan Chart.js dilayani dari vendor lokal, bukan CDN. Tile peta tetap memerlukan internet.

## Pemeriksaan browser yang masih perlu tim lakukan

Lingkungan implementasi tidak menyediakan browser terpasang, Playwright, atau Puppeteer. Maka ukuran di bawah **belum diverifikasi lewat render browser**. Jangan menganggap checklist ini telah lulus sebelum tim melihat halaman pada browser desktop dan ponsel.

| Lebar | Halaman yang disarankan | Yang dicek |
|---:|---|---|
| 360 px | Landing, peta, form, detail, admin, analitik | Tidak ada scroll horizontal; tombol dan form tidak terpotong; menu navigasi tetap dapat dipakai; peta/list nyaman dibaca |
| 768 px | Landing, peta, dashboard, ekosistem | Kolom tidak bertabrakan; judul dan kartu tidak saling menimpa; peta tidak menutupi kontrol |
| 1280 px | Semua halaman | Hierarki visual, kontras teks, keterbacaan, panel dan grafik tertata |

Di setiap ukuran, uji skip link, fokus keyboard, label/error form, banner darurat, tile OSM dan daftar alternatif, perubahan peran demo, reset, serta localStorage. Uji tile dengan koneksi terputus: alur inti dan daftar seharusnya tetap dapat dipakai.

## Keterbatasan teknis

- CSS memakai media query breakpoint 900px dan 640px serta `min-width:320px`; hasil visual final tetap perlu render browser.
- Tidak ada pengujian aksesibilitas otomatis eksternal maupun uji perangkat nyata di lingkungan ini.
- Tidak menjalankan `npm run build` atau `composer check`: aplikasi kompetisi sengaja tidak memiliki build step dan tidak mengubah/menggunakan backend Laravel.
