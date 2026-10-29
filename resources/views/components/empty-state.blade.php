@props(['title' => 'Belum ada data', 'description' => 'Data akan muncul setelah tersedia.'])
<section {{ $attributes->merge(['class' => 'panel text-center']) }} role="status"><h2 class="font-bold">{{ $title }}</h2><p class="mt-2 text-slate-600">{{ $description }}</p></section>
