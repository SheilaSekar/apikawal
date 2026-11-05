@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-slate-800']) }}>
    {{ $value ?? $slot }}
</label>
