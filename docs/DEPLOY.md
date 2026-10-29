# Deploy APIKAWAL

Repository ini adalah aplikasi Laravel/PHP yang dijalankan sebagai container Docker. Jangan deploy sebagai proyek Vite statis di Vercel: Vite hanya membangun aset ke `public/build`, sedangkan aplikasi membutuhkan runtime PHP dan database. Mengatur Vercel Output Directory ke `public` hanya menyajikan file statis dan tidak menjalankan Laravel. Tidak ada `vercel.json` karena Vercel bukan target deployment proyek ini.

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
