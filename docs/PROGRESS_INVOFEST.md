# APIKAWAL INVOFEST — Progress

Branch kerja: `invofest` (lokal, jangan push). Tenggat: 7 November 2026.

## Tahapan

- [x] T0 — Baca brief dan audit sumber; requirements, assumptions, dan port logika dicatat di `docs/invofest/`.
- [ ] T1 — Fondasi frontend statis, design system, shell dan halaman, dependensi lokal.
- [ ] T2 — Domain JS murni dan unit test `node --test`.
- [ ] T3 — Store, seed deterministik, pelaporan, duplikat, detail/verifikasi/status.
- [ ] T4 — Peta, dashboard, petugas, admin, analitik, ekosistem, tentang.
- [ ] T5 — Aksesibilitas, responsif, state, 404, dan QA tertulis.
- [ ] T6 — README kompetisi, deskripsi karya, skrip video, checklist submit.

## Catatan kerja

- Sumber bisnis dibaca: `AGENTS.md`, `KICKOFF_PROMPT.md`, `README.md`, `docs/PROGRESS.md`, `app/Domain/`, `app/Services/`, `config/apikawal.php`, `database/seeders/`, dan konten `resources/views/`.
- Stack lama dan deadline UINIC hanya berlaku untuk Laravel root. Brief INVOFEST mengatur aplikasi baru dalam `invofest/`; jangan ubah Laravel root.
- Toolchain terdeteksi: PHP 8.3.36, Composer 2.8.12, Node 24.21.0, npm 11.19.0 (gunakan `npm.cmd` di PowerShell), Git 2.53.0.
- [ ] Hasil `node --test` dan build/vendor lokal akan dicatat per tahap setelah implementasi.
- [ ] QA browser 360/768/1280 dan video/proposal masih memerlukan tindakan tim.
