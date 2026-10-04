<x-layouts.public title="Career Plus"
                  description="Staged learning journeys that sequence Sharukh Academy courses into a job-ready programme.">
    <section class="relative overflow-hidden border-b border-ink-200">
        <div class="hero-texture-grid absolute inset-0 -z-10" aria-hidden="true"></div>
        <div class="hero-blob absolute -top-24 -left-24 -z-10 size-[24rem] rounded-full bg-pine-200/50 blur-3xl" aria-hidden="true"></div>
        <div class="hero-blob-alt absolute top-1/4 -right-24 -z-10 size-[26rem] rounded-full bg-sand-300/40 blur-3xl" aria-hidden="true"></div>

        <div class="mx-auto max-w-[1400px] px-5 py-16 lg:px-8 lg:py-24">
            <p class="flex items-center gap-2 text-[0.6875rem] font-medium tracking-[0.16em] text-pine-700 uppercase">
                <x-ui.icon name="graduation-cap" class="size-4" />
                Career Plus
            </p>
            <h1 class="mt-4 max-w-2xl font-serif text-[2.25rem] leading-[1.15] sm:text-[2.75rem]">
                A sequence, not just a catalogue
            </h1>
            <p class="mt-5 max-w-xl text-[1.0625rem]/8 text-ink-600">
                Each path strings together courses from the catalogue in the order we would actually recommend
                taking them: foundation first, then the tools, then the judgement to use them on a real project.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-[1400px] px-5 py-14 lg:px-8 lg:py-20">
        @if ($careerPaths->isEmpty())
            <x-ui.empty-state icon="graduation-cap" title="No paths published yet"
                              description="Browse the full catalogue instead. Every course on it stands on its own.">
                <x-ui.button :href="route('courses.index')">Browse courses</x-ui.button>
            </x-ui.empty-state>
        @else
            <div class="grid gap-8 sm:grid-cols-2">
                @foreach ($careerPaths as $careerPath)
                    @php $cardImage = $careerPath->banner_url ?? $careerPath->courses->first()?->thumbnail_url; @endphp
                    <a href="{{ route('career-paths.show', $careerPath) }}"
                       class="card-interactive group flex flex-col overflow-hidden border border-ink-100 bg-white">
                        <div class="relative aspect-[21/9] overflow-hidden bg-ink-900">
                            @if ($cardImage)
                                <img src="{{ $cardImage }}" alt="" loading="lazy"
                                     class="size-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
                                <div class="absolute inset-0 bg-linear-to-t from-ink-950/80 via-ink-950/10 to-transparent"></div>
                            @else
                                <div class="bg-texture-dots-overlay absolute inset-0" aria-hidden="true"></div>
                            @endif

                            <p class="absolute bottom-4 left-5 text-[0.6875rem] font-medium tracking-[0.14em] text-white/85 uppercase">
                                {{ $careerPath->courses_count }} {{ \Illuminate\Support\Str::plural('course', $careerPath->courses_count) }}
                            </p>
                        </div>

                        <div class="flex flex-1 flex-col p-6">
                            <h2 class="font-serif text-xl leading-snug text-ink-900 transition-colors group-hover:text-pine-700">
                                {{ $careerPath->title }}
                            </h2>
                            @if ($careerPath->subtitle)
                                <p class="mt-2.5 text-sm/6 text-ink-600">{{ $careerPath->subtitle }}</p>
                            @endif

                            <span class="mt-6 flex items-center gap-1.5 text-[0.8125rem] font-medium text-pine-700">
                                View the roadmap
                                <x-ui.icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-1" />
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.public>
