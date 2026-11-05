@props(['active'])

@php
$classes = ($active ?? false)
            ? 'breeze-nav-link is-active inline-flex min-h-[44px] items-center px-1 text-sm font-medium leading-5'
            : 'breeze-nav-link inline-flex min-h-[44px] items-center px-1 text-sm font-medium leading-5';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
