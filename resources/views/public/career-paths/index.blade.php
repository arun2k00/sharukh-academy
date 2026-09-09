<x-layouts.public title="Career paths"
                  description="Staged learning journeys that sequence Edunova courses into a job-ready programme.">
    <section class="border-b border-ink-200">
        <div class="mx-auto max-w-6xl px-5 py-14 lg:px-8 lg:py-20">
            <p class="text-[0.6875rem] font-medium tracking-[0.16em] text-pine-700 uppercase">Career paths</p>
            <h1 class="mt-4 max-w-3xl text-[2rem] leading-tight sm:text-[2.5rem]">
                A sequence, not just a catalogue
            </h1>
            <p class="mt-5 max-w-2xl text-[1.0625rem]/8 text-ink-600">
                Each path strings together courses from the catalogue in the order we would actually recommend
                taking them — foundation first, then the tools, then the judgement to use them on a real project.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-5 py-12 lg:px-8 lg:py-16">
        @if ($careerPaths->isEmpty())
            <x-ui.empty-state icon="graduation-cap" title="No paths published yet"
                              description="Browse the full catalogue instead — every course on it stands on its own.">
                <x-ui.button :href="route('courses.index')">Browse courses</x-ui.button>
            </x-ui.empty-state>
        @else
            <div class="grid gap-6 sm:grid-cols-2">
                @foreach ($careerPaths as $careerPath)
                    <a href="{{ route('career-paths.show', $careerPath) }}"
                       class="group flex flex-col justify-between border border-ink-200 bg-white p-6 transition-all duration-200 hover:-translate-y-0.5 hover:border-pine-300 hover:shadow-lg">
                        <div>
                            <p class="text-[0.6875rem] font-medium tracking-[0.14em] text-pine-700 uppercase">
                                {{ $careerPath->courses_count }} {{ \Illuminate\Support\Str::plural('course', $careerPath->courses_count) }}
                            </p>
                            <h2 class="mt-2 text-xl leading-snug">{{ $careerPath->title }}</h2>
                            @if ($careerPath->subtitle)
                                <p class="mt-2 text-sm/6 text-ink-600">{{ $careerPath->subtitle }}</p>
                            @endif
                        </div>
                        <span class="mt-6 flex items-center gap-1.5 text-[0.8125rem] font-medium text-pine-700">
                            View the roadmap
                            <x-ui.icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-0.5" />
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.public>
