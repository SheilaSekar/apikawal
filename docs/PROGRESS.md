# APIKAWAL — Progress

## Checklist fase (stack Laravel + Blade + Tailwind + MySQL)
- [x] Fase 0 — Fondasi Laravel, Breeze Blade, locale id, layout, Geo (commit `3e0935b`)
- [x] Fase 1 — Auth dan peran (commit `a8867a4`)
- [x] Fase 2 — Lapor, foto, duplikat, dan rate limit (commit `462f6bd`)
- [x] Fase 3 — Verifikasi, prioritas, state machine (commit `730f238`)
- [x] Fase 4 — Peta, antrean petugas, dashboard, dan landing (commit `0b8be9b`)
- [x] Fase 5 — Admin (commit `7d5f98a`)
- [x] Fase 6 — Analitik dan API (composer check lulus)
- [x] Fase 7 — Seed dan dokumentasi (composer check lulus)
- [ ] Fase 8 — Polishing, keamanan, deploy, dan penutupan

## Fase 0 — Fondasi
- [x] Scaffold Laravel 13.39.0 + Breeze 2.4.2 Blade tanpa menimpa file pelindung; PHP 8.3.36 dan SQLite lokal.
- [x] Locale id, config, enum, Geo, layout, komponen dasar, `composer check`.
- Validasi: 28 test (66 assertions), Vite build lulus. Commit `3e0935b`.

## Fase 1 — Auth dan peran
- [x] Registrasi warga, throttle Breeze, pemeriksaan akun aktif, middleware role, policy, notifikasi, dashboard awal.
- Validasi: 33 test (79 assertions). Commit `a8867a4`.

## Fase 2 — Pelaporan
- [x] Laporan/foto di DB; MIME nyata dan re-encode JPEG maks. 1280 px; pin/geolokasi; cek duplikat/dukungan; kode AK; cache foto; rate limit.
- Validasi: 40 test (103 assertions). Commit `462f6bd`.

## Fase 3 — Verifikasi, prioritas, state machine
- [x] Verifikasi berbobot, skor transparan/cap 59, state machine, response log/notifikasi, abuse flag, detail laporan.
- Validasi: 47 test (135 assertions). Commit `730f238`.

## Fase 4 — Peta, landing, dasbor, antrean
- [x] Landing dengan KPI dan laporan terbaru; Leaflet filter + daftar, koordinat publik dibulatkan; dashboard warga; antrean petugas.
- Validasi: 47 test (135 assertions). Commit `0b8be9b`.

## Fase 5 — Admin
- [x] Pengguna/peran, moderasi, abuse flag, bobot total 100/reset, impor CSV hotspot, grid, ekspor CSV.
- Validasi: 49 test (141 assertions). Commit `7d5f98a`.

## Fase 6 — Analitik dan API
- [x] Dashboard KPI/tren/distribusi/grid choropleth dan tabel sel; dokumentasi API; API read-only reports/detail, risk areas, stats, health; filter, rate limit, cache, CORS GET.
- [x] Feature test memeriksa anonimasi, koordinat tiga desimal, penyembunyian moderasi, health check.
- Validasi: `composer check` lulus — Pint; 52 test (154 assertions); Vite production build lulus.
- Commit: menunggu.

## Fase 7 — Seed dan dokumentasi
- [x] Seeder idempoten dengan akun opsional `SEED_DEMO=true`, 32 POI, 315 hotspot simulasi, 30 laporan semua status, dukungan, timeline, foto GD, skor prioritas, grid.
- [x] Factory utama, perintah impor hotspot dan hitung grid, FK notifikasi, README dan seluruh dokumen wajib, Docker/shared-hosting panduan.
- Validasi: seeder berhasil dua kali pada SQLite disposable: 30 laporan, 315 hotspot, 32 POI, 12 akun, 221 sel grid. `composer check` lulus; 52 test (154 assertions), Vite build lulus.
- Catatan: satu percobaan inspeksi PDO langsung tanpa scan-dir gagal menemukan driver; diverifikasi kembali lewat Artisan dengan konfigurasi PHP 8.3 yang benar.
- Commit: menunggu.

## Catatan kondisi
- PHP checks menggunakan PHP 8.3.36 dengan `.local/php/conf.d`; konfigurasi lokal diabaikan Git.
- Toolchain terverifikasi: PHP 8.3.36, Composer 2.8.12, Node 24.21.0, npm 11.19.0, Git 2.53.0.
- Git memakai safe.directory per proses karena kepemilikan `.git` berbeda; konfigurasi global tidak diubah.
- Repository awal tidak memiliki artefak Next.js/Node backend/Prisma.

