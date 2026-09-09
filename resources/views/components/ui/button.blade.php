@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'submit',
    'icon' => null,
    'iconAfter' => null,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-medium transition-all duration-150 active:scale-[0.98] disabled:pointer-events-none disabled:opacity-50 disabled:active:scale-100';

    $variants = [
        'primary' => 'bg-pine-700 text-white shadow-sm hover:bg-pine-800 hover:shadow-md',
        'secondary' => 'bg-ink-900 text-white shadow-sm hover:bg-ink-800 hover:shadow-md',
        'outline' => 'border border-ink-300 bg-white text-ink-800 hover:border-pine-600 hover:bg-sand-100',
        'ghost' => 'text-ink-600 hover:bg-sand-200 hover:text-ink-900',
        'danger' => 'border border-red-200 bg-white text-red-700 hover:bg-red-50',
        'light' => 'bg-white text-ink-900 hover:bg-sand-100',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-[0.8125rem]',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-[0.9375rem]',
    ];

    $classes = trim(implode(' ', [$base, $variants[$variant] ?? $variants['primary'], $sizes[$size] ?? $sizes['md']]));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-ui.icon :name="$icon" class="size-4" />@endif
        {{ $slot }}
        @if ($iconAfter)<x-ui.icon :name="$iconAfter" class="size-4" />@endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-ui.icon :name="$icon" class="size-4" />@endif
        {{ $slot }}
        @if ($iconAfter)<x-ui.icon :name="$iconAfter" class="size-4" />@endif
    </button>
@endif
