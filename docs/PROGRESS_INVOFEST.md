# APIKAWAL INVOFEST — Progress

Branch kerja: `invofest` (lokal, jangan push). Tenggat: 7 November 2026.

## Tahapan

- [x] T0 — Baca brief dan audit sumber; requirements, assumptions, dan port logika dicatat di `docs/invofest/`.
- [x] T1 — Fondasi frontend statis, design system, shell dan halaman, dependensi lokal.
- [x] T2 — Domain JS murni dan unit test `node --test`.
- [ ] T3 — Store, seed deterministik, pelaporan, duplikat, detail/verifikasi/status.
- [ ] T4 — Peta, dashboard, petugas, admin, analitik, ekosistem, tentang.
- [ ] T5 — Aksesibilitas, responsif, state, 404, dan QA tertulis.
- [ ] T6 — README kompetisi, deskripsi karya, skrip video, checklist submit.

## Catatan kerja

- Sumber bisnis dibaca: `AGENTS.md`, `KICKOFF_PROMPT.md`, `README.md`, `docs/PROGRESS.md`, `app/Domain/`, `app/Services/`, `config/apikawal.php`, `database/seeders/`, dan konten `resources/views/`.
- Stack lama dan deadline UINIC hanya berlaku untuk Laravel root. Brief INVOFEST mengatur aplikasi baru dalam `invofest/`; jangan ubah Laravel root.
- Toolchain terdeteksi: PHP 8.3.36, Composer 2.8.12, Node 24.21.0, npm 11.19.0 (gunakan `npm.cmd` di PowerShell), Git 2.53.0.
- [ ] Hasil `node --test` dan build/vendor lokal akan dicatat per tahap setelah implementasi.
- [x] Vendored Leaflet 1.9.4 dan Chart.js 4.6.1 dari `node_modules` yang telah tersedia; lisensi disertakan, tidak mengakses CDN.
- [x] T1 memuat 11 halaman HTML, palet dan komponen CSS, banner darurat, serta pemilih peran simulasi.
- [x] T2 mengimplementasikan geospasial, duplikat, evaluasi suara, skor prioritas, dan state machine di JS murni.
- [x] `node --test invofest/tests/domain.test.js`: 5 tes lulus (dijalankan ulang setelah satu ekspektasi transisi tes diperbaiki).
- [ ] QA browser 360/768/1280 dan video/proposal masih memerlukan tindakan tim.
