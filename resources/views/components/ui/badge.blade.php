@props(['class' => 'bg-sand-200 text-ink-700'])

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2 py-0.5 text-[0.6875rem] font-medium tracking-wide uppercase {$class}"]) }}>
    {{ $slot }}
</span>
