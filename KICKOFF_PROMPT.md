# Prompt Kickoff untuk Codex (Versi Laravel)

Pastikan `AGENTS.md` (versi Laravel yang baru) sudah menggantikan yang lama di root folder proyek, dan Codex sudah dalam mode yang boleh menulis file dan menjalankan perintah.

---

## Prompt 1 — Mulai / ulang dari awal dengan Laravel (tempel ini)

```
Ada perubahan stack. Baca ulang AGENTS.md di root proyek ini sampai habis: AGENTS.md sudah diganti dan sekarang stack-nya LARAVEL (PHP) + Blade + Tailwind + MySQL, BUKAN Next.js. Abaikan keputusan stack sebelumnya. Jika ada file sisa dari pendekatan lama (mis. Next.js/Node/Prisma), hapus dan catat.

Ini spesifikasi lengkap proyek APIKAWAL untuk lomba Web Development UINIC 8.0 dengan deadline sangat ketat (sekitar 24 jam).

Langkah awal:
1. Jalankan php -v, composer -V, node -v, npm -v, git --version dan laporkan. Jika PHP atau Composer belum terpasang, berhenti dan beri instruksi pemasangan singkat untuk Windows.
2. Perbarui docs/PROGRESS.md agar sesuai rencana fase di AGENTS.md bagian 13 (versi Laravel).
3. Mulai dari Fase 0. Perhatikan cara membuat proyek Laravel di folder yang tidak kosong (AGENTS.md bagian 2): jangan menimpa AGENTS.md, KICKOFF_PROMPT.md, docs/, atau .git.

Tugasmu: bangun seluruh aplikasi sampai selesai mengikuti AGENTS.md, secara mandiri dan berurutan dari Fase 0 sampai Fase 8, tanpa berhenti untuk bertanya kecuali benar-benar buntu. Jika ada hal yang tidak jelas, ambil keputusan paling aman dan sederhana, lalu catat di docs/ASSUMPTIONS.md.

Aturan:
1. Setelah setiap fase: jalankan composer check (atau minimal php artisan test + npm run build), perbaiki semua error, lalu git commit. Jangan lanjut dengan build/test yang rusak.
2. Tulis file lengkap, bukan potongan parsial.
3. Perbarui docs/PROGRESS.md di akhir tiap fase (selesai, tersisa, masalah).
4. Jangan mengklaim fitur yang belum ada. Jika waktu/konteks menipis, ikuti urutan pemotongan di bagian 13 dan jangan memotong item "tidak boleh dipotong".
5. Jika perintah gagal karena sandbox, izin, atau jaringan, jelaskan perintahnya dan alasannya, jangan dilewati diam-diam.
6. Di akhir, beri ringkasan: fitur terimplementasi, yang dipotong, langkah manual untuk tim, dan hasil composer check.

Mulai sekarang dari Fase 0.
```

---

## Prompt 2 — Jika sesi terputus / konteks penuh / kena limit

```
Lanjutkan pekerjaan proyek APIKAWAL (Laravel). Baca AGENTS.md dan docs/PROGRESS.md untuk tahu posisi terakhir, lalu cek git log dan jalankan composer check untuk melihat kondisi sebenarnya. Lanjutkan dari fase yang belum selesai sesuai AGENTS.md. Jangan mengulang pekerjaan yang sudah beres. Perbarui docs/PROGRESS.md dan commit setiap fase.
```

## Prompt 3 — Kalau mau satu fase per prompt (lebih terkontrol)

```
Kerjakan HANYA Fase [NOMOR] dari AGENTS.md bagian 13 (Laravel). Setelah selesai: jalankan composer check, perbaiki error, git commit, perbarui docs/PROGRESS.md, lalu berhenti dan laporkan apa yang selesai, apa yang bermasalah, dan apa yang perlu kuuji manual.
```

## Prompt 4 — Audit akhir

```
Lakukan audit akhir terhadap Definition of Done di AGENTS.md (bagian 15). Untuk setiap butir, verifikasi benar-benar berfungsi (jalankan aplikasi dan uji alurnya, jangan hanya membaca kode). Daftar butir yang gagal atau belum lengkap, lalu perbaiki. Pastikan juga: kontras warna dan label form, tampilan di lebar 360px, tidak ada secret di git, role tidak bisa diubah lewat mass-assignment, README dan docs/PROPOSAL_NOTES.md akurat dan tidak melebih-lebihkan fitur.
```

## Prompt 5 — Bahan proposal

```
Berdasarkan kode yang sudah ada (bukan asumsi), perbarui docs/PROPOSAL_NOTES.md dan docs/SCREENSHOTS.md. Pastikan daftar fitur hanya berisi yang benar-benar berjalan, rumus indeks prioritas sesuai kode, dan diagram Mermaid di docs/ sesuai skema migrasi serta state machine yang sebenarnya.
```
