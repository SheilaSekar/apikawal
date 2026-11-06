# Asumsi dan batas implementasi INVOFEST

- Kompetisi berlangsung tanpa backend. Seluruh mutasi dan pemilihan peran hanya simulasi lokal pada browser. Mode demo tidak memberi keamanan otorisasi; state localStorage dapat diubah pengguna.
- ID demo peran bersifat tetap agar aturan satu verifikasi per pengguna dapat diperagakan. Relawan dianggap sudah disetujui. Pilihan peran tidak menjadi identitas akun sungguhan.
- Data seed deterministik diperbarui relatif terhadap waktu buka pertama; timestamp tersimpan setelah hidrasi. Reset demo membuang perubahan lokal lalu memuat seed kembali.
- `localStorage` dipilih karena tetap ada setelah tab/browser ditutup. Jika penyimpanan ditolak/penuh, aplikasi memberi pesan dan tidak mengklaim perubahan berhasil tersimpan. Foto dapat menjadi sumber kuota; canvas mengecilkan gambar sebelum menyimpannya.
- Data contoh dan POI adalah sintetis, bukan pelaporan atau batas administrasi nyata. Semua data contoh ditandai simulasi.
- OSM tile tidak boleh disalin/di-cache sembarang untuk penggunaan offline dan memerlukan koneksi serta mengikuti kebijakan penyedia. Saat tile gagal, daftar laporan tetap menjadi alternatif yang dapat dipakai.
- Chart.js dan Leaflet di-vendorkan dari paket npm yang sudah tersedia di repository jika ada; versi dan lisensi dicatat. Tidak menambah font eksternal, CDN, atau aset berhak cipta.
- Foto laporan simulasi dibuat secara lokal dengan SVG/canvas. Foto pengguna hanya diproses sementara di browser dan disimpan di localStorage perangkat tersebut.
- Skor, voting, anti-spam, laporan penyalahgunaan, dan status tidak dikirim ke BPBD/instansi. Semuanya adalah demonstrasi alur frontend.
- Admin dapat mengganti bobot hanya dengan total 100. Nilai tersimpan lokal dan reset kembali ke bobot bawaan.
- Informasi tim diambil hanya dari `config/apikawal.php` yang sudah ada. Lembar orisinalitas, tautan video, dan bagian identitas yang belum diberikan harus tetap berupa placeholder yang jelas.
- README root dan aplikasi Laravel tidak diubah untuk menyesuaikan lomba baru. `invofest/README.md` menjelaskan versi frontend kompetisi dan dengan jelas membedakannya dari Laravel root yang memakai autentikasi nyata.
- Batas waktu aktual saat mulai pengerjaan adalah 6 November 2026 untuk tenggat 7 November. Urutan fitur inti didahulukan; fitur opsional mengikuti urutan potong dari brief.
