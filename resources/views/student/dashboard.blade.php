<x-layouts.app title="Dashboard" :heading="'Welcome back, '.str(auth()->user()->name)->before(' ')"
               description="Pick up where you left off, or see what is coming next.">

    {{-- Continue learning --}}
    @if ($currentEnrollment)
        <section class="border border-ink-200 bg-white">
            <div class="grid sm:grid-cols-12">
                <div class="sm:col-span-4 lg:col-span-3">
                    @if ($currentEnrollment->course->thumbnail_url)
                        <img src="{{ $currentEnrollment->course->thumbnail_url }}" alt=""
                             class="aspect-[16/9] size-full object-cover sm:aspect-auto">
                    @else
                        <div class="flex h-full min-h-32 items-center justify-center bg-ink-900 p-4 text-center">
                            <span class="font-serif text-sand-200">{{ $currentEnrollment->course->title }}</span>
                        </div>
                    @endif
                </div>

                <div class="p-5 sm:col-span-8 sm:p-6 lg:col-span-9">
                    <p class="text-[0.6875rem] font-medium tracking-[0.12em] text-pine-700 uppercase">
                        {{ $currentEnrollment->isCompleted() ? 'Recently completed' : 'Continue learning' }}
                    </p>
                    <h2 class="mt-2 text-xl leading-snug">{{ $currentEnrollment->course->title }}</h2>

                    @if ($resumeLesson)
                        <p class="mt-2 text-sm text-ink-600">
                            Next up: <span class="text-ink-900">{{ $resumeLesson->title }}</span>
                        </p>
                    @endif

                    <div class="mt-5 max-w-md">
                        <x-ui.progress :value="$currentEnrollment->progress_percent" label="Progress" />
                    </div>

                    <div class="mt-5 flex flex-wrap gap-3">
                        @if ($resumeLesson)
                            <x-ui.button :href="route('learn.lesson', [$currentEnrollment->course, $resumeLesson])" icon-after="arrow-right">
                                {{ $currentEnrollment->progress_percent > 0 ? 'Resume course' : 'Start course' }}
                            </x-ui.button>
                        @endif
                        <x-ui.button :href="route('courses.show', $currentEnrollment->course)" variant="outline">
                            Course details
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </section>
    @else
        <x-ui.empty-state title="You are not enrolled in anything yet"
                          description="Browse the catalogue and start with the programme that fits where you are now.">
            <x-ui.button :href="route('courses.index')">Browse courses</x-ui.button>
        </x-ui.empty-state>
    @endif

    {{-- Learning statistics --}}
    @if ($enrollments->isNotEmpty())
        <section class="mt-8">
            <h2 class="font-sans text-[0.6875rem] font-medium tracking-[0.12em] text-ink-500 uppercase">Your learning</h2>
            <dl class="mt-3 grid divide-y divide-ink-200 border border-ink-200 bg-white sm:grid-cols-4 sm:divide-x sm:divide-y-0">
                <x-ui.stat label="Courses" :value="$stats['enrolled']" :hint="$stats['completed'].' completed'" />
                <x-ui.stat label="Lessons done" :value="$stats['lessonsCompleted']" />
                <x-ui.stat label="Average progress" :value="$stats['averageProgress'].'%'" />
                <x-ui.stat label="Certificates" :value="$stats['certificates']" />
            </dl>
        </section>
    @endif

    <div class="mt-8 grid gap-8 lg:grid-cols-12">
        {{-- Courses --}}
        <section class="lg:col-span-7">
            <div class="flex items-center justify-between">
                <h2 class="font-sans text-[0.6875rem] font-medium tracking-[0.12em] text-ink-500 uppercase">My courses</h2>
                @if ($enrollments->count() > 3)
                    <a href="{{ route('my-courses') }}" class="text-[0.8125rem] text-pine-700 hover:underline">See all</a>
                @endif
            </div>

            @if ($enrollments->isEmpty())
                <p class="mt-3 border border-dashed border-ink-200 bg-white px-5 py-8 text-center text-sm text-ink-500">
                    Nothing here yet.
                </p>
            @else
                <ul class="mt-3 divide-y divide-ink-200 border border-ink-200 bg-white">
                    @foreach ($enrollments->take(4) as $enrollment)
                        <li class="flex items-center gap-4 p-4">
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('learn.course', $enrollment->course) }}"
                                   class="block truncate text-sm font-medium text-ink-900 hover:underline">
                                    {{ $enrollment->course->title }}
                                </a>
                                @if ($enrollment->course->instructor)
                                    <p class="mt-0.5 truncate text-[0.8125rem] text-ink-500">
                                        {{ $enrollment->course->instructor->name }}
                                    </p>
                                @endif
                                <div class="mt-2.5 max-w-xs">
                                    <x-ui.progress :value="$enrollment->progress_percent" size="sm" />
                                </div>
                            </div>
                            <span class="shrink-0 text-sm text-ink-500 tabular-nums">{{ $enrollment->progress_percent }}%</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Upcoming live classes --}}
        <section class="lg:col-span-5">
            <div class="flex items-center justify-between">
                <h2 class="font-sans text-[0.6875rem] font-medium tracking-[0.12em] text-ink-500 uppercase">Upcoming live classes</h2>
                <a href="{{ route('live-classes.index') }}" class="text-[0.8125rem] text-pine-700 hover:underline">All classes</a>
            </div>

            @if ($upcomingClasses->isEmpty())
                <p class="mt-3 border border-dashed border-ink-200 bg-white px-5 py-8 text-center text-sm text-ink-500">
                    No live classes scheduled for your courses.
                </p>
            @else
                <ul class="mt-3 divide-y divide-ink-200 border border-ink-200 bg-white">
                    @foreach ($upcomingClasses as $liveClass)
                        <li class="p-4">
                            <p class="text-[0.75rem] tracking-wide text-ink-500 uppercase">
                                {{ $liveClass->scheduled_at->format('D j M') }} · {{ $liveClass->scheduled_at->format('g:ia') }}
                            </p>
                            <a href="{{ route('live-classes.show', $liveClass) }}"
                               class="mt-1 block text-sm font-medium text-ink-900 hover:underline">
                                {{ $liveClass->title }}
                            </a>
                            <p class="mt-0.5 truncate text-[0.8125rem] text-ink-500">{{ $liveClass->course->title }}</p>

                            @if ($liveClass->isJoinable())
                                <x-ui.button :href="route('live-classes.join', $liveClass)" size="sm" class="mt-3"
                                             target="_blank" rel="noopener">
                                    Join now
                                </x-ui.button>
                            @else
                                <p class="mt-2 text-[0.75rem] text-ink-400">
                                    Join opens {{ $liveClass->scheduled_at->copy()->subMinutes(config('academy.live_join_window'))->diffForHumans() }}
                                </p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</x-layouts.app>
