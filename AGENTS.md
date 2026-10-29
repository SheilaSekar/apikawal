# AGENTS.md — APIKAWAL (Spesifikasi Proyek Lengkap, Versi Laravel)

> File ini adalah sumber kebenaran proyek. Baca seluruhnya sebelum menulis kode. Jika ada konflik antara file ini dan asumsi kamu, file ini yang menang. **Stack proyek ini adalah Laravel (PHP). JANGAN memakai Next.js, Node backend, Prisma, atau Python.** Jika ada hal yang tidak tercakup, ambil keputusan paling aman dan sederhana, lalu catat di `docs/ASSUMPTIONS.md`. **Jangan berhenti untuk bertanya** kecuali benar-benar buntu (misalnya butuh kredensial hosting atau software belum terpasang).

---

## 0. Konteks dan Batas Waktu

- Proyek: web untuk lomba **Web Development Competition UINIC 8.0** (UIN Sunan Kalijaga), subtema **Lingkungan**.
- Tema lomba: *"Engineering Interconnected Web Solutions to Empower Digital Ecosystems"* (interoperabilitas via API, basis data relasional yang rapi, UI aksesibel, scalable, high availability, keamanan proaktif).
- **Judul karya:** *APIKAWAL: Sistem Kolaborasi Deteksi Dini, Verifikasi, dan Respons Risiko Kebakaran Lahan Berbasis Informasi Spasial*.
- **Deadline pengumpulan: Jumat, 30 Oktober 2026 pukul 23.59 WIB.** Waktu pengerjaan efektif sekitar 24 jam. Prioritaskan fitur inti yang bekerja penuh di atas fitur banyak yang setengah jadi.
- Tim: maksimal 3 mahasiswa. Anggota tim harus bisa menjelaskan sistem ini saat tanya jawab final (berbobot besar), jadi **kode harus mudah dibaca**: ikuti konvensi Laravel standar, hindari abstraksi berlebihan, beri komentar singkat pada logika bisnis penting (skor prioritas, verifikasi, state machine).

### Kriteria penilaian penyisihan (arahkan keputusan teknis ke sini)
| Kriteria | Bobot |
|---|---|
| Inovasi dan relevansi solusi (orisinalitas, nilai guna, keunggulan vs platform serupa) | 30% |
| Kesesuaian dengan kebutuhan pengguna (target, alur fitur logis) | 20% |
| Kualitas teknis dan fungsionalitas (validasi input, autentikasi, kode modular dan maintainable) | 20% |
| Kualitas UI/UX (konsistensi, usability, accessibility) | 15% |
| Dokumentasi dan kelengkapan (sistematika, user flow, proyeksi pengembangan, testing) | 15% |

### Syarat teknis dari panitia
- Website **harus online** (hosting gratis/demosite) dan bisa dijalankan di browser.
- Repository GitHub **harus public** dan aktif sampai lomba selesai tanpa minta izin akses.
- Wajib ada **dokumentasi kode dan panduan penggunaan** (README).
- Dilarang konten pornografi, SARA, sadis, atau merendahkan pihak lain.

---

## 1. Definisi Produk

APIKAWAL adalah **alat pelaporan dan koordinasi** untuk risiko kebakaran hutan dan lahan (karhutla). Warga dan relawan melaporkan asap atau indikasi kebakaran. Laporan **diverifikasi oleh beberapa pengguna atau petugas** sebelum diteruskan ke petugas, diberi **indeks prioritas** yang transparan, ditampilkan di **peta**, dan status penanganannya dilacak sampai selesai. Data historis dianalisis menjadi **wilayah rawan**.

### Latar masalah (untuk teks di UI/halaman Tentang)
Menurut laporan Reuters (22 Okt 2026), Indonesia menghadapi kebakaran hutan terparah dalam 11 tahun dengan El Niño memperburuk kerentanan hutan dan gambut. Kementerian Kehutanan menyatakan hotspot satelit adalah anomali suhu permukaan, satu kebakaran bisa terdeteksi sebagai beberapa hotspot, dan bara bisa tetap ada di tanah walau hotspot tidak terdeteksi, terutama di gambut. Karena itu data satelit perlu dilengkapi **verifikasi lapangan**, dan itulah celah yang diisi APIKAWAL. (Tulis sebagai kutipan sumber, jangan menambah angka yang tidak ada di sini.)

### Batasan produk (WAJIB tampil di UI, halaman Tentang, dan README)
- APIKAWAL **bukan pendeteksi api otomatis**. Tidak ada klaim deteksi tanpa sensor/data memadai.
- APIKAWAL **bukan pengganti layanan darurat**. Banner permanen di semua halaman: "Kebakaran sedang berlangsung dan mengancam jiwa? Hubungi 112 atau BPBD/pemadam kebakaran setempat. APIKAWAL adalah alat pelaporan dan koordinasi."
- **Indeks Prioritas adalah alat triase** untuk mengurutkan antrean, bukan pengukur akurat tingkat bahaya. Bobotnya heuristik awal dan dapat dikalibrasi bersama pakar.
- Peran petugas instansi **disimulasikan** dalam prototipe; belum terintegrasi resmi dengan BPBD/Manggala Agni.
- Data demo bersifat **simulasi**. Beri label "DATA SIMULASI" (badge di UI + kolom `is_simulated`).

### Pembeda dari platform serupa (Sipongi, NASA FIRMS, dll.)
Platform tersebut berbasis satelit. APIKAWAL melengkapi dengan lapisan laporan lapangan: (1) verifikasi berlapis, (2) indeks prioritas transparan dengan rincian "kenapa skor ini", (3) pelacakan status respons terbuka, (4) analisis wilayah rawan dari data historis, (5) API publik read-only.

### Pemetaan SDGs (tampilkan di halaman Tentang)
SDG 13 (utama), SDG 15, SDG 11, SDG 3, SDG 17.

---

## 2. Stack (sudah diputuskan, jangan ganti)

