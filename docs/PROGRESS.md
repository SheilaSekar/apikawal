# APIKAWAL — Progress

## Checklist fase (stack Laravel + Blade + Tailwind + MySQL)
- [x] Fase 0 — Fondasi Laravel, Breeze Blade, locale id, layout, Geo, `composer check` (commit `3e0935b`)
- [x] Fase 1 — Auth dan peran (`composer check` lulus, commit `a8867a4`)
- [x] Fase 2 — Lapor, foto, duplikat, dan rate limit (`composer check` lulus)
- [ ] Fase 3 — Verifikasi, prioritas, state machine, dan test
- [ ] Fase 4 — Peta, antrean petugas, dashboard, dan landing
- [ ] Fase 5 — Admin
- [ ] Fase 6 — Analitik dan API
- [ ] Fase 7 — Seed dan dokumentasi
- [ ] Fase 8 — Polishing, keamanan, deploy, dan penutupan

## Fase 0 — Fondasi
- [x] Scaffold Laravel 13.39.0 + Breeze 2.4.2 Blade di root tanpa menimpa file pelindung.
- [x] PHP 8.3.36 didahulukan pada PATH; ekstensi SQLite diaktifkan untuk proses proyek melalui `.local/php/conf.d` (folder diabaikan Git).
- [x] `.env.example`, timezone `Asia/Jakarta`, locale Indonesia, config bisnis, enum, `Geo` dan unit test.
- [x] Layout Blade responsif, banner darurat, skip link, footer, komponen dasar, slot logo.
- [x] Script `composer check` (Pint, test, Vite production build).
- Validasi: `composer check` lulus — Pint passed; 28 test passed (66 assertions); Vite production build lulus.
- Commit Fase 0: `3e0935b` (`feat: bootstrap Laravel foundations`).

## Fase berikutnya
- [ ] Fase 2 — Lapor, foto, duplikat, dan rate limit
- [ ] Fase 3 — Verifikasi, prioritas, state machine, dan test
- [ ] Fase 4 — Peta, antrean petugas, dashboard, dan landing
- [ ] Fase 5 — Admin
- [ ] Fase 6 — Analitik dan API
- [ ] Fase 7 — Seed dan dokumentasi
- [ ] Fase 8 — Polishing, keamanan, deploy, dan penutupan

## Catatan kondisi
- AGENTS.md dibaca ulang; stack yang berlaku adalah Laravel/PHP, Blade, Tailwind, Vite, dan MySQL dengan fallback SQLite.
- Pemeriksaan toolchain: PHP 8.3.36, Composer 2.8.12, Node 24.21.0, npm 11.19.0, Git 2.53.0.
- Git memerlukan `safe.directory` per perintah karena metadata `.git` dimiliki user sandbox; konfigurasi global tidak diubah.
- Repo awal tidak memiliki artefak Next.js/Node/Prisma.
- Fase 1 selesai: registrasi publik hanya mendapat role warga dari default DB; URL `/masuk` dan `/daftar`; Breeze login throttle dan pesan generik; user aktif diperiksa saat login dan route privat; middleware role; UserPolicy + otorisasi endpoint profil; UserNotification; dashboard awal; halaman `/petugas` dan `/admin` dilindungi role.
- Validasi Fase 1: `composer check` lulus — Pint passed; 33 test passed (79 assertions); Vite production build lulus.
- Komit Fase 1: `a8867a4` (`feat: add role-aware authentication`).
- Fase 2 selesai: laporan dan foto disimpan di DB, MIME diperiksa dengan finfo/getimagesize, gambar di-encode ulang ke JPEG maksimal 1280 px, peta pin/geolokasi, cek duplikat, dukungan otomatis untuk duplikat, kode AK, route foto cache, dan rate limit 5 laporan/jam.
- Validasi Fase 2: `composer check` lulus — Pint passed; 40 test passed (103 assertions); Vite production build lulus.
- Komit Fase 2: menunggu.
