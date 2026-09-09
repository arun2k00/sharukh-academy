<x-layouts.app title="My courses" heading="My courses"
               description="Everything you are enrolled in, and where you left off.">

    @if ($enrollments->isEmpty())
        <x-ui.empty-state title="No courses yet"
                          description="When you enrol in a programme it appears here with your progress.">
            <x-ui.button :href="route('courses.index')">Browse the catalogue</x-ui.button>
        </x-ui.empty-state>
    @else
        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($enrollments as $enrollment)
                @php $resume = $resumeLessons[$enrollment->course_id] ?? null; @endphp

                <article class="flex flex-col border border-ink-200 bg-white">
                    <a href="{{ route('learn.course', $enrollment->course) }}" class="block aspect-[16/9] overflow-hidden bg-ink-100">
                        @if ($enrollment->course->thumbnail_url)
                            <img src="{{ $enrollment->course->thumbnail_url }}" alt="" class="size-full object-cover" loading="lazy">
                        @else
                            <span class="flex size-full items-center justify-center bg-ink-900 px-4 text-center font-serif text-sand-200">
                                {{ $enrollment->course->title }}
                            </span>
                        @endif
                    </a>

                    <div class="flex flex-1 flex-col p-5">
                        @if ($enrollment->isCompleted())
                            <x-ui.badge class="self-start bg-pine-50 text-pine-800">Completed</x-ui.badge>
                        @endif

                        <h2 class="{{ $enrollment->isCompleted() ? 'mt-3' : '' }} text-[1.0625rem] leading-snug">
                            <a href="{{ route('learn.course', $enrollment->course) }}" class="hover:underline">
                                {{ $enrollment->course->title }}
                            </a>
                        </h2>

                        @if ($enrollment->course->instructor)
                            <p class="mt-1 text-[0.8125rem] text-ink-500">{{ $enrollment->course->instructor->name }}</p>
                        @endif

                        <div class="mt-4">
                            <x-ui.progress :value="$enrollment->progress_percent" label="Progress" />
                        </div>

                        @if ($resume && ! $enrollment->isCompleted())
                            <p class="mt-4 text-[0.8125rem] text-ink-600">
                                Next: <span class="text-ink-900">{{ $resume->title }}</span>
                            </p>
                        @endif

                        <div class="mt-auto pt-5">
                            @if ($resume)
                                <x-ui.button :href="route('learn.lesson', [$enrollment->course, $resume])" class="w-full">
                                    {{ $enrollment->isCompleted() ? 'Review course' : ($enrollment->progress_percent > 0 ? 'Continue learning' : 'Start course') }}
                                </x-ui.button>
                            @else
                                <x-ui.button :href="route('courses.show', $enrollment->course)" variant="outline" class="w-full">
                                    Course details
                                </x-ui.button>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</x-layouts.app>
