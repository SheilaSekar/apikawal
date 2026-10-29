# Deploy APIKAWAL

Repository ini adalah aplikasi Laravel/PHP. Jalur container Docker tersedia untuk Render dan platform serupa; jalur Vercel juga tersedia memakai container FrankenPHP. Jangan deploy sebagai proyek Vite statis: Vite hanya membangun aset ke `public/build`, sedangkan Laravel membutuhkan runtime PHP dan database. Mengatur Vercel Output Directory ke `public` hanya menyajikan file statis dan tidak menjalankan Laravel.

Periksa ketentuan dan kuota terbaru setiap platform sebelum memilih paket. Deployment publik belum dilakukan dari workspace ini.

## Jalur A — container Docker

Dockerfile memakai PHP 8.3 + Apache dan membangun aset Vite di stage Node terpisah. Apache mendengarkan `PORT` dari platform (default `80` untuk lokal). Entrypoint membuat ulang direktori Laravel yang mungkin kosong karena `.dockerignore`, mengatur ownership `www-data`, mencoba migrasi dengan retry terbatas, menyimpan cache Laravel, lalu menjalankan Apache.

Untuk Render, ikuti langkah demi langkah pada bagian berikut. Railway, Fly.io, Koyeb, dan platform container lain juga dapat menjalankan Dockerfile ini selama platform menyediakan MySQL yang dapat dijangkau, variabel `PORT`, dan health check HTTP.

Environment yang dibutuhkan untuk produksi:

- `APP_NAME=APIKAWAL`, `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY`, `APP_URL`.
- `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
- `SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=sync`, `LOG_CHANNEL=stderr`.
- `SESSION_SECURE_COOKIE=true`, `SEED_DEMO=false`, `SEED_ON_BOOT=false`.
- `TRUSTED_PROXIES` sesuai ingress proxy platform; default aplikasi tetap `127.0.0.1`.
- `APP_TIMEZONE=Asia/Jakarta`, `APP_LOCALE=id`, `APP_FALLBACK_LOCALE=id`, `APP_FAKER_LOCALE=id_ID`.

Container menjalankan `php artisan migrate --force` saat startup dan berhenti dengan pesan jelas bila migrasi tetap gagal setelah lima percobaan, masing-masing hingga 60 detik. Seeder **tidak** berjalan otomatis kecuali `SEED_ON_BOOT=true`. Jangan memakai SQLite di production karena filesystem container bersifat ephemeral.

### Deploy ke Render (langkah demi langkah)

1. Buat database MySQL eksternal pada penyedia yang mendukung koneksi dari Render, misalnya Aiven, TiDB Cloud, Railway MySQL, atau layanan MySQL sejenis. Render tidak menyediakan database MySQL managed. Catat host, port, nama database, username, dan password dari dashboard penyedia database. Pastikan aturan jaringan database mengizinkan koneksi dari service Render.
2. Buat Blueprint dari repository ini atau buat Web Service baru dan pilih runtime **Docker**. Root directory harus root repository, yaitu direktori yang berisi `artisan`, `composer.json`, dan `Dockerfile`. `render.yaml` mendefinisikan service Docker dan health check `/api/health`. Pastikan `plan: free` tetap ada; Blueprint tanpa plan ini dapat memakai plan Starter berbayar dan meminta kartu pembayaran. Region service ditetapkan `singapore`, region Render terdekat untuk pengguna Indonesia. Pilih region database yang sama atau sedekat mungkin dengan Singapore untuk mengurangi latensi. Region service Render tidak bisa diubah setelah dibuat; untuk pindah region, buat service baru dan migrasikan konfigurasi/data.
3. Isi environment di Render. Blueprint memberi nilai untuk pengaturan nonrahasia; isikan nilai yang ditandai `sync: false` pada dashboard:
   - `APP_KEY`: dari menjalankan `php artisan key:generate --show` secara lokal. Masukkan hasilnya langsung ke environment Render; jangan commit atau membagikan nilainya.
   - `APP_URL`: URL HTTPS service Render setelah dibuat, atau domain khusus yang dipasang.
   - `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: detail koneksi yang diberikan penyedia MySQL eksternal.
   - Untuk penyedia yang memakai CA publik, misalnya TiDB Cloud, isi `MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt`. Penyedia dengan CA sendiri, misalnya Aiven, memerlukan file CA tersebut ditambahkan ke image container dan `MYSQL_ATTR_SSL_CA` diarahkan ke lokasinya.
   - `TRUSTED_PROXIES`: gunakan alamat/CIDR proxy tepercaya jika platform menyediakannya. Jika alamat proxy berubah-ubah dan platform memastikan request selalu melewati proxy yang memvalidasi/menimpa `X-Forwarded-*`, `*` dapat dipakai sebagai trade-off. Wildcard mempercayai header forwarding dari semua sumber yang dapat mencapai aplikasi; jika origin dapat diakses langsung atau proxy tidak membersihkan header, klien dapat memalsukan IP/skema/host yang dipercaya aplikasi. Jangan gunakan wildcard tanpa memastikan batas jaringan dan perilaku proxy platform.
