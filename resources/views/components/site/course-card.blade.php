@props(['course'])

<article {{ $attributes->merge(['class' => 'group flex flex-col border border-ink-200 bg-white transition-colors hover:border-ink-400']) }}>
    <a href="{{ route('courses.show', $course) }}" class="block aspect-[16/9] overflow-hidden bg-ink-100">
        @if ($course->thumbnail_url)
            <img src="{{ $course->thumbnail_url }}" alt=""
                 class="size-full object-cover transition-transform duration-500 group-hover:scale-[1.02]" loading="lazy">
        @else
            <span class="flex size-full items-center justify-center bg-ink-900 px-6 text-center font-serif text-lg text-sand-200">
                {{ $course->title }}
            </span>
        @endif
    </a>

    <div class="flex flex-1 flex-col p-5">
        @if ($course->category)
            <p class="text-[0.6875rem] font-medium tracking-[0.12em] text-pine-700 uppercase">{{ $course->category->name }}</p>
        @endif

        <h3 class="mt-2 text-lg leading-snug">
            <a href="{{ route('courses.show', $course) }}" class="hover:underline hover:underline-offset-4">{{ $course->title }}</a>
        </h3>

        <p class="mt-2 line-clamp-2 text-sm/6 text-ink-600">{{ $course->short_description }}</p>

        <dl class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1 text-[0.8125rem] text-ink-500">
            <div class="flex items-center gap-1.5">
                <dt class="sr-only">Lessons</dt>
                <x-ui.icon name="play-circle" class="size-4 text-ink-400" />
                <dd>{{ $course->lessons_count ?? $course->lessons()->count() }} lessons</dd>
            </div>
            <div class="flex items-center gap-1.5">
                <dt class="sr-only">Length</dt>
                <x-ui.icon name="clock" class="size-4 text-ink-400" />
                <dd>{{ $course->duration_label }}</dd>
            </div>
        </dl>

        <div class="mt-5 flex items-end justify-between gap-4 border-t border-ink-100 pt-4">
            <div class="min-w-0">
                @if ($course->instructor)
                    <p class="truncate text-[0.8125rem] text-ink-600">{{ $course->instructor->name }}</p>
                @endif
                <p class="mt-0.5 font-serif text-lg text-ink-900">{{ $course->price_label }}</p>
            </div>
            <span class="flex shrink-0 items-center gap-1.5 text-[0.8125rem] font-medium text-pine-700">
                View course
                <x-ui.icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-0.5" />
            </span>
        </div>
    </div>
</article>
