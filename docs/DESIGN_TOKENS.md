# Design tokens APIKAWAL

Bahasa visual ini memakai warna tenang bertema lingkungan dengan aksen hangat untuk tindakan dan risiko. Latar pastel dibangun memakai CSS radial-gradient lokal; tidak ada gambar latar, video, canvas dekoratif, font remote, atau framework UI tambahan. Tipografi memakai system font stack.

## Token utama

| Token | Nilai | Pemakaian |
|---|---|---|
| `--page` | `#F7FAFC` | Dasar halaman terang |
| `--primary` | `#123B46` | Identitas teal gelap, heading, kontrol |
| `--ink` | `#0F172A` | Teks utama |
| `--muted` | `#475569` | Teks sekunder |
| `--accent` | `#C2410C` | Tautan penting dan aksi utama |
| `--focus` | `#EA580C` | Fokus keyboard |
| `--emergency` | `#7F1D1D` | Banner darurat permanen |
| `--success` | `#064E3B` | Status berhasil |
| `--warning` | `#713F12` | Peringatan dan badge simulasi |
| `--danger` | `#7F1D1D` | Status/aksi berbahaya |
| `--border` | `#819287` | Garis kontrol dan permukaan |
| `--card` | `#FFFFFF` | Fallback permukaan solid |
| `--glass` | `rgb(255 255 255 / 78%)` | Permukaan kaca buram |
| `--footer` | `#022C22` | Footer |

Warna prioritas marker adalah `--priority-low: #0F766E`, `--priority-medium: #B45309`, `--priority-high: #C2410C`, dan `--priority-critical: #B91C1C`. Label prioritas memakai pasangan teks dan latar yang lebih gelap/terang, sehingga tetap terbaca tanpa bergantung pada warna.

## Kontras yang dicek

Rasio dihitung dengan rumus WCAG untuk kombinasi solid berikut. Teks isi memenuhi 4.5:1; indikator/border UI memenuhi 3:1.

| Elemen | Teks/garis | Latar | Rasio | Hasil |
|---|---|---|---:|---|
| Teks utama | `#0F172A` | `#FFFFFF` | 17.85:1 | AA |
| Teks sekunder | `#475569` | `#FFFFFF` | 7.58:1 | AA |
| Aksi utama dan tautan | `#FFFFFF` | `#C2410C` | 5.18:1 | AA |
| Fokus dan border kontrol | `#EA580C` | `#FFFFFF` | 3.56:1 | UI |
| Border kontrol/kartu | `#819287` | `#FFFFFF` | 3.28:1 | UI |
| Banner darurat | `#FFFFFF` | `#7F1D1D` | 10.02:1 | AA |
| Footer | `#FFFFFF` | `#022C22` | 15.15:1 | AA |
| Teks sekunder footer | `#E2E8F0` | `#022C22` | 12.29:1 | AA |
| Status berhasil | `#064E3B` | `#ECFDF5` | 9.23:1 | AA |
| Status/peringatan | `#713F12` | `#FEF3C7` | 7.79:1 | AA |
| Status berbahaya | `#7F1D1D` | `#FEF2F2` | 9.16:1 | AA |
| Prioritas rendah | `#115E59` | `#F0FDFA` | 7.27:1 | AA |
| Prioritas sedang | `#713F12` | `#FFFBEB` | 8.36:1 | AA |
| Prioritas tinggi | `#7C2D12` | `#FFF7ED` | 8.83:1 | AA |
| Prioritas kritis | `#7F1D1D` | `#FEF2F2` | 9.16:1 | AA |

> Rasio ini berlaku untuk pasangan warna solid yang tercantum. Latar gradient dan alpha glass menambah variasi visual; teks ditempatkan pada permukaan putih semi-opaque/solid agar kontrasnya tetap terjaga. Pemeriksaan visual akhir pada browser tetap diperlukan.

## Gerak dan fallback

- Permukaan memakai `backdrop-filter` hanya bila didukung; jika tidak, latarnya menjadi putih solid.
- Reveal dan hitung naik memakai `IntersectionObserver`/`requestAnimationFrame` tanpa paket animasi.
- `prefers-reduced-motion` menampilkan seluruh konten langsung, menghentikan transisi/animasi, dan mematikan scroll halus. `!important` hanya dipakai pada aturan preferensi ini agar durasi komponen tidak mengalahkannya.
- Pada layar kecil jumlah radial-gradient dan intensitas blur dikurangi.
