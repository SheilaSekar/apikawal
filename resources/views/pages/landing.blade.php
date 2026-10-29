@extends('layouts.site')

@section('title', 'APIKAWAL — Waspada bersama')

@section('content')
<div class="site-container py-12 sm:py-20">
    <section class="max-w-4xl py-8 sm:py-14">
        <p class="eyebrow">KOLABORASI INFORMASI LAPANGAN</p>
        <h1 class="hero-title">Waspada bersama. Respons lebih terarah.</h1>
        <p class="hero-copy">APIKAWAL menghubungkan laporan warga, verifikasi lapangan, dan koordinasi respons risiko kebakaran lahan.</p>
        <div class="mt-8 flex flex-wrap gap-3"><a class="button button-primary" href="{{ route('register') }}">Buat Laporan</a><a class="button button-secondary" href="#peta">Lihat Peta</a></div>
    </section>

    <section class="mt-8" aria-labelledby="cara-kerja"><h2 id="cara-kerja" class="section-title">Cara kerja</h2>
        <ol class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach (['Lapor indikasi asap atau api', 'Verifikasi berlapis oleh warga dan relawan', 'Urutkan antrean dengan indeks prioritas', 'Lacak penanganan secara terbuka'] as $index => $step)
                <li class="panel"><span class="step-number">0{{ $index + 1 }}</span><p class="mt-3 font-semibold">{{ $step }}</p></li>
            @endforeach
        </ol>
    </section>

    <section id="peta" class="mt-12 panel"><div class="flex flex-wrap items-start justify-between gap-4"><div><h2 class="section-title">Peta laporan</h2><p class="mt-2 text-slate-600">Belum ada laporan untuk ditampilkan.</p></div><span class="simulated-badge">DATA SIMULASI</span></div><div class="map-placeholder mt-5" role="img" aria-label="Pratinjau peta wilayah Sumatera Selatan"><span>Peta laporan APIKAWAL</span><small>© OpenStreetMap contributors · Peta interaktif tersedia pada fase berikutnya</small></div></section>

    <section class="notice-card mt-8"><h2 class="font-bold">Informasi penting</h2><p class="mt-2">APIKAWAL bukan pendeteksi api otomatis dan bukan pengganti layanan darurat. Indeks prioritas adalah alat triase dengan bobot heuristik awal, bukan pengukur tingkat bahaya. Peran petugas pada prototipe ini disimulasikan dan belum terintegrasi resmi dengan BPBD/Manggala Agni.</p></section>
</div>
@endsection
