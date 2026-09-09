@props(['src' => null, 'title' => 'Lesson video', 'id' => null, 'poster' => null])

<div {{ $attributes->merge(['class' => 'relative aspect-video w-full overflow-hidden bg-ink-950']) }}>
    @if ($src)
        <iframe
            @if ($id) id="{{ $id }}" @endif
            src="{{ $src }}"
            title="{{ $title }}"
            class="absolute inset-0 size-full"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen
            loading="lazy"></iframe>
    @else
        <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-ink-400">
            <x-ui.icon name="video" class="size-7" />
            <p class="text-sm">No video has been attached to this lesson yet.</p>
        </div>
    @endif
</div>
