# APIKAWAL — Progress

## Checklist fase (stack Laravel + Blade + Tailwind + MySQL)
- [x] Fase 0 — Fondasi Laravel, Breeze Blade, locale id, layout, Geo, composer check (commit `3e0935b`)
- [x] Fase 1 — Auth dan peran (commit `a8867a4`)
- [x] Fase 2 — Lapor, foto, duplikat, dan rate limit (commit `462f6bd`)
- [x] Fase 3 — Verifikasi, prioritas, state machine, dan test (commit `730f238`)
- [x] Fase 4 — Peta, antrean petugas, dashboard, dan landing (commit `0b8be9b`)
- [x] Fase 5 — Admin (composer check lulus)
- [ ] Fase 6 — Analitik dan API
- [ ] Fase 7 — Seed dan dokumentasi
- [ ] Fase 8 — Polishing, keamanan, deploy, dan penutupan

## Fase 0 — Fondasi
- [x] Scaffold Laravel 13.39.0 + Breeze 2.4.2 Blade tanpa menimpa file pelindung.
- [x] PHP 8.3.36 didahulukan pada PATH; ekstensi SQLite diaktifkan untuk proses proyek melalui `.local/php/conf.d` (diabaikan Git).
- [x] `.env.example`, timezone Asia/Jakarta, locale Indonesia, config bisnis, enum, `Geo`, unit test, layout Blade responsif, banner darurat, skip link, footer, komponen dasar.
- [x] Script `composer check` (Pint, test, Vite production build).
- Validasi: lulus; 28 test (66 assertions), Vite build lulus.
- Commit: `3e0935b` (`feat: bootstrap Laravel foundations`).

## Fase 1 — Auth dan peran
- [x] Registrasi publik sebagai warga, route masuk/daftar lokal, throttle Breeze, pemeriksaan user aktif, middleware role, UserPolicy, notifikasi, dashboard awal, route petugas/admin terlindungi.
- Validasi: lulus; 33 test (79 assertions), Vite build lulus.
- Commit: `a8867a4` (`feat: add role-aware authentication`).

## Fase 2 — Pelaporan
- [x] Laporan/foto disimpan di DB; MIME nyata diperiksa dan gambar di-encode ulang ke JPEG maks. 1280 px; lokasi via pin/geolokasi; cek duplikat; dukungan otomatis untuk duplikat; kode AK; route foto cache; rate limit 5 laporan/jam.
- Validasi: lulus; 40 test (103 assertions), Vite build lulus.
- Commit: `462f6bd` (`feat: add field reporting and duplicate support`).

## Fase 3 — Verifikasi, prioritas, state machine
- [x] Evaluator verifikasi berbobot, kalkulator prioritas dengan rincian faktor dan cap 59 untuk laporan baru, alur status tervalidasi, response log/notifikasi atomik, serta laporan penyalahgunaan.
- [x] Detail laporan menyediakan panel skor, timeline, pemungutan suara, pelaporan penyalahgunaan, dan kontrol status petugas.
- Validasi: `composer check` lulus — Pint passed; 47 test (135 assertions); Vite production build lulus.
- Catatan: `PointOfInterest` memetakan eksplisit ke tabel migrasi `points_of_interest`.
- Commit: menunggu.

## Fase berikutnya
- [x] Fase 4 — Peta, antrean petugas, dashboard, dan landing (commit `0b8be9b`)
- [x] Fase 5 — Admin (composer check lulus)
- [ ] Fase 6 — Analitik dan API
- [ ] Fase 7 — Seed dan dokumentasi
- [ ] Fase 8 — Polishing, keamanan, deploy, dan penutupan

## Catatan kondisi
- AGENTS.md dibaca ulang; stack Laravel/PHP, Blade, Tailwind, Vite, MySQL dengan fallback SQLite.
- Toolchain: PHP 8.3.36, Composer 2.8.12, Node 24.21.0, npm 11.19.0, Git 2.53.0.
- Pemeriksaan PHP menggunakan PHP 8.3 melalui PATH dan konfigurasi ekstensi lokal.
- Git memerlukan safe.directory per proses karena metadata `.git` dimiliki akun sandbox; konfigurasi global tidak diubah.
- Repo awal tidak memiliki artefak Next.js/Node/Prisma.
## Fase 4 — Peta, landing, dasbor, antrean
- [x] Landing menampilkan KPI, alur kerja, laporan terbaru, tautan lapor/peta, dan batasan produk.
- [x] Peta responsif memakai Leaflet dari bundle Vite, filter, simbol status + warna prioritas, layer hotspot 24 jam opsional, daftar alternatif, dan koordinat publik dibulatkan.
- [x] Dashboard berisi laporan saya, antrian verifikasi, notifikasi yang dapat ditandai dibaca, reputasi dan peran; antrean petugas memakai urutan prioritas/waktu.
- Validasi: `composer check` lulus — Pint passed; 47 test (135 assertions); Vite production build lulus.
- Commit: menunggu.

## Fase 5 — Admin
- [x] Panel pengguna/peran dan status aktif, moderasi laporan, resolusi abuse flag, perubahan/reset bobot dengan jumlah wajib 100, impor CSV hotspot, hitung ulang grid, ekspor laporan CSV.
- Validasi: `composer check` lulus — Pint passed; 49 test (141 assertions); Vite production build lulus.
- Commit: menunggu.
