@extends('layouts.site')
@section('title', ($report->code ?? 'Laporan').' — APIKAWAL')
@section('content')
<article class="site-container max-w-4xl py-10">
    @if (session('status'))<x-alert>{{ session('status') }}</x-alert>@endif
    <p class="eyebrow mt-5">{{ $report->code }} @if($report->is_simulated)<span class="simulated-badge">DATA SIMULASI</span>@endif</p>
    <h1 class="section-title mt-2 text-3xl">Laporan indikasi kebakaran</h1>
    <p class="mt-3">Status <x-status-badge :label="$report->status->label()" /> · Prioritas <x-status-badge :label="$report->priority_level->label()" /></p>
    <div class="panel mt-5"><p>Jenis lahan: {{ $report->land_type->label() }}</p><p>Tanda: {{ collect($report->signs)->map(fn ($sign) => ucfirst($sign))->join(', ') }}</p><p>Koordinat: {{ number_format($report->lat, 3) }}, {{ number_format($report->lng, 3) }}</p><p class="mt-3">{{ $report->description ?: 'Tidak ada deskripsi.' }}</p></div>
    @if ($report->photos->isNotEmpty())
        <h2 class="section-title mt-7">Foto laporan</h2><div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
            @foreach($report->photos as $photo)<img src="{{ route('reports.photos.show', $photo) }}" alt="Foto bukti laporan {{ $report->code }}" class="rounded-lg">@endforeach
        </div>
    @endif
</article>
@endsection