- **Laravel versi stabil terbaru yang kompatibel dengan PHP terpasang** (PHP ≥ 8.2). Pasang dengan `composer create-project laravel/laravel`.
- **Laravel Breeze, stack Blade** (`composer require laravel/breeze --dev`, `php artisan breeze:install blade`) → Tailwind CSS, Alpine.js, Vite, autentikasi siap pakai (hash password, CSRF, session, throttle login). **Tanpa** Livewire, Inertia, React, Vue, Filament, Nova, atau paket admin panel. Panel admin dibuat manual dengan Blade supaya mudah dijelaskan.
- **Database:** target produksi **MySQL/MariaDB**. Kode dan migrasi harus **DB-agnostic** (tanpa SQL mentah khusus MySQL, tanpa fungsi spasial) agar test berjalan di **SQLite in-memory** dan pengembangan lokal bisa jatuh ke SQLite jika MySQL tidak tersedia. Jarak dihitung dengan **Haversine di PHP** + bounding box di query (`whereBetween` lat/lng).
- **Peta:** `leaflet` lewat npm (di-bundle Vite, bukan CDN) + tile OpenStreetMap, atribusi OSM wajib tampil. **Grafik:** `chart.js` lewat npm. Interaktivitas lain pakai Alpine.js atau JS biasa di `resources/js/`.
- **Foto:** `intervention/image` (v3, driver GD). Re-encode ke JPEG/WebP (otomatis membuang EXIF), resize maks 1280px sisi panjang. **Simpan di database sebagai base64 di kolom `mediumText`** (bukan filesystem, karena hosting gratis sering punya disk sementara; dan bukan BLOB, agar kompatibel lintas DB). Layani lewat route dengan header cache. Batas: maks 3 foto per laporan, maks 5 MB per file sebelum kompres, hanya JPEG/PNG/WebP (validasi `image` + `mimes` + cek MIME nyata dengan `getimagesize`/finfo).
- **Validasi:** Form Request untuk semua input. **Otorisasi:** Policy + middleware role. **API:** API Resource, prefix `v1`.
- **Rate limiting:** `RateLimiter` bawaan Laravel (cache). Set `CACHE_STORE=database` agar bekerja di hosting tanpa Redis.
- **Testing:** `php artisan test` (PHPUnit/Pest sesuai default installer). Gaya kode: **Laravel Pint**.
- **Script `composer check`:** jalankan `pint --test`, `php artisan test`, lalu `npm run build`. Harus hijau sebelum commit tiap fase.
- **Zona waktu dan bahasa:** `APP_TIMEZONE=Asia/Jakarta`, `APP_LOCALE=id`, `APP_FALLBACK_LOCALE=id`, `APP_FAKER_LOCALE=id_ID`. Tulis file bahasa `lang/id/` (minimal `validation.php`, `auth.php`, `passwords.php`, `pagination.php`) dalam Bahasa Indonesia yang ringkas dan benar.

### Prasyarat lokal (cek di awal, laporkan jika kurang)
Jalankan `php -v`, `composer -V`, `node -v`, `npm -v`, `git --version`. PHP butuh ekstensi: mbstring, openssl, pdo_sqlite, pdo_mysql, gd, fileinfo, xml, ctype, tokenizer, json. Jika MySQL lokal tidak ada, pakai SQLite untuk pengembangan dan catat. Jika PHP/Composer belum terpasang, **berhenti dan beri instruksi pemasangan singkat** (mis. Laragon di Windows) alih-alih menebak.

### Cara membuat proyek di folder yang tidak kosong
Folder ini sudah berisi `AGENTS.md`, `KICKOFF_PROMPT.md`, `docs/`, dan `.git`. `composer create-project` butuh folder kosong, jadi: buat di subfolder sementara (`composer create-project laravel/laravel _laravel_tmp`), **pindahkan seluruh isinya termasuk file tersembunyi** (`.env.example`, `.gitignore`, `.editorconfig`, dll.) ke root tanpa menimpa `AGENTS.md`, `KICKOFF_PROMPT.md`, atau `docs/`, hapus folder sementara, lalu `composer install`/`php artisan key:generate`. Jangan menghapus `.git`.

### Struktur folder (modular, dipisah per tanggung jawab)
```
app/
  Enums/                 # Role, LandType, Sign, ReportStatus, PriorityLevel, VoteType, VolunteerStatus (backed enum + label())
  Models/
  Policies/              # ReportPolicy, UserPolicy
  Http/
    Controllers/         # Public/, Report/, Officer/, Admin/, Api/V1/
    Requests/            # Form Request per aksi
    Middleware/          # EnsureRole, SecurityHeaders
    Resources/           # ReportPublicResource, RiskAreaResource, ...
  Support/Geo.php        # haversine, bounding box, rounding
  Domain/                # LOGIKA MURNI (tanpa DB), mudah dites
    PriorityCalculator.php
    VerificationEvaluator.php
    StatusMachine.php
    DuplicateMatcher.php
  Services/              # memanggil DB + Domain
    ReportService.php  VerificationService.php  ResponseService.php
    ImageProcessor.php  AnalyticsService.php  HotspotImporter.php  NotificationService.php
  Console/Commands/      # apikawal:import-hotspots, apikawal:recompute-grid
config/apikawal.php      # semua konstanta: radius duplikat, jendela waktu, ambang, bobot default, bbox demo, info tim
database/migrations, factories, seeders
resources/views/         # layouts/, components/, pages/ (landing, map, report, dashboard, officer, admin, analytics, about, api-docs)
resources/js/            # map.js, report-form.js, charts.js
routes/web.php, routes/api.php
tests/Unit, tests/Feature
docs/  ERD.md STATE_MACHINE.md USER_FLOW.md ARCHITECTURE.md API.md ASSUMPTIONS.md PROPOSAL_NOTES.md DEPLOY.md SCREENSHOTS.md PROGRESS.md
```

