@extends('layouts.site')
@section('title', 'Gangguan layanan')
@section('content')<section class="site-container py-20"><p class="eyebrow">500</p><h1 class="section-title">Terjadi gangguan pada layanan.</h1><p class="mt-3">Silakan coba beberapa saat lagi. Jika keadaan darurat, hubungi 112 atau layanan setempat.</p><a class="button button-primary mt-5" href="{{ route('home') }}">Kembali ke beranda</a></section>@endsection
