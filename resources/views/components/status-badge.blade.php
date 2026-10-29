@props(['label', 'tone' => 'neutral'])
<span {{ $attributes->class(['status-badge', 'status-badge-danger' => $tone === 'danger', 'status-badge-success' => $tone === 'success']) }}>{{ $label }}</span>
