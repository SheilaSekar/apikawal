@extends('layouts.site')
@section('title', 'APIKAWAL — Waspada bersama')
@section('content')
<div class="landing-page">
    <section class="site-container landing-hero" aria-labelledby="landing-title">
        <div class="landing-hero-copy" data-reveal>
            <p class="eyebrow">KOLABORASI INFORMASI LAPANGAN</p>
            <h1 id="landing-title" class="hero-title">Waspada bersama.<br><span>Respons lebih terarah.</span></h1>
            <p class="hero-copy">Satukan laporan warga, verifikasi lapangan, dan koordinasi respons risiko kebakaran lahan dalam satu ruang terbuka.</p>
            <div class="landing-actions">
                <a class="button button-primary" href="{{ auth()->check() ? route('reports.create') : route('register') }}">Buat laporan <span aria-hidden="true">↗</span></a>
                <a class="button button-secondary" href="{{ route('map') }}">Jelajahi peta</a>
            </div>
            <p class="landing-caption">Laporan lapangan yang diperiksa bersama, untuk membantu respons yang lebih terarah.</p>
        </div>

        <section class="landing-counters" aria-label="Ringkasan laporan">
            @foreach ([['Laporan aktif', $counts['active'], 'Aktif dipantau'], ['Terverifikasi', $counts['verified'], 'Dukungan terkonfirmasi'], ['Dalam penanganan', $counts['handling'], 'Sedang ditangani'], ['Selesai', $counts['done'], 'Respons tercatat']] as [$label, $count, $hint])
                <article class="panel landing-counter" data-reveal>
                    <p class="landing-counter-label">{{ $label }}</p>
                    <p class="landing-counter-value" data-count-up="{{ $count }}">{{ number_format($count, 0, ',', '.') }}</p>
                    <p class="landing-counter-hint">{{ $hint }}</p>
                </article>
            @endforeach
        </section>
    </section>

    <section class="landing-band" aria-labelledby="cara-kerja">
        <div class="site-container landing-section">
            <div class="landing-section-heading" data-reveal>
                <p class="eyebrow">ALUR KOLABORASI</p>
                <h2 id="cara-kerja" class="display-title">Empat langkah.<br>Respons yang lebih jelas.</h2>
                <p class="section-copy">Setiap laporan melewati proses yang bisa dipahami dan dipantau bersama.</p>
            </div>
            <ol class="landing-steps">
                @foreach ([['01', 'Lapor', 'Sampaikan tanda asap, api, atau bara beserta lokasi kejadian.'], ['02', 'Verifikasi', 'Pengguna lain membantu memeriksa informasi lapangan.'], ['03', 'Prioritas', 'Indeks triase membantu menyusun antrean secara transparan.'], ['04', 'Penanganan', 'Petugas mencatat perkembangan respons hingga selesai.']] as [$number, $title, $copy])
                    <li class="panel landing-step" data-reveal>
                        <span class="step-number">{{ $number }}</span>
                        <h3>{{ $title }}</h3>
                        <p>{{ $copy }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="site-container landing-section landing-map-section" aria-labelledby="landing-map-title">
        <div class="landing-section-heading" data-reveal>
            <p class="eyebrow">PANTAU BERSAMA</p>
            <h2 id="landing-map-title" class="display-title">Laporan dalam<br>konteks wilayah.</h2>
            <p class="section-copy">Lihat lokasi laporan dan sebarannya. Koordinat publik dibulatkan untuk menjaga privasi pelapor.</p>
            <a class="text-link" href="{{ route('map') }}">Buka peta interaktif <span aria-hidden="true">→</span></a>
        </div>
        <div class="panel landing-map-panel" x-data="apiKawalLandingMap" data-reveal>
            <div class="landing-map-heading"><span class="status-dot" aria-hidden="true"></span><span>Wilayah pemantauan</span><span class="simulated-badge">DATA SIMULASI</span></div>
            <div id="landing-map" class="report-map mt-4 h-72" role="img" aria-label="Peta ringkas laporan APIKAWAL"></div>
            <p class="map-attribution">Peta © OpenStreetMap contributors.</p>
        </div>
    </section>

    <section class="landing-band landing-score-band" aria-labelledby="landing-score-title">
        <div class="site-container landing-section landing-score-layout">
            <div class="landing-section-heading" data-reveal>
                <p class="eyebrow">TRIASE YANG TERBUKA</p>
                <h2 id="landing-score-title" class="display-title">Kenapa skor ini?</h2>
                <p class="section-copy">Setiap faktor ditampilkan agar dasar pengurutan laporan bisa ditinjau. Bobot adalah heuristik awal, bukan ukuran akurat tingkat bahaya.</p>
                <a class="text-link" href="{{ route('about') }}">Pelajari metode indeks <span aria-hidden="true">→</span></a>
            </div>
            <div class="panel landing-factor-panel" aria-label="Faktor bobot awal indeks prioritas" data-reveal>
                @foreach ([['w_verification', 'Tingkat verifikasi'], ['w_landtype', 'Jenis lahan'], ['w_hotspot', 'Kedekatan hotspot'], ['w_poi', 'Kedekatan fasilitas'], ['w_support', 'Usia dan dukungan'], ['w_photo', 'Bukti foto']] as [$key, $label])
                    @php($weight = config('apikawal.priority.weights.'.$key, 0))
                    <div class="factor-row">
                        <div class="factor-label"><span>{{ $label }}</span><strong>{{ $weight }}%</strong></div>
                        <div class="factor-track" role="img" aria-label="Bobot {{ $label }} {{ $weight }} persen"><span style="width: {{ $weight }}%"></span></div>
                    </div>
                @endforeach
                <p class="factor-note">Bobot awal heuristik · dapat dikalibrasi bersama pakar</p>
            </div>
        </div>
    </section>

    <section class="site-container landing-section" aria-labelledby="latest-reports-title">
        <div class="landing-section-heading landing-row-heading" data-reveal>
            <div><p class="eyebrow">INFORMASI TERBARU</p><h2 id="latest-reports-title" class="display-title">Dari lapangan.</h2></div>
            <a class="text-link" href="{{ route('map') }}">Lihat semua laporan <span aria-hidden="true">→</span></a>
        </div>
        @if ($reports->isEmpty())
            <x-empty-state class="mt-6" title="Belum ada laporan" description="Laporan warga akan tampil di sini setelah dikirim." />
        @else
            <div class="landing-report-list">
                @foreach ($reports as $report)
                    <a class="panel landing-report-card" href="{{ route('reports.show', $report->code) }}" data-reveal>
                        <span class="landing-report-code">{{ $report->code }}</span>
                        <span class="landing-report-info"><strong>{{ $report->status->label() }}</strong><span>Prioritas {{ $report->priority_level->label() }}</span></span>
                        @if ($report->is_simulated)<span class="simulated-badge">DATA SIMULASI</span>@endif
                        <span class="landing-report-time">{{ $report->created_at->diffForHumans() }}</span>
                        <span class="landing-report-arrow" aria-hidden="true">↗</span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <section class="site-container landing-limits" aria-labelledby="landing-limits-title" data-reveal>
        <div><p class="eyebrow">PENTING DIKETAHUI</p><h2 id="landing-limits-title">Kolaborasi yang jujur tentang batasannya.</h2></div>
        <p>APIKAWAL bukan pendeteksi api otomatis dan bukan pengganti layanan darurat. Indeks prioritas adalah alat triase dengan bobot heuristik awal. Peran petugas dalam prototipe ini disimulasikan dan belum terintegrasi resmi dengan BPBD/Manggala Agni.</p>
    </section>

    <section class="site-container landing-cta" data-reveal>
        <p class="eyebrow">BERSAMA MENJAGA LINGKUNGAN</p>
        <h2 class="display-title">Satu laporan dapat<br>membuka langkah berikutnya.</h2>
        <p class="section-copy">Bantu lengkapi informasi lapangan atau pantau laporan di wilayah Anda.</p>
        <div class="landing-actions">
            <a class="button button-primary" href="{{ auth()->check() ? route('reports.create') : route('register') }}">Buat laporan <span aria-hidden="true">↗</span></a>
            <a class="button button-secondary" href="{{ route('map') }}">Lihat peta</a>
        </div>
    </section>
</div>
@endsection
