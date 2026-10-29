@extends('layouts.site')
@section('title', 'Dokumentasi API — APIKAWAL')
@section('content')<article class="site-container prose-section py-10"><p class="eyebrow">INTEGRASI TERBUKA</p><h1 class="section-title">API publik v1</h1><p>Endpoint hanya-baca, JSON, tanpa identitas pelapor. Batas permintaan 60 per menit per alamat IP; daftar dibatasi maksimal 100 item per halaman.</p>
<h2>Daftar laporan</h2><code>GET /api/v1/reports?status=terverifikasi&amp;level=tinggi&amp;land_type=gambut&amp;since=2026-10-01&amp;page=1&amp;per_page=20</code><p>Respons berisi kode, koordinat dibulatkan 3 desimal, status, prioritas, jenis lahan, waktu, dan penanda simulasi. Tanpa foto atau deskripsi bebas.</p>
<h2>Detail laporan</h2><code>GET /api/v1/reports/AK-2026-000001</code><p>Menyediakan informasi ringkas dan timeline status anonim.</p>
<h2>Wilayah rawan dan statistik</h2><p><code>GET /api/v1/risk-areas?level=tinggi</code> dan <code>GET /api/v1/stats</code>. Sel grid merupakan ringkasan historis, bukan prediksi.</p>
<h2>Health check</h2><code>GET /api/health</code><p>Memeriksa koneksi database, versi Laravel, dan waktu server. Gunakan untuk monitoring ketersediaan.</p>
<h2>Contoh</h2><pre class="overflow-x-auto rounded bg-slate-900 p-4 text-white"><code>curl -H "Accept: application/json" "{{ url('/api/v1/reports?per_page=10') }}"</code></pre><p>Cache-Control singkat diterapkan pada endpoint publik agregat.</p></article>@endsection