Aturan kode: controller tipis → Form Request → Service → Domain/Model. Nama kode dalam bahasa Inggris, teks UI dalam Bahasa Indonesia. Semua konstanta bisnis di `config/apikawal.php`, bukan hardcode tersebar. Gunakan `$fillable` (jangan `$guarded = []`), dan **`role`, `reputation`, `is_active` dan `volunteer_status` tidak boleh mass-assignable dari input pengguna**.

---

## 3. Peran dan Hak Akses

| Aksi | Publik | WARGA | RELAWAN | PETUGAS | ADMIN |
|---|---|---|---|---|---|
| Lihat peta, detail laporan (versi anonim), analitik, API publik | ✅ | ✅ | ✅ | ✅ | ✅ |
| Buat laporan | ❌ | ✅ | ✅ | ✅ | ✅ |
| Memberi verifikasi (bukan pada laporan sendiri) | ❌ | ✅ (bobot 1) | ✅ (bobot 2) | ✅ (bobot 3) | ✅ (bobot 3) |
| Lihat antrean petugas, ubah status penanganan | ❌ | ❌ | ❌ | ✅ | ✅ |
| Verifikasi relawan, kelola pengguna, ubah bobot prioritas, moderasi, ekspor CSV | ❌ | ❌ | ❌ | ❌ | ✅ |

- Registrasi publik hanya membuat `WARGA` (role dipaksa di server, bukan dari input). Pengajuan relawan: warga mengajukan dari profil (`volunteer_status = pending`), admin menyetujui → role `relawan`. `PETUGAS` dan `ADMIN` hanya dibuat admin/seeder.
- Bobot RELAWAN hanya berlaku jika `volunteer_status = approved`.
- Otorisasi dua lapis: middleware `role:...` pada route **dan** Policy/`authorize()` di controller/service. Cek **kepemilikan** (anti-IDOR).
- Identitas pelapor **tidak tampil publik** ("Pelapor #xxxx" dari hash id); petugas/admin melihat nama.

---

## 4. Skema Database (migrasi Laravel)

Buat migrasi sesuai tabel di bawah (tambahan atas tabel bawaan Laravel/Breeze: `sessions`, `cache`, `jobs`, `password_reset_tokens`). Nilai enum disimpan sebagai **string** (cast ke PHP backed enum di Model), nilai huruf kecil snake_case. Hindari nama kolom yang kata terlindungi MySQL (`key`, `rank` bisa bermasalah → gunakan `cell_key`, `rank_position`).

**users** (modifikasi tabel bawaan): `id`, `name`, `email` unique, `password`, `role` string default `warga`, `volunteer_status` string default `none` (none|pending|approved|rejected), `region` nullable, `reputation` int default 0, `is_active` bool default true, `remember_token`, timestamps. (`email_verified_at` boleh dibiarkan nullable dan tidak dipakai.)

**reports**: `id`, `code` string unique nullable (diisi setelah insert, format `AK-{tahun}-{id 6 digit}`), `reporter_id` FK users, `parent_id` nullable FK reports (untuk duplikat), `lat` double, `lng` double, `land_type` (gambut|hutan|semak|kebun|lainnya), `signs` json (array dari asap|api|bara), `estimated_area_ha` float nullable, `description` text nullable, `status` string default `baru`, `priority_score` unsignedSmallInteger default 0, `priority_level` string default `rendah`, `priority_breakdown` json nullable, `is_simulated` bool default false, `needs_moderation` bool default false, timestamps. Index: `(status, priority_level, created_at)`, `(lat, lng)`, `parent_id`.

**report_photos**: `id`, `report_id` FK cascade, `mime`, `data_base64` mediumText, `size_bytes`, timestamps.

**verifications**: `id`, `report_id` FK cascade, `user_id` FK, `vote` (konfirmasi|tidak_terlihat|sudah_padam), `weight` unsignedTinyInteger (snapshot bobot), `distance_km` float nullable (opsional, dari geolokasi pemilih, tidak ditegakkan), `source_report_id` nullable FK reports nullOnDelete (jika suara berasal dari laporan duplikat), timestamps. **Unique `(report_id, user_id)`**.

**response_logs**: `id`, `report_id` FK cascade, `actor_id` nullable FK users nullOnDelete (null = sistem), `from_status` nullable, `to_status`, `note` text nullable, timestamps.

**abuse_flags**: `id`, `report_id` FK cascade, `flagged_by` nullable FK users, `reason` string, `resolved` bool default false, timestamps.

**hotspots**: `id`, `lat`, `lng`, `confidence` unsignedSmallInteger nullable, `detected_at` datetime (index), `source` string (mis. "NASA FIRMS (impor)" atau "SIMULASI"), `is_simulated` bool. Index `(lat, lng)`.

**points_of_interest**: `id`, `name`, `kind` (permukiman|sekolah|faskes), `lat`, `lng`, `is_simulated`.

**grid_cells**: `id`, `cell_key` string unique (`{latIdx}_{lngIdx}`), `min_lat`, `min_lng`, `max_lat`, `max_lng`, `hotspot_count` int default 0, `report_count` int default 0, `score` float default 0, `rank_position` nullable int, `prone_level` string nullable (rendah|sedang|tinggi), timestamps.

**priority_configs**: `name` string **primary key** (non-incrementing; mis. `w_verification`), `value` float, `label` string.

**user_notifications**: `id`, `user_id` FK cascade, `report_id` nullable FK, `message` string, `read_at` nullable, timestamps. (Model `UserNotification`; jangan bentrok dengan notifikasi bawaan Laravel.)

Buat factory untuk model utama (User, Report, Verification, Hotspot, PointOfInterest) agar test dan seeder ringkas.

---

## 5. Logika Bisnis

Letakkan aturan sebagai **kelas murni di `app/Domain/`** (input array/DTO sederhana, output nilai/array, tanpa akses DB) dan tulis unit test-nya. Service di `app/Services/` mengambil data dari DB lalu memanggil Domain. Semua angka dari `config/apikawal.php`.

