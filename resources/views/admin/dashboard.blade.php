<x-layouts.admin title="Dashboard" heading="Dashboard">
    <x-slot:actions>
        <x-ui.button :href="route('admin.courses.create')" size="sm" icon="plus">New course</x-ui.button>
    </x-slot:actions>

    <dl class="grid divide-y divide-ink-200 border border-ink-200 bg-white sm:grid-cols-2 sm:divide-x lg:grid-cols-4 lg:divide-y-0">
        <x-ui.stat label="Students" :value="number_format($stats['students'])" />
        <x-ui.stat label="Published courses" :value="$stats['publishedCourses']"
                   :hint="$stats['draftCourses'].' in draft'" />
        <x-ui.stat label="Enrolments" :value="number_format($stats['enrollments'])" />
        <x-ui.stat label="Certificates issued" :value="number_format($stats['certificates'])" />
    </dl>

    @if ($stats['unreadMessages'] > 0)
        <x-ui.alert variant="warning" class="mt-6">
            {{ $stats['unreadMessages'] }} unread {{ str('message')->plural($stats['unreadMessages']) }} from the contact form.
            <a href="{{ route('admin.messages.index') }}" class="font-medium text-brass-700 underline underline-offset-2">Read them</a>
        </x-ui.alert>
    @endif

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <x-ui.panel title="Recent enrolments" :padded="false">
            <x-slot:actions>
                <a href="{{ route('admin.enrollments.index') }}" class="text-[0.8125rem] text-pine-700 hover:underline">All</a>
            </x-slot:actions>

            @if ($recentEnrollments->isEmpty())
                <p class="px-5 py-8 text-center text-sm text-ink-500">No enrolments yet.</p>
            @else
                <ul class="divide-y divide-ink-200">
                    @foreach ($recentEnrollments as $enrollment)
                        <li class="flex items-center gap-3 px-5 py-3">
                            <x-ui.avatar :src="$enrollment->user->avatar_url" :name="$enrollment->user->name" size="xs" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm text-ink-900">{{ $enrollment->user->name }}</p>
                                <p class="truncate text-[0.8125rem] text-ink-500">{{ $enrollment->course->title }}</p>
                            </div>
                            <span class="shrink-0 text-[0.75rem] text-ink-400">{{ $enrollment->created_at->diffForHumans(short: true) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.panel>

        <x-ui.panel title="Upcoming live classes" :padded="false">
            <x-slot:actions>
                <a href="{{ route('admin.live-classes.index') }}" class="text-[0.8125rem] text-pine-700 hover:underline">All</a>
            </x-slot:actions>

            @if ($upcomingClasses->isEmpty())
                <p class="px-5 py-8 text-center text-sm text-ink-500">Nothing scheduled.</p>
            @else
                <ul class="divide-y divide-ink-200">
                    @foreach ($upcomingClasses as $liveClass)
                        <li class="px-5 py-3">
                            <div class="flex items-baseline justify-between gap-3">
                                <a href="{{ route('admin.live-classes.edit', $liveClass) }}"
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

        <x-ui.panel title="Most enrolled courses" :padded="false">
            @if ($topCourses->isEmpty())
                <p class="px-5 py-8 text-center text-sm text-ink-500">No courses yet.</p>
            @else
                <ul class="divide-y divide-ink-200">
                    @foreach ($topCourses as $course)
                        <li class="flex items-center justify-between gap-4 px-5 py-3">
                            <a href="{{ route('admin.courses.edit', $course) }}"
                               class="min-w-0 truncate text-sm text-ink-900 hover:underline">{{ $course->title }}</a>
                            <span class="shrink-0 text-sm text-ink-500 tabular-nums">{{ $course->enrollments_count }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.panel>

        <x-ui.panel title="Newest students" :padded="false">
            <x-slot:actions>
                <a href="{{ route('admin.students.index') }}" class="text-[0.8125rem] text-pine-700 hover:underline">All</a>
            </x-slot:actions>

            @if ($recentStudents->isEmpty())
                <p class="px-5 py-8 text-center text-sm text-ink-500">No students yet.</p>
            @else
                <ul class="divide-y divide-ink-200">
                    @foreach ($recentStudents as $student)
                        <li class="flex items-center gap-3 px-5 py-3">
                            <x-ui.avatar :src="$student->avatar_url" :name="$student->name" size="xs" />
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('admin.students.show', $student) }}"
                                   class="block truncate text-sm text-ink-900 hover:underline">{{ $student->name }}</a>
                                <p class="truncate text-[0.8125rem] text-ink-500">{{ $student->email }}</p>
                            </div>
                            <span class="shrink-0 text-[0.75rem] text-ink-400">
                                {{ $student->enrollments_count }} {{ str('course')->plural($student->enrollments_count) }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.panel>
    </div>
</x-layouts.admin>
