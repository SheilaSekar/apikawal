# API publik v1

Semua route GET; CORS GET terbuka, dibatasi 60 permintaan/menit/IP. JSON memakai `{data, meta}`. Koordinat laporan tiga angka desimal; tidak ada nama, deskripsi bebas, foto, atau ID pengguna. Laporan moderasi dan ditolak tidak publik. Respons daftar memiliki `Cache-Control: public, max-age=30`; stats 60 detik; grid 300 detik.

## Daftar
`GET /api/v1/reports?status=terverifikasi&level=tinggi&land_type=gambut&since=2026-10-01&per_page=20&page=1`

```json
{"data":[{"code":"AK-2026-000001","lat":-3.123,"lng":105.765,"status":"terverifikasi","priority_level":"tinggi","priority_score":68,"land_type":"gambut","created_at":"2026-10-29T10:00:00+07:00","is_simulated":true}],"meta":{"current_page":1,"last_page":1,"per_page":20,"total":1}}
```

Filter `status`, `level`, `land_type`, `since` (tanggal ISO); `per_page` 1–100.

## Detail
`GET /api/v1/reports/{code}` — field daftar + timeline anonim `{status,note,at}`. Catatan operasional perlu ditulis tanpa data pribadi.

## Risk areas
`GET /api/v1/risk-areas?level=tinggi` — grid bbox, skor, rank, level, counts.

## Stats
`GET /api/v1/stats` — jumlah laporan, verified/rejected, penanganan/selesai, median waktu.

## Health
`GET /api/health` — `status`, status DB, versi Laravel, waktu; HTTP 503 jika DB gagal.

Kesalahan validasi mengikuti format JSON Laravel. Jangan gunakan endpoint untuk keadaan darurat.