### 5.1 Deteksi duplikat (`DuplicateMatcher` + `ReportService`)
Saat laporan baru dibuat: cari laporan **induk** (`parent_id` null) berstatus bukan {ditolak, selesai, padam_sendiri, duplikat}, dalam **radius 300 m** dan **6 jam** terakhir (config). Pakai bounding box di query, lalu Haversine di PHP.
- Jika ada: laporan baru disimpan berstatus `duplikat` dengan `parent_id` terisi, dan **dihitung sebagai suara `konfirmasi`** dari pelapor ke laporan induk (bobot sesuai peran; `source_report_id` diisi). Beri tahu pengguna: "Laporan serupa sudah ada. Laporanmu menambah dukungan."
- Jika pelapor yang sama sudah punya laporan/suara di laporan induk itu: tolak dengan pesan jelas.
- Setelah merge: evaluasi ulang verifikasi dan prioritas laporan induk.

### 5.2 Verifikasi (`VerificationEvaluator`)
Satu suara per pengguna per laporan (unique DB + cek aplikasi), **tidak boleh memilih laporan sendiri**, hanya pada laporan aktif (status baru/terverifikasi). Bobot: WARGA 1, RELAWAN(approved) 2, PETUGAS/ADMIN 3.
- `confirmW`, `denyW` (`tidak_terlihat`), `outW` (`sudah_padam`) = jumlah bobot per jenis suara. Ambang di config (`confirm_threshold = 2`).
- Aturan untuk status `baru`:
  - Ada suara `konfirmasi` dari relawan/petugas/admin **atau** `confirmW >= 2` dan `confirmW > denyW` → `terverifikasi`.
  - `denyW >= 3` dan `denyW > confirmW` → `ditolak` (reputasi pelapor −1).
  - `outW >= 2` dan `outW >= confirmW` → `padam_sendiri`.
- Saat menjadi `terverifikasi`: reputasi pelapor +1, notifikasi ke semua PETUGAS dan ke pelapor.
- `distance_km` opsional (jika pemilih mengizinkan geolokasi), tampil ke petugas, **tidak ditegakkan** (mudah dipalsukan); cantumkan sebagai batasan di docs.

### 5.3 Indeks Prioritas (`PriorityCalculator`)
Skor 0–100 = Σ (nilai faktor 0–1 × bobot). Bobot default disimpan di tabel `priority_configs` (dapat diubah admin; total harus 100, divalidasi) dan di-cache. Simpan rincian per faktor di `priority_breakdown` untuk panel **"Kenapa skor ini?"**.

| Faktor | Nama bobot | Default | Nilai faktor (0–1) |
|---|---|---|---|
| Tingkat verifikasi | `w_verification` | 30 | `clamp((confirmW − denyW) / 3, 0, 1)` |
| Jenis lahan | `w_landtype` | 20 | gambut 1.0, hutan 0.8, semak 0.7, kebun 0.6, lainnya 0.3 |
| Kedekatan hotspot satelit (24 jam terakhir) | `w_hotspot` | 20 | jarak ke hotspot terdekat: ≤1 km → 1; turun linear ke 0 di 5 km; tidak ada → 0 |
| Kedekatan permukiman/fasilitas | `w_poi` | 15 | jarak ke POI terdekat: ≤1 km → 1; turun linear ke 0 di 5 km |
| Usia dan dukungan | `w_support` | 10 | `0.6 × min(1, jumlahDuplikat/4) + 0.4 × recency` (recency: 1 jika <6 jam, turun linear ke 0 di 48 jam) |
| Bukti foto | `w_photo` | 5 | 1 jika ≥1 foto, selain itu 0 |

- **Cap:** laporan berstatus `baru` dibatasi maksimal skor **59** dan ditandai "belum terverifikasi" (menegakkan prinsip verifikasi dulu).
- Level: 0–39 `rendah`, 40–59 `sedang`, 60–79 `tinggi`, ≥80 `kritis`.
- Hitung ulang saat: laporan dibuat, ada verifikasi/duplikat baru, status berubah, dan saat antrean petugas dimuat untuk laporan aktif yang `updated_at`-nya lebih dari 30 menit (tidak perlu scheduler).
- UI dan docs wajib menyebut: bobot = **heuristik awal**, bukan hasil kalibrasi statistik.

### 5.4 State machine (`StatusMachine`)
| Dari | Ke | Oleh |
|---|---|---|
| baru | terverifikasi / ditolak / padam_sendiri | Sistem (5.2) atau ADMIN |
| baru | duplikat | Sistem (5.1) |
| terverifikasi | diteruskan | PETUGAS/ADMIN (menugaskan, dengan catatan) |
| terverifikasi | padam_sendiri | Sistem atau PETUGAS |
| diteruskan | dalam_penanganan | PETUGAS/ADMIN |
| dalam_penanganan | terkendali | PETUGAS/ADMIN |
| terkendali | selesai | PETUGAS/ADMIN |
| status apa pun | status apa pun | ADMIN (override, **wajib** alasan) |

Setiap perubahan **wajib** menulis `response_logs` (dalam satu DB transaction dengan perubahan status) dan membuat `user_notifications` untuk pelapor. Transisi tidak valid ditolak dengan pesan jelas. Hasilkan diagram Mermaid ke `docs/STATE_MACHINE.md` dari tabel ini.

### 5.5 Anti-penyalahgunaan
- Maks **5 laporan per akun per jam** (`RateLimiter::for('create-report', ...)` per user id).
- Login: throttle bawaan Breeze (5 percobaan) dipertahankan; pesan error generik.
- Reputasi: akun dengan reputasi ≤ −3 → laporannya `needs_moderation = true` (tidak tampil publik sampai admin menyetujui) dan membuat `abuse_flags`.
- Tombol "Laporkan penyalahgunaan" pada detail laporan → `abuse_flags` → tampil di panel admin.
- Throttle API publik per IP (`RateLimiter::for('public-api', 60/menit)`).

