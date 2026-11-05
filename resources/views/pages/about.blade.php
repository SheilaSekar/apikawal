@extends('layouts.site')
@section('title', 'Tentang APIKAWAL')
@section('description', 'Cara kerja, batasan, indeks prioritas, dan kontribusi SDGs APIKAWAL.')
@section('content')
<article class="site-container prose-section about-page py-10">
    <p class="eyebrow">TENTANG APIKAWAL</p>
    <h1 class="hero-title">Kolaborasi untuk respons yang lebih terarah</h1>

    <h2>Latar masalah</h2>
    <blockquote class="notice-card about-source">
        <p class="prose-justify">Indonesia menghadapi kebakaran hutan terparah dalam 11 tahun, dengan El Niño memperburuk kerentanan hutan dan gambut. Kementerian Kehutanan menjelaskan bahwa hotspot satelit adalah anomali suhu permukaan: satu kebakaran dapat menghasilkan beberapa deteksi, sementara bara dapat bertahan tanpa hotspot terdeteksi—terutama di gambut. APIKAWAL melengkapi informasi satelit dengan laporan dan verifikasi lapangan.</p>
        <footer>Ringkasan sumber: <cite>Reuters, 22 Oktober 2026</cite>.</footer>
    </blockquote>

    <h2>Cara kerja</h2>
    <ol class="prose-justify">
        <li>Warga atau relawan melaporkan indikasi asap, api, atau bara.</li>
        <li>Pengguna lain melakukan verifikasi berlapis.</li>
        <li>Indeks prioritas mengurutkan antrean dan menjelaskan faktor skornya.</li>
        <li>Petugas dalam simulasi mencatat status respons sampai selesai.</li>
    </ol>

    <h2>Indeks Prioritas</h2>
    <p class="prose-justify">Skor 0–100 merupakan jumlah nilai faktor dikali bobot di bawah. Bobot adalah heuristik awal, bukan hasil kalibrasi statistik. Laporan berstatus baru dibatasi skor 59.</p>
    <div class="about-table-wrap">
        <table class="about-factor-table">
            <thead><tr><th scope="col">Faktor</th><th scope="col">Bobot</th></tr></thead>
            <tbody>
                @foreach(['w_verification'=>'Tingkat verifikasi','w_landtype'=>'Jenis lahan','w_hotspot'=>'Kedekatan hotspot','w_poi'=>'Kedekatan permukiman/fasilitas','w_support'=>'Usia dan dukungan','w_photo'=>'Bukti foto'] as $key=>$label)
                    <tr><td>{{ $label }}</td><td>{{ $weights[$key] ?? 0 }}%</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <h2>Batasan produk</h2>
    <ul class="prose-justify">
        <li>APIKAWAL bukan pendeteksi api otomatis.</li>
        <li>APIKAWAL bukan pengganti layanan darurat. Kebakaran sedang berlangsung dan mengancam jiwa? Hubungi 112 atau BPBD/pemadam kebakaran setempat.</li>
        <li>Indeks prioritas adalah alat triase, bukan pengukur akurat tingkat bahaya.</li>
        <li>Peran petugas instansi disimulasikan; belum terintegrasi resmi dengan BPBD/Manggala Agni.</li>
        <li>Data demo bersifat simulasi dan ditandai DATA SIMULASI.</li>
        <li>Koordinat publik dibulatkan. Geolokasi pemilih dan jarak tidak menjamin kebenaran lapangan.</li>
    </ul>

    @include('pages.partials.sdg-section')

    <h2>Tim</h2>
    <ul>@foreach(config('apikawal.team') as $member)<li>{{ $member }}</li>@endforeach</ul>
    <h2>Sumber data</h2>
    <p class="prose-justify">OpenStreetMap digunakan untuk basemap. Hotspot demo bersifat sintetis; impor data nyata perlu memeriksa lisensi dan ketentuan penggunaan sumber asal.</p>
</article>
@endsection
