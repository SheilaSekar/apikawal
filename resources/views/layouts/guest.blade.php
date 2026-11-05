<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }} — {{ config('app.name', 'APIKAWAL') }}</title>
        <meta name="description" content="Masuk atau buat akun APIKAWAL untuk berkolaborasi dalam pelaporan dan verifikasi lapangan.">
        <meta property="og:title" content="{{ $title }} — {{ config('app.name', 'APIKAWAL') }}">
        <meta property="og:description" content="Pelaporan, verifikasi, dan koordinasi respons risiko kebakaran lahan.">
        <meta name="theme-color" content="#f7fafc">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
        <div class="site-chrome">
            <div class="emergency-banner" role="note">{{ config('apikawal.emergency_banner') }}</div>
            <header class="site-header"><nav class="site-container site-nav auth-nav"><a class="brand auth-brand" href="{{ route('home') }}" aria-label="APIKAWAL, kembali ke beranda"><x-application-logo /></a></nav></header>
        </div>
        <main id="main-content" class="auth-shell">
            <div class="auth-card">
                {{ $slot }}
                <a class="auth-home-link" href="{{ route('home') }}">Kembali ke beranda</a>
            </div>
        </main>
        <footer class="site-footer"><div class="site-container"><strong>APIKAWAL</strong><p>Alat pelaporan dan koordinasi, bukan pendeteksi api otomatis dan bukan pengganti layanan darurat.</p></div></footer>
    </body>
</html>
