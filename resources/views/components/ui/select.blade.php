@props(['invalid' => false])

@php
    $classes = 'block w-full appearance-none border bg-white bg-[length:14px] bg-[right_0.75rem_center] bg-no-repeat '
        ."bg-[url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236b6b65' stroke-width='2' stroke-linecap='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E\")] "
        .'px-3 py-2.5 pr-9 text-sm text-ink-900 focus:border-pine-600 focus:ring-0 focus:outline-none '
        .($invalid ? 'border-red-400' : 'border-ink-300');
@endphp

<select {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</select>
