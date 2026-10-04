<x-layouts.admin title="Courses" heading="Courses">
    <x-slot:actions>
        <x-ui.button :href="route('admin.courses.create')" size="sm" icon="plus">New course</x-ui.button>
    </x-slot:actions>

    <form method="GET" class="mb-5 flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-64">
            <x-ui.field label="Search" for="q">
                <x-ui.input id="q" name="q" value="{{ request('q') }}" placeholder="Course title" />
            </x-ui.field>
        </div>
        <div class="w-full sm:w-44">
            <x-ui.field label="Status" for="status">
                <x-ui.select id="status" name="status">
                    <option value="">All statuses</option>
                    @foreach (\App\Enums\CourseStatus::options() as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                    <option value="deleted" @selected($showingDeleted)>Deleted</option>
                </x-ui.select>
            </x-ui.field>
        </div>
        <x-ui.button type="submit" variant="outline">Filter</x-ui.button>
        @if (request()->hasAny(['q', 'status']))
            <a href="{{ route('admin.courses.index') }}" class="pb-2.5 text-[0.8125rem] text-ink-500 hover:text-ink-900">Clear</a>
        @endif
    </form>

    @if ($showingDeleted)
        <x-ui.alert variant="info" class="mb-5">
            Deleted courses keep their curriculum, enrollments, progress and certificates. Restoring one puts it back
            exactly as it was.
        </x-ui.alert>
    @endif

    @if ($courses->isEmpty())
        <x-ui.empty-state :title="$showingDeleted ? 'Nothing has been deleted' : 'No courses match'"
                          description="Adjust the filters, or create the first course.">
            <x-ui.button :href="route('admin.courses.create')">New course</x-ui.button>
        </x-ui.empty-state>
    @else
        <x-admin.table :headings="['Course', 'Status', 'Lessons', 'Students', 'Price', ['label' => '', 'class' => 'text-right']]">
            @foreach ($courses as $course)
                <tr class="align-middle">
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.courses.edit', $course) }}" class="font-medium text-ink-900 hover:underline">
                            {{ $course->title }}
                        </a>
                        <p class="mt-0.5 text-[0.8125rem] text-ink-500">
                            {{ $course->instructor?->name ?? 'No instructor' }}
                            @if ($course->category) · {{ $course->category->name }} @endif
                        </p>
                    </td>
                    <td class="px-4 py-3">
                        <x-ui.badge :class="$course->status->badgeClasses()">{{ $course->status->label() }}</x-ui.badge>
                    </td>
                    <td class="px-4 py-3 text-ink-600 tabular-nums">{{ $course->lessons_count }}</td>
                    <td class="px-4 py-3 text-ink-600 tabular-nums">{{ $course->enrollments_count }}</td>
                    <td class="px-4 py-3 text-ink-600">{{ $course->price_label }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-3 whitespace-nowrap">
                            @if ($course->trashed())
                                <form method="POST" action="{{ route('admin.courses.restore', $course) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-[0.8125rem] font-medium text-pine-700 hover:underline">
                                        Restore
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('admin.courses.curriculum', $course) }}"
                                   class="text-[0.8125rem] text-pine-700 hover:underline">Curriculum</a>
                                <a href="{{ route('admin.courses.edit', $course) }}"
                                   class="text-[0.8125rem] text-ink-600 hover:text-ink-900">Edit</a>
                                <x-admin.delete-form :action="route('admin.courses.destroy', $course)"
                                                     confirm="Delete this course? Student records are kept and it can be restored." />
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>

        <div class="mt-5">{{ $courses->links() }}</div>
    @endif
</x-layouts.admin>
