@extends('layouts.site')
@section('title', 'Halaman tidak ditemukan')
@section('content')<section class="site-container py-20"><p class="eyebrow">404</p><h1 class="section-title">Halaman tidak ditemukan.</h1><p class="mt-3">Periksa kembali alamat atau cari laporan dari peta.</p><a class="button button-primary mt-5" href="{{ route('map') }}">Buka peta</a></section>@endsection
