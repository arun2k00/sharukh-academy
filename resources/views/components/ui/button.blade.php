@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'submit',
    'icon' => null,
    'iconAfter' => null,
])

@php
    $base = 'js-btn-sound inline-flex items-center justify-center gap-2 font-medium transition-all duration-200 ease-out hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.97] disabled:pointer-events-none disabled:opacity-50 disabled:hover:translate-y-0 disabled:active:scale-100';

    $variants = [
        'primary' => 'bg-pine-700 text-white shadow-sm hover:bg-pine-800 hover:shadow-lg hover:shadow-pine-900/20',
        'secondary' => 'bg-ink-900 text-white shadow-sm hover:bg-ink-800 hover:shadow-lg',
        'outline' => 'border border-ink-300 bg-white text-ink-800 hover:border-pine-600 hover:bg-sand-100 hover:shadow-md',
        'ghost' => 'text-ink-600 hover:bg-sand-200 hover:text-ink-900',
        'danger' => 'border border-red-200 bg-white text-red-700 hover:bg-red-50 hover:shadow-md',
        'light' => 'bg-white text-ink-900 hover:bg-sand-100 hover:shadow-md',
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
