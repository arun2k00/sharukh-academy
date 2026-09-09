@props(['invalid' => false])

@php
    $classes = 'block w-full border bg-white px-3 py-2.5 text-sm text-ink-900 placeholder:text-ink-400 '
        .'focus:border-pine-600 focus:ring-0 focus:outline-none disabled:bg-ink-50 disabled:text-ink-400 '
        .($invalid ? 'border-red-400' : 'border-ink-300');
@endphp

<input {{ $attributes->merge(['class' => $classes]) }} />
