# Deploy APIKAWAL

Layanan gratis, kuota, buildpack, persistence, dan ketentuan penggunaan sering berubah. Tim harus memeriksa ketentuan terbaru sebelum memilih hosting dan mencoba deploy **hello world** lebih dahulu. Deployment belum dilakukan karena belum ada kredensial/akun hosting.

## Jalur A — container

Gunakan `Dockerfile` (PHP 8.3 Apache; build aset terpisah) pada platform container yang mendukung Docker dan menyediakan MySQL kompatibel. Set environment:

- `APP_NAME=APIKAWAL`, `APP_ENV=production`, `APP_KEY` (buat dengan `php artisan key:generate --show`), `APP_DEBUG=false`, `APP_URL=https://domain`.
- `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
- `SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=sync`, `LOG_CHANNEL=stderr`.
- `SESSION_SECURE_COOKIE=true`, `SEED_DEMO=true` hanya jika demo publik memang diperlukan.
- `TRUSTED_PROXIES` diisi alamat/CIDR ingress proxy platform yang benar; default proyek hanya `127.0.0.1`, bukan wildcard.

Entrypoint menjalankan `php artisan migrate --force`, cache config/route/view, lalu Apache. Migrasi berjalan saat container start; pastikan DB tersedia dan lakukan backup sebelum update schema.

Seed database produksi sekali dari shell platform atau lingkungan lokal yang `.env`-nya sementara diarahkan ke DB produksi:

```bash
php artisan migrate --force && php artisan db:seed --force
```

Jangan meninggalkan kredensial DB produksi di repo atau file lokal yang dibagikan.

## Jalur B — shared hosting PHP + MySQL

Arahkan document root ke folder `public/`. Bila panel tidak menyediakan pilihan itu, minta dukungan hosting; hindari memindahkan file rahasia ke public. Jika SSH tidak tersedia, build lokal `composer install --no-dev --optimize-autoloader` dan `npm ci && npm run build`, lalu unggah source, `vendor/`, dan `public/build/`. Atur `.env` production (`APP_DEBUG=false`, HTTPS, koneksi DB, session/cache database), impor skema dan data lewat migrasi SSH atau phpMyAdmin.

Buat cadangan SQL setelah migrasi dan seed:

```bash
mysqldump --single-transaction --default-character-set=utf8mb4 -h DB_HOST -u DB_USER -p DB_NAME > database/apikawal_demo.sql
```

Simpan dump dengan aman, jangan commit jika memuat data/kredensial sensitif.

## Checklist sebelum lomba

- [ ] Link demo dapat dibuka dari HP dan jaringan lain.
- [ ] Repository public dapat dibuka dalam mode incognito.
- [ ] Akun demo berfungsi; password diganti bila diperlukan.
- [ ] README, dokumentasi, dan proposal selesai (maksimal 30 halaman sesuai ketentuan).
- [ ] Nama file mengikuti aturan lomba.
- [ ] Ganti logo/nama tim placeholder.
- [ ] Tinjau ketentuan tile OSM dan lisensi sumber hotspot.
- [ ] Uji register → laporan → verifikasi → respons → selesai pada deployment.
