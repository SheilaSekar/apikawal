# APIKAWAL — Progress

## Checklist fase (Laravel + Blade + Tailwind + MySQL)
- [x] Fase 0 — Fondasi Laravel (commit `3e0935b`)
- [x] Fase 1 — Auth dan peran (commit `a8867a4`)
- [x] Fase 2 — Lapor, foto, duplikat, rate limit (commit `462f6bd`)
- [x] Fase 3 — Verifikasi, prioritas, state machine (commit `730f238`)
- [x] Fase 4 — Peta, antrean petugas, dashboard, landing (commit `0b8be9b`)
- [x] Fase 5 — Admin (commit `7d5f98a`)
- [x] Fase 6 — Analitik dan API (commit `82bd6c0`)
- [x] Fase 7 — Seed dan dokumentasi (commit `28a95a7`)
- [x] Fase 8 — Polishing, keamanan, deploy, penutupan (commit `a631f61`)

## Fase 0 — Fondasi
- [x] Laravel 13.39.0 + Breeze Blade tanpa menimpa file pelindung; PHP 8.3.36, SQLite lokal, locale id, config, enum, Geo, layout dan komponen dasar.
- Validasi saat fase: 28 test (66 assertions), Vite build lulus.

## Fase 1 — Auth dan peran
- [x] Registrasi warga ditetapkan server, throttle Breeze, pemeriksaan akun aktif, middleware role, policy, notifikasi dan dashboard awal.
- Validasi saat fase: 33 test (79 assertions).

## Fase 2 — Pelaporan
- [x] Laporan/foto di DB, MIME nyata + re-encode JPEG maks. 1280px, peta pin/geolokasi, duplicate matching 300m/6 jam, dukungan otomatis, kode AK, cache foto, rate limit 5 laporan/jam.
- Validasi saat fase: 40 test (103 assertions).

## Fase 3 — Verifikasi, prioritas, state machine
- [x] Suara berbobot, skor dan rincian faktor/cap 59, alur state tervalidasi, logs/notifikasi transaksional, flag penyalahgunaan, detail laporan.
- Validasi saat fase: 47 test (135 assertions).

## Fase 4 — Peta, landing, dashboard, antrean
- [x] Landing KPI, laporan terbaru dan peta mini; peta Leaflet filter/layer/list dengan koordinat publik dibulatkan; dashboard/notifikasi; antrean petugas.
- Validasi saat fase: 47 test (135 assertions).

## Fase 5 — Admin
- [x] Kelola pengguna/aktif/peran/status relawan, moderasi + alasan penolakan, flag, bobot total 100/reset, impor hotspot, hitung grid, ekspor CSV.
- Validasi saat fase: 49 test (141 assertions).

## Fase 6 — Analitik dan API
- [x] KPI/tren/distribusi, choropleth grid, tabel top 10, API reports/detail/risk-areas/stats/health, CORS GET, rate limit, cache dan halaman API/Tentang.
- [x] Feature test API anonim, koordinat tiga desimal, moderasi tersembunyi dan health check.
- Validasi saat fase: 52 test (154 assertions).

## Fase 7 — Seed dan dokumentasi
- [x] Seed idempoten: demo opsional, 30 laporan, 315 hotspot, 32 POI, foto sintetis, seluruh status/timeline, prioritas, 192 sel grid.
- [x] Factories, command import/recompute, README dan semua dokumen wajib, Dockerfile, entrypoint, panduan shared hosting.
- Validasi: seed dua kali pada SQLite disposable menghasilkan jumlah tetap (30/315/32/12/192); composer check 52 test (154 assertions).

## Fase 8 — Polishing dan penutupan
- [x] Header keamanan/CSP, error 403/404/500, perbaikan otorisasi Form Request + Policy, batas moderasi reputasi, alur pengajuan/penolakan relawan, privasi timeline/API, status moderation tercatat.
- [x] Grid memakai dimensi tepat 12×16; antrean memperbarui skor usang sebelum pagination; peta mini, indikator loading/error dan simbol status.
- [x] Feature test alur laporan → dua verifikasi → diteruskan → penanganan → terkendali → selesai.
- Validasi akhir `composer check`: Pint passed; 58 test (195 assertions); Vite production build lulus.
- Seed final dua kali pada SQLite sementara: 30 laporan, 315 hotspot, 32 POI, 12 akun, 192 sel; file sementara dibersihkan.
- Deploy online tidak dilakukan karena tidak ada kredensial hosting; instruksi dan file deploy disiapkan.
- Commit: `a631f61` (`fix: harden access and complete responsive workflows`).

## Catatan kondisi dan tindak lanjut manual
- PHP checks memakai PHP 8.3.36 dan `.local/php/conf.d`; konfigurasi lokal diabaikan Git.
- Toolchain: PHP 8.3.36, Composer 2.8.12, Node 24.21.0, npm 11.19.0, Git 2.53.0.
- Git memakai `safe.directory` per command karena kepemilikan `.git` berbeda; konfigurasi global tidak diubah.
- Default `TRUSTED_PROXIES` lokal adalah 127.0.0.1; sebelum deploy isi alamat/CIDR proxy hosting.
- Tim perlu mengisi nama anggota/logo, memilih data hotspot nyata setelah memeriksa lisensi, mengubah password demo, memeriksa OSM tile policy, mencoba deploy, dan menguji tampilan pada layar 360px.
- Belum ada remote Git terkonfigurasi; repository belum dipush/dipastikan public. Tim perlu menambahkan URL remote GitHub yang benar, memastikan visibility public, lalu push commit.
- Tidak ada fitur wajib bagian “tidak boleh dipotong” yang sengaja dipangkas. Integrasi resmi, WhatsApp/SMS, cuaca real-time, kalibrasi pakar, model prediksi, dan PWA/offline tetap proyeksi.
