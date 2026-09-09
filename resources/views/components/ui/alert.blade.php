@props(['variant' => 'info', 'title' => null])

@php
    $styles = [
        'info' => ['border-ink-200 bg-white', 'text-ink-500', 'info'],
        'success' => ['border-pine-200 bg-pine-50', 'text-pine-700', 'check-circle'],
        'warning' => ['border-brass-100 bg-brass-50', 'text-brass-600', 'alert'],
        'danger' => ['border-red-200 bg-red-50', 'text-red-700', 'alert'],
    ];

    [$box, $iconColour, $icon] = $styles[$variant] ?? $styles['info'];
@endphp

<div role="status" {{ $attributes->merge(['class' => "flex gap-3 border px-4 py-3 {$box}"]) }}>
    <x-ui.icon :name="$icon" class="mt-0.5 size-[1.05rem] shrink-0 {{ $iconColour }}" />
    <div class="min-w-0 text-sm text-ink-700">
        @if ($title)
            <p class="font-medium text-ink-900">{{ $title }}</p>
        @endif
        {{ $slot }}
    </div>
</div>
