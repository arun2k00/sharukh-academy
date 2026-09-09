@props(['testimonial'])

<figure {{ $attributes->merge(['class' => 'flex flex-col']) }}>
    <x-ui.rating :value="$testimonial->rating" />
    <blockquote class="mt-4 flex-1 font-serif text-[1.0625rem]/7 text-ink-800">
        &ldquo;{{ $testimonial->quote }}&rdquo;
    </blockquote>
    <figcaption class="mt-5 flex items-center gap-3 border-t border-ink-200 pt-4">
        <x-ui.avatar :src="$testimonial->avatar_url" :name="$testimonial->name" size="sm" />
        <div class="min-w-0">
            <p class="truncate text-sm font-medium text-ink-900">{{ $testimonial->name }}</p>
            @if ($testimonial->role)
                <p class="truncate text-[0.8125rem] text-ink-500">{{ $testimonial->role }}</p>
            @endif
        </div>
    </figcaption>
</figure>
