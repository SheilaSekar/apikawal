# Kebutuhan INVOFEST 2026 — APIKAWAL

## Kompetisi

- INVOFEST 2026 Web Design Competition, tema **Smart Ecosystems: Digital Solutions for a Sustainable Future**.
- Subtema pilihan: **Green Tech & Sustainable Environment**, dengan fokus SDG 12 dan SDG 13.
- Penilaian: desain 30%, kreativitas/inovasi 25%, kemudahan dan responsibilitas 20%, orisinalitas 15%, kesesuaian tema 10%.
- Tenggat karya: **7 November 2026**. Antarmuka dan demo menjadi prioritas tertinggi.
- Luaran yang perlu disiapkan tim: tautan repo, deskripsi karya dalam template panitia, video publik 3–10 menit minimal 720p, dan lembar orisinalitas. Identitas/orisinalitas tidak boleh dikarang; gunakan placeholder sampai tim mengisinya.

## Batas teknis dan pemisahan aplikasi

- Aplikasi kompetisi baru berada di `invofest/` dan menggunakan HTML, CSS, JavaScript vanilla ES modules. Tanpa PHP/Blade/Laravel/Tailwind/framework UI, CMS, bundler, atau build step.
- Library lokal yang diizinkan hanya Leaflet dan Chart.js. Salin paket beserta lisensinya ke `invofest/assets/vendor/`; jangan memakai CDN. Peta membutuhkan tile OpenStreetMap saat tersambung internet; mode daftar dan semua alur lain tetap bisa dipakai tanpa tile.
- Jangan mengubah aplikasi Laravel di root. Laravel tetap menjadi versi dengan autentikasi nyata; versi INVOFEST menggunakan localStorage dan pemilih peran simulasi, bukan login.
- Gunakan branch Git lokal `invofest`. Setiap tahap di-commit; jangan push.
- Semua UI berbahasa Indonesia, `<html lang="id">`, tanpa penyebutan UINIC.

## Data dan logika

- State browser disimpan pada satu kunci localStorage berversi dan dimulai dari JSON seed deterministik. Ada kontrol reset data demo.
- Semua laporan, hotspot, POI, data contoh, dan foto placeholder diberi label **DATA SIMULASI** / **FOTO SIMULASI**.
- Port logika: Haversine/bounding box, duplikat 300 m/6 jam, suara berbobot dan evaluator, enam faktor indeks + rincian + cap 59, state machine + log, reputasi/moderasi, dan batas 5 laporan/jam. Bobot berasal dari satu konfigurasi JS.
- Test domain dijalankan dengan `node --test`, tanpa dependensi.
- Seed generator memakai RNG ber-seed dan menghasilkan sekitar 30 laporan dengan semua status/timeline, 300 hotspot sintetis, 30 POI, dan grid 0,05°. Timestamp seed disimpan sebagai offset relatif terhadap waktu saat data dimuat.
- Foto unggahan dibatasi 3 dan diproses menjadi JPEG maksimal 800 px lewat canvas. Kegagalan kuota localStorage harus menghasilkan pesan yang jelas.

## Halaman wajib

- `index.html`: hero, empat langkah, KPI, peta ringkas, laporan terbaru.
- `ekosistem.html`: diagram SVG interaktif warga → relawan → petugas/data satelit/analitik → wilayah rawan, dengan penjelasan dampak SDG.
- `peta.html`: filter, layer, daftar alternatif, simbol status non-warna, koordinat presisi hanya pada mode Petugas/Admin.
- `lapor.html` dan `laporan.html?kode=...`: buat laporan, cek duplikat, detail skor, verifikasi, timeline, flag, dan kontrol petugas/admin.
- `dashboard.html`, `petugas.html`, `admin.html`, `analitik.html`, `tentang.html`, `404.html`.
- Banner 112 permanen, batasan produk, metadata, favicon SVG, dan shell bersama yang dirender `assets/js/ui.js`. Sediakan fallback `<noscript>`.

## Desain, aksesibilitas, dan penerimaan

- Identitas visual orisinal dengan token CSS; tidak memakai template siap pakai. Komponen konsisten untuk tombol, kartu, status/prioritas, form, tabel, modal, toast, dan state kosong/loading/error.
- WCAG AA, skip link, fokus terlihat, label form, `aria-live` untuk error, heading berurutan, alt text, target sentuh minimal 44 px, reduced motion, dan alternatif daftar peta.
- Ukuran uji: 360, 768, 1280 px; tidak boleh ada scroll horizontal.
- Dilarang mengklaim deteksi otomatis atau AI. APIKAWAL bukan layanan darurat; skor adalah heuristik triase; peran petugas simulasi; data demo sintetis.
- Jika waktu tidak cukup, pangkas berurutan: diagram ekosistem interaktif (boleh statis), ekspor CSV, tema gelap, animasi. Fitur inti yang tidak boleh dipangkas tercantum pada brief dan dipetakan di `LOGIC_PORTS.md`.
