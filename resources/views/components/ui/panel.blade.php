@props(['title' => null, 'description' => null, 'padded' => true])

<section {{ $attributes->merge(['class' => 'border border-ink-200 bg-white']) }}>
    @if ($title || isset($actions))
        <header class="flex flex-wrap items-center justify-between gap-3 border-b border-ink-200 px-5 py-3.5">
            <div class="min-w-0">
                @if ($title)
                    <h2 class="font-serif text-base text-ink-900">{{ $title }}</h2>
                @endif
                @if ($description)
                    <p class="mt-0.5 text-[0.8125rem] text-ink-500">{{ $description }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
            @endisset
        </header>
    @endif

    <div class="{{ $padded ? 'p-5' : '' }}">{{ $slot }}</div>
</section>
