@props(['type' => 'info'])
<div {{ $attributes->class(['notice-card', 'notice-card-error' => $type === 'error']) }} role="alert" aria-live="polite"><span class="alert-icon" aria-hidden="true">{{ $type === 'error' ? '!' : 'i' }}</span><div>{{ $slot }}</div></div>
