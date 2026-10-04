<x-layouts.tutor title="My courses" heading="My courses">
    <x-slot:actions>
        <x-ui.button :href="route('tutor.courses.create')" size="sm" icon="plus">New course</x-ui.button>
    </x-slot:actions>

    <form method="GET" class="mb-5 flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-64">
            <x-ui.field label="Search" for="q">
                <x-ui.input id="q" name="q" value="{{ request('q') }}" placeholder="Course title" />
            </x-ui.field>
        </div>
        <x-ui.button type="submit" variant="outline">Filter</x-ui.button>
        @if (request()->hasAny(['q']))
            <a href="{{ route('tutor.courses.index') }}" class="pb-2.5 text-[0.8125rem] text-ink-500 hover:text-ink-900">Clear</a>
        @endif
    </form>

    @if ($courses->isEmpty())
        <x-ui.empty-state title="No courses yet" description="Create your first course to get started.">
            <x-ui.button :href="route('tutor.courses.create')">New course</x-ui.button>
        </x-ui.empty-state>
    @else
        <x-admin.table :headings="['Course', 'Status', 'Lessons', 'Students', 'Price', ['label' => '', 'class' => 'text-right']]">
            @foreach ($courses as $course)
                <tr class="align-middle">
                    <td class="px-4 py-3">
                        <a href="{{ route('tutor.courses.edit', $course) }}" class="font-medium text-ink-900 hover:underline">
                            {{ $course->title }}
                        </a>
                        @if ($course->category)
                            <p class="mt-0.5 text-[0.8125rem] text-ink-500">{{ $course->category->name }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <x-ui.badge :class="$course->status->badgeClasses()">{{ $course->status->label() }}</x-ui.badge>
                    </td>
                    <td class="px-4 py-3 text-ink-600 tabular-nums">{{ $course->lessons_count }}</td>
                    <td class="px-4 py-3 text-ink-600 tabular-nums">{{ $course->enrollments_count }}</td>
                    <td class="px-4 py-3 text-ink-600">{{ $course->price_label }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-3 whitespace-nowrap">
                            <a href="{{ route('tutor.courses.curriculum', $course) }}"
                               class="text-[0.8125rem] text-pine-700 hover:underline">Curriculum</a>
                            <a href="{{ route('tutor.courses.edit', $course) }}"
                               class="text-[0.8125rem] text-ink-600 hover:text-ink-900">Edit</a>
                            <x-admin.delete-form :action="route('tutor.courses.destroy', $course)"
                                                 confirm="Delete this course? Student records are kept, and an administrator can restore it." />
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>

        <div class="mt-5">{{ $courses->links() }}</div>
    @endif
</x-layouts.tutor>
