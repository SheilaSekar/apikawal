<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
        <div class="site-chrome">
            <div class="emergency-banner" role="note">{{ config('apikawal.emergency_banner') }}</div>
            @include('layouts.navigation')
        </div>

        @isset($header)
            <header class="site-page-heading">
                <div class="site-container">{{ $header }}</div>
            </header>
        @endisset

        <main id="main-content" class="app-content">{{ $slot }}</main>
        <footer class="site-footer"><div class="site-container"><strong>APIKAWAL</strong><p>Alat pelaporan dan koordinasi, bukan pendeteksi api otomatis dan bukan pengganti layanan darurat.</p></div></footer>
    </body>
</html>