### 5.6 Analisis wilayah rawan (`AnalyticsService`)
- Wilayah demo dibagi menjadi **grid 0,05° × 0,05°** (≈5,5 km) dari bounding box di config; sel dibuat oleh seeder.
- `hotspot_count` = jumlah hotspot historis pada periode (default 12 bulan) per sel; `report_count` = jumlah laporan terverifikasi per sel.
- `score = 0.7 × norm(hotspot_count) + 0.3 × norm(report_count)` (min–max, 0–100). Urutkan menurun → `rank_position`. `prone_level`: 20% teratas = tinggi, 20–50% = sedang, sisanya rendah.
- Tampilkan sebagai **choropleth grid** (Leaflet Rectangle) dengan keterangan metode, sumber, periode, dan "bukan prediksi".
- `php artisan apikawal:recompute-grid` dan tombol di admin memanggil fungsi yang sama.
- KPI waktu (median laporan→verifikasi, median verifikasi→penanganan) dihitung dari `response_logs` dan boleh dihitung di PHP (collection) agar DB-agnostic.

---

## 6. Halaman dan Fitur (UI Bahasa Indonesia, Blade + Tailwind + Alpine)

Semua halaman responsif (mobile-first; warga melapor dari HP), `<html lang="id">`. Gunakan Blade layout + Blade components (Button, Input, Select, Badge, Card, Modal, Table, Alert/Toast, EmptyState, StatusBadge, PriorityBadge).

1. **`/` Landing:** hero, banner darurat (112), "Cara kerja" 4 langkah (Lapor → Verifikasi → Prioritas → Penanganan), counter (laporan aktif, terverifikasi, dalam penanganan, selesai), peta mini, laporan terbaru, tombol "Buat Laporan" dan "Lihat Peta".
2. **`/masuk`, `/daftar`:** dari Breeze, dilokalkan, pesan error jelas, password min 8 karakter. Hapus/nonaktifkan fitur yang tidak dipakai (verifikasi email, reset password lewat email) bila menyulitkan, dan catat di ASSUMPTIONS.
3. **`/peta`:** peta publik penuh. Marker berwarna menurut **prioritas** dan berbentuk/ikon menurut **status** (jangan hanya warna). Filter: status, level prioritas, jenis lahan, rentang waktu. Toggle layer: laporan, hotspot satelit (label sumber), wilayah rawan. **Tombol "Tampilan Daftar"** sebagai alternatif aksesibel (daftar/tabel dengan filter yang sama). Data peta dari endpoint JSON internal; **non-petugas melihat koordinat dibulatkan 3 desimal**, petugas/admin melihat koordinat presisi.
4. **`/lapor` (login):** form ringkas: pin lokasi di peta atau "Gunakan lokasi saya" (geolokasi, fallback manual), jenis lahan, tanda (multi-pilih), estimasi luas (opsional), deskripsi, foto (preview, maks 3). Sebelum submit, cek duplikat (endpoint JSON) dan tampilkan peringatan jika ada laporan serupa. Setelah submit: halaman sukses dengan kode laporan dan tautan pantau.
5. **`/laporan/{code}` Detail** (route model binding via `code`): info, foto, peta kecil, **badge status + prioritas**, **panel "Kenapa skor ini?"** (dari `priority_breakdown`), **timeline status** (dari `response_logs`), jumlah dukungan/suara (tanpa nama), tombol verifikasi (3 pilihan; hanya login & bukan pelapor & belum memilih), "Laporkan penyalahgunaan". PETUGAS/ADMIN: kontrol perubahan status sesuai state machine (dengan catatan).
6. **`/dashboard` (login):** "Laporan saya" + status, "Perlu verifikasi" (laporan aktif terbaru yang belum dia pilih dan bukan miliknya), notifikasi (tandai dibaca), profil + pengajuan relawan, reputasi.
7. **`/petugas` (PETUGAS/ADMIN):** antrean `terverifikasi` dan penanganan aktif, urut **prioritas lalu waktu**, filter, aksi cepat (Teruskan, Mulai tangani, Terkendali, Selesai), tampilan peta + daftar, statistik singkat.
8. **`/admin` (ADMIN):** tab Pengguna (ubah role/aktif, setujui relawan), Moderasi (flag, laporan perlu moderasi, laporan ditolak), **Bobot prioritas** (validasi total 100, tombol reset), Data (impor hotspot CSV, hitung ulang grid), **Ekspor CSV** laporan, ringkasan.
9. **`/analitik` (publik):** KPI (jumlah laporan, rasio terverifikasi vs ditolak, median waktu laporan→verifikasi, median verifikasi→penanganan), grafik tren per hari (Chart.js), distribusi per status/jenis lahan, **peta wilayah rawan**, tabel 10 sel teratas.
10. **`/tentang`:** latar masalah (dengan kutipan sumber), cara kerja, penjelasan Indeks Prioritas (tabel faktor + bobot dibaca dari DB), batasan produk, SDGs, tim (dari `config/apikawal.php`), sumber data.
11. **`/api-docs`:** dokumentasi API publik (contoh request/response, parameter, batas).
12. Halaman 404/500/403 yang rapi, serta state loading/kosong di semua daftar.

---

## 7. API

### Publik read-only (`/api/v1/*`, JSON, CORS terbuka hanya untuk GET, throttle, pagination)
- `GET /api/v1/reports?status=&level=&land_type=&since=&page=&per_page=` — **anonim**: kode, koordinat **dibulatkan 3 desimal**, status, level prioritas, jenis lahan, waktu. **Tanpa** identitas pelapor, foto, atau deskripsi bebas. Hanya laporan yang tidak `needs_moderation` dan bukan `ditolak`.
- `GET /api/v1/reports/{code}` — detail anonim + timeline status (tanpa nama petugas).
- `GET /api/v1/risk-areas?level=` — sel grid wilayah rawan (bbox, skor, rank, level).
- `GET /api/v1/stats` — KPI agregat.
- `GET /api/health` — cek koneksi DB, versi, waktu (untuk monitoring/availability). (`/up` bawaan Laravel boleh tetap aktif.)

