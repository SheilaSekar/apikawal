@extends('layouts.site')
@section('title', 'Akses ditolak')
@section('content')<section class="site-container py-20"><p class="eyebrow">403</p><h1 class="section-title">Anda tidak memiliki akses ke halaman ini.</h1><p class="mt-3">Masuk dengan akun yang sesuai atau kembali ke halaman utama.</p><a class="button button-primary mt-5" href="{{ route('home') }}">Kembali ke beranda</a></section>@endsection
