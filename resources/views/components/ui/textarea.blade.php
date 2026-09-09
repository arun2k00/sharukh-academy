@props(['invalid' => false, 'rows' => 4])

@php
    $classes = 'block w-full border bg-white px-3 py-2.5 text-sm text-ink-900 placeholder:text-ink-400 '
        .'focus:border-pine-600 focus:ring-0 focus:outline-none '
        .($invalid ? 'border-red-400' : 'border-ink-300');
@endphp

<textarea rows="{{ $rows }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</textarea>
