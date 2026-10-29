# APIKAWAL

**Sistem Kolaborasi Deteksi Dini, Verifikasi, dan Respons Risiko Kebakaran Lahan Berbasis Informasi Spasial** — prototipe kompetisi UINIC 8.0.

APIKAWAL membantu warga dan relawan melaporkan indikasi kebakaran lahan, mengumpulkan verifikasi berlapis, mengurutkan antrean dengan indeks prioritas transparan, dan mencatat status respons.

> **Peringatan darurat:** Kebakaran sedang berlangsung dan mengancam jiwa? Hubungi 112 atau BPBD/pemadam kebakaran setempat. APIKAWAL adalah alat pelaporan dan koordinasi.

## Batasan produk

- APIKAWAL **bukan pendeteksi api otomatis** dan **bukan pengganti layanan darurat**.
- Indeks prioritas adalah alat triase heuristik awal, bukan ukuran akurat tingkat bahaya.
- Peran petugas instansi disimulasikan; belum terintegrasi resmi dengan BPBD/Manggala Agni.
- Data demo diberi label **DATA SIMULASI**.

## Fitur

- Registrasi warga, dashboard, pengajuan relawan, peran warga/relawan/petugas/admin.
- Form laporan responsif, pin/geolokasi, foto yang diolah ulang, deteksi laporan berdekatan, dan dukungan duplikat.
- Verifikasi berbobot, timeline, state machine penanganan, notifikasi, dan pelaporan penyalahgunaan.
- Peta Leaflet dengan filter dan alternatif daftar; koordinat publik dibulatkan 3 desimal.
- Skor prioritas 0–100 dengan panel faktor dan cap 59 untuk laporan baru.
- Panel admin untuk moderasi, bobot, hotspot CSV, grid, dan ekspor.
- Analitik historis, API publik read-only, health check.

## Stack dan arsitektur

Laravel 13, PHP 8.3+, Blade, Breeze, Tailwind, Alpine, Vite, MySQL/MariaDB produksi, SQLite untuk tes/lokal, Leaflet, Chart.js, Intervention Image. Alur kode: route → Form Request/policy → controller tipis → service → domain/model. Detail ada di [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md).

Aplikasi dirancang stateless di antara request; session/cache database mendukung beberapa instance web, sedangkan file foto disimpan base64 di DB agar tidak bergantung pada disk lokal. Sesuaikan kapasitas dan strategi cache DB saat beban meningkat.

## Prasyarat

- PHP ≥ 8.3 dan ekstensi `mbstring`, `openssl`, `pdo_sqlite`, `pdo_mysql`, `gd`, `fileinfo`, `xml`, `ctype`, `tokenizer`, `json`.
- Composer 2+, Node.js 22+ dan npm.
- MySQL/MariaDB (produksi) atau SQLite (pengembangan lokal).

## Instalasi lokal

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
npm install
```

Atur `.env`: gunakan `DB_CONNECTION=sqlite` (pastikan `database/database.sqlite` tersedia) atau konfigurasi MySQL. `SESSION_DRIVER=database`, `CACHE_STORE=database`, `APP_TIMEZONE=Asia/Jakarta`, `APP_LOCALE=id`.

```powershell
php artisan migrate --seed
npm run build
php artisan serve
```

Untuk akun dan data demonstrasi, tetapkan `SEED_DEMO=true` sebelum menjalankan `php artisan db:seed`. Demo memakai kata sandi `Demo#2026` untuk akun warga, relawan, petugas, dan admin. Ganti/sembunyikan akun demo pada lingkungan publik. Untuk iterasi front-end gunakan `npm run dev`.

## Penggunaan per peran

- **Warga:** daftar, masuk, buat laporan, pantau status, dukung/verifikasi laporan lain, laporkan penyalahgunaan, ajukan menjadi relawan dari profil (jika tersedia).
- **Relawan:** fungsi warga dengan bobot verifikasi lebih besar setelah disetujui admin.
- **Petugas simulasi:** antrean `/petugas`, teruskan laporan dan catat tahapan penanganan dari detail laporan.
- **Admin simulasi:** `/admin`, kelola akun dan bobot, moderasi, impor hotspot, hitung grid, ekspor CSV.
- **Publik:** peta `/peta`, analitik `/analitik`, tentang `/tentang`, dokumentasi `/api-docs`.

## Indeks prioritas

Skor adalah Σ(faktor 0–1 × bobot). Bobot awal 30/20/20/15/10/5 untuk verifikasi/jenis lahan/hotspot/POI/dukungan/foto. Nilai laporan berstatus baru dibatasi 59. Rincian formula ada di halaman Tentang dan `app/Domain/PriorityCalculator.php`. Bobot merupakan heuristik awal dan layak dikalibrasi bersama pakar.

## Data hotspot

Tanpa CSV, `SEED_DEMO=true` menghasilkan hotspot sintetis berlabel `SIMULASI`. Tim dapat meletakkan `data/hotspots_hist.csv` dengan kolom `detected_at,lat,lng,confidence,source`, atau memakai:

```bash
php artisan apikawal:import-hotspots data/hotspots_hist.csv
php artisan apikawal:recompute-grid
```

Data NASA FIRMS atau sumber terbuka lain harus diperiksa lisensi dan syarat penggunaannya sebelum dipakai atau dipublikasikan.

## API publik

Semua endpoint hanya-baca, tanpa identitas pelapor, rate limit 60 permintaan/menit per IP:

- `GET /api/v1/reports` (filter status, level, lahan, tanggal; pagination)
- `GET /api/v1/reports/{code}` (ringkasan + timeline)
- `GET /api/v1/risk-areas?level=tinggi`
- `GET /api/v1/stats`
- `GET /api/health`

Detail contoh respons: [docs/API.md](docs/API.md).

## Pengujian dan kualitas

```bash
composer check
```

Menjalankan Pint `--test`, `php artisan test`, dan `npm run build`. Untuk memformat kode PHP: `vendor/bin/pint`.

## Struktur penting

`app/Domain`, `app/Services`, `app/Http`, `app/Models`, `app/Policies`, `database/migrations`, `database/seeders`, `resources/views`, `resources/js`, `routes`, `docs`.

## Privasi dan keamanan

CSRF, hash password, request validation, policy + middleware role, throttle, MIME pemeriksaan upload + re-encode, Blade escaping, pembulatan koordinat publik, dan CSP/header keamanan (lihat docs keamanan). Jangan pernah commit `.env` atau kredensial. Identitas pelapor tidak diberikan endpoint API publik.

## Data, atribusi, dan proyeksi

Tile peta © OpenStreetMap contributors (lihat kebijakan tile server sebelum produksi). Hotspot demo sintetis. Nama anggota tim ditampilkan di halaman Tentang; logo kompetisi belum tersedia dan dapat ditambahkan tim.

Pengembangan berikutnya: integrasi resmi BPBD/Manggala Agni, WhatsApp/SMS, cuaca real-time, kalibrasi pakar, model prediksi, PWA/offline, dan monitoring operasional.

## Dokumentasi proyek

ERD, alur, state machine, arsitektur, API, deployment, asumsi, catatan proposal, dan daftar screenshot berada di folder `docs/`.
