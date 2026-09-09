@props(['title', 'description' => null, 'back' => null, 'backLabel' => 'Back'])

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-wrap items-end justify-between gap-4']) }}>
    <div class="min-w-0">
        @if ($back)
            <a href="{{ $back }}" class="mb-2 inline-flex items-center gap-1.5 text-[0.8125rem] text-ink-500 hover:text-ink-900">
                <x-ui.icon name="arrow-left" class="size-3.5" />
                {{ $backLabel }}
            </a>
        @endif
        <h1 class="text-xl leading-tight">{{ $title }}</h1>
        @if ($description)
            <p class="mt-1 max-w-2xl text-sm text-ink-600">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
