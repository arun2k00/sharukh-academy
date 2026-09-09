@props(['eyebrow' => null, 'title', 'description' => null, 'align' => 'left'])

<div {{ $attributes->merge(['class' => $align === 'center' ? 'mx-auto max-w-2xl text-center' : 'max-w-2xl']) }}>
    @if ($eyebrow)
        <p class="text-[0.6875rem] font-medium tracking-[0.14em] text-pine-700 uppercase">{{ $eyebrow }}</p>
    @endif
    <h2 class="mt-3 text-2xl leading-tight sm:text-[1.75rem]">{{ $title }}</h2>
    @if ($description)
        <p class="mt-3 text-[0.975rem]/7 text-ink-600">{{ $description }}</p>
    @endif
</div>
