# Peta port logika Laravel ke JavaScript

Sumber port adalah implementasi Laravel yang sudah ada, dengan keputusan konfigurasi dibaca dari `config/apikawal.php`. Modul browser diletakkan di `invofest/assets/js/domain/`, konstanta di `assets/js/config.js`, dan seluruh fungsi domain murni tanpa DOM/localStorage.

| Port JS | Sumber Laravel | Kontrak dan penerimaan |
|---|---|---|
| `geo.js` | `app/Support/Geo.php` | Haversine dengan radius bumi 6371,0088 km; bounding box sebelum jarak; pembulatan koordinat publik ke 3 desimal; indeks sel 0,05°. Uji jarak, batas, dan rounding. |
| `duplicate-matcher.js` | `app/Domain/DuplicateMatcher.php`, `app/Services/ReportService.php` | Kandidat akar aktif dalam 300 m dan 6 jam; abaikan status tertutup; pilih yang terdekat; blokir laporan ulang oleh persona yang sama dan dukungan ganda. Duplikat baru menjadi suara konfirmasi ke induk. |
| `verification.js` | `app/Domain/VerificationEvaluator.php`, `app/Services/VerificationService.php` | Bobot warga 1, relawan disetujui 2, petugas/admin 3. Satu suara/persona/laporan dan bukan laporan sendiri. Konfirmasi terpercaya atau confirm ≥2 dan lebih besar deny mengesahkan; deny ≥3 dan lebih besar confirm menolak; suara padam ≥2 dan ≥ confirm menandai padam sendiri. Reputasi pelapor +1 saat terverifikasi, −1 saat ditolak. |
| `priority.js` | `app/Domain/PriorityCalculator.php`, `app/Services/PriorityService.php` | Faktor verifikasi, lahan, hotspot 24 jam, POI, umur/dukungan, foto. Bobot 30/20/20/15/10/5; skor dibulatkan, status `baru` dicap 59; level 0–39 rendah, 40–59 sedang, 60–79 tinggi, ≥80 kritis. Hasil memuat kontribusi tiap faktor untuk “Kenapa skor ini?”. |
| `status-machine.js` | `app/Domain/StatusMachine.php`, `app/Services/ResponseService.php` | Transisi baru → terverifikasi/ditolak/padam_sendiri/duplikat; terverifikasi → diteruskan/padam_sendiri; diteruskan → dalam_penanganan; dalam_penanganan → terkendali; terkendali → selesai. Admin dapat override status apa pun dengan alasan. Tiap transisi sah menghasilkan log. |
| `abuse.js` | `app/Services/ReportService.php`, `app/Services/AbuseReportService.php` | Reputasi ≤−3 menandai laporan untuk moderasi dan membuat flag. Laporan ditahan dari tampilan publik sampai demo admin menyetujui. Flag menyimpan alasan dan status penyelesaian. |
| `rate-limit.js` | `app/Services/ReportService.php`, aturan `AGENTS.md` 5.5 | Maksimum 5 laporan per persona dalam jendela geser 1 jam. Batas hanya simulasi frontend dan dapat direset bersama data demo. |
| `risk-grid.js` | `app/Services/AnalyticsService.php` | Buat sel 0,05° pada bbox demo; akumulasi hotspot dan laporan terverifikasi/ditangani; skor gabungan hotspot/laporan; urut dan beri level 20% teratas tinggi, sampai 50% sedang, sisanya rendah. Tampilkan periode, sumber sintetis, bbox perkiraan, dan label bukan prediksi. |

## Pemetaan status

`baru`, `terverifikasi`, `ditolak`, `padam_sendiri`, `duplikat`, `diteruskan`, `dalam_penanganan`, `terkendali`, `selesai` dipertahankan sebagai string Indonesia dari enum Laravel.

## Perbedaan yang perlu dijaga tetap jujur

- Laravel memiliki auth, database, policy/middleware, transaction, dan state bersama server. Frontend INVOFEST mengganti semuanya dengan persona demo dan localStorage; jangan menggambarkan versi statis sebagai layanan operasional multi-pengguna.
- Rumus geografis/prioritas dan status dipertahankan agar penjelasan produk konsisten. Peran tepercaya pada verifikasi hanya efektif karena pemilih role adalah simulasi.
- Analitik frontend diberi label demonstrasi. Ia tidak memprediksi kebakaran dan tidak menggantikan verifikasi lapangan.