Format respons konsisten: `{ "data": ..., "meta": ... }` atau `{ "error": { "code", "message" } }` (atur handler exception untuk request `api/*`). Pakai API Resource. Untuk routes API gunakan `php artisan install:api` (Sanctum tidak dipakai; abaikan fiturnya) atau daftarkan `routes/api.php` manual di `bootstrap/app.php`. Atur `config/cors.php` agar `api/*` hanya GET dari semua origin. Beri `Cache-Control` singkat pada endpoint agregat.

### Internal
Mutasi lewat controller web: `auth` → middleware role → Form Request (validasi) → `authorize()`/Policy → Service → `response_logs`. CSRF aktif (bawaan). Endpoint JSON internal (data peta, cek duplikat) tetap di balik middleware sesuai kebutuhan dan di-throttle.

---

## 8. Keamanan (kriteria 20%, jangan dilewatkan)

- Password di-hash (`Hash::make`, bcrypt/argon bawaan), rule `Password::min(8)`. Jangan log password/token.
- Session: `SESSION_SECURE_COOKIE=true` di production, `same_site=lax`, `http_only=true`. Regenerate session saat login (bawaan Breeze).
- CSRF di semua form (`@csrf`), validasi server-side lewat Form Request, **mass-assignment aman** (bagian 2), Eloquent/Query Builder (parameter binding) mencegah SQL injection, Blade `{{ }}` meng-escape output; **jangan** pakai `{!! !!}` untuk input pengguna.
- Otorisasi berlapis dan anti-IDOR (cek kepemilikan/role pada setiap aksi). Tulis feature test untuk ini.
- Middleware `SecurityHeaders` global: `X-Content-Type-Options: nosniff`, `Referrer-Policy: strict-origin-when-cross-origin`, `X-Frame-Options: DENY`, `Permissions-Policy` (izinkan geolocation untuk self), dan **Content-Security-Policy** yang kompatibel dengan Vite build, Leaflet, dan tile OSM (`img-src 'self' data: blob: https://*.tile.openstreetmap.org`). Jika Alpine membutuhkan `unsafe-eval`, catat trade-off di ASSUMPTIONS.
- Upload: validasi tipe/ukuran/MIME nyata, re-encode (membuang EXIF), nama/ID dibuat server, tidak ada file yang bisa dieksekusi.
- Privasi: identitas pelapor tidak publik, koordinat publik dibulatkan, tidak menyimpan IP mentah lebih dari perlu.
- Rate limiting (5.5). Pesan error autentikasi generik.
- Production: `APP_DEBUG=false`, `APP_ENV=production`, **trust proxies** dikonfigurasi (agar HTTPS/URL benar di balik reverse proxy hosting). `.env` tidak boleh ter-commit; sediakan `.env.example` lengkap.
- **Scalability/availability** (kata kunci tema): pagination di semua daftar, index pada kolom filter, eager loading (hindari N+1), cache bobot prioritas dan agregat dashboard, health-check endpoint, session/cache di database agar bisa multi-instance, dan jelaskan arsitektur stateless yang bisa diskalakan horizontal di `docs/ARCHITECTURE.md`.

---

## 9. Aksesibilitas dan Desain

- Kontras warna memenuhi **WCAG AA**. Tema: netral + aksen oranye-merah api. **Jangan mengandalkan warna saja** (selalu ada label teks/ikon untuk status dan prioritas).
- Skip-to-content link, fokus keyboard terlihat, semua input punya `<label>`, `alt` pada gambar, `aria-label` pada kontrol ikon, urutan heading benar, target sentuh ≥44px, pesan error form dengan `aria-live`.
- Peta punya **alternatif daftar**.
- Konsisten: satu palet, satu skala tipografi, satu set komponen Blade. Dark mode **tidak** diperlukan.
- Teks UI Indonesia baku, ringkas, dan pesan error yang membantu.

---

## 10. Data Seed (WAJIB, agar demo terlihat hidup)

Seeder idempoten (aman dijalankan ulang, pakai `updateOrCreate`/cek keberadaan). Semua data demo `is_simulated = true` dan tampil dengan badge **"DATA SIMULASI"**.

- **Wilayah demo (bounding box perkiraan, bukan batas administratif)** di `config/apikawal.php`: sekitar lat −3.6 s.d. −3.0, lng 105.0 s.d. 105.8 (Sumatera Selatan). Ini **perkiraan** untuk demo; beri komentar bahwa dapat diganti. Peta awal fokus ke area ini.
- **Akun demo** (hanya jika env `SEED_DEMO=true`; password demo `Demo#2026`): `warga@apikawal.test`, `relawan@apikawal.test` (approved), `petugas@apikawal.test`, `admin@apikawal.test`, plus 8 warga tambahan (nama fiktif) agar verifikasi bisa disimulasikan.
- **PriorityConfig** default (5.3).
- **POI:** 25–40 titik fiktif (permukiman, sekolah, faskes) di area demo, berlabel simulasi.
- **Hotspot:** jika ada `data/hotspots_hist.csv` (kolom: `detected_at,lat,lng,confidence,source`), impor lewat `php artisan apikawal:import-hotspots {path}`. **Jika tidak ada**, buat hotspot **sintetis** berlabel `source="SIMULASI"` (±300 titik historis dalam 12 bulan terakhir dengan pola musiman lebih padat di bulan kering dan klaster spasial, ditambah ±15 titik "24 jam terakhir" relatif waktu seed). Tulis di README bahwa tim dapat mengganti dengan data nyata (mis. unduhan NASA FIRMS atau data terbuka resmi) tanpa mengubah kode, dan **periksa lisensi/syarat penggunaan sumber data**.
- **Laporan demo:** ±30 laporan tersebar dengan variasi status (semua status terwakili), jenis lahan, prioritas, beberapa memiliki duplikat/dukungan, verifikasi, dan `response_logs` lengkap agar timeline terisi. Waktu relatif terhadap sekarang. Prioritas dihitung memakai `PriorityCalculator` yang sama (bukan angka asal).
- **Foto demo:** generate placeholder dengan GD/Intervention (gradien + teks "FOTO SIMULASI"). **Jangan** mengunduh gambar dari internet.
- Setelah seed: jalankan perhitungan grid.

