<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'APIKAWAL â€” Koordinasi risiko kebakaran lahan')</title>
    <meta name="description" content="Pelaporan, verifikasi, dan koordinasi respons risiko kebakaran lahan.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-900 antialiased">
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
    <div class="emergency-banner" role="note">{{ config('apikawal.emergency_banner') }}</div>
    <header class="border-b border-slate-200 bg-white">
        <nav class="site-container flex min-h-16 flex-wrap items-center justify-between gap-4 py-3" aria-label="Navigasi utama">
            <a class="brand" href="{{ route('home') }}" aria-label="APIKAWAL, halaman utama"><span aria-hidden="true">ðŸ”¥</span> APIKAWAL</a>
            <div class="flex flex-wrap items-center gap-4 text-sm font-semibold">
                <a href="{{ route('map') }}">Peta</a>
                <a href="{{ route('about') }}">Tentang</a><a href="{{ route('analytics') }}">Analitik</a><a href="{{ route('api-docs') }}">API</a>
                @auth<a href="{{ route('dashboard') }}">Dasbor</a>@else<a href="{{ route('login') }}">Masuk</a>@endauth
                @guest<a class="button button-primary" href="{{ route('register') }}">Buat akun</a>@endguest
            </div>
        </nav>
    </header>
    <main id="main-content">@yield('content')</main>
    <footer class="mt-16 bg-emerald-950 py-8 text-white">
        <div class="site-container"><strong>APIKAWAL</strong><p class="mt-2 max-w-3xl text-sm text-emerald-100">Alat pelaporan dan koordinasi, bukan pendeteksi api otomatis dan bukan pengganti layanan darurat. Peran petugas dalam prototipe disimulasikan.</p><small class="text-emerald-200">Peta menggunakan Â© OpenStreetMap contributors.</small></div>
    </footer>
</body>
</html>


