@extends('layouts.site')
@section('title', 'Peta laporan — APIKAWAL')
@section('content')
<section class="site-container py-10" x-data="apiKawalMap" data-map-filters="{{ json_encode(['status' => $filters['status'] ?? '', 'priority_level' => $filters['level'] ?? '', 'land_type' => $filters['land_type'] ?? '', 'since' => $filters['since'] ?? '']) }}">
    <div class="flex flex-wrap items-end justify-between gap-4"><div><p class="eyebrow">PEMANTAUAN TERBUKA</p><h1 class="section-title">Peta laporan</h1><p class="mt-2 text-slate-600">Koordinat publik ditampilkan dengan pembulatan untuk menjaga privasi.</p></div><span class="simulated-badge">DATA SIMULASI</span></div>
    <form class="glass-surface map-filter-panel mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-5" @change="load()" aria-label="Filter peta">
        <label>Status<select class="form-control mt-1" x-model="filters.status"><option value="">Semua</option><option value="baru">Baru</option><option value="terverifikasi">Terverifikasi</option><option value="diteruskan">Diteruskan</option><option value="dalam_penanganan">Dalam penanganan</option><option value="terkendali">Terkendali</option><option value="selesai">Selesai</option></select></label>
        <label>Prioritas<select class="form-control mt-1" x-model="filters.priority_level"><option value="">Semua</option><option value="rendah">Rendah</option><option value="sedang">Sedang</option><option value="tinggi">Tinggi</option><option value="kritis">Kritis</option></select></label>
        <label>Jenis lahan<select class="form-control mt-1" x-model="filters.land_type"><option value="">Semua</option><option value="gambut">Gambut</option><option value="hutan">Hutan</option><option value="semak">Semak</option><option value="kebun">Kebun</option><option value="lainnya">Lainnya</option></select></label>
        <label>Rentang waktu<select class="form-control mt-1" x-model="filters.since"><option value="">Semua waktu</option><option value="1">24 jam</option><option value="7">7 hari</option><option value="30">30 hari</option></select></label>
        <label class="flex items-center gap-2 self-end pb-2"><input type="checkbox" x-model="filters.hotspots" @change="load()"> Hotspot 24 jam</label>
    </form>
    <div class="map-view-toggle mt-4 flex gap-2"><button type="button" class="button button-secondary" @click="mode='map'" :aria-pressed="mode==='map'">Tampilan Peta</button><button type="button" class="button button-secondary" @click="mode='list'" :aria-pressed="mode==='list'">Tampilan Daftar</button></div>
    <p class="mt-2 text-sm" role="status" aria-live="polite" x-show="loading">Memuat data peta…</p><p class="notice-card-error mt-2 p-3" role="alert" x-show="error" x-text="error"></p>
    <div x-show="mode==='map'" id="public-map" class="report-map mt-4 h-[65vh] min-h-[380px] rounded-xl border" role="application" aria-label="Peta laporan kebakaran lahan"></div>
    <div x-show="mode==='list'" class="glass-surface map-list-surface mt-4">
        <div class="divide-y sm:hidden"><template x-for="item in reports" :key="`mobile-${item.code}`"><article class="p-4"><a class="inline-flex min-h-[44px] items-center font-bold underline" :href="`/laporan/${item.code}`" x-text="item.code"></a><dl class="grid grid-cols-[auto_1fr] gap-x-3 gap-y-1 text-sm"><dt class="font-semibold">Status</dt><dd x-text="item.status_label"></dd><dt class="font-semibold">Prioritas</dt><dd x-text="`${item.level} · ${item.priority}`"></dd><dt class="font-semibold">Jenis lahan</dt><dd x-text="item.land_type"></dd><dt class="font-semibold">Waktu</dt><dd x-text="item.created_at || '—'"></dd></dl></article></template></div>
        <p x-show="reports.length===0" class="p-6">Belum ada laporan yang sesuai filter.</p>
        <div class="hidden overflow-x-auto sm:block"><table class="min-w-full text-left text-sm"><caption class="sr-only">Daftar laporan terfilter</caption><thead><tr><th class="p-3">Kode</th><th>Status</th><th>Prioritas</th><th>Jenis lahan</th><th>Waktu</th></tr></thead><tbody><template x-for="item in reports" :key="item.code"><tr class="border-t"><td class="p-3"><a class="inline-flex min-h-[44px] items-center underline" :href="`/laporan/${item.code}`" x-text="item.code"></a></td><td x-text="item.status_label"></td><td x-text="`${item.level} · ${item.priority}`"></td><td x-text="item.land_type"></td><td x-text="item.created_at || '—'"></td></tr></template></tbody></table></div>
    </div>
    <div class="map-legend mt-4" aria-label="Keterangan peta"><span><i class="legend-dot legend-low" aria-hidden="true"></i>Rendah / sedang</span><span><i class="legend-dot legend-high" aria-hidden="true"></i>Tinggi / kritis</span><span>Simbol menunjukkan status laporan.</span></div>
    <p class="mt-2 text-xs text-slate-600">Peta © OpenStreetMap contributors. Warna menunjukkan prioritas; status dan skor juga tersedia sebagai teks.</p>
</section>
@endsection

