# APIKAWAL — Progress

## Checklist fase (stack Laravel + Blade + Tailwind + MySQL)
- [ ] Fase 0 — Fondasi Laravel, Breeze Blade, lokal id, layout, Geo, `composer check`
- [ ] Fase 1 — Auth dan peran
- [ ] Fase 2 — Lapor, foto, duplikat, dan rate limit
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
- Commit Fase 0: menunggu.

## Fase berikutnya
- [ ] Fase 1 — Auth dan peran
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