4. Biarkan `DB_CONNECTION=mysql`, `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=sync`, `LOG_CHANNEL=stderr`, dan `SESSION_SECURE_COOKIE=true`. Render menyuntikkan `PORT`; jangan menetapkan port service berbeda dari port yang didengarkan aplikasi. Pertahankan `SEED_ON_BOOT=false` untuk startup normal.
5. Deploy service. Entrypoint menunggu DB dengan lima percobaan migrasi, masing-masing dibatasi 60 detik, lalu meng-cache konfigurasi/route/view dan memulai Apache. Periksa log deploy jika migrasi gagal; pastikan firewall, hostname, port, kredensial, dan TLS database sesuai provider.
6. Untuk seed pertama pada free tier, set `SEED_DEMO=true` dan `SEED_ON_BOOT=true`, lalu deploy. Entrypoint menjalankan seeder setelah migrasi. Setelah deploy berhasil dan data demo tersedia, kembalikan **keduanya** ke `false`, lalu deploy ulang agar seeder tidak berjalan lagi saat restart berikutnya. Alternatifnya, dari lokal arahkan `.env` sementara ke database produksi dengan `SEED_DEMO=true`, lalu jalankan `php artisan db:seed --force`; pulihkan `.env` lokal setelah selesai. Seeder bersifat idempoten.
7. Verifikasi setelah deploy:
   - Buka `/` dan `/peta` melalui HTTPS.
   - Buka `/api/health`; pastikan HTTP 200 dan status database `ok`.
   - Uji daftar akun, login, lalu buat laporan dengan foto pada deployment yang menggunakan data uji.
   - Pastikan data tetap ada setelah service restart (database eksternal, bukan SQLite/container disk).

Nilai proxy yang dapat dipercaya berbeda menurut platform dan dapat berubah. Jangan mengubah default kode menjadi wildcard; tentukan nilai dari dokumentasi jaringan/platform dan evaluasi trade-off di atas.

Free Web Service Render akan tidur setelah 15 menit tanpa request masuk dan biasanya perlu sekitar satu menit untuk bangun saat menerima request berikutnya. Ini perilaku normal free tier, bukan kegagalan health check. Render tidak menyediakan perubahan region untuk service yang sudah dibuat; rencanakan `singapore` sebelum membuat Blueprint.

### Jalur C — Vercel (container FrankenPHP)

Konfigurasi ini menggunakan Dockerfile Vercel khusus (`Dockerfile.vercel`) agar tetap memenuhi kebutuhan ekstensi PHP/GD dan menghindari deteksi Vite sebagai situs statis. Vercel mengarahkan request ke container; Caddy/FrankenPHP menyajikan file yang ada di `public/` (termasuk `public/build`, `public/images`, favicon, dan `robots.txt`) dan meneruskan route Laravel ke `public/index.php`. Aset Vite dibangun dengan `npm ci && npm run build` di image build; Composer memasang dependency production. Dockerfile/entrypoint Render tetap terpisah dan tidak diubah oleh jalur ini.

Vercel Function membatasi request dan response body sampai 4.5 MB. Karena itu form laporan membatasi setiap foto menjadi 1 MB, maksimal tiga foto; ukuran gabungan file sekitar 3 MiB menyisakan ruang untuk multipart dan field formulir. File tetap divalidasi dan diolah ulang oleh Laravel, lalu disimpan base64 di MySQL seperti biasa.

