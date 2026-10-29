# Asumsi dan Keputusan Implementasi

- Proyek memakai Laravel 13.39.0 (rilis stabil yang dapat dipasang dengan PHP 8.3.36) dan Laravel Breeze 2.4.2 untuk autentikasi Blade.
- Pengembangan lokal dikonfigurasi ke SQLite agar tidak bergantung pada layanan MySQL lokal. Deploy tetap ditargetkan ke MySQL/MariaDB; migrasi dan query aplikasi dijaga DB-agnostic.
- PHP 8.3 menyediakan `php_pdo_sqlite.dll`, tetapi ekstensi tidak aktif pada php.ini Laragon. Agar konfigurasi mesin tidak diubah, proyek menaruh `extension=sqlite3` dan `extension=pdo_sqlite` di `.local/php/conf.d/99-apikawal-sqlite.ini`, mengabaikan `.local/` di Git, dan menjalankan command dengan `PHP_INI_SCAN_DIR` menunjuk direktori tersebut.
- Jika menggunakan terminal baru, aktifkan PHP 8.3 pada PATH dan set `PHP_INI_SCAN_DIR` ke `.local/php/conf.d` sebelum menjalankan Artisan/test.
- Nama anggota dan logo lomba tetap memakai slot yang dapat diisi tim. Tidak ada aset eksternal berhak cipta yang ditambahkan.
- Dependensi utama: Laravel Framework (routing, ORM, validasi, autentikasi/session, cache, rate limit), Breeze (scaffold auth Blade), Tailwind CSS/Vite/Alpine (UI responsif dan interaksi ringan), Leaflet (peta), Chart.js (grafik), Intervention Image (olah foto), Pint (format kode).
- Git menandai workspace sebagai kepemilikan berbeda dari user proses. Perintah Git menggunakan `-c safe.directory=<workspace>` hanya pada pemanggilan terkait; konfigurasi global tidak diubah.
