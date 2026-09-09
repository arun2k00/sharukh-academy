@props(['value' => 0, 'label' => null, 'size' => 'md'])

@php
    $value = max(0, min(100, (int) $value));
    $height = $size === 'sm' ? 'h-1' : 'h-1.5';
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    @if ($label)
        <div class="mb-1.5 flex items-baseline justify-between text-[0.8125rem]">
            <span class="text-ink-600">{{ $label }}</span>
            <span class="font-medium text-ink-900 tabular-nums">{{ $value }}%</span>
        </div>
    @endif

    <div class="{{ $height }} w-full overflow-hidden bg-ink-100" role="progressbar"
         aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="100">
        <div class="h-full bg-pine-600 transition-[width] duration-500" style="width: {{ $value }}%"></div>
    </div>
</div>
