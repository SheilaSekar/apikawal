<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'APIKAWAL — Koordinasi risiko kebakaran lahan')</title>
    <meta name="description" content="Pelaporan, verifikasi, dan koordinasi respons risiko kebakaran lahan.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-900 antialiased">
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
    <div class="emergency-banner" role="note">{{ config('apikawal.emergency_banner') }}</div>
    <header class="border-b border-slate-200 bg-white">
        <nav class="site-container flex min-h-16 flex-wrap items-center justify-between gap-4 py-3" aria-label="Navigasi utama">
            <a class="brand" href="{{ route('home') }}" aria-label="APIKAWAL, halaman utama"><svg aria-hidden="true" class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2.5c.6 3.1-1.3 4.1-2.7 6.2-1.1 1.7-.8 3.5.3 4.6-.1-2.1 1.3-3.1 2.5-4.2 1.8 2 3.4 3.7 3.4 6.4a5.5 5.5 0 1 1-11 0c0-3.3 2.3-6.4 5-8.8C11.5 5 12.3 4 12 2.5Z" fill="currentColor"/><path d="M12.6 13.2c1.5 1.6 2.2 2.6 2.2 4a2.8 2.8 0 1 1-5.6 0c0-1.1.6-2 1.7-3.1.5 1 .4 1.6.2 2.2 1.1-.7 1.6-1.7 1.5-3.1Z" fill="#fff"/></svg> APIKAWAL</a>
            <div class="flex flex-wrap items-center gap-4 text-sm font-semibold">
                <a class="site-nav-link" href="{{ route('map') }}">Peta</a>
                <a class="site-nav-link" href="{{ route('about') }}">Tentang</a><a class="site-nav-link" href="{{ route('analytics') }}">Analitik</a><a class="site-nav-link" href="{{ route('api-docs') }}">API</a>
                @auth<a class="site-nav-link" href="{{ route('dashboard') }}">Dasbor</a>@else<a class="site-nav-link" href="{{ route('login') }}">Masuk</a>@endauth
                @guest<a class="button button-primary" href="{{ route('register') }}">Buat akun</a>@endguest
            </div>
        </nav>
    </header>
    <main id="main-content">@yield('content')</main>
    <footer class="mt-16 bg-emerald-950 py-8 text-white">
        <div class="site-container"><strong>APIKAWAL</strong><p class="mt-2 max-w-3xl text-sm text-emerald-100">Alat pelaporan dan koordinasi, bukan pendeteksi api otomatis dan bukan pengganti layanan darurat. Peran petugas dalam prototipe disimulasikan.</p><small class="text-emerald-200">Peta menggunakan © OpenStreetMap contributors.</small></div>
    </footer>
</body>
</html>


