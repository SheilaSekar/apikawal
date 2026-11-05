@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'form-error text-sm space-y-1']) }} aria-live="polite">
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
