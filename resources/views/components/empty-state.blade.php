@props(['title' => 'Belum ada data', 'description' => 'Data akan muncul setelah tersedia.'])
<section {{ $attributes->merge(['class' => 'panel empty-state text-center']) }} role="status"><span class="empty-state-mark" aria-hidden="true">—</span><h2 class="font-bold">{{ $title }}</h2><p class="mt-2 text-slate-600">{{ $description }}</p></section>
