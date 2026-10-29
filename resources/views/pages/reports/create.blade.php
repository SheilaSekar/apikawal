@extends('layouts.site')
@section('title', 'Buat laporan — APIKAWAL')
@section('content')
<section class="site-container py-10 sm:py-14">
    <p class="eyebrow">LAPORAN WARGA</p><h1 class="section-title mt-2 text-3xl">Buat laporan risiko</h1>
    <p class="mt-2 max-w-2xl text-slate-600">Laporkan tanda yang Anda amati. Informasi lokasi dan deskripsi akan ditinjau; APIKAWAL bukan layanan darurat.</p>
    @if ($errors->any())<x-alert type="error" class="mt-5"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></x-alert>@endif
    <form id="report-form" method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-5 lg:grid-cols-[1.2fr_.8fr]">
        @csrf
        <div class="panel grid gap-4">
            <div><h2 class="font-bold">Lokasi kejadian</h2><p class="text-sm text-slate-600">Klik peta, geser penanda, atau gunakan lokasi perangkat.</p></div>
            <div id="report-map" class="report-map" data-lat="{{ old('lat', -3.2) }}" data-lng="{{ old('lng', 105.3) }}" aria-label="Peta untuk memilih lokasi laporan"></div>
            <button id="use-my-location" class="button button-secondary w-fit" type="button">Gunakan lokasi saya</button>
            <button id="check-duplicate" class="button button-secondary w-fit" type="button">Cek laporan serupa</button>
            <p id="duplicate-result" aria-live="polite" class="text-sm font-semibold"></p>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="form-label">Lintang<input id="report-lat" class="form-control" type="number" step="any" name="lat" required value="{{ old('lat', -3.2) }}"></label>
                <label class="form-label">Bujur<input id="report-lng" class="form-control" type="number" step="any" name="lng" required value="{{ old('lng', 105.3) }}"></label>
            </div>
        </div>
        <div class="panel grid content-start gap-4">
            <label class="form-label">Jenis lahan<select class="form-control" name="land_type" required>@foreach (['gambut' => 'Gambut', 'hutan' => 'Hutan', 'semak' => 'Semak', 'kebun' => 'Kebun', 'lainnya' => 'Lainnya'] as $value => $label)<option value="{{ $value }}" @selected(old('land_type') === $value)>{{ $label }}</option>@endforeach</select></label>
            <fieldset><legend class="form-label">Tanda yang terlihat</legend><div class="mt-2 flex flex-wrap gap-4">@foreach (['asap' => 'Asap', 'api' => 'Api', 'bara' => 'Bara'] as $value => $label)<label class="inline-flex min-h-11 items-center gap-2"><input type="checkbox" name="signs[]" value="{{ $value }}" @checked(in_array($value, old('signs', []), true))>{{ $label }}</label>@endforeach</div></fieldset>
            <label class="form-label">Perkiraan luas (hektare, opsional)<input class="form-control" type="number" min="0" step="0.1" name="estimated_area_ha" value="{{ old('estimated_area_ha') }}"></label>
            <label class="form-label">Deskripsi<textarea class="form-control" name="description" rows="4" maxlength="2000">{{ old('description') }}</textarea></label>
            <label class="form-label">Foto bukti (maksimal 3, 1 MB per foto)<input id="report-photos" class="form-control" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple></label>
            <div id="photo-preview" class="grid grid-cols-3 gap-2" aria-live="polite"></div>
            <p class="text-sm text-slate-600">Foto diproses ulang, ukuran maksimal 1280 px, dan metadata foto dibuang.</p>
            <button class="button button-primary" type="submit">Kirim laporan</button>
        </div>
    </form>
</section>
@endsection
