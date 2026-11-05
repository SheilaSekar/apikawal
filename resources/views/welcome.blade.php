@extends('layouts.site')
@section('title', 'APIKAWAL — Waspada bersama')
@section('content')
<section class="site-container error-page py-20">
    <p class="eyebrow">APIKAWAL</p>
    <h1 class="section-title">Waspada bersama. Respons lebih terarah.</h1>
    <p class="mt-3">Pelaporan dan koordinasi risiko kebakaran lahan berbasis informasi lapangan.</p>
    <a class="button button-primary mt-5" href="{{ route('home') }}">Kembali ke beranda APIKAWAL</a>
</section>
@endsection