---

## 11. Dokumentasi yang Harus Dihasilkan

- **`README.md` (lengkap):** deskripsi, fitur, batasan produk, stack, arsitektur singkat, prasyarat, instalasi lokal langkah demi langkah (`composer install`, `npm install`, `.env`, `php artisan key:generate`, `migrate --seed`, `npm run dev`/`build`, `php artisan serve`), variabel lingkungan, test, akun demo, panduan penggunaan per peran, struktur folder, daftar endpoint, catatan keamanan, proyeksi pengembangan, atribusi (OSM, sumber data), tim.
- **`docs/ERD.md`** (Mermaid erDiagram), **`docs/STATE_MACHINE.md`**, **`docs/USER_FLOW.md`** (flowchart per peran), **`docs/ARCHITECTURE.md`** (browser ↔ Laravel ↔ DB, layer hotspot, API publik, catatan scalability), **`docs/API.md`**.
- **`docs/ASSUMPTIONS.md`:** keputusan dan asumsi, plus daftar dependensi utama dan alasannya.
- **`docs/PROPOSAL_NOTES.md`:** ringkasan siap pakai untuk penulis proposal: fitur yang **benar-benar terimplementasi**, rumus indeks prioritas + bobot default, aturan verifikasi dan duplikat, daftar endpoint, ringkasan keamanan, batasan jujur, dan fitur yang **belum** ada (proyeksi pengembangan): integrasi resmi BPBD/Manggala Agni, notifikasi WhatsApp/SMS, data cuaca real-time, kalibrasi bobot dengan pakar, model prediksi, PWA/offline.
- **`docs/DEPLOY.md`:** lihat bagian 12.
- **`docs/SCREENSHOTS.md`:** daftar halaman/kondisi yang perlu di-screenshot untuk proposal (landing, peta dengan filter, form lapor, detail dengan "Kenapa skor ini?", antrean petugas, admin bobot, analitik wilayah rawan, API docs).

---

## 12. Deploy

Laravel membutuhkan hosting PHP. Kamu tidak punya kredensial hosting; **jangan berpura-pura sudah deploy**. Siapkan semuanya, lalu beri instruksi manual singkat. Syarat dan kuota layanan gratis sering berubah, tulis di DEPLOY.md agar tim memeriksa ketentuan terbaru dan **mencoba deploy "hello world" lebih dulu**.

Siapkan dua jalur di `docs/DEPLOY.md`:

**Jalur A — Container (platform yang mendukung Docker):**
- `Dockerfile` (PHP 8.3 + ekstensi yang dibutuhkan + Composer install `--no-dev --optimize-autoloader` + build aset Vite di stage terpisah) dengan web server yang sederhana dan benar (mis. image Apache atau Nginx+PHP-FPM), `docker/entrypoint.sh` yang menjalankan `php artisan migrate --force`, cache config/route/view, lalu start server, dan `.dockerignore`.
- Daftar env produksi: `APP_NAME`, `APP_ENV=production`, `APP_KEY` (dibuat dengan `php artisan key:generate --show`), `APP_DEBUG=false`, `APP_URL`, `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=sync`, `LOG_CHANNEL=stderr`, `SESSION_SECURE_COOKIE=true`, `SEED_DEMO=true`.
- Langkah seed ke DB produksi (dari lokal dengan `.env` produksi sementara, atau lewat shell platform): `php artisan migrate --force && php artisan db:seed --force`.

**Jalur B — Shared hosting PHP + MySQL (cPanel/hPanel):**
- Dokumen root harus mengarah ke folder `public/` (atau sediakan `.htaccess` pengalihan yang aman jika tidak bisa), jalankan `composer install --no-dev --optimize-autoloader` dan `npm run build` **secara lokal** lalu unggah `vendor/` dan `public/build/` jika hosting tidak punya akses SSH, atur `.env` produksi, dan **impor dump SQL** lewat phpMyAdmin.
- Sediakan petunjuk membuat dump SQL berisi skema + data demo (`mysqldump`) ke `database/apikawal_demo.sql` sebagai cadangan jika migrasi lewat SSH tidak memungkinkan.

Pastikan: build produksi lulus, aplikasi tidak crash tanpa data seed (halaman kosong aman), dan tidak ada ketergantungan pada symlink `storage:link` (foto di DB). Sertakan **daftar periksa sebelum submit**: link demo terbuka dari HP/jaringan lain, repo public terbuka di incognito, akun demo berfungsi, README lengkap, proposal ≤ 30 halaman, nama file sesuai ketentuan.

---

## 13. Rencana Kerja Bertahap (kerjakan berurutan)

Setelah **setiap fase**: jalankan `composer check` (atau minimal `php artisan test` + `npm run build`), perbaiki error, `git commit` dengan pesan jelas (`feat:`, `fix:`, `docs:`), dan perbarui `docs/PROGRESS.md`. Jangan lanjut dengan build/test yang rusak. Tulis **file lengkap** (bukan potongan parsial) untuk file baru atau yang diubah besar.

