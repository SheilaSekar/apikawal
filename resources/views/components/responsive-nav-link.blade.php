@props(['active'])

@php
$classes = ($active ?? false)
            ? 'breeze-nav-link is-active flex min-h-[44px] w-full items-center px-3 text-start text-base font-medium'
            : 'breeze-nav-link flex min-h-[44px] w-full items-center px-3 text-start text-base font-medium';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
