@props(['label', 'tone' => 'neutral'])
<span {{ $attributes->class(['status-badge', 'status-badge-danger' => $tone === 'danger', 'status-badge-success' => $tone === 'success']) }}><span class="badge-icon" aria-hidden="true">{{ $tone === 'success' ? '✓' : ($tone === 'danger' ? '!' : '•') }}</span>{{ $label }}</span>
