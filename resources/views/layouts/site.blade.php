<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Pelaporan, verifikasi, dan koordinasi respons risiko kebakaran lahan.')">
    <meta property="og:title" content="@yield('title', 'APIKAWAL — Koordinasi risiko kebakaran lahan')">
    <meta property="og:description" content="@yield('description', 'Pelaporan, verifikasi, dan koordinasi respons risiko kebakaran lahan.')">
    <meta name="theme-color" content="#f7fafc">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>@yield('title', 'APIKAWAL — Koordinasi risiko kebakaran lahan')</title>
    <meta name="description" content="Pelaporan, verifikasi, dan koordinasi respons risiko kebakaran lahan.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-900 antialiased">
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
    <div class="site-chrome" data-site-chrome>
        <div class="emergency-banner" role="note">{{ config('apikawal.emergency_banner') }}</div>
        <header class="site-header">
            <nav class="site-container site-nav flex flex-wrap items-center justify-between gap-4 py-3" aria-label="Navigasi utama">
                <a class="brand" href="{{ route('home') }}" aria-label="APIKAWAL, halaman utama"><x-application-logo /></a>
                <button class="menu-toggle" type="button" aria-controls="site-nav-links" aria-expanded="false" data-menu-toggle>
                    <span class="menu-toggle-icon" aria-hidden="true"><span></span><span></span></span>
                    <span class="sr-only">Buka navigasi</span>
                </button>
                <div id="site-nav-links" class="site-nav-links text-sm font-semibold" data-collapsible-nav>
                    <a class="site-nav-link" href="{{ route('map') }}" @if(request()->routeIs('map')) aria-current="page" @endif>Peta</a>
                    <a class="site-nav-link" href="{{ route('about') }}" @if(request()->routeIs('about')) aria-current="page" @endif>Tentang</a>
                    <a class="site-nav-link" href="{{ route('analytics') }}" @if(request()->routeIs('analytics')) aria-current="page" @endif>Analitik</a>
                    <a class="site-nav-link" href="{{ route('api-docs') }}" @if(request()->routeIs('api-docs')) aria-current="page" @endif>API</a>
                    @auth
                        <a class="site-nav-link" href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>Dashboard</a>
                        @if (in_array(auth()->user()->role->value, ['petugas', 'admin'], true))
                            <a class="site-nav-link" href="{{ route('officer.index') }}" @if(request()->routeIs('officer.*')) aria-current="page" @endif>Panel petugas</a>
                        @endif
                        @if (auth()->user()->role->value === 'admin')
                            <a class="site-nav-link" href="{{ route('admin.index') }}" @if(request()->routeIs('admin.*')) aria-current="page" @endif>Panel admin</a>
                        @endif
                    @else
                        <a class="site-nav-link" href="{{ route('login') }}" @if(request()->routeIs('login')) aria-current="page" @endif>Masuk</a>
                    @endauth
                    @guest<a class="button button-primary" href="{{ route('register') }}">Buat akun</a>@endguest
                </div>
            </nav>
        </header>
    </div>
    <main id="main-content">@yield('content')</main>
    <footer class="mt-16 bg-emerald-950 py-8 text-white">
        <div class="site-container"><strong>APIKAWAL</strong><p class="mt-2 max-w-3xl text-sm text-emerald-100">Alat pelaporan dan koordinasi, bukan pendeteksi api otomatis dan bukan pengganti layanan darurat. Peran petugas dalam prototipe disimulasikan.</p><small class="text-emerald-200">Peta menggunakan © OpenStreetMap contributors.</small></div>
    </footer>
</body>
</html>


