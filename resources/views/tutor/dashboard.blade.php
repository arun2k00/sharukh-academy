<x-layouts.tutor title="Dashboard" heading="Dashboard">
    <x-slot:actions>
        <x-ui.button :href="route('tutor.courses.create')" size="sm" icon="plus">New course</x-ui.button>
    </x-slot:actions>

    <p class="-mt-2 mb-6 text-sm text-ink-600">Welcome back, {{ $instructor->name }}.</p>

    <dl class="grid divide-y divide-ink-200 border border-ink-200 bg-white sm:grid-cols-2 sm:divide-x lg:grid-cols-4 lg:divide-y-0">
        <x-ui.stat label="Your courses" :value="$courses->count()" />
        <x-ui.stat label="Published" :value="$publishedCount" :hint="$draftCount.' in draft'" />
        <x-ui.stat label="Total enrollments" :value="number_format($totalEnrollments)" />
        <x-ui.stat label="Upcoming live classes" :value="$upcomingLiveClasses->count()" />
    </dl>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <x-ui.panel title="Your courses" :padded="false">
            <x-slot:actions>
                <a href="{{ route('tutor.courses.index') }}" class="text-[0.8125rem] text-pine-700 hover:underline">All</a>
            </x-slot:actions>

            @if ($courses->isEmpty())
                <p class="px-5 py-8 text-center text-sm text-ink-500">
                    You haven't created a course yet.
                    <a href="{{ route('tutor.courses.create') }}" class="text-pine-700 hover:underline">Start one</a>.
                </p>
            @else
                <ul class="divide-y divide-ink-200">
                    @foreach ($courses->take(6) as $course)
                        <li class="flex items-center justify-between gap-4 px-5 py-3">
                            <a href="{{ route('tutor.courses.edit', $course) }}"
                               class="min-w-0 truncate text-sm text-ink-900 hover:underline">{{ $course->title }}</a>
                            <div class="flex shrink-0 items-center gap-3">
                                <x-ui.badge class="{{ $course->status->badgeClasses() }}">{{ $course->status->label() }}</x-ui.badge>
                                <span class="text-sm text-ink-500 tabular-nums">{{ $course->enrollments_count }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.panel>

        <x-ui.panel title="Upcoming live classes" :padded="false">
            <x-slot:actions>
                <a href="{{ route('tutor.live-classes.index') }}" class="text-[0.8125rem] text-pine-700 hover:underline">All</a>
            </x-slot:actions>

            @if ($upcomingLiveClasses->isEmpty())
                <p class="px-5 py-8 text-center text-sm text-ink-500">Nothing scheduled.</p>
            @else
                <ul class="divide-y divide-ink-200">
                    @foreach ($upcomingLiveClasses as $liveClass)
                        <li class="px-5 py-3">
                            <div class="flex items-baseline justify-between gap-3">
                                <a href="{{ route('tutor.live-classes.edit', $liveClass) }}"
                                   class="truncate text-sm text-ink-900 hover:underline">{{ $liveClass->title }}</a>
                                <span class="shrink-0 text-[0.75rem] text-ink-500 tabular-nums">
                                    {{ $liveClass->scheduled_at->format('j M, g:ia') }}
                                </span>
                            </div>
                            <p class="truncate text-[0.8125rem] text-ink-500">{{ $liveClass->course->title }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.panel>
    </div>
</x-layouts.tutor>
