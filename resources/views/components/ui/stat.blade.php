@props(['label', 'value', 'hint' => null])

<div {{ $attributes->merge(['class' => 'px-5 py-4']) }}>
    <p class="text-[0.6875rem] font-medium tracking-[0.08em] text-ink-500 uppercase">{{ $label }}</p>
    <p class="mt-1.5 font-serif text-2xl text-ink-900 tabular-nums">{{ $value }}</p>
    @if ($hint)
        <p class="mt-0.5 text-[0.8125rem] text-ink-500">{{ $hint }}</p>
    @endif
</div>
