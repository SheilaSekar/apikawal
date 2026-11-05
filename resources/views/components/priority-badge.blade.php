@props(['label', 'level'])

@php
    $icon = ['rendah' => '1', 'sedang' => '2', 'tinggi' => '3', 'kritis' => '!'][$level] ?? '•';
@endphp

<span {{ $attributes->class(['priority-badge', 'priority-badge-'.$level]) }}>
    <span class="badge-icon" aria-hidden="true">{{ $icon }}</span>
    {{ $label }}
</span>
