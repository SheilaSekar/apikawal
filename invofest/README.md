# APIKAWAL · Prototipe INVOFEST 2026

APIKAWAL memperagakan cara laporan lapangan, pemeriksaan komunitas, indeks triase, pemetaan, dan koordinasi dapat terhubung untuk mendukung kesiapsiagaan risiko kebakaran lahan.

## Batasan yang perlu dibaca

- APIKAWAL **bukan pendeteksi api otomatis** dan **bukan pengganti layanan darurat**. Jika kebakaran mengancam jiwa, hubungi 112 atau BPBD/pemadam kebakaran setempat.
- Indeks prioritas adalah heuristik awal untuk mengurutkan antrean, bukan ukuran akurat tingkat bahaya dan belum dikalibrasi bersama pakar.
- Peran petugas dan seluruh laporan, hotspot, POI, reputasi, serta perubahan status demo disimulasikan. Prototipe ini tidak terhubung dengan instansi dan tidak mengirim data ke server.
- Data tersimpan di localStorage browser pada perangkat ini. Pemilih peran adalah demonstrasi antarmuka, bukan autentikasi atau pengamanan akses.

## Menjalankan

Tidak ada bundler, build step, maupun dependency install. Buka folder `invofest/` dengan ekstensi server statis lokal seperti Live Server, atau sajikan dengan HTTP static server yang tersedia. Buka halaman `index.html` melalui alamat lokal tersebut. Module JavaScript dan Leaflet membutuhkan HTTP; `file://` dapat membatasi module browser.

Pustaka Leaflet dan Chart.js disimpan lokal bersama lisensinya. Peta memakai tile OpenStreetMap saat online; jika tile tidak tersedia, daftar laporan tetap bisa digunakan. Atribusi OSM tampil di peta.

Reset seluruh state demo melalui **Admin → Reset semua data demo**. Ekspor JSON mengunduh salinan state di browser. Jangan memasukkan informasi pribadi atau informasi kejadian nyata.

## Menguji

Jalankan dari root repository:

```sh
node --test invofest/tests/*.test.js
node invofest/tools/check-static.mjs
```

Tes mencakup geospasial, duplikat, bobot suara, aturan indeks, transisi status, seed dan penyimpanan alur laporan. Pemeriksaan statis memeriksa shell, fallback darurat, bahasa dokumen, dan asset lokal.

## Halaman

- `index.html` — ringkasan demo dan laporan terbaru
- `ekosistem.html` — visualisasi alur kolaborasi
- `peta.html` — filter, layer laporan/hotspot/grid, dan daftar alternatif
- `lapor.html`, `laporan.html?kode=...` — kirim laporan lokal dan tinjau status/skor
- `dashboard.html`, `petugas.html`, `admin.html` — tampilan peran simulasi
- `analitik.html`, `tentang.html`, `404.html`

## Catatan lomba

Deskripsi karya final, nama anggota, lembar orisinalitas, URL repository publik, dan tautan video publik (3–10 menit, minimal 720p) harus diisi tim. Informasi tersebut sengaja tidak dikarang di prototipe. Lihat `../docs/invofest/ASSUMPTIONS.md` dan `../docs/PROGRESS_INVOFEST.md` untuk asumsi dan catatan penerimaan.
