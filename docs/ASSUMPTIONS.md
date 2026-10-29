# Asumsi dan Keputusan Implementasi

- Proyek memakai Laravel 13.39.0 dan Laravel Breeze 2.4.2 untuk autentikasi Blade.
- Lokal menggunakan SQLite agar tidak tergantung layanan MySQL; deployment target MySQL/MariaDB.
- PHP 8.3.36 Laragon menyediakan DLL `pdo_sqlite`, tetapi belum aktif pada konfigurasi normal. Untuk tidak mengubah instalasi Laragon, `.local/php/conf.d/99-apikawal-sqlite.ini` (diabaikan Git) menyalakan sqlite untuk proses pemeriksaan.
- Terminal baru perlu menambahkan PHP 8.3 ke `PATH` dan mengatur `PHP_INI_SCAN_DIR` ke `.local/php/conf.d` sebelum menjalankan Composer/Artisan.
- `SEED_DEMO=true` mengaktifkan akun + data demonstrasi; default tidak membuat akun rahasia.
- Demo memakai koordinat Sumatera Selatan perkiraan bbox, bukan batas administratif.
- Identitas publik dihapus dan koordinat dibulatkan ke tiga desimal. Verifikasi jarak tidak diwajibkan karena geolokasi dapat dipalsukan.
- Update bobot tidak menulis audit log tersendiri; perubahan bobot dibatasi admin dan total 100.
- Data hotspot sintetis bukan pengamatan lapangan. Sumber nyata harus ditinjau lisensinya.
- CSP memberi `'unsafe-eval'` pada `script-src` karena runtime Alpine.js mengevaluasi ekspresi `x-data`/`x-on`; ini memperlemah mitigasi injeksi skrip. Jangan memasukkan HTML tidak tepercaya ke direktif Alpine, dan evaluasi migrasi ke komponen CSP-safe bila kebijakan hosting mengharuskan CSP ketat. `style-src 'unsafe-inline'` diperlukan untuk Leaflet dan style peta dinamis.
- Wildcard trust proxy tidak digunakan. `.env` menyediakan `TRUSTED_PROXIES` dengan default `127.0.0.1`; tim harus mengatur IP/CIDR proxy ingress hosting sebelum mengandalkan forwarded HTTPS/IP header.
- Logo UINIC dan nama tim memakai placeholder; tim wajib mengganti sebelum submission.
- Dependensi utama: Laravel untuk routing/ORM/session/validasi/cache; Breeze untuk auth; Intervention Image 3 untuk re-encode; Tailwind/Alpine/Vite untuk UI; Leaflet untuk peta dan OSM tiles; Chart.js untuk grafik; Pint untuk format PHP; PHPUnit untuk tes.
- Git memakai safe.directory hanya dalam proses command karena kepemilikan folder `.git` sandbox berbeda; konfigurasi global tidak diubah.