Pendekatan ini mengikuti panduan Laravel Vercel yang memakai container FrankenPHP. Runtime komunitas `vercel-php` menyediakan PHP 8.3 dan modul seperti `pdo_mysql`, `mbstring`, dan `fileinfo`, tetapi daftar ekstensi yang dipublikasikannya tidak memuat GD yang dibutuhkan pengolah foto APIKAWAL. Karena itu jalur ini memakai container PHP dengan ekstensi diverifikasi saat image build. Lihat [panduan Laravel + Docker Vercel](https://vercel.com/kb/guide/laravel-php-with-docker), [runtime vercel-community/php](https://github.com/vercel-community/php), dan [batas Vercel Functions](https://vercel.com/docs/functions/limitations).

#### Persiapan dan deploy

1. Buat database MySQL eksternal (bukan SQLite) dan catat host, port, nama database, username, password, serta instruksi TLS dari provider. Pilih region yang dekat dengan Vercel function region yang dipakai. Pastikan provider mengizinkan koneksi dari Vercel dan batasi kredensial database untuk aplikasi.
2. Pastikan Docker tersedia jika akan menguji/build lokal. `vercel.json` memilih `Dockerfile.vercel` dan mengirim semua route ke service `app`; container mendengarkan `PORT` yang diinjeksi Vercel. Build Docker mencakup Composer production install, `npm ci`, dan `npm run build` sehingga tidak perlu commit `public/build`.
3. Buat project dengan **New Project → Import Git Repository**, pilih repository APIKAWAL, atur Root Directory ke root repo (folder `artisan` dan `Dockerfile.vercel`), lalu deploy. Atau gunakan CLI dari root repo: `vercel login`, `vercel link`, atur environment production, kemudian `vercel deploy --prod`. Jangan pilih preset/output directory Vite; konfigurasi container dalam `vercel.json` yang menentukan runtime.
4. Isi environment variables produksi pada dashboard sebelum deployment pertama (atau melalui `vercel env add`). Jangan menaruh nilai rahasia di source, `vercel.json`, atau file environment yang dikomit. Gunakan daftar berikut; `APP_KEY` harus dihasilkan satu kali dengan `php artisan key:generate --show` dan tetap sama pada redeploy.

| Variable | Nilai |
|---|---|
| `APP_NAME` | `APIKAWAL` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | Nilai baru hasil `php artisan key:generate --show` |
| `APP_URL` | URL HTTPS project, misalnya `https://nama-project.vercel.app` |
| `APP_TIMEZONE` | `Asia/Jakarta` |
| `APP_LOCALE` | `id` |
| `APP_FALLBACK_LOCALE` | `id` |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | Host dari database MySQL eksternal |
| `DB_PORT` | Port dari database (biasanya `3306`; gunakan nilai provider) |
| `DB_DATABASE` | Nama database produksi |
| `DB_USERNAME` | Username database aplikasi |
| `DB_PASSWORD` | Password database aplikasi |
| `MYSQL_ATTR_SSL_CA` | `/etc/ssl/certs/ca-certificates.crt` bila provider memakai CA publik; CA privat perlu dipaketkan di image dan gunakan path tersebut |
| `SESSION_DRIVER` | `database` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `sync` |
| `LOG_CHANNEL` | `stderr` |
| `SESSION_SECURE_COOKIE` | `true` |
| `SESSION_HTTP_ONLY` | `true` |
| `SESSION_SAME_SITE` | `lax` |
| `SEED_DEMO` | `false` untuk produksi normal |
| `SEED_ON_BOOT` | `false`; container tidak menjalankan migrasi/seed otomatis |
| `TRUSTED_PROXIES` | `*` untuk deployment Vercel di mana Vercel menjadi satu-satunya ingress; lihat catatan keamanan di bawah |

Biarkan `SESSION_DOMAIN` tidak disetel agar cookie host-only untuk domain `*.vercel.app` atau domain kustom yang dipakai. `SESSION_SECURE_COOKIE=true`, `APP_URL` HTTPS, dan proxy forwarding yang dipercaya membuat cookie secure, redirect, URL form, CSRF, dan Vite asset URLs konsisten pada HTTPS. Jangan pakai skema `http://` untuk `APP_URL` production.

Vercel mendokumentasikan `x-forwarded-proto` untuk skema dan menyatakan bahwa Vercel menimpa `X-Forwarded-For` untuk mencegah spoofing ([header request Vercel](https://vercel.com/docs/headers/request-headers)). Dokumentasi publik yang diperiksa tidak memberikan daftar CIDR proxy khusus yang stabil untuk diisi satu per satu. Karena itu contoh di atas mempercayai proxy forwarding dari semua peer (`*`) dengan asumsi container hanya menerima trafik melalui ingress Vercel. Ini trade-off keamanan: jika aplikasi/container nantinya bisa dicapai langsung atau diletakkan di platform lain, klien berpotensi memalsukan header forwarding yang dipercaya. Dalam kondisi itu, batasi `TRUSTED_PROXIES` ke CIDR proxy aktual yang dijamin platform tersebut atau jangan gunakan wildcard; jangan menyalin setelan Vercel ke deployment lain tanpa audit.

#### Migrasi dan seed database

Vercel tidak menjalankan `docker/entrypoint.sh`, dan migrasi tidak dipanggil otomatis saat startup karena beberapa instance dapat hidup bersamaan. Jalankan migrasi satu kali dari checkout lokal yang dipercaya setelah mengatur environment lokal sementara ke DB produksi (atau jalankan dari CI/release runner dengan secret manager):

```bash
php artisan migrate --force
```

Jangan commit atau membagikan `.env` sementara. Simpan salinan konfigurasi lokal sebelum mengganti nilai, pulihkan segera setelah migrasi, dan jangan jalankan `migrate:fresh` pada database produksi. Jika perlu data demo, set `SEED_DEMO=true` hanya untuk sesi seed dan jalankan:

```bash
php artisan db:seed --force
```

Kembalikan `SEED_DEMO=false` sesudahnya. Seed idempoten, tetapi akun/konten demonstrasi tetap merupakan data simulasi dan akun demo publik harus dipertimbangkan risikonya.

Untuk TLS database, gunakan `MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt` bila penyedia menggunakan CA publik dan sertifikat sistem di image mencukupi (misalnya TiDB Cloud). Jika provider memberikan CA privat (misalnya Aiven), tambahkan file CA yang sesuai ke `Dockerfile.vercel`/image dengan path tetap dan set `MYSQL_ATTR_SSL_CA` ke path itu. Jangan memasukkan sertifikat privat atau kredensial ke repo.

#### Filesystem, cache, dan operasional

Container Vercel tidak menyediakan disk durable; request runtime hanya memakai temporary storage untuk kebutuhan framework. Entrypoint memindahkan storage Laravel, compiled Blade views, cache manifest framework, dan direktori data/config Caddy ke `/tmp/apikawal`, lalu mengirim log ke stderr. File laporan tetap berada di MySQL, bukan disk. Jangan mengubah penyimpanan tersebut menjadi SQLite atau file lokal.

Konfigurasi dan route cache tidak dibuat saat build atau startup karena environment production disuntikkan saat runtime; `config:cache` akan membekukan nilai environment saat cache dibuat. Blade view cache berjalan di `/tmp`; package/service cache yang perlu dibuat saat boot juga diarahkan ke `/tmp`. Build tidak menjalankan migrasi atau seed.

#### Verifikasi setelah deploy

- Buka `/` dan `/peta`; pastikan Blade tampil dan asset CSS/JS, marker, serta gambar dimuat melalui HTTPS.
- Buka `/api/health`; pastikan HTTP 200 dan pemeriksaan DB menunjukkan status `ok`. `/up` juga tersedia sebagai health route Laravel.
- Daftar akun baru, keluar, login kembali, lalu muat ulang halaman untuk memastikan sesi dan cookie bertahan.
- Buat laporan dengan satu foto ≤1 MB; pastikan detail laporan dan foto dapat dibuka.
- Periksa `/api/v1/reports`, `/api/v1/risk-areas`, dan `/api/v1/stats` mengembalikan respons anonim yang valid.
- Setelah redeploy, pastikan akun, laporan, foto, sesi aktif, dan data database tetap ada; jangan menganggap filesystem sementara sebagai penyimpanan permanen.

Kegagalan umum: HTTP 413 berarti request body melebihi 4.5 MB; HTTP 502 umumnya berarti container tidak bind ke `PORT` atau FrankenPHP gagal boot; redirect HTTP/CSRF/session bermasalah biasanya menunjukkan `APP_URL`, `TRUSTED_PROXIES`, atau secure cookie belum konsisten; error database cek koneksi MySQL, allowlist jaringan, TLS CA, dan migrasi yang dijalankan.

### Menjalankan container lokal

```bash
docker build -t apikawal .
docker run --rm -p 8000:8000 -e PORT=8000 --env-file .env apikawal
```

`.env` lokal harus berisi konfigurasi MySQL yang dapat dijangkau container untuk migrasi startup. File `.env` bersifat lokal dan tidak boleh masuk Git.

## Jalur B — shared hosting PHP + MySQL

Arahkan document root ke folder `public/`. Bila panel tidak menyediakan pilihan itu, minta dukungan hosting; jangan memindahkan file rahasia ke public. Jika SSH tidak tersedia, build lokal `composer install --no-dev --optimize-autoloader` dan `npm ci && npm run build`, lalu unggah source, `vendor/`, dan `public/build/`. Atur `.env` production (`APP_DEBUG=false`, HTTPS, koneksi MySQL, session/cache database), impor skema dan data lewat migrasi SSH atau phpMyAdmin.

Buat cadangan SQL setelah migrasi dan seed:

```bash
mysqldump --single-transaction --default-character-set=utf8mb4 -h DB_HOST -u DB_USER -p DB_NAME > database/apikawal_demo.sql
```

Simpan dump dengan aman; jangan commit jika memuat data sensitif.

## Checklist sebelum lomba

- [ ] Link demo dapat dibuka dari HP dan jaringan lain.
- [ ] Repository public dapat dibuka dalam mode incognito.
- [ ] Akun demo berfungsi; password diganti bila diperlukan.
- [ ] README, dokumentasi, dan proposal selesai (maksimal 30 halaman sesuai ketentuan).
- [ ] Nama file mengikuti aturan lomba.
- [ ] Tambahkan logo kompetisi jika sudah tersedia dan verifikasi nama tim pada halaman Tentang.
- [ ] Tinjau ketentuan tile OSM dan lisensi sumber hotspot.
- [ ] Uji register → laporan → verifikasi → respons → selesai pada deployment.