**Fase 0 — Fondasi:** cek prasyarat; buat proyek Laravel sesuai cara di bagian 2 (folder tidak kosong); Breeze Blade; konfigurasi `.env.example`, timezone/locale, `lang/id`; `config/apikawal.php`; enum; `Support/Geo` + unit test; layout dasar (header, footer, banner darurat, skip link) dan komponen Blade dasar; script `composer check`.
**Fase 1 — Auth dan peran:** modifikasi users + migrasi; registrasi memaksa role `warga`; middleware `EnsureRole`; Policy dasar; lokalisasi pesan auth; model `UserNotification`; halaman dashboard awal. Acceptance: tiap peran hanya mengakses halamannya; feature test untuk akses dan anti-IDOR.
**Fase 2 — Lapor:** migrasi reports/report_photos; form lapor + peta pin + geolokasi; `ImageProcessor` (validasi, re-encode, base64); route foto; kode laporan; `DuplicateMatcher` + merge; rate limit laporan. Acceptance: laporan tersimpan dengan foto tanpa EXIF; duplikat tergabung dan jadi dukungan.
**Fase 3 — Verifikasi, prioritas, state machine:** `VerificationEvaluator`, `PriorityCalculator` (+rincian), `StatusMachine`, services, `response_logs`, halaman detail dengan "Kenapa skor ini?" dan timeline, tombol verifikasi. **Unit test wajib:** Haversine, duplikat, aturan verifikasi, skor prioritas (termasuk cap 59), transisi status. Feature test: alur laporan → verifikasi → terverifikasi.
**Fase 4 — Peta publik, antrean petugas, dashboard:** `/peta` (filter, layer, tampilan daftar, pembulatan koordinat non-petugas), `/petugas`, `/dashboard`, landing dengan counter.
**Fase 5 — Admin:** pengguna, relawan, bobot prioritas (total 100), moderasi, ekspor CSV, impor hotspot, hitung ulang grid.
**Fase 6 — Analitik + API:** `/analitik` (KPI, grafik, choropleth wilayah rawan), `/api/v1/*`, `/api/health`, `/api-docs`, `/tentang`. Feature test: API anonim (koordinat dibulatkan, tidak ada data pelapor).
**Fase 7 — Seed + dokumentasi:** seeder lengkap (bagian 10), semua dokumen (bagian 11), README. Verifikasi alur end-to-end: daftar → lapor → verifikasi 2 akun → petugas teruskan → tangani → selesai.
**Fase 8 — Polishing dan penutupan:** audit aksesibilitas (kontras, label, fokus, keyboard), responsif di lebar 360px, state kosong/loading/error, header keamanan, cek tidak ada secret ter-commit, file deploy (Dockerfile, entrypoint, DEPLOY.md), `composer check` hijau, ringkasan akhir.

### Jika waktu habis, potong dengan urutan ini (yang paling dulu dibuang)
1. Notifikasi (sisakan timeline), 2. layer hotspot di peta publik (sisakan di analitik), 3. analisis wilayah rawan, 4. API publik selain `reports` dan `health`, 5. ekspor CSV, 6. Jalur B deploy (sisakan Jalur A atau sebaliknya sesuai kebutuhan tim).
**Tidak boleh dipotong:** lapor, verifikasi, status respons + log, peta, indeks prioritas + rincian, keamanan dasar (bagian 8), seed, README, `PROPOSAL_NOTES.md`.

---

## 14. Aturan Kerja untuk Codex

1. **Jangan memakai stack lain** (bukan Next.js/Node/Prisma). Jika menemukan sisa file Next.js, hapus dan catat.
2. Jangan mengklaim fitur yang belum ada (di UI, README, atau dokumen). Fitur yang tidak jadi → "proyeksi pengembangan".
3. Jangan menambah dependensi besar tanpa alasan kuat; catat setiap dependensi utama di `ASSUMPTIONS.md`.
4. Jangan mengunduh aset berhak cipta. Logo UINIC 8.0 tidak tersedia; sediakan slot placeholder (`public/images/`, `config/apikawal.php`) dan beri tahu tim untuk menambahkannya.
5. Setiap aksi mutasi: auth → role → Form Request → authorize → Service → log. Tidak ada pengecualian.
6. Tangani error dengan pesan pengguna yang jelas dan log server yang tidak membocorkan data sensitif.
7. Jangan menjalankan perintah destruktif di luar folder proyek, dan jangan `migrate:fresh` pada database selain lokal/test. Jika perintah gagal karena sandbox/jaringan/izin, jelaskan perintahnya dan alasannya, jangan dilewati diam-diam.
8. Pastikan tidak ada `TODO`/placeholder tersisa di jalur fitur inti pada akhir pekerjaan. Hal yang sengaja belum ada ditulis di dokumen.
9. Di akhir, beri ringkasan: fitur terimplementasi, yang dipotong, langkah manual untuk tim (deploy, logo, data hotspot nyata, nama tim), dan hasil `composer check`.

## 15. Definition of Done

- [ ] Alur end-to-end: daftar → lapor (dengan foto) → duplikat tergabung → verifikasi 2 akun/1 relawan → terverifikasi → antrean petugas → diteruskan → dalam_penanganan → terkendali → selesai, dengan timeline dan notifikasi.
- [ ] Indeks prioritas menampilkan rincian; cap 59 untuk laporan belum terverifikasi; bobot dapat diubah admin.
- [ ] Peta publik dengan filter + tampilan daftar; wilayah rawan tampil dengan keterangan metode.
- [ ] API publik `/api/v1/reports`, `/risk-areas`, `/stats`, `/api/health` berfungsi dan anonim.
- [ ] Keamanan bagian 8 terpenuhi; tidak ada secret di repo; `role` tidak bisa diubah lewat mass-assignment.
- [ ] Seed menghasilkan demo yang hidup (berlabel simulasi); aplikasi juga aman tanpa seed.
- [ ] `composer check` hijau; unit dan feature test untuk logika inti dan otorisasi lulus.
- [ ] README + seluruh `docs/` lengkap; `PROPOSAL_NOTES.md` jujur dan siap dipakai.
- [ ] File deploy siap (Dockerfile/entrypoint, DEPLOY.md, `.env.example`); instruksi manual untuk tim jelas.
