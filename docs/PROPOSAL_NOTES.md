# Catatan proposal

## Fitur yang terimplementasi
- Form pelaporan lapangan dan upload foto yang divalidasi/re-encode.
- Pencocokan laporan induk 300 m/6 jam; laporan baru menjadi dukungan, dengan pencegahan vote ganda.
- Evaluasi suara berbobot: warga 1, relawan disetujui 2, petugas/admin 3; dua bobot konfirmasi atau suara tepercaya dapat memverifikasi.
- State machine respons, log perubahan dan notifikasi aplikasi.
- Peta publik/filter/daftar, detail laporan anonim, antrean petugas simulasi, panel admin.
- Wilayah grid dan API publik read-only, analitik tren, impor hotspot dan ekspor CSV.

## Rumus indeks
`skor = Σ(faktor 0..1 × bobot)`, bobot awal verifikasi 30, lahan 20, hotspot 20, POI 15, usia/dukungan 10, foto 5. Laporan `baru` dibatasi 59. Bobot heuristik awal, belum dikalibrasi pakar.

## API
`GET /api/v1/reports`, `/api/v1/reports/{code}`, `/api/v1/risk-areas`, `/api/v1/stats`, `/api/health`.

## Keamanan dan privasi
Password hashing, CSRF, request validation, role middleware + policy, rate limit, pengolahan ulang gambar, anonimisasi pelapor pada publik, pembulatan koordinat. Data simulasi diberi badge.

## Batasan
Bukan pendeteksi otomatis/pengganti layanan darurat; petugas disimulasikan. Koordinat/verifikasi warga dapat tidak akurat. Tidak ada integrasi resmi.

## Proyeksi pengembangan (belum ada)
Integrasi BPBD/Manggala Agni, notifikasi WhatsApp/SMS, cuaca real-time, kalibrasi bobot pakar, model prediksi, PWA/offline, monitoring produksi.
