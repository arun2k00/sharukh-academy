@php
    $stageList = $stages->keys();
@endphp

<x-layouts.public :title="$careerPath->title"
                  :description="$careerPath->subtitle ?? \Illuminate\Support\Str::limit($careerPath->description, 155)">
    <section class="border-b border-ink-200">
        <div class="mx-auto max-w-4xl px-5 py-14 lg:px-8 lg:py-20">
            @unless ($careerPath->is_published)
                <p class="mb-4 inline-flex items-center gap-1.5 bg-ink-900 px-2.5 py-1 text-[0.6875rem] font-medium tracking-wide text-white uppercase">
                    Draft — only visible to you as an administrator
                </p>
            @endunless

            <p class="text-[0.6875rem] font-medium tracking-[0.16em] text-pine-700 uppercase">Career path</p>
            <h1 class="mt-4 text-[2rem] leading-tight sm:text-[2.5rem]">{{ $careerPath->title }}</h1>
            @if ($careerPath->subtitle)
                <p class="mt-4 max-w-2xl text-[1.0625rem]/8 text-ink-600">{{ $careerPath->subtitle }}</p>
            @endif
            <p class="mt-5 max-w-2xl text-sm/7 text-ink-600">{{ $careerPath->description }}</p>

            <p class="mt-6 text-[0.8125rem] text-ink-500">
                {{ $careerPath->courses->count() }} courses across {{ $stageList->count() }} stages{{ $careerPath->hasCapstone() ? ', plus a capstone project' : '' }}
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-5 py-14 lg:px-8">
        <ol class="space-y-0">
            @foreach ($stages as $stageLabel => $coursesInStage)
                <li>
                    <div class="flex items-center gap-3">
                        <span class="flex size-8 shrink-0 items-center justify-center border border-pine-700 font-mono text-xs text-pine-700">
                            {{ $loop->iteration }}
                        </span>
                        <p class="text-[0.75rem] font-medium tracking-[0.14em] text-ink-900 uppercase">{{ $stageLabel }}</p>
                    </div>

                    <div class="ml-4 space-y-4 border-l border-ink-200 py-5 pl-8">
                        @foreach ($coursesInStage as $course)
                            <div class="flex flex-col gap-4 border border-ink-200 bg-white p-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <p class="text-[0.75rem] text-ink-500">
                                        {{ $course->category?->name }} · {{ $course->level->label() }}
                                    </p>
                                    <h3 class="mt-1 text-base leading-snug">
                                        <a href="{{ route('courses.show', $course) }}" class="hover:underline hover:underline-offset-4">
                                            {{ $course->title }}
                                        </a>
                                    </h3>
                                    <p class="mt-1 line-clamp-1 text-sm text-ink-600">{{ $course->short_description }}</p>
                                    <p class="mt-1.5 text-[0.75rem] text-ink-500">
                                        {{ $course->lessons_count }} lessons · {{ $course->duration_label }}
                                    </p>
                                </div>

                                <div class="shrink-0">
                                    @if (($accessByCourseId[$course->id] ?? false))
                                        <x-ui.button :href="route('learn.course', $course)" size="sm" variant="outline">
                                            Continue
                                        </x-ui.button>
                                    @else
                                        <x-ui.button :href="route('courses.show', $course)" size="sm" variant="outline">
                                            View course
                                        </x-ui.button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </li>
            @endforeach

            @if ($careerPath->hasCapstone())
                <li>
                    <div class="flex items-center gap-3">
                        <span class="flex size-8 shrink-0 items-center justify-center border border-brass-600 font-mono text-xs text-brass-700">
                            {{ $stageList->count() + 1 }}
                        </span>
                        <p class="text-[0.75rem] font-medium tracking-[0.14em] text-ink-900 uppercase">Real-world project</p>
                    </div>
                    <div class="ml-4 border-l border-ink-200 py-5 pl-8">
                        <div class="border border-dashed border-brass-300 bg-brass-50/40 p-4">
                            @if ($careerPath->capstone_title)
                                <h3 class="text-base leading-snug text-ink-900">{{ $careerPath->capstone_title }}</h3>
                            @endif
                            @if ($careerPath->capstone_description)
                                <p class="mt-1.5 text-sm/6 text-ink-600">{{ $careerPath->capstone_description }}</p>
                            @endif
                        </div>
                    </div>
                </li>
            @endif

            <li class="flex items-center gap-3">
                <span class="flex size-8 shrink-0 items-center justify-center bg-pine-700 text-white">
                    <x-ui.icon name="award" class="size-4" />
                </span>
                <p class="text-[0.75rem] font-medium tracking-[0.14em] text-pine-700 uppercase">Job-ready</p>
            </li>
        </ol>
    </section>

    <section class="border-t border-ink-200 bg-white">
        <div class="mx-auto flex max-w-4xl flex-wrap items-center justify-between gap-6 px-5 py-12 lg:px-8">
            <div class="max-w-xl">
                <h2 class="text-xl">Ready to start?</h2>
                <p class="mt-2 text-sm/6 text-ink-600">
                    Enrol in the first course above — each one is yours on its own, so you can go at your own pace
                    through the rest of the path.
                </p>
            </div>
            <x-ui.button :href="route('career-paths.index')" variant="outline">See other paths</x-ui.button>
        </div>
    </section>
</x-layouts.public>
