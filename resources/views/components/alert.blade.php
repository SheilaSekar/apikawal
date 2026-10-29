@props(['type' => 'info'])
<div {{ $attributes->class(['notice-card', 'notice-card-error' => $type === 'error']) }} role="alert" aria-live="polite">{{ $slot }}</div>
