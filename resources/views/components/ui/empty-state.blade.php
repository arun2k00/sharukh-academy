@props(['icon' => 'book-open', 'title', 'description' => null])

<div {{ $attributes->merge(['class' => 'border border-dashed border-ink-200 bg-white px-6 py-14 text-center']) }}>
    <x-ui.icon :name="$icon" class="mx-auto size-7 text-ink-300" />
    <p class="mt-4 font-serif text-lg text-ink-900">{{ $title }}</p>
    @if ($description)
        <p class="mx-auto mt-1.5 max-w-md text-sm text-ink-500">{{ $description }}</p>
    @endif
    @if (trim($slot) !== '')
        <div class="mt-6 flex flex-wrap justify-center gap-3">{{ $slot }}</div>
    @endif
</div>
